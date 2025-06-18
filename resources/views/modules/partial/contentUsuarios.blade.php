<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
@include('modules.modals.modalnuevouser')
@include('modules.modals.modaleditaruser')
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Usuarios</h1>
            <div class="btn-clientes">
                <a href="" id="openModalBtnnuevousuario" style="width: 170px"><img
                        src="{{ asset('img/icon-clientes.svg') }}" alt=""><span>Agregar Usuario</span></a>


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
            <div id="custom-alert" class="alert"
                style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; width: 80%; max-width: 400px;">
                <span id="custom-alert-message"></span>
            </div>
            <div id="alert-notification" class="alert"
                style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; width: 80%; max-width: 400px; font-size: 16px; text-align: center; border-radius: 5px;">
                <span id="alert-notification-message"></span>
            </div>

            <table id="tablaUsuarios" class="table table-striped table1" style="width:100%">

                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Rol</th>
                        <th>Inicio de Sesión</th>
                        <th>Ultima Conexión</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $item)
                        <tr>
                            <td hidden>{{ $item->id }}</td>
                            <td>{{ $item->name }} {{ $item->last_name }}</td>
                            <td>{{ $item->email }}</td>
                            <td style="text-align: center">
                                {{ $item->fecha_nacimiento == '0000-00-00' || !$item->fecha_nacimiento ? '-' : \Carbon\Carbon::parse($item->fecha_nacimiento)->format('d/m/Y') }}
                            </td>
                            <td>{{ $item->rol }}</td>
                            <td>
                                {{ optional($item->latestSession)->started_at
                                    ? \Carbon\Carbon::parse($item->latestSession->started_at)->setTimezone('America/Guatemala')->format('Y-m-d H:i:s')
                                    : '-' }}
                            </td>
                            <td>
                                {{ optional($item->latestSession)->ended_at
                                    ? \Carbon\Carbon::parse($item->latestSession->ended_at)->setTimezone('America/Guatemala')->format('Y-m-d H:i:s')
                                    : '-' }}
                            </td>
                            <td>
                                <span
                                    class="estados {{ $item->estado == 'activo' ? 'estado-activo' : ($item->estado == 'inactivo' ? 'estado-inactivo' : '') }}">
                                    {{ $item->estado }}
                                </span>
                            </td>

                            <td>
                                {{ $item->created_at == '0000-00-00' ||
                                !$item->created_at ||
                                !\Carbon\Carbon::hasFormat($item->created_at, 'Y-m-d H:i:s')
                                    ? '-'
                                    : \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') }}
                            </td>


                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>


    </div>


</div>
