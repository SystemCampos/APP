<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/conexion.php";

Class Usuario
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
public function insertar($nombre, $apellido, $tipo_documento, $txtID_CLIENTE, $lisencia, $direccion, $telefono, $email, $certificado){
		
$sql="INSERT INTO guia_chofer (nombre, apellido, idempresa, doctipo, docnumero, lisencia, direccion, telefono, correo, estado, fecha, certificado)
VALUES ('$nombre', '$apellido', '$_COOKIE[id]', '$tipo_documento', '$txtID_CLIENTE', '$lisencia', '$direccion', '$telefono', '$email', '1', NOW(), '$certificado')";
		return ejecutarConsulta($sql);

	}
	
	//Implementamos un método para insertar registros
public function insertarv($placa, $anio, $sector, $marca, $modelo, $equipo, $tarjetacirculacion, $autorizacionmtc){
		
$sql="INSERT INTO guia_vehiculo VALUES (NULL, '$_COOKIE[id]', '$placa', '$equipo', '$anio', '$sector', '$marca', '$modelo', '$tarjetacirculacion', '$autorizacionmtc', '1', NOW())";

//echo $sql;

		return ejecutarConsulta($sql);

	}
	
	//Implementamos un método para insertar registros
public function insertarET($nombre, $ruc, $direccion){
		
$sql="INSERT INTO guia_transportista VALUES (NULL, '$_COOKIE[id]', '$nombre', '$ruc', '$direccion', '1', NOW())";
return ejecutarConsulta($sql);

	}

	//Implementamos un método para editar registros
public function editar($idusuario, $nombre, $apellido, $tipo_documento, $txtID_CLIENTE, $lisencia, $direccion, $telefono, $email, $certificado){
		
$sql="UPDATE guia_chofer SET nombre='$nombre', apellido='$apellido', doctipo='$tipo_documento', docnumero='$txtID_CLIENTE', direccion='$direccion', telefono='$telefono', correo='$email', lisencia='$lisencia', certificado='$certificado' WHERE id='$idusuario'";
return ejecutarConsulta($sql);

	}
	//Implementamos un método para editar registros
public function editarv($idusuario, $placa, $anio, $sector, $marca, $modelo, $equipo, $tarjetacirculacion, $autorizacionmtc){
		
$sql="UPDATE guia_vehiculo SET placa='$placa', anio='$anio', sector='$sector', marca='$marca', 
modelo='$modelo', equipo='$equipo', tarjetacirculacion='$tarjetacirculacion', autorizacionmtc='$autorizacionmtc' WHERE id='$idusuario'";
return ejecutarConsulta($sql);

	}
	//Implementamos un método para editar registros
public function editarET($idusuario, $nombre, $ruc, $direccion){
		
$sql="UPDATE guia_transportista SET nombre='$nombre', ruc='$ruc', direccion='$direccion' WHERE id='$idusuario'";
return ejecutarConsulta($sql);

	}
	
//Implementamos un método para desactivar categorías
public function desactivar($idusuario)	{
		$sql="UPDATE usuario SET condicion='0' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
}
	
//DESACTIVAR CHOFER
public function desactivarc($idusuario)	{
		$sql="UPDATE guia_chofer SET estado='0' WHERE id='$idusuario'";
		return ejecutarConsulta($sql);
}

    public function activarc($idusuario)	{
        $sql="UPDATE guia_chofer SET estado='1' WHERE id='$idusuario'";
        return ejecutarConsulta($sql);
    }

    //DESACTIVAR VEHICULO
    public function desactivarv($idusuario)	{
        $sql="UPDATE guia_vehiculo SET estado='0' WHERE id='$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function activarv($idusuario)	{
        $sql="UPDATE guia_vehiculo SET estado='1' WHERE id='$idusuario'";
        return ejecutarConsulta($sql);
    }


    //DESACTIVAR TRANSPORTISTA
    public function desactivartr($idusuario)	{
        $sql="UPDATE guia_transportista SET estado='0' WHERE id='$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function activartr($idusuario)	{
        $sql="UPDATE guia_transportista SET estado='1' WHERE id='$idusuario'";
        return ejecutarConsulta($sql);
    }

	//Implementamos un método para activar categorías
	public function activar($idusuario)
	{
		$sql="UPDATE usuario SET condicion='1' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idusuario){
		$sql="SELECT * FROM guia_chofer WHERE id='$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}
	
	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrarvehiculo($idusuario){
		$sql="SELECT * FROM guia_vehiculo WHERE id='$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar(){
		$sql="SELECT * FROM guia_chofer WHERE idempresa='$_COOKIE[id]' ";
		return ejecutarConsulta($sql);		
	}

    public function listarch(){
        $sql="SELECT * FROM guia_chofer WHERE idempresa='$_COOKIE[id]' AND estado='1'";
        return ejecutarConsulta($sql);
    }

    public function listarch2(){
        $sql2="SELECT * FROM guia_chofer WHERE idempresa='$_COOKIE[id]' AND estado='1'";
        return ejecutarConsulta($sql2);
    }
	
	//Implementar un método para listar los registros
	public function listarguia(){
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
		
		$sql="SELECT * FROM guia_guia WHERE idempresa='$_COOKIE[id]' AND beta='$fa[tipo]' ";
		return ejecutarConsulta($sql);		
	}
	
public function listarv(){
		$sql="SELECT * FROM guia_vehiculo WHERE idempresa='$_COOKIE[id]' ";
		return ejecutarConsulta($sql);		
	}

    public function listarv2(){
        $sql="SELECT * FROM guia_vehiculo WHERE idempresa='$_COOKIE[id]' AND estado='1'  ";
        return ejecutarConsulta($sql);
    }
	
public function listarMOT(){
		$sql="SELECT * FROM guia_traslado ORDER BY id ASC";
		return ejecutarConsulta($sql);		
	}
	
public function listarET(){
		$sql="SELECT * FROM guia_transportista WHERE idempresa='$_COOKIE[id]' ";
		return ejecutarConsulta($sql);		
	}
	
	//Implementar un método para listar los permisos marcados
	public function listarmarcados($idusuario)
	{
		$sql="SELECT * FROM usuario_permiso WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Función para verificar el acceso al sistema
	public function verificar($login,$clave)
    {
    	$sql="SELECT idusuario,nombre,tipo_documento,txtID_CLIENTE,telefono,email,cargo,imagen,login FROM usuario WHERE login='$login' AND clave='$clave' AND condicion='1'"; 
    	return ejecutarConsulta($sql);  
    }
}

?>