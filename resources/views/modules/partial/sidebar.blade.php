@include('modules.modals.modalarchivinfored');
@include('modules.modals.modalcolocacionprestamomes');
@include('modules.modals.modalmutuoacuerdogrupal');
@include('modules.modals.modalmutuoindividual');
@include('modules.modals.modalActualizacionllave');
{{-- @php
    dd(Auth::user());
@endphp --}}

<header>
    <nav class="navbar">
        <div class="contenerdor-navbar">
            <div class="personal-name">
                <img class="icono-usuario" src="{{ asset('img/icon_personal.svg') }}" alt="">
                <span class="nombre-usuario">
                    @if (!empty(Auth::user()->name || !empty(Auth::user()->lastname)))
                        {{ Auth::user()->name }}
                        {{ Auth::user()->last_name }}
                    @else
                        Invitado
                    @endif
                </span>
            </div>
        </div>
    </nav>
</header>

<div class="sidebar">
    <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" class="logo-img">
    <h3 class="logo-text">EDUCREDI RURAL
        S.A DE C.V</h3>
    <hr class="separate-line">


    <ul class="menu">
        <!-- Menú principal que aparece para todos los usuarios -->

        <!-- Menú exclusivo para el administrador -->
        @if (Auth::check())
            @switch(Auth::user()->rol)
                @case('administrador')
                    <li class="menu-item menu-item-static"><a href="{{ route('home') }}" class="menu-link"><img
                                src="{{ asset('img/icon-users.svg') }}" alt="">
                            <span>Usuarios</span></a></li>
                    <li class="menu-item menu-item-static"><a href="{{ route('admin.clientes') }}" class="menu-link"><img
                                src="{{ asset('img/icon-clientes.svg') }}" alt=""><span>Clientes</span></a></li>

                    <li class="menu-item menu-item-static"> <a href="{{ route('bitacora') }}" class="menu-link"><img
                                src="{{ asset('img/icon-bitacora.svg') }}" alt="">
                            <span>Bitacora</span></a>
                    </li>

                    <li class="menu-item menu-item-dropdown">
                        <a href="javascript:void(0);" class="menu-link"><img src="{{ asset('img/icon-prestamo.svg') }}"
                                alt=""><span>Prestamos</span> <span class="arrow">></span></a>

                        <ul class="sub-menu">
                            <li><a href="{{ route('grupos') }}" class="sub-menu-link">Grupos</a></li>
                            <li><a href="{{ route('creditos') }}" class="sub-menu-link">Desembolso de préstamos</a></li>

                        </ul>
                    </li>
                    <li class="menu-item menu-item-dropdown">
                        <a href="javascript:void(0);" class="menu-link"><img src="{{ asset('img/icon-tecnico.svg') }}"
                                alt=""><span>Tecnico</span> <span class="arrow">></span></a>

                        <ul class="sub-menu">
                            <li><a href="{{ route('reverliquidacion') }}" class="sub-menu-link">Revertir desembolso de
                                    prestamo</a></li>
                            <li><a href="{{ route('transferenciadecartera') }}" class="sub-menu-link">Transferencia de cartera
                                    entre asesores</a></li>
                        </ul>
                    </li>
                    <li class="menu-item menu-item-static"><a href="{{ route('caja') }}" class="menu-link"><img
                                src="{{ asset('img/icon-caja.svg') }}" alt=""><span>Caja</span></a>
                    </li>
                    <li class="menu-item menu-item-static"><a href="{{ route('control') }}" class="menu-link"><img
                                style="align-self: center" src="{{ asset('img/icon_controlprestamos.svg') }}"
                                alt=""><span>Control de Préstamos</span></a>
                    </li>
                    <li class="menu-item menu-item-static" style="font-size: 15px;"><a href="{{ route('reversion') }}"
                            class="menu-link"><img style="align-self: center;" src="{{ asset('img/icon_reversioncaja.svg') }}"
                                alt=""><span>Revertir movimientos <br> de caja<span></a>
                    </li>
                    <li class="menu-item menu-item-static" style="font-size: 15px;"><a href="" id="btn_llave"
                            class="menu-link"><img style="align-self: center;" src="{{ asset('img/icon_key.svg') }}"
                                alt=""><span>Llave para Usuarios<span></a>
                    </li>

                    <li class="menu-item menu-item-dropdown">
                        <a href="javascript:void(0);" class="menu-link"><img src="{{ asset('img/icon_reportes.svg') }}"
                                alt=""><span>Reportes</span> <span class="arrow">></span></a>

                        <ul class="sub-menu">
                            <li><a href="" id="archivoinfored" class="sub-menu-link">Archivo de INFORED</a></li>
                            <li><a href="" class="sub-menu-link" id="btn_colocacionprestamos">Colocación de préstamos por
                                    mes</a></li>
                            <li><a href="" class="sub-menu-link" id="btn_mutuogrupal">Mutuo Acuerdo Grupal</a></li>
                            <li><a href="" class="sub-menu-link" id="btn_mutuoindividual">Mutuo Acuerdo Individual</a>
                            </li>


                        </ul>
                    </li>
                @break

                @case('caja')
                    <li class="menu-item menu-item-static"><a href="{{ route('caja') }}" class="menu-link"><img
                                src="{{ asset('img/icon-caja.svg') }}" alt=""><span>Caja</span></a>
                    </li>
                    <li class="menu-item menu-item-static"><a href="{{ route('grupos') }}" class="menu-link"><img
                                src="{{ asset('img/icon-grupo.svg') }}" alt=""><span>Grupos</span></a>
                    </li>
                    <li class="menu-item menu-item-static"><a href="{{ route('control') }}" class="menu-link"><img
                                style="align-self: center" src="{{ asset('img/icon_controlprestamos.svg') }}"
                                alt=""><span>Control de Préstamos</span></a>
                    </li>

                    <li class="menu-item menu-item-static" style="font-size: 15px;"><a href="{{ route('reversion') }}"
                            class="menu-link"><img style="align-self: center;"
                                src="{{ asset('img/icon_reversioncaja.svg') }}" alt=""><span>Revertir movimientos
                                <br> de caja<span></a>
                    </li>
                    <li class="menu-item menu-item-dropdown">
                        <a href="javascript:void(0);" class="menu-link"><img src="{{ asset('img/icon_reportes.svg') }}"
                                alt=""><span>Reportes</span> <span class="arrow">></span></a>

                        <ul class="sub-menu">
                            <li><a href="" id="archivoinfored" class="sub-menu-link">Archivo de INFORED</a></li>
                            <li><a href="" class="sub-menu-link" id="btn_colocacionprestamos">Colocación de préstamos
                                    por
                                    mes</a></li>
                            <li><a href="" class="sub-menu-link" id="btn_mutuogrupal">Mutuo Acuerdo Grupal</a></li>
                            <li><a href="" class="sub-menu-link" id="btn_mutuoindividual">Mutuo Acuerdo Individual</a>
                            </li>


                        </ul>
                    </li>
                @break

                @case('contador')
                    <li class="menu-item menu-item-static"><a href="{{ route('contador') }}" class="menu-link"><img
                                src="{{ asset('img/icon-clientes.svg') }}" alt=""><span>Clientes</span></a>
                    </li>
                    <li class="menu-item menu-item-dropdown">
                        <a href="javascript:void(0);" class="menu-link"><img src="{{ asset('img/icon-prestamo.svg') }}"
                                alt=""><span>Prestamos</span> <span class="arrow">></span></a>

                        <ul class="sub-menu">
                            <li><a href="{{ route('grupos') }}" class="sub-menu-link">Grupos</a></li>
                            <li><a href="{{ route('creditos') }}" class="sub-menu-link">Desembolso de préstamos</a></li>
                            <li><a href="{{ route('cambiardatos') }}" class="sub-menu-link">Cambiar datos de prestamo</a>
                            </li>

                        </ul>
                    </li>

                    <li class="menu-item menu-item-static"><a href="{{ route('mantenimientoAsesores') }}"
                            class="menu-link"><img src="{{ asset('img/icon-mantenimiento.svg') }}" alt="">
                            <span>Mantenimiento de asesor</span></a></li>


                    <li class="menu-item menu-item-dropdown">
                        <a href="javascript:void(0);" class="menu-link"><img src="{{ asset('img/icon-tecnico.svg') }}"
                                alt=""><span>Tecnico</span> <span class="arrow">></span></a>

                        <ul class="sub-menu">
                            <li><a href="{{ route('reverliquidacion') }}" class="sub-menu-link">Revertir desembolso de
                                    prestamo</a></li>
                            <li><a href="{{ route('transferenciadecartera') }}" class="sub-menu-link">Transferencia de
                                    cartera
                                    entre asesores</a></li>
                        </ul>
                    </li>
                    <li class="menu-item menu-item-dropdown">
                        <a href="javascript:void(0);" class="menu-link"><img src="{{ asset('img/icon_reportes.svg') }}"
                                alt=""><span>Reportes</span> <span class="arrow">></span></a>

                        <ul class="sub-menu">
                            <li><a href="" id="archivoinfored" class="sub-menu-link">Archivo de INFORED</a></li>
                            <li><a href="" class="sub-menu-link" id="btn_colocacionprestamos">Colocación de préstamos
                                    por
                                    mes</a></li>
                            <li><a href="" class="sub-menu-link" id="btn_mutuogrupal">Mutuo Acuerdo Grupal</a></li>
                            <li><a href="" class="sub-menu-link" id="btn_mutuoindividual">Mutuo Acuerdo Individual</a>
                            </li>


                        </ul>
                    </li>
                @break
            @endswitch
        @endif

    </ul>
    <li class="menu-item menu-item-static" id="cerrarsesion">
        <hr class="separate-line">

        <a class="menu-link" href="{{ route('logout') }}">
            <img src="{{ asset('img/icon-cerrarsesion.svg') }}" alt=""><span>Cerrar Sesión</span>
        </a>
    </li>
</div>
