
// ===============================
// PATCH 2026-01-31
// Motivo: Migración combobox a Select2 (robusto en tabs) + búsqueda visible en multi-select
// ===============================
function initSelect2Articulo(){
  if(!$.fn.select2) return;

  var $sels = $('#idmarca, #idcategoria, #linea, #sublinea, #subfamilia, #idlocal, #farmaceutica, #pactivo, #medida, #medida2, #idcatalogo_afectacion');

  $sels.each(function(){
    var $s = $(this);
    // evitar dobles instancias
    if ($s.hasClass('select2-hidden-accessible')) {
      try { $s.select2('destroy'); } catch(e) {}
    }
    var isMultiple = $s.prop('multiple') === true;

    $s.select2({
      width: '100%',
      placeholder: 'Seleccione',
      allowClear: true,
      closeOnSelect: !isMultiple,           // multi: no cerrar al seleccionar
      minimumResultsForSearch: 0,           // siempre mostrar búsqueda
      dropdownAutoWidth: true
    });
  });
}

// Helper para asignar valores a Select2 (single/multiple)
function setSelect2Value(selector, values){
  if(!$.fn.select2) return;
  var $el = $(selector);
  if(!$el.length) return;

  if(values === null || typeof values === 'undefined' || values === '') values = [];
  if(!Array.isArray(values)) values = [values];

  $el.val(values).trigger('change');
}

// ===============================
// PATCH 2026-01-31 (V5)
// Motivo: En cada alta/edición, llevar al TAB #home y hacer focus
//         en txtDESCRIPCION_ARTICULO para agilizar el ingreso.
// ===============================
function focusDescripcionArticulo(){
  try {
    // Forzar TAB #home (Bootstrap tabs)
    var $homeTab = $('a[href="#home"]');
    if($homeTab.length && typeof $homeTab.tab === 'function'){
      $homeTab.tab('show');
    }
  } catch(e) {}

  // Espera mínima para que el tab y layout terminen de renderizar
  setTimeout(function(){
    var $inp = $('#txtDESCRIPCION_ARTICULO');
    if($inp.length){
      $inp.trigger('focus');
      // Seleccionar texto si ya existe (útil en edición)
      try { $inp[0].select(); } catch(e) {}
    }
  }, 180);
}

var tabla;
var tablaf;
var tablasunat;
var paquetes;
var tablarecetas;
var conciliacion;

function initFiltrosListadoArticulo(){
  var $filterBox = $("#tbllistado_articulo_filter");
  if(!$filterBox.length) return;

  if(!$("#toolbar_filtros_articulo").length){
    if(!$("#style_filtros_select2_art").length){
      $("head").append(
        '<style id="style_filtros_select2_art">'+
        '#toolbar_filtros_articulo .select2-container{min-width:180px; max-width:340px; width:auto !important;}'+
        '#toolbar_filtros_articulo .select2-selection__rendered{white-space:nowrap !important; overflow:visible !important; text-overflow:clip !important; line-height:16px !important; padding-top:4px !important; padding-bottom:4px !important;}'+
        '#tbllistado_articulo th{text-align:center !important;}'+
        '#tbllistado_articulo td{padding:5px 7px !important; vertical-align:middle !important; line-height:1.25;}'+
        '#tbllistado_articulo th{vertical-align:middle !important;}'+
        '#tbllistado_articulo{width:100% !important; table-layout:auto;}'+
        '#tbllistado_articulo td, #tbllistado_articulo th{word-break:normal; overflow-wrap:break-word;}'+
        '#wrap_tbllistado_articulo{margin-top:24px;}'+
        '#tbllistado_articulo_filter input[type=search]{background:#d9f2ff !important; border:2px solid #5bc0de !important; color:#006b8f !important; font-weight:700 !important; transition:all .2s ease;}'+
        '#tbllistado_articulo_filter input[type=search]:focus{background:#bfe9ff !important; transform:scale(1.02);}'+
        '</style>'
      );
    }

    var htmlFiltros = ''+
      '<div id="toolbar_filtros_articulo" style="float:left; display:flex; align-items:center; gap:8px; margin-right:12px; flex-wrap:nowrap;">'+
        '<button type="button" id="btn_inicializar_filtros" class="btn btn-xs" style="background:#d9534f;color:#fff;border-color:#d43f3a;">'+
          '<i class="fa fa-bolt"></i> Inicializar'+
        '</button>'+
        '<select id="filtro_grupo" style="min-width:180px;"></select>'+
        '<select id="filtro_marca" style="min-width:180px;"></select>'+
        '<select id="filtro_linea" style="min-width:180px;"></select>'+
        '<label style="margin:0 0 0 6px; display:inline-flex; align-items:center; gap:5px; font-weight:600; white-space:nowrap;">'+
          '<input type="checkbox" id="filtro_stock_check"> CON STOCK'+
        '</label>'+
      '</div>';

    $filterBox.prepend(htmlFiltros);
  }

  $.post("data/articulo.php?op=filtro_grupo", function(r){
    var current = $("#filtro_grupo").val() || "0";
    $("#filtro_grupo").html(r);
    if($.fn.select2){
      $("#filtro_grupo").select2({ width:'resolve', placeholder:'TODOS LOS GRUPOS', allowClear:false, dropdownAutoWidth:true });
    }
    $("#filtro_grupo").val(current).trigger("change.select2");
  });

  $.post("data/articulo.php?op=filtro_marca", function(r){
    var current = $("#filtro_marca").val() || "0";
    $("#filtro_marca").html(r);
    if($.fn.select2){
      $("#filtro_marca").select2({ width:'resolve', placeholder:'TODAS LAS MARCAS', allowClear:false, dropdownAutoWidth:true });
    }
    $("#filtro_marca").val(current).trigger("change.select2");
  });

  $.post("data/articulo.php?op=filtro_linea", function(r){
    var current = $("#filtro_linea").val() || "0";
    $("#filtro_linea").html(r);
    if($.fn.select2){
      $("#filtro_linea").select2({ width:'resolve', placeholder:'TODAS LAS LÍNEAS', allowClear:false, dropdownAutoWidth:true });
    }
    $("#filtro_linea").val(current).trigger("change.select2");
  });

  $(document).off('change.filtros_articulo');
  $(document).on('change.filtros_articulo', '#filtro_grupo, #filtro_marca, #filtro_linea, #filtro_stock_check', function(){
    if (tabla) tabla.ajax.reload(null, true);
  });

  $(document).off('click.init_filtros').on('click.init_filtros', '#btn_inicializar_filtros', function(){
    $("#filtro_grupo").val("0").trigger("change");
    $("#filtro_marca").val("0").trigger("change");
    $("#filtro_linea").val("0").trigger("change");
    $("#filtro_stock_check").prop("checked", false).trigger("change");
  });
}

function init(){
  listar();
  initFiltrosListadoArticulo();
  mostrarform(false);
  $("#formulario").on("submit", function(e){ guardaryeditar(e); });
}

