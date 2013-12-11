<?php include_once('../../code/lib/htmlhelper.php'); include_once('../../code/bl/bl_general.php');include_once('../../code/lib/loghelper.php');
  Class bl_ordenservicio_reportes
  {
      function Filtros_Reporte($opc){
         switch($opc)
         {
            case 1: return $this->Filtros_Listar_OSProductosxTienda($opc);
              break;
            case 2: return $this->Filtros_Listar_OSClientesxFecha($opc);
              break;

            default: return "";
              break;
         }
      }
      function Grilla_Reporte($prm){
         $valor=explode('|',$prm);
         switch(intval($valor[0],10))
         {
            case 1: return $this->Grilla_Listar_OSProductosxTienda($prm);
              break;
            case 2: return $this->Grilla_Listar_OSClientesxFecha($prm);
              break;

            default: return "";
              break;
         }
      }
      function Obtener_Nombre_Reporte($opc){
         switch($opc)
         {
            case 1: return htmlentities("Producto por Tienda según OS");
              break;
            case 2: return htmlentities("Órdenes de Servicio por Cliente según Fecha");
              break;

            default: return "";
              break;
         }
      }
/********************************************************REPORTE DE PRODUCTOS POR TIENDA**************************************************************************/
      function Filtros_Listar_OSProductosxTienda($opc){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$index=10;
         $Val_Tamano=new InputValidacion();
         $Val_Tamano->InputValidacion('DocValue("fil_txtTamano")!=""','Debe especificar el orden por tamaño');
         $Val_Categoria=new InputValidacion();
         $Val_Categoria->InputValidacion('DocValue("fil_txtCategoria")!=""','Debe especificar el orden por categoria');
         $Val_Color=new InputValidacion();
         $Val_Color->InputValidacion('DocValue("fil_txtColor")!=""','Debe especificar el orden por color');
         $Val_Calidad=new InputValidacion();
         $Val_Calidad->InputValidacion('DocValue("fil_txtCalidad")!=""','Debe especificar el orden por color');
         $inputs=array(
            "Nro. OS" => $Helper->textbox("fil_txtBusquedaNroComprobante",++$index,"",5000,300,$TipoTxt->texto,"","","",""),
            "Nro. OC Cliente" => $Helper->textbox("fil_txtBusquedaNroOCCliente",++$index,"",100,300,$TipoTxt->texto,"","","",""),
         	"Tamaño"=> $Helper->textbox("fil_txtTamano",++$index,"",1,40,$TipoTxt->texto,"","","","",$Val_Tamano),
         	"Categoría"=> $Helper->textbox("fil_txtCategoria",++$index,"",1,40,$TipoTxt->texto,"","","","",$Val_Categoria),
         	"Color"=> $Helper->textbox("fil_txtColor",++$index,"",1,40,$TipoTxt->texto,"","","","",$Val_Color),
         	"Calidad"=> $Helper->textbox("fil_txtCalidad",++$index,"",1,40,$TipoTxt->texto,"","","","",$Val_Calidad)
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('ordenservicio_reportes','Grilla_Listar_OSProductosxTienda','tbl_listarcomprobante','" . $opc . "','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listarcomprobante",$inputs,$buttons,2,900,"","");
      }
      function Grilla_Listar_OSProductosxTienda($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$os="";$oc_cliente="";
         $log=new loghelper;
         if($valor[2]!="")$oc_cliente=$valor[2];
         if($valor[1]!="")$os=$valor[1];
         $valor[1]="'" . str_replace(",","','",$valor[1]) . "'";
         if($valor[2]!="")$valor[2]="'" . str_replace(",","','",$valor[2]) . "'";
         $sql="	SELECT distinct ifnull(dir.sgo_int_direccion,'0') as sgo_int_direccion,
         		CASE 	
         				WHEN ifnull(dir.sgo_int_direccion,0)!=0 
         				THEN Concat(dir.sgo_vch_codigotienda,'-',dir.sgo_vch_nombretiendaalias) 
         				ELSE '** SIN LOCAL **' 
         		END as sgo_vch_direccion,
         		Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero) as OS,
         		per.sgo_vch_nombre as Cliente,CASE oc.sgo_int_tiporecepcion WHEN 3 THEN 'TIENDA' ELSE 'CENTRO DISTRIBUCION' END as TipoDireccion,
         		DATE_FORMAT(oc.sgo_dat_fechainiciovigencia,'%d/%m/%Y') as IniVigencia,DATE_FORMAT(oc.sgo_dat_fechafinvigencia,'%d/%m/%Y') as FinVigencia, oc.sgo_vch_nroordencompracliente as 'OCCliente'
         FROM tbl_sgo_ordenservicio oc
         INNER JOIN tbl_sgo_ordenserviciodetalle ocd ON oc.sgo_int_ordenservicio=ocd.sgo_int_ordenservicio
         INNER JOIN tbl_sgo_persona per ON per.sgo_int_persona=oc.sgo_int_cliente
         LEFT JOIN tbl_sgo_direccioncliente dir ON dir.sgo_int_direccion=ocd.sgo_int_direccion
         LEFT JOIN tbl_sgo_direccioncliente diroc ON diroc.sgo_int_direccion=oc.sgo_int_direccion
         WHERE Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero) in (" . ($valor[1]!=""?$valor[1]:"") . ") " . ($valor[2]!=""?" OR oc.sgo_vch_nroordencompracliente in (" . $valor[2] . ") ":"") . " ORDER BY 2";
         $result=$Obj->consulta($sql);$locales=array();$cliente="";$entrega="";$vigencia="";
         while ($row = mysqli_fetch_array($result)){
             $locales[$row["sgo_int_direccion"]]= utf8_decode($row["sgo_vch_direccion"]);
             if($os=="")$os=$row["OS"];$cliente=$row["Cliente"];$entrega=$row["TipoDireccion"];$vigencia="Del " . $row["IniVigencia"] . " al " . $row["FinVigencia"];if($oc_cliente=="")$oc_cliente="--";//$row["OCCliente"];
         }
         
         $sql ="(SELECT 0 as param,
          CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodtam.sgo_vch_tamano,''), ' ', ifnull(catprodcol.sgo_vch_color,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as PRODUCTO ";
         foreach ($locales as $key => $value){
             $sql .=",round(SUM(CASE ifnull(dir.sgo_int_direccion,'0') WHEN " . $key . " THEN round(ocd.sgo_dec_cantidad,0) ELSE 0 END),0) as '" . $value . "'";
         }
         $sql .=",round(SUM(ocd.sgo_dec_cantidad),0) as TOTAL, ocd.sgo_txt_observaciones as 'OBSERVACIONES' 
         FROM tbl_sgo_ordenservicio oc
         INNER JOIN tbl_sgo_ordenserviciodetalle ocd ON ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio
         INNER JOIN tbl_sgo_producto pdt ON pdt.sgo_int_producto=ocd.sgo_int_producto
         LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
         on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
         and	pdt.sgo_int_color = catprodcol.sgo_int_color
         LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
         on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
         and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
         LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
         on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
         and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
         LEFT JOIN tbl_sgo_direccioncliente dir ON dir.sgo_int_direccion=ocd.sgo_int_direccion
         WHERE Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero) in (" . ($valor[1]!=""?$valor[1]:"") . ") " . ($valor[2]!=""?" OR oc.sgo_vch_nroordencompracliente in (" . $valor[2] . ") ":"") . "
         and	pdt.sgo_bit_activo=1 and oc.sgo_int_estado <> 4
         GROUP BY pdt.sgo_vch_nombre,   catprodcol.sgo_vch_color, catprodtam.sgo_vch_tamano, catprodcal.sgo_vch_calidad";
         $auxOrderBy="";
         $mayor=0;
         if($valor[3]=="" && $valor[4]=="" && $valor[5]=="" && $valor[6]=="")
         {
         	$sql.=" ORDER	BY pdt.sgo_int_categoriaproducto, catprodcol.sgo_int_orden, catprodtam.sgo_int_orden , catprodcal.sgo_int_orden desc";	
         }	   
         else
         {
         	$orderby[0] = ($valor[3]==""?99:$valor[3]);
         	$orderby[1] = ($valor[4]==""?99:$valor[4]);
         	$orderby[2] = ($valor[5]==""?99:$valor[5]);
         	$orderby[3] = ($valor[6]==""?99:$valor[6]);
         	
         	asort($orderby);
         	while($val = current($orderby))
         	{
         		if($val!="")
         		{
         			switch (key($orderby))
         			{
         				case 0: $auxOrderBy.=($val!=99?" catprodtam.sgo_int_orden,":"");
         				break;
         				case 1: $auxOrderBy.=($val!=99?" pdt.sgo_int_categoriaproducto,":"");
         				break;
         				case 2: $auxOrderBy.=($val!=99?" catprodcol.sgo_int_orden,":"");
         				break;
         				case 3: $auxOrderBy.=($val!=99?" catprodcal.sgo_int_orden,":"");
         				break;
         			}         			         			
         		}
         		next($orderby);
         	}
         	$auxOrderBy = substr($auxOrderBy, 0,strlen($auxOrderBy)-1);
         	$sql.=" ORDER BY ".$auxOrderBy." asc";
         }
         $sql.=" LIMIT 1000
         )         
         UNION
         (SELECT '--totales--' as param,'Unidades'";

         //oc.sgo_vch_serie,oc.sgo_vch_numero,oc.sgo_vch_nroordencompracliente,pdt.sgo_vch_nombre
          foreach ($locales as $key => $value){
             $sql .=",round(SUM(CASE ifnull(dir.sgo_int_direccion,'0') WHEN " . $key . " THEN round(ocd.sgo_dec_cantidad,0) ELSE 0 END),0) as '" . $value . "'";
         }
         $sql .=",round(SUM(ocd.sgo_dec_cantidad),0) as TOTAL, '' as 'OBSERVACIONES' FROM tbl_sgo_ordenservicio oc
          INNER JOIN tbl_sgo_ordenserviciodetalle ocd ON ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio
          INNER JOIN tbl_sgo_producto pdt ON pdt.sgo_int_producto=ocd.sgo_int_producto
          LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
          on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
          and	pdt.sgo_int_color = catprodcol.sgo_int_color
          LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
          on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
          and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano
          LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
          on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
          and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad	
          LEFT JOIN tbl_sgo_direccioncliente dir ON dir.sgo_int_direccion=ocd.sgo_int_direccion          
          WHERE Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero) in (" . ($valor[1]!=""?$valor[1]:"") . ") " . ($valor[2]!=""?" 
          OR oc.sgo_vch_nroordencompracliente in (" . $valor[2] . ") ":"") . " and	pdt.sgo_bit_activo=1 and oc.sgo_int_estado <> 4 
          )          
          UNION
         (SELECT '--totales--' as param,'Bultos'";
          foreach ($locales as $key => $value){
             $sql .=",SUM(CASE ifnull(dir.sgo_int_direccion,'0') WHEN " . $key . " THEN CASE ocd.sgo_int_unidadesporbulto WHEN 0 THEN 1 ELSE round((ocd.sgo_dec_cantidad/ocd.sgo_int_unidadesporbulto),0) END ELSE 0 END) as '" . $value . "'";
         }
         $sql .=", round(SUM(CASE sgo_int_unidadesporbulto WHEN 0 THEN 1 ELSE (ocd.sgo_dec_cantidad/ocd.sgo_int_unidadesporbulto) END),0)  as TOTAL, '' as 'OBSERVACIONES' FROM tbl_sgo_ordenservicio oc
          INNER JOIN tbl_sgo_ordenserviciodetalle ocd ON ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio
          INNER JOIN tbl_sgo_producto pdt ON pdt.sgo_int_producto=ocd.sgo_int_producto
          LEFT JOIN tbl_sgo_direccioncliente dir ON dir.sgo_int_direccion=ocd.sgo_int_direccion
          WHERE Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero) in (" . ($valor[1]!=""?$valor[1]:"") . ") " . ($valor[2]!=""?" OR oc.sgo_vch_nroordencompracliente in (" . $valor[2] . ") ":"") . " and	pdt.sgo_bit_activo=1 and oc.sgo_int_estado <> 4)
          ";
          $log->log($sql);
         $titulo= ($cliente!=""?$cliente . "<br/>" . "O/C " . $oc_cliente . "<br/>" . $vigencia:"");
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"","","","",null,array(),array(),array(),200,$titulo);
      }

