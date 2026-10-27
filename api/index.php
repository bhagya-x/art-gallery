<?php
declare(strict_types=1);

$root = dirname(__DIR__);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$path = $_GET['path'] ?? 'index.php';
$path = ltrim($path, '/');

$allowedPages = [
    'index.php',
    'about.php',
    'articles.php',
    'artists.php',
    'artwork.php',
    'cart.php',
    'checkout.php',
    'contact.php',
    'custom-order.php',
    'dashboard.php',
    'exhibitions.php',
    'gallery.php',
    'login.php',
    'logout.php',
    'messages.php',
    'order-view.php',
    'register.php',
    'verify-otp.php',
    'admin/artists.php',
    'admin/artwork-delete.php',
    'admin/artwork-form.php',
    'admin/artworks.php',
    'admin/customers.php',
    'admin/dashboard.php',
    'admin/messages.php',
    'admin/orders.php',
    'admin/report-pdf.php',
    'admin/reports.php',
];

if (!in_array($path, $allowedPages, true)) {
    http_response_code(404);
    exit('Page not found');
}

$file = $root . DIRECTORY_SEPARATOR
    . str_replace('/', DIRECTORY_SEPARATOR, $path);

if (!is_file($file)) {
    http_response_code(404);
    exit('Page not found');
}

require $file;
