import './bootstrap';
import $, { error, event } from 'jquery'; // Importar jQuery
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

    let clienteId = null;
    let clienteNombre = null;
    let datacliente = [];
    let centroSeleccionado = null;
    let seguro = 0;
    let capital = 0;


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
            clienteId = this.getAttribute('data-id');
            clienteNombre = this.getAttribute('data-name');

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

        $.ajax({
            url: '/prestamos/obtener-centros-grupos-clientes/' + clienteId,
            type: 'GET',
            success: function (response) {
                datacliente = response;
                renderCentros(response);
                configurarEventosSelects(); // 👈 Aquí activamos los listeners
            },
            error: function () {
                alert('Error al obtener los datos del cliente.');
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


    function calcularCuota(monto, tasa, row = null) {


        // Obtener valores de los elementos del formulario
        const formaPago = document.getElementById('formaPago');
        const cantPago = document.getElementById('cantPagos');
        const manejoInput = document.getElementById('manejo');
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

    document.getElementById('montootorgar').addEventListener('input', function () {
        const montoTotal = parseFloat(this.value);
        const inputsMonto = document.querySelectorAll('input[name="monto"]');
        const cantidadPersonas = inputsMonto.length;
        const totalInput = document.getElementById('total');

        if (!isNaN(montoTotal) && cantidadPersonas > 0) {
            const montoPorPersona = (montoTotal / cantidadPersonas).toFixed(2);
            inputsMonto.forEach(input => {
                input.value = montoPorPersona;
            });

            // Mostrar el total exacto distribuido (puede tener decimales ajustados)
            const montoDistribuido = montoPorPersona * cantidadPersonas;
            totalInput.value = montoDistribuido.toFixed(2);
        } else {
            // Si se borra el monto o es inválido, limpiar la tabla y el total
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


        // Obtener el valor directamente (formato YYYY-MM-DD)
        console.log("id_colector".id_colector.value);
        console.log("id_aprobadopor".id_aprobadorpor.value);
    });

    // Escuchar cambios en la forma de pago
    document.getElementById('formaPago')?.addEventListener('change', () => {
        actualizarTodasLasCuotas();
    });
    function manejarCambioGrupo() {
        const grupoSeleccionado = this.value;
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
                alert('Error al obtener los miembros del grupo.');
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
        // contenedorMiembrosGrupo.addEventListener('input', function (e) {
        //     if (e.target.matches('input[name="monto"], input[name="tasa"], select[name="formaPago"], input[name="cantPagos"]')) {
        //         const row = e.target.closest('tr');

        //         // Obtener elementos
        //         const montoInput = row.querySelector('input[name="monto"]');
        //         const tasaInput = row.querySelector('input[name="tasa"]');
        //         const cuotaInput = row.querySelector('input[name="cuota"]');
        //         const formaPagoSelect = row.querySelector('select[name="formaPago"]') || document.getElementById('formaPago');
        //         const cantPagoInput = row.querySelector('input[name="cantPagos"]') || document.getElementById('cantPagos');
        //         const manejoInput = document.getElementById('manejo');
        //         const tasaIvaInput = document.getElementById('tasa_iva');

        //         // Valores con fallback
        //         const monto = parseFloat(montoInput?.value) || 0;
        //         const tasa = parseFloat(tasaInput?.value) || 0;
        //         const cantPagos = parseInt(cantPagoInput?.value) || 12;
        //         const tasa_iva = parseFloat(tasaIvaInput?.value) || 0.13;
        //         const manejo = (10 / cantPagos);

        //         const textoSeleccionado = formaPagoSelect.options[formaPagoSelect.selectedIndex]?.text?.trim();
        //         const diasporpago = diasPorTipoPago[textoSeleccionado] || 30;

        //         if (monto > 0 && tasa > 0 && cuotaInput) {
        //             // 1. Tasa diaria
        //             const tasaDiaria = (tasa / 360) / 100;

        //             // 2. Interés
        //             const interes = monto * tasaDiaria * diasporpago;

        //             // 3. Seguros
        //             const porcentajemonto = monto * 0.02;
        //             const segurodiario = porcentajemonto / 365;
        //             const microseguro = (segurodiario * diasporpago) * (1 + tasa_iva);

        //             // 4. IVA
        //             const iva = interes * tasa_iva;

        //             // 5. Cálculo cuota principal
        //             const tasadiariaparacuota = (tasa / 365) / 100;
        //             const tasaporperiodo = tasadiariaparacuota * diasporpago;
        //             const baseCalculo = Math.pow(1 + tasaporperiodo, cantPagos);
        //             const valorcuota = (monto * tasaporperiodo * baseCalculo) / (baseCalculo - 1);

        //             // 6. Cuota final
        //             const cuotaFinal = (valorcuota + iva + manejo - microseguro);

        //             capital = (cuotaFinal - interes - manejo - microseguro - iva);
        //             seguro = (interes + capital + iva);

        //             // Almacenar TODOS los datos en atributos data-* de la fila
        //             row.dataset.calculoDetalle = JSON.stringify({
        //                 valorcuota: valorcuota,
        //                 iva: iva,
        //                 manejo: manejo,
        //                 microseguro: microseguro,
        //                 cuotaFinal: cuotaFinal,
        //                 diasporpago: diasporpago,
        //                 cantPagos: cantPagos,
        //                 capital: capital,
        //                 seguro: seguro,
        //             });

        //             // Asignar solo el valor final al input
        //             cuotaInput.value = cuotaFinal > 0 ? cuotaFinal.toFixed(2) : '0.00';
        //         } else {
        //             cuotaInput.value = '0.00';
        //         }
        //     }

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

        // Validar que se seleccionó una garantía
        if (!garantiaId) {
            alert('Por favor seleccione un tipo de garantía');
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




                    }
                };
            } catch (e) {
                console.error('Error procesando fila:', row, e);
                return null;
            }
        }).filter(Boolean);
    }
    document.getElementById('btnAceptar').addEventListener('click', function (event) {
        event.preventDefault(); // Evita el envío del formulario si estás usando un submit
        const montoOtorgar = parseFloat(document.getElementById('montootorgar').value);
        const total = parseFloat(document.getElementById('total').value);


        const prestamos = obtenerTodosLosDatos();
        console.log(prestamos);
        // Verificamos si los valores son números válidos y mayores que 0
        if (isNaN(montoOtorgar) || isNaN(total) || montoOtorgar <= 0 || total <= 0) {
            // Si alguno de los valores no es un número válido o es igual o menor a 0, mostramos la alerta de error
            mostrarAlerta("Por favor, ingrese valores válidos y mayores que 0 para los montos.", "error");
        } else if (montoOtorgar !== total) {
            // Si los montos no coinciden, mostramos la alerta de error
            mostrarAlerta("¡Error! Los montos no coinciden.", "error");
        } else {
            // Si todo es correcto, mostramos una alerta de éxito


            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token no encontrado');
                return;
            }

            console.log(JSON.stringify({ prestamos: prestamos }));

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
                    console.log('Respuesta del servidor:', data);
                })
                .catch(error => {
                    console.error('Error al enviar:', error);
                });





        }
    });

    // Limpiar y cerrar modal prestamo grupal
    function limpiarModalPrestamoGrupal() {
        selectCentro.innerHTML = '<option value="" disabled selected>Centro:</option>';
        selectGrupo.innerHTML = '<option value="" disabled selected>Grupo:</option>';
        document.getElementById('contenedorMiembrosGrupo').innerHTML = '';
        document.getElementById('id').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('colector').value = '';
        document.getElementById('aprobadopor').value = '';
        document.getElementById('formaPago').value = '';
        document.getElementById('sucursal').value = '';
        document.getElementById('supervisor').value = '';
        document.getElementById('linea').value = '';
        document.getElementById('montootorgar').value = '';
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

    // Eventos para cerrar el modal
    $('.close-btn1').on('click', function () {
        $('#modalprestamogrupal').fadeOut();
        limpiarModalPrestamoGrupal();
    });

    $(window).on('click', function (event) {
        if ($(event.target).is('#modalprestamogrupal')) {
            $('#modalprestamogrupal').fadeOut();
            limpiarModalPrestamoGrupal();
        }
    });

    $(document).on('keydown', function (event) {
        if (event.key === "Escape") {
            $('#modalprestamogrupal').fadeOut();
            limpiarModalPrestamoGrupal();
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


