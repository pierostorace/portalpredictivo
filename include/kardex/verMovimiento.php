<?php session_start(); ?>
<?php include('../db.php'); ?>
<html>
<head>
<link href="../../css/galver.css" rel='stylesheet' type="text/css" />
</head>
<body topmargin="0"  bottommargin="0" bgcolor="#FFFFFF">
<form id="frmEdicion" method="post" target="_self" action="verMovimiento.php">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#FFFFFF">
	<tr class="fondoCabeceraPopup">
    	<td width="20px"></td>
    	<td height="30px" class="textoCabeceraPopup">Listado de Movimientos</td>
        <td width="20px"></td>
    </tr>
    <tr><td colspan="3" height="15px"></td></tr>
    <tr>
    	<td></td>
        <td>
        	<table border="0" cellpadding="0" cellspacing="0" width="100%">
            	<tr>
                	<td class="textoData">Desde</td>
                    <td><input type="text" name="txtDesde" value="2012-09-01" /></td>
                    <td class="textoData">Hasta</td>
                    <td><input type="text" name="txtHasta" value="2012-09-22" /></td>
                    <td><input type="button" value="Buscar" /></td>
                </tr>
                <tr><td colspan="5" height="10px"></td>
                <tr><td colspan="5"><?php include("grillaMovimiento.php") ?></td>
            </table>
        </td>
        <td></td>
    </tr>
    <tr><td colspan="3" height="15px"></td></tr>
</table>
<input type="hidden" name="hfId" value="<?php echo $_GET["id"]?>" />
</form>
</body>
</html>