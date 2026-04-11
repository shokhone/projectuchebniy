-- tests/fixtures/test_data.sql
-- Очистка тестовых данных
DELETE FROM sick_leaves WHERE number LIKE 'TEST%';
DELETE FROM medical_records WHERE diagnosis LIKE 'TEST%';
DELETE FROM appointments WHERE purpose LIKE 'TEST%';
DELETE FROM patients WHERE policy_number LIKE 'TEST%';
DELETE FROM doctors WHERE specialty = 'TEST';

-- Добавление тестового врача
INSERT INTO doctors (full_name, specialty, cabinet) VALUES 
('Тестовый Врач', 'TEST', '999');

-- Добавление тестового пациента
INSERT INTO patients (full_name, birth_date, gender, phone, policy_number) VALUES 
('Тестовый Пациент', '2000-01-01', 'М', '+7(999)000-00-00', 'TEST_PATIENT_001');

-- Добавление тестовой записи
INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, purpose, status)
SELECT p.id, d.id, CURDATE(), '15:00:00', 'TEST_APPOINTMENT', 'scheduled'
FROM patients p, doctors d
WHERE p.policy_number = 'TEST_PATIENT_001' AND d.specialty = 'TEST';

-- Добавление тестовой медзаписи
INSERT INTO medical_records (patient_id, doctor_id, diagnosis, prescriptions)
SELECT p.id, d.id, 'TEST_DIAGNOSIS', 'TEST_PRESCRIPTION'
FROM patients p, doctors d
WHERE p.policy_number = 'TEST_PATIENT_001' AND d.specialty = 'TEST';

-- Добавление тестового больничного
INSERT INTO sick_leaves (patient_id, doctor_id, start_date, end_date, number, status)
SELECT p.id, d.id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'TEST_BL_001', 'active'
FROM patients p, doctors d
WHERE p.policy_number = 'TEST_PATIENT_001' AND d.specialty = 'TEST';