<?php
require'config/conexion.php';
if ($_COOKIE['vencimiento']==0){
header('Location: escritorio.php');
}

clearstatcache();


?>
<!DOCTYPE html>
<html lang="en">
<head><?php require'includes/header.php'; ?>
<style>
  /* ===== Modal Pago Datos: pequeño y centrado encima de PAGO VENTA ===== */
  #modalPagoDatos .modal-dialog.modal-pagodatos{
    max-width: 420px;
    margin: 0 auto;
  }
  /* Asegura stacking correcto cuando hay 2 modales (pagof + modalPagoDatos) */
  .modal.modal-stack{ /* z-index se calcula por JS */ }
  .modal-backdrop.modal-stack{ /* z-index se calcula por JS */ }
</style>

</head>
<body class="page-body  page-fade" data-url="http://neon.dev">
<?php require'includes/sup.php'; ?>

		
<?php
if ($_COOKIE['ventas']==1){
	
	

//apc_clear_cache();
	
	
?>
	
	
<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="panel-title">Venta</div>

                    </div>


                    <div class="box">

                        <!-- /.box-header -->
                        <!-- centro -->
                        <div class="panel-body table-responsive" id="listadoregistros">

                            <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="row">



                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <label>Fecha Inicio</label>
                                        <input type="date" class="form-control input-sm" name="fecha_inicio" id="fecha_inicio" value="<?php echo date("Y-m-d"); ?>">
                                    </div>
                                    <div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <label>Fecha Fin</label>
                                        <input type="date" class="form-control input-sm" name="fecha_fin" id="fecha_fin" value="<?php echo date("Y-m-d"); ?>">
                                    </div>



                            <div class="form-inline col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                <div class="input-group-btn">

                                    <button class="btn btn-default" onclick="listar()" type="button" ><span class="glyphicon glyphicon-search"></span> FILTRAR</button>

<?php
if ($_COOKIE['administrador']==1){ 
?>
                                    <button class="btn btn-default " onclick="mostrarform(true, 0)"><i class="fa fa-plus-circle"></i> AGREGAR</button>
         <?php
} 
?>                           
                                    
                                    <a href="venta-rapida.php" >
                                        <button  type="button" class="btn btn-default"><i class="glyphicon glyphicon-send"></i> IR A VENTA RAPIDA</button>
                                    </a>                                </div>
                            </div>

                            </div>
                            </div>

                            <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">

                                <table id="tbllistado_venta"  class="table table-bordered table-responsive display nowrap" width="100%" cellspacing="0">
                                    <thead>
							  <th>-</th>
                            <th>Opciones</th>
							<th>Estado</th>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>T/Doc</th>
                            <th>Ser-Núm</th>
                            <th>Cliente</th>
                            <th>DOC/CLI</th>
                            <th>S/Tot</th>
                            <th>IGV</th>
                            <th>Total</th>
                            <th>Usuario</th>
							<th>Detracción</th>

                                    </thead>
                                    <tbody></tbody>

                                </table>
                    </div>
	
	</div>
<div class="panel-body" style="height: auto;" id="formularioregistros">
	
<form name="miForm" id="miForm" method="POST">	

<ul style="visibility: visible;" class="nav nav-tabs test2">
    <li class="active"><a data-toggle="tab" href="#home" aria-expanded="false">COMPROBANTE</a></li>
    <li class=""><a data-toggle="tab" href="#menu1" aria-expanded="false">OTROS ATRIBUTOS</a></li>
    <li class=""><a data-toggle="tab" href="#menu2" aria-expanded="false">GUÍAS RELACIONADAS</a></li>
    <li class=""><a data-toggle="tab" href="#menu3" aria-expanded="false">EXPORTACIÓN</a></li>
    <li class=""><a data-toggle="tab" href="#menu4" aria-expanded="false">MACH</a></li>
</ul>

<div class="tab-content">
<div id="home" class="tab-pane fade active in">

<div class="col-sm-12 borde" style="border-top:0px;">
<div class="row">
<div class="row">

<!-- contenido ocultos -->
<input type='hidden' id='accion' name='accion' value='0' />
<input type='hidden' id='txtID' name='txtID' value='' />
<input type='hidden' id='vendedorp' name='vendedorp' value='<?=$_SESSION["idusuario"]?>' />
<input type='hidden' id='mes' name='mes' />
<input type='hidden' id='opadelanto' name='opadelanto' value='NO' />
<input type="hidden" name="scredito" id="scredito">
<input type="hidden" name="ncredito" id="ncredito">
<input type="hidden" id="adelanto" name="adelanto" readonly class="form-control" value="" />									
<input name="adelantot" type="hidden" id="adelantot" value="0.00" style="height: 20px;" class="form-control" readonly />
<input name="saldoanterior" type="hidden" id="saldoanterior" value="0.00" class="form-control" readonly />
<input type="hidden" id="condiciones" name="condiciones" class="form-control" value="0" />
<input type="hidden" id="tipoguia" name="tipoguia" class="form-control" value="09" />
	
<input type='hidden' id='idventa' name='idventa' value='0' />
	
	
<div class="col-xs-12">

	
<div class=" col-lg-1 col-md-1 col-sm-6 col-xs-12 padno">
<label>Tipo.Compro:</label>
                                
                                      
<select name ="txtID_TIPO_DOCUMENTO" id="txtID_TIPO_DOCUMENTO" class="form-control" onChange="llamaseries()"  >
                                            <option value='03' selected="selected">BOLETA</option>
                                            <option value='01' >FACTURA</option>
                                          <!--  <option value='90'>RECIBO</option>-->
                          </select>
	

</div>

<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Series:</label>
<div class="input-group">
<div class="input-group-btn">
<button type="button" class="btn btn-info">Action-</button>
<select class="form-control" name="txtSERIE" id="txtSERIE" onChange="numerosfinales()" required style="margin-top: -31px; padding-left: 1px; padding-right: 1px;" ></select>
</div>	
	
<input type="text" id="txtNUMERO" name="txtNUMERO" readonly class="form-control" style="padding-left: 0px; padding-right: 0px; margin: 0px;" value="" />

</div>
	
</div>

    <div class=" col-lg-1 col-md-1 col-sm-6 col-xs-12 padno">
<label>Nota/Ped:</label>                                
<input type="text" id="notpedido" name="notpedido" class="form-control" value="" />
</div>
	  
<div class="col-lg-1 col-md-1 col-sm-6 col-xs-12 form-group oculto">
<div class="row">	
<label>O/Comp:</label>                                
<input type="text" id="ocompra" name="ocompra" class="form-control" value="" />
</div>
</div>	
<div class="form-group col-lg-3">	
<label>N Guía:</label>


<div class="input-group">
<div class="input-group-btn">
<div class="btn-group">
<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
<span id="nombreguia" >REMIT</span> <span class="caret"></span>
</button>
<ul class="dropdown-menu" role="menu">								
<li><a href="javascript:void(0)" onclick="tipoguia('09', 'REMIT')" >REMIT</a></li>
<li><a href="javascript:void(0)" onclick="tipoguia('31', 'TRANS')" >TRANS</a></li>
</ul>
</div>
</div>
<input type="text" id="guia" name="guia" class="form-control" value="" />
<input type="hidden" id="idguia" name="idguia" class="form-control" value="0" />

<span class="input-group-btn">
<button class="btn  btn-danger" onClick="listarguia()" type="button"><span class="glyphicon glyphicon-plus"></span>
</span>
</div>
</div>	






<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12  oculto">
<label>Fecha.Doc:</label>
<input type="date" id="txtFECHA_DOCUMENTO" class="form-control" name="txtFECHA_DOCUMENTO" value="<?=date('Y-m-d');?>" />
</div>


    <div class=" col-lg-2 col-md-2 col-sm-6 col-xs-12  oculto">
        <label>Vendedor: </label>
        <div class="form-group">

            <select class="form-control" name="vendedor" id="vendedor" required>

                <option value='' selected >-SELECCIONE-</option>

                <?php
                $n=0;
                $datos = "SELECT *FROM usuario WHERE nivel='1' AND condicion='1' AND idempresa='$_COOKIE[id]' ";
                $datos = ejecutarConsulta($datos);
                while($dato=mysqli_fetch_array($datos)) {
                    $n=$n+1;
                    ?>
                    <option value='<?=$dato['idusuario']?>' ><?=$dato['nombre']?></option>
                <?php } ?>
            </select>
        </div>

    </div>




</div>

<div class="col-xs-12" style="margin-top: -10px;" >

    <div class="col-lg-4">
        <label>Cliente Comprobante:</label>
        <div class="input-group" >
            <select id="txtID_CLIENTE" name="txtID_CLIENTE" class=" form-control form-control-solid " data-live-search="true" required data-width="auto" ></select>
            <span class="input-group-btn oculto">
<button class="btn  btn-danger" id="addcliente" onClick="ncliente()" type="button"><span class="glyphicon glyphicon-plus"></span></button>
      </span>
        </div>
    </div>

<div class="col-lg-1 col-md-1 col-sm-6 col-xs-12">
<label>Moneda:</label>  
<div class="form-group">
<select name ="txtMONEDA" id="txtMONEDA" class="form-control" onChange="totales()"  />
<option value="PEN" selected="selected">SOLES</option>
<option value="USD">DOLARES</option>
</select>
</div>
</div>

 <div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12 padno">
        <label>F.Pago/M.Pago</label>
        <div class="input-group" >
            <select id="fpago_mpago" name="guia5" class=" form-control form-control-solid " data-live-search="true" required data-width="auto" ></select>
            <span class="input-group-btn oculto">
      </span>
        </div>
    </div>

<div class="form-group col-lg-2">	
<label>Forma/pago:</label>

<div class="input-group">
<select id="pago" name="pago" class="form-control" />
<option value="CONTADO" selected="selected">CONTADO</option>
<option value="CREDITO" >CRÉDITO</option>
</select>
<span class="input-group-btn">
<button class="btn  btn-danger" onClick="modalltras()" type="button"><span class="glyphicon glyphicon-plus"></span>
</span>
	
</div>
</div>	

<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12 padno">
<label>Med/pago:</label>
<select id="medio" name="medio" class="form-control" />
</select>
</div>



   
	

	



</div>

</div>
</div>
</div>
			
<div class="col-sm-12 borde mostrar" style="border-top:0px;">

<div class="row" >
<div class="col-sm-6  col-xs-6 form-group" >
<button type="button" class="btn btn-default form-control " onClick="BuscarArticulo()" id="agregar2" ><i class="fa fa-plus-square-o"></i> Agregar</button>
</div>
		
<div class="col-sm-6  col-xs-6 form-group" >
<button type="button" class="btn btn-default form-control" onClick="grabaventa()" id="guardar2" ><i class="fa fa-save"></i> Guardar</button>
</div>

<div class="col-sm-6  col-xs-6 form-group" >
<button type="button" class="btn btn-default form-control" id="imprimirt2" onclick="llenaimpresion()">
<i class="fa fa-print"></i> Imprimir Ticket</button>
</div>
<div class="col-sm-6  col-xs-6 form-group" >
<button type="button" class="btn btn-default form-control" onClick="nuevo()"><i class="fa fa-file-o"></i> Nuevo</button>
</div>
</div>

</div>

                          
<div id="mydiv" style="display: none"></div>
                        
		
		
		
		
</div>
					

<div id="menu1" class="tab-pane fade">

		
<div class="col-xs-12">
  <div class="row">

<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" style="margin-left: 0%; padding-left: 0px;">
<label>ÁREA/C.COSTOS:</label>
<select id="ccostos" name="ccostos" class="form-control" />
<option value='0' selected >-NINGUNO-</option>
<?php 
	$n=0;
$datos = "SELECT *FROM categoria WHERE idempresa='$_COOKIE[id]' AND nivel='4' ORDER BY nombre ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['idcategoria']?>' ><?=$dato['nombre']?></option>
<?php } ?>
</select>
	
