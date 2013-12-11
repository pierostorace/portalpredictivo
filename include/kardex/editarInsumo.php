<?php session_start(); ?>
<?php include('../db.php'); ?>
<?php 
	if($_GET["modo"]=="2") //editar
	{
		$sql = "select sgo_int_proveedor, sgo_int_categoriainsumo, sgo_vch_nombre, sgo_int_unidadmedida, sgo_dec_precio, sgo_dec_cantidad, sgo_dec_stock, sgo_dec_stockminimo from tbl_sgo_insumo where sgo_bit_activo = 1 and sgo_int_insumo =".$_GET["id"];
				
		$lista = mysql_query($sql, $conn);	
		if($lista)
		{
			while($fila = mysql_fetch_array($lista))
			{	
				$cboProveedor			=	$fila["sgo_int_proveedor"];
				$cboCategoriaInsumo 	= 	$fila["sgo_int_categoriainsumo"];
				$nombre					=	$fila["sgo_vch_nombre"];
				$cboUnidadMedida		= 	$fila["sgo_int_unidadmedida"];
				$precio					=	$fila["sgo_dec_precio"];
				$cantidad				=	$fila["sgo_dec_cantidad"];
				$stock					=	$fila["sgo_dec_stockminimo"];
			}
		}
	}
?>
<html>
<head>
<link href="../../css/galver.css" rel='stylesheet' type="text/css" />
</head>
<body topmargin="0"  bottommargin="0" bgcolor="#FFFFFF">
<form id="frmEdicion" method="post" target="_self" action="../include/process/gear.php">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#FFFFFF">
	<tr class="fondoCabeceraPopup">
    	<td width="20px"></td>
    	<td height="30px" class="textoCabeceraPopup">Formulario de edición de insumos</td>
        <td width="20px"></td>
    </tr>
    <tr><td colspan="3" height="15px"></td></tr>
    <tr>
    	<td></td>
        <td>
        	<table border="0" cellpadding="0" cellspacing="0" width="100%">
            	<tr>
                	<td class="textoInput">Proveedor</td>
                    <td></td>
                    <td><?php include("../combo/cboProveedor.php"); ?></td>
                </tr>
                <tr><td colspan="3" height="5px"></td></tr>
                <tr>
                	<td class="textoInput">Categoria</td>
                    <td></td>
                    <td><?php include("../combo/cboCategoriaInsumo.php"); ?></td>
                </tr>
                <tr><td colspan="3" height="5px"></td></tr>
                <tr>
                	<td class="textoInput">Nombre</td>
                    <td></td>
                    <td><input type="text" class="cajaInput" name="txtNombreInsumo" value="<?php echo $nombre?>" /></td>
                </tr>
                 <tr><td colspan="3" height="5px"></td></tr>
                <tr>
                	<td class="textoInput">UM</td>
                    <td></td>
                    <td><?php include("../combo/cboUnidadMedida.php"); ?></td>
                </tr>
                <tr><td colspan="3" height="5px"></td></tr>
                <tr>
                	<td class="textoInput">Precio</td>
                    <td></td>
                    <td><input type="text" class="cajaInput" name="txtPrecioInsumo" value="<?php echo $precio?>" /></td>
                </tr>
                <tr><td colspan="3" height="5px"></td></tr>
                <tr>
                	<td class="textoInput">Cantidad</td>
                    <td></td>
                    <td><input type="text" class="cajaInput" name="txtCantidad" value="<?php echo $cantidad?>" /></td>
                </tr>
                <tr><td colspan="3" height="5px"></td></tr>
                <tr>
                	<td class="textoInput">Stock mínimo</td>
                    <td></td>
                    <td><input type="text" class="cajaInput" name="txtStockMinimo" value="<?php echo $stock?>" /></td>
                </tr>
                <tr><td colspan="3" height="15px"></td></tr>
                <tr><td colspan="3" align="center"><input type="submit" value="Grabar" /></td></tr>
            </table>
        </td>
        <td></td>
    </tr>
    <tr><td colspan="3" height="15px"></td></tr>
</table>
<input type="hidden" name="hfAction" value="INSUMO" />
<input type="hidden" name="hfModo" value="<?php echo $_GET["modo"]?>" />
<input type="hidden" name="hfId" value="<?php echo $_GET["id"]?>" />
<input type="hidden" name="hfDestino" value="../../kardex/index.php?app=<?php echo $_GET["app"]?>" />
</form>
</body>
</html>