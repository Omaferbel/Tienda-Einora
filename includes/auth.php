<?php

declare(strict_types=1);

require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/flash.php';

function current_user(): ?array
{
    $raw = $_SESSION['user_id'] ?? null;
    if ($raw === null || $raw === '') {
        return null;
    }
    $id = (int) $raw;
    if ($id < 1) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, email, name, phone, role FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function auth_login(string $email, string $password): bool
{
    $email = mb_strtolower(trim($email));
    if ($email === '' || $password === '') {
        return false;
    }

    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($password, $row['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $row['id'];

    return true;
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
    }
    session_destroy();
}

/** @return string|null null si el registro fue correcto */
function auth_register_customer(string $email, string $password, string $name, ?string $phone): ?string
{
    $email = mb_strtolower(trim($email));
    $name = trim($name);
    $phone = $phone !== null ? trim($phone) : null;
    if ($phone === '') {
        $phone = null;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Correo no válido.';
    }
    if (mb_strlen($name) < 2) {
        return 'Indica tu nombre (mínimo 2 caracteres).';
    }
    if (mb_strlen($password) < 8) {
        return 'La contraseña debe tener al menos 8 caracteres.';
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = db()->prepare(
            'INSERT INTO users (email, password_hash, name, phone, role) VALUES (?, ?, ?, ?, \'customer\')'
        );
        $stmt->execute([$email, $hash, $name, $phone]);
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate') || (string) $e->getCode() === '23000') {
            return 'Ese correo ya está registrado.';
        }
        throw $e;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) db()->lastInsertId();

    return null;
}

function require_login(): void
{
    if (!current_user()) {
        header('Location: /login.php', true, 302);
        exit;
    }
}

function require_admin(): void
{
    $u = current_user();
    if (!$u || $u['role'] !== 'admin') {
        header('Location: /login.php', true, 302);
        exit;
    }
}
