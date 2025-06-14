import $, { data } from 'jquery'; // Importa jQuery
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
                        option.value = grupo.id;
                        option.textContent = grupo.nombre;
                        option.dataset.nombre = grupo.nombre;

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
    let nombre_centro = null;
    let nombre_grupo = '';

    function formatearFechaDMY(fecha) {
        if (!fecha) return '—';
        const partes = fecha.split('-');
        if (partes.length !== 3) return '—';
        return `${partes[2]}-${partes[1]}-${partes[0]}`;
    }


    let tablaDataTable;
    function obtenerDatosTabla() {
        const tabla = $('#tablaCaja');
        const tablaBody = tabla.find("tbody");
        const id_centro = selectCentro.value;
        const id_grupo = selectGrupo.value;

        if ($.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable().destroy();
        }

        tablaBody.empty();

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
                    window.saldosAnteriores = {};
                }


                data.datos.forEach(prestamo => {
                    window.saldosAnteriores[prestamo.cliente_id] = parseFloat(prestamo.saldo) || 0;

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

                tablaDataTable = tabla.DataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: false,
                    colReorder: true,
                    order: [[0, "asc"]],
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
                    pageLength: 5
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

                    nombre_centro = selectCentro.options[selectCentro.selectedIndex]?.dataset?.nombre || '';
                    nombre_grupo = selectGrupo.options[selectGrupo.selectedIndex]?.dataset?.nombre || '';


                    // Puedes agregarlos también a la variable clienteSeleccionado si los necesitas
                    window.clienteSeleccionado = {
                        id: id_cliente,
                        nombrecliente: segundoDato,
                        Apertura: penultimo,
                        Vencimiento: ultimo,
                        centroId,
                        grupoId,

                    };
                    window.clienteSeleccionado.saldoAnterior = window.saldosAnteriores[id_cliente] || 0;


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
                            mostrarAlerta('Error al obtener el conteo de cuotas:', "error");
                        });



                });

            })
            .catch(error => {
                mostrarAlerta('Error al obtener los datos:', "error");
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
            const cliente_id = celdas.eq(0).text().trim();

            const filaDatos = {
                cliente_id: cliente_id,
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
                saldo_anterior: window.saldosAnteriores[cliente_id] || 0,
                nombre_centro: nombre_centro,
                nombre_grupo: nombre_grupo,


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
                    if (data.pdf) {
                        const pdfWindow = window.open(""); // Abrir nueva ventana
                        pdfWindow.document.write(`
                                <html>
                                    <head>
                                        <title>Comprobante de Pago</title>
                                    </head>
                                    <body style="margin:0">
                                        <embed width="100%" height="100%" src="data:application/pdf;base64,${data.pdf}" type="application/pdf">
                                    </body>
                                </html>
                            `);
                    }
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
        $('#modalCajaEstadoCuenta').fadeOut();

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
            $('#modalCajaEstadoCuenta').fadeOut();
        }
    });


    document.getElementById('fapertura').addEventListener('change', function () {
        const fechaSeleccionada = this.value;
        const registros = window.mapaFechasRegistros?.[fechaSeleccionada] || [];
        mostrarRegistrosEnTabla(registros);
    });

    const btnMostrarEstadoCuenta = document.getElementById('openModalBtnEstadoCuenta');
    btnMostrarEstadoCuenta.addEventListener('click', function () {
        const centroId = $('#id_centro').val();
        const grupoId = $('#id_grupo').val();

        if (centroId === null || centroId === '') {
            mostrarAlerta('Por favor, selecciona un Centro.', 'error');
            return;
        }
        if (grupoId === null || grupoId === '') {
            mostrarAlerta('Por favor, selecciona un Grupo.', 'error');
            return;
        }
        fetch('/caja/obtenerEstadoCuenta', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }, body: JSON.stringify({
                id_centro: centroId,
                id_grupo: grupoId
            })
        }).then(response => response.json())
            .then(data => {
                const selectFecha = document.getElementById('fapertura');
                selectFecha.innerHTML = ''; // Limpiar opciones anteriores
                if (data.movimientos_presta && data.movimientos_presta.length > 0 &&
                    data.datoscuotas && data.datoscuotas.length > 0) {

                    // Procesar fechas: extraer solo la fecha inicial
                    const fechasProcesadas = data.movimientos_presta
                        .filter(item => item.grupo_fecha !== '') // Filtrar fechas vacías
                        .map(item => ({
                            fechaCompleta: item.grupo_fecha,
                            fechaInicial: item.grupo_fecha.split(' / ')[0],
                            registros: item.registros
                        }));

                    // Ordenar fechas de más reciente a más antigua
                    fechasProcesadas.sort((a, b) => new Date(b.fechaInicial) - new Date(a.fechaInicial));

                    // Procesar datoscuotas para fácil acceso
                    const cuotasPorFecha = {};
                    data.datoscuotas.forEach(cuota => {
                        if (cuota.grupo_fecha !== '') {
                            const fechaKey = cuota.grupo_fecha.split(' / ')[0];
                            cuotasPorFecha[fechaKey] = cuota.prestamos;
                        }
                    });

                    // Llenar el select con las fechas
                    fechasProcesadas.forEach((item, index) => {
                        const option = document.createElement('option');
                        option.value = item.fechaInicial; // yyyy-mm-dd
                        option.textContent = formatearFechaDMY(item.fechaInicial); // dd-mm-yyyy
                        if (index === 0) option.selected = true;
                        selectFecha.appendChild(option);
                    });


                    // Mostrar los registros de la fecha más reciente al cargar
                    if (fechasProcesadas.length > 0) {
                        const primeraFecha = fechasProcesadas[0].fechaInicial;
                        mostrarRegistrosEnTabla(fechasProcesadas[0].registros);
                        mostrarCuotasEnTabla(cuotasPorFecha[primeraFecha]);
                    }

                    // Event listener para cuando cambie la selección de fecha
                    selectFecha.addEventListener('change', function () {
                        const fechaSeleccionada = this.value;

                        // Encontrar los registros para esta fecha
                        const fechaData = fechasProcesadas.find(f => f.fechaInicial === fechaSeleccionada);
                        if (fechaData) {
                            mostrarRegistrosEnTabla(fechaData.registros);
                        }

                        // Mostrar las cuotas correspondientes
                        if (cuotasPorFecha[fechaSeleccionada]) {
                            mostrarCuotasEnTabla(cuotasPorFecha[fechaSeleccionada]);
                        } else {
                            mostrarCuotasEnTabla([]);
                        }
                    });

                } else {
                    // No hay datos suficientes
                    const option = document.createElement('option');
                    option.textContent = 'No hay datos disponibles';
                    selectFecha.appendChild(option);
                    mostrarRegistrosEnTabla([]);
                    mostrarCuotasEnTabla([]);
                }


                $('#modalCajaEstadoCuenta').fadeIn('show');


            }).catch(error => {
                mostrarAlerta('Error al obtener el Estado de Cuentas:', "error");
            });


    });
    function mostrarCuotasEnTabla(prestamos) {
        const tbody = document.querySelector('#tablaDetallesGrupo tbody');
        tbody.innerHTML = ''; // Limpiar tabla
        if (!prestamos || prestamos.length === 0) {
            const filaVacia = document.createElement('tr');
            filaVacia.innerHTML = '<td colspan="9" style="text-align: center;">No hay préstamos para mostrar</td>';
            tbody.appendChild(filaVacia);
            return;
        }

        // Inicializar totales
        let totalIntNormal = 0;
        let totalIntMora = 0;
        let totalSeguro = 0;
        let totalMicroSeg = 0;
        let totalIva = 0;
        let totalCapital = 0;
        let totalValor = 0;
        let totalMonto = 0;
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        prestamos.forEach(item => {
            const p = item.prestamo;
            const c = item.proxima_cuota;

            const fechaCuota = new Date(c.fecha);
            fechaCuota.setHours(0, 0, 0, 0);

            let interesMora = 0;
            if (hoy > fechaCuota) {
                const diffTime = hoy - fechaCuota; // diferencia en ms
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)); // diferencia en días

                const saldoPendiente = parseFloat(c.saldo) || 0;

  
                const tasaInteresAnual = parseFloat(c.tasa_interes) || 0;
                const tasaInteresMensual = tasaInteresAnual / 12 / 100; // porcentaje mensual en decimal

                // Calcular interés mora proporcional a los días de retraso:
                const interesMora = saldoPendiente * tasaInteresMensual * (diffDays / 30);

                // Ya tienes el interés de mora calculado
            }

            totalIntNormal += parseFloat(c.intereses) || 0;
            totalIntMora += interesMora;
            totalSeguro += parseFloat(c.seguro) || 0;
            totalMicroSeg += parseFloat(c.manejo) || 0;
            totalIva += parseFloat(c.iva) || 0;
            totalCapital += parseFloat(c.capital) || 0;
            totalValor += parseFloat(c.cuota) || 0;
            totalMonto += parseFloat(p.MONTO) || 0;  // <-- Acumula el monto del préstamo


            if (prestamos.length > 0) {
                const primerPrestamo = prestamos[0].prestamo;
                const nombreCentro = primerPrestamo.centro_nombre || '';
                const nombreGrupo = primerPrestamo.grupo_nombre || '';

                // Mostrar en el span centros-grupos
                document.getElementById('centros-grupos').textContent = `${nombreCentro} - ${nombreGrupo}`;
            }

            document.getElementById('valorponersealdia').value = totalValor.toFixed(2);
            document.getElementById('monto').value = totalMonto.toFixed(2);


            const nombreCompleto = (p.nombre || '') + ' ' + (p.apellido || '');

            const fila = document.createElement('tr');
            fila.innerHTML = `
            <td>${p.id_cliente || ''}</td>
            <td>${nombreCompleto}</td>
            <td>${parseFloat(c.intereses || 0).toFixed(2)}</td>
            <td>${parseFloat(item.int_mora || 0).toFixed(2)}</td>
            <td>${parseFloat(c.seguro || 0).toFixed(2)}</td>
            <td>${parseFloat(c.manejo || 0).toFixed(2)}</td>
            <td>${parseFloat(c.iva || 0).toFixed(2)}</td>
            <td>${parseFloat(c.capital || 0).toFixed(2)}</td>
            <td>${parseFloat(c.cuota || 0).toFixed(2)}</td>
        `;
            tbody.appendChild(fila);
        });

        // Agregar fila de totales
        const filaTotales = document.createElement('tr');
        filaTotales.style.fontWeight = 'bold';
        filaTotales.innerHTML = `
        <td colspan="2" style="text-align: right;">Totales:</td>
        <td>${totalIntNormal.toFixed(2)}</td>
        <td>${totalIntMora.toFixed(2)}</td>
        <td>${totalSeguro.toFixed(2)}</td>
        <td>${totalMicroSeg.toFixed(2)}</td>
        <td>${totalIva.toFixed(2)}</td>
        <td>${totalCapital.toFixed(2)}</td>
        <td>${totalValor.toFixed(2)}</td>
    `;
        tbody.appendChild(filaTotales);
    }

    function mostrarRegistrosEnTabla(registros) {
        const tbody = document.querySelector('#tablaPagosRecib tbody');
        tbody.innerHTML = ''; // Limpiar tabla

        // Inicializar acumuladores
        let totalCuota = 0;
        let totalCapital = 0;
        let totalIntApli = 0;
        let totalIntMora = 0;
        let totalManejo = 0;
        let totalSeguro = 0;
        let totalIva = 0;
        let totalSaldo = 0;

        if (registros.length === 0) {
            const filaVacia = document.createElement('tr');
            filaVacia.innerHTML = '<td colspan="9" style="text-align: center;">No hay registros para mostrar</td>';
            tbody.appendChild(filaVacia);
            return;
        }

        registros.forEach(reg => {
            // Convertir a número y acumular (manejar posibles valores vacíos o null)
            totalCuota += parseFloat(reg.valor_cuota) || 0;
            totalCapital += parseFloat(reg.capital) || 0;
            totalIntApli += parseFloat(reg.int_apli) || 0;
            totalIntMora += parseFloat(reg.int_mora) || 0;
            totalManejo += parseFloat(reg.manejo) || 0;
            totalSeguro += parseFloat(reg.seguro) || 0;
            totalIva += parseFloat(reg.iva) || 0;
            totalSaldo += parseFloat(reg.saldo) || 0;

            const fila = document.createElement('tr');
            fila.innerHTML = `
            <td>${formatearFechaDMY(reg.fecha)}</td>
            <td>${parseFloat(reg.valor_cuota || 0).toFixed(2)}</td>
            <td>${parseFloat(reg.capital || 0).toFixed(2)}</td>
            <td>${parseFloat(reg.int_apli || 0).toFixed(2)}</td>
            <td>${parseFloat(reg.int_mora || 0).toFixed(2)}</td>
            <td>${parseFloat(reg.manejo || 0).toFixed(2)}</td>
            <td>${parseFloat(reg.seguro || 0).toFixed(2)}</td>
            <td>${parseFloat(reg.iva || 0).toFixed(2)}</td>
            <td>${parseFloat(reg.saldo || 0).toFixed(2)}</td>
        `;
            tbody.appendChild(fila);
        });


        // Agregar fila de totales
        const filaTotales = document.createElement('tr');
        filaTotales.style.fontWeight = 'bold';
        filaTotales.innerHTML = `
        <td style="text-align: right;">Totales:</td>
        <td>${totalCuota.toFixed(2)}</td>
        <td>${totalCapital.toFixed(2)}</td>
        <td>${totalIntApli.toFixed(2)}</td>
        <td>${totalIntMora.toFixed(2)}</td>
        <td>${totalManejo.toFixed(2)}</td>
        <td>${totalSeguro.toFixed(2)}</td>
        <td>${totalIva.toFixed(2)}</td>
        <td></td>
    `;
        tbody.appendChild(filaTotales);
    }
    function formatearFechaDMY(fechaStr) {
        if (!fechaStr) return '';
        const partes = fechaStr.split('-');
        if (partes.length !== 3) return fechaStr;

        const anio = partes[0];
        const mes = partes[1];
        const dia = partes[2];

        return `${dia}-${mes}-${anio}`;
    }


    $(window).on('click', function (event) {
        if ($(event.target).is('#modalCajaEstadoCuenta')) {
            $('#modalCajaDebeSer').fadeOut();
            $('#modalCajaEstadoCuenta').fadeOut();

        }
    });


});
document.getElementById('btnGenerarPDF').addEventListener('click', function () {
    // 1. Extraer valores de inputs
    const monto = document.getElementById('monto').value;
    const valorPonerseAlDia = document.getElementById('valorponersealdia').value;

    const centrosGruposText = document.getElementById('centros-grupos').textContent.trim();
    // Asumiendo que está en formato "Centro - Grupo"
    const [nombreCentro, nombreGrupo] = centrosGruposText.split(' - ');
    // 2. Extraer opción seleccionada en el select
    const fechaSeleccionada = document.getElementById('fapertura').value;

    // 3. Extraer datos visibles de la tabla de cuotas
    const filasDetalles = document.querySelectorAll('#tablaDetallesGrupo tbody tr');
    const datosCuotas = [];
    filasDetalles.forEach((fila, index) => {
        // Evitar incluir fila "No hay datos..." o totales si es necesario
        const celdas = fila.querySelectorAll('td');
        if (celdas.length === 9 && !fila.innerText.includes('Totales')) {
            datosCuotas.push({
                id_cliente: celdas[0].innerText.trim(),
                nombre: celdas[1].innerText.trim(),
                int_normal: celdas[2].innerText.trim(),
                int_mora: celdas[3].innerText.trim(),
                seguro: celdas[4].innerText.trim(),
                manejo: celdas[5].innerText.trim(),
                iva: celdas[6].innerText.trim(),
                capital: celdas[7].innerText.trim(),
                cuota: celdas[8].innerText.trim(),
            });
        }
    });

    // 4. Extraer datos de la tabla de pagos recibidos
    const filasPagos = document.querySelectorAll('#tablaPagosRecib tbody tr');
    const datosPagos = [];
    filasPagos.forEach((fila, index) => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length === 9 && !fila.innerText.includes('Totales') && !fila.innerText.includes('No hay registros')) {
            datosPagos.push({
                fecha: celdas[0].innerText.trim(),
                valor_cuota: celdas[1].innerText.trim(),
                capital: celdas[2].innerText.trim(),
                int_apli: celdas[3].innerText.trim(),
                int_mora: celdas[4].innerText.trim(),
                manejo: celdas[5].innerText.trim(),
                seguro: celdas[6].innerText.trim(),
                iva: celdas[7].innerText.trim(),
                saldo: celdas[8].innerText.trim(),
            });
        }
    });


    fetch('/pdf/estadoCuenta', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            datosCuotas: datosCuotas,
            datosPagos: datosPagos,
            fechaSeleccionada: fechaSeleccionada,
            valorPonerseAlDia: valorPonerseAlDia,
            monto: monto,
            nombreCentro: nombreCentro ? nombreCentro.trim() : '',
            nombreGrupo: nombreGrupo ? nombreGrupo.trim() : '',

        })
    })
        .then(response => {
            if (!response.ok) throw new Error("Error en la respuesta del servidor");
            return response.blob(); // Obtener el PDF como blob
        })
        .then(blob => {
            const pdfURL = URL.createObjectURL(blob);
            const pdfWindow = window.open("");
            pdfWindow.document.write(`
        <html>
            <head><title>Comprobante de Pago</title></head>
            <body style="margin:0">
                <embed width="100%" height="100%" src="${pdfURL}" type="application/pdf">
            </body>
        </html>
    `);
            ocultarAlertaInfo();
            mostrarAlerta('PDF generado correctamente.', 'success');
            setTimeout(() => location.reload(), 1000);
        })
        .catch(error => {
            mostrarAlerta('Error al generar el PDF.', 'error');
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