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
$sql = ''; // Aseguramos que $sql esté definido
$params = []; // Aseguramos que $params esté definido

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar el formulario de agregar estudiante o profesor
    $type = $_POST['type']; // Obtener el tipo seleccionado (estudiante o profesor)

    if ($type === 'estudiante') {
        // Obtener datos del estudiante
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $apellido = filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        
        // Consulta para agregar estudiante
        $sql = "INSERT INTO Estudiantes (nombre, apellido, username, password) VALUES (?, ?, ?, ?)";
        $params = array($nombre, $apellido, $username, $password);
        
    } elseif ($type === 'profesor') {
        // Obtener datos del profesor
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $apellido = filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        
        // Consulta para agregar profesor
        $sql = "INSERT INTO Profesores (nombre, apellido, username) VALUES (?, ?, ?)";
        $params = array($nombre, $apellido, $username);
    }

    // Verificar si se definieron las variables antes de ejecutar la consulta
    if ($sql && !empty($params)) {
        // Ejecutar la consulta
        $stmt = sqlsrv_prepare($conn_sis, $sql, $params);
        if ($stmt && sqlsrv_execute($stmt)) {
            $message = "Registro agregado exitosamente.";
        } else {
            $message = "Error al agregar el registro: " . print_r(sqlsrv_errors(), true);
        }

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
</head>
<body>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Navbar -->
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Sistema de Registro de Asistencias</span>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1 class="text-center">Agregar Registro</h1>

        <!-- Mensaje de confirmación -->
        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para agregar estudiantes o profesores -->
        <form method="post" class="mb-3">
            <div class="mb-3">
                <label for="type" class="form-label">Seleccionar Tipo</label>
                <select id="type" name="type" class="form-select" required>
                    <option value="" disabled selected>Seleccione un tipo</option>
                    <option value="estudiante">Estudiante</option>
                    <option value="profesor">Profesor</option>
                </select>
            </div>

            <div id="fields">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" required>
                </div>

                <div class="mb-3" id="username-field">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="mb-3" id="password-field">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Agregar</button>
        </form>
    </div>

    <script>
        const typeSelect = document.getElementById('type');
        const usernameField = document.getElementById('username-field');
        const passwordField = document.getElementById('password-field');

        typeSelect.addEventListener('change', function() {
            const selectedValue = this.value;
            if (selectedValue === 'estudiante') {
                usernameField.style.display = 'block';
                passwordField.style.display = 'block';
            } else if (selectedValue === 'profesor') {
                usernameField.style.display = 'block';
                passwordField.style.display = 'block'; // Ocultar el campo de contraseña
            }
        });
    </script>
</body>
</html>
