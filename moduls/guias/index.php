<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_guiaremision.php'); $Obj=new bl_guiaremision;?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> Guías de remisión</td>
  </tr>
  <tr>
      <td class="pag_zona_sup">
        	<?php echo $Obj->Filtros_Listar_Guiasremision();?>
      </td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_inf">
      	<?php echo $Obj->Grilla_Listar_Guiasremision("||||||");?> 
      </td>
  </tr>
</table>
<iframe id='dumb' style='display:none'></iframe>
<?php include("../../include/pie_principal.php"); ?>