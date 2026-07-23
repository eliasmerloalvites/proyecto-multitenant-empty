<header @class([
    'fixed w-full backdrop-blur-md z-50 border-b transition-colors duration-300',
    'bg-slate-950/40 border-white/5' => $colorview == 'dark',
    'bg-white/80 border-gray-200/50 shadow-sm' => $colorview !== 'dark',
])>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span
                class="{{ $colorview == 'dark' ? 'text-white' : 'text-gray-900' }} text-2xl font-black tracking-wider flex items-center">
                <span class="text-brand-500 font-extrabold mr-0.5">K</span>KAEL
            </span>
        </div>

        <nav class="hidden md:flex items-center gap-8 text-xs font-semibold uppercase tracking-widest">
            @php
                $links = [
                    'Inicio' => ['route' => 'central.inicio', 'url' => '/'],
                    'Servicios' => ['route' => 'web.servicios', 'url' => '/servicios'],
                    'Reservar' => ['route' => 'web.reservar', 'url' => '/reservar'],
                    'Historial' => ['route' => 'web.historial', 'url' => '/historial'],
                    'Catálogo' => ['route' => 'web.catalogo', 'url' => '/catalogo'],
                    'Nosotros' => ['route' => 'web.nosotros', 'url' => '/nosotros'],
                    'Contacto' => ['route' => 'web.contacto', 'url' => '/contacto'],
                ];
            @endphp

            @foreach ($links as $name => $data)
                <a href="{{ $data['url'] }}" @class([
                    'pb-1 transition-colors duration-200',
                    'text-brand-500 border-b-2 border-brand-500' => request()->routeIs(
                        $data['route']),
                    'text-gray-400 hover:text-white' =>
                        !request()->routeIs($data['route']) && $colorview == 'dark',
                    'text-gray-600 hover:text-gray-900' =>
                        !request()->routeIs($data['route']) && $colorview !== 'dark',
                ])>
                    {{ $name }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-5">
            <a href="{{ tenant_url('tenant.login') }}" @class([
                'text-xs font-medium transition flex items-center gap-1.5',
                'text-gray-400 hover:text-white' => $colorview == 'dark',
                'text-gray-600 hover:text-gray-900' => $colorview !== 'dark',
            ])>
                <i data-lucide="user" class="w-4 h-4 text-brand-500"></i> Acceso Administrador
            </a>

            <a href="/reservar" @class([
                'px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all duration-300 border',
                'bg-white hover:bg-gray-50 border-gray-200 text-gray-900 shadow-sm' =>
                    $colorview !== 'dark',
                'bg-slate-900 hover:bg-slate-800 border-white/10 text-white shadow-[0_4px_20px_rgba(0,0,0,0.3)]' =>
                    $colorview == 'dark',
            ])>
                <i data-lucide="calendar" @class([
                    'w-4 h-4',
                    'text-brand-500' => $colorview !== 'dark',
                    'text-brand-400' => $colorview == 'dark',
                ])></i>
                Reserva tu cita
            </a>
        </div>
    </div>
</header>
