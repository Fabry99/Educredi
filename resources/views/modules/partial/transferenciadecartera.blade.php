<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
<div class="container">
    <div class="main-content">

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
        <div id="alert-notification" class="alert"
            style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; width: 80%; max-width: 400px; font-size: 16px; text-align: center; border-radius: 5px;">
            <span id="alert-notification-message"></span>
        </div>

        <div class="contenedor">
            <div class="tittle">
                <h2 style="margin-bottom: 40px">Transferencia de cartera entre asesores</h2>
            </div>
            <div class="contain-option">
                <div class="option">
                    <span>Asesor con la Cartera:</span>
                    <select name="asesorcartera" id="asesorcartera">
                        <option value="" disabled selected>Seleccionar:</option>
                        @foreach ($asesor as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="option">
                    <span>Centro:</span>
                    <select name="Centro" id="centro_id">
                        <option value="" disabled selected>Seleccionar:</option>
                        @foreach ($centro as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="option">
                    <span>Grupo:</span>
                    <select name="Grupo" id="grupo_id">
                        <option value="" disabled selected>Seleccionar:</option>
                    </select>
                </div>
            </div>
            <div class="contenedor-conteo">
                <div class="conteo">
                    <span>Número de Creditos:</span>
                    <input type="numer" name="creditos" id="creditos" readonly style="text-align: right; padding-right:5px">
                </div>
                <div class="conteo">
                    <span>Monto Total de los Creditos:</span>
                    <input type="numer" name="MONTO" id="monto_total" readonly style="text-align: right">
                </div>
            </div>
            <div class="tabla">
                <table id="tablaTransCartera" class="table table-striped table1" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 40px;"> <!-- Aseguramos que la columna tenga un ancho específico -->
                                <input type="checkbox" id="selectAllCheckbox">
                            </th>
                            <th >id</th>
                            <th>Préstamo</th>
                            <th>Nombre del cliente</th>
                            <th>Apertura</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
            <div class="contenedor-footer">
                <div class="option">
                    <span>Asesor a Transferir:</span>
                    <select name="asesorcartera" id="asesorcartea">
                        <option value="">1</option>
                        <option value="">2</option>
                    </select>
                </div>
                {{-- <div class="botones">
                    <button type="submit" class="btn-aceptar" id="btnAceptar"><img src="{{ asset('img/aceptar.svg') }}"
                            alt=""></button>
                </div> --}}
                <div class="botones">
                    <button type="submit" class="btn-guardar" id="btnGuardarTransferencia">
                        <img src="{{ asset('img/aceptar.svg') }}" alt="">
                    </button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .table1 {
            border-color: var(--verde-shadow);
            box-shadow: 0 0 10px rgba(43, 255, 0, 0.341);
        }

        .table1 td:first-child,
        .table1 th:first-child {
            text-align: center;
            /* Centrado horizontal */
            vertical-align: middle;
            /* Centrado vertical */
        }

        .table1 th:nth-child(3),
        .table1 td:nth-child(3) {
            width: 300px;
            /* Ajusta el valor como necesites */
        }

        /* Hacer que el checkbox sea más grande */
        .table1 input[type="checkbox"] {
            transform: scale(1.5);
            /* Aumentar tamaño del checkbox */
            margin: 0;
            /* Eliminar márgenes que pudieran alterar la alineación */
            cursor: pointer;
            /* Cambiar el cursor para hacerlo más intuitivo */
        }

        /* Asegurarse de que las filas y el encabezado tengan el mismo padding */
        .table1 td,
        .table1 th {
            padding: 12px;
            /* Ajusta según sea necesario */
        }

        /* Opcional: Para dar mayor control sobre el tamaño de la celda */
        .table1 th {
            width: 50px;
            /* Ajusta el ancho del encabezado si es necesario */
        }

        .table1 td {
            width: 50px;
            /* Ajusta el ancho de las celdas si es necesario */
        }
    </style>
