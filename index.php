<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';

header('Content-Type: text/html; charset=utf-8');

$user = current_user();

$dbOk = false;
$dbError = null;
$userCount = 0;
try {
    $stmt = db()->query('SELECT COUNT(*) AS c FROM users');
    $row = $stmt->fetch();
    $userCount = (int) ($row['c'] ?? 0);
    $dbOk = true;
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

layout_start('Inicio', $user);

$flashOk = flash_take('ok');
$flashErr = flash_take('err');
if ($flashOk) {
    echo '<p class="flash ok">' . h($flashOk) . '</p>';
}
if ($flashErr) {
    echo '<p class="flash err">' . h($flashErr) . '</p>';
}

if (!$dbOk) {
    echo '<p class="err">No se pudo conectar a la base de datos.</p>';
    echo '<p><small>' . h((string) $dbError) . '</small></p>';
    echo '<p>Comprueba <code>config.local.php</code> y que hayas importado <code>database/schema.sql</code>.</p>';
} else {
    echo '<p class="ok">Conexión a la base de datos correcta.</p>';
    if ($user && $user['role'] === 'admin') {
        echo '<p>Usuarios en <code>users</code>: <strong>' . h((string) $userCount) . '</strong></p>';
    }
    echo '<p>Próximamente: <strong>catálogo público</strong> y flujo de compra (contraentrega).</p>';
    echo '<p><small>Despliegue: <code>git push</code> → GitHub → Hostinger.</small></p>';
}

layout_end();
