//Función que se ejecuta al inicio
$.jgrid.no_legacy_api = true;
$.jgrid.useJSON = true;
var tabla;
var lst = '';
        var tbl = '';
        var frm = '';
var formenvio='0';
var seleccionado='0';
var precio='0';
var porigv=$("#porcentajeigv").val();
porigv=porigv/100;
porigv=porigv+1.00;	
var impuesto=porigv;

// ======================================================
// SELECT2 - MED/PAGO y F.PAGO/M.PAGO (POR EMPRESA)
// - #medio  => data/venta.php?op=select_tipopago (caja_tipopago.descripcion)
// - #fpago_mpago  => data/venta.php?op=select_tipopago_persona (caja_tipopago_persona.descripcion)
// Nota: Ambos endpoints filtran por empresa logueada (cookie id / idempresa).
// ======================================================
function initSelect2MedioPago(){
  if (typeof $.fn.select2 === "undefined") return;
  var $el = $("#medio");
  if(!$el.length) return;

  try { $el.select2("destroy"); } catch(e){}

  $el.select2({
    placeholder: "-SELECCIONE-",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "data/venta.php?op=select_tipopago",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { searchTerm: params.term || "" };
      },
      processResults: function (response) {
        return { results: response || [] };
      },
      cache: true
    }
  });
}

function initSelect2FormaPagoPersona(){
  if (typeof $.fn.select2 === "undefined") return;
  var $el = $("#fpago_mpago");
  if(!$el.length) return;

  try { $el.select2("destroy"); } catch(e){}

  $el.select2({
    placeholder: "-SELECCIONE-",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "data/venta.php?op=select_tipopago_persona",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { searchTerm: params.term || "" };
      },
      processResults: function (response) {
        return { results: response || [] };
      },
      cache: true
    }
  });
}



function _lockPagoMedioVenta(lock){
  var lk = !!lock;
  var pe = lk ? 'none' : 'auto';
  var bg = lk ? '#f5f5f5' : '';

  var $pago = $('#pago');
  $pago.css('pointer-events', pe);
  $pago.css('background-color', bg);

  var $medio = $('#medio');
  $medio.css('pointer-events', pe);
  $medio.css('background-color', bg);

  // Cuando #medio está con Select2, bloquear también su contenedor visual
  try{
    var $s2 = $medio.next('.select2');
    if($s2.length){
      $s2.css('pointer-events', pe);
      $s2.css('opacity', lk ? '0.85' : '1');
    }
  }catch(e){}
}

function _lockMonedaVenta(lock){
  var $m = $('#txtMONEDA');
  if(!$m.length){ return; }
  $m.prop('disabled', !!lock);
}

function _validarFpagoSeleccion(){
  var v = String($('#fpago_mpago').val() || '').trim();
  if(v === '' || v === '0' || v.toLowerCase() === 'null'){
    var msg = 'Debe seleccionar F.Pago/M.Pago antes de agregar productos o servicios.';
    try{
      if(typeof Swal !== 'undefined' && Swal.fire){ Swal.fire({icon:'warning', title:'Validación', text: msg}); }
      else{ alert(msg); }
    }catch(e){ alert(msg); }
    return false;
  }
  return true;
}

function _sumarDiasYmdVenta(fechaYmd, dias){
  try{
    var p = String(fechaYmd || '').split('-');
    if(p.length !== 3){ return fechaYmd; }
    var d = new Date(parseInt(p[0],10), parseInt(p[1],10)-1, parseInt(p[2],10));
    d.setDate(d.getDate() + parseInt(dias,10));
    var y = d.getFullYear();
    var m = ('0' + (d.getMonth()+1)).slice(-2);
    var da = ('0' + d.getDate()).slice(-2);
    return y + '-' + m + '-' + da;
  }catch(e){ return fechaYmd; }
}

function _generarCuotasSegunFpago(done){
  var cb = (typeof done === 'function') ? done : function(){};
  var fpago = String($('#fpago_mpago').val() || '').trim();
  if(!fpago){
    try{ Swal.fire({icon:'warning', title:'Validación', text:'Debe seleccionar F.Pago/M.Pago para generar cuotas.'}); }catch(e){ alert('Debe seleccionar F.Pago/M.Pago para generar cuotas.'); }
    cb(false); return;
  }

  $.getJSON('data/venta.php', { op: 'cfg_tipopago_persona', id: fpago })
    .done(function(cfg){
      var cuotas = parseInt(cfg && cfg.cuotas, 10);
      if(isNaN(cuotas) || cuotas <= 0){ cuotas = 1; }
      var dias = parseInt(cfg && cfg.dias, 10);
      if(isNaN(dias) || dias <= 0){ dias = 30; }
      var diasCuota = parseInt(dias, 10);
      if(isNaN(diasCuota) || diasCuota <= 0){ diasCuota = 30; }

      var pagoSel = String($('#pago').val() || '').toUpperCase();
      var esCredito = (pagoSel.indexOf('CRED') !== -1) || (cuotas > 1);
      if(cuotas > 1){ $('#pago').val('CREDITO').trigger('change'); }
      if(!esCredito){ cb(true); return; }

      $('#properiodo2').val(cuotas > 1 ? 'SI' : 'NO');
      $('#letras2').val(String(cuotas));
      $('#periodo2').val(String(diasCuota));

      if(typeof t === 'undefined' || !t || !t.clear || !t.row || !t.row.add){ cb(true); return; }

      var total = _moneyFix2($('#txtTOTAL').val());
      var fechaDoc = String($('#txtFECHA_DOCUMENTO').val() || '');
      if(!fechaDoc){ cb(true); return; }
      var moneda = String($('#txtMONEDA').val() || 'PEN');
      var tcambio = _moneyFix2($('#costodolar').val());
      if(moneda === 'PEN'){ tcambio = '1.00'; }
      var tpagoTxt = String($('#medio option:selected').text() || $('#medio').val() || '');

      t.clear();
      var base = Math.round((total / cuotas) * 100) / 100;
      var acum = 0;
      for(var i=1;i<=cuotas;i++){
        var monto = base;
        if(i===cuotas){ monto = Math.round((total - acum) * 100) / 100; }
        acum = Math.round((acum + monto) * 100) / 100;
        var venc = _sumarDiasYmdVenta(fechaDoc, diasCuota * i);
        t.row.add([tpagoTxt, fechaDoc, venc, moneda, _moneyFix2(monto), _moneyFix2(tcambio), '<button type="button" class="btn btn-danger btn-xs del" ><span class="glyphicon glyphicon-trash"></span></button>']);
      }
      t.draw(false);
      cb(true);
    })
    .fail(function(){ cb(true); });
}
var cont=0;
var detalles=0;
$("#idventa").val("0");
$('#idventa').val(0);	

var tabladetalles= $('#detpedidos').DataTable({
autoWidth: false, //step 1
"searching": false,
"paging": false,
info: false,
      columnDefs: [
         { width: '5px', targets: 0 },
         { width: '46%', targets: 1 },
		 { width: '5%', targets: 2, className: 'text-right' },
         { width: '6%', targets: 3, className: 'text-right' },
         { width: '9%', targets: 4, className: 'text-right' },
         { width: '7%', targets: 5, className: 'text-right' },
		 { width: '8%', targets: 6, className: 'text-right' },
         { width: '7%', targets: 7, className: 'text-right' },
         { width: '8%', targets: 8, className: 'text-right' },
         { width: '2%', targets: 9 }
      ],
        fixedColumns: true, 
	"responsive": true 
});

function responsive_jqgrid(jqgrid) {
            jqgrid.find('.ui-jqgrid').addClass('clear-margin span12').css('width', '');
            jqgrid.find('.ui-jqgrid-view').addClass('clear-margin span12').css('width', '');
            jqgrid.find('.ui-jqgrid-view > div').eq(1).addClass('clear-margin span12').css('width', '').css('min-height', '0');
            jqgrid.find('.ui-jqgrid-view > div').eq(2).addClass('clear-margin span12').css('width', '').css('min-height', '0');
            jqgrid.find('.ui-jqgrid-sdiv').addClass('clear-margin span12').css('width', ''); 
      jqgrid.find('.ui-jqgrid-pager').addClass('clear-margin span12').css('width', '');
}
    
function CalcularArticulo() {
            var igv_div = parseFloat((18 / 100) + 1);
            var precio = $('#txtPRECIO_ARTICULO').val(); //filaArticulo.PRECIO_VENTA;//redondeo(parseFloat(importe / igv_div),2);
            var cantidad = $('#txtCANTIDAD_ARTICULO').val();
            var total = redondeo(parseFloat(precio) * parseFloat(cantidad), 2);
            var sub_total = parseFloat(total) / parseFloat(igv_div);
            var igv = parseFloat(total) - parseFloat(sub_total);
            $('#txtSUB_TOTAL_ARTICULO').val(redondeo(sub_total, 2));
            $('#txtIGV_ARTICULO').val(redondeo(igv, 2));
            $('#txtTOTAL_ARTICULO').val(redondeo(total, 2));
        }
	
function eliminarFila() {
			var id; var rowData; var igve; var precioe; var exo;
            var rowid = jQuery('#list').jqGrid('getGridParam', 'selrow');
	
var rowId =$("#list").jqGrid('getGridParam','selrow'); 
rowData = jQuery("#list").getRowData(rowId); 

console.log(rowData['IMPORTE']);
	
id=rowid.replace('jqg','');
id=parseInt(id-1);

if(rowData['IGV']=='0'){ 
var exo=$('#exonerado').val();
precioe=parseFloat(exo)-parseFloat(rowData['IMPORTE']);	
precioe=redondeo(precioe, 2);
$('#exonerado').val(precioe);
	
}
		
            var $grid = jQuery("#list");
            $grid.jqGrid('delRowData', rowid);

            calculateTotal();

        }

 function nuevo() {
            location.reload();
        }

function redondeo(numero, decimales) {
            var flotante = parseFloat(numero);
            var resultado = Math.round(flotante * Math.pow(10, decimales)) / Math.pow(10, decimales);
            return resultado;
        }


function _moneyRaw(val){
	if(val===undefined || val===null){ return '0'; }
	return String(val).replace(/,/g,'').trim();
}

function _moneyNum(val){
	var n = parseFloat(_moneyRaw(val));
	if(isNaN(n)){ return 0; }
	return n;
}

function _moneyFmt(val){
	var n = _moneyNum(val);
	return n.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
}

function _moneyFmtN(val, dec){
	var n = _moneyNum(val);
	return n.toLocaleString('en-US', {minimumFractionDigits:dec, maximumFractionDigits:dec});
}

function _formatearInputsDetallePedido(){
	try{
		$('#detpedidos input[id^="ctif"]').each(function(){ $(this).css({'text-align':'center','min-width':'62px'}); });
		$('#detpedidos input[id^="preciof"]').each(function(){
			$(this).css({'text-align':'right','min-width':'140px'});
			if(this===document.activeElement){ return; }
			$(this).val(_moneyFmtN($(this).val(),4));
		});
		$('#detpedidos input[id^="descuento"]').each(function(){
			$(this).css({'text-align':'right','min-width':'88px'});
			if(this===document.activeElement){ return; }
			$(this).val(_moneyFmtN($(this).val(),2));
		});
		$('#detpedidos input[id^="sub"], #detpedidos input[id^="igv"], #detpedidos input[id^="totf"]').each(function(){
			$(this).css({'text-align':'right','min-width':'118px'});
			if(this===document.activeElement){ return; }
			$(this).val(_moneyFmtN($(this).val(),2));
		});
	}catch(e){}
}

function initSelect2Detracciones(){
	if (typeof $.fn.select2 === 'undefined') return;
	var $el = $('#detracciones');
	if(!$el.length) return;
	try{ $el.select2('destroy'); }catch(e){}
	$el.select2({
		placeholder: '-SELECCIONE DETRACCIÓN-',
		width: '100%',
		dropdownParent: $('#menu1').length ? $('#menu1') : $('body')
	});
	try{
		$('#select2-detracciones-container').css({'min-width':'320px','display':'inline-block'});
		$el.next('.select2').css({'min-width':'340px'});
	}catch(e){}
}

$(document).off('focus.detpedfmt', '#detpedidos input[id^="preciof"], #detpedidos input[id^="descuento"]').on('focus.detpedfmt', '#detpedidos input[id^="preciof"], #detpedidos input[id^="descuento"]', function(){
	$(this).val(_moneyRaw($(this).val()));
});

$(document).off('blur.detpedfmt', '#detpedidos input[id^="preciof"], #detpedidos input[id^="descuento"]').on('blur.detpedfmt', '#detpedidos input[id^="preciof"], #detpedidos input[id^="descuento"]', function(){
	if($(this).attr('id').indexOf('preciof')===0){
		$(this).val(_moneyFmtN($(this).val(),4));
	}else{
		$(this).val(_moneyFmtN($(this).val(),2));
	}
});

function _moneyFix2(val){
	return _moneyNum(val).toFixed(2);
}

function _aplicarFormatoTotalesVenta(){
	var ids = [
		'#gravadas','#txtSUB_TOTAL','#gratuita','#exoneradof','#inafecta','#txtIGV','#txtTOTAL',
		'#valref','#comisiont','#descuentotot','#totanticipo','#totsaldo','#totpagar','#totalf','#puntos'
	];
	ids.forEach(function(sel){
		var $el=$(sel);
		if(!$el.length){ return; }
		$el.css('text-align','right');
		$el.val(_moneyFmt($el.val()));
	});

	var $totalPagar = $('#totalpagarf');
	if($totalPagar.length){
		$totalPagar.css({'text-align':'right','display':'inline-block','width':'100%'});
		$totalPagar.text(_moneyFmt($totalPagar.text()));
	}
}

$(document).ready(function(){
	_aplicarFormatoTotalesVenta();
	$('#gravadas,#txtSUB_TOTAL,#gratuita,#exoneradof,#inafecta,#txtIGV,#txtTOTAL,#valref,#comisiont,#descuentotot,#totanticipo,#totsaldo,#totpagar,#totalf,#puntos')
		.on('blur', function(){
			$(this).css('text-align','right');
			$(this).val(_moneyFmt($(this).val()));
		});
});

