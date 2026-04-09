<?php
session_start();
if (!isset($_SESSION['user_id'])) header('Location: ../index.php');
require_once '../config/database.php';

$patients = $pdo->query("SELECT id, full_name FROM patients")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, doctor_id, diagnosis, prescriptions, recommendations) VALUES (?,?,?,?,?)");
    $stmt->execute([$_POST['patient_id'], 1, $_POST['diagnosis'], $_POST['prescriptions'], $_POST['recommendations']]);
    header('Location: medical_record.php');
    exit;
}

$records = $pdo->query("SELECT mr.*, p.full_name FROM medical_records mr JOIN patients p ON mr.patient_id = p.id ORDER BY mr.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Выписка и лечение</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-3">
    <a href="../index.php" class="btn btn-secondary mb-3">← На главную</a>
    <div class="card">
        <div class="card-header bg-primary text-white">🩺 Выписка / Назначение лечения</div>
        <div class="card-body">
            <form method="POST">
                <select name="patient_id" class="form-select mb-2" required><option value="">Пациент</option><?php foreach($patients as $p): ?><option value="<?=$p['id']?>"><?=htmlspecialchars($p['full_name'])?></option><?php endforeach; ?></select>
                <input type="text" name="diagnosis" class="form-control mb-2" placeholder="Диагноз (код МКБ)" required>
                <textarea name="prescriptions" class="form-control mb-2" placeholder="Назначения"></textarea>
                <textarea name="recommendations" class="form-control mb-2" placeholder="Рекомендации"></textarea>
                <button type="submit" class="btn btn-success">Сохранить</button>
            </form>
        </div>
    </div>
    <h4 class="mt-4">История выписок</h4>
    <?php foreach ($records as $r): ?>
    <div class="card mt-2"><div class="card-body"><strong><?=htmlspecialchars($r['full_name'])?></strong><br>Диагноз: <?=htmlspecialchars($r['diagnosis'])?><br>Назначения: <?=htmlspecialchars($r['prescriptions'])?><br><small><?=$r['created_at']?></small></div></div>
    <?php endforeach; ?>
</div>
</body>
</html>