document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('buscarcliente');

    input.addEventListener('keyup', function () {
        const query = input.value.trim();

        if (query.length >= 2) {
            fetch(`/buscar-clientes?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(clientes => {
                    mostrarSugerencias(clientes);
                });
        } else {
            limpiarSugerencias();
        }
    });

    function mostrarSugerencias(clientes) {
        let lista = document.getElementById('sugerencias-clientes');
        if (!lista) {
            lista = document.createElement('ul');
            lista.id = 'sugerencias-clientes';
            lista.style.position = 'absolute';
            lista.style.background = '#fff';
            lista.style.border = '1px solid #ccc';
            lista.style.width = '400px';
            lista.style.zIndex = '9999';
            lista.style.listStyle = 'none';
            lista.style.margin = 0;
            lista.style.padding = '0';
            lista.style.maxHeight = '200px';
            lista.style.overflowY = 'auto';
            input.parentNode.appendChild(lista);
        }

        lista.innerHTML = '';

        clientes.forEach(cliente => {
            const item = document.createElement('li');
            item.textContent = cliente.nombre + ' ' + cliente.apellido;
            item.style.padding = '8px';
            item.style.cursor = 'pointer';

            item.addEventListener('click', () => {
                input.value = cliente.nombre + ' ' + cliente.apellido;
                document.getElementById('codigo_cliente').value = cliente.id || '';
                document.getElementById('nombre_cliente').value = cliente.nombre + ' ' + cliente.apellido;
                document.getElementById('telefono').value = cliente.telefono_casa || '';

                limpiarSugerencias();

                if (cliente.id) {
                    consultarDatosCliente(cliente.id);
                }
            });

            lista.appendChild(item);
        });

        if (clientes.length === 0) {
            const item = document.createElement('li');
            item.textContent = 'No se encontraron clientes';
            item.style.padding = '8px';
            lista.appendChild(item);
        }
    }

    function limpiarSugerencias() {
        const lista = document.getElementById('sugerencias-clientes');
        if (lista) lista.remove();
    }

    document.addEventListener('click', function (event) {
        if (!input.contains(event.target)) {
            limpiarSugerencias();
        }
    });

    function formatFecha(fecha) {
        const partes = fecha.split('-');
        const anio = parseInt(partes[0], 10);
        const mes = parseInt(partes[1], 10) - 1;
        const dia = parseInt(partes[2], 10);

        const date = new Date(anio, mes, dia);
        const dd = String(date.getDate()).padStart(2, '0');
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const yyyy = date.getFullYear();

        return `${dd}-${mm}-${yyyy}`;
    }

    function consultarDatosCliente(clienteId) {
        fetch(`/datos-cliente?id=${encodeURIComponent(clienteId)}`)
            .then(res => res.json())
            .then(prestamos => {

                prestamos.sort((a, b) => b.id - a.id);

                const select = document.getElementById('fechasprestamos');
                select.innerHTML = '<option value="">Seleccione un préstamo</option>';

                prestamos.forEach(prestamo => {
                    const option = document.createElement('option');
                    option.value = prestamo.id;
                    option.textContent = `Apertura: ${formatFecha(prestamo.fecha_apertura)} - Venc: ${formatFecha(prestamo.fecha_vencimiento)}`;
                    select.appendChild(option);
                });

                select.addEventListener('change', function () {
                    const idSeleccionado = parseInt(this.value);
                    const prestamoSeleccionado = prestamos.find(p => p.id === idSeleccionado);

                    if (prestamoSeleccionado) {

                        const idCliente = prestamoSeleccionado.id_cliente;
                        const fechaInicio = prestamoSeleccionado.fecha_apertura;
                        const fechaFin = prestamoSeleccionado.fecha_vencimiento;

                        const montoInput = document.getElementById('montoprestamo');
                        if (montoInput) montoInput.value = prestamoSeleccionado.monto || '';

                        fetch(`/consulta-avanzada?id_cliente=${idCliente}&inicio=${fechaInicio}&fin=${fechaFin}`)
                            .then(res => res.json())
                            .then(data => {

                                if (data.saldoprestamo.length > 0) {
                                    document.getElementById('supervisor').value = data.saldoprestamo[0].nombre_supervisor || '';
                                    document.getElementById('sucursal').value = data.saldoprestamo[0].nombre_sucursal || '';
                                    document.getElementById('garantia').value = data.saldoprestamo[0].nombre_garantia || '';
                                    document.getElementById('grupo').value = data.saldoprestamo[0].nombre_grupo || '';
                                    document.getElementById('centro').value = data.saldoprestamo[0].nombre_centro || '';
                                    const interesFormateado = parseFloat(data.saldoprestamo[0].INTERES || 0).toFixed(2);
                                    document.getElementById('interes').value = interesFormateado;

                                    const fechaFormateada = data.saldoprestamo[0].ULTIMA_FECHA_PAGADA
                                        ? formatFecha(data.saldoprestamo[0].ULTIMA_FECHA_PAGADA)
                                        : '';
                                    document.getElementById('fechaultmovimiento').value = fechaFormateada;
                                    document.getElementById('plazo').value = data.saldoprestamo[0].PLAZO || '';
                                    document.getElementById('saldo').value = data.saldoprestamo[0].SALDO || '';
                                    document.getElementById('seguro').value = data.saldoprestamo[0].MANEJO || '';
                                    document.getElementById('mseguro').value = data.saldoprestamo[0].segu_d || '';
                                    document.getElementById('cuota').value = data.saldoprestamo[0].CUOTA || '';
                                } else {
                                    document.getElementById('supervisor').value = '';
                                    document.getElementById('sucursal').value = '';
                                    document.getElementById('garantia').value = '';
                                    document.getElementById('grupo').value = '';
                                    document.getElementById('centro').value = '';
                                    document.getElementById('fechaultmovimiento').value = '';
                                }

                                // ✅ ALERTA si no hay movimientos
                                if (data.movimientos.length === 0) {
                                    mostrarAlerta('No existen pagos para este cliente.','error');
                                    document.getElementById('fechaprimpago').value = '';
                                    const tbody = document.querySelector('#tablareversionCuota tbody');
                                    tbody.innerHTML = '';
                                    return; // Detiene la ejecución
                                }

                                // ✅ Mostrar fecha primer pago
                                const fechaPrimerPago = formatFecha(data.movimientos[0].fecha);
                                document.getElementById('fechaprimpago').value = fechaPrimerPago;

                                // ✅ Llenar tabla
                                const tbody = document.querySelector('#tablareversionCuota tbody');
                                tbody.innerHTML = '';

                                data.movimientos.forEach(mov => {
                                    const fila = document.createElement('tr');
                                    fila.innerHTML = `
                                    <td style="text-align:center">${mov.fecha ? formatFecha(mov.fecha) : ''}</td>
                                    <td style="text-align:center">${mov.fecha_conta ? formatFecha(mov.fecha_conta) : ''}</td>
                                    <td style="text-align:center">${mov.comprobante || ''}</td>
                                    <td style="text-align:center">${formatearNumero(mov.valor_cuota)}</td>
                                    <td style="text-align:center">${formatearNumero(mov.capital)}</td>
                                    <td style="text-align:center">${formatearNumero(mov.saldo)}</td>
                                    <td style="text-align:center">${formatearNumero(mov.int_apli)}</td>
                                    <td style="text-align:center">${formatearNumero(mov.seguro)}</td>
                                    <td style="text-align:center">${formatearNumero(mov.manejo)}</td>
                                    <td style="text-align:center">${formatearNumero(mov.iva)}</td>
                                `;
                                    tbody.appendChild(fila);
                                });

                                function formatearNumero(valor) {
                                    return valor != null ? parseFloat(valor).toFixed(2) : '0.00';
                                }
                            })
                            .catch(error => {
                                console.error('Error en consulta avanzada:', error);
                            });
                    }
                });
            })
            .catch(error => console.error('Error al consultar datos cliente:', error));
    }
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