</div>


<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" style="margin-left: 0%; padding-left: 0px;">
<label>Control Pres:</label>
<select id="controlpresupuestal" name="controlpresupuestal" class="form-control" />
<option value='0' selected >-NINGUNO-</option>
<?php 
	$n=0;
$datos = "SELECT *FROM categoria WHERE idempresa='$_COOKIE[id]' AND nivel='6' ORDER BY nombre ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['idcategoria']?>' ><?=$dato['nombre'].'|'.$dato['ctaventas']?></option>
<?php } ?>
</select>
	
</div>

<div class=" col-lg-2 col-md-2 col-sm-6 col-xs-12  oculto">
<label>DETRACCIONES: </label>
<div class="form-group">  
                                      
 <select class="form-control" name="detracciones" onChange="totales()" id="detracciones" required>
    
<option value='0|0|0' selected >-SELECCIONE-</option>
	 
<?php 
	$n=0;
$datos = "SELECT *FROM detracciones ORDER BY id ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['id']?>|<?=$dato['codigo']?>|<?=$dato['porcentaje']?>' ><?=$dato['codigo']?> (<?=$dato['porcentaje']?>%) <?=$dato['nombre']?></option>
<?php } ?>
</select>
</div>

</div>                            

<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<div class="form-group">
<label>MONTO:</label>
<input type="text" id="montodetraccion" name="montodetraccion" readonly class="form-control" value="0.00"/>
</div>
</div>
	
	                              
<div class="col-xs-12" style="margin-top: -10px;" >
<div class="row">
	
	
<div class=" col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>RETENCIONES:</label>
<div class="form-group">
<select name ="operacionretenciones" id="operacionretenciones"  onChange="totales()" class="form-control"  >
<option value="0" selected="selected">-SELECCIONE-</option>
<option value="1">RETENCIÓN 3%</option>
</select>
</div>
</div>

<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<div class="form-group">
<label>MONTO:</label>
<input name="retencion" type="text" id="retencion" value="0.00" class="form-control" readonly />
</div>
</div>

<div class=" col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>PERCEPCIONES:</label>
<div class="form-group">
<select name ="percepcionsi" id="percepcionsi"  onChange="totales()" class="form-control"  >
<option value="0" selected="selected">-SELECCIONE-</option>
<option value="1">PERCEPCION 2%</option>
</select>
</div>
</div>

<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<div class="form-group">
<label>MONTO:</label>
<input name="percepcion" type="text" id="percepcion" value="0.00" class="form-control" readonly />
</div>
</div>
	
	
	
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<div class="row">
<div class="form-group">
<label>ANTICIPOS:</label>
<div class="input-group">
<input type="hidden" id="idanticipo" name="idanticipo" readonly class="form-control" value="0" />
<input type="text" id="serieanticipo" name="serieanticipo" readonly class="form-control" value="" />
      <span class="input-group-btn oculto">
<button class="btn  btn-danger" id="anticipo" onClick="listardoc2()" type="button"><span class="glyphicon glyphicon-plus"></span></button>
      </span>
    </div>
</div>
</div>
</div>







<div class="col-lg-1 col-md-2 col-sm-4 col-xs-6" >	
<label>Descontar stock:</label>
<div class="form-group">
<select name ="kardex" id="kardex" class="form-control input-sm" style="max-width:90px;" >
<option value='0' selected >SI</option>
<option value='1' >NO</option>
</select>
</div>	
</div>






</div>
</div>

		
</div></div>		
	
		
    </div>



<div id="menu2" class="tab-pane fade">

		
<div class="col-xs-12">
<div class=" col-lg-2">
<div class="form-group">
<label>N° Guía 2:</label>


                    <div class="input-group">
                        <div class="input-group-btn">
                            <div class="btn-group">
<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                    <span id="nombreguia2" >REMIT</span> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">								
                                    <li><a href="javascript:void(0)" onclick="tipoguia('09', 'REMIT', '2')" >REMIT</a></li>
                                    <li><a href="javascript:void(0)" onclick="tipoguia('31', 'TRANS', '2')" >TRANS</a></li>
                                </ul>
                            </div>
                        </div>
<input type="text" id="guia2" name="guia2" class="form-control" value="" />

</div>
</div>	
</div>
	
<div class=" col-lg-2">
<div class="form-group">
<label>N° Guía 3:</label>


                    <div class="input-group">
                        <div class="input-group-btn">
                            <div class="btn-group">
<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                    <span id="nombreguia3" >REMIT</span> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">								
                                    <li><a href="javascript:void(0)" onclick="tipoguia('09', 'REMIT', '3')" >REMIT</a></li>
                                    <li><a href="javascript:void(0)" onclick="tipoguia('31', 'TRANS', '3')" >TRANS</a></li>
                                </ul>
                            </div>
                        </div>
<input type="text" id="guia3" name="guia3" class="form-control" value="" />

</div>
</div>	
</div>

<div class=" col-lg-2">
<div class="form-group">
<label>N° Guía 4:</label>


                    <div class="input-group">
                        <div class="input-group-btn">
                            <div class="btn-group">
<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                    <span id="nombreguia4" >REMIT</span> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">								
                                    <li><a href="javascript:void(0)" onclick="tipoguia('09', 'REMIT', '4')" >REMIT</a></li>
                                    <li><a href="javascript:void(0)" onclick="tipoguia('31', 'TRANS', '4')" >TRANS</a></li>
                                </ul>
                            </div>
                        </div>
<input type="text" id="guia4" name="guia4" class="form-control" value="" />

</div>
</div>
</div>	
	


<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="margin-left: 0%; padding-left: 0px;">
<div class="form-group">
<label>CATEGORÍAS:</label>
<select id="categoria" name="categoria" class="form-control" />
<option value='0' selected >-NINGUNO-</option>
<?php 
	$n=0;
$datos = "SELECT *FROM categoria WHERE idempresa='$_COOKIE[id]' AND nivel='0' ORDER BY nombre ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['idcategoria']?>' ><?=$dato['nombre']?></option>
<?php } ?>
</select>
</div>	
</div>
	
<div class=" col-lg-2">	
<div class="form-group">
<label>BOTON:</label>
<button type="button" class="btn btn-ddefault" onClick="listaimport()" ><i class="fa fa-search"></i> IMPORTAR PLANTILLA</button>
</div>	
</div>

		
</div>		
	
		
    </div>


	
	

<div id="menu3" class="tab-pane fade">


<div class="col-xs-12">
<div class="row">
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="form-group">
<label>VENTA DE EXPORTACIÓN?:</label>
<div class="form-group">
<select name ="exportacion" id="exportacion" class="form-control" >
<option value='NO' selected >NO</option>
<option value='SI' >SI</option>
</select>
</div>
</div>

</div>
</div>

        </div>
    </div>

					
					
<div id="menu4" class="tab-pane fade">


        <div class="col-xs-12">
<div class="row">


<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<div class="form-group">
<label>ID:</label>
<input name="mach_id" type="text" id="mach_id" value="" class="form-control" />
</div>
</div>
	

<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<div class="form-group">
<label>N° MACH:</label>
<input name="mach_numero" type="text" id="mach_numero" value="" class="form-control" />
</div>
</div>

<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<div class="form-group">
<label>MACH MONTO:</label>
<input name="mach_monto" type="text" id="mach_monto" value="0.00" class="form-control" />
</div>
</div>

<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<div class="form-group">
<label>MACH FECHA:</label>
<input name="mach_fecha" type="date" id="mach_fecha" value="<?php echo date("Y-m-d"); ?>" class="form-control" />
</div>
</div>

<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
<div class="form-group">
<label>MACH OBSERVACIONES:</label>
<input name="mach_observaciones" type="text" id="mach_observaciones" value="" class="form-control" />
</div>
</div>

				
</div>

        </div>
    </div>
					
					
					
					
<!-- DETALLE FACTURACION --> 
<div class="row">
<div class="col-sm-12 borde" style="border-top:0px;">

                                    <div class="col-sm-12">
										
