<?php include_once('../../code/lib/htmlhelper.php');include_once('../../code/lib/loghelper.php');include_once('../../code/config/app.inc');
  Class bl_ordenservicio
  {
/********************************************************CATaLOGO oRDENES SERICIO**************************************************************************/
      function Filtros_Listar_OrdenesServicio(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            //"Cliente" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"",""),
            "Cliente" => $Helper->textbox_predictivo("fil_txtCliente",++$index,"",128,300,"","","clientes"),
            "Entrega desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Creacion" => $Helper->textdate("fil_txtBusquedaCreacionDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedaCreacionhasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Nro. Orden" => $Helper->textbox("fil_txtBusquedaNroOrden",++$index,"",11,100,$TipoTxt->texto,"","","",""),
            "Estado" => $Helper->combo_estado_ordenservicio("fil_cmbBusquedaestado",++$index,"",""),
         );
         $buttons=array($Helper->button("btnBuscarOC","Buscar",70,"Buscar_Grilla('ordenservicio','Grilla_Listar_OrdenesServicio','tbl_listaroc','','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listaroc",$inputs,$buttons,3,990,"","");
      }
      function Grilla_Listar_OrdenesServicio($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$loghelper=new loghelper;
         $sql ="SELECT oc.sgo_int_ordenservicio as param, oc.sgo_dat_fecharegistro as Registro,per.sgo_vch_nrodocumentoidentidad as RUC, CONCAT(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) as Cliente,Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as 'Cotizacion', Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero) as OS,oc.sgo_vch_nroordencompracliente as 'OC Cliente',COUNT(ocd.sgo_int_ordenserviciodetalle) as Items,FORMAT(SUM(ocd.sgo_dec_cantidad * ocd.sgo_dec_precio),2) AS Monto,eoc.sgo_vch_descripcion as Estado,
                case when oc.sgo_int_estado!=2 then '|PopUp(\'ordenservicio\',\'Mant_OrdenProduccion\',\'\',\'|gear.jpg' else '||green_check.gif' end as 'Generar orden de Produccion'
                FROM tbl_sgo_ordenservicio oc
                LEFT JOIN tbl_sgo_cotizacion co on co.sgo_int_cotizacion=oc.sgo_int_cotizacion
                INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=oc.sgo_int_cliente
                INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=cli.sgo_int_cliente
                INNER JOIN tbl_sgo_estadoordenservicio eoc on eoc.sgo_int_estadoos=oc.sgo_int_estado
                LEFT JOIN tbl_sgo_ordenserviciodetalle ocd on ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio
                WHERE 1=1";
         if($valor[0]!="")
         {
         	//$sql.=" and cli.sgo_int_cliente=".$valor[0];
         	$sql.=" and concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) like '%".$valor[0]."%'";
         }
         if($valor[1]!="")
         {
         	$sql.=" and oc.sgo_dat_fechainiciovigencia>='".$Helper->convertir_fecha_ingles($valor[1])." 00:00'";
         }
         if($valor[2]!="")
         {
         	$sql.=" and oc.sgo_dat_fechainiciovigencia<='".$Helper->convertir_fecha_ingles($valor[2])." 23:59'";
         }
         if($valor[3]!="")
         {
         	$sql.=" and oc.sgo_dat_fecharegistro>='".$Helper->convertir_fecha_ingles($valor[3])." 00:00'";
         }
         if($valor[4]!="")
         {
         	$sql.=" and oc.sgo_dat_fecharegistro<='".$Helper->convertir_fecha_ingles($valor[4])." 23:59'";
         }
         if($valor[5]!="")
         {
         	$sql.=" and concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero) like '%".$valor[5]."%'";
         }
         if($valor[6]!="")
         {
         	$sql.=" and oc.sgo_int_estado=".$valor[6];
         }
         
         //$loghelper->log($sql);
         $groupby = "GROUP BY oc.sgo_int_ordenservicio, oc.sgo_dat_fecharegistro,per.sgo_vch_nrodocumentoidentidad, per.sgo_vch_nombre, oc.sgo_int_cotizacion,oc.sgo_vch_serie,oc.sgo_vch_numero,eoc.sgo_vch_descripcion";
         $orderby = "ORDER BY oc.sgo_int_ordenservicio DESC";
                 
         $btn_extra=array();
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $groupby . " " . $orderby),"PopUp('ordenservicio','PopUp_Mant_OrdenServicio','','","PopUp('ordenservicio','Detalles','','","PopUp('ordenservicio','PopUp_Mant_OrdenServicio','','","PopUp('ordenservicio','Confirma_Eliminar','','",null,$btn_extra, array(), array(),20,"");
      }
      function Detalles_OrdenServicio($prm){
          echo '<script>location.href="detalles.php?app=2&prm=' . $prm . '";</script>';
      }
      function PopUp_Mant_OrdenServicio($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;$Loghelper=new loghelper;
         $clientefactura="0";$clienteguia="0";$cliente="0";$occliente="";$estado="1";$ini="";$fin="";$albaran="";$tiporecepcion="";$dir="";$tipo="";$categoria="";$serie="";$numero="";$tipoFormaPago="0";
         $result = $Obj->consulta("SELECT sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_vch_serie,sgo_vch_numero,sgo_int_cliente,sgo_int_clienteguia, sgo_int_clientefactura, sgo_vch_nroordencompracliente,sgo_int_estado,DATE_FORMAT(sgo_dat_fechainiciovigencia, '%d/%m/%Y') as sgo_dat_fechainiciovigencia,DATE_FORMAT(sgo_dat_fechafinvigencia, '%d/%m/%Y') as sgo_dat_fechafinvigencia,sgo_vch_albaran,sgo_int_tiporecepcion,sgo_int_direccion, sgo_int_tipoformapago, (select sgo_int_tipoformapago from tbl_sgo_datosfinancieroscliente where sgo_int_cliente=tbl_sgo_ordenservicio.sgo_int_cliente) as maestrotipoformapago FROM tbl_sgo_ordenservicio WHERE sgo_int_ordenservicio = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $clientefactura=$row["sgo_int_clientefactura"];
            $clienteguia=$row["sgo_int_clienteguia"];
            $cliente=$row["sgo_int_cliente"];
            $occliente=$row["sgo_vch_nroordencompracliente"];
            $estado=$row["sgo_int_estado"];
            $ini=$row["sgo_dat_fechainiciovigencia"];
            $fin=$row["sgo_dat_fechafinvigencia"];
            $albaran=$row["sgo_vch_albaran"];
            $tiporecepcion=$row["sgo_int_tiporecepcion"];
            $dir=$row["sgo_int_direccion"];
            $tipo = $row["sgo_int_tipocomprobante"];
            $categoria = $row["sgo_int_categoriacomprobante"];
            $serie = $row["sgo_vch_serie"];
            $numero= $row["sgo_vch_numero"];
            $tipoFormaPago=($row["sgo_int_tipoformapago"]=="0"?$row["maestrotipoformapago"]:$row["sgo_int_tipoformapago"]);
            break;
         }
         $disabled=($prm=="0"?false:true);
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
         $Val_Estado->InputValidacion('DocValue("fil_cmbEstadooOS")!=""','Debe especificar el estado de la orden de servicio');
         $Val_IniVig=new InputValidacion();
         $Val_IniVig->InputValidacion('DocValue("fil_txtIniVigencia")!=""','Debe especificar el inicio de la fecha de entrega');
         $Val_FinVig=new InputValidacion();
         $Val_FinVig->InputValidacion('DocValue("fil_txtFinVigencia")!=""','Debe especificar el fin de la fecha de entrega');
         $Val_TipoRecepcion=new InputValidacion();
         $Val_TipoRecepcion->InputValidacion('DocValue("fil_cmbTipoRecepcion")!=""','Debe especificar el tipo de recepcion');
         $Val_FormaPago=new InputValidacion();
         $Val_FormaPago->InputValidacion('DocValue("fil_cmb_tipoformapago")!=""','Debe especificar una forma de pago');
         $inputs=array(
            "Tipo Documento" => $Helper->combo_tipocomprobante("fil_cmbTipoDocumento",++$index,"14",$tipo,"Cargar_Objeto('general','Combo_TipoCategoria_TipoComprobante','',this.value,'fil_cmbCategoriaDocumento','panel');Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_Tipo,$disabled),
            "Categoria" => $Helper->combo_categoriacomprobante("fil_cmbCategoriaDocumento",++$index,($prm=="0"?"0":"14"),$categoria,"",$Val_Categoria,$disabled),
            "OS Galver" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,40,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . '-' . $Helper->textbox("fil_txtNroNumero",++$index,$numero,6,70,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
            "Cliente" => $Helper->combo_cliente("fil_cmbcliente",$index,$cliente,"Cargar_Combo('general','Combo_Direccion_x_Cliente','fil_cmbDireccionOC','fil_cmbcliente','fil_cmbDireccionOC');document.getElementById('fil_cmbclienteguia').selectedIndex = document.getElementById('fil_cmbcliente').selectedIndex;document.getElementById('fil_cmbclientefactu').selectedIndex = document.getElementById('fil_cmbcliente').selectedIndex;",$Val_Cli,($prm=="0"?false:true)),
         	"Cliente Guia" => $Helper->combo_cliente("fil_cmbclienteguia",$index,$clienteguia,"",$Val_Cli,($prm=="0"?false:true)),
         	"Cliente Factura" => $Helper->combo_cliente("fil_cmbclientefactu",$index,$clientefactura,"",$Val_Cli,($prm=="0"?false:true)),
            "OC Cliente" => $Helper->textbox("fil_txtnrooc",++$index,$occliente,20,100,$TipoTxt->texto,"","","","",null),
            "Fecha de Entrega" => "Del " . $Helper->textdate("fil_txtIniVigencia",++$index,$ini,false,$TipoDate->fecha,80,"","",$Val_IniVig) . " Al " . $Helper->textdate("fil_txtFinVigencia",++$index,$fin,false,$TipoDate->fecha,80,"","",$Val_FinVig),
            "Nro Albaran" => $Helper->textbox("fil_txtalbaran",++$index,$albaran,20,100,$TipoTxt->numerico,"","","","",null),
            "Tipo Recepcion" => $Helper->combo_tiporecepcion("fil_cmbTipoRecepcion",++$index,$tiporecepcion,"",$Val_TipoRecepcion),
            "Direccion" => $Helper->combo_direccion_x_cliente("fil_cmbDireccionOC",++$index,$cliente,$dir,""),
            "Estado" => $Helper->combo_estado_ordenservicio("fil_cmbEstadooOS",++$index,$estado,$estado,"",$Val_Estado),
            "Forma de Pago" => $Helper->combo_tipoformapago("fil_cmb_tipoformapago",++$index,$tipoFormaPago,"",$Val_FormaPago)
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_oc",$inputs,$buttons,3,1100,"","");
         $content_tabs = array(); $titulo_tabs = array();
         $content_tabs[0] ="<br/>" . $html;
         $titulo_tabs[0]="Orden Servicio";

         $content_tabs[1] ="<br/>";
         $content_tabs[1] .=($estado!=5?"<div class='left'>Importar Items: " . $Helper->button("","Importar",70,"PopUp('ordenservicio','PopUp_Mant_ImportarOrdenServicioDetalle','','" . $prm . "')","") ." *Cargue aqu&iacute; archivos B2B Falabella/Metro/Tottus/Almacenes Paris</div>":"");
         $content_tabs[1] .="<br/>";
         $content_tabs[1] .="<div id='div_OrdenesServicio_Detalles'>" . $this->Tab_OrdenesServicio_Detalles($prm,1100,$estado) . "</div>";
         $titulo_tabs[1]="Detalles";

         $html = $Helper->Crear_Tabs("Tabs_Mant_OrdenServicio",$content_tabs, $titulo_tabs, "","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Orden de Servicio",1100,$html,($estado!=5?$Helper->button("","Grabar",70,"Operacion('ordenservicio','Mant_OrdenServicio','tbl_mant_oc','" . $prm . "')","textoInput"):"") . ($prm==0?"<script>Display('Tabs_Mant_OrdenServicio_1','none');</script>":"")) . "<script>Focus('fil_cmbTipoDocumento');</script>";
      }
      function PopUp_Mant_ImportarOrdenServicioDetalle($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $cliente="0";
         $result = $Obj->consulta("SELECT sgo_int_cliente FROM tbl_sgo_ordenservicio WHERE sgo_int_ordenservicio = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $cliente=$row["sgo_int_cliente"]; break;
         }
         $Val_Direccion=new InputValidacion();
         $Val_Direccion->InputValidacion('(DocValue("fil_cmbTipo")==2) || (DocValue("fil_cmbTipo")!=2 && DocValue("fil_cmbDireccionOC")!="")','Debe especificar la direccion');
         $Val_Xml=new InputValidacion();
         $Val_Xml->InputValidacion('DocValue("fil_fileUpload")!=""','Debe especificar el archivo xml/xls');
         $inputs=array(
            "Tipo"=> $Helper->combo_importar_ositem("fil_cmbTipo",++$index,1,""),
            "Direccion" => $Helper->combo_local_x_cliente("fil_cmbDireccionOC",++$index,$cliente,"","",$Val_Direccion),
            "Items"=>$Helper->Upload("fil_fileUpload",++$index,"",450,$Val_Xml) . "* Formatos: B2B Falabella (xml) / Almacenes Paris (xls) / CEN - Metro, Tottus (xml)"
         );
         $html = $Helper->Crear_Form_Layer("tbl_mant_impooc","ordenservicio","Upload_ItemOS",$prm,$inputs,array(),1,550,"","");
         return $Helper->PopUp("PopUp_Mant_ImportarOrdenServicioDetalle","Importar Items Orden de Servicio",550,$html,$Helper->button("tbl_mant_impooc","Grabar",70,"if(Valida_Datos('tbl_mant_impooc')==0)Obj('form_tbl_mant_impooc').submit()","textoInput"));
      }
      function Mant_OrdenServicio($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0)
          { 
          	$sql= "	UPDATE 	tbl_sgo_ordenservicio 
          			SET 	sgo_vch_nroordencompracliente='" . $valor[8] . "',
          					sgo_dat_fecharegistro='" . date("Y-m-d H:i") . "',
          					sgo_dat_fechainiciovigencia='" . $Helper->convertir_fecha_ingles($valor[9]) . "',
          					sgo_dat_fechafinvigencia='" . $Helper->convertir_fecha_ingles($valor[10]) . "',
          					sgo_vch_albaran='" . $valor[11] . "',
          					sgo_int_tiporecepcion=" . $valor[12]  . 
          					($valor[13]=="0"?"null":",sgo_int_direccion=" . $valor[13]) . ",
          					sgo_int_estado=" . $valor[14] . ", 
          					sgo_int_tipoformapago=".$valor[15]." 
          			WHERE 	sgo_int_ordenservicio=" . $valor[0];
            return $Obj->execute($sql);
          }
          else{
            try{
                $trans=$Obj->transaction();
                include_once('../../code/bl/bl_general.php'); $Obj_general=new bl_general; 
                $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante($valor[1]);
                $correlativo=$Obj_general->Obtener_Correlativo_Comprobante($valor[1],$trans);
                
                if($correlativo==-1) throw new Exception("Error: Obtener_Correlativo_Comprobante");
                
                $sql="	INSERT INTO tbl_sgo_ordenservicio (
                		sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_vch_serie,sgo_vch_numero,sgo_int_cliente, 
                		sgo_int_clienteguia, sgo_int_clientefactura, sgo_vch_nroordencompracliente,sgo_dat_fecharegistro,
                		sgo_dat_fechainiciovigencia,sgo_dat_fechafinvigencia,sgo_vch_albaran,sgo_int_tiporecepcion,sgo_int_direccion,
                		sgo_int_estado, sgo_int_tipoformapago)
                		VALUES (" . 
                		$valor[1] . "," . $valor[2] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[4] . ",".
                		$valor[5].",".$valor[7].",'" . $valor[8] . "','" . date("Y-m-d H:i") . "','" . 
                		$Helper->convertir_fecha_ingles($valor[9]) . "','" . $Helper->convertir_fecha_ingles($valor[10]) . "','" . $valor[11] . "'," . $valor[12] . "," .  (is_numeric($valor[13])===false?"null":$valor[13])  . "," . 
                		$valor[14] . ",".$valor[15].")";

                if($trans->query($sql)){
                    $id=mysqli_insert_id($trans);
                    if($Obj_general->Generar_Numeracion_Comprobante($valor[1],$trans)==-1)throw new Exception("Error: Generar_Numeracion_Comprobante");
                }
                else throw new Exception($sql . " => " . $trans->error);
                    $trans->commit();$trans->close();return $id;
            }
            catch(Exception $e)
            {
              echo "<script>alert('Error: " . $e . "');</script>";
              $trans->rollback();$trans->close();return -1;
            }
         }
      }
      function Confirma_Eliminar_OrdenServicio($prm){
         $Obj=new mysqlhelper; $Helper=new htmlhelper;
         return $Helper->PopUp("","Confirmacion",450,htmlentities('¿Está seguro de eliminar la orden de servicio ' . $this->Obtener_Nombre_OrdenServicio($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('ordenservicio','Eliminar_OrdenServicio','','" . $prm . "')"));
      }
      function Eliminar_OrdenServicio($prm){
          $Obj=new mysqlhelper;
          $result = $Obj->consulta("SELECT sgo_int_cotizacion FROM tbl_sgo_ordenservicio WHERE sgo_int_ordenservicio = " . $prm);
          $co=0;$resp="<script>Operacion_Result(false);</script>";
          while ($row = mysqli_fetch_array($result))
          {
             $co=$row["sgo_int_cotizacion"];
             break;
          }
          if($Obj->execute("UPDATE tbl_sgo_ordenservicio SET sgo_int_estado=4 WHERE sgo_int_ordenservicio=" . $prm)!=-1){
              if($co!=0){$Obj->execute("UPDATE tbl_sgo_cotizacion SET sgo_int_estadocotizacion=1 WHERE sgo_int_cotizacion=" . $co);}
              return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarOC');</script>";
          }
          else $resp;
      }

/****************************************************************ORDEN COMPRA DETALLE************************************************************************/
      function Tab_OrdenesServicio_Detalles($prm,$size=1100,$estado=null){
          $html = $this->Grilla_Listar_OrdenesServicio_Detalles($prm,$size,$estado);
          $html .="<b>Monto Total:</b> " . $this->Obtener_MontoTotal_OrdenServicio($prm);
          return $html;
      }
      function Grilla_Listar_OrdenesServicio_Detalles($prm,$size=null,$estado=null){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT 	ocd.sgo_int_ordenserviciodetalle as param, 
         				CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as Producto, 
         				ocd.sgo_dec_cantidad as Cantidad, 
         				ocd.sgo_dec_precio as Precio, 
         				ocd.sgo_int_unidadesporbulto as 'Unidades x Bulto', 
         				Concat(dircli.sgo_vch_codigotienda,' - ',dircli.sgo_vch_nombretienda,' - ',dircli.sgo_vch_direccion) as Destino
          FROM tbl_sgo_ordenservicio oc
          INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=oc.sgo_int_cliente
          INNER JOIN tbl_sgo_ordenserviciodetalle ocd on ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio
          INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=ocd.sgo_int_producto
          LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
         on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
         and	pdt.sgo_int_color = catprodcol.sgo_int_color
         LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
         on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
         and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
         LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
         on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
         and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
          LEFT JOIN tbl_sgo_direccioncliente dircli on dircli.sgo_int_direccion=ocd.sgo_int_direccion";
         $where = "WHERE oc.sgo_int_ordenservicio=@p1";
         $where = $Obj->sql_where($where,$prm);
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),($estado!=5?"PopUp('ordenservicio','PopUp_Mant_OrdenServicio_Detalle','','" . $prm . "|":""),"",($estado!=5?"PopUp('ordenservicio','PopUp_Mant_OrdenServicio_Detalle','','" . $prm . "|":""),($estado!=5?"PopUp('ordenservicio','Confirma_Eliminar_Detalle','','" . $prm . "|":""),$size,array(),array(),array(),10,"");
      }
      function PopUp_Mant_OrdenServicio_Detalle($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate;$valor=explode('|',$prm); $index=10;
         $cliente="";$producto="";$precio="0";$cantidad="1";$dir="";$obs=""; $unxbulto="";
         $result = $Obj->consulta("SELECT ocd.sgo_int_producto,ocd.sgo_dec_precio,ocd.sgo_dec_cantidad,ocd.sgo_int_direccion,ocd.sgo_txt_observaciones, ocd.sgo_int_unidadesporbulto FROM tbl_sgo_ordenserviciodetalle ocd WHERE ocd.sgo_int_ordenserviciodetalle = " . $valor[1]);
         while ($row = mysqli_fetch_array($result))
         {
            $producto=$row["sgo_int_producto"];$precio=$row["sgo_dec_precio"];$cantidad=$row["sgo_dec_cantidad"];$dir=$row["sgo_int_direccion"];$obs=$row["sgo_txt_observaciones"];$unxbulto=$row["sgo_int_unidadesporbulto"];
            break;
         }
         $result = $Obj->consulta("SELECT oc.sgo_int_cliente FROM tbl_sgo_ordenservicio oc WHERE oc.sgo_int_ordenservicio = " . $valor[0]);
         while ($row = mysqli_fetch_array($result))
         {
            $cliente=$row["sgo_int_cliente"];
            break;
         }

         $Val_Producto=new InputValidacion();
         $Val_Producto->InputValidacion('DocValue("fil_cmbproducto")!=""','Debe especificar el producto');
         $Val_Cantidad=new InputValidacion();
         $Val_Cantidad->InputValidacion('(DocValue("fil_txtCantidad")!="" && DocValue("fil_txtCantidad")!="0")','Debe especificar la cantidad solicitada');
         $Val_Precio=new InputValidacion();
         $Val_Precio->InputValidacion('DocValue("fil_txtPrecio")!=""','Debe especificar el precio del producto');
         $Val_Direccion=new InputValidacion();
         $Val_Direccion->InputValidacion('DocValue("fil_cmbDireccionCliente")!=""','Debe especificar un direccion de entrega');
         $Val_UnxBulto=new InputValidacion();
         $Val_UnxBulto->InputValidacion('DocValue("fil_txtUnxBulto")!=""','Debe especificar la cantidad de unidades por bulto');
         $inputs=array(
            "Producto" =>$Helper->hidden("fil_item",$index,"") . $Helper->combo_producto_cliente("fil_cmbproducto",++$index,$cliente,$producto,"Cargar_Objeto('general','Valor_Producto_Precio','fil_cmbcliente',this.value,'fil_txtPrecio','value');Cargar_Objeto('general','Valor_Producto_UnidadxBulto','fil_cmbcliente',this.value,'fil_txtUnxBulto','value');",$Val_Producto),
            "Cantidad" => $Helper->textbox("fil_txtCantidad",++$index,$cantidad,10,100,$TipoTxt->numerico,"","","","",$Val_Cantidad),
            "Precio" => $Helper->textbox("fil_txtPrecio",++$index,$precio,18,100,$TipoTxt->decimal,"","","","",$Val_Precio),
            "Direccion" => $Helper->combo_local_x_cliente("fil_cmbDireccionCliente",++$index,$cliente,$dir,"",$Val_Direccion),
//            "Archivo" => $Helper->Upload_Cargar("fil_fileUpload_OC_Detalle",++$index,"ordenservicio","Upload_OrdenServicio","","","",200),
            "Observaciones " => $Helper->textarea("fil_txtObservacion",++$index,$obs,125,3,"","","") . "~3",
            "Un. x Bulto" => $Helper->textbox("fil_txtUnxBulto",++$index,$unxbulto,18,100,$TipoTxt->numerico,"","","","",$Val_UnxBulto)
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_ordenserviciodetalle",$inputs,$buttons,2,870,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Detalle",870,$html,$Helper->button("","Grabar",70,"Operacion('ordenservicio','Mant_OrdenServicio_Detalle','tbl_mant_ordenserviciodetalle','" . $prm . "')","textoInput"));
      }
      function Mant_OrdenServicio_Detalle($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[1]!=0) $sql= "UPDATE tbl_sgo_ordenserviciodetalle SET sgo_vch_item='" . $valor[2] . "',sgo_int_producto=" . $valor[3] . ",sgo_dec_cantidad=" . $valor[4] . ",sgo_dec_precio=" . $valor[5] . ",sgo_int_direccion=" . $valor[6] . ",sgo_txt_observaciones='" . $valor[7] . "', sgo_int_unidadesporbulto = " . $valor[8] . " WHERE sgo_int_ordenserviciodetalle=" . $valor[1];
          else $sql="INSERT INTO tbl_sgo_ordenserviciodetalle (sgo_int_ordenservicio,sgo_vch_item,sgo_int_producto, sgo_dec_cantidad, sgo_dec_precio,sgo_int_direccion,sgo_txt_observaciones, sgo_int_unidadesporbulto) VALUES (" . $valor[0] . ",'" . $valor[2] . "'," . $valor[3] . "," . $valor[4] . "," . $valor[5] . "," . $valor[6] . ",'" . $valor[7] . "'," . $valor[8] . ")";
//          echo '<script>alert("'.$sql .'")</script>';
          return $Obj->execute($sql);
      }
      function Confirma_Eliminar_OrdenServicio_Detalle($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT pdt.sgo_vch_nombre FROM tbl_sgo_ordenserviciodetalle ocd INNER JOIN tbl_sgo_producto pdt ON ocd.sgo_int_producto=pdt.sgo_int_producto WHERE sgo_int_ordenserviciodetalle=" . $valor[1]);
          while ($row = mysqli_fetch_array($result))
          {
               return $Helper->PopUp("","Confirmacion",450,htmlentities('¿Está seguro de eliminar el producto ' . $row["sgo_vch_nombre"] . '?'),$Helper->button("", "Si", 70, "Operacion('ordenservicio','Eliminar_OrdenServicio_Detalle','','" . $prm . "')"));
          }
          return $Helper->PopUp("","Atencion",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_OrdenServicio_Detalle($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          $sql="DELETE FROM tbl_sgo_ordenserviciodetalle WHERE sgo_int_ordenserviciodetalle=" . $valor[1];
//          echo '<script>alert("'.$sql .'")</script>';
          return $Obj->execute($sql);
      }
      function Upload_ItemOS($id,$oc,$id_direccion,$file,$tipo){
        try{
          $dir="../../archivo/ordenservicio/";
          include_once ("../lib/loghelper.php"); $Log = new loghelper;
          include_once ("../lib/uploadhelper.php"); $Up=new uploadhelper;
          $Obj=new mysqlhelper;
          $cliente="0";$result = $Obj->consulta("SELECT oc.sgo_int_cliente FROM tbl_sgo_ordenservicio oc WHERE oc.sgo_int_ordenservicio = " . $oc);
          while ($row = mysqli_fetch_array($result))
          {
             $cliente=$row["sgo_int_cliente"];
             break;
          }
          if($tipo=="1") //XML
          {
            $resp=$Up->form_cargar_archivo($dir,$id,$file,array('.xml'));           //debo cargar el archivo
            if($resp==1)
            {
               include_once ("bl_ordenproduccion.php"); $Obj_Prod=new bl_ordenproduccion;
               $xml = simplexml_load_file($dir . $file["name"]);$cad="";$msj="";$id_prod=0;$obs="";
               foreach($xml->children() as $child)
               {
                   if($child->getName()=="row")
                   {
                       $row=$child;
                       $id_prod=$Obj_Prod->Obtener_IDProducto_x_Codigo($row->column[1]);
                       if($id_prod=="")
                       {
                          $msj .="<div class=\'left\'>&nbsp;&nbsp;Producto: " . $row->column[1] . " " . $row->column[2] . " no se encuentra registrado.</div><br/>";
                       }
                       else{
                         $cad .="(" . $oc . ",'" . trim($row->column[1]);
                         $cad .="'," . $id_prod;
                         //$obs=trim($row->column[2]);
                         //$obs .= " - " . trim($row->column[3]);
                         $obs="";
                         $cad .="," . $row->column[4] . "," . $Obj_Prod->Obtener_Producto_Precio($id_prod,$cliente);
                         $cad .="," . $id_direccion . ",'" . $obs . "'," . $Obj_Prod->Obtener_Producto_UnidadesxBulto($id_prod,$cliente) . "),";
                       }
                   }
               }
               if($msj=="")
               {
                   $sql="INSERT INTO tbl_sgo_ordenserviciodetalle (sgo_int_ordenservicio,sgo_vch_item,sgo_int_producto, sgo_dec_cantidad, sgo_dec_precio,sgo_int_direccion,sgo_txt_observaciones, sgo_int_unidadesporbulto) VALUES " . substr($cad,0,strlen($cad)-1);
                   return $Obj->execute($sql);
               }
               else{
                 echo "<script>parent.Ver_Mensaje('Importaci&oacute;n de Items Orden de Servicio','" . $msj . "','',650);</script>";
                 return -1;
               }
            }
            else{ echo $resp; return -1;}
          }
          else if($tipo=="2") //EXCEL
          {
             $resp=$Up->form_cargar_archivo($dir,$id,$file,array('.xls'));           //debo cargar el archivo
             if($resp==1)
             {
                include_once ("bl_general.php"); $Obj_Gen=new bl_general;
                include_once ("bl_ordenproduccion.php"); $Obj_Prod=new bl_ordenproduccion;
                include_once ("../lib/excel_reader.php");                
                $data = new Spreadsheet_Excel_Reader($dir . $file["name"]);
                //$data->setOutputEncoding('ASCII');
                //$data->setUTFEncoder('iconv');                
                $rows=$data->rowcount($sheet_index=0);                                                                             
                for($i=1;$i<=$data->colcount($sheet_index=0);$i++)
                {
                	//$Log->log("VALOR EVALUADO: ".trim($data->val(1,$i)));
                	switch (trim($data->val(1,$i)))
                	{
                		case "CÃ³d. Cencosud":
                		case "Cód. Cencosud": $val[0] = $i;
                		break;                
                		case "DescripciÃ³n":		
                		case "Descripción": $val[1] =$i;
                		break;
                		case "EAN13": $val[2] =$i;
                		break;              
                		case "Unidades por Empaque": $val[3] =$i;
                		break;   		
                		case "Empaques Pedidos": $val[4] = $i;
                		break;
                		case "CÃ³d. Local Destino":
                		case "Cód. Local Destino": $val[5] = $i;
                		break;   		
                	}
                }                
    			//$Log->log("val[0]: ".$val[0]);
    			//$Log->log("val[1]: ".$val[1]);
    			//$Log->log("val[2]: ".$val[2]);
    			//$Log->log("val[3]: ".$val[3]);
    			//$Log->log("val[4]: ".$val[4]);
    			//$Log->log("val[5]: ".$val[5]);
    			
                for($i=2;$i<=$rows;$i++)
                {
                    $id_prod=$Obj_Gen->Obtener_IDProducto_x_Codigo($data->val($i, $val[0]));
                    if($id_prod=="")
                    {
                       $msj .="<div class=\'left\'>&nbsp;&nbsp;Producto: " . $data->val($i,$val[0]) . " " . $data->val($i,$val[1]) . " no se encuentra registrado.</div><br/>";
                    }
                    else
                    {
                      if($Obj_Gen->Obtener_IDDireccion_x_Codigo($data->val($i,$val[5]),$cliente)=="")
                      {
						$msj .="<div class=\'left\'>&nbsp;&nbsp;Local con c&oacute;digo: " . $data->val($i,$val[5]) . " no se encuentra registrado.</div><br/>";                      	
                      }
                      else 
                      {
	                      $cad .="(" . $oc . ",'" . trim($data->val($i,$val[2]));
	                      $cad .="'," . $id_prod;
	                      $obs="";//trim($data->val($i,$val[1]));
	                      $cad .="," . (floatval($data->val($i,$val[3])) * floatval($data->val($i,$val[4]))) . "," . $Obj_Gen->Obtener_Producto_Precio($id_prod,$cliente);
	                      $cad .="," . $Obj_Gen->Obtener_IDDireccion_x_Codigo($data->val($i,$val[5]),$cliente) . ",'" . $obs . "'," . $Obj_Prod->Obtener_Producto_UnidadesxBulto($id_prod,$cliente) . "),";
                      }
                    }
                }
                if($msj=="")
                {
                    $sql="INSERT INTO tbl_sgo_ordenserviciodetalle (sgo_int_ordenservicio,sgo_vch_item,sgo_int_producto, sgo_dec_cantidad, sgo_dec_precio,sgo_int_direccion,sgo_txt_observaciones, sgo_int_unidadesporbulto) VALUES " . substr($cad,0,strlen($cad)-1);                    
                    return $Obj->execute($sql);
                }
                else{
                  echo "<script>parent.Ver_Mensaje('Importaci&oacute;n de Items Orden de Servicio','" . $msj . "','',650);</script>";
                  return -1;
                }
             }
             else{ echo $resp; return -1;}
          }
          else //XML
          {
            $resp=$Up->form_cargar_archivo($dir,$id,$file,array('.xml'));           //debo cargar el archivo
            if($resp==1)
            {
               include_once ("bl_ordenproduccion.php"); $Obj_Prod=new bl_ordenproduccion;
               $file = trim(str_replace("eanucc:","",file_get_contents($dir . $file["name"])));
               $xml = simplexml_load_string($file);$cad="";$msj="";$id_prod=0;$obs="";
//               $xml = simplexml_load_file($dir . $file["name"]);
               foreach($xml->body->transaction->command->documentCommand->documentCommandOperand->order->lineItem as $item)
               {
                  $id_prod=$Obj_Prod->Obtener_IDProducto_x_Codigo($item->itemIdentification->gtin);
                  $nom_prod=$Obj_Prod->Obtener_Nombre_Producto_x_Codigo($item->itemIdentification->gtin);
                  if($id_prod=="")
                  {
                     $msj .="<div class=\'left\'>&nbsp;&nbsp;Producto con código: " . $item->itemIdentification->gtin . " no se encuentra registrado.</div><br/>";
                  }
                  else{
                    $cad .="(" . $oc . ",'" . trim($item->itemIdentification->gtin);
                    $cad .="'," . $id_prod;
                    $obs="";
                    $cad .="," . str_replace(",","",$item->requestedQuantity) . "," . $Obj_Prod->Obtener_Producto_Precio($id_prod,$cliente);
                    $cad .="," . $id_direccion . ",'" . $obs . "'," . $Obj_Prod->Obtener_Producto_UnidadesxBulto($id_prod,$cliente) . "),";
                  }
               }
               if($msj=="")
               {
                   $sql="INSERT INTO tbl_sgo_ordenserviciodetalle (sgo_int_ordenservicio,sgo_vch_item,sgo_int_producto, sgo_dec_cantidad, sgo_dec_precio,sgo_int_direccion,sgo_txt_observaciones, sgo_int_unidadesporbulto) VALUES " . substr($cad,0,strlen($cad)-1);
                   return $Obj->execute($sql);
               }
               else{
                 echo "<script>parent.Ver_Mensaje('Importaci&oacute;n de Items Orden de Servicio','" . $msj . "','',650);</script>";
                 return -1;
               }
            }
            else{ echo $resp; return -1;}
          }
        }
        catch(Exception $e){
          echo "<script>parent.Ver_Mensaje('Importaci&oacute;n de Items Orden de Servicio','" . $e->getMessage() . "','',650);</script>";
          return -1;}
      }	 
      
/****************************************************************ORDEN COMPRA DETALLE PRODUCTOS************************************************************************/
      function Grilla_Listar_OrdenesServicio_Productos($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT ocd.sgo_int_ordenserviciodetalle as param, Concat(DATE_FORMAT(sgo_dat_fechainiciovigencia, '%d/%m/%Y'),' - ',DATE_FORMAT(sgo_dat_fechafinvigencia, '%d/%m/%Y')) as Fch_Vigencia,ocd.sgo_vch_item as Item,pdt.sgo_vch_nombre as Producto, ocd.sgo_dec_cantidad as Cantidad, ocd.sgo_dec_precio as Precio, Concat(dircli.sgo_vch_codigotienda,' - ',dircli.sgo_vch_nombretienda,' - ',dircli.sgo_vch_direccion) as Destino
          FROM tbl_sgo_ordenservicio oc
          INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=oc.sgo_int_cliente
          INNER JOIN tbl_sgo_ordenserviciodetalle ocd on ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio
          INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=ocd.sgo_int_producto
          LEFT JOIN tbl_sgo_direccioncliente dircli on dircli.sgo_int_direccion=ocd.sgo_int_direccion";
         $where = $Obj->sql_where("WHERE oc.sgo_int_ordenservicio=@p1",$prm);
         $orderby = " ORDER BY pdt.sgo_vch_nombre ASC";
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"","","","");
      }

/****************************************************************ORDEN COMPRA DETALLE************************************************************************/
      function Mant_OrdenProduccion($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
          $trans=$Obj->transaction();
          try{
            $sql="INSERT INTO tbl_sgo_ordenproduccion (sgo_int_ordenservicio, sgo_dat_fechacreacion,sgo_int_estado) VALUES (" . $prm . ",'" . date("Y-m-d H:i") . "',1)";
            if($trans->query($sql)){
                $id=mysqli_insert_id($trans);
                $result =$Obj->consulta("SELECT sgo_int_ordenserviciodetalle FROM tbl_sgo_ordenserviciodetalle WHERE sgo_int_ordenservicio=" . $prm);
                while ($row = mysqli_fetch_array($result)){
                   if(!$trans->query("INSERT INTO tbl_sgo_ordenproducciondetalle (sgo_int_ordenproduccion,sgo_int_ordenservicio, sgo_int_ordenserviciodetalle,sgo_dec_cantidadproducida) VALUES (" . $id . "," . $prm . "," . $row['sgo_int_ordenserviciodetalle'] . ",0)"))
                   {throw new Exception($trans->error);}
                }
                $sql= "UPDATE tbl_sgo_ordenservicio SET sgo_int_estado=2 WHERE sgo_int_ordenservicio=" . $prm;
                if($trans->query($sql)){
                  $sql= "UPDATE tbl_sgo_ordenproduccion SET sgo_vch_ordenproduccion='" . date("Y") . str_pad($id,4,'0', STR_PAD_LEFT) . "' WHERE sgo_int_ordenproduccion=" . $id;
                  if($trans->query($sql)){
                      echo $Helper->PopUp("","Notificacion",500,"Se ha generado la &Oacute;rden de Producci&oacute;n \"" . date("Y") . str_pad($id,4,'0', STR_PAD_LEFT) . "\"","");
          //          echo '<script>alert("'.$sql .'")</script>';
                  }
                  else throw new Exception($trans->error);
                }
                else throw new Exception($trans->error);
              }
              else throw new Exception($trans->error);
              $trans->commit();$trans->close();return 1;
          }
          catch(Exception $e)
          {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
      }

/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
      function Obtener_Nombre_OrdenServicio($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT Concat(sgo_vch_serie,'-',sgo_vch_numero) as sgo_vch_nroordenservicio FROM tbl_sgo_ordenservicio WHERE sgo_int_ordenservicio=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_nroordenservicio"];
          }
          return "";
      }
      function Obtener_MontoTotal_OrdenServicio($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT FORMAT(sum(sgo_dec_precio*sgo_dec_cantidad),2) as total FROM tbl_sgo_ordenserviciodetalle WHERE sgo_int_ordenservicio=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["total"];
          }
          return "";
      }
  	  function Obtener_MontoTotal_OrdenServicioSinFormato($prm , $comprobante){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("	SELECT 	sum(sgo_dec_precio*sgo_dec_cantidad) -  
											(
												select ifnull(sum(tbl_sgo_ordenserviciodetalle.sgo_dec_precio*tbl_sgo_ordenserviciodetalle.sgo_dec_cantidad),0)
												FROM 		tbl_sgo_ordenserviciodetalle 
												inner		join tbl_sgo_comprobanteventadetalle
												on			tbl_sgo_ordenserviciodetalle.sgo_int_ordenservicio = tbl_sgo_comprobanteventadetalle.sgo_int_ordenservicio
												WHERE 	tbl_sgo_ordenserviciodetalle.sgo_int_ordenservicio=".$prm."
												and		tbl_sgo_comprobanteventadetalle.sgo_int_comprobanteventa<>".$comprobante."
											)
											as total 
									FROM 		tbl_sgo_ordenserviciodetalle 
									WHERE 	sgo_int_ordenservicio=".$prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["total"];
          }
          return "";
      }
   }
?>