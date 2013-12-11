<?php 
	include_once('../../code/lib/htmlhelper.php');
	include_once('../../code/lib/loghelper.php');
	include_once('../../code/config/app.inc');
  Class bl_finanzas
  {
/********************************************************MOVIMIENTOS**************************************************************************/
      function Filtros_Listar_Movimientos(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Nro. Documento" => $Helper->textbox("fil_txtBusquedaNroMovimiento",++$index,"",25,100,$TipoTxt->texto,"","","","")
         );
         $buttons=array($Helper->button("btnBuscarMovimientos","Buscar",70,"Buscar_Grilla('finanzas','Grilla_Listar_Movimientos','tbl_listarmovimientos','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarmovimientos",$inputs,$buttons,2,550,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Movimientos($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT	distinct mov.sgo_int_movimiento as 'param',mov.sgo_dat_fecharegistro as 'Registro', 
			concat(mov.sgo_vch_serie,'-',mov.sgo_vch_numero) as 'Nro. Movimiento', 
			concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) as 'Entidad',
			mov.sgo_dec_total as 'Total',
			(
								select	group_concat(concat(sgo_vch_serie,'-',sgo_vch_numero))
								from		tbl_sgo_documentoporcobrar
								where		sgo_int_documentoporcobrar in
								(
									select	sgo_int_documentoporcobraropagar
									from		tbl_sgo_movimientodetalle
									where		sgo_int_movimiento = mov.sgo_int_movimiento
									and		sgo_int_tipooperacion = 1
								)
							)		as 'Documento por Cobrar',
			(
								select	group_concat(sgo_vch_nrocomprobante)
								from		tbl_sgo_documentoporpagar
								where		sgo_int_documentoporpagar in
								(
									select	sgo_int_documentoporcobraropagar
									from		tbl_sgo_movimientodetalle
									where		sgo_int_movimiento = mov.sgo_int_movimiento
									and		sgo_int_tipooperacion = 2
								)
							)		as 'Documento por Pagar'
FROM 		tbl_sgo_movimiento mov
INNER		JOIN tbl_sgo_movimientodetalle movdet
on			mov.sgo_int_movimiento = movdet.sgo_int_movimiento
INNER		JOIN tbl_sgo_persona per
on			movdet.sgo_int_persona = per.sgo_int_persona
WHERE 	mov.sgo_bit_activo=1";
         if($valor[0]!="")
         {
         	$sql.=" and mov.sgo_dat_fecha>=".$Helper->convertir_fecha_ingles($valor[0]);
      	 }
         if($valor[1]!="")
         {
         	$sql.=" and mov.sgo_dat_fecha<=".$Helper->convertir_fecha_ingles($valor[1]);
      	 }         	
         /*if($valor[2]!="")
         {
         	$sql.=" and '".$valor[2]."' in 
         	(select 	concat(sgo_vch_serie,'-',sgo_vch_numero) 
			from		tbl_sgo_documentoporcobrar
			where		sgo_int_documentoporcobrar = (select 	sgo_int_documentoporcobraropagar 
																from 		tbl_sgo_movimientodetalle
																where		sgo_int_movimiento = mov.sgo_int_movimiento)
			UNION
			select 	concat(sgo_vch_serie,'-',sgo_vch_numero) 
			from		tbl_sgo_documentoporpagar
			where		sgo_int_documentoporpagar = (select 	sgo_int_documentoporcobraropagar 
																from 		tbl_sgo_movimientodetalle
																where		sgo_int_movimiento = mov.sgo_int_movimiento))";
      	 }*/
         $orderby = "group		by param ORDER BY mov.sgo_dat_fecharegistro DESC,3 ASC ";
         $log=new loghelper;
         $log->log($sql . " " . $orderby);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $orderby),"PopUp('finanzas','PopUp_Mant_Movimiento','','","","PopUp('finanzas','PopUp_Mant_Movimiento','','","PopUp('finanzas','Confirma_Eliminar_Movimiento','','",null,array(),array(),array(),20,"");
      }
      function Grilla_Listar_Movimiento_Items($prm,$size=1150)
      {
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $where = $Obj->sql_where("WHERE mde.sgo_int_movimiento=@p1",$prm);
         $sql ="SELECT 	mde.sgo_int_movimiento as param,         				
         				DATE_FORMAT(doc.sgo_dat_fecharegistro, '%d/%m/%Y') as 'Registro',
         				tco.sgo_vch_descripcion as 'Tipo',         				
         				Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero,' (',mon.sgo_vch_simbolo,')') as 'Comprobante',         				
         				doc.sgo_dec_total as 'Total',
         				case mde.sgo_int_tipooperacion when 1 then 'ENTRADA' else 'SALIDA' end as 'E/S',
         				caj.sgo_vch_descripcion as 'Caja',
         				mde.sgo_dec_monto as 'Monto Movimiento',
         				dpd.sgo_vch_documento as 'Documento',
         				tom.sgo_vch_descripcion as 'Modo',         				
         				mde.sgo_dat_fechatransaccion as 'Fecha'
                FROM 	tbl_sgo_movimientodetalle mde
                INNER	JOIN tbl_sgo_caja caj ON mde.sgo_int_caja = caj.sgo_int_caja
                INNER 	JOIN tbl_sgo_documentoporcobrar doc ON doc.sgo_int_documentoporcobrar=mde.sgo_int_documentoporcobraropagar
                INNER 	JOIN tbl_sgo_documentoporcobrardetalle dpd ON dpd.sgo_int_documentoporcobrar=doc.sgo_int_documentoporcobrar and dpd.sgo_int_movimiento=mde.sgo_int_movimiento
                INNER 	JOIN tbl_sgo_comprobanteventa co ON co.sgo_int_comprobanteventa=doc.sgo_int_comprobanteventa and mde.sgo_int_tipocomprobante in (1,2)
                INNER 	JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=co.sgo_int_tipocomprobante
                INNER 	JOIN tbl_sgo_tipooperacionmovimiento tom ON tom.sgo_int_tipooperacionmovimiento=dpd.sgo_int_modo 
                INNER	JOIN tbl_sgo_moneda mon on doc.sgo_int_moneda = mon.sgo_int_moneda " . $where . "                
                UNION
                SELECT 	mde.sgo_int_movimiento as param,                		
                		DATE_FORMAT(dop.sgo_dat_fecharegistro, '%d/%m/%Y') as 'Registro',	
                		tco.sgo_vch_descripcion as 'Tipo',                		
                		concat(co.sgo_vch_serie,'-',co.sgo_vch_numero,' (',mon.sgo_vch_simbolo,')') as 'Comprobante',                		                		
                		dop.sgo_dec_total as 'Total',
                		case mde.sgo_int_tipooperacion when 1 then 'ENTRADA' else 'SALIDA' end as 'E/S',
                		caj.sgo_vch_descripcion as 'Caja',
                		mde.sgo_dec_monto as 'Monto Movimiento',
                		dpd.sgo_vch_documento as 'Documento',
                		tom.sgo_vch_descripcion as 'Modo',
                		mde.sgo_dat_fechatransaccion as 'Fecha'
                FROM 	tbl_sgo_movimientodetalle mde
                INNER	JOIN tbl_sgo_caja caj ON mde.sgo_int_caja = caj.sgo_int_caja
                INNER 	JOIN tbl_sgo_documentoporpagar dop ON dop.sgo_int_documentoporpagar=mde.sgo_int_documentoporcobraropagar
                INNER 	JOIN tbl_sgo_documentoporpagardetalle dpd ON dpd.sgo_int_documentoporpagar=dop.sgo_int_documentoporpagar and dpd.sgo_int_movimiento=mde.sgo_int_movimiento
                INNER 	JOIN tbl_sgo_comprobantecompra co ON co.sgo_int_comprobantecompra=dop.sgo_int_comprobantecompra and mde.sgo_int_tipocomprobante in (3,4)
                INNER 	JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=co.sgo_int_tipocomprobante
                INNER 	JOIN tbl_sgo_tipooperacionmovimiento tom ON tom.sgo_int_tipooperacionmovimiento=dpd.sgo_int_modo 
                INNER	JOIN tbl_sgo_moneda mon on dop.sgo_int_moneda = mon.sgo_int_moneda " . $where . " 
                UNION
                SELECT 	mde.sgo_int_movimiento as param,
                		DATE_FORMAT(dop.sgo_dat_fecharegistro, '%d/%m/%Y') as 'Registro',
                		tco.sgo_vch_descripcion as 'Tipo',                		
                		concat(co.sgo_vch_serie,'-',co.sgo_vch_numero,' (',mon.sgo_vch_simbolo,')') as 'Comprobante',                		                		
                		dop.sgo_dec_total as 'Total',
                		case mde.sgo_int_tipooperacion when 1 then 'ENTRADA' else 'SALIDA' end as 'E/S',
                		caj.sgo_vch_descripcion as 'Caja',
                		mde.sgo_dec_monto as 'Monto Movimiento',
                		dpd.sgo_vch_documento as 'Documento',
                		tom.sgo_vch_descripcion as 'Modo',
                		mde.sgo_dat_fechatransaccion as 'Fecha'
                FROM 	tbl_sgo_movimientodetalle mde
                INNER	JOIN tbl_sgo_caja caj ON mde.sgo_int_caja = caj.sgo_int_caja
                INNER 	JOIN tbl_sgo_documentoporpagar dop ON dop.sgo_int_documentoporpagar=mde.sgo_int_documentoporcobraropagar
                INNER 	JOIN tbl_sgo_documentoporpagardetalle dpd ON dpd.sgo_int_documentoporpagar=dop.sgo_int_documentoporpagar and dpd.sgo_int_movimiento=mde.sgo_int_movimiento
                INNER 	JOIN tbl_sgo_comprobanteventa co ON co.sgo_int_comprobanteventa=dop.sgo_int_comprobantecompra AND mde.sgo_int_tipocomprobante = 9  
                INNER 	JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=co.sgo_int_tipocomprobante
                INNER 	JOIN tbl_sgo_tipooperacionmovimiento tom ON tom.sgo_int_tipooperacionmovimiento=dpd.sgo_int_modo 
                INNER	JOIN tbl_sgo_moneda mon on dop.sgo_int_moneda = mon.sgo_int_moneda " . $where;
         $botones=new GrillaBotones;
         $botones->GrillaBotones(($prm==0?"PopUp('finanzas','PopUp_Mant_Documentos','tbl_mant_co','":""),"","","","");
         return $Helper->Crear_Grilla($Obj->consulta($sql),"tbl_mov_items",$botones,$size,array(),array(),array(),20,"");
      }
      function Grilla_Listar_Movimiento_Items_Documentos($prm,$size=1000){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         //$cliente = (strlen($valor[3])<=5?$valor[3]:$valor[4]);
         $where = $Obj->sql_where("and doc.sgo_int_persona=@p1",$valor[3]);
         $sql ="SELECT 	distinct Concat(doc.sgo_int_documentoporcobrar,'~',1) as param,
         				'POR COBRAR' as Tipo,
         				per.sgo_vch_nombre as 'Persona', 
         				tco.sgo_vch_descripcion as 'Tipo Comprobante',
         				DATE_FORMAT(doc.sgo_dat_fecharegistro, '%d/%m/%Y') as 'Fecha Registro',
         				Concat(doc.sgo_vch_serie,'-',doc.sgo_vch_numero) as 'Comprobante', 
         				mon.sgo_vch_simbolo as 'Moneda',
         				doc.sgo_dec_total as 'Total', 
         				doc.sgo_dec_saldo as 'Pendiente',
         				doc.sgo_vch_observacion as 'Observacion'
                FROM 	tbl_sgo_documentoporcobrar doc
                INNER 	JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=doc.sgo_int_tipocomprobante
                INNER 	JOIN tbl_sgo_persona per ON per.sgo_int_persona=doc.sgo_int_persona
                INNER 	JOIN tbl_sgo_moneda mon on doc.sgo_int_moneda = mon.sgo_int_moneda
                WHERE doc.sgo_dec_saldo!=0 ". $where . "
                UNION
                SELECT 	Concat(doc.sgo_int_documentoporpagar,'~',2) as param,
                		'POR PAGAR' as Tipo,
                		per.sgo_vch_nombre as 'Persona', 
                		tco.sgo_vch_descripcion as 'Tipo Comprobante',
                		DATE_FORMAT(doc.sgo_dat_fecharegistro, '%d/%m/%Y') as 'Fecha Registro',
                		doc.sgo_vch_nrocomprobante as 'Comprobante',
                		mon.sgo_vch_simbolo as 'Moneda', 
                		doc.sgo_dec_total as 'Total', 
                		doc.sgo_dec_saldo as 'Pendiente',
                		doc.sgo_vch_observacion as 'Observacion'
                FROM 	tbl_sgo_documentoporpagar doc               
                INNER 	JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=doc.sgo_int_tipocomprobante
                INNER 	JOIN tbl_sgo_persona per ON per.sgo_int_persona=doc.sgo_int_persona
                INNER 	JOIN tbl_sgo_moneda mon on doc.sgo_int_moneda = mon.sgo_int_moneda
                WHERE 	doc.sgo_dec_saldo!=0 ". $where;
         $botones=new GrillaBotones;
         $botones->GrillaBotones("","","","","1");
         $log = new loghelper();
         $log->log($sql); 
         $html= $Helper->Crear_Grilla($Obj->consulta($sql),"tbl_mov_docs",$botones,$size, array(), array(), array(),10,"") . "<script>Carga_Js('../../js/jscript_finanzas.js')</script>";
         return $Helper->PopUp("","Documentos por cobrar o pagar",1050,$html,$Helper->button("","Aceptar",70,"Agregar_Documentos('tbl_mov_docs','tbl_mov_items');Cerrar_PopUp('PopUp@')","textoInput"));
      }
      function PopUp_Mant_Movimiento($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox; $index=1;$TipoDate=new TipoTextDate;
         $caja="";$serie="";$numero="";$tipo="";$persona="";$obs="";$total="0";$fechapago="";
         $result = $Obj->consulta("SELECT 	sgo_int_tipocomprobante,
         									DATE_FORMAT(sgo_dat_fechapago, '%d/%m/%Y') as sgo_dat_fechapago, 
         									sgo_vch_serie,
         									sgo_vch_numero,
         									sgo_int_caja,
         									ifnull(sgo_int_persona,0) as sgo_int_persona,
         									sgo_vch_observacion,
         									sgo_dec_total 
         							FROM 	tbl_sgo_movimiento 
         							WHERE 	sgo_int_movimiento = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $tipo=$row["sgo_int_tipocomprobante"];$serie=$row["sgo_vch_serie"];$numero=$row["sgo_vch_numero"];$caja=$row["sgo_int_caja"];
            $persona=$row["sgo_int_persona"];$obs=$row["sgo_vch_observacion"];$total=$row["sgo_dec_total"];$fechapago=$row["sgo_dat_fechapago"];
            break;
         }
         $grilla="<div id='div_tbl_mov' style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_Movimiento_Items($prm) . "</div>";
         $disabled=($prm=="0"?false:true);
         $Val_Tipo=new InputValidacion();
         $Val_Tipo->InputValidacion('DocValue("fil_cmbTipoDocumento")!=""','Debe especificar el tipo de documento');
         $Val_Persona=new InputValidacion();
         $Val_Persona->InputValidacion('DocValue("fil_cmbpersona")!=""','Debe especificar la persona');
         $Val_Caja=new InputValidacion();
         $Val_Caja->InputValidacion('DocValue("fil_cmbcaja")!=""','Debe especificar la caja');
         $Val_Serie=new InputValidacion();
         $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
         $Val_Numero=new InputValidacion();
         $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
         $Val_Fecha=new InputValidacion();
         $Val_Fecha->InputValidacion('DocValue("fil_txtFechaPago")!=""','Debe especificar la fecha de pago');
         $inputs=array(
            //"Tipo Documento" => $Helper->combo_tipocomprobante("fil_cmbTipoDocumento",++$index,"12",$tipo,"Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_Tipo,$disabled),
            //"Caja" => $Helper->combo_caja("fil_cmbcaja",++$index,$caja,"",$Val_Caja,$disabled),
            "Movimiento" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,40,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,6,70,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
            "Tipo Persona" =>$Helper->combo_tipopersona("cmbtipopersona",++$index,"","Cargar_Combo('general','Combo_PersonaporTipo_reload','fil_cmbpersona','cmbtipopersona','fil_cmbpersona')",null,$disabled),
            "Persona" => $Helper->combo_persona("fil_cmbpersona",++$index,$persona,"",$Val_Persona,$disabled),
            //"Fecha Pago" => $Helper->textdate("fil_txtFechaPago",++$index,$fechapago,false,$TipoDate->fecha,80,"","",$Val_Fecha),
            "Items"=>$grilla . "~5",
            "Observaciones" => $Helper->textarea("fil_txtObservaciones",++$index,$obs,190,3,"","","","",null,$disabled) . '~5',
            ""=>"<div class='right'>Total Operaci&oacute;n&nbsp;" . $Helper->textbox("txt_total",0,$total,0,100,$TipoTxt->decimal,"","","","",null,true) . '&nbsp;</div>~5',
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_co",$inputs,$buttons,3,1250,"","");
         return $Helper->PopUp("",($prm==0?"Nuevo":"Ver") . " Movimiento",1250,$html,(!$disabled?$Helper->button("","Grabar",70,"Operacion('finanzas','Mant_Movimiento','tbl_mant_co','" . $prm . "')","textoInput"):"")) . "<script>Cargar_Objeto('general','Numeracion_Comprobante','',17,'fil_txtNroSerie|fil_txtNroNumero','custom');</script>";
      }
      function obtenerMonedaCaja($prm)
      {
      	 $moneda=0;
         $Obj=new mysqlhelper;
         $sql = "	select	sgo_int_moneda
					from	tbl_sgo_caja
					where 	sgo_int_caja =".$prm;

		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {
            $moneda=$row["sgo_int_moneda"];
            break;
         }
		 return $moneda;
      }  	  
      function obtenerMonedaDocumento($documento, $tipoOperacion)
      {
      	 $Obj=new mysqlhelper;
      	 $moneda=0;
      	 if($tipoOperacion==1) // documento por cobrar
      	 {
      	 	$sql = "select	sgo_int_moneda
					from	tbl_sgo_documentoporcobrar
					where 	sgo_int_documentoporcobrar =".$documento;
      	 }
      	 else //documento por pagar
      	 {
      	 	$sql = "select	sgo_int_moneda
					from	tbl_sgo_documentoporpagar
					where 	sgo_int_documentoporpagar =".$documento;
      	 }                  
		 $result = $Obj->consulta($sql);
		 while ($row = mysqli_fetch_array($result))
		 {
            $moneda=$row["sgo_int_moneda"];
            break;
         }
		 return $moneda;
      }
      /***************
       * Método que obtiene el tipo de comprobante (Factura, Boleta, Nota de Crédito, etc) y la persona a la que se 
       * le adjudica dicho documento
       * *********/
      function Obtener_Tipo_Comprobante_Persona($documento, $tipo)
      {
      	 $Obj=new mysqlhelper;
      	 $datos;
      	 if($tipo==1) // documento por cobrar
      	 {
      	 	$sql = "select 		sgo_int_tipocomprobante as tipo, sgo_int_cliente as persona
					from		tbl_sgo_comprobanteventa 
					where		sgo_int_comprobanteventa in 
					(
						select 		sgo_int_comprobanteventa
						from		tbl_sgo_documentoporcobrar
						where		sgo_int_documentoporcobrar =" .$documento."
					)";
      	 }
      	 else //documento por pagar
      	 {
      	 	$presql="	select 	   sgo_int_tipocomprobante
						from		tbl_sgo_documentoporpagar
						where		sgo_int_documentoporpagar = ".$documento;
      	 	
	      	 $result = $Obj->consulta($presql);
	      	 
			 while ($row = mysqli_fetch_array($result))
			 {
	            $tipocomprobante=$row["sgo_int_tipocomprobante"];	            
	            break;
	         }	
	         if($tipocomprobante==TIPO_COMPROBANTE_VENTA_NOTACREDITO)
	         {
	         	$sql = "select 	sgo_int_tipocomprobante as tipo, sgo_int_cliente as persona
						from		tbl_sgo_comprobanteventa
						where		sgo_int_comprobanteventa in 
						(
							select 	sgo_int_comprobantecompra
							from		tbl_sgo_documentoporpagar
							where		sgo_int_documentoporpagar = ".$documento."
						)";
	         }
	         else 
	         {
	         	$sql = "select 	sgo_int_tipocomprobante as tipo, sgo_int_proveedor as persona
						from		tbl_sgo_comprobantecompra
						where		sgo_int_comprobantecompra in 
						(
							select 	sgo_int_comprobantecompra
							from		tbl_sgo_documentoporpagar
							where		sgo_int_documentoporpagar = ".$documento."
						)";	
	         }      	 	
      	 }                  
		 $result = $Obj->consulta($sql);
		 while ($row = mysqli_fetch_array($result))
		 {
            $datos[0]=$row["tipo"];
            $datos[1]=$row["persona"];
            break;
         }
		 return $datos;
      }
      function Mant_Movimiento($prm)
      {    	 
      	 include_once('../../code/bl/bl_general.php');
    	 include_once('../../code/bl/bl_facturacion.php'); 
    	 
    	 $Obj_general=new bl_general; 
    	 $Obj_facturacion = new bl_facturacion;    	 
         $Obj=new mysqlhelper;
         $Helper= new htmlhelper;
         $valor=explode('|',$prm);         
         $trans=$Obj->transaction();
         $totalCaja=0;               
         $tipoCambio = $Obj_facturacion->obtenerTipoCambio(date("Y-m-d H:i"), MONEDA_DOLARES);           
         //1RO: SE AGREGA EL MOVIMIENTO SIN TOTAL (aún no se realizan los cálculos)
         $sql="	INSERT INTO tbl_sgo_movimiento 
         		(
         			sgo_vch_serie,
         			sgo_vch_numero,         			
         			sgo_vch_observacion,
         			sgo_dat_fecharegistro,         			
         			sgo_dec_total,
         			sgo_bit_activo
         		)
         		VALUES 
         		('" 
         			. $valor[1] . "','" 
         			. $valor[2] . "','" 
         			. $valor[4] . "','"
         			. date("Y-m-d H:i") . "',         		
         			0,
         			1
         		)";         
         try{
              if($trans->query($sql))
              {				  				  
                  $id=mysqli_insert_id($trans);              	  
				  if($Obj_general->Generar_Numeracion_Comprobante(TIPO_MOVIMIENTO_ENTRADA,$trans)==-1){throw new Exception("Error: Generar_Numeracion_Comprobante");}
                  $valor_item=explode('_',$valor[5]);
                  $valortotal=0;
                  foreach ($valor_item as $k) 
                  {
                     $valor_col=explode('~',$k);
                     $monedaCaja = $this->obtenerMonedaCaja($valor_col[3]);
                     $monedaDocumento = $this->obtenerMonedaDocumento($valor_col[0],$valor_col[1]);
                     $monto=0;
                     if($monedaCaja==MONEDA_SOLES && $monedaDocumento==MONEDA_SOLES)
                     {
                     	$monto=floatval($valor_col[4]);
                     }
                     else
                     {
                     	if($monedaCaja==MONEDA_DOLARES && $monedaDocumento==MONEDA_DOLARES)
                     	{
                     		$monto=floatval($valor_col[4]);	
                     	}
                     	else
                     	{
                     		if($monedaCaja==MONEDA_SOLES && $monedaDocumento==MONEDA_DOLARES)
                     		{
                     			$monto=floatval(($valor_col[4]*$tipoCambio[1]));	
                     		}
                     		else
                     		{
                     			if($monedaCaja==MONEDA_DOLARES && $monedaDocumento==MONEDA_SOLES)
                     			{
                     				$monto=floatval(($valor_col[4]/$tipoCambio[0]));	
                     			}	
                     		}
                     	}
                     }
                     $datos = $this->Obtener_Tipo_Comprobante_Persona($valor_col[0],$valor_col[1]);                     
                     $sql="	INSERT INTO tbl_sgo_movimientodetalle 
                     		(
                     			sgo_int_movimiento, 
                     			sgo_int_documentoporcobraropagar, 
                     			sgo_int_tipooperacion,
                     			sgo_dec_monto,                     			
                     			sgo_int_tipocomprobante,
                     			sgo_int_persona,
                     			sgo_int_caja,
                     			sgo_dat_fechatransaccion,
                     			sgo_vch_nrodocumento,
                     			sgo_int_tipooperacionmovimiento
                     		)
                     		VALUES 
                     		(" 
                     			. $id . "," 
                     			. $valor_col[0] . "," 
                     			. $valor_col[1] . "," 
                     			. $monto . "," 
                     			. $datos[0] . "," 
                     			. $datos[1] . ","
                     			. $valor_col[3] . ",'"
                     			. $Helper->convertir_fecha_ingles($valor_col[7]) . "','"
                     			. $valor_col[5] . "',"
                     			. $valor_col[6] . "
                     		)";
                     if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                     //Por Cobrar
                     if($valor_col[1]=="1")
                     {
                         $sql="	INSERT INTO tbl_sgo_documentoporcobrardetalle 
                         		(
                         			sgo_int_documentoporcobrar, 
                         			sgo_int_caja, 
                         			sgo_dec_monto,
                         			sgo_vch_documento,
                         			sgo_int_modo,
                         			sgo_vch_observacion,
                         			sgo_int_movimiento,
                         			sgo_dat_fecharegistro,
                         			sgo_bit_activo
                         		) 
                         		VALUES 
                         		(" 
                         			. $valor_col[0] . "," 
                         			. $valor_col[3] . "," 
                         			. $valor_col[4] . ",'" 
                         			. $valor_col[5] . "'," 
                         			. $valor_col[6] . ",'" 
                         			. "" . "'," 
                         			. $id . ",'" 
                         			. date("Y-m-d H:i") 
                         			. "',
                         			1
                         		)";
                         $valortotal +=floatval($valor_col[4]);
                         $totalCaja  += ($valor_col[2]==1?$monto:($monto*-1));
                         
                         if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                         
                         $sql="	UPDATE 	tbl_sgo_documentoporcobrar 
                         		SET 	sgo_dec_saldo = (sgo_dec_saldo - " . floatval($valor_col[4]) . ") 
                         		WHERE 	sgo_int_documentoporcobrar=" . $valor_col[0];
                         if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                         
                          $sql="UPDATE 	tbl_sgo_comprobanteventa 
                         		SET 	sgo_int_estadocomprobante = case 
                         		when (sgo_dec_total-".($valor_col[4]).")=0 then ".ESTADO_COMPROBANTE_CANCELADA." 
                         		else ".ESTADO_COMPROBANTE_PAGO_PARCIAL." end 
                         		where 	sgo_int_comprobanteventa = 
                         				(
                         					select 	distinct sgo_int_comprobanteventa
                         					from 	tbl_sgo_documentoporcobrar 
                         					where 	sgo_int_documentoporcobrar =".$valor_col[0]."                         					
                         				)";                         	
                         
                         if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                         
                         if($this->Mant_EntradaCaja($valor_col[3],($valor_col[2]==1?$monto:($monto*-1)),$trans)==-1) throw new Exception("Error en Mant_EntradaCaja");
                     }
                     else //Por pagar
                     {
                         $sql="	INSERT INTO tbl_sgo_documentoporpagardetalle 
                         		(
                         			sgo_int_documentoporpagar, 
                         			sgo_int_caja, 
                         			sgo_dec_monto,
                         			sgo_vch_documento,
                         			sgo_int_modo,
                         			sgo_vch_observacion,
                         			sgo_int_movimiento,
                         			sgo_dat_fecharegistro,
                         			sgo_bit_activo
                         		) 
                         		VALUES 
                         		(" 
                         			. $valor_col[0] . "," 
                         			. $valor_col[3] . "," 
                         			. $valor_col[4] . ",'" 
                         			. $valor_col[5] . "'," 
                         			. $valor_col[6] . ",'" 
                         			. "" . "'," 
                         			. $id . ",'" 
                         			. date("Y-m-d H:i") . "',
                         			1
                         		)";
                         $valortotal -=floatval($valor_col[4]);
                         //$totalCaja  -= $monto;
                         $totalCaja  += ($valor_col[2]==1?$monto:($monto*-1));
                         
                         if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                         
                         $sql="	UPDATE 	tbl_sgo_documentoporpagar 
                         		SET 	sgo_dec_saldo=(sgo_dec_saldo - " . floatval($valor_col[4]) . ") 
                         		WHERE 	sgo_int_documentoporpagar=" . $valor_col[0];
                         
                         if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                         
                         $sql="	UPDATE 	tbl_sgo_comprobantecompra 
                         		SET 	sgo_int_estadocomprobante = case 
                         		when (sgo_dec_total-".($valor_col[4]*-1).")=0 then ".ESTADO_COMPROBANTE_CANCELADA." 
                         		else ".ESTADO_COMPROBANTE_PAGO_PARCIAL." end 
                         		where 	sgo_int_comprobantecompra = 
                         				(
                         					select 	distinct sgo_int_comprobantecompra 
                         					from 	tbl_sgo_documentoporpagar 
                         					where 	sgo_int_documentoporpagar =".$valor_col[0]."
                         				)";                         	
                         
                         if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                         
                         if($this->Mant_EntradaCaja($valor_col[3],($valor_col[2]==1?$monto:($monto*-1)),$trans)==-1) throw new Exception("Error en Mant_EntradaCaja");
                     }
                  }
                  
                  $sql="	UPDATE 	tbl_sgo_movimiento 
                  			SET 	sgo_dec_total=" . $totalCaja . " 
                  			WHERE 	sgo_int_movimiento=" . $id;
                  if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);                                    
              }
              else throw new Exception($sql . " => " . $trans->error);
              $trans->commit();$trans->close();return 1;
         }
         catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
//             throw new Exception("insert name again",0,$e);
         }
      }
      function Confirma_Eliminar_Movimiento($prm){
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el movimiento ' . $this->Obtener_Numero_Movimiento($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('finanzas','Eliminar_Movimiento','','" . $prm . "')"));
      }
      function Eliminar_Movimiento($prm){
         $Obj=new mysqlhelper;$trans=$Obj->transaction();
         try{
            if($trans->query("UPDATE tbl_sgo_movimiento SET sgo_bit_activo=0 WHERE sgo_int_movimiento=" . $prm)){
               $sql="SELECT sgo_int_caja,sgo_dec_monto,sgo_int_tipooperacion, sgo_int_tipocomprobante, sgo_int_documentoporcobraropagar FROM tbl_sgo_movimientodetalle WHERE sgo_int_movimiento=" . $prm;
               $result=$Obj->consulta($sql);$tipo="0";
               while ($row = mysqli_fetch_array($result))
               { 
	               	$caja=$row["sgo_int_caja"];
	               	$monto=$row["sgo_dec_monto"]; 
	               	$tipo=$row["sgo_int_tipocomprobante"]; 
	               	$operacion=$row["sgo_int_tipooperacion"];
	               	$porcobraropagar=$row["sgo_int_documentoporcobraropagar"];
	               	
	               	if($operacion==1) //Si el documento es por cobrar
	               	{
	               		$sql="delete from tbl_sgo_documentoporcobrardetalle where sgo_int_documentoporcobrar = ".$porcobraropagar;
	               		if(!$trans->query($sql))throw new Exception("Error: ". $trans->error);
	               		$sql="update tbl_sgo_documentoporcobrar set sgo_dec_saldo = (sgo_dec_saldo + ".$monto.") where sgo_int_documentoporcobrar = ".$porcobraropagar;
	               		if(!$trans->query($sql))throw new Exception("Error: ". $trans->error);
	               		$sql="update tbl_sgo_caja set sgo_dec_total = (sgo_dec_total - ".$monto.") where sgo_int_caja = ".$caja;
	               		if(!$trans->query($sql))throw new Exception("Error: ". $trans->error);
	               	}
	               	else // Si el documento es por pagar
	               	{
	               		$sql="delete from tbl_sgo_documentoporpagardetalle where sgo_int_documentoporpagar = ".$porcobraropagar;
	               		if(!$trans->query($sql))throw new Exception("Error: ". $trans->error);
	               		$sql="update tbl_sgo_documentoporpagar set sgo_dec_saldo = (sgo_dec_saldo + ".$monto.") where sgo_int_documentoporpagar = ".$porcobraropagar;
	               		if(!$trans->query($sql))throw new Exception("Error: ". $trans->error);
	               		$sql="update tbl_sgo_caja set sgo_dec_total = (sgo_dec_total + ".$monto.") where sgo_int_caja = ".$caja;
	               		if(!$trans->query($sql))throw new Exception("Error: ". $trans->error);
	               	}
	               		               	
               }               
            }
            else throw new Exception($sql . " => ". $trans->error);
            $trans->commit();$trans->close(); return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarMovimientos');</script>";
         }
         catch(Exception $e){
             echo '<script>alert("Error: ' . $e . '");</script>';
             $trans->rollback();$trans->close(); return "<script>Operacion_Result(false);</script>";
         }
      }

