<?php  
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_crm.php"); $Obj=new bl;
  switch($_REQUEST["opc"])
  {
      case "Grilla_Listar_Clientes":
          echo $Obj->Grilla_Listar_Clientes($_REQUEST["prm"]);
          break;
      case "Nuevo":
          echo $Obj->Nuevo_Cliente($_REQUEST["prm"]);
          break;
      case "Registrar_Cliente":
          $resp= $Obj->Actualizar_DatosGenerales($_REQUEST["prm"]);
          if($resp!=-1) echo "<script>Buscar_Grilla('crm','Grilla_Listar_Clientes','tbl_listarclientes','','td_General');Cargar_Combo('general','Combo_Cliente_Reload','fil_cmbBusquedacliente','','fil_cmbBusquedacliente');</script>";
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Detalles":
          echo $Obj->Detalles_Cliente($_REQUEST["prm"]);
          break;
      case "Actualizar_Cliente":
          echo $Obj->Actualizar_Cliente($_REQUEST["prm"]);
          break;
      case "Confirma_Eliminar":
          echo $Obj->Confirma_Eliminar_Cliente($_REQUEST["prm"]);
          break;
      case "Eliminar_Cliente":
          echo $Obj->Eliminar_Cliente($_REQUEST["prm"]);
          break;

/***********************************************************DETALLES DEL CLIENTE*****************************************************************/
      case "Actualizar_DatosGenerales":
          $resp= $Obj->Actualizar_DatosGenerales($_REQUEST["prm"]);
          if($resp!=-1) echo '<script>Operacion_Result(true);</script>';
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Grilla_Listar_Direcciones_Cliente":
          $prm=explode('|',$_REQUEST["prm"]);
          echo $Obj->Grilla_Listar_Direcciones_Cliente($prm[0],(count($prm)>1?$prm[1]:''),(count($prm)>2?$prm[2]:''),(count($prm)>3?$prm[3]:''),(count($prm)>4?$prm[4]:''),(count($prm)>5?$prm[5]:''),(count($prm)>6?$prm[6]:''),(count($prm)>7?$prm[7]:''),(count($prm)>8?$prm[8]:''));
          break;
      case "PopUp_Mant_Direccion":
//          echo "<script>Ver_PopUp('" .$Obj->PopUp_Mant_Direccion($_REQUEST["prm"]) . "');</script>";
          echo $Obj->PopUp_Mant_Direccion($_REQUEST["prm"]);
          break;
      case "Mant_Direccion_Cliente":
          echo $Obj->Mant_Direccion_Cliente($_REQUEST["prm"]);
          break;
      case "Confirma_Eliminar_Direccion_Cliente":
          echo $Obj->Confirma_Eliminar_Direccion_Cliente($_REQUEST["prm"]);
          break;
      case "Eliminar_Direccion_Cliente":
          echo $Obj->Eliminar_Direccion_Cliente($_REQUEST["prm"]);
          break;
      case "Grilla_Listar_Contactos_Cliente":
          $prm=explode('|',$_REQUEST["prm"]);
          echo $Obj->Grilla_Listar_Contactos_Cliente($prm[0],(count($prm)>1?$prm[1]:''),(count($prm)>2?$prm[2]:''),(count($prm)>3?$prm[3]:''),(count($prm)>4?$prm[4]:''),(count($prm)>5?$prm[5]:''),(count($prm)>6?$prm[6]:''),(count($prm)>7?$prm[7]:''));
          break;
      case "PopUp_Mant_Contacto":
//          echo "<script>Ver_PopUp('" .$Obj->PopUp_Mant_Contacto($_REQUEST["prm"]) . "');</script>";
          echo $Obj->PopUp_Mant_Contacto($_REQUEST["prm"]);
          break;
      case "Mant_Contacto_Cliente":
          echo $Obj->Mant_Contacto_Cliente($_REQUEST["prm"]);
          break;
      case "Confirma_Eliminar_Contacto_Cliente":
          echo $Obj->Confirma_Eliminar_Contacto_Cliente($_REQUEST["prm"]);
          break;
      case "Eliminar_Contacto_Cliente":
          echo $Obj->Eliminar_Contacto_Cliente($_REQUEST["prm"]);
          break;
      case "Actualizar_DatosFinancieros":
          echo $Obj->Actualizar_DatosFinancieros($_REQUEST["prm"]);
          break;

      case "Grilla_Listar_Credito_Cliente":
          echo $Obj->Grilla_Listar_Credito_Cliente($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Credito":
          echo $Obj->PopUp_Mant_Credito($_REQUEST["prm"]);
          break;
      case "Mant_Credito_Cliente":
          echo $Obj->Mant_Credito_Cliente($_REQUEST["prm"]);
          break;
      case "Confirma_Eliminar_Credito_Cliente":
          echo $Obj->Confirma_Eliminar_Credito_Cliente($_REQUEST["prm"]);
          break;
      case "Eliminar_Credito_Cliente":
          echo $Obj->Eliminar_Credito_Cliente($_REQUEST["prm"]);
          break;

      case "Grilla_Listar_Visitas_Cliente":
          $prm=explode('|',$_REQUEST["prm"]);
          echo $Obj->Grilla_Listar_Visitas_Cliente($prm[0],(count($prm)>1?$prm[1]:''),(count($prm)>2?$prm[2]:''),(count($prm)>3?$prm[3]:''),(count($prm)>4?$prm[4]:''));
          break;
      case "PopUp_Mant_Visita":
//          echo '<script>Ver_PopUp("' . $Obj->PopUp_Mant_Visita($_REQUEST["prm"]) . '");</script>';
          echo $Obj->PopUp_Mant_Visita($_REQUEST["prm"]);
          break;
      case "Mant_Visita_Cliente":
          $resp=$Obj->Mant_Visita_Cliente($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Visitas_Cliente','','" . $valor[0] . "','div_Visitas_Cliente');</script>"; }
          else echo  "<script>Operacion_Result(false);</script>";
          break;
      case "Confirma_Eliminar_Visita_Cliente":
          echo $Obj->Confirma_Eliminar_Visita_Cliente($_REQUEST["prm"]);
          break;
      case "Eliminar_Visita_Cliente":
          echo $Obj->Eliminar_Visita_Cliente($_REQUEST["prm"]);
          break;

      case "Grilla_Listar_Acuerdos_Cliente":
          $prm=explode('|',$_REQUEST["prm"]);
          echo $Obj->Grilla_Listar_Acuerdos_Cliente($prm[0],(count($prm)>1?$prm[1]:''),(count($prm)>2?$prm[2]:''));
          break;
      case "Grilla_Listar_Visita_Acuerdos_Cliente":
          $prm=explode('|',$_REQUEST["prm"]);
          echo $Obj->Grilla_Listar_Visita_Acuerdos_Cliente($prm[0],(count($prm)>1?$prm[1]:''));
          break;
      case "PopUp_Mant_Acuerdo":
          echo $Obj->PopUp_Mant_Acuerdo($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Visita_Acuerdo":
          echo $Obj->PopUp_Mant_Visita_Acuerdo($_REQUEST["prm"]);
          break;
      case "Mant_Acuerdo_Cliente":
          $resp=$Obj->Mant_Acuerdo_Cliente($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Acuerdos_Cliente','','" . $valor[0] . "','div_Acuerdos_Cliente');</script>";}
          else echo "<script>Operacion_Result(false);</script>";
          break;
      case "Mant_Visita_Acuerdo_Cliente":
          $resp= $Obj->Mant_Acuerdo_Cliente($_REQUEST["prm"]);
          if($resp!=-1){$valor=explode('|',$_REQUEST["prm"]);echo "<script>Cerrar_PopUp();Operacion_Reload('crm','Grilla_Listar_Visita_Acuerdos_Cliente','','" . $_REQUEST["prm"] . "','div_tbl_Visita_Acuerdo_Cliente');Operacion_Reload('crm','Grilla_Listar_Acuerdos_Cliente','','" . $valor[0] . "','div_Acuerdos_Cliente');</script>";}
          else echo "<script>Operacion_Result(false);</script>";
          break;
      case "Confirma_Eliminar_Acuerdo_Cliente":
          echo $Obj->Confirma_Eliminar_Acuerdo_Cliente($_REQUEST["prm"]);
          break;
      case "Confirma_Eliminar_Visita_Acuerdo_Cliente":
          echo $Obj->Confirma_Eliminar_Visita_Acuerdo_Cliente($_REQUEST["prm"]);
          break;
      case "Eliminar_Acuerdo_Cliente":
          $resp= $Obj->Eliminar_Acuerdo_Cliente($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Acuerdos_Cliente','','" . $valor[0] . "','div_Acuerdos_Cliente');</script>";}
          else echo "<script>Operacion_Result(false);</script>";
          break;
      case "Eliminar_Visita_Acuerdo_Cliente":
          $resp= $Obj->Eliminar_Acuerdo_Cliente($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Cerrar_PopUp();Operacion_Reload('crm','Grilla_Listar_Visita_Acuerdos_Cliente','','" . $_REQUEST["prm"] . "','div_tbl_Visita_Acuerdo_Cliente');Operacion_Reload('crm','Grilla_Listar_Acuerdos_Cliente','','" . $valor[0] . "','div_Acuerdos_Cliente');</script>";}
          else echo "<script>Operacion_Result(false);</script>";
          break;

      case "Grilla_Listar_Pendientes_Cliente":
          $prm=explode('|',$_REQUEST["prm"]);
          echo $Obj->Grilla_Listar_Pendientes_Cliente($prm[0],(count($prm)>1?$prm[1]:''),(count($prm)>2?$prm[2]:''),(count($prm)>3?$prm[3]:''));
          break;
      case "Grilla_Listar_Visita_Pendientes_Cliente":
          $prm=explode('|',$_REQUEST["prm"]);
          echo $Obj->Grilla_Listar_Visita_Pendientes_Cliente($prm[0],(count($prm)>1?$prm[1]:''));
          break;
      case "PopUp_Mant_Pendiente":
          echo $Obj->PopUp_Mant_Pendiente($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Visita_Pendiente":
          echo $Obj->PopUp_Mant_Visita_Pendiente($_REQUEST["prm"]);
          break;
      case "Mant_Pendiente_Cliente":
          $resp= $Obj->Mant_Pendiente_Cliente($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Pendientes_Cliente','','" . $valor[0] . "','div_Pendientes_Cliente');</script>";}
          else echo "<script>Operacion_Result(false);</script>";
          break;
      case "Mant_Visita_Pendiente_Cliente":
          $resp= $Obj->Mant_Pendiente_Cliente($_REQUEST["prm"]);
          if($resp!=-1){$valor=explode('|',$_REQUEST["prm"]);echo "<script>Cerrar_PopUp();Operacion_Reload('crm','Grilla_Listar_Visita_Pendientes_Cliente','','" . $_REQUEST["prm"] . "','div_tbl_Visita_Pendiente_Cliente');Operacion_Reload('crm','Grilla_Listar_Pendientes_Cliente','','" . $valor[0] . "','div_Pendientes_Cliente');</script>";}
          else echo "<script>Operacion_Result(false);</script>";
          break;
      case "Confirma_Eliminar_Pendiente_Cliente":
          echo $Obj->Confirma_Eliminar_Pendiente_Cliente($_REQUEST["prm"]);
          break;
      case "Confirma_Eliminar_Visita_Pendiente_Cliente":
          echo $Obj->Confirma_Eliminar_Visita_Pendiente_Cliente($_REQUEST["prm"]);
          break;
      case "Eliminar_Pendiente_Cliente":
          $resp= $Obj->Eliminar_Pendiente_Cliente($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Pendientes_Cliente','','" . $valor[0] . "','div_Pendientes_Cliente');</script>";}
          else echo "<script>Operacion_Result(false);</script>";
          break;
      case "Eliminar_Visita_Pendiente_Cliente":
          $resp= $Obj->Eliminar_Pendiente_Cliente($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Cerrar_PopUp();Operacion_Reload('crm','Grilla_Listar_Visita_Pendientes_Cliente','','" . $_REQUEST["prm"] . "','div_tbl_Visita_Pendiente_Cliente');Operacion_Reload('crm','Grilla_Listar_Pendientes_Cliente','','" . $valor[0] . "','div_Pendientes_Cliente');</script>";}
          else echo "<script>Operacion_Result(false);</script>";
          break;

/***********************************************************VISITAS*****************************************************************/
      case "Grilla_Listar_Visitas":
          $prm=explode('|',$_REQUEST["prm"]);
          echo $Obj->Grilla_Listar_Visitas($prm[0],(count($prm)>1?$prm[1]:''),(count($prm)>2?$prm[2]:''),(count($prm)>3?$prm[3]:''));
          break;
      case "Grilla_Listar_Visitas_Pendientes":
          $prm=explode('|',$_REQUEST["prm"]);
          echo $Obj->Grilla_Listar_Visitas_Pendientes($prm[0],(count($prm)>1?$prm[1]:''));
          break;
      case "Grilla_Listar_Visitas_Acuerdos":
          $prm=explode('|',$_REQUEST["prm"]);
          echo $Obj->Grilla_Listar_Visitas_Acuerdos($prm[0],(count($prm)>1?$prm[1]:''));
          break;
      case "PopUp_Mant_Visitas":
          echo $Obj->PopUp_Mant_Visitas($_REQUEST["prm"]);
          break;
      case "Mant_Visitas":
          $resp=$Obj->Mant_Visitas($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Visitas','','','td_General');</script>"; }
          else echo  "<script>Operacion_Result(false);</script>";
          break;
      case "PopUp_Mant_Visitas_Acuerdo":
          echo $Obj->PopUp_Mant_Visitas_Acuerdo($_REQUEST["prm"]);
          break;
      case "PopUp_Mant_Visitas_Pendiente":
          echo $Obj->PopUp_Mant_Visitas_Pendiente($_REQUEST["prm"]);
          break;
      case "Mant_Visitas_Acuerdo":
          $resp= $Obj->Mant_Acuerdo_Cliente($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Cerrar_PopUp();Operacion_Reload('crm','Grilla_Listar_Visitas_Acuerdos','','" . $_REQUEST["prm"] . "','div_tbl_Visitas_Acuerdo');</script>";
          else echo "<script>Operacion_Result(false);</script>";
          break;
      case "Mant_Visitas_Pendiente":
          $resp= $Obj->Mant_Pendiente_Cliente($_REQUEST["prm"]);
          if($resp!=-1)echo "<script>Cerrar_PopUp();Operacion_Reload('crm','Grilla_Listar_Visitas_Pendientes','','" . $_REQUEST["prm"] . "','div_tbl_Visitas_Pendiente');</script>";
          else echo "<script>Operacion_Result(false);</script>";
          break;
      case "Confirma_Eliminar_Visitas_Acuerdo":
          echo $Obj->Confirma_Eliminar_Visitas_Acuerdo($_REQUEST["prm"]);
          break;
      case "Eliminar_Visitas_Acuerdo":
          $resp= $Obj->Eliminar_Acuerdo_Cliente($_REQUEST["prm"]);
          if($resp!=-1){ echo "<script>Cerrar_PopUp();Operacion_Reload('crm','Grilla_Listar_Visitas_Acuerdos','','" . $_REQUEST["prm"] . "','div_tbl_Visitas_Acuerdo');</script>";}
          else echo "<script>Operacion_Result(false);</script>";
          break;
      case "Confirma_Eliminar_Visitas_Pendiente":
          echo $Obj->Confirma_Eliminar_Visitas_Pendiente($_REQUEST["prm"]);
          break;
      case "Eliminar_Visitas_Pendiente":
          $resp= $Obj->Eliminar_Pendiente_Cliente($_REQUEST["prm"]);
          if($resp!=-1){ echo "<script>Cerrar_PopUp();Operacion_Reload('crm','Grilla_Listar_Visitas_Pendientes','','" . $_REQUEST["prm"] . "','div_tbl_Visitas_Pendiente');</script>";}
          else echo "<script>Operacion_Result(false);</script>";
          break;

      default:
          echo $_REQUEST["opc"];
        break;
  }
  ob_end_flush();
?>