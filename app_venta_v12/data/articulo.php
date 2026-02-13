<?php
require'../config/conexion.php';
require_once "../modelos/Articulo.php";

if (strlen(session_id()) < 1) 
session_start();

$articulo=new Articulo();
$txtCOD_ARTICULO=isset($_POST["txtCOD_ARTICULO"])? limpiarCadena($_POST["txtCOD_ARTICULO"]):"";
$idcategoria=isset($_POST["idcategoria"])? limpiarCadena($_POST["idcategoria"]):"";
$idmarca=isset($_POST["idmarca"])? limpiarCadena($_POST["idmarca"]):"";
$tipoac=isset($_POST["tipoac"])? limpiarCadena($_POST["tipoac"]):"";
$idlocal=isset($_POST["idlocal"])? limpiarCadena($_POST["idlocal"]):"0";

$linea=isset($_POST["linea"])? limpiarCadena($_POST["linea"]):"";
$sublinea=isset($_POST["sublinea"])? limpiarCadena($_POST["sublinea"]):"";
$subfamilia=isset($_POST["subfamilia"])? limpiarCadena($_POST["subfamilia"]):"";

$idprincipio=isset($_POST["idprincipio"])? limpiarCadena($_POST["idprincipio"]):"0";
$idproveedor=isset($_POST["farmaceutica"])? limpiarCadena($_POST["farmaceutica"]):"0";
if($idproveedor=='null'){ $idproveedor='0'; }
$sanitario=isset($_POST["sanitario"])? limpiarCadena($_POST["sanitario"]):"0";

$codigo=isset($_POST["codigo"])? limpiarCadena($_POST["codigo"]):"";
$txtDESCRIPCION_ARTICULO=isset($_POST["txtDESCRIPCION_ARTICULO"])? limpiarCadena($_POST["txtDESCRIPCION_ARTICULO"]):"";
$stock=isset($_POST["stock"])? limpiarCadena($_POST["stock"]):"0";
$stockmin=isset($_POST["stockmin"])? limpiarCadena($_POST["stockmin"]):"5";
$stockmax=isset($_POST["stockmax"])? limpiarCadena($_POST["stockmax"]):"50";

$precio=isset($_POST["precio"])? limpiarCadena($_POST["precio"]):"0";
$precio_porcentaje=isset($_POST["precio_porcentaje"])? limpiarCadena($_POST["precio_porcentaje"]):"1";

$precio_mayor=isset($_POST["precio_mayor"])? limpiarCadena($_POST["precio_mayor"]):"0";
$precio_porcentaje2=isset($_POST["precio_porcentaje2"])? limpiarCadena($_POST["precio_porcentaje2"]):"1";

$precio_mayor2=isset($_POST["precio_mayor2"])? limpiarCadena($_POST["precio_mayor2"]):"0";
$precio_porcentaje3=isset($_POST["precio_porcentaje3"])? limpiarCadena($_POST["precio_porcentaje3"]):"1";

$precioc=isset($_POST["precioc"])? limpiarCadena($_POST["precioc"]):"";
$ctimayor=isset($_POST["ctimayor"])? limpiarCadena($_POST["ctimayor"]):"";

$imagen=isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]):"";
$medida=isset($_POST["medida"])? limpiarCadena($_POST["medida"]):"";
$medida2=isset($_POST["medida2"])? limpiarCadena($_POST["medida2"]):"";
$estado=isset($_POST["estado"])? limpiarCadena($_POST["estado"]):"";
$exonerado=isset($_POST["exonerado"])? limpiarCadena($_POST["exonerado"]):"";

$id=isset($_POST["codart"])? limpiarCadena($_POST["codart"]):"";
$serie=isset($_POST["serie"])? limpiarCadena($_POST["serie"]):"";
$lote=isset($_POST["lote"])? limpiarCadena($_POST["lote"]):"";
$fechven=isset($_POST["fechven"])? limpiarCadena($_POST["fechven"]):"";
$fecha=isset($_POST["fecha"])? limpiarCadena($_POST["fecha"]):"";
$comision=isset($_POST["comision"])? limpiarCadena($_POST["comision"]):"0.00";
$comisionm=isset($_POST["comisionm"])? limpiarCadena($_POST["comisionm"]):"0";
$comisionmp=isset($_POST["comisionmp"])? limpiarCadena($_POST["comisionmp"]):"0.00";

$codartu=isset($_POST["codartu"])? limpiarCadena($_POST["codartu"]):"0";

$ctiunidad=isset($_POST["ctiunidad"])? limpiarCadena($_POST["ctiunidad"]):"0";
$nombreu=isset($_POST["nombreu"])? limpiarCadena($_POST["nombreu"]):"0";
$preciou=isset($_POST["preciou"])? limpiarCadena($_POST["preciou"]):"0";
$comisionu=isset($_POST["comisionu"])? limpiarCadena($_POST["comisionu"]):"0";

$ctimayoru=isset($_POST["ctimayoru"])? limpiarCadena($_POST["ctimayoru"]):"0";
$preciomu=isset($_POST["preciomu"])? limpiarCadena($_POST["preciomu"]):"0";
$comisionmu=isset($_POST["comisionmu"])? limpiarCadena($_POST["comisionmu"]):"0";

$ctacompras=isset($_POST["ctacompras"])? limpiarCadena($_POST["ctacompras"]):"0";
$ctaventas=isset($_POST["ctaventas"])? limpiarCadena($_POST["ctaventas"]):"0";
$bolsa=isset($_POST["bolsa"])? limpiarCadena($_POST["bolsa"]):"0";
$existencia=isset($_POST["existencia"])? limpiarCadena($_POST["existencia"]):"";

$mes=isset($_POST["mes"])? limpiarCadena($_POST["mes"]):"";
$anio=isset($_POST["anio"])? limpiarCadena($_POST["anio"]):"";
$codigos=isset($_POST["codigos"])? limpiarCadena($_POST["codigos"]):"0";

$idproveedora=isset($_POST["idproveedora"])? limpiarCadena($_POST["idproveedora"]):"";
$stockser=isset($_POST["stockser"])? limpiarCadena($_POST["stockser"]):"";

$exonerado=isset($_POST["exonerado"])? limpiarCadena($_POST["exonerado"]):"";
$idsec=isset($_POST["idsec"])? limpiarCadena($_POST["idsec"]):"0";
$idsec2=isset($_POST["id"])? limpiarCadena($_POST["id"]):"0";
$moneda=isset($_POST["moneda"])? limpiarCadena($_POST["moneda"]):"PEN";
$pactivo=isset($_POST["pactivo"])? limpiarCadena($_POST["pactivo"]):"";


$idproducto=isset($_POST["idproducto"])? limpiarCadena($_POST["idproducto"]):"0";
$idsub=isset($_POST["idsub"])? limpiarCadena($_POST["idsub"]):"0";
$cantidad=isset($_POST["cantidad"])? limpiarCadena($_POST["cantidad"]):"0";

$oferta=isset($_POST["oferta"])? limpiarCadena($_POST["oferta"]):"0.00";
$canje=isset($_POST["canje"])? limpiarCadena($_POST["canje"]):"NO";
$canjepuntos=isset($_POST["canjepuntos"])? limpiarCadena($_POST["canjepuntos"]):"0";
$canjecobro=isset($_POST["canjecobro"])? limpiarCadena($_POST["canjecobro"]):"0";
$cat=isset($_POST["cat"])? limpiarCadena($_POST["cat"]):"0";

$pmayor=isset($_POST["mayor"])? limpiarCadena($_POST["mayor"]):"0";
$idclienteproducto=isset($_POST["idclienteproducto"])? limpiarCadena($_POST["idclienteproducto"]):"0";

$hoy = date("Y-m-d"); 

