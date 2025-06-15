@include('modules.modals.modalreversionprestamo');
<div class="container">
    <div class="main-content">
        <h1>Reversión de liquidación</h1>

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

        <div class="section" style="height: auto; margin-top: 50px">
            <div class="section-container">
                <div class="form-reversion">
                    <label for="codigo">Codigo de Prestamo:</label>
                    <input type="number" value="" id="codigo" name="codigo" class="form-control"
                        title="Ingrese un Código de Cliente Correcto">
                </div>
                <div class="form-reversion">
                    <label for="monto">Saldo del Préstamo:</label>
                    <input type="text" value="" id="monto" name="monto" class="form-control" readonly>
                </div>
            </div>
            <div class="informacion" style=" width: 100%; margin-top: 50px">
                <label for="total" style="margin-left: 40px; ">¿Por qué se va a
                    Revertir?</label>
                <div class="" style="width: 100%; margin: 5px 0px 0px 40px ">
                    <textarea id="motivo" name="motivo" class="form-control" rows="3"
                        style="width: 90%;height: 100px; resize: vertical; background: #f1f1f1"></textarea>
                </div>
            </div>
            <div class="section-container2" style="margin-bottom: 30px">

                <a href="" id="btn-abrirmodalreversion">Realizar la Reversión</a>
            </div>
        </div>

    </div>
    <input type="hidden" id="fecha_apertura" name="fecha_apertura" value="">
    <input type="hidden" id="fecha_vencimiento" name="fecha_vencimiento" value="">
</div>
