<?php 
require_once "../modelos/series.php";
$suc=new Series();

$idserie=isset($_POST["idserie"])? limpiarCadena($_POST["idserie"]):"0";
$id=isset($_POST["id"])? limpiarCadena($_POST["id"]):"0";
$idlocal=isset($_POST["idlocal"])? limpiarCadena($_POST["idlocal"]):"";
$serie=isset($_POST["serie"])? limpiarCadena($_POST["serie"]):"";
$numeroinicio=isset($_POST["numeroinicio"])? limpiarCadena($_POST["numeroinicio"]):"";
$tipo=isset($_POST["tipo"])? limpiarCadena($_POST["tipo"]):"";
$documento=isset($_POST["documento"])? limpiarCadena($_POST["documento"]):"";

$moneda=isset($_POST["moneda"])? limpiarCadena($_POST["moneda"]):"";
$simbolo=isset($_POST["simbolo"])? limpiarCadena($_POST["simbolo"]):"";
$codigo=isset($_POST["codigo"])? limpiarCadena($_POST["codigo"]):"";

$mesa=isset($_POST["mesa"])? limpiarCadena($_POST["mesa"]):"";
$caja=isset($_POST["caja"])? limpiarCadena($_POST["caja"]):"";
$ubigeo=isset($_POST["ubigeo"])? limpiarCadena($_POST["ubigeo"]):"";
$nivel=isset($_POST["nivel"])? limpiarCadena($_POST["nivel"]):"";
$idcategoria=isset($_POST["idcategoria"])? limpiarCadena($_POST["idcategoria"]):"0";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($idserie)){
			$rspta=$suc->insertar($serie, $numeroinicio, $tipo, $documento, $idlocal, $idcategoria);
			echo $rspta ? "Serie registrada".$idserie."" : "Serie no se pudo registrar".$idserie;
		}
		else {

			$rspta=$suc->editarserie($idserie, $serie, $numeroinicio, $tipo, $documento, $idcategoria);
			echo $rspta ? "Serie actualizada / ".$idcategoria."" : "Serie no se pudo actualizar".$idcategoria;
		}
	break;
		
case 'guardaryeditarm':
		if (empty($id)){
			$rspta=$suc->insertarm($moneda, $simbolo, $codigo);
			echo $rspta ? "Moneda registrada" : "Moneda no se pudo registrar";
		}
		else {
			$rspta=$suc->editarm($id, $moneda, $simbolo, $codigo);
			echo $rspta ? "Moneda actualizada" : "Moneda no se pudo actualizar";
		}
	break;
		
case 'guardarm':
			$rspta=$suc->insertarm($mesa, $ids);
			echo $rspta ? "Mesa registrada" : "Mesa no se pudo registrar";
	break;
		
	case 'guardarc':
			$rspta=$suc->insertarc($caja, $ids);
			echo $rspta ? "Caja registrada" : "Caja no se pudo registrar";
	break;

	case 'desactivar':
		$rspta=$suc->desactivar($id);
 		echo $rspta ? "Desactivado" : "Categoría no se puede desactivar";
	break;
		
	case 'desactivarm':
		$rspta=$suc->desactivarm($id);
 		echo $rspta ? "Mesa Desactivada" : "Mesa no se puede desactivar";
	break;
		
	case 'desactivarc':
		$rspta=$suc->desactivarc($id);
 		echo $rspta ? "Caja Desactivada" : "Caja no se puede desactivar";
	break;

	case 'activar':
		$rspta=$suc->activar($id);
 		echo $rspta ? "Activada" : "Categoría no se puede activar";
	break;
		
	case 'activarm':
		$rspta=$suc->activarm($id);
 		echo $rspta ? "Mesa activada" : "Mesa no se puede activar";
	break;
		
	case 'activarc':
		$rspta=$suc->activarc($id);
 		echo $rspta ? "Caja activada" : "Caja no se puede activar";
	break;

	case 'mostrar':
		$rspta=$suc->mostrar($id);
 		echo json_encode($rspta);
	break;
		
