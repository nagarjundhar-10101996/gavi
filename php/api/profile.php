<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header('Content-Type: application/json');
require_once __DIR__ . '/../service/authService.php';
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
function getAuthToken() {
    $headers = apache_request_headers();
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'authorization') {
            return str_replace('Bearer ', '', trim($value));
        }
    }
    return null;
}

$token = getAuthToken();
$service = new AuthService();
$payload = $service->validateToken($token);
if (!$payload) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}
// Fetch user data
$pdo = getMysqlPDO();
$userRepo = new UserRepository($pdo);
$user = $userRepo->getUserById($payload['user_id']);
echo json_encode(['success' => true, 'user' => $user],JSON_UNESCAPED_UNICODE);