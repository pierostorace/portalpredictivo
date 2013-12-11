<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_ordenservicio.php'); $Obj=new bl_ordenservicio;?>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo">  <span onmouseover="this.className='hover_home'" onmouseout="this.className=''" class="" onclick="location.href='index.php?app=<?php echo $_REQUEST["app"]; ?>'">Catálogo de Órdenes de Servicio</span> > <?php echo "Detalles de la orden " .$Obj->Obtener_Nombre_OrdenServicio($_REQUEST["prm"]); ?></td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_sup">
      	<?php echo $Obj->Grilla_Listar_OrdenesServicio_Detalles($_REQUEST["prm"]);?>
      </td>
  </tr>
</table>
<?php include("../../include/pie_principal.php"); ?>