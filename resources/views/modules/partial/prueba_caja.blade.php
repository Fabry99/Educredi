<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
@include('modules.modals.modalreporte')
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Consulta de préstamos</h1>
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
            <div class="btn-grupos" style="display: flex; margin-bottom: 10px; margin-left: 10px;">
                <a href="#" id="openModalBtn" class="btn-reporte"
                    style="margin-right: 15px;"><span>Reporte préstamos</span></a>
            </div>
        </div>
    </div>

</div>

<script>
    document.getElementById('openModalBtn').addEventListener('click', function (e) {
        e.preventDefault();
        const myModal = new bootstrap.Modal(document.getElementById('myModal'));
        myModal.show();
    });
</script>
