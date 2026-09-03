<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'aster_wear');

// Koneksi Database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");

// Fungsi untuk escape string
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, $data);
}

// Fungsi untuk fetch data
function fetchData($query) {
    global $conn;
    $result = $conn->query($query);
    $data = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

// Fungsi untuk fetch single row
function fetchSingleData($query) {
    global $conn;
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Fungsi untuk execute query
function executeQuery($query) {
    global $conn;
    return $conn->query($query);
}
?>
