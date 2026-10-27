<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_admin();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM artworks WHERE id = ?");
$stmt->execute([$id]);
$_SESSION['flash_success'] = 'Artwork deleted successfully.';
redirect('admin/artworks.php');
