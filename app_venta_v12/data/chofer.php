<?php
require_once "../modelos/chofer.php";
require_once "../modelos/envio.php";

$usuario=new Usuario();

$idusuario=isset($_POST["idusuario"])? limpiarCadena($_POST["idusuario"]):"";
$idguia=isset($_POST["idguia"])? limpiarCadena($_POST["idguia"]):"0";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$apellido=isset($_POST["apellido"])? limpiarCadena($_POST["apellido"]):"";
$tipo_documento=isset($_POST["tipo_documento"])? limpiarCadena($_POST["tipo_documento"]):"";
$txtID_CLIENTE=isset($_POST["txtID_CLIENTE"])? limpiarCadena($_POST["txtID_CLIENTE"]):"";

$txtID_CLIENTE2=isset($_POST["txtID_CLIENTE2"])? limpiarCadena($_POST["txtID_CLIENTE2"]):"";
$direccion=isset($_POST["direccion"])? limpiarCadena($_POST["direccion"]):"";
$telefono=isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$email=isset($_POST["email"])? limpiarCadena($_POST["email"]):"";
$lisencia=isset($_POST["lisencia"])? limpiarCadena($_POST["lisencia"]):"";
$imagen=isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]):"";

$placa=isset($_POST["placa"])? limpiarCadena($_POST["placa"]):"";
$anio=isset($_POST["anio"])? limpiarCadena($_POST["anio"]):"";
$sector=isset($_POST["sector"])? limpiarCadena($_POST["sector"]):"";
$marca=isset($_POST["marca"])? limpiarCadena($_POST["marca"]):"";
$modelo=isset($_POST["modelo"])? limpiarCadena($_POST["modelo"]):"";
$certificado=isset($_POST["certificado"])? limpiarCadena($_POST["certificado"]):"";
$ruc=isset($_POST["ruc"])? limpiarCadena($_POST["ruc"]):"";
$id=isset($_POST["id"])? limpiarCadena($_POST["id"]):"";

$nombrel=isset($_POST["nombrel"])? limpiarCadena($_POST["nombrel"]):"";
$idp=isset($_POST["idp"])? limpiarCadena($_POST["idp"]):"";
$idpl=isset($_POST["idpl"])? limpiarCadena($_POST["idpl"]):"";
$ubigeo=isset($_POST["ubigeo"])? limpiarCadena($_POST["ubigeo"]):"";

