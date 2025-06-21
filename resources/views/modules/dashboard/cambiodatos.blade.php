@extends('layouts/main')

@section('titulo_pagina', 'Cambiar datos de préstamo')

@section('css')
    @vite('resources/css/sidebar.css')
    @vite('resources/css/container.css')
@endsection

@section('sidebar')
    @include('modules.partial.sidebar')
@endsection

@section('contenido')
   @include('modules.partial.cambiardatosprestamos')
@endsection
@section('js')
    @vite('resources/js/app.js')
    @vite('resources/js/cambiardatos.js')
@endsection