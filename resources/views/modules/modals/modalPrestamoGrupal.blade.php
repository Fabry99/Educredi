<div id="modalprestamogrupal" class="modalPrestamosGrupal">
    <!-- Contenido del Modal -->
    <div class="modal-contentPrestamosGrupal">
        <span class="close-btn1">&times;</span>
        <h2>Desembolso de Préstamos</h2>
        <form action="" method="POST">
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="nav-links">
                <a href="#" id="link-datos" class="active">Datos del Préstamo</a>
                <a href="#" id="link-garantias">Garantías y Fiadores</a>
            </div>
            <hr class="separate-line">

            <!-- Datos del Préstamo -->
            <div class="datos-prestamos seccion visible">
                <div class="modal-gePrestamos">
                    <div class="input-group1">
                        <label for="id" class="label1">Código:
                            <input type="text" id="id" name="id" placeholder="ID:" readonly
                                style="width: 20%">
                        </label>
                    </div>
                    <div class="input-group1" style="margin-left: -80px;">
                        <label for="nombre" class="label1">Nombre:
                            <input type="text" id="nombre" name="nombre" placeholder="NOMBRE:" readonly
                                style="width: 280px;">
                        </label>
                    </div>
                    <div class="input-group1">
                        <p style="white-space:nowrap;">Linea:</p>
                        <select id="linea" name="linea" style="width:200px; margin-right:50px; margin-left:5px;" required>
                            <option value="" disabled selected>Seleccionar:</option>
                            @foreach ($linea as $item)
                                <option value="{{ $item->id }}" data-interes="{{ $item->tasa_interes }}">
                                    {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-gePrestamos" style="margin-top: 20px;">
                    <div class="input-group1" style="margin-left: 60px">
                        <p style="white-space:nowrap;">Sucursal:</p>
                        <select id="sucursal" name="sucursal" style="width:160px; margin-left:5px;">
                            <option value="" disabled selected>Seleccionar:</option>
                            @foreach ($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group1" style="margin-left: 20px">
                        <p style="white-space:nowrap;">Supervisor:</p>
                        <select id="supervisor" name="supervisor"
                            style="width:200px; margin-right:50px; margin-left:5px;">
                            <option value="" disabled selected>Seleccionar:</option>
                            @foreach ($supervisor as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->nombre }}</option>
                            @endforeach
                        </select>
                    </div>


                </div>
                <div class="modal-gePrestamos" style="margin-top:20px;">
                    <div class="input-group1">
                        <p style="white-space:nowrap;">Centro:</p>
                        <select id="centro" name="centro" style="width:200px; margin-left:5px;" required>
                            <option value="" disabled selected>Centro:</option>
                        </select>
                    </div>
                    <div class="input-group1">
                        <p style="white-space:nowrap;">Grupo:</p>
                        <select id="grupo" name="grupo" style="width:200px; margin-left:5px;" required>
                            <option value="" disabled selected>Grupo:</option>
                        </select>
                    </div>


                </div>
                <div class="modal-gePrestamos"
                    style="border: 1px solid var(--border-color-datosprestamos);
                    background:var(--background-datosprestamos); border-radius:4px; margin:20px 0px;">
                    <div class="group-datosPrestamos">
                        <div class="input-group1" style="margin: 10px 5px; ">
                            <label for="montootorgar" class="label1">Monto a Otorgar:
                                <input type="number" id="montootorgar" name="montootorgar" placeholder="0.00"
                                    style="width: 120px;" required step="0.01" min="0">
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="tasainteres" class="label1">Tasa de Interés:
                                <input type="text" id="tasainteres" name="tasainteres" placeholder="0.00"
                                    style="width: 120px; margin-left:10px" required>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="cantPagos" class="label1">Pagos:
                                <input type="number" id="cantPagos" name="cantPagos" placeholder="0"
                                    style="width: 120px; margin-left:75px" required>
                            </label>
                        </div>
                        <div class="input-group1" style="margin: 10px 5px; ">
                            <p style="white-space:nowrap;">Forma de Pago:</p>
                            <select id="formaPago" name="formaPago" style="width:142px; margin-left:15px;">
                                <option value="" disabled selected>Seleccionar:</option>
                                @foreach ($tipopago as $tipopago)
                                    <option value="{{ $tipopago->id }}">{{ $tipopago->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="group-datosPrestamos">
                        <div class="input-group1" style="margin: 10px 5px; ">
                            <label for="fechaapertura" class="label1">Fecha Apertura:
                                <input type="date" id="fechaapertura" name="fechaapertura" style="width: 120px;"
                                    required>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="fechaprimerpagointereses" class="label1">Primer Pago <br>(Interes):
                                <input type="date" id="fechaprimerpagointereses" name="fechaprimerpagointereses"
                                    style="width: 120px; margin-left:50px" required>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="fechaprimerpagodebeser" class="label1">Primer Pago <br>(Debe Ser):
                                <input type="date" id="fechaprimerpagodebeser" name="fechaprimerpagodebeser"
                                    style="width: 120px; margin-left:30px" required>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="fechavencimiento" class="label1">Fecha <br> Vencimiento:
                                <input type="date" id="fechavencimiento" name="fechavencimiento"
                                    style="width: 120px; margin-left:22px; color:red" required>
                            </label>
                        </div>
                    </div>
                    <div class="group-datosPrestamos">
                        <div class="input-group1" style="margin: 20px 5px; ">
                            <label for="colector" class="label1">Colector:
                                <select id="colector" name="colector" style="width:200px; margin-left:42px;">
                                    <option value="" disabled selected>Seleccionar:</option>
                                    @foreach ($colector as $colector)
                                        <option value="{{ $colector->id }}">{{ $colector->nombrecolector }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="aprobadorpor" class="label1">Aprobado por:
                                <select id="aprobadopor" name="aprobadopor" style="width:200px; margin-left:5px;">
                                    <option value="" disabled selected>Seleccionar:</option>
                                    @foreach ($aprobaciones as $aprobaciones)
                                        <option value="{{ $aprobaciones->id }}">{{ $aprobaciones->nombre }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <div class="input-group1" style="margin: 20px 5px;">
                            <input type="checkbox" name="microseguro" id="microseguro" value="1" checked
                                style="width: 16px; height: 16px; margin-left:10px;">
                            <span style="margin-left:-10px;">Cobrar Micro Seguro</span>
                        </div>

                    </div>

                </div>

            </div>



            <!-- Garantías y Fiadores -->
            <div class="datos-Garantias-Fiadores seccion ">
                <span style="margin-left:5px; 
                font-weight: bold ">GARANTIAS</span>
                <div class="modal-gePrestamos">
                    <div class="input-group1" style="margin-top:20px; margin-left:40px">
                        <input type="radio" name="garantia" id="pagare" value="1"
                            style="width: 16px; height: 16px; margin-left:10px;">
                        <span style="margin-left:-10px;">PAGARE</span>
                    </div>
                    <div class="input-group1" style="margin-top:20px; margin-left:50px;">
                        <input type="radio" name="garantia" id="aportaciones" value="4"
                            style="width: 16px; height: 16px; margin-left:10px;">
                        <span style="margin-left:-10px;">APORTACIONES</span>
                    </div>
                    <div class="input-group1" style="margin: 20px 5px;">
                        <input type="radio" name="garantia" id="fiduciaria" value="7"
                            style="width: 16px; height: 16px; margin-left:10px;">
                        <span style="margin-left:-10px;">FIDUCIARIA O APORTACIÓN</span>
                    </div>
                </div>
                <div class="modal-gePrestamos" style="margin-left: -140px; margin-bottom:20px;">
                    <div class="input-group1" style="margin-top:20px; margin-right:22px">
                        <input type="radio" name="garantia" id="solidaria" value="2"
                            style="width: 16px; height: 16px; margin-left:10px;">
                        <span style="margin-left:-10px;">SOLIDARIA</span>
                    </div>
                    <div class="input-group1" style="margin-top:20px; margin-left:12px">
                        <input type="radio" name="garantia" id="prendaria" value="5"
                            style="width: 16px; height: 16px; margin-left:10px;">
                        <span style="margin-left:-10px;">PREDARIA</span>
                    </div>

                </div>
                <div class="modal-gePrestamos" style="margin-left: -105px; margin-top:25px;">
                    <div class="input-group1" style="margin: 20px 5px;">
                        <input type="radio" name="garantia" id="hipotecaria" value="3"
                            style="width: 16px; height: 16px; margin-left:10px;">
                        <span style="margin-left:-10px;">HIPOTECARIA</span>
                    </div>
                    <div class="input-group1" style="margin: 20px 5px;">
                        <input type="radio" name="garantia" id="depositoplazos" value="6"
                            style="width: 16px; height: 16px; margin-left:10px;">
                        <span style="margin-left:-10px;">DEPOSITO A PLAZO</span>
                    </div>

                </div>
                <hr>
                <div class="tabla-miembrosgrupo">
                    <span>MIEMBROS DEL GRUPO</span>
                </div>
                <div id="contenedorMiembrosGrupo" style="margin-top: 10px;"></div>
                <div class="total"
                    style="display: flex; justify-content: center; margin-top: 20px; 
                justify-items: center; align-items: center;">
                    <span>Total:</span>
                    <input type="text" id="total" name="total" placeholder="0.00" readonly
                        style="width: 80px; height: 20px; margin-left:10px; margin-right:20px;
                        background: var(--background-inputs); border: 1px solid var(--borde); font-size: 15px;
                        border-radius:4px; text-align: right;">
                </div>
                <div class="botones">
                    <button type="submit" class="btn-aceptar" id="btnAceptar"><img src="{{ asset('img/aceptar.svg') }}"
                            alt=""></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    const links = document.querySelectorAll('.nav-links a');
    const secciones = document.querySelectorAll('.seccion');


    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // Quitar clase .active de todos los enlaces
            links.forEach(l => l.classList.remove('active'));
            this.classList.add('active'); // Agregar al que se clickeó

            // Ocultar todas las secciones
            secciones.forEach(sec => sec.classList.remove('visible'));

            // Mostrar la sección correspondiente
            if (this.id === 'link-datos') {
                document.querySelector('.datos-prestamos').classList.add('visible');
            } else if (this.id === 'link-garantias') {
                document.querySelector('.datos-Garantias-Fiadores').classList.add('visible');
            }
        });
    });
</script>
<style>
    :root {
        --background-inputs: #f7f7f7;
        --borde: #ccc;
        --verde-shadow: rgba(43, 255, 0, 0.938);
        --border-color-input: #159109;
        --border-color-datosprestamos: #717171ae;
        --background-datosprestamos: #f0f0f07a;
        --background-color-verde: #0b7914;
    }

    .modalPrestamosGrupal {
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

    .modal-contentPrestamosGrupal {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        max-width: 1000px;
        border-radius: 10px;
    }

    .modal-contentPrestamosGrupal h2 {
        text-align: center;
        font-size: 28px;
    }

    .modal-gePrestamos {
        display: flex;
        width: 100%;
        margin-top: 10px;
        flex-wrap: wrap;
        gap: 1rem;
        box-sizing: border-box;
        justify-content: center;
    }

    .modal-gePrestamos select {
        width: 100%;
        padding: 10px 15px;
        border-radius: 4px;
        background: #fff;
    }


    .input-group1.select {
        border: 1px solid var(--borde);
        border-radius: 4px;
        background: #fff;
    }

    .input-group1 {
        display: flex;
        flex-direction: row;
    }

    .input-group1 input {
        font-size: 16px;
        margin-top: 5px;
        background: white;
        border: 2px solid var(--borde);
        border-radius: 4px;
        padding: 10px 10px;
        margin: 0px 20px 0px 5px;
    }

    .input-group1 input:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }

    .input-group1 select:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }

    .group-datosPrestamos {
        border: 1px solid #717171ae;
        margin: 25px 0px;

    }

    .tabla-miembrosgrupo {
        width: 100%;
        margin-top: 20px;
    }

    .tabla-miembrosgrupo span {
        font-weight: bold;
        margin-left: 5px;
        margin-bottom: 20px;
    }

    .botones {
        display: flex;
        justify-content: right;
        margin-top: 20px;
    }

    .btn-aceptar {
        background: var(--background-color-verde);
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
        background: #03880e;
        box-shadow: 0 0 5px var(--verde-shadow);
    }

    .nav-links {
        justify-content: center;
        align-items: center;
        display: flex;
    }

    .nav-links a {
        width: 100%;
        margin-right: 15px;
        cursor: pointer;
        text-decoration: none;
        font-weight: bold;
        color: #555;
        padding: 5px 10px;
        border-radius: 5px;
        text-align: center;
    }

    .nav-links a:hover {
        width: 100%;
        padding: 20px;
        background-color: #f0f0f0;
        color: var(--background-color-verde);
        margin-left: 10px;
    }

    .nav-links a.active {
        width: 100%;
        margin: 0px 0px 10px 0px;
        background-color: var(--background-color-verde);
        color: white;
        padding: 20px
    }

    hr.separate-line {
        margin: 0px 0px 20px 0px;
    }


    .seccion {
        display: none;
        margin-top: 20px;
    }

    .seccion.visible {
        display: block;
    }
</style>
