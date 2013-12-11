<select name="cboTipoMovimiento" class="ccboL">
<?php
	$consulta="SELECT sgo_int_tipomovimiento, sgo_vch_descripcion FROM tbl_sgo_tipomovimiento where sgo_bit_activo = '1'";
	$resultado = mysql_query($consulta, $conn);
	if($resultado)
	{
		while($fila = mysql_fetch_array($resultado))
		{					
			?>
				<option value="<?php echo $fila["sgo_int_tipomovimiento"]?>"  <?php if($_POST["cboTipoMovimiento"] == $fila["sgo_int_tipomovimiento"]? print("selected"):print("")) ?>><?php echo $fila["sgo_vch_descripcion"]?></option>
			<?php		
		}
	}
?>
</select>