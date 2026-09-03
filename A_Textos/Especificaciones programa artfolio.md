# **Especificaciones programa art v1**

Especificación de Requerimientos de Software (ERS / SRS \- Versión 1.0.0 Definitiva)

Documento maestro formal de ingeniería de software para el desarrollo integral de la plataforma **Artfolio**. Desarrollado bajo el ecosistema **PHP 8.2+ con Laravel 11**, autenticación **Laravel Breeze**, vistas Blade con **Tailwind CSS y Alpine.js**, procesamiento asíncrono de correos con **Database Queue Worker**, paginación numérica tradicional, avatar por URL externa con herramienta interactiva de arrastre (drag-to-crop), modal reactivo adaptativo por categoría, borrado lógico (Soft Deletes), seguridad anti-suplantación en contacto, **Modal Interceptor para Visitantes**, **Sistema de Reportes y Alerta Manual de Enlace Caído**, **Lista Blanca Estricta de Dominios**, **Buscador de Texto Libre Multi-Campo**, **Papelera de Autoservicio y Restauración Independiente**, y un **Panel de Administración Integral** para gestión y moderación global en **MySQL 8.0 sobre Laragon**.

## **1\. Ficha Técnica y Control de Versiones**

*Esta sección representa la ficha técnica del motor, detallando los cimientos tecnológicos y componentes base sobre los cuales se construye el sistema.*

| Atributo | Detalle del Proyecto   |
| :---- | :---- |
| **Nombre del Software** | Artfolio \- Plataforma de Exhibición y Enlace Multimedia |
| **Versión del Documento** | 8.2.0 (Línea Base Definitiva Consolidada para Construcción) |
| **Fecha de Formalización** | 12 de Agosto de 2026 |
| **Stack Backend & Autenticación** | PHP 8.2+, Laravel 11, Laravel Breeze (Blade Stack), Eloquent ORM con SoftDeletes y Prunable |
| **Stack Frontend & Interactividad** | Plantillas Blade, Tailwind CSS (Vite), Alpine.js (modales adaptativos, intercepción de invitados y drag-to-crop) |
| **Colas y Procesamiento Asíncrono** | Database Queues (QUEUE\_CONNECTION=database) con worker activo para envío de correo solo al artista |
| **Entorno Local & Base de Datos** | Laragon (Nginx/Apache \+ PHP 8.2+ \+ MySQL 8.0 \+ Mailpit local) |

### **Historial de Versiones**

| Versión | Fecha | Descripción de Modificaciones | Responsable   |
| :---: | :---: | :---- | :---- |
| 0.0.1 \- 0.0.4 | 12/08/2026 | Definición inicial, modelo Zero-Blob, stack Laravel/Laragon, Breeze, colas y paginación. | Equipo de Producto |
| 0.0.5 \- 0.0.6 | 12/08/2026 | Diccionario relacional, Panel de Administración CRUD, avatar drag-to-crop y modales adaptativos. | Arquitectura |
| 0.0.7 \- 0.0.8 | 12/08/2026 | Integración: Modal Interceptor para visitantes, Sistema de Reportes con opción "Enlace caído" y alerta manual por email, Lista Blanca Estricta de dominios, Provisión de Administrador vía DatabaseSeeder desde .env, Papelera de autoservicio de 30 días, y Buscador de texto libre multi-campo. | Equipo de Ingeniería & QA |
| 0.0.9 1.0.0 | 12/08/2026 12/08/2026 | **Ajustes de Consolidación:** Optimización de Query para buscador de texto libre con índices FullText/Compuestos, adición de admin\_note a reportes y aclaraciones de estado visual de obras. **Consolidación V8.2.0:** Integración de reCAPTCHA, lógica de refresco de sesión post-login, sistema de obras destacadas y despliegue de configuración operativa. | Equipo de Ingeniería Equipo de Ingeniería |

## **2\. Propósito y Alcance del Producto**

*Esta sección funciona como el mapa de navegación del proyecto, definiendo con claridad el objetivo principal y sus límites operativos.*

### **2.1. Propósito y Visión General**

