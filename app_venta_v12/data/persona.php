<?php

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
require_once "../config/conexion.php";
require_once "../modelos/Persona.php";

$persona=new Persona();

$idpersona=isset($_POST["idpersona"])? limpiarCadena($_POST["idpersona"]):"";
$cci=isset($_POST["cci"])? limpiarCadena($_POST["cci"]):"";
$tipo_persona=isset($_POST["tipo_persona"])? limpiarCadena($_POST["tipo_persona"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";

$tipo_documento=isset($_POST["tipo_documento"])? limpiarCadena($_POST["tipo_documento"]):"";
$tipo_documentoU=isset($_POST["tipo_documentoU"])? limpiarCadena($_POST["tipo_documentoU"]):"";

$txtID_CLIENTE=isset($_POST["numerodocumento"])? limpiarCadena($_POST["numerodocumento"]):"";
$txtID_CLIENTEU=isset($_POST["txtID_CLIENTEU"])? limpiarCadena($_POST["txtID_CLIENTEU"]):"";

$direccion=isset($_POST["direccion"])? limpiarCadena($_POST["direccion"]):"";
$telefono=isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$email=isset($_POST["email"])? limpiarCadena($_POST["email"]):"";
$email2=isset($_POST["email2"])? limpiarCadena($_POST["email2"]):"";
$txtRAZON_SOCIAL=isset($_POST["txtRAZON_SOCIAL"])? limpiarCadena($_POST["txtRAZON_SOCIAL"]):"";
$descuento=isset($_POST["descuento"])? limpiarCadena($_POST["descuento"]):"";
$descuentomayor=isset($_POST["descuentomayor"])? limpiarCadena($_POST["descuentomayor"]):"";

$nombrel=isset($_POST["nombrel"])? limpiarCadena($_POST["nombrel"]):"";
$idp=isset($_POST["idp"])? limpiarCadena($_POST["idp"]):"";
$idpl=isset($_POST["idpl"])? limpiarCadena($_POST["idpl"]):"";
$ubigeo=isset($_POST["ubigeo"])? limpiarCadena($_POST["ubigeo"]):"";
$sector=isset($_POST["idcategoria"])? limpiarCadena($_POST["idcategoria"]):"";
$lat=isset($_POST["latitud"])? limpiarCadena($_POST["latitud"]):"";
$lon=isset($_POST["longitud"])? limpiarCadena($_POST["longitud"]):"";

$vendedor=isset($_POST["vendedor"])? limpiarCadena($_POST["vendedor"]):"0";
$obs=isset($_POST["obs"])? limpiarCadena($_POST["obs"]):"";
$pais=isset($_POST["pais"])? limpiarCadena($_POST["pais"]):"PE";
$ciudad=isset($_POST["ciudad"])? limpiarCadena($_POST["ciudad"]):"";
$codigo=isset($_POST["codigo"])? limpiarCadena($_POST["codigo"]):"";
$edad=isset($_POST["edad"])? limpiarCadena($_POST["edad"]):"";

$creditolimite=isset($_POST["creditolimite"])? limpiarCadena($_POST["creditolimite"]):"";
$credito=isset($_POST["credito"])? limpiarCadena($_POST["credito"]):"";
$venta_pago=isset($_POST["id_pago"])? limpiarCadena($_POST["id_pago"]):"0";


if($tipo_documento==''){ $tipo_documento=$tipo_documentoU; }

switch ($_GET["op"]){
	case 'guardaryeditar':
		
		if (empty($idpersona)){
			
			
$sql="SELECT *FROM persona WHERE txtID_CLIENTE='$txtID_CLIENTE' AND idempresa='$_COOKIE[id]' AND tipo_persona='$tipo_persona' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
			
if($mostrar){

echo "EL PROVEEDOR YA ESTA REGISTRADO";
	
}else{

$rspta=$persona->insertar($tipo_persona, $nombre, $tipo_documento, $txtID_CLIENTE, $direccion, $pais, $ciudad, $telefono,$email, $email2, $txtRAZON_SOCIAL, $descuento, $descuentomayor, $sector, $lat, $lon, $codigo, $cci, $vendedor, $creditolimite, $credito, $obs,  $edad, $venta_pago);
echo $rspta ? "Persona registrada" : "Persona no se pudo registrar";

}

}else {

$rspta=$persona->editar($idpersona,$tipo_persona,$nombre,$tipo_documento,$txtID_CLIENTE,$direccion, $pais, $ciudad,$telefono,$email, $email2, $txtRAZON_SOCIAL, $descuento, $descuentomayor, $sector, $lat, $lon, $codigo, $cci, $vendedor, $creditolimite, $credito, $obs,  $edad, $venta_pago);
echo $rspta ? "Persona actualizada".$venta_pago : "Persona no se pudo actualizar";
		}
	break;
		
case 'guardarS':
		if (empty($idpl)){
			$rspta=$persona->insertars($idp, $nombrel, $direccion, $ubigeo);
			echo $rspta ? "Sucursal registrada" : "Sucursal no se pudo registrar";
		}
		else {
			$rspta=$persona->editars($idpl, $nombrel, $direccion, $ubigeo);
			echo $rspta ? "Sucursal actualizada" : "Sucursal no se pudo actualizar";
		}
	break;

	case 'eliminar':
		$rspta=$persona->eliminar($idpersona);
 		echo $rspta ? "Persona eliminada" : "Persona no se puede eliminar";
	break;

	case 'mostrar':
		$rspta=$persona->mostrar($idpersona);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
	break;

	case 'listarp':
		$rspta=$persona->listarp();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idpersona.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-danger btn-xs" onclick="eliminar('.$reg->idpersona.')"><i class="fa fa-trash"></i></button>',
				"1"=>$reg->idpersona,
 				"2"=>$reg->nombre,
 				"3"=>$reg->cci,
 				"4"=>$reg->tipo_documento.': '.$reg->txtID_CLIENTE,
 				"5"=>$reg->telefono,
 				"6"=>$reg->email,
 				"7"=>$reg->txtRAZON_SOCIAL
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;

	case 'listarc':
		$rspta=$persona->listarc();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
			
			
			
			
 			$data[]=array(
 				"0"=>'<button class="btn btn-warning" onclick="mostrar('.$reg->idpersona.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-danger" onclick="eliminar('.$reg->idpersona.')"><i class="fa fa-trash"></i></button>',
 				"1"=>$reg->nombre,
 				"2"=>$reg->tipo_documento,
 				"3"=>$reg->txtID_CLIENTE,
 				"4"=>$reg->telefono,
 				"5"=>$reg->email,
 				"6"=>$reg->txtRAZON_SOCIAL
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
		case 'listard':
		
$idsector=$_GET['idsector'];
		
		$rspta=$persona->listard($idsector);
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
		$botones='<button class="btn btn-default btn-xs" onclick="mostrar('.$reg->idpersona.')"><i class="fa fa-pencil"></i></button> <button type="button" onclick="BuscarSUC('.$reg->idpersona.')" class="btn btn-default btn-xs"><i class="fa fa-home"></i></button> <button class="btn btn-danger btn-xs" onclick="eliminar('.$reg->idpersona.')"><i class="fa fa-trash"></i></button>';
			
$botones.=' <button class="btn btn-danger btn-xs" onclick="mapa(\''.$reg->lat.'\', \''.$reg->lon.'\')"> <i class="fa fa-map-marker"></i> </button>';
			
$sql="SELECT *FROM categoria WHERE idcategoria='$reg->sector' ";
$datos = ejecutarConsultaSimpleFila($sql);

$nombre='';
if($datos){
$nombre=$datos['nombre'];
}


            $data[]=array(
                "0"=>$botones,
                "1"=>$reg->idpersona,
                "2"=>$reg->nombre,
                "3"=>$reg->tipo_documento.': '.$reg->txtID_CLIENTE,
                "4"=>$reg->codigo,
                "5"=>$reg->edad,
                "6"=>$reg->telefono,
                "7"=>$reg->email,
                "8"=>$reg->direccion,
                "9"=>$reg->txtRAZON_SOCIAL,

            );
        }
        $results = array(
            "sEcho"=>1, //Información para el datatables
            "iTotalRecords"=>count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
            "aaData"=>$data);
        echo json_encode($results);

        break;

		
case 'provincias':

$id=$_GET['id'];
$html = "";
$html= '<option value="">-SELECCIONE-</option>';
		
$datos = "SELECT *FROM u_provincias WHERE id_lista='$id' ORDER BY nombre asc ";
$datos = ejecutarConsulta($datos);

while($dato=mysqli_fetch_array($datos)) {
$html.= '<option value="'.$dato["id"].'">'.$dato["nombre"].'</option>';
}

echo $html;	
		
		
break;
		
case 'distrito':

$id=$_GET['id'];
$html = "";
$html= '<option value="">-SELECCIONE-</option>';
		
$datos = "SELECT *FROM u_distritos WHERE id_lista='$id' ORDER BY nombre asc ";
$datos = ejecutarConsulta($datos);

while($dato=mysqli_fetch_array($datos)) {
$html.= '<option value="'.$dato["id"].'">'.$dato["nombre"].'</option>';
}

echo $html;	
		
		
break;
		
case 'verificar':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

$dni=$_POST['dni'];
$tipo_persona=$_POST['tipo_persona'];
		
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['tipo_persona'] =$tipo_persona;
$jsondata['mensaje'] = 'EL CLIENTE YA EXISTE';
$jsondata['doc'] = $dni;
		
$sql="SELECT *FROM persona WHERE txtID_CLIENTE='$dni' AND idempresa='$_COOKIE[id]' AND tipo_persona='$tipo_persona' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

if(!$mostrar){
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'TODO CORRECTO';	
}
echo json_encode($jsondata);
exit();	
		
		
	break;		

/*DESDE AQUI TODO ES PARA LA APP*/		
		
case 'listarapp':
		
$idsector=$_GET['idsector'];
		
		$rspta=$persona->listard($idsector);
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
		$botones='<button class="btn btn-default btn-xs" onclick="mostrar('.$reg->idpersona.')"><i class="fa fa-pencil"></i></button> <button type="button" onclick="BuscarSUC('.$reg->idpersona.')" class="btn btn-default btn-xs"><i class="fa fa-home"></i></button>';
			
$sql="SELECT *FROM categoria WHERE idcategoria='$reg->sector' ";
$datos = ejecutarConsultaSimpleFila($sql);
			
 			$data[]=array(
 				"0"=>$botones,
 				"1"=>$reg->nombre,
 				"2"=>$reg->tipo_documento.': '.$reg->txtID_CLIENTE,
				"3"=>$datos['nombre'],
 				"4"=>$reg->telefono,
 				"5"=>$reg->email,
				"6"=>$reg->direccion,
 				"7"=>$reg->txtRAZON_SOCIAL
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;		

case 'grabacontacto':
		
$sql="INSERT INTO cliente_contacto VALUES (NULL, '$_COOKIE[idusuario]', '$idpersona', '$nombre', '$telefono', '$email', '0')";
$idventanew=ejecutarConsulta_retornarID($sql);
		
echo 'GUARDADO CORRECTAMENTE';
		
break;	

		
case 'contactocliente':

$data= Array();
		
$id=$_GET['id'];
		
$results=ejecutarConsulta("SELECT * FROM cliente_contacto WHERE  idcliente='$id' ORDER BY id DESC ");
while ($reg=$results->fetch_object()){

	

$botones='
      <div class="input-group-btn">
        <button class="btn btn-default  btn-xs dropdown-toggle" type="button" id="menuvencli" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i><span class="caret"></span></button>
        <ul class="dropdown-menu" aria-labelledby="menuvencli">
';
	
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="print(\''.$reg->id.'\')" ><i class="fa fa-print"></i> Imprimir</a></li>';	
	
		
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="sendcorreo(\''.$reg->id.'\')" ><i class="fa fa-send"></i></i> Env. Correo</a></li>';
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="descargaxml(\''.$reg->id.'\')" ><span class="glyphicon glyphicon-cloud-download"></span> Descargar</a></li>';
	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="baja(\''.$reg->id.'\')" ><i class="fa fa-remove"></i> Dar de baja</a></li>';
	
$botones.='</ul></div>';
		
if($reg->estado=='0'){ $estadob='<span class="badge  badge-success">Act.</span>'; }	
if($reg->estado=='1'){ $estadob='<span class="badge badge-warning">Anul.</span>'; }	
			
$data[]=array(
"0"=>$botones,
"1"=>$reg->id,
"2"=>$reg->nombre,
"3"=>$reg->telefono,
"4"=>$reg->correo,
"5"=>$estadob
);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

break;
		
case 'productocliente':

$data= Array();
		
$id=$_GET['id'];
		
$results=ejecutarConsulta("SELECT d.idproducto, v.idventa, v.estado, d.nombreproducto, v.txtFECHA_DOCUMENTO, v.fecha_vto FROM venta v INNER JOIN detalle_venta d ON v.idventa=d.idventa WHERE  v.txtID_CLIENTE='$id' GROUP BY v.idventa ORDER BY v.idventa DESC ");
while ($reg=$results->fetch_object()){

$sql="SELECT *FROM articulo WHERE txtCOD_ARTICULO='$reg->idproducto' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
		
$botones='
      <div class="input-group-btn">
        <button class="btn btn-default  btn-xs dropdown-toggle" type="button" id="menuvencli" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i><span class="caret"></span></button>
        <ul class="dropdown-menu" aria-labelledby="menuvencli">
';
	

if($reg->idventa=='0'){	
$botones.='<li id="venta_newcli"><a class="dropdown-item" href="javascript:void(0)" onclick="generarfacura(\''.$reg->idproducto.'\')" ><i class="fas fa-trash"></i> Generar Factura</a></li>';	
}else{
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="vencimiento(\''.$reg->idproducto.'\')" ><i class="fas fa-envelope-open-text"></i> Fecha Venc</a></li>';
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="sendcorreo(\''.$reg->idproducto.'\')" ><i class="fas fa-envelope-open-text"></i> Env. Correo</a></li>';
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="baja(\''.$reg->idproducto.'\')" ><i class="fas fa-trash"></i> Dar de baja</a></li>';		
}
		
$botones.='</ul></div>';
	
if($reg->estado<='2'){ $estadob='<span class="badge  badge-success">Act.</span>'; }	
if($reg->estado>='3'){ $estadob='<span class="badge badge-warning">Anul.</span>'; }	
			
$data[]=array(
"0"=>$reg->idproducto,
"1"=>$botones,
"2"=>$estadob,
"3"=>$reg->nombreproducto,
"4"=>$reg->txtFECHA_DOCUMENTO,
"5"=>$reg->fecha_vto
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