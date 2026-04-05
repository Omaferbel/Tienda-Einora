<?php

declare(strict_types=1);

require_once __DIR__ . '/csrf.php';

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/**
 * @param array{id: int, email: string, name: string, role: string}|null $user
 */
function layout_start(string $title, ?array $user = null): void
{
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> — Einora</title>
    <style>
        :root { color-scheme: light; }
        body { font-family: system-ui, sans-serif; max-width: 42rem; margin: 0 auto; padding: 1.25rem; line-height: 1.45; }
        header { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e5e5; }
        header h1 { font-size: 1.25rem; margin: 0; flex: 1 1 auto; }
        nav { display: flex; flex-wrap: wrap; gap: 0.5rem 1rem; font-size: 0.95rem; }
        nav a { color: #2563eb; }
        .ok { color: #15803d; }
        .err { color: #b91c1c; }
        .flash { padding: 0.65rem 0.85rem; border-radius: 6px; margin-bottom: 1rem; }
        .flash.ok { background: #ecfdf5; color: #065f46; }
        .flash.err { background: #fef2f2; color: #991b1b; }
        code { background: #f4f4f5; padding: 0.12em 0.35em; border-radius: 4px; font-size: 0.9em; }
        label { display: block; font-weight: 600; margin-top: 0.85rem; }
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"] {
            width: 100%; max-width: 22rem; padding: 0.45rem 0.55rem; margin-top: 0.25rem; box-sizing: border-box;
        }
        button, .btn {
            display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background: #18181b; color: #fff;
            border: none; border-radius: 6px; cursor: pointer; font-size: 0.95rem; text-decoration: none;
        }
        button.secondary, a.secondary { background: #fff; color: #18181b; border: 1px solid #d4d4d8; }
        form.inline { display: inline; margin: 0; }
        form.inline button { margin-top: 0; margin-left: 0.35rem; padding: 0.25rem 0.5rem; font-size: 0.85rem; vertical-align: baseline; }
    </style>
</head>
<body>
<header>
    <h1><a href="/index.php" style="text-decoration:none;color:inherit;">Tienda Einora</a></h1>
    <nav>
        <a href="/index.php">Inicio</a>
        <?php if ($user): ?>
            <span>Hola, <?= h($user['name']) ?></span>
            <?php if ($user['role'] === 'admin'): ?>
                <a href="/admin/index.php">Administración</a>
            <?php endif; ?>
            <form class="inline" method="post" action="/logout.php">
                <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                <button type="submit" class="secondary">Cerrar sesión</button>
            </form>
        <?php else: ?>
            <a href="/login.php">Iniciar sesión</a>
            <a href="/register.php">Registrarse</a>
        <?php endif; ?>
    </nav>
</header>
    <?php
}

function layout_end(): void
{
    ?>
</body>
</html>
    <?php
}
