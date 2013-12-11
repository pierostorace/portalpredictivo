<?php
    $mensaje="";
	$respuesta = (isset($_REQUEST["res"])?$_REQUEST["res"]: 0);

	if($respuesta!="")
	{
		if($respuesta==1)
		{
			$mensaje="Se realizó la operación con éxito!";
			?>
			<script language="javascript" type="text/javascript">
				document.getElementById("dvMensajeContenedor").style.visibility='visible';
				document.getElementById("dvMensaje").innerHTML = <?php echo $mensaje; ?>;
				setTimeout("cerrarMensaje()",5000);
			</script>
			<?php
		}
		if($respuesta==2)
		{
			$mensaje="Ocurrió una falla en el proceso y la operación no pudo concretarse. Por favor, intentelo nuevamente o póngase en contacto con el proveedor";
			?>
			<script language="javascript" type="text/javascript">
				document.getElementById("dvMensajeContenedor").style.visibility="visible";
				document.getElementById("dvMensaje").innerHTML = <?php echo $mensaje; ?>;
				setTimeout("cerrarMensaje()",5000);
			</script>
			<?php
		}
		if($respuesta==3)
		{
			$mensaje="El usuario o la contraseña son incorrectos.";
			?>
			<script language="javascript" type="text/javascript">
				document.getElementById("dvMensajeContenedor").style.visibility="visible";
				document.getElementById("dvMensaje").innerHTML = <?php echo $mensaje; ?>;
				setTimeout("cerrarMensaje()",5000);
			</script>
			<?php
		}
		if($respuesta==4)
		{
			$mensaje="Usted intentó ingresar a una opción para la cual no tiene permisos asignados. Por seguridad, ha sido redireccionado al inicio del sistema.";
			?>
			<script language="javascript" type="text/javascript">
				document.getElementById("dvMensajeContenedor").style.visibility="visible";
				document.getElementById("dvMensaje").innerHTML = <?php echo $mensaje; ?>;
				setTimeout("cerrarMensaje()",5000);
			</script>
			<?php
		}
	}
?>