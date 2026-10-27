<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../config/database.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? 'Pending';
    $allowed = ['Pending','Processing','Reserved','Completed','Cancelled'];
    if (in_array($status, $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $userStmt = $pdo->prepare("SELECT users.email, orders.order_number FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id = ?");
        $userStmt->execute([$id]);
        $data = $userStmt->fetch();
        if ($data) { log_email($data['email'], 'Order Status Updated - ' . $data['order_number'], 'Your order status is now: ' . $status); }
        $_SESSION['flash_success'] = 'Order status updated successfully.';
    }
    redirect('admin/orders.php');
}

$orders = $pdo->query("SELECT orders.*, users.name, users.email FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC")->fetchAll();
$pageTitle = 'Manage Orders';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Admin</span><h1>Manage Orders</h1><p>View, track, and update customer artwork order statuses.</p></div></section>
<div class="container dashboard">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <section><div class="panel">
        <h2>Orders</h2>
        <div class="table-wrap" style="margin-top:18px"><table><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Update</th><th>Details</th></tr></thead><tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><strong><?= e($order['order_number']) ?></strong><br><small><?= e($order['created_at']) ?></small></td>
                <td><?= e($order['name']) ?><br><small><?= e($order['email']) ?></small></td>
                <td><?= money((float)$order['total']) ?></td>
                <td><?= status_badge($order['status']) ?></td>
                <td><form method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?= csrf_token() ?>"><input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>"><?php $tone = status_meta($order['status'])['tone']; $toneColor = ['success'=>'#0c7a43','danger'=>'#c2182a','warning'=>'#9a6400','info'=>'#1456c4','purple'=>'#6b2fb3','neutral'=>'#545464'][$tone]; ?><select class="form-control" name="status" onchange="this.form.submit()" style="min-width:150px;border-color:<?= $toneColor ?>;border-left:5px solid <?= $toneColor ?>;color:<?= $toneColor ?>"><?php foreach (['Pending','Processing','Reserved','Completed','Cancelled'] as $s): ?><option value="<?= e($s) ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></form></td>
                <td><a href="<?= url('order-view.php?id=' . (int)$order['id']) ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody></table></div>
    </div></section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
