<?php 
if (strlen(session_id()) < 1) 

require_once "../modelos/credito-debito.php";

$venta=new Venta();

$idventa=isset($_POST["idventa"])? limpiarCadena($_POST["idventa"]):"";
$tipodoc=isset($_POST["tipodoc"])? limpiarCadena($_POST["tipodoc"]):"";
$txtID_CLIENTE=isset($_POST["txtID_CLIENTE"])? limpiarCadena($_POST["txtID_CLIENTE"]):"";
$idusuario=$_COOKIE["idusuario"];
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



switch ($_GET["op"]){
	case 'guardaryeditar':

		
$sql="SELECT *FROM persona WHERE idpersona='$reg->txtCOD_ARTICULO' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
		
		
$rspta=$venta->insertar($txtID_CLIENTE,$idusuario,$txtID_TIPO_DOCUMENTO,$txtSERIE,$txtNUMERO,$txtFECHA_DOCUMENTO,$txtTIPO_DOCUMENTO_CLIENTE,$txtOBSERVACION,$txtSUB_TOTAL,$txtIGV,$txtTOTAL, $txtID_MONEDA,$txtID_TIPO_DOCUMENTO_MODIFICA,$txtNRO_DOC_MODIFICA,$txtID_MOTIVO,$txtCOD_ARTICULO,$txtCANTIDAD_ARTICULO,$txtPRECIO_ARTICULO);
echo $rspta ? "Venta registrada" : "No se pudieron registrar todos los datos de la venta";


	break;

	case 'anular':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR AL ANULAR';	
		
$rspta=$venta->anular($idventa);

$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'DOCUMENTO ANULADO';	

echo json_encode($jsondata);
exit();	
		
		
	break;

	case 'mostrar':
		$rspta=$venta->mostrar($idventa);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
	break;

	case 'listarDetalle':
		//Recibimos el idingreso
		$id=$_GET['id'];

		$rspta = $venta->listarDetalle($id);
		$txtTOTAL=0;
		echo '<thead style="background-color:#A9D0F5">
                                    <th>Opciones</th>
                                    <th>Artículo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Venta</th>
                                    <th>txtSUB_TOTAL</th>
                                </thead>';

		while ($reg = $rspta->fetch_object())
				{
					echo '<tr class="filas"><td></td><td>'.$reg->txtDESCRIPCION_ARTICULO.'</td><td>'.$reg->txtCANTIDAD_ARTICULO.'</td><td>'.$reg->txtPRECIO_ARTICULO.'<td>'.$reg->txtSUB_TOTAL.'</td></tr>';
					$txtTOTAL=$txtTOTAL+($reg->txtPRECIO_ARTICULO*$reg->txtCANTIDAD_ARTICULO);
				}
		echo '<tfoot>
                                    <th>txtTOTAL</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><h4 id="txtTOTAL">S/.'.$txtTOTAL.'</h4><input type="hidden" name="txtTOTAL" id="txtTOTAL"></th> 
                                </tfoot>';
	break;

	case 'listar':
		$rspta=$venta->listar();
 		//Vamos a declarar un array
 		$data= Array();

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$conf= ejecutarConsultaSimpleFila($sql3);
		
if($conf['tipo']==03){ $tipoc='BETA'; }else{ $tipoc='PRODUCCION'; }
		
while ($reg=$rspta->fetch_object()){

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
			
$sql2="SELECT *FROM usuario WHERE idusuario='$reg->idusuario' ";
$mos= ejecutarConsultaSimpleFila($sql2);
	
	$pdf=$conf['ruc'].'-'.$reg->txtID_TIPO_DOCUMENTO.'-'.$reg->txtSERIE.'-'.$reg->txtNUMERO.'.pdf';
	
if($reg->estado=='0'){ $estadob='<span class="label label-default">Pendiente</span>'; }	
if($reg->estado=='1'){ $estadob='<span class="label bg-orange">Enviado</span>'; }	
if($reg->estado=='2'){ $estadob='<span class="label bg-green">Aceptado</span>'; }
if($reg->estado=='3'){ $estadob='<span class="label bg-red">Rechazo</span>'; }
if($reg->estado=='4'){ $estadob='<span class="label label-default">Anul. pend.</span>'; }
if($reg->estado=='5'){ $estadob='<span class="label bg-orange">An. Env.</span>'; }
if($reg->estado=='6'){ $estadob='<span class="label bg-green">An. Acept.</span>'; }

$botones='<a target="_blank" href="../api_cpe/'.$tipoc.'/'.$conf['ruc'].'/'.$pdf.'"><button class="btn btn-default btn-xs"><i class="fa fa-file"></i></button></a> <button class="btn btn-default btn-xs" onclick="sendcorreo(\''.$reg->idventa.'\')" ><i class="fa fa-send"></i></button>';

if($reg->estado=='2'){
$botones.=' <button class="btn btn-danger btn-xs" onclick="baja('.$reg->idventa.')" ><i class="fa fa-remove"></i></button>';
}
	
if($reg->estado=='0'){
$botones.=' <button class="btn btn-danger btn-xs" onclick="reenviaFact(\''.$reg->idventa.'\')"><i class="fa fa-play-circle"></i></button>';
}

$data[]=array(

"0"=>$botones,
"1"=>$reg->idventa,		
	"2"=>date("Y-m-d", strtotime($reg->txtFECHA_DOCUMENTO)),
 				"3"=>$mostrar['nombre'],
 				"4"=>$mos['nombre'],
 				"5"=>$reg->txtSERIE.'-'.$reg->txtNUMERO,
 				"6"=>$reg->txtTOTAL,
				"7"=>$estadob
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
		
	case 'selectCliente':
		require_once "../modelos/Persona.php";
		$persona = new Persona();
$tipodoc=$_GET['tipodoc'];
		$rspta = $persona->listarC($tipodoc);

		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
				}
	break;
		
case 'ClienteRep':
		require_once "../modelos/Persona.php";
		$persona = new Persona();
		$rspta = $persona->listard($tipodoc);
		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
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
		
$rspta=$venta->listar();
 		//Vamos a declarar un array
 		$data= Array();

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$conf= ejecutarConsultaSimpleFila($sql3);
		
if($conf['tipo']==03){ $tipoc='BETA'; }else{ $tipoc='PRODUCCION'; }
		
while ($reg=$rspta->fetch_object()){

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
			
$sql2="SELECT *FROM usuario WHERE idusuario='$reg->idusuario' ";
$mos= ejecutarConsultaSimpleFila($sql2);
	
	$pdf=$conf['ruc'].'-'.$reg->txtID_TIPO_DOCUMENTO.'-'.$reg->txtSERIE.'-'.$reg->txtNUMERO.'.pdf';
	
if($reg->estado=='0'){ $estadob='<span class="label label-default">Pendiente</span>'; }	
if($reg->estado=='1'){ $estadob='<span class="label bg-orange">Enviado</span>'; }	
if($reg->estado=='2'){ $estadob='<span class="label bg-green">Aceptado</span>'; }
if($reg->estado=='3'){ $estadob='<span class="label bg-red">Rechazo</span>'; }
if($reg->estado=='4'){ $estadob='<span class="label label-default">An. pendiente</span>'; }
if($reg->estado=='5'){ $estadob='<span class="label bg-orange">An. Enviado</span>'; }
if($reg->estado=='6'){ $estadob='<span class="label bg-green">An. aceptado</span>'; }
			
$data[]=array(
"0"=>'<a target="_blank" href="../api_cpe/'.$tipoc.'/'.$conf['ruc'].'/'.$pdf.'"><button class="btn btn-default btn-xs"><i class="fa fa-file"></i></button></a> <button class="btn btn-default btn-xs" onclick="sendcorreo(\''.$reg->txtSERIE.'\', \''.$reg->txtNUMERO.'\')" ><i class="fa fa-send"></i></button>'.(($reg->estado=='0')?' <button class="btn btn-danger btn-xs" onclick="reenviaFact(\''.$reg->txtSERIE.'\', \''.$reg->txtNUMERO.'\')" ><i class="fa fa-play-circle"></i></button> ':
' ').(($reg->estado=='2')?' <button class="btn btn-danger btn-xs" onclick="baja('.$reg->idventa.')" ><i class="fa fa-remove"></i></button> ':
' '),
 				"1"=>$reg->txtFECHA_DOCUMENTO,
 				"2"=>$mostrar['nombre'],
 				"3"=>$mos['nombre'],
 				"4"=>$reg->txtSERIE.'-'.$reg->txtNUMERO,
 				"5"=>$reg->txtTOTAL,
				"6"=>$estadob
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);
		
		
	break;
		
		
case 'listarArticulos':
		
if(isset($_GET['tipo'])){ $tipo=$_GET['tipo']; }else{ $tipo='0'; }
		

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
$searchQuery .= " AND  concat(txtSERIE, '-', txtNUMERO) LIKE '%".$_POST['search']['value']."%' ";
}

$buscar=" AND (txtID_TIPO_DOCUMENTO='01' OR txtID_TIPO_DOCUMENTO='03') "; 

$records= ejecutarConsultaSimpleFila("select count(*) as allcount from venta WHERE (estado='0' OR estado='1' OR estado='2') $buscar AND  idempresa='$_COOKIE[id]' ");
$totalRecords = $records['allcount'];

$records= ejecutarConsultaSimpleFila("select count(*) as allcount from venta WHERE (estado='0' OR estado='1' OR estado='2') $buscar AND  idempresa='$_COOKIE[id]' ".$searchQuery);

$totalRecordwithFilter = $records['allcount'];

$sql="SELECT * FROM venta WHERE (estado='0' OR estado='1' OR estado='2') $buscar AND idempresa='$_COOKIE[id]' ".$searchQuery." limit ".$row.",".$rowperpage;
$empRecords=ejecutarConsulta($sql);

$data= Array();

while ($reg=$empRecords->fetch_object()){

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$nombrepersona='';
$idpersona='0';	
if($mostrar){
$nombrepersona=$mostrar['nombre'];
$idpersona=$mostrar['idpersona'];		
}
	
$modifica=$reg->txtSERIE.'-'.$reg->txtNUMERO;
		
$sqlan="SELECT * FROM venta_cobrar WHERE doc_relaciona='$modifica' ORDER BY id DESC";
$man=ejecutarConsultaSimpleFila($sqlan);
	
$saldoactual='0.00';
if($man){
$saldoactual=$man['saldoact'];
}
	
if($reg->estado=='0'){ $estadob='<span class="label label-default">Pendiente</span>'; }	
if($reg->estado=='1'){ $estadob='<span class="label bg-orange">Enviado</span>'; }	
if($reg->estado=='2'){ $estadob='<span class="label bg-green">Aceptado</span>'; }
if($reg->estado=='3'){ $estadob='<span class="label bg-red">Rechazo</span>'; }
if($reg->estado=='4'){ $estadob='<span class="label label-default">An. pendiente</span>'; }
if($reg->estado=='5'){ $estadob='<span class="label bg-orange">An. Enviado</span>'; }
if($reg->estado=='6'){ $estadob='<span class="label bg-green">An. aceptado</span>'; }
			
$data[]=array(
"0"=>'<button class="btn btn-danger btn-xs" onclick="addtablan(\''.$reg->txtSERIE.'\', \''.$reg->txtNUMERO.'\', \''.$reg->txtID_MONEDA.'\', \''.$idpersona.'\', \''.$nombrepersona.'\', \''.$reg->doc_relaciona.'\', \''.$saldoactual.'\', \''.$reg->idventa.'\', \''.$reg->tipo_pago.'\', \''.$reg->medio_pago.'\', \''.$reg->presupuesto.'\', \''.$reg->txtID_TIPO_DOCUMENTO.'\', \''.$reg->exonerado.'\', \''.$reg->inafecta.'\', \''.$reg->gratuita.'\')" ><i class="fa fa-plus"></i></button> ',
"1"=>$reg->idventa,
 				"2"=>$reg->txtSERIE.'-'.$reg->txtNUMERO,
 				"3"=>$reg->txtFECHA_DOCUMENTO ,
				"4"=>$estadob
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
		
case 'listardoc':
	
$rspta=$venta->listarventa2();
$data= Array();

while ($reg=$rspta->fetch_object()){

$sql="SELECT *FROM persona WHERE idpersona='$reg->txtID_CLIENTE' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
if($reg->estado=='0'){ $estadob='<span class="label label-default">Pendiente</span>'; }	
if($reg->estado=='1'){ $estadob='<span class="label bg-orange">Enviado</span>'; }	
if($reg->estado=='2'){ $estadob='<span class="label bg-green">Aceptado</span>'; }
if($reg->estado=='3'){ $estadob='<span class="label bg-red">Rechazo</span>'; }
if($reg->estado=='4'){ $estadob='<span class="label label-default">An. pendiente</span>'; }
if($reg->estado=='5'){ $estadob='<span class="label bg-orange">An. Enviado</span>'; }
if($reg->estado=='6'){ $estadob='<span class="label bg-green">An. aceptado</span>'; }
			
$data[]=array(
"0"=>'<button class="btn btn-danger btn-xs" onclick="adddocr(\''.$reg->txtSERIE.'-'.$reg->txtNUMERO.'\', \''.$mostrar['nombre'].'\', \''.$mostrar['idpersona'].'\', \''.$reg->txtSERIE.'\', \''.$reg->txtNUMERO.'\', \''.$reg->idventa.'\')" ><i class="fa fa-plus"></i></button> ',
	"1"=>$mostrar['nombre'],
 				"2"=>$reg->txtSERIE,
 				"3"=>$reg->txtNUMERO,
 				"4"=>$reg->txtFECHA_DOCUMENTO ,
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
		
		
case 'numero':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	
	$num=0;	
$doc=$_GET['doc'];		
$sql="SELECT *FROM venta WHERE txtSERIE LIKE '%$doc%' ORDER BY idventa DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);

		$num=$mostrar['txtNUMERO']+1;
		$num=str_pad($num, 5, "0", STR_PAD_LEFT);

$jsondata['estado'] = '1';
$jsondata['mensaje'] = $num;	

echo json_encode($jsondata);
exit();	
		
		
break;
		
		
case 'impresion':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
	$serie=$_GET['serie'];
	$numero=$_GET['numero'];
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	

$sql="SELECT *FROM venta WHERE txtSERIE='$serie' AND txtNUMERO='$numero' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);
		
if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

$ruta="../api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero=$mempresa['ruc'].'-'.$mostrar['txtID_TIPO_DOCUMENTO'].'-'.$serie.'-'.$numero.'.pdf';
		
$ruta=$ruta.$fichero;
		
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $ruta;	
		
echo json_encode($jsondata);
exit();	
		
		
break;	
	
case 'enviaSunat':
/*	
$txtSERIE=$_GET["txtSERIE"];
$txtNUMERO=$_GET["txtNUMERO"];
*/		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
		
require "resumen.php";
require "../modelos/numeros-letras.php";

$jsondata = array();

$resumen=new Resumen();
$new = new api_sunat();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = "Error al enviar";

$sql="SELECT *FROM notacredito WHERE id='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[idcliente]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

$detalle = array();
$json = array();

$ndoc='';
if($mcliente['tipo_documento']=='RUC'){ $ndoc='6'; }else if($mcliente['tipo_documento']=='DNI'){ $ndoc='1'; }

$n=0;
$rspta = $resumen->detnota($mostrar['id']);
while ($reg = $rspta->fetch_object()){

$n=$n+1;
$preciod=($reg->precio*$reg->cti);
$preciod=round($preciod, 2);

$json['txtITEM']=$n;
$json["txtUNIDAD_MEDIDA_DET"] ="NIU";
$json["txtCANTIDAD_DET"] = $reg->cti;
$json["txtPRECIO_DET"] = $reg->precio;
$json["txtSUB_TOTAL_DET"] = $reg->subtotal; //PRECIO * CANTIDAD                       
$json["txtPRECIO_TIPO_CODIGO"] = "01";
$json["txtIGV"] = $reg->igv;
$json["txtISC"] = "0";
$json["txtIMPORTE_DET"] = $reg->subtotal; //rowData.IMPORTE; //SUB_TOTAL + IGV
$json["txtCOD_TIPO_OPERACION"] = "10";
$json["txtCODIGO_DET"] = $reg->codigoproducto;
$json["txtDESCRIPCION_DET"] = $reg->nombreproducto;
$json["txtPRECIO_SIN_IGV_DET"] = $reg->precio;
$detalle[]=$json;	

}
	

$data = array(
"txtTIPO_OPERACION"=>"0101",
"txtTOTAL_GRAVADAS"=> $mostrar['txtSUB_TOTAL'],
"txtSUB_TOTAL"=>$mostrar['txtSUB_TOTAL'],
"txtPOR_IGV"=> "18.00", 
"txtTOTAL_IGV"=> $mostrar['txtIGV'],
"txtTOTAL"=> $mostrar['txtTOTAL'],
"txtTOTAL_LETRAS"=> numtoletras($mostrar['txtTOTAL']), 
"txtNRO_COMPROBANTE"=> $mostrar['serie']."-".$mostrar['numero'],
"txtFECHA_DOCUMENTO"=> date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])),
"txtFECHA_VTO"=> date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])),
"txtCOD_TIPO_DOCUMENTO"=> $mostrar['tipodoc'], //01=factura,03=boleta
"txtCOD_MONEDA"=> $mostrar['txtID_MONEDA'],
//==========documentos de referencia(nota credito, debito)=============
"txtTIPO_COMPROBANTE_MODIFICA"=> $mostrar['docmodifica_tipo'],
"txtNRO_DOCUMENTO_MODIFICA"=> $mostrar['docmodifica'],
"txtCOD_TIPO_MOTIVO"=> $mostrar['modifica_motivo'],
"txtDESCRIPCION_MOTIVO"=> $mostrar['modifica_motivod'], //$("[name='txtID_MOTIVO'] option:selected").text(),
//=================datos del cliente=================
 "txtNRO_DOCUMENTO_CLIENTE"=>$mcliente['txtID_CLIENTE'],
 "txtRAZON_SOCIAL_CLIENTE"=>$mcliente['nombre'],
 "txtTIPO_DOCUMENTO_CLIENTE"=>$ndoc,
 "txtDIRECCION_CLIENTE"=>$mcliente['direccion'],
 "txtCIUDAD_CLIENTE"=>"",
 "txtCOD_PAIS_CLIENTE"=>"PE",
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
"detalle"=>$detalle,
);
//echo json_encode($data);
$resultado = $new->sendPostCPE(json_encode($data), RUTA);
//var_dump($resultado);

