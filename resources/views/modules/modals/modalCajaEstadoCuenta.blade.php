<!-- El Modal -->
<div id="modalCajaEstadoCuenta" class="modalEstadoCuenta">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Contenido del Modal -->
    <div class="modal-contentestados">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" width="80" height="80" />
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
            <div class="tittle" style="text-align: center">
                <h2 id="modal-title" style="text-align: center">Estado de Cuentas por SFDM</h2>
                <span id="centros-grupos"></span>
            </div>
            <!-- Aquí se actualizará el título con el ID -->
        </div>
        <div class="information-estado">
            <div class="information-estado">
                <span>Monto:</span>
                <input type="text" id="monto" value="" disabled style="width: 70px; text-align: center">
            </div>
            <div class="information-estado">
                <span>Fecha Apertura:</span>
                <select id="fapertura" style="width: 120px; border-radius: 5px; padding: 2px; text-align: center; height: 40px">
                    <!-- Las opciones se agregarán con JavaScript -->
                </select>
            </div>
            <div class="information-estado">
                <span>Valor para ponerse al día:</span>
                <input type="text" id="valorponersealdia" value="" disabled style="width: 70px; text-align: center">
            </div>
        </div>
        <span class="tittle-information" style="margin-left: 3px; ">
            Pagos Recibidos:
        </span>
        <table id="tablaPagosRecib" class="table table-striped tableEstado"
            style="width:100%; margin-top: 5px; margin-bottom: 25px">
            <thead>
                <tr>
                    <th style="width: 120px">Fecha</th>
                    <th>Valor</th>
                    <th>Capital</th>
                    <th>Int Cte</th>
                    <th>Int Mora</th>
                    <th>Seguro</th>
                    <th>Micro Seg.</th>
                    <th>IVA</th>
                    <th>Saldo</th>

                </tr>
            </thead>
            <tbody>

            </tbody>

        </table>
        <span class="tittle-information" style="margin-left: 3px; margin-top: 20px ">
            Detalle del Grupo Para Ponerse al Día:
        </span>
        <table id="tablaDetallesGrupo" class="table table-striped tableEstado"
            style="width:100%; margin-top: 5px; margin-bottom: 15px">
            <thead>
                <tr>
                    <th style="width: 100px">Codigo</th>
                    <th style="width: 250px">Nombre</th>
                    <th>Int Normal</th>
                    <th>Int Mora</th>
                    <th>Seguro</th>
                    <th style="width: 60px">Micro Seg.</th>
                    <th>IVA</th>
                    <th>Capital</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>

            </tbody>

        </table>

        <div class="botones">
            <button type="submit" class="btn-imprimir" id="btnGenerarPDF"><img src="{{ asset('img/icon_imprimir.svg') }}"
                    alt="imprimir"></button>
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

    .modalEstadoCuenta {
        display: none;
        /* Ocultar el modal por defecto */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    /* Contenido del Modal */
    .modal-contentestados {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: auto;
        max-width: 1000px;
        border-radius: 10px;
    }

    h2 {
        justify-self: center
    }

    .close-btn1 {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close-btn1:hover,
    .close-btn1:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .head-tittle {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        height: 90px;
        margin-bottom: 40px
    }

    .head-logo {
        position: absolute;
        left: 10px;
        display: flex;
        flex-direction: column;
        /* ⬅️ Coloca el h3 debajo del img */
        align-items: center;
        gap: 5px;
        margin-top: 25px;

    }

    .head-logo img {
        width: 80px;
        height: 80px;
        border-radius: 50%;

    }

    .head-logo h3 {
        font-size: 14px;
        margin: 0;
        text-align: center;
        color: #252525ee;
    }

    .information-estado {
        display: flex;
        justify-items: center;
        justify-content: center;
        gap: 25px;
        margin: 0px 20px 15px 20px;

    }

    .information-estado {
        display: flex;
        align-items: center;
        gap: 10px;

    }

    .information-estado input {
        width: auto;
        height: 25px;
        font-size: 15px;

    }

    /* Cambiar el color de fondo de las cabeceras */
    .tableEstado th {
        background-color: var(--tittle-column);
        color: white;
        text-align: center;
    }

    /* Cambiar el color de las filas al pasar el cursor sobre ellas */
    .tableEstado tbody tr:hover {
        background-color: #e1efda;
    }

    .tableEstado tbody tr:nth-child(even):hover {
        background-color: #e1efda;
    }

    /* Establecer el borde de las celdas */
    .tableEstado th,
    .tableEstado td {
        border: 1px solid #ddd;
        padding: 10px;
    }

    /* Personalizar las filas alternadas */
    .tableEstado tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Cambiar el color de la paginación */
    .dataTables_paginate .paginate_button {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
    }

    .dataTables_paginate .paginate_button:hover {
        background-color: #0056b3;
    }

    table.dataTable tbody tr:hover {
        cursor: pointer;
    }

    table.tableEstado td {
        text-align: center !important;
    }

    .btn-imprimir {
        margin-top: 20px;
        background: var(--azul);
        padding: 5px 25px;
        border-radius: 4px;
        color: var(--color-font);
        text-decoration: none;
        margin-right: 0px;
        border: 1px solid black;

    }

    .btn-imprimir:hover {
        background: #2756c3;
        border: 1px solid #fff;
        box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 8px;
    }
</style>
