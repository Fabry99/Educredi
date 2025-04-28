@include('modules.modals.modalreversionprestamo');
<div class="container">
    <div class="main-content">
        <h1>Reversion de liquidación</h1>

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

        <div class="section">
            <div class="section-container">
                <div class="form-reversion" >
                    <label for="codigo">Codigo de Prestamo:</label>
                    <input type="number" value="" id="codigo" name="codigo" class="form-control" title="Ingrese un Código de Cliente Correcto">
                </div>
                <div class="form-reversion">
                    <label for="monto">Saldo del Préstamo:</label>
                    <input type="text" value="" id="monto" name="monto" class="form-control" readonly>
                </div>
            </div>
            <div class="section-container2">
                <select name="" id="">
                    <option value="">Seleccione una opcion</option>
                    <option value="">Opcion 1</option>
                    <option value="">Opcion 2</option>
                    <option value="">Opcion 3</option>
                </select>
                <a href="" id="btn-abrirmodalreversion">Realizar la Reversión</a>
            </div>
        </div>

    </div>
</div>