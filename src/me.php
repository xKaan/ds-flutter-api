<?php

require __DIR__ . '/config/http.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/config/auth.php';

requireMethod('GET');

$token = getBearerToken();

if ($token === null) {
    sendResponse(401);
}

$user = requireUser($token);

sendResponse(200, [
    'id'        => $user['id'],
    'firstName' => $user['first_name'],
    'lastName'  => $user['last_name'],
    'email'     => $user['email'],
    'role'      => $user['role'],
]);
