@extends('central.landing.layouts.app')

@section('titulo', 'Confirma tu correo — Kael Tech')

@section('content')

    <section class="relative min-h-screen bg-[#020817] flex items-center py-24 overflow-hidden">

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-32 w-[650px] h-[650px] bg-blue-500/20 blur-[180px] rounded-full"></div>
            <div class="absolute -bottom-40 -right-32 w-[650px] h-[650px] bg-cyan-400/15 blur-[180px] rounded-full"></div>
        </div>

        <div class="relative z-10 max-w-lg mx-auto px-6 w-full text-center">

            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-3xl mx-auto mb-6 shadow-xl">
                <i class="fa-solid fa-envelope-circle-check"></i>
            </div>

            <h1 class="text-3xl font-black text-white mb-3">Revisa tu correo 📩</h1>

            <p class="text-slate-300 mb-2 leading-7">
                Te mandamos un enlace de confirmación
                @if ($email)
                    a <span class="text-white font-semibold">{{ $email }}</span>
                @endif
                . Ábrelo para crear tu cuenta y tu panel.
            </p>

            <p class="text-slate-500 text-sm mt-6">
                El enlace vence en 30 minutos. Si no lo ves, revisa tu carpeta de spam.
            </p>

        </div>

    </section>

@endsection
