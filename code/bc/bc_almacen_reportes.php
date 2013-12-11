<?php 
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_almacen_reportes.php"); $Obj=new bl_almacen_reportes;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_StockxAlmacen":
          echo $Obj->Grilla_Listar_StockxAlmacen($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_KardexAlmacen":
          echo $Obj->Grilla_Listar_KardexAlmacen($_REQUEST["prm"]);
          break;

      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>