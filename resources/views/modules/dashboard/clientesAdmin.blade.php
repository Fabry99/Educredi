@extends('layouts/main')

@section('titulo_pagina', 'Clientes')

@section('css')
    @vite('resources/css/sidebar.css')
    @vite('resources/css/container.css')
@endsection

@section('sidebar')
    @include('modules.partial.sidebar')
@endsection

@section('contenido')
@include('modules.partial.clientes')
    {{-- @switch($rol)
        @case('contador')
            @include('modules.partial.clientes')
            @break
        @case('administrador')
            @include('modules.partial.prueba_administrador')
            
            @break
        @case('caja')
            @include('modules.partial.prueba_caja')
            @break
        @default
    @endswitch --}}
@endsection
@section('js')
    @vite('resources/js/app.js')
    @vite('resources/js/notifications.js')
@endsection