@extends('central.landing.layouts.app')

@section('titulo', 'Soluciones — Kael Tech')

@section('content')
    <div class="pt-24">
        @include('central.landing.sections.soluciones')
    </div>
    @include('central.landing.sections.footer')
@endsection