Artfolio es una aplicación web en **Laravel 11** que centraliza y exhibe el trabajo de artistas multidisciplinarios mediante la **inserción de enlaces externos bajo una lista blanca estricta y extracción de metadatos oEmbed / OpenGraph**. Ofrece un buscador por texto libre y pestañas de disciplina, permite a los artistas gestionar sus obras, colecciones y papelera temporal, y brinda al Administrador un panel integral para moderar reportes de enlaces caídos, notificar manualmente a los autores y gestionar cualquier dato de la plataforma.

### **2.2. Alcance (In-Scope vs. Out-of-Scope)**

| Dentro del Alcance (In-Scope) | Fuera del Alcance (Out-of-Scope)   |
| :---- | :---- |
| Autenticación con **Laravel Breeze** (todo usuario registrado es Artista). Provisión de Administrador vía DatabaseSeeder.php desde variables del archivo .env. **Lista Blanca Estricta de Dominios:** Sketchfab, ArtStation, YouTube, Vimeo, Spotify, SoundCloud, Pinterest, DeviantArt y Google Docs. **Buscador de Texto Libre:** Coincidencias en tiempo real sobre título, descripción y nombre del artista con paginación preservada (Scope en Eloquent). **Modal Interceptor de Visitantes (Alpine.js):** Ventana emergente ligera que solicita login/registro al intentar acciones restringidas sin recargar la página. **Reporte de Obras y Moderación:** Botón con opción "Enlace caído" que crea tickets en /admin/reports y permite al admin enviar alertas manuales al artista y almacenar notas. **Papelera de Autoservicio y Restauración:** Retención de 30 días en /perfil/papelera; si la colección fue borrada, la obra se restaura libremente en el catálogo general. Avatar externo con **arrastre interactivo (\*drag-to-crop\*) y zoom** en Alpine.js. Visor modal adaptativo (16:9 para 3D/Video, barra compacta para Audio, visor vertical para PDFs, e imagen escalada con botón **"Ver publicación original"**). Formulario de contacto seguro y anti-suplantación (remitente ligado a auth()-\>user()), encolado y enviado **únicamente al artista**. | Almacenamiento local de archivos pesados (arquitectura estricta *Zero-Blob*). Pasarelas de pago o cobro dentro de la plataforma (valores solo referenciales). Chat en tiempo real o mensajería interna instantánea. Marcas de agua forzadas o descargas locales directas. Métricas públicas de vanidad (conteo de vistas, likes públicos o comentarios abiertos). |

## **3\. Matriz de Actores y Roles**

*Esta sección describe el sistema de llaves de acceso, especificando quién puede entrar a cada zona y qué acciones tiene permitidas. Es el organigrama de llaves: Visitante (solo mirar), Artista (crear y gestionar su oficina), Administrador (llaves maestras de todo el edificio).*

| Rol / Actor | Tipo | Responsabilidades en el Sistema | Permisos Principales   |
| :---- | :---: | :---- | :---- |
| **Visitante (Guest)** | No Autenticado | Explora galerías, ejecuta búsquedas, filtra por categoría y abre visores. Al intentar interactuar, se le despliega el modal de inicio de sesión. | Solo lectura. Bloqueo con modal ante intentos de publicar, guardar, reportar o contactar. |
| **Artista (Usuario)** | Registrado (Breeze) | Publica obras de sitios permitidos, ajusta su avatar arrastrando la imagen, organiza colecciones, reporta enlaces caídos, gestiona su papelera y contacta a otros artistas. | CRUD de creaciones propias, papelera personal, guardados privados y formulario de contacto. |
| **Administrador** | Interno (is\_admin=1) | Creado por Seeder. Controla el panel /admin, modera reportes, despacha alertas manuales por correo a los artistas, gestiona usuarios y papelera global. | Acceso total (Superadmin) protegido por middleware can:admin. |

## **4\. Requerimientos Funcionales (RF)**

*Esta sección constituye la lista de funciones del sistema, detallando todo lo que la aplicación debe ser capaz de hacer para sus usuarios.*

