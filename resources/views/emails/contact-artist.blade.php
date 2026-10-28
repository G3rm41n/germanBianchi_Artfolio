<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Mensaje en Artfolio</title>
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
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 28px 32px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .header p {
            color: #e0e7ff;
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
        .meta-box {
            background-color: #f1f5f9;
            border-left: 4px solid #6366f1;
            padding: 16px 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .meta-row {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .meta-row:last-child {
            margin-bottom: 0;
        }
        .meta-label {
            font-weight: 700;
            color: #475569;
            display: inline-block;
            min-width: 85px;
        }
        .meta-value {
            color: #0f172a;
            font-weight: 500;
        }
        .message-box {
            background-color: #faf5ff;
            border: 1px solid #e9d5ff;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            font-size: 15px;
            line-height: 1.6;
            color: #334155;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .actions {
            text-align: center;
            margin: 28px 0 16px 0;
        }
        .btn-reply {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            padding: 12px 28px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
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
            <h1>Artfolio</h1>
            <p>Plataforma de Exhibición y Contacto Multimedia</p>
        </div>

        <!-- Body -->
        <div class="body">
            <div class="greeting">
                ¡Hola, {{ $recipientName }}!
            </div>
            <p style="margin: 0 0 16px 0; font-size: 14px; color: #475569;">
                Has recibido una nueva consulta de un usuario interesado en tu trabajo a través de tu portafolio público.
            </p>

            <!-- Sender Meta -->
            <div class="meta-box">
                <div class="meta-row">
                    <span class="meta-label">Remitente:</span>
                    <span class="meta-value">{{ $senderName }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Correo:</span>
                    <span class="meta-value">{{ $senderEmail }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Motivo:</span>
                    <span class="meta-value" style="color: #4f46e5; font-weight: 700;">{{ $subjectText }}</span>
                </div>
            </div>

            <!-- Message Content -->
            <div style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                Mensaje recibido:
            </div>
            <div class="message-box">{!! nl2br(e($messageText)) !!}</div>

            <!-- Reply action -->
            <div class="actions">
                <a href="mailto:{{ $senderEmail }}?subject=Re: {{ rawurlencode($subjectText) }}" class="btn-reply">
                    Responder a {{ $senderName }}
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 4px 0;">
                Este correo fue despachado de forma segura mediante el sistema anti-suplantación de <strong>Artfolio</strong>.
            </p>
            <p style="margin: 0;">
                Tu dirección de correo permanece privada y protegida en la plataforma.
            </p>
        </div>
    </div>
</body>
</html>
