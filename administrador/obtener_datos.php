<?php
include 'conexion.php';

$id_analista = $_GET['id'];
$sql = "SELECT nombre_analista AS nombre, ap_analista AS ap_paterno, am_analista AS ap_materno, usuario_analista AS usuario, contrasenia_analista AS contrasena 
        FROM analistas 
        WHERE id_analista = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_analista);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode([]);
}

$stmt->close();
$conn->close();
?>
