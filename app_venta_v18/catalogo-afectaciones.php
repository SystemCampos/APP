<?php
require 'config/conexion.php';
require_once 'funcionesGlobales/catalogo_afectaciones.php';
$idempresa = isset($_COOKIE['id']) ? (int)$_COOKIE['id'] : 0;
if($idempresa<=0){ die('Empresa no definida.'); }
caf_ensure_catalogo_empresa($idempresa);
$colId = caf_columna_existe('catalogo_afectaciones_sunat','idcatalogo_afectacion') ? 'idcatalogo_afectacion' : 'id';
$colCodigo = caf_columna_existe('catalogo_afectaciones_sunat','codigo_afectacion_igv') ? 'codigo_afectacion_igv' : 'codigo';

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save']) && is_array($_POST['row'])){
    foreach($_POST['row'] as $id=>$r){
        $id=(int)$id; if($id<=0){ continue; }
        $descripcion = isset($r['descripcion']) ? limpiarCadena($r['descripcion']) : '';
        $descripcion_corta = isset($r['descripcion_corta']) ? limpiarCadena($r['descripcion_corta']) : '';
        $orden = isset($r['orden']) ? (int)$r['orden'] : 0;
        $estado = isset($r['estado']) ? (int)$r['estado'] : 0;
        $codigo_tributo = isset($r['codigo_tributo']) ? limpiarCadena($r['codigo_tributo']) : '';
        $porcentaje_impuesto = isset($r['porcentaje_impuesto']) ? number_format((float)$r['porcentaje_impuesto'],4,'.','') : '0.0000';
        $codigo_tipo_precio = isset($r['codigo_tipo_precio']) ? limpiarCadena($r['codigo_tipo_precio']) : '01';
        $es_onerosa = isset($r['es_onerosa']) ? 1 : 0;
        $es_gratuito = isset($r['es_gratuito']) ? 1 : 0;
        $requiere_valor_referencial = isset($r['requiere_valor_referencial']) ? 1 : 0;

        $sets=[];
        if(caf_columna_existe('catalogo_afectaciones_sunat','descripcion')) $sets[]="descripcion='$descripcion'";
        if(caf_columna_existe('catalogo_afectaciones_sunat','descripcion_corta')) $sets[]="descripcion_corta='$descripcion_corta'";
        if(caf_columna_existe('catalogo_afectaciones_sunat','orden')) $sets[]="orden='$orden'";
        if(caf_columna_existe('catalogo_afectaciones_sunat','estado')) $sets[]="estado='$estado'";
        if(caf_columna_existe('catalogo_afectaciones_sunat','codigo_tributo')) $sets[]="codigo_tributo='$codigo_tributo'";
        if(caf_columna_existe('catalogo_afectaciones_sunat','porcentaje_impuesto')) $sets[]="porcentaje_impuesto='$porcentaje_impuesto'";
        if(caf_columna_existe('catalogo_afectaciones_sunat','codigo_tipo_precio')) $sets[]="codigo_tipo_precio='$codigo_tipo_precio'";
        if(caf_columna_existe('catalogo_afectaciones_sunat','es_onerosa')) $sets[]="es_onerosa='$es_onerosa'";
        if(caf_columna_existe('catalogo_afectaciones_sunat','es_gratuito')) $sets[]="es_gratuito='$es_gratuito'";
        if(caf_columna_existe('catalogo_afectaciones_sunat','requiere_valor_referencial')) $sets[]="requiere_valor_referencial='$requiere_valor_referencial'";
        if(caf_columna_existe('catalogo_afectaciones_sunat','fecha_modifica')) $sets[]="fecha_modifica=NOW()";

        if(!empty($sets)){
$sql="UPDATE catalogo_afectaciones_sunat SET ".implode(',', $sets)." WHERE ".$colId."='$id' AND idempresa='$idempresa' LIMIT 1";
            ejecutarConsulta($sql);
        }
    }
    header('Location: catalogo-afectaciones.php?ok=1');
    exit;
}

$items = caf_catalogo_listar($idempresa, false);
?>
<!DOCTYPE html>
<html lang="es">
<head><?php require 'includes/header.php'; ?></head>
<body class="page-body page-fade">
<?php require 'includes/sup.php'; ?>
<div class="row"><div class="col-sm-12"><div class="panel panel-primary">
<div class="panel-heading"><div class="panel-title">Mantenimiento Catálogo Afectaciones SUNAT</div></div>
<div class="panel-body">
<?php if(isset($_GET['ok'])): ?><div class="alert alert-success">Catálogo actualizado.</div><?php endif; ?>
<form method="post">
<div class="table-responsive"><table class="table table-bordered table-striped">
<thead><tr><th>Código</th><th>Descripción</th><th>Corta</th><th>Tributo</th><th>%</th><th>TipoPrecio</th><th>Onerosa</th><th>Gratuito</th><th>Req.Ref</th><th>Orden</th><th>Estado</th></tr></thead>
<tbody>
<?php foreach($items as $it): $id=(int)$it['id']; if($id<=0) continue; ?>
<tr>
<td><?= htmlspecialchars($it['codigo']) ?></td>
<td><input class="form-control" name="row[<?= $id ?>][descripcion]" value="<?= htmlspecialchars($it['descripcion']) ?>"></td>
<td><input class="form-control" name="row[<?= $id ?>][descripcion_corta]" value="<?= htmlspecialchars($it['descripcion_corta']) ?>"></td>
<td><input class="form-control" name="row[<?= $id ?>][codigo_tributo]" value="<?= htmlspecialchars(isset($it['codigo_tributo'])?$it['codigo_tributo']:$it['cod_tributo']) ?>"></td>
<td><input class="form-control" name="row[<?= $id ?>][porcentaje_impuesto]" value="<?= htmlspecialchars(isset($it['porcentaje_impuesto'])?$it['porcentaje_impuesto']:$it['porcentaje_igv']) ?>"></td>
<td><input class="form-control" name="row[<?= $id ?>][codigo_tipo_precio]" value="<?= htmlspecialchars(isset($it['codigo_tipo_precio'])?$it['codigo_tipo_precio']:'01') ?>"></td>
<td><input type="checkbox" name="row[<?= $id ?>][es_onerosa]" value="1" <?= (!isset($it['es_onerosa']) || (int)$it['es_onerosa']===1)?'checked':'' ?>></td>
<td><input type="checkbox" name="row[<?= $id ?>][es_gratuito]" value="1" <?= (isset($it['es_gratuito']) && (int)$it['es_gratuito']===1)?'checked':'' ?>></td>
<td><input type="checkbox" name="row[<?= $id ?>][requiere_valor_referencial]" value="1" <?= (isset($it['requiere_valor_referencial']) && (int)$it['requiere_valor_referencial']===1)?'checked':'' ?>></td>
<td><input class="form-control" name="row[<?= $id ?>][orden]" value="<?= (int)$it['orden'] ?>"></td>
<td><select class="form-control" name="row[<?= $id ?>][estado]"><option value="1" <?= ((string)$it['estado']==='1')?'selected':'' ?>>Activo</option><option value="0" <?= ((string)$it['estado']!=='1')?'selected':'' ?>>Inactivo</option></select></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<button class="btn btn-primary" type="submit" name="save" value="1">Guardar cambios</button>
<a href="venta.php" class="btn btn-default">Volver a Venta</a>
</form>
</div></div></div></div>
</body></html>
