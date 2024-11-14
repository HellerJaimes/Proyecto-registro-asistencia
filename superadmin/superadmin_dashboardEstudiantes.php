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
    // Procesar el formulario de agregar estudiante
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $programaID = filter_input(INPUT_POST, 'programa', FILTER_SANITIZE_NUMBER_INT);

    // Insertar en la tabla Personas
    $sql = "INSERT INTO Personas (Nombre, correo, telefono) VALUES (?, ?, ?); SELECT SCOPE_IDENTITY() AS PersonaID;";
    $params = array($nombre, $correo, $telefono);

    $stmt = sqlsrv_prepare($conn_sis, $sql, $params);
    if ($stmt && sqlsrv_execute($stmt)) {
        sqlsrv_next_result($stmt);
        sqlsrv_fetch($stmt);
        $personaID = sqlsrv_get_field($stmt, 0);
    } else {
        $message = "Error al agregar el registro en Personas: " . print_r(sqlsrv_errors(), true);
    }

    // Insertar en la tabla Estudiantes
    if ($personaID) {
        $sql = "INSERT INTO Estudiantes (PersonaID, ProgramaID, username, password) VALUES (?, ?, ?, ?)";
        $params = array($personaID, $programaID, $username, $password);

        $stmt = sqlsrv_prepare($conn_sis, $sql, $params);
        if ($stmt && sqlsrv_execute($stmt)) {
            $message = "Estudiante agregado exitosamente.";
        } else {
            $message = "Error al agregar el registro en Estudiantes: " . print_r(sqlsrv_errors(), true);
        }
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
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header text-center">
                        <h5>Agregar Estudiante</h5>
                    </div>
                    <div class="card-body">
                        <!-- Mensaje de confirmación -->
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario para agregar estudiantes -->
                        <form method="post">
                            <div class="form-section">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required>
                            </div>

                            <div class="form-section">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" id="correo" name="correo" class="form-control" required>
                            </div>

                            <div class="form-section">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" class="form-control" required>
                            </div>

                            <div class="form-row">
                                <div class="form-column">
                                    <div class="form-section">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" id="username" name="username" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-column">
                                    <div class="form-section">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" id="password" name="password" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <label for="programa" class="form-label">Seleccionar Programa</label>
                                <select id="programa" name="programa" class="form-select" required>
                                    <option value="" disabled selected>Seleccione un programa</option>
                                    <option value="21">Ingeniería de Sistemas</option>
                                    <option value="22">Ingeniería Industrial</option>
                                    <option value="23">Administración de Empresas</option>
                                    <option value="24">Contaduría Pública</option>
                                    <option value="25">Ingeniería Electrónica</option>
                                    <option value="26">Psicología</option>
                                    <option value="27">Derecho</option>
                                    <option value="28">Medicina</option>
                                    <option value="29">Arquitectura</option>
                                    <option value="30">Diseño Gráfico</option>
                                </select>
                            </div>

                            <div class="btn-container">
                                <button type="submit" class="btn btn-primary">Agregar Estudiante</button>
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
