<?php
require'config/conexion.php';

?>
<!DOCTYPE html>
<html lang="es">
<head><?php require'includes/header.php'; ?>
<style>
#select2-medida-container{
  min-width: 180px;
}
#existencia{
  min-width: 180px;
}
</style>
</head>
<body class="page-body  page-fade" data-url="http://neon.dev">
	
<?php require'includes/sup.php'; ?>

		
<?php
if ($_COOKIE['almacen']==1){
	
$bloqueo='';
$cat=$_GET['cat'];
$t=$_GET['t'];	
	
if($_COOKIE['configuracion']=='0'){ $bloqueo='readonly'; }
	
?>
	
	
	
<div class="row">
		
		
			<div class="col-sm-12">
		
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="panel-title"><?=$t?></div>
		
						<div class="panel-options">
					    
<?php
if ($_COOKIE['gerencia']==1){ 
?>
<button type="button" class="btn btn-xs" style="background:#000;color:#fff;border-color:#000;" onclick="stockkardex()">
  <i class="fa fa-list"></i> REGULARIZAR STOCK
</button>
<?php
}
?>
<button type="button" class="btn btn-xs" style="background:#2ca02c;color:#fff;border-color:#2ca02c;" onclick="agregararticulo('2')">
  <i class="fa fa-plus-circle"></i> AGREGAR
</button>
						
	
						</div>
					</div>
		
<div class="table-responsive" id="listadoregistros">	
		
<div id="barraprogreso" style="display: none;" >				
	 
<div class="col-md-12">	
<h5 id="mensaje" >Active Progressbars</h5>
</div>
	
<div class="col-md-12">
<div class="progress progress-striped active">
<div id="barcalcula" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width:1%"><span class="sr-only">40% Complete (success)</span>
									</div>
</div>
</div>
</div>

<div class="table-responsive" id="wrap_tbllistado_articulo">
  <table id="tbllistado_articulo" class="table table-bordered display nowrap" width="100%" cellspacing="0">

<thead>
  <tr>
<th></th>
<th>Opc</th>
<th>ID</th>
<th>Código</th>
<th>Grupo</th>
<th>Presentación</th>
<th>Descripción</th>
<th>Stock</th>
<th>M</th>
<th>Precio</th>
<th>P.Compra</th>
<th>Lab</th>
<th>Lote</th>
<th>F/Ven</th>
<th>Estado</th>
  </tr>
</thead>
		
						<tbody></tbody>
					</table>
					</div>
	
</div>
<!-- LA ID DEL PRODUCTO -->
<input type="hidden" name="txtCOD_ARTICULO" id="txtCOD_ARTICULO">	
<input type="hidden" id="idtmp_articulo" name="idtmp_articulo" value="">


<div class="panel-body" id="formularioregistros">
<form name="formulario" id="formulario" method="POST">
							
							
							
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#home"><span class="glyphicon glyphicon-briefcase"></span> PRODUCTO/SERVICIO</a></li>
	
<li><a data-toggle="tab" href="#menu1" onClick="listarunidad()" ><span class="glyphicon glyphicon-list-alt"></span> MÁS CARACTERÍSTICAS</a></li>
	
	
  <li><a data-toggle="tab" href="#menu2" onClick="listarunidad()" ><span class="glyphicon glyphicon-list-alt"></span> PRESENTACIONES</a></li>
	
<li><a data-toggle="tab" href="#menu4" onClick="listarrecetafin()" ><span class="glyphicon glyphicon-list-alt"></span> GRUPOS/RECETAS</a></li>
	
<li><a data-toggle="tab" href="#menu3"><span class="glyphicon glyphicon-picture"></span> IMAGENES</a></li>

</ul>

<div class="tab-content">
  <div id="home" class="tab-pane fade in active">
	  

<div class="row">
<div class="form-group col-lg-5 col-md-6 col-sm-6 col-xs-12">
<label>Nombre:</label>
<input type="text" class="form-control" name="txtDESCRIPCION_ARTICULO" id="txtDESCRIPCION_ARTICULO" onMouseOut="actualizararticulo()" placeholder="Descripción">
<input type="hidden" name="idproveedor" id="idproveedor">							  
<input type="hidden" name="tipoac" id="tipoac">	
<input type="hidden" name="idlocal" id="idlocal" value="<?=$_COOKIE['idlocal']?>" >	
<input type="hidden" name="marca" id="marca" value="" >	

<input name="ctimayoru" type="hidden" required id="ctimayoru"  value="99999999">
<input name="preciomu" type="hidden" required  id="preciomu" value="0.00">
<input name="comisionmu" type="hidden" required  id="comisionmu" value="0.00">
<input type="hidden" name="comision" id="comision" value="0" >

<input type="hidden" class="form-control" onKeyUp="actualizararticulo()" name="comisionm" id="comisionm" value="99999999" placeholder="Comision/mayor">

<input type="hidden" class="form-control" onKeyUp="actualizararticulo()" name="comisionmp" id="comisionmp" placeholder="Comisión/mayor %" value="0"  >
<input name="comisionu" type="hidden" required  id="comisionu" value="0.00">
	
	
<input name="ctimayor" type="hidden" onKeyUp="actualizararticulo()" required class="form-control" id="ctimayor" value="99999999">
<input type="hidden" class="form-control" onKeyUp="actualizararticulo()" name="pmayor" id="pmayor" value="0" required>

</div>

<div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
                            <label>Código:</label>
<input type="text" class="form-control" onKeyUp="actualizararticulo()" name="codigo" id="codigo" placeholder="Código Barras">
   
                          </div>
	  
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label>Unidad de medida:</label>
<select name ="medida" id="medida" class="form-control select2"  ></select>
</div>

<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label>T.Existencia:</label>
	
 <select class="form-control select-picker" name="existencia" id="existencia" required>
                             
<?php 
	$n=0;
$datos = "SELECT *FROM existencia  ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['cod']?>' ><?=$dato['tit']?></option>
<?php } ?>
</select>
</div>
</div>