<div class="table-responsive" >										
<table class="table tablapedido responsive nowrap"  style="width:100%" id="detpedidos">
<thead>
<tr>
<th>#</th>
<th>Descripción</th>
<th>U/M</th>
<th>Cti</th>
<th>P/U</th>

<th>DSC</th>
<th>S.tot</th>
<th>IGV</th>
<th>Tot</th>
<th></th>
</tr>
</thead>
<tbody></tbody>
</table>
</div>
                                    </div>
                        
                            </div>
                            <!-- FIN DETALLE FACTURACION -->  

                            <div class="col-sm-12 borde" style="border-top:0px;">
                               <div class="row">
    
                                    <br>
                                    <div class="col-sm-6">
                                        <div class="col-sm-12">Observacion</div>
                                        <div class="col-sm-12">
                                            <textarea  name="txtOBSERVACION" id="txtOBSERVACION" rows="4" class="form-control"></textarea>
                                        </div>
                                    </div>

<input name="tarjeta" type=hidden id="tarjeta" value="0.00" />

                                  <div class="col-sm-6">
                                 <div class="row">      
       <div class="col-sm-6 col-xs-6">  
<div class="col-sm-4">Gravadas</div>
<div class="col-sm-8 p-b-1">
<input type="text" id="gravadas" name="gravadas" class="form-control  form-control-sm" disabled style="height: 20px;" value="0.00" readonly />
</div>
                                                                                        
<div class="col-sm-4">SubTotal</div>
<div class="col-sm-8 p-b-1">
<input type="text" id="txtSUB_TOTAL" name="txtSUB_TOTAL" class="form-control  form-control-sm" style="height: 20px;" value="0.00" readonly />
</div>

<div class="col-sm-4 p-b-1">Gratuitas</div>
<div class="col-sm-8">                                                        
<input type="text" id="gratuita" name="gratuita" value="0.00" style="height: 20px;" class="form-control form-control-sm"  readonly />
</div>                                   
<div class="col-sm-4 p-b-1">Exonerada</div>
<div class="col-sm-8">                                                        
<input type="text" id="exoneradof" name="exoneradof" value="0.00" style="height: 20px;" class="form-control form-control-sm"  readonly />
</div>  
      
<div class="col-sm-4 p-b-1">Inafecta</div>
<div class="col-sm-8">                                                        
<input type="text" id="inafecta" name="inafecta" value="0.00" style="height: 20px;" class="form-control form-control-sm"  readonly />
</div>  

<div class="col-sm-4 p-b-1">IGV(18)%<input name="txtPORCENTAJE_IGV" type="hidden" id="txtPORCENTAJE_IGV" value="0.00" class="form-control"  readonly /></div>
<div class="col-sm-8">                                                        
<input type="text" id="txtIGV" name="txtIGV" value=""  style="height: 20px;" value="0.00" class="form-control" readonly />
</div>
 
<div class="col-sm-4">Total</div>
<div class="col-sm-8">
<input name="txtTOTAL" type="text" id="txtTOTAL" style="height: 20px;" value="0.00" class="form-control" readonly />
</div>

<div class="col-sm-4">Val.Ref</div>
<div class="col-sm-8">
<input name="valref" type="text" id="valref" style="height: 20px;" value="0.00" class="form-control" readonly />
</div>	

</div>
 <div class="col-sm-6 col-xs-6">

	 
	 
<div class="col-sm-4">PUNTOS</div>
<div class="col-sm-8">
<input name="puntos" type="text" id="puntos" value="0.00" style="height: 20px; text-align: right;" class="form-control" readonly />
</div>

<div class="col-sm-4">Comisión</div>
<div class="col-sm-8">
<input type="text"  name="comisiont" id="comisiont" value="0.00" style="height: 20px;" class="form-control" readonly />
</div>
	 
<div class="col-sm-4">Descuento</div>
<div class="col-sm-8">
<input type="text"  name="descuentotot" id="descuentotot" value="0.00" style="height: 20px;" class="form-control" readonly />
</div>
	 
<div class="col-sm-4">Tot/Anticipo</div>
<div class="col-sm-8">
<input type="text"  name="totanticipo" id="totanticipo" value="0.00" style="height: 20px;" class="form-control" readonly />
</div>
	 
<div class="col-sm-4">Tot/Saldo</div>
<div class="col-sm-8">
<input type="text"  name="totsaldo" id="totsaldo" value="0.00" style="height: 20px;" class="form-control" readonly />
</div>
	 
<div class="col-sm-4">Tot/Pagar</div>
<div class="col-sm-8">
<input type="text"  name="totpagar" id="totpagar" value="0.00" style="height: 20px;" class="form-control" readonly />
</div>
                                        
                                        
                                  </div>
                                    
                            </div>        
                                 
                        </div>             
                                    
                                    
                  
                            </div>
                            </div>
                            
<div class="col-sm-12 borde  oculto" style="border-top:0px;">
<hr>
                            <fieldset>
<button type="button" class="btn btn-default" onClick="BuscarArticulo()" id="agregar" ><i class="fa fa-plus-square-o"></i> Agregar Prod</button>

<button type="button" class="btn btn-default" onClick="Buscarserv()" id="agregars" ><i class="fa fa-plus-square-o"></i> Agregar Serv</button>
						
								
<button type="button" class="btn btn-default guardaventa" onClick="guardarventa(2)" ><i class="fa fa-save"></i> Guardar</button>
<button type="button" class="btn btn-default guardaventa" onClick="guardarventa(1)" ><i class="fa fa-save"></i> Guardar Env.</button>
								
<button type="button" class="btn btn-default" id="imprimir" onClick="printPdf(1)">
<i class="fa fa-print"></i> Imprimir</button>
								
<!--<button type="button" class="btn btn-default" id="imprimirb" onClick="printPdf(2)">
<i class="fa fa-print"></i> Impr Botica</button>-->

<button type="button" class="btn btn-default" id="imprimirt" onclick="llenaimpresion()">
<i class="fa fa-print"></i> Impr Ticket</button>

<button type="button" class="btn btn-danger" onClick="nuevo()"><i class="fa fa-file-o"></i> Nuevo</button>

</fieldset>
<hr>
</div>
</div>	
	
	
	
	
</form>	
	
<!--
FIN DEL FORMULARIO
--!-->	
	
                        
                    </div>
				
				
</div>
		
			</div>
		
		</div>


		
		<br />


