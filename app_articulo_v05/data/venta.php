<?php 

if(isset($rutat)){ require "config/conexion.php"; require "modelos/caja.php"; }else{ require "../config/conexion.php"; require "../modelos/caja.php"; }

if (isset($_SERVER['HTTP_ORIGIN'])) {  
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");  
    header('Access-Control-Allow-Credentials: true');  
    header('Access-Control-Max-Age: 86400');   
}  
  
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {  
  
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))  
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  
  
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))  
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");  
}

require_once "../modelos/Venta.php";
require_once "../modelos/envio.php";
require_once "../funcionesGlobales/catalogo_afectaciones.php";

$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã", "ÃŠ", "ÃŽ", "Ã", "Ã›", "ü","Ã¶", "Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã","Ã‹", "*","%", "'", '"');
$permitidas= array ("a","e","i","o","u","A","E","I","O","U","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i", "a", "e", "U", "I", "A", "E", ".", ".", "", "");

$venta=new Venta();


function opcionesAfectacionVentaSelect($selected='10'){
    $idempresa = isset($_COOKIE['id']) ? (int)$_COOKIE['id'] : 0;
    return caf_options_html($selected, $idempresa, true);
}

function esModoBetaSunatVenta($tipo){
    $valor = strtoupper(trim((string)$tipo));
    return in_array($valor, array('3','03','BETA'), true);
}


$idventa=isset($_POST["idventa"])? limpiarCadena($_POST["idventa"]):"";
$txtID_CLIENTE=isset($_POST["txtID_CLIENTE"])? limpiarCadena($_POST["txtID_CLIENTE"]):"";

if(isset($_COOKIE["idusuario"])){
$idusuario=$_COOKIE["idusuario"];	
}

$tcambio=isset($_POST["tcambio"])? limpiarCadena($_POST["tcambio"]):"0";
$tpago=isset($_POST["tpago"])? limpiarCadena($_POST["tpago"]):"0";
$moneda=isset($_POST["moneda"])? limpiarCadena($_POST["moneda"]):"0";
$operacion=isset($_POST["operacion"])? limpiarCadena($_POST["operacion"]):"";

$txtID_TIPO_DOCUMENTO=isset($_POST["txtID_TIPO_DOCUMENTO"])? limpiarCadena($_POST["txtID_TIPO_DOCUMENTO"]):"";
$txtSERIE=isset($_POST["txtSERIE"])? limpiarCadena($_POST["txtSERIE"]):"";
$txtNUMERO=isset($_POST["txtNUMERO"])? limpiarCadena($_POST["txtNUMERO"]):"";
$txtFECHA_DOCUMENTO=isset($_POST["txtFECHA_DOCUMENTO"])? limpiarCadena($_POST["txtFECHA_DOCUMENTO"]):"";
$txtTIPO_DOCUMENTO_CLIENTE=isset($_POST["txtTIPO_DOCUMENTO_CLIENTE"])? limpiarCadena($_POST["txtTIPO_DOCUMENTO_CLIENTE"]):"";
$txtOBSERVACION=isset($_POST["txtOBSERVACION"])? limpiarCadena($_POST["txtOBSERVACION"]):"";
$txtSUB_TOTAL=isset($_POST["txtSUB_TOTAL"])? limpiarCadena($_POST["txtSUB_TOTAL"]):"";
$txtIGV=isset($_POST["txtIGV"])? limpiarCadena($_POST["txtIGV"]):"";
$txtTOTAL=isset($_POST["txtTOTAL"])? limpiarCadena($_POST["txtTOTAL"]):"";
$txtID_MONEDA=isset($_POST["txtID_MONEDA"])? limpiarCadena($_POST["txtID_MONEDA"]):"";
$txtID_TIPO_DOCUMENTO_MODIFICA=isset($_POST["txtID_TIPO_DOCUMENTO_MODIFICA"])? limpiarCadena($_POST["txtID_TIPO_DOCUMENTO_MODIFICA"]):"";
$txtNRO_DOC_MODIFICA=isset($_POST["txtNRO_DOC_MODIFICA"])? limpiarCadena($_POST["txtNRO_DOC_MODIFICA"]):"";
$txtID_MOTIVO=isset($_POST["txtID_MOTIVO"])? limpiarCadena($_POST["txtID_MOTIVO"]):"";
$txtCOD_ARTICULO=isset($_POST["txtCOD_ARTICULO"])? limpiarCadena($_POST["txtCOD_ARTICULO"]):"";
$txtCANTIDAD_ARTICULO=isset($_POST["txtCANTIDAD_ARTICULO"])? limpiarCadena($_POST["txtCANTIDAD_ARTICULO"]):"";
$txtPRECIO_ARTICULO=isset($_POST["txtPRECIO_ARTICULO"])? limpiarCadena($_POST["txtPRECIO_ARTICULO"]):"";
$nivel=isset($_POST["nivel"])? limpiarCadena($_POST["nivel"]):"0";
$id=isset($_POST["id"])? limpiarCadena($_POST["id"]):"0";
$anticipo=isset($_POST["anticipo"])? limpiarCadena($_POST["anticipo"]):"";

$properiodo=isset($_POST["properiodo"])? limpiarCadena($_POST["properiodo"]):"";
$periodo=isset($_POST["periodo"])? limpiarCadena($_POST["periodo"]):"";
$letras=isset($_POST["letras"])? limpiarCadena($_POST["letras"]):"";
$tipopagodet=isset($_POST["tipopagodet"])? limpiarCadena($_POST["tipopagodet"]):"CREDITO";
$tipopago=isset($_POST["tipopago"])? limpiarCadena($_POST["tipopago"]):"0";
$precio=isset($_POST["precio"])? limpiarCadena($_POST["precio"]):"0";

$fechahoy= date('Y-m-d');

function revisar($tipoc, $serie, $numero, $ruta, $token){

$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'DOCUMENTO PENDIENTE DE SER ANULADO';
	
$data2 = array(	
"operacion"=> "consultar_comprobante",
  "tipo_de_comprobante"=> $tipoc,
  "serie"=> $serie,
  "numero"=> $numero
);

$data_json = json_encode($data2);
		
//Invocamos el servicio de NUBEFACT
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ruta);
curl_setopt(
	$ch, CURLOPT_HTTPHEADER, array(
	'Authorization: Token token="'.$token.'"',
	'Content-Type: application/json',
	)
);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$respuesta  = curl_exec($ch);
curl_close($ch);
		
$leer_respuesta = json_decode($respuesta, true);
if (isset($leer_respuesta['errors'])) {
	//Mostramos los errores si los hay
	$jsondata['estado'] = '0';
	$jsondata['mensaje'] = $leer_respuesta['errors'];
	
$sql="UPDATE venta SET estado='5', mensaje='$leer_respuesta[errors]' WHERE idventa='$idventa' ";
	
} else {
	
$jsondata['estado'] = '1';
	
if($leer_respuesta['aceptada_por_sunat']==false){
$jsondata['aceptado'] =$leer_respuesta['aceptada_por_sunat'];
$jsondata['cod_sunat'] ='99999';
$jsondata['mensaje'] = 'ENVIADO A SUNAT';	
}else{
	
$jsondata['aceptado'] =$leer_respuesta['aceptada_por_sunat'];
$jsondata['cod_sunat'] =$leer_respuesta['sunat_responsecode'];
$jsondata['mensaje'] = $leer_respuesta['sunat_description'];
	
}
$sql="UPDATE venta SET estado='6', hash_cdr='$leer_respuesta[codigo_hash]', mensaje='$leer_respuesta[sunat_description]' WHERE idventa='$idventa' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'DOCUMENTO ANULADO';

}		


echo json_encode($jsondata);	

}


function restardias($fecha1, $fecha2){
//calculo timestam de las dos fechas
$por=explode("-", $fecha1);
$dia1=$por[2];
$mes1=$por[1];
$ano1=$por[0];
	
$por2=explode("-", $fecha2);
$dia2=$por2[2];
$mes2=$por2[1];
$ano2=$por2[0];
	
$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2);

//resto a una fecha la otra
$segundos_diferencia = $timestamp1 - $timestamp2;
//echo $segundos_diferencia;

//convierto segundos en días
$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

//obtengo el valor absoulto de los días (quito el posible signo negativo)
$dias_diferencia = abs($dias_diferencia);

//quito los decimales a los días de diferencia
$dias_diferencia = floor($dias_diferencia);

return $dias_diferencia;
}




// ============================================================
// Helpers para Select2 (evita romper si cambian columnas)
// ============================================================
if(!function_exists('_table_has_column')){
  function _table_has_column($table, $col){
    static $cache = [];
    $k = $table.'|'.$col;
    if(isset($cache[$k])) return $cache[$k];
    $ok = false;
    $rs = ejecutarConsulta("DESCRIBE `".$table."`");
    if($rs){
      while($r = $rs->fetch_assoc()){
        if(isset($r['Field']) && $r['Field'] === $col){ $ok = true; break; }
      }
    }
    $cache[$k] = $ok;
    return $ok;
  }
}

// Verifica si una tabla existe (para compatibilidad entre BD)
if(!function_exists('_table_exists')){
  function _table_exists($table){
    static $cache = [];
    if(isset($cache[$table])) return $cache[$table];
    $ok = false;
    $rs = ejecutarConsulta("SHOW TABLES LIKE '".str_replace("'","",$table)."'");
    if($rs){
      $row = $rs->fetch_row();
      if($row && isset($row[0])) $ok = true;
    }
    $cache[$table] = $ok;
    return $ok;
  }
}

// ============================================================
// Contexto: idempresa (evita confundir cookie "id" = idusuario)
// - Prioridad: $_SESSION['idempresa'] -> $_COOKIE['idempresa']
// - Si no existe, devuelve '0' (y el query NO filtra para no quedar vacío)
// ============================================================
if(!function_exists('_ctx_idempresa')){
  /**
   * Devuelve el idempresa de la sesión actual.
   * En este proyecto, el idempresa suele venir en la cookie 'id'.
   * Fallbacks: $_SESSION['idempresa'], $_COOKIE['idempresa'], $_COOKIE['empresa_id'].
   */
  function _ctx_idempresa(){
    // 1) Cookie principal del proyecto (empresa)
    if(isset($_COOKIE['id']) && trim((string)$_COOKIE['id']) !== ''){
      return trim((string)$_COOKIE['id']);
    }
    // 2) Session (si el proyecto la usa)
    if(isset($_SESSION) && isset($_SESSION['idempresa']) && trim((string)$_SESSION['idempresa']) !== ''){
      return trim((string)$_SESSION['idempresa']);
    }
    // 3) Cookies alternativas
    if(isset($_COOKIE['idempresa']) && trim((string)$_COOKIE['idempresa']) !== ''){
      return trim((string)$_COOKIE['idempresa']);
    }
    if(isset($_COOKIE['empresa_id']) && trim((string)$_COOKIE['empresa_id']) !== ''){
      return trim((string)$_COOKIE['empresa_id']);
    }
    return '0';
  }
}