<div class="row">
 <div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
 <label>Precio(*)Inc.IGV:</label>
<input name="precio" type="text" required class="form-control" id="precio" value="0.00">
 </div>

<div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
<label>P.POR.1:</label>
<div class="input-group">
<input name="precio_porcentaje" type="text" required class="form-control" id="precio_porcentaje" value="1">
<span class="input-group-addon">%</span>
</div>
</div>

<div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
<label>Precio May1(*):</label>
<input name="precio_mayor" type="text" required class="form-control" id="precio_mayor" value="0">
</div>

<div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
<label>P.POR.2:</label>
<div class="input-group">
<input name="precio_porcentaje2" type="text" required class="form-control" id="precio_porcentaje2" value="1">
<span class="input-group-addon">%</span>
</div>
</div>

<div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
<label>Precio May2(*):</label>
<input name="precio_mayor2" type="text" required class="form-control" id="precio_mayor2"  value="0">
</div>

<div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
<label>P.POR.3:</label>
<div class="input-group">
<input name="precio_porcentaje3" type="text" required class="form-control" id="precio_porcentaje3" value="1">
<span class="input-group-addon">%</span>
</div>
</div>

<div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
<label>Precio May3(*):</label>
<input name="precio_mayor3" type="text" required class="form-control" id="precio_mayor3"  value="0">
</div>

<div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
<label>Stock(*):</label>
<?php if ($_COOKIE['configuracion']==1) { ?> 
<input type="text" class="form-control" onKeyUp="actualizararticulo()" name="stock" id="stock" required>
<?php }else{ ?>
<input type="text" class="form-control" onKeyUp="actualizararticulo()" name="stock" id="stock" readonly required>
<?php } ?>
</div>


<div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
<label>Dscto:</label>
<input name="oferta" type="text" required class="form-control" id="oferta" value="0.00">
</div>

<div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
<label>Precio Compra(*):</label>
<input type="text" class="form-control" onKeyUp="actualizararticulo()" name="precioc" id="precioc" required>
</div>


</div>
		
		
		
		
	</div>
		
<div id="menu1" class="tab-pane fade">

	  

<div class="col-sm-12" >


<input type='hidden' id='codartu' name='codartu' value='' /> 

