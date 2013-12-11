<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_ordencompra.php'); $Obj=new bl_ordencompra;?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> Catálogo de Órdenes de Compra</td>
  </tr>
  <tr>
      <td class="pag_zona_sup">
        	<?php echo $Obj->Filtros_Listar_OrdenesCompra();?>
      </td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_inf">
      	<?php echo $Obj->Grilla_Listar_OrdenesCompra("||||||");?>
      </td>
  </tr>
</table>
<?php include("../../include/pie_principal.php"); ?>