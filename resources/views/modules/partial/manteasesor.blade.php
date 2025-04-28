<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
{{-- @include('modules.modals.modalcliente') --}}
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Mantenimiento de Asesores</h1>
            <div class="btn-grupos" style="display: flex; margin-bottom: 10px; margin-top: 10px; margin-left: 10px;">
                <a href="" id="openModalBtn" class="btn-agregar" style="margin-right: 15px;"><span>Nuevo Asesor</span></a>
                <a href="" id="openModalBtn" class="btn-eliminar"><span>Eliminar Asesor</span></a>


            </div>

            <table id="mitabla" class="table table-striped" style="width:100%">
                
                <thead>
                    <tr>
                        <th>Codigo Asesor</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Sucursal</th>
                        <th>Cartera</th>
                        
                        
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Codigo Asesor</td>
                        <td>Nombre</td>
                        <td>Apellido</td>
                        <td>Sucursal</td>
                        <td>Cartera</td>

                        
                    
                     </tr>

                </tbody>
                
            </table>
        </div>
    </div>

</div>
