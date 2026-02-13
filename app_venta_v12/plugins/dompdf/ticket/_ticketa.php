<?php

$cont='


<style type="text/css">


@font-face {
            font-family: "trabuchefuente";           
            src: local("Trebuchet MS"), url("fuente/trebuc.ttf") format("truetype");
            font-weight: normal;
            font-style: normal;

        } 
		
@font-face {
            font-family: "colibril";           
            src: local("Colibri L"), url("fuente/calibril.ttf") format("truetype");
            font-weight: normal;
            font-style: normal;

        } 
 
 
 
 @font-face {
            font-family: "trabuchebold";           
            src: local("Trebuchet MS Bold"), url("fuente/TREBUCBD.ttf") format("truetype");
            font-weight: bold;
            font-style: bold;

        } 
  @font-face {
            font-family: "arialbold";           
            src: local("Arial Bold"), url("fuente/FontsFree-Net-arial-bold.ttf") format("truetype");
            font-weight: bold;
            font-style: bold;

        } 
		
  @font-face {
            font-family: "arialnarrowbold";           
            src: local("Arial narrow Bold"), url("fuente/5631-font.ttf") format("truetype");
            font-weight: bold;
            font-style: bold;

        } 


.tabla { 
width: 100%;
font-family: colibril;
font-size: 13px;
font-weight: lighter;
}

.tabla2 { 
font-family: colibril;
font-size: 11px;
font-weight: lighter; 
}

.estiloarial { 
font-family: Arial;
font-size: 14px;
}

.estiloarialbold { 
font-family: arialnarrowbold;
font-weight: bold;
font-size: 12px;
}


.centrado { text-align: center; }
.negrita { font-weight: bold; }

.arial { font-weight: bold; }

@page {

font-family: colibril;
font-size: 6px;
font-weight: lighter;
            margin-top: 0.9em;
            margin-left: 0.9em;
        }

  </style>';

$cont.='
<table class="tabla" border="0" cellspacing="0" style="width: 100% !important;">
  <tbody>';


$cont.='<tr>
      <td style="text-align: center" >';
$cont.='<img src="'.RUTA.'/files/logo/'.$mempresa['id'].'.png" style="width: 260px; max-height: 190px; " />';
$cont.='</td>
    </tr>';


$cont.='<tr>
      <td style="text-align: center" class="tabla2" >';
$cont.='RUC: '.$mempresa['ruc'].'<br>';
$cont.=$mempresa['razon_social'].'<br><hr>';
$cont.=$mempresa['nombre_comercial'].'<br><hr>';
$cont.=$mempresa['direccion'].'<br>';
$cont.='Telf: '.$mempresa['telefono'].'<br>';
$cont.=$mempresa['correo'].'<br>';
$cont.='</td></tr>';
$cont.='<tr><td style="text-align: center" >';
$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'</b><br>';
$cont.='</td></tr>

    <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 1px solid #000d06 !important;"></td>
       </tr>
      <td >';

$cont.='DATOS DEL CLIENTE: <br>';
$cont.='SEÑOR(ES): '.$mcliente['nombre'].'<br>';
$cont.=$mcliente['tipo_documento'].':' .$mcliente['txtID_CLIENTE'].'<br>';
$cont.='DIRECCIÓN:'.$mcliente['direccion'].'<br>';
$cont.='FORMA DE PAGO:'.$formapago.'<br>';
$cont.='FECHA DE EMISIÓN:'.date("d/m/Y", strtotime($mostrar['txtFECHA_DOCUMENTO'])).'<br>';
$cont.='TIPO MONEDA:'.$valmoneda.'<br>';
$cont.='VENDEDOR:'.$user['nombre'].'<br>';
$cont.='Transacciones<br>';
$cont.='

