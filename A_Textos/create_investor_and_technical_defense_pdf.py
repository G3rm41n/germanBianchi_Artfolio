import os
import sys
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


def set_cell_margins(cell, top=110, bottom=110, left=140, right=140):
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


def add_callout(doc, text, title="CLAVE DE PRESENTACIÓN / DEFENSA", border_color="4338CA", bg_color="EEF2FF"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    table.columns[0].width = Inches(6.7)

    cell = table.cell(0, 0)
    set_cell_background(cell, bg_color)
    set_cell_margins(cell, top=130, bottom=130, left=170, right=170)

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

    run_title = p.add_run(f"💡 {title}: ")
    run_title.bold = True
    run_title.font.name = "Calibri"
    run_title.font.size = Pt(10)
    run_title.font.color.rgb = RGBColor(30, 27, 75)

    run_text = p.add_run(text)
    run_text.font.name = "Calibri"
    run_text.font.size = Pt(9.5)
    run_text.font.color.rgb = RGBColor(30, 41, 59)

    doc.add_paragraph().paragraph_format.space_after = Pt(4)


def add_code_box(doc, code_text, title="Ruta / Fragmento Técnico Clave"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    table.columns[0].width = Inches(6.7)

    cell = table.cell(0, 0)
    set_cell_background(cell, "F8FAFC")
    set_cell_margins(cell, top=100, bottom=100, left=150, right=150)

    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>'
        f'<w:left w:val="single" w:sz="18" w:space="0" w:color="334155"/>'
        f'<w:top w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/>'
        f'<w:right w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/>'
        f'<w:bottom w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/>'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)

    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(1)
    p.paragraph_format.space_after = Pt(1)
    p.paragraph_format.line_spacing = 1.1

    run_hdr = p.add_run(f"📂 {title}\n")
    run_hdr.bold = True
    run_hdr.font.name = "Consolas"
    run_hdr.font.size = Pt(8.5)
    run_hdr.font.color.rgb = RGBColor(67, 56, 202)

    run_code = p.add_run(code_text.strip())
    run_code.font.name = "Consolas"
    run_code.font.size = Pt(8.5)
    run_code.font.color.rgb = RGBColor(15, 23, 42)

    doc.add_paragraph().paragraph_format.space_after = Pt(4)


def add_part_banner(doc, part_label, part_title, subtitle_text):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    table.columns[0].width = Inches(6.8)

    cell = table.cell(0, 0)
    set_cell_background(cell, "1E1B4B")
    set_cell_margins(cell, top=160, bottom=160, left=200, right=200)

    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)

    r1 = p.add_run(f"{part_label}\n")
    r1.bold = True
    r1.font.name = "Calibri"
    r1.font.size = Pt(10)
    r1.font.color.rgb = RGBColor(165, 180, 252)

    r2 = p.add_run(f"{part_title}\n")
    r2.bold = True
    r2.font.name = "Calibri"
    r2.font.size = Pt(16)
    r2.font.color.rgb = RGBColor(255, 255, 255)

    r3 = p.add_run(subtitle_text)
    r3.font.name = "Calibri"
    r3.font.size = Pt(9.5)
    r3.font.color.rgb = RGBColor(203, 213, 225)

    doc.add_paragraph().paragraph_format.space_after = Pt(6)


