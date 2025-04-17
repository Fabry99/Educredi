<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">

<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Bitácora de Personal</h1>

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

            <table id="tablaBitacora" class="table table-striped table1" style="width:100%">
                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th>Usuario</th>
                        <th>Tabla Afectada</th>
                        <th>Acción Realizada</th>
                        <th>Datos Afectados</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bitacora as $item)
                        @php
                            // Decodificar el JSON de la columna 'datos'
                            $datos = json_decode($item->datos, true);

                            // Formatear fechas si existen
                            $created_at = !empty($datos['created_at'])
                                ? \Carbon\Carbon::parse($datos['created_at'])->timezone('America/Guatemala')->format('d/m/Y H:i:s')
                                : '';

                            $updated_at = !empty($datos['updated_at'])
                                ? \Carbon\Carbon::parse($datos['updated_at'])->timezone('America/Guatemala')->format('d/m/Y H:i:s')
                                : '';
                        @endphp

                        <tr>
                            <td hidden>{{ $item->id }}</td>
                            <td>{{ strtoupper(optional($item->user)->name ?? '') }} {{ strtoupper(optional($item->user)->last_name ?? '') }}</td>
                            <td>{{ strtoupper($item->tabla_afectada) }}</td>
                            <td>{{ $item->accion }}</td>
                            <td>
                                <ul>
                                    @if ($item->accion === 'UPDATE')
                                        @foreach ($datos as $campo => $valor)
                                            <li><strong>{{ ucfirst(str_replace('_', ' ', $campo)) }}:</strong> {{ $valor }}</li>
                                        @endforeach
                                    @else
                                        <li><strong>Nombre:</strong> {{ $datos['nombre'] ?? 'No disponible' }}</li>
                                        <li><strong>ID Asesor:</strong> {{ $datos['id_asesor'] ?? 'No disponible' }}</li>
                                    @endif

                                    @if (!empty($created_at))
                                        <li><strong>Fecha de Creación:</strong> {{ $created_at }}</li>
                                    @endif

                                    @if (!empty($updated_at))
                                        <li><strong>Fecha de Actualización:</strong> {{ $updated_at }}</li>
                                    @endif
                                </ul>
                            </td>
                            <td>{{ $created_at ?: $updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
