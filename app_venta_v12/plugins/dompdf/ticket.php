<?php
//require_once("dompdf/dompdf_config.inc.php");
$rutat=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

require_once 'lib/html5lib/Parser.php';
require_once 'lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'lib/php-svg-lib/src/autoload.php';
require_once 'src/Autoloader.php';
Dompdf\Autoloader::register();
use Dompdf\Dompdf;
use Dompdf\Options;
include "../phpqrcode/qrlib.php";
require "../../config/conexion.php";
require "../../modelos/numeros-letras.php";

$idventa=$_GET['id'];

$sql="SELECT *FROM venta WHERE idventa='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
		
$sql2="SELECT c.nombre AS nsector, p.* FROM persona p LEFT JOIN categoria c ON p.sector=c.idcategoria WHERE p.idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
		
$sqluser="SELECT *FROM usuario WHERE idusuario='$mostrar[idusuario]' ";
$user= ejecutarConsultaSimpleFila($sqluser);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);
		
$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);

$sql22="SELECT *FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente2= ejecutarConsultaSimpleFila($sql22);
		
if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }
		
$tdocumento='TICKET DE VENTA';
		
if($mostrar['txtID_TIPO_DOCUMENTO']=='03'){ $tdocumento='BOLETA DE VENTA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='90'){ $tdocumento='RECIBO DE VENTA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='92'){ $tdocumento='TICKET DE VENTA'; }

$subtotal=$mostrar['txtTOTAL']-$mostrar['txtIGV'];		
$subtotal= number_format($subtotal, 2);
	
$medpago=$mostrar['tipo_pago'];

// Los delimitadores pueden ser barra, punto o guión
$fecha = date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO']));
$fechaf = explode('-', $fecha);
$anio= $fechaf[2].'/'.$fechaf[1].'/'.$fechaf[0];

$fvencimiento=$fvencimiento=date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO']));
$condicionesf='0';
$formapago='CONTADO';
$formapago2='CONTADO';

$sqllet="SELECT *FROM caja_ventapago WHERE idventa='$idventa' ORDER BY fecha_pago DESC ";
$lettot= ejecutarConsultaSimpleFila($sqllet);


if($mostrar['tipo_pago']=='CREDITO'){ 
$fvencimiento=date("Y-m-d", strtotime($lettot['fecha_pago']));
	
$date1 = new DateTime($fecha);
$date2 = new DateTime($fvencimiento);
$diff = $date1->diff($date2);
$condicionesf=$diff->days;
	
$formapago='CRÉDITO';	
$formapago2='CRÉDITO A '.$condicionesf.' DÍAS';
	
}

$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['txtSERIE'].' | '.$mostrar['txtNUMERO'].' | '.$mostrar['txtIGV'].' | '.$mostrar['txtTOTAL'].' | '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' | '.$mcliente['tipo_documento'].' | '.$mcliente['txtID_CLIENTE'].' |';
		
$rutaqr=$mempresa['ruc'].".png";
QRcode::png($text, $rutaqr, 'Q', 15, 0);
	
if($mempresa['id']=='18'){
    require "ticket/clinica.php";
}else if($mostrar['idempresa']=='19'){
    require "ticket/andeticket.php";
}else if($mostrar['idempresa']=='0'){
    require "ticket/ticketa.php";
}else {
    require "ticket/ticketa.php";
}

echo $cont;




?>