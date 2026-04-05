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
if ($productId < 1) {
    flash_set('err', 'Producto no válido.');
    header('Location: /admin/products.php', true, 302);
    exit;
}

$stmt = db()->prepare('SELECT COUNT(*) AS c FROM order_items WHERE product_id = ?');
$stmt->execute([$productId]);
$count = (int) ($stmt->fetch()['c'] ?? 0);
if ($count > 0) {
    flash_set('err', 'No se puede eliminar: hay líneas de pedido que usan este producto. Desactívalo en su lugar.');
    header('Location: /admin/products.php', true, 302);
    exit;
}

$del = db()->prepare('DELETE FROM products WHERE id = ?');
$del->execute([$productId]);
if ($del->rowCount() === 0) {
    flash_set('err', 'No se encontró el producto.');
} else {
    flash_set('ok', 'Producto eliminado.');
}

header('Location: /admin/products.php', true, 302);
exit;
