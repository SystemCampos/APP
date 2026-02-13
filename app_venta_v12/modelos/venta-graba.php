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

$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';
$jsondata['numero'] = '00000000';

//$array = explode("/", $_SERVER['REQUEST_URI']);
$bodyRequest = file_get_contents("php://input");
// Decodifica el cuerpo de la solicitud y lo guarda en un array de PHP
$cab = json_decode($bodyRequest, true);
$detalle = $cab['detalle'];
$detallepago = $cab['detallepago'];

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

// ------------------------------------------------------------
// Base documento para series de cuotas: SERIE-NUMERO(8)
// En payload puede venir "numero" (8 dígitos) o "txtNUMERO".
// ------------------------------------------------------------
$docNumeroCab = (isset($cab['numero']) && $cab['numero']!='') ? $cab['numero'] : $txtNUMERO;
$docNumeroCab = preg_replace('/\D/','', (string)$docNumeroCab);
$docNumeroCab = str_pad($docNumeroCab, 8, "0", STR_PAD_LEFT);
$baseDoc = trim((string)$txtSERIE)!='' ? (trim((string)$txtSERIE)."-".$docNumeroCab) : '';
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
$kardex=isset($cab["kardex"])?$cab["kardex"]:"0";
$referencial=isset($cab["referencial"])?$cab["referencial"]:"0";
$controlpresupuestal=isset($cab["controlpresupuestal"])?$cab["controlpresupuestal"]:"";

$guia=(isset($cab['guia'])) ? $cab['guia'] : "";
$tipoguia=(isset($cab['tipoguia'])) ? $cab['tipoguia'] : "09";
$guia2=(isset($cab['guia2'])) ? $cab['guia2'] : "";
$tipoguia2=(isset($cab['tipoguia2'])) ? $cab['tipoguia2'] : "09";
$guia3=(isset($cab['guia3'])) ? $cab['guia3'] : "";
$tipoguia3=(isset($cab['tipoguia3'])) ? $cab['tipoguia3'] : "09";
$guia4=(isset($cab['guia4'])) ? $cab['guia4'] : "";
$tipoguia4=(isset($cab['tipoguia4'])) ? $cab['tipoguia4'] : "09";
$guia5=(isset($cab['guia5'])) ? $cab['guia5'] : "";
$tipoguia5=(isset($cab['tipoguia5'])) ? $cab['tipoguia5'] : "09";
$exportacion=(isset($cab['exportacion'])) ? $cab['exportacion'] : "NO";
$inafecta=(isset($cab['inafecto'])) ? $cab['inafecto'] : "0.00";

$txtSUB_TOTAL=abs($txtSUB_TOTAL);

$fechacambio=$txtFECHA_DOCUMENTO;
$periodo=date("Y-m",strtotime($txtFECHA_DOCUMENTO));

$jsondata['seriefinal']=$txtSERIE;

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

$act=$_GET['act'];

