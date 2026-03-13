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


function normalizarFechaYmd($valor, $fallback=''){
$valor=trim((string)$valor);
$fallback=trim((string)$fallback);
if($fallback===''){ $fallback=date('Y-m-d'); }

if($valor==='' || $valor==='0000-00-00' || $valor==='0000-00-00 00:00:00'){
return $fallback;
}

if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)){
return $valor;
}

if(preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}(:\d{2})?$/', $valor)){
return substr($valor,0,10);
}

if(preg_match('/^(\d{2})[\/-](\d{2})[\/-](\d{4})$/', $valor, $m)){
return $m[3].'-'.$m[2].'-'.$m[1];
}

$ts=strtotime($valor);
if($ts===false){
return $fallback;
}

$fecha=date('Y-m-d',$ts);
if($fecha<'2000-01-01'){
return $fallback;
}

return $fecha;
}


function num_str($v){
  // Normaliza números tipo "1,000.00" -> "1000.00"
  if($v===null){ return '0'; }
  $s = trim((string)$v);
  if($s===''){ return '0'; }
  // espacios (incluye NBSP)
  $s = str_replace(["\xc2\xa0", ' '], '', $s);
  // paréntesis para negativos: (123.45) -> -123.45
  if(preg_match('/^\((.+)\)$/', $s, $m)){ $s='-'.$m[1]; }
  // quita separador de miles
  $s = str_replace(',', '', $s);
  // deja solo dígitos, punto y signo
  $s = preg_replace('/[^0-9\.\-]/', '', $s);
  if($s==='' || $s==='-' || $s==='.'){ return '0'; }
  return $s;
}

function num_fix($v, $dec=2){
  $n = (float)num_str($v);
  // number_format: asegura punto decimal y SIN separador de miles
  return number_format($n, (int)$dec, '.', '');
}

function guardarErrorJson($mensaje){
  $jsondata=array('estado'=>'0','mensaje'=>$mensaje,'numero'=>'00000000');
  echo json_encode($jsondata);
  exit();
}

function mapTipoToAfectacion($tipo){
  return caf_tipo_to_codigo($tipo);
}

function tributoPorAfectacion($cod){
  $idempresa = isset($_COOKIE['id']) ? (int)$_COOKIE['id'] : 0;
  $conf = caf_get_codigo_conf($cod, $idempresa);
  return array((string)$conf['cod_tributo'], number_format((float)$conf['porcentaje_igv'], 2, '.', ''));
}
function existeTabla($tabla){
  $tabla=preg_replace('/[^a-zA-Z0-9_]/','',$tabla);
  if($tabla===''){ return false; }
  $sql="SHOW TABLES LIKE '$tabla'";
  $rs=ejecutarConsulta($sql);
  if(!$rs){ return false; }
  return ($rs->num_rows>0);
}


function gv_table_has_column($table,$col){
  static $cache=array();
  $k=$table.'|'.$col;
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

  if(!$ok && in_array($col,$critical,true)){
    // En instalaciones con permisos restringidos a metadata, no ocultar columnas críticas.
    $ok=true;
  }

  $cache[$k]=$ok;
  return $ok;
}

// ======================================================
// SUNAT - Redondeo detracción a la UNIDAD
// Regla:
// - Si la fracción < 0.5 => se suprime decimal
// - Si la fracción >= 0.5 => se ajusta a la unidad inmediata superior
// Nota: Se aplica sobre valores >= 0 (detracción)
// ======================================================
function sunat_round_unidad($v){
  $n = (float)num_str($v);
  if(!is_finite($n) || $n <= 0){ return 0; }
  $ent = floor($n);
  $frac = $n - $ent;
  if($frac >= 0.5){ $ent = $ent + 1; }
  return (int)$ent;
}



$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';
$jsondata['numero'] = '00000000';
$hoy = date("Y-m-d");

$txtTOTAL_GRAVADAS=(isset($_POST['gravadas'])) ? $_POST['gravadas'] : "0";
$txtSUB_TOTAL=(isset($_POST['txtSUB_TOTAL'])) ? $_POST['txtSUB_TOTAL'] : "0";
$txtTOTAL_IGV=(isset($_POST['txtIGV'])) ? $_POST['txtIGV'] : "0";
$txtTOTAL=(isset($_POST['txtTOTAL'])) ? $_POST['txtTOTAL'] : "0";
$txtTOTAL_EXONERADAS=(isset($_POST['exoneradof'])) ? $_POST['exoneradof'] : "0.00";
$txtTOTAL_GRATUITAS=(isset($_POST['gratuita'])) ? $_POST['gratuita'] : "0.00";

$retencion=(isset($_POST['retencion'])) ? $_POST['retencion'] : "0";
$operacion=(isset($_POST['operacion'])) ? $_POST['operacion'] : "0";
$iddetraccion=(isset($_POST['iddetraccion'])) ? $_POST['iddetraccion'] : "0";
$montodetraccion=(isset($_POST['montodetraccion'])) ? $_POST['montodetraccion'] : "0";

$txtPAGO=(isset($_POST['pago'])) ? $_POST['pago'] : "CONTADO";
$mediopago=(isset($_POST['medio'])) ? $_POST['medio'] : "008";
$totdescuento=(isset($_POST['descuentotot'])) ? $_POST['descuentotot'] : "0";
$dto_global_tipo=(isset($_POST['dto_global_tipo'])) ? $_POST['dto_global_tipo'] : "MONTO";
$dto_global_valor=(isset($_POST['dto_global_valor'])) ? $_POST['dto_global_valor'] : "0";
$dto_global_monto_base_post=(isset($_POST['dto_global_monto_base'])) ? $_POST['dto_global_monto_base'] : "0";
$dto_global_monto_igv_post=(isset($_POST['dto_global_monto_igv'])) ? $_POST['dto_global_monto_igv'] : "0";
$dto_global_aplica_antes_igv=(isset($_POST['dto_global_aplica_antes_igv'])) ? $_POST['dto_global_aplica_antes_igv'] : "1";

$txtSERIE=(isset($_POST['txtSERIE'])) ? $_POST['txtSERIE'] : "0";
$txtNUMERO=(isset($_POST['txtNUMERO'])) ? $_POST['txtNUMERO'] : "0";
$txtFECHA_DOCUMENTO=(isset($_POST['txtFECHA_DOCUMENTO'])) ? $_POST['txtFECHA_DOCUMENTO'] : date("Y-m-d");
$txtCOD_TIPO_DOCUMENTO=(isset($_POST['txtID_TIPO_DOCUMENTO'])) ? $_POST['txtID_TIPO_DOCUMENTO'] : "03";
$txtCOD_MONEDA=(isset($_POST['txtMONEDA'])) ? $_POST['txtMONEDA'] : "PEN";
$cliente=(isset($_POST['txtID_CLIENTE'])) ? $_POST['txtID_CLIENTE'] : "0";
$txtOBSERVACION=(isset($_POST['txtOBSERVACION'])) ? $_POST['txtOBSERVACION'] : "0";
$idlocal=$_COOKIE["idlocal"];
$vendedor=(isset($_POST['vendedor'])) ? $_POST['vendedor'] : "";

