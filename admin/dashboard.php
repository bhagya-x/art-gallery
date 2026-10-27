<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../config/database.php';
require_admin();
$pageTitle = 'Admin Dashboard';

$counts = [
    'artworks' => $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn(),
    'customers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
    'orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'messages' => $pdo->query("SELECT COUNT(*) FROM messages WHERE status='Open'")->fetchColumn(),
];
$sales = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('Processing','Reserved','Completed')")->fetchColumn();
$recentOrders = $pdo->query("SELECT orders.*, users.name FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC LIMIT 6")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Admin</span><h1>Dashboard</h1><p>Manage artworks, artists, orders, customers, reports, and messages.</p></div></section>
<div class="container dashboard">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <section>
        <div class="stats-grid">
            <div class="stat-card"><strong><?= (int)$counts['artworks'] ?></strong><span>Artworks</span></div>
            <div class="stat-card"><strong><?= (int)$counts['customers'] ?></strong><span>Customers</span></div>
            <div class="stat-card"><strong><?= (int)$counts['orders'] ?></strong><span>Orders</span></div>
            <div class="stat-card"><strong><?= money((float)$sales) ?></strong><span>Sales Value</span></div>
        </div>
        <div class="panel">
            <div class="panel-head"><h2>Recent Orders</h2><a class="btn btn-dark btn-small" href="<?= url('admin/orders.php') ?>">Manage Orders</a></div>
            <div class="table-wrap"><table><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead><tbody>
            <?php foreach ($recentOrders as $order): ?>
                <tr><td><?= e($order['order_number']) ?></td><td><?= e($order['name']) ?></td><td><?= money((float)$order['total']) ?></td><td><?= status_badge($order['status']) ?></td><td><?= e(date('M d, Y', strtotime($order['created_at']))) ?></td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
