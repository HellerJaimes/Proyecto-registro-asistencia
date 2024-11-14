<?php
// Conectar a la base de datos
require '../../db_config.php';
session_start();

// Verificar si el profesor está autenticado
if (!isset($_SESSION['usernameP'])) {
    // Redirigir al login si no está autenticado
    header('Location: login_profesores.php');
    exit();
}

// Obtener información del profesor
$usernameP = $_SESSION['usernameP'];
$sql = "SELECT E.*, P.Nombre AS nombre_persona, P.Correo, P.Telefono
        FROM Profesores E
        JOIN Personas P ON E.PersonaID = P.PersonaID
        WHERE E.usernameP = ?";
$params = array($usernameP);
$stmt = sqlsrv_prepare($conn_sis, $sql, $params);

if ($stmt && sqlsrv_execute($stmt)) {
    $profesorInfo = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
} else {
    die(print_r(sqlsrv_errors(), true));
}

// Obtener el ProfesorID
$profesorID = $profesorInfo['ProfesorID'];
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Profesores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #1e1e1e;
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: #2d2d2d;
            border-bottom: 2px solid #444;
        }

        .navbar-brand {
            color: #fff;
            font-weight: bold;
            font-size: 1.4rem;
        }

        .navbar-brand:hover {
            color: #00bcd4;
        }

        .container {
            max-width: 960px;
        }

        .card {
            background-color: #2c2f38;
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 30px;
        }

        .card-title {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 500;
        }

        .card-text {
            color: #c0c0c0;
        }

        .list-group-item {
            background-color: #383d42;
            border: none;
            color: #f1f1f1;
            font-size: 1.1rem;
        }

        .list-group-item:hover {
            background-color: #007bff;
            color: #fff;
        }

        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
            font-weight: bold;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }

        .list-group {
            margin-top: 20px;
        }

        h1, h3 {
            color: #fff;
            font-weight: 600;
        }

        .section-header {
            margin-top: 50px;
            border-bottom: 2px solid #444;
            padding-bottom: 15px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Sistema de Registro de Asistencias</span>
            <form class="d-flex" action="../logout.php" method="post">
                <button class="btn btn-outline-danger" type="submit">Cerrar sesión</button>
            </form>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Bienvenido, Profesor <?php echo htmlspecialchars($profesorInfo['nombre_persona']); ?></h1>

        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Detalles del Profesor</h3>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Nombre:</strong> <?php echo htmlspecialchars($profesorInfo['nombre_persona']); ?></li>
                    <li class="list-group-item"><strong>Username:</strong> <?php echo htmlspecialchars($usernameP); ?></li>
                    <li class="list-group-item"><strong>Correo:</strong> <?php echo htmlspecialchars($profesorInfo['Correo']); ?></li>
                    <li class="list-group-item"><strong>Teléfono:</strong> <?php echo htmlspecialchars($profesorInfo['Telefono']); ?></li>
                </ul>
            </div>
        </div>

        <div class="section-header">
            <h3>Opciones</h3>
        </div>

        <div class="list-group">
            <a href="ver_clases.php" class="list-group-item list-group-item-action">Ver mis clases</a>
            <a href="registro_Asistencia_Profe.php" class="list-group-item list-group-item-action">Registrar Asistencia</a>
            <a href="gestionar_materias.php" class="list-group-item list-group-item-action">Gestionar Materias</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
