<select name="cboCategoriaInsumo" class="ccboL">
<?php
	$consulta="SELECT sgo_int_categoriainsumo, sgo_vch_descripcion FROM tbl_sgo_categoriainsumo where sgo_bit_estado = '1'";
	$resultado = mysql_query($consulta, $conn);
	if($resultado)
	{
		while($fila = mysql_fetch_array($resultado))
		{					
			?>
				<option value="<?php echo $fila["sgo_int_categoriainsumo"]?>"  <?php if($_POST["cboCategoriaInsumo"] == $fila["sgo_int_categoriainsumo"]? print("selected"):print("")) ?>><?php echo $fila["sgo_vch_descripcion"]?></option>
			<?php		
		}
	}
?>
</select>