

$(document).ready(function(){
    $("#ruta").on('change', function () {
        $("#ruta option:selected").each(function () {
            var id_category = $(this).val();
            $.post("data/venta.php?op=destinotransporte", { id_category: id_category }, function(data) {
                $("#destino").html(data);
            });			
        });
   });
});

