<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../service/authService.php';

// Get JSON payload
$payload = json_decode(file_get_contents('php://input'), true);
$email = trim($payload['email'] ?? '');
$password = $payload['password'] ?? '';

// Initialize response
$response = ['success' => false, 'message' => ''];

try {
    // Basic validation
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email");
    }
    if (!$password) {
        throw new Exception("Password required");
    }

    $service = new AuthService();
    $result = $service->login($email, $password);

    if ($result) {
        $response['success'] = true;
        $response['token'] = $result['token'];
        $response['user'] = $result['user'];
    } else {
        $response['message'] = 'Invalid credentials';
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
