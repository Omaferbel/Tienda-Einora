# Registro de avances — Tienda Einora

Pequeño seguimiento de lo implementado y lo pendiente. Actualizar al cerrar cada bloque de trabajo.

## Hecho

- [x] Plan general (`PLAN.md`), SSH y rutas en Hostinger (`public_html` de einora.com).
- [x] Base MySQL `u549981132_tienda` creada; PHP 8.4 en hosting.
- [x] Esquema SQL: tablas `users`, `products`, `orders`, `order_items` (`database/schema.sql`).
- [x] Usuario admin semilla: **admin@einora.com** / **CambiarEstaClave1!** (cambiar tras primer acceso).
- [x] Configuración local `config.local.php` + plantilla `config/config.example.php`.
- [x] Conexión PDO (`includes/db.php`) y página de prueba (`index.php`).

## Próximos pasos (orden sugerido)

1. **Importar BD:** en phpMyAdmin, base `u549981132_tienda` → SQL o Importar → pegar/elegir `database/schema.sql`.
2. **Subir código** a `domains/einora.com/public_html` (SFTP, Git o archivos): al menos `index.php`, `includes/`, `config/` y en el servidor crear **`config.local.php`** con las credenciales MySQL (no subir credenciales a Git si el repo es público).
3. Abrir **https://einora.com/** (o la URL temporal) y comprobar mensaje de conexión OK.
4. **Autenticación:** login / registro clientes, sesiones, middleware rol `admin` vs `customer`.
5. **Admin:** CRUD productos, ajuste de stock, listado de pedidos y estados.
6. **Tienda pública:** catálogo, ficha producto, carrito (sesión), checkout contraentrega (`payment_method` = `cod`).
7. Pulido: CSRF en formularios críticos, mensajes de error amigables, imágenes de producto.

## Notas

- Credenciales locales: `ssh-credentials.local.txt`, `db-credentials.local.txt` (no versionadas).
- Si reimportas `schema.sql` entero en una BD ya poblada, puede fallar por tablas existentes o email admin duplicado; en desarrollo suele bastar vaciar tablas o usar BD nueva.

---

*Última actualización: 2026-04-05*
