<?php
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah(int|float|string $amount): string
{
    return 'Rp' . number_format((float) $amount, 0, ',', '.');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    $expected = $_SESSION['csrf_token'] ?? '';
    if (!is_string($token) || !is_string($expected) || $expected === '' || !hash_equals($expected, $token)) {
        http_response_code(419);
        exit('Sesi formulir berakhir. Silakan kembali dan coba lagi.');
    }
}

function status_text(string $status): string
{
    return match ($status) {
        'menunggu' => 'Menunggu dibuat',
        'diproses' => 'Sedang dibuat',
        'siap_diambil' => 'Siap diambil',
        'diterima' => 'Sudah diterima',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        default => ucfirst($status),
    };
}

function queue_code(int $number): string
{
    return 'A' . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
}
