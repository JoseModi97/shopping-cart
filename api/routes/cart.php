<?php
require_once __DIR__ . '/../utils/jwt.php';

function handle_cart_routes($method, $endpoint) {
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $jwt = $matches[1];
    $user_data = verify_jwt($jwt);

    if (!$user_data) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $db = new SQLite3(__DIR__ . '/../db/electromart.db');
    $user_id = $user_data['id'];

    if ($method === 'GET' && $endpoint === '/api/cart') {
        $stmt = $db->prepare('SELECT c.id, p.name as productName, c.quantity, p.price FROM cart c JOIN products p ON c.variant_id = p.id WHERE c.user_id = :user_id');
        $stmt->bindValue(':user_id', $user_id);
        $result = $stmt->execute();

        $items = [];
        $subtotal = 0;
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $items[] = $row;
            $subtotal += $row['price'] * $row['quantity'];
        }

        header('Content-Type: application/json');
        echo json_encode(['items' => $items, 'subtotal' => $subtotal]);
    } elseif ($method === 'POST' && $endpoint === '/api/cart') {
        $data = json_decode(file_get_contents('php://input'), true);
        $variant_id = $data['variantId'];
        $quantity = $data['quantity'];

        $stmt = $db->prepare('SELECT * FROM cart WHERE user_id = :user_id AND variant_id = :variant_id');
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':variant_id', $variant_id);
        $existing_item = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($existing_item) {
            $new_quantity = $existing_item['quantity'] + $quantity;
            $update_stmt = $db->prepare('UPDATE cart SET quantity = :quantity WHERE id = :id');
            $update_stmt->bindValue(':quantity', $new_quantity);
            $update_stmt->bindValue(':id', $existing_item['id']);
            $update_stmt->execute();
        } else {
            $insert_stmt = $db->prepare('INSERT INTO cart (user_id, variant_id, quantity) VALUES (:user_id, :variant_id, :quantity)');
            $insert_stmt->bindValue(':user_id', $user_id);
            $insert_stmt->bindValue(':variant_id', $variant_id);
            $insert_stmt->bindValue(':quantity', $quantity);
            $insert_stmt->execute();
        }

        handle_cart_routes('GET', '/api/cart');

    } elseif ($method === 'DELETE' && preg_match('/\/api\/cart\/(\d+)/', $endpoint, $matches)) {
        $item_id = $matches[1];
        $stmt = $db->prepare('DELETE FROM cart WHERE id = :id AND user_id = :user_id');
        $stmt->bindValue(':id', $item_id);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();

        http_response_code(204);
    }
}
?>
