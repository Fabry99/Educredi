document.addEventListener('DOMContentLoaded', function() {
    // Seleccionamos las notificaciones
    let successNotification = document.querySelector('.custom_success');
    let errorNotification = document.querySelector('.custom_error');

    // Función para mostrar y ocultar las notificaciones
    function showNotification(notification) {
        if (notification) {
            // Mostrar la notificación
            notification.style.visibility = 'visible'; // Hacerla visible
            notification.style.opacity = 1; // Hacerla opaca

            // Después de 4 segundos, ocultarla
            setTimeout(function() {
                notification.style.opacity = 0; // Desvanecer la notificación
                notification.style.visibility = 'hidden'; // Ocultarla completamente
            }, 4000); // La notificación se oculta después de 4 segundos
        }
    }

    // Mostrar las notificaciones si existen
    if (successNotification) {
        showNotification(successNotification);
    }
    if (errorNotification) {
        showNotification(errorNotification);
    }
});

