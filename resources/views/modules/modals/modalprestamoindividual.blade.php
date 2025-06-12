<div id="modalprestamoIndividual" class="modalPrestamoIndividual">
    <!-- Contenido del Modal -->
    <div class="modal-contentPrestamosGrupal">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo" width="80" height="80">
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
        <h2>Desembolso de Préstamo Individual</h2>
        </div>
        <form action="" method="POST" id="formPrestamoIndividual">
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <hr class="separate-line">

            <!-- Datos del Préstamo -->
            <div class="datos-prestamos ">
                <div class="modal-gePrestamos" style="margin-top: 40px">
                    <div class="input-group1">
                        <label for="id" class="label1">Código:
                            <input type="text" id="id_ind" name="id" placeholder="ID:" readonly
                                style="width: 20%">
                        </label>
                    </div>
                    <div class="input-group1" style="margin-left: -80px;">
                        <label for="nombre" class="label1">Nombre:
                            <input type="text" id="nombre_ind" name="nombre" placeholder="NOMBRE:" readonly
                                style="width: 280px;">
                        </label>
                    </div>
                    <div class="input-group1">
                        <p style="white-space:nowrap;">Linea:</p>
                        <select id="lineaind" name="linea" style="width:200px; margin-right:50px; margin-left:5px;"
                            required>
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
                        <select id="sucursalind" name="sucursal" style="width:160px; margin-left:5px;">
                            <option value="" disabled selected>Seleccionar:</option>
                            @foreach ($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group1" style="margin-left: 20px">
                        <p style="white-space:nowrap;">Supervisor:</p>
                        <select id="supervisorind" name="supervisor"
                            style="width:200px; margin-right:50px; margin-left:5px;">
                            <option value="" disabled selected>Seleccionar:</option>
                            @foreach ($supervisor as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group1" style="margin-left: 20px">
                        <p style="white-space:nowrap;">Asesor:</p>
                        <select id="asesorind" name="asesor" style="width:200px; margin-right:50px; margin-left:5px;">
                            <option value="" disabled selected>Seleccionar:</option>
                            @foreach ($asesores as $asesor)
                                <option value="{{ $asesor->id }}">{{ $asesor->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-gePrestamos"
                    style="border: 1px solid var(--border-color-datosprestamos);
                    background:var(--background-datosprestamos); border-radius:4px; margin:20px 0px;">
                    <div class="group-datosPrestamos">
                        <div class="input-group1" style="margin: 10px 5px; ">
                            <label for="montootorgarind" class="label1">Monto a Otorgar:
                                <input type="number" id="montootorgarind" name="montootorgar" placeholder="0.00"
                                    style="width: 120px;" required step="0.01" min="0">
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="tasainteres" class="label1">Tasa de Interés:
                                <input type="text" id="tasainteresind" name="tasainteres" placeholder="0.00"
                                    style="width: 120px; margin-left:10px" required>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="plazo" class="label1">Plazo:
                                <input type="number" id="plazoind" name="plazo" placeholder="0"
                                    style="width: 120px; margin-left:80px" required>
                            </label>
                        </div>
                        <div class="input-group1" style="margin: 10px 5px; ">
                            <p style="white-space:nowrap;">Forma de Pago:</p>
                            <select id="tipo_pago" name="tipo_pago" style="width:142px; margin-left:15px;">
                                <option value="" disabled selected>Seleccionar:</option>
                                @foreach ($tipopago as $tipopago)
                                    <option value="{{ $tipopago->id }}">{{ $tipopago->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group1" style=" margin: 0px 5px; ">
                            <label for="frecuenciameses" class="label1">Frecuencia <br> en Meses:
                                <input type="number" id="frecuenciamesesind" name="frecuenciameses" placeholder="0"
                                    style="width: 120px; margin-left:50px">
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 0px 5px 20px 5px;">
                            <label for="frecuenciadias" class="label1">Frecuencia <br> en Días:
                                <input type="number" id="frecuenciadiasind" name="frecuenciadias" placeholder="0"
                                    style="width: 120px; margin-left:65px">
                            </label>
                        </div>
                        <div class="input-group1" style="margin: 20px 5px;">
                            <input type="checkbox" name="microseguro" id="microseguroind" value="1" checked
                                disabled style="width: 16px; height: 16px; margin-left:10px;">
                            <span style="margin-left:-10px;">Cobrar Micro Seguro</span>
                        </div>
                    </div>

                    <div class="group-datosPrestamos">
                        <div class="input-group1" style="margin: 10px 5px; ">
                            <label for="fechaapertura" class="label1">Fecha Apertura:
                                <input type="date" id="fechaaperturaind" name="fechaapertura"
                                    style="width: 120px;" required>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="fechaprimerpagodebeser" class="label1">Fecha Primer <br> Pago:
                                <input type="date" id="fechaprimerpagodebeserind" name="fechaprimerpagodebeser"
                                    style="width: 120px; margin-left:75px" required>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="fechavencimiento" class="label1">Fecha <br> Vencimiento:
                                <input type="date" id="fechavencimientoind" name="fechavencimiento"
                                    style="width: 120px; margin-left:22px; color:red" required>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 0px 5px 20px 5px;">
                            <label for="cuota" class="label1">Cuota:
                                <input type="number" id="cuotaind" name="cuotaind" placeholder="0.00"
                                    style="width: 120px; margin: 10px 0px 0px 68px" required step="0.01"
                                    min="0" readonly>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 0px 5px 20px 5px;">
                            <label for="desembolso" class="label1">Desembolso:
                                <input type="number" id="desembolsoind" name="desembolso" placeholder="0.00"
                                    style="width: 120px; margin: 5px 0px 0px 23px" required step="0.01"
                                    min="0" readonly>
                            </label>
                        </div>
                        <div class="input-group1" style="margin: 20px 5px;">
                            <span>Tipo Cuota:</span>
                            <input type="radio" name="Tipo_cuota" id="cuotafijaind" value="1"
                                style="width: 16px; height: 16px; margin-left:20px;" checked>
                            <span style="margin-left:-10px;">Fija</span>
                            <input type="radio" name="Tipo_cuota" id="cuotavariableind" value="2"
                                style="width: 16px; height: 16px; margin-left:20px;">
                            <span style="margin-left:-10px;">Variable</span>
                        </div>

                    </div>
                    <div class="group-datosPrestamos">
                        <div class="title-garantia" style="margin: 10px 5px">
                            <span style="font-weight: bold">Tipo de Garantías Ofrecidas</span>
                        </div>
                        <div class="modal-gePrestamos">
                            <div class="input-group1" style="margin-top:20px; margin-left:5px">
                                <input type="radio" name="garantia_ind" id="fiduciariaind" value="7"
                                    style="width: 16px; height: 16px; margin-left:10px;">
                                <span style="margin-left:-10px;">Fiduciaria</span>
                            </div>
                            <div class="input-group1" style="margin-top:20px; margin-left:10px;">
                                <input type="radio" name="garantia_ind" id="hipotecariaind" value="3"
                                    style="width: 16px; height: 16px; margin-left:0px;">
                                <span style="margin-left:-10px;">Hipotecaria</span>
                            </div>

                        </div>

                        <div class="modal-gePrestamos">

                            <div class="input-group1" style="margin: 10px 5px;">
                                <input type="radio" name="garantia_ind" id="prendariaind" value="4"
                                    style="width: 16px; height: 16px; margin-left:0px;">
                                <span style="margin-left:-10px;">Prendaria</span>
                            </div>
                        </div>
                        <hr>
                        <div class="input-group1" style="margin: 20px 5px; ">
                            <label for="colector" class="label1">Colector:
                                <select id="colectorind" name="colector" style="width:200px; margin-left:42px;">
                                    <option value="" disabled selected>Seleccionar:</option>
                                    @foreach ($colector as $colector)
                                        <option value="{{ $colector->id }}">{{ $colector->nombrecolector }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <div class="input-group1" style=" margin: 10px 5px; ">
                            <label for="aprobadorpor" class="label1">Aprobado por:
                                <select id="aprobadoporind" name="aprobadopor" style="width:200px; margin-left:5px;">
                                    <option value="" disabled selected>Seleccionar:</option>
                                    @foreach ($aprobaciones as $aprobaciones)
                                        <option value="{{ $aprobaciones->id }}">{{ $aprobaciones->nombre }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <div class="input-group1" style="margin: 20px 5px; ">
                            <label for="banco" class="label1">Banco:
                                <select id="bancoind" name="banco" style="width:200px; margin-left:56px;">
                                    <option value="" disabled selected>Seleccionar:</option>
                                    @foreach ($bancos as $banco)
                                        <option value="{{ $banco->id }}">{{ $banco->nombre_banco }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>


                        <div class="input-group1" style="margin: 20px 5px; ">
                            <label for="formapagoind" class="label1">Forma de <br> Pago:
                                <select id="formapagoind" name="formapagoind" style="width:200px; margin-left:65px;">
                                    <option value="" disabled selected>Seleccionar:</option>
                                    @foreach ($formapago as $item_formapago)
                                        <option value="{{ $item_formapago->id }}">
                                            {{ $item_formapago->nombre_formapago }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>


                    </div>

                </div>
                <div class="botones">
                    <button type="submit" class="btn-aceptarPrestamo" id="btnAceptarPrestamo"><img
                            src="{{ asset('img/aceptar.svg') }}" alt=""></button>
                </div>
            </div>
        </form>
    </div>
</div>

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

    .modalPrestamoIndividual {
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

    .btn-aceptarPrestamo {
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

    .btn-aceptarPrestamo:hover {
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
</style>
