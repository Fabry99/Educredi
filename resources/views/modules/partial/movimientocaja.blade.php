<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Ingreso de pagos</h1>
            <div class="input-group">
                    <label for="fecha" class="label1">
                        <input type="date" id="fecha" name="fecha" placeholder="Fecha:">
                    </label>
                    <label for="fcontable" class="label1">
                        <input type="date" id="fcontable" name="fcontable" placeholder="F.Contable:">
                    </label>
                    <label for="fabono" class="label1">
                        <input type="date" id="fabono" name="fabono" placeholder="F.Abono:">
                    </label>
                    <label for="comprobante" class="label1">
                        <input type="" id="comprobante" name="comprobante" placeholder="Comprobante:">
                    </label>
                    <label>
                        <input type="radio" name="tipo_calculo" value="calculo">Aplicar Valor Cálculo
                    </label>
                    <label style="margin-left: 10px;">
                        <input type="radio" name="tipo_calculo" value="fijo">
                        Aplicar Valor Fijo
                    </label>
                    <label>
                        <input type="checkbox" name="habilitar_manual">
                        Habilitar Cálculo Manual
                    </label>
            </div>
            <div class="modal-ge select">
                <div class="input-group select">
                    <select id="id_sfdm" name="id_sfdm" required>
                        <option value="" disabled selected>SFDM</option>
                        <option value="Opcion1">Opcion1</option>      
                    </select>
                </div>
                <div class="input-group select">
                    <select id="id_grupo" name="id_grupo" required>
                        <option value="" disabled selected>Grupo</option>
                        <option value="Opcion1">Opcion1</option>      
                    </select>
                </div>
                <div class="input-group select">
                    <select id="id_cuenta" name="id_cuenta" required>
                        <option value="" disabled selected>Cuenta</option>
                        <option value="Opcion1">Opcion1</option>      
                    </select>
                </div>
                <div class="input-group">
                    <label for="cuotatotal" class="label1">
                        <input type="" id="cuotatotal" name="cuotatotal" placeholder="Cuota Total:">
                    </label>
                    <label for="numcuota" class="label1">
                        <input type="" id="numcuota" name="numcuota" placeholder="Numero Cuota:">
                    </label>
                </div>
            </div>
            <div class="input-group" style="margin-top: 10px;">
                <label for="comprobante" class="label1">
                    <input type="" id="comprobante" name="comprobante" placeholder="Saldo total:">
                </label>
            </div>
            <div class="btn-grupos" style="display: flex; margin-bottom: 10px; margin-left: 10px;">
                <a href="#" id="openModalBtn" class="btn-agregar"
                    style="margin-right: 15px;"><span>Confirmar</span></a>
                <a href="#" id="openModalBtn" class="btn-agregar"
                    style="margin-right: 15px;"><span>Reimprimir</span></a>
                <a href="#" id="openModalBtn" class="btn-agregar"
                    style="margin-right: 15px;"><span>Debe ser</span></a>
                <a href="#" id="openModalBtn" class="btn-agregar"
                    style="margin-right: 15px;"><span>Est. Cuenta</span></a>
                <a href="#" id="openModalBtn" class="btn-agregar"
                    style="margin-right: 15px;"><span>Actualizar</span></a>
            </div>

            <table id="mitabla" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Saldo</th>
                        <th>Ultim Movimiento</th>
                        <th>Poximo Pago</th>
                        <th>Valor</th>
                        <th>Int. Normal</th>
                        <th>Int. Morator</th>
                        <th>Micro seg</th>
                        <th>Seguro</th>
                        <th>IVA</th>
                        <th>Capital</th>
                        <th>Frecuencia</th>
                        <th>Apertura</th>
                        <th>Vencimiento</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nombre</td>
                        <td>zxczxczxczxcasdasdasdasdasdasd</td>
                        <td>Saldo</td>
                        <td>Ultim Movimiento</td>
                        <td>Poximo Pago</td>
                        <td>Valor</td>
                        <td>Int. Normal</td>
                        <td>Int. Morator</td>
                        <td>Micro seg</td>
                        <td>Seguro</td>
                        <td>IVA</td>
                        <td>Capital</td>
                        <td>Frecuencia</td>
                        <td>Apertura</td>
                        <td>Vencimiento</td>
                    </tr>
                   
                </tbody>
                
            </table>
        </div>
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
        display: flex;
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
