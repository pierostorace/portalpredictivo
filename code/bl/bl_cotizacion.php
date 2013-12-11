<?php include_once('../../code/lib/htmlhelper.php');include_once('../../code/lib/loghelper.php');include_once('../../code/lib/mailhelper.php');include_once('../../code/config/app.inc');
  Class bl_cotizacion
  {
/********************************************************COTIZACIONES**************************************************************************/
  function Filtros_Listar_Cotizaciones(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            //"Cliente" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"",""),
            "Cliente" => $Helper->textbox_predictivo("fil_txtCliente",++$index,"",128,300,"","","clientes"),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Nro. Cotizacion" => $Helper->textbox("fil_txtBusquedaNroCotizacion",++$index,"",11,100,$TipoTxt->texto,"","","",""),
            "Estado" => $Helper->combo_estado_cotizacion("fil_cmbBusquedaestadocotizacion",++$index,"",""),
         );
         $buttons=array($Helper->button("btnBuscarCotizacion","Buscar",70,"Buscar_Grilla('cotizacion','Grilla_Listar_Cotizaciones','tbl_listarcotizacion','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarcotizacion",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Cotizaciones($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $loghelper=new loghelper();
         $sql ="SELECT 	 co.sgo_int_cotizacion as param, 
			DATE_FORMAT(co.sgo_dat_fecharegistro, '%d/%m/%Y %H:%i') as 'Fecha Registro',
			per.sgo_vch_nrodocumentoidentidad as RUC, 
			concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) as Cliente, 
			Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as 'Cotización', 			
			eco.sgo_vch_descripcion as Estado,
			case co.sgo_int_estadocotizacion 
				when 2 then '|PopUp(\'cotizacion\',\'PopUp_Mant_OrdenCompra\',\'\',\'|gear.jpg' 
                when 5 then (select concat(sgo_vch_serie,'-',sgo_vch_numero) from tbl_sgo_ordenservicio where sgo_int_cotizacion=co.sgo_int_cotizacion and sgo_int_estado<>4 limit 1) 
                else '' 
            end as 'OS Generada',
			 concat(gr.sgo_vch_serie,'-',gr.sgo_vch_numero) as 'Guia Remision',
	 		 concat(vent.sgo_vch_serie,'-',vent.sgo_vch_numero) as 'Factura',
 			(select count(*) from tbl_sgo_cotizaciondetalle where sgo_int_cotizacion = co.sgo_int_cotizacion) as Items,
 			(select FORMAT(SUM(sgo_dec_cantidad * sgo_dec_precio),2) from tbl_sgo_cotizaciondetalle where sgo_int_cotizacion = co.sgo_int_cotizacion) AS Monto
          FROM tbl_sgo_cotizacion co
          INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=co.sgo_int_cliente
          INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=cli.sgo_int_cliente
          INNER JOIN tbl_sgo_estadocotizacion eco on eco.sgo_int_estadocotizacion=co.sgo_int_estadocotizacion
   		LEFT JOIN tbl_sgo_ordenservicio os on os.sgo_int_cotizacion = co.sgo_int_cotizacion
   		LEFT JOIN tbl_sgo_guiaremisiondetalle grdet on os.sgo_int_ordenservicio = grdet.sgo_int_ordenservicio
   		LEFT JOIN tbl_sgo_guiaremision gr on grdet.sgo_int_guiaremision = gr.sgo_int_guiaremision
   		LEFT JOIN tbl_sgo_comprobanteventadetalle ventdet on gr.sgo_int_guiaremision = ventdet.sgo_int_guiaremision
   		LEFT JOIN tbl_sgo_comprobanteventa vent on vent.sgo_int_comprobanteventa = ventdet.sgo_int_comprobanteventa
   		WHERE 1=1";
         if($valor[0]!="")
         {
         	//$sql.=" and cli.sgo_int_cliente=".$valor[0];
         	$sql.=" and concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) like '%".$valor[0]."%'";
         }
         if($valor[1]!="")
         {
         	$sql.=" and co.sgo_dat_fecharegistro>='".$Helper->convertir_fecha_ingles($valor[1])." 00:00'";
         }
         if($valor[2]!="")
         {
         	$sql.=" and co.sgo_dat_fecharegistro<='".$Helper->convertir_fecha_ingles($valor[2])." 23:59'";
         }
         if($valor[3]!="")
         {
         	$sql.=" and concat('001','-',LPAD(co.sgo_int_cotizacion,6,'0')) like '%".$valor[3]."%'";
         }
         if($valor[4]!="")
         {
         	$sql.=" and co.sgo_int_estadocotizacion= ".$valor[4];
         }
         
                 
         $groupby = " GROUP BY co.sgo_int_cotizacion ";
         $orderby = " ORDER BY co.sgo_int_cotizacion DESC ";
         //$loghelper->log($sql . " " . $groupby . " " . $orderby);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $groupby . " " . $orderby),"PopUp('cotizacion','PopUp_Mant_Cotizacion','','","","PopUp('cotizacion','PopUp_Mant_Cotizacion','','","PopUp('cotizacion','Confirma_Eliminar','','",null,"",array(),array(),20,"");         
      }  	  
      function PopUp_Mant_Cotizacion($prm){
         $Obj=new mysqlhelper;
		 $Helper=new htmlhelper;
		 $TipoTxt=new TipoTextBox;
		 $index=1;
         $disabled=false;$clienteguia="";$clientefactura="";$categoria="";$tipo="0";$serie=""; $numero="";$cliente="";$estado="1";$moneda="";$obs="";$subtotal="0";
		 if($prm!=0)
		 {
			 $result = $Obj->consulta("SELECT co.sgo_int_tipocomprobante,co.sgo_int_categoriacomprobante, co.sgo_vch_serie, co.sgo_vch_numero,co.sgo_int_cliente,co.sgo_int_estadocotizacion,sgo_int_moneda, sgo_vch_observacion,sgo_dec_subtotal, co.sgo_int_clienteguia, co.sgo_int_clientefactura FROM tbl_sgo_cotizacion co WHERE co.sgo_int_cotizacion = " . $prm);
			 while ($row = mysqli_fetch_array($result))
			 {
				$clientefactura=$row["sgo_int_clientefactura"];
				$clienteguia=$row["sgo_int_clienteguia"];
				$cliente=$row["sgo_int_cliente"];
				$estado=$row["sgo_int_estadocotizacion"];
            	$tipo = $row["sgo_int_tipocomprobante"];
            	$categoria = $row["sgo_int_categoriacomprobante"];
            	$serie = $row["sgo_vch_serie"];
            	$numero= $row["sgo_vch_numero"];
            	$moneda=$row["sgo_int_moneda"];
            	$obs=$row["sgo_vch_observacion"];
            	$subtotal=$row["sgo_dec_subtotal"];
            	break;
			 }
			 $disabled=($estado!=1?true:false);
		 }
		 //validaciones
         $Val_Tipo=new InputValidacion();
         $Val_Tipo->InputValidacion('DocValue("fil_cmbTipoDocumento")!=""','Debe especificar el tipo de documento');
         $Val_Categoria=new InputValidacion();
         $Val_Categoria->InputValidacion('DocValue("fil_cmbTipoDocumento")!=""','Debe especificar el tipo de documento');
         $Val_Serie=new InputValidacion();
         $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
         $Val_Numero=new InputValidacion();
         $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
         $Val_Cliente=new InputValidacion();
         $Val_Cliente->InputValidacion('DocValue("fil_cmbcliente")!=""','Debe especificar el cliente');
         $Val_ClienteFactura=new InputValidacion();
         $Val_ClienteFactura->InputValidacion('DocValue("fil_cmbclientefactura")!=""','Debe especificar el cliente');
         $Val_ClienteGuia=new InputValidacion();
         $Val_ClienteGuia->InputValidacion('DocValue("fil_cmbclienteguia")!=""','Debe especificar el cliente');
         $Val_Estado=new InputValidacion();
         $Val_Estado->InputValidacion('DocValue("fil_cmbEstadoCotizacion")!=""','Debe especificar el estado');
         $Val_Moneda=new InputValidacion();
         $Val_Moneda->InputValidacion('DocValue("fil_cmbmoneda")!=""','Debe especificar la moneda');

		 //campos de la cabecera
         $inputs=array(
            "Tipo Documento" => $Helper->combo_tipocomprobante("fil_cmbTipoDocumento",++$index,"13",$tipo,"Cargar_Objeto('general','Combo_TipoCategoria_TipoComprobante','',this.value,'fil_cmbCategoriaDocumento','panel');Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_Tipo,($prm!="0"?true:$disabled)),
            "Categoria" => $Helper->combo_categoriacomprobante("fil_cmbCategoriaDocumento",++$index,($prm=="0"?"0":"13"),$categoria,"",$Val_Categoria,($prm!="0"?true:$disabled)),
            "Nro Documento" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,40,$TipoTxt->numerico,"","","","",$Val_Serie,($prm!="0"?true:$disabled)) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,6,70,$TipoTxt->numerico,"","","","",$Val_Numero,($prm!="0"?true:$disabled)),
            "Cliente" => $Helper->combo_cliente("fil_cmbcliente",++$index,$cliente,"document.getElementById('fil_cmbclienteguia').selectedIndex = document.getElementById('fil_cmbcliente').selectedIndex;document.getElementById('fil_cmbclientefactura').selectedIndex = document.getElementById('fil_cmbcliente').selectedIndex;Recarga_Detalles('grilla_cotizacion_det');",$Val_Cliente,($prm!="0"?true:$disabled)),         	
            "Moneda" => $Helper->combo_moneda_alias("fil_cmbmoneda",++$index,$moneda,"",$Val_Moneda,($prm!="0"?true:$disabled)),
            "Estado" => $Helper->combo_estado_cotizacion("fil_cmbEstadoCotizacion",++$index,($estado==""?"1":$estado),"",$Val_Estado,($estado==1?false:true)),
         	"Cliente Guia" => $Helper->combo_cliente("fil_cmbclienteguia",++$index,$clienteguia,"",$Val_ClienteGuia,($prm!="0"?true:$disabled)),
         	"Cliente Factura" => $Helper->combo_cliente("fil_cmbclientefactura",++$index,$clientefactura,"",$Val_ClienteFactura,($prm!="0"?true:$disabled)).'~1',            
            "Observaciones" => $Helper->textarea("fil_txtObservaciones",++$index,$obs,173,3,"","","","",null,$disabled) . '~5'
         );

         $buttons=array();
		 $html = '<fieldset class="textoInput"><legend align= "left">Cabecera de la Cotizacion</legend>';
         $html .= $Helper->Crear_Layer("tbl_mant_cotizacion",$inputs,$buttons,3,1000,"","");
		 $html .= '</fieldset>';

		 if($prm!=0)
		 {
				 $html.='<br/>';
				 $html.= '<fieldset class="textoInput"><legend align= "left">Detalle de la Cotizacion</legend>';         		
				 $html.="<div id='div_ComprobanteVenta_Detalles'>" . $this->Grilla_Listar_Cotizacion_Detalle($prm,$estado) . "</div>";
				 $html.='</fieldset>';
		 }
         return $Helper->PopUp("popupfaturacion",($prm==0?"Nuevo":"Actualizar") . " Cotizacion",1000,$html,($estado==1?$Helper->button("","Grabar",70,"Operacion('cotizacion','Mant_Cotizacion','tbl_mant_cotizacion','" . $prm . "')","textoInput"):""));
      }	  	 
	  function Mant_Cotizacion($prm){
	  	 include_once('../../code/bl/bl_general.php'); $Obj_general=new bl_general;
         $Obj=new mysqlhelper;
         $valor=explode('|',$prm);
         $id=$valor[0]; 
         $Helper=new htmlhelper;                		 	
         $trans=$Obj->transaction();
         try{    		                
         	  //Subiendo el archivo         	  
         	           	  
              //el comprobante ya existe y se está realizando una actualización
              $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante($valor[1]);
              if($valor[0]!=0)
              {			  	                              	  
				  $sql="UPDATE tbl_sgo_cotizacion SET sgo_int_estadocotizacion=" . $valor[7] . ",sgo_vch_observacion='" . $valor[10] . "', sgo_int_clienteguia=".$valor[8].", sgo_int_clientefactura=".$valor[9]." WHERE sgo_int_cotizacion=" . $valor[0];
                  if($trans->query($sql))
                  {
        			  
                  }
                  else
                  { 
                  	throw new Exception($trans->error);
                  }
    		  }
              else
              {
              	  $sql="INSERT INTO tbl_sgo_cotizacion (sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_vch_serie,sgo_vch_numero,sgo_int_cliente,sgo_int_moneda,sgo_int_estadocotizacion,sgo_vch_observacion,sgo_dat_fecharegistro, sgo_int_clienteguia, sgo_int_clientefactura)
            	  VALUES (" . $valor[1] . "," . $valor[2] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[5] . "," . $valor[6] . "," . $valor[7] . ",'" . $valor[10] . "','" . date("Y-m-d H:i") . "',".$valor[8].",".$valor[9].")";              	  
              	  if($trans->query($sql))
                  {
        			  $id=mysqli_insert_id($trans);
        			  if($Obj_general->Generar_Numeracion_Comprobante($valor[1],$trans)==-1)throw new Exception("Error: Generar_Numeracion_Comprobante");
                  }
                  else
                  { 
                  	throw new Exception($trans->error);
                  }
    		  }
              $trans->commit();$trans->close();return $id;
         }
         catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
      }      
      
  	    
   function Confirma_Eliminar_Cotizacion($prm){
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la cotización ' . $this->Obtener_Nombre_Cotizacion($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('cotizacion','Eliminar_Cotizacion','','" . $prm . "')"));
      }
   function Obtener_Nombre_Cotizacion($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as sgo_vch_nrocotizacion FROM tbl_sgo_cotizacion co WHERE co.sgo_int_cotizacion=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_nrocotizacion"];
          }
          return "";
      }
  function Eliminar_Cotizacion($prm){
          $Obj=new mysqlhelper;
          if($Obj->execute("UPDATE tbl_sgo_cotizacion SET sgo_int_estadocotizacion=4 WHERE sgo_int_cotizacion=" . $prm)!=-1){
             return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarCotizacion');</script>";
          }
          else return "<script>Operacion_Result(false);</script>";
      }
