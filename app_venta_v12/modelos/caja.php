<?php

function agregarcaja($monto, $operacion, $salida, $idusuario){
	
$fecha=date("Y-m-d");
$monto=round($monto, 2);	
	
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
		
if ($operacion == 'SUMA' && $salida == 'ENTRADA') {
        $sqlca = "UPDATE cajas SET monto=monto+'$monto' WHERE idlocal='$_COOKIE[idlocal]' AND idempresa='$_COOKIE[id]' AND estado='1' AND beta='$fa[tipo]' ";
    } else if ($operacion == 'RESTA' && $salida == 'ENTRADA') {
        $sqlca = "UPDATE cajas SET monto=monto-'$monto' WHERE idlocal='$_COOKIE[idlocal]' AND idempresa='$_COOKIE[id]' AND estado='1' AND beta='$fa[tipo]' ";
    } else if ($operacion == 'SUMA' && $salida == 'SALIDA') {
        $sqlca = "UPDATE cajas SET salidas=salidas+'$monto' WHERE idlocal='$_COOKIE[idlocal]' AND idempresa='$_COOKIE[id]' AND estado='1' AND beta='$fa[tipo]' ";
    } else if ($operacion == 'RESTA' && $salida == 'SALIDA') {
        $sqlca = "UPDATE cajas SET salidas=salidas-'$monto' WHERE idlocal='$_COOKIE[idlocal]' AND idempresa='$_COOKIE[id]' AND estado='1' AND beta='$fa[tipo]' ";
    }
    ejecutarConsulta($sqlca);
}


function credito($monto, $operacion, $idusuario){

if($operacion=='SUMA'){
$sqlca="UPDATE persona SET credito=credito+'$monto' WHERE idpersona='$_COOKIE[id]' ";
}else{
$sqlca="UPDATE persona SET credito=credito-'$monto' WHERE idpersona='$_COOKIE[id]' ";	
}
ejecutarConsulta($sqlca);

}



?>