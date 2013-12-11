<?php include_once('../../code/lib/htmlhelper.php');include_once('../../code/lib/loghelper.php');include_once('../../code/config/app.inc');
  Class bl_facturacion
  {
/********************************************************FACTURAS**************************************************************************/
      function Filtros_Listar_ComprobanteVenta(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $inputs=array(
            //"Cliente" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"",""),
            "Cliente" => $Helper->textbox_predictivo("fil_txtCliente",++$index,"",128,300,"","","clientes"),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Tipo Documento" => $Helper->combo_tipo_filtrodocumentocomprobanteventa("fil_cmbTipoDocumentoFiltro", ++$index, "", ""),
			"Nro. Documento" => $Helper->textbox("fil_txtBusquedaNroDocumento",++$index,"",11,100,$TipoTxt->texto,"","","",""),
			"Tipo"=> $Helper->combo_tipocomprobante("fil_cmbTipoComprobante",++$index,"1","","","",""),			
            "Estado" => $Helper->combo_estado_comprobante("fil_cmbBusquedaestadocotizacion",++$index,"1","")
         );
         $buttons=array($Helper->button("btnBuscarGuia","Buscar",70,"Buscar_Grilla('facturacion','Grilla_Listar_ComprobanteVenta','tbl_listarfacturas','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarfacturas",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_ComprobanteVenta($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);

         $sql ="SELECT		factura.sgo_int_comprobanteventa as param,
		 					tipocomprobante.sgo_vch_descripcion as 'Tipo Comprobante',		 					
		 					concat(identidad.sgo_vch_descripcion,'-',per.sgo_vch_nrodocumentoidentidad) as 'Doc. Identidad',
							concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) as Cliente,
							concat(factura.sgo_vch_serie,'-',factura.sgo_vch_numero) as 'Nro. Factura',
							factura.sgo_dat_fechaemision as 'Fecha Emision',                            
							moneda.sgo_vch_simbolo as 'Moneda',
							factura.sgo_dec_subtotal as 'Subtotal',
							factura.sgo_dec_igv as 'IGV',
							ifnull(factura.sgo_dec_totaladelanto,0.00) as 'Adelantos',
							factura.sgo_dec_total  as 'Total',
							estado.sgo_vch_descripcion as 'Estado'
				FROM		tbl_sgo_comprobanteventa factura
				inner		join tbl_sgo_cliente cliente ON	factura.sgo_int_cliente = cliente.sgo_int_cliente
            	inner    	join tbl_sgo_persona per ON per.sgo_int_persona=cliente.sgo_int_cliente
				inner		join tbl_sgo_tipodocumentoidentidad identidad ON per.sgo_int_tipodocumentoidentidad = identidad.sgo_int_documentoidentidad
				inner		join tbl_sgo_moneda moneda ON factura.sgo_int_moneda = moneda.sgo_int_moneda
				inner		join tbl_sgo_estadocomprobante estado ON factura.sgo_int_estadocomprobante = estado.sgo_int_estadocomprobante
				inner		join tbl_sgo_tipocomprobante tipocomprobante on factura.sgo_int_tipocomprobante = tipocomprobante.sgo_int_tipocomprobante
				inner		join tbl_sgo_categoriacomprobante catcomp on factura.sgo_int_categoriacomprobante = catcomp.sgo_int_categoriacomprobante    
				where 		factura.sgo_int_tipocomprobante in (1,2)				
				and 		factura.sgo_int_categoriacomprobante in (2,3) ";
            $where="";
			if($valor[0]!="")
				$where.=" and concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) like '%".$valor[0]."%'";
			if($valor[1]!="")
         		$where .= " and factura.sgo_dat_fechaemision >= '".$Helper->convertir_fecha_ingles($valor[1])." 00:00'";
			IF($valor[2]!="")
         		$where .= " and factura.sgo_dat_fechaemision <= '".$Helper->convertir_fecha_ingles($valor[2])." 23:59'";
			if($valor[3]!="")
			{
				switch($valor[3])
				{
					case "1": $where.=" and concat(factura.sgo_vch_serie,'-',factura.sgo_vch_numero) like '%".$valor[4]."%'";
					break;
					case "2": $where.=" and factura.sgo_int_comprobanteventa in (
										select	detalle.sgo_int_comprobanteventa
										from	tbl_sgo_comprobanteventadetalle detalle
										inner	join tbl_sgo_guiaremision guia
										on		detalle.sgo_int_guiaremision = guia.sgo_int_guiaremision
										where	concat(sgo_vch_serie,'-',sgo_vch_numero) like '%".$valor[4]."%')";
					break;
					case "3": $where.=" and factura.sgo_int_comprobanteventa in (
										select	detalle.sgo_int_comprobanteventa
										from	tbl_sgo_comprobanteventadetalle detalle
										inner	join tbl_sgo_ordenservicio os
										on		detalle.sgo_int_ordenservicio = os.sgo_int_ordenservicio
										where	concat(sgo_vch_serie,'-',sgo_vch_numero) like '%".$valor[4]."%')";
					break;
					case "4": $where.=" and factura.sgo_int_comprobanteventa in (
										select	detalle.sgo_int_comprobanteventa
										from	tbl_sgo_comprobanteventadetalle detalle
										inner	join tbl_sgo_ordenservicio os
										on		detalle.sgo_int_ordenservicio = os.sgo_int_ordenservicio
										where	sgo_vch_nroordencompracliente like '%".$valor[4]."%')";
					break;
				}
			}				
			if($valor[5]!="")
				$where .= " and factura.sgo_int_tipocomprobante = ".$valor[5];
				
			
			if($valor[6]!="")
				$where .= " and factura.sgo_int_estadocomprobante =".$valor[6];		
			else
			{
				if($valor[0]=="" && $valor[1]=="" && $valor[2]=="" && $valor[3]=="" && $valor[4]=="" && $valor[5]=="")
					$where .= " and factura.sgo_int_estadocomprobante =1";
			}
							
         $orderby=" ORDER BY factura.sgo_int_comprobanteventa DESC";		
         //$loghelper=new loghelper();
         //$loghelper->log($sql . " " . $where . " " . $orderby); 
		 return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"PopUp('facturacion','PopUp_Mant_ComprobanteVenta','','","","PopUp('facturacion','PopUp_Mant_ComprobanteVenta','','","PopUp('facturacion','Confirma_Eliminar','','",null,null,array(),array(),20,"");
      }
      function emitirGuiaComprobante($prm){
      	$Obj   = new mysqlhelper;          
		$sql= "UPDATE tbl_sgo_comprobanteventa SET sgo_int_estadocomprobante = 6 where sgo_int_comprobanteventa=". $prm;		
        $Obj->execute($sql);        
      }
      function PopUp_Imprimir($prm){
      	  $valor=explode('|',$prm);$id=$valor[0];      	  
          $ruta = $this->Obtener_ArchivoImpresion_ComprobanteVenta($id);         
          $this->Mant_ComprobanteVentaByEstado($prm,ESTADO_COMPROBANTE_IMPRESA);
          $this->emitirGuiaComprobante($id);
          if($ruta!="") echo "<script>
          						BtnMouseDown('btnBuscarGuia');
          	 					Operacion_Reload('facturacion','PopUp_Mant_ComprobanteVenta','',".$prm.",'popupfaturacion');
          						window.open('../../" . $ruta . "?prm=" . $prm . "','dumb');
          					  </script>";
          else echo "<script>Ver_Mensaje('Impresi&oacute;n','No existe un formato de impresi&oacute;n configurado')</script>";
      }
  	  function PopUp_ReImprimir($prm){
      	  $valor=explode('|',$prm);$id=$valor[0];      	  
          $ruta = $this->Obtener_ArchivoImpresion_ComprobanteVenta($id);         
          //$this->Mant_ComprobanteVentaByEstado($prm,ESTADO_COMPROBANTE_IMPRESA);
          //$this->emitirGuiaComprobante($id);
          if($ruta!="") echo "<script>
          						BtnMouseDown('btnBuscarGuia');
          	 					Operacion_Reload('facturacion','PopUp_Mant_ComprobanteVenta','',".$prm.",'popupfaturacion');
          						window.open('../../" . $ruta . "?prm=" . $prm . "','dumb');
          					  </script>";
          else echo "<script>Ver_Mensaje('Impresi&oacute;n','No existe un formato de impresi&oacute;n configurado')</script>";
      }
      function PopUp_Mant_ComprobanteVenta($prm){
         $Obj=new mysqlhelper;
		 $Helper=new htmlhelper;
		 $TipoTxt=new TipoTextBox;
		 $TipoDate=new TipoTextDate;
		 $index=1;
         $disabled=false;$cliente="0";$clientefactura="0";$estado="1"; $serie=""; $numero=""; $moneda=""; $direccion ="0"; $categoria="";$tipo="0";$fechaemision=date("d/m/Y");
		 if($prm!=0)
		 {
			 $result = $Obj->consulta("SELECT factura.sgo_int_cliente, factura.sgo_int_clientefactura, factura.sgo_int_tipocomprobante,factura.sgo_int_categoriacomprobante, factura.sgo_vch_serie, factura.sgo_int_direccion, factura.sgo_vch_numero,	factura.sgo_int_estadocomprobante, factura.sgo_int_moneda, factura.sgo_bit_exportador, DATE_FORMAT(factura.sgo_dat_fechaemision,'%d/%m/%Y') as fechaemision FROM 		tbl_sgo_comprobanteventa factura WHERE 	factura.sgo_int_comprobanteventa = " . $prm);
			 while ($row = mysqli_fetch_array($result))
			 {
				$cliente = $row["sgo_int_cliente"];
				$clientefactura = $row["sgo_int_clientefactura"];
				$direccion =$row["sgo_int_direccion"];
				$estado = $row["sgo_int_estadocomprobante"];
				$moneda = $row["sgo_int_moneda"];
				$tipo = $row["sgo_int_tipocomprobante"];
				$categoria = $row["sgo_int_categoriacomprobante"];
				$serie = $row["sgo_vch_serie"];
				$numero= $row["sgo_vch_numero"];
                $exportador = $row["sgo_bit_exportador"];
                $fechaemision=($row["fechaemision"]!=""?$row["fechaemision"]:date("d/m/Y"));
				break;
			 }
			 $disabled=true;
		 }		 		 
		 //validaciones
         $Val_Tipo=new InputValidacion();
         $Val_Tipo->InputValidacion('DocValue("fil_cmbTipoDocumento")!=""','Debe especificar el tipo de documento');
         $Val_Categoria=new InputValidacion();
         $Val_Categoria->InputValidacion('DocValue("fil_cmbTipoDocumento")!=""','Debe especificar el tipo de documento');
         $Val_Cliente=new InputValidacion();
         $Val_Cliente->InputValidacion('DocValue("fil_cmbclientePopUp")!=""','Debe especificar el cliente');
         $Val_Estado=new InputValidacion();
         $Val_Estado->InputValidacion('DocValue("fil_cmbEstadoGuiaRemisionPopUp")!=""','Debe especificar el estado');
         $Val_Moneda=new InputValidacion();
         $Val_Moneda->InputValidacion('DocValue("fil_cmbMonedaPopUp")!=""','Debe especificar la moneda');
         $Val_Serie=new InputValidacion();
         $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
         $Val_Numero=new InputValidacion();
         $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
         $Val_Fecha=new InputValidacion();
         $Val_Fecha->InputValidacion('DocValue("fil_txtFechaEmision")!=""','Debe especificar la fecha en la que se emite el documento');

		 //campos de la cabecera
         $inputs=array(
            "Tipo Documento" => $Helper->combo_tipocomprobante("fil_cmbTipoDocumento",++$index,"1",$tipo,"Cargar_Objeto('general','Combo_TipoCategoria_TipoComprobante','',this.value,'fil_cmbCategoriaDocumento','panel');Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_Tipo,$disabled),
            "Categoria" => $Helper->combo_categoriacomprobante("fil_cmbCategoriaDocumento",++$index,"1",$categoria,"",$Val_Categoria,$disabled),
            "Nro Comprobante" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,40,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,6,70,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
		 	"Cliente" => $Helper->combo_cliente("fil_cmbclientePopUp",$index,$cliente,"document.getElementById('fil_cmbclientefacturaPopUp').selectedIndex = document.getElementById('fil_cmbclientePopUp').selectedIndex;Cargar_Combo('general','Combo_Direccion_x_Cliente','fil_cmbDireccionDatoCliente','fil_cmbclientefacturaPopUp','fil_cmbDireccionDatoCliente');",null,$disabled),
         	"Cliente Factura" => $Helper->combo_cliente("fil_cmbclientefacturaPopUp",$index,$clientefactura,"Cargar_Combo('general','Combo_Direccion_x_Cliente','fil_cmbDireccionDatoCliente','fil_cmbclientefacturaPopUp','fil_cmbDireccionDatoCliente')",null,$disabled),
			"Direccion" => $Helper->combo_direccion_x_cliente("fil_cmbDireccionDatoCliente",++$index,$cliente,$direccion,"",null,$disabled),
			"Moneda" => $Helper->combo_moneda_alias("fil_cmbMonedaPopUp",++$index,$moneda,"",$Val_Moneda,""),
            "Estado" => $Helper->combo_estado_comprobante("fil_cmbEstadoGuiaRemisionPopUp",++$index,$estado,"",$Val_Estado,true),
       		"Exportador"=> $Helper->checkbox("fil_chkExportacion",++$index,$exportador),
         	"Emisión" =>  $Helper->textdate("fil_txtFechaEmision",++$index,$fechaemision,false,$TipoDate->fecha,80,"","",$Val_Fecha)
       	 );

         $buttons=array();
		 $html = '<fieldset class="textoInput"><legend align= "left">Cabecera de la Factura</legend>';
         $html .= $Helper->Crear_Layer("tbl_mant_guiaremision",$inputs,$buttons,3,1000,"","");
		 $html .= '</fieldset>';

		 if($prm!=0)
		 {
				 $html.='<br/>';
				 $html.= '<fieldset class="textoInput"><legend align= "left">Detalle de la Factura</legend>';
         		 $html.='<table id="tbl_detalleguia_cabecera" border="0" cellpadding="3" cellspacing="0" style="width:100%;" class="textoInput"><tbody><tr>';
				 if($estado!=6 && $estado!=3 ){
    			 $html.='<tr><td style="padding-left:5px;">OC</td><td>' . $Helper->combo_ordenservicio_cliente("fil_cmbOCPopUpDetalle",1,$cliente,"","Cargar_Combo('general','Combo_Guias_x_OC','fil_cmbGuiaRemisionOCPopUpDetalle','fil_cmbOCPopUpDetalle','fil_cmbGuiaRemisionOCPopUpDetalle');","")."&nbsp;Guia&nbsp;".$Helper->combo_guiaremision_x_ordenservicio("fil_cmbGuiaRemisionOCPopUpDetalle",2,"","","","")."&nbsp;".$Helper->button("","Cargar Productos",120,"Operacion('facturacion','Carga_DetalleGuiaRemision','tbl_detalleguia_cabecera','" . $prm . "');Cargar_Objeto('facturacion','MensajeCredito','',".$prm.", 'dvMensajeCredito', 'panel');","textoInput").'&nbsp;'.$Helper->button("","Adelantos",100,"PopUp('facturacion','PopUp_DocumentosAplicablesDescuento','','" . $prm ."|".$cliente. "')","textoInput").'</td><td><div id="dvMensajeCredito"></div></td></tr>';
				 }
    			 $html.='<tr><td colspan="3" height="10px"></td></tr>';
    			 $html.='</tbody></table>';
				 $html.="<div id='div_ComprobanteVenta_Detalles'>" . $this->Grilla_Listar_ComprobanteVenta_Detalle($prm,950,$estado) . "</div>";
				 $html.='</fieldset>';
				 //$subtotal = $this->obtenerSubtotal($prm);
		 }
         return $Helper->PopUp("popupfaturacion",($prm==0?"Nuevo":"Actualizar") . " Documento de Venta",1000,$html,($estado!=6 && $estado!=3?$Helper->button("","Grabar",70,"Operacion('facturacion','Mant_ComprobanteVenta','tbl_mant_guiaremision','" . $prm . "')","textoInput")."  ".($estado!=6 && $estado!=3  && $prm!=0?$Helper->button("","Imprimir",70,"Operacion('facturacion','PopUp_Imprimir','tbl_mant_guiaremision','" . $prm . "')","textoInput"):""):$Helper->button("","Re-imprimir",120,"Operacion('facturacion','PopUp_ReImprimir','tbl_mant_guiaremision','" . $prm . "')","textoInput")));
      }
	  function PopUp_DocumentosAplicablesDescuento($prm){
         $Obj=new mysqlhelper;
		 $Helper=new htmlhelper;
		 $TipoTxt=new TipoTextBox;
		 $valor = explode('|',$prm);
		 $index=1;
		 $estado="";

		 $html = '<fieldset class="textoInput"><legend align= "left">Aplicar adelanto</legend>';
         $html.= "<div id='div_ComprobanteVenta_DocumentosDescuento'>" . $this->Grilla_Listar_DocumentosDescuento($valor[0], $valor[1]) . "</div>";
		 $html .= '</fieldset><br>';

		 $html .= '<fieldset class="textoInput"><legend align= "left">Adelantos aplicados</legend>';
         $html .= "<div id='div_ComprobanteVenta_DocumentosDescuentoAplicados'>" . $this->Grilla_Listar_DocumentosDescuentoAplicados($valor[0], $valor[1]) . "</div>";
		 $html .= '</fieldset>';

         return $Helper->PopUp("","Aplicar Documento de Adelanto",1000,$html,($estado!=6?$Helper->button("","Grabar",70,"Operacion('facturacion','Mant_Descuento','div_ComprobanteVenta_DocumentosDescuento','" . $prm . "')","textoInput"):""));
      }
	  function Mant_Descuento($prm){
		 $Obj=new mysqlhelper;
		 $valor=explode('|',$prm);
		 $id=$valor[0];
		 $valor_grilla = explode('_',$valor[2]);
		 $sql="select sgo_bit_exportador from tbl_sgo_comprobanteventa where sgo_int_comprobanteventa = ".$id;
		 $resultado = $Obj->execute($sql);
		 while ($row = mysqli_fetch_array($result))
		 {
		 	$exportador=$row["sgo_bit_exportador"];
		 }
         try{
			 if(count($valor_grilla)>0)
			 {
				 $trans=$Obj->transaction();
				 foreach ($valor_grilla as $k)
				 {
					$valor_col=explode('~',$k);
					if($valor_col[1]!=0)
					{
						$sql = "UPDATE 	tbl_sgo_comprobanteventa 
								SET 	sgo_dec_totaladelanto = (sgo_dec_totaladelanto + ".$valor_col[1]."), 
										sgo_dec_subtotal = (sgo_dec_subtotal - ".$valor_col[1]."), 
										sgo_dec_igv = ".($exportador==0?"(sgo_dec_subtotal * ".VALOR_IGV.")":"0").", 
										sgo_dec_total = (sgo_dec_subtotal + sgo_dec_igv) 
								WHERE 	sgo_int_comprobanteventa = ".$id;
						if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
						$sql = "INSERT INTO tbl_sgo_adelanto 
								(
									sgo_int_comprobante, 
									sgo_int_comprobanteadelanto, 
									sgo_dec_monto, 
									sgo_dec_fecha
								) 
								VALUES 
								("
									.$id.","
									.$valor_col[0].","
									.$valor_col[1].",'"
									.date("Y-m-d H:i").
								"')";
									
						if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
					}
				 }
			 }
			 $trans->commit();$trans->close();return 1;
		 }
		 catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
	  }
	  function Grilla_Listar_DocumentosDescuento($comprobante, $cliente){
 			$Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;
         	$sql = "select 	cv.sgo_int_comprobanteventa as param, concat(cv.sgo_vch_serie,'-',cv.sgo_vch_numero) as 'Nro Documento',
			cv.sgo_dec_subtotal as 'Total Documento',
			ifnull((cv.sgo_dec_subtotal - (select sum(ad.sgo_dec_monto) from tbl_sgo_adelanto ad where ad.sgo_int_comprobanteadelanto = cv.sgo_int_comprobanteventa)),cv.sgo_dec_subtotal) as 'Saldo Disponible',
			0 as 'Monto a Aplicar'
			from		tbl_sgo_comprobanteventa cv
			where		cv.sgo_int_categoriacomprobante in (1,6)
			and cv.sgo_int_cliente =".$cliente."  and ifnull((cv.sgo_dec_total - (select sum(ad.sgo_dec_monto) from tbl_sgo_adelanto ad where ad.sgo_int_comprobanteadelanto = cv.sgo_int_comprobanteventa)),cv.sgo_dec_total) > 0";
		 	$input_extra=array();$descript_cols=array();
         	$input_extra=array("" => '0|' . $Helper->hidden("fil_gcol_hf_id","num_posicion","num_valor"));
            $descript_cols=array($Helper->textbox("fil_grow_txtMontoApp","num_posicion","num_valor",10,100,$TipoTxt->decimal,"","","","") => 4);
			return $Helper->Imprimir_Grilla($Obj->consulta($sql),"","","","",null,array(),$input_extra,$descript_cols, 20,"");
	  }
	  function Grilla_Listar_DocumentosDescuentoAplicados($comprobante, $cliente){
 			$Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;
         	$sql = "select		concat(cv.sgo_int_comprobanteventa, '|', cv.sgo_int_cliente ,'|', ad.sgo_int_codigo) as param, concat(cd.sgo_vch_serie,'-',cd.sgo_vch_numero) as 'Comprobante de Adelanto',
								ad.sgo_dec_monto as 'Monto',
								ad.sgo_dec_fecha as 'Fecha'
					from		tbl_sgo_adelanto ad
					inner		join tbl_sgo_comprobanteventa cv
					on			ad.sgo_int_comprobante = cv.sgo_int_comprobanteventa
					inner		join tbl_sgo_comprobanteventa cd
					on			ad.sgo_int_comprobanteadelanto = cd.sgo_int_comprobanteventa
					where		cv.sgo_int_comprobanteventa = ".$comprobante;

		 	return $Helper->Imprimir_Grilla($Obj->consulta($sql),"","","","PopUp('facturacion','Confirma_Eliminar_Adelanto','','",null,array(),array(),array(),20,"");
	  }
	  function obtenerTipoComprobante($comprobante){
		 $Obj=new mysqlhelper;
         $sql = "	select	sgo_int_tipocomprobante
		 			from	tbl_sgo_comprobanteventa
					where 	sgo_int_comprobanteventa =".$comprobante;
		 $result = $Obj->consulta($sql);
		 while ($row = mysqli_fetch_array($result))
		 {
            $resp=$row["sgo_int_tipocomprobante"];
            break;
         }
		 return $resp;
	  }
	  function obtenerOCPorComprobante($comprobante){
	  	  	 $resp="";	  	  	
	  		 $Obj=new mysqlhelper;	  		 
	         $sql = "	select	distinct group_concat(sgo_vch_nroordencompracliente) as OC
						from		tbl_sgo_ordenservicio os
						inner		join tbl_sgo_comprobanteventadetalle cvd
						on			os.sgo_int_ordenservicio = cvd.sgo_int_ordenservicio
						where		cvd.sgo_int_comprobanteventa =".$comprobante;
	  		 $result = $Obj->consulta($sql);
	  		 while ($row = mysqli_fetch_array($result))
	  		 {
	              $resp=$row["OC"];
	              break;
	           }
	  		 return $resp;
	  }
	  function obtenerGUIASPorComprobante($comprobante){
	  	  		 $Obj=new mysqlhelper;
	  	         $sql = "	select	group_concat(concat(grem.sgo_vch_serie,'-',grem.sgo_vch_numero)) as GUIA
							from		tbl_sgo_guiaremision grem
							inner		join tbl_sgo_guiaremisiondetalle gremdet
							on			grem.sgo_int_guiaremision = gremdet.sgo_int_guiaremision
							inner		join tbl_sgo_comprobanteventadetalle cvd
							on			cvd.sgo_int_guiaremision = gremdet.sgo_int_guiaremision
							and			cvd.sgo_int_guiaremisiondetalle = gremdet.sgo_int_guiaremisiondetalle
							where		cvd.sgo_int_comprobanteventa =".$comprobante;
	  	  		 $result = $Obj->consulta($sql);
	  	  		 while ($row = mysqli_fetch_array($result))
	  	  		 {
	  	              $resp=$row["GUIA"];
	  	              break;
	  	           }
	  	  		 return $resp;
	  }
      function obtenerDatosCredito($comprobanteVenta)
      {
        $cliente="0";$moneda="0";$formaPago="";$resultado=""; $txtformaPago="";
        $Obj=new mysqlhelper;
        $sql="  select  sgo_int_cliente, sgo_int_moneda
                from    tbl_sgo_comprobanteventa
                where   sgo_int_comprobanteventa =".$comprobanteVenta;
        $result = $Obj->consulta($sql);
		while ($row = mysqli_fetch_array($result))
		{
            $cliente=$row["sgo_int_cliente"];
            $moneda=$row["sgo_int_moneda"];
            break;
        }
        $resultado.=$cliente."|".$moneda;
        $sql="  select  sgo_int_tipoformapago
                from    tbl_sgo_ordenservicio
                where   sgo_int_ordenservicio = (select sgo_int_ordenservicio from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$comprobanteVenta." limit 1)";
        $result = $Obj->consulta($sql);
		while ($row = mysqli_fetch_array($result))
		{
            $formaPago=$row["sgo_int_tipoformapago"];
            break;
        }
        $resultado.="|".$formaPago;

        $sql="  select  sgo_vch_descripcion
                from    tbl_sgo_tipoformapago
                where   sgo_int_tipoformapago =".$formaPago;
        $result = $Obj->consulta($sql);
		while ($row = mysqli_fetch_array($result))
		{
            $txtformaPago=$row["sgo_vch_descripcion"];
            break;
        }
        $resultado.="|".$txtformaPago;               
        return $resultado;
      }
      function Carga_MensajeCredito($prm){
        $Obj=new mysqlhelper;
        $resp="0";$html;
        $sql="";
        $datosCredito = explode('|',$this->obtenerDatosCredito($prm));
        $respuesta="";
        if($datosCredito[2]==1) // credito
        {
          $sql="select
                    (select sgo_dec_credito from tbl_sgo_creditocliente where sgo_int_moneda = ".$datosCredito[1]." and sgo_int_cliente=".$datosCredito[0].") -
                    (
                      (select ifnull(sum(sgo_dec_saldo),0) from tbl_sgo_documentoporcobrar where sgo_int_persona = ".$datosCredito[0].") +
                      (select sgo_dec_total from tbl_sgo_comprobanteventa where sgo_int_comprobanteventa = ".$prm.")
                    ) as Saldo";
          $result = $Obj->consulta($sql);
  		while ($row = mysqli_fetch_array($result))
  		{
              $resp=$row["Saldo"];
              break;
          }
          if($resp>=0)
          {
              $respuesta ="Forma de pago: ".$datosCredito[3]." - Cliente dispone de credito";
          }
          else
          {
              $respuesta ="Forma de pago: ".$datosCredito[3]." - Cliente excede linea de credito";
          }
        }
        else
        {
          $respuesta="Pago al contado";
        }
        return $respuesta;
      }
	  function Carga_DetalleGuiaRemision($prm){
		  $resp="-1";
		  $Obj=new mysqlhelper;$valor=explode('|',$prm);
		  $tipoComprobante = $this->obtenerTipoComprobante($valor[0]);
          $exportador = $this->obtenerExportador($valor[0]);
          $moneda = $this->obtenerMoneda($valor[0]);
          $fecha = $this->obtenerFechaEmision($valor[0]);
          if($moneda==2) //dólares
          {
          	$tipoCambio = $this->obtenerTipoCambio($fecha, $moneda);
          }
          if($valor[0]!=0)
		  {
		      //if($tipoComprobante==2) // si es una factura
			  //{
    			  if($valor[1]!="" && $valor[2]=="") //Solo selecciono una orden de servicio
    			  {
    				 $sql= "insert into tbl_sgo_comprobanteventadetalle 
    				 		(
    				 			sgo_int_comprobanteventa, 
    				 			sgo_int_ordenserviciodetalle , 
    				 			sgo_int_ordenservicio, 
    				 			sgo_vch_descripcion, 
    				 			sgo_dec_cantidad, 
    				 			sgo_dec_precio, 
    				 			sgo_int_producto,
    				 			sgo_dec_valorventa
    				 		) 
    				 		select 		".$valor[0].", 
    				 					ocdet.sgo_int_ordenserviciodetalle, 
    				 					ocdet.sgo_int_ordenservicio, 
    				 					CONCAT(producto.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')), 
    				 					ocdet.sgo_dec_cantidad, 
    				 					".($moneda!=1?"(round(ocdet.sgo_dec_precio/".$tipoCambio[1].",2))":"ocdet.sgo_dec_precio").", 
    				 					ocdet.sgo_int_producto, 
    				 					(ocdet.sgo_dec_cantidad * ".($moneda!=1?"(round(ocdet.sgo_dec_precio/".$tipoCambio[1].",2))":"ocdet.sgo_dec_precio").") 
    				 		from		tbl_sgo_ordenserviciodetalle ocdet 
    				 		inner 		join tbl_sgo_producto producto 
    				 		on 			producto.sgo_int_producto = ocdet.sgo_int_producto 
    				 		LEFT JOIN 	tbl_sgo_categoriaproductocolor catprodcol 
    				 		on 			producto.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto 
    				 		and 		producto.sgo_int_color = catprodcol.sgo_int_color 
    				 		LEFT 		JOIN tbl_sgo_categoriaproductotamano catprodtam 
    				 		on 			producto.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto 
    				 		and 		producto.sgo_int_tamano = catprodtam.sgo_int_tamano 
    				 		LEFT 		JOIN tbl_sgo_categoriaproductocalidad catprodcal 
    				 		on 			producto.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto 
    				 		and 		producto.sgo_int_calidad = catprodcal.sgo_int_calidad 
    				 		where 		ocdet.sgo_int_ordenservicio=".$valor[1]; // in (select sgo_int_ordenservicio from tbl_sgo_ordenservicio where sgo_vch_nroordencompracliente = (select sgo_vch_nroordencompracliente from tbl_sgo_ordenservicio where sgo_int_ordenservicio = ".$valor[1].")) and 		ocdet.sgo_int_ordenserviciodetalle not in (select 	sgo_int_ordenserviciodetalle from 		tbl_sgo_comprobanteventadetalle where 	sgo_int_comprobanteventa = " . $valor[0] .");";
    			  }
    			  else // seleccionó la orden de servicio y una guía de remisión
    			  {
    				 $sql= "insert into tbl_sgo_comprobanteventadetalle (sgo_int_comprobanteventa,  sgo_int_ordenserviciodetalle , 
    				 		sgo_int_ordenservicio, sgo_vch_descripcion, sgo_dec_cantidad, sgo_dec_precio,sgo_int_producto, sgo_dec_valorventa, 
    				 		sgo_int_guiaremision, sgo_int_guiaremisiondetalle) 
    				 		select 		".$valor[0].", 
    				 					ocdet.sgo_int_ordenserviciodetalle,
    				 					ocdet.sgo_int_ordenservicio, 
    				 					CONCAT(producto.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), 
    				 					' ', 
    				 					ifnull(catprodtam.sgo_vch_tamano,''), 
    				 					' ',
    				 					ifnull(catprodcal.sgo_vch_calidad,'')), 
    				 					guiadet.sgo_dec_cantidad, 
    				 					".($moneda!=1?"(round(ocdet.sgo_dec_precio/".$tipoCambio[1].",2))":"ocdet.sgo_dec_precio").",ocdet.sgo_int_producto, (guiadet.sgo_dec_cantidad * ".($moneda!=1?"(round(ocdet.sgo_dec_precio/".$tipoCambio[1].",2))":"ocdet.sgo_dec_precio")."), guiadet.sgo_int_guiaremision, guiadet.sgo_int_guiaremisiondetalle from tbl_sgo_guiaremisiondetalle guiadet inner	join tbl_sgo_ordenserviciodetalle ocdet on guiadet.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle inner join tbl_sgo_producto producto on producto.sgo_int_producto = ocdet.sgo_int_producto LEFT JOIN tbl_sgo_categoriaproductocolor catprodcol on producto.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto and producto.sgo_int_color = catprodcol.sgo_int_color LEFT JOIN tbl_sgo_categoriaproductotamano catprodtam on producto.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto and producto.sgo_int_tamano = catprodtam.sgo_int_tamano LEFT JOIN tbl_sgo_categoriaproductocalidad catprodcal on producto.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto and producto.sgo_int_calidad = catprodcal.sgo_int_calidad where guiadet.sgo_int_guiaremision = ".$valor[2]; //." and ocdet.sgo_int_ordenserviciodetalle not in (select sgo_int_ordenserviciodetalle from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa = ".$valor[0]."); ";
    			  }
              /*}
              else
              {
                   if($valor[1]!="" && $valor[2]=="") //Solo selecciono una orden de servicio
    			  {
    				 $sql= "insert into tbl_sgo_comprobanteventadetalle (sgo_int_comprobanteventa, sgo_int_ordenserviciodetalle , sgo_int_ordenservicio, sgo_vch_descripcion, sgo_dec_cantidad, sgo_dec_precio, sgo_int_producto,sgo_dec_valorventa) select ".$valor[0].", ocdet.sgo_int_ordenserviciodetalle,ocdet.sgo_int_ordenservicio, CONCAT(producto.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')), ocdet.sgo_dec_cantidad, (".($moneda!=1?"(round(ocdet.sgo_dec_precio/".$tipoCambio[1].",2))":"ocdet.sgo_dec_precio")." * ".VALOR_IGV."),ocdet.sgo_int_producto, (ocdet.sgo_dec_cantidad * (".($moneda!=1?"(round(ocdet.sgo_dec_precio/".$tipoCambio[1].",2))":"ocdet.sgo_dec_precio")." * ".VALOR_IGV.")) from		tbl_sgo_ordenserviciodetalle ocdet inner join tbl_sgo_producto producto on producto.sgo_int_producto = ocdet.sgo_int_producto LEFT JOIN tbl_sgo_categoriaproductocolor catprodcol on producto.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto and producto.sgo_int_color = catprodcol.sgo_int_color LEFT JOIN tbl_sgo_categoriaproductotamano catprodtam on producto.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto and producto.sgo_int_tamano = catprodtam.sgo_int_tamano LEFT JOIN tbl_sgo_categoriaproductocalidad catprodcal on producto.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto and producto.sgo_int_calidad = catprodcal.sgo_int_calidad   where ocdet.sgo_int_ordenservicio in (select sgo_int_ordenservicio from tbl_sgo_ordenservicio where sgo_vch_nroordencompracliente = (select sgo_vch_nroordencompracliente from tbl_sgo_ordenservicio where sgo_int_ordenservicio=".$valor[1].")); ";
    			  }
    			  else // seleccionó la orden de servicio y una guía de remisión
    			  {
    				 $sql= "insert into tbl_sgo_comprobanteventadetalle (sgo_int_comprobanteventa,  sgo_int_ordenserviciodetalle , sgo_int_ordenservicio, sgo_vch_descripcion, sgo_dec_cantidad, sgo_dec_precio,sgo_int_producto, sgo_dec_valorventa) select ".$valor[0].", ocdet.sgo_int_ordenserviciodetalle,ocdet.sgo_int_ordenservicio, CONCAT(producto.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')), guiadet.sgo_dec_cantidad, (".($moneda!=1?"(round(ocdet.sgo_dec_precio/".$tipoCambio[1].",2))":"ocdet.sgo_dec_precio")." * ".VALOR_IGV."),ocdet.sgo_int_producto, (guiadet.sgo_dec_cantidad * (".($moneda!=1?"(round(ocdet.sgo_dec_precio/".$tipoCambio[1].",2))":"ocdet.sgo_dec_precio")." * ".VALOR_IGV.")) from tbl_sgo_guiaremisiondetalle guiadet inner	join tbl_sgo_ordenserviciodetalle ocdet on guiadet.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle inner join tbl_sgo_producto producto on producto.sgo_int_producto = ocdet.sgo_int_producto LEFT JOIN tbl_sgo_categoriaproductocolor catprodcol on producto.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto and producto.sgo_int_color = catprodcol.sgo_int_color LEFT JOIN tbl_sgo_categoriaproductotamano catprodtam on producto.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto and producto.sgo_int_tamano = catprodtam.sgo_int_tamano LEFT JOIN tbl_sgo_categoriaproductocalidad catprodcal on producto.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto and producto.sgo_int_calidad = catprodcal.sgo_int_calidad  where guiadet.sgo_int_guiaremision = ".$valor[2]."; ";
    			  }

              }*/
			  if($Obj->execute_insert($sql)!=-1)
			  {
				if($tipoComprobante==2) // si es una factura
				{
				    if($exportador==0)
                    {
                        $sql2 = " 	UPDATE 	tbl_sgo_comprobanteventa set sgo_dec_subtotal = (select sum(sgo_dec_valorventa) from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$valor[0]."), sgo_dec_igv  = (select (sum(sgo_dec_valorventa) * 0.18) from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$valor[0]."), sgo_dec_total  = (sgo_dec_subtotal + sgo_dec_igv ) where		sgo_int_comprobanteventa = ".$valor[0]."";
                    }
                    else
                    {
                        $sql2 = " 	UPDATE 	tbl_sgo_comprobanteventa set sgo_dec_subtotal = (select sum(sgo_dec_valorventa) from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$valor[0]."), sgo_dec_igv  = 0, sgo_dec_total  = sgo_dec_subtotal  where		sgo_int_comprobanteventa = ".$valor[0]."";
                    }
				}
				else
				{
			  		$sql2 = " 	UPDATE 	tbl_sgo_comprobanteventa set sgo_dec_subtotal = (select sum(sgo_dec_valorventa) from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$valor[0]."), sgo_dec_igv  = 0, sgo_dec_total  = sgo_dec_subtotal  where		sgo_int_comprobanteventa = ".$valor[0]."";
				}
			  	$resp = $Obj->execute_insert($sql2);
			  }
		  }
		  return $resp;
	  }
	  function Comprobar_AgenteRetencion($cliente)
	  {
		 $Obj=new mysqlhelper;
         $sql = "	select	case sgo_bit_agenteretencion when 1 then 1 else 0 end as 'sgo_bit_agenteretencion'
		 			from	tbl_sgo_cliente
					where 	sgo_int_cliente =".$cliente;

		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {
            $agente=$row["sgo_bit_agenteretencion"];
            break;
         }
		 return $agente;
	  }
  function Comprobar_AgenteRetencionPorComprobante($comprobante)
	  {
		 $Obj=new mysqlhelper;
         $sql = "	select	case sgo_bit_agenteretencion when 1 then 1 else 0 end as 'sgo_bit_agenteretencion'
		 			from	tbl_sgo_cliente
					where 	sgo_int_cliente =(select sgo_int_cliente from tbl_sgo_comprobanteventa where sgo_int_comprobante=".$comprobante.")";

		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {
            $agente=$row["sgo_bit_agenteretencion"];
            break;
         }
		 return $agente;
	  }
	  function generaMovimientoRetencion($trans, $persona, $observacion, $total, $documento, $documentopocobrar)
	  {
		  include_once('../../code/bl/bl_general.php'); $Obj_general=new bl_general;
		  $comprobante = $Obj_general->Obtener_Caracteristica_Comprobante(17);
		  $sql = "insert into tbl_sgo_movimiento (sgo_int_tipocomprobante, sgo_vch_serie, sgo_vch_numero, sgo_dat_fecharegistro, sgo_int_persona, sgo_int_caja,sgo_vch_observacion, sgo_dec_total, sgo_bit_activo) values (17,'".($comprobante["serie"]!=0?str_pad($comprobante["serie"],$comprobante["digitosserie"],'0', STR_PAD_LEFT):"")."','".str_pad($comprobante["correlativo"],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT)."','".date("Y-m-d H:i")."',".$persona.",1,'".$observacion."',".$total.",1)";
		  try
		  {
			  if($trans->query($sql))
			  {
				  $id=mysqli_insert_id($trans);
				  if($Obj_general->Generar_Numeracion_Comprobante(17,$trans)==-1){throw new Exception("Error: Generar_Numeracion_Comprobante");}
				  $sql= "insert into tbl_sgo_movimientodetalle 
				  		(
				  			sgo_int_movimiento, 
				  			sgo_int_documentoporcobraropagar, 
				  			sgo_int_tipooperacion, 
				  			sgo_dec_monto, 
				  			sgo_vch_descripcion, 
				  			sgo_int_tipodocumento
				  		) 
				  		values
				  		("
				  			.$id.","
				  			.$documentopocobrar.
				  			",1,"
				  			.$total.
				  			",'6% adelantado - Cliente es agente de retención',
				  			17
				  		)";
				  if(!$trans->query($sql)){ throw new Exception($sql . " => " . $trans->error);}

				  $sql = "insert into tbl_sgo_documentoporcobrardetalle (sgo_int_documentoporcobrar, sgo_dat_fecharegistro, sgo_int_caja, sgo_dec_monto, sgo_vch_documento, sgo_int_modo, sgo_vch_observacion, sgo_int_movimiento, sgo_bit_activo) values(".$documentopocobrar.",'".date("Y-m-d H:i")."',1,".$total.",'-',6,'".$observacion."',".$id.",1)";


				  if(!$trans->query($sql)){ throw new Exception($sql . " => " . $trans->error);}
				  
				  

			  }else { throw new Exception($sql . " => " . $trans->error); }
		  }
		  catch(Exception $e)
		  {
			echo "<script>alert('Error: " . $e . "');</script>";
           	$trans->rollback();$trans->close();return -1;
		  }
	  }
      function Mant_ComprobanteVenta($prm){
         $Obj=new mysqlhelper;$valor=explode('|',$prm);$id=$valor[0]; $Helper=new htmlhelper;         
		 $observacion = "Documento por cobrar";
		 $esAgenteRetencion = $this->Comprobar_AgenteRetencion($valor[5]);
         $trans=$Obj->transaction();
         try{
    		  if($valor[2]=="")$valor[2]=0; if($valor[7]=="")$valor[7]=0;
              include_once('../../code/bl/bl_general.php');
              $Obj_general=new bl_general;
              $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante($valor[1]);
              //el comprobante ya existe y se está realizando una actualización
              if($valor[0]!=0)
              {
			  	$adelanto = explode('|',$this->obtenerAdelantos($prm));
				                 			      
    			$sql= "UPDATE 	tbl_sgo_comprobanteventa 
    				  SET 		sgo_int_tipocomprobante=".$valor[1] . ",
    						  	sgo_int_categoriacomprobante=" . $valor[2] .",
    						  	sgo_vch_serie='".$valor[3]."', 
    						  	sgo_vch_numero='".$valor[4]."', 
    						  	sgo_int_cliente=".$valor[5].", 
    						  	sgo_int_clientefactura=".$valor[6].", 
    						  	sgo_int_direccion=".$valor[7].",
    						  	sgo_int_moneda=".$valor[8] . ",
    						  	sgo_int_estadocomprobante=".$valor[9]. ", 
    						  	sgo_dec_totaladelanto = ". $adelanto[1] .",
    						  	sgo_dec_subtotal = (sgo_dec_subtotal - ". $adelanto[1] ."), 
    						  	sgo_dec_igv = ".($valor[1]==TIPO_COMPROBANTE_VENTA_FACTURA?($exportador==0?"(sgo_dec_subtotal * ".VALOR_IGV.")":"0"):"0").",
    						  	sgo_dec_total = (sgo_dec_subtotal + sgo_dec_igv ), 
    						  	sgo_bit_exportador=".($valor[10]=="true"?1:0)."
    					WHERE 	sgo_int_comprobanteventa=" . $valor[0];                                                 								  				                   

                  if(!$trans->query($sql))
                  {
        			  throw new Exception($trans->error);
                  }                  
    		  }
              else
              {
                  $correlativo=$Obj_general->Obtener_Correlativo_Comprobante($valor[1],$trans);
                  if($correlativo==-1) throw new Exception("Error: Obtener_Correlativo_Comprobante");
      		      $sql="	INSERT INTO tbl_sgo_comprobanteventa 
      		      			(
      		      				sgo_int_tipocomprobante,
      		      				sgo_int_categoriacomprobante,
      		      				sgo_vch_serie, 
      		      				sgo_vch_numero,
      		      				sgo_int_cliente, 
      		      				sgo_int_clientefactura, 
      		      				sgo_int_direccion, 
      		      				sgo_int_moneda, 
      		      				sgo_int_estadocomprobante,
      		      				sgo_dat_fechaemision, 
      		      				sgo_bit_exportador)
                  			VALUES 
                  			("
      		      				.$valor[1].","
      		      				.$valor[2].",'" 
      		      				. str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" 
      		      				. str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "',"
      		      				.$valor[5].","
      		      				.$valor[6].","
      		      				.$valor[7].","
      		      				.$valor[8].","
      		      				.$valor[9].",'"
      		      				.$Helper->convertir_fecha_ingles($valor[11])."',"
      		      				.($valor[10]=="true"?1:0).
      		      			")";                  
                  if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                  $id=mysqli_insert_id($trans);
                  if($Obj_general->Generar_Numeracion_Comprobante($valor[1],$trans)==-1)throw new Exception("Error: Generar_Numeracion_Comprobante");
    		  }
              $trans->commit();
              $trans->close();
              return $id;
         }
         catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
      }
  	  function Mant_ComprobanteVentaByEstado($prm,$estadoParam){
         include_once('../../code/bl/bl_general.php');$Obj_general=new bl_general;$Obj=new mysqlhelper;$Helper=new htmlhelper;
         $valor=explode('|',$prm);$id=$valor[0];          
		 $esAgenteRetencion = $this->Comprobar_AgenteRetencion($valor[5]);
         $trans=$Obj->transaction();
         $adelanto = $this->obtenerAdelantos($prm);
         try{
    		  if($valor[2]=="")$valor[2]=0; if($valor[7]=="")$valor[7]=0;              				                			  
    			$res = $Obj->consulta("select sgo_dec_total from tbl_sgo_comprobanteventa where sgo_int_comprobanteventa=".$valor[0]);
        		while ($row = mysqli_fetch_array($res))
        		{
        			$total=$row["sgo_dec_total"];
        			break;
        		}
				//$saldo = ($esAgenteRetencion==1?round(($total * VALOR_RETENCION_SALDO),2):$total);
				$saldo = $total;				
				$sql = "INSERT INTO tbl_sgo_documentoporcobrar 
						(
							sgo_int_comprobanteventa,
							sgo_int_tipocomprobante,
							sgo_int_categoriacomprobante,
							sgo_vch_serie,
							sgo_vch_numero,
							sgo_int_persona, 
							sgo_dec_total, 
							sgo_dec_saldo, 
							sgo_dec_saldoporutilizar,
							sgo_int_moneda, 
							sgo_dat_fecharegistro, 
							sgo_bit_activo, 
							sgo_vch_observacion
						) 
						VALUES 
						("
							.$valor[0].","
							.$valor[1].","
							.$valor[2].",'" 
							. str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" 
							. str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "',"
							.$valor[5] . "," 
							. $total.","
							.$saldo.","
							.$total.","
							.$valor[8].",'"
							.date("Y-m-d H:i")."',
							1,'"
							.($esAgenteRetencion==1?TEXTO_DOCUMENTOXCOBRAR_AR:TEXTO_DOCUMENTOXCOBRAR)
							."'
						)";
                if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);

				/*if($esAgenteRetencion==1)
				{
					$idDXC=mysqli_insert_id($trans);
					$this->generaMovimientoRetencion($trans, $valor[5], $observacion, round(($total * VALOR_RETENCION),2), $valor[0], $idDXC);
				}*/
						  
				//Finaliza las ordenes de compra
				$this->finalizarOrdenCompra($valor[0]);
				//Finaliza las guías de remisión
				$this->finalizarGuíasRemision($valor[0]);
        	       			       
              $trans->commit();$trans->close();return $id;
         }
         catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
      }
      function finalizarGuíasRemision($prm)
      {
		  $Obj=new mysqlhelper;		  		 
		  $sql = "select sgo_int_guiaremision from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$prm;
		  $result = $Obj->consulta($sql);
		  while ($row = mysqli_fetch_array($result))
		  {     				  	
	     	$sql = "update tbl_sgo_guiaremision set sgo_int_estadoguiaremision = ". ESTADO_GR_FACTURADA ." where sgo_int_guiaremision =" . $row["sgo_int_guiaremision"];			  
			$Obj->execute($sql);			  	       
          }     	
      }
      function finalizarOrdenCompra($prm)
      {      	
      	  include('../../code/bl/bl_ordenservicio.php');
		  $blOrdenServicio = new bl_ordenservicio;
		  $Obj=new mysqlhelper;
		  $trans=$Obj->transaction();		  
		  $sql = "	select 	sgo_int_ordenservicio, sum(sgo_dec_valorventa) as total 
		  			from	tbl_sgo_comprobanteventadetalle  
		  			where 	sgo_int_comprobanteventa =".$prm." 
		  			group 	by sgo_int_ordenservicio";
		  $result = $Obj->consulta($sql);
		  while ($row = mysqli_fetch_array($result))
		  {
     		if($row["total"] == $blOrdenServicio->Obtener_MontoTotal_OrdenServicioSinFormato($row["sgo_int_ordenservicio"], $prm))
     		{
	     	  $sql = "update tbl_sgo_ordenservicio set sgo_int_estado = ".ESTADO_OS_FINALIZADA." where sgo_int_ordenservicio =" . $row["sgo_int_ordenservicio"];
			  try{	
				$Obj->execute($sql);
			  }
			  catch(Exception $e)
			  {
	  		    echo "<script>alert('Error: " . $e . "');</script>";
	            $trans->rollback();$trans->close();	          	
			  }
     		}       	
          }
          $trans->commit();$trans->close();
      }
      function Confirma_Eliminar_ComprobanteVenta($prm){
		  $Helper=new htmlhelper;
		  $Obj=new mysqlhelper;
		  $numero = "";
		  $sql = "select concat(sgo_vch_serie,'-',sgo_vch_numero) as numero from tbl_sgo_comprobanteventa where sgo_int_comprobanteventa=".$prm;
		  $result = $Obj->consulta($sql);
		  while ($row = mysqli_fetch_array($result))
		  {
            $numero=$row["numero"];
            break;
          }
  		  return $Helper->PopUp("","Confirmacion",450,htmlentities('Esta seguro de eliminar el documento ' . $numero . '?'),$Helper->button("", "Si", 70, "Operacion('facturacion','Eliminar_ComprobanteVenta','','" . $prm . "')"));
      }
	  function Confirma_Eliminar_Adelanto($prm){
		  $Helper=new htmlhelper;
  		  return $Helper->PopUp("","Confirmacion",450,htmlentities('¿Esta seguro de eliminar el adelanto? '),$Helper->button("", "Si", 70, "Operacion('facturacion','Eliminar_Adelanto','','" . $prm . "')"));
      }
	  function Eliminar_Adelanto($prm){	  	  
		  $valores = explode('|',$prm);
          $Obj=new mysqlhelper;
          $id="";
          $exportador="";
	  	  
	  	  $sql = "select sgo_dec_monto, sgo_int_comprobante from tbl_sgo_adelanto where sgo_int_codigo=" . $valores[2];
          $totalAdelanto=0;
          $result = $Obj->consulta($sql);
          while ($row = mysqli_fetch_array($result))
          {
          	$totalAdelanto+=$row["sgo_dec_monto"];
          	$id=$row["sgo_int_comprobante"];		
          }
          
          $sql="select sgo_bit_exportador from tbl_sgo_comprobanteventa where sgo_int_comprobanteventa = ".$id;
		  $resultado = $Obj->consulta($sql);
		  while ($row = mysqli_fetch_array($resultado))
		  {
		 		$exportador=$row["sgo_bit_exportador"];
		  }
		          
		  $trans=$Obj->transaction();
		  		  		  
		  $sql = "	UPDATE 	tbl_sgo_comprobanteventa 
					SET 	sgo_dec_totaladelanto = (sgo_dec_totaladelanto - ".$totalAdelanto."), 
							sgo_dec_subtotal = (sgo_dec_subtotal + ".$totalAdelanto."), 
							sgo_dec_igv = ".($exportador==0?"(sgo_dec_subtotal * ".VALOR_IGV.")":"0").", 
							sgo_dec_total = (sgo_dec_subtotal + sgo_dec_igv) 
					WHERE 	sgo_int_comprobanteventa = ".$id;
		  
		  try{
			if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
		  	$sql = "delete from tbl_sgo_adelanto where sgo_int_codigo =" . $valores[2];
			if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
			$trans->commit();$trans->close(); return 1;
		  }
		  catch(Exception $e)
		  {
  		    echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();
          	return "<script>Operacion_Result(false);</script>";
		  }
      }
      function Eliminar_ComprobanteVenta($prm){      	  
          $Obj=new mysqlhelper;	
          $trans=$Obj->transaction();	  
          //se actualiza el estado del comprobante a "ANULADO"
		  $sql = "UPDATE tbl_sgo_comprobanteventa SET sgo_int_estadocomprobante=".ESTADO_COMPROBANTE_ANULADA." WHERE sgo_int_comprobanteventa=" . $prm;		  			  	 
		  try{			
			if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
			//	Se actualiza el documento por cobrar generado a "INACTIVO"
            $sql = "UPDATE tbl_sgo_documentoporcobrar set sgo_bit_activo = ".FLAG_INACTIVO." where sgo_int_comprobanteventa =". $prm;
			if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
			//Se activa la(s) orden(es) de servicio involucradas
			$sql="	update 	tbl_sgo_ordenservicio 
					set		sgo_int_estado = ".ESTADO_OS_LISTAPARAENTREGAR."
					where		sgo_int_ordenservicio in
					(
					select 	distinct facturadet.sgo_int_ordenservicio
					from 		tbl_sgo_comprobanteventa factura
					inner		join tbl_sgo_comprobanteventadetalle facturadet
					on			factura.sgo_int_comprobanteventa = facturadet.sgo_int_comprobanteventa
					where		factura.sgo_int_comprobanteventa = ".$prm."
					)";
			if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
			//se activa la(s) guía(s) de remisión involucradas
			$sql="	update	tbl_sgo_guiaremision
					set		sgo_int_estadoguiaremision = ".ESTADO_GR_EMITIDA."
					where		sgo_int_guiaremision in
					(
						select	sgo_int_guiaremision
						from		tbl_sgo_comprobanteventadetalle
						where		sgo_int_comprobanteventa =".$prm."
					)";
			if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
			//Se eliminan los movimientos asociados a este documento
			$sql="delete from tbl_sgo_adelanto where sgo_int_comprobante=".$prm;
			if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
			//se eliminan los movimientos asociaos a este documento
			$sql="update tbl_sgo_movimiento set sgo_bit_activo = ".FLAG_INACTIVO." where sgo_int_movimiento in (select sgo_int_movimiento from tbl_sgo_movimientodetalle where sgo_int_documentoporcobraropagar in (select sgo_int_documentoporcobrar from tbl_sgo_documentoporcobrar where sgo_int_comprobanteventa=".$prm."))";
			if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
			//se eliminan los documentos por cobrar asociados
			$sql="	delete
					from		tbl_sgo_documentoporcobrardetalle
					where		sgo_int_documentoporcobrar in 
					(
						select	sgo_int_documentoporcobrar
						from	tbl_sgo_documentoporcobrar
						where	sgo_int_comprobanteventa in
						(
							select	sgo_int_comprobanteventa
							from		tbl_sgo_comprobanteventa 
							where		sgo_int_estadocomprobante = 3
						)
					)";
			if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
			$sql="	delete
					from		tbl_sgo_documentoporcobrar
					where		sgo_int_comprobanteventa in 
					(
					select	sgo_int_comprobanteventa
					from		tbl_sgo_comprobanteventa 
					where		sgo_int_estadocomprobante = 3
					)";
			if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error); 
			$trans->commit();$trans->close();
		  	return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarGuia');</script>";		  	
		  }
		  catch(Exception $e)
		  {  		    
            $trans->rollback();$trans->close();
          	return "<script>Operacion_Result(false);</script>";
		  }
      }
		
