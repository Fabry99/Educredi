import './bootstrap';
import $, { data, error, event } from 'jquery'; // Importar jQuery
import 'datatables.net-dt';
import 'datatables.net-colreorder';
import 'datatables.net-keytable-dt';
import 'datatables.net-scroller-dt';
import { Button } from 'bootstrap';

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

$(document).ready(function () {
    const table = $('#tablaUsuarios').DataTable();

    $('#tablaUsuarios tbody').on('click', 'tr', function () {
        const rowData = table.row(this).data(); // Obtiene los datos de la fila seleccionada
        const id_user = rowData[0];
        const nombreUser = rowData[1];
        $('#user_id').val(id_user);
        $('#modaleditaruser h2').text('Editar Usuario - ' + nombreUser);

        $.ajax({
            url: '/admin/usurios/obtener-user/' + id_user,
            type: 'GET',
            success: function (response) {
                $('#modaleditaruser #nombreupdate').val(response.name);
                $('#modaleditaruser #apellidoupdate').val(response.last_name);
                $('#modaleditaruser #correoupdate').val(response.email);
                $('#modaleditaruser #rolupdate').val(response.rol);
                $('#modaleditaruser #actividadupdate').val(response.estado);
                $('#modaleditaruser #nacimientoupdate').val(response.fecha_nacimiento);
                $('#modaleditaruser #passwordupdate').val(response.password);

            },
            error: function () {
                mostrarAlerta('Error al obtener los datos del cliente.', 'error');
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
            modal.classList.remove('flex-center');
        }
    }
    window.cerrarModal = cerrarModal; // Para usarla desde otros scripts si hace falta


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
            const modalToClose = this.closest(".modal");
            cerrarModal(modalToClose);
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
            document.querySelectorAll(".modal").forEach((modal) => cerrarModal(modal));

        }
    });
});



