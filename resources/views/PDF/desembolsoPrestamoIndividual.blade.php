<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Comprobante de Préstamo Individual</title>
    <style>
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
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .table td:nth-child(3),
        .table td:nth-child(4) {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>EDUCREDI</h2>
        <h4>Préstamo Individual</h4>
        <h3 style="margin-bottom: -5px;">DESEMBOLSO DEL EFECTIVO</h3>

        <p>Nombre del Cliente: {{ strtoupper($prestamo['nombre'] ?? 'NO ESPECIFICADO') }}</p>
        <p>Nombre del Personal que Realiza el Desembolso: _______________________________</p>

        <p>Fecha del Desembolso: {{ \Carbon\Carbon::parse($prestamo['FECHAAPERTURA'])->format('d/m/Y') }} </p>
        <p>Monto neto a desembolsar: ${{ number_format($prestamo['MONTO'], 2) }}</p>
    </div>

    {{-- <table class="table">
        <thead>
            <tr>
                <th>Detalle</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Interés</td>
                <td>{{ $prestamo['INTERES'] ?? 'N/A' }}%</td>
            </tr>
            <tr>
                <td>Cuota</td>
                <td>${{ number_format($prestamo['CUOTA'], 2) }}</td>
            </tr>
     
            <tr>
                <td>Fecha de Primer Pago</td>
                <td>{{ \Carbon\Carbon::parse($prestamo['ULTIMA_FECHA_PAGADA'])->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Fecha de Vencimiento</td>
                <td>{{ \Carbon\Carbon::parse($prestamo['FECHAVENCIMIENTO'])->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Garantía</td>
                <td>{{ ucfirst($prestamo['GARANTIA'] ?? 'Ninguna') }}</td>
            </tr>
        </tbody>
    </table> --}}
    <table class="table">
        <thead>
            <tr>
                <th>CODIGO</th>
                <th>Nombre</th>
                <th>Monto</th>
                <th>Fecha Apertura</th>
                <th>Firma de Recibido</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $prestamo['id_cliente'] ?? 'N/A' }}</td>
                <td>{{ $prestamo['nombre'] }}</td>
                <td>${{ number_format($prestamo['MONTO'], 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($prestamo['FECHAAPERTURA'])->format('d/m/Y') }}</td>
                <td></td>

            </tr>
        </tbody>
    </table>


    <div class="firma">
        <p>Firma de Recibido:</p>
        <div class="firma-linea"></div>
    </div>
</body>

</html>
