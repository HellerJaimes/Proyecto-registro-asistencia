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
    // Obtener y sanitizar los datos
    $nombreCurso = filter_input(INPUT_POST, 'nombreCurso', FILTER_SANITIZE_STRING);
    $creditos = filter_input(INPUT_POST, 'creditos', FILTER_SANITIZE_NUMBER_INT);
    $programaID = filter_input(INPUT_POST, 'programaID', FILTER_SANITIZE_NUMBER_INT);

    // Validar que los créditos estén en el rango de 1 a 10
    if ($creditos < 1 || $creditos > 10) {
        $message = "Los créditos deben estar entre 1 y 10.";
    } else {
        // Insertar el curso en la base de datos
        $sql = "INSERT INTO Cursos (NombreCurso, Creditos, ProgramaID) VALUES (?, ?, ?)";
        $params = array($nombreCurso, $creditos, $programaID);

        $stmt = sqlsrv_prepare($conn_sis, $sql, $params);
        if ($stmt && sqlsrv_execute($stmt)) {
            $message = "Curso registrado exitosamente.";
        } else {
            $message = "Error al registrar el curso: " . print_r(sqlsrv_errors(), true);
        }

        sqlsrv_free_stmt($stmt);
    }
}

// Obtener los programas disponibles para llenar el select
$sqlProgramas = "SELECT ProgramaID, NombrePrograma FROM Programas"; // Asegúrate de que esta tabla exista.
$stmtProgramas = sqlsrv_query($conn_sis, $sqlProgramas);

// Verificar si la consulta fue exitosa
if ($stmtProgramas === false) {
    die("Error en la consulta de programas: " . print_r(sqlsrv_errors(), true));
}

$programas = [];
while ($row = sqlsrv_fetch_array($stmtProgramas, SQLSRV_FETCH_ASSOC)) {
    $programas[] = $row;
}

sqlsrv_free_stmt($stmtProgramas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Curso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Agregar estilos personalizados si es necesario */
    </style>
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
        <h2 class="text-center">Registrar Curso</h2>

        <!-- Mostrar mensaje si hay uno -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Formulario para registrar un nuevo curso -->
        <form action="superadmin_dashboardCursos.php" method="POST">
            <div class="form-section mb-3">
                <label for="nombreCurso" class="form-label">Nombre del Curso</label>
                <input type="text" id="nombreCurso" name="nombreCurso" class="form-control" required>
            </div>

            <div class="form-section mb-3">
                <label for="creditos" class="form-label">Créditos</label>
                <input type="number" id="creditos" name="creditos" class="form-control" min="1" max="10" required>
            </div>

            <div class="form-section mb-3">
                <label for="programa" class="form-label">Programa</label>
                <select id="programa" name="programaID" class="form-control" required>
                    <option value="" disabled selected>Seleccionar programa</option>
                    <?php foreach ($programas as $programa): ?>
                        <option value="<?php echo $programa['ProgramaID']; ?>"><?php echo $programa['NombrePrograma']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Registrar Curso</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
