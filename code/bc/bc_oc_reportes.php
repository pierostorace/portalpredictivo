<?php
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_oc_reportes.php"); $Obj=new bl_oc_reportes;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_OCProductosxTienda":
          echo $Obj->Grilla_Listar_OCProductosxTienda($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_OCClientesxFecha":
          echo $Obj->Grilla_Listar_OCClientesxFecha($_REQUEST["prm"]);
          break;

      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>