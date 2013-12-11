<?php	
	$sql = "SELECT 	movimiento.sgo_dat_fecha,tipo.sgo_vch_descripcion,concat(orden.sgo_vch_codigo,' - ',cliente.sgo_vch_razonsocial) as orden,		concat(cast(movimiento.sgo_dec_cantidad as char),' ',unidad.sgo_vch_descripcion) as cantidad,		concat(usuario.sgo_vch_nombre,' ',usuario.sgo_vch_apellidopaterno) as usuario from 	tbl_sgo_insumomovimiento movimiento inner	join tbl_sgo_tipomovimiento tipo on		movimiento.sgo_int_tipomovimiento = tipo.sgo_int_tipomovimiento inner	join tbl_sgo_ordenproduccion orden on		orden.sgo_int_ordenproduccion = movimiento.sgo_int_ordenproduccion inner	join tbl_sgo_cliente cliente on		cliente.sgo_int_cliente = orden.sgo_int_cliente inner	join tbl_sgo_insumo insumo on movimiento.sgo_int_insumo = insumo.sgo_int_insumo inner	join tbl_sgo_unidadmedida unidad on	unidad.sgo_int_unidadmedida = insumo.sgo_int_unidadmedida inner	join tbl_sgo_usuario usuario on	movimiento.sgo_int_usuario = usuario.sgo_int_usuario";
	$sql = $sql." where movimiento.sgo_int_insumo = ".$_GET["id"];
	if($txtDesde!="")
	{
		$sql = $sql." and movimiento.sgo_dat_fecha >='".$_POST["txtDesde"]."'";
	}
	if($txtHasta!="")
	{
	 	$sql = $sql." and movimiento.sgo_dat_fecha <='".$_POST["txtHasta."]."'";
	}
	$lista = mysql_query($sql, $conn);	
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr class="cabeceraTablaResultado">
        <td align="center" height="25px">Fecha</td>
        <td align="center">Tipo</td>
        <td align="center">OP</td>
        <td align="center">Cantidad</td>
        <td align="center">Responsable</td>
    </tr>
    <?php
		$cont=0;
		if($lista)
		{
			while($fila = mysql_fetch_array($lista))
			{	
			$cont++;										
	?>   
    		 <tr>
                <td class="textoData" align="center"><?php echo $fila["sgo_dat_fecha"]?></td>
                <td class="textoData" align="center"><?php echo $fila["sgo_vch_descripcion"]?></td>
                <td class="textoData" align="center"><?php echo $fila["orden"]?></td>
                <td class="textoData" align="center"><?php echo $fila["cantidad"]?></td>
                <td class="textoData" align="center"><?php echo $fila["usuario"]?></td>
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