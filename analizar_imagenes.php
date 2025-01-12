<?php
// Incluir el archivo de conexión
include('Conexion/Conexion.php');
require 'vendor/autoload.php';

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

// Consultar las denuncias y usuarios desde la base de datos
$query = "
    SELECT d.idDenuncia, d.Descripcion, u.DNI, d.Ubicacion, d.Prioridad, d.Foto, d.Analizado
    FROM denuncias d
    JOIN usuario u ON d.idUsuario = u.idUsuario
    WHERE d.Ubicacion IS NOT NULL
";
$stmt = $pdo->query($query);
$denuncias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Instanciar el cliente de Google Cloud Vision
$vision = new ImageAnnotatorClient([
    'credentials' => 'C:\xampp\htdocs\app\AppControlEc3\claveServicios\moonlit-sphinx-443320-m8-dbc4673f71ab.json'
]);
// Analizar las imágenes y actualizar las prioridades
foreach ($denuncias as $denuncia) {
    // Verificar si ya ha sido analizada
    if ($denuncia['Analizado']) {
        continue;
    }

    $image = $denuncia['Foto'];
    if ($image) {
        // Convertir Long Blob a base64
        $imageData = $image; // Usa directamente los datos binarios
        $response = $vision->labelDetection($imageData);
        $labels = $response->getLabelAnnotations();
        $hasGarbage = false;
        $garbageCount = 0;

        // Etiquetas relacionadas con basura (ampliadas)
        $garbageLabels = [
            'garbage', 'trash', 'litter', 'rubbish', 'waste', 'pollution',
            'bag', 'plastic bag', 'garbage bag', 'debris', 'dump', 'landfill',
            'contamination', 'scrap', 'junk', 'overflow', 'spilled trash',
            'environmental pollution', 'waste management', 'disposal site',
            'household waste', 'industrial waste'
        ];

        foreach ($labels as $label) {
			$description = strtolower($label->getDescription());
			echo "Etiqueta detectada: $description<br>"; // Para depuración
			if (in_array($description, $garbageLabels)) {
				$hasGarbage = true;
				$garbageCount++;
			}
		}

        // Cambiar la prioridad según el análisis de la imagen
        if ($hasGarbage) {
            $priority = $garbageCount > 1 ? 'Alta' : 'Normal';
        } else {
            $priority = 'Ninguna';
        }
    } else {
        $priority = 'Ninguna';
    }

    // Actualizar la base de datos con la nueva prioridad y marcar como analizado
    $updateQuery = "
        UPDATE denuncias 
        SET Prioridad = :prioridad, Analizado = 1 
        WHERE idDenuncia = :idDenuncia
    ";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([
        ':prioridad' => $priority,
        ':idDenuncia' => $denuncia['idDenuncia']
    ]);
}

$vision->close();

// Redirigir de vuelta a index.php
header("Location: index.php");
exit();

?>