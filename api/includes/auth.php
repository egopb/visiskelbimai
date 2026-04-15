<?php
/**
 * Auth sistema — cookie-based (works on Vercel serverless + local)
 */

define('AUTH_SECRET', 'visiskelbimai-2026-secret-key-xK9m');
define('AUTH_COOKIE', 'vs_auth');
define('AUTH_EXPIRE', 86400 * 7); // 7 days

// Hardcoded users (password_hash bcrypt)
define('USERS', [
    'admin' => '$2y$10$Fximk9WDjxAdp5wyGbUd1uWnuBO6.FELyBdu3L1q5qyTN4eAtyGKe',
    'demo'  => '$2y$10$XSC.HVrLLqPIqVZPJSesNec3xxkMEzxhPBuOU9gjBn1H2XWTbh7kK',
    'egopb' => '$2y$10$n7DV12HhuWlKZlXfxCehAOci4KKdansAM5sqZQhPplo452sHqekvi',
]);

function signToken(string $user, int $exp): string {
    $payload = "$user|$exp";
    $sig = hash_hmac('sha256', $payload, AUTH_SECRET);
    return base64_encode("$payload|$sig");
}

function verifyToken(string $token): ?string {
    $raw = base64_decode($token, true);
    if (!$raw) return null;
    $parts = explode('|', $raw);
    if (count($parts) !== 3) return null;
    [$user, $exp, $sig] = $parts;
    if ((int)$exp < time()) return null;
    $expected = hash_hmac('sha256', "$user|$exp", AUTH_SECRET);
    if (!hash_equals($expected, $sig)) return null;
    return $user;
}

function isLoggedIn(): bool {
    return currentUser() !== null;
}

function currentUser(): ?string {
    $token = $_COOKIE[AUTH_COOKIE] ?? '';
    if ($token === '') return null;
    return verifyToken($token);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
}

function doLogin(string $username, string $password): bool {
    $username = strtolower(trim($username));
    if (isset(USERS[$username]) && password_verify($password, USERS[$username])) {
        $token = signToken($username, time() + AUTH_EXPIRE);
        setcookie(AUTH_COOKIE, $token, [
            'expires' => time() + AUTH_EXPIRE,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => isset($_SERVER['HTTPS']),
        ]);
        return true;
    }
    return false;
}

function doLogout(): void {
    setcookie(AUTH_COOKIE, '', ['expires' => 1, 'path' => '/']);
}
