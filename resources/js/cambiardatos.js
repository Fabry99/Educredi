import $, { data } from 'jquery'; // Importa jQuery
import 'datatables.net-dt'; // Importa DataTables
import 'datatables.net-colreorder'; // Importa DataTables colReorder
import 'datatables.net-keytable-dt'; // Importa DataTables keytable
import 'datatables.net-scroller-dt';

$('#tablacambiardatos').DataTable({
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

});
const selectCentro = document.getElementById('centro_idcambiar');
const selectGrupo = document.getElementById('grupo_idcambiar');
const selectFecha = document.getElementById('selectfeapertura');
const selectAsesor = document.getElementById('asesorcambiar');
const tablaBody = document.querySelector('#tablacambiardatos tbody');

const inputPrimerPago = document.getElementById('fechaprimerpago');
const inputFechaVencimiento = document.getElementById('fechavencimiento');

let centro_id = null;
let datosAgrupados = [];

// Listener para cuando el usuario seleccione una fecha
selectFecha.addEventListener('change', function () {
    const fechaSeleccionada = this.value;
    tablaBody.innerHTML = '';

    // Buscar el grupo que coincide con la fecha seleccionada
    const grupo = datosAgrupados.find(item => item.fecha_apertura === fechaSeleccionada);

    if (grupo && grupo.prestamos.length > 0) {
        grupo.prestamos.forEach(prestamo => {
            tablaBody.appendChild(crearFila(prestamo));
        });
    } else {
        tablaBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No hay datos para esta fecha</td></tr>';
    }
});

// Obtener grupos al cambiar centro
selectCentro.addEventListener('change', function () {
    centro_id = this.value;

    fetch('/obtener-grupo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id: centro_id })
    })
        .then(res => res.json())
        .then(data => {
            selectGrupo.innerHTML = '<option value="" selected>Seleccionar:</option>';
            data.forEach(grupo => {
                const option = document.createElement('option');
                option.value = grupo.id;
                option.textContent = grupo.nombre;
                selectGrupo.appendChild(option);
            });

            // Si ya hay grupo seleccionado, disparamos búsqueda
            if (selectGrupo.value) {
                buscarClientes();
            }
        });
});

// Búsqueda cuando cambie grupo o asesor
selectGrupo.addEventListener('change', buscarClientes);
selectAsesor.addEventListener('change', buscarClientes);

// Función para buscar clientes según centro, grupo y asesor (si aplica)
function buscarClientes() {
    const grupo_id = selectGrupo.value;
    const asesor_id = selectAsesor.value; // puede ser vacío

    if (!centro_id || !grupo_id) return; // centro y grupo son requeridos

    fetch('/obtener/clientes/tablas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            id_grupo: grupo_id,
            id_centro: centro_id,
            asesorcambiar: asesor_id || null
        })
    })
        .then(res => res.json())
        .then(data => {
            datosAgrupados = data;
            tablaBody.innerHTML = '';

            if (centro_id === '1' && grupo_id === '1') {
                // Caso especial: mostrar todos los préstamos de inmediato
                selectFecha.innerHTML = '';
                selectFecha.disabled = true;

                datosAgrupados.forEach(grupo => {
                    grupo.prestamos.forEach(prestamo => {
                        tablaBody.appendChild(crearFila(prestamo));
                    });
                });
            } else {
                // Caso general: llenar select de fechas pero NO llenar tabla hasta seleccionar fecha
                selectFecha.disabled = false;
                selectFecha.innerHTML = '<option value="" disabled selected>Seleccionar Fecha:</option>';

                data.sort((a, b) => new Date(b.fecha_apertura) - new Date(a.fecha_apertura));

                data.forEach(item => {
                    if (item.fecha_apertura) {
                        const fecha = new Date(`${item.fecha_apertura}T00:00:00`);
                        const dia = String(fecha.getDate()).padStart(2, '0');
                        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                        const anio = fecha.getFullYear();
                        const fechaFormateada = `${dia}-${mes}-${anio}`;

                        const option = document.createElement('option');
                        option.value = item.fecha_apertura;
                        option.textContent = fechaFormateada;
                        selectFecha.appendChild(option);
                    }
                });

                tablaBody.innerHTML = ''; // limpiar tabla, espera selección fecha
            }
        })
        .catch(error => {
            console.error('Error al obtener clientes:', error);
            mostrarAlerta('Error al obtener clientes', 'error');
        });
}