/****************************************************************DOCUMENTOS POR COBRAR O PAGAR************************************************************************/
      function Filtros_Listar_Documentos(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $TipoDate=new TipoTextDate;$index=0;
         $inputs=array(
		 	"Cliente" => $Helper->textbox_predictivo("fil_txtCliente",++$index,"",128,300,"","","clientes"),
            "Nro. Documento" => $Helper->textbox("fil_cmbBusquedaDocumento",2,"",25,100,$TipoTxt->texto,"","","",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",3,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",4,"",false,$TipoDate->fecha,80,"",""),
            "Tipo Documento" => $Helper->combo_tipocomprobante("fil_cmbTipoComprobante",5,"1,2,3,4","","")
         );
         $buttons=array($Helper->button("btnBuscarDocumento","Buscar",70,"Buscar_Grilla('finanzas','Grilla_Listar_Documentos','tbl_listardocumentos','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listardocumentos",$inputs,$buttons,3,950,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Documentos($prm){
		 $valor = explode('|',$prm);
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
		  $sql ="select		distinct Concat(doc.sgo_int_documentoporcobrar,'|',1) as param, 
		  					DATE_FORMAT(comp.sgo_dat_fechaemision, '%d/%m/%Y') as 'Fecha Registro', 
		  					DATE_FORMAT(ADDDATE(doc.sgo_dat_fecharegistro,(select sgo_int_diascredito from tbl_sgo_datosfinancieroscliente where sgo_int_cliente=per.sgo_int_persona)), '%d/%m/%Y') as 'Fecha Vencimiento', 
		  					tipocomp.sgo_vch_descripcion as 'Tipo Comprobante', 
		  					concat(doc.sgo_vch_serie,'-',doc.sgo_vch_numero) as 'Nro. Comprobante', 
		  					concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) as 'Persona', 
		  					mon.sgo_vch_simbolo as 'Moneda', 
		  					doc.sgo_dec_total as 'Total', 
		  					doc.sgo_dec_saldo as 'Saldo', 
		  					doc.sgo_vch_observacion as 'Observación' 
		  		from 		tbl_sgo_documentoporcobrar doc 
		  		inner 		join tbl_sgo_persona per 
		  		on 			doc.sgo_int_persona = per.sgo_int_persona 
		  		inner 		join tbl_sgo_comprobanteventa comp 
		  		on 			comp.sgo_int_comprobanteventa = doc.sgo_int_comprobanteventa 
		  		inner 		join tbl_sgo_tipocomprobante tipocomp 
		  		on			doc.sgo_int_tipocomprobante = tipocomp.sgo_int_tipocomprobante 
		  		inner		join tbl_sgo_moneda mon 
		  		on			doc.sgo_int_moneda = mon.sgo_int_moneda  
		  		WHERE 		doc.sgo_dec_saldo!=0 
		  		and 		doc.sgo_bit_activo=1 ";
		  if($valor[0]!="")
		  {
			 $sql = $sql." and concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) like '%".$valor[0]."%'";  
		  }
		  if($valor[1]!="")
		  {
			 $sql = $sql." and Concat(doc.sgo_vch_serie,'-',doc.sgo_vch_numero) like '%".$valor[1]."%'";  
		  }

		  if($valor[2]!="" && $valor[3]!="")
		  {
		    $sql = $sql." and (DATE_FORMAT(doc.sgo_dat_fecharegistro, '%d/%m/%Y') BETWEEN '".$valor[2]." 00:00' and '".$valor[3]." 23:59')";
		  }
          if(count($valor)==5)
          {
    		  if($valor[4]!="")
    		  {
    		    $sql = $sql." and doc.sgo_int_tipocomprobante=".$valor[4];
    		  }
		  }
		  $sql = $sql." group by comp.sgo_int_comprobanteventa 
		  UNION
          SELECT 	Concat(dop.sgo_int_documentoporpagar,'|',2) as param, 
          			DATE_FORMAT(dop.sgo_dat_fecharegistro, '%d/%m/%Y') as 'Fecha Registro',
          			null, 
          			tco.sgo_vch_descripcion as 'Tipo Comprobante',
          			case 
          				when sgo_vch_numero <> '' then concat(sgo_vch_serie,'-',sgo_vch_numero) 
          				else sgo_vch_nrocomprobante 
          			end as 'Nro. Comprobante',
          			per.sgo_vch_nombre as 'Persona',
          			mon.sgo_vch_simbolo as Moneda,
          			dop.sgo_dec_total as 'Total',
          			dop.sgo_dec_saldo as 'Saldo', 
          			dop.sgo_vch_observacion as 'Observación'
          FROM 		tbl_sgo_documentoporpagar dop
          INNER 	JOIN tbl_sgo_moneda mon ON mon.sgo_int_moneda=dop.sgo_int_moneda
          INNER 	JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=dop.sgo_int_tipocomprobante
          LEFT 		JOIN tbl_sgo_persona per ON per.sgo_int_persona=dop.sgo_int_persona 
          WHERE 	dop.sgo_dec_saldo!=0  
          and 		dop.sgo_bit_activo=1 ";
		  if($valor[0]!="")
		  {
			 $sql = $sql." and per.sgo_vch_nombre like '%".$valor[0]."%'";  
		  }
		  if($valor[1]!="")
		  {
			 $sql = $sql." and Concat(dop.sgo_vch_serie,'-',dop.sgo_vch_numero) like '%".$valor[1]."%'";  
		  }

		  if($valor[2]!="" && $valor[3]!="")
		  {
		    $sql = $sql." and (DATE_FORMAT(dop.sgo_dat_fecharegistro, '%d/%m/%Y') BETWEEN '".$valor[2]." 00:00' and '".$valor[3]." 23:59')";
		  }
          if(count($valor)==5)
          {
		  if($valor[4]!="")
		  {
		    $sql = $sql." and dop.sgo_int_tipocomprobante=".$valor[4];
		  }
          }
		  $sql = $sql ." order by 5";
		  //$objlog = new loghelper;
		  //$objlog->log($sql);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('finanzas','PopUp_Mant_Documento','','","","","PopUp('finanzas','Confirma_Eliminar_Documento','','",null,array(),array(),array(),20,"");
      }
      function PopUp_Mant_Documento($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=10;$valor=explode('|',$prm);
         $caja="";$tipo="";$categoria="";$serie="";$numero="";$persona="";$monto="";$moneda="";$modo="";$obs="";$sql="";
         if($prm!="0"){
           if($valor[1]=="2") $sql="SELECT sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_int_persona,sgo_vch_serie,sgo_vch_numero,sgo_dec_total,sgo_int_moneda,sgo_vch_observacion FROM tbl_sgo_documentoporpagar WHERE sgo_int_documentoporpagar = " . $valor[0];
           else $sql="SELECT sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_int_persona,sgo_vch_serie,sgo_vch_numero,sgo_dec_total,sgo_int_moneda,sgo_vch_observacion FROM tbl_sgo_documentoporcobrar WHERE sgo_int_documentoporcobrar = " . $valor[0];
           $result = $Obj->consulta($sql);
           while ($row = mysqli_fetch_array($result))
           {
              $tipo=$row["sgo_int_tipocomprobante"];$serie=$row["sgo_vch_serie"];$numero=$row["sgo_vch_numero"];$categoria=$row["sgo_int_categoriacomprobante"];
              $persona=$row["sgo_int_persona"];$moneda=$row["sgo_int_moneda"];$monto=$row["sgo_dec_total"];$obs=$row["sgo_vch_observacion"];
              break;
           }
         }
         $disabled=($valor[0]=="0"?false:true);
         $Val_Caja=new InputValidacion();
         $Val_Caja->InputValidacion('DocValue("fil_cmbcaja")!=""','Debe especificar la caja');
         $Val_Tipo=new InputValidacion();
         $Val_Tipo->InputValidacion('DocValue("fil_cmbTipoDocumento")!=""','Debe especificar el tipo de documento');
         $Val_Categoria=new InputValidacion();
         $Val_Categoria->InputValidacion('DocValue("fil_cmbCategoriaDocumento")!=""','Debe especificar la categoría del documento');
         $Val_Nro=new InputValidacion();
         $Val_Nro->InputValidacion('DocValue("fil_txtNroComprobante")!=""','Debe especificar el tipo de documento');
         $Val_Monto=new InputValidacion();
         $Val_Monto->InputValidacion('DocValue("fil_txtMonto")!=""','Debe especificar el monto del documento');
         $Val_Modo=new InputValidacion();
         $Val_Modo->InputValidacion('DocValue("fil_cmbTipoOperacion")!=""','Debe especificar el tipo de operación');
         $Val_Serie=new InputValidacion();
         $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
         $Val_Numero=new InputValidacion();
         $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
         $inputs=array(
            "Tipo Documento" => $Helper->combo_tipocomprobante("fil_cmbTipoDocumento",++$index,"3,4",$tipo,"Cargar_Objeto('general','Combo_TipoCategoria_TipoComprobante','',this.value,'fil_cmbCategoriaDocumento','panel');Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_Tipo,$disabled),
            "Categoria" => $Helper->combo_categoriacomprobante("fil_cmbCategoriaDocumento",++$index,"0",$categoria,"",$Val_Categoria,$disabled),
            "Nro Comprobante" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,40,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,6,70,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
            "Caja" => $Helper->combo_caja("fil_cmbcaja",$index,$caja,"",$Val_Caja,$disabled),
            "Persona" =>  $Helper->combo_persona("fil_cmbPersona",++$index,$persona,"",null,$disabled),
            "Monto" => $Helper->textbox("fil_txtMonto",++$index,$monto,12,100,$TipoTxt->decimal,"","","","",$Val_Monto,$disabled),
            "Modo Operación"=>$Helper->combo_tipooperacionmovimiento("fil_cmbTipoOperacion",++$index,$modo,"",$Val_Modo,$disabled) . '~5',
            "Observación" => $Helper->textarea("fil_txtObservacion",++$index,$obs,150,3,"","","","",null,$disabled) . '~5'
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_documento",$inputs,$buttons,3,700,"","");
         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Documento por cobrar o pagar",700,$html,$Helper->button("","Grabar",70,"Operacion('finanzas','Mant_Documento','tbl_mant_documento','" . $prm . "')","textoInput")) . "<script>Focus('fil_cmbTipoDocumento')</script>";
      }
      function Mant_Documento($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          $trans=$Obj->transaction();
          try{
              // Se registra el documento
              include_once('../../code/bl/bl_general.php'); $Obj_general=new bl_general;
              $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante($valor[1]);
              $categoria=$Obj_general->Obtener_Caracteristica_CategoriaComprobante($valor[2]);
              $tabla=($valor[1]=="5"?"documentoporcobrar":"documentoporpagar");$tipo=($valor[1]=="5"?18:17);
              $correlativo=$Obj_general->Obtener_Correlativo_Comprobante($valor[1],$trans);
              if($correlativo==-1) throw new Exception("Error: Obtener_Correlativo_Comprobante");
              $sql="INSERT INTO tbl_sgo_" . $tabla . " (sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_vch_serie,sgo_vch_numero,sgo_int_persona, sgo_int_moneda, sgo_dec_total,sgo_dec_saldo,sgo_vch_observacion,sgo_dat_fecharegistro,sgo_bit_activo)
              VALUES (" . $valor[1] . "," . $valor[2] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . ($valor[6]==""?"NULL":$valor[6]) . "," . $this->Obtener_IDMoneda_Caja($valor[5]) . "," . $valor[7] . "," . ($categoria["generarcobraropagar"]==1?$valor[7]:"0") . ",'" . $valor[9] . "','" . date("Y-m-d H:i") . "',1)";
              if($trans->query($sql))
              {
                 //Se registro el movimiento
                 $id=mysqli_insert_id($trans);
                 $sql="INSERT INTO tbl_sgo_movimiento (sgo_int_tipocomprobante,sgo_int_caja,sgo_vch_serie,sgo_vch_numero,sgo_int_persona,sgo_dec_total,sgo_vch_observacion,sgo_dat_fecharegistro,sgo_bit_activo)
                 VALUES (" . $tipo . "," . $valor[5] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . ($valor[6]==""?"NULL":$valor[6]) . ",'" . $valor[7] . "','" . $valor[9] . "','" . date("Y-m-d H:i") . "',1)";
                 if($trans->query($sql))
                 {					 
                     // Se asocia el movimiento con el documento
                     $id_mov=mysqli_insert_id($trans);
                     $sql="INSERT INTO tbl_sgo_movimientodetalle (sgo_int_movimiento, sgo_int_documentoporcobraropagar, sgo_int_tipooperacion,sgo_dec_monto,sgo_vch_descripcion,sgo_int_tipodocumento)
                     VALUES (" . $id_mov . "," . $id . "," . $valor[8] . "," . $valor[7] . ",'" . $valor[9] . "'," . ($valor[1]=="5"?"2":"1") . ")";
                     if($trans->query($sql))
                     {
                         $sql="INSERT INTO tbl_sgo_" . $tabla . "detalle (sgo_dat_fecharegistro,sgo_int_caja,sgo_dec_monto,sgo_vch_documento,sgo_int_modo,sgo_vch_observacion,sgo_int_" . $tabla . ",sgo_int_movimiento,sgo_bit_activo)
                         VALUES ('" . date("Y-m-d H:i") ."'," . $valor[5] . "," . $valor[7] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "-" . str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[8] . ",'" . $valor[9] . "'," . $id . "," .  $id_mov. ",1)";
                         if($trans->query($sql))
                         {
                             if($categoria["movimientocaja"]==2)
                             {
                                if($this->Mant_SalidaCaja($valor[5],$valor[7],$trans)==-1)throw new Exception("Error en Mant_SalidaCaja");
                             }
                             else if($categoria["movimientocaja"]==1)
                             {
                                if($this->Mant_EntradaCaja($valor[5],$valor[7],$trans)==-1)throw new Exception("Error en Mant_EntradaCaja");
                             }
                             if($Obj_general->Generar_Numeracion_Comprobante($valor[1],$trans)==-1)throw new Exception("Error: Generar_Numeracion_Comprobante");
                         }
                         else throw new Exception($sql . " => ". $trans->error);
                     }
                     else throw new Exception($sql . " => ". $trans->error);
                 }
                 else throw new Exception($sql . " => ". $trans->error);
              }
              else throw new Exception($sql . " => ". $trans->error);
              $trans->commit();$trans->close();return 1;
          }
          catch(Exception $e){
              echo '<script>alert("Error: ' . $e . '");</script>';
              $trans->rollback();$trans->close();return -1;
          }
//          echo '<script>alert("'.$sql .'")</script>';
      }
      function Mant_EntradaCaja($caja,$monto,$ref_trans=null){
          $sql="UPDATE tbl_sgo_caja SET sgo_dec_total=(sgo_dec_total + " . $monto . ") WHERE sgo_int_caja=" . $caja;
          if($ref_trans==null){
            $Obj=new mysqlhelper; return $Obj->execute($sql);
          }
          else{
            if($ref_trans->query($sql)) return 1;
            else return -1;
          }
      }
      function Mant_SalidaCaja($caja,$monto,$ref_trans=null){
          $sql="UPDATE tbl_sgo_caja SET sgo_dec_total=(sgo_dec_total - " . $monto . ") WHERE sgo_int_caja=" . $caja;
          if($ref_trans==null){
            $Obj=new mysqlhelper; return $Obj->execute($sql);
          }
          else{
            if($ref_trans->query($sql)) return 1;
            else return -1;
          }
      }
      function Confirma_Eliminar_Documento($prm){
          $Helper=new htmlhelper;$valor=explode('|',$prm);
          if($this->Valida_Documento_Movimiento($valor[0],$valor[1])==1) return $Helper->PopUp("","Confirmación",450,htmlentities('No se puede anular el documento ' . $this->Obtener_Numero_Documento($prm) . ', existe un movimiento de caja asociado'),"");
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el documento ' . $this->Obtener_Numero_Documento($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('finanzas','Eliminar_Documento','','" . $prm . "')"));
      }
      function Eliminar_Documento($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($valor[1]=="1")
          {
             $sql="UPDATE tbl_sgo_documentoporcobrar SET sgo_bit_activo=0 WHERE sgo_int_documentoporcobrar=" . $valor[0];
          }
          else
          {
             $sql="UPDATE tbl_sgo_documentoporpagar SET sgo_bit_activo=0 WHERE sgo_int_documentoporpagar=" . $valor[0];
          }
//          echo '<script>alert("'.$sql .'")</script>';
          if($Obj->execute($sql)!=-1){
             return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarDocumento');</script>";
          }
          else return "<script>Operacion_Result(false);</script>";
      }

/****************************************************************CAJA Y BANCOS************************************************************************/
      function Filtros_Listar_Cajas(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=10;
         $inputs=array(
            "Caja" => $Helper->textbox("fil_cmbBusquedacaja",$index,"",100,250,$TipoTxt->texto,"","","",""),
            "Moneda" => $Helper->combo_moneda_alias("fil_txtBusquedaMoneda",++$index,"","")
         );
         $buttons=array($Helper->button("btnBuscarDocumento","Buscar",70,"Buscar_Grilla('finanzas','Grilla_Listar_Cajas','tbl_listarcajas','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarcajas",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Cajas($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT caj.sgo_int_caja as param, caj.sgo_vch_descripcion as 'Caja/Bancos', mon.sgo_vch_simbolo as Moneda,caj.sgo_dec_total as 'Monto Total', caj.sgo_vch_nrocuenta as 'Nro Cuenta'
          FROM tbl_sgo_caja caj
          INNER JOIN tbl_sgo_moneda mon ON mon.sgo_int_moneda=caj.sgo_int_moneda";
         $where = $Obj->sql_where("WHERE caj.sgo_vch_descripcion like '%@p1%' and mon.sgo_int_moneda=@p2 and caj.sgo_bit_activo=1",$prm);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('finanzas','PopUp_Mant_Caja','','","","PopUp('finanzas','PopUp_Mant_Caja','','","PopUp('finanzas','Confirma_Eliminar_Caja','','",null,array(),array(),array(),20,"");
      }
      function PopUp_Mant_Caja($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=10;
         $nombre="";$moneda="";$nrocuenta="";
         $result = $Obj->consulta("SELECT sgo_vch_descripcion,sgo_int_moneda,sgo_vch_nrocuenta FROM tbl_sgo_caja WHERE sgo_int_caja = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $nombre=$row["sgo_vch_descripcion"];$moneda=$row["sgo_int_moneda"];$nrocuenta=$row["sgo_vch_nrocuenta"];
            break;
         }
         $Val_Nombre=new InputValidacion();
         $Val_Nombre->InputValidacion('DocValue("fil_txtNombre")!=""','Debe especificar el nombre de la caja o banco');
         $Val_Moneda=new InputValidacion();
         $Val_Moneda->InputValidacion('DocValue("fil_cmbMoneda")!=""','Debe especificar la moneda');
         $inputs=array(
            "Nombre" => $Helper->textbox("fil_txtNombre",++$index,$nombre,100,250,$TipoTxt->texto,"","","","",$Val_Nombre),
            "Moneda" => $Helper->combo_moneda_alias("fil_cmbMoneda",++$index,$moneda,"",$Val_Moneda),
            "Nro. Cuenta" => $Helper->textbox("fil_txtNroCuenta",++$index,$nrocuenta,20,150,$TipoTxt->texto,"","","",""),
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_caja",$inputs,$buttons,2,700,"","");
         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Caja/Banco",700,$html,$Helper->button("","Grabar",70,"Operacion('finanzas','Mant_Caja','tbl_mant_caja','" . $prm . "')","textoInput"));
      }
      function Mant_Caja($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($valor[0]!=0) $sql= "UPDATE tbl_sgo_caja SET sgo_vch_descripcion='" . $valor[1] . "',sgo_int_moneda=" . $valor[2] . ",sgo_vch_nrocuenta='" . $valor[3] . "' WHERE sgo_int_caja=" . $valor[0];
          else $sql="INSERT INTO tbl_sgo_caja (sgo_vch_descripcion,sgo_int_moneda, sgo_vch_nrocuenta, sgo_bit_activo) VALUES ('" . $valor[1] . "'," . $valor[2] . ",'" . $valor[3] . "',1)";
//          echo '<script>alert("'.$sql .'")</script>';
          return $Obj->execute($sql);
      }
      function Confirma_Eliminar_Caja($prm){
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la caja ' . $this->Obtener_Nombre_Caja($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('finanzas','Eliminar_Caja','','" . $prm . "')"));
      }
      function Eliminar_Caja($prm){
          $Obj=new mysqlhelper;
          $sql="UPDATE tbl_sgo_caja SET sgo_bit_activo=0 WHERE sgo_int_caja=" . $prm;
//          echo '<script>alert("'.$sql .'")</script>';
          if($Obj->execute($sql)!=-1){
             return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarCaja');</script>";
          }
          else return "<script>Operacion_Result(false);</script>";
      }

/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
      function Obtener_Nombre_FacturaCompra($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT Concat(LPAD(co.sgo_int_serie,3,'0'),'-',LPAD(co.sgo_int_numero,6,'0')) as sgo_vch_nrofacturacompra FROM tbl_sgo_comprobantecompra co WHERE co.sgo_int_comprobantecompra=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_nrofacturacompra"];
          }
          return "";
      }
      function Obtener_Nombre_Proveedor($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_razonsocial FROM tbl_sgo_proveedor WHERE sgo_int_proveedor=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_razonsocial"];
          }
          return "";
      }
      function Obtener_IDTipo_Articulo($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_int_tipo FROM tbl_sgo_producto WHERE sgo_int_producto=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_int_tipo"];
          }
          return "";
      }
      function Obtener_Numero_Movimiento($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT Concat(sgo_vch_serie,'-',sgo_vch_numero) as sgo_vch_codigo FROM tbl_sgo_movimiento WHERE sgo_int_movimiento=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_codigo"];
          }
          return "";
      }
      function Obtener_Nombre_Caja($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_descripcion FROM tbl_sgo_caja WHERE sgo_int_caja=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_descripcion"];
          }
          return "";
      }
      function Obtener_IDMoneda_Caja($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_int_moneda FROM tbl_sgo_caja WHERE sgo_int_caja=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_int_moneda"];
          }
          return "0";
      }
      function Obtener_Numero_Documento($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($valor[1]=="1"){
            $result= $Obj->consulta("SELECT Concat(sgo_vch_serie,'-',sgo_vch_numero) as sgo_vch_nrocomprobante FROM tbl_sgo_documentoporcobrar WHERE sgo_int_documentoporcobrar=" . $valor[0]);
            while ($row = mysqli_fetch_array($result))
            {
              return $row["sgo_vch_nrocomprobante"];
            }
          }
          else{
            $result= $Obj->consulta("SELECT Concat(sgo_vch_serie,'-',sgo_vch_numero) as sgo_vch_nrocomprobante FROM tbl_sgo_documentoporpagar WHERE sgo_int_documentoporpagar=" . $valor[0]);
            while ($row = mysqli_fetch_array($result))
            {
              return $row["sgo_vch_nrocomprobante"];
            }
          }
          return "";
      }
      function Valida_Documento_Movimiento($documento,$tipo){
          $Obj=new mysqlhelper;
          $sql="SELECT COUNT(mdet.sgo_int_movimientodetalle) FROM tbl_sgo_movimientodetalle mdet INNER JOIN tbl_sgo_movimiento mov ON mov.sgo_int_movimiento=mdet.sgo_int_movimiento WHERE mov.sgo_bit_activo=1 AND mdet.sgo_int_documentoporcobraropagar=" . $documento . " AND mdet.sgo_int_tipodocumento=" . $tipo;
          $result=$Obj->consulta($sql);
          while ($row = mysqli_fetch_array($result)){ return 1; }
          return 0;
      }
      function Valida_Documento_Nulidad_segun_Movimiento($movimiento,$tipo){
          $Obj=new mysqlhelper;$documento="0";
          if($tipo==17){ //Movimiento Entrada
             $sql="SELECT sgo_int_documentoporpagar FROM tbl_sgo_documentoporpagardetalle WHERE sgo_int_movimiento=" . $movimiento;
             $result=$Obj->consulta($sql);
             while ($row = mysqli_fetch_array($result)){ $documento= $row["sgo_int_documentoporpagar"]; }
             $sql="SELECT sgo_int_documentoporpagar FROM tbl_sgo_documentoporpagardetalle WHERE sgo_int_documentoporpagar=" . $documento . " AND sgo_bit_activo=1";
             $result=$Obj->consulta($sql);
             while ($row = mysqli_fetch_array($result)){ return -1; }
             return $documento;
          }
          else{ //Movimiento Salida
             $sql="SELECT sgo_int_documentoporcobrar FROM tbl_sgo_documentoporcobrardetalle WHERE sgo_int_movimiento=" . $movimiento;
             while ($row = mysqli_fetch_array($result)){ $documento=$row["sgo_int_documentoporcobrar"]; }
             $sql="SELECT sgo_int_documentoporcobrar FROM tbl_sgo_documentoporcobrardetalle WHERE sgo_int_documentoporcobrar=" . $documento . " AND sgo_bit_activo=1";
             $result=$Obj->consulta($sql);
             while ($row = mysqli_fetch_array($result)){ return -1; }
             return $documento;
          }

          $result=$Obj->consulta($sql);
          while ($row = mysqli_fetch_array($result)){ return 1; }
          return -1;
      }
   }
?>