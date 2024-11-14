<?php
session_start();
require '../db_config.php'; // Asegúrate de que esta ruta sea correcta

// Verificar si el superadmin está autenticado
if (!isset($_SESSION['superadmin_logged_in'])) {
    header("Location: superadmin.php"); // Redirigir si no está autenticado
    exit();
}

// Inicializar variables
$message = '';
$personaID = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario de agregar profesor
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $usernameP = filter_input(INPUT_POST, 'usernameP', FILTER_SANITIZE_STRING);
    $passwordP = filter_input(INPUT_POST, 'passwordP', FILTER_SANITIZE_STRING);
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $departamentoID = filter_input(INPUT_POST, 'departamento', FILTER_SANITIZE_NUMBER_INT);
    $tipoProfesor = filter_input(INPUT_POST, 'tipoProfesor', FILTER_SANITIZE_STRING);

    // Iniciar transacción para asegurar la integridad
    sqlsrv_begin_transaction($conn_sis);

    try {
        // Insertar en la tabla Personas
        $sql = "INSERT INTO Personas (Nombre, correo, telefono) VALUES (?, ?, ?); SELECT SCOPE_IDENTITY() AS PersonaID;";
        $params = array($nombre, $correo, $telefono);

        $stmt = sqlsrv_prepare($conn_sis, $sql, $params);
        if ($stmt && sqlsrv_execute($stmt)) {
            // Obtener el último PersonaID insertado
            sqlsrv_next_result($stmt);
            sqlsrv_fetch($stmt);
            $personaID = sqlsrv_get_field($stmt, 0);
        } else {
            throw new Exception("Error al agregar el registro en Personas: " . print_r(sqlsrv_errors(), true));
        }

        // Insertar en la tabla Profesores
        if ($personaID) {
            $sql = "INSERT INTO Profesores (PersonaID, DepartamentoID, tipoProfesor, usernameP, passwordP) VALUES (?, ?, ?, ?, ?)";
            $params = array($personaID, $departamentoID, $tipoProfesor, $username, $password);

            $stmt = sqlsrv_prepare($conn_sis, $sql, $params);
            if ($stmt && sqlsrv_execute($stmt)) {
                $message .= "Profesor agregado exitosamente.";
                // Confirmar la transacción
                sqlsrv_commit($conn_sis);
            } else {
                throw new Exception("Error al agregar el registro en Profesores: " . print_r(sqlsrv_errors(), true));
            }
        } else {
            throw new Exception("PersonaID no disponible. No se pudo agregar el profesor.");
        }
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        sqlsrv_rollback($conn_sis);
        $message .= $e->getMessage();
    }

    // Liberar recursos
    if (isset($stmt)) {
        sqlsrv_free_stmt($stmt);
    }
}

// Cerrar conexión
sqlsrv_close($conn_sis);
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Superadministrador</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212;
            color: #f0f0f0;
        }
        .container {
            max-width: 800px;
        }
        .navbar {
            background-color: #000;
        }
        .navbar-brand {
            color: #fff;
        }
        #form-profesor {
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
        }
        .form-control, .form-select {
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert-info {
            background-color: #333;
            color: #aad4ff;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Navbar -->
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Sistema de Registro de Asistencias</span>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center">Agregar Profesor</h1>

        <!-- Mensaje de confirmación -->
        <?php if ($message): ?>
            <div class="alert alert-info" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para agregar profesor -->
        <div id="form-profesor">
            <form method="post">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" id="correo" name="correo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="departamento" class="form-label">Seleccionar Departamento</label>
                    <select id="departamento" name="departamento" class="form-select" required>
                        <option value="1">Departamento de Ciencias Computacionales</option>
                        <option value="2">Departamento de Ingeniería Industrial</option>
                        <option value="3">Departamento de Administración</option>
                        <option value="4">Departamento de Contaduría</option>
                        <option value="5">Departamento de Electrónica y Telecomunicaciones</option>
                        <option value="6">Departamento de Psicología</option>
                        <option value="7">Departamento de Derecho</option>
                        <option value="8">Departamento de Medicina</option>
                        <option value="9">Departamento de Enfermería</option>
                        <option value="10">Departamento de Arquitectura</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tipoProfesor" class="form-label">Tipo de Profesor</label>
                    <select id="tipoProfesor" name="tipoProfesor" class="form-select" required>
                        <option value="T">Titular</option>
                        <option value="A">Adjunto</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="usernameP" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="passwordP" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Agregar Profesor</button>
            </form>
        </div>
    </div>
</body>
</html>
