<?php include_once('../../code/lib/htmlhelper.php');
  Class bl_ordencompra
  {
/********************************************************CATÁLOGO ÓRDENES COMPRA**************************************************************************/
      function Filtros_Listar_OrdenesCompra(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Cliente" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"",""),
            "Entrega desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Creación" => $Helper->textdate("fil_txtBusquedaCreacionDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedaCreacionhasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Nro. Orden" => $Helper->textbox("fil_txtBusquedaNroOrden",++$index,"",11,100,$TipoTxt->texto,"","","",""),
            "Estado" => $Helper->combo_estado_ordencompra("fil_cmbBusquedaestadooc",++$index,"",""),
         );
         $buttons=array($Helper->button("btnBuscarOC","Buscar",70,"Buscar_Grilla('ordencompra','Grilla_Listar_OrdenesCompra','tbl_listaroc','','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listaroc",$inputs,$buttons,3,990,"","");
      }
      function Grilla_Listar_OrdenesCompra($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT oc.sgo_int_ordencompra as param, oc.sgo_dat_fecharegistro as Registro,per.sgo_vch_nrodocumentoidentidad as RUC, per.sgo_vch_nombre as Cliente,LPAD(oc.sgo_int_cotizacion,6,'0') as 'Cotización', oc.sgo_vch_nroordencompra as OC,oc.sgo_vch_nroordencompracliente as 'OC Cliente',COUNT(ocd.sgo_int_ocdetalle) as Items,FORMAT(SUM(ocd.sgo_dec_cantidad * ocd.sgo_dec_precio),2) AS Monto,eoc.sgo_vch_descripcion as Estado,
                case when oc.sgo_int_estadooc!=2 then '|PopUp(\'ordencompra\',\'Mant_OrdenProduccion\',\'\',\'|gear.jpg' else '||green_check.gif' end as 'Generar Órden de Producción'
                FROM tbl_sgo_ordencompra oc
                INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=oc.sgo_int_cliente
                INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=cli.sgo_int_cliente
                INNER JOIN tbl_sgo_estadoordencompra eoc on eoc.sgo_int_estadooc=oc.sgo_int_estadooc
                LEFT JOIN tbl_sgo_ordencompradetalle ocd on ocd.sgo_int_ordencompra=oc.sgo_int_ordencompra";
         $groupby = "GROUP BY oc.sgo_int_ordencompra, sgo_dat_fecharegistro,per.sgo_vch_nrodocumentoidentidad, per.sgo_vch_nombre, oc.sgo_int_cotizacion,oc.sgo_vch_nroordencompra,eoc.sgo_vch_descripcion";
         $orderby = "ORDER BY oc.sgo_vch_nroordencompra DESC";
         $where = $Obj->sql_where("WHERE cli.sgo_int_cliente=@p1 and oc.sgo_dat_fechainiciovigencia <= '@p2 00:00' and sgo_dat_fechafinvigencia <= '@p3 23:59' and (oc.sgo_dat_fecharegistro BETWEEN '@p4 00:00' and '@p5 23:59') and oc.sgo_vch_nroordencompra like '%@p6%' and oc.sgo_int_estadooc=@p7 and oc.sgo_int_estadooc!=4",
         $valor[0] . '|' . $Helper->convertir_fecha_ingles($valor[1]) . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $Helper->convertir_fecha_ingles($valor[3]) .'|' . $Helper->convertir_fecha_ingles($valor[4]) . '|' . $valor[5] . '|' . $valor[6]);
         $Helper=new htmlhelper;
         $btn_extra=array();
/*         $btn_extra=array(
            "Presione aquí para imprimir." => "print.png|PopUp('ordencompra','PopUp_Mant_OrdenCompra','','"
         );*/
         /*$btn_colextra=array(
            "Presione aquí para generar Orden de Producción." => "7|Generar Órden de Producción|gear.jpg|PopUp('ordencompra','Mant_OrdenProduccion','','"
         );*/
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $groupby . " " . $orderby),"PopUp('ordencompra','PopUp_Mant_OrdenCompra','','","PopUp('ordencompra','Detalles','','","PopUp('ordencompra','PopUp_Mant_OrdenCompra','','","PopUp('ordencompra','Confirma_Eliminar','','",null,$btn_extra);
      }
      function Detalles_OrdenCompra($prm){
          echo '<script>location.href="oc_detalles.php?app=2&prm=' . $prm . '";</script>';
      }
      function PopUp_Mant_OrdenCompra($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $cliente="0";$oc="";$occliente="";$estado="";$ini="";$fin="";$albaran="";$tiporecepcion="";$dir="";
         $result = $Obj->consulta("SELECT sgo_int_cliente,sgo_vch_nroordencompra,sgo_vch_nroordencompracliente,sgo_int_estadooc,DATE_FORMAT(sgo_dat_fechainiciovigencia, '%d/%m/%Y') as sgo_dat_fechainiciovigencia,DATE_FORMAT(sgo_dat_fechafinvigencia, '%d/%m/%Y') as sgo_dat_fechafinvigencia,sgo_vch_albaran,sgo_int_tiporecepcion,sgo_int_direccion FROM tbl_sgo_ordencompra WHERE sgo_int_ordencompra = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $cliente=$row["sgo_int_cliente"];$oc=$row["sgo_vch_nroordencompra"];$occliente=$row["sgo_vch_nroordencompracliente"];$estado=$row["sgo_int_estadooc"];$ini=$row["sgo_dat_fechainiciovigencia"];$fin=$row["sgo_dat_fechafinvigencia"];$albaran=$row["sgo_vch_albaran"];$tiporecepcion=$row["sgo_int_tiporecepcion"];$dir=$row["sgo_int_direccion"];
            break;
         }
         $Val_Cli=new InputValidacion();
         $Val_Cli->InputValidacion('DocValue("fil_cmbcliente")!=""','Debe especificar el Cliente');
         $Val_Estado=new InputValidacion();
         $Val_Estado->InputValidacion('DocValue("fil_cmbEstadooOC")!=""','Debe especificar el estado de la orden de compra');
         $Val_IniVig=new InputValidacion();
         $Val_IniVig->InputValidacion('DocValue("fil_txtIniVigencia")!=""','Debe especificar el inicio de la fecha de entrega');
         $Val_FinVig=new InputValidacion();
         $Val_FinVig->InputValidacion('DocValue("fil_txtFinVigencia")!=""','Debe especificar el fin de la fecha de entrega');
         $Val_TipoRecepcion=new InputValidacion();
         $Val_TipoRecepcion->InputValidacion('DocValue("fil_cmbTipoRecepcion")!=""','Debe especificar el tipo de recepción');
         $inputs=array(
            "Cliente" => $Helper->combo_cliente("fil_cmbcliente",$index,$cliente,"Cargar_Combo('general','Combo_Direccion_x_Cliente','fil_cmbDireccionOC','fil_cmbcliente','fil_cmbDireccionOC');",$Val_Cli,($prm=="0"?false:true)),
            "Nro OC" => $Helper->textbox("fil_txtnrooc",++$index,$occliente,20,100,$TipoTxt->texto,"","","","",null),
            "Fecha de Entrega" => "Del " . $Helper->textdate("fil_txtIniVigencia",++$index,$ini,false,$TipoDate->fecha,80,"","",$Val_IniVig) . " Al " . $Helper->textdate("fil_txtFinVigencia",++$index,$fin,false,$TipoDate->fecha,80,"","",$Val_FinVig),
            "Nro Albarán" => $Helper->textbox("fil_txtalbaran",++$index,$albaran,20,100,$TipoTxt->numerico,"","","","",null),
            "Tipo Recepción" => $Helper->combo_tiporecepcion("fil_cmbTipoRecepcion",++$index,$tiporecepcion,"",$Val_TipoRecepcion),
            "Direccion" => $Helper->combo_direccion_x_cliente("fil_cmbDireccionOC",++$index,$cliente,$dir,""),
            "Estado" => $Helper->combo_estado_ordencompra("fil_cmbEstadooOC",++$index,$estado,"",$Val_Estado),
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_oc",$inputs,$buttons,2,800,"","");
         $content_tabs = array(); $titulo_tabs = array();
         $content_tabs[0] ="<br/>" . $html;
         $titulo_tabs[0]="Orden Compra";

         $content_tabs[1] ="<br/>";
         $content_tabs[1] .="<div class='left'>Importar Items: " . $Helper->button("","Importar",70,"PopUp('ordencompra','PopUp_Mant_ImportarOrdenCompraDetalle','','" . $prm . "')","") ."</div>";
         $content_tabs[1] .="<br/>";
         $content_tabs[1] .="<div id='div_OrdenesCompra_Detalles'>" . $this->Tab_OrdenesCompra_Detalles($prm,800) . "</div>";
         $titulo_tabs[1]="Detalles";

         $html = $Helper->Crear_Tabs("Tabs_Mant_OrdenCompra",$content_tabs, $titulo_tabs, "","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Orden de Compra",800,$html,$Helper->button("","Grabar",70,"Operacion('ordencompra','Mant_OrdenCompra','tbl_mant_oc','" . $prm . "')","textoInput") . ($prm==0?"<script>Display('Tabs_Mant_OrdenCompra_1','none');</script>":""));
      }
      function PopUp_Mant_ImportarOrdenCompraDetalle($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $cliente="0";
         $result = $Obj->consulta("SELECT sgo_int_cliente FROM tbl_sgo_ordencompra WHERE sgo_int_ordencompra = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $cliente=$row["sgo_int_cliente"]; break;
         }
         $Val_Direccion=new InputValidacion();
         $Val_Direccion->InputValidacion('DocValue("fil_cmbDireccionOC")!=""','Debe especificar la dirección');
         $Val_Xml=new InputValidacion();
         $Val_Xml->InputValidacion('DocValue("fil_fileUpload")!=""','Debe especificar el archivo xml');
         $inputs=array(
            "Direccion" => $Helper->combo_local_x_cliente("fil_cmbDireccionOC",++$index,$cliente,"","",$Val_Direccion),
            "Items"=>$Helper->Upload("fil_fileUpload",++$index,"",450,$Val_Xml) . "*Cargue aqu&iacute; archivos Xml B2B Falabella/CEN"
         );
         $html = $Helper->Crear_Form_Layer("tbl_mant_impooc","ordencompra","Upload_ItemOrdenCompra",$prm,$inputs,array(),1,550,"","");
         return $Helper->PopUp("PopUp_Mant_ImportarOrdenCompraDetalle","Importar Items Orden de Compra",550,$html,$Helper->button("tbl_mant_impooc","Grabar",70,"if(Valida_Datos('tbl_mant_impooc')==0)Obj('form_tbl_mant_impooc').submit()","textoInput"));
      }
      function Mant_OrdenCompra($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0){ $sql= "UPDATE tbl_sgo_ordencompra SET sgo_int_cliente=" . $valor[1] . ",sgo_vch_nroordencompracliente='" . $valor[2] . "',sgo_dat_fecharegistro='" . date("Y-m-d H:i") . "',sgo_dat_fechainiciovigencia='" . $Helper->convertir_fecha_ingles($valor[3]) . "',sgo_dat_fechafinvigencia='" . $Helper->convertir_fecha_ingles($valor[4]) . "',sgo_vch_albaran='" . $valor[5] . "',sgo_int_tiporecepcion=" . $valor[6]  . ($valor[7]==""?"":",sgo_int_direccion=" . $valor[7]) . ",sgo_int_estadooc=" . $valor[8] . " WHERE sgo_int_ordencompra=" . $valor[0];
            return $Obj->execute($sql);
          }
          else{ $sql="INSERT INTO tbl_sgo_ordencompra (sgo_int_cliente, sgo_vch_nroordencompracliente,sgo_dat_fecharegistro,sgo_dat_fechainiciovigencia,sgo_dat_fechafinvigencia,sgo_vch_albaran,sgo_int_tiporecepcion" . ($valor[7]==""?"":",sgo_int_direccion") . ",sgo_int_estadooc) VALUES (" . $valor[1] . ",'" . $valor[2] . "','" . date("Y-m-d H:i") . "','" . $Helper->convertir_fecha_ingles($valor[3]) . "','" . $Helper->convertir_fecha_ingles($valor[4]) . "','" . $valor[5] . "'," . $valor[6] . ($valor[7]==""?null:"," . $valor[7]) . "," . $valor[8] . ")";
//          echo '<script>alert("'.$sql .'")</script>';
              $id=$Obj->execute_insert($sql);
              if($id!=0){
                $sql= "UPDATE tbl_sgo_ordencompra SET sgo_vch_nroordencompra='" . str_pad($id,6,'0', STR_PAD_LEFT) . "' WHERE sgo_int_ordencompra=" . $id;
                $Obj->execute($sql);
                return $id;
              }
              else return -1;
          }
      }
      function Confirma_Eliminar_OrdenCompra($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_nroordencompra FROM tbl_sgo_ordencompra WHERE sgo_int_ordencompra=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
               return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la orden de compra ' . $row["sgo_vch_nroordencompra"] . '?'),$Helper->button("", "Si", 70, "Operacion('ordencompra','Eliminar_OrdenCompra','','" . $prm . "')"));
          }
          return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_OrdenCompra($prm){
          $Obj=new mysqlhelper;
          $result = $Obj->consulta("SELECT sgo_int_cotizacion FROM tbl_sgo_ordencompra WHERE sgo_int_ordencompra = " . $prm);
          $co=0;$resp="<script>Operacion_Result(false);</script>";
          while ($row = mysqli_fetch_array($result))
          {
             $co=$row["sgo_int_cotizacion"];
             break;
          }
          if($Obj->execute("UPDATE tbl_sgo_ordencompra SET sgo_int_estadooc=4 WHERE sgo_int_ordencompra=" . $prm)!=-1){
              if($co!=0){$Obj->execute("UPDATE tbl_sgo_cotizacion SET sgo_int_estadocotizacion=1 WHERE sgo_int_cotizacion=" . $co);}
              return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarOC');</script>";
          }
          else $resp;
      }

