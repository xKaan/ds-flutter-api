<?php

require __DIR__ . '/config/db.php';

// Pour éviter d'attendre le temps de la vérification de mot de passe si l'utilisateur demandé n'existe pas en base
const DUMMY_HASH = '$2y$12$ts72dk1wGQMoYxc6HBQ3beueMdPWD6XMKMhZ5ghEpLRNtrn2OLrK2';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

function sendResponse(int $httpStatus, array $data = []): never
{
    http_response_code($httpStatus);
    echo json_encode([
        'status'  => $httpStatus,
        'message' => $httpStatus === 200 ? 'Success' : 'Error',
    ] + $data);
    exit;
}

function readRequestBody(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (str_contains($contentType, 'application/json')) {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }

    return $_POST;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405);
}

$requestBody = readRequestBody();
$email = $requestBody['email'] ?? null;
$password = $requestBody['password'] ?? null;

if (!is_string($email) || !is_string($password)) {
    sendResponse(400);
}

$email = trim($email);

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || strlen($password) > 72) {
    sendResponse(400);
}

try {
    $userQuery = getDatabase()->prepare(
        'SELECT id, first_name, last_name, email, password_hash, role FROM users WHERE email = ?'
    );
    $userQuery->execute([$email]);
    $user = $userQuery->fetch();
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    sendResponse(500);
}

$passwordIsValid = password_verify($password, $user['password_hash'] ?? DUMMY_HASH);

if (!$user || !$passwordIsValid) {
    sendResponse(400);
}

sendResponse(200, [
    'id'        => $user['id'],
    'firstName' => $user['first_name'],
    'lastName'  => $user['last_name'],
    'email'     => $user['email'],
    'role'      => $user['role'],
]);
