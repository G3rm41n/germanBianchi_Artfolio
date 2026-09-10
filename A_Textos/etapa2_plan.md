# 🗄️ ETAPA 2 — Plan de Implementación Detallado
## Artfolio: Esquema de Base de Datos Completo y Modelos Eloquent

---

## Objetivo de la Etapa

Crear las 5 tablas restantes que necesita la plataforma (`artworks`, `collections`, `artwork_collection`, `bookmarks`, `reports`) junto con sus modelos Eloquent completos (relaciones, scopes, traits). Al finalizar, la base de datos estará 100% lista para las etapas de funcionalidad.

---

## Estado de Partida (Etapa 1 completada ✅)

| Elemento | Estado |
|---|---|
| Tabla `users` con campos extendidos | ✅ Migrada |
| Tablas `sessions`, `jobs`, `cache` | ✅ Migradas |
| Modelo `User` con SoftDeletes, slug auto, helpers | ✅ Listo |
| Gate `admin` + Middleware `EnsureIsAdmin` | ✅ Listos |
| `DatabaseSeeder` con admin desde `.env` | ✅ Listo |
| Modelos `Artwork`, `Collection`, `Bookmark`, `Report` | ❌ **Por crear** |
| Migraciones de esas 5 tablas | ❌ **Por crear** |
| Relaciones Eloquent entre modelos | ❌ **Por crear** |
| Índices MySQL para búsqueda y rendimiento | ❌ **Por crear** |

---

## ⚠️ PASO HUMANO PREVIO OBLIGATORIO

> [!CAUTION]
> **Antes de que el agente ejecute cualquier comando**, el humano debe verificar:
> 1. Abrir **Laragon** y confirmar que el botón de **MySQL** está en **verde** (activo).
> 2. Si MySQL está parado (rojo), hacer clic en **"Iniciar Todo"** o en el botón individual de MySQL.
> 3. Opcionalmente, abrir `http://localhost:8000` para confirmar que la Etapa 1 sigue funcionando (puede que necesite ejecutar `php artisan serve` en la terminal primero).

---

## BLOQUE A — Crear los 5 archivos de migración

*El agente ejecuta `make:migration` para cada tabla. Los archivos se crean vacíos y el agente los rellena inmediatamente.*

### A.1 — Comando: Crear los 5 esqueletos de migración

```powershell
php artisan make:migration create_artworks_table
php artisan make:migration create_collections_table
php artisan make:migration create_artwork_collection_table
php artisan make:migration create_bookmarks_table
php artisan make:migration create_reports_table
```

---

## BLOQUE B — Rellenar la migración: `create_artworks_table`

*El agente escribe el esquema completo en el archivo generado.*

**Contenido del archivo `[timestamp]_create_artworks_table.php`:**

```php
Schema::create('artworks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->string('price_hint', 100)->nullable();       // precio orientativo
    $table->text('external_url');                        // URL del recurso externo
    $table->string('provider', 50);                      // ej: 'youtube', 'sketchfab'
    $table->enum('category', ['3d', 'video', 'audio', 'image', 'document']);
    $table->enum('render_type', ['iframe', 'image', 'audio', 'document']);
    $table->text('thumbnail_url')->nullable();
    $table->text('embed_html')->nullable();
    $table->enum('status', ['published', 'hidden'])->default('published');
    $table->boolean('is_featured')->default(false);
    $table->timestamps();
    $table->softDeletes();

    // Índice compuesto para filtros de galería (RNF-03)
    $table->index(['category', 'status', 'deleted_at', 'created_at'],
                  'artworks_gallery_index');
});
```

---

## BLOQUE C — Rellenar la migración: `create_collections_table`

