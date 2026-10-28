<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Unavailable on Artfolio</title>
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
            <h1>Artfolio Moderation</h1>
            <p>Notification regarding the availability of your publication</p>
        </div>

        <!-- Body -->
        <div class="body">
            <div class="greeting">
                Hello, {{ $artist->name }}!
            </div>
            
            <p style="font-size: 14px; color: #334155; line-height: 1.6; margin-top: 0;">
                We are writing to inform you that we received a report indicating that the external media link for one of your artworks is no longer accessible or was taken down on its host platform.
            </p>

            <!-- Alert info -->
            <div class="alert-box">
                <strong>Artwork status: Temporarily hidden.</strong><br>
                To maintain a quality experience in the public gallery, this artwork was switched to <em>Hidden</em>. It remains visible in your personal dashboard, and you can republish it at any time by updating its URL.
            </div>

            <!-- Artwork Details -->
            <div class="artwork-card">
                <div class="artwork-title">{{ $artwork->title }}</div>
                <div class="artwork-url">
                    <strong>Reported external URL:</strong> {{ $artwork->external_url }}
                </div>
            </div>

            @if(!empty($adminNote))
                <div class="admin-note-box">
                    <strong style="color: #6b21a8; display: block; margin-bottom: 4px;">Administrator Note:</strong>
                    {{ $adminNote }}
                </div>
            @endif

            <p style="font-size: 14px; color: #475569; line-height: 1.5;">
                To restore your artwork to the public gallery, edit the artwork, provide a working link, and set its visibility back to published:
            </p>

            <!-- Edit Button -->
            <div class="actions">
                <a href="{{ route('artworks.edit', $artwork->id) }}" class="btn-action">
                    Edit Artwork & Update Link
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 4px 0;">
                This is an automated notification from the <strong>Artfolio</strong> moderation team.
            </p>
            <p style="margin: 0;">
                If you believe this was an error or the link is functioning again, you can reactivate the artwork directly from your artist dashboard.
            </p>
        </div>
    </div>
</body>
</html>
