<?php
define('_TAI', true);
require __DIR__ . '/../../includes/database.php';
require __DIR__ . '/../../includes/functions.php';

$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$sql = "SELECT * FROM crawl_news ORDER BY pubDate DESC LIMIT $perPage OFFSET $offset";
$listNews = getAll($sql);

// Trả JSON
header('Content-Type: application/json');
echo json_encode($listNews, JSON_UNESCAPED_UNICODE);