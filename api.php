<?php
/**
 * "LE'POIR"- Katalog Produk
 * API untuk AJAX requests (opsional untuk pengembangan lebih lanjut)
 */

require_once 'config/database.php';
require_once 'config/functions.php';

// Set header JSON
header('Content-Type: application/json');

// Get action dari request
$action = isset($_GET['action']) ? sanitize($_GET['action']) : '';

$response = [
    'success' => false,
    'message' => 'Invalid action',
    'data' => null
];

// API Endpoints
switch ($action) {
    
    // Get all products
    case 'get_products':
        $products = fetchData("SELECT * FROM produk ORDER BY id DESC");
        foreach ($products as &$product) {
            $product['colors'] = fetchData("SELECT warna FROM warna_produk WHERE produk_id = '" . $product['id'] . "'");
        }
        $response['success'] = true;
        $response['data'] = $products;
        break;
    
    // Get single product
    case 'get_product':
        $product_id = sanitize($_GET['id'] ?? 0);
        $product = fetchSingleData("SELECT * FROM produk WHERE id = '$product_id'");
        if ($product) {
            $product['colors'] = fetchData("SELECT warna FROM warna_produk WHERE produk_id = '$product_id'");
            $response['success'] = true;
            $response['data'] = $product;
        } else {
            $response['message'] = 'Product not found';
        }
        break;
    
    // Get settings
    case 'get_settings':
        $settings = fetchSingleData("SELECT * FROM pengaturan LIMIT 1");
        $response['success'] = true;
        $response['data'] = $settings;
        break;
    
    default:
        $response['message'] = 'Unknown action';
        break;
}

echo json_encode($response);
?>
