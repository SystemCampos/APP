<?php
/**
 * ============================================================
 *  app/webservices/receive.php (PHP 7.3)
 * ============================================================
 *  ENDPOINT: /webservices/receive.php?op=venta
 *
 *  IMPORTANTE (ENTORNO):
 *   - config.tipo: 3 = BETA / PRUEBAS, 1 = PRODUCCION
 *   - Se guarda en venta.beta / detalle_venta.beta / caja_ventapago.beta
 *
 *  NOTAS:
 *   - Mantiene OBSERVACION, guías 1..5, y tu flujo actual.
 *   - Cálculos de detalle: usa precio INC IGV y calcula base/igv/total
 *     consistente para evitar errores SUNAT (3271/3277/3103).
 * ============================================================
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS, GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

date_default_timezone_set('America/Lima');

$BASE_PATH = dirname(__DIR__);
require_once $BASE_PATH . '/config/global.php';
require_once $BASE_PATH . '/config/conexion.php';
require_once $BASE_PATH . '/modelos/numeros-letras.php';
require_once $BASE_PATH . '/modelos/envio.php';

// ------------------------------ JSON helpers ------------------------------
function j_out($ok, $arr = [], $http = 200){
  http_response_code($http);
  $base = ["ok" => (bool)$ok];
  echo json_encode(array_merge($base, $arr), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}
function out_err($mensaje, $extra = [], $http = 200){
  j_out(false, array_merge(["estado"=>"0","mensaje"=>$mensaje], $extra), $http);
}
function out_ok($arr = [], $http = 200){ j_out(true, $arr, $http); }

// ------------------------------ basic utils ------------------------------
function getv($a, $k, $d=null){ return (is_array($a) && array_key_exists($k,$a)) ? $a[$k] : $d; }
function to_str($v,$d=""){ if($v===null) return $d; return trim((string)$v); }
function to_int($v,$d=0){ if($v===null||$v==="") return (int)$d; return (int)$v; }
function to_dec($v,$d=0.0){
  if($v===null||$v==="") return (float)$d;
  if(is_string($v)){ $v=str_replace(",",".",$v); $v=str_replace(" ","",$v); }
  return (float)$v;
}
function esc($s){
  if(function_exists("limpiarCadena")) return limpiarCadena($s);
  return trim((string)$s);
}
function fmt2($n){ return number_format((float)$n, 2, '.', ''); }
function fmt4($n){ return number_format((float)$n, 4, '.', ''); }
function now_dt(){ return date('Y-m-d H:i:s'); }

function db_err(){
  if(isset($GLOBALS["conexion"]) && is_object($GLOBALS["conexion"]) && function_exists("mysqli_error")){
    $e = @mysqli_error($GLOBALS["conexion"]);
    if($e) return $e;
  }
  return "SQL error";
}
function q($sql){
  $r = ejecutarConsulta($sql);
  if($r === false){
    out_err("Error SQL", ["sql"=>$sql, "mysql_error"=>db_err()]);
  }
  return $r;
}
function q1($sql){
  $r = ejecutarConsultaSimpleFila($sql);
  return $r ? $r : null;
}
function qid($sql){
  $id = ejecutarConsulta_retornarID($sql);
  if(!$id){
    out_err("No se pudo insertar (ID no retornado)", ["sql"=>$sql, "mysql_error"=>db_err()]);
  }
  return (int)$id;
}

// ------------------------------ password check ------------------------------
function pass_ok($input, $stored){
  $input  = trim((string)$input);
  $stored = trim((string)$stored);
  if($stored === $input) return true;

  // stored base64
  $decodedStored = base64_decode($stored, true);
  if($decodedStored !== false && trim($decodedStored) === $input) return true;

  // input base64
  $decodedInput = base64_decode($input, true);
  if($decodedInput !== false && $stored === trim($decodedInput)) return true;

  // bcrypt
  if(strlen($stored) >= 55 && (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0 || strpos($stored, '$2b$') === 0)){
    return password_verify($input, $stored);
  }

  // md5/sha1/sha256 hex
  if(preg_match('/^[a-f0-9]{32}$/i', $stored)) return (md5($input) === strtolower($stored));
  if(preg_match('/^[a-f0-9]{40}$/i', $stored)) return (sha1($input) === strtolower($stored));
  if(preg_match('/^[a-f0-9]{64}$/i', $stored)) return (hash('sha256', $input) === strtolower($stored));

  return false;
}

// ------------------------------ currency / tc ------------------------------
function money_code($m){
  $m = strtoupper(trim((string)$m));
  if($m === "") return "PEN";
  if($m === "S/" || $m === "SOL" || $m === "SOLES") return "PEN";
  if($m === "$" || $m === "DOLAR" || $m === "DOLARES" || $m === "US$") return "USD";
  return $m;
}

/**
 * Busca TC USD:
 *  - empresa (idempresa = CodEmpresa) o
 *  - GENERAL (idempresa=0 AND idusuario=0)
 *  - fecha <= $fechaYmd (si no existe exacto, toma el último anterior)
 */
