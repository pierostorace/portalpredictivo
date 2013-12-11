<?php error_reporting(E_ALL^E_WARNING);include('../../code/lib/mysqlhelper.php'); include('../../code/be/be_general.php');

  Class htmlhelper{
     function combo_empresas_login($id){
        include('code/lib/mysqlhelper.php');$Obj=new mysqlhelper();
        $html='<select class="chzn-select textoInput" name="' . $id . '" id="' . $id . '" style="width:190px;">';
        $result=$Obj->conexion_maestra("SELECT sgo_int_empresa as value,sgo_vch_nombre as text FROM tbl_sgo_empresa WHERE sgo_bit_activo=1");
        while($row = mysqli_fetch_array($result,MYSQL_ASSOC)){
            $html.='<option value="' . $row["value"] . '">' . $row["text"] . '</option>';
        }
        $html .= '</select>';
        return $html;
     }
     function Menu_Vertical($menu){
        $Obj=new mysqlhelper();$titulo="";$html="";$subhtml="";
        foreach ($menu as $key => $value)
        {
           if($titulo!=$key)
           {
              $html .='<div width="100%">';
              $html .='<div onclick="" class="link titulo_menu"><div style="padding-top:2px;" onclick="location.href=' . $value . '"><img src="../../img/vineta_blanco.gif" width="7px" height="4px" align="center" />&nbsp;&nbsp;' . htmlentities($key,null,"utf-8") . '</div></div>';
//              $html .='<div onclick="Obj(\'div_submenu\').innerHTML=Obj(\'div_' . $key . '\').innerHTML;" class="link titulo_menu"><div style="padding-top:2px;"><img src="../../img/vineta_blanco.gif" width="7px" height="4px" align="center" />&nbsp;&nbsp;' . htmlentities($key) . '</div></div>';
              $html .='</div>';
              $titulo=$key;
           }

                /*<div id="div_Cotizacion" style="display:none">
                    <div class="link titulo_submenu">Clientes</div>
                    <div class="link titulo_submenu">Visitas</div>
                </div>*/
        }
        $result= $Obj->consulta("SELECT modulo.sgo_int_modulo,modulo.sgo_int_modulopadre,modulo.sgo_vch_nombre, modulo.sgo_vch_url FROM tbl_sgo_usuario usuario inner  join tbl_sgo_usuariomodulo usumod on usuario.sgo_int_usuario = usumod.sgo_int_usuario inner  join tbl_sgo_modulo modulo on    modulo.sgo_int_modulo = usumod.sgo_int_modulo where modulo.sgo_int_modulopadre	=" . (isset($_REQUEST["app"])?$_REQUEST["app"]:"-1") . " and usuario.sgo_int_usuario =".$_SESSION['usuario'][0]." order by modulo.sgo_int_orden");
        if(mysqli_num_rows($result)>0){
          $subhtml .='<div id="div_submenu_items" style="display:none">';
          while($fila = mysqli_fetch_array($result))
    	  {
  		     $subhtml .='<div class="link titulo_submenu" onclick="location.href=\'' . ($fila["sgo_vch_url"]!="#"?$fila["sgo_vch_url"] ."?app=" . $fila["sgo_int_modulopadre"]:$fila["sgo_vch_url"]) . '\'">' . htmlentities($fila["sgo_vch_nombre"],null,"utf-8") . '</div>';
   		  }
          $subhtml .='</div>';
        }
        return $html . $subhtml . "<script>document.getElementById('div_submenu').innerHTML=document.getElementById('div_submenu_items').innerHTML</script>";
     }
     function menu($usuario,$app)
     {
          $Obj=new mysqlhelper();
          $resultado= $Obj->consulta("SELECT modulo.sgo_int_modulo, modulo.sgo_vch_nombre, modulo.sgo_vch_imagen, modulo.sgo_vch_url FROM tbl_sgo_usuario usuario inner  join tbl_sgo_usuariomodulo usumod on usuario.sgo_int_usuario = usumod.sgo_int_usuario inner  join tbl_sgo_modulo modulo on    modulo.sgo_int_modulo = usumod.sgo_int_modulo where usuario.sgo_int_usuario =".$usuario." and sgo_int_modulopadre = ". $app." order by modulo.sgo_int_orden;");
          $html="<script>";
            //un array por cada uno de los men?s desplegables
            $html.='var opciones_menu = [
            					{
            						texto: "Inicio",
            						url: "../general/index.php",
                                    enlaces: []
            					},';

            			if($resultado)
            			{
              			    $cont=0;
            			    $Obj_aux=new mysqlhelper();
            				while($fila = mysqli_fetch_array($resultado))
            				{
                                $html .='{
            						texto: "' . htmlentities($fila["sgo_vch_nombre"],NULL,"utf-8") . '",
            						url: "' . ($fila["sgo_vch_url"]!="#"?$fila["sgo_vch_url"] ."?app=" . $app:$fila["sgo_vch_url"]) . '",
            						enlaces: [';
                                            $subresultado = $Obj_aux->consulta("SELECT modulo.sgo_int_modulo, modulo.sgo_vch_nombre, modulo.sgo_vch_imagen, modulo.sgo_vch_url FROM tbl_sgo_usuario usuario inner  join tbl_sgo_usuariomodulo usumod on usuario.sgo_int_usuario = usumod.sgo_int_usuario inner  join tbl_sgo_modulo modulo on    modulo.sgo_int_modulo = usumod.sgo_int_modulo where usuario.sgo_int_usuario =".$usuario." and sgo_int_modulopadre = ".$fila["sgo_int_modulo"]." order by modulo.sgo_int_orden");
          									if($subresultado)
          									{
          										while($subfila = mysqli_fetch_array($subresultado))
          										{
                                                    $html.='{
          													texto: "' . htmlentities($subfila["sgo_vch_nombre"],NULL,"utf-8") . '",
          													url: "' . $subfila["sgo_vch_url"]. (strpos($subfila["sgo_vch_url"],"?")===false?"?":"") . "&app=".$app."&mod=".$subfila["sgo_int_modulo"] . '"
          												},';
          										}
          									}
            						$html .="]}";

            					if(mysqli_num_rows($resultado)!=$cont){
                                    $html .=",";
            					}
            					$cont++;
            				}
                        }
            $html.="];";
            $html.='
            $(document).ready(function(){
            	$("#menu").generaMenu(opciones_menu);
            });
            </script>';
            echo $html;
     }
     function combo_persona($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT per.sgo_int_persona as value, per.sgo_vch_nombre as text FROM tbl_sgo_persona per WHERE per.sgo_bit_activo=1 ORDER BY per.sgo_vch_nombre",
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_persona_x_tipo_reload($prm,$selected){
        $sql="SELECT per.sgo_int_persona as value, per.sgo_vch_nombre as text FROM tbl_sgo_persona per ";
        switch($prm)
        {
           case '1': //Empleado
               $sql .="INNER JOIN tbl_sgo_empleado emp ON per.sgo_int_persona=emp.sgo_int_empleado";
               break;
           case '2': //Cliente
               $sql .="INNER JOIN tbl_sgo_cliente cli ON per.sgo_int_persona=cli.sgo_int_cliente";
               break;
           case '3': //Proveedor
               $sql .="INNER JOIN tbl_sgo_proveedor prv ON per.sgo_int_persona=prv.sgo_int_proveedor";
               break;
        }
        return $this->combo($sql . " WHERE per.sgo_bit_activo=1 ORDER BY per.sgo_vch_nombre",$selected);
     }
     function combo_proveedor($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT per.sgo_int_persona as value, per.sgo_vch_nombre as text
        FROM tbl_sgo_proveedor prv
        INNER JOIN tbl_sgo_persona per ON prv.sgo_int_proveedor=per.sgo_int_persona
        WHERE per.sgo_bit_activo=1 ORDER BY per.sgo_vch_nombre",
                $id,$posicion,300,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_cliente($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT per.sgo_int_persona as value, concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) as text
        FROM tbl_sgo_cliente cli
        INNER JOIN tbl_sgo_persona per ON cli.sgo_int_cliente=per.sgo_int_persona
        WHERE per.sgo_bit_activo=1 and cli.sgo_bit_activo=1 ORDER BY per.sgo_vch_nombre",
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
  	 function combo_cliente_reload($prm,$selected){
        return $this->combo_options("SELECT per.sgo_int_persona as value, concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) as text
        FROM tbl_sgo_cliente cli
        INNER JOIN tbl_sgo_persona per ON cli.sgo_int_cliente=per.sgo_int_persona
        WHERE per.sgo_bit_activo=1 and cli.sgo_bit_activo=1 ORDER BY per.sgo_vch_nombre",
                $selected);
     }
     function combo_direccion_x_cliente($id,$posicion,$prm,$selected,$onchange,$validacion=null,$inactivo=false){
//        return "SELECT sgo_int_direccion as value, sgo_vch_direccion as text FROM tbl_sgo_direccioncliente WHERE sgo_int_cliente=" . $prm;
        return $this->combo("SELECT sgo_int_direccion as value, sgo_vch_direccion as text FROM tbl_sgo_direccioncliente WHERE sgo_int_cliente=" . $prm,
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
	 function combo_direccion_x_cliente_x_os($id,$posicion,$prm,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT distinct dir.sgo_int_direccion as value, sgo_vch_nombretienda as text FROM tbl_sgo_direccioncliente dir INNER JOIN tbl_sgo_ordenserviciodetalle ocd ON dir.sgo_int_direccion =  ocd.sgo_int_direccion WHERE ocd.sgo_int_ordenserviciodetalle not in (SELECT sgo_int_ordenserviciodetalle FROM tbl_sgo_guiaremisiondetalle) and ocd.sgo_int_ordenservicio =" . $prm,
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_local_x_cliente($id,$posicion,$prm,$selected,$onchange,$validacion=null,$inactivo=false){
//        return "SELECT sgo_int_direccion as value, sgo_vch_direccion as text FROM tbl_sgo_direccioncliente WHERE sgo_int_cliente=" . $prm;
        return $this->combo("SELECT sgo_int_direccion as value, Concat(sgo_vch_codigotienda,' - ',sgo_vch_nombretienda,' - ',sgo_vch_direccion) as text FROM tbl_sgo_direccioncliente WHERE sgo_int_cliente=" . $prm,
                $id,$posicion,450,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_direccion_x_cliente_reload($prm,$selected){
        return $this->combo_options("SELECT 	sgo_int_direccion as value, concat(sgo_vch_codigotienda,' ',sgo_vch_nombretienda,' - ',sgo_vch_direccion) as text FROM tbl_sgo_direccioncliente where sgo_int_cliente=" . $prm,
                $selected);
     }
     function combo_contactos_x_cliente($id,$posicion,$prm,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_contacto as value, sgo_vch_nombre as text FROM tbl_sgo_contactocliente where sgo_int_cliente=" . $prm,
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_contactos_x_cliente_reload($prm,$selected){
        return $this->combo_options("SELECT sgo_int_contacto as value, sgo_vch_nombre as text FROM tbl_sgo_contactocliente where sgo_int_cliente=" . $prm,
                $selected);
     }
     function combo_producto_cliente($id,$posicion,$prm,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT pdt.sgo_int_producto as value, CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as text
        FROM tbl_sgo_producto pdt
        INNER JOIN tbl_sgo_tarifario tar ON tar.sgo_int_producto=pdt.sgo_int_producto
        LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
         on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
         and	pdt.sgo_int_color = catprodcol.sgo_int_color
         LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
         on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
         and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
         LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
         on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
         and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
        WHERE tar.sgo_int_cliente=" . $prm . " and pdt.sgo_bit_activo=1 and pdt.sgo_int_tipo in (2,3,4,5)
        ORDER BY pdt.sgo_vch_nombre asc",
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
  function combo_producto_cliente_reload($prm,$selected){
        return $this->combo_options("SELECT pdt.sgo_int_producto as value, CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as text
        FROM tbl_sgo_producto pdt
        INNER JOIN tbl_sgo_tarifario tar ON tar.sgo_int_producto=pdt.sgo_int_producto
        LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
         on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
         and	pdt.sgo_int_color = catprodcol.sgo_int_color
         LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
         on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
         and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
         LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
         on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
         and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
        WHERE 1=1 ".($prm!=""?" and tar.sgo_int_cliente=" . $prm:"") . " and pdt.sgo_bit_activo=1 and pdt.sgo_int_tipo in (2,3,4,5)
        ORDER BY pdt.sgo_vch_nombre asc",$selected);
     }
  	 function combo_producto_cliente_cotizacion($id,$posicion,$prm,$cotizacion,$selected,$onchange,$validacion=null,$inactivo=false){
        $sql = "SELECT 	pdt.sgo_int_producto as value, CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as text
        		FROM 	tbl_sgo_producto pdt
        		INNER 	JOIN tbl_sgo_tarifario tar 
        		ON 		tar.sgo_int_producto=pdt.sgo_int_producto
        		LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
         		on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
         		and		pdt.sgo_int_color = catprodcol.sgo_int_color
         		LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
         		on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
         		and		pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
         		LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
         		on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
         		and		pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
        		WHERE 	tar.sgo_int_cliente=" . $prm . " 
        		and 	pdt.sgo_bit_activo=1 and pdt.sgo_int_tipo in (2,3,4,5)"; 
        if($selected=="")
        {
	        $sql.=" and pdt.sgo_int_producto not in 
	        (select sgo_int_producto from tbl_sgo_cotizaciondetalle where sgo_int_cotizacion = ".$cotizacion.") ";	        
        }
        $sql.=" ORDER BY pdt.sgo_vch_nombre asc";
  	 	return $this->combo($sql,$id,$posicion,400,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_articuloventa_x_cliente_reload($prm,$selected){
        return $this->combo_options("SELECT pdt.sgo_int_producto as value, pdt.sgo_vch_nombre as text FROM tbl_sgo_tarifario tar INNER JOIN tbl_sgo_producto pdt ON tar.sgo_int_producto=pdt.sgo_int_producto WHERE tar.sgo_int_cliente=" . $prm . " and pdt.sgo_bit_activo=1 and pdt.sgo_int_tipo in (2,3,4,5) order by pdt.sgo_vch_nombre asc",
                $selected);
     }
     function combo_chofer_x_transportista($id,$posicion,$prm,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_chofer as value, sgo_vch_chofer as text from tbl_sgo_chofer where sgo_int_transportista=" . $prm,
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_chofer_x_transportista_reload($prm,$selected){
        return $this->combo_options("SELECT sgo_int_chofer as value, sgo_vch_chofer as text from tbl_sgo_chofer where sgo_int_transportista=".$prm,
                $selected);
     }
     function combo_vehiculo_x_transportista($id,$posicion,$prm,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_vehiculo as value, concat(sgo_vch_marcamodelo,'   ',sgo_vch_placa) as text from tbl_sgo_vehiculo where sgo_int_transportista=" . $prm,
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_vehiculo_x_transportista_reload($prm,$selected){
        return $this->combo_options("SELECT sgo_int_vehiculo as value, concat(sgo_vch_marcamodelo,'   ',sgo_vch_placa) as text from tbl_sgo_vehiculo where sgo_int_transportista=".$prm,
                $selected);
     }
     function combo_estado($id,$posicion,$selected,$onchange,$validacion=null){
        $html= '<select class="chzn-select textoInput" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" ' . ($validacion != null?'validacion="' . str_replace('"',"'",$validacion->regla . "|||" . $validacion->mensaje) . '"':"") . ' style="width:200px;">
                  <option value="">-- Seleccionar --</option>
                  <option value="1" ' . ($selected=="1"?"selected":"") . '>Activo</option>
                  <option value="0" ' . ($selected=="0"?"selected":"") . '>Inactivo</option>';
        return $html . '</select>';
     }
  	function combo_tipo_movimiento($selected){
        $html='<option value="">-- Seleccionar --</option>';
		$html.='<option value="1">ENTRADA</option>';
		$html.='<option value="2">SALIDA</option>';
		return $html;
     }
   	 function combo_tipo_filtrodocumentocomprobanteventa($id,$posicion,$selected,$onchange,$validacion=null){
        $html= '<select class="chzn-select textoInput" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" ' . ($validacion != null?'validacion="' . str_replace('"',"'",$validacion->regla . "|||" . $validacion->mensaje) . '"':"") . ' style="width:200px;">
                  <option value="">-- Seleccionar --</option>
                  <option value="1" ' . ($selected=="1"?"selected":"") . '>COMPROBANTE DE VENTA</option>
                  <option value="2" ' . ($selected=="2"?"selected":"") . '>GUIA DE REMISION</option>
                  <option value="3" ' . ($selected=="3"?"selected":"") . '>ORDEN DE SERVICIO</option>
                  <option value="4" ' . ($selected=="4"?"selected":"") . '>OC CLIENTE</option>';
        return $html . '</select>';
     }
     function combo_estadovisita($id,$posicion,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT sgo_int_estadovisita as value, sgo_vch_descripcion as text FROM tbl_sgo_estadovisita where sgo_bit_activo=1",
                $id,$posicion,200,$selected,$onchange,$validacion);
     }
	 function combo_estado_nuevocomprobante($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_estadocomprobante as value, sgo_vch_descripcion as text FROM tbl_sgo_estadocomprobante WHERE sgo_int_estadocomprobante",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }

	 function combo_estado_comprobante($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_estadocomprobante as value, sgo_vch_descripcion as text FROM tbl_sgo_estadocomprobante",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_estado_finalizado($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        $html='<select class="chzn-select" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" style="width:200px;" ' . ($validacion != null?'validacion="' . str_replace('"',"'",$validacion->regla . "|||" . htmlentities($validacion->mensaje)) . '"':"") . '  ' . ($inactivo==true?"disabled":"") . '>';
            $html.='<option value="">-- Seleccionar --</option>
                  <option value="0" ' . ($selected=="0"?"selected":"") . '>Pendiente</option>
                  <option value="1" ' . ($selected=="1"?"selected":"") . '>Concluido</option>';
        return $html . '</select>';
     }
     function combo_estadopendiente($id,$posicion,$selected,$onchange){
        $html='<select class="chzn-select" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" style="width:200px;">';
            $html.='<option value="">-- Seleccionar --</option>
                  <option value="0" ' . ($selected=="0"?"selected":"") . '>Concluido</option>
                  <option value="1" ' . ($selected=="1"?"selected":"") . '>Pendiente</option>';
        return $html . '</select>';
     }
	 function combo_ordenservicio_cliente($id,$posicion,$prm, $selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT distinct sgo_int_ordenservicio as value, case when LENGTH(LTRIM(RTRIM(sgo_vch_nroordencompracliente)))>0 then sgo_vch_nroordencompracliente else concat(sgo_vch_serie,'-',sgo_vch_numero) end as text FROM tbl_sgo_ordenservicio where sgo_int_estado not in (1,4,5) and sgo_int_cliente = ".$prm." group by text",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_ordenservicio_cliente_ordencompracliente($id,$posicion,$prm, $selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_ordenservicio as value, concat(sgo_vch_serie,'-', sgo_vch_numero, '  emitida el ', sgo_dat_fecharegistro) as text FROM 		tbl_sgo_ordenservicio where	sgo_int_estado <> 4 and sgo_vch_nroordencompracliente = (select sgo_vch_nroordencompracliente from tbl_sgo_ordenservicio where sgo_int_ordenservicio =".$prm.") and		sgo_int_cliente = (select sgo_int_cliente from tbl_sgo_ordenservicio where 	sgo_int_ordenservicio =".$prm.")",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
	 function combo_ordencompra_cliente($id,$posicion,$prm, $selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT distinct sgo_int_ordencompra as value, sgo_vch_nroordencompra as text FROM tbl_sgo_ordencompra where sgo_int_estadooc not in (1,3,4) and sgo_int_cliente = ".$prm,
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_estado_cotizacion($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_estadocotizacion as value, sgo_vch_descripcion as text FROM tbl_sgo_estadocotizacion WHERE sgo_bit_activo=1 ",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_estado_ordenservicio($id,$posicion,$prm,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_estadoos as value, sgo_vch_descripcion as text FROM tbl_sgo_estadoordenservicio WHERE sgo_bit_activo=1 " . ($prm=="0"?"AND sgo_int_estadoos IN (1)":""),
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_estado_ordencompra($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_estadooc as value, sgo_vch_descripcion as text FROM tbl_sgo_estadoordencompra WHERE sgo_bit_activo=1",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_estado_ordenproduccion($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_estado as value, sgo_vch_descripcion as text FROM tbl_sgo_estadoordenproduccion",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_moneda($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_moneda as value, sgo_vch_nombre as text FROM tbl_sgo_moneda",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_moneda_alias($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_moneda as value, sgo_vch_simbolo as text FROM tbl_sgo_moneda",
                $id,$posicion,120,$selected,$onchange,$validacion,$inactivo);
     }
  	 function combo_tamano_x_categoriaproducto($id,$posicion,$prm, $selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_tamano as value, sgo_vch_tamano as text FROM tbl_sgo_categoriaproductotamano where sgo_int_categoriaproducto =".$prm,
                $id,$posicion,200,$selected,$onchange,$validacion,$inactivo);
     }
  	 function combo_tamano_x_categoriaproducto_reload($prm,$selected){
        return $this->combo_options("SELECT sgo_int_tamano as value, sgo_vch_tamano as text FROM tbl_sgo_categoriaproductotamano where sgo_int_categoriaproducto =".$prm,
                $selected);
     }     
  	 function combo_calidad_x_categoriaproducto($id,$posicion,$prm, $selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_calidad as value, sgo_vch_calidad as text FROM tbl_sgo_categoriaproductocalidad where sgo_int_categoriaproducto =".$prm,
                $id,$posicion,200,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_calidad_x_categoriaproducto_reload($prm,$selected){
        return $this->combo_options("SELECT sgo_int_calidad as value, sgo_vch_calidad as text FROM tbl_sgo_categoriaproductocalidad where sgo_int_categoriaproducto =".$prm,
                $selected);
     }
  	 function combo_modelo_x_categoriaproducto($id,$posicion,$prm, $selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_modelo as value, sgo_vch_modelo as text FROM tbl_sgo_categoriaproductomodelo where sgo_int_categoriaproducto =".$prm,
                $id,$posicion,200,$selected,$onchange,$validacion,$inactivo);
     }
  	 function combo_modelo_x_categoriaproducto_reload($prm,$selected){
        return $this->combo_options("SELECT sgo_int_modelo as value, sgo_vch_modelo as text FROM tbl_sgo_categoriaproductomodelo where sgo_int_categoriaproducto =".$prm,
                $selected);
     }
  	 function combo_colores_x_categoriaproducto($id,$posicion, $prm, $selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_color as value, sgo_vch_color as text FROM tbl_sgo_categoriaproductocolor where sgo_int_categoriaproducto =".$prm,
                $id,$posicion,200,$selected,$onchange,$validacion,$inactivo);
     }
  	 function combo_colores_x_categoriaproducto_reload($prm,$selected){
        return $this->combo_options("SELECT sgo_int_color as value, sgo_vch_color as text FROM tbl_sgo_categoriaproductocolor where sgo_int_categoriaproducto =".$prm,
                $selected);
     }
     function combo_ubigeo($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_ubigeo as value, concat(sgo_vch_dpto,' - ',sgo_vch_prov,' - ',sgo_vch_dist) as text FROM tbl_sgo_ubigeo",
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
  	function combo_pais($id,$posicion,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT sgo_int_ubigeo as value, sgo_vch_descripcion as text FROM tbl_sgo_ubigeo WHERE sgo_vch_dpto='00' and sgo_vch_prov='00' and sgo_vch_dist='00'",
                $id,$posicion,250,$selected,$onchange,$validacion);
     }
     function combo_departamento($id,$posicion,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT sgo_int_ubigeo as value, sgo_vch_descripcion as text FROM tbl_sgo_ubigeo WHERE sgo_vch_pais = '139' and sgo_vch_prov='00' and sgo_vch_dist='00' and sgo_vch_dpto <> '00'",
                $id,$posicion,250,$selected,$onchange,$validacion);
     }
     function combo_provincia_x_departamento($id,$posicion,$prm,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT ubiprov.sgo_int_ubigeo as value, ubiprov.sgo_vch_descripcion as text FROM tbl_sgo_ubigeo ubiprov INNER JOIN tbl_sgo_ubigeo ubidpto ON ubidpto.sgo_vch_dpto=ubiprov.sgo_vch_dpto and ubidpto.sgo_int_ubigeo=" . ($prm==null?-1:$prm) . " and ubiprov.sgo_vch_prov!='00' and ubiprov.sgo_vch_dist='00'",
                $id,$posicion,250,$selected,$onchange,$validacion);
     }
     function combo_distrito_x_provincia($id,$posicion,$prm,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT ubidist.sgo_int_ubigeo as value, ubidist.sgo_vch_descripcion as text FROM tbl_sgo_ubigeo ubidist INNER JOIN tbl_sgo_ubigeo ubiprov ON ubiprov.sgo_vch_dpto=ubidist.sgo_vch_dpto and ubiprov.sgo_vch_prov=ubidist.sgo_vch_prov and ubiprov.sgo_int_ubigeo=" . ($prm==null?-1:$prm) . " and ubidist.sgo_vch_dist!='00'",
                $id,$posicion,250,$selected,$onchange,$validacion);
     }
     function combo_provincia_x_departamento_reload($prm,$selected){
        return $this->combo_options("SELECT ubiprov.sgo_int_ubigeo as value, ubiprov.sgo_vch_descripcion as text FROM tbl_sgo_ubigeo ubiprov INNER JOIN tbl_sgo_ubigeo ubidpto ON ubidpto.sgo_vch_dpto=ubiprov.sgo_vch_dpto and ubidpto.sgo_int_ubigeo=" . ($prm==null?-1:$prm) . " and ubiprov.sgo_vch_prov!='00' and ubiprov.sgo_vch_dist='00'",
                $selected);
     }
  	function combo_departamento_x_paises_reload($prm,$selected){
        return $this->combo_options("SELECT 	ubidpto.sgo_int_ubigeo as value, ubidpto.sgo_vch_descripcion as text FROM tbl_sgo_ubigeo ubidpto INNER JOIN tbl_sgo_ubigeo ubipais ON ubipais.sgo_vch_pais=ubidpto.sgo_vch_pais and ubipais.sgo_int_ubigeo=" . ($prm==null?-1:$prm) . " and 		ubidpto.sgo_vch_dpto!='00' and 		ubidpto.sgo_vch_prov='00' and ubidpto.sgo_vch_dist='00'",
                $selected);
     }
     function combo_distrito_x_provincia_reload($prm,$selected){
        return $this->combo_options("SELECT ubidist.sgo_int_ubigeo as value, ubidist.sgo_vch_descripcion as text FROM tbl_sgo_ubigeo ubidist INNER JOIN tbl_sgo_ubigeo ubiprov ON ubiprov.sgo_vch_dpto=ubidist.sgo_vch_dpto and ubiprov.sgo_vch_prov=ubidist.sgo_vch_prov and ubiprov.sgo_int_ubigeo=" . ($prm==null?-1:$prm) . " and ubidist.sgo_vch_dist!='00'",
                $selected);
     }
     function combo_tipoinventario($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_tipocomprobante as value, sgo_vch_descripcion as text FROM tbl_sgo_tipocomprobante WHERE sgo_int_tipo in (11)",
                $id,$posicion,200,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_tipodocumento($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_tipocomprobante as value, sgo_vch_descripcion as text FROM tbl_sgo_tipocomprobante WHERE sgo_int_tipo in (3,4)",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_tipocomprobante($id,$posicion,$prm,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_tipocomprobante as value, sgo_vch_descripcion as text FROM tbl_sgo_tipocomprobante " . ($prm!=""?"WHERE sgo_int_tipo in (" . $prm . ")":""),
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_categoriacomprobante($id,$posicion,$prm,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_categoriacomprobante as value, sgo_vch_descripcion as text FROM tbl_sgo_categoriacomprobante " . ($prm!=""?"WHERE sgo_int_tipo in (" . $prm . ")":"") . " ORDER BY 2",
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_categoria_tipocomprobante($prm,$selected){
        return $this->combo_options("SELECT sgo_int_categoriacomprobante as value, sgo_vch_descripcion as text
                FROM tbl_sgo_categoriacomprobante
                WHERE sgo_int_tipo IN (SELECT sgo_int_tipo FROM tbl_sgo_tipocomprobante WHERE sgo_int_tipocomprobante=" . $prm . ")  ORDER BY 2",
                $selected);
     }
     function combo_tipomovimiento($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        $html= '<select class="chzn-select textoInput" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" style="width:200px;">
                  <option value="">-- Seleccionar --</option>
                  <option value="1">Entrada</option>
                  <option value="2">Salida</option>';
        return $html . '</select>';
     }
     function combo_tiporecepcion($id,$posicion,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT sgo_int_tiporecepcion as value, sgo_vch_descripcion as text FROM tbl_sgo_tiporecepcion",
                $id,$posicion,150,$selected,$onchange,$validacion);
     }
     function combo_tipoubicacion($id,$posicion,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT sgo_int_tipodireccion as value, sgo_vch_descripcion as text FROM tbl_sgo_tipodireccion where sgo_bit_activo=1",
                $id,$posicion,150,$selected,$onchange,$validacion);
     }
     function combo_tipoformapago($id,$posicion,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT sgo_int_tipoformapago as value, sgo_vch_descripcion as text FROM tbl_sgo_tipoformapago where sgo_bit_activo=1",
                $id,$posicion,250,$selected,$onchange,$validacion);
     }
     function combo_modalidadpago($id,$posicion,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT sgo_int_modalidad as value, sgo_vch_descripcion as text FROM tbl_sgo_modalidadpago where sgo_bit_activo=1",
                $id,$posicion,200,$selected,$onchange,$validacion);
     }
     function combo_division($id,$posicion,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT sgo_int_division as value, sgo_vch_nombre as text FROM tbl_sgo_division where sgo_bit_activo=1",
                $id,$posicion,200,$selected,$onchange,$validacion);
     }
     function combo_tipodocumentoidentidad($id,$posicion,$selected,$onchange,$validacion=null){
        return $this->combo("SELECT sgo_int_documentoidentidad as value, sgo_vch_descripcion as text FROM tbl_sgo_tipodocumentoidentidad where sgo_bit_activo=1",
                $id,$posicion,200,$selected,$onchange,$validacion);
     }
     function combo_caja($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_caja as value, sgo_vch_descripcion as text FROM tbl_sgo_caja",
                $id,$posicion,200,$selected,$onchange,$validacion,$inactivo);
     }
  	function combo_caja_reload($selected){
        return $this->combo_options(" SELECT sgo_int_caja as value, sgo_vch_descripcion as text FROM tbl_sgo_caja ",
                $selected);
     }
     function combo_meses($id,$posicion,$selected,$onchange){
        $html= '<select class="chzn-select" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" style="width:120px;">';
                 $html .= '<option value="0">-- Seleccionar --</option>'.
                  '<option value="1" ' . ($selected==1?"selected":"") . '>Enero</option>'.
                  '<option value="2" ' . ($selected==2?"selected":"") . '>Febrero</option>'.
                  '<option value="3" ' . ($selected==3?"selected":"") . '>Marzo</option>'.
                  '<option value="4" ' . ($selected==4?"selected":"") . '>Abril</option>'.
                  '<option value="5" ' . ($selected==5?"selected":"") . '>Mayo</option>'.
                  '<option value="6" ' . ($selected==6?"selected":"") . '>Junio</option>'.
                  '<option value="7" ' . ($selected==6?"selected":"") . '>Julio</option>'.
                  '<option value="8" ' . ($selected==7?"selected":"") . '>Agosto</option>'.
                  '<option value="9" ' . ($selected==8?"selected":"") . '>Setiembre</option>'.
                  '<option value="10" ' . ($selected==10?"selected":"") . '>Octubre</option>'.
                  '<option value="11" ' . ($selected==11?"selected":"") . '>Noviembre</option>'.
                  '<option value="12" ' . ($selected==12?"selected":"") . '>Diciembre</option>';
        return $html . '</select>';
     }
     function combo_dias($id,$posicion,$selected,$onchange){
        $html= '<select class="chzn-select" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" style="width:120px;"><option value="0">-- Seleccionar --</option>';
                  for($i=1;$i<32;$i++)$html .= '<option value="' . $i . '" ' . ($selected==$i?"selected":"") . '>' . $i . '</option>';
        return $html . '</select>';
     }
     function combo_anios($id,$posicion,$selected,$onchange){
        $html= '<select class="chzn-select" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" style="width:120px;"><option value="">-- Seleccionar --</option>';
                  for($i=1950;$i<2013;$i++)$html .= '<option value="' . $i . '" ' . ($selected==$i?"selected":"") . '>' . $i . '</option>';
        return $html . '</select>';
     }
     function combo_articulo($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_producto as value, sgo_vch_nombre as text FROM tbl_sgo_producto where sgo_bit_activo=1 order by sgo_vch_nombre asc",
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_articulo_reload($selected){
        return $this->combo_options("SELECT sgo_int_producto as value, sgo_vch_nombre as text FROM tbl_sgo_producto where sgo_bit_activo=1 order by sgo_vch_nombre asc",
                $selected);
     }
     function combo_insumo($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_producto as value, sgo_vch_nombre as text FROM tbl_sgo_producto where sgo_bit_activo=1 and sgo_int_tipo in (1,2) order by sgo_vch_nombre asc",
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_insumo_reload($selected){
        return $this->combo_options("SELECT sgo_int_producto as value, sgo_vch_nombre as text FROM tbl_sgo_producto where sgo_bit_activo=1 and sgo_int_tipo in (1,2) order by sgo_vch_nombre asc",
                $selected);
     }
     function combo_articuloventa_reload($selected){
        return $this->combo_options("SELECT sgo_int_producto as value, sgo_vch_nombre as text FROM tbl_sgo_producto where sgo_bit_activo=1 and sgo_int_tipo in (2,3,4,5) order by sgo_vch_nombre asc",
                $selected);
     }
     function combo_articulocompra_reload($selected){
        return $this->combo_options("SELECT sgo_int_producto as value, sgo_vch_nombre as text FROM tbl_sgo_producto where sgo_bit_activo=1 and sgo_int_tipo in (1,4,5) order by sgo_vch_nombre asc",
                $selected);
     }
     function combo_articulo_comprobantecompra_reload($prm,$selected){
        return $this->combo_options("SELECT pdt.sgo_int_producto as value, pdt.sgo_vch_nombre as text
        FROM tbl_sgo_producto pdt
        INNER JOIN tbl_sgo_comprobantecompradetalle cco ON cco.sgo_int_producto=pdt.sgo_int_producto
        WHERE pdt.sgo_bit_activo=1 and cco.sgo_int_comprobantecompra in (" . $prm . ")
        ORDER BY sgo_vch_nombre asc",
                $selected);
     }
     function combo_articulo_comprobanteventa_reload($prm,$selected){
        return $this->combo_options("SELECT pdt.sgo_int_producto as value, pdt.sgo_vch_nombre as text
        FROM tbl_sgo_producto pdt
        INNER JOIN tbl_sgo_comprobanteventadetalle cco ON cco.sgo_int_producto=pdt.sgo_int_producto
        WHERE pdt.sgo_bit_activo=1 and pdt.sgo_int_tipo in (3) and cco.sgo_int_comprobanteventa in (" . $prm . ")
        GROUP BY pdt.sgo_int_producto ORDER BY sgo_vch_nombre asc",
                $selected);
     }
     function combo_almacen($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false)
     {
         return $this->combo("SELECT sgo_int_almacen as value,sgo_vch_nombre as text FROM tbl_sgo_almacen WHERE sgo_bit_activo=1",
                 $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_producto($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("	SELECT 	sgo_int_producto as value, 
        								CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as text 
        						FROM 	tbl_sgo_producto pdt 
        						LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
					          	on		pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
					          	and		pdt.sgo_int_color = catprodcol.sgo_int_color
					          	LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
					          	on		pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
					          	and		pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
					          	LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
					          	on		pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
					          	and		pdt.sgo_int_calidad = catprodcal.sgo_int_calidad
        						where 	sgo_bit_activo=1 
        						and 	sgo_int_tipo in (3) 
        						order 	by sgo_vch_nombre asc",
                $id,$posicion,250,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_producto_reload($selected){
        return $this->combo_options("SELECT sgo_int_producto as value, sgo_vch_nombre as text FROM tbl_sgo_producto where sgo_bit_activo=1 and sgo_int_tipo in (3) order by sgo_vch_nombre asc",
                $selected);
     }
     function combo_unidadmedida($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_unidadmedida as value, sgo_vch_descripcion as text FROM tbl_sgo_unidadmedida",
                $id,$posicion,200,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_categoriaproducto($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_categoriaproducto as value, sgo_vch_descripcion as text FROM tbl_sgo_categoriaproducto where sgo_bit_activo=1",
                $id,$posicion,200,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_tipopersona($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_tipopersona as value, sgo_vch_descripcion as text FROM tbl_sgo_tipopersona",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_tipooperacionmovimiento($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=null){
        return $this->combo("SELECT sgo_int_tipooperacionmovimiento as value, sgo_vch_descripcion as text FROM tbl_sgo_tipooperacionmovimiento order by sgo_vch_descripcion asc",
                $id,$posicion,200,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_tipoproducto($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=null){
        return $this->combo("SELECT sgo_int_tipoproducto as value, sgo_vch_descripcion as text FROM tbl_sgo_tipoproducto where sgo_bit_activo=1",
                $id,$posicion,200,$selected,$onchange,$validacion,$inactivo);
     }
     function combo_guiaremision_x_ordenservicio($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT distinct sgo_int_guiaremision as value, concat(sgo_vch_serie,'-', sgo_vch_numero) as text FROM tbl_sgo_guiaremision where sgo_int_guiaremision in (select sgo_int_guiaremision from tbl_sgo_guiaremisiondetalle where sgo_int_ordenservicio = 0)",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
	 function combo_guiaremision_x_ordenservicio_reload($prm,$selected){
        return $this->combo_options("SELECT distinct sgo_int_guiaremision as value, concat(sgo_vch_serie,'-', sgo_vch_numero) as text FROM tbl_sgo_guiaremision where sgo_int_guiaremision in (select sgo_int_guiaremision from tbl_sgo_guiaremisiondetalle where sgo_int_ordenservicio in ( select sgo_int_ordenservicio from tbl_sgo_ordenservicio where sgo_vch_nroordencompracliente = (select sgo_vch_nroordencompracliente from tbl_sgo_ordenservicio where sgo_int_ordenservicio=".$prm.") ) and sgo_int_estadoguiaremision not in (2,3))",
                $selected);
     }
     function combo_guiaremision_x_ordencompra($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT distinct sgo_int_guiaremision as value, concat(sgo_vch_serie,'-', sgo_vch_numero) as text FROM tbl_sgo_guiaremision where tbl_sgo_guiaremision.sgo_int_estadoguiaremision!=3 and sgo_int_guiaremision in (select sgo_int_guiaremision from tbl_sgo_guiaremisiondetalle where sgo_int_ordenservicio = 0)",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
	 function combo_guiaremision_x_ordencompra_reload($prm,$selected){
                return $this->combo_options("SELECT 	distinct guia.sgo_int_guiaremision as value, 
                										concat(guia.sgo_vch_serie,'-', guia.sgo_vch_numero) as text 
                							from		tbl_sgo_guiaremision guia
											inner		join tbl_sgo_guiaremisiondetalle guiadet
											on			guia.sgo_int_guiaremision = guiadet.sgo_int_guiaremision
											inner		join tbl_sgo_ordenserviciodetalle osdet
											on			guiadet.sgo_int_ordenserviciodetalle = osdet.sgo_int_ordenserviciodetalle
											inner		join tbl_sgo_ordenservicio os
											on			os.sgo_int_ordenservicio = osdet.sgo_int_ordenservicio											
											where		(
															os.sgo_vch_nroordencompracliente in
															(
																	select 	sgo_vch_nroordencompracliente 
																	from 		tbl_sgo_ordenservicio 
																	where 	sgo_int_ordenservicio = ".$prm."
																	and		sgo_vch_nroordencompracliente<>''
															)
															or
															guiadet.sgo_int_ordenservicio =  ".$prm."
														)
											and			guia.sgo_int_estadoguiaremision in (1,2)",
                $selected);
                //where		os.sgo_vch_nroordencompracliente = (select sgo_vch_nroordencompracliente from tbl_sgo_ordenservicio where sgo_int_ordenservicio = ".$prm.")
     }
	 function combo_estado_guiaremision($id,$posicion,$selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_estadoguiaremision as value, sgo_vch_descripcion as text FROM tbl_sgo_estadoguiaremision",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
	 function combo_transportista($id,$posicion,$prm, $selected,$onchange,$validacion=null,$inactivo=false){
        return $this->combo("SELECT sgo_int_transportista as value, sgo_vch_razonsocial as text FROM tbl_sgo_transportista",
                $id,$posicion,150,$selected,$onchange,$validacion,$inactivo);
     }
	 function combo_motivo_traslado($id,$posicion,$prm, $selected,$onchange,$validacion=null,$width){
        return $this->combo("SELECT sgo_int_motivo as value, sgo_vch_descripcion as text FROM tbl_sgo_motivotraslado where sgo_bit_activo=1",
                $id,$posicion,$width,$selected,$onchange,$validacion);
     }
     function combo_tipooperacion_movimiento_reload($prm,$selected){
        return $this->combo_options("SELECT sgo_int_tipooperacionmovimiento as value, sgo_vch_descripcion as text FROM tbl_sgo_tipooperacionmovimiento",
                $selected);
     }
     function combo_importar_ositem($id,$posicion,$selected,$onchange){
        $html= '<select class="chzn-select" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" style="width:200px;">';
                 $html .= '<option value="1" ' . ($selected==1?"selected":"") . '>Falabella</option>'.
                          '<option value="2" ' . ($selected==2?"selected":"") . '>Almacenes Par&iacute;s</option>'.
                          '<option value="3" ' . ($selected==3?"selected":"") . '>CEN</option>';
        return $html . '</select>';
     }
  	 function combo_reporte_tiporeporteventa($id,$posicion,$size,$selected,$onchange)
  	 {
        $html= '<select class="chzn-select" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" style="width:'.$size.';">';
                 $html .= '<option value="1" ' . ($selected==1?"selected":"") . '>General</option>'.
                          '<option value="2" ' . ($selected==2?"selected":"") . '>Por Tienda</option>'.
                          '<option value="3" ' . ($selected==3?"selected":"") . '>Detallado</option>';
        return $html . '</select>';
     }
     function combo($sql,$id,$posicion,$width,$selected,$onchange,$validacion=null,$inactivo=false){
        $Obj=new mysqlhelper();$result=$Obj->consulta($sql);
        $html='<select class="chzn-select textoInput" name="' . $id . '" id="' . $id . '" posicion="' . $posicion . '" ' . ($validacion != null?'validacion="' . str_replace('"',"'",$validacion->regla . "|||" . htmlentities($validacion->mensaje)) . '"':"") . ' style="width:' . $width . 'px;" onchange="' . $onchange . '" ' . ($inactivo?"disabled":"") . '><option value="">-- Seleccionar --</option>';
        while($row = mysqli_fetch_array($result,MYSQL_ASSOC)){
            $html.='<option value="' . $row["value"] . '" ' . ($selected===$row["value"]?"selected":"") . '>' . $row["text"] . '</option>';
        }
        $html .= '</select>';
//        if ($validacion != null) $html .="<input type='hidden' id='val_" . $id . "' value='" . $validacion->regla . "|" . $validacion->mensaje . "' />";
        return $html;
     }
     function combo_options($sql,$selected){
        $Obj=new mysqlhelper();$result=$Obj->consulta($sql);
        $html='<option value="">-- Seleccionar --</option>';
        while($row = mysqli_fetch_array($result,MYSQL_ASSOC)){
            $html.='<option value="' . $row["value"] . '" ' . ($selected===$row["value"]?"selected":"") . '>' . $row["text"] . '</option>';
        }
        return $html;
     }
     function hidden($id,$posicion,$value){
        return '<input type="hidden" id="' . $id . '" name="' . $id . '" posicion="' . $posicion . '" value="' . $value . '"/>';
     }
     function checkbox($id,$posicion,$selected,$onclick,$clase=""){
        return '<input type="checkbox" id="' . $id . '" name="' . $id . '" posicion="' . $posicion . '" ' . ($selected=="1"?"checked":"") . ' onclick="' . $onclick . '" class="' . $clase . '"/>';
     }
	 function label($id,$posicion,$value,$size,$clase="",$validacion=null){
        return '<input type="textbox" id="' . $id . '" name="' . $id . '" posicion="' . $posicion . '" value="' . $value . '" ' . ($validacion != null?'validacion="' . str_replace('"',"'",$validacion->regla . "|||" . htmlentities($validacion->mensaje)) . '"':"") . ' style="width:' . $size . 'px;" class="textoLabel ' . $clase . '" disabled />';
     }
	 function textbox($id,$posicion,$value,$maxlength,$size,$tipo,$onclick,$onchange,$onkeypress,$clase,$validacion=null,$inactivo=false){
        $html= '<input type="textbox" id="' . $id . '" name="' . $id . '" posicion="' . $posicion . '" value="' . $value . '" ' . ($validacion != null?'validacion="' . str_replace('"',"'",$validacion->regla . "|||" . htmlentities($validacion->mensaje)) . '"':"") . ' maxlength="' . $maxlength . '" style="width:' . $size . 'px;" onclick="' . $onclick . '" onchange="' . $onchange . '" onkeypress="' . ($onkeypress!=""?$onkeypress . ';':"") . ($tipo==0?"":($tipo==1?"return SoloNumerico(event);":"return SoloDecimal(event,this.value);")) . '" class="cajaInput ' . $clase . '" ' . ($inactivo?"disabled":"") . '/>';
//        if ($validacion != null) $html .="<input type='hidden' id='val_" . $id . "' value='" . $validacion->regla . "|" . $validacion->mensaje . "' />";
        return $html;
     }
	 function textbox_predictivo($id,$posicion,$value,$maxlength,$size,$validacion=null,$inactivo=false,$source)
	 {
	 	$script=$this->obtieneFuente($source,$id);
        $html= '<input type="textbox" id="' . $id . '" name="' . $id . '" posicion="' . $posicion . '" value="' . $value . '" ' . ($validacion != null?'validacion="' . str_replace('"',"'",$validacion->regla . "|||" . htmlentities($validacion->mensaje)) . '"':"") . ' maxlength="' . $maxlength . '" style="width:' . $size . 'px;" onclick="' . $onclick . '" onchange="' . $onchange . '" onkeypress="' . ($onkeypress!=""?$onkeypress . ';':"") . ($tipo==0?"":($tipo==1?"return SoloNumerico(event);":"return SoloDecimal(event,this.value);")) . '" class="cajaInput ' . $clase . '" ' . ($inactivo?"disabled":"") . '/>';
        return $script.$html;
     }
     function obtieneFuente($source,$id)
     {
     	$sql="";
     	$script='<script>$(function() {var availableTags = [';
		$Obj=new mysqlhelper();
		switch ($source)
		{
			case "clientes": $sql = "select 	concat(sgo_vch_nombre,' - ',sgo_vch_alias) as 'nombre' 
									from 		tbl_sgo_persona per 
									inner 		join tbl_sgo_cliente cli 
									on 			per.sgo_int_persona=cli.sgo_int_cliente 
									where 		per.sgo_bit_activo = 1";
			break;
			case "productos": $sql = "SELECT 	CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as 'nombre'
	          FROM tbl_sgo_producto pdt          
	          LEFT	JOIN tbl_sgo_categoriaproductocolor catprodcol
	          on	pdt.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
	          and	pdt.sgo_int_color = catprodcol.sgo_int_color
	          LEFT	JOIN tbl_sgo_categoriaproductotamano catprodtam
	          on	pdt.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
	          and	pdt.sgo_int_tamano = catprodtam.sgo_int_tamano	
	          LEFT	JOIN tbl_sgo_categoriaproductocalidad catprodcal
	          on	pdt.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
	          and	pdt.sgo_int_calidad = catprodcal.sgo_int_calidad          
	          INNER JOIN tbl_sgo_categoriaproducto pca 
	          on pca.sgo_int_categoriaproducto=pdt.sgo_int_categoriaproducto
	          where sgo_bit_activo =1";
			break;
		}
        $resultado= $Obj->consulta($sql);
        while($fila = mysqli_fetch_array($resultado))
        {
        	$script.='"'.$fila["nombre"].'",';
        }   
        $script = substr($script, 0,strlen($script)-1);
        $script.=']; $( "#'.$id.'" ).autocomplete({source: availableTags});});</script>';
        return $script;  	     	
     }
     function textarea($id,$posicion,$value,$cols,$rows,$onclick,$onchange,$onkeypress,$clase="",$validacion=null,$inactivo=false){
        $html=  '<textarea id="' . $id . '" name="' . $id . '" posicion="' . $posicion . '"  ' . ($validacion != null?'validacion="' . str_replace('"',"'",$validacion->regla . "|||" . htmlentities($validacion->mensaje)) . '"':"") . ' wrap="physical" rows="' . $rows . '" cols="' . $cols . '" onclick="' . $onclick . '" onchange="' . $onchange . '" onkeypress="' . ($onkeypress!=""?$onkeypress . ';':"") . '" style="resize:none;" class="cajaInput ' . $clase . '"  ' . ($inactivo?"disabled":"") . '>' . str_replace("<br>","\n",$value) . '</textarea>';
//        if ($validacion != null) $html .="<input type='hidden' id='val_" . $id . "' value='" . $validacion->regla . "|" . $validacion->mensaje . "' />";
        return $html;
     }
     function textdate($id,$posicion,$value,$multiple,$tipo,$size,$onchange,$clase,$validacion=null,$inactivo=false){
       $html= '<input type="textbox" id="' . $id . '" name="' . $id . '" posicion="' . $posicion . '" ' . ($validacion != null?'validacion="' . str_replace('"',"'",$validacion->regla . "|||" . htmlentities($validacion->mensaje)) . '"':"") . ' ' . ($multiple ? "readonly" : "") . ' onclick="JsCalendar(\'' . $id . '\',\'' . $id . '\',\'' . ($tipo == 0 ? '%d/%m/%Y %H:%M' : ($tipo == 1 ? '%d/%m/%Y' : '%H:%M')) . '\',\'' . $multiple . '\',\'' . (strlen($value) > 0 ? date("m/d/y") : '') . '\')" onchange="' . ($onchange != "" ? $onchange : '') . '" style="width:' . $size . "px" . '" value="' . $value . '" class="cajaInput ' . $clase . '" maxlength="0"  ' . ($inactivo?"disabled":"") . '/>';       
//       if ($validacion != null) $html .="<input type='hidden' id='val_" . $id . "' value='" . $validacion->regla . "|" . $validacion->mensaje . "' />";
       return $html;
     }
     function imagelink($id, $src, $width, $height, $title, $onclick, $clase=""){
        return $this->image($id, $src, $width, $height, $title, $onclick, ($clase . " link"));
     }
     function image($id, $src, $width, $height, $title, $onclick, $clase=""){
        return '<img src="' . $src . '" id="' . $id . '" onmousedown="' . $onclick . '" alt="' . $title . '" title="' . $title . '" width="' . $width . 'px" height="' . $height . 'px" border="0" class="' . $clase . '" />';
     }
     function button($id, $value, $width, $onclick,$clase=""){
        return '<input type="button" id="' . $id . '" onmousedown="' . $onclick . '" value="' . $value . '" style="width:' . $width . 'px" class="textoInput ' . $clase . '" />';
     }
     function submit($id, $value, $width, $onclick,$clase=""){
        return '<input type="submit" id="' . $id . '" onmousedown="' . $onclick . '" value="' . $value . '" style="width:' . $width . 'px" class="textoInput ' . $clase . '" />';
     }
     function onclick($onclick){
        return $onclick;
     }
     function prueba($fecha)
     {
       return $fecha;
     }
     function convertir_fecha_ingles($fecha){
        if(strlen($fecha)>0)
        {
            $partes = explode(' ',$fecha);$fecha = explode('/',$partes[0]);
            return $fecha[2] . '-'.$fecha[1] . '-'.$fecha[0] . (count($partes)>1?' ' . $partes[1]:'');
        }
        else return $fecha;
     }
  	
     function PopUp($id, $titulo, $width, $cuerpo, $btn_final){
        return '<div id="' . $id . '" class="panel"><table cellpadding="0" cellspacing="0" width="' . $width . '" class="panel_borde">' .
            '<tr class="panel_title"><td valign="middle" style="height:23px;width:100%;padding-left:10px;padding-right:5px;"><div style="float:left;">' . htmlentities($titulo) . '</div>' .
            '<div style="float:right;"><div class="cerrar" onclick="' . $this->onclick("Cerrar_PopUp('PopUp@');") . '" style="float:right;">&nbsp;</div></td>' .
            '</tr><tr><td id="body_PopUp@" class="panel_texto">' . $cuerpo . '<br/><div class="btnfinales">' . $btn_final . '&nbsp;&nbsp;&nbsp;' . $this->button("btnCerrar", "Cerrar", 70, "Cerrar_PopUp('PopUp@')") . '</div></td></tr></table></div>';
     }
     function Upload_Cargar($id,$posicion,$value,$ctrl,$opc,$prm,$pre_text,$pos_text,$size=550){
       return '<form name="form_'.$id.'" id="form_'.$id.'" enctype="multipart/form-data" action="../../code/bc/bc_' . $ctrl . '.php?opc=' . $opc . '&id='.$id.'" method="post" target="iframeUpload_'.$id.'">'.
        '<table cellpadding="0" class="textoInput"><tr>' .
            '<td>'.($pre_text!=""?$pre_text.'&nbsp;&nbsp;':"") . '<input type="text" id="'.$id.'" value="'.$value.'" posicion="'.$posicion.'" readonly="true" style="width:' . $size . 'px;font-size:12px;color:#989898">&nbsp;' .
            '<div id="formUpload_'.$id.'" class="upload right"><input type="file" name="file_'.$id.'" onchange="Obj(\'form_'.$id.'\').submit();"></div></td>'.
            '<td>&nbsp;&nbsp;'.htmlentities($pos_text).'</td>' .
        '</tr></table><div id="divUpload_mensaje_'.$id.'" class="left error"></div><iframe id="iframeAction_'.$id.'" name="iframeAction_'.$id.'" class="no_display"></iframe></form>';
     }
     function Upload($id,$posicion,$value,$size=550,$validacion=null){
       return '<input type="text" id="txt_'.$id.'" value="'.$value.'" posicion="'.$posicion.'" readonly="true" style="width:' . $size . 'px;font-size:12px;color:#989898" ' . ($validacion != null?'validacion="' . str_replace('"',"'",$validacion->regla . "|||" . htmlentities($validacion->mensaje)) . '"':"") . '>&nbsp;' .
            '<div id="formUpload_'.$id.'" class="upload "><input type="file" id="'.$id.'" name="'.$id.'" onchange="SetDocValue(\'txt_'.$id.'\',this.value)"></div>';
     }
     function Crear_Form_Layer($id,$ctrl,$opc,$prm,$inputs,$buttons,$cols,$size,$style,$class){
        return '<form name="form_'.$id.'" id="form_'.$id.'" enctype="multipart/form-data" action="../../code/bc/bc_' . $ctrl . '.php?opc=' . $opc . '&id='.$id.'&prm='.$prm.'" method="post" onsubmit="return false;" target="iframeAction_'.$id.'">'.
             $this->Crear_Layer($id,$inputs,$buttons,$cols,$size,$style,$class) .
             '<div id="divUpload_mensaje_'.$id.'" class="left error"></div><iframe id="iframeAction_'.$id.'" name="iframeAction_'.$id.'" class="no_display"></iframe></form>';
     }
     function Crear_Filtros_Layer($id,$inputs,$buttons,$cols,$size,$style,$class){
         return '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>' . $this->Crear_Layer($id,$inputs,$buttons,$cols,$size,$style,$class) . '</fieldset>';
     }
     function Crear_Layer($id,$inputs,$buttons,$cols,$size,$style,$class){
         $html ='<table id="'.$id.'" border="0" cellpadding="3" cellspacing="0" style="width:'.$size.'px;" class="textoInput"><tbody><tr>';
         $row=0;
         foreach ($inputs as $k => $v) {
            if($row%$cols==0)$html.='</tr><tr>';
            $obj=explode('~',$v);
            if(count($obj)==1){
                $html.='<td style="padding-left:5px;' . $style . '" class="' . $class . '" id="td_' . str_replace(array(" ",htmlentities("�"),htmlentities("�")),array("_", "o", "e"),htmlentities($k)) . '">' . htmlentities($k) . '</td><td>' . $v .'</td>';
            }
            else
            {
                $html.='<td style="padding-left:5px;' . $style . '" class="' . $class . '" id="td_' . str_replace(array(" ",htmlentities("�"),htmlentities("�")),array("_", "o", "e"),htmlentities($k)) . '">' . htmlentities($k) . '</td><td colspan="' . $obj[1] . '">' . $obj[0] .'</td>';
                $row+=$obj[1];
            }
            $row++;
         }
         foreach ($buttons as $v) {
            $html.='<td style="padding-left:5px">' . $v . '</td>';
         }
         $html.='</tr></tbody></table>';
         return $html;
     }
     function limpiarOCRepetidas($oc)
     {
     	$i=0;
     	$lista;
     	$valor = explode(',',$oc);
     	for($m=0;$m<count($valor);$m++)
     	{
     	if(count($lista)==0)
     	{
     	$lista[0] = $valor[$m];
     	}
     		else
     			{
     			for($j=0;$j<count($lista);$j++)
     			{
     			if($lista[$j]==$valor[$m])
     				{
     				$i=1;
     			}
     			}
     			if($i!=1)
     			{
     				$lista[count($lista)] = $valor[$m];
     			}
     				$i=0;
     			}
     			}
     			return $lista;
     			}
     function limpiarGuiasRepetidas($guias)
     {
        $i=0;
        $lista;
        $valor = explode(',',$guias);
        for($m=0;$m<count($valor);$m++)
        {
          if(count($lista)==0)
          {
            $lista[0] = $valor[$m];
          }
          else
          {
            for($j=0;$j<count($lista);$j++)
            {
              if($lista[$j]==$valor[$m])
              {
                $i=1;
              }
            }
            if($i!=1)
            {
              $lista[count($lista)] = $valor[$m];
            }
            $i=0;
          }
        }
        return $lista;
     }
  	 function Crear_Tabs($id,$content_tabs, $titulo_tabs, $size,$onClick)
  	 {
        $sb = ""; $sbTab = "";$sbContent = "";
        if (count($content_tabs) > 0)
        {
            $sb .='<div class="wrap"><ul id="' . $id . '" class="tabs">';
            $i=0;
            foreach($content_tabs as $content)
            {
                $sbTab .='<li id="' . $id . '_' . $i . '"><a href="#" ' . (strlen($size)>0?'class="' . $size . '"':'') . '>' . htmlentities($titulo_tabs[$i]) . '</a></li>';
                $sbContent .='<div class="pane">' . $content . '</div>';
                $i++;
            }
            $sb .=$sbTab . '</ul>' . $sbContent . '</div>';
            $sb .='<script>$("#' . $id . '").tabs("> .pane"' . (strlen($onClick)> 0 ? ',{onClick: function(event, tabIndex) {' . $onClick . ';}}' : '') . ');</script>';
        }
        return $sb;
     }
     function Imprimir_Grilla($result,$nuevo,$ver,$editar,$eliminar,$size=null,$btn_extras=array(),$obj_colextras=array(),$descript_cols=array(),$filas_paginacion=20,$titulo){
        $botones=new GrillaBotones;
        $botones->GrillaBotones($nuevo,$ver,$editar,$eliminar);
        return $this->Crear_Grilla($result,null,$botones,$size,$btn_extras,$obj_colextras,$descript_cols,$filas_paginacion,$titulo);
     }
     function Crear_Grilla($result,$id_grilla,$botones,$size=null,$btn_extras=array(),$obj_colextras=array(),$descript_cols=array(),$filas_paginacion=20,$titulo){     	
        $registro=1;
        $cols=mysqli_num_fields($result);
        $tot_cols=$cols;$btns=0; 
        $rows=mysqli_num_rows($result);
        $id=($id_grilla==null?"grilla_" . date("i") . "_" . rand(1, 900000):$id_grilla);
        $cabecera="";
        $grilla="<div id='div_" . $id . "'>
        			<table style='width:" . ($size!=null?$size . "px":"100%") . ";' cellspacing='0' cellpadding='0' border='0' align='center'>
        				<tr>
        					<td valign='top'>"
         						."<table id='" . $id . "' border='0' width='100%' cellspacing='0' cellpadding='0' class='sortable'>"
         							."<thead>";
         $cabecera .="<tr class='cabeceraTablaResultado' align='center'>";
        if($botones->nuevo!==""){ $cabecera.='<td class="no_print filaTablaResultado center link no_sort" align="center" style="width:30px" accessKey="n" alt="Presione aqu&iacute; para agregar un nuevo registro.' . htmlentities("\nTecla r�pida: Alt+n") . '" title="Presione aqu&iacute; para agregar un nuevo registro.' . htmlentities("\nTecla r�pida: Alt+n") . '" onclick="' . $botones->nuevo . (strpos($botones->nuevo,')')===false?'0\')':"") . '">+</td>';$tot_cols++;}
        if($botones->seleccionar!=="")
        { 
        	$cabecera.='<td class="no_print filaTablaResultado center link no_sort" align="center" style="width:30px">
        					<input id="" type="checkbox" onclick="Select_Todochk(\'' . $id . '\',this.checked)" />
        				</td>';
        	$tot_cols++;
        }
        if($botones->ver!=="" || $botones->editar!=="" || $botones->eliminar!=="" || $btn_extras!==null){ $cabecera.='<td class="no_print filaTablaResultado no_sort "></td>';
          if($botones->ver!==""){ $tot_cols++;$btns++;}
          if($botones->editar!==""){ $tot_cols++;$btns++;}
          if($botones->eliminar!==""){ $tot_cols++;$btns++;}
          foreach ($btn_extras as $k => $v) { $tot_cols++;$btns++;}
        }
        $btn_width=25*$btns;
        $finfo = mysqli_fetch_fields($result);
        $i=0;
        foreach ($finfo as $val) 
        {
          if($val->name!=="param")$cabecera.="<td class='filaTablaResultado'>" . htmlentities($val->name) . "</td>";
          foreach ($obj_colextras as $k => $v) {
            $valor=explode('|',$v);if($valor[0]==($i+1)){$cabecera.="<td class='filaTablaResultado'>" . htmlentities($k) . "</td>";}else if($valor[0]==$i){$cabecera.="<td class='no_print no_display'></td>";}}
          $i++;
        }
        $cabecera.="</tr>";
        $cabeceratitulo ="<tr class='tituloTablaResultado'><td class='tituloTablaCelda' colspan='" . $tot_cols . "'>" . $titulo . "</td></tr>";
        $toolbox ="<tr class='toolboxTablaResultado'><td class='toolboxTablaCelda' colspan='" . $tot_cols . "'>";
        $toolbox .= $this->imagelink("","../../img/excel.jpg",16,16,"Exportar a Excel","Exportar_Excel()","no_print") . "&nbsp;&nbsp;";        
        $toolbox .= $this->imagelink("","../../img/print.png",16,16,"Imprimir Grilla","Grilla_Imprimir('div_" . $id . "')","no_print") . "&nbsp;&nbsp;";
        $toolbox .="</td></tr>";
        $grilla.= $cabeceratitulo . $toolbox . $cabecera . "</thead><tbody>";
//        $grilla.= $toolbox . $cabecera . "</thead><tbody>";
        $totales=0;
        if($rows>0){
           $tr_display='';$tr_class='';
           while($row = mysqli_fetch_row($result)){
                if($registro>$filas_paginacion) $tr_display='no_display';
                if($row[0]=="--totales--"){$tr_class="cabeceraTablaResultado";$totales=1;}
                $grilla.="<tr class='" . $tr_display . " " . $tr_class . "' id='tr_" . $id . "_" . $registro . "'>";
                if($botones->nuevo!="")$grilla.='<td class="no_print filaTablaResultado center">&nbsp;</td>';
                if($botones->seleccionar!="")$grilla.="<td class='no_print filaTablaResultado center'><input id='chk_" . $id . "_" . $registro . "' type='checkbox' value='" . $row[0] . "' /></td>";
                if($botones->ver!=="" || $botones->editar!=="" || $botones->eliminar!=="" || $btn_extras!==null){
                    $grilla.='<td class="no_print filaTablaResultado" width="' . $btn_width . 'px">';
                    if($botones->ver!=="") $grilla.='&nbsp;<img src="../../img/b_ver.png" width="16px" height="16px" border="0" class="link no_print" alt="Presione aqu&iacute; para ver el detalle de este registro." title="Presione aqu&iacute; para ver el detalle de este registro." onclick="' . $botones->ver .  $row[0] . '\')" />';
                    if($botones->editar!=="") $grilla.='&nbsp;<img src="../../img/b_editar.gif" width="16px" height="16px" border="0" class="link no_print" alt="Presione aqu&iacute; para actualizar este registro." title="Presione aqu&iacute; para actualizar este registro." onclick="' . $botones->editar . $row[0] . '\')" />';
                    if($botones->eliminar!=="") $grilla.='&nbsp;<img src="../../img/b_eliminar.gif" width="16px" height="16px" border="0" class="link no_print" alt="Presione aqu&iacute; para eliminar este registro." title="Presione aqu&iacute; para eliminar este registro." onclick="' . $botones->eliminar . (strpos($botones->eliminar,')')===false?$row[0] . '\')':"") . '" />';
                    foreach ($btn_extras as $k => $v) {
                       $valor=explode('|',$v);
                       $grilla.='&nbsp;<img src="../../img/' . $valor[0] . '" width="16px" height="16px" border="0" class="link no_print" alt="' . htmlentities($k) . '" title="' . htmlentities($k) . '" onclick="' . $valor[1] . $row[0] . '\')" />';
                    }
                    $grilla .="</td>";
                }
                $j=0;$tipo;
                for($i=1;$i<$cols;$i++){
                  if(count($obj_colextras)>0){
                    foreach ($obj_colextras as $k => $v) {
                       $valor=explode('|',$v);
                       if($valor[0]==($i)){
                          $grilla.='<td class="no_print filaTablaResultado textoData center">' . str_replace("num_posicion",($registro*100) + (++$j),$valor[1]) . '</td>';
                       }
                       else if($valor[0]=="0" && ($i-1)==0){
                          $grilla.='<td class="no_display">' . str_replace("num_valor",$row[0],str_replace("num_posicion",($registro*100) + (++$j),$valor[1])) . '</td>';
                       }
                    }
                  }
                  $grilla.='<td class="filaTablaResultado textoData center">';
                  if($row[$i]!==null){
                     $valor=explode('|',$row[$i]);
                     switch(count($valor)){
                        case 1: //Columna normal
                            $tipo=array_search($i,$descript_cols);
                            if($tipo!==false)
                            {
                               $grilla.= str_replace("num_valor",$valor[0],str_replace("num_posicion",($registro*100) + (++$j),$tipo));
                            }
                            else  $grilla.=($valor[0]!=""?$valor[0]:"&nbsp;");
                            break;
                        case 2: //Columna link
                            $grilla.= '<span class="link" onclick="' . $valor[1] . (strpos($valor[1],")")===false?$row[0] . '\')':'') . '">' . $valor[0] . '</span>';
                            break;
                        case 3: //Columna link con imagen
                            $grilla.= '<span class="link" onclick="' . $valor[1] . (strpos($valor[1],")")===false?$row[0] . '\')':'') . '">' . $valor[0] . '<img src="../../img/' . $valor[2] . '" width="16px" height="16px" border="0" /></span>';
                            break;
                        default: //Columna normal
                            $grilla.= ($valor[0]!=""?$valor[0]:"&nbsp;");
                            break;
                     }
                  }
                  else $grilla.="&nbsp;";
                  $grilla .="</td>";
                }
                $grilla.="</tr>";
                $registro++;
            }
        }
        else{ $grilla.='<tr><td colspan="' . $tot_cols . '" class="textoData center">No se han encontrado resultados para la b&uacute;squeda</td></tr>';}
        if($rows==1 && $totales==1){ $grilla.='<tr><td colspan="' . $tot_cols . '" class="textoData center">No se han encontrado resultados para la b&uacute;squeda</td></tr>';}
        mysqli_free_result($result);
        $grilla.="</tbody></table></td></tr></table></div>";
        $grilla .= "<form id='frmExcel' method='post' action='../../code/lib/export_excelhelper.php' target='_blank'><input type='hidden' name='hfGrilla' id='hfGrilla' value='".str_replace("'", "",$grilla)."' /></form>";                        
        return $grilla . $this->Paginacion($filas_paginacion,$registro-1,$id . "","tr_" . $id . "_") . "<script>sorttable.init();</script>";      
     }
     function Paginacion($filas_paginacion, $filas, $tbl_id, $tr_id){
        $tot_paginas = 8; $spn_style = ""; $html="";
        $html .="<table id='paginacion_" . $tbl_id . "' width='100%' class='paginacion' cellpadding='0' cellspacing='0' border='0'><tr>";
        if ($filas > $filas_paginacion)
        {
            $paginas = $filas / $filas_paginacion;
            if ($paginas > 1)
            {
                $html .= "<td style='width:45%' class='cellgrid2 no-print'>&nbsp;";
                $html .= "<img id='" . $tbl_id . "_tab_first' src='../../img/tab_first.gif' width='16' height='16' onclick='Paginacion(1,\"" . $tbl_id . "\",\"" . $tr_id . "\",0," . $filas_paginacion . ");' style='visibility:hidden;vertical-align:middle' class='link'>";
                $html .= "<img id='" . $tbl_id . "_tab_left' src='../../img/tab_left.gif' width='16' height='16' onclick='Paginacion(1,\"" . $tbl_id . "\",\"" . $tr_id . "\",-1," . $filas_paginacion . ");' style='visibility:hidden;vertical-align:middle' class='link'>";
                $html .= "&nbsp;&nbsp;";
                $html .= "<span id='" . $tbl_id . "_Pag_numeracion'>";
                $html .= "<span style='padding-left:10px;vertical-align:middle;display:none' id='" . $tr_id . "_pagprev' onclick='Paginacion(1,\"" . $tbl_id . "\",\"" . $tr_id . "\",-1," . $filas_paginacion . ");' class='link linkpaginacion'>...</span>";
                if ($paginas < $tot_paginas) $tot_paginas=$paginas;
                for ($j = 1; $j <= $paginas; $j++)
                {
                    if ($j > $tot_paginas) $spn_style = "display:none;";
                    else $spn_style = "";
                    $html .= "<span style='padding-left:10px;vertical-align:middle;" . $spn_style . "' id='" . $tr_id . "_pag_" . $j . "' onclick='Paginacion(" . $j . ",\"" . $tbl_id . "\",\"" . $tr_id . "\",0," . $filas_paginacion . ");' class='link " . ($j === 1 ? "linkpaginacionactual" : "linkpaginacion") . " '>" . $j . "</span>";
                }
                if ($filas % $filas_paginacion != 0) $html .= "<span style='padding-left:10px;vertical-align:middle;" . $spn_style . "' id='" . $tr_id . "_pag_" . $j . "' onclick='Paginacion(" . $j . ",\"" . $tbl_id . "\",\"" . $tr_id . "\",0," . $filas_paginacion . ");' class='link linkpaginacion'>" . $j . "</span>";
                if ($paginas > $tot_paginas) $html .= "<span style='padding-left:10px;vertical-align:middle' id='" . $tr_id . "_pagant' onclick='Paginacion(2,\"" . $tbl_id . "\",\"" . $tr_id . "\",1," . $filas_paginacion . ");' class='link linkpaginacion'>...</span>";
                $html .= "</span>&nbsp;&nbsp;";
                $html .= "<img style='vertical-align:middle' id='" . $tbl_id . "_tab_right' src='../../img/tab_right.gif' width='16' height='16' onclick='Paginacion(2,\"" . $tbl_id . "\",\"" . $tr_id . "\",1," . $filas_paginacion . ");' class='link'>";
                $html .= "<img style='vertical-align:middle' id='" . $tbl_id . "_tab_last' src='../../img/tab_last.gif' width='16' height='16' onclick='Paginacion(" . $j . ",\"" . $tbl_id . "\",\"" . $tr_id . "\",0," . $filas_paginacion . ");' class='link'>";
                $html .= "&nbsp;&nbsp;</td>&nbsp;&nbsp;</td>";
            }
        }
        $html .= "<td width='25%' class='TextPaginacion'>";
		if($filas>$filas_paginacion)
        	$html .= "<span id='" . $tbl_id . "_Titulo_numeracion' style='margin-left:0px'> 1 - " . $filas_paginacion . " de " . $filas . "</span> registro(s)&nbsp;&nbsp;";
		else
			$html .= "<span id='" . $tbl_id . "_Titulo_numeracion' style='margin-left:0px'>" . $filas . "</span> registro(s)&nbsp;&nbsp;";
        $html .= "</td></tr></table><input type='hidden' id='" . $tbl_id . "_PagActual' value='1'/>";
        return $html;
     }
     function Fecha_Castellano( $time, $part = false, $formatDate = '' ){
        #Declare n compatible arrays
        $month = array("","Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiempre", "Octubre", "Noviembre", "Diciembre");#n
        $month_execute = "n"; #format for array month

        $month_mini = array("","ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "DIC");#n
        $month_mini_execute = "n"; #format for array month

        $day = array("Domingo","Lunes","Martes","Mi&eacute;rcoles","Jueves","Viernes","S&aacute;bado"); #w
        $day_execute = "w";

        $day_mini = array("DOM","LUN","MAR","MIE","JUE","VIE","SAB"); #w
        $day_mini_execute = "w";

    /*
    Other examples:
        Whether it's a leap year
        $leapyear = array("Este a?o febrero tendr? 28 d?as"."Si, estamos en un a?o bisiesto, un d?a m?s para trabajar!"); #l
         $leapyear_execute = "L";
    */

        #Content array exception print "HOY", position content the name array. Duplicate value and key for optimization in comparative
        $print_hoy = array("month"=>"month", "month_mini"=>"month_mini");

        if( $part === false ){
//            return date("d", $time) . " de " . $month[date("n",$time)] . ", ". date("H:i",$time) ." hs";
            return $day[date("w", $time)] . ", " . date("d", $time) . " de " . $month[date("n",$time)] . " del ". date("Y",$time). " a las ". date("H:i",$time);
        }elseif( $part === true ){
            if( ! empty( $print_hoy[$formatDate] ) && date("d-m-Y", $time ) == date("d-m-Y") ) return "HOY"; #Exception HOY
            if( ! empty( ${$formatDate} ) && !empty( ${$formatDate}[date(${$formatDate.'_execute'},$time)] ) ) return ${$formatDate}[date(${$formatDate.'_execute'},$time)];
            else return date($formatDate, $time);
        }else{
            return date("d-m-Y H:i", $time);
        }
     }
     //------    CONVERTIR NUMEROS A LETRAS         ---------------
//------    M�xima cifra soportada: 18 d�gitos con 2 decimales
//------    999,999,999,999,999,999.99
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE BILLONES
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE MILLONES
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE PESOS 99/100 M.N.
//------    Creada por:                        ---------------
//------             ULTIMINIO RAMOS GAL�N     ---------------
//------            uramos@gmail.com           ---------------
//------    10 de junio de 2009. M�xico, D.F.  ---------------
//------    PHP Version 4.3.1 o mayores (aunque podr�a funcionar en versiones anteriores, tendr�as que probar)
    function numtoletras($xcifra,$moneda)
    {
      $xarray = array(0 => "Cero",
      1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
      "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
      "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
      100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
      );
      //
      $xcifra = trim($xcifra);
      $xlength = strlen($xcifra);
      $xpos_punto = strpos($xcifra, ".");
      $xaux_int = $xcifra;
      $xdecimales = "00";
      if (!($xpos_punto === false))
       {
       if ($xpos_punto == 0)
          {
          $xcifra = "0".$xcifra;
          $xpos_punto = strpos($xcifra, ".");
          }
       $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
       $xdecimales = substr($xcifra."00", $xpos_punto + 1, 2); // obtengo los valores decimales
       }

    $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
    $xcadena = "";
    for($xz = 0; $xz < 3; $xz++)
       {
       $xaux = substr($XAUX, $xz * 6, 6);
       $xi = 0; $xlimite = 6; // inicializo el contador de centenas xi y establezco el l�mite a 6 d�gitos en la parte entera
       $xexit = true; // bandera para controlar el ciclo del While
       while ($xexit)
          {
          if ($xi == $xlimite) // si ya lleg� al l�mite m�ximo de enteros
             {
             break; // termina el ciclo
             }

          $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
          $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres d�gitos)
          for ($xy = 1; $xy < 4; $xy++) // ciclo para revisar centenas, decenas y unidades, en ese orden
             {
             switch ($xy)
                {
                case 1: // checa las centenas
                   if (substr($xaux, 0, 3) < 100) // si el grupo de tres d�gitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                      {
                      }
                   else
                      {
                      $xseek = $xarray[substr($xaux, 0, 3)]; // busco si la centena es n�mero redondo (100, 200, 300, 400, etc..)
                      if ($xseek)
                         {
                         $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Mill�n, Millones, Mil o nada)
                         if (substr($xaux, 0, 3) == 100)
                            $xcadena = " ".$xcadena." CIEN ".$xsub;
                         else
                            $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                         $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                         }
                      else // entra aqu� si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                         {
                         $xseek = $xarray[substr($xaux, 0, 1) * 100]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                         $xcadena = " ".$xcadena." ".$xseek;
                         } // ENDIF ($xseek)
                      } // ENDIF (substr($xaux, 0, 3) < 100)
                   break;
                case 2: // checa las decenas (con la misma l�gica que las centenas)
                   if (substr($xaux, 1, 2) < 10)
                      {
                      }
                   else
                      {
                      $xseek = $xarray[substr($xaux, 1, 2)];
                      if ($xseek)
                         {
                         $xsub = $this->subfijo($xaux);
                         if (substr($xaux, 1, 2) == 20)
                            $xcadena = " ".$xcadena." VEINTE ".$xsub;
                         else
                            $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                         $xy = 3;
                         }
                      else
                         {
                         $xseek = $xarray[substr($xaux, 1, 1) * 10];
                         if (substr($xaux, 1, 1) * 10 == 20)
                            $xcadena = " ".$xcadena." ".$xseek;
                         else
                            $xcadena = " ".$xcadena." ".$xseek." Y ";
                         } // ENDIF ($xseek)
                      } // ENDIF (substr($xaux, 1, 2) < 10)
                   break;
                case 3: // checa las unidades
                   if (substr($xaux, 2, 1) < 1) // si la unidad es cero, ya no hace nada
                      {
                      }
                   else
                      {
                      $xsub = $this->subfijo($xaux);
                      $xseek = (substr($xaux, 2, 1)=="1" && $xsub==""?"UNO":$xarray[substr($xaux, 2, 1)]); // obtengo directamente el valor de la unidad (del uno al nueve)
                      $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                      } // ENDIF (substr($xaux, 2, 1) < 1)
                   break;
                } // END SWITCH
             } // END FOR
             $xi = $xi + 3;
          } // ENDDO

          if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
             $xcadena.= " DE";

          if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
             $xcadena.= " DE";

          // ----------- esta l�nea la puedes cambiar de acuerdo a tus necesidades o a tu pa�s -------
          if (trim($xaux) != "")
             {
             switch ($xz)
                {
                case 0:
                   if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                      $xcadena.= "UN BILLON ";
                   else
                      $xcadena.= " BILLONES ";
                   break;
                case 1:
                   if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                      $xcadena.= "UN MILLON ";
                   else
                      $xcadena.= " MILLONES ";
                   break;
                case 2:
                   if ($xcifra < 1 )
                      {
                      $xcadena = "CERO CON $xdecimales/100 $moneda ";
                      }
                   if ($xcifra >= 1 && $xcifra < 2)
                      {
                      $xcadena = "UNO CON $xdecimales/100 $moneda ";
                      }
                   if ($xcifra >= 2)
                      {
                      $xcadena.= " CON $xdecimales/100 $moneda "; //
                      }
                   break;
                } // endswitch ($xz)
             } // ENDIF (trim($xaux) != "")
          // ------------------      en este caso, para M�xico se usa esta leyenda     ----------------
          $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
          $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
          $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
          $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
          $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
          $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
          $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
       } // ENDFOR ($xz)
       return trim($xcadena);
    } // END FUNCTION

    function agregarCaracteres($longitud,$direccion,$cadena)
    {
        for($i=0;$i<$longitud;$i++)
        {
            if($direccion==1)//Izquierda
            {
                $cadena = " ".$cadena;
            }
            else
            {
                $cadena=$cadena." ";
            }
        }
        return $cadena;
    }
    function agregaTab($cantidad, $direccion, $cadena)
    {
        for($i=0;$i<$cantidad;$i++)
        {
            if($direccion==1)//Izquierda
            {
                $cadena = "\t".$cadena;
            }
            else
            {
                $cadena=$cadena."\t";
            }
        }
        return $cadena;
    }
    function saltoLinea($cantidad)
    {
      $salto="";
      for($i=0;$i<$cantidad;$i++)
      {
        $salto.="\r\n";
      }
      return $salto;
    }
    function subfijo($xx)
       { // esta funci�n regresa un subfijo para la cifra
       $xx = trim($xx);
       $xstrlen = strlen($xx);
       if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
          $xsub = "";
       //
       if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
          $xsub = "MIL";
       //
       return $xsub;
       } // END FUNCTION
    function mesLetras($mes)
    {
      $mesLetras="";
      switch($mes)
      {
        case 1: $mesLetras="Enero";
        break;
        case 2: $mesLetras="Febrero";
        break;
        case 3: $mesLetras="Marzo";
        break;
        case 4: $mesLetras="Abril";
        break;
        case 5: $mesLetras="Mayo";
        break;
        case 6: $mesLetras="Junio";
        break;
        case 7: $mesLetras="Julio";
        break;
        case 8: $mesLetras="Agosto";
        break;
        case 9: $mesLetras="Septiembre";
        break;
        case 10: $mesLetras="Octubre";
        break;
        case 11: $mesLetras="Noviembre";
        break;
        case 12: $mesLetras="Diciembre";
        break;
      }
      return $mesLetras;
    }
  }
?>