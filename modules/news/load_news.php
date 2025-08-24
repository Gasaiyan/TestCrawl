<?php
define('_TAI', true);
require __DIR__ . '/../../includes/database.php';
require __DIR__ . '/../../includes/functions.php';

$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Nhận tham số từ GET
$keyword  = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$where = " WHERE 1=1 ";

// Nếu có keyword thì thêm vào điều kiện
if (!empty($keyword)) {
    $keyword = addslashes($keyword);
    $where .= " AND title LIKE '%$keyword%' ";
}

// Nếu có category thì thêm vào điều kiện
if (!empty($category)) {
    $category = addslashes($category);
    $where .= " AND category = '$category' ";
}

// Query
$sql = "SELECT * FROM crawl_news $where ORDER BY pubDate DESC LIMIT $perPage OFFSET $offset";
$listNews = getAll($sql);

// Trả JSON
header('Content-Type: application/json');
echo json_encode($listNews, JSON_UNESCAPED_UNICODE);