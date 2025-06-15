@include('modules.modals.modalreversionprestamo');
<div class="container">
    <div class="main-content">
        <h1>Reversión de Cuota</h1>

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

        <div class="section" style="height: auto; width: 800px; margin-top:40px; margin-left: -100px">
            <div class="section-container">
                <div class="form-reversion">
                    <label for="Fecha">Fecha</label>
                    <input type="date" value="" id="fecha" name="Fecha" class="form-control"
                        title="Ingrese una Fecha Valida">
                </div>
                <div class="form-reversion">
                    <label for="monto">Comprobante:</label>
                    <input type="number" value="" id="comprobante" name="comprobante" class="form-control"
                        title="Ingrese un Número de Comprobante">
                </div>
            </div>
            <div class="table" style="margin: 30px 20px 30px 20px">
                <table id="tablareversionCuota" class="table table-striped table1" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Codigo Cliente</th>
                            <th style="width: 400px">Nombre</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>


                    </tbody>

                </table>
            </div>
            <div class="total">
                <div class="form-reversion" style="justify-content: end; margin-right: 25px; margin-bottom: 20px">
                    <label for="total">Total:</label>
                    <input type="number" value="" id="total" name="total" class="form-control" readonly
                        style="width: 80px; text-align: end">
                </div>
            </div>
            <div class="informacion" style=" width: 100%; ">
                <label for="total" style="margin-left: 30px; ">¿Por qué se va a
                    Revertir?</label>
                <div class="" style="width: 100%; margin: 5px 50px 0px 30px ">
                    <textarea id="motivo" name="motivo" class="form-control" rows="3"
                        style="width: 90%;height: 70px; resize: vertical; background: #f1f1f1"></textarea>
                </div>
            </div>
            <div class="section-container2" style="margin-bottom:30px; margin-top: 20px">

                <a href="" id="btn-abrirmodalreversion">Realizar la Reversión</a>
            </div>
        </div>

    </div>
    <input type="hidden" id="fecha_apertura" name="fecha_apertura" value="">
    <input type="hidden" id="fecha_vencimiento" name="fecha_vencimiento" value="">
</div>
<script>
    const esAdministrador = {{ Auth::check() && Auth::user()->rol === 'administrador' ? 'true' : 'false' }};
</script>