function tc_usd_from_db($idempresa, $fechaYmd){
  $idempresa = (int)$idempresa;
  $fechaYmd = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaYmd) ? $fechaYmd : date('Y-m-d');

  $whereUsd = "(UPPER(tipo)='USD' OR UPPER(simbolo)='USD' OR UPPER(simbolo)='$' OR UPPER(simbolo)='US$' OR UPPER(nombre) LIKE '%DOLAR%')";

  $sql = "SELECT idempresa,idusuario,venta,cambio,fecha
          FROM tipo_cambio
          WHERE ($whereUsd)
            AND ( (idempresa=$idempresa) OR (idempresa=0 AND idusuario=0) )
            AND fecha <= '$fechaYmd'
          ORDER BY CASE WHEN idempresa=$idempresa THEN 0 ELSE 1 END, fecha DESC, id DESC
          LIMIT 1";
  $r = q1($sql);
  if(!$r){
    $sql2 = "SELECT idempresa,idusuario,venta,cambio,fecha
             FROM tipo_cambio
             WHERE ($whereUsd)
               AND ( (idempresa=$idempresa) OR (idempresa=0 AND idusuario=0) )
             ORDER BY CASE WHEN idempresa=$idempresa THEN 0 ELSE 1 END, fecha DESC, id DESC
             LIMIT 1";
    $r = q1($sql2);
  }
  if(!$r) return null;

  $venta  = isset($r["venta"])  ? (float)$r["venta"]  : 0.0;
  $cambio = isset($r["cambio"]) ? (float)$r["cambio"] : 0.0;
  $tc = ($venta > 0) ? $venta : $cambio;
  if($tc <= 0) return null;

  $src = ((int)$r["idempresa"] === 0) ? "GENERAL" : "EMPRESA";
  return ["tc"=>$tc, "fecha"=>($r["fecha"] ?? null), "src"=>$src];
}

