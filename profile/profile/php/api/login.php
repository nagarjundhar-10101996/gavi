<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header('Content-Type: application/json');
require_once __DIR__ . '/../service/authService.php';


$payload = json_decode(file_get_contents('php://input'), true);
$email = $payload['email'] ?? '';
$password = $payload['password'] ?? '';

$service = new AuthService();
$result = $service->login($email, $password);

if ($result) {
    echo json_encode(['success' => true, 'token' => $result['token'], 'user' => $result['user']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}