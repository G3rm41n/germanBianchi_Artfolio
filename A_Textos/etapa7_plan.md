# Plan de Implementación: Etapa 7 — Papelera Inteligente, Favoritos Privados y Contacto Seguro Anti-Suplantación

## 1. Contexto y Objetivos de la Etapa
Tras haber consolidado la **Etapa 6** (Colecciones temáticas) y el sistema avanzado de **Extractores Multimedia** (Pinterest, DeviantArt, Sketchfab, YouTube, Spotify y Google Docs/Drive), la **Etapa 7** se enfoca en tres módulos esenciales de retención, autoservicio y comunicación del artista:

1. **Papelera de Reciclaje Inteligente (RF-10, RN-03, RN-04):** Gestión de obras y colecciones en borrado lógico (*Soft Deletes*), contador regresivo de 30 días, desvinculación automática de colecciones eliminadas al restaurar (RN-03) y purga programada (`model:prune`).
2. **Favoritos Privados y Sincronización Reactiva (RF-05, RN-06, RF-08):** Guardado sin recarga de página mediante AJAX, privacidad absoluta (sin métricas públicas), sincronización de estado entre las tarjetas de galería y el visor modal, e intercepción de visitantes no autenticados.
3. **Contacto Seguro Anti-Suplantación (RF-06, RN-05, RNF-01):** Formulario de contacto hacia el artista, datos del remitente vinculados de forma inmutable a `auth()->user()`, bloqueo de auto-contacto, encolado asíncrono en base de datos (`Database Queue Worker`) con respuesta inmediata (< 100 ms) y privacidad total del email del artista receptor (nunca expuesto en el código HTML).

---

## 2. Requerimientos y Reglas de Negocio Aplicables

| Código | Descripción Oficial | Aplicación en la Etapa 7 |
| :--- | :--- | :--- |
| **RF-05** | **Favoritos y Guardados Privados** | Guardado de creaciones en lista privada vía AJAX sin contadores públicos ni likes. |
| **RF-06** | **Contacto Seguro Anti-Suplantación** | Formulario exclusivo para autenticados; remitente tomado de `auth()->user()`, email encolado despachado únicamente al artista. |
| **RF-08** | **Modal Interceptor para Visitantes** | Si un invitado intenta guardar en favoritos o contactar, una ventana emergente en Alpine.js solicita iniciar sesión o registrarse sin perder el contexto. |
| **RF-10** | **Papelera y Restauración Independiente** | Retención de 30 días en `/papelera`. Restauración limpia sin errores de clave foránea. |
| **RN-03** | **Restauración Desvinculada de Colección** | Si se restaura una obra y su colección original ya no existe o está en papelera, la obra queda activa en el portafolio general sin colección asignada. |
| **RN-04** | **Retención a 30 Días** | Obras o colecciones con más de 30 días en papelera se eliminan de forma permanente con `model:prune`. |
| **RN-05** | **Anti-Suplantación y Auto-Contacto** | Remitente inmutable al usuario autenticado. No es posible auto-enviarse mensajes de contacto. |
| **RN-06** | **Privacidad de Guardados** | Las listas de favoritos son estrictamente privadas. |
| **RNF-01** | **Rendimiento SMTP** | Desacoplamiento del envío de correo vía colas MySQL (`QUEUE_CONNECTION=database`). Respuesta HTTP $\le$ 100 ms. |

---

## 3. Desglose Técnico por Bloques

### BLOQUE A: Papelera de Autoservicio y Restauración Inteligente

#### A1. Soporte Dual: Obras y Colecciones en Papelera
* Actualmente `TrashController@index` solo consulta obras eliminadas. Se ampliará para incluir también las **colecciones eliminadas** mediante pestañas o secciones claras:
  * Pestaña 1: **Obras en papelera** (`Artwork::onlyTrashed()`).
  * Pestaña 2: **Colecciones en papelera** (`Collection::onlyTrashed()`).

#### A2. Contador Regresivo de 30 Días (UI)
* En cada elemento de la papelera se calcularán los días restantes de gracia:
  ```php
  $diasRestantes = max(0, 30 - (int) $item->deleted_at->diffInDays(now()));
  ```
* Se agregará un badge visual dinámico según urgencia:
  * Verde ($\ge 15$ días restantes): "28 días restantes".
  * Amarillo ($6$ a $14$ días): "9 días restantes".
  * Rojo ($\le 5$ días): "¡3 días antes de purga definitiva!".