<?php } require'includes/pie.php'; ?>



  <!-- Modal -->
  <div class="modal fade" id="cliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered modal-sm modal-pagodatos" role="document">
      <div class="modal-content">

                        <div Class="modal-header">
                           


                            <Button type="button" Class='close' data-dismiss="modal">×</Button>
                            <h4 Class='modal-title' id='myModalLabel'>CLIENTES</h4>
                        </div>

            <div Class='modal-body'>
                <div class="row" >




                    <div class="col-lg-12">

                        <form name="formularioc" id="formularioc" method="POST">




                            <div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-8">
                                <label>Tipo Documento:</label>
                                <select class="form-control select-picker" name="tipo_documento" id="tipo_documento" required>
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>

                                    <option value="NO DOMICILIADO">NO DOMICILIADO</option>
                                    <option value="4">CARNET DE EXTRANGERÍA</option>
                                    <option value="7">PASAPORTE</option>
                                    <option value="OTROS">OTROS</option>
                                </select>
                            </div>

                            <div class="form-group col-lg-4 col-md-6 col-sm-8 col-xs-12">
                                <label>Número Documento:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="numerodocumento" id="numerodocumento" placeholder="Documento">
                                    <div class="input-group-btn"><button type="button" onClick="buscarcontribuyente()" class="btn btn-danger">Buscar</button></div>
                                </div>
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-8">
                                <label>Edad:</label>
                                <input type="text" class="form-control" name="edad" id="edad" placeholder="Edad">
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-8">
                                <div class="input-group">
                                    <input type="text"   class="panel panel-success" name="estado" id="estado" readonly class="form-control" placeholder="Estado">
                                    <input type="text"   class="panel panel-success" name="condicion" id="condicion" readonly class="form-control" placeholder="Condicion">
                                </div>
                            </div>

                            <div class="form-group col-lg-6 col-md-6 col-sm-8 col-xs-12">
                                <label>Nombre/Razón social:</label>
                                <input type="hidden" name="idpersona" id="idpersona">
                                <input type="hidden" name="tipo_persona" id="tipo_persona" value="Cliente">
                                <input type="hidden" name="codigo" id="codigo" >
                                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" placeholder="Nombre/Razon social" required>
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">

                                <label>Pais:</label>


                          <select  class="form-control selectpicker" data-live-search="true"  id="pais" name="pais">
                              <option value="AF">Afganist&#225;n</option>
                              <option value="AL">Albania</option>
                              <option value="DE">Alemania</option>
                              <option value="AD">Andorra</option>
                              <option value="AO">Angola</option>
                              <option value="AI">Anguila</option>
                              <option value="AQ">Ant&#225;rtida</option>
                              <option value="AG">Antigua y Barbuda</option>
                              <option value="AN">Antillas Neerlandesas</option>
                              <option value="SA">Arabia Saud&#237;</option>
                              <option value="DZ">Argelia</option>
                              <option value="AR">Argentina</option>
                              <option value="AM">Armenia</option>
                              <option value="AW">Aruba</option>
                              <option value="AU">Australia</option>
                              <option value="AT">Austria</option>
                              <option value="BS">Bahamas</option>
                              <option value="BH">Bahr&#233;in</option>
                              <option value="BD">Bangladesh</option>
                              <option value="BB">Barbados</option>
                              <option value="BE">B&#233;lgica</option>
                              <option value="BZ">Belice</option>
                              <option value="BJ">Ben&#237;n</option>
                              <option value="BM">Bermudas</option>
                              <option value="BY">Bielorrusia</option>
                              <option value="BO">Bolivia</option>
                              <option value="BA">Bosnia y Herzegovina</option>
                              <option value="BW">Botsuana</option>
                              <option value="BR">Brasil</option>
                              <option value="BN">Brun&#233;i Darussalam</option>
                              <option value="BG">Bulgaria</option>
                              <option value="BF">Burkina Faso</option>
                              <option value="BI">Burundi</option>
                              <option value="BT">But&#225;n</option>
                              <option value="CV">Cabo Verde</option>
                              <option value="KH">Camboya</option>
                              <option value="CM">Camer&#250;n</option>
                              <option value="CA">Canad&#225;</option>
                              <option value="BQ">Caribe Neerland&#233;s</option>
                              <option value="TD">Chad</option>
                              <option value="CL">Chile</option>
                              <option value="CN">China</option>
                              <option value="CY">Chipre</option>
                              <option value="CO">Colombia</option>
                              <option value="KM">Comoras</option>
                              <option value="CG">Congo</option>
                              <option value="KR">Corea (Rep&#250;blica de)</option>
                              <option value="KP">Corea (Rep&#250;blica Popular Democr&#225;tica)</option>
                              <option value="CI">Costa de Marfil</option>
                              <option value="CR">Costa Rica</option>
                              <option value="HR">Croacia</option>
                              <option value="CU">Cuba</option>
                              <option value="CW">Curazao</option>
                              <option value="DK">Dinamarca</option>
                              <option value="DJ">Djibouti</option>
                              <option value="DM">Dominica</option>
                              <option value="EC">Ecuador</option>
                              <option value="US">EE.UU.</option>
                              <option value="EG">Egipto</option>
                              <option value="SV">El Salvador</option>
                              <option value="ER">Eritrea</option>
                              <option value="SK">Eslovaquia</option>
                              <option value="SI">Eslovenia</option>
                              <option value="ES">Espa&#241;a</option>
                              <option value="EE">Estonia</option>
                              <option value="ET">Etiop&#237;a</option>
                              <option value="FJ">Fiji</option>
                              <option value="PH">Filipinas</option>
                              <option value="FI">Finlandia</option>
                              <option value="FR">Francia</option>
                              <option value="GM">Gambia</option>
                              <option value="GE">Georgia</option>
                              <option value="GH">Ghana</option>
                              <option value="GI">Gibraltar</option>
                              <option value="GD">Granada</option>
                              <option value="GR">Grecia</option>
                              <option value="GL">Groenlandia</option>
                              <option value="GP">Guadalupe</option>
                              <option value="GU">Guam</option>
                              <option value="GT">Guatemala</option>
                              <option value="GF">Guayana Francesa</option>
                              <option value="GN">Guinea</option>
                              <option value="GQ">Guinea Ecuatorial</option>
                              <option value="GW">Guinea-Bissau</option>
                              <option value="GY">Guyana</option>
                              <option value="HT">Hait&#237;</option>
                              <option value="HN">Honduras</option>
                              <option value="HK">Hongkong, China</option>
                              <option value="HU">Hungr&#237;a</option>
                              <option value="IN">India</option>
                              <option value="ID">Indonesia</option>
                              <option value="IQ">Irak</option>
                              <option value="IR">Ir&#225;n</option>
                              <option value="IE">Irlanda</option>
                              <option value="IS">Islandia</option>
                              <option value="KY">Islas Caim&#225;n</option>
                              <option value="CK">Islas Cook</option>
                              <option value="FO">Islas Feroe</option>
                              <option value="FK">Islas Malvinas</option>
                              <option value="MP">Islas Marianas del Norte</option>
                              <option value="MH">Islas Marshall</option>
                              <option value="SB">Islas Salom&#243;n</option>
                              <option value="VG">Islas V&#237;rgenes Brit&#225;nicas</option>
                              <option value="VI">Islas V&#237;rgenes de los Estados Unidos</option>
                              <option value="IL">Israel</option>
                              <option value="IT">Italia</option>
                              <option value="JM">Jamaica</option>
                              <option value="JP">Jap&#243;n</option>
                              <option value="JO">Jordania</option>
                              <option value="KZ">Kazajist&#225;n</option>
                              <option value="KE">Kenia</option>
                              <option value="KG">Kirguizist&#225;n</option>
                              <option value="KI">Kiribati</option>
                              <option value="KW">Kuwait</option>
                              <option value="LA">Laos</option>
                              <option value="LS">Lesoto</option>
                              <option value="LV">Letonia</option>
                              <option value="LB">L&#237;bano</option>
                              <option value="LR">Liberia</option>
                              <option value="LY">Libia</option>
                              <option value="LI">Liechtenstein</option>
                              <option value="LT">Lituania</option>
                              <option value="LU">Luxemburgo</option>
                              <option value="MO">Macao, China</option>
                              <option value="MK">Macedonia</option>
                              <option value="MG">Madagascar</option>
                              <option value="MY">Malasia</option>
                              <option value="MW">Malawi</option>
                              <option value="MV">Maldivas</option>
                              <option value="ML">Mal&#237;</option>
                              <option value="MT">Malta</option>
                              <option value="MA">Marruecos</option>
                              <option value="MQ">Martinica</option>
                              <option value="MU">Mauricio</option>
                              <option value="MR">Mauritania</option>
                              <option value="YT">Mayotte</option>
                              <option value="MX">M&#233;xico</option>
                              <option value="FM">Micronesia</option>
                              <option value="MD">Moldavia</option>
                              <option value="MC">M&#243;naco</option>
                              <option value="MN">Mongolia</option>
                              <option value="ME">Montenegro</option>
                              <option value="MS">Montserrat</option>
                              <option value="MZ">Mozambique</option>
                              <option value="MM">Myanmar</option>
                              <option value="NA">Namibia</option>
                              <option value="NR">Nauru</option>
                              <option value="NP">Nepal</option>
                              <option value="NI">Nicaragua</option>
                              <option value="NE">N&#237;ger</option>
                              <option value="NG">Nigeria</option>
                              <option value="NU">Niue</option>
                              <option value="NO">Noruega</option>
                              <option value="NC">Nueva Caledonia</option>
                              <option value="NZ">Nueva Zelanda</option>
                              <option value="OM">Om&#225;n</option>
                              <option value="NL">Pa&#237;ses Bajos</option>
                              <option value="PK">Pakist&#225;n</option>
                              <option value="PW">Palaos</option>
                              <option value="PS">Palestina</option>
                              <option value="PA">Panam&#225;</option>
                              <option value="PG">Pap&#250;a Nueva Guinea</option>
                              <option value="PY">Paraguay</option>
                              <option value="PE" selected="selected" >Per&#250;</option>
                              <option value="PF">Polinesia Francesa</option>
                              <option value="PL">Polonia</option>
                              <option value="PT">Portugal</option>
                              <option value="PR">Puerto Rico</option>
                              <option value="QA">Qatar</option>
                              <option value="GB">Reino Unido</option>
                              <option value="CF">Rep&#250;blica Centroafricana</option>
                              <option value="CZ">Rep&#250;blica Checa</option>
                              <option value="AZ">Rep&#250;blica de Azerbaiy&#225;n</option>
                              <option value="CD">Rep&#250;blica Democr&#225;tica del Congo</option>
                              <option value="DO">Rep&#250;blica Dominicana</option>
                              <option value="GA">Rep&#250;blica Gabonesa</option>
                              <option value="RE">Reuni&#243;n</option>
                              <option value="RW">Ruanda</option>
                              <option value="RO">Ruman&#237;a</option>
                              <option value="RU">Rusia</option>
                              <option value="WS">Samoa</option>
                              <option value="AS">Samoa Americana</option>
                              <option value="KN">San Crist&#243;bal y Nieves</option>
                              <option value="SM">San Marino</option>
                              <option value="PM">San Pedro y Miquel&#243;n</option>
                              <option value="VC">San Vicente y las Granadinas</option>
                              <option value="SH">Santa Elena</option>
                              <option value="LC">Santa Luc&#237;a</option>
                              <option value="ST">Santo Tom&#233; y Pr&#237;ncipe</option>
                              <option value="SN">Senegal</option>
                              <option value="RS">Serbia</option>
                              <option value="SC">Seychelles</option>
                              <option value="SL">Sierra Leona</option>
                              <option value="SG">Singapur</option>
                              <option value="SX">Sint Maarten</option>
                              <option value="SY">Siria</option>
                              <option value="SO">Somalia</option>
                              <option value="LK">Sri Lanka</option>
                              <option value="SZ">Suazilandia</option>
                              <option value="ZA">Sud&#225;frica</option>
                              <option value="SD">Sud&#225;n</option>
                              <option value="SS">Sud&#225;n del Sur</option>
                              <option value="SE">Suecia</option>
                              <option value="CH">Suiza</option>
                              <option value="SR">Surinam</option>
                              <option value="TH">Tailandia</option>
                              <option value="TW">Taiw&#225;n</option>
                              <option value="TZ">Tanzania</option>
                              <option value="TJ">Tayikist&#225;n</option>
                              <option value="TL">Timor Oriental</option>
                              <option value="TG">Togo</option>
                              <option value="TK">Tokelau</option>
                              <option value="TO">Tonga</option>
                              <option value="TT">Trinidad y Tobago</option>
                              <option value="TN">T&#250;nez</option>
                              <option value="TC">Turcas y Caicos</option>
                              <option value="TM">Turkmenist&#225;n</option>
                              <option value="TR">Turqu&#237;a</option>
                              <option value="TV">Tuvalu</option>
                              <option value="AE">UAE</option>
                              <option value="UA">Ucrania</option>
                              <option value="UG">Uganda</option>
                              <option value="UY">Uruguay</option>
                              <option value="UZ">Uzbekist&#225;n</option>
                              <option value="VU">Vanuatu</option>
                              <option value="VA">Vaticano</option>
                              <option value="VE">Venezuela</option>
                              <option value="VN">Vietnam</option>
                              <option value="WF">Wallis y Futuna</option>
                              <option value="YE">Yemen</option>
                              <option value="ZM">Zambia</option>
                              <option value="ZW">Zimbabue</option>
                          </select>
                      </div>

                      <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
<label>Dirección:</label>
<div class="input-group">
<input type="text" class="form-control" name="direccion" id="direccion" maxlength="70" placeholder="Dirección">
<div class="input-group-btn">
<button type="button" onClick="buscaru();" id="search" class="btn btn-danger"><span class="glyphicon glyphicon-search"></span> Buscar</button>
<button type="button" id="mostrar" class="btn btn-default"><span class="glyphicon glyphicon-eye-open"></span> Mostrar Mapa</button>
<button type="button" id="ocultar" class="btn btn-default"><span class="glyphicon glyphicon-eye-close"></span> Ocultar Mapa</button>
</div>
</div>
</div>
							 
<div class="col-sm-12" id="target" style="display: none;" >					 
<div class="row">							 
							 
 <div class="col-sm-12">                         
<div class="col-sm-12 m-b-1 alert alert-danger">
<strong>IMPORTANTE!</strong> arrastre el marker en la ubicación de su vivienda.
</div>
</div>
	
<div class="form-group">
<label class="control-label col-sm-4" >Geolocalización*</label>
<div class="col-sm-4">
<input class="form-control" value="" type="text" readonly name="latitud" id="latitud" />
</div>

<div class="col-sm-4">
<input class="form-control" value="" type="text" readonly name="longitud" id="longitud" />
</div>

</div>
                          
<div class="form-group">
<div class="col-sm-12">
<br>
<div id='map_canvas' style="width:100%; height:400px;"></div>
<br>
</div>
</div>                       
</div> 
</div>  
                         
<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Sector(*):</label>
<select id="idcategoria" name="idcategoria" class="form-control selectpicker" data-live-search="true" required></select>
</div>
                          
                          
                          <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label>Teléfono:</label>
                            <input type="text" class="form-control" name="telefono" id="telefono" maxlength="20" placeholder="Teléfono">
                          </div>
							 
							 
                          <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label>Email:</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                          </div>
							 
                          <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label>Email2:</label>
                            <input type="email2" class="form-control" name="email" id="email2" placeholder="Email">
                          </div>
							 
                          <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <label>Nombre Comercial:</label>
                            <input type="text" class="form-control" name="txtRAZON_SOCIAL" id="txtRAZON_SOCIAL"  placeholder="Razón Social">
                          </div>
                          
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label>Descuento en (%):</label>
<input type="text" class="form-control" name="descuento" id="descuento" value="0" placeholder="Descuento en (%)">
                          </div>
                          
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label>Descuento Mayor:</label>

<select class="form-control select-picker" name="descuentomayor" id="descuentomayor" required>
                              <option value="NO" selected="selected">NO</option>
                              <option value="SI">SI</option>
                            </select>
                         
                          </div>

                          <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <button class="btn btn-primary" type="button" id="btnGuardarc" onClick="guardarcli()" ><i class="fa fa-save"></i> Guardar</button>


                          </div>
                        </form>
                   </div></div>
</div>
                                     

                        </div>


    </div>
  </div>  
  </div>
  <!-- Fin modal -->
  

 


<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog"   aria-labelledby="myModal"  aria-hidden="true"  style="z-index: 10600" style="overflow-y: scroll;" >
    <div class="modal-dialog"  style="width: 80%!important; z-index: 10500" >
      <div class="modal-content">

                        <div Class="modal-header">
                           

                           
                            <Button type="button" Class='close' data-dismiss="modal">×</Button>
                            <h4 Class='modal-title' id='myModalLabel'>ARTICULOS</h4>
                        </div>

                        <div Class='modal-body table-responsive'>
 <div class="row" >                        

<input type='hidden' id='tipocambio' name='tipocambio' value='<?=$tchoy['venta']?>' />                          
<input type='hidden' id='descuento' name='descuento' value='0.00' /> 
<input type='hidden' id='descuentom' name='descuentom' value='NO' /> 

<div class="col-sm-12" >
    
<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">

<div class="input-group">

<select name="constock" id="constock" class="form-control" >
<option value="TODO" selected="selected">LISTAR TODO</option>
<option value="STOCK">CON STOCK</option>
</select>

<span class="input-group-btn">
<button class="btn  btn-danger" onclick="listarArticulos()" type="button"><span class="glyphicon glyphicon-search"></span>
</button>
</span>
</div>
</div>


<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">

</div>

</div>
<div class="col-sm-12" style=" margin-top: -30px;">
<div class="table-responsive" >
<table id="tblarticulos" class="table  table-condensed  compact" style="width: 100%">
<thead>
<th style="width: 1%">U/Medida</th>
<th style="width: 1%">Marca</th>
<th style="width: 5%">Artículo</th>
<th style="width: 5%">Lote/Serie</th>
<th style="width: 5%">P/U</th>
<th style="width: 5%">V/U</th>
<th style="width: 5%">P.Comp</th>
<th style="width: 3%">Cti</th>
<th style="width: 1%">stock</th>
<th style="width: 5%">IGV</th>
<th style="width: 1%">LAB</th>
<th style="width: 5%">-</th>
</thead>
<tbody></tbody>
</table>
</div>
</div>
</div>

</div>


    </div>
  </div>  
  </div>
  <!-- Fin modal -->








<!-- Modal -->
  <div class="modal fade" id="modalstock"  aria-hidden="true" aria-labelledby="modalstock" tabindex="-1" role="dialog"  aria-hidden="true" style="z-index: 10900" >
    <div class="modal-dialog modal-dialog-centered"  style="width: 50%!important; z-index: 10850" >
      <div class="modal-content">

<div Class="modal-header">
<Button type="button" Class='close' data-dismiss="modal">×</Button>
<h4 Class='modal-title' id='titulostock'>TARJETAS DE PROPIEDAD</h4>
</div>
<div Class='modal-body table-responsive'>
<div class="row" >                        

<div class="col-sm-12" >
<div class="table-responsive" >	  

<table id="tblstock" class="table  table-condensed  compact" style="width: 100%">
<thead>
<th style="width: 5%">#</th>
<th style="width: 70%">Sucursal</th>
<th style="width: 10%">Stock</th>
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
  </div>
  <!-- Fin modal -->




<!-- Modal -->
<div class="modal fade" id="modalstock2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog"  style="width: 60%!important;" >
      <div class="modal-content">

<div Class="modal-header">
<button type="button" Class='close' data-dismiss="modal">×</button>
<h4 Class='modal-title' >ARTICLOS</h4>
</div>

<div Class='modal-body table-responsive'>
<div class="row" >                        

<div class="col-sm-12" style=" margin-top: -30px;">
<div class="table-responsive" >	  


</div>
</div>
</div>


</div>


    </div>
  </div>  
  </div>
  <!-- Fin modal -->


  
  
  
    <!-- Modal -->
  <div class="modal fade" id="mcredito" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">

                        <div Class="modal-header">
                            <Button type="button" Class='close' data-dismiss="modal">×</Button>
                            <h4 Class='modal-title' id='myModalLabel'>DOCUMENTO REFRERENCIA</h4>
                        </div>

                        <div Class='modal-body'>
                            <div class="panel-body">
<div class="col-sm-12" style=" margin-top: -3%;">                
<table id="tblcredito" class="table table-striped table-bordered table-condensed table-hover" style="width: 100%;">
            <thead>
<th>Opc</th>
                <th>Serie</th>
                <th>Número</th>
                <th>Fecha</th>
                <th>Estado</th>
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
  
  
<!-- REVISAR ESTADO DE FACTURA -->
  <div class="modal fade" id="modalfactura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">

<div Class="modal-header">
<Button type="button" Class='close' data-dismiss="modal">×</Button>
<h4 Class='modal-title' id='myModalLabel'>REVISAR DOCUMENTO</h4>
</div>

                        <div Class='modal-body'>
 <div class="row" >                        
<input type='hidden' id='idventarevisar' name='idventarevisar' />
<div class="col-sm-12" style="border-top:0px;" id="descuento">&nbsp;</div> 
                           


<div class="col-sm-12" style=" margin-top: -20px;">

<input type='hidden' Class='form-control' value='CapturaCriterioValidez' name='txtaccion' id='txtaccion' />
<input type='hidden' id='idcomp' name='idcomp' />
<input type='hidden' id='pagot' name='pagot' />
<div class="col-sm-8" >
	

	
	
<div class="row" >
<div class="col-sm-12" id="mensajefact" >
<!--<div class="col-sm-12" id="respuestasunat" >-->
<div class="col-sm-12" id="datosdoc" ></div>
<br><br>
<div class="col-sm-8  alert alert-success" >Estado del comprobante a la fecha de la consulta</div><div class="col-sm-4 alert alert-default" id="resEstado" ></div>
<div class="col-sm-8 alert alert-success" >Estado del contribuyente a la fecha de emisión</div><div class="col-sm-4 alert alert-default" id="resEstadoRuc" ></div>
<div class="col-sm-8  alert alert-success" >Condición de domicilio a la fecha de emisión:</div><div class="col-sm-4 alert alert-default" id="resCondicion" ></div>

<div class="col-sm-12 alert alert-danger" id="divObservaciones" ></div>
    <div class="col-sm-12 alert alert-success" id="respuestasunat" >
</div>
	
</div>

</div>
	
</div>

<div class="col-sm-4 pull-xs-right text-right" style="background-color:#CFCFCF; padding-top: 2%; padding-bottom: 2%; "> 

<button type="button" class="btn btn-success form-control text-right" onClick="cambiaestado('2')" ><i class="fa fa-check"></i> ACEPTADO</button>
<br><br>
<button type="button" class="btn btn-success form-control text-right" onClick="cambiaestado('6')" ><i class="fa fa-check"></i> ANULACIÓN ACEPTADA</button>
<br><br>
<button type="button" class="btn btn-danger form-control text-right" onClick="cambiaestado('7')" ><i class="fa fa-remove"></i> RECHAZADO</button>
<br><br>
<button type="button" class="btn btn-danger form-control text-right" onClick="reenviarf()" ><i class="fa fa-cloud-upload"></i> REENVIAR</button>
	
<br><br>
<button type="button" class="btn btn-default form-control text-right" onClick="traercdr()" ><i class="fa fa-refresh"></i> TRAER CDR</button>
    <br><br>
    <br><br>

<button type="button" class="btn btn-secondary form-control text-right" style="background-color:#CFCFCF; padding-top: 2%; padding-bottom: 2%; " onClick="cambiaestado('0')" ><i ></i></button>


</div>


<br>

</div>
</div>
                                     

                        </div>


    </div>
  </div>  
  </div>
  <!-- Fin modal -->




<!-- Modal -->
<div class="modal fade" id="pagof" tabindex="-1" role="dialog"  data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">

                        <div Class="modal-header">
 
                            <h4 Class='modal-title' id='myModalLabel'>PAGO VENTA</h4>
                        </div>

                        <div Class='modal-body'>

 <div class="row" >                        
<div class="col-lg-12"> 
 
                         <form name="formularioc" id="formularioc" method="POST">
   
<input type="hidden" name="iddoc" id="iddoc">
<input name="idventapago" type="hidden" id="idventapago" >
<input name="marcaenviado" type="hidden" id="marcaenviado" >
<input name="nivelpago" type="hidden" id="nivelpago" value="0" >
							 
<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
<label>Fecha:</label>
<input name="fechapago" type="date" required class="form-control input-sm" id="fechapago" value="<?php echo date("Y-m-d"); ?>">
</div>
							 
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Moneda:</label>
<select name="monedapago" id="monedapago" class="form-control" />
<option value="PEN" selected="selected">SOLES</option>
<option value="USD">DOLARES</option>
</select>
</div>
							 
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>T/C:</label>
<input name="tcambio" type="text" class="form-control" id="tcambio" >
</div>
                          
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>T.Pago:</label>
 <select class="form-control" name="tpago" id="tpago" required>
                             
<?php 
	$n=0;
$datos = "SELECT *FROM caja_tipopago WHERE idempresa='$_COOKIE[id]' AND estado='1' ORDER BY id ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['id']?>' ><?=$dato['descripcion']?></option>
<?php } ?>
</select>
</div>
							 
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 ">
<label>Tipo Pago:</label>
<select name="tipopago" id="tipopago" class="form-control" />
<option value="0" selected="selected">NORMAL</option>
<option value="1">DETRACCIÓN</option>
<option value="2">RETENCION</option>
</select>
</div>
							 
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Por Periodo:</label>
<select name="properiodo" id="properiodo" class="form-control" onChange="mostrarpago()" />
<option value="SI" selected="selected">SI</option>
<option value="NO">NO</option>
</select>
</div>

<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 periodos ">
<label>Periodo:</label>
<select name="periodo" id="periodo" class="form-control" />
<option value="60" >60 DÍAS</option>
<option value="90" >90 DÍAS</option>
<option value="45" >45 DÍAS</option>
<option value="30" >30 DÍAS</option>
<option value="MENSUAL" selected="selected">MENSUAL</option>
<option value="QUINCENAL">QUINCENAL</option>
<option value="SEMANAL">SEMANAL</option>
</select>
</div>
	 
	 
	
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 periodos">
<label>Letras:</label>
<select name="letras" id="letras" class="form-control" />
<option value="1">1</option>
<option value="2" selected="selected">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
</select>
</div>
                         
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 manual ">
<label>Monto pagar:</label>
<input name="montopago" type="text" id="montopago"  class="form-control" value="0.00" >	
</div>
							 
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 manual ">
<label>N° OPER.:</label>
<input name="operacion" type="text" required class="form-control" id="operacion" >
</div>
							 
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 manual ">
<label>Por Cobrar:</label>
<input name="saldo" type="text" class="form-control" readonly id="saldo" >
</div>
							
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 manual ">
<label>Detracción:</label>
<input name="detraccionf" type="text" class="form-control" readonly id="detraccionf" >
</div>
							
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 manual ">
<label>Retención:</label>
<input name="retencionf" type="text" class="form-control" readonly id="retencionf" >
</div>


							

                          <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12 form-group">
<label><hr></label>
                            <button class="btn btn-primary" type="button" id="btnGuardarc" onClick="guardapago()" ><i class="fa fa-save"></i> Guardar</button>


                          </div>
                        </form>
                  
</div>
</div>
                                     

          
<table id="tblpago" class="table table-striped table-bordered table-condensed table-hover" style="width: 100%;">
            <thead>
<th>Eliminar</th>
<th>id</th>
                <th>T.Pago</th>
                <th>Fecha</th>
				<th>Vence</th>
                <th>Mon</th>
				<th>S/.</th>
				<th>MUS$</th>
				<th>T.Camb</th>
				<th> Estado </th>
            </thead>
            <tbody></tbody>

          </table>



</div>
		


<div class="modal-footer">
<button type="button" class="btn  btn-primary" data-dismiss="modal" onClick="finingreso('PAGOS GUARDADOS CON ÉXITO')" >FINALIZAR</button>
</div>
		
</div>
  </div>  
  </div>
  <!-- Fin modal -->


<!-- Modal: DATOS DE PAGO (se muestra encima de #pagof) -->
<div class="modal fade" id="modalPagoDatos" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="modalPagoDatosLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm modal-pagodatos" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modalPagoDatosLabel">DATOS DE PAGO</h4>
      </div>

      <div class="modal-body">
        <input type="hidden" id="pago_edit_id" value="">
        <input type="hidden" id="pago_edit_idventa" value="">

        <div class="row">

          <div class="col-sm-4">
            <label>Fecha operación:</label>
            <input type="date" id="fechaoperacion" class="form-control input-sm" />
          </div>

          <div class="col-sm-4">
            <label>N° operación:</label>
            <input type="text" id="noperacion" class="form-control input-sm" placeholder="N° Operación" />
          </div>

          <div class="col-sm-4">
            <label>Monto:</label>
            <input type="text" id="montooperacion" class="form-control input-sm" value="0.00" />
          </div>

          <div class="col-sm-12" style="margin-top:10px;">
            <label>Comentarios:</label>
            <textarea id="pagocomentarios" class="form-control" rows="2"></textarea>
          </div>

          <div class="col-sm-12" style="margin-top:10px;">
            <label>Medio de pago:</label>
            <select id="idtipo_pago_edit" class="form-control" style="width:100%"></select>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btnCancelarPagoEdit">Cancelar</button>
        <button type="button" class="btn btn-success" id="btnCobrarPagoEdit"><i class="fa fa-check"></i> Cobrar</button>
      </div>

    </div>
  </div>
</div>

<!-- Fix stacking de modales (cuando #modalPagoDatos se abre encima de #pagof) -->
<style>
  /* Bootstrap 3 stacking helper */
  .modal.modal-stack { z-index: 1060 !important; }
  .modal-backdrop.modal-stack { z-index: 1055 !important; }
</style>



  <!-- Modal  COMPROBANTES2-->
  <div class="modal fade" id="myModaCOMP2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">

                        <div Class="modal-header">
                            <Button type="button" Class='close' data-dismiss="modal">×</Button>
                            <h4 Class='modal-title' id='myModalLabel'>COMPROBANTES RELACIONADOS</h4>
                        </div>
                        <div Class='modal-body'>
							
<div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
<label>MONTO ANTICIPO:</label>
<input name="montoanticipo" type="text" class="form-control" id="montoanticipo" value="0.00" >
</div>
							
							
<table id="listacomprobantes2" class="table table-condensed" style="width: 100%">
            <thead>
<th></th>
<th>#</th>
<th>Cliente</th>
                <th>Fecha</th>
                <th>Serie</th>
                <th>Total</th>
                <th>Pagado</th>
				<th>Por pagar</th>
            </thead>
            <tbody> 
            </tbody>

          </table>
</div>


    </div>
  </div>  
  </div>
  <!-- Fin modal --> 



  <!-- Modal  COMPROBANTES2-->
  <div class="modal fade" id="modalguia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">

                        <div Class="modal-header">
                            <Button type="button" Class='close' data-dismiss="modal">×</Button>
                            <h4 Class='modal-title' id='myModalLabel'>GUÍA RELACIONADOS</h4>
                        </div>
                        <div Class='modal-body'>
							
							
							
<table id="listaguia" class="table table-condensed" style="width: 100%">
            <thead>
<th></th>
<th>#</th>
<th>Cliente</th>
                <th>Fecha</th>
                <th>Serie</th>
            </thead>
            <tbody> 
            </tbody>

          </table>
</div>


    </div>
  </div>  
  </div>
  <!-- Fin modal --> 



  <!-- Modal  COMPROBANTES2-->
  <div class="modal fade" id="ventaservicios" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
<div class="modal-dialog" style="width: 70% !important;">
      <div class="modal-content">

                        <div Class="modal-header">
                            <Button type="button" Class='close' data-dismiss="modal">×</Button>
                            <h4 Class='modal-title' id='myModalLabel'>LISTA DE SERVICIOS</h4>
                        </div>
                        <div Class='modal-body'>
<div class="row">							
<div class="col-lg-12">
<div class="form-group col-lg-4">
<label>SERVICIOS:</label>
<select id="listservicio" name="listservicio" class="selectpicker form-control" data-live-search="true" onChange="cambiaservicio()"  required></select>
</div>

<div class="form-group col-lg-8 col-md-8 col-sm-12 col-xs-12">
<label>NOMBRE SERVICIO:</label>
<input name="titservicio" type="text" class="form-control" id="titservicio" value="" >
</div>
	
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label>PRECIO:</label>
<input name="precioserv" type="text" class="form-control" id="precioserv" value="0.00" >
</div>
	
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label>U/MEDIDA:</label>
	
 <select class="form-control" name="medidaserv" id="medidaserv" required>
                             
<?php 
	$n=0;
$datos = "SELECT *FROM unidad_medida ORDER BY tit ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['codigo']?>' ><?=$dato['tit']?></option>
<?php } ?>
</select>
</div>
		
<div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
<label>exonerado:</label>
<select name="igvservicio" id="igvservicio" class="form-control">
  <option value="0" selected="selected">IGV 18%</option>
  <option value="1">EXONERADO</option>
  <option value="2">GRATUITO</option>
</select>
</div>
	
<div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
<label>Calcular IGV:</label>
<select name="calculaigv" id="calculaigv" class="form-control">
  <option value="0" selected="selected">NO</option>
  <option value="1">SI</option>
</select>
</div>

<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>T.SERVICIO:</label>
	
<select class="form-control" name="tservicio" id="tservicio">
<option value='0' >SERV. NORMAL</option>
<option value='1' >SERV. TRANSPORTE</option>
</select>
</div>


<div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
<label>RUTA:</label>
	
<select class="form-control" name="ruta" id="ruta" >
<option value='0' selected >SELECCIONE</option>                       
<?php 
	$n=0;
$datos = "SELECT *FROM transporte_rutacab ORDER BY nombre ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['id']?>' ><?=$dato['nombre']?></option>
<?php } ?>
</select>
</div>
	
	
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label>DESTINO:</label>
	
 <select class="form-control" name="destino" id="destino">
<option value='0' selected >SELECCIONE</option>

</select>
</div>
	
<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
<label>CARGA ÚTIL:</label>
	
<select class="form-control" name="cargautil" id="cargautil">
<option value='0' selected >SELECCIONE</option>
<?php 
	$n=0;
$datos = "SELECT *FROM transporte_cautil ORDER BY carga ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['carga']?>' ><?=$dato['configuracion']?> (<?=$dato['carga']?>TN)</option>
<?php } ?>
</select>
</div>

