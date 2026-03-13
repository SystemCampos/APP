<?php

if(!function_exists('caf_texto_simple')){
function caf_texto_simple($txt){
    $txt = trim((string)$txt);
    if($txt === ''){ return ''; }

    $map = array(
        'á'=>'a','à'=>'a','ä'=>'a','â'=>'a','ã'=>'a',
        'é'=>'e','è'=>'e','ë'=>'e','ê'=>'e',
        'í'=>'i','ì'=>'i','ï'=>'i','î'=>'i',
        'ó'=>'o','ò'=>'o','ö'=>'o','ô'=>'o','õ'=>'o',
        'ú'=>'u','ù'=>'u','ü'=>'u','û'=>'u',
        'Á'=>'a','À'=>'a','Ä'=>'a','Â'=>'a','Ã'=>'a',
        'É'=>'e','È'=>'e','Ë'=>'e','Ê'=>'e',
        'Í'=>'i','Ì'=>'i','Ï'=>'i','Î'=>'i',
        'Ó'=>'o','Ò'=>'o','Ö'=>'o','Ô'=>'o','Õ'=>'o',
        'Ú'=>'u','Ù'=>'u','Ü'=>'u','Û'=>'u',
        'ñ'=>'n','Ñ'=>'n'
    );

    $txt = strtr($txt, $map);
    $txt = strtolower($txt);
    $txt = preg_replace('/[^a-z0-9]+/', ' ', $txt);
    $txt = trim(preg_replace('/\s+/', ' ', $txt));

    return $txt;
}
}

