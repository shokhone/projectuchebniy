

-- Выбираем вашу базу данных
USE `sobirjonov`;

-- =====================================================
-- Удаляем старые таблицы (если есть)
-- =====================================================
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `sick_leaves`;
DROP TABLE IF EXISTS `medical_records`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `patients`;
DROP TABLE IF EXISTS `doctors`;
DROP TABLE IF EXISTS `reports`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- Таблица: users (пользователи системы)
-- =====================================================
CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('registrar', 'doctor', 'admin') NOT NULL DEFAULT 'registrar',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Таблица: doctors (врачи)
-- =====================================================
CREATE TABLE `doctors` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(150) NOT NULL,
    `specialty` VARCHAR(100) NOT NULL,
    `cabinet` VARCHAR(10) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Таблица: patients (пациенты)
-- =====================================================
CREATE TABLE `patients` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(150) NOT NULL,
    `birth_date` DATE NOT NULL,
    `gender` ENUM('М', 'Ж') NOT NULL DEFAULT 'М',
    `phone` VARCHAR(20) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `policy_number` VARCHAR(20) DEFAULT NULL,
    `snils` VARCHAR(14) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `policy_number` (`policy_number`),
    UNIQUE KEY `snils` (`snils`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Таблица: appointments (записи на приём)
-- =====================================================
CREATE TABLE `appointments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `patient_id` INT(11) NOT NULL,
    `doctor_id` INT(11) NOT NULL,
    `appointment_date` DATE NOT NULL,
    `appointment_time` TIME NOT NULL,
    `purpose` TEXT DEFAULT NULL,
    `status` ENUM('scheduled', 'completed', 'cancelled', 'no_show') NOT NULL DEFAULT 'scheduled',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_appointment` (`doctor_id`, `appointment_date`, `appointment_time`),
    KEY `patient_id` (`patient_id`),
    KEY `appointment_date` (`appointment_date`),
    CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Таблица: medical_records (медицинские записи / выписки)
-- =====================================================
CREATE TABLE `medical_records` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `patient_id` INT(11) NOT NULL,
    `doctor_id` INT(11) NOT NULL,
    `appointment_id` INT(11) DEFAULT NULL,
    `diagnosis` TEXT DEFAULT NULL,
    `prescriptions` TEXT DEFAULT NULL,
    `recommendations` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `patient_id` (`patient_id`),
    KEY `doctor_id` (`doctor_id`),
    CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    CONSTRAINT `medical_records_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
    CONSTRAINT `medical_records_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Таблица: sick_leaves (больничные листы)
-- =====================================================
CREATE TABLE `sick_leaves` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `patient_id` INT(11) NOT NULL,
    `doctor_id` INT(11) NOT NULL,
    `appointment_id` INT(11) DEFAULT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `number` VARCHAR(20) NOT NULL,
    `status` ENUM('active', 'closed', 'cancelled') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `number` (`number`),
    KEY `patient_id` (`patient_id`),
    KEY `doctor_id` (`doctor_id`),
    CONSTRAINT `sick_leaves_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    CONSTRAINT `sick_leaves_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
    CONSTRAINT `sick_leaves_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Таблица: reports (отчёты)
-- =====================================================
CREATE TABLE `reports` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `type` VARCHAR(50) NOT NULL,
    `period_start` DATE NOT NULL,
    `period_end` DATE NOT NULL,
    `file_path` VARCHAR(255) DEFAULT NULL,
    `created_by` INT(11) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `created_by` (`created_by`),
    CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Добавление тестовых данных
-- =====================================================

-- Врачи
INSERT INTO `doctors` (`full_name`, `specialty`, `cabinet`) VALUES
('Иванов Иван Иванович', 'Терапевт', '101'),
('Петрова Анна Сергеевна', 'Хирург', '202'),
('Сидоров Дмитрий Алексеевич', 'Окулист', '305');

-- Пользователи (пароль = '123' в MD5)
INSERT INTO `users` (`username`, `password`, `role`) VALUES
('registrar', MD5('123'), 'registrar'),
('doctor', MD5('123'), 'doctor'),
('admin', MD5('123'), 'admin');

-- Пациенты
INSERT INTO `patients` (`full_name`, `birth_date`, `gender`, `phone`, `address`, `policy_number`, `snils`) VALUES
('Тестов Тест Тестович', '1990-01-15', 'М', '+7(999)111-22-33', 'г. Москва, ул. Тестовая, д.1', '1234567890123456', '123-456-789-01'),
('Петрова Анна Ивановна', '1985-05-20', 'Ж', '+7(999)222-33-44', 'г. Москва, ул. Цветочная, д.5', '2345678901234567', '234-567-890-12'),
('Сидоров Петр Васильевич', '1978-11-03', 'М', '+7(999)333-44-55', 'г. Москва, ул. Лесная, д.10', '3456789012345678', '345-678-901-23');

-- Записи на приём
INSERT INTO `appointments` (`patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `purpose`, `status`) VALUES
(1, 1, CURDATE(), '10:00:00', 'Плановый осмотр', 'scheduled'),
(2, 2, CURDATE(), '11:30:00', 'Консультация', 'scheduled'),
(3, 3, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', 'Проверка зрения', 'scheduled');

-- Медицинские записи (выписки)
INSERT INTO `medical_records` (`patient_id`, `doctor_id`, `diagnosis`, `prescriptions`, `recommendations`) VALUES
(1, 1, 'J06.9 - Острая респираторная инфекция', 'Парацетамол 500 мг, 3 раза в день', 'Постельный режим, обильное питьё'),
(2, 2, 'S60.0 - Ушиб пальца кисти', 'Холодный компресс, покой', 'Обратиться при усилении боли');

-- Больничные листы
INSERT INTO `sick_leaves` (`patient_id`, `doctor_id`, `start_date`, `end_date`, `number`, `status`) VALUES
(1, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'BL20241201001', 'active'),
(2, 2, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'BL20241201002', 'active');

-- Проверка
SELECT '✅ Таблицы успешно созданы!' AS 'Статус';
SELECT COUNT(*) AS 'Количество врачей' FROM doctors;
SELECT COUNT(*) AS 'Количество пользователей' FROM users;
SELECT COUNT(*) AS 'Количество пациентов' FROM patients;