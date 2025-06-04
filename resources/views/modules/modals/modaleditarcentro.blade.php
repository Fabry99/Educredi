<!-- El Modal -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="modaleditarcentro" class="modal">
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <div class="head-tittle">
            <div class="head-logo">
                <img src="{{ asset('img/logoeducredi.jpeg') }}" alt="Logo">
                <h3>EDUCREDI RURAL <br> S.A DE C.V</h3>
            </div>
            <h2>Actualizar Centro</h2>
        </div>
        <form method="POST">
            <input type="hidden" id="centro_id_editarcentro" name="id">
            <div class="modal-ge">
                <div class="input-group">
                    <label for="nombrecentro" class="nombre">Nombre del Centro:</label>
                    <input type="text" id="nombrecentro" name="nombrecentro" required>
                </div>
            </div>
            <div class="botones">
                <button type="submit" class="btn-aceptar btn-actualizarcentros">
                    <img src="{{ asset('img/aceptar.svg') }}" alt="Aceptar">
                </button>
            </div>
        </form>

    </div>
</div>
<style>
    .input-group input:hover {
        border-color: var(--verde-shadow);
        box-shadow: 0 0 2px var(--verde-shadow);

    }

    .btn-aceptar:hover {
        background: #0b7914;
        border-color: var(--verde-shadow);
        box-shadow: 0 0 2px var(--verde-shadow);
    }
</style>
