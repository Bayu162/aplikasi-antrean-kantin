<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Cek Antrean';
$order = null;
$items = [];
$error = '';
$queueInput = strtoupper(trim((string) ($_GET['queue'] ?? $_POST['queue'] ?? '')));
$dateInput = (string) ($_GET['date'] ?? $_POST['date'] ?? date('Y-m-d'));
$queueDigits = preg_replace('/^A/i', '', $queueInput);
$dateObject = DateTime::createFromFormat('!Y-m-d', $dateInput);
$validDate = $dateObject && $dateObject->format('Y-m-d') === $dateInput;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (!preg_match('/^\d{1,6}$/', $queueDigits) || !$validDate) {
        $error = 'Masukkan nomor antrean dan tanggal yang benar.';
    } else {
        $findForAction = $pdo->prepare('SELECT * FROM orders WHERE queue_date = ? AND queue_number = ? LIMIT 1');
        $findForAction->execute([$dateInput, (int) $queueDigits]);
        $actionOrder = $findForAction->fetch() ?: null;
        $action = (string) ($_POST['action'] ?? '');

        if (!$actionOrder) {
            $error = 'Pesanan tidak ditemukan. Periksa kembali nomor dan tanggalnya.';
        } elseif ($action === 'mark_received') {
            if (in_array($actionOrder['status'], ['diterima', 'selesai'], true)) {
                header('Location: status.php?date=' . urlencode($dateInput) . '&queue=' . urlencode(queue_code((int) $queueDigits)));
                exit;
            }
            if ($actionOrder['status'] === 'dibatalkan') {
                $error = 'Pesanan yang dibatalkan tidak dapat dikonfirmasi diterima.';
            } else {
                $updateReceived = $pdo->prepare("UPDATE orders SET status = 'diterima', received_at = CURRENT_TIMESTAMP WHERE id = ? AND status <> 'dibatalkan'");
                $updateReceived->execute([$actionOrder['id']]);
                header('Location: status.php?date=' . urlencode($dateInput) . '&queue=' . urlencode(queue_code((int) $queueDigits)) . '&received=1');
                exit;
            }
        } elseif ($action === 'submit_rating') {
            $rating = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
            $ratingComment = trim((string) ($_POST['rating_comment'] ?? ''));
            if (!in_array($actionOrder['status'], ['diterima', 'selesai'], true)) {
                $error = 'Konfirmasi bahwa pesanan sudah diterima sebelum memberi rating.';
            } elseif ($actionOrder['rating'] !== null) {
                $error = 'Rating untuk pesanan ini sudah pernah dikirim.';
            } elseif ($rating === false || $rating < 1 || $rating > 5) {
                $error = 'Pilih rating dari 1 sampai 5 bintang.';
            } elseif (mb_strlen($ratingComment) > 300) {
                $error = 'Komentar maksimal 300 karakter.';
            } else {
                $saveRating = $pdo->prepare('UPDATE orders SET rating = ?, rating_comment = ?, rated_at = CURRENT_TIMESTAMP WHERE id = ? AND rating IS NULL');
                $saveRating->execute([$rating, $ratingComment !== '' ? $ratingComment : null, $actionOrder['id']]);
                header('Location: status.php?date=' . urlencode($dateInput) . '&queue=' . urlencode(queue_code((int) $queueDigits)) . '&rated=1');
                exit;
            }
        }
    }
}

