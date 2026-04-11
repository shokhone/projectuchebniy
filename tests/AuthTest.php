<?php
// tests/AuthTest.php
require_once __DIR__ . '/../config/database.php';

class AuthTest {
    private $pdo;
    private $passed = 0;
    private $failed = 0;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        echo "\n🧪 === ТЕСТЫ АВТОРИЗАЦИИ ===\n\n";
        
        $this->testUserExists();
        $this->testValidLogin();
        $this->testInvalidLogin();
        $this->testRolesExist();
        
        echo "\n📊 Результаты: ✅ {$this->passed} пройдено | ❌ {$this->failed} не пройдено\n";
        return $this->failed === 0;
    }
    
    private function testUserExists() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "✅ Пользователи в БД: {$count} записей\n";
            $this->passed++;
        } else {
            echo "❌ Пользователи в БД: нет данных\n";
            $this->failed++;
        }
    }
    
    private function testValidLogin() {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? AND password = MD5(?)");
        $stmt->execute(['registrar', '123']);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ Вход с корректными данными: успешно (роль: {$user['role']})\n";
            $this->passed++;
        } else {
            echo "❌ Вход с корректными данными: не удалось\n";
            $this->failed++;
        }
    }
    
    private function testInvalidLogin() {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? AND password = MD5(?)");
        $stmt->execute(['wrong_user', 'wrong_pass']);
        $user = $stmt->fetch();
        
        if (!$user) {
            echo "✅ Вход с некорректными данными: доступ запрещён\n";
            $this->passed++;
        } else {
            echo "❌ Вход с некорректными данными: не должен работать\n";
            $this->failed++;
        }
    }
    
    private function testRolesExist() {
        $requiredRoles = ['registrar', 'doctor', 'admin'];
        $stmt = $this->pdo->query("SELECT DISTINCT role FROM users");
        $existingRoles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $missing = array_diff($requiredRoles, $existingRoles);
        
        if (empty($missing)) {
            echo "✅ Роли пользователей: все присутствуют (registrar, doctor, admin)\n";
            $this->passed++;
        } else {
            echo "❌ Роли пользователей: отсутствуют - " . implode(', ', $missing) . "\n";
            $this->failed++;
        }
    }
}
?>