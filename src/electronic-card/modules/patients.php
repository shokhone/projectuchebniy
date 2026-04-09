<?php
require_once '../includes/auth.php';
requireLogin();
$pageTitle = 'Пациенты';
include '../includes/header.php';
require_once '../config/database.php';

// Поиск
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE full_name LIKE ? OR policy_number LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%", "%$search%"]);
    $patients = $stmt->fetchAll();
} else {
    $patients = $pdo->query("SELECT * FROM patients ORDER BY id DESC")->fetchAll();
}

// Добавление (исправлено - убран address)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_patient'])) {
    $stmt = $pdo->prepare("INSERT INTO patients (full_name, birth_date, phone, policy_number, snils) VALUES (?,?,?,?,?)");
    $stmt->execute([
        $_POST['full_name'],
        $_POST['birth_date'],
        $_POST['phone'],
        $_POST['policy_number'],
        $_POST['snils'] ?? null
    ]);
    header('Location: patients.php');
    exit;
}

// Удаление
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: patients.php');
    exit;
}
?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <h2><i class="fas fa-users"></i> Пациенты</h2>
        <a href="../index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> На главную</a>
    </div>
    
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-user-plus"></i> Регистрация нового пациента
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-2">
                            <label class="form-label small">ФИО *</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Дата рождения *</label>
                                <input type="date" name="birth_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Телефон</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Номер полиса</label>
                                <input type="text" name="policy_number" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">СНИЛС</label>
                                <input type="text" name="snils" class="form-control">
                            </div>
                        </div>
                        <button type="submit" name="add_patient" class="btn btn-success w-100 mt-2">
                            <i class="fas fa-save"></i> Добавить пациента
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-list"></i> Список пациентов
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Поиск по ФИО или полису..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Найти</button>
                            <?php if ($search): ?>
                                <a href="patients.php" class="btn btn-secondary"><i class="fas fa-times"></i> Сброс</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>ФИО</th>
                                    <th>Дата рождения</th>
                                    <th>Телефон</th>
                                    <th>Полис</th>
                                    <th>СНИЛС</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($patients) > 0): ?>
                                    <?php foreach ($patients as $p): ?>
                                    <tr>
                                        <td><?= $p['id'] ?></td>
                                        <td><?= htmlspecialchars($p['full_name']) ?></td>
                                        <td><?= $p['birth_date'] ?></td>
                                        <td><?= $p['phone'] ?? '-' ?></td>
                                        <td><?= $p['policy_number'] ?? '-' ?></td>
                                        <td><?= $p['snils'] ?? '-' ?></td>
                                        <td>
                                            <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить пациента?')">
                                                <i class="fas fa-trash"></i> Удалить
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted">Нет данных</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>