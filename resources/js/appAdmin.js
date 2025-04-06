import './bootstrap';
import $, { error, event } from 'jquery'; // Importar jQuery
import 'datatables.net-dt';
import 'datatables.net-colreorder';
import 'datatables.net-keytable-dt';
import 'datatables.net-scroller-dt';

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
    "order": [[4, "desc"]],
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
            url:'/admin/usurios/obtener-user/' + id_user,
            type:'GET',
            success:function(response){
                console.log(response);
                $('#modaleditaruser #nombreupdate').val(response.name);
                $('#modaleditaruser #apellidoupdate').val(response.last_name);
                $('#modaleditaruser #correoupdate').val(response.email);
                $('#modaleditaruser #rolupdate').val(response.rol);
                $('#modaleditaruser #actividadupdate').val(response.estado);
                $('#modaleditaruser #nacimientoupdate').val(response.fecha_nacimiento);
                $('#modaleditaruser #passwordupdate').val(response.password);

            },
            error:function(){
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
        }
    }

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
