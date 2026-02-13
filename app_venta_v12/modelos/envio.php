<?php
require_once __DIR__ . "/numeros-letras.php";
require_once __DIR__ . "/facturacion-electronica.php";

if (!function_exists("enviarfactura")) {
function enviarfactura($id, $nivelenvio='0'){
	
$idventa=$id;
	
$new = new api_sunat();
$cuerpocredito= array();
	
$sql="SELECT *FROM venta WHERE idventa='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$porigv=$fa['igv'];
$porigv=$porigv/100;
$porigv=$porigv+1.00;	
	
$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
	
$sqlsuc="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$sucur= ejecutarConsultaSimpleFila($sqlsuc);
	
$igvtotal='0';
$subsubtotal='0';
$gravada='0';
	
$ndoc='0';
if($mcliente['tipo_documento']=='RUC'){ 
$ndoc='6'; 
}else if($mcliente['tipo_documento']=='DNI'){ 
$ndoc='1'; 
}else if($mcliente['tipo_documento']=='4'){ 
 $ndoc='4';    
}else if($mcliente['tipo_documento']=='7'){ 
 $ndoc='7'; 
}	
	
if($mostrar['estadopago']=='2'){
	
$numref=$mostrar['referencia'];
$line=explode('-', $numref);
$sqlm="SELECT *FROM venta WHERE txtSERIE='$line[0]' AND txtNUMERO='$line[1]' AND txtID_TIPO_DOCUMENTO='92' AND idempresa='$_COOKIE[id]' ";
$most= ejecutarConsultaSimpleFila($sqlm);

$id=$most['idventa'];
	
}
	
	
$sql="SELECT *FROM detalle_venta WHERE idventa='$id' ";
$rspta=ejecutarConsulta($sql);
$n=0;
while ($reg = $rspta->fetch_object()){
	
$n=$n+1;

$unidadmedida=$reg->unidadmedida;
if($unidadmedida=='UND'){ $unidadmedida='NIU'; }
		
$json['txtITEM']=$n;
$json["txtUNIDAD_MEDIDA_DET"] =$unidadmedida;
$json["txtCANTIDAD_DET"] = $reg->txtCANTIDAD_ARTICULO;
		
$preciofinal=$reg->precio;
	
$json["txtPRECIO_DET"]=$reg->precio; 
$json["txtPRECIO_TIPO_CODIGO"] = "01";
$json["txtISC"] = "0";

//GRABADAS 10 EXONERADAS SON 20 // GRATUITAS 31	//INAFECTA 30
if($reg->tipo=='2'){ 
$json["txtCOD_TIPO_OPERACION"] = "31";
$json["txtPRECIO_SIN_IGV_DET"] ='0.00'; 
$json["txtIGV"] =round($reg->igv, 2);
$json["txtSUB_TOTAL_DET"] =round($reg->subtotal, 2); 
$json["txtIMPORTE_DET"] =round($reg->subtotal, 2);
	
$igvtotal=$reg->igv+$igvtotal;
$subsubtotal=$reg->subtotal+$subsubtotal;
	
$preciosinigv='0.00';

}else if($reg->tipo=='3'){
	$json["txtCOD_TIPO_OPERACION"] = "30"; 
	$json["txtIGV"] =round($reg->igv, 2);
	$json["txtPRECIO_SIN_IGV_DET"] =$reg->precio; 
	$json["txtSUB_TOTAL_DET"] =round($reg->subtotal, 2); 
	$json["txtIMPORTE_DET"] =round($reg->subtotal, 2);
		
	$igvtotal=$reg->igv+$igvtotal;
	$subsubtotal=$reg->subtotal+$subsubtotal;

}else if($reg->tipo=='1'){

	if($mostrar['exportacion']=='SI'){
		$json["txtCOD_TIPO_OPERACION"] = "40"; 
		}else{
		$json["txtCOD_TIPO_OPERACION"] = "20";  
		}

$json["txtIGV"] =round($reg->igv, 2);
$json["txtPRECIO_SIN_IGV_DET"] =$reg->precio; 
$json["txtSUB_TOTAL_DET"] =round($reg->subtotal, 2); 
$json["txtIMPORTE_DET"] =round($reg->subtotal, 2);
	
$igvtotal=$reg->igv+$igvtotal;
$subsubtotal=$reg->subtotal+$subsubtotal;
	
}else if($reg->tipo=='3'){
$json["txtCOD_TIPO_OPERACION"] = "30"; 
$json["txtIGV"] =round($reg->igv, 2);
$json["txtPRECIO_SIN_IGV_DET"] =$reg->precio; 
$json["txtSUB_TOTAL_DET"] =round($reg->subtotal, 2); 
$json["txtIMPORTE_DET"] =round($reg->subtotal, 2);
	
$igvtotal=$reg->igv+$igvtotal;
$subsubtotal=$reg->subtotal+$subsubtotal;
	
$preciosinigv=$reg->precio;
	
}else{

$json["txtCOD_TIPO_OPERACION"] = "10"; 

$preciosinigv=round(($preciofinal/$porigv), 5);

$importe=$preciosinigv*$reg->txtCANTIDAD_ARTICULO;
$totalimporte=$reg->precio*$reg->txtCANTIDAD_ARTICULO;
$igv=$totalimporte-$importe;
//echo 'importe:'.$importe;
$igvtotal=$igv+$igvtotal;
$subsubtotal=$importe+$subsubtotal;
//echo 'subsubtotal:'.$subsubtotal;	
	
$subtotal=$reg->subtotal;
$igv=$reg->igv;
	
/*DESCUENTO*/
$json["descuento"]='';	
$json["porcentaje"]=''; 
$json["totalfinal"]=''; 	
$descuento='0';	
	
$precio2=$reg->precio;
	
if($reg->descuento!='0.00'){
	

//$descuento=$reg->descuento/$reg->txtCANTIDAD_ARTICULO;
$descuentobase=round(($reg->descuento/$porigv), 7);
$totaldetalle=$reg->subtotal+$descuentobase;
$porcentaje=($descuentobase*100)/$totaldetalle;
$porcentaje=$porcentaje/100;
$porcentaje=round($porcentaje, 2);
$descuentobase=round($descuentobase, 2);	
$totaldetalle=round($totaldetalle, 2);	
//$porcentaje=($reg->descuento*100)/$subdetalle;

$json["porcentaje"]=$porcentaje;
$json["descuento"]=$descuentobase;
$json["totalfinal"]=$totaldetalle;
$json["midescuento"]='SI';
	
$preciofinal=$reg->precio;	
$preciosinigv=round(($reg->precio/$porigv), 7);
$preciodescuento=$reg->subtotal/$reg->subtotal;
	
$precio2=$reg->precio*$porcentaje;
$precio2=$reg->precio-$precio2;
	
}

$json["txtPRECIO_SIN_IGV_DET"]=$preciosinigv; 	
$json["txtSUB_TOTAL_DET"] =round($reg->subtotal, 2); 
$json["txtIMPORTE_DET"] =round($reg->subtotal, 2);
$json["txtIGV"] =round($reg->igv, 2);
$json["txtPRECIO_DET"]=$precio2;
	
}
	
	
	
	
	
//AQUI ENVIAMOS LA PLACA SI ES GRIFO
if($reg->placa!=''){
$js2["txtNOMBRE"]="Numero de Placa";
$js2["txtCODIGO"]="5010";
$js2["txtVALOR"]=$reg->placa;
$json['propiedad_adicional'][]= $js2;
}
	
$tit=strip_tags($reg->nombreproducto);

$json["txtCODIGO_DET"] = $reg->codigoproducto;
$json["txtDESCRIPCION_DET"] = $tit;
$detalle[]=$json;	

}

$jsondata['moneda'] =$mostrar['txtID_MONEDA'];
	
if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }
	
$totanticipo='0.00';
$subanticipo='0.00';
$pagadoanticipo='0.00';

/*REVISAMOS A VER SI HAY ANTICIPOS*/
/*REVISAMOS A VER SI HAY ANTICIPOS*/
/*REVISAMOS A VER SI HAY ANTICIPOS*/
if($mostrar['estadopago']=='2'){
	
$numref=$mostrar['referencia'];
	
$line=explode('-', $numref);	
	
$sqlm="SELECT *FROM venta WHERE txtSERIE='$line[0]' AND txtNUMERO='$line[1]' AND txtID_TIPO_DOCUMENTO='92' AND idempresa='$_COOKIE[id]' ";
$most= ejecutarConsultaSimpleFila($sqlm);
	
$sql22="SELECT sum(txtTOTAL) as total, sum(txtSUB_TOTAL) as subtotal, sum(txtIGV) as igv FROM venta WHERE referencia='$numref' AND estadopago='1'  AND idempresa='$_COOKIE[id]' ";
$mos22= ejecutarConsultaSimpleFila($sql22);
	
$totanticipo=$most['txtTOTAL'];
$subanticipo=$most['txtTOTAL']-$most['txtIGV'];
$subanticipo=round($subanticipo, 2);	
$pagadoanticipo=$most['txtSUB_TOTAL']-$mostrar['txtSUB_TOTAL'];
$pagadoanticipo=round($pagadoanticipo, 2);
	
$igvfinal=$mostrar['txtIGV'];	
$gravadas=$mostrar['txtTOTAL']-$mostrar['txtIGV']-$mostrar['exonerado']-$mostrar['inafecta'];
$gravadas=round($gravadas, 2);
	
$totalventa=round($totanticipo-$pagadoanticipo, 2);

$n=0;	
$sqla="SELECT *FROM venta WHERE referencia='$numref' AND estadopago='1' AND idempresa='$_COOKIE[id]' ";
$rsptaa=ejecutarConsulta($sqla);
while ($reg = $rsptaa->fetch_object()){
$n=$n+1;
$jsonant["orden"]=$n;	
$jsonant["monto"]=$reg->txtSUB_TOTAL;
$jsonant["serienumero"]=$reg->txtSERIE.'-'.$reg->txtNUMERO;
$cuerpoant[]=$jsonant;	
}

}else{
$cuerpoant='';
$igvfinal=$mostrar['txtIGV'];	
$gravadas=$mostrar['txtTOTAL']-$mostrar['txtIGV']-$mostrar['exonerado']-$mostrar['inafecta'];
$gravadas=round($gravadas, 2);
$totalventa=$mostrar['txtTOTAL'];
	
}
/*REVISAMOS A VER SI HAY ANTICIPOS*/
/*REVISAMOS A VER SI HAY ANTICIPOS*/
/*REVISAMOS A VER SI HAY ANTICIPOS*/
$fvencimiento=$mostrar['txtFECHA_DOCUMENTO'];
	
$sqllet="SELECT *FROM caja_ventapago WHERE idventa='$id' ORDER BY fecha_pago DESC ";
$lettot= ejecutarConsultaSimpleFila($sqllet);

if(isset($lettot)){
if($mostrar['tipo_pago']=='CREDITO'){
$fvencimiento=$lettot['fecha_pago'];
$cuerpocredito= array();
	
$sql3="SELECT SUM(montosoles) AS soles, SUM(montodolares) AS dolares FROM caja_ventapago WHERE idventa='$id' ";
$pago= ejecutarConsultaSimpleFila($sql3);
if($mostrar['txtID_MONEDA']=='PEN'){
$totalcredito=round($pago['soles'], 2);
}else{
$totalcredito=round($pago['dolares'], 2);
}	
}else{
$cuerpocredito=''; 
$totalcredito=''; 	
}
}else{ 
$cuerpocredito='';
$totalcredito='';
}
	
$n=0;
if($mostrar['tipo_pago']=='CREDITO'){
$sqla="SELECT *FROM caja_ventapago WHERE idventa='$id' ";
$rsptaa=ejecutarConsulta($sqla);
while ($reg = $rsptaa->fetch_object()){
$n=$n+1;
$jsoncred["COD_FORMA_PAGO"]=$n;	
if($mostrar['txtID_MONEDA']=='PEN'){
$jsoncred["MONTO_FORMA_PAGO"]=round($reg->montosoles, 2);	
}else{
$jsoncred["MONTO_FORMA_PAGO"]=round($reg->montodolares, 2);		
}
$jsoncred["FECHA_FORMA_PAGO"]=date("Y-m-d", strtotime($reg->fecha_pago));
$jsoncred["COD_MONEDA"]=$mostrar['txtID_MONEDA'];
$cuerpocredito[]=$jsoncred;
}
}

$nguia='';
$codguia='';	
if($mostrar['guia']!=''){
$nguia=$mostrar['guia'];
$codguia=$mostrar['tipoguia'];
}
			
$detopreacion='';
$detcuenta='';
$detcodigo='';
$detporcentaje='';
$dettotal='';
	
$tipooperacion='0101';	
	
if($mostrar['iddetraccion']!='0'){

$sqldet="SELECT *FROM detracciones WHERE id='$mostrar[iddetraccion]' ";
$detdet= ejecutarConsultaSimpleFila($sqldet);

$totdetraccion=round($mostrar['detraccion'], 0);
	
if($mostrar['txtID_MONEDA']!='PEN'){
$totdetraccion=$mostrar['detraccion']*$mostrar['tipocambio'];
$totdetraccion=round($totdetraccion, 2);
}
	
$detopreacion='001';
$detcuenta=$fa['detraccion'];
$detcodigo=$detdet['codigo'];
$detporcentaje=$detdet['porcentaje'];
$dettotal=$totdetraccion;
$tipooperacion='1001';
	
}

$porretenciones="";
$totalretenciones='';
$retencionesn='';

if($mostrar['retencion']!='0.00'){
$porretenciones="0.03";
$totalretenciones=round($mostrar['txtTOTAL'], 2);
$retencionesn=round($mostrar['retencion'], 2);
}	
	
$subtotal=round($mostrar['txtSUB_TOTAL']+$mostrar['exonerado']+$mostrar['inafecta'], 2);

$idsunat='0000';
if($sucur['idsunat']!=''){
$idsunat=$sucur['idsunat'];
}
$percemastotal=0;
	
if ($mostrar['percepcion']> 0) {
$percemastotal=round($mostrar['percepcion']+$totalventa, 2);	
$tipooperacion='2001';
}

$txtTOTAL_EXONERADAS=round($mostrar['exonerado'], 2);
$pais='PE';
$totexport='0.00';

if($mostrar['exportacion']=='SI'){
	$tipooperacion='0200'; 
	$totexport=$mostrar['txtTOTAL']; 
	$txtTOTAL_EXONERADAS='0.00';
	$pais=$mcliente['pais'];
}
/*
$percemastotal=0;
$porcentaje='0';	
if ($mostrar['percepcion']> 0) {
$porcentaje=$subtotal*0.02;
$percemastotal=round($porcentaje+$subtotal, 2);	
$tipooperacion='2001';
$porcentaje=round($porcentaje, 2);
}	
*/

$data = array(	
"txtTIPO_OPERACION"=>$tipooperacion,
"IDSUNAT"=>$idsunat,
"txtTOTAL_GRATUITAS"=>round($mostrar['gratuita'], 2),
"txtTOTAL_EXONERADAS"=>$txtTOTAL_EXONERADAS,
"txtTOTAL_EXPORTACION"=>$totexport,
"txtTOTAL_DESCUENTO"=>$mostrar['descuento'],
"txtTOTAL_INAFECTA"=>$mostrar['inafecta'],
"detalle_forma_pago"=>$cuerpocredito,
"totalcredito"=>$totalcredito,
"txtPOR_IGV"=> $fa['igv'],
"txtTOTAL_IGV"=>$igvfinal,
"txtTOTAL_GRAVADAS"=>$gravadas,
"txtSUB_TOTAL"=>$subtotal,
"txtTOTAL"=>$totalventa,
"txtNRO_OTR_COMPROBANTE"=>$mostrar['presupuesto'],
/*PERCEPCIONES"*/
"txtTOTAL_PERCEPCIONES"=>$mostrar['percepcion'],
"PERCEMASTOTAL"=>$percemastotal,
"PERCEPORCENTAJE"=>'0.02',
/*RETENCIONES"*/
"txtPOR_RETENCIONES"=>$porretenciones,
"txtBI_RETENCIONES"=>$totalretenciones,
"txtTOTAL_RETENCIONES"=>$retencionesn,
/*DETRACCIONES"*/
"txtCOD_MEDIO_PAGO"=>$detopreacion,
"txtCTA_BANCARIA_BN"=>$detcuenta,
"txtCODIGO_DETRACCION"=>$detcodigo,
"txtPOR_DETRACCION"=>$detporcentaje,
"txtTOTAL_DETRACCIONES" =>$dettotal, 
/*CUERPO DE ANTICIPOS*/
"totalanticipo"=> $totanticipo,
"subanticipo"=> $subanticipo,
"pagadoanticipo"=> $pagadoanticipo,
"NROACTICIPO"=>$cuerpoant,
/*GUIA REMISION*/
"txtNRO_GUIA_REMISION"=>$nguia,
"txtCOD_GUIA_REMISION"=>$codguia,
/*SIGUE DOCUMENTO*/
"txtTOTAL_LETRAS"=> numtoletras($mostrar['txtTOTAL'], $valmoneda), 
"txtNRO_COMPROBANTE"=> $mostrar['txtSERIE']."-".$mostrar['txtNUMERO'],
"txtFECHA_DOCUMENTO"=> date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])),
"txtFECHA_VTO"=> date("Y-m-d", strtotime($fvencimiento)),
"txtCOD_TIPO_DOCUMENTO"=> $mostrar['txtID_TIPO_DOCUMENTO'], //01=factura,03=boleta
"txtCOD_MONEDA"=> $mostrar['txtID_MONEDA'], //PEN=SOL USD= DOLAR EUR=EURO	
//==========documentos de referencia(nota credito, debito)=============
"txtTIPO_COMPROBANTE_MODIFICA"=> $mostrar['docmodifica_tipo'],
"txtNRO_DOCUMENTO_MODIFICA"=> $mostrar['docmodifica'],
"txtCOD_TIPO_MOTIVO"=> $mostrar['modifica_motivo'],
"txtDESCRIPCION_MOTIVO"=> $mostrar['modifica_motivod'], //$("[name='txtID_MOTIVO']
//=================datos del cliente=================
 "txtNRO_DOCUMENTO_CLIENTE"=>$mcliente['txtID_CLIENTE'],
 "txtRAZON_SOCIAL_CLIENTE"=>$mcliente['nombre'],
 "txtTIPO_DOCUMENTO_CLIENTE"=>$ndoc,
 "txtDIRECCION_CLIENTE"=>$mcliente['direccion'],
 "txtCIUDAD_CLIENTE"=>"",
 "txtCOD_PAIS_CLIENTE"=>$pais,
