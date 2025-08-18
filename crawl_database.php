<?php
header('Content-Type: application/json; charset=utf-8');

// ===== Thông tin MySQL =====
$host = "localhost";
$user = "root";
$pass = "";
$db   = "manager_tai";

// Kết nối MySQL
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Kết nối thất bại: " . $conn->connect_error]));
}

// ===== Hàm làm sạch ký tự HTML Entity =====
function cleanText($str) {
    if (!$str) return '';
    // Giải mã 2 lần để xử lý cả trường hợp &amp;apos;
    $decoded = html_entity_decode($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Loại bỏ khoảng trắng dư thừa
    return trim($decoded);
}

// ===== LINK GOOGLE SHEET PUBLIC DẠNG JSONP =====
$jsonUrl = "https://docs.google.com/spreadsheets/d/1IfVBDmE6XKtFaD4i5smZR17L87TZ3b4RPTdcwCZpQGo/gviz/tq?tqx=out:json&gid=0";

// Lấy dữ liệu từ Google Sheet
$response = file_get_contents($jsonUrl);
if (!$response) {
    die(json_encode(["status" => "error", "message" => "Ko doc dc du lieu tu Google Sheet"]));
}

// Cắt bỏ JSONP để lấy JSON thuần
$start = strpos($response, '{');
$end   = strrpos($response, '}') + 1;
$json  = substr($response, $start, $end - $start);

$data = json_decode($json, true);
if (!$data) {
    die(json_encode(["status" => "error", "message" => "Ko parse dc JSON tu Google Sheet"]));
}

// ===== Chuẩn bị câu lệnh INSERT =====
$stmt = $conn->prepare("INSERT INTO crawl_news (id, title, link, image, pubdate, source, savedtime, category) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                        title = VALUES(title),
                        link = VALUES(link),
                        image = VALUES(image),
                        pubdate = VALUES(pubdate),
                        source = VALUES(source),
                        savedtime = VALUES(savedtime),
                        category = VALUES(category)");

if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Loi cau lenh: " . $conn->error]));
}

$stmt->bind_param("isssssss", $id, $title, $link, $image, $pubdate, $source, $savedtime, $category);

// ===== Đọc dữ liệu từ Google Sheet JSON =====
$rows = $data['table']['rows'];
foreach ($rows as $index => $row) {
    if ($index == 0) continue; // bỏ qua tiêu đề

    $id        = isset($row['c'][0]['v']) ? (int)$row['c'][0]['v'] : 0;
    $title     = cleanText($row['c'][1]['v'] ?? '');
    $link      = $row['c'][2]['v'] ?? '';
    $image     = $row['c'][3]['v'] ?? '';
    $pubdate   = !empty($row['c'][4]['v']) ? date("Y-m-d H:i:s", strtotime($row['c'][4]['v'])) : null;
    $source    = cleanText($row['c'][5]['v'] ?? '');
    $savedtime = date("Y-m-d H:i:s"); // lúc lưu
    $category  = cleanText($row['c'][7]['v'] ?? '');

    // Debug check xem có còn &apos; không
    // echo "Before Insert: $title\n";

    $stmt->execute();
}

$stmt->close();
$conn->close();

echo json_encode(["status" => "success", "message" => "Luu du lieu tu Google Sheet vao MySQL !!!"]);
