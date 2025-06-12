<!-- El Modal -->
<div id="ModalNuevoAsesor" class="modal">
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" width="80" height="80">
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
            <h2>Agregar Asesor</h2>
        </div>
        <form action="{{ route('asesor.insert') }}" method="POST">
            @csrf
            <div class="modal-ge" style=" justify-content: center; align-items: center">
                <div class="input-group" style=" paddin: 0px 0px; margin-top: -1px;">
                    <input type="nombre" id="nombre" name="nombre" placeholder="Nombre:" required
                        style="width: 250px">
                </div>
                <div class="input-group select"
                    style="margin-left: 20px; width: 250px;
                margin: 20px; 0px">
                    <select id="sucursal" name="sucursal" title="Seleccionar Sucursal" required>
                        <option value="" disabled selected>Asignar Sucursal</option>
                        @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="botones">
                <button type="submit" title="Ingresar" class="btn-aceptar" id="btnAceptarAsesor"><img
                        src="{{ asset('img/aceptar.svg') }}" alt=""></button>
            </div>
        </form>
    </div>
</div>

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
        --border-color-input: #159109;
    }

    h2 {
        justify-self: center
    }

    .modal-ge {
        display: flex;
        width: 100%;
        margin-top: 10px;
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

    .modal-ge select {
        width: 100%;
        padding: 10px 15px;
        border-radius: 4px;
        background: var(--background-inputs);
    }

    .modal-ge .input-group.select {
        border: 1px solid var(--borde);
        border-radius: 4px;
        background: var(--background-inputs);
    }

    .input-group {
        margin-left: 20px;
    }

    .input-group input {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 16px;
    }

    .modal-ge .input-group select {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 16px;

    }

    .modal-ge .input-group .label1 {
        display: flex;
        align-items: center;
        background: var(--background-inputs);
        border: 1px solid var(--borde);
        border-radius: 4px;
        padding: 0px 30px;
        margin-right: 20px;
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

    .botones button img {
        width: 35px;
        height: 35px;
    }

    .btn-aceptar:hover {
        background: #0b7914;
        border-color: var(--verde-shadow);
        box-shadow: 0 0 2px var(--verde-shadow);
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

    .modal-ge input:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }

    .modal-ge select:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }
</style>
