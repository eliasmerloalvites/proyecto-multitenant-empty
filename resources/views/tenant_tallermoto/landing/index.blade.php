@extends('tenant_tallermoto.landing.layouts.app')

@section('titulo', 'Kael Tech - Sistema para Talleres')

@section('content')

    @include('tenant_tallermoto.landing.sections.navbar')

    @include('tenant_tallermoto.landing.sections.hero')

    {{-- @include('tenant_tallermoto.landing.sections.problems') --}}
    @include('tenant_tallermoto.landing.sections.solution')
    @include('tenant_tallermoto.landing.sections.workflow')
    @include('tenant_tallermoto.landing.sections.benefits')
    @include('tenant_tallermoto.landing.sections.catalogo')
    @include('tenant_tallermoto.landing.sections.nosotros')
    @include('tenant_tallermoto.landing.sections.contacto')
    @include('tenant_tallermoto.landing.sections.footer')
    {{-- Próximamente --}}
    {{--  --}}

@endsection