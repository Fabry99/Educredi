<div class="modaledit" id="modaleditarcliente">
    <div class="modal-contentedit">
        <span class="close-btn1">&times;</span>
        <h2></h2>
        <form method="POST" action="{{ route('clientes.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" id="cliente_id" name="id"> <!-- Nuevo campo para el ID -->
            <div class="modal-ge1">
                <div class="input-group1">
                    <p>Nombre:</p>
                    <input type="nombre" id="nombre" name="nombre" placeholder="Nombre:">
                </div>
                <div class="input-group1">
                    <p>Apellidos:</p>
                    <input type="apellido" id="apellido" name="apellido" placeholder="Apellidos:">
                </div>
                <div class="input-group1">
                    <p>Dirección:</p>
                    <input type="text" id="direccion" name="direccion" placeholder="Dirección:">
                </div>

            </div>
            <div class="modal-ge1">
                <div class="input-group1">
                    <p style="white-space:nowrap;">Teléfono Oficina:</p>
                    <input type="text" id="teloficina" name="teloficina" placeholder="Telefono Oficina:"
                        pattern="\d{8}" title="El teléfono debe tener exactamente 8 dígitos">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Dirección Negocio:</p>
                    <input type="text" id="dir_negocio" name="dir_negocio" placeholder="Dirección Negocio:">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Sector:</p>
                    <input type="text" id="sector" name="sector" placeholder="Sector:">
                </div>
            </div>
            <div class="modal-ge1">
                <div class="input-group1">
                    <p style="white-space:nowrap;">Actividad Economica:</p>
                    <input type="nombre" id="actividadeconomica" name="actividadeconomica"
                        placeholder="Actividad Economica:">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">NIT:</p>
                    <input type="text" id="NIT" name="NIT" placeholder="NIT: 1234-567891-234-5"
                        pattern="\d{4}-\d{6}-\d{3}-\d{1}"
                        title="Formato: 4 dígitos - 6 dígitos - 3 dígitos - 1 dígito (Ej: 1234-567891-234-5)">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Departamento:</p>
                    <select id="id_departamentoeditcliente" name="id_departamentoeditcliente" required>
                        <option value="" disabled selected>Seleccione un Departamento</option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-ge1">
                <div class="input-group1">
                    <p style="white-space:nowrap;">Municipio:</p>
                    <select id="id_municipioedit" name="id_municipioedit" required>
                        <option value="" disabled selected>Seleccione un Municipio</option>
                    </select>
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Ocupación:</p>
                    <input type="text" id="ocupacion" name="ocupacion" placeholder="Ocupación:">

                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Puede Firmar ?</p>
                    <select id="firma" name="firma">
                        <option value="" disabled selected>Puede Firmar ?</option>
                        <option value="si">Si</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>
            <h3>Datos Personales</h3>
            <div class="modal-ge1">
                <div class="input-group1">
                    <p style="white-space:nowrap;">DUI:</p>
                    <input type="text" id="dui" name="dui" placeholder="DUI: 12345678-9"
                        pattern="\d{8}-\d{1}" title="Formato: 8 dígitos - 1 dígito (Ej: 12345678-9)">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Expedida En:</p>
                    <input type="text" id="expedida" name="expedida" placeholder="Expedida En:">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Fecha de Expedición:</p>
                    <input type="date" id="expedicion" name="expedicion">
                </div>
            </div>
            <div class="modal-ge1">
                <div class="input-group1">
                    <p style="white-space:nowrap;">Lugar de Nacimiento:</p>
                    <input type="text" id="lugarnacimiento" name="lugarnacimiento"
                        placeholder="Lugar de Nacimiento:">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Nacionalidad:</p>
                    <input type="text" id="nacionalidad" name="nacionalidad" placeholder="Nacionalidad:">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Genero:</p>
                    <select id="genero" name="genero">
                        <option value="" disabled selected>Genero:</option>
                        <option value="masculino">masculino</option>
                        <option value="femenino">femenino</option>

                    </select>
                </div>
            </div>
            <div class="modal-ge1">
                <div class="input-group1">
                    <p style="white-space:nowrap;">Fecha Nacimiento:</p>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="F.Nacimiento:">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Teléfono:</p>
                    <input type="text" id="telcasa" name="telcasa" placeholder="Tel.Casa:" pattern="\d{8}"
                        title="El teléfono debe tener exactamente 8 dígitos">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Estado Civil:</p>
                    <select id="estado_civil" name="estado_civil">
                        <option value="" disabled selected>Estado Civil:</option>
                        <option value="soltero">soltero</option>
                        <option value="casado">casado</option>
                        <option value="divorciado">divorciado</option>
                        <option value="viudo">viudo</option>
                    </select>
                </div>
            </div>
            <div class="modal-ge1">
                <div class="input-group1">
                    <p style="white-space:nowrap;">NRC:</p>
                    <input type="text" id="nrc" name="nrc" placeholder="NRC:" pattern="\d{22}"
                        title="Ingrese el Código de 22 Dígitos">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Personas Dependientes:</p>
                    <input type="text" id="perdependiente" name="perdependiente"
                        placeholder="Personas Dependientes:" min="1" max="20"
                        title="Se Debe Agregar la Cantidad de Personas Dependientes">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Cónyugue:</p>
                    <input type="text" id="conyugue" name="conyugue" placeholder="Conyugue:">

                </div>
            </div>
            <div class="modal-ge1">
                <div class="input-group1">
                    <p style="white-space:nowrap;">Sueldo:</p>
                    <input type="number" id="sueldo" name="sueldo" placeholder="Sueldo: 0.0" step="0.01"
                        min="0">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Egresos:</p>
                    <input type="number" id="egreso" name="egreso" placeholder="Egreso: 0.0" step="0.01"
                        min="0">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Otros Ingresos:</p>
                    <input type="text" id="otroingreso" name="otroingreso" placeholder="Otr.Ingreso: 0.0"
                        step="0.01" min="0">

                </div>
            </div>
            <div class="modal-ge1">
                <div class="input-group1">
                    <p style="white-space:nowrap;">Centro:</p>
                    <select id="id_centroeditar" name="id_centroeditar" required>
                        <option value="" disabled selected>Seleccione un Centro</option>
                        @foreach ($centros as $centro)
                            <option value="{{ $centro->id }}">{{ $centro->nombre }}</option>
                        @endforeach

                    </select>
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">Grupo:</p>
                    <select id="id_grupoeditar" name="id_grupoeditar" required>
                        <option value="" disabled selected>Seleccione un Grupo</option>
                    </select>
                </div>

            </div>
            <div class="botones">
                <button type="submit" id="" class="btn-aceptar"><img src="{{ asset('img/aceptar.svg') }}"
                        alt=""></button>
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
    }

    .modaledit {
        display: none;
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
    .modal-contentedit {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: auto;
        max-width: 1000px;
        border-radius: 10px;
        flex-direction: column
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
        padding: 10px 5px;
        border-radius: 4px;
        background: var(--background-inputs);
        margin-left: 5px;
        margin-right: 10px
    }

    .input-group1.select {
        border: 1px solid var(--borde);
        border-radius: 4px;
        background: var(--background-inputs);
    }
</style>
