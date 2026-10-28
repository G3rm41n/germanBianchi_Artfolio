# Plan de Implementación: Etapa 8 — Panel de Administración Integral y Sistema de Reportes

## 1. Contexto y Objetivos de la Etapa
Con las etapas de gestión del artista (perfil, catálogo, colecciones, extractores multimedia, papelera y contacto) completamente operativas, la **Etapa 8** dota a la plataforma del **Panel de Control del Administrador (Backoffice)** y del **Sistema de Reportes y Moderación de Contenidos**, tal como lo estipulan los requerimientos maestros **RF-09**, **RF-11**, **RF-12** y la regla de negocio **RN-02**:

1. **Sistema de Reportes Ciudadano (RF-09):** Permitir a los usuarios reportar obras desde el visor modal (opción prioritaria *"Enlace caído"*, además de copyright, spam o contenido inapropiado), con prevención estricta de reportes duplicados pendientes por el mismo usuario.
2. **Moderación y Alerta Manual de Enlace Caído (RN-02):** Bandeja de reportes donde el administrador puede inspeccionar la obra, almacenar notas internas (`admin_note`), y accionar el botón de alerta manual que oculta la obra automáticamente (`status = 'hidden'`) y encola un correo informativo al artista autor (`SendLinkDownAlert`).
3. **Gestión Integral de Usuarios / Artistas (RF-11):** Búsqueda de usuarios, auditoría de estados, suspensión temporal (`status = 'suspended'`), reactivación y eliminación lógica.
4. **Papelera Global de Administración (RF-11):** Visualización global de todo el contenido borrado en la plataforma (obras y colecciones de cualquier artista), permitiendo restauraciones de emergencia o purgas forzadas antes de los 30 días.
5. **Supervisión de Colas del Sistema (RNF-01):** Monitor de trabajos encolados y fallidos (`failed_jobs`), con opción de reintento o limpieza.
6. **Layout y Seguridad Administrativa:** Módulo `/admin` protegido estrictamente por middleware `EnsureIsAdmin` con interfaz dedicada, barra lateral y diseño premium en modo claro y oscuro.

---

## 2. Requerimientos y Reglas de Negocio Aplicables

| Código | Requerimiento / Regla | Aplicación en la Etapa 8 |
| :--- | :--- | :--- |
| **RF-09** | **Reporte de Obras y Alerta Manual** | Botón de reporte en visor modal con opción "Enlace caído". Prevención de duplicados pendientes en tabla `reports`. Alerta manual enviada por email al autor. |
| **RF-11** | **Panel de Control del Administrador** | Módulo `/admin` con dashboard, CRUD de usuarios, moderación de reportes, supervisión de colas y papelera global. |
| **RF-12** | **Provisión de Administrador vía Seeder** | Administrador provisionado desde variables `.env` (`ADMIN_EMAIL`, etc.), verificado y con permisos de superadmin. |
| **RN-02** | **Moderación y Alerta de Enlace Caído** | Al validar un reporte de enlace caído, el Administrador activa el envío de un correo de plantilla al autor y la publicación pasa automáticamente a estado `hidden`. |
| **RN-06** | **Privacidad de Guardados** | Las listas de favoritos se mantienen privadas; el administrador supervisa métricas globales sin vulnerar la privacidad de los usuarios. |
| **RNF-01** | **Rendimiento SMTP en Notificaciones** | Todas las alertas despachadas por el administrador hacia los artistas se procesan de forma asíncrona mediante colas de base de datos (`jobs`). |

---

## 3. Desglose Técnico por Bloques

### BLOQUE 1: Sistema de Reporte de Obras (Frontend y API)

#### 1.1. Modal de Reporte en el Visor Modal ([resources/views/components/viewer-modal.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/components/viewer-modal.blade.php))
* Añadir un botón discreto pero accesible con icono de bandera/alerta: *"Reportar Obra"*.
* Si el usuario no está autenticado, pulsar el botón dispara `$dispatch('open-guest-modal')` (interceptor RF-08).
* Si está autenticado, abre un modal reactivo en Alpine.js:
  * Selector de motivo (`reason`):
    * **Enlace caído o inaccesible** (opción predeterminada).
    * Contenido inapropiado o explícito.
    * Violación de derechos de autor (Copyright).
    * Spam o publicidad engañosa.
    * Otro motivo.
  * Área de texto para detalles opcionales (`details`).
  * Envío asíncrono con feedback visual inmediato y prevención de doble clic.

