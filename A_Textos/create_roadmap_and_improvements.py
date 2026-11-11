import os
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls
from docx2pdf import convert

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    tcPr.append(parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>'))

def set_cell_margins(cell, top=100, bottom=100, left=140, right=140):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(
        f'<w:tcMar {nsdecls("w")}>'
        f'<w:top w:w="{top}" w:type="dxa"/>'
        f'<w:bottom w:w="{bottom}" w:type="dxa"/>'
        f'<w:left w:w="{left}" w:type="dxa"/>'
        f'<w:right w:w="{right}" w:type="dxa"/>'
        f'</w:tcMar>'
    )
    tcPr.append(tcMar)

def add_callout(doc, text, title="DIAGNÓSTICO DE INGENIERÍA", border_color="4338CA", bg_color="EEF2FF"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    table.columns[0].width = Inches(6.8)

    cell = table.cell(0, 0)
    set_cell_background(cell, bg_color)
    set_cell_margins(cell, top=130, bottom=130, left=180, right=180)

    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>'
        f'<w:left w:val="single" w:sz="24" w:space="0" w:color="{border_color}"/>'
        f'<w:top w:val="none"/><w:right w:val="none"/><w:bottom w:val="none"/>'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)

    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)
    p.paragraph_format.line_spacing = 1.15

    run_title = p.add_run(f"📌 {title}: ")
    run_title.bold = True
    run_title.font.name = "Calibri"
    run_title.font.size = Pt(10)
    run_title.font.color.rgb = RGBColor(30, 27, 75)

    run_text = p.add_run(text)
    run_text.font.name = "Calibri"
    run_text.font.size = Pt(9.5)
    run_text.font.color.rgb = RGBColor(30, 41, 59)

    doc.add_paragraph().paragraph_format.space_after = Pt(4)

