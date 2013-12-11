<?php
    include_once('code/lib/mysqlhelper.php'); $Obj=new mysqlhelper;session_start();
	$ingresa = false;$ingresa_emp=false;$nombre = $_REQUEST["txtUsuario"];$clave = $_REQUEST["txtClave"];
  	$resultado = $Obj->conexion_maestra("select sgo_int_empresa,sgo_vch_nombre,sgo_vch_servidor,sgo_vch_usuario,sgo_vch_pass,sgo_vch_basedatos from tbl_sgo_empresa where sgo_int_empresa=" . $_REQUEST["ddlEmpresa"] . " and sgo_bit_activo = 1");
    while($fila = mysqli_fetch_array($resultado))
    {
		$_SESSION['empresa'] = array("id"=>$fila['sgo_int_empresa'],"nombre"=>$fila['sgo_vch_nombre'],
        "host"=>$fila["sgo_vch_servidor"],"user"=>$fila["sgo_vch_usuario"],"pwd"=>$fila["sgo_vch_pass"],"base"=>$fila["sgo_vch_basedatos"]);
	    $ingresa_emp=true;
        break;
    }
    if($ingresa_emp){
    	$resultado = $Obj->consulta("select sgo_int_usuario, sgo_vch_nombre, sgo_vch_apellidopaterno, sgo_vch_apellidomaterno from tbl_sgo_usuario where sgo_vch_usuario = '".$nombre."' and sgo_vch_clave = '".$clave."' and sgo_bit_activo = 1");
    	if($resultado)
    	{
    		while($fila = mysqli_fetch_array($resultado))
    		{
     			$_SESSION['usuario'] = array($fila['sgo_int_usuario'],$fila['sgo_vch_nombre'],$fila['sgo_vch_apellidopaterno'],$fila['sgo_vch_apellidomaterno'],'');
    			$ingresa=true;
                break;
    		}
            $resul= $Obj->consulta("SELECT modulo.sgo_int_modulo,modulo.sgo_vch_nombre, modulo.sgo_vch_url FROM tbl_sgo_usuario usuario inner  join tbl_sgo_usuariomodulo usumod on usuario.sgo_int_usuario = usumod.sgo_int_usuario inner  join tbl_sgo_modulo modulo on    modulo.sgo_int_modulo = usumod.sgo_int_modulo where modulo.sgo_int_modulopadre	=0 and usuario.sgo_int_usuario =".$_SESSION['usuario'][0]." order by modulo.sgo_int_orden");
            $menu=array();
            while($fila = mysqli_fetch_array($resul))
    		{
    //            $menu[]=array($fila["sgo_vch_nombre"] => "'" . ($fila["sgo_vch_url"]!="#"?$fila["sgo_vch_url"] ."?app=" . $fila["sgo_int_modulo"]:$fila["sgo_vch_url"]) . "'");
                  $menu[$fila["sgo_vch_nombre"]]="'../" . ($fila["sgo_vch_url"]!="#"?$fila["sgo_vch_url"] ."?app=" . $fila["sgo_int_modulo"]:$fila["sgo_vch_url"]) . "'";
    //            $menu[$fila["sgo_vch_nombre"]]=$fila["sgo_int_modulo"] . "|'../" . ($fila["sgo_vch_url"]!="#"?$fila["sgo_vch_url"] ."?app=" . $fila["sgo_int_modulo"]:$fila["sgo_vch_url"]) . "'";
    //            array_push($menu,$aux);
    		}
            $_SESSION["usuario"][4]=$menu;
    	}
    	if($ingresa){
    		echo '<script language="javascript" type="text/javascript">parent.location.href="moduls/general/index.php";</script>';
    	}
    	else{
    		echo '<script language="javascript" type="text/javascript">parent.document.getElementById("div_error").innerHTML="' . htmlentities("El usuario o la contraseña son incorrectos.") . '"</script>';
     	}
    }
    else{
    		echo '<script language="javascript" type="text/javascript">parent.document.getElementById("div_error").innerHTML="' . htmlentities("La empresa seleccionada no es válida") . '"</script>';
     	}
?>