/****************************************************************ORDEN COMPRA DETALLE************************************************************************/
      function Tab_OrdenesCompra_Detalles($prm,$size=800){
          $html = $this->Grilla_Listar_OrdenesCompra_Detalles($prm,$size);
          $html .="<b>Monto Total:</b> " . $this->Obtener_MontoTotal_OrdenCompra($prm);
          return $html;
      }
      function Grilla_Listar_OrdenesCompra_Detalles($prm,$size=800){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT ocd.sgo_int_ocdetalle as param, pdt.sgo_vch_nombre as Producto, ocd.sgo_dec_cantidad as Cantidad, ocd.sgo_dec_precio as Precio, Concat(dircli.sgo_vch_codigotienda,' - ',dircli.sgo_vch_nombretienda,' - ',dircli.sgo_vch_direccion) as Destino
          FROM tbl_sgo_ordencompra oc
          INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=oc.sgo_int_cliente
          INNER JOIN tbl_sgo_ordencompradetalle ocd on ocd.sgo_int_ordencompra=oc.sgo_int_ordencompra
          INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=ocd.sgo_int_producto
          LEFT JOIN tbl_sgo_direccioncliente dircli on dircli.sgo_int_direccion=ocd.sgo_int_direccion";
         $where = "WHERE oc.sgo_int_ordencompra=@p1";
         $where = $Obj->sql_where($where,$prm);
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('ordencompra','PopUp_Mant_OrdenCompra_Detalle','','" . $prm . "|","","PopUp('ordencompra','PopUp_Mant_OrdenCompra_Detalle','','" . $prm . "|","PopUp('ordencompra','Confirma_Eliminar_Detalle','','" . $prm . "|",$size,array(),array(),array(),10);
      }
      function PopUp_Mant_OrdenCompra_Detalle($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate;$valor=explode('|',$prm); $index=10;
         $cliente="";$producto="";$precio="0";$cantidad="1";$dir="";$obs="";
         $result = $Obj->consulta("SELECT ocd.sgo_int_producto,ocd.sgo_dec_precio,ocd.sgo_dec_cantidad,ocd.sgo_int_direccion,ocd.sgo_txt_observaciones FROM tbl_sgo_ordencompradetalle ocd WHERE ocd.sgo_int_ocdetalle = " . $valor[1]);
         while ($row = mysqli_fetch_array($result))
         {
            $producto=$row["sgo_int_producto"];$precio=$row["sgo_dec_precio"];$cantidad=$row["sgo_dec_cantidad"];$dir=$row["sgo_int_direccion"];$obs=$row["sgo_txt_observaciones"];
            break;
         }
         $result = $Obj->consulta("SELECT oc.sgo_int_cliente FROM tbl_sgo_ordencompra oc WHERE oc.sgo_int_ordencompra = " . $valor[0]);
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
         $inputs=array(
            "Producto" =>$Helper->hidden("fil_item",$index,"") . $Helper->combo_producto_cliente("fil_cmbproducto",++$index,$cliente,$producto,"Cargar_Input('general','Valor_Producto_Precio','fil_txtPrecio','fil_cmbproducto|fil_cmbcliente','fil_txtPrecio');",$Val_Producto),
            "Cantidad" => $Helper->textbox("fil_txtCantidad",++$index,$cantidad,10,100,$TipoTxt->numerico,"","","","",$Val_Cantidad),
            "Precio" => $Helper->textbox("fil_txtPrecio",++$index,$precio,18,100,$TipoTxt->decimal,"","","","",$Val_Precio),
            "Direccion" => $Helper->combo_local_x_cliente("fil_cmbDireccionCliente",++$index,$cliente,$dir,"",$Val_Direccion),
//            "Archivo" => $Helper->Upload_Cargar("fil_fileUpload_OC_Detalle",++$index,"ordencompra","Upload_OrdenCompra","","","",200),
            "Observaciones " => $Helper->textarea("fil_txtObservacion",++$index,$obs,125,3,"","","") . "~3"
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_ocdetalle",$inputs,$buttons,2,870,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Detalle",870,$html,$Helper->button("","Grabar",70,"Operacion('ordencompra','Mant_OrdenCompra_Detalle','tbl_mant_ocdetalle','" . $prm . "')","textoInput"));
      }
      function Upload_ItemOrdenCompra($id,$oc,$id_direccion,$file){
          include_once ("../lib/uploadhelper.php"); $Up=new uploadhelper;
          $dir="../../archivo/ordencompra/";
          $resp=$Up->form_cargar_archivo($dir,$id,$file,array('.xml'));           //debo cargar el archivo
          if($resp==1)
          {
             include_once ("bl_ordenproduccion.php"); $Obj_Prod=new bl_ordenproduccion;
             $Obj=new mysqlhelper;
             $cliente="0";$result = $Obj->consulta("SELECT oc.sgo_int_cliente FROM tbl_sgo_ordencompra oc WHERE oc.sgo_int_ordencompra = " . $oc);
             while ($row = mysqli_fetch_array($result))
             {
                $cliente=$row["sgo_int_cliente"];
                break;
             }
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
                       $obs=trim($row->column[2]);
                       $obs .= " - " . trim($row->column[3]);
                       $cad .="," . $row->column[4] . "," . $Obj_Prod->Obtener_Producto_Precio($id_prod,$cliente);
                       $cad .="," . $id_direccion . ",'" . $obs . "'),";
                     }
                 }
             }
             if($msj=="")
             {
                 $sql="INSERT INTO tbl_sgo_ordencompradetalle (sgo_int_ordencompra,sgo_vch_item,sgo_int_producto, sgo_dec_cantidad, sgo_dec_precio,sgo_int_direccion,sgo_txt_observaciones) VALUES " . substr($cad,0,strlen($cad)-1);
                 return $Obj->execute($sql);
             }
             else{
               echo "<script>parent.Ver_Mensaje('Importaci&oacute;n de Items Orden de Compra','" . $msj . "','',650);</script>";
               return -1;
             }
          }
          else{ echo $resp; return -1;}
      }
      function Mant_OrdenCompra_Detalle($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[1]!=0) $sql= "UPDATE tbl_sgo_ordencompradetalle SET sgo_vch_item='" . $valor[2] . "',sgo_int_producto=" . $valor[3] . ",sgo_dec_cantidad=" . $valor[4] . ",sgo_dec_precio=" . $valor[5] . ",sgo_int_direccion=" . $valor[6] . ",sgo_txt_observaciones='" . $valor[7] . "' WHERE sgo_int_ocdetalle=" . $valor[1];
          else $sql="INSERT INTO tbl_sgo_ordencompradetalle (sgo_int_ordencompra,sgo_vch_item,sgo_int_producto, sgo_dec_cantidad, sgo_dec_precio,sgo_int_direccion,sgo_txt_observaciones) VALUES (" . $valor[0] . ",'" . $valor[2] . "'," . $valor[3] . "," . $valor[4] . "," . $valor[5] . "," . $valor[6] . ",'" . $valor[7] . "')";