<div class="row">
  <div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
    <label>Afectación SUNAT:</label>
    <select name="idcatalogo_afectacion" id="idcatalogo_afectacion" class="form-control select2" onChange="actualizararticulo()">
      <option value="">-SELECCIONE-</option>
      <?php
      $rspta_afec = ejecutarConsulta("SELECT idcatalogo_afectacion, descripcion_corta FROM catalogo_afectaciones_sunat WHERE estado='1' ORDER BY idcatalogo_afectacion ASC");
      if ($rspta_afec) {
        while ($af = $rspta_afec->fetch_object()){
          $nom_af = isset($af->descripcion_corta) ? trim($af->descripcion_corta) : '';
          if ($nom_af === '') { $nom_af = 'Afectación '.$af->idcatalogo_afectacion; }
      ?>
        <option value="<?=$af->idcatalogo_afectacion?>"><?=$nom_af?></option>
      <?php
        }
      }
      ?>
    </select>
  </div>

  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>IGV:</label>
    <select name ="exonerado" id="exonerado" class="form-control" onChange="actualizararticulo()" >
      <option value='0' selected="selected" >IGV 18%</option>
      <option value='1' >EXONERADO 0%</option>
      <option value='3' >INAFECTO 0%</option>
    </select>
  </div>

  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>MONEDA:</label>
    <select name ="moneda" id="moneda" class="form-control" onChange="actualizararticulo()" >
      <option value='PEN' selected="selected" >SOLES</option>
      <option value='USD' >DOLARES</option>
    </select>
  </div>

  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Canjeable:</label>
    <select name ="canje" id="canje" class="form-control">
      <option value='NO' selected="selected" >NO</option>
      <option value='SI' >SI</option>
    </select>
  </div>

  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Puntos:</label>
    <input type="text" class="form-control" name="canjepuntos" id="canjepuntos" required>
  </div>

  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Puntos.Canje:</label>
    <input type="text" class="form-control" name="canjecobro" id="canjecobro" required>
  </div>

  <div class="form-group col-lg-1 col-md-3 col-sm-6 col-xs-12">
    <label>St.min:</label>
    <input type="text" class="form-control" onKeyUp="actualizararticulo()" name="stockmin" id="stockmin" required>
  </div>

  <div class="form-group col-lg-1 col-md-3 col-sm-6 col-xs-12">
    <label>St.max:</label>
    <input type="text" class="form-control" onKeyUp="actualizararticulo()" name="stockmax" id="stockmax" required>
  </div>
  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Maneja lote:</label>
    <select class="form-control" name="maneja_lote" id="maneja_lote" onChange="actualizararticulo()">
      <option value="0" selected>NO</option>
      <option value="1">SI</option>
    </select>
  </div>

  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Maneja serie:</label>
    <select class="form-control" name="maneja_serie" id="maneja_serie" onChange="actualizararticulo()">
      <option value="0" selected>NO</option>
      <option value="1">SI</option>
    </select>
  </div>

  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Maneja garantía:</label>
    <select class="form-control" name="maneja_garantia" id="maneja_garantia" onChange="actualizararticulo()">
      <option value="0" selected>NO</option>
      <option value="1">SI</option>
    </select>
  </div>

  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Tipo garantía:</label>
    <select class="form-control" name="garantia_tipo" id="garantia_tipo" onChange="actualizararticulo()">
      <option value="NINGUNA" selected>NINGUNA</option>
      <option value="FABRICANTE">FABRICANTE</option>
      <option value="COMERCIAL">COMERCIAL</option>
      <option value="EXTENDIDA">EXTENDIDA</option>
    </select>
  </div>

  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Garantía (meses):</label>
    <input type="number" min="0" class="form-control" name="garantia_meses" id="garantia_meses" value="0" onChange="actualizararticulo()">
  </div>
</div>
		
		
<div class="form-group col-lg-12">					
<div class="row">
	

	
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Marca(*):</label>
<select id="idmarca" name="idmarca" onChange="actualizararticulo()"  class="form-control form-control select2" required></select>
</div>
                  
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Grupo(*):</label>
<select id="idcategoria" name="idcategoria" onChange="actualizararticulo()" class="form-control form-control select2" required></select>
</div>
	
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Línea:</label>

