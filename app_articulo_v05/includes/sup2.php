<?php 
$hoy = date("Y-m-d");
$idusuario=$_COOKIE["idusuario"];
//$_COOKIE['imagen']
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$fa=ejecutarConsultaSimpleFila($sql3);

$sqluser="SELECT *FROM usuario WHERE idusuario='$idusuario' ";
$user= ejecutarConsultaSimpleFila($sqluser);

$sqlcc="SELECT *FROM cajas WHERE idlocal='$_COOKIE[idlocal]' AND id_usuario='$_COOKIE[idusuario]' ORDER BY id DESC ";
$mostrarcc= ejecutarConsultaSimpleFila($sqlcc);

$sqlcat="SELECT *FROM tipo_cambio WHERE fecha='$hoy' AND idempresa='0' ";
$tchoy= ejecutarConsultaSimpleFila($sqlcat);

if(!$tchoy){
$sqlcat="SELECT *FROM tipo_cambio ORDER BY fecha DESC ";
$tchoy= ejecutarConsultaSimpleFila($sqlcat);	
}

if($fa['fechafin']>$hoy){
$_COOKIE["vencimiento"]="1";
}else if($fa['estado']!='1'){ 
$_COOKIE["vencimiento"]="0";
}else{ 
$_COOKIE["vencimiento"]="0";  
}
	
?>


<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
	
	<div class="sidebar-menu" >

		<div class="sidebar-menu-inner">
			
			<header class="logo-env">

				<!-- logo -->
				<div class="logo">
					<a href="escritorio.php">
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
				
				
<?php 
            if ($_COOKIE['escritorio']==1)        {
              echo '<li>
              <a href="escritorio.php">
              <i class="entypo-gauge"></i> <span>Escritorio</span>
              </a>
            </li>';
            }
            ?>

<?php if ($_COOKIE['configuracion']==1) { ?>  
<li class="has-sub">
              <a href="#">
                <i class="fa fa-cog"></i> <span>Maestros</span>
              </a>
              <ul class="treeview-menu">
<li><a href="articulo.php"><i class="fa fa-circle-o"></i> Artículos</a></li>
<li><a href="categoria.php?nivel=1&t=MARCA"><i class="fa fa-circle-o"></i> Marca</a></li>
<li><a href="categoria.php?nivel=0&t=GRUPO"><i class="fa fa-circle-o"></i> Grupo</a></li>
<li><a href="categoria.php?nivel=3&t=LÍNEA"><i class="fa fa-circle-o"></i> Líneas</a></li>
<li><a href="categoria.php?nivel=4&t=ÁREAS / CENTRO COSTOS"><i class="fa fa-circle-o"></i> Áreas / Centro costos</a></li>	

<li><a href="proveedor.php"><i class="fa fa-circle-o"></i> Proveedores</a></li>
<li><a href="cliente.php"><i class="fa fa-circle-o"></i> Clientes</a></li>
				  
<li><a href="perfil-tipopago.php"><span class="glyphicon glyphicon-usd"></span> Tipo de pago</a></li>		  
<li><a href="perfil-series.php"><i class="fa fa-circle-o"></i> Series</a></li>
<li><a href="usuario.php?cat=1&t=VENDEDORES"><i class="fa fa-circle-o"></i> Vendedores</a></li>
<li><a href="categoria.php?nivel=2&t=SECTORES"><i class="fa fa-circle-o"></i> Sectores</a></li>			  
				  
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
<li><a href="#"><i class="fa fa-circle-o"></i> Cotización <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
<li><a href="#"><i class="fa fa-circle-o"></i> Nota de pedido <span class="badge badge-secondary badge-roundless">Caducado</span></a></li> 		  
<?php } ?>

              </ul>
            </li>

<?php } ?>
	

