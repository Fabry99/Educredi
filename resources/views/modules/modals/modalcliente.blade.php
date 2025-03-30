<!-- El Modal -->
<div id="myModal" class="modal">
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Agregar Cliente</h2>
        <form action="" method="POST">
            @csrf
            <div class="modal-ge">
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
                    <label for="direccion" class="label1">
                        <input type="nombre" id="direccion" name="direccion" placeholder="Dirección:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="apellido" class="label1">
                        <input type="number" id="teloficina" name="teloficina" placeholder="Tel.Oficina:" required>
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="direcciondenegocio" class="label1">
                        <input type="nombre" id="direcciondenegocio" name="direcciondenegocio"
                            placeholder="Dirección de Negocio:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="sector" class="label1">
                        <input type="text" id="sector" name="sectOr" placeholder="Sector:" required>
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="actividadeconomica" class="label1">
                        <input type="nombre" id="actividadeconomica" name="actividadeconomica"
                            placeholder="Actividad Economica:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="NIT" class="label1">
                        <input type="number" id="NIT" name="NIT" placeholder="NIT:" required>
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="depto" class="label1">
                        <input type="text" id="depto" name="depto" placeholder="Depto:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="municipio" class="label1">
                        <input type="text" id="municipio" name="municipio" placeholder="Municipio:" required>
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="ocupacion" class="label1">
                        <input type="text" id="ocupacion" name="ocupacion" placeholder="Ocupación:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="nopuedefirmar" class="label1">
                        <input type="checkbox" id="nopuedefirmar" name="nopuedefirmar" value="nopuedefirmar"
                            style="width: 20px; height: 20px; margin-right: 10px;">
                        No puede Firmar
                    </label>
                </div>
            </div>
            <h3>Datos Personales</h3>

            <div class="modal-ge">
                <div class="input-group">
                    <label for="cliente" class="label2">
                        <input type="text" id="cliente" name="cliente" placeholder="Cliente:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="dui" class="label2">
                        <input type="number" id="dui" name="dui" placeholder="DUI:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="edad" class="label2">
                        <input type="number" id="edad" name="edad" placeholder="Edad:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="fecha_nacimiento" class="label2">
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                            placeholder="F.Nacimiento:" required>
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="lugarnacimiento" class="label2">
                        <input type="text" id="lugarnacimiento" name="lugarnacimiento"
                            placeholder="Lugar de Nacimiento:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="estadocivil" class="label2">
                        <input type="text" id="estadocivil" name="estadocivil" placeholder="Estado Civil:"
                            required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="edad" class="label2">
                        <input type="number" id="edad" name="edad" placeholder="Edad:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="nrc" class="label2">
                        <input type="date" id="nrc" name="nrc" placeholder="NRC:" required>
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="telcasa" class="label2">
                        <input type="number" id="telcasa" name="telcasa" placeholder="Tel.Casa:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="conyugue" class="label2">
                        <input type="text" id="conyugue" name="conyugue" placeholder="Conyugue:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="expedida" class="label2">
                        <input type="date" id="expedida" name="expedida" placeholder="Expedida En:" required>
                    </label>
                </div>

            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="fechaingreso" class="label2">
                        <input type="date" id="fechaingreso" name="fechaingreso"
                            placeholder="Fecha de Ingreso:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="fecharetiro" class="label2">
                        <input type="date" id="fecharetiro" name="fecharetiro" placeholder="Fecha de Retiro:"
                            required>
                    </label>
                </div>
                
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="sueldo" class="label2">
                        <input type="text" id="sueldo" name="sueldo"
                            placeholder="Sueldo:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="otroingreso" class="label2">
                        <input type="text" id="otroingreso" name="otroingreso" placeholder="Otr.Ingreso:"
                            required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="egreso" class="label2">
                        <input type="number" id="egreso" name="egreso" placeholder="Egreso:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="perdependiente" class="label2">
                        <input type="text" id="perdependiente" name="perdependiente" placeholder="Personas Dependientes:" required>
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group" style="width: 100%">
                    <label for="sfdm" class="label2">
                        <input type="text" id="sfdm" name="sfdm"
                            placeholder="SFDM:" required>
                    </label>
                </div>
            </div>
            <div class="botones">
                <a href="" class="btn-imprimir"><img src="{{asset('img/icon_imprimir.svg')}}" alt=""></a>
                <a href="" class="btn-aceptar"><img src="{{asset('img/aceptar.svg')}}" alt=""></a>
                
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
        --azul:#385E89;
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
    .botones{
        display: flex;
        justify-content: right;
        margin-top: 20px;
    }
    .btn-aceptar{
        background: var(--color-boton);
        padding: 5px 25px;
        border-radius: 4px;
        color: var(--color-font);
        text-decoration: none;
        border: 1px solid black;
    }
    .btn-imprimir{
        background: var(--azul);
        padding: 5px 25px;
        border-radius: 4px;
        color: var(--color-font);
        text-decoration: none;
        margin-right: 10px;
        border: 1px solid black;
    }
    .botones a img{
        width: 35px;
        height: 35px;        
    }
    .btn-aceptar:hover{
        background: #0b7914;
    }
    .btn-imprimir:hover{
        background: #486c96;
    }
</style>

