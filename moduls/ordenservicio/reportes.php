<?php include("../../include/cab_principal.php");include_once('../../code/bl/bl_ordenservicio_reportes.php'); $Obj=new bl_ordenservicio_reportes;?>
<script src="../../js/highcharts.js"></script>
<script src="../../js/modules/exporting.js"></script>
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="textoUbicacion pag_titulo"> Reportes OS > <?php echo $Obj->Obtener_Nombre_Reporte($_REQUEST["opc"]); ?></td>
  </tr>
  <tr>
      <td class="pag_zona_sup">
        	<?php echo $Obj->Filtros_Reporte($_REQUEST["opc"]);?>
      </td>
  </tr>
  <tr>
      <td id="td_General" class="pag_zona_inf">
      	<?php echo $Obj->Grilla_Reporte($_REQUEST["opc"] . "|||||||");?>
      </td>
  </tr>
</table>
<?php include("../../include/pie_principal.php"); ?>