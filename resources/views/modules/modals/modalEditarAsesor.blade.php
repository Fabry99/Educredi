<!-- El Modal -->
<div id="ModalEditarAsesor" class="modal">
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <h2>Actualizar Asesor</h2>
        <form method="PUT">
            @csrf
            <div class="modal-ge">
                <div class="input-group" style="margin-top: 20px; paddin: -20px 0px;">
                    <p>Nombre:</p>
                        <input type="nombre" id="nombreActua" name="nombre" placeholder="Nombre:" required>
                    
                </div>
                <div class="input-group select"
                    style="margin-left: 20px; width: 250px;
                ">
                    <select id="sucursalActua" name="sucursal" title="Seleccionar Sucursal" required>
                        <option value="" disabled selected>Asignar Sucursal</option>
                        @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="botones">
                <button type="button" title="Ingresar" class="btn-aceptar" id="btnaceptarActuAsesor"><img
                        src="{{ asset('img/aceptar.svg') }}" alt=""></button>
            </div>
        </form>
    </div>
</div>


