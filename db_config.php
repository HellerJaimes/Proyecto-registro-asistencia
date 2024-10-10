<?php
// Configuración de la conexión a SQL Server con Autenticación de Windows
$serverName = "DESKTOP-QLPRV86";
$connectioninfo = array(
    "Database" => "BD_Registro_Asistencia",
    "UID" => "admin2",
    "PWD" => "123456",
    "CharacterSet" => "UTF-8"
);

$conn_sis = sqlsrv_connect($serverName, $connectioninfo);

if ($conn_sis === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
