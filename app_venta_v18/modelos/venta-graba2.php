<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
date_default_timezone_set('America/Lima');
if (strlen(session_id()) < 1) 

header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

require "resumen.php";
require "envio.php";
require "caja.php";
require "kardex-procesos.php";
require_once "../funcionesGlobales/catalogo_afectaciones.php";

$jsondata = array();

if(!function_exists('vg2_table_has_column')){
function vg2_table_has_column($table, $col){
  static $cache = array();
  $k = $table.'|'.$col;
  if(isset($cache[$k])) return $cache[$k];

  $critical = array('cod_afectacion_igv','idcatalogo_afectacion','es_gratuito','es_bonificacion','es_publicidad','es_retiro','cod_tributo','porc_igv');
  $ok=false;
  $rs=ejecutarConsulta("DESCRIBE `".$table."`");
  if($rs){
    while($r=$rs->fetch_assoc()){
      if(isset($r['Field']) && $r['Field']===$col){ $ok=true; break; }
    }
  }
  if(!$ok){
    $rs2=ejecutarConsulta("SHOW COLUMNS FROM `".$table."` LIKE '".limpiarCadena($col)."'");
    if($rs2 && isset($rs2->num_rows) && (int)$rs2->num_rows>0){ $ok=true; }
  }
  if(!$ok){
    $tbl=limpiarCadena($table); $cl=limpiarCadena($col);
    $rs3=ejecutarConsulta("SELECT COUNT(*) AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='".$tbl."' AND COLUMN_NAME='".$cl."'");
    $rw=$rs3 ? $rs3->fetch_assoc() : null;
    if($rw && isset($rw['c']) && (int)$rw['c']>0){ $ok=true; }
  }
  if(!$ok && in_array($col,$critical,true)){ $ok=true; }

  $cache[$k]=$ok;
  return $ok;
}
}

if(!function_exists('vg2_afectacion_normalizada')){
function vg2_afectacion_normalizada($valor, $tipoLegacy='0'){
  $cod = caf_extraer_codigo($valor, '');
  if($cod===''){ $cod = caf_tipo_to_codigo($tipoLegacy); }
  if($cod===''){ $cod='10'; }
  return $cod;
}
}


$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';
$jsondata['numero'] = '00000000';	

//$array = explode("/", $_SERVER['REQUEST_URI']);
$bodyRequest = file_get_contents("php://input");
// Decodifica el cuerpo de la solicitud y lo guarda en un array de PHP
$cab = json_decode($bodyRequest, true);
$detalle = $cab['detalle'];
$hoy = date("Y-m-d");

$txtTOTAL_GRAVADAS=(isset($cab['txtTOTAL_GRAVADAS'])) ? $cab['txtTOTAL_GRAVADAS'] : "0";
$txtSUB_TOTAL=(isset($cab['txtSUB_TOTAL'])) ? $cab['txtSUB_TOTAL'] : "0";
$txtTOTAL_IGV=(isset($cab['txtTOTAL_IGV'])) ? $cab['txtTOTAL_IGV'] : "0";
$retencion=(isset($cab['retencion'])) ? $cab['retencion'] : "0";
$operacion=(isset($cab['operacion'])) ? $cab['operacion'] : "0";
$iddetraccion=(isset($cab['iddetraccion'])) ? $cab['iddetraccion'] : "0";
$montodetraccion=(isset($cab['montodetraccion'])) ? $cab['montodetraccion'] : "0";

$txtTOTAL_EXONERADAS=(isset($cab['txtTOTAL_EXONERADAS'])) ? $cab['txtTOTAL_EXONERADAS'] : "0.00";
$txtTOTAL_GRATUITAS=(isset($cab['txtTOTAL_GRATUITAS'])) ? $cab['txtTOTAL_GRATUITAS'] : "0.00";
$txtPAGO=(isset($cab['txtPAGO'])) ? $cab['txtPAGO'] : "CONTADO";
$mediopago=(isset($cab['mediopago'])) ? $cab['mediopago'] : "008";
$txtTOTAL=(isset($cab['txtTOTAL'])) ? $cab['txtTOTAL'] : "0";
$totdescuento=(isset($cab['totdescuento'])) ? $cab['totdescuento'] : "0";

