<?php include_once('../../code/lib/htmlhelper.php');include_once('../../code/lib/loghelper.php');
  Class bl_ordenproduccion
  {
/********************************************************CATÁLOGO ÓRDENES PRODUCCIÓN**************************************************************************/
      function Filtros_Listar_OrdenProduccion(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=10;
         $inputs=array(
            "Cliente" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"",""),
            "Creación desde" => $Helper->textdate("fil_txtBusquedaDesde",++$index,"",false,$TipoDate->fecha,80,"","") . " Al " . $Helper->textdate("fil_txtBusquedahasta",++$index,"",false,$TipoDate->fecha,80,"",""),
            "Nro. Orden Producción" => $Helper->textbox("fil_txtBusquedaNroOrdenServicio",++$index,"",11,100,$TipoTxt->texto,"","","",""),
            "Estado" => $Helper->combo_estado_ordenproduccion("fil_cmbBusquedaestadoordenproduccion",++$index,"1",""),
         );
         $buttons=array($Helper->button("btnBuscarOrdenProduccion","Buscar",70,"Buscar_Grilla('ordenproduccion','Grilla_Listar_OrdenProduccion','tbl_listarordenproduccion','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarordenproduccion",$inputs,$buttons,2,800,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_OrdenProduccion($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT op.sgo_int_ordenproduccion as param, per.sgo_vch_nrodocumentoidentidad as RUC, concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) as Cliente,LPAD(oc.sgo_int_cotizacion,6,'0') as 'Cotización',Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero) as 'Órden de Servicio', sgo_vch_ordenproduccion as 'Órden Producción',eop.sgo_vch_descripcion as Estado,DATE_FORMAT(op.sgo_dat_fechainicio, '%d/%m/%Y %H:%i') as 'Fecha Inicio'
                FROM tbl_sgo_ordenproduccion op
                INNER JOIN tbl_sgo_ordenservicio oc on oc.sgo_int_ordenservicio=op.sgo_int_ordenservicio
                INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=oc.sgo_int_cliente
                INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=cli.sgo_int_cliente
                INNER JOIN tbl_sgo_estadoordenproduccion eop on eop.sgo_int_estado=op.sgo_int_estado";
         $groupby = "GROUP BY op.sgo_int_ordenproduccion, per.sgo_vch_nrodocumentoidentidad, per.sgo_vch_nombre, op.sgo_int_ordenproduccion,eop.sgo_vch_descripcion";
         $orderby = "ORDER BY op.sgo_int_ordenproduccion DESC";
         $where = $Obj->sql_where("WHERE cli.sgo_int_cliente=@p1 and (op.sgo_dat_fechacreacion BETWEEN '@p2 00:00' and '@p3 23:59') and sgo_vch_ordenproduccion like '%@p4%' and op.sgo_int_estado=@p5 and op.sgo_int_estado!=4",
                 $valor[0] . '|' . $Helper->convertir_fecha_ingles($valor[1]) . '|' . $Helper->convertir_fecha_ingles($valor[2]) . '|' . $valor[3] . '|' . $valor[4]);
         $btn_extra=array(
            "Presione aquí para imprimir." => "print.png|Operacion('ordenproduccion','PopUp_Imprimir','','"/*,
            "Presione aquí para actualizar la producción." => "eventos.png|PopUp('ordenproduccion','PopUp_Mant_OrdenProduccion_Detalles','','",*/
         );
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $groupby . " " . $orderby),"","","PopUp('ordenproduccion','PopUp_Mant_OrdenProduccion','','","PopUp('ordenproduccion','Confirma_Eliminar','','",null,$btn_extra, array(), array(),20,"");
      }
      function PopUp_Imprimir($prm){
          $ruta = $this->Obtener_ArchivoImpresion_OrdenProduccion($prm);
          if($ruta!="") echo "<script>window.open('../../" . $ruta . "?prm=" . $prm . "');</script>";
          else echo "<script>Ver_Mensaje('Impresi&oacute;n','No existe un formato de impresi&oacute;n configurado')</script>";
      }
      function PopUp_Mant_OrdenProduccion($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $index=10; $estado="1";
         $result = $Obj->consulta("SELECT sgo_int_estado FROM tbl_sgo_ordenproduccion WHERE sgo_int_ordenproduccion = " . $prm);
         while ($row = mysqli_fetch_array($result)){ $estado=$row["sgo_int_estado"]; break; }
         if($estado==1){
           $this->Mant_OrdenProduccion($prm . '|2');$msg="";
           $sql ="SELECT pdt.sgo_vch_nombre as producto, pdt.sgo_int_producto as id_producto, ocd.sgo_dec_cantidad as requerida,pdt.sgo_dec_stock as stock
            FROM tbl_sgo_ordenproduccion op
            INNER JOIN tbl_sgo_ordenservicio oc on oc.sgo_int_ordenservicio=op.sgo_int_ordenservicio
            INNER JOIN tbl_sgo_ordenserviciodetalle ocd on ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio
            INNER JOIN tbl_sgo_ordenproducciondetalle opd on opd.sgo_int_ordenproduccion=op.sgo_int_ordenproduccion and ocd.sgo_int_ordenserviciodetalle=opd.sgo_int_ordenserviciodetalle
            INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=ocd.sgo_int_producto
            WHERE op.sgo_int_ordenproduccion=" . $prm;
           $result=$Obj->consulta($sql);
           while ($row = mysqli_fetch_array($result))
           {
                $msg .="<br/><span class='left'>Producto " . $row["producto"] . ", " . $row["requerida"] . " requerido(s):</span><br/>";
                $sql ="SELECT pfo.sgo_dec_cantidad, pdt.sgo_dec_stock, pdt.sgo_vch_nombre,und.sgo_vch_abreviatura
                  FROM tbl_sgo_productoformula pfo
                  INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=pfo.sgo_int_insumo
                  INNER JOIN tbl_sgo_unidadmedida und ON und.sgo_int_unidadmedida=pdt.sgo_int_unidadmedida
                  WHERE pfo.sgo_int_producto=" . $row["id_producto"];
                $result_aux=$Obj->consulta($sql);
                while ($row_aux = mysqli_fetch_array($result_aux)){
                     if($row_aux["sgo_dec_stock"] < $row_aux["sgo_dec_cantidad"]){
                        $msg .=$row_aux["sgo_vch_nombre"] . ": requerido(s) " . ($row_aux["sgo_dec_cantidad"] * $row["requerida"]) . " de " . $row_aux["sgo_dec_stock"] . " " .  $row_aux["sgo_vch_abreviatura"] . " => Insuficiente.<br/>";
                     }
                     else $msg .=$row_aux["sgo_vch_nombre"] . ": requerido(s) " . ($row_aux["sgo_dec_cantidad"] * $row["requerida"]) . " de " . $row_aux["sgo_dec_stock"] . " " .  $row_aux["sgo_vch_abreviatura"] . " => OK<br/>";
                }
           }
           return $Helper->PopUp("","Notificación",600,htmlentities("Se ha iniciado la Órden de Producción \"" . date("Y") . str_pad($prm,4,'0', STR_PAD_LEFT)) . "\"<br/><br/>Evaluaci&oacute;n de Producci&oacute;n" . $msg,"") . "<script>Buscar_Grilla('ordenproduccion','Grilla_Listar_OrdenProduccion','tbl_listarordenproduccion','','td_General',1);</script>";
         }
         else{
           $content_tabs = array(); $titulo_tabs = array();
           $content_tabs[0] =$this->Grilla_Listar_OrdenProduccion_Detalle($prm,950,$estado);
           $titulo_tabs[0]="Productos";

           $Val_Estado=new InputValidacion();
           $Val_Estado->InputValidacion('DocValue("fil_cmbEstadoOrdenProduccion")!=""','Debe especificar el estado');
           $inputs=array(
              "Estado" => $Helper->combo_estado_ordenproduccion("fil_cmbEstadoOrdenProduccion",++$index,$estado,"",$Val_Estado,($estado==3?true:false))
           );
           $buttons=array();
           $html = $Helper->Crear_Layer("",$inputs,$buttons,3,300,"","");
           $content_tabs[1] ="<br/>" . $html;
           $titulo_tabs[1]="Orden Producción";

           $html = "<div id='tbl_mant_op'>" . $Helper->Crear_Tabs("Tabs_Mant_OrdenProduccion",$content_tabs, $titulo_tabs, "","") . "</div>";
           return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Órden de Producción",1000,$html,($estado!=3?$Helper->button("","Grabar",70,"Operacion('ordenproduccion','Mant_OrdenProduccion','tbl_mant_op','" . $prm . "')","textoInput"):""));
         }
      }
      function Mant_OrdenProduccion_Inicio($prm){
          $valor=explode('|',$prm);
          $resp=$Obj->execute("UPDATE tbl_sgo_ordenproduccion SET sgo_int_estado=2,sgo_dat_fechainicio='" . date("Y-m-d H:i:s") . "' WHERE sgo_int_ordenproduccion=" . $valor[0]);
          if($valor[1]!="0" && $resp!=-1)
          {
             $Obj_kardex->Mant_KardexSalida($pdt . '|' . $valor[1] . '|Se reserva para la Órden de Producción ' . date("Y") . str_pad($valor[0],4,'0', STR_PAD_LEFT));
          }
          return $resp;
      }
      function Mant_OrdenProduccion($prm){
//          echo '<script>alert("'.$prm .'")</script>';
         $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);$estado=0;
         $trans=$Obj->transaction();
         try{
            $result=$Obj->consulta("SELECT sgo_int_estado FROM tbl_sgo_ordenproduccion WHERE sgo_int_ordenproduccion=" . $valor[0]);
            while ($row = mysqli_fetch_array($result)){ $estado=$row['sgo_int_estado'];break; }
            if($estado==1 and $valor[1]==2){
              if(!$trans->query("UPDATE tbl_sgo_ordenproduccion SET sgo_int_estado=" . $valor[1] . ($valor[1]==2?",sgo_dat_fechainicio='" . date("Y-m-d H:i:s") . "'":",sgo_dat_fechainicio=null") . " WHERE sgo_int_ordenproduccion=" . $valor[0]))
              {throw new Exception("Error al actualizar estado => " . $trans->error);}
            }
            if($resp!=-1 && $estado==2){
               $valor_grilla=explode('_',$valor[2]);
               if(count($valor_grilla)>0){
                  include('../../code/bl/bl_kardex.php'); $Obj_kardex=new bl_kardex;
                  foreach ($valor_grilla as $k) {
                    $valor_col=explode('~',$k);
                    //Se van a utilizar productos del stock libre y debe modificarse el inventario
                    $sql="SELECT ocd.sgo_int_producto,opd.sgo_dec_cantidaddesdestock FROM tbl_sgo_ordenproducciondetalle opd INNER JOIN tbl_sgo_ordenserviciodetalle ocd ON opd.sgo_int_ordenserviciodetalle=ocd.sgo_int_ordenserviciodetalle WHERE opd.ocd.sgo_int_ordenproducciondetalle=" . $valor_col[0];
                    $result=$Obj->consulta($sql);$pdt=0;$cantstock=0;
                    while ($row = mysqli_fetch_array($result)){ $pdt=$row['sgo_int_producto'];$cantstock=$row['sgo_dec_cantidaddesdestock'];break; }
                    $sql= "UPDATE tbl_sgo_ordenproducciondetalle SET sgo_dec_cantidaddesdestock=" . $valor_col[1] . " WHERE sgo_int_ordenproducciondetalle=" . $valor_col[0];
                    if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
                    if($Obj_kardex->Mant_Quitar_KardexReserva($pdt . '|' . $cantstock,$trans)==-1) throw new Exception("Error en Mant_Quitar_KardexReserva");
                    if($Obj_kardex->Mant_KardexReserva($pdt . '|' . $valor_col[1],$trans)==-1) throw new Exception("Error en Mant_KardexReserva");

                    if($valor_col[2]>0) //Se ha producido algo Hoy y debe ser ingresado al inventario y ser reflejado en el kardex
                    {
                        $sql= "INSERT INTO tbl_sgo_ordenproduccionseguimiento (sgo_dat_fecharegistro, sgo_int_ordenproducciondetalle, sgo_dec_cantidadproducida) VALUES ('" . date("Y-m-d H:i:s") . "'," . $valor_col[0] . "," . $valor_col[2] .")";
                        if($trans->query($sql)){
                           $sql= "UPDATE tbl_sgo_ordenproducciondetalle SET sgo_dec_cantidadproducida=(sgo_dec_cantidadproducida + " . $valor_col[2] . ") WHERE sgo_int_ordenproducciondetalle=" . $valor_col[0];
                           if($trans->query($sql)){
                               $sql="SELECT ocd.sgo_int_producto FROM tbl_sgo_ordenproducciondetalle opd INNER JOIN tbl_sgo_ordenserviciodetalle ocd ON opd.sgo_int_ordenserviciodetalle=ocd.sgo_int_ordenserviciodetalle WHERE opd.sgo_int_ordenproducciondetalle=" . $valor_col[0];
                               $result=$Obj->consulta($sql);$pdt=0;
                               while ($row = mysqli_fetch_array($result)){ $pdt=$row['sgo_int_producto'];break; }
                               if($Obj_kardex->Mant_KardexEntrada($pdt . '|' . $valor_col[2] . '|Reservado para la OP ' . date("Y") . str_pad($valor[0],4,'0', STR_PAD_LEFT),1,$trans)==-1) //Ingresa productos y retira sus insumos del inventario
                               {throw new Exception("Error en Mant_KardexEntrada");}
                           }
                           else throw new Exception($sql . " => " . $trans->error);
                        }
                        else throw new Exception($sql . " => " . $trans->error);
                    }
                 }
              }
              $sql= "SELECT SUM(ocd.sgo_dec_cantidad - opd.sgo_dec_cantidaddesdestock - opd.sgo_dec_cantidadproducida) as 'Restantes'
              FROM tbl_sgo_ordenproduccion op
              INNER JOIN tbl_sgo_ordenservicio oc on oc.sgo_int_ordenservicio=op.sgo_int_ordenservicio
              INNER JOIN tbl_sgo_ordenserviciodetalle ocd on ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio
              INNER JOIN tbl_sgo_ordenproducciondetalle opd on opd.sgo_int_ordenproduccion=op.sgo_int_ordenproduccion and ocd.sgo_int_ordenserviciodetalle=opd.sgo_int_ordenserviciodetalle
              WHERE op.sgo_int_ordenproduccion=" . $valor[0];
              $result=$Obj->consulta($sql);
              while ($row = mysqli_fetch_array($result))
              {
                 if($row["Restantes"]==0){
                     $sql= "UPDATE tbl_sgo_ordenproduccion SET sgo_int_estado=3,sgo_dat_fechafin='" . date("Y-m-d H:i") . "' WHERE sgo_int_ordenproduccion=" . $valor[0];
                     if(!$trans->query($sql))throw new Exception($sql . " => " . $trans->error);
                 }
                 break;
              }
//          echo '<script>alert("'.$sql .'")</script>';
           }
           $trans->commit();$trans->close();return 1;
         }
         catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
