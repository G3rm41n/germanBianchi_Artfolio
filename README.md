# Artfolio - Plataforma de Portafolios Artísticos Multidisciplinarios

Artfolio es una plataforma web desarrollada con **Laravel 12**, **Tailwind CSS**, **Alpine.js** y **MySQL**, diseñada para que artistas visuales, escultores 3D, músicos, diseñadores e ilustradores puedan exhibir sus obras, crear colecciones temáticas (estilo tableros de Pinterest), recibir retroalimentación y gestionar su presencia profesional.

---

## 📋 Requisitos Previos del Sistema

Para clonar y ejecutar este proyecto en una nueva computadora, asegúrate de tener instalado:

1. **PHP**: Versión `>= 8.3`
   - Extensiones PHP requeridas: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`, `gd` (o `imagick`).
2. **Composer**: Versión `>= 2.x` ([getcomposer.org](https://getcomposer.org/))
3. **Node.js y NPM**: Node `>= 18.x` o `>= 20.x` ([nodejs.org](https://nodejs.org/))
4. **Base de Datos**: MySQL `>= 8.0` o MariaDB `>= 10.4` (por ejemplo mediante Laragon, XAMPP, Docker o Laravel Herd).
5. **Git**: Para control de versiones ([git-scm.com](https://git-scm.com/)).

---

## 🚀 Guía de Instalación Paso a Paso

Sigue estos comandos en tu terminal (PowerShell, Git Bash o CMD):

### 1. Clonar el Repositorio
```bash
git clone https://github.com/G3rm41n/germanBianchi_Artfolio.git
cd germanBianchi_Artfolio
```
*(Si vas a trabajar en una rama específica como `Main3`, ejecuta `git checkout Main3`)*.

---

### 2. Instalar Dependencias de PHP (Backend)
```bash
composer install
```

---

### 3. Configurar el Archivo de Entorno (`.env`)
Copia el archivo de ejemplo a tu archivo de configuración local:

- **En Windows (PowerShell / CMD):**
  ```powershell
  copy .env.example .env
  ```
- **En Linux / macOS:**
  ```bash
  cp .env.example .env
  ```

Genera la clave de cifrado única de la aplicación:
```bash
php artisan key:generate
```

---

### 4. Configurar la Base de Datos
1. Abre tu gestor de base de datos preferido (HeidiSQL, phpMyAdmin, MySQL Workbench o terminal) y crea una base de datos vacía llamada **`artfolio`** con cotejamiento `utf8mb4_unicode_ci`:
   ```sql
   CREATE DATABASE artfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Revisa que tu archivo `.env` apunte a tu conexión MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=artfolio
   DB_USERNAME=root
   DB_PASSWORD=
   ```

---

### 5. Ejecutar Migraciones y Poblar con Datos de Prueba (Seeders)
Ejecuta el siguiente comando para estructurar las tablas y cargar automáticamente los roles, categorías, artistas y obras de demostración:
```bash
php artisan migrate:fresh --seed
```

> **¿Qué hace este comando?**
> - Reconstruye desde cero el esquema de la base de datos de forma limpia.
> - Ejecuta los seeders:
>   - `AdminSeeder`: Crea la cuenta de Administrador.
>   - `TaxonomySeeder`: Carga disciplinas artísticas, categorías y habilidades.
>   - `DemoContentSeeder` y `RichCommunitySeeder`: Carga artistas multidisciplinarios con portfolios, colecciones y obras interactivas (YouTube, Sketchfab, DeviantArt, Pinterest, etc.).

---

### 6. Crear el Enlace Simbólico de Almacenamiento (Storage Link)
Es indispensable para que las imágenes de obras, miniaturas y avatares cargados sean visibles públicamente en el navegador:
```bash
php artisan storage:link
```

---

### 7. Instalar Dependencias de Frontend y Compilar Assets
Instala los paquetes de Node y compila los estilos Tailwind CSS y scripts Alpine.js:
```bash
npm install
npm run build
```

*(Si vas a estar desarrollando activamente con recarga en vivo de estilos y vistas, puedes dejar corriendo en otra terminal `npm run dev`)*.

---

### 8. Iniciar el Servidor Local
Inicia el servidor de desarrollo de Laravel:
```bash
php artisan serve
```

La plataforma estará lista y disponible en:
👉 **[http://localhost:8000](http://localhost:8000)**  
*(o `http://germanbianchi_artfolio.test` si utilizas Laragon con Virtual Hosts activos)*.

---

## 🔑 Credenciales de Acceso por Defecto

Una vez ejecutado el seeder (`migrate:fresh --seed`), puedes iniciar sesión con las siguientes cuentas de prueba:

### Cuenta Administrador
- **Email:** `admin@artfolio.test`
- **Contraseña:** `password`
- **Rol:** Acceso completo al Panel de Administración (`/admin`), registro de auditoría, moderación de obras y gestión de taxonomías.

### Cuentas de Artistas Demo
Todos los usuarios generados por los seeders tienen la misma contraseña:
- **Contraseña:** `password`
- **Ejemplos de cuentas:**
  - `elena@artfolio.test` (Artista 3D y arqueología digital)
  - `marcus@artfolio.test` (Concept Artist & Ilustrador)
  - O cualquier otro artista visible en el explorador de artistas.

---

## ⚙️ Configuraciones Opcionales

### Cloudflare Turnstile (Anti-Bot)
Por defecto, el archivo `.env` viene preparado para Turnstile. Si estás en un entorno de desarrollo local sin conexión constante a internet o deseas hacer una presentación sin bloqueos:
```env
TURNSTILE_ENABLED=false
```
Con este valor en `false`, el formulario de inicio de sesión no requerirá verificación de Cloudflare.

### Servidor de Correo Local (Mailpit / MailHog)
Para capturar correos de restablecimiento de contraseña o notificaciones de bienvenida:
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```
*(Si usas Laragon, activa la herramienta Mailpit / MailHog en Menú > Herramientas > Mail)*.

---

## 🧪 Pruebas Automatizadas

El proyecto cuenta con una amplia suite de pruebas unitarias y de integración (PHPUnit) que aseguran el correcto funcionamiento de todas las funcionalidades:

```bash
php artisan test
```

Para limpiar la caché del sistema en caso de realizar cambios de configuración:
```bash
php artisan optimize:clear
```
