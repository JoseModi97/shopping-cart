<?php
require_once __DIR__ . '/../utils/jwt.php';

function handle_profile_routes($method, $endpoint) {
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

    if ($method === 'GET' && $endpoint === '/api/profile') {
        $stmt = $db->prepare('SELECT id, username, email, name, phone, address FROM users WHERE id = :id');
        $stmt->bindValue(':id', $user_id);
        $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($user);
    } elseif ($method === 'PUT' && $endpoint === '/api/profile') {
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare('UPDATE users SET name = :name, phone = :phone, address = :address WHERE id = :id');
        $stmt->bindValue(':name', json_encode($data['name']));
        $stmt->bindValue(':phone', $data['phone']);
        $stmt->bindValue(':address', json_encode($data['address']));
        $stmt->bindValue(':id', $user_id);
        $stmt->execute();

        $stmt = $db->prepare('SELECT id, username, email, name, phone, address FROM users WHERE id = :id');
        $stmt->bindValue(':id', $user_id);
        $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($user);
    }
}
?>
