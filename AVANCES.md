# Registro de avances — Tienda Einora

Pequeño seguimiento de lo implementado y lo pendiente. Actualizar al cerrar cada bloque de trabajo.

## Hecho

- [x] Plan general (`PLAN.md`), SSH y rutas en Hostinger (`public_html` de einora.com).
- [x] Base MySQL `u549981132_tienda` creada; PHP 8.4 en hosting.
- [x] Esquema SQL: tablas `users`, `products`, `orders`, `order_items` (`database/schema.sql`).
- [x] Usuario admin semilla: **admin@einora.com** / **CambiarEstaClave1!** (cambiar tras primer acceso).
- [x] Configuración local `config.local.php` + plantilla `config/config.example.php`.
- [x] Conexión PDO (`includes/db.php`) y página de prueba (`index.php`).
- [x] **Producción einora.com:** `schema.sql` importado en phpMyAdmin; `public_html` con `index.php`, `includes/`, `config/`, `config.local.php`; sitio confirma conexión y **1 usuario** en `users`.

## Próximos pasos (orden sugerido)

1. **Autenticación:** login / registro clientes, sesiones, middleware rol `admin` vs `customer`.
2. **Admin:** CRUD productos, ajuste de stock, listado de pedidos y estados.
3. **Tienda pública:** catálogo, ficha producto, carrito (sesión), checkout contraentrega (`payment_method` = `cod`).
4. Pulido: CSRF en formularios críticos, mensajes de error amigables, imágenes de producto.

## Notas

- **Despliegue con Git:** repo **Omaferbel/Tienda-Einora**, rama **`main`** → **`public_html`**; auto-despliegue vía webhook Hostinger ↔ GitHub. Detalle en **`GIT-DEPLOY.md`**.
- Credenciales locales: `ssh-credentials.local.txt`, `db-credentials.local.txt` (no versionadas).
- Si reimportas `schema.sql` entero en una BD ya poblada, puede fallar por tablas existentes o email admin duplicado; en desarrollo suele bastar vaciar tablas o usar BD nueva.

---

*Última actualización: 2026-04-05 — despliegue inicial verificado en einora.com*
