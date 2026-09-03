# 🏗️ ETAPA 1 — Plan de Implementación Detallado
## Artfolio: Prototipo Funcional con los 3 Roles

---

## Estado de Partida (Confirmado)

| Archivo / Sistema | Estado actual |
|---|---|
| `.env` línea 23 | `DB_CONNECTION=MySQL` (en mayúsculas — hay que corregir a `mysql`) |
| `.env` líneas 24-28 | Credenciales MySQL **comentadas** — hay que descomentarlas |
| `APP_NAME` | `Laravel` — hay que cambiar a `Artfolio` |
| `APP_URL` | `http://localhost:8000` — cambiar a URL de Laragon |
| Base de datos MySQL `artfolio` | **No existe** — hay que crearla manualmente |
| Variables `ADMIN_*` | **No existen** en `.env` — hay que agregarlas |
| `AppServiceProvider` | Vacío — hay que agregar el Gate `admin` |
| `User` model | Solo `name`, `email`, `password` — faltan 6 columnas |
| `RegisteredUserController` | No genera `slug` al registrar |
| `DatabaseSeeder` | Crea un usuario de prueba genérico, sin admin desde `.env` |
| Rutas `/admin/*` | **No existen** |
| Vista `welcome.blade.php` | Default de Laravel (72 KB) — reemplazar |

---

## ⚠️ PASO HUMANO PREVIO OBLIGATORIO — Configurar MySQL en Laragon

> [!CAUTION]
> **Este paso LO DEBE HACER EL HUMANO antes de que el agente IA ejecute cualquier comando.** Requiere abrir una interfaz gráfica que el agente no puede controlar.

### Instrucciones para el humano:

1. **Abrir Laragon** → asegurarse de que los servicios **Apache/Nginx** y **MySQL** estén corriendo (botones verdes)
2. **Abrir HeidiSQL** (viene incluido en Laragon) → click en "Nueva sesión" o conectarse a `127.0.0.1:3306` con usuario `root` y sin contraseña (configuración default de Laragon)
3. **Crear la base de datos:**
   - Click derecho en el panel izquierdo → "Crear nuevo" → "Base de datos"
   - Nombre: `artfolio`
   - Cotejamiento (Collation): `utf8mb4_unicode_ci`
   - Click "Aceptar"
4. **Confirmar:** la base de datos `artfolio` debe aparecer en el panel izquierdo de HeidiSQL
5. **Anotar las credenciales** (defaults de Laragon):
   - Host: `127.0.0.1`
   - Puerto: `3306`
   - Usuario: `root`
   - Contraseña: *(vacía)*

**→ Una vez hecho esto, avisar al agente para que continúe.**

---

## BLOQUE A — Configuración del Entorno (`.env`)
*El agente IA realiza estos cambios directamente en el archivo.*

### A.1 — Cambios en el archivo `.env`

**Cambios que el agente hará:**

```diff
- APP_NAME=Laravel
+ APP_NAME=Artfolio

- APP_URL=http://localhost:8000
+ APP_URL=http://germanBianchi_artfolio.test

- DB_CONNECTION=MySQL
+ DB_CONNECTION=mysql
- # DB_HOST=127.0.0.1
- # DB_PORT=3306
- # DB_DATABASE=laravel
- # DB_USERNAME=root
- # DB_PASSWORD=
+ DB_HOST=127.0.0.1
+ DB_PORT=3306
+ DB_DATABASE=artfolio
+ DB_USERNAME=root
+ DB_PASSWORD=

+ ADMIN_NAME="Germán Bianchi"
+ ADMIN_EMAIL=admin@artfolio.test
+ ADMIN_PASSWORD=Admin1234!

- MAIL_MAILER=log
+ MAIL_MAILER=smtp
- MAIL_PORT=2525
+ MAIL_PORT=1025
```

> [!NOTE]
> `MAIL_PORT=1025` es el puerto SMTP de Mailpit en Laragon. Los emails quedan capturados localmente en `http://localhost:8025`.

