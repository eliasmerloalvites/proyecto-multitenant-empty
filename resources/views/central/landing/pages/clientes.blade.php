@extends('central.landing.layouts.app')

@section('titulo', 'Clientes — Kael Tech')

@section('content')
    <div class="pt-24">
        @include('central.landing.sections.clientes')
    </div>
    @include('central.landing.sections.footer')
@endsection
