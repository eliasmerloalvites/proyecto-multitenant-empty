<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Kael Tech</title>
</head>
<body style="margin:0;padding:0;background:#F1F5F9;font-family:Arial,Helvetica,sans-serif;">
    <div style="max-width:520px;margin:30px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.08);">

        <div style="background:linear-gradient(135deg,#2563EB,#7C3AED);padding:24px 30px;color:#fff;">
            <h1 style="margin:0;font-size:20px;">Kael Tech</h1>
        </div>

        <div style="padding:30px;color:#0F172A;">

            <h2 style="font-size:18px;margin-top:0;">Hola, {{ $nombre }}</h2>

            <p style="color:#475569;line-height:1.6;">
                Recibimos una solicitud para restablecer tu contraseña. Haz clic en el botón
                para elegir una nueva.
            </p>

            <div style="text-align:center;margin:30px 0;">
                <a href="{{ $urlReset }}"
                    style="display:inline-block;background:linear-gradient(135deg,#2563EB,#7C3AED);color:#fff;text-decoration:none;padding:14px 32px;border-radius:12px;font-weight:bold;">
                    Restablecer mi contraseña
                </a>
            </div>

            <p style="color:#94A3B8;font-size:12px;">
                Este enlace vence en 60 minutos. Si no fuiste tú quien lo solicitó, puedes ignorar
                este correo — tu contraseña actual sigue siendo válida.
            </p>

        </div>

    </div>
</body>
</html>