if(!function_exists('caf_catalogo_base')){
function caf_catalogo_base(){
    return array(
        '10'=>array('codigo'=>'10','descripcion'=>'Gravado - Operación Onerosa','descripcion_corta'=>'Gravado Onerosa','cod_tributo'=>'1000','porcentaje_igv'=>'18.00','orden'=>10,'estado'=>'1'),
        '11'=>array('codigo'=>'11','descripcion'=>'Gravado - Retiro por premio','descripcion_corta'=>'Gravado Premio','cod_tributo'=>'9996','porcentaje_igv'=>'18.00','orden'=>11,'estado'=>'1'),
        '12'=>array('codigo'=>'12','descripcion'=>'Gravado - Retiro por donación','descripcion_corta'=>'Gravado Donación','cod_tributo'=>'9996','porcentaje_igv'=>'18.00','orden'=>12,'estado'=>'1'),
        '13'=>array('codigo'=>'13','descripcion'=>'Gravado - Retiro','descripcion_corta'=>'Gravado Retiro','cod_tributo'=>'9996','porcentaje_igv'=>'18.00','orden'=>13,'estado'=>'1'),
        '14'=>array('codigo'=>'14','descripcion'=>'Gravado - Retiro por publicidad','descripcion_corta'=>'Gravado Publicidad','cod_tributo'=>'9996','porcentaje_igv'=>'18.00','orden'=>14,'estado'=>'1'),
        '15'=>array('codigo'=>'15','descripcion'=>'Gravado - Bonificación','descripcion_corta'=>'Gravado Bonificación','cod_tributo'=>'9996','porcentaje_igv'=>'18.00','orden'=>15,'estado'=>'1'),
        '16'=>array('codigo'=>'16','descripcion'=>'Gravado - Retiro por entrega a trabajadores','descripcion_corta'=>'Gravado Trabajadores','cod_tributo'=>'9996','porcentaje_igv'=>'18.00','orden'=>16,'estado'=>'1'),
        '17'=>array('codigo'=>'17','descripcion'=>'Gravado - IVAP','descripcion_corta'=>'IVAP','cod_tributo'=>'1016','porcentaje_igv'=>'4.00','orden'=>17,'estado'=>'1'),
        '20'=>array('codigo'=>'20','descripcion'=>'Exonerado - Operación Onerosa','descripcion_corta'=>'Exonerado Onerosa','cod_tributo'=>'9997','porcentaje_igv'=>'0.00','orden'=>20,'estado'=>'1'),
        '21'=>array('codigo'=>'21','descripcion'=>'Exonerado - Transferencia gratuita','descripcion_corta'=>'Exonerado Gratuita','cod_tributo'=>'9997','porcentaje_igv'=>'0.00','orden'=>21,'estado'=>'1'),
        '30'=>array('codigo'=>'30','descripcion'=>'Inafecto - Operación Onerosa','descripcion_corta'=>'Inafecto Onerosa','cod_tributo'=>'9998','porcentaje_igv'=>'0.00','orden'=>30,'estado'=>'1'),
        '31'=>array('codigo'=>'31','descripcion'=>'Inafecto - Retiro por bonificación','descripcion_corta'=>'Inafecto Bonificación','cod_tributo'=>'9998','porcentaje_igv'=>'0.00','orden'=>31,'estado'=>'1'),
        '32'=>array('codigo'=>'32','descripcion'=>'Inafecto - Retiro','descripcion_corta'=>'Inafecto Retiro','cod_tributo'=>'9998','porcentaje_igv'=>'0.00','orden'=>32,'estado'=>'1'),
        '33'=>array('codigo'=>'33','descripcion'=>'Inafecto - Retiro por muestra médica','descripcion_corta'=>'Inafecto Muestra médica','cod_tributo'=>'9998','porcentaje_igv'=>'0.00','orden'=>33,'estado'=>'1'),
        '34'=>array('codigo'=>'34','descripcion'=>'Inafecto - Retiro por convenio colectivo','descripcion_corta'=>'Inafecto Convenio colectivo','cod_tributo'=>'9998','porcentaje_igv'=>'0.00','orden'=>34,'estado'=>'1'),
        '35'=>array('codigo'=>'35','descripcion'=>'Inafecto - Retiro por premio','descripcion_corta'=>'Inafecto Premio','cod_tributo'=>'9998','porcentaje_igv'=>'0.00','orden'=>35,'estado'=>'1'),
        '36'=>array('codigo'=>'36','descripcion'=>'Inafecto - Retiro por publicidad','descripcion_corta'=>'Inafecto Publicidad','cod_tributo'=>'9998','porcentaje_igv'=>'0.00','orden'=>36,'estado'=>'1'),
        '40'=>array('codigo'=>'40','descripcion'=>'Exportación','descripcion_corta'=>'Exportación','cod_tributo'=>'9995','porcentaje_igv'=>'0.00','orden'=>40,'estado'=>'1'),
    );
}
}

if(!function_exists('caf_codigos_validos')){
function caf_codigos_validos(){
    return array_keys(caf_catalogo_base());
}
}

if(!function_exists('caf_extraer_codigo')){
function caf_extraer_codigo($valor, $default=''){
    $valor = trim((string)$valor);
    if($valor === ''){ return $default; }

    if(in_array($valor, caf_codigos_validos(), true)){
        return $valor;
    }

    if(preg_match('/(?:^|[^0-9])(10|11|12|13|14|15|16|17|20|21|30|31|32|33|34|35|36|40)(?:[^0-9]|$)/', $valor, $m)){
        return $m[1];
    }

    $simple = caf_texto_simple($valor);
    if($simple === ''){ return $default; }

    foreach(caf_catalogo_base() as $cod => $it){
        $cmp1 = caf_texto_simple($it['descripcion']);
        $cmp2 = caf_texto_simple($it['descripcion_corta']);
        $cmp3 = caf_texto_simple($it['descripcion'].' '.$it['codigo']);
        if($simple === $cmp1 || $simple === $cmp2 || $simple === $cmp3){
            return $cod;
        }
    }

    return $default;
}
}

if(!function_exists('caf_tipo_to_codigo')){
function caf_tipo_to_codigo($tipo){
    $norm = caf_extraer_codigo($tipo, '');
    if($norm !== ''){ return $norm; }

    $tipo = trim((string)$tipo);
    if($tipo === '1'){ return '20'; }
    if($tipo === '2'){ return '35'; }
    if($tipo === '3'){ return '30'; }

    return '10';
}
}

