-- SQL SEEDER DATA PENGGUNA (USERS) RUMAH QURAN TEMI
-- Password Default Seluruh User: 123456 (Format Hash Bcrypt)

-- 1. Pastikan kolom ENUM role mendukung bendahara
ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','walikelas','piket','kantin','bendahara') NOT NULL DEFAULT 'walikelas';

-- 2. Insert / Update Data Pengguna
INSERT INTO `users` (`username`, `password`, `nama_lengkap`, `role`, `kelas_diampu`) VALUES
('nurhasanah', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Nurhasanah', 'kantin', NULL),
('nana_rusmana', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Nana Rusmana', 'bendahara', NULL),
('nabila_azzahra', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Nabila Azzahra', 'bendahara', 'MDTU'),
('yati_supriati', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Yati Supriati', 'admin', NULL),
('ditya_dwi', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Ditya Dwi Edy S.A', 'admin', NULL),
('sri_anjani', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Sri Anjani', 'walikelas', 'TKQ'),
('eli_rizkiana', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Eli Rizkiana', 'walikelas', 'TKQ'),
('fauziyah', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Fauziyah', 'walikelas', 'TKQ'),
('kanifah', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Kanifah', 'walikelas', 'TKQ'),
('wulan_sari', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Wulan Sari', 'walikelas', 'TKQ'),
('rantika', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Rantika', 'walikelas', 'TKQ'),
('nursaidah', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Nursaidah', 'walikelas', 'MDTU'),
('siti_amelia', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Siti Amelia Nurul Rahmah', 'walikelas', 'MDTU'),
('farha_hidayatullaela', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Farha Hidayatullaela', 'walikelas', 'MDTU'),
('widiyaningrum', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Widiyaningrum', 'walikelas', 'MDTU'),
('nurul_aeni', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Nurul Aeni', 'walikelas', 'SDIT'),
('ibnul_mubarok', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Ibnul Mubarok', 'walikelas', 'SDIT'),
('yulianti', '$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a', 'Yulianti', 'walikelas', 'SDIT')
ON DUPLICATE KEY UPDATE `nama_lengkap` = VALUES(`nama_lengkap`), `role` = VALUES(`role`), `kelas_diampu` = VALUES(`kelas_diampu`);