#### A3. Regla RN-03: Restauración Desvinculada
* Al restaurar una obra en `TrashController@restore($id)`:
  1. Se localiza la obra `onlyTrashed()`.
  2. Se inspeccionan las colecciones asociadas en la tabla pivot `artwork_collection`.
  3. Si la colección asociada fue borrada (`trashed()`) o ya no existe, se desvincula de la pivot table (`$artwork->collections()->detach(...)`).
  4. La obra se restaura (`$artwork->restore()`) quedando inmediatamente visible en el portafolio público del artista sin colecciones huérfanas.
* Al restaurar una colección: se restaura su registro `Collection` sin forzar la restauración de sus obras individuales (cada obra mantiene su propio ciclo de vida).

#### A4. Vaciado Rápido y Eliminación Definitiva
* Añadir acción para **Vaciar Papelera** completa (con confirmación modal/diálogo de seguridad) o eliminación individual forzada (`forceDelete`).

---

### BLOQUE B: Favoritos Privados y Sincronización Reactiva

#### B1. Vista Optimizada de Favoritos
* Perfeccionar [resources/views/bookmarks/index.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/bookmarks/index.blade.php):
  * Grid responsivo con componentes `<x-artwork-card>`.
  * Filtro rápido o buscador dentro de favoritos.
  * Estado vacío premium con llamada a la acción para explorar la galería pública.

#### B2. Sincronización Bidireccional de Estado (Alpine.js)
* Garantizar que cuando el usuario pulse el botón de favorito dentro del **Visor Modal** (`viewer-modal.blade.php`):
  * Se ejecute la petición AJAX contra `/obras/{id}/bookmark`.
  * Se emita el evento global `window.$dispatch('bookmark-updated', { id, status })`.
  * Todas las tarjetas visibles en la página actualicen su icono de corazón (relleno rojo vs contorno gris) al instante y sin recargar.

#### B3. Modal Interceptor para Visitantes (RF-08)
* Si un visitante no autenticado intenta hacer clic en el corazón de favoritos:
  * El botón dispara `$dispatch('open-guest-modal')`.
  * Se despliega el modal interceptor con botones de *"Iniciar Sesión"* y *"Registrarse"* preservando la URL actual como `intended`.

---

### BLOQUE C: Contacto Seguro Anti-Suplantación

#### C1. Estructura de Controladores y Rutas
* **Ruta:** `POST /artista/{slug}/contact` -> `ContactController@send`.
* Protegida por middleware `auth` (los visitantes que intenten contactar verán el modal interceptor de visitantes RF-08).

#### C2. Validación y Regla Anti-Auto-Contacto (RN-05)
* El formulario recibe:
  * `subject` (`required`, `string`, `max:150`): Motivo del contacto (ej. "Comisión / Encargo", "Consulta técnica", "Propuesta de colaboración", "Otro").
  * `message` (`required`, `string`, `min:10`, `max:2500`): Contenido del mensaje.
* **Seguridad Anti-Suplantación:**
  * El remitente se extrae de forma obligatoria e inmutable desde `auth()->user()`. No existe ningún campo modificable en la petición para falsear el remitente.
  * **Regla RN-05:** Se verifica si `auth()->id() === $targetArtist->id`. Si coincide, se bloquea con error HTTP 403 (*"No puedes enviarte un mensaje de contacto a ti mismo"*).
  * En el frontend, si el artista está viendo su propio perfil público (`/artista/{slug}`), el botón de contacto se oculta automáticamente.

#### C3. Mailable y Plantilla de Correo
* Crear `App\Mail\ContactArtist`:
  * Asunto: `[Artfolio] Nuevo mensaje de contacto de {Sender Name}: {Subject}`.
  * Encabezado `Reply-To`: Configurado con el correo del remitente (`auth()->user()->email`) para que el artista receptor pueda responderle directamente desde su cliente de correo con un solo clic.
  * Plantilla Blade responsive:
    * Nombre y enlace al perfil del remitente en Artfolio.
    * Motivo del contacto destacado.
    * Mensaje en bloque tipográfico limpio.
    * Nota de seguridad de la plataforma.

#### C4. Procesamiento Asíncrono en Cola (RNF-01)
* Crear `App\Jobs\SendContactEmail`:
  * Implementa `ShouldQueue`.
  * Recibe los datos del remitente, del destinatario, asunto y mensaje.
  * Se despacha a la cola de base de datos (`jobs`) mediante `SendContactEmail::dispatch(...)`.
  * **Tiempo de respuesta:** La respuesta HTTP al usuario es instantánea (< 80 ms), devolviendo un feedback positivo mediante mensaje flash o JSON.
  * El worker de colas de Laragon procesa el envío sin bloquear la navegación del usuario.

