import './bootstrap';
import $, { error, event } from 'jquery'; // Importar jQuery
import 'datatables.net-dt';
import 'datatables.net-colreorder';
import 'datatables.net-keytable-dt';
import 'datatables.net-scroller-dt';
import { Button } from 'bootstrap';

// Tabla Bitacora de personal
$('.table1').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
    "colReorder": true,
    "order": [[0, "desc"]],
    "language": {
        "decimal": ",",
        "thousands": ".",
        "lengthMenu": "Mostrar _MENU_ registros por página",
        "zeroRecords": "No se encontraron resultados",
        "info": "Mostrando página _PAGE_ de _PAGES_",
        "infoEmpty": "No hay registros disponibles",
        "infoFiltered": "(filtrado de _MAX_ registros totales)",
        "search": "Buscar:",
        "paginate": {
            "first": "Primera",
            "previous": "Anterior",
            "next": "Siguiente",
            "last": "Última"
        },
        "aria": {
            "sortAscending": ": activar para ordenar la columna de manera ascendente",
            "sortDescending": ": activar para ordenar la columna de manera descendente"
        }
    },
    "lengthMenu": [5, 10, 25, 50, 100],
    "pageLength": 5
});

$(document).ready(function () {
    const table = $('#tablaUsuarios').DataTable();

    $('#tablaUsuarios tbody').on('click', 'tr', function () {
        const rowData = table.row(this).data(); // Obtiene los datos de la fila seleccionada
        const id_user = rowData[0];
        const nombreUser = rowData[1];
        console.log(id_user);
        $('#user_id').val(id_user);
        $('#modaleditaruser h2').text('Editar Usuario - ' + nombreUser);

        $.ajax({
            url: '/admin/usurios/obtener-user/' + id_user,
            type: 'GET',
            success: function (response) {
                console.log(response);
                $('#modaleditaruser #nombreupdate').val(response.name);
                $('#modaleditaruser #apellidoupdate').val(response.last_name);
                $('#modaleditaruser #correoupdate').val(response.email);
                $('#modaleditaruser #rolupdate').val(response.rol);
                $('#modaleditaruser #actividadupdate').val(response.estado);
                $('#modaleditaruser #nacimientoupdate').val(response.fecha_nacimiento);
                $('#modaleditaruser #passwordupdate').val(response.password);

            },
            error: function () {
                alert('Error al obtener los datos del cliente.');
            }
        });


        // Mostrar el modal
        $('#modaleditaruser').fadeIn();
    });

    // Cerrar el modal al hacer clic en el botón de cerrar
    $('.close-btn1').on('click', function () {
        $('#modaleditaruser').fadeOut();
    });

    // Cerrar el modal si se hace clic fuera de él
    $(window).on('click', function (event) {
        if ($(event.target).is('#modaleditaruser')) {
            $('#modaleditaruser').fadeOut();
        }
    });

    // Cerrar el modal al presionar ESC
    $(document).on('keydown', function (event) {
        if (event.key === "Escape") {
            $('#modaleditaruser').fadeOut();
        }
    });
});



document.addEventListener("DOMContentLoaded", function () {
    // Función para abrir un modal
    function abrirModal(modalId) {
        let modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "block";
        }
    }

    // Función para cerrar un modal
    function cerrarModal(modal) {
        if (modal) {
            modal.style.display = "none";
            modal.classList.remove('flex-center');
        }
    }
    window.cerrarModal = cerrarModal; // Para usarla desde otros scripts si hace falta


    // Mapeo de botones a modales
    let botones = {

        "openModalBtnnuevousuario": "ModalNuevoUsuario"

    };

    // Asignar eventos a los botones para abrir los modales
    Object.keys(botones).forEach(btnId => {
        let boton = document.getElementById(btnId);
        if (boton) {
            boton.addEventListener("click", function (event) {
                event.preventDefault();
                abrirModal(botones[btnId]);
            });
        }
    });

    // Detectar botones de cierre y asignar eventos
    document.querySelectorAll(".close-btn1").forEach((boton) => {
        boton.addEventListener("click", function () {
            const modalToClose = this.closest(".modal");
            cerrarModal(modalToClose);
        });
    });

    // Cerrar modal al hacer clic fuera de él
    window.addEventListener("click", function (event) {
        document.querySelectorAll(".modal").forEach((modal) => {
            if (event.target === modal) {
                cerrarModal(modal);
            }
        });
    });

    // Cerrar el modal al presionar la tecla ESC
    window.addEventListener("keydown", function (event) {
        if (event.key === "Escape") { // Si se presiona la tecla ESC
            document.querySelectorAll(".modal").forEach((modal) => cerrarModal(modal));

        }
    });
});



