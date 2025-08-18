<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "crawl_news";

$sqlFile = __DIR__ . "/crawl_news.sql";
if (!file_exists($sqlFile)) {
    die("❌ Không tìm thấy file SQL!");
}

// Kết nối MySQL
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("❌ Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra database đã tồn tại chưa
$dbCheck = $conn->query("SHOW DATABASES LIKE '$dbname'");
if ($dbCheck && $dbCheck->num_rows > 0) {
    echo "⚠️ Database `$dbname` đã tồn tại.<br>";

    // Chọn database
    $conn->select_db($dbname);

    // Xóa 3 bảng cũ (nếu có)
    $dropTables = ["crawl_news", "token_login", "users"];
    foreach ($dropTables as $table) {
        $conn->query("DROP TABLE IF EXISTS `$table`");
        echo "🗑️ Đã xóa bảng `$table` (nếu có).<br>";
    }

} else {
    // Nếu chưa có thì tạo mới DB
    if ($conn->query("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci") === TRUE) {
        echo "✅ Database `$dbname` đã được tạo.<br>";
    } else {
        die("❌ Lỗi tạo DB: " . $conn->error);
    }
    $conn->select_db($dbname);
}

// Import file SQL (chỉ 3 bảng và dữ liệu trong file)
$sql = file_get_contents($sqlFile);

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "🎉 Import file SQL thành công!";
} else {
    echo "❌ Lỗi khi import: " . $conn->error;
}

$conn->close();
?>
