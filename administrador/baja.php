<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener el ID del usuario desde la solicitud POST
    $id_analista = $_POST['idus'];

    if (empty($id_analista)) {
        echo "Error: ID Usuario no proporcionado.";
        exit;
    }

    // Comprobar si el usuario existe
    $sql_check = "SELECT id_analista FROM analistas WHERE id_analista = $id_analista";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        // El usuario existe, proceder con la eliminación
        $sql_delete = "DELETE FROM analistas WHERE id_analista = ?";
        if ($stmt_delete = $conn->prepare($sql_delete)) {
            $stmt_delete->bind_param("i", $id_analista);

            // Ejecutar la consulta de eliminación
            if ($stmt_delete->execute()) {
                // Mostrar mensaje y redirigir
                echo "<script>
                    alert('Usuario eliminado correctamente.');
                    setTimeout(function() {
                        window.location.href = 'seleccion_admin.html';
                    }, 1000); // 1 segundos de espera
                </script>";
            } else {
                echo "Error al eliminar el usuario: " . $conn->error;
            }

            $stmt_delete->close();
        } else {
            echo "Error al preparar la consulta de eliminación: " . $conn->error;
        }
    } else {
        // Mostrar mensaje y redirigir
        echo "<script>
        alert('El usuario proporcionado no existe.');
        setTimeout(function() {
            window.location.href = 'baja_us.html';
        }, 1000); // 1 segundos de espera
        </script>";
    }

    mysqli_free_result($result_check);
    $conn->close();
} else {
    echo "Error: Solicitud inválida.";
}
?>
