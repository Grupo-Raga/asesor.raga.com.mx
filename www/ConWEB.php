<?php 
    $serverName = "192.168.2.102";
    $connectionInfo = array("DATABASE" => "DATABASE", "UID" => "USER", "PWD" => "PASSWORD", "CharacterSet" => "UTF-8");
    $Conexion = sqlsrv_connect($serverName, $connectionInfo);
    //echo $conn;
    if ($Conexion) {
        //echo "Conexión establecida.<br />";
    } else {
        echo "Conexión no se pudo establecer.<br />";
        die(print_r(sqlsrv_errors(), true));
    }
?>