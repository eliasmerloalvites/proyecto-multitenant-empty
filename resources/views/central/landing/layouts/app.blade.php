<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Kael - Plataforma SaaS')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        html {
            scroll-behavior: smooth;
        }

        /* Evita que el navbar fijo tape el inicio de cada sección al navegar por ancla */
        section[id] {
            scroll-margin-top: 96px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, #dbeafe 0%, transparent 30%),
                radial-gradient(circle at bottom right, #bfdbfe 0%, transparent 25%),
                #f8fafc;
            color: #0f172a;
        }

        .hero-bg {
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, .45), transparent 30%),
                radial-gradient(circle at bottom right, rgba(14, 165, 233, .35), transparent 35%),
                linear-gradient(135deg, #020617, #0f172a, #111827);
        }

        .glass {
            background: rgba(255, 255, 255, .08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, .12);
        }

        .card-hover {
            transition: .35s;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(15, 23, 42, .12);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            scrollbar-width: none;
        }

        @keyframes float {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
    </style>

    @yield('head')
</head>

<body class="text-slate-800 overflow-x-hidden">

    @include('central.landing.sections.navbar')

    @yield('content')

    {{-- Compartido en todas las páginas: el footer (en cada página) tiene
         un botón "Ver demostración" que abre este modal. --}}
    @include('central.landing.sections.demo-modal')

    @stack('scripts')

</body>

</html>
