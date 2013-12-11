<?php  
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_cotizacion.php"); $Obj=new bl_cotizacion;include_once('../../code/lib/loghelper.php'); $loghelper=new loghelper;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_Cotizaciones":
          echo $Obj->Grilla_Listar_Cotizaciones($_REQUEST["prm"]);
          break;      	 
      case "PopUp_Mant_Cotizacion":
          echo $Obj->PopUp_Mant_Cotizacion($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_OrdenCompra":
          echo $Obj->PopUp_Mant_OrdenCompra($_REQUEST["prm"]);
          break;
      case "Mant_Cotizacion":	  	 
          $resp= $Obj->Mant_Cotizacion($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);PopUp('cotizacion','PopUp_Mant_Cotizacion','','" . $resp . "');Buscar_Grilla('cotizacion','Grilla_Listar_Cotizaciones','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Mant_OrdenCompra":	  	 
          $resp= $Obj->Mant_OrdenCompra($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);Cerrar_PopUp();Buscar_Grilla('cotizacion','Grilla_Listar_Cotizaciones','tbl_listarguia','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;          
      case "Confirma_Eliminar":
          echo $Obj->Confirma_Eliminar_Cotizacion($_REQUEST["prm"]);
          break;
      case "Eliminar_Cotizacion":
          echo $Obj->Eliminar_Cotizacion($_REQUEST["prm"]);
          break;      
/******************************************************************** DETALLES *************************************************************************/
      case "Grilla_Listar_Cotizacion_Detalle":
          echo $Obj->Grilla_Listar_Cotizacion_Detalle($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Cotizacion_Detalle":
          echo $Obj->PopUp_Mant_Cotizacion_Detalle($_REQUEST["prm"]);
          break;            
	  case "Mant_Cotizacion_Detalle":      	
          $resp= $Obj->Mant_Cotizacion_Detalle($_REQUEST["prm"]);
          $valor=explode('|',$resp);
          if($valor[0]!=-1) 
          	echo "<script>Cerrar_PopUp();Operacion_Result(true,0);Buscar_Grilla('cotizacion','Grilla_Listar_Cotizacion_Detalle','','".$valor[1]."','div_ComprobanteVenta_Detalles',1);Buscar_Grilla('cotizacion','Grilla_Listar_Cotizaciones','tbl_listarcotizacion','','td_General',1)</script>";
          else 
          	echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Detalle":
          echo $Obj->Confirma_Eliminar_Cotizacion_Detalle($_REQUEST["prm"]);
          break;
      case "Eliminar_Cotizacion_Detalle":
          $resp= $Obj->Eliminar_Cotizacion_Detalle($_REQUEST["prm"]);
          $valor=explode('|',$resp);
          if($valor[0]!=-1) 
          	echo "<script>Cerrar_PopUp();Operacion_Result(true,0);Buscar_Grilla('cotizacion','Grilla_Listar_Cotizacion_Detalle','','".$valor[1]."','div_ComprobanteVenta_Detalles',1);Buscar_Grilla('cotizacion','Grilla_Listar_Cotizaciones','tbl_listarcotizacion','','td_General',1)</script>";
          else 
          	echo '<script>Operacion_Result(false);</script>';
      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>