<select id="linea" name="linea" onChange="actualizararticulo()" class="form-control form-control select2" >
<?php
$rspta=ejecutarConsulta("SELECT * FROM categoria WHERE nivel='3' AND idnivel='0' AND idempresa='$_COOKIE[id]' ");
while ($reg = $rspta->fetch_object()){			
?>
<option value="<?=$reg->idcategoria?>"><?=$reg->nombre?></option>
<?php } ?>	
</select>
</div>
	
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Sub Línea:</label>
<select id="sublinea" name="sublinea" onChange="actualizararticulo()" class="form-control form-control select2" ><option value="0" >-SELECCIONE-</option></select>
</div>
	
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Sub Familia:</label>
<select id="subfamilia" name="subfamilia" onChange="actualizararticulo()" class="form-control form-control select2" ><option value="0" >-SELECCIONE-</option></select>
</div>
    
	
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Local:</label>

<select id="idlocal" name="idlocal" onChange="actualizararticulo()" class="form-control form-control select2" >
<?php

$rspta=ejecutarConsulta("SELECT * FROM sucursal WHERE id='$_COOKIE[idlocal]' and idnivel='0' AND idempresa='$_COOKIE[id]' ");

//$rspta=ejecutarConsulta("SELECT * FROM sucursal WHERE idnivel='0' AND idempresa='$_COOKIE[id]' ");
while ($reg = $rspta->fetch_object()){			
?>
<option value="<?=$reg->id?>"><?=$reg->sucursal?></option>
<?php } ?>	
</select>
</div>
	
	
</div>							
</div>					
<div class="row">
  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Cta Ventas:</label>
    <input name="ctaventas" type="text" onKeyUp="actualizararticulo()" required class="form-control" id="ctaventas" value="70111">
  </div>
	  
  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Cta Compras:</label>
    <input name="ctacompras" type="text" onKeyUp="actualizararticulo()" required class="form-control" id="ctacompras" value="70111">
  </div>
	  
  <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <label>Proveedor:</label>
    <select id="farmaceutica" class="form-control form-control select2" multiple onChange="actualizararticulo()"  name="farmaceutica[]">
      <option value='0' >SELECCIONE</option>
    </select>
  </div>

  <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <label>Principio activo:</label>
    <select id="pactivo" onChange="actualizararticulo()" multiple name="pactivo[]" class="form-control form-control select2" required></select>	
  </div>
	  
  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Registro sanitario:</label>
    <input type="text" class="form-control" name="sanitario" id="sanitario" onKeyUp="actualizararticulo()" placeholder="Registro sanitario">
  </div>
	
  <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
    <label>Bolsa:</label>
    <select name ="bolsa" id="bolsa" class="form-control" onChange="actualizararticulo()"  >
      <option value='0' selected="selected" >NO</option>
      <option value='1' >SI</option>
    </select>
  </div>
	  
							
<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Código/Sunat:</label>				  
							  
<div class="input-group">
<input type="text" class="form-control"  id="codigos" name="codigos" >
<span class="input-group-btn">
<button class="btn btn-primary" onClick="addcodigosunat()" type="button"><span class="glyphicon glyphicon-search"></span></button>
</span>
</div>
</div>
	</div>
	
	
	




</div>
  
	  
  </div>
	
	
	
	
	
	
	
  <div id="menu2" class="tab-pane fade">

	  

<div class="col-sm-12" >


<input type='hidden' id='codartu' name='codartu' value='' /> 
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label>Nombre:</label>
<input type="text" class="form-control" name="nombreu" id="nombreu" placeholder="Nombre" required>
</div>

<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Cantidad:</label>
<input name="ctiunidad" type="text" required class="form-control" id="ctiunidad" placeholder="Cantidad" value="1">
</div>

<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Precio:</label>
<input name="preciou" type="text" required class="form-control" id="preciou" placeholder="Precio" value="0.00">
</div>
	



<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label>Unidad de medida:</label>
<select name ="medida2" id="medida2" class="form-control select2"  >
<option value='' >SELECCIONE</option>
</select>
</div>

<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label><hr></label>
<button class="btn btn-success" onclick="guardaunidad()" type="button"><i class="fa fa-save"></i> AGREGAR</button>
</div>



</div>



