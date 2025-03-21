<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Comprobar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    // Preparar la consulta SQL para insertar los datos
    $sql = "INSERT INTO analistas (nombre_analista, ap_analista, am_analista, usuario_analista, contrasenia_analista, rol) 
            VALUES ('$nombre', '$apellido_paterno', '$apellido_materno', '$usuario', '$contrasena', '$rol')";

    if ($conn->query($sql) === TRUE) {
        // Mostrar mensaje y redirigir con JavaScript
        echo "<script>
            alert('Nuevo usuario agregado correctamente.');
            setTimeout(function() {
                window.location.href = 'seleccion_admin.html';
            }, 1000); // 1 segundo de espera
        </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
