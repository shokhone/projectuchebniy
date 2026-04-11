<?php
// tests/DatabaseTest.php
require_once __DIR__ . '/../config/database.php';

class DatabaseTest {
    private $pdo;
    private $passed = 0;
    private $failed = 0;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        echo "\n🧪 === ТЕСТЫ ПОДКЛЮЧЕНИЯ К БД ===\n\n";
        
        $this->testConnection();
        $this->testTablesExist();
        $this->testTableStructure();
        
        echo "\n📊 Результаты: ✅ {$this->passed} пройдено | ❌ {$this->failed} не пройдено\n";
        return $this->failed === 0;
    }
    
    private function testConnection() {
        try {
            $this->pdo->query("SELECT 1");
            echo "✅ Подключение к БД: работает\n";
            $this->passed++;
            return true;
        } catch (PDOException $e) {
            echo "❌ Подключение к БД: ошибка - " . $e->getMessage() . "\n";
            $this->failed++;
            return false;
        }
    }
    
    private function testTablesExist() {
        $requiredTables = ['users', 'doctors', 'patients', 'appointments', 'medical_records', 'sick_leaves'];
        $stmt = $this->pdo->query("SHOW TABLES");
        $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($requiredTables as $table) {
            if (in_array($table, $existingTables)) {
                echo "✅ Таблица {$table}: существует\n";
                $this->passed++;
            } else {
                echo "❌ Таблица {$table}: не найдена\n";
                $this->failed++;
            }
        }
    }
    
    private function testTableStructure() {
        $tables = ['users', 'patients', 'doctors', 'appointments'];
        foreach ($tables as $table) {
            $stmt = $this->pdo->query("DESCRIBE `{$table}`");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "📋 Таблица {$table}: " . count($columns) . " колонок\n";
        }
        $this->passed++;
    }
}
?>