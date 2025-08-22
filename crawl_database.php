<?php
header('Content-Type: application/json; charset=utf-8');

// ===== Thông tin MySQL =====
$host = "localhost";
$user = "root";
$pass = "";
$db   = "crawl_news";

// Kết nối MySQL
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Kết nối thất bại: " . $conn->connect_error]));
}

// ===== Hàm làm sạch ký tự HTML Entity =====
function cleanText($str) {
    if (!$str) return '';
    $decoded = html_entity_decode($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim($decoded);
}

// ===== Hàm xử lý URL ảnh thành thumbnail (Cách 1) =====
function makeThumbnailUrl($url) {
    if (!$url) return '';

    // Thanh Niên (thanhnien.vn)
    if (strpos($url, "thanhnien.vn") !== false) {
        if (strpos($url, "w=") !== false) {
            return preg_replace('/w=\d+/', 'w=200', $url);
        } else {
            return $url . (strpos($url, '?') !== false ? '&' : '?') . 'w=200';
        }
    }

    // Tuổi Trẻ (tuoitre.vn)
    if (strpos($url, "tuoitre.vn") !== false) {
        if (strpos($url, "w=") !== false) {
            return preg_replace('/w=\d+/', 'w=200', $url);
        } elseif (strpos($url, "zoom=") !== false) {
            return preg_replace('/zoom=\d+/', 'zoom=2', $url);
        } else {
            return $url . (strpos($url, '?') !== false ? '&' : '?') . 'w=200';
        }
    }
    return $url;
}

// ===== LINK GOOGLE SHEET PUBLIC =====
$jsonUrl = "https://docs.google.com/spreadsheets/d/1IfVBDmE6XKtFaD4i5smZR17L87TZ3b4RPTdcwCZpQGo/gviz/tq?tqx=out:json&gid=0";
$response = file_get_contents($jsonUrl);
if (!$response) {
    die(json_encode(["status" => "error", "message" => "Ko doc dc du lieu tu Google Sheet"]));
}

$start = strpos($response, '{');
$end   = strrpos($response, '}') + 1;
$json  = substr($response, $start, $end - $start);

$data = json_decode($json, true);
if (!$data) {
    die(json_encode(["status" => "error", "message" => "Ko parse dc JSON tu Google Sheet"]));
}

// ===== INSERT DB =====
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

$stmt->bind_param("isssssss", $id, $title, $link, $imageRaw, $pubdate, $source, $savedtime, $category);

$rows = $data['table']['rows'];
foreach ($rows as $index => $row) {
    if ($index == 0) continue;

    $id        = isset($row['c'][0]['v']) ? (int)$row['c'][0]['v'] : 0;
    $title     = cleanText($row['c'][1]['v'] ?? '');
    $link      = $row['c'][2]['v'] ?? '';
    $imageRaw  = $row['c'][3]['v'] ?? '';
    $image     = makeThumbnailUrl($imageRaw);
    $pubdate   = !empty($row['c'][4]['v']) ? date("Y-m-d H:i:s", strtotime($row['c'][4]['v'])) : null;
    $source    = cleanText($row['c'][5]['v'] ?? '');
    $savedtime = date("Y-m-d H:i:s");
    $category  = cleanText($row['c'][7]['v'] ?? '');

    $stmt->execute();
}

$stmt->close();
$conn->close();

echo json_encode(["status" => "success", "message" => "Luu du lieu tu Google Sheet vao MySQL (ảnh đã đổi sang thumbnail URL) !!!"]);
