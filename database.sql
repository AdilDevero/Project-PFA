-- Car rental agency database schema (MySQL)
-- Create a database first, then import this file

CREATE DATABASE IF NOT EXISTS rental_agency CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';
USE rental_agency;

-- Offices / Locations
CREATE TABLE offices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  address VARCHAR(300),
  phone VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Customers
CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) UNIQUE,
  phone VARCHAR(50),
  address VARCHAR(300),
  license_number VARCHAR(100),
  license_expiry DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Brands (manufacturers)
CREATE TABLE brands (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Categories (Economy, Compact, SUV, Luxury...)
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(500)
) ENGINE=InnoDB;

-- Vehicles
CREATE TABLE vehicles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand_id INT NOT NULL,
  model VARCHAR(150) NOT NULL,
  category_id INT NOT NULL,
  year SMALLINT,
  plate_number VARCHAR(50) UNIQUE,
  vin VARCHAR(50) UNIQUE,
  color VARCHAR(50),
  seats TINYINT DEFAULT 4,
  transmission ENUM('manual','automatic') DEFAULT 'automatic',
  fuel_type ENUM('petrol','diesel','hybrid','electric') DEFAULT 'petrol',
  daily_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  per_km_rate DECIMAL(10,2) DEFAULT 0.00,
  status ENUM('available','reserved','rented','maintenance','unavailable') DEFAULT 'available',
  mileage INT DEFAULT 0,
  quantity INT DEFAULT 1,
  office_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (office_id) REFERENCES offices(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Vehicle images
CREATE TABLE vehicle_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vehicle_id INT NOT NULL,
  url VARCHAR(500) NOT NULL,
  caption VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Rentals / Reservations
CREATE TABLE rentals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  vehicle_id INT NOT NULL,
  office_pickup_id INT,
  office_return_id INT,
  start_date DATETIME NOT NULL,
  end_date DATETIME NOT NULL,
  start_odometer INT,
  end_odometer INT,
  daily_rate DECIMAL(10,2) NOT NULL,
  total_amount DECIMAL(12,2) DEFAULT 0.00,
  status ENUM('reserved','active','completed','cancelled') DEFAULT 'reserved',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT,
  FOREIGN KEY (office_pickup_id) REFERENCES offices(id) ON DELETE SET NULL,
  FOREIGN KEY (office_return_id) REFERENCES offices(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Payments
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rental_id INT NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  payment_method ENUM('cash','card','transfer','online') DEFAULT 'card',
  status ENUM('pending','paid','failed','refunded') DEFAULT 'paid',
  paid_at DATETIME,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rental_id) REFERENCES rentals(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Maintenance / Service records
CREATE TABLE maintenance_records (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vehicle_id INT NOT NULL,
  service_date DATE NOT NULL,
  description TEXT,
  cost DECIMAL(10,2) DEFAULT 0.00,
  is_completed BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Indexes to help queries
CREATE INDEX idx_vehicle_status ON vehicles(status);
CREATE INDEX idx_rentals_status ON rentals(status);

-- Sample seed data (minimal)
INSERT INTO offices (name, address, phone) VALUES
('Siège - Centre','123 Rue Principale, Ville','+33 1 23 45 67 89');

INSERT INTO categories (name, description) VALUES
('Economy','Small, fuel-efficient cars'),
('Compact','Compact cars'),
('SUV','Sport Utility Vehicles'),
('Luxury','High-end luxury vehicles');

INSERT INTO brands (name) VALUES ('Toyota'),('Renault'),('BMW'),('Tesla');

INSERT INTO customers (first_name,last_name,email,phone,license_number) VALUES
('Jean','Dupont','jean.dupont@example.com','+33 6 12 34 56 78','FRA12345678');

INSERT INTO vehicles (brand_id,model,category_id,year,plate_number,vin,color,seats,transmission,fuel_type,daily_rate,per_km_rate,status,office_id,mileage)
VALUES
((SELECT id FROM brands WHERE name='Renault'),'Clio',(SELECT id FROM categories WHERE name='Economy'),2019,'AA-123-BB','VF1CLIO1234567890','Blanc',5,'manual','petrol',25.00,0.10,'available',1,45000);

-- End of schema
