<?php

require __DIR__ . '/config/http.php';
require __DIR__ . '/config/db.php';

// Pour éviter d'attendre le temps de la vérification de mot de passe si l'utilisateur demandé n'existe pas en base
const DUMMY_HASH = '$2y$12$ts72dk1wGQMoYxc6HBQ3beueMdPWD6XMKMhZ5ghEpLRNtrn2OLrK2';

const TOKEN_LIFETIME_DAYS = 7;

function readRequestBody(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (str_contains($contentType, 'application/json')) {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }

    return $_POST;
}

requireMethod('POST');

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

// Token aléatoire renvoyé au client, seul son hash est stocké en base
$token = bin2hex(random_bytes(32));
$expiresAt = (new DateTimeImmutable('+' . TOKEN_LIFETIME_DAYS . ' days'))->format('Y-m-d H:i:s');

try {
    $tokenQuery = getDatabase()->prepare(
        'INSERT INTO tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
    );
    $tokenQuery->execute([$user['id'], hash('sha256', $token), $expiresAt]);
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    sendResponse(500);
}

sendResponse(200, [
    'id'        => $user['id'],
    'firstName' => $user['first_name'],
    'lastName'  => $user['last_name'],
    'email'     => $user['email'],
    'role'      => $user['role'],
    'token'     => $token,
]);
