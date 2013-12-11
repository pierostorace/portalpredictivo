<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_crm.php'); $Obj=new bl;$Obj_General=new bl_general; ?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> <span onmouseover="this.className='hover_home'" onmouseout="this.className=''" class="" onclick="location.href='index.php?app=<?php echo $_REQUEST["app"]; ?>'">Cat&aacute;logo de Clientes</span> > <span id="div_nombcliente"><?php if($_REQUEST["prm"]!="0")echo "Detalles de " .$Obj_General->Obtener_Nombre_Persona($_REQUEST["prm"]); else echo "Nuevo Cliente"; ?></span></td>
  </tr>
  <tr>
      <td class="pag_zona_sup">
        	<?php echo $Obj->Tabs_Detalles_Cliente($_REQUEST["prm"]);?>
      </td>
  </tr>
</table>
<?php include("../../include/pie_principal.php"); ?>