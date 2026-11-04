CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role TINYINT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES
('Jean', 'Dupont', 'jean@example.com', '$argon2id$v=19$m=65536,t=4,p=1$QUtmQjd5QWtXR3dxNm0wSw$FWRfJve60zek9GKKgRtCxsHXS/wuuxMooVqMwuxtD4c', 2),
('Marie', 'Martin', 'marie@example.com', '$argon2id$v=19$m=65536,t=4,p=1$UzE2NTFuZllpeWRUVWk3OQ$0mul/DzB4Z77Xe6ekC0HhcQgLObQDIwszToOgKxlBrc', 1);
