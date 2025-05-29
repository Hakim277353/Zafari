CREATE TABLE IF NOT EXISTS ticket_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    visit_date DATE NOT NULL,
    time_slot VARCHAR(10) NOT NULL,
    adult_tickets INT DEFAULT 0,
    child_tickets INT DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
