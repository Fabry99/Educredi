import './bootstrap';
import $, { event } from 'jquery'; // Importar jQuery
import 'datatables.net-dt';
import 'datatables.net-colreorder';
import 'datatables.net-keytable-dt';
import 'datatables.net-scroller-dt';
import { Modal } from 'bootstrap';


// Inicializar DataTables clientes
$(document).ready(function () {
    const table = $('#mitabla').DataTable({
        "paging": true,  // Habilita la paginación
        "lengthChange": true,  // Permite cambiar la cantidad de registros por página
        "searching": true,  // Habilita la búsqueda
        "ordering": true,  // Habilita el orden de las columnas
        "info": true,  // Muestra información sobre los registros
        "autoWidth": false,  // Desactiva el ajuste automático de anchos de columnas
        "responsive": true,  // Hace la tabla responsive
        "colReorder": true,
        "order": [[0, "desc"]],  // Ordena por la primera columna de forma ascendente
        "language": {
            "decimal": ",",  // Configuración del separador decimal
            "thousands": ".",  // Configuración del separador de miles
            "lengthMenu": "Mostrar _MENU_ registros por página",  // Menú para seleccionar cuántos registros mostrar
            "zeroRecords": "No se encontraron resultados",  // Mensaje si no hay registros
            "info": "Mostrando página _PAGE_ de _PAGES_",  // Mensaje de información sobre las páginas
            "infoEmpty": "No hay registros disponibles",  // Mensaje cuando no hay registros
            "infoFiltered": "(filtrado de _MAX_ registros totales)",  // Mensaje si hay filtros aplicados
            "search": "Buscar:",  // Texto del campo de búsqueda
            "paginate": {
                "first": "Primera",  // Botón para ir a la primera página
                "previous": "Anterior",  // Botón para ir a la página anterior
                "next": "Siguiente",  // Botón para ir a la página siguiente
                "last": "Última"  // Botón para ir a la última página
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
        const table = $('.tablaClientes').DataTable();

        // Cuando se hace clic en una fila de la tabla
        $('.tablaClientes tbody').on('click', 'tr', function () {
            const rowData = table.row(this).data(); // Obtiene los datos de la fila seleccionada
            const idCliente = rowData[0]; // Asume que el ID del cliente está en la primera columna (puedes ajustarlo según tu estructura)
            console.log(idCliente);
            $('#cliente_id').val(idCliente); // Opción 1: En el campo oculto

            // Realizar una solicitud AJAX para obtener más información sobre el cliente
            $.ajax({
                url: '/obtener-cliente/' + idCliente, // Ruta definida en Laravel
                type: 'GET',
                success: function (response) {

                    // Asigna los datos al modal
                    $('#modaleditarcliente #nombre').val(response.nombre);
                    $('#modaleditarcliente #apellido').val(response.apellido);
                    $('#modaleditarcliente #direccion').val(response.direccion);
                    $('#modaleditarcliente #teloficina').val(response.telefono_oficina);
                    $('#modaleditarcliente #dir_negocio').val(response.direc_trabajo);
                    $('#modaleditarcliente #sector').val(response.sector);
                    $('#modaleditarcliente #actividadeconomica').val(response.act_economica);
                    $('#modaleditarcliente #NIT').val(response.nit);
                    $('#modaleditarcliente #id_departamentoeditcliente').val(response.departamento.id);
                    $('#modaleditarcliente #id_municipioedit').val(response.municipio.id);
                    $('#modaleditarcliente #ocupacion').val(response.ocupacion);
                    $('#modaleditarcliente #firma').val(response.puede_firmar);
                    $('#modaleditarcliente #dui').val(response.dui);
                    $('#modaleditarcliente #expedida').val(response.lugar_expe);
                    $('#modaleditarcliente #expedicion').val(response.fecha_expedicion);
                    $('#modaleditarcliente #lugarnacimiento').val(response.lugar_nacimiento);
                    $('#modaleditarcliente #fecha_nacimiento').val(response.fecha_nacimiento);
                    $('#modaleditarcliente #nacionalidad').val(response.nacionalidad);
                    $('#modaleditarcliente #genero').val(response.genero);
                    $('#modaleditarcliente #telcasa').val(response.telefono_casa);
                    $('#modaleditarcliente #estado_civil').val(response.estado_civil);
                    $('#modaleditarcliente #nrc').val(response.nrc);
                    $('#modaleditarcliente #perdependiente').val(response.persona_dependiente);
                    $('#modaleditarcliente #conyugue').val(response.nombre_conyugue);
                    $('#modaleditarcliente #sueldo').val(response.ing_economico);
                    $('#modaleditarcliente #egreso').val(response.egre_economico);
                    $('#modaleditarcliente #otroingreso').val(response.otros_ing);
                    if (response.centro) {
                        $('#modaleditarcliente #id_centro').val(response.centro.id);
                    } else {
                        $('#modaleditarcliente #id_centro').val(''); // Permite la selección de un nuevo centro
                    }

                    if (response.grupo) {
                        $('#modaleditarcliente #id_grupo').val(response.grupo.id);
                    } else {
                        $('#modaleditarcliente #id_grupo').val(''); // Dejar en blanco para permitir selección
                    }

                    // Si response.centro existe, cargar los grupos asociados
                    if (response.centro) {
                        cargarGrupos(response.centro.id, response.grupo);
                    } else {
                        cargarGrupos(null, null); // Llamar la función para resetear el select de grupos
                    }
                    $('#modaleditarcliente #id_centro').on('change', function () {
                        let centroId = $(this).val();
                        cargarGrupos(centroId, null); // Cargar grupos cuando se seleccione un centro
                    });


                    console.log(response);
                    // Actualizar el título del modal
                    $('#modaleditarcliente h2').html('Editar Cliente: ' + response.nombre.toUpperCase() + '&nbsp;' + response.apellido.toUpperCase());

                    // Cargar los municipios para el departamento seleccionado
                    cargarMunicipios(response.departamento.id, response.municipio.id);
                    $('#form-editar-cliente').attr('action', '/clientes/actualizar/' + response.id);

                    // Mostrar el modal
                    $('#modaleditarcliente').fadeIn();
                },
                error: function () {
                    alert('Error al obtener los datos del cliente.');
                }
            });

            $('#form-editar-cliente').submit(function (e) {
                e.preventDefault(); // Prevenir que se envíe el formulario de manera tradicional

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function (response) {
                        // Aquí puedes hacer algo con la respuesta, como mostrar un mensaje de éxito
                        alert('Cliente actualizado correctamente');
                        $('#modaleditarcliente').fadeOut(); // Cierra el modal
                        location.reload(); // Recarga la página para reflejar los cambios
                    },
                    error: function () {
                        alert('Error al actualizar el cliente.');
                    }
                });
            });

            // Cargar grupos
            function cargarGrupos(centroId, grupoId) {
                console.log(centroId);
                var grupoSelect = $('#modaleditarcliente #id_grupo');
                grupoSelect.empty(); // Limpiar el select
                grupoSelect.append('<option value="" disabled selected>Seleccione un Grupo</option>'); // Reset

                if (centroId) {
                    // Realizar una solicitud AJAX para obtener los grupos del centro
                    $.ajax({
                        url: '/grupos/' + centroId,
                        type: 'GET',
                        success: function (data) {
                            if (data.length > 0) {
                                data.forEach(function (grupo) {
                                    var option = $('<option></option>')
                                        .val(grupo.id)
                                        .text(grupo.nombre);
                                    grupoSelect.append(option);
                                });

                                // Si el cliente tiene un grupo asignado y no es null, seleccionarlo
                                if (grupoId && grupoId.id) {
                                    grupoSelect.val(grupoId.id);
                                }
                            }
                        },
                        error: function () {
                            console.error("Error al cargar los grupos.");
                        }
                    });
                }
            }

            // Función para cargar los municipios según el departamento seleccionado
            function cargarMunicipios(departamentoId, municipioId) {
                console.log("Cargando municipios para el departamento ID:", departamentoId);  // Agregado
                var municipioSelect = $('#modaleditarcliente #id_municipioedit');
                municipioSelect.empty();
                municipioSelect.append('<option value="" disabled selected>Seleccione un Municipio</option>');

                if (departamentoId) {
                    $.ajax({
                        url: '/municipios/' + departamentoId,
                        type: 'GET',
                        success: function (data) {
                            console.log("Municipios recibidos:", data);  // Agregado para verificar los datos
                            if (data.length > 0) {
                                data.forEach(function (municipio) {
                                    var option = $('<option></option>')
                                        .val(municipio.id)
                                        .text(municipio.nombre);
                                    municipioSelect.append(option);
                                });

                                if (municipioId) {
                                    municipioSelect.val(municipioId);
                                }
                            }
                        },
                        error: function () {
                            console.error("Error al cargar municipios.");
                        }
                    });
                }
            }
            $('#id_departamentoeditcliente').on('change', function () {
                var departamentoId = $(this).val();
                cargarMunicipios(departamentoId, null);
            });



            // Función para cerrar el modal al hacer clic en el botón de cerrar
            $('.close-btn1').on('click', function () {
                $('#modaleditarcliente').fadeOut();
            });

            // Cerrar el modal cuando se hace clic fuera de él
            $(window).on('click', function (event) {
                if ($(event.target).is('#modaleditarcliente')) {
                    $('#modaleditarcliente').fadeOut();
                }
            });

            // Cerrar el modal al presionar la tecla Escape
            $(document).on('keydown', function (event) {
                if (event.key === "Escape") {
                    $('#modaleditarcliente').fadeOut();
                }
            });

           
        });




    });
   
 

    function cargarGruposPorCentro(centroID) {
        $.ajax({
            url: '/grupos-por-centro/' + centroID, // Ruta del backend
            method: 'GET',
            success: function (response) {
                // Destruir DataTable anterior si existe
                if ($.fn.DataTable.isDataTable('#tablagrupos')) {
                    $('#tablagrupos').DataTable().destroy();
                }

                // Limpiar la tabla antes de agregar nuevos datos
                $('#tablagrupos tbody').empty();

                // Insertar filas con los grupos obtenidos
                response.forEach(grupo => {
                    const formattedDate = new Date(grupo.created_at).toLocaleString('es-ES', {
                        year: 'numeric', month: '2-digit', day: '2-digit',
                        hour: '2-digit', minute: '2-digit', second: '2-digit'
                    });

                    $('#tablagrupos tbody').append(`
                        <tr>
                            <td>${grupo.id}</td>
                            <td>${grupo.nombre}</td>
                            <td>${grupo.cantidad_personas}</td>
                            <td>${formattedDate}</td>
                        </tr>
                    `);
                });

                $(document).ready(function () {
                    const table = $('#tablagrupos').DataTable({
                        "paging": true,  // Habilita la paginación
                        "lengthChange": true,  // Permite cambiar la cantidad de registros por página
                        "searching": true,  // Habilita la búsqueda
                        "ordering": true,  // Habilita el orden de las columnas
                        "info": true,  // Muestra información sobre los registros
                        "autoWidth": false,  // Desactiva el ajuste automático de anchos de columnas
                        "responsive": true,  // Hace la tabla responsive
                        "colReorder": true,
                        "order": [[0, "asc"]],  // Ordena por la primera columna de forma ascendente
                        "language": {
                            "decimal": ",",  // Configuración del separador decimal
                            "thousands": ".",  // Configuración del separador de miles
                            "lengthMenu": "Mostrar _MENU_ registros por página",  // Menú para seleccionar cuántos registros mostrar
                            "zeroRecords": "No se encontraron resultados",  // Mensaje si no hay registros
                            "info": "Mostrando página _PAGE_ de _PAGES_",  // Mensaje de información sobre las páginas
                            "infoEmpty": "No hay registros disponibles",  // Mensaje cuando no hay registros
                            "infoFiltered": "(filtrado de _MAX_ registros totales)",  // Mensaje si hay filtros aplicados
                            "search": "Buscar:",  // Texto del campo de búsqueda
                            "paginate": {
                                "first": "Primera",  // Botón para ir a la primera página
                                "previous": "Anterior",  // Botón para ir a la página anterior
                                "next": "Siguiente",  // Botón para ir a la página siguiente
                                "last": "Última"  // Botón para ir a la última página
                            },
                            "aria": {
                                "sortAscending": ": activar para ordenar la columna de manera ascendente",
                                "sortDescending": ": activar para ordenar la columna de manera descendente"
                            }
                        },
                        "lengthMenu": [5, 10, 25, 50, 100],
                        "pageLength": 5
                    });

                });

            },
            error: function () {
                alert('Error al cargar los grupos.');
            }
        });
    }
    // Función para abrir el modal al hacer clic en una fila de la tabla centros
    $(document).ready(function () {
        const table = $('#mitabla').DataTable();

        $('#mitabla tbody').on('click', 'tr', function () {
            const rowData = table.row(this).data(); // Obtiene los datos de la fila seleccionada
            const centroID = rowData[0]; // ID del centro (Columna 0)
            const nombreCentro = rowData[1]; // Nombre del centro (Columna 1)

            // Actualizar el título del modal
            $('#modalgrupos h2').text('Grupos del Centro - ' + nombreCentro);

            // Llamar a la función para cargar los grupos
            cargarGruposPorCentro(centroID);

            // Mostrar el modal
            $('#modalgrupos').fadeIn();
        });

        // Cerrar el modal al hacer clic en el botón de cerrar
        $('.close-btn1').on('click', function () {
            $('#modalgrupos').fadeOut();
        });

        // Cerrar el modal si se hace clic fuera de él
        $(window).on('click', function (event) {
            if ($(event.target).is('#modalgrupos')) {
                $('#modalgrupos').fadeOut();
            }
        });

        // Cerrar el modal al presionar ESC
        $(document).on('keydown', function (event) {
            if (event.key === "Escape") {
                $('#modalgrupos').fadeOut();
            }
        });
    });

});