//=================datos de LA EMPRESA=================	
 "txtNRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
 "txtTIPO_DOCUMENTO_EMPRESA"=>"6",
 "txtNOMBRE_COMERCIAL_EMPRESA"=>$fa['nombre_comercial'],
 "txtCODIGO_UBIGEO_EMPRESA"=>$fa['ubigeo'],
 "txtDIRECCION_EMPRESA"=>$fa['direccion'],
 "txtDEPARTAMENTO_EMPRESA"=>$fa['departamento'],
 "txtPROVINCIA_EMPRESA"=>$fa['provincia'],
 "txtDISTRITO_EMPRESA"=>$fa['distrito'],
 "txtCODIGO_PAIS_EMPRESA"=>$fa['codpais'],
 "txtRAZON_SOCIAL_EMPRESA"=>$fa['razon_social'],
 "txtUSUARIO_SOL_EMPRESA"=>$fa['usuario'],
 "txtPASS_SOL_EMPRESA"=>$fa['clave'],
 "txtTIPO_PROCESO"=> $fa['tipo'],
 "PIN"=> $fa['firma'],
 "SUNAT"=> $fa['sunat'],
"detalle"=>$detalle,
);
//echo json_encode($data);
$resultado = $new->sendPostCPE(json_encode($data), RUTASUNAT);
//var_dump($resultado);
$me = json_decode($resultado, true);

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'DOCUMENTO GUARDADO SUNAT NO RESPONDIO';
	
