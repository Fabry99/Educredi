<div class="modaledit" id="modalmostrarcliente">
    <div class="modal-contentedit">
        <span class="close-btncerrarmostrarcliente">&times;</span>
        <h2></h2>
        <form method="POST" action="">
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <table id="tablaclientesgrupos" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
    
            </table>
    </div>
</div>



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

    .modaledit {
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

    /* Contenido del Modal */
    .modal-contentedit {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: auto;
        max-width: 1000px;
        border-radius: 10px;
        flex-direction: column
    }

    .close-btncerrarmostrarcliente {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close-btncerrarmostrarcliente:hover,
  .close-btncerrarmostrarcliente:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }

    h2 {
        justify-self: center;
        font-size: 1.3rem;
    }

    .modal-ge1 {
        display: flex;
        flex-direction: row;
        margin-top: 15px;
    }

    #tablaclientesgrupos th {
    background-color: var(--tittle-column);
    color: white;
    text-align: left;
}

/* Cambiar el color de las filas al pasar el cursor sobre ellas */
#tablaclientesgrupos tbody tr:hover {
    background-color: #f1f1f1;
}

/* Establecer el borde de las celdas */
#tablaclientesgrupos th, #tablagrupos td {
    border: 1px solid #ddd;
    padding: 10px;
}

/* Personalizar las filas alternadas */
#tablaclientesgrupos tbody tr:nth-child(even) {
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
.btn-danger{
    background: #8d0808;
}
</style>
