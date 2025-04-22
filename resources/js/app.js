import './bootstrap';
import $, { error, event } from 'jquery'; // Importar jQuery
import 'datatables.net-dt';
import 'datatables.net-colreorder';
import 'datatables.net-keytable-dt';
import 'datatables.net-scroller-dt';
import { Button, Modal } from 'bootstrap';

$('#tablaclientesgrupos').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
    "colReorder": true,
    "order": [[0, "asc"]],
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
$('#tablagrupos').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
    "colReorder": true,
    "order": [[0, "asc"]],
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



$('#tablacentros').DataTable({
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
document.addEventListener("DOMContentLoaded", function () {

    // Escuchar los clics en los botones de editar
    const editarBtns = document.querySelectorAll('.btn-editar-centro');

    editarBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const centroId = btn.getAttribute('data-id');  // Obtener el ID desde el data-id
            $('#centro_id_editarcentro').val(centroId);
            $.ajax({
                url: '/obtener-centros/' + centroId, // Ruta definida en Laravel
                type: 'GET',
                success: function (response) {
                    $('#modaleditarcentro #nombrecentro').val(response.nombre);

                    $('#modaleditarcentro').fadeIn();
                },
                error: function () {
                    alert('Error al obtener los datos del cliente.');
                }
            });
        });
    });

    const actualizarCentroBtn = document.querySelector('.btn-actualizarcentros');
    if (actualizarCentroBtn) {
        actualizarCentroBtn.addEventListener('click', function (event) {
            event.preventDefault();
            const centroId = $('#centro_id_editarcentro').val(); // Obtener el ID del centro
            const nombreCentro = $('#nombrecentro').val();


            $.ajax({
                url: '/actualizar-centro/' + centroId,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: centroId,
                    nombrecentro: nombreCentro,
                },
                success: function (response) {
                    $('#custom-alert-message').text(response.success);
                    $('#custom-alert').removeClass('alert-error').addClass('alert-success');
                    $('#custom-alert').fadeIn();

                    setTimeout(function () {
                        $('#custom-alert').fadeOut();
                        location.reload();
                    }, 1000);

                    $('#modaleditarcentro').fadeOut();
                },
                error: function (xhr, status, error) {
                    alert('Error al actualizar el centro.');
                }
            });
        });
    }
});
$(document).ready(function () {
    const table = $('#tablacentros').DataTable();

    $('#tablacentros tbody').on('click', 'tr', function (event) {
        // Si el clic es en el botón de eliminación, no abrir el modal
        if ($(event.target).closest('button').is('.btn-editar-centro')) {
            $('#modalgrupos').fadeOut(); // Cerrar el modal si el clic es en el botón de eliminar
            return; // Detener la ejecución para no abrir el modal
        }
        if ($(event.target).closest('button').is('.btn-eliminar-centro')) {
            $('#modalgrupos').fadeOut(); // Cerrar el modal si el clic es en el botón de eliminar
            return; // Detener la ejecución para no abrir el modal
        }

        // Si no es el botón de eliminar, abre el modal
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
function cargarGruposPorCentro(centroID) {
    $.ajax({
        url: '/grupos-centros/' + centroID,
        method: 'GET',
        success: function (response) {

            // Si DataTable ya está inicializado, destrúyelo para reiniciarlo
            if ($.fn.DataTable.isDataTable('#tablagrupos')) {
                $('#tablagrupos').DataTable().clear().destroy();
            }

            $('#tablagrupos tbody').empty();

            // Iterar sobre cada grupo recibido en la respuesta
            response.grupos.forEach(grupo => {
                const formattedDate = new Date(grupo.created_at).toLocaleString('es-ES', {
                    year: 'numeric', month: '2-digit', day: '2-digit',
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });

                let fila = `  
                    <tr data-id="${grupo.id}" data-nombre="${grupo.nombre}">
                        <td>${grupo.id}</td>
                        <td>${grupo.nombre}</td>
                        <td>${grupo.clientes_count}</td>
                        <td>${formattedDate}</td>`;

                if (esAdministrador) {
                    // Verificar si clientes_count es 0 para mostrar u ocultar el botón "Eliminar"
                    let eliminarBoton = grupo.clientes_count === 0 ? '' : 'style="display:none;"';

                    fila += `
                        <td>
                            <button class="btn btn-eliminarGrupo" ${eliminarBoton} data-id="${grupo.id}">Eliminar</button>
                        </td>`;
                }

                fila += '</tr>';
                $('#tablagrupos tbody').append(fila);
            });
            $(document).on('click', '.btn-eliminarGrupo', function (event) {
                event.stopPropagation();
                $('#modalmostrarcliente').fadeOut();

                var grupoId = $(this).data('id');
                // Aquí puedes realizar la acción de eliminar usando el grupoId
                $.ajax({
                    url: '/eliminar-grupo/' + grupoId,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // Agregar el token CSRF aquí
                    },
                    success: function (response) {
                        // Limpiar la tabla de clientes del grupo
                        $('#tablaclientesgrupos tbody').empty();

                        // Mostrar alerta de éxito
                        $('#custom-alert-message').text(response.success); // Mostrar el mensaje de éxito
                        $('#custom-alert').removeClass('alert-error').addClass('alert-success'); // Asegúrate de tener las clases de estilo
                        $('#custom-alert').fadeIn(); // Mostrar la alerta

                        setTimeout(function () {
                            $('#custom-alert').fadeOut(); // Ocultar la alerta
                            location.reload(); // Recargar la página después de que la alerta desaparezca
                        }, 1000);

                    },
                    error: function (xhr, status, error) {
                        alert('Error al eliminar el grupo. Por favor, inténtalo de nuevo.');
                    }
                });
            });

        },
        error: function () {
            alert('Error al cargar los grupos.');
        }
    });
}
//Mostrar la tabla de clientes al hacer clic en un grupo
$('#tablagrupos tbody').on('click', 'tr', function (event) {
    // Evitar que se abra el modal si el clic es en el botón de eliminar
    if ($(event.target).closest('button').is('.btn-eliminarGrupo')) {
        return; // No abrir el modal si se hace clic en el botón "Eliminar"
    }

    const grupoId = $(this).data('id'); // Obtiene el id
    const nombreGrupo = $(this).data('nombre'); // Obtiene el nombre
    $('#modalmostrarcliente h2').text('Grupo: ' + nombreGrupo);

    // Mostrar el modal
    $('#modalmostrarcliente').fadeIn();
    $('#tablaclientesgrupos tbody').empty();
    $.ajax({
        url: '/clientes-por-grupo/' + grupoId, // Ruta al backend que maneja la consulta
        method: 'GET',
        success: function (response) {
            $('#tablaclientesgrupos tbody').empty();

            response.forEach(grupos => {
                console.log(grupos);
                $('#tablaclientesgrupos tbody').append(`
                                <tr>
                                    <td>${grupos.clientes.id}</td>
                                    <td>${grupos.clientes.nombre}</td>
                                    <td>${grupos.clientes.apellido}</td>
            <td>
                    <!-- Botón para eliminar cliente del grupo -->
                    <button class="btn btn-danger 
                    eliminar-cliente" data-id="${grupos.clientes.id}" 
                    data-centro_id='${grupos.centro_id}'
                    data-grupo_id='${grupos.grupo_id}'>Eliminar del Grupo</button>
                </td>                                    </tr>
                            `);
            });

        },
        error: function (error) {
            alert('No se pudieron cargar los clientes para este grupo.');
        }
    });

});

// Evento para manejar el clic en el botón de "Eliminar de grupo"
$('#tablaclientesgrupos').on('click', '.eliminar-cliente', function () {
    const clienteId = $(this).data('id');
    const centroId = $(this).data('centro_id');
    const grupoId = $(this).data('grupo_id');
    console.log(clienteId, centroId, grupoId);

    $('#tablaclientesgrupos tbody').empty();

    // Realizar la solicitud AJAX para eliminar al cliente del grupo

    fetch('/eliminarclientegrupo', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            cliente_id: clienteId,
            grupo_id: grupoId,
            centro_id: centroId
        })
    })
        .then(response => {
            if (!response.ok) throw new Error('Error al eliminar cliente');
            return response.json();
        })
        .then(data => {
            $('#tablaclientesgrupos tbody').empty();

            // Muestra la alerta de éxito
            $('#custom-alert-message').text(data.success);
            $('#custom-alert').removeClass('alert-error').addClass('alert-success');
            $('#custom-alert').fadeIn().delay(3000).fadeOut();

            // Cierra el modal
            $('#modalmostrarcliente').fadeOut();

            console.log('Cliente eliminado:', data);

            // Refresca la página luego de mostrar todo (si querés)
            setTimeout(() => {
                location.reload();
            }, 1200); // Esperamos a que se vea la alerta
            console.log(data)
        })
        .catch(error => {
            console.error('Error al eliminar cliente:', error);
            $('#custom-alert-message').text('Hubo un error al eliminar el cliente.');
            $('#custom-alert').removeClass('alert-success').addClass('alert-error');
            $('#custom-alert').fadeIn().delay(5000).fadeOut();
        });

});

