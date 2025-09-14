<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../service/authService.php';
require_once __DIR__ . '/../repo/userRepository.php';

use MongoDB\Client;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Content-Type: application/json");

// --- Get token from Authorization header ---
function getAuthToken() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        return str_replace('Bearer ', '', trim($headers['Authorization']));
    }
    return null;
}

$token = getAuthToken();
$service = new AuthService();
$payload = $service->validateToken($token);

if (!$payload) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// --- Fetch MySQL user repo ---
$pdo = getMysqlPDO();
$userRepo = new UserRepository($pdo);
$mysqlUser = $userRepo->getUserById($payload['user_id']);

// --- Get POST data (JSON + form-data support) ---
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);
if (!$data) {
    $data = $_POST;
}

// --- Sanitize / whitelist fields ---
$userData = [
    'name'    => $data['name']    ?? null,
    'email'   => isset($data['email']) ? strtolower(trim($data['email'])) : null,
    'age'     => $data['age']     ?? null,
    'dob'     => $data['dob']     ?? null,
    'contact' => $data['contact'] ?? null,
];

if (empty($userData['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

$userId = $payload['user_id'];

// --- Save into MySQL ---
try {
    $stmt = $pdo->prepare("
        UPDATE users 
        SET name = :name, email = :email, age = :age, dob = :dob, contact = :contact
        WHERE id = :id
    ");
    $stmt->execute([
        ':name'    => $userData['name'],
        ':email'   => $userData['email'],
        ':age'     => $userData['age'],
        ':dob'     => $userData['dob'],
        ':contact' => $userData['contact'],
        ':id'      => $userId,
    ]);

    // Fetch updated MySQL user
    $mysqlUser = $userRepo->getUserById($userId);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'MySQL update failed', 'error' => $e->getMessage()]);
    exit;
}

// --- Save into MongoDB ---
$client = new Client("mongodb://localhost:27017");
$collection = $client->testdb->users;

$updateResult = $collection->updateOne(
    ['email' => $userData['email']], // filter
    ['$set' => $userData],           // update
    ['upsert' => true]               // insert if not exists
);

$mongoUser = $collection->findOne(['email' => $userData['email']]);

// --- Response ---
echo json_encode([
    'success'    => true,
    'status'     => $updateResult->getUpsertedCount() ? 'inserted' : 'updated',
    'profile'    => [
        'mysql' => $mysqlUser,
        'mongo' => $mongoUser,
    ],
    'message'    => $updateResult->getUpsertedCount()
        ? 'Profile inserted into MongoDB and updated in MySQL'
        : 'Profile updated in both MySQL and MongoDB',
]);
