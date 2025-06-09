@extends('layouts/main')

@section('titulo_pagina', 'Reversión de prestamos')

@section('css')
    @vite('resources/css/sidebar.css')
    @vite('resources/css/container.css')
@endsection

@section('sidebar')
    @include('modules.partial.sidebar')
@endsection

@section('contenido')
   @include('modules.partial.reverliquidacion')
@endsection
@section('js')
    @vite('resources/js/app.js')
    @vite('resources/js/appAdmin.js')
@endsection