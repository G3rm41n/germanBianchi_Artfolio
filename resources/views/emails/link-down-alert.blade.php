<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enlace no disponible en Artfolio</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 30px 15px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            padding: 28px 32px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .header p {
            color: #fef3c7;
            font-size: 14px;
            margin: 6px 0 0 0;
        }
        .body {
            padding: 32px;
        }
        .greeting {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .alert-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 16px 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
            color: #92400e;
            line-height: 1.5;
        }
        .artwork-card {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            margin: 20px 0;
        }
        .artwork-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .artwork-url {
            font-size: 13px;
            color: #64748b;
            word-break: break-all;
        }
        .admin-note-box {
            background-color: #faf5ff;
            border: 1px solid #e9d5ff;
            border-radius: 10px;
            padding: 16px;
            margin: 18px 0;
            font-size: 14px;
            color: #475569;
        }
        .actions {
            text-align: center;
            margin: 30px 0 16px 0;
        }
        .btn-action {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            padding: 13px 30px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 32px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Artfolio Moderación</h1>
            <p>Notificación sobre la disponibilidad de tu publicación</p>
        </div>

        <!-- Body -->
        <div class="body">
            <div class="greeting">
                ¡Hola, {{ $artist->name }}!
            </div>
            
            <p style="font-size: 14px; color: #334155; line-height: 1.6; margin-top: 0;">
                Te informamos que hemos recibido un reporte indicando que el enlace multimedia externo de una de tus obras no se encuentra accesible o fue dado de baja en su plataforma de origen.
            </p>

            <!-- Alerta Informativa -->
            <div class="alert-box">
                <strong>Estado de tu obra: Oculta preventivamente.</strong><br>
                Para mantener la mejor experiencia en la galería pública, la obra ha pasado a estado <em>Oculto</em>. Seguirá visible en tu panel personal y podrás reactivarla en cualquier momento actualizando su URL.
            </div>

            <!-- Ficha de la obra -->
            <div class="artwork-card">
                <div class="artwork-title">{{ $artwork->title }}</div>
                <div class="artwork-url">
                    <strong>URL externa reportada:</strong> {{ $artwork->external_url }}
                </div>
            </div>

            @if(!empty($adminNote))
                <div class="admin-note-box">
                    <strong style="color: #6b21a8; display: block; margin-bottom: 4px;">Nota del Administrador:</strong>
                    {{ $adminNote }}
                </div>
            @endif

            <p style="font-size: 14px; color: #475569; line-height: 1.5;">
                Para restablecer la publicación en la galería, accede a la edición de la obra, introduce un enlace válido y vuelve a marcar su visibilidad como publicada:
            </p>

            <!-- Botón de edición -->
            <div class="actions">
                <a href="{{ route('artworks.edit', $artwork->id) }}" class="btn-action">
                    Editar Obra y Actualizar Enlace
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 4px 0;">
                Este es un mensaje automático generado por el equipo de administración de <strong>Artfolio</strong> (Regla RN-02).
            </p>
            <p style="margin: 0;">
                Si consideras que se trata de un error o el enlace ya funciona, puedes reactivarla directamente desde tu panel de artista.
            </p>
        </div>
    </div>
</body>
</html>