<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>CTI.TONELADAS:</label>
<input name="ctitoneladas" type="text" class="form-control" id="ctitoneladas" value="0.00" >
</div>


<div class="form-group col-lg-12">
<label>DETALLE:</label>
<textarea name="detalleserv" id="detalleserv" class="form-control"></textarea>
</div>
</div>
</div>
</div>

		  
<div class="modal-footer">
        <button type="button" class="btn btn-primary" onClick="addservicio()" >AGREGAR</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
      </div> 
		  
		  
    </div>
  </div>  
  </div>
  <!-- Fin modal --> 




  <!-- Modal  COMPROBANTES2-->
  <div class="modal fade" id="importarventas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
<div class="modal-dialog" style="width: 70% !important;">
      <div class="modal-content">

<div Class="modal-header">
<Button type="button" Class='close' data-dismiss="modal">×</Button>
<h4 Class='modal-title' id='myModalLabel'>IMPORTAR VENTAS</h4>
</div>
<div Class='modal-body'>

<div class="row">							
<div class="col-lg-12">
SELECCIONE EL ARCHIVO QUE SUBIRA AL SISTEMA EN FORMATO XLSX (EXCEL)
<br><br>
<form enctype="multipart/form-data" class="for"> 
<div class="form-group col-lg-2 col-md-2 col-sm-8 col-xs-8"> 
<label>FECHA:</label>
<input type="date" class="form-control input-sm" name="fechaimport" id="fechaimport" value="<?php echo date("Y-m-d"); ?>">
</div>

