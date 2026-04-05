# Registro de avances — Tienda Einora

Pequeño seguimiento de lo implementado y lo pendiente. Actualizar al cerrar cada bloque de trabajo.

## Hecho

- [x] Plan general (`PLAN.md`), SSH y rutas en Hostinger (`public_html` de einora.com).
- [x] Base MySQL `u549981132_tienda` creada; PHP 8.4 en hosting.
- [x] Esquema SQL: tablas `users`, `products`, `orders`, `order_items` (`database/schema.sql`).
- [x] Usuario admin semilla: **admin@einora.com** / **CambiarEstaClave1!** (cambiar tras primer acceso).
- [x] Configuración local `config.local.php` + plantilla `config/config.example.php`.
- [x] Conexión PDO (`includes/db.php`) y página de prueba (`index.php`).
- [x] **Producción einora.com:** `schema.sql` importado; `public_html` vaciado y Git Hostinger recreado con **Directory** vacío; despliegue desde panel + `config.local.php`; sitio en raíz del dominio.
- [x] **Autenticación:** sesiones, CSRF en formularios, `login.php` / `register.php` (solo clientes) / `logout.php`, `db()` singleton, `require_admin()`, panel stub `admin/index.php`. Admin semilla: **admin@einora.com** / **CambiarEstaClave1!**.
- [x] **Admin productos:** listado (`admin/products.php`), alta/edición (`admin/product_form.php`), activar/desactivar, eliminar si no hay pedidos; slug único; aviso stock bajo.

## Próximos pasos (orden sugerido)

1. **Admin pedidos:** listado, cambio de estado.
2. **Tienda pública:** catálogo, ficha producto, carrito (sesión), checkout contraentrega (`payment_method` = `cod`).
3. Pulido: más CSRF donde falte, subida de imágenes, mensajes de error.

## Notas

- **Despliegue con Git:** repo **Omaferbel/Tienda-Einora**, rama **`main`** → **`public_html`**; auto-despliegue vía webhook Hostinger ↔ GitHub. Detalle en **`GIT-DEPLOY.md`**.
- Credenciales locales: `ssh-credentials.local.txt`, `db-credentials.local.txt` (no versionadas).
- Si reimportas `schema.sql` entero en una BD ya poblada, puede fallar por tablas existentes o email admin duplicado; en desarrollo suele bastar vaciar tablas o usar BD nueva.

---

*Última actualización: 2026-04-06 — CRUD productos e inventario en admin*
