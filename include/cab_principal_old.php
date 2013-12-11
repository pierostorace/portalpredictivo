<?php  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title>GALVER SAC</title>
<link rel="icon" type="image/png" href="../../img/favicon.ico" />
<link href="../../css/style.css" rel='stylesheet' type="text/css" />
<script src="../../js/jscript_inicio.js"></script>
</head>
<?php session_start(); include_once('../../include/seguridad.php'); include_once('../../code/lib/htmlhelper.php'); $Helper=new htmlhelper; ?>
<body topmargin="0" bottommargin="0">
<center>
<div id='Barra_info' style='width:100%;display:none;height:0px;z-Index:9999999'><table width='100%' cellpadding='3px' cellspacing="0" ><tr><td style='width:18px'><img src='../../img/information.png' width='16px' height='16px' /></td><td id='td_Barra_info'></td><td onclick='clearTimeout(notificacion);Ocultar_Barra_info()' class='cerrar'></td></tr></table></div>
<div id="master" style="background-color:#FFFFFF;width:100%;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="120px">
	<tr>
        <td style="padding-left:10px;width:200px;" valign="top"><img src="../../img/logo.jpg" width="179px" height="95px"></td>
        <td valign="top">
        	<table border="0" cellpadding="0" cellspacing="0" style="padding-right:20px;float:right" >
                <tr><td style="padding-top:10px" class="textoInput">Bienvenido: <span class="negrita"><?php echo $usuario[1]." ".$usuario[2]; ?> </span></td></tr>
                <tr><td style="padding-top:5px" class="textoInput"><?php echo $Helper->Fecha_Castellano(time()); ?></td></tr>
            </table>
        </td>
    </tr>
    <tr>
<!--    	<td height="44px" align="center" background="../../img/barra.jpg" style="font-family:Segoe UI, Tahoma;font-size:16px;color:#FFF;">Menú de Opciones</td>-->
    	<td colspan="2" height="44px" background="../../img/barra.jpg">
        <?php if(isset($_GET["app"])){
    		echo '<table border="0" cellpadding="0" cellspacing="0"><tr><td>';
    		$Helper->Menu($usuario[0],$_GET["app"]);
            echo '<div id="menu" align="center"></div></td></tr></table>';}
        ?>
    	</td>
    </tr>
    <tr>
<!--        <td valign="top" width="200px">
          <div width="220px" style="background-color:#F3F3EF">
            <div width="100%">
                <div onclick="AutoDisplay('div_CRM')" class="link titulo_menu"><div style="padding-top:10px;"><img src="../../img/vineta_blanco.gif" width="7px" height="4px" align="center" />&nbsp;&nbsp;CRM</div></div>
                <div id="div_CRM" style="display:none">
                    <div class="link titulo_submenu"><img src="../../img/vineta_submenu.gif" width="7px" height="4px" align="center" />&nbsp;&nbsp;Clientes</div>
                    <div class="link titulo_submenu"><img src="../../img/vineta_submenu.gif" width="7px" height="4px" align="center" />&nbsp;&nbsp;Visitas</div>
                </div>
            </div>-->
            <?php
        		//echo $Helper->Menu_Vertical($usuario[4]);
            ?>
          <!--</div>
        </td>-->
        <td colspan="2" valign="top" align="center"><div style="min-height:450px">