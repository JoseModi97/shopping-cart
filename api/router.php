<?php
require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/profile.php';
require_once __DIR__ . '/routes/products.php';
require_once __DIR__ . '/routes/cart.php';
require_once __DIR__ . '/routes/orders.php';
require_once __DIR__ . '/routes/ai.php';

function handle_request() {
    $method = $_SERVER['REQUEST_METHOD'];
    $endpoint = $_SERVER['REQUEST_URI'];

    if (strpos($endpoint, '/api/auth') === 0) {
        handle_auth_routes($method, $endpoint);
    } elseif (strpos($endpoint, '/api/profile') === 0) {
        handle_profile_routes($method, $endpoint);
    } elseif (strpos($endpoint, '/api/products') === 0) {
        handle_product_routes($method, $endpoint);
    } elseif (strpos($endpoint, '/api/cart') === 0) {
        handle_cart_routes($method, $endpoint);
    } elseif (strpos($endpoint, '/api/orders') === 0) {
        handle_order_routes($method, $endpoint);
    } elseif (strpos($endpoint, '/api/ai') === 0) {
        handle_ai_routes($method, $endpoint);
    } elseif (strpos($endpoint, '/api/uploads/') === 0) {
        $filepath = __DIR__ . str_replace('/api', '', $endpoint);
        if (file_exists($filepath)) {
            header('Content-Type: ' . mime_content_type($filepath));
            readfile($filepath);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'File not found']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}
?>
