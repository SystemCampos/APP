<?php

error_reporting(E_ALL ^ E_NOTICE);
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
// Se incluye el archivo que contiene la clase generica
require_once('config_cpe.php');


//$array = explode("/", $_SERVER['REQUEST_URI']);
$bodyRequest = file_get_contents("php://input");
// Decodifica el cuerpo de la solicitud y lo guarda en un array de PHP
$cab = json_decode($bodyRequest, true);
$sunatid= (isset($cab['sunatid'])) ? $cab['sunatid'] : "";
$clave= (isset($cab['sunatclave'])) ? $cab['sunatclave'] : "";

$ruc= (isset($cab['ruc'])) ? $cab['ruc'] : "";
$usuariosol= (isset($cab['usuariosol'])) ? $cab['usuariosol'] : "";
$password= (isset($cab['clavesol'])) ? $cab['clavesol'] : "";

$tipodoc= (isset($cab['tipodoc'])) ? $cab['tipodoc'] : "";
$serie= (isset($cab['serie'])) ? $cab['serie'] : "";
$numero= (isset($cab['numero'])) ? $cab['numero'] : "";
$token_access= (isset($cab['token_access'])) ? $cab['token_access'] : "";
	
	
	
$username=$ruc.$usuariosol;
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR AL PROCESAR';	


//echo $sql3;

$id=$sunatid;

$clave = str_replace("+", "%2B", $clave);
$clave = str_replace("==", "%3D%3D", $clave);
		
/*CREAMOS EL TOKEN*/
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api-seguridad.sunat.gob.pe/v1/clientessol/'.$id.'/oauth2/token/',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'grant_type=password&scope=https%3A%2F%2Fapi-cpe.sunat.gob.pe&client_id='.$id.'&client_secret='.$clave.'&username='.$username.'&password='.$password,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded',
    'Cookie: TS019e7fc2=019edc9eb82dcd8fec0a3bd848e49fb99eec6d2c3bf4b04081df2d440b003ae1fb1930ddaa2a6bc63ebbdcca3f4f2ff9c2d23a32af'
  ),
));

$response = curl_exec($curl);
//var_dump($response);
curl_close($curl);

$response=json_decode($response);
		
if(isset($response->access_token)){
		
$token_access=$response->access_token;
	
//$mensaje['ticket']=$guia['ticket'];
		
/*VALIDAMOS EL ESTADO DE LA GUIA*/			
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/envios/'.$guia['ticket'],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'numRucEnvia: '.$ruc,
    'numTicket: '.$guia['ticket'],
    'Authorization: Bearer '. $token_access,
  ),
));

$response3 = curl_exec($curl);
//var_dump($response3);
$response3=json_decode($response3);
$codRespuesta=$response3->codRespuesta;

curl_close($curl);

$mensaje['cod_sunat'] =$codRespuesta;

if($codRespuesta=='99'){

$error=$response3->error;
$mensaje['numerror']=$error->numError;
$mensaje['msj_sunat']=$error->desError;	
$mensaje['hash_cdr'] ='';

}else if($codRespuesta=='98'){

$mensaje['numerror']='99';
$mensaje['msj_sunat']='Envío en proceso';
$mensaje['hash_cdr'] ='';

}else if($codRespuesta=='0'){

//$mensaje['arcCdr']=$response3->arcCdr;
$mensaje['indCdrGenerado']=$response3->indCdrGenerado;

$tipoc='PRODUCCION';
	
$ruta_archivo_cdr='../api_cpe/'.$tipoc.'/'.$ruc.'/';
$archivo=$ruc.'-'.$tipodoc.'-'.$serie.'-'.$numero;
	
file_put_contents($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP', base64_decode($response3->arcCdr));
            //extraemos archivo zip a xml
            $zip = new ZipArchive;
            if ($zip->open($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP') === TRUE) {
                $zip->extractTo($ruta_archivo_cdr);
                $zip->close();
            }
unlink($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP');
 //=============hash CDR=================
            $doc_cdr = new DOMDocument();
            if (file_exists(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.XML')) {
$doc_cdr->load(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.XML');
            }else{
$doc_cdr->load(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.xml');           
            }

            $mensaje['cod_sunat'] = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;
            $mensaje['msj_sunat'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
            $mensaje['hash_cdr'] = $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;

$hascdr=$doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;
$msj_sunat=$doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
	
}else{

$mensaje['numerror']='88';
$mensaje['msj_sunat']='SUNAT FUERA DE SERVICIO';
$mensaje['hash_cdr'] ='';
	
}

}else{
$mensaje['cod_sunat']='99';
$mensaje['error']=$response->error;
$mensaje['msj_sunat']=$response->error_description;	
$mensaje['hash_cdr'] ='';
}
		
echo json_encode($mensaje);	


?>