$tipodoc=(isset($_POST['tipodoc'])) ? $_POST['tipodoc'] : "";
$modifica=(isset($_POST['modifica'])) ? $_POST['modifica'] : "0";
$motivo=(isset($_POST['motivo'])) ? $_POST['motivo'] : "0";
$motivod=(isset($_POST['motivod'])) ? $_POST['motivod'] : "0";
$smodifica=(isset($_POST['smodifica'])) ? $_POST['smodifica'] : "0";
$nmodifica=(isset($_POST['nmodifica'])) ? $_POST['nmodifica'] : "0";
$idventa=(isset($_POST['idventa'])) ? $_POST['idventa'] : "0";
$seriedoc=(isset($_POST['seriedoc'])) ? $_POST['seriedoc'] : "0";

$idcategoria=(isset($_POST['idcategoria'])) ? $_POST['idcategoria'] : "0";
$condiciones=(isset($_POST['condiciones'])) ? $_POST['condiciones'] : "0";
$comisiont=(isset($_POST['comisiont'])) ? $_POST['comisiont'] : "0";
$icb=(isset($_POST['icb'])) ? $_POST['icb'] : "0";
$serieanticipo=(isset($_POST['serieanticipo'])) ? $_POST['serieanticipo'] : "";
$idanticipo=(isset($_POST['idanticipo'])) ? $_POST['idanticipo'] : "0";

$notpedido=(isset($_POST['notpedido'])) ? $_POST['notpedido'] : "0";
$ocompra=(isset($_POST['ocompra'])) ? $_POST['ocompra'] : "0";
$puntos=(isset($_POST['puntos'])) ? $_POST['puntos'] : "0";
$percepcion=(isset($_POST['percepcion'])) ? $_POST['percepcion'] : "0";
$otrosdetalle=isset($_POST["otros"])?$_POST["otros"]:"";
$detalleBackup=isset($_POST["detalle"])?$_POST["detalle"]:"";
$directosunat=isset($_POST["directosunat"])?$_POST["directosunat"]:"1";
$kardex=isset($_POST["kardex"])?$_POST["kardex"]:"0";
$referencial=isset($_POST["referencial"])?$_POST["referencial"]:"0";
$controlpresupuestal=isset($_POST["controlpresupuestal"])?$_POST["controlpresupuestal"]:"";
$pagoscredito=isset($_POST["pagoscredito"])?$_POST["pagoscredito"]:"";

$guia=(isset($_POST['guia'])) ? $_POST['guia'] : "";
$tipoguia=(isset($_POST['tipoguia'])) ? $_POST['tipoguia'] : "09";
$guia2=(isset($_POST['guia2'])) ? $_POST['guia2'] : "";
$tipoguia2=(isset($_POST['tipoguia2'])) ? $_POST['tipoguia2'] : "09";
$guia3=(isset($_POST['guia3'])) ? $_POST['guia3'] : "";
$tipoguia3=(isset($_POST['tipoguia3'])) ? $_POST['tipoguia3'] : "09";
$guia4=(isset($_POST['guia4'])) ? $_POST['guia4'] : "";
$tipoguia4=(isset($_POST['tipoguia4'])) ? $_POST['tipoguia4'] : "09";
$guia5=(isset($_POST['guia5'])) ? $_POST['guia5'] : "";
$tipoguia5=(isset($_POST['tipoguia5'])) ? $_POST['tipoguia5'] : "09";
$exportacion=(isset($_POST['exportacion'])) ? $_POST['exportacion'] : "NO";
$inafecta=isset($_POST["inafecta"])?$_POST["inafecta"]:"0.00";
$idguia=isset($_POST["idguia"])?$_POST["idguia"]:"0";

$mach_id=isset($_POST["mach_id"])?$_POST["mach_id"]:"";
$mach_numero=isset($_POST["mach_numero"])?$_POST["mach_numero"]:"";
$mach_monto=isset($_POST["mach_monto"])?$_POST["mach_monto"]:"0.00";
$mach_fecha=isset($_POST["mach_fecha"])?$_POST["mach_fecha"]:"0000-00-00";
$mach_observaciones=isset($_POST["mach_observaciones"])?$_POST["mach_observaciones"]:"";


// ======================================================
// Normalización de importes: evita fallas cuando llegan con separador de miles
// Ej: "1,000.00" (JS) -> "1000.00" (DB)
// ======================================================
$txtTOTAL_GRAVADAS    = num_fix($txtTOTAL_GRAVADAS, 2);
$txtSUB_TOTAL         = num_fix($txtSUB_TOTAL, 2);
$txtTOTAL_IGV         = num_fix($txtTOTAL_IGV, 2);
$txtTOTAL             = num_fix($txtTOTAL, 2);
$txtTOTAL_EXONERADAS  = num_fix($txtTOTAL_EXONERADAS, 2);
$txtTOTAL_GRATUITAS   = num_fix($txtTOTAL_GRATUITAS, 2);

$retencion            = num_fix($retencion, 2);
$montodetraccion      = num_fix($montodetraccion, 2);

// ======================================================
// SUNAT: Redondeo detracción a la UNIDAD SOLO cuando la detracción está en PEN.
// - En tu flujo, si la venta está en USD, la detracción se guarda en USD (2 decimales)
//   y recién se convierte a PEN para el XML (en modelos/envio.php), donde se aplica el redondeo a unidad.
// - Si la venta está en PEN, la detracción se deposita en PEN => se redondea a unidad aquí.
// ======================================================
if($txtCOD_MONEDA === 'PEN'){
    $montodetraccion_int  = sunat_round_unidad($montodetraccion);
    $montodetraccion      = number_format((float)$montodetraccion_int, 2, '.', '');
}
$totdescuento         = num_fix($totdescuento, 2);
$dto_global_valor     = num_fix($dto_global_valor, 6);
$dto_global_monto_base_post = num_fix($dto_global_monto_base_post, 2);
$dto_global_monto_igv_post  = num_fix($dto_global_monto_igv_post, 2);
$dto_global_aplica_antes_igv = ($dto_global_aplica_antes_igv==='0') ? '0' : '1';
$dto_global_tipo      = ($dto_global_tipo==='PCT') ? 'PCT' : 'MONTO';
$percepcion           = num_fix($percepcion, 2);
$inafecta             = num_fix($inafecta, 2);
$tarjeta              = num_fix($tarjeta, 2);
$icb                  = num_fix($icb, 2);
$comisiont            = num_fix($comisiont, 2);
$referencial          = num_fix($referencial, 2);
$puntos               = num_fix($puntos, 2);