// ------------------------------ persona ------------------------------
function ensure_persona($idempresa, $tipo_documento, $doc, $nombre, $direccion, $telefono, $email){
  $idempresa=(int)$idempresa;

  $tipo_documento = strtoupper(esc($tipo_documento));
  if($tipo_documento === "") $tipo_documento = "RUC";

  $doc = esc($doc);
  if($doc === "") $doc = "00000000000";

  $nombre = esc($nombre);
  if($nombre === "") $nombre = "CLIENTE VARIOS";

  $direccion = esc($direccion);
  $telefono  = esc($telefono);
  $email     = esc($email);

  $row = q1("SELECT idpersona FROM persona WHERE idempresa='$idempresa' AND txtID_CLIENTE='$doc' LIMIT 1");
  if($row && isset($row["idpersona"])){
    $idp=(int)$row["idpersona"];
    q("UPDATE persona SET
        nombre='$nombre',
        tipo_documento='$tipo_documento',
        direccion='$direccion',
        telefono='$telefono',
        email='$email',
        txtRAZON_SOCIAL='$nombre'
      WHERE idpersona='$idp' AND idempresa='$idempresa'
      LIMIT 1");
    return $idp;
  }

  $sql = "
    INSERT INTO persona(
      idempresa, vendedor, tipo_persona, codigo, descuentom,
      nombre, tipo_documento, txtID_CLIENTE, sector, direccion, pais, ciudad, lat, lon,
      telefono, email, email2, pass, txtRAZON_SOCIAL, descuento, cci, puntos, creditolimite, credito, obs, edad, venta_pago
    ) VALUES (
      '$idempresa', '0', 'CLIENTE', '', '',
      '$nombre', '$tipo_documento', '$doc', '0', '$direccion', 'PE', '', '', '',
      '$telefono', '$email', '', '', '$nombre', '0.00', '', '0', '0.00', '0.00', '', '00', '0'
    )
  ";
  return qid($sql);
}

// ------------------------------ categoria (nivel 0/1/3) ------------------------------
function ensure_categoria_nivel($idempresa, $nivel, $nombre){
  $idempresa=(int)$idempresa;
  $nivel=(int)$nivel;

  $nombre = trim((string)$nombre);
  if($nombre === "") $nombre = "GENERAL";
  $nombreSafe = esc($nombre);

  $row = q1("SELECT idcategoria FROM categoria WHERE idempresa='$idempresa' AND nivel='$nivel' AND nombre='$nombreSafe' LIMIT 1");
  if($row && isset($row["idcategoria"])) return (int)$row["idcategoria"];

  $ctaventas = "0102";
  $sql = "INSERT INTO categoria(idempresa,nivel,idnivel,nombre,descripcion,ctaventas,condicion)
          VALUES('$idempresa','$nivel','0','$nombreSafe',NULL,'$ctaventas','1')";
  return qid($sql);
}

// ------------------------------ articulo (PK manual) ------------------------------
function next_articulo_id_global(){
  $nx = q1("SELECT IFNULL(MAX(txtCOD_ARTICULO),0)+1 AS next_id FROM articulo LIMIT 1");
  return (int)($nx ? $nx["next_id"] : 1);
}
function ensure_articulo($idempresa,$idlocal,$codigo,$descripcion,$umedida,$idcategoria,$forced_id,$moneda_item,$moneda_doc,$precio_inc){
  $idempresa=(int)$idempresa;
  $idlocal=(int)$idlocal;

  $codigo=esc($codigo);
  $descripcion=esc($descripcion);

  $umedida=esc($umedida);
  if($umedida==="") $umedida="NIU";

  $idcategoria=(int)$idcategoria;
  if($idcategoria<=0) $idcategoria = 0;

  $forced_id=(int)$forced_id;

  $m = money_code($moneda_item);
  if($m==="") $m = money_code($moneda_doc);
  if($m==="") $m = "PEN";

  $row = q1("SELECT txtCOD_ARTICULO, moneda FROM articulo WHERE idempresa='$idempresa' AND codigo='$codigo' AND medida='$umedida' LIMIT 1");
  if($row && isset($row["txtCOD_ARTICULO"])) return (int)$row["txtCOD_ARTICULO"];

  if($forced_id>0){
    $chk=q1("SELECT txtCOD_ARTICULO FROM articulo WHERE txtCOD_ARTICULO='$forced_id' LIMIT 1");
    $idart = ($chk && isset($chk["txtCOD_ARTICULO"])) ? next_articulo_id_global() : $forced_id;
  }else{
    $idart = next_articulo_id_global();
  }

  $precio = round((float)$precio_inc, 2);

  $sql = "
    INSERT INTO articulo(
      txtCOD_ARTICULO, nivel, idempresa, idcategoria, marca, linea, sublinea, subfamilia,
      medida, sanitario, principioactivo, idlocal, idproveedor, codigo, codigosunat, existencia,
      txtDESCRIPCION_ARTICULO, stock, stockmin, stockmax,
      precio, preciooferta, moneda, precio_porcentaje, precio_compra, mayor,
      precio_porcentaje2, precio_mayor2, precio_porcentaje3, precio_mayor,
      exonerado_igv, comision, comisionm, comisionmp, bolsa,
      ctacompras, ctaventas, canje, canjepuntos, canjecobro,
      imagen, estado, condicion, resaltado
    ) VALUES (
      '$idart', '1', '$idempresa', '$idcategoria', '0', '0', '0', '0',
      '$umedida', '', '', '$idlocal', '0', '$codigo', '00000000', '02',
      '$descripcion', '0', '0', '0',
      '$precio', '0', '".esc($m)."', '0', '0', '3',
      '0', '0', '0', '0',
      '0', '0.00', '0', '0.00', '0',
      '', '', 'NO', '0', '0',
      '', '1', '1', '0'
    )
  ";
  q($sql);
  return (int)$idart;
}

// ------------------------------ numeración ------------------------------
function lock_key($idempresa,$idlocal,$tipodocumento,$serie){
  return "venta_num_".$idempresa."_".$idlocal."_".$tipodocumento."_".$serie;
}
function next_numero_venta($idempresa,$idlocal,$tipodocumento,$serie){
  $idempresa=(int)$idempresa; $idlocal=(int)$idlocal;
  $tipodocumento=esc($tipodocumento);
  $serie=esc($serie);

  $sql="SELECT IFNULL(MAX(CAST(txtNUMERO AS UNSIGNED)),0) AS mx
        FROM venta
        WHERE idempresa='$idempresa'
          AND idlocal='$idlocal'
          AND txtID_TIPO_DOCUMENTO='$tipodocumento'
          AND txtSERIE='$serie'
          AND txtNUMERO REGEXP '^[0-9]+$'";
  $r=q1($sql);
  $mx = ($r && isset($r["mx"])) ? (int)$r["mx"] : 0;
  $nx = $mx + 1;
  $nx_str = str_pad((string)$nx, 8, "0", STR_PAD_LEFT);
  return [$nx, $nx_str];
}

// ------------------------------ SUNAT cod normalize ------------------------------
function normalize_cod_sunat(&$sunat){
  if(!is_array($sunat)) return;
  $cod = getv($sunat,"cod_sunat", null);
  if($cod === null) return;

  $codStr = trim((string)$cod);
  if($codStr !== "" && preg_match('/^\d+$/', $codStr)) return;

  $txt = $codStr;
  $msj = trim((string)getv($sunat,"mensaje", getv($sunat,"msj_sunat","")));

  if(preg_match('/Error\s+en\s+la\s+linea:\s*\d+:\s*(\d+)/i', $txt, $m)){
    $sunat["cod_sunat"] = $m[1];
    if($msj==="") $sunat["mensaje"] = $txt;
    return;
  }
  if(preg_match('/(?:code|codigo)\s*[:=]\s*(\d+)/i', $txt, $m)){
    $sunat["cod_sunat"] = $m[1];
    if($msj==="") $sunat["mensaje"] = $txt;
    return;
  }
  $sunat["cod_sunat"] = "9999";
  if($msj==="") $sunat["mensaje"] = $txt;
}

// ============================================================
// MAIN
// ============================================================
$op = isset($_GET["op"]) ? $_GET["op"] : "";
if($op !== "venta"){ out_err("Operación no válida", ["op"=>$op]); }

$raw = file_get_contents("php://input");
$payload = json_decode($raw, true);
if(!is_array($payload)){ out_err("JSON vacío o inválido"); }

// Credenciales
$userObj   = getv($payload,"User",[]);
$userName  = to_str(getv($userObj,"UserName",""));
$pass      = to_str(getv($userObj,"Password",""));
$CodEmpresa= to_int(getv($userObj,"CodEmpresa",0),0);

if($userName==="" || $pass==="" || $CodEmpresa<=0){
  out_err("Faltan credenciales", ["detalle"=>"UserName/Password/CodEmpresa"]);
}

$idempresa = (int)$CodEmpresa;
$rucEmisor = to_str(getv($payload,"ruc",""));
$idlocal   = to_int(getv($payload,"idlocal",0),0);

if($rucEmisor==="") out_err("Falta ruc (emisor)");
if($idlocal<=0) out_err("Falta idlocal");

// validar empresa y leer entorno (tipo)
$conf = q1("SELECT id, ruc, tipo FROM config WHERE id='$idempresa' LIMIT 1");
if(!$conf || !isset($conf["id"])) out_err("Empresa no existe en config", ["CodEmpresa"=>$idempresa]);
if(trim((string)$conf["ruc"]) !== $rucEmisor){
  out_err("RUC no corresponde a CodEmpresa", ["config_ruc"=>$conf["ruc"], "payload_ruc"=>$rucEmisor]);
}

// validar usuario
$u = q1("SELECT idusuario, clave FROM usuario
         WHERE idempresa='$idempresa' AND idlocal='$idlocal'
           AND login='".esc($userName)."'
           AND condicion=1
         LIMIT 1");
if(!$u || !isset($u["idusuario"])) out_err("Credenciales inválidas", ["message"=>"Usuario no existe o inactivo"]);
if(!pass_ok($pass, (string)$u["clave"])) out_err("Credenciales inválidas", ["message"=>"Clave incorrecta"]);
$idusuarioAuth = (int)$u["idusuario"];

// entorno beta/producción para guardar en BD
$beta = ((int)($conf["tipo"] ?? 1) === 3) ? 3 : 1;

// Moneda/TC
$warnings = [];
$docMoneda = money_code(to_str(getv($payload,"txtCOD_MONEDA", getv($payload,"txtID_MONEDA","PEN"))));
$FECHA     = to_str(getv($payload,"FECHA", now_dt()));
$fecha_tc  = substr($FECHA,0,10);
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_tc)) $fecha_tc = date('Y-m-d');

