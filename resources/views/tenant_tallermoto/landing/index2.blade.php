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
<body class="text-gray-300 font-sans antialiased selection:bg-brand-600 selection:text-white min-h-screen flex flex-col justify-between relative bg-no-repeat bg-cover bg-center bg-fixed" 
      style="background-image: url('{{ asset_root('landing_tallermoto/images/hero-moto.png') }}');">
      <header class="w-full bg-slate-950/40 backdrop-blur-md border-b border-white/5 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-black tracking-wider {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} flex items-center">
                    <span class="text-brand-500 font-extrabold mr-0.5">K</span>KAEL
                </span>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-xs font-semibold uppercase tracking-widest">
                <a href="#" class="text-brand-500 border-b-2 border-brand-500 pb-1">Inicio</a>
                <a href="#" class="text-gray-400 hover:text-white transition">Servicios</a>
                <a href="#" class="text-gray-400 hover:text-white transition">Reservar</a>
                <a href="#" class="text-gray-400 hover:text-white transition">Historial</a>
                <a href="#" class="text-gray-400 hover:text-white transition">Catálogo</a>
                <a href="#" class="text-gray-400 hover:text-white transition">Nosotros</a>
                <a href="#" class="text-gray-400 hover:text-white transition">Contacto</a>
            </nav>

            <div class="flex items-center gap-5">
                <a href="#" class="text-xs font-medium text-gray-400 hover:text-white transition flex items-center gap-1.5">
                    <i data-lucide="user" class="w-4 h-4 text-brand-500"></i> Acceso Administrador
                </a>
                <a href="#" class="bg-brand-600 hover:bg-brand-700 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-lg shadow-brand-600/20">
                    <i data-lucide="calendar" class="w-4 h-4"></i> Reserva tu cita
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-12 flex-grow flex flex-col justify-center space-y-12 relative z-10">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center w-full">
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2 bg-brand-950/50 border border-brand-900/50 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-brand-400">
                    <i data-lucide="bike" class="w-3.5 h-3.5"></i> Taller especializado en motocicletas
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-[52px] font-black tracking-tight {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} leading-[1.15]">
                    Mantenimiento profesional<br>
                    para tu moto <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500  to-brand-500">
                        con tecnología inteligente
                    </span>
                </h1>

                <p class="text-gray-400 text-sm md:text-base max-w-xl leading-relaxed">
                    Reserva mantenimientos online, consulta tu historial por placa y realiza seguimiento a tus reparaciones en tiempo real.
                </p>

                <div class="grid grid-cols-3 gap-4 text-xs font-semibold text-gray-300 max-w-lg pt-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-brand-950/40 border border-brand-900/30 flex items-center justify-center text-brand-400 shadow-sm"><i data-lucide="shield-check" class="w-4 h-4"></i></div>
                        <span class="text-[11px] leading-tight">Técnicos <br>certificados</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-brand-950/40 border border-brand-900/30 flex items-center justify-center text-brand-400 shadow-sm"><i data-lucide="package" class="w-4 h-4"></i></div>
                        <span class="text-[11px] leading-tight">Repuestos <br>originales</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-brand-950/40 border border-brand-900/30 flex items-center justify-center text-brand-400 shadow-sm"><i data-lucide="award" class="w-4 h-4"></i></div>
                        <span class="text-[11px] leading-tight">Garantía de <br>servicio</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="#" class="bg-brand-600 hover:bg-brand-700 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} px-8 py-3.5 rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-lg shadow-brand-600/30">
                        <i data-lucide="calendar" class="w-4 h-4"></i> Reservar Ahora
                    </a>
                    <a href="#buscar-placa" class="neon-border bg-slate-950/40 text-gray-300 hover:text-white px-8 py-3.5 rounded-xl text-xs font-bold flex items-center gap-2 transition backdrop-blur-sm">
                        <i data-lucide="search" class="w-4 h-4"></i> Consultar Historial
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-6 max-w-sm border-t border-white/5">
                    <div>
                        <div class="text-2xl font-black text-white">+5000</div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Motos Atendidas</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">+2000</div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Clientes</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} flex items-center gap-1">4.9 <span class="text-brand-500 text-lg">★</span></div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Valoración</div>
                    </div>
                </div>

                <div class="pt-4 space-y-2">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-gray-600">Trabajamos con las mejores marcas</p>
                    <div class="flex flex-wrap items-center gap-5 opacity-25 grayscale text-xs font-black tracking-tighter text-white">
                        <span>YAMAHA</span> <span>HONDA</span> <span>BAJAJ</span> <span>KTM</span> <span>PULSAR</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                <div class="glass-panel p-5 rounded-2xl w-full max-w-sm shadow-[0_25px_60px_rgba(0,0,0,0.5)] space-y-4">
                    <div class="flex items-center justify-between border-b border-white/5 pb-2.5">
                        <div class="flex items-center gap-1.5 text-xs font-bold {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} tracking-wide">
                            <i data-lucide="activity" class="w-4 h-4 text-brand-500"></i> Sistema Kael
                        </div>
                        <span class="flex items-center gap-1 text-[9px] bg-green-950/60 text-green-400 px-2.5 py-0.5 rounded-full font-bold border border-green-900/50">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span> En tiempo real
                        </span>
                    </div>
                    <div class="space-y-3.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4 text-brand-500"></i> Reservas Hoy</span>
                            <span class="font-bold {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-sm">24</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="wrench" class="w-4 h-4 text-brand-500"></i> Mantenimientos</span>
                            <span class="font-bold {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-sm">182</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="users" class="w-4 h-4 text-brand-500"></i> Clientes Activos</span>
                            <span class="font-bold {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} text-sm">1,247</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-white/5">
                            <span class="text-gray-400 flex items-center gap-2"><i data-lucide="smile" class="w-4 h-4 text-green-400"></i> Satisfacción</span>
                            <span class="font-black text-green-400 text-sm">98%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="buscar-placa" class="glass-panel rounded-2xl p-6 md:p-8 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center shadow-xl">
            <div class="lg:col-span-4 flex items-center gap-4">
                <div class="p-3 bg-brand-950/40 text-brand-400 rounded-xl border border-brand-900/30 shrink-0">
                    <i data-lucide="file-search" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Consulta tu historial por placa</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Revisa todos los mantenimientos anteriores, próximos servicios y más.</p>
                </div>
            </div>
            
            <form action="#" method="GET" class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-12 gap-4 w-full">
                <div class="sm:col-span-9 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-600">
                        <i data-lucide="bike" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="placa" placeholder="Ej: ABC123, BCD456" 
                        class="w-full pl-11 pr-4 py-3.5 bg-slate-950/50 border border-white/10 rounded-xl {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} placeholder-gray-600 focus:outline-none focus:border-brand-500 transition font-mono uppercase tracking-wider text-xs">
                </div>
                <button type="submit" class="sm:col-span-3 bg-brand-600 hover:bg-brand-700 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-bold py-3.5 px-6 rounded-xl transition flex items-center justify-center gap-2 text-xs shadow-lg shadow-brand-600/20">
                    <i data-lucide="search" class="w-4 h-4"></i> Buscar
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-6 pt-6 border-t border-white/5">
            <div class="flex items-start gap-3">
                <i data-lucide="calendar-days" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-white">Reserva Online</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-normal">Agenda tu cita en menos de 1 minuto.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i data-lucide="file-text" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-white">Historial Digital</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-normal">Consulta todo el historial de tu moto.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i data-lucide="map-pin" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-white">Seguimiento en tiempo real</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-normal">Sigue el estado de tu reparación paso a paso.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i data-lucide="package" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-white">Catálogo de Repuestos</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-normal">Repuestos originales con la mejor calidad.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i data-lucide="shield-check" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-white">Garantía Total</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-normal">Todos nuestros servicios incluyen garantía.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-6 pt-6 border-t border-white/5">
            <div class="flex items-start gap-3">
                <i data-lucide="calendar-days" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-white">Reserva Online</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-normal">Agenda tu cita en menos de 1 minuto.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i data-lucide="file-text" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-white">Historial Digital</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-normal">Consulta todo el historial de tu moto.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i data-lucide="map-pin" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-white">Seguimiento en tiempo real</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-normal">Sigue el estado de tu reparación paso a paso.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i data-lucide="package" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-white">Catálogo de Repuestos</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-normal">Repuestos originales con la mejor calidad.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i data-lucide="shield-check" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-xs font-bold text-white">Garantía Total</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-normal">Todos nuestros servicios incluyen garantía.</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>