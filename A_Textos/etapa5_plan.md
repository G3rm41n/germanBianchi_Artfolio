# 🎨 ETAPA 5 — Plan de Implementación Detallado
## Artfolio: Funcionalidades Avanzadas, Búsqueda, Perfil Interactivo y Retención de Datos

---

## Objetivo de la Etapa

Esta etapa se enfoca en implementar los sistemas complejos detallados en el plan maestro, dotando a la plataforma de herramientas profesionales tanto para los artistas (gestión avanzada, personalización de perfil, seguridad de datos) como para los visitantes (exploración precisa y retención mediante favoritos).

Siguiendo nuestra directriz de excelencia visual, mantendremos la estética premium utilizando micro-interacciones de **Alpine.js** y diseños oscuros y elegantes en **Tailwind CSS**.

---

## BLOQUE A — Buscador Multi-campo y Filtros de Galería (RF-07)

*El sistema de exploración (Explorer) se potenciará para permitir descubrir arte con precisión.*

1. **Eloquent Scope (`app/Models/Artwork.php`)**:
   - Crearemos un `scopeSearch($query, $term)` que utilizará `where` y `orWhere` para buscar coincidencias en:
     - Título de la obra.
     - Descripción de la obra.
     - Nombre o alias (`slug`) del artista asociado (usando `whereHas('user')`).

2. **Controlador (`ExplorerController.php`)**:
   - Leerá los parámetros `?search=` y `?category=` de la URL.
   - Encadenará el query builder para aplicar los filtros activos.
   - Utilizará `paginate()->withQueryString()` para que la navegación por páginas no pierda los filtros aplicados.

3. **Interfaz Gráfica (`explorer/index.blade.php`)**:
   - **Barra de Búsqueda Premium**: Un `input` estilo *glassmorphism* con ícono de lupa y micro-animación en *focus*.
   - **Filtros por Categoría**: Pestañas o botones estilo "Pills" (Ej: *Todos, Ilustración, 3D, Video*) que recarguen o filtren la vista dinámica de la galería.

---

## BLOQUE B — Drag-to-Crop Avatar Interactivo (RF-01)

*Dar al artista control total sobre su identidad visual mediante una interfaz dinámica en su perfil.*

1. **Componente Alpine.js (`<x-avatar-crop>`)**:
   - Un componente interactivo en la vista de edición de perfil (`profile/edit.blade.php`).
   - Permitirá seleccionar una imagen local y mostrarla en un lienzo sobre el que el usuario podrá arrastrar la imagen (pan) y hacer zoom (scroll/slider).
   - Capturará los metadatos ocultos: `crop_x`, `crop_y` y `zoom`.

2. **Backend (`ProfileController.php`)**:
   - Guardará la imagen en `storage/app/public/avatars`.
   - Persistirá los valores matemáticos del recorte en la tabla `users`.

3. **Renderizado (UI/UX)**:
   - Al mostrar el avatar en tarjetas de obras, perfil público o navbar, aplicaremos clases dinámicas de Tailwind (como `object-position` ajustado) combinadas con el estilo en línea generado a partir del `crop` guardado, asegurando que el encuadre sea exactamente el que el artista eligió sin generar múltiples archivos de imagen.

---

## BLOQUE C — Papelera de Reciclaje y Purga (RF-10, RN-03, RN-04)

*Protección contra borrado accidental y mantenimiento automático de la base de datos.*

1. **Rutas y Controlador (`TrashController.php`)**:
   - Creación de rutas bajo `/perfil/papelera`.
   - `index()`: Listar obras donde `deleted_at` no sea nulo (`onlyTrashed()`).
   - `restore($id)`: Restaurar modelo y manejar lógica de desvinculación (si la obra estaba en una colección, se restaurará sin vínculos por seguridad, tal como dicta el requerimiento RN-03).
   - `forceDelete($id)`: Eliminación manual definitiva.

2. **Vista (`trash/index.blade.php`)**:
   - Interfaz con un grid especial que muestra la obra opacada/en escala de grises.
   - Incluirá un aviso destacado: "Esta obra se eliminará permanentemente en X días".

3. **Purga Automática (Prunable)**:
   - Añadir el trait `Prunable` en el modelo `Artwork`.
   - El método `prunable()` devolverá obras eliminadas hace más de 30 días.
   - Lógica extra en el evento `pruning` para asegurar que las miniaturas alojadas localmente se borren del almacenamiento físico.

---

## BLOQUE D — Favoritos y Modal Interceptor (RF-08)

*Herramientas para interactuar y llamado a la acción para retener usuarios no registrados.*

1. **Bookmarks (`BookmarkController.php`)**:
   - Rutas protegidas (`auth`) para dar "Me gusta/Guardar" a una obra (toggle).
   - Relación `belongsToMany` entre User y Artwork.
   - Nueva pestaña/vista en el dashboard del usuario: `/perfil/favoritos`.

2. **Interceptor de Visitantes (`<x-guest-modal>`)**:
   - Si un visitante no logueado interactúa con el botón de "Guardar" u otros componentes interactivos:
     - Alpine.js interceptará el click previniendo la acción.
     - Mostrará un modal premium sugiriendo iniciar sesión o crear cuenta para acceder a esa funcionalidad.

---

## BLOQUE E — Sistema de Colecciones

*Organización del portafolio en álbumes/agrupaciones lógicas.*

1. **Gestión (`CollectionController.php`)**:
   - CRUD para que el artista cree agrupaciones (Ej: "Serie Cyberpunk 2024").
   - Interfaz para vincular/desvincular obras existentes a la colección (Pivot `artwork_collection`).

2. **Vista Pública (`PublicProfileController.php`)**:
   - El perfil público del artista se enriquecerá mostrando pestañas o secciones para explorar su portafolio tanto "Por Obra Individual" como "Por Colecciones".

---

## BLOQUE F — Reportes Seguros (RF-09)

*Herramienta de moderación descentralizada.*

1. **Flujo de Reporte**:
   - Botón discreto de bandera ("Reportar obra") en `<x-viewer-modal>`.
   - Abre un mini-modal preguntando el motivo (Enlace roto, Contenido inapropiado, Derechos de autor).
   - Se guarda en la tabla `reports` (solo para usuarios registrados, o interceptado por `<x-guest-modal>` si son visitantes).
   - El admin (fase futura) revisará este panel de reportes.

---

## ✅ CHECKLIST DE VERIFICACIÓN HUMANA (Al completar Fase 5)

Al finalizar la ejecución de este bloque de código, validaremos:
- [ ] Al escribir en la galería pública, los resultados se filtran en tiempo real o tras hacer "Enter", manteniendo un estilo limpio.
- [ ] En la configuración de tu perfil puedes subir una foto, arrastrarla dentro de un círculo para encuadrarla y guardarla con precisión.
- [ ] Al borrar una obra desde `/perfil/obras`, ésta desaparece de la galería pública pero puedes verla y restaurarla en `/perfil/papelera`.
- [ ] Como visitante (navegación en incógnito), al intentar dar click al corazón de "Favorito", aparece el hermoso Modal Interceptor invitando al registro.

---

**¿Te parece correcto el alcance y nivel de detalle de esta Etapa 5?** Si le das a "Proceed" (Proceder), comenzaremos la codificación, te sugiero que iniciemos directamente con el **BLOQUE A (Buscador)** y el **BLOQUE D (Favoritos y Modal de Visitante)** para potenciar la galería actual.
