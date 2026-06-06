<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAEL - Mantenimiento de Motocicletas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background-color: #ffffff;
        }
        /* Reflejo difuminado azul/púrpura del fondo izquierdo */
        .ambient-glow {
            position: absolute;
            top: 0;
            left: -10%;
            width: 50%;
            height: 100%;
            background: radial-gradient(circle at 30% 40%, rgba(219, 234, 254, 0.6) 0%, rgba(243, 232, 255, 0.4) 40%, transparent 70%);
            z-index: -10;
            pointer-events: none;
        }
    </style>
</head>
<body class="text-slate-600 font-sans antialiased relative overflow-x-hidden">

    <div class="ambient-glow"></div>

    <header class="bg-white/70 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-black tracking-wider text-slate-900 flex items-center">
                    <span class="text-blue-600 font-extrabold mr-0.5">K</span>KAEL
                </span>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
                <a href="#" class="text-blue-600 border-b-2 border-blue-600 pb-1">Inicio</a>
                <a href="#" class="text-slate-600 hover:text-blue-600 transition">Servicios</a>
                <a href="#" class="text-slate-600 hover:text-blue-600 transition">Reservar</a>
                <a href="#" class="text-slate-600 hover:text-blue-600 transition">Historial</a>
                <a href="#" class="text-slate-600 hover:text-blue-600 transition">Catálogo</a>
                <a href="#" class="text-slate-600 hover:text-blue-600 transition">Nosotros</a>
                <a href="#" class="text-slate-600 hover:text-blue-600 transition">Contacto</a>
            </nav>

            <div class="flex items-center gap-5">
                <a href="#" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-4 h-4"></i> Acceso Administrador
                </a>
                <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 transition shadow-md shadow-indigo-600/20">
                    <i data-lucide="calendar" class="w-4 h-4"></i> Reserva tu cita
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-6 space-y-6">
                <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-full text-xs font-bold text-blue-600">
                    <i data-lucide="bike" class="w-3.5 h-3.5"></i> Taller especializado en motocicletas
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-[54px] font-black tracking-tight text-slate-900 leading-[1.15]">
                    Mantenimiento profesional<br>
                    para tu moto <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">
                        con tecnología inteligente
                    </span>
                </h1>

                <p class="text-slate-500 text-base md:text-lg max-w-xl leading-relaxed">
                    Reserva mantenimientos online, consulta tu historial por placa y realiza seguimiento a tus reparaciones en tiempo real.
                </p>

                <div class="grid grid-cols-3 gap-4 text-xs font-semibold text-slate-700 max-w-xl pt-2">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-xl"><i data-lucide="check-circle" class="w-4 h-4"></i></div>
                        <span>Técnicos certificados</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-xl"><i data-lucide="layers" class="w-4 h-4"></i></div>
                        <span>Repuestos originales</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-xl"><i data-lucide="shield" class="w-4 h-4"></i></div>
                        <span>Garantía de servicio</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-xl font-bold flex items-center gap-2 transition shadow-lg shadow-blue-600/20">
                        <i data-lucide="calendar" class="w-5 h-5"></i> Reservar Ahora
                    </a>
                    <a href="#buscar-placa" class="border-2 border-blue-600 text-blue-600 hover:bg-blue-50 px-8 py-3.5 rounded-xl font-bold flex items-center gap-2 transition">
                        <i data-lucide="search" class="w-5 h-5"></i> Consultar Historial
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-6 max-w-md border-t border-slate-100">
                    <div>
                        <div class="text-3xl font-black text-slate-900">+5000</div>
                        <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Motos Atendidas</div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-slate-900">+2000</div>
                        <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Clientes</div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-slate-900 flex items-center gap-1">4.9 <span class="text-indigo-600 text-xl">★</span></div>
                        <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Valoración</div>
                    </div>
                </div>

                <div class="pt-4 space-y-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Trabajamos con las mejores marcas</p>
                    <div class="flex flex-wrap items-center gap-6 opacity-35 grayscale contrast-125">
                        <span class="text-slate-900 font-black tracking-tighter text-base">YAMAHA</span>
                        <span class="text-slate-900 font-black tracking-tighter text-base">HONDA</span>
                        <span class="text-slate-900 font-black tracking-tighter text-base">BAJAJ</span>
                        <span class="text-slate-900 font-black tracking-tighter text-base">KTM</span>
                        <span class="text-slate-900 font-black tracking-tighter text-base">PULSAR</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 relative flex justify-center items-center pt-10 lg:pt-0">
                <div class="absolute right-[-10%] top-[-10%] w-[110%] h-[120%] bg-gradient-to-bl from-slate-950 via-slate-900 to-blue-950 rounded-[40px] -z-20 overflow-hidden hidden lg:block shadow-2xl">
                    <div class="absolute top-12 right-12 w-40 h-1 bg-cyan-400/40 blur-sm rotate-12"></div>
                    <div class="absolute top-24 right-20 w-48 h-1 bg-blue-500/30 blur-md rotate-12"></div>
                </div>
                
                <img src="{{ asset_root('landing_tallermoto/images/hero-moto.png') }}" alt="Kael Moto Deportiva" class="w-full max-w-lg lg:max-w-none object-contain drop-shadow-2xl z-10">

                <div class="absolute bottom-6 left-2 sm:left-6 bg-white/90 backdrop-blur-xl border border-white/60 p-5 rounded-2xl w-64 shadow-[0_20px_50px_rgba(0,0,0,0.1)] z-20 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex items-center gap-1.5 text-xs font-extrabold text-slate-900 tracking-wide">
                            <i data-lucide="pulse" class="w-4 h-4 text-blue-600"></i> Sistema Kael
                        </div>
                        <span class="flex items-center gap-1 text-[10px] bg-green-50 text-green-600 px-2.5 py-0.5 rounded-full font-bold border border-green-200">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> En tiempo real
                        </span>
                    </div>
                    <div class="space-y-3.5 text-xs font-semibold text-slate-600">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i> Reservas Hoy</span>
                            <span class="font-bold text-slate-900 text-sm">24</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 flex items-center gap-2"><i data-lucide="wrench" class="w-4 h-4 text-blue-500"></i> Mantenimientos</span>
                            <span class="font-bold text-slate-900 text-sm">182</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 flex items-center gap-2"><i data-lucide="users" class="w-4 h-4 text-blue-500"></i> Clientes Activos</span>
                            <span class="font-bold text-slate-900 text-sm">1,247</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                            <span class="text-slate-400 flex items-center gap-2"><i data-lucide="smile" class="w-4 h-4 text-green-500"></i> Satisfacción</span>
                            <span class="font-black text-green-600 text-sm">98%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="buscar-placa" class="mt-20 bg-white border border-slate-100 rounded-3xl p-6 md:p-8 shadow-[0_15px_40px_rgba(0,0,0,0.03)] grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            <div class="lg:col-span-4 flex items-center gap-4">
                <div class="p-3.5 bg-blue-50 text-blue-600 rounded-2xl shrink-0">
                    <i data-lucide="search-code" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Consulta tu historial por placa</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Revisa todos los mantenimientos anteriores, próximos servicios y más.</p>
                </div>
            </div>
            
            <form action="#" method="GET" class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-12 gap-4 w-full">
                <div class="sm:col-span-9 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="bike" class="w-5 h-5"></i>
                    </div>
                    <input type="text" name="placa" placeholder="Ej: ABC123, BCD456" 
                        class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition font-mono uppercase tracking-wider text-sm">
                </div>
                <button type="submit" class="sm:col-span-3 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl transition flex items-center justify-center gap-2 text-sm shadow-md shadow-blue-600/10">
                    <i data-lucide="search" class="w-4 h-4"></i> Buscar
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 mt-16 pt-10 border-t border-slate-100">
            <div class="flex gap-3.5">
                <i data-lucide="calendar-days" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-slate-900">Reserva Online</h4>
                    <p class="text-[11px] text-slate-400 mt-1 leading-normal">Agenda tu cita en menos de 1 minuto.</p>
                </div>
            </div>
            <div class="flex gap-3.5">
                <i data-lucide="clipboard-list" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-slate-900">Historial Digital</h4>
                    <p class="text-[11px] text-slate-400 mt-1 leading-normal">Consulta todo el historial de tu moto.</p>
                </div>
            </div>
            <div class="flex gap-3.5">
                <i data-lucide="map-pin" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-slate-900">Seguimiento en tiempo real</h4>
                    <p class="text-[11px] text-slate-400 mt-1 leading-normal">Sigue el estado de tu reparación paso a paso.</p>
                </div>
            </div>
            <div class="flex gap-3.5">
                <i data-lucide="package" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-slate-900">Catálogo de Repuestos</h4>
                    <p class="text-[11px] text-slate-400 mt-1 leading-normal">Repuestos originales con la mejor calidad garantizada.</p>
                </div>
            </div>
            <div class="flex gap-3.5">
                <i data-lucide="shield-check" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-slate-900">Garantía Total</h4>
                    <p class="text-[11px] text-slate-400 mt-1 leading-normal">Todos nuestros servicios incluyen garantía.</p>
                </div>
            </div>
        </div>

    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>