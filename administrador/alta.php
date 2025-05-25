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
    $nombre = $_POST['usuario'];

    $sql = "SELECT * 
            FROM analistas 
            WHERE usuario_analista  = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
        alert('ERROR, el nombre de usuario ya esta en uso, selecciona otro');
        setTimeout(function() {
            window.location.href = 'alta_us.html';
        }, 1000); 
        </script>";
    } else {
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
}

$conn->close();
?>
