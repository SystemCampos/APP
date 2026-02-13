
function ListCliente(tipodoc){
	
	console.log(tipodoc);
	/*
	$.post("data/venta.php?op=selectCliente&tipodoc="+tipodoc, function(r){
	            $("#txtID_CLIENTE").html(r);
	            $('#txtID_CLIENTE').selectpicker('refresh');
		$('#addcliente').attr("disabled", false);
	});	
*/
	
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

	// =========================================================
	// Al seleccionar Cliente Comprobante, autoseleccionar
	// F.Pago/M.Pago (#fpago_mpago) usando:
	// persona.venta_pago -> caja_tipopago_persona.id
	// (Esto dispara el autocompletado existente de Forma/pago y Med/pago)
	// =========================================================
	$(document)
		.off('select2:select.autoguia5')
		.on('select2:select.autoguia5', '#txtID_CLIENTE', function(e){
			try{
				var data = e && e.params ? e.params.data : null;
				var idcliente = data && data.id ? data.id : $(this).val();
				if(!idcliente){ return; }

				$.getJSON('data/venta.php', { op: 'cliente_venta_pago', idcliente: idcliente })
					.done(function(resp){
						if(!resp || resp.ok !== true){
							// Si no hay configuración, no forzamos nada
							return;
						}
						if(!resp.id_tipopago_persona){ return; }

						var $fpagoMpago = $('#fpago_mpago');
						if(!$fpagoMpago.length){ return; }

						var idtpp = String(resp.id_tipopago_persona);
							_ventaPagoClienteRegistrado = idtpp;
						var txt  = resp.descripcion ? String(resp.descripcion) : idtpp;

						// Inyectar opción y seleccionar (Select2 AJAX necesita esto)
						var opt = new Option(txt, idtpp, true, true);
						$fpagoMpago.append(opt).trigger('change');
					})
					.fail(function(xhr){
						console.error('cliente_venta_pago error', xhr);
					});
			}catch(err){
				console.error(err);
			}
		});
	
	
}

// Estado para detectar cambio manual de F.Pago/M.Pago vs configuración del cliente
var _ventaPagoClienteRegistrado = '';

function _nfp(v){
	var n = parseFloat(String(v == null ? '' : v).replace(/,/g,'.'));
	return isNaN(n) ? 0 : n;
}

function _fmt2fp(v){
	return (Math.round(_nfp(v) * 100) / 100).toFixed(2);
}

function _sumarDiasFp(fechaYmd, dias){
	try{
		var p = String(fechaYmd || '').split('-');
		if(p.length !== 3){ return fechaYmd; }
		var d = new Date(parseInt(p[0],10), parseInt(p[1],10)-1, parseInt(p[2],10));
		d.setDate(d.getDate() + parseInt(dias,10));
		var y = d.getFullYear();
		var m = ('0' + (d.getMonth()+1)).slice(-2);
		var da = ('0' + d.getDate()).slice(-2);
		return y + '-' + m + '-' + da;
	}catch(e){
		return fechaYmd;
	}
}

function _recalcularCuotasFpago(resp){
	try{
		if(!resp){ return; }
		var pf = String(resp.pagoforma || '').toUpperCase();
		if(pf !== 'CREDITO'){ return; }

		var cuotas = parseInt(resp.cuotas, 10);
		if(isNaN(cuotas) || cuotas <= 0){ cuotas = 1; }
		var dias = parseInt(resp.dias, 10);
		if(isNaN(dias) || dias <= 0){ dias = 30; }
		var diasCuota = parseInt(dias, 10);
		if(isNaN(diasCuota) || diasCuota <= 0){ diasCuota = 30; }

		// Guardar configuración para modal de cuotas
		$('#properiodo2').val(cuotas > 1 ? 'SI' : 'NO');
		$('#letras2').val(String(cuotas));
		$('#periodo2').val(String(diasCuota));

		var total = _nfp($('#txtTOTAL').val());
		if(total <= 0){ return; }
		var fechaDoc = String($('#txtFECHA_DOCUMENTO').val() || '');
		if(!fechaDoc){ return; }

		if(typeof t === 'undefined' || !t || !t.clear || !t.row || !t.row.add){ return; }

		var tpago = String($('#medio option:selected').text() || $('#medio').val() || '');
		var moneda = String($('#txtMONEDA').val() || 'PEN');
		var tcambio = _fmt2fp($('#costodolar').val());
		if(moneda === 'PEN'){ tcambio = '1.00'; }

		t.clear();
		var base = Math.round((total / cuotas) * 100) / 100;
		var acum = 0;
		for(var i=1;i<=cuotas;i++){
			var monto = base;
			if(i === cuotas){ monto = Math.round((total - acum) * 100) / 100; }
			acum = Math.round((acum + monto) * 100) / 100;
			var venc = _sumarDiasFp(fechaDoc, diasCuota * i);
			t.row.add([tpago, fechaDoc, venc, moneda, _fmt2fp(monto), tcambio, '<button type="button" class="btn btn-danger btn-xs del" ><span class="glyphicon glyphicon-trash"></span></button>']);
		}
		t.draw(false);
		$('#montopago2').val(_fmt2fp(total));
		$('#saldo2').val('0.00');
	}catch(e){
		console.error(e);
	}
}

function _obtenerVentaPagoClienteActual(cb){
	try{
		var idcliente = $('#txtID_CLIENTE').val();
		if(!idcliente){ cb(null); return; }
		$.getJSON('data/venta.php', { op: 'cliente_venta_pago', idcliente: idcliente })
			.done(function(resp){
				if(resp && resp.ok === true && resp.id_tipopago_persona){
					cb(String(resp.id_tipopago_persona));
				}else{
					cb(null);
				}
			})
			.fail(function(){ cb(null); });
	}catch(e){ cb(null); }
}


