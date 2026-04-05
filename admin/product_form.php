<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/util.php';

header('Content-Type: text/html; charset=utf-8');

require_admin();
$user = current_user();

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$row = null;
if ($productId > 0) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$productId]);
    $row = $stmt->fetch();
    if (!$row) {
        flash_set('err', 'Producto no encontrado.');
        header('Location: /admin/products.php', true, 302);
        exit;
    }
}

$errors = [];
$form = [
    'name' => $row ? (string) $row['name'] : '',
    'slug' => $row ? (string) $row['slug'] : '',
    'description' => $row && $row['description'] !== null ? (string) $row['description'] : '',
    'price' => $row ? (string) $row['price'] : '',
    'stock_quantity' => $row ? (string) (int) $row['stock_quantity'] : '0',
    'low_stock_threshold' => $row && $row['low_stock_threshold'] !== null ? (string) (int) $row['low_stock_threshold'] : '',
    'image_path' => $row && $row['image_path'] !== null ? (string) $row['image_path'] : '',
    'is_active' => !$row || (int) $row['is_active'] === 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate(isset($_POST['csrf']) ? (string) $_POST['csrf'] : null)) {
        $errors[] = 'Sesión de formulario no válida. Vuelve a intentarlo.';
    } else {
        $postId = (int) ($_POST['product_id'] ?? 0);
        if ($postId !== $productId) {
            $errors[] = 'Datos incoherentes.';
        } else {
            $form['name'] = trim((string) ($_POST['name'] ?? ''));
            $form['slug'] = trim((string) ($_POST['slug'] ?? ''));
            $form['description'] = trim((string) ($_POST['description'] ?? ''));
            $form['price'] = trim((string) ($_POST['price'] ?? ''));
            $form['stock_quantity'] = trim((string) ($_POST['stock_quantity'] ?? '0'));
            $form['low_stock_threshold'] = trim((string) ($_POST['low_stock_threshold'] ?? ''));
            $form['image_path'] = trim((string) ($_POST['image_path'] ?? ''));
            $form['is_active'] = isset($_POST['is_active']);

            if (mb_strlen($form['name']) < 1) {
                $errors[] = 'El nombre es obligatorio.';
            }

            $priceVal = parse_money($form['price']);
            if ($priceVal === null) {
                $errors[] = 'Indica un precio válido (ej. 12,50).';
            }

            $stockVal = filter_var($form['stock_quantity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
            if ($stockVal === false) {
                $errors[] = 'El stock debe ser un número entero ≥ 0.';
            }

            $lowVal = null;
            if ($form['low_stock_threshold'] !== '') {
                $lowParsed = filter_var($form['low_stock_threshold'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
                if ($lowParsed === false) {
                    $errors[] = 'Umbral de stock bajo: entero ≥ 0 o déjalo vacío.';
                } else {
                    $lowVal = $lowParsed;
                }
            }

            $descVal = $form['description'] !== '' ? $form['description'] : null;
            $imgVal = $form['image_path'] !== '' ? $form['image_path'] : null;
            $isActive = $form['is_active'] ? 1 : 0;

            if ($errors === [] && $stockVal !== false) {
                $slugBase = $form['slug'] !== '' ? $form['slug'] : $form['name'];
                $slug = unique_product_slug(db(), $slugBase, $productId > 0 ? $productId : null);

                if ($productId > 0) {
                    $up = db()->prepare(
                        'UPDATE products SET name = ?, slug = ?, description = ?, price = ?, image_path = ?,
                         stock_quantity = ?, low_stock_threshold = ?, is_active = ?
                         WHERE id = ?'
                    );
                    $up->execute([
                        $form['name'],
                        $slug,
                        $descVal,
                        $priceVal,
                        $imgVal,
                        (int) $stockVal,
                        $lowVal,
                        $isActive,
                        $productId,
                    ]);
                    flash_set('ok', 'Producto actualizado.');
                } else {
                    $ins = db()->prepare(
                        'INSERT INTO products (name, slug, description, price, image_path, stock_quantity, low_stock_threshold, is_active)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                    );
                    $ins->execute([
                        $form['name'],
                        $slug,
                        $descVal,
                        $priceVal,
                        $imgVal,
                        (int) $stockVal,
                        $lowVal,
                        $isActive,
                    ]);
                    flash_set('ok', 'Producto creado.');
                }
                header('Location: /admin/products.php', true, 302);
                exit;
            }
        }
    }
}

$title = $productId > 0 ? 'Editar producto' : 'Nuevo producto';
layout_start($title, $user, 'admin-wide');

foreach ($errors as $err) {
    echo '<p class="flash err">' . h($err) . '</p>';
}
?>
<p><a class="btn secondary" href="/admin/products.php" style="margin-top:0;">← Lista de productos</a></p>

<form method="post" action="/admin/product_form.php<?= $productId > 0 ? '?id=' . $productId : '' ?>">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="product_id" value="<?= $productId ?>">

    <label for="name">Nombre</label>
    <input id="name" name="name" type="text" required maxlength="200" value="<?= h($form['name']) ?>">

    <label for="slug">Slug URL (opcional)</label>
    <input id="slug" name="slug" type="text" maxlength="220" value="<?= h($form['slug']) ?>"
           placeholder="Se genera solo a partir del nombre si lo dejas vacío">
    <small>Letras, números y guiones. Debe ser único.</small>

    <label for="description">Descripción</label>
    <textarea id="description" name="description" maxlength="65535"><?= h($form['description']) ?></textarea>

    <label for="price">Precio (€)</label>
    <input id="price" name="price" type="text" inputmode="decimal" required value="<?= h($form['price']) ?>" placeholder="0,00">

    <label for="stock_quantity">Stock</label>
    <input id="stock_quantity" name="stock_quantity" type="number" min="0" step="1" required value="<?= h($form['stock_quantity']) ?>">

    <label for="low_stock_threshold">Aviso stock bajo (opcional)</label>
    <input id="low_stock_threshold" name="low_stock_threshold" type="number" min="0" step="1" value="<?= h($form['low_stock_threshold']) ?>"
           placeholder="Vacío = 5 por defecto en listado">
    <small>En la lista se marca “bajo” si el stock es ≤ este valor (por defecto 5).</small>

    <label for="image_path">Imagen (URL o ruta, opcional)</label>
    <input id="image_path" name="image_path" type="text" maxlength="500" value="<?= h($form['image_path']) ?>" placeholder="https://... o ruta relativa">

    <label style="display:flex;align-items:center;gap:0.5rem;margin-top:1rem;font-weight:normal;">
        <input type="checkbox" name="is_active" value="1" <?= $form['is_active'] ? 'checked' : '' ?>>
        Producto activo (visible en catálogo cuando exista)
    </label>

    <button type="submit"><?= $productId > 0 ? 'Guardar cambios' : 'Crear producto' ?></button>
</form>
<?php
layout_end();
