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
$clasesSql = "SELECT C.CursoID, C.NombreCurso, H.DiaSemana, H.HoraInicio, H.HoraFin, CL.ClaseID, CL.AulaID
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

// Procesar el formulario de registro de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudianteID = $_POST['estudianteID'] ?? null;
    $claseID = $_POST['claseID'] ?? null;
    $fechaAsistencia = $_POST['fechaAsistencia'] ?? date('Y-m-d'); // Fecha actual
    $estado = $_POST['estado'] ?? null;
    $tipoAsistencia = $_POST['tipoAsistencia'] ?? null;

    // Verificar que se haya seleccionado el aula correspondiente a la clase
    $aulaID = null;
    foreach ($clases as $clase) {
        if ($clase['ClaseID'] == $claseID) {
            $aulaID = $clase['AulaID'];
            break;
        }
    }

    // Validar datos
    if (!$estudianteID || !$claseID || !$estado || !$tipoAsistencia || !$aulaID) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Registrar la asistencia
        $insertSql = "INSERT INTO Asistencia (EstudianteID, ClaseID, FechaAsistencia, Estado, TipoAsistencia, AulaID)
                      VALUES (?, ?, ?, ?, ?, ?)";
        $insertParams = array($estudianteID, $claseID, $fechaAsistencia, $estado, $tipoAsistencia, $aulaID);
        $insertStmt = sqlsrv_prepare($conn_sis, $insertSql, $insertParams);

        if ($insertStmt && sqlsrv_execute($insertStmt)) {
            $success = "Asistencia registrada correctamente para el estudiante ID: $estudianteID.";
        } else {
            $error = "Error al registrar la asistencia para el estudiante ID: $estudianteID: " . print_r(sqlsrv_errors(), true);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencia - Profesores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Sistema de Registro de Asistencias</span>
            <form class="d-flex" action="../logout.php" method="post">
                <button class="btn btn-outline-danger" type="submit">Cerrar sesión</button>
            </form>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center">Registrar Asistencia</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="claseID" class="form-label">Clase</label>
                <select class="form-select" name="claseID" id="claseID" required onchange="cargarEstudiantes()">
                    <option value="">Selecciona una clase</option>
                    <?php foreach ($clases as $clase): ?>
                        <option value="<?php echo $clase['ClaseID']; ?>" data-aula="<?php echo $clase['AulaID']; ?>">
                            <?php echo htmlspecialchars($clase['NombreCurso']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="estudiantes-container" class="mb-3"></div>

            <div class="mb-3">
                <label for="fechaAsistencia" class="form-label">Fecha de Asistencia</label>
                <input type="date" class="form-control" name="fechaAsistencia" id="fechaAsistencia" required>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" name="estado" id="estado" required>
                    <option value="">Selecciona un estado</option>
                    <option value="Asistente">Asistente</option>
                    <option value="Ausente">Ausente</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="tipoAsistencia" class="form-label">Tipo de Asistencia</label>
                <select class="form-select" name="tipoAsistencia" id="tipoAsistencia" required>
                    <option value="">Selecciona un tipo de asistencia</option>
                    <option value="P">Presencial</option>
                    <option value="V">Virtual</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Registrar Asistencia</button>
        </form>
    </div>

    <script>
        function cargarEstudiantes() {
            const claseID = document.getElementById('claseID').value;
            const estudiantesContainer = document.getElementById('estudiantes-container');

            // Limpiar el contenedor de estudiantes
            estudiantesContainer.innerHTML = '';

            if (claseID) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'get_estudiantes.php?claseID=' + claseID, true);
                xhr.onload = function() {
                    if (this.status === 200) {
                        const estudiantes = JSON.parse(this.responseText);
                        estudiantes.forEach(estudiante => {
                            estudiantesContainer.innerHTML += `
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="estudianteID" id="estudiante_${estudiante.EstudianteID}" value="${estudiante.EstudianteID}" required>
                                    <label class="form-check-label" for="estudiante_${estudiante.EstudianteID}">
                                        ${estudiante.Nombre}
                                    </label>
                                </div>
                            `;
                        });
                    }
                };
                xhr.send();
            }
        }
    </script>
    <div class="text-center mb-4">
            <a href="PaginaPrincipalProfesores.php" class="btn btn-primary">Volver Atrás</a>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

