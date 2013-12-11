<?php  
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_finanzas_reportes.php"); $Obj=new bl_finanzas_reportes;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_CuentasxCobrar":
          echo $Obj->Grilla_Listar_CuentasxCobrar($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_CuentasxPagar":
          echo $Obj->Grilla_Listar_CuentasxPagar($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_EstadoCuenta":
          echo $Obj->Grilla_Listar_EstadoCuenta($_REQUEST["prm"]);
          break;

      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>