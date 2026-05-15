<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kael - Plataforma SaaS</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fb;
        }

        .hero-bg {
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, .35), transparent 30%),
                radial-gradient(circle at bottom right, rgba(99, 102, 241, .35), transparent 30%),
                #020817;
        }

        .glass {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.08);
        }

        .card-hover {
            transition: all .35s ease;
        }

        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, .08);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }


        @keyframes float {

            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }

        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>

<body class="text-slate-800 overflow-x-hidden">

    <!-- NAVBAR -->
    <header class="fixed top-0 left-0 w-full z-50 bg-white/90 backdrop-blur-lg border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-black text-2xl shadow-xl">
                    K
                </div>

                <div>
                    <h1 class="text-2xl font-black leading-none">Kael</h1>
                    <p class="text-xs text-slate-500">Business Cloud Platform</p>
                </div>
            </div>

            <nav class="hidden lg:flex items-center gap-8 font-medium text-[15px]">
                <a href="#inicio" class="text-blue-600">Inicio</a>
                <a href="#soluciones" class="hover:text-blue-600 transition">Soluciones</a>
                <a href="#planes" class="hover:text-blue-600 transition">Planes</a>
                <a href="#clientes" class="hover:text-blue-600 transition">Clientes</a>
                <a href="#contacto" class="hover:text-blue-600 transition">Contacto</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="/login"
                    class="hidden md:flex px-5 py-3 rounded-2xl border border-slate-300 hover:border-blue-600 hover:text-blue-600 transition font-semibold">
                    Iniciar sesión
                </a>

                <a href="#planes"
                    class="bg-blue-600 hover:bg-blue-700 transition text-white font-semibold px-5 py-3 rounded-2xl shadow-xl">
                    Crear empresa
                </a>
            </div>
        </div>
    </header>

    <!-- HERO -->
    <!-- ======================================= -->
    <!-- HERO SECTION - KAEL SAAS -->
    <!-- ======================================= -->

    <section class="relative overflow-hidden bg-[#020817] min-h-screen flex items-center">

    <!-- EFECTOS -->
    <div class="absolute inset-0 overflow-hidden">

        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-blue-600/20 blur-[120px] rounded-full"></div>

        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-indigo-600/20 blur-[120px] rounded-full"></div>

        <div class="absolute inset-0 opacity-[0.05]"
            style="
                background-image: radial-gradient(#ffffff 1px, transparent 1px);
                background-size: 20px 20px;
            ">
        </div>

    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-0 py-24 w-full">

        <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-10 items-center pt-8">

            <!-- LEFT -->
            <div class="max-w-xl">

                <!-- BADGE -->
                <div
                    class="inline-flex items-center gap-2 bg-white/10 border border-white/10 text-white px-5 py-2 rounded-full text-sm font-medium backdrop-blur-md mb-8">

                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>

                    Plataforma #1 para la gestión de negocios

                </div>

                <!-- TITULO -->
                <h1
                    class="text-4xl md:text-5xl xl:text-6xl font-black leading-[1] text-white mb-8 tracking-tight">

                    Crea y administra
                    <br>

                    tu negocio

                    <span class="text-blue-500">
                        en minutos
                    </span>

                </h1>

                <!-- DESCRIPCION -->
                <p class="text-slate-300 text-base md:text-lg leading-relaxed mb-10 max-w-lg">

                    Sistemas completos en la nube para ópticas,
                    restaurantes, ferreterías, veterinarias y cualquier
                    tipo de empresa.

                </p>

                <!-- BOTONES -->
                <div class="flex items-center gap-4 mb-12">

                    <!-- BTN -->
                    <a href="#planes"
                        class="group bg-blue-600 hover:bg-blue-700 text-white px-7 py-4 rounded-2xl font-bold text-base shadow-2xl transition-all duration-300 hover:scale-105 whitespace-nowrap">

                        <span class="flex items-center gap-3">

                            Crear mi empresa ahora

                            <span class="group-hover:translate-x-1 transition">
                                →
                            </span>

                        </span>

                    </a>

                    <!-- BTN -->
                    <a href="#demos"
                        class="group bg-white/5 hover:bg-white/10 border border-white/10 text-white px-7 py-4 rounded-2xl font-bold text-base backdrop-blur-md transition-all duration-300 whitespace-nowrap">

                        <span class="flex items-center gap-3">

                            <div
                                class="w-7 h-7 rounded-full border border-white/30 flex items-center justify-center text-xs">

                                ▶

                            </div>

                            Ver demos en vivo

                        </span>

                    </a>

                </div>

                <!-- BENEFICIOS -->
                <div class="flex flex-wrap gap-8">

                    <div class="flex items-center gap-3 text-slate-300 font-medium">

                        <div
                            class="w-6 h-6 rounded-full border border-blue-500/30 bg-blue-500/10 flex items-center justify-center text-blue-400 text-sm">

                            ✓

                        </div>

                        Sin instalación

                    </div>

                    <div class="flex items-center gap-3 text-slate-300 font-medium">

                        <div
                            class="w-6 h-6 rounded-full border border-blue-500/30 bg-blue-500/10 flex items-center justify-center text-blue-400 text-sm">

                            ☁

                        </div>

                        En la nube

                    </div>

                    <div class="flex items-center gap-3 text-slate-300 font-medium">

                        <div
                            class="w-6 h-6 rounded-full border border-blue-500/30 bg-blue-500/10 flex items-center justify-center text-blue-400 text-sm">

                            🛡

                        </div>

                        Seguro y confiable

                    </div>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="relative flex justify-center lg:justify-end">

                <!-- WEB -->
                <img
                    src="{{ asset('/images/web/dashboard-web.png') }}"
                    class="
                        relative
                        z-10
                        w-full
                        max-w-[980px]
                        object-contain

                        rounded-[20px]

                        border
                        border-white/10

                        shadow-[0_40px_100px_rgba(0,0,0,.55)]

                        overflow-hidden
                    ">

                <!-- MOBILE -->
                <img
                    src="{{ asset('/images/web/dashboard-mobile.png') }}"
                    class="
                        hidden
                        md:block
                        absolute
                        bottom-[-40px]
                        right-[-40px]
                        w-[120px]
                        xl:w-[130px]
                        z-20
                        rotate-[-2deg]

                        rounded-[20px]

                        shadow-[0_30px_80px_rgba(0,0,0,.65)]

                        animate-float
                    ">

            </div>

        </div>

    </div>

</section>

    <!-- SOLUCIONES -->
    <section id="soluciones" class="py-24 bg-[#f5f7fb] overflow-hidden">

        <div class="max-w-7xl mx-auto px-6">

            <!-- TITULO -->
            <div class="text-center mb-16">

                <h2 class="text-5xl md:text-6xl font-black text-slate-900 mb-5 leading-tight">
                    Soluciones diseñadas para cada negocio
                </h2>

                <p class="text-slate-500 text-xl max-w-3xl mx-auto">
                    Elige el sistema ideal para tu rubro y empieza a crecer.
                </p>
            </div>

            @php

                $rubros = [
                    [
                        'nombre' => 'Ópticas',
                        'descripcion' => 'Ventas, recetas, control de monturas, exámenes visuales y catálogo web.',
                        'icono' => '👓',
                        'imagen' =>
                            'https://images.unsplash.com/photo-1511499767150-a48a237f0083?q=80&w=1200&auto=format&fit=crop',
                        'color' => 'bg-violet-100 text-violet-600',
                    ],

                    [
                        'nombre' => 'Restaurantes',
                        'descripcion' => 'Pedidos, mesas, cocina, delivery y control de inventario.',
                        'icono' => '🍔',
                        'imagen' =>
                            'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?q=80&w=1200&auto=format&fit=crop',
                        'color' => 'bg-orange-100 text-orange-600',
                    ],

                    [
                        'nombre' => 'Ferreterías',
                        'descripcion' => 'Inventario, cotizaciones, ventas al por mayor y proveedores.',
                        'icono' => '🛠️',
                        'imagen' =>
                            'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=1200&auto=format&fit=crop',
                        'color' => 'bg-green-100 text-green-600',
                    ],

                    [
                        'nombre' => 'Veterinarias',
                        'descripcion' => 'Historial clínico, citas, vacunas, pacientes y más.',
                        'icono' => '🐶',
                        'imagen' =>
                            'https://images.unsplash.com/photo-1517849845537-4d257902454a?q=80&w=1200&auto=format&fit=crop',
                        'color' => 'bg-indigo-100 text-indigo-600',
                    ],

                    [
                        'nombre' => 'Farmacias',
                        'descripcion' => 'Control de medicamentos, ventas, lotes y vencimientos.',
                        'icono' => '💊',
                        'imagen' =>
                            'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?q=80&w=1200&auto=format&fit=crop',
                        'color' => 'bg-emerald-100 text-emerald-600',
                    ],

                    [
                        'nombre' => 'Minimarkets',
                        'descripcion' => 'POS rápido, códigos de barras y control de stock.',
                        'icono' => '🛒',
                        'imagen' =>
                            'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1200&auto=format&fit=crop',
                        'color' => 'bg-yellow-100 text-yellow-700',
                    ],

                    [
                        'nombre' => 'Salones & Spa',
                        'descripcion' => 'Reservas, clientes, servicios y caja diaria.',
                        'icono' => '💇',
                        'imagen' =>
                            'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?q=80&w=1200&auto=format&fit=crop',
                        'color' => 'bg-pink-100 text-pink-600',
                    ],

                    [
                        'nombre' => 'Tiendas de ropa',
                        'descripcion' => 'Tallas, colores, inventario y ventas rápidas.',
                        'icono' => '👕',
                        'imagen' =>
                            'https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1200&auto=format&fit=crop',
                        'color' => 'bg-blue-100 text-blue-600',
                    ],

                    [
                        'nombre' => 'Genérico',
                        'descripcion' => 'Adaptable a cualquier tipo de negocio o empresa.',
                        'icono' => '🏢',
                        'imagen' =>
                            'https://images.unsplash.com/photo-1497366754035-f200968a6e72?q=80&w=1200&auto=format&fit=crop',
                        'color' => 'bg-slate-100 text-slate-700',
                    ],
                ];

            @endphp

            <!-- WRAPPER -->
            <div class="relative">

                <!-- BOTON IZQUIERDA -->
                <button id="prevBtn"
                    class="hidden lg:flex absolute -left-6 top-1/2 -translate-y-1/2 z-20 w-16 h-16 rounded-full bg-white shadow-2xl items-center justify-center text-3xl font-bold hover:scale-110 transition">

                    ←
                </button>

                <!-- BOTON DERECHA -->
                <button id="nextBtn"
                    class="hidden lg:flex absolute -right-6 top-1/2 -translate-y-1/2 z-20 w-16 h-16 rounded-full bg-white shadow-2xl items-center justify-center text-3xl font-bold hover:scale-110 transition">

                    →
                </button>

                <!-- CAROUSEL -->
                <div id="carousel" class="flex gap-8 overflow-x-auto scroll-smooth no-scrollbar pb-4">

                    @foreach ($rubros as $rubro)
                        <div
                            class="min-w-[340px] max-w-[340px] bg-white rounded-[32px] overflow-hidden shadow-xl border border-slate-100 hover:-translate-y-3 hover:shadow-2xl transition-all duration-500">

                            <!-- IMAGEN -->
                            <div class="relative h-64 overflow-hidden">

                                <img src="{{ $rubro['imagen'] }}"
                                    class="w-full h-full object-cover hover:scale-110 transition duration-700">

                                <!-- ICONO -->
                                <div
                                    class="absolute top-5 left-5 w-16 h-16 rounded-2xl {{ $rubro['color'] }} flex items-center justify-center text-3xl shadow-xl">

                                    {{ $rubro['icono'] }}

                                </div>

                                <!-- OVERLAY -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent">
                                </div>

                            </div>

                            <!-- CONTENT -->
                            <div class="p-8">

                                <h3 class="text-4xl font-black text-slate-900 mb-4 leading-tight">
                                    {{ $rubro['nombre'] }}
                                </h3>

                                <p class="text-slate-500 leading-relaxed text-lg mb-8 min-h-[120px]">
                                    {{ $rubro['descripcion'] }}
                                </p>

                                <a href="#"
                                    class="inline-flex items-center gap-2 text-blue-600 font-black text-xl hover:gap-4 transition-all">

                                    Ver demo

                                    <span>→</span>
                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>

                <!-- PAGINATION -->
                <div class="flex justify-center gap-3 mt-10">

                    @for ($i = 0; $i < 6; $i++)
                        <div class="w-3 h-3 rounded-full {{ $i == 0 ? 'bg-blue-600 w-10' : 'bg-slate-300' }}"></div>
                    @endfor

                </div>
            </div>
        </div>
    </section>

    <!-- PASOS -->
    <section class="pb-24">
        <div class="max-w-6xl mx-auto px-6">

            <div class="text-center mb-16">
                <h2 class="text-5xl font-black mb-5">
                    Así de fácil es empezar
                </h2>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">

                <div class="bg-white rounded-[32px] p-10 shadow-xl border border-slate-100 text-center card-hover">
                    <div
                        class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-3xl font-black mx-auto mb-8">
                        1
                    </div>

                    <h3 class="text-3xl font-black mb-4">Crea tu empresa</h3>

                    <p class="text-slate-500 text-lg leading-relaxed">
                        Registra tu empresa en pocos minutos.
                    </p>
                </div>

                <div class="bg-white rounded-[32px] p-10 shadow-xl border border-slate-100 text-center card-hover">
                    <div
                        class="w-20 h-20 rounded-full bg-indigo-600 text-white flex items-center justify-center text-3xl font-black mx-auto mb-8">
                        2
                    </div>

                    <h3 class="text-3xl font-black mb-4">Obtén tu subdominio</h3>

                    <p class="text-slate-500 text-lg leading-relaxed">
                        Tu sistema se crea automáticamente.
                    </p>
                </div>

                <div class="bg-white rounded-[32px] p-10 shadow-xl border border-slate-100 text-center card-hover">
                    <div
                        class="w-20 h-20 rounded-full bg-violet-600 text-white flex items-center justify-center text-3xl font-black mx-auto mb-8">
                        3
                    </div>

                    <h3 class="text-3xl font-black mb-4">Empieza a vender</h3>

                    <p class="text-slate-500 text-lg leading-relaxed">
                        Configura tu negocio y comienza hoy.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- BENEFICIOS -->
    <section class="pb-24">
        <div class="max-w-7xl mx-auto px-6">

            <div class="bg-white rounded-[36px] shadow-2xl border border-slate-100 p-12">

                <div class="text-center mb-14">
                    <h2 class="text-5xl font-black mb-5">
                        Todo lo que necesitas para crecer
                    </h2>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-6 gap-8">

                    @php
                        $beneficios = [
                            ['☁️', 'En la nube'],
                            ['🔒', 'Seguro'],
                            ['⚡', 'Rápido'],
                            ['📱', 'Responsive'],
                            ['📊', 'Reportes'],
                            ['🌐', 'Catálogo web'],
                        ];
                    @endphp

                    @foreach ($beneficios as $beneficio)
                        <div class="text-center">
                            <div
                                class="w-20 h-20 rounded-3xl bg-blue-100 flex items-center justify-center text-4xl mx-auto mb-5">
                                {{ $beneficio[0] }}
                            </div>

                            <h3 class="font-black text-xl mb-2">
                                {{ $beneficio[1] }}
                            </h3>

                            <p class="text-slate-500 text-sm">
                                Funcionalidades avanzadas incluidas.
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- PLANES -->
    <section id="planes" class="pb-24">

        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center mb-16">
                <h2 class="text-5xl font-black mb-5">
                    Planes para cada etapa de tu negocio
                </h2>

                <p class="text-slate-500 text-xl">
                    Elige el plan que mejor se adapte a tus necesidades.
                </p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 items-center">

                <!-- BASICO -->
                <div class="bg-white rounded-[36px] p-10 shadow-xl border border-slate-100 card-hover">

                    <div class="mb-8">
                        <h3 class="text-3xl font-black mb-3">Básico</h3>
                        <p class="text-slate-500">Ideal para empezar</p>
                    </div>

                    <div class="mb-8">
                        <span class="text-6xl font-black">S/49</span>
                        <span class="text-slate-500">/ mes</span>
                    </div>

                    <ul class="space-y-4 mb-10 text-slate-600">
                        <li>✅ 1 usuario</li>
                        <li>✅ Ventas e inventario</li>
                        <li>✅ Reportes básicos</li>
                        <li>✅ Soporte WhatsApp</li>
                    </ul>

                    <button
                        class="w-full border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition py-4 rounded-2xl font-bold">
                        Elegir plan
                    </button>
                </div>

                <!-- PROFESIONAL -->
                <div
                    class="bg-gradient-to-b from-blue-600 to-indigo-700 text-white rounded-[40px] p-10 shadow-2xl scale-105 relative overflow-hidden">

                    <div
                        class="absolute top-5 right-5 bg-white text-blue-600 px-4 py-2 rounded-full text-sm font-black">
                        MÁS POPULAR
                    </div>

                    <div class="mb-8">
                        <h3 class="text-3xl font-black mb-3">Profesional</h3>
                        <p class="text-blue-100">Todo lo necesario para crecer</p>
                    </div>

                    <div class="mb-8">
                        <span class="text-7xl font-black">S/99</span>
                        <span class="text-blue-100">/ mes</span>
                    </div>

                    <ul class="space-y-4 mb-10 text-blue-50">
                        <li>✅ Usuarios ilimitados</li>
                        <li>✅ Catálogo web incluido</li>
                        <li>✅ WhatsApp integrado</li>
                        <li>✅ Reportes avanzados</li>
                        <li>✅ Respaldo automático</li>
                    </ul>

                    <button
                        class="w-full bg-white text-blue-700 hover:bg-slate-100 transition py-5 rounded-2xl font-black text-lg shadow-2xl">
                        Elegir plan
                    </button>
                </div>

                <!-- EMPRESA -->
                <div class="bg-white rounded-[36px] p-10 shadow-xl border border-slate-100 card-hover">

                    <div class="mb-8">
                        <h3 class="text-3xl font-black mb-3">Empresa</h3>
                        <p class="text-slate-500">Máxima potencia y control</p>
                    </div>

                    <div class="mb-8">
                        <span class="text-6xl font-black">S/199</span>
                        <span class="text-slate-500">/ mes</span>
                    </div>

                    <ul class="space-y-4 mb-10 text-slate-600">
                        <li>✅ Sucursales</li>
                        <li>✅ API y webhooks</li>
                        <li>✅ Personalización avanzada</li>
                        <li>✅ Soporte prioritario</li>
                    </ul>

                    <button
                        class="w-full border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white transition py-4 rounded-2xl font-bold">
                        Elegir plan
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="pb-24">
        <div class="max-w-7xl mx-auto px-6">

            <div
                class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-[40px] overflow-hidden relative p-14 text-white shadow-2xl">

                <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>

                <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">

                    <div>
                        <h2 class="text-5xl font-black leading-tight mb-6">
                            ¿Listo para llevar tu negocio al siguiente nivel?
                        </h2>

                        <p class="text-blue-100 text-xl mb-10">
                            Únete a cientos de empresas que ya confían en Kael.
                        </p>

                        <div class="flex flex-wrap gap-4">
                            <a href="#planes"
                                class="bg-white text-blue-700 hover:bg-slate-100 px-8 py-5 rounded-2xl font-black shadow-2xl transition">
                                Crear empresa ahora
                            </a>

                            <a href="https://wa.me/51999999999"
                                class="glass border border-white/10 hover:bg-white/10 px-8 py-5 rounded-2xl font-bold transition">
                                Hablar por WhatsApp
                            </a>
                        </div>
                    </div>

                    <div>
                        <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600&auto=format&fit=crop"
                            class="rounded-[32px] shadow-2xl w-full h-[360px] object-cover border border-white/10">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer id="contacto" class="bg-[#020817] text-white py-20">

        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 lg:grid-cols-5 gap-10">

            <div class="lg:col-span-2">

                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-black text-3xl shadow-2xl">
                        K
                    </div>

                    <div>
                        <h3 class="text-3xl font-black">Kael</h3>
                        <p class="text-slate-400">Business Cloud Platform</p>
                    </div>
                </div>

                <p class="text-slate-400 leading-relaxed max-w-md mb-8">
                    Plataforma en la nube para administrar negocios modernos.
                </p>

                <div class="flex gap-4 text-2xl">
                    <div>📘</div>
                    <div>📸</div>
                    <div>🎵</div>
                    <div>▶️</div>
                </div>
            </div>

            <div>
                <h3 class="font-black text-xl mb-6">Soluciones</h3>

                <ul class="space-y-4 text-slate-400">
                    <li>Ópticas</li>
                    <li>Restaurantes</li>
                    <li>Ferreterías</li>
                    <li>Veterinarias</li>
                    <li>Genérico</li>
                </ul>
            </div>

            <div>
                <h3 class="font-black text-xl mb-6">Empresa</h3>

                <ul class="space-y-4 text-slate-400">
                    <li>Nosotros</li>
                    <li>Planes</li>
                    <li>Demos</li>
                    <li>Contacto</li>
                </ul>
            </div>

            <div>
                <h3 class="font-black text-xl mb-6">Contacto</h3>

                <ul class="space-y-4 text-slate-400">
                    <li>📞 +51 987 654 321</li>
                    <li>✉️ hola@kael.pe</li>
                    <li>📍 Lima, Perú</li>
                </ul>
            </div>
        </div>

        <div
            class="max-w-7xl mx-auto px-6 pt-10 mt-10 border-t border-white/10 flex flex-col md:flex-row justify-between gap-4 text-slate-500 text-sm">

            <p>© 2026 Kael. Todos los derechos reservados.</p>

            <p>Hecho con ❤️ por Kael</p>
        </div>
    </footer>

</body>

<!-- ============================= -->
<!-- JS -->
<!-- ============================= -->

<script>
    const carousel = document.getElementById('carousel');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');

    nextBtn.addEventListener('click', () => {

        carousel.scrollBy({
            left: 380,
            behavior: 'smooth'
        });

    });

    prevBtn.addEventListener('click', () => {

        carousel.scrollBy({
            left: -380,
            behavior: 'smooth'
        });

    });
</script>

</html>
