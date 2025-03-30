<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
@include('modules.modals.modalcliente')
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Clientes</h1>
            <div class="btn-clientes">
                <a href="" id="openModalBtn"><img src="{{asset('img/icon-clientes.svg')}}" alt=""><span>Agregar Cliente</span></a>
                

            </div>
            <table id="mitabla" class="table table-striped tablaClientes" style="width:100%">
                
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>DUI</th>
                        <th>Act.Economica</th>
                        <th>Feha Ingresor</th>
                        
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>10221</td>
                        <td>Fabricio</td>
                        <td>Abrego</td>
                        <td>245854217</td>
                        <td>-</td>
                        <td>26/03/2025</td>
                       
                    </tr>
                    <tr>
                        <td>1233</td>
                        <td>Joel</td>
                        <td>Ortiz</td>
                        <td>10285444</td>
                        <td>-</td>
                        <td>26/03/2025</td>
                       
                    </tr>
                   
                </tbody>
                
            </table>
        </div>
    </div>

</div>
