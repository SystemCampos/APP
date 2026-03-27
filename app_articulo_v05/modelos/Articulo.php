<?php 

Class Articulo{
	//Implementamos nuestro constructor
	public function __construct(){

	}

	//Implementamos un método para insertar registros
public function insertar($cat, $precio_mayor2, $precio_porcentaje2, $precio_porcentaje3, $precio_mayor3, $precio){
	
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$sqlartt="SELECT *FROM tmp_articulo WHERE id='$_COOKIE[idarticulo]' ";
$artt= ejecutarConsultaSimpleFila($sqlartt);
	
$codarticulo=$artt['codigo'];
	
if($codarticulo==''){
$codarticulo=$_COOKIE['idarticulo'];	
}

//echo 'precio:'.$precio;


/*	
$sql="INSERT INTO articulo (idempresa, idcategoria, marca, idlocal, codigo, txtDESCRIPCION_ARTICULO, stock, stockmin, stockmax, precio, precio_compra, mayor, precio_mayor, imagen, condicion, medida, exonerado_igv, comision, comisionm, comisionmp, resaltado, codigosunat, idproveedor, sanitario, linea, sublinea, subfamilia, ctacompras, ctaventas, bolsa, existencia, precio_porcentaje, moneda, principioactivo, preciooferta, canje, canjepuntos, canjecobro)
SELECT idempresa, idcategoria, marca, idlocal, '$codarticulo', txtDESCRIPCION_ARTICULO, stock, stockmin, stockmax, precio, precio_compra, mayor, precio_mayor, imagen, condicion, medida, exonerado_igv, comision, comisionm, comisionmp, resaltado, codigosunat, idproveedor, sanitario, linea, sublinea, subfamilia, ctacompras, ctaventas, bolsa, existencia, '0', 'PEN', '', '0', 'NO', '0', '0' FROM tmp_articulo WHERE id='$_COOKIE[idarticulo]' ";
$idventanew=ejecutarConsulta_retornarID($sql);
*/
	
$sql="INSERT INTO articulo (idempresa, idcategoria, marca, idlocal, codigo, txtDESCRIPCION_ARTICULO, stock, stockmin, stockmax, precio, precio_compra, mayor, precio_mayor, imagen, condicion, medida, exonerado_igv, comision, comisionm, comisionmp, resaltado, codigosunat, idproveedor, sanitario, linea, sublinea, subfamilia, ctacompras, ctaventas, bolsa, existencia, precio_porcentaje, moneda, principioactivo, preciooferta, canje, canjepuntos, canjecobro, nivel, precio_mayor2, precio_porcentaje2, precio_porcentaje3, precio_mayor3, idcatalogo_afectacion, maneja_lote, maneja_serie, maneja_garantia, garantia_tipo, garantia_meses)
SELECT idempresa, idcategoria, marca, idlocal, '$codarticulo', txtDESCRIPCION_ARTICULO, stock, stockmin, stockmax, '$precio', precio_compra, '999999', precio_mayor, imagen, condicion, medida, exonerado_igv, comision, comisionm, comisionmp, resaltado, codigosunat, idproveedor, sanitario, linea, sublinea, subfamilia, ctacompras, ctaventas, bolsa, existencia, '0', 'PEN', '', '0', 'NO', '0', '0', '$cat', '$precio_mayor2', '$precio_porcentaje2', '$precio_porcentaje3', '$precio_mayor3', idcatalogo_afectacion, maneja_lote, maneja_serie, maneja_garantia, garantia_tipo, garantia_meses FROM tmp_articulo WHERE id='$_COOKIE[idarticulo]' ";
$idventanew=ejecutarConsulta_retornarID($sql);
	
$sql="INSERT INTO articulo_images (id, idempresa, id_serv, tit, cont, imag)
SELECT NULL, idempresa, '$idventanew', tit, cont, imag FROM tmp_articuloimages WHERE id_serv='$_COOKIE[idarticulo]' ";
ejecutarConsulta($sql);
	
$sql="INSERT INTO articulo_unidad (idempresa, idlocal, beta, id, idproducto, nombre, medida, cti, ctimayor, precio, preciom, comision, comisionm)
SELECT '$_COOKIE[id]', '$_COOKIE[idlocal]', '$fa[tipo]', NULL, '$idventanew', nombre, medida, cti, ctimayor, precio, preciom, comision, comisionm FROM tmp_articulounidad WHERE idproducto='$_COOKIE[idarticulo]' ";
ejecutarConsulta($sql);
	
$sql="SELECT *FROM tmp_articulo WHERE id='$_COOKIE[idarticulo]' ";
$mostrarart= ejecutarConsultaSimpleFila($sql);

$tot=$mostrarart['stock']*$mostrarart['precio_compra'];
$subtotal=$tot/ 1.18;
$igv=round(($tot-$subtotal), 2);
	
$sql="SELECT *FROM ingreso WHERE serie_comprobante LIKE '%GP001%' AND idempresa='$_COOKIE[id]' ORDER BY idingreso DESC ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$num='00000001';	
	
if($mostrar['num_comprobante']!=''){
$num=$mostrar['num_comprobante']+1;
$num=str_pad($num, 8, "0", STR_PAD_LEFT);
}
	
$sql="INSERT INTO articulo_stock VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$idventanew', '0', '$_COOKIE[idlocal]', '$mostrarart[stock]', '$mostrarart[precio_compra]', '$mostrarart[stock]')";
ejecutarConsulta($sql);
	
$exonerado='0';
$inafecto='0';
if($mostrarart['exonerado_igv']=='1'){ $exonerado=$tot; }
if($mostrarart['exonerado_igv']=='3'){ $inafecto=$tot; }
	
$sql="INSERT INTO ingreso VALUES (NULL, '0', '$_COOKIE[id]', '$fa[tipo]', '1', '$_COOKIE[idusuario]', '$_COOKIE[idlocal]', '0', '17', 'GP001', '$num', 'EFECTIVO', '008', 'PEN', NOW(), NOW(), '$igv', '$tot', '$igv', '$tot', '$exonerado', '$inafecto', '0', '0', '0', '0', '0', '', '0')";
//return ejecutarConsulta($sql);
$iding=ejecutarConsulta_retornarID($sql);
	
$sqlart="SELECT *FROM articulo ORDER BY txtCOD_ARTICULO DESC ";
$mosart= ejecutarConsultaSimpleFila($sqlart);
if($mosart['exonerado_igv']=='0'){
$promedio=round($mostrarart['precio_compra']/ 1.18, 2);
}else{
$promedio=$mostrarart[precio_compra];	
}
$sql_detalle = "INSERT INTO detalle_ingreso VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$iding', '$_COOKIE[idlocal]', '$mosart[txtCOD_ARTICULO]', '$mostrarart[txtDESCRIPCION_ARTICULO]', '$mostrarart[stock]', '$subtotal', '$igv', '$tot', '$mostrarart[precio_compra]', '0.00', '$mostrarart[precio]', '$promedio', '$mostrarart[stock]', '$mostrarart[exonerado_igv]', '0000-00-00', NOW(), '0')";
ejecutarConsulta($sql_detalle);
	
$sqld= "DELETE FROM tmp_articuloimages WHERE id_serv='$_COOKIE[idarticulo]' ";
ejecutarConsulta($sqld);
	
$sqld= "DELETE FROM articulo_unidad WHERE idproducto='$_COOKIE[idarticulo]' ";
ejecutarConsulta($sqld);
	
$sqld= "DELETE FROM tmp_articulo WHERE id='$_COOKIE[idarticulo]' ";
ejecutarConsulta($sqld);

setcookie('idarticulo', $idventanew, time()-100, "/");

return $fa;
	
}
	
	//Implementamos un método para insertar registros
	public function insertarserie($id, $serie, $lote, $fechven, $idproveedora, $stockser){
		
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
if($idproveedora==''){ $idproveedora='0'; }
if($stockser==''){ $stockser='1'; }
if($fechven==''){ $fechven='0000-00-00'; }
		
$sql="INSERT INTO articulo_serie VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$_COOKIE[idlocal]', '1','$id', '0', '0', '0', '$serie','$lote', '$idproveedora', '$stockser', NOW(), '$fechven', '1')";
return ejecutarConsulta($sql);
	}
	
//Implementamos un método para insertar registros
public function insertarunidad($nombreu, $ctiunidad, $medida, $codartu, $preciou, $comisionu, $ctimayoru, $preciomu, $comisionmu){	
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$sql="INSERT INTO articulo_unidad VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$_COOKIE[idlocal]', '$codartu', '$nombreu','$medida', '$ctiunidad', '$ctimayoru', '$preciou', '$preciomu', '$comisionu', '$comisionmu')";
		return ejecutarConsulta($sql);
}
	

	//Implementamos un método para editar registros
public function editar($txtCOD_ARTICULO, $idcategoria, $idmarca, $codigo, $txtDESCRIPCION_ARTICULO, $stock, $stockmin, $stockmax, $precio, $precio_porcentaje, $ctimayor, $pmayor, $imagen, $medida, $exonerado, $comision, $comisionm, $comisionmp, $codigos, $idproveedor, $sanitario, $precioc, $linea, $sublinea, $subfamilia, $ctacompras, $ctaventas, $bolsa, $existencia, $idlocal, $moneda, $pactivo, $oferta, $canje, $canjepuntos, $canjecobro, $precio_mayor2, $precio_porcentaje2, $precio_porcentaje3, $precio_mayor3, $idcatalogo_afectacion, $maneja_lote, $maneja_serie, $maneja_garantia, $garantia_tipo, $garantia_meses){
	
$sqlas="SELECT *FROM articulo_stock WHERE idarticulo='$txtCOD_ARTICULO' AND idlocal='$_COOKIE[idlocal]' ";
$as= ejecutarConsultaSimpleFila($sqlas);
		
if($as){
$sql="UPDATE articulo_stock SET stock='$stock' WHERE idarticulo='$txtCOD_ARTICULO' AND idlocal='$_COOKIE[idlocal]' ";
ejecutarConsulta($sql);	
}else{
	
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);
	
$sql="INSERT INTO articulo_stock VALUES (NULL, '$_COOKIE[id]', '$fa[tipo]', '$txtCOD_ARTICULO', '0', '$_COOKIE[idlocal]', '$stock', '$precioc', '$stock')";
ejecutarConsulta($sql);	
}
		
$sql="UPDATE articulo SET idcategoria='$idcategoria', marca='$idmarca', codigo='$codigo', txtDESCRIPCION_ARTICULO='$txtDESCRIPCION_ARTICULO', precio='$precio', mayor='$ctimayor', precio_mayor='$pmayor', imagen='$imagen',  exonerado_igv='$exonerado', comision='$comision', comisionm='$comisionm', comisionmp='$comisionmp', codigosunat='$codigos', idproveedor='$idproveedor', sanitario='$sanitario', precio_compra='$precioc', linea='$linea', sublinea='$sublinea', subfamilia='$subfamilia', ctacompras='$ctacompras', ctaventas='$ctaventas', bolsa='$bolsa', stockmin='$stockmin', stockmax='$stockmax', existencia='$existencia', medida='$medida', idlocal='$idlocal', precio_porcentaje='$precio_porcentaje', moneda='$moneda', principioactivo='$pactivo', preciooferta='$oferta', canje='$canje', canjepuntos='$canjepuntos', canjecobro='$canjecobro', precio_mayor2='$precio_mayor2', precio_porcentaje2='$precio_porcentaje2', precio_porcentaje3='$precio_porcentaje3', precio_mayor3='$precio_mayor3', idcatalogo_afectacion='$idcatalogo_afectacion', maneja_lote='$maneja_lote', maneja_serie='$maneja_serie', maneja_garantia='$maneja_garantia', garantia_tipo='$garantia_tipo', garantia_meses='$garantia_meses'   WHERE txtCOD_ARTICULO='$txtCOD_ARTICULO' ";

return ejecutarConsulta($sql);
	
	}

