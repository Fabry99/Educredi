<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
<div class="container">
    <div class="main-content">
        <h1 style="margin-bottom: 40px">Transferencia de cartera entre asesores</h1>
        <div class="fila">
            <div class="titulo">
                <label for="asesor" class="label1"><b>Seleccione el asesor que posee la cartera a transferir</b></label>
            </div>
            <div class="container-select" style="margin-left: 20px; margin-top: -10px;">
                <select name="opciones" id="opciones">
                    <option value="" disabled selected>Seleccione un asesor</option>
                    <option value="opcion1">Opcion 1</option>
                    <option value="opcion2">Opcion 2</option>
                </select>
                <span class="titulo"></span>
                <i class="fa-solid fa-chevron-down icon"></i>
            </div>
        </div>
        <div class="fila">
            <div class="titulo">
                <label for="asesor" class="label1">Número de créditos encontrados:</label>
            </div>
            <div class="campos" style="margin-top: -15px; margin-left: 10px;">
                <div class="campos-desactivados">
                        <input type="number" id="cuota" name="cuota" disabled>
                </div>
            </div>
            <div class="titulo" style="margin-left: 30px;">
                <label for="asesor" class="label1">Monto total de créditos encontrados:</label>
            </div>
            <div class="campos" style="margin-top: -15px; margin-left: 10px;">
                <div class="campos-desactivados">
                        <input type="number" id="cuota" name="cuota" disabled>
                </div>
            </div>
        </div>
        <div class="columna">
            <div class="container-select" style="margin-left: 20px; margin-top: -10px;">
                    <select name="opciones" id="opciones">
                        <option value="opcion1">Grupales</option>
                        <option value="opcion2">Opcion 1</option>
                        <option value="opcion3">Opcion 2</option>
                    </select>
                    <span class="titulo"></span>
                    <i class="fa-solid fa-chevron-down icon"></i>
            </div>
            <div class="check-container" style="margin-bottom: 30px;">
                    <input type="checkbox" id="miCheckbox">Desmarcar todos</label>
            </div>
        </div>
        <div class="fila">
            <div class="titulo">
                <label for="asesor" class="label1"><b>SFDM:</b></label>
            </div>
            <div class="container-select" style="margin-left: 20px; margin-top: -10px;">
                <select name="opciones" id="opciones">
                    <option value="#" disabled selected></option>
                    <option value="opcion2">Opcion 1</option>
                    <option value="opcion3">Opcion 2</option>
                </select>
                <span class="titulo"></span>
                <i class="fa-solid fa-chevron-down icon"></i>
            </div>
            <button class="btn-verificar" style="margin-top: -5px;">
                Filtrar
            </button>
        </div>
        <table id="mitabla" class="table table-striped tablaClientes" style="width:100%">        
            <thead>
                <tr>
                    <th>[x]</th>
                    <th>Préstamo</th>
                    <th>Nombre del cliente</th>
                    <th>Apertura</th>
                    <th>Monto</th>    
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>doce millones</td>
                    <td>Mario Kémpes</td>
                    <td>Hoy</td>
                    <td>12,000.00</td>
                </tr>   
            </tbody>    
        </table>
        <div class="fila" style="margin-top: 40px;">
            <div class="titulo">
                <label for="asesor" class="label1">Seleccionados:</label>
            </div>
            <div class="campos" style="margin-top: -15px; margin-left: 10px;">
                <div class="campos-desactivados">
                        <input type="number" id="cuota" name="cuota" disabled>
                </div>
            </div>
            <div class="titulo" style="margin-left: 30px;">
                <label for="asesor" class="label1">Monto de seleccionados:</label>
            </div>
            <div class="campos" style="margin-top: -15px; margin-left: 10px;">
                <div class="campos-desactivados">
                        <input type="number" id="cuota" name="cuota" disabled>
                </div>
            </div>
            <button class="btn-verificar" style="margin-top: -12px; margin-left: 40px; background: blue;">
                <b>Otros cambios</b>
            </button>
        </div>
        <div class="fila" style="margin-top: 40px;">
            <div class="titulo">
                <label for="asesor" class="label1"><b>Seleccione el asesor que recibirá la cartera de créditos</b></label>
            </div>
            <div class="container-select" style="margin-left: 20px; margin-top: -10px;">
                <select name="opciones" id="opciones">
                    <option value="" disabled selected>Seleccione un asesor</option>
                    <option value="opcion1">Opcion 1</option>
                    <option value="opcion2">Opcion 2</option>
                </select>
                <span class="titulo"></span>
                <i class="fa-solid fa-chevron-down icon"></i>
            </div>
        </div>
        <div class="fila" style="text-align: center; justify-content: center;">
            <button class="btn-hecho">
                <i class="fa-solid fa-repeat"></i> <b>Transferir ya</b>
            </button>
        </div>
    </div>
</div>