//Tabla de grupos



// Funcion para desplegar el subMenu del sidebar
document.addEventListener("DOMContentLoaded", function () {
    const menuItems = document.querySelectorAll('.menu-item-dropdown');

    menuItems.forEach(item => {
        const link = item.querySelector('.menu-link');
        const subMenu = item.querySelector('.sub-menu');

        link.addEventListener('click', function (e) {
            e.preventDefault();

            const isActive = item.classList.toggle('sub-menu-toggle');

            if (isActive) {
                subMenu.style.height = `${subMenu.scrollHeight + 6}px`;
                subMenu.style.padding = '0.2rem 0';
            } else {
                subMenu.style.height = '0';
                subMenu.style.padding = '0';
            }

            menuItems.forEach(otherItem => {
                if (otherItem !== item) {
                    const otherSubMenu = otherItem.querySelector('.sub-menu');
                    if (otherSubMenu) {
                        otherItem.classList.remove('sub-menu-toggle');
                        otherSubMenu.style.height = '0';
                        otherSubMenu.style.padding = '0';
                    }
                }
            });
        });
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
        }
    }

    // Mapeo de botones a modales
    let botones = {
        "openModalBtnnuevocentro": "modalnuevocentro",
        "openModalBtnnuevogrupo": "modalnuevogrupo",
        "openModalBtn": "myModal"
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
            let modal = this.closest(".modal");
            cerrarModal(modal);
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
            document.querySelectorAll(".modal").forEach((modal) => {
                cerrarModal(modal);
            });
        }
    });
});


