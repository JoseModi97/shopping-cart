<?php
function handle_product_routes($method, $endpoint) {
    $db = new SQLite3(__DIR__ . '/../db/electromart.db');

    if ($method === 'GET' && $endpoint === '/api/products') {
        $result = $db->query('SELECT * FROM products');
        $products = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $products[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($products);
    } elseif ($method === 'GET' && preg_match('/\/api\/products\/(\d+)/', $endpoint, $matches)) {
        $product_id = $matches[1];
        $stmt = $db->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
        $product = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($product) {
            header('Content-Type: application/json');
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
        }
    }
}
?>
