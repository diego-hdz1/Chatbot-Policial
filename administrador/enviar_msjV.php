<?php

session_start();  // Agrega esta línea al principio del archivo PHP

require_once '../vendor/autoload.php'; // Si usas Composer, de lo contrario incluye el archivo Twilio SDK de manera directa
include('../twilio_config.php');
include('../bd_config.php');

use Twilio\Rest\Client;

// Verifica si los datos del formulario fueron enviados correctamente
if (isset($_POST['referencia']) && isset($_POST['id_oficial']) && isset($_POST['respuesta']) && isset($_POST['seguimiento'])) {
    
    // Recoger los datos del formulario
    $referencia = $_POST['referencia'];
    $id_oficial = $_POST['id_oficial'];
    $respuesta = $_POST['respuesta'];
    $seguimiento = $_POST['seguimiento'];
    
    // Conectar a la base de datos para obtener el teléfono del oficial
    $conexion = mysqli_connect($host, $username, $password, $dbname) or die("Error en la conexión.");
    
    // Obtener el número de teléfono del oficial asignado a esta consulta
    $query = "SELECT telefono_oficial FROM oficiales WHERE id_oficial = '".$id_oficial."'";
    $result = mysqli_query($conexion, $query) or die("Error en la consulta: " . mysqli_error($conexion));
    $data = mysqli_fetch_array($result);
    $telefono_oficial = $data['telefono_oficial'];
    $toBd = "+521".$data['telefono_oficial'];
    $from = "whatsapp:+521".$data['telefono_oficial'];

    $query = "SELECT referenciaS FROM consultavehiculos WHERE referenciaV = '".$referencia."'";
    $result = mysqli_query($conexion, $query) or die("Error en la consulta: " . mysqli_error($conexion));
    $data = mysqli_fetch_array($result);
    $referenciaS = $data['referenciaS'];

    //Se actualiza el estado de la solicitud
    $query = 'UPDATE solicitudes SET estado = 3 WHERE referenciaS = '.$referenciaS.'';
    mysqli_query($conexion, $query);    
    $query = 'UPDATE solicitudes SET mensaje = "'.$respuesta.'" WHERE referenciaS = '.$referenciaS.'';
    mysqli_query($conexion, $query);
    $query = 'UPDATE consultavehiculos SET estado = 3 WHERE referenciaV = '.$referencia.'';
    mysqli_query($conexion, $query);
    
    // Verificar si el teléfono del oficial fue encontrado
    if ($telefono_oficial) {
                
        $client = new Client($sid, $token);
        
        // Mensaje a enviar
        
        try {
            // Enviar el mensaje SMS
            $client->messages->create(
                $from, // Número del oficial
                [
                    'from' => $twilioPhoneNumber,
                    'body' => "*".$respuesta."*"
                ]
            );

            if($seguimiento === 'si'){
                $responseMessage = "📋 Por favor, *copie*, *complete* y *envíe* el siguiente formulario como mensaje para que podamos darle seguimiento a su caso y permitirle realizar una nueva consulta.";
                $Message = "Seguimiento Vehículo\nUbicación actual:\nColonia actual: \nSector: \nCaracterísticas Vehículo: \nCondiciones Vehículo: \nNombre conductor:";
                $query = 'UPDATE consultavehiculos SET estado = 5 WHERE referenciaV = '.$referencia.'';
                mysqli_query($conexion, $query);
                $query = 'UPDATE solicitudes SET estado = 5 WHERE referenciaS = '.$referenciaS.'';
                mysqli_query($conexion, $query);

                $client->messages->create(
                    $from, // Número del oficial
                    [
                        'from' => $twilioPhoneNumber,
                        'body' => $responseMessage
                    ]
                );
                $client->messages->create(
                    $from, // Número del oficial
                    [
                        'from' => $twilioPhoneNumber,
                        'body' => $Message
                    ]
                );
            }else{
                // Enviar el mensaje SMS
                $client->messages->create(
                    $from, // Número del oficial
                    [
                        'from' => $twilioPhoneNumber,
                        'body' => "¡Gracias por su consulta! 🖥️ Si necesita hacer otra, no dude en enviarnos un mensaje. 📲 ¡Estamos aquí para ayudarte! 🙌"
                    ]
                );
            }
            // Cerrar la conexión
            mysqli_close($conexion);
            echo "Mensaje enviado exitosamente al oficial.";
        } catch (Exception $e) {
            echo "Error al enviar el mensaje: " . $e->getMessage();
        }
    } else {
        echo "No se encontró el número de teléfono del oficial.";
    }
}
?>
