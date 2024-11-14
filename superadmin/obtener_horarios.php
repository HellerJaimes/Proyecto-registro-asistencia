<?php
require '../db_config.php';

if (isset($_POST['cursoID'])) {
    $cursoID = $_POST['cursoID'];
    $sql_horarios = "SELECT HorarioID, DiaSemana, HoraInicio, HoraFin FROM Horarios WHERE CursoID = ?";
    $params = array($cursoID);
    $result = sqlsrv_query($conn_sis, $sql_horarios, $params);
    
    if ($result) {
        echo "<option value=''>Seleccione un horario</option>";
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $diaSemana = htmlspecialchars($row['DiaSemana']);
            $horaInicio = $row['HoraInicio']->format('H:i');
            $horaFin = $row['HoraFin']->format('H:i');
            echo "<option value='{$row['HorarioID']}'>$diaSemana - $horaInicio a $horaFin</option>";
        }
    } else {
        echo "<option value=''>No hay horarios disponibles</option>";
    }
}
?>
