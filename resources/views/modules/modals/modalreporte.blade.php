<!-- resources/views/components/modal-reporte.blade.php -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
<!-- Contenido del Modal -->
    <div class="modal-content">
        <span class="close-btn1">&times;</span>
        <h2>Generando archivo INFORED</h2>
        <form action="" method="POST">
            @csrf
            <div class="modal-ge">
                <div class="input-group" style="width: 100%;">
                    <label for="nombre" class="label1">
                        <input type="nombre" id="nombre" name="nombre" placeholder="Nombre del archivo:">
                    </label>
                </div>
            </div>
            <div class="modal-ge">
                <div class="input-group-date">
                    <label for="expedicion" class="label2" style="font-size: 12px">Desde:
                        <input type="date" id="expedicion" name="expedicion" required>
                    </label>
                </div>
                <div class="input-group-date">
                    <label for="expedicion" class="label2" style="font-size: 12px">Hasta:
                        <input type="date" id="expedicion" name="expedicion" required>
                    </label>
                </div>
            </div>
            <div class="radio-group">
                <label class="radio-label">Todos
                    <input type="radio" name="opcion" value="todos" id="radioTodos">
                    <span class="radio-custom"></span>
                </label>
                <label class="radio-label">Un asesor
                    <input type="radio" name="opcion" value="asesor" id="radioAsesor">
                    <span class="radio-custom"></span>
                </label>
            </div>
            <div class="modal-ge" style="justify-content: center;">
                 <div class="input-group select" style="width: 50%;">
                    <select id="id_asesor" name="id_asesor" required disabled>
                        <option value="" disabled selected>Seleccione un asesor</option>
                    </select>
                </div>
            </div>      
            <div class="botones">
                <button type="submit" class="btn-aceptar" title="Imprimir"><img src="{{ asset('img/icon_imprimir.svg') }}"
                        alt="Imprimir"></button>
            </div>
        </form>
    </div>
</div>

<script>
    const links = document.querySelectorAll('.nav-links a');
    const secciones = document.querySelectorAll('.seccion');
    const radioAsesor = document.getElementById('radioAsesor');
    const selectAsesor = document.getElementById('id_asesor');
    const radios = document.querySelectorAll('input[name="opcion"]');

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            selectAsesor.disabled = !radioAsesor.checked;
            if (!radioAsesor.checked) {
                selectAsesor.selectedIndex = 0;
            }
        });
    });

</script>

<!-- Estilos CSS -->
<style>
    :root {
        --navbar: #067016;
        --background: #eae9e9;
        --color-font: #fff5f5;
        --background-form: rgb(255, 255, 255);
        --color-font-form: #333;
        --sombra-login-form: rgba(9, 72, 9, 0.308);
        --color-boton: rgba(11, 121, 20, 0.866);
        --borde: #ccc;
        --background-inputs: #f7f7f7;
        --font-personal: #8d0808;
        --azul: #385E89;
    }

    h2 {
        justify-self: center
    }

    .modal-ge {
        display: flex;
        width: 100%;
        margin-top: 30px;
    }

    .modal-ge select {
        width: 100%;
        padding: 10px 15px;
        border-radius: 4px;
        background: var(--background-inputs);
    }

    .input-group-date.select {
        border: 1px solid var(--borde);
        border-radius: 4px;
        background: var(--background-inputs);
    }

    .input-group.select {
        border: 1px solid var(--borde);
        border-radius: 4px;
        background: var(--background-inputs);
    }

    .input-group-date {
        margin-left: 20px;
        margin-right: 20px;
        width: 100%;
    }

    .input-group {
        margin-left: 20px;
    }

    .input-group-date input {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 16px;

    }

    .input-group input {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 16px;

    }

    .input-group select {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 16px;

    }

    .input-group .label1 {
        display: flex;
        align-items: center;
        background: var(--background-inputs);
        border: 1px solid var(--borde);
        border-radius: 4px;
        padding: 10px 30px;
        margin-right: 20px;
    }

    h3 {
        color: var(--font-personal);
        justify-self: center;
    }

    .label2 {
        display: flex;
        align-items: center;
        background: var(--background-inputs);
        border: 1px solid var(--borde);
        border-radius: 4px;
        padding: 10px;
    }

    .radio-group {
      display: flex;
      gap: 20px;
      justify-content: center;
      margin-top: 30px;
    }

    .radio-label {
      position: relative;
      padding-left: 30px;
      cursor: pointer;
      user-select: none;
      font-size: 17px;
      margin-left: 50px;
      margin-right: 50px;
    }

    .radio-label input[type="radio"] {
      position: absolute;
      opacity: 0;
      cursor: pointer;
    }

    .radio-custom {
      position: absolute;
      left: 0;
      height: 20px;
      width: 20px;
      background-color: #eee;
      border-radius: 50%;
      transition: background 0.3s;
    }

    .radio-label input:checked ~ .radio-custom {
      background-color:rgb(0, 221, 0);
    }

    .radio-custom::after {
      content: "";
      position: absolute;
      display: none;
    }

    .radio-label input:checked ~ .radio-custom::after {
      display: block;
    }

    .radio-label .radio-custom::after {
      top: 6px;
      left: 6px;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: white;
    }

    .botones {
        display: flex;
        justify-content: right;
        margin-top: 20px;
    }

    .btn-aceptar {
        background: var(--color-boton);
        padding: 5px 25px;
        border-radius: 4px;
        color: var(--color-font);
        text-decoration: none;
        border: 1px solid black;
    }

    .btn-imprimir {
        background: var(--azul);
        padding: 5px 25px;
        border-radius: 4px;
        color: var(--color-font);
        text-decoration: none;
        margin-right: 10px;
        border: 1px solid black;
    }

    .botones button img {
        width: 35px;
        height: 35px;
    }

    .btn-aceptar:hover {
        background: #0b7914;
    }

    .btn-imprimir:hover {
        background: #486c96;
    }
    .close-btn1 {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close-btn1:hover,
  .close-btn1:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }

</style>