switch ($_GET["op"]){

case 'guardaryeditar':

  // ==========================================================
  //  GUARDAR / EDITAR (JSON LIMPIO + SIN NOTICES)
  //  FIX: el UPDATE sí se ejecutaba, pero el JS caía en "error"
  //       porque la respuesta NO era JSON puro (notices/warnings).
  // ==========================================================
  @header('Content-Type: application/json; charset=utf-8');

  // Evitar que notices/warnings contaminen el JSON (en servers con display_errors=On)
  @ini_set('display_errors', '0');
  @ini_set('display_startup_errors', '0');
  @error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

  $jsondata = ['estado' => '0', 'mensaje' => ''];

  // Si algo se imprimió antes (BOM, espacios), límpialo
  if (function_exists('ob_get_length') && ob_get_length()) { @ob_clean(); }

  // =======================
  // 1) Normalizar entradas
  // =======================
  $idempresa = isset($_COOKIE['id']) ? (int)$_COOKIE['id'] : 0;
  $tipoac    = isset($_POST['tipoac']) ? trim((string)$_POST['tipoac']) : (isset($_GET['tipoac']) ? trim((string)$_GET['tipoac']) : '');
  $codigo    = isset($_POST['codigo']) ? trim((string)$_POST['codigo']) : '';
  $precio    = isset($_POST['precio']) ? trim((string)$_POST['precio']) : '0';

  // Edición real
  $txtCOD_ARTICULO = isset($_POST['txtCOD_ARTICULO']) ? (int)$_POST['txtCOD_ARTICULO'] : 0;

  // Variables de formulario (DEFAULTS para evitar Undefined variable)
  $idcategoria = isset($_POST['idcategoria']) ? (int)$_POST['idcategoria'] : 0;
  $idmarca     = isset($_POST['idmarca']) ? (int)$_POST['idmarca'] : 0;
  $txtDESCRIPCION_ARTICULO = isset($_POST['txtDESCRIPCION_ARTICULO']) ? trim((string)$_POST['txtDESCRIPCION_ARTICULO']) : '';
  $stock       = isset($_POST['stock']) ? trim((string)$_POST['stock']) : '0';
  $stockmin    = isset($_POST['stockmin']) ? trim((string)$_POST['stockmin']) : '0';
  $stockmax    = isset($_POST['stockmax']) ? trim((string)$_POST['stockmax']) : '0';
  $precio_porcentaje  = isset($_POST['precio_porcentaje']) ? trim((string)$_POST['precio_porcentaje']) : '1';
  $ctimayor     = isset($_POST['ctimayor']) ? trim((string)$_POST['ctimayor']) : '99999999';
  $precio_mayor = isset($_POST['precio_mayor']) ? trim((string)$_POST['precio_mayor']) : '0';
  $precio_mayor2 = isset($_POST['precio_mayor2']) ? trim((string)$_POST['precio_mayor2']) : '0';
  $precio_porcentaje2 = isset($_POST['precio_porcentaje2']) ? trim((string)$_POST['precio_porcentaje2']) : '1';
  $precio_porcentaje3 = isset($_POST['precio_porcentaje3']) ? trim((string)$_POST['precio_porcentaje3']) : '1';

  // imagen: si no suben nueva, usa imagenactual (o vacío)
  $imagen = '';
  if (isset($_POST['imagenactual'])) $imagen = trim((string)$_POST['imagenactual']);
  if (isset($_POST['imagen']) && trim((string)$_POST['imagen']) !== '') $imagen = trim((string)$_POST['imagen']);

  $medida     = isset($_POST['medida']) ? trim((string)$_POST['medida']) : '';
  $exonerado  = isset($_POST['exonerado']) ? trim((string)$_POST['exonerado']) : '0';
  $comision   = isset($_POST['comision']) ? trim((string)$_POST['comision']) : '0';
  $comisionm  = isset($_POST['comisionm']) ? trim((string)$_POST['comisionm']) : '99999999';
  $comisionmp = isset($_POST['comisionmp']) ? trim((string)$_POST['comisionmp']) : '0';
  $codigos    = isset($_POST['codigos']) ? trim((string)$_POST['codigos']) : '';
  $idproveedor= isset($_POST['idproveedor']) ? trim((string)$_POST['idproveedor']) : '';
  $sanitario  = isset($_POST['sanitario']) ? trim((string)$_POST['sanitario']) : '';
  $precioc    = isset($_POST['precioc']) ? trim((string)$_POST['precioc']) : '0';
  $linea      = isset($_POST['linea']) ? trim((string)$_POST['linea']) : '0';
  $sublinea   = isset($_POST['sublinea']) ? trim((string)$_POST['sublinea']) : '0';
  $subfamilia = isset($_POST['subfamilia']) ? trim((string)$_POST['subfamilia']) : '0';
  $ctacompras = isset($_POST['ctacompras']) ? trim((string)$_POST['ctacompras']) : '';
  $ctaventas  = isset($_POST['ctaventas']) ? trim((string)$_POST['ctaventas']) : '';
  $bolsa      = isset($_POST['bolsa']) ? trim((string)$_POST['bolsa']) : '0';
  $existencia = isset($_POST['existencia']) ? trim((string)$_POST['existencia']) : '';
  $idlocal    = isset($_POST['idlocal']) ? trim((string)$_POST['idlocal']) : (isset($_COOKIE['idlocal']) ? trim((string)$_COOKIE['idlocal']) : '');
  $moneda     = isset($_POST['moneda']) ? trim((string)$_POST['moneda']) : 'PEN';
  $oferta     = isset($_POST['oferta']) ? trim((string)$_POST['oferta']) : '0';

  $canje      = isset($_POST['canje']) ? trim((string)$_POST['canje']) : 'NO';
  $canjepuntos= isset($_POST['canjepuntos']) ? trim((string)$_POST['canjepuntos']) : '0';
  $canjecobro = isset($_POST['canjecobro']) ? trim((string)$_POST['canjecobro']) : '0';

  // Select múltiple: pactivo[] / farmaceutica[]
  $pactivo = '';
  if (isset($_POST['pactivo']) && is_array($_POST['pactivo'])) {
    $pactivo = implode(',', array_map('trim', $_POST['pactivo']));
  } elseif (isset($_POST['pactivo'])) {
    $pactivo = trim((string)$_POST['pactivo']);
  }

  // Tu modelo usa $idproveedor como string/array; aquí lo normalizamos
  if (isset($_POST['farmaceutica']) && is_array($_POST['farmaceutica'])) {
    $idproveedor = implode(',', array_map('trim', $_POST['farmaceutica']));
  }

  // ==============================
  // 2) Validación código duplicado
  // ==============================
  if ($tipoac == '2') {

    $sql = "SELECT 1 FROM articulo WHERE codigo='$codigo' AND idempresa='$idempresa' LIMIT 1";
    $art = ejecutarConsultaSimpleFila($sql);

    if ($art && $codigo !== '') {
      $jsondata['estado']  = '1';
      $jsondata['mensaje'] = 'EL CÓDIGO DEL PRODUCTO YA EXISTE';
      echo json_encode($jsondata);
      break;
    }

    // ====== INSERT (tu insertar original) ======
    // NOTA: respetamos tu firma original (cat, precio_mayor2, precio_porcentaje2, precio_porcentaje3, precio)
    $rspta = $articulo->insertar($cat, $precio_mayor2, $precio_porcentaje2, $precio_porcentaje3, $precio);

    // Obtener ID real
    $newId = 0;
    if (is_numeric($rspta)) {
      $newId = (int)$rspta;
    } else {
      if (isset($_COOKIE['idarticulo_real'])) $newId = (int)$_COOKIE['idarticulo_real'];
    }

    if ($newId <= 0) {
      $row = ejecutarConsultaSimpleFila("SELECT txtCOD_ARTICULO FROM articulo WHERE idempresa='$idempresa' AND codigo='$codigo' ORDER BY txtCOD_ARTICULO DESC LIMIT 1");
      if ($row && isset($row['txtCOD_ARTICULO'])) $newId = (int)$row['txtCOD_ARTICULO'];
    }

    if ($newId <= 0) {
      $jsondata['estado']  = '1';
      $jsondata['mensaje'] = 'ARTÍCULO GUARDADO, PERO NO SE PUDO OBTENER EL ID PARA VINCULAR IMÁGENES';
      echo json_encode($jsondata);
      break;
    }

    // ====== PASAR IMÁGENES TEMP -> REAL ======
    $tmpId = isset($_COOKIE['idarticulo']) ? (int)$_COOKIE['idarticulo'] : 0;

    if ($tmpId > 0) {
      ejecutarConsulta("
        INSERT INTO articulo_images (idempresa, id_serv, tit, cont, imag)
        SELECT idempresa, '$newId', tit, cont, imag
        FROM tmp_articuloimages
        WHERE idempresa='$idempresa' AND id_serv='$tmpId'
      ");

      ejecutarConsulta("DELETE FROM tmp_articuloimages WHERE idempresa='$idempresa' AND id_serv='$tmpId'");

      setcookie('idarticulo', '', time()-3600, "/");
      unset($_COOKIE['idarticulo']);
    }

    // Actualizar imagen principal
    $img = ejecutarConsultaSimpleFila("SELECT imag FROM articulo_images WHERE idempresa='$idempresa' AND id_serv='$newId' ORDER BY id DESC LIMIT 1");
    if ($img && isset($img['imag']) && $img['imag'] !== '') {
      ejecutarConsulta("UPDATE articulo SET imagen='".$img['imag']."' WHERE idempresa='$idempresa' AND txtCOD_ARTICULO='$newId' LIMIT 1");
    }

    $jsondata['estado']  = '0';
    $jsondata['mensaje'] = 'ARTÍCULO GUARDADO';
    $jsondata['idreal']  = $newId;
    $jsondata['v']       = time();
    echo json_encode($jsondata);
    break;

  } else {

    // EDICIÓN REAL
    $sql = "SELECT 1 FROM articulo WHERE codigo='$codigo' AND idempresa='$idempresa' AND txtCOD_ARTICULO!='$txtCOD_ARTICULO' LIMIT 1";
    $art = ejecutarConsultaSimpleFila($sql);

    if ($art && $codigo !== '') {
      $jsondata['estado']  = '1';
      $jsondata['mensaje'] = 'EL CÓDIGO DEL PRODUCTO YA EXISTE';
      echo json_encode($jsondata);
      break;
    }

    if ($codigo === '') { $codigo = (string)$txtCOD_ARTICULO; }

    $rspta = $articulo->editar(
      $txtCOD_ARTICULO, $idcategoria, $idmarca, $codigo, $txtDESCRIPCION_ARTICULO,
      $stock, $stockmin, $stockmax, $precio, $precio_porcentaje, $ctimayor,
      $precio_mayor, $imagen, $medida, $exonerado, $comision, $comisionm, $comisionmp,
      $codigos, $idproveedor, $sanitario, $precioc, $linea, $sublinea, $subfamilia,
      $ctacompras, $ctaventas, $bolsa, $existencia, $idlocal, $moneda, $pactivo, $oferta,
      $canje, $canjepuntos, $canjecobro, $precio_mayor2, $precio_porcentaje2, $precio_porcentaje3
    );

    // Aun si $rspta devuelve true/false, respondemos JSON limpio
    $img = ejecutarConsultaSimpleFila("SELECT imag FROM articulo_images WHERE idempresa='$idempresa' AND id_serv='$txtCOD_ARTICULO' ORDER BY id DESC LIMIT 1");
    if ($img && isset($img['imag']) && $img['imag'] !== '') {
      ejecutarConsulta("UPDATE articulo SET imagen='".$img['imag']."' WHERE idempresa='$idempresa' AND txtCOD_ARTICULO='$txtCOD_ARTICULO' LIMIT 1");
    }

    $jsondata['estado']  = '0';
    $jsondata['mensaje'] = 'ARTÍCULO ACTUALIZADO';
    $jsondata['idreal']  = $txtCOD_ARTICULO;
    $jsondata['v']       = time();
    echo json_encode($jsondata);
    break;
  }

break;

case 'stockinicial':
		
$tot=round($stock*$precio, 2);
$subtot=round($tot/1.18, 2);	
$igv=round($tot-$subtot, 2);
$promedio=round($precio/ 1.18, 2);
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

$sql3="SELECT i.idingreso FROM detalle_ingreso AS di INNER JOIN ingreso AS i ON di.idingreso=i.idingreso WHERE di.txtCOD_ARTICULO='$id' AND i.tipo_comprobante='16' ";
$ing= ejecutarConsultaSimpleFila($sql3);
		
$sqlst="SELECT * FROM articulo_stock WHERE idarticulo='$id' AND idlocal='$_COOKIE[idlocal]' ";
$artst= ejecutarConsultaSimpleFila($sqlst);

		
if(!$ing){
	
$sql="SELECT *FROM ingreso WHERE serie_comprobante='GI001' AND idempresa='$_COOKIE[id]' ORDER BY idingreso DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$sqlf="SELECT *FROM ingreso WHERE idempresa='$_COOKIE[id]' ORDER BY idingreso ASC ";
$mostrarf= ejecutarConsultaSimpleFila($sqlf);
	
$num='00000001';	
	
if($mostrar['num_comprobante']!=''){
$num=$mostrar['num_comprobante']+1;
$num=str_pad($num, 8, "0", STR_PAD_LEFT);
}

$sql="INSERT INTO ingreso VALUES (NULL, '0', '$_COOKIE[id]', '$fa[tipo]', '1', '$_COOKIE[idusuario]', '$_COOKIE[idlocal]', '0', '16', 'GI001', '$num', 'CONTADO', '008', 'PEN', '$fecha', '$fecha', '$igv', '$tot', '$igv', '$tot', '', '0')";
//return ejecutarConsulta($sql);
$iding=ejecutarConsulta_retornarID($sql);
	
$sql_detalle = "INSERT INTO detalle_ingreso VALUES (NULL, '$_COOKIE[id]', '1', '$iding', '$_COOKIE[idlocal]', '$id', '$stock', '$subtot', '$igv', '$tot', '$precio', '$precio', '$promedio', '$stock', '$fecha', '$fecha', '0')";
ejecutarConsulta($sql_detalle);

}else{
	
$sql="UPDATE ingreso SET subtotal='$subtot', igv='$igv', total_compra='$tot', impuesto='$igv', fecha_hora='$fecha', fechaven='$fecha', estado='0' WHERE idingreso='$ing[idingreso]' ";
ejecutarConsulta($sql);
	
$sql="UPDATE detalle_ingreso SET txtCANTIDAD_ARTICULO='$stock', subtotal='$subtot', igv='$igv', total='$tot', precio_compra='$precio', precio_venta='$precio', precio_promedio='$promedio', stock='$stock', fecha='$fecha', fecha_vto='$fecha', estado='0' WHERE idingreso='$ing[idingreso]' ";
ejecutarConsulta($sql);
	
$sql="UPDATE cardex_detalle SET cantidad='$stock', precio='$precio', total='$tot', preciof='$precio', cantidadf='$stock', totalf='$tot', fecha='$fecha' WHERE iddocumento='$ing[idingreso]' ";
ejecutarConsulta($sql);
	
}
		
/*actualizamos stock*/
if($artst){		
$sql="UPDATE articulo_stock SET stock_inicial='$stock', precio_inicial='$precio' WHERE idarticulo='$id' AND idlocal='$_COOKIE[idlocal]' ";
ejecutarConsulta($sql);
}else{		
$sql="INSERT INTO articulo_stock VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$id', '0', '$_COOKIE[idlocal]', '$stock', '$precio', '$stock')";
ejecutarConsulta($sql);
}
		

		
echo "Stock inicial actualizado";
		
break;

    case 'guardaserie':

        $rspta=$articulo->insertarserie($id, $serie, $lote, $fechven, $idproveedora, $stockser);
        /*
        if($rspta==1){
        $sql2="UPDATE articulo SET stock=stock+1 WHERE codigo='$id'";
        ejecutarConsulta($sql2);
        }
        */
        echo $rspta ? "Artículo registrado id:".$id : "Artículo no se pudo registrar";

        break;

case 'guardaunidadm':

if($tipoac=='1'){
	
$rspta=$articulo->insertarunidad($nombreu, $ctiunidad, $medida2, $codartu, $preciou, $comisionu, $ctimayoru, $preciomu, $comisionmu);
echo $rspta ? "PRESENTACION REGISTRADA" : "PRESENTACION NO SE PUDO REGISTRAR";
	
}else{
	
$sql="INSERT INTO tmp_articulounidad VALUES (NULL, '$_COOKIE[idarticulo]', '$nombreu','$medida2', '$ctiunidad', '$ctimayoru', '$preciou', '$preciomu', '$comisionu', '$comisionmu')";
$guardau=ejecutarConsulta($sql);
echo $guardau? "Unidad registrada" : "Unidad no se pudo registrar";	

}

break;	
		
		
case 'guardarpaquete':
		
$sql="INSERT INTO articulo_receta VALUES (NULL, '$_COOKIE[id]', '$idproducto', '$idsub', '$cantidad')";
$guardau=ejecutarConsulta($sql);
echo $guardau? "Unidad registrada" : "Unidad no se pudo registrar";	

break;	
		

	case 'desactivar':
		$rspta=$articulo->desactivar($txtCOD_ARTICULO);
 		echo $rspta ? "Artículo Desactivado" : "Artículo no se puede desactivar";
	break;

	case 'activar':
		$rspta=$articulo->activar($txtCOD_ARTICULO);
 		echo $rspta ? "Artículo activado" : "Artículo no se puede activar";
	break;
		
	case 'resaltar':
		$rspta=$articulo->resaltar($codigo, $estado);
 		echo $rspta ? "Artículo activado" : "Artículo no se puede activar";
	break;
		
	case 'eliminar':
		$rspta=$articulo->eliminar($codigo);
 		echo $rspta ? "Unidad eliminada" : "Unidad no se pudo eliminar";
	break;

case 'mostrar':
$rspta=$articulo->mostrar($txtCOD_ARTICULO, $tipoac);
echo json_encode($rspta);
break;

case 'listar':

  $idempresa      = isset($_COOKIE['id']) ? $_COOKIE['id'] : '0';
  $idlocal_cookie = isset($_COOKIE['idlocal']) ? $_COOKIE['idlocal'] : '0';

  $sql3 = "SELECT * FROM config WHERE id='". $idempresa ."' ";
  $fa   = ejecutarConsultaSimpleFila($sql3);

  $whereLocal = "";
  if ($fa && isset($fa['articulo']) && $fa['articulo'] === 'LOCAL') {
    $whereLocal = " AND a.idlocal = '". $idlocal_cookie ."' ";
  }

  // =========================
  // DataTables params
  // =========================
  $draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
  $row  = isset($_POST['start']) ? (int)$_POST['start'] : 0;
  $rowperpage = isset($_POST['length']) ? (int)$_POST['length'] : 20;

  // default order: ID desc
  $columnIndex = 2;
  $columnSortOrder = "desc";

  if (isset($_POST['order'][0]['column'])) $columnIndex = (int)$_POST['order'][0]['column'];
  if (isset($_POST['order'][0]['dir'])) {
    $d = strtolower($_POST['order'][0]['dir']);
    $columnSortOrder = ($d === "asc") ? "asc" : "desc";
  }

  $searchValue = "";
  if (isset($_POST['search']['value'])) $searchValue = trim((string)$_POST['search']['value']);

  // =========================
  // map column index -> SQL
  // =========================
  $columnsMap = [
    0  => "a.txtCOD_ARTICULO",
    1  => "a.txtCOD_ARTICULO",
    2  => "a.txtCOD_ARTICULO",
    3  => "a.codigo",
    4  => "c.nombre",
    5  => "a.txtDESCRIPCION_ARTICULO",
    6  => "s.stock",
    7  => "a.precio",
    8  => "a.precio_compra",
    9  => "p.nombre",
    10 => "ls.lote",
    11 => "ls.fecha_vto",
    12 => "a.condicion"
  ];
  $orderBy = isset($columnsMap[$columnIndex]) ? $columnsMap[$columnIndex] : "a.txtCOD_ARTICULO";

  // =========================
  // Escape simple SIN $conexion
  // =========================
  $sv = "";
  if ($searchValue !== "") {
    $sv = str_replace(["\\", "'"], ["\\\\", "\\'"], $searchValue);
  }

  // =========================
  // Search query (bien cerrado)
  // =========================
  $searchQuery = "";
  if ($sv !== "") {
    $searchQuery = " AND (
      a.txtCOD_ARTICULO LIKE '%$sv%'
      OR a.txtDESCRIPCION_ARTICULO LIKE '%$sv%'
      OR a.codigo LIKE '%$sv%'
      OR EXISTS (
        SELECT 1
        FROM articulo_serie s2
        WHERE s2.cod_articulo = a.txtCOD_ARTICULO
          AND (s2.serie LIKE '%$sv%' OR s2.lote LIKE '%$sv%')
      )
    ) ";
  }

  // =========================
  // Totales
  // =========================
  $records = ejecutarConsultaSimpleFila("
    SELECT COUNT(*) AS allcount
    FROM articulo a
    WHERE a.idempresa='$idempresa' $whereLocal
  ");
  $totalRecords = $records ? (int)$records['allcount'] : 0;

  $records = ejecutarConsultaSimpleFila("
    SELECT COUNT(*) AS allcount
    FROM articulo a
    LEFT JOIN categoria c ON a.idcategoria=c.idcategoria
    LEFT JOIN articulo_stock s ON s.idarticulo=a.txtCOD_ARTICULO AND s.idlocal='$idlocal_cookie'
    WHERE a.idempresa='$idempresa' $whereLocal $searchQuery
  ");
  $totalRecordwithFilter = $records ? (int)$records['allcount'] : 0;

  // =========================
  // Query principal
  // =========================
  $empQuery = "
    SELECT
      a.txtCOD_ARTICULO,
      a.txtDESCRIPCION_ARTICULO,
      a.codigo,
      a.condicion,
      a.precio,
      a.precio_compra,
      IFNULL(c.nombre,'GENERICA') AS grupo,
      LEFT(IFNULL(p.nombre,''),4) AS lab4,
      IFNULL(s.stock,0) AS stock_local,
      ls.lote,
      ls.fecha_vto
    FROM articulo a
    LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
    LEFT JOIN persona p ON p.idpersona = a.idproveedor
    LEFT JOIN articulo_stock s ON s.idarticulo = a.txtCOD_ARTICULO AND s.idlocal = '$idlocal_cookie'
    LEFT JOIN (
      SELECT x.cod_articulo, x.lote, x.fecha_vto
      FROM articulo_serie x
      INNER JOIN (
        SELECT cod_articulo, MAX(id) AS maxid
        FROM articulo_serie
        WHERE estado='1'
        GROUP BY cod_articulo
      ) y ON y.cod_articulo=x.cod_articulo AND y.maxid=x.id
    ) ls ON ls.cod_articulo = a.txtCOD_ARTICULO
    WHERE a.idempresa = '$idempresa'
      $whereLocal
      $searchQuery
    ORDER BY $orderBy $columnSortOrder
    LIMIT $row, $rowperpage
  ";

  $empRecords = ejecutarConsulta($empQuery);

  if ($empRecords === false) {
    echo json_encode([
      "draw" => $draw,
      "recordsTotal" => $totalRecords,
      "recordsFiltered" => $totalRecordwithFilter,
      "data" => [],
      "error" => "SQL ERROR listar. Revisa empQuery."
    ]);
    break;
  }

  $data = [];

  while ($reg = $empRecords->fetch_object()) {

    $botones = '
      <div class="dropdown">
        <button class="btn btn-default btn-xs dropdown-toggle btn-opciones-art" type="button" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i> <span class="caret"></span> OPCIONES
        </button>
        <ul class="dropdown-menu dropdown-menu-opciones">
    ';

    $botones .= '<li><a href="javascript:void(0)" onclick="mostrar('.$reg->txtCOD_ARTICULO.', 1)"><i class="fa fa-pencil"></i> Editar</a></li>';
    $botones .= '<li><a href="javascript:void(0)" onclick="addserie(\''.$reg->txtCOD_ARTICULO.'\')"><i class="fa fa-barcode"></i> Series/Lotes</a></li>';
    $botones .= '<li><a href="javascript:void(0)" onclick="imprimircode('.$reg->txtCOD_ARTICULO.')"><i class="fa fa-print"></i> Imprimir</a></li>';

    if ((string)$reg->condicion === "0") {
      $botones .= '<li><a href="javascript:void(0)" onclick="activar('.$reg->txtCOD_ARTICULO.')">Activar</a></li>';
    } else {
      $botones .= '<li><a href="javascript:void(0)" onclick="desactivar('.$reg->txtCOD_ARTICULO.')">Desactivar</a></li>';
    }

    $botones .= '</ul></div>';

    $stockf  = number_format((float)$reg->stock_local, 2, '.', '');
    $precio  = number_format((float)$reg->precio, 4, '.', '');
    $pcompra = number_format((float)$reg->precio_compra, 4, '.', '');

    $estado = ((string)$reg->condicion === "1")
      ? '<div class="label label-success">Activado</div>'
      : '<div class="label label-danger">Desactivado</div>';

    $data[] = [
      $reg->txtCOD_ARTICULO,         // 0 hidden
      $botones,                      // 1
      $reg->txtCOD_ARTICULO,         // 2 hidden ID
      $reg->codigo,                  // 3
      $reg->grupo,                   // 4
      $reg->txtDESCRIPCION_ARTICULO, // 5
      $stockf,                       // 6
      $precio,                       // 7
      $pcompra,                      // 8
      $reg->lab4,                    // 9 hidden
      (string)$reg->lote,            // 10
      (string)$reg->fecha_vto,       // 11
      $estado                        // 12
    ];
  }

  echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordwithFilter,
    "data" => $data
  ]);

break;





		
		
case 'listarserie':
		
$id=$_GET['id'];
		
		$rspta=$articulo->listarserie($id);
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
$botones='';

if($reg->estado=='0'){ 
$botones.='<button class="btn btn-primary btn-xs" ><i class="fa fa-check"></i></button>';
}else{ 
$botones.='<button class="btn btn-danger btn-xs" onclick="desactivarserie('.$reg->id.')"><i class="fa fa-close"></i> Vendido</button>'; 
}

			
 			$data[]=array(
 				"0"=>$botones,
				"1"=>$reg->id,
 				"2"=>$reg->serie,
 				"3"=>$reg->lote,
 				"4"=>$reg->fecha,
 				"5"=>$reg->fecha_vto,
 				"6"=>$reg->estado,
				"7"=>$reg->stock,
 				"8"=>($reg->estado)?'<div class="label label-success">Activado</div>':
 				'<div class="label label-danger">Vendido</div>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
	
		
		
case 'listarpaquetes':
		
$id=$_GET['id'];
		
 		$data= Array();
		
$sql="SELECT *FROM articulo_receta WHERE idproducto='$id' ";
$rspta=ejecutarConsulta($sql);

 		while ($reg=$rspta->fetch_object()){
			
$sqlst="SELECT *FROM articulo WHERE txtCOD_ARTICULO='$reg->idrelacionado' ";
$st= ejecutarConsultaSimpleFila($sqlst);
			
$botones='';
$botones.='<button class="btn btn-danger btn-xs" onclick="eliminarreceta('.$reg->id.')"><span class="glyphicon glyphicon-trash"></span> ELIMINAR</button>'; 
			
 			$data[]=array(
 				"0"=>$botones,
				"1"=>$reg->id,
 				"2"=>$st['txtDESCRIPCION_ARTICULO'],
 				"3"=>$reg->cantidad
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
 		echo json_encode($results);

	break;

case 'listarunidad':
		
$id=$_GET['id'];
$tipoac=$_GET['tipoac'];
		
		$rspta=$articulo->listarunidad($id, $tipoac);
 		$data= Array();
 		while ($reg=$rspta->fetch_object()){
$botones='';
	

$botones.='<button class="btn btn-danger btn-xs" onclick="eliminar('.$reg->id.')"><i class="fa fa-close"></i> Eliminar</button>'; 


			
 			$data[]=array(
 				"0"=>$botones,
 				"1"=>$reg->nombre,
 				"2"=>$reg->medida,
 				"3"=>$reg->cti,
				"4"=>$reg->precio,
				"5"=>$reg->preciom,
				"6"=>$reg->comision,
				"7"=>$reg->comisionm
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
		
	case "selectCategoria":
$nivel=$_GET['nivel'];
$id=$_GET['id'];
		require_once "../modelos/Categoria.php";
	/*			
echo '<option value="0" selected >-SELECCIONE-</option>';
		*/
		

		
		$categoria = new Categoria();
		
		$rspta = $categoria->select($nivel);
		while ($reg = $rspta->fetch_object()){
			if($id==$reg->idcategoria){
echo '<option value=' . $reg->idcategoria . ' selected >' . $reg->nombre . '</option>';
			}else{
echo '<option value=' . $reg->idcategoria . '>' . $reg->nombre . '</option>';
			}
					
				}
	break;
		
	case "selectCategoria2":
$nivel=$_GET['nivel'];
$id=$_GET['id'];
		require_once "../modelos/Categoria.php";
		
echo '<option value="0" selected >-SELECCIONE-</option>';

		$categoria = new Categoria();
		
		$rspta = $categoria->select($nivel);
		while ($reg = $rspta->fetch_object()){
			if($id==$reg->idcategoria){
echo '<option value=' . $reg->idcategoria . ' selected >' . $reg->nombre . '</option>';
			}else{
echo '<option value=' . $reg->idcategoria . '>' . $reg->nombre . '</option>';
			}
					
				}
	break;
		
	case "selectCategoria3":
$nivel=$_GET['nivel'];
$id=$_GET['id'];
		require_once "../modelos/Categoria.php";
		
echo '<option value="0" selected >-SELECCIONE-</option>';

		$categoria = new Categoria();
		
		$rspta = $categoria->select($nivel);
		while ($reg = $rspta->fetch_object()){
echo '<option value=' . $reg->idcategoria . '>' . $reg->nombre . '</option>';
}
	break;
		
case 'ListMedida':
		
$id=$_GET['id'];

$sql="SELECT * FROM articulo WHERE txtCOD_ARTICULO='$id'";
$mostrar=ejecutarConsultaSimpleFila($sql);
		
$rspta=$articulo->listarum();
while ($reg = $rspta->fetch_object()){
echo '<option value='. $reg->codigo;
if($mostrar){
if($mostrar['medida']==$reg->id){
echo ' selected="selected"'; 
}
}else if($reg->id=='19'){ echo ' selected="selected"'; }
echo ' >' . $reg->tit. '</option>';
	
}

	break;
		
		
case 'ListMedida2':
		
$rspta=$articulo->listarum();
while ($reg = $rspta->fetch_object()){
echo '<option value='.$reg->codigo.' >' . $reg->tit. '</option>';
	
}

	break;
	
case 'cardexinicio':		
		
break;
	
case 'cardex':

header("Content-Type: application/json; charset=UTF-8");
		
$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	
$num=0;	
		
$periodo=$anio.'-'.$mes;
$periodo2=$anio.'-'.$mes.'-01';
//$periodo='2020-08';		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

// Fuente de la primera fila en negrita
$tcecti='0';
$tcetot='0';
$tcstot='0';
$tcstot='0';
$ctttf='0';
$ptttf='0';
$data= Array();
		
$periodoant=date("Y-m",strtotime($periodo2."- 1 month"));
	
//ELIMINAMOS KARDEX ANTERIOR
$sqlc="DELETE FROM cardex WHERE periodo='$periodo' AND idlocal='$idlocal' ";
ejecutarConsulta($sqlc);
	
if($_COOKIE['id']=='19'){
	
if($fa['articulo']=='GENERAL'){

	
$sql="		
INSERT INTO cardex (idempresa, idlocal, beta, idproducto, periodo, cinicial, pinicial, tinicial, cfinal, pfinal, tfinal, comentario)
SELECT '$_COOKIE[id]', '$idlocal', '$fa[tipo]', txtCOD_ARTICULO, '$periodo', IFNULL((SELECT cfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0), IFNULL((SELECT pfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0), IFNULL((SELECT tfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0),'0', '0', '0', ''
FROM articulo WHERE idempresa='$_COOKIE[id]' AND condicion='1' AND existencia!='91' ";
$idkardex=ejecutarConsulta_retornarID($sql);
	
	
}else{
	
$sql="		
INSERT INTO cardex (idempresa, idlocal, beta, idproducto, periodo, cinicial, pinicial, tinicial, cfinal, pfinal, tfinal, comentario)
SELECT '$_COOKIE[id]', '$idlocal', '$fa[tipo]', txtCOD_ARTICULO, '$periodo', IFNULL((SELECT cfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0), IFNULL((SELECT pfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0), IFNULL((SELECT tfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0),'0', '0', '0', ''
FROM articulo WHERE idempresa='$_COOKIE[id]' AND condicion='1' AND AND existencia!='91' AND  idlocal='$idlocal' ";
$idkardex=ejecutarConsulta_retornarID($sql);
	
	
}

}else{

	
if($fa['articulo']=='GENERAL'){

	
$sql="		
INSERT INTO cardex (idempresa, idlocal, beta, idproducto, periodo, cinicial, pinicial, tinicial, cfinal, pfinal, tfinal, comentario)
SELECT '$_COOKIE[id]', '$idlocal', '$fa[tipo]', txtCOD_ARTICULO, '$periodo', IFNULL((SELECT cfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0), IFNULL((SELECT pfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0), IFNULL((SELECT tfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0),'0', '0', '0', ''
FROM articulo WHERE idempresa='$_COOKIE[id]' AND condicion='1' AND medida!='ZZ' ";
$idkardex=ejecutarConsulta_retornarID($sql);
	
	
}else{
	

$sql="		
INSERT INTO cardex (idempresa, idlocal, beta, idproducto, periodo, cinicial, pinicial, tinicial, cfinal, pfinal, tfinal, comentario)
SELECT '$_COOKIE[id]', '$idlocal', '$fa[tipo]', txtCOD_ARTICULO, '$periodo', IFNULL((SELECT cfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0), IFNULL((SELECT pfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0), IFNULL((SELECT tfinal FROM cardex WHERE idproducto=txtCOD_ARTICULO AND periodo='$periodoant' ),0),'0', '0', '0', ''
FROM articulo WHERE idempresa='$_COOKIE[id]' AND condicion='1' AND medida!='ZZ' AND  idlocal='$idlocal'
";
$idkardex=ejecutarConsulta_retornarID($sql);
	

}	
	
	
}

$sep=explode('-', $periodo);	
$anioper=$sep[0];
$mesper=$sep[1];
				
$sqlc="DELETE FROM cardex_detalle WHERE periodo='$periodo' AND idempresa='$_COOKIE[id]' AND idlocal='$idlocal' ";
ejecutarConsulta($sqlc);
	
$sql="		
INSERT INTO cardex_detalle (idempresa, idlocal, idproducto, nivel, tipooperacion, periodo, idoperacion, fecha, tipo, serie, numero, operacion, cantidad, precio, total, cantidadf, preciof, totalf, extras)
SELECT '$_COOKIE[id]', '$idlocal', txtCOD_ARTICULO, '1', '1', '$periodo', iddetalle_ingreso, fecha_hora, tipo_comprobante, serie_comprobante, num_comprobante, '02', txtCANTIDAD_ARTICULO, precio_compra, total, '0', '0', '0', '' FROM detalle_ingreso d INNER JOIN ingreso i ON i.idingreso=d.idingreso WHERE YEAR(i.fecha_hora)='$anioper' AND MONTH(i.fecha_hora)='$mesper' AND i.estado='0' AND i.nivel='0' AND i.tipo_comprobante!='17' AND d.idlocal='$idlocal' AND txtCANTIDAD_ARTICULO!='0.00000' AND i.beta='$fa[tipo]' ORDER BY i.fecha_hora ASC
";
$idkardex=ejecutarConsulta_retornarID($sql);
		
$sqlt="SELECT d.txtCANTIDAD_ARTICULO, (d.txtCANTIDAD_ARTICULO*precio_venta) AS total, d.precio_venta AS precio, d.precio_compra, i.serie_comprobante, i.num_comprobante, i.tipo_comprobante, d.precio_venta, i.fecha_hora, d.iddetalle_ingreso, d.stock, d.idingreso, d.subtotal, d.precio_promedio, i.moneda, i.estado, d.otrosgastos, d.txtCOD_ARTICULO, d.tipo FROM detalle_ingreso d INNER JOIN ingreso i ON i.idingreso=d.idingreso WHERE YEAR(i.fecha_hora)='$anioper' AND MONTH(i.fecha_hora)='$mesper' AND i.estado='0' AND i.nivel='0' AND i.tipo_comprobante!='17' AND d.idlocal='$idlocal' AND txtCANTIDAD_ARTICULO!='0.00000' AND i.beta='$fa[tipo]' ORDER BY i.fecha_hora ASC ";
$dat = ejecutarConsulta($sqlt);

while($dat1=mysqli_fetch_array($dat)){
	
$sqlk="SELECT * FROM cardex WHERE periodo='$periodo' AND idproducto='$dat1[txtCOD_ARTICULO]' AND beta='$fa[tipo]' AND idlocal='$idlocal' ";
$kar=ejecutarConsultaSimpleFila($sqlk);
	
if(!$kar){
$stocki='0.00';
$precioi='0.00';
$totali='0.00';
}else{
$stocki=$kar['cinicial'];
$precioi=$kar['pinicial'];
$totali=$kar['tinicial'];
}
	
$prcompra=$dat1['precio_compra'];	
$importe=$dat1['total'];
$otrosgastos=$dat1['otrosgastos'];
	
if($dat1['moneda']=='USD'){
$fecha=date("Y-m-d", strtotime($dat1['fecha_hora']));
$sqlca="SELECT *FROM tipo_cambio WHERE fecha='$fecha' AND idempresa='0' ";
$conca= ejecutarConsultaSimpleFila($sqlca);
$prcompra=$conca['venta']*$dat1['precio_compra'];
$importe=$conca['venta']*$dat1['total'];
if($otrosgastos!='0.0000000'){
$otrosgastos=$dat1['otrosgastos']*$conca['venta'];
}	
}

if($dat1['tipo']=='0'){
$pscinigv=$prcompra/1.18;
$importe=$importe/1.18;
}else{
$pscinigv=$prcompra;
$importe=$importe;	
}

$pscinigv=$pscinigv+$otrosgastos;	
$total=$pscinigv*$dat1['txtCANTIDAD_ARTICULO'];
	
$totalff=$totali;
	
	
$tipo45='';
	
$sqlkdet="SELECT * FROM cardex_detalle WHERE periodo='$periodo' AND idproducto='$dat1[txtCOD_ARTICULO]' AND idlocal='$idlocal' AND preciof!='0.000' AND idoperacion='1'  ORDER BY id ASC ";
$kardetf=ejecutarConsultaSimpleFila($sqlkdet);

$tipof='0';

if($kardetf['idoperacion']!=''){
$tipof='1';	
}
	
if($tipof=='0'){
$totali=$total;
$preciopromedio=$pscinigv;
$stocki=$dat1['txtCANTIDAD_ARTICULO'];
$aqui='1';
}else{
$totali=$totali+$total;
$stocki=$stocki+$dat1['txtCANTIDAD_ARTICULO'];	
$preciopromedio=$totali/$stocki;
$aqui='2';
}

$tipo45=$preciopromedio.'|'.$kardetf['idoperacion'].'|'.$aqui;	
	
$preciopromedio=round($preciopromedio, 3);	
$total=round($total, 3);
$pscinigv=round($pscinigv, 3);

$extras='';
		
$sql="UPDATE cardex_detalle SET precio='$pscinigv', total='$total', preciof='$preciopromedio', extras='$tipo45' WHERE idoperacion='$dat1[iddetalle_ingreso]' ";
ejecutarConsulta($sql);

}
	
$sqlt="SELECT d.id, o.fecha, o.serie, o.numero, d.cti, o.tipo, d.idproducto FROM  venta_opdetallet d INNER JOIN venta_orden o ON d.idpedido=o.id WHERE YEAR(d.fecha)='$anioper' AND MONTH(d.fecha)='$mesper' AND d.idlocal='$idlocal' AND o.beta='$fa[tipo]'   ORDER BY o.fecha ASC ";
$dat = ejecutarConsulta($sqlt);

while($dat1=mysqli_fetch_array($dat)){

$comper="SELECT * FROM cardex_detalle WHERE periodo='$periodo' 	AND DATE(fecha)<='$dat1[fecha]' AND idproducto='$dat1[idproducto]' AND cantidad!='0.000' AND precio!='0.000' AND nivel='1'  AND idlocal='$idlocal'  ORDER BY fecha DESC ";
$prom=ejecutarConsultaSimpleFila($comper);
	
$sqlk="SELECT * FROM cardex WHERE periodo='$periodo' AND idproducto='$dat1[idproducto]' AND beta='$fa[tipo]' AND idlocal='$idlocal' ";
$kar=ejecutarConsultaSimpleFila($sqlk);
	
if($prom['preciof']){ 
$preciopromedio=$prom['preciof']; 
}else{ 
$preciopromedio=$kar['pinicial'];
}

$total=$preciopromedio*$dat1['cti'];
$total=round($total, 3);

//$extras='PROMEDIO:'.$prom['serie'].'/CANTIDAD:'.$dat1['cti'];
	
$sql="INSERT INTO cardex_detalle VALUES (NULL, '$_COOKIE[id]', '$idlocal', '$dat1[idproducto]', '2', '2', '$periodo', '$dat1[id]', '$dat1[fecha]', '$dat1[tipo]', '$dat1[serie]', '$dat1[numero]', '10', '$dat1[cti]', '$preciopromedio', '$total', '0', '$preciopromedio', '0', '$extras')";
$idkardex=ejecutarConsulta_retornarID($sql);
	
}


$paraprecioprom='0';

$sqlv="SELECT d.txtCANTIDAD_ARTICULO AS cti, d.subtotal  AS total, d.nombreproducto, v.txtSERIE, v.txtNUMERO, v.txtID_TIPO_DOCUMENTO, d.precio, d.codigoproducto, v.txtFECHA_DOCUMENTO, d.preciocompra, d.stock, d.iddetalle_venta, d.idproducto , v.modifica_motivo FROM detalle_venta d INNER JOIN venta v ON v.idventa=d.idventa WHERE pedido='0' AND (v.txtID_TIPO_DOCUMENTO='03' OR v.txtID_TIPO_DOCUMENTO='01' OR v.txtID_TIPO_DOCUMENTO='07') AND (v.estado='0' OR v.estado='1' OR v.estado='2') AND  YEAR(v.txtFECHA_DOCUMENTO)='$anioper' AND MONTH(v.txtFECHA_DOCUMENTO)='$mesper' AND v.beta='$fa[tipo]'  AND d.idlocal='$idlocal' ";
	
$datost = ejecutarConsulta($sqlv);	
while($datv=mysqli_fetch_array($datost)) {
	
if($datv['txtID_TIPO_DOCUMENTO']=='01'||$datv['txtID_TIPO_DOCUMENTO']=='03'){ 
$nivel='2';
$tipoent='01';
}else{ 
$nivel='1';
$tipoent='00';
}
	
$sqlk="SELECT * FROM cardex WHERE periodo='$periodo' AND idproducto='$datv[idproducto]' AND beta='$fa[tipo]' AND idlocal='$idlocal' ";
$kar=ejecutarConsultaSimpleFila($sqlk);
	
$comper="SELECT * FROM cardex_detalle WHERE periodo='$periodo' 	AND fecha<='$datv[txtFECHA_DOCUMENTO]' AND idproducto='$datv[idproducto]' AND cantidad!='0.000' AND precio!='0.000' AND nivel='1'  AND idlocal='$idlocal' ORDER BY fecha DESC ";
$prom=ejecutarConsultaSimpleFila($comper);
	
if($prom['preciof']){ 
$preciopromedio=$prom['preciof']; 
}else{ 
$preciopromedio=$kar['pinicial'];
}


$centrada=$datv['cti'];
$tentrada=round($preciopromedio*$centrada, 3);
$otros='SERIE-NUMERO:'.$prom['serie'].'-'.$prom['numero'].'/PROMEDIO:'.$prom['preciof'];
//$otros=$paraprecioprom;
$guarda='2';
if($datv['modifica_motivo']==''||$datv['modifica_motivo']=='01'||$datv['modifica_motivo']=='02'){	
$guarda='1';
}
if($guarda=='1'){
$sql="INSERT INTO cardex_detalle VALUES (NULL, '$_COOKIE[id]', '$idlocal', '$datv[idproducto]', '2', '$nivel', '$periodo', '$datv[iddetalle_venta]', '$datv[txtFECHA_DOCUMENTO]', '$datv[txtID_TIPO_DOCUMENTO]', '$datv[txtSERIE]', '$datv[txtNUMERO]', '$tipoent', '$centrada', '$preciopromedio', '$tentrada', '0', '$preciopromedio', '0', '$otros')";
$idkardex=ejecutarConsulta_retornarID($sql);
}

}
	
		

$results=ejecutarConsulta("SELECT * FROM articulo WHERE idempresa='$_COOKIE[id]' AND condicion='1' AND medida!='ZZ' ");	
while($reg=$results->fetch_object()){

$sqlka="SELECT * FROM cardex WHERE periodo='$periodo' AND idproducto='$reg->txtCOD_ARTICULO' AND idempresa='$_COOKIE[id]'  AND beta='$fa[tipo]'  AND idlocal='$idlocal' ";
$kari= ejecutarConsultaSimpleFila($sqlka);

$ctiinicial=$kari['cinicial'];
$ctifinal='0';
$totfinal='0';
$promfinal='0';
	
$sqlvuk="SELECT * FROM cardex_detalle WHERE periodo='$periodo' AND idproducto='$reg->txtCOD_ARTICULO'  AND idlocal='$idlocal' ORDER BY id, fecha ASC ";
$datostuk= ejecutarConsulta($sqlvuk);	
while($datv=mysqli_fetch_array($datostuk)) {
	
$ctifinal=$datv['cantidad'];

if($datv['tipooperacion']=='2'){ 
$ctiinicial=$ctiinicial-$ctifinal;		
}else{ 
$ctiinicial=$ctiinicial+$ctifinal;	
}

$totfinal=round($datv['preciof']*$ctiinicial, 3);	
	
	
$extras='PRECIO:'.$datv['preciof'].'/CANTIDAD:'.$ctiinicial.'/TOTAL:'.$totfinal.'/';
$ctiinicial=round($ctiinicial, 3);
	
$sql="UPDATE cardex_detalle SET cantidadf='$ctiinicial', totalf='$totfinal' WHERE id='$datv[id]' ";
ejecutarConsulta($sql);
	
}
	
$sqlkaf="SELECT * FROM cardex_detalle WHERE periodo='$periodo' AND idproducto='$reg->txtCOD_ARTICULO'  AND idlocal='$idlocal' ORDER BY id DESC ";
$karif= ejecutarConsultaSimpleFila($sqlkaf);
	
if(!$karif['cantidadf']){
$ctifinal=abs($kari['cinicial']);
$prefinal=abs($kari['pinicial']);
$totfinal=abs($kari['tinicial']);	
$aqui='cantidades 1:'.abs($kari['cinicial']).'|'.$prefinal.'|'.$totfinal;	
}else{
$ctifinal=$karif['cantidadf'];
$prefinal=$karif['preciof'];
$totfinal=$karif['totalf'];
$aqui='cantidades 2:'.$ctifinal.'|'.$prefinal.'|'.$totfinal;	
}	
	
$sql="UPDATE cardex SET cfinal='$ctifinal', pfinal='$prefinal', tfinal='$totfinal', comentario='$aqui' WHERE periodo='$periodo' AND idproducto='$reg->txtCOD_ARTICULO' AND idempresa='$_COOKIE[id]'  AND idlocal='$idlocal'  AND beta='$fa[tipo]' ";
ejecutarConsulta($sql);
	
}
	
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'KARDEX GENERADO CORRECTAMENTE';	
		
echo json_encode($jsondata);
exit();	
	
break;
		
		
		
case 'listarcardex':
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
		
$idlocal=$_GET['idlocal'];
		
if($fa['tipo']=='3'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }
		
$ruta="../api_cpe/".$tipop."/".$fa['ruc']."/";
		
		
$sql="SELECT * FROM cardex WHERE idempresa='$_COOKIE[id]' AND idlocal='$idlocal' GROUP BY periodo ORDER BY periodo DESC " ;
$rspta=ejecutarConsulta($sql);

 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){

$fichero=$fa['ruc'].'-'.$reg->periodo;
			
$sqlsuc="SELECT * FROM sucursal WHERE id='$reg->idlocal' ";
$suc= ejecutarConsultaSimpleFila($sqlsuc);	
			
			
/*
$botones='<a target="_blank" href="'.$ruta.$fichero.'.xlsx" data-toggle="tooltip" data-placement="right" title="DESCARGAR EXCEL" ><button class="btn btn-default btn-xs"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
</button></a> ';
*/		
$botones='<a href="modelos/descargas.php?op=descargaexcel&id='.$reg->id.'&idlocal='.$reg->idlocal.'" data-toggle="tooltip" data-placement="right" title="DESCARGAR EXCEL" ><button class="btn btn-default btn-xs"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
</button></a> ';
			
$botones.=' <button class="btn btn-success btn-xs" onclick="txtguiasunat(\''.$reg->id.'\')" id="descargar" data-toggle="tooltip" data-placement="right" title="DESCARGAR TXT SUNAT" style="z-index: 1000" >
<span class="glyphicon glyphicon-cloud-download"></span>
</button>';	

$botones.=' <button class="btn btn-success btn-xs" onclick="arttxtguiasunat(\''.$reg->id.'\')" id="descargar" data-toggle="tooltip" data-placement="right" title="DESCARGAR ARTICULO TXT SUNAT" style="z-index: 1000" >
<span class="glyphicon glyphicon-cloud-download"></span>
</button>';	

			
			
 			$data[]=array(
 				"0"=>$botones,
				"1"=>$reg->id,
 				"2"=>$reg->periodo,
				"3"=>$suc['sucursal'],
 				"4"=>$reg->pinicial,
 				"5"=>$reg->cinicial,
 				"6"=>$reg->tinicial,
 				"7"=>$reg->cfinal,
 				"8"=>$reg->tfinal
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;

case "codigosunat":
/*
$json= array();

$q=$_GET['q'];
$q= str_replace(" ", "|", $q);
		
$results=ejecutarConsulta("SELECT * FROM serviciosproductos WHERE nombre REGEXP '^$q' LIMIT 20 ");
while ($reg =$results->fetch_object()){

$json[] = ['id'=>$reg->codigo, 'nombre'=>$reg->tit];
	
}
		
echo json_encode ($json);
*/		

	

$data= Array();
	
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchQuery= $_POST['search']['value']; // Search value
$search= str_replace(" ", "|", $searchQuery);
		
## Search 
$searchQuery = " ";
if($search!= ''){
   	$searchQuery=" WHERE nombre REGEXP '^$search' ";
}
		
$records= ejecutarConsultaSimpleFila("select count(*) as allcount from serviciosproductos ");
$totalRecords = $records['allcount'];

## Total number of record with filtering
$records= ejecutarConsultaSimpleFila("select count(*) as  allcount  from serviciosproductos ".$searchQuery);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select *from serviciosproductos ".$searchQuery." limit ".$row.",".$rowperpage;
$empRecords = ejecutarConsulta($empQuery);

while ($reg=$empRecords->fetch_object()){

$botones='<button class="btn btn-danger btn-xs" onclick="agregarsunat('.$reg->codigo.')"><i class="fa fa-plus"></i></button>';

	
			
 			$data[]=array(
 				"0"=>$botones,
				"1"=>$reg->nombre,
 				"2"=>$reg->codigo
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
		
	
		
	
case 'farmaceutica':
echo '<option value="0">SELECCIONE</option>';

$id=$_GET['id'];
$sql="SELECT * FROM persona WHERE idpersona='$id'";
$mostrar=ejecutarConsultaSimpleFila($sql);
		
$rspta=ejecutarConsulta("SELECT * FROM persona WHERE tipo_persona='Proveedor' AND idempresa='$_COOKIE[id]' ORDER BY idpersona DESC ");
while ($reg = $rspta->fetch_object()){
echo '<option value='. $reg->idpersona;
if($id==$reg->idpersona){ echo ' selected="selected"'; }
echo ' >' . $reg->nombre. '</option>';
	
}
		
break;
				
case "principioactivo":
$id=$_GET['id'];
		
echo '<option value="0">SELECCIONE</option>';
		
$rspta=ejecutarConsulta("SELECT * FROM principio_activo ORDER BY nombre ASC ");
while ($reg = $rspta->fetch_object()){
			if($id==$reg->nombre){
echo '<option value="'.$reg->nombre.'" selected >' . $reg->nombre. '</option>';
			}else{
echo '<option value="'.$reg->nombre.'" >' . $reg->nombre. '</option>';
			}
					
				}
	break;
		
		
		
		
		
case 'artrelacionados':
echo '<option value="0">SELECCIONE</option>';
		
$rspta=ejecutarConsulta("SELECT * FROM articulo WHERE idempresa='$_COOKIE[id]' ORDER BY txtDESCRIPCION_ARTICULO ASC ");
while ($reg = $rspta->fetch_object()){
echo '<option value="'. $reg->txtCOD_ARTICULO.'" >' . $reg->txtDESCRIPCION_ARTICULO.'</option>';
	
}
		
break;
		
case 'desactivarlote':
	
$sql= "UPDATE articulo_serie SET estado='0' where id='$txtCOD_ARTICULO' ";
$rspta=ejecutarConsulta($sql);

echo $rspta ? "Lote actualizado" : "Lote no se pudo actualizar".$txtCOD_ARTICULO;
break;
		
case 'eliminarprincipio':
	
$sql= "delete from articulo_principio where id='$idprincipio' ";
$rspta=ejecutarConsulta($sql);

echo $rspta ? "Principio activo eliminado" : "Principio activo no se pudo eliminar";
break;		
		
	
case 'subfamilia':		
		
$id_category = $_POST['id_category'];

	$html = '<option value="0" selected >-SELECCIONE-</option>';	
if($id_category!='0'){		
$rspta=ejecutarConsulta("SELECT * FROM categoria WHERE idnivel='$id_category' ");
while ($reg = $rspta->fetch_object()){
$html .= '<option value="'.$reg->idcategoria.'">'.$reg->nombre.'</option>';
 } 
	
}	
		
echo $html;
break;	
	
case 'proveedorp':		
		
$id= $_POST['articulo'];
		
$sql3="SELECT *FROM articulo WHERE txtCOD_ARTICULO='$id' ";
$fa= ejecutarConsultaSimpleFila($sql3);
		
$id_nums = array($fa['idproveedor']);
$id_nums = implode(", ", $id_nums); 

	$html = '<option value="0" selected >-SELECCIONE-</option>';	
if($id!='0'){		
$rspta=ejecutarConsulta("SELECT * FROM persona WHERE idpersona IN ($id_nums) ");
while ($reg = $rspta->fetch_object()){
$html .= '<option value="'.$reg->idpersona.'">'.$reg->nombre.'</option>';
 } 
	
}	
		
echo $html;
break;	
		
// ===============================
// COOKIE DE PRODUCTO (TEMPORAL)
// ===============================
case 'tempproducto':

    $idempresa = isset($_COOKIE['id']) ? $_COOKIE['id'] : '0';
    $idlocal   = isset($_COOKIE['idlocal']) ? $_COOKIE['idlocal'] : '0';

    // Si ya existe cookie, usarla
    if (isset($_COOKIE['idarticulo']) && $_COOKIE['idarticulo'] != '') {
        $idventanew = $_COOKIE['idarticulo'];
    } else {

        // Crear temporal
        $sql = "INSERT INTO tmp_articulo VALUES (
            NULL,
            '$idempresa',
            '0','233','0','0','0','0','0',
            '$idlocal',
            '0','','','01','',
            '0','1','1','0','0','100','1',
            '0','0','0','0','0',
            '','','',
            '1','1'
        )";

        $idventanew = ejecutarConsulta_retornarID($sql);

        if (!$idventanew) {
            echo json_encode(["error" => "No se pudo crear tmp_articulo"]);
            break;
        }

        // Cookie por 30 días (como ya tienes)
        setcookie('idarticulo', $idventanew, time() + 30*24*60*60, "/");
    }

    $sql = "SELECT * FROM tmp_articulo WHERE id='$idventanew' LIMIT 1";
    $art = ejecutarConsultaSimpleFila($sql);

    if (!$art) {
        echo json_encode(["error" => "No existe tmp_articulo id=$idventanew"]);
        break;
    }

    // IMPORTANTE: devuelve un campo estándar para JS
    // tu JS a veces espera txtCOD_ARTICULO, a veces id
    $art['txtCOD_ARTICULO'] = $idventanew;

    echo json_encode($art);

break;


case 'tempimages':

  $idempresa = isset($_COOKIE['id']) ? (int)$_COOKIE['id'] : 0;
  $tipoac = isset($_POST['tipoac']) ? trim($_POST['tipoac']) : '';
  $txtCOD_ARTICULO = isset($_POST['txtCOD_ARTICULO']) ? (int)$_POST['txtCOD_ARTICULO'] : 0;

  if ($tipoac === '2') {
    $id_serv = isset($_COOKIE['idarticulo']) ? (int)$_COOKIE['idarticulo'] : 0;
    $tablaImgs = "tmp_articuloimages";
  } else {
    $id_serv = $txtCOD_ARTICULO;
    $tablaImgs = "articulo_images";
  }

  if ($idempresa <= 0 || $id_serv <= 0) {
    echo json_encode([
      'initialPreview' => [],
      'initialPreviewConfig' => [],
      'initialPreviewAsData' => true,
      'uploaded' => 0
    ]);
    break;
  }

  $baseUrl = 'files/articulos/' . $idempresa . '/';
  $preview = [];
  $config  = [];

  $rspta = ejecutarConsulta("
    SELECT id, imag
    FROM $tablaImgs
    WHERE idempresa='$idempresa' AND id_serv='$id_serv'
    ORDER BY id DESC
  ");

  if ($rspta !== false) {
    while ($reg = $rspta->fetch_object()) {
      $v = time(); // rompe cache
      $url = $baseUrl . $reg->imag . '?v=' . $v;

      $preview[] = $url;
      $config[] = [
        'key' => $tablaImgs . ':' . $id_serv . ':' . $reg->id, // key única
        'caption' => $reg->imag,
        'downloadUrl' => $url,
        // Para que el tacho de fileinput funcione
        'url' => 'data/articulo.php?op=deleteimage',
        'extra' => ['key' => $tablaImgs . ':' . $id_serv . ':' . $reg->id]
      ];
    }
  }

  echo json_encode([
    'initialPreview' => $preview,
    'initialPreviewConfig' => $config,
    'initialPreviewAsData' => true,
    'uploaded' => count($preview),
    'id_serv' => $id_serv,
    'modo' => ($tipoac === '2' ? 'tmp' : 'real'),
    'v' => time()
  ]);

break;





		

case 'deleteimage':

  // fileinput envía POST key o extra[key]
  $key = '';
  if (isset($_POST['key'])) $key = trim($_POST['key']);
  if ($key === '' && isset($_POST['extra']['key'])) $key = trim($_POST['extra']['key']);

  $idempresa = isset($_COOKIE['id']) ? (int)$_COOKIE['id'] : 0;

  // key formato: tabla:id_serv:idimg
  $parts = explode(':', $key);
  if ($idempresa <= 0 || count($parts) < 3) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'message'=>'Key inválida']);
    break;
  }

  $tablaImgs = $parts[0];
  $id_serv   = (int)$parts[1];
  $idimg     = (int)$parts[2];

  if (!in_array($tablaImgs, ['articulo_images','tmp_articuloimages'], true)) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'message'=>'Tabla inválida']);
    break;
  }

  // Obtener nombre archivo
  $fila = ejecutarConsultaSimpleFila("SELECT imag FROM $tablaImgs WHERE idempresa='$idempresa' AND id_serv='$id_serv' AND id='$idimg' LIMIT 1");
  $imag = $fila && isset($fila['imag']) ? $fila['imag'] : '';

  // Borrar fila
  ejecutarConsulta("DELETE FROM $tablaImgs WHERE idempresa='$idempresa' AND id_serv='$id_serv' AND id='$idimg' LIMIT 1");

  // Borrar archivo físico
  if ($imag !== '') {
    $f = __DIR__ . '/../files/articulos/' . $idempresa . '/' . $imag;
    if (is_file($f)) { @unlink($f); }
  }

  // Si es REAL, actualizar imagen principal (articulo.imagen) a la última existente o vacío
  if ($tablaImgs === 'articulo_images') {
    $ult = ejecutarConsultaSimpleFila("SELECT imag FROM articulo_images WHERE idempresa='$idempresa' AND id_serv='$id_serv' ORDER BY id DESC LIMIT 1");
    $newMain = ($ult && isset($ult['imag'])) ? $ult['imag'] : '';
    ejecutarConsulta("UPDATE articulo SET imagen='$newMain' WHERE idempresa='$idempresa' AND txtCOD_ARTICULO='$id_serv' LIMIT 1");
  }

  echo json_encode(['ok'=>true,'message'=>'Imagen eliminada']);

break;


case 'actualizaarticulo':

$rspta=$articulo->editartemp($idcategoria, $idmarca, $codigo, $txtDESCRIPCION_ARTICULO, $stock, $stockmin, $stockmax, $precio, $ctimayor, $pmayor, $medida, $exonerado, $comision, $comisionm, $comisionmp, $codigos, $idproveedor, $sanitario, $precioc, $linea, $sublinea, $subfamilia, $ctacompras, $ctaventas, $bolsa, $idlocal, $existencia);
echo $rspta ? "Artículo actualizado": "Artículo no se pudo actualizar";
		
break;		

		
case 'verificarcod':		
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'CÓDIGO NO EXISTE';	
$jsondata['codigo'] = $codigo;
		
$sql="SELECT * FROM articulo  WHERE codigo='$codigo' AND idempresa='$_COOKIE[id]' AND txtCOD_ARTICULO!='$txtCOD_ARTICULO' ";
$art=ejecutarConsultaSimpleFila($sql);	

if($art){		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'CÓDIGO DEL PRODDUCTO YA EXISTE';
}	
		
echo json_encode($jsondata);
break;

case 'eliminarreceta':

        $id = $_GET['id'];
		
        $sql = "DELETE FROM articulo_receta WHERE id='$id' ";
        $rspta=ejecutarConsulta($sql);

break;

		
		
case 'regularizarstock':

$page = (isset($_REQUEST['pagina']) && !empty($_REQUEST['pagina']))?$_REQUEST['pagina']:1;
		
$numrows='0';
$proceso='0';	
		
$per_page=10; //la cantidad de registros que desea mostrar
$adjacents  = 4; //brecha entre páginas después de varios adyacentes
$offset = ($page-1)*$per_page;
$cantidadpasado=round($page*$per_page);
		
$count_query="SELECT COUNT(*) as numrows FROM articulo WHERE idempresa='$_COOKIE[id]'  ";	
$row=ejecutarConsultaSimpleFila($count_query);
		
if ($row){ $numrows=$row['numrows']; }
$total_pages = ceil($numrows/$per_page);
$pagina=$page+1;
if($page>=$total_pages){ $proceso='1';  }
$pagina=$page+1;
		
		
$rspta=ejecutarConsulta("SELECT * FROM articulo WHERE idempresa='$_COOKIE[id]' ORDER BY txtCOD_ARTICULO ASC LIMIT $offset,$per_page ");	
while ($reg = $rspta->fetch_object()){			

$sqlc="SELECT *FROM cardex_detalle WHERE idproducto='$reg->txtCOD_ARTICULO' AND idlocal='$_COOKIE[idlocal]' AND estado='0' AND operacion!='16' ORDER BY fecha DESC ";
$car= ejecutarConsultaSimpleFila($sqlc);
	
$sql="UPDATE articulo_stock SET stock='$car[cantidadf]' WHERE idarticulo='$reg->txtCOD_ARTICULO' AND idlocal='$_COOKIE[idlocal]' ";
ejecutarConsulta($sql);
	
//echo $reg->txtCOD_ARTICULO.'| ';	
} 
	
if($proceso=='0'){ 
$jsondata['mensaje'] = 'PROCESARON LOS PRIMEROS: '.$cantidadpasado.' INGRESOS'; 
}else{ 
$jsondata['mensaje'] = '<h3><span class="success"><i class="fa fa-check-circle"></i> Se proceso correctamente.</span></h3>'; 
}
		
$jsondata['estado'] = '1';
$jsondata['paginas'] =$numrows;
$jsondata['pagina'] =$pagina;
$jsondata['proceso'] =$proceso;
$jsondata['cantidad'] =$cantidadpasado;	
$jsondata['totalpages'] = $total_pages.'|'.$page;
/*
echo json_encode($jsondata);		
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = '<h3><span class="success"><i class="fa fa-check-circle"></i> Se proceso correctamente.</span></h3>';	
*/
echo json_encode ($jsondata);

break;
		
case 'listarreceta':

        $id = $_GET['id'];
        $data = Array();

$sql = "SELECT a.txtDESCRIPCION_ARTICULO, r.id, r.cantidad, a.medida FROM articulo_receta r LEFT JOIN articulo a ON r.idrelacionado=a.txtCOD_ARTICULO WHERE r.idproducto='$id' ";
$rspta=ejecutarConsulta($sql);
		
        while ($reg = $rspta->fetch_object()) {

$botones='<button type="button"  class="btn btn-default btn-xs" onclick="actualizareceta('.$reg->id.')"><i class="fas fa-save"></i> GUARDAR</button> <button type="button"  class="btn btn-danger btn-xs" onclick="eliminarreceta('.$reg->id.')"><i class="fas fa-trash-alt"></i> ELIMINAR</button>';
            $data[] = array(
                
                "0" => $reg->id,
                "1" => $reg->txtDESCRIPCION_ARTICULO,
				 "2" => $reg->medida,
                "3" => '<input style="width: 60px" type="text" value="'.$reg->cantidad.'" id="ctidet'.$reg->id.'" class="form-control  form-control-sm">',
				"4" => $botones,
            );
        }
        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data);
        echo json_encode($results);

        break;	
		
		
case 'articulosreceta':

$sql="SELECT * FROM articulo  WHERE idlocal='$_COOKIE[idlocal]' AND condicion='1' AND idempresa='$_COOKIE[id]'  ";
$rspta=ejecutarConsulta($sql);	

 		$data= Array();

 		while ($reg=$rspta->fetch_object()){	
			
$cti='';			
			
$data[]=array(
"0"=>'<button class="btn btn-warning" onclick="addtabla('.$reg->txtCOD_ARTICULO.')"><span class="fa fa-plus"></span></button>',
 				"1"=>$reg->codigo,
 				"2"=>$reg->txtDESCRIPCION_ARTICULO,
 				"3"=>$reg->precio,
 				"4"=>"<input type='number' class='form-control' style='width: 60px; padding: 1px; margin: 1px;  height: 24px; ' value='1' name='cti".$reg->txtCOD_ARTICULO."' id='cti".$reg->txtCOD_ARTICULO."' />"
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);
	break;
		
case 'actualizareceta':	
		
$sql="UPDATE articulo_receta SET cantidad='$cantidad' WHERE id='$id' ";
ejecutarConsulta($sql);	


echo '<h3><span class="success"><i class="fa fa-check-circle"></i> Se proceso correctamente.</span></h3>';

break;		


case 'listarcostos':

if(isset($_GET['cantidad'])){  $cantidad=$_REQUEST["cantidad"]; }else{ $cantidad=''; }

$sql="SELECT * FROM articulo  WHERE idempresa='$_COOKIE[id]' ORDER BY txtDESCRIPCION_ARTICULO DESC ";
$rspta=ejecutarConsulta($sql);	
$data= Array();
while ($reg=$rspta->fetch_object()){

$sql2="SELECT SUM(stock) AS stock FROM articulo_stock WHERE idarticulo='$reg->txtCOD_ARTICULO' ";
$stock= ejecutarConsultaSimpleFila($sql2);

$sqlkardex="SELECT * FROM cardex_detalle WHERE idproducto='$reg->txtCOD_ARTICULO' ";
$kardex= ejecutarConsultaSimpleFila($sqlkardex);

$igv='0';
$precioconigv='0';
$preciobase='0';

if($kardex){
$precioconigv=$kardex['preciof'];
$preciobase=$kardex['preciof'];
}

if($reg->exonerado_igv=='0'){
$igv='18';
$igvfinal=$precioconigv*0.18;
$precioconigv=$precioconigv+$igvfinal;
}

$bruto1=($preciobase*$reg->precio_porcentaje)/100;
$precio1a=($bruto1*$cantidad)/100;
$margen1=$bruto1-$precio1a;
$valorventa1=$preciobase+$bruto1;
$precioventa1=$valorventa1;
if($reg->exonerado_igv=='0'){
$precioventa1=$valorventa1*(1+$igv);
}

$bruto1=' <input type="text" size="5" id="bruto1'.$reg->txtCOD_ARTICULO.'" name="bruto1'.$reg->txtCOD_ARTICULO.'" value="'.$bruto1.'" readonly > ';
$precio1a=' <input type="text" size="5" id="precio1a'.$reg->txtCOD_ARTICULO.'" name="precio1a'.$reg->txtCOD_ARTICULO.'" value="'.$precio1a.'" readonly > ';
$margen1=' <input type="text" size="5" id="margen1'.$reg->txtCOD_ARTICULO.'" name="margen1'.$reg->txtCOD_ARTICULO.'" value="'.$margen1.'" readonly > ';
$valorventa1=' <input type="text" size="5" id="valorventa1'.$reg->txtCOD_ARTICULO.'" name="valorventa1'.$reg->txtCOD_ARTICULO.'" value="'.$valorventa1.'" readonly > ';
$precioventa1=' <input type="text" size="5" id="precioventa1'.$reg->txtCOD_ARTICULO.'" name="precioventa1'.$reg->txtCOD_ARTICULO.'" value="'.$precioventa1.'" readonly > ';

$bruto2=($preciobase*$reg->precio_porcentaje2)/100;
$precio2a=($bruto2*$cantidad)/100;
$margen2=$bruto2-$precio2a;
$valorventa2=$preciobase+$bruto2;
$precioventa2=$valorventa2;
if($reg->exonerado_igv=='0'){
$precioventa2=$valorventa2*(1+$igv);
}

$bruto2=' <input type="text" size="5" id="bruto2'.$reg->txtCOD_ARTICULO.'" name="bruto2'.$reg->txtCOD_ARTICULO.'" value="'.$bruto2.'" readonly > ';
$precio2a=' <input type="text" size="5" id="precio2a'.$reg->txtCOD_ARTICULO.'" name="precio2a'.$reg->txtCOD_ARTICULO.'" value="'.$precio2a.'" readonly > ';
$margen2=' <input type="text" size="5" id="margen2'.$reg->txtCOD_ARTICULO.'" name="margen2'.$reg->txtCOD_ARTICULO.'" value="'.$margen2.'" readonly > ';
$valorventa2=' <input type="text" size="5" id="valorventa2'.$reg->txtCOD_ARTICULO.'" name="valorventa2'.$reg->txtCOD_ARTICULO.'" value="'.$valorventa2.'" readonly > ';
$precioventa2=' <input type="text" size="5" id="precioventa2'.$reg->txtCOD_ARTICULO.'" name="precioventa2'.$reg->txtCOD_ARTICULO.'" value="'.$precioventa2.'" readonly > ';

$bruto3=($preciobase*$reg->precio_porcentaje3)/100;
$precio3a=($bruto3*$cantidad)/100;
$margen3=$bruto3-$precio3a;
$valorventa3=$preciobase+$bruto3;
$precioventa3=$valorventa3;
if($reg->exonerado_igv=='0'){
$precioventa3=$valorventa3*(1+$igv);
}

$bruto3=' <input type="text" size="5" id="bruto3'.$reg->txtCOD_ARTICULO.'" name="bruto3'.$reg->txtCOD_ARTICULO.'" value="'.$bruto3.'" readonly > ';
$precio3a=' <input type="text" size="5" id="precio3a'.$reg->txtCOD_ARTICULO.'" name="precio3a'.$reg->txtCOD_ARTICULO.'" value="'.$precio3a.'" readonly > ';
$margen3=' <input type="text" size="5" id="margen3'.$reg->txtCOD_ARTICULO.'" name="margen3'.$reg->txtCOD_ARTICULO.'" value="'.$margen3.'" readonly > ';
$valorventa3=' <input type="text" size="5" id="valorventa3'.$reg->txtCOD_ARTICULO.'" name="valorventa3'.$reg->txtCOD_ARTICULO.'" value="'.$valorventa3.'" readonly > ';
$precioventa3=' <input type="text" size="5" id="precioventa3'.$reg->txtCOD_ARTICULO.'" name="precioventa3'.$reg->txtCOD_ARTICULO.'" value="'.$precioventa3.'" readonly > ';

$porcentaje1='<input type="text" onkeyup="teclea('.$reg->txtCOD_ARTICULO.', 1)" size="5" id="precio1'.$reg->txtCOD_ARTICULO.'" name="precio1'.$reg->txtCOD_ARTICULO.'" value="'.$reg->precio_porcentaje.'"> ';
$porcentaje2='<input type="text" onkeyup="teclea('.$reg->txtCOD_ARTICULO.', 2)" size="5" id="precio2'.$reg->txtCOD_ARTICULO.'" name="precio2'.$reg->txtCOD_ARTICULO.'" value="'.$reg->precio_porcentaje2.'"> ';
$porcentaje3='<input type="text" onkeyup="teclea('.$reg->txtCOD_ARTICULO.', 3)" size="5" id="precio3'.$reg->txtCOD_ARTICULO.'" name="precio3'.$reg->txtCOD_ARTICULO.'" value="'.$reg->precio_porcentaje3.'"> ';

$costopromedio='<input type="hidden" id="costopromedio'.$reg->txtCOD_ARTICULO.'" name="costopromedio'.$reg->txtCOD_ARTICULO.'" value="'.$preciobase.'">';
$tipoigv='<input type="hidden" id="tipoigv'.$reg->txtCOD_ARTICULO.'" name="tipoigv'.$reg->txtCOD_ARTICULO.'" value="'.$reg->exonerado_igv.'">';

$data[]=array(
"0"=>$reg->txtCOD_ARTICULO,
"1"=>$reg->codigo,
"2"=>$reg->txtDESCRIPCION_ARTICULO.$costopromedio.$tipoigv,
"3"=>$igv,//a
"4"=>$stock['stock'],//b
"5"=>$preciobase,//c
"6"=>$precioconigv,//d
"7"=>$porcentaje1,//e AQUI INCIAMOS LOS COSTOS
"8"=>$bruto1,//f
"9"=>$cantidad.'%',//g
"10"=>$precio1a,//h
"11"=>$margen1,//I
"12"=>$valorventa1,//J
"13"=>$precioventa1,//K  AQUI FINALIZAMOS
"14"=>$porcentaje2,
"15"=>$bruto2,
"16"=>$cantidad.'%',
"17"=>$precio2a,
"18"=>$margen2,
"19"=>$valorventa2,
"20"=>$precioventa2,// AQUI FINALIZAMOS
"21"=>$porcentaje3,
"22"=>$bruto3,
"23"=>$cantidad.'%',
"24"=>$precio2a,
"25"=>$margen2,
"26"=>$valorventa2,
"27"=>$precioventa2
);
}
$results = array(
	"sEcho"=>1, //Información para el datatables
	"iTotalRecords"=>count($data), //enviamos el total registros al datatable
	"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
	"aaData"=>$data);
echo json_encode($results);

break;

case 'finalizarprocesoprecio':

$detalle=(isset($_REQUEST['detalle']) && !empty($_REQUEST['detalle']))?$_REQUEST['detalle']:'';

for ($i = 0; $i < count($detalle); $i++) {

$txtCOD_ARTICULO=(isset($detalle[$i]["txtCOD_ARTICULO"]))?$detalle[$i]["txtCOD_ARTICULO"]:"0";
$precio1=(isset($detalle[$i]["precio1"]))?$detalle[$i]["precio1"]:"0";
$porcentaje1=(isset($detalle[$i]["porcentaje1"]))?$detalle[$i]["porcentaje1"]:"0";

$precio2=(isset($detalle[$i]["precio2"]))?$detalle[$i]["precio2"]:"0";	
$porcentaje2=(isset($detalle[$i]["porcentaje2"]))?$detalle[$i]["porcentaje2"]:"0";

$precio3=(isset($detalle[$i]["precio3"]))?$detalle[$i]["precio3"]:"0";
$porcentaje3=(isset($detalle2[$i]["porcentaje3"]))?$detalle2[$i]["porcentaje3"]:"0";

$sqlp="UPDATE articulo SET precio='$precio1', precio_porcentaje='$porcentaje1', precio_mayor='$precio2', precio_porcentaje2='$porcentaje2', precio_mayor2='$precio3', precio_porcentaje3='$porcentaje3' WHERE txtCOD_ARTICULO='$txtCOD_ARTICULO' ";
ejecutarConsulta($sqlp);

}

$jsondata['estado'] = '1';
$jsondata['mensaje'] ='PRECIOS ACTUALIZADOS';

/*
echo json_encode($jsondata);		

$jsondata['estado'] = '1';
$jsondata['mensaje'] = '<h3><span class="success"><i class="fa fa-check-circle"></i> Se proceso correctamente.</span></h3>';	
*/
echo json_encode ($jsondata);

break;

case 'precio_lista_general_cliente':

$idcliente = isset($_POST['idcliente']) ? $_POST['idcliente'] : '';
if($idcliente == '' || $idcliente == '0'){
  echo json_encode(["status"=>false,"message"=>"Falta idcliente"]);
  break;
}

// Validación empresa (opcional pero recomendado)
$idempresa = $_COOKIE['id'];

// Borra precios especiales de ese cliente (queda precio lista general)
$sqlDel = "DELETE FROM articulo_precio_cliente 
           WHERE idcliente = '$idcliente'";

$rspta = ejecutarConsulta($sqlDel);

if($rspta){
  echo json_encode([
    "status"=>true,
    "message"=>"Se actualizaron los precios: ahora este cliente usa PRECIO LISTA - GENERAL."
  ]);
} else {
  echo json_encode([
    "status"=>false,
    "message"=>"No se pudo actualizar. Verifica permisos/BD."
  ]);
}

break;

case 'listarcliente':

  $sql3="SELECT * FROM config WHERE id='$_COOKIE[id]' ";
  $fa = ejecutarConsultaSimpleFila($sql3);

  $idcliente = $_GET['idcliente'];

  $idlocal=''; 
  $idlocal2='';

  if($fa && isset($fa['articulo']) && $fa['articulo']=='LOCAL'){
    $idlocal  = ' AND idlocal="'.$_COOKIE['idlocal'].'" ';
    $idlocal2 = ' AND a.idlocal="'.$_COOKIE['idlocal'].'" ';
  }

  $data = Array();

  $draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
  $row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
  $rowperpage = isset($_POST['length']) ? (int)$_POST['length'] : 20;

  // DataTables order
  $columnIndex = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 2;
  $columnSortOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
  $columnSortOrder = ($columnSortOrder === 'desc') ? 'DESC' : 'ASC';

  // Search
  $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
  $searchQuery = " ";

  if($searchValue != ''){
    $sv = $searchValue; // si tienes función limpiar, úsala aquí
    $searchQuery .= " AND (
        a.txtCOD_ARTICULO LIKE '%$sv%' 
        OR a.txtDESCRIPCION_ARTICULO LIKE '%$sv%' 
        OR a.codigo LIKE '%$sv%'
        OR a.txtCOD_ARTICULO IN (
            SELECT cod_articulo FROM articulo_serie 
            WHERE serie LIKE '%$sv%' OR lote LIKE '%$sv%'
        )
    ) ";
  }

  // Mapear columnas DataTable -> SQL (según tu data[]: 0..6)
  // 0 Opciones (no)
  // 1 List.Pre. (depende si existe precio cliente) => ordenamos por "tiene_precio_cliente"
  // 2 ID (txtCOD_ARTICULO)
  // 3 Código (codigo)
  // 4 Nombre (txtDESCRIPCION_ARTICULO)
  // 5 Precio (precio_cliente_o_lista)
  // 6 Estado (condicion)
  $orderBy = " ORDER BY a.txtCOD_ARTICULO ASC ";
  switch($columnIndex){
    case 1:
      $orderBy = " ORDER BY tiene_precio_cliente $columnSortOrder, a.txtCOD_ARTICULO ASC ";
      break;
    case 2:
      $orderBy = " ORDER BY a.txtCOD_ARTICULO $columnSortOrder ";
      break;
    case 3:
      $orderBy = " ORDER BY a.codigo $columnSortOrder ";
      break;
    case 4:
      $orderBy = " ORDER BY a.txtDESCRIPCION_ARTICULO $columnSortOrder ";
      break;
    case 5:
      $orderBy = " ORDER BY precio_calc $columnSortOrder, a.txtCOD_ARTICULO ASC ";
      break;
    case 6:
      $orderBy = " ORDER BY a.condicion $columnSortOrder ";
      break;
  }

  // Total records
  $records = ejecutarConsultaSimpleFila("SELECT count(*) as allcount FROM articulo WHERE idempresa='$_COOKIE[id]' $idlocal ");
  $totalRecords = $records ? (int)$records['allcount'] : 0;

  // Total with filter
  $records = ejecutarConsultaSimpleFila("SELECT count(*) as allcount FROM articulo a WHERE a.idempresa='$_COOKIE[id]' $idlocal2 $searchQuery ");
  $totalRecordwithFilter = $records ? (int)$records['allcount'] : 0;

  // Fetch records (incluye precio cliente o lista + flag)
  $empQuery = "
    SELECT 
      a.txtCOD_ARTICULO,
      a.txtDESCRIPCION_ARTICULO,
      a.codigo,
      a.condicion,
      a.precio,
      a.precio_compra,
      IFNULL(apc.precio, a.precio) AS precio_calc,
      CASE WHEN apc.precio IS NULL THEN 0 ELSE 1 END AS tiene_precio_cliente
    FROM articulo a
    LEFT JOIN articulo_precio_cliente apc
      ON apc.idproducto = a.txtCOD_ARTICULO
      AND apc.idcliente = '$idcliente'
    WHERE a.idempresa='$_COOKIE[id]' $idlocal2
    $searchQuery
    $orderBy
    LIMIT $row, $rowperpage
  ";

  $empRecords = ejecutarConsulta($empQuery);

  while ($reg = $empRecords->fetch_object()) {

    $botones='
      <div class="input-group-btn">
        <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="menuvencli" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i><span class="caret"></span> OPCIONES
        </button>
        <ul class="dropdown-menu" aria-labelledby="menuvencli">
          <li id="venta_newcli">
            <a href="javascript:void(0)" onclick="mostrar('.$reg->txtCOD_ARTICULO.', '.$idcliente.')">
              <i class="fa fa-pencil"></i> Editar precio
            </a>
          </li>
        </ul>
      </div>
    ';

    $precio_base = (float)$reg->precio_calc;
    $precio = number_format($precio_base, 4, '.', '');

    $label_lista = ($reg->tiene_precio_cliente == 1)
      ? '<div class="label label-success">PRECIO LISTA - CLIENTE</div>'
      : '<div class="label label-danger">PRECIO LISTA - GENERAL</div>';

    $estado = ($reg->condicion)
      ? '<div class="label label-success">Activado</div>'
      : '<div class="label label-danger">Desactivado</div>';

    $data[] = array(
      "0" => $botones,
      "1" => $label_lista,
      "2" => $reg->txtCOD_ARTICULO,
      "3" => $reg->codigo,
      "4" => $reg->txtDESCRIPCION_ARTICULO,
      "5" => $precio,
      "6" => $estado
    );
  }

  echo json_encode(array(
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordwithFilter,
    "data" => $data
  ));

break;


case 'mostrarcliente':

  $txtCOD_ARTICULO = $_POST['txtCOD_ARTICULO'];
  $idcliente = $_POST['idcliente'];

  $sql = "
    SELECT 
      a.txtCOD_ARTICULO,
      a.codigo,
      a.txtDESCRIPCION_ARTICULO,
      a.precio,
      a.precio_porcentaje,
      a.precio_mayor,
      a.precio_porcentaje2,
      a.precio_mayor2,
      a.precio_porcentaje3,
      a.precio_compra,
      a.preciooferta,
      IFNULL(s.precio, a.precio) AS preciocliente
    FROM articulo a
    LEFT JOIN articulo_precio_cliente s 
      ON a.txtCOD_ARTICULO = s.idproducto 
      AND s.idcliente = '$idcliente'
    WHERE a.txtCOD_ARTICULO = '$txtCOD_ARTICULO'
    LIMIT 1
  ";

  $rspta = ejecutarConsultaSimpleFila($sql);
  echo json_encode($rspta);

break;


case 'precio_lista_general_producto_cliente':

  $idcliente  = $_POST['idcliente'];
  $idproducto = $_POST['idproducto'];

  if($idcliente=='' || $idproducto==''){
    echo json_encode(["status"=>false,"message"=>"Faltan parámetros"]);
    break;
  }

  // elimina precio especial de ese cliente para ese producto
  ejecutarConsulta("DELETE FROM articulo_precio_cliente 
                    WHERE idcliente='$idcliente' AND idproducto='$idproducto'");

  // devuelve precio lista general para refrescar form
  $row = ejecutarConsultaSimpleFila("SELECT precio FROM articulo 
                                    WHERE txtCOD_ARTICULO='$idproducto' LIMIT 1");
  $precio_lista = $row ? (float)$row['precio'] : 0;

  echo json_encode([
    "status" => true,
    "message" => "Producto regresado a PRECIO LISTA - GENERAL.",
    "precio_lista" => number_format($precio_lista, 4, '.', '')
  ]);

break;

case 'cancelartmp':
    if (isset($_COOKIE['idarticulo'])) {
        $idtmp = $_COOKIE['idarticulo'];
        ejecutarConsulta("DELETE FROM tmp_articuloimages WHERE id_serv='$idtmp'");
        ejecutarConsulta("DELETE FROM tmp_articulo WHERE id='$idtmp'");
        setcookie('idarticulo', '', time() - 3600, '/');
    }
break;




case 'guardaryeditarcliente':

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'EL CÓDIGO DEL PRODUCTO NO EXISTE';		

$sql3="SELECT *FROM articulo_precio_cliente WHERE idproducto='$idproducto' AND idcliente='$idclienteproducto' ";
$fa= ejecutarConsultaSimpleFila($sql3);

if (!$fa){

$sql="INSERT INTO articulo_precio_cliente VALUES (NULL, '$_COOKIE[id]', '$idproducto', '$idclienteproducto', '$precio')";
$rspta=ejecutarConsulta($sql);
$jsondata['mensaje'] = 'PRECIO GUARDADO';

}else{

$sql="UPDATE articulo_precio_cliente SET precio='$precio'  WHERE idproducto='$idproducto' AND idcliente='$idclienteproducto' ";
$rspta=ejecutarConsulta($sql);
$jsondata['mensaje'] = 'PRECIO ACTUALIZADO';

}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($jsondata);

break;


}




?>