<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';

header('Content-Type: text/html; charset=utf-8');

if (current_user()) {
    header('Location: /index.php', true, 302);
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? null)) {
        $error = 'Sesión de formulario no válida. Vuelve a intentarlo.';
    } else {
        $regError = auth_register_customer(
            (string) ($_POST['email'] ?? ''),
            (string) ($_POST['password'] ?? ''),
            (string) ($_POST['name'] ?? ''),
            isset($_POST['phone']) ? (string) $_POST['phone'] : null
        );
        if ($regError !== null) {
            $error = $regError;
        } else {
            flash_set('ok', 'Cuenta creada. Ya has iniciado sesión.');
            header('Location: /index.php', true, 302);
            exit;
        }
    }
}

layout_start('Registro', null);
if ($error) {
    echo '<p class="flash err">' . h($error) . '</p>';
}
?>
<p>Las cuentas nuevas son de tipo <strong>cliente</strong>. Los administradores se crean por otro medio.</p>
<form method="post" action="/register.php" autocomplete="on">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

    <label for="name">Nombre</label>
    <input id="name" name="name" type="text" required minlength="2" maxlength="120" value="<?= h((string) ($_POST['name'] ?? '')) ?>">

    <label for="email">Correo</label>
    <input id="email" name="email" type="email" required maxlength="255" value="<?= h((string) ($_POST['email'] ?? '')) ?>">

    <label for="phone">Teléfono (opcional)</label>
    <input id="phone" name="phone" type="tel" maxlength="40" value="<?= h((string) ($_POST['phone'] ?? '')) ?>">

    <label for="password">Contraseña (mín. 8 caracteres)</label>
    <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">

    <button type="submit">Registrarse</button>
    <a class="btn secondary" href="/login.php" style="margin-left:0.5rem;">Ya tengo cuenta</a>
</form>
<?php
layout_end();
