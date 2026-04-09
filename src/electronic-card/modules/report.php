<?php
session_start();
require_once '../config/database.php';

$patientsCount = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$appointmentsCount = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$sickLeavesCount = $pdo->query("SELECT COUNT(*) FROM sick_leaves")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчётность</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-3">
    <a href="../index.php" class="btn btn-secondary mb-3">← На главную</a>
    <div class="card">
        <div class="card-header bg-primary text-white">📊 Статистика работы</div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item">👥 Всего пациентов: <strong><?= $patientsCount ?></strong></li>
                <li class="list-group-item">📅 Всего записей на приём: <strong><?= $appointmentsCount ?></strong></li>
                <li class="list-group-item">📄 Оформлено больничных: <strong><?= $sickLeavesCount ?></strong></li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>