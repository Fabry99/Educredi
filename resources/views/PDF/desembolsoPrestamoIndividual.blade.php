<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Comprobante de Préstamo Individual</title>
    <style>
        body {
            font-family: sans-serif;
        }

        /* Logo alineado a la izquierda sin afectar el flujo */
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 125px;
            height: auto;
        }

        /* Contenedor que centra todo el contenido */
        .contenido {
            text-align: center;
            margin: 0 auto;
            width: 100%;
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

        .firma {
            margin-top: 40px;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
        $logo = base64_encode(file_get_contents(public_path('img/logoeducredi.jpeg')));
    @endphp

    <!-- Logo en la esquina izquierda -->
    <img src="data:image/jpeg;base64,{{ $logo }}" alt="Logo" class="logo">

    <!-- Contenido centrado -->
    <div class="contenido">
        <div class="header">
            <h2 style="font-size: 32px; margin-bottom: 10px;">EDUCREDI RURAL <br> S.A DE C.V</h2>
            <h4 style="margin-bottom: 10px;">Préstamo Individual</h4>
            <h3 style="margin-bottom: 25px;">DESEMBOLSO DEL EFECTIVO</h3>
        </div>


        <p style="margin-bottom: 25px">Nombre del Cliente: {{ strtoupper($prestamo['nombre'] ?? 'NO ESPECIFICADO') }}</p>
        <p style="margin-bottom: 25px">Nombre del Personal que Realiza el Desembolso: _______________________________</p>
        <p style="margin-bottom: 25px">Fecha del Desembolso: {{ \Carbon\Carbon::parse($prestamo['FECHAAPERTURA'])->format('d/m/Y') }} </p>
        <p>Monto neto a desembolsar: ${{ number_format($prestamo['MONTO'], 2) }}</p>
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
</body>

</html>