$tipocambio = to_dec(getv($payload,"tipocambio",0),0);
if($docMoneda==="USD"){
  if($tipocambio<=0 || abs($tipocambio-1.000)<0.0005){
    $tc = tc_usd_from_db($idempresa, $fecha_tc);
    if($tc){
      $tipocambio = (float)$tc["tc"];
      $warnings[] = "TC obtenido desde tipo_cambio (".$tc["src"]."): ".$tipocambio." (fecha ".$tc["fecha"].")";
    } else {
      $tipocambio = 1.000;
      $warnings[] = "No existe TC USD en tipo_cambio (empresa ni general). Se usó fallback 1.000.";
    }
  }
  if($tipocambio<=0) out_err("Tipo de cambio inválido");
}else{
  if($tipocambio<=0) $tipocambio = 1.000;
}

// Cabecera importes
$SUB_TOTAL = to_dec(getv($payload,"SUB_TOTAL",0),0);
$IGV       = to_dec(getv($payload,"IGV",0),0);
$TOTAL     = to_dec(getv($payload,"TOTAL",0),0);
if($TOTAL<=0) out_err("TOTAL inválido");

$tipodocumento = to_str(getv($payload,"tipodocumento",""));
$txtSERIE      = to_str(getv($payload,"serie",""));
$txtNUMERO     = to_str(getv($payload,"numero",""));
if($tipodocumento==="" || $txtSERIE==="") out_err("Faltan datos del comprobante", ["detalle"=>"tipodocumento/serie"]);

$OBSERVACION = to_str(getv($payload,"OBSERVACION",""));
$orden       = to_str(getv($payload,"orden",""));
$referencia  = to_str(getv($payload,"referencia",""));
  // Defaults para columnas NOT NULL en algunos esquemas
  $presupuesto = to_str(getv($payload,'presupuesto',''));
  $condiciones = to_str(getv($payload,'condiciones',''));

// totales alternos
$referencial = to_dec(getv($payload,"TOTAL_GRAVADAS", getv($payload,"referencial",0)),0);
$gratuita    = to_dec(getv($payload,"gratuita",0),0);
$exonerado   = to_dec(getv($payload,"TOTAL_EXONERADOS",0),0);
$inafecta    = to_dec(getv($payload,"inafecto", getv($payload,"inafecta",0)),0);
$ICB         = to_dec(getv($payload,"ICBPER_TOTAL",0),0);

// NC/ND relación
$docmodifica_tipo = to_str(getv($payload,"TIPODOC_REL",""));
$docmodifica      = to_str(getv($payload,"SERIENUM_REL",""));
$modifica_motivo  = to_str(getv($payload,"modifica_motivo",""));
$modifica_motivod = to_str(getv($payload,"modifica_motivod",""));

$tipo_pago = strtoupper(to_str(getv($payload,"tipopago","CONTADO")));
if($tipo_pago!=="CREDITO") $tipo_pago="CONTADO";
$medio_pago = to_str(getv($payload,"medio_pago",""));
$idtipo_req = to_int(getv($payload,"idtipo",0),0);

