<?php

error_reporting(E_ALL ^ E_NOTICE);
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
// Se incluye el archivo que contiene la clase generica
require_once('config_cpe.php');
require_once('../funcionesGlobales/validaciones.php');

//$array = explode("/", $_SERVER['REQUEST_URI']);
$bodyRequest = file_get_contents("php://input");

// Decodifica el cuerpo de la solicitud y lo guarda en un array de PHP
$cab = json_decode($bodyRequest, true);
$detalle = $cab['detalle'];

$cabecera = array(	
    //===============================================
	'IDSUNAT' => (isset($cab['IDSUNAT'])) ? $cab['IDSUNAT'] : "0000",
    'txtNRO_COMPROBANTE' => $cab['txtNRO_COMPROBANTE'],
    'txtFECHA_DOCUMENTO' => $cab['txtFECHA_DOCUMENTO'],
	'REGULAR' =>(isset($cab['REGULAR'])) ? $cab['REGULAR'] : "00",
    //========================datos de la empresa=========================
    'txtNRO_DOCUMENTO_EMPRESA' => $cab['txtNRO_DOCUMENTO_EMPRESA'],
    'txtTIPO_DOCUMENTO_EMPRESA' => $cab['txtTIPO_DOCUMENTO_EMPRESA'], //RUC
    'txtNOMBRE_COMERCIAL_EMPRESA' => ValidarCaracteresInv((isset($cab['txtNOMBRE_COMERCIAL_EMPRESA'])) ? $cab['txtNOMBRE_COMERCIAL_EMPRESA'] : ""),
    'txtCODIGO_UBIGEO_EMPRESA' => $cab['txtCODIGO_UBIGEO_EMPRESA'],
    'txtDIRECCION_EMPRESA' => (isset($cab['txtDIRECCION_EMPRESA'])) ? $cab['txtDIRECCION_EMPRESA'] : "",
    'txtDEPARTAMENTO_EMPRESA' => (isset($cab['txtDEPARTAMENTO_EMPRESA'])) ? $cab['txtDEPARTAMENTO_EMPRESA'] : "",
    'txtPROVINCIA_EMPRESA' => (isset($cab['txtPROVINCIA_EMPRESA'])) ? $cab['txtPROVINCIA_EMPRESA'] : "",
    'txtDISTRITO_EMPRESA' => (isset($cab['txtDISTRITO_EMPRESA'])) ? $cab['txtDISTRITO_EMPRESA'] : "",
    'txtCODIGO_PAIS_EMPRESA' => $cab['txtCODIGO_PAIS_EMPRESA'],
    'txtRAZON_SOCIAL_EMPRESA' => ValidarCaracteresInv($cab['txtRAZON_SOCIAL_EMPRESA']),
//====================DATOS SUNAT=====================//
    "txtUSUARIO_SOL_EMPRESA" => (isset($cab['txtUSUARIO_SOL_EMPRESA'])) ? $cab['txtUSUARIO_SOL_EMPRESA'] : "MODDATOS",
    "txtPASS_SOL_EMPRESA" => (isset($cab['txtPASS_SOL_EMPRESA'])) ? $cab['txtPASS_SOL_EMPRESA'] : "moddatos",
    "txtTIPO_PROCESO" => (isset($cab['txtTIPO_PROCESO'])) ? $cab['txtTIPO_PROCESO'] : "3",
	"PIN" => (isset($cab['PIN'])) ? $cab['PIN'] : "123456",
	"SUNAT" => (isset($cab['SUNAT'])) ? $cab['SUNAT'] : "SUNAT",
);

if($cab['txtTIPO_PROCESO']=='3'){ $dir='BETA'; }else{ $dir='PRODUCCION'; }
$archivo='../api_cpe/'.$dir.'/'.$cab['txtNRO_DOCUMENTO_EMPRESA'];

if (file_exists($archivo)) {
    
} else {
   mkdir($archivo, 0775, true);
}


$mensaje_cpe = cpe_percepcion($cab['txtTIPO_PROCESO'], $cab['txtNRO_DOCUMENTO_EMPRESA'], $cab['txtUSUARIO_SOL_EMPRESA'], $cab['txtPASS_SOL_EMPRESA'], $cab['PIN'], $cab['SUNAT'], $cabecera, $detalle);

$resultado['hash_cpe'] = $mensaje_cpe['hash_cpe'];
$resultado['cod_sunat'] = $mensaje_cpe['hash_cdr']['cod_sunat'];//str_replace("SOAP-ENV:CLIENT.", "", $mensaje_cpe['hash_cdr']['cod_sunat']);
$resultado['msj_sunat'] = str_replace("'","",$mensaje_cpe['hash_cdr']['msj_sunat']);
$resultado['hash_cdr'] = $mensaje_cpe['hash_cdr']['hash_cdr'];

print_json($resultado);

function print_json($data) {
    header("HTTP/1.1");
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data, JSON_PRETTY_PRINT);
}

?>