<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kael - Plataforma SaaS</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-600 to-indigo-700 flex items-center justify-center text-white font-black text-2xl shadow-xl">
                    K
                </div>

                <div>
                    <h1 class="text-2xl font-black leading-none">Kael</h1>
                    <p class="text-xs text-slate-500">Business Cloud Platform</p>
                </div>
            </div>

            <nav class="hidden lg:flex items-center gap-8 font-medium text-[15px]">
                <a href="#inicio" class="text-brand-600">Inicio</a>
                <a href="#soluciones" class="hover:text-brand-600 transition">Soluciones</a>
                <a href="#planes" class="hover:text-brand-600 transition">Planes</a>
                <a href="#clientes" class="hover:text-brand-600 transition">Clientes</a>
                <a href="#contacto" class="hover:text-brand-600 transition">Contacto</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="/login"
                    class="hidden md:flex px-5 py-3 rounded-2xl border border-slate-300 hover:border-brand-600 hover:text-brand-600 transition font-semibold">
                    Iniciar sesión
                </a>

                <a href="#planes"
                    class="bg-brand-600 hover:bg-brand-700 transition text-white font-semibold px-5 py-3 rounded-2xl shadow-xl">
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

            <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-brand-600/20 blur-[120px] rounded-full"></div>

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

                        <span class="w-2 h-2 rounded-full bg-brand-500"></span>

                        Plataforma #1 para la gestión de negocios

                    </div>

                    <!-- TITULO -->
                    <h1 class="text-4xl md:text-5xl xl:text-6xl font-black leading-[1] text-white mb-8 tracking-tight">

                        Crea y administra
                        <br>

                        tu negocio

                        <span class="text-brand-500">
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
                            class="group bg-brand-600 hover:bg-brand-700 text-white px-7 py-4 rounded-2xl font-bold text-base shadow-2xl transition-all duration-300 hover:scale-105 whitespace-nowrap">

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
                                class="w-6 h-6 rounded-full border border-brand-500/30 bg-brand-500/10 flex items-center justify-center text-brand-400 text-sm">

                                ✓

                            </div>

                            Sin instalación

                        </div>

                        <div class="flex items-center gap-3 text-slate-300 font-medium">

                            <div
                                class="w-6 h-6 rounded-full border border-brand-500/30 bg-brand-500/10 flex items-center justify-center text-brand-400 text-sm">

                                ☁

                            </div>

                            En la nube

                        </div>

                        <div class="flex items-center gap-3 text-slate-300 font-medium">

                            <div
                                class="w-6 h-6 rounded-full border border-brand-500/30 bg-brand-500/10 flex items-center justify-center text-brand-400 text-sm">

                                🛡

                            </div>

                            Seguro y confiable

                        </div>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="relative flex justify-center lg:justify-end">

                    <!-- WEB -->
                    <img src="{{ asset('/images/web/dashboard-web.png') }}"
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
                    <img src="{{ asset('/images/web/dashboard-mobile.png') }}"
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

    {{-- ========================================= --}}
    {{-- SOLUCIONES --}}
    {{-- ========================================= --}}

    <section id="soluciones" class="py-24 bg-[#f8fafc] overflow-hidden">

        <div class="max-w-7xl mx-auto px-6">

            {{-- TITLE --}}
            <div class="text-center mb-16">

                <span
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-brand-50 text-brand-600 text-sm font-semibold mb-5">

                    NUESTRAS SOLUCIONES

                </span>

                <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-5">

                    Dos soluciones,
                    <span class="text-brand-600">
                        un mismo objetivo
                    </span>

                </h2>

                <p class="text-slate-500 text-lg max-w-3xl mx-auto leading-relaxed">

                    Sistemas modernos diseñados para ayudarte a gestionar,
                    vender y hacer crecer tu negocio.

                </p>

            </div>

            {{-- GRID --}}
            <div class="grid lg:grid-cols-2 gap-8">

                {{-- ================================= --}}
                {{-- POS --}}
                {{-- ================================= --}}

                <div class="bg-white border border-slate-200 rounded-[32px] p-8 overflow-hidden relative">

                    <div class="grid grid-cols-[0.9fr_1.1fr] items-center gap-4">

                        {{-- LEFT --}}
                        <div>

                            {{-- HEADER --}}
                            <div class="flex items-start gap-4 mb-6">

                                <div
                                    class="w-16 h-16 rounded-2xl bg-brand-600 shadow-lg flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-cart-shopping text-white text-2xl"></i>

                                </div>

                                <div>

                                    <h3 class="text-[20px] font-black leading-tight text-slate-900">

                                        KAELTECH
                                        <span class="text-brand-600">
                                            POS
                                        </span>

                                    </h3>

                                    <p class="text-slate-500 text-sm font-medium">
                                        Sistema Genérico
                                    </p>

                                </div>

                            </div>

                            {{-- DESC --}}
                            <p class="text-slate-600 text-[15px] leading-7 mb-6">

                                Ideal para tiendas, bodegas, minimarkets y
                                todo tipo de negocios que necesitan controlar
                                sus ventas, inventario y facturación.

                            </p>

                            {{-- FEATURES --}}
                            <div class="space-y-3 mb-7">

                                <div class="flex items-center gap-3 text-[15px] text-slate-700">

                                    <div class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center">

                                        <i class="fa-solid fa-check text-[10px] text-brand-600"></i>

                                    </div>

                                    Ventas e inventario

                                </div>

                                <div class="flex items-center gap-3 text-[15px] text-slate-700">

                                    <div class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center">

                                        <i class="fa-solid fa-check text-[10px] text-brand-600"></i>

                                    </div>

                                    Clientes y proveedores

                                </div>

                                <div class="flex items-center gap-3 text-[15px] text-slate-700">

                                    <div class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center">

                                        <i class="fa-solid fa-check text-[10px] text-brand-600"></i>

                                    </div>

                                    Reportes de ventas

                                </div>

                                <div class="flex items-center gap-3 text-[15px] text-slate-700">

                                    <div class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center">

                                        <i class="fa-solid fa-check text-[10px] text-brand-600"></i>

                                    </div>

                                    Control de productos

                                </div>

                            </div>

                            {{-- BUTTON --}}
                            <a href="#planes-pos"
                                class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 transition text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg">

                                Conocer más

                                <i class="fa-solid fa-arrow-right text-xs"></i>

                            </a>

                        </div>

                        {{-- RIGHT --}}
                        <div class="flex justify-center items-end">

                            <img src="{{ asset('/images/web/pos-system.png') }}"
                                class="w-full max-w-[360px] object-contain">

                        </div>

                    </div>

                </div>

                {{-- ================================= --}}
                {{-- MOTO --}}
                {{-- ================================= --}}

                <div class="bg-white border border-green-200 rounded-[32px] p-8 overflow-hidden relative">

                    <div class="grid grid-cols-[0.9fr_1.1fr] items-center gap-4">

                        {{-- LEFT --}}
                        <div>

                            {{-- HEADER --}}
                            <div class="flex items-start gap-4 mb-6">

                                <div
                                    class="w-16 h-16 rounded-2xl bg-green-600 shadow-lg flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-motorcycle text-white text-2xl"></i>

                                </div>

                                <div>

                                    <h3 class="text-[20px] font-black leading-tight text-slate-900">

                                        KAELTECH
                                        <span class="text-green-600">
                                            MOTO
                                        </span>

                                    </h3>

                                    <p class="text-slate-500 text-sm font-medium">
                                        Sistema para Talleres de Motos
                                    </p>

                                </div>

                            </div>

                            {{-- DESC --}}
                            <p class="text-slate-600 text-[15px] leading-7 mb-6">

                                Especializado para talleres de motos.
                                Controla mantenimientos, clientes,
                                técnicos, repuestos y mucho más.

                            </p>

                            {{-- FEATURES --}}
                            <div class="space-y-3 mb-7">

                                <div class="flex items-center gap-3 text-[15px] text-slate-700">

                                    <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">

                                        <i class="fa-solid fa-check text-[10px] text-green-600"></i>

                                    </div>

                                    Órdenes de servicio

                                </div>

                                <div class="flex items-center gap-3 text-[15px] text-slate-700">

                                    <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">

                                        <i class="fa-solid fa-check text-[10px] text-green-600"></i>

                                    </div>

                                    Historial por moto y cliente

                                </div>

                                <div class="flex items-center gap-3 text-[15px] text-slate-700">

                                    <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">

                                        <i class="fa-solid fa-check text-[10px] text-green-600"></i>

                                    </div>

                                    Agenda y técnicos

                                </div>

                                <div class="flex items-center gap-3 text-[15px] text-slate-700">

                                    <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">

                                        <i class="fa-solid fa-check text-[10px] text-green-600"></i>

                                    </div>

                                    Reportes y estadísticas

                                </div>

                            </div>

                            {{-- BUTTON --}}
                            <a href="#planes-moto"
                                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 transition text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg">

                                Conocer más

                                <i class="fa-solid fa-arrow-right text-xs"></i>

                            </a>

                        </div>

                        {{-- RIGHT --}}
                        <div class="flex justify-center items-end">

                            <img src="{{ asset('/images/web/moto-system.png') }}"
                                class="w-full max-w-[420px] object-contain">

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {{-- ========================================= --}}
    {{-- PLANES POS --}}
    {{-- ========================================= --}}

    <section id="planes-pos" class="py-24 bg-[#f8fafc] overflow-hidden">

        <div class="max-w-5xl mx-auto px-6">

            {{-- HEADER --}}
            <div class="text-center mb-14">

                <div class="inline-flex items-center gap-3 mb-4">

                    <div class="w-12 h-12 rounded-2xl bg-brand-600 flex items-center justify-center shadow-lg">

                        <i class="fa-solid fa-cart-shopping text-white"></i>

                    </div>

                    <div class="text-left">

                        <h2 class="text-3xl font-black text-slate-900">

                            PLANES KAELTECH
                            <span class="text-brand-600">
                                POS
                            </span>

                        </h2>

                        <p class="text-slate-500 text-sm">
                            Planes simples para empezar rápido
                        </p>

                    </div>

                </div>

            </div>

            {{-- CARDS --}}
            <div class="grid md:grid-cols-2 gap-8">

                {{-- PLAN BASICO --}}
                <div
                    class="bg-white rounded-[28px] border border-slate-200 p-8 shadow-sm hover:shadow-xl transition-all duration-300">

                    <div class="text-center mb-8">

                        <span
                            class="inline-flex px-4 py-2 rounded-full bg-brand-50 text-brand-600 text-xs font-bold mb-5">

                            PLAN BÁSICO

                        </span>

                        <p class="text-slate-500 text-sm mb-4">
                            Ideal para empezar
                        </p>

                        <div class="flex items-end justify-center gap-1">

                            <span class="text-2xl font-semibold text-slate-700">
                                S/
                            </span>

                            <span class="text-6xl font-black text-slate-900 leading-none">
                                29
                            </span>

                            <span class="text-slate-500 mb-1">
                                /mes
                            </span>

                        </div>

                    </div>

                    {{-- FEATURES --}}
                    <div class="space-y-4 mb-8">

                        <div class="flex items-center gap-3 text-slate-700 text-[15px]">

                            <div class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center shrink-0">

                                <i class="fa-solid fa-check text-[10px] text-brand-600"></i>

                            </div>

                            Ventas e inventario

                        </div>

                        <div class="flex items-center gap-3 text-slate-700 text-[15px]">

                            <div class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center shrink-0">

                                <i class="fa-solid fa-check text-[10px] text-brand-600"></i>

                            </div>

                            Clientes y proveedores

                        </div>

                        <div class="flex items-center gap-3 text-slate-700 text-[15px]">

                            <div class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center shrink-0">

                                <i class="fa-solid fa-check text-[10px] text-brand-600"></i>

                            </div>

                            Reportes básicos

                        </div>

                        <div class="flex items-center gap-3 text-slate-700 text-[15px]">

                            <div class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center shrink-0">

                                <i class="fa-solid fa-check text-[10px] text-brand-600"></i>

                            </div>

                            Sin facturación electrónica

                        </div>

                    </div>

                    {{-- BUTTON --}}
                    <button
                        class="w-full bg-brand-600 hover:bg-brand-700 transition text-white py-4 rounded-xl font-bold text-sm shadow-lg">

                        Probar gratis 14 días

                    </button>

                </div>

                {{-- PLAN FACTURACION --}}
                <div
                    class="bg-gradient-to-b from-brand-600 to-blue-700 rounded-[28px] p-8 text-white shadow-2xl relative overflow-hidden">

                    {{-- GLOW --}}
                    <div class="absolute top-0 right-0 w-52 h-52 bg-white/10 rounded-full blur-3xl">
                    </div>

                    {{-- BADGE --}}
                    <div
                        class="absolute top-5 right-5 bg-white text-brand-700 px-4 py-2 rounded-full text-xs font-black shadow-lg">

                        MÁS POPULAR

                    </div>

                    <div class="relative z-10">

                        <div class="text-center mb-8">

                            <span
                                class="inline-flex px-4 py-2 rounded-full bg-white/10 text-white text-xs font-bold mb-5">

                                PLAN FACTURACIÓN

                            </span>

                            <p class="text-brand-100 text-sm mb-4">
                                Para crecer tu negocio
                            </p>

                            <div class="flex items-end justify-center gap-1">

                                <span class="text-2xl font-semibold text-brand-100">
                                    S/
                                </span>

                                <span class="text-6xl font-black leading-none">
                                    69
                                </span>

                                <span class="text-brand-100 mb-1">
                                    /mes
                                </span>

                            </div>

                        </div>

                        {{-- FEATURES --}}
                        <div class="space-y-4 mb-8">

                            <div class="flex items-center gap-3 text-[15px]">

                                <div
                                    class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-check text-[10px]"></i>

                                </div>

                                Todo lo del plan Básico

                            </div>

                            <div class="flex items-center gap-3 text-[15px]">

                                <div
                                    class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-check text-[10px]"></i>

                                </div>

                                Facturación electrónica (SUNAT)

                            </div>

                            <div class="flex items-center gap-3 text-[15px]">

                                <div
                                    class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-check text-[10px]"></i>

                                </div>

                                Cotizaciones y boletas

                            </div>

                            <div class="flex items-center gap-3 text-[15px]">

                                <div
                                    class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-check text-[10px]"></i>

                                </div>

                                Reportes avanzados

                            </div>

                        </div>

                        {{-- BUTTON --}}
                        <button
                            class="w-full bg-white hover:bg-slate-100 transition text-brand-700 py-4 rounded-xl font-black text-sm shadow-xl">

                            Probar gratis 14 días

                        </button>

                    </div>

                </div>

            </div>

            {{-- FOOT --}}
            <div class="flex items-center justify-center gap-2 mt-8 text-slate-500 text-sm">

                <i class="fa-regular fa-circle-check text-brand-600"></i>

                Sin permanencias. Cancela cuando quieras.

            </div>

        </div>

    </section>

    {{-- ========================================= --}}
{{-- PLANES KAELTECH MOTO --}}
{{-- ========================================= --}}