if(!function_exists('caf_tasa_default_empresa')){
function caf_tasa_default_empresa($idempresa=0){
    $tasa = '18.00';
    $idempresa = (int)$idempresa;

    if($idempresa > 0 && function_exists('ejecutarConsultaSimpleFila')){
        try{
            $cfg = ejecutarConsultaSimpleFila("SELECT igv FROM config WHERE id='".$idempresa."' LIMIT 1");
            if($cfg && isset($cfg['igv']) && trim((string)$cfg['igv']) !== ''){
                $tasa = number_format((float)$cfg['igv'], 2, '.', '');
            }
        }catch(Exception $e){
            $tasa = '18.00';
        }
    }

    if((float)$tasa <= 0){ $tasa = '18.00'; }
    return number_format((float)$tasa, 2, '.', '');
}
}

if(!function_exists('caf_catalogo_defaults_empresa')){
function caf_catalogo_defaults_empresa($idempresa=0){
    $base = caf_catalogo_base();
    $tasa = caf_tasa_default_empresa($idempresa);

    foreach($base as $cod => $row){
        if(in_array($cod, array('10','11','12','13','14','15','16'), true)){
            $base[$cod]['porcentaje_igv'] = $tasa;
        }
    }

    return $base;
}
}

if(!function_exists('caf_tabla_existe')){
function caf_tabla_existe(){
    static $ok = null;
    if($ok !== null){ return $ok; }

    if(!function_exists('ejecutarConsulta')){
        $ok = false;
        return $ok;
    }

    $rs = ejecutarConsulta("SHOW TABLES LIKE 'catalogo_afectaciones_sunat'");
    $ok = ($rs && isset($rs->num_rows) && (int)$rs->num_rows > 0);
    return $ok;
}
}

if(!function_exists('caf_columna_existe')){
function caf_columna_existe($tabla, $columna){
    static $cache = array();
    $key = $tabla.'|'.$columna;
    if(isset($cache[$key])){ return $cache[$key]; }

    if(!function_exists('ejecutarConsulta')){
        $cache[$key] = false;
        return false;
    }

    $tabla = limpiarCadena($tabla);
    $columna = limpiarCadena($columna);
    $rs = ejecutarConsulta("SHOW COLUMNS FROM `".$tabla."` LIKE '".$columna."'");
    $cache[$key] = ($rs && isset($rs->num_rows) && (int)$rs->num_rows > 0);
    return $cache[$key];
}
}

if(!function_exists('caf_get_default_row')){
function caf_get_default_row($codigo, $idempresa=0){
    $codigo = caf_tipo_to_codigo($codigo);
    $defaults = caf_catalogo_defaults_empresa($idempresa);

    return isset($defaults[$codigo]) ? $defaults[$codigo] : array(
        'codigo'=>$codigo,
        'descripcion'=>$codigo,
        'descripcion_corta'=>$codigo,
        'cod_tributo'=>'1000',
        'codigo_tributo'=>'1000',
        'porcentaje_igv'=>caf_tasa_default_empresa($idempresa),
        'porcentaje_impuesto'=>caf_tasa_default_empresa($idempresa),
        'es_onerosa'=>1,
        'es_gratuito'=>0,
        'es_retiro'=>0,
        'es_publicidad'=>0,
        'es_bonificacion'=>0,
        'es_exportacion'=>0,
        'requiere_valor_referencial'=>0,
        'codigo_tipo_precio'=>'01',
        'orden'=>999,
        'estado'=>'1'
    );
}
}

if(!function_exists('caf_descripcion_corta_default')){
function caf_descripcion_corta_default($codigo, $idempresa=0){
    $def = caf_get_default_row($codigo, $idempresa);
    return isset($def['descripcion_corta']) ? trim((string)$def['descripcion_corta']) : trim((string)$def['descripcion']);
}
}