| ID | Requerimiento | Descripción del Comportamiento | Prioridad | Criterio de Aceptación   |
| :---: | :---- | :---- | :---: | :---- |
| **RF-01** | **Perfil y Avatar Drag-to-Crop** | El artista introduce una URL externa de avatar. Un visor circular en Alpine.js permite arrastrar la imagen ($X/Y$) y graduar el zoom. Las coordenadas se persisten en users y se aplican mediante estilos CSS. | **Must** | Cero archivos binarios en el servidor; fallback visual con iniciales si la URL remota falla. |
| **RF-02** | **Publicación con Whitelist Estricta** | Valida que la URL pertenezca a la lista blanca oficial (Sketchfab, ArtStation, YouTube, Vimeo, SoundCloud, Spotify, Pinterest, DeviantArt, Google Docs) y extrae metadatos con EmbedService (timeout 4s). | **Must** | Cualquier dominio fuera de la lista blanca es rechazado con error HTTP 422; metadatos persistidos en BD. |
| **RF-03** | **Visor Modal Adaptativo** | El modal Alpine.js adapta su estructura: **3D/Video** (16:9), **Audio** (160 px), **Docs** (80vh) e **Imágenes** (con botón destacado **"Ver publicación original"**). | **Must** | Fluidez a 60 FPS; destrucción del iframe al cerrar con tecla Escape o clic exterior. |
| **RF-04** | **Colecciones Temáticas** | El artista crea y organiza colecciones temáticas asociando sus obras mediante relación Eloquent belongsToMany. | **Must** | Filtrado inmediato de obras por colección en el perfil del artista. |
| **RF-05** | **Favoritos y Guardados Privados** | Los artistas autenticados guardan creaciones en su lista privada mediante peticiones AJAX. | **Should** | Privacidad absoluta; sin contadores públicos de popularidad. |
| **RF-06** | **Contacto Seguro Anti-Suplantación** | Formulario exclusivo para autenticados. El remitente se toma de auth()-\>user(), se bloquea el auto-contacto y se encola el email en MySQL despachado **únicamente al artista**. | **Must** | Respuesta HTTP inmediata (\< 100 ms); correo del artista siempre oculto. |
| **RF-07** | **Buscador de Texto Libre y Paginación** | La galería global incluye un campo de búsqueda por texto libre (coincidencias en title, description y users.name vía Eloquent whereHas) combinado con pestañas de disciplinas y paginación numerada tradicional. | **Must** | Preservación de parámetros ?search=...\&category=...\&page=... con withQueryString() en los enlaces de paginación. |
| **RF-08** | **Modal Interceptor para Visitantes** | Si un usuario no autenticado intenta reportar, guardar o contactar, un componente Alpine.js abre una ventana emergente que indica: *"Inicia sesión o regístrate para usar esta función"*, con accesos directos a login y registro sin sacarlo de su posición actual. | **Must** | No redirige bruscamente al visitante; mantiene su contexto de navegación intacto. |
| **RF-09** | **Reporte de Obras y Alerta Manual** | Los artistas autenticados pueden reportar obras con la opción **"Enlace caído"**. El ticket entra en /admin/reports. El Administrador puede revisar el reporte y pulsar un botón para enviar un correo de advertencia manual al autor. | **Must** | Prevención de reportes duplicados pendientes por el mismo usuario. El admin puede agregar una admin\_note al ticket. |
| **RF-10** | **Papelera y Restauración Independiente** | Vista /perfil/papelera con retención de 30 días. Si la colección a la que pertenecía la obra fue borrada, la obra se restaura de forma independiente en el catálogo general sin colección asignada. | **Must** | Restauración limpia sin errores de clave foránea; purga automática a los 30 días con model:prune. |
| **RF-11** | **Panel de Control del Administrador** | Módulo /admin protegido por can:admin para gestionar usuarios (editar/suspender), moderar reportes, supervisar colas (failed\_jobs) y vaciar o restaurar papelera global. | **Must** | Control total administrativo; bloqueo HTTP 403 a usuarios convencionales. |
| **RF-12** | **Provisión de Administrador vía Seeder** | Carga automática del usuario administrador mediante DatabaseSeeder.php leyendo ADMIN\_NAME, ADMIN\_EMAIL y ADMIN\_PASSWORD desde el .env. | **Must** | Seeder idempotente (updateOrCreate) para el entorno Laragon. |

## **5\. Requerimientos No Funcionales (RNF)**

*Esta sección establece la garantía de calidad, definiendo los estándares mínimos de rapidez, seguridad y estabilidad exigidos.*

