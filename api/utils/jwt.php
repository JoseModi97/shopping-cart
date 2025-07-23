<?php
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function generate_jwt($payload, $secret = 'secret') {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $header = base64url_encode($header);

    $payload = json_encode($payload);
    $payload = base64url_encode($payload);

    $signature = hash_hmac('sha256', "$header.$payload", $secret, true);
    $signature = base64url_encode($signature);

    return "$header.$payload.$signature";
}

function verify_jwt($jwt, $secret = 'secret') {
    list($header, $payload, $signature) = explode('.', $jwt);

    $decoded_signature = hash_hmac('sha256', "$header.$payload", $secret, true);
    $decoded_signature = base64url_encode($decoded_signature);

    if ($decoded_signature !== $signature) {
        return false;
    }

    return json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
}
?>
