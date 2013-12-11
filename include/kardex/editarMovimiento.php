<?php session_start(); ?>
<?php include('../db.php'); ?>
<html>
<head>
<link href="../../css/galver.css" rel='stylesheet' type="text/css" />
</head>
<body topmargin="0"  bottommargin="0" bgcolor="#FFFFFF">
<form id="frmEdicion" method="post" target="_self" action="../include/process/gear.php">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#FFFFFF">
	<tr class="fondoCabeceraPopup">
    	<td width="20px"></td>
    	<td height="30px" class="textoCabeceraPopup">Registro de movimiento de insumos</td>
        <td width="20px"></td>
    </tr>
    <tr><td colspan="3" height="15px"></td></tr>
    <tr>
    	<td></td>
        <td>
        	<table border="0" cellpadding="0" cellspacing="0" width="100%">
            	<tr>
                	<td class="textoInput">Tipo</td>
                    <td></td>
                    <td><?php include("../combo/cboTipoMovimiento.php"); ?></td>
                </tr>
                <tr><td colspan="3" height="5px"></td></tr>
                <tr>
                	<td class="textoInput">OP</td>
                    <td></td>
                    <td><?php include("../combo/cboOrdenProduccion.php"); ?></td>
                </tr>
                <tr><td colspan="3" height="5px"></td></tr>
                <tr>
                	<td class="textoInput">Cantidad</td>
                    <td></td>
                    <td><input type="text" class="cajaInput" name="txtCantidad" /></td>
                </tr>
                <tr><td colspan="3" height="5px"></td></tr>
                <tr>
                	<td class="textoInput">Observaciones</td>
                    <td></td>
                    <td><textarea name="txtObservaciones" cols="42" rows="5"></textarea></td>
                </tr>
                <tr><td colspan="3" height="15px"></td></tr>
                <tr><td colspan="3" align="center"><input type="submit" value="Grabar" /></td></tr>
            </table>
        </td>
        <td></td>
    </tr>
    <tr><td colspan="3" height="15px"></td></tr>
</table>
<input type="hidden" name="hfAction" value="MOVIMIENTO" />
<input type="hidden" name="hfModo" value="<?php echo $_GET["modo"]?>" />
<input type="hidden" name="hfId" value="<?php echo $_GET["id"]?>" />
<input type="hidden" name="hfDestino" value="../../kardex/index.php?app=<?php echo $_GET["app"]?>" />
</form>
</body>
</html>