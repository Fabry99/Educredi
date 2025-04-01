<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
@include('modules.modals.modalcliente')
@include('modules.modals.modaleditarcliente')
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Clientes</h1>
            <div class="btn-clientes">
                <a href="" id="openModalBtn"><img src="{{ asset('img/icon-clientes.svg') }}"
                        alt=""><span>Agregar Cliente</span></a>


            </div>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="notification custom_error">
                        {{ $error }}
                    </div>
                @endforeach
            @endif

            @if (session('success'))
                <div class="notification custom_success">
                    {{ session('success') }}
                </div>
            @endif
            <table id="mitabla" class="table table-striped tablaClientes" style="width:100%">

                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Genero</th>
                        <th>Edad</th>
                        <th>Telefono</th>
                        <th>DUI</th>
                        <th>Departamento</th>
                        <th>Municipio</th>
                        <th>Centro pert.</th>
                        <th>Grupo pert.</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $clientes)
                    <tr>
                        <td>{{$clientes->id}}</td>
                        <td>{{$clientes->nombre}}</td>
                        <td>{{$clientes->apellido}}</td>
                        <td>{{$clientes->genero}}</td>
                        <td>{{ \Carbon\Carbon::parse($clientes->fecha_nacimiento)->age }} años</td>
                        <td>{{$clientes->telefono_casa}}</td>
                        <td>{{$clientes->dui}}</td>
                        <td>{{$clientes->departamento->nombre ?? 'Sin Departamento'}}</td>
                        <td>{{$clientes->municipio->nombre ?? 'Sin Municipio'}}</td>
                        <td>{{$clientes->centro->nombre ?? 'Sin Centro'}}</td>
                        <td>{{$clientes->grupo->nombre ?? 'Sin Grupo'}}</td>

                    </tr>
                        
                    @endforeach

                </tbody>

            </table>
        </div>
    </div>

</div>
