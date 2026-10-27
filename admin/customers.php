<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../config/database.php';
require_admin();
$pageTitle = 'Manage Customers';
$customers = $pdo->query("SELECT users.*, COUNT(orders.id) AS order_count, COALESCE(SUM(orders.total),0) AS total_spent FROM users LEFT JOIN orders ON users.id = orders.user_id WHERE users.role='customer' GROUP BY users.id ORDER BY users.created_at DESC")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Admin</span><h1>Manage Customers</h1><p>View customer details, order history count, and total order value.</p></div></section>
<div class="container dashboard">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <section><div class="panel">
        <h2>Customers</h2>
        <div class="table-wrap" style="margin-top:18px"><table><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Total</th><th>Joined</th></tr></thead><tbody>
        <?php foreach ($customers as $customer): ?>
            <tr><td><strong><?= e($customer['name']) ?></strong></td><td><?= e($customer['email']) ?></td><td><?= e($customer['phone']) ?></td><td><?= (int)$customer['order_count'] ?></td><td><?= money((float)$customer['total_spent']) ?></td><td><?= e(date('M d, Y', strtotime($customer['created_at']))) ?></td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
    </div></section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
