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
</head>

<body class="text-slate-800 overflow-x-hidden">

    <!-- NAVBAR -->
    <header
        class="fixed top-0 left-0 w-full z-50
bg-white/80 backdrop-blur-xl
border-b border-slate-200/70
shadow-lg shadow-slate-900/5">

        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            <!-- Logo -->
            <div class="flex items-center gap-4">

                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 p-[2px] shadow-lg">

                    <div class="w-full h-full rounded-2xl bg-white flex items-center justify-center">

                        <img src="{{ asset('images/icono.jpg') }}" class="w-9 h-9 object-contain rounded-lg">

                    </div>

                </div>

                <div>

                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Kael Tech
                    </h1>

                    <p class="text-xs text-slate-500 tracking-wide">
                        Business Cloud Platform
                    </p>

                </div>

            </div>

            <!-- Menú -->
            <nav class="hidden lg:flex items-center gap-8 text-[15px] font-semibold">

                <a href="#inicio" class="text-blue-600 hover:text-blue-700 transition">
                    Inicio
                </a>

                <a href="#soluciones" class="text-slate-600 hover:text-blue-600 transition">
                    Soluciones
                </a>

                <a href="#planes" class="text-slate-600 hover:text-blue-600 transition">
                    Planes
                </a>

                <a href="#clientes" class="text-slate-600 hover:text-blue-600 transition">
                    Clientes
                </a>

                <a href="#contacto" class="text-slate-600 hover:text-blue-600 transition">
                    Contacto
                </a>

            </nav>

            <!-- Botones -->
            <div class="flex items-center gap-3">

                <a href="/login"
                    class="hidden md:flex px-5 py-3 rounded-xl
                border border-slate-300
                hover:border-blue-600
                hover:text-blue-600
                transition
                font-semibold">

                    Iniciar sesión

                </a>

                <a href="#planes"
                    class="px-6 py-3 rounded-xl
                bg-gradient-to-r from-blue-600 to-cyan-500
                hover:from-blue-700 hover:to-cyan-600
                text-white
                font-semibold
                shadow-xl shadow-blue-600/30
                transition-all">

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

            <div class="absolute -top-40 -left-32 w-[650px] h-[650px] bg-blue-500/20 blur-[180px] rounded-full"></div>

            <div class="absolute -bottom-40 -right-32 w-[650px] h-[650px] bg-cyan-400/15 blur-[180px] rounded-full">
            </div>

            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[900px] bg-sky-400/5 blur-[220px] rounded-full">
            </div>
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
                        class="inline-flex items-center gap-3
                            bg-white/10
                            border border-white/15
                            backdrop-blur-xl
                            px-5 py-2.5
                            rounded-full
                            text-white
                            shadow-lg">

                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></span>

                        <span class="font-semibold">
                            Plataforma SaaS para empresas
                        </span>

                    </div>

                    <!-- TITULO -->
                    <h1 class="text-4xl md:text-5xl xl:text-6xl font-black leading-[1] text-white mb-8 tracking-tight">

                        Crea y administra
                        <br>

                        tu negocio

                        <span
                            class="bg-gradient-to-r
                        from-blue-400
                        via-cyan-300
                        to-sky-500
                        bg-clip-text
                        text-transparent">

                            en minutos

                        </span>

                    </h1>

                    <!-- DESCRIPCION -->
                    <p class="text-slate-300 text-lg leading-8 max-w-xl">

                        Sistemas completos en la nube para ópticas,
                        restaurantes, ferreterías, veterinarias y cualquier
                        tipo de empresa.

                    </p>

                    <!-- BOTONES -->
                    <div class="flex items-center gap-4 mb-12">

                        <!-- BTN -->
                        <a href="#planes"
                            class="group px-8 py-4 rounded-xl font-bold text-white bg-gradient-to-r
                                from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 shadow-xl shadow-blue-600/30 transition-all duration-300
                                hover:-translate-y-1">

                            <span class="flex items-center gap-3">

                                Crear mi empresa ahora

                                <span class="group-hover:translate-x-1 transition">
                                    →
                                </span>

                            </span>

                        </a>

                        <!-- BTN -->
                        <a href="#demos"
                            class="group px-8 py-4 rounded-xl border border-white/15 
                                bg-white/5 hover:bg-white/10 backdrop-blur-xl text-white transition-all duration-300 hover:border-cyan-400/40">

                            <span class="flex items-center gap-3">

                                <div
                                    class="w-7 h-7 rounded-full border border-cyan-400/40
                                        bg-cyan-400/10 flex items-center justify-center">

                                    <i class="fa-solid fa-play text-cyan-400 text-xs"></i>

                                </div>

                                <span class="font-semibold">
                                    Ver demos en vivo
                                </span>

                            </span>

                        </a>

                    </div>

                    <!-- BENEFICIOS -->
                    <div class="flex flex-wrap gap-8">

                        <div class="flex items-center gap-3 text-slate-300 font-medium">

                            <div
                                class="w-6 h-6 rounded-full border border-brand-500/30 bg-brand-500/10 flex items-center justify-center text-brand-400 text-sm">

                                <i class="fa-solid fa-circle-check text-cyan-400"></i>

                            </div>

                            Sin instalación

                        </div>

                        <div class="flex items-center gap-3 text-slate-300 font-medium">

                            <div
                                class="w-6 h-6 rounded-full border border-brand-500/30 bg-brand-500/10 flex items-center justify-center text-brand-400 text-sm">

                                <i class="fa-solid fa-cloud text-cyan-400"></i>

                            </div>

                            En la nube

                        </div>

                        <div class="flex items-center gap-3 text-slate-300 font-medium">

                            <div
                                class="w-6 h-6 rounded-full border border-brand-500/30 bg-brand-500/10 flex items-center justify-center text-brand-400 text-sm">

                                <i class="fa-solid fa-shield-halved text-cyan-400"></i>

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

    <section id="soluciones"
        class="relative py-28 overflow-hidden bg-gradient-to-b from-slate-50 via-white to-slate-100">

        <!-- Luces de fondo -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">

            <div class="absolute -top-56 -left-44 w-[650px] h-[650px] rounded-full bg-blue-400/10 blur-[170px]">
            </div>

            <div class="absolute -bottom-56 -right-44 w-[650px] h-[650px] rounded-full bg-cyan-400/10 blur-[170px]">
            </div>

        </div>

        <div class="relative max-w-7xl mx-auto px-6">

            <!-- ========================= -->
            <!-- TITULO -->
            <!-- ========================= -->

            <div class="text-center mb-20">

                <span
                    class="inline-flex items-center gap-2 rounded-full bg-blue-50 text-blue-700 px-5 py-2 font-bold text-sm mb-6">

                    <i class="fa-solid fa-layer-group"></i>

                    NUESTRAS SOLUCIONES

                </span>

                <h2 class="text-5xl lg:text-6xl font-black leading-tight text-slate-900">

                    Dos soluciones,

                    <span class="bg-gradient-to-r from-blue-600 via-cyan-500 to-sky-500 bg-clip-text text-transparent">

                        un mismo objetivo

                    </span>

                </h2>

                <p class="mt-7 text-xl text-slate-600 leading-9 max-w-3xl mx-auto">

                    Sistemas modernos diseñados para ayudarte a gestionar,
                    automatizar y hacer crecer tu empresa desde una sola plataforma.

                </p>

            </div>

            <!-- ========================= -->
            <!-- GRID -->
            <!-- ========================= -->

            <div class="grid lg:grid-cols-2 gap-8">

                <!-- ================================================= -->
                <!-- POS -->
                <!-- ================================================= -->

                <div
                    class="group relative rounded-[34px] border border-slate-200 bg-white shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden">

                    <!-- Barra superior -->

                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-600">
                    </div>

                    <div class="p-8 h-full">

                        <div class="grid grid-cols-[1fr_1fr] gap-6 items-center h-full">

                            <!-- ================================= -->
                            <!-- TEXTO -->
                            <!-- ================================= -->

                            <div>

                                <span
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 text-blue-700 text-xs font-bold mb-6">

                                    <i class="fa-solid fa-fire"></i>

                                    MÁS VENDIDO

                                </span>

                                <!-- Header -->

                                <div class="flex items-center gap-4 mb-6">

                                    <div
                                        class="w-16 h-16 rounded-3xl bg-gradient-to-br from-blue-600 to-cyan-500 shadow-lg flex items-center justify-center">

                                        <i class="fa-solid fa-cash-register text-white text-2xl"></i>

                                    </div>

                                    <div>

                                        <h3 class="text-4xl font-black text-slate-900">

                                            Kael POS

                                        </h3>

                                        <p class="text-slate-500 text-lg">

                                            Sistema comercial para negocios

                                        </p>

                                    </div>

                                </div>

                                <!-- Descripción -->

                                <p class="text-slate-600 leading-8 mb-8">

                                    Gestiona ventas, inventario, clientes,
                                    proveedores, caja, compras y facturación
                                    electrónica desde cualquier dispositivo.

                                </p>

                                <!-- Características -->

                                <div class="space-y-4 mb-8">

                                    <div class="flex items-center gap-3">

                                        <i class="fa-solid fa-circle-check text-blue-600"></i>

                                        <span class="text-slate-700 font-medium">

                                            Ventas e inventario

                                        </span>

                                    </div>

                                    <div class="flex items-center gap-3">

                                        <i class="fa-solid fa-circle-check text-blue-600"></i>

                                        <span class="text-slate-700 font-medium">

                                            Facturación electrónica

                                        </span>

                                    </div>

                                    <div class="flex items-center gap-3">

                                        <i class="fa-solid fa-circle-check text-blue-600"></i>

                                        <span class="text-slate-700 font-medium">

                                            Clientes y proveedores

                                        </span>

                                    </div>

                                    <div class="flex items-center gap-3">

                                        <i class="fa-solid fa-circle-check text-blue-600"></i>

                                        <span class="text-slate-700 font-medium">

                                            Reportes inteligentes

                                        </span>

                                    </div>

                                </div>

                                <!-- Botón -->

                                <a href="#planes-pos"
                                    class="inline-flex items-center gap-3 px-7 py-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold shadow-xl shadow-blue-600/20 hover:shadow-blue-600/40 hover:scale-105 transition">

                                    Conocer más

                                    <i class="fa-solid fa-arrow-right"></i>

                                </a>

                            </div>

                            <!-- ================================= -->
                            <!-- IMAGEN -->
                            <!-- ================================= -->

                            <div class="relative flex items-center justify-center">

                                <!-- Halo -->

                                <div
                                    class="absolute w-[320px] h-[320px] rounded-full bg-blue-400/15 blur-[90px] group-hover:scale-110 transition duration-500">
                                </div>

                                <img src="{{ asset('/images/web/pos-system.png') }}"
                                    class="relative z-20 w-full max-w-[420px] object-contain transition duration-500 group-hover:scale-105">

                            </div>

                        </div>

                    </div>

                </div>
                <!-- ================================================= -->
                <!-- MOTO -->
                <!-- ================================================= -->

                <div
                    class="group relative rounded-[34px] overflow-hidden bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 border border-slate-700 shadow-2xl hover:shadow-cyan-500/20 transition-all duration-500 hover:-translate-y-2">

                    <!-- Barra superior -->

                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400">
                    </div>

                    <div class="p-8 h-full">

                        <div class="grid grid-cols-[1fr_1fr] gap-6 items-center h-full">

                            <!-- ================================= -->
                            <!-- TEXTO -->
                            <!-- ================================= -->

                            <div>

                                <span
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-cyan-500/15 text-cyan-300 text-xs font-bold mb-6 border border-cyan-400/20">

                                    <i class="fa-solid fa-motorcycle"></i>

                                    SOLUCIÓN ESPECIALIZADA

                                </span>

                                <!-- Header -->

                                <div class="flex items-center gap-4 mb-6">

                                    <div
                                        class="w-16 h-16 rounded-3xl bg-gradient-to-br from-cyan-500 to-blue-600 shadow-xl shadow-cyan-500/30 flex items-center justify-center">

                                        <i class="fa-solid fa-motorcycle text-white text-2xl"></i>

                                    </div>

                                    <div>

                                        <h3 class="text-4xl font-black text-white">

                                            Kael Moto

                                        </h3>

                                        <p class="text-slate-300 text-lg">

                                            Software para talleres de motos

                                        </p>

                                    </div>

                                </div>

                                <!-- Descripción -->

                                <p class="text-slate-300 leading-8 mb-8">

                                    Administra órdenes de servicio,
                                    mantenimientos, historial por moto,
                                    agenda de técnicos, clientes,
                                    repuestos y mucho más desde una sola plataforma.

                                </p>

                                <!-- Características -->

                                <div class="space-y-4 mb-8">

                                    <div class="flex items-center gap-3">

                                        <i class="fa-solid fa-circle-check text-cyan-400"></i>

                                        <span class="text-slate-100 font-medium">

                                            Órdenes de servicio

                                        </span>

                                    </div>

                                    <div class="flex items-center gap-3">

                                        <i class="fa-solid fa-circle-check text-cyan-400"></i>

                                        <span class="text-slate-100 font-medium">

                                            Historial por moto

                                        </span>

                                    </div>

                                    <div class="flex items-center gap-3">

                                        <i class="fa-solid fa-circle-check text-cyan-400"></i>

                                        <span class="text-slate-100 font-medium">

                                            Agenda de técnicos

                                        </span>

                                    </div>

                                    <div class="flex items-center gap-3">

                                        <i class="fa-solid fa-circle-check text-cyan-400"></i>

                                        <span class="text-slate-100 font-medium">

                                            Repuestos y mantenimiento

                                        </span>

                                    </div>

                                </div>

                                <!-- Botón -->

                                <a href="#planes-moto"
                                    class="inline-flex items-center gap-3
                                px-7 py-4
                                rounded-xl
                                font-bold
                                text-white
                                bg-gradient-to-r
                                from-cyan-500
                                to-blue-600
                                shadow-xl
                                shadow-cyan-500/20
                                hover:shadow-cyan-500/40
                                hover:scale-105
                                transition-all">

                                    Conocer más

                                    <i class="fa-solid fa-arrow-right"></i>

                                </a>

                            </div>

                            <!-- ================================= -->
                            <!-- IMAGEN -->
                            <!-- ================================= -->

                            <div class="relative flex items-center justify-center">

                                <!-- Halo -->

                                <div
                                    class="absolute
                                w-[340px]
                                h-[340px]
                                rounded-full
                                bg-cyan-400/15
                                blur-[100px]
                                group-hover:scale-110
                                transition
                                duration-500">
                                </div>

                                <img src="{{ asset('/images/web/moto-system.png') }}"
                                    class="relative
                                z-20
                                w-full
                                max-w-[430px]
                                object-contain
                                transition
                                duration-500
                                group-hover:scale-105">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {{-- ========================================= --}}
    {{-- PLANES POS --}}
    {{-- ========================================= --}}

    <section id="planes-pos"
        class="relative py-28 overflow-hidden bg-gradient-to-b from-white via-slate-50 to-slate-100">

        <!-- Background -->

        <div class="absolute inset-0 overflow-hidden pointer-events-none">

            <div class="absolute -top-56 -left-56 w-[650px] h-[650px] rounded-full bg-blue-400/10 blur-[170px]">
            </div>

            <div class="absolute -bottom-56 -right-56 w-[650px] h-[650px] rounded-full bg-cyan-400/10 blur-[170px]">
            </div>

        </div>

        <div class="relative max-w-6xl mx-auto px-6">

            <!-- ====================================== -->
            <!-- HEADER -->
            <!-- ====================================== -->

            <div class="text-center mb-20">

                <div
                    class="inline-flex items-center gap-3 px-6 py-3 rounded-full bg-blue-50 text-blue-700 font-bold text-sm mb-7">

                    <i class="fa-solid fa-credit-card"></i>

                    PLANES KAEL POS

                </div>

                <h2 class="text-5xl lg:text-6xl font-black text-slate-900 leading-tight">

                    Elige el plan

                    <span class="bg-gradient-to-r from-blue-600 via-cyan-500 to-sky-500 bg-clip-text text-transparent">

                        ideal para tu negocio

                    </span>

                </h2>

                <p class="mt-8 max-w-3xl mx-auto text-xl text-slate-600 leading-9">

                    Empieza gratis durante 14 días. Sin contratos,
                    sin permanencia y con soporte incluido.

                </p>

            </div>

            <!-- ====================================== -->
            <!-- GRID -->
            <!-- ====================================== -->

            <div class="grid lg:grid-cols-2 gap-10">

                <!-- ======================================================= -->
                <!-- PLAN BASICO -->
                <!-- ======================================================= -->

                <div
                    class="group relative overflow-hidden rounded-[34px] border border-slate-200 bg-white shadow-xl hover:-translate-y-2 hover:shadow-2xl transition-all duration-500">

                    <!-- Barra superior -->

                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-cyan-500 to-blue-500">
                    </div>

                    <div class="p-10">

                        <!-- Badge -->

                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-blue-50 text-blue-700 px-4 py-2 text-xs font-bold mb-7">

                            <i class="fa-solid fa-rocket"></i>

                            PLAN BÁSICO

                        </span>

                        <!-- Titulo -->

                        <h3 class="text-3xl font-black text-slate-900">

                            Empieza hoy

                        </h3>

                        <p class="text-slate-500 mt-2">

                            Ideal para pequeños negocios que recién comienzan.

                        </p>

                        <!-- Precio -->

                        <div class="my-10">

                            <div class="flex items-end gap-2">

                                <span class="text-3xl font-semibold text-slate-500">

                                    S/

                                </span>

                                <span class="text-7xl font-black text-slate-900 leading-none">

                                    29

                                </span>

                                <span class="text-slate-500 mb-2">

                                    /mes

                                </span>

                            </div>

                            <p class="mt-3 text-sm text-slate-500">

                                Sin pagos ocultos.

                            </p>

                        </div>

                        <!-- Características -->

                        <div class="space-y-5 mb-10">

                            <div class="flex items-center gap-4">

                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">

                                    <i class="fa-solid fa-check text-blue-600 text-sm"></i>

                                </div>

                                <span class="font-medium text-slate-700">

                                    Ventas e inventario

                                </span>

                            </div>

                            <div class="flex items-center gap-4">

                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">

                                    <i class="fa-solid fa-check text-blue-600 text-sm"></i>

                                </div>

                                <span class="font-medium text-slate-700">

                                    Clientes y proveedores

                                </span>

                            </div>

                            <div class="flex items-center gap-4">

                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">

                                    <i class="fa-solid fa-check text-blue-600 text-sm"></i>

                                </div>

                                <span class="font-medium text-slate-700">

                                    Control de caja

                                </span>

                            </div>

                            <div class="flex items-center gap-4">

                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">

                                    <i class="fa-solid fa-check text-blue-600 text-sm"></i>

                                </div>

                                <span class="font-medium text-slate-700">

                                    Reportes básicos

                                </span>

                            </div>

                            <div class="flex items-center gap-4">

                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">

                                    <i class="fa-solid fa-xmark text-red-500 text-sm"></i>

                                </div>

                                <span class="text-slate-500">

                                    Sin facturación electrónica

                                </span>

                            </div>

                        </div>

                        <!-- Botón -->

                        <button
                            class="w-full
                        py-4
                        rounded-xl
                        font-bold
                        text-white
                        bg-gradient-to-r
                        from-blue-600
                        to-cyan-500
                        hover:from-blue-700
                        hover:to-cyan-600
                        shadow-xl
                        shadow-blue-500/20
                        transition-all
                        duration-300">

                            Probar gratis durante 14 días

                        </button>

                        <!-- Footer -->

                        <div class="flex items-center justify-center gap-2 mt-6 text-sm text-slate-500">

                            <i class="fa-solid fa-circle-check text-green-500"></i>

                            Sin contratos ni permanencia.

                        </div>

                    </div>

                </div>

                <!-- ======================================================= -->
                <!-- PLAN PROFESIONAL -->
                <!-- ======================================================= -->

                <div
                    class="group relative overflow-hidden rounded-[34px]
                bg-gradient-to-br
                from-slate-900
                via-blue-950
                to-slate-900
                text-white
                shadow-2xl
                border
                border-slate-700
                hover:-translate-y-2
                hover:shadow-cyan-500/20
                transition-all
                duration-500">

                    <!-- Glow -->

                    <div
                        class="absolute
                    -top-20
                    -right-20
                    w-72
                    h-72
                    rounded-full
                    bg-cyan-400/20
                    blur-[120px]">
                    </div>

                    <!-- Barra superior -->

                    <div
                        class="absolute
                    top-0
                    left-0
                    w-full
                    h-1
                    bg-gradient-to-r
                    from-cyan-400
                    via-blue-500
                    to-cyan-400">
                    </div>

                    <!-- Badge -->

                    <div
                        class="absolute
                    top-6
                    right-6
                    px-5
                    py-2
                    rounded-full
                    bg-white
                    text-blue-700
                    font-black
                    text-xs
                    shadow-xl">

                        ⭐ MÁS ELEGIDO

                    </div>

                    <div class="relative z-10 p-10">

                        <span
                            class="inline-flex items-center gap-2 rounded-full
                        bg-cyan-500/20
                        border
                        border-cyan-400/20
                        px-4
                        py-2
                        text-xs
                        font-bold
                        text-cyan-300
                        mb-7">

                            <i class="fa-solid fa-crown"></i>

                            PLAN PROFESIONAL

                        </span>

                        <!-- Titulo -->

                        <h3 class="text-3xl font-black">

                            Lleva tu empresa al siguiente nivel

                        </h3>

                        <p class="text-slate-300 mt-2">

                            Todo lo que necesitas para administrar
                            tu negocio sin límites.

                        </p>

                        <!-- Precio -->

                        <div class="my-10">

                            <div class="flex items-end gap-2">

                                <span class="text-3xl text-cyan-200">

                                    S/

                                </span>

                                <span class="text-7xl font-black leading-none">

                                    69

                                </span>

                                <span class="mb-2 text-cyan-200">

                                    /mes

                                </span>

                            </div>

                            <p class="mt-3 text-sm text-slate-300">

                                Incluye soporte y actualizaciones.

                            </p>

                        </div>

                        <!-- Características -->

                        <div class="space-y-5 mb-10">

                            <div class="flex items-center gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-white/10
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Todo lo del plan Básico

                            </div>

                            <div class="flex items-center gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-white/10
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Facturación electrónica SUNAT

                            </div>

                            <div class="flex items-center gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-white/10
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Boletas, Facturas y Cotizaciones

                            </div>

                            <div class="flex items-center gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-white/10
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Dashboard y reportes avanzados

                            </div>

                            <div class="flex items-center gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-white/10
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Actualizaciones gratuitas

                            </div>

                            <div class="flex items-center gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-white/10
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Copias de seguridad automáticas

                            </div>

                            <div class="flex items-center gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-white/10
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Soporte prioritario

                            </div>

                        </div>

                        <!-- Botón -->

                        <button
                            class="w-full
                        py-4
                        rounded-xl
                        font-black
                        bg-white
                        text-blue-700
                        hover:bg-slate-100
                        transition-all
                        shadow-2xl">

                            Comenzar ahora

                        </button>

                        <!-- Garantía -->

                        <div class="flex items-center justify-center gap-2 mt-6 text-sm text-slate-300">

                            <i class="fa-solid fa-shield-check text-cyan-300"></i>

                            Garantía de satisfacción.

                        </div>

                    </div>

                </div>

            </div>

            <!-- ===================================== -->
            <!-- FOOTER -->
            <!-- ===================================== -->

            <div class="mt-16">

                <div
                    class="rounded-3xl
                bg-white
                border
                border-slate-200
                shadow-lg
                p-8">

                    <div
                        class="grid
                    md:grid-cols-4
                    gap-8
                    text-center">

                        <div>

                            <i class="fa-solid fa-headset text-3xl text-blue-600 mb-4"></i>

                            <h4 class="font-bold text-slate-900">

                                Soporte incluido

                            </h4>

                            <p class="text-slate-500 text-sm mt-2">

                                Te ayudamos cuando lo necesites.

                            </p>

                        </div>

                        <div>

                            <i class="fa-solid fa-cloud text-3xl text-cyan-500 mb-4"></i>

                            <h4 class="font-bold text-slate-900">

                                100% en la nube

                            </h4>

                            <p class="text-slate-500 text-sm mt-2">

                                Accede desde cualquier lugar.

                            </p>

                        </div>

                        <div>

                            <i class="fa-solid fa-rotate text-3xl text-blue-600 mb-4"></i>

                            <h4 class="font-bold text-slate-900">

                                Actualizaciones

                            </h4>

                            <p class="text-slate-500 text-sm mt-2">

                                Siempre tendrás la última versión.

                            </p>

                        </div>

                        <div>

                            <i class="fa-solid fa-lock text-3xl text-cyan-500 mb-4"></i>

                            <h4 class="font-bold text-slate-900">

                                Datos seguros

                            </h4>

                            <p class="text-slate-500 text-sm mt-2">

                                Backups automáticos y protección.

                            </p>

                        </div>

                    </div>

                    <div
                        class="mt-10
                    flex
                    flex-wrap
                    justify-center
                    items-center
                    gap-6
                    text-slate-500
                    text-sm">

                        <div class="flex items-center gap-2">

                            <i class="fa-solid fa-circle-check text-green-500"></i>

                            Sin contratos

                        </div>

                        <div class="flex items-center gap-2">

                            <i class="fa-solid fa-circle-check text-green-500"></i>

                            Cancela cuando quieras

                        </div>

                        <div class="flex items-center gap-2">

                            <i class="fa-solid fa-circle-check text-green-500"></i>

                            14 días gratis

                        </div>

                        <div class="flex items-center gap-2">

                            <i class="fa-solid fa-circle-check text-green-500"></i>

                            Sin costos ocultos

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {{-- ========================================= --}}
    {{-- PLANES KAELTECH MOTO --}}
    {{-- ========================================= --}}
    <section id="planes-moto"
        class="relative py-28 overflow-hidden bg-gradient-to-b from-slate-50 via-white to-slate-100">

        <!-- ====================================== -->
        <!-- EFECTOS -->
        <!-- ====================================== -->

        <div class="absolute inset-0 overflow-hidden pointer-events-none">

            <div
                class="absolute
            -top-60
            -left-44
            w-[700px]
            h-[700px]
            rounded-full
            bg-blue-500/10
            blur-[180px]">
            </div>

            <div
                class="absolute
            -bottom-60
            -right-44
            w-[700px]
            h-[700px]
            rounded-full
            bg-cyan-400/10
            blur-[180px]">
            </div>

        </div>

        <div class="relative max-w-7xl mx-auto px-6">

            <!-- ================================================= -->
            <!-- HERO -->
            <!-- ================================================= -->

            <div
                class="relative overflow-hidden rounded-[38px]
            bg-gradient-to-br
            from-slate-950
            via-slate-900
            to-slate-950
            border
            border-slate-800
            shadow-2xl">

                <!-- Glow -->

                <div
                    class="absolute
                -top-32
                -left-20
                w-[450px]
                h-[450px]
                rounded-full
                bg-blue-500/20
                blur-[130px]">
                </div>

                <div
                    class="absolute
                -bottom-32
                -right-20
                w-[450px]
                h-[450px]
                rounded-full
                bg-cyan-400/15
                blur-[130px]">
                </div>

                <div
                    class="grid
                lg:grid-cols-[1fr_520px]
                gap-10
                items-center
                p-10
                lg:p-14">

                    <!-- ===================================== -->
                    <!-- TEXTO -->
                    <!-- ===================================== -->

                    <div class="relative z-10">

                        <span
                            class="inline-flex
                        items-center
                        gap-2
                        rounded-full
                        bg-cyan-500/10
                        border
                        border-cyan-400/20
                        px-5
                        py-2
                        text-cyan-300
                        text-sm
                        font-bold
                        mb-7">

                            <i class="fa-solid fa-motorcycle"></i>

                            SOFTWARE PARA TALLERES

                        </span>

                        <h2
                            class="text-5xl
                        lg:text-6xl
                        font-black
                        leading-tight
                        text-white">

                            Convierte tu taller

                            <br>

                            en una

                            <span
                                class="bg-gradient-to-r
                            from-cyan-300
                            to-blue-400
                            bg-clip-text
                            text-transparent">

                                empresa inteligente

                            </span>

                        </h2>

                        <p
                            class="mt-8
                        max-w-2xl
                        text-slate-300
                        text-xl
                        leading-9">

                            Gestiona órdenes de servicio,
                            clientes, historial,
                            mantenimientos, agenda,
                            técnicos y repuestos desde
                            una sola plataforma.

                        </p>

                        <!-- BENEFICIOS -->

                        <div
                            class="grid
                        sm:grid-cols-2
                        gap-5
                        mt-10">

                            <!-- ITEM -->

                            <div
                                class="rounded-2xl
                            bg-white/5
                            border
                            border-white/10
                            backdrop-blur-xl
                            p-5">

                                <div
                                    class="w-12
                                h-12
                                rounded-2xl
                                bg-cyan-500/10
                                flex
                                items-center
                                justify-center
                                mb-4">

                                    <i class="fa-solid fa-calendar-check text-cyan-300"></i>

                                </div>

                                <h3 class="font-bold text-white">

                                    Agenda Inteligente

                                </h3>

                                <p class="text-slate-400 mt-2 text-sm">

                                    Organiza citas y mantenimientos
                                    sin perder clientes.

                                </p>

                            </div>

                            <!-- ITEM -->

                            <div
                                class="rounded-2xl
                            bg-white/5
                            border
                            border-white/10
                            backdrop-blur-xl
                            p-5">

                                <div
                                    class="w-12
                                h-12
                                rounded-2xl
                                bg-blue-500/10
                                flex
                                items-center
                                justify-center
                                mb-4">

                                    <i class="fa-solid fa-chart-line text-blue-300"></i>

                                </div>

                                <h3 class="font-bold text-white">

                                    Más Rentabilidad

                                </h3>

                                <p class="text-slate-400 mt-2 text-sm">

                                    Controla ingresos,
                                    costos y productividad.

                                </p>

                            </div>

                            <!-- ITEM -->

                            <div
                                class="rounded-2xl
                            bg-white/5
                            border
                            border-white/10
                            backdrop-blur-xl
                            p-5">

                                <div
                                    class="w-12
                                h-12
                                rounded-2xl
                                bg-cyan-500/10
                                flex
                                items-center
                                justify-center
                                mb-4">

                                    <i class="fa-solid fa-clock-rotate-left text-cyan-300"></i>

                                </div>

                                <h3 class="font-bold text-white">

                                    Historial Completo

                                </h3>

                                <p class="text-slate-400 mt-2 text-sm">

                                    Toda la información
                                    de cada motocicleta.

                                </p>

                            </div>

                            <!-- ITEM -->

                            <div
                                class="rounded-2xl
                            bg-white/5
                            border
                            border-white/10
                            backdrop-blur-xl
                            p-5">

                                <div
                                    class="w-12
                                h-12
                                rounded-2xl
                                bg-blue-500/10
                                flex
                                items-center
                                justify-center
                                mb-4">

                                    <i class="fa-solid fa-shield-halved text-blue-300"></i>

                                </div>

                                <h3 class="font-bold text-white">

                                    Información Segura

                                </h3>

                                <p class="text-slate-400 mt-2 text-sm">

                                    Tus datos protegidos
                                    en la nube.

                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- ===================================== -->
                    <!-- DASHBOARD -->
                    <!-- ===================================== -->

                    <div class="relative flex justify-center">

                        <!-- Halo -->

                        <div
                            class="absolute
                        w-[420px]
                        h-[420px]
                        rounded-full
                        bg-blue-500/20
                        blur-[120px]">
                        </div>

                        <!-- Dashboard -->

                        <img src="{{ asset('/images/web/moto-dashboard.png') }}"
                            class="relative
                        z-20
                        w-full
                        max-w-[500px]
                        rounded-3xl
                        shadow-[0_40px_100px_rgba(0,0,0,.55)]
                        border
                        border-white/10">

                        <!-- Celular -->

                        <img src="{{ asset('/images/web/moto-phone.png') }}"
                            class="absolute
                        -bottom-8
                        right-0
                        w-[220px]
                        z-30
                        drop-shadow-[0_35px_80px_rgba(0,0,0,.7)]
                        animate-float">

                    </div>

                </div>

            </div>

            <!-- ===================================== -->
            <!-- PLANES -->
            <!-- ===================================== -->

            <div class="grid xl:grid-cols-3 gap-8 mt-16">
                <!-- ================================================= -->
                <!-- STARTER -->
                <!-- ================================================= -->

                <div
                    class="group relative overflow-hidden rounded-[34px]
                border border-slate-200
                bg-white
                shadow-xl
                hover:-translate-y-2
                hover:shadow-2xl
                transition-all
                duration-500">

                    <!-- Barra superior -->

                    <div
                        class="absolute
                    top-0
                    left-0
                    w-full
                    h-1
                    bg-gradient-to-r
                    from-cyan-500
                    via-blue-500
                    to-cyan-500">
                    </div>

                    <div class="p-8 h-full flex flex-col">

                        <!-- Badge -->

                        <div class="flex items-center justify-between mb-7">

                            <span
                                class="inline-flex
                            items-center
                            gap-2
                            px-4
                            py-2
                            rounded-full
                            bg-cyan-50
                            text-cyan-700
                            font-bold
                            text-xs">

                                <i class="fa-solid fa-rocket"></i>

                                PLAN STARTER

                            </span>

                            <span
                                class="px-4
                            py-2
                            rounded-full
                            bg-slate-100
                            text-slate-700
                            font-bold
                            text-xs">

                                IDEAL PARA EMPEZAR

                            </span>

                        </div>

                        <!-- Título -->

                        <h3 class="text-3xl
                        font-black
                        text-slate-900">

                            Tu primer paso hacia

                            <span
                                class="bg-gradient-to-r
                            from-blue-600
                            to-cyan-500
                            bg-clip-text
                            text-transparent">

                                un taller digital

                            </span>

                        </h3>

                        <p class="text-slate-500
                        mt-3
                        leading-7">

                            Pensado para talleres pequeños
                            que desean organizar sus procesos
                            desde el primer día.

                        </p>

                        <!-- Precio -->

                        <div
                            class="rounded-3xl
                        bg-gradient-to-r
                        from-blue-600
                        to-cyan-500
                        text-white
                        mt-8
                        p-7
                        text-center
                        shadow-xl">

                            <div
                                class="flex
                            items-end
                            justify-center
                            gap-2">

                                <span class="text-2xl">

                                    S/

                                </span>

                                <span
                                    class="text-6xl
                                font-black
                                leading-none">

                                    99

                                </span>

                                <span class="text-cyan-100
                                mb-2">

                                    -149

                                </span>

                            </div>

                            <p
                                class="uppercase
                            tracking-[4px]
                            text-cyan-100
                            text-sm
                            mt-3">

                                POR MES

                            </p>

                        </div>

                        <!-- Características -->

                        <div class="space-y-5 mt-8">

                            <div class="flex gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-blue-50
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-blue-600"></i>

                                </div>

                                Agenda semanal inteligente

                            </div>

                            <div class="flex gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-blue-50
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-blue-600"></i>

                                </div>

                                Reserva de citas

                            </div>

                            <div class="flex gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-blue-50
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-blue-600"></i>

                                </div>

                                Gestión de clientes

                            </div>

                            <div class="flex gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-blue-50
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-blue-600"></i>

                                </div>

                                Historial básico

                            </div>

                            <div class="flex gap-4">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-blue-50
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-blue-600"></i>

                                </div>

                                Órdenes de servicio

                            </div>

                        </div>

                        <!-- Caja inferior -->

                        <div
                            class="mt-8
                        rounded-2xl
                        bg-slate-50
                        border
                        border-slate-200
                        p-5">

                            <h4
                                class="font-bold
                            text-slate-900
                            mb-4">

                                Incluye

                            </h4>

                            <div class="space-y-3 text-sm">

                                <div class="flex justify-between">

                                    <span class="text-slate-500">

                                        Sucursales

                                    </span>

                                    <span class="font-semibold">

                                        1

                                    </span>

                                </div>

                                <div class="flex justify-between">

                                    <span class="text-slate-500">

                                        Usuarios

                                    </span>

                                    <span class="font-semibold">

                                        Limitados

                                    </span>

                                </div>

                                <div class="flex justify-between">

                                    <span class="text-slate-500">

                                        Personalización

                                    </span>

                                    <span class="text-red-500 font-semibold">

                                        No

                                    </span>

                                </div>

                            </div>

                        </div>

                        <!-- Botón -->

                        <div class="mt-8">

                            <button
                                class="w-full
                            py-4
                            rounded-xl
                            bg-gradient-to-r
                            from-blue-600
                            to-cyan-500
                            text-white
                            font-bold
                            hover:from-blue-700
                            hover:to-cyan-600
                            shadow-xl
                            transition">

                                Solicitar información

                            </button>

                        </div>

                    </div>

                </div>

                <!-- ================================================= -->
                <!-- PLAN PROFESIONAL -->
                <!-- ================================================= -->

                <div
                    class="group
                relative
                overflow-hidden
                rounded-[36px]
                bg-gradient-to-br
                from-slate-950
                via-blue-950
                to-slate-900
                border
                border-blue-500/20
                shadow-[0_30px_80px_rgba(37,99,235,.25)]
                hover:-translate-y-3
                hover:shadow-[0_40px_100px_rgba(37,99,235,.40)]
                transition-all
                duration-500">

                    <!-- Glow -->

                    <div
                        class="absolute
                    -top-24
                    -right-16
                    w-80
                    h-80
                    rounded-full
                    bg-cyan-400/20
                    blur-[120px]">
                    </div>

                    <!-- Barra superior -->

                    <div
                        class="absolute
                    top-0
                    left-0
                    w-full
                    h-1
                    bg-gradient-to-r
                    from-cyan-400
                    via-blue-500
                    to-cyan-400">
                    </div>

                    <!-- Badge -->

                    <div
                        class="absolute
                    top-6
                    right-6
                    px-5
                    py-2
                    rounded-full
                    bg-white
                    text-blue-700
                    text-xs
                    font-black
                    shadow-xl">

                        ⭐ MÁS ELEGIDO

                    </div>

                    <div class="relative z-10 p-8 h-full flex flex-col">

                        <!-- Badge -->

                        <span
                            class="inline-flex
                        items-center
                        gap-2
                        px-4
                        py-2
                        rounded-full
                        bg-cyan-500/15
                        border
                        border-cyan-400/20
                        text-cyan-300
                        text-xs
                        font-bold
                        mb-7
                        w-fit">

                            <i class="fa-solid fa-crown"></i>

                            PLAN PROFESIONAL

                        </span>

                        <!-- Titulo -->

                        <h3
                            class="text-4xl
                        font-black
                        leading-tight
                        text-white">

                            Haz crecer

                            <br>

                            tu taller

                        </h3>

                        <p class="text-slate-300
                        mt-4
                        leading-8">

                            Para talleres que desean automatizar
                            completamente su operación y brindar
                            una experiencia profesional a sus clientes.

                        </p>

                        <!-- Precio -->

                        <div
                            class="rounded-3xl
                        bg-white/10
                        backdrop-blur-xl
                        border
                        border-white/10
                        mt-8
                        p-7
                        text-center">

                            <div
                                class="flex
                            items-end
                            justify-center
                            gap-2">

                                <span class="text-2xl
                                text-cyan-200">

                                    S/

                                </span>

                                <span
                                    class="text-6xl
                                font-black
                                leading-none
                                text-white">

                                    249

                                </span>

                                <span class="text-cyan-200
                                mb-2">

                                    -399

                                </span>

                            </div>

                            <p
                                class="uppercase
                            tracking-[4px]
                            text-cyan-200
                            text-sm
                            mt-3">

                                POR MES

                            </p>

                        </div>

                        <!-- Características -->

                        <div class="space-y-5 mt-8">

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-cyan-500/20
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Todo lo del Plan Starter

                            </div>

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-cyan-500/20
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Página web profesional

                            </div>

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-cyan-500/20
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Historial completo por placa

                            </div>

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-cyan-500/20
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Dashboard ejecutivo

                            </div>

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-cyan-500/20
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Reportes profesionales

                            </div>

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-cyan-500/20
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-cyan-300"></i>

                                </div>

                                Integración con WhatsApp

                            </div>

                        </div>

                        <!-- Extras -->

                        <div
                            class="mt-8
                        rounded-2xl
                        bg-white/5
                        border
                        border-white/10
                        p-5">

                            <h4
                                class="font-bold
                            text-white
                            mb-4">

                                También incluye

                            </h4>

                            <div class="space-y-3 text-sm text-slate-300">

                                <div class="flex justify-between">

                                    <span>Usuarios</span>

                                    <span class="text-cyan-300 font-semibold">

                                        Ilimitados

                                    </span>

                                </div>

                                <div class="flex justify-between">

                                    <span>Sucursales</span>

                                    <span class="text-cyan-300 font-semibold">

                                        Hasta 5

                                    </span>

                                </div>

                                <div class="flex justify-between">

                                    <span>Backups</span>

                                    <span class="text-cyan-300 font-semibold">

                                        Automáticos

                                    </span>

                                </div>

                                <div class="flex justify-between">

                                    <span>Soporte</span>

                                    <span class="text-cyan-300 font-semibold">

                                        Prioritario

                                    </span>

                                </div>

                            </div>

                        </div>

                        <!-- Botón -->

                        <div class="mt-8">

                            <button
                                class="w-full
                            py-4
                            rounded-xl
                            font-black
                            text-blue-700
                            bg-white
                            hover:bg-slate-100
                            transition
                            shadow-xl">

                                Agendar demostración

                            </button>

                        </div>

                    </div>

                </div>
                <!-- ================================================= -->
                <!-- ENTERPRISE -->
                <!-- ================================================= -->

                <div
                    class="group
                relative
                overflow-hidden
                rounded-[36px]
                bg-gradient-to-br
                from-[#111827]
                via-[#0f172a]
                to-black
                border
                border-yellow-500/20
                shadow-[0_30px_80px_rgba(0,0,0,.35)]
                hover:-translate-y-3
                hover:shadow-[0_35px_90px_rgba(234,179,8,.20)]
                transition-all
                duration-500">

                    <!-- Glow -->

                    <div
                        class="absolute
                    -top-24
                    -right-20
                    w-80
                    h-80
                    rounded-full
                    bg-yellow-400/10
                    blur-[120px]">
                    </div>

                    <!-- Barra -->

                    <div
                        class="absolute
                    top-0
                    left-0
                    w-full
                    h-1
                    bg-gradient-to-r
                    from-yellow-400
                    via-amber-500
                    to-yellow-400">
                    </div>

                    <div class="p-8 h-full flex flex-col">

                        <!-- Badge -->

                        <span
                            class="inline-flex
                        items-center
                        gap-2
                        px-4
                        py-2
                        rounded-full
                        bg-yellow-500/10
                        border
                        border-yellow-500/20
                        text-yellow-300
                        text-xs
                        font-bold
                        w-fit
                        mb-7">

                            <i class="fa-solid fa-gem"></i>

                            ENTERPRISE

                        </span>

                        <!-- Titulo -->

                        <h3
                            class="text-4xl
                        font-black
                        text-white
                        leading-tight">

                            Solución

                            <br>

                            personalizada

                        </h3>

                        <p class="text-slate-300
                        mt-4
                        leading-8">

                            Diseñado para empresas que
                            necesitan procesos únicos,
                            integraciones y una plataforma
                            desarrollada a su medida.

                        </p>

                        <!-- Precio -->

                        <div
                            class="rounded-3xl
                        bg-gradient-to-r
                        from-yellow-500
                        to-amber-500
                        text-slate-900
                        mt-8
                        p-7
                        text-center
                        shadow-xl">

                            <div
                                class="flex
                            items-end
                            justify-center
                            gap-2">

                                <span class="text-2xl font-bold">

                                    S/

                                </span>

                                <span
                                    class="text-6xl
                                font-black
                                leading-none">

                                    3,500

                                </span>

                            </div>

                            <p
                                class="uppercase
                            tracking-[4px]
                            text-sm
                            mt-3
                            font-bold">

                                PAGO ÚNICO

                            </p>

                        </div>

                        <!-- Características -->

                        <div class="space-y-5 mt-8">

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-yellow-500/15
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-yellow-300"></i>

                                </div>

                                Sistema totalmente personalizado

                            </div>

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-yellow-500/15
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-yellow-300"></i>

                                </div>

                                Marca y dominio propio

                            </div>

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-yellow-500/15
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-yellow-300"></i>

                                </div>

                                Desarrollo de nuevas funciones

                            </div>

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-yellow-500/15
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-yellow-300"></i>

                                </div>

                                Capacitación completa

                            </div>

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-yellow-500/15
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-yellow-300"></i>

                                </div>

                                Integraciones mediante API

                            </div>

                            <div class="flex gap-4 text-white">

                                <div
                                    class="w-8
                                h-8
                                rounded-full
                                bg-yellow-500/15
                                flex
                                items-center
                                justify-center">

                                    <i class="fa-solid fa-check text-yellow-300"></i>

                                </div>

                                Soporte Premium

                            </div>

                        </div>

                        <!-- Caja -->

                        <div
                            class="mt-8
                        rounded-2xl
                        bg-white/5
                        border
                        border-white/10
                        p-5">

                            <h4
                                class="font-bold
                            text-yellow-300
                            mb-4">

                                Beneficios exclusivos

                            </h4>

                            <div class="space-y-3 text-sm text-slate-300">

                                <div class="flex justify-between">

                                    <span>Usuarios</span>

                                    <span class="text-yellow-300">

                                        Ilimitados

                                    </span>

                                </div>

                                <div class="flex justify-between">

                                    <span>Sucursales</span>

                                    <span class="text-yellow-300">

                                        Ilimitadas

                                    </span>

                                </div>

                                <div class="flex justify-between">

                                    <span>Hosting</span>

                                    <span class="text-yellow-300">

                                        Incluido

                                    </span>

                                </div>

                                <div class="flex justify-between">

                                    <span>Implementación</span>

                                    <span class="text-yellow-300">

                                        Completa

                                    </span>

                                </div>

                            </div>

                        </div>

                        <!-- Botón -->

                        <div class="mt-8">

                            <button
                                class="w-full
                            py-4
                            rounded-xl
                            bg-gradient-to-r
                            from-yellow-500
                            to-amber-500
                            text-slate-900
                            font-black
                            hover:from-yellow-400
                            hover:to-amber-400
                            shadow-xl
                            transition">

                                Solicitar cotización

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
    <!-- FOOTER -->
    <footer id="contacto"
        class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white">

        <!-- ========================================== -->
        <!-- EFECTOS -->
        <!-- ========================================== -->

        <div class="absolute inset-0 overflow-hidden pointer-events-none">

            <div
                class="absolute
            -top-52
            -left-44
            w-[650px]
            h-[650px]
            rounded-full
            bg-blue-500/10
            blur-[180px]">
            </div>

            <div
                class="absolute
            -bottom-52
            -right-44
            w-[650px]
            h-[650px]
            rounded-full
            bg-cyan-400/10
            blur-[180px]">
            </div>

        </div>

        <div class="relative">

            <!-- ========================================== -->
            <!-- CTA -->
            <!-- ========================================== -->

            <section class="max-w-7xl mx-auto px-6 pt-20">

                <div
                    class="relative overflow-hidden rounded-[36px]
                bg-gradient-to-r
                from-blue-600
                via-cyan-500
                to-blue-700
                px-10
                lg:px-16
                py-14
                shadow-[0_40px_100px_rgba(37,99,235,.35)]">

                    <!-- Glow -->

                    <div
                        class="absolute
                    -top-20
                    -right-10
                    w-72
                    h-72
                    rounded-full
                    bg-white/20
                    blur-[120px]">
                    </div>

                    <div
                        class="relative
                    grid
                    lg:grid-cols-[1fr_auto]
                    gap-10
                    items-center">

                        <!-- Texto -->

                        <div>

                            <span
                                class="inline-flex
                            items-center
                            gap-2
                            rounded-full
                            bg-white/15
                            px-5
                            py-2
                            text-sm
                            font-semibold
                            backdrop-blur-lg
                            mb-6">

                                🚀 Comienza hoy mismo

                            </span>

                            <h2 class="text-4xl lg:text-5xl font-black leading-tight">

                                Digitaliza tu empresa

                                <br>

                                en menos de

                                <span class="text-yellow-300">

                                    5 minutos

                                </span>

                            </h2>

                            <p
                                class="mt-6
                            max-w-2xl
                            text-blue-100
                            text-lg
                            leading-8">

                                Únete a cientos de empresas que ya administran
                                sus ventas, inventario y facturación desde
                                Kael Tech.

                            </p>

                        </div>

                        <!-- Botones -->

                        <div
                            class="flex
                        flex-col
                        sm:flex-row
                        gap-4">

                            <a href="#planes"
                                class="px-8
                            py-4
                            rounded-xl
                            bg-white
                            text-blue-700
                            font-bold
                            text-center
                            hover:bg-slate-100
                            transition">

                                Crear mi empresa

                            </a>

                            <a href="#demos"
                                class="px-8
                            py-4
                            rounded-xl
                            border
                            border-white/20
                            bg-white/10
                            backdrop-blur-lg
                            font-bold
                            text-center
                            hover:bg-white/20
                            transition">

                                Ver demostración

                            </a>

                        </div>

                    </div>

                </div>

            </section>

            <!-- ========================================== -->
            <!-- FOOTER -->
            <!-- ========================================== -->

            <section class="max-w-7xl mx-auto px-6 py-20">

                <div class="grid lg:grid-cols-[1.5fr_1fr_1fr_1fr] gap-12">

                    <!-- ================================= -->
                    <!-- LOGO -->
                    <!-- ================================= -->

                    <div>

                        <div class="flex items-center gap-4 mb-6">

                            <div
                                class="w-16
                            h-16
                            rounded-3xl
                            bg-gradient-to-br
                            from-blue-600
                            to-cyan-500
                            p-[2px]
                            shadow-xl">

                                <div
                                    class="w-full
                                h-full
                                rounded-3xl
                                bg-white
                                flex
                                items-center
                                justify-center">

                                    <img src="{{ asset('images/icono.jpg') }}"
                                        class="w-12 h-12 object-contain rounded-xl">

                                </div>

                            </div>

                            <div>

                                <h3 class="text-3xl font-black">

                                    Kael Tech

                                </h3>

                                <p class="text-slate-400">

                                    Business Cloud Platform

                                </p>

                            </div>

                        </div>

                        <p
                            class="text-slate-400
                        leading-8
                        max-w-md
                        mb-8">

                            Plataforma SaaS diseñada para automatizar la gestión
                            de negocios modernos, con tecnología en la nube,
                            seguridad y crecimiento sin límites.

                        </p>

                        <!-- Redes -->

                        <div class="flex gap-4">

                            <a href="#"
                                class="w-12 h-12 rounded-xl
                            bg-white/5
                            border
                            border-white/10
                            flex
                            items-center
                            justify-center
                            hover:bg-blue-600
                            hover:border-blue-500
                            transition">

                                <i class="fab fa-facebook-f"></i>

                            </a>

                            <a href="#"
                                class="w-12 h-12 rounded-xl
                            bg-white/5
                            border
                            border-white/10
                            flex
                            items-center
                            justify-center
                            hover:bg-pink-600
                            transition">

                                <i class="fab fa-instagram"></i>

                            </a>

                            <a href="#"
                                class="w-12 h-12 rounded-xl
                            bg-white/5
                            border
                            border-white/10
                            flex
                            items-center
                            justify-center
                            hover:bg-sky-500
                            transition">

                                <i class="fab fa-tiktok"></i>

                            </a>

                            <a href="#"
                                class="w-12 h-12 rounded-xl
                            bg-white/5
                            border
                            border-white/10
                            flex
                            items-center
                            justify-center
                            hover:bg-red-600
                            transition">

                                <i class="fab fa-youtube"></i>

                            </a>

                        </div>

                    </div>

                    <!-- ================================= -->
                    <!-- SOLUCIONES -->
                    <!-- ================================= -->

                    <div>

                        <h3 class="text-xl font-bold mb-6">

                            Soluciones

                        </h3>

                        <ul class="space-y-4">

                            <li>
                                <a href="#" class="text-slate-400 hover:text-cyan-400 transition">
                                    Sistema POS
                                </a>
                            </li>

                            <li>
                                <a href="#" class="text-slate-400 hover:text-cyan-400 transition">
                                    Talleres de Motos
                                </a>
                            </li>

                            <li>
                                <a href="#" class="text-slate-400 hover:text-cyan-400 transition">
                                    Restaurantes
                                </a>
                            </li>

                            <li>
                                <a href="#" class="text-slate-400 hover:text-cyan-400 transition">
                                    Ferreterías
                                </a>
                            </li>

                            <li>
                                <a href="#" class="text-slate-400 hover:text-cyan-400 transition">
                                    Ópticas
                                </a>
                            </li>

                        </ul>

                    </div>

                    <!-- ================================= -->
                    <!-- EMPRESA -->
                    <!-- ================================= -->

                    <div>

                        <h3 class="text-xl font-bold mb-6">

                            Empresa

                        </h3>

                        <ul class="space-y-4">

                            <li>
                                <a href="#inicio" class="text-slate-400 hover:text-cyan-400 transition">
                                    Inicio
                                </a>
                            </li>

                            <li>
                                <a href="#soluciones" class="text-slate-400 hover:text-cyan-400 transition">
                                    Soluciones
                                </a>
                            </li>

                            <li>
                                <a href="#planes-pos" class="text-slate-400 hover:text-cyan-400 transition">
                                    Planes
                                </a>
                            </li>

                            <li>
                                <a href="#clientes" class="text-slate-400 hover:text-cyan-400 transition">
                                    Clientes
                                </a>
                            </li>

                            <li>
                                <a href="#contacto" class="text-slate-400 hover:text-cyan-400 transition">
                                    Contacto
                                </a>
                            </li>

                        </ul>

                    </div>

                    <!-- ================================= -->
                    <!-- CONTACTO -->
                    <!-- ================================= -->

                    <div>

                        <h3 class="text-xl font-bold mb-6">

                            Contáctanos

                        </h3>

                        <div class="space-y-5">

                            <div class="flex items-start gap-4">

                                <div
                                    class="w-11 h-11 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-phone text-cyan-400"></i>

                                </div>

                                <div>

                                    <p class="font-semibold">

                                        Teléfono

                                    </p>

                                    <p class="text-slate-400">

                                        +51 987 654 321

                                    </p>

                                </div>

                            </div>

                            <div class="flex items-start gap-4">

                                <div
                                    class="w-11 h-11 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-envelope text-cyan-400"></i>

                                </div>

                                <div>

                                    <p class="font-semibold">

                                        Correo

                                    </p>

                                    <p class="text-slate-400">

                                        hola@kael.pe

                                    </p>

                                </div>

                            </div>

                            <div class="flex items-start gap-4">

                                <div
                                    class="w-11 h-11 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0">

                                    <i class="fa-solid fa-location-dot text-cyan-400"></i>

                                </div>

                                <div>

                                    <p class="font-semibold">

                                        Ubicación

                                    </p>

                                    <p class="text-slate-400">

                                        Lima - Perú

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- ========================================== -->
                <!-- NEWSLETTER -->
                <!-- ========================================== -->

                <div
                    class="mt-20 rounded-[30px]
                border border-white/10
                bg-white/5
                backdrop-blur-xl
                p-10">

                    <div class="grid lg:grid-cols-[1fr_auto] gap-8 items-center">

                        <div>

                            <h3 class="text-3xl font-black mb-3">

                                Mantente informado

                            </h3>

                            <p class="text-slate-400 leading-8 max-w-2xl">

                                Recibe novedades, nuevas funcionalidades,
                                promociones y contenido exclusivo sobre Kael Tech.

                            </p>

                        </div>

                        <form class="flex flex-col sm:flex-row gap-4">

                            <input type="email" placeholder="Tu correo electrónico"
                                class="w-full sm:w-[320px]
                            px-5
                            py-4
                            rounded-xl
                            bg-slate-900/70
                            border
                            border-white/10
                            focus:border-cyan-400
                            outline-none
                            text-white
                            placeholder:text-slate-500">

                            <button
                                class="px-8
                            py-4
                            rounded-xl
                            bg-gradient-to-r
                            from-blue-600
                            to-cyan-500
                            hover:from-blue-700
                            hover:to-cyan-600
                            font-bold
                            shadow-xl
                            transition">

                                Suscribirme

                            </button>

                        </form>

                    </div>

                </div>

                <!-- ========================================== -->
                <!-- COPYRIGHT -->
                <!-- ========================================== -->

                <div class="border-t border-white/10 mt-16 pt-8">

                    <div class="flex flex-col lg:flex-row justify-between items-center gap-6">

                        <p class="text-slate-500 text-sm">

                            © 2026 <span class="text-white font-semibold">Kael Tech</span>.
                            Todos los derechos reservados.

                        </p>

                        <div class="flex flex-wrap justify-center gap-6 text-sm">

                            <a href="#" class="text-slate-500 hover:text-cyan-400 transition">
                                Política de privacidad
                            </a>

                            <a href="#" class="text-slate-500 hover:text-cyan-400 transition">
                                Términos y condiciones
                            </a>

                            <a href="#" class="text-slate-500 hover:text-cyan-400 transition">
                                Soporte
                            </a>

                        </div>

                        <p class="text-slate-500 text-sm">

                            Desarrollado con

                            <span class="text-red-500">❤</span>

                            por <span class="font-semibold text-white">Kael Tech</span>

                        </p>

                    </div>

                </div>

            </section>

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
