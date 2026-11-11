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
    run.font.size = Pt(14.5)
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
        run_f = p_foot.add_run("Dossier Ejecutivo para Evaluación Cliente/Inversor y Defensa de Arquitectura — Proyecto Artfolio")
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
    r_sub = p_sub.add_run("Dossier de Inversión (< 10 Minutos) y Manual Técnico de Arquitectura y Ubicación de Archivos")
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
                "Presentar Artfolio paso a paso por categorías (Origen, Objetivo, Modelo Zero-Storage, Comparativa Competitiva y Demo Funcional).",
                "Exposición directa al Profesor en rol de Cliente / Inversor."
            ],
            [
                "PARTE II: Defensa de Proveedores y Whitelists",
                "Explicación profunda de Whitelists, YouTube, Pinterest, DeviantArt y Google Docs, detallando cómo extraen imágenes/embeds y sus archivos.",
                "Respaldo técnico ante preguntas sobre integraciones externas."
            ],
            [
                "PARTE III: Anexo Q&A de Arquitectura Interna",
                "Servicio de Mail/Confirmación y cómo acceder a él, Base de Datos, Acceso de Visitantes sin cuenta, API de Países y Seguridad.",
                "Sección independiente para el interrogatorio técnico del evaluador."
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
        "Guion estructurado en 5 categorías progresivas para presentar el valor comercial, financiero y funcional de Artfolio en menos de 10 minutos."
    )

    add_styled_table(
        doc,
        ["Categoría de la Presentación", "Tiempo Estimado", "Mensaje Central para el Inversor"],
        [
            ["Categoría 1: Origen, Problema y Objetivo", "Min 0:00 – 1:45 (1m 45s)", "Unificar el portafolio creativo multidisciplinario sin pagar costos millonarios de almacenamiento en la nube."],
            ["Categoría 2: Modelo 'Zero-Binary Storage'", "Min 1:45 – 3:15 (1m 30s)", "Márgenes operativos >95%: las CDNs gigantes pagan el ancho de banda; Artfolio aporta la curaduría e inteligencia."],
            ["Categoría 3: Comparativa Competitiva", "Min 3:15 – 5:00 (1m 45s)", "Superior a Linktree en inmersión visual y más versátil que ArtStation/Behance en soporte multi-formato y ética IA."],
            ["Categoría 4: Recorrido de Funciones Clave", "Min 5:00 – 7:45 (2m 45s)", "Visor 5-en-1, Filtro Anti-IA, Perfiles con Recorte de Avatar/Banner, Colecciones Privadas/No-Listadas y Dashboard KPI."],
            ["Categoría 5: Seguridad y Cierre de Inversión", "Min 7:45 – 9:00 (1m 15s)", "Plataforma autolimpiante (auditoría de enlaces, moderación, sesiones seguras) lista para escalar con riesgo mínimo."]
        ],
        col_widths=[2.2, 1.5, 3.1]
    )

    # --- CATEGORÍA 1 ---
    add_h1(doc, "Categoría 1: El Origen, el Problema de Mercado y el Objetivo (Min 0:00 – 1:45)")
    add_p(
        doc,
        "Hoy en día, el creador digital moderno es multidisciplinario: un mismo profesional publica ilustraciones en Pinterest o DeviantArt, sube sus reels de animación a YouTube, aloja modelos 3D interactivos en Sketchfab, compone bandas sonoras en Spotify y redacta guiones o casos de estudio en Google Docs.",
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
        bold_prefix="3. El Objetivo de Artfolio: "
    )

    add_callout(
        doc,
        "«Artfolio no compite contra YouTube, Pinterest o Sketchfab alojando archivos pesados; los convierte en nuestros proveedores gratuitos de infraestructura CDN mientras nosotros somos dueños de la identidad profesional, la curaduría y el descubrimiento del talento.»",
        title="FRASE DE APERTURA PARA EL CLIENTE / INVERSOR"
    )

    # --- CATEGORÍA 2 ---
    add_h1(doc, "Categoría 2: Innovación Financiera — Arquitectura 'Zero-Binary Storage' (Min 1:45 – 3:15)")
    add_bullet(
        doc,
        "En lugar de almacenar gigabytes de video o texturas en servidores propios, el motor de Artfolio recibe únicamente el enlace original de la obra, verifica su autenticidad y extrae sus metadatos oficiales mediante protocolos oEmbed y OpenGraph.",
        bold_prefix="Delegación Inteligente de Ancho de Banda: "
    )
    add_bullet(
        doc,
        "Almacenar 100.000 obras multimedia en Artfolio ocupa menos de 150 MB en nuestra base de datos relacional MySQL. Una plataforma tradicional requeriría varios Terabytes en S3 y miles de dólares mensuales en tráfico de red.",
        bold_prefix="Escalabilidad Financiera Inmediata: "
    )
    add_bullet(
        doc,
        "El artista no tiene que volver a renderizar, comprimir ni resubir sus obras; pega el enlace de su plataforma habitual y en menos de 1.5 segundos Artfolio genera su tarjeta visual e integra el reproductor nativo.",
        bold_prefix="Fricción Cero en Adopción de Usuarios: "
    )

    # --- CATEGORÍA 3 ---
    add_h1(doc, "Categoría 3: Comparativa con Plataformas Similares (Min 3:15 – 5:00)")
    add_p(
        doc,
        "Si el inversor pregunta por qué un artista elegiría Artfolio frente a alternativas existentes, esta matriz demuestra nuestro posicionamiento único en el mercado:"
    )

    add_styled_table(
        doc,
        ["Criterio / Capacidad", "ArtStation / Behance", "DeviantArt / Pinterest", "Linktree / Beacons", "ARTFOLIO (Nuestra Propuesta)"],
        [
            [
                "Soporte Nativo Multi-Formato (5 en 1)",
                "Limitado (Enfocado en imagen/video propio; débil en audio/docs).",
                "No (Enfocados casi exclusivamente en imagen 2D).",
                "No (Solo muestra botones de texto que sacan al usuario del sitio).",
                "SÍ: Imagen HD, Video, Audio (Spotify), 3D (Sketchfab) y Docs en un solo visor."
            ],
            [
                "Costo de Infraestructura por Usuario",
                "Muy Alto (Alojamiento binario pesado en nube propia).",
                "Muy Alto (Millones de imágenes alojadas en CDN propia).",
                "Bajo (Solo enlaces planos sin reproductor multimedia).",
                "Ultra Bajo: Experiencia rica tipo ArtStation con el costo ligero de Linktree."
            ],
            [
                "Transparencia y Filtro de IA Generativa",
                "Polémico / Configuración compleja y rechazada por artistas.",
                "Saturado de contenido IA sin filtrado claro para clientes.",
                "Inexistente (No clasifica obras creativas).",
                "Nativo desde publicación: Sello 'Con IA' y filtro en 1 clic ('Sin IA' / 'Con IA')."
            ],
            [
                "Colecciones con Privacidad de 3 Niveles",
                "Básico (Portafolio público o borradores privados).",
                "Tableros públicos o secretos simples.",
                "No aplica.",
                "Pública, No-Listada (enlace exclusivo para clientes con token) y Privada."
            ],
            [
                "Personalización Visual sin Subir Archivos",
                "Requiere subir avatares y banners pesados al servidor.",
                "Requiere subida de archivos locales.",
                "Subida local limitada o plantillas de pago.",
                "Editor interactivo de encuadre y zoom (X, Y, Zoom) para Avatar y Banner por URL."
            ]
        ],
        col_widths=[1.4, 1.35, 1.35, 1.25, 1.45]
    )

    # --- CATEGORÍA 4 ---
    add_h1(doc, "Categoría 4: Demostración de Funciones por Módulos (Min 5:00 – 7:45)")
    add_p(
        doc,
        "Durante este bloque de la presentación, se muestra al cliente cómo interactúan los usuarios dentro de la aplicación dividida en 6 pilares funcionales:"
    )
    add_bullet(
        doc,
        "Búsqueda en tiempo real con debounce de 400ms, filtrado por las 5 categorías (Imagen, Video, Audio, 3D, Docs), ordenamiento por popularidad (favoritos) o recencia, y selector de Transparencia de Inteligencia Artificial (Todas / Solo talento humano 'Sin IA' / 'Con IA').",
        bold_prefix="1. Explorador Público Inteligente y Filtro Ético de IA: "
    )
    add_bullet(
        doc,
        "Al hacer clic en cualquier obra, se abre un modal cinematográfico sin recargar la página que adapta su motor según el formato: renderiza imágenes en resolución original para Pinterest/DeviantArt, o despliega un reproductor interactivo 16:9 seguro para YouTube, Sketchfab, Spotify y Google Docs.",
        bold_prefix="2. Visor Multimedia Adaptativo 5-en-1: "
    )
    add_bullet(
        doc,
        "Cada tarjeta del explorador y el visor modal cuentan con un botón desplegable de Colecciones (para usuarios registrados). Si el usuario aún no tiene colecciones, el menú ofrece la acción instantánea 'Create collection' que crea la colección y guarda la obra en el mismo paso. Además, cada colección puede configurarse como Pública, No-Listada (accesible solo mediante enlace secreto para enviar un pitch privado a un cliente) o Privada.",
        bold_prefix="3. Curaduría en Colecciones (Públicas, No-Listadas y Privadas): "
    )
    add_bullet(
        doc,
        "Cuando un artista se registra, un asistente de 3 pasos guía su configuración inicial (identidad artística, ubicación con selector inteligente de países con banderas, habilidades y disponibilidad laboral). Posteriormente, en su Centro de Configuración dispone de un editor interactivo de arrastrar y hacer zoom tanto para su Avatar circular como para su Banner rectangular panorámico, guardando coordenadas matemáticas (X, Y, Zoom) que se reflejan en la cabecera superior de su Public Profile.",
        bold_prefix="4. Onboarding Guiado y Perfil Público con Encuadre de Avatar y Banner: "
    )
    add_bullet(
        doc,
        "Al publicar una obra, el artista cuenta con un interruptor dedicado para declarar si la obra incluye trabajo asistido por Inteligencia Artificial ('Obra con IA'). Esto protege la confianza de los reclutadores que buscan habilidades manuales específicas.",
        bold_prefix="5. Publicación con Declaración de IA y Etiquetado: "
    )
    add_bullet(
        doc,
        "Panel ejecutivo de 3 tarjetas con indicadores clave (KPIs): Total de Obras Activas (con desglose por categoría), Impacto de Favoritos recibidos (con la obra estrella más destacada) y Estado de Visibilidad Profesional ('Disponible para contratos' vs 'Portafolio en exhibición') junto al ranking del Top 5 de obras más exitosas.",
        bold_prefix="6. Dashboard de Rendimiento para el Artista: "
    )

    # --- CATEGORÍA 5 ---
    add_h1(doc, "Categoría 5: Gobernanza, Moderación y Cierre de Inversión (Min 7:45 – 9:00)")
    add_bullet(
        doc,
        "Un riesgo clásico de los portafolios basados en enlaces es que el enlace externo muera. Artfolio incluye un comando automatizable ('artworks:check-links') que verifica periódicamente el estado HTTP de las obras, oculta automáticamente del explorador los enlaces caídos (404/410) y envía un correo de alerta al artista para que lo actualice.",
        bold_prefix="Salud Automática del Catálogo (Self-Healing Catalog): "
    )
    add_bullet(
        doc,
        "Sistema de denuncias con validación anti-duplicados, bloqueo bidireccional entre usuarios (invisibiliza obras mutuas en el explorador), papelera de reciclaje con ventana de recuperación de 30 días y suspensión administrativa inmediata para cuentas infractoras.",
        bold_prefix="Moderación y Convivencia Comunitaria: "
    )

    doc.add_page_break()

    # =========================================================================
    # PARTE II: DEFENSA TÉCNICA — WHITELISTS Y PROVEEDORES MULTIMEDIA
    # =========================================================================
    add_part_banner(
        doc,
        "PARTE II — DEFENSA TÉCNICA DE PROVEEDORES MULTIMEDIA Y WHITELISTS",
        "Funcionamiento Interno y Ubicación Exacta de Archivos (Whitelists, YouTube, Pinterest, DeviantArt y Google Docs)",
        "Guía detallada para responder con precisión quirúrgica cuando el profesor pregunte cómo funcionan las integraciones externas y en qué archivos están programadas."
    )

    # --- 2.1 WHITELISTS ---
    add_h1(doc, "1. Sistema de Whitelists (Listas Blancas) y Protección Anti-SSRF")
    add_p(
        doc,
        "Como el servidor backend de Artfolio realiza peticiones HTTP salientes para extraer miniaturas y metadatos de los enlaces que ingresan los usuarios, aceptar cualquier URL arbitraria expondría al servidor a ataques de SSRF (Server-Side Request Forgery), donde un atacante podría intentar escanear la red interna (ej. http://127.0.0.1 o puertos locales) o inyectar sitios maliciosos.",
        bold_prefix="¿Por qué existe y qué problema resuelve?: "
    )
    add_p(
        doc,
        "La validación actúa en tres pasos dentro del método processUrl(string $url) de EmbedService.php:",
        bold_prefix="¿Cómo funciona paso a paso?: "
    )
    add_bullet(doc, "Paso 1 (Validación de Formato y Bloqueo de Red Local): Se analiza la URL con filter_var($url, FILTER_VALIDATE_URL) y parse_url(). Luego se bloquea explícitamente cualquier dirección IP cruda (filter_var($host, FILTER_VALIDATE_IP)) o el dominio 'localhost' (líneas 68–70), neutralizando ataques SSRF.", bold_prefix="Filtro Anti-SSRF: ")
    add_bullet(doc, "Paso 2 (Normalización del Host): Se convierte el host a minúsculas y se limpia el prefijo 'www.' mediante preg_replace('/^www\\./', '', $host).", bold_prefix="Limpieza de Dominio: ")
    add_bullet(doc, "Paso 3 (Cotejo Estricto contra $allowedDomains): Se recorre el diccionario de proveedores autorizados (pinterest, deviantart, spotify, youtube, sketchfab, vimeo, soundcloud, googledocs, imgur). Solo se aprueba si el host coincide exactamente con uno de los dominios de la lista blanca o es un subdominio legítimo terminado en '.dominio' (str_ends_with($cleanHost, '.' . $domain)). Si no coincide con ninguno, lanza una excepción controlada y rechaza la publicación.", bold_prefix="Verificación de Lista Blanca: ")

    add_code_box(
        doc,
        "1) Archivo Principal (Motor de Whitelist de Obras):\n"
        "   -> app/Services/EmbedService.php\n"
        "      - Propiedad protegida $allowedDomains (Líneas 12 a 32): Define los dominios permitidos por proveedor.\n"
        "      - Método processUrl(string $url) (Líneas 41 a 90): Ejecuta el bloqueo anti-IP/localhost y valida la Whitelist.\n\n"
        "2) Validación de Entrada en el Formulario de Creación/Edición de Obra:\n"
        "   -> app/Http/Requests/ArtworkRequest.php (Líneas 22 a 33)\n"
        "      - Valida 'source_url' => ['required', 'url', 'max:500'] antes de llegar al controlador.\n\n"
        "3) Controlador que Invoca la Whitelist y Captura Errores:\n"
        "   -> app/Http/Controllers/ArtworkController.php\n"
        "      - Método previewUrl() (Líneas 27 a 53): Endpoint AJAX (/artworks/preview) que valida la URL en vivo.\n"
        "      - Método store() (Líneas 58 a 95): Revalida la URL en backend antes de guardar en la base de datos.\n\n"
        "4) Whitelist Adicional para Imágenes de Avatar y Banner de Perfil:\n"
        "   -> app/Http/Requests/ProfileUpdateRequest.php (Líneas 32 a 85)\n"
        "      - Valida que 'avatar_url' y 'banner_url' provengan de HTTPS y pasen por una lista blanca de CDNs de imagen seguras.",
        title="UBICACIÓN EXACTA DE ARCHIVOS — WHITELISTS"
    )

    # --- 2.2 YOUTUBE ---
    add_h1(doc, "2. Implementación de Enlaces de Videos de YouTube")
    add_p(
        doc,
        "YouTube permite múltiples formatos de enlace (enlaces largos de navegador watch?v=..., enlaces cortos móviles youtu.be/..., enlaces de YouTube Shorts shorts/... y enlaces /embed/...). Artfolio los unifica todos en un reproductor limpio y extrae la mejor miniatura disponible.",
        bold_prefix="¿Cómo funciona la integración de YouTube?: "
    )
    add_bullet(
        doc,
        "En el método fetchYouTubeData(string $url, array &$result), se aplica la expresión regular: #(?:youtube\\.com/(?:watch\\?.*v=|shorts/|embed/)|youtu\\.be/)([a-zA-Z0-9_-]{11})#i para capturar el identificador único de 11 caracteres ($videoId).",
        bold_prefix="1. Extracción del ID de 11 caracteres: "
    )
    add_bullet(
        doc,
        "Se realiza una petición GET con timeout de 5 segundos a https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$videoId}&format=json para obtener el título oficial del video.",
        bold_prefix="2. Consulta al API Oficial oEmbed: "
    )
    add_bullet(
        doc,
        "No todos los videos de YouTube tienen miniatura HD (1280x720). Por eso, Artfolio hace una petición rápida Http::timeout(3)->head() a https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg. Si responde HTTP 200 OK, usa maxresdefault.jpg; si da 404, degrada automáticamente a hqdefault.jpg (que siempre existe).",
        bold_prefix="3. Verificación Inteligente de Miniatura HD (maxresdefault vs hqdefault): "
    )
    add_bullet(
        doc,
        "Construye la URL embebible https://www.youtube.com/embed/{$videoId}?rel=0 y genera un <iframe> con atributos de seguridad sandbox=\"allow-scripts allow-same-origin allow-presentation\", asignando render_type = 'iframe' y category = 'video'.",
        bold_prefix="4. Generación del Iframe Seguro (Sandbox): "
    )
    add_bullet(
        doc,
        "En el modelo Artwork.php (método getPreviewUrlAttribute), cuando la obra se muestra en las tarjetas pequeñas del explorador, reemplaza dinámicamente maxresdefault.jpg por hqdefault.jpg para ahorrar ancho de banda y acelerar la carga de la grilla.",
        bold_prefix="5. Optimización de Rendimiento en Grilla: "
    )

    add_code_box(
        doc,
        "1) Extracción de ID, oEmbed, Miniatura y Construcción del Iframe:\n"
        "   -> app/Services/EmbedService.php\n"
        "      - Método fetchYouTubeData(string $url, array &$result) (Líneas 385 a 444).\n\n"
        "2) Optimización Dinámica de Miniatura para el Explorador:\n"
        "   -> app/Models/Artwork.php\n"
        "      - Accessor getPreviewUrlAttribute() (Líneas 44 a 57): Cambia maxresdefault por hqdefault en tarjetas.\n\n"
        "3) Renderizado Visual en la Interfaz (Frontend Blade):\n"
        "   -> resources/views/components/artwork-card.blade.php (Líneas 36 a 62): Muestra la miniatura y el badge 'VIDEO'.\n"
        "   -> resources/views/components/viewer-modal.blade.php (Líneas 120 a 136): Inyecta el <iframe> 16:9 interactivo.",
        title="UBICACIÓN EXACTA DE ARCHIVOS — YOUTUBE"
    )

    # --- 2.3 PINTEREST ---
    add_h1(doc, "3. Implementación de Enlaces de Pinterest y Extracción de Imagen HD")
    add_p(
        doc,
        "Pinterest es uno de los proveedores técnicamente más desafiantes porque: (a) los usuarios suelen compartir enlaces acortados de 'pin.it', (b) su API oEmbed entrega por defecto miniaturas pequeñas de 236 píxeles (/236x/), y (c) su servidor de imágenes (i.pinimg.com) bloquea la carga si detecta que otra web intenta incrustar la imagen (Hotlink Protection). Artfolio resuelve los tres obstáculos:",
        bold_prefix="¿Por qué Pinterest requiere una ingeniería especial y cómo toma la imagen?: "
    )
    add_bullet(
        doc,
        "Si el usuario pega un enlace corto (ej. https://pin.it/...), fetchPinterestData() realiza primero una petición HTTP siguiendo redirecciones y extrae $res->effectiveUri() para obtener la URL canónica real del Pin (líneas 153–163 de EmbedService.php).",
        bold_prefix="Paso 1 — Resolución de Redirecciones Acortadas (pin.it): "
    )
    add_bullet(
        doc,
        "Consulta https://www.pinterest.com/oembed.json?url=... que devuelve un JSON con 'thumbnail_url' (normalmente en baja resolución, ej. https://i.pinimg.com/236x/...). Nuestro algoritmo intercepta esa ruta y mediante preg_replace('#/(236x|474x|564x|736x)/#', '/originals/', $thumb) la transforma a la versión original sin compresión (/originals/). A continuación, dispara una petición HEAD de 2 segundos para verificar que el archivo original exista en el CDN de Pinterest; si no existe, hace fallback automático a alta definición (/736x/).",
        bold_prefix="Paso 2 — Escalado Automático de Baja Resolución (/236x/) a Original (/originals/): "
    )
    add_bullet(
        doc,
        "Si un Pin específico no responde en el endpoint oEmbed, el método descarga el HTML del Pin y busca primero bloques estructurados <script type=\"application/ld+json\"> y, en última instancia, la etiqueta <meta property=\"og:image\">, aplicando igualmente la promoción a /originals/ o /736x/.",
        bold_prefix="Paso 3 — Doble Motor de Respaldo (JSON-LD y OpenGraph): "
    )
    add_bullet(
        doc,
        "En Artwork.php, el accessor preview_url transforma dinámicamente la URL a /474x/ para que el Explorador cargue rápido, mientras que high_res_url fuerza /originals/ cuando el usuario abre la obra en pantalla completa en el Visor Modal.",
        bold_prefix="Paso 4 — Doble Resolución Dinámica (Grilla vs Modal): "
    )
    add_bullet(
        doc,
        "Para evitar que el CDN i.pinimg.com bloquee la imagen por venir desde nuestro dominio local, las etiquetas <img> en Blade incluyen el atributo referrerpolicy=\"no-referrer\", logrando que la imagen cargue limpia como un elemento nativo (render_type = 'image').",
        bold_prefix="Paso 5 — Bypass Legítimo de Bloqueo de Hotlinking: "
    )

    add_code_box(
        doc,
        "1) Motor de Extracción de Imagen de Pinterest (Redirección pin.it + oEmbed + Escalado /originals/ + Fallback OG):\n"
        "   -> app/Services/EmbedService.php\n"
        "      - Método fetchPinterestData(string $url, array &$result) (Líneas 147 a 250).\n\n"
        "2) Conversión Dinámica de Resolución (/474x/ en Tarjetas vs /originals/ en Visor Modal):\n"
        "   -> app/Models/Artwork.php\n"
        "      - Accessor getPreviewUrlAttribute() (Líneas 44 a 49): Convierte a /474x/ en la grilla.\n"
        "      - Accessor getHighResUrlAttribute() (Líneas 62 a 68): Convierte a /originals/ en el modal.\n\n"
        "3) Renderizado con Protección Anti-Hotlink (referrerpolicy=\"no-referrer\"):\n"
        "   -> resources/views/components/artwork-card.blade.php (Línea 40).\n"
        "   -> resources/views/components/viewer-modal.blade.php (Líneas 139 y 146).",
        title="UBICACIÓN EXACTA DE ARCHIVOS — PINTEREST"
    )

    # --- 2.4 DEVIANTART ---
    add_h1(doc, "4. Implementación de Enlaces de DeviantArt")
    add_p(
        doc,
        "DeviantArt aloja ilustraciones digitales de alta resolución en dominios como deviantart.com y enlaces cortos sta.sh. Su integración se resuelve mediante su backend oficial de oEmbed con respaldo OpenGraph:",
        bold_prefix="¿Cómo toma la imagen y los datos de DeviantArt?: "
    )
    add_bullet(
        doc,
        "El método fetchDeviantArtData() realiza una petición GET a https://backend.deviantart.com/oembed?url={urlencode($url)} enviando un cabecera User-Agent estándar.",
        bold_prefix="1. Consulta al Backend Oficial oEmbed de DeviantArt: "
    )
    add_bullet(
        doc,
        "A diferencia de otros servicios que solo entregan una miniatura pequeña, el JSON de DeviantArt devuelve en el campo $data['url'] la ruta directa al archivo gráfico completo en sus servidores (y $data['thumbnail_url'] como respaldo). Artfolio prioriza $data['url'] para garantizar nitidez total y establece render_type = 'image'.",
        bold_prefix="2. Captura de Imagen en Resolución Completa: "
    )
    add_bullet(
        doc,
        "Extrae $data['title'] y, si está presente $data['author_name'], formatea automáticamente el título sugerido como «Título de la Obra - por Autor».",
        bold_prefix="3. Atribución Automática de Autoría: "
    )
    add_bullet(
        doc,
        "Si por políticas de privacidad de una desviación el endpoint oEmbed no devuelve la imagen, el código ejecuta un segundo intento leyendo directamente el HTML de la página y extrayendo mediante expresiones regulares <meta property=\"og:image\"> y <meta property=\"og:title\">.",
        bold_prefix="4. Respaldo por Scraping de Metaetiquetas OpenGraph: "
    )

    add_code_box(
        doc,
        "1) Extracción oEmbed y Fallback OpenGraph de DeviantArt:\n"
        "   -> app/Services/EmbedService.php\n"
        "      -Lista blanca: 'deviantart' => ['deviantart.com', 'sta.sh'] (Línea 14).\n"
        "      - Método fetchDeviantArtData(string $url, array &$result) (Líneas 256 a 315).\n\n"
        "2) Visualización como Imagen Nativa de Alta Resolución:\n"
        "   -> resources/views/components/artwork-card.blade.php (Líneas 38 a 45).\n"
        "   -> resources/views/components/viewer-modal.blade.php (Líneas 137 a 148).",
        title="UBICACIÓN EXACTA DE ARCHIVOS — DEVIANTART"
    )

    # --- 2.5 GOOGLE DOCS / GOOGLE DRIVE ---
    add_h1(doc, "5. Implementación de Enlaces de Google Docs y Google Drive")
    add_p(
        doc,
        "Para que escritores, guionistas, diseñadores de narrativa (Game Designers) y directores de arte puedan exhibir guiones, presentaciones o hojas de cálculo sin obligar al visitante a descargar archivos PDF/Word, Artfolio integra Google Docs y Google Drive en vivo:",
        bold_prefix="¿Cómo funciona la transformación de enlaces de Google Docs?: "
    )
    add_bullet(
        doc,
        "Los usuarios suelen copiar enlaces terminados en /edit, /view o ?usp=sharing (que no pueden incrustarse directamente en un iframe o muestran barras de edición pesadas). El método fetchGoogleDocsData() aplica la expresión regular #(?:docs|drive)\\.google\\.com/(document|presentation|spreadsheets|file)/d/([a-zA-Z0-9_-]+)#i para extraer dos datos clave: el tipo de documento ($docType) y el ID alfanumérico del archivo ($docId).",
        bold_prefix="1. Detección de Tipo de Documento y Extracción de ID: "
    )
    add_bullet(
        doc,
        "Si es un archivo general de Drive ($docType === 'file'), construye https://drive.google.com/file/d/{$docId}/preview. Si es un documento, presentación u hoja de cálculo, construye https://docs.google.com/{$docType}/d/{$docId}/preview. El endpoint /preview de Google genera un lector limpio, rápido y de solo lectura ideal para portafolios.",
        bold_prefix="2. Conversión Automática al Visor Limpio (/preview): "
    )
    add_bullet(
        doc,
        "Google Docs no posee un endpoint oEmbed público. Para que la tarjeta en el explorador tenga una vista previa real de la primera página del documento sin requerir una API Key privada, Artfolio utiliza el generador oficial de miniaturas de Drive: https://drive.google.com/thumbnail?id={$docId}&sz=w800.",
        bold_prefix="3. Generación de Miniatura Visual sin API Key: "
    )
    add_bullet(
        doc,
        "Realiza una petición GET a la URL para leer la etiqueta <meta property=\"og:title\"> o <title>, limpia los sufijos '- Google Docs' / '- Google Drive', asigna category = 'docs' y render_type = 'iframe', e inyecta un <iframe> con sandbox=\"allow-scripts allow-same-origin allow-popups\".",
        bold_prefix="4. Extracción del Título Real y Renderizado Seguro: "
    )

    add_code_box(
        doc,
        "1) Transformación a /preview, Miniatura de Drive y Scraping de Título:\n"
        "   -> app/Services/EmbedService.php\n"
        "      - Lista blanca: 'googledocs' => ['docs.google.com', 'drive.google.com'] (Línea 29).\n"
        "      - Método fetchGoogleDocsData(string $url, array &$result) (Líneas 490 a 560).\n\n"
        "2) Renderizado en Tarjeta (con fallback visual de icono de documento si el archivo restringe miniatura) y Visor:\n"
        "   -> resources/views/components/artwork-card.blade.php (Líneas 38 a 62).\n"
        "   -> resources/views/components/viewer-modal.blade.php (Líneas 120 a 136).",
        title="UBICACIÓN EXACTA DE ARCHIVOS — GOOGLE DOCS Y DRIVE"
    )

    # --- 2.6 OTROS PROVEEDORES (SKETCHFAB Y SPOTIFY) ---
    add_h2(doc, "Nota Complementaria: Sketchfab (3D) y Spotify (Audio)")
    add_p(
        doc,
        "En el mismo archivo app/Services/EmbedService.php también se encuentran implementados los motores para las dos categorías restantes: fetchSketchfabData() (líneas 321–379, que consulta https://sketchfab.com/oembed para modelos 3D interactivos con WebGL) y fetchSpotifyData() (líneas 450–485, que consulta https://open.spotify.com/oembed para pistas, álbumes y listas de reproducción de audio).",
        bold_prefix="Cobertura Completa de las 5 Categorías: "
    )

    doc.add_page_break()

    # =========================================================================
    # PARTE III: ANEXO INDEPENDIENTE DE PREGUNTAS DEL PROFESOR (ARQUITECTURA)
    # =========================================================================
    add_part_banner(
        doc,
        "PARTE III — ANEXO INDEPENDIENTE DE INTERROGATORIO TÉCNICO (Q&A DEL PROFESOR)",
        "Servicio de Mail, Base de Datos, Acceso de Visitantes, API de Países y Seguridad",
        "Respuestas estructuradas (aparte de la presentación al inversor) con explicaciones de flujo y rutas exactas de archivos para responder al profesor."
    )

    # --- PREGUNTA 1: MAIL ---
    add_h1(doc, "Pregunta 1: ¿Cómo funciona el Servicio de Mail y la Confirmación de Cuenta, cómo se accede a él y qué archivos lo manejan?")
    add_p(
        doc,
        "Artfolio implementa un sistema completo de correo transaccional sobre Laravel 12 y Laravel Breeze, cubriendo 4 flujos críticos: (1) Confirmación/Verificación de correo al registrarse, (2) Recuperación de contraseña olvidada, (3) Formulario de contacto directo Cliente -> Artista en el perfil público, y (4) Alertas automáticas al artista cuando uno de sus enlaces externos se cae.",
        bold_prefix="A. ¿Qué funciones cumple el servicio de correo?: "
    )
    add_p(
        doc,
        "El modelo User implementa la interfaz MustVerifyEmail (en app/Models/User.php, línea 16). Cuando un usuario completa el formulario de registro en RegisteredUserController@store, Laravel dispara el evento Registered($user), que genera automáticamente un enlace firmado criptográficamente con hash SHA-1 y fecha de expiración (/verify-email/{id}/{hash}). Hasta que el usuario hace clic en ese enlace (procesado por VerifyEmailController), cualquier intento de entrar a rutas protegidas con el middleware 'verified' (como el Dashboard, Onboarding o Publicación de Obras) lo redirige a la pantalla de aviso 'Verifica tu dirección de correo electrónico' (verify-email.blade.php), desde donde puede reenviar el correo con límite anti-spam (throttle:6,1 = máximo 6 intentos por minuto).",
        bold_prefix="B. ¿Cómo funciona exactamente la Confirmación de Cuenta?: "
    )
    add_p(
        doc,
        "En entorno de desarrollo y evaluación local (Laragon), el sistema está configurado en el archivo .env mediante el protocolo SMTP apuntando al servidor local de captura de correos Mailpit (o alternativamente al driver 'log'):",
        bold_prefix="C. ¿Cómo se configura y cómo se accede a la bandeja de correos durante la demostración?: "
    )
    add_bullet(
        doc,
        "En el archivo .env se define MAIL_MAILER=smtp, MAIL_HOST=127.0.0.1 y MAIL_PORT=1025. Laragon incluye Mailpit escuchando en el puerto 1025 (SMTP) y ofrece una interfaz web completa en http://127.0.0.1:8025 (o http://localhost:8025). Al abrir http://127.0.0.1:8025 en el navegador durante la defensa, el profesor verá llegar en tiempo real el correo de confirmación, podrá inspeccionar su diseño HTML oscuro y hacer clic en el botón 'Confirmar Cuenta' para activar el usuario al instante.",
        bold_prefix="1. Acceso Visual en Vivo vía Mailpit Web UI (Recomendado para la Defensa): "
    )
    add_bullet(
        doc,
        "Creamos un comando artesanal propio en routes/console.php (líneas 29–46). Ejecutando en la terminal: php artisan mail:test profesor@ejemplo.com, el sistema envía un correo de prueba inmediato y confirma por consola si la conexión con el servidor de correo está activa.",
        bold_prefix="2. Comando de Diagnóstico Rápido por Consola: "
    )
    add_bullet(
        doc,
        "Si en .env se cambia MAIL_MAILER=log, todos los correos enviados (incluyendo el enlace firmado de confirmación) quedan impresos íntegramente en el archivo storage/logs/laravel.log.",
        bold_prefix="3. Modo Respaldo sin Servidor SMTP (Driver Log): "
    )

    add_code_box(
        doc,
        "1) Activación del Contrato de Verificación Obligatoria:\n"
        "   -> app/Models/User.php (Línea 16): class User extends Authenticatable implements MustVerifyEmail\n\n"
        "2) Controladores de Verificación y Registro:\n"
        "   -> app/Http/Controllers/Auth/RegisteredUserController.php: Registra al usuario y dispara el correo.\n"
        "   -> app/Http/Controllers/Auth/EmailVerificationPromptController.php: Muestra la vista de espera de confirmación.\n"
        "   -> app/Http/Controllers/Auth/VerifyEmailController.php: Valida el hash firmado y marca email_verified_at.\n"
        "   -> app/Http/Controllers/Auth/EmailVerificationNotificationController.php: Reenvía el enlace de confirmación.\n\n"
        "3) Rutas de Autenticación y Confirmación:\n"
        "   -> routes/auth.php (Líneas 40 a 55): Define verify-email, verify-email/{id}/{hash} (middleware 'signed') y verification.send.\n\n"
        "4) Clases Mailable Personalizadas del Proyecto y sus Plantillas HTML:\n"
        "   -> app/Mail/ContactArtist.php  --> Vista: resources/views/emails/contact-artist.blade.php\n"
        "   -> app/Mail/LinkDownAlert.php  --> Vista: resources/views/emails/link-down-alert.blade.php\n"
        "   -> Vista de bloqueo hasta confirmar: resources/views/auth/verify-email.blade.php\n\n"
        "5) Configuración y Comando de Prueba:\n"
        "   -> .env y config/mail.php (Configuración de MAIL_MAILER, MAIL_HOST=127.0.0.1, MAIL_PORT=1025).\n"
        "   -> routes/console.php (Líneas 29 a 46): Comando 'php artisan mail:test {email?}'.",
        title="UBICACIÓN EXACTA DE ARCHIVOS — SERVICIO DE MAIL Y CONFIRMACIÓN"
    )

    # --- PREGUNTA 2: ALMACENAMIENTO DE DATOS ---
    add_h1(doc, "Pregunta 2: ¿Dónde y cómo se almacenan los datos de los usuarios y sus obras?")
    add_p(
        doc,
        "Todos los datos se almacenan en una base de datos relacional MySQL llamada 'artfolio' (gestionada a través del ORM Eloquent de Laravel 12). Siguiendo nuestra arquitectura Zero-Binary Storage, la base de datos no guarda archivos binarios pesados (ni blobs de video ni imágenes subidas al disco local), sino referencias normalizadas, metadatos verificados y vectores matemáticos de posicionamiento.",
        bold_prefix="A. Motor de Base de Datos y Filosofía de Almacenamiento: "
    )
    add_bullet(
        doc,
        "Almacena credenciales seguras (password hasheado con Bcrypt), identidad pública (name, artistic_name, slug único para la URL /artista/{slug}, email, email_verified_at, role ['artist'|'admin'], status ['active'|'suspended']), perfil profesional (bio, location, work_status, y 3 columnas tipo JSON llamadas skills, software y hiring_types que Eloquent convierte automáticamente en arrays de PHP), e información visual no destructiva: avatar_url junto a sus coordenadas de recorte circular (crop_x, crop_y, crop_zoom) y banner_url junto a sus coordenadas de encuadre rectangular panorámico (banner_crop_x, banner_crop_y, banner_crop_zoom).",
        bold_prefix="B. Tabla 'users' (Datos de Usuarios y Artistas): "
    )
    add_bullet(
        doc,
        "Relacionada con 'users' mediante clave foránea user_id (con borrado en cascada onDelete('cascade')). Guarda: title, description, category ('imagen', 'video', 'audio', '3d', 'docs'), has_ai (booleano que indica si tiene trabajo de IA), provider (ej. 'youtube', 'pinterest', 'deviantart', 'googledocs'), render_type ('image' o 'iframe'), source_url (enlace original), image_url (miniatura extraída), embed_url (URL para el iframe), tags (texto separado por comas), status ('published', 'draft', 'archived'), link_status ('active', 'down') y deleted_at (para la papelera SoftDeletes de 30 días).",
        bold_prefix="C. Tabla 'artworks' (Datos de las Obras): "
    )
    add_bullet(
        doc,
        "1) Tabla 'bookmarks': Relación muchos-a-muchos de Favoritos (user_id + artwork_id con índice único compuesto para impedir dar like dos veces).\n"
        "2) Tabla 'collections' y tabla pivote 'artwork_collection': Almacena las colecciones creadas por usuarios, incluyendo su nivel de privacidad visibility ('public', 'unlisted', 'private') y su token criptográfico share_token para enlaces no-listados.\n"
        "3) Tabla 'reports': Guarda los reportes de moderación (user_id, artwork_id, reason, details, status).\n"
        "4) Tabla 'user_blocks': Almacena bloqueos entre usuarios (blocker_id, blocked_id).",
        bold_prefix="D. Tablas Relacionales Complementarias: "
    )

    add_code_box(
        doc,
        "1) Modelos Eloquent (Representación en Código de las Tablas y sus Relaciones):\n"
        "   -> app/Models/User.php: Define $fillable, $casts (skills/software/hiring_types como 'array') y relaciones.\n"
        "   -> app/Models/Artwork.php: Usa el trait SoftDeletes, define $appends = ['preview_url', 'high_res_url'] y relaciones.\n"
        "   -> app/Models/Collection.php: Gestiona visibilidad ('public', 'unlisted', 'private') y generación de share_token.\n"
        "   -> app/Models/Bookmark.php y app/Models/Report.php.\n\n"
        "2) Migraciones SQL (Estructura Exacta de las Tablas):\n"
        "   -> database/migrations/0001_01_01_000000_create_users_table.php\n"
        "   -> database/migrations/2026_02_25_001053_add_artfolio_fields_to_users_table.php\n"
        "   -> database/migrations/2026_02_25_001054_create_artworks_table.php\n"
        "   -> database/migrations/2026_02_25_001055_create_bookmarks_table.php\n"
        "   -> database/migrations/2026_02_25_001056_create_collections_table.php\n"
        "   -> database/migrations/2026_02_25_001057_create_reports_table.php\n"
        "   -> database/migrations/2026_11_11_180000_add_p1_fields_to_users_and_artworks.php (Añade has_ai y user_blocks).\n"
        "   -> database/migrations/2026_11_11_185000_add_visibility_to_collections_table.php (Añade visibility y share_token).\n"
        "   -> database/migrations/2026_11_11_190500_add_banner_crop_fields_to_users_table.php (Añade banner_crop_x/y/zoom).",
        title="UBICACIÓN EXACTA DE ARCHIVOS — BASE DE DATOS Y MODELOS"
    )

    # --- PREGUNTA 3: USUARIO VISITANTE (GUEST) ---
    add_h1(doc, "Pregunta 3: ¿Cómo accede un usuario visitante sin tener cuenta y qué archivos manejan eso?")
    add_p(
        doc,
        "Para maximizar la retención y permitir que clientes o reclutadores evalúen portafolios sin obligarlos a registrarse de entrada, Artfolio implementa una arquitectura de 'Acceso Público Abierto con Intercepción Progresiva de Acciones':",
        bold_prefix="A. Filosofía de Navegación para Visitantes (Guests): "
    )
    add_bullet(
        doc,
        "En routes/web.php, la ruta raíz GET / (Explorador), la ruta GET /artista/{slug} (Perfil Público del Artista), la ruta GET /collections/{collection} (Colecciones públicas o con enlace no-listado) y el formulario POST /artista/{slug}/contact (Contacto directo al artista) se encuentran FUERA del grupo de middleware 'auth'. Cualquier visitante anónimo puede buscar obras, filtrar por categorías o IA, abrir el visor modal interactivo, reproducir videos/modelos 3D y ver el perfil y banner del artista.",
        bold_prefix="1. Qué puede hacer el visitante libremente (Rutas Públicas): "
    )
    add_bullet(
        doc,
        "En los controladores ExplorerController y PublicProfileController se consulta Auth::check() y Auth::id(). Si el usuario es un visitante anónimo, las consultas omiten el cruce de bloqueos personales o colecciones privadas sin lanzar ningún error de sesión nula.",
        bold_prefix="2. Cómo los Controladores distinguen entre Visitante y Usuario Registrado: "
    )
    add_bullet(
        doc,
        "En las vistas Blade (artwork-card.blade.php y viewer-modal.blade.php), los botones de interacción social están envueltos en directivas condicionales @auth ... @else ... @endauth:\n"
        "   • El selector desplegable de añadir a Colección (<x-collection-picker />) está dentro de @auth, por lo que solo se renderiza para usuarios con cuenta activa.\n"
        "   • Cuando un visitante sin cuenta hace clic en 'Favorito' o 'Reportar', en lugar de enviar una petición fallida al servidor, el botón ejecuta en el navegador el evento Alpine.js: $dispatch('open-guest-modal', { context: 'favorito' }). Esto despliega al instante un modal persuasivo (guest-modal.blade.php) que le explica la función y le ofrece los botones 'Crear cuenta gratis' o 'Ya tengo una cuenta'.",
        bold_prefix="3. Cómo se interceptan las acciones exclusivas de usuarios registrados: "
    )

    add_code_box(
        doc,
        "1) Definición de Rutas Públicas (Sin Middleware 'auth'):\n"
        "   -> routes/web.php\n"
        "      - Línea 60: Route::get('/', [ExplorerController::class, 'index'])->name('home');\n"
        "      - Línea 61: Route::get('/artista/{slug}', [PublicProfileController::class, 'show'])->name('artist.portfolio');\n"
        "      - Línea 62: Route::post('/artista/{slug}/contact', [PublicProfileController::class, 'contact']);\n"
        "      - Línea 115: Route::get('/collections/{collection}', [CollectionController::class, 'show']);\n\n"
        "2) Controladores que Soportan Navegación Anónima:\n"
        "   -> app/Http/Controllers/ExplorerController.php (Líneas 13 a 89).\n"
        "   -> app/Http/Controllers/PublicProfileController.php (Líneas 16 a 63).\n\n"
        "3) Vistas y Componente Interceptor de Visitantes (Guest Modal):\n"
        "   -> resources/views/components/guest-modal.blade.php (Modal reactivo que escucha '@open-guest-modal.window').\n"
        "   -> resources/views/components/artwork-card.blade.php (Líneas 75 a 112: directivas @auth / @else).\n"
        "   -> resources/views/components/viewer-modal.blade.php (Líneas 36 a 108: directivas @auth / @else).\n"
        "   -> resources/views/layouts/navigation.blade.php (Muestra 'Iniciar Sesión / Registrarse' si es visitante).",
        title="UBICACIÓN EXACTA DE ARCHIVOS — ACCESO DE VISITANTES SIN CUENTA"
    )

    # --- PREGUNTA 4: PAÍSES ---
    add_h1(doc, "Pregunta 4: ¿De dónde se sacan los países disponibles en el registro/onboarding y perfil, y qué archivo los maneja?")
    add_p(
        doc,
        "Para que el selector de país con banderas funcione siempre de forma instantánea y nunca falle (incluso si durante la evaluación en clase no hubiera conexión a Internet o fallara una API externa), diseñamos un micro-servicio interno en Laravel con Caché de 30 días y Triple Fallback (3 niveles de respaldo):",
        bold_prefix="A. Arquitectura de Triple Respaldo para los Países: "
    )
    add_bullet(
        doc,
        "En routes/web.php (líneas 15 a 57) está declarada la ruta GET /api/countries (nombre: api.countries). Utiliza Cache::remember('global_countries_list_v1', 86400 * 30, ...) para guardar el listado completo de países en caché durante 30 días, de modo que las consultas subsiguientes tardan menos de 2 milisegundos.",
        bold_prefix="1. Endpoint Interno con Caché de 30 Días (/api/countries): "
    )
    add_bullet(
        doc,
        "La primera vez que se solicita (si la caché está vacía), el backend hace una petición HTTP con timeout de 4 segundos al repositorio oficial de países en GitHub: https://raw.githubusercontent.com/mledoze/countries/master/countries.json, extrayendo los nombres comunes ($c['name']['common']) de más de 240 países y territorios.",
        bold_prefix="2. Fuente Primaria (Dataset Oficial GitHub mledoze/countries): "
    )
    add_bullet(
        doc,
        "Si GitHub no respondiera, el bloque catch consulta automáticamente la API pública https://restcountries.com/v3.1/all?fields=name.",
        bold_prefix="3. Fuente Secundaria (API REST Countries v3.1): "
    )
    add_bullet(
        doc,
        "Si el servidor estuviera completamente desconectado de Internet, el endpoint devuelve un arreglo estático local de más de 100 países codificado directamente en las líneas 41 a 54 de routes/web.php, garantizando disponibilidad del 100%.",
        bold_prefix="4. Fuente Terciaria Offline (Arreglo Local en Código): "
    )
    add_bullet(
        doc,
        "Tanto en el Paso 1 del Onboarding inicial (step1.blade.php) como en la Pestaña de Perfil Público del Centro de Configuración (tab-profile.blade.php), un componente Alpine.js ejecuta fetch('/api/countries') al iniciar, hidrata el buscador desplegable y asocia un mapa de banderas Unicode junto a la opción especial 'Remoto / Global'.",
        bold_prefix="5. Consumo Reactivo en el Frontend: "
    )

    add_code_box(
        doc,
        "1) Backend — Endpoint Proveedor de Países con Caché y Triple Fallback:\n"
        "   -> routes/web.php (Líneas 15 a 57): Ruta GET '/api/countries' (->name('api.countries')).\n\n"
        "2) Frontend — Selector Desplegable con Buscador y Banderas durante el Onboarding Post-Registro:\n"
        "   -> resources/views/onboarding/step1.blade.php (Líneas 60 a 165 y script Alpine en líneas 215 a 265).\n\n"
        "3) Frontend — Selector Desplegable de Países en Edición de Perfil:\n"
        "   -> resources/views/profile/partials/tab-profile.blade.php (Líneas 195 a 280).\n\n"
        "4) Validación y Guardado del País en la Base de Datos (Columna 'location' de la tabla 'users'):\n"
        "   -> app/Http/Controllers/OnboardingController.php (Método saveStep1, líneas 35 a 58).\n"
        "   -> app/Http/Requests/ProfileUpdateRequest.php (Línea 29: 'location' => ['nullable', 'string', 'max:120']).",
        title="UBICACIÓN EXACTA DE ARCHIVOS — ORIGEN Y GESTIÓN DE PAÍSES"
    )

    # --- PREGUNTA 5: OTRAS FUNCIONES CLAVE QUE PUEDEN PREGUNTARSE ---
    add_h1(doc, "Pregunta 5: Otras Funciones Clave que el Profesor Puede Cuestionar (Guía Rápida)")

    add_h2(doc, "5.1. ¿Cómo funciona el Editor de Posicionamiento de Avatar (Circular) y Banner (Rectangular)?")
    add_p(
        doc,
        "Tanto el Avatar como el Banner funcionan sin subir archivos al servidor: el usuario pega el enlace de su imagen y puede arrastrarla con el ratón (drag) y usar un deslizador de zoom (1x a 3x). En lugar de recortar físicamente un archivo nuevo, guardamos en la tabla 'users' los desplazamientos porcentuales y el factor de escala (crop_x, crop_y, crop_zoom para el Avatar; banner_crop_x, banner_crop_y, banner_crop_zoom para el Banner). Luego, cuando cualquier visitante entra al Public Profile del artista, aplicamos una transformación CSS acelerada por hardware: transform: translate(X%, Y%) scale(Zoom) dentro de un contenedor con overflow-hidden.",
        bold_prefix="Explicación Técnica: "
    )
    add_code_box(
        doc,
        "-> Editor Interactivo (Drag & Zoom): resources/views/profile/partials/tab-profile.blade.php (Líneas 18 a 175 y script en líneas 370 a 470).\n"
        "-> Renderizado del Banner Superior y Avatar en Perfil Público: resources/views/profile/show.blade.php (Líneas 10 a 68).\n"
        "-> Validación de Coordenadas (-100 a 100 y Zoom 1 a 3): app/Http/Requests/ProfileUpdateRequest.php (Líneas 86 a 91).",
        title="ARCHIVOS — ENCUADRE DE AVATAR Y BANNER"
    )

    add_h2(doc, "5.2. ¿Cómo funciona la Expiración de Sesión por Inactividad y el Aviso de Extensión?")
    add_p(
        doc,
        "El tiempo de vida de la sesión está configurado en 10 minutos (SESSION_LIFETIME=10 en config/session.php). Para evitar que el artista pierda cambios mientras edita su portafolio, el componente session-timeout-modal.blade.php monitorea eventos de ratón, teclado y scroll. Si detecta 8 minutos de inactividad continua, muestra un modal con una cuenta regresiva de 120 segundos. Al pulsar 'Extender mi sesión', realiza un fetch POST a /session/keep-alive que regenera la marca de tiempo de sesión en el servidor sin recargar la página.",
        bold_prefix="Explicación Técnica: "
    )
    add_code_box(
        doc,
        "-> Configuración de Tiempo de Sesión: config/session.php (Línea 35).\n"
        "-> Endpoint Keep-Alive: routes/web.php (Líneas 71 a 74: POST '/session/keep-alive').\n"
        "-> Modal y Temporizador JS: resources/views/components/session-timeout-modal.blade.php.",
        title="ARCHIVOS — SESIÓN E INACTIVIDAD"
    )

    add_h2(doc, "5.3. ¿Cómo funcionan las Colecciones Públicas, No-Listadas y Privadas y el Botón Rápido en las Obras?")
    add_p(
        doc,
        "Cada colección tiene una columna visibility ('public', 'unlisted', 'private') y un share_token aleatorio de 32 caracteres. La autorización se centraliza en CollectionPolicy.php: (a) 'public' es visible para todos y aparece en el perfil público del artista; (b) 'unlisted' se oculta del perfil público, pero si el dueño comparte el enlace con ?token=..., cualquier cliente con ese enlace puede verla; (c) 'private' bloquea el acceso incluso teniendo el enlace (solo el dueño puede abrirla). Además, cada obra incluye el componente <x-collection-picker /> que permite añadirla a una colección existente o crear una nueva en el acto vía AJAX (POST /collections/quick-create).",
        bold_prefix="Explicación Técnica: "
    )
    add_code_box(
        doc,
        "-> Política de Acceso (3 niveles de privacidad): app/Policies/CollectionPolicy.php.\n"
        "-> Controlador (CRUD + Quick-Create AJAX): app/Http/Controllers/CollectionController.php.\n"
        "-> Botón Desplegable en Obras: resources/views/components/collection-picker.blade.php.\n"
        "-> Vista y Modal de Configuración de Privacidad: resources/views/collections/show.blade.php y index.blade.php.",
        title="ARCHIVOS — COLECCIONES Y PRIVACIDAD"
    )

    add_h2(doc, "5.4. ¿Cómo funcionan la Papelera de 30 Días, el Bloqueo de Usuarios y la Suspensión de Cuentas?")
    add_bullet(
        doc,
        "Cuando un artista elimina una obra, no se hace un DELETE físico inmediato; gracias al trait SoftDeletes en Artwork.php se llena la columna deleted_at y la obra pasa a la Papelera (TrashController.php), desde donde puede restaurarse o borrarse definitivamente. El comando programado 'php artisan artworks:purge-trash' (en routes/console.php) purga automáticamente las obras que llevan más de 30 días en la papelera.",
        bold_prefix="Papelera de Reciclaje (30 días): "
    )
    add_bullet(
        doc,
        "Gestionado por UserBlockController.php y la tabla 'user_blocks'. Si el Usuario A bloquea al Usuario B, ExplorerController filtra las obras de ambos sentidos (whereNotIn('user_id', $excludedIds)) e impide visitar el perfil o enviar mensajes.",
        bold_prefix="Bloqueo Bidireccional: "
    )
    add_bullet(
        doc,
        "El middleware EnsureUserIsActive.php verifica en cada petición si auth()->user()->status === 'suspended'. Si un administrador suspende una cuenta desde AdminController@toggleUserStatus, el middleware cierra la sesión del infractor inmediatamente y lo expulsa al login.",
        bold_prefix="Suspensión Administrativa en Caliente: "
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
            ["Enlaces de DeviantArt", "app/Services/EmbedService.php", "fetchDeviantArtData() (L.256-315)"],
            ["Enlaces de Google Docs / Drive", "app/Services/EmbedService.php", "fetchGoogleDocsData() (L.490-560)"],
            ["Servicio de Mail y Confirmación", "app/Models/User.php\nroutes/auth.php\napp/Mail/ContactArtist.php & LinkDownAlert.php", "implements MustVerifyEmail (L.16)\nRutas verify-email (L.40-55)\nMailpit UI: http://127.0.0.1:8025"],
            ["Almacenamiento (Usuarios y Obras)", "app/Models/User.php & Artwork.php\ndatabase/migrations/*.php", "Tablas MySQL: users, artworks, collections, bookmarks, reports, user_blocks"],
            ["Acceso de Visitante (Guest)", "routes/web.php\napp/Http/Controllers/ExplorerController.php\nresources/views/components/guest-modal.blade.php", "Rutas públicas (L.60-62, 115)\nInterceptor @open-guest-modal.window"],
            ["Países Disponibles en Registro", "routes/web.php\nresources/views/onboarding/step1.blade.php\nresources/views/profile/partials/tab-profile.blade.php", "GET /api/countries con Caché 30d y 3 fallbacks (L.15-57)"],
            ["Recorte de Avatar y Banner", "resources/views/profile/partials/tab-profile.blade.php\nresources/views/profile/show.blade.php", "Drag & Zoom interactivo -> crop_x/y/zoom y banner_crop_x/y/zoom"],
            ["Colecciones y Privacidad", "app/Policies/CollectionPolicy.php\napp/Http/Controllers/CollectionController.php\nresources/views/components/collection-picker.blade.php", "Visibilidad public/unlisted/private y Quick-Create AJAX"]
        ],
        col_widths=[1.85, 2.75, 2.2]
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

