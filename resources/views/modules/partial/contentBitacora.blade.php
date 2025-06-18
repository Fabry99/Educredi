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
            <div id="alert-notification" class="alert"
                style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; width: 80%; max-width: 400px; font-size: 16px; text-align: center; border-radius: 5px;">
                <span id="alert-notification-message"></span>
            </div>
            <table id="tablaBitacora" class="table table-striped table1" style="width:100%">
                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th>Usuario</th>
                        <th>Tabla Afectada</th>
                        <th>Acción Realizada</th>
                        <th>Datos Afectados</th>
                        <th>Comentarios</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bitacora as $item)
                        @php
                            // No decodificamos 'datos' como JSON, lo mostramos tal cual está.

                            // Si quieres seguir mostrando created_at y updated_at desde el JSON decodificado,
                            // puedes hacerlo así (opcional):
                            $datosJson = json_decode($item->datos, true);

                            $created_at = '';
                            $updated_at = '';

                            if (!empty($datosJson['created_at'])) {
                                $created_at = \Carbon\Carbon::parse($datosJson['created_at'])
                                    ->timezone('America/Guatemala')
                                    ->format('d/m/Y H:i:s');
                            } elseif (!empty($datosJson['cambios']['created_at'])) {
                                $created_at = \Carbon\Carbon::parse($datosJson['cambios']['created_at'])
                                    ->timezone('America/Guatemala')
                                    ->format('d/m/Y H:i:s');
                            }

                            if (!empty($datosJson['updated_at'])) {
                                $updated_at = \Carbon\Carbon::parse($datosJson['updated_at'])
                                    ->timezone('America/Guatemala')
                                    ->format('d/m/Y H:i:s');
                            } elseif (!empty($datosJson['cambios']['updated_at'])) {
                                $updated_at = \Carbon\Carbon::parse($datosJson['cambios']['updated_at'])
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

                            {{-- Mostrar el texto plano de 'datos' respetando saltos de línea --}}
                            <td style="white-space: pre-wrap;">{{ $item->datos }}</td>
                            <td>
                                @if (empty($item->comentarios))
                                    <span style="color: #999;">Sin comentarios</span>
                                @else
                                    <span style="color: #000;">{{ $item->comentarios }}</span>
                                @endif
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