if(isset($me)){
if($me['cod_sunat']=='0'){ 
$sql="UPDATE venta SET estado='2', hash_cpe='$me[hash_cpe]', hash_cdr='$me[hash_cdr]', mensaje='$me[msj_sunat]' WHERE idventa='$idventa' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $me['msj_sunat'];

}else{

$sql="UPDATE venta SET estado='1', hash_cpe='$me[hash_cpe]', hash_cdr='$me[hash_cdr]', mensaje='$me[msj_sunat]' WHERE idventa='$idventa' ";
ejecutarConsulta($sql);
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El documento fue enviado / '.$me['msj_sunat'].' / '.$me['cod_sunat'];
	
}
}
	
	
$jsondata['idventa'] =$idventa;
	
if($nivelenvio=='0'){
  echo json_encode($jsondata);
  exit();
}
return $jsondata;
}


function enviarpercepcion($id, $nivelenvio='0'){
	
$new = new api_sunat();
$cuerpocredito= array();
	
$sql="SELECT *FROM percepcion WHERE id='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$sqlsuc="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$sucur= ejecutarConsultaSimpleFila($sqlsuc);
	
$sql="SELECT *FROM percepcion_det WHERE idpercepcion='$id' ";
$rspta=ejecutarConsulta($sql);
$n=0;
while ($reg = $rspta->fetch_object()){
	
$n=$n+1;
	
$sql2="SELECT *FROM persona WHERE idpersona='$reg->idcliente' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
	
$ndoc='';
if($mcliente['tipo_documento']=='RUC'){ $ndoc='6'; }else if($mcliente['tipo_documento']=='DNI'){ $ndoc='1'; }

$json['txtITEM']=$n;
$json["TIPO_DOCUMENTO_CLIENTE"]=$ndoc;	
$json["NRO_DOCUMENTO_CLIENTE"]=$mcliente['txtID_CLIENTE'];	
$json["RAZON_SOCIAL_CLIENTE"]=$mcliente['nombre'];
$json["CODIGO_PERCEPCION"] ='01';
$json["TASA"] =$reg->porcentaje;
$json["DETALLE"] = "PERCEPCION DE ".$reg->serie.'-'.$reg->numero;
$json["PERCEPCION"] =$reg->percepcion;
$json["TOTAL"] =$reg->neto;
$json["SUBTOTAL"] =$reg->importe;
$json["TIPODOC"] =$reg->tipodocumento;
$json["SERIE"] =$reg->serie.'-'.$reg->numero;
$json["FECHA"] =$reg->fecha;	
$json["MONEDA"] =$reg->moneda;

$detalle[]=$json;
}
$idsunat='0000';
if($sucur['idsunat']!=''){
$idsunat=$sucur['idsunat'];
}	
	
$data = array(
"IDSUNAT"=>$idsunat,
"txtNRO_COMPROBANTE"=> $mostrar['serie']."-".$mostrar['numero'],
"txtFECHA_DOCUMENTO"=> date("Y-m-d", strtotime($mostrar['fecha'])),
"REGULAR"=>$mostrar['regular'],
"txtCOD_TIPO_DOCUMENTO"=>'40',	
//=================datos de LA EMPRESA=================	
 "txtNRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
 "txtTIPO_DOCUMENTO_EMPRESA"=>"6",
 "txtNOMBRE_COMERCIAL_EMPRESA"=>$fa['nombre_comercial'],
 "txtCODIGO_UBIGEO_EMPRESA"=>$fa['ubigeo'],
 "txtDIRECCION_EMPRESA"=>$fa['direccion'],
 "txtDEPARTAMENTO_EMPRESA"=>$fa['departamento'],
 "txtPROVINCIA_EMPRESA"=>$fa['provincia'],
 "txtDISTRITO_EMPRESA"=>$fa['distrito'],
 "txtCODIGO_PAIS_EMPRESA"=>$fa['codpais'],
 "txtRAZON_SOCIAL_EMPRESA"=>$fa['razon_social'],
 "txtUSUARIO_SOL_EMPRESA"=>$fa['usuario'],
 "txtPASS_SOL_EMPRESA"=>$fa['clave'],
 "txtTIPO_PROCESO"=> $fa['tipo'],
 "PIN"=> $fa['firma'],
 "SUNAT"=> $fa['sunat'],
"detalle"=>$detalle,
);
//echo json_encode($data);
$resultado = $new->sendPostCPE(json_encode($data), RUTASUNAT);
//var_dump($resultado);
$me = json_decode($resultado, true);
	

if($me['cod_sunat']=='0'){ 
$sql="UPDATE percepcion SET estado='2', hash_cpe='$me[hash_cpe]', hash_cdr='$me[hash_cdr]', mensaje='$me[msj_sunat]' WHERE id='$id' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $me['msj_sunat'];

}else{

$sql="UPDATE percepcion SET estado='1', hash_cpe='$me[hash_cpe]', hash_cdr='$me[hash_cdr]', mensaje='$me[msj_sunat]' WHERE idv='$id' ";
ejecutarConsulta($sql);
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El documento fue enviado / '.$me['msj_sunat'].' / '.$me['cod_sunat'];
	
}

$jsondata['idventa'] =$id;
	
if($nivelenvio=='0'){
  echo json_encode($jsondata);
  exit();
}
return $jsondata;
}






