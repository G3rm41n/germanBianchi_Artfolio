# Plan de Implementación: Extractores Automáticos para YouTube, Spotify y Google Docs / Drive

## 1. Contexto y Objetivo
En las fases anteriores implementamos la extracción automática de metadatos e imágenes en alta resolución para **Pinterest**, **DeviantArt** y **Sketchfab**, permitiendo que al pegar un enlace se extraiga la portada/render HD, se autocompleten el título y la categoría, y se presente una experiencia fluida tanto en el formulario como en la galería y en el visor modal.

El objetivo de este plan es extender este mismo patrón a las tres plataformas multimedia restantes de la lista blanca (**RN-01**):
1. **YouTube** (categoría `video`): Detección de enlaces estándar y cortos, obtención de carátula en máxima resolución (1080p), extracción del título y autor, y reproducción interactiva en iframe 16:9 dentro del modal.
2. **Spotify** (categoría `audio`): Detección de canciones, álbumes y podcasts vía su API pública oEmbed, extracción de la carátula oficial del álbum/tema para la galería y del widget interactivo de reproducción de audio para el modal.
3. **Google Docs & Google Drive** (categoría `doc`): Detección de documentos, presentaciones, hojas de cálculo y PDFs de Drive, resolución de ID, generación de miniatura de vista previa, corrección de la categoría a `doc` y visor de lectura `/preview` incrustado en el modal.

---

## 2. Decisiones de Diseño Acordadas con el Usuario
Tras consultar las preferencias de experiencia de usuario:
* **YouTube en el Visor Modal:** Reproductor oficial interactivo embebido (`<iframe>` responsivo en formato 16:9) que permite disfrutar del video directamente en Artfolio sin salir del sitio, complementado con un enlace directo al video en YouTube. En la galería de obras, se muestra la miniatura oficial en máxima resolución.
* **Spotify en el Visor Modal:** Reproductor interactivo oficial (widget de audio con barra de reproducción y portada) incrustado en el modal, junto a un botón destacado con la paleta de Spotify (`#1DB954`) para "Escuchar en Spotify". En la galería se muestra la portada del álbum en alta definición.
* **Google Docs / Drive en el Visor Modal:** Visor de lectura incrustado en iframe utilizando el modo `/preview` nativo de Google Docs/Drive, permitiendo leer y deslizar el documento o PDF dentro de Artfolio, más un botón de acceso directo "Abrir documento en Google Docs".

---

## 3. Especificación Técnica por Plataforma

### A. YouTube (`youtube.com`, `youtu.be`)
* **Formatos de URL soportados:**
  * `https://www.youtube.com/watch?v=VIDEO_ID`
  * `https://youtu.be/VIDEO_ID`
  * `https://www.youtube.com/shorts/VIDEO_ID`
  * `https://www.youtube.com/embed/VIDEO_ID`
* **Extracción de ID y Miniatura:**
  * Regex para extraer `VIDEO_ID` limpiando parámetros de tiempo o listas.
  * Miniatura primaria HD: `https://img.youtube.com/vi/{VIDEO_ID}/maxresdefault.jpg`.
  * Fallback de miniatura: `https://img.youtube.com/vi/{VIDEO_ID}/hqdefault.jpg`.
* **Extracción de Metadatos (oEmbed):**
  * Endpoint público: `https://www.youtube.com/oembed?url={URL}&format=json`.
  * Extrae: `title` y `author_name`.
  * Título final sugerido: `{title} - por {author_name}`.
* **Embed HTML:**
  * `<iframe src="https://www.youtube.com/embed/{VIDEO_ID}" sandbox="allow-scripts allow-same-origin allow-presentation" allowfullscreen class="w-full h-full aspect-video"></iframe>`.
* **Categoría asignada:** `video`.
* **Render Type:** `iframe`.

---

### B. Spotify (`spotify.com`, `open.spotify.com`)
* **Formatos de URL soportados:**
  * `https://open.spotify.com/track/{ID}`
  * `https://open.spotify.com/album/{ID}`
  * `https://open.spotify.com/playlist/{ID}`
  * `https://open.spotify.com/episode/{ID}`
* **Extracción de Metadatos (oEmbed):**
  * Endpoint público oficial: `https://open.spotify.com/oembed?url={URL}`.
  * Extrae:
    * `thumbnail_url`: Portada en alta definición del álbum/canción (CDN `image-cdn-ak.spotifycdn.com` o `i.scdn.co`).
    * `title`: Título de la pista o álbum.
    * `html`: Iframe oficial responsivo de Spotify.
* **Embed HTML:**
  * Formato de iframe adaptativo:
    `<iframe src="https://open.spotify.com/embed/track/{ID}" width="100%" height="352" frameborder="0" allowfullscreen allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>`.
* **Categoría asignada:** `audio`.
* **Render Type:** `iframe` (con `thumbnail_url` asignada para la tarjeta de la galería y la previsualización del formulario).

---

### C. Google Docs y Google Drive (`docs.google.com`, `drive.google.com`)
* **Formatos de URL soportados:**
  * Documentos: `https://docs.google.com/document/d/{ID}/edit...` -> `https://docs.google.com/document/d/{ID}/preview`
  * Presentaciones: `https://docs.google.com/presentation/d/{ID}/edit...` -> `https://docs.google.com/presentation/d/{ID}/preview`
  * Hojas de cálculo: `https://docs.google.com/spreadsheets/d/{ID}/edit...` -> `https://docs.google.com/spreadsheets/d/{ID}/preview`
  * Archivos / PDFs en Google Drive: `https://drive.google.com/file/d/{ID}/view...` -> `https://drive.google.com/file/d/{ID}/preview`
