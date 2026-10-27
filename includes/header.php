<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/icons.php';
$pageTitle = $pageTitle ?? APP_NAME;
$bodyClass = $bodyClass ?? '';
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');

function nav_attrs(string $file, string $currentPage): string
{
    $isActive = $file === $currentPage;
    return $isActive ? ' class="active" aria-current="page"' : '';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - <?= e(APP_NAME) ?></title>
    <link rel="icon" href="<?= asset('images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        .main-nav { gap: clamp(14px, 2vw, 26px); }
        .main-nav a { position: relative; font-weight: 720; }
        .main-nav a.active { color: #000; }
        .main-nav a.active::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -10px;
            width: 22px;
            height: 3px;
            border-radius: 999px;
            background: #050505;
            transform: translateX(-50%);
        }
        .site-header .brand-text { white-space: nowrap; }
        @media (max-width: 820px) {
            .main-nav a.active::after { display: none; }
            .main-nav a.active { background: var(--bg-soft); }
        }
        <?= $extraHead ?? '' ?>
    </style>
</head>
<body class="<?= e($bodyClass) ?>">
    <header class="site-header" data-header>
        <div class="container nav-wrap">
            <a class="brand" href="<?= url('index.php') ?>" aria-label="Arts Gallery home">
                <span class="brand-mark">A</span>
                <span class="brand-text">Arts.</span>
            </a>
            <button class="nav-toggle" type="button" data-nav-toggle aria-label="Open menu" aria-expanded="false">
                <?= icon_svg('menu') ?>
            </button>
            <nav class="main-nav" data-nav-menu aria-label="Main navigation">
                <a href="<?= url('index.php') ?>"<?= nav_attrs('index.php', $currentPage) ?>>Home</a>
                <a href="<?= url('gallery.php') ?>"<?= nav_attrs('gallery.php', $currentPage) ?>>Artworks</a>
                <a href="<?= url('artists.php') ?>"<?= nav_attrs('artists.php', $currentPage) ?>>Artists</a>
                <a href="<?= url('exhibitions.php') ?>"<?= nav_attrs('exhibitions.php', $currentPage) ?>>Exhibitions</a>
                <a href="<?= url('about.php') ?>"<?= nav_attrs('about.php', $currentPage) ?>>About Us</a>
                <a href="<?= url('articles.php') ?>"<?= nav_attrs('articles.php', $currentPage) ?>>Articles</a>
                <a href="<?= url('contact.php') ?>"<?= nav_attrs('contact.php', $currentPage) ?>>Contact</a>
            </nav>
            <div class="nav-actions">
                <a class="icon-link" href="<?= url('gallery.php') ?>" aria-label="Search gallery"><?= icon_svg('search') ?></a>
                <a class="icon-link cart-link" href="<?= url('cart.php') ?>" aria-label="Cart"><?= icon_svg('cart') ?><span><?= cart_count() ?></span></a>
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <a class="btn btn-dark btn-small" href="<?= url('admin/dashboard.php') ?>">Admin</a>
                    <?php else: ?>
                        <a class="btn btn-dark btn-small" href="<?= url('dashboard.php') ?>">Dashboard</a>
                    <?php endif; ?>
                    <a class="btn btn-light btn-small" href="<?= url('logout.php') ?>">Logout</a>
                <?php else: ?>
                    <a class="btn btn-light btn-small" href="<?= url('login.php') ?>">Login</a>
                    <a class="btn btn-dark btn-small" href="<?= url('register.php') ?>">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="page-content">
        <?php if ($success = flash('success')): ?>
            <div class="container flash flash-success"><?= e($success) ?></div>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
            <div class="container flash flash-error"><?= e($error) ?></div>
        <?php endif; ?>
