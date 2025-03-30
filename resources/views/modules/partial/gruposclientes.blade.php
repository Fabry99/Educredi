<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
@include('modules.modals.modalnuevocentros')
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Mantenimiento de Grupos</h1>
            <div class="btn-grupos" style="display: flex; margin-bottom: 10px; margin-top: 10px; margin-left: 10px;">
                <a href="#" id="openModalBtnnuevocentro" class="btn-agregar" style="margin-right: 15px;"><span>Nuevo
                        Centro</span></a>
                <a href="" id="openModalBtn" class="btn-eliminar"><span>Nuevo Grupo</span></a>


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

            <table id="mitabla" class="table table-striped" style="width:100%">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Centro</th>
                        <th>Asesor</th>
                        <th>Fecha Creación</th>
                        <th>Grupos Asignados</th>


                    </tr>
                </thead>
                <tbody>
                    @foreach ($centros as $centros)
                    <tr>
                        <td>{{ $centros->id }}</td>
                        <td>{{ $centros->nombre }}</td>
                        <td>{{ $centros->asesor->name }}</td> <!-- Muestra directamente el ID del asesor -->
                        <td>{{ $centros->created_at->format('d/m/Y H:i') }}</td>

                        <td></td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

     
    </div>


</div>
