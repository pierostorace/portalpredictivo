<?php
	$consulta="SELECT modulo.sgo_int_modulo, modulo.sgo_vch_nombre, modulo.sgo_vch_imagen, modulo.sgo_vch_url FROM tbl_sgo_usuario usuario inner  join tbl_sgo_usuariomodulo usumod on usuario.sgo_int_usuario = usumod.sgo_int_usuario inner  join tbl_sgo_modulo modulo on    modulo.sgo_int_modulo = usumod.sgo_int_modulo where usuario.sgo_int_usuario =".$usuario[0]." and sgo_int_modulopadre = ".$_GET["app"]." order by modulo.sgo_int_orden;";
	$resultado = $Obj->consulta($consulta);
?>
<script>
//un array por cada uno de los menús desplegables
var opciones_menu = [
					{
						texto: "Inicio",
						url: "../../principal.php",
						enlaces: []
					},
		<?php
			if($resultado)
			{
  			    $cont=0;
			    $Obj_aux=new mysqlhelper();
				while($fila = mysqli_fetch_array($resultado))
				{
		?>
					{
						texto: "<?php echo $fila["sgo_vch_nombre"];?>",
						url: "<?php echo $fila["sgo_vch_url"]."?app=".$_GET["app"]; ?>",
						enlaces: [
							<?php
								$subconsulta="SELECT modulo.sgo_int_modulo, modulo.sgo_vch_nombre, modulo.sgo_vch_imagen, modulo.sgo_vch_url FROM tbl_sgo_usuario usuario inner  join tbl_sgo_usuariomodulo usumod on usuario.sgo_int_usuario = usumod.sgo_int_usuario inner  join tbl_sgo_modulo modulo on    modulo.sgo_int_modulo = usumod.sgo_int_modulo where usuario.sgo_int_usuario =".$usuario[0]." and sgo_int_modulopadre = ".$fila["sgo_int_modulo"]." order by modulo.sgo_int_orden;";
								$subresultado = $Obj_aux->consulta($subconsulta);
									if($subresultado)
									{
										while($subfila = mysqli_fetch_array($subresultado))
										{
											?>
												{
													texto: "<?php echo $subfila["sgo_vch_nombre"];?>",
													url: "<?php echo $subfila["sgo_vch_url"]."?app=".$_GET["app"]."&mod=".$subfila["sgo_int_modulo"] ?>"
												},
											<?php
										}
									}
							?>
						]
					}
		<?php
					if(mysqli_num_rows($resultado)!=$cont){
					?>
					,
					<?php
					}
					$cont++;
				}
			}

		?>
];

//cuando el navegador esté listo
$(document).ready(function(){
	//lanzo el menú llamando al plugin
	$("#menu").generaMenu(opciones_menu);
})
</script>
<div id="menu"></div>