$txtSERIE=(isset($cab['txtSERIE'])) ? $cab['txtSERIE'] : "0";
$txtNUMERO=(isset($cab['txtNUMERO'])) ? $cab['txtNUMERO'] : "0";
$txtFECHA_DOCUMENTO=(isset($cab['txtFECHA_DOCUMENTO'])) ? $cab['txtFECHA_DOCUMENTO'] : "0";
$txtCOD_TIPO_DOCUMENTO=(isset($cab['txtCOD_TIPO_DOCUMENTO'])) ? $cab['txtCOD_TIPO_DOCUMENTO'] : "0";
$txtCOD_MONEDA=(isset($cab['txtCOD_MONEDA'])) ? $cab['txtCOD_MONEDA'] : "PEN";
$cliente=(isset($cab['cliente'])) ? $cab['cliente'] : "0";
$txtOBSERVACION=(isset($cab['txtOBSERVACION'])) ? $cab['txtOBSERVACION'] : "0";
$idlocal=$_COOKIE["idlocal"];
$vendedor=(isset($cab['vendedor'])) ? $cab['vendedor'] : "";

$tipodoc=(isset($cab['tipodoc'])) ? $cab['tipodoc'] : "";
$modifica=(isset($cab['modifica'])) ? $cab['modifica'] : "0";
$motivo=(isset($cab['motivo'])) ? $cab['motivo'] : "0";
$motivod=(isset($cab['motivod'])) ? $cab['motivod'] : "0";
$smodifica=(isset($cab['smodifica'])) ? $cab['smodifica'] : "0";
$nmodifica=(isset($cab['nmodifica'])) ? $cab['nmodifica'] : "0";
$idventa=(isset($cab['idventa'])) ? $cab['idventa'] : "0";
$seriedoc=(isset($cab['seriedoc'])) ? $cab['seriedoc'] : "0";
$directo=(isset($cab['directo'])) ? $cab['directo'] : "1";

$idcategoria=(isset($cab['idcategoria'])) ? $cab['idcategoria'] : "0";
$condiciones=(isset($cab['condiciones'])) ? $cab['condiciones'] : "0";
$comisiont=(isset($cab['comisiont'])) ? $cab['comisiont'] : "0";
$icb=(isset($cab['icb'])) ? $cab['icb'] : "0";
$serieanticipo=(isset($cab['serieanticipo'])) ? $cab['serieanticipo'] : "";
$idanticipo=(isset($cab['idanticipo'])) ? $cab['idanticipo'] : "0";

$guia=(isset($cab['guia'])) ? $cab['guia'] : "";
$notpedido=(isset($cab['notpedido'])) ? $cab['notpedido'] : "0";
$tipoguia=(isset($cab['tipoguia'])) ? $cab['tipoguia'] : "09";

$ocompra=(isset($cab['ocompra'])) ? $cab['ocompra'] : "0";
$puntos=(isset($cab['puntos'])) ? $cab['puntos'] : "0";
$percepcion=(isset($cab['percepcion'])) ? $cab['percepcion'] : "0";

$txtSUB_TOTAL=abs($txtSUB_TOTAL);

$fechacambio=$txtFECHA_DOCUMENTO;
$periodo=date("Y-m",strtotime($txtFECHA_DOCUMENTO));

$jsondata['seriefinal']=$txtSERIE;

$rspta=ejecutarConsulta("SELECT *FROM detalle_venta WHERE idventa='$idventa' ");
while ($reg = $rspta->fetch_object()){	
$sql="UPDATE articulo_stock SET stock=stock+$reg->txtCANTIDAD_ARTICULO WHERE idarticulo='$reg->idproducto' AND idlocal='$_COOKIE[idlocal]' ";
ejecutarConsulta($sql);	
}

$sqlc="DELETE FROM detalle_venta WHERE idventa='$idventa' ";
ejecutarConsulta($sqlc);
	
