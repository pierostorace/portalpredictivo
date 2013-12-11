<?php
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
  switch($_REQUEST["opc"])
  {
  	  case "Exportar_Excel":
  	  	  include_once ("../lib/export_excelhelper.php"); $ExcelExport=new excelhelper;
  	  	  $ExcelExport->export($_REQUEST["prm"]);
  	  	  break; 
  	  case "Combo_Tamano_x_CategoriaProducto":
  	  	  include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_tamano_x_categoriaproducto_reload($prm[0],"");
          break;
      case "Combo_Cliente_Reload":
  	  	  include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;  	  	  
  	  	  echo $Helper->combo_cliente_reload("", "");
  	  	  break;
  	  case "Combo_Producto_x_Cliente":
  	  	  include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
  	  	  $prm = explode('|',$_REQUEST["prm"]);
  	  	  echo $Helper->combo_producto_cliente_reload($prm[0], "");
  	  	  break;
      case "Combo_Colores_x_CategoriaProducto":
  	  	  include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_colores_x_categoriaproducto_reload($prm[0],"");
          break;
      case "Combo_Calidad_x_CategoriaProducto":
      	  include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_calidad_x_categoriaproducto_reload($prm[0],"");
          break;
      case "Combo_Modelo_x_CategoriaProducto":
      	  include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_modelo_x_categoriaproducto_reload($prm[0],"");
          break;
  	  case "Combo_Departamentos":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_departamento_x_paises_reload($prm[0],"");
          break;
      case "Combo_Provincias":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_provincia_x_departamento_reload($prm[0],"");
          break;
      case "Combo_Distritos":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_distrito_x_provincia_reload($prm[0],"");
          break;
      case "Combo_Direccion_x_Cliente":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_direccion_x_cliente_reload($prm[0],"");
          break;      
      case "Combo_Contactos_x_Cliente":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_contactos_x_cliente_reload($prm[0],"");
          break;
      case "Combo_PersonaporTipo_reload":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_persona_x_tipo_reload($prm[0],"");
          break;
      case "Combo_Chofer_x_Transportista":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_chofer_x_transportista_reload($prm[0],"");
          break;
      case "Combo_Vehiculo_x_Transportista":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_vehiculo_x_transportista_reload($prm[0],"");
          break;
      case "Valor_Producto_Precio":
          include_once ("../bl/bl_ordenproduccion.php"); $Obj=new bl_ordenproduccion;
          $prm = explode('|',$_REQUEST["prm"]);
          if(count($prm)>1) echo $Obj->Obtener_Producto_Precio($prm[1],$prm[0]);
          else echo $Obj->Obtener_Producto_Precio($prm[0],0);
          break;
      case "Valor_Producto_UnidadxBulto":
          include_once ("../bl/bl_ordenproduccion.php"); $Obj=new bl_ordenproduccion;
          $prm = explode('|',$_REQUEST["prm"]);
          if(count($prm)>1) echo $Obj->Obtener_Producto_UnidadesxBulto($prm[1],$prm[0]);
          else echo $Obj->Obtener_Producto_UnidadesxBulto($prm[0],0);
          break;
      case "Valor_Producto_UnidadMedida":
          include_once ("../bl/bl_ordenproduccion.php"); $Obj=new bl_ordenproduccion;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Obj->Obtener_Producto_UnidadMedida($prm[0],(count($prm)>1?$prm[1]:0));
          break;
      case "Valor_Producto_Stock":
          include_once ("../bl/bl_ordenproduccion.php"); $Obj=new bl_ordenproduccion;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Obj->Obtener_Producto_Stock($prm[0]);
          break;

      case "Combo_Articulos":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          echo $Helper->combo_articulo_reload("");
          break;
      case "Combo_Productos":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          echo $Helper->combo_producto_reload("");
          break;
      case "Combo_Insumos":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          echo $Helper->combo_insumo_reload("");
          break;
      case "Combo_ArticulosVentaCliente":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_articuloventa_x_cliente_reload($prm[0],"");
          break;
      case "Combo_ArticulosVenta":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          echo $Helper->combo_articuloventa_reload("");
          break;
      case "Combo_ArticulosCompra":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          echo $Helper->combo_articulocompra_reload("");
          break;
      case "Combo_Articulos_ComprobanteCompra":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_articulo_comprobantecompra_reload($prm[0],"");
          break;
      case "Combo_Articulos_ComprobanteVenta":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_articulo_comprobanteventa_reload($prm[0],"");
          break;

      case "Combo_TipoCategoria_TipoComprobante":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_categoria_tipocomprobante($prm[0],"");
          break;

      case "Combo_Guias_x_OC":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_guiaremision_x_ordencompra_reload($prm[0],"");
          break;

      case "Combo_TipoOperacion_Movimiento":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;
          $prm = explode('|',$_REQUEST["prm"]);
          echo $Helper->combo_tipooperacion_movimiento_reload($prm[0],"");
          break;

      case "Combo_Caja":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;          
          echo $Helper->combo_caja_reload("");
          break;
          
      case "Combo_TipoMovimiento":
          include_once ("../lib/htmlhelper.php"); $Helper=new htmlhelper;          
          echo $Helper->combo_tipo_movimiento("");
          break;
          
      case "Numeracion_Comprobante":
          include_once ("../bl/bl_general.php"); $Helper=new bl_general;
          $prm = explode('|',$_REQUEST["prm"]);$div=explode('|',$_REQUEST["div"]);
          $comprobante=$Helper->Obtener_Caracteristica_Comprobante($prm[0]);
          $script ="SetDocValue('" . $div[0] . "','" . ($comprobante["serie"]!=0?str_pad($comprobante["serie"],$comprobante["digitosserie"],'0', STR_PAD_LEFT):"") . "');";
          $disabled=($comprobante["autonumerico"]==0?false:true);
          if($disabled=='true') $script .="SetDocValue('" . $div[1] . "','" . str_pad($comprobante["correlativo"],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "');";
          else $script .="SetDocValue('" . $div[1] . "','');";
          $script.="DisabledControl('" . $div[0] . "'," . ($prm[0]==""?0:($comprobante["serie"]==0?0:1)) . ");";
          $script.="DisabledControl('" . $div[1] . "','" . $disabled . "');";
          echo "<script>" . $script . "</script>";
          break;

      default:
          echo $_REQUEST["opc"];
        break;
  }
?>