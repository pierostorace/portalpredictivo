<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_facturacion.php'); $Obj=new bl_facturacion;?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> Documentos de Venta</td>
  </tr>
  <tr>
      <td class="pag_zona_sup">
        	<?php echo $Obj->Filtros_Listar_ComprobanteVenta();?>
      </td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_inf">
      	<?php echo $Obj->Grilla_Listar_ComprobanteVenta("|||||||");?>
      </td>
  </tr>
</table>
<iframe id='dumb' style='display:none'></iframe>
<?php include("../../include/pie_principal.php"); ?>