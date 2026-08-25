    <section id="planes-moto"
        class="relative py-16 overflow-hidden bg-gradient-to-b from-slate-50 via-white to-slate-100">

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
                gap-6
                items-center
                p-7
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
                            class="text-4xl
                        lg:text-5xl
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

                        <img src="{{ asset('/images/web/moto-dashboard-real.png') }}"
                            id="motoHeroImg"
                            onerror="this.style.display='none'; document.getElementById('motoHeroFallback').style.display='flex';"
                            class="relative
                        z-20
                        w-full
                        max-w-[500px]
                        rounded-3xl
                        shadow-[0_40px_100px_rgba(0,0,0,.55)]
                        border
                        border-white/10">

                        <div id="motoHeroFallback" style="display:none;"
                            class="relative z-20 w-full max-w-[500px] aspect-[3/2] rounded-3xl border border-white/10 bg-white/5 backdrop-blur flex-col items-center justify-center text-center p-10 shadow-[0_40px_100px_rgba(0,0,0,.55)]">
                            <i class="fa-solid fa-motorcycle text-5xl text-cyan-400 mb-4"></i>
                            <p class="text-white font-semibold text-lg">Panel de Kael Moto</p>
                            <p class="text-slate-400 text-sm mt-1">Captura próximamente</p>
                        </div>

                        <!-- Celular -->

                        <img src="{{ asset('/images/web/moto-phone-real.png') }}"
                            id="motoHeroPhoneImg"
                            onerror="this.style.display='none';"
                            class="hidden
                        sm:block
                        absolute
                        -bottom-6
                        -right-4
                        w-[120px]
                        rounded-xl
                        border
                        border-white/10
                        z-30
                        shadow-[0_25px_60px_rgba(0,0,0,.7)]
                        animate-float">

                    </div>

                </div>

            </div>

            <!-- ===================================== -->
            <!-- TODO LO QUE INCLUYE KAEL MOTO -->
            <!-- ===================================== -->

            @php
                $funcionesMoto = [
                    [
                        'icon' => 'fa-solid fa-screwdriver-wrench',
                        'color' => 'from-blue-500 to-cyan-400',
                        'title' => 'Mantenimientos',
                        'desc' => 'Registra mantenimientos generales y preventivos, tanto de motos inyectadas como carburadas, más actividades variadas con checklist digital y evidencias fotográficas.',
                    ],
                    [
                        'icon' => 'fa-solid fa-calendar-check',
                        'color' => 'from-indigo-500 to-blue-400',
                        'title' => 'Reservas por bahía y turno',
                        'desc' => 'Tus clientes reservan su cita online y tu equipo visualiza la agenda semanal completa por bahía de trabajo, evitando choques de horario en tiempo real.',
                    ],
                    [
                        'icon' => 'fa-solid fa-clock-rotate-left',
                        'color' => 'from-cyan-500 to-teal-400',
                        'title' => 'Historial por placa',
                        'desc' => 'Cada moto guarda su historial completo de servicios. Tus clientes pueden consultarlo en tu web ingresando la placa, sin necesidad de crear una cuenta.',
                    ],
                    [
                        'icon' => 'fa-solid fa-boxes-stacked',
                        'color' => 'from-purple-500 to-violet-400',
                        'title' => 'Inventario en tiempo real',
                        'desc' => 'Control de stock, categorías, clases y almacenes por sede. Alertas automáticas de stock mínimo para que nunca te falten repuestos clave.',
                        'plan' => 'Plus',
                    ],
                    [
                        'icon' => 'fa-solid fa-cart-shopping',
                        'color' => 'from-emerald-500 to-green-400',
                        'title' => 'Compras y proveedores',
                        'desc' => 'Registra órdenes de compra, controla gastos por tipo y mantén tu cadena de abastecimiento organizada de principio a fin.',
                        'plan' => 'Plus',
                    ],
                    [
                        'icon' => 'fa-solid fa-cash-register',
                        'color' => 'from-amber-500 to-orange-400',
                        'title' => 'Ventas y punto de venta',
                        'desc' => 'Un POS pensado para talleres: ventas rápidas, múltiples métodos de pago, boletas y comprobantes, todo conectado con tu inventario.',
                        'plan' => 'Plus',
                    ],
                    [
                        'icon' => 'fa-solid fa-store',
                        'color' => 'from-pink-500 to-rose-400',
                        'title' => 'Catálogo de repuestos online',
                        'desc' => 'Tu propia página web con catálogo de productos, para que tus clientes vean disponibilidad y precios antes de visitar tu taller.',
                        'plan' => 'Plus',
                    ],
                    [
                        'icon' => 'fa-solid fa-chart-pie',
                        'color' => 'from-sky-500 to-blue-400',
                        'title' => 'Dashboard y reportes',
                        'desc' => 'Reservas de la semana, mantenimientos por estado y por tipo, ventas del mes y más, en un panel que se adapta a tu tema claro u oscuro.',
                    ],
                ];
            @endphp

            <div class="text-center max-w-3xl mx-auto mt-16 mb-8">

                <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-5 py-2 text-blue-700 text-sm font-bold mb-6">
                    <i class="fa-solid fa-motorcycle"></i>
                    TODO EN UNA SOLA PLATAFORMA
                </span>

                <h3 class="text-4xl lg:text-4xl font-black text-slate-900 leading-tight">
                    Todo lo que tu taller
                    <span class="bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                        necesita para crecer
                    </span>
                </h3>

                <p class="mt-5 text-slate-500 text-lg leading-8">
                    Desde el primer mantenimiento registrado hasta la venta de un repuesto: cada función de Kael Moto
                    está pensada para el día a día real de un taller de motocicletas.
                </p>

            </div>

            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6">
                @foreach ($funcionesMoto as $funcion)
                    <div class="group relative rounded-3xl border border-slate-200 bg-white p-7 shadow-lg hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300">

                        @if (isset($funcion['plan']))
                            <span class="absolute top-5 right-5 text-[10px] font-black tracking-wide px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">
                                {{ strtoupper($funcion['plan']) }}
                            </span>
                        @endif

                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $funcion['color'] }} flex items-center justify-center text-white text-xl shadow-lg mb-5">
                            <i class="{{ $funcion['icon'] }}"></i>
                        </div>

                        <h4 class="text-lg font-bold text-slate-900 mb-2">
                            {{ $funcion['title'] }}
                        </h4>

                        <p class="text-slate-500 text-sm leading-6">
                            {{ $funcion['desc'] }}
                        </p>

                    </div>
                @endforeach
            </div>

            <p class="text-center text-slate-400 text-sm mt-8">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Las funciones marcadas <span class="font-bold text-amber-600">PLUS</span> están disponibles a partir del plan Plus. Mantenimientos y Reservas están incluidos desde el plan Start.
            </p>

            <!-- ===================================== -->
            <!-- PLANES (Start / Basic / Plus / Empresarial) -->
            <!-- ===================================== -->

            @php
                $planesMoto = [
                    [
                        'key' => 'start',
                        'icon' => 'fa-solid fa-rocket',
                        'badge' => 'PLAN START',
                        'badgeSub' => 'IDEAL PARA EMPEZAR',
                        'badgeClasses' => 'bg-cyan-50 text-cyan-700',
                        'titleLine1' => 'Digitaliza y organiza',
                        'titleAccent' => 'tus mantenimientos',
                        'desc' => 'El plan ideal para talleres que quieren digitalizar y organizar sus mantenimientos desde el primer día.',
                        'price' => '49',
                        'priceNote' => null,
                        'gradient' => 'from-blue-600 to-cyan-500',
                        'gradientHover' => 'hover:from-blue-700 hover:to-cyan-600',
                        'iconBg' => 'bg-blue-50',
                        'iconColor' => 'text-blue-600',
                        'includeFrom' => null,
                        'features' => [
                            'Gestión de clientes y motocicletas',
                            'Historial de mantenimientos',
                            'Mantenimientos preventivos y correctivos',
                            'Checklist digital y evidencias fotográficas',
                            'Reportes en PDF y dashboard básico',
                            'Página web informativa (info, servicios y contacto)',
                        ],
                        'capacidad' => ['Locales' => '1', 'Usuarios' => 'Hasta 2'],
                        'noIncluye' => 'Reservas online, inventario, compras, ventas y catálogo web',
                        'cta' => 'Solicitar información',
                        'ctaDark' => false,
                        'highlighted' => false,
                    ],
                    [
                        'key' => 'basic',
                        'icon' => 'fa-solid fa-globe',
                        'badge' => 'PLAN BASIC',
                        'badgeSub' => 'TU TALLER ONLINE',
                        'badgeClasses' => 'bg-indigo-50 text-indigo-700',
                        'titleLine1' => 'Conecta con tus clientes',
                        'titleAccent' => 'desde tu propia web',
                        'desc' => 'Organiza tus mantenimientos y deja que tus clientes reserven citas y consulten su historial en línea.',
                        'price' => '99',
                        'priceNote' => null,
                        'gradient' => 'from-indigo-600 to-blue-500',
                        'gradientHover' => 'hover:from-indigo-700 hover:to-blue-600',
                        'iconBg' => 'bg-indigo-50',
                        'iconColor' => 'text-indigo-600',
                        'includeFrom' => 'Todo lo del plan Start, más:',
                        'features' => [
                            'Reservas online en tu página web',
                            'Página web dinámica (servicios, contacto)',
                            'Tus clientes ven su último mantenimiento',
                            'Notificaciones de reservas por email',
                            'Gestión de usuarios y roles básicos',
                            'Soporte técnico prioritario',
                        ],
                        'capacidad' => ['Locales' => '1', 'Usuarios' => 'Hasta 5'],
                        'noIncluye' => 'Inventario, compras, ventas y catálogo de productos',
                        'cta' => 'Comenzar ahora',
                        'ctaDark' => false,
                        'highlighted' => false,
                    ],
                    [
                        'key' => 'plus',
                        'icon' => 'fa-solid fa-chart-line',
                        'badge' => 'PLAN PLUS',
                        'badgeSub' => '⭐ MÁS ELEGIDO',
                        'badgeClasses' => 'bg-white/15 text-white',
                        'titleLine1' => 'El control completo',
                        'titleAccent' => 'de tu taller',
                        'desc' => 'Inventario, compras, ventas y una web con catálogo de repuestos. Todo integrado en un solo sistema.',
                        'price' => '199',
                        'priceNote' => 'Hasta S/ 259 / mes según tu operación',
                        'gradient' => 'from-blue-500 to-cyan-400',
                        'gradientHover' => 'hover:from-blue-400 hover:to-cyan-300',
                        'iconBg' => 'bg-white/10',
                        'iconColor' => 'text-cyan-300',
                        'includeFrom' => 'Todo lo del plan Basic, más:',
                        'features' => [
                            'Gestión completa de inventario en tiempo real',
                            'Compras a proveedores y órdenes de compra',
                            'Ventas con boletas y comprobantes',
                            'Catálogo de repuestos en tu web',
                            'Múltiples métodos de pago (Yape, tarjeta y más)',
                            'Alertas de stock mínimo y reportes de ventas',
                        ],
                        'capacidad' => ['Locales' => '1', 'Usuarios' => 'Hasta 10'],
                        'noIncluye' => null,
                        'cta' => 'Comenzar ahora',
                        'ctaDark' => false,
                        'highlighted' => true,
                    ],
                    [
                        'key' => 'empresarial',
                        'icon' => 'fa-solid fa-building',
                        'badge' => 'PLAN EMPRESARIAL',
                        'badgeSub' => 'A MEDIDA',
                        'badgeClasses' => 'bg-amber-50 text-amber-700',
                        'titleLine1' => 'Solución',
                        'titleAccent' => 'personalizada',
                        'desc' => 'Para talleres con múltiples sucursales que necesitan control total de su operación y desarrollos a medida.',
                        'price' => '300',
                        'priceNote' => 'o más, según tus necesidades',
                        'gradient' => 'from-amber-500 to-yellow-400',
                        'gradientHover' => 'hover:from-amber-400 hover:to-yellow-300',
                        'iconBg' => 'bg-amber-50',
                        'iconColor' => 'text-amber-600',
                        'includeFrom' => 'Todo lo del plan Plus, más:',
                        'features' => [
                            'Multisucursal: administra todas tus sedes',
                            'Usuarios y roles ilimitados',
                            'Tableros gerenciales y reportes avanzados',
                            'Integraciones a medida (API, contabilidad, etc.)',
                            'Capacitación personalizada para tu equipo',
                            'Soporte prioritario 24/7',
                        ],
                        'capacidad' => ['Sucursales' => 'Ilimitadas', 'Usuarios' => 'Ilimitados'],
                        'noIncluye' => null,
                        'cta' => 'Solicitar cotización',
                        'ctaDark' => true,
                        'highlighted' => false,
                    ],
                ];
            @endphp

            <div class="grid lg:grid-cols-2 xl:grid-cols-4 gap-6 mt-10 items-stretch">
                @foreach ($planesMoto as $plan)
                    <div
                        class="group relative overflow-hidden rounded-[30px]
                        {{ $plan['highlighted']
                            ? 'border-2 border-cyan-400 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 shadow-2xl shadow-cyan-500/20 xl:-translate-y-3'
                            : 'border border-slate-200 bg-white shadow-xl' }}
                        hover:-translate-y-2 hover:shadow-2xl transition-all duration-500 flex flex-col">

                        {{-- Barra superior --}}
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r {{ $plan['gradient'] }}"></div>

                        @if ($plan['highlighted'])
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                <span class="inline-flex items-center gap-1 px-4 py-1.5 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 text-slate-900 font-black text-[11px] shadow-lg">
                                    ⭐ MÁS ELEGIDO
                                </span>
                            </div>
                        @endif

                        <div class="p-7 pt-9 h-full flex flex-col {{ $plan['highlighted'] ? 'text-white' : 'text-slate-900' }}">

                            {{-- Badge --}}
                            <span class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full {{ $plan['highlighted'] ? 'bg-white/10 text-cyan-200' : $plan['badgeClasses'] }} font-bold text-[11px] mb-5">
                                <i class="{{ $plan['icon'] }}"></i>
                                {{ $plan['badge'] }}
                            </span>

                            {{-- Título --}}
                            <h3 class="text-2xl font-black leading-tight">
                                {{ $plan['titleLine1'] }}
                                <span class="block bg-gradient-to-r {{ $plan['gradient'] }} bg-clip-text text-transparent">
                                    {{ $plan['titleAccent'] }}
                                </span>
                            </h3>

                            <p class="{{ $plan['highlighted'] ? 'text-slate-300' : 'text-slate-500' }} mt-3 text-sm leading-6">
                                {{ $plan['desc'] }}
                            </p>

                            {{-- Precio --}}
                            <div class="rounded-2xl bg-gradient-to-r {{ $plan['gradient'] }} text-white mt-6 p-5 text-center shadow-xl">
                                <div class="flex items-end justify-center gap-1">
                                    <span class="text-lg mb-1">S/</span>
                                    <span class="text-4xl font-black leading-none">{{ $plan['price'] }}</span>
                                    <span class="text-sm mb-1 opacity-80">/mes</span>
                                </div>
                                @if ($plan['priceNote'])
                                    <p class="text-[11px] uppercase tracking-wider opacity-80 mt-2">{{ $plan['priceNote'] }}</p>
                                @endif
                            </div>

                            {{-- Características --}}
                            <div class="mt-6 space-y-3">
                                @if ($plan['includeFrom'])
                                    <p class="text-xs font-bold uppercase tracking-wide {{ $plan['highlighted'] ? 'text-cyan-300' : 'text-slate-400' }}">
                                        {{ $plan['includeFrom'] }}
                                    </p>
                                @endif

                                @foreach ($plan['features'] as $feature)
                                    <div class="flex gap-3 items-start text-sm">
                                        <div class="w-5 h-5 mt-0.5 rounded-full {{ $plan['highlighted'] ? 'bg-cyan-400/20' : $plan['iconBg'] }} flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-check text-[10px] {{ $plan['highlighted'] ? 'text-cyan-300' : $plan['iconColor'] }}"></i>
                                        </div>
                                        <span>{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Capacidad --}}
                            <div class="mt-6 rounded-2xl {{ $plan['highlighted'] ? 'bg-white/5 border border-white/10' : 'bg-slate-50 border border-slate-200' }} p-4">
                                <div class="space-y-2 text-sm">
                                    @foreach ($plan['capacidad'] as $label => $value)
                                        <div class="flex justify-between">
                                            <span class="{{ $plan['highlighted'] ? 'text-slate-400' : 'text-slate-500' }}">{{ $label }}</span>
                                            <span class="font-semibold">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($plan['noIncluye'])
                                    <p class="text-[11px] {{ $plan['highlighted'] ? 'text-slate-500' : 'text-slate-400' }} mt-3 pt-3 border-t {{ $plan['highlighted'] ? 'border-white/10' : 'border-slate-200' }}">
                                        <i class="fa-solid fa-circle-xmark mr-1"></i>
                                        No incluye: {{ $plan['noIncluye'] }}
                                    </p>
                                @endif
                            </div>

                            {{-- Botón --}}
                            <div class="mt-7 mt-auto pt-7">
                                <a href="{{ $plan['key'] === 'empresarial' ? url('/') . '#contacto' : route('central.registro.show', ['plan' => $plan['key']]) }}"
                                    class="block text-center w-full py-3.5 rounded-xl font-bold shadow-xl transition
                                    {{ $plan['ctaDark']
                                        ? 'bg-gradient-to-r ' . $plan['gradient'] . ' text-slate-900 ' . $plan['gradientHover']
                                        : 'bg-gradient-to-r ' . $plan['gradient'] . ' text-white ' . $plan['gradientHover'] }}">
                                    {{ $plan['cta'] }}
                                </a>
                            </div>

                        </div>

                    </div>
                @endforeach
            </div>

            <p class="text-center text-slate-400 text-sm mt-10">
                Todos los planes incluyen datos seguros y respaldados, acceso desde cualquier dispositivo,
                soporte técnico y actualizaciones constantes sin costo adicional.
            </p>


        </div>

    </section>
