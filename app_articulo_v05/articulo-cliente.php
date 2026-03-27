<?php
require'config/conexion.php';

?>
<!DOCTYPE html>
<html lang="es">
<head><?php require'includes/header.php'; ?>
	
	
<link href="plugins/fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>

<style>
  #tbllistado { width: 100% !important; }
  #tbllistado thead th, #tbllistado tbody td { white-space: nowrap; }
  #tbllistado td.dt-right { text-align: right !important; }
  #tbllistado td.dt-center, #tbllistado th.dt-center { text-align: center !important; }
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
    

    
<div class="panel panel-body">
    <div class="col-sm-4" >
        <div class="panel-title"><?=$t?></div>
        
        <div class="col-sm-2" >
            <button class="btn btn-black btn-sm" onclick="location.reload()"><span class="glyphicon glyphicon-search"></span> PRECIO LISTA - GENERAL</button>	
        </div>
        
    </div>

    
    
   
        <div class="col-sm-6" >
            <select class="js-example-basic-single form-control" id="cliente" name="cliente"  name="state">
            </select>
             </div>
         <div class="col-sm-2" >
            <button class="btn btn-success btn-sm" onclick="listar()"><span class="glyphicon glyphicon-search"></span> FILTRAR</button>	
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
	
	
<table id="tbllistado" class="table table-bordered table-responsive display nowrap" width="100%" cellspacing="0">
						<thead>
<tr>
<th>Opc</th>
<th>List.Pre.</th>
<th>ID</th>
<th>Código</th>
<th>Descripción</th>
<th>Precio</th>
<th>Estado</th>
</tr>
</thead>
<tbody></tbody>
</table>
	
</div>
<!-- LA ID DEL PRODUCTO -->
<input type="hidden" name="txtCOD_ARTICULO" id="txtCOD_ARTICULO">	
	
<div class="panel-body" id="formularioregistros">
<form name="formulario" id="formulario" method="POST">


	  
<div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <label>Nombre:</label>
<input type="text" class="form-control" name="txtDESCRIPCION_ARTICULO" id="txtDESCRIPCION_ARTICULO" placeholder="Descripción" readonly >
<input type="hidden" name="idproducto" id="idproducto"  >
<input type="hidden" name="idclienteproducto" id="idclienteproducto"  >
</div>
	  
<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Código:</label>
<input type="text" class="form-control" name="codigo" id="codigo" readonly >
</div>

 <div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
 <label>Precio(*)Inc.IGV:</label>
<input name="precio" type="text" required class="form-control" id="precio">
 </div>
					
<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
	
	
<hr>
	
	
<div style="display:flex; align-items:center; margin-bottom:10px;">
  
  <!-- GRUPO IZQUIERDA -->
  <div style="display:flex; gap:8px;">
    <button class="btn btn-primary" type="button" id="btnGuardar" onClick="actualizararticulo()">
      <i class="fa fa-save"></i> Guardar
    </button>

    <button type="button" class="btn btn-default" onclick="cancelarform();">
      <i class="fa fa-arrow-left"></i> Regresar
    </button>

    <button class="btn btn-danger" onclick="cancelarform()" type="button">
      <i class="fa fa-arrow-circle-left"></i> Eliminar
    </button>
  </div>

  <!-- ESPACIADOR -->
  <div style="flex:1;"></div>

  <!-- BOTÓN DERECHA -->
  <button type="button" class="btn btn-danger" id="btnPrecioLista">
    <i class="fa fa-undo"></i> REGRESAR AL PRECIO LISTA
  </button>

</div>


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

<table id="tbllistado" class="table  table-condensed  compact dataTable no-footer" style="width: 100%">
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


<!-- Fin modal -->
<script> 

//Función que se ejecuta al inicio
function init(){
mostrarform(false);
listar();
}

</script>
<script type="text/javascript" src="scripts/articulo-cliente.js?v=<?php echo date('s'); ?>"></script>
<script src="plugins/fileinput/js/locales/es.js" type="text/javascript"></script>	

</body>
</html>
