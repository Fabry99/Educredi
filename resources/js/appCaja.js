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
        function escapeHtml(text) {
            if (typeof text !== 'string') return text;
            return text
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
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
                if (data.datos.length > 0) {
                    window.nombreCentroActual = data.datos[0].centro;
                }
                const tabla = $('#tablaCaja');
                const tablaBody = tabla.find("tbody");
                tablaBody.empty();

                data.datos.forEach(prestamo => {


                    const fila = $(`
<tr>
    <td style='text-align:end'>${prestamo.cliente_id || '—'}</td>
    <td>${prestamo.cliente_nombre}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.saldo || '—')}">${prestamo.saldo || '—'}</td>
    <td style='text-align: center;'>${prestamo.ultima_fecha || '—'}</td>
    <td style='text-align: center;'>${prestamo.proxima_fecha || '—'}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.cuota || '—')}">${prestamo.cuota || '—'}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.intereses || '—')}">${prestamo.intereses || '—'}</td>
    <td></td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.manejo || '—')}">${prestamo.manejo || '—'}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.seguro || '—')}">${prestamo.seguro || '—'}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.iva || '—')}">${prestamo.iva || '—'}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.capital || '—')}">${prestamo.capital || '—'}</td>
    <td style='text-align:center'>${prestamo.dias || '—'}</td>
    <td style='text-align:center'>${prestamo.fecha_apertura || '—'}</td>
    <td style='text-align:center'>${prestamo.fecha_vencimiento || '—'}</td>
</tr>
`);


                    fila.find('[contenteditable="true"]').on('blur', function () {
                        const currentValue = $(this).text().trim();
                        const originalValue = $(this).attr('data-original');
                        if (currentValue === '') {
                            $(this).text(originalValue);
                        }
                    });
                    // Evento click para seleccionar fila y obtener cliente_id
                    fila.on('click', function () {
                        tabla.find("tr").removeClass("selected");
                        $(this).addClass("selected");

                        const celdas = $(this).find('td');

                        const cliente_id = celdas.eq(0).text().trim();    // primer dato
                        const segundoDato = celdas.eq(1).text().trim();   // segundo dato
                        const penultimo = celdas.eq(celdas.length - 2).text().trim();
                        const ultimo = celdas.eq(celdas.length - 1).text().trim();


                        window.clienteSeleccionado = {
                            id: cliente_id,
                            nombrecliente: segundoDato,   // guardo el segundo dato
                            Apertura: penultimo,
                            Vencimiento: ultimo
                        };
                    });
                    tablaBody.append(fila);
                });

                // Evento de clic por fila para obtener el conteo de cuotas
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
    //Eveto para mostrar el estado de cuenta Debe Ser
    const btnMostrarDebeSer = document.getElementById('openModalBtnDebeser');
    btnMostrarDebeSer.addEventListener('click', function () {
        if (!window.clienteSeleccionado || !window.clienteSeleccionado.id) {
            mostrarAlerta('Por favor, selecciona un cliente primero.', 'error');
            return;
        }

        const { id, Apertura, Vencimiento } = window.clienteSeleccionado;
        fetch('/caja/obtenerEstadoCuentaDebeser', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id_cliente: id,
                FechaApertura: Apertura,
                FechaVencimiento: Vencimiento
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.debeser && data.debeser.length > 0) {
                    window.tasa = data.debeser[0].tasa_interes;
                } else {
                }
                const tablaBody = document.querySelector('#tablaGrupos tbody');
                tablaBody.innerHTML = ''; // Limpiar contenido previo

                data.debeser.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>${item.fecha}</td>
                <td style="text-align: right;">${item.cuota}</td>
                <td style="text-align: right;">${item.capital}</td>
                <td style="text-align: right;">${item.intereses}</td>
                <td style="text-align: right;">${item.saldo}</td>
            `;
                    tablaBody.appendChild(row);
                });

                document.getElementById('modal-title').innerHTML = `Detalles Debe Ser<br>${window.clienteSeleccionado.nombrecliente || ''}`;

                if (data.nombreAsesor) {
                    document.getElementById('asesor').value = data.nombreAsesor;
                } else {
                    document.getElementById('asesor').value = '—';
                }
                document.getElementById('centro').value = window.nombreCentroActual || '—';
                document.getElementById('tasa').value = window.tasa || '—';

                // Mostrar el modal
                $('#modalCajaDebeSer').fadeIn('show');
            })
            .catch(error => {
                console.error('Error al obtener el estado de cuenta Debe Ser:', error);
                mostrarAlerta('Error al obtener el estado de cuenta Debe Ser.', 'error');
            });

    })
    // Cerrar el modal al hacer clic en el botón de cerrar
    $('.close-btn1').on('click', function () {
        $('#modalCajaDebeSer').fadeOut();
    });

    // Cerrar el modal si se hace clic fuera de él
    $(window).on('click', function (event) {
        if ($(event.target).is('#modalCajaDebeSer')) {
            $('#modalCajaDebeSer').fadeOut();
        }
    });

    // Cerrar el modal al presionar ESC
    $(document).on('keydown', function (event) {
        if (event.key === "Escape") {
            $('#modalCajaDebeSer').fadeOut();
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