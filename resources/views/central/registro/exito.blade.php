@extends('central.landing.layouts.app')

@section('titulo', '¡Listo! — Kael Tech')

@section('content')

    <section class="relative min-h-screen bg-[#020817] flex items-center py-24 overflow-hidden">

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-32 w-[650px] h-[650px] bg-blue-500/20 blur-[180px] rounded-full"></div>
            <div class="absolute -bottom-40 -right-32 w-[650px] h-[650px] bg-cyan-400/15 blur-[180px] rounded-full"></div>
        </div>

        <div class="relative z-10 max-w-lg mx-auto px-6 w-full text-center">

            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-3xl mx-auto mb-6 shadow-xl">
                <i class="fa-solid fa-check"></i>
            </div>

            <h1 class="text-3xl font-black text-white mb-3">¡Tu taller ya está listo! 🎉</h1>

            <p class="text-slate-300 mb-8 leading-7">
                Creamos tu cuenta y tu panel de administración. Ya puedes ingresar con el
                correo y la contraseña que registraste.
            </p>

            <a href="{{ $urlPanel }}"
                class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-bold shadow-xl transition">
                Ir a mi panel
                <i class="fa-solid fa-arrow-right"></i>
            </a>

            <p class="text-slate-500 text-sm mt-6">
                O guarda este enlace para más tarde: <span class="text-slate-300">{{ $urlPanel }}</span>
            </p>

        </div>

    </section>

@endsection
