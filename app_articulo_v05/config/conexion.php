<?php 

date_default_timezone_set('America/Lima');
require_once "global.php";

$conexion = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);
$conexion->set_charset('utf8');
mysqli_query( $conexion, 'SET NAMES "'.DB_ENCODE.'"');

//Si tenemos un posible error en la conexión lo mostramos
if (mysqli_connect_errno())
{
	printf("Falló conexión a la base de datos: %s\n",mysqli_connect_error());
	exit();
}

if (!function_exists('ejecutarConsulta'))
{
function ejecutarConsulta($sql){
  global $conexion;
  $rspta = $conexion->query($sql);
  if(!$rspta){
    error_log("SQL ERROR: ".$conexion->error." | SQL: ".$sql);
  }
  return $rspta;
}

	function ejecutarConsultaSimpleFila($sql){
  global $conexion;

  $query = $conexion->query($sql);
  if ($query === false) {
    // Lanza el error real de MySQL (así ya no te sale "fetch_assoc() on bool")
    throw new Exception("SQL ERROR: ".$conexion->error." | SQL: ".$sql);
  }

  $row = $query->fetch_assoc();
  return $row ? $row : [];
}


	function ejecutarConsulta_retornarID($sql)
	{
		global $conexion;
		$query = $conexion->query($sql);		
		return $conexion->insert_id;			
	}

	function limpiarCadena($str)
	{
		global $conexion;
		$str = mysqli_real_escape_string($conexion,trim($str));
        //para cambiar & comillas apostrofes
        //return htmlspecialchars($str, ENT_COMPAT);
        return $str;
	}
}
?>