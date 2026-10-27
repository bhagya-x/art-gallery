<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../config/database.php';
require_admin();
$pageTitle = 'Manage Artworks';
$items = $pdo->query("SELECT artworks.*, artists.name AS artist_name FROM artworks LEFT JOIN artists ON artworks.artist_id = artists.id ORDER BY artworks.created_at DESC")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Admin</span><h1>Manage Artworks</h1><p>Add, update, delete, and monitor artwork availability.</p></div></section>
<div class="container dashboard">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <section>
        <div class="panel">
            <div class="panel-head"><h2>Artwork List</h2><a class="btn btn-dark btn-small" href="<?= url('admin/artwork-form.php') ?>"><?= icon_svg('plus') ?> Add Artwork</a></div>
            <div class="table-wrap"><table><thead><tr><th>Title</th><th>Artist</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead><tbody>
            <?php foreach ($items as $art): ?>
                <tr>
                    <td><strong><?= e($art['title']) ?></strong><br><small><?= e($art['medium']) ?></small></td>
                    <td><?= e($art['artist_name'] ?? 'No Artist') ?></td>
                    <td><?= e($art['category']) ?></td>
                    <td><?= money((float)$art['price']) ?></td>
                    <td><?= status_badge($art['status']) ?></td>
                    <td><a class="btn btn-light btn-small" href="<?= url('admin/artwork-form.php?id=' . (int)$art['id']) ?>">Edit</a> <a class="btn btn-ghost btn-small" data-confirm="Delete artwork?" href="<?= url('admin/artwork-delete.php?id=' . (int)$art['id']) ?>">Delete</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table></div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