switch ($_GET["op"]){
	case 'guardaryeditar':

		
$sql="SELECT *FROM persona WHERE idpersona='$reg->txtCOD_ARTICULO' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
		
		
$rspta=$venta->insertar($txtID_CLIENTE,$idusuario,$txtID_TIPO_DOCUMENTO,$txtSERIE,$txtNUMERO,$txtFECHA_DOCUMENTO,$txtTIPO_DOCUMENTO_CLIENTE,$txtOBSERVACION,$txtSUB_TOTAL,$txtIGV,$txtTOTAL, $txtID_MONEDA,$txtID_TIPO_DOCUMENTO_MODIFICA,$txtNRO_DOC_MODIFICA,$txtID_MOTIVO,$txtCOD_ARTICULO,$txtCANTIDAD_ARTICULO,$txtPRECIO_ARTICULO);
echo $rspta ? "Venta registrada" : "No se pudieron registrar todos los datos de la venta";


	break;

	case 'mostrarpedido':
	$sql="SELECT v.idventa, v.txtOBSERVACION, DATE(v.txtFECHA_DOCUMENTO) as fecha, v.txtID_CLIENTE, p.nombre as cliente, u.idusuario, u.nombre as usuario, v.txtID_TIPO_DOCUMENTO, v.txtSERIE, v.txtNUMERO, v.txtSUB_TOTAL, v.txtTOTAL, v.txtIGV, v.estado, v.tipoguia, v.guia, v.tipoguia2, v.guia2, v.tipoguia3, v.guia3, v.tipoguia4, v.guia4, v.tipoguia5, v.guia5, v.fpago_mpago, COALESCE(tpp.descripcion,'') AS fpago_mpago_text, v.presupuesto, v.referencia, v.doc_relaciona, v.txtID_MONEDA, v.tipo_pago, v.medio_pago, COALESCE(tp.descripcion,'') AS medio_pago_text, v.orden, v.sector, v.iddetraccion, v.detraccion, v.gratuita, v.exonerado, v.inafecta, v.descuento, v.dto_global_monto, v.dto_global_tipo, v.dto_global_valor, v.dto_global_modo, v.dto_global_afecta_base, v.dto_global_afecta_igv, v.dto_global_monto_base, v.dto_global_monto_igv, v.dto_global_aplica_antes_igv, v.dto_global_prorrateado, v.total_descuentos_item, v.total_descuentos_global, v.total_descuentos_prorrateados, v.total_descuentos_base, v.total_descuentos_igv, v.total_descuentos_no_base, v.total_valor_venta_bruto, v.total_valor_venta_neto, v.total_igv_bruto, v.total_igv_neto, v.total_bruto_operacion, v.total_neto_operacion, dt.codigo AS codidetracciones, dt.porcentaje, v.retencion, v.percepcion, v.referencial FROM venta v INNER JOIN persona p ON v.txtID_CLIENTE=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario LEFT JOIN detracciones dt ON dt.id=v.iddetraccion LEFT JOIN caja_tipopago_persona tpp ON tpp.id = COALESCE(NULLIF(v.fpago_mpago,''), NULLIF(v.guia5,'')) LEFT JOIN caja_tipopago tp ON tp.id = v.medio_pago WHERE v.idventa='$idventa' ";
$rspta=ejecutarConsultaSimpleFila($sql);
 		echo json_encode($rspta);
	break;
		
case 'mostrarorden':
		
$sql="SELECT v.idventa, v.txtOBSERVACION, DATE(v.txtFECHA_DOCUMENTO) as fecha, v.txtID_CLIENTE, p.nombre as cliente, u.idusuario, u.nombre as usuario, v.txtID_TIPO_DOCUMENTO, v.txtSERIE, v.txtNUMERO, v.txtTOTAL, v.txtIGV, v.estado, v.guia, v.presupuesto, v.referencia, v.doc_relaciona, v.txtID_MONEDA, v.tipo_pago, v.medio_pago, v.orden, v.sector, v.iddetraccion, v.detraccion, dt.codigo AS codidetracciones, dt.porcentaje FROM venta v LEFT JOIN persona p ON v.txtID_CLIENTE=p.idpersona LEFT JOIN usuario u ON v.idusuario=u.idusuario LEFT JOIN detracciones dt ON dt.id=v.iddetraccion WHERE v.idventa='$idventa' ";
$rspta=ejecutarConsultaSimpleFila($sql);
		
echo json_encode($rspta);
		
	break;
	
case 'mostrar-salida-almacen':
		
$sql="SELECT * FROM venta_orden WHERE id='$idventa' ";
$rspta=ejecutarConsultaSimpleFila($sql);
		
echo json_encode($rspta);
		
break;

case 'select2_categoria_nivel':
header('Content-Type: application/json; charset=utf-8');
$nivel = isset($_GET['nivel']) ? (int)$_GET['nivel'] : 0;
$term  = isset($_GET['q']) ? trim($_GET['q']) : '';
$idemp = isset($_COOKIE['id']) ? (int)$_COOKIE['id'] : 0;
$hasNivel = _table_has_column('categoria','nivel');
$hasIdNivel = _table_has_column('categoria','idnivel');
$hasEmp   = _table_has_column('categoria','idempresa');
$hasCond  = _table_has_column('categoria','condicion');
$where = " WHERE 1=1 ";
if($hasNivel){ $where .= " AND nivel='".$nivel."' "; }
if($hasIdNivel){ $where .= " AND idnivel='0' "; }
if($hasCond){ $where .= " AND condicion='1' "; }
if($hasEmp && $idemp>0){ $where .= " AND idempresa='".$idemp."' "; }
if($term!==''){ $where .= " AND nombre LIKE '%".limpiarCadena($term)."%' "; }
$sql = "SELECT idcategoria, nombre FROM categoria ".$where." ORDER BY nombre ASC LIMIT 200";
$rs = ejecutarConsulta($sql);
$out = array();
while($r = $rs->fetch_object()){
  $nom = trim((string)$r->nombre);
  if($nom===''){ continue; }
  $out[] = array('id'=>$nom, 'text'=>$nom);
}
echo json_encode(array('results'=>$out));
break;
		
		
case 'mostrar-salida':
		
$sqls="SELECT v.idventa, v.txtOBSERVACION, DATE(v.txtFECHA_DOCUMENTO) as fecha, v.txtID_CLIENTE, p.nombre as cliente, u.idusuario, u.nombre as usuario, v.txtID_TIPO_DOCUMENTO, v.txtSERIE, v.txtNUMERO, v.txtTOTAL, v.txtIGV, v.estado, v.guia, v.presupuesto, v.referencia, v.doc_relaciona, v.txtID_MONEDA, v.tipo_pago, v.medio_pago, v.orden, v.sector, v.iddetraccion, v.detraccion, dt.codigo AS codidetracciones, dt.porcentaje FROM venta v LEFT JOIN persona p ON v.txtID_CLIENTE=p.idpersona LEFT JOIN usuario u ON v.idusuario=u.idusuario LEFT JOIN detracciones dt ON dt.id=v.iddetraccion WHERE v.idventa='$idventa' ";
$rspta=ejecutarConsultaSimpleFila($sqls);
		
echo json_encode($rspta);
		
	break;

	case 'listarDetalle':
		//Recibimos el idingreso
		header('Content-Type: application/json');
		$id=$_GET['id'];

		$rspta = $venta->listarDetalle($id);
		$txtTOTAL=0;
		
$responce=[];		
$total=0;
		
		if(isset($_GET['page'])){ $page = $_GET['page'];  }else{ $page =1; }
if(isset($_GET['rows'])){ $limit = $_GET['rows'];  }else{ $limit = ''; }
if(isset($_GET['sidx'])){ $sidx = $_GET['sidx'];   }else{ $sidx= 'iddetalle_venta'; }
if(isset($_GET['sord'])){ $sord = $_GET['sord'];   }else{ $sord= 'asc'; }
		
		
$sql="SELECT COUNT(*) AS count FROM detalle_venta WHERE idventa='$id' ";
$row= ejecutarConsultaSimpleFila($sql);
$count = $row['count']; 
		
if($limit!=''){
if(!$sidx) $sidx =1; 

if( $count > 0 && $limit > 0) { 
    $total_pages = ceil($count/$limit); 
} else { 
    $total_pages = 0; 
} 
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit;
if($start <0) $start = 0;
	
$datos = "SELECT *FROM detalle_venta WHERE idventa='$id' ORDER BY $sidx $sord LIMIT $start , $limit ";
$datos = ejecutarConsulta($datos);
	
}else{
	
$datos = "SELECT *FROM detalle_venta WHERE idventa='$id' ORDER BY $sidx $sord ";
$datos = ejecutarConsulta($datos);
	
}	

$i=0;
	
while ($reg = $rspta->fetch_object()){
					//echo '<tr class="filas"><td></td><td>'.$reg->txtDESCRIPCION_ARTICULO.'</td><td>'.$reg->txtCANTIDAD_ARTICULO.'</td><td>'.$reg->txtPRECIO_ARTICULO.'<td>'.$reg->txtSUB_TOTAL.'</td></tr>';
					//$txtTOTAL=$txtTOTAL+($reg->txtPRECIO_ARTICULO*$reg->txtCANTIDAD_ARTICULO);

$datosp = "SELECT *FROM unidad_medida WHERE id='$reg->idpresentacion' ";
$datosp = ejecutarConsultaSimpleFila($datosp);

$unidad='GENERALES';
if($datosp){
$unidad=$datosp['tit'];	
}
	
$rows[$i]['id']=$reg->idproducto;
$dtoItemMonto = isset($reg->dto_item_monto) ? $reg->dto_item_monto : (isset($reg->descuento) ? $reg->descuento : '0.00');
$rows[$i]['cell']=array($reg->idproducto, $reg->codigoproducto, $reg->tipo, $reg->nombreproducto, $reg->unidadmedida, $unidad, $reg->precio, $reg->txtCANTIDAD_ARTICULO, $reg->subtotal, $reg->igv, $reg->importe, $reg->comisiond, $reg->exoneradod, $reg->gratuitad, $reg->idpresentacion, $reg->precio, $reg->cantidadp, $reg->iddetalle_venta, $reg->tipoarticulo, $reg->detracciond, $reg->iddestino, $reg->carga_util, $reg->cantidad_toneladas, $dtoItemMonto,$dtoItemMonto);
$responce=$rows;
		
$i++;	
}
	
echo json_encode($responce);
		
	break;
		
		
		
		
case 'detallesalida':
		//Recibimos el idingreso
		header('Content-Type: application/json');
		$id=$_GET['id'];

$responce='';		
		
$sql="SELECT * FROM venta_opdetallet  where idpedido='$id'";
$rspta = ejecutarConsulta($sql);
$i=0;
	
while ($reg = $rspta->fetch_object()){

$unidadmedida='NIU';

$datosp = "SELECT *FROM unidad_medida WHERE id='$reg->idpresentacion' ";
$datosp = ejecutarConsultaSimpleFila($datosp);

$datoser= "SELECT *FROM articulo_serie WHERE id='$reg->idlote' ";
$rowlote = ejecutarConsultaSimpleFila($datoser);

$idlote='0';
$serie='';
$lote='';
		
if($datosp){
	$unidadmedida=$datosp['tit'];	
}

if($rowlote){
	$idlote=$datosp['id'];
	$serie=$datosp['serie'];
	$lote=$datosp['lote'];
}

$rows[$i]['id']=$reg->id;
$rows[$i]['cell']=array($reg->id, $reg->codigoproducto, $reg->nombreproducto, $reg->unidadmedida, $unidadmedida, $reg->precio, $reg->cti, $reg->subtotal, $reg->igv, $reg->importe, $reg->idproducto, $serie, $lote, $idlote, $reg->origen);
$responce=$rows;
		
$i++;	
}
	
echo json_encode($responce);
		
	break;
		


case 'listarimport':
		//Recibimos el idingreso
		header('Content-Type: application/json');
		$id=$_GET['id'];
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$porigv=$fa['igv'];
$porigv=$porigv/100;
$porigv=$porigv+1.00;
		
		$sql="SELECT * FROM articulo  where marca='$id' ";
		$rspta =ejecutarConsulta($sql);
		
		$txtTOTAL=0;
		
$responce=[];		
$total=0;
		
		if(isset($_GET['page'])){ $page = $_GET['page'];  }else{ $page =1; }
if(isset($_GET['rows'])){ $limit = $_GET['rows'];  }else{ $limit = ''; }
if(isset($_GET['sidx'])){ $sidx = $_GET['sidx'];   }else{ $sidx= 'txtCOD_ARTICULO'; }
if(isset($_GET['sord'])){ $sord = $_GET['sord'];   }else{ $sord= 'asc'; }
		
		
$sql="SELECT COUNT(*) AS count FROM articulo  where marca='$id' ";
$row= ejecutarConsultaSimpleFila($sql);
$count = $row['count']; 
		
if($limit!=''){
if(!$sidx) $sidx =1; 

if( $count > 0 && $limit > 0) { 
    $total_pages = ceil($count/$limit); 
} else { 
    $total_pages = 0; 
} 
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit;
if($start <0) $start = 0;
	
$datos = "SELECT *FROM articulo  where marca='$id' ORDER BY $sidx $sord LIMIT $start , $limit ";
$datos = ejecutarConsulta($datos);
	
}else{
	
$datos = "SELECT *FROM articulo  where marca='$id' ORDER BY $sidx $sord ";
$datos = ejecutarConsulta($datos);
	
}	

$i=0;
	
while ($reg = $rspta->fetch_object()){
	
$subtotal=$reg->precio;
$igv='0.00';
$exonerado=$reg->precio;
	
if($reg->exonerado_igv=='0'){
$subtotal=round(($reg->precio/$porigv), 7);
$igv=$reg->precio-$subtotal;
$exonerado='0.00';
}
	
$rows[$i]['id']=$reg->txtCOD_ARTICULO;
$rows[$i]['cell']=array($reg->txtCOD_ARTICULO, $reg->codigo, $reg->exonerado_igv, $reg->txtDESCRIPCION_ARTICULO, $reg->medida, '', $reg->precio, '1', $subtotal, $igv, $reg->precio, '0.00', $exonerado, '0.00', '0', $reg->precio, '1', $reg->txtCOD_ARTICULO, '0', '0', '0', '0', '0');
$responce=$rows;
		
$i++;	
}
	
echo json_encode($responce);
break;		
		
case 'detallesalida':
		//Recibimos el idingreso
		header('Content-Type: application/json');
		$id=$_GET['id'];

$responce='';		
		
$sql="SELECT * FROM venta_opdetallet  where idpedido='$id'";
$rspta = ejecutarConsulta($sql);
$i=0;
	
while ($reg = $rspta->fetch_object()){

$datosp = "SELECT *FROM unidad_medida WHERE id='$reg->idpresentacion' ";
$datosp = ejecutarConsultaSimpleFila($datosp);
			
$rows[$i]['id']=$reg->id;
$rows[$i]['cell']=array($reg->id, $reg->codigoproducto, $reg->nombreproducto, $reg->unidadmedida, $datosp['tit'], $reg->precio, $reg->cti, $reg->subtotal, $reg->igv, $reg->importe, $reg->idproducto);
$responce=$rows;
		
$i++;	
}
	
echo json_encode($responce);
		
	break;
		
		

case 'listar':
		
if(isset($_GET['mes'])){ $mes=$_GET['mes']; }else{ $mes=''; }
if(isset($_GET['fecha_inicio'])){  $fecha_inicio=$_GET["fecha_inicio"]; }else{ $fecha_inicio=''; }
if(isset($_GET['fecha_fin'])){  $fecha_fin=$_GET["fecha_fin"]; }else{ $fecha_fin=''; }
if(isset($_GET['nivel'])){  $nivel=$_GET["nivel"]; }else{ $nivel=''; }
if(isset($_GET['estados'])){  $estados=$_GET["estados"]; }else{ $estados=''; }
if($estados=='' && isset($_GET["estado_filtro"])){ $estados=$_GET["estado_filtro"]; }
if(isset($_GET['tipos'])){  $tipos=$_GET["tipos"]; }else{ $tipos=''; }
if($tipos=='' && isset($_GET["tipo_doc_filtro"])){ $tipos=$_GET["tipo_doc_filtro"]; }
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$conf= ejecutarConsultaSimpleFila($sql3);
		
if(esModoBetaSunatVenta(isset($conf['tipo']) ? $conf['tipo'] : '3')){ $tipoc='BETA'; }else{ $tipoc='PRODUCCION'; }
		
$rspta=$venta->listar($fecha_inicio, $fecha_fin, '0', $nivel, $mes);
//Vamos a declarar un array
$data= Array();
$estadoFiltroReq = array();
$tipoDocFiltroReq = array();
if(trim((string)$tipos)!==''){
    $tmpT = explode(',', strtoupper((string)$tipos));
    foreach($tmpT as $t){
        $t = trim($t);
        if($t==='BOLETA'){ $tipoDocFiltroReq['03']=1; }
        if($t==='FACTURA'){ $tipoDocFiltroReq['01']=1; }
    }
}
if(trim((string)$estados)!==''){
    $tmp = explode(',', strtoupper((string)$estados));
    foreach($tmp as $e){
        $e = trim($e);
        if($e!==''){ $estadoFiltroReq[$e]=1; }
    }
}
while ($reg=$rspta->fetch_object()){

if(!empty($tipoDocFiltroReq) && !isset($tipoDocFiltroReq[(string)$reg->txtID_TIPO_DOCUMENTO])){ continue; }

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
			
$sql2="SELECT *FROM usuario WHERE idusuario='$reg->idusuario' ";
$mos= ejecutarConsultaSimpleFila($sql2);
	
$pago='0';
	
if($reg->tipo_pago=='CREDITO'){
$sqlpa="SELECT *FROM caja_ventapago WHERE idventa='$reg->idventa' ";
$pag= ejecutarConsultaSimpleFila($sqlpa);
if(!$pag){
$pago='1';
}
	
}
	
$pdf=$conf['ruc'].'-'.$reg->txtID_TIPO_DOCUMENTO.'-'.$reg->txtSERIE.'-'.$reg->txtNUMERO.'.pdf';
$estadob='';
	
if($reg->estado=='0'){ 
if($reg->txtID_TIPO_DOCUMENTO=='90'){
$estadob='<div class="label label-info">Guardado</div>';
}else{
$estadob='<div class="label label-default">Pendiente</div>'; 	
}

}


	
if($reg->estado=='1'){ $estadob='<div class="label label-secondary">Enviado</div>'; }	
if($reg->estado=='2'){ $estadob='<div class="label label-success">Aceptado</div>'; }
if($reg->estado=='3'){ $estadob='<div class="label label-danger">Rechazo</div>'; }
if($reg->estado=='4'){ $estadob='<div class="label label-default">Anul. pend.</div>'; }
if($reg->estado=='5'){ $estadob='<div class="label label-secondary">Anul. Env.</div>'; }
if($reg->estado=='6'){ $estadob='<div class="label label-warning" style="color:black;">An. Acep.</div>'; }
if($reg->estado=='7'){ $estadob='<div class="label label-danger">Rechazado</div>'; }
    if($reg->estado=='9'){ $estadob='<div class="label label-info">ENTREGADO</div>'; }

if(!empty($estadoFiltroReq)){
    $estadoNorm = '';
    if((string)$reg->estado==='0'){ $estadoNorm = 'PENDIENTE'; }
    else if((string)$reg->estado==='1'){ $estadoNorm = 'ENVIADO'; }
    else if((string)$reg->estado==='2'){ $estadoNorm = 'ACEPTADO'; }
    else if((string)$reg->estado==='3' || (string)$reg->estado==='7'){ $estadoNorm = 'RECHAZADO'; }
    if($estadoNorm==='' || !isset($estadoFiltroReq[$estadoNorm])){ continue; }
}
	
$tdocumento='OTROS';
	
if($reg->txtID_TIPO_DOCUMENTO=='03'){ $tdocumento='BOLETA'; }
if($reg->txtID_TIPO_DOCUMENTO=='01'){ $tdocumento='FACTURA'; }
if($reg->txtID_TIPO_DOCUMENTO=='90'){ $tdocumento='RECIBO'; }
if($reg->txtID_TIPO_DOCUMENTO=='07'){ $tdocumento='NOTA DE CRÉDITO'; }
if($reg->txtID_TIPO_DOCUMENTO=='08'){ $tdocumento='NOTA DE DÉBITO'; }

	
$botones='
      <div class="input-group-btn">
        <button class="btn btn-default  btn-xs dropdown-toggle" type="button" id="menuvencli" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i><span class="caret"></span> OPCIONES
        </button>
        <ul class="dropdown-menu" aria-labelledby="menuvencli">
';
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="window.tipopago('.$reg->idventa.', 1, \''.$reg->tipo_pago.'\')">
<span class="glyphicon glyphicon-shopping-cart"></span> Pagos</a></li>';

	
if($reg->txtID_TIPO_DOCUMENTO=='07'||$reg->txtID_TIPO_DOCUMENTO=='08'){
	
$pago='0';
	
$botones.='<li id="venta_newcli"><a target="_blank" href="plugins/dompdf/nota.php?id='.$reg->idventa.'" >
<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver PDF</a></li>';
	
}else{
	
$botones.='<li id="venta_newcli"><a target="_blank" href="plugins/dompdf/?id='.$reg->idventa.'" >
<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver PDF</a></li>';	
	
}
	
//$botones.='<li id="venta_newcli"><a target="_blank" href="plugins/dompdf/indexb.php?id='.$reg->idventa.'" >
//<i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF Botica</a></li>';


if($reg->idempresa=='1'||$reg->idempresa=='19'||$reg->idempresa=='0'||$reg->idempresa=='56'){
    $botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="impresionftec(\''.$reg->idventa.'\')" ><i class="fa fa-print"></i> Imprimir T</a></li>';
}else {
        $botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="impresionf(\''.$reg->idventa.'\')" ><i class="fa fa-print"></i> Imprimir</a></li>';

}


if($_COOKIE['facturacione']==1){
	
if($reg->estado=='0'){	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="mostrar(\''.$reg->idventa.'\')" ><i class="fa fa-pencil"></i> Modificar</a></li>';
}

if($reg->estado=='0'||$reg->estado=='1'){	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="reenviaFact(\''.$reg->idventa.'\', \''.$pago.'\')" ><i class="fa fa-repeat"></i> Reenviar</a></li>';
}

if($reg->estado=='4'||$reg->estado=='5'){
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="reenviabaja(\''.$reg->idventa.'\')" ><i class="fa fa-repeat"></i> Reenviar BAJA</a></li>';
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="leerticket(\''.$reg->idventa.'\')" ><i class="fa fa-repeat"></i> Revisar BAJA</a></li>';
}
	
if($reg->txtID_TIPO_DOCUMENTO=='90'){	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="bajarecibo(\''.$reg->idventa.'\')" ><i class="fa fa-remove"></i> Dar de baja</a></li>';
}	

$fechaant=date("Y-m-d", strtotime($reg->txtFECHA_DOCUMENTO));	
$date1 = new DateTime($fechaant);
$date2 = new DateTime($fechahoy);
$diff =$date1->diff($date2);
	

if($reg->estado=='2'){ 
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="baja(\''.$reg->idventa.'\')" ><i class="fa fa-remove"></i> Dar de baja</a></li>';
}
    if($reg->idempresa=='1'||$reg->idempresa=='19'||$reg->idempresa=='0'||$reg->idempresa=='56'){
        $botones.='<li id="venta_newcli"><a href="javascript:void(0)" onClick="cambiaestadolistado(9, '.$reg->idventa.')" ><i class="fa fa-pencil"></i> ETREGA DE ALMACEN</a></li>';
    }
}



//revisafactura(ruc, tipo,fecha, serie, numero, monto)
$fecha=date("d/m/Y", strtotime($reg->txtFECHA_DOCUMENTO));
//https://api.whatsapp.com/send?phone=+51910847682&text=visita%20mi%20web%20https://www.siscontonline.com/%20Gracia%20por%20elegirnos

    if($_COOKIE['id']==1){

        $botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="sendwhatsap(\''.$reg->idventa.'\')" ><i class="fa fa-whatsapp"></i> Enviar whatsapp</a></li>';
    }

if($mostrar){
    $botones.='<li id="venta_newcli"><a href="https://wa.me/+51'.$mostrar['telefono'].'?text=Hola:%20'.$mostrar['nombre'].',%20tu%20comprobante:%20'.$reg->txtSERIE.'-'.$reg->txtNUMERO.'%20ya%20esta%20disponible%20descarga%20tu%20comprobante%20'.RUTA.'/plugins/dompdf/?id='.$reg->idventa.'%20Gracias%20por%20confiar%20en%20'.$conf['razon_social'].'" target="_blank" ><i class="fa fa-whatsapp" aria-hidden="true"></i>
 Enviar WhatsappWeb</a></li>';
}

/*
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="versolofactura(\''.$reg->idventa.'\')" ><span class="glyphicon glyphicon-ok-sign"></span> Verificar GETSTATUS</a></li>';
*/
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="sendcorreo(\''.$reg->idventa.'\')" ><i class="fa fa-send"></i> Env. Correo</a></li>';
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="descargaxml(\''.$reg->idventa.'\')" ><span class="glyphicon glyphicon-cloud-download"></span> Descargar</a></li>';
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="revisafactura(\''.$conf['ruc'].'\', \''.$reg->txtID_TIPO_DOCUMENTO.'\', \''.$fecha.'\', \''.$reg->txtSERIE.'\', \''.$reg->txtNUMERO.'\', \''.$reg->txtTOTAL.'\', \''.$reg->idventa.'\', \''.$pago.'\')" ><span class="glyphicon glyphicon-ok-sign"></span> Revisar documento</a></li>';	

$botones.='</ul></div>';
	
$nombre='';
$clientedoc='';
if($mostrar){
$nombre=$mostrar['nombre'];
$clientedoc=$mostrar['tipo_documento'].': '.$mostrar['txtID_CLIENTE'];	
}
	
$monedaTxt = isset($reg->txtID_MONEDA) ? strtoupper(trim((string)$reg->txtID_MONEDA)) : '';
$monedaStyle = 'display:inline-block; min-width:46px; text-align:center; padding:2px 6px; border-radius:3px;';
if($monedaTxt==='USD'){
$monedaStyle .= 'background:#28a745; color:#fff;';
}else if($monedaTxt==='PEN'){
$monedaStyle .= 'background:#ffc107; color:#111;';
}else{
$monedaStyle .= 'background:#e9ecef; color:#111;';
}

$data[]=array(
"0"=>$reg->idventa,
"1"=>$botones,
"2"=>$estadob,
"3"=>$tdocumento,
"4"=>$reg->txtSERIE.'-'.$reg->txtNUMERO,
"5"=>$nombre,
"6"=>$clientedoc,
"7"=>'<div style="text-align:right;">'.number_format((float)$reg->txtSUB_TOTAL, 2, '.', ',').'</div>',
"8"=>'<div style="text-align:right;">'.number_format((float)$reg->txtIGV, 2, '.', ',').'</div>',
"9"=>'<div style="text-align:right;">'.number_format((float)$reg->txtTOTAL, 2, '.', ',').'</div>',
"10"=>'<div style="text-align:center;"><span style="'.$monedaStyle.'">'.$monedaTxt.'</span></div>',
"11"=>date("Y-m-d H:i:s", strtotime($reg->txtFECHA_DOCUMENTO)),
"12"=>$mos['nombre'],
"13"=>'<div style="text-align:right;">'.number_format((float)$reg->detraccion, 2, '.', ',').'</div>'
);
 		}
 		usort($data, function($a, $b){
    $fa = isset($a['11']) ? strtotime((string)$a['11']) : 0;
    $fb = isset($b['11']) ? strtotime((string)$b['11']) : 0;
    if($fa==$fb){ return 0; }
    return ($fa > $fb) ? -1 : 1;
});
$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

break;
		
		
case 'listarpedido':

	$tipodoc=$_GET['tdoc'];
	$fecha_inicio=$_GET['fecha_inicio'];
	$fecha_fin=$_GET['fecha_fin'];
		
		
if($_COOKIE['facturacione']==1){
$local="";	

}else{

$local=" AND idlocal='".$_COOKIE['idlocal']."' ";	
}
		
$sql="SELECT *FROM venta WHERE txtID_TIPO_DOCUMENTO='$tipodoc' AND idempresa='$_COOKIE[id]' AND pedido='1' AND DATE(txtFECHA_DOCUMENTO)>='$fecha_inicio' AND DATE(txtFECHA_DOCUMENTO)<='$fecha_fin' ORDER by txtFECHA_DOCUMENTO DESC ";
$rspta=ejecutarConsulta($sql);
		
		
 		//Vamos a declarar un array
 		$data= Array();

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$conf= ejecutarConsultaSimpleFila($sql3);
				
if(esModoBetaSunatVenta(isset($conf['tipo']) ? $conf['tipo'] : '3')){ $tipoc='BETA'; }else{ $tipoc='PRODUCCION'; }
		
while ($reg=$rspta->fetch_object()){

if(!empty($tipoDocFiltroReq) && !isset($tipoDocFiltroReq[(string)$reg->txtID_TIPO_DOCUMENTO])){ continue; }

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
			
$sql2="SELECT *FROM usuario WHERE idusuario='$reg->idusuario' ";
$mos= ejecutarConsultaSimpleFila($sql2);
			
if($reg->estado=='0'){ $estadob='<div class="label label-default">Pendiente</div>'; }
if($reg->estado=='1'){ $estadob='<div class="label label-success">PROCESADO</div>'; }
if($reg->estado=='6'){ $estadob='<div class="label label-secondary">ANULADO</div>'; }
	
	
$botones='
      <div class="input-group-btn">
        <button class="btn btn-default  btn-xs dropdown-toggle" type="button" id="menuvencli" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i><span class="caret"></span> OPCIONES
        </button>
        <ul class="dropdown-menu" aria-labelledby="menuvencli">
';
	
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="window.tipopago('.$reg->idventa.', 1)">
<span class="glyphicon glyphicon-shopping-cart"></span> Pagos</a></li>';


$botones.='<li id="venta_newcli"><a target="_blank" href="plugins/dompdf/?id='.$reg->idventa.'" >
<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver PDF</a></li>';	
	
	
//$botones.='<li id="venta_newcli"><a target="_blank" href="plugins/dompdf/indexb.php?id='.$reg->idventa.'" >
//<i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF Botica</a></li>';

	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)"  onclick="impresionf(\''.$reg->idventa.'\')" ><i class="fa fa-print"></i> Imprimir</a></li>';
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="sendcorreo(\''.$reg->idventa.'\')"><i class="fa fa-send"></i> Env. Correo</a></li>';

if($reg->estado=='0'&&$_COOKIE['facturacione']==1){

$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="bajarecibo('.$reg->idventa.')" ><i class="fa fa-remove"></i> Eliminar</a></li>';
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="mostrar(\''.$reg->idventa.'\')" ><span class="glyphicon glyphicon-pencil"></span> Editar pedido</a></li>';
if($reg->txtID_TIPO_DOCUMENTO=='91'){	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="pasapedido(\''.$reg->idventa.'\', 1)" ><span class="glyphicon glyphicon-ok"></span> Volver pedido</a></li>';
}
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="pasapedido(\''.$reg->idventa.'\', 2)" ><span class="glyphicon glyphicon-ok"></span> Aceptar venta</a></li>';
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="mostrarsaldo(\''.$reg->idventa.'\')" ><span class="glyphicon glyphicon-th-list"></span> Mostrar saldo</a></li>';
	
}

$botones.='</ul></div>';
	

	
	
if($reg->txtID_TIPO_DOCUMENTO=='91'){ $tdocumento='COTIZACIÓN'; }
if($reg->txtID_TIPO_DOCUMENTO=='92'){ $tdocumento='NOTA DE PEDIDO'; }
	
$data[]=array(
"0"=>$botones,
"1"=>$reg->idventa,
"2"=>date("Y-m-d", strtotime($reg->txtFECHA_DOCUMENTO)),
"3"=>$tdocumento,	
"4"=>$reg->txtSERIE.'-'.$reg->txtNUMERO,
"5"=>$mostrar['nombre'],
"6"=>$mostrar['tipo_documento'].': '.$mostrar['txtID_CLIENTE'],
"7"=>$mos['nombre'],
"8"=>$reg->txtSUB_TOTAL,
"9"=>$reg->txtIGV,
"10"=>$reg->txtTOTAL,
"11"=>$estadob
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
case 'listarrapido':
		
$hoy=date('Y-m-d');
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

$tipo=$_GET['tipo'];
		
$idlocal=''; $idlocal2='';	
if($fa['articulo']=='LOCAL'){ $idlocal=' AND idlocal="'.$_COOKIE['idlocal'].'" '; $idlocal2=' AND a.idlocal="'.$_COOKIE['idlocal'].'" ';  }

$idtipo=''; $idtipo2='';
if($tipo!='0'){ $idtipo=' AND idcategoria="'.$tipo.'" '; $idtipo2=' AND a.idcategoria="'.$tipo.'" '; }
		
$data= Array();

$draw = isset($_REQUEST['draw']) ? (int)$_REQUEST['draw'] : 0;
$row = isset($_REQUEST['start']) ? (int)$_REQUEST['start'] : 0;
$rowperpage = isset($_REQUEST['length']) ? (int)$_REQUEST['length'] : 20; // Rows display per page
$columnIndex = isset($_REQUEST['order'][0]['column']) ? (int)$_REQUEST['order'][0]['column'] : 0; // Column index
$columnName = isset($_REQUEST['columns'][$columnIndex]['data']) ? $_REQUEST['columns'][$columnIndex]['data'] : ''; // Column name
$columnSortOrder = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : 'asc'; // asc or desc
$searchValue = isset($_REQUEST['search']['value']) ? trim((string)$_REQUEST['search']['value']) : ''; // Search value
$searchValueSql = limpiarCadena($searchValue);
$hasMarcaArticulo = _table_has_column('articulo','marca');
$selectMarcaArticulo = $hasMarcaArticulo ? ", a.marca" : "";
$hasLineaArticulo = _table_has_column('articulo','linea');
$selectLineaArticulo = $hasLineaArticulo ? ", a.linea" : "";
$catHasNivel = _table_has_column('categoria','nivel');
	
## Search 
$searchQuery = " ";
//INICIO DE BUSQUEDA 1 ABCD
    if (false && $_COOKIE['id'] == '27') {


//inicio buscador con palabras enteras de fin a inicio o viseversa

        $searchQuery = " ";
        if ($searchValue != '') {
            $aKeywordf = $_POST['search']['value'];
            $query = "SELECT count(*) as allcount FROM articulo WHERE MATCH (codigo, txtDESCRIPCION_ARTICULO) AGAINST('$aKeywordf'  IN NATURAL LANGUAGE MODE) AND idempresa='$_COOKIE[id]' $idlocal ";
            $query2 = "SELECT * FROM articulo WHERE MATCH (codigo, txtDESCRIPCION_ARTICULO) AGAINST('$aKeywordf'  IN NATURAL LANGUAGE MODE)  AND idempresa='$_COOKIE[id]' $idlocal2 limit " . $row . "," . $rowperpage;
            $records = ejecutarConsultaSimpleFila("SELECT count(*) as allcount FROM articulo WHERE idempresa='" . $_COOKIE['id'] . "' " . $idlocal);
            $totalRecords = $records['allcount'];
## Total number of record with filtering
            $records = ejecutarConsultaSimpleFila($query);
            $totalRecordwithFilter = $records['allcount'];

## Fetch records
            $empRecords = ejecutarConsulta($query2);
        } else {
            $records = ejecutarConsultaSimpleFila("select count(*) as allcount from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.idempresa='$_COOKIE[id]' $idlocal2 " . $searchQuery);
            $totalRecords = $records['allcount'];
## Total number of record with filtering
            $records = ejecutarConsultaSimpleFila("select count(*) as allcount from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.idempresa='$_COOKIE[id]' $idlocal2   " . $searchQuery);
            $totalRecordwithFilter = $records['allcount'];
## Fetch records
            $empQuery = "select a.txtCOD_ARTICULO, a.txtDESCRIPCION_ARTICULO, a.preciooferta, a.canjepuntos, a.medida, a.codigo, a.condicion, a.resaltado, a.exonerado_igv, a.mayor, a.precio, a.precio_mayor, a.precio_compra, a.comisionm, a.comisionmp, a.comision, a.existencia, a.moneda, a.idproveedor, a.canje, a.idcategoria, a.marca from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.idempresa='$_COOKIE[id]' $idlocal2  " . $searchQuery . "  ORDER BY a.txtCOD_ARTICULO DESC limit " . $row . "," . $rowperpage;
            $empRecords = ejecutarConsulta($empQuery);

        }
//fin de buscador
    } else {


        if ($searchValue != '') {
            $searchQuery .= " and ( txtCOD_ARTICULO LIKE '%" . $_POST['search']['value'] . "%' ";
            $searchQuery .= " OR txtDESCRIPCION_ARTICULO LIKE '%" . $_POST['search']['value'] . "%' ";
            $searchQuery .= " OR codigo LIKE '%" . $_POST['search']['value'] . "%' ) ";
            $searchQuery .=" OR txtCOD_ARTICULO IN (SELECT cod_articulo FROM articulo_serie WHERE serie LIKE '%".$_POST['search']['value']."%' OR lote LIKE '%".$_POST['search']['value']."%' ) ";
            //txtCOD_ARTICULO IN (SELECT cod_articulo FROM articulo_serie WHERE serie LIKE '%F0011%')
        }
        $records = ejecutarConsultaSimpleFila("select count(*) as allcount from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria  WHERE a.idempresa='$_COOKIE[id]' $idlocal2  AND a.condicion='1' " . $searchQuery);
        $totalRecords = $records['allcount'];

## Total number of record with filtering
        $records = ejecutarConsultaSimpleFila("select count(*) as allcount from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria  WHERE a.idempresa='$_COOKIE[id]' $idlocal2  AND a.condicion='1'  " . $searchQuery);
        $totalRecordwithFilter = $records['allcount'];

## Fetch records
        $empQuery = "select a.txtCOD_ARTICULO, a.txtDESCRIPCION_ARTICULO, a.preciooferta, a.canjepuntos, a.medida, a.codigo, a.condicion, a.resaltado, a.exonerado_igv, a.mayor, a.precio, a.precio_mayor, a.precio_compra, a.comisionm, a.comisionmp, a.comision, a.existencia, a.moneda, a.idproveedor, a.canje, a.idcategoria from articulo  a INNER JOIN categoria c ON a.idcategoria=c.idcategoria  WHERE a.idempresa='$_COOKIE[id]' $idlocal2  AND a.condicion='1'  " . $searchQuery . " ORDER BY a.txtCOD_ARTICULO ASC  limit " . $row . "," . $rowperpage;
        $empRecords = ejecutarConsulta($empQuery);
        //fin de buscador corelativo


    }

if(!$empRecords){
  $fallbackWhere = " WHERE a.idempresa='$_COOKIE[id]' ";
  if($fa['articulo']=='LOCAL' && _table_has_column('articulo','idlocal')){
    $fallbackWhere .= " AND a.idlocal='".$_COOKIE['idlocal']."' ";
  }
  $fallbackWhere .= " AND ".($artHasCondicion ? "a.condicion='1'" : "1=1");
  $empFallback = "SELECT ".$selectArtCols." FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria ".$fallbackWhere." ORDER BY a.txtCOD_ARTICULO ASC limit ".$row.",".$rowperpage;
  $empRecords = ejecutarConsulta($empFallback);
  $records = ejecutarConsultaSimpleFila("SELECT count(*) as allcount FROM articulo a ".$fallbackWhere);
  $totalRecords = isset($records['allcount']) ? (int)$records['allcount'] : 0;
  $totalRecordwithFilter = $totalRecords;
}

while ($empRecords && ($reg=$empRecords->fetch_object())){

$sqlf="SELECT *FROM articulo_serie WHERE cod_articulo='$reg->codigo' AND estado='1' ORDER BY id ASC ";
$fec= ejecutarConsultaSimpleFila($sqlf);
$estfecha='0';
$dias='0';	

$sqlcat="SELECT *FROM categoria WHERE idcategoria='$reg->idcategoria' ";
$cat= ejecutarConsultaSimpleFila($sqlcat);

$oculto='';

if($fec){
	
$diff =restardias($hoy, $fec['fecha_vto']);
$dias=$diff;
if($diff<'124'){ $estfecha='2'; }else if($diff<='186'&&$diff>'124'){ $estfecha='1'; }
	
}	
	
$texto = str_replace($no_permitidas, $permitidas, $reg->txtDESCRIPCION_ARTICULO, $reg->stock);

$sql="SELECT *FROM detalle_ingreso WHERE txtCOD_ARTICULO='$reg->txtCOD_ARTICULO' ORDER BY iddetalle_ingreso DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$precio='1.00';	
if($mostrar){ $precio=$mostrar['precio_venta']; }
	                       
/*
$sql="SELECT * FROM unidad_medida WHERE id='$reg->medida'";
$mostrar=ejecutarConsultaSimpleFila($sql);
*/
$sqls="SELECT * FROM articulo_stock WHERE idlocal='$_COOKIE[idlocal]' AND idarticulo='$reg->txtCOD_ARTICULO' ";
$stock=ejecutarConsultaSimpleFila($sqls);


if($_COOKIE['id']=='19'){
	
if($reg->existencia=='91'){
$stockf='3000.00';
}else{
$stockf=round($stock['stock'], 2);	
}
	
}else if($reg->medida=='ZZ'){
$stockf='3000.00';
}else{
$stockf='0.00';	
if($stock){ $stockf=round($stock['stock'], 2); }
}
/*
$oculto='<span style="display:none;"> '.$reg->nombre.' </span>';
*/
$exonerado='<input type="hidden" id="ex'.$reg->txtCOD_ARTICULO.'"  name="ex'.$reg->txtCOD_ARTICULO.'" value="'.$reg->exonerado_igv.'" >';
	
$mayor='<input type="hidden" id="may'.$reg->txtCOD_ARTICULO.'"  name="may'.$reg->txtCOD_ARTICULO.'" value="'.$reg->mayor.'" > <input id="pm'.$reg->txtCOD_ARTICULO.'" class="form-control"  style="padding: 1px; margin: 1px;  height: 22px; " name="pm'.$reg->txtCOD_ARTICULO.'" value="'.$reg->precio_mayor.'" type="hidden">';
	
$mayor.=' <input type="hidden" id="tit'.$reg->txtCOD_ARTICULO.'"  name="tit'.$reg->txtCOD_ARTICULO.'" value="'.$texto.'" > ';
$mayor.=' <input type="hidden" id="stock'.$reg->txtCOD_ARTICULO.'"  name="stock'.$reg->txtCOD_ARTICULO.'" value="'.$stockf.'" > ';
$mayor.=' <input type="hidden" id="codigo'.$reg->txtCOD_ARTICULO.'"  name="codigo'.$reg->txtCOD_ARTICULO.'" value="'.$reg->codigo.'" > ';
	
$imei='<select id="un'.$reg->txtCOD_ARTICULO.'" name="un'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 140px; padding: 1px; margin: 1px;  height: 22px; " />';
	
$imei.='<option value="'.$reg->medida.'|0|0|0|'.$reg->precio_mayor.'|'.$reg->mayor.'|'.$reg->comision.'|'.$reg->comisionm.'|'.$reg->comisionmp.'|'.$reg->txtCOD_ARTICULO.'|0" selected >'.$reg->precio.' | UNIDAD</option>';
	
$datoss = "SELECT * FROM articulo_unidad WHERE idproducto='$reg->txtCOD_ARTICULO' ";
$datos = ejecutarConsulta($datoss);
while($dato=mysqli_fetch_array($datos)) {
	
$imei.='<option value="'.$dato['medida'].'|'.$dato['cti'].'|'.$dato['precio'].'|1|'.$dato['preciom'].'|'.$dato['ctimayor'].'|'.$dato['comision'].'|'.$dato['ctimayor'].'|'.$dato['comisionm'].'|'.$reg->txtCOD_ARTICULO.$dato['id'].'|'.$dato['id'].'">'.$dato['precio'].' | '.$dato['nombre'].'</option>';
	
}
$imei.='</select>';
	
$stock='<span class="badge label-success" data-toggle="tooltip" data-html="true" title="BUEN STOCK" >'.$stockf.'</span>';
	
if($stockf=='0'){ 
$stock='<span class="badge label-danger" data-toggle="tooltip" data-html="true" title="SIN STOCK" >'.$stockf.'</span>';
}
	
$marcatot='GENERICA';
if($cat){ $marcatot=$cat['nombre']; }

    if($_COOKIE['administrador']==1){
        $preciocomprna=$reg->precio_compra;
    }else{
             $preciocomprna="0.00";
    }
	
$data[]=array(
"0"=>$imei.' '.$exonerado.' '.$mayor,	
"1"=>$marcatot,
"2"=>$reg->codigo,
"3"=>$texto.' '.$oculto,
"4"=>$reg->precio,
"5"=>$preciocomprna,
"6"=>'<input  type="number" class="form-control keyb" style="width: 40px; padding: 1px; margin: 1px;  height: 22px; " value="1" name="cti'.$reg->txtCOD_ARTICULO.'" id="cti'.$reg->txtCOD_ARTICULO.'" />',
"7"=>$stock,
"8"=>'<button class="btn btn-info btn-xs" onclick="verimagen(\''.$reg->txtCOD_ARTICULO.'\')" ><span class="fa fa-image"></span></button>',
"9"=>'<button class="btn btn-danger btn-xs" onclick="agregartabla(\''.$reg->txtCOD_ARTICULO.'\', \''.$reg->precio.'\', \''.$texto.'\', \''.$reg->codigo.'\', \''.$stockf.'\', \'0\', \''.$reg->exonerado_igv.'\', \'\', \'1\')"><span class="fa fa-shopping-cart"></span></button>',

);
 		}
$json_data = array(
        "draw"            =>intval($draw),
        "recordsTotal"    =>$totalRecords,
        "recordsFiltered" =>$totalRecordwithFilter,
        "data"            => $data   // total data array
        );
		
		
 		echo json_encode($json_data);
 		
break;
		
		
case 'listarnormal':

$hoy=date('Y-m-d');

$data= Array();
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

$idlocal=''; $idlocal2='';	
if($fa['articulo']=='LOCAL'){ $idlocal=' AND idlocal="'.$_COOKIE['idlocal'].'" '; $idlocal2=' AND a.idlocal="'.$_COOKIE['idlocal'].'" ';  }

$draw = isset($_REQUEST['draw']) ? (int)$_REQUEST['draw'] : 0;
$row = isset($_REQUEST['start']) ? (int)$_REQUEST['start'] : 0;
$rowperpage = isset($_REQUEST['length']) ? (int)$_REQUEST['length'] : 20; // Rows display per page
$columnIndex = isset($_REQUEST['order'][0]['column']) ? (int)$_REQUEST['order'][0]['column'] : 0; // Column index
$columnName = isset($_REQUEST['columns'][$columnIndex]['data']) ? $_REQUEST['columns'][$columnIndex]['data'] : ''; // Column name
$columnSortOrder = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : 'asc'; // asc or desc
$searchValue = isset($_REQUEST['search']['value']) ? trim((string)$_REQUEST['search']['value']) : ''; // Search value
$searchValueSql = limpiarCadena($searchValue);
$hasMarcaArticulo = _table_has_column('articulo','marca');
$selectMarcaArticulo = $hasMarcaArticulo ? ", a.marca" : "";
$hasLineaArticulo = _table_has_column('articulo','linea');
$selectLineaArticulo = $hasLineaArticulo ? ", a.linea" : "";
$umHasCodigo = _table_has_column('unidad_medida','codigo');
$constock = isset($_REQUEST['constock']) ? trim((string)$_REQUEST['constock']) : 'TODO';
$stockFilter = '';
if($constock==='STOCK'){
  $stockFilter = " AND EXISTS(SELECT 1 FROM articulo_stock s WHERE s.idarticulo=a.txtCOD_ARTICULO ".(($fa['articulo']=='LOCAL')?" AND s.idlocal='".$_COOKIE['idlocal']."'":"")." AND COALESCE(s.stock,0)>0) ";
}
$filtroGrupo = isset($_REQUEST['filtro_grupo_art']) ? trim((string)$_REQUEST['filtro_grupo_art']) : '';
$filtroMarca = isset($_REQUEST['filtro_marca_art']) ? trim((string)$_REQUEST['filtro_marca_art']) : '';
$filtroLinea = isset($_REQUEST['filtro_linea_art']) ? trim((string)$_REQUEST['filtro_linea_art']) : '';
$extraFilter = '';
if($filtroGrupo!==''){ $extraFilter .= " AND c.nombre='".limpiarCadena($filtroGrupo)."' "; }
if($filtroMarca!=='' && $hasMarcaArticulo){
  $marcaFilterVal = limpiarCadena($filtroMarca);
  $extraFilter .= " AND (a.marca='".$marcaFilterVal."' ";
  if($hasMarcaTbl){
    $extraFilter .= " OR (a.marca REGEXP '^[0-9]+$' AND (SELECT nombre FROM marca m WHERE m.idmarca=a.marca LIMIT 1)='".$marcaFilterVal."') ";
  }
  $extraFilter .= " OR EXISTS(SELECT 1 FROM categoria cm WHERE cm.nombre='".$marcaFilterVal."' ".($catHasNivel?" AND cm.nivel='1' ":"")." AND (cm.idcategoria=a.marca OR cm.nombre=a.marca)) ";
  $extraFilter .= ") ";
}
if($filtroLinea!=='' && $hasLineaArticulo){
  $lineaFilterVal = limpiarCadena($filtroLinea);
  $extraFilter .= " AND (a.linea='".$lineaFilterVal."' ";
  $extraFilter .= " OR EXISTS(SELECT 1 FROM categoria cl WHERE cl.nombre='".$lineaFilterVal."' ".($catHasNivel?" AND cl.nivel='3' ":"")." AND (cl.idcategoria=a.linea OR cl.nombre=a.linea)) ";
  $extraFilter .= ") ";
}
$loadPresentacionesDet = isset($_REQUEST['load_presentaciones']) && (string)$_REQUEST['load_presentaciones']==='1';
$umByCodigo = array();
$umById = array();
if($hasUnidadMedidaTbl){
  $umCols = array();
  if($umHasCodigo){ $umCols[] = "codigo"; }
  $umCols[] = "id";
  if($umHasTit){ $umCols[] = "tit"; }
  if($umHasNombre){ $umCols[] = "nombre"; }
  $umSql = "SELECT ".implode(",", $umCols)." FROM unidad_medida";
  $umRs = ejecutarConsulta($umSql);
  if($umRs){
    while($umRow=mysqli_fetch_assoc($umRs)){
      $lbl = '';
      if($umHasTit && isset($umRow['tit']) && trim((string)$umRow['tit'])!==''){ $lbl=trim((string)$umRow['tit']); }
      elseif($umHasNombre && isset($umRow['nombre']) && trim((string)$umRow['nombre'])!==''){ $lbl=trim((string)$umRow['nombre']); }
      if($lbl===''){ continue; }
      if($umHasCodigo && isset($umRow['codigo']) && trim((string)$umRow['codigo'])!==''){ $umByCodigo[trim((string)$umRow['codigo'])]=$lbl; }
      if(isset($umRow['id']) && trim((string)$umRow['id'])!==''){ $umById[trim((string)$umRow['id'])]=$lbl; }
    }
  }
}
	
## Search 
$searchQuery = " ";
		
if($searchValue != ''){
   	$searchQuery .= " and ( codigo LIKE '%".$_POST['search']['value']."%' ";
   	$searchQuery .=" OR txtDESCRIPCION_ARTICULO LIKE '%".$_POST['search']['value']."%' ";
   	$searchQuery .=" OR codigo LIKE '%".$_POST['search']['value']."%' ) ";
}
		
$records= ejecutarConsultaSimpleFila("select count(*) as allcount from articulo WHERE condicion='1' $idlocal AND idempresa='$_COOKIE[id]' ");
$totalRecords = $records['allcount'];

## Total number of record with filtering
$records= ejecutarConsultaSimpleFila("select count(*) as allcount from articulo WHERE condicion='1' $idlocal AND idempresa='$_COOKIE[id]' ".$searchQuery);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select a.txtCOD_ARTICULO, a.txtDESCRIPCION_ARTICULO, a.medida, a.codigo, a.condicion, a.resaltado, c.nombre, a.exonerado_igv, a.mayor, a.precio, a.precio_mayor, a.precio_compra, a.comisionm, a.comisionmp, a.comision, a.existencia from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.condicion='1' $idlocal2 AND a.idempresa='$_COOKIE[id]' ".$searchQuery." limit ".$row.",".$rowperpage;
$empRecords = ejecutarConsulta($empQuery);

if(!$empRecords){
	$fallbackWhere = " WHERE a.idempresa='$_COOKIE[id]' ";
	if($fa['articulo']=='LOCAL' && _table_has_column('articulo','idlocal')){
		$fallbackWhere .= " AND a.idlocal='".$_COOKIE['idlocal']."' ";
	}
	$fallbackWhere .= " AND ".($artHasCondicion ? "a.condicion='1'" : "1=1");
	$empFallback = "SELECT ".$selectArtCols." FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria ".$fallbackWhere." ORDER BY a.txtCOD_ARTICULO ASC limit ".$row.",".$rowperpage;
	$empRecords = ejecutarConsulta($empFallback);
	$records = ejecutarConsultaSimpleFila("SELECT count(*) as allcount FROM articulo a ".$fallbackWhere);
	$totalRecords = isset($records['allcount']) ? (int)$records['allcount'] : 0;
	$totalRecordwithFilter = $totalRecords;
}

while ($empRecords && ($reg=$empRecords->fetch_object())){
	
$sqlf="SELECT *FROM articulo_serie WHERE cod_articulo='$reg->txtCOD_ARTICULO' AND estado='1' ORDER BY id ASC ";
$fec= ejecutarConsultaSimpleFila($sqlf);
$estfecha='0';
$dias='0';	
if($fec){

$diff =restardias($hoy, $fec['fecha_vto']);
$dias=$diff;
if($diff<'124'){ $estfecha='2'; }else if($diff<='186'&&$diff>'124'){ $estfecha='1'; }
$fechaven=$fec['fecha_vto'];
$lote=$fec['lote'];
}else{
$fechaven='0000-00-00';	
$lote='NO';	
}
	
$texto = str_replace($no_permitidas, $permitidas, $reg->txtDESCRIPCION_ARTICULO, $reg->stock);

/*
$sql="SELECT * FROM unidad_medida WHERE id='$reg->medida'";
$mostrar=ejecutarConsultaSimpleFila($sql);
*/
	
$sqls="SELECT * FROM articulo_stock WHERE idlocal='$_COOKIE[idlocal]' AND idarticulo='$reg->txtCOD_ARTICULO' ";
$stock=ejecutarConsultaSimpleFila($sqls);

	
	
if($_COOKIE['id']=='19'){
	
if($reg->existencia=='91'){
$stockf='3000.00';
}else{
$stockf=round($stock['stock'], 2);	
}
	
}else if($reg->medida=='ZZ'){
$stockf='3000.00';
}else{
$stockf=round($stock['stock'], 2);	
}
	
	
	
$oculto='<span style="display:none;"> '.$reg->nombre.' '.$reg->txtDESCRIPCION_ARTICULO.' </span>';	
$exonerado='<input type="hidden" id="ex'.$reg->txtCOD_ARTICULO.'"  name="ex'.$reg->txtCOD_ARTICULO.'" value="'.$reg->exonerado_igv.'" >';
	
$mayor='<input type="hidden" id="may'.$reg->txtCOD_ARTICULO.'"  name="may'.$reg->txtCOD_ARTICULO.'" value="'.$reg->mayor.'" > <input id="pm'.$reg->txtCOD_ARTICULO.'" class="form-control"  style="padding: 1px; margin: 1px;  height: 22px; " name="pm'.$reg->txtCOD_ARTICULO.'" value="'.$reg->precio_mayor.'" type="hidden">';
	
$titulo=' <input type="text"  class="form-control listado" id="tit'.$reg->txtCOD_ARTICULO.'"  name="tit'.$reg->txtCOD_ARTICULO.'" value="'.$texto.'" > ';
$mayor.=' <input type="hidden" id="stock'.$reg->txtCOD_ARTICULO.'"  name="stock'.$reg->txtCOD_ARTICULO.'" value="'.$stockf.'" > ';
$mayor.=' <input type="hidden" id="codigo'.$reg->txtCOD_ARTICULO.'"  name="codigo'.$reg->txtCOD_ARTICULO.'" value="'.$reg->codigo.'" > ';

	
$imei='<select id="un'.$reg->txtCOD_ARTICULO.'" name="un'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 120px; padding: 1px; margin: 1px;  height: 22px; " />';
	
$imei.='<option value="'.$reg->medida.'|0|0|0|'.$reg->precio_mayor.'|'.$reg->mayor.'|'.$reg->comision.'|'.$reg->comisionm.'|'.$reg->comisionmp.'|'.$reg->txtCOD_ARTICULO.'|0">'.$reg->precio.' | UNIDAD</option>';
	
$datos = "SELECT *FROM articulo_unidad  WHERE idproducto='$reg->txtCOD_ARTICULO' ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$imei.='<option value="'.$dato['medida'].'|'.$dato['cti'].'|'.$dato['precio'].'|1|'.$dato['preciom'].'|'.$dato['ctimayor'].'|'.$dato['comision'].'|'.$dato['ctimayor'].'|'.$dato['comisionm'].'|'.$reg->txtCOD_ARTICULO.$dato['id'].'|'.$dato['id'].'">'.$dato['precio'].' | '.$dato['nombre'].'</option>';
}
$imei.='</select>';
	
$stock='<span class="badge label-success" data-toggle="tooltip" data-html="true" title="BUEN STOCK" >'.$stockf.'</span>';
	
if($stockf=='0'){ 
$stock='<span class="badge label-danger" data-toggle="tooltip" data-html="true" title="SIN STOCK" >'.$stockf.'</span>';
}
	
$data[]=array(
"0"=>$imei.' '.$exonerado.' '.$mayor,	
"1"=>$reg->txtCOD_ARTICULO,
"2"=>$reg->codigo,
"3"=>$titulo.' '.$oculto,
"4"=>$lote,
"5"=>$fechaven,
"6"=>$reg->precio,
"7"=>'<input  type="number" class="form-control keyb" style="width: 40px; padding: 1px; margin: 1px;  height: 22px; " value="1" name="cti'.$reg->txtCOD_ARTICULO.'" id="cti'.$reg->txtCOD_ARTICULO.'" />',
"8"=>$stock,
"9"=>'<button class="btn btn-danger btn-xs" onclick="agregartabla(\''.$reg->txtCOD_ARTICULO.'\', \''.$reg->precio.'\', \''.$texto.'\', \''.$reg->codigo.'\', \''.$stockf.'\', \'0\', \''.$reg->exonerado_igv
	.'\')"><span class="fa fa-shopping-cart"></span></button>',
"10"=>$estfecha
);
 		}
$json_data = array(
        "draw"            =>intval($draw),
        "recordsTotal"    =>$totalRecords,
        "recordsFiltered" =>$totalRecordwithFilter,
        "data"            => $data   // total data array
        );
		
		
 		echo json_encode($json_data);
		
break;
		
		
	
case 'listarArticulos':

$__listarArticulosErrLevel = error_reporting();
error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors','0');
if(ob_get_level()===0){ ob_start(); }

$hoy=date('Y-m-d');
try{

$idcliente=(isset($_GET['idcliente'])) ? $_GET['idcliente'] : "0";

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
if(!is_array($fa)){ $fa=array(); }

$sql3="SELECT *FROM persona WHERE idpersona='$idcliente' ";
$cli= ejecutarConsultaSimpleFila($sql3);
if(!is_array($cli)){ $cli=array(); }

$sqllocal="SELECT *FROM sucursal WHERE id='$_COOKIE[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqllocal);
if(!is_array($local)){ $local=array(); }

$exoneradoigv='0';

if($local && isset($local['exonerado']) && $local['exonerado']>0){ $exoneradoigv=$local['exonerado']; }

$idlocal=''; $idlocal2='';	
if(isset($fa['articulo']) && $fa['articulo']=='LOCAL'){ $idlocal=' AND idlocal="'.$_COOKIE['idlocal'].'" '; $idlocal2=' AND a.idlocal="'.$_COOKIE['idlocal'].'" ';  }
		
$data= Array();

$draw = isset($_REQUEST['draw']) ? (int)$_REQUEST['draw'] : 0;
$row = isset($_REQUEST['start']) ? (int)$_REQUEST['start'] : 0;
$rowperpage = isset($_REQUEST['length']) ? (int)$_REQUEST['length'] : 20; // Rows display per page
$columnIndex = isset($_REQUEST['order'][0]['column']) ? (int)$_REQUEST['order'][0]['column'] : 0; // Column index
$columnName = isset($_REQUEST['columns'][$columnIndex]['data']) ? $_REQUEST['columns'][$columnIndex]['data'] : ''; // Column name
$columnSortOrder = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : 'asc'; // asc or desc
$searchValue = isset($_REQUEST['search']['value']) ? trim((string)$_REQUEST['search']['value']) : ''; // Search value
$searchValueSql = limpiarCadena($searchValue);
$hasMarcaArticulo = _table_has_column('articulo','marca');
$selectMarcaArticulo = $hasMarcaArticulo ? ", a.marca" : "";
$hasLineaArticulo = _table_has_column('articulo','linea');
$selectLineaArticulo = $hasLineaArticulo ? ", a.linea" : "";
$catHasNivel = _table_has_column('categoria','nivel');
$hasCodAfectArticulo = _table_has_column('articulo','cod_afectacion_igv');
$selectCodAfectArticulo = $hasCodAfectArticulo ? ", a.cod_afectacion_igv" : "";
$artHasPrecioOferta = _table_has_column('articulo','preciooferta');
$artHasCanjePuntos  = _table_has_column('articulo','canjepuntos');
$artHasResaltado    = _table_has_column('articulo','resaltado');
$artHasMoneda       = _table_has_column('articulo','moneda');
$artHasIdProveedor  = _table_has_column('articulo','idproveedor');
$artHasCanje        = _table_has_column('articulo','canje');
$artHasMedida       = _table_has_column('articulo','medida');
$artHasCodigo       = _table_has_column('articulo','codigo');
$artHasCondicion    = _table_has_column('articulo','condicion');
$artHasExoneradoIgv = _table_has_column('articulo','exonerado_igv');
$artHasMayor        = _table_has_column('articulo','mayor');
$artHasPrecio       = _table_has_column('articulo','precio');
$artHasPrecioMayor  = _table_has_column('articulo','precio_mayor');
$artHasPrecioCompra = _table_has_column('articulo','precio_compra');
$artHasComisionM    = _table_has_column('articulo','comisionm');
$artHasComisionMP   = _table_has_column('articulo','comisionmp');
$artHasComision     = _table_has_column('articulo','comision');
$artHasExistencia   = _table_has_column('articulo','existencia');
$artHasIdCategoria  = _table_has_column('articulo','idcategoria');
$hasUnidadMedidaTbl = _table_exists('unidad_medida');
$umHasTit           = $hasUnidadMedidaTbl && _table_has_column('unidad_medida','tit');
$umHasNombre        = $hasUnidadMedidaTbl && _table_has_column('unidad_medida','nombre');
$hasArticuloUnidadTbl = _table_exists('articulo_unidad');
$hasArticuloPrecioClienteTbl = _table_exists('articulo_precio_cliente');
$hasArticuloSerieTbl = _table_exists('articulo_serie');
$hasArticuloStockTbl = _table_exists('articulo_stock');
$hasPersonaTbl = _table_exists('persona');
$hasMarcaTbl = _table_exists('marca');
$auHasIdProducto    = $hasArticuloUnidadTbl && _table_has_column('articulo_unidad','idproducto');
$auHasId            = $hasArticuloUnidadTbl && _table_has_column('articulo_unidad','id');
$auHasMedida        = $hasArticuloUnidadTbl && _table_has_column('articulo_unidad','medida');
$auHasCti           = $hasArticuloUnidadTbl && _table_has_column('articulo_unidad','cti');
$auHasPrecio        = $hasArticuloUnidadTbl && _table_has_column('articulo_unidad','precio');
$auHasPrecioM       = $hasArticuloUnidadTbl && _table_has_column('articulo_unidad','preciom');
$auHasCtiMayor      = $hasArticuloUnidadTbl && _table_has_column('articulo_unidad','ctimayor');
$auHasComision      = $hasArticuloUnidadTbl && _table_has_column('articulo_unidad','comision');
$auHasComisionM     = $hasArticuloUnidadTbl && _table_has_column('articulo_unidad','comisionm');
$auHasNombre        = $hasArticuloUnidadTbl && _table_has_column('articulo_unidad','nombre');
$auSelectCols = ($auHasId ? "id" : "0 AS id")
  .", ".($auHasMedida ? "medida" : "'' AS medida")
  .", ".($auHasCti ? "cti" : "0 AS cti")
  .", ".($auHasPrecio ? "precio" : "0 AS precio")
  .", ".($auHasPrecioM ? "preciom" : "0 AS preciom")
  .", ".($auHasCtiMayor ? "ctimayor" : "0 AS ctimayor")
  .", ".($auHasComision ? "comision" : "0 AS comision")
  .", ".($auHasComisionM ? "comisionm" : "0 AS comisionm")
  .", ".($auHasNombre ? "nombre" : "'PRESENTACION' AS nombre");
$selectArtCols = "a.txtCOD_ARTICULO, a.txtDESCRIPCION_ARTICULO"
  .", ".($artHasPrecioOferta?"a.preciooferta":"0 AS preciooferta")
  .", ".($artHasCanjePuntos?"a.canjepuntos":"0 AS canjepuntos")
  .", ".($artHasMedida?"a.medida":"'NIU' AS medida")
  .", ".($artHasCodigo?"a.codigo":"'' AS codigo")
  .", ".($artHasCondicion?"a.condicion":"'1' AS condicion")
  .", ".($artHasResaltado?"a.resaltado":"0 AS resaltado")
  .", ".($artHasExoneradoIgv?"a.exonerado_igv":"'0' AS exonerado_igv")
  .", ".($artHasMayor?"a.mayor":"'0' AS mayor")
  .", ".($artHasPrecio?"a.precio":"0 AS precio")
  .", ".($artHasPrecioMayor?"a.precio_mayor":"0 AS precio_mayor")
  .", ".($artHasPrecioCompra?"a.precio_compra":"0 AS precio_compra")
  .", ".($artHasComisionM?"a.comisionm":"0 AS comisionm")
  .", ".($artHasComisionMP?"a.comisionmp":"0 AS comisionmp")
  .", ".($artHasComision?"a.comision":"0 AS comision")
  .", ".($artHasExistencia?"a.existencia":"'0' AS existencia")
  .", ".($artHasMoneda?"a.moneda":"'PEN' AS moneda")
  .", ".($artHasIdProveedor?"a.idproveedor":"0 AS idproveedor")
  .", ".($artHasCanje?"a.canje":"'NO' AS canje")
  .", ".($artHasIdCategoria?"a.idcategoria":"0 AS idcategoria")
  .", c.nombre AS grupo"
  .$selectMarcaArticulo.$selectLineaArticulo.$selectCodAfectArticulo;
$umHasCodigo = _table_has_column('unidad_medida','codigo');
$constock = isset($_REQUEST['constock']) ? trim((string)$_REQUEST['constock']) : 'TODO';
$stockFilter = '';
if($constock==='STOCK'){
  $stockFilter = " AND EXISTS(SELECT 1 FROM articulo_stock s WHERE s.idarticulo=a.txtCOD_ARTICULO ".(($fa['articulo']=='LOCAL')?" AND s.idlocal='".$_COOKIE['idlocal']."'":"")." AND COALESCE(s.stock,0)>0) ";
}
$filtroGrupo = isset($_REQUEST['filtro_grupo_art']) ? trim((string)$_REQUEST['filtro_grupo_art']) : '';
$filtroMarca = isset($_REQUEST['filtro_marca_art']) ? trim((string)$_REQUEST['filtro_marca_art']) : '';
$filtroLinea = isset($_REQUEST['filtro_linea_art']) ? trim((string)$_REQUEST['filtro_linea_art']) : '';
$extraFilter = '';
if($filtroGrupo!==''){ $extraFilter .= " AND c.nombre='".limpiarCadena($filtroGrupo)."' "; }
if($filtroMarca!=='' && $hasMarcaArticulo){
  $marcaFilterVal = limpiarCadena($filtroMarca);
  $extraFilter .= " AND (a.marca='".$marcaFilterVal."' ";
  if($hasMarcaTbl){
    $extraFilter .= " OR (a.marca REGEXP '^[0-9]+$' AND (SELECT nombre FROM marca m WHERE m.idmarca=a.marca LIMIT 1)='".$marcaFilterVal."') ";
  }
  $extraFilter .= " OR EXISTS(SELECT 1 FROM categoria cm WHERE cm.nombre='".$marcaFilterVal."' ".($catHasNivel?" AND cm.nivel='1' ":"")." AND (cm.idcategoria=a.marca OR cm.nombre=a.marca)) ";
  $extraFilter .= ") ";
}
if($filtroLinea!=='' && $hasLineaArticulo){
  $lineaFilterVal = limpiarCadena($filtroLinea);
  $extraFilter .= " AND (a.linea='".$lineaFilterVal."' ";
  $extraFilter .= " OR EXISTS(SELECT 1 FROM categoria cl WHERE cl.nombre='".$lineaFilterVal."' ".($catHasNivel?" AND cl.nivel='3' ":"")." AND (cl.idcategoria=a.linea OR cl.nombre=a.linea)) ";
  $extraFilter .= ") ";
}
$loadPresentacionesDet = !isset($_REQUEST['load_presentaciones']) || (string)$_REQUEST['load_presentaciones']!=='0';
$umByCodigo = array();
$umById = array();
if($hasUnidadMedidaTbl){
  $umCols = array();
  if($umHasCodigo){ $umCols[] = "codigo"; }
  $umCols[] = "id";
  if($umHasTit){ $umCols[] = "tit"; }
  if($umHasNombre){ $umCols[] = "nombre"; }
  $umSql = "SELECT ".implode(",", $umCols)." FROM unidad_medida";
  $umRs = ejecutarConsulta($umSql);
  if($umRs){
    while($umRow=mysqli_fetch_assoc($umRs)){
      $lbl = '';
      if($umHasTit && isset($umRow['tit']) && trim((string)$umRow['tit'])!==''){ $lbl=trim((string)$umRow['tit']); }
      elseif($umHasNombre && isset($umRow['nombre']) && trim((string)$umRow['nombre'])!==''){ $lbl=trim((string)$umRow['nombre']); }
      if($lbl===''){ continue; }
      if($umHasCodigo && isset($umRow['codigo']) && trim((string)$umRow['codigo'])!==''){ $umByCodigo[trim((string)$umRow['codigo'])]=$lbl; }
      if(isset($umRow['id']) && trim((string)$umRow['id'])!==''){ $umById[trim((string)$umRow['id'])]=$lbl; }
    }
  }
}
		
## Search 
$searchQuery = " ";
//REGEXP '^ [abcd]'	

    if ($_COOKIE['id'] == '27') {


//inicio buscador con palabras enteras de fin a inicio o viseversa

$searchQuery = " ";
if ($searchValue != '') {
$aKeywordf = $searchValueSql;
$query = "SELECT count(*) as allcount FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE MATCH (a.codigo, a.txtDESCRIPCION_ARTICULO) AGAINST('$aKeywordf'  IN NATURAL LANGUAGE MODE) AND a.idempresa='$_COOKIE[id]' $idlocal2 $stockFilter $extraFilter ";
$query2 = "SELECT ".$selectArtCols." FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE MATCH (a.codigo, a.txtDESCRIPCION_ARTICULO) AGAINST('$aKeywordf'  IN NATURAL LANGUAGE MODE)  AND a.idempresa='$_COOKIE[id]' $idlocal2 $stockFilter $extraFilter limit " . $row . "," . $rowperpage;
            $records = ejecutarConsultaSimpleFila("SELECT count(*) as allcount FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.idempresa='" . $_COOKIE['id'] . "' $idlocal2 $stockFilter $extraFilter ");
            $totalRecords = $records['allcount'];
## Total number of record with filtering
            $records = ejecutarConsultaSimpleFila($query);
            $totalRecordwithFilter = $records['allcount'];

## Fetch records
            $empRecords = ejecutarConsulta($query2);
        } else {
            $records = ejecutarConsultaSimpleFila("select count(*) as allcount from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.idempresa='$_COOKIE[id]' $idlocal2 $stockFilter $extraFilter " . $searchQuery);
            $totalRecords = $records['allcount'];
## Total number of record with filtering
            $records = ejecutarConsultaSimpleFila("select count(*) as allcount from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.idempresa='$_COOKIE[id]' $idlocal2 $stockFilter $extraFilter " . $searchQuery);
            $totalRecordwithFilter = $records['allcount'];
## Fetch records
            $empQuery = "select ".$selectArtCols." from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.idempresa='$_COOKIE[id]' $idlocal2 $stockFilter $extraFilter " . $searchQuery . "  ORDER BY a.txtCOD_ARTICULO DESC limit " . $row . "," . $rowperpage;
            $empRecords = ejecutarConsulta($empQuery);

        }
//fin de buscador
    } else {
        if ($searchValue != '') {
	            $searchQuery .= " and ( txtCOD_ARTICULO LIKE '%" . $searchValueSql . "%' ";
	            $searchQuery .= " OR txtDESCRIPCION_ARTICULO LIKE '%" . $searchValueSql . "%' ";
	            $searchQuery .= " OR codigo LIKE '%" . $searchValueSql . "%' ";
	            $searchQuery .= " OR c.nombre LIKE '%" . $searchValueSql . "%' ";
            if($hasMarcaArticulo){
              $searchQuery .= " OR a.marca LIKE '%" . $searchValueSql . "%' ";
              if($hasMarcaTbl){ $searchQuery .= " OR a.marca IN (SELECT idmarca FROM marca WHERE nombre LIKE '%" . $searchValueSql . "%') "; }
              $searchQuery .= " OR EXISTS(SELECT 1 FROM categoria cm WHERE cm.nombre LIKE '%".$searchValueSql."%' ".($catHasNivel?" AND cm.nivel='1' ":"")." AND (cm.idcategoria=a.marca OR cm.nombre=a.marca)) ";
            }
            if($hasLineaArticulo){
              $searchQuery .= " OR a.linea LIKE '%" . $searchValueSql . "%' ";
              $searchQuery .= " OR EXISTS(SELECT 1 FROM categoria cl WHERE cl.nombre LIKE '%".$searchValueSql."%' ".($catHasNivel?" AND cl.nivel='3' ":"")." AND (cl.idcategoria=a.linea OR cl.nombre=a.linea)) ";
            }
	            $searchQuery .= " OR txtCOD_ARTICULO IN (SELECT cod_articulo FROM articulo_serie WHERE serie LIKE '%".$searchValueSql."%' OR lote LIKE '%".$searchValueSql."%' ) ";
            $searchQuery .= ") ";
            //txtCOD_ARTICULO IN (SELECT cod_articulo FROM articulo_serie WHERE serie LIKE '%F0011%')
        }
        $records = ejecutarConsultaSimpleFila("select count(*) as allcount from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria  WHERE a.idempresa='$_COOKIE[id]' $idlocal2 $stockFilter $extraFilter AND a.condicion='1' AND a.medida!='ZZ'" . $searchQuery);
        $totalRecords = $records['allcount'];

## Total number of record with filteringAND a.condicion='1'
        $records = ejecutarConsultaSimpleFila("select count(*) as allcount from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria  WHERE a.idempresa='$_COOKIE[id]' $idlocal2 $stockFilter $extraFilter AND a.condicion='1' AND a.medida!='ZZ' " . $searchQuery);
        $totalRecordwithFilter = $records['allcount'];

## Fetch records
        $empQuery = "select ".$selectArtCols." from articulo  a INNER JOIN categoria c ON a.idcategoria=c.idcategoria  WHERE a.idempresa='$_COOKIE[id]' $idlocal2 $stockFilter $extraFilter AND a.condicion='1' AND a.medida != 'ZZ' " . $searchQuery . " ORDER BY a.txtCOD_ARTICULO ASC  limit " . $row . "," . $rowperpage;
        $empRecords = ejecutarConsulta($empQuery);
        //fin de buscador corelativo
    }

if(!$empRecords){
    $fallbackWhere = " WHERE a.idempresa='$_COOKIE[id]' ";
if(isset($fa['articulo']) && $fa['articulo']=='LOCAL' && _table_has_column('articulo','idlocal')){
        $fallbackWhere .= " AND a.idlocal='".$_COOKIE['idlocal']."' ";
    }
    $fallbackWhere .= " AND ".($artHasCondicion ? "a.condicion='1'" : "1=1");
    $fallbackWhere .= $stockFilter;
    $fallbackWhere .= $extraFilter;
    $empFallback = "SELECT ".$selectArtCols." FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria ".$fallbackWhere." ORDER BY a.txtCOD_ARTICULO ASC limit ".$row.",".$rowperpage;
    $empRecords = ejecutarConsulta($empFallback);
    $records = ejecutarConsultaSimpleFila("SELECT count(*) as allcount FROM articulo a ".$fallbackWhere);
    $totalRecords = isset($records['allcount']) ? (int)$records['allcount'] : 0;
    $totalRecordwithFilter = $totalRecords;
}
		
while ($empRecords && ($reg=$empRecords->fetch_object())){

$preciofinal=$reg->precio;

if($hasArticuloPrecioClienteTbl){
	$sqlpreiocli="SELECT *FROM articulo_precio_cliente WHERE idproducto='$reg->txtCOD_ARTICULO' AND idcliente='$idcliente' ";
	$rowpreciocliente= ejecutarConsultaSimpleFila($sqlpreiocli);
	if($rowpreciocliente && isset($rowpreciocliente['precio'])){
		$preciofinal=$rowpreciocliente['precio'];
	}
}


$fec=false;
if($hasArticuloSerieTbl){
	$sqlf="SELECT *FROM articulo_serie WHERE cod_articulo='$reg->txtCOD_ARTICULO' AND estado='1' ORDER BY id ASC ";
	$fec= ejecutarConsultaSimpleFila($sqlf);
}
$estfecha='0';
$dias='0';	
if($fec){

$diff =restardias($hoy, $fec['fecha_vto']);
$dias=$diff;
if($diff<'124'){ $estfecha='2'; }else if($diff<='186'&&$diff>'124'){ $estfecha='1'; }

}

/*
$sql="SELECT * FROM unidad_medida WHERE id='$reg->medida'";
$mostrar=ejecutarConsultaSimpleFila($sql);
*/

$stockf='0.00';

$stock=false;
if($hasArticuloStockTbl){
	$sqls="SELECT * FROM articulo_stock WHERE idlocal='$_COOKIE[idlocal]' AND idarticulo='$reg->txtCOD_ARTICULO' ";
	$stock=ejecutarConsultaSimpleFila($sqls);
}
$texto = str_replace($no_permitidas, $permitidas, $reg->txtDESCRIPCION_ARTICULO, $reg->stock);	

if($_COOKIE['id']=='19'){
	
if($reg->existencia=='91'){
$stockf='3000.00';
}else{
if($stock){ $stockf=round($stock['stock'], 2);	 }
}
	
}else if($reg->medida=='ZZ'){
$stockf='3000.00';
}else{
if($stock){ $stockf=round($stock['stock'], 2);	 }
}

	
$imei='<select id="un'.$reg->txtCOD_ARTICULO.'" name="un'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 100px; padding: 1px; margin: 1px;  height: 22px; " />';
$preciofinal4 = number_format((float)$preciofinal, 4, '.', '');
$unidadBaseTxt='UNIDAD';
$medidaKey = limpiarCadena($reg->medida);
if($medidaKey!=='' && isset($umByCodigo[$medidaKey])){ $unidadBaseTxt=$umByCodigo[$medidaKey]; }
elseif($medidaKey!=='' && isset($umById[$medidaKey])){ $unidadBaseTxt=$umById[$medidaKey]; }
	
$imei.='<option value="'.$reg->medida.'|0|0|0|'.$preciofinal.'|'.$reg->mayor.'|'.$reg->comision.'|'.$reg->comisionm.'|'.$reg->comisionmp.'|'.$reg->txtCOD_ARTICULO.'|0">'.$preciofinal4.' | '.$unidadBaseTxt.'</option>';
	
if($loadPresentacionesDet && $hasArticuloUnidadTbl && $auHasIdProducto){
  $datos = "SELECT ".$auSelectCols." FROM articulo_unidad WHERE idproducto='$reg->txtCOD_ARTICULO' ";
  $datos = ejecutarConsulta($datos);
  if($datos){
    while($dato=mysqli_fetch_array($datos)) {
      $precioPres = isset($dato['precio']) ? (float)$dato['precio'] : (float)$preciofinal;
      $precioPres4 = number_format($precioPres, 4, '.', '');
      $imei.='<option value="'.$dato['medida'].'|'.$dato['cti'].'|'.$precioPres.'|1|'.$precioPres.'|'.$dato['ctimayor'].'|'.$dato['comision'].'|'.$dato['ctimayor'].'|'.$dato['comisionm'].'|'.$reg->txtCOD_ARTICULO.$dato['id'].'|'.$dato['id'].'">'.$precioPres4.' | '.$dato['nombre'].'</option>';
    }
  }
}
$imei.='</select>';

$lote='<select id="ser'.$reg->txtCOD_ARTICULO.'" name="ser'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 100px; padding: 1px; margin: 1px;  height: 22px; " />';
	
if($hasArticuloSerieTbl){
	$datos = "SELECT *FROM articulo_serie WHERE cod_articulo='$reg->txtCOD_ARTICULO' AND idlocal='$_COOKIE[idlocal]' AND estado='1' ";
	$datos = ejecutarConsulta($datos);
	if($datos){
		while($dato=mysqli_fetch_array($datos)) {
			$lote.='<option value="'.$dato['id'].'|'.$dato['serie'].'|'.$dato['lote'].'|'.$dato['fecha_vto'].'">'.$dato['lote'].'/'.$dato['serie'].'</option>';
		}
	}
}
$lote.='</select>';

$oculto='<span style="display:none;"> '.$reg->txtDESCRIPCION_ARTICULO.' </span>';
/*
$oculto='<span style="display:none;"> '.$reg->nombre.' '.$reg->txtDESCRIPCION_ARTICULO.' </span>';
*/
$exonerado='<input type="hidden" id="ex'.$reg->txtCOD_ARTICULO.'"  name="ex'.$reg->txtCOD_ARTICULO.'" value="'.$reg->exonerado_igv.'" >';

$mayor='<input type="hidden" id="may'.$reg->txtCOD_ARTICULO.'"  name="may'.$reg->txtCOD_ARTICULO.'" value="'.$reg->mayor.'" > <input id="pm'.$reg->txtCOD_ARTICULO.'" class="form-control"  style="padding: 1px; margin: 1px;  height: 22px; " name="pm'.$reg->txtCOD_ARTICULO.'" value="'.$preciofinal.'" type="hidden">';
	
$titulo=' <input type="text"  class="form-control listado" id="tit'.$reg->txtCOD_ARTICULO.'"  name="tit'.$reg->txtCOD_ARTICULO.'" value="'.$texto.'" > ';
$mayor.=' <input type="hidden" id="stock'.$reg->txtCOD_ARTICULO.'"  name="stock'.$reg->txtCOD_ARTICULO.'" value="'.$stockf.'" > ';
$mayor.=' <input type="hidden" id="codigo'.$reg->txtCOD_ARTICULO.'"  name="codigo'.$reg->txtCOD_ARTICULO.'" value="'.$reg->codigo.'" > ';
	
$stock='<span class="badge label-success" data-toggle="tooltip" data-html="true" title="BUEN STOCK" >'.$stockf.'</span>';
	
if($stockf=='0'){ 
$stock='<span class="badge label-danger" data-toggle="tooltip" data-html="true" title="SIN STOCK" >'.$stockf.'</span>';
}

$exonerado='<input type="hidden" id="exone'.$reg->txtCOD_ARTICULO.'"  name="ex'.$reg->txtCOD_ARTICULO.'" value="'.$reg->exonerado_igv.'" >';
	
$mayor='<input type="hidden" id="may'.$reg->txtCOD_ARTICULO.'"  name="may'.$reg->txtCOD_ARTICULO.'" value="'.$reg->mayor.'" >';
	
$texto = str_replace($no_permitidas, $permitidas, $reg->txtDESCRIPCION_ARTICULO);
	
$tit='<input id="titdetalle'.$reg->txtCOD_ARTICULO.'" class="form-control"  style="width: 220px; padding: 1px; margin: 1px;  height: 22px; " name="titdetalle'.$reg->txtCOD_ARTICULO.'" value="'.$texto.'" type="text">
<div style="display: none">'.$texto.'</div>';

$estilo='';
	
if(isset($fa['precio_porcentaje']) && $fa['precio_porcentaje']=='SI'){ $estilo='readonly'; }

if($exoneradoigv=='0'){

$selectedAfectacion='10';
$cliPuntos = isset($cli['puntos']) ? (float)$cli['puntos'] : 0;
if($reg->canje=='SI' && $cliPuntos>=(float)$reg->canjepuntos){
    $selectedAfectacion='35';
}elseif(isset($reg->cod_afectacion_igv) && trim((string)$reg->cod_afectacion_igv)!=''){
    $selectedAfectacion=trim((string)$reg->cod_afectacion_igv);
}elseif((string)$reg->exonerado_igv==='1'){
    $selectedAfectacion='20';
}elseif((string)$reg->exonerado_igv==='2'){
    $selectedAfectacion='35';
}elseif((string)$reg->exonerado_igv==='3'){
    $selectedAfectacion='30';
}
$tipoigv='<select id="tipoigv'.$reg->txtCOD_ARTICULO.'" name="tipoigv'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 165px; padding: 1px; margin: 1px;  height: 25px; " />';
$tipoigv.=opcionesAfectacionVentaSelect($selectedAfectacion);
$tipoigv.='</select>';

}else{

$selectedAfectacion=trim((string)$exoneradoigv);
if($selectedAfectacion==='0'){ $selectedAfectacion='10'; }
if($selectedAfectacion==='1'){ $selectedAfectacion='20'; }
if($selectedAfectacion==='2'){ $selectedAfectacion='35'; }
if($selectedAfectacion==='3'){ $selectedAfectacion='30'; }
if($selectedAfectacion===''){ $selectedAfectacion='10'; }
$tipoigv='<select id="tipoigv'.$reg->txtCOD_ARTICULO.'" name="tipoigv'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 165px; padding: 1px; margin: 1px;  height: 25px; " />';
$tipoigv.=opcionesAfectacionVentaSelect($selectedAfectacion);
$tipoigv.='</select>';


}

$proveeedor=false;
if($hasPersonaTbl){
	$sqlpro="SELECT * FROM persona WHERE idpersona='$reg->idproveedor' ";
	$proveeedor=ejecutarConsultaSimpleFila($sqlpro);
}
	
$codigoproveedor='';
if($proveeedor){
$codigoproveedor=$proveeedor['codigo'];
}
	
$marca='GENERICA';
if(isset($reg->marca) && trim((string)$reg->marca)!==''){
  $marca = trim((string)$reg->marca);
  if(ctype_digit($marca)){
    $m = $hasMarcaTbl ? ejecutarConsultaSimpleFila("SELECT nombre FROM marca WHERE idmarca='$marca' ") : false;
    if($m && isset($m['nombre']) && trim((string)$m['nombre'])!==''){
      $marca = trim((string)$m['nombre']);
    }
  }
}

$puntosf=isset($cli['puntos']) ? $cli['puntos'] : '0';
$grupoFiltro = isset($reg->grupo) ? trim((string)$reg->grupo) : '';
$lineaFiltro = isset($reg->linea) ? trim((string)$reg->linea) : '';
$metaFiltro = '<span class="art-meta-filtro" style="display:none" data-grupo="'.htmlspecialchars($grupoFiltro,ENT_QUOTES,'UTF-8').'" data-marca="'.htmlspecialchars($marca,ENT_QUOTES,'UTF-8').'" data-linea="'.htmlspecialchars($lineaFiltro,ENT_QUOTES,'UTF-8').'"></span>';

$tasaIgvDoc = isset($fa['igv']) ? ((float)$fa['igv'] / 100) : 0.18;
if($tasaIgvDoc <= 0){ $tasaIgvDoc = 0.18; }
$preciosinigv=round(($preciofinal/(1+$tasaIgvDoc)), 5);

$botonstock='<button class="btn btn-primary btn-xs" onclick="abrirstock(\''.$reg->txtCOD_ARTICULO.'\', \''.$texto.'\')">'.$stockf.' <span class="fa fa-bars"></span></button>';

    if($_COOKIE['administrador']==1){
        $preciocomprna=$reg->precio_compra;
    }else{
        $preciocomprna="0.00";
    }

$data[]=array(
"0"=>$imei,
"1"=>$marca,
"2"=>$tit.$metaFiltro,
"3"=>$lote,
"4"=>(strtoupper((string)$reg->moneda)==='USD' ? '<span class="label" style="background:#2e7d32;color:#fff;">USD</span>' : '<span class="label" style="background:#ff9800;color:#111;">PEN</span>'),
"5"=>'<input id="p'.$reg->txtCOD_ARTICULO.'" class="form-control"  style="padding: 1px; margin: 1px;  height: 22px; " name="p'.$reg->txtCOD_ARTICULO.'" value="'.$preciofinal.'" type="text" '.$estilo.' > <input id="po'.$reg->txtCOD_ARTICULO.'" name="po'.$reg->txtCOD_ARTICULO.'" value="'.$preciofinal.'" type="hidden">',	
"6"=>$preciosinigv,	
"7"=>$preciocomprna,
"8"=>"<input type='text' class='form-control'  style='width: 40px;  padding: 1px; margin: 1px;  height: 22px; ' value='1' name='ctidet".$reg->txtCOD_ARTICULO."' id='ctidet".$reg->txtCOD_ARTICULO."' />",
"9"=>$botonstock,
"10"=>$tipoigv,	
"11"=>$codigoproveedor,
"12"=>'<button class="btn btn-danger btn-xs" onclick="agregartabla(\''.$reg->txtCOD_ARTICULO.'\', \''.$preciofinal.'\', \''.$texto.'\', \''.$reg->codigo.'\', \''.$stockf.'\', \'0\', \''.$reg->exonerado_igv.'\', \''.$reg->moneda.'\', \''.$reg->preciooferta.'\', \''.$reg->canjepuntos.'\')"><span class="fa fa-shopping-cart"></span></button>',
"13"=>$reg->codigo,
"14"=>$puntosf,
"15"=>$reg->canjepuntos,
"16"=>$reg->canje
);
	
	
	
	
 		}
$json_data = array(
        "draw"            =>intval($draw),
        "recordsTotal"    =>$totalRecords,
        "recordsFiltered" =>$totalRecordwithFilter,
        "data"            => $data   // total data array
        );
		
		if(ob_get_length()){ ob_clean(); }
	 		echo json_encode($json_data);
}catch(Throwable $e){
		$drawErr = isset($_REQUEST['draw']) ? (int)$_REQUEST['draw'] : 0;
		$dataErr = array();
		$colPrecio = _table_has_column('articulo','precio') ? "precio" : "0 AS precio";
		$colCodigo = _table_has_column('articulo','codigo') ? "codigo" : "'' AS codigo";
		$colExo    = _table_has_column('articulo','exonerado_igv') ? "exonerado_igv" : "'0' AS exonerado_igv";
		$colMon    = _table_has_column('articulo','moneda') ? "moneda" : "'PEN' AS moneda";
		$rsErr = ejecutarConsulta("SELECT txtCOD_ARTICULO, txtDESCRIPCION_ARTICULO, ".$colPrecio.", ".$colCodigo.", ".$colExo.", ".$colMon." FROM articulo WHERE idempresa='$_COOKIE[id]' ORDER BY txtCOD_ARTICULO DESC LIMIT 0,50");
		if($rsErr){
			while($rErr = $rsErr->fetch_object()){
				$codArt = isset($rErr->txtCOD_ARTICULO)?$rErr->txtCOD_ARTICULO:'';
				$precio = isset($rErr->precio)?(float)$rErr->precio:0;
				$precio4 = number_format($precio,4,'.','');
				$desc = isset($rErr->txtDESCRIPCION_ARTICULO)?$rErr->txtDESCRIPCION_ARTICULO:'';
					$dataErr[] = array(
						"0"=>'<select id="un'.$codArt.'" name="un'.$codArt.'" class="form-control" style="width:100px"><option value="NIU|0|0|0|'.$precio.'|0|0|0|0|'.$codArt.'|0">'.$precio4.' | UNIDAD</option></select>',
						"1"=>'GENERICA',
						"2"=>'<input id="titdetalle'.$codArt.'" class="form-control" style="width:220px" value="'.htmlspecialchars($desc,ENT_QUOTES,'UTF-8').'" type="text">',
						"3"=>'',
						"4"=>(strtoupper((string)$rErr->moneda)==='USD' ? '<span class="label" style="background:#2e7d32;color:#fff;">USD</span>' : '<span class="label" style="background:#ff9800;color:#111;">PEN</span>'),
						"5"=>'<input id="p'.$codArt.'" class="form-control" value="'.$precio.'" type="text"><input id="po'.$codArt.'" value="'.$precio.'" type="hidden">',
						"6"=>$precio>0?round($precio/1.18,5):0,
						"7"=>"0.00",
						"8"=>"<input type='text' class='form-control' style='width:40px' value='1' name='ctidet".$codArt."' id='ctidet".$codArt."' />",
						"9"=>'<button class="btn btn-primary btn-xs">0.00 <span class="fa fa-bars"></span></button>',
						"10"=>'<select id="tipoigv'.$codArt.'" name="tipoigv'.$codArt.'" class="form-control" style="width:165px"><option value="10">10 - Gravado</option></select>',
						"11"=>'',
						"12"=>'<button class="btn btn-danger btn-xs" onclick="agregartabla(\''.$codArt.'\', \''.$precio.'\', \''.htmlspecialchars($desc,ENT_QUOTES,'UTF-8').'\', \''.$rErr->codigo.'\', \'0.00\', \'0\', \''.$rErr->exonerado_igv.'\', \''.$rErr->moneda.'\', \'0\', \'0\')"><span class="fa fa-shopping-cart"></span></button>',
						"13"=>$rErr->codigo,
						"14"=>'0',
						"15"=>'0',
						"16"=>'NO'
					);
			}
		}
		$fallback = array(
			"draw"=>$drawErr,
			"recordsTotal"=>count($dataErr),
			"recordsFiltered"=>count($dataErr),
			"data"=>$dataErr
		);
		if(ob_get_length()){ ob_clean(); }
		echo json_encode($fallback);
}
		error_reporting($__listarArticulosErrLevel);
		
		
break;

		
		
	
case 'listarsalida':

$hoy=date('Y-m-d');
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
		
if(isset($_GET['niv'])){ $niv='0'; }else{ $niv='1'; }

$idlocal=''; $idlocal2='';

if($fa['articulo']=='LOCAL'){ $idlocal=' AND idlocal="'.$_COOKIE['idlocal'].'" '; $idlocal2=' AND a.idlocal="'.$_COOKIE['idlocal'].'" ';  }
		
$data= Array();

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
	
## Search 
$searchQuery = " ";
		
if($searchValue != ''){
$searchQuery .= " and ( codigo LIKE '%".$_POST['search']['value']."%' ";
$searchQuery .=" OR txtDESCRIPCION_ARTICULO LIKE '%".$_POST['search']['value']."%' ";
$searchQuery .=" OR codigo LIKE '%".$_POST['search']['value']."%' ) ";
}
		
$records= ejecutarConsultaSimpleFila("select count(*) as allcount from articulo WHERE condicion='1' AND idempresa='$_COOKIE[id]' ");
$totalRecords = $records['allcount'];

## Total number of record with filtering
$records= ejecutarConsultaSimpleFila("select count(*) as allcount from articulo WHERE condicion='1' $idlocal AND idempresa='$_COOKIE[id]' ".$searchQuery);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select a.txtCOD_ARTICULO, a.txtDESCRIPCION_ARTICULO,  a.medida, a.codigo, a.condicion, a.resaltado, c.nombre, a.exonerado_igv, a.mayor, a.precio, a.precio_mayor, a.precio_compra, a.comisionm, a.comisionmp, a.comision, a.existencia from articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.condicion='1' $idlocal2 AND a.idempresa='$_COOKIE[id]' ".$searchQuery." limit ".$row.",".$rowperpage;
$empRecords = ejecutarConsulta($empQuery);

while ($reg=$empRecords->fetch_object()){
	
$sqlf="SELECT *FROM articulo_serie WHERE cod_articulo='$reg->txtCOD_ARTICULO' AND estado='1' ORDER BY id ASC ";
$fec= ejecutarConsultaSimpleFila($sqlf);
$estfecha='0';
$dias='0';	
if($fec){

$diff =restardias($hoy, $fec['fecha_vto']);
$dias=$diff;
if($diff<'124'){ $estfecha='2'; }else if($diff<='186'&&$diff>'124'){ $estfecha='1'; }

}
	
/*
$sql="SELECT * FROM unidad_medida WHERE id='$reg->medida'";
$mostrar=ejecutarConsultaSimpleFila($sql);
*/

if($niv=='0'){
$sqls="SELECT SUM(stock) AS stock FROM articulo_stock WHERE idarticulo='$reg->txtCOD_ARTICULO' ";
$stock=ejecutarConsultaSimpleFila($sqls);	
}else{
$sqls="SELECT * FROM articulo_stock WHERE idlocal='$_COOKIE[idlocal]' AND idarticulo='$reg->txtCOD_ARTICULO' ";
$stock=ejecutarConsultaSimpleFila($sqls);	
}
	
$texto = str_replace($no_permitidas, $permitidas, $reg->txtDESCRIPCION_ARTICULO, $stock['stock']);
	
if($_COOKIE['id']=='19'){
	
if($reg->existencia=='91'){
$stockf='3000.00';
}else{
$stockf=round($stock['stock'], 2);	
}
	
}else if($reg->medida=='ZZ'){
$stockf='3000.00';
}else{
$stockf=round($stock['stock'], 2);	
}

	
$imei='<select id="un'.$reg->txtCOD_ARTICULO.'" name="un'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 120px; padding: 1px; margin: 1px;  height: 22px; " />';
	
$imei.='<option value="'.$reg->medida.'|0|0|0|'.$reg->precio_mayor.'|'.$reg->mayor.'|'.$reg->comision.'|'.$reg->comisionm.'|'.$reg->comisionmp.'|'.$reg->txtCOD_ARTICULO.'|0">'.$reg->precio.' | UNIDAD</option>';
	
$datos = "SELECT *FROM articulo_unidad  WHERE idproducto='$reg->txtCOD_ARTICULO' ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$imei.='<option value="'.$dato['medida'].'|'.$dato['cti'].'|'.$dato['precio'].'|1|'.$dato['preciom'].'|'.$dato['ctimayor'].'|'.$dato['comision'].'|'.$dato['ctimayor'].'|'.$dato['comisionm'].'|'.$reg->txtCOD_ARTICULO.$dato['id'].'|'.$dato['id'].'">'.$dato['precio'].' | '.$dato['nombre'].'</option>';
}
$imei.='</select>';

$lote='<select id="ser'.$reg->txtCOD_ARTICULO.'" name="ser'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 120px; padding: 1px; margin: 1px;  height: 22px; " />';
	
$datos = "SELECT *FROM articulo_serie WHERE cod_articulo='$reg->txtCOD_ARTICULO' AND idlocal='$_COOKIE[idlocal]' AND estado='1' ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$lote.='<option value="'.$dato['id'].'|'.$dato['serie'].'|'.$dato['lote'].'|'.$dato['fecha_vto'].'">'.$dato['lote'].'/'.$dato['serie'].'</option>';
}
$lote.='</select>';

$oculto='<span style="display:none;"> '.$reg->nombre.' '.$reg->txtDESCRIPCION_ARTICULO.' </span>';	
$exonerado='<input type="hidden" id="ex'.$reg->txtCOD_ARTICULO.'"  name="ex'.$reg->txtCOD_ARTICULO.'" value="'.$reg->exonerado_igv.'" >';
	
$mayor='<input type="hidden" id="may'.$reg->txtCOD_ARTICULO.'"  name="may'.$reg->txtCOD_ARTICULO.'" value="'.$reg->mayor.'" > <input id="pm'.$reg->txtCOD_ARTICULO.'" class="form-control"  style="padding: 1px; margin: 1px;  height: 22px; " name="pm'.$reg->txtCOD_ARTICULO.'" value="'.$reg->precio_mayor.'" type="hidden">';
	
$titulo=' <input type="text"  class="form-control listado" id="tit'.$reg->txtCOD_ARTICULO.'"  name="tit'.$reg->txtCOD_ARTICULO.'" value="'.$texto.'" > ';
$mayor.=' <input type="hidden" id="stock'.$reg->txtCOD_ARTICULO.'"  name="stock'.$reg->txtCOD_ARTICULO.'" value="'.$stockf.'" > ';
$mayor.=' <input type="hidden" id="codigo'.$reg->txtCOD_ARTICULO.'"  name="codigo'.$reg->txtCOD_ARTICULO.'" value="'.$reg->codigo.'" > ';
	
$stock='<span class="badge label-success" data-toggle="tooltip" data-html="true" title="BUEN STOCK" >'.$stockf.'</span>';
	
if($stockf=='0'){ 
$stock='<span class="badge label-danger" data-toggle="tooltip" data-html="true" title="SIN STOCK" >'.$stockf.'</span>';
}

$exonerado='<input type="hidden" id="exone'.$reg->txtCOD_ARTICULO.'"  name="ex'.$reg->txtCOD_ARTICULO.'" value="'.$reg->exonerado_igv.'" >';
	
$mayor='<input type="hidden" id="may'.$reg->txtCOD_ARTICULO.'"  name="may'.$reg->txtCOD_ARTICULO.'" value="'.$reg->mayor.'" >';
	
$texto = str_replace($no_permitidas, $permitidas, $reg->txtDESCRIPCION_ARTICULO);
	
$tit='<input id="tit'.$reg->txtCOD_ARTICULO.'" class="form-control"  style="width: 270px; padding: 1px; margin: 1px;  height: 22px; " name="tit'.$reg->txtCOD_ARTICULO.'" value="'.$texto.'" type="text">
<div style="display: none">'.$texto.'</div>';




$localdest='<select id="loc'.$reg->txtCOD_ARTICULO.'" name="loc'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 120px; padding: 1px; margin: 1px;  height: 22px; " />';	
	
$datos = "SELECT *FROM sucursal WHERE nivel='0' AND estado='1' AND idempresa='$_COOKIE[id]'  ORDER BY id ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$localdest.='<option value="'.$dato['id'].'"'; if($dato['id']==$_COOKIE['idlocal']){ $localdest.='selected'; } $localdest.='>'; $localdest.=$dato['sucursal'].'</option>';
}
$localdest.='</select>';
	

