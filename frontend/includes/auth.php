<?php
/**
 * Auth sistema — hardcoded users, session management
 */
session_start();

// Hardcoded users (password_hash bcrypt)
// admin/admin123, demo/demo123
define('USERS', [
    'admin' => '$2y$10$Fximk9WDjxAdp5wyGbUd1uWnuBO6.FELyBdu3L1q5qyTN4eAtyGKe',
    'demo'  => '$2y$10$XSC.HVrLLqPIqVZPJSesNec3xxkMEzxhPBuOU9gjBn1H2XWTbh7kK',
    'egopb' => '$2y$10$n7DV12HhuWlKZlXfxCehAOci4KKdansAM5sqZQhPplo452sHqekvi',
]);

function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

function currentUser(): ?string {
    return $_SESSION['user'] ?? null;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function doLogin(string $username, string $password): bool {
    $username = strtolower(trim($username));
    if (isset(USERS[$username]) && password_verify($password, USERS[$username])) {
        $_SESSION['user'] = $username;
        session_regenerate_id(true);
        return true;
    }
    return false;
}

function doLogout(): void {
    $_SESSION = [];
    session_destroy();
}
