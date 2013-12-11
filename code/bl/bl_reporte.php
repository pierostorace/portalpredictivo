<?php include_once('../../code/lib/htmlhelper.php');include_once('../../code/lib/loghelper.php');include_once('../../code/config/app.inc');
  Class bl_reporte
  {
/********************************************************FACTURAS**************************************************************************/
      function Filtros_Listar_Reporte_Ventas(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $inputs=array(
            "Cliente" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"","Cargar_Combo('general','Combo_Direccion_x_Cliente','fil_cmboDireccionXCliente','fil_cmbBusquedacliente','fil_cmboDireccionXCliente');Cargar_Combo('general','Combo_Producto_x_Cliente','fil_cmbproducto','fil_cmbBusquedacliente','fil_cmbproducto');"),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Tienda" => $Helper->combo_direccion_x_cliente("fil_cmboDireccionXCliente", ++$index, "", "", ""),
         	"Producto"=> $Helper->combo_producto("fil_cmbproducto", ++$index, "", ""),
         	"Reporte"=>$Helper->combo_reporte_tiporeporteventa("fil_cmbtiporeporte", ++$index, "250px", "", "")
         );
         $buttons=array($Helper->button("btnBuscarGuia","Buscar",70,"Buscar_Grilla('reporte','Grilla_Listar_ReporteVenta','tbl_listarfacturas','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarfacturas",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Reporte_Ventas($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
		 include_once ("../../code/lib/loghelper.php");$log=new loghelper;
         if($valor[5]==1) //general
         {
         	$sql = "select	distinct sum(vent.sgo_dec_total) as param, 
							concat(per.sgo_vch_nombre,'-',per.sgo_vch_alias) as Cliente,						
							format(sum((vent.sgo_dec_subtotal)),2) as Subtotal, 
							format((sum((vent.sgo_dec_igv))),2) as Igv, 
							format(sum(vent.sgo_dec_total),2) as Total
					from		tbl_sgo_comprobanteventa vent				
					inner		join tbl_sgo_persona per
					on			vent.sgo_int_cliente = per.sgo_int_persona				
					inner		join tbl_sgo_comprobanteventadetalle ventdet
					on			vent.sgo_int_comprobanteventa = ventdet.sgo_int_comprobanteventa
					where 	vent.sgo_int_estadocomprobante = 6
					and 	vent.sgo_int_tipocomprobante in (1,2) 
					and 	vent.sgo_int_categoriacomprobante in (2,3)";
         	if($valor[0]!="")
         	{
         		$sql.=" and vent.sgo_int_cliente=".$valor[0];
         	}
         	if($valor[1]!="")
         	{
         		$sql.=" and vent.sgo_dat_fechaemision >= '".$Helper->convertir_fecha_ingles($valor[1])." 00:00'" ;
         	}    
         	if($valor[2]!="")
         	{
         		$sql.=" and vent.sgo_dat_fechaemision <= '".$Helper->convertir_fecha_ingles($valor[2])." 23:59'" ;
         	}    
         	if($valor[3]!="") //tienda
         	{
         		$sql.=" and ventdet.sgo_int_detalle in (
		         										select 	sgo_int_detalle 
		         										from 	tbl_sgo_comprobanteventa         										
         												)";
         	}
         	if($valor[4]!="")
         	{
         		$sql.=" and ventdet.sgo_int_producto = ".$valor[4];
         	}
			$sql.="	GROUP 	BY vent.sgo_int_cliente
					order		by sum(vent.sgo_dec_total) desc";
         }
         if($valor[5]==3) //detallado
         {
         	$sql = "select	vent.sgo_dec_total as param, 
							concat(per.sgo_vch_nombre,'-',per.sgo_vch_alias) as Cliente,
							concat(vent.sgo_vch_serie,'-',vent.sgo_vch_numero) as 'Nro. Documento',
							format(vent.sgo_dec_subtotal,2) as Subtotal, 
							format(vent.sgo_dec_igv,2) as Igv, 
							format(vent.sgo_dec_total,2) as Total
					from		tbl_sgo_comprobanteventa vent				
					inner		join tbl_sgo_persona per
					on			vent.sgo_int_cliente = per.sgo_int_persona									
					where 	vent.sgo_int_estadocomprobante = 6 
					and 	vent.sgo_int_tipocomprobante in (1,2) 
					and 	vent.sgo_int_categoriacomprobante in (2,3)";
         	if($valor[0]!="")
         	{
         		$sql.=" and vent.sgo_int_cliente=".$valor[0];
         	}
         	if($valor[1]!="")
         	{
         		$sql.=" and vent.sgo_dat_fechaemision >= '".$Helper->convertir_fecha_ingles($valor[1])." 00:00'" ;
         	}    
         	if($valor[2]!="")
         	{
         		$sql.=" and vent.sgo_dat_fechaemision <= '".$Helper->convertir_fecha_ingles($valor[2])." 23:59'" ;
         	}    
         	if($valor[4]!="")
         	{
         		$sql.=" and ventdet.sgo_int_producto = ".$valor[4];
         	}
			$sql.="	GROUP 	BY vent.sgo_int_comprobanteventa
					order	by vent.sgo_int_comprobanteventa asc";
         }
		 
         	//$log->log($sql);
         	$result=$Obj->consulta($sql);
         	$totalGeneral=0;
         	
         	while($fila = mysqli_fetch_array($result))
         	{
         		$totalGeneral+=$fila["param"];	
         	}
         	
		 return $Helper->Imprimir_Grilla($Obj->consulta($sql),"","","","",null,array(),array(),array(),20,"Total Ventas: S/.".number_format($totalGeneral,2));
      }
  }
?>