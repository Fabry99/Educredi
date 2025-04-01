<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
@include('modules.modals.modalnuevocentros')
@include('modules.modals.modalnuevogrupos')
@include('modules.modals.modalgrupos')
@include('modules.modals.modalmostrarclientegrupo')
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Mantenimiento de Grupos</h1>
            <div class="btn-grupos" style="display: flex; margin-bottom: 10px; margin-top: 10px; margin-left: 10px;">
                <a href="#" id="openModalBtnnuevocentro" class="btn-agregar" style="margin-right: 15px;"><span>Nuevo
                        Centro</span></a>
                <a href="#" id="openModalBtnnuevogrupo" class="btn-eliminar"><span>Nuevo
                        Grupo</span></a>

            </div>
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
            <div id="custom-alert" class="alert" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; width: 80%; max-width: 400px;">
                <span id="custom-alert-message"></span>
            </div>

            <table id="mitabla" class="table table-striped" style="width:100%">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Centro</th>
                        <th>Asesor</th>
                        <th>Grupos Asignados</th>
                        <th>Fecha Creación</th>


                    </tr>
                </thead>
                <tbody>
                    @foreach ($centros as $centros)
                        <tr>
                            <td>{{ $centros->id }}</td>
                            <td>{{ $centros->nombre }}</td>
                            <td>{{ $centros->asesor->name }}</td> <!-- Muestra directamente el ID del asesor -->
                            <td>
                                @php
                                    $grupo = $gruposPorCentro->firstWhere('id_centros', $centros->id);
                                @endphp
                                @if ($grupo)
                                    {{ $grupo->cantidad_grupos }}
                                @else
                                    0
                                @endif
                            </td>
                            <td>{{ $centros->created_at->format('d/m/Y H:i') }}</td>
                            
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>


    </div>


</div>