// Modal para Elegir el tipo de prestamo
document.addEventListener('DOMContentLoaded', function () {

    // Codigo para calcular las fechas de los prestamos
    const modal = document.getElementById('modalEleccionTipoPrestamo');
    const modalGrupal = document.getElementById('modalprestamogrupal');
    const modalIndividual = document.getElementById('modalPrestamoIndividual');

    if (modal || modalGrupal && modalIndividual) {


        let clienteId = null;
        let clienteNombre = null;
        let datacliente = [];
        let centroSeleccionado = null;
        let seguro = 0;
        let capital = 0;
        let RotacionCliente = 0;


        const selectCentro = document.getElementById('centro');
        const selectGrupo = document.getElementById('grupo');
        const cantPago = document.getElementById('cantPagos');
        const formaPago = document.getElementById('formaPago');
        const diasPorTipoPago = {
            'Diario': 1,
            'Semanal': 7,
            'Catorcenal': 14,
            'Mensual': 30,
            'Bimensual': 60,
            'Trimestral': 90,
            'Semestral': 180,
            'Anual': 365
        };
        const inputFecha = document.getElementById('fechaapertura');
        const fechaPrimerPago = document.getElementById('fechaprimerpagointereses');
        const fechaDebeSer = document.getElementById('fechaprimerpagodebeser');
        const inputInteres = document.getElementById('tasainteres');  // Campo de tasa de interés
        const selectLinea = document.getElementById('linea');   // Select que cambia la tasa
        const fechavencimiento = document.getElementById('fechavencimiento');


        selectLinea.addEventListener('change', function () {
            const interes = this.options[this.selectedIndex].dataset.interes;
            if (interes) {
                inputInteres.value = parseFloat(interes).toFixed(2);  // Asignar el valor al input de interés
                actualizarTasaEnTabla(inputInteres.value);  // Actualizar la tasa en la tabla
            } else {
                inputInteres.value = '';  // Limpiar el valor si no hay tasa
            }
        });
        inputInteres.addEventListener('input', function () {
            const nuevaTasa = parseFloat(this.value);
            if (!isNaN(nuevaTasa)) {
                actualizarTasaEnTabla(nuevaTasa.toFixed(2));  // Actualizar en la tabla en tiempo real
            }
            actualizarTodasLasCuotas();

        });


        // Función para actualizar la tasa de interés en la tabla
        function actualizarTasaEnTabla(tasa) {
            const rows = contenedorMiembrosGrupo.querySelectorAll('table tbody tr');  // Obtener todas las filas de la tabla
            rows.forEach(row => {
                const tasaInput = row.querySelector('input[name="tasa"]');  // Seleccionar el campo tasa en cada fila
                if (tasaInput) {
                    tasaInput.value = tasa;  // Asignar la tasa seleccionada al campo tasa
                }
            });
        }

        function actualizarFechaDebeSer() {
            const textoSeleccionado = formaPago.options[formaPago.selectedIndex]?.text?.trim();
            const diasporpago = diasPorTipoPago[textoSeleccionado];

            if (inputFecha.value && diasporpago) {
                const fechaInicio = new Date(inputFecha.value);
                fechaInicio.setDate(fechaInicio.getDate() + diasporpago);

                const fechaFormateada = fechaInicio.toISOString().split('T')[0]; // yyyy-mm-dd
                fechaDebeSer.value = fechaFormateada;
            } else {
                fechaDebeSer.value = '';
            }

            // ✅ Asignar la misma fecha a todos los inputs .fechaMiembros
            const inputsFechaMiembros = document.querySelectorAll('.fechaMiembros');
            inputsFechaMiembros.forEach(input => {
                input.value = fechaDebeSer.value;
            });
        }


        function calcularFechaFinal() {
            const textoSeleccionado = formaPago.options[formaPago.selectedIndex]?.text?.trim();
            const diasporpago = diasPorTipoPago[textoSeleccionado];
            const cantidadPagos = parseInt(cantPago.value);
            const fechaInicio = new Date(inputFecha.value); // Convierte el string yyyy-mm-dd en Date
            fechaPrimerPago.value = inputFecha.value;



            if (!isNaN(diasporpago) && !isNaN(cantidadPagos) && inputFecha.value) {
                const totalDias = diasporpago * cantidadPagos;
                const fechaFinal = new Date(fechaInicio);
                fechaFinal.setDate(fechaInicio.getDate() + totalDias);

                const fechaFormateada = fechaFinal.toISOString().split('T')[0]; // yyyy-mm-dd
                document.getElementById('fechavencimiento').value = fechaFormateada;
            } else {
                document.getElementById('fechavencimiento').value = '';
            }
        }
        //actualizacion al instante de los campos de fecha
        inputFecha.addEventListener('change', actualizarFechaDebeSer);
        formaPago.addEventListener('change', actualizarFechaDebeSer);
        inputFecha.addEventListener('change', calcularFechaFinal);
        formaPago.addEventListener('change', calcularFechaFinal);
        cantPago.addEventListener('input', calcularFechaFinal);

        // Mostrar modal de elección
        document.querySelectorAll('.btn-prestamo').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const fila = this.closest('tr');

                clienteId = this.getAttribute('data-id');
                clienteNombre = this.getAttribute('data-name');
                const conteoRotacion = fila.querySelector('td[hidden]').textContent.trim();

                RotacionCliente = conteoRotacion;
                modal.style.display = 'block';
                modal.classList.add('flex-center');
            });
        });

        // Click en botón "préstamo grupal"
        document.querySelector('.btn-prestamogrupal').addEventListener('click', function () {
            cerrarModal(modal);
            modalGrupal.style.display = 'block';
            modalGrupal.classList.add('flex-center');

            document.getElementById('id').value = clienteId;
            document.getElementById('nombre').value = clienteNombre;
            document.getElementById('rotacioncliente').value = RotacionCliente;

            $.ajax({
                url: '/prestamos/obtener-centros-grupos-clientes/' + clienteId,
                type: 'GET',
                success: function (response) {
                    datacliente = response;

                    renderCentros(response);
                    configurarEventosSelects(); // 👈 Aquí activamos los listeners
                },
                error: function () {
                    mostrarAlerta('Error al obtener los datos del cliente.', 'error');
                }
            });
        });


        function renderCentros(data) {
            selectCentro.innerHTML = '<option value="" disabled selected>Centro:</option>';
            const centrosAgregados = new Set();

            data.forEach(item => {
                if (!centrosAgregados.has(item.centros.id)) {
                    const option = document.createElement('option');
                    option.value = item.centros.id;
                    option.textContent = item.centros.nombre;
                    selectCentro.appendChild(option);
                    centrosAgregados.add(item.centros.id);
                }
            });

            selectGrupo.innerHTML = '<option value="" disabled selected>Grupo:</option>';
        }

        // Se llama una sola vez
        function configurarEventosSelects() {
            selectCentro.removeEventListener('change', manejarCambioCentro);
            selectGrupo.removeEventListener('change', manejarCambioGrupo);

            selectCentro.addEventListener('change', manejarCambioCentro);
            selectGrupo.addEventListener('change', manejarCambioGrupo);
        }



        function manejarCambioCentro() {
            centroSeleccionado = parseInt(this.value);
            selectGrupo.innerHTML = '<option value="" disabled selected>Grupo:</option>';

            const gruposFiltrados = datacliente.filter(item => item.centros.id === centroSeleccionado);
            const gruposAgregados = new Set();

            gruposFiltrados.forEach(item => {
                if (!gruposAgregados.has(item.grupos.id)) {
                    const option = document.createElement('option');
                    option.value = item.grupos.id;
                    option.textContent = item.grupos.nombre;
                    selectGrupo.appendChild(option);
                    gruposAgregados.add(item.grupos.id);
                }
            });
        }
        function mostrarRotacionGrupal() {
            const grupoIdSeleccionado = parseInt(selectGrupo.value);

            const itemGrupo = datacliente.find(item => item.grupos.id === grupoIdSeleccionado);

            if (itemGrupo) {
                if (itemGrupo.grupos.nombre.toLowerCase() === 'individual') {
                    document.getElementById('rotaciongrupo').value = '0';
                } else if (itemGrupo.grupos.conteo_rotacion !== undefined) {
                    document.getElementById('rotaciongrupo').value = itemGrupo.grupos.conteo_rotacion;
                } else {
                    document.getElementById('rotaciongrupo').value = '';
                }
            } else {
                document.getElementById('rotaciongrupo').value = '';
            }
        }




        function calcularCuota(monto, tasa, row = null) {


            // Obtener valores de los elementos del formulario
            const formaPago = document.getElementById('formaPago');
            const cantPago = document.getElementById('cantPagos');
            const tasaIvaInput = document.getElementById('tasa_iva');


            // Valores por defecto o del formulario
            const tasa_iva = parseFloat(tasaIvaInput?.value) || 0.13;
            const cantPagos = parseInt(cantPago?.value) || 12;
            const manejo = (10 / cantPagos);

            // Determinar días entre cuotas según forma de pago
            const textoSeleccionado = formaPago.options[formaPago.selectedIndex]?.text?.trim();
            const diasporpago = diasPorTipoPago[textoSeleccionado] || 30;

            // Validaciones básicas
            if (isNaN(monto) || monto <= 0 || isNaN(tasa)) {

                return 0;
            }

            // Cálculos
            const tasaDiaria = (tasa / 360) / 100;
            const interes = monto * tasaDiaria * diasporpago;
            const porcentajemonto = monto * 0.02;
            const segurodiario = porcentajemonto / 365;
            const microseguro = (segurodiario * diasporpago) * (1 + tasa_iva);
            const iva = interes * tasa_iva;
            const tasadiariaparacuota = (tasa / 365) / 100;
            const tasaporperiodo = tasadiariaparacuota * diasporpago;
            const baseCalculo = Math.pow(1 + tasaporperiodo, cantPagos);
            const valorcuota = (monto * tasaporperiodo * baseCalculo) / (baseCalculo - 1);
            const cuotaFinal = (valorcuota + iva + manejo - microseguro);
            capital = (cuotaFinal - interes - manejo - microseguro - iva);
            seguro = (interes + capital + iva);
            // Objeto con todos los detalles
            const detalleCalculo = {
                valorcuota,
                iva,
                manejo,
                microseguro,
                cuotaFinal,
                parametros: {
                    monto,
                    tasa,
                    cantPagos,
                    diasporpago,
                    tasa_iva,
                    capital,
                    seguro,

                },
                calculosIntermedios: {
                    tasaDiaria,
                    interes,
                    porcentajemonto,
                    segurodiario,
                    tasadiariaparacuota,
                    tasaporperiodo,
                    baseCalculo,

                }
            };

            // Almacenar en la fila si se proporcionó
            if (row) {
                row.dataset.calculoDetalle = JSON.stringify(detalleCalculo);
            }



            return cuotaFinal > 0 ? cuotaFinal : 0;
        }

        // Event listener para cambios en la tabla
        contenedorMiembrosGrupo.addEventListener('input', function (e) {
            if (e.target.matches('input[name="monto"], input[name="tasa"], select[name="formaPago"], input[name="cantPagos"]')) {
                const row = e.target.closest('tr');
                const monto = parseFloat(row.querySelector('input[name="monto"]')?.value) || 0;
                const tasa = parseFloat(row.querySelector('input[name="tasa"]')?.value) || 0;

                // Usamos la función unificada calcularCuota()
                const cuotaCalculada = calcularCuota(monto, tasa, row);
                const cuotaInput = row.querySelector('input[name="cuota"]');

                if (cuotaInput) {
                    cuotaInput.value = cuotaCalculada.toFixed(2);
                }
            }
        });

        // Función para actualizar todas las cuotas
        function actualizarTodasLasCuotas() {
            const rows = contenedorMiembrosGrupo.querySelectorAll('table tbody tr');

            rows.forEach(row => {
                const monto = parseFloat(row.querySelector('input[name="monto"]')?.value) || 0;
                const tasa = parseFloat(row.querySelector('input[name="tasa"]')?.value) || 0;

                // Usamos la función unificada calcularCuota()
                const cuotaCalculada = calcularCuota(monto, tasa, row);
                const cuotaInput = row.querySelector('input[name="cuota"]');

                if (cuotaInput) {
                    cuotaInput.value = cuotaCalculada.toFixed(2);
                }
            });
        }

        //EVENTO QUE PERMITE REPARTIR EL MONTO TOTAL ENTRE LOS MIEMBROS DEL GRUPO
        document.getElementById('montootorgar').addEventListener('input', function () {
            const montoTotal = parseFloat(this.value);
            const inputsMonto = document.querySelectorAll('input[name="monto"]');
            const totalInput = document.getElementById('total');

            if (!isNaN(montoTotal)) {
                // En lugar de repartir, poner 0 en todos los inputs monto
                inputsMonto.forEach(input => {
                    input.value = '0.00';
                });
                // Puedes actualizar total también en 0
                totalInput.value = '0.00';
            } else {
                // Si el input está vacío o no es válido, limpiar todo
                inputsMonto.forEach(input => {
                    input.value = '';
                });
                totalInput.value = '';
            }

            actualizarTotal();
            actualizarTodasLasCuotas();
        });


        function actualizarTotal() {
            const inputsMonto = document.querySelectorAll('input[name="monto"]');
            const totalInput = document.getElementById('total');
            const montoOtorgarInput = document.getElementById('montootorgar');
            const montoOtorgar = parseFloat(montoOtorgarInput.value);
            let total = 0;

            inputsMonto.forEach(input => {
                const valor = parseFloat(input.value);
                if (!isNaN(valor)) {
                    total += valor;
                }
            });

            totalInput.value = total.toFixed(2);

            // Comparar usando una tolerancia de decimales
            if (!isNaN(montoOtorgar) && Math.abs(total - montoOtorgar) > 0.01) {
                totalInput.style.color = 'red';
            } else {
                totalInput.style.color = 'black'; // o tu color normal
            }
        }
        document.getElementById('cantPagos')?.addEventListener('input', () => {
            actualizarTodasLasCuotas();
        });

        // Escuchar cambios en la forma de pago
        document.getElementById('formaPago')?.addEventListener('change', () => {
            actualizarTodasLasCuotas();
        });
        function manejarCambioGrupo() {
            const grupoSeleccionado = this.value;
            mostrarRotacionGrupal();
            const ruta = `/prestamos/obtenergrupos-clientes/${centroSeleccionado}/${grupoSeleccionado}`;



            $.ajax({
                url: ruta,
                type: 'GET',
                success: function (miembros) {
                    const contenedor = document.getElementById('contenedorMiembrosGrupo');
                    contenedor.innerHTML = '';

                    if (miembros.length === 0) {
                        contenedor.innerHTML = '<p>No hay miembros en este grupo.</p>';
                        return;
                    }

                    const tabla = document.createElement('table');
                    tabla.style.width = '100%';
                    tabla.style.borderCollapse = 'collapse';
                    tabla.style.marginTop = '10px';

                    tabla.innerHTML = `
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th style="padding: 8px; border: 1px solid #ccc;">ID</th>
                            <th style="padding: 8px; border: 1px solid #ccc;">Nombre</th>
                            <th style="padding: 8px; border: 1px solid #ccc;">Monto</th>
                            <th style="padding: 8px; border: 1px solid #ccc;">Tasa</th>
                            <th style="padding: 8px; border: 1px solid #ccc;">Cuota</th>
                            <th style="padding: 8px; border: 1px solid #ccc;">Primer Fecha Pago</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                `;

                    const tbody = tabla.querySelector('tbody');

                    miembros.forEach(miembro => {
                        const fila = document.createElement('tr');
                        fila.innerHTML = `
    <td style="padding: 8px; border: 1px solid #ccc;">${miembro.id}</td>
    <td style="padding: 8px; border: 1px solid #ccc;">${miembro.nombre} ${miembro.apellido}</td>
    
    <td style="padding: 8px; border: 1px solid #ccc;">
        <input type="number" name="monto" value="${(typeof miembro.monto === 'number' && !isNaN(miembro.monto)) ? miembro.monto : ''}"
        " style="width: 100%; border: none; outline: none; background: transparent;">
    </td>
    
    <td style="padding: 8px; border: 1px solid #ccc;">
        <input type="number" step="0.01" name="tasa" value="${(typeof miembro.tasa === 'number' && !isNaN(miembro.tasa)) ? miembro.tasa : ''}" 

        " style="width: 100%; border: none; outline: none; background: transparent;">
    </td>
    
    <td style="padding: 8px; border: 1px solid #ccc;">
        <input type="number" step="0.01" name="cuota" value="${(typeof miembro.cuota === 'number' && !isNaN(miembro.cuota)) ? miembro.cuota : ''}" 
        " style="width: 100%; border: none; outline: none; background: transparent;">
    </td>
    <td style="padding: 8px; border: 1px solid #ccc;">
        <input type="date" name="fecha" class="fechaMiembros" style="width: 100%; border: none; outline: none; background: transparent;">
    </td>
    `;
                        tbody.appendChild(fila);
                    });

                    contenedor.appendChild(tabla);
                    const inputsMonto = tabla.querySelectorAll('input[name="monto"]');
                    inputsMonto.forEach(input => {
                        input.addEventListener('input', actualizarTotal);
                    });
                    actualizarTasaEnTabla(inputInteres.value);
                    actualizarTodasLasCuotas();



                },
                error: function () {
                    mostrarAlerta('Error al obtener los miembros del grupo.', 'error');
                }
            });
            contenedorMiembrosGrupo.addEventListener('input', function (e) {
                if (e.target.matches('input[name="monto"], input[name="tasa"], select[name="formaPago"], input[name="cantPagos"]')) {
                    const row = e.target.closest('tr');

                    const monto = parseFloat(row.querySelector('input[name="monto"]')?.value) || 0;
                    const tasa = parseFloat(row.querySelector('input[name="tasa"]')?.value) || 0;
                    const cuotaInput = row.querySelector('input[name="cuota"]');

                    if (monto > 0 && tasa > 0 && cuotaInput) {
                        const cuotaCalculada = calcularCuota(monto, tasa, row); // pasa la fila para guardar detalle
                        cuotaInput.value = cuotaCalculada.toFixed(2);
                    } else {
                        cuotaInput.value = '0.00';
                    }
                }
            });

        }
        function obtenerGarantiaSeleccionada() {
            const radioSeleccionado = document.querySelector('input[name="garantia"]:checked');
            return radioSeleccionado ? radioSeleccionado.value : null;
        }
        function obtenerTodosLosDatos() {
            // Obtener valores (no los elementos)
            const fechaVencimiento = document.getElementById('fechavencimiento')?.value || '';
            const fechaapertura = document.getElementById('fechaapertura')?.value || '';
            const fechadebeser = document.getElementById('fechaprimerpagodebeser')?.value || '';
            const grupoId = document.getElementById('grupo')?.value || '';
            const centroId = document.getElementById('centro')?.value || '';
            const formapago = document.getElementById('formaPago')?.value || '';
            const id_colector = document.getElementById('colector')?.value || '';
            const id_aprobador = document.getElementById('aprobadopor')?.value || '';
            const sucursal = document.getElementById('sucursal')?.value || '';
            const supervisor = document.getElementById('supervisor')?.value || '';
            const linea = document.getElementById('linea')?.value || '';
            const garantiaId = obtenerGarantiaSeleccionada();
            const asesor = document.getElementById('asesor')?.value || '';
            const asesorNombre = document.getElementById('asesor')?.selectedOptions[0]?.text || '';
            const sucursalNombre = document.getElementById('sucursal')?.selectedOptions[0]?.text || '';
            const supervisorNombre = document.getElementById('supervisor')?.selectedOptions[0]?.text || '';


            // Validar que se seleccionó una garantía
            if (!garantiaId) {
                mostrarAlerta('Por favor seleccione un tipo de garantía', 'error');
                return null;
            }


            return Array.from(document.querySelectorAll('#contenedorMiembrosGrupo table tbody tr')).map(row => {
                const getValue = (selector) => parseFloat(row.querySelector(selector)?.value) || 0;

                try {
                    const detalleCalculo = JSON.parse(row.dataset.calculoDetalle || '{}');

                    return {
                        id: row.querySelector('td:first-child').textContent,
                        nombre: row.querySelector('td:nth-child(2)').textContent,
                        monto: getValue('input[name="monto"]'),
                        tasa: getValue('input[name="tasa"]'),
                        cuota: getValue('input[name="cuota"]'),
                        fechaMiembro: row.querySelector('input.fechaMiembros')?.value || '',

                        // Datos globales
                        fechaVencimiento: detalleCalculo.fechavencimiento || fechaVencimiento,
                        fechaapertura: detalleCalculo.fechaapertura || fechaapertura,
                        fechadebeser: detalleCalculo.fechadebeser || fechadebeser,
                        sucursal: detalleCalculo.sucursal || sucursal,
                        supervisor: detalleCalculo.supervisor || supervisor,
                        id_colector: detalleCalculo.id_colector || id_colector,
                        id_aprobador: detalleCalculo.id_aprobador || id_aprobador,
                        linea: detalleCalculo.linea || linea,
                        garantia_id: garantiaId,
                        grupoId: grupoId,
                        centroId: centroId,
                        formapago: detalleCalculo.formapago || formapago,
                        asesor: asesor,
                        nombre_asesor: asesorNombre,
                        nombre_supervisor: supervisorNombre,
                        nombre_sucursal: sucursalNombre,



                        // Detalle completo
                        detalleCalculo: {
                            ...detalleCalculo,
                            fechavencimiento: detalleCalculo.fechavencimiento || fechaVencimiento,
                            fechaapertura: detalleCalculo.fechaapertura || fechaapertura,
                            fechaDebeSer: detalleCalculo.fechadebeser || fechadebeser,
                            sucursal: detalleCalculo.sucursal || sucursal,
                            supervisor: detalleCalculo.supervisor || supervisor,
                            id_colector: detalleCalculo.id_colector || id_colector,
                            id_aprobador: detalleCalculo.id_aprobador || id_aprobador,
                            linea: detalleCalculo.linea || linea,
                            garantia_id: garantiaId,
                            grupoId: grupoId,
                            centroId: centroId,
                            formapago: detalleCalculo.formapago || formapago,
                            asesor: asesor,



                        }
                    };
                } catch (e) {
                    return null;
                }
            }).filter(Boolean);
        }
        document.getElementById('btnAceptar').addEventListener('click', function (event) {
            event.preventDefault(); // Evita el envío del formulario si estás usando un submit

            const btn = this; // botón
            const montoOtorgar = parseFloat(document.getElementById('montootorgar').value);
            const total = parseFloat(document.getElementById('total').value);
            const sucursal = document.getElementById("sucursal").value;
            const supervisor = document.getElementById("supervisor").value;
            const colector = document.getElementById("colector").value;
            const aprobado = document.getElementById("aprobadopor").value;
            const garantiaSeleccionada = document.querySelector('input[name="garantia"]:checked');
            const asesor = document.getElementById('asesor');

            const prestamos = obtenerTodosLosDatos();
            // Verificamos si los valores son números válidos y mayores que 0
            if (isNaN(montoOtorgar) || isNaN(total) || montoOtorgar <= 0 || total <= 0) {
                // Si alguno de los valores no es un número válido o es igual o menor a 0, mostramos la alerta de error
                mostrarAlerta("Por favor, ingrese valores válidos y mayores que 0 para los montos.", "error");
            } else if (montoOtorgar !== total) {
                // Si los montos no coinciden, mostramos la alerta de error
                mostrarAlerta("¡Error! Los montos no coinciden.", "error");
            } else if (selectLinea === "" || sucursal === "" || supervisor === ""
                || selectCentro === "" || selectGrupo === "" || colector === "" || aprobado === ""
                || formaPago === "" || inputInteres === "" || cantPago === "" || inputFecha.value === ""
                || fechaPrimerPago.value === "" || fechaDebeSer.value === ""
                || fechavencimiento.value === "" || !garantiaSeleccionada || asesor === ""
            ) {
                mostrarAlerta("Por Favor Ingrese los Datos Correctamente.", "error");
            } else {
                // Si todo es correcto, mostramos una alerta de éxito


                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    return;
                }

                btn.disabled = true; // deshabilita botón para evitar dobles clics
                mostrarAlerta("Procesando préstamo...", "info");

                fetch('/guardarprestamogrupal', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ prestamos: prestamos })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            mostrarAlerta("El Prestamo se Realizo Correctamente.", "success");

                            // Luego recargas la página después de un tiempo
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                            // Descargar el PDF
                            if (data.pdf) {
                                const pdfWindow = window.open(""); // Abrir nueva ventana
                                pdfWindow.document.write(`
                                <html>
                                    <head>
                                        <title>Comprobante de Préstamo Grupal</title>
                                    </head>
                                    <body style="margin:0">
                                        <embed width="100%" height="100%" src="data:application/pdf;base64,${data.pdf}" type="application/pdf">
                                    </body>
                                </html>
                            `);
                            }


                        } else {
                            mostrarAlerta('Error al guardar el préstamo.', "error");
                            btn.disabled = false; // vuelve a habilitar botón
                            btnAceptar.style.display = 'block';

                        }
                    })
                    .catch(error => {
                        mostrarAlerta('Error al enviar:', "error");
                        btn.disabled = false; // vuelve a habilitar botón
                        btnAceptar.style.display = 'block';
                    });
            }
        });

        //Codigo para desembolso de prestamo Individual

        const asesorSelect = document.getElementById('asesorind');
        const sucursalSelect = document.getElementById('sucursalind');
        const supervisorSelect = document.getElementById('supervisorind');

        // Función para obtener el texto seleccionado, ignorando "Seleccionar:"
        function obtenerTextoSeleccionado(select) {
            if (!select) return '';
            if (select.value === "" || select.selectedOptions.length === 0) return '';
            const texto = select.selectedOptions[0].text.trim();
            return texto === "Seleccionar:" ? '' : texto;
        }
        const inputInteres_ind = document.getElementById('tasainteresind');  // Campo de tasa de interés
        const selectLinea_ind = document.getElementById('lineaind');
        const select_sucursal = document.getElementById('sucursalind');
        const select_supervisor = document.getElementById('supervisorind');
        const select_asesor = document.getElementById('asesorind');
        const input_montoOtorgar = document.getElementById('montootorgarind');
        const inputPlazo_ind = document.getElementById('plazoind');
        const select_tipoPago = document.getElementById('tipo_pago');
        const frecuenciamesesind = document.getElementById('frecuenciamesesind');
        const frecuenciadiasind = document.getElementById('frecuenciadiasind');
        const fechaaperturaind = document.getElementById('fechaaperturaind');
        const fechaprimerpagodebeserind = document.getElementById('fechaprimerpagodebeserind');
        const fechavencimientoind = document.getElementById('fechavencimientoind');
        const input_cuota = document.getElementById('cuotaind');
        const input_desembolso = document.getElementById('desembolsoind');
        const select_colector = document.getElementById('colectorind');
        const select_aprobadopor = document.getElementById('aprobadoporind');
        const selectBanco = document.getElementById('bancoind');
        const selectformapago = document.getElementById('formapagoind');
        const btn_prestamoindividual = document.getElementById('btnAceptarPrestamo');




        const diasPorTipoPagoInd = {
            'diario': 1,
            'semanal': 7,
            'catorcenal': 14,
            'mensual': 30,
            'bimensual': 60,
            'trimestral': 90,
            'semestral': 180,
            'anual': 365
        };

        selectLinea_ind.addEventListener('change', function () {
            const interes_ind = this.options[this.selectedIndex].dataset.interes;
            if (interes_ind) {
                inputInteres_ind.value = parseFloat(interes_ind).toFixed(2);  // Asignar el valor al input de interés
                actualizarTasaEnTabla(inputInteres_ind.value);  // Actualizar la tasa en la tabla
            } else {
                inputInteres_ind.value = '';  // Limpiar el valor si no hay tasa
            }
        });

        document.querySelector('.btn-prestamoindividual').addEventListener('click', function (event) {
            event.preventDefault();
            $('#modalprestamoIndividual').fadeIn();
            document.getElementById('id_ind').value = clienteId;
            document.getElementById('nombre_ind').value = clienteNombre;


            const form = document.getElementById('formPrestamoIndividual');

            btn_prestamoindividual.addEventListener('click', function (event) {
                event.preventDefault();

                btn_prestamoindividual.disabled = true;

                mostrarAlerta("Procesando préstamo...", "info");


                const garantiaSeleccionada = document.querySelector('input[name="garantia_ind"]:checked');
                const garantia = garantiaSeleccionada ? garantiaSeleccionada.value : null;
                const textoTipoPagoIndi = select_tipoPago.options[select_tipoPago.selectedIndex]?.text?.trim().toLowerCase();
                const cantDiasSelectind = diasPorTipoPagoInd[textoTipoPagoIndi];

                const asesorNombre = obtenerTextoSeleccionado(asesorSelect);
                const sucursalNombre = obtenerTextoSeleccionado(sucursalSelect);
                const supervisorNombre = obtenerTextoSeleccionado(supervisorSelect);

                const datosPrestamo = {
                    id_cliente: clienteId,
                    nombre: clienteNombre,
                    montoOtorgar: input_montoOtorgar.value,
                    interes: inputInteres_ind.value,
                    linea: selectLinea_ind.value,
                    sucursal: select_sucursal.value,
                    supervisor: select_supervisor.value,
                    id_asesor: select_asesor.value,
                    plazo: inputPlazo_ind.value,
                    tipoPago: select_tipoPago.value,
                    frecuenciaMeses: frecuenciamesesind.value,
                    frecuenciaDias: frecuenciadiasind.value,
                    fechaApertura: fechaaperturaind.value,
                    fechaPrimerPago: fechaprimerpagodebeserind.value,
                    fechaVencimiento: fechavencimientoind.value,
                    cuota: input_cuota.value,
                    desembolso: input_desembolso.value,
                    colector: select_colector.value,
                    aprobadoPor: select_aprobadopor.value,
                    banco: selectBanco.value,
                    formaPago: selectformapago.value,
                    garantia: garantia,
                    manejo: manejo,
                    micro_seguro: micro_seguro,
                    iva: iva,
                    cantDiasSelect: cantDiasSelectind,
                    textoTipoPagoIndi: textoTipoPagoIndi,
                    nombre_asesor: asesorNombre,
                    nombre_sucursal: sucursalNombre,
                    nombre_supervisor: supervisorNombre,

                };
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    return;
                }

                fetch('/guardarprestamoindividual', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(datosPrestamo)

                }).then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            mostrarAlerta("Prestamo Realizado Correctamente", "success");
                            setTimeout(() => {
                                location.reload();
                            }, 1200);
                            // Descargar el PDF
                            if (data.pdf) {
                                const pdfWindow = window.open("");
                                if (pdfWindow) {
                                    pdfWindow.document.write(`
                                        <!DOCTYPE html>
                                        <html>
                                            <head>
                                                <title>Comprobante de Préstamo Individual</title>
                                                <style>
                                                    html, body {
                                                        margin: 0;
                                                        padding: 0;
                                                        height: 100%;
                                                    }
                                                    embed {
                                                        width: 100%;
                                                        height: 100%;
                                                    }
                                                </style>
                                            </head>
                                            <body>
                                                <embed src="data:application/pdf;base64,${data.pdf}" type="application/pdf" />
                                            </body>
                                        </html>
                                    `);
                                } else {
                                    mostrarAlerta("No se pudo abrir la ventana del PDF. Verifica que no esté bloqueada por el navegador.", "warning");
                                }
                            }
                        } else {
                            mostrarAlerta("Error al Realizar el Prestamo", "error");
                            btn_prestamoindividual.disabled = false; // Rehabilitar botón en caso de error
                        }

                    }).catch(error => {
                        mostrarAlerta("Error al guardar el préstamo:", "error");
                        btn_prestamoindividual.disabled = false; // Rehabilitar botón en caso de error

                    });


            });


        });

        function actualizarFechaPrimerPago() {
            const textoTipoPago = select_tipoPago.options[select_tipoPago.selectedIndex]?.text?.trim().toLowerCase();
            const frecuencia = parseInt(frecuenciamesesind.value);
            const fechaInicio = new Date(fechaaperturaind.value);



            if (['mensual', 'bimensual', 'trimestral', 'semestral', 'anual'].includes(textoTipoPago)) {
                const fechaPrimerPago = new Date(fechaInicio);
                fechaPrimerPago.setMonth(fechaPrimerPago.getMonth() + frecuencia);
                if (!fechaaperturaind.value || isNaN(frecuencia)) {
                    fechaprimerpagodebeserind.value = '';
                    return;
                }

                if (fechaPrimerPago.getDate() !== fechaInicio.getDate()) {
                    fechaPrimerPago.setDate(0);
                }

                const fechaFormateada = fechaPrimerPago.toISOString().split('T')[0];
                fechaprimerpagodebeserind.value = fechaFormateada;
            } else if (['diario', 'semanal', 'catorcenal'].includes(textoTipoPago)) {
                const diasFrecuencia = parseInt(frecuenciadiasind.value);

                if (fechaaperturaind.value && !isNaN(diasFrecuencia)) {
                    const fechaInicioDias = new Date(fechaaperturaind.value);
                    fechaInicioDias.setDate(fechaInicioDias.getDate() + diasFrecuencia);

                    const fechaFormateadaDias = fechaInicioDias.toISOString().split('T')[0];
                    fechaprimerpagodebeserind.value = fechaFormateadaDias;
                } else {
                    fechaprimerpagodebeserind.value = '';
                }
            }
        }


        function actualizarFechaFinal() {
            const textoTipoPago = select_tipoPago.options[select_tipoPago.selectedIndex]?.text?.trim().toLowerCase();
            const plazo = parseInt(inputPlazo_ind.value);
            const frecuenciaMeses = parseInt(frecuenciamesesind.value);

            if (!fechaaperturaind.value) {
                fechavencimientoind.value = '';
                return;
            }

            const fechaInicio = new Date(fechaaperturaind.value);
            if (isNaN(fechaInicio.getTime())) {
                fechavencimientoind.value = '';
                return;
            }

            // Casos de pagos por mes: mensual, bimensual, etc.
            if (['mensual', 'bimensual', 'trimestral', 'semestral', 'anual'].includes(textoTipoPago)) {
                if (isNaN(frecuenciaMeses) || isNaN(plazo)) {
                    fechavencimientoind.value = '';
                    return;
                }

                const mesesTotales = frecuenciaMeses * plazo;
                const fechaFinal = new Date(fechaInicio);
                fechaFinal.setMonth(fechaFinal.getMonth() + mesesTotales);

                if (fechaFinal.getDate() !== fechaInicio.getDate()) {
                    fechaFinal.setDate(0);
                }

                fechavencimientoind.value = fechaFinal.toISOString().split('T')[0];
            }
            // Casos de pagos por días
            else if (['diario', 'semanal', 'catorcenal'].includes(textoTipoPago)) {
                if (isNaN(plazo)) {
                    fechavencimientoind.value = '';
                    return;
                }

                const fechaFinal = new Date(fechaInicio);
                fechaFinal.setDate(fechaFinal.getDate() + (frecuenciadiasind.value * plazo));


                fechavencimientoind.value = fechaFinal.toISOString().split('T')[0];
            } else {
                fechavencimientoind.value = '';
            }
        }
        let manejo = 0;
        let intereses = 0;
        let micro_seguro = 0;
        let iva = 0;
        let tasa_iva = 0.13;
        let Cuota = 0;
        let Cuota_Final = 0;

        function calcularCuotas() {
            const textoTipoPago = select_tipoPago.options[select_tipoPago.selectedIndex]?.text?.trim().toLowerCase();
            const cantDiasSelect = diasPorTipoPago[textoTipoPago];
            const plazo = parseInt(inputPlazo_ind.value);



            if (['mensual', 'bimensual', 'trimestral', 'semestral', 'anual'].includes(textoTipoPago)) {
                if (
                    isNaN(parseFloat(input_montoOtorgar.value)) ||
                    isNaN(parseFloat(inputInteres_ind.value)) ||
                    isNaN(parseInt(inputPlazo_ind.value)) ||
                    isNaN(parseInt(frecuenciamesesind.value))
                ) {
                    input_desembolso.value = "0.00";
                    input_cuota.value = "0.00";
                    return;
                }

                manejo = (10 / plazo);
                const tasa_interes_mensuales = (parseFloat(inputInteres_ind.value) / 100) / 12;
                intereses = input_montoOtorgar.value * tasa_interes_mensuales;
                micro_seguro = (input_montoOtorgar.value * 1.252) / 100;
                iva = intereses * tasa_iva;
                Cuota = (input_montoOtorgar.value * tasa_interes_mensuales * Math.pow(1 + tasa_interes_mensuales, plazo)) / (Math.pow(1 + tasa_interes_mensuales, plazo) - 1);
                Cuota_Final = (Cuota + iva + manejo + micro_seguro) * frecuenciamesesind.value;


                input_cuota.value = Cuota_Final.toFixed(2);
                input_desembolso.value = input_montoOtorgar.value;

            } else if (['diario', 'semanal', 'catorcenal'].includes(textoTipoPago)) {
                if (
                    isNaN(parseFloat(input_montoOtorgar.value)) ||
                    isNaN(parseFloat(inputInteres_ind.value)) ||
                    isNaN(parseInt(inputPlazo_ind.value)) ||
                    isNaN(parseInt(frecuenciadiasind.value))
                ) {
                    input_desembolso.value = "0.00";
                    input_cuota.value = "0.00";
                    return;
                }
                manejo = (10 / plazo);
                const tasa_interes_diaria = (parseFloat(inputInteres_ind.value) / 100) / 360;
                intereses = input_montoOtorgar.value * tasa_interes_diaria * frecuenciadiasind.value;

                micro_seguro = input_montoOtorgar.value * 0.00315;

                iva = intereses * tasa_iva;

                const tasa_diaria_cuota = (parseFloat(inputInteres_ind.value) / 100) / 365;
                const tasa_calculo_cuota = tasa_diaria_cuota * frecuenciadiasind.value;
                Cuota = (input_montoOtorgar.value * tasa_calculo_cuota * Math.pow(1 + tasa_calculo_cuota, plazo)) / (Math.pow(1 + tasa_calculo_cuota, plazo) - 1);
                Cuota_Final = Cuota + iva + manejo + micro_seguro;


                input_cuota.value = Cuota_Final.toFixed(2);
                input_desembolso.value = input_montoOtorgar.value;



            }
        }

        input_montoOtorgar.addEventListener('input', calcularCuotas);
        inputInteres_ind.addEventListener('input', calcularCuotas);
        fechaaperturaind.addEventListener('change', actualizarFechaPrimerPago);
        fechaaperturaind.addEventListener('change', actualizarFechaFinal);
        inputPlazo_ind.addEventListener('input', () => {
            actualizarFechaPrimerPago();
            calcularCuotas();
        });
        inputPlazo_ind.addEventListener('input', () => {
            actualizarFechaFinal();
            calcularCuota();
        });
        selectformapago.addEventListener('change', () => {
            calcularCuota();
            actualizarFechaFinal();
        });
        selectformapago.addEventListener('change', () => {
            actualizarFechaPrimerPago();
            calcularCuotas();
        });
        frecuenciamesesind.addEventListener('input', () => {
            actualizarFechaPrimerPago();
            calcularCuotas();
        });
        frecuenciamesesind.addEventListener('change', () => {
            calcularCuota();
            actualizarFechaFinal();
        });
        frecuenciadiasind.addEventListener('change', () => {
            actualizarFechaFinal();
            calcularCuota();
        });
        frecuenciadiasind.addEventListener('change', () => {
            actualizarFechaPrimerPago();
            calcularCuotas();
        });



        // Limpiar y cerrar modal prestamo grupal
        function limpiarModalPrestamoGrupal() {
            selectCentro.innerHTML = '<option value="" disabled selected>Centro:</option>';
            selectGrupo.innerHTML = '<option value="" disabled selected>Grupo:</option>';
            document.getElementById('contenedorMiembrosGrupo').innerHTML = '';
            document.getElementById('id').value = '';
            document.getElementById('nombre').value = '';
            document.getElementById('aprobadopor').value = '';
            document.getElementById('colector').value = '';
            document.getElementById('sucursal').value = '';
            document.getElementById('supervisor').value = '';
            document.getElementById('montootorgar').value = '';
            formaPago.value = '';
            selectLinea.value = '';
            inputInteres.value = '';
            cantPago.value = '';
            fechaPrimerPago.value = '';
            inputFecha.value = '';
            fechaDebeSer.value = '';
            fechavencimiento.value = '';
            document.getElementsByClassName
            centroSeleccionado = null;
            // Limpiar radio buttons
            const radios = document.querySelectorAll('input[name="garantia"]');
            radios.forEach(radio => radio.checked = false);

            // Volver a mostrar la sección principal
            document.querySelectorAll('.seccion').forEach(sec => sec.classList.remove('visible'));
            document.querySelector('.datos-prestamos').classList.add('visible');

            // Restaurar estado del nav
            document.querySelectorAll('.nav-links a').forEach(link => link.classList.remove('active'));
            document.getElementById('link-datos').classList.add('active');
        }

        function limpiarModalPrestamoIndividual() {
            document.getElementById('lineaind').value = '';
            document.getElementById('sucursalind').value = '';
            document.getElementById('supervisorind').value = '';
            document.getElementById('asesorind').value = '';
            document.getElementById('montootorgarind').value = '';
            document.getElementById('tasainteresind').value = '';
            document.getElementById('plazoind').value = '';
            document.getElementById('tipo_pago').value = '';
            document.getElementById('frecuenciamesesind').value = '';
            document.getElementById('frecuenciadiasind').value = '';
            document.getElementById('microseguroind').value = '';
            document.getElementById('fechaaperturaind').value = '';
            document.getElementById('fechaprimerpagodebeserind').value = '';
            document.getElementById('fechavencimientoind').value = '';
            document.getElementById('cuotaind').value = '';
            document.getElementById('desembolsoind').value = '';
            document.getElementById('cuotafijaind').value = '';
            document.getElementById('cuotavariableind').value = '';
            document.getElementById('fiduciariaind').value = '';
            document.getElementById('hipotecariaind').value = '';
            document.getElementById('prendariaind').value = '';
            document.getElementById('colectorind').value = '';
            document.getElementById('aprobadoporind').value = '';
            document.getElementById('bancoind').value = '';
            document.getElementById('formapagoind').value = '';
            const radios = document.querySelectorAll('input[name="garantia"]');
            radios.forEach(radio => radio.checked = false);
        }
        // Eventos para cerrar el modal
        $('.close-btn1').on('click', function () {
            if ($('#modalprestamogrupal').is(':visible')) {
                $('#modalprestamogrupal').fadeOut();
                limpiarModalPrestamoGrupal();
            } else if ($('#modalprestamoIndividual').is(':visible')) {
                $('#modalprestamoIndividual').fadeOut();
                limpiarModalPrestamoIndividual();
            }
        });

        $(window).on('click', function (event) {
            if ($(event.target).is('#modalprestamogrupal')) {
                $('#modalprestamogrupal').fadeOut();
                limpiarModalPrestamoGrupal();
            } else if ($(event.target).is('#modalprestamoIndividual')) {
                $('#modalprestamoIndividual').fadeOut();
                limpiarModalPrestamoIndividual();
            }
        });

        $(document).on('keydown', function (event) {
            if (event.key === "Escape") {
                if ($('#modalprestamogrupal').is(':visible')) {
                    $('#modalprestamogrupal').fadeOut();
                    limpiarModalPrestamoGrupal();
                } else if ($('#modalprestamoIndividual').is(':visible')) {
                    $('#modalprestamoIndividual').fadeOut();
                    limpiarModalPrestamoIndividual();
                }
            }
        });
    }


    //Codio para vista Reversion de prestamos
    const modalreversionprestamo = document.getElementById('modalreversionprestamo');
    const btnreversionprestamo = document.getElementById('btn-abrirmodalreversion');
    const id_cliente = document.getElementById('codigo');
    const inputMonto = document.getElementById('monto');
    const fecha_apertura = document.getElementById('fecha_apertura');
    const fecha_vencimiento = document.getElementById('fecha_vencimiento');
    const btnAceptar = document.getElementById('btnAceptar');
    const inputPassword = document.getElementById('password');
    const motivo = document.getElementById('motivo');


    if (btnreversionprestamo) {
        btnreversionprestamo.addEventListener("click", async function (event) {
            event.preventDefault();

            const codigoCliente = id_cliente.value.trim();
            const motivoTexto = motivo.value.trim(); // ← asegúrate que exista este input en tu HTML
            const fecha_aperturaenviar = fecha_apertura.value.trim();
            const fecha_vencimientoenviar = fecha_vencimiento.value.trim();

            // 🚫 Validar campos requeridos
            if (codigoCliente === '') {
                mostrarAlerta("Por favor ingrese el código del cliente", "error");
                return;
            }

            if (motivoTexto === '') {
                mostrarAlerta("Por favor ingrese el motivo de la reversión", "error");
                return;
            }

            if (esAdministrador) {
                // 🔓 Admin: eliminar directamente
                const confirmacion = confirm("¿Está seguro de eliminar el préstamo del cliente?");
                if (!confirmacion) return;

                try {
                    const deleteResponse = await fetch(`/eliminar/desembolsoprestamo`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            codigoCliente,
                            fecha_apertura: fecha_aperturaenviar,
                            fecha_vencimiento: fecha_vencimientoenviar,
                            motivo: motivoTexto
                        })
                    });

                    const deleteData = await deleteResponse.json();

                    if (deleteData.success) {
                        mostrarAlerta("Cliente eliminado con éxito", "success");
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        mostrarAlerta("Error al eliminar cliente", "error");
                    }
                } catch (error) {
                    mostrarAlerta("Error en el servidor o en la solicitud", "error");
                }

            } else {
                // 🔐 No administrador: abrir modal para contraseña
                if (modalreversionprestamo) {
                    modalreversionprestamo.style.display = "block";

                    btnAceptar.addEventListener('click', async function (event) {
                        event.preventDefault();

                        const SpecialPassword = inputPassword.value.trim();

                        if (SpecialPassword === '') {
                            mostrarAlerta("Por favor ingrese una contraseña", "error");
                            return;
                        }

                        try {
                            btnAceptar.disabled = true;
                            btnAceptar.style.display = 'none';
                            mostrarAlerta("Procesando eliminación...", "info");

                            const response = await fetch('/validar/password', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ password: SpecialPassword })
                            });

                            const data = await response.json();

                            if (data.valida) {
                                const deleteResponse = await fetch(`/eliminar/desembolsoprestamo`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        codigoCliente,
                                        fecha_apertura: fecha_aperturaenviar,
                                        fecha_vencimiento: fecha_vencimientoenviar,
                                        motivo: motivoTexto
                                    })
                                });

                                const deleteData = await deleteResponse.json();

                                if (deleteData.success) {
                                    mostrarAlerta("Cliente eliminado con éxito", "success");
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    mostrarAlerta("Error al eliminar cliente", "error");
                                }
                            } else {
                                mostrarAlerta(data.mensaje || "Contraseña incorrecta", "error");
                            }
                        } catch (error) {
                            mostrarAlerta("Error en el servidor o en la solicitud", "error");
                        } finally {
                            btnAceptar.disabled = false;
                            btnAceptar.style.display = 'block';
                        }
                    }, { once: true }); // Solo una vez
                }
            }
        });

        //Funcion para mostrar el saldo de prestamo al escribir el codigo del cliente
        let debounceTimeout; // Variable para guardar el temporizador

        id_cliente.addEventListener('input', function () {
            clearTimeout(debounceTimeout); // Cada vez que escribe, reseteamos el timer

            debounceTimeout = setTimeout(() => { // Esperamos 500 ms después de que termine de escribir
                const codigoCliente = id_cliente.value.trim();

                if (codigoCliente) {
                    fetch(`/consulta/reversion/${codigoCliente}`)
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            } else {
                                throw new Error("Préstamo No Encontrado");
                            }
                        })
                        .then(data => {
                            inputMonto.value = data.monto !== null ? data.monto : '';
                            fecha_apertura.value = data.fecha_apertura !== null ? data.fecha_apertura : '';
                            fecha_vencimiento.value = data.fecha_vencimiento !== null ? data.fecha_vencimiento : '';
                        })
                        .catch(error => {
                            mostrarAlerta(error.message || "Error en la búsqueda", "error");
                        });
                } else {
                    inputMonto.value = '';
                }
            }, 500); // 500 milisegundos de espera
        });

    }

});

