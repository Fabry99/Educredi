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

    document.getElementById('fecha').value = fechalocal;
    document.getElementById('fcontable').value = fechalocal;
    document.getElementById('fabono').value = fechalocal;

    // const today = new Date().toISOString().split('T')[0];

    const comprobante = document.getElementById('comprobante');
    fetch('/caja/obtenercomprobante', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud: ' + response.status);
            }
            return response.json(); // o response.text(), según lo que devuelva tu backend
        })
        .then(data => {
            comprobante.value = data.comprobante
        })
        .catch(error => {
            console.error('Ocurrió un error:', error);
        });




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

    let selectedRows = new Set();

    function formatearFechaDMY(fecha) {
        if (!fecha) return '—';
        const partes = fecha.split('-');
        if (partes.length !== 3) return '—';
        return `${partes[2]}-${partes[1]}-${partes[0]}`;
    }



    function obtenerDatosTabla() {
        const id_centro = selectCentro.value;
        const id_grupo = selectGrupo.value;

        if (!id_centro || !id_grupo) return;

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
            body: JSON.stringify({ id_centro, id_grupo })
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
    <td style='text-align: center;'>${formatearFechaDMY(prestamo.ultima_fecha)}</td>
    <td style='text-align: center;'>${formatearFechaDMY(prestamo.proxima_fecha)}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.cuota || '—')}">${prestamo.cuota || '—'}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.intereses || '—')}">${prestamo.intereses || '—'}</td>
    <td></td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.manejo || '—')}">${prestamo.manejo || '—'}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.seguro || '—')}">${prestamo.seguro || '—'}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.iva || '—')}">${prestamo.iva || '—'}</td>
    <td style='text-align:center' contenteditable="true" data-original="${escapeHtml(prestamo.capital || '—')}">${prestamo.capital || '—'}</td>
    <td style='text-align:center'>${prestamo.dias || '—'}</td>
    <td style='text-align:center'>${formatearFechaDMY(prestamo.fecha_apertura)}</td>
    <td style='text-align:center'>${formatearFechaDMY(prestamo.fecha_vencimiento)}</td>
