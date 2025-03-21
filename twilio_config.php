<?php
// Incluir el autoload de Composer
require_once 'vendor/autoload.php';  // Asegúrate de que esta ruta sea correcta

use Twilio\Rest\Client;

// Definir las credenciales de Twilio
$sid = 'AC0d289ddce19ee01073dad358b931d75b';  // Tu SID de cuenta de Twilio
$token = '95c91c692fa11b32bd219a022dee621c';  // Tu token de autenticación de Twilio
$twilioPhoneNumber = "whatsapp:+14155238886";  // Tu número Twilio de WhatsApp

// Crear una instancia del cliente de Twilio
$client = new Client($sid, $token);
?>