// Modal para Elegir el tipo de prestamo
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalEleccionTipoPrestamo');
    const modalGrupal = document.getElementById('modalprestamogrupal');
    const modalIndividual = document.getElementById('modalPrestamoIndividual');

    let clienteId = null;
    let clienteNombre = null;
    let datacliente = [];
    let centroSeleccionado = null;

    const selectCentro = document.getElementById('centro');
    const selectGrupo = document.getElementById('grupo');

    // Mostrar modal de elección
    document.querySelectorAll('.btn-prestamo').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            clienteId = this.getAttribute('data-id');
            clienteNombre = this.getAttribute('data-name');

            modal.style.display = 'block';
            modal.classList.add('flex-center');
        });
    });

    // Click en botón "préstamo grupal"
    document.querySelector('.btn-prestamogrupal').addEventListener('click', function () {
        cerrarModal(modal);
        modalGrupal.style.display = 'block';
        modalGrupal.classList.add('flex-center');

        document.getElementById('id').value = clienteId;
        document.getElementById('nombre').value = clienteNombre;

        $.ajax({
            url: '/prestamos/obtener-centros-grupos-clientes/' + clienteId,
            type: 'GET',
            success: function (response) {
                datacliente = response;
                renderCentros(response);
                configurarEventosSelects(); // 👈 Aquí activamos los listeners
            },
            error: function () {
                alert('Error al obtener los datos del cliente.');
            }
        });
    });

    function renderCentros(data) {
        selectCentro.innerHTML = '<option value="" disabled selected>Centro:</option>';
        const centrosAgregados = new Set();

        data.forEach(item => {
            if (!centrosAgregados.has(item.centros.id)) {
                const option = document.createElement('option');
                option.value = item.centros.id;
                option.textContent = item.centros.nombre;
                selectCentro.appendChild(option);
                centrosAgregados.add(item.centros.id);
            }
        });

        selectGrupo.innerHTML = '<option value="" disabled selected>Grupo:</option>';
    }

    // Se llama una sola vez
    function configurarEventosSelects() {
        selectCentro.removeEventListener('change', manejarCambioCentro);
        selectGrupo.removeEventListener('change', manejarCambioGrupo);

        selectCentro.addEventListener('change', manejarCambioCentro);
        selectGrupo.addEventListener('change', manejarCambioGrupo);
    }

    function manejarCambioCentro() {
        centroSeleccionado = parseInt(this.value);
        selectGrupo.innerHTML = '<option value="" disabled selected>Grupo:</option>';

        const gruposFiltrados = datacliente.filter(item => item.centros.id === centroSeleccionado);
        const gruposAgregados = new Set();

        gruposFiltrados.forEach(item => {
            if (!gruposAgregados.has(item.grupos.id)) {
                const option = document.createElement('option');
                option.value = item.grupos.id;
                option.textContent = item.grupos.nombre;
                selectGrupo.appendChild(option);
                gruposAgregados.add(item.grupos.id);
            }
        });
    }

    function manejarCambioGrupo() {
        const grupoSeleccionado = this.value;
        const ruta = `/prestamos/obtenergrupos-clientes/${centroSeleccionado}/${grupoSeleccionado}`;

        $.ajax({
            url: ruta,
            type: 'GET',
            success: function (miembros) {
                const contenedor = document.getElementById('contenedorMiembrosGrupo');
                contenedor.innerHTML = '';

                if (miembros.length === 0) {
                    contenedor.innerHTML = '<p>No hay miembros en este grupo.</p>';
                    return;
                }

                const tabla = document.createElement('table');
                tabla.style.width = '100%';
                tabla.style.borderCollapse = 'collapse';
                tabla.style.marginTop = '10px';

                tabla.innerHTML = `
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th style="padding: 8px; border: 1px solid #ccc;">ID</th>
                            <th style="padding: 8px; border: 1px solid #ccc;">Nombre</th>
                            <th style="padding: 8px; border: 1px solid #ccc;">Monto</th>
                            <th style="padding: 8px; border: 1px solid #ccc;">Tasa</th>
                            <th style="padding: 8px; border: 1px solid #ccc;">Cuota</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                `;

                const tbody = tabla.querySelector('tbody');

                miembros.forEach(miembro => {
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
    <td style="padding: 8px; border: 1px solid #ccc;">${miembro.id}</td>
    <td style="padding: 8px; border: 1px solid #ccc;">${miembro.nombre} ${miembro.apellido}</td>
    
    <td style="padding: 8px; border: 1px solid #ccc;">
        <input type="number" name="monto" value="${(typeof miembro.monto === 'number' && !isNaN(miembro.monto)) ? miembro.monto : ''}"
        " style="width: 100%; border: none; outline: none; background: transparent;">
    </td>
    
    <td style="padding: 8px; border: 1px solid #ccc;">
        <input type="number" step="0.01" name="tasa" value="${(typeof miembro.tasa === 'number' && !isNaN(miembro.tasa)) ? miembro.tasa : ''}" 

        " style="width: 100%; border: none; outline: none; background: transparent;">
    </td>
    
    <td style="padding: 8px; border: 1px solid #ccc;">
        <input type="number" step="0.01" name="cuota" value="${(typeof miembro.cuota === 'number' && !isNaN(miembro.cuota)) ? miembro.cuota : ''}" 
        " style="width: 100%; border: none; outline: none; background: transparent;">
    </td>
`;
                    tbody.appendChild(fila);
                });

                contenedor.appendChild(tabla);
            },
            error: function () {
                alert('Error al obtener los miembros del grupo.');
            }
        });
    }

    // Limpiar y cerrar modal
    function limpiarModalPrestamoGrupal() {
        selectCentro.innerHTML = '<option value="" disabled selected>Centro:</option>';
        selectGrupo.innerHTML = '<option value="" disabled selected>Grupo:</option>';
        document.getElementById('contenedorMiembrosGrupo').innerHTML = '';
        document.getElementById('id').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('colector').value = '';
        document.getElementById('aprobadopor').value = '';
        document.getElementById('formaPago').value = '';
        document.getElementById('sucursal').value = '';
        document.getElementById('supervisor').value = '';
        document.getElementById('linea').value = '';
        document.getElementsByClassName
        centroSeleccionado = null;
        // Limpiar radio buttons
        const radios = document.querySelectorAll('input[name="garantia"]');
        radios.forEach(radio => radio.checked = false);

        // Volver a mostrar la sección principal
        document.querySelectorAll('.seccion').forEach(sec => sec.classList.remove('visible'));
        document.querySelector('.datos-prestamos').classList.add('visible');

        // Restaurar estado del nav
        document.querySelectorAll('.nav-links a').forEach(link => link.classList.remove('active'));
        document.getElementById('link-datos').classList.add('active');
    }

    // Eventos para cerrar el modal
    $('.close-btn1').on('click', function () {
        $('#modalprestamogrupal').fadeOut();
        limpiarModalPrestamoGrupal();
    });

    $(window).on('click', function (event) {
        if ($(event.target).is('#modalprestamogrupal')) {
            $('#modalprestamogrupal').fadeOut();
            limpiarModalPrestamoGrupal();
        }
    });

    $(document).on('keydown', function (event) {
        if (event.key === "Escape") {
            $('#modalprestamogrupal').fadeOut();
            limpiarModalPrestamoGrupal();
        }
    });
});
