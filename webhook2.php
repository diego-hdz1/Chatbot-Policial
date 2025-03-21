<?php
session_start();

// webhook.php
require_once 'twilio_config.php';
require 'bd_config.php';
require 'vendor/autoload.php';

use Twilio\Rest\Client;



$twilio = new Client($sid, $token);

// Verificar si el número de teléfono ya tiene sesión iniciada
$from = $_POST['From'];
$toBd = str_replace("whatsapp:", "", $from); // Para saber el número de teléfono y verificarlo en la BD
$numOficial = str_replace("whatsapp:+521", "", $from); // Para saber el número de teléfono y verificarlo en la BD

if (!isset($_SESSION[$toBd])) {
    // Si no existe la sesión para este número, inicializar las variables necesarias
    $_SESSION[$toBd] = [
        'awaiting_unit_input' => false, // Esperando entrada de unidad
        'tipo_consulta' => null, // Guardar tipo de consulta (vehiculo o persona)
        'consul_activa' =>false,
        'referenciaS' =>null,
        'id_oficial' => null
    ];
}

// Guardar el estado actual de la sesión en un archivo para inspección
file_put_contents(
    'session_debug.log',
    "Sesión para $toBd:\n" . print_r($_SESSION[$toBd], true) . "\n",
    FILE_APPEND
);

