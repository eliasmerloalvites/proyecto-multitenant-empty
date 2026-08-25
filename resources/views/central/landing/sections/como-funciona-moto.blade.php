    <section id="como-funciona-moto" class="relative py-16 overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950">

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-56 -right-44 w-[650px] h-[650px] rounded-full bg-cyan-400/10 blur-[170px]"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6">

            <div class="text-center mb-12">
                <span class="inline-flex items-center gap-2 rounded-full bg-cyan-500/15 border border-cyan-400/20 text-cyan-300 px-5 py-2 font-bold text-sm mb-6">
                    <i class="fa-solid fa-motorcycle"></i>
                    CÓMO FUNCIONA KAEL MOTO
                </span>

                <h2 class="text-4xl lg:text-5xl font-black leading-tight text-white">
                    Del mostrador
                    <span class="bg-gradient-to-r from-cyan-300 to-blue-400 bg-clip-text text-transparent">
                        al taller digital
                    </span>
                </h2>

                <p class="mt-6 text-xl text-slate-300 leading-9 max-w-3xl mx-auto">
                    Cada moto que entra a tu taller queda registrada por placa,
                    con su historial completo y disponible para tu cliente.
                </p>
            </div>

            <div class="grid lg:grid-cols-[1fr_1.1fr] gap-10 items-center">

                <!-- Captura del panel -->
                <div class="relative order-2 lg:order-1">
                    <div class="absolute -inset-6 bg-cyan-400/10 blur-[70px] rounded-full"></div>

                    <img src="{{ asset('images/web/moto-dashboard-real.png') }}" alt="Panel de Kael Moto"
                        id="motoDashboardImg"
                        class="relative z-10 w-full rounded-2xl border border-slate-700 shadow-2xl"
                        onerror="this.style.display='none'; document.getElementById('motoDashboardFallback').style.display='flex';">

                    <div id="motoDashboardFallback"
                        style="display:none;"
                        class="relative z-10 w-full aspect-[16/10] rounded-2xl border border-slate-700 bg-slate-900/60 backdrop-blur flex-col items-center justify-center text-center p-10">
                        <i class="fa-solid fa-motorcycle text-4xl text-cyan-400 mb-4"></i>
                        <p class="text-slate-300 font-semibold">Captura del panel próximamente</p>
                        <p class="text-slate-500 text-sm mt-1">Órdenes, historial por placa y agenda semanal en un solo lugar.</p>
                    </div>
                </div>

                <!-- Pasos -->
                <div class="space-y-5 order-1 lg:order-2">

                    <div class="flex gap-5 p-5 rounded-2xl border border-slate-700 hover:border-cyan-400/40 hover:bg-white/5 transition">
                        <div class="w-11 h-11 shrink-0 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-white font-black flex items-center justify-center">1</div>
                        <div>
                            <h3 class="font-bold text-white text-lg mb-1">Registra por placa</h3>
                            <p class="text-slate-300 leading-7">Cada moto queda identificada por su placa, con datos del propietario y modelo, sin importar quién la traiga.</p>
                        </div>
                    </div>

                    <div class="flex gap-5 p-5 rounded-2xl border border-slate-700 hover:border-cyan-400/40 hover:bg-white/5 transition">
                        <div class="w-11 h-11 shrink-0 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-white font-black flex items-center justify-center">2</div>
                        <div>
                            <h3 class="font-bold text-white text-lg mb-1">Agenda por bahía y turno</h3>
                            <p class="text-slate-300 leading-7">Tu cliente reserva online y tu equipo ve la semana completa sin choques de horario.</p>
                        </div>
                    </div>

                    <div class="flex gap-5 p-5 rounded-2xl border border-slate-700 hover:border-cyan-400/40 hover:bg-white/5 transition">
                        <div class="w-11 h-11 shrink-0 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-white font-black flex items-center justify-center">3</div>
                        <div>
                            <h3 class="font-bold text-white text-lg mb-1">Checklist digital con fotos</h3>
                            <p class="text-slate-300 leading-7">El técnico documenta el mantenimiento con evidencia fotográfica, sin papeles que se pierden.</p>
                        </div>
                    </div>

                    <div class="flex gap-5 p-5 rounded-2xl border border-slate-700 hover:border-cyan-400/40 hover:bg-white/5 transition">
                        <div class="w-11 h-11 shrink-0 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-white font-black flex items-center justify-center">4</div>
                        <div>
                            <h3 class="font-bold text-white text-lg mb-1">Tu cliente consulta solo</h3>
                            <p class="text-slate-300 leading-7">Con la placa, ve su historial completo en tu web — sin llamar a preguntar cómo va su moto.</p>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </section>
