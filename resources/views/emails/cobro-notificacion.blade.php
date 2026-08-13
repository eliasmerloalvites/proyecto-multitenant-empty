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

            @if ($tipo === 'recordatorio')
                <h2 style="font-size:18px;margin-top:0;">Hola, {{ $client->razon_social }} 👋</h2>
                <p style="color:#475569;line-height:1.6;">
                    Te recordamos que tu próximo pago de la plataforma Kael Tech vence el
                    <strong>{{ $fechaCobro->translatedFormat('d \d\e F') }}</strong>.
                </p>
            @elseif ($tipo === 'vencido')
                <h2 style="font-size:18px;margin-top:0;color:#D97706;">Tu pago está vencido</h2>
                <p style="color:#475569;line-height:1.6;">
                    Hola {{ $client->razon_social }}, el pago correspondiente al
                    <strong>{{ $fechaCobro->translatedFormat('d \d\e F') }}</strong> aún no se registra.
                    Regulariza tu cuenta para evitar la suspensión del servicio.
                </p>
            @else
                <h2 style="font-size:18px;margin-top:0;color:#DC2626;">Tu cuenta fue suspendida</h2>
                <p style="color:#475569;line-height:1.6;">
                    Hola {{ $client->razon_social }}, tu servicio fue suspendido por falta de pago del
                    ciclo con vencimiento el <strong>{{ $fechaCobro->translatedFormat('d \d\e F') }}</strong>.
                    Regulariza tu pago para reactivar el acceso.
                </p>
            @endif

            <div style="background:#F8FAFC;border-radius:12px;padding:16px 20px;margin:20px 0;">
                <div style="display:flex;justify-content:space-between;color:#64748B;font-size:13px;">
                    Monto: <strong style="color:#0F172A;">S/ {{ number_format($monto, 2) }}</strong>
                </div>
            </div>

            <p style="color:#94A3B8;font-size:12px;margin-top:30px;">
                Si ya realizaste el pago, ignora este mensaje o contáctanos para confirmarlo.
            </p>

        </div>

    </div>
</body>
</html>
