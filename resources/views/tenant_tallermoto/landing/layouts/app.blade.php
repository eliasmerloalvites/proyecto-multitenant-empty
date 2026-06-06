<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAEL - Mantenimiento de Motocicletas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Configuramos el fondo oscuro base por si la imagen tarda en cargar */
        body {
            background-color: #030712;
        }
        /* Clase personalizada para lograr el efecto cristal traslúcido idéntico al diseño */
        .glass-panel {
            background: rgba(7, 11, 23, 0.65);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        /* Brillo sutil de neón en los bordes de los botones de consulta */
        .neon-border {
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
    </style>
</head>

<body class="text-gray-300 font-sans antialiased selection:bg-blue-600 selection:text-white min-h-screen flex flex-col justify-between relative bg-no-repeat bg-cover bg-center bg-fixed" >

    @yield('content')

    <script>
        lucide.createIcons();
    </script>

</body>

</html>