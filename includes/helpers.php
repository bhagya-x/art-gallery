<?php
// සිංහල සටහන: session එක එකම තැනකින් ආරම්භ කරයි.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/app.php';

function url(string $path = ''): string
{
    // සිංහල සටහන: link paths නිවැරදිව සකස් කරයි.
    return BASE_URL . ltrim($path, '/');
}

function asset(string $path): string
{
    // සිංහල සටහන: CSS, JS, images වැනි asset paths සකස් කරයි.
    return url('assets/' . ltrim($path, '/'));
}

function e(?string $value): string
{
    // සිංහල සටහන: XSS attacks වලින් ආරක්ෂා වීමට output escape කරයි.
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function money(float $amount): string
{
    // සිංහල සටහන: මුදල් අගය ලස්සන format එකට පෙන්වයි.
    return CURRENCY . ' ' . number_format($amount, 2);
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_admin(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please login to continue.';
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        $_SESSION['flash_error'] = 'Admin access required.';
        redirect('index.php');
    }
}

function flash(string $type): ?string
{
    // සිංහල සටහන: success/error messages එක වරක් පමණක් පෙන්වයි.
    $key = 'flash_' . $type;
    if (!empty($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return null;
}

function csrf_token(): string
{
    // සිංහල සටහන: forms සඳහා simple CSRF token එකක් ලබා දෙයි.
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    // සිංහල සටහන: POST requests වල CSRF token එක පරීක්ෂා කරයි.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $_SESSION['flash_error'] = 'Invalid form request. Please try again.';
            redirect('index.php');
        }
    }
}

function status_meta(string $status): array
{
    // සිංහල සටහන: එක් එක් status එකට අදාළ වර්ණය (color) සහ class එක ලබා දෙයි.
    $key = strtolower(trim($status));
    $map = [
        // Orders
        'pending'    => 'warning',
        'processing' => 'info',
        'reserved'   => 'purple',
        'completed'  => 'success',
        'cancelled'  => 'danger',
        'canceled'   => 'danger',
        // Artworks
        'available'  => 'success',
        'sold'       => 'danger',
        // Messages
        'open'       => 'info',
        'answered'   => 'success',
        'closed'     => 'neutral',
        // Generic approvals
        'approved'   => 'success',
        'approval'   => 'warning',
        'rejected'   => 'danger',
        'cancel'     => 'danger',
    ];
    $tone = $map[$key] ?? 'neutral';
    return ['tone' => $tone, 'class' => 'badge badge-' . $tone . ' badge-dot'];
}

function status_badge(?string $status): string
{
    // සිංහල සටහන: status එක අදාළ වර්ණයෙන් pill එකක් ලෙස පෙන්වයි.
    $status = $status ?? '';
    $meta = status_meta($status);
    return '<span class="' . $meta['class'] . '">' . e($status) . '</span>';
}



function is_valid_person_name(string $value): bool
{
    // සිංහල සටහන: name fields වලට අකුරු, spaces, dot, apostrophe, hyphen පමණක් allow කරයි.
    return (bool) preg_match("/^[\p{L}\s.'-]+$/u", trim($value));
}

function is_valid_phone_number(string $value, bool $required = false): bool
{
    // සිංහල සටහන: phone fields වලට numbers පමණක් allow කරයි.
    $value = trim($value);
    if ($value === '') {
        return !$required;
    }
    return (bool) preg_match('/^[0-9]{7,15}$/', $value);
}

function is_valid_email_address(string $value): bool
{
    return (bool) filter_var(trim($value), FILTER_VALIDATE_EMAIL);
}

function field_error(array $errors, string $field): string
{
    if (empty($errors[$field])) {
        return '';
    }
    return '<small class="form-error" role="alert">' . e($errors[$field]) . '</small>';
}

function error_class(array $errors, string $field): string
{
    return !empty($errors[$field]) ? ' is-invalid' : '';
}

function cart_count(): int
{
    $count = 0;
    foreach ($_SESSION['cart'] ?? [] as $item) {
        $count += (int) $item['qty'];
    }
    return $count;
}

function log_email(string $to, string $subject, string $body): void
{
    // සිංහල සටහන: system notifications PHPMailer හරහා යවයි; local setup නැති නම් email.log එකට fallback වෙයි.
    require_once __DIR__ . '/mailer.php';
    send_app_mail($to, $subject, nl2br(e($body)), $body);
}

function upload_reference_image(string $fieldName): ?string
{
    // සිංහල සටහන: customer upload කරන reference image එක validate කර save කරයි.
    if (empty($_FILES[$fieldName]['name'])) {
        return null;
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }

    $file = $_FILES[$fieldName];
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($file['tmp_name']);

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.');
    }

    if ($file['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('Image size must be below 3MB.');
    }

    $filename = 'reference_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
    $target = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Image upload failed.');
    }

    return 'uploads/reference/' . $filename;
}
