-- Zenji Pharmacy Management System database
-- Import in phpMyAdmin (Import tab) or: mysql -u root < zenji_pharmacy.sql
DROP DATABASE IF EXISTS zenji_pharmacy;
CREATE DATABASE zenji_pharmacy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zenji_pharmacy;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE medicines (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  category_id INT NOT NULL,
  supplier VARCHAR(120) NOT NULL,
  quantity INT NOT NULL DEFAULT 0,
  reorder_level INT NOT NULL DEFAULT 10,
  unit_price DECIMAL(12,2) NOT NULL,
  expiry_date DATE NOT NULL,
  created_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(20),
  subject VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default administrator: admin@zenji.com / Admin@123  (change after first login)
INSERT INTO users (full_name, email, password_hash, role) VALUES
('System Admin', 'admin@zenji.com', '$2y$12$dBXT95ge7NqmmfN9vuXNJeEkTZCqRZFyvxL7VJ.eEUJ.MLfEb5A/O', 'admin');

INSERT INTO categories (name) VALUES ('Antibiotics'),('Pain Relief'),('Antimalarials'),('Vitamins & Supplements'),('Cough & Cold'),('First Aid');

INSERT INTO medicines (name, category_id, supplier, quantity, reorder_level, unit_price, expiry_date, created_by) VALUES
('Amoxicillin 500mg (caps)', 1, 'Quality Chemicals Ltd', 240, 50, 500, '2027-08-30', 1),
('Ciprofloxacin 500mg', 1, 'Abacus Pharma', 30, 40, 800, '2027-03-15', 1),
('Paracetamol 500mg', 2, 'Cipla Quality Chemicals', 900, 100, 100, '2028-01-10', 1),
('Ibuprofen 400mg', 2, 'Abacus Pharma', 15, 30, 200, '2026-12-20', 1),
('Coartem 20/120mg', 3, 'Novartis Uganda', 120, 40, 7000, '2027-05-01', 1),
('Quinine 300mg', 3, 'Quality Chemicals Ltd', 60, 20, 300, '2026-12-05', 1),
('Vitamin C 1000mg', 4, 'Kampala Pharmaceutical Ind.', 300, 50, 250, '2027-11-11', 1),
('Multivitamin Syrup 200ml', 4, 'Rene Industries', 8, 15, 9000, '2027-02-28', 1),
('Cough Syrup 100ml', 5, 'Rene Industries', 45, 20, 6000, '2027-06-30', 1),
('Bandages (roll)', 6, 'Medipharm Supplies', 150, 30, 1500, '2029-01-01', 1);

INSERT INTO messages (name, email, phone, subject, message) VALUES
('Sarah Namata', 'sarah@example.com', '+256700111222', 'Delivery request', 'Do you deliver to Ntinda? I need my monthly prescription.');
