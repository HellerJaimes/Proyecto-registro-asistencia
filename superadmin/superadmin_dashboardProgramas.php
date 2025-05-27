<?php
session_start();
require '../db_config.php'; // Asegúrate de que esta ruta sea correcta

// Verificar si el superadmin está autenticado
if (!isset($_SESSION['superadmin_logged_in'])) {
    header("Location: superadmin.php"); // Redirigir si no está autenticado
    exit();
}

// Inicializar mensaje
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar datos
    $nombrePrograma = filter_input(INPUT_POST, 'nombrePrograma', FILTER_SANITIZE_STRING);
    $duracion = filter_input(INPUT_POST, 'duracion', FILTER_SANITIZE_NUMBER_INT);
    $facultad = filter_input(INPUT_POST, 'facultad', FILTER_SANITIZE_STRING);

    if ($nombrePrograma && $duracion && $facultad) {
        // Insertar nuevo programa
        $sql = "INSERT INTO Programas (NombrePrograma, Duracion, Facultad) VALUES (?, ?, ?)";
        $params = array($nombrePrograma, $duracion, $facultad);
        $stmt = sqlsrv_prepare($conn_sis, $sql, $params);

        if ($stmt === false) {
            $message = "Error al preparar la consulta: " . print_r(sqlsrv_errors(), true);
        } else {
            if (sqlsrv_execute($stmt) === false) {
                $message = "Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true);
            } else {
                $message = "Programa registrado exitosamente.";
            }
        }
        sqlsrv_free_stmt($stmt);
    } else {
        $message = "Por favor, complete todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Programa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Sistema de Registro de Asistencias</span>
            <div class="text-end mb-1">
            <a href="registro_opciones.php" class="btn btn-primary">Volver Atrás</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Registrar Nuevo Programa</h2>

        <!-- Mensaje de estado -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Formulario -->
        <form action="superadmin_dashboardProgramas.php" method="POST">
            <div class="form-group mb-3">
                <label for="nombrePrograma" class="form-label">Nombre del Programa</label>
                <input type="text" id="nombrePrograma" name="nombrePrograma" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="duracion" class="form-label">Duración (en años)</label>
                <input type="number" id="duracion" name="duracion" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="facultad" class="form-label">Facultad</label>
                <input type="text" id="facultad" name="facultad" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Registrar Programa</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