$data[]=array(
"0"=>$imei,
"1"=>$localdest,
"2"=>$tit,
"3"=>$lote,
"4"=>'<input id="p'.$reg->txtCOD_ARTICULO.'" class="form-control"  style="padding: 1px; margin: 1px;  height: 22px; " name="p'.$reg->txtCOD_ARTICULO.'" value="'.$reg->precio.'" type="text"> <input id="po'.$reg->txtCOD_ARTICULO.'" name="po'.$reg->txtCOD_ARTICULO.'" value="'.$reg->precio.'" type="hidden">',	

"5"=>"<input type='text' class='form-control'  style='width: 40px;  padding: 1px; margin: 1px;  height: 22px; ' value='1' name='cti".$reg->txtCOD_ARTICULO."' id='cti".$reg->txtCOD_ARTICULO."' />",
"6"=>$stock,

"7"=>'<button class="btn btn-danger btn-xs" onclick="agregartabla(\''.$reg->txtCOD_ARTICULO.'\', \''.$reg->precio.'\', \''.$texto.'\', \''.$reg->codigo.'\', \''.$stockf.'\', \'0\', \''.$reg->exonerado_igv.'\')"><span class="fa fa-shopping-cart"></span></button>'
);
	
	
	
	
 		}
$json_data = array(
        "draw"            =>intval($draw),
        "recordsTotal"    =>$totalRecords,
        "recordsFiltered" =>$totalRecordwithFilter,
        "data"            => $data   // total data array
        );
		
		
 		echo json_encode($json_data);
		
		
	break;
		

	

