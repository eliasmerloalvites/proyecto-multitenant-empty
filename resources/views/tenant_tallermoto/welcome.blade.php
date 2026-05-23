<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $empresa->nombre ?? 'Moto Center' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 text-slate-800">

    @php
        $empresa = $empresa ?? (object) [
            'nombre' => 'MOTO CENTER',
            'descripcion' => 'Especialistas en mantenimiento y reparación de motos.',
            'telefono' => '987654321',
            'whatsapp' => '51987654321',
            'direccion' => 'Av. Principal 123 - Lima',
            'horario' => 'Lunes a Sábado 8:00am - 7:00pm',
            'banner' => null,
            'logo' => null,
        ];
    @endphp

    <!-- HEADER -->
    <header class="bg-white border-b border-gray-200">

        <div class="max-w-6xl mx-auto px-5 py-4 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <div
                    class="w-12 h-12 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-lg overflow-hidden">

                    @if(!empty($empresa->logo))
                        <img src="{{ asset($empresa->logo) }}"
                            class="w-full h-full object-cover">
                    @else
                        🏍️
                    @endif

                </div>

                <div>

                    <h1 class="font-extrabold text-xl">
                        {{ $empresa->nombre }}
                    </h1>

                    <p class="text-sm text-slate-500">
                        Taller de motos
                    </p>

                </div>

            </div>

            <div class="flex items-center gap-3">

                <a href="{{ tenant_url('tenant.login') }}"
                    class="hidden md:block text-sm font-medium text-slate-600 hover:text-slate-900">
                    Acceso empleados
                </a>

                <a href="https://wa.me/{{ $empresa->whatsapp }}"
                    target="_blank"
                    class="bg-green-500 hover:bg-green-600 text-white px-5 py-2.5 rounded-lg font-semibold transition">
                    WhatsApp
                </a>

            </div>

        </div>

    </header>

    <!-- HERO -->
    <section class="bg-white">

        <div
            class="max-w-6xl mx-auto px-5 py-12 grid lg:grid-cols-2 gap-10 items-center">

            <div>

                <h2 class="text-4xl font-extrabold leading-tight mb-5">
                    Tu moto
                    <br>
                    siempre lista
                    <br>
                    para rodar.
                </h2>

                <p class="text-slate-600 text-lg leading-relaxed mb-8">
                    {{ $empresa->descripcion }}
                </p>

                <div class="flex flex-wrap gap-4">

                    <a href="https://wa.me/{{ $empresa->whatsapp }}"
                        target="_blank"
                        class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 rounded-lg font-semibold transition">
                        Solicitar atención
                    </a>

                    <a href="#servicios"
                        class="border border-slate-300 hover:border-slate-900 px-6 py-3 rounded-lg font-semibold transition">
                        Ver servicios
                    </a>

                </div>

            </div>

            <div>

                <img
                    src="{{ $empresa->banner ? asset($empresa->banner) : 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?q=80&w=1400&auto=format&fit=crop' }}"
                    class="rounded-2xl w-full h-[380px] object-cover border border-gray-200">

            </div>

        </div>

    </section>

    <!-- SERVICIOS -->
    <section id="servicios" class="py-16">

        <div class="max-w-6xl mx-auto px-5">

            <div class="mb-10">

                <h2 class="text-3xl font-extrabold mb-2">
                    Nuestros servicios
                </h2>

                <p class="text-slate-500">
                    Atención rápida y profesional para tu moto.
                </p>

            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">

                <div class="bg-white rounded-2xl p-6 border border-gray-200">

                    <div class="text-4xl mb-4">🛢️</div>

                    <h3 class="font-bold text-lg mb-2">
                        Cambio de aceite
                    </h3>

                    <p class="text-sm text-slate-500 leading-relaxed">
                        Mantenimiento preventivo para alargar la vida del motor.
                    </p>

                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200">

                    <div class="text-4xl mb-4">🔧</div>

                    <h3 class="font-bold text-lg mb-2">
                        Mecánica general
                    </h3>

                    <p class="text-sm text-slate-500 leading-relaxed">
                        Reparación y mantenimiento de motos de todas las marcas.
                    </p>

                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200">

                    <div class="text-4xl mb-4">⚡</div>

                    <h3 class="font-bold text-lg mb-2">
                        Electricidad
                    </h3>

                    <p class="text-sm text-slate-500 leading-relaxed">
                        Solución de fallas eléctricas y batería.
                    </p>

                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200">

                    <div class="text-4xl mb-4">🏍️</div>

                    <h3 class="font-bold text-lg mb-2">
                        Afinamiento
                    </h3>

                    <p class="text-sm text-slate-500 leading-relaxed">
                        Optimización del rendimiento y consumo de tu moto.
                    </p>

                </div>

            </div>

        </div>

    </section>

    <!-- CONTACTO -->
    <section class="pb-16">

        <div class="max-w-6xl mx-auto px-5">

            <div class="bg-white rounded-3xl border border-gray-200 p-8">

                <h2 class="text-3xl font-extrabold mb-8">
                    Información de contacto
                </h2>

                <div class="grid md:grid-cols-3 gap-8">

                    <div>

                        <h3 class="font-bold mb-2">
                            📍 Dirección
                        </h3>

                        <p class="text-slate-500">
                            {{ $empresa->direccion }}
                        </p>

                    </div>

                    <div>

                        <h3 class="font-bold mb-2">
                            📞 Teléfono
                        </h3>

                        <p class="text-slate-500">
                            {{ $empresa->telefono }}
                        </p>

                    </div>

                    <div>

                        <h3 class="font-bold mb-2">
                            🕒 Horario
                        </h3>

                        <p class="text-slate-500">
                            {{ $empresa->horario }}
                        </p>

                    </div>

                </div>

                <div class="mt-10">

                    <a href="https://wa.me/{{ $empresa->whatsapp }}"
                        target="_blank"
                        class="inline-block bg-green-500 hover:bg-green-600 text-white px-8 py-4 rounded-xl font-bold transition">
                        Contactar por WhatsApp
                    </a>

                </div>

            </div>

        </div>

    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-white">

        <div
            class="max-w-6xl mx-auto px-5 py-8 flex flex-col md:flex-row items-center justify-between gap-4">

            <p class="text-sm text-slate-400">
                © {{ date('Y') }} {{ $empresa->nombre }} - Todos los derechos reservados
            </p>

            <a href="{{ tenant_url('tenant.login') }}"
                class="text-sm font-medium hover:text-green-400 transition">
                Acceso empleados
            </a>

        </div>

    </footer>

</body>

</html>