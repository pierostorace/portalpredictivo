<?php   
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_facturacion.php"); $Obj=new bl_facturacion;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_ComprobanteVenta":
          echo $Obj->Grilla_Listar_ComprobanteVenta($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_ComprobanteVenta":
          echo $Obj->PopUp_Mant_ComprobanteVenta($_REQUEST["prm"]);
          break;
      case "PopUp_Imprimir":
          echo $Obj->PopUp_Imprimir($_REQUEST["prm"]);
          break;
      case "PopUp_ReImprimir":
          echo $Obj->PopUp_ReImprimir($_REQUEST["prm"]);
          break;
      case "Detalles":
          echo $Obj->Detalles_ComprobanteVenta($_REQUEST["prm"]);
          break;
      case "Mant_ComprobanteVenta":	  	 
          $resp= $Obj->Mant_ComprobanteVenta($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);" .  ($valor[0]==0?"PopUp('facturacion','PopUp_Mant_ComprobanteVenta','','" . $resp . "');":"") . "Buscar_Grilla('facturacion','Grilla_Listar_ComprobanteVenta','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar":
          echo $Obj->Confirma_Eliminar_ComprobanteVenta($_REQUEST["prm"]);
          break;
      case "Eliminar_ComprobanteVenta":
          echo $Obj->Eliminar_ComprobanteVenta($_REQUEST["prm"]);
          break;
	  case "Confirma_Eliminar_Adelanto":
          echo $Obj->Confirma_Eliminar_Adelanto($_REQUEST["prm"]);
          break;
      case "Eliminar_Adelanto":	  
	  	  $valor = explode('|',$_REQUEST["prm"]);
          if($Obj->Eliminar_Adelanto($_REQUEST["prm"])!=-1)
		  {
				echo "<script>Operacion_Result(true);Cerrar_PopUp();Operacion_Reload('facturacion','Grilla_Listar_ComprobanteVenta_Detalle','','" . $valor[0] . "','div_ComprobanteVenta_Detalles');Buscar_Grilla('facturacion','Grilla_Listar_ComprobanteVenta','tbl_listarguia','','td_General',1);</script>";		
		  }
          break;
	  case "MensajeCredito":
            echo $Obj->Carga_MensajeCredito($_REQUEST["prm"]);
	        break;
	  case "Carga_DetalleGuiaRemision":
	  	  $resp = $Obj->Carga_DetalleGuiaRemision($_REQUEST["prm"]);
		  if($resp!=-1)
		  {
		  	$valor =explode('|',$_REQUEST["prm"]);
			echo "<script>Operacion_Reload('facturacion','Grilla_Listar_ComprobanteVenta_Detalle','','" . $valor[0] . "','div_ComprobanteVenta_Detalles');Buscar_Grilla('facturacion','Grilla_Listar_ComprobanteVenta','tbl_listarguia','','td_General',1);</script>";
          }
          else echo '<script>Operacion_Result(false);</script>';
		  break;

/******************************************************************** DETALLES *************************************************************************/
      case "Grilla_Listar_ComprobanteVenta_Detalle":
          echo $Obj->Grilla_Listar_ComprobanteVenta_Detalle($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_ComprobanteVenta_Detalle":
          echo $Obj->PopUp_Mant_ComprobanteVenta_Detalle($_REQUEST["prm"]);
          break;
	  case "PopUp_Mant_Facturacion_Servicio":
	  	  echo $Obj->PopUp_Mant_Facturacion_Servicio($_REQUEST["prm"]);
		  break;
      case "Mant_ComprobanteVenta_Detalle":
          $resp= $Obj->Mant_ComprobanteVenta_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('facturacion','Grilla_Listar_ComprobanteVenta_Detalle','','" . $valor[0] . "','div_ComprobanteVenta_Detalles');Buscar_Grilla('facturacion','Grilla_Listar_ComprobanteVenta','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
	  case "Mant_ServicioFacturacion_Detalle":
	  $resp= $Obj->Mant_ServicioFacturacion_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('facturacion','Grilla_Listar_ComprobanteVenta_Detalle','','" . $valor[0] . "','div_ComprobanteVenta_Detalles');Buscar_Grilla('facturacion','Grilla_Listar_ComprobanteVenta','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Detalle":
          echo $Obj->Confirma_Eliminar_ComprobanteVenta_Detalle($_REQUEST["prm"]);
          break;
      case "Eliminar_ComprobanteVenta_Detalle":
          $resp= $Obj->Eliminar_ComprobanteVenta_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('facturacion','Grilla_Listar_ComprobanteVenta_Detalle','','" . $valor[0] . "','div_ComprobanteVenta_Detalles');Buscar_Grilla('facturacion','Grilla_Listar_ComprobanteVenta','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
	  case "PopUp_DocumentosAplicablesDescuento":
	   	  echo $Obj->PopUp_DocumentosAplicablesDescuento($_REQUEST["prm"]);
		  break;
	  case "Mant_Descuento":
	  	  $resp= $Obj->Mant_Descuento($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('facturacion','Grilla_Listar_ComprobanteVenta_Detalle','','" . $valor[0] . "','div_ComprobanteVenta_Detalles');Buscar_Grilla('facturacion','Grilla_Listar_ComprobanteVenta','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;	  
      default:
          echo $_REQUEST["opc"];
        break;

/******************************************************************** NOTAS DE CREDITO *************************************************************************/
      case "Grilla_Listar_NotaCredito":
          echo $Obj->Grilla_Listar_NotaCredito($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_NotaCredito":
          echo $Obj->PopUp_Mant_NotaCredito($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_DocReferencia":
          echo $Obj->Grilla_Listar_NotaCredito_DocReferencia($_REQUEST["prm"]);
          break;
      case "Mant_NotaCredito":
      	  include_once ("../lib/loghelper.php");$objLogger = new loghelper;
      	  $objLogger->log("Aquí si llega"); 
          $resp= $Obj->Mant_NotaCredito($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);Buscar_Grilla('facturacion','Grilla_Listar_NotaCredito','tbl_listarcomprobante','','td_General',1);</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_NotaCredito":
          echo $Obj->Confirma_Eliminar_NotaCredito($_REQUEST["prm"]);
          break;
      case "Eliminar_NotaCredito":
          echo $Obj->Eliminar_NotaCredito($_REQUEST["prm"]);
          break;

/******************************************************************** NOTAS DE DEBITO *************************************************************************/
      case "Grilla_Listar_NotaDebito":
          echo $Obj->Grilla_Listar_NotaDebito($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_NotaDebito":
          echo $Obj->PopUp_Mant_NotaDebito($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_NotaDebito_DocReferencia":
          echo $Obj->Grilla_Listar_NotaDebito_DocReferencia($_REQUEST["prm"]);
          break;
      case "Mant_NotaDebito":
          $resp= $Obj->Mant_NotaDebito($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);Buscar_Grilla('facturacion','Grilla_Listar_NotaDebito','tbl_listarcomprobante','','td_General',1);</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_NotaDebito":
          echo $Obj->Confirma_Eliminar_NotaDebito($_REQUEST["prm"]);
          break;
      case "Eliminar_NotaDebito":
          echo $Obj->Eliminar_NotaDebito($_REQUEST["prm"]);
          break;

  }
  ob_end_flush();
?>