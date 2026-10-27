<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../config/database.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$art = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM artworks WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $art = $stmt->fetch();
    if (!$art) { $_SESSION['flash_error'] = 'Artwork not found.'; redirect('admin/artworks.php'); }
}
$artists = $pdo->query("SELECT id, name FROM artists ORDER BY name")->fetchAll();
$categories = ['Modern', 'Traditional', 'Hand Drawn', 'Abstract', 'Landscape'];
$statuses = ['Available', 'Reserved', 'Sold'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title = trim($_POST['title'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
    $artistId = $_POST['artist_id'] ?: null;
    $category = $_POST['category'] ?? 'Modern';
    $price = (float)($_POST['price'] ?? 0);
    $size = trim($_POST['size'] ?? '');
    $medium = trim($_POST['medium'] ?? '');
    $image = trim($_POST['image'] ?? 'assets/images/golden-memory.jpg');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'Available';
    $featured = isset($_POST['featured']) ? 1 : 0;

    if ($title === '' || $price <= 0) {
        $_SESSION['flash_error'] = 'Title and valid price are required.';
    } else {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE artworks SET artist_id=?, title=?, slug=?, category=?, price=?, size=?, medium=?, image=?, description=?, status=?, featured=? WHERE id=?");
            $stmt->execute([$artistId, $title, $slug . '-' . $id, $category, $price, $size, $medium, $image, $description, $status, $featured, $id]);
            $_SESSION['flash_success'] = 'Artwork updated successfully.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO artworks (artist_id, title, slug, category, price, size, medium, image, description, status, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$artistId, $title, $slug . '-' . time(), $category, $price, $size, $medium, $image, $description, $status, $featured]);
            $_SESSION['flash_success'] = 'Artwork added successfully.';
        }
        redirect('admin/artworks.php');
    }
}
$pageTitle = $id ? 'Edit Artwork' : 'Add Artwork';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Admin</span><h1><?= $id ? 'Edit Artwork' : 'Add Artwork' ?></h1><p>Use image path like assets/images/autumn.jpg or full image URL.</p></div></section>
<div class="container dashboard">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <section>
        <form class="panel" method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="form-grid">
                <div class="form-group"><label>Title</label><input class="form-control" name="title" value="<?= e($art['title'] ?? '') ?>" required></div>
                <div class="form-group"><label>Artist</label><select class="form-control" name="artist_id"><option value="">Select Artist</option><?php foreach ($artists as $a): ?><option value="<?= (int)$a['id'] ?>" <?= ($art['artist_id'] ?? '') == $a['id'] ? 'selected' : '' ?>><?= e($a['name']) ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Category</label><select class="form-control" name="category"><?php foreach ($categories as $c): ?><option value="<?= e($c) ?>" <?= ($art['category'] ?? '') === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Price</label><input class="form-control" type="number" step="0.01" min="1" name="price" value="<?= e($art['price'] ?? '') ?>" required></div>
                <div class="form-group"><label>Size</label><input class="form-control" name="size" value="<?= e($art['size'] ?? '') ?>"></div>
                <div class="form-group"><label>Medium</label><input class="form-control" name="medium" value="<?= e($art['medium'] ?? '') ?>"></div>
                <div class="form-group"><label>Image Path / URL</label><input class="form-control" name="image" value="<?= e($art['image'] ?? 'assets/images/golden-memory.jpg') ?>"></div>
                <div class="form-group"><label>Status</label><select class="form-control" name="status"><?php foreach ($statuses as $s): ?><option value="<?= e($s) ?>" <?= ($art['status'] ?? 'Available') === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="form-group"><label>Description</label><textarea class="form-control" name="description"><?= e($art['description'] ?? '') ?></textarea></div>
            <label style="display:flex;gap:10px;align-items:center;margin-bottom:16px"><input type="checkbox" name="featured" <?= !empty($art['featured']) ? 'checked' : '' ?>> Featured artwork</label>
            <button class="btn btn-dark" type="submit">Save Artwork</button>
            <a class="btn btn-light" href="<?= url('admin/artworks.php') ?>">Cancel</a>
        </form>
    </section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