---

## BLOQUE B — Base de Datos: Migración de `users`
*El agente IA crea el archivo de migración y lo ejecuta.*

### B.1 — Crear migración para extender `users`

**Comando del agente:**
```powershell
php artisan make:migration add_artfolio_fields_to_users_table --table=users
```

**Contenido que el agente escribirá en el archivo generado:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug', 80)->unique()->nullable()->after('name');
            $table->text('bio')->nullable()->after('email');
            $table->enum('commission_status', ['open', 'closed'])->default('open')->after('bio');
            $table->boolean('is_admin')->default(false)->after('commission_status');
            $table->enum('status', ['active', 'suspended'])->default('active')->after('is_admin');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['slug', 'bio', 'commission_status', 'is_admin', 'status']);
            $table->dropSoftDeletes();
        });
    }
};
```

### B.2 — Ejecutar migraciones

**Comando del agente:**
```powershell
php artisan migrate
```

**Resultado esperado:**
```
  INFO  Running migrations.

  2026_09_03_000001_add_artfolio_fields_to_users_table ............... DONE
```

---

## BLOQUE C — Modelo `User` Actualizado
*El agente reescribe `app/Models/User.php`.*

**Contenido nuevo del modelo:**

```php
<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'slug', 'bio', 'commission_status', 'is_admin', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
    }

    // --- Slug auto-generado al crear ---
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->slug)) {
                $user->slug = static::generateUniqueSlug($user->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name, '_');
        $slug = $base;
        $i    = 1;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '_' . $i++;
        }
        return $slug;
    }

    // --- Scope: solo administradores ---
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    // --- Helpers ---
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
```

---

## BLOQUE D — Gate `admin` y Middleware
*El agente modifica `AppServiceProvider` y crea el middleware.*

### D.1 — Gate en `AppServiceProvider`

**Archivo modificado:** `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Gate para acceso al panel de administración
        Gate::define('admin', function ($user) {
            return $user->is_admin === true && $user->status === 'active';
        });
    }
}
```

### D.2 — Crear Middleware `EnsureIsAdmin`

**Comando del agente:**
```powershell
php artisan make:middleware EnsureIsAdmin
```

**Contenido que el agente escribirá en `app/Http/Middleware/EnsureIsAdmin.php`:**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin extends Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            abort(403, 'Acceso restringido. Solo administradores.');
        }

        return $next($request);
    }
}
```

> [!NOTE]
> En Laravel 13, el middleware se registra automáticamente. No es necesario editarlo en `bootstrap/app.php` manualmente para rutas, solo se referencia por su nombre de clase en las rutas.

---

## BLOQUE E — DatabaseSeeder con Admin desde `.env`
*El agente reescribe `database/seeders/DatabaseSeeder.php`.*

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Provisión idempotente del Administrador desde .env (RF-12)
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@artfolio.test')],
            [
                'name'     => env('ADMIN_NAME', 'Administrador'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin1234!')),
                'is_admin' => true,
                'status'   => 'active',
            ]
        );
    }
}
```

**Comando del agente para ejecutar el seeder:**
```powershell
php artisan db:seed
```

**Resultado esperado:**
```
  INFO  Seeding database.

  Database\Seeders\DatabaseSeeder ....................................... DONE
```

---

## BLOQUE F — Controlador y Rutas de Administración
*El agente crea el controlador y actualiza las rutas.*

### F.1 — Crear `Admin\DashboardController`

**Comando del agente:**
```powershell
php artisan make:controller Admin/DashboardController
```

**Contenido que el agente escribirá:**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users'   => User::where('is_admin', false)->count(),
            'total_admins'  => User::where('is_admin', true)->count(),
            'suspended'     => User::where('status', 'suspended')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
```

### F.2 — Actualizar `routes/web.php`

**Archivo completo actualizado:**

