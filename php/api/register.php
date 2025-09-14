<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../service/authService.php';

// Initialize response
$response = ['success' => false, 'message' => ''];

// Get JSON payload
$payload = json_decode(file_get_contents('php://input'), true);

$email = trim($payload['email'] ?? '');
$password = $payload['password'] ?? '';
$name = trim($payload['name'] ?? '');

// Validate inputs
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Invalid email';
    echo json_encode($response);
    exit;
}

if (!$name || !preg_match("/^[A-Za-z\s]{2,}$/", $name)) {
    $response['message'] = 'Invalid name';
    echo json_encode($response);
    exit;
}

if (!$password || !preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $password)) {
    $response['message'] = 'Password must be 8+ chars, include uppercase, lowercase, number & special char';
    echo json_encode($response);
    exit;
}

// Try registration
try {
    $service = new AuthService();
    $id = $service->register($email, $password, $name);

    $response['success'] = true;
    $response['user_id'] = $id;
    $response['message'] = 'User registered successfully';
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response, JSON_UNESCAPED_UNICODE);
