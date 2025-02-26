<?php
include("ConWEB.php");

$Nombre = "";
$Celular = "";
$Correo = "";
$Folio = "";
$Mensaje = "";

if(isset($_POST['Operacion'])) $Operacion = $_POST['Operacion'];
if(isset($_POST['FolioCotizacion'])) $FolioCotizacion = $_POST['FolioCotizacion'];
if(isset($_POST['Nombre'])) $Nombre = $_POST['Nombre'];
if(isset($_POST['Celular'])) $Celular = $_POST['Celular'];
if(isset($_POST['Correo'])) $Correo = $_POST['Correo'];
if(isset($_POST['Folio'])) $Folio = $_POST['Folio'];
if(isset($_POST['Mensaje'])) $Mensaje = $_POST['Mensaje'];

if($Operacion == 'BuscarDatosAutomaticos'){

    $Nombre = '';
    $Numero = '';

    $Query = "SELECT TOP 1 
    T1.[Nombre], T1.[Numero]
    FROM [WEB].[DBO].[CotizadorExpress] T0
    INNER JOIN [WEB].[DBO].[WhatsappBot] T1 ON T0.[Cliente] = T1.[Numero]
    WHERE [IDCOT] = '$FolioCotizacion'";

    $Result = sqlsrv_query($Conexion, $Query);

    if($Result === false) {
        if(($errors = sqlsrv_errors()) != null) {
            foreach($errors as $error) {
                echo "SQLSTATE: ".$error['SQLSTATE']."<br />";
                echo "code: ".$error['code']."<br />";
                echo "message: ".$error['message']."<br />";
            }
        }
    }

    while ($Row = sqlsrv_fetch_array($Result, SQLSRV_FETCH_ASSOC)) {
        $Nombre = $Row['Nombre'];
        $Numero = $Row['Numero'];
    }

    echo $Nombre . "|" . $Numero . "|" . $FolioCotizacion;
}

if($Operacion == 'InsertarDatos'){
    $Query = "INSERT INTO [WEB].[dbo].[CitasRW] 
    ([Nombre], [Celular], [Correo], [Cotizacion], [Mensaje]) 
    VALUES ('$Nombre', '$Celular', '$Correo', '$Folio', '$Mensaje')";

    $Result = sqlsrv_query($Conexion, $Query);

    if($Result === false) {
        if(($errors = sqlsrv_errors()) != null) {
            foreach($errors as $error) {
                echo "SQLSTATE: ".$error['SQLSTATE']."<br />";
                echo "code: ".$error['code']."<br />";
                echo "message: ".$error['message']."<br />";
            }
        }
    } else {
        echo "Exito";
    }
}


?>