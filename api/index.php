<?php
declare(strict_types=1);

$root = dirname(__DIR__);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$path = $_GET['path'] ?? 'index.php';
$path = ltrim($path, '/');

// Normalize routes
if ($path === '' || $path === 'index') {
    $path = 'index.php';
} elseif ($path === 'admin' || $path === 'admin/') {
    $path = 'admin/dashboard.php';
} elseif (!str_ends_with($path, '.php')) {
    $path .= '.php';
}

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
    echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body style="font-family:sans-serif;text-align:center;padding:50px;"><h1>404 - Page Not Found</h1><p><a href="/">Return to Home</a></p></body></html>';
    exit;
}

$file = $root . DIRECTORY_SEPARATOR
    . str_replace('/', DIRECTORY_SEPARATOR, $path);

if (!is_file($file)) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body style="font-family:sans-serif;text-align:center;padding:50px;"><h1>404 - File Not Found</h1><p><a href="/">Return to Home</a></p></body></html>';
    exit;
}

// Preserve active page highlight for navbars
$_SERVER['SCRIPT_NAME'] = '/' . $path;
$_SERVER['PHP_SELF'] = '/' . $path;

require $file;
