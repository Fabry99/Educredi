<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
@include('modules.modals.modalnuevoasesor')
@include('modules.modals.modalreversionprestamo')
@include('modules.modals.modalEditarAsesor')
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Mantenimiento de Asesores</h1>
            <div class="btn-grupos" style="display: flex; margin-bottom: 10px; margin-top: 10px; margin-left: 10px;">
                <a href="#" id="openModalBtnnuevoAsesor" class="btn-agregar" style="margin-right: 15px;"><span>Nuevo
                        Asesor</span></a>


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
            <div id="alert-notification" class="alert"
                style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; width: 80%; max-width: 400px; font-size: 16px; text-align: center; border-radius: 5px;">
                <span id="alert-notification-message"></span>
            </div>



            <table id="tablaAsesores" class="table table-striped table1" style="width:100%">

                <thead>
                    <tr>
                        <th>Codigo Asesor</th>
                        <th>Nombres</th>
                        <th>Sucursal</th>
                        <th>Fecha Ingreso</th>
                        <th>Fecha Actualización</th>


                    </tr>
                </thead>
                <tbody>
                    @foreach ($asesores as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nombre }}</td>
                            <td>{{ $item->sucursales->nombre }}</td>
                            <td style="text-align: center">
                                {{ $item->getRawOriginal('created_at') != '0000-00-00 00:00:00' ? $item->created_at->format('d-m-Y H:i') : '-' }}
                            </td>

                            <td style="text-align: center">
                                {{ $item->getRawOriginal('updated_at') != '0000-00-00 00:00:00' ? $item->updated_at->format('d-m-Y H:i') : '-' }}
                            </td>


                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>
    </div>

</div>