function ListClientepaciente(tipodoc){
	// =========================================================
	// RECONSTRUIDO: guia5 = F.Pago/M.Pago (NO CLIENTES)
	// - Usa caja_tipopago_persona.descripcion por idempresa (cookie id)
	// - Evita que otro init (clientes) se quede pegado: destroy + init
	// =========================================================

	try { $('#fpago_mpago').prop("disabled", false); } catch(e){}

	try{
		if ($.fn.select2 && $("#fpago_mpago").data('select2')) {
			$("#fpago_mpago").select2('destroy');
		}
	}catch(e){}

	$("#fpago_mpago").select2({
		placeholder: "-SELECCIONE F.PAGO/M.PAGO-",
		width: '100%',
		dropdownParent: $('body'),
		ajax: {
			url: "data/venta.php?op=select_tipopago_persona",
			type: "get",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					searchTerm: params.term || ''
				};
			},
			processResults: function (response) {
				// response esperado: [{id:..., text:...}, ...]
				return { results: response };
			},
			cache: true
		}
	});

	// =========================================================
	// Al seleccionar F.Pago/M.Pago, autocompleta:
	// - Med/pago (#medio)  : caja_tipopago.id (via tpp.id_pago)
	// - Forma/pago (#pago) : caja_tipopago.pagoforma (CONTADO/CREDITO)
	// =========================================================
	$(document).off('change.guia5_pago_auto').on('change.guia5_pago_auto', '#fpago_mpago', function(){
		var idtpp = $(this).val();
		if(!idtpp){ return; }

		$.getJSON('data/venta.php', { op: 'map_tipopago_persona', id: idtpp })
			.done(function(resp){
				if(!resp || resp.ok !== true){
					console.warn('map_tipopago_persona sin ok', resp);
					return;
				}

				// 1) Med/pago (#medio) => id_pago (caja_tipopago.id)
				try{
					if(typeof resp.id_pago !== 'undefined' && resp.id_pago !== null && String(resp.id_pago) !== ''){
						var $medio = $('#medio');
						var idPago = String(resp.id_pago);
						var txtPago = resp.forma_descripcion ? String(resp.forma_descripcion) : idPago;
						// Si es Select2 AJAX, hay que inyectar la opción
						if($medio.length){
							var opt = new Option(txtPago, idPago, true, true);
							$medio.append(opt).trigger('change');
						}
					}
				}catch(e){ console.error(e); }

				// 2) Forma/pago (#pago) => pagoforma (CONTADO/CREDITO)
				try{
					if(resp.pagoforma){
						var pf = String(resp.pagoforma).toUpperCase();
						if(pf === 'CONTADO' || pf === 'CREDITO'){
							$('#pago').val(pf).trigger('change');
						}
					}
				}catch(e){ console.error(e); }

					// 3) Si cambió respecto al configurado en cliente, recalcular cuotas y advertir.
					_obtenerVentaPagoClienteActual(function(idCfgCliente){
						var idClienteCfg = idCfgCliente || _ventaPagoClienteRegistrado || '';
						var idSel = String(idtpp || '');
						if(!idClienteCfg || !idSel || idClienteCfg === idSel){ return; }
						_recalcularCuotasFpago(resp);
						var msg = 'Se generarán cuotas con la nueva Forma de Pago y Medio de Pago seleccionados.';
						try{
							if(typeof Swal !== 'undefined' && Swal.fire){
								Swal.fire({icon:'warning', title:'Advertencia', text: msg});
							}else{ alert(msg); }
						}catch(e2){ alert(msg); }
					});
			})
			.fail(function(xhr){
				console.error('Error map_tipopago_persona', xhr);
			});
	});


}

//Función cancelarform
function cancelarform(){
	limpiar();
	mostrarform(false);
}

$(document).ready(function() {
    load_map();

	// === Reglas UI del modal pagos (pagof) ===
	$(document).on('change', '#tipopago', function(){
		try{ _toggleDetRetPorTipo(); }catch(e){}
		try{ _syncMontoConSaldo(); }catch(e){}
	});
	$(document).on('change', '#properiodo', function(){
		try{ mostrarpago(); }catch(e){}
	});
});
  	
var map;
var infoWindow = null;
var zoom= parseInt('4');

function openInfoWindow(marker) {
var markerLatLng = marker.getPosition();
$('#latitud').val(markerLatLng.lat());
$('#longitud').val(markerLatLng.lng());
}
 
function load_map() {
    var myLatlng = new google.maps.LatLng('-12.0264987', '-77.2679746');
    var myOptions = {
        zoom: zoom,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map($("#map_canvas").get(0), myOptions);
	
infoWindow = new google.maps.InfoWindow();
 
    var marker = new google.maps.Marker({
        position: myLatlng,
        draggable: true,
        map: map,
        title:"Ejemplo marcador arrastrable"
    });
    google.maps.event.addListener(marker, 'dragend', function(){ openInfoWindow(marker); });
	google.maps.event.addListener(marker, 'click', function(){ openInfoWindow(marker); }); 

}
 
function buscaru() {
    var address = $('#direccion').val();
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'address': address}, geocodeResult);
};
 
