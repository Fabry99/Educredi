document.addEventListener('DOMContentLoaded', function () {
    let ultimaFecha = null;
    let ultimoComprobante = null;

    document.getElementById('fecha').addEventListener('input', verificarCampos);
    document.getElementById('comprobante').addEventListener('input', verificarCampos);

    function verificarCampos() {
        const fecha = document.getElementById('fecha').value;
        const comprobante = document.getElementById('comprobante').value;

        if (fecha && comprobante) {
            // Solo consultar si los valores han cambiado
            if (fecha !== ultimaFecha || comprobante !== ultimoComprobante) {
                ultimaFecha = fecha;
                ultimoComprobante = comprobante;
                realizarConsulta(fecha, comprobante);
            }
        }
    }

    function realizarConsulta(fecha, comprobante) {

        fetch('/caja/consultar/cuotas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ fecha, comprobante })
        })
            .then(response => response.json())
            .then(data => {
                llenarTabla(data);
                // Procesa la respuesta
            })
            .catch(error => console.error("Error en la consulta:", error));
    }
    function llenarTabla(datos) {
        const tbody = document.querySelector('#tablareversionCuota tbody');
        const totalInput = document.getElementById('total');
        tbody.innerHTML = ''; // Limpiar la tabla antes de llenarla
        let totalCuota = 0;

        if (datos.length === 0) {
            const fila = document.createElement('tr');
            fila.innerHTML = `<td colspan="3" style="text-align: center;">Sin resultados</td>`;
            tbody.appendChild(fila);
            totalInput.value = ''; // Limpiar total si no hay datos
            return;
        }

        datos.forEach(item => {
            // Asegúrate de convertir a número para evitar concatenación de strings
            const valor = parseFloat(item.valor_cuota) || 0;
            totalCuota += valor;

            const fila = document.createElement('tr');
            fila.innerHTML = `
            <td style='text-align:center;'>${item.id_cliente}</td>
            <td>${item.nombre} ${item.apellido}</td>
            <td style='text-align:center;'>${valor.toFixed(2)}</td>
        `;
            tbody.appendChild(fila);
        });

        // Mostrar el total formateado con 2 decimales
        totalInput.value = totalCuota.toFixed(2);
    }

    const btnAbrirModalContraseña = document.getElementById('btn-abrirmodalreversion');
    const modalreversion = document.getElementById('modalreversionprestamo');
    const btnAceptar = document.getElementById('btnAceptar');
    const inputPassword = document.getElementById('password');

    btnAbrirModalContraseña.addEventListener('click', function (event) {
        event.preventDefault();
        const fecha = document.getElementById('fecha').value;
        const Comprobante = document.getElementById('comprobante').value;
        const motivo = document.getElementById('motivo').value;


        if (fecha === '') {
            mostrarAlerta('Debe ingresar una fecha', 'error');
            return;
        }
        if (Comprobante === '') {
            mostrarAlerta('Debe ingresar un comprobante valido', 'error');
            return;
        }
        if (motivo === '') {
            mostrarAlerta('Debe ingresar un motivo', 'error');
            return;
        }
        if (esAdministrador) {
            // Lógica para administrador (eliminar directamente)
            eliminarReversion();
        } else {
            // Mostrar modal si no es administrador
            modalreversion.style.display = 'block';

            btnAceptar.addEventListener('click', async function (event) {
                event.preventDefault();
                const SpecialPassword = inputPassword.value.trim();
                if (SpecialPassword === '') {
                    mostrarAlerta("Por Favor Ingrese una Contraseña Correcta", "error");
                    return;
                } else {
                    btnAceptar.disabled = true;
                    btnAceptar.style.display = 'none';
                    mostrarAlerta("Procesando eliminación...", "info");
                    try {
                        const response = await fetch('/caja/validar', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ password: SpecialPassword })
                        });
                        const data = await response.json();
                        if (data.valida) {
                            mostrarAlerta("hola", "success");
                            eliminarReversion();
                        } else {
                            mostrarAlerta("Contraseña Incorrecta", "error")
                            btnAceptar.disabled = false;
                            btnAceptar.style.display = 'block';
                        }
                    } catch (error) {
                        console.error("Error al validar contraseña:", error);
                        mostrarAlerta("Error al comunicarse con el servidor", "error");
                        btnAceptar.disabled = false;
                        btnAceptar.style.display = 'block';
                    }
                }
            });
        }
    })
});
async function eliminarReversion() {
    const fecha = document.getElementById('fecha').value;
    const comprobante = document.getElementById('comprobante').value;
    const motivo = document.getElementById('motivo').value;

    try {
        mostrarAlerta("Procesando eliminación...", "info");

        const response = await fetch('/caja/revertircuota', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                fecha: fecha,
                comprobante: comprobante,
                motivo: motivo
            })
        });

        const data = await response.json();

        if (data.success) {
            mostrarAlerta("Reversión procesada correctamente", "success");
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            mostrarAlerta(data.message || "Ocurrió un error al eliminar", "error");

        }
    } catch (error) {
        console.error("Error en eliminación:", error);
        mostrarAlerta("Error al comunicarse con el servidor", "error");
    }
}
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