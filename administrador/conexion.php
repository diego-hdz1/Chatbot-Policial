<?php
// Configuración de la conexión
$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "bd";

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
