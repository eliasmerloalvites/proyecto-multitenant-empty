@extends('central.landing.layouts.app')

@section('titulo', 'Crea tu empresa — Kael Tech')

@section('content')

    <section class="relative min-h-screen bg-[#020817] flex items-center py-24 overflow-hidden">

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-32 w-[650px] h-[650px] bg-blue-500/20 blur-[180px] rounded-full"></div>
            <div class="absolute -bottom-40 -right-32 w-[650px] h-[650px] bg-cyan-400/15 blur-[180px] rounded-full"></div>
        </div>

        <div class="relative z-10 max-w-2xl mx-auto px-6 w-full">

            <div class="text-center mb-8">
                <span class="inline-flex items-center gap-2 bg-white/10 border border-white/15 backdrop-blur-xl px-5 py-2 rounded-full text-white text-sm font-semibold mb-6">
                    <i class="fa-solid fa-rocket"></i>
                    Kael Tech
                </span>
                <h1 class="text-3xl md:text-4xl font-black text-white leading-tight">
                    Crea tu empresa
                    <span class="bg-gradient-to-r from-cyan-300 to-blue-400 bg-clip-text text-transparent">en minutos</span>
                </h1>
                <p class="text-slate-300 mt-3">
                    Sin tarjeta de crédito. Empieza a usar tu panel ahora mismo.
                </p>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl p-8">

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-4 mb-6">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('central.registro.store') }}">
                    @csrf

                    {{-- Honeypot anti-bot: invisible para una persona, los bots suelen rellenar todo campo que encuentran. --}}
                    <div style="position:absolute;left:-9999px;top:-9999px;" aria-hidden="true">
                        <label for="website">No llenar este campo</label>
                        <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">¿Qué tipo de negocio manejas?</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="tipo_negocio" value="tallermoto" class="peer sr-only"
                                    {{ old('tipo_negocio', 'tallermoto') === 'tallermoto' ? 'checked' : '' }}>
                                <div class="border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-xl p-4 text-center transition">
                                    <i class="fa-solid fa-motorcycle text-2xl text-blue-600 mb-1"></i>
                                    <div class="font-bold text-slate-900 text-sm">Taller de Motos</div>
                                    <div class="text-slate-400 text-[11px]">Reservas, mantenimientos y bahías</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="tipo_negocio" value="generico" class="peer sr-only"
                                    {{ old('tipo_negocio') === 'generico' ? 'checked' : '' }}>
                                <div class="border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-xl p-4 text-center transition">
                                    <i class="fa-solid fa-boxes-stacked text-2xl text-blue-600 mb-1"></i>
                                    <div class="font-bold text-slate-900 text-sm">Negocio Genérico</div>
                                    <div class="text-slate-400 text-[11px]">Ventas, inventario y compras (POS)</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre de la empresa</label>
                            <input type="text" name="razon_social" value="{{ old('razon_social') }}"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Mi Empresa SAC" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">RUC</label>
                            <input type="text" name="ruc" value="{{ old('ruc') }}" maxlength="11"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="20123456789" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Subdominio de tu empresa</label>
                        <div class="flex items-stretch">
                            <input type="text" name="subdomain" value="{{ old('subdomain') }}"
                                class="flex-1 border border-slate-300 rounded-l-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="miempresa" pattern="[a-z0-9\-]+" required>
                            <span class="inline-flex items-center px-4 bg-slate-100 border border-l-0 border-slate-300 rounded-r-xl text-slate-500 text-sm">
                                .{{ config('app.central_domain') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Así vas a acceder a tu panel: miempresa.{{ config('app.central_domain') }}</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tu correo</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="tucorreo@ejemplo.com" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Contraseña</label>
                            <input type="password" name="password"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Mínimo 8 caracteres" required>
                        </div>
                    </div>

                    <div class="mb-6">
                        <input type="password" name="password_confirmation"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Confirma tu contraseña" required>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Elige tu plan</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach ($planes as $plan)
                                <label class="cursor-pointer">
                                    <input type="radio" name="plan" value="{{ $plan->key }}" class="peer sr-only"
                                        {{ old('plan', $planSeleccionado) === $plan->key ? 'checked' : '' }}>
                                    <div class="border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-xl p-3 text-center transition">
                                        <div class="font-bold text-slate-900 text-sm">{{ $plan->nombre }}</div>
                                        <div class="text-blue-600 font-black text-lg">S/{{ number_format($plan->price, 0) }}</div>
                                        <div class="text-slate-400 text-[11px]">/mes</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-slate-400 mt-2">
                            ¿Necesitas múltiples sucursales o algo a medida?
                            <a href="{{ route('central.inicio') }}#contacto" class="text-blue-600 font-semibold">Habla con nosotros sobre el plan Empresarial</a>.
                        </p>
                    </div>

                    <button type="submit"
                        class="w-full py-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-bold shadow-xl transition">
                        Crear mi empresa ahora
                    </button>

                    <p class="text-center text-xs text-slate-400 mt-4">
                        ¿Ya tienes cuenta? <a href="{{ route('central.login') }}" class="text-blue-600 font-semibold">Inicia sesión</a>
                    </p>

                </form>

            </div>

        </div>

    </section>

@endsection