```php
Schema::create('collections', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name', 150);
    $table->text('description')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

---

## BLOQUE D — Rellenar la migración: `create_artwork_collection_table` (Tabla Pivot)

```php
Schema::create('artwork_collection', function (Blueprint $table) {
    $table->foreignId('artwork_id')
          ->constrained()
          ->cascadeOnDelete();
    $table->foreignId('collection_id')
          ->constrained()
          ->cascadeOnDelete();

    // Evita duplicados en la tabla pivot
    $table->unique(['artwork_id', 'collection_id']);
});
```

> [!NOTE]
> Esta tabla pivot NO tiene columna `id` ni timestamps porque es una relación pura many-to-many.

---

## BLOQUE E — Rellenar la migración: `create_bookmarks_table`

```php
Schema::create('bookmarks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
    $table->timestamps();

    // Un usuario no puede guardar la misma obra dos veces
    $table->unique(['user_id', 'artwork_id']);
});
```

---

## BLOQUE F — Rellenar la migración: `create_reports_table`

```php
Schema::create('reports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // quien reporta
    $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
    $table->string('reason', 100);           // motivo del reporte (ej: 'Enlace caído')
    $table->text('details')->nullable();     // descripción adicional
    $table->text('admin_note')->nullable();  // nota interna del administrador
    $table->enum('status', ['pending', 'resolved'])->default('pending');
    $table->timestamps();

    // Previene reportes duplicados pendientes del mismo usuario (RF-09)
    $table->unique(['user_id', 'artwork_id', 'status']);
});
```

---

## BLOQUE G — Ejecutar todas las migraciones

*Comando único que aplica las 5 nuevas tablas a la base de datos MySQL:*

```powershell
php artisan migrate
```

**Resultado esperado (las 5 tablas nuevas):**
```
  artworks ..................................... DONE
  collections .................................. DONE
  artwork_collection ........................... DONE
  bookmarks .................................... DONE
  reports ...................................... DONE
```

---

## BLOQUE H — Crear los 4 Modelos Eloquent

*El agente ejecuta `make:model` para crear los esqueletos y luego los rellena con relaciones, traits y scopes.*

### H.1 — Comandos de creación

```powershell
php artisan make:model Artwork
php artisan make:model Collection
php artisan make:model Bookmark
php artisan make:model Report
```

---

### H.2 — Modelo `Artwork` completo

**Archivo: `app/Models/Artwork.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artwork extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    protected $fillable = [
        'user_id', 'title', 'description', 'price_hint',
        'external_url', 'provider', 'category', 'render_type',
        'thumbnail_url', 'embed_html', 'status', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }

    // ── Purga automática a los 30 días (RN-04) ──────────────────────────────
    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }

    // ── Relaciones ───────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Filtra solo obras publicadas */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /** Filtra por categoría */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Buscador de texto libre multi-campo (RF-07)
     * Busca en: título, descripción y nombre del artista
     */
    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
        });
    }
}
```

---

### H.3 — Modelo `Collection` completo

**Archivo: `app/Models/Collection.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    protected $fillable = ['user_id', 'name', 'description'];

    // ── Purga automática a los 30 días (RN-04) ──────────────────────────────
    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }

    // ── Relaciones ───────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artworks()
    {
        return $this->belongsToMany(Artwork::class);
    }
}
```

---

### H.4 — Modelo `Bookmark` completo

**Archivo: `app/Models/Bookmark.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'artwork_id'];

    // ── Relaciones ───────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }
}
```

---

### H.5 — Modelo `Report` completo

**Archivo: `app/Models/Report.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'artwork_id', 'reason',
        'details', 'admin_note', 'status',
    ];

    // ── Relaciones ───────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
```

---

## BLOQUE I — Actualizar el modelo `User` con las relaciones nuevas

*El agente añade los métodos de relación al modelo `User` existente.*

**Métodos a agregar en `app/Models/User.php` (al final, antes del cierre de clase):**

```php
// ── Relaciones ───────────────────────────────────────────────────────────────
public function artworks()
{
    return $this->hasMany(Artwork::class);
}

public function collections()
{
    return $this->hasMany(Collection::class);
}

public function reports()
{
    return $this->hasMany(Report::class);
}

public function bookmarks()
{
    return $this->belongsToMany(Artwork::class, 'bookmarks')
                ->withTimestamps();
}
```

---

## BLOQUE J — Programar la purga automática en el scheduler

*El agente añade el comando `model:prune` al archivo de consola para que las obras/colecciones borradas se purguen automáticamente a los 30 días (RN-04).*

**Archivo: `routes/console.php`** — agregar al final:

```php
use Illuminate\Support\Facades\Schedule;

// Purga automática de SoftDeletes a 30 días (RN-04)
Schedule::command('model:prune')->daily();
```

---

## BLOQUE K — Verificación automática del agente en Tinker

*Comandos que el agente ejecuta para confirmar que todo está correcto antes de entregar a revisión humana.*

```powershell
# 1. Verificar que las tablas nuevas existen
php artisan tinker --execute="echo implode(', ', Schema::getTableListing());"

# 2. Verificar el scope Search genera SQL correcto
php artisan tinker --execute="echo App\Models\Artwork::search('test')->toSql();"

# 3. Verificar relación pivot
php artisan tinker --execute="echo (new App\Models\Artwork)->collections()->getTable();"