<?php if ($_COOKIE['ventas']==1){ ?>
	
<li class="has-sub">
              <a href="#">
                <i class="fa fa-shopping-cart"></i>
                <span>Ventas</span>
              </a>
<ul class="treeview-menu">

<?php if ($_COOKIE['vencimiento']==1){ ?>
<li><a href="venta.php"><i class="fa fa-circle-o"></i> Venta Tradicional</a></li>
<li><a href="venta-rapida.php"><i class="fa fa-circle-o"></i> Venta Rápida</a></li>
<li><a href="credito-debito.php"><i class="fa fa-circle-o"></i>Nota Credito / Debito</a></li>
<?php }else{ ?>			  
<li><a href="#"><i class="fa fa-circle-o"></i> Venta Tradicional <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
<li><a href="#"><i class="fa fa-circle-o"></i> Venta Rápida <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
<li><a href="#"><i class="fa fa-circle-o"></i>Nota Credito / Debito <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>				  
<?php } ?>				  
<li><a href="venta-orden.php"><i class="fa fa-circle-o"></i> Orden de carga</a></li>
<li><a href="reporte-cobrar.php?t=CUENTAS POR COBRAR&cat=0"><i class="fa fa-circle-o"></i> Cuentas por cobrar</a></li>
<li><a href="cliente.php"><i class="fa fa-circle-o"></i> Clientes</a></li>
</ul>
</li>
				
<?php 
}
}else{ 
?>
<li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Pedidos <span class="badge badge-secondary badge-roundless">Premium</span></a></li>		
				
<li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Ventas <span class="badge badge-secondary badge-roundless">Premium</span></a></li>				
				
<?php } ?>
				
<?php if ($_COOKIE['pkardex']==1){ ?>
<?php if ($_COOKIE['almacen']==1){ ?>
<li class="has-sub">
<a href="#"><i class="entypo-monitor"></i>
                <span>Almacén</span>
              </a>
<ul class="treeview-menu">
<li><a href="almacen-traslado.php"><i class="fa fa-circle-o"></i> Salida de almacén</a></li>
<li><a href="almacen-kardex.php"><i class="fa fa-circle-o"></i> Reporte Kardex</a></li>
<li><a href="almacen-stock.php"><i class="fa fa-circle-o"></i> Reporte Stock</a></li>
	
<li><a href="reporte-almacen-ingreso.php"><i class="fa fa-circle-o"></i> Reporte Ingreso de almacen</a></li>
<li><a href="reporte-almacen-salida.php"><i class="fa fa-circle-o"></i> Reporte Salida de almacen</a></li>
	
<li><a href="articulo.php"><i class="fa fa-circle-o"></i> Artículos</a></li>
<li><a href="categoria.php?nivel=4&t=ÁREAS / CENTRO COSTOS"><i class="fa fa-circle-o"></i> Áreas / Centro costos</a></li>	
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
<li><a href="ingreso.php?cat=0&t=INGRESOS"><i class="fa fa-circle-o"></i> Compras/Almacen </a></li>
<li><a href="ingreso.php?cat=1&t=COMPRAS"><i class="fa fa-circle-o"></i> Gastos</a></li>
<li><a href="ingreso.php?cat=2&t=ORDEN DE COMPRA"><i class="fa fa-circle-o"></i> Orden de compras</a></li>
<li><a href="ingreso.php?cat=3&t=SOLICITUD DE COMPRA"><i class="fa fa-circle-o"></i> Solicitud de compra</a></li>
<li><a href="reporte-cobrar.php?t=CUENTAS POR PAGAR&cat=1"><i class="fa fa-circle-o"></i> Cuentas por pagar</a></li>
<li><a href="ingreso-gastos.php"><i class="fa fa-circle-o"></i> Conf. Otros gastos</a></li>				  
<!--ACTIVOS FIJOS!-->
 
				  
				  
<?php }else{ ?>
<li><a href="#"><i class="fa fa-circle-o"></i> Compras/Almacen <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
<li><a href="#"><i class="fa fa-circle-o"></i> Gastos <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>	
<li><a href="#"><i class="fa fa-circle-o"></i> Orden de compras <span class="badge badge-secondary badge-roundless">Caducado</span></a></li>
<?php } ?>

              </ul>
            </li>
				
			
				
<?php } ?>			
<?php }else{ ?>	
<li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Almacen <span class="badge badge-secondary badge-roundless">Premium</span></a></li>
<li><a href="no-accesos.php"><i class="fa fa-circle-o"></i> Compras <span class="badge badge-secondary badge-roundless">Premium</span></a></li>
				
<?php } ?>

				
		
				
				
