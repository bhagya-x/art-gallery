<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Artworks';
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$artist = trim($_GET['artist'] ?? '');

$artworks = [];
$artists = [];
$categories = ['Modern', 'Traditional', 'Hand Drawn', 'Abstract', 'Landscape'];

if (db_connected()) {
    try {
        $sql = "SELECT artworks.*, artists.name AS artist_name FROM artworks LEFT JOIN artists ON artworks.artist_id = artists.id WHERE 1=1";
        $params = [];
        if ($q !== '') {
            $sql .= " AND (artworks.title LIKE ? OR artworks.description LIKE ? OR artworks.medium LIKE ?)";
            $params[] = "%{$q}%"; $params[] = "%{$q}%"; $params[] = "%{$q}%";
        }
        if ($category !== '') { $sql .= " AND artworks.category = ?"; $params[] = $category; }
        if ($artist !== '') { $sql .= " AND artists.id = ?"; $params[] = $artist; }
        $sql .= " ORDER BY artworks.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $artworks = $stmt->fetchAll();
        $artists = $pdo->query("SELECT id, name FROM artists ORDER BY name")->fetchAll();
    } catch (Throwable $e) {
        $artworks = [];
        $artists = [];
    }
}

if (empty($artworks) && !db_connected()) {
    $artworks = [
        ['id' => 1, 'title' => 'Whispers of Autumn', 'slug' => 'whispers-of-autumn', 'category' => 'Modern', 'price' => 45500.00, 'image' => 'assets/images/autumn.jpg', 'artist_name' => 'Lucas Brown', 'status' => 'Available'],
        ['id' => 2, 'title' => 'Floating Worlds', 'slug' => 'floating-worlds', 'category' => 'Abstract', 'price' => 62000.00, 'image' => 'assets/images/floating-worlds.jpg', 'artist_name' => 'Ethan Mitchell', 'status' => 'Available'],
        ['id' => 3, 'title' => 'Tranquil Oasis', 'slug' => 'tranquil-oasis', 'category' => 'Landscape', 'price' => 78000.00, 'image' => 'assets/images/tranquil-oasis.jpg', 'artist_name' => 'Ethan Mitchell', 'status' => 'Available'],
        ['id' => 4, 'title' => 'Midnight Bloom', 'slug' => 'midnight-bloom', 'category' => 'Abstract', 'price' => 52500.00, 'image' => 'assets/images/midnight-bloom.jpg', 'artist_name' => 'Oliver Taylor', 'status' => 'Reserved'],
        ['id' => 5, 'title' => 'Golden Memory', 'slug' => 'golden-memory', 'category' => 'Traditional', 'price' => 68500.00, 'image' => 'assets/images/golden-memory.jpg', 'artist_name' => 'Amara Silva', 'status' => 'Available'],
        ['id' => 6, 'title' => 'Sea of Lines', 'slug' => 'sea-of-lines', 'category' => 'Hand Drawn', 'price' => 38000.00, 'image' => 'assets/images/sea-of-lines.jpg', 'artist_name' => 'Lucas Brown', 'status' => 'Available'],
    ];
    $artists = [
        ['id' => 1, 'name' => 'Ethan Mitchell'],
        ['id' => 2, 'name' => 'Lucas Brown'],
        ['id' => 3, 'name' => 'Oliver Taylor'],
        ['id' => 4, 'name' => 'Amara Silva'],
    ];
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Gallery</span>
        <h1>Curated Artworks</h1>
        <p>Search, filter, view details, add artworks to cart, and place orders with clear order tracking.</p>
    </div>
</section>
<div class="container">
    <form class="filters" method="get">
        <input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Search artwork, medium, style...">
        <select class="form-control" name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $item): ?>
                <option value="<?= e($item) ?>" <?= $category === $item ? 'selected' : '' ?>><?= e($item) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-control" name="artist">
            <option value="">All Artists</option>
            <?php foreach ($artists as $item): ?>
                <option value="<?= (int)$item['id'] ?>" <?= $artist == $item['id'] ? 'selected' : '' ?>><?= e($item['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-dark" type="submit">Search</button>
    </form>
    <?php if (!$artworks): ?>
        <div class="empty-state"><h3>No artworks found</h3><p>Try changing the search or filter options.</p></div>
    <?php else: ?>
        <div class="grid-3 section" style="padding-top:0">
            <?php foreach ($artworks as $art): ?>
                <article class="card art-card">
                    <img src="<?= url($art['image']) ?>" alt="<?= e($art['title']) ?>">
                    <div class="art-card-body">
                        <div class="card-meta"><span><?= e($art['category']) ?></span><span class="badge <?= $art['status'] === 'Available' ? 'badge-success' : ($art['status'] === 'Sold' ? 'badge-danger' : 'badge-warning') ?>"><?= e($art['status']) ?></span></div>
                        <h3><?= e($art['title']) ?></h3>
                        <div class="card-meta"><span><?= e($art['artist_name'] ?? 'Unknown Artist') ?></span><strong class="price"><?= money((float)$art['price']) ?></strong></div>
                        <div class="card-actions">
                            <a class="btn btn-light" href="<?= url('artwork.php?slug=' . urlencode($art['slug'])) ?>">View</a>
                            <?php if ($art['status'] === 'Available'): ?>
                                <a class="btn btn-dark" href="<?= url('cart.php?action=add&id=' . $art['id']) ?>">Add Cart</a>
                            <?php else: ?>
                                <button class="btn btn-ghost" type="button" disabled>Not Available</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
