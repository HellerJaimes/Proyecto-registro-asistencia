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
$sql = '';
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar el formulario de agregar aula
    $nombreAula = filter_input(INPUT_POST, 'nombreAula', FILTER_SANITIZE_STRING);
    $capacidad = filter_input(INPUT_POST, 'capacidad', FILTER_SANITIZE_NUMBER_INT);
    $ubicacion = filter_input(INPUT_POST, 'ubicacion', FILTER_SANITIZE_STRING);

    // Insertar en la tabla Aulas
    $sql = "INSERT INTO Aulas (NombreAula, Capacidad, Ubicacion) VALUES (?, ?, ?)";
    $params = array($nombreAula, $capacidad, $ubicacion);

    $stmt = sqlsrv_prepare($conn_sis, $sql, $params);
    if ($stmt && sqlsrv_execute($stmt)) {
        $message = "Aula agregada exitosamente.";
    } else {
        $message = "Error al agregar el registro en Aulas: " . print_r(sqlsrv_errors(), true);
    }

    sqlsrv_free_stmt($stmt);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212; /* Fondo negro */
            color: #e4e7eb; /* Texto en gris claro */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #000; /* Barra de navegación negra */
        }
        .navbar-brand {
            color: #e4e7eb;
            font-weight: bold;
        }
        .navbar-nav .nav-link {
            color: #b5b9c4;
        }
        .navbar-nav .nav-link:hover {
            color: #0069d9;
        }
        .card {
            border-radius: 12px;
            border: none;
            background: linear-gradient(145deg, #212121, #2a2a2a); /* Fondo oscuro para las tarjetas */
        }
        .card-header {
            background-color: #000;
            color: #fff;
            font-weight: bold;
            border-bottom: 2px solid #444;
        }
        .alert {
            border-radius: 8px;
            font-size: 14px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            background-color: #1d1d1d; /* Fondo de inputs oscuro */
            color: #fff;
            border: 1px solid #333; /* Borde gris oscuro */
        }
        .form-control:focus, .form-select:focus {
            border-color: #0069d9; /* Borde azul claro al enfocar */
            background-color: #2c2c2c;
        }
        .btn-primary {
            background-color: #0069d9;
            border-color: #0062cc;
            border-radius: 8px;
            font-weight: bold;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .btn-secondary {
            background-color: #444c56;
            border-color: #5b636e;
            border-radius: 8px;
        }
        .btn-secondary:hover {
            background-color: #3b434b;
            border-color: #4a5258;
        }
        .form-label {
            font-size: 0.9rem;
            color: #b5b9c4;
        }
        .container {
            padding-top: 50px;
        }
        .form-section {
            margin-bottom: 25px;
        }
        .form-section .form-label {
            font-size: 1rem;
            color: #c5c8d0;
        }
        .form-section .form-control {
            background-color: #333;
            color: #fff;
        }
        .form-section .form-select {
            background-color: #333;
            color: #fff;
        }
        .card-body {
            padding: 30px;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .form-column {
            flex: 1;
            min-width: 45%;
        }
        .btn-container {
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Sistema de Registro de Asistencias</span>
            <div class="text-end mb-1">
            <a href="registro_opciones.php" class="btn btn-primary">Volver Atrás</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header text-center">
                        <h5>Agregar Aula</h5>
                    </div>
                    <div class="card-body">
                        <!-- Mensaje de confirmación -->
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario para agregar aula -->
                        <form method="post">
                            <div class="form-section">
                                <label for="nombreAula" class="form-label">Nombre del Aula</label>
                                <input type="text" id="nombreAula" name="nombreAula" class="form-control" required>
                            </div>

                            <div class="form-section">
                                <label for="capacidad" class="form-label">Capacidad</label>
                                <input type="number" id="capacidad" name="capacidad" class="form-control" min="1" required>
                           </div>

                            <div class="form-section">
                                <label for="ubicacion" class="form-label">Ubicación</label>
                                <input type="text" id="ubicacion" name="ubicacion" class="form-control" required>
                            </div>

                            <div class="btn-container">
                                <button type="submit" class="btn btn-primary">Agregar Aula</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
