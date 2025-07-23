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
    } elseif ($method === 'POST' && $endpoint === '/api/products') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $stock = $_POST['stock'];
        $image_url = null;

        if (isset($_FILES['image'])) {
            $target_dir = __DIR__ . '/../uploads/';
            $target_file = $target_dir . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = '/api/uploads/' . basename($_FILES['image']['name']);
            }
        }

        $stmt = $db->prepare('INSERT INTO products (name, description, price, category, stock, image_url) VALUES (:name, :description, :price, :category, :stock, :image_url)');
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
        $stmt->bindValue(':price', $price, SQLITE3_FLOAT);
        $stmt->bindValue(':category', $category, SQLITE3_TEXT);
        $stmt->bindValue(':stock', $stock, SQLITE3_INTEGER);
        $stmt->bindValue(':image_url', $image_url, SQLITE3_TEXT);

        if ($stmt->execute()) {
            $product_id = $db->lastInsertRowID();
            $product = [
                'id' => $product_id,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category' => $category,
                'stock' => $stock,
                'image_url' => $image_url
            ];
            header('Content-Type: application/json');
            http_response_code(201);
            echo json_encode($product);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create product']);
        }
    }
}
?>