// ============================================================
// Select2: F.Pago/M.Pago (caja_tipopago_persona)
// ============================================================
case 'select_tipopago_persona':
    header('Content-Type: application/json; charset=utf-8');
    $idempresa = _ctx_idempresa();

    // Solo empresa logueada
    if($idempresa === '0'){
        echo json_encode([]);
        exit();
    }

    $term = isset($_GET['searchTerm']) ? limpiarCadena($_GET['searchTerm']) : '';

    $hasEmpresa = _table_has_column('caja_tipopago_persona','idempresa');
    $hasEstado  = _table_has_column('caja_tipopago_persona','estado');

    $sql = "SELECT id AS id, descripcion AS text FROM caja_tipopago_persona WHERE 1=1";
    if($hasEmpresa){ $sql .= " AND idempresa='$idempresa'"; }
    if($hasEstado){ $sql .= " AND estado='1'"; }
    if($term !== ''){ $sql .= " AND descripcion LIKE '%".str_replace("'","",$term)."%'"; }
    $sql .= " ORDER BY descripcion ASC LIMIT 100";

    $rs = ejecutarConsulta($sql);
    $out = [];
    if($rs){
        while($r = $rs->fetch_assoc()) {
            $out[] = ['id'=>(string)$r['id'], 'text'=>(string)$r['text']];
        }
    }

    echo json_encode($out);
    exit();
break;

// ============================================================
// Cliente -> F.Pago/M.Pago (autoselección)
// - Origen: persona.venta_pago (o personal.venta_pago)
// - Destino: caja_tipopago_persona.id (misma empresa logueada)
// Respuesta: {ok:true, id_tipopago_persona, descripcion}
// ============================================================
case 'cliente_venta_pago':
    header('Content-Type: application/json; charset=utf-8');
    $idempresa = _ctx_idempresa();
    if($idempresa === '0'){
        echo json_encode(['ok'=>false,'error'=>'Empresa no válida']);
        exit();
    }

    $idcliente = isset($_GET['idcliente']) ? limpiarCadena($_GET['idcliente']) : '';
    if($idcliente === ''){
        echo json_encode(['ok'=>false,'error'=>'Falta idcliente']);
        exit();
    }

    // 1) Obtener venta_pago desde tabla persona (preferido) o personal (compat)
    $ventaPago = '';

    if(_table_exists('persona') && _table_has_column('persona','venta_pago')){
        $colId = _table_has_column('persona','idpersona') ? 'idpersona' : (_table_has_column('persona','id') ? 'id' : '');
        if($colId !== ''){
            $rs = ejecutarConsulta("SELECT venta_pago FROM persona WHERE {$colId}='{$idcliente}' LIMIT 1");
            if($rs){
                $row = $rs->fetch_assoc();
                if($row && isset($row['venta_pago'])){ $ventaPago = trim((string)$row['venta_pago']); }
            }
        }
    }

    if($ventaPago === '' && _table_exists('personal') && _table_has_column('personal','venta_pago')){
        $colId = _table_has_column('personal','idpersona') ? 'idpersona' : (_table_has_column('personal','id') ? 'id' : '');
        if($colId !== ''){
            $rs = ejecutarConsulta("SELECT venta_pago FROM personal WHERE {$colId}='{$idcliente}' LIMIT 1");
            if($rs){
                $row = $rs->fetch_assoc();
                if($row && isset($row['venta_pago'])){ $ventaPago = trim((string)$row['venta_pago']); }
            }
        }
    }

    // Si no tiene venta_pago o es 0, no forzamos selección
    if($ventaPago === '' || $ventaPago === '0'){
        echo json_encode(['ok'=>false,'error'=>'Cliente sin venta_pago']);
        exit();
    }

    // 2) Validar que exista en caja_tipopago_persona y pertenezca a la empresa
    $hasEmpresa = _table_has_column('caja_tipopago_persona','idempresa');
    $hasEstado  = _table_has_column('caja_tipopago_persona','estado');

    $sql = "SELECT id, descripcion FROM caja_tipopago_persona WHERE id='{$ventaPago}'";
    if($hasEmpresa){ $sql .= " AND idempresa='{$idempresa}'"; }
    if($hasEstado){ $sql .= " AND estado='1'"; }
    $sql .= " LIMIT 1";

    $rs2 = ejecutarConsulta($sql);
    if(!$rs2){
        echo json_encode(['ok'=>false,'error'=>'No se pudo consultar caja_tipopago_persona']);
        exit();
    }
    $row2 = $rs2->fetch_assoc();
    if(!$row2 || !isset($row2['id'])){
        echo json_encode(['ok'=>false,'error'=>'venta_pago no corresponde a la empresa']);
        exit();
    }

    echo json_encode([
        'ok' => true,
        'id_tipopago_persona' => (string)$row2['id'],
        'descripcion' => isset($row2['descripcion']) ? (string)$row2['descripcion'] : ''
    ]);
    exit();
break;


// ============================================================
// Configuración de cuotas por F.Pago/M.Pago
// Respuesta: {ok:true, cuotas, dias}
// ============================================================
case 'cfg_tipopago_persona':
    header('Content-Type: application/json; charset=utf-8');
    $idempresa = _ctx_idempresa();
    if($idempresa === '0'){
        echo json_encode(['ok'=>false,'error'=>'Empresa no válida']);
        exit();
    }

    $id = isset($_GET['id']) ? limpiarCadena($_GET['id']) : '';
    if($id === ''){
        echo json_encode(['ok'=>false,'error'=>'Falta id']);
        exit();
    }

    $hasEmpresa = _table_has_column('caja_tipopago_persona','idempresa');
    $hasEstado  = _table_has_column('caja_tipopago_persona','estado');
    $hasCuotas  = _table_has_column('caja_tipopago_persona','cuotas');
    $hasDias    = _table_has_column('caja_tipopago_persona','dias');

    $sql = "SELECT id";
    if($hasCuotas){ $sql .= ", cuotas"; }
    if($hasDias){ $sql .= ", dias"; }
    $sql .= " FROM caja_tipopago_persona WHERE id='$id'";
    if($hasEmpresa){ $sql .= " AND idempresa='$idempresa'"; }
    if($hasEstado){ $sql .= " AND estado='1'"; }
    $sql .= " LIMIT 1";

    $row = ejecutarConsultaSimpleFila($sql);
    if(!$row || !isset($row['id'])){
        echo json_encode(['ok'=>false,'error'=>'Registro no encontrado']);
        exit();
    }

    $cuotasCfg = ($hasCuotas && isset($row['cuotas'])) ? (int)$row['cuotas'] : 0;
    $diasCfg   = ($hasDias && isset($row['dias'])) ? (int)$row['dias'] : 0;

    echo json_encode(['ok'=>true,'cuotas'=>$cuotasCfg,'dias'=>$diasCfg]);
    exit();
break;

// ============================================================
// Mapeo F.Pago/M.Pago -> autoselección de Med/pago y Forma/pago
// Relación: caja_tipopago_persona.id_pago -> caja_tipopago.id
// Respuesta: {ok:true, id_pago, forma_descripcion, pagoforma}
// ============================================================
case 'map_tipopago_persona':
    header('Content-Type: application/json; charset=utf-8');
    $idempresa = _ctx_idempresa();
    if($idempresa === '0'){
        echo json_encode(['ok'=>false,'error'=>'Empresa no válida']);
        exit();
    }

    $id = isset($_GET['id']) ? limpiarCadena($_GET['id']) : '';
    if($id === ''){
        echo json_encode(['ok'=>false,'error'=>'Falta id']);
        exit();
    }

    // Validaciones de columnas para evitar fallos si la BD difiere
    $tppHasEmpresa = _table_has_column('caja_tipopago_persona','idempresa');
    $tppHasEstado  = _table_has_column('caja_tipopago_persona','estado');
    $tppHasIdPago   = _table_has_column('caja_tipopago_persona','id_pago');
    $tppHasCuotas  = _table_has_column('caja_tipopago_persona','cuotas');
    $tppHasDias    = _table_has_column('caja_tipopago_persona','dias');

    if(!$tppHasIdPago){
        echo json_encode(['ok'=>false,'error'=>'caja_tipopago_persona.id_pago no existe']);
        exit();
    }

    $tpHasEmpresa = _table_has_column('caja_tipopago','idempresa');
    $tpHasEstado  = _table_has_column('caja_tipopago','estado');
    $tpHasForma   = _table_has_column('caja_tipopago','pagoforma');

    // 1) Obtener id_pago desde caja_tipopago_persona (solo empresa logueada)
    $sql = "SELECT id, id_pago";
    if($tppHasCuotas){ $sql .= ", cuotas"; }
    if($tppHasDias){ $sql .= ", dias"; }
    $sql .= " FROM caja_tipopago_persona WHERE id='$id'";
    if($tppHasEmpresa){ $sql .= " AND idempresa='$idempresa'"; }
    if($tppHasEstado){ $sql .= " AND estado='1'"; }
    $sql .= " LIMIT 1";
    $row = ejecutarConsultaSimpleFila($sql);
    if(!$row || !isset($row['id'])){
        echo json_encode(['ok'=>false,'error'=>'Registro no encontrado']);
        exit();
    }

    $idPago = isset($row['id_pago']) ? trim((string)$row['id_pago']) : '';
    $cuotasCfg = ($tppHasCuotas && isset($row['cuotas'])) ? (int)$row['cuotas'] : 0;
    $diasCfg   = ($tppHasDias && isset($row['dias'])) ? (int)$row['dias'] : 0;

    if($idPago === ''){
        echo json_encode(['ok'=>true,'id_pago'=>'','forma_descripcion'=>'','pagoforma'=>'','cuotas'=>$cuotasCfg,'dias'=>$diasCfg]);
        exit();
    }

    // 2) Traer descripcion y pagoforma del método (caja_tipopago)
    $sql2 = "SELECT id, descripcion";
    if($tpHasForma){ $sql2 .= ", pagoforma"; }
    $sql2 .= " FROM caja_tipopago WHERE id='$idPago'";
    if($tpHasEmpresa){ $sql2 .= " AND idempresa='$idempresa'"; }
    if($tpHasEstado){ $sql2 .= " AND estado='1'"; }
    $sql2 .= " LIMIT 1";
    $tp = ejecutarConsultaSimpleFila($sql2);

    $out = [
        'ok' => true,
        'id_pago' => (string)$idPago,
        'forma_descripcion' => $tp && isset($tp['descripcion']) ? (string)$tp['descripcion'] : '',
        'pagoforma' => ($tpHasForma && $tp && isset($tp['pagoforma'])) ? (string)$tp['pagoforma'] : '',
        'cuotas' => $cuotasCfg,
        'dias' => $diasCfg
    ];
    echo json_encode($out);
    exit();
break;
// ============================================================
// Select2: Med/pago (caja_tipopago)
// ============================================================
case 'select_tipopago':
    header('Content-Type: application/json; charset=utf-8');
    $idempresa = _ctx_idempresa();

    // Solo empresa logueada
    if($idempresa === '0'){
        echo json_encode([]);
        exit();
    }

    $term = isset($_GET['searchTerm']) ? limpiarCadena($_GET['searchTerm']) : '';

    $hasEmpresa = _table_has_column('caja_tipopago','idempresa');
    $hasEstado  = _table_has_column('caja_tipopago','estado');

    $sql = "SELECT id AS id, descripcion AS text FROM caja_tipopago WHERE 1=1";
    if($hasEmpresa){ $sql .= " AND idempresa='$idempresa'"; }
    if($hasEstado){ $sql .= " AND estado='1'"; }
    if($term !== ''){ $sql .= " AND descripcion LIKE '%".str_replace("'","",$term)."%'"; }
    $sql .= " ORDER BY descripcion ASC LIMIT 100";

    $rs = ejecutarConsulta($sql);
    $out = [];
    if($rs){
        while($r = $rs->fetch_assoc()) {
            $out[] = ['id'=>(string)$r['id'], 'text'=>(string)$r['text']];
        }
    }

    echo json_encode($out);
    exit();
