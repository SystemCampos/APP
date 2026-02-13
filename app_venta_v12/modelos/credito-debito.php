<?php 
//Incluímos inicialmente la conexión a la base de datos
if(isset($rutat)){ require "../../config/conexion.php"; }else{ require "../config/conexion.php"; }

Class Venta
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($txtID_CLIENTE,$idusuario,$txtID_TIPO_DOCUMENTO,$txtSERIE,$txtNUMERO,$txtFECHA_DOCUMENTO,$txtTIPO_DOCUMENTO_CLIENTE,$txtOBSERVACION,$txtSUB_TOTAL,$txtIGV,$txtTOTAL, $txtID_MONEDA,$txtID_TIPO_DOCUMENTO_MODIFICA,$txtNRO_DOC_MODIFICA,$txtID_MOTIVO,$txtCOD_ARTICULO,$txtCANTIDAD_ARTICULO,$txtPRECIO_ARTICULO)
	{
		$sql="INSERT INTO venta (txtID_CLIENTE,idusuario,txtID_TIPO_DOCUMENTO,txtSERIE,txtNUMERO,txtFECHA_DOCUMENTO,txtTIPO_DOCUMENTO_CLIENTE,txtOBSERVACION,txtSUB_TOTAL,txtIGV,txtTOTAL, txtID_MONEDA,txtID_TIPO_DOCUMENTO_MODIFICA,txtNRO_DOC_MODIFICA,txtID_MOTIVO, txtCOD_ARTICULO,txtCANTIDAD_ARTICULO,txtPRECIO_ARTICULO,estado)
		VALUES ('$txtID_CLIENTE','$idusuario','$txtID_TIPO_DOCUMENTO','$txtSERIE','$txtNUMERO','$txtFECHA_DOCUMENTO','$txtTIPO_DOCUMENTO_CLIENTE','$txtOBSERVACION','$txtSUB_TOTAL','$txtIGV','$txtTOTAL','$txtID_MONEDA','$txtID_TIPO_DOCUMENTO_MODIFICA','$txtNRO_DOC_MODIFICA','$txtID_MOTIVO', '$txtCOD_ARTICULO','$txtCANTIDAD_ARTICULO','$txtPRECIO_ARTICULO','Aceptado')";

		return ejecutarConsulta($sql);
		
	}

	
	//Implementamos un método para anular la venta
	public function anular($idventa){
		$sql="UPDATE notacredito SET estado='4' WHERE id='$idventa'";
		return ejecutarConsulta($sql);
	}


	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idventa)
	{
		$sql="SELECT v.idventa,DATE(v.txtFECHA_DOCUMENTO) as fecha,v.txtID_CLIENTE,p.nombre as cliente,u.idusuario,u.nombre as usuario,v.txtID_TIPO_DOCUMENTO,v.txtSERIE,v.txtNUMERO,v.txtTOTAL,v.txtIGV,v.txtCOD_ARTICULO,v.txtCANTIDAD_ARTICULO,v.txtPRECIO_ARTICULO,v.estado FROM venta v INNER JOIN persona p ON v.txtID_CLIENTE=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idventa='$idventa'";
	



		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarDetalle($idventa)
	{
		$sql="SELECT dv.idventa,dv.txtCOD_ARTICULO,a.txtDESCRIPCION_ARTICULO,dv.txtCANTIDAD_ARTICULO,dv.txtPRECIO_ARTICULO,(dv.txtCANTIDAD_ARTICULO*dv.txtPRECIO_ARTICULO) as txtSUB_TOTAL FROM detalle_venta dv inner join articulo a on dv.txtCOD_ARTICULO=a.txtCOD_ARTICULO where dv.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listar(){
$sql="SELECT *FROM venta WHERE (txtID_TIPO_DOCUMENTO='07' OR txtID_TIPO_DOCUMENTO='08') AND idempresa='$_COOKIE[id]' ORDER by idventa desc";
		return ejecutarConsulta($sql);
	}
	
public function listarventa($tipo){
	
if($tipo=='0'){ 
	$buscar=" AND (txtID_TIPO_DOCUMENTO='01' OR txtID_TIPO_DOCUMENTO='03') "; 
}else{
	$buscar="AND txtID_TIPO_DOCUMENTO='92' ";
}	
$sql="SELECT *FROM venta WHERE (estado='0' OR estado='1' OR estado='2') $buscar AND idempresa='$_COOKIE[id]' ORDER by txtFECHA_DOCUMENTO desc";
return ejecutarConsulta($sql);

}
	
public function listarventa2(){
$sql="SELECT *FROM venta WHERE (estado='0' OR estado='1' OR estado='2') AND idempresa='$_COOKIE[id]' ORDER by txtFECHA_DOCUMENTO desc";
		return ejecutarConsulta($sql);
}	
	
	
//LISTAMOS DETALLE DE VENTA
	public function listardet($idventa){
$sql="SELECT *FROM detalle_venta WHERE idventa='$idventa' ORDER by iddetalle_venta desc";
		return ejecutarConsulta($sql);
	}

	public function ventacabecera($idventa){
		$sql="SELECT v.idventa,v.txtID_CLIENTE,p.nombre as cliente,p.direccion,p.tipo_documento,p.num_documento,p.email,p.telefono,v.idusuario,u.nombre as usuario,v.txtID_TIPO_DOCUMENTO,v.txtSERIE,v.txtNUMERO,date(v.txtFECHA_DOCUMENTO) as fecha,v.txtIGV,v.txtTOTAL FROM venta v INNER JOIN persona p ON v.txtID_CLIENTE=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	public function ventadetalle($idventa){
		$sql="SELECT a.nombre as articulo,a.txtCOD_ARTICULO,d.txtCANTIDAD_ARTICULO,d.txtPRECIO_ARTICULO,(d.txtCANTIDAD_ARTICULO*d.txtPRECIO_ARTICULO) as txtSUB_TOTAL FROM detalle_venta d INNER JOIN articulo a ON d.txtCOD_ARTICULO=a.txtCOD_ARTICULO WHERE d.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}
	
	
//Implementar un método para mostrar los datos de un registro a modificar
	public function detfactura($fecha)	{
		
$tipodoc="AND (v.txtID_TIPO_DOCUMENTO='03' OR v.txtID_TIPO_DOCUMENTO='01' OR v.txtID_TIPO_DOCUMENTO='90')";
$local=" AND v.idlocal='".$_COOKIE['idlocal']."'";
		
		$sql="SELECT d.nombreproducto, v.txtSERIE, v.txtNUMERO, v.txtID_TIPO_DOCUMENTO, d.placa, d.precio, d.idproducto, d.iddetalle_venta, d.codigoproducto, d.txtCANTIDAD_ARTICULO, d.subtotal, d.igv, d.importe, d.unidadmedida  FROM detalle_venta d LEFT JOIN venta  v ON v.idventa=d.idventa WHERE d.fecha='$fecha' $tipodoc $local  ";
		return ejecutarConsulta($sql);	
	}
	
public function detorden($id)	{
		
		$sql="SELECT * FROM detalle_ingreso WHERE idingreso='$id' ";
		return ejecutarConsulta($sql);	
	}
	
public function detorden2($id)	{
		
		$sql="SELECT * FROM venta_opdetallet WHERE idpedido='$id' ";
		return ejecutarConsulta($sql);	
	}	
	
}
?>