<div class="col-sm-12" style=" margin-top: 0px;">
   
                       
<table id="tblunidad" class="table  table-condensed  compact" style="width: 100%">
            <thead>
                <th style="width: 1%"></th>
                <th style="width: 50%">Nombre</th>
                <th style="width: 25%">Medida</th>
                <th style="width: 5%">CTI</th>
                <th style="width: 5%">PRECIO</th>
                <th style="width: 5%">PRECIO/M</th>
                <th style="width: 5%">COMISION</th>
                <th style="width: 5%">COMISION/M</th>
            </thead>
            <tbody>
              
            </tbody>

          </table>
</div>
	  
	  
  </div>
	
	
	
	
<div id="menu4" class="tab-pane fade">

	  

<div class="form-group col-lg-1 col-md-1 col-sm-6 col-xs-12">

	<button type="button" class="btn btn-default" onClick="BuscarArticulo()" id="agregarreceta" >
<i class="fas fa-save"></i> AGREGAR ARTÍCULO</button>
</div>                          

<div class="col-sm-12">						
<table class="table"  style="width:100%" id="ventas">
<thead>
<tr>
<th width="3%">#</th>
<th width="50%">Descripción</th>
<th width="7%">U/M</th>
<th width="20%">Cti</th>
<th width="20%"></th>
</tr>
</thead>
<tbody></tbody>
</table>
</div>



	  
	  
  </div>
	
	
	
	
	
<div id="menu3" class="tab-pane fade">
  <div id="subirimagen"></div>
</div>


</div>
							
					
							
							
							
							
<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
	
	
<hr>
	
	
                            <button class="btn btn-primary" type="button" id="btnGuardar" onclick="guardaryeditar(event)"><i class="fa fa-save"></i> Guardar</button>

                            <button class="btn btn-danger" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                          </div>                          
							
							
							
							
                        </form>
                    </div>
				
				
</div>
		
			</div>
		
		</div>

<div id="mydiv" style="display: none"></div>		
		
		<br />


<?php 
							}
	require'includes/pie.php'; ?>		


<!-- Modal -->
<div class="modal fade" id="myModal" style="display: none;" aria-hidden="true">
<div class="modal-dialog modal-lg">
      <div class="modal-content">
		  
<div class="modal-header">
              <h4 class="modal-title">ARTICULOS</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>

<div class="modal-body">
<div class="row" >                        					
<div class="col-sm-12" >
                               
                               
                               
<table id="tblarticulos" class="table  table-condensed  compact dataTable no-footer" style="width: 100%">
            <thead>
                <th style="width: 5%"></th>
                <th style="width: 5%">Cód</th>
                <th style="width: 55%">Nombre</th>
                <th style="width: 5%">Precio</th>
                <th style="width: 20%">Cti</th>
            </thead>
            <tbody></tbody>
          </table>
                               
</div>
 </div>
</div>

    </div>
  </div>  
  </div>
  <!-- Fin modal -->


<!-- Modal -->
  <div class="modal fade" id="modalserie" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">

<div Class="modal-header">
  <Button type="button" Class='close' data-dismiss="modal">×</Button>
                            <h4 Class='modal-title' id='myModalLabel'>SERIES/LOTES/FECHA VENCIMIENTO</h4>
                        </div>

                        <div Class='modal-body'>
 <div class="row" >                        

<div class="col-sm-12" style="border-top:0px;" id="descuento">&nbsp;</div> 
                          
                          


<div class="col-sm-12" style=" margin-top: -20px;">

<form name="formserie" id="formserie" >

<input type='hidden' id='codart' name='codart' value='' /> 
<div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
<input type="text" class="form-control" name="serie" id="serie" placeholder="Serie" required>
</div>

<div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
<input type="text" class="form-control" name="lote" id="lote" placeholder="Lote" required>
</div>
	
<div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
<input type="text" class="form-control" name="stockser" id="stockser" value="1.00" required>
</div>

<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

<select id="idproveedora" name="idproveedora" class="form-control" ><option value="0" >-SELECCIONE-</option></select>
</div>
	
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<input type="date" value=""  class="form-control" name="fechven" id="fechven" required>
</div>
	
	

<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<button class="btn btn-success" onclick="guardaserie()" type="button"><i class="fa fa-save"></i> GUARDAR</button>
</div>

 </form> 

</div>



