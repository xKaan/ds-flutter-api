<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function sendResponse(int $httpStatus, array $data = []): never
{
    http_response_code($httpStatus);
    echo json_encode([
        'status'  => $httpStatus,
        'message' => $httpStatus === 200 ? 'Success' : 'Error',
    ] + $data);
    exit;
}

function requireMethod(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        sendResponse(405);
    }
}
