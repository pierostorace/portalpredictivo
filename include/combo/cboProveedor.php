<select name="cboProveedor" class="ccboL">
<?php
	$consulta="SELECT sgo_int_proveedor, sgo_vch_razonsocial FROM tbl_sgo_proveedor where sgo_bit_activo = '1'";
	$resultado = mysql_query($consulta, $conn);
	if($resultado)
	{
		while($fila = mysql_fetch_array($resultado))
		{					
			?>
				<option value="<?php echo $fila["sgo_int_proveedor"]?>"  <?php if($_POST["cboProveedor"] == $fila["sgo_int_proveedor"]? print("selected"):print("")) ?>><?php echo $fila["sgo_vch_razonsocial"]?></option>
			<?php		
		}
	}
?>
</select>