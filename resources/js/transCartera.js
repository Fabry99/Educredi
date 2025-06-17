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
    const btnGuardarTransferencia = document.getElementById('btnGuardarTransferencia');
    selectCentro.addEventListener('change', obtenergrupos);

    function obtenergrupos() {
        const id_centro = selectCentro.value;

        // Elimina TODAS las opciones del select de grupo
        selectGrupo.innerHTML = '';

        // Agrega opción por defecto
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.disabled = true;
        defaultOption.selected = true;
        defaultOption.textContent = 'Seleccionar:';
        selectGrupo.appendChild(defaultOption);

        // Fetch de los grupos
        fetch('/trasferencia/obtenergrupos/' + id_centro)
            .then(response => response.json())
            .then(data => {
                data.forEach(grupo => {

                    const option = document.createElement('option');
                    option.value = grupo.id;
                    option.textContent = grupo.nombre;
                    selectGrupo.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al obtener los grupos:', error);
                mostrarAlerta('Error al obtener los grupos', 'error');
            });
    }

    selectAsesorCartera.addEventListener('change', obtenerDatosTabla);
    selectGrupo.addEventListener('change', obtenerDatosTabla);
    selectCentro.addEventListener('change', obtenerDatosTabla);
    let dataTable;
    function obtenerDatosTabla() {
        const id_asesor = selectAsesorCartera.value;
        const id_grupo = selectGrupo.value;
        const id_centro = selectCentro.value;

        // Validar que todos los selects tengan un valor válido (no vacío ni null)
        if (!id_asesor || !id_grupo || !id_centro) {
            return;
        }

        fetch(`/transferencia/obtenerPrestamos/${id_asesor}/${id_grupo}/${id_centro}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('monto_total').value = `$${data.total_monto.toFixed(2)}`;
                document.getElementById('creditos').value = data.total_registros;

                const tabla = $('#tablaTransCartera');
                const tablaBody = tabla.find("tbody");
                tablaBody.empty();

                const rowsData = [];

                data.datos.forEach(prestamo => {
                    const fecha = new Date(prestamo.fecha_apertura);
                    // Obtener día, mes y año
                    const dia = String(fecha.getDate()).padStart(2, '0');
                    const mes = String(fecha.getMonth() + 1).padStart(2, '0'); // Meses empiezan en 0
                    const anio = fecha.getFullYear(); // Si quieres solo dos dígitos usa: anio.toString().slice(-2);

                    const fechaFormateada = `${dia}-${mes}-${anio}`;

                    const fila = [
                        `<input type="checkbox" class="select_row" style="transform: scale(1.5);">`,
                        prestamo.id,
                        `$${Number(prestamo.monto).toFixed(2)}`,
                        prestamo.cliente_nombre,
                        fechaFormateada,  // Aquí la fecha formateada
                        `$${Number(prestamo.monto).toFixed(2)}`
                    ];
                    rowsData.push(fila);
                });

                if ($.fn.DataTable.isDataTable('#tablaTransCartera')) {
                    tabla.DataTable().clear().rows.add(rowsData).draw();
                } else {
                    tabla.DataTable({
                        data: rowsData,
                        responsive: true,
                        paging: true,
                        lengthChange: true,
                        searching: true,
                        ordering: true,
                        info: true,
                        autoWidth: false,
                        colReorder: true,
                        order: [[0, "desc"]],
                        language: {
                            decimal: ",",
                            thousands: ".",
                            lengthMenu: "Mostrar _MENU_ registros por página",
                            zeroRecords: "No se encontraron resultados",
                            info: "Mostrando página _PAGE_ de _PAGES_",
                            infoEmpty: "No hay registros disponibles",
                            infoFiltered: "(filtrado de _MAX_ registros totales)",
                            search: "Buscar:",
                            paginate: {
                                first: "Primera",
                                previous: "Anterior",
                                next: "Siguiente",
                                last: "Última"
                            },
                            aria: {
                                sortAscending: ": activar para ordenar la columna de manera ascendente",
                                sortDescending: ": activar para ordenar la columna de manera descendente"
                            }
                        },
                        lengthMenu: [5, 10, 25, 50, 100],
                        pageLength: 5,
                        columnDefs: [
                            {
                                targets: 0,
                                orderable: false
                            }
                        ]
                    });
                }

                tabla.DataTable().column(1).visible(false);
            })
            .catch(error => {
                mostrarAlerta("Error al obtener los préstamos:");
            });
    }
    const asesorCarteraSelect = document.getElementById('asesorcartera');
    const centroSelect = document.getElementById('centro_id');
    const grupoSelect = document.getElementById('grupo_id');
    const asesorRecibeSelect = document.getElementById('asesorRecibe');



    btnGuardarTransferencia.addEventListener('click', function (event) {
        event.preventDefault();

        const tabla = $('#tablaTransCartera').DataTable();

        const prestamosSeleccionados = [];

        const asesorCarteraSelect = document.getElementById('asesorcartera');
        const centroSelect = document.getElementById('centro_id');
        const grupoSelect = document.getElementById('grupo_id');
        const asesorRecibeSelect = document.getElementById('asesorRecibe');


        if (!asesorCarteraSelect) {
            console.error("No se encontró select asesorcartera");
            mostrarAlerta("Error interno: select asesorcartera no encontrado", "error");
            return;
        }
        if (!centroSelect) {
            console.error("No se encontró select centro_id");
            mostrarAlerta("Error interno: select centro_id no encontrado", "error");
            return;
        }
        if (!grupoSelect) {
            console.error("No se encontró select grupo_id");
            mostrarAlerta("Error interno: select grupo_id no encontrado", "error");
            return;
        }
        if (!asesorRecibeSelect) {
            console.error("No se encontró select asesorRecibe");
            mostrarAlerta("Error interno: select asesorRecibe no encontrado", "error");
            return;
        }

        const asesorCarteraTexto = asesorCarteraSelect.options[asesorCarteraSelect.selectedIndex]?.text || '';
        const centroTexto = centroSelect.options[centroSelect.selectedIndex]?.text || '';
        const grupoTexto = grupoSelect.options[grupoSelect.selectedIndex]?.text || '';
        const asesorRecibeTexto = asesorRecibeSelect.options[asesorRecibeSelect.selectedIndex]?.text || '';



        tabla.rows().every(function () {
            const row = this.node();
            if (!row) return;
            const checkbox = row.querySelector('.select_row');
            if (checkbox && checkbox.checked) {
                const data = this.data();
                const id = data[1];
                const fecha = data[4];
                prestamosSeleccionados.push({ id, fecha });
            }
        });


        if (prestamosSeleccionados.length > 0 && asesorRecibeSelect.value !== '') {
            const datos = {
                prestamos: prestamosSeleccionados,
                id_asesorReceptor: asesorRecibeSelect.value,
                nombre_asesoremisor: asesorCarteraTexto,
                nombre_centro: centroTexto,
                nombre_grupo: grupoTexto,
                asesorreceptor: asesorRecibeTexto
            };

            fetch('/transferencia/transferircartera', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(datos)
            })
                .then(response => {
                    if (!response.ok) throw new Error("Error en la transferencia");
                    return response.json();
                })
                .then(data => {
                    mostrarAlerta("Transferencia realizada con éxito", "success");
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                })
                .catch(error => {
                    console.error("Error en fetch:", error);
                    mostrarAlerta("Ocurrió un error en la transferencia", "error");
                });

        } else {
            if (prestamosSeleccionados.length === 0) {
                mostrarAlerta("Debe seleccionar al menos un Cliente.", "error");
            }
            if (asesorRecibeSelect.value === '') {
                mostrarAlerta("Por Favor Seleccione un Receptor", "error");
            }
        }
    });



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
