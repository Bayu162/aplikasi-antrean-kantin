<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Beranda';
$menus = $pdo->query('SELECT id, name, description, price FROM menus WHERE is_available = 1 ORDER BY id')->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container hero-content">
        <span class="eyebrow">ANTREAN KANTIN SEKOLAH</span>
        <h1>Pesan makanan jadi lebih mudah.</h1>
        <p>Pilih makanan, dapatkan nomor antrean, konfirmasi setelah pesanan diterima, lalu beri rating untuk kantin.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="#menu">Lihat Menu</a>
            <a class="button button-secondary" href="status.php">Cek Antrean</a>
        </div>
    </div>
</section>

<section class="section" id="menu">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">MENU KANTIN</span>
                <h2>Pilih makanan favoritmu</h2>
            </div>
            <a class="text-link" href="pesan.php">Buat pesanan &rarr;</a>
        </div>

        <?php if (!$menus): ?>
            <div class="empty-box">Belum ada menu yang tersedia.</div>
        <?php else: ?>
            <div class="menu-grid">
                <?php foreach ($menus as $index => $menu): ?>
                    <article class="menu-card">
                        <div class="menu-icon"><?= ['🍛', '🍜', '🥪', '🥤', '💧', '🍌'][$index % 6] ?></div>
                        <div class="menu-info">
                            <h3><?= e($menu['name']) ?></h3>
                            <p><?= e($menu['description'] ?: 'Menu kantin sekolah') ?></p>
                            <div class="menu-bottom">
                                <strong><?= rupiah($menu['price']) ?></strong>
                                <a class="small-button" href="pesan.php?menu=<?= (int) $menu['id'] ?>">Pesan</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="center-action"><a class="button button-primary" href="pesan.php">Pesan Sekarang</a></div>
        <?php endif; ?>
    </div>
</section>

<section class="how-section">
    <div class="container">
        <h2>Cara Memesan</h2>
        <div class="steps">
            <div class="step"><span>1</span><div><b>Pilih menu</b><p>Tentukan makanan dan jumlahnya.</p></div></div>
            <div class="step"><span>2</span><div><b>Isi data diri</b><p>Masukkan nama dan kelas.</p></div></div>
            <div class="step"><span>3</span><div><b>Konfirmasi dan beri rating</b><p>Tandai pesanan sudah diterima, lalu beri penilaian.</p></div></div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
