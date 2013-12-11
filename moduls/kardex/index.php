<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_kardex.php'); $Obj=new bl_kardex;?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> Kardex de Insumos/Productos</td>
  </tr>
  <tr>
      <td class="pag_zona_sup">
        	<?php echo $Obj->Filtros_Listar_kardex();?>
      </td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_inf">
      	<?php echo $Obj->Grilla_Listar_Kardex("|");?>
      </td>
  </tr>
</table>
<?php include("../../include/pie_principal.php"); ?>