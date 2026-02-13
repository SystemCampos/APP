
<?php
$hoy = date("Y-m-d");
$idusuario=$_COOKIE["idusuario"];
//$_COOKIE['imagen']
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa=ejecutarConsultaSimpleFila($sql3);

$sqluser="SELECT *FROM usuario WHERE idusuario='$idusuario' ";
$user= ejecutarConsultaSimpleFila($sqluser);

$fecha_apertura = date('Y-m-d'); // Formato YYYY-MM-DD

$sqlcc="SELECT * FROM cajas WHERE idlocal='$_COOKIE[idlocal]' AND id_usuario='$_COOKIE[idusuario]'  and estado='1' ORDER BY id DESC ";
$mostrarcc= ejecutarConsultaSimpleFila($sqlcc);

$sqlcat="SELECT *FROM tipo_cambio WHERE fecha='$hoy' AND idempresa='0' ";
$tchoy= ejecutarConsultaSimpleFila($sqlcat);

if(!$tchoy){
    $sqlcat="SELECT *FROM tipo_cambio ORDER BY fecha DESC ";
    $tchoy= ejecutarConsultaSimpleFila($sqlcat);
}

/*if($fa['sistem_ruta']!='http://localhost/code/tec/'){
    header("Location: ".$fa['sistem_ruta']);
}*/

if($fa['estado']!='1'){
    $_COOKIE["vencimiento"]="0";
}else if($fa['fechafin']>$hoy){
    $_COOKIE["vencimiento"]="1";
}else{
    $_COOKIE["vencimiento"]="0";
}

/*if(!isset($_COOKIE['fechavencimiento'])){
    header("Location: ".$fa['sistem_ruta']);
}
*/
?>


