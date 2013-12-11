<?php header('Content-type: text/html; charset=utf-8');
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_ordencompra.php"); $Obj=new bl_ordencompra;
  switch($_REQUEST["opc"])
  {
      case "Upload_OrdenCompra":
           include_once ("../lib/uploadhelper.php"); $Up=new uploadhelper;
           echo $Up->grabar_archivo("../../archivo/ordencompra/",$_REQUEST['id'],$_FILES['file_' . $_REQUEST['id']]);
          break;
      case "Grilla_Listar_OrdenesCompra":
          echo $Obj->Grilla_Listar_OrdenesCompra($_REQUEST["prm"]);
          break;
      case "Detalles":
          echo $Obj->Detalles_OrdenCompra($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_OrdenCompra":
          echo $Obj->PopUp_Mant_OrdenCompra($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_ImportarOrdenCompraDetalle":
          echo $Obj->PopUp_Mant_ImportarOrdenCompraDetalle($_REQUEST["prm"]);
          break;
      case "Upload_ItemOrdenCompra": //utiliza un iframe para llegar hasta acá
          $resp= $Obj->Upload_ItemOrdenCompra($_REQUEST["id"],$_REQUEST["prm"],$_REQUEST["fil_cmbDireccionOC"],$_FILES["fil_fileUpload"]);
          if($resp!=-1) echo "<script>parent.$('#PopUp').overlay().close();parent.PopUp('ordencompra','PopUp_Mant_OrdenCompra','','" . $_REQUEST["prm"] . "');parent.Buscar_Grilla('ordencompra','Grilla_Listar_OrdenesCompra','tbl_listaroc','','td_General',1);parent.Operacion_Result(true);</script>";
          else echo '<script>parent.Operacion_Result(false);</script>';
          break;
      case "Mant_OrdenCompra":
          $resp= $Obj->Mant_OrdenCompra($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]); echo "<script>Operacion_Result(true);" .  ($valor[0]==0?"PopUp('ordencompra','PopUp_Mant_OrdenCompra','','" . $resp . "');":"") . "Buscar_Grilla('ordencompra','Grilla_Listar_OrdenesCompra','tbl_listaroc','','td_General',1);</script>";}
//          if($resp!=-1) echo "<script>Operacion_Reload('ordencompra','Grilla_Listar_OrdenesCompra_Detalle','','" . $_REQUEST["prm"] . "','div_OrdenesCompra_Detalles');Display('Tabs_Mant_OrdenCompra_1','');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar":
          echo $Obj->Confirma_Eliminar_OrdenCompra($_REQUEST["prm"]);
          break;
      case "Eliminar_OrdenCompra":
          echo $Obj->Eliminar_OrdenCompra($_REQUEST["prm"]);
          break;
      case "Mant_OrdenProduccion":
          $resp= $Obj->Mant_OrdenProduccion($_REQUEST["prm"]);
          if($resp!=-1) echo "<script>Operacion_Result(true,0);Buscar_Grilla('ordencompra','Grilla_Listar_OrdenesCompra','tbl_listaroc','','td_General',1);</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
/******************************************************************** DETALLES *************************************************************************/
      case "Tab_OrdenesCompra_Detalles":
          echo $Obj->Tab_OrdenesCompra_Detalles($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_OrdenesCompra_Detalle":
          echo $Obj->Grilla_Listar_OrdenesCompra_Detalles($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_OrdenCompra_Detalle":
          echo $Obj->PopUp_Mant_OrdenCompra_Detalle($_REQUEST["prm"]);
          break;
      case "Mant_OrdenCompra_Detalle":
          $resp= $Obj->Mant_OrdenCompra_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('ordencompra','Tab_OrdenesCompra_Detalles','','" . $valor[0] . "','div_OrdenesCompra_Detalles');Buscar_Grilla('ordencompra','Grilla_Listar_OrdenesCompra','tbl_listaroc','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Detalle":
          echo $Obj->Confirma_Eliminar_OrdenCompra_Detalle($_REQUEST["prm"]);
          break;
      case "Eliminar_OrdenCompra_Detalle":
          $resp= $Obj->Eliminar_OrdenCompra_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('ordencompra','Tab_OrdenesCompra_Detalles','','" . $valor[0] . "','div_OrdenesCompra_Detalles');Buscar_Grilla('ordencompra','Grilla_Listar_OrdenesCompra','tbl_listaroc','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>