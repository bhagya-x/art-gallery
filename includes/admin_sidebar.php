<?php
// සිංහල සටහන: admin dashboard sidebar links එකම තැනක තබාගනී.
$currentAdminPage = basename($_SERVER['PHP_SELF']);
function admin_active(string $file): string { return basename($_SERVER['PHP_SELF']) === $file ? 'active' : ''; }
?>
<aside class="sidebar">
    <a class="<?= admin_active('dashboard.php') ?>" href="<?= url('admin/dashboard.php') ?>"><?= icon_svg('chart') ?> Dashboard</a>
    <a class="<?= admin_active('artworks.php') ?>" href="<?= url('admin/artworks.php') ?>"><?= icon_svg('box') ?> Artworks</a>
    <a class="<?= admin_active('artists.php') ?>" href="<?= url('admin/artists.php') ?>"><?= icon_svg('user') ?> Artists</a>
    <a class="<?= admin_active('orders.php') ?>" href="<?= url('admin/orders.php') ?>"><?= icon_svg('cart') ?> Orders</a>
    <a class="<?= admin_active('customers.php') ?>" href="<?= url('admin/customers.php') ?>"><?= icon_svg('user') ?> Customers</a>
    <a class="<?= admin_active('messages.php') ?>" href="<?= url('admin/messages.php') ?>"><?= icon_svg('mail') ?> Messages</a>
    <a class="<?= admin_active('reports.php') ?>" href="<?= url('admin/reports.php') ?>"><?= icon_svg('chart') ?> Reports</a>
    <a href="<?= url('index.php') ?>"><?= icon_svg('arrow') ?> Website</a>
</aside>
