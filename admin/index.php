<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/layout.php';

header('Content-Type: text/html; charset=utf-8');

require_admin();
$user = current_user();

layout_start('Administración', $user);

$flashOk = flash_take('ok');
$flashErr = flash_take('err');
if ($flashOk) {
    echo '<p class="flash ok">' . h($flashOk) . '</p>';
}
if ($flashErr) {
    echo '<p class="flash err">' . h($flashErr) . '</p>';
}
?>
<p>Panel de administración. Conectado como <strong><?= h($user['email']) ?></strong>.</p>
<ul>
    <li><a href="/admin/products.php">Productos e inventario</a></li>
    <li>Pedidos — <em>próximamente</em></li>
</ul>
<p><a href="/index.php">Volver a la tienda</a></p>
<?php
layout_end();
