<?php
session_start();
if (!isset($_SESSION['user_id'])) header('Location: ../index.php');
require_once '../config/database.php';

$patients = $pdo->query("SELECT id, full_name FROM patients")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = 'BL' . date('Ymd') . rand(100,999);
    $stmt = $pdo->prepare("INSERT INTO sick_leaves (patient_id, doctor_id, start_date, end_date, number) VALUES (?,?,?,?,?)");
    $stmt->execute([$_POST['patient_id'], 1, $_POST['start_date'], $_POST['end_date'], $number]);
    header('Location: sick_leave.php');
    exit;
}

$leaves = $pdo->query("SELECT sl.*, p.full_name FROM sick_leaves sl JOIN patients p ON sl.patient_id = p.id ORDER BY sl.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Больничный лист</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-3">
    <a href="../index.php" class="btn btn-secondary mb-3">← На главную</a>
    <div class="card">
        <div class="card-header bg-primary text-white">📄 Оформление больничного</div>
        <div class="card-body">
            <form method="POST">
                <select name="patient_id" class="form-select mb-2" required><option value="">Пациент</option><?php foreach($patients as $p): ?><option value="<?=$p['id']?>"><?=htmlspecialchars($p['full_name'])?></option><?php endforeach; ?></select>
                <input type="date" name="start_date" class="form-control mb-2" required>
                <input type="date" name="end_date" class="form-control mb-2" required>
                <button type="submit" class="btn btn-success">Выдать больничный</button>
            </form>
        </div>
    </div>
    <h4 class="mt-4">Выданные больничные</h4>
    <table class="table table-bordered"><tr><th>Пациент</th><th>Номер</th><th>Период</th><th>Статус</th></tr>
    <?php foreach ($leaves as $l): ?>
    <tr><td><?=htmlspecialchars($l['full_name'])?></td><td><?=$l['number']?></td><td><?=$l['start_date']?> → <?=$l['end_date']?></td><td><?=$l['status']?></td></tr>
    <?php endforeach; ?>
    </table>
</div>
</body>
</html>