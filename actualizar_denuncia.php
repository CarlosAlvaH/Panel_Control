<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $resuelto = $_POST['resuelto'];

    include('Conexion/Conexion.php');

    $query = "UPDATE denuncias SET resuelto = :resuelto WHERE idDenuncia = :id";
    $stmt = $pdo->prepare($query);

    if ($stmt->execute([':resuelto' => $resuelto, ':id' => $id])) {
        echo "Denuncia actualizada correctamente.";
    } else {
        echo "Error al actualizar la denuncia.";
    }
}
?>