//             throw new Exception("insert name again",0,$e);
         }
      }
      function Confirma_Eliminar_OrdenProduccion($prm){
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de anular la órden de producción ' . date("Y") . str_pad($prm,4,'0', STR_PAD_LEFT) . '?'),$Helper->button("", "Si", 70, "Operacion('ordenproduccion','Eliminar_OrdenProduccion','','" . $prm . "')"));
      }
      function Eliminar_OrdenProduccion($prm){
          $Obj=new mysqlhelper;
          $result = $Obj->consulta("SELECT sgo_int_ordenservicio FROM tbl_sgo_ordenproduccion WHERE sgo_int_ordenproduccion = " . $prm);
          $oc=0;$resp="<script>Operacion_Result(false);</script>";
          while ($row = mysqli_fetch_array($result))
          {
             $oc=$row["sgo_int_ordenservicio"];
             break;
          }
          if($oc!=0){
            if($Obj->execute("UPDATE tbl_sgo_ordenservicio SET sgo_int_estado=1 WHERE sgo_int_ordenservicio=" . $oc)!=-1){
              if($Obj->execute("UPDATE tbl_sgo_ordenproduccion SET sgo_int_estado=4 WHERE sgo_int_ordenproduccion=" . $prm)!=-1)
                  return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarOrdenProduccion');</script>";
            }
            else return $resp;
          }
          else return $resp;
      }
