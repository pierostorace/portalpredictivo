<select name="cboUnidadMedida" class="ccboL">
<?php
	$consulta="SELECT sgo_int_unidadmedida, CONCAT(sgo_vch_abreviatura,' - ', sgo_vch_descripcion) as nombre FROM tbl_sgo_unidadmedida";
	$resultado = mysql_query($consulta, $conn);
	if($resultado)
	{
		while($fila = mysql_fetch_array($resultado))
		{					
			?>
				<option value="<?php echo $fila["sgo_int_unidadmedida"]?>"  <?php if($_POST["cboUnidadMedida"] == $fila["sgo_int_unidadmedida"]? print("selected"):print("")) ?>><?php echo $fila["nombre"]?></option>
			<?php		
		}
	}
?>
</select>