break;
case 'selectCliente':

		echo '<option value="0" selected >-SELECCIONE CLIENTE-</option>';

		$tipodoc=$_GET['tipodoc'];	
		
if($tipodoc=='OTROS'){
	$sql="SELECT * FROM persona WHERE tipo_persona='Cliente' AND idempresa='$_COOKIE[id]' ";
	$rspta = ejecutarConsulta($sql);
	}else{
	$sql="SELECT * FROM persona WHERE tipo_persona='Cliente' AND idempresa='$_COOKIE[id]' AND (tipo_documento='$tipodoc' OR tipo_documento='NO DOMICILIADO' OR tipo_documento='4' OR tipo_documento='7') ";
	$rspta = ejecutarConsulta($sql);
	}

		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->idpersona . '>' . $reg->txtID_CLIENTE. ' - ' . $reg->nombre . '</option>';
				}
	break;
		
		
case 'listarcliente':
$tipodoc=$_GET['tipodoc'];
		if($tipodoc=='OTROS'){
		$sql="SELECT * FROM persona WHERE tipo_persona='Cliente' AND idempresa='$_COOKIE[id]' ORDER BY txtID_CLIENTE ASC ";
$rspta =ejecutarConsulta($sql);
			
		}else{
			$sql="SELECT * FROM persona WHERE tipo_persona='Cliente' AND idempresa='$_COOKIE[id]' AND tipo_documento='$tipodoc' ORDER BY txtID_CLIENTE ASC  ";
$rspta =ejecutarConsulta($sql);
			
		}
		
		
		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->idpersona . '>' . $reg->txtID_CLIENTE. ' - ' . $reg->nombre . '</option>';
				}
	break;
	
	
case 'listarservicio':

		
echo '<option value="" >-SELECCIONE-</option>';
## Fetch records
$empQuery = "select * from articulo WHERE medida='ZZ' AND idempresa='$_COOKIE[id]' ";
$empRecords = ejecutarConsulta($empQuery);

while ($reg=$empRecords->fetch_object()){

$texto = str_replace($no_permitidas, $permitidas, $reg->txtDESCRIPCION_ARTICULO);


echo '<option value="'.$reg->medida.'|'.$reg->precio.'|'.$reg->comision.'|'.$reg->txtCOD_ARTICULO.'|'.$reg->codigo.'|'.$reg->txtDESCRIPCION_ARTICULO.'">'.$reg->codigo. ' - '.$reg->txtDESCRIPCION_ARTICULO.'</option>';
	
	
	
 		}
		
		

break;
		
		
case 'ClienteRep':
		require_once "../modelos/Persona.php";
		$persona = new Persona();
		$rspta = $persona->listard($tipodoc);
		echo '<option value="">SELECCIONE</option>';
		
		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
				}
	break;
		
		
		
		
case 'usuario':
    
if ($_COOKIE['administrador']==1){
		
$sql="SELECT * FROM usuario WHERE idlocal='$_COOKIE[idlocal]' and idempresa='$_COOKIE[id]' ORDER BY idusuario DESC ";
$rspta=ejecutarConsulta($sql);
		
		echo '<option value="">SELECCIONE</option>';
		
		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->idusuario . '>' . $reg->nombre . '</option>';
				}
	break;
}
else{
    
   $sql="SELECT * FROM usuario WHERE idusuario='$_COOKIE[idusuario]' and idlocal='$_COOKIE[idlocal]' and idempresa='$_COOKIE[id]' ORDER BY idusuario DESC ";
$rspta=ejecutarConsulta($sql);
		
		echo '<option value="">SELECCIONE</option>';
		
		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->idusuario . '>' . $reg->nombre . '</option>';
				}
	break; 
    
    
}
	
		
case 'usuariocomision':
		
$sql="SELECT * FROM usuario WHERE idempresa='$_COOKIE[id]' ORDER BY idusuario DESC ";
$rspta=ejecutarConsulta($sql);
		
		echo '<option value="">SELECCIONE</option>';
		
		while ($reg = $rspta->fetch_object())
				{
				echo '<option value="'.$reg->idusuario.'|'.$reg->comisions.'" >' . $reg->nombre . '('.$reg->comisions.'%)</option>';
				}
	break;		
		
case 'local':
		require_once "../modelos/Persona.php";
		$persona = new Persona();
		$rspta = $persona->listarlo();
		echo '<option value="">-TODOS-</option>';
	
		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->id. '>' . $reg->sucursal.'</option>';
				}
	break;
		
		
	case 'selectClienteDoc':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarC();

		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->txtID_CLIENTE . '>' . $reg->txtID_CLIENTE . '</option>';
				}
	break;
	case 'selectClienteRazon':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarC();

		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->txtID_CLIENTE . '>' . $reg->nombre . '</option>';
				}
	break;
	case 'selectClienteDireccion':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarC();

		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->txtID_CLIENTE . '>' . $reg->nombre . '</option>';
				}
	break;

	case 'selectArticulo':
		require_once "../modelos/Articulo.php";
		$articulo = new Articulo();

		$rspta = $articulo->listarA();

		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->txtCOD_ARTICULO . '>' . $reg->txtDESCRIPCION_ARTICULO . '</option>';
				}
	break;

case 'listarArticulosVenta':
		require_once "../modelos/Articulo.php";
		$articulo=new Articulo();

		$rspta=$articulo->listarActivosVenta();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
			
$texto = str_replace($no_permitidas, $permitidas, $reg->txtDESCRIPCION_ARTICULO);
			
$data[]=array(
"0"=>'<button class="btn btn-warning btn-xs" onclick="agregarDetalle('.$reg->txtCOD_ARTICULO.',\''.$texto.'\',\''.$reg->txtPRECIO_ARTICULO.'\')"><span class="fa fa-plus"></span></button>',
 				"1"=>$reg->categoria,
 				"2"=>$reg->codigo,
 				"3"=>$texto,
 				"4"=>$reg->stock.'--',
 				"5"=>$reg->txtPRECIO_ARTICULO,
 				"6"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px' >"
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);
	break;
		
			

		
		
case 'listarArticulosvr':
		require_once "../modelos/Articulo.php";
		$articulo=new Articulo();

		$rspta=$articulo->listarActivos();
 		//Vamos a declarar un array
 		$data= Array();
		
while ($reg=$rspta->fetch_object()){

if(!empty($tipoDocFiltroReq) && !isset($tipoDocFiltroReq[(string)$reg->txtID_TIPO_DOCUMENTO])){ continue; }

$sql="SELECT *FROM detalle_ingreso WHERE txtCOD_ARTICULO='$reg->txtCOD_ARTICULO' ORDER BY iddetalle_ingreso DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);
$precio=$mostrar['precio_venta'];
	
$sql="SELECT * FROM unidad_medida WHERE id='$reg->medida'";
$mostrar=ejecutarConsultaSimpleFila($sql);
	
$exonerado='<input type="hidden" id="ex'.$reg->txtCOD_ARTICULO.'"  name="ex'.$reg->txtCOD_ARTICULO.'" value="'.$reg->exonerado_igv.'" >';
	
$mayor='<input type="hidden" id="may'.$reg->txtCOD_ARTICULO.'"  name="may'.$reg->txtCOD_ARTICULO.'" value="'.$reg->mayor.'" >';
	
$imei='<select id="st'.$reg->txtCOD_ARTICULO.'" name="st'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 140px; padding: 1px; margin: 1px;  height: 22px; " />';
	
$datos = "SELECT *FROM articulo_serie WHERE cod_articulo='$reg->txtCOD_ARTICULO' AND estado='1' ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$imei.='<option value="'.$dato['id'].'">'.$dato['serie'].'</option>';
}
$imei.='</select>';
	
	
$texto = str_replace($no_permitidas, $permitidas, $reg->txtDESCRIPCION_ARTICULO);	
	
$tit='<input id="t'.$reg->txtCOD_ARTICULO.'" class="form-control"  style="width: 200px; padding: 1px; margin: 1px;  height: 22px; " name="t'.$reg->txtCOD_ARTICULO.'" value="'.$texto.'" type="text">
<div style="display: none">'.$texto.'</div>';
	
$data[]=array(
"0"=>'<button class="btn btn-warning btn-xs" onclick="addtabla(\''.$reg->txtCOD_ARTICULO.'\', \''.$reg->codigo.'\', \''.$texto.'\', \''.$mostrar['tit'].'\')"><span class="fa fa-plus"></span></button>',
"1"=>'<input type="checkbox" id="gr'.$reg->txtCOD_ARTICULO.'"  name="gr'.$reg->txtCOD_ARTICULO.'">',	
"2"=>$reg->codigo.' '.$exonerado.' '.$mayor,
"3"=>$tit,
"4"=>'<input id="p'.$reg->txtCOD_ARTICULO.'" class="form-control" style="width: 140px; padding: 1px; margin: 1px;  height: 22px; " name="p'.$reg->txtCOD_ARTICULO.'" value="'.$reg->precio.'" type="text">',
"5"=>'<input id="pm'.$reg->txtCOD_ARTICULO.'" class="form-control"  style="padding: 1px; margin: 1px;  height: 22px; " name="pm'.$reg->txtCOD_ARTICULO.'" value="'.$reg->precio_mayor.'" type="text">',
"6"=>"<input type='text' class='form-control' style='width: 40px; padding: 1px; margin: 1px;  height: 22px; ' value='1' name='cti".$reg->txtCOD_ARTICULO."' id='cti".$reg->txtCOD_ARTICULO."' />",
"7"=>$reg->stock,
"8"=>$imei
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);
	break;
		
		

case 'numero':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	
	$num=0;	
$tipo=$_GET['tipo'];
$idcliente=$_GET['idcliente'];
		
if(isset($_COOKIE["idlocal"])){ $idlocal=$_COOKIE["idlocal"]; }else{ $idlocal='1'; }

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$sector='0';
		
$jsondata['seriedoc'] = $_GET['seriedoc'];
$jsondata['idcliente'] =$idcliente;	
		
if($_GET['seriedoc']!=''){ 
$seriedoc=$_GET['seriedoc']; 
}else{
	
$jsondata['sector'] = $sector;	
	
$sqls="SELECT *FROM series WHERE documento='$tipo' AND idlocal='$idlocal' AND idempresa='$_COOKIE[id]' AND estado='1' ORDER BY id DESC ";

$s= ejecutarConsultaSimpleFila($sqls);	
$seriedoc='';
if($s){
$seriedoc=$s['serie'];
}
}
		
$sql="SELECT *FROM venta WHERE txtSERIE='$seriedoc' AND txtID_TIPO_DOCUMENTO='$tipo' AND idlocal='$idlocal' AND idempresa='$_COOKIE[id]' AND beta='$fa[tipo]'  ORDER BY txtNUMERO DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);

if($mostrar){
$num=trim($mostrar['txtNUMERO'])+1;	
}else if(isset($s)){
$num=$s['numeroinicio'];
}else{
$num='00000001';	
}
		
$num=str_pad($num, 8, "0", STR_PAD_LEFT);

$jsondata['estado'] = '1';
$jsondata['serie'] =$seriedoc;
$jsondata['numero'] = $num;
$jsondata['tipo'] = $tipo;
$jsondata['idlocal'] = $idlocal;
$jsondata['id'] = $_COOKIE['id'];
$jsondata['mensaje'] = 'TODO BIEN!';

echo json_encode($jsondata);
exit();	
		
		
break;
		
		
case 'impresion':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
	$serie=$_GET['serie'];
	$numero=$_GET['numero'];
		$tipo=$_GET['tipo'];
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	

$sql3="SELECT *FROM config WHERE estado='1' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);
		
if(esModoBetaSunatVenta(isset($mempresa['tipo']) ? $mempresa['tipo'] : '3')){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

$ruta="../api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero=$mempresa['ruc'].'-'.$tipo.'-'.$serie.'-'.$numero.'.pdf';
		
$ruta=$ruta.$fichero;
		
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $ruta;	
		
echo json_encode($jsondata);
exit();	
		
		
break;
		
		
		
		
		
case 'enviaSunat':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
		
enviarfactura($idventa);
			
break;
		
		
case 'anular':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
$new = new api_sunat();
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR AL ANULAR';	
				
$sql2="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql2);	
		
$sqlper="SELECT *FROM venta WHERE idventa='$idventa' ";
$percep= ejecutarConsultaSimpleFila($sqlper);

if($percep['referencia']!=''){	

$porciones = explode("-", $percep['referencia']);
$serie=$porciones[0]; // porción1
if(isset($porciones[1])){	
$numero=$porciones[1]; // porción2
$sql="UPDATE venta SET estado='0' WHERE txtID_TIPO_DOCUMENTO='$percep[doc_relaciona]' AND idempresa='$_COOKIE[id]' AND txtSERIE='$serie' AND txtNUMERO='$numero' ";
ejecutarConsulta($sql);	
}
}

$fecha=date("Y-m-d");
$serie=date("Ymd");

if($percep['txtID_TIPO_DOCUMENTO']=='01'||$percep['docmodifica_tipo']=='01'){ 
$fechadocf='FECHA_BAJA';
$tipo='4';
$codigo='RA';
}else{
$fechadocf='FECHA_DOCUMENTO'; 
$tipo='4';
$codigo='RC';
}
		
//echo $codigo.'|';

$sqlde="SELECT *FROM resumen WHERE iddocumento='$idventa' AND tipo='$tipo'  ";
$docech= ejecutarConsultaSimpleFila($sqlde);
		
$jsondata['idventa'] =$idventa;

//if(!$docech){

$sqlf="SELECT *FROM resumen WHERE serie='$serie' AND idempresa='$_COOKIE[id]' AND tipo='$tipo' ORDER BY id DESC ";
$mostrarf= ejecutarConsultaSimpleFila($sqlf);
$numero=$mostrarf['numero']+1;

$jsondata['numero1']=$numero;
	
if($percep['txtID_TIPO_DOCUMENTO']=='07'){
if($percep['modifica_motivo']=='01'||$percep['modifica_motivo']=='02'){
$sql="UPDATE cardex_detalle SET estado='1' WHERE iddocumento='$idventa' AND operacion='01' AND tipooperacion='2' AND nivel='2' ";
ejecutarConsulta($sql);
}
}else{
$sql="UPDATE cardex_detalle SET estado='1' WHERE iddocumento='$idventa' AND operacion='00' AND tipooperacion='1' AND nivel='2' ";
ejecutarConsulta($sql);	
}
	
$sql="SELECT *FROM detalle_venta WHERE idventa='$idventa' ";
$rspta=ejecutarConsulta($sql);
while ($reg = $rspta->fetch_object()){	
	
if($percep['txtID_TIPO_DOCUMENTO']=='07'){
$sql="UPDATE articulo_stock SET idlocal='$reg->idlocal', stock=stock-'$reg->txtCANTIDAD_ARTICULO' WHERE idarticulo='$reg->idproducto' ";
ejecutarConsulta($sql);
}else{
$sql="UPDATE articulo_stock SET idlocal='$reg->idlocal', stock=stock+'$reg->txtCANTIDAD_ARTICULO' WHERE idarticulo='$reg->idproducto' ";
ejecutarConsulta($sql);	
}
	
}
	
/*	
}else{
$idbaja=$docech['id'];
$serie=	$docech['serie'];
$numero=$docech['numero'];
$jsondata['numero2'] =$numero;
}
*/

		
		

$fechadoc=date("Y-m-d", strtotime($percep['txtFECHA_DOCUMENTO']));
		
$sql="SELECT *FROM persona WHERE idpersona='$percep[txtID_CLIENTE]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);	
if($mostrar['tipo_documento']=='DNI'){ $tdoc='1'; }else if($mostrar['tipo_documento']=='RUC'){ $tdoc='6'; }

$json['ITEM'] ='1';
$json["TIPO_COMPROBANTE"] = $percep['txtID_TIPO_DOCUMENTO'];
		
if($percep['txtID_TIPO_DOCUMENTO']=='01'||$percep['docmodifica_tipo']=='01'){
$json["SERIE"] = $percep['txtSERIE'];
$json["NUMERO"] = $percep['txtNUMERO'];
$json["DESCRIPCION"] = "ERROR DE DIGITACION";	
}else{
$json['NRO_COMPROBANTE'] = $percep['txtSERIE']."-".$percep['txtNUMERO'];
$json['TIPO_DOCUMENTO'] = $tdoc;
$json['NRO_DOCUMENTO'] = $mostrar['txtID_CLIENTE'];
$json['TIPO_COMPROBANTE_REF'] = $percep['docmodifica_tipo'];
$json['NRO_COMPROBANTE_REF'] = $percep['docmodifica'];

$json['STATU'] = '3';//1=ENVIO DE BOLETAS NORMALES, 3=ENVIO DE BOLETAS ANULADAS 
$json['COD_MONEDA'] = $percep['txtID_MONEDA'];
$json['TOTAL'] = $percep['txtTOTAL'];
$json['GRAVADA'] = $percep['txtSUB_TOTAL'];
	$json['ISC'] = "0";
	$json['IGV'] = $percep['txtIGV'];
	$json['OTROS'] = "0";
	$json['CARGO_X_ASIGNACION'] = "1";
	$json['MONTO_CARGO_X_ASIG'] = "0";
	$json['EXONERADO'] = $percep['exonerado'];
	$json['INAFECTO'] = "0";
	$json['EXPORTACION'] = "0";
	$json['GRATUITAS'] = $percep['gratuita'];

}

$detalle[]=$json;	
		
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
	
//var_dump(json_encode($data));

if(!$docech){		
$sql="INSERT INTO resumen (tipo, iddocumento, idempresa, codigo, serie, numero, estado, hash, hash_cdr, mensaje, ticket, fecha_documento, fecha)
VALUES ('$tipo', '$idventa', '$_COOKIE[id]', '$codigo', '$serie', '$numero', '0', '', '', '', '', '$fechadoc', '$fecha')";
$idbaja=ejecutarConsulta_retornarID($sql);
}

if($percep['txtID_TIPO_DOCUMENTO']=='01'||$percep['docmodifica_tipo']=='01'){ 
$resultado = $new->sendbaja(json_encode($data), RUTA);
}else{
$resultado = $new->sendresumen(json_encode($data), RUTA);
}

$me = json_decode($resultado, true);	
//var_dump(json_encode($me));			
		
if($me['cod_sunat']=='0'){
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El documento fue enviado a la sunat';
	
$estado='5';
	
$sql="UPDATE venta SET estado='$estado' WHERE idventa='$idventa' ";
ejecutarConsulta($sql);
	
$sql="UPDATE resumen SET estado='$estado', ticket='$me[msj_sunat]', hash_cdr='$me[hash_cdr]' WHERE id='$idbaja' ";
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
//var_dump(json_encode($data2));
$resultado2 = $new->sendticket(json_encode($data2), RUTA);
$me = json_decode($resultado2, true);
//var_dump($me);
	
if($me['cod_sunat']=='0'){
	
$estado='6';
	
$sql="UPDATE resumen SET estado='$estado', mensaje='$me[msj_sunat]', hash_cdr='$me[hash_cdr]' WHERE idventa='$idbaja' ";
ejecutarConsulta($sql);
	
$sql="UPDATE venta SET estado='$estado' WHERE idventa='$idventa' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $me['msj_sunat'].'. '.$me['cod_sunat'];	
	
}
	
}else{

$jsondata['estado'] = '0';
$jsondata['mensaje'] =$me['msj_sunat'].'. '.$me['cod_sunat'];
	
}

echo json_encode($jsondata);
exit();	
		
break;

		
case 'leerticket':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
$new = new api_sunat();
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR AL ANULAR';	
		
$tipo='5';
$codigo='RR';

$sqlde="SELECT *FROM resumen WHERE iddocumento='$idventa' AND tipo='$tipo' ";
$docech= ejecutarConsultaSimpleFila($sqlde);

$idbaja=$docech['id'];
$serie=	$docech['serie'];
$numero=$docech['numero'];
	
$sql2="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql2);	
		
$sqlper="SELECT *FROM venta WHERE idventa='$idventa' ";
$percep= ejecutarConsultaSimpleFila($sqlper);
		
$fecha=date("Y-m-d");
$fechadoc=date("Y-m-d", strtotime($percep['txtFECHA_DOCUMENTO']));
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El documento fue enviado a la sunat';
	
$data2 = array(
"TIPO_PROCESO"=>$fa['tipo'],
"NRO_DOCUMENTO_EMPRESA"=>$fa['ruc'],
"USUARIO_SOL_EMPRESA"=>$fa['usuario'],
"PASS_SOL_EMPRESA"=>$fa['clave'],
"SUNAT"=> $fa['sunat'],
"TICKET"=>$docech['ticket'],
"TIPO_DOCUMENTO"=>$codigo, 
"NRO_DOCUMENTO"=>$serie.'-'.$numero,
);
//var_dump(json_encode($data2));
$resultado2 = $new->sendticket(json_encode($data2), RUTA);
$me = json_decode($resultado2, true);
//var_dump($me);
	
if($me['cod_sunat']=='0'){
	
$estado='6';
	
$sql="UPDATE resumen SET estado='$estado', mensaje='$me[msj_sunat]', hash_cdr='$me[hash_cdr]' WHERE id='$idbaja' ";
ejecutarConsulta($sql);
	
$sql="UPDATE venta SET estado='$estado' WHERE idventa='$idventa' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $me['msj_sunat'].'. '.$me['cod_sunat'];	
	
}

echo json_encode($jsondata);
exit();	
		
		
	break;
		
case 'anularrecibo':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR AL ANULAR';	
		
$rspta=$venta->anularrecibo($idventa);
		
$sql3="SELECT *FROM venta WHERE idventa='$idventa' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
if($fa['tipo_pago']=='CONTADO'&&$fa['medio_pago']=='008'){	
agregarcaja($fa['txtTOTAL'], 'RESTA', 'ENTRADA', $fa['idusuario']);
}

$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'DOCUMENTO ANULADO';	

echo json_encode($jsondata);
exit();	
		
break;


    case 'Pasapedido':

        $jsondata = array();
        $new = new api_sunat();

        header("HTTP/1.1");
        header("Content-Type: application/json; charset=UTF-8");

        $jsondata = array();

        $jsondata['estado'] = '0';
        $jsondata['mensaje'] = "Error general";

        $idlocal=$_COOKIE["idlocal"];
        $tipo=$_GET["tipoproceso"];

        $sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
        $fa= ejecutarConsultaSimpleFila($sql3);

        $sql="SELECT *FROM venta WHERE idventa='$idventa' ";
        $v= ejecutarConsultaSimpleFila($sql);

        $jsondata['tipodoc'] = $v['txtID_TIPO_DOCUMENTO'];

        if($tipo=='1'){

            $sqls="SELECT *FROM series WHERE documento='07' AND idlocal='$_COOKIE[idlocal]' AND idempresa='$_COOKIE[id]'  ORDER BY id DESC ";
            $s= ejecutarConsultaSimpleFila($sqls);
            $rest = substr($s['serie'], -2);
            $seriedoc='NP'.$rest;

            $sql="SELECT *FROM venta WHERE txtSERIE='$seriedoc' AND idlocal='$_COOKIE[idlocal]' AND idempresa='$_COOKIE[id]' ORDER BY idventa DESC ";
            $mostrar= ejecutarConsultaSimpleFila($sql);

            $num=$mostrar['txtNUMERO']+1;
            $num=str_pad($num, 8, "0", STR_PAD_LEFT);

            $jsondata['serie'] = $seriedoc;
            $jsondata['numero'] = $num;

            $tdoc='92';
            $pedido='1';

            $numrelaciona=$v['doc_relaciona'];

        }else{

            $jsondata['docrelaciona'] =$v['doc_relaciona'];

            $sqls="SELECT *FROM series WHERE documento='$v[doc_relaciona]' AND idlocal='$idlocal' ORDER BY id DESC ";
            $s= ejecutarConsultaSimpleFila($sqls);

            $seriedoc=$s['serie'];

            $sql="SELECT *FROM venta WHERE txtSERIE='$seriedoc' AND idempresa='$_COOKIE[id]'  AND beta='$fa[tipo]' ORDER BY txtNUMERO DESC ";
            $mostrar= ejecutarConsultaSimpleFila($sql);

            if($mostrar){ $num=$mostrar['txtNUMERO']+1; }else{ $num=$s['numeroinicio']; }
            $num=str_pad($num, 8, "0", STR_PAD_LEFT);

            $tdoc=$v['doc_relaciona'];
            $pedido='0';

            if($v['tipo_pago']=='CONTADO'&&$v['medio_pago']=='008'){

                $txtTOTAL=$v['txtTOTAL']-$v['tarjeta'];
                agregarcaja($txtTOTAL, 'SUMA', 'ENTRADA', $_COOKIE['idusuario']);

            }

            $numrelaciona = '91';

        }

        $docrelaciona = $v['txtSERIE'] . '-' . $v['txtNUMERO'];

        $jsondata['serie'] = $s['serie'];
        $jsondata['numero'] = $num;
        /*
(pedido, sector, txtID_CLIENTE, idusuario, idlocal, doc_relaciona, txtID_TIPO_DOCUMENTO, txtSERIE, txtNUMERO, txtFECHA_DOCUMENTO, txtOBSERVACION, txtSUB_TOTAL, txtIGV, txtTOTAL, gratuita, exonerado, comision, txtID_MONEDA, tipo_pago, estado, docmodifica_tipo, docmodifica, modifica_motivo, modifica_motivod, hash_cpe, hash_cdr, mensaje)
	*/

	        $sqlCols = "idventa, idempresa, beta, pedido, sector, txtID_CLIENTE, idusuario, idlocal, txtID_TIPO_DOCUMENTO, doc_relaciona, docmodifica_tipo, docmodifica, modifica_motivo, modifica_motivod, txtSERIE, txtNUMERO, txtFECHA_DOCUMENTO, fecha_vto, txtOBSERVACION, txtSUB_TOTAL, txtIGV, ICB, descuento, dto_global_monto, dto_global_tipo, dto_global_valor, dto_global_monto_base, dto_global_monto_igv, dto_global_aplica_antes_igv, total_sunat, total_a_pagar, txtTOTAL, gratuita, exonerado, comision, tarjeta, txtID_MONEDA, tipo_pago, medio_pago, orden, tipoguia, guia, presupuesto, referencia, condiciones, hash_cpe, hash_cdr, mensaje, estado, estadopago, retencion, iddetraccion, detraccion, tipocambio,inafecta, percepcion, kardex, referencial, controlpresupuestal, tipoguia2, guia2, tipoguia3, guia3, tipoguia4, guia4, tipoguia5, guia5, idcaja, exportacion, fpago_mpago";
	        $sqlVals = "NULL, idempresa, beta, '$idventa', sector, txtID_CLIENTE, idusuario, idlocal, '$tdoc', txtID_TIPO_DOCUMENTO, docmodifica_tipo, docmodifica, modifica_motivo, modifica_motivod, '$seriedoc', '$num', NOW(), NOW(), txtOBSERVACION, txtSUB_TOTAL, txtIGV, ICB, descuento, dto_global_monto, dto_global_tipo, dto_global_valor, dto_global_monto_base, dto_global_monto_igv, dto_global_aplica_antes_igv, total_sunat, total_a_pagar, txtTOTAL, gratuita, exonerado, comision, tarjeta, txtID_MONEDA, tipo_pago, medio_pago, orden, tipoguia, guia, presupuesto, '$docrelaciona', condiciones, hash_cpe, hash_cdr, mensaje, '0', estadopago, retencion, iddetraccion, detraccion, tipocambio, inafecta, percepcion, '0', referencial, controlpresupuestal, tipoguia2, guia2, tipoguia3, guia3, tipoguia4, guia4, tipoguia5, guia5, idcaja, exportacion, COALESCE(fpago_mpago, guia5)";
	        if(_table_has_column('venta','dto_global_modo')){ $sqlCols .= ", dto_global_modo"; $sqlVals .= ", COALESCE(dto_global_modo,'MONTO')"; }
	        if(_table_has_column('venta','dto_global_afecta_base')){ $sqlCols .= ", dto_global_afecta_base"; $sqlVals .= ", COALESCE(dto_global_afecta_base,'1')"; }
	        if(_table_has_column('venta','dto_global_afecta_igv')){ $sqlCols .= ", dto_global_afecta_igv"; $sqlVals .= ", COALESCE(dto_global_afecta_igv,'1')"; }
	        if(_table_has_column('venta','dto_global_prorrateado')){ $sqlCols .= ", dto_global_prorrateado"; $sqlVals .= ", COALESCE(dto_global_prorrateado,'0')"; }
	        if(_table_has_column('venta','total_descuentos_item')){ $sqlCols .= ", total_descuentos_item"; $sqlVals .= ", COALESCE(total_descuentos_item,0)"; }
	        if(_table_has_column('venta','total_descuentos_global')){ $sqlCols .= ", total_descuentos_global"; $sqlVals .= ", COALESCE(total_descuentos_global,0)"; }
	        if(_table_has_column('venta','total_descuentos_prorrateados')){ $sqlCols .= ", total_descuentos_prorrateados"; $sqlVals .= ", COALESCE(total_descuentos_prorrateados,0)"; }
	        if(_table_has_column('venta','total_descuentos_base')){ $sqlCols .= ", total_descuentos_base"; $sqlVals .= ", COALESCE(total_descuentos_base,0)"; }
	        if(_table_has_column('venta','total_descuentos_igv')){ $sqlCols .= ", total_descuentos_igv"; $sqlVals .= ", COALESCE(total_descuentos_igv,0)"; }
	        if(_table_has_column('venta','total_descuentos_no_base')){ $sqlCols .= ", total_descuentos_no_base"; $sqlVals .= ", COALESCE(total_descuentos_no_base,0)"; }
	        if(_table_has_column('venta','total_valor_venta_bruto')){ $sqlCols .= ", total_valor_venta_bruto"; $sqlVals .= ", COALESCE(total_valor_venta_bruto,0)"; }
	        if(_table_has_column('venta','total_valor_venta_neto')){ $sqlCols .= ", total_valor_venta_neto"; $sqlVals .= ", COALESCE(total_valor_venta_neto,0)"; }
	        if(_table_has_column('venta','total_igv_bruto')){ $sqlCols .= ", total_igv_bruto"; $sqlVals .= ", COALESCE(total_igv_bruto,0)"; }
	        if(_table_has_column('venta','total_igv_neto')){ $sqlCols .= ", total_igv_neto"; $sqlVals .= ", COALESCE(total_igv_neto,0)"; }
	        if(_table_has_column('venta','total_bruto_operacion')){ $sqlCols .= ", total_bruto_operacion"; $sqlVals .= ", COALESCE(total_bruto_operacion,0)"; }
	        if(_table_has_column('venta','total_neto_operacion')){ $sqlCols .= ", total_neto_operacion"; $sqlVals .= ", COALESCE(total_neto_operacion,0)"; }
	        $sql = "INSERT INTO venta (".$sqlCols.") SELECT ".$sqlVals." FROM venta WHERE idventa='$idventa' ";
        $idventanew = ejecutarConsulta_retornarID($sql);

        $sqlp = "INSERT INTO caja_ventapago (id, idempresa, beta, idlocal, tipopago, otrospagos, nivel, idventa, idtipo, serie, moneda, montosoles, montodolares, tipocambio, operacion, fecha, fecha_pago, fechaoperacion, estado)
SELECT NULL, idempresa, beta, idlocal, tipopago, otrospagos, '0', '$idventanew', idtipo, serie, moneda, montosoles, montodolares, tipocambio, operacion, fecha, fecha_pago, fechaoperacion, estado FROM caja_ventapago WHERE idventa='$idventa' ";
        ejecutarConsulta($sqlp);


	        $n = 0;
	        $mapDetalleIds = array();
        /*
$rspta = $resumen->detfactura($idventa);
	*/
        $sqlu = "SELECT * FROM detalle_venta WHERE idventa='$idventa' ";
        $rspta = ejecutarConsulta($sqlu);

        while ($reg = $rspta->fetch_object()) {
            $n = $n + 1;

            $sql_detalle_cols = "iddetalle_venta,idempresa,idlocal,beta,idventa,tipoarticulo,idproducto,codigoproducto,unidadmedida,idpresentacion,nombreproducto,txtCANTIDAD_ARTICULO,cantidadp,precio,preciocompra,descuento,subtotal,igv,ICB,importe,exoneradod,inafectad,gratuitad,detracciond,comisiond,stock,tipo,anticipo,doc_anticipo,placa,idlote,idproveedor,iddestino,carga_util,cantidad_toneladas,fecha,idcaja,cod_afectacion_igv,cod_tributo,porc_igv,valor_unitario_ref,igv_ref,valor_total_ref,es_retiro,es_publicidad,es_bonificacion,es_gratuito,dto_item_monto,dto_item_tipo,dto_item_valor,dto_item_monto_base,dto_item_monto_igv";
            $sql_detalle_vals = "NULL, '$_COOKIE[id]', '$_COOKIE[idlocal]', '$reg->beta', '$idventanew', '$reg->tipoarticulo', '$reg->idproducto', '$reg->codigoproducto', '$reg->unidadmedida', '$reg->idpresentacion', '$reg->nombreproducto', '$reg->txtCANTIDAD_ARTICULO', '$reg->cantidadp', '$reg->precio', '$reg->preciocompra', '".(isset($reg->descuento)?$reg->descuento:(isset($reg->dto_item_monto)?$reg->dto_item_monto:'0.00'))."', '$reg->subtotal', '$reg->igv', '$reg->ICB', '$reg->importe', '$reg->exoneradod', '$reg->inafectad', '$reg->gratuitad', '$reg->detracciond', '$reg->comisiond', '$reg->stock', '$reg->tipo', '$reg->anticipo', '$reg->doc_anticipo', '$reg->placa', '$reg->idlote', '$reg->idproveedor', '$reg->iddestino', '$reg->carga_util', '$reg->cantidad_toneladas', '$reg->fecha', '$reg->idcaja', '".(isset($reg->cod_afectacion_igv)?$reg->cod_afectacion_igv:'10')."', '".(isset($reg->cod_tributo)?$reg->cod_tributo:'1000')."', '".(isset($reg->porc_igv)?$reg->porc_igv:'18.00')."', '".(isset($reg->valor_unitario_ref)?$reg->valor_unitario_ref:'0')."', '".(isset($reg->igv_ref)?$reg->igv_ref:'0')."', '".(isset($reg->valor_total_ref)?$reg->valor_total_ref:'0')."', '".(isset($reg->es_retiro)?$reg->es_retiro:'0')."', '".(isset($reg->es_publicidad)?$reg->es_publicidad:'0')."', '".(isset($reg->es_bonificacion)?$reg->es_bonificacion:'0')."', '".(isset($reg->es_gratuito)?$reg->es_gratuito:'0')."', '".(isset($reg->dto_item_monto)?$reg->dto_item_monto:(isset($reg->descuento)?$reg->descuento:'0.00'))."', '".(isset($reg->dto_item_tipo)?$reg->dto_item_tipo:'MONTO')."', '".(isset($reg->dto_item_valor)?$reg->dto_item_valor:'0.0000')."', '".(isset($reg->dto_item_monto_base)?$reg->dto_item_monto_base:'0.00')."', '".(isset($reg->dto_item_monto_igv)?$reg->dto_item_monto_igv:'0.00')."'";

            if(_table_has_column('detalle_venta','idcatalogo_afectacion')){ $sql_detalle_cols .= ",idcatalogo_afectacion"; $sql_detalle_vals .= ",'".(isset($reg->idcatalogo_afectacion)?$reg->idcatalogo_afectacion:'0')."'"; }
            if(_table_has_column('detalle_venta','tipo_cambio')){ $sql_detalle_cols .= ",tipo_cambio"; $sql_detalle_vals .= ",'".(isset($reg->tipo_cambio)?$reg->tipo_cambio:(isset($v['tipocambio'])?$v['tipocambio']:'1'))."'"; }
            if(_table_has_column('detalle_venta','moneda')){ $sql_detalle_cols .= ",moneda"; $sql_detalle_vals .= ",'".(isset($reg->moneda)?$reg->moneda:(isset($v['txtID_MONEDA'])?$v['txtID_MONEDA']:'PEN'))."'"; }
            if(_table_has_column('detalle_venta','version_ubl')){ $sql_detalle_cols .= ",version_ubl"; $sql_detalle_vals .= ",'".(isset($reg->version_ubl)?$reg->version_ubl:'2.1')."'"; }
            if(_table_has_column('detalle_venta','codigo_tipo_precio')){ $sql_detalle_cols .= ",codigo_tipo_precio"; $sql_detalle_vals .= ",'".(isset($reg->codigo_tipo_precio)?$reg->codigo_tipo_precio:'01')."'"; }
            if(_table_has_column('detalle_venta','precio_unitario_ref')){ $sql_detalle_cols .= ",precio_unitario_ref"; $sql_detalle_vals .= ",'".(isset($reg->precio_unitario_ref)?$reg->precio_unitario_ref:'0')."'"; }
            if(_table_has_column('detalle_venta','base_imponible_ref')){ $sql_detalle_cols .= ",base_imponible_ref"; $sql_detalle_vals .= ",'".(isset($reg->base_imponible_ref)?$reg->base_imponible_ref:'0')."'"; }
            if(_table_has_column('detalle_venta','base_imponible_xml')){ $sql_detalle_cols .= ",base_imponible_xml"; $sql_detalle_vals .= ",'".(isset($reg->base_imponible_xml)?$reg->base_imponible_xml:'0')."'"; }
            if(_table_has_column('detalle_venta','monto_tributo_xml')){ $sql_detalle_cols .= ",monto_tributo_xml"; $sql_detalle_vals .= ",'".(isset($reg->monto_tributo_xml)?$reg->monto_tributo_xml:'0')."'"; }
            if(_table_has_column('detalle_venta','valor_unitario_xml')){ $sql_detalle_cols .= ",valor_unitario_xml"; $sql_detalle_vals .= ",'".(isset($reg->valor_unitario_xml)?$reg->valor_unitario_xml:'0')."'"; }
            if(_table_has_column('detalle_venta','precio_unitario_xml')){ $sql_detalle_cols .= ",precio_unitario_xml"; $sql_detalle_vals .= ",'".(isset($reg->precio_unitario_xml)?$reg->precio_unitario_xml:'0')."'"; }
            if(_table_has_column('detalle_venta','valor_venta_xml')){ $sql_detalle_cols .= ",valor_venta_xml"; $sql_detalle_vals .= ",'".(isset($reg->valor_venta_xml)?$reg->valor_venta_xml:'0')."'"; }
            if(_table_has_column('detalle_venta','valor_subtotal_ref')){ $sql_detalle_cols .= ",valor_subtotal_ref"; $sql_detalle_vals .= ",'".(isset($reg->valor_subtotal_ref)?$reg->valor_subtotal_ref:'0')."'"; }
            if(_table_has_column('detalle_venta','codigo_leyenda')){ $sql_detalle_cols .= ",codigo_leyenda"; $sql_detalle_vals .= ",'".(isset($reg->codigo_leyenda)?$reg->codigo_leyenda:'')."'"; }
	            if(_table_has_column('detalle_venta','afecta_total_1004')){ $sql_detalle_cols .= ",afecta_total_1004"; $sql_detalle_vals .= ",'".(isset($reg->afecta_total_1004)?$reg->afecta_total_1004:'0')."'"; }
	            if(_table_has_column('detalle_venta','dto_item_modo')){ $sql_detalle_cols .= ",dto_item_modo"; $sql_detalle_vals .= ",'".(isset($reg->dto_item_modo)?$reg->dto_item_modo:((isset($reg->dto_item_tipo) && $reg->dto_item_tipo==='PCT')?'PORCENTAJE':'MONTO'))."'"; }
	            if(_table_has_column('detalle_venta','dto_item_afecta_base')){ $sql_detalle_cols .= ",dto_item_afecta_base"; $sql_detalle_vals .= ",'".(isset($reg->dto_item_afecta_base)?$reg->dto_item_afecta_base:'1')."'"; }
	            if(_table_has_column('detalle_venta','dto_item_afecta_igv')){ $sql_detalle_cols .= ",dto_item_afecta_igv"; $sql_detalle_vals .= ",'".(isset($reg->dto_item_afecta_igv)?$reg->dto_item_afecta_igv:'1')."'"; }
	            if(_table_has_column('detalle_venta','dto_global_prorrateado_monto')){ $sql_detalle_cols .= ",dto_global_prorrateado_monto"; $sql_detalle_vals .= ",'".(isset($reg->dto_global_prorrateado_monto)?$reg->dto_global_prorrateado_monto:'0')."'"; }
	            if(_table_has_column('detalle_venta','dto_global_prorrateado_base')){ $sql_detalle_cols .= ",dto_global_prorrateado_base"; $sql_detalle_vals .= ",'".(isset($reg->dto_global_prorrateado_base)?$reg->dto_global_prorrateado_base:'0')."'"; }
	            if(_table_has_column('detalle_venta','dto_global_prorrateado_igv')){ $sql_detalle_cols .= ",dto_global_prorrateado_igv"; $sql_detalle_vals .= ",'".(isset($reg->dto_global_prorrateado_igv)?$reg->dto_global_prorrateado_igv:'0')."'"; }
	            if(_table_has_column('detalle_venta','descuento_total_linea')){ $sql_detalle_cols .= ",descuento_total_linea"; $sql_detalle_vals .= ",'".(isset($reg->descuento_total_linea)?$reg->descuento_total_linea:(isset($reg->dto_item_monto)?$reg->dto_item_monto:(isset($reg->descuento)?$reg->descuento:'0')))."'"; }
	            if(_table_has_column('detalle_venta','descuento_total_base_linea')){ $sql_detalle_cols .= ",descuento_total_base_linea"; $sql_detalle_vals .= ",'".(isset($reg->descuento_total_base_linea)?$reg->descuento_total_base_linea:(isset($reg->dto_item_monto_base)?$reg->dto_item_monto_base:'0'))."'"; }
	            if(_table_has_column('detalle_venta','descuento_total_igv_linea')){ $sql_detalle_cols .= ",descuento_total_igv_linea"; $sql_detalle_vals .= ",'".(isset($reg->descuento_total_igv_linea)?$reg->descuento_total_igv_linea:(isset($reg->dto_item_monto_igv)?$reg->dto_item_monto_igv:'0'))."'"; }
	            if(_table_has_column('detalle_venta','valor_unitario_bruto')){ $sql_detalle_cols .= ",valor_unitario_bruto"; $sql_detalle_vals .= ",'".(isset($reg->valor_unitario_bruto)?$reg->valor_unitario_bruto:$reg->precio)."'"; }
	            if(_table_has_column('detalle_venta','base_imponible_bruta')){ $sql_detalle_cols .= ",base_imponible_bruta"; $sql_detalle_vals .= ",'".(isset($reg->base_imponible_bruta)?$reg->base_imponible_bruta:$reg->subtotal)."'"; }
	            if(_table_has_column('detalle_venta','igv_bruto')){ $sql_detalle_cols .= ",igv_bruto"; $sql_detalle_vals .= ",'".(isset($reg->igv_bruto)?$reg->igv_bruto:$reg->igv)."'"; }
	            if(_table_has_column('detalle_venta','total_bruto_linea')){ $sql_detalle_cols .= ",total_bruto_linea"; $sql_detalle_vals .= ",'".(isset($reg->total_bruto_linea)?$reg->total_bruto_linea:$reg->importe)."'"; }
	            if(_table_has_column('detalle_venta','base_imponible_neta')){ $sql_detalle_cols .= ",base_imponible_neta"; $sql_detalle_vals .= ",'".(isset($reg->base_imponible_neta)?$reg->base_imponible_neta:$reg->subtotal)."'"; }
	            if(_table_has_column('detalle_venta','igv_neto_linea')){ $sql_detalle_cols .= ",igv_neto_linea"; $sql_detalle_vals .= ",'".(isset($reg->igv_neto_linea)?$reg->igv_neto_linea:$reg->igv)."'"; }
	            if(_table_has_column('detalle_venta','total_neto_linea')){ $sql_detalle_cols .= ",total_neto_linea"; $sql_detalle_vals .= ",'".(isset($reg->total_neto_linea)?$reg->total_neto_linea:$reg->importe)."'"; }

            $sql_detalle = "INSERT INTO detalle_venta (".$sql_detalle_cols.") VALUES (".$sql_detalle_vals.")";

	            $newDetalleId = ejecutarConsulta_retornarID($sql_detalle);
	            $mapDetalleIds[(string)$reg->iddetalle_venta] = $newDetalleId;
            if(!$newDetalleId){ $sw = false; }
            if ($tipo == '2') {
                $sqlp = "UPDATE articulo_stock SET stock=stock-$reg->txtCANTIDAD_ARTICULO WHERE idarticulo='$reg->idproducto' AND idlocal='$_COOKIE[idlocal]' ";
                ejecutarConsulta($sqlp);
	        }

        }

	        if(_table_exists('venta_descuentos')){
	            $mapDescuentos = array();
	            $sqlDesc = "SELECT * FROM venta_descuentos WHERE idventa='$idventa' ORDER BY idventa_descuento ASC";
	            $rsDesc = ejecutarConsulta($sqlDesc);
	            while($desc = $rsDesc->fetch_object()){
	                $colsDesc = "idempresa,idlocal,idventa,iddetalle_venta,alcance";
	                $valsDesc = "'$_COOKIE[id]','$_COOKIE[idlocal]','$idventanew',";
	                if(isset($desc->iddetalle_venta) && (string)$desc->iddetalle_venta!=='' && (string)$desc->iddetalle_venta!=='0' && isset($mapDetalleIds[(string)$desc->iddetalle_venta])){
	                    $valsDesc .= "'".$mapDetalleIds[(string)$desc->iddetalle_venta]."'";
	                }else{ $valsDesc .= "NULL"; }
	                $valsDesc .= ",'".(isset($desc->alcance)?$desc->alcance:'ITEM')."'";
	
	                if(_table_has_column('venta_descuentos','idventa_descuento_padre')){
	                    $colsDesc .= ",idventa_descuento_padre";
	                    if(isset($desc->idventa_descuento_padre) && (string)$desc->idventa_descuento_padre!=='' && (string)$desc->idventa_descuento_padre!=='0' && isset($mapDescuentos[(string)$desc->idventa_descuento_padre])){
	                        $valsDesc .= ",'".$mapDescuentos[(string)$desc->idventa_descuento_padre]."'";
	                    }else{ $valsDesc .= ",NULL"; }
	                }
	                $optDesc = array('origen_descuento','tipo_descuento','modo_descuento','afecta_base_imponible','afecta_igv','valor_descuento','monto_descuento','monto_base','monto_igv','monto_total_descuento','base_antes_descuento','igv_antes_descuento','total_antes_descuento','base_despues_descuento','igv_despues_descuento','total_despues_descuento','aplica_antes_igv','orden_aplicacion');
	                foreach($optDesc as $cdesc){
	                    if(_table_has_column('venta_descuentos',$cdesc)){
	                        $colsDesc .= ",".$cdesc;
	                        if(isset($desc->{$cdesc}) && $desc->{$cdesc}!==null && (string)$desc->{$cdesc}!==''){
	                            $valsDesc .= ",'".$desc->{$cdesc}."'";
	                        }else{
	                            $valsDesc .= ",NULL";
	                        }
	                    }
	                }
	
	                $sqlInsDesc = "INSERT INTO venta_descuentos (".$colsDesc.") VALUES (".$valsDesc.")";
	                $newDescId = ejecutarConsulta_retornarID($sqlInsDesc);
	                if(isset($desc->idventa_descuento) && (string)$desc->idventa_descuento!==''){
	                    $mapDescuentos[(string)$desc->idventa_descuento] = $newDescId;
	                }
	            }
	        }

        $sqlu = "SELECT * FROM caja_ventapago WHERE idventa='$idventa' ";
        $rspta = ejecutarConsulta($sqlu);

        while ($reg = $rspta->fetch_object()) {
            $n = $n + 1;

            $sql_detalle = "INSERT INTO caja_ventapago VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$_COOKIE[idlocal]', '$_COOKIE[idusuario]',  '','0','0', '$idventanew', '1',  '$reg->serie', '$reg->moneda', '$reg->montodolares', '$reg->tipocambio', '$reg->montosoles',  '$reg->operacion', '$reg->fecha', '$reg->fecha_pago', '$reg->fechaoperacion', '','0')";

            ejecutarConsulta($sql_detalle) or $sw = false;


        }

        $sqlu = "UPDATE venta SET estado='1' WHERE idventa='$idventa' ";
        ejecutarConsulta($sqlu);

        $new->creaPDF($idventanew, RUTA);
        $jsondata['id'] = $idventanew;
        $jsondata['mensaje'] = "Actualizado correctamente";

        echo json_encode($jsondata);


        exit();
		
		
break;

case 'llenaimpresion':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
$idventa=$_GET['idventa'];

$jsondata['estado'] = '1';
$jsondata['mensaje'] =RUTA.'/plugins/dompdf/ticket.php?id='.$idventa;	
		
echo json_encode($jsondata);
exit();	
		
		
break;
		

		
		
case 'llenaimpresion2':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
require "resumen.php";
require "../modelos/numeros-letras.php";
include "../plugins/phpqrcode/qrlib.php";
	
$jsondata = array();
	$idventa=$_GET['idventa'];

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	

$sql="SELECT *FROM venta WHERE idventa='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
		
$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);

