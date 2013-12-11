<?php include_once('../../code/lib/htmlhelper.php'); include_once('../../code/bl/bl_general.php');include_once('../../code/config/app.inc');
  Class bl_compras
  {
/********************************************************COMPROBANTES DE COMPRA**************************************************************************/
      function Filtros_Listar_Compras(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Proveedor" => $Helper->combo_proveedor("fil_cmbBusquedaproveedor",$index,"",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Tipo Comprobante" => $Helper->combo_tipocomprobante("fil_cmbBusquedatipocomprobante",$index,2,"",""),
            "Nro. Comprobante" => $Helper->textbox("fil_txtBusquedaNroComprobante",++$index,"",11,100,$TipoTxt->texto,"","","",""),
            "Estado" => $Helper->combo_estado_comprobante("fil_cmbBusquedaestadocomprobante",++$index,"",""),
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('compras','Grilla_Listar_Compras','tbl_listarcomprobante','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarcomprobante",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Compras($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT co.sgo_int_comprobantecompra as param, per.sgo_vch_nrodocumentoidentidad as RUC, per.sgo_vch_nombre as Proveedor,mon.sgo_vch_simbolo as Moneda, Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as 'Comprobante', DATE_FORMAT(co.sgo_dat_fechaemision, '%d/%m/%Y') as 'Fecha Emisión',FORMAT(co.sgo_dec_subtotal,2) AS SubTotal,FORMAT(co.sgo_dec_igv,2) AS Igv,FORMAT(co.sgo_dec_total,2) AS Total,efa.sgo_vch_descripcion as Estado
                FROM tbl_sgo_comprobantecompra co
                INNER JOIN tbl_sgo_proveedor prv on prv.sgo_int_proveedor=co.sgo_int_proveedor
                INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=prv.sgo_int_proveedor
                INNER JOIN tbl_sgo_moneda mon on mon.sgo_int_moneda=co.sgo_int_moneda
                INNER JOIN tbl_sgo_estadocomprobante efa on efa.sgo_int_estadocomprobante=co.sgo_int_estadocomprobante";
         $where = $Obj->sql_where("WHERE prv.sgo_int_proveedor=@p1 and (co.sgo_dat_fechaemision BETWEEN '@p2 00:00' and '@p3 23:59') and sgo_int_tipocomprobante=@p4 and Concat(LPAD(co.sgo_int_serie,3,'0'),'-',LPAD(co.sgo_int_numero,6,'0')) like '%@p5%' and co.sgo_int_estadocomprobante=@p6",
                 $valor[0] . '|' . $Helper->convertir_fecha_ingles($valor[1]) . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $valor[3] . '|' . $valor[4]. '|' . $valor[5]);
         $orderby = "ORDER BY co.sgo_int_comprobantecompra DESC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"PopUp('compras','PopUp_Mant_Compras','','","PopUp('compras','PopUp_Mant_Compras','','","","PopUp('compras','Confirma_Eliminar','','",null,array(),array(),array(),20,"");
      }
      function Grilla_Listar_Compras_Items($prm,$size=1050,$id_grilla=null){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT fcd.sgo_int_comprobantecompradetalle as param,fcd.sgo_dec_cantidad as Cantidad, fcd.sgo_vch_descripcion as 'Descripcion', fcd.sgo_vch_observacion as 'Observacion',fcd.sgo_dec_precio as Precio,fcd.sgo_dec_valorcompra as 'Valor Compra'
                FROM tbl_sgo_comprobantecompra co
                INNER JOIN tbl_sgo_comprobantecompradetalle fcd ON co.sgo_int_comprobantecompra=fcd.sgo_int_comprobantecompra";
         $where = $Obj->sql_where("WHERE co.sgo_int_comprobantecompra=@p1",$prm);
         $orderby = "ORDER BY co.sgo_int_comprobantecompra DESC";
         $botones=new GrillaBotones;
         $botones->GrillaBotones(($prm=="0"?"Nuevo_ComprobanteDetalle('tbl_mant_co','grilla_compras_det',this.parentNode)":""),"","","","");
         return $Helper->Crear_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),$id_grilla,$botones,$size,array(),array(),array(),20,"") . "<script>Carga_Js('../../js/jscript_compras.js')</script>";
      }
      function PopUp_Mant_Compras($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $proveedor="";$tipo="";$categoria="";$fecha=date("d/m/Y");$serie="";$numero="";$moneda="";$obs="";$subtotal=0;$igv=0;$total=0;$formapago=0;$fechaEst=date("d/m/Y");
         $result = $Obj->consulta("	SELECT 	co.sgo_int_tipoformapago, 
         									co.sgo_dat_fechapagoproyectado, 
         									co.sgo_int_proveedor,	
         									sgo_int_tipocomprobante,
         									sgo_int_categoriacomprobante,
         									DATE_FORMAT(sgo_dat_fechaemision, '%d/%m/%Y') as sgo_dat_fechaemision,
         									sgo_vch_serie,sgo_vch_numero,
         									sgo_int_moneda,
         									sgo_vch_observaciones,
         									sgo_dec_subtotal,
         									sgo_dec_igv,sgo_dec_total 
         							FROM 	tbl_sgo_comprobantecompra co 
         							WHERE 	co.sgo_int_comprobantecompra = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $proveedor=$row["sgo_int_proveedor"];
            $tipo=$row["sgo_int_tipocomprobante"];
            $categoria=$row["sgo_int_categoriacomprobante"];
            $fecha=$row["sgo_dat_fechaemision"];
            $serie=$row["sgo_vch_serie"];
            $numero=$row["sgo_vch_numero"];
            $moneda=$row["sgo_int_moneda"];
            $obs=$row["sgo_vch_observaciones"];
            $subtotal=$row["sgo_dec_subtotal"];
            $igv=$row["sgo_dec_igv"];
            $total=$row["sgo_dec_total"];
            $fechaEst=$row["sgo_dat_fechapagoproyectado"];
            $formapago=$row["sgo_int_tipoformapago"];
            break;
         }
         $grilla="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_Compras_Items($prm,1050,"grilla_compras_det") . "</div>";
         $disabled=($prm=="0"?false:true);
         $Val_Proveedor=new InputValidacion();
         $Val_Proveedor->InputValidacion('DocValue("fil_cmbproveedor")!=""','Debe especificar el proveedor');
         $Val_TipoComprobante=new InputValidacion();
         $Val_TipoComprobante->InputValidacion('DocValue("fil_cmbtipocomprobante")!=""','Debe especificar el tipo de comprobante');
         $Val_CategoriaComprobante=new InputValidacion();
         $Val_CategoriaComprobante->InputValidacion('DocValue("fil_cmbCategoriaDocumento")!=""','Debe especificar la categoría del comprobante');
         $Val_Fecha=new InputValidacion();
         $Val_Fecha->InputValidacion('DocValue("fil_txtFechaEmision")!=""','Debe especificar la fecha de emision');
         $Val_FechaEst=new InputValidacion();
         $Val_FechaEst->InputValidacion('DocValue("fil_txtFechaProyectada")!=""','Debe especificar la fecha proyectada de pago');
         $Val_Serie=new InputValidacion();
         $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
         $Val_Numero=new InputValidacion();
         $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
         $Val_Moneda=new InputValidacion();
         $Val_Moneda->InputValidacion('DocValue("fil_cmbmoneda")!=""','Debe especificar la moneda');
         $inputs=array(
            "Comprobante" => $Helper->combo_tipocomprobante("fil_cmbtipocomprobante",++$index,2,$tipo,"Cargar_Objeto('general','Combo_TipoCategoria_TipoComprobante','',this.value,'fil_cmbCategoriaDocumento','panel');",$Val_TipoComprobante,$disabled),
            "Categoría" => $Helper->combo_categoriacomprobante("fil_cmbCategoriaDocumento",++$index,"",$categoria,"Valida_Categoria('grilla_compras_det')",$Val_CategoriaComprobante,$disabled),
            "Nro Comprobante" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,40,$TipoTxt->texto,"","","","",$Val_Serie,$disabled) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,18,70,$TipoTxt->texto,"","","","",$Val_Numero,$disabled),
            "Proveedor" => $Helper->combo_proveedor("fil_cmbproveedor",++$index,$proveedor,"",$Val_Proveedor,$disabled),
            "Moneda" => $Helper->combo_moneda_alias("fil_cmbmoneda",++$index,$moneda,"",$Val_Moneda,$disabled),
            "Fecha Emisión" => $Helper->textdate("fil_txtFechaEmision",++$index,$fecha,false,$TipoDate->fecha,80,"","",$Val_Fecha,$disabled),
         	"Forma de Pago" => $Helper->combo_tipoformapago("fil_cmbFormaPago",++$index,$formapago,""),
         	"Fecha de Pago (estimada)" => $Helper->textdate("fil_txtFechaProyectada",++$index,$fechaEst,false,$TipoDate->fecha,80,"","",$Val_FechaEst,$disabled)."~4",
            "Observaciones" => $Helper->textarea("fil_txtObservaciones",++$index,$obs,170,3,"","","","",null,$disabled) . '~5',
            "Items"=>$grilla . "~5",            
            " "=>$Helper->hidden("txt_igv_prc",0,"0.18"),
            "  "=>"<div class='right'>SubTotal&nbsp;" . $Helper->textbox("txt_subtotal",0,$subtotal,0,100,$TipoTxt->decimal,"","","","",null,true) .
                  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IGV&nbsp;" . $Helper->textbox("txt_igv",0,$igv,0,100,$TipoTxt->decimal,"","","","",null,true) .
                  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total&nbsp;" . $Helper->textbox("txt_total",0,$total,0,100,$TipoTxt->decimal,"","","","",null,true) . '&nbsp;&nbsp;&nbsp;</div>~3',
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_co",$inputs,$buttons,3,1050,"","");
         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Comprobante Compra",800,$html,(!$disabled?$Helper->button("","Grabar",70,"Operacion('compras','Mant_Compras','tbl_mant_co','" . $prm . "')","textoInput"):"")) . "<script>Obj('fil_cmbtipocomprobante').focus();</script>";
      }
      function Mant_Compras($prm){
         $Obj=new mysqlhelper;$Helper= new htmlhelper;$valor=explode('|',$prm);
         $trans=$Obj->transaction();
         include_once('../../code/bl/bl_general.php'); $Obj_general=new bl_general; 
         $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante(5);//Guia Entrada
         $sql="	INSERT INTO tbl_sgo_comprobantecompra 
         		(
         			sgo_int_tipocomprobante,
         			sgo_int_categoriacomprobante,
         			sgo_vch_serie,
         			sgo_vch_numero,
         			sgo_int_proveedor,
         			sgo_int_moneda,
         			sgo_dat_fechaemision,
         			sgo_int_tipoformapago,
         			sgo_dat_fechapagoproyectado,
         			sgo_vch_observaciones,
         			sgo_int_estadocomprobante
         		)
         		VALUES 
         		(" . 
         			$valor[1] . "," . 
         			$valor[2] . ",'" . 
         			str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . 
         			str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . 
         			$valor[5] . "," . 
         			$valor[6] . ",'" . 
         			$Helper->convertir_fecha_ingles($valor[7]) . "',".
         			$valor["8"].",'".
         			$Helper->convertir_fecha_ingles($valor["9"])."','". 
         			$valor["10"] . "',
         			1)";
         try{
              if($trans->query($sql))
              {
                  $id=mysqli_insert_id($trans);
                  $valor_item=explode('~',$valor[11]);
                  $valorcompra=0;$valorigv=0;$valortotal=0;
                  include('../../code/bl/bl_kardex.php'); $Obj_kardex=new bl_kardex;$Obj_General=new bl_general;
                  foreach ($valor_item as $k) {
                     $valor_col=explode('_',$k);
                     $valorcompra +=floatval($valor_col[4]);
                     $sql="	INSERT INTO tbl_sgo_comprobantecompradetalle 
                     		(
                     			sgo_int_comprobantecompra, 
                     			sgo_dec_cantidad, 
                     			sgo_int_producto,
                     			sgo_vch_descripcion,
                     			sgo_vch_observacion,
                     			sgo_dec_precio, 
                     			sgo_dec_valorcompra
                     		)
                     		VALUES 
                     		(" 
                     			. $id . "," 
                     			. $valor_col[0] . "," 
                     			. (is_numeric($valor_col[1])===false?"null":$valor_col[1]) . ",'" 
                     			. (is_numeric($valor_col[1])===false?$valor_col[1]:$Obj_General->Obtener_Nombre_Articulo($valor_col[1])) . "','" 
                     			. $valor_col[2] . "'," 
                     			. $valor_col[3] . "," 
                     			. $valor_col[4] . "
                     		)";
                     			
                     if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);                     
                     
                     /*$sql="	UPDATE 	tbl_sgo_comprobantecompra 
                     		SET 	sgo_dec_subtotal=" . $valorcompra . ",
                     				sgo_dec_igv=" . $valorigv . ",
                     				sgo_dec_total=" . $valortotal . " 
                     		WHERE 	sgo_int_comprobantecompra=" . $id;
                     
                     if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);*/
                  }
                  if($valor[1]=="4")
                  { 
                  	$valorigv=$valorcompra * VALOR_IGV; 
                  	$valortotal=$valorcompra+$valorigv; 
                  }//Factura
                  else 
                  {
                  	$valortotal=$valorcompra;
                  }
                  
                  $sql="	UPDATE 	tbl_sgo_comprobantecompra 
                  			SET 	sgo_dec_subtotal=" . $valorcompra . ",
                  					sgo_dec_igv=" . $valorigv . ",
                  					sgo_dec_total=" . $valortotal . " 
                  			WHERE 	sgo_int_comprobantecompra=" . $id;
                  
                  if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                  
                  $sql="	INSERT INTO tbl_sgo_documentoporpagar 
                  			(
                  				sgo_int_comprobantecompra,
                  				sgo_int_persona,
                  				sgo_int_tipocomprobante,
                  				sgo_vch_nrocomprobante,
                  				sgo_dec_total,
                  				sgo_dec_saldo,
                  				sgo_int_moneda,
                  				sgo_dat_fecharegistro,
                  				sgo_vch_observacion,
                  				sgo_bit_activo
                  			)
                  			VALUES 
                  			(" . 
                  				$id . "," . 
                  				$valor[5] . "," . 
                  				$valor[1] . ",'" . 
                  				str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "-" . str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . 
                  				$valortotal . "," . 
                  				$valortotal . "," . 
                  				$valor[6] . ",'" . 
                  				date("Y-m-d H:i") . "','" . 
                  				$valor[10] . "',
                  				1
                  			)";
                  if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
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
      function Confirma_Eliminar_Compras($prm){
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el comprobante de compra ' . $this->Obtener_Nombre_ComprobanteCompra($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('compras','Eliminar_Compras','','" . $prm . "')"));
      }
      function Eliminar_Compras($prm){
          $Obj=new mysqlhelper;
          if($Obj->execute("UPDATE tbl_sgo_comprobantecompra SET sgo_int_estadocomprobante=3 WHERE sgo_int_comprobantecompra=" . $prm)!=-1){
             return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarComprobante');</script>";
          }
          else return "<script>Operacion_Result(false);</script>";
      }

/********************************************************NOTAS DE CREDITO**************************************************************************/
      function Filtros_Listar_NotaCredito(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Proveedor" => $Helper->combo_proveedor("fil_cmbBusquedaproveedor",$index,"",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Nro. Comprobante" => $Helper->textbox("fil_txtBusquedaNroComprobante",++$index,"",11,100,$TipoTxt->texto,"","","",""),
            "Estado" => $Helper->combo_estado_comprobante("fil_cmbBusquedaestadocomprobante",++$index,"",""),
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('compras','Grilla_Listar_NotaCredito','tbl_listarcomprobante','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarcomprobante",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_NotaCredito($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT co.sgo_int_comprobantecompra as param, per.sgo_vch_nrodocumentoidentidad as RUC, per.sgo_vch_nombre as Proveedor,mon.sgo_vch_simbolo as Moneda, Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as 'Comprobante', DATE_FORMAT(co.sgo_dat_fechaemision, '%d/%m/%Y') as 'Fecha Emisión',FORMAT(co.sgo_dec_subtotal,2) AS SubTotal,FORMAT(co.sgo_dec_igv,2) AS Igv,FORMAT(co.sgo_dec_total,2) AS Total,efa.sgo_vch_descripcion as Estado
                FROM tbl_sgo_comprobantecompra co
                INNER JOIN tbl_sgo_proveedor prv on prv.sgo_int_proveedor=co.sgo_int_proveedor
                INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=prv.sgo_int_proveedor
                INNER JOIN tbl_sgo_moneda mon on mon.sgo_int_moneda=co.sgo_int_moneda
                INNER JOIN tbl_sgo_estadocomprobante efa on efa.sgo_int_estadocomprobante=co.sgo_int_estadocomprobante";
         $where = $Obj->sql_where("WHERE prv.sgo_int_proveedor=@p1 and (co.sgo_dat_fechaemision BETWEEN '@p2 00:00' and '@p3 23:59') Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) like '%@p4%' and co.sgo_int_estadocomprobante=@p5 and co.sgo_int_tipocomprobante=11",
                 $valor[0] . '|' . $Helper->convertir_fecha_ingles($valor[1]) . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $valor[3] . '|' . $valor[4]);
         $orderby = "ORDER BY co.sgo_int_comprobantecompra DESC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"PopUp('compras','PopUp_Mant_NotaCredito','','","PopUp('compras','PopUp_Mant_NotaCredito','','","","PopUp('compras','Confirma_Eliminar_NotaCredito','','",null,array(),array(),array(),20,"");
      }
      function Grilla_Listar_NotaCredito_Items_DocReferencia($prm,$size=290,$id_tabla=null){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT co.sgo_int_comprobantecompra as param,Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as Comprobante,ref.sgo_dec_monto as 'Monto Aplicable'
                FROM tbl_sgo_comprobantecompradocreferencia ref
                INNER JOIN tbl_sgo_comprobantecompra co ON ref.sgo_int_docreferenciador=co.sgo_int_comprobantecompra
                INNER JOIN tbl_sgo_proveedor prv ON prv.sgo_int_proveedor=co.sgo_int_proveedor";
         $where = $Obj->sql_where("WHERE ref.sgo_int_docreferenciador=@p1",$prm);
         $orderby = "ORDER BY co.sgo_int_comprobantecompra DESC";
         $botones=new GrillaBotones;
         $botones->GrillaBotones(($prm==0?"PopUp('compras','PopUp_Mant_DocReferencia','tbl_mant_co','":""),"","","","");
         return $Helper->Crear_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),$id_tabla,$botones,$size,array(),array(),array(),20,"");
      }
      function Grilla_Listar_NotaCredito_DocReferencia($prm,$size=1000){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $where = $Obj->sql_where("and doc.sgo_int_proveedor=@p1",$valor[5]);
         $sql ="SELECT doc.sgo_int_comprobantecompra as param, per.sgo_vch_nombre as 'Proveedor', tco.sgo_vch_descripcion as 'Tipo Comprobante',DATE_FORMAT(doc.sgo_dat_fechaemision, '%d/%m/%Y') as 'Fecha Emisión',Concat(doc.sgo_vch_serie,'-',doc.sgo_vch_numero) as 'Comprobante', doc.sgo_dec_total as 'Total', dpp.sgo_dec_saldo as 'Pendiente', doc.sgo_vch_observaciones as 'Observacion'
                FROM tbl_sgo_comprobantecompra doc
                INNER JOIN tbl_sgo_documentoporpagar dpp ON dpp.sgo_int_comprobantecompra=doc.sgo_int_comprobantecompra
                INNER JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=doc.sgo_int_tipocomprobante
                INNER JOIN tbl_sgo_persona per ON per.sgo_int_persona=doc.sgo_int_proveedor
                WHERE dpp.sgo_dec_saldo!=0 ". $where;
         $botones=new GrillaBotones;
         $botones->GrillaBotones("","","","","1");
         $html= $Helper->Crear_Grilla($Obj->consulta($sql),"tbl_docreferencia",$botones,$size,array(),array(),array(),20,"");
         return $Helper->PopUp("","Documentos de compra",1050,$html,$Helper->button("","Aceptar",70,"Agregar_NotaCredito_DocReferencia('tbl_docreferencia','tbl_docreferencia_det');Cerrar_PopUp('PopUp@')","textoInput"));
      }
      function Grilla_Listar_NotaCredito_Items($prm,$size=650,$id_grilla=null){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT fcd.sgo_int_comprobantecompradetalle as param,fcd.sgo_dec_cantidad as Cantidad, fcd.sgo_vch_descripcion as 'Descripción', fcd.sgo_dec_precio as Precio,fcd.sgo_dec_valorcompra as 'Valor Compra'
                FROM tbl_sgo_comprobantecompra co
                INNER JOIN tbl_sgo_comprobantecompradetalle fcd ON co.sgo_int_comprobantecompra=fcd.sgo_int_comprobantecompra";
         $where = $Obj->sql_where("WHERE co.sgo_int_comprobantecompra=@p1",$prm);
         $orderby = "ORDER BY co.sgo_int_comprobantecompra DESC";
         $botones=new GrillaBotones;
         $botones->GrillaBotones(($prm=="0"?"Nuevo_NotaCreditoDetalle('tbl_mant_co','tbl_notacredito_det',this.parentNode)":""),"","","","");
         return $Helper->Crear_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),$id_grilla,$botones,$size,array(),array(),array(),20,"") . "<script>Carga_Js('../../js/jscript_compras.js')</script>";
      }
      function PopUp_Mant_NotaCredito($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $proveedor="";$tipo="";$categoria="";$fecha=date("d/m/Y");$serie="";$numero="";$moneda="";$obs="";$subtotal=0;$igv=0;$total=0;
         $result = $Obj->consulta("SELECT co.sgo_int_proveedor,	sgo_int_tipocomprobante,sgo_int_categoriacomprobante,DATE_FORMAT(sgo_dat_fechaemision, '%d/%m/%Y') as sgo_dat_fechaemision,sgo_vch_serie,sgo_vch_numero,sgo_int_moneda,sgo_vch_observaciones,sgo_dec_subtotal,sgo_dec_igv,sgo_dec_total FROM tbl_sgo_comprobantecompra co WHERE co.sgo_int_comprobantecompra = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $proveedor=$row["sgo_int_proveedor"];$tipo=$row["sgo_int_tipocomprobante"];$categoria=$row["sgo_int_categoriacomprobante"];$fecha=$row["sgo_dat_fechaemision"];
            $serie=$row["sgo_vch_serie"];$numero=$row["sgo_vch_numero"];$moneda=$row["sgo_int_moneda"];$obs=$row["sgo_vch_observaciones"];
            $subtotal=$row["sgo_dec_subtotal"];$igv=$row["sgo_dec_igv"];$total=$row["sgo_dec_total"];
            break;
         }
         $grilla_docref="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_NotaCredito_Items_DocReferencia($prm,290,"tbl_docreferencia_det") . "</div>";
         $grilla_item="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_NotaCredito_Items($prm,650,"tbl_notacredito_det") . "</div>";
         $disabled=($prm=="0"?false:true);
         $Val_Proveedor=new InputValidacion();
         $Val_Proveedor->InputValidacion('DocValue("fil_cmbproveedor")!=""','Debe especificar el proveedor');
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
         $Val_Total=new InputValidacion();
         $Val_Total->InputValidacion('parseFloat(DocValue("txt_monto"))==parseFloat(DocValue("txt_total"))','Debe designar el total del comprobante');
         $inputs=array(
            "Comprobante" => $Helper->combo_tipocomprobante("fil_cmbtipocomprobante",++$index,9,$tipo,"",$Val_TipoComprobante,$disabled),
            "Categoría" => $Helper->combo_categoriacomprobante("fil_cmbcategoriacomprobante",++$index,9,$categoria,"",$Val_CategoriaComprobante,$disabled),
            "Nro Comprobante" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,40,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,6,70,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
            "Proveedor" => $Helper->combo_proveedor("fil_cmbproveedor",++$index,$proveedor,"",$Val_Proveedor,$disabled),
            "Moneda" => $Helper->combo_moneda_alias("fil_cmbmoneda",++$index,$moneda,"",$Val_Moneda,$disabled),
            "Fecha Emisión" => $Helper->textdate("fil_txtFechaEmision",++$index,$fecha,false,$TipoDate->fecha,80,"","",$Val_Fecha,$disabled),
            "Documentos Referencia"=>$grilla_docref . $Helper->hidden("fil_txtmonto",1000,"0x00") . $Helper->hidden("hf_idccompra",1000,"") . "~1",
            "Items"=>$grilla_item . "~3",
            "Observaciones" => $Helper->textarea("fil_txtObservaciones",++$index,$obs,170,3,"","","","",null,$disabled) . '~5',
            " "=>$Helper->hidden("txt_igv_prc",0,"0.18") . "<div class='right'>Monto&nbsp;" . $Helper->textbox("txt_monto",$index,"0",0,100,$TipoTxt->decimal,"","","","",null,true) . "</div>",
            "  "=>"<div class='right'>SubTotal&nbsp;" .$Helper->textbox("txt_subtotal",0,$subtotal,0,100,$TipoTxt->decimal,"","","","",null,true) .
                  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IGV&nbsp;" . $Helper->textbox("txt_igv",0,$igv,0,100,$TipoTxt->decimal,"","","","",null,true) .
                  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total&nbsp;" . $Helper->textbox("txt_total",0,$total,0,100,$TipoTxt->decimal,"","","","",$Val_Total,true) . '&nbsp;&nbsp;&nbsp;</div>~3',
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_co",$inputs,$buttons,3,1050,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Nota de Crédito",800,$html,(!$disabled?$Helper->button("","Grabar",70,"Operacion('compras','Mant_NotaCredito','tbl_mant_co','" . $prm . "')","textoInput"):"")) . "<script>Obj('fil_cmbtipocomprobante').focus();</script>";
      }
      function Mant_NotaCredito($prm){
         $Obj=new mysqlhelper;$Helper= new htmlhelper;$valor=explode('|',$prm);
         $trans=$Obj->transaction();
         include_once('../../code/bl/bl_general.php'); $Obj_general=new bl_general; $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante(5);//Guia Entrada
         $sql="INSERT INTO tbl_sgo_comprobantecompra (sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_vch_serie,sgo_vch_numero,sgo_int_proveedor,sgo_int_moneda,sgo_dat_fechaemision,sgo_vch_observaciones,sgo_int_estadocomprobante)
         VALUES (" . $valor[1] . "," . $valor[2] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[5] . "," . $valor[6] . ",'" . $Helper->convertir_fecha_ingles($valor[7]) . "','" . $valor[8] . "',1)";
         try{
              if($trans->query($sql))
              {
                  $id=mysqli_insert_id($trans);
                  $valor_item=explode('~',$valor[9]);
                  $valorcompra=0;$valorigv=0;$valortotal=0;
                  foreach ($valor_item as $k) { //Grilla Documentos de Referencia
                     $valor_col=explode('_',$k);
                     if(count($valor_col)>1){
                       $sql="INSERT INTO tbl_sgo_comprobantecompradocreferencia (sgo_int_docreferenciador,sgo_int_docreferenciado,sgo_dec_monto)
                       VALUES (" . $id . "," . $valor_col[0] . "," . $valor_col[1] . ")";
                       if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                       $sql="UPDATE tbl_sgo_documentoporpagar SET sgo_dec_total=(sgo_dec_total - " . $valor_col[1] . "),sgo_dec_saldo=(sgo_dec_saldo - " . $valor_col[1] . ")" . " WHERE sgo_int_comprobantecompra=" . $valor_col[0];
                       if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                     }
                  }
                  $valor_item=explode('~',$valor[10]);$Obj_General=new bl_general;
                  foreach ($valor_item as $k) {  // Grilla Items Comprobante
                     $valor_col=explode('_',$k);
                     $valorcompra +=floatval($valor_col[3]);
                     $sql="INSERT INTO tbl_sgo_comprobantecompradetalle (sgo_int_comprobantecompra, sgo_dec_cantidad, sgo_int_producto,sgo_vch_descripcion,sgo_vch_observacion,sgo_dec_precio, sgo_dec_valorcompra)
                     VALUES (" . $id . "," . $valor_col[0] . "," . (is_numeric($valor_col[1])===false?"null":$valor_col[1]) . ",'" . (is_numeric($valor_col[1])===false?$valor_col[1]:$Obj_General->Obtener_Nombre_Articulo($valor_col[1])) . "',''," . $valor_col[2] . "," . $valor_col[3] . ")";
                     if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                  }
//                  if($valor[2]=="4"){ $valorigv=$valorcompra * 0.18; $valortotal=$valorcompra+$valorigv; }//Factura
//                  else $valortotal=$valorcompra;
                  $valorigv=$valorcompra * 0.18; $valortotal=$valorcompra+$valorigv;
                  $sql="UPDATE tbl_sgo_comprobantecompra SET sgo_dec_subtotal=" . $valorcompra . ",sgo_dec_igv=" . $valorigv . ",sgo_dec_total=" . $valortotal . " WHERE sgo_int_comprobantecompra=" . $id;
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
      function Confirma_Eliminar_NotaCredito($prm){
         $Helper=new htmlhelper;
         if($this->Obtener_Estado_Comprobante($prm)!=3){
           return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la nota de crédito ' . $this->Obtener_Nombre_ComprobanteCompra($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('compras','Eliminar_NotaCredito','','" . $prm . "')"));
         }
         else{
           return $Helper->PopUp("","Confirmación",450,htmlentities('La nota de crédito ' . $this->Obtener_Nombre_ComprobanteCompra($prm) . ' ya se encuentra anulada'),"");
         }
      }
      function Eliminar_NotaCredito($prm){
         $Obj=new mysqlhelper;$trans=$Obj->transaction();
         try{
           $sql="UPDATE tbl_sgo_comprobantecompra SET sgo_int_estadocomprobante=3 WHERE sgo_int_comprobantecompra=" . $prm;
           if($trans->query($sql))
           {
               $result = $Obj->consulta("SELECT sgo_int_docreferenciado,sgo_dec_monto FROM tbl_sgo_comprobantecompradocreferencia WHERE sgo_int_docreferenciador = " . $prm);
               while ($row = mysqli_fetch_array($result))
               {
                   $sql="UPDATE tbl_sgo_documentoporpagar SET sgo_dec_total=(sgo_dec_total + " . $row["sgo_dec_monto"] . "),sgo_dec_saldo=(sgo_dec_saldo + " . $row["sgo_dec_monto"] . ") WHERE sgo_int_comprobantecompra=" . $row["sgo_int_docreferenciado"];
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
            "Proveedor" => $Helper->combo_proveedor("fil_cmbBusquedaproveedor",$index,"",""),
            "Desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Nro. Comprobante" => $Helper->textbox("fil_txtBusquedaNroComprobante",++$index,"",11,100,$TipoTxt->texto,"","","",""),
            "Estado" => $Helper->combo_estado_comprobante("fil_cmbBusquedaestadocomprobante",++$index,"",""),
         );
         $buttons=array($Helper->button("btnBuscarComprobante","Buscar",70,"Buscar_Grilla('compras','Grilla_Listar_NotaDebito','tbl_listarcomprobante','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarcomprobante",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_NotaDebito($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT co.sgo_int_comprobantecompra as param, per.sgo_vch_nrodocumentoidentidad as RUC, per.sgo_vch_nombre as Proveedor,mon.sgo_vch_simbolo as Moneda, Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as 'Comprobante', DATE_FORMAT(co.sgo_dat_fechaemision, '%d/%m/%Y') as 'Fecha Emisión',FORMAT(co.sgo_dec_subtotal,2) AS SubTotal,FORMAT(co.sgo_dec_igv,2) AS Igv,FORMAT(co.sgo_dec_total,2) AS Total,efa.sgo_vch_descripcion as Estado
                FROM tbl_sgo_comprobantecompra co
                INNER JOIN tbl_sgo_proveedor prv on prv.sgo_int_proveedor=co.sgo_int_proveedor
                INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=prv.sgo_int_proveedor
                INNER JOIN tbl_sgo_moneda mon on mon.sgo_int_moneda=co.sgo_int_moneda
                INNER JOIN tbl_sgo_estadocomprobante efa on efa.sgo_int_estadocomprobante=co.sgo_int_estadocomprobante";
         $where = $Obj->sql_where("WHERE prv.sgo_int_proveedor=@p1 and (co.sgo_dat_fechaemision BETWEEN '@p2 00:00' and '@p3 23:59') Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) like '%@p4%' and co.sgo_int_estadocomprobante=@p5 and co.sgo_int_tipocomprobante=12",
                 $valor[0] . '|' . $Helper->convertir_fecha_ingles($valor[1]) . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $valor[3] . '|' . $valor[4]);
         $orderby = "ORDER BY co.sgo_int_comprobantecompra DESC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"PopUp('compras','PopUp_Mant_NotaDebito','','","PopUp('compras','PopUp_Mant_NotaDebito','','","","PopUp('compras','Confirma_Eliminar_NotaDebito','','",null,array(),array(),array(),20,"");
      }
      function Grilla_Listar_NotaDebito_Items_DocReferencia($prm,$size=290,$id_tabla=null){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT co.sgo_int_comprobantecompra as param,Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as Comprobante,ref.sgo_dec_monto as 'Monto Aplicable'
                FROM tbl_sgo_comprobantecompradocreferencia ref
                INNER JOIN tbl_sgo_comprobantecompra co ON ref.sgo_int_docreferenciador=co.sgo_int_comprobantecompra
                INNER JOIN tbl_sgo_proveedor prv ON prv.sgo_int_proveedor=co.sgo_int_proveedor";
         $where = $Obj->sql_where("WHERE ref.sgo_int_docreferenciador=@p1",$prm);
         $orderby = "ORDER BY co.sgo_int_comprobantecompra DESC";
         $botones=new GrillaBotones;
         $botones->GrillaBotones(($prm==0?"PopUp('compras','PopUp_Mant_NotaDebito_DocReferencia','tbl_mant_co','":""),"","","","");
         return $Helper->Crear_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),$id_tabla,$botones,$size,array(),array(),array(),20,"");
      }
      function Grilla_Listar_NotaDebito_DocReferencia($prm,$size=1000){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $where = $Obj->sql_where("and doc.sgo_int_proveedor=@p1",$valor[5]);
         $sql ="SELECT doc.sgo_int_comprobantecompra as param, per.sgo_vch_nombre as 'Proveedor', tco.sgo_vch_descripcion as 'Tipo Comprobante',DATE_FORMAT(doc.sgo_dat_fechaemision, '%d/%m/%Y') as 'Fecha Emisión',Concat(doc.sgo_vch_serie,'-',doc.sgo_vch_numero) as 'Comprobante', doc.sgo_dec_total as 'Total', dpp.sgo_dec_saldo as 'Pendiente', doc.sgo_vch_observaciones as 'Observacion'
                FROM tbl_sgo_comprobantecompra doc
                INNER JOIN tbl_sgo_documentoporpagar dpp ON dpp.sgo_int_comprobantecompra=doc.sgo_int_comprobantecompra
                INNER JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=doc.sgo_int_tipocomprobante
                INNER JOIN tbl_sgo_persona per ON per.sgo_int_persona=doc.sgo_int_proveedor
                WHERE dpp.sgo_dec_saldo!=0 ". $where;
         $botones=new GrillaBotones;
         $botones->GrillaBotones("","","","","1");
         $html= $Helper->Crear_Grilla($Obj->consulta($sql),"tbl_docreferencia",$botones,$size,array(),array(),array(),20,"");
         return $Helper->PopUp("","Documentos de compra",1050,$html,$Helper->button("","Aceptar",70,"Agregar_NotaDebito_DocReferencia('tbl_docreferencia','tbl_docreferencia_det');Cerrar_PopUp('PopUp@')","textoInput"));
      }
      function Grilla_Listar_NotaDebito_Items($prm,$size=650,$id_grilla=null){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT fcd.sgo_int_comprobantecompradetalle as param,fcd.sgo_dec_cantidad as Cantidad, fcd.sgo_vch_descripcion as 'Descripción', fcd.sgo_dec_precio as Precio,fcd.sgo_dec_valorcompra as 'Valor Compra'
                FROM tbl_sgo_comprobantecompra co
                INNER JOIN tbl_sgo_comprobantecompradetalle fcd ON co.sgo_int_comprobantecompra=fcd.sgo_int_comprobantecompra";
         $where = $Obj->sql_where("WHERE co.sgo_int_comprobantecompra=@p1",$prm);
         $orderby = "ORDER BY co.sgo_int_comprobantecompra DESC";
         $botones=new GrillaBotones;
         $botones->GrillaBotones(($prm=="0"?"Nuevo_NotaDebitoDetalle('tbl_mant_co','tbl_notadebito_det',this.parentNode)":""),"","","","");
         return $Helper->Crear_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),$id_grilla,$botones,$size,array(),array(),array(),20,"") . "<script>Carga_Js('../../js/jscript_compras.js')</script>";
      }
      function PopUp_Mant_NotaDebito($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $proveedor="";$tipo="";$categoria="";$fecha=date("d/m/Y");$serie="";$numero="";$moneda="";$obs="";$subtotal=0;$igv=0;$total=0;
         $result = $Obj->consulta("SELECT co.sgo_int_proveedor,	sgo_int_tipocomprobante,sgo_int_categoriacomprobante,DATE_FORMAT(sgo_dat_fechaemision, '%d/%m/%Y') as sgo_dat_fechaemision,sgo_vch_serie,sgo_vch_numero,sgo_int_moneda,sgo_vch_observaciones,sgo_dec_subtotal,sgo_dec_igv,sgo_dec_total FROM tbl_sgo_comprobantecompra co WHERE co.sgo_int_comprobantecompra = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $proveedor=$row["sgo_int_proveedor"];$tipo=$row["sgo_int_tipocomprobante"];$categoria=$row["sgo_int_categoriacomprobante"];$fecha=$row["sgo_dat_fechaemision"];
            $serie=$row["sgo_vch_serie"];$numero=$row["sgo_vch_numero"];$moneda=$row["sgo_int_moneda"];$obs=$row["sgo_vch_observaciones"];
            $subtotal=$row["sgo_dec_subtotal"];$igv=$row["sgo_dec_igv"];$total=$row["sgo_dec_total"];
            break;
         }
         $grilla_docref="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_NotaDebito_Items_DocReferencia($prm,290,"tbl_docreferencia_det") . "</div>";
         $grilla_item="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_NotaDebito_Items($prm,650,"tbl_notadebito_det") . "</div>";
         $disabled=($prm=="0"?false:true);
         $Val_Proveedor=new InputValidacion();
         $Val_Proveedor->InputValidacion('DocValue("fil_cmbproveedor")!=""','Debe especificar el proveedor');
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
         $Val_Total=new InputValidacion();
         $Val_Total->InputValidacion('parseFloat(DocValue("txt_monto"))==parseFloat(DocValue("txt_total"))','Debe designar el total del comprobante');
         $inputs=array(
            "Comprobante" => $Helper->combo_tipocomprobante("fil_cmbtipocomprobante",++$index,10,$tipo,"",$Val_TipoComprobante,$disabled),
            "Categoría" => $Helper->combo_categoriacomprobante("fil_cmbcategoriacomprobante",++$index,10,$categoria,"",$Val_CategoriaComprobante,$disabled),
            "Nro Comprobante" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,40,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,6,70,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
            "Proveedor" => $Helper->combo_proveedor("fil_cmbproveedor",++$index,$proveedor,"",$Val_Proveedor,$disabled),
            "Moneda" => $Helper->combo_moneda_alias("fil_cmbmoneda",++$index,$moneda,"",$Val_Moneda,$disabled),
            "Fecha Emisión" => $Helper->textdate("fil_txtFechaEmision",++$index,$fecha,false,$TipoDate->fecha,80,"","",$Val_Fecha,$disabled),
            "Documentos Referencia"=>$grilla_docref . $Helper->hidden("fil_txtmonto",1000,"0x00") . $Helper->hidden("hf_idccompra",1000,"") . "~1",
            "Items"=>$grilla_item . "~3",
            "Observaciones" => $Helper->textarea("fil_txtObservaciones",++$index,$obs,170,3,"","","","",null,$disabled) . '~5',
            " "=>$Helper->hidden("txt_igv_prc",0,"0.18") . "<div class='right'>Monto&nbsp;" . $Helper->textbox("txt_monto",$index,"0",0,100,$TipoTxt->decimal,"","","","",null,true) . "</div>",
            "  "=>"<div class='right'>SubTotal&nbsp;" .$Helper->textbox("txt_subtotal",0,$subtotal,0,100,$TipoTxt->decimal,"","","","",null,true) .
                  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IGV&nbsp;" . $Helper->textbox("txt_igv",0,$igv,0,100,$TipoTxt->decimal,"","","","",null,true) .
                  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total&nbsp;" . $Helper->textbox("txt_total",0,$total,0,100,$TipoTxt->decimal,"","","","",$Val_Total,true) . '&nbsp;&nbsp;&nbsp;</div>~3',
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_co",$inputs,$buttons,3,1050,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Nota de Débito",800,$html,(!$disabled?$Helper->button("","Grabar",70,"Operacion('compras','Mant_NotaDebito','tbl_mant_co','" . $prm . "')","textoInput"):"")) . "<script>Obj('fil_cmbtipocomprobante').focus();</script>";
      }
      function Mant_NotaDebito($prm){
         $Obj=new mysqlhelper;$Helper= new htmlhelper;$valor=explode('|',$prm);
         $trans=$Obj->transaction();
         include_once('../../code/bl/bl_general.php'); $Obj_general=new bl_general; $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante(5);//Guia Entrada
         $sql="INSERT INTO tbl_sgo_comprobantecompra (sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_vch_serie,sgo_vch_numero,sgo_int_proveedor,sgo_int_moneda,sgo_dat_fechaemision,sgo_vch_observaciones,sgo_int_estadocomprobante)
         VALUES (" . $valor[1] . "," . $valor[2] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[5] . "," . $valor[6] . ",'" . $Helper->convertir_fecha_ingles($valor[7]) . "','" . $valor[8] . "',1)";
         try{
              if($trans->query($sql))
              {
                  $id=mysqli_insert_id($trans);
                  $valor_item=explode('~',$valor[9]);
                  $valorcompra=0;$valorigv=0;$valortotal=0;
                  foreach ($valor_item as $k) { //Grilla Documentos de Referencia
                     $valor_col=explode('_',$k);
                     if(count($valor_col)>1){
                       $sql="INSERT INTO tbl_sgo_comprobantecompradocreferencia (sgo_int_docreferenciador,sgo_int_docreferenciado,sgo_dec_monto)
                       VALUES (" . $id . "," . $valor_col[0] . "," . $valor_col[1] . ")";
                       if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                       $sql="UPDATE tbl_sgo_documentoporpagar SET sgo_dec_total=(sgo_dec_total + " . $valor_col[1] . "),sgo_dec_saldo=(sgo_dec_saldo + " . $valor_col[1] . ")" . " WHERE sgo_int_comprobantecompra=" . $valor_col[0];
                       if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                     }
                  }
                  $valor_item=explode('~',$valor[10]);$Obj_General=new bl_general;
                  foreach ($valor_item as $k) {  // Grilla Items Comprobante
                     $valor_col=explode('_',$k);
                     $valorcompra +=floatval($valor_col[3]);
                     $sql="INSERT INTO tbl_sgo_comprobantecompradetalle (sgo_int_comprobantecompra, sgo_dec_cantidad, sgo_int_producto,sgo_vch_descripcion,sgo_vch_observacion,sgo_dec_precio, sgo_dec_valorcompra)
                     VALUES (" . $id . "," . $valor_col[0] . "," . (is_numeric($valor_col[1])===false?"null":$valor_col[1]) . ",'" . (is_numeric($valor_col[1])===false?$valor_col[1]:$Obj_General->Obtener_Nombre_Articulo($valor_col[1])) . "',''," . $valor_col[2] . "," . $valor_col[3] . ")";
                     if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                  }
//                  if($valor[2]=="4"){ $valorigv=$valorcompra * 0.18; $valortotal=$valorcompra+$valorigv; }//Factura
//                  else $valortotal=$valorcompra;
                  $valorigv=$valorcompra * 0.18; $valortotal=$valorcompra+$valorigv;
                  $sql="UPDATE tbl_sgo_comprobantecompra SET sgo_dec_subtotal=" . $valorcompra . ",sgo_dec_igv=" . $valorigv . ",sgo_dec_total=" . $valortotal . " WHERE sgo_int_comprobantecompra=" . $id;
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
      function Confirma_Eliminar_NotaDebito($prm){
         $Helper=new htmlhelper;
         if($this->Obtener_Estado_Comprobante($prm)!=3){
           return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la nota de débito ' . $this->Obtener_Nombre_ComprobanteCompra($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('compras','Eliminar_NotaDebito','','" . $prm . "')"));
         }
         else{
           return $Helper->PopUp("","Confirmación",450,htmlentities('La nota de débito ' . $this->Obtener_Nombre_ComprobanteCompra($prm) . ' ya se encuentra anulada'),"");
         }
      }
      function Eliminar_NotaDebito($prm){
         $Obj=new mysqlhelper;$trans=$Obj->transaction();
         try{
           $sql="UPDATE tbl_sgo_comprobantecompra SET sgo_int_estadocomprobante=3 WHERE sgo_int_comprobantecompra=" . $prm;
           if($trans->query($sql))
           {
               $result = $Obj->consulta("SELECT sgo_int_docreferenciado,sgo_dec_monto FROM tbl_sgo_comprobantecompradocreferencia WHERE sgo_int_docreferenciador = " . $prm);
               while ($row = mysqli_fetch_array($result))
               {
                   $sql="UPDATE tbl_sgo_documentoporpagar SET sgo_dec_total=(sgo_dec_total - " . $row["sgo_dec_monto"] . "),sgo_dec_saldo=(sgo_dec_saldo - " . $row["sgo_dec_monto"] . ") WHERE sgo_int_comprobantecompra=" . $row["sgo_int_docreferenciado"];
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


/****************************************************************PROVEEDOR************************************************************************/
      function Filtros_Listar_Proveedor(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=10;
         $inputs=array(
            "Proveedor" => $Helper->textbox("fil_cmbBusquedaproveedor",$index,"",100,250,$TipoTxt->texto,"","","",""),
            "Nro Documento" => $Helper->textbox("fil_txtBusquedaNroDocumento",++$index,"",12,100,$TipoTxt->numerico,"","","","")
         );
         $buttons=array($Helper->button("btnBuscarProveedor","Buscar",70,"Buscar_Grilla('compras','Grilla_Listar_Proveedor','tbl_listarproveedor','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarproveedor",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Proveedor($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT prv.sgo_int_proveedor as param, per.sgo_vch_nombre as Proveedor,per.sgo_vch_alias as 'Nombre Comercial', tdo.sgo_vch_descripcion as 'Tipo Documento', per.sgo_vch_nrodocumentoidentidad as 'Nro Documento'
          FROM tbl_sgo_proveedor prv
          INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=prv.sgo_int_proveedor
          INNER JOIN tbl_sgo_tipodocumentoidentidad tdo ON tdo.sgo_int_documentoidentidad=per.sgo_int_tipodocumentoidentidad";
         $where = $Obj->sql_where("WHERE per.sgo_vch_nombre like '%@p1%' and per.sgo_vch_nrodocumentoidentidad like '%@p2%' and prv.sgo_bit_activo=1",$prm);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('compras','PopUp_Mant_Proveedor','','","","PopUp('compras','PopUp_Mant_Proveedor','','","PopUp('compras','Confirma_Eliminar_Proveedor','','",null,array(),array(),array(),20,"");
      }
      function PopUp_Mant_Proveedor($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=10;
         $razsocial="";$alias="";$nrodocumento="";$tipo="";
         $result = $Obj->consulta("SELECT sgo_vch_nombre,sgo_vch_alias,sgo_vch_nrodocumentoidentidad,sgo_int_tipodocumentoidentidad FROM tbl_sgo_persona WHERE sgo_int_persona = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $razsocial=$row["sgo_vch_nombre"];$alias=$row["sgo_vch_alias"];$nrodocumento=$row["sgo_vch_nrodocumentoidentidad"];$tipo=$row["sgo_int_tipodocumentoidentidad"];
            break;
         }
         $Val_RazSocial=new InputValidacion();
         $Val_RazSocial->InputValidacion('DocValue("fil_txtRazSocial")!=""','Debe especificar la razón social');
         $Val_NroDocumento=new InputValidacion();
         $Val_NroDocumento->InputValidacion('DocValue("fil_txtNroDocumento")!=""','Debe especificar el nro del documento');
         $Val_Tipo=new InputValidacion();
         $Val_Tipo->InputValidacion('DocValue("fil_cmbTipoDocumento")!=""','Debe especificar el tipo de documento');
         $inputs=array(
            ($tipo==2?"Razón Social":"Nombre") => $Helper->textbox("fil_txtRazSocial",++$index,$razsocial,100,250,$TipoTxt->texto,"","","","",$Val_RazSocial),
            "Nombre Comercial" => $Helper->textbox("fil_txtComercial",++$index,$alias,100,250,$TipoTxt->texto,"","","",""),
            "Nro. Documento" => $Helper->textbox("fil_txtNroDocumento",++$index,$nrodocumento,12,100,$TipoTxt->numerico,"","","","",$Val_NroDocumento),
            "Tipo Documento" => $Helper->combo_tipodocumentoidentidad("fil_cmbTipoDocumento",++$index,$tipo,"if(this.value==2){SetDocInnerHTML('td_" . ($tipo==2?"Razon_Social":"Nombre") . "','Raz&oacute;n Social');}else{SetDocInnerHTML('td_" . ($tipo==2?"Razon_Social":"Nombre") . "','Nombre');}",$Val_Tipo),
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_proveedor",$inputs,$buttons,2,700,"","");
         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Proveedor",700,$html,$Helper->button("","Grabar",70,"Operacion('compras','Mant_Proveedor','tbl_mant_proveedor','" . $prm . "')","textoInput"));
      }
      function Mant_Proveedor($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          $trans=$Obj->transaction();
         try{
           if($valor[0]!="0"){
              $sql="UPDATE tbl_sgo_persona SET sgo_vch_nombre='" . $valor[1] . "',sgo_vch_alias='" . ($valor[2]==""?$valor[1]:$valor[2]) . "',sgo_vch_nrodocumentoidentidad='" . $valor[3] . "',sgo_int_tipodocumentoidentidad=" . $valor[4] . " WHERE sgo_int_persona=" . $valor[0];
              if(!$trans->query($sql))
              /*{
                  $sql="UPDATE tbl_sgo_cliente SET sgo_int_division=" . $valor[3] . ",sgo_int_ubigeo=" . $valor[8] . " WHERE sgo_int_cliente=" . $valor[0];
                  if(!$trans->query($sql))throw new Exception($sql . " => ". $trans->error);
              }
              else*/ throw new Exception($sql . " => ". $trans->error);
              $trans->commit();$trans->close();
           }
           else{
              $sql="INSERT INTO tbl_sgo_persona (sgo_vch_nombre, sgo_vch_alias,sgo_vch_nrodocumentoidentidad,sgo_int_tipodocumentoidentidad,sgo_bit_activo) VALUES ('" . $valor[1] . "','" . ($valor[2]==""?$valor[1]:$valor[2]) . "','" . $valor[3] . "'," . $valor[4] . ",1)";
              if($trans->query($sql))
              {
                  $id=mysqli_insert_id($trans);
                  $sql="INSERT INTO tbl_sgo_proveedor (sgo_int_proveedor,sgo_bit_activo) VALUES (" . $id . ",1)";
                  if(!$trans->query($sql))throw new Exception($sql . " => ". $trans->error);
              }
              else throw new Exception($sql . " => ". $trans->error);
              $trans->commit();$trans->close();
           }
         }
         catch(Exception $e)
         {
            echo '<script>alert("Error: ' . $e . '");</script>';
            $trans->rollback();$trans->close();return -1;
//             throw new Exception("insert name again",0,$e);
         }
//          echo '<script>alert("'.$sql .'")</script>';
      }
      function Confirma_Eliminar_Proveedor($prm){
          $Helper=new htmlhelper;$Obj_General=new bl_general;
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar al proveedor ' . $Obj_General->Obtener_Nombre_Persona($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('compras','Eliminar_Proveedor','','" . $prm . "')"));
      }
      function Eliminar_Proveedor($prm){
          $Obj=new mysqlhelper;
          $sql="UPDATE tbl_sgo_proveedor SET sgo_bit_activo=0 WHERE sgo_int_proveedor=" . $prm;
//          echo '<script>alert("'.$sql .'")</script>';
          if($Obj->execute($sql)!=-1){
             return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarProveedor');</script>";
          }
          else return "<script>Operacion_Result(false);</script>";
      }

/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
      function Obtener_Nombre_ComprobanteCompra($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as sgo_vch_nrofacturacompra FROM tbl_sgo_comprobantecompra co WHERE co.sgo_int_comprobantecompra=" . $prm);
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
      function Obtener_Estado_Comprobante($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_int_estadocomprobante FROM tbl_sgo_comprobantecompra WHERE sgo_int_comprobantecompra=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_int_estadocomprobante"];
          }
          return "";
      }
   }
?>