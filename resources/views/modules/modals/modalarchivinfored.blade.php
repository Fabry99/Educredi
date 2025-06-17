<!-- El Modal -->
<div id="modalinfored" class="modal">
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" width="80" height="80">
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
            <h2 style="margin-left: 40px; margin-top: 40px">Generar Archivo INFORED</h2>

        </div>
        <form action=""method="POST">
            @csrf

            <div class=" information-reporte" style="display: flex">
                <span style="margin-left: 10px">Nombre de Archivo:</span>
                <input type="text" id="nombrearchivo" name="nombre_archivo">
            </div>
            <div class=" information-reporte" style="display: flex; margin-top: 10px">
                <span style="margin-left: 10px">Desde:</span>
                <input type="date" id="fechadesde" name="fechadesde" style="width: 185px;; text-align: center ">
                <span style="margin-left: 40px;">Hasta:</span>
                <input type="date" id="fechaHasta" name="fechaHasta" style="width: 190px;text-align: center">
            </div>
            <div class=" information-reporte" style="display: flex; margin-top: 10px;margin-bottom: 10px">
                <span style="margin-left: 10px">Asesor:</span>
                <select name="Asesor" id="asesorinfored">
                    <option value="" disabled selected>Seleccionar un Asesor</option>

                </select>
                <div class="botones" style=" width: 100%;">
                    <button type="submit" title="Generar Reporte" class="btn-aceptar" id="btn-infored" style="justify-self: end"><img
                            src="{{ asset('img/aceptar.svg') }}" alt=""></button>
                </div>
            </div>

        </form>
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
        --verde-shadow: rgba(43, 255, 0, 0.938);

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
        margin-bottom: 35px
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


    h2 {
        justify-self: center
    }

    .botones {
        display: flex;
        justify-content: right;
        margin-top: 20px;
    }

    .btn-aceptar {
        background: var(--color-boton);
        padding: 5px 45px;
        border-radius: 4px;
        color: var(--color-font);
        text-decoration: none;
        border: 1px solid black;
    }

    button img {
        width: 35px;
        height: 35px;
    }

    .btn-aceptar:hover {
        background: #0b7914;
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }

    .information-reporte {
        display: flex;
        gap: 8px;
        align-items: center;

    }

    .information-reporte input {
        height: 25px;
        margin-top: 20px;
        width: 400px;
        border-radius: 4px;
        padding: 5px;
    }

    .information-reporte input:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }

    .information-reporte span {
        margin-top: 20px;
    }

    .information-reporte select {
        height: 35px;
        margin-top: 20px;
        width: 500px;
        border-radius: 4px;
    }

    .information-reporte select:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }
</style>
