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

    $estudiantes = [];
    if ($stmt && sqlsrv_execute($stmt)) {
        while ($estudiante = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $estudiantes[] = $estudiante;
        }
    }

    // Obtener el horario de la clase (usando ClaseID, no CursoID)
    $sql_horario = "SELECT DiaSemana, HoraInicio, HoraFin 
                    FROM Horarios 
                    WHERE HorarioID = (SELECT HorarioID FROM Clases WHERE ClaseID = ?)";
    $stmt_horario = sqlsrv_prepare($conn_sis, $sql_horario, array($claseID));
    $horario = null;

    if ($stmt_horario && sqlsrv_execute($stmt_horario)) {
        $horario = sqlsrv_fetch_array($stmt_horario, SQLSRV_FETCH_ASSOC);
    }

    // Convertir HoraInicio y HoraFin a formato legible (si son tipo time o datetime)
    if ($horario) {
        // Convertir HoraInicio y HoraFin a formato de hora (HH:MM)
        $hora_inicio = $horario['HoraInicio'] ? $horario['HoraInicio']->format('H:i') : '';
        $hora_fin = $horario['HoraFin'] ? $horario['HoraFin']->format('H:i') : '';
        
        $horario['HoraInicio'] = $hora_inicio;
        $horario['HoraFin'] = $hora_fin;
    }

    // Devolver los estudiantes y el horario como JSON
    echo json_encode(['estudiantes' => $estudiantes, 'horario' => $horario]);

} else {
    echo json_encode([]);
}
?>
