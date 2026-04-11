<?php
// tests/TestRunner.php
// Запуск: php tests/TestRunner.php

require_once __DIR__ . '/../config/database.php';

echo "
╔══════════════════════════════════════════════════════════╗
║     🏥 ЭЛЕКТРОННАЯ КАРТА ПАЦИЕНТА - ЗАПУСКТЕСТОВ        ║ 
╚══════════════════════════════════════════════════════════╝
";

// Подключаем тесты
require_once __DIR__ . '/DatabaseTest.php';
require_once __DIR__ . '/PatientModelTest.php';
require_once __DIR__ . '/AppointmentTest.php';
require_once __DIR__ . '/AuthTest.php';

$tests = [
    'DatabaseTest' => new DatabaseTest($pdo),
    'PatientModelTest' => new PatientModelTest($pdo),
    'AppointmentTest' => new AppointmentTest($pdo),
    'AuthTest' => new AuthTest($pdo)
];

$allPassed = true;
foreach ($tests as $name => $test) {
    $result = $test->run();
    if (!$result) $allPassed = false;
}

echo "\n════════════════════════════════════════════════════════════\n";
if ($allPassed) {
    echo "🎉 ВСЕ ТЕСТЫ ПРОЙДЕНЫ УСПЕШНО! 🎉\n";
} else {
    echo "⚠️ НЕКОТОРЫЕ ТЕСТЫ НЕ ПРОШЛИ. Проверьте ошибки выше.\n";
}
echo "════════════════════════════════════════════════════════════\n";
?>