function ListMedida(){
		
var id=$("#txtCOD_ARTICULO").val();
var idproveedor=$("#idproveedor").val();
	//alert(id);
	$.post("data/articulo.php?op=ListMedida&id="+id, function(r){
	            $("#medida").html(r);
                // PATCH 2026-01-31: aplicar Select2 y default de medida en nuevo
                initSelect2Articulo();
                // Default: UNIDAD (BIENES) si es nuevo (sin codigo)
                try {
                  var isNuevo = ($.trim($('#txtCOD_ARTICULO').val()) === '');
                  if(isNuevo){
                    // Preferir valor NIU si existe, sino buscar texto 'UNIDAD (BIENES)'
                    if($('#medida option[value="NIU"]').length){
                      $('#medida').val('NIU').trigger('change');
                    } else {
                      var opt = $('#medida option').filter(function(){ return $(this).text().toUpperCase().indexOf('UNIDAD (BIENES)')>=0; }).first();
                      if(opt.length){ $('#medida').val(opt.val()).trigger('change'); }
                    }
                  }
                } catch(e) {}
	});
	$.post("data/articulo.php?op=farmaceutica&id="+idproveedor, function(r){
	    $("#farmaceutica").html(r);
		
      initSelect2Articulo();
$("#farmaceutica")/* selectpicker removed */;
	});
		
}

function ListMedida2(){

	$.post("data/articulo.php?op=ListMedida2", function(r){
	            $("#medida2").html(r);
                // PATCH 2026-01-31: convertir medida2 a Select2 con búsqueda
                initSelect2Articulo();
	});

}

//Función limpiar
function limpiar(){
  // PATCH 2026-01-31: default soles (PEN) en nuevo
  try { $("#moneda").val("PEN"); } catch(e) {}
	$("#codigo").val("");
	$("#txtDESCRIPCION_ARTICULO").val("");
	$("#stock").val("1");
	$("#imagenmuestra").attr("src","");
	$("#imagenactual").val("");
	$("#print").hide();
	$("#txtCOD_ARTICULO").val("");
	$("#comision").val("0");

	$("#precio").val("0.00");
	$("#precio_mayor").val("0.00");
	$("#precio_mayor2").val("0.00");
	$("#precio_mayor3").val("0.00");
	$("#precio_porcentaje").val("1.00");
	$("#precio_porcentaje1").val("1.00");
	$("#precio_porcentaje2").val("1.00");

	$("#precioc").val("0.00");
	$("#ctimayor").val("1000");
	$("#comision").val("0.00");
	$("#comisionmp").val("0");
	$("#idproveedor").val("0");
	$("#sanitario").val("");
	$("#ctacompras").val("");
	$("#ctaventas").val("");
	$("#serie").val("");
	$("#lote").val("");
	$("#stockser").val("1.00");
	$("#fechven").val("dd/mm/aaaa");
  $("#maneja_lote").val("0");
  $("#maneja_serie").val("0");
  $("#maneja_garantia").val("0");
  $("#garantia_tipo").val("NINGUNA");
  $("#garantia_meses").val("0");
  $("#idcatalogo_afectacion").val("").trigger('change');

}

function agregararticulo(tipoac){
	
$("#tipoac").val(tipoac);
ListMedida2();	
ListMedida();
cargacombo('0', '0');
	
$.post("data/articulo.php?op=tempproducto",{}, function(data, status){
console.log(data);	
data = JSON.parse(data);
	
$("#txtCOD_ARTICULO").val(data.txtCOD_ARTICULO || data.id || "");
var id = $("#txtCOD_ARTICULO").val();

	
$("#txtDESCRIPCION_ARTICULO").val(data.txtDESCRIPCION_ARTICULO);

		$("#codigo").val(data.codigo);
		$("#precio").val(data.precio);
		
		$("#stock").val(data.stock);
	$("#stockmin").val(data.stockmin);
	$("#stockmax").val(data.stockmax);
		$("#ctimayor").val(data.mayor);
		$("#pmayor").val(data.precio_mayor);
		
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src","files/articulos/"+data.imagen+"?v="+Date.now());
		$("#imagenactual").val(data.imagen);
 		$("#txtCOD_ARTICULO").val(data.txtCOD_ARTICULO);
		if(data.exonerado_igv=='0'){ $("#exonerado").val("0"); }else{ $("#exonerado").val("1"); }

		$("#idcategoria").val(data.idcategoria);
		$("#idmarca").val(data.marca);
	$("#pactivo").val(data.principioactivo);
		$("#comision").val(data.comision);
		$("#comisionm").val(data.comisionm);
		$("#comisionmp").val(data.comisionmp);
		$("#codigos").val(data.codigosunat);
		$("#idproveedor").val(data.idproveedor);
		$("#sanitario").val(data.sanitario);
	
	$("#ctacompras").val(data.ctacompras);
	$("#ctaventas").val(data.ctaventas);
	$("#precio_porcentaje").val(data.precio_porcentaje);
	$("#precio_mayor3").val((typeof data.precio_mayor3 !== 'undefined') ? data.precio_mayor3 : (data.precio_mayor2 || "0"));
	
	$("#existencia").val(data.existencia).trigger('change');
		$("#moneda").val(data.moneda ? data.moneda : "PEN");
	  var manejaLoteTmp = (typeof data.maneja_lote !== 'undefined') ? data.maneja_lote : ((typeof data.manejalote !== 'undefined') ? data.manejalote : "0");
	  var manejaSerieTmp = (typeof data.maneja_serie !== 'undefined') ? data.maneja_serie : ((typeof data.manejaserie !== 'undefined') ? data.manejaserie : "0");
	  var manejaGarantiaTmp = (typeof data.maneja_garantia !== 'undefined') ? data.maneja_garantia : ((typeof data.manejagarantia !== 'undefined') ? data.manejagarantia : "0");
	  $("#maneja_lote").val(String(manejaLoteTmp)).trigger('change');
	  $("#maneja_serie").val(String(manejaSerieTmp)).trigger('change');
	  $("#maneja_garantia").val(String(manejaGarantiaTmp)).trigger('change');
	  $("#garantia_tipo").val(data.garantia_tipo ? data.garantia_tipo : "NINGUNA").trigger('change');
	  $("#garantia_meses").val((typeof data.garantia_meses !== 'undefined') ? data.garantia_meses : "0");
	  $("#idcatalogo_afectacion").val((typeof data.idcatalogo_afectacion !== 'undefined' && data.idcatalogo_afectacion !== null) ? data.idcatalogo_afectacion : "").trigger('change');
		
$('#codigos').append(new Option(data.ncodigosunat, data.codigosunat));
		
	$("#linea").val(data.linea).trigger('change');
		
$.post("data/articulo.php?op=subfamilia", {id_category:data.linea }, function(data1) {
    $("#sublinea").html(data1);
	$("#sublinea").val(data.sublinea).trigger('change');
	$.post("data/articulo.php?op=subfamilia", {id_category:data.sublinea }, function(data2) {
	    $("#subfamilia").html(data2);
		$("#subfamilia").val(data.subfamilia).trigger('change');
    });
});	
		
cargacombo(data.idcategoria, data.marca);
ListMedida();

});	
	
mostrarform(true);
focusDescripcionArticulo();

}

//Función mostrar formulario
function mostrarform(flag, habilitar){
	$(".page-container").addClass('sidebar-collapsed');
	limpiar();
	
	if (flag){

console.log('bloquear boton2:'+habilitar);
$("#agregarreceta").prop('disabled', habilitar);
		
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
		
	}else{
		$("#listadoregistros").show(); 
		$("#formularioregistros").hide();
		$("#btnagregar").show();
		ListMedida();
		$("#btnGuardar").prop("disabled",true);
		
	}
}