/****************************************************************FACTURAS DETALLE************************************************************************/
      function Grilla_Listar_ComprobanteVenta_Detalle($prm,$size=950,$estado=1){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$html="";$tipoComprobante=0;
         $exportador = $this->obtenerExportador($prm);
         $sql ="select	factdet.sgo_int_comprobanteventadetalle as param,
						ifnull(factdet.sgo_vch_descripcion, CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,''))) as Descripcion,
						sum(factdet.sgo_dec_cantidad) as Cantidad,
						ifnull(factdet.sgo_dec_precio,ocdet.sgo_dec_precio) as Precio,
						sum(factdet.sgo_dec_valorventa) as 'Valor Venta'
			from		tbl_sgo_comprobanteventadetalle factdet
			left 	outer	join tbl_sgo_ordenserviciodetalle ocdet
			on			factdet.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle
			left 	outer join tbl_sgo_producto pdt
			on			pdt.sgo_int_producto = ocdet.sgo_int_producto
			LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
         	on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
         	and		pdt.sgo_int_color = catprodcol.sgo_int_color
         	LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
         	on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
         	and		pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
         	LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
         	on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
         	and		pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
         	 WHERE  factdet.sgo_int_comprobanteventa =".$prm."
         	 group by pdt.sgo_int_producto";         
         $botones=new GrillaBotones;
         if($estado!=6 && $estado!=3)$botones->GrillaBotones("PopUp('facturacion','PopUp_Mant_Facturacion_Servicio','','" . $prm . "|","","PopUp('facturacion','PopUp_Mant_ComprobanteVenta_Detalle','','" . $prm . "|","PopUp('facturacion','Confirma_Eliminar_Detalle','','" . $prm . "|","");

		 $resp = explode('|',$this->obtenerAdelantos($prm));
		 $html ='<br/>';
		 $tipoComprobante = $this->obtenerTipoComprobante($prm);
		 if($tipoComprobante==2) // factura
		 {
    		 $subtotal = $this->obtenerSubtotal($prm) - $resp[1];
             if($exportador==0)
             {
                $igv = $subtotal * 0.18;
             }
             else
             {
               $igv=0;
             }
    		 $total = ($subtotal + $igv);
		 }
		 else
		 {
			 $subtotal = $this->obtenerSubtotal($prm);
			 $total = $subtotal;
			 }
		 //$total = ($total  - $resp[1]);
		 $html.='<table border="0" cellpadding="0" cellspacing="0" width="950">'.$resp[0].'<tr><td width="800px"></td>';
		 $html.='<td width="80px">Subtotal</td><td align="right">'.round($subtotal,2).'<td/></tr>';
		 if($tipoComprobante==2)
		 {
		 	$html.='<tr><td></td><td>Igv</td><td align="right">'.round($igv,2).'<td/></tr>';
		 }
		 $html.='<tr><td></td><td>Total</td><td align="right">'.round(($total),2).'<td/></tr>';
		 $html.='</table>';

         return $Helper->Crear_Grilla($Obj->consulta($sql),"",$botones,$size,array(),array(),array(),10,"").$html;
      }
	  function obtenerAdelantos($prm)
	  {
	  	 $html="";
	     $valores = explode('|',$prm);
		 $totalAdelanto = 0;
		 $soloTexto="";
		 $soloTexto2="";
		 $valorAdelanto=0;
		 $Obj=new mysqlhelper;
         $sql = "select ade.sgo_dec_monto as monto,
		 				concat(cv.sgo_vch_serie,'-',cv.sgo_vch_numero) as comprobante,
						DATE_FORMAT(cv.sgo_dat_fechaemision, '%d/%m/%Y %H:%i') as fecha
				from 	tbl_sgo_adelanto ade
				inner 	join tbl_sgo_comprobanteventa cv
				on		ade.sgo_int_comprobanteadelanto = cv.sgo_int_comprobanteventa
				where 	ade.sgo_int_comprobante =".$valores[0];

		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {
            $html = $html."<tr><td colspan='2' align='center'>Adelanto por factura ".$row["comprobante"]." del ".$row["fecha"]."</td><td align='right'>(".round($row["monto"],2).")<td/></tr>";
			$soloTexto = $soloTexto." Adelanto por factura ".$row["comprobante"]." del ".$row["fecha"]."                                                                                        (S/.".$row["monto"].")_";
			$soloTexto2 = $soloTexto2." Adelanto por factura ".$row["comprobante"]." del ".$row["fecha"]."_";
			$valorAdelanto = $row["monto"]."_".$valorAdelanto;
			$totalAdelanto = $totalAdelanto + $row["monto"];
         }

		 return $html."|".$totalAdelanto."|".$soloTexto."|".$soloTexto2."|".$valorAdelanto;

	  }
  	  function obtenerAdelantosImpresion($prm)
	  {
	  	 $html="";
	     $valores = explode('|',$prm);
		 $totalAdelanto = 0;
		 $soloTexto="";
		 $soloTexto2="";
		 $valorAdelanto=0;
		 $Obj=new mysqlhelper;
         $sql = "select sum(ade.sgo_dec_monto) as monto,
						group_concat(concat(cv.sgo_vch_serie,'-',cv.sgo_vch_numero)) as comprobante						
				from 	tbl_sgo_adelanto ade
				inner 	join tbl_sgo_comprobanteventa cv
				on		ade.sgo_int_comprobanteadelanto = cv.sgo_int_comprobanteventa
				where 	ade.sgo_int_comprobante =".$valores[0];

		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {            
			$soloTexto = " Adelanto por factura(s) ".$row["comprobante"];			
			$valorAdelanto = $row["monto"];			
         }

		 return $soloTexto."|".$valorAdelanto;

	  }
	  function obtenerFechaEmision($prm)
  	  {
	  	 $fechaEmision=0;
         $Obj=new mysqlhelper;
         $sql = "	select	sgo_dat_fechaemision
					from	tbl_sgo_comprobanteventa
					where 	sgo_int_comprobanteventa =".$prm;

		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {
            $fechaEmision=$row["sgo_dat_fechaemision"];
            break;
         }
		 return $fechaEmision;
	  }
	  function obtenerMoneda($prm)
	  {
	  	 $moneda=0;
         $Obj=new mysqlhelper;
         $sql = "	select	sgo_int_moneda
					from	tbl_sgo_comprobanteventa
					where 	sgo_int_comprobanteventa =".$prm;

		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {
            $moneda=$row["sgo_int_moneda"];
            break;
         }
		 return $moneda;
	  }
  	  function obtenerTipoCambio($fecha,$moneda)
	  {
         $Obj=new mysqlhelper;
         $sql = "	select	sgo_dec_venta, sgo_dec_compra
					from	tbl_sgo_tipocambio
					where 	sgo_dat_fecha ='".substr($fecha,0,10)."' 
					and		sgo_int_moneda=".$moneda;

		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {
            $cambio[0]=$row["sgo_dec_venta"];
            $cambio[1]=$row["sgo_dec_compra"];
            break;
         }
		 return $cambio;
	  }  	
      function obtenerExportador($prm)
      {
         $exportador=0;
         $Obj=new mysqlhelper;
         $sql = "	select	sgo_bit_exportador
					from	tbl_sgo_comprobanteventa
					where 	sgo_int_comprobanteventa =".$prm;

		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {
            $exportador=$row["sgo_bit_exportador"];
            break;
         }
		 return $exportador;
      }
	  function obtenerSubtotal($prm)
	  {
		 $Obj=new mysqlhelper;
         $sql = "	select	sum(factdet.sgo_dec_valorventa) as subtotal
					from	tbl_sgo_comprobanteventadetalle factdet
					left 	outer join tbl_sgo_ordenserviciodetalle ocdet
					on		factdet.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle
					left 	outer join tbl_sgo_producto producto
					on		producto.sgo_int_producto = ocdet.sgo_int_producto
					where 	factdet.sgo_int_comprobanteventa =".$prm;

		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {
            $subtotal=$row["subtotal"];
            break;
         }
		 return $subtotal;
	  }
	  function PopUp_Mant_Facturacion_Servicio($prm){
		 $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$valor=explode('|',$prm); $index=10;
         $precio="0";$descripcion="";
         $result = $Obj->consulta("	select sgo_vch_descripcion, sgo_dec_precio from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventadetalle =" . $valor[1]);

         while ($row = mysqli_fetch_array($result))
         {
			$precio = $row["sgo_dec_precio"];
            $descripcion=$row["sgo_vch_descripcion"];
            break;
         }
         $Val_Precio=new InputValidacion();
         $Val_Precio->InputValidacion('(DocValue("fil_txtPrecioServicio")!="" && DocValue("fil_txtPrecioServicio")!="0")','Debe especificar el precio del servicio');
		 $Val_Descripcion=new InputValidacion();
         $Val_Descripcion->InputValidacion('(DocValue("fil_txtDescripcionServicio")!="")','Debe especificar la descripcion del servicio');

         $inputs=array(
		 	"Descripcion" => $Helper->textarea("fil_txtDescripcionServicio",++$index,$descripcion,70,5,"","","","",$Val_Descripcion),
            "Valor Venta" => $Helper->textbox("fil_txtPrecioServicio",++$index,$precio,10,100,$TipoTxt->decimal,"","","","",$Val_Precio));
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_servicio",$inputs,$buttons,1,700,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Detalle",700,$html,$Helper->button("","Grabar",70,"Operacion('facturacion','Mant_ServicioFacturacion_Detalle','tbl_mant_servicio','" . $prm . "')","textoInput"));
	  }
      function PopUp_Mant_ComprobanteVenta_Detalle($prm)
      {
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$valor=explode('|',$prm); $index=0;
         $detalleguia="";$cantidad="0";$producto="";
         $result = $Obj->consulta("	select 		producto.sgo_vch_nombre, factdet.sgo_int_comprobanteventadetalle, factdet.sgo_dec_cantidad
									from		tbl_sgo_comprobanteventadetalle factdet
									inner		join tbl_sgo_ordenserviciodetalle ocdetalle
									on			factdet.sgo_int_ordenserviciodetalle = ocdetalle.sgo_int_ordenserviciodetalle
									inner		join tbl_sgo_producto producto
									on			producto.sgo_int_producto = ocdetalle.sgo_int_producto
									where		factdet.sgo_int_comprobanteventadetalle =" . $valor[1]);

         while ($row = mysqli_fetch_array($result))
         {
			$producto = $row["sgo_vch_nombre"];
            $detalleguia=$row["sgo_int_comprobanteventadetalle"];
			$cantidad=$row["sgo_dec_cantidad"];
            break;
         }
         $Val_Cantidad=new InputValidacion();
         $Val_Cantidad->InputValidacion('(DocValue("fil_txtCantidad")!="" && DocValue("fil_txtCantidad")!="0")','Debe especificar la cantidad solicitada');
         $inputs=array(
		 	"Producto" => $Helper->textbox("fil_txtCantidad",++$index,$producto,128,250,$TipoTxt->numerico,"","","","",$Val_Cantidad,true),
            "Cantidad" => $Helper->textbox("fil_txtCantidad",++$index,$cantidad,10,100,$TipoTxt->numerico,"","","","",$Val_Cantidad));
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_cdetalle",$inputs,$buttons,2,700,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Detalle",700,$html,$Helper->button("","Grabar",70,"Operacion('facturacion','Mant_ComprobanteVenta_Detalle','tbl_mant_cdetalle','" . $prm . "')","textoInput"));
      }
      function Mant_ComprobanteVenta_Detalle($prm)
      {
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
		  $sql= "UPDATE tbl_sgo_comprobanteventadetalle  SET sgo_dec_cantidad =" . $valor[3]. ", sgo_dec_valorventa = (sgo_dec_precio * ".$valor[3].") where sgo_int_comprobanteventadetalle=". $valor[1] . " and sgo_int_comprobanteventa=" . $valor[0];
          return $Obj->execute($sql);
      }
	  function Mant_ServicioFacturacion_Detalle($prm)
	  {
		  $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($valor[1]!=0)
		  {
			  $sql= "UPDATE tbl_sgo_comprobanteventadetalle SET sgo_vch_descripcion='" . $valor[2] . "',sgo_dec_precio=" . $valor[3] . ", sgo_int_cantidad = 1, sgo_dec_valorventa = ".  $valor[3] ."  WHERE sgo_int_comprobanteventadetalle=" . $valor[1];
		  }
          else
		  {
			  $sql="INSERT INTO tbl_sgo_comprobanteventadetalle (sgo_int_comprobanteventa, sgo_vch_descripcion,sgo_dec_precio, sgo_dec_valorventa) VALUES (" . $valor[0] . ",'" . $valor[2] . "'," . $valor[3] . "," . $valor[3] . ")";
		  }

		  $Obj->execute($sql);

		  //$sql2 = " UPDATE 	tbl_sgo_comprobanteventa set sgo_dec_subtotal = (select sum(sgo_dec_valorventa) from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$valor[0]."), sgo_dec_igv  = (select (sum(sgo_dec_valorventa) * 0.18) from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$valor[0]."), sgo_dec_total  = (sgo_dec_subtotal + sgo_dec_igv ) where		sgo_int_comprobanteventa = ".$valor[0]."";

		  //$resp = $Obj->execute_insert($sql2);
		  $tipo = $this->obtenerTipoComprobante($valor[0]);
          return $this->procesaTotalfactura($prm, $tipo);
	  }
	  function procesaTotalfactura($prm, $tipoComprobante,$exportador=null)
	  {
		  $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($exportador!=0 && $exportador!=1)
          {
            $exportador = $this->obtenerExportador($valor[0]);
          }

		  if($tipoComprobante=="2")
		  {
		    if($exportador==1)
            {
                $sql = "UPDATE  tbl_sgo_comprobanteventa
                    set     sgo_dec_subtotal = (select sum(sgo_dec_valorventa) from    tbl_sgo_comprobanteventadetalle where   sgo_int_comprobanteventa=".$valor[0]."),
                            sgo_dec_igv  = 0,
                            sgo_dec_total  = (sgo_dec_subtotal + sgo_dec_igv ) where		sgo_int_comprobanteventa = ".$valor[0]."";
            }
            else
            {
    		  	$sql = "UPDATE  tbl_sgo_comprobanteventa
                    set     sgo_dec_subtotal = (select sum(sgo_dec_valorventa) from    tbl_sgo_comprobanteventadetalle where   sgo_int_comprobanteventa=".$valor[0]."),
                            sgo_dec_igv  = (select (sum(sgo_dec_valorventa) * 0.18) from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$valor[0]."),
                            sgo_dec_total  = (sgo_dec_subtotal + sgo_dec_igv ) where		sgo_int_comprobanteventa = ".$valor[0]."";
            }
		  }
		  else
		  {

			  $sql = " UPDATE tbl_sgo_comprobanteventa set sgo_dec_subtotal = (select sum(sgo_dec_valorventa) from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$valor[0]."), sgo_dec_igv  = 0, sgo_dec_total  = sgo_dec_subtotal  where		sgo_int_comprobanteventa = ".$valor[0]."";
		  }

		  $resp = $Obj->execute($sql);
          return $resp;
	  }
      function Confirma_Eliminar_ComprobanteVenta_Detalle($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          return $Helper->PopUp("","Confirmacion",450,htmlentities('Esta seguro de eliminar este registro de la factura?'),$Helper->button("", "Si", 70, "Operacion('facturacion','Eliminar_ComprobanteVenta_Detalle','','" . $prm . "')"));
      }
      function Eliminar_ComprobanteVenta_Detalle($prm)
      {
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          $sql="DELETE FROM tbl_sgo_comprobanteventadetalle WHERE sgo_int_comprobanteventadetalle=" . $valor[1];
		  if($Obj->execute_insert($sql)!=-1)
		  {
			  $sql2 = " UPDATE 	tbl_sgo_comprobanteventa set sgo_dec_subtotal = (select sum(sgo_dec_valorventa) from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$valor[0]."), sgo_dec_igv  = (select (sum(sgo_dec_valorventa) * 0.18) from tbl_sgo_comprobanteventadetalle where sgo_int_comprobanteventa=".$valor[0]."), sgo_dec_total  = (sgo_dec_subtotal + sgo_dec_igv ) where		sgo_int_comprobanteventa = ".$valor[0]."";
			  $resp = $Obj->execute_insert($sql2);
		  }
          return $resp;
      }

