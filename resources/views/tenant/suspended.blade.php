<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicio no disponible</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            color: #1f2937;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 48px 40px;
            max-width: 460px;
            text-align: center;
        }

        .icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: #fff;
            background: {{ $status === 'cancelado' ? '#DC2626' : '#F59E0B' }};
        }

        h1 {
            font-size: 20px;
            margin: 0 0 12px;
        }

        p {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

        .badge {
            display: inline-block;
            margin-top: 20px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            background: {{ $status === 'cancelado' ? 'rgba(220,38,38,.08)' : 'rgba(245,158,11,.08)' }};
            color: {{ $status === 'cancelado' ? '#DC2626' : '#F59E0B' }};
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">!</div>
        <h1>Servicio no disponible</h1>
        <p>{{ $mensaje }}</p>
        <span class="badge">{{ $status }}</span>
    </div>
</body>
</html>
