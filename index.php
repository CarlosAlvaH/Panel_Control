<?php
// Incluir el archivo de conexión
include('Conexion/Conexion.php');

// Consultar las denuncias y usuarios desde la base de datos
$query = "
    SELECT d.Descripcion, u.DNI, d.Ubicacion, d.Prioridad, d.Foto, d.resuelto, d.idDenuncia
    FROM denuncias d
    JOIN usuario u ON d.idUsuario = u.idUsuario
    WHERE d.Ubicacion IS NOT NULL
";

$stmt = $pdo->query($query);
$denuncias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar las coordenadas
$coordenadas = [];
foreach ($denuncias as $id => $denuncia) {
    if (preg_match('/-?\d+\.\d+,-?\d+\.\d+/', $denuncia['Ubicacion'], $matches)) {
        $coords = explode(',', $matches[0]);
        $coordenadas[] = [
            'id' => $id, // Índice único
            'lat' => floatval($coords[0]),
            'lng' => floatval($coords[1]),
            'dni' => $denuncia['DNI'],
            'descripcion' => $denuncia['Descripcion'],
			'idDe' => $denuncia['idDenuncia'],
            'prioridad' => $denuncia['Prioridad'],
            'foto' => $denuncia['Foto'] ? 'data:image/jpeg;base64,' . base64_encode($denuncia['Foto']) : null,
            'resuelto' => isset($denuncia['resuelto']) ? (bool)$denuncia['resuelto'] : false // Agregar el estado de resolución
        ];
    }
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa con Tabla de Denuncias</title>
    <!-- Bootstrap CSS -->
    <link href="	https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Scripts -->
    <script defer src="js/script.js"></script>
    <script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6yoAcxrS2h2AKegbaSll2tx7rSNLF6yQ&callback=initMap"></script>
</head>
<body>
    <!-- Encabezado -->
    <header id="header" class="header text-white p-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="images/logo.jpg" alt="Logo" class="img-fluid me-3" style="height: 50px;">
            <h1 class="h5 mb-0">Gestión de Denuncias</h1>
        </div>
    </header>

    <main id="main-content" class="container-fluid mt-4">
        <div class="row" >
            <!-- Tabla de denuncias -->
            <section id="table-container" class="col-lg-4 col-md-5 col-12 bg-light p-3 rounded shadow-sm">
                <h2 class="h6 mb-4">Lista de Denuncias</h2>
                <form method="POST" action="analizar_imagenes.php" class="mb-3">
                    <button type="submit" class="btn btn-warning w-100">Analizar Imágenes y Actualizar Prioridades</button>
                </form>
                <a href="analizador.php" class="btn btn-warning w-100 mb-3">Ir al Analizador</a>
                <!-- Generar tabla dinámicamente -->
                <table class="table table-striped table-hover">
                    <?php
                    // Agrupar denuncias por prioridad
                    $prioridades = ['Alta', 'Normal', 'Ninguna'];
                    foreach ($prioridades as $prioridad): ?>
                        <thead>
                            <tr class="table-warning">
                                <th colspan="6">Prioridad <?php echo htmlspecialchars($prioridad); ?></th>
                            </tr>
                            <tr>
                                <th>DNI</th>
                                <th>Descripción</th>
                                <th>Prioridad</th>
                                <th>Acciones</th>
								<th colspan=2>Problema Atendido</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coordenadas as $index => $denuncia): ?>
                                <?php if ($denuncia['prioridad'] === $prioridad): ?>
                                    <tr data-id="<?php echo $denuncia['id']; ?>">
                                        <td><?php echo htmlspecialchars($denuncia['dni']); ?></td>
                                        <td><?php echo htmlspecialchars($denuncia['descripcion']); ?></td>
                                        <td><?php echo htmlspecialchars($denuncia['prioridad']); ?></td>
                                        <td>
                                            <!-- Botón Mostrar Imagen -->
                                            <?php if ($denuncia['foto']): ?>
                                                <button type="button" class="btn btn-sm btn-primary btn-mostrar-imagen" data-bs-toggle="modal" data-bs-target="#modal-imagen"  data-foto="<?php echo $denuncia['foto']; ?>">
                                                    Mostrar Imagen
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">Sin imagen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- Checkbox para Resuelto -->
                                            <input type="checkbox" class="form-check-input resuelto-checkbox" 
                                                data-id="<?php echo $denuncia['idDe']; ?>" 
                                                <?php echo $denuncia['resuelto'] ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <!-- Botón Actualizar -->
                                            <button class="btn btn-sm btn-secondary btn-actualizar" data-id="<?php echo $denuncia['idDe']; ?>">Actualizar</button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    <?php endforeach; ?>
                </table>
            </section>

            <!-- Mapa -->
            <section id="map-container" class="col-lg-8 col-md-7 col-12">
                <div id="map" class="rounded shadow-sm" style="height: 500px;"></div>
            </section>
        </div>
    </main>
	
	<!-- Modal para mostrar imagen -->
		<div id="modals-container">
        <div class="modal fade" id="modal-imagen" tabindex="-1" aria-labelledby="modal-imagen-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-imagen-label">Imagen de la Denuncia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img id="modal-imagen-foto" src="" alt="Imagen de denuncia" class="img-fluid w-100">
                    </div>
                </div>
            </div>
        </div>
    </div>

	
    <!-- Pasar datos de coordenadas al script -->
    <script id="data-ubicaciones" type="application/json">
        <?php echo json_encode($coordenadas); ?>
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
		
</body>
</html>


