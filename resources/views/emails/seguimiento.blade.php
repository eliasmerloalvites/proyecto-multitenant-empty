<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $asunto }}</title>
</head>
<body style="margin:0;padding:0;background:#F1F5F9;font-family:Arial,Helvetica,sans-serif;">
    <div style="max-width:520px;margin:30px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.08);">

        <div style="background:linear-gradient(135deg,#2563EB,#7C3AED);padding:24px 30px;color:#fff;">
            <h1 style="margin:0;font-size:20px;">Kael Tech</h1>
        </div>

        <div style="padding:30px;color:#0F172A;">
            <div style="color:#334155;line-height:1.65;font-size:15px;">{!! nl2br(e($cuerpo)) !!}</div>

            @if ($urlBoton)
                <div style="text-align:center;margin:26px 0 6px;">
                    <a href="{{ $urlBoton }}"
                        style="display:inline-block;background:linear-gradient(135deg,#2563EB,#7C3AED);color:#fff;text-decoration:none;padding:14px 32px;border-radius:12px;font-weight:bold;">
                        {{ $textoBoton ?: 'Ir a mi panel' }}
                    </a>
                </div>
            @endif
        </div>

    </div>
</body>
</html>