$sqlc="DELETE FROM cardex_detalle WHERE iddocumento='$idventa' AND operacion='01' AND tipooperacion='2' AND nivel='2' ";
ejecutarConsulta($sqlc);

if($notpedido!=''){ $serieanticipo=$notpedido; }
$tarjeta=(isset($cab['tarjeta'])) ? $cab['tarjeta'] : "0";
$txtSUB_TOTAL=$txtTOTAL_GRAVADAS;
$txtSUB_TOTAL=abs($txtSUB_TOTAL);
$vasunat="0";
$txtFECHA_DOCUMENTO=$txtFECHA_DOCUMENTO.' '.date('H:i:s');
$fechavto=date("Y-m-d",strtotime($txtFECHA_DOCUMENTO."+ ".$condiciones." days"));



$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

if($vendedor!=''){ 
$sqlv="SELECT *FROM usuario WHERE idusuario='$vendedor' ";
$vendedor= ejecutarConsultaSimpleFila($sqlv);
$idusuario=$vendedor['idusuario'];
}else{
$idusuario=$_COOKIE["idusuario"];
}

$sqlvent="SELECT *FROM venta WHERE idventa='$idventa' ";
$conventa= ejecutarConsultaSimpleFila($sqlvent);

if($conventa['tipo_pago']=='CONTADO'&&$conventa['medio_pago']=='008'){
$totrestar=$conventa['txtTOTAL']-$conventa['tarjeta'];
agregarcaja($totrestar, 'RESTA', 'ENTRADA', $idusuario);	
}

$sqlcat="SELECT *FROM tipo_cambio WHERE fecha='$fechacambio' AND idempresa='0' ";
$tchoy= ejecutarConsultaSimpleFila($sqlcat);

if(!$tchoy){
$sqlcat="SELECT *FROM tipo_cambio ORDER BY fecha DESC ";
$tchoy= ejecutarConsultaSimpleFila($sqlcat);	
}

