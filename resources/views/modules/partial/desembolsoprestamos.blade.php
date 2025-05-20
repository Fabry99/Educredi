<link href="{{ Vite::asset('node_modules/datatables.net-dt/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
@include('modules.modals.modalEleccionTipoPrestamo')
@include('modules.modals.modalPrestamoGrupal')
@include('modules.modals.modalprestamoindividual')
<div class="container">
    <div class="main-content">
        <div class="container mt-4">
            <h1>Desembolso de Prestamo</h1>

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



            <table id="tabladesembolso" class="table table-striped table1" style="width:100%">

                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>DUI</th>
                        <th>Prestamo</th>
                        <th>Fecha Prestamo</th>
                        <th></th>


                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->id }}</td>
                        <td>{{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                        <td>{{ $cliente->dui }}</td>
                        <td>{{ $cliente->saldoprestamo?->MONTO ?? '-' }}</td>
                        <td>{{ $cliente->saldoprestamo?->FECHAAPERTURA ?? '-' }}</td>
                        <td>
                            <button type="button" class="btn-prestamo"
                                data-id="{{ $cliente->id }}"
                                data-name="{{ $cliente->nombre }} {{ $cliente->apellido }}">
                                Préstamo
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>


            </table>
        </div>
    </div>

</div>
