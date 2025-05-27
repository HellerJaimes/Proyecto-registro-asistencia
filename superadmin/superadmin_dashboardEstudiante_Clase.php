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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar datos
    $estudianteID = filter_input(INPUT_POST, 'estudianteID', FILTER_SANITIZE_NUMBER_INT);
    $claseID = filter_input(INPUT_POST, 'claseID', FILTER_SANITIZE_NUMBER_INT);

    if ($estudianteID && $claseID) {
        // Verificar si la relación ya existe
        $sqlCheck = "SELECT COUNT(*) AS count FROM Estudiante_Clase WHERE EstudianteID = ? AND ClaseID = ?";
        $paramsCheck = array($estudianteID, $claseID);
        $stmtCheck = sqlsrv_query($conn_sis, $sqlCheck, $paramsCheck);

        if ($stmtCheck === false) {
            $message = "Error al verificar la relación existente: " . print_r(sqlsrv_errors(), true);
        } else {
            $row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
            if ($row['count'] > 0) {
                $message = "La relación entre el estudiante y la clase ya existe.";
            } else {
                // Insertar nueva relación
                $sql = "INSERT INTO Estudiante_Clase (EstudianteID, ClaseID) VALUES (?, ?)";
                $params = array($estudianteID, $claseID);
                $stmt = sqlsrv_prepare($conn_sis, $sql, $params);

                if ($stmt === false) {
                    $message = "Error al preparar la consulta: " . print_r(sqlsrv_errors(), true);
                } else {
                    if (sqlsrv_execute($stmt) === false) {
                        $message = "Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true);
                    } else {
                        $message = "Relación registrada exitosamente.";
                    }
                }
                sqlsrv_free_stmt($stmt);
            }
            sqlsrv_free_stmt($stmtCheck);
        }
    } else {
        $message = "Por favor, seleccione un estudiante y una clase válidos.";
    }
}

// Obtener estudiantes para el select
$sqlEstudiantes = "SELECT EstudianteID, username FROM Estudiantes";
$stmtEstudiantes = sqlsrv_query($conn_sis, $sqlEstudiantes);
if ($stmtEstudiantes === false) {
    die("Error en la consulta de estudiantes: " . print_r(sqlsrv_errors(), true));
}
$estudiantes = [];
while ($row = sqlsrv_fetch_array($stmtEstudiantes, SQLSRV_FETCH_ASSOC)) {
    $estudiantes[] = $row;
}
sqlsrv_free_stmt($stmtEstudiantes);

// Obtener clases con nombres de cursos y horarios
$sqlClases = "
    SELECT c.ClaseID, cu.NombreCurso, h.DiaSemana, h.HoraInicio, h.HoraFin
    FROM Clases c
    JOIN Cursos cu ON c.CursoID = cu.CursoID
    JOIN Horarios h ON c.HorarioID = h.HorarioID
";
$stmtClases = sqlsrv_query($conn_sis, $sqlClases);
if ($stmtClases === false) {
    die("Error en la consulta de clases: " . print_r(sqlsrv_errors(), true));
}
$clases = [];
while ($row = sqlsrv_fetch_array($stmtClases, SQLSRV_FETCH_ASSOC)) {
    // Convertir HoraInicio y HoraFin a string si son objetos DateTime
    $horaInicio = $row['HoraInicio'] instanceof DateTime ? $row['HoraInicio']->format('H:i') : $row['HoraInicio'];
    $horaFin = $row['HoraFin'] instanceof DateTime ? $row['HoraFin']->format('H:i') : $row['HoraFin'];

    $clases[] = [
        'ClaseID' => $row['ClaseID'],
        'NombreCurso' => $row['NombreCurso'],
        'DiaSemana' => $row['DiaSemana'],
        'HoraInicio' => $horaInicio,
        'HoraFin' => $horaFin
    ];
}
sqlsrv_free_stmt($stmtClases);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Relación Estudiante-Clase</title>
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
        <h2 class="text-center">Registrar Relación Estudiante - Clase</h2>

        <!-- Mensaje de estado -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Formulario -->
        <form action="superadmin_dashboardEstudiante_Clase.php" method="POST">
            <div class="form-group mb-3">
                <label for="estudianteID" class="form-label">Estudiante</label>
                <select id="estudianteID" name="estudianteID" class="form-control" required>
                    <option value="" disabled selected>Seleccionar estudiante</option>
                    <?php foreach ($estudiantes as $estudiante): ?>
                        <option value="<?php echo $estudiante['EstudianteID']; ?>"><?php echo $estudiante['username']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="claseID" class="form-label">Clase</label>
                <select id="claseID" name="claseID" class="form-control" required>
                    <option value="" disabled selected>Seleccionar clase</option>
                    <?php foreach ($clases as $clase): ?>
                        <option value="<?php echo $clase['ClaseID']; ?>">
                            <?php echo $clase['NombreCurso'] . " - " . $clase['DiaSemana'] . " de " . $clase['HoraInicio'] . " a " . $clase['HoraFin']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Registrar Relación</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