<div class="col-sm-12" style=" margin-top: 0px;">
   
                       
<table id="tblseries" class="table  table-condensed  compact" style="width: 100%">
            <thead>
                <th style="width: 1%"></th>
				<th style="width: 1%">#</th>
                <th style="width: 5%">Serie</th>
                <th style="width: 25%">Lote</th>
                <th style="width: 5%">Fecha</th>
                <th style="width: 30%">Fecha/Vto</th>
                <th style="width: 1%">Est</th>
                <th style="width: 5%">Stock</th>
				<th style="width: 5%">Estado</th>
            </thead>
            <tbody>
              
            </tbody>

          </table>
</div>



</div>
                                     
</div>
</div>
  </div>  
  </div>

  <!-- Fin modal -->


  
  
  <!-- Modal -->
  <div class="modal fade" id="modalunidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">

<div Class="modal-header">
  <Button type="button" Class='close' data-dismiss="modal">×</Button>
                            <h4 Class='modal-title' id='myModalLabel'>UNIDAD DE MEDIDA</h4>
                        </div>

                        <div Class='modal-body'>
 <div class="row" >                        

<div class="col-sm-12" style="border-top:0px;" id="descuento">&nbsp;</div> 

</div>
                                     
</div>
</div>
  </div>  
  </div>
	
	
	
<!-- Modal -->
  <div class="modal fade" id="modalcodigo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">

<div Class="modal-header">
<Button type="button" Class='close' data-dismiss="modal">×</Button>
<h4 Class='modal-title' id='myModalLabel'>CÓDIGO SUNAT</h4>
</div>

                        <div Class='modal-body'>
 <div class="row" >                        

<div class="col-sm-12" style="border-top:0px;" id="descuento">&nbsp;</div> 
                          
                          

<div class="col-sm-12" style=" margin-top: 0px;">
   
                       
<table id="tblsunat" class="table  table-condensed  compact">
            <thead>
                <th style="width: 1%"></th>
                <th style="width: 25%">ARTÍCULO</th>
                <th style="width: 5%">CODIGO SUNAT</th>
            </thead>
            <tbody>
              
            </tbody>

          </table>
</div>



</div>
                                     
</div>
</div>
  </div>  
  </div>

  <!-- Fin modal -->
	
	
	
	

<!-- Modal -->
  <div class="modal fade" id="stockinicial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">

<div Class="modal-header">
  <Button type="button" Class='close' data-dismiss="modal">×</Button>
                            <h4 Class='modal-title' id='myModalLabel'>STOCK INICIAL</h4>
                        </div>

                        <div Class='modal-body'>
 <div class="row" >                        

<div class="col-sm-12" style="border-top:0px;" id="descuento">&nbsp;</div> 

<div class="col-sm-12" style=" margin-top: -20px;">
<form name="formserie" id="formserie" >

<input type='hidden' id='idi' name='idi' value='' /> 
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label class="mr-sm-2" for="inlineFormCustomSelect">STOCK INICIAL</label>
<input type="text" class="form-control" name="stocki" id="stocki" value="0.00" required>
</div>

<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label class="mr-sm-2" for="inlineFormCustomSelect">PRECIO INICIAL</label>
<input type="text" class="form-control" name="precioi" id="precioi" value="0.00" required>
</div>
	
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label class="mr-sm-2" for="inlineFormCustomSelect">FECHA INGRESO</label>
<input type="date" class="form-control" name="fechai" id="fechai"  value="" required>
</div>

<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label class="mr-sm-2" for="inlineFormCustomSelect"><hr></label>
<button class="btn btn-success" onclick="guardastockinicial()" type="button"><i class="fa fa-save"></i> GUARDAR</button>
</div>

 </form> 

</div>





</div>
                                     
</div>
</div>
  </div>  
  </div>

  <!-- Fin modal -->

	
	
	
	
	
	
<link type="text/css" href="plugins/dataTables.checkboxes.css" rel="stylesheet" />
<script type="text/javascript" src="plugins/dataTables.checkboxes.min.js"></script>	
	

  <!-- Fin modal -->
 <script> 
  
//Función que se ejecuta al inicio
function init(){
	mostrarform(false);
	listar();
	$("#formulario").on("submit",function(e)	{
		guardaryeditar(e);
	})
	$("#imagenmuestra").hide();
}
	
  </script>

<script src="plugins/jquery/jquery.min.js"></script>

