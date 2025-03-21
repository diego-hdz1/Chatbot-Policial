<?php

session_start();

// Incluir la configuración de la base de datos
include('../bd_config.php');

// Conectar a la base de datos
$conexion = mysqli_connect($host, $username, $password, $dbname) or die("Error en la conexión.");

// Obtener la consulta más reciente con estado 1
$query = "SELECT * FROM consultapersonas WHERE estado = 1 ORDER BY id_consulta DESC LIMIT 1";
$result = mysqli_query($conexion, $query) or die("Error en la consulta: " . mysqli_error($conexion));

$consulta = mysqli_fetch_array($result);

// Si la consulta existe, devolver los datos en formato JSON
if ($consulta) {
    $response = [
        'referencia' => $consulta['id_consulta'],
        'id_oficial' => $consulta['id_oficial'],
        'referenciaS' => $consulta['referenciaS'],
        'nombre_sospechoso' => $consulta['nombre_sospechoso']." ".$consulta['ap_sospechoso']." ".$consulta['am_sospechoso'],
        'fechaNacimiento' => $consulta['fechaNacimiento_sospechoso'],
        'motivo' => $consulta['motivo_consulta']

    ];
    // Devolver los datos en formato JSON
    /**Modificar los estados */
    
    echo json_encode($response);
    //$_SESSION['referenciaS'] = $consulta['nombre_sospechoso'];
} else {
    // Si no hay consulta con estado 1, devolver datos vacíos
    $response = [
        'referencia' => 'En espera',
        'id_oficial' => 'En espera',
        'referenciaS' => 'En espera',
        'nombre_sospechoso' => 'En espera',
        'fechaNacimiento' => 'En espera',
        'motivo' => 'En espera'
    ];
    // Devolver los datos en formato JSON
    echo json_encode($response);
}

// Cerrar la conexión
mysqli_close($conexion);

?>
