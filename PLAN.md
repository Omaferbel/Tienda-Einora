# Plan — Tienda PHP (Einora / Hostinger)

Documento de referencia local. Actualiza fechas o decisiones aquí cuando cambien.

## 1. Objetivo

Tienda web en **PHP** con:

- Catálogo y **control de productos** (CRUD).
- **Inventario** (stock, descuento al vender, alertas opcionales).
- **Compras** con carrito y pedidos.
- **Registro de clientes** para guardar datos de contacto.
- **Administradores** con accesos propios (rol distinto al cliente).
- **Pago contraentrega** en la fase piloto (una ciudad). Sin pasarela online.
- **Futuro**: ampliación nacional; cobros vía **QR** y contacto directo; más adelante posible **API bancaria**.

## 2. Arquitectura sugerida

| Capa | Contenido |
|------|-----------|
| Público | Catálogo, ficha, registro/login cliente, carrito, checkout. |
| Admin | Login admin, productos, stock, pedidos, clientes (lectura). |
| Datos | MySQL (PDO + consultas preparadas). |
| Sesión | Carrito y usuario logueado. |

Estructura de carpetas (orientativa): `public/` o raíz web, `admin/`, `includes/`, `config/` (fuera del web root si el hosting lo permite).

## 3. Modelo de datos (mínimo)

- **users**: email, password hash, nombre, teléfono, `role` (`customer` | `admin`), fechas.
- **products**: nombre, descripción, precio, imagen, activo, etc.
- **inventory**: `product_id`, `quantity` (o stock en la misma tabla `products`).
- **orders**: usuario (nullable si invitado), datos de envío snapshot, total, `payment_method` (ej. `cod`), `status`, fechas.
- **order_items**: `order_id`, `product_id`, cantidad, `precio_unitario` al momento de la venta.

**Inventario**: al confirmar pedido, usar **transacción**: comprobar stock → insertar pedido e ítems → restar stock.

## 4. Fases de desarrollo

1. **Base**: BD, config, admin login, CRUD productos, stock.
2. **Tienda pública**: listado, detalle, registro/login clientes.
3. **Carrito y pedidos**: sesión, checkout contraentrega, emails opcionales.
4. **Pulido**: estados de pedido, stock mínimo, validaciones y CSRF en formularios críticos.

## 5. Seguridad

- `password_hash()` / `password_verify()`.
- PDO preparado; nunca concatenar entrada de usuario en SQL.
- `htmlspecialchars()` al mostrar datos.
- Tokens **CSRF** en admin y checkout.
- No commitear contraseñas: `config.local.php` o variables de entorno + `.gitignore`.

## 6. Hostinger — conexión SSH (tus datos van en el panel)

**Valores típicos** (confirma en hPanel → Advanced → SSH Access):

- Puerto: `65002` (no es el 22 estándar).
- Usuario: el que muestra el panel (ej. `u549981132`).
- Host: IP o hostname que indique Hostinger.

### Paso a paso SSH (Windows, CMD o PowerShell)

1. En hPanel → **SSH Access**: estado **Active**. Si dice Inactive, pulsa **Enable** y espera.
2. Define o cambia la **contraseña SSH** en el mismo apartado si hace falta.
3. Abre **CMD** o **PowerShell** y ejecuta (sustituye usuario y host por los tuyos):

   ```bash
   ssh -p 65002 TU_USUARIO@TU_HOST
   ```

4. Si pregunta por la autenticidad del host, escribe `yes`.
5. Cuando pida **password**, escribe la contraseña SSH (**no se ve al escribir**; es normal) y pulsa Enter.

Si entras, verás un prompt del servidor: la conexión SSH **funciona**. Eso abre una **terminal remota**; no es una ventana de arrastrar archivos.

### Aclaración: subir archivos

- **SSH** = shell (comandos). Para **archivos** usa una de estas opciones:
  - **SFTP** (mismo host, mismo puerto, mismo usuario/contraseña o clave): WinSCP, FileZilla en modo SFTP, o extensiones del editor.
  - **Administrador de archivos** en hPanel (subida manual).
  - **Git** (recomendado para ir versionando y desplegar con un clic o automático): ver sección 7.

## 7. Despliegue con Git en Hostinger

En hPanel → **Advanced → GIT** puedes:

1. **Repositorio nuevo vacío en `public_html`**: Hostinger clona una URL (GitHub/GitLab) en la ruta que elijas. **La carpeta destino debe estar vacía** la primera vez.
2. **Repositorio privado**: pulsa **Generate SSH Key** en Hostinger y añade esa clave pública en GitHub (Settings → SSH keys) o en Bitbucket, etc. Luego usa la URL `git@github.com:usuario/repo.git`.
3. **Rama**: indica `main` o `master` según tu repo.
4. Tras el primer deploy, en el servidor suele poder ejecutarse **git pull** por SSH para actualizar (según lo que permita tu plan/panel).

**Flujo de trabajo habitual**

1. En tu PC: proyecto en `e:\Cursor\Tienda`, `git init`, commits, remoto en GitHub.
2. En Hostinger GIT: conectar el repo y rama; despliegue a `public_html` (o subcarpeta).
3. Cada avance: `git push` desde tu PC; en el servidor **pull** o el botón de despliegue que ofrezca el panel.

Si el panel solo hace el clon inicial, entra por **SSH** y dentro de `public_html` (o la carpeta del sitio):

```bash
cd domains/einora.com/public_html
git pull
```

(Ajusta la ruta real: mírala con `pwd` / `ls` una vez conectado.)

## 8. Checklist rápido

- [ ] SSH activo y login OK
- [ ] Base de datos MySQL creada en hPanel (usuario, nombre BD, host, contraseña)
- [ ] PHP version adecuada (hPanel → PHP Configuration)
- [ ] `config.local.php` en servidor con credenciales (no en Git)
- [ ] Repo Git + despliegue Hostinger o SFTP como respaldo

## 9. Notas

- Dominio de prueba: **einora.com** (ajusta rutas en el servidor si usas otro).
- Huella ED25519 del servidor: la verás al primer `ssh`; puedes guardarla para comprobar en reconexiones futuras.

---

*Última actualización del documento: 2026-04-05*
