<?php
function handle_ai_routes($method, $endpoint) {
    if ($method === 'POST' && $endpoint === '/api/ai/ask') {
        $data = json_decode(file_get_contents('php://input'), true);
        $product_id = $data['productId'];
        $question = $data['question'];

        $db = new SQLite3(__DIR__ . '/../db/electromart.db');
        $stmt = $db->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
        $product = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($product) {
            $prompt = "Question: {$question}\nProduct Details: " . json_encode($product);

            // In a real application, you would make a secure call to the Gemini API.
            // For this example, we'll simulate a response.
            $answer = "This is a simulated answer about " . $product['name'] . ". For detailed information, please consult the official documentation.";

            header('Content-Type: application/json');
            echo json_encode(['answer' => $answer]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
        }
    }
}
?>
