<?php include_once('../../code/lib/htmlhelper.php'); include_once('../../code/bl/bl_general.php');include_once('../../code/lib/loghelper.php');
  Class bl_finanzas_reportes
  {
      function Filtros_Reporte($opc){
         switch($opc)
         {
            case 1: return $this->Filtros_Listar_CuentasxCobrar($opc);
              break;
            case 2: return $this->Filtros_Listar_CuentasxPagar($opc);
              break;
            case 3: return $this->Filtros_Listar_EstadoCuenta($opc);
              break;
            default: return "";
              break;
         }
      }
      function Grilla_Reporte($prm){
         $valor=explode('|',$prm);
         switch(intval($valor[0],10))
         {
            case 1: return $this->Grilla_Listar_CuentasxCobrar($prm);
              break;
            case 2: return $this->Grilla_Listar_CuentasxPagar($prm);
              break;
            case 3: return $this->Grilla_Listar_EstadoCuenta($prm);
              break;
            default: return "";
              break;
         }
      }
      function Obtener_Nombre_Reporte($opc){
         switch($opc)
         {
            case 1: return "Cuentas por Cobrar";
              break;
            case 2: return "Cuentas por Pagar";
              break;
            case 3: return "Estado de Cuenta";
              break;
            default: return "";
              break;
         }
      }
/********************************************************CUENTAS POR COBRAR**************************************************************************/
      function Filtros_Listar_CuentasxCobrar($opc){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Persona" => $Helper->combo_persona("fil_cmbBusquedaproveedor",$index,"",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Tipo Comprobante" => $Helper->combo_tipocomprobante("fil_cmbBusquedatipocomprobante",$index,1,"",""),
            "Nro. Comprobante" => $Helper->textbox("fil_txtBusquedaNroComprobante",++$index,"",11,100,$TipoTxt->texto,"","","","")
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('finanzas_reportes','Grilla_Listar_CuentasxCobrar','tbl_listarcomprobante','" . $opc . "','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarcomprobante",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_CuentasxCobrar($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="	SELECT 	distinct doc.sgo_int_documentoporcobrar as param, 
         					DATE_FORMAT(doc.sgo_dat_fecharegistro, '%d/%m/%Y') as 'Fecha Registro',
         					DATE_FORMAT(ADDDATE(doc.sgo_dat_fecharegistro,(select sgo_int_diascredito from tbl_sgo_datosfinancieroscliente where sgo_int_cliente=per.sgo_int_persona)), '%d/%m/%Y') as 'Fecha Vencimiento', 
         					per.sgo_vch_nombre as 'Persona',
         					tco.sgo_vch_descripcion as 'Tipo Comprobante',
         					Concat(doc.sgo_vch_serie,'-',doc.sgo_vch_numero) as 'Nro. Comprobante',
         					mon.sgo_vch_simbolo as Moneda,
         					doc.sgo_dec_total as 'Total',
         					doc.sgo_dec_saldo as 'Saldo', 
         					doc.sgo_vch_observacion as 'Observación'
          			FROM 	tbl_sgo_documentoporcobrar doc
          			INNER 	JOIN tbl_sgo_moneda mon ON mon.sgo_int_moneda=doc.sgo_int_moneda
          			INNER 	JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=doc.sgo_int_tipocomprobante
          			INNER 	JOIN tbl_sgo_persona per ON doc.sgo_int_persona=per.sgo_int_persona ";
         $where = $Obj->sql_where("WHERE per.sgo_int_persona=@p1 and (doc.sgo_dat_fecharegistro BETWEEN '@p2 00:00' and '@p3 23:59') and doc.sgo_int_tipocomprobante=@p4 and sgo_vch_nrocomprobante like '%@p5%' and doc.sgo_dec_saldo!=0",
                 $valor[1] . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $Helper->convertir_fecha_ingles($valor[3]) . '|' . $valor[4] . '|' . $valor[5]);
         $orderby = "ORDER BY Concat(doc.sgo_vch_serie,'-',doc.sgo_vch_numero) asc";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " GROUP BY doc.sgo_int_documentoporcobrar " . $orderby),"","","","",null,array(),array(),array(),20,"");
      }

