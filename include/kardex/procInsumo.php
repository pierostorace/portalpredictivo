<?php
	include('../db.php');
		
	$id 	= $_POST["id"];
	$modo 	= $_POST["modo"];
	$app	= $_POST["app"];
	
	$nombre	 = $_POST["txtNombre"];
	if(isset($_POST["chkActivo"]))
	{
		$estado = 1;
	}
	else
	{
		$estado = 2;
	}
	
		if($modo==1) // Agregar un nuevo usuario
		{
			$consulta = "INSERT INTO TBL_PTM_CATEGORIA (PTM_VCH_NOMBRE, PTM_INT_ESTADO) VALUES ";	
			$consulta = $consulta."('".$nombre."',".$estado.")";	

			if(!mysql_query($consulta, $conn))
			{
?>
			<script language="javascript" type="text/javascript">
				location.href = "categoria.php?res=3";
			</script>                
<?php	
			}
			else
			{
?>
			<script language="javascript" type="text/javascript">
				location.href = "categoria.php?res=1";
			</script>                
<?php	
			}
		}
		else // Grabar los datos de la edición de una publicación
		{
			$consulta = "UPDATE TBL_PTM_CATEGORIA SET ";
			$consulta = $consulta." PTM_VCH_NOMBRE 	= '".$nombre."' ,";
			$consulta = $consulta." PTM_INT_ESTADO 	= ".$estado;
			$consulta = $consulta." WHERE PTM_INT_CATEGORIA = ".$codigo;
			
			
			if(!mysql_query($consulta, $conn))
			{
?>
			<script language="javascript" type="text/javascript">
				location.href = "categoria.php?res=3";
			</script>                
<?php	
			}
			else
			{
?>
			<script language="javascript" type="text/javascript">
				location.href = "categoria.php?res=2";
			</script>                
<?php	
			}
		}	
?>