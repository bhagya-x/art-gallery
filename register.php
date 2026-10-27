<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';

$errors = [];
$old = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $old['name'] = trim($_POST['name'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $old['phone'] = preg_replace('/\D+/', '', $_POST['phone'] ?? '');
    $old['address'] = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($old['name'] === '') {
        $errors['name'] = 'Name is required.';
    } elseif (!is_valid_person_name($old['name'])) {
        $errors['name'] = 'Name can contain letters and spaces only. Numbers are not allowed.';
    }

    if ($old['email'] === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!is_valid_email_address($old['email'])) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($old['phone'] === '') {
        $errors['phone'] = 'Phone number is required.';
    } elseif (!is_valid_phone_number($old['phone'], true)) {
        $errors['phone'] = 'Phone number must contain numbers only, between 7 and 15 digits.';
    }

    if (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$old['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'This email is already registered.';
        }
    }

    if ($errors) {
        $_SESSION['flash_error'] = 'Please correct the highlighted fields.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, 'customer')");
        $stmt->execute([$old['name'], $old['email'], $old['phone'], $old['address'], $hash]);
        $_SESSION['flash_success'] = 'Registration successful. Please login.';
        redirect('login.php');
    }
}
$pageTitle = 'Register';
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-wrap">
    <div class="form-card auth-card">
        <span class="eyebrow">Create Account</span>
        <h1>Join Arts Gallery</h1>
        <p>Register to place orders, upload reference images, and track your artwork requests.</p>
        <form method="post" class="validated-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="form-group">
                <label>Name</label>
                <input class="form-control<?= error_class($errors, 'name') ?>" name="name" value="<?= e($old['name']) ?>" required data-validate-name data-error-message="Name can contain letters and spaces only.">
                <?= field_error($errors, 'name') ?>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input class="form-control<?= error_class($errors, 'email') ?>" type="email" name="email" value="<?= e($old['email']) ?>" required data-validate-email data-error-message="Please enter a valid email address.">
                <?= field_error($errors, 'email') ?>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Phone</label>
                    <input class="form-control<?= error_class($errors, 'phone') ?>" name="phone" value="<?= e($old['phone']) ?>" required inputmode="numeric" minlength="7" maxlength="15" data-validate-phone data-error-message="Phone number must contain numbers only.">
                    <?= field_error($errors, 'phone') ?>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control<?= error_class($errors, 'password') ?>" type="password" name="password" minlength="6" required>
                    <?= field_error($errors, 'password') ?>
                </div>
            </div>
            <div class="form-group"><label>Address</label><textarea class="form-control" name="address"><?= e($old['address']) ?></textarea></div>
            <button class="btn btn-dark btn-block" type="submit">Create Account</button>
        </form>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
