@extends('layouts/main')

@section('titulo_pagina', 'Bitacora')

@section('css')
    @vite('resources/css/sidebar.css')
    @vite('resources/css/container.css')
    @vite('resources/css/cssAdministracion.css')
@endsection

@section('sidebar')
    @include('modules.partial.sidebar')
@endsection

@section('contenido')
@include('modules.partial.contentBitacora')
@endsection
@section('js')
    @vite('resources/js/appAdmin.js')
@endsection
