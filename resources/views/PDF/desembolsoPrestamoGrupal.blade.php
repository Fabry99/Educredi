<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Comprobante de Préstamo Grupal</title>
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

        /* Logo alineado a la izquierda sin afectar el flujo */
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 125px;
            height: auto;
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
        <h4>Prestamos Nuevos y de Seguimiento</h4>
        <h3 style="margin-bottom: -5px;">DESEMBOLSO DEL EFECTIVO</h3>
        <div class="informacion-header">
            <p>Nombre del Grupo de Crédito:
                {{ strtoupper($prestamos[0]['nombre_centro'] ?? 'NO ESPECIFICADO') }} /
                {{ strtoupper($prestamos[0]['nombre_grupo'] ?? 'NO ESPECIFICADO') }}
            </p>
            <p>Nombre del Personal que Realiza el Desembolso: _______________________________</p>
        </div>

        <p>Fecha del Desembolso: {{ now()->format('d/m/Y') }} </p>
        <p>Monto neto a desembolsar: ${{ number_format(array_sum(array_column($prestamos, 'monto')), 2) }}</p>
    </div>

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
            @foreach ($prestamos as $prestamo)
                <tr>
                    <td>{{ $prestamo['id_cliente'] }}</td>
                    <td>{{ $prestamo['nombre_cliente'] }}</td>
                    <td>${{ number_format($prestamo['monto'], 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($prestamo['fechaapertura'])->format('d/m/Y') }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