#### 1.2. Controlador de Reportes de Usuario ([app/Http/Controllers/ReportController.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Http/Controllers/ReportController.php))
* Endpoint: `POST /obras/{id}/report`.
* **Prevención de Duplicados (RF-09):** Comprueba si el usuario autenticado ya posee un reporte con `status = 'pending'` para esa obra (`Report::where('user_id', auth()->id())->where('artwork_id', $id)->where('status', 'pending')->exists()`). Si existe, rechaza con mensaje: *"Ya tienes un reporte pendiente de revisión para esta obra"*.
* Crea el registro con `status = 'pending'` y devuelve respuesta JSON exitosa.

---

### BLOQUE 2: Layout Administrativo Dedicado (`layouts/admin.blade.php`)
* Crear una plantilla base profesional e independiente para la zona `/admin`:
  * **Sidebar Lateral Colapsable:**
    * Logotipo de Artfolio con badge distintivo `ADMIN`.
    * Enlaces de navegación activos:
      1. **Dashboard** (Métricas globales e indicadores).
      2. **Reportes** (Bandeja con badge numérico de tickets pendientes).
      3. **Usuarios** (Gestión de artistas y estados).
      4. **Papelera Global** (Auditoría de borrados).
      5. **Colas / Sistema** (Monitoreo de jobs y fallos).
    * Botón de retorno rápido a la **Galería Pública**.
    * Información del admin conectado y botón de cierre de sesión.
  * Soporte nativo para modo oscuro/claro y diseño 100% responsivo para móviles y tabletas.

---

### BLOQUE 3: Dashboard y Métricas Globales (`Admin\DashboardController`)
* Actualizar [app/Http/Controllers/Admin/DashboardController.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Http/Controllers/Admin/DashboardController.php) y [resources/views/admin/dashboard.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/admin/dashboard.blade.php):
  * **Tarjetas KPI principales:**
    * Total de Artistas registrados (+ conteo de suspendidos).
    * Total de Obras publicadas vs. ocultas (`hidden`).
    * Reportes pendientes de revisión (resaltado en ámbar/rojo si hay tickets abiertos).
    * Trabajos en cola y fallos en cola (`failed_jobs`).
  * **Paneles de acceso rápido:**
    * Últimos reportes recibidos con acceso directo a moderación.
    * Obras agregadas recientemente y últimas enviadas a la papelera.

---

### BLOQUE 4: Moderación de Reportes y Alerta de Enlace Caído (RN-02)

#### 4.1. Controlador `Admin\ReportController`
* `index(Request $request)`: Listado con filtros por estado (`pending`, `resolved`, `todos`), filtro por motivo (`reason`), búsqueda por título de obra o nombre del autor, y paginación.
* `show($id)`: Vista detallada del ticket con:
  * Previsualización de la obra (miniatura, proveedor, enlace externo).
  * Datos del autor y datos del usuario denunciante.
  * Motivo y descripción del reporte.
  * Campo de notas internas (`admin_note`) con guardado inmediato.
* `resolve($id)`: Marca el reporte como resuelto sin alterar la obra.
* `dismiss($id)`: Desestima el reporte si se considera infundado.
* `sendLinkDownAlert(Request $request, $id)` (**Regla RN-02**):
  1. Valida que el motivo corresponda a un problema de enlace o moderación.
  2. Cambia automáticamente el estado de la obra a **`status = 'hidden'`** para que deje de aparecer en la galería pública.
  3. Guarda la `admin_note` en el reporte y marca el ticket como `resolved`.
  4. Despacha el Job asíncrono `SendLinkDownAlert` hacia el autor de la obra.

