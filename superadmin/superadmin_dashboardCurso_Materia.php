<?php
session_start();
require '../db_config.php'; // Asegúrate de que esta ruta sea correcta

// Verificar si el superadmin está autenticado
if (!isset($_SESSION['superadmin_logged_in'])) {
    header("Location: superadmin.php"); // Redirigir si no está autenticado
    exit();
}

// Inicializar mensaje de estado
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar los datos
    $cursoID = filter_input(INPUT_POST, 'cursoID', FILTER_SANITIZE_NUMBER_INT);
    $materiaID = filter_input(INPUT_POST, 'materiaID', FILTER_SANITIZE_NUMBER_INT);

    if ($cursoID && $materiaID) {
        // Verificar si la relación ya existe en la tabla Cursos_Materias
        $sqlCheck = "SELECT COUNT(*) AS count FROM Cursos_Materias WHERE CursoID = ? AND MateriaID = ?";
        $paramsCheck = [$cursoID, $materiaID];
        $stmtCheck = sqlsrv_query($conn_sis, $sqlCheck, $paramsCheck);

        if ($stmtCheck === false) {
            $message = "Error al verificar la relación existente: " . print_r(sqlsrv_errors(), true);
        } else {
            $row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
            if ($row['count'] > 0) {
                $message = "La relación entre el curso y la materia ya existe en Cursos_Materias.";
            } else {
                // Intentar insertar la nueva relación
                $sql = "INSERT INTO Cursos_Materias (CursoID, MateriaID) VALUES (?, ?)";
                $params = [$cursoID, $materiaID];
                $stmt = sqlsrv_query($conn_sis, $sql, $params);

                if ($stmt === false) {
                    $message = "Error al registrar la relación: " . print_r(sqlsrv_errors(), true);
                } else {
                    $message = "Relación entre curso y materia registrada exitosamente en Cursos_Materias.";
                }
                sqlsrv_free_stmt($stmt);
            }
            sqlsrv_free_stmt($stmtCheck);
        }
    } else {
        $message = "Por favor, seleccione un curso y una materia válidos.";
    }
}

// Obtener los cursos para llenar el select
$sqlCursos = "SELECT CursoID, NombreCurso FROM Cursos";
$stmtCursos = sqlsrv_query($conn_sis, $sqlCursos);
if ($stmtCursos === false) {
    die("Error en la consulta de cursos: " . print_r(sqlsrv_errors(), true));
}
$cursos = [];
while ($row = sqlsrv_fetch_array($stmtCursos, SQLSRV_FETCH_ASSOC)) {
    $cursos[] = $row;
}
sqlsrv_free_stmt($stmtCursos);

// Obtener las materias para llenar el select
$sqlMaterias = "SELECT MateriaID, NombreMateria FROM Materias";
$stmtMaterias = sqlsrv_query($conn_sis, $sqlMaterias);
if ($stmtMaterias === false) {
    die("Error en la consulta de materias: " . print_r(sqlsrv_errors(), true));
}
$materias = [];
while ($row = sqlsrv_fetch_array($stmtMaterias, SQLSRV_FETCH_ASSOC)) {
    $materias[] = $row;
}
sqlsrv_free_stmt($stmtMaterias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relacionar Cursos y Materias</title>
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
        <h2 class="text-center">Relacionar Cursos y Materias</h2>

        <!-- Mostrar mensaje de estado -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Formulario para registrar la relación -->
        <form action="superadmin_dashboardCurso_Materia.php" method="POST">
            <div class="form-group mb-3">
                <label for="cursoID" class="form-label">Curso</label>
                <select id="cursoID" name="cursoID" class="form-control" required>
                    <option value="" disabled selected>Seleccionar curso</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?php echo $curso['CursoID']; ?>"><?php echo htmlspecialchars($curso['NombreCurso']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="materiaID" class="form-label">Materia</label>
                <select id="materiaID" name="materiaID" class="form-control" required>
                    <option value="" disabled selected>Seleccionar materia</option>
                    <?php foreach ($materias as $materia): ?>
                        <option value="<?php echo $materia['MateriaID']; ?>"><?php echo htmlspecialchars($materia['NombreMateria']); ?></option>
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