/****************************************************************COTIZACIÓN DETALLE************************************************************************/
      function Grilla_Listar_Cotizacion_Detalle($prm,$estado){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$html="";$tipoComprobante=0;        
         $sql ="SELECT 	cod.sgo_int_cotizaciondetalle as param, CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as Producto, cod.sgo_dec_cantidad as Cantidad, cod.sgo_dec_precio as Precio, FORMAT((cod.sgo_dec_cantidad * cod.sgo_dec_precio),2) as 'Valor Venta'
          		FROM 	tbl_sgo_cotizacion co
          		INNER 	JOIN tbl_sgo_cliente cli 
          		on 		cli.sgo_int_cliente=co.sgo_int_cliente
          		INNER 	JOIN tbl_sgo_cotizaciondetalle cod 
          		on 		cod.sgo_int_cotizacion=co.sgo_int_cotizacion
          		INNER 	JOIN tbl_sgo_producto pdt 
          		on 		pdt.sgo_int_producto=cod.sgo_int_producto
          		LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
         		on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
         		and		pdt.sgo_int_color = catprodcol.sgo_int_color
         		LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
         		on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
         		and		pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
         		LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
         		on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
         		and		pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
           		WHERE co.sgo_int_cotizacion=".$prm . "  group by pdt.sgo_vch_nombre,catprodcol.sgo_int_color, catprodtam.sgo_int_tamano, catprodcal.sgo_int_calidad ";         		
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),($estado!="2" && $estado!="5"?"PopUp('cotizacion','PopUp_Mant_Cotizacion_Detalle','','" . $prm . "|":""),"",($estado!="2" && $estado!="5"?"PopUp('cotizacion','PopUp_Mant_Cotizacion_Detalle','','" . $prm . "|":""),($estado!="2" && $estado!="5"?"PopUp('cotizacion','Confirma_Eliminar_Detalle','','" . $prm . "|":""),1150,array(),array(),array(),20,"");
      }
      function PopUp_Mant_Cotizacion_Detalle($prm)
  	  {
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate;$valor=explode('|',$prm); $index=10;
         $cliente="";
         $producto="";
         $descripcion="";
         $precio="0";
         $cantidad=1;
         $valorventa=0;
         $observacion="";         
         $sql=" select sgo_int_producto, sgo_vch_descripcion, sgo_dec_precio, sgo_dec_cantidad, sgo_dec_valorventa, sgo_vch_observacion from tbl_sgo_cotizaciondetalle where sgo_int_cotizaciondetalle = " . $valor[1];
         $result = $Obj->consulta($sql);
         while ($row = mysqli_fetch_array($result))
         {
            $producto=$row["sgo_int_producto"];
            $descripcion=$row["sgo_vch_descripcion"];
            $precio=$row["sgo_dec_precio"];
            $cantidad=$row["sgo_dec_cantidad"];
            $valorventa=$row["sgo_dec_valorventa"];
            $observacion=$row["sgo_vch_observacion"];
            break;
         }
         $result = $Obj->consulta("SELECT sgo_int_cliente FROM tbl_sgo_cotizacion WHERE sgo_int_cotizacion =" . $valor[0]);
         while ($row = mysqli_fetch_array($result))
         {
            $cliente=$row["sgo_int_cliente"];
            break;
         }

         $Val_Producto=new InputValidacion();
         $Val_Producto->InputValidacion('DocValue("fil_cmbproducto")!=""','Debe especificar el producto');
         $Val_Precio=new InputValidacion();
         $Val_Precio->InputValidacion('DocValue("fil_txtPrecio")!=""','Debe especificar el precio del producto');
         $Val_Cantidad=new InputValidacion();
         $Val_Cantidad->InputValidacion('(DocValue("fil_txtCantidad")!="" && DocValue("fil_txtCantidad")!="0")','Debe especificar la cantidad solicitada');         
         $inputs=array(
            "Producto" => $Helper->combo_producto_cliente_cotizacion("fil_cmbproducto",++$index,$cliente,$valor[0],$producto,"Cargar_Objeto('general','Valor_Producto_Precio','fil_cmbcliente',this.value,'fil_txtPrecio','value');Cargar_Objeto('general','Valor_Producto_UnidadxBulto','fil_cmbcliente',this.value,'fil_txtUnxBulto','value');",$Val_Producto),
            "Precio" => $Helper->textbox("fil_txtPrecio",++$index,$precio,18,100,$TipoTxt->decimal,"","","","",$Val_Precio),
            "Cantidad" => $Helper->textbox("fil_txtCantidad",++$index,$cantidad,10,100,$TipoTxt->numerico,"","","","",$Val_Cantidad)."~1",						         	
			"Observacion" => $Helper->textarea("fil_txtObservacion",++$index,$observacion,110,4,"","","","","","")."~3"       	            
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_cotizaciondetalle",$inputs,$buttons,2,870,"","");
         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar")." Item",870,$html,$Helper->button("","Grabar",70,"Operacion('cotizacion','Mant_Cotizacion_Detalle','tbl_mant_cotizaciondetalle','" . $prm . "')","textoInput"));
      }
      function Mant_Cotizacion_Detalle($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[1]!=0) 
          	$sql= "UPDATE tbl_sgo_cotizaciondetalle SET sgo_int_producto=" . $valor[2] . ",sgo_dec_cantidad=" . $valor[4] . ",sgo_dec_precio=" . $valor[3] . ",sgo_dec_valorventa=" . ($valor[3] * $valor[4]) . ",sgo_vch_observacion='" . $valor[5] . "' WHERE sgo_int_cotizaciondetalle=" . $valor[1];
          else 
          	$sql="INSERT INTO tbl_sgo_cotizaciondetalle (sgo_int_cotizacion,sgo_int_producto, sgo_dec_cantidad, sgo_dec_precio,sgo_dec_valorventa, sgo_vch_observacion) VALUES (" . $valor[0] . "," . $valor[2] . "," . $valor[4] . "," . $valor[3] . "," . ($valor[3]*$valor[4]) . ",'" . $valor[5] . "')";
          return $Obj->execute($sql)."|".$valor[0];
      }   
  	  function Confirma_Eliminar_Cotizacion_Detalle($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT pdt.sgo_vch_nombre FROM tbl_sgo_cotizaciondetalle cod INNER JOIN tbl_sgo_producto pdt ON cod.sgo_int_producto=pdt.sgo_int_producto WHERE cod.sgo_int_cotizaciondetalle=" . $valor[1]);
          while ($row = mysqli_fetch_array($result))
          {
               return $Helper->PopUp("","Confirmacion",450,htmlentities('Esta seguro de eliminar el producto ' . $row["sgo_vch_nombre"] . '?'),$Helper->button("", "Si", 70, "Operacion('cotizacion','Eliminar_Cotizacion_Detalle','','" . $prm . "')"));
          }
          return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
   	  function Eliminar_Cotizacion_Detalle($prm)
      {
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          $sql="DELETE FROM tbl_sgo_cotizaciondetalle WHERE sgo_int_cotizaciondetalle=" . $valor[1];
          return $Obj->execute($sql)."|".$valor[0];
      }
  function PopUp_Mant_OrdenCompra($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;$valor=explode('|',$prm);
         $result = $Obj->consulta("SELECT count(sgo_int_cotizacion) items FROM tbl_sgo_cotizaciondetalle WHERE sgo_int_cotizacion = " . $valor[0]);
         $resp=0;
         while ($row = mysqli_fetch_array($result))
         {
            $resp=$row["items"];
            break;
         }
         if($resp==0){
            return $Helper->PopUp("","Generar Orden de Compra",350,htmlentities("La cotización debe tener al menos un Item"),"");
         }
         else{
           $cliente="";$estado="1";$ini=date("d/m/Y");$fin="";$clienteguia="";$clientefactura="";
           $result = $Obj->consulta("SELECT sgo_int_cliente, sgo_int_clienteguia, sgo_int_clientefactura FROM tbl_sgo_cotizacion WHERE sgo_int_cotizacion = " . $valor[0]);
           while ($row = mysqli_fetch_array($result))
           {
              $cliente=$row["sgo_int_cliente"];
              $clienteguia=$row["sgo_int_clienteguia"];
              $clientefactura=$row["sgo_int_clientefactura"];
              break;
           }
           $Val_Tipo=new InputValidacion();
           $Val_Tipo->InputValidacion('DocValue("fil_cmbTipoDocumento")!=""','Debe especificar el tipo de documento');
           $Val_Categoria=new InputValidacion();
           $Val_Categoria->InputValidacion('DocValue("fil_cmbTipoDocumento")!=""','Debe especificar el tipo de documento');
           $Val_Serie=new InputValidacion();
           $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
           $Val_Numero=new InputValidacion();
           $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
           $Val_Cli=new InputValidacion();
           $Val_Cli->InputValidacion('DocValue("fil_cmbcliente")!=""','Debe especificar el Cliente');
           $Val_Estado=new InputValidacion();
           $Val_Estado->InputValidacion('DocValue("fil_cmbEstadoOC")!=""','Debe especificar el estado de la orden de compra');
           $Val_IniVig=new InputValidacion();
           $Val_IniVig->InputValidacion('DocValue("fil_txtIniVigencia")!=""','Debe especificar el inicio de la fecha de entrega');
           $Val_FinVig=new InputValidacion();
           $Val_FinVig->InputValidacion('DocValue("fil_txtFinVigencia")!=""','Debe especificar el fin de la fecha de entrega');
           $Val_TipoRecepcion=new InputValidacion();
           $Val_TipoRecepcion->InputValidacion('DocValue("fil_cmbTipoRecepcion")!=""','Debe especificar el tipo de recepción');
           $inputs=array(
              "Tipo Documento" => $Helper->combo_tipocomprobante("fil_cmbTipoDocumento",++$index,"14","","Cargar_Objeto('general','Combo_TipoCategoria_TipoComprobante','',this.value,'fil_cmbCategoriaDocumento','panel');Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_Tipo,$disabled),
              "Categoria" => $Helper->combo_categoriacomprobante("fil_cmbCategoriaDocumento",++$index,($prm=="0"?"0":"14"),"","",$Val_Categoria,$disabled),
              "Nro Comprobante" => $Helper->textbox("fil_txtNroSerie",++$index,"",3,40,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,6,70,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
              "Cliente" => $Helper->combo_cliente("fil_cmbcliente",$index,$cliente,"",$Val_Cli,true),
           	  "Cliente Guia" => $Helper->combo_cliente("fil_cmbcliente",$index,$clienteguia,"","",true),
              "Cliente Factura" => $Helper->combo_cliente("fil_cmbcliente",$index,$clientefactura,"","",true),
              "OC Cliente" => $Helper->textbox("fil_txtnrooc",++$index,"",20,100,$TipoTxt->texto,"","","","",null),
              "Fecha Entrega" => "Del " . $Helper->textdate("fil_txtIniVigencia",++$index,$ini,false,$TipoDate->fecha,80,"","",$Val_IniVig) . " Al " . $Helper->textdate("fil_txtFinVigencia"  ,++$index,$fin,false,$TipoDate->fecha,80,"","",$Val_FinVig),
              "Nro Albarán" => $Helper->textbox("fil_txtalbaran",++$index,"",20,100,$TipoTxt->numerico,"","","","",null),
              "Tipo Recepción" => $Helper->combo_tiporecepcion("fil_cmbTipoRecepcion",++$index,"","",$Val_TipoRecepcion),
              "Direccion" => $Helper->combo_direccion_x_cliente("fil_cmbDireccionOC",++$index,$cliente,$dir,"")
           );
           $buttons=array();
           $html = $Helper->Crear_Layer("tbl_mant_oc",$inputs,$buttons,3,1100,"","");
           return $Helper->PopUp("","Generar Orden de Compra",1100,$html,$Helper->button("","Grabar",70,"PopUp('cotizacion','Mant_OrdenCompra','tbl_mant_oc','" . $prm . "');","textoInput")) . "<script>Focus('fil_cmbTipoDocumento')</script>";
         }
      }
   function Mant_OrdenCompra($prm){
          $Obj=new mysqlhelper;$mailhelper=new mailhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
          $trans=$Obj->transaction();
          include('../../code/bl/bl_general.php'); $Obj_general=new bl_general;$comprobante=$Obj_general->Obtener_Caracteristica_Comprobante($valor[1]);
          try{
              $correlativo=$Obj_general->Obtener_Correlativo_Comprobante($valor[1],$trans);
              if($correlativo==-1) throw new Exception("Error: Obtener_Correlativo_Comprobante");
              
              $sql="INSERT INTO tbl_sgo_ordenservicio(
              		sgo_int_cotizacion,sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_vch_serie,sgo_vch_numero,sgo_int_cliente, 
              		sgo_int_clienteguia, sgo_int_clientefactura, sgo_vch_nroordencompracliente, sgo_dat_fecharegistro,sgo_dat_fechainiciovigencia,
              		sgo_dat_fechafinvigencia,sgo_vch_albaran,sgo_int_tiporecepcion,sgo_int_direccion,sgo_int_estado)
              		VALUES(" . 
              		$valor[0] . "," . $valor[1] . ",'" . $valor[2] . "','" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[5] . "," . 
              		$valor[6] . ",".$valor[7].",'".$valor[8]."','" . date("Y-m-d H:i") . "','" . $Helper->convertir_fecha_ingles($valor[9]) . "',
              		'" . $Helper->convertir_fecha_ingles($valor[10]) . "','" . $valor[11] . "'," . $valor[12] . ($valor[13]==""?null:"," . $valor[13]) . ",1)";
              
              if($trans->query($sql))
              {
                 $id=mysqli_insert_id($trans);
                 $result=$Obj->consulta("SELECT sgo_int_producto,sgo_dec_precio,sgo_dec_cantidad,sgo_vch_observacion FROM tbl_sgo_cotizaciondetalle WHERE sgo_int_cotizacion=" . $valor[0]);
                 while ($row = mysqli_fetch_array($result))
                 {
                    $sql="	INSERT INTO tbl_sgo_ordenserviciodetalle (sgo_int_ordenservicio,sgo_int_producto,sgo_vch_descripcion,sgo_dec_precio,sgo_dec_cantidad,sgo_txt_observaciones,sgo_int_direccion) 
                    		VALUES (" . $id . "," . (is_numeric($row['sgo_int_producto'])===false?"null":$row['sgo_int_producto']) . ",'" . (is_numeric($row['sgo_int_producto'])===false?$row['sgo_int_producto']:$Obj_general->Obtener_Nombre_Articulo($row['sgo_int_producto'])) . "'," . $row['sgo_dec_precio'] . "," . $row['sgo_dec_cantidad'] . ",'" . $row['sgo_vch_observacion'] . "'," . (is_numeric($valor[13])===false?"null":$valor[13]) . ")";
                    if(!$trans->query($sql))throw new Exception($sql . " => " . $trans->error);
                 }
                 $sql="UPDATE tbl_sgo_cotizacion SET sgo_int_estadocotizacion=5 WHERE sgo_int_cotizacion=" . $valor[0];
                 if(!$trans->query($sql))throw new Exception($sql . " => " . $trans->error);
                 if($Obj_general->Generar_Numeracion_Comprobante($valor[1],$trans)==-1)throw new Exception("Error: Generar_Numeracion_Comprobante");
                 
                 $mailhelper->enviarEmail(CORREO_ADMINISTRACION, "ORDEN DE SERVICIO GENERADA: ".str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "-" . str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT), "La orden de servicio de la referencia ha sido generada desde el área comercial. Para mayor detalle haga clic <a href='http://www.galverperu.com/portal/moduls/ordenservicio/index.php?app=2'>aquí</a>");
                 
                 echo $Helper->PopUp("","Notificación",600,"Se ha generado la &Oacute;rden de Servicio \"" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "-" . str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "\"","") . "<script>$('#PopUp').overlay().close();</script>";
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
  }
?>