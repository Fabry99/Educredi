<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Comprobante de Préstamo Grupal</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
            /* Márgenes del PDF */
        }

        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header p {
            margin-top: 30px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            /* Evitar que se desborde */
            word-wrap: break-word;
            table-layout: fixed;
            /* Para columnas iguales y evitar que crezca demasiado */
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 3px;
            /* Reduce el padding para que quepa mejor */
            font-size: 9pt;
            /* Ajusta tamaño de fuente para PDF */
            overflow-wrap: break-word;
            white-space: normal;
        }

        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            /* tamaño más pequeño para los encabezados */
        }


        .table td:nth-child(3),
        .table td:nth-child(4) {
            text-align: center;
        }

        /* Logo alineado a la izquierda sin afectar el flujo */
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 125px;
            height: auto;
        }

        .table td:first-child {
            width: 20%;
        }

        .table tbody tr.total-row td {
            border: 1px solid #dddddd;
            /* borde más visible */
            font-weight: bolder;
            background-color: #ffffff;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        @php
            $logo = base64_encode(file_get_contents(public_path('img/logoeducredi.jpeg')));
        @endphp

        <!-- Logo en la esquina izquierda -->
        <img src="data:image/jpeg;base64,{{ $logo }}" alt="Logo" class="logo">

        <h2 style="font-size: 32px; margin-bottom: 10px;">EDUCREDI RURAL <br> S.A DE C.V</h2>
        <h3 style="margin-bottom: -5px;">Comprobante de Pago</h3>
        <div class="informacion-header">
            {{-- <p>Nombre del Grupo de Crédito:
                {{ strtoupper($prestamos[0]['nombre_centro'] ?? 'NO ESPECIFICADO') }} /
                {{ strtoupper($prestamos[0]['nombre_grupo'] ?? 'NO ESPECIFICADO') }}
            </p> --}}
            <p>Comprobante N° {{ $pago[0]['comprobante'] ?? 'N/A' }}</p>
            <p>Nombre del Grupo de Crédito:
                {{ strtoupper($pago[0]['nombre_centro'] ?? 'NO ESPECIFICADO') }} /
                {{ strtoupper($pago[0]['nombre_grupo'] ?? 'NO ESPECIFICADO') }}
            </p>
            <p>Fecha de Comprobante generado: {{ now()->format('d/m/Y') }} </p>

        </div>

        {{-- <p>Fecha del Desembolso: {{ now()->format('d/m/Y') }} </p>
        <p>Monto neto a desembolsar: ${{ number_format(array_sum(array_column($prestamos, 'monto')), 2) }}</p> --}}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Saldo Anterior</th>
                <th>Valor</th>
                <th>Capital</th>
                <th>Int Normal</th>
                <th>Int Mora</th>
                <th>Seguro</th>
                <th>Micro Seguro</th>
                <th>IVA</th>
                <th>Saldo Actual</th>
                <th>Exceso</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Inicializar variables para acumular totales
                $total_saldo_anterior = 0;
                $total_valor_cuota = 0;
                $total_capital = 0;
                $total_intereses = 0;
                $total_intereses_mora = 0;
                $total_manejo = 0;
                $total_micro_seg = 0;
                $total_iva = 0;
                $total_saldo_actual = 0;
                $total_exceso = 0;
            @endphp

            @foreach ($pago as $pago)
                @php
                    $total_saldo_anterior += $pago['saldo_anterior'];
                    $total_valor_cuota += $pago['valor_cuota'];
                    $total_capital += $pago['capital'];
                    $total_intereses += $pago['intereses'];
                    $total_intereses_mora += $pago['intereses_mora'] ?? 0;
                    $total_manejo += $pago['manejo'];
                    $total_micro_seg += $pago['micro_seg'];
                    $total_iva += $pago['iva'];
                    $total_saldo_actual += $pago['saldo_actual'];
                    $total_exceso += $pago['exceso'] ?? 0;
                @endphp
                <tr>
                    <td>{{ $pago['nombrecliente'] }}</td>
                    <td style="text-align: center">${{ number_format($pago['saldo_anterior'], 2) }}</td>
                    <td style="text-align: center">${{ number_format($pago['valor_cuota'], 2) }}</td>
                    <td style="text-align: center">${{ number_format($pago['capital'], 2) }}</td>
                    <td style="text-align: center">${{ number_format($pago['intereses'], 2) }}</td>
                    <td style="text-align: center">
                        ${{ isset($pago['intereses_mora']) ? number_format($pago['intereses_mora'], 2) : '0.00' }}</td>
                    <td style="text-align: center">${{ number_format($pago['manejo'], 2) }}</td>
                    <td style="text-align: center">${{ number_format($pago['micro_seg'], 2) }}</td>
                    <td style="text-align: center">${{ number_format($pago['iva'], 2) }}</td>
                    <td style="text-align: center">${{ number_format($pago['saldo_actual'], 2) }}</td>
                    <td style="text-align: center">${{ number_format($pago['exceso'] ?? 0, 2) }}</td>
                </tr>
            @endforeach

            <!-- Fila de totales -->
            <tr class="total-row">
                <td>Totales</td>
                <td>${{ number_format($total_saldo_anterior, 2) }}</td>
                <td>${{ number_format($total_valor_cuota, 2) }}</td>
                <td>${{ number_format($total_capital, 2) }}</td>
                <td>${{ number_format($total_intereses, 2) }}</td>
                <td>${{ number_format($total_intereses_mora, 2) }}</td>
                <td>${{ number_format($total_manejo, 2) }}</td>
                <td>${{ number_format($total_micro_seg, 2) }}</td>
                <td>${{ number_format($total_iva, 2) }}</td>
                <td>${{ number_format($total_saldo_actual, 2) }}</td>
                <td>${{ number_format($total_exceso, 2) }}</td>
            </tr>
        </tbody>

    </table>

</body>

</html>
