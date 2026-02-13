<?php
require_once __DIR__ . "/../config/conexion.php";

/**
 * ======================================================
 * persona(): crea/obtiene cliente por documento
 * ======================================================
 */
function persona($idempresa, $doc, $nombre, $direccion = "", $correo = "", $telefono = ""){

  $doc = limpiarCadena($doc);
  $nombre = limpiarCadena($nombre);
  $direccion = limpiarCadena($direccion);
  $correo = limpiarCadena($correo);
  $telefono = limpiarCadena($telefono);

  $row = ejecutarConsultaSimpleFila("SELECT idpersona FROM persona WHERE idempresa='$idempresa' AND txtID_CLIENTE='$doc' LIMIT 1");
  if($row && isset($row["idpersona"])){
    $idpersona = (int)$row["idpersona"];
    // actualiza básicos si llegan nuevos
    ejecutarConsulta("
      UPDATE persona SET
        nombre='$nombre',
        direccion='$direccion',
        email='$correo',
        telefono='$telefono'
      WHERE idpersona='$idpersona' AND idempresa='$idempresa'
    ");
    return $idpersona;
  }

  // Defaults requeridos por tu tabla persona (BD NUEVA)
  $vendedor=0;
  $tipo_persona='CLIENTE';
  $codigo='';
  $descuentom='';
  $tipo_documento='RUC';
  $sector=0;
  $pais='PE';
  $ciudad='';
  $lat=''; $lon='';
  $email2='';
  $pass=''; // OJO: tu tabla permite pass NOT NULL
  $txtRAZON_SOCIAL=$nombre;
  $descuento=0;
  $cci='';
  $puntos=0;
  $creditolimite=0;
  $credito=0;
  $obs='';
  $edad='00';
  $venta_pago=0;

  $sql = "
    INSERT INTO persona(
      idempresa, vendedor, tipo_persona, codigo, descuentom,
      nombre, tipo_documento, txtID_CLIENTE, sector, direccion, pais, ciudad, lat, lon,
      telefono, email, email2, pass, txtRAZON_SOCIAL, descuento, cci, puntos, creditolimite, credito, obs, edad, venta_pago
    ) VALUES (
      '$idempresa', '$vendedor', '$tipo_persona', '$codigo', '$descuentom',
      '$nombre', '$tipo_documento', '$doc', '$sector', '$direccion', '$pais', '$ciudad', '$lat', '$lon',
      '$telefono', '$correo', '$email2', '$pass', '$txtRAZON_SOCIAL', '$descuento', '$cci', '$puntos', '$creditolimite', '$credito', '$obs', '$edad', '$venta_pago'
    )
  ";
  return ejecutarConsulta_retornarID($sql);
}

/**
 * ======================================================
 * ensure_categoria(): reutiliza tabla categoria para GRUPO/MARCA/LINEA
 * nivel: 1=GRUPO, 2=MARCA, 3=LINEA
 * ======================================================
 */
function ensure_categoria($idempresa, $nivel, $nombre){

  $nombre = trim((string)$nombre);
  if($nombre === "") $nombre = "GENERAL";

  $nombreSafe = limpiarCadena($nombre);

  $row = ejecutarConsultaSimpleFila("SELECT idcategoria FROM categoria WHERE idempresa='$idempresa' AND nivel='$nivel' AND nombre='$nombreSafe' LIMIT 1");
  if($row && isset($row["idcategoria"])){
    return (int)$row["idcategoria"];
  }

  // BD NUEVA: ctaventas es NOT NULL (varchar(4))
  $idnivel = 0;
  $descripcion = null;
  $ctaventas = "0102";
  $condicion = 1;

  $sql = "
    INSERT INTO categoria(idempresa, nivel, idnivel, nombre, descripcion, ctaventas, condicion)
    VALUES('$idempresa','$nivel','$idnivel','$nombreSafe', NULL, '$ctaventas', '$condicion')
  ";
  return ejecutarConsulta_retornarID($sql);
}

/**
 * ======================================================
 * articulo(): crea/obtiene artículo por (codigo + medida + idempresa)
 * - Compat: acepta 11º argumento $idlocal (opcional).
 * - Si existe y llegan nuevos IDs de grupo/marca/linea, ACTUALIZA.
 * ======================================================
 */
/**
 * articulo(): crea/obtiene artículo por (codigo + medida + idempresa)
 *
 * FIX BD NUEVA (según tu DDL): txtCOD_ARTICULO es NOT NULL y NO siempre es AUTO_INCREMENT.
 *
 * Reglas solicitadas:
 *  ✔ Artículo existente → no se altera moneda
 *  ✔ Artículo nuevo + moneda enviada → se guarda esa moneda
 *  ✔ Artículo nuevo sin moneda → usa moneda_default (normalmente la moneda del doc)
 *  ✔ Si se envía $forcedId > 0 → se respeta (si no colisiona); si colisiona se genera correlativo.
 *  ✔ Si no hay $forcedId → genera correlativo manual (por empresa) y asegura unicidad.
 *
 * Compatibilidad:
 *  - Mantiene los 11 parámetros originales.
 *  - Se agregan 2 parámetros opcionales al final para no romper otros módulos.
 */
function articulo($codigo, $unidad, $tipoventa, $exonerado, $precio, $nombrearticulo, $idempresa, $idgrupo = 0, $idmarca = 0, $idlinea = 0, $idlocal = 0, $forcedId = 0, $monedaNuevo = ""){

  $codigo = limpiarCadena(trim((string)$codigo));
  $unidad = limpiarCadena(trim((string)$unidad));
  $nombrearticulo = limpiarCadena(trim((string)$nombrearticulo));

  if($codigo === "") $codigo = "SIN-COD-" . time();
  if($unidad === "") $unidad = "NIU";
  if($nombrearticulo === "") $nombrearticulo = "ARTICULO SIN NOMBRE";

  // buscar artículo existente
  $ex = ejecutarConsultaSimpleFila("
    SELECT txtCOD_ARTICULO, idcategoria, marca, linea
    FROM articulo
    WHERE idempresa='$idempresa' AND codigo='$codigo' AND medida='$unidad'
    LIMIT 1
  ");

  if($ex && isset($ex["txtCOD_ARTICULO"])){
    $idart = (int)$ex["txtCOD_ARTICULO"];

    // actualizar ids si llegaron (y si son distintos)
    $updates = [];
    if($idgrupo > 0 && (int)$ex["idcategoria"] !== (int)$idgrupo){
      $updates[] = "idcategoria='".(int)$idgrupo."'";
    }
    if($idmarca > 0 && (int)$ex["marca"] !== (int)$idmarca){
      $updates[] = "marca='".(int)$idmarca."'";
    }
    if($idlinea > 0 && (int)$ex["linea"] !== (int)$idlinea){
      $updates[] = "linea='".(int)$idlinea."'";
    }
    if($idlocal > 0){
      $updates[] = "idlocal='".(int)$idlocal."'";
    }
    if(count($updates) > 0){
      ejecutarConsulta("UPDATE articulo SET ".implode(",", $updates)." WHERE txtCOD_ARTICULO='$idart' AND idempresa='$idempresa'");
    }

    return $idart;
  }

  // ======================================================
  // insertar nuevo (BD NUEVA)
  // ======================================================
  $nivel = 0;
  $sublinea = 0;
  $subfamilia = 0;
  $sanitario = "";
  $principioactivo = "";
  $idproveedor = "";
  $codigosunat = "";
  $existencia = "02";

  $stock = 0;
  $stockmin = 0;
  $stockmax = 0;

  $precio = (float)$precio;
  $preciooferta = 0;
  $moneda = trim((string)$monedaNuevo);
  if($moneda === ""){
    // por compatibilidad con módulos antiguos, default PEN
    $moneda = "PEN";
  }

  $precio_porcentaje = 0;
  $precio_compra = $precio;

  $mayor = 3;

  $precio_porcentaje2 = 0;
  $precio_mayor2 = $precio;

  $precio_porcentaje3 = 0;
  $precio_mayor = $precio;

  $exonerado_igv = 0;

  $comision = 0;
  $comisionm = 0;
  $comisionmp = 0;
  $bolsa = 0;

  $ctacompras = "";
  $ctaventas = "";
  $canje = "NO";
  $canjepuntos = 0;
  $canjecobro = 0;
  $imagen = null;
  $estado = 1;
  $condicion = 1;
  $resaltado = 0;

  // ======================================================
  // Generación correlativa manual de txtCOD_ARTICULO
  // - Primero intenta usar $forcedId (si viene)
  // - Si no, correlativo por empresa: MAX(txtCOD_ARTICULO) WHERE idempresa=? + 1
  // - Asegura unicidad global por si txtCOD_ARTICULO es PK global
  // ======================================================

  $forcedId = (int)$forcedId;
  $txtCOD_ARTICULO = 0;

  if($forcedId > 0){
    $chk = ejecutarConsultaSimpleFila("SELECT txtCOD_ARTICULO FROM articulo WHERE txtCOD_ARTICULO='$forcedId' LIMIT 1");
    if(!$chk || !isset($chk['txtCOD_ARTICULO'])){
      $txtCOD_ARTICULO = $forcedId;
    }
  }

  if($txtCOD_ARTICULO <= 0){
    $nx = ejecutarConsultaSimpleFila("SELECT IFNULL(MAX(txtCOD_ARTICULO),0)+1 AS next_id FROM articulo WHERE idempresa='$idempresa' LIMIT 1");
    $txtCOD_ARTICULO = ($nx && isset($nx['next_id'])) ? (int)$nx['next_id'] : 1;
  }

  // asegurar unicidad global
  $safeGuard = 0;
  while(true){
    $chk = ejecutarConsultaSimpleFila("SELECT txtCOD_ARTICULO FROM articulo WHERE txtCOD_ARTICULO='$txtCOD_ARTICULO' LIMIT 1");
    if(!$chk || !isset($chk['txtCOD_ARTICULO'])) break;
    $txtCOD_ARTICULO++;
    $safeGuard++;
    if($safeGuard > 100000){
      // evita bucles infinitos
      return 0;
    }
  }

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
      '$txtCOD_ARTICULO', '$nivel', '$idempresa', '$idgrupo', '$idmarca', '$idlinea', '$sublinea', '$subfamilia',
      '$unidad', '$sanitario', '$principioactivo', '".(int)$idlocal."', '$idproveedor', '$codigo', '$codigosunat', '$existencia',
      '$nombrearticulo', '$stock', '$stockmin', '$stockmax',
      '$precio', '$preciooferta', '".limpiarCadena($moneda)."', '$precio_porcentaje', '$precio_compra', '$mayor',
      '$precio_porcentaje2', '$precio_mayor2', '$precio_porcentaje3', '$precio_mayor',
      '$exonerado_igv', '$comision', '$comisionm', '$comisionmp', '$bolsa',
      '$ctacompras', '$ctaventas', '$canje', '$canjepuntos', '$canjecobro',
      NULL, '$estado', '$condicion', '$resaltado'
    )
  ";

  ejecutarConsulta($sql);
  return $txtCOD_ARTICULO;
}
