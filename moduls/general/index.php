<?php include("../../include/cab_principal.php");include_once('../../code/lib/mysqlhelper.php'); $Obj=new mysqlhelper;?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
<tr>
	<td height="430px" style="padding-left:50px" valign="top" align="left">
    	<table border="0" cellpadding="5" cellspacing="0" width="100%">
        <?php
        	$result = $Obj->consulta("SELECT modulo.sgo_int_modulo, modulo.sgo_vch_nombre, modulo.sgo_vch_imagen, modulo.sgo_vch_url FROM   tbl_sgo_usuario usuario inner  join tbl_sgo_usuariomodulo usumod on usuario.sgo_int_usuario = usumod.sgo_int_usuario inner  join tbl_sgo_modulo modulo on    modulo.sgo_int_modulo = usumod.sgo_int_modulo where modulo.sgo_int_modulopadre = 0 and usuario.sgo_int_usuario =".$usuario[0]." order by modulo.sgo_int_orden;");
        	if($result){ $contador = 0;
				while($obj = mysqli_fetch_array($result)){
    				if($contador%5==0){echo("<tr>");}
                    echo '<td width="275px" align="left"><a href="../' . $obj['sgo_vch_url'] . '?app=' . $obj['sgo_int_modulo'] . '"><img src="../../img/' . $obj['sgo_vch_imagen'] . '" border="0" width="204px" height="217px" /></a></td>';
    				$contador++; if($contador%5==0){echo("</tr>");}
				}
			}
		?>
        </table>
    </td>
</tr>
</table>
<?php include("../../include/pie_principal.php"); ?>