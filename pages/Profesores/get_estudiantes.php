<?php
require '../../db_config.php';

// Verificar que se haya recibido el ID de la clase
if (isset($_GET['claseID'])) {
    $claseID = $_GET['claseID'];

    // Obtener los estudiantes para la clase especificada
    $sql = "SELECT E.EstudianteID, P.Nombre
            FROM Estudiantes E
            JOIN Personas P ON E.PersonaID = P.PersonaID
            JOIN Estudiante_Clase EC ON E.EstudianteID = EC.EstudianteID
            WHERE EC.ClaseID = ?";
    $params = array($claseID);
    $stmt = sqlsrv_prepare($conn_sis, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        $estudiantes = [];
        while ($estudiante = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $estudiantes[] = $estudiante;
        }
        echo json_encode($estudiantes);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>
