# 📌 Plan de Implementación: Extracción Automática de Imágenes de Pinterest

## 1. Verificación Técnica de la Imagen (DevTools & Análisis de Red)

Al inspeccionar el enlace proporcionado ([https://ar.pinterest.com/pin/497718196334247681/](https://ar.pinterest.com/pin/497718196334247681/)), confirmamos que la imagen que detectaste en las herramientas de desarrollo es exactamente:

```text
https://i.pinimg.com/736x/09/3d/e3/093de35dce87439bac559c41445c199d.jpg
```

### Hallazgos de la CDN de Pinterest (`i.pinimg.com`):
Pinterest organiza todas las imágenes bajo una misma estructura hash en su CDN según la resolución deseada:

| Ruta | Resolución | Peso detectado | Uso típico | Estado HTTP |
|---|---|---|---|---|
| `/236x/09/3d/e3/...jpg` | 236px ancho | ~15 KB | Miniaturas y respuesta oEmbed estándar | ✅ 200 OK |
| `/736x/09/3d/e3/...jpg` | 736px ancho | **78.6 KB** | **Visualización principal en DevTools web** | ✅ 200 OK |
| `/originals/09/3d/e3/...jpg` | Original completa | **126.9 KB** | **Archivo original subido sin compresión extra** | ✅ 200 OK |

### ¿Dónde reside esta información en la página del Pin?
1. **En la etiqueta OpenGraph (`<meta property="og:image">`):** Pinterest expone directamente la versión `736x`.
2. **En los metadatos JSON-LD (`<script type="application/ld+json">`):** Pinterest expone la versión `/originals/`.
3. **En la API oficial oEmbed (`pinterest.com/oembed.json`):** Devuelve la miniatura en `/236x/`, cuyo patrón hash (`09/3d/e3/...`) coincide idénticamente con la versión `736x` y `originals`.

---

## 2. Diagnóstico del Estado Actual en Artfolio

Actualmente en [EmbedService.php](file:///c:/laragon/www/germanBianchi_artfolio/app/Services/EmbedService.php):
- Cuando el usuario pega un enlace de Pinterest (`pinterest.com/pin/...`), el sistema lo trata como un widget embebible (`render_type = 'iframe'`).
- En el modal de visualización ([viewer-modal.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/components/viewer-modal.blade.php)), esto carga el iframe de Pinterest (`assets.pinterest.com/ext/embed.html`), el cual muestra publicidad, bordes fijos y a menudo solicita inicio de sesión.
- En la tarjeta ([artwork-card.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/components/artwork-card.blade.php)), se utiliza la miniatura baja de `236x`.

---

## 3. Plan de Implementación Propuesto

### Fase 1: Actualización del Servicio de Extracción (`EmbedService.php`)
- **Detección inteligente:** Identificar si la URL corresponde a un Pin de Pinterest (`pinterest.com/pin/*`, `ar.pinterest.com/pin/*`, `pin.it/*`).
- **Resolución a imagen directa:**
  - Consultar el oEmbed oficial para obtener el hash de la imagen de forma instantánea sin riesgo de bloqueo por bot.
  - Transformar la URL de `/236x/` a `/736x/` (o `/originals/` previa verificación HEAD rápida).
  - En caso de requerirlo, realizar fallback leyendo las cabeceras `og:image` de la página.
- **Asignación de tipos:**
  - `render_type` pasará a ser `'image'` (no `'iframe'`).
  - `thumbnail_url` almacenará la versión de alta calidad (`736x`).
  - `category` se establecerá automáticamente como `'image'`.

### Fase 2: Previsualización en Vivo en el Formulario (`artworks/create` y `artworks/edit`)
- Crear una ruta/controlador API interno: `POST /api/oembed/preview`.
- Añadir a la vista [create.blade.php](file:///c:/laragon/www/germanBianchi_artfolio/resources/views/artworks/create.blade.php) un listener Alpine.js en el campo `external_url`.
- Al pegar el enlace de Pinterest, el frontend muestra de inmediato:
  - Una miniatura de la imagen encontrada.
  - Un mensaje de confirmación: *"Imagen de Pinterest detectada correctamente"*.
  - Autocompletado del título si oEmbed o la página provee uno y el campo está vacío.

### Fase 3: Visualización en el Visor Modal (`viewer-modal.blade.php`)
- Adaptar el visor para que, al ser una imagen de Pinterest con `render_type = 'image'`:
  - Se visualice en pantalla completa limpia sin bordes ni iframe widget.
  - Incluya la cabecera `referrerpolicy="no-referrer"` para evitar bloqueos por hotlinking de Pinterest CDN.
  - Conserve el enlace de atribución original: *"Ver pin en Pinterest ↗"*.

### Fase 4: Actualización de Obras Previas (Opcional)
- Comando artisan rápido para actualizar cualquier pin existente en la base de datos que haya quedado guardado con la miniatura pequeña o como iframe.
