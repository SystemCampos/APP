<?php 
//Incluímos inicialmente la conexión a la base de datos
Class Persona{
	//Implementamos nuestro constructor
	public function __construct(){

	}

	//Implementamos un método para insertar registros
public function insertar($tipo_persona,$nombre,$tipo_documento,$txtID_CLIENTE,$direccion, $pais, $ciudad,$telefono, $email, $email2,  $txtRAZON_SOCIAL, $descuento, $descuentom, $sector, $lat, $lon, $codigo, $cci, $vendedor, $creditolimite, $credito, $obs,  $edad, $venta_pago)
	{
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
		
if($descuento==''){ $descuento='0'; }
if($descuentom==''){ $descuentom='0'; }
if($lat==''){ $lat='0'; }
if($lon==''){ $lon='0'; }
if($sector==''){ $sector='0'; }
        if($creditolimite==''){ $sector='0'; }
        if($credito==''){ $sector='0'; }

        if($_COOKIE['id']=='1'){

            $sqlnn="SELECT *FROM persona WHERE idempresa='$_COOKIE[id]'  ORDER BY idpersona DESC ";
            $mostrarnn= ejecutarConsultaSimpleFila($sqlnn);
            if($codigo==''){ $codigo=$mostrarnn['codigo']+1; }

        }else{
            if($codigo=='!='){ $codigo= $codigo ;}
        }
        
         if($creditolimite==''){ $creditolimite='0'; }
        if($credito==''){ $credito='0'; }
        $codigo=str_pad($codigo, 6, "0", STR_PAD_LEFT);
$sql="INSERT INTO persona VALUES (NULL, '$_COOKIE[id]', '$vendedor', '$tipo_persona', '$codigo', '$descuentom',  '$nombre', '$tipo_documento', '$txtID_CLIENTE', '$sector', '$direccion', '$pais', '$ciudad', '$lat', '$lon', '$telefono', '$email', '$email2','', '$txtRAZON_SOCIAL', '$descuento', '$cci', '0', '0','0','$obs', '$edad', '$venta_pago')";
		//return ejecutarConsulta($sql);
		$id=ejecutarConsulta_retornarID($sql);

//echo $sql;
	
$jsondata = array();
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'Error no se pudo agregar.';

if($id!=''){
$jsondata['estado'] = '1';
$jsondata['id'] = $id;
$jsondata['mensaje'] = 'Cliente: '.$nombre.', agregado con exito';
$jsondata['doc'] =$tipo_documento;
$jsondata['nombre'] = $nombre;
}
		
echo json_encode($jsondata);
exit();	
		
		
	}
	
public function insertars($idp, $nombrel, $direccion, $ubigeo)	{
$sql="INSERT INTO sucursal (nivel, idnivel, sucursal, direccion, ubigeo, estado, observacion, idempresa, telefono, idsunat, exonerado, exclusivo)
VALUES ('$idp', '1','$nombrel','$direccion', '$ubigeo', '1', '', '$_COOKIE[id]', '', '', '', 'NO')";
return ejecutarConsulta($sql);
}

	//Implementamos un método para editar registros
public function editar($idpersona, $tipo_persona, $nombre, $tipo_documento, $txtID_CLIENTE, $direccion, $pais, $ciudad, $telefono, $email, $email2,  $txtRAZON_SOCIAL, $descuento, $descuentom, $sector, $lat, $lon, $codigo, $cci, $vendedor, $obs,$creditolimite, $credito, $edad, $venta_pago)	{

if($descuento==''){ $descuento='0'; }
if($descuentom==''){ $descuentom='0'; } 
if($sector==''){ $sector='0'; }
$sql="UPDATE persona SET tipo_persona='$tipo_persona', nombre='$nombre', tipo_documento='$tipo_documento', txtID_CLIENTE='$txtID_CLIENTE', direccion='$direccion', telefono='$telefono', email='$email', email2='$email2', txtRAZON_SOCIAL='$txtRAZON_SOCIAL', descuento='$descuento', descuentom='$descuentom', sector='$sector', lat='$lat', lon='$lon', codigo='$codigo', cci='$cci', vendedor='$vendedor', obs='$obs', pais='$pais', ciudad='$ciudad' ,creditolimite='0', credito='0' , edad='$edad', venta_pago='$venta_pago' WHERE idpersona='$idpersona'";
$id=ejecutarConsulta($sql);

header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

$jsondata = array();
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'Error no se pudo modificar.';

if($id!=''){
$jsondata['estado'] = '1';
$jsondata['id'] = $id;
$jsondata['mensaje'] = 'Cliente: '.$nombre.', modificado con exito';
$jsondata['doc'] =$tipo_documento;
$jsondata['nombre'] = $nombre;
}
		
echo json_encode($jsondata);
exit();	
	
	
	}
	
//Implementamos un método para editar registros
	public function editars($ids, $nombrel, $direccion)	{
$sql="UPDATE sucursal SET sucursal='$nombrel', direccion='$direccion'  WHERE id='$ids'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para eliminar categorías
	public function eliminar($idpersona)
	{
		$sql="DELETE FROM persona WHERE idpersona='$idpersona'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idpersona)
	{
		$sql="SELECT * FROM persona WHERE idpersona='$idpersona' ";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listarp()
	{
		$sql="SELECT * FROM persona WHERE tipo_persona='Proveedor' AND idempresa='$_COOKIE[id]' ";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros 
	public function listarc($tipodoc){
		
		if($tipodoc=='OTROS'){
		$sql="SELECT * FROM persona WHERE tipo_persona='Cliente' AND idempresa='$_COOKIE[id]' ";
		return ejecutarConsulta($sql);
			
		}else{
			$sql="SELECT * FROM persona WHERE tipo_persona='Cliente' AND idempresa='$_COOKIE[id]' AND tipo_documento='$tipodoc' ";
		return ejecutarConsulta($sql);
			
		}

	}
	
	public function listarpersona($tipodoc){

		$sql="SELECT * FROM persona WHERE tipo_persona='$tipodoc' AND idempresa='$_COOKIE[id]' ";
		return ejecutarConsulta($sql);

	}
	
	
	public function listard($idsector){
		
if($idsector=='null'||$idsector==''||$idsector=='undefined'){ $buscar=''; }else{ $buscar='AND sector="'.$idsector.'" '; }

		
		$sql="SELECT * FROM persona WHERE tipo_persona='Cliente' AND idempresa='$_COOKIE[id]' $buscar ORDER BY idpersona DESC ";
		return ejecutarConsulta($sql);
	}
	
	public function listaru(){
		$sql="SELECT * FROM usuario WHERE idempresa='$_COOKIE[id]' ORDER BY idusuario DESC ";
		return ejecutarConsulta($sql);
	}
	
	public function listarCliente($idpersona)
	{
		$sql="SELECT txtID_CLIENTE, txtRAZON_SOCIAL, direccion, idpersona FROM persona WHERE idpersona='$idpersona' ";
	}
	
	
	public function listarlo(){
		$sql="SELECT * FROM sucursal WHERE estado='1' AND nivel='0' AND id='$_COOKIE[idlocal]' AND idempresa='$_COOKIE[id]' ORDER BY id DESC ";
		return ejecutarConsulta($sql);
	}
	
	
}

?>