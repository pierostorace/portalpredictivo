<table border="0" cellpadding="0" cellspacing="0" width="1200px" height="120px" bgcolor="#FFFFFF">
	<tr>
    	<td width="20px"></td>
        <td valign="top"><img src="../../img/logo.jpg" width="182px" height="110px"></td>
        <td width="693px"></td>
        <td width="305px" align="left" valign="top">
        	<table border="0" cellpadding="0" cellspacing="0" width="305px">
            	<tr><td height="10px"></td></tr>
                <tr><td class="textoInput">Bienvenido: <?php echo $usuario[1]." ".$usuario[2]; ?> </td></tr>
                <tr><td height="5px"></td></tr>
                <tr><td class="textoInput"><?php echo date("d/m/Y"); ?></td></tr>
            </table>
        </td>
    </tr>
    <tr>
    	<td colspan="4" height="44px" background="../../img/barra.jpg" width="1200px">
    		<table border="0" cellpadding="0" cellspacing="0">
            <tr><td width="20px"></td>
            	<td>
<?php include_once('../../code/lib/htmlhelper.php'); $Obj=new htmlhelper;$Obj->Menu($usuario[0],$_GET["app"]); ?>
                <div id="menu"></div>
                </td>
            </tr>
            </table>
    	</td>
    </tr>
</table>