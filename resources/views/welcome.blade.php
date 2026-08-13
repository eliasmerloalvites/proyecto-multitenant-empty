@extends('central.landing.layouts.app')

@section('titulo', 'Kael - Plataforma SaaS')

@section('content')
    @include('central.landing.sections.hero')
    @include('central.landing.sections.soluciones')
    @include('central.landing.sections.planes-pos')
    @include('central.landing.sections.moto')
    @include('central.landing.sections.clientes')
    @include('central.landing.sections.footer')
@endsection