if ($queueInput !== '' && $validDate && preg_match('/^\d{1,6}$/', $queueDigits)) {
    $findOrder = $pdo->prepare('SELECT * FROM orders WHERE queue_date = ? AND queue_number = ? LIMIT 1');
    $findOrder->execute([$dateInput, (int) $queueDigits]);
    $order = $findOrder->fetch() ?: null;
    if ($order) {
        $findItems = $pdo->prepare('SELECT menu_name, quantity, subtotal FROM order_items WHERE order_id = ? ORDER BY id');
        $findItems->execute([$order['id']]);
        $items = $findItems->fetchAll();
    } elseif ($error === '') {
        $error = 'Pesanan tidak ditemukan. Periksa kembali nomor dan tanggalnya.';
    }
} elseif ($queueInput !== '') {
    $error = 'Masukkan nomor antrean dan tanggal yang benar.';
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div class="container">
        <a class="back-link" href="index.php">&larr; Kembali ke beranda</a>
        <h1>Cek Nomor Antrean</h1>
        <p>Lihat pesanan, konfirmasi penerimaan, dan beri penilaian.</p>
    </div>
</section>
<section class="section section-small">
    <div class="container form-container">
        <form method="get" class="form-card lookup-form" action="status.php">
            <label class="field"><span>Nomor antrean</span><input type="text" name="queue" placeholder="Contoh: A001" value="<?= e($queueInput) ?>" required></label>
            <label class="field"><span>Tanggal pesan</span><input type="date" name="date" value="<?= e($dateInput) ?>" required></label>
            <button class="button button-primary button-full" type="submit">Cari Pesanan</button>
        </form>

        <?php if (isset($_GET['new']) && $order): ?>
            <div class="alert alert-success">Pesanan berhasil disimpan. Simpan nomor antrean ini.</div>
        <?php endif; ?>
        <?php if (isset($_GET['received']) && $order): ?>
            <div class="alert alert-success">Terima kasih! Pesanan ditandai sudah diterima. Silakan beri rating untuk kantin.</div>
        <?php endif; ?>
        <?php if (isset($_GET['rated']) && $order): ?>
            <div class="alert alert-success">Terima kasih atas rating dan masukanmu!</div>
        <?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

        <?php if ($order): ?>
            <article class="ticket">
                <p class="ticket-label">NOMOR ANTREAN</p>
                <div class="ticket-number"><?= e(queue_code((int) $order['queue_number'])) ?></div>
                <span class="status-badge <?= in_array($order['status'], ['diterima', 'selesai'], true) ? 'status-received' : '' ?>"><?= e(status_text($order['status'])) ?></span>
                <div class="ticket-info">
                    <div><small>Nama</small><b><?= e($order['customer_name']) ?></b></div>
                    <div><small>Kelas</small><b><?= e($order['class_name']) ?></b></div>
                    <div><small>Tanggal</small><b><?= date('d-m-Y', strtotime($order['queue_date'])) ?></b></div>
                </div>
                <div class="ticket-items">
                    <h2>Pesanan</h2>
                    <?php foreach ($items as $item): ?>
                        <div class="ticket-line"><span><?= e($item['menu_name']) ?> × <?= (int) $item['quantity'] ?></span><b><?= rupiah($item['subtotal']) ?></b></div>
                    <?php endforeach; ?>
                    <div class="ticket-total"><span>Total</span><b><?= rupiah($order['total']) ?></b></div>
                </div>
                <?php if ($order['notes'] !== ''): ?><p class="ticket-note">Catatan: <?= e($order['notes']) ?></p><?php endif; ?>

                <?php if (!in_array($order['status'], ['diterima', 'selesai', 'dibatalkan'], true)): ?>
                    <div class="received-panel">
                        <p>Sudah mengambil dan menerima pesananmu?</p>
                        <form method="post" action="status.php">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="queue" value="<?= e(queue_code((int) $order['queue_number'])) ?>">
                            <input type="hidden" name="date" value="<?= e($order['queue_date']) ?>">
                            <input type="hidden" name="action" value="mark_received">
                            <button type="submit" class="button button-primary button-full">Pesanan Sudah Diterima</button>
                        </form>
                    </div>
                <?php elseif ($order['status'] === 'dibatalkan'): ?>
                    <p class="ticket-help">Pesanan ini berstatus dibatalkan dan tidak dapat diberi rating.</p>
                <?php endif; ?>

                <?php if (in_array($order['status'], ['diterima', 'selesai'], true)): ?>
                    <?php if ($order['rating'] === null): ?>
                        <form method="post" class="rating-panel" action="status.php">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="queue" value="<?= e(queue_code((int) $order['queue_number'])) ?>">
                            <input type="hidden" name="date" value="<?= e($order['queue_date']) ?>">
                            <input type="hidden" name="action" value="submit_rating">
                            <h2>Beri Rating Kantin</h2>
                            <p>Bagaimana pengalamanmu dengan pesanan ini?</p>
                            <div class="rating-options" role="radiogroup" aria-label="Pilih rating">
                                <?php for ($star = 1; $star <= 5; $star++): ?>
                                    <label><input type="radio" name="rating" value="<?= $star ?>" required><span><?= $star ?> ★</span></label>
                                <?php endfor; ?>
                            </div>
                            <label class="field rating-comment"><span>Komentar (opsional)</span><textarea name="rating_comment" rows="2" maxlength="300" placeholder="Tuliskan saran atau pendapatmu..."></textarea></label>
                            <button type="submit" class="button button-primary button-full">Kirim Rating</button>
                        </form>
                    <?php else: ?>
                        <div class="rating-result">
                            <h2>Terima kasih atas penilaianmu!</h2>
                            <p class="rating-stars-display" aria-label="Rating <?= (int) $order['rating'] ?> dari 5"><?= str_repeat('★', (int) $order['rating']) . str_repeat('☆', 5 - (int) $order['rating']) ?></p>
                            <p><?= (int) $order['rating'] ?> dari 5 bintang</p>
                            <?php if (!empty($order['rating_comment'])): ?><blockquote><?= e($order['rating_comment']) ?></blockquote><?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($order['status'] !== 'dibatalkan'): ?>
                    <p class="ticket-help">Simpan nomor antrean ini untuk melihat kembali pesanan dan ratingmu.</p>
                <?php endif; ?>
            </article>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
