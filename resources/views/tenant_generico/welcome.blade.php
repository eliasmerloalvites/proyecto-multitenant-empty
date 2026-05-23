<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $empresa->nombre ?? 'Tu Empresa' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .glass {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.8);
        }

        .hero-gradient {
            background: linear-gradient(to right, rgba(255,255,255,.95), rgba(255,255,255,.4));
        }
    </style>
</head>

<body class="bg-[#f5f7fb] text-slate-800">

    @php
        $empresa = $empresa ?? (object) [
            'nombre' => 'TU EMPRESA',
            'slogan' => 'Calidad que te acompaña',
            'descripcion' => 'Productos y servicios pensados para satisfacer las necesidades de tus clientes con atención profesional y resultados garantizados.',
            'whatsapp' => '51987654321',
            'telefono' => '987654321',
            'correo' => 'hola@tuempresa.pe',
            'direccion' => 'Av. Principal 123 - Lima, Perú',
            'horario' => 'Lunes a Viernes 9:00 am - 7:00 pm',
            'nosotros' => 'Somos una empresa enfocada en brindar productos y servicios de calidad, ofreciendo atención personalizada y soluciones eficientes para nuestros clientes.',
            'logo' => null,
            'banner' => null,
            'imagen_nosotros' => null,
        ];

        $productos = $productos ?? [
            (object) [
                'nombre' => 'Producto Premium',
                'descripcion' => 'Producto de alta calidad disponible para tus clientes.',
                'precio' => 149.90,
                'imagen' => null,
            ],
            (object) [
                'nombre' => 'Servicio Profesional',
                'descripcion' => 'Servicio especializado con atención personalizada.',
                'precio' => 299.90,
                'imagen' => null,
            ],
            (object) [
                'nombre' => 'Accesorio Destacado',
                'descripcion' => 'Complemento ideal para tus necesidades.',
                'precio' => 89.90,
                'imagen' => null,
            ],
            (object) [
                'nombre' => 'Producto Empresarial',
                'descripcion' => 'Soluciones eficientes para tu empresa.',
                'precio' => 499.90,
                'imagen' => null,
            ],
        ];
    @endphp

    <!-- NAVBAR -->
    <header class="w-full bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-slate-900 flex items-center justify-center text-white font-bold text-xl shadow-lg overflow-hidden">

                    @if(!empty($empresa->logo))
                        <img src="{{ asset($empresa->logo) }}" class="w-full h-full object-cover">
                    @else
                        K
                    @endif
                </div>

                <div>
                    <h1 class="text-2xl font-black leading-none">
                        {{ $empresa->nombre ?? 'TU EMPRESA' }}
                    </h1>

                    <p class="text-sm text-slate-500">
                        {{ $empresa->slogan ?? 'Calidad que te acompaña' }}
                    </p>
                </div>
            </div>

            <nav class="hidden lg:flex items-center gap-8 font-medium text-[15px]">
                <a href="#inicio" class="text-blue-600 border-b-2 border-blue-600 pb-1">
                    Inicio
                </a>

                <a href="#productos" class="hover:text-blue-600 transition">
                    Productos
                </a>

                <a href="#servicios" class="hover:text-blue-600 transition">
                    Servicios
                </a>

                <a href="#nosotros" class="hover:text-blue-600 transition">
                    Nosotros
                </a>

                <a href="#contacto" class="hover:text-blue-600 transition">
                    Contacto
                </a>
            </nav>

            <div class="flex items-center gap-3">

                <a href="{{ tenant_url('tenant.login') }}"
                    class="hidden md:flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-blue-600 transition">
                    👤 Acceso empleados
                </a>

                <a href="https://wa.me/{{ $empresa->whatsapp ?? '51999999999' }}"
                    target="_blank"
                    class="bg-green-500 hover:bg-green-600 transition text-white font-semibold px-5 py-3 rounded-2xl shadow-lg">
                    WhatsApp
                </a>
            </div>
        </div>
    </header>

    <!-- HERO -->
    <section id="inicio" class="max-w-7xl mx-auto px-6 py-16 grid lg:grid-cols-2 gap-12 items-center">

        <div>
            <div class="inline-flex items-center bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                ⭐ Empresa destacada
            </div>

            <h2 class="text-5xl lg:text-6xl font-black leading-tight tracking-tight mb-6">
                Calidad,
                <br>
                confianza
                <br>
                y el mejor
                <span class="text-blue-600"> servicio.</span>
            </h2>

            <p class="text-slate-600 text-lg leading-relaxed max-w-xl mb-8">
                {{ $empresa->descripcion ?? 'Productos y servicios pensados para satisfacer las necesidades de tus clientes con atención profesional y resultados garantizados.' }}
            </p>

            <div class="flex flex-wrap gap-4">

                <a href="#productos"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-semibold shadow-xl transition">
                    Ver productos / servicios
                </a>

                <a href="#contacto"
                    class="bg-white border border-slate-300 hover:border-blue-600 hover:text-blue-600 px-8 py-4 rounded-2xl font-semibold transition">
                    Contáctanos
                </a>
            </div>
        </div>

        <div>
            <div class="relative overflow-hidden rounded-[32px] shadow-2xl bg-white">

                <img
                    src="{{ $empresa->banner ? asset($empresa->banner) : 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?q=80&w=1600&auto=format&fit=crop' }}"
                    class="w-full h-[520px] object-cover">

                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

                <div class="absolute bottom-6 left-6 right-6 glass rounded-2xl p-5 shadow-xl border border-white/50">
                    <div class="flex items-center justify-between">

                        <div>
                            <h3 class="text-xl font-bold">
                                {{ $empresa->nombre ?? 'TU EMPRESA' }}
                            </h3>

                            <p class="text-slate-600 text-sm">
                                Atención profesional garantizada
                            </p>
                        </div>

                        <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl font-bold shadow-lg overflow-hidden">

                            @if(!empty($empresa->logo))
                                <img src="{{ asset($empresa->logo) }}" class="w-full h-full object-cover">
                            @else
                                K
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="max-w-7xl mx-auto px-6 mb-20">

        <div class="bg-white rounded-[32px] shadow-xl border border-slate-100 grid md:grid-cols-2 lg:grid-cols-4 overflow-hidden">

            <div class="p-8 border-b lg:border-b-0 lg:border-r border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl mb-5">
                    🏅
                </div>

                <h3 class="font-bold text-lg mb-2">Productos de calidad</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Seleccionamos lo mejor para nuestros clientes.
                </p>
            </div>

            <div class="p-8 border-b lg:border-b-0 lg:border-r border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl mb-5">
                    🎧
                </div>

                <h3 class="font-bold text-lg mb-2">Atención personalizada</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Te asesoramos en todo lo que necesites.
                </p>
            </div>

            <div class="p-8 border-b lg:border-b-0 lg:border-r border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl mb-5">
                    🛡️
                </div>

                <h3 class="font-bold text-lg mb-2">Confianza garantizada</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Comprometidos con tu satisfacción.
                </p>
            </div>

            <div class="p-8">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl mb-5">
                    🚚
                </div>

                <h3 class="font-bold text-lg mb-2">Envíos rápidos</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Entregas seguras a todo el país.
                </p>
            </div>
        </div>
    </section>

    <!-- PRODUCTOS -->
    <section id="productos" class="max-w-7xl mx-auto px-6 mb-24">

        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-4xl font-black mb-2">
                    Nuestros productos y servicios
                </h2>

                <p class="text-slate-500">
                    Soluciones pensadas para todo tipo de cliente.
                </p>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">

            @foreach($productos ?? [] as $producto)

                <div class="bg-white rounded-[28px] overflow-hidden border border-slate-100 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">

                    <div class="h-60 overflow-hidden bg-slate-100">

                        <img
                            src="{{ !empty($producto->imagen) ? asset($producto->imagen) : 'https://placehold.co/600x600/f1f5f9/64748b?text=Producto' }}"
                            class="w-full h-full object-cover hover:scale-110 transition duration-500">
                    </div>

                    <div class="p-6">

                        <h3 class="text-xl font-bold mb-2 line-clamp-1">
                            {{ $producto->nombre }}
                        </h3>

                        <p class="text-slate-500 text-sm mb-5 line-clamp-2">
                            {{ $producto->descripcion ?? 'Producto disponible.' }}
                        </p>

                        @if(!empty($producto->precio))
                            <div class="text-3xl font-black text-blue-600 mb-5">
                                S/ {{ number_format($producto->precio, 2) }}
                            </div>
                        @endif

                        <a
                            href="https://wa.me/{{ $empresa->whatsapp ?? '51999999999' }}?text=Hola,%20quiero%20consultar%20sobre%20{{ urlencode($producto->nombre) }}"
                            target="_blank"
                            class="block w-full text-center bg-green-500 hover:bg-green-600 text-white py-3 rounded-2xl font-semibold transition">
                            Consultar
                        </a>
                    </div>
                </div>

            @endforeach

        </div>
    </section>

    <!-- NOSOTROS -->
    <section id="nosotros" class="max-w-7xl mx-auto px-6 mb-24">

        <div class="grid lg:grid-cols-2 gap-10 bg-white rounded-[36px] p-10 shadow-xl border border-slate-100 items-center">

            <div>
                <div class="inline-flex bg-blue-100 text-blue-700 px-4 py-2 rounded-full font-semibold text-sm mb-6">
                    Sobre nosotros
                </div>

                <h2 class="text-5xl font-black leading-tight mb-6">
                    Comprometidos
                    <br>
                    con la excelencia.
                </h2>

                <p class="text-slate-600 text-lg leading-relaxed mb-10">
                    {{ $empresa->nosotros ?? 'Somos una empresa enfocada en brindar productos y servicios de calidad, ofreciendo atención personalizada y soluciones eficientes para nuestros clientes.' }}
                </p>

                <div class="grid grid-cols-3 gap-6 mb-10">

                    <div>
                        <h3 class="text-4xl font-black text-blue-600 mb-1">+5</h3>
                        <p class="text-slate-500 text-sm">Años de experiencia</p>
                    </div>

                    <div>
                        <h3 class="text-4xl font-black text-blue-600 mb-1">+1000</h3>
                        <p class="text-slate-500 text-sm">Clientes satisfechos</p>
                    </div>

                    <div>
                        <h3 class="text-4xl font-black text-blue-600 mb-1">100%</h3>
                        <p class="text-slate-500 text-sm">Compromiso total</p>
                    </div>
                </div>
            </div>

            <div>
                <img
                    src="{{ $empresa->imagen_nosotros ? asset($empresa->imagen_nosotros) : 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=1600&auto=format&fit=crop' }}"
                    class="rounded-[32px] shadow-2xl w-full h-[520px] object-cover">
            </div>
        </div>
    </section>

    <!-- CONTACTO -->
    <section id="contacto" class="max-w-7xl mx-auto px-6 mb-24">

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-white rounded-[28px] p-8 border border-slate-100 shadow-lg">
                <div class="text-3xl mb-5">📍</div>
                <h3 class="text-xl font-bold mb-4">Visítanos</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ $empresa->direccion ?? 'Lima, Perú' }}
                </p>
            </div>

            <div class="bg-white rounded-[28px] p-8 border border-slate-100 shadow-lg">
                <div class="text-3xl mb-5">📞</div>
                <h3 class="text-xl font-bold mb-4">Llámanos</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ $empresa->telefono ?? '987654321' }}
                </p>
            </div>

            <div class="bg-white rounded-[28px] p-8 border border-slate-100 shadow-lg">
                <div class="text-3xl mb-5">✉️</div>
                <h3 class="text-xl font-bold mb-4">Escríbenos</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ $empresa->correo ?? 'correo@empresa.com' }}
                </p>
            </div>

            <div class="bg-white rounded-[28px] p-8 border border-slate-100 shadow-lg">
                <div class="text-3xl mb-5">🕒</div>
                <h3 class="text-xl font-bold mb-4">Horario</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ $empresa->horario ?? 'Lunes a Viernes 9am - 7pm' }}
                </p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-[#081120] text-white py-16 mt-10">

        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 lg:grid-cols-4 gap-10">

            <div>
                <h3 class="text-3xl font-black mb-5">
                    {{ $empresa->nombre ?? 'TU EMPRESA' }}
                </h3>

                <p class="text-slate-400 leading-relaxed text-sm">
                    Brindamos productos y servicios de calidad con atención personalizada.
                </p>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-5">Enlaces rápidos</h3>

                <ul class="space-y-3 text-slate-400 text-sm">
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#productos">Productos</a></li>
                    <li><a href="#nosotros">Nosotros</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-5">Contáctanos</h3>

                <ul class="space-y-3 text-slate-400 text-sm">
                    <li>{{ $empresa->telefono ?? '987654321' }}</li>
                    <li>{{ $empresa->correo ?? 'correo@empresa.com' }}</li>
                    <li>{{ $empresa->direccion ?? 'Lima, Perú' }}</li>
                </ul>
            </div>

            <div>
                <div class="bg-white/5 border border-white/10 rounded-[28px] p-8 backdrop-blur-md">

                    <h3 class="font-bold text-lg mb-4">
                        Acceso empleados
                    </h3>

                    <p class="text-slate-400 text-sm mb-6">
                        Ingresa al sistema interno de la empresa.
                    </p>

                    <a href="{{ tenant_url('tenant.login', [tenant('id')]) }}"
                        class="block text-center w-full bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-4 rounded-2xl shadow-lg">
                        Ir al sistema →
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 pt-12 mt-12 border-t border-white/10 flex flex-col md:flex-row justify-between gap-4 text-slate-500 text-sm">

            <p>
                © {{ date('Y') }} {{ $empresa->nombre ?? 'Tu Empresa' }}. Todos los derechos reservados.
            </p>

            <p>
                Powered by <span class="text-white font-semibold">Kael</span>
            </p>
        </div>
    </footer>

</body>

</html>