#### C5. Modal de Contacto Reactivo en el Perfil del Artista
* En [resources/views/profile/show.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/profile/show.blade.php):
  * Botón elegante **"Contactar al Artista"** con icono de sobre.
  * Modal en Alpine.js:
    * Cabecera con el avatar y nombre del artista destinatario.
    * Tarjeta de remitente bloqueada: *"Enviando como: Germán Bianchi (tu_correo@...)"*.
    * Selector de motivo + área de texto para el mensaje.
    * Indicador de carga *"Enviando mensaje..."* con botón deshabilitado durante el submit para evitar envíos dobles.

---

## 4. Archivos a Crear y Modificar

| Tipo | Archivo | Acción / Propósito |
| :--- | :--- | :--- |
| **Controlador** | [app/Http/Controllers/TrashController.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Http/Controllers/TrashController.php) | Aplicar RN-03 (desvinculación de colección eliminada), soporte de colecciones y vaciado. |
| **Controlador** | `app/Http/Controllers/ContactController.php` *(Nuevo)* | Validar solicitud, aplicar RN-05 (anti-autocontacto) y despachar el Job encolado. |
| **Mailable** | `app/Mail/ContactArtist.php` *(Nuevo)* | Definir el correo con `replyTo` del remitente y vista HTML/texto. |
| **Plantilla Correo**| `resources/views/emails/contact-artist.blade.php` *(Nuevo)* | Diseño limpio y adaptable del correo de contacto recibido por el artista. |
| **Job de Cola** | `app/Jobs/SendContactEmail.php` *(Nuevo)* | Despachar el envío asíncrono a la tabla `jobs` de MySQL. |
| **Vista** | [resources/views/trash/index.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/trash/index.blade.php) | Contador regresivo de 30 días, badges por color y pestañas Obras / Colecciones. |
| **Vista** | [resources/views/profile/show.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/profile/show.blade.php) | Botón "Contactar" y modal interactivo de contacto con Alpine.js. |
| **Vista** | [resources/views/components/viewer-modal.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/components/viewer-modal.blade.php) | Verificación de sincronización de favoritos y modal interceptor. |
| **Rutas** | [routes/web.php](file:///c:/laragon/www/germanBianchi_artfolio/routes/web.php) | Añadir ruta `POST /artista/{slug}/contact` y rutas ampliadas de papelera. |

---

## 5. Matriz de Pruebas y Validación

| Caso de Prueba | Acción Realizada | Resultado Esperado | Criterio de Éxito |
| :--- | :--- | :--- | :--- |
| **Contador de Papelera** | Enviar obra a papelera y abrir `/papelera`. | Muestra badge con cálculo exacto de días restantes (ej. "30 días restantes"). | Visual y reactivo |
| **Regla RN-03 (Desvinculación)** | Borrar colección, luego restaurar una obra de esa colección. | La obra se restaura sin errores y sin colección asociada; no produce errores SQL. | Base de datos limpia |
| **Purga de 30 días (RN-04)** | Ejecutar `php artisan model:prune`. | Registros con `deleted_at <= now() - 30 days` se eliminan permanentemente. | Verificado vía Artisan |
| **Favoritos Reactivos** | Pulsar corazón en modal y cerrar. | La tarjeta en el fondo refleja el cambio de estado inmediatamente. | Sin recarga de página |
| **Anti-Auto-Contacto (RN-05)** | Visitar el perfil propio (`/artista/{mi-slug}`). | El botón de contactar no se muestra; si se fuerza por POST, retorna HTTP 403. | Bloqueo garantizado |
| **Contacto Asíncrono (RF-06)** | Enviar mensaje a otro artista. | Respuesta HTTP < 100 ms; ticket en tabla `jobs`; correo capturado en Mailpit (puerto 1025). | Encolado verificado |
| **Privacidad del Email** | Inspeccionar código fuente de la página del artista. | El email del destinatario no aparece en ningún atributo HTML ni script JS. | Cero fugas de datos |

---

## 6. Siguientes Pasos
Una vez aprobado este plan:
1. Implementar la lógica de restauración con desvinculación (RN-03), soporte de colecciones y contador regresivo en la Papelera.
2. Afinar la sincronización de favoritos y el modal interceptor para invitados.
3. Crear el Job `SendContactEmail`, el Mailable `ContactArtist` y el `ContactController`.
4. Añadir el botón y modal de contacto en el perfil público del artista.
5. Ejecutar la suite de pruebas local con Mailpit y colas de base de datos.
