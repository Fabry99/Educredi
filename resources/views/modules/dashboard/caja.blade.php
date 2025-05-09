@extends('layouts/main')

@section('titulo_pagina', 'Movimientos de caja')

@section('css')
    @vite('resources/css/sidebar.css')
    @vite('resources/css/container.css')
@endsection

@section('sidebar')
    @include('modules.partial.sidebar')
@endsection

@section('contenido')
   @include('modules.partial.movimientocaja')
@endsection
@section('js')
    @vite('resources/js/app.js')
@endsection