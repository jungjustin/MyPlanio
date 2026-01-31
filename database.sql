-- MyPlanio Datenbank Schema
-- Entwickelt von: Justin Jung
-- Kontakt: justinjung@t-online.de

CREATE DATABASE IF NOT EXISTS myplanio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myplanio;

-- Benutzer Tabelle
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    is_approved BOOLEAN DEFAULT FALSE,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_approved (is_approved)
) ENGINE=InnoDB;

-- Gruppen Tabelle
CREATE TABLE groups_table (
    group_id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(100) NOT NULL,
    created_by INT NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#ffb3ba',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB;

-- Gruppenmitglieder Tabelle
CREATE TABLE group_members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups_table(group_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_membership (group_id, user_id),
    INDEX idx_group (group_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- Termine Tabelle
CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    location VARCHAR(200),
    color VARCHAR(7) DEFAULT '#ffb3ba',
    is_all_day BOOLEAN DEFAULT FALSE,
    created_by INT NOT NULL,
    group_id INT NULL,
    is_private BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups_table(group_id) ON DELETE CASCADE,
    INDEX idx_created_by (created_by),
    INDEX idx_group (group_id),
    INDEX idx_start (start_datetime),
    INDEX idx_end (end_datetime)
) ENGINE=InnoDB;

-- Markierte Personen bei Terminen
CREATE TABLE event_participants (
    participant_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_participant (event_id, user_id),
    INDEX idx_event (event_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- Standard Admin Benutzer erstellen (Passwort: admin123)
INSERT INTO users (username, password_hash, full_name, is_approved, is_admin) 
VALUES ('admin', '$2y$10$jiy/m.hHm8Xf0HWqu8Wm6OgyXS/n7j/tzMYU/og5qejfQstY3Nwym', 'Administrator', TRUE, TRUE);
