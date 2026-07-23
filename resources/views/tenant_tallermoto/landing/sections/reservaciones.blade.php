<!-- Oscurecer fondo
    <div class="absolute inset-0 z-10 "></div>-->

<!-- Gradiente lateral -->
<main class="relative  overflow-hidden">
    {{-- <div class="absolute inset-0 z-20
        bg-gradient-to-l
        from-slate-700 v ">
    </div> --}}

    <div class="relative z-30 max-w-7xl mx-auto px-6 py-12">

        <section id="reservas-main" class="py-20 relative overflow-hidden">
            <div
                class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand-600/5 rounded-full blur-[120px] pointer-events-none">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <div
                    class="flex flex-col md:flex-row justify-between items-end gap-6 mb-12 border-b border-white/5 pb-8">
                    <div class="space-y-3">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-950/50 border border-emerald-500/30 rounded-full text-[10px] font-bold uppercase tracking-widest text-emerald-400">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                            Sistema de Bahías Activo
                        </div>
                        <h2
                            class="text-3xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase tracking-tighter">
                            Centro de <span class="text-brand-500">Agendamiento</span>
                        </h2>
                        <p class="text-xs text-gray-500 max-w-md">
                            Selecciona un bloque horario disponible. Nuestro sistema asigna automáticamente la bahía
                            técnica mejor equipada para tu tipo de moto.
                        </p>
                    </div>

                    <div class="flex items-center gap-4 bg-slate-900/50 p-2 rounded-2xl border border-white/5">
                        <div class="text-right">
                            <span class="block text-[10px] text-gray-500 font-bold uppercase">Semana Actual</span>
                            <span
                                class="text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-mono">08
                                Jun - 13 Jun</span>
                        </div>
                        <div
                            class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} shadow-lg shadow-brand-600/20">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <div class="lg:col-span-8 space-y-6">

                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                            <button
                                class="bg-brand-600 border border-brand-400/50 p-3 rounded-2xl text-center transition-all shadow-[0_0_20px_rgba(37,99,235,0.2)]">
                                <span class="block text-[10px] text-brand-100 font-bold uppercase">Lun</span>
                                <span
                                    class="text-lg font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} italic">08</span>
                            </button>
                            <button
                                class="bg-slate-900/40 border border-white/5 p-3 rounded-2xl text-center hover:border-white/20 transition-all group">
                                <span
                                    class="block text-[10px] text-gray-500 font-bold uppercase group-hover:text-gray-300">Mar</span>
                                <span class="text-lg font-black text-gray-400 group-hover:text-white italic">09</span>
                            </button>
                            <button
                                class="bg-slate-900/40 border border-white/5 p-3 rounded-2xl text-center hover:border-white/20 transition-all group">
                                <span
                                    class="block text-[10px] text-gray-500 font-bold uppercase group-hover:text-gray-300">Mie</span>
                                <span class="text-lg font-black text-gray-400 group-hover:text-white italic">10</span>
                            </button>
                            <button
                                class="bg-slate-900/40 border border-white/5 p-3 rounded-2xl text-center hover:border-white/20 transition-all group">
                                <span
                                    class="block text-[10px] text-gray-500 font-bold uppercase group-hover:text-gray-300">Jue</span>
                                <span class="text-lg font-black text-gray-400 group-hover:text-white italic">11</span>
                            </button>
                            <button
                                class="bg-slate-900/40 border border-white/5 p-3 rounded-2xl text-center hover:border-white/20 transition-all group">
                                <span
                                    class="block text-[10px] text-gray-500 font-bold uppercase group-hover:text-gray-300">Vie</span>
                                <span class="text-lg font-black text-gray-400 group-hover:text-white italic">12</span>
                            </button>
                            <button
                                class="bg-slate-900/40 border border-white/5 p-3 rounded-2xl text-center hover:border-white/20 transition-all group">
                                <span
                                    class="block text-[10px] text-gray-500 font-bold uppercase group-hover:text-gray-300">Sab</span>
                                <span class="text-lg font-black text-gray-400 group-hover:text-white italic">13</span>
                            </button>
                        </div>

                        <div class="space-y-4">

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                <div class="md:col-span-2 text-center md:text-left">
                                    <span
                                        class="block text-xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-mono tracking-tighter italic">08:00</span>
                                    <span class="text-[10px] text-gray-500 font-bold uppercase">AM Bloque</span>
                                </div>
                                <div class="md:col-span-10 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="relative group cursor-not-allowed">
                                        <div class="absolute inset-0 bg-red-500/5 rounded-2xl border border-red-500/20">
                                        </div>
                                        <div class="relative p-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-slate-950 rounded-xl flex items-center justify-center text-red-500/50 border border-red-500/10">
                                                    <i data-lucide="bike" class="w-5 h-5"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-black text-gray-400 uppercase">Bahía 01</h4>
                                                    <p class="text-[10px] text-red-500/70 font-bold tracking-widest">
                                                        OCUPADO</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="block text-[9px] text-gray-600 font-bold">CLIENTE</span>
                                                <span class="text-[10px] text-gray-500 font-mono">ELVIS MERLE</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="relative group overflow-hidden transition-all hover:-translate-y-1">
                                        <div
                                            class="absolute inset-0 bg-slate-900/40 group-hover:bg-brand-600/10 rounded-2xl border border-white/5 group-hover:border-brand-500/50 transition-all">
                                        </div>
                                        <div class="relative p-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                                                    <i data-lucide="plus" class="w-5 h-5"></i>
                                                </div>
                                                <div class="text-left">
                                                    <h4
                                                        class="text-xs font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase">
                                                        Bahía 02</h4>
                                                    <p
                                                        class="text-[10px] text-emerald-400 font-bold tracking-widest animate-pulse">
                                                        DISPONIBLE</p>
                                                </div>
                                            </div>
                                            <i data-lucide="chevron-right"
                                                class="w-4 h-4 text-gray-700 group-hover:text-brand-500 transition-colors"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                <div class="md:col-span-2 text-center md:text-left">
                                    <span
                                        class="block text-xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-mono tracking-tighter italic">10:30</span>
                                    <span class="text-[10px] text-gray-500 font-bold uppercase">AM Bloque</span>
                                </div>
                                <div class="md:col-span-10 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <button class="relative group overflow-hidden transition-all hover:-translate-y-1">
                                        <div
                                            class="absolute inset-0 bg-slate-900/40 group-hover:bg-brand-600/10 rounded-2xl border border-white/5 group-hover:border-brand-500/50 transition-all">
                                        </div>
                                        <div class="relative p-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                                                    <i data-lucide="plus" class="w-5 h-5"></i>
                                                </div>
                                                <div class="text-left">
                                                    <h4
                                                        class="text-xs font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase">
                                                        Bahía 01</h4>
                                                    <p class="text-[10px] text-emerald-400 font-bold tracking-widest">
                                                        DISPONIBLE</p>
                                                </div>
                                            </div>
                                            <i data-lucide="chevron-right"
                                                class="w-4 h-4 text-gray-700 group-hover:text-brand-500 transition-colors"></i>
                                        </div>
                                    </button>
                                    <button class="relative group overflow-hidden transition-all hover:-translate-y-1">
                                        <div
                                            class="absolute inset-0 bg-slate-900/40 group-hover:bg-brand-600/10 rounded-2xl border border-white/5 group-hover:border-brand-500/50 transition-all">
                                        </div>
                                        <div class="relative p-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                                                    <i data-lucide="plus" class="w-5 h-5"></i>
                                                </div>
                                                <div class="text-left">
                                                    <h4
                                                        class="text-xs font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase">
                                                        Bahía 02</h4>
                                                    <p class="text-[10px] text-emerald-400 font-bold tracking-widest">
                                                        DISPONIBLE</p>
                                                </div>
                                            </div>
                                            <i data-lucide="chevron-right"
                                                class="w-4 h-4 text-gray-700 group-hover:text-brand-500 transition-colors"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                <div class="md:col-span-2 text-center md:text-left">
                                    <span
                                        class="block text-xl font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-mono tracking-tighter italic">03:00</span>
                                    <span class="text-[10px] text-gray-500 font-bold uppercase">PM Bloque</span>
                                </div>
                                <div class="md:col-span-10 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="relative">
                                        <div
                                            class="absolute inset-0 bg-brand-500/5 rounded-2xl border border-brand-500/20">
                                        </div>
                                        <div class="relative p-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-brand-600 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} rounded-xl flex items-center justify-center border border-brand-400/30">
                                                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                                                </div>
                                                <div>
                                                    <h4
                                                        class="text-xs font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase">
                                                        Bahía 01</h4>
                                                    <p class="text-[10px] text-brand-400 font-bold tracking-widest">SU
                                                        SELECCIÓN</p>
                                                </div>
                                            </div>
                                            <button class="p-1.5 hover:bg-white/5 rounded-lg transition-colors">
                                                <i data-lucide="x" class="w-4 h-4 text-gray-500"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button class="relative group overflow-hidden transition-all hover:-translate-y-1">
                                        <div
                                            class="absolute inset-0 bg-slate-900/40 group-hover:bg-brand-600/10 rounded-2xl border border-white/5 group-hover:border-brand-500/50 transition-all">
                                        </div>
                                        <div class="relative p-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                                                    <i data-lucide="plus" class="w-5 h-5"></i>
                                                </div>
                                                <div class="text-left">
                                                    <h4
                                                        class="text-xs font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase">
                                                        Bahía 02</h4>
                                                    <p class="text-[10px] text-emerald-400 font-bold tracking-widest">
                                                        DISPONIBLE</p>
                                                </div>
                                            </div>
                                            <i data-lucide="chevron-right"
                                                class="w-4 h-4 text-gray-700 group-hover:text-brand-500 transition-colors"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div
                            class="glass-panel sticky top-24 rounded-3xl p-6 space-y-6 border border-white/10 shadow-2xl">
                            <h3
                                class="text-sm font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} uppercase tracking-widest border-b border-white/5 pb-4 flex items-center gap-2">
                                <i data-lucide="shopping-bag" class="w-4 h-4 text-brand-500"></i> Resumen de Reserva
                            </h3>

                            <div class="space-y-4">
                                <div
                                    class="flex justify-between items-center bg-slate-950/50 p-3 rounded-2xl border border-white/5">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Fecha
                                        Seleccionada</span>
                                    <span
                                        class="text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-mono">Lunes,
                                        08 de Junio</span>
                                </div>
                                <div
                                    class="flex justify-between items-center bg-slate-950/50 p-3 rounded-2xl border border-white/5">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Bloque
                                        Horario</span>
                                    <span
                                        class="text-xs {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} font-mono font-black italic">03:00
                                        PM</span>
                                </div>
                                <div
                                    class="flex justify-between items-center bg-slate-950/50 p-3 rounded-2xl border border-white/5">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Punto de
                                        Trabajo</span>
                                    <span class="text-xs text-brand-400 font-mono font-black">Bahía Técnica 01</span>
                                </div>
                            </div>

                            <div class="bg-brand-600/5 border border-brand-500/20 rounded-2xl p-4">
                                <div class="flex gap-3">
                                    <i data-lucide="info" class="w-4 h-4 text-brand-400 shrink-0"></i>
                                    <p class="text-[10px] text-gray-400 leading-relaxed">
                                        Su reserva garantiza la prioridad en la bahía. Por favor, llegue 10 minutos
                                        antes para el registro de ingreso.
                                    </p>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-gradient-to-r from-brand-500 to-brand-400 hover:from-brand-400 hover:to-brand-500 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-brand-500/20 hover:shadow-xl hover:shadow-brand-500/40 flex items-center justify-center gap-2 group">
                                Confirmar Cita
                                <i data-lucide="arrow-right"
                                    class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                            </button>

                            <p class="text-center text-[9px] text-gray-600 font-bold uppercase tracking-tighter">
                                Sistema Asegurado • KAEL Technologies 2026
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>


    </div>
</main>

<script></script>
