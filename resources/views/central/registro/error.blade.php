@extends('central.landing.layouts.app')

@section('titulo', 'No pudimos confirmar tu registro — Kael Tech')

@section('content')

    <section class="relative min-h-screen bg-[#020817] flex items-center py-24 overflow-hidden">

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-32 w-[650px] h-[650px] bg-red-500/10 blur-[180px] rounded-full"></div>
        </div>

        <div class="relative z-10 max-w-lg mx-auto px-6 w-full text-center">

            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white text-3xl mx-auto mb-6 shadow-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <h1 class="text-2xl font-black text-white mb-3">No pudimos confirmar tu registro</h1>

            <p class="text-slate-300 mb-8 leading-7">
                {{ $mensaje }}
            </p>

            <a href="{{ route('central.registro.show') }}"
                class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-bold shadow-xl transition">
                Volver a intentar
            </a>

        </div>

    </section>

@endsection
