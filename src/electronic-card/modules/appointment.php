<?php
session_start();
if (!isset($_SESSION['user_id'])) header('Location: ../index.php');
require_once '../config/database.php';

$doctors = $pdo->query("SELECT * FROM doctors")->fetchAll();
$patients = $pdo->query("SELECT id, full_name FROM patients")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time) VALUES (?,?,?,?)");
    $stmt->execute([$_POST['patient_id'], $_POST['doctor_id'], $_POST['date'], $_POST['time']]);
    header('Location: appointment.php');
    exit;
}

$appointments = $pdo->query("
    SELECT a.*, p.full_name as patient, d.full_name as doctor 
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    ORDER BY a.appointment_date DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Запись на приём</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-3">
    <a href="../index.php" class="btn btn-secondary mb-3">← На главную</a>
    <div class="card">
        <div class="card-header bg-primary text-white">📅 Запись на приём</div>
        <div class="card-body">
            <form method="POST">
                <select name="patient_id" class="form-select mb-2" required><option value="">Пациент</option><?php foreach($patients as $p): ?><option value="<?=$p['id']?>"><?=htmlspecialchars($p['full_name'])?></option><?php endforeach; ?></select>
                <select name="doctor_id" class="form-select mb-2" required><option value="">Врач</option><?php foreach($doctors as $d): ?><option value="<?=$d['id']?>"><?=htmlspecialchars($d['full_name'])?> (<?=$d['specialty']?>)</option><?php endforeach; ?></select>
                <input type="date" name="date" class="form-control mb-2" required>
                <input type="time" name="time" class="form-control mb-2" required>
                <button type="submit" class="btn btn-success">Записать</button>
            </form>
        </div>
    </div>
    <h4 class="mt-4">Текущие записи</h4>
    <table class="table table-bordered">
        <tr><th>Пациент</th><th>Врач</th><th>Дата</th><th>Время</th><th>Статус</th></tr>
        <?php foreach ($appointments as $a): ?>
        <tr><td><?=htmlspecialchars($a['patient'])?></td><td><?=htmlspecialchars($a['doctor'])?></td><td><?=$a['appointment_date']?></td><td><?=$a['appointment_time']?></td><td><?=$a['status']?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>