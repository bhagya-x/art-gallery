<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../config/database.php';
require_admin();

$editId = (int)($_GET['edit'] ?? 0);
$editArtist = null;
if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM artists WHERE id = ? LIMIT 1");
    $stmt->execute([$editId]);
    $editArtist = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $image = trim($_POST['image'] ?? 'assets/images/artist-amara.jpg');
    if ($name === '') {
        $_SESSION['flash_error'] = 'Artist name is required.';
    } elseif (!preg_match("/^[\p{L}\s.'-]+$/u", $name)) {
        $_SESSION['flash_error'] = 'Artist name can contain letters and spaces only. Numbers are not allowed.';
    } else {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE artists SET name=?, location=?, bio=?, image=? WHERE id=?");
            $stmt->execute([$name, $location, $bio, $image, $id]);
            $_SESSION['flash_success'] = 'Artist updated successfully.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO artists (name, location, bio, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $location, $bio, $image]);
            $_SESSION['flash_success'] = 'Artist added successfully.';
        }
        redirect('admin/artists.php');
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM artists WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash_success'] = 'Artist deleted successfully.';
    redirect('admin/artists.php');
}

$artists = $pdo->query("SELECT artists.*, COUNT(artworks.id) AS artwork_count FROM artists LEFT JOIN artworks ON artists.id = artworks.artist_id GROUP BY artists.id ORDER BY artists.name")->fetchAll();
$pageTitle = 'Manage Artists';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Admin</span><h1>Manage Artists</h1><p>Add and update artist profiles and artwork contributions.</p></div></section>
<div class="container dashboard">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <section>
        <form class="panel" method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= (int)($editArtist['id'] ?? 0) ?>">
            <div class="panel-head"><h2><?= $editArtist ? 'Edit Artist' : 'Add Artist' ?></h2></div>
            <div class="form-grid">
                <div class="form-group"><label>Name</label><input class="form-control" name="name" value="<?= e($editArtist['name'] ?? '') ?>" required pattern="[A-Za-z\u00C0-\u024F\u0D80-\u0DFF\s.'-]+" title="Please enter letters and spaces only. Numbers are not allowed." oninput="this.value=this.value.replace(/[^\p{L}\s.'-]/gu,'')"></div>
                <div class="form-group"><label>Location</label><input class="form-control" name="location" value="<?= e($editArtist['location'] ?? '') ?>"></div>
                <div class="form-group"><label>Image Path / URL</label><input class="form-control" name="image" value="<?= e($editArtist['image'] ?? 'assets/images/artist-amara.jpg') ?>"></div>
            </div>
            <div class="form-group"><label>Bio</label><textarea class="form-control" name="bio"><?= e($editArtist['bio'] ?? '') ?></textarea></div>
            <button class="btn btn-dark" type="submit">Save Artist</button>
            <?php if ($editArtist): ?><a class="btn btn-light" href="<?= url('admin/artists.php') ?>">Cancel</a><?php endif; ?>
        </form>
        <div class="panel">
            <h2>Artist List</h2>
            <div class="table-wrap" style="margin-top:18px"><table><thead><tr><th>Name</th><th>Location</th><th>Artworks</th><th>Actions</th></tr></thead><tbody>
                <?php foreach ($artists as $artist): ?>
                    <tr><td><strong><?= e($artist['name']) ?></strong></td><td><?= e($artist['location']) ?></td><td><?= (int)$artist['artwork_count'] ?></td><td><a class="btn btn-light btn-small" href="<?= url('admin/artists.php?edit=' . (int)$artist['id']) ?>">Edit</a> <a class="btn btn-ghost btn-small" href="<?= url('admin/artists.php?delete=' . (int)$artist['id']) ?>" data-confirm="Delete artist?">Delete</a></td></tr>
                <?php endforeach; ?>
            </tbody></table></div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
