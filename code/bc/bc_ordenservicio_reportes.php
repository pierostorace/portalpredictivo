<?php
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_ordenservicio_reportes.php"); $Obj=new bl_ordenservicio_reportes;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_OSProductosxTienda":
          echo $Obj->Grilla_Listar_OSProductosxTienda($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_OSClientesxFecha":
          echo $Obj->Grilla_Listar_OSClientesxFecha($_REQUEST["prm"]);
          break;

      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>