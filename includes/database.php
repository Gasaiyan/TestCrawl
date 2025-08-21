<?php
if(!defined('_TAI')) {
    die('Truy cập không hợp lệ');
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "crawl_news";

// Kết nối MySQLi (không dùng PDO nữa)
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn nhiều dòng dữ liệu
function getAll($sql) {
    global $conn;
    $result = $conn->query($sql);
    if(!$result) {
        die("Lỗi query: " . $conn->error);
    }
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// Đếm số dòng trả về
function getRows($sql) {
    global $conn;
    $result = $conn->query($sql);
    if(!$result) {
        die("Lỗi query: " . $conn->error);
    }
    return $result->num_rows;
}

// Truy vấn 1 dòng dữ liệu
function getOne($sql) {
    global $conn;
    $result = $conn->query($sql);
    if(!$result) {
        die("Lỗi query: " . $conn->error);
    }
    return $result->fetch_assoc();
}

// Insert dữ liệu
function insert($table, $data) {
    global $conn;
    $columns = implode(", ", array_keys($data));
    $values  = "'" . implode("','", array_map([$conn, 'real_escape_string'], $data)) . "'";
    $sql = "INSERT INTO $table ($columns) VALUES ($values)";
    $rel = $conn->query($sql);
    if(!$rel) {
        die("Lỗi insert: " . $conn->error);
    }
    return $rel;
}

// Update dữ liệu
function update($table, $data, $condition = '') {
    global $conn;
    $updateArr = [];
    foreach($data as $key => $value) {
        $updateArr[] = "$key='" . $conn->real_escape_string($value) . "'";
    }
    $update = implode(", ", $updateArr);

    $sql = "UPDATE $table SET $update";
    if(!empty($condition)) {
        $sql .= " WHERE $condition";
    }

    $rel = $conn->query($sql);
    if(!$rel) {
        die("Lỗi update: " . $conn->error);
    }
    return $rel;
}

// Xóa dữ liệu
function delete($table, $condition = '') {
    global $conn;
    $sql = "DELETE FROM $table";
    if(!empty($condition)) {
        $sql .= " WHERE $condition";
    }
    $rel = $conn->query($sql);
    if(!$rel) {
        die("Lỗi delete: " . $conn->error);
    }
    return $rel;
}

// Lấy ID dòng dữ liệu mới insert
function lastID() {
    global $conn;
    return $conn->insert_id;
}
?>