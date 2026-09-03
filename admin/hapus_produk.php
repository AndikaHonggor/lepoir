<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkAdminLogin();

if (!isset($_GET['id'])) {
    redirect('produk.php');
}

$produk_id = sanitize($_GET['id']);
$produk = fetchSingleData("SELECT * FROM produk WHERE id = '$produk_id'");

if (!$produk) {
    redirect('produk.php');
}

// Delete image if exists
if (!empty($produk['gambar']) && (strpos($produk['gambar'], 'assets/uploads/') === 0 || strpos($produk['gambar'], 'uploads/') === 0)) {
    deleteImage(basename($produk['gambar']));
}

// Delete warna
executeQuery("DELETE FROM warna_produk WHERE produk_id = '$produk_id'");

// Delete produk
if (executeQuery("DELETE FROM produk WHERE id = '$produk_id'")) {
    $_SESSION['success'] = 'Produk berhasil dihapus!';
} else {
    $_SESSION['error'] = 'Gagal menghapus produk!';
}

redirect('produk.php');
?>
