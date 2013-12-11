<?php
    include_once('code/lib/mysqlhelper.php'); $Obj=new mysqlhelper;session_start();
	$ingresa = false;$nombre = $_REQUEST["txtUsuario"];$clave = $_REQUEST["txtClave"];
	$consulta="select sgo_int_usuario, sgo_vch_nombre, sgo_vch_apellidopaterno, sgo_vch_apellidomaterno from tbl_sgo_usuario where sgo_vch_usuario = '".$nombre."' and sgo_vch_clave = '".$clave."' and sgo_bit_activo = '1'";
	$resultado = $Obj->consulta($consulta);
	if($resultado)
	{
		while($fila = mysqli_fetch_array($resultado))
		{
			$usuario = array($fila['sgo_int_usuario'],$fila['sgo_vch_nombre'],$fila['sgo_vch_apellidopaterno'],$fila['sgo_vch_apellidomaterno']);
 			$_SESSION['usuario'] = $usuario;
			$ingresa=true;
		}
	}
	if($ingresa){
		echo '<script language="javascript" type="text/javascript">parent.location.href="moduls/general/index.php";</script>';
	}
	else{
		echo '<script language="javascript" type="text/javascript">parent.document.getElementById("div_error").innerHTML="' . htmlentities("El usuario o la contraseña son incorrectos.") . '"</script>';
 	}
?>