//          echo '<script>alert("'.$sql .'")</script>';
          return $Obj->execute($sql);
      }
      function Confirma_Eliminar_OrdenCompra_Detalle($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT pdt.sgo_vch_nombre FROM tbl_sgo_ordencompradetalle ocd INNER JOIN tbl_sgo_producto pdt ON ocd.sgo_int_producto=pdt.sgo_int_producto WHERE sgo_int_ocdetalle=" . $valor[1]);
          while ($row = mysqli_fetch_array($result))
          {
               return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el producto ' . $row["sgo_vch_nombre"] . '?'),$Helper->button("", "Si", 70, "Operacion('ordencompra','Eliminar_OrdenCompra_Detalle','','" . $prm . "')"));
          }
          return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_OrdenCompra_Detalle($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          $sql="DELETE FROM tbl_sgo_ordencompradetalle WHERE sgo_int_ocdetalle=" . $valor[1];
//          echo '<script>alert("'.$sql .'")</script>';
          return $Obj->execute($sql);
      }

/****************************************************************ORDEN COMPRA DETALLE PRODUCTOS************************************************************************/
      function Grilla_Listar_OrdenesCompra_Productos($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT ocd.sgo_int_ocdetalle as param, Concat(DATE_FORMAT(sgo_dat_fechainiciovigencia, '%d/%m/%Y'),' - ',DATE_FORMAT(sgo_dat_fechafinvigencia, '%d/%m/%Y')) as Fch_Vigencia,ocd.sgo_vch_item as Item,pdt.sgo_vch_nombre as Producto, ocd.sgo_dec_cantidad as Cantidad, ocd.sgo_dec_precio as Precio, Concat(dircli.sgo_vch_codigotienda,' - ',dircli.sgo_vch_nombretienda,' - ',dircli.sgo_vch_direccion) as Destino
          FROM tbl_sgo_ordencompra oc
          INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=oc.sgo_int_cliente
          INNER JOIN tbl_sgo_ordencompradetalle ocd on ocd.sgo_int_ordencompra=oc.sgo_int_ordencompra
          INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=ocd.sgo_int_producto
          LEFT JOIN tbl_sgo_direccioncliente dircli on dircli.sgo_int_direccion=ocd.sgo_int_direccion";
         $where = $Obj->sql_where("WHERE oc.sgo_int_ordencompra=@p1",$prm);
         $orderby = " ORDER BY pdt.sgo_vch_nombre ASC";
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"","","","");
      }

