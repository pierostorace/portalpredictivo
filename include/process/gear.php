<?php session_start(); ?>
<?php include('../seguridad.php'); ?>
<?php include('../db.php'); ?>
<?php
	$sql		= "";
	$action 	= $_POST["hfAction"];	
	$modo 		= $_POST["hfModo"];
	$codigo 	= $_POST["hfId"];
	$destino 	= $_POST["hfDestino"];

	//*********************************************************** INSUMOS **************************************************************************
	if($action=="INSUMO")
	{
		if($modo==1) //agregar
		{
			$sql = "INSERT INTO tbl_sgo_insumo (sgo_int_proveedor,sgo_int_categoriainsumo,sgo_int_unidadmedida,sgo_vch_nombre,sgo_dec_precio,sgo_dec_cantidad,sgo_dec_stock,sgo_dec_stockminimo, sgo_bit_activo)";
			$sql = $sql." values (".$_POST["cboProveedor"].",".$_POST["cboCategoriaInsumo"].",".$_POST["cboUnidadMedida"].",'".$_POST["txtNombreInsumo"]."',".$_POST["txtPrecioInsumo"].",".$_POST["txtCantidad"].",".$_POST["txtCantidad"].",".$_POST["txtStockMinimo"].",1)";
		}
		if($modo==2) //editar
		{
			$sql = "UPDATE tbl_sgo_insumo set ";
			$sql = $sql." sgo_int_proveedor 		= ".$_POST["cboProveedor"].",";
			$sql = $sql." sgo_int_categoriainsumo 	= ".$_POST["cboCategoriaInsumo"].",";
			$sql = $sql." sgo_int_unidadmedida 		= ".$_POST["cboUnidadMedida"].",";
			$sql = $sql." sgo_vch_nombre 			= '".$_POST["txtNombreInsumo"]."',";
			$sql = $sql." sgo_dec_precio 			= ".$_POST["txtPrecioInsumo"].",";
			$sql = $sql." sgo_dec_cantidad 			= ".$_POST["txtCantidad"].",";
			$sql = $sql." sgo_dec_stock 			= ".$_POST["txtCantidad"].",";
			$sql = $sql." sgo_dec_stockminimo 		= ".$_POST["txtStockMinimo"].",";
			$sql = $sql." sgo_bit_activo     		= 1";
			$sql = $sql." where sgo_int_insumo=".$codigo;
		}
		if($modo==3) //eliminar
		{
			$sql = "UPDATE tbl_sgo_insumo set ";
			$sql = $sql." sgo_bit_activo     		= 0";
			$sql = $sql." where sgo_int_insumo=".$codigo;
		}
	}
	
	if($action=="MOVIMIENTO")
	{
		if($modo==1) //agregar
		{
			$sql = "INSERT INTO tbl_sgo_insumomovimiento(sgo_int_insumo,sgo_int_ordenproduccion,sgo_dec_cantidad,sgo_vch_observaciones,sgo_dat_fecha,sgo_int_usuario,sgo_int_tipomovimiento)";
			$sql = $sql." values (".$codigo.",".$_POST["cboOrdenProduccion"].",".$_POST["txtCantidad"].",'".$_POST["txtObservaciones"]."','".date("Y-m-d")."',".$usuario[0].",".$_POST["cboTipoMovimiento"].");";
		}
	}
	if($sql!="")
	{
		if(!mysql_query($sql, $conn))
		{
			print(mysql_error());
?>
			<script language="javascript" type="text/javascript">
				location.href = "<?php echo $destino?>" + "&res=2";
			</script>
<?php
		}
		else
		{
?>
			<script language="javascript" type="text/javascript">
				location.href = "<?php echo $destino?>" + "&res=1";
			</script>
<?php
		}
	}
?>