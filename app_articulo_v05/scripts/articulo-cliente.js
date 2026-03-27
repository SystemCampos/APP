var tabla;
var tablaf;
var tablasunat;
var paquetes;
var tablarecetas;
var conciliacion;


//Función limpiar
function limpiar(){

	$("#precio").val("0.00");
	$("#precio_mayor").val("0.00");
	$("#precio_mayor2").val("0.00");
	$("#precio_porcentaje").val("1.00");
	$("#precio_porcentaje1").val("1.00");
	$("#precio_porcentaje2").val("1.00");

	$("#precioc").val("0.00");

}

//Función mostrar formulario
function mostrarform(flag){

	limpiar();
	
	if (flag){
		
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);


	}else{
		$("#listadoregistros").show(); 
		$("#formularioregistros").hide();
		$("#btnGuardar").prop("disabled",true);
		
	}
}

//Función cancelarform
function cancelarform(){
mostrarform(false);
listar();
}

//Función Listar
function listar(){
    
if ($.fn.DataTable.isDataTable('#tbllistado')) {
  $('#tbllistado').DataTable().destroy();
  $('#tbllistado tbody').empty();
}

var cliente=$("#cliente").val();

tabla = $('#tbllistado').DataTable({
  processing: true,
  serverSide: true,
  serverMethod: 'post',
  responsive: true,
  autoWidth: false,      // ✅ mejor para controlar anchos
  scrollX: false,
  dom: 'Bfrtip',

  // ✅ Orden inicial (puedes cambiarlo)
  order: [[2, "asc"]],   // 2 = ID/Cód (según tu tabla)

  // ✅ Definir columnas (mejor que solo columnDefs)
  columnDefs: [
    { targets: 0, width: "90px",  orderable: false, className: "dt-center" }, // Opciones
    { targets: 1, width: "90px",  orderable: true,  className: "dt-center" }, // List.Pre.
    { targets: 2, width: "20px",  orderable: true,  className: "dt-center"  },// ID / Cód
    { targets: 3, width: "120px", orderable: true },                          // Código
    { targets: 4,                orderable: true },                           // Descripción
    {
      targets: 5,
      width: "120px",
      orderable: true,
      className: "dt-right",
      render: function (data, type, row) {
        // ✅ Para ordenar/filtrar usamos el número "real"
        if (type === 'sort' || type === 'type') return parseFloat(data || 0);
        // ✅ Para mostrar: 4 decimales
        let n = parseFloat(data || 0);
        return n.toFixed(4);
      }
    },
    { targets: 6, width: "100px", orderable: true, className: "dt-center" }   // Estado
  ],

  buttons: [
    'copyHtml5',
    'csvHtml5',
    'pdf',
    {
      text: '<span class="glyphicon glyphicon-save-file"></span> DESCARGAR EXCEL',
      action: function (e, dt, node, config) {
        desexcel();
      }
    }
  ],

  ajax: {
    url: 'data/articulo.php?op=listarcliente&idcliente=' + cliente,
    type: "POST",
    dataType: "json",
    error: function (e) {
      console.log(e.responseText);
    }
  },

  destroy: true,
  pageLength: 20
});

}

// ==============================
// BOTÓN: PRECIO LISTA (GENERAL) - SOLO PRODUCTO SELECCIONADO
// ==============================
$(document).on("click", "#btnPrecioLista", function () {

  // ✅ Debe existir producto seleccionado (se setea en mostrar())
  var idcliente = $("#idclienteproducto").val();
  var idproducto = $("#idproducto").val();

  if (!idcliente || idcliente === "0") {
    Swal.fire("Seleccione un cliente y luego edite un producto (Editar precio).");
    return;
  }
  if (!idproducto || idproducto === "0") {
    Swal.fire("Seleccione un producto (Editar precio) para poder regresarlo a precio lista.");
    return;
  }

  Swal.fire({
    title: "¿Regresar a PRECIO LISTA - GENERAL?",
    html: "Se actualizará <b>solo este producto</b> al <b>PRECIO LISTA - GENERAL</b> para este cliente.<br><br>¿Deseas continuar?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "CONFIRMAR",
    cancelButtonText: "CANCELAR",
    reverseButtons: true
  }).then((result) => {

    if (!result.isConfirmed) return;

    $.post(
      "data/articulo.php?op=precio_lista_general_producto_cliente",
      { idcliente: idcliente, idproducto: idproducto },
      function (resp) {

        try { resp = JSON.parse(resp); } catch (e) {}

        if (resp && resp.status) {
          // ✅ actualiza el campo precio en el formulario con 4 decimales
          if (resp.precio_lista !== undefined && resp.precio_lista !== null) {
            $("#precio").val(parseFloat(resp.precio_lista || 0).toFixed(4));
          }

          Swal.fire("OK", resp.message || "Actualizado correctamente.", "success");

          // ✅ refrescar la tabla sin perder página
          if ($.fn.DataTable.isDataTable('#tbllistado')) {
            $('#tbllistado').DataTable().ajax.reload(null, false);
          }

        } else {
          Swal.fire("Error", (resp && resp.message) ? resp.message : "No se pudo actualizar.", "error");
        }
      }
    ).fail(function (xhr) {
      Swal.fire("Error", "Error de servidor: " + xhr.status, "error");
      console.log(xhr.responseText);
    });

  });
});


