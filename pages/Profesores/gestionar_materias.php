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

// Obtener los cursos que tiene asignados este profesor
$sql_cursos = "SELECT c.CursoID, c.NombreCurso
               FROM Clases cl
               INNER JOIN Cursos c ON cl.CursoID = c.CursoID
               WHERE cl.ProfesorID = ?";
$params_cursos = array($profesorID);
$stmt_cursos = sqlsrv_prepare($conn_sis, $sql_cursos, $params_cursos);

if ($stmt_cursos && sqlsrv_execute($stmt_cursos)) {
    $cursos = array();
    while ($curso = sqlsrv_fetch_array($stmt_cursos, SQLSRV_FETCH_ASSOC)) {
        $cursos[] = $curso;
    }
} else {
    die(print_r(sqlsrv_errors(), true));
}

// Manejo del formulario para asignar estudiantes a los cursos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cursoID']) && isset($_POST['accion'])) {
        // Obtener el curso y acción seleccionada
        $cursoID = $_POST['cursoID'];
        $accion = $_POST['accion'];

        // Paso 1: Obtener los estudiantes disponibles dependiendo de la acción seleccionada
        if ($accion == 'agregar') {
            // Estudiantes que NO están registrados en esta clase
            $sql_estudiantes = "SELECT e.EstudianteID, p.Nombre
                                FROM Estudiantes e
                                JOIN Personas p ON e.PersonaID = p.PersonaID
                                WHERE e.EstudianteID NOT IN (
                                    SELECT ec.EstudianteID
                                    FROM Estudiante_Clase ec
                                    INNER JOIN Clases c ON ec.ClaseID = c.ClaseID
                                    WHERE c.CursoID = ?
                                )";
            $stmt_estudiantes = sqlsrv_prepare($conn_sis, $sql_estudiantes, array($cursoID));

            if ($stmt_estudiantes && sqlsrv_execute($stmt_estudiantes)) {
                $estudiantes = array();
                while ($estudiante = sqlsrv_fetch_array($stmt_estudiantes, SQLSRV_FETCH_ASSOC)) {
                    $estudiantes[] = $estudiante;
                }
            } else {
                die(print_r(sqlsrv_errors(), true));
            }

        } elseif ($accion == 'eliminar') {
            // Estudiantes que YA ESTÁN registrados en esta clase
            $sql_estudiantes = "SELECT e.EstudianteID, p.Nombre
                                FROM Estudiantes e
                                JOIN Personas p ON e.PersonaID = p.PersonaID
                                WHERE e.EstudianteID IN (
                                    SELECT ec.EstudianteID
                                    FROM Estudiante_Clase ec
                                    INNER JOIN Clases c ON ec.ClaseID = c.ClaseID
                                    WHERE c.CursoID = ?
                                )";
            $stmt_estudiantes = sqlsrv_prepare($conn_sis, $sql_estudiantes, array($cursoID));

            if ($stmt_estudiantes && sqlsrv_execute($stmt_estudiantes)) {
                $estudiantes = array();
                while ($estudiante = sqlsrv_fetch_array($stmt_estudiantes, SQLSRV_FETCH_ASSOC)) {
                    $estudiantes[] = $estudiante;
                }
            } else {
                die(print_r(sqlsrv_errors(), true));
            }
        }

        // Paso 2: Agregar o eliminar estudiantes en el curso dependiendo de la acción
        if ($accion == 'agregar' && isset($_POST['estudiantes_agregar'])) {
            $estudiantes_agregar = $_POST['estudiantes_agregar'];

            foreach ($estudiantes_agregar as $estudianteID) {
                // Agregar estudiante a la clase (Estudiante_Clase)
                $sql_insert = "INSERT INTO Estudiante_Clase (EstudianteID, ClaseID)
                               SELECT ?, ClaseID
                               FROM Clases
                               WHERE CursoID = ?";
                $stmt_insert = sqlsrv_prepare($conn_sis, $sql_insert, array($estudianteID, $cursoID));
                sqlsrv_execute($stmt_insert);
            }

            echo "Estudiantes agregados correctamente al curso.";
        } elseif ($accion == 'eliminar' && isset($_POST['estudiantes_eliminar'])) {
            $estudiantes_eliminar = $_POST['estudiantes_eliminar'];

            foreach ($estudiantes_eliminar as $estudianteID) {
                // Eliminar estudiante de la clase (Estudiante_Clase)
                $sql_delete = "DELETE FROM Estudiante_Clase
                               WHERE EstudianteID = ? AND ClaseID IN (SELECT ClaseID FROM Clases WHERE CursoID = ?)";
                $stmt_delete = sqlsrv_prepare($conn_sis, $sql_delete, array($estudianteID, $cursoID));
                sqlsrv_execute($stmt_delete);
            }

            echo "Estudiantes eliminados correctamente del curso.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Estudiantes en Cursos</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Personalizado -->
    <style>
        body {
            background-color: #343a40;
            color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        .form-select {
            background-color: #495057;
            border: 1px solid #6c757d;
            color: #f8f9fa;
        }
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(38, 143, 255, 0.5);
        }
        h1, h3 {
            color: #ffffff;
        }
        button {
            margin-top: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .table {
            background-color: #495057;
            border-radius: 5px;
        }
        .table th, .table td {
            color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestionar Estudiantes en Cursos</h1>

        <!-- Formulario para seleccionar curso y acción (Agregar o Eliminar) -->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="curso" class="form-label">Seleccionar Curso:</label>
                <select name="cursoID" id="curso" class="form-select" required>
                    <option value="">Seleccione un curso</option>
                    <?php foreach ($cursos as $curso) { ?>
                        <option value="<?php echo $curso['CursoID']; ?>"><?php echo $curso['NombreCurso']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="accion" class="form-label">Acción:</label>
                <select name="accion" id="accion" class="form-select" required>
                    <option value="agregar">Agregar Estudiantes</option>
                    <option value="eliminar">Eliminar Estudiantes</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Seleccionar Curso y Acción</button>
        </form>

        <?php if (isset($estudiantes)) { ?>
            <!-- Tabla para agregar o eliminar estudiantes -->
            <h3>Estudiantes disponibles para el curso</h3>
            <?php if ($_POST['accion'] == 'agregar') { ?>
                <form method="POST">
                    <input type="hidden" name="cursoID" value="<?php echo $cursoID; ?>">
                    <input type="hidden" name="accion" value="agregar">

                    <div class="mb-3">
                        <label for="estudiantes_agregar" class="form-label">Seleccionar Estudiantes para agregar:</label>
                        <select name="estudiantes_agregar[]" id="estudiantes_agregar" class="form-select" multiple required>
                            <?php foreach ($estudiantes as $estudiante) { ?>
                                <option value="<?php echo $estudiante['EstudianteID']; ?>"><?php echo $estudiante['Nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">Agregar Estudiantes</button>
                </form>
            <?php } elseif ($_POST['accion'] == 'eliminar') { ?>
                <form method="POST">
                    <input type="hidden" name="cursoID" value="<?php echo $cursoID; ?>">
                    <input type="hidden" name="accion" value="eliminar">

                    <div class="mb-3">
                        <label for="estudiantes_eliminar" class="form-label">Seleccionar Estudiantes para eliminar:</label>
                        <select name="estudiantes_eliminar[]" id="estudiantes_eliminar" class="form-select" multiple required>
                            <?php foreach ($estudiantes as $estudiante) { ?>
                                <option value="<?php echo $estudiante['EstudianteID']; ?>"><?php echo $estudiante['Nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-danger">Eliminar Estudiantes</button>
                </form>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="text-center mb-4">
            <a href="PaginaPrincipalProfesores.php" class="btn btn-primary">Volver Atrás</a>
        </div>                           
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
