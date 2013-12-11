<?php  
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_kardex.php"); $Obj=new bl_kardex;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_Kardex":
          echo $Obj->Grilla_Listar_Kardex($_REQUEST["prm"]);
          break;
      case "PopUp_Detalles_Kardex":
          echo $Obj->PopUp_Detalles_Kardex($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_KardexEntrada":
          echo $Obj->PopUp_Mant_KardexEntrada($_REQUEST["prm"]);
          break;
      case "Mant_KardexEntrada":
          echo $_REQUEST["opc"];
          $resp= $Obj->Mant_KardexEntrada($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true,0);Buscar_Grilla('kardex','Grilla_Listar_Kardex','tbl_listarkardex','','td_General');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "PopUp_Mant_KardexSalida":
          echo $Obj->PopUp_Mant_KardexSalida($_REQUEST["prm"]);
          break;
      case "Mant_KardexSalida":
          $resp= $Obj->Mant_KardexSalida($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true,0);Buscar_Grilla('kardex','Grilla_Listar_Kardex','tbl_listarkardex','','td_General');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;

/******************************************************************** INVENTARIO *************************************************************************/
      case "Filtros_Listar_Inventario":
          echo $Obj->Filtros_Listar_Inventario($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_Inventario":
          echo $Obj->Grilla_Listar_Inventario($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Inventario":
          echo $Obj->PopUp_Mant_Inventario($_REQUEST["prm"]);
          break;
      case "PopUp_Grilla_Listar_Productos":
          echo $Obj->PopUp_Grilla_Listar_Productos();
          break;
      case "Grilla_Listar_Inventario_Items":
          echo $Obj->Grilla_Listar_Inventario_Items($_REQUEST["prm"]);
          break;
      case "Mant_Inventario":
          $resp= $Obj->Mant_Inventario($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);BtnMouseDown('btnBuscar');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Inventario":
          echo $Obj->Confirma_Eliminar_Inventario($_REQUEST["prm"]);
          break;
      case "Eliminar_Inventario":
          $resp= $Obj->Eliminar_Inventario($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);BtnMouseDown('btnBuscar');</script>";
          else echo '<script>Operacion_Result(false);</script>';

/******************************************************************** GUIA DE ENTRADA *************************************************************************/
      case "Filtros_Listar_GuiaEntrada":
          echo $Obj->Filtros_Listar_GuiaEntrada($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_GuiaEntrada":
          echo $Obj->Grilla_Listar_GuiaEntrada($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_GuiaEntrada":
          echo $Obj->PopUp_Mant_GuiaEntrada($_REQUEST["prm"]);
          break;
      case "Mant_GuiaEntrada":
          $resp= $Obj->Mant_GuiaEntrada($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);BtnMouseDown('btnBuscarGuia');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_GuiaEntrada":
          echo $Obj->Confirma_Eliminar_GuiaEntrada($_REQUEST["prm"]);
          break;
      case "Eliminar_GuiaEntrada":
          $resp= $Obj->Eliminar_GuiaEntrada($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);BtnMouseDown('btnBuscarGuia');</script>";
          else echo '<script>Operacion_Result(false);</script>';

/******************************************************************** GUIA DE SALIDA *************************************************************************/
      case "Filtros_Listar_GuiaSalida":
          echo $Obj->Filtros_Listar_GuiaSalida($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_GuiaSalida":
          echo $Obj->Grilla_Listar_GuiaSalida($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_GuiaSalida":
          echo $Obj->PopUp_Mant_GuiaSalida($_REQUEST["prm"]);
          break;
      case "Mant_GuiaSalida":
          $resp= $Obj->Mant_GuiaSalida($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);BtnMouseDown('btnBuscarGuia');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_GuiaSalida":
          echo $Obj->Confirma_Eliminar_GuiaSalida($_REQUEST["prm"]);
          break;
      case "Eliminar_GuiaSalida":
          $resp= $Obj->Eliminar_GuiaSalida($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true);BtnMouseDown('btnBuscarGuia');</script>";
          else echo '<script>Operacion_Result(false);</script>';
      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>