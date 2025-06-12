<!-- El Modal -->
<div id="myModal" class="modal">
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" width="80" height="80">
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
            <h2>Agregar Cliente</h2>
        </div>
        <form action="{{ route('clientes.saveclient') }}" method="POST">
            @csrf
            <div class="modal-ge" style="margin-top: 45px;">
                <div class="input-group">
                    <label for="nombre" class="label1">
                        <input type="nombre" id="nombre" name="nombre" placeholder="Nombre:">
                    </label>
                </div>
                <div class="input-group">
                    <label for="apellido" class="label1">
                        <input type="apellido" id="apellido" name="apellido" placeholder="Apellidos:">
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="direccion" class="label1">
                        <input type="text" id="direccion" name="direccion" placeholder="Dirección:">
                    </label>
                </div>
                <div class="input-group">
                    <label for="teloficina" class="label1">
                        <input type="text" id="teloficina" name="teloficina" placeholder="Telefono Oficina:"
                            pattern="\d{8}" title="El teléfono debe tener exactamente 8 dígitos">
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="dir_negocio" class="label1">
                        <input type="text" id="dir_negocio" name="dir_negocio" placeholder="Dirección Negocio:">
                    </label>
                </div>
                <div class="input-group">
                    <label for="sector" class="label1">
                        <input type="text" id="sector" name="sector" placeholder="Sector:">
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="actividadeconomica" class="label1">
                        <input type="nombre" id="actividadeconomica" name="actividadeconomica"
                            placeholder="Actividad Economica:">
                    </label>
                </div>
                <div class="input-group">
                    <label for="NIT" class="label1">
                        <input type="text" id="NIT" name="NIT" placeholder="NIT: 1234-567891-234-5"
                            pattern="\d{4}-\d{6}-\d{3}-\d{1}"
                            title="Formato: 4 dígitos - 6 dígitos - 3 dígitos - 1 dígito (Ej: 1234-567891-234-5)">
                    </label>
                </div>
            </div>
            <div class="modal-ge select">
                <div class="input-group select">
                    <select id="id_departamento" name="id_departamento" required>
                        <option value="" disabled selected>Seleccione un Departamento</option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="input-group select" style="margin-left: 40px;">
                    <select id="id_municipio" name="id_municipio" required>
                        <option value="" disabled selected>Seleccione un Municipio</option>
                    </select>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="ocupacion" class="label1">
                        <input type="text" id="ocupacion" name="ocupacion" placeholder="Ocupación:">
                    </label>
                </div>
                <div class="input-group select" style="margin-left: 20px;">
                    <select id="firma" name="firma">
                        <option value="" disabled selected>Puede Firmar ?</option>
                        <option value="si">Si</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>
            <h3>Datos Personales</h3>

            <div class="modal-ge">

                <div class="input-group">
                    <label for="dui" class="label2">
                        <input type="text" id="dui" name="dui" placeholder="DUI: 12345678-9"
                            pattern="\d{8}-\d{1}" title="Formato: 8 dígitos - 1 dígito (Ej: 12345678-9)" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="expedida" class="label2">
                        <input type="text" id="expedida" name="expedida" placeholder="Expedida En:">
                    </label>
                </div>

                <div class="input-group">
                    <label for="expedicion" class="label2" style="font-size: 12px">Fecha Expedición:
                        <input type="date" id="expedicion" name="expedicion" required>
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="lugarnacimiento" class="label2">
                        <input type="text" id="lugarnacimiento" name="lugarnacimiento"
                            placeholder="Lugar de Nacimiento:">
                    </label>
                </div>
                <div class="input-group">
                    <label for="nacionalidad" class="label2">
                        <input type="text" id="nacionalidad" name="nacionalidad" placeholder="Nacionalidad:">
                    </label>
                </div>
                <div class="input-group select" style="margin-left: 20px;">
                    <select id="genero" name="genero">
                        <option value="" disabled selected>Genero:</option>
                        <option value="masculino">masculino</option>
                        <option value="femenino">femenino</option>

                    </select>
                </div>


            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="fecha_nacimiento" class="label2" style="font-size: 12px">Fecha Nacimiento:
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                            placeholder="F.Nacimiento:" required>
                    </label>
                </div>
                <div class="input-group">
                    <label for="telcasa" class="label2">
                        <input type="text" id="telcasa" name="telcasa" placeholder="Tel.Casa:" pattern="\d{8}"
                            title="El teléfono debe tener exactamente 8 dígitos">
                    </label>
                </div>
                <div class="input-group select" style="margin-left: 20px;">
                    <select id="estado_civil" name="estado_civil" required>
                        <option value="" disabled selected>Estado Civil:</option>
                        <option value="soltero">soltero</option>
                        <option value="casado">casado</option>
                        <option value="divorciado">divorciado</option>
                        <option value="viudo">viudo</option>
                    </select>
                </div>



            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="nrc" class="label2">
                        <input type="text" id="nrc" name="nrc" placeholder="NRC:" pattern="\d{22}"
                            title="Ingrese el Código de 22 Dígitos">
                    </label>
                </div>
                <div class="input-group">
                    <label for="perdependiente" class="label2">
                        <input type="text" id="perdependiente" name="perdependiente"
                            placeholder="Personas Dependientes:" min="1" max="20"
                            title="Se Debe Agregar la Cantidad de Personas Dependientes">
                    </label>
                </div>
                <div class="input-group">
                    <label for="conyugue" class="label2">
                        <input type="text" id="conyugue" name="conyugue" placeholder="Conyugue:">
                    </label>
                </div>

            </div>
            <div class="modal-ge">
                <div class="input-group">
                    <label for="sueldo" class="label2">
                        <input type="number" id="sueldo" name="sueldo" placeholder="Sueldo: 0.0"
                            step="0.01" min="0">

                    </label>
                </div>
                <div class="input-group">
                    <label for="otroingreso" class="label2">
                        <input type="text" id="otroingreso" name="otroingreso" placeholder="Otr.Ingreso: 0.0"
                            step="0.01" min="0">
                    </label>
                </div>
                <div class="input-group">
                    <label for="egreso" class="label2">
                        <input type="number" id="egreso" name="egreso" placeholder="Egreso: 0.0"
                            step="0.01" min="0">
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
    document.getElementById("id_departamento").addEventListener("change", function() {
        var departamentoId = this.value;

        // Limpiar el select de municipios
        var municipioSelect = document.getElementById("id_municipio");
        municipioSelect.innerHTML =
            '<option value="" disabled selected>Seleccione un Municipio</option>'; // Reset

        // Solo hacer la solicitud si se seleccionó un departamento
        if (departamentoId) {
            fetch(`/municipios/${departamentoId}`)
                .then(response => response.json())
                .then(data => {
                    // Agregar los municipios del departamento seleccionado
                    data.forEach(function(municipio) {
                        var option = document.createElement("option");
                        option.value = municipio.id;
                        option.text = municipio.nombre;
                        municipioSelect.appendChild(option);
                    });
                })
                .catch(error => {});
        }
    });

    function formatDate(date) {
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }

    window.addEventListener("DOMContentLoaded", function() {
        const today = new Date();

        // Persona debe tener al menos 18 años => Fecha máxima = hoy - 18 años
        const maxBirthDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());

        // Por si quieres un límite inferior (por ejemplo, no más de 100 años de edad)
        const minBirthDate = new Date(today.getFullYear() - 100, today.getMonth(), today.getDate());

        // Fecha de expedición también debe ser hasta hoy (sin futuro)
        const maxToday = formatDate(today);

        // Aplicar a los campos correspondientes
        const fechaNacimientoInput = document.getElementById("fecha_nacimiento");
        const expedicionInput = document.getElementById("expedicion");

        if (fechaNacimientoInput) {
            fechaNacimientoInput.setAttribute("max", formatDate(maxBirthDate));
            fechaNacimientoInput.setAttribute("min", formatDate(minBirthDate));
        }

        if (expedicionInput) {
            expedicionInput.setAttribute("max", maxToday);
        }
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
</style>
