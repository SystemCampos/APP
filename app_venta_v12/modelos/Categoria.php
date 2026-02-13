<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/conexion.php";
date_default_timezone_set('America/Lima');

Class Categoria
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre,$descripcion, $nivel, $familia, $ctaventas){
		$sql="INSERT INTO categoria VALUES (NULL, '$_COOKIE[id]', '$nivel', '$familia', '$nombre', '$descripcion', '$ctaventas', '1')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idcategoria,$nombre,$descripcion, $nivel, $ctaventas)
	{
		$sql="UPDATE categoria SET nivel='$nivel', nombre='$nombre', descripcion='$descripcion', ctaventas='$ctaventas' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

//Implementamos un método para insertar registros
	public function insertart($cambio){
		$idusuario=$_SESSION["idusuario"];
		$sql="INSERT INTO tipo_cambio (id, idusuario, cambio, fecha)
		VALUES (NULL, '$idusuario', '$cambio', NOW())";
		return ejecutarConsulta($sql);
	}
	
	//Implementamos un método para desactivar categorías
	public function desactivar($idcategoria)
	{
		$sql="UPDATE categoria SET condicion='0' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idcategoria)
	{
		$sql="UPDATE categoria SET condicion='1' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idcategoria)
	{
		$sql="SELECT * FROM categoria WHERE idcategoria='$idcategoria'";
		return ejecutarConsultaSimpleFila($sql);
	}

	
//Implementamos un método para desactivar categorías
    public function desactivarcaja($id){

        /*        $sqlu="SELECT *FROM usuario WHERE idusuario='$_COOKIE[idusuario]' ";
                $mostraru= ejecutarConsultaSimpleFila($sqlu);*/

        $sqlc="SELECT *FROM cajas WHERE id_usuario='$_COOKIE[idusuario]'  ORDER BY id DESC ";
        $mostrarcc= ejecutarConsultaSimpleFila($sqlc);

        $fecha=date('Y-m-d').' '.date('H:i:s');
        $sql="UPDATE cajas SET estado='0', fecha_cierre='$fecha' WHERE id_usuario='$id' AND id='$mostrarcc[id]'";
        return ejecutarConsulta($sql);
    }
	
//Implementamos un método para insertar registros
/*	public function activarcaja($id, $saldoi){
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

$sql="INSERT INTO cajas VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$_COOKIE[idlocal]', '1', '$_COOKIE[idusuario]', '$saldoi', '0.00', '0.00', NOW(), '0000-00-00 00:00:00')";
		return ejecutarConsulta($sql);
	}*/
	
	//Implementar un método para listar los registros
	public function listar($id){
		
		$sql="SELECT * FROM categoria WHERE nivel='$id' AND idempresa='$_COOKIE[id]' ORDER BY idcategoria ASC ";
		return ejecutarConsulta($sql);		
	}
	//Implementar un método para listar los registros y mostrar en el select
	public function select($nivel)
	{
		$sql="SELECT * FROM categoria where condicion=1 AND nivel='$nivel' AND idempresa='$_COOKIE[id]' ORDER BY idcategoria ASC ";
		return ejecutarConsulta($sql);		
	}
}

?>