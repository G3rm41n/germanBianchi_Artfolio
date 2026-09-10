# 🎨 ETAPA 3 — Plan de Implementación Detallado
## Artfolio: Gestión de Obras (CRUD del Artista y EmbedService)

---

## Objetivo de la Etapa

Habilitar a los artistas para que puedan gestionar sus propias obras. Esto implica crear el sistema para subir (enlazar), visualizar, editar y eliminar obras de forma lógica. El corazón de esta etapa es el `EmbedService`, responsable de aplicar la política de **Lista Blanca Estricta** (RF-02) y extraer los metadatos de las URLs externas.

---

## Estado de Partida (Etapas 1 y 2 completadas ✅)

| Elemento | Estado |
|---|---|
| Esquema de BD completo (tablas y relaciones) | ✅ Listo |
| Modelos Eloquent completos | ✅ Listos |
| Configuración de SoftDeletes y Prunable | ✅ Listos |
| Lógica de subida y validación de URLs | ❌ **Por crear** |
| Controlador de Obras (`ArtworkController`) | ❌ **Por crear** |
| Servicio de extracción (`EmbedService`) | ❌ **Por crear** |
| Vistas (index, create, edit) con Tailwind | ❌ **Por crear** |

---

## BLOQUE A — Creación del `EmbedService` (Core de Negocio)

*El agente creará un servicio dedicado a procesar y validar las URLs externas.*

**Archivo: `app/Services/EmbedService.php`**
- Implementará la **RN-01** (Lista Blanca Estricta): Validación mediante regex o parsing de dominio para aceptar únicamente: `sketchfab.com, artstation.com, youtube.com, youtu.be, vimeo.com, soundcloud.com, spotify.com, pinterest.com, pin.it, deviantart.com, drive.google.com, docs.google.com`.
- Bloqueará dominios no permitidos y redes locales (anti-SSRF, RNF-02).
- Analizará la URL para determinar la `category` (video, 3d, audio, image, document) y `provider`.
- Generará un `embed_html` seguro (con `sandbox="allow-scripts allow-same-origin allow-presentation"` según RNF-04) dependiendo del proveedor.

---

## BLOQUE B — Creación del Form Request (`ArtworkRequest`)

*El agente ejecutará `php artisan make:request ArtworkRequest` y programará la validación HTTP.*

**Archivo: `app/Http/Requests/ArtworkRequest.php`**
- Asegurará que la `external_url` sea obligatoria, sea una URL válida y pertenezca a la lista blanca.
- Asegurará que el título sea obligatorio (max 255).
- Validación de que `price_hint` (opcional) no exceda la longitud.

---

## BLOQUE C — Creación del `ArtworkController` (Gestión del Artista)

*El agente ejecutará `php artisan make:controller ArtworkController` y programará el flujo de datos.*

**Archivo: `app/Http/Controllers/ArtworkController.php`**
- `index()`: Obtiene las obras del usuario autenticado (`auth()->user()->artworks()->latest()->get()`) y devuelve la vista de listado.
- `create()`: Devuelve el formulario de nueva obra.
- `store()`: Usa el `ArtworkRequest`. Pasa la URL al `EmbedService` para extraer metadatos. Guarda la obra en BD asignándola al artista.
- `edit(Artwork $artwork)`: Verifica propiedad (usando Policy manual o `abort_if`). Retorna vista de edición.
- `update()`: Actualiza título, descripción, precio y estado (publicado/oculto). La URL externa por el momento será de solo lectura una vez creada.
- `destroy(Artwork $artwork)`: Elimina lógicamente la obra (`delete()`), que luego será purgada por el sistema a los 30 días.

---

## BLOQUE D — Actualización del Enrutamiento

*El agente añadirá las rutas protegidas al archivo principal.*

