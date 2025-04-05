<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Usuarios</h1>
            <div class="btn-clientes">
                <a href="" id="openModalBtn" style="width: 170px"><img src="{{ asset('img/icon-clientes.svg') }}"
                        alt=""><span>Agregar Usuario</span></a>


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

            <table id="tablaBitacora" class="table table-striped" style="width:100%">

                <thead>
                    <tr>
                        <th>ID</th>
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
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }} {{ $item->last_name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>
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
                    {{-- @foreach ($bitacora as $item)
                        @php
                            // Decodificar el JSON de la columna 'datos'
                            $datos = json_decode($item->datos, true);

                            // formatear fechas para hacerlas legibles
                            $created_at = \Carbon\Carbon::parse($datos['created_at'] ?? '')
                                ->timezone('America/Guatemala')
                                ->format('d/m/Y H:i:s');
                            $updated_at = \Carbon\Carbon::parse($datos['updated_at'] ?? '')
                                ->timezone('America/Guatemala')
                                ->format('d/m/Y H:i:s');
                        @endphp

                        <tr>
                            <td>{{ strtoupper(optional($item->user)->name ?? '') }} {{ strtoupper(optional($item->user)->last_name ?? '') }}</td>
                            <td>{{ strtoupper($item->tabla_afectada) }}</td>
                            <td>{{ $item->accion }}</td>
                            <td>
                                <ul>
                                    <li><strong>Nombre:</strong> {{ $datos['nombre'] ?? 'No disponible' }}</li>
                                    <li><strong>ID Asesor:</strong> {{ $datos['id_asesor'] ?? 'No disponible' }}</li>
                                    <li><strong>Fecha de Creación:</strong> {{ $created_at }}</li>
                                    <li><strong>Fecha de Actualización:</strong> {{ $updated_at }}</li>
                                </ul>
                            </td>
                            <td>{{ $created_at }}</td>
                        </tr>
                    @endforeach --}}

                </tbody>

            </table>
        </div>


    </div>


</div>