/****************************************************************ORDEN PRODUCCIÓN DETALLE************************************************************************/

      function Grilla_Listar_OrdenProduccion_Detalle($prm,$size=900,$extra=0){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;
         $sql ="SELECT opd.sgo_int_ordenproducciondetalle as param, pdt.sgo_vch_nombre as Producto, ocd.sgo_dec_cantidad as 'Cant. Solicitada', " . ($extra!="3"?"pdt.sgo_dec_stocklibre as 'Stock Disponible',":"") . "sgo_dec_cantidaddesdestock as 'Cant. Desde Stock', opd.sgo_dec_cantidadproducida as 'Cant. Producida Total', (ocd.sgo_dec_cantidad - opd.sgo_dec_cantidaddesdestock - opd.sgo_dec_cantidadproducida) as 'Restantes'
          FROM tbl_sgo_ordenproduccion op
          INNER JOIN tbl_sgo_ordenservicio oc on oc.sgo_int_ordenservicio=op.sgo_int_ordenservicio
          INNER JOIN tbl_sgo_ordenserviciodetalle ocd on ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio
          INNER JOIN tbl_sgo_ordenproducciondetalle opd on opd.sgo_int_ordenproduccion=op.sgo_int_ordenproduccion and ocd.sgo_int_ordenserviciodetalle=opd.sgo_int_ordenserviciodetalle
          INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=ocd.sgo_int_producto";
         $where = $Obj->sql_where("WHERE op.sgo_int_ordenproduccion=@p1",$prm);
         $input_extra=array();$descript_cols=array();
         if($extra!=3){
           $descript_cols=array(
              $Helper->textbox("fil_gcol_txtDesdeStock","num_posicion","num_valor",10,100,$TipoTxt->numerico,"","","","") => 4
           );
           $input_extra=array(
              "" => '0|' . $Helper->hidden("fil_gcol_hf_id","num_posicion","num_valor"),
              "Cant. Producida Hoy" => '5|' . $Helper->textbox("fil_grow_txtCPH","num_posicion","0.00",10,100,$TipoTxt->numerico,"","","","")/*,
              "Presione aquí para actualizar la producción." => "eventos.png|PopUp('ordenproduccion','PopUp_Mant_OrdenProduccion_Detalles','','",*/
           );
         }
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"","PopUp('ordenproduccion','PopUp_Detalles_OrdenProduccion','','","","",$size,array(),$input_extra,$descript_cols,20,"");
      }
      function PopUp_Detalles_OrdenProduccion($prm,$size=500){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;
         $sql ="SELECT ops.sgo_int_ordenproduccionseguimiento as param, ops.sgo_dec_cantidadproducida as 'Cant. Producida', DATE_FORMAT(ops.sgo_dat_fecharegistro, '%d/%m/%Y %H:%i') as 'Fecha Producción'
          FROM tbl_sgo_ordenproducciondetalle opd
          INNER JOIN tbl_sgo_ordenproduccionseguimiento ops on opd.sgo_int_ordenproducciondetalle=ops.sgo_int_ordenproducciondetalle
          WHERE opd.sgo_int_ordenproducciondetalle=" .$prm;
         $html= $Helper->Imprimir_Grilla($Obj->consulta($sql),"","","","",$size);
         return $Helper->PopUp("","Detalle de producción",550,$html,"");
      }