$sql3="SELECT *FROM config WHERE estado='1' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);
		
$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);
		
if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; }
		
if($mostrar['txtID_TIPO_DOCUMENTO']=='03'){ $tdocumento='BOLETA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='90'){ $tdocumento='RECIBO'; }
		
$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['txtSERIE'].' | '.$mostrar['txtNUMERO'].' | '.$mostrar['txtIGV'].' | '.$mostrar['txtTOTAL'].' | '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' | '.$mcliente['tipo_documento'].' | '.$mcliente['txtID_CLIENTE'].' |';
		
$rutaqr="../plugins/dompdf/".$mempresa['ruc'].".png";
QRcode::png($text, $rutaqr, 'Q', 15, 0);
		
		
$cont='<style type="text/css">
.cuerpo { float: left; width: 100%; font-size: 10px; font-family: Arial; }
.tabla { font-size: 10px; }
.centrado { text-align: center; }
.negrita { font-weight: bold; }
.logo { max-width: inherit;  width: 100%; height: auto; }
  </style>';
		
		
$cont.='<img src="'.RUTA.'/images/tulogo.jpg" style="width: 100%; height: auto;"  class="logo" >';
$cont.='<div class="cuerpo" ><div class="centrado">'.$mempresa['razon_social'].'<br>';
$cont.='RUC: '.$mempresa['ruc'].' - '.$local['direccion'].'<br>';
$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'</b></div>';
$cont.='FECHA DE EMISIÓN: '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).'<br>';
$cont.='<hr>';
$cont.='DATOS DEL CLIENTE: <br>';		
$cont.=$mcliente['nombre'].'<br>';		
$cont.=$mcliente['tipo_documento'].': '.$mcliente['txtID_CLIENTE'].'<br>';	
$cont.='DIRECCIÓN: '.$mcliente['direccion'].'<br>';		
		
$cont.='<hr>';
$cont.='
<table width="100%" class="negrita tabla" border="0" cellspacing="0">
  <tbody>
    <tr>
      <td width="15%" >Cód</td>
      <td width="45%" >Prod.</td>
      <td width="5%" >Cant.</td>
      <td width="15%" style="text-align:center" >P. Unit.</td>
      <td width="15%" style="text-align: right" >Imp</td>
    </tr>
  </tbody>
</table>
<hr>

<table width="100%" class="tabla" border="0" cellspacing="0">
  <tbody>';
		

$rspta = $resumen->detfactura($mostrar['idventa']);
while ($reg = $rspta->fetch_object()){	
$cont.='<tr>
      <td width="15%" >'.$reg->codigoproducto.'</td>
      <td width="45%" >'.$reg->nombreproducto.'</td>
      <td width="5%" >'.$reg->txtCANTIDAD_ARTICULO.'</td>
      <td width="15%" style="text-align:center">'.$reg->precio.'</td>
      <td width="15%" style="text-align: right" >'.$reg->importe.'</td>
    </tr>';
}	
$cont.='</tbody>
</table>
<hr>
<table width="100%" border="0" class="tabla" cellspacing="0">
  <tbody>
    <tr>
      <td width="55%"></td>
      <td width="10%"></td>
      <td width="15%" style="text-align:center" >Total S/</td>
      <td width="15%" style="text-align: right" >'.$mostrar['txtTOTAL'].'</td>
    </tr>
    
<tr>
      <td width="55%" >Op Gravada</td>
      <td width="10%"></td>
      <td width="15%" ></td>
      <td width="15%" style="text-align: right" >'.$mostrar['txtSUB_TOTAL'].'</td>
    </tr>
	
<tr>
      <td width="55%" >Gratuita</td>
      <td width="10%"></td>
      <td width="15%" ></td>
      <td width="15%" style="text-align: right" >'.$mostrar['gratuita'].'</td>
</tr>
	
<tr>
      <td width="55%" >Exonerado</td>
      <td width="10%"></td>
      <td width="15%" ></td>
      <td width="15%" style="text-align: right" >'.$mostrar['exonerado'].'</td>
    </tr>
	

    
<tr>
      <td width="55%" >I.G.V. S/</td>
      <td width="10%"></td>
      <td width="15%" style="text-align:center"></td>
      <td width="15%" style="text-align: right" >'.$mostrar['txtIGV'].'</td>
    </tr>
    
<tr>
      <td width="55%" >Importe total S/</td>
      <td width="10%"></td>
      <td width="15%" style="text-align:center" ></td>
      <td width="15%" style="text-align: right" >'.$mostrar['txtTOTAL'].'</td>
    </tr>
  <tr>
      <td width="55%">Importe a Pagar S/</td>
      <td width="10%"></td>
      <td width="15%" style="text-align:center"></td>
      <td width="15%" style="text-align: right" >'.$mostrar['txtTOTAL'].'</td>
    </tr>  
    
  </tbody>
</table>';
$cont.='<br>SON: '.numtoletras($mostrar['txtTOTAL']);
$cont.='<div style="text-align: center"><br><img src="'.$rutaqr.'" style="width: 70%; height: auto;"  /><br><br></div>';		
//$cont.='Representación impresa de la Boleta de '.$tdocumento.' esta puede ser consultada en http://monalisa.zapto.org:8080/erp/pag_cliente/ <br>';	
$cont.='<div style="text-align: center">GRACIAS POR SU PREFERENCIA</div>';
$cont.='</div>';
		
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $cont;	
		
echo json_encode($jsondata);
exit();	
		
		
break;
		
		
case 'bdescuento':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	
	
$sql="SELECT *FROM persona WHERE idpersona='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$jsondata['estado'] = '1';
$jsondata['descuento']=$mostrar['descuento'];	
$jsondata['descuentom']=$mostrar['descuentom'];	
		
echo json_encode($jsondata);
exit();	

break;
		
		
case 'listarpedidoanticipo':
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$sql="SELECT *FROM venta WHERE idempresa='$_COOKIE[id]'  AND beta='$fa[tipo]' AND  	txtID_TIPO_DOCUMENTO='92' ORDER by idventa desc";
$nivel=ejecutarConsulta($sql);

 		$data= Array();
	