$me = json_decode($resultado, true);

if($me['cod_sunat']=='0'){ 
$sql="UPDATE notacredito SET estado='2', hash_cpe='$me[hash_cpe]', hash_cdr='$me[hash_cdr]', mensaje='$me[msj_sunat]' WHERE id='$idventa' ";
ejecutarConsulta($sql);
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = $me['msj_sunat'];		
}else{

$sql="UPDATE notacredito SET estado='1', hash_cpe='$me[hash_cpe]', hash_cdr='$me[hash_cdr]', mensaje='$me[msj_sunat]' WHERE idventa='$idventa' ";
ejecutarConsulta($sql);
$jsondata['estado'] = '2';
$jsondata['mensaje'] = $me['msj_sunat'];
	
}
	
echo json_encode($jsondata);
exit();	
	
break;	
		
case 'NDOC':
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

$jsondata = array();
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';	
$jsondata['serie'] = '';
$jsondata['numero'] = '';
		
$idlocal=$_COOKIE["idlocal"];
	
$jsondata['serief'] =$txtSERIE;
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

$sqls="SELECT *FROM series WHERE idlocal='$idlocal' AND idempresa='$_COOKIE[id]' AND documento='$tipodoc' ORDER BY id DESC ";
$ser= ejecutarConsultaSimpleFila($sqls);
		
