-- Add Tickets Table for Reservations
CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  vehicle_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  number_of_days INT NOT NULL,
  status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
