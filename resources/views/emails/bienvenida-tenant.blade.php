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

            <h2 style="font-size:18px;margin-top:0;">¡Bienvenido, {{ $razonSocial }}! 🎉</h2>

            <p style="color:#475569;line-height:1.6;">
                Tu cuenta y tu panel de administración ya están listos. Guarda este correo:
                aquí siempre vas a encontrar el enlace de acceso a tu taller.
            </p>

            <div style="text-align:center;margin:26px 0;">
                <a href="{{ $urlLogin }}"
                    style="display:inline-block;background:linear-gradient(135deg,#2563EB,#7C3AED);color:#fff;text-decoration:none;padding:14px 32px;border-radius:12px;font-weight:bold;">
                    Ir a mi panel
                </a>
            </div>

            <div style="background:#F8FAFC;border-radius:12px;padding:16px 20px;margin:20px 0;font-size:13px;color:#475569;">
                <div style="margin-bottom:6px;"><strong>Enlace de acceso:</strong> {{ $urlLogin }}</div>
                <div style="margin-bottom:6px;"><strong>Usuario:</strong> {{ $email }}</div>
                <div><strong>Plan contratado:</strong> {{ $planNombre }}</div>
            </div>

            <p style="color:#94A3B8;font-size:12px;">
                Por seguridad no incluimos tu contraseña en este correo — usa la que registraste
                al crear tu cuenta. Si la olvidaste, puedes recuperarla desde la pantalla de inicio de sesión.
            </p>

        </div>

    </div>
</body>
</html>
