<?php
//require_once("dompdf/dompdf_config.inc.php");
$rutat=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

require_once 'lib/html5lib/Parser.php';
require_once 'lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'lib/php-svg-lib/src/autoload.php';
require_once 'src/Autoloader.php';
Dompdf\Autoloader::register();
use Dompdf\Dompdf;
use Dompdf\Options;
include "../phpqrcode/qrlib.php";
require "../../config/conexion.php";
require "../../modelos/numeros-letras.php";

$idventa=$_GET['id'];

$sql="SELECT *FROM venta WHERE idventa='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
		
$sql2="SELECT c.nombre AS nsector, p.* FROM persona p LEFT JOIN categoria c ON p.sector=c.idcategoria WHERE p.idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
		
$sqluser="SELECT *FROM usuario WHERE idusuario='$mostrar[idusuario]' ";
$user= ejecutarConsultaSimpleFila($sqluser);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);
		
$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);
		
if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }
		
$tdocumento='TICKET DE VENTA';
		
if($mostrar['txtID_TIPO_DOCUMENTO']=='03'){ $tdocumento='BOLETA DE VENTA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='90'){ $tdocumento='RECIBO DE VENTA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='92'){ $tdocumento='TICKET DE VENTA'; }

$subtotal=$mostrar['txtTOTAL']-$mostrar['txtIGV'];		
$subtotal= number_format($subtotal, 2);
		
$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['txtSERIE'].' | '.$mostrar['txtNUMERO'].' | '.$mostrar['txtIGV'].' | '.$mostrar['txtTOTAL'].' | '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' | '.$mcliente['tipo_documento'].' | '.$mcliente['txtID_CLIENTE'].' |';
		
$rutaqr=$mempresa['ruc'].".png";
QRcode::png($text, $rutaqr, 'Q', 15, 0);
		
	
$cont='<style type="text/css">
.tabla { font-size: 17px; width: 390px; }
.centrado { text-align: center; }
.negrita { font-weight: bold; }


@page {
            margin-top: 0.9em;
            margin-left: 0.9em;
        }

  </style>';
		
	
		
$cont.='
<table class="tabla" border="0" cellspacing="0">
  <tbody>';
if($mostrar['idempresa']!='34'){
	
$cont.='<tr>
      <td style="text-align: center" >';	  
$cont.='<img src="'.RUTA.'/files/logo/'.$mempresa['id'].'b.jpg" style="width: 260px; max-height: 190px; " />';		  
$cont.='</td>
    </tr>';	
} 

$cont.='<tr>
      <td style="text-align: center" >';
$cont.=''.$mempresa['razon_social'].'<br>';
$cont.='RUC: '.$mempresa['ruc'].' - ';

$results=ejecutarConsulta("SELECT *FROM sucursal WHERE idempresa='$mostrar[idempresa]' AND nivel='0' ");
while($obj = $results->fetch_object()){
$cont.='<strong>'.$obj->sucursal.':</strong> '.$obj->direccion.'<br>';
$cont.=$obj->telefono.'<br>';
}

$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'</b><br>';
$cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' / ';
$cont.=date("H:i:s", strtotime($mostrar['txtFECHA_DOCUMENTO'])).'<br>';

		  
$cont.='</td>
    </tr>
    <tr>
      <td >';
$cont.='VENDEDOR: '.$user['nombre'].'<hr>';		  
$cont.='DATOS DEL CLIENTE: <br>';		
$cont.=$mcliente['nombre'].'<br>';		
$cont.=$mcliente['tipo_documento'].': '.$mcliente['txtID_CLIENTE'].' | COD CLI:'.$mcliente['codigo'].' | SECTOR:'.$mcliente['nsector'].'<br>';	
$cont.='DIRECCIÓN: '.$mcliente['direccion'].'<hr>';	
		  
		  
$cont.='</td>
    </tr>
  </tbody>
</table>

';
$cont.='
<table class="tabla" border="0" cellspacing="0">

    <tr>
      <td width="45%" >Prod.</td>
	  <td width="5%" >UM</td>
      <td width="5%" >C.</td>
      <td width="15%" style="text-align:center" >Pre.</td>
      <td width="15%" style="text-align: center" >Tot</td>
    </tr>

