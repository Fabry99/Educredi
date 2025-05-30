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
                            $created_at = '';
                            $updated_at = '';

                            if (!empty($datos['created_at'])) {
                                $created_at = \Carbon\Carbon::parse($datos['created_at'])
                                    ->timezone('America/Guatemala')
                                    ->format('d/m/Y H:i:s');
                            } elseif (!empty($datos['cambios']['created_at'])) {
                                $created_at = \Carbon\Carbon::parse($datos['cambios']['created_at'])
                                    ->timezone('America/Guatemala')
                                    ->format('d/m/Y H:i:s');
                            }

                            if (!empty($datos['updated_at'])) {
                                $updated_at = \Carbon\Carbon::parse($datos['updated_at'])
                                    ->timezone('America/Guatemala')
                                    ->format('d/m/Y H:i:s');
                            } elseif (!empty($datos['cambios']['updated_at'])) {
                                $updated_at = \Carbon\Carbon::parse($datos['cambios']['updated_at'])
                                    ->timezone('America/Guatemala')
                                    ->format('d/m/Y H:i:s');
                            }
                        @endphp

                        <tr>
                            <td hidden>{{ $item->id }}</td>
                            <td>{{ strtoupper(optional($item->user)->name ?? '') }}
                                {{ strtoupper(optional($item->user)->last_name ?? '') }}</td>
                            <td>{{ strtoupper($item->tabla_afectada) }}</td>
                            <td>{{ $item->accion }}</td>
                            <td>
                                <ul>
                                    @foreach ($datos as $campo => $valor)
                                        @if (is_array($valor))
                                            <li>
                                                <strong>{{ ucfirst(str_replace('_', ' ', $campo)) }}:</strong>
                                                <ul>
                                                    @foreach ($valor as $subCampo => $subValor)
                                                        <li><strong>{{ ucfirst(str_replace('_', ' ', $subCampo)) }}:</strong>
                                                            {{ $subValor }}</li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @else
                                            <li><strong>{{ ucfirst(str_replace('_', ' ', $campo)) }}:</strong>
                                                {{ $valor }}</li>
                                        @endif
                                    @endforeach

                                    @if (!empty($created_at))
                                        <li><strong>Fecha de Creación:</strong> {{ $created_at }}</li>
                                    @endif

                                    @if (!empty($updated_at))
                                        <li><strong>Fecha de Actualización:</strong> {{ $updated_at }}</li>
                                    @endif
                                </ul>
                            </td>

                            <td>
                                {{ $created_at ?: ($updated_at ?: \Carbon\Carbon::parse($item->fecha)->timezone('America/Guatemala')->format('d/m/Y H:i:s')) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
