<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header('Content-Type: application/json');
require_once __DIR__ . '/../service/authService.php';
$response = ['success' => false, 'message' => ''];

$payload = json_decode(file_get_contents('php://input'), true);
$email = $payload['email'] ?? '';
$password = $payload['password'] ?? '';
$name = $payload['name'] ?? '';
try {
    if ($email && $password && $name) {
        $service = new AuthService();
        $id = $service->register($email, $password, $name);
        $response['success'] = true;
        $response['user_id'] = $id;
        $response['message'] = 'Sucessfull';
        echo json_encode(["message" => "User registered successfully"]);

    } else {
        $response['message'] = 'Invalid input';
        echo json_encode(["message" => "User registered unsuccessfully"]);
    } 
     } 
catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    echo json_encode(["message" => $e->getMessage()]);
}

//echo json_encode($response);