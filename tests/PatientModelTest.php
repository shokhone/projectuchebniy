<?php
// tests/PatientModelTest.php
require_once __DIR__ . '/../config/database.php';

class PatientModelTest {
    private $pdo;
    private $passed = 0;
    private $failed = 0;
    private $testPatientId = null;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        echo "\n🧪 === ТЕСТЫ МОДУЛЯ ПАЦИЕНТОВ ===\n\n";
        
        $this->testCreatePatient();
        $this->testReadPatient();
        $this->testSearchPatient();
        $this->testUpdatePatient();
        $this->testDeletePatient();
        
        echo "\n📊 Результаты: ✅ {$this->passed} пройдено | ❌ {$this->failed} не пройдено\n";
        return $this->failed === 0;
    }
    
    private function testCreatePatient() {
        $sql = "INSERT INTO patients (full_name, birth_date, gender, phone, policy_number) 
                VALUES (:name, :date, :gender, :phone, :policy)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':name' => 'Тестов Тест Тестович',
            ':date' => '1990-01-01',
            ':gender' => 'М',
            ':phone' => '+7(999)111-22-33',
            ':policy' => 'TEST' . time()
        ]);
        
        if ($result) {
            $this->testPatientId = $this->pdo->lastInsertId();
            echo "✅ Создание пациента: успешно (ID: {$this->testPatientId})\n";
            $this->passed++;
        } else {
            echo "❌ Создание пациента: ошибка\n";
            $this->failed++;
        }
    }
    
    private function testReadPatient() {
        if (!$this->testPatientId) {
            echo "⚠️ Пропуск: нет ID для чтения\n";
            return;
        }
        
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$this->testPatientId]);
        $patient = $stmt->fetch();
        
        if ($patient && $patient['full_name'] == 'Тестов Тест Тестович') {
            echo "✅ Чтение пациента: успешно\n";
            $this->passed++;
        } else {
            echo "❌ Чтение пациента: данные не найдены\n";
            $this->failed++;
        }
    }
    
    private function testSearchPatient() {
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE full_name LIKE ?");
        $stmt->execute(['%Тестов%']);
        $results = $stmt->fetchAll();
        
        if (count($results) > 0) {
            echo "✅ Поиск пациента: найдено " . count($results) . " записей\n";
            $this->passed++;
        } else {
            echo "❌ Поиск пациента: ничего не найдено\n";
            $this->failed++;
        }
    }
    
    private function testUpdatePatient() {
        if (!$this->testPatientId) {
            echo "⚠️ Пропуск: нет ID для обновления\n";
            return;
        }
        
        $sql = "UPDATE patients SET phone = '+7(999)999-99-99' WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$this->testPatientId]);
        
        if ($result) {
            echo "✅ Обновление пациента: успешно\n";
            $this->passed++;
        } else {
            echo "❌ Обновление пациента: ошибка\n";
            $this->failed++;
        }
    }
    
    private function testDeletePatient() {
        if (!$this->testPatientId) {
            echo "⚠️ Пропуск: нет ID для удаления\n";
            return;
        }
        
        $stmt = $this->pdo->prepare("DELETE FROM patients WHERE id = ?");
        $result = $stmt->execute([$this->testPatientId]);
        
        if ($result) {
            echo "✅ Удаление пациента: успешно\n";
            $this->passed++;
        } else {
            echo "❌ Удаление пациента: ошибка\n";
            $this->failed++;
        }
    }
}
?>