//EDITAR TEMP
public function editartemp($idcategory, $idmarca, $codigo, $txtDESCRIPCION_ARTICULO, $stock, $stockmin, $stockmax, $precio, $ctimayor, $pmayor, $medida, $exonerado, $comision, $comisionm, $comisionmp, $codigos, $idproveedor, $sanitario, $precioc, $linea, $sublinea, $subfamilia, $ctacompras, $ctaventas, $bolsa, $idlocal, $existencia, $precio_mayor3, $idcatalogo_afectacion, $maneja_lote, $maneja_serie, $maneja_garantia, $garantia_tipo, $garantia_meses){
	
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa= ejecutarConsultaSimpleFila($sql3);

	
$sql="UPDATE tmp_articulo SET idcategoria='$idcategory', marca='$idmarca', codigo='$codigo', txtDESCRIPCION_ARTICULO='$txtDESCRIPCION_ARTICULO', precio='$precio', mayor='$ctimayor', precio_mayor='$pmayor', exonerado_igv='$exonerado', comision='$comision', comisionm='$comisionm', codigosunat='$codigos', idproveedor='$idproveedor', sanitario='$sanitario', precio_compra='$precioc', linea='$linea', sublinea='$sublinea', subfamilia='$subfamilia', medida='$medida', stock='$stock', stockmin='$stockmin', stockmax='$stockmax', ctaventas='$ctaventas', ctacompras='$ctacompras', bolsa='$bolsa', existencia='$existencia', idlocal='$idlocal', precio_mayor3='$precio_mayor3', idcatalogo_afectacion='$idcatalogo_afectacion', maneja_lote='$maneja_lote', maneja_serie='$maneja_serie', maneja_garantia='$maneja_garantia', garantia_tipo='$garantia_tipo', garantia_meses='$garantia_meses'  WHERE id='$_COOKIE[idarticulo]' ";
return ejecutarConsulta($sql);
		
	}
	

	//Implementamos un método para desactivar registros
	public function desactivar($txtCOD_ARTICULO)
	{
		$sql="UPDATE articulo SET condicion='0' WHERE txtCOD_ARTICULO='$txtCOD_ARTICULO'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar registros
	public function activar($txtCOD_ARTICULO)
	{
		$sql="UPDATE articulo SET condicion='1' WHERE txtCOD_ARTICULO='$txtCOD_ARTICULO'";
		return ejecutarConsulta($sql);
	}
	
	//Implementamos un método para activar registros
	public function resaltar($codigo, $estado)	{
		$sql="UPDATE articulo SET resaltado='$estado' WHERE txtCOD_ARTICULO='$codigo'";
		return ejecutarConsulta($sql);
	}
	
//Implementamos un método para activar registros
	public function eliminar($codigo)	{
		$sql="DELETE FROM articulo_unidad WHERE id='$codigo'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($txtCOD_ARTICULO, $tipoac){
		
if($tipoac=='2'){
		
		$sql="SELECT * FROM tmp_articulo  WHERE id='$_COOKIE[idarticulo]' ";
		return ejecutarConsultaSimpleFila($sql);
			
		}else{
			
		$sqla="SELECT * FROM  articulo_stock WHERE idarticulo ='$txtCOD_ARTICULO' AND idlocal='$_COOKIE[idlocal]' ";
		$stock=ejecutarConsultaSimpleFila($sqla);
	
if($stock){	
$sql="SELECT a.txtCOD_ARTICULO, a.comision, a.idcategoria, a.bolsa, a.marca, a.medida, a.codigo, a.txtDESCRIPCION_ARTICULO, a.precio, a.mayor, a.precio_mayor, a.exonerado_igv, a.imagen, a.condicion, a.resaltado, s.stock AS stock, a.stockmin, a.stockmax, a.comisionm, a.comisionmp, a.codigosunat, a.sanitario, a.idproveedor, a.linea, a.sublinea, a.subfamilia, a.precio_compra, a.ctaventas, a.ctacompras, a.existencia, a.idlocal, a.precio_porcentaje, a.moneda, /* PATCH V7 2026-01-31: devolver moneda en mostrar() */ a.principioactivo, a.preciooferta, a.canje, a.canjepuntos, a.canjecobro, a.precio_mayor2, a.precio_porcentaje3, a.precio_porcentaje2, a.precio_mayor3, a.idcatalogo_afectacion, a.maneja_lote, a.maneja_serie, a.maneja_garantia, a.garantia_tipo, a.garantia_meses FROM articulo a LEFT JOIN articulo_stock s ON a.txtCOD_ARTICULO=s.idarticulo  WHERE a.txtCOD_ARTICULO='$txtCOD_ARTICULO' AND s.idlocal='$_COOKIE[idlocal]' ";
}else{
$sql="SELECT a.txtCOD_ARTICULO, a.comision, a.idcategoria, a.bolsa, a.marca, a.medida, a.codigo, a.txtDESCRIPCION_ARTICULO, a.precio, a.mayor, a.precio_mayor, a.exonerado_igv, a.imagen, a.condicion, a.resaltado, s.stock AS stock, a.stockmin, a.stockmax, a.comisionm, a.comisionmp, a.codigosunat, a.sanitario, a.idproveedor, a.linea, a.sublinea, a.subfamilia, a.precio_compra, a.ctaventas, a.ctacompras, a.existencia, a.idlocal, a.precio_porcentaje, a.moneda, /* PATCH V7 2026-01-31: devolver moneda en mostrar() */ a.principioactivo, a.preciooferta, a.canje, a.canjepuntos, a.canjecobro, a.precio_mayor2, a.precio_porcentaje3, a.precio_porcentaje2, a.precio_mayor3, a.idcatalogo_afectacion, a.maneja_lote, a.maneja_serie, a.maneja_garantia, a.garantia_tipo, a.garantia_meses FROM articulo a LEFT JOIN articulo_stock s ON a.txtCOD_ARTICULO=s.idarticulo  WHERE a.txtCOD_ARTICULO='$txtCOD_ARTICULO' ";	
}
return ejecutarConsultaSimpleFila($sql);
	
		}

	}

	//Implementar un método para listar los registros
	public function listar(){
		
		$sql="SELECT a.txtCOD_ARTICULO,a.idcategoria, c.nombre as categoria, a.codigo, a.txtDESCRIPCION_ARTICULO, a.imagen, a.condicion, a.resaltado, a.precio, a.idproveedor FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria  WHERE a.idempresa='$_COOKIE[id]'  " ;
		return ejecutarConsulta($sql);
		
	}
	//Implementar un método para listar los registros
	public function listarserie($id)
	{
		$sql="SELECT *FROM articulo_serie WHERE cod_articulo='$id' ORDER BY estado DESC ";
		return ejecutarConsulta($sql);
	}
//Implementar un método para listar los registros
	public function listarunidad($id, $tipoac){
		
if($tipoac=='2'){	
$sql="SELECT *FROM tmp_articulounidad WHERE idproducto='$_COOKIE[idarticulo]' ORDER BY id DESC ";
}else{
$sql="SELECT *FROM articulo_unidad WHERE idproducto='$id' ORDER BY id DESC ";
}
		
		
		return ejecutarConsulta($sql);
		
		
	}
	//Implementar un método para listar los articulos
	public function listarA()
	{
		$sql="SELECT * FROM articulo WHERE condicion='1' AND idempresa='$_COOKIE[id]' ";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros activos
	public function listarActivos()	{
		
		$sql="SELECT a.txtCOD_ARTICULO, a.comision, a.idcategoria as categoria, a.codigo, a.txtDESCRIPCION_ARTICULO, s.stock, a.imagen, a.condicion, a.medida, a.resaltado, a.precio, a.exonerado_igv, a.precio_mayor, a.mayor, a.comisionm, a.comisionmp, a.precio_compra  FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN  articulo_stock s ON s.idarticulo=a.txtCOD_ARTICULO  WHERE a.condicion='1' AND a.idempresa='$_COOKIE[id]'   ";
		return ejecutarConsulta($sql);		
	}

	
//Implementar un método para listar los registros activos
	public function listarAventa1(){
		$sql="SELECT a.codigo, a.comision, a.txtDESCRIPCION_ARTICULO, s.stock, a.precio, a.imagen, a.condicion, a.resaltado, a.txtCOD_ARTICULO, a.idcategoria, a.marca, a.medida FROM articulo a INNER JOIN detalle_ingreso di ON a.txtCOD_ARTICULO=di.txtCOD_ARTICULO LEFT JOIN  articulo_stock s ON s.idarticulo=a.txtCOD_ARTICULO  AND s.idlocal='$_COOKIE[idlocal]'  WHERE  a.stock>0 ";
		return ejecutarConsulta($sql);		
	}
	
	
	//Implementar un método para listar los registros activos, su último precio y el stock (vamos a unir con el último registro de la tabla detalle_ingreso)
	public function listarActivosVenta()
	{
		$sql="SELECT a.txtCOD_ARTICULO,a.idcategoria,c.nombre as categoria,a.codigo,a.txtDESCRIPCION_ARTICULO,s.stock,(SELECT precio_venta FROM detalle_ingreso WHERE txtCOD_ARTICULO=a.txtCOD_ARTICULO order by iddetalle_ingreso desc limit 0,1) as txtPRECIO_ARTICULO,a.txtDESCRIPCION_ARTICULO,a.imagen,a.condicion FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN  articulo_stock s ON s.idarticulo=a.txtCOD_ARTICULO  AND s.idlocal='$_COOKIE[idlocal]' WHERE a.condicion='1'";
		return ejecutarConsulta($sql);		


	}
	public function listarum(){
		$sql="SELECT * FROM unidad_medida ORDER BY id ASC ";
		return ejecutarConsulta($sql);
	}
	
//Implementar un método para listar los registros
	public function listarcardex()
	{
		$sql="SELECT SUM(pinicial) as pinicial, SUM(cinicial) as cinicial, SUM(tinicial) AS tinicial, SUM(cfinal) AS cfinal, SUM(tfinal) AS tfinal, id, periodo  FROM cardex WHERE idempresa='$_COOKIE[id]' GROUP BY periodo " ;
		return ejecutarConsulta($sql);		
	}
	
	
}

?>