**Archivo: `routes/web.php`**
- Se añadirá un recurso `Route::resource('artworks', ArtworkController::class)` bajo el grupo con middleware `['auth', 'verified']`.
- Esto proveerá automáticamente rutas como `/artworks/create`, `/artworks` (index), etc.

---

## BLOQUE E — Construcción de Interfaces Gráficas (Vistas Blade)

*El agente creará 3 nuevas vistas utilizando la estética moderna y Tailwind CSS de Artfolio.*

1. **`resources/views/artworks/index.blade.php` (Mis Obras):**
   - Una grilla estilizada (estética premium, glassmorphism sutil) que muestre las obras del usuario, con miniaturas/iconos según la categoría.
   - Botón para "Añadir Nueva Obra".
   - Acciones de Editar y Enviar a Papelera.

2. **`resources/views/artworks/create.blade.php`:**
   - Formulario elegante para insertar Título, URL Externa, Precio orientativo, Descripción.
   - Feedback visual sobre qué dominios están permitidos.

3. **`resources/views/artworks/edit.blade.php`:**
   - Similar a Create, pero para actualizar estado (Oculto / Publicado) y textos.

---

## BLOQUE F — Integración del Dashboard del Artista

*El agente conectará el nuevo módulo con el dashboard existente.*

**Archivo: `resources/views/dashboard.blade.php`**
- Se reemplazará el botón deshabilitado de "Publicar Obra (Próximamente)" por un enlace real hacia `route('artworks.create')`.
- Se añadirá un botón para ver la galería privada "Mis Obras" que apunte a `route('artworks.index')`.

---

## ✅ CHECKLIST DE VERIFICACIÓN HUMANA

Cuando el agente IA termine de implementar los bloques A-F, el humano deberá realizar estas pruebas desde el navegador (asumiendo `php artisan serve` corriendo):

1. **Prueba de Creación Exitosa:**
   - Entra como artista al Dashboard.
   - Clic en "Publicar Obra".
   - Ingresa un título y un enlace permitido de YouTube (ej. `https://www.youtube.com/watch?v=dQw4w9WgXcQ`).
   - Verifica que se guarda correctamente y apareces en la lista de "Mis Obras".

2. **Prueba de Lista Blanca (Rechazo):**
   - Intenta crear otra obra, pero usando una URL de un sitio no permitido (ej. `https://tiktok.com/video123` o `http://localhost/script`).
   - El formulario debe rechazar la URL y mostrar un mensaje de error validando la lista blanca.

3. **Prueba de Edición y Eliminación (Soft Delete):**
   - Edita el título de la obra que creaste.
   - Cambia su estado a "Oculto".
   - Verifica que los cambios se reflejan en la lista.
   - Pulsa "Eliminar". La obra debe desaparecer del listado (pero si revisas la tabla `artworks` en HeidiSQL, verás que el registro sigue ahí, pero con una fecha en `deleted_at`).

4. **Verificación de Seguridad:**
   - Intenta acceder a `http://localhost:8000/artworks/1/edit` estando deslogueado (debe mandarte al login).
   - Intenta acceder a la edición de una obra de otro usuario (deberías recibir un error 403 No Autorizado).

---

## 📋 Resumen de Acción del Agente

| Archivo / Comando | Acción Principal |
|---|---|
| `EmbedService.php` | Nuevo servicio de parsing y Whitelist |
| `make:request ArtworkRequest` | Validación de reglas de dominio |
| `make:controller ArtworkController` | Controladores de CRUD y Ownership |
| `routes/web.php` | Añadir `Route::resource` |
| `artworks/index.blade.php` | Vista grilla de obras propias |
| `artworks/create.blade.php` | Formulario de subida de URLs |
| `artworks/edit.blade.php` | Edición y cambio de estado |
| `dashboard.blade.php` | Habilitar enlaces de gestión |

**¿Estás de acuerdo con el alcance de esta Etapa 3?** Si respondes "aprobado", procederé con la escritura y ejecución del código bloque por bloque.
