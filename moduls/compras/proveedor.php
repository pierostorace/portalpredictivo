<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_compras.php'); $Obj=new bl_compras;?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> Catálogo de Proveedores</td>
  </tr>
  <tr>
      <td class="pag_zona_sup">
        	<?php echo $Obj->Filtros_Listar_Proveedor();?>
      </td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_inf">
      	<?php echo $Obj->Grilla_Listar_Proveedor("|");?>
      </td>
  </tr>
</table>
<?php include("../../include/pie_principal.php"); ?>