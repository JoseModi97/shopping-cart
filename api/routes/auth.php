<?php
require_once __DIR__ . '/../utils/jwt.php';

function handle_auth_routes($method, $endpoint) {
    if ($method === 'POST' && $endpoint === '/api/auth/register') {
        $data = json_decode(file_get_contents('php://input'), true);
        $db = new SQLite3(__DIR__ . '/../db/electromart.db');
        $stmt = $db->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
        $stmt->bindValue(':username', $data['username']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $result = $stmt->execute();

        if ($result) {
            $user_id = $db->lastInsertRowID();
            $user_stmt = $db->prepare('SELECT id, username, email FROM users WHERE id = :id');
            $user_stmt->bindValue(':id', $user_id);
            $user_result = $user_stmt->execute()->fetchArray(SQLITE3_ASSOC);

            header('Content-Type: application/json');
            http_response_code(201);
            echo json_encode(['message' => 'User registered successfully.', 'user' => $user_result]);
        } else {
            header('Content-Type: application/json');
            http_response_code(409);
            echo json_encode(['error' => 'Username or email already exists.']);
        }
    } elseif ($method === 'POST' && $endpoint === '/api/auth/login') {
        $data = json_decode(file_get_contents('php://input'), true);
        $db = new SQLite3(__DIR__ . '/../db/electromart.db');
        $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->bindValue(':username', $data['username']);
        $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($user && password_verify($data['password'], $user['password'])) {
            $token = generate_jwt(['id' => $user['id'], 'username' => $user['username']]);
            unset($user['password']);
            header('Content-Type: application/json');
            echo json_encode(['token' => $token, 'user' => $user]);
        } else {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password.']);
        }
    }
}
?>