$equipo=isset($_POST["equipo"])? limpiarCadena($_POST["equipo"]):"";
$tarjetacirculacion=isset($_POST["tarjetacirculacion"])? limpiarCadena($_POST["tarjetacirculacion"]):"";
$autorizacionmtc=isset($_POST["autorizacionmtc"])? limpiarCadena($_POST["autorizacionmtc"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':

		if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name']))
		{
			$imagen=$_POST["imagenactual"];
		}
		else 
		{
			$ext = explode(".", $_FILES["imagen"]["name"]);
			if ($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png")
			{
				$imagen = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../images/usuarios/" . $imagen);
			}
		}


		if (empty($idusuario)){
$rspta=$usuario->insertar($nombre, $apellido, $tipo_documento, $txtID_CLIENTE2, $lisencia, $direccion, $telefono, $email, $certificado);
			echo $rspta ? "Usuario registrado" : "No se pudieron registrar todos los datos del usuario";
		}
		else {
$rspta=$usuario->editar($idusuario, $nombre, $apellido, $tipo_documento, $txtID_CLIENTE2, $lisencia, $direccion, $telefono, $email, $certificado);
echo $rspta ? "Usuario actualizado" : "Usuario no se pudo actualizar";
		}
	break;

		
case 'guardarv':


		if (empty($id)){

$rspta=$usuario->insertarv($placa, $anio, $sector, $marca, $modelo, $equipo, $tarjetacirculacion, $autorizacionmtc);
			echo $rspta ? "Vehículo registrado" : "No se pudieron registrar todos los datos del Vehículo ";
		}
		else {
$rspta=$usuario->editarv($id, $placa, $anio, $sector, $marca, $modelo, $equipo, $tarjetacirculacion, $autorizacionmtc);
			echo $rspta ? "Vehículo actualizado" : "Vehículo no se pudo actualizar";
		}
	break;
		
case 'guardarET':

		if (empty($idusuario)){
			$rspta=$usuario->insertarET($nombre, $ruc, $direccion);
			echo $rspta ? "Vehículo registrado" : "No se pudieron registrar todos los datos del Vehículo ";
		}
		else {
			$rspta=$usuario->editarET($idusuario, $nombre, $ruc, $direccion);
			echo $rspta ? "Vehículo actualizado" : "Vehículo no se pudo actualizar";
		}
	break;
		
		
		
	case 'desactivar':
		$rspta=$usuario->desactivar($idusuario);
 		echo $rspta ? "Usuario Desactivado" : "Usuario no se puede desactivar";
	break;
		
case 'desactivarc':
		$rspta=$usuario->desactivarc($idusuario);
 		echo $rspta ? "Chofer Desactivado": "Chofer no se puede desactivar";
	break;

    case 'activarc':
        $rspta=$usuario->activarc($idusuario);
        echo $rspta ? "Chofer Activado": "Chofer no se puede activar";
        break;



    case 'desactivarv':
        $rspta=$usuario->desactivarv($idusuario);
        echo $rspta ? "Vehículo Desactivado": "Vehículo no se puede desactivar";
        break;

    case 'activarv':
        $rspta=$usuario->activarv($idusuario);
        echo $rspta ? "Vehículo Activado": "Vehículo no se puede activar";
        break;

    case 'desactivartr':
        $rspta=$usuario->desactivartr($idusuario);
        echo $rspta ? "Transportista Desactivado": "Transportista no se puede desactivar";
        break;

    case 'activartr':
        $rspta=$usuario->activartr($idusuario);
        echo $rspta ? "Transportista Activado": "Transportista no se puede activar";
        break;





	case 'activar':
		$rspta=$usuario->activar($idusuario);
 		echo $rspta ? "Usuario activado" : "Usuario no se puede activar";
	break;

	case 'mostrar':
		$rspta=$usuario->mostrar($idusuario);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
	break;
		
	case 'mostrarvehiculo':
		$rspta=$usuario->mostrarvehiculo($idusuario);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
	break;

	case 'listar':
		$rspta=$usuario->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>($reg->estado)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->id.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->id.')"><i class="fa fa-close"></i></button>':
 					'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->id.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->id.')"><i class="fa fa-check"></i></button>',
 				"1"=>$reg->nombre,
 				"2"=>$reg->doctipo,
 				"3"=>$reg->docnumero,
 				"4"=>$reg->telefono,
 				"5"=>$reg->correo,
 				"6"=>"<img src='../files/usuarios/".$reg->id."' height='20px' width='20px' >",
 				"7"=>($reg->estado)?'<div class="label label-success">Activado</div>':
 				'<div class="label label-danger">Desactivado</div>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;

case 'listarguia':
		
$fecha_inicio=isset($_GET["fecha_inicio"])? limpiarCadena($_GET["fecha_inicio"]):"0000-00-00";
$fecha_fin=isset($_GET["fecha_fin"])? limpiarCadena($_GET["fecha_fin"]):"0000-00-00";
$estado=isset($_GET["estadoguia"])? limpiarCadena($_GET["estadoguia"]):"";

$estadoguia='';

if($estado=='0'){
	$estadoguia=" AND idventa='0' ";
}else if($estado>'0'){
	$estadoguia=" AND idventa>'0' ";
}

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$conf= ejecutarConsultaSimpleFila($sql3);
		
if($conf['tipo']==3){ $tipoc='BETA'; }else{ $tipoc='PRODUCCION'; }
				
$sql="SELECT * FROM guia_guia WHERE idempresa='$_COOKIE[id]' AND beta='$conf[tipo]' AND DATE(fecha)>='$fecha_inicio'  AND DATE(fecha)<='$fecha_fin' $estadoguia  ORDER BY id DESC ";

$rspta=ejecutarConsulta($sql);	
		
$data= Array();

while ($reg=$rspta->fetch_object()){

$pdf=$conf['ruc'].'-'.$reg->tipodoc.'-'.$reg->serie.'-'.$reg->numero.'.pdf';
			
$sqlc="SELECT *FROM persona WHERE idpersona='$reg->idcliente' ";
$moc= ejecutarConsultaSimpleFila($sqlc);

$botones='
      <div class="input-group-btn">
        <button class="btn btn-default  btn-xs dropdown-toggle" type="button" id="menuvencli" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i><span class="caret"></span> OPCIONES
        </button>
        <ul class="dropdown-menu" aria-labelledby="menuvencli">
';
/*
$sqldetalle="SELECT COUNT(*) AS total FROM guia_detalle  WHERE idguia='$reg->id' ";
$rowdetalle= ejecutarConsultaSimpleFila($sqldetalle);
	*/		
if($_COOKIE['facturacione']==1){
if($reg->estado=='0'){	
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="mostrarguia(\''.$reg->id.'\')" ><i class="fa fa-pencil"></i> Modificar</a></li>';
}
	
}

if($reg->tipodoc=='09'){	
$botones.='<li id="venta_newcli"><a target="_blank" target="_blank" href="plugins/dompdf/guia.php?id='.$reg->id.'" >
<i class="fa fa-file"></i> Ver documento</a></li>';
}else{
 $botones.='<li id="venta_newcli"><a target="_blank" target="_blank" href="plugins/tcpdf/guia.php?id='.$reg->id.'" >
<i class="fa fa-file"></i> Ver documento</a></li>';   
}

$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="bajaguia('.$reg->id.')">
<i class="fa fa-trash"></i> Anular</a></li>';

$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="descargaxml(\''.$reg->id.'\')" ><span class="glyphicon glyphicon-cloud-download"></span> Descargar</a></li>';			
			
if($reg->estado=='1'||$reg->estado=='0'||$reg->estado=='3'){
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="reenviaFact('.$reg->id.')" ><i class="fa fa-file"></i> REENVIAR</a></li>';
}
//revisafactura(ruc, tipo, fecha, serie, numero, monto, idventa, pago)
$fecha=date("d/m/Y", strtotime($reg->fecha));
$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="revisafactura(\''.$conf['ruc'].'\', \''.$reg->tipodoc.'\', \''.$fecha.'\', \''.$reg->serie.'\', \''.$reg->numero.'\', '.$reg->id.', '.$reg->id.')" ><i class="fa fa-file"></i> REVISAR DOCUMENTO</a></li>';

$botones.='<li id="venta_newcli"><a href="javascript:void(0)" onclick="revisaitems('.$reg->id.')" ><i class="fa fa-file"></i> REVISAR ITEMS</a></li>';
			
$botones.='</ul></div>';
	
if($reg->estado=='0'){ 	
$estadob='<div class="label label-default">Pendiente</div>';	
}
if($reg->estado=='1'){ 	
$estadob='<div class="label label-secondary">Enviado</div>';	
}
		

if($reg->estado=='2'){ $estadob='<div class="label label-success">Aceptado</div>'; }
if($reg->estado=='3'){ $estadob='<div class="label label-danger">Enviado</div>'; }
if($reg->estado=='4'){ $estadob='<div class="label label-danger">Anulado</div>'; }
if($reg->estado=='5'){ $estadob='<div class="label label-danger">Fuera de Fecha</div>'; }

$estadoventa='<div class="label label-default">Pendiente</div>';
if($reg->idventa>'0'){ $estadoventa='<div class="label label-success">Entregado</div>'; }

$nombredesc='';
if($moc){ $nombredesc=$moc['nombre']; }

$docrelacionado='';

$sqlventa="SELECT *FROM venta WHERE idventa='$reg->idventa' ";
$relventa= ejecutarConsultaSimpleFila($sqlventa);

if($relventa){ $docrelacionado=$relventa['txtSERIE'].'-'.$relventa['txtNUMERO']; }

$data[]=array(
	"0"=>$reg->id,
 				"1"=>$botones,
				//"1"=>$reg->id.'['.$rowdetalle['total'].']',
				"2"=>$estadob,

 				"3"=>$reg->serie.'-'.$reg->numero,
 				"4"=>$reg->motivo,
				"5"=>$nombredesc,
 				"6"=>date("Y-m-d", strtotime($reg->fecha)),
 				"7"=>date("Y-m-d", strtotime($reg->fecha_transporte)),
 				"8"=>$reg->tipo_transporte,
                "9"=>$docrelacionado,

 				"10"=>$estadoventa,
				"11"=>$reg->id
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
		
case 'listarv':
		$rspta=$usuario->listarv();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>($reg->estado)?'<button class="btn btn-warning" onclick="mostrarvehiculo('.$reg->id.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-danger" onclick="desactivarv('.$reg->id.')"><i class="fa fa-close"></i></button>':
 					'<button class="btn btn-warning" onclick="mostrar('.$reg->id.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-primary" onclick="activarv('.$reg->id.')"><i class="fa fa-check"></i></button>',
 				"1"=>$reg->placa,
 				"2"=>$reg->anio,
 				"3"=>$reg->sector,
 				"4"=>$reg->marca,
 				"5"=>$reg->modelo,
 				"6"=>($reg->estado)?'<span class="label bg-green">Activado</span>':
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
		
case 'listarv2':
		$rspta=$usuario->listarv2();
 		$data= Array();
    echo '<option value="">SELECCIONE</option>';
 		while ($reg=$rspta->fetch_object()){
echo '<option value=' . $reg->id. '>' . $reg->placa. '</option>';
 		}
break;		
case 'listarMOT':
		$rspta=$usuario->listarMOT();
 		$data= Array();
 		while ($reg=$rspta->fetch_object()){
echo '<option value=' . $reg->codigo. '>' . $reg->nombre. '</option>';
 		}

	break;
		
case 'listarch':
		$rspta=$usuario->listarch();
 		$data= Array();
    echo '<option value="">SELECCIONE</option>';
 		while ($reg=$rspta->fetch_object()){
echo '<option value=' . $reg->id. '>' . $reg->nombre. '</option>';
 		}

	break;

    case 'listarch2':
        $rspta2=$usuario->listarch2();
        $data= Array();
        echo '<option value="">SELECCIONE</option>';
        while ($reg=$rspta2->fetch_object()){
            echo '<option value=' . $reg->id. '>' . $reg->nombre. '</option>';
        }

        break;
		
	
case 'listarET':
		$rspta=$usuario->listarET();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>($reg->estado)?'<button class="btn btn-warning" onclick="mostrar('.$reg->id.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-danger" onclick="desactivartr('.$reg->id.')"><i class="fa fa-close"></i></button>':
 					'<button class="btn btn-warning" onclick="mostrar('.$reg->id.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-primary" onclick="activartr('.$reg->id.')"><i class="fa fa-check"></i></button>',
 				"1"=>$reg->nombre,
 				"2"=>$reg->ruc,
 				"3"=>$reg->direccion,
 				"4"=>$reg->fecha,
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
		
case 'listarETP':
		$rspta=$usuario->listarET();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>'<button class="btn btn-warning  btn-xs" onclick="addTrans(\''.$reg->nombre.'\', \''.$reg->id.'\')"><i class="fa fa-plus"></i></button>',
 				"1"=>$reg->nombre,
 				"2"=>$reg->ruc,
 				"3"=>$reg->direccion,
 				"4"=>$reg->fecha
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		

	case 'permisos':
		//Obtenemos todos los permisos de la tabla permisos
		require_once "../modelos/Permiso.php";
		$permiso = new Permiso();
		$rspta = $permiso->listar();

		//Obtener los permisos asignados al usuario
		$id=$_GET['id'];
		$marcados = $usuario->listarmarcados($id);
		//Declaramos el array para almacenar todos los permisos marcados
		$valores=array();

		//Almacenar los permisos asignados al usuario en el array
		while ($per = $marcados->fetch_object())
			{
				array_push($valores, $per->idpermiso);
			}

		//Mostramos la lista de permisos en la vista y si están o no marcados
		while ($reg = $rspta->fetch_object())
				{
					$sw=in_array($reg->idpermiso,$valores)?'checked':'';
					echo '<li> <input type="checkbox" '.$sw.'  name="permiso[]" value="'.$reg->idpermiso.'">'.$reg->nombre.'</li>';
				}
	break;

	case 'verificar':
		$logina=$_POST['logina'];
	    $clavea=$_POST['clavea'];

	    //Hash SHA256 en la contraseña
		$clavehash=hash("SHA256",$clavea);

		$rspta=$usuario->verificar($logina, $clavehash);

		$fetch=$rspta->fetch_object();

		if (isset($fetch))
	    {
	        //Declaramos las variables de sesión
	        $_SESSION['idusuario']=$fetch->idusuario;
	        $_SESSION['nombre']=$fetch->nombre;
	        $_SESSION['imagen']=$fetch->imagen;
	        $_SESSION['login']=$fetch->login;

	        //Obtenemos los permisos del usuario
	    	$marcados = $usuario->listarmarcados($fetch->idusuario);

	    	//Declaramos el array para almacenar todos los permisos marcados
			$valores=array();

			//Almacenamos los permisos marcados en el array
			while ($per = $marcados->fetch_object())
				{
					array_push($valores, $per->idpermiso);
				}

			//Determinamos los accesos del usuario
			in_array(1,$valores)?$_SESSION['escritorio']=1:$_SESSION['escritorio']=0;
			in_array(2,$valores)?$_SESSION['almacen']=1:$_SESSION['almacen']=0;
			in_array(3,$valores)?$_SESSION['compras']=1:$_SESSION['compras']=0;
			in_array(4,$valores)?$_SESSION['ventas']=1:$_SESSION['ventas']=0;
			in_array(5,$valores)?$_SESSION['acceso']=1:$_SESSION['acceso']=0;
			in_array(6,$valores)?$_SESSION['consultac']=1:$_SESSION['consultac']=0;
			in_array(7,$valores)?$_SESSION['consultav']=1:$_SESSION['consultav']=0;

	    }
	    echo json_encode($fetch);
	break;

	case 'salir':
		//Limpiamos las variables de sesión   
        session_unset();
        //Destruìmos la sesión
        session_destroy();
        //Redireccionamos al login
        header("Location: ../index.php");

	break;
		
case 'numero':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';

$num=0;	
		
if(isset($_COOKIE["idlocal"])){ $idlocal=$_COOKIE["idlocal"]; }else{ $idlocal='1'; }

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

    $tipo=$_GET['tipo'];

    $sqls="SELECT *FROM series WHERE documento='$tipo' AND idlocal='$idlocal' AND idempresa='$_COOKIE[id]'  ORDER BY id DESC ";
    $s= ejecutarConsultaSimpleFila($sqls);

    $sql="SELECT *FROM guia_guia WHERE serie LIKE '%$s[serie]%' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
    $mostrar= ejecutarConsultaSimpleFila($sql);

    if($mostrar){
        $num=$mostrar['numero']+1;
    }else{
        $num=$s['numeroinicio'];
    }

    $num=str_pad($num, 8, "0", STR_PAD_LEFT);

    $jsondata['estado'] = '1';
    $jsondata['mensaje'] = $num;
    $jsondata['serie'] = $s['serie'];
		
echo json_encode($jsondata);
exit();	
		
		
break;

		
		
case 'enviaSunat':
	
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");


$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = "Error al enviar";

enviarguia($id);

echo json_encode($jsondata);
break;

exit();
		
		
case 'listarsaldo':

$id=isset($_GET["id"])? limpiarCadena($_GET["id"]):"";
	
$sqlnn="SELECT d.idproducto, d.codigoproducto, d.nombreproducto, d.txtCANTIDAD_ARTICULO, v.idventa FROM detalle_venta d LEFT JOIN venta v ON d.idventa=v.idventa WHERE v.idventa='$id' ";
$rspta =ejecutarConsulta($sqlnn);
$data= Array();	
while ($reg=$rspta->fetch_object()){

$sql2="SELECT SUM(cantidad) cantidad FROM guia_detalle d LEFT JOIN guia_guia g ON d.idguia=g.id  WHERE g.iddoc_relacionado='$reg->idventa' AND d.idproducto='$reg->idproducto' AND g.estado!='4'  ";
$mo= ejecutarConsultaSimpleFila($sql2);
	
$entregados='0.00';
$saldo=$reg->txtCANTIDAD_ARTICULO;
if($mo['cantidad']!=''){
$entregados=$mo['cantidad'];
$saldo=round(($reg->txtCANTIDAD_ARTICULO-$mo['cantidad']), 2);
}
	
$data[]=array(
	"0"=>$reg->idproducto,
 	"1"=>$reg->codigoproducto,
 	"2"=>$reg->nombreproducto,
	"3"=>$reg->txtCANTIDAD_ARTICULO,
	"4"=>$entregados,
	"5"=>$saldo
);
 		}

 		echo json_encode($data);

	break;
		

case 'bajaguia':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR AL ANULAR';	

$sql_detalle="UPDATE guia_guia SET estado='4' WHERE id='$id' ";
$sql=ejecutarConsulta($sql_detalle) or $sw = false;	
				
if($sql){
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'PREPARADO PARA DAR DE BAJA';	
}

echo json_encode($jsondata);	
		
exit();	
		
break;
	
case 'verificarguia':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR AL ANULAR';	

$idguia=isset($_GET["id"])? limpiarCadena($_GET["id"]):"0";

$sqlguia="SELECT *FROM guia_guia WHERE id='$idguia' ";
$guia= ejecutarConsultaSimpleFila($sqlguia);

$sql3="SELECT *FROM config WHERE id='$guia[idempresa]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

//echo $sql3;

$sqlgre="SELECT *FROM gre WHERE idempresa='$guia[idempresa]' ";
$gre= ejecutarConsultaSimpleFila($sqlgre);		

$id=$gre['public'];

$clave=$gre['secret'];
$clave = str_replace("+", "%2B", $clave);
$clave = str_replace("==", "%3D%3D", $clave);

if($fa['sunat']=='SUNAT'){
$username=$fa['ruc'].$fa['usuario'];
$password=$fa['clave'];	
}else{
$username=$gre['ruc'].$gre['usuariosol'];
$password=$gre['clavesol'];  
}

$mensaje['idguia']=$idguia;
		
/*CREAMOS EL TOKEN*/
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api-seguridad.sunat.gob.pe/v1/clientessol/'.$id.'/oauth2/token/',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'grant_type=password&scope=https%3A%2F%2Fapi-cpe.sunat.gob.pe&client_id='.$id.'&client_secret='.$clave.'&username='.$username.'&password='.$password,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded',
    'Cookie: TS019e7fc2=019edc9eb82dcd8fec0a3bd848e49fb99eec6d2c3bf4b04081df2d440b003ae1fb1930ddaa2a6bc63ebbdcca3f4f2ff9c2d23a32af'
  ),
));

$response = curl_exec($curl);
//var_dump($response);
curl_close($curl);

$response=json_decode($response);
		
if(isset($response->access_token)){
		
$token_access=$response->access_token;
	
//$mensaje['ticket']=$guia['ticket'];
		
/*VALIDAMOS EL ESTADO DE LA GUIA*/			
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/envios/'.$guia['ticket'],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'numRucEnvia: '.$fa['ruc'],
    'numTicket: '.$guia['ticket'],
    'Authorization: Bearer '. $token_access,
  ),
));