if(!function_exists('caf_ensure_catalogo_empresa')){
function caf_ensure_catalogo_empresa($idempresa=0){
    $idempresa = (int)$idempresa;
    if($idempresa <= 0 || !caf_tabla_existe() || !function_exists('ejecutarConsulta')){ return false; }

    $row = ejecutarConsultaSimpleFila("SELECT COUNT(*) AS c FROM catalogo_afectaciones_sunat WHERE idempresa='".$idempresa."'");
    $cnt = ($row && isset($row['c'])) ? (int)$row['c'] : 0;
    if($cnt > 0){ return true; }

    $usaDescCorta = caf_columna_existe('catalogo_afectaciones_sunat', 'descripcion_corta');
    $colCodigo = caf_columna_existe('catalogo_afectaciones_sunat','codigo_afectacion_igv') ? 'codigo_afectacion_igv' : 'codigo';
    $defaults = caf_catalogo_defaults_empresa($idempresa);

    foreach($defaults as $it){
        $codigo = $it['codigo'];
        $descripcion = limpiarCadena($it['descripcion']);
        $descripcionCorta = limpiarCadena($it['descripcion_corta']);
        $codTrib = $it['cod_tributo'];
        $pct = $it['porcentaje_igv'];
        $orden = (int)$it['orden'];

        $colCodigo = caf_columna_existe('catalogo_afectaciones_sunat','codigo_afectacion_igv') ? 'codigo_afectacion_igv' : 'codigo';
        $colCodTrib = caf_columna_existe('catalogo_afectaciones_sunat','codigo_tributo') ? 'codigo_tributo' : 'cod_tributo';
        $colPct = caf_columna_existe('catalogo_afectaciones_sunat','porcentaje_impuesto') ? 'porcentaje_impuesto' : 'porcentaje_igv';
        if($usaDescCorta){
            $sql = "INSERT INTO catalogo_afectaciones_sunat (idempresa,".$colCodigo.",descripcion,descripcion_corta,".$colCodTrib.",".$colPct.",orden,estado)
                    VALUES ('".$idempresa."','".$codigo."','".$descripcion."','".$descripcionCorta."','".$codTrib."','".$pct."','".$orden."','1')";
        }else{
            $sql = "INSERT INTO catalogo_afectaciones_sunat (idempresa,".$colCodigo.",descripcion,".$colCodTrib.",".$colPct.",orden,estado)
                    VALUES ('".$idempresa."','".$codigo."','".$descripcion."','".$codTrib."','".$pct."','".$orden."','1')";
        }
        ejecutarConsulta($sql);
    }

    return true;
}
}