# 4. Verificar el scope de purga
php artisan tinker --execute="echo App\Models\Artwork::prunable()->toSql();"
```

---

## ✅ CHECKLIST DE VERIFICACIÓN HUMANA

### 🗄️ 1. Verificar la Base de Datos en HeidiSQL

1. Abrir **HeidiSQL** → conectarse a la BD `artfolio`.
2. Presionar **F5** para recargar.
3. Verificar que existen las siguientes **11 tablas** en total:

| Tabla | Descripción | Etapa |
|---|---|---|
| `migrations` | Control de versiones de BD | base |
| `users` | Usuarios con campos extendidos | Etapa 1 |
| `sessions` | Sesiones de usuarios | Etapa 1 |
| `jobs` / `job_batches` / `failed_jobs` | Colas de trabajo | Etapa 1 |
| `cache` / `cache_locks` | Caché de BD | Etapa 1 |
| `password_reset_tokens` | Recuperación de contraseña | Etapa 1 |
| **`artworks`** | Obras publicadas | **Etapa 2** |
| **`collections`** | Colecciones de artistas | **Etapa 2** |
| **`artwork_collection`** | Tabla pivot many-to-many | **Etapa 2** |
| **`bookmarks`** | Favoritos privados | **Etapa 2** |
| **`reports`** | Reportes de obras | **Etapa 2** |

4. Hacer clic en `artworks` → pestaña **"Columnas"** y verificar:
   - Existe la columna `category` de tipo `ENUM` con valores `3d, video, audio, image, document`
   - Existe la columna `deleted_at` (SoftDeletes)
   - Existe la columna `is_featured` de tipo `TINYINT(1)` (boolean)

5. Hacer clic en `artworks` → pestaña **"Índices"** y confirmar el índice **`artworks_gallery_index`**.

6. Hacer clic en `artwork_collection` → confirmar que **NO tiene columna `id`** ni `created_at`.

7. Hacer clic en `bookmarks` → pestaña **"Índices"** → confirmar índice único en `user_id, artwork_id`.

8. Hacer clic en `reports` → pestaña **"Índices"** → confirmar índice único en `user_id, artwork_id, status`.

---

### 🌐 2. Verificación en el navegador

1. Abrir `http://localhost:8000`.
2. Iniciar sesión como administrador → ir a `/admin`. **No debe haber errores.**
3. Ir a `/profile`. **No debe haber errores.**
4. Cerrar sesión. **No debe haber errores.**

> En esta etapa no aparece ninguna interfaz nueva visible para el usuario. El trabajo es 100% de capa de datos.

---

### 🐛 3. Diagnóstico de errores comunes

| Síntoma | Causa probable | Solución |
|---|---|---|
| `Connection refused` al ejecutar `migrate` | MySQL no corre | Abrir Laragon y activar MySQL |
| `Table artworks already exists` | Migración ejecutada dos veces | Verificar `migrate:status`; hacer rollback si es necesario |
| `Class App\Models\Collection not found` | Autoload no actualizado | Ejecutar `composer dump-autoload` |
| `Declaration of prunable() conflicts` | Uso incorrecto del trait | Confirmar que se usa `MassPrunable`, no `Prunable` |
| Error en tabla pivot sin `id` | Eloquent intenta usar `id` por defecto | Verificar que la relación usa `belongsToMany` y no `hasMany` |

---

## 📋 Resumen de Archivos por Acción

| Archivo | Acción | Comanda artisan |
|---|---|---|
| `database/migrations/[ts]_create_artworks_table.php` | **Nuevo** | `make:migration` |
| `database/migrations/[ts]_create_collections_table.php` | **Nuevo** | `make:migration` |
| `database/migrations/[ts]_create_artwork_collection_table.php` | **Nuevo** | `make:migration` |
| `database/migrations/[ts]_create_bookmarks_table.php` | **Nuevo** | `make:migration` |
| `database/migrations/[ts]_create_reports_table.php` | **Nuevo** | `make:migration` |
| `app/Models/Artwork.php` | **Nuevo** | `make:model` |
| `app/Models/Collection.php` | **Nuevo** | `make:model` |
| `app/Models/Bookmark.php` | **Nuevo** | `make:model` |
| `app/Models/Report.php` | **Nuevo** | `make:model` |
| `app/Models/User.php` | Modificar (4 relaciones nuevas) | — |
| `routes/console.php` | Modificar (schedule de purga) | — |

**Total: 5 migraciones nuevas · 4 modelos nuevos · 2 archivos modificados**  
**Sin pasos intermedios humanos — el agente ejecuta todo de corrido.**
