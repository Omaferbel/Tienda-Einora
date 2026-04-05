# Despliegue automático con Git (GitHub + Hostinger)

Objetivo: hacer `git push` desde tu PC y que **einora.com** se actualice sin subir archivos a mano.

## Qué ya está preparado en el proyecto

- Repositorio Git local con historial.
- `.gitignore` evita subir `config.local.php`, credenciales y archivos `.local`.

**Importante:** `config.local.php` **no** va a GitHub. En el servidor debe existir **solo una vez** (creado a mano en `public_html` o copiado por SFTP). Cada despliegue Git **no** lo borra si Hostinger hace merge/pull sin limpiar la carpeta entera; si alguna vez reinstalas desde cero, vuelve a subir ese archivo.

---

## Paso 1 — Repositorio en GitHub

1. Entra en [GitHub](https://github.com) → **New repository**.
2. Nombre (ej. `tienda-einora`), **público** (más simple; no hay secretos en el código) o privado (ver paso 4).
3. **Sin** README, .gitignore ni licencia (el proyecto local ya tiene commits).
4. Crea el repositorio y copia la URL **HTTPS** (ej. `https://github.com/TU_USUARIO/tienda-einora.git`).

En **PowerShell** en la carpeta del proyecto:

```powershell
cd e:\Cursor\Tienda
git branch -M main
git remote add origin https://github.com/Omaferbel/Tienda-Einora.git
git push -u origin main
```

Si GitHub avisa que el repositorio se movió y muestra otra URL, alinéala con:

`git remote set-url origin https://github.com/Omaferbel/Tienda-Einora.git`

Si GitHub pide usuario/contraseña, usa un **Personal Access Token** como contraseña (Settings → Developer settings → Tokens).

---

## Paso 2 — Primera conexión en Hostinger (GIT)

1. hPanel → sitio **einora.com** → **Avanzado → GIT**.
2. En **Create a New Repository**:
   - **Repository:** la misma URL (`https://github.com/...` para repo público).
   - **Branch:** `main`.
   - **Directory (opcional) / Install path:** **déjalo completamente vacío.**

**No escribas `public_html` en ese campo.** Si lo rellenas con `public_html`, Hostinger crea una carpeta **dentro** de la raíz del sitio y el código queda en `public_html/public_html/`. El dominio sigue sirviendo el `index.php` **viejo** de la carpeta de arriba.

### Si ya tienes `public_html/public_html/` (despliegue en la carpeta equivocada)

1. **Respalda** `config.local.php` del nivel correcto (`domains/.../public_html/`).
2. En el **administrador de archivos**, entra en la carpeta **interior** `public_html` (la que sí se actualiza con Git).
3. **Mueve** todo su contenido (`index.php`, `includes`, `config`, `.git` si existe, etc.) al **`public_html` de un nivel arriba** (la raíz del sitio). Si pide sobrescribir `index.php`, acepta (el de dentro suele ser el actualizado).
4. **Borra** la carpeta `public_html` vacía que quedó dentro (solo la interior, no la del dominio).
5. En hPanel → **GIT**: **borra** el repositorio configurado y **vuelve a crearlo** con el campo **Directory / Install path vacío**, misma URL y rama `main`. Vuelve a configurar el **webhook** en GitHub si Hostinger te da una URL nueva.
6. Pulsa **Deploy** o haz un `git push` vacío (`git commit --allow-empty -m "redeploy"` y `git push`) para comprobar que ahora actualiza la raíz correcta.

### Error: «Project directory is not empty»

Significa que la carpeta destino (**`public_html`**, si **Directory** está vacío) **no está vacía** (cuenta todo: carpetas, archivos ocultos como `.git` o `.htaccess`). **No** se soluciona poniendo un nombre al azar en **Directory** si lo que quieres es servir el sitio en la raíz del dominio.

**Solución recomendada:**

1. Ve a **`domains/einora.com/`** (carpeta **padre** de `public_html`).
2. Crea **`_respaldo_sitio`** (u otro nombre) **ahí**, al mismo nivel que `public_html`.
3. Entra en **`public_html`**, selecciona **todo** y **muévelo** a **`_respaldo_sitio`**.
4. Comprueba que **`public_html` quede totalmente vacía**.
5. En **GIT → Create a New Repository**: URL, rama `main`, **Directory vacío** → **Create** / desplegar.
6. Copia otra vez **`config.local.php`** desde `_respaldo_sitio` (o tu PC) dentro de **`public_html`**.
7. Si Hostinger muestra webhook nuevo, actualízalo en GitHub.

**No pongas el respaldo dentro de `public_html`**: si dentro queda una carpeta `_backup`, el directorio **sigue sin estar vacío**.

### El formulario parece obligar «Directory»

1. Primero **vacía `public_html`** como arriba; a menudo entonces el campo **sí puede ir vacío** y desaparece el error.
2. Si con `public_html` vacía el interfaz **no acepta vacío**, prueba escribir **solo** `.` (punto). **No** escribas `public_html` (anida otra vez).
3. Si sigue fallando, despliega con **SSH** sin el asistente del panel (con `public_html` vacía):

```bash
cd ~/domains/einora.com/public_html
git clone https://github.com/Omaferbel/Tienda-Einora.git .
```

Luego sube **`config.local.php`**. Las actualizaciones: entra por SSH y `git pull origin main` (el webhook del panel no aplica si no usas GIT en hPanel).

### Si pide carpeta vacía (resumen)

1. Respalda `config.local.php`.
2. Vacía **`public_html`** moviendo **todo** a **`domains/einora.com/_respaldo_sitio`**.
3. Crea el repositorio GIT y despliega.
4. Vuelve a poner **`config.local.php`** en `public_html`.

---

## Paso 3 — Actualizar el sitio después de cada cambio

### Opción A — Auto-despliegue (recomendada)

1. En hPanel → **GIT**, con el repositorio ya creado, abre **Auto-Deployment** (o similar).
2. Copia la **Webhook URL** que te da Hostinger.
3. En GitHub: tu repo → **Settings → Webhooks → Add webhook**:
   - **Payload URL:** la URL de Hostinger.
   - **Content type:** `application/json`.
   - **Events:** suele bastar “Just the push event”.
4. Guarda.

A partir de ahí, cada `git push` a la rama configurada puede disparar el despliegue (puede tardar un minuto).

### Opción B — Manual desde el panel

En **GIT**, usa el botón de **Deploy** / **Pull** cuando quieras traer los últimos commits (según el nombre en tu panel).

### Opción C — Por SSH

```bash
cd ~/domains/einora.com/public_html
git pull origin main
```

(Solo si en esa carpeta quedó un clon/repositorio Git gestionado por Hostinger.)

---

## Paso 4 — Repositorio privado en GitHub

1. En Hostinger → GIT → **Generate SSH Key** y copia la clave **pública**.
2. En GitHub → **Settings → SSH and GPG keys → New SSH key** y pégala.
3. En Hostinger, en **Repository**, usa la URL **SSH:**  
   `git@github.com:TU_USUARIO/tienda-einora.git`

---

## Resumen del día a día

1. Cambias código en `e:\Cursor\Tienda`.
2. `git add -A` → `git commit -m "mensaje"` → `git push`.
3. Con webhook: el hosting actualiza solo; si no, **Deploy** en el panel o `git pull` por SSH.

### No “ves” cambios en GitHub o en la web

- En GitHub, mira **Commits** en la rama `main` (no solo la portada del repo).
- Los cambios solo en `.md` no modifican la página pública PHP.
- En **View latest build output**, mensajes como `nothing to commit, working tree clean` suelen indicar que el servidor **ya tenía** el último código (despliegue correcto, sin archivos nuevos que aplicar).

---

*Documento alineado con la ayuda oficial de Hostinger sobre GIT y webhooks de auto-despliegue.*
