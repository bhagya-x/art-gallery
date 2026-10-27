<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/otp.php';
require_once __DIR__ . '/config/database.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!is_valid_email_address($email)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // සිංහල සටහන: password එක හරි නම් සෘජුව login නොකර OTP එකක් email එකට යවයි.
            $otp = create_login_otp($user);
            $sent = send_login_otp($user, $otp);

            if ($sent) {
                $_SESSION['flash_success'] = 'OTP code sent to ' . mask_email($user['email']) . '. Please verify to login.';
                redirect('verify-otp.php');
            }

            clear_login_otp();
            $_SESSION['flash_error'] = 'OTP email could not be sent. Please check SMTP settings in config/mail.php.';
        } else {
            $_SESSION['flash_error'] = 'Invalid email or password.';
        }
    } else {
        $_SESSION['flash_error'] = 'Please correct the highlighted fields.';
    }
}
$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-wrap">
    <div class="form-card auth-card">
        <span class="eyebrow">Secure Login</span>
        <h1>Welcome Back</h1>
        <p>Enter your email and password. After that, a 6-digit OTP code will be sent to your email.</p>
        <form method="post" class="validated-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="form-group">
                <label>Email</label>
                <input class="form-control<?= error_class($errors, 'email') ?>" type="email" name="email" value="<?= e($email) ?>" required data-validate-email data-error-message="Please enter a valid email address.">
                <?= field_error($errors, 'email') ?>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input class="form-control<?= error_class($errors, 'password') ?>" type="password" name="password" required>
                <?= field_error($errors, 'password') ?>
            </div>
            <button class="btn btn-dark btn-block" type="submit">Send OTP & Continue</button>
        </form>
        <p style="margin-top:18px"><strong>Admin:</strong> admin@artgallery.lk / admin123<br><strong>Customer:</strong> customer@artgallery.lk / customer123</p>
        <p style="font-size:.88rem">Local testing note: if SMTP is not configured yet, OTP is written to <code>storage/logs/email.log</code>.</p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
