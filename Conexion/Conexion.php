<?php
// Detalles de conexión a la base de datos en Clever Cloud
$host = 'bce6vf4tu3cmol22tsge-mysql.services.clever-cloud.com';  // El host de tu base de datos
$port = '3306';  // Puerto del servicio de base de datos
$user = 'uxxvwebuxr4dzmz5';  // Tu nombre de usuario
$password = 'L1N5W18vBAwcitoMwyc2';  // Tu contraseña
$dbname = 'bce6vf4tu3cmol22tsge';  // El nombre de la base de datos

try {
    // Crear la conexión PDO
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $password);
    // Configurar el modo de error para lanzar excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa a la base de datos!";
} catch (PDOException $e) {
    // Manejar errores de conexión
    echo "Error de conexión: " . $e->getMessage();
}
?>
