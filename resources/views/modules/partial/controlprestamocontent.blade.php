<div class="container">
    <div class="main-content">
        <h1>Control de Préstamos</h1>

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

        <div class="section"
            style="height: auto; width: 1050px; margin-top:40px; margin-left: -10px; background: #c1c0c0">
            <div class="personal-information">
                <span>Código Cliente:</span>
                <input type="text" id="codigo_cliente"
                    style="width: 70px; height: 30px; border-radius: 4px; background: #dfdfdf;text-align: center"
                    readonly>
                <span style="margin-left: 30px">Buscar Cliente:</span>
                <div class="input-container">
                    <img src="{{ asset('img/icon_buscar.svg') }}" alt="" width="30px" height="30px"
                        class="icon-lupa">
                    <input type="text" id="buscarcliente" placeholder="Buscar Nombre de Cliente"
                        style="width: 400px">
                </div>


            </div>
            <div class="" style="margin-left: 20px; gap: 10px">
                <span>Nombre:</span>
                <input type="text" placeholder="Nombre Cliente" id="nombre_cliente" readonly
                    style="background: #dfdfdf; width: 300px">
                <span style="margin-left: 25px">Telefono:</span>
                <input type="text" placeholder="Telefono" id="telefono" readonly
                    style="background: #dfdfdf; width: 100px">
                <span style="margin-left: 20px">Prestamos:</span>
                <select name="prestamos" id="fechasprestamos" style="width: 270px">
                    <option value="">Seleccione un préstamo</option>
                </select>
            </div>
            <div class="" style="margin-left: 0px; gap: 10px; margin-top: 20px">
                <span style="margin-left: 20px">SFDM:</span>
                <input type="text" placeholder="Centro" id="centro" readonly
                    style="background: #dfdfdf; width: 220px">
                <span style="margin-left: 10px">Grupo:</span>
                <input type="text" placeholder="Grupo...." id="grupo" readonly
                    style="background: #dfdfdf; width: 220px">
                <span style="margin-left: 10px">Garantía:</span>
                <input type="text" placeholder="Garantia" id="garantia" readonly
                    style="background: #dfdfdf; width: 220px">
            </div>
            <div class="" style="margin-left: 0px; gap: 10px; margin-top: 20px">
                <span style="margin-left: 20px">Supervisor:</span>
                <input type="text" placeholder="Supervisor" id="supervisor" readonly
                    style="background: #dfdfdf; width: 220px">
                <span style="margin-left: 10px">Sucursal:</span>
                <input type="text" placeholder="Sucursal" id="sucursal" readonly
                    style="background: #dfdfdf; width: 220px">
                <span style="margin-left: 20px">Plazo:</span>
                <input type="text" placeholder="0" id="plazo" readonly
                    style="background: #dfdfdf; width: 70px; text-align: center">
                <span style="margin-left: 10px">Interes:</span>
                <input type="text" placeholder="0.00" id="interes" readonly
                    style="background: #dfdfdf; width: 70px; text-align: center">
            </div>
            <div class="" style="margin-left: 20px; gap: 10px; margin-top: 20px">
                <span>Ultimo Movimiento:</span>
                <input type="text" placeholder="--/--/----/" id="fechaultmovimiento" readonly
                    style="background: #dfdfdf; width: 100px; text-align: center">
                <span style="margin-left: 25px">Fecha Primer Pago:</span>
                <input type="text" placeholder="--/--/----/" id="fechaprimpago" readonly
                    style="background: #dfdfdf; width: 100px;text-align: center">
                <span style="margin-left: 20px">Monto Prestamo:</span>
                <input type="text" placeholder="0.00" id="montoprestamo" readonly
                    style="background: #dfdfdf; width: 70px;text-align: center">
                <span style="margin-left: 20px">Saldo:</span>
                <input type="text" placeholder="0.00" id="saldo" readonly
                    style="background: #dfdfdf; width: 70px;text-align: center">
            </div>
            <div class="" style="margin-left: 20px; gap: 10px; margin-top: 20px; ">
                <span>Cuota:</span>
                <input type="text" placeholder="0.00" id="cuota" readonly
                    style="background: #dfdfdf; width: 100px; text-align: center">
                <span style="margin-left: 25px">Seguro:</span>
                <input type="text" placeholder="0.00" id="seguro" readonly
                    style="background: #dfdfdf; width: 100px;text-align: center">
                <span style="margin-left: 20px">Micro Seguro:</span>
                <input type="text" placeholder="0.00" id="mseguro" readonly
                    style="background: #dfdfdf; width: 70px;text-align: center">
            </div>
            <h3 style="text-align: center; margin-top: 30px; margin-bottom: 5px">Pagos de Cuotas</h3>

            <div class="table" style="margin: 5px 20px 30px 20px">
                <table id="tablareversionCuota" class="table table-striped table1" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Fecha</th>
                            <th style="width: 120px">Fecha Contable</th>
                            <th style="width: 100px">Comprobante</th>
                            <th style="width: 60px">Valor</th>
                            <th>Abonos</th>
                            <th style="width: 100px">Saldo</th>
                            <th>Interes Aplicado</th>
                            <th>Seguro</th>
                            <th>Manejo</th>
                            <th>Iva</th>
                        </tr>
                    </thead>
                    <tbody>


                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>
<style>
    select {
        height: 30px;
        border-radius: 4px;
    }

    input {
        height: 30px;
        border-radius: 4px;
        padding-left: 10px
    }

    .personal-information {
        display: flex;
        gap: 5px;
        align-items: center;
        margin: 20px;

    }

    .input-container {
        position: relative;
        width: 100%;
        max-width: 300px;
        /* opcional, para que no se extienda demasiado */
    }

    .input-container .icon-lupa {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        pointer-events: none;
        /* evita que el mouse interfiera */
        opacity: 0.6;
        /* da ese efecto tenue tipo gris */
    }

    .input-container input {
        padding-left: 35px;
        /* espacio suficiente para la lupa */
        height: 36px;
        width: 100%;
        box-sizing: border-box;
        border-radius: 4px;
    }

    /* Cambiar color de fondo de la tabla y bordes */
    #tablareversionCuota {
        background-color: #f7f7f7;
        border: 1px solid #ccc;
        color: #333;
    }

    /* Cambiar color del encabezado */
    #tablareversionCuota thead {
        background-color: #007bff;
        color: white;
    }

    /* Cambiar color de las filas al pasar el mouse */
    #tablareversionCuota tbody tr:hover {
        background-color: #dbe9ff;
    }

    /* Opcional: rayado de filas alternas */
    #tablareversionCuota tbody tr:nth-child(even) {
        background-color: #f1f1f1;
    }

    #sugerencias-clientes li:hover {
        background-color: #007bff;
        color: white;
    }
</style>