if($act==1){



    $sqlnn="SELECT *FROM venta WHERE txtSERIE='$txtSERIE' AND pedido='1' AND idempresa='$_COOKIE[id]' AND beta='$fa[tipo]' AND idlocal='$_COOKIE[idlocal]' ORDER BY txtNUMERO DESC ";
    $mostrarnn= ejecutarConsultaSimpleFila($sqlnn);

    $snum='00000001';
    if($mostrarnn){ $snum=$mostrarnn['txtNUMERO']+1; }
    $snum=str_pad($snum, 8, "0", STR_PAD_LEFT);

// Si es crédito (idpago=55) y nivel 0 => serie = BASEDOC-XX (01,02,03...) por venta
if($baseDoc!='' && $nivelpago=='0' && (string)$idpago==='55'){
  $sqlLast = "SELECT serie FROM caja_ventapago WHERE idventa='$idventanew' AND serie LIKE '".$baseDoc."-%' ORDER BY id DESC LIMIT 1";
  $last = ejecutarConsultaSimpleFila($sqlLast);
  $next = 1;
  if($last && isset($last['serie'])){
    $tmp = explode('-', $last['serie']);
    $suf = end($tmp);
    if(is_numeric($suf)) $next = intval($suf) + 1;
  }
  $snum = $baseDoc.'-'.str_pad((string)$next, 2, "0", STR_PAD_LEFT);
}


    $jsondata['serie'] = $txtSERIE;
    $jsondata['idventaf'] = $idventa;

    if($idventa!='0'){

        $sqlv="SELECT *FROM venta WHERE idventa='$idventa' ";
        $vendedor= ejecutarConsultaSimpleFila($sqlv);
        $idventanew=$idventa;

        $sqlv="DELETE FROM detalle_venta WHERE idventa='$idventa'";
        ejecutarConsulta($sqlv);
        /*
        $sql="DELETE FROM venta WHERE idventa='$idventa'";
        ejecutarConsulta($sql);
        */
        
        /*
            if($guia5==''){
            $nombrepaciente="$cliente";
        }else{
            $nombrepaciente=$guia5;
        }
*/
$nombrepaciente=$guia5;

$sqlup="UPDATE venta SET idusuario='$idusuario', txtFECHA_DOCUMENTO='$txtFECHA_DOCUMENTO', doc_relaciona='$txtCOD_TIPO_DOCUMENTO', fecha_vto='$fechavto', txtOBSERVACION='$txtOBSERVACION', txtSUB_TOTAL='$txtSUB_TOTAL', txtIGV='$txtTOTAL_IGV', percepcion='$percepcion', retencion='$retencion', iddetraccion='$iddetraccion', detraccion='$montodetraccion',  ICB='$icb', descuento='$totdescuento', txtTOTAL='$txtTOTAL', gratuita='$txtTOTAL_GRATUITAS', txtID_CLIENTE='$cliente', exonerado='$txtTOTAL_EXONERADAS', comision='$comisiont', tarjeta='$tarjeta', txtID_MONEDA='$txtCOD_MONEDA', tipo_pago='$txtPAGO', medio_pago='$mediopago', tipoguia='$tipoguia', guia='$guia', presupuesto='$ocompra', referencia='$serieanticipo', condiciones='$condiciones', estadopago='$idanticipo', referencial='$referencial', controlpresupuestal='$controlpresupuestal', guia2='$guia2', tipoguia2='$tipoguia2', guia3='$guia3', tipoguia3='$tipoguia3', guia4='$guia4', tipoguia4='$tipoguia4', guia5='$nombrepaciente', tipoguia5='$tipoguia5', exportacion='$exportacion', inafecta='$inafecta'  WHERE idventa='$idventa' ";
        ejecutarConsulta($sqlup);


        $snum=$txtNUMERO;

    }else{

$sql="INSERT INTO venta (pedido, sector, txtID_CLIENTE,idusuario, idlocal, txtID_TIPO_DOCUMENTO, docmodifica_tipo, docmodifica, modifica_motivo, modifica_motivod, txtSERIE, txtNUMERO, txtFECHA_DOCUMENTO, txtOBSERVACION, txtSUB_TOTAL, txtIGV, retencion, iddetraccion, detraccion, ICB, descuento, txtTOTAL, gratuita, exonerado, txtID_MONEDA, tipo_pago, medio_pago, estado, idempresa, beta, fecha_vto, doc_relaciona, comision, tarjeta, tipocambio, orden, tipoguia, guia, presupuesto, referencia, condiciones, hash_cpe, hash_cdr, mensaje, estadopago, inafecta, percepcion, kardex, referencial, controlpresupuestal, tipoguia2, guia2, tipoguia3, guia3, tipoguia4, guia4, tipoguia5, guia5, idcaja, exportacion)
VALUES ('$act', '$idcategoria', '$cliente', '$idusuario', '$idlocal', '$tipodoc', '', '$modifica', '$motivo', '$motivod', '$txtSERIE', '$snum', '$txtFECHA_DOCUMENTO', '$txtOBSERVACION', '$txtSUB_TOTAL', '$txtTOTAL_IGV', '$retencion', '$iddetraccion', '$montodetraccion', '$icb', '$totdescuento', '$txtTOTAL', '$txtTOTAL_GRATUITAS', '$txtTOTAL_EXONERADAS', '$txtCOD_MONEDA', '$txtPAGO', '$mediopago', '0', '$_COOKIE[id]', '$fa[tipo]', '$fechavto', '$txtCOD_TIPO_DOCUMENTO', '$comisiont', '$tarjeta', '$tchoy[venta]', '', '$tipoguia', '$guia', '$ocompra', '$serieanticipo', '$condiciones', '', '', '', '0', '$inafecta', '$percepcion', '$kardex', '$referencial', '$controlpresupuestal', '$tipoguia2', '$guia2', '$tipoguia3', '$guia3', '$tipoguia4', '$guia4', '$tipoguia5', '$cliente', '0', 'NO')";

        $idventanew=ejecutarConsulta_retornarID($sql);

//echo $sql;


}


for ($i = 0; $i < count($detalle); $i++) {

        $id=(isset($detalle[$i]["txtID"]))?$detalle[$i]["txtID"]:"0";
        $tipo=(isset($detalle[$i]["tipo"]))?$detalle[$i]["tipo"]:"0";
        $serie=(isset($detalle[$i]["serie"]))?$detalle[$i]["serie"]:"0";
        $proveedor=(isset($detalle[$i]["proveedor"]))?$detalle[$i]["proveedor"]:"0";
        $codigo=(isset($detalle[$i]["txtCODIGO_DET"]))?$detalle[$i]["txtCODIGO_DET"]:"0";
        $nombre=(isset($detalle[$i]["txtDESCRIPCION_DET"]))?$detalle[$i]["txtDESCRIPCION_DET"]:"0";
        $cti=(isset($detalle[$i]["txtCANTIDAD_DET"]))?$detalle[$i]["txtCANTIDAD_DET"]:"0";
        $subtotal=(isset($detalle[$i]["txtPRECIO_DET"]))?$detalle[$i]["txtPRECIO_DET"]:"0";
        $importe=(isset($detalle[$i]["txtIMPORTE_DET"]))?$detalle[$i]["txtIMPORTE_DET"]:"0";
        $igv=(isset($detalle[$i]["txtIGV"]))?$detalle[$i]["txtIGV"]:"0";
        $precio=(isset($detalle[$i]["txtPRECIO"]))?$detalle[$i]["txtPRECIO"]:"0";
        $descuento=(isset($detalle[$i]["descuento"]))?$detalle[$i]["descuento"]:"0";
        $placa=(isset($detalle[$i]["placa"]))?$detalle[$i]["placa"]:"";
        $ctiunidad=(isset($detalle[$i]["ctiunidad"]))?$detalle[$i]["ctiunidad"]:"0";
        $comision=(isset($detalle[$i]["comision"]))?$detalle[$i]["comision"]:"0";
        $UNIDAD_MEDIDA=(isset($detalle[$i]["UNIDAD_MEDIDA"]))?$detalle[$i]["UNIDAD_MEDIDA"]: "0";
        $tipoart=(isset($detalle[$i]["tipoart"]))?$detalle[$i]["tipoart"]: "0";
        $idunit=(isset($detalle[$i]["idunit"]))?$detalle[$i]["idunit"]: "0";

        $exonerado=(isset($detalle[$i]["exonerado"]))?$detalle[$i]["exonerado"]: "0";
        $gratuita=(isset($detalle[$i]["gratuita"]))?$detalle[$i]["gratuita"]: "0";
        $icbd=(isset($detalle[$i]["icbd"]))?$detalle[$i]["icbd"]: "0";
        $detracciond=(isset($detalle[$i]["detracciond"]))?$detalle[$i]["detracciond"]: "0";

        $iddestino=(isset($detalle[$i]["iddestino"]))?$detalle[$i]["iddestino"]: "0";
        $carga_util=(isset($detalle[$i]["carga_util"]))?$detalle[$i]["carga_util"]: "0";
        $cantidad_toneladas=(isset($detalle[$i]["cantidad_toneladas"]))?$detalle[$i]["cantidad_toneladas"]: "0";
        $inafectadet=(isset($detalle[$i]["inafecta"]))?$detalle[$i]["inafecta"]: "0.00";
        
        $idcaja=(isset($value->idcaja))?$value->idcaja: "0";

        if($iddestino==''){ $iddestino='0'; }
        if($inafectadet==''){ $inafectadet='0.00'; }
        if($carga_util==''){ $carga_util='0'; }
        if($cantidad_toneladas==''){ $cantidad_toneladas='0'; }

        $stock='0';

        if($detracciond==''){ $detracciond='0'; }
        if($gratuita==''){ $gratuita='0'; }
        if($exonerado==''){ $exonerado='0'; }
        if($descuento==''){ $descuento='0'; }
        if($idunit==''){ $idunit='0'; }
        if($ctiunidad==''){ $ctiunidad='0'; }
        if($cti==''){ $cti='0'; }
        if($descuento==''){ $descuento='0'; }
        if($serie==''){ $serie='0'; }

        $subtotal=(isset($detalle[$i]["txtPRECIO_DET"]))?$detalle[$i]["txtPRECIO_DET"]: "0";

        if($ctiunidad>'0'){
            $ctitot=$cti*$ctiunidad;
        }else{
            $ctitot=$ctiunidad;
        }
        $sqldi="SELECT *FROM detalle_ingreso WHERE txtCOD_ARTICULO='$id' AND estado='0' AND idlocal='$_COOKIE[idlocal]' ORDER BY iddetalle_ingreso DESC ";
        $most= ejecutarConsultaSimpleFila($sqldi);

        if($most){ $preciocompra=$most['precio_compra']; }else{ $preciocompra=0; }

        $nombre= str_replace("|", "\n", $nombre);

$sql_detalle = "INSERT INTO detalle_venta VALUES (NULL, '$_COOKIE[id]', '$_COOKIE[idlocal]', '$fa[tipo]', '$idventanew', '$tipoart', '$id', '$codigo', '$UNIDAD_MEDIDA', '$idunit', '$nombre', '$cti', '$ctiunidad', '$precio', '$preciocompra', '$descuento', '$subtotal', '$igv', '$icbd', '$importe', '$exonerado', '$inafectadet', '$gratuita', '$detracciond',  '$comision', '$stock', '$tipo', '0', '', '$placa', '$serie', '$proveedor', '$iddestino', '$carga_util', '$cantidad_toneladas', '$txtFECHA_DOCUMENTO', '0')";

//echo $sql_detalle;
 $iddetalle=ejecutarConsulta_retornarID($sql_detalle) or $sw = false;

}

$jsondata['estado'] = '16';
$jsondata['mensaje'] = 'Documento guardado';

}else{

/*AQUI GENERAMOS LA SALIDA DE ALMACEN*/
/*VENTA ACT=0*/


if($txtSERIE==''){

        $sqls="SELECT *FROM series WHERE documento='$txtCOD_TIPO_DOCUMENTO' AND tipo='02' AND idlocal='$idlocal' AND estado='1' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
        $s= ejecutarConsultaSimpleFila($sqls);

        $txtSERIE=$s['serie'];

    }

    $sqls="SELECT *FROM series WHERE serie='$txtSERIE' AND tipo='02' AND estado='1' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
    $ser= ejecutarConsultaSimpleFila($sqls);
//POR SI ES NOTA DE CREDITO O DEBITO
if($txtCOD_TIPO_DOCUMENTO=='07'||$txtCOD_TIPO_DOCUMENTO=='08'){
$sqls="SELECT *FROM series WHERE documento='$txtCOD_TIPO_DOCUMENTO' AND tipo='02' AND estado='1' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
$ser= ejecutarConsultaSimpleFila($sqls);

if($motivo=='01'||$motivo=='02'){
credito($txtTOTAL, 'RESTA', $cliente);
}



}

$sqlnn="SELECT *FROM venta WHERE txtSERIE='$txtSERIE' AND txtID_TIPO_DOCUMENTO='$txtCOD_TIPO_DOCUMENTO' AND idempresa='$_COOKIE[id]' AND beta='$fa[tipo]' ORDER BY txtNUMERO DESC ";
$mostrarnn= ejecutarConsultaSimpleFila($sqlnn);

    /*
    $sqlnn="SELECT *FROM venta WHERE txtSERIE='$txtSERIE' AND txtID_TIPO_DOCUMENTO='$txtCOD_TIPO_DOCUMENTO' AND idempresa='$_COOKIE[id]' AND beta='$fa[tipo]' ORDER BY txtFECHA_DOCUMENTO  DESC, txtNUMERO DESC, idventa DESC ";
    $mostrarnn= ejecutarConsultaSimpleFila($sqlnn);
    */

    $snum=$ser['numeroinicio'];
    if($mostrarnn){	$snum=$mostrarnn['txtNUMERO']+1;}
    $snum=str_pad($snum, 8, "0", STR_PAD_LEFT);

    $jsondata['numerof3'] =$ser['numeroinicio'];
    $jsondata['txtSERIE'] =$txtSERIE;
    $jsondata['tdocumento'] =$txtCOD_TIPO_DOCUMENTO;
    $jsondata['idlocal'] =$idlocal;

    if($txtCOD_TIPO_DOCUMENTO=='07'||$txtCOD_TIPO_DOCUMENTO=='08'){

        $sql="SELECT *FROM venta WHERE txtSERIE='$smodifica' AND txtNUMERO='$nmodifica' AND idempresa='$_COOKIE[id]' AND (txtID_TIPO_DOCUMENTO='01' OR txtID_TIPO_DOCUMENTO='03' ) AND beta='$fa[tipo]' ";
        $mostrarv= ejecutarConsultaSimpleFila($sql);
        $cliente=$mostrarv['txtID_CLIENTE'];
        $txtCOD_MONEDA=$mostrarv['txtID_MONEDA'];

    }

    if($act==''){ $act='0'; }
    if($cliente==''){ $cliente='0'; }
    if($idlocal==''){ $idlocal='0'; }
    if($txtTOTAL_GRATUITAS==''){ $txtTOTAL_GRATUITAS='0'; }
    if($txtTOTAL_EXONERADAS==''){ $txtTOTAL_EXONERADAS='0'; }
    if($comisiont==''){ $comisiont='0'; }

    $costodolar=$tchoy['venta'];

    $sql3="SELECT *FROM persona WHERE idpersona='$cliente' ";
    $cli= ejecutarConsultaSimpleFila($sql3);

    $sqlca="SELECT * FROM cajas WHERE id_usuario='$idusuario' AND idempresa='$_COOKIE[id]' AND estado='1' AND beta='$fa[tipo]' ";
    $caj= ejecutarConsultaSimpleFila($sqlca);
    $idcaja='0';
    if($caj){ $idcaja=$caj['id']; }

    $mach_id='';
    $mach_numero='';
    $mach_monto='0.00';
    $mach_fecha=$txtFECHA_DOCUMENTO;
    $mach_observaciones='';

    $sql="INSERT INTO venta VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$act', '$idcategoria', '$controlpresupuestal', '$exportacion', '$cliente', '$idusuario', '$idlocal', '$txtCOD_TIPO_DOCUMENTO', '$modifica', '$tipodoc', '$modifica', '$motivo', '$motivod', '$txtSERIE', '$snum', '$txtFECHA_DOCUMENTO', '$fechavto', '$txtOBSERVACION', '$txtSUB_TOTAL', '$txtTOTAL_IGV', '$percepcion', '$retencion', '$iddetraccion', '$montodetraccion', '$icb', '$totdescuento', '$txtTOTAL', '$referencial', '$txtTOTAL_GRATUITAS', '$txtTOTAL_EXONERADAS', '$inafecta', '$comisiont', '$tarjeta', '$costodolar', '$txtCOD_MONEDA', '$txtPAGO', '$mediopago', '', '$tipoguia', '$guia', '$tipoguia2', '$guia2', '$tipoguia3', '$guia3', '$tipoguia4', '$guia4', '$tipoguia5', '$cliente',  '$ocompra', '$serieanticipo', '$condiciones', '', '', '', '$mach_id', '$mach_numero', '$mach_monto', '$mach_fecha', '$mach_observaciones', '0', '$idanticipo', '$kardex', '$idcaja')";
//echo $sql;
    $idventanew=ejecutarConsulta_retornarID($sql);

    /*GUARDAMOS PUNTOS Y SUMAMOS PUNTOS*/
    if($puntos!='0'){

        $sql="INSERT INTO venta_puntos VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '0', '$idlocal', '$idventanew', '$cliente', '$puntos', '$txtFECHA_DOCUMENTO')";
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

/**
 * GUARDAMOS LOS PAGOS
*/
$nivelpago='0';
for ($i = 0; $i < count($detallepago); $i++) {

    $idpago=(isset($detallepago[$i]["idpago"]))?$detallepago[$i]["idpago"]:"0";
    $fechavence=(isset($detallepago[$i]["fechavence"]))?$detallepago[$i]["fechavence"]:"0000-00-00";
    $moneda=(isset($detallepago[$i]["moneda"]))?$detallepago[$i]["moneda"]:"PEN";
    $montosoles=(isset($detallepago[$i]["montosoles"]))?$detallepago[$i]["montosoles"]:"0";
    $montodolar=(isset($detallepago[$i]["montodolar"]))?$detallepago[$i]["montodolar"]:"0";
    $tcambio=(isset($detallepago[$i]["tcambio"]))?$detallepago[$i]["tcambio"]:"0";
    $operacion=(isset($detallepago[$i]["operacion"]))?$detallepago[$i]["operacion"]:"";

    $sqlci="SELECT *FROM caja_ventapago WHERE nivel='$nivelpago' AND tipopago='$txtPAGO' ORDER BY id DESC ";
    $ci= ejecutarConsultaSimpleFila($sqlci);
    
    $snum='00000001';
    if($ci){ $snum=$ci['serie']+1; }
    $snum=str_pad($snum, 8, "0", STR_PAD_LEFT);	

if($txtCOD_MONEDA=='PEN'){
    $soles=$montosoles;
    $dolares=$montosoles/$tcambio;
    $dolares=round($dolares, 3);
}else{
    $dolares=$montosoles;
    $soles=$montosoles*$tcambio;
    $soles=round($soles, 3);
}

$sql_detallepago= "INSERT INTO caja_ventapago VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$_COOKIE[idlocal]', '$idusuario', '$txtPAGO', '0', '$nivelpago', '$idventanew', '$idpago', '$snum', '$txtCOD_MONEDA', '$soles', '$dolares', '$tcambio', '$operacion', '$txtFECHA_DOCUMENTO', '$fechavence', '$fechavence', '', '1')";
$idventpago=ejecutarConsulta_retornarID($sql_detallepago) or $sw = false;

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
        $idunit=(isset($detalle[$i]["idunit"]))?$detalle[$i]["idunit"]:"0";
        $idv=(isset($detalle[$i]["idv"]))?$detalle[$i]["idv"]: "0";
        $tipoart=(isset($detalle[$i]["tipoart"]))?$detalle[$i]["tipoart"]: "0";

        $exonerado=(isset($detalle[$i]["exonerado"]))?$detalle[$i]["exonerado"]: "0";
        $gratuita=(isset($detalle[$i]["gratuita"]))?$detalle[$i]["gratuita"]: "0";
        $icbd=(isset($detalle[$i]["icbd"]))?$detalle[$i]["icbd"]: "0";
        $detracciond=(isset($detalle[$i]["detracciond"]))?$detalle[$i]["detracciond"]: "0";

        $iddestino=(isset($detalle[$i]["iddestino"]))?$detalle[$i]["iddestino"]: "0";
        $carga_util=(isset($detalle[$i]["carga_util"]))?$detalle[$i]["carga_util"]: "0";
        $cantidad_toneladas=(isset($detalle[$i]["cantidad_toneladas"]))?$detalle[$i]["cantidad_toneladas"]: "0";
        $inafectadet=(isset($detalle[$i]["inafecta"]))?$detalle[$i]["inafecta"]: "0.00";


        $idcaja=(isset($value->idcja))?$value->idcaja: "0";

        if($iddestino==''){ $iddestino='0'; }
        if($carga_util==''){ $carga_util='0'; }
        if($cantidad_toneladas==''){ $cantidad_toneladas='0'; }

        if($detracciond==''){ $detracciond='0'; }
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
        if($inafectadet==''){ $inafectadet='0'; }
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

        $sql_detalle = "INSERT INTO detalle_venta VALUES (NULL, '$_COOKIE[id]', '$_COOKIE[idlocal]', '$fa[tipo]', '$idventanew', '$tipoart', '$id', '$codigo', '$UNIDAD_MEDIDA', '$idunit', '$nombre', '$cti', '$ctiunidad', '$precio', '0', '$descuento', '$subtotal', '$igv', '$icbd', '$importe', '$exonerado', '$inafectadet', '$gratuita', '$detracciond',  '$comision', '0', '$tipo', '0', '', '$placa', '$serie', '$proveedor', '$iddestino', '$carga_util', '$cantidad_toneladas',  '$fechakardex', '$idcaja')";

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

        if($kardex=='0'){
            guardakardex($id, $idventanew, $idvent, $tipodocf, $txtSERIE, $snum, $ctit, $ctiunidad, $fechacambio, $UNIDAD_MEDIDA, $tipoart, $fa['tipo'], $serie, $periodo, $tipo, $txtCOD_MONEDA, $precio, $costodolar, $cliente, $idusuario, $nombre, $motivo, $idlocal);
        }

}

    $jsondata['estado'] = '1';
    $jsondata['mensaje'] = "Documento Guardado";

    $jsondata['txtPAGO'] = $txtPAGO;

    $tipodoc=substr($txtSERIE,0,1);

    if($fa['directo']=='SI'){
//'$txtPAGO', '$mediopago',
        if($txtPAGO=='CONTADO'){

            if($tipodoc2!='90'){
                $vasunat="1";
                enviarfactura($idventanew);
            }

        }
    }else{

    }

}

$jsondata['idventa'] = $idventanew;
echo json_encode($jsondata);
exit();

?>