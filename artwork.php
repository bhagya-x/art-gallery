<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';

$slug = $_GET['slug'] ?? '';
$art = null;

if (db_connected()) {
    try {
        $stmt = $pdo->prepare("SELECT artworks.*, artists.name AS artist_name, artists.location AS artist_location FROM artworks LEFT JOIN artists ON artworks.artist_id = artists.id WHERE artworks.slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $art = $stmt->fetch();
    } catch (Throwable $e) {
        $art = null;
    }
}

if (!$art) {
    $fallbackArtworks = [
        'whispers-of-autumn' => ['id' => 1, 'title' => 'Whispers of Autumn', 'slug' => 'whispers-of-autumn', 'category' => 'Modern', 'price' => 45500.00, 'size' => '24 x 30 inch', 'medium' => 'Acrylic on canvas', 'image' => 'assets/images/autumn.jpg', 'description' => 'A bright modern artwork created with warm autumn colours and bold layered strokes.', 'status' => 'Available', 'artist_name' => 'Lucas Brown', 'artist_location' => 'Bangkok, Thailand'],
        'floating-worlds' => ['id' => 2, 'title' => 'Floating Worlds', 'slug' => 'floating-worlds', 'category' => 'Abstract', 'price' => 62000.00, 'size' => '30 x 36 inch', 'medium' => 'Mixed media', 'image' => 'assets/images/floating-worlds.jpg', 'description' => 'An abstract composition that explores flow, movement, and inner balance.', 'status' => 'Available', 'artist_name' => 'Ethan Mitchell', 'artist_location' => 'Venice, Italy'],
        'tranquil-oasis' => ['id' => 3, 'title' => 'Tranquil Oasis', 'slug' => 'tranquil-oasis', 'category' => 'Landscape', 'price' => 78000.00, 'size' => '32 x 40 inch', 'medium' => 'Oil on canvas', 'image' => 'assets/images/tranquil-oasis.jpg', 'description' => 'A peaceful artwork suitable for premium interior spaces and galleries.', 'status' => 'Available', 'artist_name' => 'Ethan Mitchell', 'artist_location' => 'Venice, Italy'],
        'midnight-bloom' => ['id' => 4, 'title' => 'Midnight Bloom', 'slug' => 'midnight-bloom', 'category' => 'Abstract', 'price' => 52500.00, 'size' => '24 x 24 inch', 'medium' => 'Acrylic ink', 'image' => 'assets/images/midnight-bloom.jpg', 'description' => 'A dramatic dark canvas with soft floral movement and premium contrast.', 'status' => 'Reserved', 'artist_name' => 'Oliver Taylor', 'artist_location' => 'Cape Town, South Africa'],
        'golden-memory' => ['id' => 5, 'title' => 'Golden Memory', 'slug' => 'golden-memory', 'category' => 'Traditional', 'price' => 68500.00, 'size' => '28 x 34 inch', 'medium' => 'Natural pigment', 'image' => 'assets/images/golden-memory.jpg', 'description' => 'Traditional inspired artwork with elegant warm tones and cultural richness.', 'status' => 'Available', 'artist_name' => 'Amara Silva', 'artist_location' => 'Colombo, Sri Lanka'],
        'sea-of-lines' => ['id' => 6, 'title' => 'Sea of Lines', 'slug' => 'sea-of-lines', 'category' => 'Hand Drawn', 'price' => 38000.00, 'size' => '18 x 24 inch', 'medium' => 'Ink and watercolour', 'image' => 'assets/images/sea-of-lines.jpg', 'description' => 'Hand-drawn linework blended with soft colour layers and minimal movement.', 'status' => 'Available', 'artist_name' => 'Lucas Brown', 'artist_location' => 'Bangkok, Thailand'],
    ];

    if (isset($fallbackArtworks[$slug])) {
        $art = $fallbackArtworks[$slug];
    } else {
        $_SESSION['flash_error'] = 'Artwork not found.';
        redirect('gallery.php');
    }
}
$pageTitle = $art['title'];
require_once __DIR__ . '/includes/header.php';
?>
<div class="container detail-grid">
    <div class="detail-img">
        <img src="<?= url($art['image']) ?>" alt="<?= e($art['title']) ?>">
    </div>
    <div class="detail-content">
        <span class="eyebrow"><?= e($art['category']) ?></span>
        <h1><?= e($art['title']) ?></h1>
        <span class="badge <?= $art['status'] === 'Available' ? 'badge-success' : ($art['status'] === 'Sold' ? 'badge-danger' : 'badge-warning') ?>"><?= e($art['status']) ?></span>
        <p><?= e($art['description']) ?></p>
        <div class="detail-list">
            <div class="detail-item"><span>Artist</span><strong><?= e($art['artist_name'] ?? 'Unknown') ?></strong></div>
            <div class="detail-item"><span>Location</span><strong><?= e($art['artist_location'] ?? 'Gallery') ?></strong></div>
            <div class="detail-item"><span>Medium</span><strong><?= e($art['medium']) ?></strong></div>
            <div class="detail-item"><span>Size</span><strong><?= e($art['size']) ?></strong></div>
        </div>
        <h2><?= money((float)$art['price']) ?></h2>
        <?php if ($art['status'] === 'Available'): ?>
            <form class="qty-row" method="post" action="<?= url('cart.php') ?>">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= (int)$art['id'] ?>">
                <input class="form-control" type="number" min="1" max="5" name="qty" value="1" aria-label="Quantity">
                <button class="btn btn-dark" type="submit">Add to Cart <?= icon_svg('cart') ?></button>
                <a class="btn btn-light" href="<?= url('custom-order.php') ?>">Custom Request</a>
            </form>
        <?php else: ?>
            <a class="btn btn-light" href="<?= url('messages.php') ?>">Ask About Availability</a>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
