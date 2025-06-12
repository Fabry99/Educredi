<!-- El Modal -->
<div id="modalnuevogrupo" class="modal">
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" width="80" height="80">
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
            <h2>Agregar Grupos</h2>
        </div>
        <form action="{{ route('grupos.savegroup') }}" method="POST">
            @csrf
            <div class="modal-ge" style="margin-top:-20px;">
                <div class="input-group">

                    <input type="nombre" id="nombre" name="nombre" placeholder="Nombre del Grupo:" required
                        style="height: 50%; margin-top: 1px">

                </div>
                <div class="input-group">
                    <select class="select-centro" id="id_centros" name="id_centros" required>
                        <option value="" disabled selected>Selecciona un Centro</option>
                        @foreach ($centros as $centro)
                            <option value="{{ $centro->id }}">{{ $centro->nombre }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="botones">
                <button type="submit" class="btn-aceptar"><img src="{{ asset('img/aceptar.svg') }}"
                        alt=""></button>
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
    }

    h2 {
        justify-self: center
    }

    .modal-ge {
        display: flex;
        width: 100%;
        margin-top: 10px;
        justify-content: center;
    }

    .input-group {
        margin-left: 20px;
        display: flex;
        margin-top: 20px
    }

    .input-group input {
        border: 2px solid var(--borde);
        border-radius: 6px;
        background: none;
        width: 100%;
        font-size: 16px;
        padding: 10px 25px;
        margin-left: 10px;
    }

    .input-group select {
        border: 2px solid var(--borde);
        border-radius: 6px;
        background: var(--background-inputs);
        padding: 10px 10px;
        margin-right: 10px;
        outline: none;
        width: 100%;
        font-size: 16px;

    }

    .input-group select:hover {

        border-color: var(--verde-shadow);
        box-shadow: 0 0 2px var(--verde-shadow);

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

    .nombre {
        display: flex;
        width: 75%;
        justify-content: center;
        align-items: center;
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
    }
</style>