<link rel="stylesheet" href="plugins/datatables/datatables.min.css">
<script src="plugins/datatables/datatables.min.js"></script>

<!-- Si usas Buttons -->
<link rel="stylesheet" href="plugins/datatables/buttons.dataTables.min.css">
<script src="plugins/datatables/dataTables.buttons.min.js"></script>
<script src="plugins/datatables/buttons.html5.min.js"></script>
<script src="plugins/datatables/jszip.min.js"></script>
<script src="plugins/datatables/pdfmake.min.js"></script>
<script src="plugins/datatables/vfs_fonts.js"></script>

<!-- Bootstrap (si quieres dropdown automático, opcional si usas tu fix manual) -->
<script src="plugins/bootstrap/js/bootstrap.min.js"></script>

<!-- TU SCRIPT AL FINAL -->
<script src="scripts/articulo.js?v=<?php echo time(); ?>"></script>
	


	
	


<script>
	

	
	
	$(document).ready(function(){
 
    $(".messages").hide();
    //queremos que esta variable sea global
    var fileExtension = "";
    //función que observa los cambios del campo file y obtiene información
    $(':file').change(function()
    {
        //obtenemos un array con los datos del archivo
        var file = $("#archivo")[0].files[0];
        //obtenemos el nombre del archivo
        var fileName = file.name;
        //obtenemos la extensión del archivo
        fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        //obtenemos el tamaño del archivo
        var fileSize = file.size;
        //obtenemos el tipo de archivo image/png ejemplo
        var fileType = file.type;
        //mensaje con la información del archivo
		console.log(fileType);
        showMessage("<span class='info'>Archivo para subir: "+fileName+", peso total: "+fileSize+" bytes.</span>");
    });
 
    //al enviar el formulario
		/*
    $(':button').click(function(){
		
 
		
		
		
    });*/
})
 
	
	
	

	
function subeexcel(){

$('#btnsubir').attr("disabled", true);	
	
var file = $("#archivo")[0].files[0];
        //obtenemos el nombre del archivo
        var fileName = file.name;
        //obtenemos la extensión del archivo
        fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        //obtenemos el tamaño del archivo
        var fileSize = file.size;
        //obtenemos el tipo de archivo image/png ejemplo
        var fileType = file.type;
        //mensaje con la información del archivo
		
		if(fileType=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
		
        //información del formulario
        var formData = new FormData($(".for")[0]);
        var message = ""; 
        //hacemos la petición ajax  
        $.ajax({
            url: 'modelos/upload.php',  
            type: 'POST',
            // Form data
            //datos del formulario
            data: formData,
            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            //mientras enviamos el archivo
            beforeSend: function(){
                message = $("<span class='before'>Subiendo archivo, por favor espere...</span>");
                showMessage(message)        
            },
            //una vez finalizado correctamente
            success: function(data){
				console.log(data);
                message = $("<span class='success'>El archivo ha subido correctamente.</span>");
                showMessage(message);
                if(isImage(fileExtension))
                {
                    $(".showImage").html("<img src='images/"+data+"' />");
                }
            },
            //si ha ocurrido un error
            error: function(){
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
		
}else{
	message = $("<span class='error'>El archivo solo puede ser .xlsx (Excel).</span>");
    showMessage(message);
}
	
	
}
	
//como la utilizamos demasiadas veces, creamos una función para 
//evitar repetición de código
function showMessage(message){
    $(".messages").html("").show();
    $(".messages").html(message);
}
 
//comprobamos si el archivo a subir es una imagen
//para visualizarla una vez haya subido
function isImage(extension)
{
    switch(extension.toLowerCase()) 
    {
        case 'jpg': case 'gif': case 'png': case 'jpeg':
            return true;
        break;
        default:
            return false;
        break;
    }
}

	//$("body").removeClass('sidebar-collapse');
	// element.classList.add("mystyle");

	
	
	
	
	</script>
	
<!-- FILEINPUT (SUBIDA DE IMÁGENES) -->
<link href="plugins/fileinput/css/fileinput.min.css" rel="stylesheet">

<script src="plugins/fileinput/js/fileinput.min.js"></script>
<script src="plugins/fileinput/js/locales/es.js"></script>




</body>
</html>
