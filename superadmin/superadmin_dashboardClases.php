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
// Obtener los cursos y profesores desde la base de datos
$sql_cursos = "SELECT CursoID, NombreCurso FROM Cursos";
$sql_profesores = "SELECT ProfesorID, usernameP FROM Profesores";
$sql_aulas = "SELECT AulaID, NombreAula FROM Aulas";

// Realizamos las consultas para obtener cursos, profesores y aulas
$cursos = sqlsrv_query($conn_sis, $sql_cursos);
$profesores = sqlsrv_query($conn_sis, $sql_profesores);
$aulas = sqlsrv_query($conn_sis, $sql_aulas);

// Lógica para obtener horarios según el curso seleccionado (en caso de que ya haya una selección)
$horarios = [];
if (isset($_POST['cursoID'])) {
    $cursoID = $_POST['cursoID'];
    $sql_horarios = "SELECT HorarioID, DiaSemana, HoraInicio, HoraFin FROM Horarios WHERE CursoID = ?";
    $params = array($cursoID);
    $horarios = sqlsrv_query($conn_sis, $sql_horarios, $params);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registro_clase'])) {
    // Recibir los datos del formulario y guardar en la base de datos
    $cursoID = $_POST['cursoID'];
    $profesorID = $_POST['profesorID'];
    $aulaID = $_POST['aulaID'];
    $horarioID = $_POST['horarioID'];

    $sql_insert = "INSERT INTO Clases (CursoID, ProfesorID, AulaID, HorarioID) 
                   VALUES (?, ?, ?, ?)";
    $params = array($cursoID, $profesorID, $aulaID, $horarioID);
    $result = sqlsrv_query($conn_sis, $sql_insert, $params);

    if ($result) {
        $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Éxito!</strong> La clase ha sido registrada exitosamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    } else {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> Hubo un problema al registrar la clase.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Clases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <h2 class="text-center">Formulario de Registro de Clases</h2>

        <!-- Mostrar el mensaje de éxito o error -->
        <?php if ($message) echo $message; ?>

        <!-- Formulario para registrar la clase -->
        <form action="superadmin_dashboardClases.php" method="post">
            <div class="form-group mb-3">
                <label for="cursoID" class="form-label">Curso</label>
                <select name="cursoID" id="cursoID" class="form-control" required>
                    <option value="">Seleccione un curso</option>
                    <?php while ($row = sqlsrv_fetch_array($cursos, SQLSRV_FETCH_ASSOC)) { ?>
                        <option value="<?php echo $row['CursoID']; ?>"><?php echo htmlspecialchars($row['NombreCurso']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="profesorID" class="form-label">Profesor</label>
                <select name="profesorID" class="form-control" required>
                    <option value="">Seleccione un profesor</option>
                    <?php while ($row = sqlsrv_fetch_array($profesores, SQLSRV_FETCH_ASSOC)) { ?>
                        <option value="<?php echo $row['ProfesorID']; ?>"><?php echo htmlspecialchars($row['usernameP']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="aulaID" class="form-label">Aula</label>
                <select name="aulaID" class="form-control" required>
                    <option value="">Seleccione un aula</option>
                    <?php while ($row = sqlsrv_fetch_array($aulas, SQLSRV_FETCH_ASSOC)) { ?>
                        <option value="<?php echo $row['AulaID']; ?>"><?php echo htmlspecialchars($row['NombreAula']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="horarioID" class="form-label">Horario</label>
                <select name="horarioID" id="horarioID" class="form-control" required>
                    <option value="">Seleccione un horario</option>
                    <!-- Los horarios se cargarán automáticamente según el curso seleccionado -->
                </select>
            </div>

            <button type="submit" name="registro_clase" class="btn btn-primary">Registrar Clase</button>
        </form>
    </div>

    <script>
        // Función AJAX para obtener los horarios de acuerdo al curso seleccionado
        $('#cursoID').change(function() {
            var cursoID = $(this).val();
            if (cursoID) {
                $.ajax({
                    url: 'obtener_horarios.php',  // Archivo PHP que consulta los horarios según el curso
                    type: 'POST',
                    data: {cursoID: cursoID},
                    success: function(response) {
                        $('#horarioID').html(response);
                    }
                });
            } else {
                $('#horarioID').html('<option value="">Seleccione un horario</option>');
            }
        });

        // Asignar el primer horario automáticamente cuando se elige un curso
        $('#cursoID').change(function() {
            var cursoID = $(this).val();
            if (cursoID) {
                $.ajax({
                    url: 'obtener_horarios.php',
                    type: 'POST',
                    data: {cursoID: cursoID},
                    success: function(response) {
                        $('#horarioID').html(response);
                        // Seleccionar el primer horario automáticamente
                        $('#horarioID').prop('selectedIndex', 1);
                    }
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