| ID | Categoría | Requerimiento Técnico | Métrica Cuantitativa Verificable   |
| :---: | :---- | :---- | :---- |
| **RNF-01** | **Rendimiento SMTP** | Desacoplamiento del envío de correo del hilo HTTP mediante colas de base de datos. | Respuesta HTTP ≤ **100 ms**; procesamiento del job por el worker en \< 3 s. |
| **RNF-02** | **Seguridad Whitelist y Anti-SSRF** | Validación estricta contra lista blanca de dominios y bloqueo de IPs privadas antes de llamadas HTTP. | Rechazo inmediato de dominios fuera de whitelist y rangos 127.0.0.0/8, 192.168.0.0/16 (HTTP 422). |
| **RNF-03** | **Eficiencia en MySQL** | Consultas indexadas para búsqueda por texto libre, paginación, filtros y SoftDeletes. | Tiempo de consulta SQL \< **25 ms** en MySQL 8.0 de Laragon con índices en (category, deleted\_at, created\_at). |
| **RNF-04** | **Aislamiento de Iframes** | Sandboxing estricto de reproductores de terceros. | Atributo sandbox="allow-scripts allow-same-origin allow-presentation" obligatorio. |
| **RNF-05** | **Rendimiento Frontend** | Reactividad ágil con Alpine.js sin recargas de página completas. | Bundle JS \< **45 KB** (gzipped); apertura de modales a 60 FPS sostenidos. |

## **6\. Reglas de Negocio (RN)**

*Esta sección reúne las leyes de la casa, es decir, las reglas obligatorias e inquebrantables que mantienen el orden y la coherencia del sistema.*

> * **RN-01 (Lista Blanca Estricta):** Solo se admiten enlaces de: sketchfab.com, artstation.com, youtube.com, youtu.be, vimeo.com, soundcloud.com, spotify.com, pinterest.com, pin.it, deviantart.com, drive.google.com y docs.google.com.  
> * **RN-02 (Moderación y Alerta Manual de Enlace Caído):** Al validar un reporte de enlace caído, el Administrador puede activar el envío de un correo de plantilla al autor de la obra y cambiar el estado de la publicación a hidden.  
> * **RN-03 (Restauración Desvinculada de Colección):** Si una obra es restaurada desde la papelera y su colección temática original ya no existe o está eliminada, la obra queda activa y visible en el portafolio general del artista sin colección asignada.  
> * **RN-04 (Retención a 30 Días):** Toda obra o colección en papelera (*Soft Delete*) se elimina de forma permanente y automática transcurridos 30 días calendario.  
> * **RN-05 (Anti-Suplantación y Auto-Contacto):** Los mensajes de contacto se asocian de forma inmutable al usuario autenticado. No es posible auto-enviarse mensajes.  
> * **RN-06 (Privacidad de Guardados):** Las listas de favoritos son estrictamente privadas.  
> * **RN-07 (Valores Orientativos):** Los costes indicados en las obras tienen carácter exclusivamente informativo y no vinculante.

## **7\. Esquema Relacional y Diccionario de Datos**

### **7.1. Tabla: users**

| Campo | Tipo de Dato | Nulo | Descripción   |
| :---- | :---- | :---: | :---- |
| **id** | bigIncrements('id') | NO | Clave primaria. |
| **name** | string('name', 120\) | NO | Nombre artístico. |
| **slug** | string('slug', 80)-\>unique() | NO | Slug URL (/@slug). |
| **email** | string('email', 191)-\>unique() | NO | Email de acceso y contacto. |
| **password** | string('password') | NO | Hash de contraseña. |
| **avatar\_url** | text('avatar\_url') | SÍ | URL externa del avatar. |
| **avatar\_crop\_x/y/zoom** | integer / decimal | NO | Parámetros CSS de arrastre y zoom. |
| **bio, price\_guide** | text / string | SÍ | Biografía y tarifas orientativas. |
| **commission\_status** | enum('open', 'closed') | NO | Estado de comisiones. |
| **is\_admin, status** | boolean / enum | NO | Privilegios y estado activo/suspendido. |
| **timestamps, deleted\_at** | created\_at, updated\_at, softDeletes | SÍ | Auditoría y borrado lógico. |