```php
<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Support\Facades\Route;

// ── Página de inicio (placeholder Etapa 1) ───────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ── Artista autenticado: Dashboard y Perfil ──────────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Panel de Administración ──────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureIsAdmin::class])
    ->group(function () {
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    });

// ── Rutas de Autenticación (Breeze) ─────────────────────────────────────────
require __DIR__.'/auth.php';
```

---

## BLOQUE G — Actualizar `RegisteredUserController`
*El agente agrega la generación de slug al registro.*

**Solo se modifica el método `store` para incluir el slug:**

```php
$user = User::create([
    'name'     => $request->name,
    'email'    => $request->email,
    'password' => Hash::make($request->password),
    // El slug se genera automáticamente via el booted() del modelo User
]);
```

> [!NOTE]
> No es necesario cambiar código aquí — el modelo `User::booted()` genera el slug automáticamente en `creating`. El `RegisteredUserController` existente ya funciona correctamente.

---

## BLOQUE H — Vistas: Welcome, Dashboard y Admin
*El agente crea/actualiza las vistas.*

### H.1 — Nuevo `welcome.blade.php` (página de inicio Artfolio)

**Reemplaza completamente** el default de Laravel con un placeholder de identidad visual de Artfolio: hero oscuro, tipografía elegante, botones de acceso.

### H.2 — Dashboard mejorado para Artistas

**Actualizar** `resources/views/dashboard.blade.php` con mensaje de bienvenida personalizado que muestre el nombre y slug del artista.

### H.3 — Nueva vista `resources/views/admin/dashboard.blade.php`

Vista con las estadísticas básicas del sistema: contador de usuarios, mensaje de bienvenida al administrador.

### H.4 — Layout Admin `resources/views/layouts/admin.blade.php`

Layout separado para el panel admin con sidebar de navegación básico.

---

## BLOQUE I — Limpiar Caché y Compilar Assets

**Comandos del agente (en orden):**

```powershell
# Limpiar cachés de configuración y rutas
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Compilar assets (Tailwind + Alpine)
npm run build
```

---

## BLOQUE J — Verificación Final del Agente

**Comandos de verificación del agente:**

```powershell
# Verificar que las rutas están registradas
php artisan route:list --columns=method,uri,name,middleware

# Verificar que el admin fue creado
php artisan tinker --execute="echo App\Models\User::where('is_admin', true)->first()->toJson();"

# Verificar que el slug se genera
php artisan tinker --execute="echo App\Models\User::generateUniqueSlug('Test Artista');"
```

---

## ✅ PASOS DE VERIFICACIÓN HUMANA

*Luego de que el agente complete todos los bloques, el humano debe verificar lo siguiente:*

---

### 🗄️ 1. Verificar la Base de Datos en HeidiSQL

1. Abrir **HeidiSQL** → conectarse a `artfolio`
2. Expandir la base de datos → verificar las siguientes **tablas existentes**:

| Tabla | ¿Existe? | Columnas clave a confirmar |
|---|---|---|
| `users` | ✓ | `slug`, `bio`, `commission_status`, `is_admin`, `status`, `deleted_at` |
| `sessions` | ✓ | (viene de Breeze) |
| `jobs` | ✓ | (colas de BD) |
| `cache` | ✓ | |
| `password_reset_tokens` | ✓ | |

3. Hacer clic en la tabla `users` → pestaña **"Datos"** → verificar que existe **1 fila** con:
   - `name` = "Germán Bianchi" (o el valor de `ADMIN_NAME`)
   - `email` = "admin@artfolio.test"
   - `is_admin` = 1
   - `status` = "active"
   - `slug` = "german_bianchi" (auto-generado)

---

### 🌐 2. Verificar la Página en el Navegador

Abrir `http://germanBianchi_artfolio.test` (o `http://localhost:8000` si no está configurado el dominio virtual en Laragon).

**Checklist del navegador:**

