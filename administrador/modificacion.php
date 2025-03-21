<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_analista = $_POST['idus'];
    $nombre = $_POST['nombre'];
    $ap_paterno = $_POST['apellido_paterno'];
    $ap_materno = $_POST['apellido_materno'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Actualizar datos en la tabla `analistas`
    $sql = "UPDATE analistas 
            SET nombre_analista = ?, ap_analista = ?, am_analista = ?, usuario_analista = ?, contrasenia_analista = ? 
            WHERE id_analista = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $ap_paterno, $ap_materno, $usuario, $contrasena, $id_analista);

    if ($stmt->execute()) {
        // Mostrar mensaje y redirigir
        echo "<script>
            alert('Datos actualizados correctamente.');
            setTimeout(function() {
                window.location.href = 'seleccion_admin.html';
            }, 1000); // 1 segundo de espera
        </script>";
    } else {
        echo "Error al actualizar los datos: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