### **7.2. Tabla: artworks y reports**

| Tabla | Columnas Principales | Propósito   |
| :---- | :---- | :---- |
| **artworks** | id, user\_id, title, description, price\_hint, external\_url, provider, category, render\_type, thumbnail\_url, embed\_html, status (published/hidden), timestamps, deleted\_at | Almacenamiento de obras multimedia con metadatos oEmbed e índice compuesto en categoría, status y fechas para búsquedas. |
| **reports** | id, user\_id (reporter), artwork\_id, reason, details, admin\_note (text nullable), status (pending/resolved), timestamps | Bandeja de moderación para reportes de enlaces caídos. Incluye la nota manual enviada al artista (admin\_note). |
| **collections / artwork\_collection / bookmarks** | Estructura relacional completa para álbumes temáticos y guardados privados de usuarios. | Organización de portafolios y favoritos de los usuarios autenticados. |

## **8\. Matriz de Trazabilidad (RTM)**

| ID Req | Descripción Funcional | Controlador / Componente | Test PHPUnit | Estado   |
| :---: | :---- | :---- | :---: | ----- |
| **RF-02** | Whitelist Estricta | EmbedService | ArtworkEmbedTest | **Passed** |
| **RF-07** | Buscador Texto Libre (ScopeSearch) | ExplorerController | ExplorerSearchTest | **Passed** |
| **RF-08** | Modal Interceptor Visitantes | \<x-guest-modal /\> | GuestInterceptorTest | **Passed** |
| **RF-09** | Reportes y Alerta Manual Admin | Admin\\ReportController | AdminManualReportTest | **Passed** |
| **RF-10** | Papelera y Restauración Huérfana | TrashController | TrashIndependentTest | **Passed** |
| **RF-12** | Provisión Admin Seeder (.env) | DatabaseSeeder | AdminSeederTest | **Passed** |

## **9\. Supuestos y Dependencias**

> * **Dependencias de Entorno:** PHP 8.2+ con extensiones (mbstring, xml, curl), Composer, Node.js (Vite), MySQL 8.0.  
> * **Dependencias Externas:** Disponibilidad de endpoints oEmbed/OpenGraph de los servicios (Sketchfab, ArtStation, YouTube, etc.).  
> * **Supuestos:** Se asume una conexión estable a internet para el scraping de metadatos; se asume que el servidor cuenta con permisos de escritura en directorios temporales de log.

## **10\. Estrategia de Manejo de Errores**

> * **Errores de Cliente (4xx):** Validación de formularios (422), recursos no encontrados (404), acceso no autorizado (403). Implementar mensajes de feedback amigables en el frontend vía Alpine.js.  
> * **Errores de Servidor (5xx):** Registro detallado en storage/logs/laravel.log. Implementar página de error personalizada para el Administrador en caso de fallo del worker.

## **11\. Flujo de Despliegue y Mantenimiento**

> * **Pasos de Deployment:** Ejecución de php artisan migrate \--force, npm run build para assets, y reinicio del proceso de queue:work en el servidor.

**Estrategia de Mantenimiento:** Purga de logs antiguos y optimización periódica de tablas artworks y reports para evitar degradación de performance.

## **12\. Instrucciones de Prompt para Nueva IA**

*Estas son las instrucciones de 'memoria' para que cualquier IA que nos ayude a programar en el futuro entienda perfectamente qué estamos construyendo y no pierda el contexto.*

> * **Rol:** Desarrollador Full-Stack Senior experto en Laravel 11, PHP 8.2+ y Tailwind/Alpine.js.  
> * **Instrucción:** Actúa de acuerdo a las especificaciones relacionales y de negocio detalladas en este documento de "Artfolio (V8.2.0)". Es un entorno de desarrollo Laragon y base de datos MySQL local. Mantén la arquitectura Zero-Blob estricta, la protección anti-spam reCAPTCHA, el encolado asíncrono para el formulario de contacto con validación anti-suplantación y la lógica de negocio para la papelera de reciclaje y reportes de enlaces caídos. No sugieras funcionalidades que queden fuera del alcance.

**Responde únicamente:** "Contexto asimilado. ¿Qué controlador, modelo o vista de Artfolio comenzamos a programar primero?"