function cargacombo(categoria, marca, pactivo){
//Cargamos los items al select categoria
	$.post("data/articulo.php?op=selectCategoria&nivel=0&id="+categoria, function(r){
	         $("#idcategoria").html(r);
	         
      initSelect2Articulo();
$('#idcategoria')/* selectpicker removed */;

	});
	
$.post("data/articulo.php?op=selectCategoria&nivel=1&id="+marca, function(r){
	         $("#idmarca").html(r);
	         
      initSelect2Articulo();
$('#idmarca')/* selectpicker removed */;

	});  

$.post("data/articulo.php?op=principioactivo&id="+pactivo, function(r){
	         $("#pactivo").html(r);
	         
      initSelect2Articulo();
$('#pactivo')/* selectpicker removed */;

	});  
	
	
}
//Función cancelarform
function cancelarform(){
	limpiar();
	mostrarform(false);
}
//Función Listar
var tabla = null;

function listar() {

  // Si ya existe, destruye limpio
  if ($.fn.DataTable.isDataTable("#tbllistado_articulo")) {
    $("#tbllistado_articulo").DataTable().clear().destroy();
  }

  tabla = $("#tbllistado_articulo").DataTable({
    processing: true,
    serverSide: true,
    autoWidth: true,
    destroy: true,
    serverMethod: "post",
    scrollX: false,
    responsive: false,
    pageLength: 20,

    // ✅ por defecto: ordenar por ID desc (col 2)
    order: [[2, "desc"]],

	    ajax: {
	      url: "data/articulo.php?op=listar",
	      type: "POST",
	      dataType: "json",
        data: function(d){
          d.filtro_grupo = $("#filtro_grupo").val() || "0";
          d.filtro_marca = $("#filtro_marca").val() || "0";
          d.filtro_linea = $("#filtro_linea").val() || "0";
          d.filtro_stock = $("#filtro_stock_check").is(":checked") ? "1" : "0";
        },
	      cache: false,
	      error: function (xhr) {
	        console.error("AJAX listar ERROR:", xhr.status, xhr.responseText);
	      }
	    },

	    // ✅ tus reglas de columnas
	    columnDefs: [
	      { targets: [0, 2, 4, 11], visible: false, searchable: false },
	      { targets: 1, orderable: false },  // Opciones (no ordenar)
	      { targets: 3, className: "text-center" }, // Codigo
	      { targets: 6, className: "text-left" }, // Descripcion
	      { targets: 7, className: "text-center" }, // Stock
	      { targets: 8, className: "text-center" }, // Moneda
	      { targets: [9,10], className: "text-right" },      // Precio y P.Compra
	      { targets: 13, className: "text-center" }, // F/Ven
	      { targets: 14, className: "text-center" } // Estado
	    ],
      createdRow: function(row){
        $('td', row).css('white-space', 'normal');
      },
      headerCallback: function(thead){
        $(thead).find('th').addClass('text-center');
      },
      initComplete: function(){
        var $s = $('#tbllistado_articulo_filter input[type=search]');
        $s.attr('placeholder', 'BUSCAR AQUI...');
        $s.off('focus.buscaraqui').on('focus.buscaraqui', function(){
          $(this).val('');
        });
      }
	  });

  // ✅ Debug útil
	  console.log("DT inicializado:", $.fn.DataTable.isDataTable("#tbllistado_articulo"));
	  console.log("DT ajax url:", tabla.ajax.url());
}

$(document).on('change', '.sel-lote-art', function(){
  var fv = $(this).find('option:selected').data('fv') || '';
  var $row = $(this).closest('tr');
  $row.find('.fv-lote-art').text(fv);
});

$(document).on('change', '.sel-present-art', function(){
  var p = $(this).find('option:selected').data('precio');
  if(typeof p === 'undefined') return;
  var $row = $(this).closest('tr');
  $row.find('.precio-art').text(p);
});

$(document).on('change', '#maneja_garantia', function(){
  var on = ($(this).val() === "1");
  if(!on){
    $("#garantia_tipo").val("NINGUNA");
    $("#garantia_meses").val("0");
  }
});



// Formateador simple (evita NaN y respeta decimales)
function formatNumber(val, decimals){
  if(val === null || val === undefined || val === '') return (0).toFixed(decimals);
  var n = parseFloat(val);
  if(isNaN(n)) return (0).toFixed(decimals);
  return n.toFixed(decimals);
}


