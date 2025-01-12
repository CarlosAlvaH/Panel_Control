<?php
// Incluir el archivo de conexión
include('Conexion/Conexion.php');
require 'vendor/autoload.php';

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

// Verificar si el archivo ha sido subido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];

    // Verificar si no hubo errores al subir el archivo
    if ($fileError === 0) {
        $fileContent = file_get_contents($fileTmpName);

        // Instanciar el cliente de Google Cloud Vision
        $vision = new ImageAnnotatorClient([
            'credentials' => 'C:\xampp\htdocs\app\AppControlEc3\claveServicios\moonlit-sphinx-443320-m8-dbc4673f71ab.json'
        ]);

        // Analizar la imagen usando Google Cloud Vision
        $response = $vision->labelDetection($fileContent);
        $labels = $response->getLabelAnnotations();
        $vision->close();

        $hasGarbage = false;
        $garbageCount = 0;

        $garbageLabels = [
            'garbage', 'trash', 'litter', 'rubbish', 'waste', 'pollution',
            'plastic', 'plastic bottle', 'plastic bag', 'bottle cap', 'food wrapper',
            'paper', 'cardboard', 'can', 'aluminum can', 'soda can',
            'compost', 'food waste', 'spoiled food', 'vegetable waste', 'fruit waste',
            'chemical waste', 'toxic waste', 'hazardous waste', 'oil spill', 'battery',
            'landfill', 'dump', 'garbage dump', 'polluted water', 'polluted soil', 'polluted beach',
            'garbage bag', 'trash bin', 'dustbin', 'waste bin', 'recycling bin'
        ];

        foreach ($labels as $label) {
            $description = strtolower($label->getDescription());
            $confidence = $label->getScore(); // Puntaje de confianza
            if (in_array($description, $garbageLabels) && $confidence > 0.7) {
                $hasGarbage = true;
                $garbageCount++;
            }
        }

        // Determinar la prioridad basada en el análisis de la imagen
        $priority = 'Ninguna';
        if ($hasGarbage) {
            $priority = $garbageCount > 1 ? 'Alta' : 'Normal';
        }

        // Redirigir a otra página con los resultados usando POST
        echo "<form id='resultForm' action='resultado.php' method='POST'>
                <input type='hidden' name='image_data' value='" . base64_encode($fileContent) . "'>
                <input type='hidden' name='priority' value='$priority'>
                <input type='submit' value='Ver resultados'>
              </form>";

        // Autoenviar el formulario para redirigir
        echo "<script>document.getElementById('resultForm').submit();</script>";
    } else {
        echo "Hubo un error al subir la imagen.";
    }
} else {
    echo "No se ha seleccionado ninguna imagen.";
}
?>
