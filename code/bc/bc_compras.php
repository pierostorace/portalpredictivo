<?php if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_compras.php"); $Obj=new bl_compras;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_Compras":
          echo $Obj->Grilla_Listar_Compras($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Compras":
          echo $Obj->PopUp_Mant_Compras($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Proveedor_Compras":
          echo $Obj->PopUp_Mant_Proveedor($_REQUEST["prm"],"_Compras");
          break;
      case "Mant_Proveedor_Compras":
          $resp= $Obj->Mant_Proveedor($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);Cargar_Objeto('general','Combo_Proveedor','','','fil_cmbproveedor','panel');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Mant_Compras":
          $resp= $Obj->Mant_Compras($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);Buscar_Grilla('compras','Grilla_Listar_Compras','tbl_listarcomprobante','','td_General',1);</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar":
          echo $Obj->Confirma_Eliminar_Compras($_REQUEST["prm"]);
          break;
      case "Eliminar_Compras":
          echo $Obj->Eliminar_Compras($_REQUEST["prm"]);
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
          $resp= $Obj->Mant_NotaCredito($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);Buscar_Grilla('compras','Grilla_Listar_NotaCredito','tbl_listarcomprobante','','td_General',1);</script>";
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
          if($resp!=-1)echo "<script>Operacion_Result(true);Buscar_Grilla('compras','Grilla_Listar_NotaDebito','tbl_listarcomprobante','','td_General',1);</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_NotaDebito":
          echo $Obj->Confirma_Eliminar_NotaDebito($_REQUEST["prm"]);
          break;
      case "Eliminar_NotaDebito":
          echo $Obj->Eliminar_NotaDebito($_REQUEST["prm"]);
          break;

/******************************************************************** PROVEEDOR *************************************************************************/
      case "Grilla_Listar_Proveedor":
          echo $Obj->Grilla_Listar_Proveedor($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Proveedor":
          echo $Obj->PopUp_Mant_Proveedor($_REQUEST["prm"]);
          break;
      case "Mant_Proveedor":
          $resp= $Obj->Mant_Proveedor($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);Buscar_Grilla('compras','Grilla_Listar_Proveedor','tbl_listarproveedor','','td_General',1);</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Proveedor":
          echo $Obj->Confirma_Eliminar_Proveedor($_REQUEST["prm"]);
          break;
      case "Eliminar_Proveedor":
          echo $Obj->Eliminar_Proveedor($_REQUEST["prm"]);
          break;

      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>