<section id="planes-moto" class="py-20 bg-[#f4f7fb] overflow-hidden">

    <div class="max-w-7xl mx-auto px-6">

        {{-- ================================= --}}
        {{-- HEADER --}}
        {{-- ================================= --}}

        <div
            class="relative overflow-hidden rounded-[32px] bg-[#020817] border border-slate-800 mb-10">

            {{-- EFFECTS --}}
            <div
                class="absolute top-0 left-0 w-[350px] h-[350px] bg-brand-600/20 blur-[120px] rounded-full">
            </div>

            <div
                class="absolute bottom-0 right-0 w-[350px] h-[350px] bg-green-600/20 blur-[120px] rounded-full">
            </div>

            <div
                class="relative z-10 grid lg:grid-cols-[1fr_480px] gap-10 items-center p-8 lg:p-12">

                {{-- LEFT --}}
                <div>

                    {{-- TOP --}}
                    <div class="flex items-center gap-4 mb-6">

                        <div
                            class="w-14 h-14 rounded-2xl bg-green-600 flex items-center justify-center shadow-xl">

                            <i class="fa-solid fa-motorcycle text-white text-xl"></i>

                        </div>

                        <div>

                            <h2 class="text-3xl font-black text-white">

                                PLANES KAELTECH
                                <span class="text-green-500">
                                    MOTO
                                </span>

                            </h2>

                            <p class="text-slate-400 text-sm mt-1">
                                Sistema de gestión para talleres de motos
                            </p>

                        </div>

                    </div>

                    {{-- TITLE --}}
                    <h1
                        class="text-4xl md:text-5xl font-black leading-tight text-white mb-5 uppercase">

                        Haz que tu taller
                        trabaje como

                        <span class="text-green-500">
                            empresa
                        </span>

                    </h1>

                    <p class="text-slate-300 text-lg mb-8 max-w-2xl">

                        Más orden, más clientes y más ganancias
                        desde el primer mes.

                    </p>

                    {{-- BENEFITS --}}
                    <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-5">

                        {{-- ITEM --}}
                        <div class="flex items-start gap-3">

                            <div
                                class="w-12 h-12 rounded-2xl bg-green-500/10 border border-green-500/20 flex items-center justify-center shrink-0">

                                <i
                                    class="fa-regular fa-calendar-check text-green-400"></i>

                            </div>

                            <div>

                                <h3 class="text-white font-black text-sm">
                                    MÁS ORDEN
                                </h3>

                                <p class="text-slate-400 text-xs">
                                    en tus procesos
                                </p>

                            </div>

                        </div>

                        {{-- ITEM --}}
                        <div class="flex items-start gap-3">

                            <div
                                class="w-12 h-12 rounded-2xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center shrink-0">

                                <i
                                    class="fa-solid fa-shield-halved text-brand-400"></i>

                            </div>

                            <div>

                                <h3 class="text-white font-black text-sm">
                                    MÁS CONTROL
                                </h3>

                                <p class="text-slate-400 text-xs">
                                    de tu taller
                                </p>

                            </div>

                        </div>

                        {{-- ITEM --}}
                        <div class="flex items-start gap-3">

                            <div
                                class="w-12 h-12 rounded-2xl bg-green-500/10 border border-green-500/20 flex items-center justify-center shrink-0">

                                <i
                                    class="fa-solid fa-chart-line text-green-400"></i>

                            </div>

                            <div>

                                <h3 class="text-white font-black text-sm">
                                    MÁS CLIENTES
                                </h3>

                                <p class="text-slate-400 text-xs">
                                    y más ganancias
                                </p>

                            </div>

                        </div>

                        {{-- ITEM --}}
                        <div class="flex items-start gap-3">

                            <div
                                class="w-12 h-12 rounded-2xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center shrink-0">

                                <i
                                    class="fa-regular fa-star text-brand-400"></i>

                            </div>

                            <div>

                                <h3 class="text-white font-black text-sm">
                                    MEJOR IMAGEN
                                </h3>

                                <p class="text-slate-400 text-xs">
                                    profesionalismo
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- RIGHT --}}
                <div class="relative flex justify-center">

                    {{-- DASHBOARD --}}
                    <img
                        src="{{ asset('/images/web/moto-dashboard.png') }}"
                        class="w-full max-w-[480px] relative z-10">

                    {{-- PHONE --}}
                    <img
                        src="{{ asset('/images/web/moto-phone.png') }}"
                        class="absolute bottom-0 right-4 w-[250px] z-20">

                </div>

            </div>

        </div>

        {{-- ================================= --}}
        {{-- CARDS --}}
        {{-- ================================= --}}

        <div class="grid xl:grid-cols-3 gap-6">

            {{-- ================================= --}}
            {{-- STARTER --}}
            {{-- ================================= --}}

            <div
                class="relative overflow-hidden rounded-[30px] bg-gradient-to-b from-[#119c36] to-[#0d7428] text-white shadow-[0_15px_40px_rgba(16,185,129,.25)]">

                <div class="p-6">

                    {{-- TOP --}}
                    <div class="flex items-start justify-between mb-5">

                        <div>

                            <h3 class="text-3xl font-black uppercase leading-none">

                                PLAN
                                <br>

                                <span class="text-green-300">
                                    STARTER
                                </span>

                            </h3>

                            <p class="text-green-100 mt-3 text-sm">
                                Ideal para talleres pequeños
                            </p>

                        </div>

                        <div
                            class="px-4 py-2 rounded-full bg-green-400/20 border border-green-300/20 text-xs font-black">

                            INICIAL

                        </div>

                    </div>

                    {{-- PRICE --}}
                    <div
                        class="bg-gradient-to-r from-green-500 to-green-600 rounded-[22px] p-5 mb-6 shadow-xl">

                        <div class="text-center">

                            <div class="flex items-end justify-center gap-2">

                                <span class="text-2xl font-bold">
                                    S/
                                </span>

                                <span
                                    class="text-5xl font-black tracking-tight leading-none">

                                    99 - 149

                                </span>

                            </div>

                            <div
                                class="uppercase text-green-100 font-bold tracking-widest text-sm mt-2">

                                POR MES

                            </div>

                        </div>

                    </div>

                    {{-- FEATURES --}}
                    <div class="space-y-4 mb-6 text-[15px]">

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-200"></i>
                            Agenda semanal visual
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-200"></i>
                            Reservas de citas
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-200"></i>
                            Gestión de clientes
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-200"></i>
                            Historial básico de motos
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-200"></i>
                            Mantenimientos y servicios
                        </div>

                    </div>

                    {{-- FOOT --}}
                    <div
                        class="rounded-2xl border border-green-300/20 bg-black/20 p-4 mb-6">

                        <div class="space-y-3 text-sm">

                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-shop"></i>
                                1 sucursal
                            </div>

                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-users"></i>
                                Usuarios limitados
                            </div>

                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-ban"></i>
                                Sin personalización
                            </div>

                        </div>

                    </div>

                    {{-- BUTTON --}}
                    <button
                        class="w-full bg-white hover:bg-slate-100 transition text-green-700 py-4 rounded-2xl text-sm font-black shadow-xl">

                        Solicitar información

                    </button>

                </div>

            </div>

            {{-- ================================= --}}
            {{-- PROFESIONAL --}}
            {{-- ================================= --}}

            <div
                class="relative overflow-hidden rounded-[30px] bg-gradient-to-b from-[#0d56f0] to-[#0835a1] text-white shadow-[0_20px_60px_rgba(37,99,235,.35)] scale-[1.01]">

                {{-- BADGE --}}
                <div
                    class="absolute top-5 right-5 bg-white text-brand-700 px-5 py-2 rounded-full font-black text-xs shadow-xl">

                    MÁS VENDIDO

                </div>

                <div class="p-6">

                    <div class="mb-5">

                        <h3 class="text-3xl font-black uppercase leading-none">

                            PLAN
                            <br>

                            <span class="text-brand-300">
                                PROFESIONAL
                            </span>

                        </h3>

                        <p class="text-brand-100 mt-3 text-sm">
                            Para talleres que buscan crecer
                        </p>

                    </div>

                    {{-- PRICE --}}
                    <div
                        class="bg-gradient-to-r from-brand-500 to-blue-600 rounded-[22px] p-5 mb-6 shadow-xl">

                        <div class="text-center">

                            <div class="flex items-end justify-center gap-2">

                                <span class="text-2xl font-bold">
                                    S/
                                </span>

                                <span
                                    class="text-5xl font-black tracking-tight leading-none">

                                    249 - 399

                                </span>

                            </div>

                            <div
                                class="uppercase text-brand-100 font-bold tracking-widest text-sm mt-2">

                                POR MES

                            </div>

                        </div>

                    </div>

                    {{-- FEATURES --}}
                    <div class="space-y-4 mb-6 text-[15px]">

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-brand-200"></i>
                            Todo lo del plan Starter
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-brand-200"></i>
                            Página web profesional
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-brand-200"></i>
                            Historial completo por placa
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-brand-200"></i>
                            Reportes PDF profesionales
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-brand-200"></i>
                            Integración con WhatsApp
                        </div>

                    </div>

                    {{-- BUTTON --}}
                    <button
                        class="w-full bg-white hover:bg-slate-100 transition text-brand-700 py-4 rounded-2xl text-sm font-black shadow-xl">

                        Agendar demo

                    </button>

                </div>

            </div>

            {{-- ================================= --}}
            {{-- EMPRESA --}}
            {{-- ================================= --}}

            <div
                class="relative overflow-hidden rounded-[30px] bg-gradient-to-b from-[#ea0914] to-[#a1070e] text-white shadow-[0_15px_40px_rgba(239,68,68,.35)]">

                <div class="p-6">

                    <div class="flex items-start justify-between mb-5">

                        <div>

                            <h3 class="text-3xl font-black uppercase leading-none">

                                PLAN
                                <br>

                                <span class="text-red-300">
                                    EMPRESA
                                </span>

                            </h3>

                            <p class="text-red-100 mt-3 text-sm">
                                Solución personalizada
                            </p>

                        </div>

                        <div
                            class="px-4 py-2 rounded-full bg-red-400/20 border border-red-300/20 text-xs font-black">

                            ENTERPRISE

                        </div>

                    </div>

                    {{-- PRICE --}}
                    <div
                        class="bg-gradient-to-r from-red-500 to-red-600 rounded-[22px] p-5 mb-6 shadow-xl">

                        <div class="text-center">

                            <div class="flex items-end justify-center gap-2">

                                <span class="text-2xl font-bold">
                                    S/
                                </span>

                                <span
                                    class="text-5xl font-black tracking-tight leading-none">

                                    3,500

                                </span>

                            </div>

                            <div
                                class="uppercase text-red-100 font-bold tracking-widest text-sm mt-2">

                                PAGO ÚNICO

                            </div>

                        </div>

                    </div>

                    {{-- FEATURES --}}
                    <div class="space-y-4 mb-6 text-[15px]">

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-red-200"></i>
                            Sistema 100% personalizado
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-red-200"></i>
                            Marca y dominio propio
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-red-200"></i>
                            Implementación completa
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-red-200"></i>
                            Capacitación a tu equipo
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-red-200"></i>
                            Soporte preferencial
                        </div>

                    </div>

                    {{-- BUTTON --}}
                    <button
                        class="w-full bg-white hover:bg-slate-100 transition text-red-700 py-4 rounded-2xl text-sm font-black shadow-xl">

                        Solicitar cotización

                    </button>

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
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-600 to-indigo-700 flex items-center justify-center text-white font-black text-3xl shadow-2xl">
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
