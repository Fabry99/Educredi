<div id="modalEleccionTipoPrestamo" class="modal">
    <!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <div class="botones-eleccion">
            <button type="button" class="btn-prestamogrupal">
                <img src="{{ asset('img/icon-prestamogrupal.svg') }}" style="margin:0px 50px;">
                <span>Prestamo
                    Grupal</span>
            </button>
            <button type="button" class="btn-prestamoindividual">
                <img src="{{ asset('img/icon-prestamoindividual.svg') }}" alt="">
                <span>Prestamo
                    Individual</span>
            </button>

        </div>
    </div>
</div>
<style>
    .botones-eleccion {
        margin: 20px 5px;
        display: flex;
        justify-content: center;
        gap: 25px;
    }

    .botones-eleccion button {
        background: #5bbe5b;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: var(--color-font);
        border-radius: 4px;
        border: 1px solid #388169b4;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.25);
    }

    .botones-eleccion button:hover {
        background-color: #3bba3b;
        border-color: var(--verde-shadow);
        box-shadow: 0 0 5px var(--verde-shadow);
    }

    .botones-eleccion button img {
        margin: 7px 5px;
        width: 95px;
        height: 100px;

    }

    .botones-eleccion button span {
        font-size: 18px;
        font-weight: 600;
        margin: 5px 9px;
    }
</style>