def add_h1(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(16)
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.bold = True
    run.font.name = "Calibri"
    run.font.size = Pt(15)
    run.font.color.rgb = RGBColor(30, 27, 75)
    return p

def add_h2(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(12)
    p.paragraph_format.space_after = Pt(4)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.bold = True
    run.font.name = "Calibri"
    run.font.size = Pt(12)
    run.font.color.rgb = RGBColor(67, 56, 202)
    return p

def add_p(doc, text, space_after=5, bold_prefix=None):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(space_after)
    p.paragraph_format.line_spacing = 1.15
    if bold_prefix:
        r_pre = p.add_run(bold_prefix)
        r_pre.bold = True
        r_pre.font.name = "Calibri"
        r_pre.font.size = Pt(10)
        r_pre.font.color.rgb = RGBColor(15, 23, 42)
    run = p.add_run(text)
    run.font.name = "Calibri"
    run.font.size = Pt(10)
    run.font.color.rgb = RGBColor(51, 65, 85)
    return p

def add_bullet(doc, text, bold_prefix=None):
    p = doc.add_paragraph(style='List Bullet')
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(3)
    p.paragraph_format.line_spacing = 1.15
    if bold_prefix:
        r_pre = p.add_run(bold_prefix)
        r_pre.bold = True
        r_pre.font.name = "Calibri"
        r_pre.font.size = Pt(9.5)
        r_pre.font.color.rgb = RGBColor(15, 23, 42)
    run = p.add_run(text)
    run.font.name = "Calibri"
    run.font.size = Pt(9.5)
    run.font.color.rgb = RGBColor(51, 65, 85)
    return p

def add_priority_table(doc, headers, rows, col_widths):
    table = doc.add_table(rows=len(rows) + 1, cols=len(headers))
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False

    hdr_cells = table.rows[0].cells
    for i, h in enumerate(headers):
        hdr_cells[i].text = h
        set_cell_background(hdr_cells[i], "1E1B4B")
        set_cell_margins(hdr_cells[i], top=90, bottom=90, left=100, right=100)
        p = hdr_cells[i].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        for run in p.runs:
            run.font.name = "Calibri"
            run.font.size = Pt(9)
            run.font.bold = True
            run.font.color.rgb = RGBColor(255, 255, 255)

    for r_idx, row in enumerate(rows):
        row_cells = table.rows[r_idx + 1].cells
        bg_col = "F8FAFC" if r_idx % 2 == 0 else "FFFFFF"
        for c_idx, val in enumerate(row):
            row_cells[c_idx].text = str(val)
            # Highlight priority cell
            if c_idx == 1:
                prio = str(val).upper()
                if "CRÍTICA" in prio or "P1" in prio:
                    set_cell_background(row_cells[c_idx], "FEE2E2")
                elif "ALTA" in prio or "P2" in prio:
                    set_cell_background(row_cells[c_idx], "FEF3C7")
                elif "MEDIA" in prio or "P3" in prio:
                    set_cell_background(row_cells[c_idx], "E0E7FF")
                else:
                    set_cell_background(row_cells[c_idx], "F1F5F9")
            else:
                set_cell_background(row_cells[c_idx], bg_col)

            set_cell_margins(row_cells[c_idx], top=75, bottom=75, left=100, right=100)
            p = row_cells[c_idx].paragraphs[0]
            if c_idx in (0, 1):
                p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.space_before = Pt(1)
            p.paragraph_format.space_after = Pt(1)
            p.paragraph_format.line_spacing = 1.1
            for run in p.runs:
                run.font.name = "Calibri"
                run.font.size = Pt(8.5)
                if c_idx in (0, 1, 2):
                    run.font.bold = True
                run.font.color.rgb = RGBColor(30, 41, 59)

    for row in table.rows:
        for idx, w in enumerate(col_widths):
            row.cells[idx].width = Inches(w)

    doc.add_paragraph().paragraph_format.space_after = Pt(6)

def generate_roadmap_pdf():
    doc = Document()

    for s in doc.sections:
        s.top_margin = Inches(0.75)
        s.bottom_margin = Inches(0.75)
        s.left_margin = Inches(0.8)
        s.right_margin = Inches(0.8)

        header = s.header
        p_head = header.paragraphs[0]
        p_head.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        run_h = p_head.add_run("ARTFOLIO | Diagnóstico de Estabilidad, 30 Mejoras y 30 Nuevas Funcionalidades")
        run_h.font.name = "Calibri"
        run_h.font.size = Pt(8)
        run_h.font.color.rgb = RGBColor(148, 163, 184)

        footer = s.footer
        p_foot = footer.paragraphs[0]
        p_foot.alignment = WD_ALIGN_PARAGRAPH.CENTER
        run_f = p_foot.add_run("Informe Técnico de Consultoría y Hoja de Ruta Evolutiva — Proyecto Artfolio")
        run_f.font.name = "Calibri"
        run_f.font.size = Pt(8)
        run_f.font.color.rgb = RGBColor(148, 163, 184)

    # PORTADA
    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_before = Pt(18)
    p_title.paragraph_format.space_after = Pt(4)
    r_title = p_title.add_run("AUDITORÍA TÉCNICA Y HOJA DE RUTA EVOLUTIVA")
    r_title.bold = True
    r_title.font.name = "Calibri"
    r_title.font.size = Pt(22)
    r_title.font.color.rgb = RGBColor(30, 27, 75)

    p_sub = doc.add_paragraph()
    p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_after = Pt(12)
    r_sub = p_sub.add_run("Diagnóstico de Cierre Estabilizado • 30 Mejoras Técnicas Priorizadas • 30 Nuevas Funcionalidades Propuestas")
    r_sub.font.name = "Calibri"
    r_sub.font.size = Pt(11.5)
    r_sub.font.color.rgb = RGBColor(79, 70, 229)

    add_callout(
        doc,
        "Este informe profesional evalúa el estado actual de Artfolio desde una perspectiva de ingeniería de software en producción. "
        "En primer lugar, identifica los ajustes puntuales recomendados para dar por cerrado el ciclo de estabilización 1.0. "
        "Posteriormente, presenta dos matrices exhaustivas de 30 ítems cada una (60 propuestas en total), rigurosamente ordenadas "
        "por prioridad de implementación (Crítica P1, Alta P2, Media P3 y Baja P4).",
        title="RESUMEN EJECUTIVO DE AUDITORÍA",
        border_color="4338CA",
        bg_color="EEF2FF"
    )

    # =========================================================================
    # SECCIÓN 1: DIAGNÓSTICO DE CIERRE — ¿QUÉ FALTA PARA TENERLO FINALIZADO Y ESTABLE?
    # =========================================================================
    add_h1(doc, "1. Diagnóstico Profesional: ¿Qué falta para un cierre 100% finalizado y estable?")
    add_p(
        doc,
        "Actualmente, Artfolio cuenta con una base sólida y funcional respaldada por 49 pruebas automatizadas aprobadas en PHPUnit. "
        "El núcleo (autenticación Breeze con verificación de email, publicación de enlaces multimedia vía EmbedService, visor modal adaptativo, "
        "colecciones, favoritos, papelera SoftDeletes y panel de administración) opera correctamente. Sin embargo, tras auditar el código fuente "
        "línea por línea, existen 6 puntos clave de cierre que diferencian un proyecto funcional de un producto 100% blindado y finalizado:"
    )

    add_bullet(
        doc,
        "Aunque el panel de administración permite suspender usuarios (status = 'suspended'), falta un middleware activo en las rutas autenticadas "
        "y en el controlador de perfil público (PublicProfileController) que cierre la sesión de inmediato al usuario suspendido e impida que su portafolio público siga visible.",
        bold_prefix="1. Aplicación efectiva del bloqueo por suspensión en tiempo real: "
    )
    add_bullet(
        doc,
        "En resources/views/dashboard.blade.php, la grilla está definida para 3 columnas (md:grid-cols-3) pero solo contiene 2 tarjetas ('My Artworks' y 'My Profile'). "
        "Añadir la tercera tarjeta dedicada a 'My Collections & Favorites' junto con contadores rápidos (KPIs del artista) completaría visual y funcionalmente el panel.",
        bold_prefix="2. Completitud visual del Dashboard del Artista: "
    )
    add_bullet(
        doc,
        "Existen pequeñas cadenas residuales en español dentro de plantillas o rutas (ej. title='Guardar a Favoritos' en viewer-modal.blade.php, "
        "o la plantilla de correo link-down-alert.blade.php en español). Unificar el 100% de los textos visibles al inglés garantizará coherencia total ante la evaluación.",
        bold_prefix="3. Homogeneidad lingüística al 100% (Inglés UI): "
    )
    add_bullet(
        doc,
        "En routes/web.php coexisten rutas específicas de papelera (/papelera/obras/{id}/restore) junto con rutas genéricas heredadas (/papelera/{id}/restore). "
        "Depurar las rutas redundantes evitará confusiones en el mantenimiento y en la lectura de 'php artisan route:list'.",
        bold_prefix="4. Limpieza de rutas heredadas en Papelera: "
    )
    add_bullet(
        doc,
        "Cuando un visitante ingresa a una URL inexistente o un usuario intenta editar una obra ajena (403/404), Laravel muestra su página gris por defecto. "
        "Incluir vistas personalizadas en resources/views/errors/ (404, 403, 500) con el diseño y modo oscuro de Artfolio eleva drásticamente el acabado profesional.",
        bold_prefix="5. Páginas de Error Personalizadas (404, 403, 500): "
    )
    add_bullet(
        doc,
        "Asegurar en DemoContentSeeder y AdminSeeder que todos los usuarios de prueba tengan email_verified_at poblado y datos ricos para que, al correr "
        "'php artisan migrate:fresh --seed', cualquier evaluador pueda probar todas las cuentas sin bloqueos inesperados.",
        bold_prefix="6. Blindaje de Seeders con Verificación de Correo: "
    )

    # =========================================================================
    # SECCIÓN 2: LISTA 1 — 30 CAMBIOS Y MEJORAS SOBRE LO EXISTENTE (ORDENADOS POR PRIORIDAD)
    # =========================================================================
    add_h1(doc, "2. Lista 1: 30 Cambios, Refactorizaciones y Mejoras de Estabilidad (Priorizadas)")
    add_p(
        doc,
        "Esta primera lista reúne las 30 mejoras técnicas, de experiencia de usuario (UX), seguridad y calidad de código sobre las funciones "
        "que ya existen en el proyecto, ordenadas estrictamente desde la prioridad más urgente (P1 - Crítica) hasta mejoras de pulido (P4 - Baja):"
    )

    headers_list1 = ["#", "Prioridad", "Área / Archivo", "Cambio o Mejora Propuesta y Justificación Técnica"]
    rows_list1 = [
        ["1", "P1 - Crítica", "Seguridad / Middleware", "Crear middleware EnsureUserIsActive para cerrar sesión e impedir acciones a usuarios con status 'suspended'."],
        ["2", "P1 - Crítica", "PublicProfileController", "Filtrar perfiles públicos con where('status', 'active') para que artistas suspendidos no sean visibles públicamente."],
        ["3", "P1 - Crítica", "Seeders (Admin/Demo)", "Garantizar que AdminSeeder y DemoContentSeeder asignen email_verified_at = now() tras activar MustVerifyEmail."],
        ["4", "P1 - Crítica", "UI / Idioma (Emails)", "Traducir íntegramente resources/views/emails/link-down-alert.blade.php al inglés para mantener consistencia 100%."],
        ["5", "P1 - Crítica", "UI / Viewer Modal", "Corregir tooltips residuales en español (ej. 'Guardar a Favoritos' en viewer-modal.blade.php línea 219) a 'Save to Favorites'."],
        ["6", "P1 - Crítica", "Rutas (routes/web.php)", "Eliminar las rutas duplicadas heredadas trash.restore y trash.destroy conservando las específicas de obras y colecciones."],
        ["7", "P2 - Alta", "Dashboard del Artista", "Añadir la 3ra tarjeta faltante en dashboard.blade.php ('My Collections & Favorites') y barra superior de estadísticas."],
        ["8", "P2 - Alta", "Vistas de Error HTTP", "Crear vistas personalizadas resources/views/errors/404.blade.php, 403.blade.php y 500.blade.php con soporte Dark Mode."],
        ["9", "P2 - Alta", "Rendimiento Eloquent", "Optimizar consultas N+1 en ExplorerController y PublicProfileController cargando with(['user', 'collections']) de forma ansiosa."],
        ["10", "P2 - Alta", "Seguridad / Policies", "Extraer validaciones manuales abort_if($artwork->user_id !== auth()->id()) hacia una ArtworkPolicy y CollectionPolicy estándar."],
        ["11", "P2 - Alta", "Validación EmbedService", "Añadir timeout corto (3s) y manejo de excepciones silencioso cuando EmbedService consulta oEmbed externos sin internet."],
        ["12", "P2 - Alta", "Prevención Doble Clic", "Deshabilitar botones de envío en formularios de creación/edición de obras y colecciones durante el submit para evitar duplicados."],
        ["13", "P2 - Alta", "Limpieza de Controladores", "Remover archivos huérfanos que ya no tienen ruta activa (como ContactController.php y ContactArtist.php) o documentar su reserva."],
        ["14", "P2 - Alta", "Paginación en Paneles", "Sustituir ->get() por ->paginate(12) en ArtworkController@index y CollectionController@index para escalar con catálogos grandes."],
        ["15", "P3 - Media", "Feedback Visual (Toasts)", "Unificar los mensajes flash session('success') y session('error') en un único componente Blade reutilizable <x-flash-alert />."],
        ["16", "P3 - Media", "Accesibilidad (A11y)", "Asegurar atributos aria-label, roles de diálogo y retorno de foco al cerrar los modales con la tecla Escape."],
        ["17", "P3 - Media", "Fallback de Imágenes", "Mejorar el fallback automático en <x-artwork-card /> cuando una miniatura externa falla al cargar (evento @error en <img>)."],
        ["18", "P3 - Media", "Consistencia de URLs", "Estandarizar los prefijos de rutas en routes/web.php al inglés (/favorites, /trash, /artist/{slug}) manteniendo redirección."],
        ["19", "P3 - Media", "Índices en Base de Datos", "Añadir índice compuesto en la tabla artworks sobre (status, category, created_at) para acelerar el filtrado del explorador."],
        ["20", "P3 - Media", "Confirmación de Borrado", "Reemplazar los confirm() nativos del navegador al borrar obras/colecciones por un modal de confirmación estilizado en Alpine.js."],
        ["21", "P3 - Media", "Contador en Colecciones", "Usar withCount('artworks') en CollectionController para mostrar cuántas obras tiene cada colección sin consultas adicionales."],
        ["22", "P3 - Media", "Filtros Persistentes", "Añadir un botón rápido 'Clear all filters' con contador de filtros activos en la barra de búsqueda de la galería pública."],
        ["23", "P3 - Media", "Seguridad de Cabeceras", "Añadir cabeceras básicas de seguridad HTTP (X-Frame-Options, X-Content-Type-Options) mediante un middleware ligero."],
        ["24", "P4 - Baja", "Tipografía Local/Caché", "Unificar la fuente en guest.blade.php (usa Figtree) con app.blade.php (usa Inter y Outfit) para coherencia tipográfica total."],
        ["25", "P4 - Baja", "Orden de Colecciones", "Permitir elegir el orden de visualización en el explorador (Más recientes, Más antiguos, Alfabético A-Z)."],
        ["26", "P4 - Baja", "Meta Tags Dinámicos", "Pasar un título dinámico <x-app-layout :title='$artwork->title'> en cada vista para mejorar las pestañas del navegador."],
        ["27", "P4 - Baja", "Limpieza de Favoritos", "Ocultar automáticamente de la lista de Favoritos las obras cuyos autores hayan sido suspendidos o puestos en estado 'hidden'."],
        ["28", "P4 - Baja", "Estado Vacío (Empty States)", "Enriquecer los estados vacíos en Colecciones, Favoritos y Papelera con ilustraciones SVG e instrucciones de primer paso."],
        ["29", "P4 - Baja", "Validación de Slugs", "Permitir al artista personalizar su propio slug en el formulario de perfil validando caracteres alfanuméricos y unicidad."],
        ["30", "P4 - Baja", "Documentación README", "Actualizar el archivo README.md principal del repositorio con las instrucciones paso a paso de instalación, seeders y tests."]
    ]
    add_priority_table(doc, headers_list1, rows_list1, [0.45, 0.95, 1.45, 3.95])

    # =========================================================================
    # SECCIÓN 3: LISTA 2 — 30 NUEVAS FUNCIONALIDADES PROPUESTAS (ORDENADAS POR PRIORIDAD)
    # =========================================================================
    add_h1(doc, "3. Lista 2: 30 Nuevas Funcionalidades y Expansiones Estratégicas (Priorizadas)")
    add_p(
        doc,
        "Esta segunda lista presenta 30 funcionalidades completamente nuevas que pueden incorporarse al proyecto para expandir su alcance funcional, "
        "social y comercial, ordenadas desde las más rápidas y de mayor impacto académico/cliente (P1) hasta innovaciones avanzadas (P4):"
    )

    headers_list2 = ["#", "Prioridad", "Módulo / Categoría", "Nueva Funcionalidad Propuesta y Valor Agregado"]
    rows_list2 = [
        ["1", "P1 - Inmediata", "Portafolio Público", "Enlaces a Redes Sociales Oficiales: Campos en el perfil para ArtStation, Instagram, Behance, X y sitio web con íconos en cabecera."],
        ["2", "P1 - Inmediata", "Analítica de Obras", "Contador de Vistas (Views Counter): Registro del número de aperturas de cada obra en el visor modal y visualización en las tarjetas."],
        ["3", "P1 - Inmediata", "Catálogo y Filtros", "Sistema de Etiquetas (Tags / Hashtags): Relación muchos-a-muchos para etiquetar obras (#blender, #pixelart, #lowpoly) y filtrar por tag."],
        ["4", "P1 - Inmediata", "Descubrimiento", "Obras Destacadas (Pinned Artworks): Permitir al artista fijar hasta 3 obras principales en la parte superior de su portafolio."],
        ["5", "P1 - Inmediata", "Viralidad / UX", "Botón 'Share Artwork / Portfolio' con generación automática de código QR y copia rápida de enlace directo al portapapeles."],
        ["6", "P1 - Inmediata", "Reconocimiento", "Insignia de Artista Verificado (Verified Badge): Check azul otorgable por el administrador a artistas destacados de la comunidad."],
        ["7", "P1 - Inmediata", "Curaduría Personal", "Portada Personalizada de Perfil (Banner Image): Imagen panorámica superior en el portafolio público del artista."],
        ["8", "P2 - Alta", "Interacción Social", "Sistema de Aplausos / Likes ('Appreciations'): Botón de reconocimiento público separado del guardado privado en Favoritos."],
        ["9", "P2 - Alta", "Detalle Técnico", "Ficha de Herramientas y Software Usado: Selector de programas utilizados en cada obra (Photoshop, ZBrush, Unreal, Ableton)."],
        ["10", "P2 - Alta", "Comisiones", "Tabla de Tarifas y Servicios (Commission Sheet): Sección estructurada en el perfil del artista detallando precios base y tiempos de entrega."],
        ["11", "P2 - Alta", "Nuevos Proveedores", "Ampliación de EmbedService: Soporte para SoundCloud, Vimeo, CodePen (arte generativo), Bandcamp y ArtStation embeds."],
        ["12", "P2 - Alta", "Presentación", "Modo Presentación / Slideshow a Pantalla Completa: Botón en las colecciones para reproducir las obras secuencialmente como exposición."],
        ["13", "P2 - Alta", "Derechos de Autor", "Selector de Licencia Creative Commons / Copyright: Especificar en cada obra si permite uso comercial, atribución o todos los derechos reservados."],
        ["14", "P2 - Alta", "Moderación Activa", "Centro de Notificaciones Internas (Campana en Navbar): Avisos en la barra superior cuando una obra recibe favoritos o alertas del admin."],
        ["15", "P3 - Media", "Exportación", "Generador de CV / Portafolio en PDF: Botón para que el artista descargue un resumen elegante en PDF de su biografía y mejores obras."],
        ["16", "P3 - Media", "Exploración Visual", "Filtro por Paleta de Colores Dominante: Asignar color principal a cada obra visual y permitir explorar la galería por tonalidades."],
        ["17", "P3 - Media", "Internacionalización", "Selector de Idioma Dinámico (EN / ES): Botón en la barra superior junto al cambio de tema para alternar toda la interfaz entre inglés y español."],
        ["18", "P3 - Media", "Seguridad", "Registro de Actividad de Moderación (Audit Log): Tabla histórica para saber qué administrador suspendió a un usuario o resolvió un reporte."],
        ["19", "P3 - Media", "Productividad", "Acciones por Lote (Bulk Actions) en Mis Obras: Seleccionar varias obras a la vez para cambiar su estado, moverlas a una colección o enviarlas a papelera."],
        ["20", "P3 - Media", "Comunidad", "Directorio Público de Artistas (/artists): Página dedicada a explorar creadores filtrando por disponibilidad de comisiones ('Open') y especialidad."],
        ["21", "P3 - Media", "Proceso Creativo", "Comparador Interactivo 'Antes / Después' (Wireframe vs Render Final): Slider superpuesto para mostrar el detrás de escena de una obra."],
        ["22", "P3 - Media", "Integración", "Canal RSS / JSON Feed por Artista: URL pública (/artista/{slug}/feed) para que seguidores se suscriban a las novedades del creador."],
        ["23", "P4 - Exploratoria", "Social", "Sistema de Seguimiento entre Artistas (Follow / Following) y pestaña de feed personalizado con obras de artistas seguidos."],
        ["24", "P4 - Exploratoria", "Feedback", "Comentarios Curados por Obra: Sección de comentarios donde el artista autor tiene control para aprobar, fijar o desactivar comentarios."],
        ["25", "P4 - Exploratoria", "E-Commerce Ligero", "Enlaces de Compra / Descarga Directa (Gumroad, Patreon, Ko-fi, Itch.io) integrados como botón de acción en cada obra."],
        ["26", "P4 - Exploratoria", "Autenticación", "Inicio de Sesión Social (OAuth con Google y GitHub/Discord) mediante Laravel Socialite para registro en un clic."],
        ["27", "P4 - Exploratoria", "Curaduría Editorial", "Sección 'Staff Picks / Obra de la Semana' en la portada elegida desde el panel de administración."],
        ["28", "P4 - Exploratoria", "Protección", "Filtro de Contenido Sensible / Maduro (NSFW Blur Toggle): Desenfoque preventivo opcional que el visitante decide revelar con un clic."],
        ["29", "P4 - Exploratoria", "Colaboración", "Colecciones Colaborativas o Co-Autoría: Permitir acreditar a más de un artista registrado en un mismo proyecto multidisciplinario."],
        ["30", "P4 - Exploratoria", "API Abierta", "Widget Embebible / API Pública Read-Only: Permitir al artista incrustar una tarjeta interactiva de su portafolio Artfolio en su blog externo."]
    ]
    add_priority_table(doc, headers_list2, rows_list2, [0.45, 0.95, 1.45, 3.95])

    # =========================================================================
    # SECCIÓN 4: RECOMENDACIÓN DE HOJA DE RUTA (PRÓXIMOS PASOS)
    # =========================================================================
    add_h1(doc, "4. Recomendación Estratégica de Implementación")
    add_p(
        doc,
        "Para maximizar el impacto del proyecto sin comprometer la simplicidad ni la limpieza arquitectónica alcanzada, se sugiere "
        "abordar la evolución en dos bloques cortos:"
    )
    add_bullet(
        doc,
        "Implementar los ítems 1 al 8 de la Lista 1 (Middleware de suspensión activa, 3ra tarjeta y KPIs en el Dashboard del artista, "
        "traducción de los últimos textos residuales al inglés, limpieza de rutas duplicadas y páginas 404/403 personalizadas). Esto dejará "
        "la versión 1.0 completamente blindada ante cualquier prueba de evaluación.",
        bold_prefix="Fase A — Cierre de Estabilidad 1.0 (Tiempo estimado: 1 sesión rápida): "
    )
    add_bullet(
        doc,
        "Si se desea añadir un factor diferenciador extra de alto impacto visual con mínimo código, los ítems 1, 2 y 4 de la Lista 2 "
        "(Redes sociales en el perfil, Contador de vistas por obra y Obras fijadas/destacadas) aportan un enorme valor estético y funcional "
        "utilizando exactamente los mismos patrones de Laravel vistos en clase.",
        bold_prefix="Fase B — Expansión Funcional Destacada (Opcional): "
    )

    docx_path = r"c:\laragon\www\germanBianchi_artfolio\A_Textos\Artfolio_Diagnostico_Mejoras_y_Nuevas_Funciones.docx"
    pdf_path = r"c:\laragon\www\germanBianchi_artfolio\A_Textos\Artfolio_Diagnostico_Mejoras_y_Nuevas_Funciones.pdf"

    doc.save(docx_path)
    print(f"DOCX saved at: {docx_path}")
    convert(docx_path, pdf_path)
    print(f"PDF saved at: {pdf_path} ({os.path.getsize(pdf_path)} bytes)")

if __name__ == "__main__":
    generate_roadmap_pdf()

