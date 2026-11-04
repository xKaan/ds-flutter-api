<?php
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'status'  => 200,
    'message' => 'API en ligne',
]);
