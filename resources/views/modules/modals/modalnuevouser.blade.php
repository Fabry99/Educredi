<!-- El Modal -->
<div id="ModalNuevoUsuario" class="modal" >
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" width="80" height="80">
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
            <h2>Agregar Usuario</h2>
        </div>
        <form action="{{ route('usuarios.nuevousuario') }}" method="POST">
            @csrf
            <div class="modal-ge" style="margin-top: 45px">
                <div class="input-group">
                    <label for="nombre" class="label1">
                        <input type="nombre" id="nombre" name="nombre" placeholder="Nombre:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="apellido" class="label1">
                        <input type="apellido" id="apellido" name="apellido" placeholder="Apellidos:" required>
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="correo" class="label1">
                        <input type="email" id="correo" name="correo" placeholder="Correo:"
                            pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                            title="Ingrese un correo válido como example@dominio.com" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="password" class="label1">
                        <input type="password" id="password" name="password" placeholder="Asignar Contraseña:"
                            minlength=" 8 " title="La contraseña debe tener al menos 8 caracteres" required>
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group select" style="margin-left: 20px; width: 260px;">
                    <select id="rol" name="rol" required>
                        <option value="" disabled selected>Asignar Rol:</option>
                        <option value="administrador">Administrador</option>
                        <option value="contador">Contador</option>
                        <option value="caja">Cajero</option>
                    </select>
                </div>
                <div class="input-group select" style="margin-left: 40px; width: 200px;">
                    <select id="actividad" name="actividad" required>
                        <option value="" disabled selected>Asignar Actividad</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="nacimiento" class="label2" style="font-size: 12px">Fecha de Nacimiento:
                        <input type="date" id="nacimiento" name="nacimiento" required>
                    </label>
                </div>
            </div>


            <div class="botones">
                <button type="submit" class="btn-aceptar"><img src="{{ asset('img/aceptar.svg') }}"
                        alt=""></button>
            </div>
        </form>
    </div>
</div>
<script>
    const fechaActual = new Date();
    const fechaMinima = new Date(
        fechaActual.getFullYear() - 18,
        fechaActual.getMonth(),
        fechaActual.getDate()
    );

    const inputNacimiento = document.getElementById("nacimiento");
    inputNacimiento.max = fechaMinima.toISOString().split("T")[0];
</script>
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

    .btn-imprimir {
        background: var(--azul);
        padding: 5px 25px;
        border-radius: 4px;
        color: var(--color-font);
        text-decoration: none;
        margin-right: 10px;
        border: 1px solid black;
    }

    .botones button img {
        width: 35px;
        height: 35px;
    }

    .btn-aceptar:hover {
        background: #0b7914;
    }

    .btn-imprimir:hover {
        background: #486c96;
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

    .modal-ge label:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }

    .modal-ge select:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }
</style>