<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Tipo/Pago:</label>
<select class="form-control" name="tpago2" id="tpago2" required>
<?php 
	$n=0;
$datos = "SELECT *FROM caja_tipopago WHERE idempresa='$_COOKIE[id]' AND estado='1' ORDER BY id ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['id']?>' ><?=$dato['descripcion']?></option>
<?php } ?>
</select>
</div>

<div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
<label>TIPO/OPE:</label>
<select class="form-control" name="contadocredito" id="contadocredito" required>
<option value='CONTADO' selected >CONTADO</option>
<option value='CREDITO' >CREDITO</option>
</select>
</div>

<div class="form-group col-lg-2 col-md-2 col-sm-8 col-xs-8"> 
<label>FECHA CRÉDITO:</label>
<input type="date" class="form-control input-sm" name="fechacredito" id="fechacredito" value="<?php echo date("Y-m-d"); ?>">
</div>

<div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
<label>exonerado:</label>
<select name="igvoperacion" id="igvoperacion" class="form-control">
<option value="1" selected="selected" >EXONERADO</option>
  <option value="0" >IGV 18%</option>
  <option value="2">GRATUITO</option>
</select>
</div>
	
<div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
<label>Calcular IGV:</label>
<select name="calculaigvupload" id="calculaigvupload" class="form-control">
  <option value="0" selected="selected">NO</option>
  <option value="1">SI</option>
