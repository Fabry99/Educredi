@extends('layouts/main')

@section('titulo_pagina', 'Desembolso préstamo')

@section('css')
    @vite('resources/css/sidebar.css')
    @vite('resources/css/container.css')
@endsection

@section('sidebar')
    @include('modules.partial.sidebar')
@endsection

@section('contenido')
   @include('modules.partial.desembolsoprestamos')
@endsection
@section('js')
    @vite('resources/js/appAdmin.js')
    @vite('resources/js/app.js')
@endsection