#### 4.2. Mailable `LinkDownAlert` y Job Asíncrono `SendLinkDownAlert`
* Crear `App\Mail\LinkDownAlert`:
  * Asunto: `[Artfolio] Notificación importante: Enlace no disponible en tu obra "{Título}"`.
  * Plantilla HTML responsive ([resources/views/emails/link-down-alert.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/emails/link-down-alert.blade.php)):
    * Notificación clara de que su obra ha sido reportada con un enlace caído.
    * Aviso de que la publicación pasó a estado **Oculto** de forma preventiva.
    * Instrucciones y botón directo para acceder a la edición de la obra (`/obras/{id}/editar`) y actualizar la URL.
* Crear `App\Jobs\SendLinkDownAlert` (`ShouldQueue`):
  * Procesa el envío en segundo plano para que el panel de administración responda en < 50 ms.

---

### BLOQUE 5: Gestión de Usuarios y Artistas (`Admin\UserController`)
* Controlador [app/Http/Controllers/Admin/UserController.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Http/Controllers/Admin/UserController.php):
  * `index(Request $request)`: Lista de usuarios con buscador por nombre, email o slug, y filtro por estado (`active`, `suspended`).
  * `suspend($id)`: Cambia `status = 'suspended'`. Impide que el usuario cree o edite obras mientras esté suspendido.
  * `activate($id)`: Restaura el estado a `active`.
  * `destroy($id)`: Borrado lógico (*SoftDelete*) de la cuenta del usuario.
* Vista [resources/views/admin/users/index.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/admin/users/index.blade.php):
  * Tabla moderna con avatar, nombre, correo, fecha de registro, rol (Admin / Artista), estado y conteo de obras.
  * Botones de acción rápida con confirmación.

---

### BLOQUE 6: Papelera Global de Administración (`Admin\TrashController`)
* Controlador [app/Http/Controllers/Admin/TrashController.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Http/Controllers/Admin/TrashController.php):
  * Auditoría centralizada de todas las obras y colecciones eliminadas de cualquier usuario.
  * Acciones:
    * Restaurar obra o colección a su propietario.
    * Eliminación definitiva forzada (`forceDelete`).
    * Botón para forzar la purga global de elementos con más de 30 días (`Artisan::call('model:prune')`).
* Vista [resources/views/admin/trash/index.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/admin/trash/index.blade.php).

---

### BLOQUE 7: Monitor de Colas y Trabajos Fallidos (`Admin\QueueController`)
* Controlador [app/Http/Controllers/Admin/QueueController.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Http/Controllers/Admin/QueueController.php):
  * Visualización de trabajos pendientes en tabla `jobs`.
  * Visualización de fallos en tabla `failed_jobs` con detalle de excepción y fecha.
  * Botones para reintentar (`Artisan::call('queue:retry', ['id' => ...])`) o descartar (`failed_jobs` delete).
* Vista [resources/views/admin/queues/index.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/admin/queues/index.blade.php).

---

## 4. Archivos a Crear y Modificar