def add_h1(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(16)
    p.paragraph_format.space_after = Pt(5)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.bold = True
    run.font.name = "Calibri"
    run.font.size = Pt(14)
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
    run.font.size = Pt(11.5)
    run.font.color.rgb = RGBColor(67, 56, 202)
    return p


def add_h3(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(8)
    p.paragraph_format.space_after = Pt(2)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.bold = True
    run.font.name = "Calibri"
    run.font.size = Pt(10.5)
    run.font.color.rgb = RGBColor(109, 40, 217)
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
        r_pre.font.size = Pt(10)
        r_pre.font.color.rgb = RGBColor(15, 23, 42)
    run = p.add_run(text)
    run.font.name = "Calibri"
    run.font.size = Pt(10)
    run.font.color.rgb = RGBColor(51, 65, 85)
    return p


def add_styled_table(doc, headers, data, col_widths=None):
    table = doc.add_table(rows=len(data) + 1, cols=len(headers))
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
            run.font.size = Pt(9.5)
            run.font.bold = True
            run.font.color.rgb = RGBColor(255, 255, 255)

    for r_idx, row in enumerate(data):
        row_cells = table.rows[r_idx + 1].cells
        bg_col = "F8FAFC" if r_idx % 2 == 0 else "FFFFFF"
        for c_idx, val in enumerate(row):
            row_cells[c_idx].text = str(val)
            set_cell_background(row_cells[c_idx], bg_col)
            set_cell_margins(row_cells[c_idx], top=75, bottom=75, left=100, right=100)
            p = row_cells[c_idx].paragraphs[0]
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            p.paragraph_format.line_spacing = 1.1
            for run in p.runs:
                run.font.name = "Calibri"
                run.font.size = Pt(9)
                run.font.color.rgb = RGBColor(30, 41, 59)

    if col_widths:
        for row in table.rows:
            for idx, w in enumerate(col_widths):
                row.cells[idx].width = Inches(w)

    doc.add_paragraph().paragraph_format.space_after = Pt(6)


def build_document():
    doc = Document()

    for s in doc.sections:
        s.top_margin = Inches(0.75)
        s.bottom_margin = Inches(0.75)
        s.left_margin = Inches(0.8)
        s.right_margin = Inches(0.8)

        header = s.header
        p_head = header.paragraphs[0]
        p_head.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        p_head.paragraph_format.space_after = Pt(2)
        run_h = p_head.add_run("ARTFOLIO | Presentación de Inversión (<10 min) + Manual de Defensa Técnica y Archivos")
        run_h.font.name = "Calibri"
        run_h.font.size = Pt(8.5)
        run_h.font.color.rgb = RGBColor(148, 163, 184)

        footer = s.footer
        p_foot = footer.paragraphs[0]
        p_foot.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_foot.paragraph_format.space_before = Pt(2)
        run_f = p_foot.add_run("Dossier Ejecutivo Actualizado — Arquitectura, Innovaciones de Plataforma y Guía Técnica — Artfolio")
        run_f.font.name = "Calibri"
        run_f.font.size = Pt(8.5)
        run_f.font.color.rgb = RGBColor(148, 163, 184)

    # =========================================================================
    # PORTADA
    # =========================================================================
    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_before = Pt(18)
    p_title.paragraph_format.space_after = Pt(4)
    r_title = p_title.add_run("ARTFOLIO")
    r_title.bold = True
    r_title.font.name = "Calibri"
    r_title.font.size = Pt(32)
    r_title.font.color.rgb = RGBColor(30, 27, 75)

    p_sub = doc.add_paragraph()
    p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_before = Pt(0)
    p_sub.paragraph_format.space_after = Pt(10)
    r_sub = p_sub.add_run("Dossier de Inversión (< 10 Minutos) y Manual de Defensa Técnica de Arquitectura, Innovaciones y Código")
    r_sub.bold = True
    r_sub.font.name = "Calibri"
    r_sub.font.size = Pt(13)
    r_sub.font.color.rgb = RGBColor(79, 70, 229)

    add_styled_table(
        doc,
        ["Sección del Documento", "Propósito en la Evaluación", "Audiencia / Uso"],
        [
            [
                "PARTE I: Pitch de Inversión (< 10 min)",
                "Presentación comercial ejecutiva de Artfolio por categorías (Origen, Zero-Storage, Comparativa de Mercado, Recorrido Funcional y Gobernanza/Escalabilidad).",
                "Exposición directa al Profesor en rol de Cliente / Inversor de Capital de Riesgo."
            ],
            [
                "PARTE II: Defensa de Proveedores y Whitelists",
                "Ingeniería inversa de Whitelists, Anti-SSRF, enlaces reales y funcionales de Pinterest, DeviantArt, Sketchfab, YouTube, Spotify y Google Docs.",
                "Respaldo técnico ante preguntas sobre integraciones externas y scraping ético."
            ],
            [
                "PARTE III: Anexo Q&A de Arquitectura e Innovaciones",
                "Servicio de Mail, MySQL, Visitantes, Países, Directorio de Artistas, Buscador Facetado, Vista Pinterest de Obra, Tableros de Colecciones, Moderación, Auditoría y +18.",
                "Interrogatorio técnico exhaustivo con ubicación exacta de archivos y líneas clave."
            ]
        ],
        col_widths=[2.1, 2.7, 2.0]
    )

    # =========================================================================
    # PARTE I: PRESENTACIÓN AL CLIENTE / INVERSOR (< 10 MINUTOS)
    # =========================================================================
    add_part_banner(
        doc,
        "PARTE I — EXPOSICIÓN EJECUTIVA CRONOMETRADA (DURACIÓN TOTAL: 9 MINUTOS)",
        "Presentación Comercial y de Producto para el Cliente / Inversor",
        "Estructura en 5 categorías progresivas para demostrar el valor comercial, la viabilidad financiera y la experiencia interactiva de Artfolio en menos de 10 minutos."
    )

    add_styled_table(
        doc,
        ["Categoría de la Presentación", "Tiempo Estimado", "Mensaje Central para el Inversor"],
        [
            ["Categoría 1: Origen, Problema y Objetivo", "Min 0:00 – 1:45 (1m 45s)", "Unificar el portafolio creativo multidisciplinario sin pagar costos millonarios de almacenamiento en la nube."],
            ["Categoría 2: Modelo 'Zero-Binary Storage'", "Min 1:45 – 3:15 (1m 30s)", "Márgenes operativos >95%: las CDNs gigantes pagan el ancho de banda; Artfolio aporta la curaduría e inteligencia."],
            ["Categoría 3: Comparativa Competitiva", "Min 3:15 – 5:00 (1m 45s)", "Superior a Linktree en inmersión visual y más versátil que ArtStation/Behance en soporte multi-formato y ética IA."],
            ["Categoría 4: Recorrido de Funciones Clave", "Min 5:00 – 7:45 (2m 45s)", "Directorio de Artistas, Buscador Facetado, Vista Detalle Pinterest, Tableros Slideshow, Onboarding y Filtro +18."],
            ["Categoría 5: Seguridad y Cierre de Inversión", "Min 7:45 – 9:00 (1m 15s)", "Auditoría integral del sistema, moderación activa, catálogo autolimpiante y 173 tests automatizados listos para escalar."]
        ],
        col_widths=[2.2, 1.5, 3.1]
    )

    # --- CATEGORÍA 1 ---
    add_h1(doc, "Categoría 1: El Origen, el Problema de Mercado y el Objetivo (Min 0:00 – 1:45)")
    add_p(
        doc,
        "Hoy en día, el creador digital moderno es multidisciplinario: un mismo profesional publica ilustraciones en Pinterest o DeviantArt, sube reels de animación a YouTube, aloja modelos 3D interactivos en Sketchfab, compone bandas sonoras en Spotify y redacta guiones o casos de estudio en Google Docs.",
        bold_prefix="1. El Origen del Proyecto (El Dolor del Creador): "
    )
    add_p(
        doc,
        "Cuando un reclutador o estudio quiere evaluar a ese artista, recibe entre 5 y 7 enlaces dispersos. El reclutador debe saltar entre pestañas con interfaces distintas, anuncios y distracciones, perdiendo más del 40% de conversión en contratación. Por otro lado, desde la perspectiva de un inversor tecnológico, crear una red social tradicional donde los usuarios suban archivos pesados (videos 4K, mallas 3D, audio sin compresión) destruye la rentabilidad inicial debido a las facturas exponenciales de almacenamiento en Amazon S3 y transferencia CDN.",
        bold_prefix="2. El Problema Financiero para el Inversor: "
    )
    add_p(
        doc,
        "Artfolio nace para resolver ambos problemas simultáneamente: es un Hub de Curaduría Multimedia Universal que unifica imágenes, video, 3D, audio y documentos interactivos dentro de una sola galería profesional con estética de cine, operando bajo una arquitectura 'Zero-Binary Storage' (Cero Almacenamiento Binario Pesado).",
        bold_prefix="3. La Solución y Objetivo de Negocio: "
    )

    add_callout(
        doc,
        "El creador digital actual no quiere otra red social donde resubir gigabytes de trabajo; quiere un escaparate unificado que exhiba su talento donde ya está alojado con máxima fidelidad visual y cero fricción.",
        title="TESIS DE INVERSIÓN CENTRAL"
    )

    # --- CATEGORÍA 2 ---
    add_h1(doc, "Categoría 2: Modelo de Negocio 'Zero-Binary Storage' (Min 1:45 – 3:15)")
    add_p(
        doc,
        "En lugar de permitir que los usuarios carguen videos de 500 MB o imágenes RAW que colapsarían la factura mensual de AWS S3 y Cloudflare, Artfolio almacena únicamente la URL original del proveedor (YouTube, Pinterest, DeviantArt, Sketchfab, Google Docs, Spotify). En el momento en que se ingresa el enlace, nuestro backend analiza la URL, consulta el protocolo oEmbed o extrae los metadatos necesarios y guarda en la base de datos relacional MySQL solamente cadenas de texto (título, descripción, autor, miniatura optimizada y código embebible).",
        bold_prefix="1. ¿Cómo funciona técnicamente 'Zero-Binary Storage'?: "
    )
    add_p(
        doc,
        "El costo marginal por cada 10,000 obras nuevas registradas en Artfolio es prácticamente cero dólares en almacenamiento binario (unos pocos megabytes en MySQL para registros alfanuméricos). El ancho de banda de transmitir el video 4K o el modelo 3D con texturas lo paga Google (YouTube), Pinterest o Sketchfab. Artfolio captura el 100% de la experiencia visual y los datos de interacción con un margen bruto superior al 95%.",
        bold_prefix="2. Ventaja Financiera Radical para el Inversor: "
    )

    # --- CATEGORÍA 3 ---
    add_h1(doc, "Categoría 3: Comparativa Competitiva de Mercado (Min 3:15 – 5:00)")
    add_styled_table(
        doc,
        ["Criterio de Evaluación", "Artfolio", "Linktree / Beacons", "ArtStation / Behance"],
        [
            ["Experiencia Multimedia", "Embebido interactivo nativo (video, audio, 3D y docs en vivo).", "Botones de texto plano y enlaces externos salientes.", "Centrado casi exclusivamente en imágenes fijas 2D."],
            ["Costo de Almacenamiento", "Zero-Binary (almacena metadatos y vectores).", "Bajo, pero sin inmersión visual.", "Altísimo (subida binaria pesada)."],
            ["Curaduría por Colecciones", "Colecciones públicas, no-listadas con token y privadas.", "Listas estáticas de enlaces.", "Carpetas rígidas orientadas solo a venta."],
            ["Buscador y Directorio", "Buscador global facetado + Directorio B2B de Artistas.", "Inexistente (cada usuario es una isla aislada).", "Explorador general saturado de publicidad."],
            ["Gobernanza y Ética IA", "Declaración ética de IA por obra y filtros de contenido sensible.", "Sin control ni declaración de contenidos.", "Controversia constante por scraping masivo de IA."]
        ],
        col_widths=[1.5, 1.8, 1.7, 1.8]
    )

    # --- CATEGORÍA 4 ---
    add_h1(doc, "Categoría 4: Recorrido de Funciones Clave del Producto (Min 5:00 – 7:45)")
    add_p(
        doc,
        "Durante este bloque de la presentación, se demuestra al evaluador cómo interactúan los usuarios en la plataforma a través de sus pilares funcionales de última generación:"
    )
    add_bullet(
        doc,
        "Directorio profesional B2B para reclutadores y estudios que permite filtrar artistas por habilidades técnicas, software de dominio (Photoshop, Blender, Unreal, etc.), país de residencia, disponibilidad para comisiones/contratación y artistas verificados, con ordenamiento por impacto (más seguidos, más apreciados o recientes).",
        bold_prefix="1. Directorio Global de Artistas (/artists): "
    )
    add_bullet(
        doc,
        "Explorador de obras facetado y consistente con el diseño de artistas, que incluye dropdowns de ordenamiento (recientes, populares, más vistas, más apreciadas), filtrado por tipo de medio (imagen, video, 3D, audio, doc), software, habilidades curadas y conmutador discreto de contenido maduro (+18) en una grilla limpia y responsiva.",
        bold_prefix="2. Buscador Avanzado de Obras con Taxonomía Curada (/explore): "
    )
    add_bullet(
        doc,
        "Página dedicada con estética Behance/Pinterest que complementa el modal rápido. Presenta la obra en máxima fidelidad, estadísticas en tiempo real (visitas únicas y likes asincrónicos), badges estandarizados (formato, 18+, IA), herramientas utilizadas, autor con biografía e insignia de verificación, comentarios comunitarios y selector rápido de colecciones.",
        bold_prefix="3. Vista Detalle de Obra al Estilo Pinterest (/artworks/{id}): "
    )
    add_bullet(
        doc,
        "Las colecciones del usuario cuentan con cabeceras visuales tipo mosaico de portadas destacadas (preview_artwork_ids), reordenamiento personalizado de piezas (sort_order), compartición segura de colecciones no-listadas mediante share_token aleatorio de 32 caracteres, y un Modo Presentación (Slideshow) a pantalla completa ideal para pitches ante clientes.",
        bold_prefix="4. Tableros de Colección con Portadas y Slideshow (/collections/{id}): "
    )
    add_bullet(
        doc,
        "Onboarding en 3 pasos con geolocalización inteligente y selector de países con banderas en caché de 30 días, editor visual interactivo de encuadre (drag & zoom con coordenadas matemáticas X, Y, Zoom) para Avatar circular y Banner panorámico sin recortar archivos en el servidor.",
        bold_prefix="5. Onboarding Guiado y Perfil de Artista con Drag & Zoom: "
    )
    add_bullet(
        doc,
        "Protección de audiencia mediante el atributo is_mature con desenfoque automático (blur) en el feed para usuarios anónimos o que hayan activado el filtro en su perfil, y tarjetas de obra rediseñadas con CSS Grid simétrico de 3 columnas que alinea matemáticamente títulos, autores, métricas y acciones.",
        bold_prefix="6. Protección de Contenido Maduro (+18) y Tarjetas Simétricas: "
    )

    # --- CATEGORÍA 5 ---
    add_h1(doc, "Categoría 5: Gobernanza, Moderación y Cierre de Inversión (Min 7:45 – 9:00)")
    add_bullet(
        doc,
        "Panel exclusivo para administradores (/admin/audit-logs) que registra con trazabilidad forense inmutable los inicios de sesión, cambios de perfil, suspensiones de cuentas, eliminación/restauración de obras y resolución de reportes, con filtrado por severidad (info, warning, danger).",
        bold_prefix="1. Módulo de Auditoría del Sistema (Audit Logs): "
    )
    add_bullet(
        doc,
        "Gestión centralizada de habilidades, herramientas de software y estilos artísticos oficiales (/admin/tags) que nutren automáticamente el onboarding, el perfil y los filtros del explorador.",
        bold_prefix="2. Taxonomía Curada y Ecosistema de Tags: "
    )
    add_bullet(
        doc,
        "Comando automatizable ('artworks:check-links') que verifica el estado HTTP de las obras, oculta enlaces caídos (404/410) y envía correos de alerta automática al creador para que repare el enlace sin romper la experiencia comunitaria.",
        bold_prefix="3. Catálogo Autolimpiante (Self-Healing Catalog): "
    )
    add_bullet(
        doc,
        "Papelera de reciclaje con ventana de recuperación de 30 días ('artworks:purge-trash'), bloqueo bidireccional entre usuarios (invisibiliza obras en ambos sentidos) y suspensión inmediata de infractores mediante middleware en caliente (EnsureUserIsActive).",
        bold_prefix="4. Moderación Comunitaria y Resiliencia Técnica: "
    )

    doc.add_page_break()

    # =========================================================================
    # PARTE II: DEFENSA TÉCNICA — WHITELISTS Y PROVEEDORES MULTIMEDIA
    # =========================================================================
    add_part_banner(
        doc,
        "PARTE II — DEFENSA TÉCNICA DE PROVEEDORES MULTIMEDIA Y WHITELISTS",
        "Funcionamiento Interno y Ubicación Exacta de Archivos (Whitelists, YouTube, Pinterest, DeviantArt, Sketchfab y Google Docs)",
        "Guía detallada para responder con precisión quirúrgica cuando el evaluador interrogue sobre cómo operan las integraciones externas, el bypass de bloqueos y la ubicación de su código."
    )

    # --- 2.1 WHITELISTS ---
    add_h1(doc, "1. Sistema de Whitelists (Listas Blancas) y Protección Anti-SSRF")
    add_p(
        doc,
        "Como el servidor backend de Artfolio realiza peticiones HTTP salientes para extraer miniaturas y metadatos de los enlaces ingresados, aceptar cualquier URL arbitraria expondría al servidor a ataques de SSRF (Server-Side Request Forgery), donde un atacante podría escanear la red local (ej. 127.0.0.1, 192.168.x.x) o inyectar servidores de comando maliciosos.",
        bold_prefix="¿Por qué existe y qué problema resuelve?: "
    )
    add_bullet(doc, "Paso 1 (Validación de Formato y Bloqueo de Red Local): Se valida la URL con filter_var($url, FILTER_VALIDATE_URL) y parse_url(). Luego se bloquea explícitamente cualquier dirección IP cruda (filter_var($host, FILTER_VALIDATE_IP)) o el dominio 'localhost' (líneas 68–70 de EmbedService.php), neutralizando ataques SSRF.", bold_prefix="Filtro Anti-SSRF: ")
    add_bullet(doc, "Paso 2 (Normalización del Host): Se normaliza a minúsculas y se limpia el prefijo 'www.' mediante preg_replace('/^www\\./', '', $host).", bold_prefix="Limpieza de Dominio: ")
    add_bullet(doc, "Paso 3 (Cotejo Estricto contra $allowedDomains): Se verifica contra la lista de proveedores permitidos (pinterest, deviantart, spotify, youtube, sketchfab, vimeo, soundcloud, googledocs, imgur). Solo se autoriza si el host coincide exactamente o es un subdominio legítimo terminado en '.dominio' (str_ends_with($cleanHost, '.' . $domain)).", bold_prefix="Verificación de Lista Blanca: ")

    add_code_box(
        doc,
        "1) Motor Principal de Whitelist y Bloqueo Anti-SSRF:\n"
        "   -> app/Services/EmbedService.php\n"
        "      - Propiedad $allowedDomains (Líneas 12 a 32): Define los dominios autorizados.\n"
        "      - Método processUrl(string $url) (Líneas 41 a 90): Bloqueo anti-IP/localhost y verificación de Whitelist.\n\n"
        "2) Validación de Entrada Formulario Obra:\n"
        "   -> app/Http/Requests/ArtworkRequest.php (Líneas 22 a 33): Valida 'source_url' => ['required', 'url', 'max:500'].\n\n"
        "3) Controlador que Invoca la Whitelist:\n"
        "   -> app/Http/Controllers/ArtworkController.php\n"
        "      - previewUrl() (Líneas 27 a 53): Endpoint AJAX (/artworks/preview) para validar en vivo.\n"
        "      - store() (Líneas 58 a 95): Revalida en backend antes de persistir en MySQL.\n\n"
        "4) Whitelist Adicional para Imágenes de Avatar y Banner:\n"
        "   -> app/Http/Requests/ProfileUpdateRequest.php (Líneas 32 a 85): Valida HTTPS y CDNs autorizadas.",
        title="UBICACIÓN EXACTA DE ARCHIVOS — WHITELISTS Y SEGURIDAD HTTP"
    )

    # --- 2.2 YOUTUBE ---
    add_h1(doc, "2. Implementación de Enlaces de Videos de YouTube")
    add_p(
        doc,
        "YouTube permite múltiples formatos de enlace (watch?v=..., enlaces cortos youtu.be/..., shorts/... y /embed/...). Artfolio extrae su ID con regex, consulta el API oEmbed oficial para el título, evalúa miniaturas HD dinámicamente y genera un reproductor 16:9 con sandboxing seguro.",
        bold_prefix="Flujo Técnico: "
    )
    add_bullet(doc, "Extracción de ID: Regex #(?:youtube\\.com/(?:watch\\?.*v=|shorts/|embed/)|youtu\\.be/)([a-zA-Z0-9_-]{11})#i.", bold_prefix="Paso 1: ")
    add_bullet(doc, "Consulta oEmbed: Petición GET con timeout de 5s a https://www.youtube.com/oembed?url=... para obtener el título.", bold_prefix="Paso 2: ")
    add_bullet(doc, "Detección de Miniatura HD: Petición HEAD a maxresdefault.jpg; si da 200 la conserva, si da 404 degrada a hqdefault.jpg.", bold_prefix="Paso 3: ")
    add_bullet(doc, "Optimización en Tarjeta: En Artwork.php, getPreviewUrlAttribute() sirve hqdefault.jpg en la grilla para acelerar el renderizado.", bold_prefix="Paso 4: ")

    add_code_box(
        doc,
        "-> app/Services/EmbedService.php (Método fetchYouTubeData, líneas 385 a 444).\n"
        "-> app/Models/Artwork.php (Accessor getPreviewUrlAttribute, líneas 44 a 57).\n"
        "-> resources/views/components/viewer-modal.blade.php (Renderizado en iframe seguro 16:9).",
        title="UBICACIÓN EXACTA DE ARCHIVOS — YOUTUBE"
    )

    # --- 2.3 PINTEREST ---
    add_h1(doc, "3. Implementación de Enlaces de Pinterest y Extracción de Imagen HD")
    add_p(
        doc,
        "Pinterest presenta tres desafíos técnicos: enlaces acortados pin.it, miniaturas por defecto de baja resolución (/236x/) y protección contra hotlinking en i.pinimg.com. Artfolio los resuelve mediante resolución de redirecciones, promoción regex a /originals/, fallback OpenGraph y el atributo referrerpolicy='no-referrer' en el frontend.",
        bold_prefix="Ingeniería de Extracción: "
    )
    add_bullet(doc, "Resolución pin.it: Sigue redirecciones HTTP y extrae la URL canónica del Pin ($res->effectiveUri()).", bold_prefix="Paso 1: ")
    add_bullet(doc, "Promoción a Original: Transforma /236x/ a /originals/ mediante preg_replace y verifica con HEAD de 2 segundos; si falla, degrada a /736x/.", bold_prefix="Paso 2: ")
    add_bullet(doc, "Doble Resolución: preview_url entrega /474x/ en tarjetas mientras que high_res_url entrega /originals/ en el visor de pantalla completa.", bold_prefix="Paso 3: ")
    add_bullet(doc, "Protección Anti-Hotlink: Las etiquetas <img> incluyen referrerpolicy='no-referrer', permitiendo cargar la imagen directamente desde la CDN de Pinterest.", bold_prefix="Paso 4: ")

    add_code_box(
        doc,
        "-> app/Services/EmbedService.php (Método fetchPinterestData, líneas 147 a 250).\n"
        "-> app/Models/Artwork.php (Accessors preview_url y high_res_url, líneas 44 a 68).\n"
        "-> resources/views/components/artwork-card.blade.php y viewer-modal.blade.php (referrerpolicy='no-referrer').",
        title="UBICACIÓN EXACTA DE ARCHIVOS — PINTEREST"
    )

    # --- 2.4 DEVIANTART, SKETCHFAB Y OTROS ---
    add_h1(doc, "4. Implementación de DeviantArt, Sketchfab (3D) y Google Docs")
    add_bullet(
        doc,
        "DeviantArt (app/Services/EmbedService.php, L.256-315): Consulta backend.deviantart.com/oembed capturando la URL del archivo de alta definición en $data['url'] y atribuyendo el nombre del artista, con fallback scraping de etiquetas <meta property='og:image'>.",
        bold_prefix="DeviantArt (Ilustración Digital): "
    )
    add_bullet(
        doc,
        "Sketchfab (app/Services/EmbedService.php, L.321-379): Consulta sketchfab.com/oembed, extrayendo el reproductor 3D interactivo en WebGL con iframe sandbox='allow-scripts allow-same-origin allow-popups' y miniatura en alta resolución.",
        bold_prefix="Sketchfab (Modelos 3D Interactivos): "
    )
    add_bullet(
        doc,
        "Google Docs y Drive (app/Services/EmbedService.php, L.490-560): Convierte enlaces de edición (/edit, ?usp=sharing) a la vista de lectura limpia /preview y genera la miniatura oficial mediante drive.google.com/thumbnail?id={docId}&sz=w800.",
        bold_prefix="Google Docs / Sheets / Slides / Drive: "
    )

    doc.add_page_break()

    # =========================================================================
    # PARTE III: ANEXO INDEPENDIENTE DE PREGUNTAS DEL PROFESOR (ARQUITECTURA)
    # =========================================================================
    add_part_banner(
        doc,
        "PARTE III — ANEXO DE INTERROGATORIO TÉCNICO Y ARQUITECTURA DEL SISTEMA",
        "Servicio de Mail, MySQL, Visitantes, Países, Directorio de Artistas, Buscador Facetado, Obras Pinterest, Colecciones, Auditoría y +18",
        "Respuestas exhaustivas organizadas en 10 preguntas maestras con diagramación técnica, flujos lógicos y referencias de archivos para responder cualquier pregunta del evaluador."
    )

    # --- PREGUNTA 1: MAIL ---
    add_h1(doc, "Pregunta 1: ¿Cómo funciona el Servicio de Mail y la Confirmación de Cuenta, cómo se accede a él y qué archivos lo manejan?")
    add_p(
        doc,
        "Artfolio implementa el contrato MustVerifyEmail en el modelo User (app/Models/User.php, L.16). Al registrarse, Laravel emite un enlace firmado criptográficamente SHA-1 (/verify-email/{id}/{hash}). Hasta su confirmación, el middleware 'verified' bloquea el acceso a funciones privadas. En desarrollo, Mailpit escucha en SMTP (127.0.0.1:1025) y su bandeja web interactiva se visualiza en http://127.0.0.1:8025. Contamos además con el comando artesanal 'php artisan mail:test {email?}' para pruebas inmediatas por consola.",
        bold_prefix="Resumen Operativo: "
    )
    add_code_box(
        doc,
        "-> app/Models/User.php (L.16): implements MustVerifyEmail.\n"
        "-> routes/auth.php (L.40-55): Rutas de verificación firmada.\n"
        "-> app/Mail/ContactArtist.php y app/Mail/LinkDownAlert.php: Correos transaccionales.\n"
        "-> routes/console.php (L.29-46): Comando de consola 'php artisan mail:test'.\n"
        "-> Bandeja Web en Vivo: http://127.0.0.1:8025 (Mailpit).",
        title="ARCHIVOS — SERVICIO DE MAIL Y CONFIRMACIÓN"
    )

    # --- PREGUNTA 2: BASE DE DATOS ---
    add_h1(doc, "Pregunta 2: ¿Dónde y cómo se almacenan los datos de los usuarios y sus obras?")
    add_p(
        doc,
        "Se almacenan en MySQL mediante Eloquent ORM bajo el principio Zero-Binary Storage. No se guardan archivos pesados, sino registros normalizados:",
        bold_prefix="Arquitectura de Datos: "
    )
    add_bullet(doc, "Tabla 'users': Credenciales (Bcrypt), identidad, arrays JSON (skills, software, hiring_types), crop de avatar (crop_x, crop_y, crop_zoom) y crop de banner (banner_crop_x, banner_crop_y, banner_crop_zoom), estatus 'active'|'suspended' y campo 'is_verified'.", bold_prefix="Usuarios: ")
    add_bullet(doc, "Tabla 'artworks': user_id, title, category, provider, render_type, source_url, embed_html, has_ai, is_mature, views_count, likes_count, status, link_status y deleted_at (SoftDeletes). Con índice compuesto ['status', 'category', 'created_at'] para búsquedas ultrarrápidas.", bold_prefix="Obras: ")
    add_bullet(doc, "Tablas Relacionales: 'artwork_likes' (apreciaciones), 'artwork_comments' (comentarios), 'bookmarks' (favoritos), 'collections' y 'artwork_collection' (con preview_artwork_ids y sort_order), 'reports' (moderación), 'user_blocks' (bloqueos mutuos), 'curated_tags' (taxonomía) y 'audit_logs' (auditoría).", bold_prefix="Ecosistema Relacional: ")

    add_code_box(
        doc,
        "-> database/migrations/: Migraciones versionadas y fechadas cronológicamente.\n"
        "-> app/Models/: User.php, Artwork.php, Collection.php, Comment.php, CuratedTag.php, AuditLog.php.\n"
        "-> database/seeders/RichCommunitySeeder.php: Datos semilla con 100% de enlaces reales y verificados (HTTP 200).",
        title="ARCHIVOS — MODELOS Y BASE DE DATOS"
    )

    # --- PREGUNTA 3: VISITANTE GUEST ---
    add_h1(doc, "Pregunta 3: ¿Cómo accede un usuario visitante sin tener cuenta y qué archivos manejan eso?")
    add_p(
        doc,
        "Rutas públicas sin middleware 'auth' (home '/', '/explore', '/artists', '/artworks/{id}', '/artista/{slug}', '/collections/{collection}'). Los controladores comprueban Auth::check(). Si un visitante intenta dar like, guardar en favoritos o añadir a colección, el evento Alpine.js $dispatch('open-guest-modal', { context: '...' }) intercepta la acción y abre un modal persuasivo sin recargar.",
        bold_prefix="Acceso Público con Intercepción Gradual: "
    )
    add_code_box(
        doc,
        "-> routes/web.php: Rutas públicas fuera del grupo de autenticación.\n"
        "-> resources/views/components/guest-modal.blade.php: Modal reactivo interceptor.\n"
        "-> resources/views/components/artwork-card.blade.php y show.blade.php: Directivas @auth / @else.",
        title="ARCHIVOS — INTERCEPTOR DE VISITANTES"
    )

    # --- PREGUNTA 4: PAÍSES ---
    add_h1(doc, "Pregunta 4: ¿De dónde se sacan los países disponibles en el registro/onboarding y perfil, y qué archivo los maneja?")
    add_p(
        doc,
        "Ruta interna GET /api/countries con Cache::remember() por 30 días y triple nivel de respaldo: (1) Repositorio GitHub mledoze/countries (240+ países con banderas), (2) API pública restcountries.com y (3) Arreglo local estático en routes/web.php garantizando funcionamiento offline absoluto.",
        bold_prefix="Triple Fallback: "
    )
    add_code_box(
        doc,
        "-> routes/web.php (L.15-57): Endpoint GET /api/countries con caché 30d.\n"
        "-> resources/views/onboarding/step1.blade.php y tab-profile.blade.php: Consumo reactivo Alpine.js.",
        title="ARCHIVOS — API DE PAÍSES"
    )

    # --- PREGUNTA 5: AVATAR, SESIÓN, COLECCIONES Y PAPELERA ---
    add_h1(doc, "Pregunta 5: ¿Cómo funcionan el Drag & Zoom de Avatar/Banner, la Expiración de Sesión y la Papelera de 30 Días?")
    add_bullet(
        doc,
        "Editor interactivo de arrastrar y zoom (1x-3x) que guarda coordenadas matemáticas porcentuales en la tabla 'users'. El perfil público aplica transform: translate(X%, Y%) scale(Zoom) en CSS sin recortar ni duplicar imágenes.",
        bold_prefix="Avatar y Banner Drag & Zoom: "
    )
    add_bullet(
        doc,
        "Sesión de 10 min en config/session.php. El componente session-timeout-modal.blade.php detecta 8 min de inactividad, muestra cuenta regresiva de 120s y permite extender la sesión mediante POST /session/keep-alive.",
        bold_prefix="Aviso de Inactividad de Sesión: "
    )
    add_bullet(
        doc,
        "Trait SoftDeletes en Artwork.php llena deleted_at. La papelera (/trash) permite restaurar o purgar. El comando 'artworks:purge-trash' elimina automáticamente obras con más de 30 días en papelera.",
        bold_prefix="Papelera de 30 Días: "
    )

    # --- PREGUNTA 6: DIRECTORIO DE ARTISTAS Y BUSCADOR FACETADO ---
    add_h1(doc, "Pregunta 6: ¿Cómo funcionan el Directorio de Artistas (/artists) y los nuevos Filtros Avanzados de Obras (/explore)?")
    add_p(
        doc,
        "El Directorio de Artistas (app/Http/Controllers/ArtistExplorerController.php) ofrece un motor de búsqueda y reclutamiento creativo. Permite buscar por nombre, biografía o especialidad, y filtrar por: país de residencia (conectado a /api/countries), disponibilidad de comisiones ('available', 'busy', 'not_available'), herramientas de software (Photoshop, Blender, Unreal, etc.), habilidades técnicas y artistas verificados. Admite ordenamientos por seguidores, apreciaciones totales recibidas o recencia. Las tarjetas (resources/views/components/artist-card.blade.php) cargan en mosaico las 3 obras más destacadas de cada creador evitando consultas N+1.",
        bold_prefix="Directorio Global de Artistas: "
    )
    add_p(
        doc,
        "El Buscador de Obras (app/Http/Controllers/ExplorerController.php) se unificó estéticamente con el directorio de artistas. Emplea dropdowns limpios para ordenamiento (recientes, populares, más vistas, más apreciadas), botones unificados por tipo de medio (imagen, video, 3D, audio, doc) y filtros combinables por software y estilo artístico mediante la tabla 'curated_tags'. El selector redundante de IA se eliminó del buscador general para otorgar una interfaz profesional sin distracciones, manteniendo la insignia ética en cada obra individual.",
        bold_prefix="Buscador Facetado de Obras: "
    )
    add_code_box(
        doc,
        "-> app/Http/Controllers/ArtistExplorerController.php: Lógica del directorio de artistas.\n"
        "-> resources/views/artists/index.blade.php y components/artist-card.blade.php: Vistas del directorio.\n"
        "-> app/Http/Controllers/ExplorerController.php: Filtros combinados de obras.\n"
        "-> resources/views/explorer/index.blade.php: Grilla responsiva de obras con filtros estilizados.",
        title="ARCHIVOS — DIRECTORIO DE ARTISTAS Y EXPLORADOR DE OBRAS"
    )

    # --- PREGUNTA 7: VISTA DETALLE DE OBRA, LIKES Y COMENTARIOS ---
    add_h1(doc, "Pregunta 7: ¿Cómo funciona la Vista Detalle de Obra (/artworks/{id}), el Sistema de Likes y los Comentarios?")
    add_p(
        doc,
        "La vista dedicada (resources/views/artworks/show.blade.php) ofrece una experiencia inmersiva estilo Pinterest/Behance. Integra un contador de visualizaciones persistente administrado por ArtworkViewController.php mediante cookies de sesión para evitar visitas duplicadas artificiales. El sistema de likes (ArtworkLikeController.php) opera asincrónicamente mediante peticiones POST AJAX a /artworks/{id}/like; cuando un usuario da o quita un like, se emite el evento global 'artwork-like-updated' en Alpine.js que actualiza instantáneamente todas las tarjetas visibles en la página. Además, incorpora un sistema de comentarios comunitarios (ArtworkCommentController.php) con soporte de eliminación garantizada tanto para el autor del comentario como para el dueño de la obra.",
        bold_prefix="Mecanismo de Interacción y Feedback: "
    )
    add_p(
        doc,
        "En la cabecera de la obra, las insignias de formato (IMAGE, VIDEO, 3D, AUDIO, DOC), contenido sensible (18+) y procedencia ética (AI) están estandarizadas con una paleta índigo armónica (text-indigo-700 dark:text-indigo-300), ocultando textos genéricos redundantes como 'Todos los derechos reservados' para priorizar la apreciación estética.",
        bold_prefix="Estandarización Visual de Badges: "
    )
    add_code_box(
        doc,
        "-> app/Http/Controllers/ArtworkViewController.php: Registro anti-duplicado de visitas únicas.\n"
        "-> app/Http/Controllers/ArtworkLikeController.php: Endpoints de like asíncrono.\n"
        "-> app/Http/Controllers/ArtworkCommentController.php: CRUD y moderación de comentarios.\n"
        "-> resources/views/artworks/show.blade.php: Plantilla completa de la obra.",
        title="ARCHIVOS — VISTA DETALLE, LIKES, VISITAS Y COMENTARIOS"
    )

    # --- PREGUNTA 8: TABLEROS DE COLECCIÓN, PORTADAS Y SLIDESHOW ---
    add_h1(doc, "Pregunta 8: ¿Cómo funcionan los Tableros de Colección con Portadas Mosaico, Reordenamiento y Modo Slideshow?")
    add_p(
        doc,
        "Las colecciones (app/Models/Collection.php) evolucionaron a tableros visuales inspirados en Pinterest. En la tabla 'collections' se añadieron las columnas 'preview_artwork_ids' (array JSON que guarda las obras destacadas para la portada mosaico) y 'sort_order' (criterio de ordenamiento personalizado o por fecha). La vista de colección (resources/views/collections/show.blade.php) presenta un header dinámico con las miniaturas de portada y un botón para iniciar el 'Modo Presentación' (x-slideshow-modal.blade.php), que permite recorrer todas las obras de la colección a pantalla completa mediante teclado o controles táctiles, ideal para presentaciones de portafolio o pitches de diseño ante clientes.",
        bold_prefix="Tableros Creativos Dinámicos: "
    )
    add_code_box(
        doc,
        "-> app/Models/Collection.php: Casts de preview_artwork_ids y generación de share_token.\n"
        "-> app/Http/Controllers/CollectionController.php: Métodos show, edit, toggleArtwork y quickCreate.\n"
        "-> resources/views/collections/show.blade.php: Header con mosaico de portadas.\n"
        "-> resources/views/components/slideshow-modal.blade.php: Visor cinematográfico a pantalla completa.",
        title="ARCHIVOS — TABLEROS DE COLECCIÓN Y SLIDESHOW"
    )

    # --- PREGUNTA 9: AUDITORÍA Y TAXONOMÍA EN ADMIN ---
    add_h1(doc, "Pregunta 9: ¿Cómo funcionan los Módulos Administrativos de Auditoría del Sistema (/admin/audit-logs) y Taxonomía Curada (/admin/tags)?")
    add_p(
        doc,
        "Para auditorías de seguridad y gobernanza empresarial, se construyó el servicio app/Services/AuditLogger.php y el modelo app/Models/AuditLog.php. Cada evento crítico en la aplicación (logins fallidos/exitosos, cambios de contraseña, suspensiones de cuentas, eliminación/restauración de obras y resoluciones de reportes) se registra automáticamente con marca de tiempo, usuario, dirección IP, agente de usuario, severidad (info, warning, danger) y un diferencial de valores anteriores/nuevos en formato JSON. El panel (/admin/audit-logs) permite a los administradores filtrar eventos, inspeccionar detalles y exportar reportes de seguridad.",
        bold_prefix="Auditoría Forense de Seguridad: "
    )
    add_p(
        doc,
        "El módulo de Taxonomía (/admin/tags gestionado por TaxonomyController.php y el modelo CuratedTag.php) administra el catálogo centralizado de etiquetas de la plataforma divididas en 'skills' (ej. Concept Art, Digital Artist), 'software' (ej. Blender, Photoshop) y 'styles' (ej. Anime, Sci-Fi). Esto garantiza que el autocompletado en el onboarding, en el perfil del artista y en el buscador de obras sea coherente, profesional y esté libre de etiquetas basura o duplicadas.",
        bold_prefix="Taxonomía Curada Oficial: "
    )
    add_code_box(
        doc,
        "-> app/Services/AuditLogger.php y app/Models/AuditLog.php: Motor de registro forense.\n"
        "-> app/Http/Controllers/Admin/AuditLogController.php: Panel de visualización y filtros de auditoría.\n"
        "-> app/Models/CuratedTag.php y app/Http/Controllers/Admin/TaxonomyController.php: Gestión de tags.\n"
        "-> database/seeders/TaxonomySeeder.php: Catálogo de tags y software pre-poblados.",
        title="ARCHIVOS — AUDITORÍA DEL SISTEMA Y TAXONOMÍA"
    )

    # --- PREGUNTA 10: CONTENIDO MADURO (+18) Y TARJETAS SIMÉTRICAS ---
    add_h1(doc, "Pregunta 10: ¿Cómo funciona la Protección de Contenido Maduro (+18), el Desenfoque Automático y el Diseño Simétrico de Tarjetas en CSS Grid?")
    add_p(
        doc,
        "Para proteger a audiencias menores y cumplir estándares de plataformas globales, se incorporó la columna booleana 'is_mature' en la tabla 'artworks' y el método 'wantsMatureBlurred()' en el modelo User.php. En las tarjetas y visores, las obras con contenido maduro se renderizan con un filtro CSS blur-lg por defecto para visitantes y usuarios con el filtro activo, mostrando un botón discreto de advertencia que permite desenfocar la obra bajo consentimiento explícito sin exponer imágenes no solicitadas.",
        bold_prefix="Protección de Contenido Maduro: "
    )
    add_p(
        doc,
        "En el pie de cada tarjeta de obra (resources/views/components/artwork-card.blade.php), se implementó una estructura basada en CSS Grid de 3 columnas (grid-cols-[1fr_auto_auto] items-center gap-x-2 gap-y-2). Esto garantiza una alineación matemática perfecta: la Fila 1 alinea horizontalmente el Título de la obra con el botón de Likes y el botón de Favoritos; la Fila 2 alinea horizontalmente el Avatar y Nombre del artista con el contador de Vistas y el selector de Colecciones. Además, la Columna 2 alinea verticalmente el Corazón sobre el Ojo (ambos con íconos de tamaño w-4 h-4 y tipografía font-mono de idéntica altura), y la Columna 3 alinea verticalmente el Marcador sobre el selector de Colecciones (ajustado con size='sm' en x-collection-picker.blade.php).",
        bold_prefix="Diseño Simétrico de Tarjetas en CSS Grid: "
    )
    add_code_box(
        doc,
        "-> database/migrations/*_add_is_mature_to_artworks_and_mature_pref_to_users_table.php\n"
        "-> resources/views/components/artwork-card.blade.php: Cuadrícula CSS Grid de 3 columnas simétrica.\n"
        "-> resources/views/components/collection-picker.blade.php: Selector de colecciones escalable (size='sm').\n"
        "-> tests/Feature/MatureContentTest.php: Suite de pruebas de verificación de desenfoque.",
        title="ARCHIVOS — CONTENIDO MADURO Y TARJETAS SIMÉTRICAS"
    )

    # --- TABLA MAESTRA DE ARCHIVOS ---
    add_h1(doc, "Mapa Maestro de Archivos del Proyecto (Referencia Rápida para el Examen)")
    add_styled_table(
        doc,
        ["Módulo / Función Preguntada", "Archivo(s) Principal(es)", "Método o Línea Clave"],
        [
            ["Whitelists y Anti-SSRF", "app/Services/EmbedService.php", "$allowedDomains (L.12-32), processUrl() (L.41-90)"],
            ["Enlaces y Videos de YouTube", "app/Services/EmbedService.php\napp/Models/Artwork.php", "fetchYouTubeData() (L.385-444)\ngetPreviewUrlAttribute() (L.44-57)"],
            ["Enlaces e Imágenes de Pinterest", "app/Services/EmbedService.php\nresources/views/components/artwork-card.blade.php", "fetchPinterestData() (L.147-250)\nreferrerpolicy=\"no-referrer\" (L.40)"],
            ["Enlaces de DeviantArt y Sketchfab", "app/Services/EmbedService.php", "fetchDeviantArtData() (L.256-315)\nfetchSketchfabData() (L.321-379)"],
            ["Enlaces de Google Docs / Drive", "app/Services/EmbedService.php", "fetchGoogleDocsData() (L.490-560)"],
            ["Servicio de Mail y Confirmación", "app/Models/User.php\nroutes/auth.php\nroutes/console.php", "implements MustVerifyEmail (L.16)\nRutas verify-email (L.40-55)\nComando mail:test (L.29-46)"],
            ["Directorio de Artistas", "app/Http/Controllers/ArtistExplorerController.php\nresources/views/artists/index.blade.php", "Filtros por skills, software, país y comisiones\nComponente x-artist-card"],
            ["Buscador Avanzado de Obras", "app/Http/Controllers/ExplorerController.php\nresources/views/explorer/index.blade.php", "Filtros por medio, software, ordenamiento y tags\nGrilla responsiva de tarjetas"],
            ["Vista Detalle Pinterest de Obra", "app/Http/Controllers/ArtworkController.php\nresources/views/artworks/show.blade.php", "ArtworkViewController (visitas)\nArtworkLikeController (likes AJAX)\nArtworkCommentController (comentarios)"],
            ["Tableros de Colección y Slideshow", "app/Http/Controllers/CollectionController.php\nresources/views/collections/show.blade.php", "preview_artwork_ids (mosaico portadas)\nx-slideshow-modal (presentación pantalla completa)"],
            ["Auditoría del Sistema (Admin)", "app/Services/AuditLogger.php\napp/Http/Controllers/Admin/AuditLogController.php", "Registro inmutable de seguridad y severidad\nVista admin/audit_logs/index.blade.php"],
            ["Taxonomía Curada (Admin)", "app/Models/CuratedTag.php\napp/Http/Controllers/Admin/TaxonomyController.php", "CuratedTag::where('type', ...)\nTaxonomySeeder.php"],
            ["Contenido Maduro (+18) y Blur", "app/Models/Artwork.php\napp/Models/User.php", "is_mature, wantsMatureBlurred()\nDesenfoque CSS blur-lg en miniaturas"],
            ["Tarjetas Simétricas de Obra", "resources/views/components/artwork-card.blade.php\nresources/views/components/collection-picker.blade.php", "CSS Grid 3 columnas (grid-cols-[1fr_auto_auto])\nx-collection-picker size='sm' (w-4 h-4)"],
            ["Acceso Visitante (Guest Modal)", "routes/web.php\nresources/views/components/guest-modal.blade.php", "Rutas públicas fuera de auth\nInterceptor @open-guest-modal.window"],
            ["API de Países con Banderas", "routes/web.php\nresources/views/onboarding/step1.blade.php", "GET /api/countries con Caché 30d y Triple Fallback"],
            ["Drag & Zoom Avatar y Banner", "resources/views/profile/partials/tab-profile.blade.php\nresources/views/profile/show.blade.php", "Drag & Zoom -> crop_x/y/zoom y banner_crop_x/y/zoom\ntransform CSS acelerado por GPU"],
            ["Tests Automatizados (173 tests)", "tests/Feature/*.php\nphpunit.xml", "173 pruebas pasando al 100% (816 aserciones)"]
        ],
        col_widths=[1.8, 2.7, 2.3]
    )

    out_dir = os.path.dirname(os.path.abspath(__file__))
    docx_path = os.path.join(out_dir, "Artfolio_Presentacion_Inversor_y_Defensa_Tecnica.docx")
    pdf_path = os.path.join(out_dir, "Artfolio_Presentacion_Inversor_y_Defensa_Tecnica.pdf")

    doc.save(docx_path)
    print(f"DOCX generado exitosamente en: {docx_path}")

    try:
        convert(docx_path, pdf_path)
        print(f"PDF generado exitosamente en: {pdf_path}")
    except Exception as e:
        print(f"Error al convertir a PDF con docx2pdf: {e}")
        sys.exit(1)


if __name__ == "__main__":
    build_document()
