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
<table class="tabla" border="0" cellspacing="0" style="width: 100% !important;">
  <tbody>';


$cont.='<tr>
      <td style="text-align: center" >';
$cont.='<img src="'.RUTA.'/files/logo/'.$mempresa['id'].'b.jpg" style="width: 260px; max-height: 190px; " />';
$cont.='</td>
    </tr>';


$cont.='<tr>
      <td style="text-align: center" >';
$cont.='RUC: '.$mempresa['ruc'].'<br>';
$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'</b><br>';
$cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' / ';
$cont.=date("H:i:s", strtotime($mostrar['txtFECHA_DOCUMENTO'])).'<br>';


$cont.='</td>
    <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>
      <td >';

$cont.='DATOS DEL CLIENTE: <br>';
$cont.=$mcliente['nombre'].'<br>';
$cont.=$mcliente['tipo_documento'].':' .$mcliente['txtID_CLIENTE'].'<br>';
$cont.='DIR:'.$mcliente['direccion'].'<br>';

$cont.='

<table class="tabla" border="0" cellspacing="0" style="width: 100% !important;"> 
       <tr >
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>      
    <tr>
      <td width="5%" >Cant</td>
	  <td width="5%" >UM</td>
      <td width="45%" align="center" >Descripción</td>
      <td width="15%" style="text-align:center" >P.U.</td>
      <td width="15%" style="text-align: center" >Total</td>
    </tr>
    
        <tr >
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>

   <td >';

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
        <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>

    <tr>
      <td colspan="4" style="text-align: right !important;">Op Gravada:  '.$mf.'.</td>
      <td style="text-align: right" >'.number_format($mostrar['txtSUB_TOTAL'], 2).'</td>
    </tr>
	
    <tr>
      <td colspan="4" style="text-align: right !important;">Gratuita:  '.$mf.'.</td>
      <td  style="text-align: right" >'.number_format($mostrar['gratuita'], 2).'</td>
    </tr>
	
    <tr>
      <td colspan="4" style="text-align: right !important;">Exonerado:  '.$mf.'.</td>
      <td  style="text-align: right" >'.number_format($mostrar['exonerado'], 2).'</td>
    </tr>
	
    
    <tr>
      <td colspan="4" style="text-align: right !important;">I.G.V.  '.$mf.'.</td>
      <td  style="text-align: right" >'.number_format($mostrar['txtIGV'], 2).'</td>
    </tr>
	
     <tr>
      <td colspan="4" style="text-align: right !important;">ICBPER:  '.$mf.'.</td>
      <td style="text-align: right" >'.number_format($mostrar['ICB'], 2).'</td>
      </tr>
    
     <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
     </tr>

    <tr>
      <td colspan="4" style="text-align: right !important;">IMPORTE TOTAL:  '.$mf.'.</td>
      <td  style="text-align: right" >'.number_format($mostrar['txtTOTAL'], 2).'</td>
    </tr>
    
      <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
      </tr>
 


     <tr>
     <td  colspan="5" style="text-align: center !important; margin-top: 15px" >SON: '.numtoletras($mostrar['txtTOTAL'], $valmoneda).'
     </td>
     </tr>

    <tr>
    <td  colspan="5" style="text-align: center !important; " >Observación:'.$mostrar['txtOBSERVACION'].'</td>
   </tr>

    <tr>
    <td  colspan="5" style="text-align: center !important; " ><img src="'.RUTA.'/plugins/dompdf/'.$mempresa['ruc'].'.png" style="width: 120px; max-height: 120px; " /></td>
    </tr>
    
    <tr>
    <td  colspan="5" style="text-align: center !important; " >Representación impresa de '.$tdocumento.' Consulte su comprobante (pdf,cdr) aquí: '.RUTA.'/comprobantes/ </td> 
    </tr>
    
</table>';

