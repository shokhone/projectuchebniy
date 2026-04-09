<?php
// includes/header.php - единый шаблон для всех страниц
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Электронная карта пациента' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f7ff; font-family: 'Segoe UI', system-ui; }
        .navbar-brand { font-weight: bold; }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .sidebar { min-height: calc(100vh - 56px); background: white; border-right: 1px solid #dee2e6; }
        .nav-link { color: #2c3e50; }
        .nav-link:hover { background: #e9ecef; }
        .nav-link.active { background: #0d6efd; color: white; }
        footer { background: white; border-top: 1px solid #dee2e6; margin-top: auto; }
        .wrapper { display: flex; flex-direction: column; min-height: 100vh; }
        .content { flex: 1; }
    </style>
</head>
<body>
<div class="wrapper">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid px-4">
            <span class="navbar-brand">
                <i class="fas fa-notes-medical me-2"></i>Электронная карта пациента
            </span>
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($_SESSION['username'] ?? 'Пользователь') ?>
                    <span class="badge bg-secondary ms-1"><?= $_SESSION['role'] ?? '' ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <!-- <li><a class="dropdown-item" href="../index.php"><i class="fas fa-home"></i> Главная</a></li> -->
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="?logout=1"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </nav>
    <div class="content">