if(!function_exists('caf_catalogo_listar')){
function caf_catalogo_listar($idempresa=0, $soloActivos=false){
    $idempresa = (int)$idempresa;
    $defaults = caf_catalogo_defaults_empresa($idempresa);

    if($idempresa > 0 && caf_tabla_existe()){
        caf_ensure_catalogo_empresa($idempresa);
        $usaDescCorta = caf_columna_existe('catalogo_afectaciones_sunat', 'descripcion_corta');

        $where = "WHERE idempresa='".$idempresa."'";
        if($soloActivos){ $where .= " AND estado='1'"; }

        $colId = caf_columna_existe('catalogo_afectaciones_sunat','idcatalogo_afectacion') ? 'idcatalogo_afectacion' : 'id';
        $colCodigo = caf_columna_existe('catalogo_afectaciones_sunat','codigo_afectacion_igv') ? 'codigo_afectacion_igv' : 'codigo';
        $colCodTrib = caf_columna_existe('catalogo_afectaciones_sunat','codigo_tributo') ? 'codigo_tributo' : 'cod_tributo';
        $colPct = caf_columna_existe('catalogo_afectaciones_sunat','porcentaje_impuesto') ? 'porcentaje_impuesto' : 'porcentaje_igv';
        $colOn = caf_columna_existe('catalogo_afectaciones_sunat','es_onerosa') ? 'es_onerosa' : '1';
        $colGra = caf_columna_existe('catalogo_afectaciones_sunat','es_gratuito') ? 'es_gratuito' : '0';
        $colReqRef = caf_columna_existe('catalogo_afectaciones_sunat','requiere_valor_referencial') ? 'requiere_valor_referencial' : '0';
        $colTipoPrecio = caf_columna_existe('catalogo_afectaciones_sunat','codigo_tipo_precio') ? 'codigo_tipo_precio' : "'01'";
        $colRet = caf_columna_existe('catalogo_afectaciones_sunat','es_retiro') ? 'es_retiro' : '0';
        $colPub = caf_columna_existe('catalogo_afectaciones_sunat','es_publicidad') ? 'es_publicidad' : '0';
        $colBon = caf_columna_existe('catalogo_afectaciones_sunat','es_bonificacion') ? 'es_bonificacion' : '0';
        $colExp = caf_columna_existe('catalogo_afectaciones_sunat','es_exportacion') ? 'es_exportacion' : '0';
        $cols = $colId." AS id,idempresa,".$colCodigo." AS codigo,descripcion,".($usaDescCorta ? "descripcion_corta," : "'' AS descripcion_corta,").$colCodTrib." AS cod_tributo,".$colPct." AS porcentaje_igv,".$colOn." AS es_onerosa,".$colGra." AS es_gratuito,".$colRet." AS es_retiro,".$colPub." AS es_publicidad,".$colBon." AS es_bonificacion,".$colExp." AS es_exportacion,".$colReqRef." AS requiere_valor_referencial,".$colTipoPrecio." AS codigo_tipo_precio,orden,estado";
        $sql = "SELECT ".$cols." FROM catalogo_afectaciones_sunat ".$where." ORDER BY orden ASC, $colCodigo ASC";
        $rs = ejecutarConsulta($sql);
        $out = array();

        if($rs){
            while($row = $rs->fetch_assoc()){
                $cod = caf_tipo_to_codigo(isset($row['codigo']) ? $row['codigo'] : '');
                $def = isset($defaults[$cod]) ? $defaults[$cod] : null;
                $desc = isset($row['descripcion']) && trim((string)$row['descripcion']) !== '' ? $row['descripcion'] : ($def ? $def['descripcion'] : $cod);
                $descCorta = isset($row['descripcion_corta']) && trim((string)$row['descripcion_corta']) !== '' ? $row['descripcion_corta'] : ($def ? $def['descripcion_corta'] : $desc);

                $out[] = array(
                    'id' => isset($row['id']) ? (int)$row['id'] : 0,
                    'idempresa' => isset($row['idempresa']) ? (int)$row['idempresa'] : $idempresa,
                    'codigo' => $cod,
                    'descripcion' => $desc,
                    'descripcion_corta' => $descCorta,
                    'cod_tributo' => isset($row['cod_tributo']) && trim((string)$row['cod_tributo']) !== '' ? trim((string)$row['cod_tributo']) : ($def ? $def['cod_tributo'] : '1000'),
                    'porcentaje_igv' => number_format((float)(isset($row['porcentaje_igv']) ? $row['porcentaje_igv'] : ($def ? $def['porcentaje_igv'] : '0.00')), 2, '.', ''),
                    'orden' => isset($row['orden']) ? (int)$row['orden'] : ($def ? (int)$def['orden'] : 0),
                    'estado' => isset($row['estado']) ? (string)$row['estado'] : '1',
                    'codigo_tributo' => isset($row['cod_tributo']) ? trim((string)$row['cod_tributo']) : '',
                    'porcentaje_impuesto' => number_format((float)(isset($row['porcentaje_igv']) ? $row['porcentaje_igv'] : 0), 2, '.', ''),
                    'es_onerosa' => isset($row['es_onerosa']) ? (int)$row['es_onerosa'] : 1,
                    'es_gratuito' => isset($row['es_gratuito']) ? (int)$row['es_gratuito'] : 0,
                    'es_retiro' => isset($row['es_retiro']) ? (int)$row['es_retiro'] : 0,
                    'es_publicidad' => isset($row['es_publicidad']) ? (int)$row['es_publicidad'] : 0,
                    'es_bonificacion' => isset($row['es_bonificacion']) ? (int)$row['es_bonificacion'] : 0,
                    'es_exportacion' => isset($row['es_exportacion']) ? (int)$row['es_exportacion'] : 0,
                    'requiere_valor_referencial' => isset($row['requiere_valor_referencial']) ? (int)$row['requiere_valor_referencial'] : 0,
                    'codigo_tipo_precio' => isset($row['codigo_tipo_precio']) ? (string)$row['codigo_tipo_precio'] : '01',
                );
            }
        }

        if(!empty($out)){ return $out; }
    }

    $out = array();
    foreach($defaults as $it){
        if($soloActivos && (string)$it['estado'] !== '1'){ continue; }
        $out[] = array(
            'id'=>0,
            'idempresa'=>$idempresa,
            'codigo'=>$it['codigo'],
            'descripcion'=>$it['descripcion'],
            'descripcion_corta'=>$it['descripcion_corta'],
            'cod_tributo'=>$it['cod_tributo'],
            'porcentaje_igv'=>number_format((float)$it['porcentaje_igv'], 2, '.', ''),
            'orden'=>(int)$it['orden'],
            'codigo_tributo'=>isset($it['codigo_tributo']) ? (string)$it['codigo_tributo'] : (string)$it['cod_tributo'],
            'porcentaje_impuesto'=>isset($it['porcentaje_impuesto']) ? number_format((float)$it['porcentaje_impuesto'], 2, '.', '') : number_format((float)$it['porcentaje_igv'], 2, '.', ''),
            'es_onerosa'=>isset($it['es_onerosa']) ? (int)$it['es_onerosa'] : 1,
            'es_gratuito'=>isset($it['es_gratuito']) ? (int)$it['es_gratuito'] : 0,
            'es_retiro'=>isset($it['es_retiro']) ? (int)$it['es_retiro'] : 0,
            'es_publicidad'=>isset($it['es_publicidad']) ? (int)$it['es_publicidad'] : 0,
            'es_bonificacion'=>isset($it['es_bonificacion']) ? (int)$it['es_bonificacion'] : 0,
            'es_exportacion'=>isset($it['es_exportacion']) ? (int)$it['es_exportacion'] : 0,
            'requiere_valor_referencial'=>isset($it['requiere_valor_referencial']) ? (int)$it['requiere_valor_referencial'] : 0,
            'codigo_tipo_precio'=>isset($it['codigo_tipo_precio']) ? (string)$it['codigo_tipo_precio'] : '01',
            'estado'=>(string)$it['estado'],
            'codigo_tributo'=>(string)$it['cod_tributo'],
            'porcentaje_impuesto'=>number_format((float)$it['porcentaje_igv'], 2, '.', ''),
            'es_onerosa'=>in_array((string)$it['codigo'], array('11','12','13','14','15','16','21','31','32','33','34','35','36'), true) ? 0 : 1,
            'es_gratuito'=>in_array((string)$it['codigo'], array('11','12','13','14','15','16','21','31','32','33','34','35','36'), true) ? 1 : 0,
            'es_retiro'=>in_array((string)$it['codigo'], array('11','12','13','14','15','16','31','32','33','34','35','36'), true) ? 1 : 0,
            'es_publicidad'=>in_array((string)$it['codigo'], array('14','36'), true) ? 1 : 0,
            'es_bonificacion'=>in_array((string)$it['codigo'], array('15','31'), true) ? 1 : 0,
            'es_exportacion'=>((string)$it['codigo']==='40') ? 1 : 0,
            'requiere_valor_referencial'=>in_array((string)$it['codigo'], array('11','12','13','14','15','16','21','31','32','33','34','35','36'), true) ? 1 : 0,
            'codigo_tipo_precio'=>in_array((string)$it['codigo'], array('11','12','13','14','15','16','21','31','32','33','34','35','36'), true) ? '02' : '01',
        );
    }

    usort($out, function($a,$b){
        if((int)$a['orden'] === (int)$b['orden']){ return strcmp($a['codigo'], $b['codigo']); }
        return ((int)$a['orden'] < (int)$b['orden']) ? -1 : 1;
    });

    return $out;
}
}

