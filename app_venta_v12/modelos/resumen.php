<?php 
//Incluímos inicialmente la conexión a la base de datos

if(isset($rutat)){ require "../../config/conexion.php"; }else{ require "../config/conexion.php"; }

Class Resumen
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($idproveedor,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_compra,$txtCOD_ARTICULO,$txtCANTIDAD_ARTICULO,$precio_compra,$precio_venta){
$sql="INSERT INTO ingreso (idproveedor, idusuario,tipo_comprobante,serie_comprobante,num_comprobante,fecha_hora,impuesto,total_compra,estado)
		VALUES ('$idproveedor','$idusuario','$tipo_comprobante','$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto','$total_compra','Aceptado')";
		//return ejecutarConsulta($sql);
		$idingresonew=ejecutarConsulta_retornarID($sql);

		$num_elementos=0;
		$sw=true;

		while ($num_elementos < count($txtCOD_ARTICULO))
		{
			$sql_detalle = "INSERT INTO detalle_ingreso(idingreso,txtCOD_ARTICULO,txtCANTIDAD_ARTICULO,precio_compra,precio_venta) VALUES ('$idingresonew','$txtCOD_ARTICULO[$num_elementos]','$txtCANTIDAD_ARTICULO[$num_elementos]','$precio_compra[$num_elementos]','$precio_venta[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;
			$num_elementos=$num_elementos + 1;
		}

		return $sw;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function enviarresumen($fecha, $estado, $tipodoc)	{
		$sql="SELECT *FROM venta WHERE (estado='$estado' OR estado='1' OR estado='5') AND txtFECHA_DOCUMENTO LIKE '%$fecha%' AND (txtID_TIPO_DOCUMENTO='$tipodoc' OR docmodifica_tipo='$tipodoc') AND idempresa='$_COOKIE[id]' ";
		return ejecutarConsulta($sql);
	}
	
	//Implementar un método para mostrar los datos de un registro a modificar
	public function detfactura($id)	{
		$sql="SELECT *FROM detalle_venta WHERE idventa='$id' ";
		return ejecutarConsulta($sql);	
	}
	
	
	
	//Implementar un método para mostrar los datos de un registro a modificar
	public function detfacturaf($id)	{
		
$sql="SELECT SUM(cti) AS cti, SUM(importe)  AS total, nombreproducto, serie, unidadmedida, codigoproducto, idpresentacion  FROM venta_opdetallet WHERE idpedido='$id'  GROUP BY unidadmedida, idproducto ";
return ejecutarConsulta($sql);	
	}
	
//Implementar un método para mostrar los datos de un registro a modificar
	public function detfacturaf2($fecha_inicio,$fecha_fin,$ttdoc, $mes)	{
		
$tipodoc="AND (v.txtID_TIPO_DOCUMENTO='03' OR v.txtID_TIPO_DOCUMENTO='01' OR v.txtID_TIPO_DOCUMENTO='90') AND (v.estado='0' OR v.estado='1' OR v.estado='2')";
$local=" AND v.idlocal='".$_SESSION['idlocal']."'";
		
if($fecha_inicio==''){ $buscar=""; }else{ $buscar=" AND DATE(v.txtFECHA_DOCUMENTO)>='$fecha_inicio' "; }
if($fecha_fin==''){ $buscaru=""; }else{ $buscaru=" AND DATE(v.txtFECHA_DOCUMENTO)<='$fecha_fin' "; }	
		
		
		$sql="SELECT SUM(d.txtCANTIDAD_ARTICULO) AS cti, SUM(d.importe)  AS total, d.nombreproducto, v.txtSERIE, v.txtNUMERO, v.txtID_TIPO_DOCUMENTO, d.placa, d.precio  FROM detalle_venta d INNER JOIN venta v ON v.idventa=d.idventa WHERE pedido='$ttdoc' $tipodoc $local $buscar $buscaru  ";
		return ejecutarConsulta($sql);	
	}
	
	
	public function detnota($id)	{
		$sql="SELECT *FROM nota_detalle WHERE idventa='$id' ";
		return ejecutarConsulta($sql);	
	}
	
	public function guardarresumen($tipo, $codigo, $serie, $numero, $estado, $hash, $ticket, $fechadoc, $fecha)	{
$sql="INSERT INTO resumen (tipo, idempresa, codigo, serie, numero, estado, hash, hash_cdr, mensaje, ticket, fecha_documento, fecha, iddocumento)
VALUES ('$tipo', '$_COOKIE[id]', '$codigo', '$serie', '$numero', '$estado', '$hash', '', '', '$ticket', '$fechadoc', '$fecha', '0')";
		return ejecutarConsulta($sql);
	}

public function listarDetalle($idingreso){
$sql="SELECT di.idingreso,di.txtCOD_ARTICULO,a.nombre,di.txtCANTIDAD_ARTICULO,di.precio_compra,di.precio_venta FROM detalle_ingreso di inner join articulo a on di.txtCOD_ARTICULO=a.txtCOD_ARTICULO where di.idingreso='$idingreso'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listar($nivel)
	{
		$sql="SELECT *FROM resumen WHERE tipo='$nivel' AND idempresa='$_COOKIE[id]' ORDER BY id DESC";
		return ejecutarConsulta($sql);		
	}
	
}

?>