</tr>
            `);

                    // Restaurar valor original si el campo queda vacío
                    fila.find('[contenteditable="true"]').on('blur', function () {
                        const currentValue = $(this).text().trim();
                        const originalValue = $(this).attr('data-original');
                        if (currentValue === '') {
                            $(this).text(originalValue);
                        }
                    });

                    tablaBody.append(fila);
                });



                // Agrega el evento de selección múltiple + lógica adicional
                tablaBody.off('click').on('click', 'tr', function (event) {
                    const fila = $(this);
                    const celdas = fila.find('td');
                    const id_cliente = celdas.eq(0).text().trim();

                    if (event.ctrlKey || event.metaKey) {
                        if (fila.hasClass('selected')) {
                            fila.removeClass('selected');
                            selectedRows.delete(id_cliente);
                        } else {
                            fila.addClass('selected');
                            selectedRows.add(id_cliente);
                        }

                        const datosFila = {};
                        celdas.each(function (index) {
                            datosFila[`columna_${index}`] = $(this).text().trim();
                        });


                    } else {
                        tablaBody.find('tr').removeClass('selected');
                        selectedRows.clear();

                        fila.addClass('selected');
                        selectedRows.add(id_cliente);

                    }

                    // Guardar datos del cliente seleccionado (último click)
                    const segundoDato = celdas.eq(1).text().trim();
                    const penultimo = celdas.eq(celdas.length - 2).text().trim();
                    const ultimo = celdas.eq(celdas.length - 1).text().trim();

                    const centroSelect = document.getElementById('id_centro');
                    const grupoSelect = document.getElementById('id_grupo');

                    const centroId = centroSelect.value;

                    const grupoId = grupoSelect.value;



                    // Puedes agregarlos también a la variable clienteSeleccionado si los necesitas
                    window.clienteSeleccionado = {
                        id: id_cliente,
                        nombrecliente: segundoDato,
                        Apertura: penultimo,
                        Vencimiento: ultimo,
                        centroId,
                        grupoId

                    };

                    // Obtener conteo de cuotas si el cliente es válido
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
                            id_cliente,
                            Apertura: penultimo,
                            Vencimiento: ultimo,
                            centroId,
                            grupoId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('input_cuota_total').value = `${data.conteo_comparativo} de ${data.conteo_total}`;
                            document.getElementById('cuota_total').value = `${data.total_cuotas}`;
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
    $(document).on('click', function (event) {
        if (!$(event.target).closest('#tablaCaja').length) {
            $('#tablaCaja tbody tr').removeClass('selected');
            selectedRows.clear();
            $('#input_cuota_total').val('');
            $('#cuota_total').val('');
        }
    });
    // Asignar eventos una vez que se genera la tabla
    $('#tablaCaja').on('focus', 'td[contenteditable="true"]', function () {
        // Guardamos valor original al entrar a editar
        const celda = $(this);
        celda.attr('data-original', celda.text().trim());
        celda.data('edited', false); // flag que indica si el usuario escribió algo
    });

    $('#tablaCaja').on('input', 'td[contenteditable="true"]', function () {
        // Cuando el usuario escribe algo, ponemos flag true
        const celda = $(this);
        celda.data('edited', true);
    });

    $('#tablaCaja').on('blur', 'td[contenteditable="true"]', function () {
        const celda = $(this);
        const fila = celda.closest('tr');
        const index = celda.index();

        const currentValue = celda.text().trim();
        const originalValue = celda.attr('data-original');
        const edited = celda.data('edited'); // true si el usuario escribió algo

        // Si está vacío, regresamos el valor original
        if (currentValue === '') {
            celda.text(originalValue);
            return;
        }

        // Sólo recalculamos si el usuario realmente escribió (no solo entró y salió)
        if (!edited) {
            return; // No hacer nada porque no hubo edición
        }

        const cuotaCelda = fila.find('td').eq(5);
        const capitalCelda = fila.find('td').eq(11);
        const saldoCelda = fila.find('td').eq(2);

        const saldoBase = parseFloat(saldoCelda.attr('data-saldo-base')) || parseFloat(saldoCelda.text()) || 0;
        const cuotaActual = parseFloat(cuotaCelda.text()) || 0;

        function recalcular() {
            const intereses = parseFloat(fila.find('td').eq(6).text()) || 0;
            const manejo = parseFloat(fila.find('td').eq(8).text()) || 0;
            const seguro = parseFloat(fila.find('td').eq(9).text()) || 0;
            const iva = parseFloat(fila.find('td').eq(10).text()) || 0;

            const capital = cuotaActual - intereses - manejo - seguro - iva;
            const nuevoSaldo = saldoBase - capital;

            capitalCelda.text(capital.toFixed(2));
            saldoCelda.text(nuevoSaldo.toFixed(2));
        }

        if ([5, 6, 8, 9, 10].includes(index)) {
            recalcular();
        } else if (index === 11) {
            const capitalActual = parseFloat(capitalCelda.text()) || 0;
            const nuevoSaldo = saldoBase - capitalActual;
            saldoCelda.text(nuevoSaldo.toFixed(2));
        }

        // Actualizar valor original y flag
        celda.attr('data-original', currentValue);
        celda.data('edited', false);
    });


    $('#btnGuardar').on('click', function () {
        const btn = $(this);

        if (btn.prop('disabled')) return;

        btn.prop('disabled', true); // Desactivar botón

        const filasSeleccionadas = $('#tablaCaja tbody tr.selected');
        if (filasSeleccionadas.length === 0) {
            mostrarAlerta('No hay filas seleccionadas para guardar.', 'error');
            btn.prop('disabled', false);
            return;
        }

        const fcontable = $('#fcontable').val();
        const fabono = $('#fabono').val();
        const comprobante = $('#comprobante').val();
        const cuenta_id = $('#id_cuenta').val(); // Valor del banco seleccionado
        const id_centro = $('#id_centro').val(); // obtiene el valor (id) seleccionado en el select
        const id_grupo = $('#id_grupo').val();  // acá corriges para tomar el select correcto

        if (!cuenta_id || cuenta_id === '0') {
            mostrarAlerta('Por favor, seleccione una cuenta antes de guardar.', 'error');
            btn.prop('disabled', false);
            return;
        }

        const datosFilas = [];

        filasSeleccionadas.each(function () {
            const celdas = $(this).find('td');
            const filaDatos = {
                cliente_id: celdas.eq(0).text().trim(),
                cliente_nombre: celdas.eq(1).text().trim(),
                saldo: celdas.eq(2).text().trim(),
                ultima_fecha: celdas.eq(3).text().trim(),
                proxima_fecha: celdas.eq(4).text().trim(),
                cuota: celdas.eq(5).text().trim(),
                intereses: celdas.eq(6).text().trim(),
                manejo: celdas.eq(8).text().trim(),
                seguro: celdas.eq(9).text().trim(),
                iva: celdas.eq(10).text().trim(),
                capital: celdas.eq(11).text().trim(),
                dias: celdas.eq(12).text().trim(),
                fecha_apertura: celdas.eq(13).text().trim(),
                fecha_vencimiento: celdas.eq(14).text().trim(),
                fecha_contable: fcontable,
                fecha_abono: fabono,
                comprobante: comprobante,
                id_cuenta: cuenta_id,
                id_centro: id_centro,
                id_grupo: id_grupo,

            };
            datosFilas.push(filaDatos);
        });

        if (datosFilas.length === 0) {
            mostrarAlerta('No hay datos para guardar.', 'info');
            btn.prop('disabled', false);
            return;
        }

        // Mostrar mensaje de "Procesando..."
        mostrarAlerta('Procesando pago...', 'info');
        // Simular envío (reemplaza esto con tu llamada real al backend)
        fetch('/caja/AlmacenarCuota', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ datos: datosFilas })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }

                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("La respuesta no es JSON");
                }

                return response.json();
            })
            .then(data => {
                ocultarAlertaInfo();
                btn.prop('disabled', false);
                if (data.status === 'success') {
                    mostrarAlerta('Pago procesado correctamente.', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarAlerta(data.message || 'Ocurrió un error inesperado.', 'error');
                }
            })
            .catch(error => {
                ocultarAlertaInfo();
                console.error('Error al guardar:', error);
                mostrarAlerta('Error al procesar el pago.', 'error');
                btn.prop('disabled', false);
            });
    });


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

    // Limpiar contenido y clases anteriores
    mensajeAlerta.textContent = mensaje;
    alerta.classList.remove('error_notification', 'success_notification', 'info_notification');

    // Asignar clase según tipo
    if (tipo === "error") {
        alerta.classList.add('error_notification');
    } else if (tipo === "success") {
        alerta.classList.add('success_notification');
    } else if (tipo === "info") {
        alerta.classList.add('info_notification');
    }

    // Mostrar la alerta
    alerta.style.display = 'block';
    setTimeout(() => {
        alerta.classList.add('show');
    }, 10);

    // Solo ocultar automáticamente si el mensaje no es "Procesando pago..."
    if (tipo !== "info") {
        setTimeout(() => {
            alerta.classList.remove('show');
            setTimeout(() => {
                alerta.style.display = 'none';
            }, 500);
        }, 4000);
    }
}
function ocultarAlertaInfo() {
    const alerta = document.getElementById('alert-notification');

    if (alerta.classList.contains('info_notification')) {
        alerta.classList.remove('show');  // Quita la clase show (sin animación)
        alerta.style.display = 'none';    // Oculta inmediatamente
    }
}