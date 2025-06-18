<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Comprobante de Préstamo Grupal</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
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

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 125px;
            height: auto;
        }

        /* Tabla encabezado sin bordes pero con línea inferior en el header */
        .tabla-encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            table-layout: fixed;
            border: none;
            /* quitar bordes generales */
        }

        .tabla-encabezado th {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            padding: 5px;
            border: none;
            /* quitar bordes */
            border-bottom: 2px solid black;
            /* línea inferior solo en encabezados */
        }

        .tabla-encabezado td {
            font-size: 9pt;
            text-align: center;
            padding: 5px;
            border: none;
            /* quitar bordes */
        }

        .informacion-pres {
            text-align: left;
            margin-top: 10px;
        }

        .informacion-pres span {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
        }

        /* Tabla datos sin bordes */
        .tabla-datos {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            font-size: 11px;
            table-layout: fixed;
            border: none;
            /* quitar bordes */
        }

        .tabla-datos td {
            border: none;
            /* quitar bordes */
            padding: 5px;
            word-break: break-word;
            vertical-align: top;
        }
    </style>
</head>

<body>
    <div class="header">
        @php
            $logo = base64_encode(file_get_contents(public_path('img/logoeducredi.jpeg')));
        @endphp

        <img src="data:image/jpeg;base64,{{ $logo }}" alt="Logo" class="logo">

        <h2 style="font-size: 32px; margin-bottom: 10px;">EDUCREDI RURAL <br> S.A DE C.V</h2>
        <h3 style="margin-bottom: -5px; margin-top: 35px">Reporte de Colocación de Crédito</h3>

        <div class="informacion-header">
            <p>Fecha de Creación: {{ now()->format('d/m/Y') }}</p>
        </div>

        <!-- Tabla encabezado -->
        <table class="tabla-encabezado">
            <thead>
                <tr>
                    <th style="width: 16.66%;">Código</th>
                    <th style="width: 35%;">Nombre del Cliente</th>
                    <th style="width: 60px;">Monto</th>
                    <th style="width: 100px;">Saldo</th>
                    <th style="width: 100px;">Plazo</th>
                    <th style="width: 100px;">Interés</th>
                </tr>
            </thead>
        </table>

        <!-- Información vertical a la izquierda -->

        @foreach ($prestamos as $grupo)
            @foreach ($grupo as $item)
                {{-- Encabezado: se imprime solo la primera vez del grupo --}}
                @if ($loop->first)
                    <div class="informacion-pres">
                        <span style="margin-top:10px;">
                            Sucursal: {{ $item->nombre_sucursales ?? 'N/A' }}
                        </span>
                        <span style="margin-left: 4%;">
                            Supervisor: {{ $item->nombre_supervisor ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="informacion-pres">
                        <span style="margin-left: 8%;">
                            Asesor: {{ $item->nombre_asesor ?? 'N/A' }}
                        </span>
                        <span style="margin-left: 12%;">
                            SFDM: {{ $item->nombre_centro ?? 'N/A' }}
                        </span>
                        <span style="margin-left: 16%;">
                            Grupo: {{ $item->nombre_grupo ?? 'N/A' }}
                        </span>
                    </div>
                @endif

                <div style="text-align: left; margin-top: 50px">
                    <span style="font-weight: 600;">
                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                    </span>
                </div>

                <!-- Tabla datos para este grupo -->
                <table class="tabla-datos">
                    @foreach ($grupo as $item)
                        <tr>
                            <td style="width: 16.66%;">{{ $item->id }}</td>
                            <td style="width: 35%;">{{ $item->nombre_cliente }} {{ $item->apellido }}</td>
                            <td style="text-align: center;">${{ number_format($item->MONTO, 2) }}</td>
                            <td style="text-align: center;">
                                ${{ number_format($saldo0 ? 0 : $item->SALDO, 2) }}
                            </td>
                            <td style="text-align: center;">{{ $item->PLAZO }} </td>
                            <td style="text-align: center;">{{ number_format($item->INTERES, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="font-weight: bold; text-align: left;">
                            Total de: {{ \Carbon\Carbon::parse($grupo->first()->created_at)->format('d/m/Y') }}
                        </td>
                        <td style="text-align: center;">${{ number_format($grupo->sum('MONTO'), 2) }}</td>
                        <td style="text-align: center;">
                            ${{ number_format($saldo0 ? 0 : $item->SALDO, 2) }}
                        </td>
                        <td style="text-align: center;">{{ $grupo->count() }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left; ">
                            <span style="margin-left: 50px">Total
                                de: {{ !empty($item->nombre_grupo) ? $item->nombre_grupo : 'N/A' }} </span>
                        </td>
                        <td style="text-align: center;">${{ number_format($grupo->sum('MONTO'), 2) }}</td>
                        <td style="text-align: center;">
                            ${{ number_format($saldo0 ? 0 : $item->SALDO, 2) }}
                        </td>
                        <td style="text-align: center;">{{ $grupo->count() }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left; ">
                            <span style="margin-left: 50%">Total de:
                                {{ !empty($item->nombre_centro) ? $item->nombre_centro : 'N/A' }}</span>
                        </td>
                        <td style="text-align: center;">${{ number_format($grupo->sum('MONTO'), 2) }}</td>
                        <td style="text-align: center;">
                            ${{ number_format($saldo0 ? 0 : $item->SALDO, 2) }}
                        </td>

                        <td style="text-align: center;">{{ $grupo->count() }}</td>
                        <td></td>
                    </tr>
                    <tr style="margin-bottom: 20px">
                        <td colspan="2" style="text-align: left;">
                            <span style="margin-left: 25%">
                                Total de: {{ $item->nombre_asesor ?? 'N/A' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            ${{ number_format($totalesPorAsesor[$item->nombre_asesor] ?? 0, 2) }}
                        </td>
                        <td style="text-align: center;">
                            ${{ number_format($totalesPorAsesor[$item->nombre_asesor] ?? 0, 2) }}
                        </td>
                        <td style="text-align: center;">
                            {{ $conteoPorAsesor[$item->nombre_asesor] ?? 0 }}

                        </td>
                        <td></td>
                    </tr>

                </table>
            @endforeach
        @endforeach
    </div>
</body>

</html>
