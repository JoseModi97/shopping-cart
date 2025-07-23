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
    } elseif ($method === 'POST' && $endpoint === '/api/profile') {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $image_url = null;

        if (isset($_FILES['image'])) {
            $target_dir = __DIR__ . '/../uploads/';
            $target_file = $target_dir . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = '/api/uploads/' . basename($_FILES['image']['name']);
            }
        }

        if ($image_url) {
            $stmt = $db->prepare('UPDATE users SET name = :name, phone = :phone, address = :address, image_url = :image_url WHERE id = :id');
            $stmt->bindValue(':image_url', $image_url, SQLITE3_TEXT);
        } else {
            $stmt = $db->prepare('UPDATE users SET name = :name, phone = :phone, address = :address WHERE id = :id');
        }

        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $stmt->bindValue(':address', $address, SQLITE3_TEXT);
        $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();

        $stmt = $db->prepare('SELECT id, username, email, name, phone, address, image_url FROM users WHERE id = :id');
        $stmt->bindValue(':id', $user_id);
        $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($user);
    }
}
?>