<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->

    <div class="sidebar-menu" >

        <div class="sidebar-menu-inner">

            <header class="logo-env">

                <!-- logo -->
                <div class="logo">
                    <a href="escritorio.php">
                        <input type="hidden" id="porcentajeigv" name="porcentajeigv" class="form-control" value="<?=$fa['igv']?>" />
                        <img src="files/logo/<?=$_COOKIE['id']?>.png" class="img-fluid" style="max-width: 100%; " alt="" />
                    </a>
                </div>

                <!-- logo collapse icon -->
                <div class="sidebar-collapse">
                    <a href="#" class="sidebar-collapse-icon"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
                        <i class="entypo-menu"></i>
                    </a>
                </div>


                <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
                <div class="sidebar-mobile-menu visible-xs">
                    <a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
                        <i class="entypo-menu"></i>
                    </a>
                </div>

            </header>


            <ul id="main-menu" class="main-menu">
                <!-- add class "multiple-expanded" to allow multiple submenus to open -->
                <!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->


        <!--        <?php if ($_COOKIE['escritorio']==1){ ?>
                    <li class="has-sub">
                        <a href="escritorio.php"><i class="entypo-gauge"></i><span>Escritorio</span></a>
                    </li>
                <?php } ?>-->
                
                
              
                    <li class="has-sub">
                        <a href="escritorio.php"><i class="entypo-gauge"></i><span>Escritorio</span></a>
                    </li>
            
                

                <?php if ($_COOKIE['maestros']==1) { ?>
                    <li class="has-sub">
                        <a href="#">
                            <i class="fa fa-cog"></i> <span>Maestros</span>
                        </a>
                        <ul class="treeview-menu">
<li><a href="articulo.php?cat=0&t=PRODUCTOS/SERVICIOS/VENTAS"><i class="fa fa-circle-o"></i> Artículos/Servicios/Ventas</a></li>
<li><a href="articulo-cliente.php?cat=0&t=PRODUCTOS/PRECIO/CLIENTES"><i class="fa fa-circle-o"></i> Lista de Precios - Cliente</a></li>
<li><a href="pagos-clientes.php"><i class="fa fa-circle-o"></i> Pagos clientes</a></li>



<li><a href="articulo-precio.php?cat=0&t=PRODUCTOS/GASTOS"><i class="fa fa-circle-o"></i> Artículos/Precios</a></li>

<li><a href="cliente.php"><i class="fa fa-circle-o"></i> Clientes</a></li>
<li><a href="proveedor.php"><i class="fa fa-circle-o"></i> Proveedores</a></li>
<li><a href="perfil-tipopago.php"><span class="glyphicon glyphicon-usd"></span> Tipo de pago</a></li>
<li><a href="categoria.php?nivel=4&t=ÁREAS / CENTRO COSTOS"><i class="fa fa-circle-o"></i> Áreas / Centro costos</a></li>
<li><a href="categoria.php?nivel=6&t=CONTROL PRESUPUESTAL"><i class="fa fa-circle-o"></i> Presupuestos </a></li>
<li><a href="perfil-series.php"><i class="fa fa-circle-o"></i> Series</a></li>
<li><a href="usuario.php?cat=1&t=VENDEDORES"><i class="fa fa-circle-o"></i> Experto/Profesional</a></li>
<li><a href="categoria.php?nivel=1&t=MARCA"><i class="fa fa-circle-o"></i> Marca</a></li>
<li><a href="categoria.php?nivel=0&t=GRUPO"><i class="fa fa-circle-o"></i> Grupo</a></li>
<li><a href="categoria.php?nivel=3&t=LÍNEA"><i class="fa fa-circle-o"></i> Líneas</a></li>

                             <li><a href="categoria.php?nivel=4&t=ÁREAS / CENTRO COSTOS"><i class="fa fa-circle-o"></i> Principio Activo</a></li> 

<li><a href="categoria.php?nivel=2&t=SECTORES"><i class="fa fa-circle-o"></i> Sectores</a></li>
<li><a href="contabilidad-plan-cuentas.php?nivel=0&t=PLAN CONTABLE"><i class="fa fa-circle-o"></i> Plan contable</a></li>	
                        </ul>
                    </li>
                <?php } ?>

                <?php if ($_COOKIE['pventas']==1){ ?>
                    <?php if ($_COOKIE['pedidos']==1) { ?>


                        <li class="has-sub">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i> <span> Pedidos</span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($_COOKIE['vencimiento']==1){ ?>
                                    <li><a href="pedidos.php?cat=91&t=COTIZACIÓN"><i class="fa fa-circle-o"></i> Cotización</a></li>
                                    <li><a href="pedidos.php?cat=92&t=NOTA DE PEDIDO"><i class="fa fa-circle-o"></i> Nota de pedido</a></li>
                                <?php }else{ ?>
                                    <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Cotización <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                    <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Nota de pedido <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                <?php } ?>

                            </ul>
                        </li>

                    <?php } ?>


<?php if ($_COOKIE['ventas']==1){ ?>
<li class="has-sub">
    
    
<?php 
	
	if($mostrarcc['estado']=='1'){
?>
<a href="#" onclick="cerrarcaja()">
<?php 
}else{ 
?>
<a href="#" data-toggle="modal" data-target="#myModalcaja"> 
			
<?php } ?>




<i class="fa fa-shopping-cart"></i>
<span>Ventas</span>
</a>
<ul class="treeview-menu">

<?php if ($_COOKIE['vencimiento']==1){ ?>




<li><a href="venta.php"><i class="fa fa-circle-o"></i> Venta Tradicional</a></li>
<li><a href="venta-post.php"><i class="fa fa-circle-o"></i> Venta POST</a></li>
<li><a href="venta-rapida.php"><i class="fa fa-circle-o"></i> Venta Rápida</a></li>
<li><a href="credito-debito.php"><i class="fa fa-circle-o"></i>Nota Credito / Debito</a></li>
          
<li><a href="comprobante-percepcion.php"><i class="fa fa-circle-o"></i>Percepción</a></li>

<li><a href="venta-periodo.php"><i class="fa fa-circle-o"></i> Venta por periodo</a></li>

<?php
if ($_COOKIE['administracion']==1){ 
?>
<li><a href="reporte-cobrar.php?t=CUENTAS POR COBRAR&cat=0"><i class="fa fa-circle-o"></i> Cuentas por cobrar</a></li>
<?php
} 
?>                        
                                <?php }else{ ?>
                                    <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Venta Tradicional <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                    <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Venta Rápida <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                    <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i>Nota Credito / Debito <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                    <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i>Venta por periodo <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                    
                                    <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i>Cuentas por cobrar <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                <?php } ?>
                                
                                
                                <li><a href="venta-periodo.php"><i class="fa fa-circle-o"></i> Venta por periodo</a></li>
                                
                        
                                
                                <li><a href="reporte-puntos.php"><i class="fa fa-circle-o"></i> Puntos Clientes</a></li>
                                
                                <li><a href="cliente.php"><i class="fa fa-circle-o"></i> Clientes</a></li>
                            </ul>
                        </li>

                        <?php
                    }
                }else{
                    ?>

                    <li><a href="no-accesos.php"><i class="fa fa-bar-chart-o"></i><span class="label-secondary "> Pedidos Premium</span></a></li>
                    <li><a href="no-accesos.php"><i class="fa fa-shopping-cart"></i><span class="label-secondary "> Ventas Premium</span></a></li>

                <?php } ?>

                <?php if ($_COOKIE['pkardex']==1){ ?>
                    <?php if ($_COOKIE['almacen']==1){ ?>
                        <li class="has-sub">
                            <a href="#"><i class="entypo-monitor"></i>
                                <span>Almacén</span>
                            </a>
                            <ul class="treeview-menu">

<?php if ($_COOKIE['vencimiento']==1){ ?>
    <li><a href="almacen-traslado.php?cat=1&t=SALIDA DE ALMACEN"><i class="fa fa-circle-o"></i> Salida de almacén</a></li>
<li><a href="almacen-ingresos.php?cat=0&t=INGRESO DE ALMACEN"><i class="fa fa-circle-o"></i> Ingreso de almacén</a></li>

<li><a href="almacen-kardex.php"><i class="fa fa-circle-o"></i> Reporte Kardex</a></li>

<li><a href="almacen-lotes.php"><i class="fa fa-circle-o"></i> Series/Lotes</a></li>

<li><a href="almacen-stock.php"><i class="fa fa-circle-o"></i> Reporte Stock</a></li>
<li><a href="almacen-conciliacion.php"><i class="fa fa-circle-o"></i> Conciliación de Inventario</a></li>

<li><a href="articulo.php"><i class="fa fa-circle-o"></i> Artículos</a></li>
<li><a href="categoria.php?nivel=4&t=ÁREAS / CENTRO COSTOS"><i class="fa fa-circle-o"></i> Áreas / Centro costos</a></li>
<?php }else{ ?>

<li><a href="#"><i class="fa fa-circle-o"></i> Salida de almacén <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
<li><a href="#"><i class="fa fa-circle-o"></i> Ingreso de almacén <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>

<li><a href="#"><i class="fa fa-circle-o"></i> Reporte Kardex <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
<li><a href="#"><i class="fa fa-circle-o"></i> Series/Lotes <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
<li><a href="#"><i class="fa fa-circle-o"></i> Reporte Stock <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>

<li><a href="#"><i class="fa fa-circle-o"></i> Artículos <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
<li><a href="#"><i class="fa fa-circle-o"></i> Áreas / Centro costos <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>


<?php } ?>



                            </ul>
                        </li>
                    <?php } ?>


                    <?php if ($_COOKIE['compras']==1) { ?>
                        <li class="has-sub">
                            <a href="#">
                                <i class="entypo-layout"></i>
                                <span>Compras</span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($_COOKIE['vencimiento']==1){ ?>
                                    <li><a href="ingreso.php?cat=0&t=INGRESOS"><i class="fa fa-circle-o"></i> Compra Mercadería </a></li>
<li><a href="ingreso-gastos-otros.php?cat=1&t=COMPRAS"><i class="fa fa-circle-o"></i> Gastos</a></li>
                                    <li><a href="ingreso.php?cat=2&t=ORDEN DE COMPRA"><i class="fa fa-circle-o"></i> Orden de compras</a></li>
                                    <li><a href="ingreso.php?cat=3&t=SOLICITUD DE COMPRA"><i class="fa fa-circle-o"></i> Solicitud de compra</a></li>
								
<li><a href="ingreso-servicio.php?cat=4&t=ORDEN DE SERVICIO"><i class="fa fa-circle-o"></i> Orden de servicio</a></li>
								
<li><a href="reporte-cobrar.php?t=CUENTAS POR PAGAR&cat=1"><i class="fa fa-circle-o"></i> Cuentas por pagar</a></li>
<li><a href="ingreso-gastos.php"><i class="fa fa-circle-o"></i> Conf. Otros gastos</a></li>


                                <?php }else{ ?>
                                    <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Compras/Almacen <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                    <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Gastos <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                    <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Orden de compras <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                <?php } ?>
                            </ul>
                        </li>



                    <?php } ?>


                <?php }else{ ?>

                    <li><a href="no-accesos.php"><i class="entypo-monitor"></i><span class="label-secondary "> Almacen Premium</span></a></li>
                    <li><a href="no-accesos.php"><i class="entypo-layout"></i><span class="label-secondary "> Compras Premium</span></a></li>

                <?php } ?>



                <?php if ($_COOKIE['acceso']==1){ ?>
                    <li class="has-sub">
                        <a href="#"><i class="fa fa-users"></i><span>Acceso</span></a>
                        <ul class="treeview-menu">
                            <li><a href="usuario.php?cat=0&t=USUARIOS"><i class="fa fa-circle-o"></i> Usuarios</a></li>
                        </ul>
                    </li>
                <?php } ?>



                <?php if ($_COOKIE['planilla']==1){ ?>
                <?php if ($_COOKIE['planillas']==1){ ?>

                    <li class="has-sub">
                        <a href="#"><i class="fa fa-users"></i><span>Planillas</span></a>
                        <ul class="treeview-menu">

                            <li class="has-sub">
                                <a href="#">
                                    <i class="fa fa-cog"></i> <span>Planillas</span>
                                </a>
                                <ul class="treeview-menu">
                                    <?php if ($_COOKIE['vencimiento']==1){ ?>
                                    <li><a href="planillas-planillas.php?cat=0&t=PLANILLAS GENERAL"><i class="fa fa-circle-o"></i> Planillas General</a></li>
                                    <li><a href="planillas-planillas.php?cat=2&t=PLANILLAS GRATIFICACIONES"><i class="fa fa-circle-o"></i> Planillas Gratificaciones</a></li>
                                    <li><a href="planillas-planillas.php?cat=3&t=PLANILLAS CTS"><i class="fa fa-circle-o"></i> Planillas CTS</a></li>
                                    <li><a href="planillas-planillas.php?cat=1&t=PLANILLAS VACACIONES"><i class="fa fa-circle-o"></i> Planillas Vacaciones</a></li>

                                </ul>
                            </li>
<li><a href="planillas-afectaciones.php"><i class="fa fa-circle-o"></i> Afectaciones</a></li>
<li><a href="planillas-pagosextras.php"><i class="fa fa-circle-o"></i> Pagos extras</a></li>
<li><a href="planillas-descuento.php"><i class="fa fa-circle-o"></i> Descuentos</a></li>
<li><a href="planillas-tareo.php"><i class="fa fa-circle-o"></i> Tareo</a></li>
<li><a href="planillas-rh.php"><i class="fa fa-circle-o"></i> Recibo por Honorarios</a></li>
<li><a href="planillas-horarios.php"><i class="fa fa-circle-o"></i> Horarios</a></li>
<li><a href="planillas-trabajadores.php"><i class="fa fa-circle-o"></i> Trabajadores</a></li>
<li><a href="planillas-configuracion.php"><i class="fa fa-circle-o"></i> Configuración</a></li>
                            <?php }else{ ?>
                                <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> planilla<span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php }else{ ?>
                <li><a href="no-accesos.php"><i class="fa fa-shopping-cart"></i><span class="label-secondary "> Planilla Premium</span></a></li>
                <?php } ?>




<?php if ($_COOKIE['pactivos']==1){ ?>
                <?php if ($_COOKIE['activofijo']==1){ ?>
                    <li class="has-sub">
                        <a href="#">
                            <i class="fa fa-building"></i> <span>Activos fijos</span>
                        </a>
                        <ul class="treeview-menu">

                        <?php if ($_COOKIE['vencimiento']==1){ ?>

                            <li><a href="activos-depreciacion.php?act=0&t=DEPRECIACIÓN ANUAL"><i class="fa fa-circle-o"></i> Depreciación</a></li>
                            <li><a href="activos-fijos.php"><i class="fa fa-circle-o"></i> Registrar </a></li>
                            <?php }else{ ?>

<li><a href="#"><i class="fa fa-circle-o"></i> Depreciación <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
<li><a href="#"><i class="fa fa-circle-o"></i> Registrar <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>

                                <?php } ?>
                        </ul>
                    </li>
    <?php } ?>
                <?php }else{ ?>
        <li><a href="no-accesos.php"><i class="fa fa-shopping-cart"></i><span class="label-secondary "> Activos Fijos Premium</span></a></li>
                <?php } ?>




<?php if ($_COOKIE['reportes']==1) { ?>
<li class="has-sub">
<a href="#">
<i class="fa fa-bar-chart"></i> <span> Reportes</span>
</a>
<ul class="treeview-menu">
<?php if ($_COOKIE['administrador']==1) { ?>
<li><a href="ventas-graficos.php"><i class="fa fa-circle-o"></i> Ventas por Gráfico</a></li>
<li><a href="ventasfechacliente.php"><i class="fa fa-circle-o"></i> Ventas por documento</a></li>
<li><a href="reporte-detalle.php"><i class="fa fa-circle-o"></i> Ventas por detalle</a></li>
<li><a href="ventas-entregar.php"><i class="fa fa-circle-o"></i> Ventas por entregar</a></li>
<li><a href="reporte-ventas-cobrar.php"><i class="fa fa-circle-o"></i> Ventas a Crédito</a></li>
<li><a href="reporte-detraccion.php"><i class="fa fa-circle-o"></i> Ventas a Detracción</a></li>							
<li><a href="reporte-rentabilidad.php"><i class="fa fa-circle-o"></i> Rentabilidad</a></li>
<li><a href="comprasfecha.php"><i class="fa fa-circle-o"></i> Compras por documento</a></li>
<li><a href="reporte-detalle-compras.php"><i class="fa fa-circle-o"></i> Compras por detalle</a></li>
<li><a href="reporte-cobrar.php?t=ESTADO DE CUENTA&cat=0"><i class="fa fa-circle-o"></i> Estado de cuenta</a></li>
<li><a href="reportes-efectivo.php"><i class="fa fa-circle-o"></i> Movimiento de efectivo</a></li>
<li><a href="reportes-cajas.php"><i class="fa fa-circle-o"></i> Consulta Cajas</a></li>
                            
                            <li><a href="reportes-comisiones.php"><i class="fa fa-circle-o"></i> Comisiones Experto/Profesional</a></li>
<?php } ?>
                        </ul>
                    </li>
                <?php } ?>




                <?php if ($_COOKIE['paguiar']==1){ ?>
                    <?php if ($_COOKIE['guia']==1){ ?>

                        <li class="has-sub">
                            <a href="#">
                                <i class="fa fa-truck"></i> <span>Guía de remisión</span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($_COOKIE['vencimiento']==1){ ?>
                                    <li><a href="guia-remitente.php"><i class="fa fa-circle-o"></i> Guía Remisión</a></li>
                                    <li><a href="mantenimiento-etransporte.php"><i class="fa fa-circle-o"></i> Mant. Empr. Trans.</a></li>
                                    <li><a href="mantenimiento-vehiculo.php"><i class="fa fa-circle-o"></i> Mant. vehículos</a></li>
                                    <li><a href="mantenimiento-chofer.php"><i class="fa fa-circle-o"></i> Mant. chofer</a></li>
                        <?php }else{ ?>
                                <li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Guía Remisión<span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php }else{ ?>
                    <li><a href="no-accesos.php"><i class="fa fa-shopping-cart"></i><span class="label-secondary "> Guia de Remision Premium</span></a></li>
                <?php } ?>





                <?php if ($_COOKIE['facturacione']==1) { ?>
                    <li class="has-sub">
                        <a href="#"><i class="fa fa-cloud-upload"></i> <span>Resumen / Baja de doc</span></a>
                        <ul class="treeview-menu">
                            <li><a href="resumen-diario.php"><i class="fa fa-circle-o"></i> Resumen diario</a></li>
                            <li><a href="bajas-facturas.php?t=BAJA DE BOLETAS&cat=03"><i class="fa fa-circle-o"></i> Bajas de boletas</a></li>
                            <li><a href="bajas-facturas.php?t=BAJA DE FACTURAS&cat=01"><i class="fa fa-circle-o"></i> Bajas de facturas</a></li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ($_COOKIE['configuracion']==1) { ?>
                    <li class="has-sub">
                        <a href="#">
                            <i class="fa fa-cog"></i> <span>Configuración</span>
                        </a>
                        <ul class="treeview-menu">
<li><a href="perfil-empresa.php"><i class="fa fa-circle-o"></i> Perfil</a></li>
<li><a href="perfil-contablidad.php"><i class="fa fa-circle-o"></i> Contabilidad</a></li>
<li><a href="perfil-bancos.php"><i class="fa fa-circle-o"></i> Cuentas bancarias</a></li>
<li><a href="perfil-tcambio.php"><i class="fa fa-circle-o"></i> Tipo de Cambio</a></li>							
                            <li><a href="sucursal.php"><i class="fa fa-circle-o"></i> Sucursales</a></li>
                            <li><a href="perfil-logo.php"><i class="fa fa-circle-o"></i> Logotipo</a></li>
                            <li><a href="gre.php"><i class="fa fa-circle-o"></i> GRE</a></li>
                            <li><a href="rutas.php"><i class="fa fa-circle-o"></i> RUTAS</a></li>
                            <?php if ($_COOKIE['id']==1){ ?>
                                <li><a href="contribuyentes.php"><i class="glyphicon glyphicon-th-list"></i> <span>Contribuyente</span></a></li>
                            <?php } ?>
                            <li class="has-sub">
                                <a href="#">
                                    <i class="fa fa-cog"></i> <span>MANTENIMIENTO</span>
                                </a>
                                <ul class="treeview-menu">

                                    <li><a href="mentenimiento-importaciones.php"><i class="glyphicon glyphicon-th-list"></i><span> IMPORTACIONES</span></a></li>
                                    <?php if ($_COOKIE['id']==1){ ?>
                                        <li><a href="mantenimiento-noticias.php?cat=A&t=NOTICIAS FACTURACIÓN"><i class="glyphicon glyphicon-th-list"></i> <span>NOTICIAS</span></a></li>

                                        <li><a href="mantenimiento-noticias.php?cat=B&t=MÓDULOS"><i class="glyphicon glyphicon-th-list"></i> <span>MÓDULOS</span></a></li>

                                        <li><a href="mantenimiento-noticias.php?cat=C&t=CARACTERISTICAS"><i class="glyphicon glyphicon-th-list"></i> <span>CARACTERISTICAS</span></a></li>

                                        <li><a href="mantenimiento-noticias.php?cat=D&t=PLANES Y PAQUETES"><i class="glyphicon glyphicon-th-list"></i> <span>PLANES Y PAQUETES</span></a></li>
                                        <li><a href="mantenimiento-noticias.php?cat=E&t=BLOG"><i class="glyphicon glyphicon-th-list"></i> <span>BLOG</span></a></li>
                                    <?php } ?>
                                </ul>
                            </li>





                        </ul>
                    </li>
                <?php } ?>

                <li><a href="data/usuario.php?op=salir"><i class="entypo-logout right"></i> Salir</a></li>


            </ul>

        </div>

    </div>

    <div class="main-content">

        <div class="row">

            <!-- Profile Info and Notifications -->
            <div class="col-md-6 col-sm-8 clearfix">

                <ul class="user-info pull-left pull-none-xsm">

                    <!-- Profile Info -->
                    <li class="profile-info dropdown"><!-- add class "pull-right" if you want to place this from right -->
                        <a href="escritorio.php" class="dropdown-toggle">
                            <?php if(file_exists('files/usuarios/'.$_COOKIE['imagen'])){ ?>
                                <img src="files/usuarios/<?php echo $_COOKIE['imagen']; ?>" alt="" class="img-circle" width="44" />
                            <?php }else{ ?>
                                <img src="files/usuarios/0.jpg" alt="" class="img-circle" width="44" />
                            <?php } ?>
                            <?=$_COOKIE['nombre']?> (N°<?=$_COOKIE['id']?>)
                        </a>
                
                    </li>

                    
                </ul>



            </div>
            
          
            
            
            
            <!-- Raw Links -->
            <div class="col-md-6 col-sm-4 clearfix hidden-sm">

                <ul class="list-inline links-list pull-right">

                <input type="hidden" name="tipocambiosistema" id="tipocambiosistema" value="<?=$tchoy['venta']?>" >

                   <li>
     
                    <?php 
	
	if($mostrarcc['estado']=='0'||!$mostrarcc){ 

?>
<a href="escritorio.php" >
							IR ESCRITORIO A ABRIR CAJA <span class="glyphicon glyphicon-shopping-cart"></span>
<?php 
	
}else{ 
					

?>
<a href="escritorio.php" >
							IR ESCRITORIO A CERRAR CAJA <span class="glyphicon glyphicon-shopping-cart"></span>			
<?php } ?>

  </li>




                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            TIPO CAMBIO:
                            <span class="badge badge-info"><?=$tchoy['venta']?></span>
                        </a>
                    </li>

<?php if ($_COOKIE['vencimiento']==1){ ?>
	<li>
<a href="mentenimiento-descargas.php" >
							DESCARGAS <span class="glyphicon glyphicon-cloud-download"></span>
						</a>
</li>
<?php }else{ ?>			  
	<li>
<a>
							DESCARGAS <span class="glyphicon glyphicon-cloud-download"></span>
						</a>
</li>			  
<?php } ?>

<?php if ($_COOKIE['vencimiento']==1){ ?>
	<li>
<a href="modelos/backup.php" >
                            BACKUP DB <span class="glyphicon glyphicon-cloud-download"></span>
                        </a>
</li>
<?php }else{ ?>			  
	<li>
<a>
							BACKUP DB <span class="glyphicon glyphicon-cloud-download"></span>
						</a>
</li>			  
<?php } ?>


                    <li class="sep"></li>

                    <li>
                        <a href="data/usuario.php?op=salir">
                            Salir <i class="entypo-logout right"></i>
                        </a>
                    </li>
                </ul>

            </div>



        </div>

        <hr />
		