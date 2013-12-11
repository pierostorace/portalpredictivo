<?php  
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_finanzas.php"); $Obj=new bl_finanzas;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_Movimientos":
          echo $Obj->Grilla_Listar_Movimientos($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Movimiento":
          echo $Obj->PopUp_Mant_Movimiento($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Documentos":
          echo $Obj->Grilla_Listar_Movimiento_Items_Documentos($_REQUEST["prm"]);
          break;
      case "Mant_Movimiento":
          $resp= $Obj->Mant_Movimiento($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);Buscar_Grilla('finanzas','Grilla_Listar_Movimientos','tbl_listarmovimientos','','td_General',1);</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Movimiento":
          echo $Obj->Confirma_Eliminar_Movimiento($_REQUEST["prm"]);
          break;
      case "Eliminar_Movimiento":
          echo $Obj->Eliminar_Movimiento($_REQUEST["prm"]);
          break;

/******************************************************************** CAJA Y BANCOS *************************************************************************/
      case "Grilla_Listar_Documentos":
          echo $Obj->Grilla_Listar_Documentos($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Documento":
          echo $Obj->PopUp_Mant_Documento($_REQUEST["prm"]);
          break;
      case "Mant_Documento":
          $resp= $Obj->Mant_Documento($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);Buscar_Grilla('finanzas','Grilla_Listar_Documentos','tbl_listardocumentos','','td_General',1);</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Documento":
          echo $Obj->Confirma_Eliminar_Documento($_REQUEST["prm"]);
          break;
      case "Eliminar_Documento":
          echo $Obj->Eliminar_Documento($_REQUEST["prm"]);
          break;

/******************************************************************** CAJA Y BANCOS *************************************************************************/
      case "Grilla_Listar_Cajas":
          echo $Obj->Grilla_Listar_Cajas($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Caja":
          echo $Obj->PopUp_Mant_Caja($_REQUEST["prm"]);
          break;
      case "Mant_Caja":
          $resp= $Obj->Mant_Caja($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);Buscar_Grilla('finanzas','Grilla_Listar_Cajas','tbl_listarcajas','','td_General',1);</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Caja":
          echo $Obj->Confirma_Eliminar_Caja($_REQUEST["prm"]);
          break;
      case "Eliminar_Caja":
          echo $Obj->Eliminar_Caja($_REQUEST["prm"]);
          break;

      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>