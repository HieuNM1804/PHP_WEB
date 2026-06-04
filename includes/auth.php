<?php
/**
 * Auth Guard - Kiểm tra đăng nhập & phân quyền
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect(baseUrl('admin/login.php'));
    }
}

function requireAdmin(): void
{
    requireLogin();
    if ($_SESSION['role'] !== 'Admin') {
        $_SESSION['flash_error'] = 'Bạn không có quyền truy cập trang này.';
        redirect(baseUrl('admin/login.php'));
    }
}

function currentUser(): array
{
    return [
        'user_id'  => $_SESSION['user_id'] ?? 0,
        'username' => $_SESSION['username'] ?? '',
        'role'     => $_SESSION['role'] ?? '',
        'email'    => $_SESSION['email'] ?? '',
    ];
}

/**
 * CSRF Token
 */
function generateCSRFToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}