function clientesfinales(idcliente, cliente){

	var adelanto=$("#opadelanto").val();
	
	var TipoComprobate = $('#txtID_TIPO_DOCUMENTO').val();
		
				if (TipoComprobate == '01') {
					tipodoc='RUC';
			} else if (TipoComprobate == '03') {
					tipodoc='DNI';
				}else {
					tipodoc='OTROS';
				}
		
	if(adelanto=='NO'){

	if(idcliente!=''){
		
	var data = {
		id: idcliente,
		text: cliente
	};
	var newOption = new Option(data.text, data.id, false, false);
	$('#txtID_CLIENTE').append(newOption).trigger('change');
	$('#addcliente').attr("disabled", false);
		$('#addclientes').attr("disabled", true);
	}else{
	$('#addcliente').attr("disabled", false);
		
	$("#txtID_CLIENTE").select2({
			placeholder: "-SELECCIONE CLIENTE-",
			ajax: {
				url: "data/venta.php?op=buscarcliente&tipodoc="+tipodoc,
				type: "get",
				dataType: 'json',
				delay: 350,
				data: function (params) {				
					return {
						searchTerm: params.term // search term
					};
				},
				processResults: function (response) {
					return {
						results: response
					};
				},
				cache: true
			},
		});
	}	
	}	
	
}
//Función limpiar
function limpiar(){
	$("#txtID_TIPO_DOCUMENTO").val("03");
	$("#txtID_CLIENTE").val("");
	$("#cliente").val("");
	$("#txtID_TIPO_DOCUMENTO").val("");
	$("#txtSERIE").val("");
	$("#txtNUMERO").val("")
	$("#txtTIPO_DOCUMENTO_CLIENTE").val("");
	$("#txtOBSERVACION").val("");
	$("#txtSUB_TOTALL").val("");
	$("#txtIGV").val("");
	$("#txtTOTAL").val("");
	$("#txtID_MONEDA").val("");
	$("#txtID_TIPO_DOCUMENTO_MODIFICA").val("");
	$("#txtNRO_DOC_MODIFICA").val("");
	$("#txtID_MOTIVO").val("");
	$(".filas").remove();
	$("#total").html("0");
	$("#idventa").val("0");
$("#valref").val("0");
$("#tipopaso").val("0");
$("#idguia").val("0");

$("#mach_id").val("");
$("#mach_numero").val("");
$("#mach_monto").val("0.00");
$("#mach_observaciones").val("0");


//Obtenemos la fecha actual
	var now = new Date();
	var day = ("0" + now.getDate()).slice(-2);
	var month = ("0" + (now.getMonth() + 1)).slice(-2);
	var today = now.getFullYear()+"-"+(month)+"-"+(day) ;
    $('#txtFECHA_DOCUMENTO').val(today);

    //Marcamos el primer tipo_documento
    
}
//Función mostrar formulario
function mostrarform(flag, nivel){
limpiar();
	var $menuToggle = $('.entypo-menu:visible').first();
	
$("#montodetraccion2").val('6000.00');

if (flag){
		if($menuToggle.length){
			$menuToggle.trigger('click');
		}
		_lockMonedaVenta(false);

if(nivel=='0'){
_lockPagoMedioVenta(true);
seriesfinales('03');
serie(1);	
}else{
_lockPagoMedioVenta(false);
}
ListClientepaciente();
contadocredito();
	
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		//$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
		
$("#miForm").trigger("reset");
$('.panel-options').hide(); 
		
		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		$("#btnAgregarArt").show();
		detalles=0;
		cargacombo('sector');

$('#imprimir').attr("disabled", true);
$('#imprimirt').attr("disabled", true);
$('#imprimirb').attr("disabled", true);
$('#imprimirt2').attr("disabled", true);
		
	}else{
		
$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
		$('.panel-options').show(); 

	}
	
	
	
}
//Función cancelarform
function cancelarform(){
	limpiar();
	mostrarform(false, 0);
}
//Función Listar
function listar(){
	
var mes=$('#mes').val();
var finicio=$('#fecha_inicio').val();
var ffin=$('#fecha_fin').val();

	
	tabla=$('#tbllistado_venta').dataTable({
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [
		'excelHtml5',
		'csvHtml5',
			{
                text: '<span class="glyphicon glyphicon-print"></span> IMPRIMIR',
                action: function ( e, dt, node, config ) {
                    printgrupo();
                }
				},			
			{
                text: '<span class="glyphicon glyphicon-book"></span> DES PDF',
                action: function ( e, dt, node, config ) {
                    descargapdfs();
                }
			},
			{
                text: '<span class="glyphicon glyphicon-envelope"></span> ENV. CORREO',
                action: function ( e, dt, node, config ) {
                    correosmasivos();
                }
				},
			{
                text: '<span class="glyphicon glyphicon-send"></span> ENV. SUNAT',
                action: function ( e, dt, node, config ) {
                    enviossmasivos();
                }
				},
				{
					text: '<span class="glyphicon glyphicon-cloud-upload"></span> IMPORTAR',
					action: function ( e, dt, node, config ) {
						importarventas();
					}
					},

			
		        ],
		
		'columnDefs': [
      {
         'targets': 0,
         'checkboxes': {
            'selectRow': true
         }
      }
   ],
   'select': {
      'style': 'multi'
   },
		
		"ajax":
				{
					url: 'data/venta.php?op=listar&mes='+mes+'&nivel=0&fecha_inicio='+finicio+'&fecha_fin='+ffin,
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 16,//Paginación
	    "order": [[1, "desc" ]],//Ordenar (columna,orden)
		//"sScrollY" : "400",

rowCallback:function(row, data){

$node = this.api().row(row).nodes().to$();

      if(data[3] == "BOLETA") {
        //$node.addClass('pink');
      }
	var intVal = function ( i ) {
		return typeof i === 'string' ?
			i.replace(/[\$,]/g, '')*1 :
			typeof i === 'number' ?
				i : 0;
	};
    }
		
	}).DataTable();
// Handle form submission event
	 
}
//Función Listar
function listarpedido(){
 	
var mes=$('#mes').val();
var seriedoc = $('#tipodoc').val();
var finicio=$('#fecha_inicio').val();
var ffin=$('#fecha_fin').val();

	tabla=$('#tbllistado').dataTable({
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control  de tabla
	    buttons: [		          
		            'copyHtml5',
		            'excelHtml5',
		            'csvHtml5',
		            'pdf'
		        ],
		"ajax":
				{
					url: 'data/venta.php?op=listarpedido&tdoc='+seriedoc+'&fecha_inicio='+finicio+'&fecha_fin='+ffin,
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 16,//Paginación
		"order": [[1, "desc" ]],//Ordenar (columna,orden)
	}).DataTable();


}
//Función para guardar o editar
function mostrar(idventa){
	ListCliente();
$("#idventa").val("0");
$('#txtID_CLIENTE').empty();
var accion=$("#accion").val();

tabladetalles.clear().draw();
listdetalles(idventa);
		
$.ajax({
url: "data/venta.php?op=mostrarpedido",
type: "POST",
dataType: 'json',
data:{idventa : idventa},
success: function(data){
mostrarform(true, 1);
console.log(data);	
	
if(accion=='0'){
var TipoComprobate=data.txtID_TIPO_DOCUMENTO;
//$('#txtID_TIPO_DOCUMENTO option[value='+data.txtID_TIPO_DOCUMENTO+']').prop('selected', 'selected').change();
	
console.log('data.txtID_TIPO_DOCUMENTO:'+data.txtID_TIPO_DOCUMENTO);
	
$("#txtID_TIPO_DOCUMENTO").val(data.txtID_TIPO_DOCUMENTO);
$('#txtID_TIPO_DOCUMENTO').prop('disabled', 'disabled');
TipoComprobate=data.txtID_TIPO_DOCUMENTO;
}else{
var TipoComprobate=data.doc_relaciona;
$("#txtID_TIPO_DOCUMENTO").val(data.doc_relaciona);
$("#txtID_TIPO_DOCUMENTO").selectpicker('refresh');
TipoComprobate=data.doc_relaciona;
	
//$('#txtID_TIPO_DOCUMENTO').prop('disabled', 'disabled');
}
/**/

clientesfinales(data.txtID_CLIENTE, data.cliente);


//$('#txtID_CLIENTE').prop('disabled', 'disabled');	
	
$("#txtSERIE").empty().append("<option value='"+data.txtSERIE+"' >"+data.txtSERIE+"</option>");	
$('#txtSERIE').prop('disabled', 'disabled');	
$("#txtNUMERO").val(data.txtNUMERO);
$("#serienum").val(data.txtSERIE+'-'+data.txtNUMERO);
$('#txtMONEDA option[value='+data.txtID_MONEDA+']').prop('selected', 'selected').change();	
$('#pago option[value='+data.tipo_pago+']').prop('selected', 'selected').change();
$('#medio option[value='+data.medio_pago+']').prop('selected', 'selected').change();
$('#vendedor option[value='+data.idusuario+']').prop('selected', 'selected').change();
$("#txtFECHA_DOCUMENTO").val(data.fecha);
$("#txtTIPO_DOCUMENTO_CLIENTE").val(data.txtTIPO_DOCUMENTO_CLIENTE);
$("#txtOBSERVACION").val(data.txtOBSERVACION);
$("#txtSUB_TOTALL").val(data.txtSUB_TOTALL);
$("#txtIGV").val(data.txtIGV);
$("#txtTOTAL").val(data.txtTOTAL);	
$("#idventa").val(data.idventa);	
$("#notpedido").val(data.referencia);
$("#ocompra").val(data.presupuesto);
$("#txtOBSERVACION").val(data.txtOBSERVACION);
$("#valref").val(data.referencial);


$("#tipoguia").val(data.tipoguia);
$("#guia").val(data.guia);	
$("#tipoguia2").val(data.tipoguia2);
$("#guia2").val(data.guia2);
$("#tipoguia3").val(data.tipoguia3);
$("#guia3").val(data.guia3);
$("#tipoguia4").val(data.tipoguia4);
$("#guia4").val(data.guia4);
$("#tipoguia5").val(data.tipoguia5);
$("#inafecta").val(data.inafecta);
$("#fpago_mpago").val((data.fpago_mpago && String(data.fpago_mpago).trim()!=='') ? data.fpago_mpago : data.guia5).trigger('change');
	_aplicarFormatoTotalesVenta();
	
if(data.percepcion!='0.00'){
$("#percepcion").val(data.percepcion);
$('#percepcionsi option[value=1]').prop('selected', 'selected').change();
}else{
$("#percepcion").val(0);
$('#percepcionsi option[value=0]').prop('selected', 'selected').change();	
}
	
if(data.retencion!='0.00'){
$("#retencion").val(data.retencion);
$('#operacionretenciones option[value=1]').prop('selected', 'selected').change();
}else{
$("#retencion").val(0);
$('#operacionretenciones option[value=0]').prop('selected', 'selected').change();	
}
	
$('#ccostos option[value='+data.sector+']').prop('selected', 'selected').change();
if(data.codidetracciones!=null){
$("#detracciones").val(data.iddetraccion+'|'+data.codidetracciones+'|'+data.porcentaje);
$("#montodetraccion").val(data.detraccion);
}

			if(data.controlpresupuestal!=''){
				$('#controlpresupuestal option[value='+data.controlpresupuestal+']').prop('selected', 'selected').change();
			}else{
				$('#controlpresupuestal option[value=0]').prop('selected', 'selected').change();
			}

//Ocultar y mostrar los botones
			$("#btnGuardar").hide();
			$("#btnCancelar").show();
			$("#btnAgregarArt").hide();

		},
		error: function(datos) {
			console.log(datos);
		}

	});


}

function evaluar(){
  	if (detalles>0)
    {
      $("#btnGuardar").show();
    }
    else   
    {
      $("#btnGuardar").hide(); 
      cont=0;
    }
  }

function eliminarDetalle(indice){
  	$("#fila" + indice).remove();
  	calcularTotales();
  	detalles=detalles-1;
  	evaluar()
  } 

//Función ListarArticulos
function listarArticulos(){
	
var cliente=$("#txtID_CLIENTE option:selected").val();

tabla=$('#tblarticulos').dataTable({
		'processing': true,
      'serverSide': true,
		autoWidth: false,
		'serverMethod': 'post',
	    buttons: [],
		"ajax":
				{
					url: 'data/venta.php?op=listarArticulos&idcliente='+cliente,
					type : "post",
					dataType : "json",					
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 12,//Paginación
		'columnDefs': [
			{
				"targets": 8, // Tu primera columna
				"className": "dt-body-right",
				"width": "4%"
		   }],
/*
columnDefs: [
         { width: '5px', targets: 0 }, //step 2, column 1 out of 4

         { width: '5px', targets: 1, "visible": false },
         { width: '5px', targets: 2, "visible": false  },
	
         { width: '40px', targets: 3 },
         { width: '5px', targets: 4 },
         { width: '5px', targets: 5 },
         { width: '5px', targets: 6 },
		 { width: '255px', targets: 8 }
      ],
*/
        fixedColumns:true,
		select: {
            style: 'single'
        },
        keys: {
           keys: [13 /* ENTER */, 38 /* UP */, 40 /* DOWN */ ]
        },
	    "order": [[2, "asc" ]],
	
"createdRow": function( row, data, dataIndex ) {
console.log('10:'+data[11]);
	
if ( data[12] == "SI" ) { 
	if ( data[10]>=data[11]) {  
         $('td', row).addClass('tablaverde');
       }
}
   }   

               
	}).DataTable();
    
    // Handle event when cell gains focus
    $('#tblarticulos').on('key-focus.dt', function(e, datatable, cell){
        // Select highlighted row
        tabla.row(cell.index().row).select();
    });
    
    // Handle click on table cell
    $('#tblarticulos').on('click', 'tbody td', function(e){
        e.stopPropagation();
        // Get index of the clicked row
        var rowIdx = tabla.cell(this).index().row;
        // Select row
        tabla.row(rowIdx).select();
    });
    
// Handle key event that hasn't been handled by KeyTable
$('#tblarticulos').on('key.dt', function(e, datatable, key, cell, originalEvent){
        // If ENTER key is pressed
if(key==13){
            // Get highlighted row data
var data = tabla.row(cell.index().row).data()[1];
var precio= tabla.row(cell.index().row).data()[4];
var cti=$('#cti'+data).val();
var tit=$('#tit'+data).val();
var stock=$('#stock'+data).val();
var codigo=$('#codigo'+data).val();
var exonerada=$('#ex'+data).val();
			
agregartabla(data, precio, tit, codigo, stock, cti, exonerada);
$('#tblarticulos_filter').val('');
tabla.search('').columns().search('').draw();
$('#tblarticulos_filter label input').focus();			
			
        }
    });
	
	
}

function teclea(id, precio){

	var total;
	var sub='0.00';
	var igv='0.00';
	var exon='0.00';
	var gravada='0.00';
	var inafecta='0.00';
	var gratuita='0.00';
	var porigv=$("#porcentajeigv").val();
	porigv=porigv/100;
	porigv=porigv+1.00;
//var pre=precio;
	var cti=_moneyNum($('#ctif'+id).val());
	var pre=_moneyNum($('#preciof'+id).val());
	var tipo=$('#tipo'+id).val();
	var descuento=_moneyNum($('#descuento'+id).val());

	console.log('descuento:'+descuento+'|id:'+id);

//var descuento=$('#ctidescuento'+id).val();
	var descuentofinal='0.00';
	var ctipuntos=$('#ctipuntos'+id).val();
	var puntosfinal='0';
	total=pre*cti;

	if(ctipuntos!='0'){
		puntosfinal=ctipuntos*cti;
	}

//$('#detpedidos').dataTable().fnUpdate(total, idrow, 4);
	if(tipo=='2'){
		sub=total;
		igv='0.00';
		exon='0.00';
		gratuita=total;
		gravada='0.00';

	}else if(tipo=='3'){

		if(descuento>'0.00'){
			total=parseFloat(total-descuento);
		}

		sub=total;
		inafecta=total;
		exon='0.00';
		igv='0.00';
		gravada='0.00';
		gratuita='0.00';

	}else if(tipo=='1'){

		if(descuento>'0.00'){
			total=parseFloat(total-descuento);
		}

		sub=total;
		exon=total;
		igv='0.00';
		gravada='0.00';
		gratuita='0.00';
	}else{

		sub=parseFloat(total/porigv);

		if(descuento>'0.00'){
			descuento=parseFloat(descuento);
			var preciodescuento=parseFloat(descuento/cti);
			console.log('preciodescuento1:'+preciodescuento);
			preciodescuento=parseFloat(pre-preciodescuento);
			console.log('preciodescuento2:'+preciodescuento);

			total=parseFloat(preciodescuento*cti);
//total=parseFloat(total*porigv);
		}

		sub=parseFloat(total/porigv);
		igv= parseFloat(total-sub);
		gravada=sub;
		exon='0.00';
		gratuita='0.00';

	}

	sub=_moneyFix2(sub);
	igv=_moneyFix2(igv);
	exon=_moneyFix2(exon);
	inafecta=_moneyFix2(inafecta);
	total=_moneyFix2(total);
	gravada=_moneyFix2(gravada);
	gratuita=_moneyFix2(gratuita);

	$('#igv'+id).val(igv);
	$('#exo'+id).val(exon);
	$('#inafecta'+id).val(inafecta);
	$('#totf'+id).val(total);
	$('#sub'+id).val(sub);
	$('#gravadas'+id).val(gravada);
	$('#grat'+id).val(gratuita);
//$('#descuento'+id).val(descuentofinal);
	$('#puntos'+id).val(puntosfinal);
	_formatearInputsDetallePedido();

	totales();
}


function teclea3(id, precio){
	var porigv=$("#porcentajeigv").val();
	porigv=porigv/100;
	porigv=porigv+1.00;
	//var pre=precio;
	var pre=$('#preciof'+id).val();
	var tipo=$('#tipo'+id).val();

	//$('#detpedidos').dataTable().fnUpdate(total, idrow, 4);
	if(tipo=='2'){
		sub=total;
		igv='0.00';
		exon='0.00';
		gratuita=total;
		gravada='0.00';

	}else if(tipo=='3'){


		sub=total;
		inafecta=total;
		exon='0.00';
		igv='0.00';
		gravada='0.00';
		gratuita='0.00';

	}else if(tipo=='1'){

		sub=total;
	}else{

		sub=parseFloat(total/porigv);
		igv= parseFloat(total-sub);
		gravada=sub;

	}

	teclea();
}




$('#detpedidos tbody').on( 'click', 'tr', function (){
    //alert( 'Row index: '+tabla.row( this ).index() );
	idrow=tabla.row( this ).index();
});

$("#detpedidos").on('click', '.btn-danger', function () {
var fila=$(this).parent().parent();
tabladetalles.row(fila).remove().draw(false); 
totales();
});

$( "#txtpaga" ).keyup(function() {

var vuelto='0.00';
var total=$('#totalf').val();
var pago=$('#txtpaga').val();
	
vuelto=pago-total;
vuelto=vuelto.toFixed(2);
$('#vueltof').html(vuelto);	
	console.log(total);
	console.log(pago);
});

function totales(exonerada){

	var arrayIds = new Array();
	var contador = 0;
	var ids;
	var id;
	var sumtotal='0.00';
	var subtotal='0.00';
	var exonerado='0.00';
	var inafecta='0.00';
	var igv='0.00';
	var gravadas='0.00';
	var comision='0.00'; var idu; var gratuita='0.00'; var tipo='0'; var precioo='0.00';
	var ctif=0; var totgratuita='0.00'; var descuento='0.00'; var sumtotaldesc='0.00';
	var igv2='0.00';
	var subtotal2='0.00';
	var total='0.00';
	var total='0.00';
	var retencion='0.00';
	var percepcion='0.00';
	var totdetraccion='0.00';
	var operacionretenciones=$('#operacionretenciones').val();
	var percepcionsi=$('#percepcionsi').val();
	var detracciones=$('#detracciones').val();
	var oferta='0.00';
	var puntos='0';
	var inafecta='0.00';
	var valref='0.00';
	var descuentofinal='0.00';
	var totinafecta='0.00';

	$("#detpedidos tr").each(function(index){
		$(this).children("td").each(function(index2){
			switch(index2){

				case 0:
					idu=$(this).text();
					tipo=$('#tipo'+idu).val();
					descuento=_moneyNum(descuento)+_moneyNum($('#descuento'+idu).val());
console.log('tipo:'+tipo);
					if(tipo=='2'){

						precioo=$('#precioo'+idu).val();
						ctif=$('#ctif'+idu).val();
						totgratuita=precioo*ctif;
						gratuita=parseFloat(gratuita)+parseFloat(totgratuita);
						sumtotal=sumtotal;
						igv=igv;
						exonerado=exonerado;
						subtotal=subtotal;
						gravadas=gravadas;
						comision=comision;
						comision=comision;

					}else if(tipo=='3'){

						inafecta=$('#inafecta'+idu).val();
						totinafecta=_moneyNum(inafecta)+_moneyNum(totinafecta);

						gratuita=gratuita;
						igv=igv;
						gravadas=gravadas;
						exonerado=exonerado;

						sumtotaldesc=_moneyNum($('#totf'+idu).val());
						subtotal=_moneyNum(subtotal)+_moneyNum($('#sub'+idu).val());
						comision=_moneyNum(comision)+_moneyNum($('#comision'+idu).val());
						sumtotal=_moneyNum(sumtotal)+_moneyNum(sumtotaldesc);

					}else if(tipo=='1'){

						gratuita=gratuita;
						igv=igv;
						gravadas=gravadas;
						inafecta=inafecta;

						sumtotaldesc=_moneyNum($('#totf'+idu).val());
						exonerado=_moneyNum(exonerado)+_moneyNum($('#exo'+idu).val());
						subtotal=_moneyNum(subtotal)+_moneyNum($('#sub'+idu).val());

						comision=_moneyNum(comision)+_moneyNum($('#comision'+idu).val());
						sumtotal=_moneyNum(sumtotal)+_moneyNum(sumtotaldesc);

					}else{

						gratuita=gratuita;
						inafecta=inafecta;
						sumtotaldesc=_moneyNum($('#totf'+idu).val());

						subtotal=_moneyNum(subtotal)+_moneyNum($('#sub'+idu).val());
						gravadas=_moneyNum(gravadas)+_moneyNum($('#gravadas'+idu).val());
						igv=_moneyNum(igv)+_moneyNum($('#igv'+idu).val());

						sumtotal=_moneyNum(sumtotal)+_moneyNum(sumtotaldesc);
						comision=_moneyNum(comision)+_moneyNum($('#comision'+idu).val());
						
						console.log('gravadasdetalle:'+gravadas);
						
					}

					total=_moneyNum(total)+_moneyNum($('#totalf'+idu).val());
					puntos=_moneyNum(puntos)+_moneyNum($('#puntos'+idu).val());
					valref=_moneyNum(valref)+_moneyNum($('#totdetraccion'+idu).val());

				case 4:
					ids=$(this).text();
					//sumtotal=parseFloat(sumtotal)+parseFloat(ids);
					break;
			}
		});

	});

	if(igv!='0.00'){
		igv=igv.toFixed(2);
	}

	if(operacionretenciones=='1'){
		retencion=parseFloat(3)*parseFloat(sumtotal)/100;
		retencion=retencion.toFixed(2);
	}

	if(detracciones!='0|0|0'){

		var ret=$('#detracciones').val().split('|');
		var iddetraccion=ret[0];
		var codigo=ret[1];
		var porcentaje=ret[2];

		if(valref>sumtotal){
			totdetraccion=parseFloat(porcentaje)*parseFloat(valref)/100;
		}else{
			totdetraccion=parseFloat(porcentaje)*parseFloat(sumtotal)/100;
		}

		var moneda=$('#txtMONEDA').val();

		if(moneda=='USD'){
			totdetraccion=totdetraccion.toFixed(2);
		}else{
			totdetraccion=totdetraccion.toFixed();
		}
	}


	if(exonerado!='0.00'){ exonerado=exonerado.toFixed(2); }
	if(totinafecta!='0.00'){ totinafecta=totinafecta.toFixed(2); }
	if(subtotal!='0.00'){ subtotal=subtotal.toFixed(2); }
	if(sumtotal!='0.00'){ sumtotal=sumtotal.toFixed(2); }
	if(gravadas!='0.00'){ gravadas=gravadas.toFixed(2); }
	if(comision!='0.00'){ comision=comision.toFixed(2); }
	if(gratuita!='0.00'){ gratuita=gratuita.toFixed(2); }
	if(oferta!='0.00'){ oferta=oferta.toFixed(2); }
	if(descuento!='0.00'){ descuento=descuento.toFixed(2); }
	if(total!='0.00'){ total=total.toFixed(2); }
	if(puntos!='0.00'){ puntos=_moneyNum(puntos).toFixed(2); }
	if(valref!='0.00'){ valref=valref.toFixed(2); }

	if(percepcionsi=='1'){
		percepcion=(parseFloat(2)*parseFloat(sumtotal))/100;
		percepcion=percepcion.toFixed(2);
	}

console.log('gravadastotales:'+gravadas);


	var totalDocumento=_moneyNum(sumtotal)+_moneyNum(percepcion)-_moneyNum(retencion);
	if(totalDocumento<0){ totalDocumento=0; }
	totalDocumento=totalDocumento.toFixed(2);

	$('#descuentotot').val(descuento);
	$('#puntos').val(puntos);
	$('#txtSUB_TOTAL').val(subtotal);
	$('#gravadas').val(gravadas);
	$('#exoneradof').val(exonerado);
	$('#gratuita').val(gratuita);
	$('#txtIGV').val(igv);
	$('#txtTOTAL').val(totalDocumento);
	$('#totalf').val(totalDocumento);
	$('#totalpagarf').html(totalDocumento);
	$('#comisiont').val(comision);
	$('#inafecta').val(totinafecta);
//$('#descuentotot').val(descuento);
//$('#descuentotot').val('0.00');
	$('#retencion').val(retencion);
	$('#montodetraccion').val(totdetraccion);
	$('#percepcion').val(percepcion);
	$('#valref').val(valref);
	_aplicarFormatoTotalesVenta();

}

function serie(tipobuscar) {

console.log('solo serie');
	
//===========================numero correlativo==========================
var TipoComprobate = $('#txtID_TIPO_DOCUMENTO').val();
var TipoComprobateRef = $('#txtID_TIPO_DOCUMENTO_MODIFICA').val();
var idcliente= $('#txtID_CLIENTE').val();
	
var seriedoc = $('#tipodoc').val();
			
			var serie;
			var tipodoc; var tipoc='';
			
            if (TipoComprobate == '01') {
				tipodoc='RUC';
            } else if (TipoComprobate == '03') {
				tipodoc='DNI';
            }else {
				tipodoc='OTROS';
			}

//var TipoComprobate = $('#txtID_TIPO_DOCUMENTO').val();
	//$('#txtSERIE').val();	

var adelanto=$("#opadelanto").val();
	
	
if(adelanto=='NO'){
if(tipobuscar=='1'){
ListCliente(tipodoc);
}
}
	
console.log('TipoComprobate:'+TipoComprobate);
console.log('tipoc:'+tipoc);
	
if(seriedoc=='91'||seriedoc=='92'){ 
if(seriedoc=='92'){
tipoc='';
TipoComprobate=seriedoc;
}else if(seriedoc=='91'){
tipoc='';	
}  
TipoComprobate=seriedoc;
}

$.ajax({
url: "data/venta.php?op=numero&tipo="+TipoComprobate+"&seriedoc="+tipoc+"&idcliente="+idcliente,
type: "get",
dataType: 'json',
data: {"op": "numero", "doc":serie, "idcliente":idcliente},
success: function (response) {

	$('#txtNUMERO').val(response.numero);
	//$('#txtSERIE').val(response.serie);
	$('#seriedoc').val(response.seriedoc);
	$('#serienum').val(response.serie+'-'+response.numero);
	verdescuento();
},
error: function (data) {
console.log(data);
alert('Error Al conectar la Base Datos');
                            //console.log(data);
}
});
			

}

function printPdf(tipo){ 
console.log('pdf impresion');
var idventa=$('#idventa').val();
if(tipo=='1'){	
var url='plugins/dompdf/?id='+idventa;
}else{
 var url='plugins/dompdf/indexb.php?id='+idventa;  
}
window.open(url, '_blank');
	
}

function impresion(url){ 
	/*
  newwindow=window.open();  
  newdocument=newwindow.document;   
  newdocument.write('<embed   type="application/pdf" src="'+url+'" id="pdfDocument"   height="100%" width="100%" ></embed> <SCR'+'IPT LANGUAGE="JavaScript">window.print();window.close();</SCR'+'IPT>'); 
	*/
window.open(url, '_blank');

}

function llenaimpresion(){

var idventa=$('#idventa').val();

impresionf(idventa);

}


function llenaimpresiontec(){

	var idventa=$('#idventa').val();

	impresionftec(idventa);

}

function PrintElem(elem){
        Popup($(elem).html());
    }

function Popup(data){

        var mywindow = window.open('', 'mydiv', 'height=400,width=200');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();

        return true;
    }

function baja(id){
	
Swal.fire({
  title: 'DESEA DAR DE BAJA?',
  text: "RECUERDA QUE ESTO ANULARA EL DOCUMENTO!",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'SI DAR DE BAJA!',
cancelButtonText: 'CANCELAR!'
}).then((result) => {
  if (result.isConfirmed) {


Swal.fire({
            title: "ENVIANDO INFORMACIÓN!",
            text: "Porfavor espere un momento",
            type: "info",
            showLoaderOnConfirm: true,
            onOpen: function(){
                swal.clickConfirm();
            },
            preConfirm: function() {
                return new Promise(function(resolve) {

                });
            },allowOutsideClick: false
        });

		  
$.ajax({
url: "data/venta.php?op=anular",
type: "POST",
dataType: 'json',
data: {"idventa": id},
success: function (data) {
console.log(data); 
Swal.fire('PROCESADO!', data.mensaje, 'success'); 					  
listar(); 
},
error: function (data) { console.log(data); }
});
	  
	  

  }
})
	
	
}

function leerticket(id){ 
	
Swal.fire('VERIFICANDO INFORMACIÓN!'); 	
	
$.ajax({
url: "data/venta.php?op=leerticket",
type: "POST",
dataType: 'json',
data: {"idventa": id},
success: function (data) {
console.log(data); 
Swal.fire('PROCESADO!', data.mensaje, 'success'); 					  
listar(); 
},
error: function (data) { console.log(data); }
});
	
}

function reenviabaja(id){ 
	
$.ajax({
url: "data/venta.php?op=anular",
type: "POST",
dataType: 'json',
data: {"idventa": id},
success: function (data) {
console.log(data); 
Swal.fire('PROCESADO!', data.mensaje, 'success'); 					  
listar(); 
},
error: function (data) { console.log(data); }
});
	
}

function bajarecibo(id){ 
	
Swal.fire({
          title: 'BAJA',
          text: 'DESEA DAR DE BAJA EL COMPROBANTE?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'SI DAR DE BAJA',
          cancelButtonText: 'CANCELAR',
          showCloseButton: true,
          showLoaderOnConfirm: true
        }).then((result) => {
          if(result.value) {
			  
$.ajax({
url: "data/venta.php?op=anularrecibo",
type: "POST",
dataType: 'json',
data: {"idventa": id},
success: function (data) { 
	console.log(data); 
	Swal.fire('ESTADO', data.mensaje, 'success')
	listar(); 
},
error: function (data) { console.log(data); }
});

          }
        })

}
function sendwhatsap(id){
	Swal.fire({
		title: 'WHATSAPP',
		text: 'DESEA ENVIAR A WHATSAPP EL COMPROBANTE?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonText: 'SI ENVIAR',
		cancelButtonText: 'CANCELAR',
		showCloseButton: true,
		showLoaderOnConfirm: true
	}).then((result) => {
		if(result.value) {


			console.log('id:'+id);
			$.post("../modelos/whatsapp.php?op=enviardoc&id="+id, function(datos){
				Swal.fire(datos.mensaje);

			});

		}
	})
}
function sendcorreo(id){

	Swal.fire({
		title: 'CORREO',
		text: 'DESEA ENVIAR POR CORREO EL COMPROBANTE?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonText: 'SI ENVIAR',
		cancelButtonText: 'CANCELAR',
		showCloseButton: true,
		showLoaderOnConfirm: true
	}).then((result) => {
		if(result.value) {



			$.ajax({
				url: "data/correos.php?op=enviardoc&id="+id,
				type: "POST",
				dataType: 'json',
				data: {"id": id},
				success: function (data) {
					console.log(data);
					Swal.fire(data.mensaje);

				},
				error: function (data) { console.log(data); }
			});
		}
	})

}

function reenviarf(){ 
	
var id=$("#idventarevisar").val();
var pago=$("#pagot").val();
//console.log('idventa:'+$("#idventa").val());
reenviaFact(id, pago);

}

function reenviaFact(id, pago){


if(pago=='0'){
reenviafinal(id);
}else{
	
Swal.fire({
  title: 'ESTE DOCUMENTO ES A CREDITO, NO HAS CREADO NINGUNA LETRA?',
  text: "Si lo envias, llegará a la SUNAT como pago al contado!",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#d33',
  cancelButtonColor: '#3085d6',
  confirmButtonText: 'SI, ENVIAR A SUNAT!', 
cancelButtonText: 'CANCELAR'
}).then((result) => {
  if (result.isConfirmed) {
reenviafinal(id);
  }
})
	
}

}

function reenviafinal(id){

	Swal.fire({
		title: "ENVIANDO INFORMACIÓN!",
		text: "Porfavor espere un momento",
		type: "info",
		showLoaderOnConfirm: true,
		onOpen: function(){
			swal.clickConfirm();
		},
		preConfirm: function() {
			return new Promise(function(resolve) {

			});
		},allowOutsideClick: false
	});

$.ajax({
url: "data/venta.php?op=enviaSunat",
type: "POST",
dataType: 'json',
data: {"idventa":id },
success: function (data) { 
	console.log(data); 
	//Swal.fire(data.mensaje);
Swal.fire(data.mensaje);
tabla.ajax.reload();

},
error: function (data) { console.log(data); }
});
	
}

function traercdr(){ 

var id=$("#idventarevisar").val();
	
$.ajax({
url: "api_cpe/consultas.php?id="+id,
type: "GET",
dataType: 'json',
data: {"id":id },
success: function (data) { 
	console.log(data); 
Swal.fire(data.msj_sunat);
},
error: function (data) { console.log(data); }
});
	
}

function pasaventa(id){ 

$.ajax({
url: "data/venta.php?op=PasaVenta",
type: "POST",
dataType: 'json',
data: {"idventa":id },
success: function (data) { 
	console.log(data); 
	Swal.fire(data.mensaje);
	listar(); 
},
error: function (data) { console.log(data); }
});
	
}

function pasapedido(id, tipo){

	Swal.fire({
		title: 'DESEA CREAR EL DOCUMENTO?',
		text: 'Este proceso crear la Boleta/Factura!',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'SI, generar!',
		cancelButtonText: 'NO, generar'
	}).then((result) => {
		if (result.value) {
//eli9minado 

			Swal.fire({
				title: "GENERANDO DOCUMENTO!",
				text: "Porfavor espere un momento",
				type: "info",
				showLoaderOnConfirm: true,
				onOpen: function(){
					swal.clickConfirm();
				},
				preConfirm: function() {
					return new Promise(function(resolve) {

					});
				},allowOutsideClick: false
			});


			$.ajax({
				url: "data/venta.php?op=Pasapedido&tipoproceso="+tipo,
				data : { idventa : id },
				type : 'POST',
				success: function (e) {

					Swal.fire(
						'DOCUMENTO CREADO!',
						''+e.mensaje,
						'success'
					)
					tabla.ajax.reload();

				},
				error : function(data) {
					console.log(data);
				},

			});








		} else if (result.dismiss === Swal.DismissReason.cancel) {
			Swal.fire(
				'CANCELADO!',
				'No se hicieron cambios :)',
				'error'
			)
		}
	})


}
/*LISTAR DETALLE*/
function listdetalles(id){ 
var exonerado='';	
var idu;
$.ajax({
url: "data/venta.php?op=listarDetalle&id="+id,
success: function (result) {

for(var i = 0; i < result.length; i++) {
                    
//calculateTotal();
var descuentofinal='0.00';		 
articulo=result[i].cell[3];				 
id=result[i].cell[0];
codigo=result[i].cell[1];
						 
umedida=result[i].cell[4];
exonf=result[i].cell[12];
igv=result[i].cell[9];
exonerado=result[i].cell[2];
tipoope=result[i].cell[2];
gravadas=_moneyFix2(result[i].cell[8]);
comision=result[i].cell[11];
totalventa=_moneyFix2(result[i].cell[10]);
idunit=result[i].cell[14];
gratuitas=result[i].cell[13];
precio=result[i].cell[15];
cti=result[i].cell[7];
cti2=result[i].cell[16];
idu=result[i].cell[17];
tipoarticulo=result[i].cell[18];
detracciond=result[i].cell[19];

iddestino=result[i].cell[20];
carga_util=result[i].cell[21];
cantidad_toneladas=result[i].cell[22];
descuentofinal=result[i].cell[23];
inafecta=result[i].cell[24];

var campo='<input type="hidden" id="idp'+idu+'" name="idp'+idu+'" value="'+id+'"> <input type="hidden" id="tipoart'+idu+'" name="tipoart'+idu+'" value="'+tipoarticulo+'"> <input type="hidden" id="codigo'+idu+'" name="codigo'+idu+'" value="'+codigo+'"> <input type="hidden" id="umedida'+idu+'" name="umedida'+idu+'" value="'+umedida+'"> <input type="hidden" id="tit'+idu+'" name="tit'+idu+'" value="'+articulo+'"> <input type="hidden" id="cti2'+idu+'" name="cti2'+idu+'" value="'+cti2+'"> <input type="hidden" id="exon'+idu+'" name="exon'+idu+'" value="'+exonerado+'"> <input type="hidden" id="tipo'+idu+'" name="tipo'+idu+'" value="'+tipoope+'"> <input type="hidden" id="igvh'+idu+'" name="igvh'+idu+'" value="'+igv+'"> <input type="hidden" id="exo'+idu+'" name="exo'+idu+'" value="'+exonf+'">  <input type="hidden" id="gravadas'+idu+'" name="gravadas'+idu+'" value="'+gravadas+'"> <input type="hidden" id="comision'+idu+'" name="comision'+idu+'" value="'+comision+'"> <input type="hidden" id="idu'+id+'" name="idu'+id+'" value="'+idu+'"> <input type="hidden" id="idunid'+idu+'" name="idunit'+idu+'" value="'+idunit+'"> <input type="hidden" id="grat'+idu+'" name="grat'+idu+'" value="'+gratuitas+'"> <input type="hidden" id="descuento'+idu+'" name="descuento'+idu+'" value="0.00"> <input type="hidden" id="puntos'+idu+'" name="puntos'+idu+'" value="0"> <input type="hidden" id="ctipuntos'+idu+'" name="ctipuntos'+idu+'" value="0">  <input type="hidden" id="totdetraccion'+idu+'" name="totdetraccion'+idu+'" value="'+detracciond+'">   <input type="hidden" id="cantidad_toneladas'+idu+'" name="cantidad_toneladas'+idu+'" value="'+cantidad_toneladas+'">  <input type="hidden" id="carga_util'+idu+'" name="carga_util'+idu+'" value="'+carga_util+'">  <input type="hidden" id="iddestino'+idu+'" name="iddestino'+idu+'" value="'+iddestino+'">';
	
campo+=' <input type="hidden" id="inafecta'+idu+'" name="inafecta'+idu+'" value="'+inafecta+'">';

var ctif=' <input type="text" onkeyup="teclea('+idu+', '+precio+')" size="3" id="ctif'+idu+'" name="ctif'+idu+'" value="'+cti+'"> ';
	
var preciof=' <input type="text" onkeyup="teclea('+idu+', '+precio+')" size="5" id="preciof'+idu+'" name="preciof'+idu+'" value="'+precio+'"> ';


var totf=' <input type="text" size="5" id="totf'+idu+'" name="totf'+idu+'" value="'+totalventa+'" readonly > ';
var igvf=' <input type="text" size="5" id="igv'+idu+'" name="igvh'+idu+'" value="'+_moneyFix2(igv)+'" readonly > ';
	
var descuentotxt='<input type="text" size="5" onkeyup="teclea('+idu+', '+precio+')" size="5" id="descuento'+idu+'" name="descuento'+idu+'" value="'+descuentofinal+'"> <input type="hidden" size="5" id="ctidescuento'+idu+'" name="ctidescuento'+idu+'" value="0" >';

var subtotaldetalle='<input type="text" id="sub'+idu+'" size="5" name="sub'+idu+'" readonly value="'+gravadas+'">';


tabladetalles.row.add([
			idu, 
			campo+articulo,
			umedida,
			ctif,
            preciof,

			descuentotxt,
				subtotaldetalle,
				igvf,
	            totf,
	'<button type="button"  class="btn btn-danger btn-xs" ><span class="glyphicon glyphicon-trash"></span></button>'
        ]).draw(false);
							 
}
	
if(exonerado!=''){
totales(exonerado);	
}	
	
}
	
})  .fail(function(result) {
console.log(result);
  });

	
}


function listaimport(){
	
tabladetalles.clear().draw();
var categoria=$('#categoria').val();
var exonerado='';	
var idu;
	
Swal.fire({
            title: "PROCESANDO INFORMACIÓN!",
            text: "Porfavor espere un momento",
            type: "info",
            showLoaderOnConfirm: true,
            onOpen: function(){
                swal.clickConfirm();
            },
            preConfirm: function() {
                return new Promise(function(resolve) {

                });
            },allowOutsideClick: false
        });
	  	
$.ajax({
url: "data/venta.php?op=listarimport&id="+categoria,
success: function (result) {

console.log(result);
	
for(var i = 0; i < result.length; i++) {
                    
//calculateTotal();
				 
				 
id=result[i].cell[0];
codigo=result[i].cell[1];
exonerado=result[i].cell[2];
tipoope=result[i].cell[2];
articulo=result[i].cell[3];

umedida=result[i].cell[4];
	
cti=result[i].cell[7];
gravadas=_moneyFix2(result[i].cell[8]);
igv=result[i].cell[9];
totalventa=result[i].cell[10];
comision=result[i].cell[11];
exonf=result[i].cell[12];
	
gratuitas=result[i].cell[13];
idunit=result[i].cell[14];
precio=_moneyFix2(result[i].cell[15]);
cti2=result[i].cell[16];
idu=result[i].cell[17];
tipoarticulo=result[i].cell[18];
detracciond=result[i].cell[19];
	
iddestino=result[i].cell[20];
carga_util=result[i].cell[21];
cantidad_toneladas=result[i].cell[22];
	
var campo='<input type="hidden" id="idp'+idu+'" name="idp'+idu+'" value="'+id+'"> <input type="hidden" id="tipoart'+idu+'" name="tipoart'+idu+'" value="'+tipoarticulo+'"> <input type="hidden" id="codigo'+idu+'" name="codigo'+idu+'" value="'+codigo+'"> <input type="hidden" id="umedida'+idu+'" name="umedida'+idu+'" value="'+umedida+'"> <input type="hidden" id="tit'+idu+'" name="tit'+idu+'" value="'+articulo+'"> <input type="hidden" id="cti2'+idu+'" name="cti2'+idu+'" value="'+cti2+'"> <input type="hidden" id="exon'+idu+'" name="exon'+idu+'" value="'+exonerado+'"> <input type="hidden" id="tipo'+idu+'" name="tipo'+idu+'" value="'+tipoope+'"> <input type="hidden" id="igvh'+idu+'" name="igvh'+idu+'" value="'+igv+'"> <input type="hidden" id="exo'+idu+'" name="exo'+idu+'" value="'+exonf+'"> <input type="hidden" id="sub'+idu+'" name="sub'+idu+'" value="'+gravadas+'"> <input type="hidden" id="gravadas'+idu+'" name="gravadas'+idu+'" value="'+gravadas+'"> <input type="hidden" id="comision'+idu+'" name="comision'+idu+'" value="'+comision+'"> <input type="hidden" id="idu'+id+'" name="idu'+id+'" value="'+idu+'"> <input type="hidden" id="idunid'+idu+'" name="idunit'+idu+'" value="'+idunit+'"> <input type="hidden" id="grat'+idu+'" name="grat'+idu+'" value="'+gratuitas+'"> <input type="hidden" id="descuento'+idu+'" name="descuento'+idu+'" value="0.00"> <input type="hidden" id="puntos'+idu+'" name="puntos'+idu+'" value="0"> <input type="hidden" id="ctipuntos'+idu+'" name="ctipuntos'+idu+'" value="0">  <input type="hidden" id="totdetraccion'+idu+'" name="totdetraccion'+idu+'" value="'+detracciond+'">   <input type="hidden" id="cantidad_toneladas'+idu+'" name="cantidad_toneladas'+idu+'" value="'+cantidad_toneladas+'">  <input type="hidden" id="carga_util'+idu+'" name="carga_util'+idu+'" value="'+carga_util+'">  <input type="hidden" id="iddestino'+idu+'" name="iddestino'+idu+'" value="'+iddestino+'">';
	
var ctif=' <input type="text" onkeyup="teclea('+idu+', '+precio+')" size="3" id="ctif'+idu+'" name="ctif'+idu+'" value="'+cti+'"> ';
	
var preciof=' <input type="text" onkeyup="teclea('+idu+', '+precio+')" size="5" id="preciof'+idu+'" name="preciof'+idu+'" value="'+precio+'"> ';
	
var totf=' <input type="text" size="5" id="totf'+idu+'" name="totf'+idu+'" value="'+totalventa+'" readonly > ';
var igvf=' <input type="text" size="5" id="igv'+idu+'" name="igvh'+idu+'" value="'+_moneyFix2(igv)+'" readonly > ';
	
var descuentotxt='<input type="text" onkeyup="teclea('+idu+', '+precio+')" size="5" id="descuento'+idu+'" name="descuento'+idu+'" value="0"> <input type="hidden" size="5" id="ctidescuento'+idu+'" name="ctidescuento'+idu+'" value="0">';

tabladetalles.row.add([
			idu, 
			campo+articulo,
			umedida,
			ctif,
            preciof,
			descuentotxt,
				subtotaldetalle,
				igvf,
	            totf,
	'<button type="button"  class="btn btn-danger btn-xs" ><span class="glyphicon glyphicon-trash"></span></button>'
        ]).draw(false);
							 
}
	
if(exonerado!=''){
totales(exonerado);	
}	

Swal.fire('PROCESADO!', 'PROCESO TERMINADO', 'success'); 
	
}
	
})  .fail(function(result) {
console.log(result);
  });

	
}


function cargacombo(sector){

	$.post("data/articulo.php?op=selectCategoria&nivel=2&id="+sector, function(r){
	         $("#idcategoria").html(r);
	          $('#idcategoria').selectpicker('refresh');

	});
	

}

$(document).ready(function () {
    // Init Select2 combos (empresa)
    initSelect2MedioPago();
    initSelect2FormaPagoPersona();
    initSelect2Detracciones();
    _formatearInputsDetallePedido();
    $('#detpedidos').on('draw.dt', function(){ _formatearInputsDetallePedido(); });

            $('#xmlComprobantes').hide();
            $('#txtSUB_TOTAL_ARTICULO').attr("disabled", true);
            $('#txtIGV_ARTICULO').attr("disabled", true);
            $('#txtTOTAL_ARTICULO').attr("disabled", true);
            $('#miFormArticulo').validate({
                rules: {
                    txtCOD_ARTICULO: {required: true},
                    txtDESCRIPCION_ARTICULO: {required: true},
                    txtUNIDAD_MEDIDA_ARTICULO: {required: true},
                    txtPRECIO_ARTICULO: {required: true, min: 0.0001},
                    txtCANTIDAD_ARTICULO: {required: true, min: 0.0001}
                },
                messages: {
                    txtCOD_ARTICULO: 'El Campo CODIGO es Obligatorio',
                    txtDESCRIPCION_ARTICULO: 'El Campo DESCRIPCION es Obligatorio',
                    txtUNIDAD_MEDIDA_ARTICULO: 'El Campo UND/MEDIDA es Obligatorio',
                    txtPRECIO_ARTICULO: {required: 'El Campo PRECIO es Obligatorio', min: 'PRECIO MINIMO ES 0.0001'},
                    txtCANTIDAD_ARTICULO: {required: 'El Campo CANTIDAD es Obligatorio', min: 'CANTIDAD MINIMA ES 0.0001'}
                },
                submitHandler: function (form) {
                    var datarow = {
                        ID_ARTICULO: $('#txtCOD_ARTICULO').val(),
                        CODIGO: $('#txtCOD_ARTICULO').val(),
                        DESCRIPCION: $('#txtDESCRIPCION_ARTICULO').val(),
                        ID_UNIDAD_MEDIDA: $('#txtUNIDAD_MEDIDA_ARTICULO').val(),
                        UNIDAD_MEDIDA: $('#txtUNIDAD_MEDIDA_ARTICULO').val(),
                        PRECIO: $('#txtPRECIO_ARTICULO').val(),
                        CANTIDAD: $('#txtCANTIDAD_ARTICULO').val(),
						CANTIDADP: $('#txtCANTIDAD_ARTICULOP').val(),
                        SUB_TOTAL: $('#txtSUB_TOTAL_ARTICULO').val(),
                        IGV: $('#txtIGV_ARTICULO').val(),
                        IMPORTE: $('#txtTOTAL_ARTICULO').val(),
						PLACA: $('#PLACA').val(),
                        ESTADO: 'V'
                    };
                    //======================LE AGREGAMOS UN ID A NUESTRO REGISTRO(DETALLE)=====================
                    var su = jQuery("#list").addRowData($('#txtCOD_ARTICULO').val(), datarow, 'last');
                    calculateTotal();
                    $('#miFormArticulo')[0].reset();
                }
            });
        });

function guardarcli(){

var dni=$("#numerodocumento").val();  	
	
var dataString = 'dni='+dni;

        $.ajax({
            type: "POST",
            url: "data/persona.php?op=verificar",
            data: dataString,
            success: function(data) {
				console.log(data);
              //Swal.fire(data);
				
				if(data.estado==0){
					Swal.fire(data.mensaje);
				}else{
					guardaf();
					
				}
            }
        });

	
/*
	
	*/
}

function guardaf(){

//$('#txtID_CLIENTE').val('').selectpicker('refresh');

	var formData = new FormData($("#formularioc")[0]);

	$.ajax({
		url: "data/persona.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function(datos){

			console.log(datos);
			Swal.fire(datos.mensaje);
			$('#txtID_CLIENTE').html("<option value='"+datos.id+"' selected='selected'>"+datos.nombre+"</option>").selectpicker('refresh');
			$('#cliente').modal('toggle');
		}

	});


}

function addadelanto() {
	
var adelanto=$("#opadelanto").val();

if(adelanto=='SI'){

$("#adelantot").prop('disabled', false);
$('#btnadelanto').attr("disabled", false);
$('#agregar').attr("disabled", true);
$('#eliminar').attr("disabled", true);
	
$("#txtMONEDA").attr('disabled','disabled');
$("#txtID_TIPO_DOCUMENTO").attr('disabled','disabled');
$("#txtID_CLIENTE").attr('disabled','disabled');
	
}else{

$("#adelantot").prop('disabled', true);
$('#btnadelanto').attr("disabled", true);
	
$("#txtMONEDA").removeAttr('disabled');
$("#txtID_TIPO_DOCUMENTO").removeAttr('disabled');
$('#agregar').attr("disabled", false);
$('#eliminar').attr("disabled", false);
	
$('#adelanto').val("");
$('#scredito').val("");	
$('#ncredito').val("");
	
}
	
}

function agregartabla(id, precio, articulo, codigo, stock, ctif, exonerado, moneda, oferta, puntos){

	if(formenvio=='0'){

		var cti2='0'; var precio2='0'; var cti; var tipoope='0'; var gratuitas='0.00'; var valor='0.00';
		var untot=$('#un'+id).val().split('|');
		var umedida=untot[0];
		var ctimedida=untot[1];
		var cprecio=untot[2];
		var tipo=untot[3];
		var preciom=untot[4];
		var precio=$('#p'+id).val();
		var precioini=$('#p'+id).val();
		var ctipuntos=puntos;

		if(tipo=='1'){
			precio=cprecio;
		}

		var formData = new FormData($("#formularioc")[0]);

		formData.append("precio", precio);
		formData.append("id", id);

		$.ajax({
			url: "data/venta.php?op=verificarprecio",
			type: "POST",
			data: formData,
			contentType: false,
			processData: false,
			success: function(datos){

				if(datos.pasa=='NO'){
					Swal.fire(datos.mensaje);
				}else{
					/*AQUI ABEXAMOS EL PRODUCTO*/

					var porigv=$("#porcentajeigv").val();
					porigv=porigv/100;
					porigv=porigv+1.00;
					var tipocambio=$('#tipocambio').val();
					var monedadoc=$("#txtMONEDA option:selected").val();
					var tservicio=$('#tservicio').val();
					var ruta='0';
					var destinoid='0';
					var destinoid2='0';
					var parcial='0';
					var acumulada='0';
					var montotonelada='0';
					var tiporecorrido='0';
					var ctitoneladas='0';
					var totdetraccion2='0';
					var totdetraccion='0';
					var inafecta='0';
					var detracciones=$('#detracciones').val();

					if(moneda!=monedadoc){
						if(monedadoc=='PEN'){
							var preciom=parseFloat(preciom*tipocambio);
							precio=parseFloat(precio*tipocambio);
							precioini=parseFloat(precio*tipocambio);

						}else{
							var preciom=parseFloat(preciom/tipocambio);
							precio=parseFloat(precio/tipocambio);
							precioini=parseFloat(precio/tipocambio);
						}
						var preciom=preciom.toFixed(7);
						precio=precio.toFixed(7);
						precioini=precio;
					}

					var ctim=untot[5];
					var comision=untot[6];
					var comisionm=untot[7];
					var comisionmp=untot[8];
					var idu=untot[9];
					var idunit=untot[10];

					var descuento=oferta;
					var descuentom=$('#descuentom').val();

					var idserie='0';
					var serie='';
					var titserie='';

					var serint=$('#ser'+id).val();
					var tipoigv=$('#tipoigv'+id).val();
					var textoserie='';

					if(serint!==null){
						var unserie=$('#ser'+id).val().split('|');
						var idserie=unserie[0];
						var serie=unserie[1];
						var lote=unserie[2];
						var fecha=unserie[3];
						//textoserie=' (SERIE/LOTE: '+serie+'/'+lote+')';
						textoserie='';
					}

					var igv='0.00';
					var sub='0.00';
					var exonf='0.00';
					var gravadas='0.00';

					var articulo=$('#titdetalle'+id).val()+''+titserie;
					var cti=parseFloat($('#ctidet'+id).val());
					var pase='0';

					stock=parseFloat(stock);

/*
console.log('cti2:'+cti2);
console.log('stock:'+stock);
console.log('cti:'+cti);
*/

if(umedida=='ZZ'){ pase='1'; 
    
}else if(cti>0&&cti<=stock){ pase='1';	}

					if(pase=='1'){

						ctic=Math.round(cti);
						ctimc=Math.round(ctim);


						var descuentofinal='0.00';
						var subtotal=precio*cti;
						/*AQUI AFECTAMOS CON EL DESCUENTO*/
						if(descuento!='0.00'){
							descuentofinal=(parseFloat(descuento)*parseFloat(cti));
							subtotal=parseFloat(subtotal)-parseFloat(descuentofinal);
						}
//PUNTOS DE COMPRA	
						if(puntos!='0'){
							puntos=(parseFloat(puntos)*parseFloat(cti));
						}

						if(tipoigv=='2'){
							valor=precioini;
							tipoope='2';
							gratuitas=subtotal;
							sub=subtotal;
							exonf='0.00';
							igv='0.00';
							gravadas='0.00';
							idu='2'+idu;
						} else if(tipoigv=='3'){
							valor=precioini;
							tipoope='3';
							exonf='0.00';
							inafecta=subtotal;
							sub=subtotal;
							gravadas='0.00';
							igv='0.00';
							comision=parseFloat(comision*cti);
						}else if(tipoigv=='1'){
							valor=precioini;
							idu='1'+idu;
							tipoope='1';
							exonf=subtotal;
							sub=subtotal;
							gravadas='0.00';
							igv='0.00';
							comision=parseFloat(comision*cti);
						}else{
							tipoope='0';
							valor=parseFloat(precioini/porigv);
							sub=parseFloat(subtotal/porigv);
							igv= parseFloat(subtotal-sub);
							gravadas=sub;
							comision=parseFloat(comision*cti);
						}

var campo='<input type="hidden" id="idp'+cont+'" name="idp'+cont+'" value="'+id+'"> <input type="hidden" id="tipoart'+cont+'" name="tipoart'+cont+'" value="0"> <input type="hidden" id="codigo'+cont+'" name="codigo'+cont+'" value="'+codigo+'"> <input type="hidden" id="umedida'+cont+'" name="umedida'+cont+'" value="'+umedida+'"> <input type="hidden" id="tit'+cont+'" name="tit'+cont+'" value="'+articulo+textoserie+'"> <input type="hidden" id="cti2'+cont+'" name="cti2'+cont+'" value="'+ctimedida+'"> <input type="hidden" id="exon'+cont+'" name="exon'+cont+'" value="'+exonerado+'"> <input type="hidden" id="tipo'+cont+'" name="tipo'+cont+'" value="'+tipoope+'"> <input type="hidden" id="igvh'+cont+'" name="igvh'+cont+'" value="'+igv+'"> <input type="hidden" id="exo'+cont+'" name="exo'+cont+'" value="'+exonf+'">  <input type="hidden" id="gravadas'+cont+'" name="gravadas'+cont+'" value="'+gravadas+'"> <input type="hidden" id="comision'+cont+'" name="comision'+cont+'" value="'+comision+'"> <input type="hidden" id="idunid'+cont+'" name="idunit'+cont+'" value="'+idunit+'"> <input type="hidden" id="grat'+cont+'" name="grat'+cont+'" value="'+gratuitas+'"> <input type="hidden" id="precioo'+cont+'" name="precioo'+cont+'" value="'+precio+'"> <input type="hidden" id="serie'+cont+'" name="serie'+cont+'" value="'+idserie+'"> <input type="hidden" id="puntos'+cont+'" name="puntos'+cont+'" value="'+puntos+'"> <input type="hidden" id="ctipuntos'+cont+'" name="ctipuntos'+cont+'" value="'+ctipuntos+'">  <input type="hidden" id="totdetraccion'+cont+'" name="totdetraccion'+cont+'" value="'+totdetraccion+'">   <input type="hidden" id="cantidad_toneladas'+cont+'" name="cantidad_toneladas'+cont+'" value="0">  <input type="hidden" id="carga_util'+cont+'" name="carga_util'+cont+'" value="0">  <input type="hidden" id="iddestino'+cont+'" name="iddestino'+cont+'" value="0"> ';
campo+=' <input type="hidden" id="inafecta'+cont+'" name="inafecta'+cont+'" value="'+inafecta+'"> ';
var ctif=' <input type="text" onkeyup="teclea('+cont+', '+precio+')" size="3" id="ctif'+cont+'" name="ctif'+cont+'" value="'+cti+'"> ';
var preciof=' <input type="text" onkeyup="teclea('+cont+', '+precio+')" size="5" id="preciof'+cont+'" name="preciof'+cont+'" value="'+precioini+'"> ';
var valorf=' <input type="text" onkeyup="teclea2('+cont+', '+valor+')" size="5" id="valor'+cont+'" name="valor'+cont+'" value="'+valor+'"> ';
var totf=' <input type="text" size="5" id="totf'+cont+'" name="totf'+cont+'" value="'+subtotal+'" readonly > ';
var descuentotxt='<input type="text" size="5" onkeyup="teclea('+cont+', '+precio+')"  id="descuento'+cont+'" name="descuento'+cont+'" value="'+descuentofinal+'"> <input type="hidden" size="5" id="ctidescuento'+cont+'" name="ctidescuento'+cont+'" value="'+descuento+'">';

var subtotaldetalle='<input type="text" id="sub'+cont+'" name="sub'+cont+'" value="'+sub+'" readonly size="5" >';
var igvf=' <input type="text" size="5" id="igv'+cont+'" name="igvh'+cont+'" value="'+_moneyFix2(igv)+'" readonly > ';

						tabladetalles.row.add([
							cont,
							campo+articulo+textoserie,
							umedida,
							ctif,
							preciof,
							/*valorf,  agregar el velor unitario*/
							descuentotxt,
				subtotaldetalle,
				igvf,
	            totf,
							'<button type="button"  class="btn btn-danger btn-xs" ><span class="glyphicon glyphicon-trash"></span></button>'
						]).draw(false);
						cont++;
						totales(exonerado);
					}else{
						Swal.fire('NO HAY SUFICIENTE STOCK');
					}



					/*AQUI ABEXAMOS EL PRODUCTO*/
				}
			}
		});

	}
}
//Función ListarArticulos
function lstcredito(){
	
$('#mcredito').modal('show');	
	
	tabla=$('#tblcredito').dataTable(
	{
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          
		            
		        ],
		"ajax":
				{
					url: 'data/credito-debito.php?op=listarArticulos&tipo=1',
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 14,//Paginación
	    "order": [[ 1, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}

var idrow;
var idp;
var t = $('#tbllistado').DataTable();

function listaranticipo(serie, numero){
	
$("#list").jqGrid("clearGridData");

	
 $.ajax({
url: 'data/credito-debito.php?op=listaranticipo&serie='+serie+'&numero='+numero,
success: function (result) {
	console.log(result);						

for(var i = 0; i < result.length; i++) {
//var item = result.cell[i];
//console.log(result[i].cell[0]);	
console.log(result[i].cell[10]);
var datarow = {
ID_ARTICULO: result[i].cell[0],
CODIGO: result[i].cell[1],
DESCRIPCION: result[i].cell[2],
ID_UNIDAD_MEDIDA: result[i].cell[3],
UNIDAD_MEDIDA: result[i].cell[4],
PRECIO: result[i].cell[5],
CANTIDAD: result[i].cell[6],
SUB_TOTAL: result[i].cell[7],
IGV: result[i].cell[8],
IMPORTE: result[i].cell[9],
COMISION: result[i].cell[10],
PLACA: result[i].cell[11],
ESTADO: 'v'
};
//======================LE AGREGAMOS UN ID A NUESTRO REGISTRO(DETALLE)=====================
var su = jQuery("#list").addRowData($('#txtCOD_ARTICULO').val(), datarow, 'last');
           
						calculateTotal();		 
}
}
});
}
//ORDEN DE CARGA
function listarorden(){

tabla=$('#tbllistadov').dataTable({
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          
		            
		        ],
		"ajax":
				{
					url: 'data/venta.php?op=listarorden&nivel=0',
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 100,//Paginación
	    "order": [[ 1, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}
//Función mostrar formulario
function mostrarformo(flag){
	limpiar();
	if (flag){
		_lockMonedaVenta(false);
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		//$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
		//listarventas();
serieorden();
		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		$("#btnAgregarArt").show();
		detalles=0;
	}
	else
	{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}
//LISTAMOS VENTAS
function listarventas(){
	
var fecha_inicio = $("#txtFECHA_DOCUMENTO").val();
var fecha_fin = $("#txtFECHA_DOCUMENTO").val();
var idusuario= $("#idusuario").val();

$.ajax({
url: 'data/venta.php?op=listarordenf&fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin+'&idusuario='+idusuario,
type: "get",
dataType: 'json',
data:{fecha_inicio: fecha_inicio,fecha_fin: fecha_fin, idusuario: idusuario},
success: function (data) {
    //console.log(data);
	//$('#txtSERIE').val(response.serie);
$.each(data, function(i,datas){

t.row.add( [
            data[i].id,
            data[i].fecha,
            data[i].tdocumento,
            data[i].serie,
			data[i].nombre,
			data[i].docliente,
			data[i].cliente,
			data[i].subtotal,
			data[i].igv,
			data[i].total
        ]).draw( false );
	
});
totalv();
	
},                   
error: function (data) {
console.log(data);
alert('Error Al conectar la Base Datos');
}
});


}

$('#tbllistado tbody').on( 'click', 'tr', function () {
//alert( 'Row index: '+tabla.row( this ).index() );
idrow=t.row( this ).index();
	
if ( $(this).hasClass('selected') ) {
$(this).removeClass('selected');
}else {
t.$('tr.selected').removeClass('selected');
$(this).addClass('selected');
}
} );

$('#eliminar').click( function () {
        t.row('.selected').remove().draw( false );
	totalv();
});

function totalv(){
	
var arrayIds = new Array();
var ids;
var sumtotal=0;
var subtotal=0;	
var igv=0;


$("#tbllistado tr").each(function(index){
    $(this).children("td").each(function(index2){
        switch(index2){
			case 7:
				ids = $(this).text();
				subtotal=parseFloat(subtotal)+parseFloat(ids);
				 break;
			case 8:
                ids = $(this).text();
				igv=parseFloat(igv)+parseFloat(ids);
				 break;
            case 9:
				ids = $(this).text();
				sumtotal=parseFloat(sumtotal)+parseFloat(ids);
             break;						
        }
    });
});
//igv = parseFloat(sumtotal) - parseFloat(sub_total);	
sub_total=subtotal.toFixed(2);
igv=igv.toFixed(2);
sumtotal=sumtotal.toFixed(2);

$('#txtSUB_TOTAL').val(sub_total);
$('#txtIGV').val(igv);
$('#txtTOTAL').val(sumtotal);
	_aplicarFormatoTotalesVenta();
//console.log('------aqui llegamos');
	
}

function serieorden() {
//serie_numero();
	
	console.log('serie orden');
	
//===========================numero correlativo==========================
var TipoComprobate = $('#txtID_TIPO_DOCUMENTO').val();
			
			var serie;
			var tipodoc; var tipoc='';
			
            if (TipoComprobate == '01') {
				tipodoc='RUC';
            } else if (TipoComprobate == '03') {
				tipodoc='DNI';
            }
	
$.ajax({
url: "data/venta.php?op=numeroorden",
type: "get",
dataType: 'json',
data: {"op": "numeroorden"},
success: function (response) {
    console.log(response);
	$('#txtSERIE').val(response.serie);
	$('#serienumero').val(response.seriet);
	ListCliente(tipodoc);
	ListClientepaciente();
},
error: function (data) {
console.log(data);
alert('Error Al conectar la Base Datos');
                            //console.log(data);
}
});
			

}

function verificarfactura(id) {
$("#idcomp").val('');
$('#modalfactura').modal('show');
$("#idcomp").val(id);
	
$.ajax({
url: "modelos/consultas-sunat.php?id=id",
type: "GET",
dataType: 'json',
data: {"id": id},
success: function (data) { 

	$("#mensajefact").html("MENSAJE SUNAT:<br>"+data.msj_sunat);
},
error: function (data) { console.log(data); }
});
	
}

function versolofactura(id) {
$("#idcomp").val('');
$('#modalfactura').modal('show');
$("#idcomp").val(id);
	
$.ajax({
url: "api_cpe/consultas.php",
type: "GET",
dataType: 'json',
data: {"id": id},
success: function (data) { 
	console.log(data); 
	$("#mensajefact").html("MENSAJE SUNAT:<br>"+data.msj_sunat);
},
error: function (data) { console.log(data); }
});
	
}

init();

var imgCodigo=null;
	var spanRefrescaCodigo=null;
		var _jsonEstadoCp = {
			"-" : "-",
			"0" : "NO EXISTE",
			"1" : "ACEPTADO",
			"2" : "ANULADO",
			"3" : "AUTORIZADO",
			"4" : "NO AUTORIZADO"
		};
		
var _jsonEstadoRuc = {
			"-" : "-",
			"00" : "ACTIVO",
			"01" : "BAJA PROVISIONAL",
			"02" : "BAJA PROV. POR OFICIO",
			"03" : "SUSPENSION TEMPORAL",
			"10" : "BAJA DEFINITIVA",
			"11" : "BAJA DE OFICIO",
			"12" : "BAJA MULT.INSCR. Y OTROS ",
			"20" : "NUM. INTERNO IDENTIF.",
			"21" : "OTROS OBLIGADOS",
			"22" : "INHABILITADO-VENT.UNICA",
			"30" : "ANULACION - ERROR SUNAT   "
		};
		
		var _jsonCondicion = {
			"-" : "-",
			"00" : "HABIDO",
			"01" : "NO HALLADO SE MUDO DE DOMICILIO",
			"02" : "NO HALLADO FALLECIO",
			"03" : "NO HALLADO NO EXISTE DOMICILIO",
			"04" : "NO HALLADO CERRADO",
			"05" : "NO HALLADO NRO.PUERTA NO EXISTE",
			"06" : "NO HALLADO DESTINATARIO DESCONOCIDO",
			"07" : "NO HALLADO RECHAZADO",
			"08" : "NO HALLADO OTROS MOTIVOS",
			"09" : "PENDIENTE",
			"10" : "NO APLICABLE",
			"11" : "POR VERIFICAR",
			"12" : "NO HABIDO",
			"20" : "NO HALLADO",
			"21" : "NO EXISTE LA DIRECCION DECLARADA",
			"22" : "DOMICILIO CERRADO",
			"23" : "NEGATIVA RECEPCION X PERSONA CAPAZ",
			"24" : "AUSENCIA DE PERSONA CAPAZ",
			"25" : "NO APLICABLE X TRAMITE DE REVERSION",
			"40" : "DEVUELTO"
		};
		
		var cantidadEval = null;
		var fechaReporte = null;
		var data;


function revisafactura(ruc, tipo,fecha, serie, numero, monto, idventa, pago){

	$.ajax({
		url: "api_cpe/consultas.php?id="+idventa,
		type: "GET",
		dataType: 'json',
		data: {"id":idventa },
		success: function (data) {

			$("#respuestasunat").html(data.msj_sunat);

		},
		error: function (data) { console.log(data); }
	});

$('#modalfactura').modal('show');
$("#idventarevisar").val(idventa);
$("#pagot").val(pago);
	
	
$('#resEstado').html('PROCESANDO...');
$('#resEstadoRuc').html('PROCESANDO...');
$('#resCondicion').html('PROCESANDO...');
$("#divObservaciones").removeClass('hidden');
	
$.ajax({
url: "webservices/validar-compras.php?op=valida",
type: "POST",
dataType: 'json',
data: {"rucconsulta":ruc, tipodoc:tipo, fecha:fecha, serie:serie, numero:numero, monto:monto },
success: function (data) {
console.log(data);
var result=data.data;
console.log(result.estadoCp);

$("#datosdoc").html('CONSULTAR DOCUMENTO SERIE:'+serie+'-'+numero);
	
if (data.success===true) {
console.log(data);

									$("#divResultado").removeClass('hidden');
										$("#uno").removeClass('hidden');
									$("#dos").removeClass('hidden');
									$("#tres").removeClass('hidden');
									$("#cuatro").addClass('hidden');
									var desEstadoCp = "";

									var desEstadoRuc = "";
									var desEstadoCondicion = "";

									$.each(_jsonEstadoCp, function(key, val) {
										if (key == result.estadoCp) {
											desEstadoCp = val;
											return false;
										}
									});

									$.each(_jsonCondicion, function(key, val) {
										if (key == result.condDomiRuc) {
											desEstadoCondicion = val;
											return false;
										}
									});

									$.each(_jsonEstadoRuc, function(key, val) {
										if (key == result.estadoRuc) {
											desEstadoRuc = val;
											return false;
										}
									});
									$("#resEstado").html(desEstadoCp);
									$("#resEstadoRuc").html(desEstadoRuc);
									$("#resCondicion").html(desEstadoCondicion);
									var cadena = "";
									if (result.observaciones != undefined && result.observaciones.length > 0) {
										for (var i = 0; i < result.observaciones.length; i++) {
											cadena = cadena
													+ result.observaciones[i]
													+ "<br>";
										}
										$("#divObservaciones").removeClass('hidden');
									}else{									
										$("#divObservaciones").addClass('hidden');
									}
									$("#resObservaciones").html(cadena);
									
}else{

if (result.estadoCp == "2") {
mostrarError(result.data);		
}
if (result.estadoCp == "0") {
	
console.log(2);	
	
console.log(result);
	
$("#divResultado").removeClass('hidden');
//$("#uno").removeClass('hidden');resEstado
									$("#uno").addClass('hidden');
									$("#dos").addClass('hidden');
									$("#tres").addClass('hidden');
									$("#cuatro").removeClass('hidden');
									//$("#divObservaciones").addClass('hidden');
									$("#divObservaciones").html($("#codComp :selected").text() +" "+ $("#numeroSerie").val()+"-"+$("#numero").val()+" no existe en los registros de SUNAT.");
								
}
}
	
	
	
	
//$("#respuestasunat").html(data.msj_sunat);	
	
},error: function (data) { console.log(data); }
});	
	
	
	
/*	
$.ajax({
url: "api_cpe/consultas.php?id="+idventa,
type: "GET",
dataType: 'json',
data: {"id":idventa },
success: function (data) { 

$("#respuestasunat").html(data.msj_sunat);	
	
},
error: function (data) { console.log(data); }
});
*/	
	
	
	
/*	
$('#resEstado').html('PROCESANDO...');
$('#resEstadoRuc').html('PROCESANDO...');
$('#resCondicion').html('PROCESANDO...');
$("#divObservaciones").removeClass('hidden');
$("#idventa").val(idventa);
$("#pagot").val(pago);
	
$.ajax({
    url : 'http://perudatos.online/consultas/consulta-comprobantes.php',
    data : {ruc:ruc, tipo:tipo,fecha:fecha, serie:serie, numero:numero, monto:monto },
    type : 'GET',
    dataType : 'json',
    success : function(result) {

console.log(result.data);
		
		
$("#datosdoc").html('CONSULTAR DOCUMENTO SERIE:'+serie+'-'+numero);
		
if (result.rpta == "1") {
	
	

									var data = result.data;
									$("#divResultado").removeClass('hidden');
										$("#uno").removeClass('hidden');
									$("#dos").removeClass('hidden');
									$("#tres").removeClass('hidden');
									$("#cuatro").addClass('hidden');
									var desEstadoCp = "";

									var desEstadoRuc = "";
									var desEstadoCondicion = "";

									$.each(_jsonEstadoCp, function(key, val) {
										if (key == data.estadoCp) {
											desEstadoCp = val;
											return false;
										}
									});

									$.each(_jsonCondicion, function(key, val) {
										if (key == data.condDomiRuc) {
											desEstadoCondicion = val;
											return false;
										}
									});

									$.each(_jsonEstadoRuc, function(key, val) {
										if (key == data.estadoRuc) {
											desEstadoRuc = val;
											return false;
										}
									});
									$("#resEstado").html(desEstadoCp);
									$("#resEstadoRuc").html(desEstadoRuc);
									$("#resCondicion").html(desEstadoCondicion);
									var cadena = "";
									if (data.observaciones != undefined && data.observaciones.length > 0) {
										for (var i = 0; i < data.observaciones.length; i++) {
											cadena = cadena
													+ data.observaciones[i]
													+ "<br>";
										}
										$("#divObservaciones").removeClass('hidden');
									}else{									
										$("#divObservaciones").addClass('hidden');
									}
									$("#resObservaciones").html(cadena);
									
}else{
if (result.rpta == "2") {
mostrarError(result.data);		
}
if (result.rpta == "3") {
$("#divResultado").removeClass('hidden');
//$("#uno").removeClass('hidden');
									$("#uno").addClass('hidden');
									$("#dos").addClass('hidden');
									$("#tres").addClass('hidden');
									$("#cuatro").removeClass('hidden');
									$("#divObservaciones").addClass('hidden');
									$("#resObservaciones2").html($("#codComp :selected").text() +" "+ $("#numeroSerie").val()+"-"+$("#numero").val()+" no existe en los registros de SUNAT.");
								
}
}
		
		

    },
    error : function(xhr, status) {
        alert('Disculpe, existió un problema');
    },
    complete : function(xhr, status) {
        //alert('Petición realizada');
    }
});
*/	
	
}

function cambiaestado(estado) {

var id=$("#idventarevisar").val();

console.log('estado:'+estado);
	
$.ajax({

url: "data/venta.php?op=estadofact&id="+id+"&estado="+estado,
type: "GET",
dataType: 'json',
data: {"id": id},
success: function (data) {
	console.log(data); 
Swal.fire(data.mensaje);
$('#modalfactura').modal('toggle');
tabla.ajax.reload( null, false );
},
 
error: function (data) { console.log(data); }
});



}

function impresionf(idventa){

console.log('idventa:'+idventa);
	
$("#mydiv").css("display", "block");
	
	$.ajax({
		type: "GET",
		url: "plugins/dompdf/ticket.php?op=llenaimpresion&id="+idventa+"&nivel=1",
		success: function(datos) {

//console.log(datos);

			$("#mydiv").html(datos);

			printJS({
				printable: 'mydiv',
				type: 'html',
				style: '#mydiv { width: 100px; }',
				scanStyles: false
			})

			$("#mydiv").css("display", "none");
			$("#mydiv").html("");
		}
	})
	
	

}

function impresionftec(idventa){

	console.log('idventa:'+idventa);

	$("#mydiv").css("display", "block");

	$.ajax({
		type: "GET",
		url: "plugins/dompdf/ticketec.php?op=llenaimpresion&id="+idventa+"&nivel=1",
		success: function(datos) {

//console.log(datos);

			$("#mydiv").html(datos);

			printJS({
				printable: 'mydiv',
				type: 'html',
				style: '#mydiv { width: 100px; }',
				scanStyles: false
			})

			$("#mydiv").css("display", "none");
			$("#mydiv").html("");
		}
	})



}

function eliminarpago(id, idventa){
	
	
Swal.fire({
    title: 'ESTAS SEGURO DE ELIMINAR PAGO?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'ELIMINAR',
	cancelButtonText: 'CANCELAR'
}).then((result) => {
    if (result.value) {
		
var dataString = {idventa:id};
$.ajax({
            type: "POST",
            url: "data/venta.php?op=eliminapago&nivel=0",
            data: dataString,
dataType : 'json',
success: function(data) {
				
				
                Swal.fire(
                    'DELETE',
                    'Se elimino correctamente',
                    'success',
                ).then(function () {
					
					
versaldo(idventa);
tablapago.ajax.reload();	
					
					
					
                });
            },
			error : function(data) {
			console.log(data);
			},
        })
    } else if (result.dismiss === swal.DismissReason.cancel) {
        swal.fire(
            'Canceled',
            'No se ha eliminado',
            'error'
        )
    }
});
	
	
}




// ======================================================
// REQ-2: Botón "Pagago" (onclick="pagado(idPago, idVenta)")
// - Debe abrir un MODAL encima de #pagof y mostrar el registro de caja_ventapago
// - Permite editar datos y cobrar (actualiza caja_ventapago)
// ======================================================
function pagado(idPago, idVenta){
	try{
		if(!idPago){ return; }
		// Guardar contexto
		$('#pago_edit_id').val(idPago);
		$('#pago_edit_idventa').val(idVenta || '');

		// Asegurar stacking correcto (2 modales)
		try{
			$('#modalPagoDatos').addClass('modal-stack');
		}catch(e0){}

		// 1) Cargar datos del pago
		$.getJSON('data/venta.php?op=getpagodatos&id=' + encodeURIComponent(idPago), function(r){
			if(!r || r.estado !== '1'){
				alert((r && r.mensaje) ? r.mensaje : 'No se pudo obtener los datos del pago.');
				return;
			}
			var d = r.data || {};

			// 2) Pintar campos
			$('#fechaoperacion').val(_formatFechaOperacionInput(d.fechaoperacion || '')); 
			$('#noperacion').val(d.operacion || '');
			$('#montooperacion').val((d.montooperacion !== undefined && d.montooperacion !== null) ? d.montooperacion : '0.00');
			$('#pagocomentarios').val(d.comentarios || '');

			// 3) Cargar select2 de medio de pago
			_cargarTipoPagoSelect2(d.idtipo || null, function(){
				// 4) Mostrar modal siempre al frente
				$('#modalPagoDatos').appendTo('body');
				_bringModalPagoDatosToFront();
				$('#modalPagoDatos').modal('show');
				setTimeout(function(){ _bringModalPagoDatosToFront(); }, 30);
				setTimeout(function(){ _bringModalPagoDatosToFront(); }, 120);
			});
		}).fail(function(){
			alert('Error al consultar datos del pago.');
		});
	}catch(e){
		console.error(e);
		alert('Error al abrir el detalle de pago.');
	}
}


function _bringModalPagoDatosToFront(){
	try{
		var $m = $('#modalPagoDatos');
		if(!$m.length){ return; }

		$m.appendTo('body');

		var maxZ = 1040;
		$('.modal:visible, .modal-backdrop').each(function(){
			var zi = parseInt($(this).css('z-index'), 10);
			if(!isNaN(zi) && zi > maxZ){ maxZ = zi; }
		});

		var modalZ = maxZ + 20;
		var backdropZ = modalZ - 10;

		$m.addClass('modal-stack');
		$m.get(0).style.setProperty('z-index', String(modalZ), 'important');

		setTimeout(function(){
			var $bd = $('.modal-backdrop').last();
			if($bd.length){
				$bd.addClass('modal-stack');
				$bd.get(0).style.setProperty('z-index', String(backdropZ), 'important');
			}
		}, 0);
	}catch(e){ console.error(e); }
}

// Carga opciones de caja_tipopago y selecciona el valor indicado

/**
 * Convierte fecha/hora (DB) a formato compatible con <input type="date">
 * Acepta: YYYY-MM-DD, YYYY-MM-DD HH:MM:SS, DD/MM/YYYY
 */
function _formatFechaOperacionInput(val){
	if(!val){ return ''; }
	val = String(val).trim();
	// YYYY-MM-DD HH:MM:SS -> YYYY-MM-DD
	var m1 = val.match(/^(\d{4})-(\d{2})-(\d{2})/);
	if(m1){ return m1[1] + '-' + m1[2] + '-' + m1[3]; }
	// DD/MM/YYYY -> YYYY-MM-DD
	var m2 = val.match(/^(\d{2})\/(\d{2})\/(\d{4})/);
	if(m2){ return m2[3] + '-' + m2[2] + '-' + m2[1]; }
	return val;
}

function _cargarTipoPagoSelect2(idSeleccionado, cb){
	try{
		var $sel = $('#idtipo_pago_edit');
		if(!$sel.length){ if(typeof cb==='function') cb(); return; }

		// Inicializar select2 (una sola vez)
		if(!$sel.hasClass('select2-hidden-accessible')){
			$sel.select2({
				placeholder: 'SELECCIONE',
				width: '100%',
				dropdownParent: $('#modalPagoDatos')
			});
		}

		// Cargar opciones desde backend
		$.getJSON('data/venta.php?op=cajatipopagos', function(map){
			$sel.empty();
			$sel.append('<option value=""></option>');
			try{
				Object.keys(map || {}).forEach(function(k){
					$sel.append('<option value="'+k+'">'+map[k]+'</option>');
				});
			}catch(e2){}

			if(idSeleccionado !== null && idSeleccionado !== undefined && String(idSeleccionado) !== ''){
				$sel.val(String(idSeleccionado)).trigger('change');
			}else{
				$sel.val('').trigger('change');
			}
			if(typeof cb==='function') cb();
		}).fail(function(){
			if(typeof cb==='function') cb();
		});
	}catch(e){
		console.error(e);
		if(typeof cb==='function') cb();
	}
}


function _volverAModalPagoVenta(){
	try{
		var $pagof = $('#pagof');
		if(!$pagof.length){ return; }

		$pagof.appendTo('body');
		$pagof.modal('show');

		setTimeout(function(){
			$('body').addClass('modal-open');
			$pagof.get(0).style.setProperty('z-index', '1050', 'important');
			var $bd = $('.modal-backdrop').last();
			if($bd.length){
				$bd.get(0).style.setProperty('z-index', '1040', 'important');
			}
		}, 0);
	}catch(e){}
}

// Eventos del modal (Cobrar / Cancelar)
$(document).off('click', '#btnCancelarPagoEdit').on('click', '#btnCancelarPagoEdit', function(){
	try{
		window._volverAPagoVentaDesdeModalPagoDatos = true;
		$('#modalPagoDatos').modal('hide');
	}catch(e){}
});

// Backdrop stacking helper (Bootstrap 3: segundo modal sobre el primero)
$(document).off('shown.bs.modal', '#modalPagoDatos').on('shown.bs.modal', '#modalPagoDatos', function(){
	try{ _bringModalPagoDatosToFront(); }catch(e){}
});


$(document).off('hidden.bs.modal', '#modalPagoDatos').on('hidden.bs.modal', '#modalPagoDatos', function(){
	try{
		this.style.removeProperty('z-index');
		$('.modal-backdrop.modal-stack').last().each(function(){ this.style.removeProperty('z-index'); }).removeClass('modal-stack');

		if(window._volverAPagoVentaDesdeModalPagoDatos){
			window._volverAPagoVentaDesdeModalPagoDatos = false;
			_volverAModalPagoVenta();
		}
	}catch(e){}
});

$(document).off('click', '#btnCobrarPagoEdit').on('click', '#btnCobrarPagoEdit', function(){
	try{
		var idPago = $('#pago_edit_id').val();
		var idVenta = $('#pago_edit_idventa').val();
		var fechaop = $('#fechaoperacion').val();
		var oper = $('#noperacion').val();
		var monto = $('#montooperacion').val();
		var com = $('#pagocomentarios').val();
		var idtipo = $('#idtipo_pago_edit').val();

		// Validaciones mínimas
		if(!idPago){ return alert('No se encontró el pago seleccionado.'); }
		if(!fechaop){ return alert('La fecha de operación es obligatoria.'); }
		if(!oper){ return alert('El N° de operación es obligatorio.'); }
		if(!monto || parseFloat(String(monto).replace(/,/g,'')) <= 0){ return alert('El monto debe ser mayor a 0.'); }
		if(!idtipo){ return alert('Debe seleccionar el medio de pago.'); }

		var data = {
			id: idPago,
			fechaoperacion: fechaop,
			operacion: oper,
			montooperacion: monto,
			comentarios: com,
			idtipo: idtipo
		};

		$.ajax({
			type: 'POST',
			url: 'data/venta.php?op=updatepagodatos',
			data: data,
			dataType: 'json'
		}).done(function(r){
			if(r && r.estado === '1'){
				window._volverAPagoVentaDesdeModalPagoDatos = true;
				try{ $('#modalPagoDatos').modal('hide'); }catch(e0){}
				// Refrescar tabla + saldo (modal principal permanece abierto)
				try{ if(tablapago && tablapago.ajax){ tablapago.ajax.reload(null, false); } }catch(e1){}
				try{ if(typeof versaldo === 'function'){ var niv = ($('#nivelpago').val()!==undefined) ? $('#nivelpago').val() : 0; versaldo(idVenta, niv); } }catch(e2){}
				try{ alert(r.mensaje || 'Pago actualizado.'); }catch(e3){}
			}else{
				alert((r && r.mensaje) ? r.mensaje : 'No se pudo actualizar el pago.');
			}
		}).fail(function(xhr){
			alert('Error al actualizar el pago.');
		});
	}catch(e){
		console.error(e);
		alert('Error al procesar el cobro.');
	}
});



var tablarel;

function listardoc2(){
	
	tablarel=$('#listacomprobantes2').dataTable({
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          
		            
		        ],
		"ajax":
				{
					url: 'data/venta.php?op=listarpedidoanticipo',
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true, 
		"iDisplayLength": 7,//Paginación
	    "order": [[1, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
	
	$('#myModaCOMP2').modal('show');
}
var guiarel;

function listarguia(){
	
	guiarel=$('#listaguia').dataTable({
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          
		            
		        ],
		"ajax":
				{
					url: 'data/venta.php?op=listarguia',
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 7,//Paginación
	    "order": [[1, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
	
	$('#modalguia').modal('show');
}

function addguia(serie, numero, nombre, idcliente, id){


/**/	
$("#guia").val(serie+'-'+numero);
$("#idguia").val(id);

console.log('idguia:'+id);

$('#modalguia').modal('toggle');
$("#txtID_CLIENTE").val(idcliente);

$("#txtID_CLIENTE").selectpicker('refresh');


console.log(nombre);

listarguiadet(id);
	
}

function listarguiadet(id){

console.log('guia:'+$("#guia").val());

$.ajax({
	url:'data/venta.php?op=listarguiadetalle&id='+id,
	dataType : "json",
	success: function(respuesta) {
		console.log(respuesta);
		
for(var i=0;i<respuesta.length;i++){

tabladetalles.row.add([
respuesta[i][0], 
respuesta[i][1],
respuesta[i][2],
respuesta[i][3],
respuesta[i][4],
respuesta[i][5],
respuesta[i][6],
respuesta[i][7],
respuesta[i][8]
        ]).draw(false);	
        
 totales(0);	       
        
}
	
		
	},
	error: function(data) {
		
		
        console.log(data);
    }
});
		
}

function adddocr2(serief, numero, otro1, otro2, id){
	
var montoanticipo=$("#montoanticipo").val();
var porigv=$("#porcentajeigv").val();
porigv=porigv/100;
porigv=porigv+1.00;	
	
if(montoanticipo=='0.00'){
	
Swal.fire('DEBE PONER UN ANTICIPO');
		
}else{

$("#serieanticipo").val(serief+'-'+numero);
	
var dataString = {id:id, anticipo:montoanticipo};
$.ajax({
            type: "POST",
            url: "data/venta.php?op=anticipocabeza",
            data: dataString,
dataType : 'json',
success: function(data) {

if(data.idanticipo=='3'){
	
Swal.fire('EL MONTO POR PAGAR NO DEBE SER MAYOR A LA DEUDA');
	
}else{
	
console.log(data);
console.log('montoanticipo:'+montoanticipo);

$("#totanticipo").val(data.pagado);
$("#totsaldo").val(data.saldo);
$("#idanticipo").val(data.idanticipo);
$("#totpagar").val(data.total);
	_aplicarFormatoTotalesVenta();
	
$("#txtID_TIPO_DOCUMENTO").val(data.docmodifica_tipo);
$("#txtID_TIPO_DOCUMENTO").selectpicker('refresh');
serie();
$('#txtMONEDA option[value='+data.txtID_MONEDA+']').prop('selected', 'selected').change();	

$("#txtID_CLIENTE").val(data.txtID_CLIENTE);
$("#txtID_CLIENTE").selectpicker('refresh');


	
var igv='0.00';
var cti='1.00';
var descuentofinal='0.00';
var gratuitas='0.00';
var gravadas='0.00';
var subtotal='0.00';
var exonf='0.00';
var idunit='0';	
var idserie='0';
var ctimedida='1';
var idu=data.idproducto;
var comision='0';
var umedida=data.unidadmedida;
	
var precio=montoanticipo;
	
if(data.tipo=='1'){
subtotal=montoanticipo;
exonf=montoanticipo;
}else{
gravadas=parseFloat(montoanticipo/porigv);
igv=montoanticipo-gravadas;
subtotal=gravadas;
}
	
var articulo=data.nombreproducto;
	
var campo='<input type="hidden" id="idp'+idu+'" name="idp'+idu+'" value="'+data.idproducto+'"> <input type="hidden" id="codigo'+idu+'" name="codigo'+idu+'" value="'+data.codigoproducto+'"> <input type="hidden" id="umedida'+idu+'" name="umedida'+idu+'" value="'+data.unidadmedida+'"> <input type="hidden" id="tit'+idu+'" name="tit'+idu+'" value="'+articulo+'"> <input type="hidden" id="cti2'+idu+'" name="cti2'+idu+'" value="'+ctimedida+'"> <input type="hidden" id="exon'+idu+'" name="exon'+idu+'" value="'+exonf+'"> <input type="hidden" id="tipo'+idu+'" name="tipo'+idu+'" value="'+data.tipo+'"> <input type="hidden" id="igvh'+idu+'" name="igvh'+idu+'" value="'+igv+'"> <input type="hidden" id="exo'+idu+'" name="exo'+idu+'" value="'+exonf+'"> <input type="hidden" id="sub'+idu+'" name="sub'+idu+'" value="'+subtotal+'"> <input type="hidden" id="gravadas'+idu+'" name="gravadas'+idu+'" value="'+gravadas+'"> <input type="hidden" id="comision'+idu+'" name="comision'+idu+'" value="'+comision+'"> <input type="hidden" id="idu'+id+'" name="idu'+id+'" value="'+idu+'"> <input type="hidden" id="idunid'+idu+'" name="idunit'+idu+'" value="'+idunit+'"> <input type="hidden" id="grat'+idu+'" name="grat'+idu+'" value="'+gratuitas+'"> <input type="hidden" id="precioo'+idu+'" name="precioo'+idu+'" value="'+precio+'"> <input type="hidden" id="serie'+idu+'" name="serie'+idu+'" value="'+idserie+'"> <input type="hidden" id="descuento'+idu+'" name="descuento'+idu+'" value="'+descuentofinal+'"> <input type="hidden" id="puntos'+idu+'" name="puntos'+idu+'" value="0"> <input type="hidden" id="ctipuntos'+idu+'" name="ctipuntos'+idu+'" value="0">    <input type="hidden" id="totdetraccion'+idu+'" name="totdetraccion'+idu+'" value="0">  <input type="hidden" id="cantidad_toneladas'+idu+'" name="cantidad_toneladas'+idu+'" value="0">  <input type="hidden" id="carga_util'+idu+'" name="carga_util'+idu+'" value="0">  <input type="hidden" id="iddestino'+idu+'" name="iddestino'+idu+'" value="0"> ';
	
var ctif=' <input type="text" onkeyup="teclea('+idu+', '+precio+')" size="3" id="ctif'+idu+'" name="ctif'+idu+'" value="'+cti+'"> ';
	
var preciof=' <input type="text" onkeyup="teclea('+idu+', '+precio+')" size="5" id="preciof'+idu+'" name="preciof'+idu+'" value="'+precio+'"> ';
	
var totf=' <input type="text" size="5" id="totf'+idu+'" name="totf'+idu+'" value="'+precio+'" readonly > ';
var subtotaldetalle='<input type="text" id="sub'+idu+'" size="5" name="sub'+idu+'" readonly value="'+_moneyFix2(subtotal)+'">';
var igvf=' <input type="text" size="5" id="igv'+idu+'" name="igvh'+idu+'" value="'+_moneyFix2(igv)+'" readonly > ';
var descuentof=' <input type="text" size="5" id="descuento'+idu+'" name="descuento'+idu+'" value="0.00" readonly > ';

tabladetalles.row.add([
			idu, 
			campo+articulo,
	umedida,
			ctif,
            preciof,
	descuentof,
				subtotaldetalle,
				igvf,
	            totf,
	'<button type="button"  class="btn btn-danger btn-xs" ><span class="glyphicon glyphicon-trash"></span></button>'
        ]).draw(false);
	
totales(0);
	
$("#myModaCOMP2").modal('hide');
$('body').removeClass('modal-open');
$('.modal-backdrop').remove();
	
}

	
},
	error: function(error) {
        console.log(error);
    }
});

		
	
}	
	
	
}

/*AQUI LISTAMOS LOS SERVICIOS*/
function Buscarserv(){
if(!_validarFpagoSeleccion()){ return false; }
_lockMonedaVenta(true);
$('#precioserv').val('0.00');
$('#titservicio').val('');
verdescuento();
	
$('#ventaservicios').modal('show');
$('#detalleserv').val('');	
	
	$.post("data/venta.php?op=listarservicio", function(r){
	    $("#listservicio").html(r);
		$('#listservicio').selectpicker('refresh');
	});
}

function cambiaservicio(){

var untot=$('#listservicio').val().split('|');
var umedida=untot[0];
var precio=untot[1];
var comision=untot[2];
var codigo=untot[3];
var id=untot[4];
var tit=untot[5];

$('#precioserv').val(precio);
$('#titservicio').val(tit);

}

var numserv='0';

function addservicio(){

var untot=$('#listservicio').val().split('|');

var comision=untot[2];
var idu=untot[3];
var id=untot[3];
var codigo=untot[4];
var calculaigv=$('#calculaigv').val();
var precio=$('#precioserv').val();
var tipoigv=$('#igvservicio').val();
var igvsub='0';

var tservicio=$('#tservicio').val();
var ruta='0';
var destinoid='0';
var destinoid2='0';
var parcial='0';
var acumulada='0';
var montotonelada='0';
var tiporecorrido='0';
var ctitoneladas='0';
var totdetraccion2='0';
var cargautil='0';
var detracciones=$('#detracciones').val();
var carga_util=$('#cargautil').val();	
var cantidad_toneladas=$('#ctitoneladas').val();
	
numserv=parseFloat(numserv)+1;

if(tipoigv=='0'){
if(calculaigv=='0'){
precio=precio;	
}else{
igvsub=parseFloat(precio*0.18);
precio=parseFloat(precio)+parseFloat(igvsub);
}
}
var titservicio=$('#titservicio').val();
var detalleserv=$('#detalleserv').val();
var detalleserv=$('#detalleserv').val();
var umedida=$('#medidaserv').val();

var igv='0.00';
var cti='1.00';
var descuentofinal='0.00';
var gratuitas='0.00';
var gravadas='0.00';
var subtotal='0.00';
var exonf='0.00';
var idunit='0';	
var idserie='0';
var ctimedida='1';
var comision='0';
	
if(tipoigv=='1'){
subtotal=precio;
exonf=precio;
}else{
gravadas=parseFloat(precio/1.18);
igv=precio-gravadas;
igv=igv.toFixed(2);
gravadas=gravadas.toFixed(2);
subtotal=gravadas;
}


var total=parseFloat(precio)*parseFloat(cti);
	
if(detracciones!='0|0|0'){
	
if(tservicio!='0'){
	
ruta=$('#ruta').val();
if(ruta=='0'){ Swal.fire('SELECCIONE UNA RUTA'); return true; }
destinoid=$('#destino').val();	
if(destinoid=='0'){ Swal.fire('SELECCIONE UN DESTINO'); return true; }
ctitoneladas=$('#ctitoneladas').val();		
if(ctitoneladas=='0.00'){ Swal.fire('DEBE INDICAR LAS TONALADAS'); return true; }
cargautil=ruta=$('#cargautil').val();	
if(cargautil=='0'){ Swal.fire('SELECCIONE LA CARGA ÚTIL'); return true; }

var porcentaje = ((parseFloat(ctitoneladas)/parseFloat(cargautil))*100).toFixed(7);	
var topoporcentaje=parseFloat(69.00);

if(porcentaje>topoporcentaje){ 
//Swal.fire('LA CARGA ÚTIL DEBE SER MINIMO '+topoporcentaje+'%:'+porcentaje);
}else{
Swal.fire('LA CARGA ÚTIL DEBE SER MINIMO 70%:'); return true;
}

destinoid=destinoid.split('|');
destinoid2=destinoid[0];
parcial=destinoid[1];
acumulada=destinoid[2];
montotonelada=destinoid[3];

console.log('montotonelada:'+montotonelada);
console.log('ctitoneladas:'+ctitoneladas);
	
totdetraccion2=parseFloat(ctitoneladas)*parseFloat(montotonelada);
//totdetraccion2=parseFloat(porcentaje)*parseFloat(totdetraccion2)/100;	

}
}


var articulo='<b>'+titservicio+'</b>\n'+detalleserv;
	
var campo='<input type="hidden" id="idp'+cont+'" name="idp'+cont+'" value="'+idu+'"> <input type="hidden" id="tipoart'+cont+'" name="tipoart'+cont+'" value="1"> <input type="hidden" id="codigo'+cont+'" name="codigo'+cont+'" value="'+codigo+'"> <input type="hidden" id="umedida'+cont+'" name="umedida'+cont+'" value="'+umedida+'"> <input type="hidden" id="tit'+cont+'" name="tit'+cont+'" value="'+articulo+'"> <input type="hidden" id="cti2'+cont+'" name="cti2'+cont+'" value="'+ctimedida+'"> <input type="hidden" id="exon'+cont+'" name="exon'+cont+'" value="'+exonf+'"> <input type="hidden" id="tipo'+cont+'" name="tipo'+cont+'" value="'+tipoigv+'"> <input type="hidden" id="igvh'+cont+'" name="igvh'+cont+'" value="'+igv+'"> <input type="hidden" id="exo'+cont+'" name="exo'+cont+'" value="'+exonf+'"> <input type="hidden" id="sub'+cont+'" name="sub'+cont+'" value="'+subtotal+'"> <input type="hidden" id="gravadas'+cont+'" name="gravadas'+cont+'" value="'+gravadas+'"> <input type="hidden" id="comision'+cont+'" name="comision'+cont+'" value="'+comision+'"> <input type="hidden" id="idu'+id+'" name="idu'+id+'" value="'+cont+'"> <input type="hidden" id="idunid'+cont+'" name="idunit'+cont+'" value="'+idunit+'"> <input type="hidden" id="grat'+cont+'" name="grat'+cont+'" value="'+gratuitas+'"> <input type="hidden" id="precioo'+cont+'" name="precioo'+cont+'" value="'+precio+'"> <input type="hidden" id="serie'+cont+'" name="serie'+cont+'" value="'+idserie+'"> <input type="hidden" id="puntos'+cont+'" name="puntos'+cont+'" value="0"> <input type="hidden" id="ctipuntos'+cont+'" name="ctipuntos'+cont+'" value="0">  <input type="hidden" id="totdetraccion'+cont+'" name="totdetraccion'+cont+'" value="'+totdetraccion2+'">   <input type="hidden" id="cantidad_toneladas'+cont+'" name="cantidad_toneladas'+cont+'" value="'+cantidad_toneladas+'">  <input type="hidden" id="carga_util'+cont+'" name="carga_util'+cont+'" value="'+carga_util+'">  <input type="hidden" id="iddestino'+cont+'" name="iddestino'+cont+'" value="'+destinoid2+'">';
	
var ctif=' <input type="text" onkeyup="teclea('+cont+', '+precio+')" size="3" id="ctif'+cont+'" name="ctif'+cont+'" value="'+cti+'"> ';
	
var preciof=' <input type="text" onkeyup="teclea('+cont+', '+precio+')" size="5" id="preciof'+cont+'" name="preciof'+cont+'" value="'+precio+'"> ';
	
var totf=' <input type="text" size="5" id="totf'+cont+'" name="totf'+cont+'" value="'+precio+'" readonly > ';
var subtotaldetalle='<input type="text" id="sub'+cont+'" size="5" name="sub'+cont+'" readonly value="'+_moneyFix2(subtotal)+'">';
var igvf=' <input type="text" size="5" id="igv'+cont+'" name="igvh'+cont+'" value="'+_moneyFix2(igv)+'" readonly > ';
	
var descuentotxt='<input type="text" onkeyup="teclea('+cont+', '+precio+')"  size="5" id="descuento'+cont+'" name="descuento'+cont+'"  value="'+descuentofinal+'" > <input type="hidden" size="5" id="ctidescuento'+cont+'" name="ctidescuento'+cont+'" value="0">';

tabladetalles.row.add([
			cont, 
			campo+articulo,
			umedida,
			ctif,
            preciof,
			descuentotxt,
				subtotaldetalle,
				igvf,
	            totf,
	'<button type="button"  class="btn btn-danger btn-xs" ><span class="glyphicon glyphicon-trash"></span></button>'
        ]).draw(false);
cont++;	
totales(0);
$('#titservicio').val('');
$('#precioserv').val('0.00');
$('#detalleserv').val('');	
$('#listservicio').val('');
$("#listservicio").selectpicker('refresh');
	
}

var tablasaldo= $('#listarsaldo').DataTable();

function mostrarsaldo(id){
	
$('#modalsaldo').modal('show');	

$.ajax({
	url:'data/chofer.php?op=listarsaldo&id='+id,
	dataType : "json",
	success: function(respuesta) {
		console.log(respuesta);
		
for(var i=0;i<respuesta.length;i++){

tablasaldo.row.add([
respuesta[i][0], 
respuesta[i][1],
respuesta[i][2],
respuesta[i][3],
respuesta[i][4],
respuesta[i][5],
respuesta[i][6],
respuesta[i][7]
        ]).draw(false);	
}
		
		
	},
	error: function(respuesta) {
        console.log(respuesta);
    } 
});
	
}

function descargaxml(id){
	
window.location.href = 'modelos/descargas.php?op=descargaxml&id='+id; 
	
} 

function descargapdfs(){ 
	
var precio=$('#precioserv').val();
/*	
Swal.fire('<i class="glyphicon glyphicon-refresh"></i> PROCESANDO');
*/
var selected_items = [];
	
var rows_selected = tabla.column(0).checkboxes.selected();
$.each(rows_selected, function(index, rowId){	
selected_items.push({rowId});		  			  
});
var datos=JSON.stringify(selected_items);	
var hash = window.btoa(datos);

window.open("plugins/dompdf/index-multi.php?id="+hash, '_blank');
	
}

function correosmasivos(){
	
Swal.fire('<i class="fa fa-circle-o-notch fa-spin "></i> PROCESANDO CORREOS!');
	
var selected_items = [];
	
var rows_selected = tabla.column(0).checkboxes.selected();
$.each(rows_selected, function(index, rowId){	
selected_items.push({rowId});		  			  
});
var datos=JSON.stringify(selected_items);	
var hash = window.btoa(datos);
	
$.ajax({
	url:'modelos/correos-masivos.php',
	dataType : "json",
	type : 'GET',
	data : {id:hash},
	success: function(respuesta) {
		console.log(respuesta);
		swal.close();
		Swal.fire(respuesta.mensaje);
},
	error: function(respuesta) {
        console.log(respuesta);
    } 
});
	
}

function enviossmasivos(){
	
Swal.fire('<i class="fa fa-circle-o-notch fa-spin "></i> PROCESANDO COMPROBANTES!');
	
var selected_items = [];
	
var rows_selected = tabla.column(0).checkboxes.selected();
$.each(rows_selected, function(index, rowId){	
selected_items.push({rowId});		  			  
});
var datos=JSON.stringify(selected_items);	
var hash = window.btoa(datos);
	
$.ajax({
	url:'modelos/envio-masivo.php',
	dataType : "json",
	type : 'GET',
	data : {id:hash},
	success: function(respuesta) {
		console.log(respuesta);
		swal.close();
		Swal.fire(respuesta.mensaje);
		tabla.ajax.reload();
},
	error: function(respuesta) {
        console.log(respuesta);
    } 
});
	
}

function numerosfinales(){
		
var serie=$('#txtSERIE').val();	
var tipo=$('#txtID_TIPO_DOCUMENTO').val();
var tipo3=$("#txtID_TIPO_DOCUMENTO").val();
	
$.post("data/series.php?op=numeroventas", { elegido:serie, tipo:tipo }, function(data){
$("#txtNUMERO").val(data.numero);
 });
	
	
}

function llamaseries(){
var tipo=$('#txtID_TIPO_DOCUMENTO').val();	
seriesfinales(tipo);
}

function seriesfinales(tipo){
/**/
$.post("data/series.php?op=serieventas", {elegido:tipo}, function(data){
$("#txtSERIE").html(data);
numerosfinales();
clientesfinales('', '');
 });

}

function tipoguia(tipo, nombre){ 

$('#tipoguia').val(tipo);
$("#nombreguia").html(nombre);
	
}
/*
$(document).ready(function(){
    $("#txtID_TIPO_DOCUMENTO").on('change', function () {
        $("#txtID_TIPO_DOCUMENTO option:selected").each(function () {
            elegido=$(this).val();
		clientesfinales();
        seriesfinales(elegido);		
        });
   });
});
*/
function guardarventa(directosunat){

	var cliente=$("#txtID_CLIENTE option:selected").val();
	var tipopago=$('#pago').val();
	
	if(cliente==undefined){ Swal.fire('¡ SELECCIONE  CLIENTE!'); return true; }
	if (!tabladetalles.data().any() ) { Swal.fire('¡ AGREGUE MINIMO UN PRODUCTO!'); return true; }

var d = new Date(); 
var month = d.getMonth()+1; 
var day = d.getDate(); 
var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;

$('#fechapago2').val(output)

	$( "#fechapago2" ).prop( "disabled",  false);
if(tipopago=='CONTADO'){
	$( "#fechapago2" ).prop( "disabled", true);
}
/*
Swal.fire({
	title: 'DESEA AGREGAR LOS PAGOS?',
	showDenyButton: true,
	confirmButtonText: 'Crear pagos',
	denyButtonText: `Cancelar`,
}).then((result) => {

	if (result.isConfirmed) {
	  //Swal.fire('Saved!', '', 'success')
modalltras(2, directosunat);
	} else if (result.isDenied) {
		guardarventa2(directosunat);
	}
})
*/
_generarCuotasSegunFpago(function(ok){
	if(!ok){ return; }
	guardarventa2(directosunat);
});


}

function guardarventa2(directosunat){

	Swal.fire({
		title: "ENVIANDO INFORMACIÓN!",
		text: "Porfavor espere un momento",
		type: "info",
		showLoaderOnConfirm: true,
		onOpen: function(){
			swal.clickConfirm();
		},
		preConfirm: function() {
			return new Promise(function(resolve) {

			});
		},allowOutsideClick: false
	});

$('.guardaventa').attr('disabled', true);	
//$('#txtID_CLIENTE').val('').selectpicker('refresh');

	['#gravadas','#txtSUB_TOTAL','#gratuita','#exoneradof','#inafecta','#txtIGV','#txtTOTAL','#valref','#comisiont','#descuentotot','#totanticipo','#totsaldo','#totpagar','#totalf','#montodetraccion'].forEach(function(sel){
		var $el=$(sel); if($el.length){ $el.val(_moneyRaw($el.val())); }
	});

	var formData = new FormData($("#miForm")[0]);

var detracciones=$('#detracciones').val();
	
var iddetraccion='0';
var montodetraccion='0';
	
if(detracciones!='0|0|0'){	
var ret=$('#detracciones').val().split('|');
iddetraccion=ret[0];
montodetraccion=$('#montodetraccion').val();	
}
	
console.log('tipodoc:'+$('#txtID_TIPO_DOCUMENTO').val());	
	
formData.append('montodetraccion', _moneyFix2(montodetraccion));	
formData.append('iddetraccion', iddetraccion);	
formData.append('gravadas', _moneyFix2($('#gravadas').val()));
formData.append('directosunat', directosunat);
formData.append('txtID_TIPO_DOCUMENTO', $('#txtID_TIPO_DOCUMENTO').val());
formData.append('txtSERIE', $('#txtSERIE').val());	
formData.append('txtSUB_TOTAL', _moneyFix2($('#txtSUB_TOTAL').val()));
formData.append('txtIGV', _moneyFix2($('#txtIGV').val()));
formData.append('txtTOTAL', _moneyFix2($('#txtTOTAL').val()));
formData.append('referencial', _moneyFix2($('#valref').val()));	
formData.append('descuentotot', _moneyFix2($('#descuentotot').val()));	
formData.append('inafecta', _moneyFix2($('#inafecta').val()));
formData.append('exportacion', $('#exportacion').val());
formData.append('controlpresupuestal', $('#controlpresupuestal').val());	
	
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
detalle["exonerado"]=$('#exo'+idp).val();
detalle["gratuita"]=$('#grat'+idp).val();
detalle["tipoart"]=$('#tipoart'+idp).val();		
detalle["serie"]=$('#serie'+idp).val();
detalle["descuento"]=$('#descuento'+idp).val();
detalle["placa"]='';

detalle["detracciond"]=$('#totdetraccion'+idp).val();
detalle["iddestino"]=$('#iddestino'+idp).val();
detalle["carga_util"]=$('#carga_util'+idp).val();
detalle["cantidad_toneladas"]=$('#cantidad_toneladas'+idp).val();
detalle["inafecta"]=$('#inafecta'+idp).val();
detalle["guia5"]=$('#fpago_mpago').val();
});
DATA.push(detalle);
}	
num=num+1;		
});
	
var hash = JSON.stringify(DATA);	
formData.append('otros', hash);


var DATA2 = [];
var numdoc='0';
$("#tblpagof tr").each(function(index4){

if(numdoc!='0'){
var detalle2 = {};
    $(this).children("td").each(function(index3){

        switch(index3){
		case 0:	detalle2["tipopago"]=$(this).text();
		case 1:	detalle2["fechadoc"]=$(this).text();
		case 2:	detalle2["fechavence"]=$(this).text();
		case 3:	detalle2["moneda"]=$(this).text();
		case 4:	detalle2["monto"]=_moneyRaw($(this).text());
		case 5:	detalle2["tcambio"]=_moneyNum($(this).text()).toString();		
		break;
    }

});
DATA2.push(detalle2);
}	
numdoc=numdoc+1;		
});

if(DATA2.length>0){
	var totalFacturaCuotas = parseFloat(_moneyFix2($('#txtTOTAL').val()));
	var sumaCuotas = 0;
	for(var ii=0; ii<DATA2.length; ii++){
		DATA2[ii].monto = parseFloat(_moneyFix2(DATA2[ii].monto));
		sumaCuotas += DATA2[ii].monto;
	}
	sumaCuotas = parseFloat(sumaCuotas.toFixed(2));
	var diffCuotas = parseFloat((totalFacturaCuotas - sumaCuotas).toFixed(2));
	if(Math.abs(diffCuotas) > 0){
		var last = DATA2.length - 1;
		DATA2[last].monto = parseFloat((DATA2[last].monto + diffCuotas).toFixed(2));
	}
}

var hashpagos = JSON.stringify(DATA2);	

var idguia=$('#idguia').val();

var mach_id=$('#mach_id').val();
var mach_numero=$('#mach_numero').val();
var mach_monto=$('#mach_monto').val();
var mach_fecha=$('#mach_fecha').val();
var mach_observaciones=$('#mach_observaciones').val();

formData.append('idguia', idguia);
formData.append('pagoscredito', hashpagos);

formData.append('mach_id', mach_id);
formData.append('mach_numero', mach_numero);
formData.append('mach_monto', mach_monto);
formData.append('mach_fecha', mach_fecha);
formData.append('mach_observaciones', mach_observaciones);



$.ajax({
		url: "modelos/graba-venta.php",
	    type: "POST",
	    data: formData,
	    contentType: false,
	    processData: false,
	    success: function(datos){  
			
			console.log(datos);
Swal.fire(datos.mensaje);
			
$("#idventa").val(datos.idventa);
$("#txtNUMERO").val(datos.numero);			
$('#txtID_TIPO_DOCUMENTO').prop('disabled', 'disabled');			
$('#txtSERIE').prop('disabled', 'disabled');
$('#txtNUMERO').val(datos.numero);
			
if(datos.estado=='1'){	
$('.guardaventa').attr('disabled', true);			
}else{
$('.guardaventa').attr('disabled', false);	
}

_aplicarFormatoTotalesVenta();
	
$('#imprimir').attr("disabled", false);
$('#imprimirt').attr("disabled", false);
$('#imprimirb').attr("disabled", false);
$('#imprimirt2').attr("disabled", false);
			
			/*
$('#txtID_CLIENTE').html("<option value='"+datos.id+"' selected='selected'>"+datos.nombre+"</option>").selectpicker('refresh');	
			$('#cliente').modal('toggle');
			*/

	    },error : function(datos) {
console.log(datos);
    }

	});

	
}

function modalltras(nivel, directosunat){

	$('#tipopaso').val(nivel);
	var pago=$('#pago').val();
	//if (pago=='CONTADO') { Swal.fire('SELECCIONE FORMA DE PAGO CREDITO'); return true; }

var idventa=$('#idventa').val();	
console.log('idventa:'+idventa);
	
if(idventa=='0'){
	
$.ajax({
            type: "POST",
            url: "https://facturacion.siscontonline.com/webservices/tipo-cambio.php?op=tipocambio",
            data: '',
	dataType : 'json',
            success: function(data) {
				console.log(data.compra);
$('#tcambio2').val(data.venta);	
				
            }
        });

$('#montopago2').val(_moneyRaw($('#txtTOTAL').val()))
$('#saldo2').val(_moneyRaw($('#txtTOTAL').val()));
$('#monedapago2').val($('#txtMONEDA').val());
$('#fechadoc2').val($('#txtFECHA_DOCUMENTO').val());	
$('#modalpagoadd').modal('show');

}else{
tipopago(idventa, 1, 'CREDITO');	
}

}

var t = $('#tblpagof').DataTable();

function addpagoventa() {
var fechadoc=$('#fechadoc2').val();
var fecha=$('#fechapago2').val();
var monto=_moneyFix2($('#montopago2').val());
var tpago=$('#tpago2').val();
var tcambio=_moneyRaw($('#tcambio2').val());
var moneda=$('#monedapago2').val();
var nivel=$('#nivelpago2').val();
var tipopago=$('#tipopago2').val();	

var operacion=$('#operacion2').val();
if(nivel=='1'){ operacion=$('#operacionp2').val(); }	

var properiodo=$('#properiodo2').val();
var periodo=$('#periodo2').val();
var letras=$('#letras2').val();
var tipopagodet=$('#tipopagodet2').val();
//var dataString = 'idventa='+idventa+': txtFECHA_DOCUMENTO='+fecha+': txtTOTAL='+monto+', txtSUB_TOTAL='+saldo;

t.row.add([
tpago, 
fechadoc,
fecha,
moneda,
monto,
tcambio,
'<button type="button" class="btn btn-danger btn-xs del" ><span class="glyphicon glyphicon-trash"></span></button>'
]).draw(false);	
}

t.on('click', 'button.del', function() {
  let $tr = $(this).closest('tr');
  // Le pedimos al DataTable que borre la fila
  t.row($tr).remove().draw(false);
});


/*CREDITO*/
function contadocredito(){
var pago=$('#pago').val();

if(pago=='CONTADO'){
$('#botonpago').attr('disabled', true);	
}else{
$('#botonpago').attr('disabled', false);		
}	
}

/*IMPORTAR VENTAS*/
function importarventas(){
	$('#precioserv').val('0.00');
	$('#titservicio').val('');		
	$('#importarventas').modal('show');
		
		$.post("data/venta.php?op=listarservicio", function(r){
			$("#listservicio").html(r);
			$('#listservicio').selectpicker('refresh');
		});
	}

	function subeexcel(){

		var tipo=$("#tipoup").val();	
		var idlocalventa=$("#idlocalventa").val();
		var igvoperacion=$("#igvoperacion").val();
		var fechaimport=$("#fechaimport").val();
		var tpago2=$("#tpago2").val();
		var contadocredito=$("#contadocredito").val();

		var link='modelos/upload-ventas2.php?tipo='+tipo;
					
		console.log('tipo:'+tipo);
			
		$('#btnsubir').attr("disabled", true);		
		var file = $("#archivo")[0].files[0];
				var fileName = file.name;
				fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
				var fileSize = file.size;
				var fileType = file.type;		
				if(fileType=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
				//información del formulario
				var formData = new FormData($(".for")[0]);
					formData.append('idlocalventa', idlocalventa);
					formData.append('igvoperacion', igvoperacion);
					formData.append('fechaimport', fechaimport);
					formData.append('tpago2', tpago2);
					formData.append('contadocredito', contadocredito);
					formData.append('archivo3', file);
				var message = ""; 
				//hacemos la petición ajax 
				$.ajax({
					url:link,  
					type: 'POST',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					beforeSend: function(){
		message = $('<h3><span class="success"><i class="fa fa-circle-o-notch fa-spin "></i> Procesando archivo, por favor espere...</span></h3>');
		showMessage(message)      
					},
					//una vez finalizado correctamente
					success: function(data){
						console.log(data);
						
						listar();

		message = $('<h3><span class="success"><i class="fa fa-check-circle"></i> Se proceso correctamente.</span></h3>');
		showMessage(message);
		$('#btnsubir').attr("disabled", false);
		
					},
					//si ha ocurrido un error
					error: function(){
						
		message = $('<h3><span class="error"><i class="fa fa-exclamation-triangle"></i> Ha ocurrido un error.</span></h3>');
		showMessage(message)
					}
				});
		
		}else{
			message = $("<span class='error'>El archivo solo puede ser .xlsx (Excel).</span>");
			showMessage(message);
		}
			
			
		}

var tablastock;

function listarstock(id){

tablastock=$('#tblstock').dataTable(
	{
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          
		            'copyHtml5',
		            'excelHtml5',
		            'csvHtml5',
		            'pdf'
		        ],
		"ajax":
				{
					url: 'data/venta.php?op=listarstock&id='+id,
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);

					}
				},
		"bDestroy": true,
		"iDisplayLength": 5,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
}).DataTable();


}

function abrirstock(id, titulo) {
listarstock(id);
$("#titulostock").html(titulo);
$('#modalstock').modal('show');
}



