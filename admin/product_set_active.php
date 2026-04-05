<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/csrf.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/flash.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate(isset($_POST['csrf']) ? (string) $_POST['csrf'] : null)) {
    flash_set('err', 'Solicitud no válida.');
    header('Location: /admin/products.php', true, 302);
    exit;
}

$productId = (int) ($_POST['product_id'] ?? 0);
$active = isset($_POST['active']) ? (int) $_POST['active'] : -1;
if ($productId < 1 || ($active !== 0 && $active !== 1)) {
    flash_set('err', 'Datos no válidos.');
    header('Location: /admin/products.php', true, 302);
    exit;
}

$stmt = db()->prepare('UPDATE products SET is_active = ? WHERE id = ?');
$stmt->execute([$active, $productId]);
if ($stmt->rowCount() === 0) {
    flash_set('err', 'No se encontró el producto.');
} else {
    flash_set('ok', $active === 1 ? 'Producto activado.' : 'Producto desactivado.');
}

header('Location: /admin/products.php', true, 302);
exit;