$ver = false;
$id_oficial;
$result;
$awaitingUnitInput = $_SESSION[$toBd]['awaiting_unit_input'];
$tipoConsulta = $_SESSION[$toBd]['tipo_consulta']; // Tipo de consulta (vehiculo o persona)
$referenciaSol = $_SESSION[$toBd]['referenciaS']; //Verifica si hay una consulta en curso //Sera cambiado por la variable de la BD


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = strtolower(trim($_POST['Body'])); // Convertir el mensaje a minúsculas y eliminar espacios en blanco

    $sendSecondMessage = false; // Controlar si se debe enviar un segundo mensaje
    $secondMessage = ""; // Contenido del segundo mensaje
    $sendThirdMessage = false; // Controlar si se debe enviar un tercero mensaje
    $thirdMessage = ""; // Contenido del tercero mensaje

    // Conectar a la base de datos
    $conexion = mysqli_connect($host, $username, $password) or die ("Error en la conexión.");

    if ($conexion) {
        mysqli_select_db($conexion, $dbname) or die ("ERROR db");

        // Consulta SQL para obtener el valor de verificado_oficial
        $query = 'SELECT * FROM oficiales WHERE telefono_oficial = "'.$numOficial.'" ';
        $resul = mysqli_query($conexion, $query) or die("Error query: " . mysqli_error($conexion));

        // Inicializar $result como null
        $result = null;

        // Obtener el resultado de la consulta
        if ($tupla = mysqli_fetch_array($resul)) {
            $result = $tupla['verificado_oficial'];
            $id_oficial = $tupla['id_oficial'];
        }

        // Cerrar conexión
        mysqli_close($conexion);
    }

    if (is_null($result)) {
        $responseMessage = "🚫 No se encontró el número de teléfono en la base de datos. Por favor, intente nuevamente.".$toBd;
    } else {
        if($result === "1"){
            $ver = true;
        }else{
            $ver = false;
        }
        if (!$ver) {
            if ($body === 'si') {
                $responseMessage = "📋 Por favor, *copie el siguiente formato*, *complete la información requerida* y *envíalo* como mensaje para continuar. ¡Gracias! 😊";
                $secondMessage = "Teléfono: \nUnidad: ";
                $sendSecondMessage = true;
                $_SESSION[$toBd]['awaiting_unit_input'] = true; // Cambiar estado para esperar el mensaje con "Unidad:"
            } elseif ($body === 'no') {
                $responseMessage = "Entendido. Si desea confirmar su identidad, por favor envíe un mensaje nuevamente. 😊";
            } else {
                if($awaitingUnitInput){
                    // Paso 1: Eliminar saltos de línea u otros caracteres innecesarios
                    $body = str_replace(["\n", "\r"], " ", $body); // Reemplaza los saltos de línea con un espacio

                    // Paso 2: Buscar la posición de la palabra "Unidad:"
                    $unidadPos = strpos(strtolower($body), 'unidad:'); // Convertimos todo a minúsculas para evitar errores de mayúsculas/minúsculas
                    if($body === 'cancelar'){
                        $responseMessage = "👌 Entendido. Si decide confirmar su identidad más tarde, solo envíe un mensaje nuevamente. ¡Estamos aquí para ayudarte! 😊";
                        $_SESSION[$toBd]['awaiting_unit_input'] = false; 
                    }else{
                        // Paso 3: Si se encuentra la palabra "unidad:" en el mensaje
                        if ($unidadPos !== false) {
                            // Extraer el texto que sigue a "unidad:", ignorando espacios adicionales
                            $unitData = trim(substr($body, $unidadPos + strlen('unidad:'))); // Extraemos todo después de "unidad:"

                            // Verificar si hay contenido después de "unidad:"
                            if (!empty($unitData)) {
                                // Aquí se haría la verificación en la base de datos
                                $responseMessage = "✨ Gracias. Estamos en proceso de verificar sus datos. Por favor, espere un momento mientras completamos la validación. ⏳";
                                //$_SESSION[$toBd]['awaiting_unit_input'] = false; 
                                
                                /**Chequeo de datos para hacer verificación*/
                                // Conectar a la base de datos
                                $conexion = mysqli_connect($host, $username, $password) or die ("Error en la conexión.");

                                if ($conexion) {
                                    mysqli_select_db($conexion, $dbname) or die ("ERROR db");

                                    // Consulta SQL para obtener el valor de verificado_oficial
                                    $query = 'SELECT * FROM oficiales WHERE telefono_oficial = "'.$numOficial.'" AND unidad_oficial = '.$unitData.'';
                                    $resul = mysqli_query($conexion, $query) or die("Error query: " . mysqli_error($conexion));

                                    // Obtener el resultado de la consulta
                                    if ($tupla = mysqli_fetch_array($resul)) {
                                        $sendSecondMessage = true;
                                        $secondMessage = "✅ ¡Verificación exitosa! 🎉 Sus datos han sido confirmados *correctamente*. Gracias por su paciencia. 😊";
                                        $sendThirdMessage = true;
                                        $thirdMessage = "📌 *Instrucciones de uso*:\n- 🆘 *Ayuda*: Obtén información sobre cómo usar este bot.\n- 🔄 *Inicio*: Inicia una nueva consulta.\n- ❌ *Cancelar*: Cancela su consulta si aún no ha sido asignada a un analista.\n⚠️ *Nota*: Solo puedes iniciar una nueva consulta una vez que la actual haya sido resuelta por un analista. 😊";
                                        /**Subida de datos a la BD */
                                        $conexion = mysqli_connect($host, $username, $password) or die ("Error en la conexión.");
                                        $query = 'UPDATE oficiales SET verificado_oficial = 1 WHERE telefono_oficial = "'.$numOficial.'" ';
                                        mysqli_select_db($conexion, $dbname) or die("ERROR en base de datos");
                                        mysqli_query($conexion, $query);
                                    }else{
                                        $responseMessage = "⚠️ Por favor, verifique que los datos proporcionados sean correctos e inténtelo nuevamente. \n\nSi desea cancelar la verificación, envíe un mensaje con la palabra *cancelar*. 😊";
                                    }
                                    // Cerrar conexión
                                    mysqli_close($conexion);
                                }
                            }else {
                                $responseMessage = "⚠️ Por favor, verifique que los datos proporcionados sean correctos e inténtelo nuevamente. \n\nSi desea cancelar la verificación, envíe un mensaje con la palabra *cancelar*. 😊";
                            }
                        } else {
                            $responseMessage = "🚫 No pudimos encontrar todos los datos necesarios en su mensaje. Por favor, revíselo y vuelva a intentarlo. \n\nSi desea cancelar la verificación, simplemente envíe un mensaje con la palabra *cancelar*. 😊";
                        }
                    }
                }else {
                    $responseMessage = "⚠️ *Atención:* Su identidad aún no ha sido verificada. ¿Le gustaría intentarlo ahora?\n\n" . 
                    "Por favor, responda con la palabra *sí* o *no* para continuar. ✅❌";
                }
            }
        }else{
            // Conectar a la base de datos para obtener el teléfono del oficial
            $conexion = mysqli_connect($host, $username, $password, $dbname) or die("Error en la conexión.");

            $query = "SELECT estado FROM solicitudes WHERE referenciaS = '".$referenciaSol."'";
            $result = mysqli_query($conexion, $query) or die("Error en la consulta: " . mysqli_error($conexion));
            $data = mysqli_fetch_array($result);
            $estado = $data['estado'];
            
            if($estado === '5'){
                $_SESSION[$toBd]['awaiting_unit_input'] = true; // Cambiar estado
                $_SESSION[$toBd]['consul_activa'] = true; // Cambiar estado
                $consul_activa = true;
                $awaitingUnitInput=true;
            }else if($estado === '3'){
                $_SESSION[$toBd]['awaiting_unit_input'] = false; // Cambiar estado
                $_SESSION[$toBd]['consul_activa'] = false; // Cambiar estado
                $_SESSION[$toBd]['referenciaS'] = null;
                $consul_activa = false;
                $awaitingUnitInput=false;

            }else{
                $consul_activa = $_SESSION[$toBd]['consul_activa']; //Verifica si hay una consulta en curso //Sera cambiado por la variable de la BD
            }
            // Cerrar la conexión
            mysqli_close($conexion);

            $lines = explode("\n", $body);
            // Verificar si la primera línea contiene "Seguimiento Personas" o "Seguimiento Vehículo"
            $firstLine = strtolower(trim($lines[0])); // Convertir a minúsculas y eliminar espacios

            if ($firstLine === strtolower("Seguimiento Personas")) {
                // Normalizar texto: reemplazar espacios no estándar y eliminar caracteres innecesarios
                $body = preg_replace('/\x{00A0}+/u', ' ', $body); // Reemplaza espacios no separación (\u00A0) con espacios regulares
                $body = trim($body); // Elimina espacios en blanco adicionales al inicio y final

                // Expresiones regulares para capturar los nuevos campos
                $patternUbicacion = '/^Ubicación actual:\s*(.*?)\s*(?:\n|$)/im';
                $patternColonia = '/^Colonia actual:\s*(.*?)\s*(?:\n|$)/im';
                $patternSector = '/^Sector:\s*(.*?)\s*(?:\n|$)/im';
                $patternEdad = '/^Edad:\s*(.*?)\s*(?:\n|$)/im';
                $patternNacionalidad = '/^Nacionalidad:\s*(.*?)\s*(?:\n|$)/im';
                $patternDomicilio = '/^Domicilio detenido:\s*(.*?)\s*(?:\n|$)/im';

                // Variables para almacenar los valores extraídos
                $ubicacionActual = $coloniaActual = $sector = $edad = $nacionalidad = $domicilioDetenido = null;

                // Ejecutar las expresiones regulares para cada campo
                if (preg_match($patternUbicacion, $body, $matches)) {
                    $ubicacionActual = trim($matches[1]);
                }
                if (preg_match($patternColonia, $body, $matches)) {
                    $coloniaActual = trim($matches[1]);
                }
                if (preg_match($patternSector, $body, $matches)) {
                    $sector = trim($matches[1]);
                }
                if (preg_match($patternEdad, $body, $matches)) {
                    $edad = trim($matches[1]);
                }
                if (preg_match($patternNacionalidad, $body, $matches)) {
                    $nacionalidad = trim($matches[1]);
                }
                if (preg_match($patternDomicilio, $body, $matches)) {
                    $domicilioDetenido = trim($matches[1]);
                }

                // Validar campos vacíos o malformados
                $missingFields = [];

                // Función para verificar si un campo contiene palabras clave de otros campos
                function containsInvalidValue($value) {
                    $invalidKeywords = ['Ubicación actual:', 'Colonia actual:', 'Sector:', 'Edad:', 'Nacionalidad:', 'Domicilio detenido:'];
                    foreach ($invalidKeywords as $keyword) {
                        if (stripos($value, $keyword) !== false) {
                            return true;
                        }
                    }
                    return false;
                }

                // Validar cada campo
                if (empty($ubicacionActual) || ctype_space($ubicacionActual) || containsInvalidValue($ubicacionActual)) $missingFields[] = "*Ubicación actual*";
                if (empty($coloniaActual) || ctype_space($coloniaActual) || containsInvalidValue($coloniaActual)) $missingFields[] = "*Colonia actual*";
                if (empty($sector) || ctype_space($sector) || containsInvalidValue($sector)) $missingFields[] = "*Sector*";
                if (empty($edad) || ctype_space($edad) || containsInvalidValue($edad)) $missingFields[] = "*Edad*";
                if (empty($nacionalidad) || ctype_space($nacionalidad) || containsInvalidValue($nacionalidad)) $missingFields[] = "*Nacionalidad*";
                if (empty($domicilioDetenido) || ctype_space($domicilioDetenido) || containsInvalidValue($domicilioDetenido)) $missingFields[] = "*Domicilio detenido*";

                // Generar respuesta según los campos validados
                if (!empty($missingFields)) {
                    // Mensaje de error indicando los campos faltantes o malformados
                    $responseMessage = "❌ *Error: Información incompleta o malformada.*\n" . 
                        "Por favor, asegúrese de completar correctamente los siguientes campos:\n" . 
                        implode("\n", $missingFields) . "\n\n" . 
                        "Corrija el formulario y envíelo nuevamente.";
                } else {
                    /**
                     * Aquí es donde se sube todo a la base de datos
                     */

                    $fechaHoy = date('Y-m-d');
                    $conexion = mysqli_connect($host, $username, $password, $dbname) or die("Error de conexión: " . mysqli_connect_error());
                    //Se actualiza el estado de la solicitud
                    $query = 'UPDATE solicitudes SET estado = 3 WHERE referenciaS = '.$referenciaSol.'';
                    mysqli_query($conexion, $query);
                    $query = "INSERT INTO seguimientopersonas (referenciaP, id_oficial, fecha, ubicacion, colonia, sector, edad_detenido, nacionalidad_detenido, domicilio_detenido) 
                    VALUES ('".$referenciaSol."', '".$id_oficial."', '".$fechaHoy."', '".$ubicacionActual."', '".$coloniaActual."', '".$sector."', '".$edad."', '".$nacionalidad."', '".$domicilioDetenido."')";
                    mysqli_query($conexion, $query);
                    mysqli_close($conexion);

                    // Generar el mensaje de verificación con todos los datos
                    $responseMessage = "✅ *Verificación de datos:*\n" . 
                        "- 📍 *Ubicación actual:* $ubicacionActual\n" . 
                        "- 🏘 *Colonia actual:* $coloniaActual\n" . 
                        "- 📌 *Sector:* $sector\n" . 
                        "- 🎂 *Edad:* $edad\n" . 
                        "- 🌎 *Nacionalidad:* $nacionalidad\n" . 
                        "- 🏠 *Domicilio detenido:* $domicilioDetenido\n\n";
                    $sendSecondMessage = true;
                    $secondMessage = "¡Gracias por la información ingresada! 🙌\n" . 
                        "Su mensaje ha sido recibido con éxito. El seguimiento sera realizado, puede realizar una nueva consulta mandando un mensaje con la palabra *Inicio*. ⏳\n" . 
                        "¡Le agradecemos por su paciencia!";
                    $_SESSION[$toBd]['awaiting_unit_input'] = false; // Cambiar estado    
                    $_SESSION[$toBd]['consul_activa'] = false; // Cambiar estado
                    $_SESSION[$toBd]['referenciaS'] = null;
                }
            }elseif ($firstLine === strtolower("Seguimiento Vehículo")){
                // Normalizar texto: reemplazar espacios no estándar y eliminar caracteres innecesarios
                $body = preg_replace('/\x{00A0}+/u', ' ', $body); // Reemplaza espacios no separación (\u00A0) con espacios regulares
                $body = trim($body); // Elimina espacios en blanco adicionales al inicio y final

                // Expresiones regulares para capturar los nuevos campos
                $patternUbicacion = '/^Ubicación actual:\s*(.*?)\s*(?:\n|$)/im';
                $patternColonia = '/^Colonia actual:\s*(.*?)\s*(?:\n|$)/im';
                $patternSector = '/^Sector:\s*(.*?)\s*(?:\n|$)/im';
                $patternCaractVehiculo = '/^Características Vehículo:\s*(.*?)\s*(?:\n|$)/im';
                $patternCondicionesVehiculo = '/^Condiciones Vehículo:\s*(.*?)\s*(?:\n|$)/im';
                $patternNombreConductor = '/^Nombre conductor:\s*(.*?)\s*(?:\n|$)/im';

                // Variables para almacenar los valores extraídos
                $ubicacionActual = $coloniaActual = $sector = $caracteristicasVehiculo = $condicionesVehiculo = $nombreConductor = null;

                // Ejecutar las expresiones regulares para cada campo
                if (preg_match($patternUbicacion, $body, $matches)) {
                    $ubicacionActual = trim($matches[1]);
                }
                if (preg_match($patternColonia, $body, $matches)) {
                    $coloniaActual = trim($matches[1]);
                }
                if (preg_match($patternSector, $body, $matches)) {
                    $sector = trim($matches[1]);
                }
                if (preg_match($patternCaractVehiculo, $body, $matches)) {
                    $caracteristicasVehiculo = trim($matches[1]);
                }
                if (preg_match($patternCondicionesVehiculo, $body, $matches)) {
                    $condicionesVehiculo = trim($matches[1]);
                }
                if (preg_match($patternNombreConductor, $body, $matches)) {
                    $nombreConductor = trim($matches[1]);
                }

                // Validar campos vacíos o malformados
                $missingFields = [];

                // Función para verificar si un campo contiene palabras clave de otros campos
                function containsInvalidValue($value) {
                    $invalidKeywords = ['Ubicación actual:', 'Colonia actual:', 'Sector:', 'Características Vehículo:', 'Condiciones Vehículo:', 'Nombre conductor:'];
                    foreach ($invalidKeywords as $keyword) {
                        if (stripos($value, $keyword) !== false) {
                            return true;
                        }
                    }
                    return false;
                }

                // Validar cada campo
                if (empty($ubicacionActual) || ctype_space($ubicacionActual) || containsInvalidValue($ubicacionActual)) $missingFields[] = "*Ubicación actual*";
                if (empty($coloniaActual) || ctype_space($coloniaActual) || containsInvalidValue($coloniaActual)) $missingFields[] = "*Colonia actual*";
                if (empty($sector) || ctype_space($sector) || containsInvalidValue($sector)) $missingFields[] = "*Sector*";
                if (empty($caracteristicasVehiculo) || ctype_space($caracteristicasVehiculo) || containsInvalidValue($caracteristicasVehiculo)) $missingFields[] = "*Características Vehículo*";
                if (empty($condicionesVehiculo) || ctype_space($condicionesVehiculo) || containsInvalidValue($condicionesVehiculo)) $missingFields[] = "*Condiciones Vehículo*";
                if (empty($nombreConductor) || ctype_space($nombreConductor) || containsInvalidValue($nombreConductor)) $missingFields[] = "*Nombre conductor*";

                // Generar respuesta según los campos validados
                if (!empty($missingFields)) {
                    // Mensaje de error indicando los campos faltantes o malformados
                    $responseMessage = "❌ *Error: Información incompleta o malformada.*\n" . 
                        "Por favor, asegúrese de completar correctamente los siguientes campos:\n" . 
                        implode("\n", $missingFields) . "\n\n" . 
                        "Corrija el formulario y envíelo nuevamente. 😊";
                } else {
                    /**
                     * Aquí es donde se sube todo a la base de datos
                     */
                    // Conexión a la base de datos
                    $fechaHoy = date('Y-m-d');
                    $conexion = mysqli_connect($host, $username, $password, $dbname) or die("Error de conexión: " . mysqli_connect_error());
                    //Se actualiza el estado de la solicitud
                    $query = 'UPDATE solicitudes SET estado = 3 WHERE referenciaS = '.$referenciaSol.'';
                    mysqli_query($conexion, $query);
                    $query = "INSERT INTO seguimientovehiculos (referenciaV, id_oficial, fecha, ubicacion, colonia, sector, caracteristicasV, condicionesV, nombre_conductor) 
                    VALUES (" . intval($referenciaSol) . ", " . intval($id_oficial) . ", '" . mysqli_real_escape_string($conexion, $fechaHoy) . "', '" . mysqli_real_escape_string($conexion, $ubicacionActual) . "', '" . mysqli_real_escape_string($conexion, $coloniaActual) . "', '" . mysqli_real_escape_string($conexion, $sector) . "', '" . mysqli_real_escape_string($conexion, $caracteristicasVehiculo) . "', '" . mysqli_real_escape_string($conexion, $condicionesVehiculo) . "', '" . mysqli_real_escape_string($conexion, $nombreConductor) . "')";
                    mysqli_query($conexion, $query);
                    // Consulta SQL preparada para evitar inyecciones SQL
                    mysqli_close($conexion);

                    // Generar el mensaje de verificación con todos los datos
                    $responseMessage = "✅ *Verificación de datos:*\n" . 
                        "- 📍 *Ubicación actual:* $ubicacionActual\n" . 
                        "- 🏘 *Colonia actual:* $coloniaActual\n" . 
                        "- 📌 *Sector:* $sector\n" . 
                        "- 🚗 *Características del vehículo:* $caracteristicasVehiculo\n" . 
                        "- 🔧 *Condiciones del vehículo:* $condicionesVehiculo\n" . 
                        "- 👤 *Nombre del conductor:* $nombreConductor\n\n";
                    $sendSecondMessage = true;
                    $secondMessage = "¡Gracias por la información ingresada! 🙌\n" . 
                        "Su mensaje ha sido recibido con éxito. El seguimiento sera realizado, puede realizar una nueva consulta mandando un mensaje con la palabra *Inicio*. ⏳\n" . 
                        "¡Le agradecemos por su paciencia!";
                    $_SESSION[$toBd]['awaiting_unit_input'] = false; // Cambiar estado
                    $_SESSION[$toBd]['consul_activa'] = false; // Cambiar estado
                    $_SESSION[$toBd]['referenciaS'] = null;
                }
            }else{
                // Mensaje cuando ya está verificado
                if($consul_activa){
                    $responseMessage = "🚨 ¡Atención! 🚨\n" .
                        "Actualmente tienes una consulta activa. 🕒 Por favor, espera a que sea resuelta antes de iniciar una nueva. 😊\n" .
                        "¡Gracias por tu comprensión y paciencia!";
                }else{
                    if ($body === 'inicio' && !$awaitingUnitInput) {
                        $responseMessage = "¡Hola! 😁 ¿En que puedo ayudarte?";
                        $sendSecondMessage = true;
                        $secondMessage = "Para realizar una consulta referente a un vehiculo mande un mensaje con la palabra: \n🚗 *Vehiculo*";
                        $sendThirdMessage = true;
                        $thirdMessage = "Para realizar una consulta referente a una persona mande un mensaje con la palabra: \n👨👩 *Persona*";
                    } elseif (($body === 'vehículo' || $body === 'vehiculo') && /*!$_SESSION[$toBd]['awaiting_unit_input']*/!$awaitingUnitInput){
                        // Si selecciona "vehiculo"
                        $_SESSION[$toBd]['tipo_consulta'] = 'vehiculo'; // Guardar tipo de consulta
                        $_SESSION[$toBd]['awaiting_unit_input'] = true; // Cambiar estado para esperar el mensaje con "Unidad:"
                        $responseMessage = "Por favor, *copie y llene* el siguiente formato con los datos del vehículo:\n" .
                        "Una vez completado, *envíalo como mensaje*. 😊";            
                        $sendSecondMessage = true;
                        $secondMessage = "Motivo:\nNo. Serie:\nPlaca:\nNombre sospechoso:";
                        $sendThirdMessage = true;
                        $thirdMessage = "🔔 *Recordatorio importante:* 🔔\n" .
                        "Si cometio un error o desea cancelar la consulta en cualquier momento, solo envíe la palabra *cancelar* y lo haremos por usted. ❌😊";

                        /**
                         * Aqui se crea la referencia de la consulta
                         * se guarda el telefono del oficial 
                         * y se pone el estado de la solicitud (Enviada, en proceso, resuelta, seguimiento)
                         * 
                         * */ 
                        /**
                         * motivo_consulta
                         * no_serie
                         * placa
                         */
                    } elseif ($body === 'persona') {
                        // Si selecciona "persona"
                        $_SESSION[$toBd]['tipo_consulta'] = 'persona'; // Guardar tipo de consulta
                        $_SESSION[$toBd]['awaiting_unit_input'] = true; // Cambiar estado para esperar el mensaje 
                        $responseMessage = "Por favor, *copie y llene* el siguiente formato con los datos del sospechoso:\n" .
                        "Una vez completado, *envíalo como mensaje*. 😊\n".
                        "*Importante:* La fecha de nacimiento debe tener el formato *DD/MM/AAAA*";
                        $sendSecondMessage = true;
                        $secondMessage = "Motivo:\nNombre:\nApellido Paterno:\nApellido Materno:\nFecha de nacimiento:";
                        $sendThirdMessage = true;
                        $thirdMessage = "🔔 *Recordatorio importante:* 🔔\n" .
                        "Si cometio un error o desea cancelar la consulta en cualquier momento, solo envíe la palabra *cancelar* y lo haremos por usted. ❌😊";
                        /**
                         * Aqui se crea la referencia de la consulta
                         * se guarda el telefono del oficial 
                         * y se pone el estado de la solicitud (Enviada, en proceso, resuelta, seguimiento)
                         * 
                         * */ 
                        /**
                         * motivo_consulta
                         * nombre_sospechoso
                         * ap_sospechoso
                         * am_sospechoso
                         * fecha_Nacimiento_Sospechoso
                         */
                    } else if($body === 'ayuda'){
                        $responseMessage = "👋 ¡Hola! Aquí tienes cómo usar este bot:\n\n" .
                        "- ✨ *Ayuda*: Obtén instrucciones para usar el bot.\n" .
                        "- 🚀 *Inicio*: Comienza una nueva consulta.\n" .
                        "- ❌ *Cancelar*: Cancela tu consulta (solo si aún no está asignada a un analista).\n\n" .
                        "📝 *¿Cómo funciona?*\n" .
                        "1. Escribe *Inicio* para comenzar.\n" .
                        "2. Elige entre:\n" .
                        "   - 🚗 *Vehículo*\n" .
                        "   - 👤 *Persona*\n" .
                        "3. Recibirás un formulario. *Cópialo, complétalo y envíalo como mensaje*.\n\n" .
                        "📩 Tu consulta será revisada por un analista.\n" .
                        "- Si se encuentra algo, te indicaremos los pasos a seguir.\n" .
                        "- Si no se encuentra nada, tendrás que llenar un nuevo formulario para dar seguimiento.\n\n" .
                        "⚠️ *Nota*: Solo podrás iniciar una nueva consulta después de que la actual sea resuelta. 😊";

                    }else if($awaitingUnitInput) {
                        if($body === 'cancelar' && !$consul_activa){
                            $responseMessage = "Entendido. Si desea realizar una consulta, envíe un mensaje nuevamente. 😊";
                            $_SESSION[$toBd]['awaiting_unit_input'] = false; // Restablecer estado
                        }else if($tipoConsulta === 'vehiculo'){
                            // Normalizar texto: reemplazar espacios no estándar y eliminar caracteres innecesarios
                            $body = preg_replace('/\x{00A0}+/u', ' ', $body); // Reemplaza espacios no separación (\u00A0) con espacios regulares
                            $body = trim($body); // Elimina espacios en blanco adicionales al inicio y final

                            // Expresiones regulares para capturar cada campo
                            $patternMotivo = '/^Motivo:\s*(.*?)\s*(?:\n|$)/im';
                            $patternNoSerie = '/^No\. Serie:\s*(.*?)\s*(?:\n|$)/im';
                            $patternPlaca = '/^Placa:\s*(.*?)\s*(?:\n|$)/im';
                            $patternNombreSospechoso = '/^Nombre sospechoso:\s*(.*?)\s*(?:\n|$)/im'; // Nueva expresión para "Nombre sospechoso"

                            // Variables para almacenar los valores extraídos
                            $motivo = $noSerie = $placa = $nombreSospechoso = null;

                            // Ejecutar las expresiones regulares para cada campo
                            if (preg_match($patternMotivo, $body, $matches)) {
                                $motivo = trim($matches[1]);
                            }

                            if (preg_match($patternNoSerie, $body, $matches)) {
                                $noSerie = trim($matches[1]);
                            }

                            if (preg_match($patternPlaca, $body, $matches)) {
                                $placa = trim($matches[1]);
                            }

                            if (preg_match($patternNombreSospechoso, $body, $matches)) { // Capturar "Nombre sospechoso"
                                $nombreSospechoso = trim($matches[1]);
                            }

                            // Validar campos vacíos o malformados
                            $missingFields = [];

                            // Función para verificar si un campo contiene palabras clave de otros campos
                            function containsInvalidValue($value) {
                                $invalidKeywords = ['Motivo:', 'No. Serie:', 'Placa:', 'Nombre sospechoso:'];
                                foreach ($invalidKeywords as $keyword) {
                                    if (stripos($value, $keyword) !== false) {
                                        return true;
                                    }
                                }
                                return false;
                            }

                            // Validar cada campo
                            if (empty($motivo) || ctype_space($motivo) || containsInvalidValue($motivo)) $missingFields[] = "*Motivo*";
                            if (empty($noSerie) || ctype_space($noSerie) || containsInvalidValue($noSerie)) $missingFields[] = "*No. Serie*";
                            if (empty($placa) || ctype_space($placa) || containsInvalidValue($placa)) $missingFields[] = "*Placa*";
                            if (empty($nombreSospechoso) || ctype_space($nombreSospechoso) || containsInvalidValue($nombreSospechoso)) $missingFields[] = "*Nombre sospechoso*"; // Validar nuevo campo

                            // Generar respuesta según los campos validados
                            if (!empty($missingFields)) {
                                // Mensaje de error indicando los campos faltantes o malformados
                                $responseMessage = "❌ *Error: Información incompleta o malformada.*\n" .
                                    "Por favor, asegúrese de completar correctamente los siguientes campos:\n" .
                                    implode("\n", $missingFields) . "\n\n" .
                                    "Corrija el formulario y envíelo nuevamente. 😊\n\nRecuerde que puede cancelar su consulta enviando un mensaje con la palabra *cancelar*";
                            } else {
                                // Conexión a la base de datos
                                $conexion = mysqli_connect($host, $username, $password, $dbname) or die("Error de conexión: " . mysqli_connect_error());
                                // Insertar los datos en la tabla 'solicitudes'
                                $queryInsert = "INSERT INTO solicitudes (mensaje, telefono_oficial, estado) 
                                VALUES ('', '".$numOficial."', 1)";

                                if(mysqli_query($conexion, $queryInsert)){
                                    // Obtener el valor de referenciaS (llave primaria)
                                    $referenciaS = mysqli_insert_id($conexion);
                                    $query = "INSERT INTO consultavehiculos (id_oficial, referenciaS, motivo_consulta, no_serie, placa, nom_sospechoso) 
                                    VALUES ('".$id_oficial."', '".$referenciaS."', '".$motivo."', '".$noSerie."', '".$placa."', '".$nombreSospechoso."')";
                                    mysqli_query($conexion, $query);

                                    // Generar el mensaje de verificación con todos los datos
                                    $responseMessage = "✅ *Verificación de datos:*\n" .
                                    "- 📋 *Motivo:* $motivo\n" .
                                    "- 🏷 *No. Serie:* $noSerie\n" .
                                    "- 🏷 *Placa:* $placa\n" .
                                    "- 👤 *Nombre sospechoso:* $nombreSospechoso\n\n";
                                    $sendSecondMessage = true;
                                    $secondMessage = "¡Gracias por tu consulta! 🙌\n" . 
                                        "Su mensaje ha sido recibido con éxito. Estamos procesando su solicitud y en breve recibirá los resultados. ⏳\n" . 
                                        "¡Le agradecemos por su paciencia!";
                                    $_SESSION[$toBd]['consul_activa'] = true; // Cambiar estado 
                                    $_SESSION[$toBd]['awaiting_unit_input'] =  false;
                                    $_SESSION[$toBd]['referenciaS'] = $referenciaS;
                                }else{
                                    $referenciaS = mysqli_insert_id($conexion);
                                    $responseMessage = "No se pudo realizar la subida de datos";
                                }
                                mysqli_close($conexion);  
                            }
                        }else if ($tipoConsulta === 'persona') {
                            //$body = "Motivo: Sospechoso raro\nNombre: Angel\nApellido Paterno: Hurtado\nApellido Materno: Salcedo\nFecha de nacimiento: 20/10/2001";
                            // Normalizar texto: reemplazar espacios no estándar y eliminar caracteres innecesarios
                            $body = preg_replace('/\x{00A0}+/u', ' ', $body); // Reemplaza espacios de no separación (\u00A0) con espacios regulares
                            $body = trim($body); // Elimina espacios en blanco adicionales al inicio y final

                            // Expresiones regulares para capturar cada campo
                            $patternMotivo = '/^Motivo:\s*(.*?)\s*(?:\n|$)/im';
                            $patternNombre = '/^Nombre:\s*(.*?)\s*(?:\n|$)/im';
                            $patternApellidoPaterno = '/^Apellido Paterno:\s*(.*?)\s*(?:\n|$)/im';
                            $patternApellidoMaterno = '/^Apellido Materno:\s*(.*?)\s*(?:\n|$)/im';
                            $patternFechaNacimiento = '/^Fecha de nacimiento:\s*(.*?)\s*(?:\n|$)/im';

                            // Variables para almacenar los valores extraídos
                            $motivo = $nombre = $apellidoPaterno = $apellidoMaterno = $fechaNacimiento = null;

                            // Ejecutar las expresiones regulares para cada campo
                            if (preg_match($patternMotivo, $body, $matches)) {
                                $motivo = trim($matches[1]);
                            }

                            if (preg_match($patternNombre, $body, $matches)) {
                                $nombre = trim($matches[1]);
                            }

                            if (preg_match($patternApellidoPaterno, $body, $matches)) {
                                $apellidoPaterno = trim($matches[1]);
                            }

                            if (preg_match($patternApellidoMaterno, $body, $matches)) {
                                $apellidoMaterno = trim($matches[1]);
                            }

                            if (preg_match($patternFechaNacimiento, $body, $matches)) {
                                $fechaNacimiento = trim($matches[1]);
                            }

                            // Validar campos vacíos o malformados
                            $missingFields = [];

                            // Función para verificar si un campo contiene palabras clave de otros campos
                            function containsInvalidValue($value) {
                                $invalidKeywords = ['Motivo:', 'Nombre:', 'Apellido Paterno:', 'Apellido Materno:', 'Fecha de nacimiento:'];
                                foreach ($invalidKeywords as $keyword) {
                                    if (stripos($value, $keyword) !== false) {
                                        return true;
                                    }
                                }
                                return false;
                            }

                            // Validar cada campo
                            if (empty($motivo) || ctype_space($motivo) || containsInvalidValue($motivo)) $missingFields[] = "*Motivo*";
                            if (empty($nombre) || ctype_space($nombre) || containsInvalidValue($nombre)) $missingFields[] = "*Nombre*";
                            if (empty($apellidoPaterno) || ctype_space($apellidoPaterno) || containsInvalidValue($apellidoPaterno)) $missingFields[] = "*Apellido Paterno*";
                            if (empty($apellidoMaterno) || ctype_space($apellidoMaterno) || containsInvalidValue($apellidoMaterno)) $missingFields[] = "*Apellido Materno*";
                            if (empty($fechaNacimiento) || ctype_space($fechaNacimiento) || containsInvalidValue($fechaNacimiento)) $missingFields[] = "*Fecha de nacimiento*";

                            // Generar respuesta según los campos validados
                            if (!empty($missingFields)) {
                                // Mensaje de error indicando los campos faltantes o malformados
                                $responseMessage = "❌ *Error: Información incompleta o malformada.*\n" .
                                    "Por favor, asegúrese de completar correctamente los siguientes campos:\n" .
                                    implode("\n", $missingFields) . "\n\n" .
                                    "Corrija el formulario y envíelo nuevamente. 😊\n\nRecuerde que puede cancelar su consulta enviando un mensaje con la palabra *cancelar*";
                            } else {
                                /**
                                 * Aqui va toda la subida de datos
                                 */

                                // Conexión a la base de datos
                                $conexion = mysqli_connect($host, $username, $password, $dbname) or die("Error de conexión: " . mysqli_connect_error());
                                // Insertar los datos en la tabla 'solicitudes'
                                $queryInsert = "INSERT INTO solicitudes (mensaje, telefono_oficial, estado) 
                                VALUES ('', '".$numOficial."', 1)";
                        
                                if(mysqli_query($conexion, $queryInsert)){
                                   // Obtener el valor de referenciaS (llave primaria)
                                    $referenciaS = mysqli_insert_id($conexion);
                                    $query = "INSERT INTO consultapersonas (id_oficial, referenciaS, motivo_consulta, nombre_sospechoso, ap_sospechoso, am_sospechoso, fechaNacimiento_sospechoso)
                                    VALUES ('" . $id_oficial . "', '" . $referenciaS . "', '" . $motivo . "', '" . $nombre . "', '" . $apellidoPaterno . "', '" . $apellidoMaterno . "', '" . $fechaNacimiento . "')";
                                    mysqli_query($conexion, $query);

                                    // Generar el mensaje de verificación con todos los datos
                                    $responseMessage = "✅ *Verificación de datos:*\n" .
                                    "- 📋 *Motivo:* $motivo\n" .
                                    "- 👤 *Nombre:* $nombre\n" .
                                    "- 🏷 *Apellido Paterno:* $apellidoPaterno\n" .
                                    "- 🏷 *Apellido Materno:* $apellidoMaterno\n" .
                                    "- 📅 *Fecha de nacimiento:* $fechaNacimiento\n\n";
                                    $sendSecondMessage = true;
                                    $secondMessage = "¡Gracias por su consulta! 🙌\n" . 
                                    "Su mensaje ha sido recibido con éxito. Estamos procesando su solicitud y en breve recibirá los resultados. ⏳\n" . 
                                    "¡Le agradecemos por su paciencia!";
                                    $_SESSION[$toBd]['consul_activa'] = true; // Cambiar estado
                                    $_SESSION[$toBd]['awaiting_unit_input'] =  false;
                                    $_SESSION[$toBd]['referenciaS'] = $referenciaS;
                                }else{
                                    $referenciaS = mysqli_insert_id($conexion);
                                    $responseMessage = "No se pudo realizar la subida de datos";
                                }
                                mysqli_close($conexion);   
                            }
                        }             
                    } else {
                        $responseMessage = "¡Ups! 😅 No pude entender su mensaje. No se preocupes, estamos aquí para ayudarte. 🤖 Por favor, intente enviarlo de nuevo o escriba *Ayuda* para obtener instrucciones sobre cómo utilizar el chatbot. ¡Gracias por su paciencia! 🙌";
                    }
                }      
            }
            
        }
    }  

    try {
        // Enviar el primer mensaje
        $twilio->messages->create(
            $from,
            [
                'from' => $twilioPhoneNumber,
                'body' => $responseMessage
            ]
        );

        // Enviar el segundo mensaje solo si es necesario
        if ($sendSecondMessage) {
            $twilio->messages->create(
                $from,
                [
                    'from' => $twilioPhoneNumber,
                    'body' => $secondMessage
                ]
            );
        }

        if ($sendThirdMessage) {
            $twilio->messages->create(
                $from,
                [
                    'from' => $twilioPhoneNumber,
                    'body' => $thirdMessage
                ]
            );
        }
        error_log("Mensaje(s) enviado(s) correctamente a $from");
    } catch (Exception $e) {
        error_log("Error al enviar el mensaje: " . $e->getMessage());
    }
} else {
    error_log("Solicitud no es POST");
}
?>