// guías (venta exige 1..5)
$tipoguia  = to_str(getv($payload,"guia_tipo",""));
$guia      = to_str(getv($payload,"guia_serie",""));
$tipoguia2 = to_str(getv($payload,"guia_tipo2",""));
$guia2     = to_str(getv($payload,"guia_serie2",""));
$tipoguia3 = to_str(getv($payload,"guia_tipo3",""));
$guia3     = to_str(getv($payload,"guia_serie3",""));
$tipoguia4 = to_str(getv($payload,"guia_tipo4",""));
$guia4     = to_str(getv($payload,"guia_serie4",""));
$tipoguia5 = to_str(getv($payload,"guia_tipo5",""));
$guia5     = to_str(getv($payload,"guia_serie5",""));

if($tipodocumento==="07" || $tipodocumento==="08"){
  if($docmodifica_tipo==="" || $docmodifica==="" || $modifica_motivo==="" || $modifica_motivod===""){
    out_err("Faltan datos de relación (NC/ND)");
  }
}

// Detalle
$detalle = getv($payload,"detalle",[]);
if(!is_array($detalle) || count($detalle)===0) out_err("Falta detalle");

// ============================================================
// NORMALIZACIÓN DE DETALLE (precio INC IGV -> base/igv/total)
// ============================================================
$tol = 0.10;

// IGV % desde config.igv (si existe)
$igvPct = 0.18;
$faIgv = q1("SELECT igv FROM config WHERE id='$idempresa' LIMIT 1");
if($faIgv && isset($faIgv["igv"])){
  $igvPct = ((float)$faIgv["igv"])/100;
  if($igvPct <= 0) $igvPct = 0.18;
}
$factor = 1 + $igvPct;

$sum_base=0.0; $sum_igv=0.0; $sum_tot=0.0;
$detalle_norm=[];
foreach($detalle as $it){
  if(!is_array($it)) continue;

  $CODIGO=to_str(getv($it,"CODIGO",""));
  $DESCRIPCION=to_str(getv($it,"DESCRIPCION",""));
  if($CODIGO==="" || $DESCRIPCION==="") continue;

  $qty = (float)to_dec(getv($it,"CANTIDAD",0),0);
  if($qty <= 0) $qty = 1;

  $p_inc_raw = (float)to_dec(getv($it,"PRECIO",0),0); // precio inc igv
  $p_sin_raw = (float)to_dec(getv($it,"PRECIO_SIN_IGV",0),0); // opcional

  // Si el item trae SUB_TOTAL/IGV/IMPORTE, solo se usan si están completos y coherentes
  $base_in = (float)to_dec(getv($it,"SUB_TOTAL",0),0);
  $igv_in  = (float)to_dec(getv($it,"IGV",0),0);
  $tot_in  = (float)to_dec(getv($it,"IMPORTE",0),0);

  if($base_in > 0 && $tot_in > 0){
    $base = round($base_in, 2);
    $igvL = round($igv_in, 2);
    $tot  = round($tot_in, 2);
  }else{
    // Recomendación: VU sin IGV a 4 dec, base a 2 dec, igv a 2 dec, total a 2 dec.
    $vu = ($p_sin_raw>0) ? round($p_sin_raw, 4) : (($factor>0) ? round($p_inc_raw/$factor, 4) : 0);
    $base = round($vu*$qty, 2);
    $igvL = round($base*$igvPct, 2);
    $tot  = round($base + $igvL, 2);
  }

  $sum_base += $base;
  $sum_igv  += $igvL;
  $sum_tot  += $tot;

  $it["_qty"] = $qty;
  $it["_p_inc"] = $p_inc_raw;
  $it["_base"] = $base;
  $it["_igv"] = $igvL;
  $it["_tot"] = $tot;

  $detalle_norm[]=$it;
}

if(count($detalle_norm)===0) out_err("Detalle inválido");

// Validación de consistencia cabecera vs detalle
if(abs(round($sum_base,2)-round($SUB_TOTAL,2))>$tol) out_err("Inconsistencia SUB_TOTAL vs detalle", ["cabecera"=>fmt2($SUB_TOTAL),"detalle"=>fmt2($sum_base)]);
if(abs(round($sum_igv,2)-round($IGV,2))>$tol)       out_err("Inconsistencia IGV vs detalle", ["cabecera"=>fmt2($IGV),"detalle"=>fmt2($sum_igv)]);
if(abs(round($sum_tot,2)-round($TOTAL,2))>$tol)     out_err("Inconsistencia TOTAL vs detalle", ["cabecera"=>fmt2($TOTAL),"detalle"=>fmt2($sum_tot)]);

// ============================================================
// TRANSACCIÓN
// ============================================================
q("START TRANSACTION");
$lockAcquired=false;
$lockName = lock_key($idempresa,$idlocal,$tipodocumento,$txtSERIE);

