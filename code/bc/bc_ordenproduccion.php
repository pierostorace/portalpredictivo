<?php
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start(); 
  include_once ("../bl/bl_ordenproduccion.php"); $Obj=new bl_ordenproduccion;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_OrdenProduccion":
          echo $Obj->Grilla_Listar_OrdenProduccion($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_OrdenProduccion":
          echo $Obj->PopUp_Mant_OrdenProduccion($_REQUEST["prm"]);
          break;
      case "PopUp_Imprimir":
          echo $Obj->PopUp_Imprimir($_REQUEST["prm"]);
          break;
      case "Mant_OrdenProduccion_Inicio":
          $resp= $Obj->Mant_OrdenProduccion_Inicio($_REQUEST["prm"]);
          if($resp!=-1) echo "<script>Buscar_Grilla('ordenproduccion','Grilla_Listar_OrdenProduccion','tbl_listarordenproduccion','','td_General');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Mant_OrdenProduccion":
          $resp= $Obj->Mant_OrdenProduccion($_REQUEST["prm"]);
          if($resp!=-1) echo "<script>Buscar_Grilla('ordenproduccion','Grilla_Listar_OrdenProduccion','tbl_listarordenproduccion','','td_General');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar":
          echo $Obj->Confirma_Eliminar_OrdenProduccion($_REQUEST["prm"]);
          break;
      case "Eliminar_OrdenProduccion":
          echo $Obj->Eliminar_OrdenProduccion($_REQUEST["prm"]);
          break;

/******************************************************************** DETALLES *************************************************************************/
      case "Grilla_Listar_OrdenProduccion_Detalle":
          echo $Obj->Grilla_Listar_OrdenProduccion_Detalle($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_OrdenProduccion_Detalle":
          echo $Obj->PopUp_Mant_OrdenProduccion_Detalle($_REQUEST["prm"]);
          break;
      case "Mant_OrdenProduccion_Detalle":
          $resp= $Obj->Mant_OrdenProduccion_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('ordenproduccion','Grilla_Listar_OrdenProduccion_Detalle','','" . $valor[0] . "','div_OrdenProduccion_Detalles');</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "PopUp_Detalles_OrdenProduccion":
          echo $Obj->PopUp_Detalles_OrdenProduccion($_REQUEST["prm"]);
          break;
      case "Confirma_Eliminar_Detalle":
          echo $Obj->Confirma_Eliminar_OrdenProduccion_Detalle($_REQUEST["prm"]);
          break;
      case "Eliminar_OrdenProduccion_Detalle":
          $resp= $Obj->Eliminar_OrdenProduccion_Detalle($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]); echo "<script>Cerrar_PopUp();Operacion_Reload('ordenproduccion','Grilla_Listar_OrdenProduccion_Detalle','','" . $valor[0] . "','div_OrdenProduccion_Detalles');</script>";}
          else echo '<script>Operacion_Result(false);</script>';

/******************************************************************** ARTICULOS *************************************************************************/
      case "Grilla_Listar_Articulo":
          echo $Obj->Grilla_Listar_Articulo($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Articulo":
          echo $Obj->PopUp_Mant_Articulo($_REQUEST["prm"]);
          break;
      case "Mant_Articulo":
          $resp= $Obj->Mant_Articulo($_REQUEST["prm"]);
          if($resp!=-1) echo "<script>Operacion_Result(true);Buscar_Grilla('ordenproduccion','Grilla_Listar_Articulo','tbl_listararticulo','','td_General');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Upload_Articulo":
           include_once ("../lib/uploadhelper.php"); $Up=new uploadhelper;
           echo $Up->grabar_archivo("../../archivo/ordenproduccion/",$_REQUEST['id'],$_FILES['file_' . $_REQUEST['id']]);
          break;
      case "Confirma_Eliminar_Articulo":
          echo $Obj->Confirma_Eliminar_Articulo($_REQUEST["prm"]);
          break;
      case "Eliminar_Articulo":
          $resp= $Obj->Eliminar_Articulo($_REQUEST["prm"]);
          if($resp!=-1) echo "<script>Operacion_Result(true);Buscar_Grilla('ordenproduccion','Grilla_Listar_Articulo','tbl_listararticulo','','td_General');</script>";
          else echo '<script>Operacion_Result(false);</script>';

/******************************************************************** FÓRMULAS *************************************************************************/
      case "Grilla_Listar_Formula":
          echo $Obj->Grilla_Listar_Formula($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Formula":
          echo $Obj->PopUp_Mant_Formula($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_ArticuloFormula":
          echo $Obj->PopUp_Mant_ArticuloFormula($_REQUEST["prm"]);
          break;
      case "Mant_ArticuloFormula":
          $resp= $Obj->Mant_ArticuloFormula($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]);  echo "<script>Operacion_Result(true);Operacion_Reload('ordenproduccion','Grilla_Listar_Formula','','" . $valor[0] . "','div_ArticuloFormula');</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_ArticuloFormula":
          echo $Obj->Confirma_Eliminar_ArticuloFormula($_REQUEST["prm"]);
          break;
      case "Eliminar_ArticuloFormula":
          $resp= $Obj->Eliminar_ArticuloFormula($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]);  echo "<script>Operacion_Result(true);Operacion_Reload('ordenproduccion','Grilla_Listar_Formula','','" . $valor[0] . "','div_ArticuloFormula');</script>";}
          else echo '<script>Operacion_Result(false);</script>';

/******************************************************************** FÓRMULAS *************************************************************************/
      case "Grilla_Listar_Tarifario":
          echo $Obj->Grilla_Listar_Tarifario($_REQUEST["prm"]);
          break;

      case "Grilla_Listar_Tarifa":
          echo $Obj->Grilla_Listar_Tarifa($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Tarifario":
          echo $Obj->PopUp_Mant_Tarifario($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Tarifa":
          echo $Obj->PopUp_Mant_Tarifa($_REQUEST["prm"]);
          break;
      case "Mant_Tarifa":
          $resp= $Obj->Mant_Tarifa($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]);  echo "<script>Operacion_Result(true);Operacion_Reload('ordenproduccion','Grilla_Listar_Tarifa','','" . $valor[0] . "','div_Tarifa');Buscar_Grilla('ordenproduccion','Grilla_Listar_Tarifario','tbl_listartarifario','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Confirma_Eliminar_Tarifa":
          echo $Obj->Confirma_Eliminar_Tarifa($_REQUEST["prm"]);
          break;
      case "Eliminar_Tarifa":
          $resp= $Obj->Eliminar_Tarifa($_REQUEST["prm"]);
          if($resp!=-1){ $valor =explode('|',$_REQUEST["prm"]);  echo "<script>Operacion_Result(true);Operacion_Reload('ordenproduccion','Grilla_Listar_Tarifa','','" . $valor[0] . "','div_Tarifa');Buscar_Grilla('ordenproduccion','Grilla_Listar_Tarifario','tbl_listartarifario','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';

      default:
          echo $_REQUEST["opc"];
        break;
  }
?>