case 'mostrars':
		$rspta=$suc->mostrars($id);
 		echo json_encode($rspta);
	break;

	case 'listar':

		$rspta=$suc->listar();
 		$data= Array();

while ($reg=$rspta->fetch_object()){
	
$tipo='';
$doc='';	
	if($reg->tipo=='01'){ $tipo='COMVENCIONAL'; }
	if($reg->tipo=='02'){ $tipo='ELECTRÓNICA'; }
	
	if($reg->documento=='01'){ $doc='FACTURA'; }
	if($reg->documento=='03'){ $doc='BOLETA'; }
	if($reg->documento=='07'){ $doc='NOTA DE CRÉDITO'; }
	if($reg->documento=='08'){ $doc='NOTA DE DÉBITO'; }
	if($reg->documento=='90'){ $doc='RECIBO'; }
	if($reg->documento=='91'){ $doc='COTIZACIÓN'; }
	if($reg->documento=='92'){ $doc='NOTA DE PEDIDO'; }
	if($reg->documento=='09'){ $doc='GUÍA REMISIÓN - REMITENTE'; }
	if($reg->documento=='40'){ $doc='PERCEPCIÓN'; }
if($reg->documento=='31'){ $doc='GUÍA REMISIÓN - TRANSPORTISTA'; }
	
$sqlc="SELECT *FROM sucursal WHERE id='$reg->idlocal' ";
$loc= ejecutarConsultaSimpleFila($sqlc);
	
$data[]=array(
"0"=>($reg->estado)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->id.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->id.')"><i class="fa fa-close"></i></button>':
 					'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->id.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->id.')"><i class="fa fa-check"></i></button>',
				"1"=>$reg->id,
 				"2"=>$reg->serie,
	"3"=>$reg->numeroinicio,
 				"4"=>$tipo,
 				"5"=>$doc,
	"6"=>$loc['sucursal'],
				"7"=>($reg->estado)?'<div class="label label-success">Activado</div>':
 				'<div class="label label-secondary">Desactivado</div>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
		
case 'listarm':

		$rspta=$suc->listarm();
 		$data= Array();

while ($reg=$rspta->fetch_object()){
	
	
$data[]=array(
"0"=>($reg->estado)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->id.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->id.')"><i class="fa fa-close"></i></button>':
 					'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->id.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->id.')"><i class="fa fa-check"></i></button>',
				"1"=>$reg->id,
 				"2"=>$reg->codigo,
 				"3"=>$reg->moneda,
	"4"=>$reg->simbolo,
				"5"=>($reg->estado)?'<span class="label bg-green">Activado</span>':
 				'<span class="label bg-red">Desactivado</span>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
	
		
case 'listarSuc':
$id=$_GET['id'];
		$rspta=$suc->listar($id);
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>'<button class="btn btn-warning btn-xs" onclick="addSucur(\''.$reg->sucursal.'\', \''.$reg->id.'\')"><i class="fa fa-plus"></i></button>',
				"1"=>$reg->id,
 				"2"=>$reg->sucursal,
 				"3"=>$reg->direccion,
 				);
}
$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;	
		
case 'listardest':
$id=$_GET['id'];
		$rspta=$suc->listar($id);
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>'<button class="btn btn-warning btn-xs" onclick="addDest(\''.$reg->sucursal.'\', \''.$reg->id.'\')"><i class="fa fa-plus"></i></button>',
				"1"=>$reg->id,
 				"2"=>$reg->sucursal,
 				"3"=>$reg->direccion,
 				);
}
$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
case 'listarSucE':
$id=$_GET['id'];
		$rspta=$suc->listar($id);
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>'<button class="btn btn-warning btn-xs" onclick="editSucur(\''.$reg->id.'\', \''.$reg->sucursal.'\', \''.$reg->direccion.'\', \''.$reg->ubigeo.'\')"><i class="fa fa-pencil"></i> Editar</button>',
				"1"=>$reg->id,
				"2"=>$reg->ubigeo,
 				"3"=>$reg->sucursal,
 				"4"=>$reg->direccion,
 				);
}
		
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
	
		

		
		
