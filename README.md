Se necesita una cuenta de Twilio activa, una vez obtenida la cuenta de twilio es necesario activar un número de prueba y modificar el archivo "twilio_config.php" conforme a las credenciales de tu cuenta de twilio, es decir, modificar:
- $sid = '';  // Tu SID de cuenta de Twilio
$token = '';  // Tu token de autenticación de Twilio
$twilioPhoneNumber = "whatsapp:";  // Tu número Twilio de WhatsApp
