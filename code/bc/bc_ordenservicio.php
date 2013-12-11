<?php header('Content-type: text/html; charset=utf-8');
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_ordenservicio.php"); $Obj=new bl_ordenservicio;
  switch($_REQUEST["opc"])
  {
      case "Upload_OrdenServicio":
           include_once ("../lib/uploadhelper.php"); $Up=new uploadhelper;
           echo $Up->grabar_archivo("../../archivo/ordenservicio/",$_REQUEST['id'],$_FILES['file_' . $_REQUEST['id']]);
          break;
      case "Grilla_Listar_OrdenesServicio":
          echo $Obj->Grilla_Listar_OrdenesServicio($_REQUEST["prm"]);
          break;
      case "Detalles":
          echo $Obj->Detalles_OrdenServicio($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_OrdenServicio":
          echo $Obj->PopUp_Mant_OrdenServicio($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_ImportarOrdenServicioDetalle":
          echo $Obj->PopUp_Mant_ImportarOrdenServicioDetalle($_REQUEST["prm"]);
          break;
      case "Upload_ItemOS": //utiliza un iframe para llegar hasta acá
          $resp= $Obj->Upload_ItemOS($_REQUEST["id"],$_REQUEST["prm"],$_REQUEST["fil_cmbDireccionOC"],$_FILES["fil_fileUpload"],$_REQUEST["fil_cmbTipo"]);
          if($resp!=-1) echo "<script>parent.$('#PopUp').overlay().close();parent.PopUp('ordenservicio','PopUp_Mant_OrdenServicio','','" . $_REQUEST["prm"] . "');parent.Buscar_Grilla('ordenservicio','Grilla_Listar_OrdenesServicio','tbl_listaroc','','td_General',1);parent.Operacion_Result(true);</script>";
          else echo '<script>parent.Operacion_Result(false);</script>';
          break;
      case "Mant_OrdenServicio":
          $resp= $Obj->Mant_OrdenServicio($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]); echo "<script>Operacion_Result(true);" .  ($valor[0]==0?"PopUp('ordenservicio','PopUp_Mant_OrdenServicio','','" . $resp . "');":"") . "Buscar_Grilla('ordenservicio','Grilla_Listar_OrdenesServicio','tbl_listaroc','','td_General',1);</script>";}
//          if($resp!=-1) echo "<script>Operacion_Reload('ordenservicio','Grilla_Listar_OrdenesServicio_Detalle','','" . $_REQUEST["prm"] . "','div_OrdenesServicio_Detalles');Display('Tabs_Mant_OrdenServicio_1','');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar":
          echo $Obj->Confirma_Eliminar_OrdenServicio($_REQUEST["prm"]);
          break;
      case "Eliminar_OrdenServicio":
          echo $Obj->Eliminar_OrdenServicio($_REQUEST["prm"]);
          break;
      case "Mant_OrdenProduccion":
          $resp= $Obj->Mant_OrdenProduccion($_REQUEST["prm"]);
          if($resp!=-1) echo "<script>Operacion_Result(true,0);Buscar_Grilla('ordenservicio','Grilla_Listar_OrdenesServicio','tbl_listaroc','','td_General',1);</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
/******************************************************************** DETALLES *************************************************************************/
      case "Tab_OrdenesServicio_Detalles":
          echo $Obj->Tab_OrdenesServicio_Detalles($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_OrdenesServicio_Detalle":
          echo $Obj->Grilla_Listar_OrdenesServicio_Detalles($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_OrdenServicio_Detalle":
          echo $Obj->PopUp_Mant_OrdenServicio_Detalle($_REQUEST["prm"]);
          break;
      case "Mant_OrdenServicio_Detalle":
          $resp= $Obj->Mant_OrdenServicio_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('ordenservicio','Tab_OrdenesServicio_Detalles','','" . $valor[0] . "','div_OrdenesServicio_Detalles');Buscar_Grilla('ordenservicio','Grilla_Listar_OrdenesServicio','tbl_listaroc','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Detalle":
          echo $Obj->Confirma_Eliminar_OrdenServicio_Detalle($_REQUEST["prm"]);
          break;
      case "Eliminar_OrdenServicio_Detalle":
          $resp= $Obj->Eliminar_OrdenServicio_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('ordenservicio','Tab_OrdenesServicio_Detalles','','" . $valor[0] . "','div_OrdenesServicio_Detalles');Buscar_Grilla('ordenservicio','Grilla_Listar_OrdenesServicio','tbl_listaroc','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>