<?php  header('Content-type: text/html; charset=ISO-8859-1'); 
	if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_seguridad.php"); $Obj=new bl_seguridad;
  switch($_REQUEST["opc"])
  {
 /***********************************************************usuario*****************************************************************/
      case "Grilla_Listar_usuario":
          echo $Obj->Grilla_Listar_usuario($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_usuarioperfil":
          echo $Obj->Grilla_Listar_usuarioperfil($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_usuario":
          echo $Obj->PopUp_Mant_usuario($_REQUEST["prm"]);
          break;
      case "Mant_usuario":
          echo $_REQUEST["opc"];
          $resp= $Obj->Mant_usuario($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true,0);Buscar_Grilla('seguridad','Grilla_Listar_usuario','tbl_listar_usuario','','td_General');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
	  case "Confirma_Eliminar_usuario":
          echo $Obj->Confirma_Eliminar_usuario($_REQUEST["prm"]);
          break;
      case "Eliminar_usuario":
          $resp= $Obj->Eliminar_usuario($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Result(true,0);Buscar_Grilla('seguridad','Grilla_Listar_usuario','tbl_listar_usuario','','td_General');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Mant_usuarioperfil":
          $resp= $Obj->Mant_usuarioperfil($_REQUEST["prm"]);
          $valor=explode('|',$_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Operacion_Reload('seguridad','Grilla_Listar_usuarioperfil','tbl_mant_usuarioperfil','" . $valor[0] . "','div_mant_usuarioperfil');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
	  case "Confirma_Eliminar_usuarioperfil":
          echo $Obj->Confirma_Eliminar_usuarioperfil($_REQUEST["prm"]);
          break;
      case "Eliminar_usuarioperfil":
          $resp= $Obj->Eliminar_usuarioperfil($_REQUEST["prm"]);
          $valor=explode('|',$_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Cerrar_PopUp();Operacion_Reload('seguridad','Grilla_Listar_usuarioperfil','tbl_mant_usuarioperfil','" . $valor[0] . "','div_mant_usuarioperfil');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;

      default:
          echo $_REQUEST["opc"];
        break;
  }
?>