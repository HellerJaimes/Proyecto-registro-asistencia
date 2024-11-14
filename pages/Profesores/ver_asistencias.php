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

// Verificar si se ha seleccionado una clase
if (isset($_GET['claseID'])) {
    $claseID = $_GET['claseID'];

    // Obtener los registros de asistencia para la clase seleccionada
    $asistenciasSql = "
        SELECT A.FechaAsistencia, A.Estado, A.TipoAsistencia, P.Nombre
        FROM Asistencia A
        JOIN Estudiantes E ON A.EstudianteID = E.EstudianteID
        JOIN Personas P ON E.PersonaID = P.PersonaID
        WHERE A.ClaseID = ?";
    
    $asistenciasParams = array($claseID);
    $asistenciasStmt = sqlsrv_prepare($conn_sis, $asistenciasSql, $asistenciasParams);

    if ($asistenciasStmt && sqlsrv_execute($asistenciasStmt)) {
        $asistencias = [];
        while ($asistencia = sqlsrv_fetch_array($asistenciasStmt, SQLSRV_FETCH_ASSOC)) {
            $asistencias[] = $asistencia;
        }
    } else {
        die(print_r(sqlsrv_errors(), true));
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Asistencia - Profesores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Sistema de Registro de Asistencias</span>
            <form class="d-flex" action="PaginaPrincipalProfesores.php" method="post">
                <button class="btn btn-outline-danger" type="submit">Atras</button>
            </form>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center">Ver Registros de Asistencia</h1>

        <div class="mb-3">
            <label for="claseID" class="form-label">Selecciona una Clase</label>
            <select class="form-select" id="claseID" onchange="window.location.href='ver_asistencias.php?claseID=' + this.value;">
                <option value="">Selecciona una clase</option>
                <?php foreach ($clases as $clase): ?>
                    <option value="<?php echo $clase['ClaseID']; ?>" <?php echo (isset($claseID) && $clase['ClaseID'] == $claseID) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($clase['NombreCurso']); ?> (<?php echo $clase['DiaSemana']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (isset($asistencias) && !empty($asistencias)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Tipo de Asistencia</th>
                        <th>Estudiante</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asistencias as $asistencia): ?>
                        <tr>
                        <td><?php echo htmlspecialchars($asistencia['FechaAsistencia']->format('Y-m-d H:i:s')); ?></td>

                            <td><?php echo htmlspecialchars($asistencia['Estado']); ?></td>
                            <td><?php echo htmlspecialchars($asistencia['TipoAsistencia']); ?></td>
                            <td><?php echo htmlspecialchars($asistencia['Nombre']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($claseID)): ?>
            <div class="alert alert-info">No hay registros de asistencia para esta clase.</div>
        <?php endif; ?>
    </div>

</body>
</html>
