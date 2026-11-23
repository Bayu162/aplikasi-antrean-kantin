<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

try {
    $pdo = db();
} catch (PDOException $exception) {
    http_response_code(500);
    echo '<!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Database belum terhubung</title><body style="font-family:Arial,sans-serif;max-width:620px;margin:60px auto;padding:24px;line-height:1.6;color:#23332d"><h1>Database belum terhubung</h1><p>Pastikan MySQL sudah aktif, database <b>antrean_kantin</b> sudah diimpor, dan pengaturan pada <code>config/database.php</code> sudah benar.</p></body></html>';
    exit;
}