/****************************************************************MANTENIMIENTO ATICULOS - INSUMOS - PRODUCTOS************************************************************************/
      function Filtros_Listar_Articulo(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=10;
         $inputs=array(
            "Código" => $Helper->textbox("fil_txtBusquedaCodigo",++$index,"",120,120,$TipoTxt->texto,"","","",""),
            "Artículo" => $Helper->textbox("fil_txtBusquedaArticulo",++$index,"",120,250,$TipoTxt->texto,"","","",""),
            "Categoría" => $Helper->combo_categoriaproducto("fil_cmbBusquedaCatProd",++$index,"",""),
            "Tipo" => $Helper->combo_tipoproducto("fil_txtBusquedaTipoProd",++$index,"",""),
            "Estado" => $Helper->combo_estado("fil_cmbBusquedaestadoordenproduccion",++$index,"1",""),
         );
         $buttons=array($Helper->button("btnBuscarArticulo","Buscar",70,"Buscar_Grilla('ordenproduccion','Grilla_Listar_Articulo','tbl_listararticulo','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listararticulo",$inputs,$buttons,2,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Articulo($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$log=new loghelper();
         $valor=explode('|',$prm);
         $sql ="SELECT pdt.sgo_int_producto as param, pdt.sgo_vch_codigo as 'Código', 
         		CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as Articulo,und.sgo_vch_descripcion as 'Und. Medida',pdt.sgo_dec_stock as Stock,pdt.sgo_dec_stockminimo as 'Stock Min.',moneda.sgo_vch_simbolo as 'Moneda', tar.sgo_dec_precio as 'Precio Base', tar.sgo_int_unidadesporbulto as 'Unidades x Bulto',
                pca.sgo_vch_descripcion as Categoria,
                pti.sgo_vch_descripcion as Tipo,
                case pdt.sgo_bit_activo when 1 then 'Activo' else 'Inactivo' end as Estado,
                Concat('|Ver_Archivo(\'../../archivo/ordenproduccion/',pdt.sgo_vch_archivoinstrucciones,'\')|file_doc.png') as Instrucciones,
                case when pti.sgo_int_tipoproducto in (2,3) then '|PopUp(\'ordenproduccion\',\'PopUp_Mant_Formula\',\'\',\'|script.gif' else '' end as 'Fórmula de Producción'
          FROM tbl_sgo_producto pdt          
          LEFT  JOIN tbl_sgo_unidadmedida und ON und.sgo_int_unidadmedida=pdt.sgo_int_unidadmedida
          LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
          on	pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
          and	pdt.sgo_int_color = catprodcol.sgo_int_color
          LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
          on	pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
          and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
          LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
          on	pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
          and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
          LEFT JOIN tbl_sgo_tarifario tar ON tar.sgo_int_producto=pdt.sgo_int_producto and tar.sgo_int_cliente=0
          LEFT	JOIN tbl_sgo_moneda moneda
          on	tar.sgo_int_moneda = moneda.sgo_int_moneda
          INNER JOIN tbl_sgo_categoriaproducto pca 
          on pca.sgo_int_categoriaproducto=pdt.sgo_int_categoriaproducto
          INNER JOIN tbl_sgo_tipoproducto pti 
          on pti.sgo_int_tipoproducto=pdt.sgo_int_tipo
          WHERE 1=1";
          if($valor[0]!="")
          {
          	$sql.=" and pdt.sgo_vch_codigo like '%".$valor[0]."%'";
          }
      	  if($valor[1]!="")
          {
          	$sql.=" and (pdt.sgo_vch_nombre like '%".$valor[1]."%' OR TRIM(CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,' '), ' ',ifnull(catprodtam.sgo_vch_tamano,' '), ' ',ifnull(catprodcal.sgo_vch_calidad,' '))) like '%".$valor[1]."%')";
          }
      	  if($valor[2]!="")
          {
          	$sql.=" and pca.sgo_int_categoriaproducto=".$valor[2];
          }
          if($valor[3]!="")
          {
          	$sql.=" AND pti.sgo_int_tipoproducto=".$valor[3];
          }
          if($valor[4]!="")
          {
          	$sql.=" AND pdt.sgo_bit_activo=".$valor[4];
          }
         //$where = $Obj->sql_where("WHERE pdt.sgo_vch_codigo like '%@p1%' AND pdt.sgo_vch_nombre like '%@p2%' OR CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) like '%@p2%' OR pca.sgo_int_categoriaproducto=@p3 OR pti.sgo_int_tipoproducto=@p4",$prm);
         $orderby = "ORDER BY CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) ASC";
         $log->log($sql);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $orderby),"PopUp('ordenproduccion','PopUp_Mant_Articulo','','","","PopUp('ordenproduccion','PopUp_Mant_Articulo','','","PopUp('ordenproduccion','Confirma_Eliminar_Articulo','','",null,array(),array(),array(),200,"");
      }
      function PopUp_Mant_Articulo($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$index=10;
         $impresion="";$modeloproducto=0;$color=0;$moneda="";$codigo="";$producto="";$undmedida="";$categoria="";$tipo="";$archivo="";$precio=0;$unxbulto=0;$tamproducto=0;$calproducto=0;
         $sql = "SELECT pdt.sgo_vch_codigo,pdt.sgo_vch_nombre,pdt.sgo_vch_textoimpresion,pdt.sgo_int_tamano,pdt.sgo_int_color,pdt.sgo_int_calidad, pdt.sgo_int_modelo, pdt.sgo_int_unidadmedida,pdt.sgo_int_categoriaproducto,pdt.sgo_int_tipo,pdt.sgo_vch_archivoinstrucciones,tar.sgo_dec_precio, tar.sgo_int_unidadesporbulto, tar.sgo_int_moneda FROM tbl_sgo_producto pdt LEFT JOIN tbl_sgo_tarifario tar ON tar.sgo_int_producto=pdt.sgo_int_producto and tar.sgo_int_cliente=0 WHERE pdt.sgo_int_producto = " . $prm;
         $result = $Obj->consulta($sql);
         while ($row = mysqli_fetch_array($result)){
            $impresion=$row["sgo_vch_textoimpresion"];$calproducto= $row["sgo_int_calidad"]; $color = $row["sgo_int_color"]; $tamproducto = $row["sgo_int_tamano"]; $moneda=$row["sgo_int_moneda"]; $codigo=$row["sgo_vch_codigo"];$producto=$row["sgo_vch_nombre"];$undmedida=$row["sgo_int_unidadmedida"];$categoria=$row["sgo_int_categoriaproducto"];$tipo=$row["sgo_int_tipo"];$archivo=$row["sgo_vch_archivoinstrucciones"];$precio=$row['sgo_dec_precio'];$unxbulto=$row['sgo_int_unidadesporbulto'];$modeloproducto=$row['sgo_int_modelo']; break;
         }
         $Val_Producto=new InputValidacion();
         $Val_Producto->InputValidacion('DocValue("fil_cmbproducto")!=""','Debe especificar el producto');
         $Val_Nombre=new InputValidacion();
         $Val_Nombre->InputValidacion('DocValue("fil_txtArticulo")!=""','Debe especificar el nombre del producto');
         $Val_UndMedida=new InputValidacion();
         $Val_UndMedida->InputValidacion('DocValue("fil_cmbundmedida")!=""','Debe especificar la unidad de medida');
         $Val_Categoria=new InputValidacion();
         $Val_Categoria->InputValidacion('DocValue("fil_cmbcatproducto")!=""','Debe especificar la categoria del producto');
         $Val_Tipo=new InputValidacion();
         $Val_Tipo->InputValidacion('DocValue("fil_cmbtipoproducto")!=""','Debe especificar el tipo producto');
         $Val_Estado=new InputValidacion();
         $Val_Estado->InputValidacion('DocValue("fil_txtDescuento")!=""','Debe especificar un descuento');
         $Val_Precio=new InputValidacion();
         $Val_Precio->InputValidacion('DocValue("fil_txtPrecioBase")!=""','Debe especificar un precio mayor o igual a 0');
         $Val_UnidadesPorBulto=new InputValidacion();
         $Val_UnidadesPorBulto->InputValidacion('DocValue("fil_txtUnidadesPorBulto")!=""','Debe especificar una cantidad de unidades por bulto mayor o igual a 1');
         $inputs=array(
            "Codigo" =>$Helper->textbox("fil_txtCodigo",++$index,$codigo,15,200,$TipoTxt->texto,"","","",""),
            "Articulo" =>$Helper->textbox("fil_txtArticulo",++$index,$producto,120,200,$TipoTxt->texto,"","","","",$Val_Nombre),
            "Und. Medida" => $Helper->combo_unidadmedida("fil_cmbundmedida",++$index,$undmedida,"",$Val_UndMedida),
            "Categoria" => $Helper->combo_categoriaproducto("fil_cmbcatproducto",++$index,$categoria,"Cargar_Combo('general','Combo_Tamano_x_CategoriaProducto','fil_cmbtamproducto','fil_cmbcatproducto','fil_cmbtamproducto');Cargar_Combo('general','Combo_Colores_x_CategoriaProducto','fil_cmbcolproducto','fil_cmbcatproducto','fil_cmbcolproducto');Cargar_Combo('general','Combo_Calidad_x_CategoriaProducto','fil_cmbcalproducto','fil_cmbcatproducto','fil_cmbcalproducto');Cargar_Combo('general','Combo_Modelo_x_CategoriaProducto','fil_cmbmodproducto','fil_cmbcatproducto','fil_cmbmodproducto');",$Val_Categoria),
            "Tipo" => $Helper->combo_tipoproducto("fil_cmbtipoproducto",++$index,$tipo,"",$Val_Tipo),
         	"Archivo" => $Helper->Upload_Cargar("fil_fileUpload_Articulo",++$index,$archivo,"ordenproduccion","Upload_Articulo","","","",200),
            "Moneda"=>$Helper->combo_moneda("fil_cmbMoneda", ++$index, $moneda, ""),
            "Precio Base" =>$Helper->textbox("fil_txtPrecioBase",++$index,(is_null($precio)?"0":$precio),18,100,$TipoTxt->decimal,"","","","",$Val_Precio),            
            "Un. x Bulto" =>$Helper->textbox("fil_txtUnidadesPorBulto",++$index,(is_null($unxbulto)?"0":$unxbulto),18,100,$TipoTxt->numerico,"","","","",$Val_UnidadesPorBulto),
         	"Tamano" => $Helper->combo_tamano_x_categoriaproducto("fil_cmbtamproducto",++$index,$categoria, $tamproducto,""),            
         	"Color" => $Helper->combo_colores_x_categoriaproducto("fil_cmbcolproducto",++$index,$categoria, $color,""),
         	"Calidad" => $Helper->combo_calidad_x_categoriaproducto("fil_cmbcalproducto",++$index,$categoria, $calproducto,""),
         	"Modelo" => $Helper->combo_modelo_x_categoriaproducto("fil_cmbmodproducto",++$index,$categoria, $modeloproducto,""),
         	"Impresión" =>$Helper->textbox("fil_txtImpresion",++$index,$impresion,120,200,$TipoTxt->texto,"","","","")
         );
         $buttons=array();
         $html = "<fieldset class='textoInput'><legend align='left'>Producto</legend>".$Helper->Crear_Layer("tbl_mant_articulo",$inputs,$buttons,2,700,"","")."</fieldset>";
         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Artículo",700,$html,$Helper->button("","Grabar",70,"Operacion('ordenproduccion','Mant_Articulo','tbl_mant_articulo','" . $prm . "')","textoInput"));
      }
      function Mant_Articulo($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($valor[0]!=0)
          {
            $sql= "	UPDATE 	tbl_sgo_producto 
            		SET 	sgo_vch_codigo='" . $valor[1] . "',
            				sgo_vch_nombre='".$valor[2]."',
            				sgo_int_unidadmedida=" . $valor[3] . ",
            				sgo_int_categoriaproducto=" . $valor[4] . ",
            				sgo_int_tipo=" . $valor[5] . ",
            				sgo_vch_archivoinstrucciones=" . ($valor[6]==""?"''":"'".$valor[6]."'") . ", 
            				sgo_int_color=".($valor[11]!=""?$valor[11]:"0").", 
            				sgo_int_tamano=".($valor[10]!=""?$valor[10]:"0").", 
            				sgo_int_calidad=".($valor[12]!=""?$valor[12]:"0").",
            				sgo_int_modelo=".($valor[13]!=""?$valor[13]:"0").", 
            				sgo_vch_textoimpresion='".($valor[14]!=""?$valor[14]:"")."' 
            		WHERE 	sgo_int_producto=" . $valor[0];
            if($Obj->execute($sql)!=-1)
            {
                if($valor[5]!="1"){
                   $sql="SELECT sgo_int_producto FROM tbl_sgo_tarifario WHERE sgo_int_cliente=0 and sgo_int_producto=" . $valor[0];
                   $result=$Obj->consulta($sql);
                   while ($row = mysqli_fetch_array($result))
                   {
                       $sql= "UPDATE tbl_sgo_tarifario SET sgo_dec_precio=" . $valor[8] . ", sgo_int_unidadesporbulto=" . $valor[9] . ($valor[7]!=""?", sgo_int_moneda=".$valor[7]:"")." WHERE sgo_int_cliente=0 and sgo_int_producto=" . $valor[0];
                       return $Obj->execute($sql);
                   }
                   $sql= "INSERT INTO tbl_sgo_tarifario (sgo_int_producto,sgo_int_cliente,sgo_dec_precio, sgo_int_unidadesporbulto". ($valor[7]==""?"":",sgo_int_moneda") .") VALUES (" . $valor[0] . ",0," . $valor[8] . "," . $valor[9] . ($valor[7]==""?"":",".$valor[7]) .")";
                   return $Obj->execute($sql);
                }
                return 1;
            }
          }
          else{
            $sql="	INSERT INTO tbl_sgo_producto 
            		(
            			sgo_vch_codigo,
            			sgo_vch_nombre,
            			sgo_int_unidadmedida,
            			sgo_int_categoriaproducto,
            			sgo_int_tipo,
            			sgo_vch_archivoinstrucciones,
            			sgo_dec_stock,
            			sgo_dec_stockminimo,
            			sgo_bit_activo, 
            			sgo_int_color, 
            			sgo_int_tamano, 
            			sgo_int_calidad,
            			sgo_int_modelo, 
            			sgo_vch_textoimpresion
            		)
            		VALUES 
            		('" 
            			. $valor[1] . "','" 
            			. $valor[2] . "'," 
            			. $valor[3] . "," 
            			. $valor[4] . "," 
            			. $valor[5] . "," 
            			. ($valor[6]===""?"''":"'".$valor[6]."'") . ",
            			0,
            			0,
            			1,"
            			.($valor[11]!=""?$valor[11]:"0").","
            			.($valor[10]!=""?$valor[10]:"0").","
            			.($valor[12]!=""?$valor[12]:"0").","
            			.($valor[13]!=""?$valor[13]:"0").","
            			.($valor[14]==""?"''":"'".$valor[14]."'").")";
            $id=$Obj->execute_insert($sql);
            if($id!=0)
            {
                if($valor[5]!="1"){
                  $sql= "INSERT INTO tbl_sgo_tarifario (sgo_int_producto,sgo_int_cliente,sgo_dec_precio, sgo_int_unidadesporbulto, sgo_int_moneda) 
                  		VALUES (" . $id . ",0," . $valor[8] . ", " . $valor[9] . ",".$valor[7].")";;
                  return $Obj->execute($sql);
                }
                return 1;
            }
          }
      }
      function Confirma_Eliminar_Articulo($prm){
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el producto ' . $this->Obtener_Nombre_Producto($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('ordenproduccion','Eliminar_Articulo','','" . $prm . "')"));
      }
      function Eliminar_Articulo($prm){
          $Obj=new mysqlhelper;
          $sql="UPDATE tbl_sgo_producto SET sgo_bit_activo = 0 WHERE sgo_int_producto=" . $prm;
//          echo '<script>alert("'.$sql .'")</script>';
          return $Obj->execute($sql);
      }

/****************************************************************FORMULA************************************************************************/
      function Grilla_Listar_Formula($prm,$size=800){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT pfo.sgo_int_productoformula as param, pdt.sgo_vch_nombre as Insumo, pfo.sgo_dec_cantidad as Cantidad, sgo_vch_instrucciones as Instrucciones
          FROM tbl_sgo_productoformula pfo
          INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=pfo.sgo_int_insumo
          WHERE pfo.sgo_int_producto=" . $prm;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('ordenproduccion','PopUp_Mant_ArticuloFormula','','" . $prm . "|","","PopUp('ordenproduccion','PopUp_Mant_ArticuloFormula','','" . $prm . "|","PopUp('ordenproduccion','Confirma_Eliminar_ArticuloFormula','','" . $prm . "|",$size,array(),array(),array(),20,"");
      }
      function PopUp_Mant_Formula($prm){
         $html="<div id='div_ArticuloFormula'>" .$this->Grilla_Listar_Formula($prm) . "</div>";$Helper=new htmlhelper;
         return $Helper->PopUp("","Fórmula Producto",700,$html,"");
      }
      function PopUp_Mant_ArticuloFormula($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$index=10;$valor=explode('|',$prm);
         $insumo=0;$cantidad=0;$obs="";
         $result = $Obj->consulta("SELECT sgo_int_producto, sgo_int_insumo,sgo_dec_cantidad,sgo_vch_instrucciones FROM tbl_sgo_productoformula WHERE sgo_int_productoformula = " . $valor[1]);
         while ($row = mysqli_fetch_array($result)){
            $insumo=$row["sgo_int_insumo"];$cantidad=$row["sgo_dec_cantidad"];$obs=$row["sgo_vch_instrucciones"];
         }
         $Val_Insumo=new InputValidacion();
         $Val_Insumo->InputValidacion('DocValue("fil_cmbinsumo")!=""','Debe especificar el insumo');
         $Val_Cantidad=new InputValidacion();
         $Val_Cantidad->InputValidacion('DocValue("fil_txtCantidad")!=""','Debe especificar el producto');
         $inputs=array(
            "Insumo" => $this->combo_insumo_formula("fil_cmbinsumo",++$index,$valor[0] . '|' . $insumo,$insumo,"",$Val_Insumo),
            "Cantidad" =>$Helper->textbox("fil_txtCantidad",++$index,$cantidad,18,100,$TipoTxt->decimal,"","","","",$Val_Cantidad),
            "Observaciones" =>$Helper->textarea("fil_txtObservaciones",++$index,$obs,55,3,"","",""),
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_articuloformula",$inputs,$buttons,1,600,"","");
         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Artículo",600,$html,$Helper->button("","Grabar",70,"Operacion('ordenproduccion','Mant_ArticuloFormula','tbl_mant_articuloformula','" . $prm . "')","textoInput"));
      }
      function Mant_ArticuloFormula($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($valor[1]!=0) $sql= "UPDATE tbl_sgo_productoformula SET sgo_int_insumo=" . $valor[2] . ",sgo_dec_cantidad=" . $valor[3] . ",sgo_vch_instrucciones='" . $valor[4] . "' WHERE sgo_int_productoformula=" . $valor[1];
          else $sql="INSERT INTO tbl_sgo_productoformula (sgo_int_producto,sgo_int_insumo,sgo_dec_cantidad,sgo_vch_instrucciones) VALUES (" . $valor[0] . "," . $valor[2] . "," . $valor[3] . ",'" . $valor[4] . "')";
//          echo '<script>alert("'.$sql .'")</script>';
          return $Obj->execute($sql);
      }
      function Confirma_Eliminar_ArticuloFormula($prm){
          $Helper=new htmlhelper;$valor=explode('|',$prm);
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la fórmula con insumo ') . $this->Obtener_Nombre_Insumo($valor[1]) . '?',$Helper->button("", "Si", 70, "Operacion('ordenproduccion','Eliminar_ArticuloFormula','','" . $prm . "')"));
      }
      function Eliminar_ArticuloFormula($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          $sql="DELETE FROM tbl_sgo_productoformula WHERE sgo_int_productoformula=" . $valor[1];
//          echo '<script>alert("'.$sql .'")</script>';
          return $Obj->execute($sql);
      }

/****************************************************************MANTENIMIENTO PRECIOS************************************************************************/
      function Filtros_Listar_Tarifario(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$index=10;
         $inputs=array(
            "Cliente" => $Helper->textbox("fil_txtBusquedaCliente",++$index,"",120,250,$TipoTxt->texto,"","","","")
         );
         $buttons=array($Helper->button("btnBuscarTarifario","Buscar",70,"Buscar_Grilla('ordenproduccion','Grilla_Listar_Tarifario','tbl_listartarifario','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listartarifario",$inputs,$buttons,2,400,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Tarifario($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql =($prm==""?"SELECT 0 as param,'-- POR DEFECTO --' as Cliente,'' as 'RUC',COUNT(tar.sgo_int_cliente) as Tarifas
               FROM tbl_sgo_tarifario tar WHERE tar.sgo_int_cliente=0
               UNION ALL ":"") .
          "SELECT cli.sgo_int_cliente as param, concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) as Cliente,per.sgo_vch_nrodocumentoidentidad as 'RUC',COUNT(tar.sgo_int_cliente) as Tarifas
          FROM tbl_sgo_cliente cli
          INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=cli.sgo_int_cliente
          LEFT JOIN tbl_sgo_tarifario tar on tar.sgo_int_cliente=cli.sgo_int_cliente
          where 1=1";
         if($prm!="")
         {
         	$sql.=" and per.sgo_vch_nombre like '%".$prm."%'";
         }
         $sql.=" and cli.sgo_bit_activo=1";
         $groupby = " GROUP BY cli.sgo_int_cliente,per.sgo_vch_nombre";
         $orderby = " ORDER BY 2 ASC";         
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $groupby . " " . $orderby),"","","PopUp('ordenproduccion','PopUp_Mant_Tarifario','','","",null,array(),array(),array(),20,"");
      }
      function Grilla_Listar_Tarifa($prm,$size=800){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT tar.sgo_int_tarifa as param, CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as Producto, moneda.sgo_vch_simbolo as 'Moneda', tar.sgo_dec_precio as Precio, tar.sgo_int_unidadesporbulto as 'Unidades x Bulto'
          FROM tbl_sgo_tarifario tar
          LEFT 	JOIN tbl_sgo_moneda moneda
          on	tar.sgo_int_moneda = moneda.sgo_int_moneda          
          INNER JOIN tbl_sgo_producto pdt on pdt.sgo_int_producto=tar.sgo_int_producto
          LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
          on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
          and	pdt.sgo_int_color = catprodcol.sgo_int_color
          LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
          on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
          and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano
          LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
          on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
          and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
          WHERE tar.sgo_int_cliente=" . $prm;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('ordenproduccion','PopUp_Mant_Tarifa','','" . $prm . "|","grilla_tarifario","PopUp('ordenproduccion','PopUp_Mant_Tarifa','','" . $prm . "|","PopUp('ordenproduccion','Confirma_Eliminar_Tarifa','','" . $prm . "|",$size,array(),array(),array(),10,"");
      }
      function PopUp_Mant_Tarifario($prm){
//         $html="<div id='div_TarifaTarifario'>" .$this->Grilla_Listar_Tarifa($prm) . "</div>";$Helper=new htmlhelper;
         $Helper=new htmlhelper;
         //$html= $this->Grilla_Listar_Tarifa($prm);
         $html="<div id='div_TarifaTarifario'>" .$this->Grilla_Listar_Tarifa($prm) . "</div>";$Helper=new htmlhelper;
         return $Helper->PopUp("popUpTarifasMF","Tarifas por Producto",500,$html,"");
      }
      function PopUp_Mant_Tarifa($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=10;$valor=explode('|',$prm);
         $producto=0;$precio=""; $unxbulto="";$moneda="";
         $result = $Obj->consulta("SELECT sgo_int_producto,sgo_dec_precio, sgo_int_unidadesporbulto, sgo_int_moneda FROM tbl_sgo_tarifario WHERE sgo_int_tarifa = " . $valor[1]);
         while ($row = mysqli_fetch_array($result)){
            $producto=$row["sgo_int_producto"];$precio=$row["sgo_dec_precio"];$unxbulto=$row["sgo_int_unidadesporbulto"];$moneda=$row["sgo_int_moneda"];
         }
         $Val_Producto=new InputValidacion();
         $Val_Producto->InputValidacion('DocValue("fil_cmbproducto")!=""','Debe especificar el producto');
         $Val_Precio=new InputValidacion();
         $Val_Precio->InputValidacion('DocValue("fil_txtPrecio")!=""','Debe especificar un precio mayor o igual a 0');
         $Val_UnidadesPorBulto=new InputValidacion();
         $Val_UnidadesPorBulto->InputValidacion('DocValue("fil_txtUnidadesPorBulto")!=""','Debe especificar una cantidad de unidades por bulto mayor o igual a 1');
         $inputs=array(
            "Producto " => $this->combo_producto_tarifario("fil_cmbproducto",++$index,$valor[0] . '|' . $producto,$producto,"Cargar_Input('general','Valor_Producto_Precio','fil_txtPrecio','fil_cmbproducto','fil_txtPrecio');Cargar_Input('general','Valor_Producto_UnidadxBulto','fil_txtUnidadesPorBulto','fil_cmbproducto','fil_txtUnidadesPorBulto');",$Val_Producto),
            "Moneda" => $Helper->combo_moneda("fil_txtmoneda", ++$index, $moneda, ""),
            "Precio" => $Helper->textbox("fil_txtPrecio",++$index,$precio,18,100,$TipoTxt->decimal,"","","","",$Val_Precio),
            "Un. x Bulto" =>$Helper->textbox("fil_txtUnidadesPorBulto",++$index,(is_null($unxbulto)?"0":$unxbulto),18,100,$TipoTxt->numerico,"","","","",$Val_UnidadesPorBulto),
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_tarifa34",$inputs,$buttons,2,500,"","");
         return $Helper->PopUp("",($prm==0?"Nueva":"Actualizar") . " Tarifa",500,$html,$Helper->button("","Grabar",70,"Operacion('ordenproduccion','Mant_Tarifa','tbl_mant_tarifa34','" . $prm . "')","textoInput"));
      }
      function Mant_Tarifa($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($valor[1]!=0) $sql= "UPDATE tbl_sgo_tarifario SET sgo_int_producto=" . $valor[2] . ",sgo_int_moneda=".$valor[3]." ,sgo_dec_precio=" . $valor[4] . ", sgo_int_unidadesporbulto = " . $valor[5] . " WHERE sgo_int_tarifa=" . $valor[1];
          else $sql="INSERT INTO tbl_sgo_tarifario (sgo_int_cliente,sgo_int_producto,sgo_dec_precio, sgo_int_unidadesporbulto, sgo_int_moneda) VALUES (" . $valor[0] . "," . $valor[2] . "," . $valor[4] . ", " . $valor[5] . ",".$valor[3].")";
          
//          echo '<script>alert("'.$sql .'")</script>';
          return $Obj->execute($sql);
      }
      function Confirma_Eliminar_Tarifa($prm){
          $Helper=new htmlhelper;$valor=explode('|',$prm);
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la tarifa del producto ' . $this->Obtener_Nombre_Tarifa($valor[1]) . '?'),$Helper->button("", "Si", 70, "Operacion('ordenproduccion','Eliminar_Tarifa','','" . $prm . "')"));
      }
      function Eliminar_Tarifa($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          $sql="DELETE FROM tbl_sgo_tarifario WHERE sgo_int_tarifa=" . $valor[1];
//          echo '<script>alert("'.$sql .'")</script>';
          return $Obj->execute($sql);
      }

/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
      function Obtener_Nombre_Producto($prm){
          $Obj=new mysqlhelper;$res="";
          $result= $Obj->consulta("SELECT sgo_vch_nombre FROM tbl_sgo_producto WHERE sgo_int_producto=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            $res = $row["sgo_vch_nombre"];
            break;
          }
          return $res;
      }
      function Obtener_Nombre_Producto_x_Codigo($prm){
          $Obj=new mysqlhelper;$res="";
          $result= $Obj->consulta("SELECT sgo_vch_nombre FROM tbl_sgo_producto WHERE sgo_vch_codigo='" . $prm . "'");
          while ($row = mysqli_fetch_array($result))
          {
            $res = $row["sgo_vch_nombre"];
            break;
          }
          return $res;
      }
      function Obtener_Nombre_Insumo($prm){
          $Obj=new mysqlhelper;$res="";
          $result= $Obj->consulta("SELECT pdt.sgo_vch_nombre FROM tbl_sgo_productoformula pfo INNER JOIN tbl_sgo_producto pdt ON pdt.sgo_int_producto=pfo.sgo_int_insumo WHERE sgo_int_productoformula=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            $res = $row["sgo_vch_nombre"];
            break;
          }
          return $res;
      }
      function Obtener_Nombre_Tarifa($prm){
          $Obj=new mysqlhelper;$res="";
          $result= $Obj->consulta("SELECT pdt.sgo_vch_nombre FROM tbl_sgo_tarifario tar INNER JOIN tbl_sgo_producto pdt ON pdt.sgo_int_producto=tar.sgo_int_producto WHERE sgo_int_tarifa=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            $res = $row["sgo_vch_nombre"];
            break;
          }
          return $res;
      }
      function Obtener_IDProducto_x_Codigo($prm){
          $Obj=new mysqlhelper;$res="";
          $result= $Obj->consulta("SELECT sgo_int_producto FROM tbl_sgo_producto WHERE sgo_vch_codigo='" . $prm . "'");
          while ($row = mysqli_fetch_array($result))
          {
            $res = $row["sgo_int_producto"];
            break;
          }
          return $res;
      }
      function Obtener_Producto_Precio($pdt,$cli){
          if($pdt!==""){
            $Obj=new mysqlhelper;
            $result= $Obj->consulta("SELECT sgo_dec_precio FROM tbl_sgo_tarifario WHERE sgo_int_producto=" . $pdt . " and sgo_int_cliente=" . $cli);
            while ($row = mysqli_fetch_array($result))
            {
              return $row["sgo_dec_precio"];
            }
            $result= $Obj->consulta("SELECT sgo_dec_precio FROM tbl_sgo_tarifario WHERE sgo_int_producto=" . $pdt . " and sgo_int_cliente=0");
            while ($row = mysqli_fetch_array($result))
            {
              return $row["sgo_dec_precio"];
            }
            return 0;
          }
          else return "";
      }
      function Obtener_Producto_UnidadesxBulto($pdt,$cli){
          if($pdt!==""){
            $Obj=new mysqlhelper;
            $result= $Obj->consulta("SELECT sgo_int_unidadesporbulto FROM tbl_sgo_tarifario WHERE sgo_int_producto=" . $pdt . " and sgo_int_cliente=" . $cli);
            while ($row = mysqli_fetch_array($result))
            {
              return $row["sgo_int_unidadesporbulto"];
            }
            $result= $Obj->consulta("SELECT sgo_int_unidadesporbulto FROM tbl_sgo_tarifario WHERE sgo_int_producto=" . $pdt . " and sgo_int_cliente=0");
            while ($row = mysqli_fetch_array($result))
            {
              return $row["sgo_int_unidadesporbulto"];
            }
            return 0;
          }
          else return "";
      }
      function Obtener_Nombre_OrdenProducion($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_ordenproduccion FROM tbl_sgo_ordenproduccion WHERE sgo_int_ordenproduccion=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_ordenproduccion"];
          }
          return "";
      }
      function Obtener_Producto_UnidadMedida($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT und.sgo_vch_abreviatura FROM tbl_sgo_producto pdt INNER JOIN tbl_sgo_unidadmedida und ON pdt.sgo_int_unidadmedida=und.sgo_int_unidadmedida WHERE pdt.sgo_int_producto=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_abreviatura"];
          }
          return "";
      }
      function Obtener_Producto_Stock($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_dec_stock FROM tbl_sgo_producto WHERE sgo_int_producto=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_dec_stock"];
          }
          return "0";
      }
      function Obtener_ArchivoImpresion_OrdenProduccion($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT ifnull(tco.sgo_vch_archivoimpresion,'') as sgo_vch_archivoimpresion
          FROM tbl_sgo_ordenproduccion cov
          INNER JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=cov.sgo_int_tipocomprobante
          WHERE cov.sgo_int_ordenproduccion=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_archivoimpresion"];
          }
          return "";
      }