* **Corrección de Categoría Crítica:**
  * Actualmente `EmbedService.php` asigna la categoría `'document'`, pero la regla de validación en `ArtworkRequest.php` y el formulario esperan `'doc'`. Se unifica a `'doc'`.
* **Extracción de Metadatos:**
  * Extracción del `ID` del documento mediante expresión regular.
  * Miniatura de previsualización: `https://drive.google.com/thumbnail?id={ID}&sz=w800` (o fallback visual si el documento tiene permisos restringidos).
  * Título: Intento de lectura vía OpenGraph / `<title>` con cliente HTTP y User-Agent adecuado. Si no es accesible públicamente, se genera un título descriptivo: `Documento de Google Docs` o `Archivo en Google Drive`.
* **Embed HTML:**
  * `<iframe src="{previewUrl}" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" class="w-full h-full min-h-[500px]"></iframe>`.
* **Categoría asignada:** `doc`.
* **Render Type:** `iframe`.

---

## 4. Archivos a Modificar

1. **[app/Services/EmbedService.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Services/EmbedService.php):**
   * Separar `'spotify'` de `'audio_provider'` para otorgarle su propio manejador oEmbed especializado.
   * Implementar `fetchYouTubeData(string $url)`.
   * Implementar `fetchSpotifyData(string $url)`.
   * Implementar `fetchGoogleDocsData(string $url)`.
   * Corregir el retorno de categoría para Google Drive de `'document'` a `'doc'`.
   * Actualizar `processUrl()` para enriquecer automáticamente con `thumbnail_url`, `title`, `embed_html`, `category` y `render_type`.
2. **[app/Http/Controllers/ArtworkController.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Http/Controllers/ArtworkController.php):**
   * El endpoint `POST /artworks/preview-url` consumirá la salida actualizada de `EmbedService::processUrl()`, retornando de forma instantánea el `thumbnail_url`, `title`, `category` y `provider` para que el formulario reactivo los refleje al vuelo.
3. **[resources/views/artworks/create.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/artworks/create.blade.php) y [edit.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/artworks/edit.blade.php):**
   * Añadir etiquetas de calidad y badges para las nuevas plataformas en la tarjeta de previsualización:
     * `YouTube HD (1080p)`
     * `Spotify (Audio Track / Album)`
     * `Google Docs / Drive (Documento)`
   * Garantizar que al pegar una URL de YouTube se seleccione la categoría `Animación / Cine` (`video`), para Spotify `Música / Podcast` (`audio`), y para Google Docs `Documento / Cómic` (`doc`).
4. **[resources/views/components/viewer-modal.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/components/viewer-modal.blade.php):**
   * En el CASO 1 (`render_type === 'iframe'`), asegurar que el reproductor de YouTube mantenga el aspect ratio 16:9 responsivo.
   * Para Spotify, asegurar un contenedor centrado con bordes redondeados acordes al reproductor (`max-w-lg`).
   * Para Google Docs, asegurar un contenedor de altura completa (`min-h-[600px]`) que permita la lectura fluida del documento.
   * Añadir botones estilizados de atribución para cada proveedor en el modal:
     * YouTube: Rojo `#ff0000` con texto "Ver en YouTube".
     * Spotify: Verde `#1DB954` con texto "Escuchar en Spotify".
     * Google Docs: Azul `#4285F4` con texto "Abrir en Google Docs".
5. **[resources/views/components/artwork-card.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/components/artwork-card.blade.php):**
   * Como ahora las obras de YouTube y Spotify tendrán `thumbnail_url` real, la tarjeta mostrará de inmediato la miniatura real del video o la carátula del álbum en lugar de los iconos de sustitución grises.

---

## 5. Matriz de Pruebas y Validación

| Plataforma | URL de Prueba | Miniatura Obtenida | Título Obtenido | Categoría | Comportamiento en Modal |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **YouTube (Estándar)** | `https://www.youtube.com/watch?v=dQw4w9WgXcQ` | Miniatura 1080p (`maxresdefault.jpg`) | Título oficial del video | `video` | Reproductor interactivo 16:9 con controles de video |
| **YouTube (Short / YouTu.be)** | `https://youtu.be/dQw4w9WgXcQ` | Miniatura 1080p | Título oficial del video | `video` | Reproductor interactivo 16:9 |
| **Spotify (Track)** | `https://open.spotify.com/track/4cOdK2wGLETKBW3PvgPWqT` | Carátula HD del álbum | Nombre de la canción | `audio` | Widget interactivo oficial con botón Play |
| **Google Docs** | `https://docs.google.com/document/d/.../edit` | Miniatura / Icono de documento | Título del documento | `doc` | Visor de lectura incrustado `/preview` |
| **Google Drive PDF** | `https://drive.google.com/file/d/.../view` | Miniatura del PDF en Drive | Nombre del archivo | `doc` | Visor de lectura de PDF incrustado |

---

## 6. Siguientes Pasos
Una vez aprobado este plan:
1. Implementar los métodos de extracción y generación de HTML en [EmbedService.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Services/EmbedService.php).
2. Ajustar la vista modal [viewer-modal.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/components/viewer-modal.blade.php) con los nuevos estilos de reproductor y botones de atribución.
3. Actualizar la tarjeta de previsualización en [create.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/artworks/create.blade.php) y [edit.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/artworks/edit.blade.php).
4. Ejecutar pruebas con el navegador y scripts de validación para garantizar carga rápida y ausencia de errores de consola.
