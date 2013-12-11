<?php include_once('../../code/lib/htmlhelper.php');
  Class bl_kardex
  {
/********************************************************CATALOGO KARDEX**************************************************************************/
      function Filtros_Listar_Kardex(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=10;
         $inputs=array(
            "Artículo" => $Helper->textbox("fil_cmbBusquedaProducto",$index,"",200,250,$TipoTxt->texto,"","","",""),
            "Categoría" => $Helper->combo_categoriaproducto("fil_txtBusquedaCategoriaProducto",++$index,"","")
         );
         $buttons=array($Helper->button("btnBuscarProductos","Buscar",70,"Buscar_Grilla('kardex','Grilla_Listar_Kardex','tbl_listarkardex','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarkardex",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Kardex($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
/*         $sql ="SELECT pdt.sgo_int_producto as param, categoria.sgo_vch_descripcion as 'Categoría', pdt.sgo_vch_nombre as 'Artículo', unidad.sgo_vch_abreviatura as 'Und. Medida', pdt.sgo_dec_stock as Stock, pdt.sgo_dec_stockminimo as 'Stock Mínimo', tar.sgo_dec_precio as 'Precio', (tar.sgo_dec_precio * pdt.sgo_dec_stock) as Valorizado,
                '|PopUp(\'kardex\',\'PopUp_Mant_KardexEntrada\',\'\',\'|insert_table_row.png' as 'Entrada',
                '|PopUp(\'kardex\',\'PopUp_Mant_KardexSalida\',\'\',\'|removecell.png' as 'Salida'
                FROM tbl_sgo_producto pdt
                LEFT JOIN tbl_sgo_tarifario tar ON tar.sgo_int_producto=pdt.sgo_int_producto and tar.sgo_int_cliente=0
                INNER JOIN tbl_sgo_categoriaproducto categoria on pdt.sgo_int_categoriaproducto = categoria.sgo_int_categoriaproducto
                INNER JOIN tbl_sgo_unidadmedida unidad on pdt.sgo_int_unidadmedida = unidad.sgo_int_unidadmedida";*/
        $sql ="SELECT pdt.sgo_int_producto as param, categoria.sgo_vch_descripcion as 'Categoría', pdt.sgo_vch_nombre as 'Artículo', unidad.sgo_vch_abreviatura as 'Und. Medida',pdt.sgo_dec_stock as Stock, pdt.sgo_dec_stockminimo as 'Stock Mínimo', tar.sgo_dec_precio as 'Precio', (tar.sgo_dec_precio * pdt.sgo_dec_stock) as Valorizado
                FROM tbl_sgo_producto pdt
                LEFT JOIN tbl_sgo_tarifario tar ON tar.sgo_int_producto=pdt.sgo_int_producto and tar.sgo_int_cliente=0
                INNER JOIN tbl_sgo_categoriaproducto categoria on pdt.sgo_int_categoriaproducto = categoria.sgo_int_categoriaproducto
                INNER JOIN tbl_sgo_unidadmedida unidad on pdt.sgo_int_unidadmedida = unidad.sgo_int_unidadmedida";
         $where = $Obj->sql_where("WHERE pdt.sgo_vch_nombre like '%@p1%' and pdt.sgo_int_categoriaproducto=@p2 and pdt.sgo_bit_activo = 1",$prm);
         $order = "ORDER BY pdt.sgo_vch_nombre ASC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $order),"","PopUp('kardex','PopUp_Detalles_Kardex','','","","",null,array(),array(),array(),20,"");
      }
      function PopUp_Detalles_Kardex($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="(SELECT kde.sgo_int_kardexentrada as param, und.sgo_vch_abreviatura as 'Und. Medida',
                kde.sgo_dec_saldo as Saldo,kde.sgo_dec_cantidad as Ingreso, '' as Salida, kde.sgo_dec_stock as Stock,kde.sgo_dat_fechaentrada as 'Fecha Movimiento',kde.sgo_txt_observaciones as Observacion
                FROM tbl_sgo_kardexentrada kde
                LEFT JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto = kde.sgo_int_producto
                LEFT JOIN tbl_sgo_unidadmedida und ON und.sgo_int_unidadmedida=pdt.sgo_int_unidadmedida
                WHERE pdt.sgo_int_producto=" . $prm . ")
                UNION ALL
                (SELECT kds.sgo_int_kardexsalida as param,  und.sgo_vch_abreviatura as 'Und. Medida',
                kds.sgo_dec_saldo as Saldo,'' as Entrada, kds.sgo_dec_cantidad as Salida, kds.sgo_dec_stock as Stock, kds.sgo_dat_fechasalida as 'Fecha Movimiento',kds.sgo_txt_observaciones as Observacion
                FROM tbl_sgo_kardexsalida kds
                LEFT JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto = kds.sgo_int_producto
                LEFT JOIN tbl_sgo_unidadmedida und ON und.sgo_int_unidadmedida=pdt.sgo_int_unidadmedida
                WHERE pdt.sgo_int_producto=" . $prm . ") ORDER BY 7 DESC";
         $html=$Helper->Imprimir_Grilla($Obj->consulta($sql),"","","","",800,array(),array(),array(),20,"");
         return $Helper->PopUp("","Kardex",600,$html,"","textoInput");
      }
      function PopUp_Mant_KardexEntrada($prm){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$index=10;
         $Val_Cantidad=new InputValidacion();
         $Val_Cantidad->InputValidacion('DocValue("fil_txtcantidad")!="" && parseFloat(DocValue("fil_txtcantidad"))>0','Debe especificar una cantidad mayor a 0.');
         $inputs=array(
            "Cantidad de Entrada" => $Helper->textbox("fil_txtcantidad",$index,"",10,100,$TipoTxt->decimal,"","","","",$Val_Cantidad),
            "Observaciones" => $Helper->textarea("fil_txtobservaciones",++$index,"",50,3,"","","")
         );
         $html = $Helper->Crear_Layer("tbl_mant_kardex",$inputs,array(),1,600,"","");
         return $Helper->PopUp("","Kardex de Entrada",600,"<div class='error' align='left'>" . htmlentities(" * Este ingreso no realizara una salida de inventario de insumos (segun formula de produccion)") . "</div><br/>" . $html,$Helper->button("","Grabar",70,"Operacion('kardex','Mant_KardexEntrada','tbl_mant_kardex','" . $prm . "')","textoInput"));
      }
      function Mant_KardexEntrada($prm,$oc=0,$ref_trans=null){
         $Obj=new mysqlhelper;$valor=explode('|',$prm);
         $trans=($ref_trans==null?$Obj->transaction():$ref_trans);
         try{
           //Agregar la cantidad de productos al stock
           //Si provienen de una orden de compra (oc) no lo agrega al campo sgo_dec_stocklibre (puesto que le pertenece a esa oc), de otro modo lo suma tambien a sgo_dec_stocklibre
           $sql="UPDATE tbl_sgo_producto SET sgo_dec_stock=(sgo_dec_stock + " . $valor[1] . ")" . ($oc===0?",sgo_dec_stocklibre=(sgo_dec_stocklibre + " . $valor[1] . ")":"") . " WHERE sgo_int_producto=" . $valor[0];
           if($trans->query($sql)){
              $stock=$this->Obtener_StockTotal_producto($valor[0]);
              //Realiza un ingreso de productos al kardex
              $sql="INSERT INTO tbl_sgo_kardexentrada (sgo_int_producto, sgo_dec_cantidad, sgo_dec_saldo,sgo_dec_stock,sgo_txt_observaciones, sgo_dat_fechaentrada, sgo_bit_activo) VALUES (" . $valor[0] . "," . $valor[1] . "," . $stock . "," . ($stock + $valor[1]) . ",'" . $valor[2] . "','" . date("Y-m-d H:i:s") . "',1)";
              if($trans->query($sql)){
                  //Si el ingreso proviene de una orden de produccion entonces se deben descontar los insumos
                  if($oc===1){
                      $sql="SELECT pfo.sgo_int_insumo,pfo.sgo_dec_cantidad,pdt.sgo_vch_nombre
                            FROM tbl_sgo_productoformula pfo INNER JOIN tbl_sgo_producto pdt ON pfo.sgo_int_producto=pdt.sgo_int_producto
                            WHERE pfo.sgo_int_producto =" . $valor[0];
                      $result=$Obj->consulta($sql);
                      //Realiza una salida de inventario de los insumos del producto
                      while ($row = mysqli_fetch_array($result)){
                          if($this->Mant_KardexSalida($row["sgo_int_insumo"] . '|' . ($valor[1] * $row["sgo_dec_cantidad"]) . '|Salida de inventario para la produccion de ' . $row["sgo_dec_cantidad"] . ' ' . $row["sgo_vch_nombre"] . '(s)',$trans)==-1)
                          {throw new Exception("");}
                      }
                  }
                  if($ref_trans==null){ $trans->commit();$trans->close(); }
                  return 1;
              }
              else throw new Exception($sql);
           }
           else throw new Exception($sql);
         }
         catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            if($ref_trans==null){ $trans->rollback();$trans->close();} return -1;
//             throw new Exception("insert name again",0,$e);
         }
          //echo '<script>alert("'.$sql .'")</script>';
      }
      function PopUp_Mant_KardexSalida($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$index=10;$stock=0;
         $result = $Obj->consulta("SELECT sgo_dec_stock FROM tbl_sgo_producto WHERE sgo_int_producto=" . $prm);
         while ($row = mysqli_fetch_array($result))
         {
             $stock=$row["sgo_dec_stock"];
         }
         $Val_Cantidad=new InputValidacion();
         $Val_Cantidad->InputValidacion('DocValue("fil_txtcantidad")!="" && parseFloat(DocValue("fil_txtcantidad"))>0 && parseFloat(DocValue("fil_txtcantidad"))<=' . $stock,'Debe especificar una cantidad mayor a 0 y no menor al stock actual de ' . $stock);
         $inputs=array(
            "Cantidad de Salida" => $Helper->textbox("fil_txtcantidad",$index,"",10,100,$TipoTxt->decimal,"","","","",$Val_Cantidad),
            "Observaciones" => $Helper->textarea("fil_txtobservaciones",++$index,"",50,3,"","","")
         );
         $html = $Helper->Crear_Layer("tbl_mant_kardex",$inputs,array(),1,600,"","");
         return $Helper->PopUp("","Kardex de Salida",600,$html,$Helper->button("","Grabar",70,"Operacion('kardex','Mant_KardexSalida','tbl_mant_kardex','" . $prm . "')","textoInput"));
      }
      function Mant_KardexSalida($prm,$ref_trans=null){
         $Obj=new mysqlhelper;$valor=explode('|',$prm);
         $trans=($ref_trans==null?$Obj->transaction():$ref_trans);
         try{
            //Registra la salida del producto del stock
            $sql="UPDATE tbl_sgo_producto SET sgo_dec_stock=(sgo_dec_stock - " . $valor[1] . "),sgo_dec_stocklibre=(sgo_dec_stocklibre - " . $valor[1] . ") WHERE sgo_int_producto=" . $valor[0];
            if($trans->query($sql)){$stock=$this->Obtener_StockTotal_producto($valor[0]);
                //Registra la salida del producto del kardex
                $sql="INSERT INTO tbl_sgo_kardexsalida (sgo_int_producto, sgo_dec_cantidad,sgo_dec_saldo,sgo_dec_stock, sgo_txt_observaciones, sgo_dat_fechasalida, sgo_bit_activo) VALUES (" . $valor[0] . "," . $valor[1] . "," . $stock . "," . ($stock - $valor[1]) . ",'" . $valor[2] . "','" . date("Y-m-d H:i:s") . "',1)";
                if($trans->query($sql)){ if($ref_trans==null){ $trans->commit();$trans->close(); }return 1;}
                else throw new Exception($sql);
            }
            else throw new Exception($sql);
         }
         catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            if($ref_trans==null){ $trans->rollback();$trans->close();} return -1;
//             throw new Exception("insert name again",0,$e);
         }
      }
      function Mant_KardexReserva($prm,$ref_trans=null){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          //Registra la salida del producto del stock libre
          $trans=($ref_trans==null?$Obj->transaction():$ref_trans);
          $sql="UPDATE tbl_sgo_producto SET sgo_dec_stocklibre=(sgo_dec_stocklibre - " . $valor[1] . ") WHERE sgo_int_producto=" . $valor[0];
          if($ref_trans==null){
            $Obj=new mysqlhelper; return $Obj->execute($sql);
          }
          else{
            if($ref_trans->query($sql)) return 1;
            else return -1;
          }
      }
      function Mant_Quitar_KardexReserva($prm,$ref_trans=null){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          //Registra la salida del producto del stock libre
          $trans=($ref_trans==null?$Obj->transaction():$ref_trans);
          $sql="UPDATE tbl_sgo_producto SET sgo_dec_stocklibre=(sgo_dec_stocklibre + " . $valor[1] . ") WHERE sgo_int_producto=" . $valor[0];
          if($ref_trans==null){
            $Obj=new mysqlhelper; return $Obj->execute($sql);
          }
          else{
            if($ref_trans->query($sql)) return 1;
            else return -1;
          }
      }

/********************************************************INVENTARIO**************************************************************************/
      function Filtros_Listar_Inventario(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $inputs=array(
            "Nro Serie" => $Helper->textbox("fil_cmbBusquedaSerie",$index,"",3,70,$TipoTxt->texto,"","","",""),
            "Numero" => $Helper->textbox("fil_cmbBusquedaNumero",$index,"",15,100,$TipoTxt->texto,"","","",""),
            "Registro desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Almacen" => $Helper->combo_almacen("fil_txtBusquedaAlmacen",++$index,"","",""),
            "Tipo Inventario" => $Helper->combo_tipoinventario("fil_txtBusquedatipo",++$index,"","","")
         );
         $buttons=array($Helper->button("btnBuscar","Buscar",70,"Buscar_Grilla('kardex','Grilla_Listar_Inventario','tbl_listar','','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listar",$inputs,$buttons,2,990,"","");
      }
      function Grilla_Listar_Inventario($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT inv.sgo_int_inventario as param,Concat(inv.sgo_vch_serie,'-',inv.sgo_vch_numero) as 'Inventario',DATE_FORMAT(inv.sgo_dat_registro,'%d/%m/%Y') as 'Fecha Registro',alm.sgo_vch_nombre as 'Almacen',tco.sgo_vch_descripcion as 'Tipo Inventario',
                CASE inv.sgo_bit_finalizado WHEN 1 THEN 'Finalizado' ELSE '** Pendiente **' END AS Estado,
                CASE inv.sgo_bit_activo WHEN 1 THEN '' ELSE '** Anulada **' END AS Anulada
                FROM tbl_sgo_inventario inv
                INNER JOIN tbl_sgo_almacen alm ON alm.sgo_int_almacen=inv.sgo_int_almacen
                INNER JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=inv.sgo_int_tipocomprobante";
         $where = $Obj->sql_where("WHERE inv.sgo_vch_serie like '%@p1%' and inv.sgo_vch_numero like '%@p2%' and (inv.sgo_dat_registro BETWEEN '@p3 00:00' and '@p4 23:59') and alm.sgo_int_almacen=@p5 and tco.sgo_int_tipocomprobante=@p6",$prm);
         $order = "ORDER BY inv.sgo_dat_registro DESC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $order),"PopUp('kardex','PopUp_Mant_Inventario','','","","PopUp('kardex','PopUp_Mant_Inventario','','","PopUp('kardex','Confirma_Eliminar_Inventario','','",null,array(),array(),array(),20,"");
      }
      function PopUp_Grilla_Listar_Productos($size=700){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT pdt.sgo_int_producto as param, pdt.sgo_vch_nombre as Item,und.sgo_vch_abreviatura as 'Und Medida',pdt.sgo_dec_stock as 'Stock Actual',tpdt.sgo_vch_descripcion as 'Tipo'
                FROM tbl_sgo_producto pdt
                INNER JOIN tbl_sgo_unidadmedida und ON und.sgo_int_unidadmedida=pdt.sgo_int_unidadmedida
                INNER JOIN tbl_sgo_tipoproducto tpdt ON tpdt.sgo_int_tipoproducto=pdt.sgo_int_tipo";
         $orderby = "ORDER BY pdt.sgo_vch_nombre ASC";
         $botones=new GrillaBotones;
         $botones->GrillaBotones("","","","","1");
         $html=$Helper->Crear_Grilla($Obj->consulta($sql . " " . $orderby),"tbl_producto",$botones,$size,array(),array(),array(),10,"") . "</div><script>Carga_Js('../../js/jscript_almacen.js')</script>";
         return $Helper->PopUp("","Listado de Artículos",$size + 50,$html,$Helper->button("","Aceptar",70,"Agregar_Articulos('tbl_producto','tbl_inventario_items');Cerrar_PopUp('PopUp@')","textoInput"));
      }
      function Grilla_Listar_Inventario_Items($prm,$size=1000,$estado){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT invdet.sgo_int_producto as param, pdt.sgo_vch_nombre as Item,und.sgo_vch_abreviatura as 'Und Medida',pdt.sgo_dec_stock as 'Stock Actual',invdet.sgo_dec_stockreal as 'Stock Real',invdet.sgo_vch_comentario as Comentario
                FROM tbl_sgo_producto pdt
                INNER JOIN tbl_sgo_inventariodetalle invdet ON invdet.sgo_int_producto=pdt.sgo_int_producto
                INNER JOIN tbl_sgo_inventario inv on inv.sgo_int_inventario=invdet.sgo_int_inventario
                INNER JOIN tbl_sgo_unidadmedida und ON und.sgo_int_unidadmedida=pdt.sgo_int_unidadmedida";
         $where = $Obj->sql_where("WHERE inv.sgo_int_inventario=@p1",$prm);
         $orderby = "ORDER BY invdet.sgo_int_inventariodetalle ASC";
         $input_extra=array(
              "" => '0|' . $Helper->hidden("fil_gcol_hf_id","num_posicion","num_valor"),
//              "Stock Real" => '4|' . $Helper->textbox("fil_gcol_txtStock","num_posicion","0",10,100,$TipoTxt->decimal,"","","",""),
           );
         $botones=new GrillaBotones;
         if($estado=="0"){
           $descript_cols=array(
              $Helper->label("fil_gcol_txtStockActual","num_posicion","num_valor",70) => 3,
              $Helper->textbox("fil_gcol_txtStockReal","num_posicion","num_valor",10,70,$TipoTxt->decimal,"","","","") => 4,
              $Helper->textbox("fil_grow_txtComentario","num_posicion","num_valor",250,400,$TipoTxt->texto,"","","","") => 5
           );
           $botones->GrillaBotones("PopUp('kardex','PopUp_Grilla_Listar_Productos','tbl_mant_inventario','","","","Delete_Row(this.parentNode.parentNode)","");
         }
         else{
           $descript_cols=array(
              $Helper->label("fil_gcol_txtStockActual","num_posicion","num_valor",70) => 3,
              $Helper->label("fil_gcol_txtStockReal","num_posicion","num_valor",70) => 4,
              $Helper->label("fil_grow_txtComentario","num_posicion","num_valor",400) => 5,
           );
         }
         return $Helper->Crear_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"tbl_inventario_items",$botones,$size,array(),$input_extra,$descript_cols,20,"");
      }
      function PopUp_Mant_Inventario($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $categoria="";$serie="";$numero="";$almacen="";$tipo="";$estado="0";$obs="";
         $result = $Obj->consulta("SELECT sgo_vch_serie,sgo_vch_numero,sgo_int_almacen,sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_bit_finalizado,sgo_vch_observacion FROM tbl_sgo_inventario WHERE sgo_int_inventario = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $serie=$row["sgo_vch_serie"];$numero=$row["sgo_vch_numero"];$almacen=$row["sgo_int_almacen"];$tipo=$row["sgo_int_tipocomprobante"];
            $estado=$row["sgo_bit_finalizado"];$obs=$row["sgo_vch_observacion"];$categoria=$row["sgo_int_categoriacomprobante"];
            break;
         }
         $grilla="<div id='div_inventario_items' style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_Inventario_Items($prm,1000,$estado) . "</div>";
         $disabled=($estado=="0"?false:true);
         $Val_Serie=new InputValidacion();
         $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
         $Val_Numero=new InputValidacion();
         $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
         $Val_Almacen=new InputValidacion();
         $Val_Almacen->InputValidacion('DocValue("fil_cmbalmacen")!=""','Debe especificar el almacen');
         $Val_TipoComprobante=new InputValidacion();
         $Val_TipoComprobante->InputValidacion('DocValue("fil_cmbtipocomprobante")!=""','Debe especificar el tipo de comprobante de referencia');
         $Val_Categoria=new InputValidacion();
         $Val_Categoria->InputValidacion('DocValue("fil_cmbcategoriacomprobante")!=""','Debe especificar la categoría');
         $inputs=array(
            "Tipo Comprobante" => $Helper->combo_tipocomprobante("fil_cmbtipocomprobante",++$index,"11",$tipo,"Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_TipoComprobante,$disabled),
            "Categoría" => $Helper->combo_categoriacomprobante("fil_cmbcategoriacomprobante",++$index,"11",$categoria,"",$Val_Categoria,$disabled),
            "Nro Inventario" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,50,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . "-" . $Helper->textbox("fil_txtNroNumero",$index,$numero,10,100,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
            "Almacen" => $Helper->combo_almacen("fil_cmbalmacen",++$index,$almacen,"",$Val_Almacen,$disabled),
            "Estado" => $Helper->combo_estado_finalizado("fil_txtNroComprobante",++$index,$estado,"",null,$disabled),
            ""=>"",
            "Items"=>$grilla . "~5",
            "Observacion"=>$Helper->textarea("fil_txtobservacion",++$index,$obs,166,3,"","","","",null,$disabled) . "~5"
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_inventario",$inputs,$buttons,3,1100,"","");
         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Inventario",1150,$html,($estado=="0"?$Helper->button("","Grabar",70,"Operacion('kardex','Mant_Inventario','tbl_mant_inventario','" . $prm . "')","textoInput"):"")) . "<script>Focus('fil_cmbcategoriacomprobante')</script>";
      }
      function Mant_Inventario($prm){
         $Obj=new mysqlhelper;$Helper= new htmlhelper;$valor=explode('|',$prm);
         $trans=$Obj->transaction();
         include('../../code/bl/bl_general.php'); $Obj_general=new bl_general;
         $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante($valor[2]);
         try{
             if($valor["0"]=="0"){
                 $correlativo=$Obj_general->Obtener_Correlativo_Comprobante($valor[1],$trans);
                 if($correlativo==-1) throw new Exception("Error: Obtener_Correlativo_Comprobante");
                 $sql="INSERT INTO tbl_sgo_inventario (sgo_int_tipocomprobante,sgo_int_categoriacomprobante,sgo_vch_serie,sgo_vch_numero,sgo_int_almacen,sgo_bit_finalizado,sgo_vch_observacion,sgo_dat_registro,sgo_bit_activo)
                 VALUES (" . $valor[1] . "," . $valor[2] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[5] . "," . $valor[6] . ",'" . $valor[7] . "','" . date("Y-m-d H:i") . "',1)";
               }
               else{
                  $sql="UPDATE tbl_sgo_inventario SET sgo_int_tipocomprobante=" . $valor[1] . ",sgo_int_categoriacomprobante=" . $valor[2] . ",sgo_vch_serie='" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "',sgo_vch_numero='" . str_pad($valor[4],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "',
                  sgo_int_almacen=" . $valor[5] . ",sgo_bit_finalizado=" . $valor[6] . ",sgo_vch_observacion='" . $valor[7] . "'
                  WHERE sgo_int_inventario=" . $valor[0];
              }
              if($trans->query($sql))
              {
                  $id=($valor["0"]=="0"?mysqli_insert_id($trans):$valor[0]);
                  if($valor["0"]!="0")
                  {
                     $sql="DELETE FROM tbl_sgo_inventariodetalle WHERE sgo_int_inventario=" . $id;
                     if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                  }
                  else{
                    if($Obj_general->Generar_Numeracion_Comprobante($valor[1],$trans)==-1)throw new Exception("Error: Generar_Numeracion_Comprobante");
                  }
                  $valor_item=explode('_',$valor[8]);
                  $valortotal=0;
                  foreach ($valor_item as $k) {
                     $valor_col=explode('~',$k);
                     $sql="INSERT INTO tbl_sgo_inventariodetalle (sgo_int_inventario,sgo_int_producto,sgo_dec_stock,sgo_dec_stockreal,sgo_vch_comentario,sgo_bit_cuadrado)
                      VALUES (" . $id . "," . $valor_col[0] . "," . $valor_col[1] . "," . $valor_col[2] . ",'" . $valor_col[3] . "'," . (floatval($valor_col[2])==floatval($valor_col[3])?"1":"0") . ")";
                     if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                  }
                  //Al cerrarse un inventario inicial se deben crear los productos y su stock en la tabla de productos-almacen.
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
      function Confirma_Eliminar_Inventario($prm){
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmacion",450,htmlentities('¿Esta seguro de anular el inventario ' . $this->Obtener_Nombre_Inventario($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('kardex','Eliminar_Inventario','','" . $prm . "')"));
      }
      function Eliminar_Inventario($prm){
          $Obj=new mysqlhelper;
          $sql="UPDATE tbl_sgo_inventario SET sgo_bit_activo=0 WHERE sgo_int_inventario=" . $prm;
          return $Obj->execute($sql);
      }

/********************************************************GUIAS DE ENTRADA**************************************************************************/
      function Filtros_Listar_GuiaEntrada(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $inputs=array(
            "Nro Serie" => $Helper->textbox("fil_cmbBusquedaSerie",$index,"",3,70,$TipoTxt->texto,"","","",""),
            "Numero" => $Helper->textbox("fil_cmbBusquedaNumero",$index,"",15,100,$TipoTxt->texto,"","","",""),
            "Registro desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Almacen" => $Helper->combo_almacen("fil_txtBusquedaAlmacen",++$index,"","",""),
            "Proveedor" => $Helper->combo_proveedor("fil_txtBusquedaProveedor",++$index,"","",""),
            "Tipo Comprobante" => $Helper->combo_tipocomprobante("fil_txtBusquedaTipoComprobante",++$index,"","","")
         );
         $buttons=array($Helper->button("btnBuscarGuia","Buscar",70,"Buscar_Grilla('kardex','Grilla_Listar_GuiaEntrada','tbl_listarguia','','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listarguia",$inputs,$buttons,2,990,"","");
      }
      function Grilla_Listar_GuiaEntrada($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT agi.sgo_int_almacenguiaingreso as param,Concat(agi.sgo_vch_serie,'-',agi.sgo_vch_numero) as 'Comprobante',alm.sgo_vch_nombre as 'Almacen',per.sgo_vch_nombre as 'Proveedor',tco.sgo_vch_descripcion as 'Tipo Comp. Referencia',agi.sgo_vch_nrocomprobante as 'Comp. Referencia',
                CASE agi.sgo_bit_activo WHEN 1 THEN '' ELSE '** Anulada **' END AS Anulada
                FROM tbl_sgo_almacenguiaingreso agi
                INNER JOIN tbl_sgo_persona per ON per.sgo_int_persona=agi.sgo_int_proveedor
                INNER JOIN tbl_sgo_almacen alm ON alm.sgo_int_almacen=agi.sgo_int_almacen
                INNER JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=agi.sgo_int_tipocomprobante";
         $where = $Obj->sql_where("WHERE agi.sgo_vch_serie like '%@p1%' and agi.sgo_vch_numero like '%@p2%' and (agi.sgo_dat_fecharegistro BETWEEN '@p3 00:00' and '@p4 23:59') and alm.sgo_int_almacen=@p5 and per.sgo_int_persona=@p6 and tco.sgo_int_tipocomprobante=@p7",$prm);
         $order = "ORDER BY agi.sgo_dat_fecharegistro DESC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $order),"PopUp('kardex','PopUp_Mant_GuiaEntrada','','","PopUp('kardex','PopUp_Mant_GuiaEntrada','','","","PopUp('kardex','Confirma_Eliminar_GuiaEntrada','','",null,array(),array(),array(),20,"");
      }
      function Grilla_Listar_GuiaEntrada_Items($prm,$size=1000){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT gsd.sgo_int_almacenguiaingresodetalle as param, pdt.sgo_vch_nombre as Item,und.sgo_vch_abreviatura as 'Und Medida'," . ($prm==0?"pdt.sgo_dec_stock as Stock,":"") . "gsd.sgo_dec_cantidad as 'Ingreso'
                FROM tbl_sgo_almacenguiaingreso agi
                INNER JOIN tbl_sgo_almacenguiaingresodetalle gsd ON gsd.sgo_int_almacenguiaingreso=agi.sgo_int_almacenguiaingreso
                INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=gsd.sgo_int_producto
                INNER JOIN tbl_sgo_unidadmedida und ON und.sgo_int_unidadmedida=pdt.sgo_int_unidadmedida";
         $where = $Obj->sql_where("WHERE gsd.sgo_int_almacenguiaingreso=@p1",$prm);
         $orderby = "ORDER BY gsd.sgo_int_almacenguiaingresodetalle ASC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"Nuevo_AlmacenGuiaDetalle('tbl_mant_guia',this.parentNode,1);","","","",$size,array(),array(),array(),20,"") . "<script>Carga_Js('../../js/jscript_almacen.js')</script>";
      }
      function PopUp_Mant_GuiaEntrada($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $serie="";$numero="";$almacen="";$proveedor="";$tipo="";$compreferencia="";$fecha=date("d/m/Y");$motivo="";
         $result = $Obj->consulta("SELECT sgo_vch_serie,sgo_vch_numero,sgo_int_almacen,sgo_int_proveedor,sgo_int_tipocomprobante,sgo_vch_nrocomprobante,DATE_FORMAT(sgo_dat_fechamovimiento,'%d/%m/%Y') as sgo_dat_fechamovimiento,sgo_vch_motivo FROM tbl_sgo_almacenguiaingreso WHERE sgo_int_almacenguiaingreso = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $serie=$row["sgo_vch_serie"];$numero=$row["sgo_vch_numero"];$almacen=$row["sgo_int_almacen"];$proveedor=$row["sgo_int_proveedor"];$tipo=$row["sgo_int_tipocomprobante"];
            $compreferencia=$row["sgo_vch_nrocomprobante"];$motivo=$row["sgo_vch_motivo"];$fecha=$row["sgo_dat_fechamovimiento"];
            break;
         }
         $grilla="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_GuiaEntrada_Items($prm) . "</div>";
         $disabled=($prm=="0"?false:true);
         $Val_Serie=new InputValidacion();
         $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
         $Val_Numero=new InputValidacion();
         $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
         $Val_Almacen=new InputValidacion();
         $Val_Almacen->InputValidacion('DocValue("fil_cmbalmacen")!=""','Debe especificar el almacen');
         $Val_Proveedor=new InputValidacion();
         $Val_Proveedor->InputValidacion('DocValue("fil_cmbproveedor")!=""','Debe especificar el proveedor');
         $Val_TipoComprobante=new InputValidacion();
         $Val_TipoComprobante->InputValidacion('DocValue("fil_cmbtipocomprobante")!=""','Debe especificar el tipo de comprobante de referencia');
         $Val_NroComprobante=new InputValidacion();
         $Val_NroComprobante->InputValidacion('DocValue("fil_txtNroComprobante")!=""','Debe especificar el numero de comprobante de referencia');
         $Val_Fecha=new InputValidacion();
         $Val_Fecha->InputValidacion('DocValue("fil_txtFechaMovimiento")!=""','Debe especificar la fecha de ingreso');
         $inputs=array(
            "Tipo Comprobante" => $Helper->combo_tipocomprobante("fil_cmbtipocomprobante",++$index,"5",$tipo,"Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_TipoComprobante,$disabled),
            "Almacen" => $Helper->combo_almacen("fil_cmbalmacen",++$index,$almacen,"",$Val_Almacen,$disabled),
            "Nro Guia" => $Helper->textbox("fil_txtNroSerie",++$index,$serie,3,50,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . "-" . $Helper->textbox("fil_txtNroNumero",++$index,$numero,10,100,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
            "Proveedor" => $Helper->combo_proveedor("fil_cmbproveedor",++$index,$proveedor,"",$Val_Proveedor,$disabled),
            "Nro Comprobante" => $Helper->textbox("fil_txtNroComprobante",++$index,$compreferencia,15,100,$TipoTxt->texto,"","","","",$Val_NroComprobante,$disabled),
            "Fecha Ingreso"=>$Helper->textdate("fil_txtFechaMovimiento",++$index,$fecha,false,$TipoDate->fecha,80,"","",$Val_Fecha,$disabled),
            "Items"=>$grilla . "~5",
            "Motivo" => $Helper->textarea("fil_txtMotivo",++$index,$motivo,170,3,"","","","",null,$disabled) . '~5'
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_guia",$inputs,$buttons,3,1100,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Ver") . " Guía de Entrada",1150,$html,(!$disabled?$Helper->button("","Grabar",70,"Operacion('kardex','Mant_GuiaEntrada','tbl_mant_guia','" . $prm . "')","textoInput"):""));
      }
      function Mant_GuiaEntrada($prm){
         $Obj=new mysqlhelper;$Helper= new htmlhelper;$valor=explode('|',$prm);
         $trans=$Obj->transaction();
         include('../../code/bl/bl_general.php'); $Obj_general=new bl_general;
         $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante(5);//Guia Entrada
         try{
             $correlativo=$Obj_general->Obtener_Correlativo_Comprobante($valor[1],$trans);
             if($correlativo==-1) throw new Exception("Error: Obtener_Correlativo_Comprobante");
             $sql="INSERT INTO tbl_sgo_almacenguiaingreso (sgo_int_tipocomprobante,sgo_int_almacen,sgo_vch_serie,sgo_vch_numero,sgo_int_proveedor,sgo_vch_nrocomprobante,sgo_dat_fechamovimiento,sgo_vch_motivo,sgo_dat_fecharegistro,sgo_bit_activo)
             VALUES (" . $valor[1] . "," . $valor[2] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[5] . ",'" . $valor[6] . "','" . $Helper->convertir_fecha_ingles($valor[7]) . "','" . $valor[8] . "','" . date("Y-m-d H:i") . "',1)";
    //         VALUES ('" . str_pad($valor[1],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($valor[2],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[3] . "," . $valor[4] . "," . $valor[5] . ",'" . $valor[6] . "','" . $Helper->convertir_fecha_ingles($valor[7]) . "','" . $valor[8] . "','" . date("Y-m-d H:i") . "',1)";
              if($trans->query($sql))
              {
                  $id=mysqli_insert_id($trans);
                  $valor_item=explode('~',$valor[9]);
                  foreach ($valor_item as $k) {
                     $valor_col=explode('_',$k);
                     $valorcompra +=floatval($valor_col[3]);
                     $sql="INSERT INTO tbl_sgo_almacenguiaingresodetalle (sgo_int_almacenguiaingreso,sgo_int_producto,sgo_dec_cantidad)
                     VALUES (" . $id . "," . $valor_col[0] . "," . $valor_col[1] . ")";
                     if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                     if($Obj_general->Obtener_IDTipo_Articulo($valor_col[0])!=5)//Servicio
                     {
                         if($this->Mant_KardexEntrada($valor_col[0] . '|' . $valor_col[1] . '|Guía de Entrada ' . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . '-' . str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT),0,$trans)==-1) throw new Exception("Error en Mant_KardexEntrada");
                         if($this->Mant_ProductoAlmacenEntrada($valor[2],$valor_col[0],$valor_col[1],$trans)==-1) throw new Exception("Error en Mant_ProductoAlmacenEntrada");
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
//             throw new Exception("insert name again",0,$e);
         }
      }
      function Mant_ProductoAlmacenEntrada($almacen,$producto,$cantidad,$ref_trans=null){
          $Obj=new mysqlhelper;
          //Registra el ingreso de un producto por almacen
          $trans=($ref_trans==null?$Obj->transaction():$ref_trans);$existe=0;
          $sql="SELECT sgo_int_almacen FROM tbl_sgo_productoalmacen WHERE sgo_int_almacen=" . $almacen . " and sgo_int_producto=" . $producto;
          $result=$Obj->consulta($sql);
          while ($row = mysqli_fetch_array($result))
          {
             $existe=1;
          }
          if($existe==1) $sql="UPDATE tbl_sgo_productoalmacen SET sgo_dec_stock=(sgo_dec_stock + " . $cantidad . ") WHERE sgo_int_almacen=" . $almacen . " and sgo_int_producto=" . $producto;
          else $sql="INSERT INTO tbl_sgo_productoalmacen (sgo_int_almacen,sgo_int_producto,sgo_dec_stock) VALUES(" . $almacen . "," . $producto . "," . $cantidad . ")";
          if($ref_trans==null){
            $Obj=new mysqlhelper; return $Obj->execute($sql);
          }
          else{
            if($ref_trans->query($sql)) return 1;
            else return -1;
          }
      }
      function Mant_ProductoAlmacenSalida($almacen,$producto,$cantidad,$ref_trans=null){
          $Obj=new mysqlhelper;
          //Registra la salida de un producto por almacen
          $trans=($ref_trans==null?$Obj->transaction():$ref_trans);
          $sql="UPDATE tbl_sgo_productoalmacen SET sgo_dec_stock=(sgo_dec_stock - " . $cantidad . ") WHERE sgo_int_almacen=" . $almacen . " and sgo_int_producto=" . $producto;
          if($ref_trans==null){
            $Obj=new mysqlhelper; return $Obj->execute($sql);
          }
          else{
            if($ref_trans->query($sql)) return 1;
            else return -1;
          }
      }
      function Confirma_Eliminar_GuiaEntrada($prm){
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmacion",450,htmlentities('¿Esta seguro de eliminar la guía de entrada ' . $this->Obtener_Nombre_GuiaEntrada($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('kardex','Eliminar_GuiaEntrada','','" . $prm . "')"));
      }
      function Eliminar_GuiaEntrada($prm){
          $Obj=new mysqlhelper;
          $trans=$Obj->transaction();
          try{
            $sql="UPDATE tbl_sgo_almacenguiaingreso SET sgo_bit_activo=0 WHERE sgo_int_almacenguiaingreso=" . $prm;
            if($trans->query($sql))
            {
                $result=$Obj->consulta("SELECT agi.sgo_int_almacen,agd.sgo_int_producto,agd.sgo_dec_cantidad FROM tbl_sgo_almacenguiaingresodetalle agd INNER JOIN tbl_sgo_almacenguiaingreso agi ON agi.sgo_int_almacenguiaingreso=agd.sgo_int_almacenguiaingreso WHERE agi.sgo_int_almacenguiaingreso=" . $prm);
                while ($row = mysqli_fetch_array($result))
                {
                    if($this->Mant_KardexSalida($row["sgo_int_producto"] . '|'. $row["sgo_dec_cantidad"] . '|Anulacion de Guía de Entrada ' . $this->Obtener_Nombre_GuiaEntrada($prm),$trans)==-1)throw new Exception($sql . " => " . $trans->error);
                    if($this->Mant_ProductoAlmacenSalida($row["sgo_int_almacen"],$row["sgo_int_producto"],$row["sgo_dec_cantidad"],$trans)==-1) throw new Exception("Error en Mant_ProductoAlmacenSalida");

                }
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

/********************************************************GUIAS DE SALIDA**************************************************************************/
      function Filtros_Listar_GuiaSalida(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $inputs=array(
            "Nro Serie" => $Helper->textbox("fil_cmbBusquedaSerie",$index,"",3,70,$TipoTxt->texto,"","","",""),
            "Numero" => $Helper->textbox("fil_cmbBusquedaNumero",$index,"",15,100,$TipoTxt->texto,"","","",""),
            "Registro desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Almacen" => $Helper->combo_almacen("fil_txtBusquedaAlmacen",++$index,"","",""),
            "Proveedor" => $Helper->combo_proveedor("fil_txtBusquedaProveedor",++$index,"","",""),
            "Tipo Comprobante" => $Helper->combo_tipocomprobante("fil_txtBusquedaTipoComprobante",++$index,"","","")
         );
         $buttons=array($Helper->button("btnBuscarGuia","Buscar",70,"Buscar_Grilla('kardex','Grilla_Listar_GuiaSalida','tbl_listarguia','','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listarguia",$inputs,$buttons,2,990,"","");
      }
      function Grilla_Listar_GuiaSalida($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT agi.sgo_int_almacenguiasalida as param,Concat(agi.sgo_vch_serie,'-',agi.sgo_vch_numero) as 'Comprobante',alm.sgo_vch_nombre as 'Almacen',per.sgo_vch_nombre as 'Cliente',tco.sgo_vch_descripcion as 'Tipo Comp. Referencia',agi.sgo_vch_nrocomprobante as 'Comp. Referencia',
                CASE agi.sgo_bit_activo WHEN 1 THEN '' ELSE '** Anulada **' END AS Anulada
                FROM tbl_sgo_almacenguiasalida agi
                INNER JOIN tbl_sgo_persona per ON per.sgo_int_persona=agi.sgo_int_cliente
                INNER JOIN tbl_sgo_almacen alm ON alm.sgo_int_almacen=agi.sgo_int_almacen
                INNER JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=agi.sgo_int_tipocomprobante";
         $where = $Obj->sql_where("WHERE agi.sgo_vch_serie like '%@p1%' and agi.sgo_vch_numero like '%@p2%' and (agi.sgo_dat_fecharegistro BETWEEN '@p3 00:00' and '@p4 23:59') and alm.sgo_int_almacen=@p5 and per.sgo_int_persona=@p6 and tco.sgo_int_tipocomprobante=@p7",$prm);
         $order = "ORDER BY agi.sgo_dat_fecharegistro DESC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $order),"PopUp('kardex','PopUp_Mant_GuiaSalida','','","PopUp('kardex','PopUp_Mant_GuiaSalida','','","","PopUp('kardex','Confirma_Eliminar_GuiaSalida','','",null,array(),array(),array(),20,"");
      }
      function Grilla_Listar_GuiaSalida_Items($prm,$size=1000){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);$index=0;$TipoTxt=new TipoTextBox;
         $sql ="SELECT gsd.sgo_int_almacenguiasalidadetalle as param, pdt.sgo_vch_nombre as Item,und.sgo_vch_abreviatura as 'Und Medida'," . ($prm==0?"pdt.sgo_dec_stock as Stock,":"") . "gsd.sgo_dec_cantidad as 'Salida'
                FROM tbl_sgo_almacenguiasalida agi
                INNER JOIN tbl_sgo_almacenguiasalidadetalle gsd ON gsd.sgo_int_almacenguiasalida=agi.sgo_int_almacenguiasalida
                INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=gsd.sgo_int_producto
                INNER JOIN tbl_sgo_unidadmedida und ON und.sgo_int_unidadmedida=pdt.sgo_int_unidadmedida";
         $where = $Obj->sql_where("WHERE gsd.sgo_int_almacenguiasalida=@p1",$prm);
         $orderby = "ORDER BY gsd.sgo_int_almacenguiasalidadetalle ASC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"Nuevo_AlmacenGuiaDetalle('tbl_mant_guia',this.parentNode,2);","","","",$size,array(),array(),array(),20,"") . "<script>Carga_Js('../../js/jscript_almacen.js')</script>";
      }
      function PopUp_Mant_GuiaSalida($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=1;
         $serie="";$numero="";$almacen="";$cliente="";$tipo="";$compreferencia="";$fecha=date("d/m/Y");$motivo="";
         $result = $Obj->consulta("SELECT sgo_vch_serie,sgo_vch_numero,sgo_int_almacen,sgo_int_cliente,sgo_int_tipocomprobante,sgo_vch_nrocomprobante,DATE_FORMAT(sgo_dat_fechamovimiento,'%d/%m/%Y') as sgo_dat_fechamovimiento,sgo_vch_motivo FROM tbl_sgo_almacenguiasalida WHERE sgo_int_almacenguiasalida = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $serie=$row["sgo_vch_serie"];$numero=$row["sgo_vch_numero"];$almacen=$row["sgo_int_almacen"];$cliente=$row["sgo_int_cliente"];$tipo=$row["sgo_int_tipocomprobante"];
            $compreferencia=$row["sgo_vch_nrocomprobante"];$motivo=$row["sgo_vch_motivo"];$fecha=$row["sgo_dat_fechamovimiento"];
            break;
         }
         $grilla="<div style='height:250px;overflow-y:scroll;'><br/>" . $this->Grilla_Listar_GuiaSalida_Items($prm) . "</div>";
         $disabled=($prm=="0"?false:true);
         $Val_Serie=new InputValidacion();
         $Val_Serie->InputValidacion('DocValue("fil_txtNroSerie")!=""','Debe especificar el numero de serie');
         $Val_Numero=new InputValidacion();
         $Val_Numero->InputValidacion('DocValue("fil_txtNroNumero")!=""','Debe especificar el numero del documento');
         $Val_Almacen=new InputValidacion();
         $Val_Almacen->InputValidacion('DocValue("fil_cmbalmacen")!=""','Debe especificar el almacen');
         $Val_Cliente=new InputValidacion();
         $Val_Cliente->InputValidacion('DocValue("fil_cmbcliente")!=""','Debe especificar el cliente');
         $Val_TipoComprobante=new InputValidacion();
         $Val_TipoComprobante->InputValidacion('DocValue("fil_cmbtipocomprobante")!=""','Debe especificar el tipo de comprobante de referencia');
         $Val_NroComprobante=new InputValidacion();
         $Val_NroComprobante->InputValidacion('DocValue("fil_txtNroComprobante")!=""','Debe especificar el numero de comprobante de referencia');
         $Val_Fecha=new InputValidacion();
         $Val_Fecha->InputValidacion('DocValue("fil_txtFechaMovimiento")!=""','Debe especificar la fecha de salida');
         $inputs=array(
            "Tipo Comprobante" => $Helper->combo_tipocomprobante("fil_cmbtipocomprobante",++$index,"6",$tipo,"Cargar_Objeto('general','Numeracion_Comprobante','',this.value,'fil_txtNroSerie|fil_txtNroNumero','custom');",$Val_TipoComprobante,$disabled),
            "Almacen" => $Helper->combo_almacen("fil_cmbalmacen",++$index,$almacen,"",$Val_Almacen,$disabled),
            "Nro Guia" => $Helper->textbox("fil_txtNroSerie",$index,++$serie,3,70,$TipoTxt->numerico,"","","","",$Val_Serie,$disabled) . "-" . $Helper->textbox("fil_txtNroNumero",++$index,$numero,10,100,$TipoTxt->numerico,"","","","",$Val_Numero,$disabled),
            "Cliente" => $Helper->combo_cliente("fil_cmbcliente",++$index,$cliente,"",$Val_Cliente,$disabled),
            "Nro Comprobante" => $Helper->textbox("fil_txtNroComprobante",++$index,$compreferencia,15,100,$TipoTxt->texto,"","","","",$Val_NroComprobante,$disabled),
            "Fecha Salida"=>$Helper->textdate("fil_txtFechaMovimiento",++$index,$fecha,false,$TipoDate->fecha,80,"","",$Val_Fecha,$disabled),
            "Items"=>$grilla . "~5",
            "Motivo" => $Helper->textarea("fil_txtMotivo",++$index,$motivo,170,3,"","","","",null,$disabled) . '~5'
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_guia",$inputs,$buttons,3,1100,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Ver") . " Guía de Salida",1150,$html,(!$disabled?$Helper->button("","Grabar",70,"Operacion('kardex','Mant_GuiaSalida','tbl_mant_guia','" . $prm . "')","textoInput"):"")) . "<script>Focus('fil_cmbtipocomprobante')</script>";
      }
      function Mant_GuiaSalida($prm){
         $Obj=new mysqlhelper;$Helper= new htmlhelper;$valor=explode('|',$prm);
         $trans=$Obj->transaction();
         include('../../code/bl/bl_general.php'); $Obj_general=new bl_general;
         $comprobante=$Obj_general->Obtener_Caracteristica_Comprobante(6);//Guia Salida
         try{
             $correlativo=$Obj_general->Obtener_Correlativo_Comprobante($valor[1],$trans);
             if($correlativo==-1) throw new Exception("Error: Obtener_Correlativo_Comprobante");
             $sql="INSERT INTO tbl_sgo_almacenguiasalida (sgo_int_tipocomprobante,sgo_int_almacen,sgo_vch_serie,sgo_vch_numero,sgo_int_cliente,sgo_vch_nrocomprobante,sgo_dat_fechamovimiento,sgo_vch_motivo,sgo_dat_fecharegistro,sgo_bit_activo)
             VALUES (" . $valor[1] . "," . $valor[2] . ",'" . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[5] . ",'" . $valor[6] . "','" . $Helper->convertir_fecha_ingles($valor[7]) . "','" . $valor[8] . "','" . date("Y-m-d H:i") . "',1)";
    //         VALUES ('" . str_pad($valor[1],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . "','" . str_pad($valor[2],$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT) . "'," . $valor[3] . "," . $valor[4] . "," . $valor[5] . ",'" . $valor[6] . "','" . $Helper->convertir_fecha_ingles($valor[7]) . "','" . $valor[8] . "','" . date("Y-m-d H:i") . "',1)";
              if($trans->query($sql))
              {
                  $id=mysqli_insert_id($trans);
                  $valor_item=explode('~',$valor[9]);
                  foreach ($valor_item as $k) {
                     $valor_col=explode('_',$k);
                     $valorcompra +=floatval($valor_col[3]);
                     $sql="INSERT INTO tbl_sgo_almacenguiasalidadetalle (sgo_int_almacenguiasalida,sgo_int_producto,sgo_dec_cantidad)
                     VALUES (" . $id . "," . $valor_col[0] . "," . $valor_col[1] . ")";
                     if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                     if($Obj_general->Obtener_IDTipo_Articulo($valor_col[0])!=5)//Servicio
                     {
                         if($this->Mant_KardexSalida($valor_col[0] . '|' . $valor_col[1] . '|Guía de Salida ' . str_pad($valor[3],$comprobante["digitosserie"],'0', STR_PAD_LEFT) . '-' . str_pad($correlativo,$comprobante["digitoscorrelativo"],'0', STR_PAD_LEFT),$trans)==-1) throw new Exception("Error en Mant_KardexSalida");
                         if($this->Mant_ProductoAlmacenSalida($valor[2],$valor_col[0],$valor_col[1],$trans)==-1) throw new Exception("Error en Mant_ProductoAlmacenSalida");
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
//             throw new Exception("insert name again",0,$e);
         }
      }
      function Confirma_Eliminar_GuiaSalida($prm){
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmacion",450,htmlentities('¿Esta seguro de eliminar la guía de salida ' . $this->Obtener_Nombre_GuiaSalida($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('kardex','Eliminar_GuiaSalida','','" . $prm . "')"));
      }
      function Eliminar_GuiaSalida($prm){
          $Obj=new mysqlhelper;
          $trans=$Obj->transaction();
          try{
            $sql="UPDATE tbl_sgo_almacenguiasalida SET sgo_bit_activo=0 WHERE sgo_int_almacenguiasalida=" . $prm;
            if($trans->query($sql))
            {
                $result=$Obj->consulta("SELECT agi.sgo_int_almacen,agd.sgo_int_producto,agd.sgo_dec_cantidad FROM tbl_sgo_almacenguiasalidadetalle agd INNER JOIN tbl_sgo_almacenguiasalida agi ON agi.sgo_int_almacenguiasalida=agd.sgo_int_almacenguiasalida WHERE agi.sgo_int_almacenguiasalida=" . $prm);
                while ($row = mysqli_fetch_array($result))
                {
                    if($this->Mant_KardexEntrada($row["sgo_int_producto"] . '|'. $row["sgo_dec_cantidad"] . '|Anulacion de Guía de Salida ' . $this->Obtener_Nombre_GuiaSalida($prm),0,$trans)==-1)throw new Exception($sql . " => " . $trans->error);
                    if($this->Mant_ProductoAlmacenEntrada($row["sgo_int_almacen"],$row["sgo_int_producto"],$row["sgo_dec_cantidad"],$trans)==-1) throw new Exception("Error en Mant_ProductoAlmacenEntrada");
                }
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

/**********************************************************OBTENER DATOS****************************************************************/
/**********************************************************OBTENER DATOS****************************************************************/
/**********************************************************OBTENER DATOS****************************************************************/
      function Obtener_StockTotal_producto($pdt)
      {
          $Obj=new mysqlhelper;
          $result = $Obj->consulta("SELECT sgo_dec_stock FROM tbl_sgo_producto WHERE sgo_int_producto=" . $pdt);
          while ($row = mysqli_fetch_array($result)){ return $row["sgo_dec_stock"]; }
          return 0;
      }
      function Obtener_Nombre_GuiaEntrada($prm)
      {
          $Obj=new mysqlhelper;
          $result = $Obj->consulta("SELECT Concat(sgo_vch_serie,'-',sgo_vch_numero) as nombre FROM tbl_sgo_almacenguiaingreso WHERE sgo_int_almacenguiaingreso=" . $prm);
          while ($row = mysqli_fetch_array($result)){ return $row["nombre"]; }
          return "";
      }
      function Obtener_Nombre_GuiaSalida($prm)
      {
          $Obj=new mysqlhelper;
          $result = $Obj->consulta("SELECT Concat(sgo_vch_serie,'-',sgo_vch_numero) as nombre FROM tbl_sgo_almacenguiasalida WHERE sgo_int_almacenguiasalida=" . $prm);
          while ($row = mysqli_fetch_array($result)){ return $row["nombre"]; }
          return "";
      }
      function Obtener_Nombre_Inventario($prm)
      {
          $Obj=new mysqlhelper;
          $result = $Obj->consulta("SELECT Concat(sgo_vch_serie,'-',sgo_vch_numero) as nombre FROM tbl_sgo_inventario WHERE sgo_int_inventario=" . $prm);
          while ($row = mysqli_fetch_array($result)){ return $row["nombre"]; }
          return "";
      }
  }
?>