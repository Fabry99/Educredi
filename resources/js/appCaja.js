import $ from 'jquery'; // Importa jQuery
import 'datatables.net-dt'; // Importa DataTables
import 'datatables.net-colreorder'; // Importa DataTables colReorder
import 'datatables.net-keytable-dt'; // Importa DataTables keytable
import 'datatables.net-scroller-dt';



$('.table1  ').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
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

document.addEventListener('DOMContentLoaded', function () {

    //Obtener la fecha actual y mostrarla en los inputs
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    const fechalocal = `${year}-${month}-${day}`;

    // const today = new Date().toISOString().split('T')[0];

    document.getElementById('fecha').value = fechalocal;
    document.getElementById('fcontable').value = fechalocal;
    document.getElementById('fabono').value = fechalocal;

    const selectCentro = document.getElementById('id_centro');
    const selectGrupo = document.getElementById('id_grupo');
    let id_centro = null;

    selectCentro.addEventListener('change', obtenergrupos);
    function obtenergrupos() {
        id_centro = selectCentro.value;

        // Limpiar completamente el select de grupos
        selectGrupo.innerHTML = '';

        // Añadir la opción por defecto nuevamente
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.disabled = true;
        defaultOption.selected = true;
        defaultOption.textContent = 'Seleccionar:';
        selectGrupo.appendChild(defaultOption);
        fetch('/trasferencia/obtenergrupos/' + id_centro)
            .then(response => response.json())
            .then(data => {
                const idsUnicos = new Set();

                data.forEach(grupo => {

                    if (!idsUnicos.has(grupo.id)) {
                        idsUnicos.add(grupo.id);

                        const option = document.createElement('option');
                        option.value = grupo.id; // ✅ usa 'id'
                        option.textContent = grupo.nombre; // ✅ usa 'nombre'

                        selectGrupo.appendChild(option);
                    }
                });

                // Opcional: mostrar alerta si no hay grupos válidos
                if (idsUnicos.size === 0) {
                    mostrarAlerta('No se encontraron grupos para este centro.', 'info');
                }
            })
            .catch(error => {
                console.error("Error en la petición:", error); // para depurar
                mostrarAlerta('Error al obtener los grupos:', 'error');
            });
    }

    selectGrupo.addEventListener('change', obtenerDatosTabla);

    function obtenerDatosTabla() {
        const id_centro = selectCentro.value;
        const id_grupo = selectGrupo.value;

        if (!id_centro || !id_grupo) {
            return;
        }

        fetch('/caja/obtenerPrestamos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id_centro: id_centro,
                id_grupo: id_grupo
            })
        })
            .then(response => response.json())
            .then(data => {

                const tabla = $('#tablaCaja');
                const tablaBody = tabla.find("tbody");
                tablaBody.empty();

                data.datos.forEach(prestamo => {
                    const fila = `
            <tr>
                <td style = 'text-align:end'>${prestamo.cliente_id ? prestamo.cliente_id : '—'}</td>
                <td>${prestamo.cliente_nombre}</td>
                <td style = 'text-align:center' contenteditable="true">${prestamo.saldo}</td>
                <td style="text-align: center;">${prestamo.ultima_fecha ? prestamo.ultima_fecha : '—'}</td>
                <td style="text-align: center;">${prestamo.proxima_fecha ? prestamo.proxima_fecha : '—'}</td>
                <td style = 'text-align:center' contenteditable="true">${prestamo.cuota ? prestamo.cuota : '—'}</td>
                <td style = 'text-align:center' contenteditable="true">${prestamo.intereses ? prestamo.intereses : '—'}</td>
                <td></td>
                <td style = 'text-align:center' contenteditable="true">${prestamo.manejo ? prestamo.manejo : '—'}</td>
                <td style = 'text-align:center' contenteditable="true">${prestamo.seguro ? prestamo.seguro : '—'}</td>
                <td style = 'text-align:center' contenteditable="true">${prestamo.iva ? prestamo.iva : '—'}</td>
                <td style='text-align:center' contenteditable="true">${prestamo.capital ? prestamo.capital : '—'}</td>
                <td style = 'text-align:center'>${prestamo.dias ? prestamo.dias : '—'}</td>
                <td style = 'text-align:center'>${prestamo.fecha_apertura ? prestamo.fecha_apertura : '—'}</td>
                <td style = 'text-align:center'>${prestamo.fecha_vencimiento ? prestamo.fecha_vencimiento : '—'}</td>

            </tr>
        `;
                    tablaBody.append(fila);
                });
                // Evento de clic por fila
                tablaBody.find('tr').on('click', function () {
                    const id_cliente = $(this).find('td:first').text().trim();

                    if (!id_cliente || id_cliente === '—') {
                        mostrarAlerta('No se ha seleccionado un cliente válido.', 'error');
                        return;
                    }

                    fetch('/caja/obtenerconteocuotas', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            id_cliente: id_cliente,
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('input_cuota_total').value = `${data.conteo_comparativo} de ${data.conteo_total}`;

                        })
                        .catch(error => {
                            console.error('Error al obtener el conteo de cuotas:', error);
                        });
                });

            })
            .catch(error => {
                console.error('Error al obtener los datos:', error);
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