/********************************************************NOTAS DE CREDITO**************************************************************************/
      function Filtros_Listar_NotaCredito(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Cliente" => $Helper->textbox_predictivo("fil_txtCliente",++$index,"",128,300,"","","clientes"),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Nro. Comprobante" => $Helper->textbox("fil_txtBusquedaNroComprobante",++$index,"",11,100,$TipoTxt->texto,"","","",""),
            "Estado" => $Helper->combo_estado_comprobante("fil_cmbBusquedaestadocomprobante",++$index,"",""),
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('facturacion','Grilla_Listar_NotaCredito','tbl_listarcomprobante','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarcomprobante",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_NotaCredito($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT 	co.sgo_int_comprobanteventa as param, 
         				per.sgo_vch_nrodocumentoidentidad as RUC, 
         				concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) as Cliente,
         				mon.sgo_vch_simbolo as Moneda, 
         				Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as 'Comprobante', 
         				DATE_FORMAT(co.sgo_dat_fechaemision, '%d/%m/%Y') as 'Fecha Emisión',
         				FORMAT(co.sgo_dec_subtotal,2) AS SubTotal,
         				FORMAT(co.sgo_dec_igv,2) AS Igv,
         				FORMAT(co.sgo_dec_total,2) AS Total,
         				efa.sgo_vch_descripcion as Estado,
         				(
							select	group_concat(comp.sgo_vch_serie,'-',sgo_vch_numero)
							from		tbl_sgo_comprobanteventadocreferencia compref
							inner		join tbl_sgo_comprobanteventa comp
							where		comp.sgo_int_comprobanteventa = compref.sgo_int_docreferenciado
							and		compref.sgo_int_docreferenciador = co.sgo_int_comprobanteventa
						) as 'Referencia'
                FROM tbl_sgo_comprobanteventa co
                INNER JOIN tbl_sgo_cliente prv on prv.sgo_int_cliente=co.sgo_int_cliente
                INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=prv.sgo_int_cliente
                INNER JOIN tbl_sgo_moneda mon on mon.sgo_int_moneda=co.sgo_int_moneda
                INNER JOIN tbl_sgo_estadocomprobante efa on efa.sgo_int_estadocomprobante=co.sgo_int_estadocomprobante";         	
         $where = $Obj->sql_where("WHERE concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) like '%@p1%' and (co.sgo_dat_fechaemision BETWEEN '@p2 00:00' and '@p3 23:59') Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) like '%@p4%' and co.sgo_int_estadocomprobante=@p5 and co.sgo_int_tipocomprobante=9",
                 $valor[0] . '|' . $Helper->convertir_fecha_ingles($valor[1]) . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $valor[3] . '|' . $valor[4]);
         $orderby = "ORDER BY co.sgo_int_comprobanteventa DESC";
		 $btn_extra=array("Presione aquí para imprimir." => "print.png|Operacion('facturacion','PopUp_Imprimir','','");
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"PopUp('facturacion','PopUp_Mant_NotaCredito','','","PopUp('facturacion','PopUp_Mant_NotaCredito','','","","PopUp('facturacion','Confirma_Eliminar_NotaCredito','','",null,$btn_extra,array(),array(),20,"");
      }
      function Grilla_Listar_NotaCredito_Items_DocReferencia($prm,$size=290,$id_tabla=null){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT 	co.sgo_int_comprobanteventa as param,
         				Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as Comprobante
                FROM 	tbl_sgo_comprobanteventadocreferencia ref
                INNER 	JOIN tbl_sgo_comprobanteventa co ON ref.sgo_int_docreferenciado=co.sgo_int_comprobanteventa
                INNER 	JOIN tbl_sgo_cliente prv ON prv.sgo_int_cliente=co.sgo_int_cliente";
         $where = $Obj->sql_where("WHERE ref.sgo_int_docreferenciador=@p1",$prm);
         $orderby = "ORDER BY co.sgo_int_comprobanteventa DESC";
         $botones=new GrillaBotones;
         $botones->GrillaBotones(($prm==0?"PopUp('facturacion','PopUp_Mant_DocReferencia','tbl_mant_co','":""),"","","","");
         return $Helper->Crear_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),$id_tabla,$botones,$size,array(),array(),array(),15,"")."<script>Carga_Js('../../js/jscript_facturacion.js')</script>";
      }
      function Grilla_Listar_NotaCredito_DocReferencia($prm,$size=1000){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;$objLog=new loghelper;
         $where = $Obj->sql_where("and doc.sgo_int_cliente=@p1",$valor[5]);
         $sql ="SELECT distinct doc.sgo_int_comprobanteventa as param, per.sgo_vch_nombre as 'Cliente', tco.sgo_vch_descripcion as 'Tipo Comprobante',DATE_FORMAT(doc.sgo_dat_fechaemision, '%d/%m/%Y') as 'Fecha Emisión',Concat(doc.sgo_vch_serie,'-',doc.sgo_vch_numero) as 'Comprobante', doc.sgo_dec_total as 'Total', dpp.sgo_dec_saldo as 'Pendiente', doc.sgo_vch_descripcion as 'Observacion'
                FROM tbl_sgo_comprobanteventa doc
                INNER JOIN tbl_sgo_documentoporcobrar dpp ON dpp.sgo_int_comprobanteventa=doc.sgo_int_comprobanteventa
                INNER JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=doc.sgo_int_tipocomprobante
                INNER JOIN tbl_sgo_persona per ON per.sgo_int_persona=doc.sgo_int_cliente
                WHERE doc.sgo_int_estadocomprobante = 6 ". $where;
         $objLog->log($sql);
         $botones=new GrillaBotones;
         $botones->GrillaBotones("","","","","1");
         $html= $Helper->Crear_Grilla($Obj->consulta($sql),"tbl_docreferencia",$botones,$size,array(),array(),array(),10,"");
         return $Helper->PopUp("","Documentos de Venta",1050,$html,$Helper->button("","Aceptar",70,"Agregar_NotaCredito_DocReferencia('tbl_docreferencia','tbl_docreferencia_det');Cerrar_PopUp('PopUp@')","textoInput"));
      }
      function Grilla_Listar_NotaCredito_Items($prm,$size=650,$id_grilla=null)
      {
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT fcd.sgo_int_comprobanteventadetalle as param,fcd.sgo_dec_cantidad as Cantidad, fcd.sgo_vch_descripcion as 'Descripción', fcd.sgo_dec_precio as Precio,fcd.sgo_dec_valorventa as 'Valor Venta'
                FROM tbl_sgo_comprobanteventa co
                INNER JOIN tbl_sgo_comprobanteventadetalle fcd ON co.sgo_int_comprobanteventa=fcd.sgo_int_comprobanteventa";
         $where = $Obj->sql_where("WHERE co.sgo_int_comprobanteventa=@p1",$prm);
         $orderby = "ORDER BY co.sgo_int_comprobanteventa DESC";
         $botones=new GrillaBotones;
         $botones->GrillaBotones(($prm=="0"?"Nuevo_NotaCreditoDetalle('tbl_mant_co','tbl_notacredito_det',this.parentNode)":""),"","","","");
         return $Helper->Crear_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),$id_grilla,$botones,$size) . "<script>Carga_Js('../../js/jscript_facturacion.js')</script>";
      }
      function PopUp_Mant_NotaCredito($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $cliente="";$tipo="";$categoria="";$fecha=date("d/m/Y");$serie="";$numero="";$moneda="";$obs="";$subtotal=0;$igv=0;$total=0;
         $result = $Obj->consulta("SELECT co.sgo_int_cliente,	sgo_int_tipocomprobante,sgo_int_categoriacomprobante,DATE_FORMAT(sgo_dat_fechaemision, '%d/%m/%Y') as sgo_dat_fechaemision,sgo_vch_serie,sgo_vch_numero,sgo_int_moneda,sgo_vch_descripcion,sgo_dec_subtotal,sgo_dec_igv,sgo_dec_total FROM tbl_sgo_comprobanteventa co WHERE co.sgo_int_comprobanteventa = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $cliente=$row["sgo_int_cliente"];
            $tipo=$row["sgo_int_tipocomprobante"];
            $categoria=$row["sgo_int_categoriacomprobante"];
            $fecha=$row["sgo_dat_fechaemision"];
            $serie=$row["sgo_vch_serie"];
            $numero=$row["sgo_vch_numero"];
            $moneda=$row["sgo_int_moneda"];
            $obs=$row["sgo_vch_descripcion"];
            $subtotal=$row["sgo_dec_subtotal"];
            $igv=$row["sgo_dec_igv"];
            $total=$row["sgo_dec_total"];
            break;
         }
         $grilla_docref="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_NotaCredito_Items_DocReferencia($prm,1050,"tbl_docreferencia_det") . "</div>";
         //$grilla_item="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_NotaCredito_Items($prm,650,"tbl_notacredito_det") . "</div>";
         $disabled=($prm=="0"?false:true);
         $Val_Cliente=new InputValidacion();
         $Val_Cliente->InputValidacion('DocValue("fil_cmbcliente")!=""','Debe especificar el cliente');
         $Val_TipoComprobante=new InputValidacion();
         $Val_TipoComprobante->InputValidacion('DocValue("fil_cmbtipocomprobante")!=""','Debe especificar el tipo de comprobante');
         $Val_CategoriaComprobante=new InputValidacion();
         $Val_CategoriaComprobante->InputValidacion('DocValue("fil_cmbcategoriacomprobante")!=""','Debe especificar la categoría del comprobante');
         $Val_Fecha=new InputValidacion();
         $Val_Fecha->InputValidacion('DocValue("fil_txtFechaEmision")!=""','Debe especificar la fecha de emision');
         $Val_Serie=new InputValidacion();
         $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
         $Val_Numero=new InputValidacion();
         $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
         $Val_Moneda=new InputValidacion();
         $Val_Moneda->InputValidacion('DocValue("fil_cmbmoneda")!=""','Debe especificar la moneda');
//         $Val_Total=new InputValidacion();
//         $Val_Total->InputValidacion('parseFloat(DocValue("txt_monto"))==parseFloat(DocValue("txt_total"))','El monto total de los documentos no coincide con el monto total de los items');
         $inputs=array(
            "Comprobante" => $Helper->combo_tipocomprobante("fil_cmbtipocomprobante",++$index,7,$tipo,"Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_TipoComprobante,$disabled),
            "Categoría" => $Helper->combo_categoriacomprobante("fil_cmbcategoriacomprobante",++$index,7,$categoria,"",$Val_CategoriaComprobante,$disabled),
            "Nro Comprobante" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,40,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,6,70,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
            "Cliente" => $Helper->combo_cliente("fil_cmbcliente",++$index,$cliente,"",$Val_Cliente,$disabled),
            "Moneda" => $Helper->combo_moneda_alias("fil_cmbmoneda",++$index,$moneda,"",$Val_Moneda,$disabled),
            "Fecha Emisión" => $Helper->textdate("fil_txtFechaEmision",++$index,$fecha,false,$TipoDate->fecha,80,"","",$Val_Fecha,$disabled),
            "Documentos Referencia"=>$grilla_docref . $Helper->hidden("fil_txtmonto",1000,"0x00") . $Helper->hidden("hf_idcventa",1000,"") . "~5",
            //"Items"=>$grilla_item . "~3",
            "Observaciones" => $Helper->textarea("fil_txtObservaciones",++$index,$obs,170,3,"","","","",null,$disabled) . '~5',
            "Subtotal"=>$Helper->textbox("fil_txt_subtotal",++$index,($subtotal!=""?$subtotal:0),32,100,$TipoTxt->decimal,"","document.getElementById('fil_txt_igv').value = (this.value * ".VALOR_IGV.").toFixed(2);document.getElementById('fil_txt_total').value = (parseFloat(document.getElementById('fil_txt_subtotal').value) + parseFloat(document.getElementById('fil_txt_igv').value)).toFixed(2);","","",null,($subtotal!=""?true:false)), 
            "Igv" => $Helper->textbox("fil_txt_igv",++$index,$igv,0,100,$TipoTxt->decimal,"","","","",null,true) ,
            "Total" => $Helper->textbox("fil_txt_total",++$index,$total,0,100,$TipoTxt->decimal,"","","","",null,true)
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_co",$inputs,$buttons,3,1050,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Nota de Crédito",800,$html,(!$disabled?$Helper->button("","Grabar",70,"Operacion('facturacion','Mant_NotaCredito','tbl_mant_co','" . $prm . "');","textoInput"):"")) . "<script>Obj('fil_cmbtipocomprobante').focus();</script>";
      }
      function Mant_NotaCredito($prm){
         $Obj=new mysqlhelper;$Helper= new htmlhelper;$valor=explode('|',$prm);
         $trans=$Obj->transaction();
         include_once('../../code/bl/bl_general.php'); $Obj_general=new bl_general; $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante(5);
         $valor_item=explode('~',$valor[9]);
         $valorventa=0;$valorigv=0;$valortotal=0;         
         $sql="	INSERT INTO tbl_sgo_comprobanteventa 
         		(
         			sgo_int_tipocomprobante,
         			sgo_int_categoriacomprobante,
         			sgo_vch_serie,
         			sgo_vch_numero,
         			sgo_int_cliente,
         			sgo_int_moneda,
         			sgo_dat_fechaemision,
         			sgo_vch_descripcion,
         			sgo_int_estadocomprobante,
         			sgo_dec_subtotal,
         			sgo_dec_igv,
         			sgo_dec_total
         		)
         		VALUES 
         		(" 
         			. $valor[1] . "," 
         			. $valor[2] . ",'" 
         			. str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" 
         			. str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," 
         			. $valor[5] . "," 
         			. $valor[6] . ",'" 
         			. $Helper->convertir_fecha_ingles($valor[7]) . "','" 
         			. $valor[8] . "',
         			1,"
         			. $valor[9] .","
         			. $valor[10] . ","
         			. $valor[11] ."
         			
         		)";
         try{         	  
              if($trans->query($sql))
              {
                  $id=mysqli_insert_id($trans);
                  
                  $sql="INSERT INTO tbl_sgo_documentoporpagar
                       		(
                       			sgo_int_comprobantecompra,
                       			sgo_int_persona,
                       			sgo_int_tipocomprobante,
                       			sgo_int_categoriacomprobante,
                       			sgo_vch_serie,
                       			sgo_vch_numero,
                       			sgo_dec_total,
                       			sgo_dec_saldo,
                       			sgo_int_moneda,
                       			sgo_dat_fecharegistro,
                       			sgo_bit_activo,                       			
                       			sgo_vch_nrocomprobante
                       		)
                       		VALUES
                       		(" 
                       			. $id . ","
                       			. $valor[5] . ","
                       			. $valor[1] . ","
                       			. $valor[2] . ",'"
                       			. str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" 
         						. str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," 	
         						. $valor[11] .","
         						. $valor[11] .","
         						. $valor[6] . ",'"
         						. date("Y-m-d H:i") . "',"
         						. "1,'" 
         						. str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT).'-'.str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT)."'         						
                       		)
                       		";
                       if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                       
                  //Grilla Documentos de Referencia
                  foreach ($valor_item as $k) 
                  { 
                     $valor_col=explode('_',$k);
                     if(count($valor_col)>1){
                       $sql="INSERT INTO tbl_sgo_comprobanteventadocreferencia (sgo_int_docreferenciador,sgo_int_docreferenciado,sgo_dec_monto)
                       VALUES (" . $id . "," . $valor_col[0] . "," . $valor_col[1] . ")";
                       if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);                       
                     }
                  }                  
                  if($Obj_general->Generar_Numeracion_Comprobante($valor[1],$trans)==-1)throw new Exception("Error: Generar_Numeracion_Comprobante");
              }
              else throw new Exception($sql . " => " . $trans->error);
              $trans->commit();$trans->close();return 1;
         }
         catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
      }
      function Confirma_Eliminar_NotaCredito($prm){
        $Helper=new htmlhelper;
        if($this->Obtener_Estado_Comprobante($prm)!=3){
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la nota de crédito ' . $this->Obtener_Nombre_ComprobanteVenta($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('facturacion','Eliminar_NotaCredito','','" . $prm . "')"));
        }
        else{
          return $Helper->PopUp("","Confirmación",450,htmlentities('La nota de crédito ' . $this->Obtener_Nombre_ComprobanteVenta($prm) . ' ya se encuentra anulada'),"");
        }
      }
      function Eliminar_NotaCredito($prm){
         $Obj=new mysqlhelper;$trans=$Obj->transaction();
         try{
           $sql="UPDATE tbl_sgo_comprobanteventa SET sgo_int_estadocomprobante=3 WHERE sgo_int_comprobanteventa=" . $prm;
           if($trans->query($sql))
           {
               $result = $Obj->consulta("SELECT sgo_int_docreferenciado,sgo_dec_monto FROM tbl_sgo_comprobanteventadocreferencia WHERE sgo_int_docreferenciador = " . $prm);
               while ($row = mysqli_fetch_array($result))
               {
                   $sql="UPDATE tbl_sgo_documentoporcobrar SET sgo_dec_saldo=(sgo_dec_saldo + (" . $row["sgo_dec_monto"] . "  + ( " . $row["sgo_dec_monto"] . " * ".VALOR_IGV."))) WHERE sgo_int_comprobanteventa=" . $row["sgo_int_docreferenciado"];
                   if(!$trans->query($sql))throw new Exception($sql . " => " . $trans->error);
               }
           }
           else throw new Exception($sql . " => " . $trans->error);
           $trans->commit();$trans->close();return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarComprobante');</script>";
         }
         catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return "<script>Operacion_Result(false);</script>";
//             throw new Exception("insert name again",0,$e);
         }
      }

