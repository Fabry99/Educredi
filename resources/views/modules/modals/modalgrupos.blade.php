<!-- El Modal -->
<div id="modalgrupos" class="modalgrupos">
    <!-- Contenido del Modal -->
    <div class="modal-contentgrupos">
        <span class="close-btn1">&times;</span>
        <h2 id="modal-title">Detalles del Grupo</h2> <!-- Aquí se actualizará el título con el ID -->
        <table id="tablagrupos" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Grupos</th>
                    <th>Cantidad de Personas</th>
                    <th>Fecha Creación</th>
                </tr>
            </thead>
            <tbody>
               
            </tbody>

        </table>
    </div>
</div>
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
    .modalgrupos {
    display: none; /* Ocultar el modal por defecto */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
  }

  /* Contenido del Modal */
  .modal-contentgrupos {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: auto;
    max-width: 1000px;
    border-radius: 10px;
  }
    h2 {
        justify-self: center
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


    .modal-ge {
        display: flex;
        width: 100%;
        margin-top: 10px;
        justify-content: center;
    }

    .input-group {
        margin-left: 20px;
        display: flex;
        margin-top: 20px
    }

    .input-group input {
        border: 2px solid var(--borde);
        border-radius: 6px;
        background: none;
        width: 100%;
        font-size: 16px;
        padding: 10px 25px;
        margin-left: 10px;
    }

    .input-group select {
        border: 2px solid var(--borde);
        border-radius: 6px;
        background: var(--background-inputs);
        padding: 10px 10px;
        margin-right: 10px;
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

    .nombre {
        display: flex;
        width: 75%;
        justify-content: center;
        align-items: center;
    }

    .botones {
        display: flex;
        justify-content: right;
        margin-top: 20px;
    }

    .btn-aceptar {
        background: var(--color-boton);
        padding: 5px 45px;
        border-radius: 4px;
        color: var(--color-font);
        text-decoration: none;
        border: 1px solid black;
    }

    button img {
        width: 35px;
        height: 35px;
    }

    .btn-aceptar:hover {
        background: #0b7914;
    }

    .btn-imprimir:hover {
        background: #486c96;
    }

/* Cambiar el color de fondo de las cabeceras */
#tablagrupos th {
    background-color: var(--tittle-column);
    color: white;
    text-align: left;
}

/* Cambiar el color de las filas al pasar el cursor sobre ellas */
#tablagrupos tbody tr:hover {
    background-color: #f1f1f1;
}

/* Establecer el borde de las celdas */
#tablagrupos th, #tablagrupos td {
    border: 1px solid #ddd;
    padding: 10px;
}

/* Personalizar las filas alternadas */
#tablagrupos tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Cambiar el color de la paginación */
.dataTables_paginate .paginate_button {
    background-color: #007bff;
    color: white;
    padding: 5px 10px;
}

.dataTables_paginate .paginate_button:hover {
    background-color: #0056b3;
}
table.dataTable tbody tr:hover {
    cursor: pointer;
}


</style>