function mostrar(txtCOD_ARTICULO, idcliente) {

  if (idcliente === null || idcliente === undefined || idcliente === "" || idcliente === 0 || idcliente === "0") {
    Swal.fire('Seleccione cliente y clic en filtrar');
    return;
  }

  // ✅ setea ids para usar en guardar y en el botón PRECIO LISTA
  $("#idclienteproducto").val(idcliente);
  $("#idproducto").val(txtCOD_ARTICULO);

  $.post(
    "data/articulo.php?op=mostrarcliente",
    { txtCOD_ARTICULO: txtCOD_ARTICULO, idcliente: idcliente }
  )
  .done(function (resp) {
    let data;

    try {
      data = (typeof resp === "object") ? resp : JSON.parse(resp);
    } catch (e) {
      console.error("Respuesta no es JSON válido:", resp);
      Swal.fire("Error", "El servidor devolvió una respuesta inválida (no JSON). Revisa articulo.php.", "error");
      return;
    }

    if (!data || data.error) {
      Swal.fire("Aviso", (data && data.error) ? data.error : "No se encontró información del artículo/cliente.", "warning");
      return;
    }

    mostrarform(true);

    // ✅ Precio con 4 decimales
    let p = parseFloat(data.preciocliente || 0);
    $("#precio").val(p.toFixed(4));

    $("#codigo").val(data.codigo ?? "");
    $("#txtDESCRIPCION_ARTICULO").val(data.txtDESCRIPCION_ARTICULO ?? "");

    $("#precio_porcentaje").val(data.precio_porcentaje ?? "");
    $("#precio_mayor").val(data.precio_mayor ?? "");

    $("#precio_porcentaje2").val(data.precio_porcentaje2 ?? "");
    $("#precio_mayor2").val(data.precio_mayor2 ?? "");

    $("#precio_porcentaje3").val(data.precio_porcentaje3 ?? "");
    $("#precioc").val(data.precio_compra ?? "");
    $("#oferta").val(data.preciooferta ?? "");
  })
  .fail(function (xhr) {
    console.error("Error AJAX:", xhr.responseText);
    Swal.fire("Error", "No se pudo obtener el artículo del servidor. Revisa consola/PHP.", "error");
  });
}


function actualizararticulo() {

  var formData2 = new FormData($("#formulario")[0]);

  // ✅ asegurar 4 decimales
  let precio = parseFloat($("#precio").val() || 0).toFixed(4);

  formData2.append('precio', precio);
  formData2.append('idproducto', $("#idproducto").val());
  formData2.append('idclienteproducto', $("#idclienteproducto").val());

  $.ajax({
    url: "data/articulo.php?op=guardaryeditarcliente",
    type: "POST",
    data: formData2,
    contentType: false,
    processData: false,
    dataType: 'json',
    success: function(datos){
      console.log(datos);

      // ✅ si tu backend retorna {status:true, mensaje:"..."} aprovecha:
      if (datos && (datos.status === true || datos.estado === true)) {
        Swal.fire("OK", datos.mensaje || "Guardado correctamente.", "success");
      } else {
        // si tu backend solo manda mensaje, igual mostramos
        Swal.fire(datos.mensaje || "Guardado.", "", "success");
      }

      // ✅ volver al listado
      mostrarform(false);

      // ✅ refrescar listado del cliente sin perder página
      if ($.fn.DataTable.isDataTable('#tbllistado')) {
        $('#tbllistado').DataTable().ajax.reload(null, false);
      } else {
        listar();
      }
    },
    error: function(xhr){
      console.log(xhr.responseText);
      Swal.fire("Error", "No se pudo guardar.", "error");
    }
  });

}



// In your Javascript (external .js resource or <script> tag)
$(document).ready(function() {
    $('.js-example-basic-single').select2(
        {
            placeholder: "-SELECCIONE CLIENTE-",
            ajax: {
                url: 'data/venta.php?op=buscarcliente&tipodoc=DNI3',

                type: "get",
			dataType: 'json',
			data: function (params) {	
                console.log(params);			
				return {
					searchTerm: params.term // search term
				};
			},
			processResults: function (response) {
                console.log(response);
				return {
					results: response
				};
			},
			cache: true
              }
        }
    );
});

$(document).ready(function() {
    $('.js-example-basic-single').select2({
        placeholder: "-SELECCIONE CLIENTE-",
        ajax: {
            url: 'data/venta.php?op=buscarcliente&tipodoc=DNI3',
            type: "get",
            dataType: 'json',
            data: function (params) {
                return { searchTerm: params.term };
            },
            processResults: function (response) {
                return { results: response };
            },
            cache: true
        }
    });
});

// 🔁 Al seleccionar un cliente, ejecutar lo mismo que FILTRAR
$('.js-example-basic-single').on('select2:select', function (e) {
    listar();
});

$('.js-example-basic-single').on('select2:select', function () {
    mostrarform(false);
    listar();
});

init();