/****************************************************************ORDEN COMPRA DETALLE************************************************************************/
      function Mant_OrdenProduccion($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
          $trans=$Obj->transaction();
          try{
            $sql="INSERT INTO tbl_sgo_ordenproduccion (sgo_int_ordencompra, sgo_dat_fechacreacion,sgo_int_estado) VALUES (" . $prm . ",'" . date("Y-m-d H:i") . "',1)";
            if($trans->query($sql)){
                $id=mysqli_insert_id($trans);
                $result =$Obj->consulta("SELECT sgo_int_ocdetalle FROM tbl_sgo_ordencompradetalle WHERE sgo_int_ordencompra=" . $prm);
                while ($row = mysqli_fetch_array($result)){
                   if(!$trans->query("INSERT INTO tbl_sgo_ordenproducciondetalle (sgo_int_ordenproduccion,sgo_int_ordencompra, sgo_int_ocdetalle,sgo_dec_cantidadproducida) VALUES (" . $id . "," . $prm . "," . $row['sgo_int_ocdetalle'] . ",0)"))
                   {throw new Exception($trans->error);}
                }
                $sql= "UPDATE tbl_sgo_ordencompra SET sgo_int_estadooc=2 WHERE sgo_int_ordencompra=" . $prm;
                if($trans->query($sql)){
                  $sql= "UPDATE tbl_sgo_ordencompra SET sgo_int_estadooc=2 WHERE sgo_int_ordencompra=" . $prm;
                  if($trans->query($sql)){
                    $sql= "UPDATE tbl_sgo_ordenproduccion SET sgo_vch_ordenproduccion='" . date("Y") . str_pad($id,4,'0', STR_PAD_LEFT) . "' WHERE sgo_int_ordenproduccion=" . $id;
                    if($trans->query($sql)){
                        echo $Helper->PopUp("","Notificación",500,"Se ha generado la &Oacute;rden de Producci&oacute;n \"" . date("Y") . str_pad($id,4,'0', STR_PAD_LEFT) . "\"","");
            //          echo '<script>alert("'.$sql .'")</script>';
                    }
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

/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
      function Obtener_Nombre_OrdenCompra($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_nroordencompra FROM tbl_sgo_ordencompra WHERE sgo_int_ordencompra=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_nroordencompra"];
          }
          return "";
      }
      function Obtener_MontoTotal_OrdenCompra($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT FORMAT(sum(sgo_dec_precio*sgo_dec_cantidad),2) as total FROM tbl_sgo_ordencompradetalle WHERE sgo_int_ordencompra=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["total"];
          }
          return "";
      }
   }
?>