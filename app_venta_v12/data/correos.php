<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/conexion.php";
require("../plugins/phpmailer/class.phpmailer.php");
require("../plugins/phpmailer/class.smtp.php");

$id=isset($_GET["id"])? limpiarCadena($_GET["id"]):"";

switch ($_GET["op"]){
		
case 'enviardoc':
$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';
	
$sql="SELECT *FROM venta WHERE idventa='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
	
$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$config= ejecutarConsultaSimpleFila($sql3);
	
if($config['tipo']=='3'){ $proceso='BETA'; }else{ $proceso='PRODUCCION'; }

$link=$config['ruc'].'-'.$mostrar['txtID_TIPO_DOCUMENTO'].'-'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'];
		
$comprobante=$link.'.pdf';
$comprobante2=$link.'.XML';
$comprobante3='R-'.$link.'.XML';
	
$rutapdf=RUTA.'/plugins/dompdf/';	

$serienumero=$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'];
$tipodoc='Boleta de venta Electrónica';
if($mostrar['txtID_TIPO_DOCUMENTO']=='01'){ $tipodoc='Factura Electrónica'; }	
		
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
CURLOPT_URL => $rutapdf."index.php?id=".$id."&correo=1",
    CURLOPT_USERAGENT => "Codular Sample cURL Request"
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
curl_close($curl);
	
$mail = new PHPMailer();
//Luego tenemos que iniciar la validación por SMTP:
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
		
    //De donde se va enviar
    if ($config['id'] == '6') {
      $smtp = "mail.claudiacupcakes.com";
      $usuario = "contabilidad@claudiacupcakes.com";
      $contrrasenia = "Cl4ud14.Cupcakes.2024";
      $puertoo = "465";
    } else {
      $smtp = "mail.templatemonsterperu.com";
      $usuario = "cpe@templatemonsterperu.com";
      $contrrasenia = "cesY0RD@N...@.@";
      $puertoo = "465";
    }	
	
$mail->Host = $smtp; // SMTP a utilizar. Por ej. smtp.elserver.com
$mail->Username = $usuario;	
$mail->Password = $contrrasenia;
$mail->SMTPSecure = 'ssl';
$mail->Port = $puertoo; // Puerto a utilizar

//$mail->SMTPDebug = SMTP::DEBUG_SERVER; //PARA VER SI HAY ERRORES
$mail->From = $usuario; // Desde donde enviamos (Para mostrar)
$mail->FromName = $config['razon_social'];

if($mcliente['email']!=''){
$mail->AddAddress($mcliente['email']); // Esta es la dirección a donde enviamos
}
		
if($mcliente['email2']!=''){
$mail->AddAddress($mcliente['email2']); // Esta es la dirección a donde enviamos
}
		
if($mostrar['idusuario']=='200'){	
$mail->AddAddress('comprobantecliente@falabella.com');	
}
		
if($mostrar['idusuario']=='300'){	
$mail->AddAddress('comprobantelinio@falabella.com');	
}

if ($config['id'] == '100') {
      $mail->AddAddress('correo@cliente.com'); // Esta es la dirección a donde enviamos
    } else {
      $mail->AddAddress($mcliente['email']); // Esta es la dirección a donde enviamos
    }

$mail->IsHTML(true); // El correo se envía como HTML
		
if($mostrar['idusuario']=='200'){	
$mail->Subject = $mostrar['presupuesto']." - comprobante Falabella ".$comprobante; // Este es el titulo del email.	
}else if($mostrar['idusuario']=='300'){	
$mail->Subject = $mostrar['presupuesto']." - comprobante Linio ".$comprobante; // Este es el titulo del email.	
}else{
$mail->Subject = "Envío de comprobante: ".$comprobante; // Este es el titulo del email.	
}
		
$body ="<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<title>Documento sin título</title>
</head>

<body>
<img src='https://claudiacupcakes.tmperu.net.pe/files/logo/".$config['id'].".png' style='max-width: 300px; height: auto;' >
<p>Estimado ".$mcliente['nombre'].",</p>
<p>Por la presente le comunicamos que se ha emitido el siguiente comprobante electrónico:</p>
<table border='0'>
  <tbody>
    <tr>
      <td><strong>Tipo de documento</strong></td>
      <td>:</td>
      <td><strong>".$tipodoc."</strong></td>
    </tr>
    <tr>
      <td><strong>Serie y número</strong></td>
      <td>:</td>
      <td><strong>".$serienumero."</strong></td>
    </tr>
    <tr>
      <td><strong>Fecha de emisión</strong></td>
      <td>:</td>
      <td><strong>".date("d/m/Y", strtotime($mostrar['txtFECHA_DOCUMENTO']))."</strong></td>
    </tr>
  </tbody>
</table>
<p>Se adjunta el Comprobante Electrónico.</p>
<p>Le informamos además que a través del Portal Web puede consultar y descargar este Comprobante de Pago Electrónico.<br>
  <a href='https://claudiacupcakes.tmperu.net.pe/comprobantes' target='_blank' >https://claudiacupcakes.tmperu.net.pe/comprobantes</a>.</p>
<p>Atentamente,</p>
<p>".$config['razon_social']."<br>
</p>
</body>
</html>";
	
	
//$body .= "Acá continuo el <strong>mensaje</strong>";
$mail->Body = $body; // Mensaje a enviar
$mail->AddAttachment("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante, $comprobante);
$mail->AddAttachment("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante2, $comprobante2);
$mail->AddAttachment("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante3, $comprobante3);
$mail->CharSet = 'UTF-8';
$exito = $mail->Send(); // Envía el correo.

unlink("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante);	

if($exito){
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El Correo fue enviado exitosamente';
	
}else{
	
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'No se pudo enviar el correo, Intenta más tarde';
	
}
	
echo json_encode($jsondata);
exit();
		
case 'enviarpercepcion':
$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';
		
$sql="SELECT *FROM percepcion WHERE id='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[idcliente]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
	
$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$config= ejecutarConsultaSimpleFila($sql3);
	
if($config['tipo']=='3'){ $proceso='BETA'; }else{ $proceso='PRODUCCION'; }

$link=$config['ruc'].'-'.$mostrar['tipodocumento'].'-'.$mostrar['serie'].'-'.$mostrar['numero'];
		
$comprobante=$link.'.pdf';
$comprobante2=$link.'.XML';
$comprobante3='R-'.$link.'.XML';
	
$rutapdf=RUTA.'/plugins/dompdf/';	

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
CURLOPT_URL => $rutapdf."percepcion.php?id=".$id."&correo=1",
    CURLOPT_USERAGENT => "Codular Sample cURL Request"
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
curl_close($curl);
	
$mail = new PHPMailer();
//Luego tenemos que iniciar la validación por SMTP:
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

 if ($config['id'] == '6') {
      $smtp = "mail.claudiacupcakes.com";
      $usuario = "contabilidad@claudiacupcakes.com";
      $contrrasenia = "Cl4ud14.Cupcakes.2024";
      $puertoo = "465";
    } else {
      $smtp = "mail.templatemonsterperu.com";
      $usuario = "cpe@templatemonsterperu.com";
      $contrrasenia = "cesY0RD@N...@.@";
      $puertoo = "465";
    }
    
$mail->Host = $smtp; // SMTP a utilizar. Por ej. smtp.elserver.com
$mail->Username = $usuario;	
$mail->Password = $contrrasenia;
    $mail->SMTPSecure = 'ssl';
    $mail->Port = $puertoo; // Puerto a utilizar

//$mail->SMTPDebug = SMTP::DEBUG_SERVER; //PARA VER SI HAY ERRORES
$mail->AddAddress($mcliente['email']);
$mail->From = $usuario; // Desde donde enviamos (Para mostrar)
$mail->FromName = $config['razon_social'];		
$mail->IsHTML(true); // El correo se envía como HTML
$mail->Subject = "Envío de comprobante: ".$comprobante; // Este es el titulo del email.	
		
$body = "Hola:".$mcliente['nombre'].". Te estamos enviando adjunto el comprobante (".$comprobante.") de la compra que hiciste en ".$config['razon_social']."<br />";
//$body .= "Acá continuo el <strong>mensaje</strong>";
$mail->Body = $body; // Mensaje a enviar
$mail->AddAttachment("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante, $comprobante);
$mail->AddAttachment("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante2, $comprobante2);
$mail->AddAttachment("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante3, $comprobante3);
$mail->CharSet = 'UTF-8';
$exito = $mail->Send(); // Envía el correo.
		
if($exito){
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El Correo fue enviado exitosamente';
	
}else{
	
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'No se pudo enviar el correo, Intenta más tarde';
	
}
if(file_exists($comprobante)){
unlink("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante);	
}
echo json_encode($jsondata);
exit();

case 'enviarNota':

$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';
	
$sql="SELECT *FROM notacredito WHERE id='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[idcliente]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
	
$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$config= ejecutarConsultaSimpleFila($sql3);
	
if($config['tipo']=='03'){ $proceso='BETA'; }else{ $proceso='PRODUCCION'; }

$link=$config['ruc'].'-'.$mostrar['tipodoc'].'-'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'];
$comprobante=$link.'.pdf';
$comprobante2=$link.'.XML';
	
$mail = new PHPMailer();
//Luego tenemos que iniciar la validación por SMTP:
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
    $mail->Host = $smtp; // SMTP a utilizar. Por ej. smtp.elserver.com
    $mail->Username = $usuario;
    $mail->Password = $contrrasenia;
    $mail->SMTPSecure = 'ssl';
    $mail->Port = $puertoo; // Puerto a utilizar


//$mail->SMTPDebug = SMTP::DEBUG_SERVER; //PARA VER SI HAY ERRORES

    $mail->From = $usuario; // Desde donde enviamos (Para mostrar)
    $mail->FromName = $config['razon_social'];
	$mail->AddAddress($mcliente['email']);// Esta es la dirección a donde enviamos
    $mail->IsHTML(true); // El correo se envía como HTML



$mail->Subject = "Envío de comprobante: ".$comprobante1; // Este es el titulo del email.
$body = "Hola:".$mcliente['nombre'].". Te estamos enviando adjunto el comprobante (".$comprobante1.") de la compra que hiciste en ".$config['razon_social']."<br />";
//$body .= "Acá continuo el <strong>mensaje</strong>";
$mail->Body = $body; // Mensaje a enviar
if(file_exists(RUTA."/plugins/dompdf/?".$config['ruc']."/".$comprobante)){
$mail->AddAttachment(RUTA."/plugins/dompdf/?".$config['ruc']."/".$comprobante, $comprobante);
	}
if(file_exists(RUTA."/plugins/dompdf/?".$config['ruc']."/".$comprobante)){
$mail->AddAttachment("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante2, $comprobante2);
}	
$mail->CharSet = 'UTF-8';
$exito = $mail->Send(); // Envía el correo.


if($exito){
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El Correo fue enviado exitosamente';
	
}else{
	
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'No se pudo enviar el correo, Intenta más tarde';
	
}
	
echo json_encode($jsondata);
exit();	

	
}
	

	

?>