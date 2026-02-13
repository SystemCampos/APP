<?php
	
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
$cont.='RUC: '.$mempresa['ruc'].'<br>';
$cont.=$mempresa['direccion'].'<br>';
$cont.=$mempresa['telefono'].'<br>';

$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'</b><br>';
$cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' / ';
$cont.=date("H:i:s", strtotime($mostrar['txtFECHA_DOCUMENTO'])).'<br>';

		  
$cont.='</td>
    </tr>
    <tr>
      <td >';
$cont.='VENDEDOR: '.$user['nombre'].'<hr>';		  
$cont.='CLIENTE: <br>';		
$cont.=$mcliente['nombre'].'<br>';
$cont.=$mcliente['tipo_documento'].': '.$mcliente['txtID_CLIENTE'].'<br>';	
$cont.='DIRECCIÓN: '.$mcliente['direccion'].'<hr>';	
		  
		  
$cont.='</td>
    </tr>
  </tbody>
</table>

';
$cont.='
<table class="tabla" border="0" cellspacing="0">

    <tr>
      <td width="5%" >Cant</td>
	  <td width="5%" >UM</td>
      <td width="45%" align="center" >Descripción</td>
      <td width="15%" style="text-align:center" >P.U.</td>
      <td width="15%" style="text-align: center" >Total</td>
    </tr>

';
		
$sql="SELECT *FROM detalle_venta WHERE idventa='$idventa' ";
$rspta =ejecutarConsulta($sql);

while ($reg = $rspta->fetch_object()){

$nombre=$reg->unidadmedida;

$cont.='<tr>
      <td align="center" valign="top" >'.round($reg->txtCANTIDAD_ARTICULO, 2).'</td>
	  <td valign="top" >'.$nombre.'</td>
      <td valign="top" >'.$reg->nombreproducto.'</td>
      <td style="text-align:center" valign="top">'.number_format($reg->precio, 2).'</td>
      <td style="text-align: right" valign="top" >'.number_format($reg->importe, 2).'</td>
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
$cont.='<br> Representación impresa de '.$tdocumento.'Consulte su comprobante (pdf,cdr) aquí: '.RUTA.'/comprobantes/  </br>';
$cont.='GRACIAS POR SU PREFERENCIA';
		  
$cont.='</td>
    </tr>

</table>';
	
?>