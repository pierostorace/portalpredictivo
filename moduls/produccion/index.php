<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_ordenproduccion.php'); $Obj=new bl_ordenproduccion;?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> Catálogo de órdenes de producción</td>
  </tr>
  <tr>
      <td class="pag_zona_sup">
        	<?php echo $Obj->Filtros_Listar_OrdenProduccion();?>
      </td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_inf">
      	<?php echo $Obj->Grilla_Listar_OrdenProduccion("||||1");?>
      </td>
  </tr>
</table>
<?php include("../../include/pie_principal.php"); ?>