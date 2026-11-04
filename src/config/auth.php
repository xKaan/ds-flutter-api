<?php

function getBearerToken(): ?string
{
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    $header = $headers['authorization'] ?? '';

    if (!preg_match('/^Bearer ([a-f0-9]{64})$/', $header, $matches)) {
        return null;
    }

    return $matches[1];
}

function requireUser(string $token): array
{
    try {
        $query = getDatabase()->prepare(
            'SELECT u.id, u.first_name, u.last_name, u.email, u.role
             FROM tokens t
             JOIN users u ON u.id = t.user_id
             WHERE t.token_hash = ? AND t.expires_at > NOW()'
        );
        $query->execute([hash('sha256', $token)]);
        $user = $query->fetch();
    } catch (PDOException $exception) {
        error_log($exception->getMessage());
        sendResponse(500);
    }

    if (!$user) {
        sendResponse(401);
    }

    return $user;
}