// Función para crear fila de la tabla, incluyendo data-fecha-primer-pago
function crearFila(prestamo) {
    const tr = document.createElement('tr');

    const fechaApertura = formatDate(prestamo.FECHAAPERTURA);
    const fechaVenc = formatDate(prestamo.FECHAVENCIMIENTO);
    const fechaPrimerPago = formatDate(prestamo.FECHA_PRIMER_PAGO);

    tr.setAttribute('data-fecha-primer-pago', fechaPrimerPago);
    tr.setAttribute('data-dias', prestamo.dias);


    tr.innerHTML = `
        <td>${prestamo.id_cliente}</td>
        <td>${prestamo.PLAZO} Cuotas</td>
        <td>${prestamo.nombre} ${prestamo.apellido}</td>
        <td contenteditable="true" class="editable-monto">$${prestamo.MONTO}</td>
        <td contenteditable="true" class="editable-interes">${Number(prestamo.INTERES).toFixed(2)}</td>
        <td>${fechaApertura}</td>
        <td>${fechaVenc}</td>
    `;
    return tr;
}

// Formatear fecha a d-m-Y
function formatDate(fechaStr) {
    if (!fechaStr) return '';
    const fecha = new Date(`${fechaStr}T00:00:00`);
    const dia = String(fecha.getDate()).padStart(2, '0');
    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
    const anio = fecha.getFullYear();
    return `${dia}-${mes}-${anio}`;
}

// Selección filas: click normal para seleccionar una fila, Ctrl+click para seleccionar varias
tablaBody.addEventListener('click', function (event) {
    let fila = event.target.closest('tr');
    if (!fila) return;

    if (event.ctrlKey || event.metaKey) {
        fila.classList.toggle('selected');
    } else {
        tablaBody.querySelectorAll('tr.selected').forEach(r => r.classList.remove('selected'));
        fila.classList.add('selected');
    }

    const filasSeleccionadas = tablaBody.querySelectorAll('tr.selected');
    if (filasSeleccionadas.length === 1) {
        const filaSeleccionada = filasSeleccionadas[0];
        const celdas = filaSeleccionada.querySelectorAll('td');
        const fechaPrimerPagoTexto = filaSeleccionada.getAttribute('data-fecha-primer-pago') || '';
        const fechaVencimientoTexto = celdas[6].textContent.trim() || '';

        inputPrimerPago.value = fechaPrimerPagoTexto;
        inputFechaVencimiento.value = fechaVencimientoTexto;
    } else {
        inputPrimerPago.value = '';
        inputFechaVencimiento.value = '';
    }
});

// Deseleccionar filas y limpiar inputs al hacer click fuera de la tabla
document.addEventListener('click', function (event) {
    const tabla = document.getElementById('tablacambiardatos');
    if (!tabla.contains(event.target)) {
        tabla.querySelectorAll('tr.selected').forEach(row => row.classList.remove('selected'));
        inputPrimerPago.value = '';
        inputFechaVencimiento.value = '';
    }
});

// Obtener datos de filas seleccionadas
function obtenerFilasSeleccionadas() {
    const filas = document.querySelectorAll('#tablacambiardatos tbody tr.selected');
    const datos = [];

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        const idCliente = celdas[0].textContent.trim();
        const plazo = celdas[1].textContent.trim();
        const nombre = celdas[2].textContent.trim();
        const monto = parseFloat(celdas[3].textContent.replace('$', '').trim()) || 0;
        const interes = parseFloat(celdas[4].textContent.replace('$', '').trim()) || 0;
        const fechaApertura = celdas[5].textContent.trim();
        const fechaVencimiento = celdas[6].textContent.trim();
        const dias = fila.getAttribute('data-dias');


        datos.push({
            id_cliente: idCliente,
            plazo: plazo,
            nombre: nombre,
            monto: monto,
            interes: interes,
            fecha_apertura: fechaApertura,
            fecha_vencimiento: fechaVencimiento,
            dias: dias
        });
    });

    return datos;
}

const id_centro = document.getElementById('centro_idcambiar');
const id_grupo = document.getElementById('grupo_idcambiar');
// Enviar datos seleccionados al backend, pero validar que haya selección
document.getElementById('btnGuardarTransferencia').addEventListener('click', function () {
    const datos = obtenerFilasSeleccionadas();

    if (datos.length === 0) {
        mostrarAlerta('Debe seleccionar al menos una fila antes de enviar.', 'error');
        return;
    }


    fetch('/procesar-datos-seleccionados', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            datos: datos,
            id_centro: id_centro.value,
            id_grupo: id_grupo.value
        })
    })
        .then(res => res.json())
        .then(response => {
            if (response.status == 'success') {
                mostrarAlerta('Prestamos Actualizados Correctamente.', 'success');
                setTimeout(() => location.reload(), 1000);

            } else {
                mostrarAlerta('Ocurrio un Error al Actualizar Prestamo', 'error');
            }
        })
        .catch(error => {
            mostrarAlerta('Error al enviar los datos', 'error');
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
