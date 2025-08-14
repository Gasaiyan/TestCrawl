<?php
header('Content-Type: application/json; charset=utf-8');

// Thông tin MySQL
$host = "localhost";
$user = "root";
$pass = "";
$db   = "manager_tai";

// Kết nối MySQL
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Kết nối thất bại: " . $conn->connect_error]));
}

// ===== LINK GOOGLE SHEET PUBLIC DẠNG CSV =====
// Vào Google Sheets → File → Share → Publish to web → chọn CSV
$csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQoj0lS4bujEgh8pYEHDvUijdCazrSI7_2Dcj4w8vUnyeMyW1Ypzh1CAOnnqWMHXFSQdRIIoE3PsWg6/pub?gid=0&single=true&output=csv";

// Mở file CSV từ URL
if (($handle = fopen($csvUrl, "r")) === FALSE) {
    die(json_encode(["status" => "error", "message" => "Không đọc được dữ liệu từ Google Sheet"]));
}

// Chuẩn bị câu lệnh INSERT
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
    die(json_encode(["status" => "error", "message" => "Lỗi chuẩn bị câu lệnh: " . $conn->error]));
}

$stmt->bind_param("isssssss", $id, $title, $link, $image, $pubdate, $source, $savedtime, $category);

$rowIndex = 0;
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $rowIndex++;
    if ($rowIndex == 1) continue; // Bỏ qua dòng tiêu đề

    // Gán dữ liệu từ CSV
    $id        = (int)$data[0];
    $title     = $data[1];
    $link      = $data[2];
    $image     = $data[3];
    $pubdate   = !empty($data[4]) ? date("Y-m-d H:i:s", strtotime($data[4])) : null;
    $source    = $data[5];
    $savedtime = !empty($data[6]) ? date("Y-m-d H:i:s", strtotime($data[6])) : null;
    $category  = $data[7];

    $stmt->execute();
}

fclose($handle);
$stmt->close();
$conn->close();

echo json_encode(["status" => "success", "message" => "Đã lưu dữ liệu từ Google Sheet vào MySQL"]);