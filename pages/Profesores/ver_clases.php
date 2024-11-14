<?php
// Conectar a la base de datos
require '../../db_config.php';
session_start();

// Verificar si el profesor está autenticado
if (!isset($_SESSION['usernameP'])) {
    header('Location: login_profesores.php'); // Redirigir al login si no está autenticado
    exit();
}

// Obtener información del profesor
$usernameP = $_SESSION['usernameP'];
$sql = "SELECT E.*, P.Nombre AS nombre_persona
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

// Obtener las clases impartidas por el profesor
$clasesSql = "SELECT C.CursoID, C.NombreCurso, H.DiaSemana, H.HoraInicio, H.HoraFin, CL.ClaseID
              FROM Clases CL
              JOIN Cursos C ON CL.CursoID = C.CursoID
              JOIN Horarios H ON CL.HorarioID = H.HorarioID
              WHERE CL.ProfesorID = ?";
$clasesParams = array($profesorID);
$clasesStmt = sqlsrv_prepare($conn_sis, $clasesSql, $clasesParams);

if ($clasesStmt && sqlsrv_execute($clasesStmt)) {
    $clases = [];
    while ($clase = sqlsrv_fetch_array($clasesStmt, SQLSRV_FETCH_ASSOC)) {
        $clases[] = $clase;
    }
} else {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Clases - Panel de Profesores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #343a40; /* Color de fondo oscuro */
            color: #ffffff; /* Color del texto */
        }
        .navbar {
            margin-bottom: 20px; /* Espacio debajo de la barra de navegación */
        }
        .table {
            background-color: #495057; /* Fondo de la tabla */
            color: #ffffff; /* Color del texto de la tabla */
        }
        .table th, .table td {
            vertical-align: middle; /* Centrar contenido en las celdas */
        }
        .btn-primary {
            background-color: #007bff; /* Color del botón Volver Atrás */
            border-color: #007bff; /* Color del borde del botón */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Sistema de Registro de Asistencias</span>
            <form class="d-flex ms-auto" action="../logout.php" method="post">
                <button class="btn btn-outline-danger" type="submit">Cerrar sesión</button>
            </form>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Clases Impartidas por <?php echo htmlspecialchars($profesorInfo["nombre_persona"]); ?></h1>

        <div class="text-center mb-4">
            <a href="PaginaPrincipalProfesores.php" class="btn btn-primary">Volver Atrás</a>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Día</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (count($clases) > 0) {
                foreach ($clases as $clase) {
                    echo "<tr>
                        <td>" . htmlspecialchars($clase['NombreCurso']) . "</td>
                        <td>" . htmlspecialchars($clase['DiaSemana']) . "</td>
                        <td>" . $clase['HoraInicio']->format('H:i') . "</td>
                        <td>" . $clase['HoraFin']->format('H:i') . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No se encontraron clases impartidas.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