case 'listarmesa':
		
$ids=$_GET["ids"];
		$rspta=$suc->listarmesa($ids);
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>($reg->estado)?'<button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->id.')"><i class="fa fa-close"></i></button>':
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->id.')"><i class="fa fa-check"></i></button>',
				"1"=>$reg->id,
 				"2"=>$reg->mesa,
				"3"=>($reg->estado)?'<span class="label bg-green">Activado</span>':
 				'<span class="label bg-red">Desactivado</span>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
	
case 'ListLocal':
echo '<option value="">SELECCIONE</option>';
$rspta=$suc->listarsucursal();
while ($reg = $rspta->fetch_object()){
echo '<option value='. $reg->id.' >' . $reg->sucursal. '</option>';	
}
		
break;
		
		
case 'listarcaja':
		
$ids=$_GET["ids"];
		$rspta=$suc->listarcaja($ids);
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>($reg->estado)?'<button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->id.')"><i class="fa fa-close"></i></button>':
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->id.')"><i class="fa fa-check"></i></button>',
				"1"=>$reg->id,
 				"2"=>$reg->caja,
				"3"=>($reg->estado)?'<span class="label bg-green">Activado</span>':
 				'<span class="label bg-red">Desactivado</span>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
case 'listarcaja2':
$sucursal=$_GET["sucursal"];
echo '<option value="">SELECCIONE</option>';
$rspta=$suc->listarcaja($sucursal);
while ($reg = $rspta->fetch_object()){
echo '<option value='. $reg->id.' >' . $reg->caja. '</option>';	
}
		
break;

case 'generarserie':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	
	$num=0;	
$tipo=$_GET['tipo'];
if(isset($_COOKIE["idlocal"])){ $idlocal=$_COOKIE["idlocal"]; }else{ $idlocal='1'; }

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
		
if($_GET['seriedoc']!=''){ 

$sqls="SELECT *FROM series WHERE documento='07' AND idlocal='$idlocal' AND idempresa='$_COOKIE[id]'  ORDER BY id DESC ";
$s= ejecutarConsultaSimpleFila($sqls);
$seriedoc=$_GET['seriedoc'].$s['serie'];

}else{
$sqls="SELECT *FROM series WHERE documento='$tipo' AND idlocal='$idlocal' AND idempresa='$_COOKIE[id]'  ORDER BY id DESC ";
$s= ejecutarConsultaSimpleFila($sqls);	
$seriedoc=$s['serie'];	
}
		
$sql="SELECT *FROM venta_orden WHERE serie LIKE '%$seriedoc%' AND tipo='$tipo' AND idlocal='$idlocal' AND idempresa='$_COOKIE[id]' AND beta='$fa[tipo]'  ORDER BY numero DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);