if(!function_exists('caf_get_codigo_conf')){
function caf_get_codigo_conf($codigo, $idempresa=0){
    $codigo = caf_tipo_to_codigo($codigo);
    $def = caf_get_default_row($codigo, $idempresa);
    $items = caf_catalogo_listar($idempresa, false);

    foreach($items as $it){
        if((string)$it['codigo'] === (string)$codigo){
            return $it;
        }
    }

    return $def;
}
}

if(!function_exists('caf_options_html')){
function caf_options_html($selected='10', $idempresa=0, $soloActivos=true){
    $selected = caf_tipo_to_codigo($selected);
    $items = caf_catalogo_listar($idempresa, $soloActivos);
    $html = '';

    foreach($items as $it){
        $cod = trim((string)$it['codigo']);
        $desc = trim((string)$it['descripcion']);
        $descCorta = trim((string)$it['descripcion_corta']);
        $codTrib = trim((string)$it['cod_tributo']);
        $pct = number_format((float)$it['porcentaje_igv'], 2, '.', '');
        $sel = ($selected === $cod) ? ' selected' : '';
        $html .= '<option value="'.$cod.'" data-codigo="'.$cod.'" data-descripcion="'.htmlspecialchars($desc, ENT_QUOTES, 'UTF-8').'" data-descripcion-corta="'.htmlspecialchars($descCorta, ENT_QUOTES, 'UTF-8').'" data-cod-tributo="'.$codTrib.'" data-porcentaje-igv="'.$pct.'"'.$sel.'>'.$descCorta.' ('.$cod.')</option>';
    }

    return $html;
}
}

