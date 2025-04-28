<div class="modal" id="modaleditaruser">
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <h2></h2>
        <form method="POST" action="{{ route('user.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" id="user_id" name="id"> <!-- Nuevo campo para el ID -->
            <div class="modal-ge1">
                <div class="input-group1">
                    <label for="nombre" class="label1">
                        Nombres:
                        <input type="text" id="nombreupdate" name="nombreupdate" placeholder="Nombre:" required>
                    </label>
                </div>
                <div class="input-group1">
                    <label for="apellido" class="label1">
                        Apellidos:
                        <input type="text" id="apellidoupdate" name="apellidoupdate" placeholder="Apellidos:"
                            required>
                    </label>
                </div>
            </div>
            <div class="modal-ge1">
                <div class="input-group1">
                    <label for="correo" class="label1">
                        Correo:
                        <input type="email" id="correoupdate" name="correoupdate" placeholder="Correo:"
                            title="Ingrese un correo válido como example@dominio.com" required>

                    </label>
                </div>
                <div class="input-group1">
                    <label for="password" class="label1">
                        Contraseña:
                        <input type="password" id="passwordupdate" name="passwordupdate"
                            placeholder="Asignar Nueva Contraseña:" minlength=" 8 "
                            title="La contraseña debe tener al menos 8 caracteres">
                    </label>
                </div>
            </div>
            <div class="modal-ge1" style="margin-left: 40px">
                <label for="">Rol:
                    <div class="input-group1 select" style="margin-left: 30px; width: 190px;">
                        <select id="rolupdate" name="rolupdate" required>
                            <option value="" disabled selected>Asignar Rol:</option>
                            <option value="administrador">Administrador</option>
                            <option value="contador">Contador</option>
                            <option value="caja">Cajero</option>
                        </select>
                    </div>
                </label>
                <label for="" style="margin-left: 45px">Estado:
                    <div class="input-group1 select" style="margin-left: 30px; width: 170px;">
                        <select id="actividadupdate" name="actividadupdate" required>
                            <option value="" disabled selected>Asignar Actividad</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </label>
            </div>
            <div class="modal-ge1">
                <div class="input-group">
                    <label for="nacimiento" class="label2" style="font-size: 12px">Fecha de Nacimiento:
                        <input type="date" id="nacimientoupdate" name="nacimientoupdate" required>
                    </label>
                </div>
            </div>
            <div class="botones">
                <button type="submit" id="" class="btn-aceptar"><img src="{{ asset('img/aceptar.svg') }}"
                        alt=""></button>
            </div>
    </div>

    </form>
</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const fechaActual = new Date();
        const fechaMinima = new Date(
            fechaActual.getFullYear() - 18,
            fechaActual.getMonth(),
            fechaActual.getDate()
        );

        const inputNacimiento = document.getElementById("nacimientoupdate");
        if (inputNacimiento) {
            inputNacimiento.max = fechaMinima.toISOString().split("T")[0];
        }
    });
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
    }

    h2 {
        justify-self: center;
        font-size: 1.3rem;
    }


    .modal-ge1 {
        display: flex;
        flex-direction: row;
        margin-top: 15px;
    }

    .input-group1 {
        display: flex;
        flex-direction: row;
    }

    .input-group1 input {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 16px;
        margin-top: 5px;
        background: var(--background-inputs);
        border: 1px solid var(--borde);
        border-radius: 4px;
        padding: 10px 10px;
        margin: 0px 20px 0px 5px;
    }

    .modal-ge1 select {
        width: 100%;
        padding: 10px 0px;
        border-radius: 4px;
        background: var(--background-inputs);
        margin-left:-20px; 
    }


    .input-group1 {
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

    .modal-ge1 input:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }

    .modal-ge1 select:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }
</style>
