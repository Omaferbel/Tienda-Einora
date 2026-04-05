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
function layout_start(string $title, ?array $user = null, string $bodyClass = ''): void
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
        body { font-family: system-ui, sans-serif; max-width: 48rem; margin: 0 auto; padding: 1.25rem; line-height: 1.45; }
        body.admin-wide { max-width: 64rem; }
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
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"], input[type="number"], input[type="url"] {
            width: 100%; max-width: 22rem; padding: 0.45rem 0.55rem; margin-top: 0.25rem; box-sizing: border-box;
        }
        textarea {
            width: 100%; max-width: 32rem; min-height: 7rem; margin-top: 0.25rem; padding: 0.45rem 0.55rem;
            box-sizing: border-box; font-family: inherit; font-size: 0.95rem;
        }
        table.data { width: 100%; border-collapse: collapse; font-size: 0.9rem; margin-top: 1rem; }
        table.data th, table.data td { border: 1px solid #e5e5e7; padding: 0.45rem 0.55rem; text-align: left; vertical-align: top; }
        table.data th { background: #fafafa; font-weight: 600; }
        .badge { display: inline-block; padding: 0.15rem 0.45rem; border-radius: 4px; font-size: 0.8rem; }
        .badge.on { background: #dcfce7; color: #166534; }
        .badge.off { background: #f4f4f5; color: #52525b; }
        .badge.stock-low { background: #fef3c7; color: #92400e; }
        .row-actions { display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center; }
        .row-actions form { margin: 0; }
        .row-actions button { margin-top: 0; padding: 0.25rem 0.5rem; font-size: 0.8rem; }
        button, .btn {
            display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background: #18181b; color: #fff;
            border: none; border-radius: 6px; cursor: pointer; font-size: 0.95rem; text-decoration: none;
        }
        button.secondary, a.secondary { background: #fff; color: #18181b; border: 1px solid #d4d4d8; }
        form.inline { display: inline; margin: 0; }
        form.inline button { margin-top: 0; margin-left: 0.35rem; padding: 0.25rem 0.5rem; font-size: 0.85rem; vertical-align: baseline; }
    </style>
</head>
<body<?= $bodyClass !== '' ? ' class="' . h($bodyClass) . '"' : '' ?>>
<header>
    <h1><a href="/index.php" style="text-decoration:none;color:inherit;">Tienda Einora</a></h1>
    <nav>
        <a href="/index.php">Inicio</a>
        <?php if ($user): ?>
            <span>Hola, <?= h($user['name']) ?></span>
            <?php if ($user['role'] === 'admin'): ?>
                <a href="/admin/index.php">Administración</a>
                <a href="/admin/products.php">Productos</a>
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
