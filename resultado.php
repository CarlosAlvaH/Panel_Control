<?php
if (isset($_POST['priority']) && isset($_POST['image_data'])) {
    $priority = $_POST['priority'];
    $imageData = $_POST['image_data']; // Imagen en base64

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Análisis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-body text-center">
                        <h1 class="display-4 mb-4">Resultado del Análisis</h1>
                        <?php if ($imageData): ?>
                            <img src="data:image/jpeg;base64,<?php echo $imageData; ?>" alt="Imagen Subida" class="img-fluid mb-4" />
                        <?php else: ?>
                            <p>No se pudo cargar la imagen.</p>
                        <?php endif; ?>
                        <h2 class="mb-3">Prioridad: <span class="badge <?php echo ($priority == 'Alta') ? 'bg-danger' : ($priority == 'Normal' ? 'bg-warning' : 'bg-success'); ?>"><?php echo $priority; ?></span></h2>
                        <a href="index.php" class="btn btn-secondary">Volver a la Página Principal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
} else {
    echo "No se han recibido los parámetros necesarios.";
}
?>
