<?php include_once('../../code/lib/htmlhelper.php'); include_once('../../code/bl/bl_general.php');
  Class bl_almacen_reportes
  {
      function Filtros_Reporte($opc){
         switch($opc)
         {
            case 1: return $this->Filtros_Listar_StockxAlmacen($opc);
              break;
            case 2: return $this->Filtros_Listar_KardexAlmacen($opc);
              break;
            default: return "";
              break;
         }
      }
      function Grilla_Reporte($prm){
         $valor=explode('|',$prm);
         switch(intval($valor[0],10))
         {
            case 1: return $this->Grilla_Listar_StockxAlmacen($prm);
              break;
            case 2: return $this->Grilla_Listar_KardexAlmacen($prm);
              break;
            default: return "";
              break;
         }
      }
      function Obtener_Nombre_Reporte($opc){
         switch($opc)
         {
            case 1: return "Stock por Almac&eacute;n";
              break;
            case 2: return "Kardex por Almac&eacute;n";
              break;
            default: return "";
              break;
         }
      }
/********************************************************CUENTAS POR COBRAR**************************************************************************/
      function Filtros_Listar_StockxAlmacen($opc){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Almacén" => $Helper->combo_almacen("fil_cmbBusquedaAlmacen",$index,"",""),
            "Artículo" => $Helper->combo_articulo("fil_cmbBusquedaArticulo",++$index,"",""),
            "Tipo Producto" => $Helper->combo_tipoproducto("fil_cmbBusquedatipoproducto",++$index,1,"",""),
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('almacen_reportes','Grilla_Listar_StockxAlmacen','tbl_listarreportes','" . $opc . "','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listarreportes",$inputs,$buttons,3,1050,"","");
      }
      function Grilla_Listar_StockxAlmacen($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT '' as param,sed.sgo_vch_nombre as 'Sede', alm.sgo_vch_nombre as 'Almacén',tpd.sgo_vch_descripcion as 'Tipo Articulo',pdt.sgo_vch_nombre as 'Artículo',pal.sgo_dec_stock as 'Stock'
          FROM tbl_sgo_productoalmacen pal
          INNER JOIN tbl_sgo_producto pdt ON pdt.sgo_int_producto=pal.sgo_int_producto
          INNER JOIN tbl_sgo_almacen alm ON alm.sgo_int_almacen=pal.sgo_int_almacen
          INNER JOIN tbl_sgo_sede sed ON sed.sgo_int_sede=alm.sgo_int_sede
          INNER JOIN tbl_sgo_tipoproducto tpd ON tpd.sgo_int_tipoproducto=pdt.sgo_int_tipo";
         $where = $Obj->sql_where("WHERE alm.sgo_int_almacen=@p1 and pdt.sgo_int_producto=@p2 and tpd.sgo_int_tipoproducto=@p3",$valor[1] . '|' . $valor[2] . '|' . $valor[3]);
         $orderby = "ORDER BY alm.sgo_vch_nombre ASC,pdt.sgo_vch_nombre ASC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"","","","");
      }

/********************************************************CUENTAS POR PAGAR**************************************************************************/
      function Filtros_Listar_KardexAlmacen($opc){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Almacén" => $Helper->combo_almacen("fil_cmbBusquedaAlmacen",$index,"",""),
            "Artículo" => $Helper->combo_articulo("fil_cmbBusquedaArticulo",++$index,"",""),
            "Tipo Producto" => $Helper->combo_tipoproducto("fil_cmbBusquedatipoproducto",++$index,1,"",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('almacen_reportes','Grilla_Listar_KardexAlmacen','tbl_listarreportes','" . $opc . "','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listarreportes",$inputs,$buttons,2,1000,"","");
      }
      function Grilla_Listar_KardexAlmacen($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $where = $Obj->sql_where("WHERE pdt.sgo_int_producto=@p1 and tpd.sgo_int_tipoproducto=@p2 and (kde.sgo_dat_fechaentrada BETWEEN '@p3 00:00' and '@p4 23:59')",
                 $valor[2] . '|' . $valor[3] . '|' . $Helper->convertir_fecha_ingles($valor[4]) . '|' . $Helper->convertir_fecha_ingles($valor[5]));
         $sql ="(SELECT kde.sgo_int_kardexentrada as param, pdt.sgo_vch_nombre as 'Artículo',und.sgo_vch_abreviatura as 'Und. Medida',
                kde.sgo_dec_saldo as Saldo,kde.sgo_dec_cantidad as Ingreso, '' as Salida, kde.sgo_dec_stock as Stock,kde.sgo_dat_fechaentrada as 'Fecha Movimiento',kde.sgo_txt_observaciones as Observación
                FROM tbl_sgo_kardexentrada kde
                INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto = kde.sgo_int_producto
                INNER JOIN tbl_sgo_tipoproducto tpd ON tpd.sgo_int_tipoproducto=pdt.sgo_int_tipo
                INNER JOIN tbl_sgo_unidadmedida und ON und.sgo_int_unidadmedida=pdt.sgo_int_unidadmedida " . $where . ")";
         $where = $Obj->sql_where("WHERE pdt.sgo_int_producto=@p1 and tpd.sgo_int_tipoproducto=@p2 and (kds.sgo_dat_fechasalida BETWEEN '@p3 00:00' and '@p4 23:59')",
                 $valor[2] . '|' . $valor[3] . '|' . $Helper->convertir_fecha_ingles($valor[4]) . '|' . $Helper->convertir_fecha_ingles($valor[5]));
         $sql .= " UNION ALL
                (SELECT kds.sgo_int_kardexsalida as param, pdt.sgo_vch_nombre as 'Artículo', und.sgo_vch_abreviatura as 'Und. Medida',
                kds.sgo_dec_saldo as Saldo,'' as Entrada, kds.sgo_dec_cantidad as Salida, kds.sgo_dec_stock as Stock, kds.sgo_dat_fechasalida as 'Fecha Movimiento',kds.sgo_txt_observaciones as Observación
                FROM tbl_sgo_kardexsalida kds
                INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto = kds.sgo_int_producto
                INNER JOIN tbl_sgo_tipoproducto tpd ON tpd.sgo_int_tipoproducto=pdt.sgo_int_tipo
                INNER JOIN tbl_sgo_unidadmedida und ON und.sgo_int_unidadmedida=pdt.sgo_int_unidadmedida
                " . $where . ")";
         $orderby = "ORDER BY 2 ASC,7 DESC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $orderby),"","PopUp('compras','PopUp_Mant_Compras','','","","");
      }
  }
?>