| Tipo | Archivo | Acción / Responsabilidad |
| :--- | :--- | :--- |
| **Layout** | `resources/views/layouts/admin.blade.php` *(Nuevo)* | Plantilla maestra de administración con sidebar responsivo y soporte dark mode. |
| **Controlador** | `app/Http/Controllers/ReportController.php` *(Nuevo)* | Endpoint de creación de reportes por parte de usuarios autenticados (RF-09). |
| **Controlador** | [app/Http/Controllers/Admin/DashboardController.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Http/Controllers/Admin/DashboardController.php) | Enriquecer métricas de reportes, obras, colas y actividad reciente. |
| **Controlador** | `app/Http/Controllers/Admin/ReportController.php` *(Nuevo)* | Gestión de tickets, notas del admin, resolución y alerta manual de enlace caído (RN-02). |
| **Controlador** | `app/Http/Controllers/Admin/UserController.php` *(Nuevo)* | Búsqueda, suspensión, reactivación y borrado lógico de usuarios. |
| **Controlador** | `app/Http/Controllers/Admin/TrashController.php` *(Nuevo)* | Auditoría y gestión de papelera global para administradores. |
| **Controlador** | `app/Http/Controllers/Admin/QueueController.php` *(Nuevo)* | Supervisión de colas de base de datos y gestión de `failed_jobs`. |
| **Mailable** | `app/Mail/LinkDownAlert.php` *(Nuevo)* | Correo estructurado al artista notificando el enlace caído y estado oculto (RN-02). |
| **Plantilla Correo**| `resources/views/emails/link-down-alert.blade.php` *(Nuevo)* | Plantilla HTML del aviso con enlace de edición de obra. |
| **Job de Cola** | `app/Jobs/SendLinkDownAlert.php` *(Nuevo)* | Despacho asíncrono de la alerta al autor para evitar bloqueo en panel admin. |
| **Vista** | [resources/views/components/viewer-modal.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/components/viewer-modal.blade.php) | Incorporar botón "Reportar Obra" y modal interactivo de reporte. |
| **Vistas Admin** | `resources/views/admin/reports/*.blade.php` *(Nuevos)* | Bandeja de moderación y ficha detallada de reporte. |
| **Vistas Admin** | `resources/views/admin/users/index.blade.php` *(Nuevo)* | Tabla de gestión de usuarios con filtros y acciones. |
| **Vistas Admin** | `resources/views/admin/trash/index.blade.php` *(Nuevo)* | Panel de papelera global y purga manual. |
| **Vistas Admin** | `resources/views/admin/queues/index.blade.php` *(Nuevo)* | Monitor de colas y tabla de fallos. |
| **Rutas** | [routes/web.php](file:///c:/laragon/www/germanBianchi_artfolio/routes/web.php) | Grupo completo `/admin/*` con `EnsureIsAdmin` y ruta `POST /obras/{id}/report`. |

---

## 5. Matriz de Pruebas y Validación

| Escenario de Prueba | Acción Realizada | Resultado Esperado | Criterio de Éxito |
| :--- | :--- | :--- | :--- |
| **Reporte de Obra (Usuario)** | Pulsar "Reportar" en visor modal con motivo "Enlace caído". | Se crea registro en `reports` con estado `pending`. | HTTP 200 / JSON de éxito |
| **Prevención de Duplicados (RF-09)** | Intentar reportar la misma obra por segunda vez. | La solicitud es rechazada indicando que ya existe un reporte pendiente. | Bloqueo validado |
| **Acceso Restringido a /admin** | Usuario estándar no admin intenta ingresar a `/admin`. | El middleware `EnsureIsAdmin` bloquea el acceso con HTTP 403. | Seguridad garantizada |
| **Alerta Manual Enlace Caído (RN-02)** | Admin pulsa "Enviar Alerta de Enlace Caído" en reporte. | 1. Obra pasa a `hidden`.<br>2. Reporte pasa a `resolved`.<br>3. Correo es encolado y enviado al artista con enlace de edición. | Flujo completo RN-02 verificado |
| **Suspensión de Usuario** | Admin suspende a un usuario en `/admin/users`. | El usuario cambia a `status = 'suspended'`. | Estado reflejado |
| **Papelera Global** | Admin consulta `/admin/trash`. | Visualiza obras eliminadas de todos los usuarios con opción de restauración o purga. | Auditoría completa |
| **Supervisión de Colas** | Admin ingresa a `/admin/queues`. | Se listan los jobs en proceso y el historial de fallos con opción de reintento. | Monitor activo |

---

## 6. Siguientes Pasos
Una vez aprobado este plan:
1. Implementar la funcionalidad de reporte de obras en el visor modal y el `ReportController`.
2. Crear el layout administrativo `layouts/admin.blade.php`.
3. Desarrollar el módulo de moderación de reportes con la alerta manual de enlace caído (RN-02), Job y Mailable.
4. Implementar el módulo de gestión de usuarios y la papelera global.
5. Implementar el monitor de colas y failed_jobs.
6. Ejecutar pruebas integrales con el usuario administrador y verificar captura de correos en Mailpit.
