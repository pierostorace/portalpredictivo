<?php  
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_guiaremision.php"); $Obj=new bl_guiaremision;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_GuiasRemision":
          echo $Obj->Grilla_Listar_GuiasRemision($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_GuiaRemision":
          echo $Obj->PopUp_Mant_GuiaRemision($_REQUEST["prm"]);
          break;
	  case "PopUp_Imprimir":
          echo $Obj->PopUp_Imprimir($_REQUEST["prm"]);
          break;
      case "Detalles":
          echo $Obj->Detalles_GuiaRemision($_REQUEST["prm"]);
          break;
      case "Mant_GuiaRemision":
          $resp= $Obj->Mant_GuiaRemision($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);" .  ($valor[0]==0?"PopUp('guiaremision','PopUp_Mant_GuiaRemision','','" . $resp . "');":"") . "Buscar_Grilla('guiaremision','Grilla_Listar_GuiasRemision','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar":
          echo $Obj->Confirma_Eliminar_GuiaRemision($_REQUEST["prm"]);
          break;
      case "Eliminar_GuiaRemision":	  	  
          echo $Obj->Eliminar_GuiaRemision($_REQUEST["prm"]);
          break;
	  case "Carga_DetalleGuiaRemision":
	  	  $resp = $Obj->Carga_DetalleGuiaRemision($_REQUEST["prm"]);
		  if($resp!=-1)
		  { 
		  	$valor =explode('|',$_REQUEST["prm"]); 
			echo "<script>Operacion_Reload('guiaremision','Grilla_Listar_GuiaRemision_Detalle','','" . $valor[0] . "','div_GuiaRemision_Detalles');Buscar_Grilla('guiaremision','Grilla_Listar_GuiasRemision','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
		  break;

/******************************************************************** DETALLES *************************************************************************/
      case "Grilla_Listar_GuiaRemision_Detalle":
          echo $Obj->Grilla_Listar_GuiaRemision_Detalle($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_GuiaRemision_Detalle":
          echo $Obj->PopUp_Mant_GuiaRemision_Detalle($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Guia_Servicio":
	  	  echo $Obj->PopUp_Mant_Guia_Servicio($_REQUEST["prm"]);
		  break;
	  case "Mant_ServicioGuia_Detalle":
	  	  $resp= $Obj->Mant_ServicioGuia_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('guiaremision','Grilla_Listar_GuiaRemision_Detalle','','" . $valor[0] . "','div_GuiaRemision_Detalles');Buscar_Grilla('guiaremision','Grilla_Listar_GuiasRemision','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Mant_GuiaRemision_Detalle":
          $resp= $Obj->Mant_GuiaRemision_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('guiaremision','Grilla_Listar_GuiaRemision_Detalle','','" . $valor[0] . "','div_GuiaRemision_Detalles');</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Detalle":
          echo $Obj->Confirma_Eliminar_GuiaRemision_Detalle($_REQUEST["prm"]);
          break;
      case "Eliminar_GuiaRemision_Detalle":
          $resp= $Obj->Eliminar_GuiaRemision_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('guiaremision','Grilla_Listar_GuiaRemision_Detalle','','" . $valor[0] . "','div_GuiaRemision_Detalles');Buscar_Grilla('guiaremision','Grilla_Listar_GuiasRemision','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
	  case "Combo_Tienda":
	  	  echo $Obj->Cargar_Combo_Tienda($_REQUEST["prm"]);
	      break;
      case "Combo_OS_ordencompra":
	  	  echo $Obj->Cargar_Combo_OrdenCompraCliente($_REQUEST["prm"]);
	      break;
	  case "Carga_TotalOrdenServicio":
	  	  echo $Obj->Cargar_Orden_Servicio($_REQUEST["prm"]);
	      break;
	  case "Carga_Chofer":
	  	  echo $Obj->Cargar_Chofer_Transportista($_REQUEST["prm"]);
	      break;
	  case "Carga_Licencia":
	  	  echo $Obj->Cargar_Licencia_Transportista($_REQUEST["prm"]);
	      break;
	  case "Carga_DatosTransportista":
	  	  echo $Obj->Cargar_DatosTransportista_Transportista($_REQUEST["prm"]);
	      break;
      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>