/********************************************************NOTAS DE DEBITO**************************************************************************/
      function Filtros_Listar_NotaDebito(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Cliente" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Nro. Comprobante" => $Helper->textbox("fil_txtBusquedaNroComprobante",++$index,"",11,100,$TipoTxt->texto,"","","",""),
            "Estado" => $Helper->combo_estado_comprobante("fil_cmbBusquedaestadocomprobante",++$index,"",""),
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('facturacion','Grilla_Listar_NotaDebito','tbl_listarcomprobante','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarcomprobante",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_NotaDebito($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT co.sgo_int_comprobanteventa as param, per.sgo_vch_nrodocumentoidentidad as RUC, per.sgo_vch_nombre as Cliente,mon.sgo_vch_simbolo as Moneda, Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as 'Comprobante', DATE_FORMAT(co.sgo_dat_fechaemision, '%d/%m/%Y') as 'Fecha Emisión',FORMAT(co.sgo_dec_subtotal,2) AS SubTotal,FORMAT(co.sgo_dec_igv,2) AS Igv,FORMAT(co.sgo_dec_total,2) AS Total,efa.sgo_vch_descripcion as Estado
                FROM tbl_sgo_comprobanteventa co
                INNER JOIN tbl_sgo_cliente prv on prv.sgo_int_cliente=co.sgo_int_cliente
                INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=prv.sgo_int_cliente
                INNER JOIN tbl_sgo_moneda mon on mon.sgo_int_moneda=co.sgo_int_moneda
                INNER JOIN tbl_sgo_estadocomprobante efa on efa.sgo_int_estadocomprobante=co.sgo_int_estadocomprobante";
         $where = $Obj->sql_where("WHERE prv.sgo_int_cliente=@p1 and (co.sgo_dat_fechaemision BETWEEN '@p2 00:00' and '@p3 23:59') Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) like '%@p4%' and co.sgo_int_estadocomprobante=@p5 and co.sgo_int_tipocomprobante=10",
                 $valor[0] . '|' . $Helper->convertir_fecha_ingles($valor[1]) . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $valor[3] . '|' . $valor[4]);
         $orderby = "ORDER BY co.sgo_int_comprobanteventa DESC";
		 $btn_extra=array("Presione aquí para imprimir." => "print.png|Operacion('facturacion','PopUp_Imprimir','','");
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"PopUp('facturacion','PopUp_Mant_NotaDebito','','","PopUp('facturacion','PopUp_Mant_NotaDebito','','","","PopUp('facturacion','Confirma_Eliminar_NotaDebito','','",null,$btn_extra,array(),array(),20,"");
      }
      function Grilla_Listar_NotaDebito_Items_DocReferencia($prm,$size=290,$id_tabla=null){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT co.sgo_int_comprobanteventa as param,Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as Comprobante,ref.sgo_dec_monto as 'Monto Aplicable'
                FROM tbl_sgo_comprobanteventadocreferencia ref
                INNER JOIN tbl_sgo_comprobanteventa co ON ref.sgo_int_docreferenciador=co.sgo_int_comprobanteventa
                INNER JOIN tbl_sgo_cliente prv ON prv.sgo_int_cliente=co.sgo_int_cliente";
         $where = $Obj->sql_where("WHERE ref.sgo_int_docreferenciador=@p1",$prm);
         $orderby = "ORDER BY co.sgo_int_comprobanteventa DESC";
         $botones=new GrillaBotones;
         $botones->GrillaBotones(($prm==0?"PopUp('facturacion','PopUp_Mant_NotaDebito_DocReferencia','tbl_mant_co','":""),"","","","");
         return $Helper->Crear_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),$id_tabla,$botones,$size);
      }
      function Grilla_Listar_NotaDebito_DocReferencia($prm,$size=1000){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $where = $Obj->sql_where("and doc.sgo_int_cliente=@p1",$valor[5]);
         $sql ="SELECT doc.sgo_int_comprobanteventa as param, per.sgo_vch_nombre as 'Cliente', tco.sgo_vch_descripcion as 'Tipo Comprobante',DATE_FORMAT(doc.sgo_dat_fechaemision, '%d/%m/%Y') as 'Fecha Emisión',Concat(doc.sgo_vch_serie,'-',doc.sgo_vch_numero) as 'Comprobante', doc.sgo_dec_total as 'Total', dpp.sgo_dec_saldo as 'Pendiente', doc.sgo_vch_descripcion as 'Observacion'
                FROM tbl_sgo_comprobanteventa doc
                INNER JOIN tbl_sgo_documentoporcobrar dpp ON dpp.sgo_int_comprobanteventa=doc.sgo_int_comprobanteventa
                INNER JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=doc.sgo_int_tipocomprobante
                INNER JOIN tbl_sgo_persona per ON per.sgo_int_persona=doc.sgo_int_cliente
                WHERE 1=1 ". $where;
         $botones=new GrillaBotones;
         $botones->GrillaBotones("","","","","1");
         $html= $Helper->Crear_Grilla($Obj->consulta($sql),"tbl_docreferencia",$botones,$size);
         return $Helper->PopUp("","Documentos de Venta",1050,$html,$Helper->button("","Aceptar",70,"Agregar_NotaDebito_DocReferencia('tbl_docreferencia','tbl_docreferencia_det');Cerrar_PopUp('PopUp@')","textoInput"));
      }
      function Grilla_Listar_NotaDebito_Items($prm,$size=650,$id_grilla=null){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT fcd.sgo_int_comprobanteventadetalle as param,fcd.sgo_dec_cantidad as Cantidad, fcd.sgo_vch_descripcion as 'Descripción', fcd.sgo_dec_precio as Precio,fcd.sgo_dec_valorventa as 'Valor Venta'
                FROM tbl_sgo_comprobanteventa co
                INNER JOIN tbl_sgo_comprobanteventadetalle fcd ON co.sgo_int_comprobanteventa=fcd.sgo_int_comprobanteventa";
         $where = $Obj->sql_where("WHERE co.sgo_int_comprobanteventa=@p1",$prm);
         $orderby = "ORDER BY co.sgo_int_comprobanteventa DESC";
         $botones=new GrillaBotones;
         $botones->GrillaBotones(($prm=="0"?"Nuevo_NotaDebitoDetalle('tbl_mant_co','tbl_notadebito_det',this.parentNode)":""),"","","","");
         return $Helper->Crear_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),$id_grilla,$botones,$size) . "<script>Carga_Js('../../js/jscript_facturacion.js')</script>";
      }
      function PopUp_Mant_NotaDebito($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $cliente="";$tipo="";$categoria="";$fecha=date("d/m/Y");$serie="";$numero="";$moneda="";$obs="";$subtotal=0;$igv=0;$total=0;
         $result = $Obj->consulta("SELECT co.sgo_int_cliente,	sgo_int_tipocomprobante,sgo_int_categoriacomprobante,DATE_FORMAT(sgo_dat_fechaemision, '%d/%m/%Y') as sgo_dat_fechaemision,sgo_vch_serie,sgo_vch_numero,sgo_int_moneda,sgo_vch_descripcion,sgo_dec_subtotal,sgo_dec_igv,sgo_dec_total FROM tbl_sgo_comprobanteventa co WHERE co.sgo_int_comprobanteventa = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $cliente=$row["sgo_int_cliente"];$tipo=$row["sgo_int_tipocomprobante"];$categoria=$row["sgo_int_categoriacomprobante"];$fecha=$row["sgo_dat_fechaemision"];
            $serie=$row["sgo_vch_serie"];$numero=$row["sgo_vch_numero"];$moneda=$row["sgo_int_moneda"];$obs=$row["sgo_vch_descripcion"];
            $subtotal=$row["sgo_dec_subtotal"];$igv=$row["sgo_dec_igv"];$total=$row["sgo_dec_total"];
            break;
         }
         $grilla_docref="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_NotaDebito_Items_DocReferencia($prm,290,"tbl_docreferencia_det") . "</div>";
         $grilla_item="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_NotaDebito_Items($prm,650,"tbl_notadebito_det") . "</div>";
         $disabled=($prm=="0"?false:true);
         $Val_Cliente=new InputValidacion();
         $Val_Cliente->InputValidacion('DocValue("fil_cmbcliente")!=""','Debe especificar el cliente');
         $Val_TipoComprobante=new InputValidacion();
         $Val_TipoComprobante->InputValidacion('DocValue("fil_cmbtipocomprobante")!=""','Debe especificar el tipo de comprobante');
         $Val_CategoriaComprobante=new InputValidacion();
         $Val_CategoriaComprobante->InputValidacion('DocValue("fil_cmbcategoriacomprobante")!=""','Debe especificar la categoría del comprobante');
         $Val_Fecha=new InputValidacion();
         $Val_Fecha->InputValidacion('DocValue("fil_txtFechaEmision")!=""','Debe especificar la fecha de emision');
         $Val_Serie=new InputValidacion();
         $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
         $Val_Numero=new InputValidacion();
         $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
         $Val_Moneda=new InputValidacion();
         $Val_Moneda->InputValidacion('DocValue("fil_cmbmoneda")!=""','Debe especificar la moneda');
         //$Val_Total=new InputValidacion();
         //$Val_Total->InputValidacion('parseFloat(DocValue("txt_monto"))==parseFloat(DocValue("txt_total"))','Debe designar el total del comprobante');
         $inputs=array(
            "Comprobante" => $Helper->combo_tipocomprobante("fil_cmbtipocomprobante",++$index,8,$tipo,"Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_TipoComprobante,$disabled),
            "Categoría" => $Helper->combo_categoriacomprobante("fil_cmbcategoriacomprobante",++$index,8,$categoria,"",$Val_CategoriaComprobante,$disabled),
            "Nro Comprobante" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,40,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,6,70,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
            "Cliente" => $Helper->combo_cliente("fil_cmbcliente",++$index,$cliente,"",$Val_Cliente,$disabled),
            "Moneda" => $Helper->combo_moneda_alias("fil_cmbmoneda",++$index,$moneda,"",$Val_Moneda,$disabled),
            "Fecha Emisión" => $Helper->textdate("fil_txtFechaEmision",++$index,$fecha,false,$TipoDate->fecha,80,"","",$Val_Fecha,$disabled),
            "Documentos Referencia"=>$grilla_docref . $Helper->hidden("fil_txtmonto",1000,"0x00") . $Helper->hidden("hf_idcventa",1000,"") . "~1",
            "Items"=>$grilla_item . "~3",
            "Observaciones" => $Helper->textarea("fil_txtObservaciones",++$index,$obs,170,3,"","","","",null,$disabled) . '~5',
            " "=>$Helper->hidden("txt_igv_prc",0,"0.18") . "<div class='right'>Monto&nbsp;" . $Helper->textbox("txt_monto",$index,"0",0,100,$TipoTxt->decimal,"","","","",null,true) . "</div>",
            "  "=>"<div class='right'>SubTotal&nbsp;" .$Helper->textbox("txt_subtotal",0,$subtotal,0,100,$TipoTxt->decimal,"","","","",null,true) .
                  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IGV&nbsp;" . $Helper->textbox("txt_igv",0,$igv,0,100,$TipoTxt->decimal,"","","","",null,true) .
                  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total&nbsp;" . $Helper->textbox("txt_total",0,$total,0,100,$TipoTxt->decimal,"","","","",null,true) . '&nbsp;&nbsp;&nbsp;</div>~3',
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_co",$inputs,$buttons,3,1050,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Nota de Débito",800,$html,(!$disabled?$Helper->button("","Grabar",70,"if(parseFloat(DocValue('txt_monto'))==parseFloat(DocValue('txt_total'))){Operacion('facturacion','Mant_NotaDebito','tbl_mant_co','" . $prm . "');} else {Ver_Mensaje('Nota de Cr&eacute;dito','El monto total de los documentos no coincide con el monto total de los items');}","textoInput"):"")) . "<script>Obj('fil_cmbtipocomprobante').focus();</script>";
      }
      function Mant_NotaDebito($prm){
         $Obj=new mysqlhelper;$Helper= new htmlhelper;$valor=explode('|',$prm);
         $trans=$Obj->transaction();
         include_once('../../code/bl/bl_general.php'); $Obj_general=new bl_general; $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante(5);//Guia Entrada
         $sql="INSERT INTO tbl_sgo_comprobanteventa (sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_vch_serie,sgo_vch_numero,sgo_int_cliente,sgo_int_moneda,sgo_dat_fechaemision,sgo_vch_descripcion,sgo_int_estadocomprobante)
         VALUES (" . $valor[1] . "," . $valor[2] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[5] . "," . $valor[6] . ",'" . $Helper->convertir_fecha_ingles($valor[7]) . "','" . $valor[8] . "',1)";
         try{
              if($trans->query($sql))
              {
                  $id=mysqli_insert_id($trans);
                  $valor_item=explode('~',$valor[9]);
                  $valorventa=0;$valorigv=0;$valortotal=0;
                  foreach ($valor_item as $k) { //Grilla Documentos de Referencia
                     $valor_col=explode('_',$k);
                     if(count($valor_col)>1){
                       $sql="INSERT INTO tbl_sgo_comprobanteventadocreferencia (sgo_int_docreferenciador,sgo_int_docreferenciado,sgo_dec_monto)
                       VALUES (" . $id . "," . $valor_col[0] . "," . $valor_col[1] . ")";
                       if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                       $sql="UPDATE tbl_sgo_documentoporcobrar SET sgo_dec_total=(sgo_dec_total + " . $valor_col[1] . "),sgo_dec_saldo=(sgo_dec_saldo + " . $valor_col[1] . ")" . " WHERE sgo_int_comprobanteventa=" . $valor_col[0];
                       if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                     }
                  }
                  $valor_item=explode('~',$valor[10]);$Obj_General=new bl_general;
                  foreach ($valor_item as $k) {  // Grilla Items Comprobante
                     $valor_col=explode('_',$k);
                     $valorventa +=floatval($valor_col[3]);
                     $sql="INSERT INTO tbl_sgo_comprobanteventadetalle (sgo_int_comprobanteventa, sgo_dec_cantidad, sgo_int_producto,sgo_vch_descripcion,sgo_vch_observacion,sgo_dec_precio, sgo_dec_valorventa)
                     VALUES (" . $id . "," . $valor_col[0] . "," . (is_numeric($valor_col[1])===false?"null":$valor_col[1]) . ",'" . (is_numeric($valor_col[1])===false?$valor_col[1]:$Obj_General->Obtener_Nombre_Articulo($valor_col[1])) . "',''," . $valor_col[2] . "," . $valor_col[3] . ")";
                     if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                  }
//                  if($valor[2]=="4"){ $valorigv=$valorventa * 0.18; $valortotal=$valorventa+$valorigv; }//Factura
//                  else $valortotal=$valorventa;
                  $valorigv=$valorventa * 0.18;
                  $valortotal=$valorventa+$valorigv;
                  $sql="UPDATE tbl_sgo_comprobanteventa SET sgo_dec_subtotal=" . $valorventa . ",sgo_dec_igv=" . $valorigv . ",sgo_dec_total=" . $valortotal . " WHERE sgo_int_comprobanteventa=" . $id;
                  if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                  if($Obj_general->Generar_Numeracion_Comprobante($valor[1],$trans)==-1)throw new Exception("Error: Generar_Numeracion_Comprobante");
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
      function Confirma_Eliminar_NotaDebito($prm){
         $Helper=new htmlhelper;
         if($this->Obtener_Estado_Comprobante($prm)!=3){
           return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la nota de débito ' . $this->Obtener_Nombre_ComprobanteVenta($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('facturacion','Eliminar_NotaDebito','','" . $prm . "')"));
         }
         else{
           return $Helper->PopUp("","Confirmación",450,htmlentities('La nota de débito ' . $this->Obtener_Nombre_ComprobanteVenta($prm) . ' ya se encuentra anulada'),"");
        }
      }
      function Eliminar_NotaDebito($prm){
         $Obj=new mysqlhelper;$trans=$Obj->transaction();
         try{
           $sql="UPDATE tbl_sgo_comprobanteventa SET sgo_int_estadocomprobante=3 WHERE sgo_int_comprobanteventa=" . $prm;
           if($trans->query($sql))
           {
               $result = $Obj->consulta("SELECT sgo_int_docreferenciado,sgo_dec_monto FROM tbl_sgo_comprobanteventadocreferencia WHERE sgo_int_docreferenciador = " . $prm);
               while ($row = mysqli_fetch_array($result))
               {
                   $sql="UPDATE tbl_sgo_documentoporcobrar SET sgo_dec_total=(sgo_dec_total - " . $row["sgo_dec_monto"] . "),sgo_dec_saldo=(sgo_dec_saldo - " . $row["sgo_dec_monto"] . ") WHERE sgo_int_comprobanteventa=" . $row["sgo_int_docreferenciado"];
                   if(!$trans->query($sql))throw new Exception($sql . " => " . $trans->error);
               }
           }
           else throw new Exception($sql . " => " . $trans->error);
           $trans->commit();$trans->close();return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarComprobante');</script>";
         }
         catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return "<script>Operacion_Result(false);</script>";
//             throw new Exception("insert name again",0,$e);
         }
      }

/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
      function Obtener_Nombre_ComprobanteVenta($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as sgo_vch_nrofacturaventa FROM tbl_sgo_comprobanteventa co WHERE co.sgo_int_comprobanteventa=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_nrofacturaventa"];
          }
          return "";
      }
      function Obtener_Estado_Comprobante($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_int_estadocomprobante FROM tbl_sgo_comprobanteventa WHERE sgo_int_comprobanteventa=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_int_estadocomprobante"];
          }
          return "";
      }
      function Obtener_ArchivoImpresion_ComprobanteVenta($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT ifnull(tco.sgo_vch_archivoimpresion,'') as sgo_vch_archivoimpresion
          FROM tbl_sgo_comprobanteventa cov
          INNER JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=cov.sgo_int_tipocomprobante
          WHERE cov.sgo_int_comprobanteventa=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_archivoimpresion"];
          }
          return "";
      }
  }
?>