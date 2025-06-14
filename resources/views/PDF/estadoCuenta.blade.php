{
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Estado de Cuenta Grupal</title>
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

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px;
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

        th.header-grande {
            font-size: 16px;
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
        <h3 style="margin-bottom: -5px;">Estado de Cuenta Grupal</h3>
        <p> {{ $nombreCentro }} - {{ $nombreGrupo }}</p>

        @php
            use Carbon\Carbon;
            $fechaFormateada = Carbon::parse($fechaSeleccionada)->format('d-m-Y');
        @endphp

        <p><strong>Fecha:</strong> {{ $fechaFormateada }}</p>
        <p><strong>Monto Total:</strong> ${{ number_format($monto, 2) }}</p>
        <p><strong>Valor para ponerse al día:</strong> ${{ number_format($valorPonerseAlDia, 2) }}</p>
        <div class="informacion-header">


        </div>

    </div>

    <h3>Pagos Recibidos</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 120px;">Fecha</th>
                <th>Valor Cuota</th>
                <th>Capital</th>
                <th>Int Apli</th>
                <th>Int Mora</th>
                <th>Manejo</th>
                <th>Seguro</th>
                <th>IVA</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalCuota = 0;
                $totalCapital = 0;
                $totalIntApli = 0;
                $totalIntMora = 0;
                $totalManejo = 0;
                $totalSeguro = 0;
                $totalIVA = 0;
                $totalSaldo = 0;
            @endphp

            @foreach ($datosPagos as $pago)
                <tr>
                    <td>{{ $pago['fecha'] }}</td>
                    <td>{{ $pago['valor_cuota'] }}</td>
                    <td>{{ $pago['capital'] }}</td>
                    <td>{{ $pago['int_apli'] }}</td>
                    <td>{{ $pago['int_mora'] }}</td>
                    <td>{{ $pago['manejo'] }}</td>
                    <td>{{ $pago['seguro'] }}</td>
                    <td>{{ $pago['iva'] }}</td>
                    <td>{{ $pago['saldo'] }}</td>
                </tr>
                @php
                    $totalCuota += (float) $pago['valor_cuota'];
                    $totalCapital += (float) $pago['capital'];
                    $totalIntApli += (float) $pago['int_apli'];
                    $totalIntMora += (float) $pago['int_mora'];
                    $totalManejo += (float) $pago['manejo'];
                    $totalSeguro += (float) $pago['seguro'];
                    $totalIVA += (float) $pago['iva'];
                @endphp
            @endforeach

            <tr style="font-weight: bold;">
                <td>Total:</td>
                <td>{{ number_format($totalCuota, 2) }}</td>
                <td>{{ number_format($totalCapital, 2) }}</td>
                <td>{{ number_format($totalIntApli, 2) }}</td>
                <td>{{ number_format($totalIntMora, 2) }}</td>
                <td>{{ number_format($totalManejo, 2) }}</td>
                <td>{{ number_format($totalSeguro, 2) }}</td>
                <td>{{ number_format($totalIVA, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <br>

    <h3>Detalle del Grupo Para Ponerse al Día</h3>
    <table>
        <thead>
            <tr>
                <th>ID Cliente</th>
                <th style="width: 150px;">Nombre</th>
                <th>Interés Normal</th>
                <th>Interés Mora</th>
                <th>Seguro</th>
                <th>Manejo</th>
                <th>IVA</th>
                <th>Capital</th>
                <th>Cuota</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalIntNormal = 0;
                $totalIntMora = 0;
                $totalSeguro = 0;
                $totalManejo = 0;
                $totalIVA = 0;
                $totalCapital = 0;
                $totalCuota = 0;
            @endphp

            @foreach ($datosCuotas as $cuota)
                <tr>
                    <td>{{ $cuota['id_cliente'] }}</td>
                    <td style="text-align: left">{{ $cuota['nombre'] }}</td>
                    <td>{{ $cuota['int_normal'] }}</td>
                    <td>{{ $cuota['int_mora'] }}</td>
                    <td>{{ $cuota['seguro'] }}</td>
                    <td>{{ $cuota['manejo'] }}</td>
                    <td>{{ $cuota['iva'] }}</td>
                    <td>{{ $cuota['capital'] }}</td>
                    <td>{{ $cuota['cuota'] }}</td>
                </tr>
                @php
                    $totalIntNormal += (float) $cuota['int_normal'];
                    $totalIntMora += (float) $cuota['int_mora'];
                    $totalSeguro += (float) $cuota['seguro'];
                    $totalManejo += (float) $cuota['manejo'];
                    $totalIVA += (float) $cuota['iva'];
                    $totalCapital += (float) $cuota['capital'];
                    $totalCuota += (float) $cuota['cuota'];
                @endphp
            @endforeach

            <tr style="font-weight: bold;">
                <td colspan="2">Total:</td>
                <td>{{ number_format($totalIntNormal, 2) }}</td>
                <td>{{ number_format($totalIntMora, 2) }}</td>
                <td>{{ number_format($totalSeguro, 2) }}</td>
                <td>{{ number_format($totalManejo, 2) }}</td>
                <td>{{ number_format($totalIVA, 2) }}</td>
                <td>{{ number_format($totalCapital, 2) }}</td>
                <td>{{ number_format($totalCuota, 2) }}</td>
            </tr>
        </tbody>
    </table>

</body>

</html>
