<?php  header('Content-type: text/html; charset=utf-8'); 
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();?>
<?php 
    $title="INICIO";
	if(isset($_GET["app"]))
	{
		switch ($_GET["app"])
		{
			case 1:$title="CRM";
			break;
			case 21:$title="COTIZACIONES";
			break;
			case 2:$title="OS";
			break;
			case 22:$title="PRODUCCI&#211;N";
			break;
			case 3:$title="GUIAS";
			break;
			case 4:$title="FACTURACI&#211;N";
			break;
			case 5:$title="FINANZAS";
			break;
			case 32:$title="COMPRAS";
			break;
			case 6:$title="ALMAC&#201;N";
			break;
			case 7:$title="REPORTES";
			break;
		}	
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo $title?> - GALVER SAC</title>
<link rel="icon" type="image/png" href="../../img/favicon.ico" />
<link href="../../css/style.css" rel='stylesheet' type="text/css" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"></script>
<script src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script src="../../js/jscript_framework.js"></script>
<script src="../../js/sorttable.js"></script>



<script>
(function($){$.fn.generaMenu=function(menu){this.each(function(){var retardo,capaMenu=$(this);var listaPrincipal=$('<ul></ul>');capaMenu.append(listaPrincipal);var arrayEnlaces=[],arrayCapasSubmenu=[],arrayLiMenuPrincipal=[];jQuery.each(menu,function(){var elementoPrincipal=$('<li></li>');listaPrincipal.append(elementoPrincipal);var enlacePrincipal=$('<a href="'+this.url+'">'+this.texto+'</a>');elementoPrincipal.append(enlacePrincipal);var capaSubmenu=$('<div class="submenu"></div>');
if(this.enlaces.length>0){enlacePrincipal.data("capaSubmenu",capaSubmenu);var subLista=$('<ul></ul>');capaSubmenu.append(subLista);jQuery.each(this.enlaces,function(){var subElemento=$('<li></li>');subLista.append(subElemento);var subEnlace=$('<a href="'+this.url+'">'+this.texto+'</a>');subElemento.append(subEnlace);});}$(document.body).append(capaSubmenu);enlacePrincipal.mouseover(function(e){var enlace=$(this);clearTimeout(retardo)
ocultarTodosSubmenus();submenu=enlace.data("capaSubmenu");if(submenu)submenu.css("display","block");});enlacePrincipal.mouseout(function(e){var enlace=$(this);submenu=enlace.data("capaSubmenu");clearTimeout(retardo);if(submenu)retardo=setTimeout("submenu.css('display', 'none');",1000)});capaSubmenu.mouseover(function(){clearTimeout(retardo);})
capaSubmenu.mouseout(function(){clearTimeout(retardo);if(submenu){submenu=$(this);retardo=setTimeout("submenu.css('display', 'none');",1000)}})
if(arrayEnlaces.length==0){$(window).resize(function(){colocarCapasSubmenus();});}
function ocultarTodosSubmenus(){$.each(arrayCapasSubmenu,function(){this.css("display","none");});}
function colocarCapasSubmenus(){$.each(arrayCapasSubmenu,function(i){var posicionEnlace=arrayLiMenuPrincipal[i].offset();this.css({left:posicionEnlace.left,top:posicionEnlace.top+28});});}
arrayEnlaces.push(enlacePrincipal);arrayCapasSubmenu.push(capaSubmenu);arrayLiMenuPrincipal.push(elementoPrincipal);colocarCapasSubmenus();});});return this;};})(jQuery);
</script>
</head>
<?php session_start(); include_once('../../include/seguridad.php'); include_once('../../code/lib/htmlhelper.php'); $Helper=new htmlhelper; ?>
<body topmargin="0" bottommargin="0">
<center>
<div id='Barra_info' style='width:100%;display:none;height:0px;z-Index:9999999'><table width='100%' cellpadding='3px' cellspacing="0" ><tr><td style='width:18px'><img src='../../img/information.png' width='16px' height='16px' /></td><td id='td_Barra_info'></td><td onclick='clearTimeout(notificacion);Ocultar_Barra_info()' class='cerrar'></td></tr></table></div>
<div id="master" style="background-color:#FFFFFF;width:100%;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="120px">
	<tr>
        <td style="padding-left:10px;width:180px;height:96px" valign="top"><img src="../../img/logo.jpg" width="179px" height="95px"></td>
        <td valign="top">
        	<table border="0" cellpadding="0" cellspacing="0" style="padding-right:20px;float:right" >
                <tr><td style="padding-top:10px" class="textoInput">Empresa: <span class="negrita"><?php echo $_SESSION["empresa"]["nombre"]; ?> </span></td></tr>
                <tr><td style="padding-top:5px" class="textoInput">Bienvenido: <span class="negrita"><?php echo $usuario[1]." ".$usuario[2]; ?> </span></td></tr>
                <tr><td style="padding-top:5px" class="textoInput"><?php echo $Helper->Fecha_Castellano(time()); ?></td></tr>
            </table>
        </td>
    </tr>
    <tr>
<!--    	<td height="44px" align="center" background="../../img/barra.jpg" style="font-family:Segoe UI, Tahoma;font-size:16px;color:#FFF;">Men&uacute; de Opciones</td>-->
    	<td colspan="2" height="44px" background="../../img/barra.jpg">
        <?php
    		echo '<div class="left"><table border="0" cellpadding="0" cellspacing="0"><tr><td>';
    		if(isset($_GET["app"])){ $Helper->Menu($usuario[0],$_GET["app"]);} else echo "&nbsp;";
            echo '<div id="menu" align="center"></div></td></tr></table></div>';
        ?>
        <div class="right" style="padding-right:15px;color:#FFF"><a href="http://<?php echo $_SERVER['HTTP_HOST'];?>/portal/cerrar.php"><img src="../../img/cerrar.gif" width="16px" height="16" border="0" style="margin-top:12px;"/><div class="right" style="margin-top:10px;decoration:none;color:#FFF">&nbsp;&nbsp;Cerrar Sesión</div></a></div>
    	</td>
    </tr>
    <tr>
<!--        <td valign="bottom" width="180px" style="height:100%;background-color:#F3F3EF;">
          <div>
          <div id="div_submenu" style="background-color:;height:100%;"></div>
          <div width="180px" style="background-color:#F3F3EF">
            <?php
        		echo $Helper->Menu_Vertical($usuario[4]);
            ?>
          </div>
          </div>
        </td>-->
        <td colspan="2" valign="top" align="center"><iframe id='dumb_excel' style='display:none'></iframe><div style="min-height:450px">