@extends('layouts/main')

@section('titulo_pagina', 'Control de Prestamos')

@section('css')
    @vite('resources/css/sidebar.css')
    @vite('resources/css/container.css')
@endsection

@section('sidebar')
    @include('modules.partial.sidebar')
@endsection

@section('contenido')
   @include('modules.partial.controlprestamocontent')
@endsection
@section('js')
    @vite('resources/js/app.js')
    @vite('resources/js/controlprestamo.js')
@endsection