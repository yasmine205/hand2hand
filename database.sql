-- Hand2Hand : création de la base de données
-- Import : phpMyAdmin > onglet "Importer" > choisir ce fichier

CREATE DATABASE IF NOT EXISTS hand2hand
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hand2hand;

CREATE TABLE IF NOT EXISTS users (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  first_name  VARCHAR(100) NOT NULL,
  last_name   VARCHAR(100) NOT NULL,
  email       VARCHAR(190) NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,
  phone       VARCHAR(30),
  city        VARCHAR(100),
  address     VARCHAR(255),
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS associations (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  assoc_name  VARCHAR(150) NOT NULL,
  reg_number  VARCHAR(100) NOT NULL,
  first_name  VARCHAR(100) NOT NULL,
  last_name   VARCHAR(100) NOT NULL,
  email       VARCHAR(190) NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,
  phone       VARCHAR(30),
  city        VARCHAR(100),
  address     VARCHAR(255),
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admins (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  username    VARCHAR(100) DEFAULT 'Administrator',
  email       VARCHAR(190) NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS items (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT NOT NULL,
  item_name       VARCHAR(150) NOT NULL,
  category        ENUM('Food','Clothes','Medical','Other') NOT NULL DEFAULT 'Other',
  quantity        VARCHAR(100) NOT NULL,
  item_condition  ENUM('New','Like New','Good','Fair') NOT NULL DEFAULT 'Good',
  item_image      VARCHAR(255) DEFAULT NULL,
  description     TEXT,
  contact_phone   VARCHAR(30),
  status          ENUM('pending','approved','refused','reserved') NOT NULL DEFAULT 'pending',
  assoc_id        INT DEFAULT NULL,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (assoc_id) REFERENCES associations(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS job_offers (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  job_title   VARCHAR(150) NOT NULL,
  company     VARCHAR(150) NOT NULL,
  location    VARCHAR(150) NOT NULL,
  description TEXT,
  status      ENUM('pending','approved','refused','reserved') NOT NULL DEFAULT 'pending',
  assoc_id    INT DEFAULT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (assoc_id) REFERENCES associations(id) ON DELETE SET NULL
) ENGINE=InnoDB;