</select>
</div>

<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>Local(*):</label>
<select class="form-control select-picker" name="idlocalventa" id="idlocalventa" required>                          
<?php 
	$n=0;
$datos = "SELECT *FROM sucursal WHERE nivel='0' AND estado='1' AND idempresa='$_COOKIE[id]'  ORDER BY id ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['id']?>' ><?=$dato['sucursal']?></option>
<?php } ?>
</select>
</div>
	
<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>TIPO IMPORTACIÓN:</label>
<select class="form-control select-picker" name="importacionc" id="importacionc" required>                          
<option value='0' >-GENERAL-</option>
</select>
</div>



<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
<input name="archivo" type="file" id="archivo" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
</div>	
		
	
</form>
<div class="messages"></div>
</div>
</div>
</div>
<div class="modal-footer">
        <button type="button" class="btn btn-primary" onClick="subeexcel()" >IMPORTAR EXCEL</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
      </div> 
    </div>
  </div>  
  </div>
  <!-- Fin modal --> 






<!-- Modal -->
<div class="modal fade" id="modalpagoadd" tabindex="-1" role="dialog"  data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">

<div Class="modal-header">
<h4 Class='modal-title' id='myModalLabel'>CUOTA DE LOS CRÉDITOS</h4>
</div>

<div Class='modal-body'>

<div class="row" >                        
<div class="col-lg-12"> 
<form name="formularioc" id="formularioc" method="POST">
<input type="hidden" name="iddoc" id="iddoc">
<input name="idventapago2" type="hidden" id="idventapago2" >
<input name="marcaenviado2" type="hidden" id="marcaenviado2" >
<input name="nivelpago2" type="hidden" id="nivelpago2" value="0" >
<input name="tipopaso" type="hidden" id="tipopaso" value="0" >
<input name="directosunat" type="hidden" id="directosunat" value="0" >

<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
<label>Fecha:</label>
<input name="fechapago2" type="date" required class="form-control input-sm" id="fechapago2" value="<?php echo date("Y-m-d"); ?>">
</div>
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>T/C:</label>
<input name="tcambio2" type="text" class="form-control" id="tcambio2" >
</div>
                          
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
<label>T.Pago:</label>
 <select class="form-control" name="tpago2" id="tpago2" required>
                             
<?php 
	$n=0;
$datos = "SELECT *FROM caja_tipopago WHERE idempresa='$_COOKIE[id]' AND estado='1' ORDER BY id ASC ";
$datos = ejecutarConsulta($datos);
while($dato=mysqli_fetch_array($datos)) {
$n=$n+1;
?>
<option value='<?=$dato['id']?>' ><?=$dato['descripcion']?></option>
<?php } ?>
</select>
</div>
                   
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 manual ">
<label>Monto pagar:</label>
<input name="montopago2" type="text" id="montopago2"  class="form-control" value="0.00" >	
</div>
						 
<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 manual ">

<label>Por Cobrar:</label>
<input name="saldo2" type="text" class="form-control" readonly id="saldo2" >
</div>
<input name="fechadoc2" type="hidden" class="form-control" readonly id="fechadoc2" value="" >
<input name="monedapago2" type="hidden" class="form-control" readonly id="monedapago2" value="PEN" >							 
<input name="tipopago" type="hidden" class="form-control" readonly id="tipopago" value="0" >
<input name="properiodo2" type="hidden" class="form-control" readonly id="properiodo2" value="NO" >		  
<input name="periodo2" type="hidden" class="form-control" readonly id="periodo2" value="60" >		 
<input name="letras2" type="hidden" class="form-control" readonly id="letras2" value="1" >	
<input name="detraccionf2" type="hidden" class="form-control" readonly id="detraccionf2" >
<input name="operacion2" type="hidden" required class="form-control" id="operacion2" >
<input name="retencionf2" type="hidden" class="form-control" readonly id="retencionf2" >

                          <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12 form-group">
<label><hr></label>
                            <button class="btn btn-primary" type="button" id="btnGuardarc2" onClick="addpagoventa()" ><i class="fa fa-save"></i> Guardar</button>


                          </div>
                        </form>
                  
</div>
</div>
                                     

          
<table id="tblpagof" class="table table-striped table-bordered table-condensed table-hover" style="width: 100%;">
            <thead>
                <th>T.Pago</th>
                <th>Fecha</th>
				<th>Vence</th>
                <th>Mon</th>
				<th>Monto</th>
				<th>T.Camb</th>
				<th></th>
            </thead>
            <tbody></tbody>

          </table>



</div>

<div class="modal-footer">
<button type="button" class="btn  btn-primary" data-dismiss="modal" onClick="finingreso('PAGOS GUARDADOS CON ÉXITO')" >FINALIZAR</button>
</div>
		
</div>
  </div>  
  </div>
  <!-- Fin modal -->





<link type="text/css" href="plugins/dataTables.checkboxes.css" rel="stylesheet" />
<script type="text/javascript" src="plugins/dataTables.checkboxes.min.js"></script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD8elcokuYJ5erAfkSHkXEbTDyNSU7679Y&libraries=adsense&sensor=true&language=es"></script>    

<script type="text/javascript" >

$(document).ready(function(){
 
 $(".messages").hide();
 var fileExtension = "";
 $(':file').change(function(){
     var file = $("#archivo")[0].files[0];
     var fileName = file.name;
     fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
     var fileSize = file.size;
     var fileType = file.type;
 console.log(fileType);
     showMessage("<span class='info'>Archivo para subir: "+fileName+", peso total: "+fileSize+" bytes.</span>");
 });
})
function showMessage(message){
 $(".messages").html("").show();
 $(".messages").html(message);
}


