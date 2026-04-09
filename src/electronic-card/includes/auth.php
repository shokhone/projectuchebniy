<?php
// includes/auth.php - проверка авторизации и прав доступа
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../index.php');
        exit;
    }
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        die('<div class="alert alert-danger m-3">⛔ Доступ запрещён. Требуется роль: ' . htmlspecialchars($role) . '</div>');
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}
?>