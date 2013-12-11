<?php
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  include_once ("../bl/bl_configuracion.php"); $Obj=new bl_configuracion;
  switch($_REQUEST["opc"])
  {
  	/***********************************************************CATEGORIA PRODUCTO CALIDAD******************************************************/
      case "Grilla_Listar_CategoriaProductoCalidad":
          echo $Obj->Grilla_Listar_CategoriaProductoCalidad($_REQUEST["prm"]);
          break;
      case "PopupCategoriaProductoCalidad":
          echo $Obj->PopUp_CategoriaCalidad($_REQUEST["prm"]);
          break;
	  case "ConfirmaEliminarCategoriaProductoCalidad":
          echo $Obj->Confirma_Eliminar_CategoriaProductoCalidad($_REQUEST["prm"]);
          break;
      case "Registrar_CategoriaProductoCalidad":
          $resp= $Obj->Mant_CategoriaProductoCalidad($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);" .  ($valor[0]!=0?"PopUp('configuracion','PopupCategoriaProductoCalidad','','" . $resp . "');":"") . "Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoCalidad','tbl_lista','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Eliminar_CategoriaProductoCalidad":
          echo $Obj->Eliminar_CategoriaProductoCalidad($_REQUEST["prm"]);
          break;
    /***********************************************************CATEGORIA PRODUCTO MODELO******************************************************/
      case "Grilla_Listar_CategoriaProductoModelo":
          echo $Obj->Grilla_Listar_CategoriaProductoModelo($_REQUEST["prm"]);
          break;
      case "PopupCategoriaProductoModelo":
          echo $Obj->PopUp_CategoriaModelo($_REQUEST["prm"]);
          break;
	  case "ConfirmaEliminarCategoriaProductoModelo":
          echo $Obj->Confirma_Eliminar_CategoriaProductoModelo($_REQUEST["prm"]);
          break;
      case "Registrar_CategoriaProductoModelo":
          $resp= $Obj->Mant_CategoriaProductoModelo($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);" .  ($valor[0]!=0?"PopUp('configuracion','PopupCategoriaProductoModelo','','" . $resp . "');":"") . "Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoModelo','tbl_lista','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Eliminar_CategoriaProductoModelo":
          echo $Obj->Eliminar_CategoriaProductoModelo($_REQUEST["prm"]);
          break;
  	/***********************************************************CATEGORIA PRODUCTO COLOR******************************************************/
      case "Grilla_Listar_CategoriaProductoColor":
          echo $Obj->Grilla_Listar_CategoriaProductoColor($_REQUEST["prm"]);
          break;
      case "PopupCategoriaProductoColor":
          echo $Obj->PopUp_CategoriaColor($_REQUEST["prm"]);
          break;
	  case "ConfirmaEliminarCategoriaProductoColor":
          echo $Obj->Confirma_Eliminar_CategoriaProductoColor($_REQUEST["prm"]);
          break;
      case "Registrar_CategoriaProductoColor":
          $resp= $Obj->Mant_CategoriaProductoColor($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);" .  ($valor[0]!=0?"PopUp('configuracion','PopupCategoriaProductoColor','','" . $resp . "');":"") . "Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoColor','tbl_lista','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Eliminar_CategoriaProductoColor":
          echo $Obj->Eliminar_CategoriaProductoColor($_REQUEST["prm"]);
          break;
  	/***********************************************************CATEGORIA PRODUCTO TAMANO******************************************************/
      case "Grilla_Listar_CategoriaProductoTamano":
          echo $Obj->Grilla_Listar_CategoriaProductoTamano($_REQUEST["prm"]);
          break;
      case "PopupCategoriaProductoTamano":
          echo $Obj->PopUp_CategoriaTamano($_REQUEST["prm"]);
          break;
	  case "ConfirmaEliminarCategoriaProductoTamano":
          echo $Obj->Confirma_Eliminar_CategoriaProductoTamano($_REQUEST["prm"]);
          break;
      case "Registrar_CategoriaProductoTamano":
          $resp= $Obj->Mant_CategoriaProductoTamano($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);" .  ($valor[0]!=0?"PopUp('configuracion','PopupCategoriaProductoTamano','','" . $resp . "');":"") . "Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoTamano','tbl_lista','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Eliminar_CategoriaProductoTamano":
          echo $Obj->Eliminar_CategoriaProductoTamano($_REQUEST["prm"]);
          break;
/***********************************************************TRANSPORTISTA*****************************************************************/
      case "Grilla_Listar_Transportista":
          echo $Obj->Grilla_Listar_Transportista($_REQUEST["prm"]);
          break;
      case "PopupTransportista":
          echo $Obj->PopUp_Transportista($_REQUEST["prm"]);
          break;
	  case "ConfirmaEliminarTransportista":
          echo $Obj->Confirma_Eliminar_Transportista($_REQUEST["prm"]);
          break;
      case "Registrar_Transportista":
          $resp= $Obj->Mant_Transportista($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);" .  ($valor[0]==0?"PopUp('configuracion','PopupTransportista','','" . $resp . "');":"") . "Buscar_Grilla('configuracion','Grilla_Listar_Transportista','tbl_listatransportista','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Eliminar_Transportista":
          echo $Obj->Eliminar_Transportista($_REQUEST["prm"]);
          break;
/***********************************************************CHOFER*****************************************************************/
      case "Grilla_Listar_Chofer":
          echo $Obj->Grilla_Listar_Chofer($_REQUEST["prm"]);
          break;
      case "PopupChofer":
          echo $Obj->PopUp_Chofer($_REQUEST["prm"]);
          break;
	  case "ConfirmaEliminarChofer":
          echo $Obj->Confirma_Eliminar_Chofer($_REQUEST["prm"]);
          break;
      case "Registrar_Chofer":
          $resp= $Obj->Mant_Chofer($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);" .  ($valor[0]!=0?"PopUp('configuracion','PopupChofer','','" . $resp . "');":"") . "Buscar_Grilla('configuracion','Grilla_Listar_Chofer','tbl_listachofer','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Eliminar_Chofer":
          echo $Obj->Eliminar_Chofer($_REQUEST["prm"]);
          break;
/***********************************************************TIPO DE CAMBIO*****************************************************************/
      case "Grilla_Listar_TipoCambio":
          echo $Obj->Grilla_Listar_TipoCambio($_REQUEST["prm"]);
          break;
      case "PopupTipoCambio":
          echo $Obj->PopUp_TipoCambio($_REQUEST["prm"]);
          break;
	  case "ConfirmaEliminarTipoCambio":
          echo $Obj->Confirma_Eliminar_TipoCambio($_REQUEST["prm"]);
          break;
      case "Registrar_TipoCambio":
          $resp= $Obj->Mant_TipoCambio($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);" .  ($valor[0]!=0?"PopUp('configuracion','PopupTipoCambio','','" . $resp . "');":"") . "Buscar_Grilla('configuracion','Grilla_Listar_TipoCambio','tbl_listatipocambio','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Eliminar_TipoCambio":
          echo $Obj->Eliminar_TipoCambio($_REQUEST["prm"]);
          break;
/***********************************************************CHOFER*****************************************************************/
      case "Grilla_Listar_Vehiculo":
          echo $Obj->Grilla_Listar_Vehiculo($_REQUEST["prm"]);
          break;
      case "PopupVehiculo":
          echo $Obj->PopUp_Vehiculo($_REQUEST["prm"]);
          break;
	  case "ConfirmaEliminarVehiculo":
          echo $Obj->Confirma_Eliminar_Vehiculo($_REQUEST["prm"]);
          break;
      case "Registrar_Vehiculo":
          $resp= $Obj->Mant_Vehiculo($_REQUEST["prm"]);
          if($resp!=-1){ $valor=explode('|',$_REQUEST["prm"]);echo "<script>Operacion_Result(true);" .  ($valor[0]!=0?"PopUp('configuracion','PopupVehiculo','','" . $resp . "');":"") . "Buscar_Grilla('configuracion','Grilla_Listar_Vehiculo','tbl_listavehiculo','','td_General',1);</script>";}
          else echo '<script>Operacion_Result(false);</script>';
          break;
      case "Eliminar_Vehiculo":
          echo $Obj->Eliminar_Vehiculo($_REQUEST["prm"]);
          break;
 /***********************************************************ESTADO COTIZACION*****************************************************************/
      case "Grilla_Listar_EstadoCotizacion":
          echo $Obj->Grilla_Listar_EstadoCotizacion($_REQUEST["prm"]);
          break;
      case "Nuevo":
          echo $Obj->Nuevo_EstadoCotizacion($_REQUEST["prm"]);
          break;
	  case "Detalles":
          echo $Obj->Detalles_EstadoCotizacion($_REQUEST["prm"]);
          break;
	  case "Confirma_Eliminar":
          echo $Obj->Confirma_Eliminar_EstadoCotizacion($_REQUEST["prm"]);
          break;
      case "Registrar_EstadoCotizacion":
          echo $Obj->Actualizar_EstadoCotizacion($_REQUEST["prm"]);
          break;     
      case "Eliminar_EstadoCotizacion":
          echo $Obj->Eliminar_Estado_Cotizacion($_REQUEST["prm"]);
          break;
 /***********************************************************ESTADO COTIZACION*****************************************************************/
      case "Grilla_Listar_EstadoFactura":
          echo $Obj->Grilla_Listar_EstadoFactura($_REQUEST["prm"]);
          break;
      case "Nuevo_Mant_EstadoFactura":
          echo $Obj->Nuevo_EstadoFactura($_REQUEST["prm"]);
          break;
	  case "Detalles_Mant_EstadoFactura":
          echo $Obj->Detalles_EstadoFactura($_REQUEST["prm"]);
          break;
	  case "Confirma_Eliminar_Mant_EstadoFactura":
          echo $Obj->Confirma_Eliminar_EstadoFactura($_REQUEST["prm"]);
          break;
      case "Registrar_EstadoFactura":
          echo $Obj->Actualizar_EstadoFactura($_REQUEST["prm"]);
          break;     
      case "Eliminar_EstadoFactura":
          echo $Obj->Eliminar_Estado_Factura($_REQUEST["prm"]);
          break;
 /***********************************************************ESTADO GUIA REMISION*****************************************************************/
      case "Grilla_Listar_EstadoGuiaRemision":
          echo $Obj->Grilla_Listar_EstadoGuiaRemision($_REQUEST["prm"]);
          break;
      case "Nuevo_Mant_EstadoGuiaRemision":
          echo $Obj->Nuevo_EstadoGuiaRemision($_REQUEST["prm"]);
          break;
	  case "Detalles_Mant_EstadoGuiaRemision":
          echo $Obj->Detalles_EstadoGuiaRemision($_REQUEST["prm"]);
          break;
	  case "Confirma_Eliminar_Mant_EstadoGuiaRemision":
          echo $Obj->Confirma_Eliminar_EstadoGuiaRemision($_REQUEST["prm"]);
          break;
      case "Registrar_EstadoGuiaRemision":
          echo $Obj->Actualizar_EstadoGuiaRemision($_REQUEST["prm"]);
          break;     
      case "Eliminar_EstadoGuiaRemision":
          echo $Obj->Eliminar_Estado_GuiaRemision($_REQUEST["prm"]);
          break;		
 /***********************************************************ESTADO ORDEN DE COMPRA*****************************************************************/
      case "Grilla_Listar_EstadoOrdenCompra":
          echo $Obj->Grilla_Listar_EstadoOrdenCompra($_REQUEST["prm"]);
          break;
      case "Nuevo_Mant_EstadoOrdenCompra":
          echo $Obj->Nuevo_EstadoOrdenCompra($_REQUEST["prm"]);
          break;
	  case "Detalles_Mant_EstadoOrdenCompra":
          echo $Obj->Detalles_EstadoOrdenCompra($_REQUEST["prm"]);
          break;
	  case "Confirma_Eliminar_Mant_EstadoOrdenCompra":
          echo $Obj->Confirma_Eliminar_EstadoOrdenCompra($_REQUEST["prm"]);
          break;
      case "Registrar_EstadoOrdenCompra":
          echo $Obj->Actualizar_EstadoOrdenCompra($_REQUEST["prm"]);
          break;     
      case "Eliminar_EstadoOrdenCompra":
          echo $Obj->Eliminar_Estado_OrdenCompra($_REQUEST["prm"]);
          break;
 /***********************************************************ESTADO VISITA*****************************************************************/
      case "Grilla_Listar_EstadoVisita":
          echo $Obj->Grilla_Listar_EstadoVisita($_REQUEST["prm"]);
          break;
      case "Nuevo_Mant_EstadoVisita":
          echo $Obj->Nuevo_EstadoVisita($_REQUEST["prm"]);
          break;
	  case "Detalles_Mant_EstadoVisita":
          echo $Obj->Detalles_EstadoVisita($_REQUEST["prm"]);
          break;
	  case "Confirma_Eliminar_Mant_EstadoVisita":
          echo $Obj->Confirma_Eliminar_EstadoVisita($_REQUEST["prm"]);
          break;
      case "Registrar_EstadoVisita":
          echo $Obj->Actualizar_EstadoVisita($_REQUEST["prm"]);
          break;     
      case "Eliminar_EstadoVisita":
          echo $Obj->Eliminar_Estado_Visita($_REQUEST["prm"]);
          break;	
 /***********************************************************ESTADO ORDEN PRODUCCION*****************************************************************/
      case "Grilla_Listar_EstadoOrdenProduccion":
          echo $Obj->Grilla_Listar_EstadoOrdenProduccion($_REQUEST["prm"]);
          break;
      case "Nuevo_Mant_EstadoOrdenProduccion":
          echo $Obj->Nuevo_EstadoOrdenProduccion($_REQUEST["prm"]);
          break;
	  case "Detalles_Mant_EstadoOrdenProduccion":
          echo $Obj->Detalles_EstadoOrdenProduccion($_REQUEST["prm"]);
          break;
	  case "Confirma_Eliminar_Mant_EstadoOrdenProduccion":
          echo $Obj->Confirma_Eliminar_EstadoOrdenProduccion($_REQUEST["prm"]);
          break;
      case "Registrar_EstadoOrdenProduccion":
          echo $Obj->Actualizar_EstadoOrdenProduccion($_REQUEST["prm"]);
          break;     
      case "Eliminar_EstadoOrdenProduccion":
          echo $Obj->Eliminar_Estado_OrdenProduccion($_REQUEST["prm"]);
          break;
 /*********************************************************** ALMACEN - CATEGORIA INSUMO *****************************************************************/
      case "Grilla_Listar_CategoriaInsumo":
          echo $Obj->Grilla_Listar_CategoriaInsumo($_REQUEST["prm"]);
          break;
      case "Nuevo_Mant_CategoriaInsumo":
          echo $Obj->Nuevo_CategoriaInsumo($_REQUEST["prm"]);
          break;
	  case "Detalles_Mant_CategoriaInsumo":
          echo $Obj->Detalles_CategoriaInsumo($_REQUEST["prm"]);
          break;
	  case "Confirma_Eliminar_Mant_CategoriaInsumo":
          echo $Obj->Confirma_Eliminar_CategoriaInsumo($_REQUEST["prm"]);
          break;
      case "Registrar_CategoriaInsumo":
          echo $Obj->Actualizar_CategoriaInsumo($_REQUEST["prm"]);
          break;     
      case "Eliminar_CategoriaInsumo":
          echo $Obj->Eliminar_Estado_CategoriaInsumo($_REQUEST["prm"]);
          break;	 
 /*********************************************************** ALMACEN - CARACTERISTICA *****************************************************************/
      case "Grilla_Listar_CaracteristicaProducto":
          echo $Obj->Grilla_Listar_CaracteristicaProducto($_REQUEST["prm"]);
          break;
      case "Nuevo_Mant_CaracteristicaProducto":
          echo $Obj->Nuevo_CaracteristicaProducto($_REQUEST["prm"]);
          break;
	  case "Detalles_Mant_CaracteristicaProducto":
          echo $Obj->Detalles_CaracteristicaProducto($_REQUEST["prm"]);
          break;
	  case "Confirma_Eliminar_Mant_CaracteristicaProducto":
          echo $Obj->Confirma_Eliminar_CaracteristicaProducto($_REQUEST["prm"]);
          break;
      case "Registrar_CaracteristicaProducto":
          echo $Obj->Actualizar_CaracteristicaProducto($_REQUEST["prm"]);
          break;     
      case "Eliminar_CaracteristicaProducto":
          echo $Obj->Eliminar_Estado_CaracteristicaProducto($_REQUEST["prm"]);
          break;	 
  /*********************************************************** ALMACEN - CATEGORIA PRODUCTO *****************************************************************/
      case "Grilla_Listar_CategoriaProducto":
          echo $Obj->Grilla_Listar_CategoriaProducto($_REQUEST["prm"]);
          break;
      case "Nuevo_Mant_CategoriaProducto":
          echo $Obj->Nuevo_CategoriaProducto($_REQUEST["prm"]);
          break;
	  case "Detalles_Mant_CategoriaProducto":
          echo $Obj->Detalles_CategoriaProducto($_REQUEST["prm"]);
          break;
	  case "Confirma_Eliminar_Mant_CategoriaProducto":
          echo $Obj->Confirma_Eliminar_CategoriaProducto($_REQUEST["prm"]);
          break;
      case "Registrar_CategoriaProducto":
          echo $Obj->Actualizar_CategoriaProducto($_REQUEST["prm"]);
          break;     
      case "Eliminar_CategoriaProducto":
          echo $Obj->Eliminar_Estado_CategoriaProducto($_REQUEST["prm"]);
          break;	 
      default:
          echo $_REQUEST["opc"];
        break;
  }
?>