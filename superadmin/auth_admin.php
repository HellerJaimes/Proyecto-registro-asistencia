<?php
session_start();
require '../db_config.php'; // Asegúrate de que esta ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y limpiar los datos del formulario
    $username = filter_input(INPUT_POST, 'useradmin', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Consulta SQL para verificar el usuario y la contraseña
    $sql = "SELECT * FROM Superadmin WHERE useradmin = ? AND password = ?";
    $params = array($username, $password);
    $stmt = sqlsrv_prepare($conn_sis, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        if (sqlsrv_has_rows($stmt)) {
            // Si se encuentra el usuario, se considera que ha iniciado sesión correctamente
            $_SESSION['superadmin_logged_in'] = true;
            header("Location: superadmin_dashboard.php"); // Cambia a la ruta de tu dashboard
            exit();
        } else {
            // Si las credenciales son incorrectas, redirigir con un mensaje de error
            header("Location: superadmin.php?error=invalid_credentials");
            exit();
        }
    } else {
        die(print_r(sqlsrv_errors(), true)); // Manejar el error de la consulta
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn_sis);
} else {
    // Redirigir si no es un método POST
    header("Location: superadmin_login.php");
    exit();
}
?>
