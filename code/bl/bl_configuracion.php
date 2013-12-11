<?php  include_once('../../code/lib/htmlhelper.php'); include_once('../../code/be/be_general.php');
  Class bl_configuracion
  {
  /*************************************************CARACTERISTICA CATEGORIA PRODUCTO - CALIDAD *****************************************************/
    function Filtros_Listar_CategoriaProductoCalidad()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Categoria" => $Helper->combo_categoriaproducto("fil_cmbTransportista",++$index,"","","","",""),            
            "Nombre" => $Helper->textbox("fil_txtNombreTamano",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")
         );
         $buttons=array($Helper->button("btnBuscarTamano","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoCalidad','tbl_lista','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_lista",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
    function Grilla_Listar_CategoriaProductoCalidad($prm)
      {
         $valor = explode('|',$prm);
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_calidad as param, sgo_vch_calidad as 'CALIDAD', sgo_int_orden as 'ORDEN'
                from tbl_sgo_categoriaproductocalidad where 1=1";
         if($valor[0]!="")
         {
            $sql.=" and sgo_int_categoriaproducto =".$valor[0];
         }
         if($valor[1]!="")
         {
            $sql.=" and sgo_vch_calidad like '%".$valor[1]."%'";
         }        
         $sql.=" order by sgo_int_orden"; 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('configuracion','PopupCategoriaProductoCalidad','','","PopUp('configuracion','PopupCategoriaProductoCalidad','','","","PopUp('configuracion','ConfirmaEliminarCategoriaProductoCalidad','','",null,$btn_extra,array(),array(),20,"");
      }
  function PopUp_CategoriaCalidad($prm)
      {
         $Helper=new htmlhelper;
         $Obj=new mysqlhelper();
         $TipoTxt=new TipoTextBox;
         $index=0;
         $valor = explode('|', $prm);
         $categoria="";$tamano="";$orden="";
         $sql = "   select  sgo_int_calidad as param, sgo_int_categoriaproducto, sgo_vch_calidad, sgo_int_orden
                    from    tbl_sgo_categoriaproductocalidad
                    where   sgo_int_calidad=".$valor[0];
         $result = $Obj->consulta($sql);

         while ($row = mysqli_fetch_array($result))
         {
            $categoria=$row["sgo_int_categoriaproducto"];
            $tamano=$row["sgo_vch_calidad"];
            $orden=$row["sgo_int_orden"];
            break;
         }

         $Val_TAMANO=new InputValidacion();
         $Val_TAMANO->InputValidacion('DocValue("fil_txtTamanoPU")!=""','Debe especificar la calidad');

         $Val_ORDEN=new InputValidacion();
         $Val_ORDEN->InputValidacion('DocValue("fil_txtOrdenPU")!=""','Debe especificar el orden');

         $Val_CATEGORIA=new InputValidacion();
         $Val_CATEGORIA->InputValidacion('DocValue("fil_cboCategoriaPU")!="-1"','Debe indicar la categoria');

         $buttons=array();
         $html = '<fieldset class="textoInput"><legend align= "left">Calidad</legend>';
         $html .= "<table border='0' id='tbl_mant_tamano' cellspacing='0' cellpadding='0' width='500px' class='textoInput'>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Categoria</td><td width='10px'></td><td>".$Helper->combo_categoriaproducto("fil_cboCategoriaPU",++$index,$categoria,"",$Val_CATEGORIA,"")."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Calidad</td><td width='10px'></td><td>".$Helper->textbox("fil_txtTamanoPU",++$index,$tamano,64,250,"","","","","",$Val_TAMANO)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Orden</td><td width='10px'></td><td>".$Helper->textbox("fil_txtOrdenPU",++$index,$orden,2,50,$TipoTxt->numerico,"","","","",$Val_ORDEN)."</td></tr>";
         $html .= "<tr><td colspan='3' height='10px'></td></tr>";
         $html .= "</table>";
         $html .= '</fieldset>';

         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Calidad",500,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_CategoriaProductoCalidad','tbl_mant_tamano','" . $prm . "')","textoInput"));
      }
      function Mant_CategoriaProductoCalidad($prm)
	  {
		 $Obj=new mysqlhelper;
		 $valor=explode('|',$prm);

         try{
                 if($valor[0]=="0")
                 {
    				 $trans=$Obj->transaction();
    		 		 $sql = "INSERT INTO tbl_sgo_categoriaproductocalidad (sgo_int_categoriaproducto, sgo_vch_calidad, sgo_int_orden) values (".$valor[1].",'".$valor[2]."',".$valor[3].")";
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $idCh=mysqli_insert_id($trans);
                     $trans->close();
                     return $idCh;
                 }
                 else
                 {
                     $trans=$Obj->transaction();
    		 		 $sql = "UPDATE tbl_sgo_categoriaproductocalidad set sgo_int_categoriaproducto=".$valor[1].", sgo_vch_calidad='".$valor[2]."', sgo_int_orden=".$valor[3]." where sgo_int_calidad = ".$valor[0];
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $trans->close();return $valor[0];
                 }
		 }
		 catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
	  }
      function Confirma_Eliminar_CategoriaProductoCalidad($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_calidad FROM tbl_sgo_categoriaproductocalidad WHERE sgo_int_calidad=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar la calidad "' . $row["sgo_vch_calidad"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_CategoriaProductoCalidad','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_CategoriaProductoCalidad($prm)
      {
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("delete from tbl_sgo_categoriaproductocalidad  WHERE sgo_int_calidad=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoCalidad','tbl_lista','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      } 
/*************************************************CARACTERISTICA CATEGORIA PRODUCTO - MODELO *****************************************************/
    function Filtros_Listar_CategoriaProductoModelo()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Categoria" => $Helper->combo_categoriaproducto("fil_cmbTransportista",++$index,"","","","",""),            
            "Nombre" => $Helper->textbox("fil_txtNombreTamano",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")
         );
         $buttons=array($Helper->button("btnBuscarTamano","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoModelo','tbl_lista','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_lista",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
    function Grilla_Listar_CategoriaProductoModelo($prm)
      {
         $valor = explode('|',$prm);
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_modelo as param, sgo_vch_modelo as 'MODELO', sgo_int_orden as 'ORDEN'
                from tbl_sgo_categoriaproductomodelo where 1=1";
         if($valor[0]!="")
         {
            $sql.=" and sgo_int_categoriaproducto =".$valor[0];
         }
         if($valor[1]!="")
         {
            $sql.=" and sgo_vch_modelo like '%".$valor[1]."%'";
         }        
         $sql.=" order by sgo_int_orden"; 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('configuracion','PopupCategoriaProductoModelo','','","PopUp('configuracion','PopupCategoriaProductoModelo','','","","PopUp('configuracion','ConfirmaEliminarCategoriaProductoModelo','','",null,$btn_extra,array(),array(),20,"");
      }
  	  function PopUp_CategoriaModelo($prm)
      {
         $Helper=new htmlhelper;
         $Obj=new mysqlhelper();
         $TipoTxt=new TipoTextBox;
         $index=0;
         $valor = explode('|', $prm);
         $categoria="";$tamano="";$orden="";
         $sql = "   select  sgo_int_modelo as param, sgo_int_categoriaproducto, sgo_vch_modelo, sgo_int_orden
                    from    tbl_sgo_categoriaproductomodelo
                    where   sgo_int_modelo=".$valor[0];
         $result = $Obj->consulta($sql);

         while ($row = mysqli_fetch_array($result))
         {
            $categoria=$row["sgo_int_categoriaproducto"];
            $tamano=$row["sgo_vch_modelo"];
            $orden=$row["sgo_int_orden"];
            break;
         }

         $Val_TAMANO=new InputValidacion();
         $Val_TAMANO->InputValidacion('DocValue("fil_txtTamanoPU")!=""','Debe especificar el modelo');

         $Val_ORDEN=new InputValidacion();
         $Val_ORDEN->InputValidacion('DocValue("fil_txtOrdenPU")!=""','Debe especificar el orden');

         $Val_CATEGORIA=new InputValidacion();
         $Val_CATEGORIA->InputValidacion('DocValue("fil_cboCategoriaPU")!="-1"','Debe indicar la categoria');

         $buttons=array();
         $html = '<fieldset class="textoInput"><legend align= "left">Modelo</legend>';
         $html .= "<table border='0' id='tbl_mant_tamano' cellspacing='0' cellpadding='0' width='500px' class='textoInput'>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Categoria</td><td width='10px'></td><td>".$Helper->combo_categoriaproducto("fil_cboCategoriaPU",++$index,$categoria,"",$Val_CATEGORIA,"")."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Modelo</td><td width='10px'></td><td>".$Helper->textbox("fil_txtTamanoPU",++$index,$tamano,64,250,"","","","","",$Val_TAMANO)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Orden</td><td width='10px'></td><td>".$Helper->textbox("fil_txtOrdenPU",++$index,$orden,2,50,$TipoTxt->numerico,"","","","",$Val_ORDEN)."</td></tr>";
         $html .= "<tr><td colspan='3' height='10px'></td></tr>";
         $html .= "</table>";
         $html .= '</fieldset>';

         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Modelo",500,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_CategoriaProductoModelo','tbl_mant_tamano','" . $prm . "')","textoInput"));
      }
      function Mant_CategoriaProductoModelo($prm)
	  {
		 $Obj=new mysqlhelper;
		 $valor=explode('|',$prm);

         try{
                 if($valor[0]=="0")
                 {
    				 $trans=$Obj->transaction();
    		 		 $sql = "INSERT INTO tbl_sgo_categoriaproductomodelo (sgo_int_categoriaproducto, sgo_vch_modelo, sgo_int_orden) values (".$valor[1].",'".$valor[2]."',".$valor[3].")";
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $idCh=mysqli_insert_id($trans);
                     $trans->close();
                     return $idCh;
                 }
                 else
                 {
                     $trans=$Obj->transaction();
    		 		 $sql = "UPDATE tbl_sgo_categoriaproductomodelo set sgo_int_categoriaproducto=".$valor[1].", sgo_vch_modelo='".$valor[2]."', sgo_int_orden=".$valor[3]." where sgo_int_modelo = ".$valor[0];
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $trans->close();return $valor[0];
                 }
		 }
		 catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
	  }
  	  function Confirma_Eliminar_CategoriaProductoModelo($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_modelo FROM tbl_sgo_categoriaproductomodelo WHERE sgo_int_modelo=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('¿Esta seguro de eliminar el modelo"' . $row["sgo_vch_modelo"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_CategoriaProductoModelo','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_CategoriaProductoModelo($prm)
      {
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("delete from tbl_sgo_categoriaproductomodelo  WHERE sgo_int_modelo=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoModelo','tbl_lista','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      } 
   /*************************************************CARACTERISTICA CATEGORIA PRODUCTO - COLOR *****************************************************/
    function Filtros_Listar_CategoriaProductoColor()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Categoria" => $Helper->combo_categoriaproducto("fil_cmbTransportista",++$index,"","","","",""),            
            "Nombre" => $Helper->textbox("fil_txtNombreTamano",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")
         );
         $buttons=array($Helper->button("btnBuscarTamano","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoColor','tbl_lista','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_lista",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
    function Grilla_Listar_CategoriaProductoColor($prm)
      {
         $valor = explode('|',$prm);
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_color as param, sgo_vch_color as 'COLOR', sgo_int_orden as 'ORDEN'
                from tbl_sgo_categoriaproductocolor where 1=1";
         if($valor[0]!="")
         {
            $sql.=" and sgo_int_categoriaproducto =".$valor[0];
         }
         if($valor[1]!="")
         {
            $sql.=" and sgo_vch_color like '%".$valor[1]."%'";
         }        
         $sql.=" order by sgo_int_orden"; 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('configuracion','PopupCategoriaProductoColor','','","PopUp('configuracion','PopupCategoriaProductoColor','','","","PopUp('configuracion','ConfirmaEliminarCategoriaProductoColor','','",null,$btn_extra,array(),array(),20,"");
      }
      function PopUp_CategoriaColor($prm)
      {
         $Helper=new htmlhelper;
         $Obj=new mysqlhelper();
         $TipoTxt=new TipoTextBox;
         $index=0;
         $valor = explode('|', $prm);
         $categoria="";$tamano="";$orden="";
         $sql = "   select  sgo_int_color as param, sgo_int_categoriaproducto, sgo_vch_color, sgo_int_orden
                    from    tbl_sgo_categoriaproductocolor
                    where   sgo_int_color=".$valor[0];
         $result = $Obj->consulta($sql);

         while ($row = mysqli_fetch_array($result))
         {
            $categoria=$row["sgo_int_categoriaproducto"];
            $tamano=$row["sgo_vch_color"];
            $orden=$row["sgo_int_orden"];
            break;
         }

         $Val_TAMANO=new InputValidacion();
         $Val_TAMANO->InputValidacion('DocValue("fil_txtTamanoPU")!=""','Debe especificar el color');

         $Val_ORDEN=new InputValidacion();
         $Val_ORDEN->InputValidacion('DocValue("fil_txtOrdenPU")!=""','Debe especificar el orden');

         $Val_CATEGORIA=new InputValidacion();
         $Val_CATEGORIA->InputValidacion('DocValue("fil_cboCategoriaPU")!="-1"','Debe indicar la categoria');

         $buttons=array();
         $html = '<fieldset class="textoInput"><legend align= "left">Color</legend>';
         $html .= "<table border='0' id='tbl_mant_tamano' cellspacing='0' cellpadding='0' width='500px' class='textoInput'>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Categoria</td><td width='10px'></td><td>".$Helper->combo_categoriaproducto("fil_cboCategoriaPU",++$index,$categoria,"",$Val_CATEGORIA,"")."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Color</td><td width='10px'></td><td>".$Helper->textbox("fil_txtTamanoPU",++$index,$tamano,64,250,"","","","","",$Val_TAMANO)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Orden</td><td width='10px'></td><td>".$Helper->textbox("fil_txtOrdenPU",++$index,$orden,2,50,$TipoTxt->numerico,"","","","",$Val_ORDEN)."</td></tr>";
         $html .= "<tr><td colspan='3' height='10px'></td></tr>";
         $html .= "</table>";
         $html .= '</fieldset>';

         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Color",500,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_CategoriaProductoColor','tbl_mant_tamano','" . $prm . "')","textoInput"));
      }
      function Mant_CategoriaProductoColor($prm)
	  {
		 $Obj=new mysqlhelper;
		 $valor=explode('|',$prm);

         try{
                 if($valor[0]=="0")
                 {
    				 $trans=$Obj->transaction();
    		 		 $sql = "INSERT INTO tbl_sgo_categoriaproductocolor (sgo_int_categoriaproducto, sgo_vch_color, sgo_int_orden) values (".$valor[1].",'".$valor[2]."',".$valor[3].")";
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $idCh=mysqli_insert_id($trans);
                     $trans->close();
                     return $idCh;
                 }
                 else
                 {
                     $trans=$Obj->transaction();
    		 		 $sql = "UPDATE tbl_sgo_categoriaproductocolor set sgo_int_categoriaproducto=".$valor[1].", sgo_vch_color='".$valor[2]."', sgo_int_orden=".$valor[3]." where sgo_int_color = ".$valor[0];
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $trans->close();return $valor[0];
                 }
		 }
		 catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
	  }
      function Confirma_Eliminar_CategoriaProductoColor($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_color FROM tbl_sgo_categoriaproductocolor WHERE sgo_int_color=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar el color "' . $row["sgo_vch_color"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_CategoriaProductoColor','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_CategoriaProductoColor($prm)
      {
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("delete from tbl_sgo_categoriaproductocolor  WHERE sgo_int_color=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoColor','tbl_lista','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }  	
  /*************************************************CARACTERISTICA CATEGORIA PRODUCTO - TAMANO *****************************************************/
    function Filtros_Listar_CategoriaProductoTamano()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Categoria" => $Helper->combo_categoriaproducto("fil_cmbTransportista",++$index,"","","","",""),            
            "Nombre" => $Helper->textbox("fil_txtNombreTamano",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")
         );
         $buttons=array($Helper->button("btnBuscarTamano","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoTamano','tbl_lista','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_lista",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
    function Grilla_Listar_CategoriaProductoTamano($prm)
      {
         $valor = explode('|',$prm);
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_tamano as param, sgo_vch_tamano as 'TAMANO', sgo_int_orden as 'ORDEN'
                from tbl_sgo_categoriaproductotamano where 1=1";
         if($valor[0]!="")
         {
            $sql.=" and sgo_int_categoriaproducto =".$valor[0];
         }
         if($valor[1]!="")
         {
            $sql.=" and sgo_vch_tamano like '%".$valor[1]."%'";
         }        
         $sql.=" order by sgo_int_orden"; 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('configuracion','PopupCategoriaProductoTamano','','","PopUp('configuracion','PopupCategoriaProductoTamano','','","","PopUp('configuracion','ConfirmaEliminarCategoriaProductoTamano','','",null,$btn_extra,array(),array(),20,"");
      }
      function PopUp_CategoriaTamano($prm)
      {
         $Helper=new htmlhelper;
         $Obj=new mysqlhelper();
         $TipoTxt=new TipoTextBox;
         $index=0;
         $valor = explode('|', $prm);
         $categoria="";$tamano="";$orden="";
         $sql = "   select  sgo_int_tamano as param, sgo_int_categoriaproducto, sgo_vch_tamano, sgo_int_orden
                    from    tbl_sgo_categoriaproductotamano
                    where   sgo_int_tamano=".$valor[0];
         $result = $Obj->consulta($sql);

         while ($row = mysqli_fetch_array($result))
         {
            $categoria=$row["sgo_int_categoriaproducto"];
            $tamano=$row["sgo_vch_tamano"];
            $orden=$row["sgo_int_orden"];
            break;
         }

         $Val_TAMANO=new InputValidacion();
         $Val_TAMANO->InputValidacion('DocValue("fil_txtTamanoPU")!=""','Debe especificar el tamaño');

         $Val_ORDEN=new InputValidacion();
         $Val_ORDEN->InputValidacion('DocValue("fil_txtOrdenPU")!=""','Debe especificar el orden');

         $Val_CATEGORIA=new InputValidacion();
         $Val_CATEGORIA->InputValidacion('DocValue("fil_cboCategoriaPU")!="-1"','Debe indicar la categoria');

         $buttons=array();
         $html = '<fieldset class="textoInput"><legend align= "left">Tamaño</legend>';
         $html .= "<table border='0' id='tbl_mant_tamano' cellspacing='0' cellpadding='0' width='500px' class='textoInput'>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Categoria</td><td width='10px'></td><td>".$Helper->combo_categoriaproducto("fil_cboCategoriaPU",++$index,$categoria,"",$Val_CATEGORIA,"")."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Tamano</td><td width='10px'></td><td>".$Helper->textbox("fil_txtTamanoPU",++$index,$tamano,64,250,"","","","","",$Val_TAMANO)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Orden</td><td width='10px'></td><td>".$Helper->textbox("fil_txtOrdenPU",++$index,$orden,2,50,$TipoTxt->numerico,"","","","",$Val_ORDEN)."</td></tr>";
         $html .= "<tr><td colspan='3' height='10px'></td></tr>";
         $html .= "</table>";
         $html .= '</fieldset>';

         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Tamaño",500,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_CategoriaProductoTamano','tbl_mant_tamano','" . $prm . "')","textoInput"));
      }
      function Mant_CategoriaProductoTamano($prm)
	  {
		 $Obj=new mysqlhelper;
		 $valor=explode('|',$prm);

         try{
                 if($valor[0]=="0")
                 {
    				 $trans=$Obj->transaction();
    		 		 $sql = "INSERT INTO tbl_sgo_categoriaproductotamano (sgo_int_categoriaproducto, sgo_vch_tamano, sgo_int_orden) values (".$valor[1].",'".$valor[2]."',".$valor[3].")";
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $idCh=mysqli_insert_id($trans);
                     $trans->close();
                     return $idCh;
                 }
                 else
                 {
                     $trans=$Obj->transaction();
    		 		 $sql = "UPDATE tbl_sgo_categoriaproductotamano set sgo_int_categoriaproducto=".$valor[1].", sgo_vch_tamano='".$valor[2]."', sgo_int_orden=".$valor[3]." where sgo_int_tamano = ".$valor[0];
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $trans->close();return $valor[0];
                 }
		 }
		 catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
	  }
      function Confirma_Eliminar_CategoriaProductoTamano($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_tamano FROM tbl_sgo_categoriaproductotamano WHERE sgo_int_tamano=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar el tamaño "' . $row["sgo_vch_tamano"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_CategoriaProductoTamano','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_CategoriaProductoTamano($prm)
      {		  
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("delete from tbl_sgo_categoriaproductotamano  WHERE sgo_int_tamano=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProductoTamano','tbl_lista','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }  	
/***********************************************************TRANSPORTISTA*****************************************************************/
    function Filtros_Listar_Transportista()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Nro. Documento" => $Helper->textbox("fil_txtNombre",++$index,"",11,150,$TipoTxt->texto,"","","","textoInput"),
            "Razon Social" => $Helper->textbox("fil_txtRazonSocial",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")
         );
         $buttons=array($Helper->button("btnBuscarTransportista","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_Transportista','tbl_listatransportista','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listatransportista",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
    function Grilla_Listar_Transportista($prm)
      {
         $valor = explode('|',$prm);
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_transportista as param, sgo_vch_nrodocumentoidentidad as 'NRO. DOCUMENTO', sgo_vch_razonsocial as 'RAZON SOCIAL',
                sgo_vch_direccion as 'DIRECCION'
                from tbl_sgo_transportista where 1=1";
         if($valor[0]!="")
         {
            $sql.=" and sgo_vch_nrodocumentoidentidad like '%".$valor[0]."%'";
         }
         if($valor[1]!="")
         {
            $sql.=" and sgo_vch_razonsocial like '%".$valor[1]."%'";
         }
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('configuracion','PopupTransportista','','","PopUp('configuracion','PopupTransportista','','","","PopUp('configuracion','ConfirmaEliminarTransportista','','",null,$btn_extra,array(),array(),20,"");
      }
      function PopUp_Transportista($prm)
      {
         $Helper=new htmlhelper;
         $Obj=new mysqlhelper();
         $TipoTxt=new TipoTextBox;
         $index=0;
         $valor = explode('|', $prm);
         $documento="";$razonsocial="";$direccion="";$dpto="-1";$prov="-1";$dist="-1";
         $sql = "   select  sgo_int_transportista as param, sgo_vch_nrodocumentoidentidad, sgo_vch_razonsocial, sgo_vch_direccion,
                            tbl_sgo_transportista.sgo_int_ubigeo as idubidist,ubiprov.sgo_int_ubigeo as idubiprov,ubidpto.sgo_int_ubigeo as idubidpto
                    from    tbl_sgo_transportista
                    LEFT    JOIN tbl_sgo_ubigeo ubidist on ubidist.sgo_int_ubigeo=tbl_sgo_transportista.sgo_int_ubigeo
                    LEFT    JOIN tbl_sgo_ubigeo ubiprov on ubiprov.sgo_vch_prov=ubidist.sgo_vch_prov and ubiprov.sgo_vch_dpto=ubidist.sgo_vch_dpto and ubiprov.sgo_vch_dist='00'
                    LEFT    JOIN tbl_sgo_ubigeo ubidpto on ubidpto.sgo_vch_dpto=ubidist.sgo_vch_dpto and ubidpto.sgo_vch_prov='00' and ubidpto.sgo_vch_dist='00'
                    where   sgo_int_transportista=".$valor[0];
         if(isset($valor[1]))
         {
           if($valor[1]!="")
           {
            $sql .= " and sgo_vch_nrodocumentoidentidad = '".$valor[0]."'";
           }
         }
         if(isset($valor[2]))
         {
           if($valor[2]!="")
           {
            $sql .= " and sgo_vch_razonsocial = '".$valor[1]."'";
           }
         }
         $result = $Obj->consulta($sql);

         while ($row = mysqli_fetch_array($result))
         {
            $documento=$row["sgo_vch_nrodocumentoidentidad"];
            $razonsocial=$row["sgo_vch_razonsocial"];
            $direccion=$row["sgo_vch_direccion"];
            $dpto=$row["idubidpto"];
            $prov=$row["idubiprov"];
            $dist=$row["idubidist"];
            break;
         }

         $Val_NRODOCIDENTIDAD=new InputValidacion();
         $Val_NRODOCIDENTIDAD->InputValidacion('DocValue("fil_txtNroDocumentoIdentidad")!=""','Debe especificar el nro. de documento de identidad');

         $Val_RAZONSOCIAL=new InputValidacion();
         $Val_RAZONSOCIAL->InputValidacion('DocValue("fil_txtRazonSocialTransportista")!=""','Debe especificar la razÃ³n social del transportista');

         $Val_DIRECCION=new InputValidacion();
         $Val_DIRECCION->InputValidacion('DocValue("fil_txtDireccionTransportista")!=""','Debe especificar la direcciÃ³n del transportista');

         $buttons=array();
         $html = '<fieldset class="textoInput"><legend align= "left">Transportista</legend>';
         $html .= "<table border='0' id='tbl_mant_transportista' cellspacing='0' cellpadding='0' width='500px' class='textoInput'>";//;$Helper->Crear_Layer("tbl_mant_transportista",$inputs,$buttons,3,1000,"","");
         $html .= "<tr><td>Nro. Documento</td><td width='10px'></td><td>".$Helper->textbox("fil_txtNroDocumentoIdentidad",++$index,$documento,11,100,$TipoTxt->numerico,"","","","",$Val_NRODOCIDENTIDAD)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>RazÃ³n Social</td><td width='10px'></td><td>".$Helper->textbox("fil_txtRazonSocialTransportista",++$index,$razonsocial,128,250,$TipoTxt->texto,"","","","",$Val_RAZONSOCIAL)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Departamento</td><td width='10px'></td><td>".$Helper->combo_departamento("fil_cmbdepartamentoDatoCliente",++$index,$dpto,"Cargar_Combo('general','Combo_Provincias','fil_cmbprovinciaDatoCliente','fil_cmbdepartamentoDatoCliente','fil_cmbprovinciaDatoCliente');")."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Provincia</td><td width='10px'></td><td>".$Helper->combo_provincia_x_departamento("fil_cmbprovinciaDatoCliente",++$index,$dpto,$prov,"Cargar_Combo('general','Combo_Distritos','fil_cmbdistritoDatoCliente','fil_cmbprovinciaDatoCliente','fil_cmbdistritoDatoCliente')")."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Disitrito</td><td width='10px'></td><td>".$Helper->combo_distrito_x_provincia("fil_cmbdistritoDatoCliente",++$index,$prov,$dist,"",$Val_Ubigeo)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>DirecciÃ³n</td><td width='10px'></td><td>".$Helper->textbox("fil_txtDireccionTransportista",++$index,$direccion,128,250,$TipoTxt->texto,"","","","",$Val_DIRECCION)."</td></tr>";
         $html .= "<tr><td colspan='3' height='10px'></td></tr>";
         $html .= "</table>";
         $html .= '</fieldset>';

         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Transportista",500,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_Transportista','tbl_mant_transportista','" . $prm . "')","textoInput"));
      }
      function Mant_Transportista($prm)
	  {
		 $Obj=new mysqlhelper;
		 $valor=explode('|',$prm);

         try{
                 if($valor[0]=="0")
                 {
    				 $trans=$Obj->transaction();
    		 		 $sql = "INSERT INTO tbl_sgo_transportista (sgo_vch_nrodocumentoidentidad, sgo_vch_razonsocial, sgo_int_documentoidentidad, sgo_vch_direccion, sgo_int_ubigeo) values (".$valor[1].",'".$valor[2]."',1,'".$valor[6]."',".$valor[5].")";
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $idCh=mysqli_insert_id($trans);
                     $trans->close();return $idCh;
                 }
                 else
                 {
                     $trans=$Obj->transaction();
    		 		 $sql = "UPDATE tbl_sgo_transportista set sgo_vch_nrodocumentoidentidad='".$valor[1]."', sgo_vch_razonsocial='".$valor[2]."', sgo_vch_direccion='".$valor[6]."', sgo_int_ubigeo=".$valor[5]." where sgo_int_transportista = ".$valor[0];
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $trans->close();return $valor[0];
                 }
		 }
		 catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
	  }
      function Confirma_Eliminar_Transportista($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_razonsocial FROM tbl_sgo_transportista WHERE sgo_int_transportista=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar al transportista "' . $row["sgo_vch_razonsocial"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_Transportista','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_Transportista($prm)
      {
		   print($prm);
		  end();
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("delete from tbl_sgo_transportista  WHERE sgo_int_transportista=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_Transportista','tbl_listatransportista','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
/***********************************************************CHOFER*****************************************************************/
    function Filtros_Listar_Chofer()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Transportista" => $Helper->combo_transportista("fil_cmbTransportista",++$index,"","","","",""),
            "Nro. Licencia" => $Helper->textbox("fil_txtNroDocumentoChofer",++$index,"",11,150,$TipoTxt->texto,"","","","textoInput"),
            "Nombre" => $Helper->textbox("fil_txtNombreChofer",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")
         );
         $buttons=array($Helper->button("btnBuscarChofer","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_Chofer','tbl_listachofer','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listachofer",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
    function Grilla_Listar_Chofer($prm)
      {
         $valor = explode('|',$prm);
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_chofer as param, sgo_vch_licencia as 'LICENCIA', sgo_vch_chofer as 'NOMBRE DEL CHOFER'
                from tbl_sgo_chofer where 1=1";
         if($valor[0]!="")
         {
            $sql.=" and sgo_int_transportista =".$valor[0];
         }
         if($valor[1]!="")
         {
            $sql.=" and sgo_vch_licencia like '%".$valor[1]."%'";
         }
         if($valor[2]!="")
         {
            $sql.=" and sgo_vch_chofer like '%".$valor[2]."%'";
         }
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('configuracion','PopupChofer','','","PopUp('configuracion','PopupChofer','','","","PopUp('configuracion','ConfirmaEliminarChofer','','",null,$btn_extra,array(),array(),20,"");
      }
      function PopUp_Chofer($prm)
      {
         $Helper=new htmlhelper;
         $Obj=new mysqlhelper();
         $TipoTxt=new TipoTextBox;
         $index=0;
         $valor = explode('|', $prm);
         $licencia="";$chofer="";$transportista="";
         $sql = "   select  sgo_int_chofer as param, sgo_int_transportista, sgo_vch_chofer, sgo_vch_licencia
                    from    tbl_sgo_chofer
                    where   sgo_int_chofer=".$valor[0];
         $result = $Obj->consulta($sql);

         while ($row = mysqli_fetch_array($result))
         {
            $licencia=$row["sgo_vch_licencia"];
            $chofer=$row["sgo_vch_chofer"];
            $transportista=$row["sgo_int_transportista"];
            break;
         }

         $Val_LICENCIA=new InputValidacion();
         $Val_LICENCIA->InputValidacion('DocValue("fil_txtNroLicenciaChoferPU")!=""','Debe especificar la licencia del chofer');

         $Val_CHOFER=new InputValidacion();
         $Val_CHOFER->InputValidacion('DocValue("fil_txtNombreChoferPU")!=""','Debe especificar el nombre del chofer');

         $Val_TRANSPORTISTA=new InputValidacion();
         $Val_TRANSPORTISTA->InputValidacion('DocValue("fil_cboTransportistaPU")!="-1"','Debe indicar el transportista');

         $buttons=array();
         $html = '<fieldset class="textoInput"><legend align= "left">Chofer</legend>';
         $html .= "<table border='0' id='tbl_mant_chofer' cellspacing='0' cellpadding='0' width='500px' class='textoInput'>";//;$Helper->Crear_Layer("tbl_mant_transportista",$inputs,$buttons,3,1000,"","");
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Transportista</td><td width='10px'></td><td>".$Helper->combo_transportista("fil_cmbTransportista",++$index,"",$transportista,"",$Val_TRANSPORTISTA,"")."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Licencia</td><td width='10px'></td><td>".$Helper->textbox("fil_txtNroLicenciaChoferPU",++$index,$licencia,11,100,"","","","","",$Val_LICENCIA)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Nombre</td><td width='10px'></td><td>".$Helper->textbox("fil_txtNombreChoferPU",++$index,$chofer,128,250,$TipoTxt->texto,"","","","",$Val_CHOFER)."</td></tr>";
         $html .= "<tr><td colspan='3' height='10px'></td></tr>";
         $html .= "</table>";
         $html .= '</fieldset>';

         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Chofer",500,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_Chofer','tbl_mant_chofer','" . $prm . "')","textoInput"));
      }
      function Mant_Chofer($prm)
	  {
		 $Obj=new mysqlhelper;
		 $valor=explode('|',$prm);

         try{
                 if($valor[0]=="0")
                 {
    				 $trans=$Obj->transaction();
    		 		 $sql = "INSERT INTO tbl_sgo_chofer (sgo_int_transportista, sgo_vch_chofer, sgo_vch_licencia) values (".$valor[1].",'".$valor[3]."','".$valor[2]."')";
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $idCh=mysqli_insert_id($trans);
                     $trans->close();
                     return $idCh;
                 }
                 else
                 {
                     $trans=$Obj->transaction();
    		 		 $sql = "UPDATE tbl_sgo_chofer set sgo_int_transportista=".$valor[1].", sgo_vch_chofer='".$valor[3]."', sgo_vch_licencia='".$valor[2]."' where sgo_int_chofer = ".$valor[0];
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $trans->close();return $valor[0];
                 }
		 }
		 catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
	  }
      function Confirma_Eliminar_Chofer($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_chofer FROM tbl_sgo_chofer WHERE sgo_int_chofer=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar al chofer "' . $row["sgo_vch_chofer"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_Chofer','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_Chofer($prm)
      {
		   print($prm);
		  end();
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("delete from tbl_sgo_chofer  WHERE sgo_int_chofer=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_Chofer','tbl_listachofer','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
/***********************************************************TIPO DE CAMBIO*****************************************************************/
    function Filtros_Listar_TipoCambio()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Moneda" => $Helper->combo_moneda("fil_cmbMoneda",++$index,"","","","",""),
            "Desde" => $Helper->textdate("fil_txtFechaDesde",++$index,"",false,$TipoDate->fecha,75,"",""),
            "Hasta" => $Helper->textdate("fil_txtFechaHasta",++$index,"",false,$TipoDate->fecha,75,"","")
         );
         $buttons=array($Helper->button("btnBuscarTipoCambio","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_TipoCambio','tbl_listatipocambio','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listatipocambio",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
    function Grilla_Listar_TipoCambio($prm)
      {
        $Helper=new htmlhelper;
         $valor = explode('|',$prm);
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_tipocambio as param,  tbl_sgo_moneda.sgo_vch_nombre as 'MONEDA', sgo_dec_venta as 'VENTA', sgo_dec_compra as 'COMPRA', sgo_dat_fecha as 'FECHA' from 	tbl_sgo_tipocambio inner join tbl_sgo_moneda on 	tbl_sgo_tipocambio.sgo_int_moneda = tbl_sgo_moneda.sgo_int_moneda where 1=1";
         if($valor[0]!="")
         {
            $sql.=" and tbl_sgo_tipocambio.sgo_int_moneda =".$valor[0];
         }
         if($valor[1]!="")
         {
            $sql.=" and sgo_dat_fecha >= '".$Helper->convertir_fecha_ingles($valor[1])."'";
         }
         if($valor[2]!="")
         {
            $sql.=" and sgo_dat_fecha <= '".$Helper->convertir_fecha_ingles($valor[2])."'";
         }
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('configuracion','PopupTipoCambio','','","PopUp('configuracion','PopupTipoCambio','','","","PopUp('configuracion','ConfirmaEliminarTipoCambio','','",null,$btn_extra,array(),array(),20,"");
      }
      function PopUp_TipoCambio($prm)
      {
         $Helper=new htmlhelper;
         $Obj=new mysqlhelper();
         $TipoTxt=new TipoTextBox;
         $index=0;
         $valor = explode('|', $prm);
         $moneda="";$compra="";$venta="";$fecha="";
         $sql = "   select  sgo_int_tipocambio as param, sgo_dec_compra, sgo_dec_venta, sgo_int_moneda, date_format(sgo_dat_fecha,'%d/%m/%Y') as sgo_dat_fecha
                    from    tbl_sgo_tipocambio
                    where   sgo_int_tipocambio=".$valor[0];
         $result = $Obj->consulta($sql);

         while ($row = mysqli_fetch_array($result))
         {
            $moneda=$row["sgo_int_moneda"];
            $compra=$row["sgo_dec_compra"];
            $venta=$row["sgo_dec_venta"];
            $fecha=$row["sgo_dat_fecha"];
            break;
         }

         $Val_COMPRA=new InputValidacion();
         $Val_COMPRA->InputValidacion('DocValue("fil_txtCompraPU")!=""','Debe especificar el tipo de cambio para la compra');

         $Val_VENTA=new InputValidacion();
         $Val_VENTA->InputValidacion('DocValue("fil_txtVentaPU")!=""','Debe especificar el tipo de cambio para la venta');

         $Val_MONEDA=new InputValidacion();
         $Val_MONEDA->InputValidacion('DocValue("fil_cboMonedaPU")!=""','Debe indicar la moneda');

         $Val_FECHA=new InputValidacion();
         $Val_FECHA->InputValidacion('DocValue("fil_txtFechaCambioPU")!=""','Debe especificar la fecha');

         $buttons=array();
         $html = '<fieldset class="textoInput"><legend align= "left">Tipo de cambio</legend>';
         $html .= "<table border='0' id='tbl_mant_tipocambio' cellspacing='0' cellpadding='0' width='500px' class='textoInput'>";//;$Helper->Crear_Layer("tbl_mant_transportista",$inputs,$buttons,3,1000,"","");
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Moneda</td><td width='10px'></td><td>".$Helper->combo_moneda("fil_cboMonedaPU",++$index,$moneda,"",$Val_MONEDA)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Fecha</td><td width='10px'></td><td>".$Helper->textdate("fil_txtFechaCambioPU",++$index,$fecha,false,$TipoDate->fecha,75,"","",$Val_FECHA)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Compra</td><td width='10px'></td><td>".$Helper->textbox("fil_txtCompraPU",++$index,$compra,128,250,$TipoTxt->decimal,"","","","",$Val_COMPRA)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Venta</td><td width='10px'></td><td>".$Helper->textbox("fil_txtVentaPU",++$index,$venta,128,250,$TipoTxt->decimal,"","","","",$Val_VENTA)."</td></tr>";
         $html .= "<tr><td colspan='3' height='10px'></td></tr>";
         $html .= "</table>";
         $html .= '</fieldset>';

         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Tipo de Cambio",500,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_TipoCambio','tbl_mant_tipocambio','" . $prm . "')","textoInput"));
      }
  	  function Mant_TipoCambio($prm)
	  {
	     $Helper=new htmlhelper;
		 $Obj=new mysqlhelper;
		 $valor=explode('|',$prm);

         try{
                 if($valor[0]=="0")
                 {
    				 $trans=$Obj->transaction();
    		 		 $sql = "INSERT INTO tbl_sgo_tipocambio (sgo_int_moneda, sgo_dat_fecha, sgo_dec_compra, sgo_dec_venta) values (".$valor[1].",'".$Helper->convertir_fecha_ingles($valor[2])."',".$valor[3].",".$valor[4].")";
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $idCh=mysqli_insert_id($trans);
                     $trans->close();
                     return $idCh;
                 }
                 else
                 {
                     $trans=$Obj->transaction();
    		 		 $sql = "UPDATE tbl_sgo_tipocambio set sgo_int_moneda=".$valor[1].", sgo_dat_fecha='".$Helper->convertir_fecha_ingles($valor[2])."', sgo_dec_compra=".$valor[3].", sgo_dec_venta=".$valor[4]." where sgo_int_tipocambio = ".$valor[0];
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $trans->close();return $valor[0];
                 }
		 }
		 catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
	  }
  	  function Confirma_Eliminar_TipoCambio($prm)
      {
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('¿Esta seguro de eliminar el tipo de cambio seleccionado?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_TipoCambio','','" . $prm . "')"));
      }
      function Eliminar_TipoCambio($prm)
      {
		   print($prm);
		  end();
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("delete from tbl_sgo_tipocambio  WHERE sgo_int_tipocambio=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_TipoCambio','tbl_listatipocambio','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
      /*
  	  function Obtener_Tipo_Cambio($fecha)
      {      	
      	 include_once("../config/app.inc");      	
      	 $Helper=new htmlhelper;
         $Obj=new mysqlhelper;
         $tipoCambio[];
         $sql ="select sgo_dec_venta , sgo_dec_compra from tbl_sgo_tipocambio where sgo_int_moneda = ".MONEDA_DOLARES." and sgo_dat_fecha = " .$Helper->convertir_fecha_ingles($fecha);         
         $result = $Obj->consulta($sql);
      	 while ($row = mysqli_fetch_array($result))
         {
            $tipoCambio[0]=$row["sgo_dec_venta"];
            $tipoCambio[1]=$row["sgo_dec_compra"];
         }
         return $tipoCambio;
      }           
      */
/***********************************************************VEHICULO*****************************************************************/
    function Filtros_Listar_Vehiculo()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Transportista" => $Helper->combo_transportista("fil_cmbTransportista",++$index,"","","","",""),
            "Marca" => $Helper->textbox("fil_txtMarca",++$index,"",11,150,$TipoTxt->texto,"","","","textoInput"),
            "Placa" => $Helper->textbox("fil_txtPlaca",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")
         );
         $buttons=array($Helper->button("btnBuscarVehiculo","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_Vehiculo','tbl_listavehiculo','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listavehiculo",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
    function Grilla_Listar_Vehiculo($prm)
      {
         $valor = explode('|',$prm);
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_vehiculo as param, sgo_vch_marcamodelo as 'MARCA-MODELO', sgo_vch_placa as 'PLACA'
                from tbl_sgo_vehiculo where 1=1";
         if($valor[0]!="")
         {
            $sql.=" and sgo_int_transportista =".$valor[0];
         }
         if($valor[1]!="")
         {
            $sql.=" and sgo_vch_marcamodelo like '%".$valor[1]."%'";
         }
         if($valor[2]!="")
         {
            $sql.=" and sgo_vch_placa like '%".$valor[2]."%'";
         }
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('configuracion','PopupVehiculo','','","PopUp('configuracion','PopupVehiculo','','","","PopUp('configuracion','ConfirmaEliminarVehiculo','','",null,$btn_extra,array(),array(),20,"");
      }
      function PopUp_Vehiculo($prm)
      {
         $Helper=new htmlhelper;
         $Obj=new mysqlhelper();
         $TipoTxt=new TipoTextBox;
         $index=0;
         $valor = explode('|', $prm);
         $marca="";$placa="";$transportista="";$certificado="";
         $sql = "   select  sgo_int_vehiculo as param, sgo_int_transportista, sgo_vch_marcamodelo, sgo_vch_placa, sgo_vch_certificado
                    from    tbl_sgo_vehiculo
                    where   sgo_int_vehiculo=".$valor[0];
         $result = $Obj->consulta($sql);

         while ($row = mysqli_fetch_array($result))
         {
            $marca=$row["sgo_vch_marcamodelo"];
            $placa=$row["sgo_vch_placa"];
            $certificado=$row["sgo_vch_certificado"];
            $transportista=$row["sgo_int_transportista"];
            break;
         }

         $Val_MARCA=new InputValidacion();
         $Val_MARCA->InputValidacion('DocValue("fil_txtMarcaPU")!=""','Debe especificar la marca del veh&iacute;culo');

         $Val_PLACA=new InputValidacion();
         $Val_PLACA->InputValidacion('DocValue("fil_txtPlacaPU")!=""','Debe especificar la placa del veh&iacute;culo');

         $Val_CERTIFICADO=new InputValidacion();
         $Val_CERTIFICADO->InputValidacion('DocValue("fil_txtCertificadoPU")!=""','Debe especificar el certificado del veh&iacute;culo');

         $Val_TRANSPORTISTA=new InputValidacion();
         $Val_TRANSPORTISTA->InputValidacion('DocValue("fil_cboTransportistaPU")!="-1"','Debe indicar el transportista');

         $buttons=array();
         $html = '<fieldset class="textoInput"><legend align= "left">Chofer</legend>';
         $html .= "<table border='0' id='tbl_mant_vehiculo' cellspacing='0' cellpadding='0' width='500px' class='textoInput'>";//;$Helper->Crear_Layer("tbl_mant_transportista",$inputs,$buttons,3,1000,"","");
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Transportista</td><td width='10px'></td><td>".$Helper->combo_transportista("fil_cmbTransportista",++$index,"",$transportista,"",$Val_TRANSPORTISTA,"")."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Marca</td><td width='10px'></td><td>".$Helper->textbox("fil_txtMarcaPU",++$index,$marca,128,250,"","","","","",$Val_MARCA)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Placa</td><td width='10px'></td><td>".$Helper->textbox("fil_txtPlacaPU",++$index,$placa,7,100,$TipoTxt->texto,"","","","",$Val_PLACA)."</td></tr>";
         $html .= "<tr><td colspan='3' height='5px'></td></tr>";
         $html .= "<tr><td>Certificado</td><td width='10px'></td><td>".$Helper->textbox("fil_txtCertificadoPU",++$index,$certificado,128,250,$TipoTxt->texto,"","","","","")."</td></tr>";
         $html .= "<tr><td colspan='3' height='10px'></td></tr>";
         $html .= "</table>";
         $html .= '</fieldset>';

         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " VehÃ­culo",500,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_Vehiculo','tbl_mant_vehiculo','" . $prm . "')","textoInput"));
      }
      function Mant_Vehiculo($prm)
	  {
		 $Obj=new mysqlhelper;
		 $valor=explode('|',$prm);

         try{
                 if($valor[0]=="0")
                 {
    				 $trans=$Obj->transaction();
    		 		 $sql = "INSERT INTO tbl_sgo_vehiculo (sgo_int_transportista, sgo_vch_marcamodelo, sgo_vch_placa, sgo_vch_certificado) values (".$valor[1].",'".$valor[2]."','".$valor[3]."','".$valor[4]."')";
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $idCh=mysqli_insert_id($trans);
                     $trans->close();
                     return $idCh;
                 }
                 else
                 {
                     $trans=$Obj->transaction();
    		 		 $sql = "UPDATE tbl_sgo_vehiculo set sgo_int_transportista=".$valor[1].", sgo_vch_marcamodelo='".$valor[2]."', sgo_vch_placa='".$valor[3]."', sgo_vch_certificado='".$valor[4]."' where sgo_int_vehiculo = ".$valor[0];
    				 if(!$trans->query($sql)) throw new Exception($sql . " => " . $trans->error);
        			 $trans->commit();
                     $trans->close();return $valor[0];
                 }
		 }
		 catch(Exception $e)
         {
            echo "<script>alert('Error: " . $e . "');</script>";
            $trans->rollback();$trans->close();return -1;
         }
	  }
      function Confirma_Eliminar_Vehiculo($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_placa FROM tbl_sgo_vehiculo WHERE sgo_int_vehiculo=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar el veh&iacute;culo de placa "' . $row["sgo_vch_placa"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_Vehiculo','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_Vehiculo($prm)
      {
		   print($prm);
		  end();
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("delete from tbl_sgo_vehiculo  WHERE sgo_int_vehiculo=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_Vehiculo','tbl_listavehiculo','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
 /***********************************************************ESTADO COTIZACION*****************************************************************/
	  function Filtros_Listar_EstadosCotizacion()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Nombre" => $Helper->textbox("fil_txtNombre",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")            
         );
         $buttons=array($Helper->button("btnBuscarEstadoCotizacion","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_EstadoCotizacion','tbl_listarestadoscotizacion','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarestadoscotizacion",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
	  function Grilla_Listar_EstadoCotizacion($prm)
      {
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_estadocotizacion as param, sgo_vch_descripcion as 'NOMBRE DEL ESTADO' from tbl_sgo_estadocotizacion ";
         $where = "WHERE sgo_vch_descripcion like '%@p1%' and sgo_bit_activo = '1'";
         $where = $Obj->sql_where($where,$prm); 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('configuracion','Nuevo','','","PopUp('configuracion','Detalles','','","","PopUp('configuracion','Confirma_Eliminar','','");
      }
	  function Nuevo_EstadoCotizacion($prm)
      {
          $html=$this->Nuevo_Estado_Cotizacion($prm);$Helper=new htmlhelper;
          return $Helper->PopUp("","Nuevo Estado CotizaciÃ³n",450,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_EstadoCotizacion','tbl_estadocotizacion_pu','" . $prm . "')","textoInput"));
      }
      function Detalles_EstadoCotizacion($prm)
      {
          return $this->Nuevo_EstadoCotizacion($prm);
      }
      function Confirma_Eliminar_EstadoCotizacion($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_descripcion FROM tbl_sgo_estadocotizacion WHERE sgo_int_estadocotizacion=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar el estado "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_EstadoCotizacion','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
	  function Nuevo_Estado_Cotizacion($prm)
      {
         $Obj=new mysqlhelper();
         $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $descripcion="";
         $result = $Obj->consulta("select sgo_vch_descripcion from tbl_sgo_estadocotizacion WHERE sgo_int_estadocotizacion = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $descripcion=$row["sgo_vch_descripcion"];$estado=$row["sgo_bit_activo"];
            break;
         }
         $Val_DESCRIPCION=new InputValidacion();
         $Val_DESCRIPCION->InputValidacion('DocValue("fil_txtNombreEstadoCotizacion")!=""','Debe especificar el nombre del estado');
         $html='<table id="tbl_estadocotizacion_pu" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">DescripciÃ³n</td><td>' . $Helper->textbox("fil_txtNombreEstadoCotizacion",++$index,$descripcion,128,200,$TipoTxt->texto,"","","","textoInput",$Val_DESCRIPCION) . '</td></tr>';
			$html .='</table>';
         return $html;
      }
	  function Eliminar_Estado_Cotizacion($prm)
      {
		   print($prm);
		  end();
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("update tbl_sgo_estadocotizacion set sgo_bit_activo = 0 WHERE sgo_int_estadocotizacion=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoCotizacion','tbl_listarestadoscotizacion','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
	  function Actualizar_EstadoCotizacion($prm)
      {
          $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0) $resp= $Obj->execute("UPDATE tbl_sgo_estadocotizacion
                            SET sgo_vch_descripcion='" . $valor[1] . "' WHERE sgo_int_estadocotizacion=" . $valor[0]);
          else $resp= $Obj->execute("INSERT INTO tbl_sgo_estadocotizacion (sgo_vch_descripcion, sgo_bit_activo) VALUES ('" . $valor[1] . "',1)");          
          if($resp!=-1)		  
			  return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoCotizacion','tbl_listarestadoscotizacion','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
			 
      }
 /***********************************************************ESTADO FACTURA*****************************************************************/
  	  function Filtros_Listar_EstadosFactura()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Nombre" => $Helper->textbox("fil_txtNombreEstadoFactura",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")            
         );
         $buttons=array($Helper->button("btnBuscarEstadoFactura","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_EstadoFactura','tbl_listaresultados','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listaresultados",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
	  function Grilla_Listar_EstadoFactura($prm)
      {
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_estadofactura as param, sgo_vch_descripcion as 'NOMBRE DEL ESTADO' from tbl_sgo_estadofactura ";
         $where = "WHERE sgo_vch_descripcion like '%@p1%' and sgo_bit_activo = '1'";
         $where = $Obj->sql_where($where,$prm); 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('configuracion','Nuevo_Mant_EstadoFactura','','","PopUp('configuracion','Detalles_Mant_EstadoFactura','','","","PopUp('configuracion','Confirma_Eliminar_Mant_EstadoFactura','','");
      }
	  function Nuevo_EstadoFactura($prm)
      {
          $html=$this->Nuevo_Estado_Factura($prm);$Helper=new htmlhelper;
          return $Helper->PopUp("","Nuevo Estado Factura",450,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_EstadoFactura','tbl_estadofactura_pu','" . $prm . "')","textoInput"));
      }
      function Detalles_EstadoFactura($prm)
      {
          return $this->Nuevo_EstadoFactura($prm);
      }
      function Confirma_Eliminar_EstadoFactura($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_descripcion FROM tbl_sgo_estadofactura WHERE sgo_int_estadofactura=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar el estado "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_EstadoFactura','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
	  function Nuevo_Estado_Factura($prm)
      {
         $Obj=new mysqlhelper; $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $descripcion="";
         $result = $Obj->consulta("select sgo_vch_descripcion from tbl_sgo_estadofactura WHERE sgo_int_estadofactura = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $descripcion=$row["sgo_vch_descripcion"];$estado=$row["sgo_bit_activo"];
            break;
         }
         $Val_DESCRIPCION=new InputValidacion();
         $Val_DESCRIPCION->InputValidacion('DocValue("fil_txtNombreEstadoFacturaNuevo")!=""','Debe especificar el nombre del estado');
         $html='<table id="tbl_estadofactura_pu" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">DescripciÃ³n</td><td>' . $Helper->textbox("fil_txtNombreEstadoFacturaNuevo",++$index,$descripcion,128,200,$TipoTxt->texto,"","","","textoInput",$Val_DESCRIPCION) . '</td></tr>';
			$html .='</table>';
         return $html;
      }
	   function Eliminar_Estado_Factura($prm)
      {		  
          $Obj=new mysqlhelper;$valor=explode('|',$prm);		 
          if($Obj->execute("update tbl_sgo_estadofactura set sgo_bit_activo = 0 WHERE sgo_int_estadofactura=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoFactura','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
	  function Actualizar_EstadoFactura($prm)
      {
          $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0) $resp= $Obj->execute("UPDATE tbl_sgo_estadofactura
                            SET sgo_vch_descripcion='" . $valor[1] . "' WHERE sgo_int_estadofactura=" . $valor[0]);
          else $resp= $Obj->execute("INSERT INTO tbl_sgo_estadofactura (sgo_vch_descripcion, sgo_bit_activo) VALUES ('" . $valor[1] . "',1)");          
          if($resp!=-1)		  
			  return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoFactura','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
			 
      }
/***********************************************************ESTADO GUIA REMISION*****************************************************************/
  	  function Filtros_Listar_EstadosGuiaRemision()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Nombre" => $Helper->textbox("fil_txtNombreEstadoGuiaRemision",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")            
         );
         $buttons=array($Helper->button("btnBuscarEstadoGuiaRemision","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_EstadoGuiaRemision','tbl_listaresultados','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listaresultados",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
	  function Grilla_Listar_EstadoGuiaRemision($prm)
      {
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_estadoguiaremision as param, sgo_vch_descripcion as 'NOMBRE DEL ESTADO' from tbl_sgo_estadoguiaremision ";
         $where = "WHERE sgo_vch_descripcion like '%@p1%' and sgo_bit_activo = '1'";
         $where = $Obj->sql_where($where,$prm); 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('configuracion','Nuevo_Mant_EstadoGuiaRemision','','","PopUp('configuracion','Detalles_Mant_EstadoGuiaRemision','','","","PopUp('configuracion','Confirma_Eliminar_Mant_EstadoGuiaRemision','','");
      }
	  function Nuevo_EstadoGuiaRemision($prm)
      {
          $html=$this->Nuevo_Estado_GuiaRemision($prm);$Helper=new htmlhelper;
          return $Helper->PopUp("","Nuevo Estado Guia de Remision",450,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_EstadoGuiaRemision','tbl_estadofactura_pu','" . $prm . "')","textoInput"));
      }
      function Detalles_EstadoGuiaRemision($prm)
      {
          return $this->Nuevo_EstadoGuiaRemision($prm);
      }
      function Confirma_Eliminar_EstadoGuiaRemision($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_descripcion FROM tbl_sgo_estadoguiaremision WHERE sgo_int_estadoguiaremision=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar el estado "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_EstadoGuiaRemision','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
	  function Nuevo_Estado_GuiaRemision($prm)
      {
         $Obj=new mysqlhelper; $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $descripcion="";
         $result = $Obj->consulta("select sgo_vch_descripcion from tbl_sgo_estadoguiaremision WHERE sgo_int_estadoguiaremision = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $descripcion=$row["sgo_vch_descripcion"];$estado=$row["sgo_bit_activo"];
            break;
         }
         $Val_DESCRIPCION=new InputValidacion();
         $Val_DESCRIPCION->InputValidacion('DocValue("fil_txtNombreEstadoGuiaRemisionNuevo")!=""','Debe especificar el nombre del estado');
         $html='<table id="tbl_estadofactura_pu" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">DescripciÃ³n</td><td>' . $Helper->textbox("fil_txtNombreEstadoGuiaRemisionNuevo",++$index,$descripcion,128,200,$TipoTxt->texto,"","","","textoInput",$Val_DESCRIPCION) . '</td></tr>';
			$html .='</table>';
         return $html;
      }
	   function Eliminar_Estado_GuiaRemision($prm)
      {		  
          $Obj=new mysqlhelper;$valor=explode('|',$prm);		 
          if($Obj->execute("update tbl_sgo_estadoguiaremision set sgo_bit_activo = 0 WHERE sgo_int_estadoguiaremision=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoGuiaRemision','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
	  function Actualizar_EstadoGuiaRemision($prm)
      {
          $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0) $resp= $Obj->execute("UPDATE tbl_sgo_estadoguiaremision
                            SET sgo_vch_descripcion='" . $valor[1] . "' WHERE sgo_int_estadoguiaremision=" . $valor[0]);
          else $resp= $Obj->execute("INSERT INTO tbl_sgo_estadoguiaremision (sgo_vch_descripcion, sgo_bit_activo) VALUES ('" . $valor[1] . "',1)");          
          if($resp!=-1)		  
			  return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoGuiaRemision','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
			 
      }
/***********************************************************ESTADO ORDEN DE COMPRA*****************************************************************/
  	  function Filtros_Listar_EstadosOrdenCompra()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Nombre" => $Helper->textbox("fil_txtNombreEstadoOrdenCompra",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")            
         );
         $buttons=array($Helper->button("btnBuscarEstadoOrdenCompra","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_EstadoOrdenCompra','tbl_listaresultados','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listaresultados",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
	  function Grilla_Listar_EstadoOrdenCompra($prm)
      {
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_estadooc as param, sgo_vch_descripcion as 'NOMBRE DEL ESTADO' from tbl_sgo_estadoordencompra ";
         $where = "WHERE sgo_vch_descripcion like '%@p1%' and sgo_bit_activo = '1'";
         $where = $Obj->sql_where($where,$prm); 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('configuracion','Nuevo_Mant_EstadoOrdenCompra','','","PopUp('configuracion','Detalles_Mant_EstadoOrdenCompra','','","","PopUp('configuracion','Confirma_Eliminar_Mant_EstadoOrdenCompra','','");
      }
	  function Nuevo_EstadoOrdenCompra($prm)
      {
          $html=$this->Nuevo_Estado_OrdenCompra($prm);$Helper=new htmlhelper;
          return $Helper->PopUp("","Nuevo Estado Orden de Compra",450,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_EstadoOrdenCompra','tbl_estadofactura_pu','" . $prm . "')","textoInput"));
      }
      function Detalles_EstadoOrdenCompra($prm)
      {
          return $this->Nuevo_EstadoOrdenCompra($prm);
      }
      function Confirma_Eliminar_EstadoOrdenCompra($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_descripcion FROM tbl_sgo_estadoordencompra WHERE sgo_int_estadooc=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar el estado "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_EstadoOrdenCompra','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
	  function Nuevo_Estado_OrdenCompra($prm)
      {
         $Obj=new mysqlhelper; $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $descripcion="";
         $result = $Obj->consulta("select sgo_vch_descripcion from tbl_sgo_estadoordencompra WHERE sgo_int_estadooc = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $descripcion=$row["sgo_vch_descripcion"];$estado=$row["sgo_bit_activo"];
            break;
         }
         $Val_DESCRIPCION=new InputValidacion();
         $Val_DESCRIPCION->InputValidacion('DocValue("fil_txtNombreEstadoOrdenCompraNuevo")!=""','Debe especificar el nombre del estado');
         $html='<table id="tbl_estadofactura_pu" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">DescripciÃ³n</td><td>' . $Helper->textbox("fil_txtNombreEstadoOrdenCompraNuevo",++$index,$descripcion,128,200,$TipoTxt->texto,"","","","textoInput",$Val_DESCRIPCION) . '</td></tr>';
			$html .='</table>';
         return $html;
      }
	   function Eliminar_Estado_OrdenCompra($prm)
      {		  
          $Obj=new mysqlhelper;$valor=explode('|',$prm);		 
          if($Obj->execute("update tbl_sgo_estadoordencompra set sgo_bit_activo = 0 WHERE sgo_int_estadooc=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoOrdenCompra','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
	  function Actualizar_EstadoOrdenCompra($prm)
      {
          $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0) $resp= $Obj->execute("UPDATE tbl_sgo_estadoordencompra
                            SET sgo_vch_descripcion='" . $valor[1] . "' WHERE sgo_int_estadooc=" . $valor[0]);
          else $resp= $Obj->execute("INSERT INTO tbl_sgo_estadoordencompra (sgo_vch_descripcion, sgo_bit_activo) VALUES ('" . $valor[1] . "',1)");          
          if($resp!=-1)		  
			  return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoOrdenCompra','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
			 
      }
/***********************************************************ESTADO VISITA*****************************************************************/
  	  function Filtros_Listar_EstadosVisita()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Nombre" => $Helper->textbox("fil_txtNombreEstadoVisita",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")            
         );
         $buttons=array($Helper->button("btnBuscarEstadoVisita","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_EstadoVisita','tbl_listaresultados','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listaresultados",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
	  function Grilla_Listar_EstadoVisita($prm)
      {
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_estadovisita as param, sgo_vch_descripcion as 'NOMBRE DEL ESTADO' from tbl_sgo_estadovisita ";
         $where = "WHERE sgo_vch_descripcion like '%@p1%' and sgo_bit_activo = '1'";
         $where = $Obj->sql_where($where,$prm); 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('configuracion','Nuevo_Mant_EstadoVisita','','","PopUp('configuracion','Detalles_Mant_EstadoVisita','','","","PopUp('configuracion','Confirma_Eliminar_Mant_EstadoVisita','','");
      }
	  function Nuevo_EstadoVisita($prm)
      {
          $html=$this->Nuevo_Estado_Visita($prm);$Helper=new htmlhelper;
          return $Helper->PopUp("","Nuevo Estado Orden de Compra",450,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_EstadoVisita','tbl_estadofactura_pu','" . $prm . "')","textoInput"));
      }
      function Detalles_EstadoVisita($prm)
      {
          return $this->Nuevo_EstadoVisita($prm);
      }
      function Confirma_Eliminar_EstadoVisita($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_descripcion FROM tbl_sgo_estadovisita WHERE sgo_int_estadovisita=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar el estado "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_EstadoVisita','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
	  function Nuevo_Estado_Visita($prm)
      {
         $Obj=new mysqlhelper; $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $descripcion="";
         $result = $Obj->consulta("select sgo_vch_descripcion from tbl_sgo_estadovisita WHERE sgo_int_estadovisita = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $descripcion=$row["sgo_vch_descripcion"];$estado=$row["sgo_bit_activo"];
            break;
         }
         $Val_DESCRIPCION=new InputValidacion();
         $Val_DESCRIPCION->InputValidacion('DocValue("fil_txtNombreEstadoVisitaNuevo")!=""','Debe especificar el nombre del estado');
         $html='<table id="tbl_estadofactura_pu" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">DescripciÃ³n</td><td>' . $Helper->textbox("fil_txtNombreEstadoVisitaNuevo",++$index,$descripcion,128,200,$TipoTxt->texto,"","","","textoInput",$Val_DESCRIPCION) . '</td></tr>';
			$html .='</table>';
         return $html;
      }
	   function Eliminar_Estado_Visita($prm)
      {		  
          $Obj=new mysqlhelper;$valor=explode('|',$prm);		 
          if($Obj->execute("update tbl_sgo_estadovisita set sgo_bit_activo = 0 WHERE sgo_int_estadovisita=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoVisita','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
	  function Actualizar_EstadoVisita($prm)
      {
          $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0) $resp= $Obj->execute("UPDATE tbl_sgo_estadovisita
                            SET sgo_vch_descripcion='" . $valor[1] . "' WHERE sgo_int_estadovisita=" . $valor[0]);
          else $resp= $Obj->execute("INSERT INTO tbl_sgo_estadovisita (sgo_vch_descripcion, sgo_bit_activo) VALUES ('" . $valor[1] . "',1)");          
          if($resp!=-1)		  
			  return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoVisita','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
			 
      }
/***********************************************************ESTADO ORDEN DE PRODUCCION*****************************************************************/
  	  function Filtros_Listar_EstadosOrdenProduccion()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Nombre" => $Helper->textbox("fil_txtNombreEstadoOrdenProduccion",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")            
         );
         $buttons=array($Helper->button("btnBuscarEstadoOrdenProduccion","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_EstadoOrdenProduccion','tbl_listaresultados','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listaresultados",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
	  function Grilla_Listar_EstadoOrdenProduccion($prm)
      {
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_estadoOrdenProduccion as param, sgo_vch_descripcion as 'NOMBRE DEL ESTADO' from tbl_sgo_estadoOrdenProduccion ";
         $where = "WHERE sgo_vch_descripcion like '%@p1%' and sgo_bit_activo = '1'";
         $where = $Obj->sql_where($where,$prm); 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('configuracion','Nuevo_Mant_EstadoOrdenProduccion','','","PopUp('configuracion','Detalles_Mant_EstadoOrdenProduccion','','","","PopUp('configuracion','Confirma_Eliminar_Mant_EstadoOrdenProduccion','','");
      }
	  function Nuevo_EstadoOrdenProduccion($prm)
      {
          $html=$this->Nuevo_Estado_OrdenProduccion($prm);$Helper=new htmlhelper;
          return $Helper->PopUp("","Nuevo Estado Orden de Compra",450,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_EstadoOrdenProduccion','tbl_estadofactura_pu','" . $prm . "')","textoInput"));
      }
      function Detalles_EstadoOrdenProduccion($prm)
      {
          return $this->Nuevo_EstadoOrdenProduccion($prm);
      }
      function Confirma_Eliminar_EstadoOrdenProduccion($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_descripcion FROM tbl_sgo_estadoOrdenProduccion WHERE sgo_int_estadoOrdenProduccion=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar el estado "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_EstadoOrdenProduccion','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
	  function Nuevo_Estado_OrdenProduccion($prm)
      {
         $Obj=new mysqlhelper; $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $descripcion="";
         $result = $Obj->consulta("select sgo_vch_descripcion from tbl_sgo_estadoOrdenProduccion WHERE sgo_int_estadoOrdenProduccion = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $descripcion=$row["sgo_vch_descripcion"];$estado=$row["sgo_bit_activo"];
            break;
         }
         $Val_DESCRIPCION=new InputValidacion();
         $Val_DESCRIPCION->InputValidacion('DocValue("fil_txtNombreEstadoOrdenProduccionNuevo")!=""','Debe especificar el nombre del estado');
         $html='<table id="tbl_estadofactura_pu" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">DescripciÃ³n</td><td>' . $Helper->textbox("fil_txtNombreEstadoOrdenProduccionNuevo",++$index,$descripcion,128,200,$TipoTxt->texto,"","","","textoInput",$Val_DESCRIPCION) . '</td></tr>';
			$html .='</table>';
         return $html;
      }
	   function Eliminar_Estado_OrdenProduccion($prm)
      {		  
          $Obj=new mysqlhelper;$valor=explode('|',$prm);		 
          if($Obj->execute("update tbl_sgo_estadoOrdenProduccion set sgo_bit_activo = 0 WHERE sgo_int_estadoOrdenProduccion=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoOrdenProduccion','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
	  function Actualizar_EstadoOrdenProduccion($prm)
      {
          $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0) $resp= $Obj->execute("UPDATE tbl_sgo_estadoOrdenProduccion
                            SET sgo_vch_descripcion='" . $valor[1] . "' WHERE sgo_int_estadoOrdenProduccion=" . $valor[0]);
          else $resp= $Obj->execute("INSERT INTO tbl_sgo_estadoOrdenProduccion (sgo_vch_descripcion, sgo_bit_activo) VALUES ('" . $valor[1] . "',1)");          
          if($resp!=-1)		  
			  return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_EstadoOrdenProduccion','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
			 
      }
/*********************************************************** ALMACÃ‰N - CATEGORÃ�A INSUMO *****************************************************************/
  	  function Filtros_Listar_CategoriaInsumo()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Nombre" => $Helper->textbox("fil_txtNombreCategoriaInsumo",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")            
         );
         $buttons=array($Helper->button("btnBuscarCategoriaInsumo","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_CategoriaInsumo','tbl_listaresultados','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listaresultados",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
	  function Grilla_Listar_CategoriaInsumo($prm)
      {
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_categoriainsumo as param, sgo_vch_descripcion as 'NOMBRE DE LA CATEGORIA' from tbl_sgo_categoriainsumo ";
         $where = "WHERE sgo_vch_descripcion like '%@p1%' and sgo_bit_estado = '1'";
         $where = $Obj->sql_where($where,$prm); 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('configuracion','Nuevo_Mant_CategoriaInsumo','','","PopUp('configuracion','Detalles_Mant_CategoriaInsumo','','","","PopUp('configuracion','Confirma_Eliminar_Mant_CategoriaInsumo','','");
      }
	  function Nuevo_CategoriaInsumo($prm)
      {
          $html=$this->Nuevo_Detalle_CategoriaInsumo($prm);$Helper=new htmlhelper;
          return $Helper->PopUp("","Nuevo Caracteristica de insumo",450,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_CategoriaInsumo','tbl_estadofactura_pu','" . $prm . "')","textoInput"));
      }
      function Detalles_CategoriaInsumo($prm)
      {
          return $this->Nuevo_CategoriaInsumo($prm);
      }
      function Confirma_Eliminar_CategoriaInsumo($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_descripcion FROM tbl_sgo_categoriainsumo WHERE sgo_int_categoriainsumo=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar la categoria "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_CategoriaInsumo','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
	  function Nuevo_Detalle_CategoriaInsumo($prm)
      {
         $Obj=new mysqlhelper; $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $descripcion="";
         $result = $Obj->consulta("select sgo_vch_descripcion from tbl_sgo_categoriainsumo WHERE sgo_int_categoriainsumo = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $descripcion=$row["sgo_vch_descripcion"];$estado=$row["sgo_bit_activo"];
            break;
         }
         $Val_DESCRIPCION=new InputValidacion();
         $Val_DESCRIPCION->InputValidacion('DocValue("fil_txtNombreCategoriaInsumoNuevo")!=""','Debe especificar el nombre de la categoria');
         $html='<table id="tbl_estadofactura_pu" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">DescripciÃ³n</td><td>' . $Helper->textbox("fil_txtNombreCategoriaInsumoNuevo",++$index,$descripcion,128,200,$TipoTxt->texto,"","","","textoInput",$Val_DESCRIPCION) . '</td></tr>';
			$html .='</table>';
         return $html;
      }
	   function Eliminar_Estado_CategoriaInsumo($prm)
      {		  
          $Obj=new mysqlhelper;$valor=explode('|',$prm);		 
          if($Obj->execute("update tbl_sgo_categoriainsumo set sgo_bit_estado = 0 WHERE sgo_int_categoriainsumo=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_CategoriaInsumo','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
	  function Actualizar_CategoriaInsumo($prm)
      {
          $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0) $resp= $Obj->execute("UPDATE tbl_sgo_categoriainsumo
                            SET sgo_vch_descripcion='" . $valor[1] . "' WHERE sgo_int_categoriainsumo=" . $valor[0]);
          else $resp= $Obj->execute("INSERT INTO tbl_sgo_categoriainsumo (sgo_vch_descripcion, sgo_bit_estado) VALUES ('" . $valor[1] . "',1)");          
          if($resp!=-1)		  
			  return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_CategoriaInsumo','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
			 
      }
/*********************************************************** ALMACÃ‰N - CARACTERISTICA PRODUCTO *****************************************************************/
  	  function Filtros_Listar_CaracteristicaProducto()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Nombre" => $Helper->textbox("fil_txtNombreCaracteristicaProducto",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")            
         );
         $buttons=array($Helper->button("btnBuscarCaracteristicaProducto","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_CaracteristicaProducto','tbl_listaresultados','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listaresultados",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
	  function Grilla_Listar_CaracteristicaProducto($prm)
      {
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_caracteristicaproducto as param, sgo_vch_descripcion as 'NOMBRE DE LA CARACTERISTICA' from tbl_sgo_caracteristica ";
         $where = "WHERE sgo_vch_descripcion like '%@p1%' and sgo_bit_activo = '1'";
         $where = $Obj->sql_where($where,$prm); 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('configuracion','Nuevo_Mant_CaracteristicaProducto','','","PopUp('configuracion','Detalles_Mant_CaracteristicaProducto','','","","PopUp('configuracion','Confirma_Eliminar_Mant_CaracteristicaProducto','','");
      }
	  function Nuevo_CaracteristicaProducto($prm)
      {
          $html=$this->Nuevo_Detalle_CaracteristicaProducto($prm);$Helper=new htmlhelper;
          return $Helper->PopUp("","Nuevo Caracteristica de insumo",450,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_CaracteristicaProducto','tbl_estadofactura_pu','" . $prm . "')","textoInput"));
      }
      function Detalles_CaracteristicaProducto($prm)
      {
          return $this->Nuevo_CaracteristicaProducto($prm);
      }
      function Confirma_Eliminar_CaracteristicaProducto($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_descripcion FROM tbl_sgo_caracteristica WHERE sgo_int_caracteristicaproducto=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar la caracteristica "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_CaracteristicaProducto','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
	  function Nuevo_Detalle_CaracteristicaProducto($prm)
      {
         $Obj=new mysqlhelper; $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $descripcion="";
         $result = $Obj->consulta("select sgo_vch_descripcion from tbl_sgo_caracteristica WHERE sgo_int_caracteristicaproducto = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $descripcion=$row["sgo_vch_descripcion"];$estado=$row["sgo_bit_activo"];
            break;
         }
         $Val_DESCRIPCION=new InputValidacion();
         $Val_DESCRIPCION->InputValidacion('DocValue("fil_txtNombreCaracteristicaProductoNuevo")!=""','Debe especificar el nombre de la caracteristica');
         $html='<table id="tbl_estadofactura_pu" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">DescripciÃ³n</td><td>' . $Helper->textbox("fil_txtNombreCaracteristicaProductoNuevo",++$index,$descripcion,128,200,$TipoTxt->texto,"","","","textoInput",$Val_DESCRIPCION) . '</td></tr>';
			$html .='</table>';
         return $html;
      }
	   function Eliminar_Estado_CaracteristicaProducto($prm)
      {		  
          $Obj=new mysqlhelper;$valor=explode('|',$prm);		 
          if($Obj->execute("update tbl_sgo_caracteristica set sgo_bit_activo = 0 WHERE sgo_int_caracteristicaproducto=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_CaracteristicaProducto','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
	  function Actualizar_CaracteristicaProducto($prm)
      {
          $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0) $resp= $Obj->execute("UPDATE tbl_sgo_caracteristica
                            SET sgo_vch_descripcion='" . $valor[1] . "' WHERE sgo_int_caracteristicaproducto=" . $valor[0]);
          else $resp= $Obj->execute("INSERT INTO tbl_sgo_caracteristica (sgo_vch_descripcion, sgo_bit_activo) VALUES ('" . $valor[1] . "',1)");          
          if($resp!=-1)		  
			  return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_CaracteristicaProducto','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
			 
      }
/*********************************************************** ALMACÃ‰N - CATEGORIA PRODUCTO *****************************************************************/
  	  function Filtros_Listar_CategoriaProducto()
      {
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $inputs=array(
            "Nombre" => $Helper->textbox("fil_txtNombreCategoriaProducto",++$index,"",128,150,$TipoTxt->texto,"","","","textoInput")            
         );
         $buttons=array($Helper->button("btnBuscarCategoriaProducto","Buscar",70,"Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProducto','tbl_listaresultados','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listaresultados",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
	  function Grilla_Listar_CategoriaProducto($prm)
      {
         $Obj=new mysqlhelper;
         $sql ="select sgo_int_categoriaproducto as param, sgo_vch_descripcion as 'NOMBRE DE LA CATEGORIA' from tbl_sgo_categoriaproducto ";
         $where = "WHERE sgo_vch_descripcion like '%@p1%' and sgo_bit_activo = '1'";
         $where = $Obj->sql_where($where,$prm); 
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('configuracion','Nuevo_Mant_CategoriaProducto','','","PopUp('configuracion','Detalles_Mant_CategoriaProducto','','","","PopUp('configuracion','Confirma_Eliminar_Mant_CategoriaProducto','','");
      }
	  function Nuevo_CategoriaProducto($prm)
      {
          $html=$this->Nuevo_Detalle_CategoriaProducto($prm);$Helper=new htmlhelper;
          return $Helper->PopUp("","Nuevo Categproa de Producto",450,$html,$Helper->button("","Grabar",70,"Operacion('configuracion','Registrar_CategoriaProducto','tbl_estadofactura_pu','" . $prm . "')","textoInput"));
      }
      function Detalles_CategoriaProducto($prm)
      {
          return $this->Nuevo_CategoriaProducto($prm);
      }
      function Confirma_Eliminar_CategoriaProducto($prm)
      {
          $Obj=new mysqlhelper; $Helper=new htmlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_descripcion FROM tbl_sgo_categoriaproducto WHERE sgo_int_categoriaproducto=" . $prm);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmaci&oacute;n",450,htmlentities('Â¿Esta seguro de eliminar la categoria "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('configuracion','Eliminar_CategoriaProducto','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
	  function Nuevo_Detalle_CategoriaProducto($prm)
      {
         $Obj=new mysqlhelper; $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $descripcion="";
         $result = $Obj->consulta("select sgo_vch_descripcion from tbl_sgo_categoriaproducto WHERE sgo_int_categoriaproducto = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $descripcion=$row["sgo_vch_descripcion"];$estado=$row["sgo_bit_activo"];
            break;
         }
         $Val_DESCRIPCION=new InputValidacion();
         $Val_DESCRIPCION->InputValidacion('DocValue("fil_txtNombreCategoriaProductoNuevo")!=""','Debe especificar el nombre de la caracteristica');
         $html='<table id="tbl_estadofactura_pu" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">DescripciÃ³n</td><td>' . $Helper->textbox("fil_txtNombreCategoriaProductoNuevo",++$index,$descripcion,128,200,$TipoTxt->texto,"","","","textoInput",$Val_DESCRIPCION) . '</td></tr>';
			$html .='</table>';
         return $html;
      }
	   function Eliminar_Estado_CategoriaProducto($prm)
      {		  
          $Obj=new mysqlhelper;$valor=explode('|',$prm);		 
          if($Obj->execute("update tbl_sgo_categoriaproducto set sgo_bit_activo = 0 WHERE sgo_int_categoriaproducto=" . $prm)!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProducto','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
	  function Actualizar_CategoriaProducto($prm)
      {
          $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);
          if($valor[0]!=0) $resp= $Obj->execute("UPDATE tbl_sgo_categoriaproducto
                            SET sgo_vch_descripcion='" . $valor[1] . "' WHERE sgo_int_categoriaproducto=" . $valor[0]);
          else $resp= $Obj->execute("INSERT INTO tbl_sgo_categoriaproducto (sgo_vch_descripcion, sgo_bit_activo) VALUES ('" . $valor[1] . "',1)");          
          if($resp!=-1)		  
			  return "<script>Operacion_Result(true);Buscar_Grilla('configuracion','Grilla_Listar_CategoriaProducto','tbl_listaresultados','','td_General');</script>";
          else return "<script>Operacion_Result(false);</script>";
			 
      }
  }
 ?>