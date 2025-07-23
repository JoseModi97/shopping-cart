<?php
require_once __DIR__ . '/../utils/jwt.php';

function handle_order_routes($method, $endpoint) {
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

    if ($method === 'POST' && $endpoint === '/api/orders') {
        $stmt = $db->prepare('SELECT c.quantity, p.price FROM cart c JOIN products p ON c.variant_id = p.id WHERE c.user_id = :user_id');
        $stmt->bindValue(':user_id', $user_id);
        $result = $stmt->execute();

        $total = 0;
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $total += $row['price'] * $row['quantity'];
        }

        if ($total > 0) {
            $order_stmt = $db->prepare('INSERT INTO orders (user_id, order_date, total) VALUES (:user_id, :order_date, :total)');
            $order_stmt->bindValue(':user_id', $user_id);
            $order_stmt->bindValue(':order_date', date('Y-m-d H:i:s'));
            $order_stmt->bindValue(':total', $total);
            $order_stmt->execute();

            $order_id = $db->lastInsertRowID();

            $clear_cart_stmt = $db->prepare('DELETE FROM cart WHERE user_id = :user_id');
            $clear_cart_stmt->bindValue(':user_id', $user_id);
            $clear_cart_stmt->execute();

            header('Content-Type: application/json');
            http_response_code(201);
            echo json_encode(['message' => 'Order placed successfully!', 'orderId' => 'ORD-' . $order_id]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Cart is empty.']);
        }
    }
}
?>
