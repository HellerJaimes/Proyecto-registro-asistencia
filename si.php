<?php
if (function_exists('sqlsrv_connect')) {
    echo "La extensión SQLSRV está instalada correctamente.";
} else {
    echo "La extensión SQLSRV no está instalada.";
}
?>