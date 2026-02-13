<?php

define("DB_HOSTP","localhost"); 
define("DB_NAMEP", "tmperu_db_app");
define("DB_USERNAMEP", "tmperu_user_app");
define("DB_PASSWORDP", "KFEayAEdb3DG72mG");
define("DB_ENCODEP","utf8");
define("RUTAP","https://app.tmperu.net.pe/sursagas"); 


$conexionp = new mysqli(DB_HOSTP,DB_USERNAMEP,DB_PASSWORDP,DB_NAMEP);

mysqli_query( $conexionp, 'SET NAMES "'.DB_ENCODEP.'"');

if (mysqli_connect_errno()){
	printf("Falló conexión a la base de datos: %s\n",mysqli_connect_error());
	exit();
}

if (!function_exists('ejecutarConsultap')){
	function ejecutarConsultap($sql){
		global $conexionp;
		$query = $conexionp->query($sql);
		return $query;
	}

	function ejecutarConsultaSimpleFilap($sql){
		global $conexionp;
		$query = $conexionp->query($sql);		
		$row = $query->fetch_assoc();
		return $row;
	}

	function ejecutarConsulta_retornarIDp($sql){
		global $conexionp;
		$query = $conexionp->query($sql);		
		return $conexionp->insert_id;			
	}

	function limpiarCadenap($str){
		global $conexionp;
		$str = mysqli_real_escape_string($conexionp,trim($str));
		return htmlspecialchars($str);
	}
}



?>
