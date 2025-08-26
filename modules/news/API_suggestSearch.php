<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config.php';

$query = $_POST['query'] ?? '';
$query = trim($query);

if (strlen($query) < 2) {
    echo json_encode(["suggestions" => []]);
    exit;
}

$stmt = $conn->prepare("SELECT title FROM news WHERE title LIKE ? ORDER BY pubDate DESC LIMIT 5");
$like = "%" . $query . "%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row['title'];
}

$stmt->close();
$conn->close();

echo json_encode(["suggestions" => $suggestions], JSON_UNESCAPED_UNICODE);
