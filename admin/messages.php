<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../config/database.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $messageId = (int)($_POST['message_id'] ?? 0);
    $reply = trim($_POST['reply'] ?? '');
    if ($messageId && $reply !== '') {
        $stmt = $pdo->prepare("INSERT INTO message_replies (message_id, admin_id, reply) VALUES (?, ?, ?)");
        $stmt->execute([$messageId, current_user()['id'], $reply]);
        $pdo->prepare("UPDATE messages SET status='Answered' WHERE id=?")->execute([$messageId]);
        $emailStmt = $pdo->prepare("SELECT email, subject FROM messages WHERE id=?");
        $emailStmt->execute([$messageId]);
        $msg = $emailStmt->fetch();
        if ($msg) { log_email($msg['email'], 'Reply: ' . $msg['subject'], $reply); }
        $_SESSION['flash_success'] = 'Reply sent successfully.';
    }
    redirect('admin/messages.php');
}

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
$replyRows = $pdo->query("SELECT message_replies.*, users.name AS admin_name FROM message_replies JOIN users ON users.id = message_replies.admin_id ORDER BY created_at ASC")->fetchAll();
$replies = [];
foreach ($replyRows as $row) { $replies[$row['message_id']][] = $row; }
$pageTitle = 'Manage Messages';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Admin</span><h1>Manage Messages</h1><p>Respond to customer inquiries and custom artwork requests.</p></div></section>
<div class="container dashboard">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <section><div class="panel">
        <h2>Customer Inquiries</h2>
        <?php foreach ($messages as $msg): ?>
            <div class="message-box">
                <strong><?= e($msg['subject']) ?></strong> <?= status_badge($msg['status']) ?>
                <br><small><?= e($msg['name']) ?> - <?= e($msg['email']) ?> - <?= e($msg['created_at']) ?></small>
                <p><?= nl2br(e($msg['message'])) ?></p>
                <?php foreach ($replies[$msg['id']] ?? [] as $reply): ?>
                    <div class="message-box message-reply"><strong>Reply by <?= e($reply['admin_name']) ?></strong><br><small><?= e($reply['created_at']) ?></small><p><?= nl2br(e($reply['reply'])) ?></p></div>
                <?php endforeach; ?>
                <form method="post" class="validated-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="message_id" value="<?= (int)$msg['id'] ?>">
                    <div class="form-group"><label>Reply</label><textarea class="form-control" name="reply" required></textarea></div>
                    <button class="btn btn-dark btn-small" type="submit">Send Reply</button>
                </form>
            </div>
        <?php endforeach; ?>
        <?php if (!$messages): ?><p>No messages found.</p><?php endif; ?>
    </div></section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
