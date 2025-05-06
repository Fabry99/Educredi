import $ from 'jquery'; // Importa jQuery
import 'datatables.net-dt'; // Importa DataTables
import 'datatables.net-colreorder'; // Importa DataTables colReorder
import 'datatables.net-keytable-dt'; // Importa DataTables keytable
import 'datatables.net-scroller-dt';




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
    "pageLength": 5,
    "columnDefs": [
        {
            "targets": 0,  // Índice de la columna que contiene los checkboxes
            "orderable": false  // Desactivar ordenación en esa columna
        }
    ],
    "initComplete": function () {
        // Ocultar la columna con índice 1
        this.api().column(1).visible(false);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // const selectAll = document.getElementById('select-all');
    // const checkboxes = document.querySelectorAll('.select_row');
    function actualizarColorFila() {
        // Obtener todos los checkboxes dentro de la tabla
        const checkboxes = document.querySelectorAll('.table1 tbody input[type="checkbox"]');

        checkboxes.forEach(cb => {
            const row = cb.closest('tr');  // Obtener la fila asociada al checkbox
            if (cb.checked) {
                row.classList.add('row-selected');  // Añadir la clase si el checkbox está seleccionado
            } else {
                row.classList.remove('row-selected');  // Eliminar la clase si no está seleccionado
            }
        });
    }

    // Evento para seleccionar o deseleccionar todas las filas
    const selectAll = document.querySelector('#selectAllCheckbox');  // Asegúrate de tener un checkbox para seleccionar/deseleccionar todo

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            // Seleccionar o deseleccionar todos los checkboxes
            const checkboxes = document.querySelectorAll('.table1 tbody input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            actualizarColorFila();  // Actualizar el color de las filas
        });
    }

    // Delegación de eventos para la selección de filas individuales
    $('.table1 tbody').on('change', 'input[type="checkbox"]', function () {
        actualizarColorFila();  // Actualizar el color de la fila cuando cambie el estado de un checkbox
    });
    const selectCentro = document.getElementById('centro_id');
    const selectGrupo = document.getElementById('grupo_id');
    const selectAsesorCartera = document.getElementById('asesorcartera');
    let id_centro = null;
    let id_grupo = null;
    let id_asesor = null;

    selectCentro.addEventListener('change', obtenergrupos);

    function obtenergrupos() {
        id_centro = selectCentro.value;

        try {

            selectGrupo.innerHTML = '<option value="" disabled selected>Seleccionar:</option>';


            fetch('/trasferencia/obtenergrupos/' + id_centro)
                .then(response => response.json())
                .then(data => {
                    data.forEach(grupo => {
                        const option = document.createElement('option');
                        option.value = grupo.grupo_id; // o grupo.grupos.id si prefieres
                        option.textContent = grupo.grupos.nombre;
                        selectGrupo.appendChild(option);
                    });

                })
                .catch(error => {
                    mostrarAlerta('Error al obtener los grupos:', 'error');

                });
        } catch (error) {
            mostrarAlerta("Ocurrio un error al Obtener los Datos");
        }
    }

    selectAsesorCartera.addEventListener('change', obtenerDatosTabla);
    let dataTable;
    function obtenerDatosTabla() {
        id_asesor = selectAsesorCartera.value;
        id_grupo = selectGrupo.value;
    
        fetch(`/transferencia/obtenerPrestamos/${id_asesor}/${id_grupo}/${id_centro}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('monto_total').value = `$${data.total_monto.toFixed(2)}`;
                document.getElementById('creditos').value = data.total_registros;
    
                const tabla = $('#tablaTransCartera');
                const tablaBody = tabla.find("tbody");
                tablaBody.empty(); // Limpiar el cuerpo de la tabla
    
                const rowsData = []; // Contendrá las filas que se agregarán a DataTable
    
                data.datos.forEach(prestamo => {
                    const fila = [
                        `<input type="checkbox" class="select_row" style="transform: scale(1.5);">`, // Columna 1 (checkbox)
                        prestamo.id, // Columna 2 (id)
                        `$${Number(prestamo.monto).toFixed(2)}`, // Columna 3 (monto)
                        prestamo.cliente_nombre, // Columna 4 (cliente nombre)
                        prestamo.fecha_apertura, // Columna 5 (fecha apertura)
                        `$${Number(prestamo.monto).toFixed(2)}` // Columna 6 (monto)
                    ];
                    rowsData.push(fila); // Agregar la fila al array de filas
                });
    
                // Verifica si la tabla ya está inicializada
                if ($.fn.DataTable.isDataTable('#tablaTransCartera')) {
                    // Si ya está inicializada, destruimos la instancia anterior
                    tabla.DataTable().clear().rows.add(rowsData).draw();
                } else {
                    // Si no está inicializada, la inicializamos
                    tabla.DataTable({
                        data: rowsData,
                        responsive: true,
                        "paging": true,
                        "lengthChange": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
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
                        "pageLength": 5,
                        "columnDefs": [
                            {
                                "targets": 0,  // Índice de la columna que contiene los checkboxes
                                "orderable": false  // Desactivar ordenación en esa columna
                            }
                        ]
                    });
                }
    
                // Ocultar la columna 1 (segunda columna con índice 1)
                tabla.DataTable().column(1).visible(false);
            })
            .catch(error => {
                console.error("Error al obtener los préstamos:", error);
            });
    }
    


});

function mostrarAlerta(mensaje, tipo) {
    const alerta = document.getElementById('alert-notification');
    const mensajeAlerta = document.getElementById('alert-notification-message');

    // Limpiar cualquier contenido o clase previa
    mensajeAlerta.textContent = mensaje;
    alerta.classList.remove('error_notification', 'success_notification'); // Limpiar clases anteriores

    // Asignar la clase correcta según el tipo
    if (tipo === "error") {
        alerta.classList.add('error_notification'); // Clase de error (rojo)
    } else if (tipo === "success") {
        alerta.classList.add('success_notification'); // Clase de éxito (verde)
    }

    // Mostrar la alerta y aplicar animación
    alerta.style.display = 'block';
    setTimeout(() => {
        alerta.classList.add('show'); // Aplica la animación de mostrar
    }, 10);

    // Ocultar la alerta después de 4 segundos
    setTimeout(function () {
        alerta.classList.remove('show'); // Eliminar la animación de mostrar
        setTimeout(function () {
            alerta.style.display = 'none'; // Ocultar la alerta completamente
        }, 500);  // Tiempo para que la animación termine
    }, 4000);  // La alerta se oculta después de 4 segundos
}