| URL | Qué verificar |
|---|---|
| `/` | Página de inicio con identidad visual de Artfolio (no el default de Laravel) |
| `/login` | Formulario de login de Breeze estilizado |
| `/register` | Formulario de registro funcionando |
| `/dashboard` | **Sin sesión**: redirige a `/login` ✓ |
| `/admin` | **Sin sesión**: redirige a `/login` ✓ |

**Flujo de verificación — Artista:**
1. Ir a `/register` → registrar un usuario nuevo (ej: nombre "María García", email "maria@test.com")
2. Confirmar que redirige a `/dashboard` con mensaje de bienvenida
3. Verificar que en HeidiSQL la nueva fila del usuario tiene un `slug` generado automáticamente (ej: "maria_garcia")
4. Ir a `/profile` → verificar formulario de perfil con campos `bio` y `commission_status`
5. Ir a `/admin` → verificar que **redirige con error 403** (usuario no es admin)
6. Cerrar sesión (`/logout`)

**Flujo de verificación — Administrador:**
1. Ir a `/login` → ingresar con las credenciales del admin (las del `.env`)
2. Confirmar que redirige a `/dashboard`
3. Ir a `/admin` → verificar que **muestra el panel de administración** con las estadísticas
4. Verificar que las estadísticas muestran al menos 1 usuario artista (María García)
5. Cerrar sesión

**Flujo de verificación — Visitante:**
1. Sin sesión, ir a `/dashboard` → confirmar redirección a `/login`
2. Sin sesión, ir a `/admin` → confirmar redirección a `/login`
3. La página de inicio `/` es visible sin autenticación ✓

---

### 📧 3. Verificar Mailpit (Opcional en Etapa 1)

Si se registró un nuevo usuario, abrir `http://localhost:8025` para confirmar que Mailpit está capturando correos. En Etapa 1 no hay emails de negocio, pero se puede verificar que el canal de correo está activo.

---

### 🐛 4. Si algo falla — Cómo diagnosticar

| Síntoma | Causa probable | Solución |
|---|---|---|
| Error "SQLSTATE[HY000]" | MySQL no conecta | Verificar que MySQL está corriendo en Laragon y que las credenciales en `.env` son correctas |
| Error "Base de datos artfolio no existe" | No se creó en HeidiSQL | Crear la BD manualmente en HeidiSQL |
| Rutas `/admin` dan 404 | Caché de rutas vieja | Ejecutar `php artisan route:clear` |
| Slug no se genera | Modelo no tiene `booted()` | Verificar que el `User.php` fue guardado correctamente |
| Assets no cargan (CSS roto) | Vite no compiló | Ejecutar `npm run build` o `npm run dev` |
| Error 403 en admin para el propio admin | Gate no reconoce is_admin | Verificar en HeidiSQL que `is_admin = 1` en la fila del admin |

---

## 📋 Resumen de Archivos Modificados por el Agente

| Archivo | Acción |
|---|---|
| `.env` | Modificar (MySQL, APP_NAME, APP_URL, ADMIN_*, MAIL) |
| `database/migrations/[timestamp]_add_artfolio_fields_to_users_table.php` | **Nuevo** |
| `app/Models/User.php` | Reescribir (SoftDeletes, fillable, booted, helpers) |
| `app/Providers/AppServiceProvider.php` | Modificar (Gate admin) |
| `app/Http/Middleware/EnsureIsAdmin.php` | **Nuevo** |
| `database/seeders/DatabaseSeeder.php` | Reescribir (admin desde .env) |
| `app/Http/Controllers/Admin/DashboardController.php` | **Nuevo** |
| `routes/web.php` | Reescribir (agregar rutas admin) |
| `resources/views/welcome.blade.php` | Reemplazar (identidad Artfolio) |
| `resources/views/dashboard.blade.php` | Mejorar (bienvenida personalizada) |
| `resources/views/admin/dashboard.blade.php` | **Nueva** |
| `resources/views/layouts/admin.blade.php` | **Nuevo** |

**Total: 7 archivos modificados, 5 archivos nuevos creados, 2 comandos artisan, 1 npm.**