<table class="tabla" border="0" cellspacing="0" style="width: 100% !important;"> 
       <tr >
      <td colspan="5" style="text-align: right !important; border-bottom: 1px solid #000d06 !important;"></td>
       </tr>      
    <tr>
      <td width="5%" class="tabla2" >Cant</td>
	  <td width="5%" class="tabla2"  >UM</td>
      <td width="15%" style="text-align:center" class="tabla2"  >P.Unit.</td>
      <td width="15%" style="text-align: center" class="tabla2"  >Valor Comp</td>
    </tr>
    
        <tr >
      <td colspan="5" style="text-align: right !important; border-bottom: 1px solid #000d06 !important;"></td>
       </tr>

';

$sql="SELECT *FROM detalle_venta WHERE idventa='$idventa' ";
$rspta =ejecutarConsulta($sql);

while ($reg = $rspta->fetch_object()){

    $nombre=$reg->unidadmedida;

    $cont.='<tr>
      <td align="center" valign="top" >'.round($reg->txtCANTIDAD_ARTICULO, 2).'</td>
	  <td valign="top" >'.$nombre.'</td>
      <td style="text-align:center" valign="top">'.number_format($reg->precio, 2).'</td>
      <td style="text-align: right" valign="top" >'.number_format($reg->importe, 2).'</td>
    </tr>
	
	
<tr>
      <td colspan="5" valign="top" >'.$reg->nombreproducto.'</td>
	  </tr>
	
	
	';
}
$cont.='
        <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 1px solid #000d06 !important;"></td>
       </tr>
    

 


     <tr>
     <td  colspan="5"  >SON: '.numtoletras($mostrar['txtTOTAL'], $valmoneda).'
     </td>
     </tr>

    <tr>
    <td  colspan="5" style="text-align: center !important; " >&nbsp;</td>
   </tr>
    
</table>
<table class="tabla" border="0" cellspacing="0" style="width: 100% !important;">

  <tr>
    <td width="40%" align="left" valign="top" ><img src="'.RUTA.'/plugins/dompdf/'.$mempresa['ruc'].'.png" style="width: 100px; max-height: 100px; " /></td>
    <td width="60%" colspan="-2" style="text-align: right"  valign="top" >
	
	<table border="0" cellspacing="0" class="tabla" style="width: 100% !important;">
      ';

      $cont.='
      <tr>
        <td width="50%" style="text-align: right !important;">Op Gravada:  '.$mf.'.</td>
        <td width="50%" colspan="-2" style="text-align: right" >'.number_format($mostrar['txtSUB_TOTAL'], 2).'</td>
      </tr>
      <tr>
        <td style="text-align: right !important;">Gratuita:  '.$mf.'.</td>
        <td colspan="-2"  style="text-align: right" >'.number_format($mostrar['gratuita'], 2).'</td>
      </tr>
      <tr>
        <td style="text-align: right !important;">Exonerado:  '.$mf.'.</td>
        <td colspan="-2"  style="text-align: right" >'.number_format($mostrar['exonerado'], 2).'</td>
      </tr>
      <tr>
        <td style="text-align: right !important;">I.G.V.  '.$mf.'.</td>
        <td colspan="-2"  style="text-align: right" >'.number_format($mostrar['txtIGV'], 2).'</td>
      </tr>
      <tr>
        <td style="text-align: right !important;">TOTAL:  '.$mf.'.</td>
        <td colspan="-2"  style="text-align: right" >'.number_format($mostrar['txtTOTAL'], 2).'</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td  colspan="2" style="text-align: center !important; " ><img src="'.RUTA.'/images/firma.jpg" style="width: 80%; max-height: auto; " /></td>
  </tr>
	
	
  <tr>
    <td  colspan="2" class="estiloarial" style="text-align: center !important; " >'.$mostrar['hash_cpe'].'</td>
  </tr>
  <tr>
    <td  colspan="2" class="estiloarialbold" style="text-align: center !important; " >Horario de Atención. Lunes a Domingo de 8.am - 7:30 pm </td>
  </tr>
</table>




';