while ($reg=$nivel->fetch_object()){
	
$sqlc="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$moc= ejecutarConsultaSimpleFila($sqlc);
	
$serienum=$reg->txtSERIE.'-'.$reg->txtNUMERO;
	
$sql22="SELECT sum(txtTOTAL) as total FROM venta WHERE referencia='$serienum' AND idempresa='$fa[id]' AND txtID_TIPO_DOCUMENTO='01' ";
$mos22= ejecutarConsultaSimpleFila($sql22);
	
$porpagar=$reg->txtTOTAL;
$pagado='0.00';
	
if($mos22['total']!=''){ $porpagar=number_format($reg->txtTOTAL-$mos22['total'], 2, '.', ''); $pagado=$mos22['total']; }

$nombrep='';
$dpersona='0';
if($moc){
$nombrep=$moc['nombre'];
$dpersona=$moc['idpersona'];
}
	
$botones='<button class="btn btn-danger btn-xs" onclick="adddocr2(\''.$reg->txtSERIE.'\', \''.$reg->txtNUMERO.'\', \''.$nombrep.'\', \''.$dpersona.'\', \''.$reg->idventa.'\')" ><i class="fa fa-plus"></i></button>';
	
$data[]=array(
"0"=>$botones,
"1"=>$reg->idventa,
"2"=>$nombrep,
"3"=>$reg->txtFECHA_DOCUMENTO,
"4"=>$reg->txtSERIE.'-'.$reg->txtNUMERO,		
"5"=>$reg->txtTOTAL,
"6"=>$pagado,
"7"=>$porpagar
);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
		
case 'listarguia':
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$sql="SELECT *FROM guia_guia WHERE idempresa='$_COOKIE[id]'  AND beta='$fa[tipo]' AND idventa='0' ORDER by id desc";
$nivel=ejecutarConsulta($sql);
$data= Array();

while ($reg=$nivel->fetch_object()){
	
$sqlc="SELECT *FROM persona WHERE idpersona='$reg->idcliente' ";
$moc= ejecutarConsultaSimpleFila($sqlc);
	
$serienum=$reg->serie.'-'.$reg->numero;
	
$botones='<button class="btn btn-danger btn-xs" onclick="addguia(\''.$reg->serie.'\', \''.trim($reg->numero).'\', \''.$moc['nombre'].'\', \''.$moc['idpersona'].'\', \''.$reg->id.'\')" ><i class="fa fa-plus"></i></button>';
	
$data[]=array(
"0"=>$botones,
"1"=>$reg->id,
"2"=>$moc['nombre'],
"3"=>$reg->fecha,
	
"4"=>$reg->serie.'-'.$reg->numero
);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
	
case 'listarguiadetalle':

$id=isset($_GET["id"])? limpiarCadena($_GET["id"]):"";

$sql2="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$emp= ejecutarConsultaSimpleFila($sql2);

$porigv=$emp['igv'];
$porigv=$porigv/100;
$porigv=$porigv+1.00;

		
$sql="SELECT *FROM guia_detalle WHERE idguia='$id' ORDER by id desc";
$nivel=ejecutarConsulta($sql);
$data= Array();

while ($reg=$nivel->fetch_object()){

$sql2="SELECT *FROM articulo WHERE codigo='$reg->codigoproducto' ";
$mo= ejecutarConsultaSimpleFila($sql2);
	
$sql3="SELECT *FROM unidad_medida WHERE id='$mo[medida]' ";
$me= ejecutarConsultaSimpleFila($sql3);


/*
IMPUESTOS
*/


$igv='0.00';
$sub='0.00';
$exonerado='0.00';
$gravadas='0.00';
$inafecta='0';
$gratuitas='0.00';
$precio=$mo['precio'];

//echo 'idproducto:'.$reg->idproducto.'|';


$subtotal=$precio*$reg->cantidad;

if($reg->tipo=='2'){
$valor=$precio;
$gratuitas=$subtotal;
$sub=$subtotal;
$exonerado='0.00';
$igv='0.00';
$gravadas='0.00';

} else if(in_array((string)$reg->tipo, array('11','12','13','14','15','16','21','31','32','33','34','35','36'), true)){
$valor=$precio;
$gratuitas=$subtotal;
$sub='0.00';
$exonerado='0.00';
$igv='0.00';
$gravadas='0.00';
$inafecta='0.00';

} else if($reg->tipo=='3'){
$valor=$precio;
$exonerado='0.00';
$inafecta=$subtotal;
$sub=$subtotal;
$gravadas='0.00';
$igv='0.00';
}else if($reg->tipo=='1'){
$valor=$precio;
$exonerado=$subtotal;
$sub=$subtotal;
$gravadas='0.00';
$igv='0.00';

}else{
$valor=($precio/$porigv);
$sub=($subtotal/$porigv);
$igv= ($subtotal-$sub);

$gravadas=$sub;

}


/*
IMPUESTOS
*/


$sub=round($sub, 2);


$idp='<input type="hidden" id="idp'.$reg->id.'" name="idp'.$reg->id.'" value="'.$reg->idproducto.'" />';
$idp.='<input type="hidden" id="tipoart'.$reg->id.'" name="tipoart'.$reg->id.'" value="0" />';	
$idp.='<input type="hidden" id="codigo'.$reg->id.'" name="codigo'.$reg->id.'" value="'.$reg->codigoproducto.'" />';
$idp.='<input type="hidden" id="umedida'.$reg->id.'" name="umedida'.$reg->id.'" value="'.$reg->unidadmedida.'">';
$idp.='<input type="hidden" id="tit'.$reg->id.'" name="tit'.$reg->id.'" value="'.$reg->nombreproducto.'">';
$idp.='<input type="hidden" id="cti2'.$reg->id.'" name="cti2'.$reg->id.'" value="1">';
$idp.='<input type="hidden" id="inafecta'.$reg->id.'" name="inafecta'.$reg->id.'" value="'.$inafecta.'">';

$idp.='	
<input type="hidden" id="exon'.$reg->id.'" name="exon'.$reg->id.'" value="'.$mo['exonerado_igv'].'"> <input type="hidden" id="tipo'.$reg->id.'" name="tipo'.$reg->id.'" value="'.$reg->tipo.'"> <input type="hidden" id="igv'.$reg->id.'" name="igv'.$reg->id.'" value="'.$igv.'"> <input type="hidden" id="exo'.$reg->id.'" name="exo'.$reg->id.'" value="'.$exonerado.'">  <input type="hidden" id="gravadas'.$reg->id.'" name="gravadas'.$reg->id.'" value="'.$gravadas.'"> <input type="hidden" id="comision'.$reg->id.'" name="comision'.$reg->id.'" value="0.00"> <input type="hidden" id="idu'.$reg->id.'" name="idu'.$reg->id.'" value="'.$reg->id.'"> <input type="hidden" id="idunid'.$reg->id.'" name="idunit'.$reg->id.'" value="0"> <input type="hidden" id="grat'.$reg->id.'" name="grat'.$reg->id.'" value="'.$gratuitas.'"> <input type="hidden" id="precioo'.$reg->id.'" name="precioo'.$reg->id.'" value="0.00"> <input type="hidden" id="serie'.$reg->id.'" name="serie'.$reg->id.'" value="0"> <input type="hidden" id="descuento'.$reg->id.'" name="descuento'.$reg->id.'" value="0.00"> <input type="hidden" id="puntos'.$reg->id.'" name="puntos'.$reg->id.'" value="0"> <input type="hidden" id="ctipuntos'.$reg->id.'" name="ctipuntos'.$reg->id.'" value="0"> <input type="hidden" id="totdetraccion'.$reg->id.'" name="totdetraccion'.$reg->id.'" value="0">  <input type="hidden" id="cantidad_toneladas'.$reg->id.'" name="cantidad_toneladas'.$reg->id.'" value="0">  <input type="hidden" id="carga_util'.$reg->id.'" name="carga_util'.$reg->id.'" value="0">  <input type="hidden" id="iddestino'.$reg->id.'" name="iddestino'.$reg->id.'" value="0">';
	
$cti='<input type="text" id="ctif'.$reg->id.'" onkeyup="teclea('.$reg->id.', '.$precio.')" name="ctif'.$reg->id.'" value="'.$reg->cantidad.'" size="8" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';

$precio='<input type="text" id="preciof'.$reg->id.'" name="preciof'.$reg->id.'" onkeyup="teclea('.$reg->id.', '.$precio.')" value="'.$precio.'" size="8" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';
	
$tot='<input type="text" id="totf'.$reg->id.'" name="totf'.$reg->id.'" readonly value="'.$subtotal.'" size="10" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';

$descuentotxt='<input type="text" size="5" id="descuento'.$reg->id.'" name="descuento'.$reg->id.'" value="0"> <input type="hidden" size="5" id="ctidescuento'.$reg->id.'" name="ctidescuento'.$reg->id.'" value="0">';


$subtotatxt='<input type="text" id="sub'.$reg->id.'" name="sub'.$reg->id.'" value="'.$sub.'" readonly size="10" style="margin: 0px; padding: 1px; padding-left: 2px;  " >';
	
$data[]=array(
	"0"=>$reg->id,
	"1"=>$idp.' '.$reg->nombreproducto,
 	"2"=>$reg->unidadmedida,
 	"3"=>$cti,
 	"4"=>$precio,
	"5"=>$descuentotxt,
	"6"=>$subtotatxt,
	"7"=>$tot,
	"8"=>'<button type="button"  class="btn btn-danger btn-xs" ><span class="glyphicon glyphicon-trash"></span></button>'
 	);
 		}

echo json_encode($data);

break;
		
		

	
//AQUI ORDEN DE PEDIDO2
case 'listarorden2':

$cat=(isset($_GET['cat'])) ? $_GET['cat'] : "2";
		
$nivel='2';
if($cat=='2'){ $nivel='3'; }
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$conf= ejecutarConsultaSimpleFila($sql3);
		
if(esModoBetaSunatVenta(isset($conf['tipo']) ? $conf['tipo'] : '3')){ $tipoc='BETA'; }else{ $tipoc='PRODUCCION'; }
		
$sql="SELECT *FROM ingreso WHERE idlocal='$_COOKIE[idlocal]' AND beta='$conf[tipo]' AND estado='0' AND nivel='$nivel' ORDER by idingreso desc";
$rspta=ejecutarConsulta($sql);		

 		$data= Array();
	
while ($reg=$rspta->fetch_object()){
	
$sqlc="SELECT *FROM persona WHERE idpersona='$reg->idproveedor' ";
$moc= ejecutarConsultaSimpleFila($sqlc);
	
$botones='<button class="btn btn-danger btn-xs" onclick="adddocr2(\''.$reg->serie_comprobante.'\', \''.$reg->num_comprobante.'\', \''.$moc['nombre'].'\', \''.$moc['idpersona'].'\', \''.$reg->idingreso.'\')" ><i class="fa fa-plus"></i></button>';
	
$data[]=array(
"0"=>$botones,
"1"=>$reg->idingreso,
"2"=>$moc['nombre'],
"3"=>date("Y-m-d", strtotime($reg->fecha_hora)),
"4"=>$reg->serie_comprobante.'-'.$reg->num_comprobante,		
"5"=>$reg->subtotal,
"6"=>$reg->igv,
"7"=>$reg->total_compra
);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;	
	

case 'listarordenf':
		
if(isset($_GET['idusuario'])){ $mes=$_GET['idusuario']; }else{ $mes=''; }
		
if(isset($_GET['fecha_inicio'])){  $fecha_inicio=$_GET["fecha_inicio"]; }else{ $fecha_inicio=''; }
if(isset($_GET['fecha_fin'])){  $fecha_fin=$_GET["fecha_fin"]; }else{ $fecha_fin=''; }
		
$jsondata = array();
$json = array();

$rspta=$venta->listarordenf($fecha_inicio, $fecha_fin, '0', $mes);	
while ($reg=$rspta->fetch_object()){

if(!empty($tipoDocFiltroReq) && !isset($tipoDocFiltroReq[(string)$reg->txtID_TIPO_DOCUMENTO])){ continue; }

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sql2="SELECT *FROM usuario WHERE idusuario='$reg->idusuario' ";
$mos= ejecutarConsultaSimpleFila($sql2);	
	
if($reg->txtID_TIPO_DOCUMENTO=='03'){ $tdocumento='BOLETA'; }
if($reg->txtID_TIPO_DOCUMENTO=='01'){ $tdocumento='FACTURA'; }
if($reg->txtID_TIPO_DOCUMENTO=='90'){ $tdocumento='RECIBO'; }	

$json['id']=$reg->idventa;
$json['fecha']=date("Y-m-d", strtotime($reg->txtFECHA_DOCUMENTO));
$json['tdocumento']=$tdocumento;	
$json['serie']=$reg->txtSERIE.'-'.$reg->txtNUMERO;
$json['nombre']=$mostrar['nombre'];
$json['docliente']=$mostrar['tipo_documento'].': '.$mostrar['txtID_CLIENTE'];
$json['cliente']=$mos['nombre'];
$json['subtotal']=$reg->txtSUB_TOTAL;
$json['igv']=$reg->txtIGV;
$json['total']=$reg->txtTOTAL;
$jsondata[] = $json;
 		}

echo json_encode($jsondata);
exit();
		
break;
		
		
case 'numeroorden':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	
$idlocal=$_COOKIE["idlocal"];
	
$sql="SELECT *FROM venta_orden WHERE idlocal='$idlocal' ORDER BY id DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);

		$num=$mostrar['id']+1;
		$num=str_pad($num, 8, "0", STR_PAD_LEFT);
		
$seriet='OC001-'.$num;
$serie='OC001';
$jsondata['estado'] = '1';
$jsondata['serie'] = $serie;
$jsondata['seriet'] = $seriet;
$jsondata['mensaje'] = 'TODO BIEN!';

echo json_encode($jsondata);
exit();	
		
		
break;		

/*PROCESOS APP*/
		
case 'articulosapp':
		require_once "../modelos/Articulo.php";
		$articulo=new Articulo();
		
		$rspta=$articulo->listarActivos();
 		$data= Array();
		
while ($reg=$rspta->fetch_object()){
	
$texto = str_replace($no_permitidas, $permitidas, $reg->txtDESCRIPCION_ARTICULO);

$data[]=array(
"0"=>'<button class="btn btn-warning btn-xs" onclick="agregar1(\''.$reg->txtCOD_ARTICULO.'\')"><span class="fa fa-plus"></span></button>',
"1"=>$reg->txtCOD_ARTICULO,
"2"=>$texto
);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);
	break;
		
case 'detproducto':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	
	$num=0;	
$idp=$_GET['idp'];

if(isset($_COOKIE["idlocal"])){ $idlocal=$_COOKIE["idlocal"]; }else{ $idlocal='1'; }
		
$sqls="SELECT *FROM articulo WHERE txtCOD_ARTICULO='$idp' ";
$s= ejecutarConsultaSimpleFila($sqls);
		
$sql="SELECT * FROM unidad_medida WHERE id='$s[medida]'";
$mostrar=ejecutarConsultaSimpleFila($sql);
	
$costos='<option value="NIU-1-0-0-0-0-0" >UNIDAD (BIENES)</option>';	
$dat = "SELECT *FROM articulo_unidad WHERE idproducto='".$s['txtCOD_ARTICULO']."' ORDER BY id DESC ";
$dat = ejecutarConsulta($dat);
while($dato=mysqli_fetch_array($dat)) {
$costos.='<option value="'.$dato['medida'].'-'.$dato['cti'].'-'.$dato['precio'].'-'.$dato['comision'].'-'.$dato['ctimayor'].'-'.$dato['preciom'].'-'.$dato['comisionm'].'" >'.$dato['nombre'].'</option>';
}
/*	
<button class="btn btn-warning btn-xs" onclick="addtablap(\''.$reg->txtCOD_ARTICULO.'\',\''.$reg->codigo.'\', \''.$mostrar['codigo'].'\', \''.$mostrar['tit'].'\', \''.$reg->stock.'\', \''.$reg->mayor.'\', \''.$reg->comision.'\', \''.$reg->comisionm.'\', \''.$reg->comisionmp.'\')"><span class="fa fa-plus"></span></button>
*/

$jsondata['estado'] = '1';
$jsondata['nombre'] = $s['txtDESCRIPCION_ARTICULO'];
$jsondata['codigo'] = $s['codigo'];
$jsondata['precio'] = $s['precio'];
$jsondata['preciom'] = $s['precio_mayor'];
$jsondata['medidacom'] = $costos;	
$jsondata['umedida'] = $mostrar['codigo'];	
$jsondata['medida'] = $mostrar['tit'];
$jsondata['stock'] = $s['stock'];
$jsondata['mayor'] = $s['mayor'];
$jsondata['comision'] = $s['comision'];
$jsondata['comisionm'] = $s['comisionm'];
$jsondata['comisionmp'] = $s['comisionmp'];
$jsondata['id'] = $s['txtCOD_ARTICULO'];
$jsondata['mensaje'] = 'TODO BIEN!';

echo json_encode($jsondata);
break;

case 'listarapp':
		
if(isset($_GET['mes'])){ $mes=$_GET['mes']; }else{ $mes=''; }
if(isset($_GET['fecha_inicio'])){  $fecha_inicio=$_GET["fecha_inicio"]; }else{ $fecha_inicio=''; }
if(isset($_GET['fecha_fin'])){  $fecha_fin=$_GET["fecha_fin"]; }else{ $fecha_fin=''; }
		
		$rspta=$venta->listar($fecha_inicio,$fecha_fin,'0', $mes);
 		//Vamos a declarar un array
 		$data= Array();

$sql3="SELECT *FROM config WHERE estado='1' ";
$conf= ejecutarConsultaSimpleFila($sql3);
		
if(esModoBetaSunatVenta(isset($conf['tipo']) ? $conf['tipo'] : '3')){ $tipoc='BETA'; }else{ $tipoc='PRODUCCION'; }
		
while ($reg=$rspta->fetch_object()){

if(!empty($tipoDocFiltroReq) && !isset($tipoDocFiltroReq[(string)$reg->txtID_TIPO_DOCUMENTO])){ continue; }

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$pdf=$conf['ruc'].'-'.$reg->txtID_TIPO_DOCUMENTO.'-'.$reg->txtSERIE.'-'.$reg->txtNUMERO.'.pdf';
$estadob='';
	
$botones='<a target="_blank" href="../api_cpe/'.$tipoc.'/'.$conf['ruc'].'/'.$pdf.'"><button class="btn btn-default btn-xs"><i class="fa fa-file"></i></button></a>';
	
if($reg->estado=='0'){ 
	
if($reg->txtID_TIPO_DOCUMENTO=='90'){
$estadob='<span class="label bg-green">Guardado</span>';	
}else{
$estadob='<span class="label label-default">Pendiente</span>'; 	
}

}

	
if($reg->txtID_TIPO_DOCUMENTO=='03'){ $tdocumento='BOLETA'; }
if($reg->txtID_TIPO_DOCUMENTO=='01'){ $tdocumento='FACTURA'; }
if($reg->txtID_TIPO_DOCUMENTO=='90'){ $tdocumento='RECIBO'; }	
	
$data[]=array(
"0"=>$botones,
"1"=>date("Y-m-d", strtotime($reg->txtFECHA_DOCUMENTO)),
"2"=>$reg->txtSERIE.'-'.$reg->txtNUMERO,
"3"=>$mostrar['nombre'],
"4"=>$reg->txtTOTAL
);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;	
		
case 'buscarcodigo':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
$codigo=$_GET['codigo'];

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR NO SE ENCONTRO ARTICULO';	

$sql="SELECT *FROM articulo WHERE codigo='$codigo' AND idempresa='$_COOKIE[id]' AND idlocal='$_COOKIE[idlocal]' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

if($mostrar){

$sqls="SELECT *FROM articulo_stock WHERE idarticulo='$mostrar[txtCOD_ARTICULO]' AND idlocal='$_COOKIE[idlocal]' ";
$stockf= ejecutarConsultaSimpleFila($sqls);
	
$otrosdatos=$mostrar['medida'].'|1|1|0|'.$mostrar['precio_mayor'].'|'.$mostrar['mayor'].'|'.$mostrar['comision'].'|'.$mostrar['comisionm'].'|'.$mostrar['comisionmp'].'|'.$mostrar['txtCOD_ARTICULO'].'|0';
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'ENCONTRADO';
$jsondata['id'] = $mostrar['txtCOD_ARTICULO'];
$jsondata['precio'] = $mostrar['precio'];
$jsondata['articulo'] = $mostrar['txtDESCRIPCION_ARTICULO'];
$jsondata['codigo'] = $mostrar['codigo'];
$jsondata['stock'] = $stockf['stock'];
$jsondata['otrosdatos'] =$otrosdatos;
$jsondata['exonerado'] = $mostrar['exonerado_igv'];
}		
echo json_encode($jsondata);
exit();	
		
		
break;
		

		
case 'estadofact':

$id=$_GET['id'];
$estado=$_GET['estado'];
		
$sql="UPDATE venta SET estado='$estado' WHERE idventa='$id' ";
$where=ejecutarConsulta($sql);

if($where){
$jsondata['estado'] = '1';
$jsondata['mensaje'] ='ACTUALIZADO CON EXITO'.$estado;
}else{
$jsondata['estado'] = '2';
$jsondata['mensaje'] ='NO SE PUDO ACTUALIZAR';	
}
		
echo json_encode($jsondata);
exit();	
		
		
break;		
		
	
	
case 'listarpagos':

$idventa=$_GET['id'];
$nivel=$_GET['nivel'];
		
$data= Array();
		
$results=ejecutarConsulta("SELECT * FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' ");
while ($reg=$results->fetch_object()){
			
$sql2="SELECT *FROM caja_tipopago WHERE id='$reg->idtipo' ";
$mos= ejecutarConsultaSimpleFila($sql2);

$tipopago='';
if($mos){ $tipopago=$mos['descripcion']; }
	
$botones='<button type="button" class="btn btn-danger btn-xs" onClick="eliminarpago('.$reg->id.', '.$reg->idventa.')" ><span class="glyphicon glyphicon-trash"></span></button>';

if($reg->estado=='0'){
$botones.=' <button type="button" class="btn btn-success btn-xs" onClick="pagado('.$reg->id.', '.$reg->idventa.')" ><span class="glyphicon glyphicon-ok-sign"></span>Pagado</button>';
$estadob='<span class="label label-default">Pendiente</span>'; 
}else{
$estadob='<span class="label label-success">Pagado</span>';
}

$data[]=array(
"0"=>$botones,
"1"=>$reg->id,
"2"=>$tipopago,
"3"=>date("Y-m-d", strtotime($reg->fecha)),
"4"=>date("Y-m-d", strtotime($reg->fecha_pago)),
"5"=>$reg->moneda,
"6"=>$reg->montosoles,
"7"=>$reg->montodolares,
"8"=>$reg->tipocambio,
"9"=>$estadob
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
case 'guardasaldo':

$jsondata = array();
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

$jsondata['estado'] = '0';
$jsondata['mensaje'] = "Error general";

// Validación mínima de entrada
$idventa = isset($idventa) ? $idventa : (isset($_POST['idventa']) ? $_POST['idventa'] : '');
if($idventa===''){
	$jsondata['mensaje'] = 'Falta idventa.';
	echo json_encode($jsondata); exit();
}
$nivel = isset($nivel) ? $nivel : (isset($_POST['nivel']) ? $_POST['nivel'] : '0');
$tipopagodet = isset($tipopagodet) ? $tipopagodet : (isset($_POST['tipopagodet']) ? $_POST['tipopagodet'] : '');
$otrospagos = isset($tipopago) ? $tipopago : (isset($_POST['tipopago']) ? $_POST['tipopago'] : '0');
$properiodo = isset($properiodo) ? $properiodo : (isset($_POST['properiodo']) ? $_POST['properiodo'] : 'NO');
$periodo = isset($periodo) ? $periodo : (isset($_POST['periodo']) ? $_POST['periodo'] : '');
$letras = isset($letras) ? (int)$letras : (isset($_POST['letras']) ? (int)$_POST['letras'] : 0);

$txtTOTAL = isset($txtTOTAL) ? (float)$txtTOTAL : (isset($_POST['txtTOTAL']) ? (float)$_POST['txtTOTAL'] : 0);
if($txtTOTAL<=0 && $properiodo=='NO'){
	$jsondata['mensaje'] = 'El monto debe ser mayor a 0.';
	echo json_encode($jsondata); exit();
}

// ======================================================
// Validación contra total del documento (evita duplicados/overpay)
// - Considera pagos ya registrados (mismo concepto otrospagos)
// - Si excede, NO graba y devuelve 1 solo mensaje
// ======================================================
$total_doc = 0;
$mon_doc = 'PEN';
if($nivel=='0'){
    $rowv = ejecutarConsultaSimpleFila("SELECT txtTOTAL AS total, txtID_MONEDA AS moneda FROM venta WHERE idventa='$idventa' LIMIT 1");
    if($rowv && isset($rowv['total'])){ $total_doc = (float)$rowv['total']; }
    if($rowv && isset($rowv['moneda'])){ $mon_doc = (string)$rowv['moneda']; }
}else{
    $rowi = ejecutarConsultaSimpleFila("SELECT total_compra AS total, moneda AS moneda FROM ingreso WHERE idingreso='$idventa' LIMIT 1");
    if($rowi && isset($rowi['total'])){ $total_doc = (float)$rowi['total']; }
    if($rowi && isset($rowi['moneda'])){ $mon_doc = (string)$rowi['moneda']; }
}

// Suma ya registrada para este concepto
$sumRow = ejecutarConsultaSimpleFila("SELECT SUM(montosoles) AS sPEN, SUM(montodolares) AS sUSD FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' AND otrospagos='$otrospagos'");
$ya_reg = 0;
if($mon_doc==='PEN'){ $ya_reg = (float)$sumRow['sPEN']; } else { $ya_reg = (float)$sumRow['sUSD']; }
$ya_reg = round($ya_reg, 2);

// Evitar doble click (mismo monto/fecha/operacion/concepto)
if($properiodo!='SI'){
    $dupRow = ejecutarConsultaSimpleFila("SELECT COUNT(*) AS c FROM caja_ventapago 
        WHERE idventa='$idventa' AND nivel='$nivel' AND otrospagos='$otrospagos'
          AND fecha_pago='$txtFECHA_DOCUMENTO' AND operacion='$operacion'
          AND ((montosoles='$txtTOTAL' AND '$mon_doc'='PEN') OR (montodolares='$txtTOTAL' AND '$mon_doc'<>'PEN'))");
    if($dupRow && (int)$dupRow['c']>0){
        $jsondata['mensaje'] = 'Este pago ya fue registrado.';
        echo json_encode($jsondata); exit();
    }
}

if($total_doc>0){
    $nuevo_total = round($ya_reg + (float)$txtTOTAL, 2);
    if($nuevo_total - round($total_doc,2) > 0.009){
        $jsondata['mensaje'] = 'El monto registrado más el monto a agregar supera el TOTAL del documento. Pendiente por asignar: '.number_format(round($total_doc-$ya_reg,2),2,'.','');
        echo json_encode($jsondata); exit();
    }
}


$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$facon= ejecutarConsultaSimpleFila($sql3);

$sqlvent="SELECT *FROM venta WHERE idventa='$idventa' ";
$vent= ejecutarConsultaSimpleFila($sqlvent);
if(!$vent){
	$jsondata['mensaje'] = 'Venta no encontrada.';
	echo json_encode($jsondata); exit();
}

// Moneda/TC del documento (si no vienen, toma del documento)
if(!isset($moneda) || $moneda==''){ $moneda = $vent['txtID_MONEDA']; }
if(!isset($tcambio) || (float)$tcambio<=0){
	$tcambio = (isset($vent['tipocambio']) ? $vent['tipocambio'] : 0);
	if((float)$tcambio<=0){ $tcambio = 1; }
}

// Helpers
function _pad8($n){
	$n = preg_replace('/\D/', '', (string)$n);
	if($n==='') $n='0';
	return str_pad($n, 8, "0", STR_PAD_LEFT);
}
function _baseDoc($vent){
	$ser = '';
	$num = '';
	if(isset($vent['txtSERIE'])) $ser = trim((string)$vent['txtSERIE']);
	if($ser===''){ if(isset($vent['serie'])) $ser = trim((string)$vent['serie']); }
	if(isset($vent['numero'])) $num = _pad8($vent['numero']);
	if($num==='00000000'){ if(isset($vent['txtNUMERO'])) $num = _pad8($vent['txtNUMERO']); }
	return ($ser!=='' ? ($ser.'-'.$num) : $num);
}
$baseDoc = _baseDoc($vent);

// Obtener correlativo de cuota para serie tipo F001-00000008-01
function _nextCuota($idventa,$nivel,$baseDoc){
	$max = 0;
	$sql="SELECT serie FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' AND tipopago='CREDITO' AND serie LIKE '$baseDoc-%' ORDER BY id DESC LIMIT 1";
	$row = ejecutarConsultaSimpleFila($sql);
	if($row && isset($row['serie'])){
		$parts = explode('-', (string)$row['serie']);
		$last = end($parts);
		$max = (int)$last;
	}
	return $max + 1;
}

// Recalcular pendiente real por concepto (considera registros existentes)
function _sumConcepto($idventa,$nivel,$otrospagos,$moneda){
	$sql="SELECT SUM(montosoles) AS s, SUM(montodolares) AS d FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' AND otrospagos='$otrospagos'";
	$r= ejecutarConsultaSimpleFila($sql);
	if($moneda=='PEN') return (float)$r['s'];
	return (float)$r['d'];
}
$pagado_normal = _sumConcepto($idventa,$nivel,'0',$moneda);
$pagado_det = _sumConcepto($idventa,$nivel,'1',$moneda);
$pagado_ret = _sumConcepto($idventa,$nivel,'2',$moneda);

// Totales por concepto
$total_normal = (float)$vent['txtTOTAL'] - (float)$vent['detraccion'] - (float)$vent['retencion'] + (float)$vent['percepcion'] + $pagado_det + $pagado_ret;
$pend_normal = round($total_normal - $pagado_normal, 2);

$pend_det = round(((float)$vent['detraccion'] - $pagado_det), 2);
if($pend_det < 0) $pend_det = 0;
$pend_ret = round(((float)$vent['retencion'] - $pagado_ret), 2);
if($pend_ret < 0) $pend_ret = 0;

// Determinar pendiente según concepto seleccionado
$pendiente = $pend_normal;
if((string)$otrospagos==='1') $pendiente = $pend_det;
if((string)$otrospagos==='2') $pendiente = $pend_ret;

// Si DET/RET y no está configurado en la venta
if(((string)$otrospagos==='1' && (float)$vent['detraccion']<=0) || ((string)$otrospagos==='2' && (float)$vent['retencion']<=0)){
	$jsondata['mensaje'] = 'La venta no tiene detracción/retención configurada. Modifica la venta para incluir estos valores.';
	echo json_encode($jsondata); exit();
}

if($properiodo=='NO'){
	// Validar monto <= pendiente
	if(round($txtTOTAL,2) - round($pendiente,2) > 0.009){
		$jsondata['mensaje'] = 'No se puede registrar un monto mayor al pendiente. Pendiente por asignar: '.number_format($pendiente,2,'.','');
		echo json_encode($jsondata); exit();
	}

	// Convertir montos según moneda
	if($moneda=='PEN'){
		$soles=$txtTOTAL;
		$dolares=($tcambio>0? $txtTOTAL/$tcambio : 0);
		$dolares=round($dolares, 3);
	}else{
		$dolares=$txtTOTAL;
		$soles=$txtTOTAL*$tcambio;
		$soles=round($soles, 3);
	}

	$fechadoc = date('Y-m-d');
	if($nivel=='1'){
		$sql3="SELECT *FROM ingreso WHERE idingreso='$idventa' ";
		$fa= ejecutarConsultaSimpleFila($sql3);
		$fechadoc=$fa['fecha_hora'];
	}

	// Serie: si es CREDITO (cuota) => baseDoc-XX, caso contrario mantiene correlativo numérico
	$serieGuardar = '00000001';
	if($tipopagodet==='CREDITO' && $nivel=='0'){
		$n = _nextCuota($idventa,$nivel,$baseDoc);
		$serieGuardar = $baseDoc.'-'.str_pad((string)$n,2,'0',STR_PAD_LEFT);
	}else{
		$sqlci="SELECT *FROM caja_ventapago WHERE nivel='$nivel' AND tipopago='$tipopagodet' ORDER BY id DESC ";
		$ci= ejecutarConsultaSimpleFila($sqlci);
		if($ci){ $serieGuardar=str_pad(((int)$ci['serie']+1), 8, "0", STR_PAD_LEFT); }
		else { $serieGuardar=str_pad('1',8,"0",STR_PAD_LEFT); }
	}

	$sql="INSERT INTO caja_ventapago VALUES (NULL, '$_COOKIE[id]', '$facon[tipo]', '$_COOKIE[idlocal]', '$_COOKIE[idusuario]', '$tipopagodet', '$otrospagos', '$nivel', '$idventa', '$tpago', '$serieGuardar', '$moneda', '$soles', '$dolares', '$tcambio', '$operacion', '$fechadoc', '$txtFECHA_DOCUMENTO', '$txtFECHA_DOCUMENTO', '', '0');";
	$sqlf=ejecutarConsulta($sql);

	if($sqlf){
		$pend_rest = round($pendiente - $txtTOTAL, 2);
		if($pend_rest < 0) $pend_rest = 0;
		$jsondata['estado'] = '1';
		$jsondata['mensaje'] = "Guardado correctamente";
		$jsondata['pendiente_restante'] = $pend_rest;
	}else{
		$jsondata['mensaje'] = "No se pudo guardar el pago.";
	}

	echo json_encode($jsondata);
	exit();

}else{
	// Periodo SI: generar cuotas automáticamente

	if($letras<=0){
		$jsondata['mensaje'] = 'Debe indicar la cantidad de letras (cuotas).';
		echo json_encode($jsondata); exit();
	}

	// Si existen cuotas y el usuario intenta cambiar letras => obligar a eliminar primero
	$sqlcnt="SELECT COUNT(*) AS c FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' AND tipopago='$tipopagodet' AND otrospagos='$otrospagos' ";
	$cnt= ejecutarConsultaSimpleFila($sqlcnt);
	$exist = (int)$cnt['c'];
	if($exist>0 && $exist!=$letras){
		$jsondata['mensaje'] = 'Debe eliminar todas las cuotas existentes antes de cambiar el número de cuotas por periodo.';
		echo json_encode($jsondata); exit();
	}

	// Monto base por concepto (pendiente actual)
	$monto_base = $pendiente;
	if($monto_base<=0){
		$jsondata['mensaje'] = 'No hay monto pendiente para generar cuotas.';
		echo json_encode($jsondata); exit();
	}

	// Calcular por cuota y ajustar la última para cuadrar exacto con el pendiente
	$porCuota = round($monto_base / $letras, 2);
	$acum = 0;

	$fechabase = date("Y-m-d", strtotime($vent['txtFECHA_DOCUMENTO']));
	$fechadoc = $fechabase;

	$next = 1;
	if($tipopagodet==='CREDITO' && $nivel=='0'){
		$next = _nextCuota($idventa,$nivel,$baseDoc);
	}else{
		$next = 1;
	}

	$okAll = true;

	for($n=1; $n<=$letras; $n++){
		$m = $porCuota;
		if($n==$letras){
			$m = round($monto_base - $acum, 2);
		}
		$acum = round($acum + $m, 2);

		// Fechas por periodo
		if($periodo=='MENSUAL'){
			$fecha=date("Y-m-d",strtotime($fechadoc."+ $n month"));
		}else if($periodo=='QUINCENAL'){
			$dias=round(15*$n);
			$fecha=date("Y-m-d",strtotime($fechadoc."+ $dias days"));
		}else if($periodo=='SEMANAL'){
			$fecha=date("Y-m-d",strtotime($fechadoc."+ $n week"));
		}else{
			$fecha=date("Y-m-d",strtotime($fechadoc."+ $periodo days"));
		}

		// Convertir montos según moneda
		if($moneda=='PEN'){
			$soles=$m;
			$dolares=($tcambio>0? $m/$tcambio : 0);
			$dolares=round($dolares, 3);
		}else{
			$dolares=$m;
			$soles=$m*$tcambio;
			$soles=round($soles, 3);
		}

		// Serie por cuota
		$serieGuardar = '00000001';
		if($tipopagodet==='CREDITO' && $nivel=='0'){
			$serieGuardar = $baseDoc.'-'.str_pad((string)$next,2,'0',STR_PAD_LEFT);
			$next++;
		}else{
			$serieGuardar = str_pad((string)$n, 8, "0", STR_PAD_LEFT);
		}

		$sql="INSERT INTO caja_ventapago VALUES (NULL, '$_COOKIE[id]', '$facon[tipo]', '$_COOKIE[idlocal]', '$_COOKIE[idusuario]', '$tipopagodet', '$otrospagos', '$nivel', '$idventa', '$tpago', '$serieGuardar', '$moneda', '$soles', '$dolares', '$tcambio', '$operacion', '$fechadoc', '$fecha', '$fecha', '', '0');";
		$ins=ejecutarConsulta($sql);
		if(!$ins){ $okAll=false; break; }
	}

	// Validar sumatoria exacta
	if($okAll && abs($acum - round($monto_base,2)) > 0.01){
		$jsondata['mensaje'] = 'La sumatoria de cuotas no coincide con el total pendiente del documento.';
		echo json_encode($jsondata); exit();
	}

	if($okAll){
		$jsondata['estado'] = '1';
		$jsondata['mensaje'] = "Cuotas generadas correctamente";
		$jsondata['pendiente_restante'] = 0;
	}else{
		$jsondata['mensaje'] = "No se pudo generar las cuotas.";
	}

	echo json_encode($jsondata);
	exit();
}

exit();
break;
		
case 'versaldo':

$jsondata = array();
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

// Sumas por concepto (otrospagos: 0 NORMAL / 1 DETRACCION / 2 RETENCION)
$sql2="SELECT SUM(montosoles) AS total, SUM(montodolares) AS totald FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' AND otrospagos='0' ";
$mos= ejecutarConsultaSimpleFila($sql2);

$sql3="SELECT SUM(montosoles) AS total, SUM(montodolares) AS totald FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' AND otrospagos='1' ";
$det3= ejecutarConsultaSimpleFila($sql3);

$sql4="SELECT SUM(montosoles) AS total, SUM(montodolares) AS totald FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' AND otrospagos='2' ";
$det4= ejecutarConsultaSimpleFila($sql4);

// Conteo de cuotas CREDITO por concepto (para recalculo por periodo)
$sqlc0="SELECT COUNT(*) AS c FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' AND tipopago='CREDITO' AND otrospagos='0' ";
$cc0= ejecutarConsultaSimpleFila($sqlc0);
$sqlc1="SELECT COUNT(*) AS c FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' AND tipopago='CREDITO' AND otrospagos='1' ";
$cc1= ejecutarConsultaSimpleFila($sqlc1);
$sqlc2="SELECT COUNT(*) AS c FROM caja_ventapago WHERE idventa='$idventa' AND nivel='$nivel' AND tipopago='CREDITO' AND otrospagos='2' ";
$cc2= ejecutarConsultaSimpleFila($sqlc2);

$totalfinal='0';
$detraccion='0';
$retencion='0';
$detraccionpagado='0';
$retencionpagado='0';
$tcambio_doc='0';
$tipo_pago_doc='';

if($nivel=='0'){
	$sqlv="SELECT *FROM venta WHERE idventa='$idventa' ";
	$mos2= ejecutarConsultaSimpleFila($sqlv);

	// Tipo cambio / tipo pago (si existen columnas)
	if(isset($mos2['tipocambio'])){ $tcambio_doc=$mos2['tipocambio']; }
	if(isset($mos2['tipo_pago'])){ $tipo_pago_doc=$mos2['tipo_pago']; }
	if(isset($mos2['txtID_TIPO_PAGO']) && $tipo_pago_doc===''){ $tipo_pago_doc=$mos2['txtID_TIPO_PAGO']; }

	// Pagos DET/RET según moneda del documento
	if($mos2['txtID_MONEDA']=='PEN'){
		$detraccionpagado=(float)$det3['total'];
		$retencionpagado=(float)$det4['total'];
	}else{
		$detraccionpagado=(float)$det3['totald'];
		$retencionpagado=(float)$det4['totald'];
	}

	$totalfinal=$mos2['txtTOTAL']-$mos2['detraccion']-$mos2['retencion'];
	$detraccion=$mos2['detraccion'];
	$retencion=$mos2['retencion'];

}else if($nivel=='3'){
	$sql2="SELECT percibido AS txtTOTAL, moneda AS txtID_MONEDA FROM percepcion p INNER JOIN percepcion_det d ON p.id=d.idpercepcion  WHERE p.id='$idventa' ";
	$mos2= ejecutarConsultaSimpleFila($sql2);
}else{
	$sql2="SELECT total_compra AS txtTOTAL, moneda AS txtID_MONEDA FROM ingreso WHERE idingreso='$idventa' ";
	$mos2= ejecutarConsultaSimpleFila($sql2);
	$totalfinal=$mos2['txtTOTAL'];
}

if($mos2['txtID_MONEDA']=='PEN'){
	$saldototal=(float)$mos['total'];
}else{
	$saldototal=(float)$mos['totald'];
}

$saldo=$totalfinal-$saldototal;
$saldo=round($saldo, 2);

// Pendientes por concepto
$det_pend = round(((float)$detraccion - (float)$detraccionpagado), 2);
if($det_pend < 0) $det_pend = 0;
$ret_pend = round(((float)$retencion - (float)$retencionpagado), 2);
if($ret_pend < 0) $ret_pend = 0;

$jsondata['estado'] = '1';
$jsondata['MOENDA'] = $mos2['txtID_MONEDA'];
$jsondata['saldo'] = $saldo;
$jsondata['pagado'] = round($saldototal, 2);
$jsondata['total_doc'] = round($totalfinal, 2);

$jsondata['detraccion'] = round((float)$detraccion, 2);
$jsondata['retencion'] = round((float)$retencion, 2);
$jsondata['det_pagado'] = round((float)$detraccionpagado, 2);
$jsondata['ret_pagado'] = round((float)$retencionpagado, 2);
$jsondata['det_pendiente'] = $det_pend;
$jsondata['ret_pendiente'] = $ret_pend;
$jsondata['pendiente'] = $saldo;

// Totales para validación client-side
$jsondata['total_doc'] = (float)$totalfinal;
$jsondata['pagado_doc'] = (float)$saldototal;
$jsondata['det_pagado'] = (float)$detraccionpagado;
$jsondata['ret_pagado'] = (float)$retencionpagado;

$jsondata['tcambio'] = $tcambio_doc;
$jsondata['tipo_pago'] = $tipo_pago_doc;
$jsondata['medio_pago'] = isset($mos2['medio_pago']) ? $mos2['medio_pago'] : '';
$jsondata['fpago_mpago'] = isset($mos2['fpago_mpago']) ? $mos2['fpago_mpago'] : '';

$jsondata['cuotas_reg'] = (int)$cc0['c'];
$jsondata['det_reg'] = (int)$cc1['c'];
$jsondata['ret_reg'] = (int)$cc2['c'];
$jsondata['nivel'] = $nivel;

echo json_encode($jsondata);
exit();
break;
case 'eliminapago':

$jsondata = array();		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

$jsondata['estado'] = '0';
$jsondata['saldo'] ='NO SE PUDO ELIMINAR';

// Acepta id por GET o POST (id del registro en caja_ventapago)
$idpago = '';
if(isset($_GET['id'])){ $idpago = limpiarCadena($_GET['id']); }
if($idpago=='' && isset($_POST['id'])){ $idpago = limpiarCadena($_POST['id']); }
if($idpago=='' && isset($_POST['idventa'])){ $idpago = limpiarCadena($_POST['idventa']); } // compat

if($idpago==''){
	$jsondata['saldo'] = 'Falta id de pago';
	echo json_encode($jsondata);
	exit();
}

// Traer registro para reverso de caja (si aplica)
$caja = ejecutarConsultaSimpleFila("SELECT * FROM caja_ventapago WHERE id='$idpago' LIMIT 1");

if($caja && isset($caja['id'])){
	$sqlt="SELECT *FROM caja_tipopago WHERE id='".$caja['idtipo']."' ";
	$tipo= ejecutarConsultaSimpleFila($sqlt);

	if($tipo && isset($tipo['pagoforma']) && $tipo['pagoforma']=='CONTADO' && isset($tipo['pagomedio']) && $tipo['pagomedio']=='008'){
		if((string)$caja['nivel']=='0'){
			agregarcaja((float)$caja['montosoles'], 'RESTA', 'ENTRADA', $_COOKIE['idusuario']);
		}else{
			agregarcaja((float)$caja['montosoles'], 'SUMA', 'SALIDA', $_COOKIE['idusuario']);	
		}
	}
}

$eliminar=ejecutarConsulta("DELETE FROM caja_ventapago WHERE id='$idpago' ");

if($eliminar){	
	$jsondata['estado'] = '1';
	$jsondata['saldo'] ='ELIMINADO CORRECTAMENTE';
}		
echo json_encode($jsondata);	

exit();		
break;

// ======================================================
// REQ-2 (venta.php):
// - Mostrar modal "DATOS DE PAGO" cargando info desde caja_ventapago
// - Permitir actualizar (cobrar) y marcar estado=1
// Endpoints:
//   op=getpagodatos   (GET id)
//   op=updatepagodatos (POST id, fechaoperacion, operacion, montooperacion, comentarios, idtipo)
// ======================================================

case 'getpagodatos':
	$jsondata = array();
	header("HTTP/1.1");
	header("Content-Type: application/json; charset=UTF-8");

	$jsondata['estado'] = '0';
	$jsondata['mensaje'] = 'No se pudo obtener el pago';

	$idpago = '';
	if(isset($_GET['id'])){ $idpago = limpiarCadena($_GET['id']); }
	if($idpago==''){
		$jsondata['mensaje'] = 'Falta id de pago';
		echo json_encode($jsondata);
		exit();
	}

	$caja = ejecutarConsultaSimpleFila("SELECT * FROM caja_ventapago WHERE id='$idpago' LIMIT 1");
	if(!$caja || !isset($caja['id'])){
		$jsondata['mensaje'] = 'Pago no encontrado';
		echo json_encode($jsondata);
		exit();
	}

	// Monto a mostrar según moneda
	$montoMostrar = ((string)$caja['moneda'] === 'USD') ? (string)$caja['montodolares'] : (string)$caja['montosoles'];
	if($montoMostrar === '' || $montoMostrar === null){ $montoMostrar = '0.00'; }

	// Traer moneda/total de la venta (para mostrar en el modal)
	$venta_moneda = '';
	$venta_total = '';
	$idventa_pago = (isset($caja['idventa']) ? (string)$caja['idventa'] : '');
	if($idventa_pago !== ''){
		// Nota: en este sistema venta.txtID_MONEDA suele ser 'PEN' o 'USD'
		$rowv = ejecutarConsultaSimpleFila("SELECT txtID_MONEDA AS moneda, txtTOTAL AS total FROM venta WHERE idventa='$idventa_pago' LIMIT 1");
		if($rowv){
			if(isset($rowv['moneda'])){ $venta_moneda = (string)$rowv['moneda']; }
			if(isset($rowv['total'])){ $venta_total = (string)$rowv['total']; }
		}
	}

	$jsondata['estado'] = '1';
	$jsondata['mensaje'] = 'OK';
	$jsondata['data'] = array(
		'id' => (string)$caja['id'],
		'idventa' => (isset($caja['idventa']) ? (string)$caja['idventa'] : ''),
		'fechaoperacion' => (isset($caja['fechaoperacion']) ? (string)$caja['fechaoperacion'] : ''),
		'operacion' => (isset($caja['operacion']) ? (string)$caja['operacion'] : ''),
		'moneda' => (isset($caja['moneda']) ? (string)$caja['moneda'] : 'PEN'),
		'venta_moneda' => $venta_moneda,
		'venta_total' => $venta_total,
		'montooperacion' => (string)$montoMostrar,
		'comentarios' => (isset($caja['comentarios']) ? (string)$caja['comentarios'] : ''),
		'idtipo' => (isset($caja['idtipo']) ? (string)$caja['idtipo'] : '')
	);

	echo json_encode($jsondata);
	exit();
	break;

case 'validar_credito_envio':
	// Valida si un cliente puede enviar una venta CREDITO a SUNAT.
	// Regla: solo enviar si persona.venta_pago existe y es > 0.
	header("HTTP/1.1");
	header("Content-Type: application/json; charset=UTF-8");
	$r = array('ok'=>false, 'habilitar_envio'=>false, 'venta_pago'=>0, 'mensaje'=>'');
	$idcliente = isset($_POST['idcliente']) ? trim((string)$_POST['idcliente']) : '';
	if($idcliente === '' || !ctype_digit($idcliente)){
		$r['mensaje'] = 'Cliente inválido';
		echo json_encode($r);
		exit();
	}
	$row = ejecutarConsultaSimpleFila("SELECT IFNULL(venta_pago,0) AS venta_pago FROM persona WHERE idpersona='$idcliente' LIMIT 1");
	if(!$row){
		$r['mensaje'] = 'Cliente no encontrado';
		echo json_encode($r);
		exit();
	}
	$vp = (float)$row['venta_pago'];
	$r['venta_pago'] = $vp;
	$r['ok'] = true;
	if($vp > 0){
		$r['habilitar_envio'] = true;
		$r['mensaje'] = 'OK';
	}else{
		$r['habilitar_envio'] = false;
		$r['mensaje'] = 'Falta registrar las cuotas del cliente (venta_pago) para poder enviar a SUNAT, porque la venta es CREDITO.';
	}
	echo json_encode($r);
	exit();
break;


case 'updatepagodatos':
	$jsondata = array();
	header("HTTP/1.1");
	header("Content-Type: application/json; charset=UTF-8");

	$jsondata['estado'] = '0';
	$jsondata['mensaje'] = 'No se pudo actualizar el pago';

	$idpago = '';
	if(isset($_POST['id'])){ $idpago = limpiarCadena($_POST['id']); }
	if($idpago==''){
		$jsondata['mensaje'] = 'Falta id de pago';
		echo json_encode($jsondata);
		exit();
	}

	$fechaoperacion = isset($_POST['fechaoperacion']) ? limpiarCadena($_POST['fechaoperacion']) : '';
	$operacion = isset($_POST['operacion']) ? limpiarCadena($_POST['operacion']) : '';
	$montooperacion = isset($_POST['montooperacion']) ? limpiarCadena($_POST['montooperacion']) : '';
	$comentarios = isset($_POST['comentarios']) ? limpiarCadena($_POST['comentarios']) : '';
	$idtipo = isset($_POST['idtipo']) ? limpiarCadena($_POST['idtipo']) : '';

	// Validaciones
	if($fechaoperacion==''){ $jsondata['mensaje'] = 'La fecha de operación es obligatoria.'; echo json_encode($jsondata); exit(); }
	if($operacion==''){ $jsondata['mensaje'] = 'El N° de operación es obligatorio.'; echo json_encode($jsondata); exit(); }
	if($idtipo==''){ $jsondata['mensaje'] = 'Debe seleccionar el medio de pago.'; echo json_encode($jsondata); exit(); }

	$montoNum = (float)str_replace(',', '', $montooperacion);
	if(!($montoNum > 0)){
		$jsondata['mensaje'] = 'El monto debe ser mayor a 0.';
		echo json_encode($jsondata);
		exit();
	}

	// Validar que exista el pago
	$caja = ejecutarConsultaSimpleFila("SELECT * FROM caja_ventapago WHERE id='$idpago' LIMIT 1");
	if(!$caja || !isset($caja['id'])){
		$jsondata['mensaje'] = 'Pago no encontrado.';
		echo json_encode($jsondata);
		exit();
	}

	// Validar idtipo exista y activo
	$tp = ejecutarConsultaSimpleFila("SELECT id FROM caja_tipopago WHERE idempresa='$_COOKIE[id]' AND estado='1' AND id='$idtipo' LIMIT 1");
	if(!$tp || !isset($tp['id'])){
		$jsondata['mensaje'] = 'El medio de pago seleccionado no es válido.';
		echo json_encode($jsondata);
		exit();
	}

	// Tipo de cambio (último <= fechaoperacion)
	$dol = ejecutarConsultaSimpleFila("SELECT venta FROM tipo_cambio WHERE fecha<='$fechaoperacion' ORDER BY fecha DESC LIMIT 1");
	$tc = 1;
	if($dol && isset($dol['venta']) && (float)$dol['venta'] > 0){ $tc = (float)$dol['venta']; }

	$moneda = isset($caja['moneda']) ? (string)$caja['moneda'] : 'PEN';
	$montosoles = 0;
	$montodolares = 0;
	if($moneda === 'USD'){
		$montodolares = $montoNum;
		$montosoles = $montoNum * $tc;
	}else{
		$montosoles = $montoNum;
		$montodolares = ($tc > 0) ? ($montoNum / $tc) : 0;
	}
	$montosoles = round($montosoles, 2);
	$montodolares = round($montodolares, 2);

	// Actualizar: se marca como cobrado (estado=1)
	$idusuario = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
	$sqlu = "UPDATE caja_ventapago SET 
		estado='1',
		fechaoperacion='$fechaoperacion',
		operacion='$operacion',
		montosoles='$montosoles',
		montodolares='$montodolares',
		fecha_pago='$fechaoperacion',
		idtipo='$idtipo',
		comentarios='$comentarios',
		idusuario='$idusuario'
		WHERE id='$idpago'";

	$ok = ejecutarConsulta($sqlu);
	if($ok){
		$jsondata['estado'] = '1';
		$jsondata['mensaje'] = 'Pago actualizado y cobrado correctamente.';
	}else{
		$jsondata['mensaje'] = 'No se pudo actualizar el pago. Verifique e intente nuevamente.';
	}

	echo json_encode($jsondata);
	exit();
	break;

case 'marcapagado':
		
$jsondata = array();		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
// id del registro en caja_ventapago
$idpago = '';
if(isset($_GET['id'])){ $idpago = limpiarCadena($_GET['id']); }
if($idpago=='' && isset($_POST['id'])){ $idpago = limpiarCadena($_POST['id']); }
if($idpago==''){
	$jsondata['saldo'] = 'Falta id de pago';
	echo json_encode($jsondata);
	exit();
}

// Parámetros opcionales (si no vienen, se toman del registro)
$operacion = isset($_GET['operacion']) ? limpiarCadena($_GET['operacion']) : '';
$nivel = isset($_GET['nivel']) ? limpiarCadena($_GET['nivel']) : '';
$fecha = isset($_GET['fecha']) ? limpiarCadena($_GET['fecha']) : '';
$montooperacion = isset($_GET['montooperacion']) ? limpiarCadena($_GET['montooperacion']) : '';
$mediopago = isset($_GET['mediopago']) ? limpiarCadena($_GET['mediopago']) : '';
$pagocomentarios = isset($_GET['pagocomentarios']) ? limpiarCadena($_GET['pagocomentarios']) : '';

// Traer registro para completar datos faltantes
$caja = ejecutarConsultaSimpleFila("SELECT * FROM caja_ventapago WHERE id='$idpago' LIMIT 1");
if(!$caja || !isset($caja['id'])){
	$jsondata['saldo'] = 'Pago no encontrado';
	echo json_encode($jsondata);
	exit();
}
if($operacion===''){ $operacion = (string)$caja['operacion']; }
if($nivel===''){ $nivel = (string)$caja['nivel']; }
if($fecha===''){ $fecha = date('Y-m-d'); }
if($montooperacion===''){
	$montooperacion = ((string)$caja['moneda']==='USD') ? (string)$caja['montodolares'] : (string)$caja['montosoles'];
}
if($mediopago===''){ $mediopago = (string)$caja['idtipo']; }
if($pagocomentarios===''){ $pagocomentarios = (string)$caja['comentarios']; }

// Compat: variables esperadas en código antiguo
$idventa = $idpago;


$fechaActual = date('Y-m-d');

$jsondata['estado'] = '0';
$jsondata['saldo'] ='NO SE PUDO PAGAR';

$sqldol="SELECT *FROM  tipo_cambio WHERE fecha<='$idventa' ";
$dol= ejecutarConsultaSimpleFila($sqldol);

$sqlsgui="SELECT *FROM caja_ventapago WHERE id='$idventa' ";
$caja= ejecutarConsultaSimpleFila($sqlsgui);

if($caja['nivel']=='0'){
$sqlvent="SELECT tipo_pago, txtID_MONEDA FROM venta WHERE idventa='$caja[idventa]' ";
$vent= ejecutarConsultaSimpleFila($sqlvent);

$montosoles=$montooperacion;
$montodolar=$montooperacion/$dol['venta'];

if($vent['txtID_MONEDA']=='USD'){
	$montosoles=$montooperacion*$dol['venta'];
	$montodolar=$montooperacion;
}

}else{
$sqlvent="SELECT moneda, tipopago AS tipo_pago FROM compra WHERE id='$caja[idventa]' ";
$vent= ejecutarConsultaSimpleFila($sqlvent);

	$montosoles=$montooperacion;
	$montodolar=$montooperacion/$dol['venta'];
	
	if($vent['moneda']=='USD'){
		$montosoles=$montooperacion*$dol['venta'];
		$montodolar=$montooperacion;
	}
}

$sqlp="UPDATE caja_ventapago SET estado='1', operacion='$operacion', fechaoperacion='$fechaActual', montosoles='$montosoles', montodolares='$montodolar', fecha_pago='$fecha', idtipo='$mediopago', idusuario='$_COOKIE[idusuario]', comentarios='$pagocomentarios' WHERE id='$idventa' ";	
$sqlt="SELECT *FROM caja_tipopago WHERE id='$caja[idtipo]' ";
$tipo= ejecutarConsultaSimpleFila($sqlt);

if($nivel=='0'){
$sqlventas="SELECT txtID_CLIENTE FROM venta WHERE idventa='$caja[idventa]' ";
$ventas= ejecutarConsultaSimpleFila($sqlventas);

credito($caja['montosoles'], 'RESTA', $ventas['txtID_CLIENTE']);
}

if($tipo['pagoforma']=='CONTADO'&&$tipo['pagomedio']=='008'){
if($nivel=='0'){
agregarcaja($caja['montosoles'], 'SUMA', 'ENTRADA', $_COOKIE['idusuario']);
}else{
agregarcaja($caja['montosoles'], 'RESTA', 'SALIDA', $_COOKIE['idusuario']);	
}

}



$eliminar=ejecutarConsulta($sqlp);
if($eliminar){	
$jsondata['estado'] = '1';
$jsondata['saldo'] ='PAGADO CORRECTAMENTE';
$jsondata['tipopago'] =$vent['tipo_pago'];
}		
echo json_encode($jsondata);	
		
exit();		
break;
		
case 'descuento':
		
$jsondata = array();		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
		
$jsondata['estado'] = '1';
		
$sql2="SELECT *FROM persona WHERE idpersona='$id' ";
$mos2= ejecutarConsultaSimpleFila($sql2);
		
if($mos2['descuentom']==''){
$jsondata['descuentom'] ='NO';
}else{
$jsondata['descuentom'] = $mos2['descuentom'];
}	
$jsondata['descuento'] = $mos2['descuento'];
		
echo json_encode($jsondata);	
		
exit();		
break;

/*
ANTICIPOS
*/		

case 'anticipocabeza':
		
$jsondata = array();		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
		
$sql2="SELECT *FROM venta WHERE idventa='$id' ";
$mos2= ejecutarConsultaSimpleFila($sql2);
		
$serienum=$mos2['txtSERIE'].'-'.$mos2['txtNUMERO'];
		
$sql22="SELECT sum(txtTOTAL) as total FROM venta WHERE referencia='$serienum' AND idempresa='$mos2[idempresa]' AND txtID_TIPO_DOCUMENTO='01' ";
$mos22= ejecutarConsultaSimpleFila($sql22);
		
$sqld="SELECT * FROM detalle_venta WHERE idventa='$id' AND (tipo='1' OR tipo='0') ";
$det= ejecutarConsultaSimpleFila($sqld);

$tot='0.00';
if($mos22['total']!=''){ $tot=$mos22['total']; }
		
$pagado=round($anticipo+$tot, 2);
		
$tit='PAGO ANTICIPADO POR';
$porpagar=round($mos2['txtTOTAL']-$pagado, 2);

if($porpagar=='0'){ $tit='PAGO FINAL POR'; $idanticipo='2'; }else{ $idanticipo='1'; }
if($pagado>$mos2['txtTOTAL']){ $idanticipo='3'; }

$jsondata['id']=$id;	
$jsondata['referencia']=$serienum;
$jsondata['total']=$mos2['txtTOTAL'];
$jsondata['pagado']=$pagado;
$jsondata['saldo']=$porpagar;
$jsondata['idanticipo']=$idanticipo;
$jsondata['docmodifica_tipo']=$mos2['doc_relaciona'];
$jsondata['txtID_MONEDA']=$mos2['txtID_MONEDA'];
$jsondata['txtID_CLIENTE']=$mos2['txtID_CLIENTE'];
$jsondata['txtID_TIPO_DOCUMENTO']=$mos2['doc_relaciona'];
$jsondata['idproducto']=$det['idproducto'];
$jsondata['codigoproducto']=$det['codigoproducto'];
$jsondata['unidadmedida']=$det['unidadmedida'];
$jsondata['nombreproducto']=$tit.': '.$det['nombreproducto'].' |DOC REL: '.$serienum.' |TOTAL PAGADO: '.$pagado.' |MONTO TOTAL: '.$mos2['txtTOTAL'];
$jsondata['exoneradod']=$det['exoneradod'];
$jsondata['num']='4545';
$jsondata['tipo']=$det['tipo'];
	
echo json_encode($jsondata);	
		
exit();		
break;
		
case 'destinotransporte':		
$html = '';
 
$id_category = $_POST['id_category'];
 	
$html .= '<option value="0" selected >SELECCIONE</option>';
		
$results=ejecutarConsulta("SELECT * FROM transporte_ruta WHERE idruta='$id_category' ");
while ($reg=$results->fetch_object()){

$html .= '<option value="'.$reg->id.'|'.$reg->parcial.'|'.$reg->acumulada.'|'.$reg->monto.'">'.$reg->ruta.'</option>';

}		
echo $html;			
exit();		
break;

case 'buscarcliente':

        if ($_COOKIE['id'] == '1') {


            $tipodoc = $_GET['tipodoc'];
            $json = [];
            if ($tipodoc == 'RUC' || $tipodoc == 'DNI') {
                $documento = ' AND tipo_documento="' . $tipodoc . '"';
            } else {
                $documento = '';
            }

            if (!isset($_GET['searchTerm'])) {
                $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente' and idempresa='$_COOKIE[id]' $documento LIMIT 10 ";
                $rspta = ejecutarConsulta($sql);
                while ($row = $rspta->fetch_object()) {

                    $nombrecliente = $row->nombre;
                    $codhistoria = $row->codigo;
                    $json[] = ['id' => $row->idpersona, 'text' => $row->txtID_CLIENTE . '-' . $nombrecliente . '(' . $codhistoria . ')'];
                }
            } else {
                $search = $_GET['searchTerm'];
                $json = [];

                $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente' and idempresa='$_COOKIE[id]' $documento AND (nombre LIKE '%" . $search . "%' OR txtID_CLIENTE LIKE '%" . $search . "%') LIMIT 20 ";
                $rspta = ejecutarConsulta($sql);
                while ($row = $rspta->fetch_object()) {

                    $nombrecliente = $row->nombre;
                    $codhistoria = $row->codigo;
                    $json[] = ['id' => $row->idpersona, 'text' => $row->txtID_CLIENTE . '-' . $nombrecliente . '(' . $codhistoria . ')'];
                }
            }
            echo json_encode($json);

        } else {

            $tipodoc = $_GET['tipodoc'];
            $json = [];
            if ($tipodoc == 'RUC' || $tipodoc == 'DNI') {
                $documento = ' AND tipo_documento="' . $tipodoc . '"';
            } else {
                $documento = '';
            }

            if (!isset($_GET['searchTerm'])) {
                $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente' and idempresa='$_COOKIE[id]' $documento LIMIT 10 ";
                $rspta = ejecutarConsulta($sql);
                while ($row = $rspta->fetch_object()) {

                    $nombrecliente = $row->nombre;
                    $codhistoria = $row->codigo;
                    $json[] = ['id'=>$row->idpersona, 'text'=>$row->txtID_CLIENTE.'-'.$nombrecliente];
                }
            } else {
                $search = $_GET['searchTerm'];
                $json = [];

                $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente' and idempresa='$_COOKIE[id]' $documento AND (nombre LIKE '%" . $search . "%' OR txtID_CLIENTE LIKE '%" . $search . "%') LIMIT 20 ";
                $rspta = ejecutarConsulta($sql);
                while ($row = $rspta->fetch_object()) {

                    $nombrecliente = $row->nombre;
                    $codhistoria = $row->codigo;
                    $json[] = ['id'=>$row->idpersona, 'text'=>$row->txtID_CLIENTE.'-'.$nombrecliente];

                }
            }
            echo json_encode($json);
        }
break;

case 'buscarclienteremitente':

        if ($_COOKIE['id'] == '1') {


            $tipodoc = $_GET['tipodoc'];
            $json = [];
            if ($tipodoc == 'RUC' || $tipodoc == 'DNI') {
                $documento = ' AND tipo_documento="' . $tipodoc . '"';
            } else {
                $documento = '';
            }

            if (!isset($_GET['searchTerm'])) {
                $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente' and idempresa='$_COOKIE[id]' and txtID_CLIENTE='$_COOKIE[ruc]' $documento LIMIT 10 ";
                $rspta = ejecutarConsulta($sql);
                while ($row = $rspta->fetch_object()) {

                    $nombrecliente = $row->nombre;
                    $codhistoria = $row->codigo;
                    $json[] = ['id' => $row->idpersona, 'text' => $row->txtID_CLIENTE . '-' . $nombrecliente . '(' . $codhistoria . ')'];
                }
            } else {
                $search = $_GET['searchTerm'];
                $json = [];

                $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente' and idempresa='$_COOKIE[id]' and txtID_CLIENTE='$_COOKIE[ruc]' $documento AND (nombre LIKE '%" . $search . "%' OR txtID_CLIENTE LIKE '%" . $search . "%') LIMIT 20 ";
                $rspta = ejecutarConsulta($sql);
                while ($row = $rspta->fetch_object()) {

                    $nombrecliente = $row->nombre;
                    $codhistoria = $row->codigo;
                    $json[] = ['id' => $row->idpersona, 'text' => $row->txtID_CLIENTE . '-' . $nombrecliente . '(' . $codhistoria . ')'];
                }
            }
            echo json_encode($json);

        } else {

            $tipodoc = $_GET['tipodoc'];
            $json = [];
            if ($tipodoc == 'RUC' || $tipodoc == 'DNI') {
                $documento = ' AND tipo_documento="' . $tipodoc . '"';
            } else {
                $documento = '';
            }

            if (!isset($_GET['searchTerm'])) {
                $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente' and idempresa='$_COOKIE[id]' and txtID_CLIENTE='$_COOKIE[ruc]' $documento LIMIT 10 ";
                $rspta = ejecutarConsulta($sql);
                while ($row = $rspta->fetch_object()) {

                    $nombrecliente = $row->nombre;
                    $codhistoria = $row->codigo;
                    $json[] = ['id'=>$row->idpersona, 'text'=>$row->txtID_CLIENTE.'-'.$nombrecliente];
                }
            } else {
                $search = $_GET['searchTerm'];
                $json = [];

                $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente' and idempresa='$_COOKIE[id]' and txtID_CLIENTE='$_COOKIE[ruc]' $documento AND (nombre LIKE '%" . $search . "%' OR txtID_CLIENTE LIKE '%" . $search . "%') LIMIT 20 ";
                $rspta = ejecutarConsulta($sql);
                while ($row = $rspta->fetch_object()) {

                    $nombrecliente = $row->nombre;
                    $codhistoria = $row->codigo;
                    $json[] = ['id'=>$row->idpersona, 'text'=>$row->txtID_CLIENTE.'-'.$nombrecliente];

                }
            }
            echo json_encode($json);
        }
break;


    case 'buscarclienteatendido':


        if (!isset($_GET['searchTerm'])) {
            $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente' and idempresa='$_COOKIE[id]'  LIMIT 10 ";
            $rspta = ejecutarConsulta($sql);
            while ($row = $rspta->fetch_object()) {

                $nombrecliente = $row->nombre;
                $codhistoria = $row->codigo;
                $json[] = ['id' => $row->idpersona, 'text' => $row->txtID_CLIENTE . '-' . $nombrecliente . '(' . $codhistoria . ')'];
            }
        } else {
            $search = $_GET['searchTerm'];
            $json = [];

            $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente' and idempresa='$_COOKIE[id]' AND (nombre LIKE '%" . $search . "%' OR txtID_CLIENTE LIKE '%" . $search . "%') LIMIT 20 ";
            $rspta = ejecutarConsulta($sql);
            while ($row = $rspta->fetch_object()) {

                $nombrecliente = $row->nombre;
                $codhistoria = $row->codigo;
                $json[] = ['id' => $row->idpersona, 'text' => $row->txtID_CLIENTE . '-' . $nombrecliente . '(' . $codhistoria . ')'];
            }
        }
        echo json_encode($json);

        break;

case 'cajatipopagos':
	// Devuelve un mapa simple: { id : "descripcion" }
	header("Content-Type: application/json; charset=UTF-8");

	$empresa = isset($_COOKIE['id']) ? $_COOKIE['id'] : '';
	if($empresa===''){
		echo json_encode(new stdClass());
		exit();
	}

	$rows = array();
	$sql="SELECT id, descripcion FROM caja_tipopago WHERE idempresa='$empresa' AND estado='1' ORDER BY id ASC";
	$rspta = ejecutarConsulta($sql);
	if($rspta){
		while ($reg = $rspta->fetch_object()){
			$rows[(string)$reg->id] = (string)$reg->descripcion;
		}
	}

	echo json_encode($rows);
	exit();
	break;

case 'verificarprecio':
		
	$jsondata = array();		
	header("HTTP/1.1");
	header("Content-Type: application/json; charset=UTF-8");
			
	$jsondata['estado'] = '1';
			
	$sql2="SELECT *FROM articulo WHERE txtCOD_ARTICULO='$id' ";
	$mos2= ejecutarConsultaSimpleFila($sql2);
			
	if($precio>$mos2['precio_compra']){
	$jsondata['pasa'] ='SI';
	}else{
	$jsondata['pasa'] ='NO';
	$jsondata['mensaje'] ='EL PRECIO NO PUEDE SER MENOR AL COSTO';
	}	
echo json_encode($jsondata);	
			
	exit();		
	break;


case 'listarstock':

$idproducto=$_GET['id'];

$sql="SELECT * FROM sucursal  WHERE idempresa='$_COOKIE[id]' AND estado='1' AND idnivel='0' ORDER BY id ASC ";
$rspta = ejecutarConsulta($sql);
//Vamos a declarar un array
$data= Array();

while ($reg=$rspta->fetch_object()){

$sql2="SELECT *FROM articulo_stock WHERE idlocal='$reg->id' AND idarticulo='$idproducto' ";
$mos2= ejecutarConsultaSimpleFila($sql2);

$stock='0.00';
if($mos2){ $stock=$mos2['stock']; }

$data[]=array(
"0"=>$reg->id,
"1"=>$reg->sucursal,
"2"=>$stock
);
}
$results = array(
"sEcho"=>1, //Información para el datatables
"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
"aaData"=>$data);
echo json_encode($results);
break;



}
?>
