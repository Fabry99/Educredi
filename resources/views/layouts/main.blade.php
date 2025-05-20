<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('titulo_pagina')</title>
    <link rel="icon" href="{{ asset('img/logoeducredi.jpeg') }}" type="image/x-icon">
    @yield('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">


</head>

<body>
    @yield('sidebar')
    @yield('contenido')
</body>
{{-- @vite(['resources/js/app.js']) --}}
@yield('js')

</html>
