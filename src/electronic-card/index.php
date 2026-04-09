<?php
session_start();
require_once 'config/database.php';

// Выход
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Обработка входа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный логин или пароль";
    }
}

// Если авторизован — показываем дашборд
if (isset($_SESSION['user_id'])):
    $role = $_SESSION['role'];
    $pageTitle = 'Главная';
    include 'includes/header.php';
?>
<div class="container-fluid px-4">
    <div class="row g-4 py-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Добро пожаловать, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!
                Ваша роль: <span class="badge bg-primary"><?= $role ?></span>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <?php if ($role == 'registrar' || $role == 'admin'): ?>
        <div class="col-md-3">
            <div class="card text-center card-hover shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Ввод карточки</h5>
                    <p class="card-text text-muted small">Регистрация новых пациентов</p>
                    <a href="modules/patients.php" class="btn btn-primary btn-sm">Перейти →</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center card-hover shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-calendar-plus fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Запись на приём</h5>
                    <p class="card-text text-muted small">Запись пациентов к врачам</p>
                    <a href="modules/appointment.php" class="btn btn-primary btn-sm">Перейти →</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($role == 'doctor' || $role == 'admin'): ?>
        <div class="col-md-3">
            <div class="card text-center card-hover shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-stethoscope fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Выписка</h5>
                    <p class="card-text text-muted small">Диагнозы, назначения, лечение</p>
                    <a href="modules/medical_record.php" class="btn btn-primary btn-sm">Перейти →</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center card-hover shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-file-medical fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Больничный</h5>
                    <p class="card-text text-muted small">Оформление листков нетрудоспособности</p>
                    <a href="modules/sick_leave.php" class="btn btn-primary btn-sm">Перейти →</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="col-md-3">
            <div class="card text-center card-hover shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Отчётность</h5>
                    <p class="card-text text-muted small">Статистика и аналитика</p>
                    <a href="modules/report.php" class="btn btn-primary btn-sm">Перейти →</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    include 'includes/footer.php';
    exit;
endif;
?>

<!-- Форма входа для неавторизованных -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход - Электронная карта пациента</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-card { border-radius: 20px; overflow: hidden; }
        .login-header { background: #2c3e50; color: white; padding: 30px; text-align: center; }
        .login-body { padding: 30px; }
    </style>
</head>
<body>
<div class="container" style="max-width: 450px;">
    <div class="card login-card shadow-lg">
        <div class="login-header">
            <i class="fas fa-notes-medical fa-3x mb-2"></i>
            <h4 class="mb-0">Электронная карта пациента</h4>
            <small>Вход в систему</small>
        </div>
        <div class="login-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user"></i> Логин</label>
                    <input type="text" name="username" class="form-control" placeholder="Введите логин" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-lock"></i> Пароль</label>
                    <input type="password" name="password" class="form-control" placeholder="Введите пароль" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 py-2">Войти</button>
            </form>
            <hr>
            <div class="text-center">
                <small class="text-muted">
                    <!-- <i class="fas fa-info-circle"></i> Тестовые данные:<br>
                    registrar / 123 | doctor / 123 | admin / 123 -->
                </small>
            </div>
        </div>
    </div>
</div>
</body>
</html>