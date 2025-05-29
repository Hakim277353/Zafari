CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create default admin account (password: admin123)
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$GkFK45JJg9d4yFqqm9Wqj.BhfX7EmSPpjrY1gWjk2PXbRWqhbPK9m');
