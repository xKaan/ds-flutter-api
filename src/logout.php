<?php

require __DIR__ . '/config/http.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/config/auth.php';

requireMethod('POST');

$token = getBearerToken();

if ($token === null) {
    sendResponse(401);
}

try {
    $query = getDatabase()->prepare('DELETE FROM tokens WHERE token_hash = ?');
    $query->execute([hash('sha256', $token)]);
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    sendResponse(500);
}

sendResponse(200);
