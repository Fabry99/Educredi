<!-- El Modal -->
<div id="modalmutuacuerdogrupal" class="modal">
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" width="80" height="80">
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
            <h2 style="margin-left: 40px; margin-top: 40px">Mutuo Acuerdo Grupal</h2>

        </div>
        <form action=""method="POST">
            @csrf

            <div class=" information-reporte" style="display: flex; margin-top: 10px; margin-bottom: 20px">
                <span style="margin-left: 20px">SFDM:</span>
                <select name="Centro" id="centromutuogrupal" style="width: 190px">
                    <option value="">Seleccionar</option>
                </select>

                <span style="margin-left: 40px;">Grupo:</span>
                <select name="Grupo" id="grupomutuogrupal" style="width: 190px">
                    <option value="">Seleccionar</option>
                </select>
            </div>
            <span style=" margin-left: 20px">Liquidación:</span>
            <div class=" information-reporte"
                style="display: flex; margin-top: 10px;align-items: center; justify-content: center">
                <table id="tabla_montos" style="border: 2px solid rgb(116, 116, 116); border-collapse: collapse; width: 300px ">
                    <thead>
                        <tr>
                            <th style="padding: 0 30px;">Fecha</th>
                            <th style="padding: 0 30px; border-left: 1px solid black">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="text-align: center;">
                            <td style="padding: 5px;"></td>
                            <td style="padding: 5px;border-left: 1px solid black;"></td>
                        </tr>
                    </tbody>
                </table>


            </div>
            <div class=" information-reporte" style="display: flex; margin-top: 10px; margin-bottom: 15px">


                <span style="margin-left: 20px;">Tipo de Mutuo a Emitir:</span>
                <select name="Grupo" id="tipomutuo" style="width: 300px" required>
                    <option value="">Seleccionar</option>
                    <option value="1">MUTUO GRUPAL SIN FIRMA SOLIDARIA</option>
                    <option value="2">MUTUO GRUPAL CON FIRMA SOLIDARIA</option>
                </select>
            </div>
            <div class=" information-reporte" style="display: flex; margin-top: 10px; margin-bottom: 20px">
                <span style="margin-left: 20px">Depto:</span>
                <select name="Departamento" id="deptomutuo" style="width: 180px">
                    <option value="">Seleccionar</option>
                </select>

                <span style="margin-left: 40px;">Municipio:</span>
                <select name="Municipio" id="municipiomutuo" style="width: 180px">
                    <option value="">Seleccionar</option>
                </select>
            </div>
            <div class=" information-reporte" style="display: flex; margin-top: 10px;justify-content: center">
                <span style="margin-left: 10px">Fecha de Mutuo:</span>
                <input type="date" id="fechamutuogrupal" name="Fecha Mutuo"
                    style="width: 155px;; text-align: center; font-size: 16px" disabled>

            </div>
            <div class=" information-reporte" style="display: flex; margin-top: 10px;margin-bottom: 10px">

                <div class="botones" style=" width: 100%;">
                    <button type="submit" title="Generar Reporte" class="btn-aceptar" id="btn-mutuogrupal"
                        style="justify-self: end"><img src="{{ asset('img/aceptar.svg') }}" alt=""></button>
                </div>
            </div>

        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputFecha = document.getElementById('fechamutuogrupal');
        const hoy = new Date();
        const yyyy = hoy.getFullYear();
        const mm = String(hoy.getMonth() + 1).padStart(2, '0');
        const dd = String(hoy.getDate()).padStart(2, '0');
        inputFecha.value = `${yyyy}-${mm}-${dd}`;
    });
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
        --verde-shadow: rgba(43, 255, 0, 0.938);

    }

    .fila-seleccionada {
        background-color: #d3eafd;
    }

    #tabla_montos tr:hover {
        background-color: #f0f8ff;
        /* Un azul muy claro, puedes cambiarlo */
        cursor: pointer;
        /* Opcional, para que se note que es interactivo */
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
