<?php 
date_default_timezone_set('America/Lima');

header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

if (strlen(session_id()) < 1) 
require_once "../modelos/resumen.php";
require_once "../modelos/facturacion-electronica.php";

$resumen=new Resumen();


switch ($_GET["op"]){
    
    
case 'enviarresumen':

$jsondata = array();
//$fechadoc='2018-06-02';
$fechadoc=$_GET["fecha"];
$tipodoc=$_GET["tipodoc"];
$nivel='0';
$id='';
if(isset($_GET["id"])){ $id=$_GET["id"]; }
		
//$tipodoc=substr($tipodoc,0,1);
//echo $tipodoc.'<br>';
$fechadocf='FECHA_DOCUMENTO'; 
$nnivel='2'; 
$codigo='RC'; 
$tipoboleta='1';
/*		
$sql="SELECT *FROM venta WHERE estado='$nivel' AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%' AND txtID_TIPO_DOCUMENTO='$tipodoc' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
*/ 

$sql="SELECT *FROM venta WHERE estado='$estado' AND DATE(txtFECHA_DOCUMENTO)='$fechadoc' AND txtID_TIPO_DOCUMENTO='03'  AND idempresa='$_COOKIE[id]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
//$rspta = $resumen->enviarresumen($fechadoc, $nivel, $tipodoc);

$sql="SELECT *FROM venta WHERE estado='$estado' AND DATE(txtFECHA_DOCUMENTO)='$fechadoc' AND (txtID_TIPO_DOCUMENTO='03' OR docmodifica_tipo='03') AND idempresa='$_COOKIE[id]' ";
$rspta = ejecutarConsulta($sql);

if($mostrar){
		
$detalle = array();
$json = array();
$fecha=date("Y-m-d");
$serie=date("Ymd");
$new = new api_sunat();

$sqlf="SELECT *FROM resumen WHERE serie LIKE '%$serie%' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
$mostrarf= ejecutarConsultaSimpleFila($sqlf);
$numero=$mostrarf['numero']+1;
$numero=str_pad($numero, 3, "0", STR_PAD_LEFT);
	
$sql2="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql2);
if($id==''){	
$resumen->guardarresumen($nivel, $codigo, $serie, $numero, '0', '', '', $fechadoc, $fecha);
	
$sqlfre="SELECT *FROM resumen WHERE idempresa='$_COOKIE[id]' ORDER BY id DESC ";
$mostrarre= ejecutarConsultaSimpleFila($sqlfre);
$id=$mostrarre['id'];	
	
}
$n=0;


	
while ($reg = $rspta->fetch_object()){		

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	

	
if($mostrar['tipo_documento']=='DNI'){ $tdoc='1'; }else if($mostrar['tipo_documento']=='RUC'){ $tdoc='6'; }
	
$n=$n+1;
$json['ITEM'] = $n;
$json["TIPO_COMPROBANTE"] = $reg->txtID_TIPO_DOCUMENTO;	

$json['NRO_COMPROBANTE'] = $reg->txtSERIE."-".$reg->txtNUMERO;
$json['TIPO_DOCUMENTO'] = $tdoc;
$json['NRO_DOCUMENTO'] = $mostrar['txtID_CLIENTE'];
$json['TIPO_COMPROBANTE_REF'] = $reg->docmodifica_tipo;
$json['NRO_COMPROBANTE_REF'] = $reg->docmodifica;

$json['STATU'] = $tipoboleta;//1=ENVIO DE BOLETAS NORMALES, 3=ENVIO DE BOLETAS ANULADAS 
$json['COD_MONEDA'] = $reg->txtID_MONEDA;
$json['TOTAL'] = $reg->txtTOTAL;
$json['GRAVADA'] = $reg->txtSUB_TOTAL;
	$json['ISC'] = "0";
	$json['IGV'] = $reg->txtIGV;
	$json['OTROS'] = "0";
	$json['CARGO_X_ASIGNACION'] = "1";
	$json['MONTO_CARGO_X_ASIG'] = "0";
	$json['EXONERADO'] = $reg->exonerado;
	$json['INAFECTO'] = "0";
	$json['EXPORTACION'] = "0";
	$json['GRATUITAS'] = $reg->gratuita;


$detalle[]=$json;
	
	
}

$data = array(
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"RAZON_SOCIAL"=>$fa['razon_social'],
"TIPO_DOCUMENTO"=>"6",
"CODIGO"=>$codigo,
"SERIE"=>$serie,
"SECUENCIA"=>$numero, 
"FECHA_REFERENCIA"=>$fechadoc,
$fechadocf=>$fecha,
"TIPO_PROCESO"=>$fa['tipo'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"PIN"=>$fa['firma'],
"SUNAT"=> $fa['sunat'],
"detalle"=>$detalle
);
	
//echo json_encode($data);
$resultado = $new->sendresumen(json_encode($data), RUTA);
//var_dump($resultado);
	
$me = json_decode($resultado, true);
		
if($me['cod_sunat']=='0'){
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El documento fue enviado a la sunat';
	
if($nivel=='4'){ $estado='5'; $nivel='4'; }else{ $estado='1'; $nivel='0'; }

$sql="UPDATE resumen SET estado='$estado', ticket='$me[msj_sunat]' WHERE id='$id' ";
ejecutarConsulta($sql);
	
$sql="UPDATE venta SET estado='$estado' WHERE DATE(txtFECHA_DOCUMENTO)='$fechadoc' AND estado='$nivel' AND (txtID_TIPO_DOCUMENTO='03' OR docmodifica_tipo='03') AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sql);
	
$data2 = array(
"TIPO_PROCESO"=>$fa['tipo'],
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"SUNAT"=> $fa['sunat'],
"TICKET"=>$me['msj_sunat'],
"TIPO_DOCUMENTO"=>$codigo, 
"NRO_DOCUMENTO"=>$serie.'-'.$numero,
);

$resultado2 = $new->sendticket(json_encode($data2), RUTA);
//var_dump($resultado2);
$me = json_decode($resultado2, true);

if($me['cod_sunat']=='0'){
	
if($nivel=='4'){ $estado='6'; $nivel2='5'; }else{ $estado='2'; $nivel2='1'; }
	
$jsondata['hash_cdr'] =$me['hash_cdr'];
$jsondata['id'] =$id;	
	
$sql="UPDATE resumen SET estado='$estado', mensaje='$me[msj_sunat]', hash_cdr='$me[hash_cdr]' WHERE id='$id' ";
ejecutarConsulta($sql);
	
$sql="UPDATE venta SET estado='$estado' WHERE DATE(txtFECHA_DOCUMENTO)='$fechadoc' AND estado='0' AND txtID_TIPO_DOCUMENTO='03' AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'MENSAJE DE SUNAT:'.$me['msj_sunat'].'. '.$me['cod_sunat'];	
	
}
	
	
}else{

$jsondata['estado'] = '0';
$jsondata['mensaje'] =$me['msj_sunat'].'. '.$me['cod_sunat'];
	
}		

}else{
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'No hay documentos pendientes';	
}
echo json_encode($jsondata);
exit();		
		
break;
    
    
    
		
case 'enviar':

$jsondata = array();
//$fechadoc='2018-06-02';
$fechadoc=$_GET["fecha"];
$tipodoc=$_GET["tipodoc"];
$nivel=$_GET["nivel"];
$id='';
if(isset($_GET["id"])){ $id=$_GET["id"]; }
		
//$tipodoc=substr($tipodoc,0,1);
//echo $tipodoc.'<br>';
if($tipodoc=='01'){ $fechadocf='FECHA_BAJA'; }else{ $fechadocf='FECHA_DOCUMENTO'; }
if($nivel=='4'){ $nnivel='6'; $codigo='RA'; $tipoboleta='3';   }else{ $nnivel='2'; $codigo='RC'; $tipoboleta='1'; }
if($tipodoc=='03'){ $codigo='RC'; }		
/*		
$sql="SELECT *FROM venta WHERE estado='$nivel' AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%' AND txtID_TIPO_DOCUMENTO='$tipodoc' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
*/ 

if($id!=''){

$sql="SELECT *FROM venta WHERE (estado='1' OR estado='2' OR estado='5' OR estado='6') AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%'AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sqlr="SELECT *FROM resumen WHERE id='$tipodoc' ";
$mr= ejecutarConsultaSimpleFila($sqlr);

	/*
if($mr['ticket']=='SUNAT ESTA FUERA SERVICIO'){
$rspta = $resumen->enviarresumen($fechadoc, '2', $tipodoc);	
}else{
$rspta = $resumen->enviarresumen($fechadoc, $nivel, $tipodoc);	
}
*/
	
$sqlrs="SELECT *FROM venta WHERE (estado='1' OR estado='5') AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%' AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]'";
$RES= ejecutarConsultaSimpleFila($sqlrs);
	
$sqlrs2="SELECT *FROM venta WHERE estado='2' AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%' AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]'";
$RES2= ejecutarConsultaSimpleFila($sqlrs2);

if($RES){
	
$jsondata['nivel'] = '1';
$rspta = $resumen->enviarresumen($fechadoc, $nivel, $tipodoc);
	
$sql="SELECT *FROM venta WHERE (estado='$nivel' OR estado='1' OR estado='5')  AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%'AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$jsondata['tipodoc'] = $tipodoc;
	
}else if($RES2){
$jsondata['nivel'] = '2';
$rspta = $resumen->enviarresumen($fechadoc, '2', $tipodoc);	
	
$sql="SELECT *FROM venta WHERE estado='2' AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%'AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$jsondata['tipodoc'] = $tipodoc;
	
}else{
$jsondata['nivel'] = '3';
$rspta = $resumen->enviarresumen($fechadoc, '6', $tipodoc);	
	
$sql="SELECT *FROM venta WHERE estado='6'  AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%'AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$jsondata['tipodoc'] = $tipodoc;
	
}

	
}else{
	
$sql="SELECT *FROM venta WHERE estado='$nivel' AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%'AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$rspta = $resumen->enviarresumen($fechadoc, $nivel, $tipodoc);
	
}
	
		
if($mostrar){
		
$detalle = array();
$json = array();
$fecha=date("Y-m-d");
$serie=date("Ymd");
$new = new api_sunat();

$sqlf="SELECT *FROM resumen WHERE serie LIKE '%$serie%' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
$mostrarf= ejecutarConsultaSimpleFila($sqlf);
$numero=$mostrarf['numero']+1;
$numero=str_pad($numero, 3, "0", STR_PAD_LEFT);
	
$sql2="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql2);
if($id==''){	
$resumen->guardarresumen($nivel, $codigo, $serie, $numero, '0', '', '', $fechadoc, $fecha);
	
$sqlfre="SELECT *FROM resumen WHERE idempresa='$_COOKIE[id]' ORDER BY id DESC ";
$mostrarre= ejecutarConsultaSimpleFila($sqlfre);
$id=$mostrarre['id'];	
	
}
$n=0;


	
while ($reg = $rspta->fetch_object()){		

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	

	
if($mostrar['tipo_documento']=='DNI'){ $tdoc='1'; }else if($mostrar['tipo_documento']=='RUC'){ $tdoc='6'; }
	
$n=$n+1;
$json['ITEM'] = $n;
$json["TIPO_COMPROBANTE"] = $reg->txtID_TIPO_DOCUMENTO;	
	
if($tipodoc=='01'){

$json["SERIE"] = $reg->txtSERIE;
$json["NUMERO"] = $reg->txtNUMERO;
$json["DESCRIPCION"] = "ERROR DE DIGITACION";	
	
}else{

$json['NRO_COMPROBANTE'] = $reg->txtSERIE."-".$reg->txtNUMERO;
$json['TIPO_DOCUMENTO'] = $tdoc;
$json['NRO_DOCUMENTO'] = $mostrar['txtID_CLIENTE'];
$json['TIPO_COMPROBANTE_REF'] = $reg->docmodifica_tipo;
$json['NRO_COMPROBANTE_REF'] = $reg->docmodifica;

$json['STATU'] = $tipoboleta;//1=ENVIO DE BOLETAS NORMALES, 3=ENVIO DE BOLETAS ANULADAS 
$json['COD_MONEDA'] = $reg->txtID_MONEDA;
$json['TOTAL'] = $reg->txtTOTAL;
$json['GRAVADA'] = $reg->txtSUB_TOTAL;
	$json['ISC'] = "0";
	$json['IGV'] = $reg->txtIGV;
	$json['OTROS'] = "0";
	$json['CARGO_X_ASIGNACION'] = "1";
	$json['MONTO_CARGO_X_ASIG'] = "0";
	$json['EXONERADO'] = $reg->exonerado;
	$json['INAFECTO'] = "0";
	$json['EXPORTACION'] = "0";
	$json['GRATUITAS'] = $reg->gratuita;

}
	
	
$detalle[]=$json;
	
	
}

$data = array(
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"RAZON_SOCIAL"=>$fa['razon_social'],
"TIPO_DOCUMENTO"=>"6",
"CODIGO"=>$codigo,
"SERIE"=>$serie,
"SECUENCIA"=>$numero, 
"FECHA_REFERENCIA"=>$fechadoc,
$fechadocf=>$fecha,
"TIPO_PROCESO"=>$fa['tipo'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"PIN"=>$fa['firma'],
"SUNAT"=> $fa['sunat'],
"detalle"=>$detalle
);
	
//echo json_encode($data);

if($tipodoc=='01'){ 
$resultado = $new->sendbaja(json_encode($data), RUTA);
}else{
$resultado = $new->sendresumen(json_encode($data), RUTA);
}

//var_dump($resultado);
	
$me = json_decode($resultado, true);
		
if($me['cod_sunat']=='0'){
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El documento fue enviado a la sunat';
	
if($nivel=='4'){ $estado='5'; $nivel='4'; }else{ $estado='1'; $nivel='0'; }

$sql="UPDATE resumen SET estado='$estado', ticket='$me[msj_sunat]' WHERE id='$id' ";
ejecutarConsulta($sql);
	
$sql="UPDATE venta SET estado='$estado' WHERE txtFECHA_DOCUMENTO LIKE '%$fechadoc%' AND (estado='$nivel' OR estado='1' OR estado='5') AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sql);
	
$data2 = array(
"TIPO_PROCESO"=>$fa['tipo'],
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"SUNAT"=> $fa['sunat'],
"TICKET"=>$me['msj_sunat'],
"TIPO_DOCUMENTO"=>$codigo, 
"NRO_DOCUMENTO"=>$serie.'-'.$numero,
);

$resultado2 = $new->sendticket(json_encode($data2), RUTA);
//var_dump($resultado2);
$me = json_decode($resultado2, true);

if($me['cod_sunat']=='0'){
	
if($nivel=='4'){ $estado='6'; $nivel2='5'; }else{ $estado='2'; $nivel2='1'; }
	
$jsondata['hash_cdr'] =$me['hash_cdr'];
$jsondata['id'] =$id;	
	
$sql="UPDATE resumen SET estado='$estado', mensaje='$me[msj_sunat]', hash_cdr='$me[hash_cdr]' WHERE id='$id' ";
ejecutarConsulta($sql);
	
$sql="UPDATE venta SET estado='$estado' WHERE DATE(txtFECHA_DOCUMENTO)='$fechadoc' AND estado='0' AND txtID_TIPO_DOCUMENTO='03' AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $me['msj_sunat'].'. '.$me['cod_sunat'];	
	
}
	
	
}else{

$jsondata['estado'] = '0';
$jsondata['mensaje'] =$me['msj_sunat'].'. '.$me['cod_sunat'];
	
}		

}else{
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'No hay documentos pendientes';	
}
echo json_encode($jsondata);
exit();		
		
break;
		
case 'reenviar':

$jsondata = array();
//$fechadoc='2018-06-02';
$fechadoc=$_GET["fecha"];
$tipodoc=$_GET["tipodoc"];
$nivel=$_GET["nivel"];
$id=$_GET["id"];
		
//$tipodoc=substr($tipodoc,0,1);
//echo $tipodoc.'<br>';
$fechadocf='FECHA_DOCUMENTO';
$nnivel='2'; 
$codigo='RC'; 
$tipoboleta='1';
	
/*		
$sql="SELECT *FROM venta WHERE estado='$nivel' AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%' AND txtID_TIPO_DOCUMENTO='$tipodoc' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
*/ 


$sql="SELECT *FROM venta WHERE (estado='1' OR estado='2' OR estado='5' OR estado='6') AND DATE(txtFECHA_DOCUMENTO)= '$fechadoc'AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sqlr="SELECT *FROM resumen WHERE id='$tipodoc' ";
$mr= ejecutarConsultaSimpleFila($sqlr);

$sqlrs="SELECT *FROM venta WHERE (estado='1' OR estado='5') AND DATE(txtFECHA_DOCUMENTO)='$fechadoc' AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]'";
$RES= ejecutarConsultaSimpleFila($sqlrs);
	
$sqlrs2="SELECT *FROM venta WHERE estado='2' AND DATE(txtFECHA_DOCUMENTO)='$fechadoc' AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]'";
$RES2= ejecutarConsultaSimpleFila($sqlrs2);

$sql="SELECT *FROM venta WHERE estado='$nivel' AND estado='0'  AND DATE(txtFECHA_DOCUMENTO)='$fechadoc'AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$jsondata['tipodoc'] = $tipodoc;


if($mostrar){
		
$detalle = array();
$json = array();
$fecha=date("Y-m-d");
$serie=date("Ymd");
$new = new api_sunat();

$sqlf="SELECT *FROM resumen WHERE serie LIKE '%$serie%' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
$mostrarf= ejecutarConsultaSimpleFila($sqlf);
$numero=$mostrarf['numero']+1;
$numero=str_pad($numero, 3, "0", STR_PAD_LEFT);
	
$sql2="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql2);

$n=0;

$jsondata['nivel'] = '1';
$sql="SELECT *FROM venta WHERE estado='0' AND DATE(txtFECHA_DOCUMENTO)='$fecha' AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
$rspta =ejecutarConsulta($sql);

while ($reg = $rspta->fetch_object()){	

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	

	
if($mostrar['tipo_documento']=='DNI'){ $tdoc='1'; }else if($mostrar['tipo_documento']=='RUC'){ $tdoc='6'; }
	
$n=$n+1;
$json['ITEM'] = $n;
$json["TIPO_COMPROBANTE"] = $reg->txtID_TIPO_DOCUMENTO;	
	
if($tipodoc=='01'){

$json["SERIE"] = $reg->txtSERIE;
$json["NUMERO"] = $reg->txtNUMERO;
$json["DESCRIPCION"] = "ERROR DE DIGITACION";	
	
}else{

$json['NRO_COMPROBANTE'] = $reg->txtSERIE."-".$reg->txtNUMERO;
$json['TIPO_DOCUMENTO'] = $tdoc;
$json['NRO_DOCUMENTO'] = $mostrar['txtID_CLIENTE'];
$json['TIPO_COMPROBANTE_REF'] = $reg->docmodifica_tipo;
$json['NRO_COMPROBANTE_REF'] = $reg->docmodifica;

$json['STATU'] = $tipoboleta;//1=ENVIO DE BOLETAS NORMALES, 3=ENVIO DE BOLETAS ANULADAS 
$json['COD_MONEDA'] = $reg->txtID_MONEDA;
$json['TOTAL'] = $reg->txtTOTAL;
$json['GRAVADA'] = $reg->txtSUB_TOTAL;
	$json['ISC'] = "0";
	$json['IGV'] = $reg->txtIGV;
	$json['OTROS'] = "0";
	$json['CARGO_X_ASIGNACION'] = "1";
	$json['MONTO_CARGO_X_ASIG'] = "0";
	$json['EXONERADO'] = $reg->exonerado;
	$json['INAFECTO'] = "0";
	$json['EXPORTACION'] = "0";
	$json['GRATUITAS'] = $reg->gratuita;

}
	
	
$detalle[]=$json;
	
	
}

$data = array(
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"RAZON_SOCIAL"=>$fa['razon_social'],
"TIPO_DOCUMENTO"=>"6",
"CODIGO"=>$codigo,
"SERIE"=>$serie,
"SECUENCIA"=>$numero, 
"FECHA_REFERENCIA"=>$fechadoc,
$fechadocf=>$fecha,
"TIPO_PROCESO"=>$fa['tipo'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"PIN"=>$fa['firma'],
"SUNAT"=> $fa['sunat'],
"detalle"=>$detalle
);
	
//echo json_encode($data);

if($tipodoc=='01'){ 
$resultado = $new->sendbaja(json_encode($data), RUTA);
}else{
$resultado = $new->sendresumen(json_encode($data), RUTA);
}

//var_dump($resultado);
	
$me = json_decode($resultado, true);
		
if($me['cod_sunat']=='0'){
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El documento fue enviado a la sunat';
	
if($nivel=='4'){ $estado='5'; $nivel='4'; }else{ $estado='1'; $nivel='0'; }

$sql="UPDATE resumen SET estado='$estado', ticket='$me[msj_sunat]' WHERE id='$id' ";
ejecutarConsulta($sql);
	
$sql="UPDATE venta SET estado='$estado' WHERE txtFECHA_DOCUMENTO LIKE '%$fechadoc%' AND (estado='$nivel' OR estado='1' OR estado='5') AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sql);
	
$data2 = array(
"TIPO_PROCESO"=>$fa['tipo'],
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"SUNAT"=> $fa['sunat'],
"TICKET"=>$me['msj_sunat'],
"TIPO_DOCUMENTO"=>$codigo, 
"NRO_DOCUMENTO"=>$serie.'-'.$numero,
);

$resultado2 = $new->sendticket(json_encode($data2), RUTA);
//var_dump($resultado2);
$me = json_decode($resultado2, true);

if($me['cod_sunat']=='0'){
	
if($nivel=='4'){ $estado='6'; $nivel2='5'; }else{ $estado='2'; $nivel2='1'; }
	
$jsondata['hash_cdr'] =$me['hash_cdr'];
$jsondata['id'] =$id;	
	
$sql="UPDATE resumen SET estado='$estado', mensaje='$me[msj_sunat]', hash_cdr='$me[hash_cdr]' WHERE id='$id' ";
ejecutarConsulta($sql);
	
$sql="UPDATE venta SET estado='$estado' WHERE DATE(txtFECHA_DOCUMENTO)='$fechadoc' AND estado='0' AND txtID_TIPO_DOCUMENTO='03' AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $me['msj_sunat'].'. '.$me['cod_sunat'];	
	
}
	
	
}else{

$jsondata['estado'] = '0';
$jsondata['mensaje'] =$me['msj_sunat'].'. '.$me['cod_sunat'];
	
}		

}else{
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'No hay documentos pendientes';	
}
echo json_encode($jsondata);
exit();		
		
break;		
		
case 'reticket':

$jsondata = array();
		
$detalle = array();
$json = array();
$new = new api_sunat();
$id=$_GET['id'];
		
$sql="SELECT *FROM resumen WHERE id='$id' ";
$re= ejecutarConsultaSimpleFila($sql);
$fecha=$re['fecha_documento'];
$sql2="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql2);
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'Intente mas tarde';
		
$data2 = array(
"TIPO_PROCESO"=>$fa['tipo'],
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"SUNAT"=> $fa['sunat'],
"TICKET"=>$re['ticket'],
"TIPO_DOCUMENTO"=>$re['codigo'], 
"NRO_DOCUMENTO"=>$re['serie'].'-'.$re['numero'],
);

$resultado2 = $new->sendticket(json_encode($data2), RUTA);
//var_dump($resultado2);
$me = json_decode($resultado2, true);


if($me['cod_sunat']=='0'){
	
$estado='2';
	
$sql="UPDATE resumen SET estado='$estado', mensaje='$me[msj_sunat]', hash_cdr='$me[hash_cdr]' WHERE id='$id' ";
ejecutarConsulta($sql);

$sqlv="UPDATE venta SET estado='2', mensaje='$me[msj_sunat]' WHERE txtFECHA_DOCUMENTO LIKE '%$fecha%' AND estado='0' AND txtID_TIPO_DOCUMENTO='03' AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sqlv);

$jsondata['estado'] = '1';
$jsondata['mensaje'] = $me['msj_sunat'].'.';  
}else if($me['cod_sunat']!=''){
$jsondata['mensaje'] = $me['msj_sunat'].'.';   
}

echo json_encode($jsondata);
exit();		
		
break;
		
		
		
case 'reestado':

$jsondata = array();
		
$detalle = array();
$json = array();
$new = new api_sunat();
		
$id=$_GET['id'];
		
$sql="SELECT *FROM resumen WHERE id='$id' ";
$re= ejecutarConsultaSimpleFila($sql);

$fecha=$re['fecha_documento'];
$sql2="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql2);
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'Intente mas tarde';
$jsondata['fecha'] =$fecha;
$jsondata['id'] =$id;

$estado='2';
	
$sql="UPDATE resumen SET estado='$estado' WHERE id='$id' ";
ejecutarConsulta($sql);

$sqlv="UPDATE venta SET estado='2' WHERE DATE(txtFECHA_DOCUMENTO)='$fecha' AND estado='0' AND txtID_TIPO_DOCUMENTO='03' AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sqlv);

$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'SISTEMA ACTUALIZADO.';	
	


echo json_encode($jsondata);
exit();		
		
break;
		
	
case 'enviarnota':

$jsondata = array();

$fechadoc=$_GET["fecha"];
$tipodoc=$_GET["tipodoc"];
$nivel=$_GET["nivel"];
$tipodoc=substr($txtSERIE,0,1);
		
if($tipodoc=='F'){ $fechadocf='FECHA_BAJA'; }else{ $fechadocf='FECHA_DOCUMENTO'; }
if($nivel=='4'){ $nnivel='6'; $codigo='RA';  }else{ $nnivel='2'; $codigo='RC'; }
		
$sql="SELECT *FROM notacredito WHERE estado='$nivel' AND txtFECHA_DOCUMENTO LIKE '%$fechadoc%' AND txtID_TIPO_DOCUMENTO='$tipodoc' AND idempresa='$_COOKIE[id]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
		
if($mostrar){
$detalle = array();
$json = array();
$fecha=date("Y-m-d");
$serie=date("Ymd");
$new = new api_sunat();
		
$sql="SELECT *FROM resumen WHERE  idempresa='$_COOKIE[id]' ORDER BY id DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);
$numero=$mostrar['numero']+1;
	
$sql2="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql2);
	
$n=0;
$rspta = $resumen->enviarresumen($fechadoc, $nivel, $tipodoc);
while ($reg = $rspta->fetch_object()){		

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	

	
if($mostrar['tipo_documento']=='DNI'){ $tdoc='1'; }else if($mostrar['tipo_documento']=='RUC'){ $tdoc='6'; }
	
$n=$n+1;
	
if($tipodoc=='01'){
	
$json["ITEM"] = $n;
$json["TIPO_COMPROBANTE"] = "01";
$json["SERIE"] = $reg->txtSERIE;
$json["NUMERO"] = $reg->txtNUMERO;
$json["DESCRIPCION"] = "ERROR DE DIGITACION";	
	
}else{
	
$json['ITEM'] = $n;
$json['TIPO_COMPROBANTE'] = "03";
$json['NRO_COMPROBANTE'] = $reg->txtSERIE."-".$reg->txtNUMERO;
$json['TIPO_DOCUMENTO'] = $tdoc;
$json['NRO_DOCUMENTO'] = $mostrar['txtID_CLIENTE'];
$json['TIPO_COMPROBANTE_REF'] = "";
$json['NRO_COMPROBANTE_REF'] = "";
$json['STATU'] = "1";
$json['COD_MONEDA'] = $reg->txtID_MONEDA;
$json['TOTAL'] = $reg->txtTOTAL;
$json['GRAVADA'] = $reg->txtSUB_TOTAL;
	$json['ISC'] = "0";
	$json['IGV'] = $reg->txtIGV;
	$json['OTROS'] = "0";
	$json['CARGO_X_ASIGNACION'] = "1";
	$json['MONTO_CARGO_X_ASIG'] = "0";
	$json['EXONERADO'] = "0";
	$json['INAFECTO'] = "0";
	$json['EXPORTACION'] = "0";
	$json['GRATUITAS'] = "0";
}
	
$detalle[]=$json;
	
	
}

$data = array(
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"RAZON_SOCIAL"=>$fa['razon_social'],
"TIPO_DOCUMENTO"=>"6",
"CODIGO"=>$codigo,
"SERIE"=>$serie,
"SECUENCIA"=>$numero, 
"FECHA_REFERENCIA"=>$fechadoc,
$fechadocf=>$fecha,
"TIPO_PROCESO"=>$fa['tipo'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"SUNAT"=> $fa['sunat'],
"detalle"=>$detalle
);

		
/*		
echo json_encode($data);
*/

if($tipodoc=='01'){ 
$resultado = $new->sendbaja(json_encode($data), RUTA);
}else{ 
$resultado = $new->sendresumen(json_encode($data), RUTA);
}
//var_dump($resultado);
$me = json_decode($resultado, true);
if($me['cod_sunat']=='0'){
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El documento fue enviado a la sunat';
	
if($nivel=='4'){ $estado='5'; $nivel='4'; }else{ $estado='1'; $nivel='0'; }
	
$resumen->guardarresumen($nivel, $codigo, $serie, $numero, $estado, $me['hash_cpe'], $me['msj_sunat'], $fechadoc, $fecha);

$sql="UPDATE venta SET estado='$estado' WHERE txtFECHA_DOCUMENTO LIKE '%$fechadoc%' AND estado='$nivel' AND txtID_TIPO_DOCUMENTO='$tipodoc' AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sql);
	
$data2 = array(
"TIPO_PROCESO"=>$fa['tipo'],
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"SUNAT"=> $fa['sunat'],
"TIPO_DOCUMENTO"=>$codigo, 
"NRO_DOCUMENTO"=>$serie.'-'.$numero,
);

$resultado2 = $new->sendticket(json_encode($data2), RUTA);
//var_dump($resultado2);
$me = json_decode($resultado2, true);

if($me['cod_sunat']=='0'){
	
if($nivel=='4'){ $estado='6'; $nivel2='5'; }else{ $estado='2'; $nivel2='1'; }
	
$sql="UPDATE resumen SET estado='$estado', mensaje='$me[msj_sunat]', hash_cdr='$me[hash_cdr]' WHERE serie='$serie' AND numero='$numero' AND codigo='$codigo' AND tipo='$nivel' AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sql);
	
$sql="UPDATE venta SET estado='$estado' WHERE txtFECHA_DOCUMENTO LIKE '%$fechadoc%' AND estado='$nivel2' AND txtID_TIPO_DOCUMENTO='$tipodoc' AND idempresa='$_COOKIE[id]' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $me['msj_sunat'];	
	
}
	
	
}else{

$jsondata['estado'] = '0';
$jsondata['mensaje'] =$me['msj_sunat'];
	
}		

}else{
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'No hay documentos pendientes';	
}
echo json_encode($jsondata);
exit();		
		
	break;
		
		
		
	case 'anular':
		$rspta=$ingreso->anular($idingreso);
 		echo $rspta ? "Ingreso anulado" : "Ingreso no se puede anular";
	break;

	case 'mostrar':
		$rspta=$ingreso->mostrar($idingreso);
 		echo json_encode($rspta);
	break;

	case 'listarDetalle':
		//Recibimos el idingreso
		$id=$_GET['id'];

		$rspta = $ingreso->listarDetalle($id);
		$total=0;
		echo '<thead style="background-color:#A9D0F5">
                                    <th>Opciones</th>
                                    <th>Artículo</th>
                                    <th>txtCANTIDAD_ARTICULO</th>
                                    <th>Precio Compra</th>
                                    <th>Precio Venta</th>
                                    <th>Subtotal</th>
                                </thead>';

		while ($reg = $rspta->fetch_object())
				{
					echo '<tr class="filas"><td></td><td>'.$reg->nombre.'</td><td>'.$reg->txtCANTIDAD_ARTICULO.'</td><td>'.$reg->precio_compra.'</td><td>'.$reg->precio_venta.'</td><td>'.$reg->precio_compra*$reg->txtCANTIDAD_ARTICULO.'</td></tr>';
					$total=$total+($reg->precio_compra*$reg->txtCANTIDAD_ARTICULO);
				}
		echo '<tfoot>
                                    <th>TOTAL</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><h4 id="total">S/.'.$total.'</h4><input type="hidden" name="total_compra" id="total_compra"></th> 
                                </tfoot>';
	break;

	case 'listar':
	
$sqlc="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$conf= ejecutarConsultaSimpleFila($sqlc);
		
$nivel=$_GET['nivel'];
//Vamos a declarar un array
$data= Array();	
		
$sql="SELECT *FROM resumen WHERE tipo='$nivel' AND idempresa='$_COOKIE[id]' ORDER BY id DESC";
$rspta=ejecutarConsulta($sql);	
		
while ($reg=$rspta->fetch_object()){
	
$estadob='<span class="label bg-red">Enviado</span>'; 
	
if($reg->codigo=='RC'){ $tipdoc='03'; $tipodocf='BOLETAS'; }else{ $tipdoc='01'; $tipodocf='FACTURAS'; }
	
$sql3="SELECT *FROM venta WHERE DATE(txtFECHA_DOCUMENTO)='$reg->fecha_documento' AND txtID_TIPO_DOCUMENTO='03' AND idempresa='$reg->idempresa' ";
$ven= ejecutarConsultaSimpleFila($sql3);
	
$fecha=date("d/m/Y", strtotime($ven['txtFECHA_DOCUMENTO']));
	
$boton='<button class="btn btn-success btn-xs" ><i class="fa fa-check"></i></button>';			
if($nivel=='4'){
	
	
if($reg->estado=='5'){ 
	$estadob='<span class="label bg-orange">Enviado</span>'; 
	
if($reg->ticket==''){
$boton='<button class="btn btn-warning btn-xs" onclick="reticket('.$reg->id.')"><i class="fa fa-play"></i></button>';
	}else{
$boton='<button class="btn btn-warning btn-xs" onclick="reticket('.$reg->id.')"><i class="fa fa-play"></i></button>';
	}
		
$boton.=' <button class="btn btn-warning btn-xs" onclick="revisafactura(\''.$conf['ruc'].'\', \''.$ven['txtID_TIPO_DOCUMENTO'].'\', \''.$fecha.'\', \''.$ven['txtSERIE'].'\', \''.$ven['txtNUMERO'].'\', \''.$ven['txtTOTAL'].'\', \''.$reg->id.'\', \''.$reg->fecha_documento.'\');"><i class="fa fa-search"></i> REVISAR</button>';
	
		
}

	
if($reg->estado=='6'){ $estadob='<span class="label bg-green">Aceptado</span>'; }	
}else{
	
if($reg->estado=='2'){ $estadob='<span class="label bg-green">Aceptado</span>'; }
	
if($reg->estado=='1'){ 
$estadob='<span class="label bg-red">Enviado</span>'; 

if($reg->ticket==''){

$boton='<button class="btn btn-warning btn-xs" onclick="REresumen(\''.$reg->fecha_documento.'\', '.$reg->id.', \''.$tipdoc.'\')">REENVIAR <i class="fa fa-play"></i></button>';
	}else{
$boton='<button class="btn btn-warning btn-xs" onclick="reticket('.$reg->id.')"><i class="fa fa-play"></i></button>';	
	}
	
$boton.=' <button class="btn btn-warning btn-xs" onclick="revisafactura(\''.$conf['ruc'].'\', \''.$ven['txtID_TIPO_DOCUMENTO'].'\', \''.$fecha.'\', \''.$ven['txtSERIE'].'\', \''.$ven['txtNUMERO'].'\', \''.$ven['txtTOTAL'].'\', \''.$reg->id.'\');"><i class="fa fa-search"></i> REVISAR</button>';
	
}
	
}
	
	
if($reg->ticket=='SUNAT ESTA FUERA SERVICIO'){

$boton.=' <button class="btn btn-warning btn-xs" onclick="revisafactura(\''.$conf['ruc'].'\', \''.$ven['txtID_TIPO_DOCUMENTO'].'\', \''.$fecha.'\', \''.$ven['txtSERIE'].'\', \''.$ven['txtNUMERO'].'\', \''.$ven['txtTOTAL'].'\', \''.$reg->id.'\');"><i class="fa fa-search"></i> REVISAR</button>';
	
}
	
			
			
 			$data[]=array(
"0"=>$boton,
 				"1"=>$reg->id,
				"2"=>$tipodocf,
				"3"=>$reg->hash,
				"4"=>$reg->ticket,
 				"5"=>$reg->serie.'-'.$reg->numero,
				"6"=>$reg->fecha_documento,
 				"7"=>$reg->fecha,
 				"8"=>$estadob
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;

		
case 'listarbaja':
	
$sqlc="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$conf= ejecutarConsultaSimpleFila($sqlc);
		
$nivel=$_GET['nivel'];
$tipodoc=$_GET['tipodoc'];
		
$tipo='RC';
		
if($tipodoc=='01'){ $tipo='RA'; }
		
$sql="SELECT *FROM resumen WHERE tipo='$nivel' AND idempresa='$_COOKIE[id]' AND codigo='$tipo' ORDER BY id DESC";
$rspta=ejecutarConsulta($sql);
//Vamos a declarar un array
$data= Array();	
		
while ($reg=$rspta->fetch_object()){
	
if($reg->codigo=='RC'){ $tipdoc='03'; $tipodocf='BOLETAS'; }else{ $tipdoc='01'; $tipodocf='FACTURAS'; }
	
$sql3="SELECT *FROM venta WHERE txtFECHA_DOCUMENTO LIKE '%$reg->fecha_documento%' AND txtID_TIPO_DOCUMENTO='03' ";
$ven= ejecutarConsultaSimpleFila($sql3);
	
$sqlcl="SELECT *FROM persona WHERE idpersona='$ven[txtID_CLIENTE]' ";
$cli= ejecutarConsultaSimpleFila($sqlcl);

$fecha=date("d/m/Y", strtotime($ven['txtFECHA_DOCUMENTO']));
	
$boton='<button class="btn btn-success btn-xs" ><i class="fa fa-check"></i></button>';			
if($nivel=='4'){
	
	
if($reg->estado=='5'){ 
	$estadob='<span class="label bg-orange">Enviado</span>'; 
	
if($reg->ticket==''){
$boton='<button class="btn btn-warning btn-xs" onclick="reticket('.$reg->id.')"><i class="fa fa-play"></i></button>';
	}else{
$boton='<button class="btn btn-warning btn-xs" onclick="reticket('.$reg->id.')"><i class="fa fa-play"></i></button>';
	}
		
$boton.=' <button class="btn btn-warning btn-xs" onclick="revisafactura(\''.$conf['ruc'].'\', \''.$ven['txtID_TIPO_DOCUMENTO'].'\', \''.$fecha.'\', \''.$ven['txtSERIE'].'\', \''.$ven['txtNUMERO'].'\', \''.$ven['txtTOTAL'].'\', \''.$reg->id.'\', \''.$reg->fecha_documento.'\');"><i class="fa fa-search"></i> REVISAR</button>';
	
		
}

	
if($reg->estado=='6'){ $estadob='<span class="label bg-green">Aceptado</span>'; }	
}else{
	
if($reg->estado=='2'){ $estadob='<span class="label bg-green">Aceptado</span>'; }
	
if($reg->estado=='1'){ 
$estadob='<span class="label bg-red">Enviado</span>'; 

if($reg->ticket==''){

$boton='<button class="btn btn-warning btn-xs" onclick="REresumen(\''.$reg->fecha_documento.'\', '.$reg->id.', \''.$tipdoc.'\')">REENVIAR <i class="fa fa-play"></i></button>';
	}else{
$boton='<button class="btn btn-warning btn-xs" onclick="reticket('.$reg->id.')"><i class="fa fa-play"></i></button>';	
	}
	
$boton.=' <button class="btn btn-warning btn-xs" onclick="revisafactura(\''.$conf['ruc'].'\', \''.$ven['txtID_TIPO_DOCUMENTO'].'\', \''.$fecha.'\', \''.$ven['txtSERIE'].'\', \''.$ven['txtNUMERO'].'\', \''.$ven['txtTOTAL'].'\', \''.$reg->id.'\');"><i class="fa fa-search"></i> REVISAR</button>';
	
}
	
}
	
	
if($reg->ticket=='SUNAT ESTA FUERA SERVICIO'){

$boton.=' <button class="btn btn-warning btn-xs" onclick="revisafactura(\''.$conf['ruc'].'\', \''.$ven['txtID_TIPO_DOCUMENTO'].'\', \''.$fecha.'\', \''.$ven['txtSERIE'].'\', \''.$ven['txtNUMERO'].'\', \''.$ven['txtTOTAL'].'\', \''.$reg->id.'\');"><i class="fa fa-search"></i> REVISAR</button>';
	
}
	
			
			
 			$data[]=array(
"0"=>$boton,
 				"1"=>$reg->id,
				"2"=>$tipodocf,
				"3"=>$reg->hash,
				"4"=>$reg->ticket,
 				"5"=>$reg->serie.'-'.$reg->numero,
				"6"=>$reg->fecha_documento,
 				"7"=>$reg->fecha,
 				"8"=>$estadob
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
		
		
	case 'selectProveedor':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarP();

		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
				}
	break;

	case 'listarArticulos':
		require_once "../modelos/Articulo.php";
		$articulo=new Articulo();

		$rspta=$articulo->listarActivos();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>'<button class="btn btn-warning" onclick="agregarDetalle('.$reg->txtCOD_ARTICULO.',\''.$reg->txtDESCRIPCION_ARTICULO.'\')"><span class="fa fa-plus"></span></button>',
 				"1"=>$reg->categoria,
 				"2"=>$reg->codigo,
 				"3"=>$reg->txtDESCRIPCION_ARTICULO,
 				"4"=>$reg->stock,
 				"5"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px' >"
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);
	break;
}
?>