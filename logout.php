<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php', true, 302);
    exit;
}

if (!csrf_validate($_POST['csrf'] ?? null)) {
    header('Location: /index.php', true, 302);
    exit;
}

auth_logout();
header('Location: /index.php', true, 302);
exit;
