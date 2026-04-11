<?php
// tests/AppointmentTest.php
require_once __DIR__ . '/../config/database.php';

class AppointmentTest {
    private $pdo;
    private $passed = 0;
    private $failed = 0;
    private $testPatientId = null;
    private $testDoctorId = null;
    private $testAppointmentId = null;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        echo "\n🧪 === ТЕСТЫ ЗАПИСИ НА ПРИЁМ ===\n\n";
        
        $this->prepareTestData();
        $this->testCreateAppointment();
        $this->testPreventDoubleBooking();
        $this->testCancelAppointment();
        $this->testGetPatientAppointments();
        $this->cleanup();
        
        echo "\n📊 Результаты: ✅ {$this->passed} пройдено | ❌ {$this->failed} не пройдено\n";
        return $this->failed === 0;
    }
    
    private function prepareTestData() {
        // Создаём тестового врача
        $sql = "INSERT INTO doctors (full_name, specialty, cabinet) VALUES ('Тестов Врач', 'Тест', '999')";
        $this->pdo->exec($sql);
        $this->testDoctorId = $this->pdo->lastInsertId();
        
        // Создаём тестового пациента
        $sql = "INSERT INTO patients (full_name, birth_date, gender, policy_number) 
                VALUES ('Тест Пациент', '1990-01-01', 'М', 'TEST_APT" . time() . "')";
        $this->pdo->exec($sql);
        $this->testPatientId = $this->pdo->lastInsertId();
        
        echo "📝 Тестовые данные созданы: врач ID={$this->testDoctorId}, пациент ID={$this->testPatientId}\n";
    }
    
    private function testCreateAppointment() {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status) 
                VALUES (?, ?, CURDATE(), '10:00:00', 'scheduled')";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$this->testPatientId, $this->testDoctorId]);
        
        if ($result) {
            $this->testAppointmentId = $this->pdo->lastInsertId();
            echo "✅ Создание записи: успешно (ID: {$this->testAppointmentId})\n";
            $this->passed++;
        } else {
            echo "❌ Создание записи: ошибка\n";
            $this->failed++;
        }
    }
    
    private function testPreventDoubleBooking() {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status) 
                VALUES (?, ?, CURDATE(), '10:00:00', 'scheduled')";
        $stmt = $this->pdo->prepare($sql);
        
        try {
            $stmt->execute([$this->testPatientId, $this->testDoctorId]);
            echo "❌ Защита от двойной записи: не сработала\n";
            $this->failed++;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "✅ Защита от двойной записи: работает\n";
                $this->passed++;
            } else {
                echo "❌ Защита от двойной записи: другая ошибка\n";
                $this->failed++;
            }
        }
    }
    
    private function testCancelAppointment() {
        if (!$this->testAppointmentId) {
            echo "⚠️ Пропуск: нет ID записи\n";
            return;
        }
        
        $sql = "UPDATE appointments SET status = 'cancelled' WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$this->testAppointmentId]);
        
        if ($result) {
            echo "✅ Отмена записи: успешно\n";
            $this->passed++;
        } else {
            echo "❌ Отмена записи: ошибка\n";
            $this->failed++;
        }
    }
    
    private function testGetPatientAppointments() {
        $sql = "SELECT * FROM appointments WHERE patient_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->testPatientId]);
        $appointments = $stmt->fetchAll();
        
        if (count($appointments) > 0) {
            echo "✅ Получение записей пациента: " . count($appointments) . " записей\n";
            $this->passed++;
        } else {
            echo "❌ Получение записей пациента: нет данных\n";
            $this->failed++;
        }
    }
    
    private function cleanup() {
        if ($this->testPatientId) {
            $this->pdo->prepare("DELETE FROM patients WHERE id = ?")->execute([$this->testPatientId]);
        }
        if ($this->testDoctorId) {
            $this->pdo->prepare("DELETE FROM doctors WHERE id = ?")->execute([$this->testDoctorId]);
        }
        echo "🧹 Тестовые данные удалены\n";
    }
}
?>