<?php
// Conectar a la base de datos
require '../../db_config.php';
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header("Location: login_estudiantes.php"); // Redirigir si no hay sesión
    exit();
}

// Obtener información del estudiante
$username = $_SESSION['username'];
$sql = "SELECT E.*, P.Nombre AS nombre_persona, P.Correo, P.Telefono
        FROM Estudiantes E
        JOIN Personas P ON E.PersonaID = P.PersonaID
        WHERE E.username = ?";
$params = array($username);
$stmt = sqlsrv_prepare($conn_sis, $sql, $params);

if ($stmt && sqlsrv_execute($stmt)) {
    $studentInfo = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
} else {
    die(print_r(sqlsrv_errors(), true));
}

// Obtener el EstudianteID
$estudianteID = $studentInfo['EstudianteID'];
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal - Estudiante</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- FullCalendar JavaScript -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
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
        <h1 class="text-center">Bienvenido, <?php echo htmlspecialchars($studentInfo['nombre_persona']); ?></h1>
        <h3>Detalles del Estudiante</h3>
        <ul class="list-group">
            <li class="list-group-item"><strong>Nombre:</strong> <?php echo htmlspecialchars($studentInfo['nombre_persona']); ?></li>
            <li class="list-group-item"><strong>Username:</strong> <?php echo htmlspecialchars($studentInfo['username']); ?></li>
            <li class="list-group-item"><strong>Correo:</strong> <?php echo htmlspecialchars($studentInfo['Correo']); ?></li>
            <li class="list-group-item"><strong>Teléfono:</strong> <?php echo htmlspecialchars($studentInfo['Telefono']); ?></li>
        </ul>

        <h3 class="mt-4">Clases Inscritas</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Profesor</th>
                    <th>Horario</th>
                    <th>Asistencia</th>
                </tr>
            </thead>
            <tbody>
            <?php
                // Obtener cursos
                $cursosQuery = "SELECT C.CursoID, C.NombreCurso, C.Creditos, P.Nombre AS nombre_profesor, H.DiaSemana, H.HoraInicio, H.HoraFin, CL.ClaseID, CL.AulaID
                FROM Cursos C
                JOIN Clases CL ON C.CursoID = CL.CursoID
                JOIN Profesores PR ON CL.ProfesorID = PR.ProfesorID
                JOIN Personas P ON PR.PersonaID = P.PersonaID
                JOIN Horarios H ON CL.HorarioID = H.HorarioID
                WHERE CL.ClaseID IN (SELECT ClaseID FROM Estudiante_Clase WHERE EstudianteID = ?)";
                $cursosParams = array($estudianteID);
                $cursosStmt = sqlsrv_prepare($conn_sis, $cursosQuery, $cursosParams);

                if ($cursosStmt && sqlsrv_execute($cursosStmt)) {
                    $cursoEncontrado = false;
                    while ($curso = sqlsrv_fetch_array($cursosStmt, SQLSRV_FETCH_ASSOC)) {
                        $cursoEncontrado = true;
                        echo "<tr>
                            <td>" . htmlspecialchars($curso['NombreCurso']) . " (" . htmlspecialchars($curso['Creditos']) . " créditos)</td>
                            <td>" . htmlspecialchars($curso['nombre_profesor']) . "</td>
                            <td>" . htmlspecialchars($curso['DiaSemana']) . " de " . $curso['HoraInicio']->format('H:i') . " a " . $curso['HoraFin']->format('H:i') . "</td>
                            <td>
                                <a href='#' data-claseid='" . $curso['ClaseID'] . "' data-aulaid='" . $curso['AulaID'] . "' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#asistenciaModal'>Registrar Asistencia</a>
                            </td>
                        </tr>";
                    }
                    if (!$cursoEncontrado) {
                        echo "<tr><td colspan='4'>No se encontraron cursos inscritos.</td></tr>";
                    }
                } else {
                    die(print_r(sqlsrv_errors(), true));
                }
            ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para registrar asistencia -->
    <div class="modal fade" id="asistenciaModal" tabindex="-1" aria-labelledby="asistenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="registro_Asistencia.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="asistenciaModalLabel">Registrar Asistencia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tipoAsistencia" class="form-label">Tipo de Asistencia</label>
                            <select class="form-select" id="tipoAsistencia" name="tipoAsistencia" required>
                                <option value="P">Presencial</option>
                                <option value="V">Virtual</option>
                            </select>
                        </div>
                        <input type="hidden" id="claseID" name="claseID">
                        <input type="hidden" id="aulaID" name="aulaID">
                        <input type="hidden" id="estudianteID" name="estudianteID" value="<?php echo $estudianteID; ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Script para manejar el modal de asistencia -->
    <script>
        var asistenciaModal = document.getElementById('asistenciaModal');
        asistenciaModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var claseID = button.getAttribute('data-claseid');
            var aulaID = button.getAttribute('data-aulaid');

            var modalClaseID = asistenciaModal.querySelector('#claseID');
            var modalAulaID = asistenciaModal.querySelector('#aulaID');

            modalClaseID.value = claseID;
            modalAulaID.value = aulaID;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
