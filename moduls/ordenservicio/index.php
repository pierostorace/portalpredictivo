<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_ordenservicio.php'); $Obj=new bl_ordenservicio;?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> Catálogo de Órdenes de Servicio</td>
  </tr>
  <tr>
      <td class="pag_zona_sup">
        	<?php echo $Obj->Filtros_Listar_OrdenesServicio();?>
      </td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_inf">
      	<?php echo $Obj->Grilla_Listar_OrdenesServicio("||||||");?>
      </td>
  </tr>
</table>
<?php include("../../include/pie_principal.php"); ?>