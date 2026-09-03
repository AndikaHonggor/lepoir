<?php
session_start();

// File untuk fungsi-fungsi umum aplikasi

// Fungsi untuk redirect
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// Fungsi untuk cek login admin
function checkAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        redirect('login.php');
    }
}

// Fungsi untuk logout
function logout() {
    session_destroy();
    redirect('index.php');
}

// Fungsi untuk format rupiah
function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// Fungsi untuk upload gambar
function uploadImage($file) {
    if (!isset($file) || !is_array($file) || $file['error'] !== UPLOAD_ERR_OK || empty($file['name'])) {
        return ['success' => false, 'message' => 'File gagal diupload atau tidak ada file yang dipilih'];
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = basename($file['name']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF'];
    }

    $target_dir = dirname(__DIR__) . '/assets/uploads';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (!is_dir($target_dir) || !is_writable($target_dir)) {
        return ['success' => false, 'message' => 'Folder upload tidak bisa ditulis. Cek permission folder assets/uploads'];
    }

    $new_filename = 'produk_' . time() . '_' . uniqid() . '.' . $ext;
    $upload_path = $target_dir . '/' . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => true, 'filename' => $new_filename, 'path' => 'assets/uploads/' . $new_filename];
    }

    return ['success' => false, 'message' => 'Gagal mengupload file. Pastikan folder assets/uploads bisa ditulis'];
}

// Fungsi untuk upload logo perusahaan
function uploadLogo($file) {
    if (!isset($file) || !is_array($file) || $file['error'] !== UPLOAD_ERR_OK || empty($file['name'])) {
        return ['success' => false, 'message' => 'File logo gagal diupload atau tidak ada file yang dipilih'];
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo(basename($file['name']), PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return ['success' => false, 'message' => 'Format logo tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP'];
    }

    $target_dir = dirname(__DIR__) . '/assets/uploads';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $new_filename = 'logo_' . time() . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $target_dir . '/' . $new_filename)) {
        return ['success' => true, 'filename' => $new_filename, 'path' => 'assets/uploads/' . $new_filename];
    }

    return ['success' => false, 'message' => 'Gagal mengupload logo perusahaan'];
}

// Fungsi untuk delete file gambar
function deleteImage($filename) {
    $file_path = dirname(__DIR__) . '/assets/uploads/' . basename($filename);
    if (file_exists($file_path)) {
        unlink($file_path);
        return true;
    }
    return false;
}

// Fungsi untuk password hashing
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Fungsi untuk verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Fungsi untuk generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}
?>
