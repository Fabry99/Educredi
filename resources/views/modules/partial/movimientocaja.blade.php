<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
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

            <div class="contenedor-header" style="width: 100%; margin-bottom: 30px">
                <h1 style="margin-left: 20px">Caja</h1>
                <div class="input-group">
                    <label for="fecha" class="label1"><span style="margin-right: 5px">Fecha:</span>
                        <input type="date" id="fecha" name="fecha" placeholder="Fecha:">
                    </label>
                    <label for="fcontable" class="label1"><span style="margin-right: 5px">Fecha Contable:</span>
                        <input type="date" id="fcontable" name="fcontable" placeholder="F.Contable:">
                    </label>
                    <label for="fabono" class="label1"><span style="margin-right: 5px">Fecha Abono:</span>
                        <input type="date" id="fabono" name="fabono" placeholder="F.Abono:">
                    </label>
                    <div style="display: flex; align-items:center;">
                        <span style="margin-right: 5px">Comprobante:</span>
                        <label for="comprobante" class="label1">
                            <input type="" id="comprobante" name="comprobante" placeholder="Comprobante:">
                        </label>
                    </div>
                </div>
                <div class="valores-calculo">
                    <label>
                        <input type="radio" name="tipo_calculo" value="calculo">Aplicar Valor Cálculo
                    </label>
                    <label style="margin-left: 10px;">
                        <input type="radio" name="tipo_calculo" value="fijo" checked>
                        Aplicar Valor Fijo
                    </label>
                    <label>
                        <input type="checkbox" name="habilitar_manual">
                        Habilitar Cálculo Manual
                    </label>
                </div>

                <div class="modal-ge select">
                    <div class="information">
                        <span>SFDM:</span>
                        <select name="Centro" id="id_centro" style="width: 180px;">
                            <option value="" disabled selected>Seleccionar:</option>
                            @foreach ($centro as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="information">
                        <span>Grupo:</span>
                        <select name="Grupo" id="id_grupo" style="width: 180px;">
                            <option value="" disabled selected>Seleccionar</option>
                        </select>
                    </div>
                    <div class="information">
                        <span>Cuenta:</span>
                        <select name="Cuenta" id="id_cuenta" style="width: 180px;">
                            <option value="" disabled selected>Seleccionar</option>
                        </select>
                    </div>
                    <div class="information">
                        <span>Cuota Total:</span>
                        <input type="text" value="" disabled>
                    </div>
                    <div class="information">
                        <span>Num Cuota:</span>
                        <input type="text" id="input_cuota_total" value="" disabled
                            style="text-align: center; font-weight: bold; width: 75px; font-size: 14px">
                    </div>
                </div>
                <div class="btn-grupos" style="display: flex; margin:15px; justify-content: center">
                    <a href="#" id="openModalBtn" class="btn-agregar"
                        style="margin-right: 15px;"><span>Confirmar</span></a>
                    <a href="#" id="openModalBtn" class="btn-agregar"
                        style="margin-right: 15px;"><span>Reimprimir</span></a>
                    <a href="#" id="openModalBtn" class="btn-agregar" style="margin-right: 15px;"><span>Debe
                            ser</span></a>
                    <a href="#" id="openModalBtn" class="btn-agregar" style="margin-right: 15px;"><span>Est.
                            Cuenta</span></a>
                    <a href="#" id="openModalBtn" class="btn-agregar"
                        style="margin-right: 15px;"><span>Actualizar</span></a>
                </div>
            </div>
            <table id="tablaCaja" class="table table-striped table1" style="width:100%">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th style="width: 500px">Nombre</th>
                        <th>Saldo</th>
                        <th>Ultim Movimiento</th>
                        <th>Poximo Pago</th>
                        <th>Valor</th>
                        <th>Int. Normal</th>
                        <th>Int. Morator</th>
                        <th>Micro seg</th>
                        <th>Seguro</th>
                        <th>IVA</th>
                        <th>Capital</th>
                        <th>Frecuencia</th>
                        <th>Apertura</th>
                        <th>Vencimiento</th>
                    </tr>
                </thead>
                <tbody>


                </tbody>

            </table>
        </div>
    </div>

</div>
<!-- Estilos CSS -->
<style>
    :root {
        --navbar: #067016;
        --background: #eae9e9;
        --color-font: #fff5f5;
        --background-form: rgb(255, 255, 255);
        --color-font-form: #333;
        --sombra-login-form: rgba(9, 72, 9, 0.308);
        --color-boton: rgba(11, 121, 20, 0.866);
        --borde: #ccc;
        --background-inputs: #f7f7f7;
        --font-personal: #8d0808;
        --azul: #385E89;
    }

    h2 {
        justify-self: center
    }

    .modal-ge {
        display: flex;
        width: 100%;
        margin-top: 10px;
    }

    .modal-ge select {
        width: 100%;
        padding: 10px 15px;
        border-radius: 4px;
        background: var(--background-inputs);
    }

    .input-group.select {
        border: 1px solid var(--borde);
        border-radius: 4px;
        background: var(--background-inputs);
    }

    .input-group {
        margin-left: 20px;
        display: flex;
    }

    .input-group input {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 16px;

    }

    .input-group select {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 16px;

    }

    .input-group .label1 {
        display: flex;
        align-items: center;
        background: var(--background-inputs);
        border: 1px solid var(--borde);
        border-radius: 4px;
        padding: 10px 30px;
        margin-right: 20px;
    }

    h3 {
        color: var(--font-personal);
        justify-self: center;
    }

    .label2 {
        display: flex;
        align-items: center;
        background: var(--background-inputs);
        border: 1px solid var(--borde);
        border-radius: 4px;
        padding: 10px;
    }

    .botones {
        display: flex;
        justify-content: right;
        margin-top: 20px;
    }

    .btn-aceptar {
        background: var(--color-boton);
        padding: 5px 25px;
        border-radius: 4px;
        color: var(--color-font);
        text-decoration: none;
        border: 1px solid black;
    }

    .btn-aceptar:hover {
        background: #0b7914;
    }

    /* Forzamos la columna "Nombre" (segunda columna) a un ancho fijo */
    #tablaCaja th:nth-child(2),
    #tablaCaja td:nth-child(2) {
        width: 250px !important;
        min-width: 250px !important;
        max-width: 250px !important;
        white-space: normal !important;
        word-break: break-word !important;
        display: table-cell !important;
    }

    #tablaCaja th:nth-child(3),
    #tablaCaja td:nth-child(3) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
        white-space: normal !important;
        word-break: break-word !important;
        display: table-cell !important;
    }

    #tablaCaja th:nth-child(5),
    #tablaCaja td:nth-child(5) {
        width: 110px !important;
        min-width: 110px !important;
        max-width: 110px !important;
        white-space: normal !important;
        word-break: break-word !important;
        display: table-cell !important;
    }

    #tablaCaja th {
        text-align: center;
    }
</style>
