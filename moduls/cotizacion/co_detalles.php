<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_cotizacion.php'); $Obj=new bl_cotizacion;?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> <span onmouseover="this.className='hover_home'" onmouseout="this.className=''" class="" onclick="location.href='index.php?app=<?php echo $_REQUEST["app"]; ?>'">Catálogo de Cotizaciones</span> > <?php echo "Detalles de la cotización " . str_pad($_REQUEST["prm"],6,'0', STR_PAD_LEFT); ?></td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_sup">
      	<?php echo $Obj->Grilla_Listar_Cotizacion_Productos($_REQUEST["prm"]);?>
      </td>
  </tr>
</table>
<?php include("../../include/pie_principal.php"); ?>