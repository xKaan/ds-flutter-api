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
('Jean', 'Dupont', 'jean@example.com', '$2y$12$BFhWq8GP11oOTqaKyFzBlOAP8qw6s5tuAJFnThFsXKbRliYb8l.8i', 2),
('Marie', 'Martin', 'marie@example.com', '$2y$12$RQOoy.OWkKkbZKKGsvn20OIQUE1DmnUM1nlBNXk1HxGr3y80m3zeC', 1);
