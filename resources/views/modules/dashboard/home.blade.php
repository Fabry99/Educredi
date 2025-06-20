@extends('layouts/main')

@section('titulo_pagina', 'Inicio')

@section('css')
    @vite('resources/css/sidebar.css')
    @vite('resources/css/container.css')
@endsection

@section('sidebar')
    @include('modules.partial.sidebar')
    @if ($rol === 'administrador')
        @vite('resources/css/cssAdministracion.css')
    @endif
@endsection

@section('contenido')
    @switch($rol)
        @case('contador')
            @include('modules.partial.clientes')
        @break

        @case('administrador')
            @include('modules.partial.contentUsuarios')
        @break

        @case('caja')
            @include('modules.partial.movimientocaja')
        @break

        @default
    @endswitch
@endsection
@section('js')
    @vite('resources/js/app.js')
    @if ($rol === 'administrador')
        @vite('resources/js/appAdmin.js')
    @endif
    @vite('resources/js/notifications.js')

    @if ($rol === 'caja')
        @vite('resources/js/appCaja.js')
    @endif
@endsection