$response3 = curl_exec($curl);
//var_dump($response3);
$response3=json_decode($response3);
$codRespuesta=$response3->codRespuesta;

curl_close($curl);

$mensaje['cod_sunat'] =$codRespuesta;

if($codRespuesta=='99'){

$error=$response3->error;
$mensaje['numerror']=$error->numError;
$mensaje['msj_sunat']=$error->desError;	
$mensaje['hash_cdr'] ='';

if($error->numError=='1033'){
$sql="UPDATE guia_guia SET estado='2' WHERE id='$idguia' ";
ejecutarConsulta($sql); 
}

}else if($codRespuesta=='98'){

$mensaje['numerror']='99';
$mensaje['msj_sunat']='Envío en proceso';
$mensaje['hash_cdr'] ='';

}else if($codRespuesta=='0'){

//$mensaje['arcCdr']=$response3->arcCdr;
$mensaje['indCdrGenerado']=$response3->indCdrGenerado;

if($fa['tipo']==3){ $tipoc='BETA'; }else{ $tipoc='PRODUCCION'; }
	
$ruta_archivo_cdr='../api_cpe/'.$tipoc.'/'.$fa['ruc'].'/';
$archivo=$fa['ruc'].'-'.$guia['tipodoc'].'-'.$guia['serie'].'-'.$guia['numero'];
	
	
file_put_contents($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP', base64_decode($response3->arcCdr));

            //extraemos archivo zip a xml
            $zip = new ZipArchive;
            if ($zip->open($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP') === TRUE) {
                $zip->extractTo($ruta_archivo_cdr);
                $zip->close();
            }
unlink($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP');
 //=============hash CDR=================
            $doc_cdr = new DOMDocument();
            if (file_exists(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.XML')) {
$doc_cdr->load(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.XML');
            }else{
$doc_cdr->load(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.xml');           
            }


//var_dump($doc_cdr);

            $mensaje['cod_sunat'] = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;
            $mensaje['msj_sunat'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
            $mensaje['hash_cdr'] = $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;
            $mensaje['linkqr'] = $doc_cdr->getElementsByTagName('DocumentDescription')->item(0)->nodeValue;
            
$hascdr=$doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;
$msj_sunat=$doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
$linkqr= $doc_cdr->getElementsByTagName('DocumentDescription')->item(0)->nodeValue;
	
$sql="UPDATE guia_guia SET estado='2', hash_cdr='$hascdr', mensaje='$msj_sunat', rutaqr='$linkqr' WHERE id='$idguia' ";
ejecutarConsulta($sql);
	
}else{

$mensaje['numerror']='88';
$mensaje['msj_sunat']='SUNAT FUERA DE SERVICIO';
$mensaje['hash_cdr'] ='';
	
}

}else{
$mensaje['cod_sunat']='99';
$mensaje['error']=$response->error;
$mensaje['msj_sunat']=$response->error_description;	
$mensaje['hash_cdr'] ='';
}
		
echo json_encode($mensaje);	
		
exit();	
		
break;
		

case 'mostrarguia':

 		//Codificar el resultado utilizando json
		
$sql="SELECT 

v.id, v.observacion, DATE(v.fecha) as fecha, DATE(v.fecha_transporte) as fechatransporte, v.iddoc_relacionado, v.idcliente, p.nombre as cliente,   v.tipodoc, v.serie, v.numero,  v.motivo, v.motivoid, v.peso, v.cajas, v.ncarga, v.placacarreta, v.kardex, s.id AS idpartida, s.sucursal AS partida, d.id AS iddestino, d.sucursal AS destino, gt.id AS idtransporte, gt.nombre AS emptransporte, gch.id AS idchofer, gch.nombre, gve.id AS idvehiculo,  gve.placa, v.tipo_transporteid, v.observacion, v.docadicional, v.docadicionalnum


FROM guia_guia v 

INNER JOIN persona p ON v.idcliente=p.idpersona 
INNER JOIN sucursal s ON v.sucursal=s.id 
LEFT JOIN sucursal d ON d.id=v.destino  
LEFT JOIN guia_transportista gt ON gt.id=v.emptrans_id 
LEFT JOIN guia_chofer gch ON gch.id=v.idchofer 
LEFT JOIN guia_vehiculo gve ON gve.id=v.idvehiculo 

WHERE v.id='$idguia' ";
$rspta=ejecutarConsultaSimpleFila($sql);
		
 		echo json_encode($rspta);
	break;	
		

case 'detalleguia':
		//Recibimos el idingreso
		header('Content-Type: application/json');
		$id=$_GET['id'];

		
$sql="SELECT * FROM guia_detalle  where idguia='$id' ORDER BY id DESC ";
$rspta =ejecutarConsulta($sql);

		$txtTOTAL=0;
		
$responce=[];		
$total=0;
		
		if(isset($_GET['page'])){ $page = $_GET['page'];  }else{ $page =1; }
if(isset($_GET['rows'])){ $limit = $_GET['rows'];  }else{ $limit = ''; }
if(isset($_GET['sidx'])){ $sidx = $_GET['sidx'];   }else{ $sidx= 'txtCOD_ARTICULO'; }
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
			
$rows[$i]['id']=$reg->idproducto;
$rows[$i]['cell']=array($reg->idproducto, $reg->codigoproducto, $reg->tipo, $reg->nombreproducto, $reg->unidadmedida, '', $reg->precio, $reg->cantidad, $reg->subtotal, $reg->igv, $reg->importe, '0', $reg->exoneradod, $reg->gratuitad, '0', $reg->precio, '0', $reg->id, '0', '0', '0', '0', '0');
$responce=$rows;
		
$i++;	
}
	
echo json_encode($responce);
		
	break;
		
		
case 'addchofer':
	
		
$sql="INSERT INTO guia_chofer (nombre, apellido, idempresa, doctipo, docnumero, lisencia, direccion, telefono, correo, estado, fecha, certificado)
VALUES ('$nombre', '$apellido', '$_COOKIE[id]', '$tipo_documento', '$txtID_CLIENTE', '$lisencia', '$direccion', '$telefono', '$email', '1', NOW(), '$certificado')";
$idventanew=ejecutarConsulta_retornarID($sql);	
	
		
$jsondata['id'] = $idventanew;
$jsondata['nombre'] = $nombre.' '.$apellido;
$jsondata['mensaje'] = 'CHOFER AGREGADO CON ÉXITO';
		
echo json_encode($jsondata);
		
	break;		
		
	
case 'addvehiculo':
	
$sql="INSERT INTO guia_vehiculo (placa, idempresa, anio, sector, marca, modelo, estado, fecha)
VALUES ('$placa', '$_COOKIE[id]',  '$anio', '$sector', '$marca', '$modelo', '1', NOW())";
$idventanew=ejecutarConsulta_retornarID($sql);	
		
$jsondata['id'] = $idventanew;
$jsondata['nombre'] = $placa;
$jsondata['mensaje'] = 'VEHICULO AGREGADO CON ÉXITO';
		
echo json_encode($jsondata);
		
	break;	

	
case 'addsucursal':
			
$sql="INSERT INTO sucursal (nivel, idnivel, sucursal, direccion, ubigeo, estado, observacion, idempresa, telefono, idsunat, exonerado, exclusivo)
		VALUES ('0', '0','$nombrel','$direccion', '$ubigeo', '1', '', '$_COOKIE[id]', '', '', '', 'NO')";
$idventanew=ejecutarConsulta_retornarID($sql);	
		
$jsondata['id'] = $idventanew;
$jsondata['nombre'] = $nombrel;
$jsondata['mensaje'] = 'SUCURSAL AGREGADO CON ÉXITO';
		
echo json_encode($jsondata);
		
break;

case 'veritemsguia':
			
$sqldetalle="SELECT COUNT(*) AS total FROM guia_detalle  WHERE idguia='$idusuario' ";
$rowdetalle= ejecutarConsultaSimpleFila($sqldetalle);
		
echo $rowdetalle['total'];
		
break;





		
}





?>