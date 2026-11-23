<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Buat Pesanan';
$menus = $pdo->query('SELECT id, name, description, price FROM menus WHERE is_available = 1 ORDER BY id')->fetchAll();
$selectedMenu = isset($_GET['menu']) ? (int) $_GET['menu'] : 0;
$postedQuantities = is_array($_POST['qty'] ?? null) ? $_POST['qty'] : [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $customerName = trim((string) ($_POST['customer_name'] ?? ''));
    $className = trim((string) ($_POST['class_name'] ?? ''));
    $notes = trim((string) ($_POST['notes'] ?? ''));
    $quantities = $_POST['qty'] ?? [];

    if ($customerName === '' || mb_strlen($customerName) > 100) {
        $error = 'Nama harus diisi (maksimal 100 karakter).';
    } elseif ($className === '' || mb_strlen($className) > 40) {
        $error = 'Kelas harus diisi (maksimal 40 karakter).';
    } elseif (!is_array($quantities)) {
        $error = 'Pilihan menu tidak valid.';
    } elseif (mb_strlen($notes) > 300) {
        $error = 'Catatan maksimal 300 karakter.';
    } else {
        $requested = [];
        foreach ($quantities as $menuId => $quantity) {
            $menuId = filter_var($menuId, FILTER_VALIDATE_INT);
            $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
            if ($menuId !== false && $menuId > 0 && $quantity !== false && $quantity > 0) {
                if ($quantity > 20) {
                    $error = 'Jumlah maksimal 20 untuk setiap menu.';
                    break;
                }
                $requested[(int) $menuId] = (int) $quantity;
            }
        }

        if (!$error && !$requested) {
            $error = 'Pilih minimal satu menu.';
        }

        if (!$error) {
            $ids = array_keys($requested);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $menuQuery = $pdo->prepare("SELECT id, name, price FROM menus WHERE is_available = 1 AND id IN ($placeholders)");
            $menuQuery->execute($ids);
            $availableMenus = $menuQuery->fetchAll();

            if (count($availableMenus) !== count($requested)) {
                $error = 'Ada menu yang tidak tersedia. Muat ulang halaman dan pilih kembali.';
            } else {
                try {
                    $pdo->beginTransaction();
                    $queueDate = date('Y-m-d');
                    $pdo->prepare('INSERT IGNORE INTO queue_counters (queue_date, last_number) VALUES (?, 0)')->execute([$queueDate]);
                    $counterQuery = $pdo->prepare('SELECT last_number FROM queue_counters WHERE queue_date = ? FOR UPDATE');
                    $counterQuery->execute([$queueDate]);
                    $queueNumber = (int) $counterQuery->fetchColumn() + 1;
                    $pdo->prepare('UPDATE queue_counters SET last_number = ? WHERE queue_date = ?')->execute([$queueNumber, $queueDate]);

                    $total = 0;
                    foreach ($availableMenus as $item) {
                        $total += (int) $item['price'] * $requested[(int) $item['id']];
                    }

                    $insertOrder = $pdo->prepare("INSERT INTO orders (queue_date, queue_number, customer_name, class_name, notes, total) VALUES (?, ?, ?, ?, ?, ?)");
                    $insertOrder->execute([$queueDate, $queueNumber, $customerName, $className, $notes, $total]);
                    $orderId = (int) $pdo->lastInsertId();
                    $insertItem = $pdo->prepare('INSERT INTO order_items (order_id, menu_id, menu_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)');

                    foreach ($availableMenus as $item) {
                        $quantity = $requested[(int) $item['id']];
                        $price = (int) $item['price'];
                        $insertItem->execute([$orderId, $item['id'], $item['name'], $price, $quantity, $price * $quantity]);
                    }

                    $pdo->commit();
                    header('Location: status.php?date=' . urlencode($queueDate) . '&queue=' . urlencode(queue_code($queueNumber)) . '&new=1');
                    exit;
                } catch (Throwable $exception) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $error = 'Pesanan belum berhasil disimpan. Silakan coba lagi.';
                }
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div class="container">
        <a class="back-link" href="index.php">&larr; Kembali ke beranda</a>
        <h1>Buat Pesanan</h1>
        <p>Isi nama, kelas, lalu pilih menu dan jumlah yang diinginkan.</p>
    </div>
</section>
<section class="section section-small">
    <div class="container form-container">
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <?php if (!$menus): ?>
            <div class="empty-box">Belum ada menu yang tersedia. Silakan kembali lagi nanti.</div>
        <?php else: ?>
            <form method="post" class="order-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="form-card">
                    <label class="field"><span>Nama</span><input type="text" name="customer_name" maxlength="100" placeholder="Nama lengkap" value="<?= e($_POST['customer_name'] ?? '') ?>" required></label>
                    <label class="field"><span>Kelas</span><input type="text" name="class_name" maxlength="40" placeholder="Contoh: XII RPL 1" value="<?= e($_POST['class_name'] ?? '') ?>" required></label>
                </div>

                <div class="form-card">
                    <h2>Daftar Menu</h2>
                    <?php foreach ($menus as $menu): ?>
                        <div class="order-row">
                            <div>
                                <b><?= e($menu['name']) ?></b>
                                <small><?= e($menu['description']) ?></small>
                                <strong><?= rupiah($menu['price']) ?></strong>
                            </div>
                            <label class="quantity"><span class="sr-only">Jumlah</span><input type="number" name="qty[<?= (int) $menu['id'] ?>]" min="0" max="20" value="<?= e((string) ($postedQuantities[$menu['id']] ?? ($selectedMenu === (int) $menu['id'] ? 1 : 0))) ?>"></label>
                        </div>
                    <?php endforeach; ?>
                    <label class="field notes-field"><span>Catatan (opsional)</span><textarea name="notes" rows="2" maxlength="300" placeholder="Contoh: tidak pakai cabai"><?= e($_POST['notes'] ?? '') ?></textarea></label>
                </div>

                <button type="submit" class="button button-primary button-full">Kirim Pesanan</button>
            </form>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
