<?php 
//Incluímos inicialmente la conexión a la base de datos
Class Venta{
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
		/*$sql="UPDATE venta SET estado='4' WHERE idventa='$idventa'";
		return ejecutarConsulta($sql);
		
		*/
		
$sw=true;
$datost = "SELECT *FROM detalle_venta WHERE idventa='$idventa' ";
$datost = ejecutarConsulta($datost);
//Inserto los articulos tantas veces como sea su cantidad en la venta.
while($datof=mysqli_fetch_array($datost)) {	

$sqlp="UPDATE articulo_stock SET stock=stock+$datof[txtCANTIDAD_ARTICULO] WHERE idarticulo='$datof[idproducto]' AND idlocal='$_COOKIE[idlocal]' ";
ejecutarConsulta($sqlp);
	
$sqls="UPDATE articulo_serie SET estado='1' WHERE idventa='$datof[iddetalle_venta]' ";
ejecutarConsulta($sqls);
	
}
		
$sql="UPDATE venta SET estado='4' WHERE idventa='$idventa'";
ejecutarConsulta($sql);
return $sw;	

}
	
	
public function anularrecibo($idventa){
		/*$sql="UPDATE venta SET estado='4' WHERE idventa='$idventa'";
		return ejecutarConsulta($sql);
		*/
$sw=true;
	
$sql3="SELECT *FROM venta WHERE idventa='$idventa' ";
$fa= ejecutarConsultaSimpleFila($sql3);	
	
if($fa['txtID_TIPO_DOCUMENTO']=='90'){
	
$datost = "SELECT *FROM detalle_venta WHERE idventa='$idventa' ";
$datost = ejecutarConsulta($datost);
		
//Inserto los articulos tantas veces como sea su cantidad en la venta.
while($datof=mysqli_fetch_array($datost)) {	

$sqlp="UPDATE articulo_stock SET stock=stock+$datof[txtCANTIDAD_ARTICULO] WHERE idarticulo='$datof[idproducto]' AND idlocal='$_COOKIE[idlocal]' ";
ejecutarConsulta($sqlp);
//echo $idingreso.'<br>';
$sqls="UPDATE articulo_serie SET estado='1' WHERE idventa='$datof[iddetalle_venta]' ";
ejecutarConsulta($sqls);
//echo $datof['iddetalle_ingreso'].'<br>';
	
}
	
}	

$sql="UPDATE venta SET estado='6' WHERE idventa='$idventa'";
ejecutarConsulta($sql);
	
return $sw;	

}
	


	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idventa)
	{
$sql="SELECT v.idventa, v.txtOBSERVACION, DATE(v.txtFECHA_DOCUMENTO) as fecha, v.txtID_CLIENTE, p.nombre as cliente, u.idusuario, u.nombre as usuario, v.txtID_TIPO_DOCUMENTO, v.txtSERIE, v.txtNUMERO, v.txtTOTAL, v.txtIGV, v.estado, v.guia, v.presupuesto, v.referencia, v.doc_relaciona, v.txtID_MONEDA, v.tipo_pago, v.medio_pago, v.orden, v.sector, v.iddetraccion, v.detraccion, dt.codigo AS codidetracciones, dt.porcentaje, v.retencion, v.percepcion FROM venta v INNER JOIN persona p ON v.txtID_CLIENTE=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario LEFT JOIN detracciones dt ON dt.id=v.iddetraccion WHERE v.idventa='$idventa' ";
		return ejecutarConsultaSimpleFila($sql);
	}
	
	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrarorden($idventa)
	{
		$sql="SELECT * FROM venta_orden WHERE id='$idventa'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarDetalle($idventa)
	{
		$sql="SELECT * FROM detalle_venta  where idventa='$idventa' ORDER BY iddetalle_venta DESC ";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
public function listar($fecha_inicio,$fecha_fin, $ttdoc, $nivel, $mes){
	
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

if($nivel=='0'){
$tipodoc="(txtID_TIPO_DOCUMENTO='03' OR txtID_TIPO_DOCUMENTO='01' OR txtID_TIPO_DOCUMENTO='90')";
}else{
$tipodoc="(txtID_TIPO_DOCUMENTO='07' OR txtID_TIPO_DOCUMENTO='08')";	
}
	
if(isset($_COOKIE['facturacione'])){
if($_COOKIE['facturacione']==1){
$local="";
}}else{
$local=" AND idlocal='".$_COOKIE['idlocal']."'";	
}
		
if($fecha_inicio==''){ $buscar=""; }else{ $buscar=" AND DATE(txtFECHA_DOCUMENTO)>='$fecha_inicio' "; }
if($fecha_fin==''){ $buscaru=""; }else{ $buscaru=" AND DATE(txtFECHA_DOCUMENTO)<='$fecha_fin' "; }

    $sql="SELECT *FROM venta WHERE $tipodoc $local AND pedido != '1' $buscar $buscaru AND beta='$fa[tipo]' AND idlocal='$_COOKIE[idlocal]' AND idempresa='$_COOKIE[id]' ORDER by txtFECHA_DOCUMENTO desc";
return ejecutarConsulta($sql);
		
}

	//Implementar un método para listar los registros
public function nnf($fecha_inicio,$fecha_fin,$ttdoc, $idusuario){

$tipodoc="AND (txtID_TIPO_DOCUMENTO='03' OR txtID_TIPO_DOCUMENTO='01' OR txtID_TIPO_DOCUMENTO='90')";
	
if(isset($_COOKIE['facturacione'])){
if($_COOKIE['facturacione']==1){
$local=" AND idlocal='".$_COOKIE['idlocal']."' ";
}}

if($idusuario==''){ $busuario=''; }else{ $busuario=" AND idusuario='".$idusuario."' "; }
	
if($fecha_inicio==''){ $fechai=""; }else{ $fechai=" AND DATE(txtFECHA_DOCUMENTO)='$fecha_inicio' "; }
		
		
$sql="SELECT *FROM venta WHERE pedido='$ttdoc' $local $tipodoc $fechai $busuario ORDER by txtFECHA_DOCUMENTO desc";
return ejecutarConsulta($sql);
		
}	
	
public function listar2($fecha_inicio,$fecha_fin,$ttdoc, $mes){
	
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

$tipodoc="(txtID_TIPO_DOCUMENTO='03' OR txtID_TIPO_DOCUMENTO='01' OR txtID_TIPO_DOCUMENTO='90') AND (estado='0' OR estado='1' OR estado='2')";
		
if($_COOKIE['facturacione']==1){ 
$local="";	
}else{
$local=" AND idlocal='".$_COOKIE['idlocal']."'";	
}

if($mes==''){ 
	$mesf=''; 
}else{ 

$anio=date('Y');
$fecha=$anio.'-'.$mes;
$mesf=" AND txtFECHA_DOCUMENTO LIKE '%$fecha%' ";
$tipodoc="(txtID_TIPO_DOCUMENTO='03' OR txtID_TIPO_DOCUMENTO='01')";
}
		
if($fecha_inicio==''){ $buscar=""; }else{ $buscar=" AND DATE(txtFECHA_DOCUMENTO)>='$fecha_inicio' "; }
if($fecha_fin==''){ $buscaru=""; }else{ $buscaru=" AND DATE(txtFECHA_DOCUMENTO)<='$fecha_fin' "; }			
		


$sql="SELECT *FROM venta WHERE $tipodoc $mesf $local AND pedido='$ttdoc' $buscar $buscaru AND beta='$fa[tipo]' ORDER by txtFECHA_DOCUMENTO desc";
return ejecutarConsulta($sql);
	
		
}
	
	
public function listarpedido($ttdoc){
		
if($_COOKIE['facturacione']==1){ 
$local="";	

}else{

$local=" AND idlocal='".$_COOKIE['idlocal']."' ";	
}
		
$sql="SELECT *FROM venta WHERE txtID_TIPO_DOCUMENTO='$ttdoc' AND idempresa='$_COOKIE[id]' ORDER by txtFECHA_DOCUMENTO desc";
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
	
public function listarorden($nivel){
		
$sql="SELECT *FROM venta_orden WHERE idlocal='$_COOKIE[idlocal]' AND nivel='$nivel' ORDER by id desc";
return ejecutarConsulta($sql);
		
}

//Implementar un método para listar los registros
public function listarordenf($fecha_inicio,$fecha_fin,$ttdoc, $idusuario){

$tipodoc="AND (txtID_TIPO_DOCUMENTO='03' OR txtID_TIPO_DOCUMENTO='01' OR txtID_TIPO_DOCUMENTO='90') AND (estado='0' OR estado='1' OR estado='2') ";
	
if(isset($_COOKIE['facturacione'])){
if($_COOKIE['facturacione']==1){
$local=" AND idlocal='".$_COOKIE['idlocal']."' ";
}}

if($idusuario==''){ $busuario=''; }else{ $busuario=" AND idusuario='".$idusuario."' "; }
	
if($fecha_inicio==''){ $fechai=""; }else{ $fechai=" AND DATE(txtFECHA_DOCUMENTO)='$fecha_inicio' "; }
		
		
$sql="SELECT *FROM venta WHERE pedido='$ttdoc' AND idempresa='$_COOKIE[id]' $local $tipodoc $fechai $busuario ORDER by txtFECHA_DOCUMENTO desc";
return ejecutarConsulta($sql);
		
}
	
	
public function listarordenguia(){
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$sql="SELECT *FROM ingreso WHERE idlocal='$_COOKIE[idlocal]' AND beta='$fa[tipo]' AND estado='0' AND (nivel='2' OR nivel='3') ORDER by idingreso desc";
return ejecutarConsulta($sql);
		
}
	
public function listarordenguia2(){
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$sql="SELECT *FROM venta_orden WHERE idlocal='$_COOKIE[idlocal]' AND beta='$fa[tipo]' AND estado='0' ORDER by id desc";
return ejecutarConsulta($sql);
		
}
	
//Implementar un método para listar los registros activos, su último precio y el stock (vamos a unir con el último registro de la tabla detalle_ingreso)
public function listarActivosVenta(){
		$sql="SELECT a.txtCOD_ARTICULO,a.idcategoria,c.nombre as categoria,a.codigo,a.txtDESCRIPCION_ARTICULO,s.stock,(SELECT precio_venta FROM detalle_ingreso WHERE txtCOD_ARTICULO=a.txtCOD_ARTICULO order by iddetalle_ingreso desc limit 0,1) as txtPRECIO_ARTICULO,a.txtDESCRIPCION_ARTICULO,a.imagen,a.condicion FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN  articulo_stock s ON s.idarticulo=a.txtCOD_ARTICULO  AND s.idlocal='$_COOKIE[idlocal]' WHERE a.condicion='1'";
		return ejecutarConsulta($sql);		

}
	
//Implementar un método para listar los registros activos
	public function listarActivos()	{
		
if(isset($_COOKIE["idlocal"])){ $idlocal=$_COOKIE["idlocal"]; }else{ $idlocal='1'; }
		
		$sql="SELECT a.txtCOD_ARTICULO, a.comision, a.idcategoria as categoria, a.codigo, a.txtDESCRIPCION_ARTICULO, s.stock, a.imagen, a.condicion, a.medida, a.resaltado, a.precio, a.exonerado_igv, a.precio_mayor, a.mayor, a.comisionm, a.comisionmp  FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN  articulo_stock s ON s.idarticulo=a.txtCOD_ARTICULO  AND s.idlocal='$idlocal'  WHERE a.condicion='1' ";
		return ejecutarConsulta($sql);		
	}
	
	
}
?>