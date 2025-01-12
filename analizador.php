<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir y Analizar Imagen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Encabezado -->
    <header class="header text-white text-center py-4">
        <div class="container">
            <img src="images/logo.jpg" alt="Logo" class="img-fluid mb-3" style="max-width: 150px;">
            <h1 class="display-4">Subir y Analizar Imagen</h1>
        </div>
    </header>

    <!-- Contenedor principal -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <form class="form-upload" action="upload.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="image" class="form-label">Selecciona una imagen para analizar:</label>
                                <input type="file" id="image" name="image" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Subir Imagen</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="index.php" class="btn btn-secondary">Volver a la Página Principal</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
