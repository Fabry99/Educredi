@extends('layouts/main')

@section('titulo_pagina', 'Inicio')

@section('css')
    @vite('resources/css/sidebar.css')
    @vite('resources/css/container.css')
@endsection

@section('sidebar')
    @include('modules.partial.sidebar')
@endsection

@section('contenido')
    @switch($rol)
        @case('contador')
            @include('modules.partial.clientes')
        @break

        @case('administrador')
            @include('modules.partial.prueba_administrador')
        @break

        @case('caja')
            @include('modules.partial.movimientocaja')
        @break

        @default
    @endswitch
@endsection
@section('js')
    @vite('resources/js/app.js')
    @vite('resources/js/notifications.js')

    @if ($rol === 'contador')
        @vite('resources/js/appCaja.js')
    @endif
@endsection