/********************************************************CUENTAS POR PAGAR**************************************************************************/
      function Filtros_Listar_CuentasxPagar($opc){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Persona" => $Helper->combo_persona("fil_cmbBusquedaproveedor",$index,"",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Tipo Comprobante" => $Helper->combo_tipocomprobante("fil_cmbBusquedatipocomprobante",$index,2,"",""),
            "Nro. Comprobante" => $Helper->textbox("fil_txtBusquedaNroComprobante",++$index,"",11,100,$TipoTxt->texto,"","","","")
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('finanzas_reportes','Grilla_Listar_CuentasxPagar','tbl_listarcomprobante','" . $opc . "','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarcomprobante",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_CuentasxPagar($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT 	doc.sgo_int_documentoporpagar as param, 
         				tfp.sgo_vch_descripcion as 'Forma Pago',
         				DATE_FORMAT(doc.sgo_dat_fecharegistro, '%d/%m/%Y') as 'Fecha Registro',
         				DATE_FORMAT(co.sgo_dat_fechapagoproyectado, '%d/%m/%Y') as 'Fecha Pago',
         				per.sgo_vch_nombre as 'Persona',
         				tco.sgo_vch_descripcion as 'Tipo Comprobante',
         				doc.sgo_vch_nrocomprobante as 'Nro. Comprobante',
         				mon.sgo_vch_simbolo as Moneda,
         				doc.sgo_dec_total as 'Total',
         				doc.sgo_dec_saldo as 'Saldo', 
         				doc.sgo_vch_observacion as 'Observación'
          		FROM 	tbl_sgo_documentoporpagar doc
          		INNER	JOIN tbl_sgo_comprobantecompra co
          		ON		doc.sgo_int_comprobantecompra = co.sgo_int_comprobantecompra
          		LEFT 	JOIN tbl_sgo_persona per 
          		ON 		doc.sgo_int_persona=per.sgo_int_persona
          		INNER 	JOIN tbl_sgo_moneda mon 
          		ON 		mon.sgo_int_moneda=doc.sgo_int_moneda
          		INNER 	JOIN tbl_sgo_tipocomprobante tco 
          		ON 		tco.sgo_int_tipocomprobante=doc.sgo_int_tipocomprobante 
          		INNER	JOIN tbl_sgo_tipoformapago tfp
          		ON		tfp.sgo_int_tipoformapago = co.sgo_int_tipoformapago";
         $where = $Obj->sql_where("WHERE per.sgo_int_persona=@p1 and (doc.sgo_dat_fecharegistro BETWEEN '@p2 00:00' and '@p3 23:59') and doc.sgo_int_tipocomprobante=@p4 and sgo_vch_nrocomprobante like '%@p5%' and doc.sgo_dec_saldo!=0",
                 $valor[1] . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $Helper->convertir_fecha_ingles($valor[3]) . '|' . $valor[4] . '|' . $valor[5]);
         $orderby = "ORDER BY doc.sgo_dat_fecharegistro ASC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"","","","",null,array(),array(),array(),20,"");
      }

/********************************************************CUENTAS POR PAGAR**************************************************************************/
      function Filtros_Listar_EstadoCuenta($opc){
         $Helper=new htmlhelper; $TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            //"Caja" => $Helper->combo_caja("fil_cmbBusquedacaja",$index,"",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"","")
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('finanzas_reportes','Grilla_Listar_EstadoCuenta','tbl_listarcomprobante','" . $opc . "','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listarcomprobante",$inputs,$buttons,2,990,"","");
      }
      function Grilla_Listar_EstadoCuenta($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoGrafico=new TipoGrafico();$valor=explode('|',$prm);$html="";
         $loghelper=new loghelper;
         $sql ="SELECT 0 as param,
						case 	Tipo when 1 then 'Ingresos' else 'Egresos' end as Tipo,
						Monto,Periodo
				FROM (
				select	1 as Tipo,
							sum(sgo_dec_saldo) as Monto,
							tbl_sgo_documentoporcobrar.sgo_dat_fecharegistro as Fecha,
							case month(tbl_sgo_documentoporcobrar.sgo_dat_fecharegistro)
								when 1 then 'ENERO'
								when 2 then 'FEBRERO'
								when 3 then 'MARZO'
								when 4 then 'ABRIL'
								when 5 then 'MAYO'
								when 6 then 'JUNIO'
								when 7 then 'JULIO'
								when 8 then 'AGOSTO'
								when 9 then 'SEPTIEMBRE'
								when 10 then 'OCTUBRE'
								when 11 then 'NOVIEMBRE'
								when 12 then 'DICIEMBRE'
							end as Periodo,
							tbl_sgo_documentoporcobrardetalle.sgo_int_caja as ID_Caja
				from		tbl_sgo_documentoporcobrar 
				inner	join tbl_sgo_documentoporcobrardetalle
				on			tbl_sgo_documentoporcobrar.sgo_int_documentoporcobrar = tbl_sgo_documentoporcobrardetalle.sgo_int_documentoporcobrar 
				where		sgo_dec_saldo >0 
				group		by Periodo,ID_Caja
				union
				select	2 as Tipo,
							sum(sgo_dec_saldo) as Monto,tbl_sgo_documentoporpagar.sgo_dat_fecharegistro as Fecha,
							case month(tbl_sgo_documentoporpagar.sgo_dat_fecharegistro)
								when 1 then 'ENERO'
								when 2 then 'FEBRERO'
								when 3 then 'MARZO'
								when 4 then 'ABRIL'
								when 5 then 'MAYO'
								when 6 then 'JUNIO'
								when 7 then 'JULIO'
								when 8 then 'AGOSTO'
								when 9 then 'SEPTIEMBRE'
								when 10 then 'OCTUBRE'
								when 11 then 'NOVIEMBRE'
								when 12 then 'DICIEMBRE' 
							end as Periodo,
							tbl_sgo_documentoporpagardetalle.sgo_int_caja as ID_Caja
				from		tbl_sgo_documentoporpagar
				inner	join tbl_sgo_documentoporpagardetalle
				on			tbl_sgo_documentoporpagar.sgo_int_documentoporpagar = tbl_sgo_documentoporpagardetalle.sgo_int_documentoporpagar
				where		sgo_dec_saldo >0 			
				group		by Periodo,ID_Caja
				) as tabla ";
         //$sql .= $Obj->sql_where("WHERE ID_Caja=@p1 and (Fecha BETWEEN '@p2 00:00' and '@p3 23:59')",
         $sql .= $Obj->sql_where("WHERE (Fecha BETWEEN '@p1 00:00' and '@p2 23:59')",
                 $Helper->convertir_fecha_ingles($valor[1]) . '|' . $Helper->convertir_fecha_ingles($valor[2]));
         $sql .=" GROUP BY Tipo,Periodo";
         $sql .= " ORDER BY Tipo ASC";
         $loghelper->log($sql);
         $result=$Obj->consulta($sql);
         if(mysqli_num_rows($result)>0){
           $categorias=array();$acumulado=array();$ingresos=array();$egresos=array();$series=array();$periodo="";$monto_acumulado=0;
           while ($row = mysqli_fetch_array($result))
           {
               if(array_search($row["Periodo"],$categorias)===false){
                  $categorias[]=$row["Periodo"];
               }
               if($periodo=="" || $periodo!=$row["Periodo"])
               {
                  if($periodo!="")$acumulado[]=$monto_acumulado;
                  $periodo=$row["Periodo"];
               }
               if($row["Tipo"]=="Ingresos"){$ingresos[]=$row["Monto"];$monto_acumulado +=floatval($row["Monto"]);}
               else{ $egresos[]=$row["Monto"];$monto_acumulado -=floatval($row["Monto"]);}
  //             $acumulado[]=$row["Periodo"];
           }
           $acumulado[]=$monto_acumulado;
           include_once('../../code/lib/graphichelper.php'); $Obj_Graf=new graphichelper();
  //         $categorias="'Enero','Febrero','Marzo'";
           $series=array(
                  "Ingresos"=>implode(",",$ingresos),
                  "Egresos"=>implode(",",$egresos),
                  "Acumulado"=>implode(",",$acumulado));
           $html .=$Obj_Graf->Crear_Grafico("contenedor",$TipoGrafico->lineal,"Estado de Cuenta por Periodo","","","",$categorias,"Nuevos Soles (S/.)","S/.",$series);
           $html .="<br/>";
         }
         $html .= $Helper->Imprimir_Grilla($Obj->consulta($sql),"","","","",null,array(),array(),array(),20,"");
         return $html;
      }
  }
?>