';
		
$sql="SELECT *FROM detalle_venta WHERE idventa='$idventa' ";
$rspta =ejecutarConsulta($sql);

while ($reg = $rspta->fetch_object()){
	
$nombre=$reg->unidadmedida;	
	
$cont.='<tr>
      <td width="45%" >'.$reg->nombreproducto.'</td>
	  <td width="5%" >'.$nombre.'</td>
      <td width="5%" >'.number_format($reg->txtCANTIDAD_ARTICULO, 2).'</td>
      <td width="15%" style="text-align:center">'.number_format($reg->precio, 2).'</td>
      <td width="15%" style="text-align: right" >'.number_format($reg->importe, 2).'</td>
    </tr>';
}	
$cont.='
</table>

<table class="tabla" border="0" cellspacing="0">
<tr><td style="text-align: center" ><hr></td></tr>
</table>

<table border="0" class="tabla" cellspacing="0">
    
<tr>
      <td >Op Gravada '.$mf.'</td>
      <td width="15%" style="text-align: right" >'.number_format($mostrar['txtSUB_TOTAL'], 2).'</td>
    </tr>
	
<tr>
      <td >Gratuita '.$mf.'</td>
      <td width="15%" style="text-align: right" >'.number_format($mostrar['gratuita'], 2).'</td>
</tr>
	
<tr>
      <td >Exonerado '.$mf.'</td>
      <td width="15%" style="text-align: right" >'.number_format($mostrar['exonerado'], 2).'</td>
    </tr>
	
<tr>
      <td >Descuento '.$mf.'</td>
      <td width="15%" style="text-align: right" >'.number_format($mostrar['descuento'], 2).'</td>
    </tr>
    

    
<tr>
      <td >I.G.V.  '.$mf.'</td>
      <td width="15%" style="text-align: right" >'.number_format($mostrar['txtIGV'], 2).'</td>
    </tr>
	
<tr>
      <td >ICBPER  '.$mf.'</td>
      <td width="15%" style="text-align: right" >'.number_format($mostrar['ICB'], 2).'</td>
    </tr>
    
<tr>




      <td >Importe total  '.$mf.'</td>
      <td width="15%" style="text-align: right" >'.number_format($mostrar['txtTOTAL'], 2).'</td>
    </tr>

</table>';


$cont.='
<table class="tabla" border="0" cellspacing="0">
<tr>
      <td style="text-align: center" >';
$cont.='<br>'.$mostrar['txtOBSERVACION'].'<br>';		  
$cont.='<br>SON: '.numtoletras($mostrar['txtTOTAL'], $valmoneda);

		  
$cont.='</td>
  </tr>
  
  
    <tr>
      <td style="text-align: center" >';

$cont.='<br><img src="'.RUTA.'/plugins/dompdf/'.$mempresa['ruc'].'.png" width="100" height="100" /><br><br>';	

		  
$cont.='</td>
    </tr>
    <tr>
      <td style="text-align: center" >';

$cont.='GRACIAS POR SU PREFERENCIA';		  
		  
$cont.='</td>
    </tr>

</table>';
	


if(isset($_GET['nivel'])){
echo $cont;
}else{

$filename = "newpdffile";
   $dompdf = new DOMPDF();
   //$dompdf->set_paper('letter','portrait');

$dompdf->set_paper(array(0,0,210,800));

$GLOBALS['bodyHeight'] = 0;

$dompdf->setCallbacks(
  array(
    'myCallbacks' => array(
      'event' => 'end_frame', 'f' => function ($infos) {
        $frame = $infos["frame"];
        if (strtolower($frame->get_node()->nodeName) === "body") {
            $padding_box = $frame->get_padding_box();
            $GLOBALS['bodyHeight'] += $padding_box['h'];
        }
      }
    )
  )
);

   $dompdf->load_html($cont);
   $dompdf->render();

$dompdf = new Dompdf();
$dompdf->set_paper(array(0,0,210,$GLOBALS['bodyHeight']+50));
$dompdf->loadHtml($cont);
$dompdf->render();
$dompdf->stream($filename,array("Attachment"=>0));

}

?>