try{
  // Persona (cliente)
  $tipodoc_cli   = to_str(getv($payload,"tipodoc","RUC"));
  $ruccliente    = to_str(getv($payload,"ruccliente","00000000000"));
  $nombre_cli    = to_str(getv($payload,"nombre","CLIENTE VARIOS"));
  $direccion_cli = to_str(getv($payload,"direccion",""));
  $correo_cli    = to_str(getv($payload,"correo",""));
  $telefono_cli  = to_str(getv($payload,"telefono",""));
  $idcliente = ensure_persona($idempresa, $tipodoc_cli, $ruccliente, $nombre_cli, $direccion_cli, $telefono_cli, $correo_cli);
  if($idcliente<=0){ q("ROLLBACK"); out_err("No se pudo registrar/obtener cliente"); }

  // Categorías opcionales (si llegan)
  $idgrupo=0; $idmarca=0; $idlinea=0;
  $first = $detalle_norm[0];
  $grupo = to_str(getv($first,"GRUPO",""));
  $marca = to_str(getv($first,"MARCA",""));
  $linea = to_str(getv($first,"LINEA",""));
  if($grupo!=="") $idgrupo = ensure_categoria_nivel($idempresa, 0, $grupo);
  if($marca!=="") $idmarca = ensure_categoria_nivel($idempresa, 1, $marca);
  if($linea!=="") $idlinea = ensure_categoria_nivel($idempresa, 3, $linea);

  // Numeración automática
  $needAuto = ($txtNUMERO==="" || $txtNUMERO==="00000000");
  if($needAuto){
    $lk = q1("SELECT GET_LOCK('".esc($lockName)."', 5) AS lk");
    if($lk && isset($lk["lk"]) && (int)$lk["lk"]===1) $lockAcquired=true;

    list($_nx_int, $_nx_str) = next_numero_venta($idempresa,$idlocal,$tipodocumento,$txtSERIE);
    $txtNUMERO = $_nx_str;
    $warnings[]="Numeración automática aplicada: ".$txtSERIE."-".$txtNUMERO." (max+1 por doc/serie/local)";
  } else {
    if(preg_match('/^[0-9]+$/',$txtNUMERO)){
      $txtNUMERO = str_pad($txtNUMERO, 8, "0", STR_PAD_LEFT);
    }
  }

  // Venta
  $pedido=0; $sector=0;
  $controlpresupuestal=to_str(getv($payload,"asientocontable",""));
  $exportacion= strtoupper(to_str(getv($payload,"exportacion","NO")));
  if($exportacion!=="SI") $exportacion="NO";

  $doc_relaciona=to_str(getv($payload,"doc_relaciona",""));
  $percepcion=to_dec(getv($payload,"percepcion",0),0);
  $retencion=to_dec(getv($payload,"retenciones", getv($payload,"retencion",0)),0);
  $iddetraccion=to_int(getv($payload,"iddetraccion",0),0);
  $detraccion=to_dec(getv($payload,"detraccion",0),0);
  $descuento=to_dec(getv($payload,"descuento",0),0);
  $comision=0.00; $tarjeta=0.00;

  $fecha_vto=to_str(getv($payload,"fecha_vto",""));
  if($fecha_vto==="") $fecha_vto = substr($FECHA,0,10);

  $hash_cpe=""; $hash_cdr=""; $mensaje="";
  $mach_id=""; $mach_numero=""; $mach_monto=0.00000; $mach_fecha="2020-01-01"; $mach_observaciones="";
  $estado=0;
  $estadopago = ($tipo_pago==="CONTADO") ? 1 : 0;
  $kardex=0; $idcaja=0;

  $sqlVenta="INSERT INTO venta (
      idempresa, beta, pedido, sector, controlpresupuestal, exportacion,
      txtID_CLIENTE, idusuario, idlocal, txtID_TIPO_DOCUMENTO,
      doc_relaciona, docmodifica_tipo, docmodifica, modifica_motivo, modifica_motivod,
      txtSERIE, txtNUMERO, txtFECHA_DOCUMENTO, fecha_vto, txtOBSERVACION,
      txtSUB_TOTAL, txtIGV, percepcion, retencion, iddetraccion, detraccion, ICB,
      descuento, txtTOTAL, referencial, gratuita, exonerado, inafecta,
      comision, tarjeta, tipocambio, txtID_MONEDA, tipo_pago, medio_pago,
      orden,
      tipoguia, guia,
      tipoguia2, guia2,
      tipoguia3, guia3,
      tipoguia4, guia4,
      tipoguia5, guia5,
      presupuesto, referencia, condiciones, hash_cpe, hash_cdr, mensaje,
      mach_id, mach_numero, mach_monto, mach_fecha, mach_observaciones,
      estado, estadopago, kardex, idcaja
    ) VALUES (
      '$idempresa','$beta','$pedido','$sector','".esc($controlpresupuestal)."','".esc($exportacion)."',
      '$idcliente','$idusuarioAuth','$idlocal','".esc($tipodocumento)."',
      '".esc($doc_relaciona)."','".esc($docmodifica_tipo)."','".esc($docmodifica)."','".esc($modifica_motivo)."','".esc($modifica_motivod)."',
      '".esc($txtSERIE)."','".esc($txtNUMERO)."','".esc($FECHA)."','".esc($fecha_vto)."','".esc($OBSERVACION)."',
      '$SUB_TOTAL','$IGV','$percepcion','$retencion','$iddetraccion','$detraccion','$ICB',
      '$descuento','$TOTAL','$referencial','$gratuita','$exonerado','$inafecta',
      '$comision','$tarjeta','$tipocambio','".esc($docMoneda)."','".esc($tipo_pago)."','".esc($medio_pago)."',
      '".esc($orden)."',
      '".esc($tipoguia)."','".esc($guia)."',
      '".esc($tipoguia2)."','".esc($guia2)."',
      '".esc($tipoguia3)."','".esc($guia3)."',
      '".esc($tipoguia4)."','".esc($guia4)."',
      '".esc($tipoguia5)."','".esc($guia5)."',
      '', '".esc($referencia)."', '',
      '".esc($hash_cpe)."','".esc($hash_cdr)."','".esc($mensaje)."',
      '".esc($mach_id)."','".esc($mach_numero)."','$mach_monto','".esc($mach_fecha)."','".esc($mach_observaciones)."',
      '$estado','$estadopago','$kardex','$idcaja'
    )";
  $idventa = qid($sqlVenta);

  if($lockAcquired){
    q("SELECT RELEASE_LOCK('".esc($lockName)."')");
    $lockAcquired=false;
  }

  // detalle_venta + artículo
  $insertedDetalles=0;
  foreach($detalle_norm as $it){
    $CODIGO=to_str(getv($it,"CODIGO",""));
    $DESCRIPCION=to_str(getv($it,"DESCRIPCION",""));
    if($CODIGO===""||$DESCRIPCION==="") continue;

    $umedida=to_str(getv($it,"umedida","NIU")); if($umedida==="") $umedida="NIU";

    $forced_id=to_int(getv($it,"txtCOD_ARTICULO", getv($it,"idproducto",0)),0);
    $moneda_item=to_str(getv($it,"moneda", getv($it,"MONEDA","")));

    $qty=(float)getv($it,"_qty",1);
    $p_inc=(float)getv($it,"_p_inc",0);
    $base=(float)getv($it,"_base",0);
    $igvL=(float)getv($it,"_igv",0);
    $tot=(float)getv($it,"_tot",0);
    $icb=(float)to_dec(getv($it,"ICBPER_DETALLE",0),0);

    $EXON=to_dec(getv($it,"EXONERADO",0),0);
    $INAF=to_dec(getv($it,"INAFECTO",0),0);
    $GRAT=to_dec(getv($it,"GRATUITA",0),0);

    $tipoDet = 0;
    if($GRAT > 0) $tipoDet = 2;
    else if($INAF > 0) $tipoDet = 3;
    else if($EXON > 0) $tipoDet = 1;

    $idproducto = ensure_articulo(
      $idempresa,$idlocal,$CODIGO,$DESCRIPCION,$umedida,
      ($idgrupo>0?$idgrupo:0),
      $forced_id,$moneda_item,$docMoneda,$p_inc
    );
    if($idproducto<=0){ q("ROLLBACK"); out_err("No se pudo registrar/obtener artículo", ["CODIGO"=>$CODIGO]); }

    $sqlDet="INSERT INTO detalle_venta (
        idempresa,idlocal,beta,idventa,tipoarticulo,idproducto,codigoproducto,unidadmedida,idpresentacion,nombreproducto,
        txtCANTIDAD_ARTICULO,cantidadp,precio,preciocompra,descuento,subtotal,igv,ICB,importe,
        exoneradod,inafectad,gratuitad,detracciond,comisiond,stock,tipo,anticipo,doc_anticipo,placa,idlote,idproveedor,iddestino,
        carga_util,cantidad_toneladas,fecha,idcaja
      ) VALUES (
        '$idempresa','$idlocal','$beta','$idventa','1','$idproducto','".esc($CODIGO)."','".esc($umedida)."','0','".esc($DESCRIPCION)."',
        '$qty','$qty','".round($p_inc,2)."','0','0','$base','$igvL','$icb','$tot',
        '$EXON','$INAF','$GRAT','0','0','0','$tipoDet','0','','','0','0','0',
        '0','0','".esc(now_dt())."','0'
      )";
    q($sqlDet);
    $insertedDetalles++;
  }
  if($insertedDetalles<=0){ q("ROLLBACK"); out_err("No se insertó ningún detalle válido"); }

  $vr=q1("SELECT COUNT(*) AS c FROM detalle_venta WHERE idventa='$idventa'");
  $detReal=$vr?(int)$vr["c"]:0;
  if($detReal!==$insertedDetalles) $warnings[]="Detalle: contador=$insertedDetalles, BD=$detReal";
  $insertedDetalles=$detReal;

  // pagos
  $pagosInsertados=0;
  $serie_pago = $txtSERIE."-".$txtNUMERO;

  if($tipo_pago==="CREDITO"){
    $cuotas=getv($payload,"cuotas",[]);
    if(!is_array($cuotas) || count($cuotas)===0){
      $cuotas=[["fecha"=>date('Y-m-d 00:00:00'),"monto"=>fmt2($TOTAL)]];
    }
      $idxCuota = 0;
    foreach($cuotas as $c){
      $idxCuota++;
      if(!is_array($c)) continue;
      $fecha_pago=to_str(getv($c,"fecha",date('Y-m-d 00:00:00')));
      $monto = round(to_dec(getv($c,"monto",0), 0.0), 2);
      if($monto<=0) continue;

      $montosoles=0.000; $montodolares=0.000;
      if($docMoneda==="USD"){ $montodolares=$monto; $montosoles=$monto*$tipocambio; }
      else { $montosoles=$monto; $montodolares=($tipocambio>0)?($monto/$tipocambio):0.000; }

      // Reglas crédito (cuotas):
      // - nivel = 0 (indica cuota)
      // - estado = 0 (pendiente)
      // - serie = F001-00000020-01 (nro cuota a 2 dígitos)
      $cuotaNro  = to_str(getv($c, "nro", (string)$idxCuota));
      $cuotaNro2 = str_pad($cuotaNro, 2, "0", STR_PAD_LEFT);
      $serieCuota = $serie_pago . "-" . $cuotaNro2;
      $comentCuota = "API receive CUOTA " . $cuotaNro2;

      $sqlPago="INSERT INTO caja_ventapago (
        idempresa,beta,idlocal,idusuario,tipopago,otrospagos,nivel,idventa,idtipo,serie,moneda,
        montosoles,montodolares,tipocambio,operacion,fecha,fecha_pago,fechaoperacion,comentarios,estado
      ) VALUES (
        '$idempresa','$beta','$idlocal','$idusuarioAuth','CREDITO','0','0','$idventa','".(int)$idtipo_req."','".esc($serieCuota)."','".esc($docMoneda)."',
        '$montosoles','$montodolares','$tipocambio','','".esc(now_dt())."','".esc($fecha_pago)."','".esc(now_dt())."','".esc($comentCuota)."','0'
      )";
      q($sqlPago);
      $pagosInsertados++;
    }
    if($pagosInsertados<=0){ q("ROLLBACK"); out_err("Crédito sin cuotas válidas"); }
  } else {
    $montosoles=0.000; $montodolares=0.000;
    if($docMoneda==="USD"){ $montodolares=$TOTAL; $montosoles=$TOTAL*$tipocambio; }
    else { $montosoles=$TOTAL; $montodolares=($tipocambio>0)?($TOTAL/$tipocambio):0.000; }

    $comentContado = "API receive PAGO CONTADO";
    $sqlPago="INSERT INTO caja_ventapago (
      idempresa,beta,idlocal,idusuario,tipopago,otrospagos,nivel,idventa,idtipo,serie,moneda,
      montosoles,montodolares,tipocambio,operacion,fecha,fecha_pago,fechaoperacion,comentarios,estado
    ) VALUES (
      '$idempresa','$beta','$idlocal','$idusuarioAuth','CONTADO','0','1','$idventa','".(int)$idtipo_req."','".esc($serie_pago)."','".esc($docMoneda)."',
      '$montosoles','$montodolares','$tipocambio','','".esc(now_dt())."','".esc(now_dt())."','".esc(now_dt())."','".esc($comentContado)."','1'
    )";
    q($sqlPago);
    $pagosInsertados=1;
  }

  q("COMMIT");

  // SUNAT (después de grabar)
  $sunat = [
    "moneda"=>$docMoneda,
    "estado"=>"1",
    "mensaje"=>"(ver envio.php)",
    "serie"=>$txtSERIE,
    "numero"=>$txtNUMERO,
    "cod_sunat"=>"0",
    "idventa"=>(string)$idventa,
    "pdf"=>"https://app.tmperu.online/plugins/dompdf/index.php?id=".$idventa
  ];

  try{
    if(function_exists("enviarfactura")){
      // IMPORTANTE: enviarfactura usa config.tipo internamente (BETA/PROD)
      $res = enviarfactura($idventa,'1');
      if(is_array($res)){
        $sunat["estado"]   = (string)to_str(getv($res,"estado","1"));
        $sunat["mensaje"]  = to_str(getv($res,"msj_sunat", getv($res,"mensaje","")));
        $sunat["cod_sunat"]= (string)to_str(getv($res,"cod_sunat","0"));
        $sunat["serie"]    = to_str(getv($res,"serie",$sunat["serie"]));
        $sunat["numero"]   = to_str(getv($res,"numero",$sunat["numero"]));
        $sunat["pdf"]      = to_str(getv($res,"pdf",$sunat["pdf"]));
        normalize_cod_sunat($sunat);
      }
    }else{
      $warnings[]="SUNAT: función enviarfactura() no existe (envio.php).";
    }
  }catch(\Throwable $e){
    $warnings[]="SUNAT: ".$e->getMessage();
  }

  $serie_num = $txtSERIE."-".$txtNUMERO;

  out_ok([
    "estado"=>"1",
    "mensaje"=>"GUARDADO CORRECTAMENTE",
    "sunat"=>$sunat,
    "tipoigv"=>"0",
    "serie_num"=>$serie_num,
    "beta_guardado"=>$beta,
    "detalles_insertados"=>$insertedDetalles,
    "subtotal"=>fmt2($SUB_TOTAL),
    "igv"=>fmt2($IGV),
    "total"=>fmt2($TOTAL),
    "pagos_insertados"=>$pagosInsertados,
    "warnings"=>$warnings
  ]);

}catch(\Throwable $e){
  q("ROLLBACK");
  if($lockAcquired){
    @q("SELECT RELEASE_LOCK('".esc($lockName)."')");
  }
  out_err("Error interno: ".$e->getMessage());
}