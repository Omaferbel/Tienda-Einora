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

### Si pide carpeta vacía

La primera vez Hostinger a veces exige que el destino esté vacío.

1. En **Administrador de archivos**, entra en `domains/einora.com/public_html`.
2. **Descarga una copia** de `config.local.php` a tu PC (respaldo).
3. Mueve el resto a una carpeta de respaldo (ej. `_backup_manual`) o bórralo si ya tienes todo en Git.
4. Crea el repositorio GIT en el panel y despliega.
5. Vuelve a **subir solo** `config.local.php` a `public_html`.

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
