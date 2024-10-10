<?php

require '../../db_config.php';  // Incluir el archivo de configuración de la base de datos


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username-login'], $_POST['password-login'])) {
    // Validar y limpiar los datos del formulario
    $username = filter_input(INPUT_POST, 'username-login', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password-login', FILTER_SANITIZE_STRING);

    $sql = "SELECT password FROM Estudiantes WHERE username = ?";
    $params = array($username);
    $stmt = sqlsrv_prepare($conn_sis, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        if (sqlsrv_has_rows($stmt)) {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $hashedPassword = $row['password'];

            if (password_verify($password, $hashedPassword)) {
                session_start();
                $_SESSION['username'] = $username;
                header("Location: PaginaPrincipal.php");
                exit();
            } else {
                header("Location: index.php?error=incorrect_password");
                exit();
            }
        } else {
            header("Location: index.php?error=user_not_found");
            exit();
        }
    } else {
        die(print_r(sqlsrv_errors(), true));  // Error en la consulta
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn_sis);
}
?>
