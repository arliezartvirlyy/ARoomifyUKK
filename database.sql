CREATE DATABASE IF NOT EXISTS roomify
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE roomify;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','peminjam') NOT NULL DEFAULT 'peminjam',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(150) NOT NULL,
    capacity INT NOT NULL DEFAULT 1,
    facilities TEXT NULL,
    description TEXT NULL,
    status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    purpose VARCHAR(200) NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    participants INT NOT NULL DEFAULT 1,
    status ENUM('menunggu','disetujui','ditolak','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
    admin_note VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_room FOREIGN KEY (room_id) REFERENCES rooms(id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_booking_schedule (room_id, booking_date, start_time, end_time),
    INDEX idx_booking_status (status)
) ENGINE=InnoDB;

INSERT INTO rooms (name, location, capacity, facilities, description, status)
SELECT 'Ruang Rapat A', 'Lantai 1', 20, 'Proyektor, Wi-Fi, Papan tulis', 'Ruang rapat untuk diskusi dan presentasi.', 'aktif'
WHERE NOT EXISTS (SELECT 1 FROM rooms WHERE name = 'Ruang Rapat A');

INSERT INTO rooms (name, location, capacity, facilities, description, status)
SELECT 'Aula Utama', 'Lantai 2', 100, 'Sound system, Proyektor, AC', 'Ruangan untuk acara dan pertemuan besar.', 'aktif'
WHERE NOT EXISTS (SELECT 1 FROM rooms WHERE name = 'Aula Utama');

INSERT INTO rooms (name, location, capacity, facilities, description, status)
SELECT 'Ruang Presentasi', 'Lantai 1', 30, 'TV display, Wi-Fi, AC', 'Ruangan untuk presentasi dan pelatihan.', 'aktif'
WHERE NOT EXISTS (SELECT 1 FROM rooms WHERE name = 'Ruang Presentasi');
