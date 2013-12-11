<select name="cboOrdenProduccion" class="ccboL">
<?php
	$consulta="SELECT op.sgo_int_ordenproduccion, CONCAT(op.sgo_vch_codigo,' - ', cliente.sgo_vch_razonsocial) as orden FROM tbl_sgo_ordenproduccion op INNER JOIN tbl_sgo_cliente cliente on op.sgo_int_cliente = cliente.sgo_int_cliente";
	$resultado = mysql_query($consulta, $conn);
	if($resultado)
	{
		while($fila = mysql_fetch_array($resultado))
		{					
			?>
				<option value="<?php echo $fila["sgo_int_ordenproduccion"]?>"  <?php if($_POST["cboOrdenProduccion"] == $fila["sgo_int_ordenproduccion"]? print("selected"):print("")) ?>><?php echo $fila["orden"]?></option>
			<?php		
		}
	}
?>
</select>