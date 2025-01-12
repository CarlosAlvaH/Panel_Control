document.addEventListener("DOMContentLoaded", function () {
    const botonesMostrarImagen = document.querySelectorAll('.btn-mostrar-imagen');
    const modalImagen = document.getElementById('modal-imagen');
    const imagenModal = document.getElementById('modal-imagen-foto');
    const header = document.getElementById('header');
    const mainContent = document.getElementById('main-content');

    // Crear una única instancia del modal
    const nuevoModal = new bootstrap.Modal(modalImagen, {
        backdrop: true,
        keyboard: true
    });

    // Función para activar el modal
    botonesMostrarImagen.forEach(function (boton) {
        boton.addEventListener('click', function () {
            const foto = boton.getAttribute('data-foto');
            imagenModal.src = foto;

            // Aplicar inert al contenido principal
            header.setAttribute('inert', '');
            mainContent.setAttribute('inert', '');

            // Mostrar el modal
            nuevoModal.show();
        });
    });

    // Quitar inert cuando se cierra el modal
    modalImagen.addEventListener('hidden.bs.modal', function () {
        header.removeAttribute('inert');
        mainContent.removeAttribute('inert');
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const modalImagen = document.getElementById('modal-imagen');
    modalImagen.classList.remove('show'); // Asegúrate de que no tenga clases de "mostrar"
    modalImagen.style.display = 'none';  // Ocúltalo completamente
});


let map;
let markers = []; // Array para almacenar los marcadores

// Inicializar el mapa
function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: -12.045871952074563, lng: -77.04496935009956 },
        zoom: 12,
    });

    // Coordenadas desde PHP (insertadas dinámicamente)
    const ubicaciones = JSON.parse(document.getElementById("data-ubicaciones").textContent);

    // Agregar marcadores
    ubicaciones.forEach((ubicacion, index) => {
        addMarker(ubicacion, index);
    });

    // Vincular clics de filas de tabla a los marcadores
    const rows = document.querySelectorAll("#table-container table tbody tr");
    rows.forEach((row) => {
        const id = row.getAttribute("data-id");

        row.addEventListener("click", () => {
            // Encuentra el marcador correspondiente usando el id
            const marker = markers.find(m => m.id === parseInt(id));

            if (marker) {
                highlightMarker(marker);
            }
        });
    });
}

// Asegurar que initMap esté disponible globalmente
window.initMap = initMap;

// Agregar un marcador al mapa
function addMarker(data) {
    const marker = new google.maps.Marker({
        position: { lat: data.lat, lng: data.lng },
        map: map,
        icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
    });

    // Asocia el id único con el marcador
    marker.id = data.id;

    // Almacena el marcador
    markers.push(marker);

    // Configura la ventana de información
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <p><strong>DNI:</strong> ${data.dni}</p>
            <p><strong>Descripción:</strong> ${data.descripcion}</p>
            <p><strong>Prioridad:</strong> ${data.prioridad}</p>
            ${data.foto ? `<img src="${data.foto}" style="width:100px;height:auto;">` : "<p>Sin imagen</p>"}
        `,
    });

    marker.addListener("click", () => {
        infoWindow.open(map, marker);
    });
}

// Resaltar un marcador y centrar el mapa
function highlightMarker(marker) {
    // Resetear iconos
    markers.forEach(m => m.setIcon("http://maps.google.com/mapfiles/ms/icons/red-dot.png"));

    // Cambiar el icono del marcador seleccionado
    marker.setIcon("http://maps.google.com/mapfiles/ms/icons/blue-dot.png");

    // Centrar el mapa en el marcador seleccionado
    map.setCenter(marker.getPosition());
    map.setZoom(14);
}

// Resaltar una fila seleccionada
function highlightRow(selectedRow) {
    const rows = document.querySelectorAll("#table-container table tbody tr");
    rows.forEach((row) => row.classList.remove("selected"));
    selectedRow.classList.add("selected");
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-actualizar').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const checkbox = document.querySelector(`.resuelto-checkbox[data-id="${id}"]`);
            const resuelto = checkbox.checked ? 1 : 0;

            fetch('actualizar_denuncia.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${id}&resuelto=${resuelto}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});