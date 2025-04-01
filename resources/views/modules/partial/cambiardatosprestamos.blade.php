
<div class="container">
    <div class="main-content">
        <h1>Cambiar datos de préstamo.</h1>
        <div class="fila">
            <div class="columna">
                <div class="container-select">
                    <div class="titulo">
                        <label for="asesor" class="label1">Asesor</label>
                    </div>
                    <select name="opciones" id="opciones">
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="opcion1">Opcion 1</option>
                        <option value="opcion2">Opcion 2</option>
                    </select>
                    <span class="titulo"></span>
                    <i class="fa-solid fa-chevron-down icon"></i>
                </div>
                <div class="container-select">
                    <div class="titulo">
                        <label for="centro" class="label1">Centro</label>
                    </div>
                    <select name="opciones" id="opciones">
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="opcion1">Opcion 1</option>
                        <option value="opcion2">Opcion 2</option>
                    </select>
                    <span class="titulo"></span>
                    <i class="fa-solid fa-chevron-down icon"></i>
                </div>
                <div class="container-select">
                    <div class="titulo">
                        <label for="gruposolidario" class="label1">Grupo solidario</label>
                    </div>
                    <select name="opciones" id="opciones">
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="opcion1">Opcion 1</option>
                        <option value="opcion2">Opcion 2</option>
                    </select>
                    <span class="titulo"></span>
                    <i class="fa-solid fa-chevron-down icon"></i>
                </div>
                <div class="fila">
                    <div class="campos">
                        <label for="apertura-actual" class="label1">Apertura actual</label>
                        <label for="apertura-actual" class="label1">
                            <input type="date" id="apertura" name="apertura" placeholder="/ /" required>
                        </label>
                    </div>
                    <button class="btn-verificar">
                        Verificar
                    </button>
                </div>
            </div>
            <div class="columna">
                <div class="campos" style="margin-left: 200px">
                    <label for="aperturaactual" class="label1">Apertura Actual</label>
                    <label for="aperturaactual" class="label1">
                        <input type="date" id="aperturaactual" name="aperturaactual" placeholder="/ /" required>
                    </label>
                    <label for="primerpago" class="label1">Primer Pago</label>
                    <label for="primerpago" class="label1">
                        <input type="date" id="primerpago" name="primerpago" placeholder="/ /" required>
                    </label>
                    <label for="vencimiento" class="label1">Vencimiento</label>
                    <label for="vencimiento" class="label1">
                        <input type="date" id="vencimiento" name="vencimiento" placeholder="/ /" required>
                    </label>
                    <button class="btn-verificar" style="margin-left: 60px; background: blue;">
                        Aplicar cambios
                    </button>
                </div>
            </div>
        </div>
        <div class="campo-verificar">
            <textarea rows="15" cols="60"></textarea>
        </div>
    </div>
</div>