<?php if ($_COOKIE['acceso']==1){ ?>
<li class="has-sub">
<a href="#"><i class="fa fa-users"></i><span>Acceso</span></a>
<ul class="treeview-menu">
<li><a href="usuario.php?cat=0&t=USUARIOS"><i class="fa fa-circle-o"></i> Usuarios</a></li>
</ul>
</li>
<?php } ?>

<?php if ($_COOKIE['reportes']==1) { ?>
<li class="has-sub">
              <a href="#">
                <i class="fa fa-bar-chart"></i> <span> Reportes</span>
              </a>
              <ul class="treeview-menu">
<?php if ($_COOKIE['facturacione']==1) { ?>          
<li><a href="ventasfechacliente.php"><i class="fa fa-circle-o"></i> Ventas por Documento</a></li>
<li><a href="reporte-detalle.php"><i class="fa fa-circle-o"></i> Ventas por Detalle</a></li> 
<li><a href="reporte-rentabilidad.php"><i class="fa fa-circle-o"></i> Rentabilidad</a></li> 
<li><a href="comprasfecha.php"><i class="fa fa-circle-o"></i> Consulta Compras</a></li>
<li><a href="reportes-cajas.php"><i class="fa fa-circle-o"></i> Consulta Cajas</a></li>
<?php } ?>         
<li><a href="reportes-comisiones.php"><i class="fa fa-circle-o"></i> Comisiones vendedores</a></li>

              </ul>
            </li>
<?php } ?>
<?php if ($_COOKIE['guiar']==1) { ?>
<li class="has-sub">
              <a href="#">
<i class="fa fa-truck"></i> <span>Guía de remisión</span>
              </a>
              <ul class="treeview-menu">
<li><a href="guia-remitente.php"><i class="fa fa-circle-o"></i> Guía Remisión</a></li>
<li><a href="mantenimiento-etransporte.php"><i class="fa fa-circle-o"></i> Mant. Empr. Trans.</a></li>
<li><a href="mantenimiento-vehiculo.php"><i class="fa fa-circle-o"></i> Mant. vehículos</a></li>
<li><a href="mantenimiento-chofer.php"><i class="fa fa-circle-o"></i> Mant. chofer</a></li>         
              </ul>
            </li>
<?php } ?> 
<?php if ($_COOKIE['facturacione']==1) { ?>           
<li class="has-sub">
<a href="#"><i class="fa fa-cloud-upload"></i> <span>Facturación Electrónica</span></a>
<ul class="treeview-menu">
<li><a href="resumen-diario.php"><i class="fa fa-circle-o"></i> Resumen diario</a></li>
<li><a href="bajas-facturas.php"><i class="fa fa-circle-o"></i> Bajas de documentos</a></li>       
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
			  
<li><a href="sucursal.php"><i class="fa fa-circle-o"></i> Sucursales</a></li>
<li><a href="perfil-logo.php"><i class="fa fa-circle-o"></i> Logotipo</a></li>

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
<a href="#" class="dropdown-toggle" data-toggle="dropdown">
<?php if(file_exists('files/usuarios/'.$_COOKIE['imagen'])){ ?>
<img src="files/usuarios/<?php echo $_COOKIE['imagen']; ?>" alt="" class="img-circle" width="44" />
<?php }else{ ?>
<img src="files/usuarios/0.jpg" alt="" class="img-circle" width="44" />
<?php } ?>
<?=$_COOKIE['nombre']?> (N°<?=$_COOKIE['id']?>)
</a>
		
						<ul class="dropdown-menu">
		
							<!-- Reverse Caret -->
							<li class="caret"></li>
		
							<!-- Profile sub-links -->
							<li>
								<a href="extra-timeline.html">
									<i class="entypo-user"></i>
									Editar perfil
								</a>
							</li>
		
							<li>
								<a href="mailbox.html">
									<i class="entypo-mail"></i>
								Mensajes
								</a>
							</li>
		
							<li>
								<a href="extra-calendar.html">
									<i class="entypo-calendar"></i>
								Historial de pagos
								</a>
							</li>
						</ul>
					</li>
		
				</ul>
				
				<ul class="user-info pull-left pull-right-xs pull-none-xsm">
		
					<!-- Raw Notifications -->
					<li class="notifications dropdown">
						
						
						
						
		
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
							<i class="entypo-attention"></i>
							<span class="badge badge-info">6</span>
						</a>
		
						<ul class="dropdown-menu">
							<li class="top">
								<p class="small">
									<a href="#" class="pull-right">Mark all Read</a>
									You have <strong>3</strong> new notifications.
								</p>
							</li>
							
							<li>
								<ul class="dropdown-menu-list scroller">
									<li class="unread notification-success">
										<a href="#">
											<i class="entypo-user-add pull-right"></i>
											
											<span class="line">
												<strong>New user registered</strong>
											</span>
											
											<span class="line small">
												30 seconds ago
											</span>
										</a>
									</li>
									
									<li class="unread notification-secondary">
										<a href="#">
											<i class="entypo-heart pull-right"></i>
											
											<span class="line">
												<strong>Someone special liked this</strong>
											</span>
											
											<span class="line small">
												2 minutes ago
											</span>
										</a>
									</li>
									
									<li class="notification-primary">
										<a href="#">
											<i class="entypo-user pull-right"></i>
											
											<span class="line">
												<strong>Privacy settings have been changed</strong>
											</span>
											
											<span class="line small">
												3 hours ago
											</span>
										</a>
									</li>
									
									<li class="notification-danger">
										<a href="#">
											<i class="entypo-cancel-circled pull-right"></i>
											
											<span class="line">
												John cancelled the event
											</span>
											
											<span class="line small">
												9 hours ago
											</span>
										</a>
									</li>
									
									<li class="notification-info">
										<a href="#">
											<i class="entypo-info pull-right"></i>
											
											<span class="line">
												The server is status is stable
											</span>
											
											<span class="line small">
												yesterday at 10:30am
											</span>
										</a>
									</li>
									
									<li class="notification-warning">
										<a href="#">
											<i class="entypo-rss pull-right"></i>
											
											<span class="line">
												New comments waiting approval
											</span>
											
											<span class="line small">
												last week
											</span>
										</a>
									</li>
								</ul>
							</li>
							
							<li class="external">
								<a href="#">View all notifications</a>
							</li>
						</ul>
		
					</li>
		
					<!-- Message Notifications -->
					<li class="notifications dropdown">
		
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
							<i class="entypo-mail"></i>
							<span class="badge badge-secondary">10</span>
						</a>
		
						<ul class="dropdown-menu">
							<li>
								<form class="top-dropdown-search">
									
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Search anything..." name="s" />
									</div>
									
								</form>
								
								<ul class="dropdown-menu-list scroller">
									<li class="active">
										<a href="#">
											<span class="image pull-right">
												<img src="assets/images/thumb-1@2x.png" width="44" alt="" class="img-circle" />
											</span>
											
											<span class="line">
												<strong>Luc Chartier</strong>
												- yesterday
											</span>
											
											<span class="line desc small">
												This ain’t our first item, it is the best of the rest.
											</span>
										</a>
									</li>
									
									<li class="active">
										<a href="#">
											<span class="image pull-right">
												<img src="assets/images/thumb-2@2x.png" width="44" alt="" class="img-circle" />
											</span>
											
											<span class="line">
												<strong>Salma Nyberg</strong>
												- 2 days ago
											</span>
											
											<span class="line desc small">
												Oh he decisively impression attachment friendship so if everything. 
											</span>
										</a>
									</li>
									
									<li>
										<a href="#">
											<span class="image pull-right">
												<img src="assets/images/thumb-3@2x.png" width="44" alt="" class="img-circle" />
											</span>
											
											<span class="line">
												Hayden Cartwright
												- a week ago
											</span>
											
											<span class="line desc small">
												Whose her enjoy chief new young. Felicity if ye required likewise so doubtful.
											</span>
										</a>
									</li>
									
									<li>
										<a href="#">
											<span class="image pull-right">
												<img src="assets/images/thumb-4@2x.png" width="44" alt="" class="img-circle" />
											</span>
											
											<span class="line">
												Sandra Eberhardt
												- 16 days ago
											</span>
											
											<span class="line desc small">
												On so attention necessary at by provision otherwise existence direction.
											</span>
										</a>
									</li>
								</ul>
							</li>
							
							<li class="external">
								<a href="mailbox.html">All Messages</a>
							</li>
						</ul>
		
					</li>
		
					<!-- Task Notifications -->
					<li class="notifications dropdown">
		
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
							<i class="entypo-list"></i>
							<span class="badge badge-warning">1</span>
						</a>
		
						<ul class="dropdown-menu">
							<li class="top">
								<p>You have 6 pending tasks</p>
							</li>
							
							<li>
								<ul class="dropdown-menu-list scroller">
									<li>
										<a href="#">
											<span class="task">
												<span class="desc">Procurement</span>
												<span class="percent">27%</span>
											</span>
										
											<span class="progress">
												<span style="width: 27%;" class="progress-bar progress-bar-success">
													<span class="sr-only">27% Complete</span>
												</span>
											</span>
										</a>
									</li>
									<li>
										<a href="#">
											<span class="task">
												<span class="desc">App Development</span>
												<span class="percent">83%</span>
											</span>
											
											<span class="progress progress-striped">
												<span style="width: 83%;" class="progress-bar progress-bar-danger">
													<span class="sr-only">83% Complete</span>
												</span>
											</span>
										</a>
									</li>
									<li>
										<a href="#">
											<span class="task">
												<span class="desc">HTML Slicing</span>
												<span class="percent">91%</span>
											</span>
											
											<span class="progress">
												<span style="width: 91%;" class="progress-bar progress-bar-success">
													<span class="sr-only">91% Complete</span>
												</span>
											</span>
										</a>
									</li>
									<li>
										<a href="#">
											<span class="task">
												<span class="desc">Database Repair</span>
												<span class="percent">12%</span>
											</span>
											
											<span class="progress progress-striped">
												<span style="width: 12%;" class="progress-bar progress-bar-warning">
													<span class="sr-only">12% Complete</span>
												</span>
											</span>
										</a>
									</li>
									<li>
										<a href="#">
											<span class="task">
												<span class="desc">Backup Create Progress</span>
												<span class="percent">54%</span>
											</span>
											
											<span class="progress progress-striped">
												<span style="width: 54%;" class="progress-bar progress-bar-info">
													<span class="sr-only">54% Complete</span>
												</span>
											</span>
										</a>
									</li>
									<li>
										<a href="#">
											<span class="task">
												<span class="desc">Upgrade Progress</span>
												<span class="percent">17%</span>
											</span>
											
											<span class="progress progress-striped">
												<span style="width: 17%;" class="progress-bar progress-bar-important">
													<span class="sr-only">17% Complete</span>
												</span>
											</span>
										</a>
									</li>
								</ul>
							</li>
							
							<li class="external">
								<a href="#">See all tasks</a>
							</li>
						</ul>
		
					</li>
		
				</ul>
		
			</div>
		
		
			<!-- Raw Links -->
			<div class="col-md-6 col-sm-4 clearfix hidden-xs">
		
				<ul class="list-inline links-list pull-right">
<li>					
<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
							TIPO CAMBIO: 
							<span class="badge badge-info"><?=$tchoy['venta']?></span>
						</a>
</li>
					
					
<li>
						<a href="javascript:void(0)" onclick="backup()" >
							BACKUP <span class="glyphicon glyphicon-cloud-download"></span>
						</a>
					</li>
					
					
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
		