<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

try {
    $pdo = require __DIR__ . '/includes/db.php';
    $stmt = $pdo->query('SELECT COUNT(*) AS c FROM users');
    $row = $stmt->fetch();
    $userCount = (int) ($row['c'] ?? 0);
    $ok = true;
} catch (Throwable $e) {
    $ok = false;
    $errorMessage = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Einora — Tienda</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 40rem; margin: 2rem auto; padding: 0 1rem; }
        .ok { color: #0a0; }
        .err { color: #c00; }
        code { background: #f4f4f4; padding: 0.15em 0.4em; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Tienda Einora</h1>
    <?php if ($ok): ?>
        <p class="ok">Conexión a la base de datos correcta.</p>
        <p>Usuarios en <code>users</code>: <strong><?= htmlspecialchars((string) $userCount, ENT_QUOTES, 'UTF-8') ?></strong></p>
        <p>Siguiente: catálogo público y panel admin (ver <code>AVANCES.md</code>).</p>
    <?php else: ?>
        <p class="err">No se pudo conectar a la base de datos.</p>
        <p><small><?= htmlspecialchars($errorMessage ?? '', ENT_QUOTES, 'UTF-8') ?></small></p>
        <p>Comprueba <code>config.local.php</code> y que hayas importado <code>database/schema.sql</code>.</p>
    <?php endif; ?>
</body>
</html>
