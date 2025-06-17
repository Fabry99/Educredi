<!-- El Modal -->
<div id="modalreversionprestamo" class="modal">
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" width="80" height="80">
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
            <h2 style="margin-left: 40px">Validar Eliminación</h2>
        </div>
        <form action="{{ route('centros.store') }}" method="POST">
            @csrf
            <div class="modal-ge">
                <div class="input-group">
                    <label for="password" class="password">Ingrese Contraseña Especial:
                    </label>
                    <input type="password" id="password" name="password" required>

                </div>

            </div>
            <div class="botones">
                <button type="submit" class="btn-aceptar" id="btnAceptar"><img src="{{ asset('img/aceptar.svg') }}"
                        alt=""></button>
            </div>

        </form>
    </div>
</div>
{{-- <script>
    const esAdministrador = {{ Auth::check() && Auth::user()->rol === 'administrador' ? 'true' : 'false' }};
</script> --}}


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

    .modal-ge {
        display: flex;
        width: 100%;
        margin-top: 10px;
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
        padding: 10px;
        margin-left: 10px;
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