//LISTAR SERIE/LOTE/FECHA VENCIMIENTO
function listarserie(id){
	tabla=$('#tblseries').dataTable({
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
					url: 'data/articulo.php?op=listarserie&id='+id,
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
	    "order": [[ 5, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}
//LISTAR SERIE/LOTE/FECHA VENCIMIENTO
function listarunidad(){
	
var id=$("#txtCOD_ARTICULO").val();
var tipoac=$("#tipoac").val();
	
	tablaf=$('#tblunidad').dataTable({
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
					url: 'data/articulo.php?op=listarunidad&id='+id+'&tipoac='+tipoac,
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
	    "order": [[0, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}
//Función para guardar o editar
function guardaryeditar(e) {
  if (e && e.preventDefault) e.preventDefault();

  const tipoac = $("#tipoac").val();          // 2 = NUEVO (temporal) | !=2 = EDICIÓN
  const codigo = $("#codigo").val().trim();
  const precio = $("#precio").val();

  $("#btnGuardar").prop("disabled", true);

  /* =========================================================
     CASO 1: NUEVO ARTÍCULO (tipoac = 2)
     ========================================================= */
  if (tipoac === "2") {

    $.ajax({
      url: "data/articulo.php?op=guardaryeditar",
      type: "POST",
      dataType: "json",
      data: {
        tipoac: tipoac,
        codigo: codigo,
        precio: precio
      },
      success: function (datos) {
        console.log("RESPUESTA GUARDAR NUEVO:", datos);

        Swal.fire(datos.mensaje || "Proceso terminado");

        // ❌ error de validación
        if (datos.estado === "1") {
          $("#btnGuardar").prop("disabled", false);
          return;
        }

        // ✅ artículo creado correctamente
        if (datos.idreal) {

          // 🔑 fijamos el ID REAL del artículo
          $("#txtCOD_ARTICULO").val(datos.idreal);

          // 🔄 ya no es temporal
          $("#tipoac").val("1");

          // 🔄 refresca tabla
          if (tabla) tabla.ajax.reload(null, false);

          // 🖼️ refresca imágenes (rompe cache)
          setTimeout(() => {
            verimagen();
          }, 200);

          // 🧹 limpia cookie temporal (por seguridad extra)
          document.cookie = "idarticulo=; Max-Age=0; path=/;";
        }

        mostrarform(false);
        limpiar();
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        Swal.fire("Error", "Error al guardar (revisa Console/Network).", "error");
        $("#btnGuardar").prop("disabled", false);
      }
    });

    return;
  }

  /* =========================================================
     CASO 2: EDICIÓN DE ARTÍCULO REAL
     ========================================================= */
  const formData = new FormData($("#formulario")[0]);

  formData.append("tipoac", tipoac);
  formData.append("txtCOD_ARTICULO", $("#txtCOD_ARTICULO").val());

  $.ajax({
    url: "data/articulo.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (datos) {
      console.log("RESPUESTA EDITAR:", datos);

      Swal.fire(datos.mensaje || "Artículo actualizado");

      // ❌ error de validación
      if (datos.estado === "1") {
        $("#btnGuardar").prop("disabled", false);
        return;
      }

      // ✅ edición correcta
      if (tabla) tabla.ajax.reload(null, false);

      // 🖼️ refresca imágenes reales y rompe cache
      setTimeout(() => {
        verimagen();
      }, 200);

      mostrarform(false);
      limpiar();
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      Swal.fire("Error", "Error al actualizar (revisa Console/Network).", "error");
      $("#btnGuardar").prop("disabled", false);
    }
  });
}

function mostrar(txtCOD_ARTICULO, tipoac){
	
$("#tipoac").val(tipoac);
ListMedida2();

	$.post("data/articulo.php?op=mostrar",{txtCOD_ARTICULO : txtCOD_ARTICULO, tipoac: tipoac}, function(data, status){
		data = JSON.parse(data);		
	console.log(data);	
		//$("#idcategoria").val(data.idcategoria);
		//$('#idcategoria')/* selectpicker removed */;	
if(data.existencia=='02'){
mostrarform(true, false);
}else{	
mostrarform(true, true);
}
$("#txtDESCRIPCION_ARTICULO").val(data.txtDESCRIPCION_ARTICULO);
		$("#codigo").val(data.codigo);

$("#precio").val(data.precio);
// ===============================
// PATCH 2026-01-31: moneda guardada en edición
// - En nuevo queda por defecto PEN (SOLES)
// ===============================
$("#moneda").val((data.moneda && data.moneda!=="" ) ? data.moneda : "PEN");
$("#precio_porcentaje").val(data.precio_porcentaje);

$("#precio_mayor").val(data.precio_mayor);
$("#precio_porcentaje2").val(data.precio_porcentaje2);

$("#precio_mayor2").val(data.precio_mayor2);
$("#precio_porcentaje3").val(data.precio_porcentaje3);
$("#precio_mayor3").val((typeof data.precio_mayor3 !== 'undefined') ? data.precio_mayor3 : (data.precio_mayor2 || "0"));
  var manejaLote = (typeof data.maneja_lote !== 'undefined') ? data.maneja_lote : ((typeof data.manejalote !== 'undefined') ? data.manejalote : "0");
  var manejaSerie = (typeof data.maneja_serie !== 'undefined') ? data.maneja_serie : ((typeof data.manejaserie !== 'undefined') ? data.manejaserie : "0");
  var manejaGarantia = (typeof data.maneja_garantia !== 'undefined') ? data.maneja_garantia : ((typeof data.manejagarantia !== 'undefined') ? data.manejagarantia : "0");
  $("#maneja_lote").val(String(manejaLote)).trigger('change');
  $("#maneja_serie").val(String(manejaSerie)).trigger('change');
  $("#maneja_garantia").val(String(manejaGarantia)).trigger('change');
  $("#garantia_tipo").val(data.garantia_tipo ? data.garantia_tipo : "NINGUNA").trigger('change');
  $("#garantia_meses").val((typeof data.garantia_meses !== 'undefined') ? data.garantia_meses : "0");
  $("#idcatalogo_afectacion").val((typeof data.idcatalogo_afectacion !== 'undefined' && data.idcatalogo_afectacion !== null) ? data.idcatalogo_afectacion : "").trigger('change');

		$("#stock").val(data.stock);
		$("#stockmin").val(data.stockmin);
		$("#stockmax").val(data.stockmax);
		
		$("#ctimayor").val(data.mayor);
		
		$("#precioc").val(data.precio_compra);
		
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src","files/articulos/"+data.imagen+"?v="+Date.now());
		$("#imagenactual").val(data.imagen);
 		$("#txtCOD_ARTICULO").val(data.txtCOD_ARTICULO);
		if(data.exonerado_igv=='0'){ $("#exonerado").val("0"); }else{ $("#exonerado").val("1"); }
		$("#idcategoria").val(data.idcategoria);
		$("#idmarca").val(data.marca);
		
		$("#comision").val(data.comision);
		$("#comisionm").val(data.comisionm);
		$("#comisionmp").val(data.comisionmp);
		$("#codigos").val(data.codigosunat);
		//$("#idproveedor").val(data.idproveedor);		
				
		$("#sanitario").val(data.sanitario);
		
		$("#ctacompras").val(data.ctacompras);
		$("#ctaventas").val(data.ctaventas);
		$("#oferta").val(data.preciooferta);
	
$("#canjepuntos").val(data.canjepuntos);
$("#canjecobro").val(data.canjecobro);
		
$("#linea").val((typeof data.linea !== 'undefined' && data.linea !== null && data.linea !== '') ? data.linea : "0").trigger('change');
$('#idlocal option[value='+data.idlocal+']').prop('selected', 'selected').change();
$('#medida option[value='+data.medida+']').prop('selected', 'selected').change();

if(data.canje!=''){
$('#canje option[value='+data.canje+']').prop('selected', 'selected').change();
}		
$('#codigos').append(new Option(data.ncodigosunat, data.codigosunat));
		
$('#bolsa option[value='+data.bolsa+']').prop('selected', 'selected').change();

if(data.existencia!=''){
$('#existencia option[value='+data.existencia+']').prop('selected', 'selected').change();
}
var lineaSel = (typeof data.linea !== 'undefined' && data.linea !== null && data.linea !== '') ? data.linea : ((typeof data.idlinea !== 'undefined') ? data.idlinea : "0");
var sublineaSel = (typeof data.sublinea !== 'undefined' && data.sublinea !== null && data.sublinea !== '') ? data.sublinea : ((typeof data.idsublinea !== 'undefined') ? data.idsublinea : "0");
var subfamiliaSel = (typeof data.subfamilia !== 'undefined' && data.subfamilia !== null && data.subfamilia !== '') ? data.subfamilia : ((typeof data.idsubfamilia !== 'undefined') ? data.idsubfamilia : "0");
$.post("data/articulo.php?op=subfamilia", {id_category:lineaSel }, function(data1) {
    $("#sublinea").html(data1);
	$("#sublinea").val(sublineaSel).trigger('change');
	$.post("data/articulo.php?op=subfamilia", {id_category:sublineaSel }, function(data2) {
	    $("#subfamilia").html(data2);
		$("#subfamilia").val(subfamiliaSel).trigger('change');
    });
});	
		
		
//$("#pactivo").val(data.principioactivo);
cargacombo(data.idcategoria, data.marca, data.principioactivo);		
listarunidad();
		
var datos  = [];
var objeto = {}; 
var arrayDeCadenas = data.idproveedor.split(',');
		
for(var i= 0; i < arrayDeCadenas.length; i++) {
datos.push(arrayDeCadenas[i]);	
$('#farmaceutica').find('option[value="'+arrayDeCadenas[i]+'"]').prop('selected', true).parent()/* selectpicker removed */;
}
objeto= datos;			
$('#farmaceutica').val(datos).trigger('change');	

var datos2  = [];
var objeto2 = {}; 
var arrayDeCadenas2 = data.principioactivo.split(',');

console.log('princpio-activo:'+arrayDeCadenas2);
		
for(var i= 0; i < arrayDeCadenas2.length; i++) {
datos2.push(arrayDeCadenas2[i]);	
$('#pactivo').find('option[value="'+arrayDeCadenas2[i]+'"]').prop('selected', true).parent()/* selectpicker removed */;
}
objeto2= datos2;	
$('#pactivo').val(datos2).trigger('change');			
		
		// En edición: volver a #home y enfocar descripción
		focusDescripcionArticulo();


})
	
listarrecetafin();	
}
 
const swalWithBootstrapButtons = Swal.mixin({
  customClass: {
    confirmButton: 'btn btn-success',
    cancelButton: 'btn btn-danger'
  },
  buttonsStyling: false
})

//Función para desactivar registros
function desactivarserie(txtCOD_ARTICULO){

swalWithBootstrapButtons.fire({
  title: 'DESEA CONTINUAR?', 
  text: "Esta por marcar como vendido!",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonText: 'SI, cambiar!',
  cancelButtonText: 'NO, cancelar!',
  reverseButtons: true
}).then((result) => {
  if (result.isConfirmed) {
	  
$.post("data/articulo.php?op=desactivarlote", {txtCOD_ARTICULO : txtCOD_ARTICULO}, function(e){
	
swalWithBootstrapButtons.fire(
      'ACTUALIZADO!',
      e,
      'success'
    )
	    tabla.ajax.reload();
	
        	});	  

  } else if (
    /* Read more about handling dismissals below */
    result.dismiss === Swal.DismissReason.cancel
  ) {
    swalWithBootstrapButtons.fire(
      'CANCELADO',
      'No se realizó ningun cambio :)',
      'error'
    )
  }
})
	
	/*
	bootbox.confirm("¿Desea marcar como vendido?", function(result){
		if(result)
        {
        	$.post("data/articulo.php?op=desactivarserie", {txtCOD_ARTICULO : txtCOD_ARTICULO}, function(e){
        		Swal.fire(e);
	            tabla.ajax.reload();
        	});	
        }
	})
	
	*/
	
}

//Función para desactivar registros

function desactivar(txtCOD_ARTICULO){
	Swal.fire({
			title: "CONFIRMAR",
			text: "¿¿Está Seguro de desactivar Articulo?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "DESACTIVAR",
			cancelButtonText: "CANCELAR"
		}
	).then(

		function (isConfirm) {

			if (isConfirm.value==true) {
				console.log('CONFIRMED');

				$.post("data/articulo.php?op=desactivar", {txtCOD_ARTICULO : txtCOD_ARTICULO}, function(e){
					Swal.fire(e);
					tabla.ajax.reload();
				});

			}else{
				console.log('BACK');
			}
		},
	);

}
//Función para activar registros
function activar(txtCOD_ARTICULO){
	Swal.fire({
			title: "CONFIRMAR",
			text: "¿¿Está Seguro de activar Articulo?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "DESACTIVAR",
			cancelButtonText: "CANCELAR"
		}
	).then(

		function (isConfirm) {

			if (isConfirm.value==true) {
				console.log('CONFIRMED');

				$.post("data/articulo.php?op=activar", {txtCOD_ARTICULO : txtCOD_ARTICULO}, function(e){
					Swal.fire(e);
					tabla.ajax.reload();
				});

			}else{
				console.log('BACK');
			}
		},
	);

}


//Función para activar registros
function eliminar(id){
	
	
Swal.fire({
  title: 'DESEA ELIMINAR?',
  text: 'Si elimina no podra vovler a recuperar!',
  icon: 'warning',
  showCancelButton: true,
  confirmButtonText: 'SI, eliminar!',
  cancelButtonText: 'NO, eliminar'
}).then((result) => {
  if (result.value) {
//eli9minado  
$.post("data/articulo.php?op=eliminar", {codigo: id}, function(e){
    Swal.fire(
      'Deleted!',
      'Your imaginary file has been deleted.',
      'success'
    )
tablaf.ajax.reload();
});	
  // For more information about handling dismissals please visit
  // https://sweetalert2.github.io/#handling-dismissals
  } else if (result.dismiss === Swal.DismissReason.cancel) {
    Swal.fire(
      'Cancelled',
      'No se hicieron cambios :)',
      'error'
    )
  }
})

	
	
/*
	bootbox.confirm("¿Deseas eliminar?", function(result){
		if(result)
        {
        	$.post("data/articulo.php?op=eliminar", {codigo: id}, function(e){				
swal.close();
Swal.fire(e);
				
	            tablaf.ajax.reload();
        	});	
        }
	})
*/	
	
	
}
//función para generar el código de barras
function generarbarcode(){
	codigo=$("#codigo").val();
	JsBarcode("#barcode", codigo);
	$("#print").show();
}
//Función para imprimir el Código de barras
function imprimir(){
	$("#print").printArea();
}
//Función para activar registros
function resaltar(codigo, estado){

        	$.post("data/articulo.php?op=resaltar", {codigo : codigo, estado: estado}, function(e){
        		Swal.fire(e);
	            tabla.ajax.reload();
        	});	

}

function addmedida(id) {
        $('#modalunidad').modal('show');
	$("#codartu").val(id);
	ListMedida();
	listarunidad();

}

function addserie(id) {
    $('#modalserie').modal('show');
	$("#codart").val(id);
	listarserie(id);
	
$.post("data/articulo.php?op=proveedorp", {articulo:id }, function(data1) {
    $("#idproveedora").html(data1);
});	

}

function stockinicial(id, stock, precio, fecha) {
    $('#stockinicial').modal('show');
	$("#stocki").val(stock);
	$("#precioi").val(precio);
	$("#idi").val(id);
	$("#fechai").val(fecha);
	
	console.log(id);
}

function guardastockinicial() {
	
	var idi=$("#idi").val();
	var stocki=$("#stocki").val();
	var precioi=$("#precioi").val();
	var fechai=$("#fechai").val();
	
console.log(idi);
	
	$.ajax({
		url: "data/articulo.php?op=stockinicial",
	    type: "POST",
	    data: {'codart':idi, 'stock':stocki, 'precio':precioi, 'fecha':fechai},
	    success: function(datos){ 
	 		
$("#stockinicial").modal('hide');//ocultamos el modal
  $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
  $('.modal-backdrop').remove();//eliminamos el backdrop del modal
swal.close();
Swal.fire(datos);
tabla.ajax.reload();
	    }

	});

}

function guardaserie() {
	var fechven=$("#fechven").val();



var formData2 = new FormData($("#formserie")[0]);
formData2.append("codart", $("#codart").val());
formData2.append("serie", $("#serie").val());
formData2.append("stockser", $("#stockser").val());
formData2.append("lote", $("#lote").val());
formData2.append("idproveedora", $("#idproveedora").val());
formData2.append("fechven", $("#fechven").val());
	
console.log('fechven:'+$("#fechven").val());

	var serie=$("#serie").val();
	if(serie==''){ Swal.fire('AGREGUE MINIMO UNA SERIE'); return true; }

	$.ajax({
		url: "data/articulo.php?op=guardaserie",
	    type: "POST",
	    data: formData2,
	    contentType: false,
	    processData: false,
	    success: function(datos){                    
swal.close();
Swal.fire(datos);

			tabla.ajax.reload();
			limpiar();
	    }

	});

}

function guardaunidad() {
	
var formData2 = new FormData($("#formunidad")[0]);

formData2.append('nombreu', $("#nombreu").val());
formData2.append('ctiunidad', $("#ctiunidad").val());
formData2.append('medida2', $("#medida2").val());
formData2.append('codartu', $("#txtCOD_ARTICULO").val());
formData2.append('preciou', $("#preciou").val());
formData2.append('comisionu', $("#comisionu").val());
formData2.append('ctimayoru', $("#ctimayoru").val());
formData2.append('preciomu', $("#preciomu").val());
formData2.append('comisionmu', $("#comisionmu").val());
formData2.append('tipoac', $("#tipoac").val());

	$.ajax({
		url: "data/articulo.php?op=guardaunidadm",
	    type: "POST",
	    data: formData2,
	    contentType: false,
	    processData: false,
	    success: function(datos){
			console.log(datos);      
	          tablaf.ajax.reload();
	    }

	});
	
	
	
}

function guardarcardex(){ 

var mes=$("#mes").val();
var anio=$("#anio").val();
var idlocal=$("#idlocal").val();

Swal.fire('GENERANDO INFORMACIÓN');
	
$.post("data/articulo.php?op=cardex", {mes:mes, anio: anio, idlocal:idlocal}, function(e, status){
console.log(status);
	console.log(e);
        		Swal.fire(e.mensaje);
	            tabla.ajax.reload();
        	}).fail(function(e) {
   console.log(e);
  });
	
	
}

function guardapaquete(){
	
var formData2 = new FormData($("#formunidad")[0]);

formData2.append('idsec', $("#idartpaquete").val());
formData2.append('id', $("#codart").val());
formData2.append('ctiunidad', $("#ctipaquete").val());

	$.ajax({
		url: "data/articulo.php?op=guardarpaquete",
	    type: "POST",
	    data: formData2,
	    contentType: false,
	    processData: false,
	    success: function(datos){
			console.log(datos);      
	          paquetes.ajax.reload();
	    }

	});
	
	
	
}

//Función Listar
function listarcardex(){
	
var idlocal=$("#idlocal").val();	
	
	tabla=$('#tbllistado_articulo').dataTable({
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
					url: 'data/articulo.php?op=listarcardex&idlocal='+idlocal,
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 15,//Paginación
	    "order": [[ 1, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}

function agregarsunat(codigo){
	$("#codigos").val(codigo);
	
$("#modalcodigo").modal('hide');//ocultamos el modal
$('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
$('.modal-backdrop').remove();//eliminamos el backdrop del modal
	
}

function listarcodigos(){
tablasunat=$('#tblsunat').dataTable({
"processing": true,
      'serverSide': true,
      'serverMethod': 'post',
	    buttons: [		          
		            'copyHtml5',
		            'excelHtml5',
		            'csvHtml5',
		            'pdf'
		        ],
		"ajax":
				{
					url: 'data/articulo.php?op=codigosunat',
					type : "post",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
"bScrollCollapse": true,
 
"filter": true,         // this is for disable filter (search box)
"orderMulti": true,     // for disable multiple column at once
"pageLength": 15,
"searchDelay":1000,
"bAutoWidth": false,
"iDisplayLength": 20,//Paginación
	    "order": [1, "desc"]//Ordenar (columna,orden)
	}).DataTable();
}


function addcodigosunat() {
$('#modalcodigo').modal('show');
listarcodigos();

}

$(document).ready(function(){
	
    $("#linea").on('change', function () {
        $("#linea option:selected").each(function () {
            var id_category = $(this).val();
			console.log(1);
            $.post("data/articulo.php?op=subfamilia", { id_category: id_category }, function(data) {
				console.log(2);
				console.log(data);
                $("#sublinea").html(data);
            });			
        });
   });
	
$("#sublinea").on('change', function () {
        $("#sublinea option:selected").each(function () {
            var id_category = $(this).val();
			console.log(1);
            $.post("data/articulo.php?op=subfamilia", { id_category: id_category }, function(data) {
				console.log(2);
				console.log(data);
                $("#subfamilia").html(data);
            });			
        });
   });	
	
	
});

function actualizararticulo() {

	var tipoac=$("#tipoac").val();
	
if(tipoac=='2'){
	
var formData2 = new FormData($("#formulario")[0]);

formData2.append('txtDESCRIPCION_ARTICULO', $("#txtDESCRIPCION_ARTICULO").val());
formData2.append('idcategoria', $("#idcategoria").val());
formData2.append('idmarca', $("#idmarca").val());
formData2.append('linea', $("#linea").val());
formData2.append('sublinea', $("#sublinea").val());
formData2.append('subfamilia', $("#subfamilia").val());
formData2.append('stock', $("#stock").val());
formData2.append('stockmin', $("#stockmin").val());
formData2.append('stockmax', $("#stockmax").val());
formData2.append('precio', $("#precio").val());
formData2.append('precio_porcentaje', $("#precio_porcentaje").val());
formData2.append('precio_mayor', $("#precio_mayor").val());
formData2.append('precio_porcentaje2', $("#precio_porcentaje2").val());
formData2.append('precio_mayor2', $("#precio_mayor2").val());
formData2.append('precio_porcentaje3', $("#precio_porcentaje3").val());
formData2.append('precio_mayor3', $("#precio_mayor3").val());
formData2.append('precioc', $("#precioc").val());
formData2.append('ctimayor', $("#ctimayor").val());
formData2.append('pmayor', $("#pmayor").val());
formData2.append('exonerado', $("#exonerado").val());
formData2.append('comision', $("#comision").val());
formData2.append('comisionm', $("#comisionm").val());
formData2.append('comisionmp', $("#comisionmp").val());
formData2.append('medida', $("#medida").val());
formData2.append('farmaceutica', $("#farmaceutica").val());
formData2.append('sanitario', $("#sanitario").val());
formData2.append('codigos', $("#codigos").val());
formData2.append('codigo', $("#codigo").val());
formData2.append('bolsa', $("#bolsa").val());
formData2.append('existencia', $("#existencia").val());
formData2.append('idcatalogo_afectacion', $("#idcatalogo_afectacion").val());
formData2.append('pactivo', $("#pactivo").val());

console.log('medida:'+$("#medida").val());
	
	$.ajax({
		url: "data/articulo.php?op=actualizaarticulo",
	    type: "POST",
	    data: formData2,
	    contentType: false,
	    processData: false,
	    success: function(datos){
			console.log(datos);      
	    }

	});
	
}	
	
}

function txtguiasunat(id){

window.location.href = 'modelos/descargas.php?op=txtguiasunat&id='+id;
	
}

function arttxtguiasunat(id){

window.location.href = 'modelos/descargas.php?op=arttxtguiasunat&id='+id;
	
}

function kardexfecha(){

var fechaini=$("#fechaini").val();
var fechafin=$("#fechafin").val();
/*
window.location.href = "modelos/descargas.php?op=kardexfecha&fechaini="+fechaini+"&fechafin="+fechafin;
	*/
window.open("modelos/descargas.php?op=kardexfecha&fechaini="+fechaini+"&fechafin="+fechafin, '_blank');	
}

function imprimircode(id){

$.ajax({

url: "plugins/dompdf/codigo-barras.php?id="+id,
success: function (response) { 
	
var docprint = window.open("about:blank", "_blank", "channelmode");    
                var oTable = document.getElementById("tbl");
                docprint.document.open(); 
                docprint.document.write('<html><head><title>your title</title>'); 
                docprint.document.write('</head><body>');
                docprint.document.write(response);
                docprint.document.write('</body></html>'); 
                docprint.document.close(); 
                docprint.print();
                docprint.close();

	
//$('#mydiv').hide();
},

error: function (data) { console.log(data); alert('Error Al conectar la Base Datos'); }

});
	

}

function listarpaquetes(id){
	
paquetes=$('#tblpaquetes').dataTable({
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
					url: 'data/articulo.php?op=listarpaquetes&id='+id,
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"iDisplayLength": 6,//Paginación
	    "order": [[ 1, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
	
}

function artrelacioados(id) {
    $('#myModal').modal('show');
	$("#codart").val(id);
listarpaquetes(id);
	
$.post("data/articulo.php?op=artrelacionados", function(r){
	    $("#idartpaquete").html(r);
		$("#idartpaquete")/* selectpicker removed */;
	});
	
	
}

function eliminarreceta(id){
	
	$.post("data/articulo.php?op=eliminarreceta&id="+id, function(r){
paquetes.ajax.reload();

	});	
	
}

function paginadorstock(pagina, idlocal, cantidad){

//Swal.fire('PROCESANDO INGRESOS ('+cantidad+')');
		
$.ajax({
	url:'data/articulo.php?op=regularizarstock',
	dataType : "json",
	type : 'POST',
	data : {idlocal:idlocal, pagina:pagina},
	success: function(respuesta) {
	
//console.log(respuesta);
//Swal.fire(respuesta.mensaje);
if(respuesta.proceso=='0'){
setTimeout(transfieredatos(respuesta.pagina, respuesta.cantidad), 3000);

var porcenta=(respuesta.cantidad*100)/respuesta.paginas;
$("#mensaje").html(respuesta.mensaje);
$('#barcalcula').css({ 'width':porcenta+'%' });
	
}else{	
tabla.ajax.reload();	
Swal.fire(respuesta.mensaje);
	
$("#barraprogreso").css("display", "none");
$("#mensaje").html('INICIANDO PROCESO...');
$('#barcalcula').css({ 'width':'1%' });
	
}
		
},
	error: function(respuesta) {
        console.log(respuesta);
    } 
});	
}

function transfieredatos(pagina, cantidad){
var idlocal=$("#idlocal").val();
paginadorstock(pagina, idlocal, cantidad);
	
}

function stockkardex(){
	
Swal.fire({
  title: 'ESTE PROCESO ACTUALIZARA STOCK APARTIR DEL KARDEX, DESEAS COTINUAR?',
  text: "Recuerda que esto no tiene marcha atras!",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#d33',
  cancelButtonColor: '#3085d6',
  confirmButtonText: 'SI, ACTUALIZAR!', 
cancelButtonText: 'CANCELAR'
}).then((result) => {
  if (result.isConfirmed) {

$("#barraprogreso").css("display", "block");
$("#mensaje").html('INICIANDO PROCESO...');
$('#barcalcula').css({ 'width':'1%' });
	  
transfieredatos('0', '300');
	  
	  /*
Swal.fire('<i class="fa fa-circle-o-notch fa-spin "></i> PROCESANDO COMPROBANTES!');

$.ajax({
url: "data/articulo.php?op=regularizarstock",
type: "POST",
dataType: 'json',
data: {},
success: function (data) { 
	console.log(data); 
Swal.fire(data.mensaje);
tabla.ajax.reload();

},
error: function (data) { console.log(data); }
});
  
	*/  
	  
	  
  }
})
	
	
}

function desexcel(){

	var idlocal= $("#idlocal").val();
	var marca= $("#marca").val();

window.location.href = 'modelos/descargas.php?op=excelstock&idlocal='+idlocal+'&marca='+marca;
	
}

function listarrecetafin(){
	
var id=$("#txtCOD_ARTICULO").val();
		
tablarecetas=$('#ventas').dataTable({

		"ajax":
				{
					url: 'data/articulo.php?op=listarreceta&id='+id,
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
	    "order": [[0, "desc" ]],
    "info": false, //Dont display info e.g. "Showing 1 to 4 of 4 entries"
    "paging": false,//Dont want paging                
    "bPaginate": false,
	searching: false,
	info: false
	}).DataTable();
	
tablarecetas.destroy();
	
}


function BuscarArticulo() {
            $('#myModal').modal('show');
          listarArticulos();
}

function listarArticulos(){
	
	tabla=$('#tblarticulos').dataTable({
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          
		            
		        ],
		"ajax":
				{
					url: 'data/articulo.php?op=articulosreceta',
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

function addtabla(idsub) {
	
var idproducto=$("#txtCOD_ARTICULO").val();
var cti=$("#cti"+idsub).val();

$.post("data/articulo.php?op=guardarpaquete", {idproducto:idproducto, idsub:idsub, cantidad:cti}, function(e, status){
console.log(status);
	console.log(e);
        		Swal.fire(e);
tablarecetas.ajax.reload();
        	}).fail(function(e) {
   console.log(e);
  });

}

function eliminarreceta(id){
	
	$.post("data/articulo.php?op=eliminarreceta&id="+id, function(r){
tablarecetas.ajax.reload();

	});	
	
}

function actualizareceta(id) {

var cti=$("#ctidet"+id).val();
	
$.post("data/articulo.php?op=actualizareceta", {codart:id, cantidad:cti}, function(e, status){
console.log(status);
	console.log(e);
        		Swal.fire(e);
tablarecetas.ajax.reload();
        	}).fail(function(e) {
   console.log(e);
  });
	

}






function listarcostos(){

$('#btniniciar').attr('disabled', true);
$('#btnfinalizar').attr('disabled', false);

var cantidad= $("#cantidad").val();

conciliacion=$('#detconciliacion').dataTable({
"aProcessing": true,//Activamos el procesamiento del datatables
"aServerSide": true,//Paginación y filtrado realizados por el servidor
"ajax":{
url: 'data/articulo.php?op=listarcostos&cantidad='+cantidad,
type : "get",
dataType : "json",						
error: function(e){
console.log(e.responseText);
}
},
"bDestroy": true,
"bPaginate": false,
"paging": false,
"bDestroy" : true,
"bAutoWidth" : true,
"sScrollY" : "340",
"sScrollX" : "100%",
"bLengthChange" : false,
"sDom": "lfrti",
    "bFilter": false, //hide Search bar
    "bInfo": false, // hide showing entries


			"order": [[ 0, "desc" ]]//Ordenar (columna,orden)
		}).DataTable();
}


function teclea(id, nivel){

var costopromedio=$('#costopromedio'+id).val();
var tipoigv=$('#tipoigv'+id).val();
var porcentaje=$('#precio'+nivel+id).val();
var cantidad=$('#cantidad').val();
var precioa;
var bruto; var margen; var valorventa; var precioventa;
var igvinicial='1.18';

bruto=parseFloat(costopromedio*porcentaje)/parseFloat(100);
precioa=parseFloat(bruto*cantidad)/parseFloat(100);
margen=parseFloat(bruto-precioa);
valorventa=parseFloat(costopromedio+bruto);
precioventa=valorventa;
if(tipoigv=='0'){
precioventa=parseFloat(valorventa*igvinicial);
}

$('#bruto'+nivel+id).val(bruto);
$('#precioa'+nivel+id).val(precioa);	
$('#margen'+nivel+id).val(margen);
$('#valorventa'+nivel+id).val(valorventa);
$('#precioventa'+nivel+id).val(precioventa);

}


function finalizarprocesoprecio() {

Swal.fire({
title: 'DESEAS ACTUALIZAR LOS PRECIOS?',
text: "Recuerda que esto no tiene marcha atras!",
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#3085d6',
confirmButtonText: 'SI, ACTUALIZAR!', 
cancelButtonText: 'CANCELAR'
}).then((result) => {
if (result.isConfirmed) {

$("#barraprogreso").css("display", "block");
$("#mensaje").html('INICIANDO PROCESO...');
$('#barcalcula').css({ 'width':'1%' });
$('#btnfinalizar').attr('disabled', true);

Swal.fire('<i class="fa fa-circle-o-notch fa-spin "></i> PROCESANDO PRECIOS!');

var DATA2= [];
var num='0';
$("#detconciliacion tr").each(function(index){
var idp='';

if(num>1){
var detalle2 = {};
$(this).children("td").each(function(index2){
switch(index2){
case 0:	
idp=$(this).text();
break;
}

detalle2["txtCOD_ARTICULO"]=idp;
detalle2["precio1"]=$('#margen1'+idp).val();
detalle2["porcentaje1"]=$('#precio1'+idp).val();
detalle2["precio2"]=$('#margen2'+idp).val();
detalle2["porcentaje2"]=$('#precio2'+idp).val();
detalle2["precio3"]=$('#margen3'+idp).val();
detalle2["porcentaje3"]=$('#precio3'+idp).val();

});
DATA2.push(detalle2);
}	
num=num+1;		
});

$('#btniniciar').attr('disabled', false);

$.ajax({
url: "data/articulo.php?op=finalizarprocesoprecio",
type: "POST",
dataType: 'json',
data: {detalle:DATA2},
success: function (data) { 
console.log(data); 
Swal.fire(data.mensaje);
conciliacion.clear().draw();

//destroy datatable
//table.destroy();

},
error: function (data) { console.log(data); }
});

}
})

}

function verimagen() {
  const tipoac = String($("#tipoac").val() || "");
  const idReal = String($("#txtCOD_ARTICULO").val() || "");

  // TEMP: puede existir cookie idarticulo aunque txtCOD_ARTICULO esté vacío
  // pero tú ya lo llenas en tempproducto, igual dejo la validación
  if (!idReal && tipoac !== "2") {
    $("#subirimagen").html('<div class="alert alert-warning">Primero crea o selecciona el artículo.</div>');
    return;
  }

  $.ajax({
    url: "data/articulo.php?op=tempimages",
    type: "POST",
    dataType: "json",
    data: { tipoac: tipoac, txtCOD_ARTICULO: idReal },
    success: function (response) {

      $("#subirimagen").html(`
        <div class="file-loading">
          <input id="input-705" type="file" name="input-705[]" multiple>
        </div>
      `);

      const uploadUrl =
        (tipoac === "2")
          ? "modelos/upload-imagen.php?modo=tmp"
          : "modelos/upload-imagen.php?modo=real&idarticulo=" + encodeURIComponent(idReal);

      $("#input-705").fileinput("destroy").fileinput({
        uploadUrl: uploadUrl,
        language: "es",

        browseOnZoneClick: true,
        showBrowse: true,      // ✅ que aparezca Examinar
        showCaption: true,
        showUpload: true,
        showRemove: true,

        allowedFileExtensions: ["jpg", "png", "jpeg"],
        maxFileCount: 5,
        overwriteInitial: false,

        initialPreviewAsData: true,
        initialPreview: response.initialPreview || [],
        initialPreviewConfig: response.initialPreviewConfig || [],

        // evita cache del plugin
        uploadExtraData: function () {
          return { _v: Date.now() };
        }
      });
      // Refrescar preview luego de subir o borrar (evita cache y muestra cambios al instante)
      $("#input-705")
        .off("fileuploaded filedeleted filebatchuploadsuccess")
        .on("fileuploaded filebatchuploadsuccess filedeleted", function () {
          setTimeout(function(){ verimagen(); }, 250);
        });

    },
    error: function (xhr) {
      console.log(xhr.responseText);
      $("#subirimagen").html('<div class="alert alert-danger">Error cargando imágenes.</div>');
    }
  });
}



$('a[href="#menu3"]').on('shown.bs.tab', function () {
  verimagen();
});





// Cerrar al hacer click fuera
$(document).on("click", function () {
  $(".dropdown-menu").hide();
  $(".btn-opciones-art").attr("aria-expanded", "false");
});



function ListCliente(tipodoc){

console.log(tipodoc);

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

// ===============================
// FIX OPCIONES (DataTables + scrollX + overflow)
// - Un solo handler
// - Menú se posiciona con fixed (no se recorta)
// ===============================
(function () {

  function closeAllArtDropdowns() {
    $(".dropdown-menu-opciones").hide();
    $(".btn-opciones-art").attr("aria-expanded", "false");
  }

  // Cerrar al hacer click fuera o scroll
  $(document).on("click", function () {
    closeAllArtDropdowns();
  });

  $(document).on("scroll", function () {
    closeAllArtDropdowns();
  });

  // Evita que click dentro del menú lo cierre
  $(document).on("click", ".dropdown-menu-opciones", function (e) {
    e.stopPropagation();
  });

  // Toggle del botón
  $(document).on("click", ".btn-opciones-art", function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $btn = $(this);
    const $dropdown = $btn.closest(".dropdown");
    const $menu = $dropdown.find(".dropdown-menu-opciones");

    if (!$menu.length) {
      console.warn("No se encontró .dropdown-menu-opciones para este botón.");
      return;
    }

    // Si ya estaba abierto, cerrar
    if ($menu.is(":visible")) {
      closeAllArtDropdowns();
      return;
    }

    // Cierra otros
    closeAllArtDropdowns();

    // Posicionar (fixed) según botón
    const rect = this.getBoundingClientRect();

    $menu.css({
      position: "fixed",
      top: (rect.bottom + 2) + "px",
      left: rect.left + "px",
      zIndex: 999999,
      display: "block"
    }).show();

    // Si se sale por la derecha, lo ajusta
    const menuRect = $menu[0].getBoundingClientRect();
    const overflowRight = menuRect.right - window.innerWidth;
    if (overflowRight > 0) {
      const newLeft = Math.max(5, rect.left - overflowRight - 5);
      $menu.css({ left: newLeft + "px" });
    }

    $btn.attr("aria-expanded", "true");
  });

})();



init();
// =====================================================
// EXPONER FUNCIONES (tu HTML usa onclick/onkeyup/onmouseout)
// =====================================================
window.guardaryeditar = guardaryeditar;
window.actualizararticulo = actualizararticulo;
window.cancelarform = cancelarform;
window.mostrar = mostrar;
window.eliminar = eliminar;
window.limpiar = limpiar;

// PATCH 2026-01-31: reenganchar Select2 al mostrar pestañas (tabs)
$('a[data-toggle="tab"]').on('shown.bs.tab', function () {
  initSelect2Articulo();
});