function init(){
	mostrarform(false);
	listar();

	$("#miFormArticulo").on("submit",function(e){
		guardaryeditar(e);	
	});
	
$('#addcliente').attr("disabled", true);
$('#btnadelanto').attr("disabled", true);
$("#adelantot").prop('disabled', true);
	//document.getElementById('imprimir').disabled=true;
	//document.getElementById('boton').disabled=false;
}

</script>

    <!--PLUGIN JQGRID-->
    <script src="assets/js/jquery.jqGrid.js" type="text/javascript"></script>     
    <script src="assets/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="assets/js/numero_letras.js" type="text/javascript"></script>    

<script type="text/javascript" src="scripts/venta.js?v=4"></script>
<script type="text/javascript" src="scripts/generales.js"></script>
<script type="text/javascript" src="scripts/servicio-transporte.js"></script>
<script src="plugins/fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="plugins/fileinput/js/locales/es.js" type="text/javascript"></script>	
    <!--FIN PLUGIN JQGRID-->

    <script type='text/javascript'>
var link='<?=RUTA?>';

function printgrupo(){
	
      var rows_selected = tabla.column(0).checkboxes.selected();
      $.each(rows_selected, function(index, rowId){
		  			  
var ruta=link+'/plugins/dompdf/ticket.php?id='+rowId;
	  
$('<iframe />'); 
$('<iframe />', {
    name: 'iframe'+rowId,
    id: 'iframe'+rowId,
    //width: '1px',
    //height: '1px',
    src: ruta
}).appendTo('body');
		  
$('#iframe'+rowId).attr("src", ruta).load(function(){
	
var onPrintFinished=function(printed){console.log("do something...");}	
window.onafterprint = function(e){
   console.log("Printing completed...");
	console.log('rowId:'+rowId);
	document.getElementById('pdf-iframe').contentWindow.print();
    };

onPrintFinished(document.getElementById('iframe'+rowId).contentWindow.print());

});
		
	  
      });
   }
		
function BuscarArticulo() {
var idcli=$('#txtID_CLIENTE').val();
if(idcli=='0'){ Swal.fire('SELECCIONE CLIENTE!'); return false; }
if(typeof _validarFpagoSeleccion === 'function' && !_validarFpagoSeleccion()){ return false; }
if(typeof _lockMonedaVenta === 'function'){ _lockMonedaVenta(true); }
	
listarArticulos();	
$('#myModal').modal('show');
$('#miFormArticulo')[0].reset();
}

function listartabla() {
            $('#myModal').modal('show');
            $('#miFormArticulo')[0].reset();
        }		
	
		
function enviasunat(){
	
var cliente=$("#txtID_CLIENTE option:selected").val();
	
if(cliente=='0'){ Swal.fire('SELECCIONE CLIENTE!'); return true; }
if (!tabladetalles.data().any() ) { Swal.fire('Agrege un producto'); return true; }		

            $("#txtSUB_TOTAL").attr("disabled", true);
            $("#txtIGV").attr("disabled", true);
            $("#txtTOTAL").attr("disabled", true);
            tbl = $('#list');
            frm = $('#frmBusqueda');
	var fpago=$("#pago").val();
var medio=$("#medio").val();
	
Swal.fire('GUARDANDO INFORMACIÓN!');
	
//BLOQUEAMOS LOS BOTONES		
$('#agregar').attr("disabled", true);
							$('#guardar').attr("disabled", true);
							$('#eliminar').attr("disabled", true);
    $('#agregars').attr("disabled", true);

                    var tipo_doc = $('#txtTIPO_DOCUMENTO_CLIENTE').val();


var DATA = [];
var num='0';
$("#detpedidos tr").each(function(index){
var sumtotal='0.00';
var subtotal='0.00';
var igv='0.00';
var idp='';
var exonerada='0';
if(num!='0'){
var detalle = {};
    $(this).children("td").each(function(index2){

        switch(index2){
		case 0:	
		idp=$(this).text();
		//case 2:	detalle["txtCANTIDAD_DET"]=$(this).text();
		//case 4:	detalle["txtIMPORTE_DET"]=$(this).text();
		case 5: detalle["descuento"]='0.00'; 
		case 6: detalle["BOLSA"]='0';
		break;
    }
	
detalle["txtID"]=$('#idp'+idp).val();
detalle["txtIMPORTE_DET"]=$('#totf'+idp).val();
detalle["txtCANTIDAD_DET"]=$('#ctif'+idp).val();
detalle["txtPRECIO"]=$('#preciof'+idp).val();
sumtotal=detalle["txtIMPORTE_DET"];
exonerada=$('#tipo'+idp).val();
detalle["tipo"]=exonerada;
detalle["tipounidad"]=$('#tipo'+idp).val();	
detalle["txtPRECIO_DET"]=$('#sub'+idp).val();
detalle["txtIGV"]=$('#igv'+idp).val();;
detalle["txtDESCRIPCION_DET"]=$('#tit'+idp).val();
detalle["UNIDAD_MEDIDA"]=$('#umedida'+idp).val();

detalle["txtCODIGO_DET"]=$('#codigo'+idp).val();
detalle["ctiunidad"]=$('#cti2'+idp).val();
detalle["comision"]=$('#comision'+idp).val();
detalle["idunit"]=$('#idunid'+idp).val();
/*
console.log('unidad:'+$('#umedida'+idp).val());
console.log('idunidad:'+$('#idunid'+idp).val());
*/
detalle["detracciond"]=$('#totdetraccion'+idp).val();
detalle["exonerado"]=$('#exo'+idp).val();
detalle["gratuita"]=$('#grat'+idp).val();
detalle["tipoart"]=$('#tipoart'+idp).val();		
detalle["serie"]=$('#serie'+idp).val();
detalle["descuento"]=$('#descuento'+idp).val();
detalle["placa"]='';
		
});
DATA.push(detalle);
}	
num=num+1;		
});

var total_letras = NumeroALetras($("#txtTOTAL").val());
var moneda=$("#txtMONEDA option:selected").val();
var detracciones=$('#detracciones').val();
	
var iddetraccion='0';
var montodetraccion='0';
	
if(detracciones!='0|0|0'){	
var ret=$('#detracciones').val().split('|');
iddetraccion=ret[0];
montodetraccion=$('#montodetraccion').val();	
}
	
console.log('idventa:'+$('#idventa').val());
	
                    $.ajax({
                        url: "modelos/venta-graba2.php?act=0",
                        type: "post",
                        dataType: 'json',
                        data: JSON.stringify({
"opadelanto": $("#opadelanto").val(),
"scredito": $("#scredito").val(),
"ncredito": $("#ncredito").val(),
"condiciones": $("#condiciones").val(),
"adelantot": $("#adelantot").val(),
"txtTOTAL_GRAVADAS": $("#gravadas").val(),
"txtSUB_TOTAL": $("#txtSUB_TOTAL").val(),
"totdescuento": $("#descuentotot").val(),
"serieanticipo": $("#serieanticipo").val(),
"idanticipo": $("#idanticipo").val(),
"txtTOTAL_IGV": $("#txtIGV").val(),
"retencion": $("#retencion").val(),
"operacion": $("#operacionretenciones").val(),	
"idcategoria": $('#ccostos').val(),
"iddetraccion":iddetraccion,	
"montodetraccion":montodetraccion,
							
"txtTOTAL": $("#txtTOTAL").val(),
"comisiont":$('#comisiont').val(),
"txtTOTAL_LETRAS": total_letras, //"SETECIENTOS TREINTA Y SIETE CON 50/100 SOLES",
"txtSERIE": $('#txtSERIE').val(),
"txtNUMERO": $('#txtNUMERO').val(),
"txtTOTAL_EXONERADAS": $("#exoneradof").val(),
"txtTOTAL_GRATUITAS": $("#gratuita").val(),
"txtPAGO": $("#pago").val(),
"mediopago": $("#medio").val(),
"guia": $("#guia").val(),
"notpedido": $("#notpedido").val(),
"ocompra": $("#ocompra").val(),							
"txtFECHA_DOCUMENTO": $('#txtFECHA_DOCUMENTO').val(), //$("#txtTOTAL").val(),
"txtCOD_TIPO_DOCUMENTO": $('#txtID_TIPO_DOCUMENTO').val(), //01=factura,03=boleta
"txtOBSERVACION":$('#txtOBSERVACION').val(),
"txtCOD_MONEDA": moneda,
//=================datos del cliente=================
"cliente": cliente,
"vendedor": $('#vendedor').val(),
"tarjeta": $('#tarjeta').val(),
"tipoguia": $('#tipoguia').val(),	
"tipocambio": $('#tipocambio').val(),
"puntos": $('#puntos').val(),
"percepcion": $('#percepcion').val(),
"idventa": $('#idventa').val(),
"kardex": $('#kardex').val(),
"detalle": DATA
                        }),
                        success: function (datos) {
                           console.log(datos);
							swal.close();
							//Swal.fire(datos.mensaje);

							$('#idventa').val(datos.idventa);
							console.log('idventa:'+datos.idventa);
							
							$('#agregar').attr("disabled", true);
							$('#agregar1').attr("disabled", true);
							$('#guardar').attr("disabled", true);
							$('#guardar1').attr("disabled", true);

tipooperacion='100';

$('.guardaventa').attr("disabled", true);
					
$('#imprimir').attr("disabled", false);
$('#imprimirt').attr("disabled", false);
$('#imprimirb').attr("disabled", false);
$('#imprimirt2').attr("disabled", false);

Swal.fire(datos.mensaje);
							
                        },
                        error: function (data) {
                            console.log(data);
                            alert('Error Al conectar la Base Datos');
                            //console.log(data);
                        }
                    });
                }

	
		/*
$('body').keyup(function(e) {
    if(e.keyCode == 13) {
        alert('Has presionado ENTER');
    }
});
		
	*/	
		

		
		
		
    </script>





</body>
</html>
