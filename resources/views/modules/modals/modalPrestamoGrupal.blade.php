<div id="modalprestamogrupal" class="modalPrestamosGrupal">
    <!-- Contenido del Modal -->
    <div class="modal-contentPrestamosGrupal">
        <span class="close-btn1">&times;</span>
        <h2>Desembolso de Préstamos</h2>
        <form action="" method="POST">
            @csrf
            <div class="nav-links">
                <a href="#" id="link-datos" class="active">Datos del Préstamo</a>
                <a href="#" id="link-garantias">Garantías y Fiadores</a>
            </div>
            <hr class="separate-line">

            <!-- Datos del Préstamo -->
            <div class="datos-prestamos seccion visible">
                <div class="modal-ge">
                    <div class="input-group">
                        <label for="nombre" class="label1">
                            <input type="text" id="nombre" name="nombre" placeholder="Nombre:">
                        </label>
                    </div>
                    <div class="input-group">
                        <label for="apellido" class="label1">
                            <input type="text" id="apellido" name="apellido" placeholder="Apellidos:">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Garantías y Fiadores -->
            <div class="datos-Garantias-Fiadores seccion">
                <div class="input-group1">
                    <p style="white-space:nowrap;">Actividad Económica:</p>
                    <input type="text" id="actividadeconomica" name="actividadeconomica"
                        placeholder="Actividad Económica:">
                </div>
                <div class="input-group1">
                    <p style="white-space:nowrap;">NIT:</p>
                    <input type="text" id="NIT" name="NIT" placeholder="NIT: 1234-567891-234-5"
                        pattern="\d{4}-\d{6}-\d{3}-\d{1}"
                        title="Formato: 4 dígitos - 6 dígitos - 3 dígitos - 1 dígito (Ej: 1234-567891-234-5)">
                </div>
                <div class="botones">
                    <button type="submit" id="" class="btn-aceptar"><img src="{{ asset('img/aceptar.svg') }}"
                            alt=""></button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .modalPrestamosGrupal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-contentPrestamosGrupal {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        max-width: 600px;
        border-radius: 10px;
    }
    .modal-contentPrestamosGrupal h2{
        text-align: center;
        font-size: 28px;
    }
    .nav-links {
        justify-content: center;
        align-items: center;
        display: flex;
    }

    .nav-links a {
        width: 100%;
        margin-right: 15px;
        cursor: pointer;
        text-decoration: none;
        font-weight: bold;
        color: #555;
        padding: 5px 10px;
        border-radius: 5px;
        text-align: center;
    }
    .nav-links a:hover{
        width: 100%;
        padding: 20px;
        background-color: #f0f0f0;
        color: var(--color-boton);
        margin-left: 10px;
    }

    .nav-links a.active {
        width: 100%;
        margin: 0px 0px 10px 0px;
        background-color: var(--color-boton);
        color: white;
        padding: 20px
    }
    hr.separate-line{
        margin: 0px 0px 40px 0px;
    }


    .seccion {
        display: none;
        margin-top: 20px;
    }

    .seccion.visible {
        display: block;
    }
</style>
<script>
    const links = document.querySelectorAll('.nav-links a');
    const secciones = document.querySelectorAll('.seccion');

    links.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // Quitar clase .active de todos los enlaces
            links.forEach(l => l.classList.remove('active'));
            this.classList.add('active'); // Agregar al que se clickeó

            // Ocultar todas las secciones
            secciones.forEach(sec => sec.classList.remove('visible'));

            // Mostrar la sección correspondiente
            if (this.id === 'link-datos') {
                document.querySelector('.datos-prestamos').classList.add('visible');
            } else if (this.id === 'link-garantias') {
                document.querySelector('.datos-Garantias-Fiadores').classList.add('visible');
            }
        });
    });
</script>