if($operacion=='1'){
	
if($txtCOD_MONEDA=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($txtCOD_MONEDA=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='US$'; }
if($txtCOD_MONEDA=='EUR'){ $valmoneda='EUROS'; $mf='€'; }
$totalnet=round($txtTOTAL-$retencion, 2);
$txtOBSERVACION=$txtOBSERVACION.' | RETENCIÓN 3%: '.$mf.' '.$retencion.'; NETO A PAGAR: '.$mf.' '.$totalnet;	
}
	
if($txtCOD_TIPO_DOCUMENTO=='07'||$txtCOD_TIPO_DOCUMENTO=='08'){
	
$sql="SELECT *FROM venta WHERE txtSERIE='$smodifica' AND txtNUMERO='$nmodifica' AND idempresa='$_COOKIE[id]' AND (txtID_TIPO_DOCUMENTO='01' OR txtID_TIPO_DOCUMENTO='03' ) AND beta='$fa[tipo]' ";
$mostrarv= ejecutarConsultaSimpleFila($sql);
$cliente=$mostrarv['txtID_CLIENTE'];
$txtCOD_MONEDA=$mostrarv['txtID_MONEDA'];

}	


if($cliente==''){ $cliente='0'; }
if($idlocal==''){ $idlocal='0'; }
if($txtTOTAL_GRATUITAS==''){ $txtTOTAL_GRATUITAS='0'; }
if($txtTOTAL_EXONERADAS==''){ $txtTOTAL_EXONERADAS='0'; }
if($comisiont==''){ $comisiont='0'; }	
	
$costodolar=$tchoy['venta'];
	
$sql3="SELECT *FROM persona WHERE idpersona='$cliente' ";
$cli= ejecutarConsultaSimpleFila($sql3);
	

/*GUARDAMOS PUNTOS Y SUMAMOS PUNTOS*/
if($puntos!='0'){
	
$sql="INSERT INTO venta_puntos VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '0', '$idlocal', '$idventa', '$cliente', '$puntos', '$txtFECHA_DOCUMENTO')";
ejecutarConsulta($sql);
	
$sqlp="UPDATE persona SET puntos=puntos+$puntos WHERE idpersona='$cliente' ";
ejecutarConsulta($sqlp);
	
}	
/*VERIFICAMOS EL DOCUMENTO RELACIONADO  A LA GUIA*/
if($guia!=''){
	
$porciones = explode("-", $guia);
$serie=$porciones[0];
$numero=$porciones[1];
	
$sqlsgui="SELECT *FROM guia_guia WHERE serie='$serie' AND numero='$numero' AND idempresa='$_COOKIE[id]' ";
$sguia= ejecutarConsultaSimpleFila($sqlsgui);
	
$sqlca="UPDATE venta SET estado='2' WHERE idventa='$sguia[iddoc_relacionado]' AND txtID_TIPO_DOCUMENTO='92' ";
ejecutarConsulta($sqlca);

}
	
$tipodoc2= $txtCOD_TIPO_DOCUMENTO;
$tipodocf=$txtCOD_TIPO_DOCUMENTO;
	
if($txtCOD_TIPO_DOCUMENTO=='01'||$txtCOD_TIPO_DOCUMENTO='03'||$txtCOD_TIPO_DOCUMENTO='90'){

if($txtPAGO=='CONTADO'&&$mediopago=='008'){
$txtTOTAL=$txtTOTAL-$tarjeta;
agregarcaja($txtTOTAL, 'SUMA', 'ENTRADA', $idusuario);	
}

$jsondata['pago'] =$txtPAGO;
$jsondata['total'] =$txtTOTAL;
$jsondata['usuario'] =$idusuario;
$jsondata['idempresa'] =$_COOKIE['id'];
	
}


if($idventa!='0'){

$sqlup="UPDATE venta SET idusuario='$idusuario', txtFECHA_DOCUMENTO='$txtFECHA_DOCUMENTO', fecha_vto='$fechavto', txtOBSERVACION='$txtOBSERVACION', txtSUB_TOTAL='$txtSUB_TOTAL', txtIGV='$txtTOTAL_IGV', percepcion='$percepcion', retencion='$retencion', iddetraccion='$iddetraccion', detraccion='$montodetraccion',  ICB='$icb', descuento='$totdescuento', txtTOTAL='$txtTOTAL', gratuita='$txtTOTAL_GRATUITAS', exonerado='$txtTOTAL_EXONERADAS', comision='$comisiont', tarjeta='$tarjeta', tipocambio='$costodolar', txtID_MONEDA='$txtCOD_MONEDA', tipo_pago='$txtPAGO', medio_pago='$mediopago', tipoguia='$tipoguia', guia='$guia', presupuesto='$ocompra', referencia='$serieanticipo', condiciones='$condiciones', estadopago='$idanticipo'  WHERE idventa='$idventa' ";
ejecutarConsulta($sqlup);
}else{
	
$sqls="SELECT *FROM series WHERE serie='$txtSERIE' AND tipo='02' AND estado='1' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
$ser= ejecutarConsultaSimpleFila($sqls);
//POR SI ES NOTA DE CREDITO O DEBITO
if($txtCOD_TIPO_DOCUMENTO=='07'||$txtCOD_TIPO_DOCUMENTO=='08'){	
$sqls="SELECT *FROM series WHERE documento='$txtCOD_TIPO_DOCUMENTO' AND tipo='02' AND estado='1' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
$ser= ejecutarConsultaSimpleFila($sqls);
}
	
$sqlnn="SELECT *FROM venta WHERE txtSERIE='$txtSERIE' AND txtID_TIPO_DOCUMENTO='$txtCOD_TIPO_DOCUMENTO' AND idempresa='$_COOKIE[id]' AND beta='$fa[tipo]' ORDER BY txtNUMERO DESC, txtFECHA_DOCUMENTO  DESC,  idventa DESC ";
$mostrarnn= ejecutarConsultaSimpleFila($sqlnn);

$snum=$mostrarnn['txtNUMERO']+1;
if($mostrarnn['txtNUMERO']==''){ $snum=$ser['numeroinicio']; }
$snum=str_pad($snum, 8, "0", STR_PAD_LEFT);
	
if($txtCOD_TIPO_DOCUMENTO=='07'||$txtCOD_TIPO_DOCUMENTO=='08'){
	
$sql="SELECT *FROM venta WHERE txtSERIE='$smodifica' AND txtNUMERO='$nmodifica' AND idempresa='$_COOKIE[id]' AND (txtID_TIPO_DOCUMENTO='01' OR txtID_TIPO_DOCUMENTO='03' ) AND beta='$fa[tipo]' ";
$mostrarv= ejecutarConsultaSimpleFila($sql);
$cliente=$mostrarv['txtID_CLIENTE'];
$txtCOD_MONEDA=$mostrarv['txtID_MONEDA'];

}

if($cliente==''){ $cliente='0'; }
if($idlocal==''){ $idlocal='0'; }
if($txtTOTAL_GRATUITAS==''){ $txtTOTAL_GRATUITAS='0'; }
if($txtTOTAL_EXONERADAS==''){ $txtTOTAL_EXONERADAS='0'; }
if($comisiont==''){ $comisiont='0'; }	

$act='0';
	
$sql3="SELECT *FROM persona WHERE idpersona='$cliente' ";
$cli= ejecutarConsultaSimpleFila($sql3);

$sql="INSERT INTO venta VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$act', '$idcategoria', '$cliente', '$idusuario', '$idlocal', '$txtCOD_TIPO_DOCUMENTO', '$modifica', '$tipodoc', '$modifica', '$motivo', '$motivod', '$txtSERIE', '$snum', '$txtFECHA_DOCUMENTO', '$fechavto', '$txtOBSERVACION', '$txtSUB_TOTAL', '$txtTOTAL_IGV', '$percepcion', '$retencion', '$iddetraccion', '$montodetraccion', '$icb', '$totdescuento', '$txtTOTAL', '$txtTOTAL_GRATUITAS', '$txtTOTAL_EXONERADAS', '0', '$comisiont', '$tarjeta', '$costodolar', '$txtCOD_MONEDA', '$txtPAGO', '$mediopago', '', '$tipoguia', '$guia', '$ocompra', '$serieanticipo', '$condiciones', '', '', '', '0', '$idanticipo')";
	
$idventa=ejecutarConsulta_retornarID($sql);
	
}



//var_dump($detalle);
for ($i = 0; $i < count($detalle); $i++) {
	
$id=(isset($detalle[$i]["txtID"]))?$detalle[$i]["txtID"]:"0";
$tipo=(isset($detalle[$i]["tipo"]))?$detalle[$i]["tipo"]:"0";
$serie=(isset($detalle[$i]["serie"]))?$detalle[$i]["serie"]:"0";
$proveedor=(isset($detalle[$i]["proveedor"]))?$detalle[$i]["proveedor"]:"0";
$codigo=(isset($detalle[$i]["txtCODIGO_DET"]))?$detalle[$i]["txtCODIGO_DET"]:"0";
$nombre=(isset($detalle[$i]["txtDESCRIPCION_DET"]))?$detalle[$i]["txtDESCRIPCION_DET"]:"0";
$cti=(isset($detalle[$i]["txtCANTIDAD_DET"]))?$detalle[$i]["txtCANTIDAD_DET"]:"0";
$importe=(isset($detalle[$i]["txtIMPORTE_DET"]))?$detalle[$i]["txtIMPORTE_DET"]:"0";	
$igv=(isset($detalle[$i]["txtIGV"]))?$detalle[$i]["txtIGV"]:"0";
$precio=(isset($detalle[$i]["txtPRECIO"]))?$detalle[$i]["txtPRECIO"]:"0";
$descuento=(isset($detalle[$i]["descuento"]))?$detalle[$i]["descuento"]:"0";
$placa=(isset($detalle[$i]["placa"]))?$detalle[$i]["placa"]:"";
$ctiunidad=(isset($detalle[$i]["ctiunidad"]))?$detalle[$i]["ctiunidad"]:"0";
$comision=(isset($detalle[$i]["comision"]))?$detalle[$i]["comision"]:"0";
$UNIDAD_MEDIDA=(isset($detalle[$i]["UNIDAD_MEDIDA"]))?$detalle[$i]["UNIDAD_MEDIDA"]: "0";
$idunit=(isset($detalle[$i]["idunit"]))?$detalle[$i]["idunit"]: "0";
$idv=(isset($detalle[$i]["idv"]))?$detalle[$i]["idv"]: "0";
$tipoart=(isset($detalle[$i]["tipoart"]))?$detalle[$i]["tipoart"]: "0";

$exonerado=(isset($detalle[$i]["exonerado"]))?$detalle[$i]["exonerado"]: "0";
$gratuita=(isset($detalle[$i]["gratuita"]))?$detalle[$i]["gratuita"]: "0";
$icbd=(isset($detalle[$i]["icbd"]))?$detalle[$i]["icbd"]: "0";
	
if($gratuita==''){ $gratuita='0'; }
if($exonerado==''){ $exonerado='0'; }
if($descuento==''){ $descuento='0'; }
if($idunit==''){ $idunit='0'; }
if($ctiunidad==''){ $ctiunidad='0'; }
if($cti==''){ $cti='0'; }
if($descuento==''){ $descuento='0'; }
	
$subtotal=$importe-$igv;	
$stock='0.00';
	
if($icbd==''){ $icbd='0'; }
if($UNIDAD_MEDIDA==''){ $UNIDAD_MEDIDA='UND'; }
if($idunit==''){ $idunit='0'; }
if($cti==''){ $cti='0'; }
if($importe==''){ $importe='0'; }
if($ctiunidad==''){ $ctiunidad='0'; }
if($precio==''){ $precio='0'; }
if($descuento==''){ $descuento='0'; }
if($subtotal==''){ $subtotal='0'; }
if($igv==''){ $igv='0'; }
if($exonerado==''){ $exonerado='0'; }
if($gratuita==''){ $gratuita='0'; }
if($comision==''){ $comision='0'; }
if($stock==''){ $stock='0'; }
if($tipo==''){ $tipo='0'; }
if($serie==''){ $serie='0'; }

// WHERE idarticulo='$id' AND idlocal='$idlocal'
if($ctiunidad=='0'){ $ctit=$cti; }else{ $ctit=$ctiunidad*$cti; }		
$stock='0';
if($tipoart=='1'){
$idunit='1';
}
	
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$fechakardex=$fechacambio.' '.date('H:i:s.'.$micro);
	
$codAfectacion = vg2_afectacion_normalizada(isset($detalle[$i]["cod_afectacion_igv"])?$detalle[$i]["cod_afectacion_igv"]:'', $tipo);
$confAfectacion = caf_get_codigo_conf($codAfectacion, isset($_COOKIE['id']) ? (int)$_COOKIE['id'] : 0);
$codAfectacion = isset($confAfectacion['codigo']) && trim((string)$confAfectacion['codigo'])!=='' ? trim((string)$confAfectacion['codigo']) : $codAfectacion;
$codTributo = isset($detalle[$i]["cod_tributo"]) && trim((string)$detalle[$i]["cod_tributo"])!=='' ? trim((string)$detalle[$i]["cod_tributo"]) : (string)$confAfectacion['cod_tributo'];
$porcIgv = isset($detalle[$i]["porc_igv"]) && trim((string)$detalle[$i]["porc_igv"])!=='' ? number_format((float)$detalle[$i]["porc_igv"],2,'.','') : number_format((float)$confAfectacion['porcentaje_igv'],2,'.','');

$esNoOnerosa = ($codTributo==='9996') ? true : (isset($confAfectacion['es_onerosa']) ? ((int)$confAfectacion['es_onerosa']===0) : in_array($codAfectacion, array('11','12','13','14','15','16','21','31','32','33','34','35','36'), true));
$requiereRef = ($codTributo==='9996') ? true : (isset($confAfectacion['requiere_valor_referencial']) ? ((int)$confAfectacion['requiere_valor_referencial']===1) : $esNoOnerosa);
$valorUnitRef = $requiereRef ? (float)$precio : 0;
$valorTotalRef = $requiereRef ? round(((float)$cti*(float)$valorUnitRef),2) : 0;
$igvRef = (in_array($codAfectacion, array('11','12','13','14','15','16'), true)) ? round($valorTotalRef * ((float)$porcIgv/100),2) : 0;
$codigoTipoPrecio = ($codTributo==='9996') ? '02' : (isset($confAfectacion['codigo_tipo_precio']) ? trim((string)$confAfectacion['codigo_tipo_precio']) : ($requiereRef ? '02' : '01'));
$idcatalogoAfect = isset($confAfectacion['id']) ? (int)$confAfectacion['id'] : 0;
$versionUbl = isset($confAfectacion['version_ubl']) ? trim((string)$confAfectacion['version_ubl']) : '2.1';
$codigoLeyenda = isset($confAfectacion['codigo_leyenda']) ? trim((string)$confAfectacion['codigo_leyenda']) : ($requiereRef ? '1002' : '');
$afecta1004 = isset($confAfectacion['requiere_total_1004']) ? (int)$confAfectacion['requiere_total_1004'] : ($requiereRef ? 1 : 0);
$baseImponibleXml = (float)$subtotal;
$montoTributoXml = (float)$igv;
$valorUnitarioXml = ((float)$cti>0) ? ((float)$subtotal/(float)$cti) : 0;
$precioUnitarioXml = ((float)$cti>0) ? ((float)$importe/(float)$cti) : 0;
$valorVentaXml = (float)$subtotal;

$cols = array('iddetalle_venta','idempresa','idlocal','beta','idventa','tipoarticulo','idproducto','codigoproducto','unidadmedida','idpresentacion','nombreproducto','txtCANTIDAD_ARTICULO','cantidadp','precio','preciocompra','descuento','subtotal','igv','ICB','importe','exoneradod','gratuitad','comisiond','stock','tipo','anticipo','doc_anticipo','placa','idlote','idproveedor','fecha');
$vals = array("NULL", "'$_COOKIE[id]'", "'$_COOKIE[idlocal]'", "'$fa[tipo]'", "'$idventa'", "'$tipoart'", "'$id'", "'$codigo'", "'$UNIDAD_MEDIDA'", "'$idunit'", "'$nombre'", "'$cti'", "'$ctiunidad'", "'$precio'", "'0'", "'$descuento'", "'$subtotal'", "'$igv'", "'$icbd'", "'$importe'", "'$exonerado'", "'$gratuita'", "'$comision'", "'0'", "'$tipo'", "'0'", "''", "'$placa'", "'$serie'", "'$proveedor'", "'$fechakardex'");

if(true){ $cols[]='cod_afectacion_igv'; $vals[]="'$codAfectacion'"; }
if(true){ $cols[]='cod_tributo'; $vals[]="'$codTributo'"; }
if(true){ $cols[]='porc_igv'; $vals[]="'$porcIgv'"; }
if(true){ $cols[]='es_gratuito'; $vals[]="'".($esNoOnerosa?1:0)."'"; }
if(true){ $cols[]='es_bonificacion'; $vals[]="'".((isset($confAfectacion['es_bonificacion'])?(int)$confAfectacion['es_bonificacion']:($codAfectacion==='15'||$codAfectacion==='31'?1:0)))."'"; }
if(true){ $cols[]='es_publicidad'; $vals[]="'".((isset($confAfectacion['es_publicidad'])?(int)$confAfectacion['es_publicidad']:($codAfectacion==='14'||$codAfectacion==='36'?1:0)))."'"; }
if(true){ $cols[]='es_retiro'; $vals[]="'".((isset($confAfectacion['es_retiro'])?(int)$confAfectacion['es_retiro']:0))."'"; }
if(vg2_table_has_column('detalle_venta','valor_unitario_ref')){ $cols[]='valor_unitario_ref'; $vals[]="'".number_format($valorUnitRef,4,'.','')."'"; }
if(vg2_table_has_column('detalle_venta','igv_ref')){ $cols[]='igv_ref'; $vals[]="'".number_format($igvRef,2,'.','')."'"; }
if(vg2_table_has_column('detalle_venta','valor_total_ref')){ $cols[]='valor_total_ref'; $vals[]="'".number_format($valorTotalRef,2,'.','')."'"; }
if(true){ $cols[]='idcatalogo_afectacion'; $vals[]="'$idcatalogoAfect'"; }
if(vg2_table_has_column('detalle_venta','version_ubl')){ $cols[]='version_ubl'; $vals[]="'$versionUbl'"; }
if(vg2_table_has_column('detalle_venta','codigo_tipo_precio')){ $cols[]='codigo_tipo_precio'; $vals[]="'$codigoTipoPrecio'"; }
if(vg2_table_has_column('detalle_venta','base_imponible_xml')){ $cols[]='base_imponible_xml'; $vals[]="'".number_format($baseImponibleXml,2,'.','')."'"; }
if(vg2_table_has_column('detalle_venta','monto_tributo_xml')){ $cols[]='monto_tributo_xml'; $vals[]="'".number_format($montoTributoXml,2,'.','')."'"; }
if(vg2_table_has_column('detalle_venta','valor_unitario_xml')){ $cols[]='valor_unitario_xml'; $vals[]="'".number_format($valorUnitarioXml,10,'.','')."'"; }
if(vg2_table_has_column('detalle_venta','precio_unitario_xml')){ $cols[]='precio_unitario_xml'; $vals[]="'".number_format($precioUnitarioXml,10,'.','')."'"; }
if(vg2_table_has_column('detalle_venta','valor_venta_xml')){ $cols[]='valor_venta_xml'; $vals[]="'".number_format($valorVentaXml,2,'.','')."'"; }
if(vg2_table_has_column('detalle_venta','codigo_leyenda')){ $cols[]='codigo_leyenda'; $vals[]="'$codigoLeyenda'"; }
if(vg2_table_has_column('detalle_venta','afecta_total_1004')){ $cols[]='afecta_total_1004'; $vals[]="'$afecta1004'"; }

$sql_detalle = "INSERT INTO detalle_venta (".implode(',',$cols).") VALUES (".implode(',',$vals).")";

$idvent=ejecutarConsulta_retornarID($sql_detalle) or $sw = false;
	
$sqlart="SELECT *FROM articulo WHERE txtCOD_ARTICULO='$id' ";
$art= ejecutarConsultaSimpleFila($sqlart);

if($tipo=='2'){	
if($art['canje']=='SI'){
if($cli['puntos']>=$art['canjecobro']){

$sql="INSERT INTO venta_puntos VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '1', '$idlocal', '$idventanew', '$cliente', '$puntos', '$txtFECHA_DOCUMENTO')";
ejecutarConsulta($sql);
	
$sqlp="UPDATE persona SET puntos=puntos-$puntos WHERE idpersona='$cliente' ";
ejecutarConsulta($sqlp);
	
}
}}
	
guardakardex($id, $idventa, $idvent, $tipodocf, $txtSERIE, $txtNUMERO, $ctit, $ctiunidad, $fechacambio, $UNIDAD_MEDIDA, $tipoart, $fa['tipo'], $serie, $periodo, $tipo, $txtCOD_MONEDA, $precio, $costodolar, $cliente, $idusuario, $nombre, $motivo); 
	
}
		
$jsondata['estado'] = '1';
$jsondata['mensaje'] = "DOCUMENTO GUARDADO / FALTA ENVIAR A SUNAT";
$jsondata['txtPAGO'] = $txtPAGO;
$jsondata['directo'] = $directo;

//'$txtPAGO', '$mediopago',
if($txtPAGO=='CONTADO'){

if($tipodoc2!='90'){
$vasunat="1";
enviarfactura($idventa);
}

}	

	
$jsondata['idventa'] = $idventa;
echo json_encode($jsondata);
exit();	

?>