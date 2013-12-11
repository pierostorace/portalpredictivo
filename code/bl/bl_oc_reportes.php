<?php include_once('../../code/lib/htmlhelper.php'); include_once('../../code/bl/bl_general.php');
  Class bl_oc_reportes
  {
      function Filtros_Reporte($opc){
         switch($opc)
         {
            case 1: return $this->Filtros_Listar_OCProductosxTienda($opc);
              break;
            case 2: return $this->Filtros_Listar_OCClientesxFecha($opc);
              break;

            default: return "";
              break;
         }
      }
      function Grilla_Reporte($prm){
         $valor=explode('|',$prm);
         switch(intval($valor[0],10))
         {
            case 1: return $this->Grilla_Listar_OCProductosxTienda($prm);
              break;
            case 2: return $this->Grilla_Listar_OCClientesxFecha($prm);
              break;

            default: return "";
              break;
         }
      }
      function Obtener_Nombre_Reporte($opc){
         switch($opc)
         {
            case 1: return htmlentities("Producto por Tienda según OC");
              break;
            case 2: return htmlentities("Órdenes de Compra por Cliente según Fecha");
              break;

            default: return "";
              break;
         }
      }
/********************************************************CUENTAS POR COBRAR**************************************************************************/
      function Filtros_Listar_OCProductosxTienda($opc){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$index=10;
         $inputs=array(
            "Nro. OC" => $Helper->textbox("fil_txtBusquedaNroComprobante",++$index,"",128,100,$TipoTxt->texto,"","","",""),
            "Nro. OC Cliente" => $Helper->textbox("fil_txtBusquedaNroOCCliente",++$index,"",128,100,$TipoTxt->texto,"","","","")
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('oc_reportes','Grilla_Listar_OCProductosxTienda','tbl_listarcomprobante','" . $opc . "','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listarcomprobante",$inputs,$buttons,2,550,"","");
      }
      function Grilla_Listar_OCProductosxTienda($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $os=explode(',',$valor[1]);
         $sql="SELECT distinct ifnull(dir.sgo_int_direccion,'0') as sgo_int_direccion,CASE WHEN ifnull(dir.sgo_int_direccion,0)!=0 THEN Concat(dir.sgo_vch_codigotienda,'-',dir.sgo_vch_nombretiendaalias) ELSE '** SIN LOCAL **' END as sgo_vch_direccion,
         oc.sgo_vch_nroordencompra as OC,per.sgo_vch_nombre as Cliente,CASE oc.sgo_int_tiporecepcion WHEN 3 THEN 'TIENDA' ELSE 'CENTRO DISTRIBUCION' END as TipoDireccion,
         DATE_FORMAT(oc.sgo_dat_fechainiciovigencia,'%d/%m/%Y') as IniVigencia,DATE_FORMAT(oc.sgo_dat_fechafinvigencia,'%d/%m/%Y') as FinVigencia,oc.sgo_vch_nroordencompracliente as 'OCCliente'
         FROM tbl_sgo_ordencompra oc
         INNER JOIN tbl_sgo_ordencompradetalle ocd ON oc.sgo_int_ordencompra=ocd.sgo_int_ordencompra
         INNER JOIN tbl_sgo_persona per ON per.sgo_int_persona=oc.sgo_int_cliente
         LEFT JOIN tbl_sgo_direccioncliente dir ON dir.sgo_int_direccion=ocd.sgo_int_direccion
         LEFT JOIN tbl_sgo_direccioncliente diroc ON diroc.sgo_int_direccion=oc.sgo_int_direccion
         WHERE 1=1";
         if(count($os)>0)
         {
           $contador=1;
           $sql.= " and oc.sgo_vch_nroordencompra in (";
           foreach ($os as &$valor) {
                if($contador==count($os)){
                    $sql.= "'".$valor."'" ;}else{$sql.= "'".$valor."',";}$contador++;
           }
           $sql.=")";
         }
         $sql.= ($valor[2]!=""?" OR oc.sgo_vch_nroordencompracliente like '%" . $valor[2] . "%'":"") . " ORDER BY 2";

         $result=$Obj->consulta($sql);$locales=array();$oc="";$cliente="";$entrega="";$vigencia="";$oc_cliente="";
         while ($row = mysqli_fetch_array($result)){
             $locales[$row["sgo_int_direccion"]]=$row["sgo_vch_direccion"];
             $oc=$row["OC"];$cliente=$row["Cliente"];$entrega=$row["TipoDireccion"];$vigencia="Del " . $row["IniVigencia"] . " al " . $row["FinVigencia"];$oc_cliente=$row["OCCliente"];
         }
         $sql ="(SELECT 0 as param,
          pdt.sgo_vch_nombre as PRODUCTO ";
         foreach ($locales as $key => $value){
             $sql .=",round(SUM(CASE ifnull(dir.sgo_int_direccion,'0') WHEN " . $key . " THEN round(ocd.sgo_dec_cantidad,2) ELSE 0 END),0) as '" . $value . "'";
         }
         $sql .=",round(SUM(ocd.sgo_dec_cantidad),2) as TOTAL FROM tbl_sgo_ordencompra oc
          INNER JOIN tbl_sgo_ordencompradetalle ocd ON ocd.sgo_int_ordencompra=oc.sgo_int_ordencompra
          INNER JOIN tbl_sgo_producto pdt ON pdt.sgo_int_producto=ocd.sgo_int_producto
          LEFT JOIN tbl_sgo_direccioncliente dir ON dir.sgo_int_direccion=ocd.sgo_int_direccion
         WHERE oc.sgo_vch_nroordencompra like '" . ($valor[1]!=""?"%" . $valor[1] . "%":"") . "' " . ($valor[2]!=""?" OR oc.sgo_vch_nroordencompracliente like '%" . $valor[2] . "%' ":"") . "
         GROUP BY oc.sgo_vch_nroordencompra,oc.sgo_vch_nroordencompracliente,pdt.sgo_vch_nombre ORDER BY 3)
         UNION
         (SELECT '--totales--' as param,'Totales'";
          foreach ($locales as $key => $value){
             $sql .=",round(SUM(CASE ifnull(dir.sgo_int_direccion,'0') WHEN " . $key . " THEN round(ocd.sgo_dec_cantidad,0) ELSE 0 END),0) as '" . $value . "'";
         }
         $sql .=",round(SUM(ocd.sgo_dec_cantidad),2) as TOTAL FROM tbl_sgo_ordencompra oc
          INNER JOIN tbl_sgo_ordencompradetalle ocd ON ocd.sgo_int_ordencompra=oc.sgo_int_ordencompra
          INNER JOIN tbl_sgo_producto pdt ON pdt.sgo_int_producto=ocd.sgo_int_producto
          LEFT JOIN tbl_sgo_direccioncliente dir ON dir.sgo_int_direccion=ocd.sgo_int_direccion
          WHERE oc.sgo_vch_nroordencompra like '" . ($valor[1]!=""?"%" . $valor[1] . "%":"") . "' " . ($valor[2]!=""?" OR oc.sgo_vch_nroordencompracliente like '%" . $valor[2] . "%' ":"") . ")";
//          echo $sql;
         $titulo= ($cliente!=""?$cliente . "<br/>" . $entrega . "<br/>O/C Int. " . $oc . "  -  O/C Cliente " . $oc_cliente . "<br/>Vigencia " . $vigencia:"");
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"","","","",null,array(),array(),array(),20,$titulo);         
      }

/********************************************************CUENTAS POR PAGAR**************************************************************************/
      function Filtros_Listar_OCClientesxFecha($opc){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Persona" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('oc_reportes','Grilla_Listar_OCClientesxFecha','tbl_listarcomprobante','" . $opc . "','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listarcomprobante",$inputs,$buttons,2,990,"","");
      }
      function Grilla_Listar_OCClientesxFecha($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT 0 as param, DATE_FORMAT(oc.sgo_dat_fecharegistro, '%d/%m/%Y') as 'Fecha Registro',oc.sgo_vch_nroordencompra as OC,oc.sgo_vch_nroordencompracliente as 'OC Cliente',oc.sgo_vch_albaran as Albaran,
          per.sgo_vch_nombre as 'Persona',COUNT(distinct ocd.sgo_int_producto) as Productos, FORMAT(SUM(ocd.sgo_dec_cantidad * ocd.sgo_dec_precio),2) as 'Monto Total'
          FROM tbl_sgo_ordencompra oc
          LEFT JOIN tbl_sgo_persona per ON oc.sgo_int_cliente=per.sgo_int_persona
          LEFT JOIN tbl_sgo_ordencompradetalle ocd ON ocd.sgo_int_ordencompra=oc.sgo_int_ordencompra ";
         $where = $Obj->sql_where("WHERE per.sgo_int_persona=@p1 and (oc.sgo_dat_fecharegistro BETWEEN '@p2 00:00' and '@p3 23:59')",
                 $valor[1] . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $Helper->convertir_fecha_ingles($valor[3]));
         $orderby = " GROUP BY oc.sgo_dat_fecharegistro,oc.sgo_vch_nroordencompra,oc.sgo_vch_nroordencompracliente,oc.sgo_vch_albaran,per.sgo_vch_nombre ORDER BY oc.sgo_dat_fecharegistro ASC";
//         echo $sql . " " . $where . " " . $orderby;
         $titulo="O/C por Clientes según rango de fecha";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"","","","",null,array(),array(),array(),20,$titulo);
      }

  }
?>