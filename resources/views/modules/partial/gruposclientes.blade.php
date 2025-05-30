<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
@include('modules.modals.modalnuevocentros')
@include('modules.modals.modalnuevogrupos')
@include('modules.modals.modalgrupos')
@include('modules.modals.modalmostrarclientegrupo')
@include('modules.modals.modaleditarcentro')
<form id="eliminar-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="id" id="centro-id" value="">
</form>
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Mantenimiento de Grupos</h1>
            @if (Auth::check() && (Auth::user()->rol === 'administrador' || Auth::user()->rol === 'contador'))
            <div class="btn-grupos" style="display: flex; margin-bottom: 10px; margin-top: 10px; margin-left: 10px;">
                <a href="#" id="openModalBtnnuevocentro" class="btn-agregar"
                    style="margin-right: 15px;"><span>Nuevo
                        Centro</span></a>
                <a href="#" id="openModalBtnnuevogrupo" class="btn-eliminar"><span>Nuevo
                        Grupo</span></a>
            </div>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="notification custom_error">
                        {{ $error }}
                    </div>
                @endforeach
            @endif

            @if (session('success'))
                <div class="notification custom_success">
                    {{ session('success') }}
                </div>
            @endif
            <div id="custom-alert" class="alert"
                style="display:none; position: fixed; top: 90px; right: 20px; z-index: 9999; padding: 15px; width: 80%; max-width: 400px;">
                <span id="custom-alert-message">Hola</span>
            </div>


            <table id="tablacentros" class="table table-striped " style="width:100%">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Centro</th>
                        <th>Asesor</th>
                        <th>Grupos Asignados</th>
                        <th>Fecha Creación</th>
                        @if (Auth::check() && Auth::user()->rol === 'administrador')
                            <th>Botones</th>
                        @endif

                    </tr>
                </thead>
                <tbody>
                    @foreach ($centros as $centros)
                        <tr>
                            <td>{{ $centros->id }}</td>
                            <td>{{ $centros->nombre }}</td>
                            <td>{{ $centros->asesor->name }}</td> <!-- Muestra directamente el ID del asesor -->
                            <!-- Aquí obtenemos la cantidad de grupos desde $contar -->
                            @php
                                $grupo = $contar->firstWhere('id', $centros->id);
                                $cantidadGrupos = $grupo ? $grupo->grupos_count : 0;
                            @endphp

                            <td>{{ $cantidadGrupos }}</td> <!-- Mostramos el conteo de grupos -->

                            <td>{{ $centros->created_at->format('d/m/Y H:i') }}</td>

                            @if (Auth::check() && Auth::user()->rol === 'administrador')
                                <!-- Mostrar el botón de eliminar solo si el conteo de grupos es 0 -->

                                <td>
                                    <button type="button" class="btn-editar-centro btn-editar"
                                        id="btn-editarcentro-{{ $centros->id }}" data-id="{{ $centros->id }}">
                                        <img src="{{ asset('img/icon-editar.svg') }}" alt="">
                                    </button>
                                    @if ($cantidadGrupos == 0)
                                        <form action="{{ route('centros.eliminar', $centros->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-eliminar-centro btn-eliminar"><img
                                                    src="{{ asset('img/icon-eliminar.svg') }}" alt=""></button>
                                        </form>
                                   
                            @endif
                            </td>
                    @endif
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>


    </div>


</div>
