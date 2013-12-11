<?php
	$sql = "select insumo.sgo_int_insumo, categoria.sgo_vch_descripcion, insumo.sgo_vch_nombre, unidad.sgo_vch_abreviatura, insumo.sgo_dec_stock, insumo.sgo_dec_stockminimo, insumo.sgo_dec_precio, (insumo.sgo_dec_precio * insumo.sgo_dec_stock) as total from 	tbl_sgo_insumo insumo inner	join tbl_sgo_categoriainsumo categoria on insumo.sgo_int_categoriainsumo = categoria.sgo_int_categoriainsumo inner join tbl_sgo_unidadmedida unidad on insumo.sgo_int_unidadmedida = unidad.sgo_int_unidadmedida where sgo_bit_activo = 1";
	/*if($_POST["cboCategoriaInsumo"]>0)
	{
		$sql = $sql." and insumo.sgo_int_categoriainsumo = ".$_POST["cboCategoriaInsumo"];
	}
	if($txtNombreFiltro!="")
	{
		$sql = $sql." and insumo.sgo_vch_nombre like '%".$_POST["txtNombreFiltro"]."%'";
	} */
	$lista = $Obj->consulta($sql);
?>

<table border="0" cellpadding="0" cellspacing="0" width="1170px">
    <tr class="cabeceraTablaResultado">
        <td width="10px"></td>
        <td width="20px" height="37px"><a href="javascript:popup('../include/kardex/editarInsumo.php',0,1,<?php echo $_GET["app"]; ?>);" style="text-decoration:none;" title="Presione aquí para agregar un insumo nuevo">+</a></td>
        <td width="20px"></td>
        <td width="20px"></td>
        <td width="20px"></td>
        <td align="center">Categoría</td>
        <td align="center">Nombre</td>
        <td align="center">Unidad de Medida</td>
        <td align="center">Stock</td>
        <td align="center">Stock Mínimo</td>
        <td align="center">Precio Unitario</td>
        <td align="center">Total</td>
        <td align="center">Registrar Movimiento</td>
    </tr>
    <?php
		$cont=0;
		if($lista)
		{
			while($fila = mysqli_fetch_array($lista))
			{
			$cont++;
	?>
    		 <tr>
             	<td width="10px"></td>
                <td width="20px" height="37px"></td>
                <td width="20px" align="center"><a href="javascript:popup('../../include/kardex/editarInsumo.php',<?php echo $fila["sgo_int_insumo"]?>,2,<?php echo $_GET["app"]?>);" style="text-decoration:none;"><img src="../../img/b_editar.gif" border="0" title="Presione aquí para editar este insumo" /></a></td>
                <td width="20px" align="center"><a href="javascript:eliminarInsumo(<?php echo $fila["sgo_int_insumo"]?>);" style="text-decoration:none;"><img src="../../img/b_eliminar.gif" border="0" title="Presione aquí para eliminar este insumo" /></a></td>
                <td width="20px"></td>
                <td class="textoData" align="center"><?php echo $fila["sgo_vch_descripcion"]?></td>
                <td class="textoData" align="center"><a href="javascript:popup('../../include/kardex/verMovimiento.php',<?php echo $fila["sgo_int_insumo"]?>,0,<?php echo $_GET["app"]?>);"><?php echo $fila["sgo_vch_nombre"]?></a></td>
                <td class="textoData" align="center"><?php echo $fila["sgo_vch_abreviatura"]?></td>
                <td class="textoData" align="center"><?php echo $fila["sgo_dec_stock"]?></td>
                <td class="textoData" align="center"><?php echo $fila["sgo_dec_stockminimo"]?></td>
                <td class="textoData" align="center"><?php echo $fila["sgo_dec_precio"]?></td>
                <td class="textoData" align="center"><?php echo $fila["total"]?></td>
                <td align="center"><a href="javascript:popup('../../include/kardex/editarMovimiento.php',<?php echo $fila["sgo_int_insumo"]?>,1,<?php echo $_GET["app"]?>);" style="text-decoration:none;"><img src="../../img/gear.jpg" border="0" title="Presione aquí para registrar un movimiento en el Kardex de este insumo"></a></td>
             </tr>
             <tr><td colspan="13" height="1px" style="background-color:#0087B6;"></td></tr>
    <?php
			}
		}
		if($cont==0)
		{
	?>
    	<tr><td colspan="13" height="30px"></td></tr>
    	<tr><td colspan="13" align="center" class="textoData">No se han encontrado resultados para la búsqueda</td></tr>
        <tr><td colspan="13" height="10px"></td></tr>
    <?php
		}
	?>
</table>