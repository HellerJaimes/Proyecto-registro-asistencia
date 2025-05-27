<?php
session_start();
require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Activar buffer de salida para evitar cualquier texto previo a la redirección
    ob_start();

    // Obtener y limpiar los datos del formulario
    $usernameP = trim(filter_input(INPUT_POST, 'usernameP', FILTER_SANITIZE_STRING));
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
// Consultar solo el usernameP para verificar si el usuario existe
$sql = "SELECT * FROM Profesores WHERE usernameP = ?";
$params = array($usernameP);
$stmt = sqlsrv_prepare($conn_sis, $sql, $params);

if ($stmt && sqlsrv_execute($stmt)) {
    if (sqlsrv_has_rows($stmt)) {
        // Usuario existe, verificar contraseña
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Sanitizar la contraseña de la base de datos
        $dbPassword = trim($row['password']);

        if ($dbPassword === $password) {
            // Iniciar sesión y redirigir
            $_SESSION['usernameP'] = $usernameP; 
            
            header("Location: PaginaPrincipalProfesores.php");
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: login_profesores.php?error=invalid_credentials");
            exit();
        }
    } else {
        // Usuario no encontrado
        header("Location: login_profesores.php?error=user_not_found");
        exit();
    }
} else {
    // Error en la ejecución de la consulta
    die("Error en la consulta: " . print_r(sqlsrv_errors(), true));
}


    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn_sis);
    ob_end_flush(); // Limpiar y desactivar el buffer
} else {
    // Redirigir si no es un método POST
    header("Location: index.php");
    exit();
}