$dto_global_monto = num_fix($totdescuento, 2);
$dto_global_monto_base = '0.00';
$dto_global_monto_igv = '0.00';
if($dto_global_monto > 0 && (float)$dto_global_monto_base_post > 0){
    $dto_global_monto_base = num_fix($dto_global_monto_base_post, 2);
    $dto_global_monto_igv  = num_fix($dto_global_monto_igv_post, 2);
}
$dto_global_monto = num_fix($dto_global_monto, 2);


$jsondata['inafecta'] =$inafecta;
if($inafecta==''){ $inafecta='0.00'; }
if($idlocal==''){ $idlocal='0'; }
$tipodoc2= $txtCOD_TIPO_DOCUMENTO;

if($retencion!='0.00'){
	
if($txtCOD_MONEDA=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($txtCOD_MONEDA=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='US$'; }
if($txtCOD_MONEDA=='EUR'){ $valmoneda='EUROS'; $mf='€'; }
$totalnet=round($txtTOTAL-$retencion, 2);
$txtOBSERVACION=$txtOBSERVACION.' | RETENCIÓN 3%: '.$mf.' '.$retencion.'; NETO A PAGAR: '.$mf.' '.$totalnet;	
}

$fechacambio=$txtFECHA_DOCUMENTO;
$periodo=date("Y-m",strtotime($txtFECHA_DOCUMENTO));

if($notpedido!=''){ $serieanticipo=$notpedido; }
$tarjeta=(isset($_POST['tarjeta'])) ? $_POST['tarjeta'] : "0";
$txtSUB_TOTAL=$txtTOTAL_GRAVADAS;
$txtSUB_TOTAL=abs($txtSUB_TOTAL);
$vasunat="0";
$fechaDocumentoBase=normalizarFechaYmd($txtFECHA_DOCUMENTO, date("Y-m-d"));
$txtFECHA_DOCUMENTO=$fechaDocumentoBase.' '.date('H:i:s');
$fechavto=date("Y-m-d",strtotime($fechaDocumentoBase."+ ".$condiciones." days"));

if($vendedor!=''){ 
$sqlv="SELECT *FROM usuario WHERE idusuario='$vendedor' ";
$vendedor= ejecutarConsultaSimpleFila($sqlv);
$idusuario=$vendedor['idusuario'];
}else{
$idusuario=$_COOKIE["idusuario"];
}

$sqlcat="SELECT *FROM tipo_cambio WHERE fecha<='$fechacambio' ORDER BY fecha DESC ";
$tchoy= ejecutarConsultaSimpleFila($sqlcat);
$costodolar=$tchoy['venta'];

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

if((float)$dto_global_monto > 0){
    $tasa_igv_doc = isset($fa['igv']) ? ((float)$fa['igv'] / 100) : 0.18;
    if($tasa_igv_doc <= 0){ $tasa_igv_doc = 0.18; }
    if((float)$dto_global_monto_base <= 0){
        $dto_global_monto_base = num_fix(round(((float)$dto_global_monto / (1 + $tasa_igv_doc)), 2), 2);
        $dto_global_monto_igv  = num_fix(round(((float)$dto_global_monto - (float)$dto_global_monto_base), 2), 2);
    }else{
        $dto_global_monto_base = num_fix($dto_global_monto_base, 2);
        $dto_global_monto_igv  = num_fix(round(((float)$dto_global_monto - (float)$dto_global_monto_base), 2), 2);
    }
}

$sqlca="SELECT * FROM cajas WHERE idlocal='$idlocal' AND idempresa='$_COOKIE[id]' AND estado='1' AND beta='$fa[tipo]' ";
$caj= ejecutarConsultaSimpleFila($sqlca);

$idcaja='0';
if($caj){ $idcaja=$caj['id']; }	


if($idventa!='0'){
/*	
$rspta=ejecutarConsulta("SELECT *FROM detalle_venta WHERE idventa='$idventa' ");
while ($reg = $rspta->fetch_object()){	
$sql="UPDATE articulo_stock SET stock=stock-$reg->txtCANTIDAD_ARTICULO WHERE idarticulo='$reg->idproducto' AND idlocal='$_COOKIE[idlocal]' ";
ejecutarConsulta($sql);	
}
*/
$sqlvent="SELECT *FROM venta WHERE idventa='$idventa' ";
$conventa= ejecutarConsultaSimpleFila($sqlvent);
	
if($conventa['tipo_pago']=='CONTADO'&&$conventa['medio_pago']=='008'){
$totrestar=$conventa['txtTOTAL']-$conventa['tarjeta'];
agregarcaja($totrestar, 'RESTA', 'ENTRADA', $idusuario);	
}
	
$sqlc="DELETE FROM detalle_venta WHERE idventa='$idventa' ";
ejecutarConsulta($sqlc);
	
$snum=$txtNUMERO;
$idventanew=$idventa;
/*
    if($guia5==''){
        $nombrepaciente="$cliente";
    }else{
        $nombrepaciente=$guia5;
    }
*/    
$nombrepaciente=trim((string)$guia5);
if($nombrepaciente===''){ $nombrepaciente=$cliente; }

    $sqlup="UPDATE venta SET idusuario='$idusuario', txtFECHA_DOCUMENTO='$txtFECHA_DOCUMENTO', fecha_vto='$fechavto', txtOBSERVACION='$txtOBSERVACION', txtSUB_TOTAL='$txtSUB_TOTAL', txtIGV='$txtTOTAL_IGV', percepcion='$percepcion', retencion='$retencion', iddetraccion='$iddetraccion', detraccion='$montodetraccion',  ICB='$icb', descuento='$dto_global_monto', dto_global_monto='$dto_global_monto', dto_global_tipo='$dto_global_tipo', dto_global_valor='$dto_global_valor', dto_global_monto_base='$dto_global_monto_base', dto_global_monto_igv='$dto_global_monto_igv', dto_global_aplica_antes_igv='$dto_global_aplica_antes_igv', total_sunat='$txtTOTAL', total_a_pagar='".num_fix(((float)$txtTOTAL-(float)$retencion),2)."', txtTOTAL='$txtTOTAL', gratuita='$txtTOTAL_GRATUITAS', exonerado='$txtTOTAL_EXONERADAS', comision='$comisiont', tarjeta='$tarjeta', tipocambio='$costodolar', txtID_MONEDA='$txtCOD_MONEDA', tipo_pago='$txtPAGO', medio_pago='$mediopago', tipoguia='$tipoguia', guia='$guia', presupuesto='$ocompra', referencia='$serieanticipo', condiciones='$condiciones', estadopago='$idanticipo', referencial='$referencial', controlpresupuestal='$controlpresupuestal', guia2='$guia2', tipoguia2='$tipoguia2', guia3='$guia3', tipoguia3='$tipoguia3', guia4='$guia4', tipoguia4='$tipoguia4', guia5='$nombrepaciente', fpago_mpago='$guia5', tipoguia5='$tipoguia5', exportacion='$exportacion', inafecta='$inafecta' , txtID_CLIENTE='$cliente'  WHERE idventa='$idventa' ";
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

$snum=$ser['numeroinicio'];
if($mostrarnn){ $snum=$mostrarnn['txtNUMERO']+1; }
$snum=str_pad($snum, 8, "0", STR_PAD_LEFT);
	
if($txtCOD_TIPO_DOCUMENTO=='07'||$txtCOD_TIPO_DOCUMENTO=='08'){
	
$sql="SELECT *FROM venta WHERE txtSERIE='$smodifica' AND txtNUMERO='$nmodifica' AND idempresa='$_COOKIE[id]' AND (txtID_TIPO_DOCUMENTO='01' OR txtID_TIPO_DOCUMENTO='03' ) AND beta='$fa[tipo]' ";
$mostrarv= ejecutarConsultaSimpleFila($sql);
$cliente=$mostrarv['txtID_CLIENTE'];
$txtCOD_MONEDA=$mostrarv['txtID_MONEDA'];

}

if($cliente==''){ $cliente='0'; }
if($txtTOTAL_GRATUITAS==''){ $txtTOTAL_GRATUITAS='0'; }
if($txtTOTAL_EXONERADAS==''){ $txtTOTAL_EXONERADAS='0'; }
if($comisiont==''){ $comisiont='0'; }	

$act='0';

$sql3="SELECT *FROM persona WHERE idpersona='$cliente' ";
$cli= ejecutarConsultaSimpleFila($sql3);

/*
PAGO A CREDITO LIMITE DE CREDITO
*/
if($txtPAGO=='CREDITO'){
credito($txtTOTAL, 'SUMA', $cliente);	
}

/*
    if($guia5==''){
        $nombrepaciente="$cliente";
    }else{
        $nombrepaciente=$guia5;
    }
*/    
$nombrepaciente='';

$total_a_pagar=num_fix(((float)$txtTOTAL-(float)$retencion),2);
$sql="INSERT INTO venta (idventa,idempresa,beta,pedido,sector,controlpresupuestal,exportacion,txtID_CLIENTE,idusuario,idlocal,txtID_TIPO_DOCUMENTO,doc_relaciona,docmodifica_tipo,docmodifica,modifica_motivo,modifica_motivod,txtSERIE,txtNUMERO,txtFECHA_DOCUMENTO,fecha_vto,txtOBSERVACION,txtSUB_TOTAL,txtIGV,percepcion,retencion,iddetraccion,detraccion,ICB,descuento,dto_global_monto,dto_global_tipo,dto_global_valor,dto_global_monto_base,dto_global_monto_igv,dto_global_aplica_antes_igv,total_sunat,total_a_pagar,txtTOTAL,referencial,gratuita,exonerado,inafecta,comision,tarjeta,tipocambio,txtID_MONEDA,tipo_pago,medio_pago,orden,tipoguia,guia,tipoguia2,guia2,tipoguia3,guia3,tipoguia4,guia4,tipoguia5,guia5,presupuesto,referencia,condiciones,hash_cpe,hash_cdr,mensaje,mach_id,mach_numero,mach_monto,mach_fecha,mach_observaciones,estado,estadopago,kardex,idcaja,fpago_mpago)
VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$act', '$idcategoria', '$controlpresupuestal', '$exportacion', '$cliente', '$idusuario', '$idlocal', '$txtCOD_TIPO_DOCUMENTO', '$modifica', '$tipodoc', '$modifica', '$motivo', '$motivod', '$txtSERIE', '$snum', '$txtFECHA_DOCUMENTO', '$fechavto', '$txtOBSERVACION', '$txtSUB_TOTAL', '$txtTOTAL_IGV', '$percepcion', '$retencion', '$iddetraccion', '$montodetraccion', '$icb', '$dto_global_monto', '$dto_global_monto', '$dto_global_tipo', '$dto_global_valor', '$dto_global_monto_base', '$dto_global_monto_igv', '$dto_global_aplica_antes_igv', '$txtTOTAL', '$total_a_pagar', '$txtTOTAL', '$referencial', '$txtTOTAL_GRATUITAS', '$txtTOTAL_EXONERADAS', '$inafecta', '$comisiont', '$tarjeta', '$costodolar', '$txtCOD_MONEDA', '$txtPAGO', '$mediopago', '', '$tipoguia', '$guia', '$tipoguia2', '$guia2', '$tipoguia3', '$guia3', '$tipoguia4', '$guia4', '$tipoguia5', '$nombrepaciente', '$ocompra', '$serieanticipo', '$condiciones', '', '', '', '$mach_id', '$mach_numero', '$mach_monto', '$mach_fecha', '$mach_observaciones', '0', '$idanticipo', '$kardex', '$idcaja', '$guia5')";

$idventanew=ejecutarConsulta_retornarID($sql);	
$idventa=$idventanew;

if($idguia>0){

$sqlup="UPDATE guia_guia SET idventa='$idventa'  WHERE id='$idguia' ";
ejecutarConsulta($sqlup);

}


}

if($txtCOD_TIPO_DOCUMENTO=='01'||$txtCOD_TIPO_DOCUMENTO=='03'||$txtCOD_TIPO_DOCUMENTO=='90'){
if($txtPAGO=='CONTADO'){
$txtTOTAL=$txtTOTAL-$tarjeta;	
agregarcaja($txtTOTAL, 'SUMA', 'ENTRADA', $idusuario);	
}	
}

// Reemplazar pagos previos al modificar/guardar para evitar duplicados
if($idventa!='0'){
$delpagos="DELETE FROM caja_ventapago WHERE idventa='$idventa' AND nivel='0'";
ejecutarConsulta($delpagos);
}

/*GUARDA CREDITOS*/
/*GUARDA CREDITOS*/
if($txtPAGO=='CREDITO'){


//echo 'beso:'.$pagoscredito;

$idtipopagoPersonaSel = trim((string)$guia5);
if($idtipopagoPersonaSel!=='' && ctype_digit($idtipopagoPersonaSel) && (int)$idtipopagoPersonaSel>0){
    $sqlcliente="SELECT *FROM caja_tipopago_persona WHERE id='$idtipopagoPersonaSel' ";
}else{
    $sqlcliente="SELECT *FROM caja_tipopago_persona WHERE id='$cli[venta_pago]' ";
}
$datocliente= ejecutarConsultaSimpleFila($sqlcliente);

if($datocliente){

$cuotaspagos=(int)$datocliente['cuotas'];
$diascliente=(int)$datocliente['dias'];
if($cuotaspagos<=0){ $cuotaspagos=1; }

$diaspagos=(int)$diascliente;
if($diaspagos<=0){ $diaspagos=30; }

$fechaDocumentoPago=normalizarFechaYmd($txtFECHA_DOCUMENTO, date("Y-m-d"));

$baseCuotas = round((float)$txtTOTAL - (float)$montodetraccion - (float)$retencion, 2);
if($baseCuotas < 0){ $baseCuotas = 0; }
$totalCuotas = $baseCuotas;
$montoBase=round($totalCuotas/$cuotaspagos, 2);
$acumCuotas=0;
$estadopago='0';
$operacion=$idventanew;
$tipopago=$datocliente['id_pago'];

for($i=1; $i<=$cuotaspagos; $i++){

$monto=$montoBase;
if($i==$cuotaspagos){
	$monto=round($totalCuotas-$acumCuotas, 2);
}
$acumCuotas=round($acumCuotas+$monto, 2);

$fechafinal=date("Y-m-d",strtotime($fechaDocumentoPago." + ".($diaspagos*$i)." days"));
if(!$fechafinal || $fechafinal<'2000-01-01'){ $fechafinal=$fechaDocumentoPago; }

if($txtCOD_MONEDA === 'PEN'){ 
$montosoles = $monto; 
$montodolares = $monto / $costodolar; 
}

if($txtCOD_MONEDA === 'USD'){ 
$montodolares = $monto; 
$montosoles = $monto * $costodolar; 
}

$sqlci="SELECT *FROM caja_ventapago WHERE nivel='0' AND tipopago='$txtPAGO' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
$ci= ejecutarConsultaSimpleFila($sqlci);

$snum=$ci['serie']+1;
$snum=str_pad($snum, 8, "0", STR_PAD_LEFT);	

$sql_detallepago = "INSERT INTO caja_ventapago VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$_COOKIE[idlocal]', '$idusuario', '$txtPAGO', '0', '0', '$idventa', '$tipopago', '$snum', '$txtCOD_MONEDA', '$montosoles', '$montodolares', '$costodolar', '$operacion', '$fechaDocumentoPago', '$fechafinal', '$fechafinal', '', '$estadopago')";
//echo $sql_detallepago;
$idpago=ejecutarConsulta_retornarID($sql_detallepago) or $sw = false;

}

}else{

if($pagoscredito!=''){

$otrostotcredito=json_decode($pagoscredito);

$sqlci="SELECT *FROM caja_ventapago WHERE nivel='0' AND tipopago='$txtPAGO' ORDER BY id DESC ";
$ci= ejecutarConsultaSimpleFila($sqlci);
$operacion=$idventanew;

$estadopago='1';
if($txtPAGO=='CREDITO'){ $estadopago='0'; }


$snum=$ci['serie']+1;
$snum=str_pad($snum, 8, "0", STR_PAD_LEFT);	
	
foreach ($otrostotcredito as $value) {

if($value->tipopago!='No data available in table'){
	
$tipopago=(isset($value->tipopago))?$value->tipopago:"0";
$fechadoc=(isset($value->fechadoc))?$value->fechadoc:"";
$fechavence=(isset($value->fechavence))?$value->fechavence:"";
$moneda=(isset($value->moneda))?$value->moneda:"PEN";
$monto=(isset($value->monto))?$value->monto:"0";
$tcambio=(isset($value->tcambio))?$value->tcambio:"0";

$monto=str_replace(',', '', trim((string)$monto));
$tcambio=str_replace(',', '', trim((string)$tcambio));
if($monto===''){ $monto='0'; }
if($tcambio===''){ $tcambio='0'; }

$monto=round((float)$monto, 2);
	
if((float)$tcambio<=0){ $tcambio=$costodolar; }
if((float)$tcambio<=0){ $tcambio=1; }

if($moneda === 'PEN'){ 
$montosoles = $monto; 
$montodolares = $monto / $tcambio; 
}

if($moneda ==='USD'){
$montodolares = $monto; 
$montosoles = $monto * $tcambio;	
}

$fechadoc=normalizarFechaYmd($fechadoc, $fechaDocumentoBase);
$fechavence=normalizarFechaYmd($fechavence, $fechadoc);

$sql_detallepago = "INSERT INTO caja_ventapago VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$_COOKIE[idlocal]', '$idusuario', '$txtPAGO', '0', '0', '$idventa', '$tipopago', '$snum', '$moneda', '$montosoles', '$montodolares', '$tcambio', '$operacion', '$fechadoc', '$fechavence', '$fechavence', '', '$estadopago')";

//echo $sql_detallepago;
$idpago=ejecutarConsulta_retornarID($sql_detallepago) or $sw = false;

}

}

}


}

}


// Garantizar al menos un registro en caja_ventapago para CONTADO/CREDITO
$sqlCountPagos = "SELECT COUNT(*) AS c FROM caja_ventapago WHERE idventa='$idventa' AND nivel='0'";
$rowCountPagos = ejecutarConsultaSimpleFila($sqlCountPagos);
$cantPagos = ($rowCountPagos && isset($rowCountPagos['c'])) ? (int)$rowCountPagos['c'] : 0;

if($cantPagos<=0){
    $sqlci="SELECT *FROM caja_ventapago WHERE nivel='0' AND tipopago='$txtPAGO' ORDER BY id DESC ";
    $ci= ejecutarConsultaSimpleFila($sqlci);
    $snum=(isset($ci['serie']) ? (int)$ci['serie'] : 0) + 1;
    $snum=str_pad($snum, 8, "0", STR_PAD_LEFT);

    $operacion=$idventanew;
    $estadopago = ($txtPAGO=='CONTADO') ? '1' : '0';
    $tipopagoauto = ($txtPAGO=='CONTADO') ? $mediopago : '0';

    $fechaBasePago=normalizarFechaYmd($txtFECHA_DOCUMENTO, date("Y-m-d"));
    $fechaVtoPago=$fechaBasePago;

    if($txtPAGO=='CREDITO'){
        $idtipopagoPersonaSel = trim((string)$guia5);
        if($idtipopagoPersonaSel!=='' && ctype_digit($idtipopagoPersonaSel) && (int)$idtipopagoPersonaSel>0){
            $sqlcfg="SELECT *FROM caja_tipopago_persona WHERE id='$idtipopagoPersonaSel' ";
        }else{
            $sqlcfg="SELECT *FROM caja_tipopago_persona WHERE id='$cli[venta_pago]' ";
        }
        $cfg=ejecutarConsultaSimpleFila($sqlcfg);
        if($cfg){
            $diascfg=(isset($cfg['dias'])) ? (int)$cfg['dias'] : 0;
            if($diascfg<=0){ $diascfg=30; }
            $fechaVtoPago=date("Y-m-d",strtotime($fechaBasePago." + ".$diascfg." days"));
            if(isset($cfg['id_pago']) && trim((string)$cfg['id_pago'])!==''){
                $tipopagoauto=$cfg['id_pago'];
            }
        }
    }

    if($txtCOD_MONEDA === 'PEN'){
        $montosoles = (float)$txtTOTAL;
        $montodolares = (float)$txtTOTAL / (float)$costodolar;
    }else{
        $montodolares = (float)$txtTOTAL;
        $montosoles = (float)$txtTOTAL * (float)$costodolar;
    }

    $sqlAutoPago = "INSERT INTO caja_ventapago VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$_COOKIE[idlocal]', '$idusuario', '$txtPAGO', '0', '0', '$idventa', '$tipopagoauto', '$snum', '$txtCOD_MONEDA', '$montosoles', '$montodolares', '$costodolar', '$operacion', '$fechaBasePago', '$fechaVtoPago', '$fechaVtoPago', '', '$estadopago')";
    ejecutarConsulta($sqlAutoPago);
}

/*GUARDA CREDITOS*/
/*GUARDA CREDITOS*/


/*GRABA CUERPO*/
if($otrosdetalle!='' || $detalleBackup!=''){

$otrostot=json_decode($otrosdetalle);
if(!is_array($otrostot)){ $otrostot=json_decode(stripslashes((string)$otrosdetalle)); }
if(!is_array($otrostot) && $detalleBackup!=''){ $otrostot=json_decode($detalleBackup); }
if(!is_array($otrostot)){ $otrostot=[]; }
		
foreach ($otrostot as $value) {
	
$id=(isset($value->txtID))?$value->txtID:"0";
$tipo=(isset($value->tipo))?$value->tipo:"0";
$serie=(isset($value->serie))?$value->serie:"0";
$proveedor=(isset($value->proveedor))?$value->proveedor:"0";
$codigo=(isset($value->txtCODIGO_DET))?$value->txtCODIGO_DET:"0";
$nombre=(isset($value->txtDESCRIPCION_DET))?$value->txtDESCRIPCION_DET:"0";
$cti=(isset($value->txtCANTIDAD_DET))?$value->txtCANTIDAD_DET:"0";
$importe=(isset($value->txtIMPORTE_DET))?$value->txtIMPORTE_DET:"0";	
$igv=(isset($value->txtIGV))?$value->txtIGV:"0";
$precio=(isset($value->txtPRECIO))?$value->txtPRECIO:"0";
$descuento=(isset($value->descuento))?$value->descuento:"0";
$placa=(isset($value->placa))?$value->placa:"";
$ctiunidad=(isset($value->ctiunidad))?$value->ctiunidad:"0";
$comision=(isset($value->comision))?$value->comision:"0";
$UNIDAD_MEDIDA=(isset($value->UNIDAD_MEDIDA))?$value->UNIDAD_MEDIDA: "0";
$idunit=(isset($value->idunit))?$value->idunit: "0";
$idv=(isset($value->idv))?$value->idv: "0";
$tipoart=(isset($value->tipoart))?$value->tipoart: "0";

$exonerado=(isset($value->exonerado))?$value->exonerado: "0";
$gratuita=(isset($value->gratuita))?$value->gratuita: "0";
$icbd=(isset($value->icbd))?$value->icbd: "0";
$detracciond=(isset($value->detracciond))?$value->detracciond: "0";
$idcaja=(isset($value->idcaja))?$value->idcaja:"0";

$iddestino=(isset($value->iddestino))?$value->iddestino: "0";	
$carga_util=(isset($value->carga_util))?$value->carga_util: "0";
$cantidad_toneladas=(isset($value->cantidad_toneladas))?$value->cantidad_toneladas: "0";
$inafectadet=(isset($value->inafecta))?$value->inafecta: "0.00";
$cod_afectacion_igv=(isset($value->cod_afectacion_igv))?$value->cod_afectacion_igv:mapTipoToAfectacion($tipo);
$cod_afectacion_igv=caf_extraer_codigo($cod_afectacion_igv, '');
if($cod_afectacion_igv===''){ $cod_afectacion_igv=mapTipoToAfectacion($tipo); }
$cod_afectacion_igv=trim((string)$cod_afectacion_igv);
$codigosValidosAfectacion = caf_codigos_validos();
if(!in_array($cod_afectacion_igv, $codigosValidosAfectacion, true)){
  // fallback defensivo por si el helper retorna formato no estricto
  $cod_afectacion_igv = caf_extraer_codigo($cod_afectacion_igv, '10');
}
if(!in_array($cod_afectacion_igv, caf_codigos_validos(), true)){
  $cod_afectacion_igv='10';
}

$confAfectacion = caf_get_codigo_conf($cod_afectacion_igv, isset($_COOKIE['id']) ? (int)$_COOKIE['id'] : 0);
$cod_afectacion_igv = isset($confAfectacion['codigo']) && trim((string)$confAfectacion['codigo'])!=='' ? trim((string)$confAfectacion['codigo']) : $cod_afectacion_igv;

$dto_item_tipo=(isset($value->dto_item_tipo))?$value->dto_item_tipo:'MONTO';
$dto_item_tipo=($dto_item_tipo==='PCT')?'PCT':'MONTO';
$dto_item_valor=(isset($value->dto_item_valor))?$value->dto_item_valor:$descuento;
$dto_item_valor=num_fix($dto_item_valor, 6);
$valor_unitario_ref=(isset($value->valor_unitario_ref))?$value->valor_unitario_ref:'0';
$valor_unitario_ref=num_fix($valor_unitario_ref, 6);
$precioo=(isset($value->precioo))?$value->precioo:'0';
$precioo=num_fix($precioo, 6);
$cod_tributo=(isset($value->cod_tributo))?$value->cod_tributo:'';
$porc_igv=(isset($value->porc_igv))?$value->porc_igv:'';
if(trim((string)$cod_tributo)===''){ $cod_tributo = isset($confAfectacion['cod_tributo']) ? (string)$confAfectacion['cod_tributo'] : ''; }
if(trim((string)$porc_igv)===''){ $porc_igv = isset($confAfectacion['porcentaje_igv']) ? number_format((float)$confAfectacion['porcentaje_igv'],2,'.','') : ''; }
if(trim((string)$cod_tributo)==='' || trim((string)$porc_igv)===''){
  list($defTributo,$defPorc)=tributoPorAfectacion($cod_afectacion_igv);
  if(trim((string)$cod_tributo)===''){ $cod_tributo=$defTributo; }
  if(trim((string)$porc_igv)===''){ $porc_igv=$defPorc; }
}
$porc_igv=num_fix($porc_igv,2);

// Normaliza números del detalle (por si vienen con coma de millar)
$cti         = num_str($cti);
$ctiunidad   = num_str($ctiunidad);
$precio      = num_fix($precio, 4);
$descuento   = num_fix($descuento, 2);
$igv         = num_fix($igv, 2);
$importe     = num_fix($importe, 2);
$exonerado   = num_fix($exonerado, 2);
$gratuita    = num_fix($gratuita, 2);
$inafectadet = num_fix($inafectadet, 2);
$icbd        = num_fix($icbd, 2);
$detracciond = num_fix($detracciond, 2);
$comision    = num_fix($comision, 2);
$carga_util  = num_str($carga_util);
$cantidad_toneladas = num_str($cantidad_toneladas);

if($inafectadet==''){ $inafectadet='0'; }
if($iddestino==''){ $iddestino='0'; }
if($carga_util==''){ $carga_util='0'; }	
if($cantidad_toneladas==''){ $cantidad_toneladas='0'; }
	
if($gratuita==''){ $gratuita='0'; }
if($detracciond==''){ $detracciond='0'; }
if($exonerado==''){ $exonerado='0'; }
if($descuento==''){ $descuento='0'; }
if($idunit==''){ $idunit='0'; }
if($ctiunidad==''){ $ctiunidad='0'; }
if($cti==''){ $cti='0'; }
if($descuento==''){ $descuento='0'; }
$subtotal=(isset($value->txtPRECIO_DET))?$value->txtPRECIO_DET: "0";
$subtotal = num_fix($subtotal, 2);	
//$subtotal=$importe-$igv;	
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

if($iddestino==''){ $iddestino='0'; }
if($carga_util==''){ $carga_util='0'; }
if($cantidad_toneladas==''){ $cantidad_toneladas='0'; }

if((float)$cti<=0){ $cti='0'; }

$valor_total_ref='0.00';
$igv_ref='0.00';
$es_retiro='0'; $es_publicidad='0'; $es_bonificacion='0'; $es_gratuito='0';

$esNoOnerosa = ($cod_tributo==='9996') ? true : (isset($confAfectacion['es_onerosa']) ? ((int)$confAfectacion['es_onerosa']===0) : in_array($cod_afectacion_igv, array('11','12','13','14','15','16','21','31','32','33','34','35','36'), true));
$requiereRef = ($cod_tributo==='9996') ? true : (isset($confAfectacion['requiere_valor_referencial']) ? ((int)$confAfectacion['requiere_valor_referencial']===1) : $esNoOnerosa);
$codigo_tipo_precio = ($cod_tributo==='9996') ? '02' : (isset($confAfectacion['codigo_tipo_precio']) ? trim((string)$confAfectacion['codigo_tipo_precio']) : ($requiereRef ? '02' : '01'));

if($requiereRef){
  if((float)$valor_unitario_ref<=0 && (float)$precioo>0){ $valor_unitario_ref=$precioo; }
  if((float)$valor_unitario_ref<=0 && (float)$precio>0){ $valor_unitario_ref=$precio; }
  if((float)$valor_unitario_ref<=0){
    guardarErrorJson('Para Catálogo 07 '.$cod_afectacion_igv.' se requiere valor_unitario_ref > 0 en ítem ['.$nombre.']');
  }
  $valor_unitario_ref=num_fix($valor_unitario_ref, 6);
  $valor_total_ref=num_fix(((float)$valor_unitario_ref*(float)$cti),2);
  if((float)$valor_total_ref<=0 && (float)$gratuita>0){ $valor_total_ref=num_fix($gratuita,2); }
  if((float)$valor_total_ref<=0){
    guardarErrorJson('Para Catálogo 07 '.$cod_afectacion_igv.' se requiere valor_total_ref > 0 en ítem ['.$nombre.']');
  }
  $igv_ref='0.00';
  if((float)$porc_igv>0){
    $igv_ref=num_fix(round(((float)$valor_total_ref*((float)$porc_igv/100)),2),2);
  }
  $es_gratuito='1';
  $importe='0.00'; $subtotal='0.00'; $precio='0.0000'; $igv='0.00';
}else{
  $valor_unitario_ref='0.000000';
  $valor_total_ref='0.00';
  $igv_ref='0.00';
  if($codigo_tipo_precio==='02'){ $codigo_tipo_precio='01'; }
}

$es_retiro = isset($confAfectacion['es_retiro']) ? ((int)$confAfectacion['es_retiro'] ? '1' : '0') : '0';
$es_publicidad = isset($confAfectacion['es_publicidad']) ? ((int)$confAfectacion['es_publicidad'] ? '1' : '0') : '0';
$es_bonificacion = isset($confAfectacion['es_bonificacion']) ? ((int)$confAfectacion['es_bonificacion'] ? '1' : '0') : ((in_array($cod_afectacion_igv, array('15','31'), true)) ? '1' : '0');
$es_gratuito = isset($confAfectacion['es_gratuito']) ? ((int)$confAfectacion['es_gratuito'] ? '1' : $es_gratuito) : $es_gratuito;
$cod_tributo = isset($confAfectacion['cod_tributo']) ? trim((string)$confAfectacion['cod_tributo']) : $cod_tributo;
if($cod_tributo===''){ $cod_tributo='1000'; }
$idcatalogo_afectacion = isset($confAfectacion['id']) ? (int)$confAfectacion['id'] : 0;
$version_ubl = isset($confAfectacion['version_ubl']) ? trim((string)$confAfectacion['version_ubl']) : '2.1';
$afecta_total_1004 = isset($confAfectacion['requiere_total_1004']) ? (int)$confAfectacion['requiere_total_1004'] : (in_array($cod_afectacion_igv, array('11','12','13','14','15','16','21','31','32','33','34','35','36'), true) ? 1 : 0);
$codigo_leyenda = isset($confAfectacion['codigo_leyenda']) ? trim((string)$confAfectacion['codigo_leyenda']) : (in_array($cod_afectacion_igv, array('11','12','13','14','15','16','21','31','32','33','34','35','36'), true) ? '1002' : '');
$base_imponible_xml = (float)$subtotal;
$monto_tributo_xml = (float)$igv;
$valor_unitario_xml = ((float)$cti>0) ? ((float)$subtotal/(float)$cti) : 0;
$precio_unitario_xml = ((float)$cti>0) ? ((float)$importe/(float)$cti) : 0;
$valor_venta_xml = (float)$subtotal;

$dto_item_monto=num_fix($descuento,2);
$dto_item_monto_base='0.00';
$dto_item_monto_igv='0.00';
if((float)$dto_item_monto>0){
  if($cod_afectacion_igv==='10' && (float)$porc_igv>0){
    $factor_item = 1 + ((float)$porc_igv/100);
    $dto_item_monto_base=num_fix(round(((float)$dto_item_monto/$factor_item),2),2);
    $dto_item_monto_igv=num_fix(round(((float)$dto_item_monto-(float)$dto_item_monto_base),2),2);
  }else{
    $dto_item_monto_base=num_fix($dto_item_monto,2);
    $dto_item_monto_igv='0.00';
  }
}

$cols=array('iddetalle_venta','idempresa','idlocal','beta','idventa','tipoarticulo','idproducto','codigoproducto','unidadmedida','idpresentacion','nombreproducto','txtCANTIDAD_ARTICULO','cantidadp','precio','preciocompra','descuento','subtotal','igv','ICB','importe','exoneradod','inafectad','gratuitad','detracciond','comisiond','stock','tipo','anticipo','doc_anticipo','placa','idlote','idproveedor','iddestino','carga_util','cantidad_toneladas','fecha','idcaja');
$vals=array("NULL","'$_COOKIE[id]'","'$_COOKIE[idlocal]'","'$fa[tipo]'","'$idventanew'","'$tipoart'","'$id'","'$codigo'","'$UNIDAD_MEDIDA'","'$idunit'","'$nombre'","'$cti'","'$ctiunidad'","'$precio'","'0'","'$dto_item_monto'","'$subtotal'","'$igv'","'$icbd'","'$importe'","'$exonerado'","'$inafectadet'","'$gratuita'","'$detracciond'","'$comision'","'0'","'$tipo'","'0'","''","'$placa'","'$serie'","'$proveedor'","'$iddestino'","'$carga_util'","'$cantidad_toneladas'","'$fechakardex'","'$idcaja'");

$opt=array(
  'cod_afectacion_igv'=>"'$cod_afectacion_igv'",
  'cod_tributo'=>"'$cod_tributo'",
  'porc_igv'=>"'$porc_igv'",
  'valor_unitario_ref'=>"'$valor_unitario_ref'",
  'igv_ref'=>"'$igv_ref'",
  'valor_total_ref'=>"'$valor_total_ref'",
  'es_retiro'=>"'$es_retiro'",
  'es_publicidad'=>"'$es_publicidad'",
  'es_bonificacion'=>"'$es_bonificacion'",
  'es_gratuito'=>"'$es_gratuito'",
  'dto_item_monto'=>"'$dto_item_monto'",
  'dto_item_tipo'=>"'$dto_item_tipo'",
  'dto_item_valor'=>"'$dto_item_valor'",
  'dto_item_monto_base'=>"'$dto_item_monto_base'",
  'dto_item_monto_igv'=>"'$dto_item_monto_igv'",
  'idcatalogo_afectacion'=>"'".$idcatalogo_afectacion."'",
  'version_ubl'=>"'".$version_ubl."'",
  'codigo_tipo_precio'=>"'".$codigo_tipo_precio."'",
  'base_imponible_xml'=>"'".num_fix($base_imponible_xml,2)."'",
  'monto_tributo_xml'=>"'".num_fix($monto_tributo_xml,2)."'",
  'valor_unitario_xml'=>"'".num_fix($valor_unitario_xml,10)."'",
  'precio_unitario_xml'=>"'".num_fix($precio_unitario_xml,10)."'",
  'valor_venta_xml'=>"'".num_fix($valor_venta_xml,2)."'",
  'codigo_leyenda'=>"'".$codigo_leyenda."'",
  'afecta_total_1004'=>"'".$afecta_total_1004."'"
);
$criticalCols = array('cod_afectacion_igv','cod_tributo','porc_igv','es_gratuito','es_bonificacion','es_publicidad','es_retiro','idcatalogo_afectacion');
foreach($opt as $c=>$v){ if(in_array($c,$criticalCols,true) || gv_table_has_column('detalle_venta',$c)){ $cols[]=$c; $vals[]=$v; } }

$sql_detalle = "INSERT INTO detalle_venta (".implode(',',$cols).") VALUES (".implode(',',$vals).")";

//echo $sql_detalle;

$idvent=ejecutarConsulta_retornarID($sql_detalle) or $sw = false;

if(existeTabla('venta_descuentos')){
  $sqlAud="INSERT INTO venta_descuentos (idempresa,idlocal,idventa,iddetalle_venta,alcance,tipo_descuento,valor_descuento,monto_descuento,monto_base,monto_igv,aplica_antes_igv)
  VALUES ('$_COOKIE[id]','$_COOKIE[idlocal]','$idventanew','$idvent','ITEM','$dto_item_tipo','$dto_item_valor','$dto_item_monto','$dto_item_monto_base','$dto_item_monto_igv','1')";
  ejecutarConsulta($sqlAud);
}

/*AQUI AFECTAMOS AL KARDEX*/	
if ($_COOKIE['pkardex']==1){
if($directosunat=='1'){
if($tipodoc2!='90'){
if($UNIDAD_MEDIDA!='ZZ'){
if($kardex=='0'){
guardakardex($id, $idventa, $idvent, $tipodoc2, $txtSERIE, $snum, $ctit, $ctiunidad, $fechacambio, $UNIDAD_MEDIDA, $tipoart, $fa['tipo'], $serie, $periodo, $tipo, $txtCOD_MONEDA, $precio, $costodolar, $cliente, $idusuario, $nombre, $motivo, $idlocal);
}	
}

}
}
}
/*AQUI AFECTAMOS AL KARDEX*/	
	
}
}

if(existeTabla('venta_descuentos')){
  $sqlAudG="INSERT INTO venta_descuentos (idempresa,idlocal,idventa,iddetalle_venta,alcance,tipo_descuento,valor_descuento,monto_descuento,monto_base,monto_igv,aplica_antes_igv)
  VALUES ('$_COOKIE[id]','$_COOKIE[idlocal]','$idventanew',NULL,'GLOBAL','$dto_global_tipo','$dto_global_valor','$dto_global_monto','$dto_global_monto_base','$dto_global_monto_igv','$dto_global_aplica_antes_igv')";
  ejecutarConsulta($sqlAudG);
}

if($directosunat=='1'){
if($tipodoc2!='90'){
$vasunat="1";
enviarfactura($idventa);
}
}else{
$jsondata['mensaje'] = "DOCUMENTO GUARDADO / FALTA ENVIAR A SUNAT";	
}

$jsondata['txtSERIE'] =$txtSERIE;
$jsondata['numero'] =$snum;
$jsondata['tdocumento'] =$tipodoc2;
$jsondata['idventa'] = $idventanew;
$jsondata['id'] = $idventa;
$jsondata['idguia'] = $idguia;
$jsondata['idcategoria'] = $idcategoria;


echo json_encode($jsondata);
exit();	

?>