function geocodeResult(results, status) {
    // Verificamos el estatus
    if (status == 'OK') {
        // Si hay resultados encontrados, centramos y repintamos el mapa
        // esto para eliminar cualquier pin antes puesto
        var mapOptions = {
            center: results[0].geometry.location,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map($("#map_canvas").get(0), mapOptions);
infoWindow = new google.maps.InfoWindow();
        map.fitBounds(results[0].geometry.viewport);
        // Dibujamos un marcador con la ubicación del primer resultado obtenido
        var markerOptions = { position: results[0].geometry.location, draggable: true, }
        var marker = new google.maps.Marker(markerOptions);
        marker.setMap(map);

    google.maps.event.addListener(marker, 'dragend', function(){ openInfoWindow(marker); });
	google.maps.event.addListener(marker, 'click', function(){ openInfoWindow(marker); });
		
		$('#latitud').val(results[0].geometry.location.lat());
		$('#longitud').val(results[0].geometry.location.lng());
		
    } else {
        alert("Geocoding no tuvo éxito debido a: " + status);
    }
}

function buscarcontribuyente(){

var tipo=$("#tipo_documento").val();
var numdoc=$("#numerodocumento").val().length;

	if (tipo=='RUC'){
		if(numdoc!=11){
			Swal.fire("REVISAR TIPO DE DOCUMENTO - # DE RUC DEBE SER 11 CARACTERES");
			return false;
		}
	}

	if (tipo=='DNI'){
		if(numdoc!=8){
			Swal.fire("REVISAR TIPO DE DOCUMENTO - # DE DNI DEBE SER 8 CARACTERES");
			return false;
		}
	}
	
	
var formData = new FormData();
formData.append("numero", $("#numerodocumento").val());
formData.append("tipo", tipo);
	
if(tipo=='DNI'){

console.log('dnif');
	
	$.ajax({
		url: "https://sunat.solutions.net.pe/consultas/",
	    type: "POST",
	    data: formData,
	    contentType: false,
	    processData: false,
success: function(data){
	
console.log(data);

$("#nombre").val(data.apellidopaterno+' '+data.apellidomaterno+' '+data.nombres); 
$("#Tipo").val(data.necimiento);
$("#sexo").val(data.sexo);

	    },error : function(data) {
console.log(data);
    }
	});
	
}else{
console.log(formData);	
	
	$.ajax({
		url: "https://sunat.solutions.net.pe/consultas/padron.php",
	    type: "POST",
	    data: formData,
	    contentType: false,
	    processData: false,
success: function(data){
	
console.log(data);
	
$("#numerodocumento").val(data.ruc);
					   $("#nombre").val(data.razonSocial);
					   $("#telefono").val(data.telefono);
					   $("#txtRAZON_SOCIAL").val(data.nombreComercial);					  
					  $("#Tipo").val(data.tipo);
					  $("#Estado").val(data.estado);
					  $("#direccion").val(data.direccion);				 
					  $("#ActividadExterior").val(data.actExterior);
					  $("#Oficio").val(data.profesion);
$("#distrito").val(data.distrito);
$("#provincia").val(data.provincia);
$("#departamento").val(data.departamento);
$("#ubigeo").val(data.ubigeo);
	
	    }
	});
	
}	
	
}

//Función limpiar
function limpiarpersona(){
	$("#nombre").val(""); 
	$("#txtID_CLIENTE").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#txtRAZON_SOCIAL").val("");
	$("#idpersona").val("");
	$("#descuento").val("0");
	$("#edad").val("");
}
 
function guardarcli(){

var dni=$("#numerodocumento").val();  		
var formData = new FormData();

	var tipo=$("#tipo_documento").val();
	var numdoc=$("#numerodocumento").val().length;
var tipo_persona=$("#tipo_persona").val();

	if (tipo=='RUC'){
		if(numdoc!=11){
			Swal.fire("REVISAR TIPO DE DOCUMENTO - # DE RUC DEBE SER 11 CARACTERES");
			return false;
		}
	}

	if (tipo=='DNI'){
		if(numdoc!=8){
			Swal.fire("REVISAR TIPO DE DOCUMENTO - # DE DNI DEBE SER 8 CARACTERES");
			return false;
		}
	}

formData.append("dni", dni);
formData.append("tipo_persona", tipo_persona);

console.log(dni);

        $.ajax({
            type: "POST",
            url: "data/persona.php?op=verificar",
            data: formData,
			processData: false,
    contentType: false,
			dataType: 'json',
            success: function(data) {
				console.log(data);	
				if(data.estado==0){
swal.close();
Swal.fire(data.mensaje);	
				}else{
					guardaclifinal();			
				}

            },
        error: function(data){
 console.log(data);
        }

        });
}

function guardaclifinal(){

//$('#txtID_CLIENTE').val('').selectpicker('refresh');
	
var tipodoccli=$("#tipo_persona").val();
	
var formData = new FormData();
formData.append("numerodocumento", $("#numerodocumento").val());
formData.append("tipo_documento", $("#tipo_documento").val());
formData.append("tipo_persona", $("#tipo_persona").val());
formData.append("nombre", $("#nombre").val());
formData.append("direccion", $("#direccion").val());
formData.append("telefono", $("#telefono").val());
formData.append("email", $("#email").val());
formData.append("email2", $("#email2").val());
formData.append("txtRAZON_SOCIAL", $("#txtRAZON_SOCIAL").val());
formData.append("descuento", $("#descuento").val());

formData.append("descuentomayor", $("#descuentomayor").val());
formData.append("sector", $("#sector").val());
formData.append("lat", $("#lat").val());
formData.append("lon", $("#lon").val());
formData.append("codigo", $("#codigo").val());
formData.append("pais", $("#pais").val());
formData.append("ciudad", $("#cuidad").val());
	formData.append("edad", $("#edad").val());
	
console.log('numero:'+$("#numerodocumento").val());

	$.ajax({
		url: "data/persona.php?op=guardaryeditar",
	    type: "POST",
	    data: formData,
	    contentType: false,
	    processData: false,
	    success: function(datos){  
			console.log(datos);
	        //Swal.fire(datos);
$("#modalcliente").modal('hide');//ocultamos el modal venta rapida
$("#cliente").modal('hide');//ocultamos el modal
$('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
$('.modal-backdrop').remove();//eliminamos el backdrop del modal
			
swal.close();
Swal.fire(datos.mensaje);

if(tipodoccli=='Cliente'){

console.log('tipodoccli:'+tipodoccli);	
/*	
var newOption = new Option(datos.nombre, datos.id, false, false);
$('#txtID_CLIENTE').append(newOption).trigger('change');
	
$('#txtID_CLIENTE').html("<option value='"+datos.id+"' selected='selected'>"+datos.nombre+"</option>").selectpicker('refresh');	
*/	

console.log('datos.id2:'+datos.id);
/*	
var newOption = new Option(datos.nombre, datos.id, false, false);
$('#txtID_CLIENTE').append(newOption).trigger('change');
*/
$("#txtID_CLIENTE").append("<option value='"+datos.id+"' selected>"+datos.nombre+"</option>");
$('#txtID_CLIENTE').trigger('change');


//$("#select2").select2('data', {id: newID, text: newText}); 
	
}else{		
$('#idproveedor').html("<option value='"+datos.id+"' selected='selected'>"+datos.nombre+"</option>").selectpicker('refresh');
$('#cliente').modal('toggle');

$("#cliente").modal('hide');//ocultamos el modal
$('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
$('.modal-backdrop').remove();

console.log('cieera ventana');


}
	    },
        error: function(data){
 console.log(data);
        }

	});


	 

}

function serie_numero(){

var TipoComprobate=$('#tipo_comprobante').val();
var seriedoc;

TipoComprobate=TipoComprobate.toString();

if(TipoComprobate=='10'){seriedoc='SP';
}else if(TipoComprobate=='01'){seriedoc='VN';
}else if(TipoComprobate=='02'){seriedoc='CN';
}else if(TipoComprobate=='03'){seriedoc='CR';
}else if(TipoComprobate=='04'){seriedoc='CE';
}else if(TipoComprobate=='05'){seriedoc='DR';
}else if(TipoComprobate=='06'){seriedoc='DE';
}else if(TipoComprobate=='07'){seriedoc='BN';
}else if(TipoComprobate=='08'){seriedoc='PR';
}else if(TipoComprobate=='09'){seriedoc='DN';

}else if(TipoComprobate=='11'){seriedoc='TA';
}else if(TipoComprobate=='12'){seriedoc='RT';
}else if(TipoComprobate=='13'){seriedoc='ME';
}else if(TipoComprobate=='14'){seriedoc='DM';
}else if(TipoComprobate=='15'){seriedoc='DT';
}else if(TipoComprobate=='16'){seriedoc='SI';
}else if(TipoComprobate=='17'){seriedoc='EX';
}else if(TipoComprobate=='18'){seriedoc='IM';
}else if(TipoComprobate=='19'){seriedoc='EP';
}else if(TipoComprobate=='20'){seriedoc='DP';
}else if(TipoComprobate=='21'){seriedoc='EA';
}else if(TipoComprobate=='22'){seriedoc='EE';
}else if(TipoComprobate=='23'){seriedoc='SE';
}else if(TipoComprobate=='24'){seriedoc='DC';
}else if(TipoComprobate=='25'){seriedoc='DP';
}else if(TipoComprobate=='26'){seriedoc='ES';
}else if(TipoComprobate=='27'){seriedoc='SS';
}else if(TipoComprobate=='28'){seriedoc='AD';
}else if(TipoComprobate=='29'){seriedoc='EB';
}else if(TipoComprobate=='30'){seriedoc='SB';
}else if(TipoComprobate=='31'){seriedoc='EC';
}else if(TipoComprobate=='32'){seriedoc='SC';
}else if(TipoComprobate=='33'){seriedoc='MM';
}else if(TipoComprobate=='34'){seriedoc='PB';
}else if(TipoComprobate=='35'){seriedoc='GR';
}else if(TipoComprobate=='36'){seriedoc='RE';
}else if(TipoComprobate=='37'){seriedoc='RC';
}else if(TipoComprobate=='38'){seriedoc='RS';

}else if(TipoComprobate=='91'){seriedoc='O1';
}else if(TipoComprobate=='92'){seriedoc='O2';

}else if(TipoComprobate=='98'){seriedoc='MU';
}else if(TipoComprobate=='99'){seriedoc='OT';

}else if(TipoComprobate=='210'){seriedoc='I';
}

	

$.ajax({
url: "data/series.php?op=generarserie&tipo="+TipoComprobate+'&seriedoc='+seriedoc,
type: "get",
dataType: 'json',
data: {"op": "generarserie", "tipo":TipoComprobate, "seriedoc":seriedoc},
success: function (response) {
	
	$('#numero').val(response.numero);
	$('#serie').val(response.serie);	
},
                        error: function (data) {
                            console.log(data);
                            alert('Error Al conectar la Base Datos');
                            //console.log(data);
                        }
                    });

        }

function serieingreso(){

var idingreso=$("#idingreso").val();

if(idingreso==''){
var TipoComprobate=$('#tipo_comprobante').val();
var cat=$('#cat').val();
var seriedoc;
var generarserie='0';

console.log('TIPODOC:'+TipoComprobate);
		
if(cat=='1'||cat=='0'){
	
if(TipoComprobate=='10'){
seriedoc='SP';
generarserie='1';
}else if(TipoComprobate=='11'){
seriedoc='TA';
generarserie='1';
}else if(TipoComprobate=='12'){
seriedoc='RT';
generarserie='1';
}else if(TipoComprobate=='04'){
seriedoc='CE';
generarserie='1';
}else if(TipoComprobate=='06'){
seriedoc='DE';
generarserie='1';
}
	
}else{
	
if(TipoComprobate=='200'){
seriedoc='OC';
TipoComprobate='200';
generarserie='1';
}else if(TipoComprobate=='203'){
	seriedoc='OS';
	TipoComprobate='203';
	generarserie='1';
}else{
seriedoc='PED';
TipoComprobate='201';
generarserie='1';	
}

}

console.log('seriedoc:'+seriedoc);

if(generarserie=='1'){
	
$.ajax({
url: "data/series.php?op=generarserieingreso&tipo="+TipoComprobate+'&seriedoc='+seriedoc,
type: "get",
dataType: 'json',
data: {"op": "generarserieingreso", "tipo":TipoComprobate, "seriedoc":seriedoc}, 
success: function (response) {
console.log(response);
	$('#num_comprobante').val(response.numero);
	$('#serie_comprobante').val(response.serie);
	$('#serienumero').val(response.serie+'-'+response.numero);


},
                        error: function (data) {
                            console.log(data);
                            alert('Error Al conectar la Base Datos');
                            //console.log(data);
                        }
                    });
}
	
}

}

/*CAJA Y BANCOS*/

// ======================================================
// PAGOS (Modal #pagof) - Validaciones + Recalculo montopago
// Reglas:
// - Validar antes de grabar (1 solo mensaje, no graba si falla)
// - Ocultar operacion si properiodo == "SI"
// - Recalcular montopago al cambiar: letras, tipopago, properiodo
// - NORMAL => detraccionf/retencionf = 0
// - DETRACCION/RETENCION => requiere que la venta tenga monto configurado
// - Considera pagos ya guardados (consulta versaldo antes de grabar)
// ======================================================

function _pagoMsg(msg){
	try{
		if(typeof Swal !== 'undefined' && Swal.fire){
			Swal.fire({icon:'warning', title:'Validación', text: msg});
		}else{
			alert(msg);
		}
	}catch(e){
		alert(msg);
	}
}

function _getModoTipopago(){
	// Detecta NORMAL / DETRACCION / RETENCION usando value o texto
	var v = String($('#tipopago').val() || '').toUpperCase();
	var t = String($('#tipopago option:selected').text() || '').toUpperCase();
	var s = (v + ' ' + t);
	if(s.indexOf('DETR') !== -1) return 'DETRACCION';
	if(s.indexOf('RETEN') !== -1) return 'RETENCION';
	return 'NORMAL';
}

function _toggleOperacionPorPeriodo(){
	var properiodo = String($('#properiodo').val() || 'NO');
	if(properiodo === 'SI'){
		$('.periodos').show();
		try{ $('#operacion').closest('div.col-lg-2,div.col-md-2,div.col-sm-6,div.col-xs-12').hide(); }catch(e){}
	}else{
		$('.periodos').hide();
		try{ $('#operacion').closest('div.col-lg-2,div.col-md-2,div.col-sm-6,div.col-xs-12').show(); }catch(e){}
	}
	// montopago y saldo siempre visibles
	try{ $('#montopago').closest('div.col-lg-2,div.col-md-2,div.col-sm-6,div.col-xs-12').show(); }catch(e){}
	try{ $('#saldo').closest('div.col-lg-2,div.col-md-2,div.col-sm-6,div.col-xs-12').show(); }catch(e){}
}


function _applyLabelMonto(){
	var properiodo = String($('#properiodo').val() || '');
	var modo = _getModoTipopago();
	var base = 'Monto a pagar:';
	if(properiodo === 'SI'){
		if(modo === 'DETRACCION') base = 'Monto detracción x cuota:';
		else if(modo === 'RETENCION') base = 'Monto retención x cuota:';
		else base = 'Monto pagar x cuota:';
	}else{
		if(modo === 'DETRACCION') base = 'Monto detracción a pagar:';
		else if(modo === 'RETENCION') base = 'Monto retención a pagar:';
		else base = 'Monto a pagar:';
	}
	// intenta actualizar label del input montopago
	var $lbl = $('label[for="montopago"]');
	if($lbl.length){ $lbl.text(base); }
	// si tu label tiene otro id/clase, no rompe nada
}

function _num(v){
	var n = parseFloat(String(v).replace(/,/g,'.'));
	return isNaN(n) ? 0 : n;
}

function _fetchSaldoVenta(cb){
	var idventa = $('#idventapago').val();
	var nivel = $('#nivelpago').val();
	$.ajax({
		type: "POST",
		url: "data/venta.php?op=versaldo",
		data: {idventa:idventa, nivel:nivel},
		dataType: 'json'
	}).done(function(resp){
		cb(null, resp || {});
	}).fail(function(xhr){
		cb(new Error('No se pudo obtener saldo'), null);
	});
}

function _updateCamposDesdeSaldo(resp){
	// saldo normal
	if(resp && typeof resp.saldo !== 'undefined'){
		$('#saldo').val(resp.saldo);
						try{ _syncMontoConSaldo(); }catch(e){}
}
	// moneda, tc, tpago (si vienen)
	if(resp && resp.MOENDA){
		$("#monedapago").val(resp.MOENDA);
	}
	if(resp && resp.tcambio && _num(resp.tcambio) > 0){
		$('#tcambio').val(resp.tcambio);
	}
	if(resp && resp.tipo_pago){
		var cur = String($('#tpago').val()||'').trim();
		if(cur===''){ $('#tpago').val(resp.tipo_pago); }
	}
	// detraccion/retencion del documento
	if(resp && typeof resp.detraccion !== 'undefined') $('#detraccionf').val(resp.detraccion);
	if(resp && typeof resp.retencion !== 'undefined') $('#retencionf').val(resp.retencion);
}

function _calcularMontoSugerido(resp){
	var properiodo = String($('#properiodo').val() || 'NO');
	var letras = parseInt($('#letras').val(), 10);
	if(isNaN(letras) || letras < 1) letras = 0;

	var modo = _getModoTipopago();

	var pend_normal = _num(resp.pendiente);
	var pend_det = _num(resp.det_pendiente);
	var pend_ret = _num(resp.ret_pendiente);

	var basePend = pend_normal;
	var regCount = _num(resp.cuotas_reg || 0);

	if(modo === 'DETRACCION'){
		basePend = pend_det;
		regCount = _num(resp.det_reg || 0);
	}else if(modo === 'RETENCION'){
		basePend = pend_ret;
		regCount = _num(resp.ret_reg || 0);
	}else{
		regCount = _num(resp.cuotas_reg || 0);
	}

	// Si DET/RET y no hay monto configurado en la venta
	if((modo === 'DETRACCION' && _num(resp.detraccion) <= 0) || (modo === 'RETENCION' && _num(resp.retencion) <= 0)){
		return {ok:false, msg:'La venta no tiene detracción/retención configurada. Modifica la venta para incluir estos valores.'};
	}

	if(properiodo === 'SI'){
		if(letras <= 0){
			return {ok:false, msg:'Debe indicar la cantidad de letras.'};
		}
		var restantes = letras - regCount;
		if(restantes <= 0){
			return {ok:false, msg:'Ya no quedan cuotas pendientes para este concepto. Elimina cuotas existentes si deseas modificar el número de cuotas.'};
		}
		var porCuota = basePend / restantes;
		porCuota = Math.round(porCuota * 100) / 100;
		return {ok:true, monto: porCuota, pendiente: basePend, restantes: restantes};
	}

	// Manual: sugerir el pendiente total
	var m = Math.round(basePend * 100) / 100;
	return {ok:true, monto: m, pendiente: basePend, restantes: null};
}

function _recalcularUI(){
	_applyLabelMonto();
	_toggleOperacionPorPeriodo();
	_fetchSaldoVenta(function(err, resp){
		if(err){ return; }
		_updateCamposDesdeSaldo(resp);

		// Normal: set detr/ret en 0
		var modo = _getModoTipopago();
		if(modo === 'NORMAL'){
			$('#detraccionf').val('0');
			$('#retencionf').val('0');
		}

		var r = _calcularMontoSugerido(resp);
		if(r.ok){
			$('#montopago').val((r.monto).toFixed(2));
			// mensaje de pendiente por agregar si es menor que pendiente y no es por periodo
			// (no muestra alerta aquí, solo prepara)
		}else{
			// no bloqueamos solo por recalculo; se bloqueará al grabar
		}
	});
}

/*CAJA Y BANCOS*/
function guardapago() {

	var idventa=$('#idventapago').val();
	var fecha=$('#fechapago').val();
	var monto=_num($('#montopago').val());
	var tpago=$('#tpago').val();
	var tcambio=$('#tcambio').val();
	var moneda=$('#monedapago').val();
	var nivel=$('#nivelpago').val();
	var tipopago_sel=$('#tipopago').val();

	var operacion=$('#operacion').val();
	if(nivel=='1'){ operacion=$('#operacionp').val(); }

	var properiodo=$('#properiodo').val();
	var periodo=$('#periodo').val();
	var letras=$('#letras').val();
	var tipopagodet=$('#tipopagodet').val();

	// ======================================================
	// Validaciones básicas client-side
	// REQ-1:
	// - Si el monto es 0 y dispara el mensaje "El monto a pagar debe ser mayor a 0.",
	//   entonces al cerrar el mensaje (OK / click fuera / X) se debe recalcular:
	//   montopago = saldo / letras
	// ======================================================
	if(!idventa){ return _pagoMsg('No se encontró la venta para registrar el pago.'); }
	if(!fecha){ return _pagoMsg('Debe indicar la fecha de pago.'); }
	if(!(monto > 0)){
		try{
			var s = _num($('#saldo').val());
			var l = parseInt($('#letras').val(), 10);
			if(isNaN(l) || l <= 0){ l = 1; }
			var nuevo = (l > 0) ? (s / l) : s;
			var _set = function(){ $('#montopago').val((Math.round(nuevo*100)/100).toFixed(2)).focus(); };

			if(typeof Swal !== 'undefined' && Swal.fire){
				Swal.fire({icon:'warning', title:'Validación', text:'El monto a pagar debe ser mayor a 0.'})
					.then(function(){ _set(); });
			}else{
				alert('El monto a pagar debe ser mayor a 0.');
				_set();
			}
		}catch(e){
			_pagoMsg('El monto a pagar debe ser mayor a 0.');
		}
		return;
	}

	// Validar tipo de pago
	if(!tpago){ return _pagoMsg('Debe seleccionar el tipo de pago (t.pago).'); }

	// Antes de grabar: validar con saldo real (considera registros ya guardados)
	_fetchSaldoVenta(function(err, resp){
		if(err){ return _pagoMsg('No se pudo obtener el saldo de la venta.'); }
		_updateCamposDesdeSaldo(resp);

		var modo = _getModoTipopago();

		// Concepto pendiente según modo
		var pendiente = _num(resp.pendiente);
		if(modo === 'DETRACCION') pendiente = _num(resp.det_pendiente);
		if(modo === 'RETENCION') pendiente = _num(resp.ret_pendiente);

		// Validación detr/ret configurado
		if((modo === 'DETRACCION' && _num(resp.detraccion) <= 0) || (modo === 'RETENCION' && _num(resp.retencion) <= 0)){
			return _pagoMsg('La venta no tiene detracción/retención configurada. Modifica la venta para incluir estos valores.');
		}

		// Validación monto <= pendiente
		pendiente = Math.round(pendiente * 100) / 100;
		if(monto - pendiente > 0.009){
			var falt = pendiente.toFixed(2);
			return _pagoMsg('No se puede registrar un monto mayor al pendiente. Pendiente por asignar: ' + falt);
		}

		// Validación por periodo
		if(String(properiodo||'') === 'SI'){
			var nL = parseInt(letras, 10);
			if(isNaN(nL) || nL <= 0){
				return _pagoMsg('Debe indicar la cantidad de letras (cuotas).');
			}
			// Backend verificará cambio de cuotas con cuotas existentes
		}

		// Armar request
		var dataString = {
			idventa:idventa,
			txtFECHA_DOCUMENTO:fecha,
			txtTOTAL:monto.toFixed(2),
			tpago:tpago,
			tcambio:tcambio,
			moneda:moneda,
			operacion:operacion,
			nivel:nivel,
			properiodo:properiodo,
			periodo:periodo,
			letras:letras,
			tipopagodet:tipopagodet,
			tipopago:tipopago_sel
		};

		$.ajax({
			type: "POST",
			url: "data/venta.php?op=guardasaldo",
			data: dataString,
			dataType: 'json'
		}).done(function(r){
			if(r && r.estado === '1'){
				// ok
				if(tablapago && tablapago.ajax){ tablapago.ajax.reload(null, false); }else{ try{ listarcobrar(idventa, nivel); }catch(e){} }
				versaldo(idventa, nivel);
				limpiapago();
				// Mostrar pendiente restante si aplica
				if(typeof r.pendiente_restante !== 'undefined'){
					var pr = _num(r.pendiente_restante);
					if(pr > 0.009){
						_pagoMsg('Pago registrado. Pendiente por agregar: ' + pr.toFixed(2));
					}
				}
			}else{
				_pagoMsg((r && r.mensaje) ? r.mensaje : 'No se pudo registrar el pago.');
			}
		}).fail(function(){
			_pagoMsg('Disculpe, existió un problema al registrar el pago.');
		});
	});
}

function finingreso(id){
	
	$("#pagof").modal('hide');//ocultamos el modal
    $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
    $('.modal-backdrop').remove();//eliminamos el backdrop del modal
	
var idventa=$('#idventapago').val();
var accion=$('#marcaenviado').val();
var tipopaso=$('#tipopaso').val();
var directosunat=$('#directosunat').val();
tabla.ajax.reload();
if(tipopaso=='2'){ guardarventa2(directosunat); }

}

function mostrarpago(){
	// Reglas UI (Modal PAGO VENTA - #pagof)
	// - Si properiodo = SI: periodo/letras visibles, operacion oculto
	// - Si properiodo = NO: periodo/letras ocultos, operacion visible
	// - montopago y saldo SIEMPRE visibles
	var periodo = String($('#properiodo').val() || 'NO');
	if(periodo === 'SI'){
		$('.periodos').show();
		try{ $('#operacion').closest('div.col-lg-2,div.col-md-2,div.col-sm-6,div.col-xs-12').hide(); }catch(e){}
	}else{
		$('.periodos').hide();
		try{ $('#operacion').closest('div.col-lg-2,div.col-md-2,div.col-sm-6,div.col-xs-12').show(); }catch(e){}
	}
	// Siempre visibles
	try{ $('#montopago').closest('div.col-lg-2,div.col-md-2,div.col-sm-6,div.col-xs-12').show(); }catch(e){}
	try{ $('#saldo').closest('div.col-lg-2,div.col-md-2,div.col-sm-6,div.col-xs-12').show(); }catch(e){}
	// aplicar visibilidad detracción/retención según tipopago
	try{ _toggleDetRetPorTipo(); }catch(e){}
	// sincroniza monto = saldo
	try{ _syncMontoConSaldo(); }catch(e){}
}

function tipopago(id, accion, tipo){

$('#montopago').val(_num($('#saldo').val()).toFixed(2));
$('#idventapago').val(id);
$('#marcaenviado').val(accion);	
$('#tipopagodet').val(tipo);	

var nivel=$('#nivelpago').val();

$("#pagof").modal("show");
listarcobrar(id, nivel);
versaldo(id, nivel);
limpiapago();
mostrarpago();
	_toggleOperacionPorPeriodo();
	_applyLabelMonto();
	// Recalcula montopago según saldo y selección
	_recalcularUI();
	
	// Solo consulta TC externo si el documento no trae TC
	if(_num($('#tcambio').val())<=0){
	$.ajax({
            type: "POST",
            url: "https://sunat.solutions.net.pe/webservices/tipo-cambio.php?op=tipocambio",
            data: '',
	dataType : 'json',
            success: function(data) {
				console.log(data.compra);
$('#tcambio').val(data.venta);	
				
            }
        });	
	}	
}

function listarcobrar(idventa, nivel){

	tablapago=$('#tblpago').dataTable({

		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
		responsive: true,
	    buttons: [],

		"ajax":{
					url: 'data/venta.php?op=listarpagos&id='+idventa+'&nivel='+nivel,
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}

				},
		"bDestroy": true,
		"iDisplayLength": 12,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();

}

function versaldo(id, nivel) {
	
console.log('versaldo:'+id);
	
var dataString = {idventa:id, nivel:nivel};
$.ajax({
            type: "POST",
            url: "data/venta.php?op=versaldo",
            data: dataString, 
	dataType : 'json',
            success: function(data) {
				
				console.log(data);
				
$('#saldo').val(data.saldo);
$('#detraccionf').val(data.detraccion);
$('#retencionf').val(data.retencion);			
$("#monedapago option[value='"+data.MOENDA+"']").attr("selected", true);
				
if(data.saldo=='0'){ 
	//$("#btnGuardarc").prop('disabled', true); 
				   }
	
            },error : function(data) {
				console.log(data);
    }
        });	
}

function limpiapago(){
$('#montopago').val(_num($('#saldo').val()).toFixed(2));
$('#operacion').val("");
}

function verdescuento(){
	
var id=$('#txtID_CLIENTE').val();
	
var dataString = {id:id};
$.ajax({
            type: "POST",
            url: "data/venta.php?op=descuento",
            data: dataString,
dataType : 'json',
success: function(data) {
console.log(data);
$('#descuento').val(data.descuento);
$('#descuentom').val(data.descuentom);
	
            }
        });	
}

function ncliente() {
$('#cliente').modal('show');
}

$(document).ready(function(){
		$("#mostrar").on( "click", function() {
			$('#target').show(); //muestro mediante id
			$('.target').show(); //muestro mediante clase
		 });
		$("#ocultar").on( "click", function() {
			$('#target').hide(); //oculto mediante id
			$('.target').hide(); //muestro mediante clase
		});
	});





// Exponer funciones a window (evita colisiones con elementos DOM)
try{ window.tipopago = tipopago; window.abrirPagos = tipopago; window.ListCliente = ListCliente; }catch(e){}


// ======================================================
// BINDINGS MODAL PAGOS (#pagof)
// ======================================================
$(document).ready(function(){
	// Evita que el botón no haga nada

	// Recalcular cuando cambian controles
	$(document).off('change', '#properiodo').on('change', '#properiodo', function(){
		_toggleOperacionPorPeriodo();
		_recalcularUI();
	});
	$(document).off('change', '#letras').on('change', '#letras', function(){
		_recalcularUI();
	});
	$(document).off('change', '#tipopago').on('change', '#tipopago', function(){
		_recalcularUI();
	});

	// Al abrir modal, refrescar datos
	$('#pagof').off('shown.bs.modal').on('shown.bs.modal', function(){
		_toggleOperacionPorPeriodo();
		_recalcularUI();
	});
});


/* ============================================================
 * FIX: Acciones en modal pagof (tblpago)
 * - eliminarpago(idPago, idVenta)
 * - pagado(idPago, idVenta)
 * Estas funciones son llamadas desde onclick en data/venta.php
 * ============================================================ */
if (typeof window.eliminarpago !== 'function') {
  function eliminarpago(idPago, idVenta){
    try{
      if(!idPago){ return; }
      if(!confirm('¿Eliminar este pago?')){ return; }

      $.getJSON('data/venta.php?op=eliminapago&id=' + encodeURIComponent(idPago), function(r){
        if(r && r.estado=='1'){
          // refrescar tabla y saldo real
          if(typeof listarcobrar === 'function'){
            var niv = ($('#nivel').val()!==undefined) ? $('#nivel').val() : 0;
            listarcobrar(idVenta, niv);
          }
          if(typeof versaldo === 'function'){
            var niv2 = ($('#nivel').val()!==undefined) ? $('#nivel').val() : 0;
            versaldo(idVenta, niv2);
          }
          // forzar recalculo letras para evitar bypass
          try{
            var $l = $('#letras');
            if($l.length){
              var v = $l.val();
              // intenta 1->2->1, si no existe 2 dispara change igual
              if(v=='1' && $l.find("option[value='2']").length){
                $l.val('2').trigger('change');
                $l.val('1').trigger('change');
              }else{
                $l.trigger('change');
              }
            }
          }catch(e2){}
          alert(r.saldo || 'Eliminado');
        }else{
          alert((r && (r.saldo||r.mensaje)) ? (r.saldo||r.mensaje) : 'No se pudo eliminar');
        }
      }).fail(function(xhr){
        alert('Error al eliminar pago');
      });
    }catch(e){
      console.error(e);
      alert('Error al eliminar pago');
    }
  }
  window.eliminarpago = eliminarpago;
}

if (typeof window.pagado !== 'function') {
  function pagado(idPago, idVenta){
    try{
      if(!idPago){ return; }
      if(!confirm('¿Marcar como pagado?')){ return; }

      // Backend completa datos faltantes desde caja_ventapago si no se envían
      $.getJSON('data/venta.php?op=marcapagado&id=' + encodeURIComponent(idPago), function(r){
        if(r && r.estado=='1'){
          if(typeof listarcobrar === 'function'){
            var niv = ($('#nivel').val()!==undefined) ? $('#nivel').val() : 0;
            listarcobrar(idVenta, niv);
          }
          if(typeof versaldo === 'function'){
            var niv2 = ($('#nivel').val()!==undefined) ? $('#nivel').val() : 0;
            versaldo(idVenta, niv2);
          }
          try{
            $('#letras').trigger('change');
          }catch(e2){}
          alert(r.saldo || 'Actualizado');
        }else{
          alert((r && (r.saldo||r.mensaje)) ? (r.saldo||r.mensaje) : 'No se pudo marcar pagado');
        }
      }).fail(function(){
        alert('Error al marcar pagado');
      });
    }catch(e){
      console.error(e);
      alert('Error al marcar pagado');
    }
  }
  window.pagado = pagado;
}