if($mostrar){
$num=$mostrar['numero']+1;	
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
		
		
case 'generarserieingreso':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	
	$num=0;	
$tipo=$_GET['tipo'];
if(isset($_COOKIE["idlocal"])){ $idlocal=$_COOKIE["idlocal"]; }else{ $idlocal='1'; }

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
		
if($_GET['seriedoc']!=''){ 

$sqls="SELECT *FROM series WHERE documento='07' AND idlocal='$idlocal' AND idempresa='$_COOKIE[id]'  ORDER BY id DESC ";
$s= ejecutarConsultaSimpleFila($sqls);
$rest = substr($s['serie'], -2);
$seriedoc=$_GET['seriedoc'].$rest;

}else{
$sqls="SELECT *FROM series WHERE documento='$tipo' AND idlocal='$idlocal' AND idempresa='$_COOKIE[id]'  ORDER BY id DESC ";
$s= ejecutarConsultaSimpleFila($sqls);	
$seriedoc=$s['serie'];	
}
		
$sql="SELECT *FROM ingreso WHERE serie_comprobante='$seriedoc' AND tipo_comprobante='$tipo' AND idlocal='$idlocal' AND idempresa='$_COOKIE[id]' AND beta='$fa[tipo]' ORDER BY idingreso DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);

if($mostrar){
$num=$mostrar['num_comprobante']+1;	
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
			
case 'serieventas':
		
		
$tipo=$_POST["elegido"];
		
if(isset($_COOKIE["idlocal"])){ $idlocal=$_COOKIE["idlocal"]; }else{ $idlocal='1'; }		
$sql="SELECT *FROM series WHERE idlocal='$idlocal' AND documento='$tipo' AND estado='1' ";
$rspta = ejecutarConsulta($sql);
while ($reg = $rspta->fetch_object()){
echo '<option value="' . $reg->serie. '" >' . $reg->serie. '</option>';
}
		
exit();		
break;
		

		
case 'numeroventas':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';
	$num=0;	
		
$serie=$_POST['elegido'];
$tipo=$_POST['tipo'];
		
if(isset($_COOKIE["idlocal"])){ $idlocal=$_COOKIE["idlocal"]; }else{ $idlocal='1'; }

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$sql="SELECT *FROM venta WHERE txtSERIE='$serie' AND idempresa='$_COOKIE[id]' AND txtID_TIPO_DOCUMENTO='$tipo' AND beta='$fa[tipo]' ORDER BY txtNUMERO DESC, txtFECHA_DOCUMENTO  DESC, idventa DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);

if($mostrar){
$num=$mostrar['txtNUMERO']+1;	
}else if(isset($s)){
	
$sqls="SELECT *FROM series WHERE serie='$serie' AND idlocal='$idlocal' ";
$s= ejecutarConsultaSimpleFila($sqls);	
$seriedoc=$s['serie'];
	
$num=$s['numeroinicio'];
}else{
$num='00000001';	
}
		
$num=str_pad($num, 8, "0", STR_PAD_LEFT);

$jsondata['estado'] = '1';
$jsondata['numero'] = $num;
$jsondata['mensaje'] = 'TODO BIEN!';

echo json_encode($jsondata);
exit();	
		
		
break;
		
case 'serienotas':
		
		
$txtSERIE=$_POST["txtSERIE"];
$tipo=$_POST["tipodoc"];
		
$serief=substr($txtSERIE,0,1);
		
if(isset($_COOKIE["idlocal"])){ $idlocal=$_COOKIE["idlocal"]; }else{ $idlocal='1'; }
		
$sql="SELECT *FROM series WHERE idlocal='$idlocal' AND documento='$tipo' ";
$rspta = ejecutarConsulta($sql);
while ($reg = $rspta->fetch_object()){
echo '<option value="'.$serief. $reg->serie. '" >'.$serief.$reg->serie. '</option>';
}
		
exit();		
break;

case 'numeronota':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	
	$num=0;	
		
$serie=$_POST['elegido'];
$tipodoc=$_POST['tipodoc'];
		
if(isset($_COOKIE["idlocal"])){ $idlocal=$_COOKIE["idlocal"]; }else{ $idlocal='1'; }

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
		
$sql="SELECT *FROM venta WHERE txtSERIE='$serie' AND txtID_TIPO_DOCUMENTO='$tipodoc' AND idlocal='$idlocal' AND idempresa='$_COOKIE[id]' AND beta='$fa[tipo]' ORDER BY txtNUMERO DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);

if($mostrar){
$num=$mostrar['txtNUMERO']+1;	
}else if(isset($s)){
$num=$s['numeroinicio'];
}else{
$num='00000001';	
}
		
$num=str_pad($num, 8, "0", STR_PAD_LEFT);

$jsondata['estado'] = '1';
$jsondata['numero'] = $num;
$jsondata['mensaje'] = 'TODO BIEN!';

echo json_encode($jsondata);
exit();	
		
		
break;
		
		
		
}
?>