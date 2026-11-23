<?php
$pageTitle = $pageTitle ?? 'Antrean Kantin';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1f5b45">
    <title><?= e($pageTitle) ?> - Antrean Kantin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php"><span class="brand-icon">K</span><span>Kantin<span class="brand-dot">.Antri</span></span></a>
        <nav class="main-nav" aria-label="Navigasi utama">
            <a href="index.php">Beranda</a>
            <a href="status.php">Cek Antrean</a>
        </nav>
    </div>
</header>
<main>
