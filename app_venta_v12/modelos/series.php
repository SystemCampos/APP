<?php

//Incluímos inicialmente la conexión a la base de datos
require "../config/conexion.php";

Class Series{

	//Implementamos un método para insertar registros
	public function insertar($serie, $numeroinicio, $tipo, $documento, $idlocal, $idcategoria){

		$sql="INSERT INTO series (idlocal, idempresa, serie, tipo, documento, numeroinicio, sector, estado)
		VALUES ('$idlocal', '$_COOKIE[id]', '$serie', '$tipo', '$documento', '$numeroinicio', '$idcategoria', '1')";
		return ejecutarConsulta($sql);
	}
	
	//Implementamos un método para insertar registros
	public function insertarm($moneda, $simbolo, $codigo){
		$sql="INSERT INTO moneda (codigo, idempresa, moneda, simbolo, estado)
		VALUES ('$codigo', '$_COOKIE[id]', '$moneda', '$simbolo', '1')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
public function editar($id, $sucursal, $direccion, $obs, $ubigeo){
$sql="UPDATE sucursal SET sucursal='$sucursal', direccion='$direccion', observacion='$obs', ubigeo='$ubigeo' WHERE id='$id'";
return ejecutarConsulta($sql);
}
	
//Implementamos un método para editar registros
public function editarserie($id, $serie, $numeroinicio, $tipo, $documento, $idcategoria){
$sql="UPDATE series SET serie='$serie', numeroinicio='$numeroinicio', sector='$idcategoria' WHERE id='$id'";
return ejecutarConsulta($sql);
}
	
//Implementamos un método para editar registros
public function editarm($id, $moneda, $simbolo, $codigo){
$sql="UPDATE moneda SET codigo='$codigo', moneda='$moneda', simbolo='$simbolo' WHERE id='$id'";
return ejecutarConsulta($sql);
}

	//Implementamos un método para desactivar categorías
	public function desactivar($id){
		$sql="UPDATE series SET estado='0' WHERE id='$id'";
		return ejecutarConsulta($sql);
	}
	
	public function desactivarm($id){
		$sql="UPDATE mesas SET estado='0' WHERE id='$id'";
		return ejecutarConsulta($sql);
	}
	
	public function desactivarc($id){
		$sql="UPDATE cajas SET estado='0' WHERE id='$id'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($id){
		$sql="UPDATE series SET estado='1' WHERE id='$id'";
		return ejecutarConsulta($sql);
	}
	
	public function activarm($id){
		$sql="UPDATE mesas SET estado='1' WHERE id='$id'";
		return ejecutarConsulta($sql);
	}
	
	public function activarc($id){
		$sql="UPDATE cajas SET estado='1' WHERE id='$id'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($id)
	{
		$sql="SELECT * FROM sucursal WHERE id='$id'";
		return ejecutarConsultaSimpleFila($sql);
	}
	
	public function mostrars($id)	{
		$idlocal=$_COOKIE["idlocal"];
		$sql="SELECT * FROM series WHERE id='$id' ";
		return ejecutarConsultaSimpleFila($sql);
	}


	//Implementar un método para listar los registros
	public function listar(){
		$idlocal=$_COOKIE["idlocal"];
		$sql="SELECT * FROM series WHERE idempresa='$_COOKIE[id]'  ORDER BY id DESC";
		return ejecutarConsulta($sql);		
	}
	//LISTAR MONEDA
	public function listarm(){
		$sql="SELECT * FROM moneda ORDER BY id DESC";
		return ejecutarConsulta($sql);		
	}
	public function listarmesa($ids){
		$sql="SELECT * FROM mesas WHERE idsucursal='$ids' ";
		return ejecutarConsulta($sql);		
	}
	public function listarcaja($ids){
		$sql="SELECT * FROM cajas WHERE idsucursal='$ids' ";
		return ejecutarConsulta($sql);		
	}
	public function listarsucursal(){
		$sql="SELECT * FROM sucursal WHERE estado='1'";
		return ejecutarConsulta($sql);		
	}
	//Implementar un método para listar los registros y mostrar en el select
	public function select()
	{
		$sql="SELECT * FROM categoria where condicion=1";
		return ejecutarConsulta($sql);		
	}
}

?>