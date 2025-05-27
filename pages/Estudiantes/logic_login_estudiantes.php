<?php
session_start();
require '../../db_config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y limpiar los datos del formulario
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Primero, consulta solo el username para verificar si el usuario existe
    $sql = "SELECT * FROM Estudiantes WHERE username = ?";
    $params = array($username);
    $stmt = sqlsrv_prepare($conn_sis, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        if (sqlsrv_has_rows($stmt)) {
            // El usuario existe, ahora verifica la contraseña
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            
            if ($row['password'] === $password) {
                // Si la contraseña es correcta
                $_SESSION['username'] = $username; // Guardar el username en la sesión
                header("Location: PaginaPrincipalEstudiante.php");
                exit();
            } else {
                // Contraseña incorrecta
                header("Location: login_estudiantes.php?error=incorrect_password");
                exit();
            }
        } else {
            // Usuario no encontrado
            header("Location: login_estudiantes.php?error=user_not_found");
            exit();
        }
    } else {
        die(print_r(sqlsrv_errors(), true)); // Manejar el error de la consulta
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn_sis);
} else {
    // Redirigir si no es un método POST
    header("Location: index.php");
    exit();
}
