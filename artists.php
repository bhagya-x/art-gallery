<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';
$pageTitle = 'Artists';
$artists = [];
if (db_connected()) {
    try {
        $artists = $pdo->query("SELECT artists.*, COUNT(artworks.id) AS artwork_count FROM artists LEFT JOIN artworks ON artists.id = artworks.artist_id GROUP BY artists.id ORDER BY artists.name")->fetchAll();
    } catch (Throwable $e) {
        $artists = [];
    }
}

if (empty($artists)) {
    $artists = [
        ['id' => 1, 'name' => 'Ethan Mitchell', 'location' => 'Venice, Italy', 'bio' => 'Known for layered abstract textures and calm ocean-inspired forms.', 'image' => 'assets/images/artist-ethan.jpg', 'artwork_count' => 2],
        ['id' => 2, 'name' => 'Lucas Brown', 'location' => 'Bangkok, Thailand', 'bio' => 'Creates expressive contemporary work with energetic colour movement.', 'image' => 'assets/images/artist-lucas.jpg', 'artwork_count' => 2],
        ['id' => 3, 'name' => 'Oliver Taylor', 'location' => 'Cape Town, South Africa', 'bio' => 'Works with vivid contrast and bold visual rhythm.', 'image' => 'assets/images/artist-oliver.jpg', 'artwork_count' => 1],
        ['id' => 4, 'name' => 'Amara Silva', 'location' => 'Colombo, Sri Lanka', 'bio' => 'Specialist in hand-drawn and traditional compositions for homes and galleries.', 'image' => 'assets/images/artist-amara.jpg', 'artwork_count' => 1],
    ];
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Artists</span><h1>Creative Minds</h1><p>Manage and display artist profiles and artwork contributions.</p></div></section>
<section class="section"><div class="container"><div class="grid-4">
<?php foreach ($artists as $artist): ?>
    <article class="artist-card">
        <img src="<?= url($artist['image']) ?>" alt="<?= e($artist['name']) ?>">
        <h3><?= e($artist['name']) ?></h3>
        <p><?= e($artist['location']) ?></p>
        <p><?= e($artist['bio']) ?></p>
        <span class="badge"><?= (int)$artist['artwork_count'] ?> Artworks</span>
    </article>
<?php endforeach; ?>
</div></div></section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
