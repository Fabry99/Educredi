<!-- El Modal -->
<div id="modalCajaDebeSer" class="modalDebeSer">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Contenido del Modal -->
    <div class="modal-contentgrupos">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" width="80" height="80" />
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
            <h2 id="modal-title" style="text-align: center">Detalles Debe Ser</h2>
            <!-- Aquí se actualizará el título con el ID -->
        </div>
        <div class="information-debe">
            <div class="information-group">
                <span>Centro:</span>
                <input type="text" id="centro" value="" disabled style="width: 180px">
            </div>
            <div class="information-group">
                <span>Tasa:</span>
                <input type="text" id="tasa" value="" disabled style="width: 60px">
            </div>
            <div class="information-group">
                <span>Asesor:</span>
                <input type="text" id="asesor" value="" disabled style="width: 200px">
            </div>
        </div>
        <table id="tablaGrupos" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Valor</th>
                    <th>Capital</th>
                    <th>Interes</th>
                    <th>Saldo</th>

                </tr>
            </thead>
            <tbody>

            </tbody>

        </table>
    </div>
</div>
<script>
    const esAdministrador = {{ Auth::check() && Auth::user()->rol === 'administrador' ? 'true' : 'false' }};
    console.log(esAdministrador); // Verifica si es administrador
</script>

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

    .modalDebeSer {
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
    .modal-contentgrupos {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: auto;
        max-width: 700px;
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

    .information-debe {
        display: flex;
        justify-items: center;
        justify-content: center;
        gap: 25px;
        margin: 0px 15px 25px 15px;

    }

    .information-group {
        display: flex;
        align-items: center;
        gap: 10px;

    }

    .information-group input {
        width: auto;
        height: 25px;
        font-size: 15px;

    }

    /* Cambiar el color de fondo de las cabeceras */
    #tablagrupos th {
        background-color: var(--tittle-column);
        color: white;
        text-align: left;
    }

    /* Cambiar el color de las filas al pasar el cursor sobre ellas */
    #tablagrupos tbody tr:hover {
        background-color: #e1efda;
    }

    .tablagrupos tbody tr:nth-child(even):hover {
        background-color: #e1efda;
    }

    /* Establecer el borde de las celdas */
    #tablagrupos th,
    #tablagrupos td {
        border: 1px solid #ddd;
        padding: 10px;
    }

    /* Personalizar las filas alternadas */
    #tablagrupos tbody tr:nth-child(even) {
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

    table#tablaGrupos td {
        text-align: center !important;
    }
</style>
