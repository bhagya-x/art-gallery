<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../config/database.php';
require_admin();
$pageTitle = 'Reports';
$inventory = $pdo->query("SELECT category, status, COUNT(*) AS total, COALESCE(SUM(price),0) AS value FROM artworks GROUP BY category, status ORDER BY category")->fetchAll();
$sales = $pdo->query("SELECT DATE(created_at) AS order_date, COUNT(*) AS orders_count, COALESCE(SUM(total),0) AS total_value FROM orders GROUP BY DATE(created_at) ORDER BY order_date DESC LIMIT 30")->fetchAll();
$topCustomers = $pdo->query("SELECT users.name, users.email, COUNT(orders.id) AS orders_count, COALESCE(SUM(orders.total),0) AS total_value FROM users JOIN orders ON users.id = orders.user_id GROUP BY users.id ORDER BY total_value DESC LIMIT 10")->fetchAll();
$summary = [
    'artworks' => (int)$pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn(),
    'orders' => (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'customers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
    'revenue' => (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders")->fetchColumn(),
];
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Admin</span><h1>Reports</h1><p>Inventory, sales, customer, and order summary reports with a professional PDF export for submission or review.</p></div></section>
<div class="container dashboard">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <section>
        <div class="panel report-toolbar">
            <div>
                <span class="eyebrow">Report Center</span>
                <h2>Professional PDF Report</h2>
                <p>Generate a clean PDF report including summary cards, order status, inventory, sales, and top customer records.</p>
            </div>
            <a class="btn btn-dark" href="<?= url('admin/report-pdf.php') ?>" target="_blank" rel="noopener">Download PDF Report <?= icon_svg('arrow') ?></a>
        </div>

        <div class="stats-grid report-stats">
            <div class="stat-card"><strong><?= (int)$summary['artworks'] ?></strong><span>Total Artworks</span></div>
            <div class="stat-card"><strong><?= (int)$summary['orders'] ?></strong><span>Total Orders</span></div>
            <div class="stat-card"><strong><?= (int)$summary['customers'] ?></strong><span>Customers</span></div>
            <div class="stat-card"><strong><?= money((float)$summary['revenue']) ?></strong><span>Total Revenue</span></div>
        </div>

        <div class="panel">
            <div class="panel-head"><h2>Inventory Report</h2><span class="badge badge-info badge-dot">Live Data</span></div>
            <div class="table-wrap" style="margin-top:18px"><table><thead><tr><th>Category</th><th>Status</th><th>Total Artworks</th><th>Inventory Value</th></tr></thead><tbody><?php foreach ($inventory as $row): ?><tr><td><?= e($row['category']) ?></td><td><?= status_badge($row['status']) ?></td><td><?= (int)$row['total'] ?></td><td><?= money((float)$row['value']) ?></td></tr><?php endforeach; ?><?php if (!$inventory): ?><tr><td colspan="4">No inventory records found.</td></tr><?php endif; ?></tbody></table></div>
        </div>
        <div class="panel">
            <div class="panel-head"><h2>Sales Report</h2><span class="badge badge-purple badge-dot">Last 30 Days</span></div>
            <div class="table-wrap" style="margin-top:18px"><table><thead><tr><th>Date</th><th>Orders</th><th>Total Value</th></tr></thead><tbody><?php foreach ($sales as $row): ?><tr><td><?= e($row['order_date']) ?></td><td><?= (int)$row['orders_count'] ?></td><td><?= money((float)$row['total_value']) ?></td></tr><?php endforeach; ?><?php if (!$sales): ?><tr><td colspan="3">No sales records found.</td></tr><?php endif; ?></tbody></table></div>
        </div>
        <div class="panel">
            <div class="panel-head"><h2>Top Customers</h2><span class="badge badge-success badge-dot">Performance</span></div>
            <div class="table-wrap" style="margin-top:18px"><table><thead><tr><th>Name</th><th>Email</th><th>Orders</th><th>Total</th></tr></thead><tbody><?php foreach ($topCustomers as $row): ?><tr><td><?= e($row['name']) ?></td><td><?= e($row['email']) ?></td><td><?= (int)$row['orders_count'] ?></td><td><?= money((float)$row['total_value']) ?></td></tr><?php endforeach; ?><?php if (!$topCustomers): ?><tr><td colspan="4">No customer purchase records found.</td></tr><?php endif; ?></tbody></table></div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
