<script src="assets/js/sweetalert2@10"></script>
<link rel="stylesheet" href="assets/js/fullcalendar-2/fullcalendar.min.css">

<link rel="stylesheet" href="assets/js/selectboxit/jquery.selectBoxIt.css">
<link rel="stylesheet" href="assets/js/daterangepicker/daterangepicker-bs3.css">
<link rel="stylesheet" href="assets/js/icheck/skins/minimal/_all.css">
<link rel="stylesheet" href="assets/js/icheck/skins/square/_all.css">
<link rel="stylesheet" href="assets/js/icheck/skins/flat/_all.css">
<link rel="stylesheet" href="assets/js/icheck/skins/futurico/futurico.css">
<link rel="stylesheet" href="assets/js/icheck/skins/polaris/polaris.css">

<link rel="stylesheet" href="assets/css/font-icons/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="assets/js/jvectormap/jquery-jvectormap-1.2.2.css">
<link rel="stylesheet" href="assets/js/rickshaw/rickshaw.min.css">

<script src="assets/js/gsap/TweenMax.min.js"></script>
<script src="assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/joinable.js"></script>
<script src="assets/js/resizeable.js"></script>
<script src="assets/js/neon-api.js"></script>
<script src="assets/js/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>

<script src="assets/js/neon-custom.js"></script>
<script src="assets/js/neon-demo.js"></script>

<script src="plugins/select2/js/select2.full.min.js"></script>

<!-- ajax-bootstrap-select depende de bootstrap-select -->
<script src="plugins/ajax-bootstrap-select.min.js"></script>

<!-- ✅ DEJAMOS SOLO 1 bootstrap-select (NO DUPLICAR) -->
<script src="assets/js/bootstrap-select.min.js"></script>

<script src="assets/js/bootstrap-tagsinput.min.js"></script>
<script src="assets/js/typeahead.min.js"></script>

<script src="plugins/jquery-ui.js"></script>
<script src="assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
<script src="assets/js/bootstrap-datepicker.js"></script>
<script src="assets/js/bootstrap-timepicker.min.js"></script>
<script src="assets/js/bootstrap-colorpicker.min.js"></script>
<script src="assets/js/daterangepicker/daterangepicker.js"></script>
<script src="assets/js/jquery.multi-select.js"></script>
<script src="assets/js/icheck/icheck.min.js"></script>

<script src="assets/js/jvectormap/jquery-jvectormap-europe-merc-en.js"></script>
<script src="assets/js/jquery.sparkline.min.js"></script>
<script src="assets/js/rickshaw/vendor/d3.v3.js"></script>
<script src="assets/js/rickshaw/rickshaw.min.js"></script>
<script src="assets/js/raphael-min.js"></script>
<script src="assets/js/morris.min.js"></script>
<script src="assets/js/toastr.js"></script>

<script src="assets/js/bootbox.min.js"></script>
<script type="text/javascript" src="assets/js/JsBarcode.all.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.PrintArea.js"></script>

<!-- DATATABLES -->
<script src="plugins/datatables/datatables.min.js"></script>
<script src="plugins/datatables/dataTables.buttons.min.js"></script>
<script src="plugins/datatables/buttons.html5.min.js"></script>
<script src="plugins/datatables/buttons.colVis.min.js"></script>
<script src="plugins/datatables/jszip.min.js"></script>
<script src="plugins/datatables/pdfmake.min.js"></script>
<script src="plugins/datatables/vfs_fonts.js"></script>

<script src="assets/js/moment.min.js"></script>
<script src="assets/js/fullcalendar-2/fullcalendar.min.js"></script>
<script type="text/javascript" src="plugins/charts/Chart.min.js"></script>

<script src="assets/js/neon-chat.js"></script>
<script src='plugins/es.js'></script>

<link type="text/css" href="plugins/dataTables.checkboxes.css" rel="stylesheet" />
<script type="text/javascript" src="plugins/dataTables.checkboxes.min.js"></script>

<?php
// ==============================
// FIX: primero cargar config
// ==============================
$hoy = date("Y-m-d");
$idusuario = $_COOKIE["idusuario"];

$sql3 = "SELECT * FROM config WHERE id='".$_COOKIE['id']."' ";
$fa   = ejecutarConsultaSimpleFila($sql3);

// Ahora sí podemos usar $fa['fechafin']
$vence = '0';
$dias  = '';

if ($fa && isset($fa['fechafin'])) {
  $fechavence  = date("Y-m-d", strtotime($fa['fechafin']."- 5 days"));
  $fechavence2 = date("Y-m-d", strtotime($fa['fechafin']."- 4 days"));
  $fechavence3 = date("Y-m-d", strtotime($fa['fechafin']."- 3 days"));
  $fechavence4 = date("Y-m-d", strtotime($fa['fechafin']."- 2 days"));
  $fechavence5 = date("Y-m-d", strtotime($fa['fechafin']."- 1 days"));

  if ($fechavence == $hoy)      { $dias='5'; $vence='1'; }
  else if ($fechavence2 == $hoy){ $dias='4'; $vence='1'; }
  else if ($fechavence3 == $hoy){ $dias='3'; $vence='1'; }
  else if ($fechavence4 == $hoy){ $dias='2'; $vence='1'; }
  else if ($fechavence5 == $hoy){ $dias='1'; $vence='1'; }
  else if ($fa['fechafin'] == $hoy){ $dias='0'; $vence='1'; }
}

$sqlcs="SELECT COUNT(*) AS pendientes
        FROM venta
        WHERE idempresa='$_COOKIE[id]'
          AND (estado='0' OR estado='4')
          AND (txtID_TIPO_DOCUMENTO ='01' OR txtID_TIPO_DOCUMENTO ='03' OR txtID_TIPO_DOCUMENTO ='07')
          AND beta='$fa[tipo]' ";
$ps1= ejecutarConsultaSimpleFila($sqlcs);
$pendientes = $ps1 ? $ps1['pendientes'] : 0;

$sqlcs2="SELECT COUNT(*) AS env
         FROM venta
         WHERE idempresa='$_COOKIE[id]'
           AND (estado='1' OR estado='5')
           AND (txtID_TIPO_DOCUMENTO ='01' OR txtID_TIPO_DOCUMENTO ='03' OR txtID_TIPO_DOCUMENTO ='07')
           AND beta='$fa[tipo]' ";
$ps2= ejecutarConsultaSimpleFila($sqlcs2);
$env = $ps2 ? $ps2['env'] : 0;
?>

<script type="text/javascript">
<?php if($pendientes!='0'){ ?>
setTimeout(function(){
  var opts = {
    "closeButton": true,
    "debug": false,
    "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
    "toastClass": "black",
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  };
  toastr.success('Tiene (<?=$pendientes?>) documentos PENDIENTES.', "ALERTA!!!", opts);
}, 2500);
<?php } ?>

<?php if($env!='0'){ ?>
setTimeout(function(){
  var opts = {
    "closeButton": true,
    "debug": false,
    "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  };
  toastr.error('Tiene (<?=$env?>) por REENVIAR o revisar en SUNAT.', "ALERTA!!!", opts);
}, 2000);
<?php } ?>

<?php if($vence=='1'){ ?>
setTimeout(function(){
  var opts = {
    "closeButton": true,
    "debug": false,
    "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "8000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  };
  toastr.warning('Renueva tu cuenta te quedan solo (<?=$dias?> Días)', "ALERTA!!!", opts);
}, 3000);
<?php } ?>
</script>

<script>
function backup(){
  window.location.href = 'modelos/descargas.php?op=backup';
}
</script>