function enviarguia($id, $nivelenvio='0'){

$new = new api_sunat();	
	
$sql="SELECT *FROM guia_guia WHERE id='$id' ";
$guia= ejecutarConsultaSimpleFila($sql);
	
$subtotal='0';
	$total='0';
	$igv='0';
	$moneda='PEN';

if($guia['nivel']=='2'){

$sql="SELECT *FROM venta_orden WHERE id='$guia[iddoc_relacionado]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$subtotal=$mostrar['subtotal'];
	$total=$mostrar['total'];
	$igv=$mostrar['igv'];
	$moneda=$mostrar['moneda'];
}else{
$sql="SELECT *FROM venta WHERE idventa='$guia[iddoc_relacionado]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

if($mostrar){
$subtotal=$mostrar['txtSUB_TOTAL'];
	$total=$mostrar['txtTOTAL'];
	$igv=$mostrar['txtIGV'];
	$moneda=$mostrar['txtID_MONEDA'];
}	
}

$sql2="SELECT *FROM persona WHERE idpersona='$guia[idcliente]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);

$sql5="SELECT *FROM persona WHERE idpersona='$guia[remitenteid]' ";
$remitentes= ejecutarConsultaSimpleFila($sql5);

$sqlt="SELECT *FROM guia_transportista WHERE id='$guia[emptrans_id]' ";
$transp= ejecutarConsultaSimpleFila($sqlt);

$sql3="SELECT *FROM config WHERE id='$guia[idempresa]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
		
$sqlgre="SELECT *FROM gre WHERE idempresa='$guia[idempresa]' ";
$gre= ejecutarConsultaSimpleFila($sqlgre);

$sqlp="SELECT *FROM sucursal WHERE id='$guia[sucursal]' ";
$part= ejecutarConsultaSimpleFila($sqlp);

$sqld="SELECT *FROM sucursal WHERE id='$guia[destino]' ";
$dest= ejecutarConsultaSimpleFila($sqld);

$sqlv="SELECT *FROM guia_vehiculo WHERE id='$guia[idvehiculo]' ";
$vehi= ejecutarConsultaSimpleFila($sqlv);

$sqlch="SELECT *FROM guia_chofer WHERE id='$guia[idchofer]' ";
$cho= ejecutarConsultaSimpleFila($sqlch);

$sqlch2="SELECT *FROM guia_chofer WHERE id='$guia[idchofer2]' ";
$cho2= ejecutarConsultaSimpleFila($sqlch2);

$detalle = array();
$json = array();

$n=0;

$sql_detalle3 = "SELECT *FROM guia_detalle WHERE idguia='$guia[id]' ";
$rspta =ejecutarConsulta($sql_detalle3) or $sw = false;

while ($reg = $rspta->fetch_object()){			
$n=$n+1;

$json['ITEM']=$n;
$json["NUMERO_ORDEN"] ="";
$json["PESO"] =$reg->cantidad;//CANTIDAD DEL PRODUCTO
$json["CODIGO_PRODUCTO"] = $reg->codigoproducto;
$json["DESCRIPCION"] = $reg->nombreproducto;
$detalle[]=$json;	

}

if($remitentes['tipo_documento']=='RUC'){ $tipodocc='6'; }else{ $tipodocc='1'; }

$ndoc='0';
if($mcliente['tipo_documento']=='RUC'){ $ndoc='6'; }else if($mcliente['tipo_documento']=='DNI'){ $ndoc='1'; }

if($guia['tipo_transporteid']=='02'){
$ruc=$fa['ruc'];
$rsocial=$fa['razon_social'];
$tdoc="6";
}else{ 
$ruc=$transp['ruc'];
$rsocial=$transp['nombre'];
$tdoc="6";
}

$valmoneda='SOLES'; 

if($moneda=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($moneda=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($moneda=='EUR'){ $valmoneda='EUROS'; $mf='€'; }
	
$titdam='';

if($guia['docadicional']!=''){

if($guia['docadicional']=='50'){
$titdam='Declaración Aduanera de Mercancías';
}

if($guia['docadicional']=='09'){
$titdam='Guía de remisión-remitente';
}
	
}
	
$tipodchofer1='';
$numerochofer='';
$choferlicencia='';
$chofernombre='';
$choferapellido='';

if($cho2){
 
$tipodchofer1='1';
$numerochofer1=$cho2['docnumero'];
$choferlicencia1=$cho2['lisencia'];
$chofernombre1=$cho2['nombre'];
$choferapellido1=$cho2['apellido'];
    
}


$tarjetacirculacion='';
$mtcregistro='';

if($guia['tipodoc']=='31'){
$tarjetacirculacion=$vehi['tarjetacirculacion'];
$mtcregistro=$gre['mtcregistro'];
}

	
$data = array(
"CODIGO"=>$guia['tipodoc'],
"SERIE"=>$guia['serie'],
"SECUENCIA"=>$guia['numero'],
"NOTA"=> $guia['observacion'],
"FECHA_DOCUMENTO"=>date("Y-m-d", strtotime($guia['fecha'])),
"FECHA_TRANSPORTE"=>date("Y-m-d", strtotime($guia['fecha_transporte'])),
"PESO"=>$guia['peso'],
"NUMERO_PAQUETES"=>$guia['cajas'],
"txtTOTAL_GRAVADAS"=> $subtotal,
"txtSUB_TOTAL"=>$subtotal,
"txtPOR_IGV"=> "18.00", 
"txtTOTAL_IGV"=> $igv,
"txtTOTAL"=> $total,
"txtTOTAL_LETRAS"=> numtoletras($total, $valmoneda), 

"txtCOD_MONEDA"=> $moneda,
"CODMOTIVO_TRASLADO"=>$guia['motivoid'],
"MOTIVO_TRASLADO"=>$guia['motivo'],
"CODTIPO_TRANSPORTISTA"=>$guia['tipo_transporteid'],
//=================datos de PARTIDA=================
"UBIGEO_DESTINO"=>$dest['ubigeo'],
"DIR_DESTINO"=>$dest['direccion'],
//=================datos de DESTINO=================	
"UBIGEO_PARTIDA"=>$part['ubigeo'],
"DIR_PARTIDA"=>$part['direccion'],
//=================datos de VEHÍCULO=================	
"NRO_PLACA"=>$vehi['placa'],
"NRO_CARGA"=>$guia['ncarga'],
"PLACA_VEHICULO"=>$vehi['placa'],
"TARJETA_CIRCULACION"=>$tarjetacirculacion,
"PLACA_CARRETA"=>$guia['placacarreta'],
//=================datos de CHOFER=================		
"COD_TIPO_DOC_CHOFER"=>"1",
"NRO_DOC_CHOFER"=>$cho['docnumero'],
"chofer_lisencia"=>$cho['lisencia'],
"chofer_nombre"=>$cho['nombre'],
"chofer_apellido"=>$cho['apellido'],
//=================datos de CHOFER2=================
"COD_TIPO_DOC_CHOFER2"=>$tipodchofer1,
"NRO_DOC_CHOFER2"=>$numerochofer1,
"chofer_lisencia2"=>$choferlicencia1,
"chofer_nombre2"=>$chofernombre1,
"chofer_apellido2"=>$choferapellido1,
//=================datos del remitente=================
"TIPO_DOCUMENTO_REMITENTE"=>$tipodocc,
"NRO_DOCUMENTO_REMITENTE"=>$remitentes['txtID_CLIENTE'],
"RAZON_SOCIAL_REMITENTE"=>$remitentes['nombre'],
//=================datos del cliente=================
"TIPO_DOCUMENTO_CLIENTE"=>$ndoc,
"NRO_DOCUMENTO_CLIENTE"=>$mcliente['txtID_CLIENTE'],
"RAZON_SOCIAL_CLIENTE"=>$mcliente['nombre'],
//=================datos de LA TRANSPORTISTA=================
"NRO_DOCUMENTO_TRANSPORTE"=>$ruc,
"RAZON_SOCIAL_TRANSPORTE"=>$rsocial,
"TIPO_DOCUMENTO_TRANSPORTE"=>$tdoc,
//=================TRANSPORTE ESPECIAL=================	
"transbordo"=>$guia['transbordo'],
"vehiculo_m1l"=>$guia['vehiculo_m1l'],
//=================DOCUMENTOS ADICIONALES=================	
"DOCADICIONAL"=>$guia['docadicional'],
"DOCADICIONALNUM"=>$guia['docadicionalnum'],
"DOCADICIONALTIT"=>$titdam,
//=================datos de LA EMPRESA=================
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"TIPO_DOCUMENTO_EMPRESA"=>"6",
"RAZON_SOCIAL"=>$fa['razon_social'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"TIPO_PROCESO"=> $fa['tipo'],
"PIN"=>$fa['firma'],
"SUNAT"=> $fa['sunat'],
"sunatid"=>$gre['public'],
"sunatclave"=> $gre['secret'],
"PERMISO_CIRCULACION"=>$mtcregistro,
"detalle"=>$detalle,
);

//echo json_encode($data);
$resultado = $new->sendGuia(json_encode($data), RUTASUNAT);
//var_dump($resultado);

$me = json_decode($resultado, true);

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'EL COMPROBANTE FUE GUARDADO | FALTA ENVIAR A SUNAT';

if(isset($me['cod_sunat'])){

$mensaje = utf8_encode($me['msj_sunat']);

if($me['cod_sunat']=='0'){
$sql="UPDATE guia_guia SET estado='2', hash_cpe='$me[hash_cpe]', hash_cdr='$me[hash_cdr]', mensaje='$mensaje', ticket='$me[ticket]', rutaqr='$me[linkqr]' WHERE id='$id' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $me['msj_sunat'].' / DOCUMENTO GUARDADO';

	}else{
	    
$sql="UPDATE guia_guia SET estado='1', hash_cpe='$me[hash_cpe]', hash_cdr='$me[hash_cdr]', mensaje='$mensaje', ticket='$me[ticket]' WHERE id='$id' ";

//echo $sql;

		ejecutarConsulta($sql);
		$jsondata['estado'] = '1';
		$jsondata['mensaje'] = $me['msj_sunat'].' | DOCUMENTO GUARDADO | '.$me['cod_sunat'];
	}

}else{
$sql="UPDATE guia_guia SET estado='3' WHERE id='$id' ";
ejecutarConsulta($sql);
}
	
if($nivelenvio=='0'){
  echo json_encode($jsondata);
  exit();
}
return $jsondata;
}

}
?>