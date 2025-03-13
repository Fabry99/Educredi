import './bootstrap';
import $, { event } from 'jquery'; // Importar jQuery
import 'datatables.net-dt';
import 'datatables.net-colreorder';
import 'datatables.net-keytable-dt';
import 'datatables.net-scroller-dt';

// Inicializar DataTables
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

    //funcion para abrir el modal al hacer clic en una fila de la tabla
    $(document).ready(function() {
        const table = $('.tablaClientes').DataTable();
    
        $('.tablaClientes tbody').on('click', 'tr', function () {
            $('#myModal').fadeIn();  
    
            $('.close-btn').on('click', function () {
                $('#myModal').fadeOut(); 
            });
    
            $(window).on('click', function(event) {
                if ($(event.target).is('#myModal')) {
                    $('#myModal').fadeOut();
                }
            });
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

//abrir modal con boton
var modal = document.getElementById("myModal");
var btn = document.getElementById("openModalBtn");
var closeBtn = document.getElementsByClassName("close-btn")[0];

btn.onclick = function (event) {
    event.preventDefault();
    modal.style.display = "block";
}

closeBtn.onclick = function () {
    modal.style.display = "none";
}

window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
        modal.style.display = "none";
    }
});