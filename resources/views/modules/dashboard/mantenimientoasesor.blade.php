@extends('layouts/main')

@section('titulo_pagina', 'Mantenimiento')

@section('css')
    @vite('resources/css/sidebar.css')
    @vite('resources/css/container.css')
@endsection

@section('sidebar')
    @include('modules.partial.sidebar')
@endsection

@section('contenido')
   @include('modules.partial.manteasesor')
@endsection
@section('js')
    @vite('resources/js/app.js')
@endsection