/********************************************************CUENTAS POR PAGAR**************************************************************************/
      function Filtros_Listar_OSClientesxFecha($opc){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Persona" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('ordenservicio_reportes','Grilla_Listar_OSClientesxFecha','tbl_listarcomprobante','" . $opc . "','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listarcomprobante",$inputs,$buttons,2,990,"","");
      }
      function Grilla_Listar_OSClientesxFecha($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT 0 as param, DATE_FORMAT(oc.sgo_dat_fecharegistro, '%d/%m/%Y') as 'Fecha Registro',Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero) as OS,oc.sgo_vch_nroordencompracliente as 'OC Cliente',oc.sgo_vch_albaran as Albaran,
          per.sgo_vch_nombre as 'Persona',COUNT(distinct ocd.sgo_int_producto) as Productos, FORMAT(SUM(ocd.sgo_dec_cantidad * ocd.sgo_dec_precio),2) as 'Monto Total'
          FROM tbl_sgo_ordenservicio oc
          LEFT JOIN tbl_sgo_persona per ON oc.sgo_int_cliente=per.sgo_int_persona
          LEFT JOIN tbl_sgo_ordenserviciodetalle ocd ON ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio ";
         $where = $Obj->sql_where("WHERE per.sgo_int_persona=@p1 and (oc.sgo_dat_fecharegistro BETWEEN '@p2 00:00' and '@p3 23:59')",
                 $valor[1] . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $Helper->convertir_fecha_ingles($valor[3]));
         $orderby = " GROUP BY oc.sgo_dat_fecharegistro,oc.sgo_vch_serie,oc.sgo_vch_numero,oc.sgo_vch_nroordencompracliente,oc.sgo_vch_albaran,per.sgo_vch_nombre ORDER BY oc.sgo_dat_fecharegistro ASC";
//         echo $sql . " " . $where . " " . $orderby;
         $titulo="O/S por Clientes seg&uacute;n rango de fecha";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"","","","",null,array(),array(),array(),20,$titulo);
      }

  }
?>