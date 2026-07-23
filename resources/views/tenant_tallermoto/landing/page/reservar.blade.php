@extends('tenant_tallermoto.landing.layouts.app')

@section('titulo', 'Kael Tech - Sistema para Talleres')

@section('content')

    @include('tenant_tallermoto.landing.sections.navbar')

    @include('tenant_tallermoto.landing.sections.reservaciones')

    @include('tenant_tallermoto.landing.sections.footer')
    {{-- Próximamente --}}
    {{--  --}}

@endsection