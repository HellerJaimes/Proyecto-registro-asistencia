<?php

    $serverName="DESKTOP-QLPRV86";
    $connectioninfo=array("Database"=>"BD_Registro_Asistencia", "UID"=>"admin2", "PWD"=>"123456", "CharacterSet"=>"UTF-8");
    $conn_sis= sqlsrv_connect($serverName, $connectioninfo);

    if($conn_sis){
        echo"   Conexion exitosa";
    }else{
            echo "Falló la conexion";
            die( print_r(sqlsrv_errors(), true));
    }