<?php   
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_reporte.php"); $Obj=new bl_reporte;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_ReporteVenta":
          echo $Obj->Grilla_Listar_Reporte_Ventas($_REQUEST["prm"]);
          break;      
  }
  ob_end_flush();
?>