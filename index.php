<?php ob_start("ob_gzhandler"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GALVER SAC</title>
<link rel="icon" type="image/png" href="img/favicon.ico">
<link href="css/style.css" rel='stylesheet' type="text/css" />
</head>
<body topmargin="0" style="padding:0;margin-left:0;margin-right:0;background-color:#FFF;">
<?php session_start(); $_SESSION = null; $_SESSION['usuario']=NULL;unset($_SESSION['usuario']); session_destroy();?>
<form method="post" name="frmLogin" action="procLogin.php" target="iframe">
<center>
<br /><br /><br /><br /><br /><br />
<table boder="0" cellpadding="0" cellspacing="0" width="518px" class="tablaLogin">
	<tr>
    	<td width="36px" height="31px"></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    	<tr>
    	<td></td>
        <td width="182px" height="110px"><img src="img/logo.jpg" width="182px" height="110px" /></td>
        <td width="67px"></td>
        <td align="left" valign="top">
        	<table boder="0" cellpadding="0" cellspacing="0" width="233px">
            	<tr><td width="233px" class="textoInput">Usuario</td></tr>
                <tr><td height="5px"></td></tr>
                <tr><td><input type="text" class="cajaLogin" name="txtUsuario" /></td></tr>
                <tr><td height="15px"></td></tr>
                <tr><td class="textoInput">Contraseña</td></tr>
                <tr><td height="5px"></td></tr>
                <tr><td><input type="password" class="cajaLogin" name="txtClave" /></td></tr>
                <tr><td height="15px"></td></tr>
                <tr><td class="textoInput">Empresa</td></tr>
                <tr><td height="5px"></td></tr>
                <tr><td>
                    <?php
                        include("code/lib/htmlhelper.php"); $Helper=new htmlhelper();
                        echo $Helper->combo_empresas_login("ddlEmpresa");
                    ?>
                </td></tr>
            </table>
        </td>
    </tr>
    <tr><td colspan="4" height="8px"></td></tr>
    <tr>
    	<td colspan="3"></td>
        <td align="left"><input type="submit" value="Enviar" /></td>
    </tr>
    <tr>
        <td colspan="4" style="padding-top:10px" align="center"><div class="error" id="div_error"></div></td>
    </tr>
    <tr><td colspan="4" height="20px"></td></tr>
</table>
<?php
  if(isset($_REQUEST["res"])){
    echo "<div align='left' class='error' style=\"padding-top:20px;width:350px\">
        Se le ha redireccionado a la página de inicio<br/><br/>
        Posibles causas:
        <ul>
           <li>El tiempo de inactividad en la aplicación ha superado los 30 min.</li>
           <li>No cuenta con los permisos necesarios para esa opción</li>
        </ul>
      </div>";
  }
?>
</center>
</form>
<iframe id="iframe" name="iframe" class="no_display"></iframe>
</body>
</html>