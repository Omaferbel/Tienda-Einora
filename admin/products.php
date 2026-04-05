<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/layout.php';

header('Content-Type: text/html; charset=utf-8');

require_admin();
$user = current_user();

$stmt = db()->query(
    'SELECT id, name, slug, price, stock_quantity, low_stock_threshold, is_active
     FROM products
     ORDER BY id DESC'
);
$products = $stmt->fetchAll();

layout_start('Productos', $user, 'admin-wide');

$flashOk = flash_take('ok');
$flashErr = flash_take('err');
if ($flashOk) {
    echo '<p class="flash ok">' . h($flashOk) . '</p>';
}
if ($flashErr) {
    echo '<p class="flash err">' . h($flashErr) . '</p>';
}
?>
<p><a class="btn secondary" href="/admin/index.php">← Panel</a>
   <a class="btn" href="/admin/product_form.php">Nuevo producto</a></p>

<?php if ($products === []): ?>
    <p>No hay productos todavía. Crea el primero.</p>
<?php else: ?>
    <table class="data">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <?php
            $threshold = $p['low_stock_threshold'] !== null ? (int) $p['low_stock_threshold'] : 5;
            $stock = (int) $p['stock_quantity'];
            $low = $stock <= $threshold;
            ?>
            <tr>
                <td>
                    <strong><?= h((string) $p['name']) ?></strong><br>
                    <small><code><?= h((string) $p['slug']) ?></code></small>
                </td>
                <td><?= h(number_format((float) $p['price'], 2, ',', '.')) ?> €</td>
                <td>
                    <?= h((string) $stock) ?>
                    <?php if ($low && (int) $p['is_active'] === 1): ?>
                        <span class="badge stock-low">bajo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ((int) $p['is_active'] === 1): ?>
                        <span class="badge on">Activo</span>
                    <?php else: ?>
                        <span class="badge off">Inactivo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="row-actions">
                        <a class="btn secondary" style="margin-top:0;padding:0.25rem 0.5rem;font-size:0.8rem;" href="/admin/product_form.php?id=<?= (int) $p['id'] ?>">Editar</a>
                        <?php if ((int) $p['is_active'] === 1): ?>
                            <form method="post" action="/admin/product_set_active.php">
                                <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                <input type="hidden" name="active" value="0">
                                <button type="submit" class="secondary">Desactivar</button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="/admin/product_set_active.php">
                                <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                <input type="hidden" name="active" value="1">
                                <button type="submit">Activar</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" action="/admin/product_remove.php" onsubmit="return confirm('¿Eliminar permanentemente? Solo si no hay pedidos con este producto.');">
                            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" class="secondary">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php
layout_end();