/********************************************************************** COMBOS *****************************************************************************/
/********************************************************************** COMBOS *****************************************************************************/
/********************************************************************** COMBOS *****************************************************************************/
     function combo_producto_tarifario($id,$posicion,$prm,$selected,$onchange,$validacion=null){
        $Helper=new htmlhelper;$valor=explode('|',$prm);
//        return "SELECT sgo_int_producto as value, sgo_vch_nombre as text FROM tbl_sgo_producto WHERE sgo_bit_activo=1 and sgo_int_tipo in (2,3) and sgo_int_producto NOT IN (SELECT sgo_int_producto FROM tbl_sgo_tarifario WHERE sgo_int_cliente=" . $valor[0] . " AND sgo_int_producto!=" . $valor[1] . ")";
        return $Helper->combo("	SELECT 	pdt.sgo_int_producto as value, CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as text 
        						FROM 	tbl_sgo_producto pdt
        						LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
						          on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
						          and	pdt.sgo_int_color = catprodcol.sgo_int_color
						          LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
						          on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
						          and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano
						          LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
						          on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
						          and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
        						WHERE 	pdt.sgo_bit_activo=1 
        						and   	pdt.sgo_int_tipo in (2,3) 
        						and 	pdt.sgo_int_producto NOT IN (SELECT sgo_int_producto FROM tbl_sgo_tarifario WHERE sgo_int_cliente=" . $valor[0] . " AND sgo_int_producto!=" . $valor[1] . ") order by pdt.sgo_vch_nombre asc",
                $id,$posicion,250,$selected,$onchange,$validacion);
     }
     function combo_insumo_formula($id,$posicion,$prm,$selected,$onchange,$validacion=null){
        $Helper=new htmlhelper;$valor=explode('|',$prm);
        return $Helper->combo("SELECT sgo_int_producto as value, sgo_vch_nombre as text FROM tbl_sgo_producto WHERE sgo_bit_activo=1 and sgo_int_tipo in (1,2) and sgo_int_producto NOT IN (SELECT sgo_int_insumo FROM tbl_sgo_productoformula WHERE sgo_int_producto=" . $valor[0] . " AND sgo_int_insumo!=" . $valor[1] . ")",
                $id,$posicion,250,$selected,$onchange,$validacion);
     }
  }
?>