$serief=substr($txtSERIE,0,1);
		
$serie=$serief.$ser['serie'];

$sql="SELECT *FROM venta WHERE txtSERIE='$serie' AND idempresa='$_COOKIE[id]' AND txtID_TIPO_DOCUMENTO='$tipodoc' AND beta='$fa[tipo]' ORDER BY txtNUMERO DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);
		
if($mostrar){
$numero=$mostrar['txtNUMERO']+1;		
$numero=str_pad($numero, 8, "0", STR_PAD_LEFT);
}else{
$numero=str_pad($ser['numeroinicio'], 8, "0", STR_PAD_LEFT);	
}
		
if($serief=='F'){ $jsondata['tipodoc']='01'; }else{ $jsondata['tipodoc']='03'; }

$jsondata['serie'] = $serie;
$jsondata['numero'] = $numero;
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'GENERADO';	
		
echo json_encode($jsondata);
exit();	
		
		
break;		
		

case 'listardetn':

$idventa=isset($_GET["idventa"])? limpiarCadena($_GET["idventa"]):"";
		
$sql="SELECT *FROM venta WHERE idventa='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$rspta=$venta->listardet($mostrar['idventa']);
$data= Array();

while ($reg=$rspta->fetch_object()){

$comisionf='0.00';
	
$sql2="SELECT *FROM articulo WHERE txtCOD_ARTICULO='$reg->idproducto' ";
$mo= ejecutarConsultaSimpleFila($sql2);
	
$sql3="SELECT *FROM unidad_medida WHERE id='$mo[medida]' ";
$me= ejecutarConsultaSimpleFila($sql3);

$comisionf=$reg->comisiond/$reg->txtCANTIDAD_ARTICULO;
$comisionf=round($comisionf, 2);

$exonerado='<input type="hidden" id="cti2'.$reg->iddetalle_venta.'"  name="cti2'.$reg->iddetalle_venta.'" value="'.$reg->cantidadp.'" >';
$exonerado.='<input type="hidden" id="exo'.$reg->iddetalle_venta.'"  name="exo'.$reg->iddetalle_venta.'" value="'.$reg->exoneradod.'" >';
$exonerado.='<input type="hidden" id="grat'.$reg->iddetalle_venta.'"  name="grat'.$reg->iddetalle_venta.'" value="'.$reg->gratuitad.'" >';
$exonerado.='<input type="hidden" id="icb'.$reg->iddetalle_venta.'"  name="icb'.$reg->iddetalle_venta.'" value="'.$reg->ICB.'" >';
$exonerado.='<input type="hidden" id="comision'.$reg->iddetalle_venta.'"  name="comision'.$reg->iddetalle_venta.'" value="'.$reg->comisiond.'" >';

$exonerado.='<input type="hidden" id="tipoart'.$reg->iddetalle_venta.'"  name="tipoart'.$reg->iddetalle_venta.'" value="'.$reg->tipoarticulo.'" >';

$exonerado.='<input type="hidden" id="inafecta'.$reg->iddetalle_venta.'"  name="inafecta'.$reg->iddetalle_venta.'" value="'.$reg->inafectad.'" >';

$exonerado.='<input type="hidden" id="tipo'.$reg->iddetalle_venta.'"  name="tipo'.$reg->iddetalle_venta.'" value="'.$reg->tipo.'" >';
	
$descripcion='<input type="text" class="form-control  form-control-sm" style="padding:2px; height: 25px; " id="nom'.$reg->iddetalle_venta.'"  name="nom'.$reg->iddetalle_venta.'" value="'.$reg->nombreproducto.'" >';
	
$data[]=array(
 	"0"=>$reg->iddetalle_venta,
 	"1"=>$descripcion,
 	"2"=>$reg->unidadmedida,
	"3"=>'<input type="text" id="p'.$reg->iddetalle_venta.'" name="p'.$reg->iddetalle_venta.'" onkeyup="teclea(\''.$reg->iddetalle_venta.'\', \''.$reg->tipo.'\')" class="form-control"  style="height: 25px; padding:2px; " value="'.$reg->precio.'" />',
	"4"=>'<input type="text" id="c'.$reg->iddetalle_venta.'" name="c'.$reg->iddetalle_venta.'" class="form-control"  style="height: 25px; padding:2px; " onkeyup="teclea(\''.$reg->iddetalle_venta.'\', \''.$reg->tipo.'\')" value="'.$reg->txtCANTIDAD_ARTICULO.'" />'.$exonerado,
"5"=>'<input type="text" id="subtotal'.$reg->iddetalle_venta.'" name="subtotal'.$reg->iddetalle_venta.'" readonly class="form-control form-control-sm" style="height: 25px; padding:2px; " value="'.$reg->subtotal.'" />',
"6"=>'<input type="text" id="igv'.$reg->iddetalle_venta.'" name="igv'.$reg->iddetalle_venta.'" readonly class="form-control form-control-sm" style="height: 25px; padding:2px; " value="'.$reg->igv.'" />',
"7"=>'<input type="text" id="total'.$reg->iddetalle_venta.'" name="total'.$reg->iddetalle_venta.'" readonly class="form-control form-control-sm" style="height: 25px; padding:2px; " value="'.$reg->importe.'" />',
	"8"=>$comisionf,
	"9"=>$reg->comisiond,
	"10"=>$reg->tipo,
	"11"=>$reg->gratuitad,
	"12"=>$reg->idproducto,
	"13"=>$reg->iddetalle_venta,
	"14"=>$reg->idpresentacion,
	"15"=>$reg->codigoproducto
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el txtTOTAL registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el txtTOTAL registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
		
		
case 'listardetn2':

$id=isset($_GET["id"])? limpiarCadena($_GET["id"]):"";
	
$rspta=$venta->listardet($id);
$data= Array();
	
while ($reg=$rspta->fetch_object()){

$sql2="SELECT *FROM articulo WHERE txtCOD_ARTICULO='$reg->idproducto' ";
$mo= ejecutarConsultaSimpleFila($sql2);
	
$sql3="SELECT *FROM unidad_medida WHERE id='$mo[medida]' ";
$me= ejecutarConsultaSimpleFila($sql3);
	
	
$cti='<input type="text" id="cti'.$reg->iddetalle_venta.'" onkeyup="teclea('.$reg->iddetalle_venta.')" name="cti'.$reg->iddetalle_venta.'" value="'.$reg->txtCANTIDAD_ARTICULO.'" size="8" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';

$precio='<input type="text" id="precio'.$reg->iddetalle_venta.'" name="precio'.$reg->iddetalle_venta.'" readonly value="'.$reg->precio.'" size="8" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';
	
$subtotal='<input type="text" id="subtotal'.$reg->iddetalle_venta.'" name="subtotal'.$reg->iddetalle_venta.'" readonly value="'.$reg->subtotal.'" size="8" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';
	
$igv='<input type="text" id="igv'.$reg->iddetalle_venta.'" name="igv'.$reg->iddetalle_venta.'" readonly value="'.$reg->igv.'" size="8" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';
	
$tot='<input type="text" id="tot'.$reg->iddetalle_venta.'" name="tot'.$reg->iddetalle_venta.'" readonly value="'.$reg->importe.'" size="10" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';
	
$operacion='<input type="hidden" id="operacion'.$reg->iddetalle_venta.'" name="operacion'.$reg->iddetalle_venta.'" readonly value="'.$reg->tipo.'" size="10" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';
	
$operacion.='<input type="hidden" id="exoneradod'.$reg->iddetalle_venta.'" name="exoneradod'.$reg->iddetalle_venta.'" readonly value="'.$reg->exoneradod.'" />';
	
$operacion.='<input type="hidden" id="gratuitad'.$reg->iddetalle_venta.'" name="gratuitad'.$reg->iddetalle_venta.'" readonly value="'.$reg->gratuitad.'" />';
	
$operacion.='<input type="hidden" id="medida'.$reg->iddetalle_venta.'" name="medida'.$reg->iddetalle_venta.'" readonly value="'.$reg->unidadmedida.'" />';
	
$data[]=array(
	"0"=>$reg->idproducto,
	"1"=>$reg->iddetalle_venta,
 	"2"=>$reg->codigoproducto,
 	"3"=>$reg->nombreproducto,
 	"4"=>$reg->unidadmedida,
	"5"=>$precio,
	"6"=>$cti,
	"7"=>$subtotal,
	"8"=>$igv,
	"9"=>$tot,
	"10"=>'<button type="button"  class="btn btn-danger btn-xs" ><span class="glyphicon glyphicon-trash"></span></button>'.$operacion
 				);
 		}

 		echo json_encode($data);

	break;
		
		
case 'listardetn3':

$id=isset($_GET["id"])? limpiarCadena($_GET["id"]):"";
	
$rspta=$venta->detorden($id);
$data= Array();
	
while ($reg=$rspta->fetch_object()){
	
$sql2="SELECT *FROM articulo WHERE txtCOD_ARTICULO='$reg->txtCOD_ARTICULO' ";
$mo= ejecutarConsultaSimpleFila($sql2);
	
$data[]=array(
	"0"=>$reg->txtCOD_ARTICULO,
	"1"=>$reg->iddetalle_ingreso,
 	"2"=>$mo['codigo'],
 	"3"=>$mo['txtDESCRIPCION_ARTICULO'],
 	"4"=>$mo['medida'],
	"5"=>$reg->precio_compra,
	"6"=>$reg->txtCANTIDAD_ARTICULO,
	"7"=>$reg->subtotal,
	"8"=>$reg->igv,
	"9"=>$reg->total,
	"10"=>$mo['precio'],
	"11"=>$mo['precio_mayor'],
	"12"=>$reg->otrosgastos,
	"13"=>$reg->tipo
 				);
 		}

echo json_encode($data);

	break;
		
case 'listardetn4':

$id=isset($_GET["id"])? limpiarCadena($_GET["id"]):"";
	
$rspta=$venta->detorden($id);
$data= Array();
	
while ($reg=$rspta->fetch_object()){
	
$cti='<input type="text" id="cti'.$reg->id.'" onkeyup="teclea('.$reg->id.')" name="cti'.$reg->id.'" value="'.$reg->cti.'" size="8" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';

$precio='<input type="text" id="precio'.$reg->id.'" name="precio'.$reg->id.'" readonly value="'.$reg->precio.'" size="8" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';
	
$subtotal='<input type="text" id="subtotal'.$reg->id.'" name="subtotal'.$reg->id.'" readonly value="'.$reg->subtotal.'" size="8" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';
	
$igv='<input type="text" id="igv'.$reg->id.'" name="igv'.$reg->id.'" readonly value="'.$reg->igv.'" size="8" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';
	
$tot='<input type="text" id="tot'.$reg->id.'" name="tot'.$reg->id.'" readonly value="'.$reg->importe.'" size="10" style="margin: 0px; padding: 1px; padding-left: 2px;  " />';
	
$operacion='<input type="hidden" id="operacion'.$reg->id.'" name="operacion'.$reg->id.'" readonly value="0" />';
	
$data[]=array(
	"0"=>$reg->idproducto,
	"1"=>$reg->id,
 	"2"=>$reg->codigoproducto,
 	"3"=>$reg->nombreproducto,
 	"4"=>$reg->unidadmedida,
	"5"=>$precio,
	"6"=>$cti,
	"7"=>$subtotal,
	"8"=>$igv,
	"9"=>$tot,
	"10"=>'<button type="button"  class="btn btn-danger btn-xs" ><span class="glyphicon glyphicon-trash"></span></button>'.$operacion
 				);
 		}

echo json_encode($data);

	break;
		
case 'listaranticipo':

$serie=isset($_GET["serie"])? limpiarCadena($_GET["serie"]):"";
$numero=isset($_GET["numero"])? limpiarCadena($_GET["numero"]):"";
		
	header('Content-Type: application/json');
		
$sql="SELECT *FROM venta WHERE txtSERIE='$serie' AND txtNUMERO='$numero' AND txtID_TIPO_DOCUMENTO='92'  AND idempresa='$_COOKIE[id]'  ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$rspta=$venta->listardet($mostrar['idventa']);
$data= Array();

		
$total=0;
		
if(isset($_GET['page'])){ $page = $_GET['page'];  }else{ $page =1; }
if(isset($_GET['rows'])){ $limit = $_GET['rows'];  }else{ $limit = ''; }
if(isset($_GET['sidx'])){ $sidx = $_GET['sidx'];   }else{ $sidx= 'txtCOD_ARTICULO'; }
if(isset($_GET['sord'])){ $sord = $_GET['sord'];   }else{ $sord= 'asc'; }
		
$sql="SELECT COUNT(*) AS count FROM detalle_venta WHERE idventa='$mostrar[idventa]' ";
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
	
$datos = "SELECT *FROM detalle_venta WHERE idventa='$mostrar[idventa]' ORDER BY $sidx $sord LIMIT $start , $limit ";
$datos = ejecutarConsulta($datos);
	
}else{
	
$datos = "SELECT *FROM detalle_venta WHERE idventa='$mostrar[idventa]' ORDER BY $sidx $sord ";
$datos = ejecutarConsulta($datos);
	
}	

$i=0;


		
while ($reg=$rspta->fetch_object()){

$sql2="SELECT *FROM articulo WHERE txtCOD_ARTICULO='$reg->idproducto' ";
$mo= ejecutarConsultaSimpleFila($sql2);
	
$sql3="SELECT *FROM unidad_medida WHERE id='$mo[medida]' ";
$me= ejecutarConsultaSimpleFila($sql3);

$rows[$i]['id']=$reg->iddetalle_venta;
$rows[$i]['cell']=array($reg->idproducto, $reg->codigoproducto, $reg->nombreproducto, $me['codigo'], $me['tit'], $reg->precio, $reg->txtCANTIDAD_ARTICULO, $reg->subtotal, $reg->igv, $reg->importe, $reg->comisiond, $reg->placa);
$responce=$rows;
		
$i++;
	
}

echo json_encode($responce);

	break;
		
		
		
		
		
}
?>