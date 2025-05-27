<?php
// Conectar a la base de datos
require '../../db_config.php';
session_start();

$mensaje = ''; // Variable para almacenar el mensaje

// Obtener los datos del formulario
$estudianteID = $_POST['estudianteID'];
$claseID = $_POST['claseID'];
$aulaID = $_POST['aulaID'];
$tipoAsistencia = $_POST['tipoAsistencia'];

// Verificar si la asistencia ya fue registrada
$verificarSql = "SELECT * FROM Asistencia 
                 WHERE EstudianteID = ? AND ClaseID = ? AND CAST(FechaAsistencia AS DATE) = CAST(GETDATE() AS DATE)";
$paramsVerificar = array($estudianteID, $claseID);
$stmtVerificar = sqlsrv_prepare($conn_sis, $verificarSql, $paramsVerificar);

if ($stmtVerificar && sqlsrv_execute($stmtVerificar)) {
    if (sqlsrv_fetch_array($stmtVerificar, SQLSRV_FETCH_ASSOC)) {
        // La asistencia ya fue registrada
        $mensaje = '<div class="alert alert-warning">La asistencia ya ha sido registrada para hoy.</div>';
    } else {
        // Insertar la nueva asistencia
        $sql = "INSERT INTO Asistencia (EstudianteID, ClaseID, AulaID, FechaAsistencia, Estado, tipoAsistencia)
                VALUES (?, ?, ?, GETDATE(), 'Asistente', ?)";
        $params = array($estudianteID, $claseID, $aulaID, $tipoAsistencia);
        $stmt = sqlsrv_prepare($conn_sis, $sql, $params);
        
        if (sqlsrv_execute($stmt)) {
            $mensaje = '<div class="alert alert-success">Asistencia registrada correctamente.</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">Error al registrar la asistencia: ' . print_r(sqlsrv_errors(), true) . '</div>';
        }
    }
} else {
    $mensaje = '<div class="alert alert-danger">Error al verificar la asistencia: ' . print_r(sqlsrv_errors(), true) . '</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Mostrar el mensaje en la interfaz -->
    <div class="container mt-3">
        <?php if (!empty($mensaje)) echo $mensaje; ?>
    </div>

    <div class="container mt-4">
        <h1>Registro de Asistencia</h1>
        <a href="PaginaPrincipalEstudiante.php" class="btn btn-primary">Volver</a>
    </div>
</body>
</html>
