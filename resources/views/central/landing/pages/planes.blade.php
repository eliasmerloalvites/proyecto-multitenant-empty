@extends('central.landing.layouts.app')

@section('titulo', 'Planes — Kael Tech')

@section('content')
    <div class="pt-24">
        @include('central.landing.sections.como-funciona-pos')
    </div>
    @include('central.landing.sections.planes-pos')
    @include('central.landing.sections.como-funciona-moto')
    @include('central.landing.sections.moto')
    @include('central.landing.sections.footer')
@endsection
