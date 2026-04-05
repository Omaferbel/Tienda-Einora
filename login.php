<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';

header('Content-Type: text/html; charset=utf-8');

$user = current_user();
if ($user) {
    $target = $user['role'] === 'admin' ? '/admin/index.php' : '/index.php';
    header('Location: ' . $target, true, 302);
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? null)) {
        $error = 'Sesión de formulario no válida. Vuelve a intentarlo.';
    } else {
        $email = (string) ($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        if (!auth_login($email, $password)) {
            $error = 'Correo o contraseña incorrectos.';
        } else {
            $u = current_user();
            $target = ($u && $u['role'] === 'admin') ? '/admin/index.php' : '/index.php';
            header('Location: ' . $target, true, 302);
            exit;
        }
    }
}

layout_start('Iniciar sesión', null);
if ($error) {
    echo '<p class="flash err">' . h($error) . '</p>';
}
?>
<form method="post" action="/login.php" autocomplete="on">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <label for="email">Correo</label>
    <input id="email" name="email" type="email" required maxlength="255" value="<?= h((string) ($_POST['email'] ?? '')) ?>">

    <label for="password">Contraseña</label>
    <input id="password" name="password" type="password" required autocomplete="current-password">

    <button type="submit">Entrar</button>
    <a class="btn secondary" href="/register.php" style="margin-left:0.5rem;">Crear cuenta</a>
</form>
<?php
layout_end();