//Codigo Para Vista Mantenimiento de Asesor

document.addEventListener('DOMContentLoaded', function () {
    const btnVerificarpassword = document.getElementById('btnAceptar');
    const inputPassword = document.getElementById('password');
    const btnaceptarActuAsesor = document.getElementById('btnaceptarActuAsesor');
    const form = document.querySelector('#ModalEditarAsesor form');

    // Verificamos si estamos en la vista correcta
    if (document.getElementById('openModalBtnnuevoAsesor') && document.getElementById('tablaAsesores')) {



        // Inicialización segura de DataTables
        let tablaAsesores;

        try {
            // Verificamos si ya está inicializada
            if ($.fn.DataTable.isDataTable('#tablaAsesores')) {
                tablaAsesores = $('#tablaAsesores').DataTable();
            } else {
                tablaAsesores = $('#tablaAsesores').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                    },
                    columns: [
                        { data: 'id' },
                        { data: 'nombre' },
                        { data: 'sucursal' },
                        { data: 'created_at' },
                        { data: 'updated_at' }
                    ]
                });
            }

            // Evento para selección de filas - VERSIÓN CORREGIDA
            $('#tablaAsesores').on('click', 'tbody tr', function () {
                // Obtener la instancia correcta de DataTable
                const dt = $('#tablaAsesores').DataTable();
                const rowData = dt.row(this).data();
                const id_asesor = rowData[0];
                const nombre_asesor = rowData[1];
                const sucursalNombre = rowData[2];


                $('#modalreversionprestamo h2').text('Validación para Actualización');
                $('#modalreversionprestamo').fadeIn();

                btnVerificarpassword.addEventListener('click', function (event) {
                    event.preventDefault();

                    const SpecialPassword = inputPassword.value.trim();
                    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');

                    if (!csrfTokenElement) {
                        return;
                    }
                    const csrfToken = csrfTokenElement.getAttribute('content');
                    if (SpecialPassword === '') {
                        mostrarAlerta("Por Favor Ingrese una Contraseña Correcta", "error");
                        return;
                    } fetch('/validar/password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ password: SpecialPassword })
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Error de autenticación");
                            }
                            return response.json();
                        }).then(data => {
                            if (data.valida) {
                                $('#modalreversionprestamo').fadeOut();

                                $('#ModalEditarAsesor h2').html("Actualizar Asesor<br>" + nombre_asesor);
                                $('#ModalEditarAsesor #sucursalActua option').each(function () {
                                    if ($(this).text().trim() === sucursalNombre) {
                                        $('#ModalEditarAsesor #sucursalActua').val($(this).val());
                                    }
                                });
                                $('#ModalEditarAsesor #nombreActua').val(nombre_asesor);
                                $('#ModalEditarAsesor').fadeIn();


                                form.addEventListener('keydown', function (event) {
                                    if (event.key === 'Enter') {
                                        event.preventDefault(); // Evitar que se envíe el formulario por defecto
                                        btnaceptarActuAsesor.click(); // Llamar a la misma función que el clic en el botón
                                    }
                                });

                                const nombreActualizacion = document.getElementById('nombreActua').value;
                                const sucursalActualizacion = document.getElementById('sucursalActua').value;

                                if (btnaceptarActuAsesor) {
                                    btnaceptarActuAsesor.addEventListener('click', function (event) {
                                        event.preventDefault();

                                        // Captura actualizada aquí dentro
                                        const nombreActualizacion = document.getElementById('nombreActua').value;
                                        const sucursalActualizacion = document.getElementById('sucursalActua').value;

                                        fetch('/update/asesor/' + id_asesor, {
                                            method: 'PUT',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                                'X-Requested-With': 'XMLHttpRequest'
                                            },
                                            body: JSON.stringify({
                                                nombre: nombreActualizacion,
                                                sucursal: sucursalActualizacion
                                            })
                                        })
                                            .then(response => response.json())
                                            .then(data => {
                                                mostrarAlerta("Asesor Actualizado con éxito", "success");

                                                // Redirigir a la misma página en lugar de recargar
                                                setTimeout(() => {
                                                    window.location.href = window.location.href;
                                                }, 1000);
                                            })
                                            .catch(error => {
                                                mostrarAlerta("Error al actualizar asesor:", "error");
                                            });
                                    });
                                }

                            } else {
                                mostrarAlerta(data.mensaje || "Contraseña incorrecta", "error");
                            }
                        }).catch(error => {
                            mostrarAlerta("Ocurrió un error: " + error.message, "error");
                        });

                });
            });

        } catch (error) {
            mostrarAlerta('Error al inicializar DataTable:', "error");
        }

        // Resto de tu código para esta vista...
        const modalNuevoAsesor = document.getElementById('ModalNuevoAsesor');
        const btnAbrirModalNuevoAsesor = document.getElementById('openModalBtnnuevoAsesor');

        btnAbrirModalNuevoAsesor.addEventListener("click", function (event) {
            event.preventDefault();
            modalNuevoAsesor.style.display = "block";
        });
    }
});


function mostrarAlerta(mensaje, tipo) {
    const alerta = document.getElementById('alert-notification');
    const mensajeAlerta = document.getElementById('alert-notification-message');

    // Limpiar cualquier contenido o clase previa
    mensajeAlerta.textContent = mensaje;
    alerta.classList.remove('error_notification', 'success_notification', 'info_notification'); // Limpiar clases anteriores

    // Asignar la clase correcta según el tipo
    if (tipo === "error") {
        alerta.classList.add('error_notification'); // Clase de error (rojo)
    } else if (tipo === "success") {
        alerta.classList.add('success_notification'); // Clase de éxito (verde)
    } else if (tipo === "info") {
        alerta.classList.add('info_notification'); // Clase informativa (azul oscuro)
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



