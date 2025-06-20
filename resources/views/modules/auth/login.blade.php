@extends('layouts/main')

@section('titulo_pagina', 'Iniciar Sesión')
@section('css')
    <!-- Cargar solo el CSS de la vista home -->
    @vite(['resources/css/login.css'])

@section('contenido')
    <div class="container">
        <h1 class="">EDUCREDI RURAL S.A DE C.V</h1>
    </div>

    <div class="container-login">
        <div class="login">
            <div class="login-image">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="User Icon" ">
            </div>
            <div class="login-form">
                <div class="titulo_login">


                    <!-- Mostrar mensaje de error -->
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <h2>Iniciar Sesión</h2>
                </div>
                <form action="{{ route('loggear') }}" method="POST">
                    @csrf
                    <div class="user">
                        <div class="input-group">
                            <label for="email">
                                <span class="icon">📧</span>
                                <input type="email" id="email" name="email" placeholder="Email" required>
                            </label>
                            @error('email')
                                <div class="custom_error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group">
                            <label for="password">
                                <span class="icon">🔒</span>
                                <input type="password" id="password" name="password" placeholder="Password" required>
                            </label>
                            @error('password')
                                <div class="custom_error">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="login-button">LOGIN</button>
                    </div>
                </form>


            </div>
        </div>
    </div>


@endsection
