<?php include_once('../../code/lib/htmlhelper.php');
      include_once('../../code/lib/loghelper.php');
      include_once('../../code/config/app.inc');
  Class bl_guiaremision
  {
/********************************************************GUï¿½AS DE REMISIï¿½N**************************************************************************/
      function Filtros_Listar_Guiasremision(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            //"Cliente" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"",""),
            "Cliente" => $Helper->textbox_predictivo("fil_txtCliente",++$index,"",128,300,"","","clientes"),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Nro. OC" => $Helper->textbox("fil_txtBusquedaNroOC",++$index,"",11,100,$TipoTxt->texto,"","","",""),
			"Nro. Guia" => $Helper->textbox("fil_txtBusquedaNroGuia",++$index,"",11,100,$TipoTxt->texto,"","","",""),
			"Nro. Factura" => $Helper->textbox("fil_txtBusquedaNroFactura",++$index,"",11,100,$TipoTxt->texto,"","","",""),
            "Estado" => $Helper->combo_estado_guiaremision("fil_cmbBusquedaestadocotizacion",++$index,"4",""),

         );
         $buttons=array($Helper->button("btnBuscarGuia","Buscar",70,"Buscar_Grilla('guiaremision','Grilla_Listar_GuiasRemision','tbl_listarguia','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarguia",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_GuiasRemision($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="select 	guia.sgo_int_guiaremision as param,
         				DATE(guia.sgo_dat_fecha) as Fecha,
						concat(persona.sgo_vch_nombre,' - ',persona.sgo_vch_alias) as Cliente,
						concat(guia.sgo_vch_serie,'-',guia.sgo_vch_numero) as 'Nro. Guia',						
						(	select group_concat(distinct Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero))
							from 	tbl_sgo_ordenservicio oc
							inner join tbl_sgo_ordenserviciodetalle ocd
							on oc.sgo_int_ordenservicio = ocd.sgo_int_ordenservicio
							where ocd.sgo_int_ordenserviciodetalle in
							(
								select gdet.sgo_int_ordenserviciodetalle
								from 	tbl_sgo_guiaremisiondetalle gdet
								where	gdet.sgo_int_guiaremision  =    guia.sgo_int_guiaremision
							)) as OS,
							(select group_concat( distinct concat(factura.sgo_vch_serie,'-',factura.sgo_vch_numero))
							from 	tbl_sgo_comprobanteventa factura
							inner join tbl_sgo_comprobanteventadetalle facturadetalle
							on factura.sgo_int_comprobanteventa = facturadetalle.sgo_int_comprobanteventa
							inner	join tbl_sgo_guiaremision guiarem
							on	facturadetalle.sgo_int_guiaremision = guiarem.sgo_int_guiaremision
							where	guiarem.sgo_int_guiaremision = guia.sgo_int_guiaremision
							and		factura.sgo_int_estadocomprobante!=3
							)
							 as FACTURA,
						transportista.sgo_vch_razonsocial as Transportista,
						estado.sgo_vch_descripcion as Estado						
			from		tbl_sgo_guiaremision guia
			left		join tbl_sgo_cliente cliente
			on			guia.sgo_int_cliente = cliente.sgo_int_cliente
			inner		join tbl_sgo_persona persona
			on			persona.sgo_int_persona = cliente.sgo_int_cliente
			left		join tbl_sgo_estadoguiaremision estado
			on			estado.sgo_int_estadoguiaremision = guia.sgo_int_estadoguiaremision
			left		join tbl_sgo_transportista transportista
			on			transportista.sgo_int_transportista = guia.sgo_int_transportista";
            $where = "where 1=1";
			if($valor[0]!="")
				$where.=" and concat(persona.sgo_vch_nombre,' - ',persona.sgo_vch_alias) like '%".$valor[0]."%'";
			if($valor[1]!="" || $valor[2]!="")
         		$where .= " and (guia.sgo_dat_fecha BETWEEN '".$Helper->convertir_fecha_ingles($valor[1])." 00:00' and '".$Helper->convertir_fecha_ingles($valor[2])." 23:59')";
			if($valor[3]!="")
				$where .= " and (select group_concat(Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero))
							from 	tbl_sgo_ordenservicio oc
							inner join tbl_sgo_ordenserviciodetalle ocd
							on oc.sgo_int_ordenservicio = ocd.sgo_int_ordenservicio
							where ocd.sgo_int_ordenserviciodetalle in (select gdet.sgo_int_ordenserviciodetalle from tbl_sgo_guiaremisiondetalle gdet
							where gdet.sgo_int_guiaremision= guia.sgo_int_guiaremision)) like '%".$valor[3]."%'";
			if($valor[4]!="")
				$where .= " and concat(guia.sgo_vch_serie,'-',guia.sgo_vch_numero) like '%".$valor[4]."%'";
			if($valor[5]!="")
				$where .= " and ( select group_concat(concat(factura.sgo_vch_serie,'-',factura.sgo_vch_numero)) from tbl_sgo_comprobanteventa factura inner join tbl_sgo_comprobanteventadetalle facturadetalle on factura.sgo_int_comprobanteventa = facturadetalle.sgo_int_comprobanteventa where facturadetalle.sgo_int_ordenserviciodetalle in (select gdet.sgo_int_ordenserviciodetalle from tbl_sgo_guiaremisiondetalle gdet where gdet.sgo_int_guiaremision=guia.sgo_int_guiaremision)) like '%".$valor[5]."%'";
			if($valor[6]!="")
				$where .= " and guia.sgo_int_estadoguiaremision in (".$valor[6].")";
			else 
			{
				if($valor[0]=="" && $valor[1]=="" && $valor[2]=="" && $valor[3]=="" && $valor[4]=="" && $valor[5]=="")
					$where .= " and guia.sgo_int_estadoguiaremision = 4 ";
			}
				
				
			
         $orderby =" ORDER BY guia.sgo_int_guiaremision DESC";
		 //$btn_extra=array("Presione aquí para imprimir." => "print.png|Operacion('guiaremision','PopUp_Imprimir','','");
		 return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"PopUp('guiaremision','PopUp_Mant_GuiaRemision','','","","PopUp('guiaremision','PopUp_Mant_GuiaRemision','','","PopUp('guiaremision','Confirma_Eliminar','','",null,null, array(),array(),20,"");
      }
	  function PopUp_Imprimir($prm){  
          $ruta = $this->Obtener_ArchivoImpresion_ComprobanteVenta($prm);
          $this->emitirGuiaRemision($prm);
          if($ruta!="") 
          {
          	echo "<script>
          				BtnMouseDown('btnBuscarGuia');
          	 			Operacion_Reload('guiaremision','PopUp_Mant_GuiaRemision','',".$prm.",'popupGuiaRemision');
          				window.open('../../" . $ruta . "?prm=" . $prm . "','dumb');
          		  </script>";
          }          
          else
          { 
          	echo "<script>Ver_Mensaje('Impresi&oacute;n','No existe un formato de impresi&oacute;n configurado')</script>";
          }
      }
	  function emitirGuiaRemision($prm)
	  {
	  	  $Obj   = new mysqlhelper;          
		  $sql= "UPDATE tbl_sgo_guiaremision  SET sgo_int_estadoguiaremision =1 where sgo_int_guiaremision=". $prm;
          $Obj->execute($sql);
	  }
      function PopUp_Mant_GuiaRemision($prm){
		 include_once('../../code/bl/bl_general.php');
		 $general = new bl_general;
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $TipoDate=new TipoTextDate; $index=1;
         $clienteguia="";$precinto="";$cliente="0";$oc="";$estado="4"; $serie=""; $codigotienda=""; $nombretienda=""; $fechadespacho=""; $fechaemision=""; $numero=""; $transportista="0"; $chofer=""; $vehiculo="";$direccion=""; $motivo="";
         $result = $Obj->consulta("SELECT  co.sgo_int_cliente, co.sgo_int_clienteguia, LPAD(co.sgo_int_guiaremision,6,'0') as sgo_int_guiaremision, co.sgo_int_direccion, co.sgo_int_estadoguiaremision, co.sgo_vch_serie, co.sgo_vch_numero, co.sgo_int_transportista, cho.sgo_int_chofer, veh.sgo_int_vehiculo, co.sgo_int_motivo, DATE_FORMAT(co.sgo_dat_fechadespacho,'%d/%m/%Y') as sgo_dat_fechadespacho, DATE_FORMAT(co.sgo_dat_fecha,'%d/%m/%Y') as sgo_dat_fecha, sgo_vch_codigotienda, sgo_vch_nombretienda, sgo_vch_precinto FROM    tbl_sgo_guiaremision co left   JOIN tbl_sgo_chofer cho on co.sgo_int_chofer = cho.sgo_int_chofer left JOIN tbl_sgo_vehiculo veh on co.sgo_int_vehiculo = veh.sgo_int_vehiculo WHERE   co.sgo_int_guiaremision = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $cliente = $row["sgo_int_cliente"];
            $clienteguia=$row["sgo_int_clienteguia"];
			$oc	= $row["sgo_int_guiaremision"];
			$estado	= $row["sgo_int_estadoguiaremision"];
			$serie = $row["sgo_vch_serie"];
			$numero	= $row["sgo_vch_numero"];
			$direccion = $row["sgo_int_direccion"];
			$transportista = $row["sgo_int_transportista"];
			$chofer = $row["sgo_int_chofer"];
			$vehiculo = $row["sgo_int_vehiculo"];
			$motivo = $row["sgo_int_motivo"];
			$fechaemision = ($row["sgo_dat_fecha"]==""?date("d/m/Y"):$row["sgo_dat_fecha"]);
			$fechadespacho = ($row["sgo_dat_fechadespacho"]==""?date("d/m/Y"):$row["sgo_dat_fechadespacho"]);
			$codigotienda= $row["sgo_vch_codigotienda"];
			$nombretienda= $row["sgo_vch_nombretienda"];
            $precinto=$row["sgo_vch_precinto"];
            break;
         }
		 if($serie=="")
		 {
			$comprobante = $general->Obtener_Caracteristica_Comprobante(8);
			$serie 	= str_pad($comprobante["serie"], $comprobante["digitosserie"], "0", STR_PAD_LEFT);
			$numero = str_pad($comprobante["correlativo"], $comprobante["digitoscorrelativo"], "0", STR_PAD_LEFT);
			$fechaemision = date("d/m/Y");
			$fechadespacho = date("d/m/Y");
		 }
         $Val_Cliente=new InputValidacion();
         $Val_Cliente->InputValidacion('DocValue("fil_cmbclientePopUp")!=""','Debe especificar el cliente');
         $Val_ClienteGuia=new InputValidacion();
         $Val_ClienteGuia->InputValidacion('DocValue("fil_cmbclienteGuiaPopUp")!=""','Debe especificar el cliente de la guia');
         $Val_Direccion=new InputValidacion();
         $Val_Direccion->InputValidacion('DocValue("fil_cmbDireccionDatoCliente")!=""','Debe especificar la dirección de la guia');
         $Val_Estado=new InputValidacion();
         $Val_Estado->InputValidacion('DocValue("fil_cmbEstadoGuiaRemisionPopUp")!=""','Debe especificar el estado');
         $inputs=array(
		 	"Cliente" => $Helper->combo_cliente("fil_cmbclientePopUp",$index,$cliente,"Cargar_Combo('general','Combo_Direccion_x_Cliente','fil_cmbDireccionDatoCliente','fil_cmbclientePopUp','fil_cmbDireccionDatoCliente');document.getElementById('fil_cmbclienteGuiaPopUp').selectedIndex = document.getElementById('fil_cmbclientePopUp').selectedIndex;",""),         	         
			"Direccion" => $Helper->combo_direccion_x_cliente("fil_cmbDireccionDatoCliente",++$index,$cliente,$direccion,"",$Val_Direccion),
            "Estado" => $Helper->combo_estado_guiaremision("fil_cmbEstadoGuiaRemisionPopUp",++$index,$estado,"",$Val_Estado,true),
            "Cliente Guia" => $Helper->combo_cliente("fil_cmbclienteGuiaPopUp",$index,$clienteguia,"",""),
		 	"Serie" => $Helper->textbox("fil_txtNroSeriePU",++$index,$serie,3,100,$TipoTxt->texto,"","","","","",true),
			"Nro. Guia" => $Helper->textbox("fil_txtNroGuiaPU",++$index,$numero,8,100,$TipoTxt->texto,"","","","", "", true),
			"Transportista" => $Helper->combo_transportista("fil_cmbTransportistaPopUp",++$index,"",$transportista,"Cargar_Combo('general','Combo_Chofer_x_Transportista','fil_cmbChoferPopUp','fil_cmbTransportistaPopUp','fil_cmbChoferPopUp');Cargar_Combo('general','Combo_Vehiculo_x_Transportista','fil_cmbVehiculo','fil_cmbTransportistaPopUp','fil_cmbVehiculo');",""),
			"Chofer" => $Helper->combo_chofer_x_transportista("fil_cmbChoferPopUp",++$index,$transportista,$chofer,"","",""),
			"Vehiculo" => $Helper->combo_vehiculo_x_transportista("fil_cmbVehiculo",++$index,$transportista,$vehiculo,"","",""),
			"Motivo" => $Helper->combo_motivo_traslado("fil_cmbMotivoTrasladoPopUp",++$index,"",$motivo,"","",250),
			"Despacho" =>  $Helper->textdate("fil_txtFechaDespacho",++$index,$fechadespacho,false,$TipoDate->fecha,80,"",""),
            "Precinto" => $Helper->textbox("fil_txtPrecinto",++$index,$precinto,128,100,$TipoTxt->texto,"","","",""),
         	"Emisión" =>  $Helper->textdate("fil_txtFechaEmision",++$index,$fechaemision,false,$TipoDate->fecha,80,"",""),
			);
         $buttons=array();
		 $html = '<fieldset class="textoInput"><legend align= "left">Cabecera de la Guia de Remision</legend>';
         $html .= $Helper->Crear_Layer("tbl_mant_guiaremision",$inputs,$buttons,3,800,"","");
		 $html .= '</fieldset>';


         if($prm!=0)
		 {
			 $html.='<br/>';
			 $html.= '<fieldset class="textoInput"><legend align= "left">Detalle de la Guia de Remision</legend>';
     		 $html.='<table id="tbl_detalleguia_cabecera" border="0" cellpadding="3" cellspacing="0" style="width:790px;" class="textoInput"><tbody><tr>';
		     if($estado==4)
		     {
     		 $html.='<tr><td style="padding-left:5px;width:50px;">O/C</td><td style="width:150px;">' . $Helper->combo_ordenservicio_cliente("fil_cmbOCPopUpDetalle",1,$cliente,"","Cargar_Objeto('guiaremision','Combo_OS_ordencompra','',this.value, 'dvComboTienda', 'panel');","").'</td><td style="width:250px;"><div id="dvComboTienda"></div></td><td>'.$Helper->button("","Cargar Productos",130,"Operacion('guiaremision','Carga_DetalleGuiaRemision','tbl_detalleguia_cabecera','" . $prm . "')","textoInput").'</td><td><div id="dvTotalOS"></div></td></tr>';
		     }
		     $html.='<tr><td colspan="4" height="10px"></td></tr>';
			 $html.='</tbody></table>';
			 $html.="<div id='div_GuiaRemision_Detalles'>" . $this->Grilla_Listar_GuiaRemision_Detalle($prm,950,$estado) . "</div>";
			 $html.='</fieldset>';
		 }

         return $Helper->PopUp("popupGuiaRemision",($prm==0?"Nueva":"Actualizar") . " Guia de Remision",950,$html,($estado==4 ?$Helper->button("","Grabar",70,"Operacion('guiaremision','Mant_GuiaRemision','tbl_mant_guiaremision','" . $prm . "')","textoInput")."  ".($estado==4 && $prm!=0?$Helper->button("","Imprimir",70,"Operacion('guiaremision','PopUp_Imprimir','','" . $prm . "')","textoInput"):""):""));
      }
	  function Cargar_Chofer_Transportista($prm){
		 $Obj=new mysqlhelper;$resp="";
		 $sql = "SELECT sgo_vch_chofer FROM tbl_sgo_transportistadetalle where sgo_int_transportista =".$prm." limit 1";
		 $result = $Obj->consulta($sql);

		 while ($row = mysqli_fetch_array($result))
		 {
            $resp=$row["sgo_vch_chofer"];
			break;
         }
		 return $resp;
	  }
	  function Cargar_Licencia_Transportista($prm){
		 $Obj=new mysqlhelper;
		 $resp="";
		 $sql = "SELECT sgo_vch_licencia as licencia FROM tbl_sgo_transportistadetalle where sgo_int_transportista =".$prm." limit 1";
		 $result = $Obj->consulta($sql);
		 while ($row = mysqli_fetch_array($result))
		 {
            $resp=$row["licencia"];
            break;
         }
		 return $resp;
	  }
	  function Cargar_DatosTransportista_Transportista($prm){
		 $Obj=new mysqlhelper;$valor="";
		 $sql = "SELECT sgo_vch_informaciontransp FROM tbl_sgo_transportistadetalle where sgo_int_transportista =".$prm." limit 1";
		 $result = $Obj->consulta($sql);
		 while ($row = mysqli_fetch_array($result))
		 {
            $valor=$row["sgo_vch_informaciontransp"];
            break;
         }
		 return $valor;
	  }
	  function Cargar_Orden_Servicio($prm){
		 $Helper=new htmlhelper;$tipoRecepcion="0";$Obj=new mysqlhelper;
		 $sql = "SELECT sum((sgo_dec_cantidad*sgo_dec_precio)) as total FROM tbl_sgo_ordenserviciodetalle where sgo_int_ordenservicio =".$prm;
		 $result = $Obj->consulta($sql);
		 while ($row = mysqli_fetch_array($result))
		 {
            $resp=$row["total"];
            break;
         }
		 return "Total OS: S/. ".number_format($resp,2);
	  }
      function Cargar_Combo_OrdenCompraCliente($prm)      {
         $Helper=new htmlhelper;
	    return $Helper->combo_ordenservicio_cliente_ordencompracliente("fil_os_galver",2,$prm,"","Cargar_Objeto('guiaremision','Carga_TotalOrdenServicio','',this.value, 'dvTotalOS', 'panel')");
      }
	  function Cargar_Combo_Tienda($prm){
		 $Helper=new htmlhelper;$tipoRecepcion="0";$Obj=new mysqlhelper;
		 $sql = "SELECT sgo_int_tiporecepcion FROM tbl_sgo_ordenservicio where sgo_int_ordenservicio =".$prm;
		 $result = $Obj->consulta($sql);
		 while ($row = mysqli_fetch_array($result))
		 {
            $tipoRecepcion=$row["sgo_int_tiporecepcion"];
            break;
         }
		 if($tipoRecepcion!="1")
		 {
			 return $Helper->combo_direccion_x_cliente_x_os("fil_tienda",2,$prm,"","");
		 }
	  }
	  function Carga_DetalleGuiaRemision($prm){
		  $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($valor[0]!=0 && $valor[2]==0)//seleccionï¿½ solo la Orden de Compra
		  {
			  $sql= "insert into tbl_sgo_guiaremisiondetalle
					(sgo_int_guiaremision, sgo_vch_item, sgo_vch_descripcion, sgo_dec_cantidad, sgo_int_ordenserviciodetalle, sgo_int_ordenservicio,sgo_int_totalbultos)
					select		".$valor[0].",
								ocdet.sgo_vch_item,
								CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')),
								ifnull((
									select	ocdet.sgo_dec_cantidad - sum(a.sgo_dec_cantidad)
									from 		tbl_sgo_guiaremision g
									inner		join tbl_sgo_guiaremisiondetalle a
									on			g.sgo_int_guiaremision = a.sgo_int_guiaremision
									where		a.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle
									and			g.sgo_int_estadoguiaremision!=3
									group 	by a.sgo_int_guiaremisiondetalle
								),ocdet.sgo_dec_cantidad),
								ocdet.sgo_int_ordenserviciodetalle,
								ocdet.sgo_int_ordenservicio,
                                ocdet.sgo_int_unidadesporbulto
					from		tbl_sgo_ordenserviciodetalle ocdet
					inner		join tbl_sgo_producto pdt
					on			pdt.sgo_int_producto = ocdet.sgo_int_producto
					LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
			         on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
			         and	pdt.sgo_int_color = catprodcol.sgo_int_color
			         LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
			         on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
			         and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
			         LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
			         on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
			         and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
					where		ocdet.sgo_int_ordenservicio in (select sgo_int_ordenservicio from tbl_sgo_ordenservicio where sgo_vch_nroordencompracliente = (select sgo_vch_nroordencompracliente from tbl_sgo_ordenservicio where sgo_int_ordenservicio =".$valor[1]."))
					and			ifnull((
									select		ocdet.sgo_dec_cantidad - sum(a.sgo_dec_cantidad)
									from 		tbl_sgo_guiaremision g
									inner		join tbl_sgo_guiaremisiondetalle a
									on			g.sgo_int_guiaremision = a.sgo_int_guiaremision
									where		a.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle
									and			g.sgo_int_estadoguiaremision!=3
									group 	by a.sgo_int_guiaremisiondetalle
								),ocdet.sgo_dec_cantidad) > 0  limit 30";
		  }
		  else
		  {
			  $sql= "insert into tbl_sgo_guiaremisiondetalle
					(sgo_int_guiaremision, sgo_vch_item, sgo_vch_descripcion, sgo_dec_cantidad, sgo_int_ordenserviciodetalle, sgo_int_ordenservicio,sgo_int_totalbultos)
					select		".$valor[0].",
								ocdet.sgo_vch_item,
								CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')),
								ifnull((
									select	ocdet.sgo_dec_cantidad - sum(a.sgo_dec_cantidad)
									from 		tbl_sgo_guiaremision g
									inner		join tbl_sgo_guiaremisiondetalle a
									on			g.sgo_int_guiaremision = a.sgo_int_guiaremision
									where		a.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle
									and			g.sgo_int_estadoguiaremision!=3
									group 	by  a.sgo_int_ordenserviciodetalle
								),ocdet.sgo_dec_cantidad),
								ocdet.sgo_int_ordenserviciodetalle,
								ocdet.sgo_int_ordenservicio,
                                                                 ocdet.sgo_int_unidadesporbulto
					from		tbl_sgo_ordenserviciodetalle ocdet
					inner		join tbl_sgo_producto pdt
					on			pdt.sgo_int_producto = ocdet.sgo_int_producto
					LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
			         on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
			         and	pdt.sgo_int_color = catprodcol.sgo_int_color
			         LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
			         on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
			         and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
			         LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
			         on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
			         and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
					where		ocdet.sgo_int_ordenservicio = ".$valor[2]."
					and			ifnull((
									select	ocdet.sgo_dec_cantidad - sum(a.sgo_dec_cantidad)
									from 		tbl_sgo_guiaremision g
									inner		join tbl_sgo_guiaremisiondetalle a
									on			g.sgo_int_guiaremision = a.sgo_int_guiaremision
									where		a.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle
									and			g.sgo_int_estadoguiaremision!=3
									group 	by 	a.sgo_int_ordenserviciodetalle
								),ocdet.sgo_dec_cantidad) > 0 limit 30";
			  }
		  return $Obj->execute_insert($sql);
	  }
	  function obtieneDireccionGuiaRemision($prm)	  {
		 $Obj=new mysqlhelper;
		 $direccion = 0;
		 $sql = "SELECT sgo_int_direccion FROM tbl_sgo_guiaremision where sgo_int_guiaremision =".$prm;
		 $result = $Obj->consulta($sql);
		 while ($row = mysqli_fetch_array($result))
		 {
            $direccion=$row["sgo_int_direccion"];
            break;
         }
		 return $direccion;
	  }
      function Mant_GuiaRemision($prm){
          $Obj=new mysqlhelper;$trans=$Obj->transaction();$valor=explode('|',$prm);$id=0;
		  include_once('../../code/bl/bl_general.php');
		  $general = new bl_general;
		  $Helper=new htmlhelper;
		  if($valor[2]=="")$valor[2]=0;
		  if($valor[6]=="")$valor[6]=0;
          if($valor[7]=="")$valor[7]=0;
          if($valor[8]=="")$valor[8]=0;
		  try
		  {
			  if($valor[0]!=0)
			  {
				  $sql= "UPDATE tbl_sgo_guiaremision SET 
				  		sgo_int_cliente=".$valor[1].", 
				  		sgo_int_direccion=".$valor[2].", 
				  		sgo_int_estadoguiaremision=".$valor[4].",
				  		sgo_int_clienteguia=".$valor[3].",  
				  		sgo_vch_serie='".$valor[5]."',
						sgo_vch_numero='".$valor[6]."',". 						
				  		($valor[7]!=0?"sgo_int_transportista=".$valor[7].",":"").
				  		($valor[8]!=0?"sgo_int_chofer=".$valor[8].",":"").
				  		($valor[9]!=0?"sgo_int_vehiculo=".$valor[9].",":"")." 
				  		sgo_int_motivo = ".$valor[10].", 
				  		sgo_dat_fechadespacho='".$Helper->convertir_fecha_ingles($valor[11])."', 
				  		sgo_vch_precinto = '" . $valor[12] . "', 
				  		sgo_dat_fecha='".$Helper->convertir_fecha_ingles($valor[13])."'
						WHERE sgo_int_guiaremision=".$valor[0];

				  if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
			  }
			  else
			  {
					$sql="INSERT INTO tbl_sgo_guiaremision 
						 (
						 	sgo_int_cliente, 
						 	sgo_int_direccion, 
						 	sgo_int_estadoguiaremision, 
						 	sgo_int_clienteguia, 
						 	sgo_vch_serie, 
						 	sgo_vch_numero, 
						 	".($valor[7]!=0?"sgo_int_transportista,":"")." 
						 	".($valor[8]!=0?"sgo_int_chofer,":"")." 
						 	".($valor[9]!=0?"sgo_int_vehiculo,":"")." 
						 	sgo_int_motivo, 						 	
						 	sgo_dat_fechadespacho, 
						 	sgo_vch_precinto,
						 	sgo_dat_fecha
						 )
						VALUES 
						("
							.$valor[1].","
							.$valor[2].","
							.$valor[4].","
							.$valor[3].",'"
							.$valor[5]."','"
							.$valor[6]."',"
							.($valor[7]!=0?$valor[7].",":"")
							.($valor[8]!=0?$valor[8].",":"")
							.($valor[9]!=0?$valor[9].",":"")
							.$valor[10].",'"							
							.$Helper->convertir_fecha_ingles($valor[11])."','" 
							. $valor[12] ."','"
							.$Helper->convertir_fecha_ingles($valor[13])."')";

				   if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
				   $id = mysqli_insert_id($trans);
				   $general->Generar_Numeracion_Comprobante(8,$trans);
			  }
			  $trans->commit();$trans->close(); return $id;
		  }
		  catch(Exception $e)
          {
		     echo "<script>alert('Error: " . $e . "');</script>";
             $trans->rollback();$trans->close();return -1;
		  }
      }
      function Confirma_Eliminar_GuiaRemision($prm){
		  $Helper=new htmlhelper;
		  $Obj=new mysqlhelper;
		  $numero = "";
		  $sql = "select concat(sgo_vch_serie,'-',sgo_vch_numero) as numero from tbl_sgo_guiaremision where sgo_int_guiaremision=".$prm;
		  $result = $Obj->consulta($sql);
		  while ($row = mysqli_fetch_array($result))
		  {
            $numero=$row["numero"];
            break;
          }
  		  return $Helper->PopUp("","Confirmacion",450,htmlentities('Esta seguro de eliminar la guia de remision ' . $numero . '?'),$Helper->button("", "Si", 70, "Operacion('guiaremision','Eliminar_GuiaRemision','','" . $prm . "')"));
      }
      function Eliminar_GuiaRemision($prm){
          $Obj=new mysqlhelper;
          if($Obj->execute("UPDATE tbl_sgo_guiaremision SET sgo_int_estadoguiaremision=3 WHERE sgo_int_guiaremision=" . $prm)!=-1)
		  {
             return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarGuia');</script>";
          }
          else return "<script>Operacion_Result(false);</script>";
      }

/****************************************************************ORDEN COMPRA DETALLE************************************************************************/
      function Grilla_Listar_GuiaRemision_Detalle($prm,$size=950,$estado=null)
      {
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $codigo="";
         $sql ="select	guiadet.sgo_int_guiaremisiondetalle as param,
							pdt.sgo_int_producto as Codigo,
							case when 
								case
									when pdt.sgo_vch_textoimpresion is null or pdt.sgo_vch_textoimpresion='' then CONCAT(ifnull(pdt.sgo_vch_nombre,''),' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,''))
									else pdt.sgo_vch_textoimpresion
								end ='' then guiadet.sgo_vch_descripcion
							else
								case
									when pdt.sgo_vch_textoimpresion is null or pdt.sgo_vch_textoimpresion='' then CONCAT(ifnull(pdt.sgo_vch_nombre,''),' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,''))
									else pdt.sgo_vch_textoimpresion
								end
							end as Descripcion,
							sum(guiadet.sgo_dec_cantidad) as Cantidad,
							ocdet.sgo_dec_precio as Precio,
							round(sum((guiadet.sgo_dec_cantidad * ocdet.sgo_dec_precio)),2) as Total,
							sum(guiadet.sgo_int_totalbultos) as Bultos
				from		tbl_sgo_guiaremisiondetalle guiadet
				left		join tbl_sgo_ordenserviciodetalle ocdet
				on			guiadet.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle
				left		join tbl_sgo_producto pdt
				on			pdt.sgo_int_producto = ocdet.sgo_int_producto
				LEFT		JOIN tbl_sgo_categoriaproductocolor catprodcol
         		on			pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
         		and			pdt.sgo_int_color = catprodcol.sgo_int_color
         		LEFT		JOIN tbl_sgo_categoriaproductotamano catprodtam
         		on			pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
         		and			pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
         		LEFT		JOIN tbl_sgo_categoriaproductocalidad catprodcal
         		on			pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
         		and			pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
				";
         $where = $Obj->sql_where(" WHERE guiadet.sgo_int_guiaremision = @p1",$prm);
         $result = $Obj->consulta($sql." ".$where);
          while ($row = mysqli_fetch_array($result))
          {
          	$codigo = $row["Codigo"];	
          }
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where." group by case when case when pdt.sgo_vch_textoimpresion is null or pdt.sgo_vch_textoimpresion='' then CONCAT(ifnull(pdt.sgo_vch_nombre,''),' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) else pdt.sgo_vch_textoimpresion end ='' then guiadet.sgo_vch_descripcion else pdt.sgo_int_producto end order	by case when case when pdt.sgo_vch_textoimpresion is null or pdt.sgo_vch_textoimpresion='' then CONCAT(ifnull(pdt.sgo_vch_nombre,''),' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) else pdt.sgo_vch_textoimpresion end ='' then guiadet.sgo_int_guiaremisiondetalle else pdt.sgo_int_producto end"),"PopUp('guiaremision','PopUp_Mant_Guia_Servicio','','" . $prm . "|","",($codigo!=""?($estado!=2?"PopUp('guiaremision','PopUp_Mant_GuiaRemision_Detalle','','" . $prm . "|":""):"PopUp('guiaremision','PopUp_Mant_Guia_Servicio','','" . $prm . "|"),($estado!=2?"PopUp('guiaremision','Confirma_Eliminar_Detalle','','" . $prm . "|":""),$size,array(),array(),array(),10,"");
      }
  	  function Mant_ServicioGuia_Detalle($prm)
	  {
		  $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($valor[1]!=0)
		  {
			  $sql= "UPDATE tbl_sgo_guiaremisiondetalle SET sgo_vch_descripcion='" . str_replace("<br>","",$valor[2]) . "',sgo_dec_cantidad=" . $valor[3] . " WHERE sgo_int_guiaremisiondetalle=" . $valor[1];
		  }
          else
		  {
			  $sql="INSERT INTO tbl_sgo_guiaremisiondetalle (sgo_int_guiaremision, sgo_vch_descripcion,sgo_dec_cantidad) VALUES (" . $valor[0] . ",'" . str_replace("<br>","",$valor[2]) . "'," . $valor[3] . ")";
		  }

		  $Obj->execute($sql);
		  
          return 1;
	  }
      function PopUp_Mant_GuiaRemision_Detalle($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$valor=explode('|',$prm); $index=10;
         $detalleguia="";$cantidad="0";$producto="";
         $result = $Obj->consulta("	select 		producto.sgo_vch_nombre, guiadet.sgo_int_guiaremisiondetalle, guiadet.sgo_dec_cantidad, guiadet.sgo_int_totalbultos
									from		tbl_sgo_guiaremisiondetalle guiadet
									inner		join tbl_sgo_ordenserviciodetalle ocdetalle
									on			guiadet.sgo_int_ordenserviciodetalle = ocdetalle.sgo_int_ordenserviciodetalle
									inner		join tbl_sgo_producto producto
									on			producto.sgo_int_producto = ocdetalle.sgo_int_producto
									where		guiadet.sgo_int_guiaremisiondetalle =" . $valor[1]);

         while ($row = mysqli_fetch_array($result))
         {
			$producto = $row["sgo_vch_nombre"];
            $detalleguia=$row["sgo_int_guiaremisiondetalle"];$cantidad=$row["sgo_dec_cantidad"];
			$bultos =$row["sgo_int_totalbultos"];
            break;
         }
         $Val_Cantidad=new InputValidacion();
         $Val_Cantidad->InputValidacion('(DocValue("fil_txtCantidad")!="" && DocValue("fil_txtCantidad")!="0")','Debe especificar la cantidad solicitada');
         $inputs=array(
		 	"Producto" => $Helper->textbox("fil_txtCantidad",++$index,$producto,128,250,$TipoTxt->numerico,"","","","",$Val_Cantidad,true),
            "Cantidad" => $Helper->textbox("fil_txtCantidad",++$index,$cantidad,10,100,$TipoTxt->numerico,"","","","",$Val_Cantidad),
			"Bultos" => $Helper->textbox("fil_txtBultos",++$index,$bultos,10,100,$TipoTxt->numerico,"","","","","")
			);
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_cdetalle",$inputs,$buttons,2,600,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Detalle",600,$html,$Helper->button("","Grabar",70,"Operacion('guiaremision','Mant_GuiaRemision_Detalle','tbl_mant_cdetalle','" . $prm . "')","textoInput"));
      }
      function Mant_GuiaRemision_Detalle($prm){
          //$log   = new loghelper;
          $Obj   = new mysqlhelper;
          $valor = explode('|',$prm);
          //$log->log($valor[4]."_". $valor[3]);
         // $log->log($valor[3]);
          //$log->log($valor[1]);
          //$log->log($valor[0]);
          //$log->log($valor[2]);

		  $sql= "UPDATE tbl_sgo_guiaremisiondetalle  SET sgo_dec_cantidad =" . $valor[3]. ", sgo_int_totalbultos=".$valor[4]." where sgo_int_guiaremisiondetalle=". $valor[1] . " and sgo_int_guiaremision=" . $valor[0];
          return $Obj->execute($sql);
      }
      function Confirma_Eliminar_GuiaRemision_Detalle($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT pdt.sgo_vch_nombre FROM tbl_sgo_guiaremisiondetalle guiadet inner join tbl_sgo_ordenserviciodetalle ocdet on guiadet.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle INNER JOIN tbl_sgo_producto pdt ON ocdet.sgo_int_producto=pdt.sgo_int_producto WHERE guiadet.sgo_int_guiaremisiondetalle=" . $valor[1]);
          while ($row = mysqli_fetch_array($result))
          {
               return $Helper->PopUp("","Confirmaciï¿½n",450,htmlentities('ï¿½Estï¿½ seguro de eliminar el producto ' . $row["sgo_vch_nombre"] . ' de la guï¿½a de remisiï¿½n?'),$Helper->button("", "Si", 70, "Operacion('guiaremision','Eliminar_GuiaRemision_Detalle','','" . $prm . "')"));
          }
          return $Helper->PopUp("","Atenciï¿½n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaciï¿½n'),"");
      }
      function Eliminar_GuiaRemision_Detalle($prm)
      {
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          $sql="DELETE FROM tbl_sgo_guiaremisiondetalle WHERE sgo_int_guiaremisiondetalle=" . $valor[1];
          return $Obj->execute($sql);
      }
	  function Obtener_ArchivoImpresion_ComprobanteVenta($prm){
          return "impresion/guias/guiaremisionsalida_galver.php";
      }
  	  function PopUp_Mant_Guia_Servicio($prm){
		 $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$valor=explode('|',$prm); $index=10;
         $cantidad="0";$descripcion="";
         $result = $Obj->consulta("	select sgo_vch_descripcion, sgo_dec_cantidad from tbl_sgo_guiaremisiondetalle where sgo_int_guiaremisiondetalle =" . $valor[1]);

         while ($row = mysqli_fetch_array($result))
         {
			$cantidad = $row["sgo_dec_cantidad"];
            $descripcion=$row["sgo_vch_descripcion"];
            break;
         }
         $Val_Precio=new InputValidacion();
         $Val_Precio->InputValidacion('(DocValue("fil_txtPrecioServicio")!="" && DocValue("fil_txtPrecioServicio")!="0")','Debe especificar la cantidad del servicio');
		 $Val_Descripcion=new InputValidacion();
         $Val_Descripcion->InputValidacion('(DocValue("fil_txtDescripcionServicio")!="")','Debe especificar la descripcion del servicio');

         $inputs=array(
		 	"Descripcion" => $Helper->textarea("fil_txtDescripcionServicio",++$index,$descripcion,70,5,"","","","",$Val_Descripcion),
            "Cantidad" => $Helper->textbox("fil_txtPrecioServicio",++$index,$cantidad,10,100,$TipoTxt->decimal,"","","","",$Val_Precio));
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_servicio",$inputs,$buttons,1,700,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Detalle",700,$html,$Helper->button("","Grabar",70,"Operacion('guiaremision','Mant_ServicioGuia_Detalle','tbl_mant_servicio','" . $prm . "')","textoInput"));
	  }
  }
?>