<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';

$errors = [];
$old = [
    'name' => current_user()['name'] ?? '',
    'email' => current_user()['email'] ?? '',
    'subject' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $old['name'] = trim($_POST['name'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $old['subject'] = trim($_POST['subject'] ?? '');
    $old['message'] = trim($_POST['message'] ?? '');

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

    if ($old['subject'] === '') {
        $errors['subject'] = 'Subject is required.';
    }

    if ($old['message'] === '') {
        $errors['message'] = 'Message is required.';
    }

    if (!db_connected()) {
        $errors['general'] = 'Database is not connected. Please set up database environment variables in Vercel to submit inquiries.';
    }

    if ($errors) {
        $_SESSION['flash_error'] = 'Please correct the highlighted fields.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([current_user()['id'] ?? null, $old['name'], $old['email'], $old['subject'], $old['message']]);
        log_email(ADMIN_EMAIL, 'New Contact Message', $old['name'] . ' sent a message: ' . $old['subject']);
        $_SESSION['flash_success'] = 'Your inquiry was sent successfully.';
        redirect('contact.php');
    }
}
$pageTitle = 'Contact';
require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Contact</span><h1>Contact Gallery</h1><p>Send inquiries about artworks, orders, availability, or custom artwork requests.</p></div></section>
<div class="container contact-grid">
    <div class="contact-info">
        <h2>Gallery Support</h2>
        <p>The contact system stores messages in the admin dashboard and writes email notification logs for SMTP-ready workflow.</p>
        <div class="info-line"><?= icon_svg('mail') ?><span>admin@artgallery.lk</span></div>
        <div class="info-line"><?= icon_svg('box') ?><span>Colombo Art Gallery, Sri Lanka</span></div>
        <div class="info-line"><?= icon_svg('shield') ?><span>Secure inquiry management</span></div>
        <div class="qa-mini">
            <strong>Quick Answers</strong>
            <span>Order questions, custom artwork requests, artist details, and availability inquiries are answered through the message dashboard.</span>
        </div>
    </div>
    <form class="form-card validated-form" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="form-grid">
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
        </div>
        <div class="form-group">
            <label>Subject</label>
            <input class="form-control<?= error_class($errors, 'subject') ?>" name="subject" value="<?= e($old['subject']) ?>" required>
            <?= field_error($errors, 'subject') ?>
        </div>
        <div class="form-group">
            <label>Message</label>
            <textarea class="form-control<?= error_class($errors, 'message') ?>" name="message" required><?= e($old['message']) ?></textarea>
            <?= field_error($errors, 'message') ?>
        </div>
        <button class="btn btn-dark" type="submit">Send Inquiry</button>
    </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
