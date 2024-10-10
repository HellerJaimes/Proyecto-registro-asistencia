<?php
// Parámetros de conexión
$serverName = "localhost"; // Nombre del servidor o dirección IP
$connectionOptions = array(
    "Database" => "BD_Registro_Asistencia", // Nombre de la base de datos
);

// Establecer la conexión
$conn = sqlsrv_connect( $serverName, $connectionInfo);

// Verificar si la conexión fue exitosa
if ($conn) {
    echo "Conexión exitosa a la base de datos.";
} else {
    echo "Error en la conexión a la base de datos: ";
    die(print_r(sqlsrv_errors(), true)); // Imprimir errores si los hay
}
