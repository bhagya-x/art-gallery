<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';
$pageTitle = 'Exhibitions';
$items = [];
if (db_connected()) {
    try {
        $items = $pdo->query("SELECT * FROM exhibitions ORDER BY start_date DESC")->fetchAll();
    } catch (Throwable $e) {
        $items = [];
    }
}

if (empty($items)) {
    $items = [
        ['title' => 'New Forms in Modern Art', 'location' => 'Colombo Art Hall', 'start_date' => '2026-06-01', 'end_date' => '2026-06-10', 'image' => 'assets/images/exhibition-01.jpg', 'description' => 'A curated exhibition showcasing modern, traditional, and hand-drawn artworks.'],
        ['title' => 'Collectors Private Viewing', 'location' => 'Galle Creative Studio', 'start_date' => '2026-07-05', 'end_date' => '2026-07-08', 'image' => 'assets/images/custom-work.jpg', 'description' => 'Private viewing event for collectors and gallery customers.'],
    ];
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">Exhibitions</span><h1>Gallery Events</h1><p>Display exhibitions and public or private art viewing events.</p></div></section>
<section class="section"><div class="container"><div class="grid-3">
<?php foreach ($items as $item): ?>
    <article class="card article-card">
        <img src="<?= url($item['image']) ?>" alt="<?= e($item['title']) ?>">
        <div class="art-card-body">
            <div class="card-meta"><span><?= e($item['location']) ?></span><span><?= e(date('M d', strtotime($item['start_date']))) ?> - <?= e(date('M d, Y', strtotime($item['end_date']))) ?></span></div>
            <h3><?= e($item['title']) ?></h3>
            <p><?= e($item['description']) ?></p>
        </div>
    </article>
<?php endforeach; ?>
</div></div></section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
