<?php include("../../include/cab_principal.php");
include_once('../../code/bl/bl_reporte.php'); $Obj=new bl_reporte;?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> Reporte de Ventas</td>
  </tr>
  <tr>
      <td class="pag_zona_sup">
        	<?php echo $Obj->Filtros_Listar_Reporte_Ventas();?>
      </td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_inf">
      	<?php echo $Obj->Grilla_Listar_Reporte_Ventas("|||||||");?>
      </td>
  </tr>
</table>
<iframe id='dumb' style='display:none'></iframe>
<?php include("../../include/pie_principal.php"); ?>