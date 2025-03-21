<?php
// Configuración de conexión con la base de datos
$servername = "localhost";  // Cambia por el host de tu base de datos si es necesario
$username = "root";         // Cambia por el usuario de tu base de datos
$password = "";             // Cambia por la contraseña de tu base de datos
$dbname = "bd";             // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Preparar la consulta para buscar al usuario en la base de datos
    // Aseguramos que los dos parámetros se incluyan correctamente
    $sql = "SELECT `id_analista`, `nombre_analista`, `ap_analista`, `am_analista`, `usuario_analista`, `contrasenia_analista`, `rol`
            FROM `analistas`
            WHERE `usuario_analista` = ?";

    // Preparar la sentencia
    if ($stmt = $conn->prepare($sql)) {
        // Enlazar los parámetros (el nombre de usuario que viene del formulario)
        $stmt->bind_param("s", $user);  // Vinculamos el parámetro del usuario

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $stmt->store_result();

        // Comprobar si existe el usuario
        if ($stmt->num_rows > 0) {
            // Vincular las variables de los resultados
            $stmt->bind_result($id, $nombre, $ap, $am, $usuario, $contrasenia, $rol);

            // Obtener los datos
            $stmt->fetch();

            // Mostrar los datos para debug (verificar los valores que están siendo recuperados)
            // Puedes eliminar estas líneas una vez que todo funcione correctamente
            echo "Usuario encontrado: " . $usuario . "<br>";
            echo "Contraseña de la base de datos: " . $contrasenia . "<br>";

            // Verificar si las contraseñas coinciden
            // Si las contraseñas en la base de datos están cifradas (por ejemplo, con password_hash), entonces usa password_verify()
            // Si las contraseñas están en texto plano, solo compara directamente las contraseñas.
            if ($contrasenia == $pass) { // Si las contraseñas están en texto claro
                if($rol == 1){
                    header("Location: administrador/seleccion_analista.html");
                    exit();
                }else{
                    header("Location: administrador/seleccion_admin.html");
                    exit();
                }
                // Si la contraseña es correcta, redirigir a seleccion.html
                
            } else {
                // Si la contraseña es incorrecta, redirigir de nuevo al login
                echo "<script>alert('Contraseña incorrecta.'); window.location.href='index.html';</script>";
            }
        } else {
            // Si no se encuentra el usuario, redirigir al login
            echo "<script>alert('Usuario no encontrado.'); window.location.href='index.html';</script>";
        }

        // Cerrar la sentencia
        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