if(!function_exists('caf_catalogo_js_activo')){
function caf_catalogo_js_activo($idempresa=0){
    $items = caf_catalogo_listar($idempresa, true);
    $out = array();

    foreach($items as $it){
        $out[] = array(
            'codigo'=>(string)$it['codigo'],
            'descripcion'=>(string)$it['descripcion'],
            'descripcion_corta'=>(string)$it['descripcion_corta'],
            'cod_tributo'=>(string)$it['cod_tributo'],
            'porcentaje_igv'=>number_format((float)$it['porcentaje_igv'], 2, '.', ''),
            'orden'=>(int)$it['orden'],
            'codigo_tributo'=>isset($it['codigo_tributo']) ? (string)$it['codigo_tributo'] : (string)$it['cod_tributo'],
            'porcentaje_impuesto'=>isset($it['porcentaje_impuesto']) ? number_format((float)$it['porcentaje_impuesto'], 2, '.', '') : number_format((float)$it['porcentaje_igv'], 2, '.', ''),
            'es_onerosa'=>isset($it['es_onerosa']) ? (int)$it['es_onerosa'] : 1,
            'es_gratuito'=>isset($it['es_gratuito']) ? (int)$it['es_gratuito'] : 0,
            'es_retiro'=>isset($it['es_retiro']) ? (int)$it['es_retiro'] : 0,
            'es_publicidad'=>isset($it['es_publicidad']) ? (int)$it['es_publicidad'] : 0,
            'es_bonificacion'=>isset($it['es_bonificacion']) ? (int)$it['es_bonificacion'] : 0,
            'es_exportacion'=>isset($it['es_exportacion']) ? (int)$it['es_exportacion'] : 0,
            'requiere_valor_referencial'=>isset($it['requiere_valor_referencial']) ? (int)$it['requiere_valor_referencial'] : 0,
            'codigo_tipo_precio'=>isset($it['codigo_tipo_precio']) ? (string)$it['codigo_tipo_precio'] : '01',
        );
    }

    return $out;
}
}
?>