$('.close-btncerrarmostrarcliente').on('click', function () {
    $('#modalmostrarcliente').fadeOut();
});
$(window).on('click', function (event) {
    if ($(event.target).is('#modalmostrarcliente')) {
        $('#modalmostrarcliente').fadeOut();
    }
});

// Cerrar el modal al presionar ESC
$(document).on('keydown', function (event) {
    if (event.key === "Escape") {
        $('#modalmostrarcliente').fadeOut();
    }
});


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
            $('#cliente_id').val(idCliente); // Opción 1: En el campo oculto
            $('#modaleditarcliente #id_centroeditar')
                .off('change') // 💥 Quita listeners anteriores
                .on('change', function () {
                    let centroId = $(this).val();
                    cargarGrupos(centroId, null);
                });

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
                var grupoSelect = $('#modaleditarcliente #id_grupoeditar');
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
                        }
                    });
                }
            }

            // Función para cargar los municipios según el departamento seleccionado
            function cargarMunicipios(departamentoId, municipioId) {
                var municipioSelect = $('#modaleditarcliente #id_municipioedit');
                municipioSelect.empty();
                municipioSelect.append('<option value="" disabled selected>Seleccione un Municipio</option>');

                if (departamentoId) {
                    $.ajax({
                        url: '/municipios/' + departamentoId,
                        type: 'GET',
                        success: function (data) {
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
                        }
                    });
                }
            }
            $('#id_departamentoeditcliente').on('change', function () {
                var departamentoId = $(this).val();
                cargarMunicipios(departamentoId, null);
            });


            function limpiarCamposModal() {
                $('#modaleditarcliente #id_grupoeditar').html('<option value="" disabled selected>Seleccione un Grupo</option>');
                $('#modaleditarcliente #id_centroeditar').val('').prop('selectedIndex', 0);
            }
            // Función para cerrar el modal al hacer clic en el botón de cerrar
            $('.close-btn1').on('click', function () {
                limpiarCamposModal();
                $('#modaleditarcliente').fadeOut();
            });

            // Cerrar el modal cuando se hace clic fuera de él
            $(window).on('click', function (event) {
                if ($(event.target).is('#modaleditarcliente')) {
                    limpiarCamposModal();
                    $('#modaleditarcliente').fadeOut();
                }
            });

            // Cerrar el modal al presionar la tecla Escape
            $(document).on('keydown', function (event) {
                if (event.key === "Escape") {
                    limpiarCamposModal();
                    $('#modaleditarcliente').fadeOut();
                }
            });


        });

    });

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
