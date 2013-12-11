<?php session_start(); ?>
<?php include('../include/seguridad.php'); ?>
<?php include('../include/db.php'); ?>
<?php	
	$conn=conexionMY();	
	$mod = $_GET["mod"];
	$ruta = "";
	if($mod=="")
	{
		$mod = $_POST["hfModulo"];		
	}
	$nombre = $_POST["txtNombreFiltro"];
	$pk="";
	$tabla="";
	switch($mod)
	{
		case 10: 	$pk="sgo_int_estadocotizacion";
					$tabla = "tbl_sgo_estadocotizacion";
					$ruta = "- Estados - Cotizacion";
		break;	
		case 11: 	$pk="sgo_int_estadofactura";
					$tabla = "tbl_sgo_estadofactura";
					$ruta = "- Estados - Factura";
		break;	
		case 12: 	$pk="sgo_int_estadoguiaremision";
					$tabla = "tbl_sgo_estadoguiaremision";
					$ruta = "- Estados - Guia de Remision";
		break;	
		case 13: 	$pk="sgo_int_estadooc";
					$tabla = "tbl_sgo_estadoordencompra";
					$ruta = "- Estados - Orden de Compra";
		break;
		case 14: 	$pk="sgo_int_estadovisita";
					$tabla = "tbl_sgo_estadovisita";
					$ruta = "- Estados - Visita";
		break;	
	}
	$sql="SELECT ".$pk." , sgo_vch_descripcion from ".$tabla." where sgo_bit_activo = 1";
	if($nombre!="")
	{ 
		$sql = $sql." and sgo_vch_descripcion like '%".$nombre."%'";
	}
	$lista = mysql_query($sql, $conn);	
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GALVER SAC</title>
<link href="../css/galver.css" rel='stylesheet' type="text/css" />
<script language="javascript" src="../js/jquery.js"></script>
<script language="javascript" src="../js/js.js"></script>
</head>
<body topmargin="0" style="padding:0;margin-left:0;margin-right:0;background-color:#CCCCCC">
<form action="configuracion_estado.php" method="post">
<center>
<?php include("../include/cabecera.php"); ?>
<table border="0" cellpadding="0" cellspacing="0" width="1200px" style="background-color:#FFFFFF;">
<tr><td colspan="3" height="15px"></td></tr>
<tr>
	<td width="15px"></td>
    <td class="textoUbicacion" align="left">> Configuracion <?php echo $ruta?></td>
    <td width="15px"></td>
</tr>
<tr><td colspan="3" height="15px"></td></tr>
<tr>
	<td></td>
    <td>
    <fieldset class="textoInput">
        <legend align= "left">Filtro de búsqueda</legend>
        <table border="0" cellpadding="0" cellspacing="0" width="990px">
        	<tr><td colspan="7" height="10px"></td>
            <tr>
                <td width="10px"></td>
                <td class="textoInput">Descripcion</td>
                <td width="10px"></td>
                <td><input type="text" name="txtNombreFiltro" class="cajaFiltroGrande" value="<?php echo $nombre?>" style="width:350px" /></td>
                <td width="10px"></td>
                <td><input type="submit" id="btnBuscar" value="Buscar" class="textoInput" /></td>
                 <td width="20px"></td>
            </tr>
            <tr><td colspan="7" height="10px"></td>
        </table>
    </fieldset>
    </td>
    <td></td>
</tr>
<tr><td colspan="3" height="15px"></td></tr>
<tr>
	<td></td>
    <td>
    	<table border="0" cellpadding="0" cellspacing="0" width="1170px">
        	<tr class="cabeceraTablaResultado">
            	<td width="10px" class="bordeTopBotLe"></td>
                <td width="20px" height="37px"><a href="#" style="text-decoration:none;">+</a></td>
                <td width="20px"></td>
                <td width="20px"></td>
                <td width="20px"></td>
                <td width="1080px"> Descripcion</td>
            </tr>
            <?php
				$cont=0;
            	if($lista)
				{
					while($fila = mysql_fetch_array($lista))
					{
						$cont++;
			?>
            	<tr>
                    <td width="10px"></td>
                    <td width="20px" height="37px"></td>
                    <td width="20px"><a href="#"><img src="../img/b_editar.gif" border="0" /></a></td>
                    <td width="20px"><a href="#"><img src="../img/b_eliminar.gif" border="0" /></a></td>
                    <td width="20px"></td>
                    <td width="1100px" class="textoData"><?php echo $fila['sgo_vch_descripcion']?></td>
           		</tr>
                <tr><td colspan="6" height="1px" style="background-color:#C5D3FB;"></td></tr>
			<?php
					}
				}
				if($cont==0)
				{
			?>	
            	<tr><td colspan="6" height="10px"></td></tr>
            	<tr><td colspan="6" align="center" class="textoData">No se han encontrado datos para la consulta realizada</td></tr>
                <tr><td colspan="6" height="10px"></td></tr>
            <?php
				}
			?>		
        </table>
    </td>
    <td></td>
</tr>
<tr><td colspan="3" height="25px"></td></tr>
</table>
<?php include("../include/pie.php"); ?>
</center>
<input type="hidden" name="hfModulo" value="<?php echo $mod?>" />
</form>
</body>
</html>