<?php include_once('../../code/lib/htmlhelper.php'); include_once('../../code/bl/bl_general.php');
include_once('../../code/lib/loghelper.php');      
  Class bl
  {
/********************************************************CATÁLOGO CLIENTES**************************************************************************/
      function Filtros_Listar_Clientes(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=10;
         $inputs=array(
            //"Cliente" => $Helper->combo_cliente("fil_cmbBusquedacliente",$index,"",""),
            "Cliente" => $Helper->textbox_predictivo("fil_txtCliente",++$index,"",128,200,"","","clientes"),            
            "Nro. Doc." => $Helper->textbox("fil_txtBusquedaRUC",++$index,"",11,100,$TipoTxt->numerico,"","","",""),
            "Estado" => $Helper->combo_estado("fil_cmbBusquedaestado",++$index,"1",""),
            "Departamento" => $Helper->combo_departamento("fil_cmbBusquedadepartamento",++$index,"","Cargar_Combo('general','Combo_Provincias','fil_cmbBusquedaprovincia','fil_cmbBusquedadepartamento','fil_cmbBusquedaprovincia');"),
            "Provincia" => $Helper->combo_provincia_x_departamento("fil_cmbBusquedaprovincia",++$index,"-1","","Cargar_Combo('general','Combo_Distritos','fil_cmbBusquedadistrito','fil_cmbBusquedaprovincia','fil_cmbBusquedadistrito');"),
            "Distrito" => $Helper->combo_distrito_x_provincia("fil_cmbBusquedadistrito",++$index,"-1","","")
         ); 
         $buttons=array($Helper->button("btnBuscarCliente","Buscar",70,"Buscar_Grilla('crm','Grilla_Listar_Clientes','tbl_listarclientes','','td_General')","textoInput"));
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>';
         $html .= $Helper->Crear_Layer("tbl_listarclientes",$inputs,$buttons,3,990,"","");
         $html .='</fieldset>';
         return $html;
      }
      function Grilla_Listar_Clientes($prm){
         $Obj=new mysqlhelper;         
         $valor=explode('|',$prm);
         $sql ="SELECT 	cli.sgo_int_cliente as param, 
							per.sgo_vch_nrodocumentoidentidad as Documento, 
							per.sgo_vch_nombre as 'Razón Social',
							per.sgo_vch_alias as 'Nombre Comercial'			
				FROM 		tbl_sgo_persona per
				INNER 	JOIN tbl_sgo_cliente cli 
				ON 		per.sgo_int_persona=cli.sgo_int_cliente 
         		WHERE 	1=1";
         
         if($valor[0]!="")
         {
         	//$sql.=" and per.sgo_int_persona =".$valor[0];
         	$sql.=" and concat(per.sgo_vch_nombre,' - ',per.sgo_vch_alias) like '%".$valor[0]."%'";
         }
      	 if($valor[1]!="")
         {
         	$sql.=" and per.sgo_vch_nrodocumentoidentidad like '%".$valor[1]."%'";
         }
         if($valor[2]!="")
         {
         	$sql.=" and cli.sgo_bit_activo=".$valor[2];
         }
         else 
         {
         	$sql.=" and cli.sgo_bit_activo=1";
         }
      	 if($valor[3]!="")
         {
         	$sql.=" and (select sgo_vch_dpto from tbl_sgo_ubigeo where sgo_int_ubigeo = cli.sgo_int_ubigeo) = (select sgo_vch_dpto from tbl_sgo_ubigeo where sgo_int_ubigeo =".$valor[3].")";
         }
         if($valor[4]!="")
         {
         	$sql.=" and (select sgo_vch_prov from tbl_sgo_ubigeo where sgo_int_ubigeo = cli.sgo_int_ubigeo) = (select sgo_vch_prov from tbl_sgo_ubigeo where sgo_int_ubigeo =".$valor[4].")";
         }
         if($valor[5]!="")
         {
         	$sql.=" and (select sgo_vch_dist from tbl_sgo_ubigeo where sgo_int_ubigeo = cli.sgo_int_ubigeo) = (select sgo_vch_dist from tbl_sgo_ubigeo where sgo_int_ubigeo =".$valor[5].")";
         }
         
         //"cli.sgo_int_cliente=@p1 and per.sgo_vch_nrodocumentoidentidad	='@p2' and cli.sgo_bit_activo=@p3 and cli.sgo_bit_activo=1";
         //and ubidpto.sgo_int_ubigeo=@p4 and ubiprov.sgo_int_ubigeo=@p5 and ubidist.sgo_int_ubigeo=@p6 
         
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('crm','Nuevo','','","PopUp('crm','Detalles','','","","PopUp('crm','Confirma_Eliminar','','",null, array(), array(), array(), 20, "");
      }
      function Nuevo_Cliente($prm){
          $html="<fieldset class='textoInput'><legend align='left'>Datos Generales</legend>".$this->Nuevo_Cliente_DatosGenerales($prm)."</fieldset>";
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Nuevo Cliente",1200,$html,$Helper->button("","Grabar",70,"Operacion('crm','Registrar_Cliente','tbl_Tab_DatosGenerales','" . $prm . "')","textoInput")) . "<script>Focus('fil_txtRUC');</script>";
      }
      function Detalles_Cliente($prm){
          echo '<script>location.href="cliente_detalle.php?app=1&prm=' . $prm . '";</script>';
      }
      function Confirma_Eliminar_Cliente($prm){
          $Obj_General=new bl_general();$Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar al cliente ' . $Obj_General->Obtener_Nombre_Persona($prm) . '?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Cliente','','" . $prm . "')"));
      }
      function Eliminar_Cliente($prm){
          $Obj=new mysqlhelper;
          if($Obj->execute("UPDATE tbl_sgo_cliente SET sgo_bit_activo=0 WHERE sgo_int_cliente=" . $prm)!=-1)
              return "<script>Operacion_Result(true);BtnMouseDown('btnBuscarCliente');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }

/********************************************************DETALLES DEL CLIENTE**************************************************************************/
      function Tabs_Detalles_Cliente($prm){
        $Helper=new htmlhelper;$content_tabs = array(); $titulo_tabs = array();
        $content_tabs[0] ="<br/>" . $this->Tab_DatosGenerales($prm);
        $titulo_tabs[0]="General";
        $content_tabs[1] ="<br/>" . $this->Tab_Direcciones_Cliente($prm);
        $titulo_tabs[1]="Locales";
        $content_tabs[2] ="<br/>" . $this->Tab_Contactos_Cliente($prm);
        $titulo_tabs[2]="Contactos";
        $content_tabs[3] ="<br/>" . $this->Tab_DatosFinancieros($prm);
        $titulo_tabs[3]="Finanzas";
        $content_tabs[4] ="<br/>" . $this->Tab_Credito_Cliente($prm);
        $titulo_tabs[4]="Credito";
        $content_tabs[5] ="<br/><div id='div_Cotizaciones_Cliente'>" . $this->Grilla_Listar_Cotizaciones_Cliente($prm) . "</div>";
        $titulo_tabs[5]="Cotizaciones";
        $content_tabs[6] ="<br/><div id='div_OrdenesCompra_Cliente'>" . $this->Grilla_Listar_OrdenesServicio_Cliente($prm) . "</div>";
        $titulo_tabs[6]="O/S";
        $content_tabs[7] ="<br/><div id='div_Facturas_Cliente'>" . $this->Grilla_Listar_Facturas_Cliente($prm) . "</div>";
        $titulo_tabs[7]="Facturas";
        $content_tabs[8] ="<br/><div id='div_Guias_Cliente'>" . $this->Grilla_Listar_Guias_Cliente($prm) . "</div>";
        $titulo_tabs[8]="GR";
        $content_tabs[9] ="<br/>" . $this->Tab_Visitas_Cliente($prm);
        $titulo_tabs[9]="Visitas";
        $content_tabs[10] ="<br/>" . $this->Tab_Acuerdos_Cliente($prm);
        $titulo_tabs[10]="Acuerdos";
        $content_tabs[11] ="<br/>" . $this->Tab_Pendientes_Cliente($prm);
        $titulo_tabs[11]="Pendientes";
        $html = $Helper->Crear_Tabs("Tabs_Detalles_Cliente",$content_tabs, $titulo_tabs, "s","");
        return $html;
      }
      function Nuevo_Cliente_DatosGenerales($prm){
         $Obj=new mysqlhelper; $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $ruc="";$razsocial="";$division="";$tipodoc="";$nombcomercial="";$agente="0";$reglas="";$dist="-1";$prov="-1";$dpto="-1";
         $result = $Obj->consulta("SELECT per.sgo_vch_nrodocumentoidentidad	,per.sgo_vch_nombre,cli.sgo_int_division,per.sgo_int_tipodocumentoidentidad,per.sgo_vch_alias,case cli.sgo_bit_agenteretencion when 1 then 1 else 0 end as sgo_bit_agenteretencion,cli.sgo_txt_reglasretencion,cli.sgo_int_ubigeo as idubidist,ubiprov.sgo_int_ubigeo as idubiprov,ubidpto.sgo_int_ubigeo as idubidpto
              FROM tbl_sgo_cliente cli
              INNER JOIN tbl_sgo_persona per ON per.sgo_int_persona=cli.sgo_int_cliente
              LEFT JOIN tbl_sgo_ubigeo ubidist on ubidist.sgo_int_ubigeo=cli.sgo_int_ubigeo
              LEFT JOIN tbl_sgo_ubigeo ubiprov on ubiprov.sgo_vch_prov=ubidist.sgo_vch_prov and ubiprov.sgo_vch_dpto=ubidist.sgo_vch_dpto and ubiprov.sgo_vch_dist='00'
              LEFT JOIN tbl_sgo_ubigeo ubidpto on ubidpto.sgo_vch_dpto=ubidist.sgo_vch_dpto and ubidpto.sgo_vch_prov='00' and ubidpto.sgo_vch_dist='00' WHERE sgo_int_cliente = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $ruc=$row["sgo_vch_nrodocumentoidentidad"];$razsocial=$row["sgo_vch_nombre"];$division=$row["sgo_int_division"];$tipodoc=$row["sgo_int_tipodocumentoidentidad"];$nombcomercial=$row["sgo_vch_alias"];
            $agente=$row["sgo_bit_agenteretencion"];$reglas=$row["sgo_txt_reglasretencion"];$dist=$row["idubidist"];$prov=$row["idubiprov"];$dpto=$row["idubidpto"];
            break;
         }
         $Val_RUC=new InputValidacion();
         $Val_RUC->InputValidacion('DocValue("fil_txtRUC")!=""','Debe especificar el Ruc del cliente');
         $Val_RazSocial=new InputValidacion();
         $Val_RazSocial->InputValidacion('DocValue("fil_txtRazonSocial")!=""','Debe especificar la raz&oacute;n social del cliente');
         $Val_NombComer=new InputValidacion();
         $Val_NombComer->InputValidacion('DocValue("fil_txtNombreComercial")!=""','Debe especificar el nombre comercial del cliente');
         $Val_Division=new InputValidacion();
         $Val_Division->InputValidacion('DocValue("fil_cmb_division")!=""','Debe especificar la divisi&oacute;n del cliente');
         $Val_TipoDoc=new InputValidacion();
         $Val_TipoDoc->InputValidacion('DocValue("fil_cmb_tipodocidentidad")!=""','Debe especificar el tipo de documento');
         $Val_Ubigeo=new InputValidacion();
         $Val_Ubigeo->InputValidacion('DocValue("fil_cmbdistritoDatoCliente")!=""','Debe especificar correctamente el ubigeo');
         $html='<table id="tbl_Tab_DatosGenerales" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">Nro. Doc.</td><td>' . $Helper->textbox("fil_txtRUC",++$index,$ruc,15,200,$TipoTxt->texto,"","","","textoInput",$Val_RUC) . '</td>
                         <td class="textoInput" id="td_Razon">' . ($tipodoc==2?"Raz&oacute;n Social":"Nombres y Apellidos") . '</td><td>' . $Helper->textbox("fil_txtRazonSocial",++$index,$razsocial,150,300,$TipoTxt->texto,"","","","textoInput",$Val_RazSocial) . '</td>
                         <td class="textoInput">Divisi&oacute;n</td><td>' . $Helper->combo_division("fil_cmb_division",++$index,$division,"",$Val_Division) . '</td></tr>
                     <tr><td class="textoInput">Tipo Doc. Identidad</td><td>' . $Helper->combo_tipodocumentoidentidad("fil_cmb_tipodocidentidad",++$index,$tipodoc,"if(DocValue('fil_cmb_tipodocidentidad')==2){SetDocInnerHTML('td_Razon','Raz&oacute;n Social');}else{SetDocInnerHTML('td_Razon','Nombres y Apellidos');}",$Val_TipoDoc) . '</td>
                         <td class="textoInput">Nombre Comercial</td><td>' . $Helper->textbox("fil_txtNombreComercial",++$index,$nombcomercial,100,300,$TipoTxt->texto,"","","","textoInput",$Val_NombComer) . '</td></tr>
                     <tr><td class="textoInput">Agente de Retenci&oacute;n</td><td>' . $Helper->checkbox("fil_chkAgeRetencion",++$index,$agente,"DisabledControl('fil_txtReglasRetencion',!this.checked)") . '</td>
                         <td class="textoInput">Observaciones</td><td>' . $Helper->textarea("fil_txtReglasRetencion",++$index,$reglas,60,3,"","","","",null,($agente==1?false:true)) . '</td></tr>
                     <tr><td style="padding-left:5px" class="textoInput">Departamento</td><td>' . $Helper->combo_departamento("fil_cmbdepartamentoDatoCliente",++$index,$dpto,"Cargar_Combo('general','Combo_Provincias','fil_cmbprovinciaDatoCliente','fil_cmbdepartamentoDatoCliente','fil_cmbprovinciaDatoCliente');") . '</td>
                         <td style="padding-left:5px" class="textoInput">Provincia</td><td>' . $Helper->combo_provincia_x_departamento("fil_cmbprovinciaDatoCliente",++$index,$dpto,$prov,"Cargar_Combo('general','Combo_Distritos','fil_cmbdistritoDatoCliente','fil_cmbprovinciaDatoCliente','fil_cmbdistritoDatoCliente')") . '</td>
                         <td style="padding-left:5px" class="textoInput">Distrito</td><td>' . $Helper->combo_distrito_x_provincia("fil_cmbdistritoDatoCliente",++$index,$prov,$dist,"",$Val_Ubigeo) . '</td></tr>';
            $html .='</table>';
         return $html;
      }
      function Tab_DatosGenerales($prm){
         $Helper=new htmlhelper;
         $html=$this->Nuevo_Cliente_DatosGenerales($prm);
         $html .= '<br/><div align="right">' . $Helper->button("","Grabar",70,"Operacion('crm','Actualizar_DatosGenerales','tbl_Tab_DatosGenerales','" . $prm . "');","textoInput") . '</div>';
         return $html;
      }
      function Actualizar_DatosGenerales($prm){
         $Obj=new mysqlhelper;$valor=explode('|',$prm);
         $trans=$Obj->transaction();
         try{
           if($valor[0]!="0"){
              $sql="UPDATE 	tbl_sgo_persona 
              		SET 	sgo_vch_nrodocumentoidentidad='" . $valor[1] . "',
              				sgo_vch_nombre='" . str_replace('&', '&amp;', $valor[2]) . "',sgo_int_tipodocumentoidentidad=" . $valor[4] . ",sgo_vch_alias='" . $valor[5] . "' WHERE sgo_int_persona=" . $valor[0];
              if($trans->query($sql))
              {
                  $sql="UPDATE 	tbl_sgo_cliente 
                  		SET 	sgo_int_division=" . $valor[3] . ",
                  				sgo_bit_agenteretencion=" . $valor[6] . ",
                  				sgo_txt_reglasretencion='" .  $valor[7] . "',
                  				sgo_int_ubigeo=" . $valor[10] . " 
                  		WHERE 	sgo_int_cliente=" . $valor[0];
                  if(!$trans->query($sql))throw new Exception($sql . " => ". $trans->error);
              }
              else throw new Exception($sql . " => ". $trans->error);
              $trans->commit();$trans->close();
           }
           else{
              $sql="INSERT INTO tbl_sgo_persona 
              		(
              			sgo_vch_nrodocumentoidentidad, 
              			sgo_vch_nombre, 
              			sgo_int_tipodocumentoidentidad,
              			sgo_vch_alias,
              			sgo_bit_activo
              		) 
              		VALUES 
              		(
              			'" . $valor[1] . "',
              			'" . $valor[2] . "',
              			"  . $valor[4] . ",
              			'" . $valor[5] . "',
              			1
              		)";
              if($trans->query($sql))
              {
                  $id=mysqli_insert_id($trans);
                  $sql="INSERT INTO tbl_sgo_cliente (sgo_int_cliente,sgo_int_division,sgo_bit_agenteretencion,sgo_txt_reglasretencion,sgo_int_ubigeo,sgo_bit_activo) VALUES (" . $id . "," . $valor[3] . "," . $valor[6] . ",'" . $valor[7] . "'," . $valor[10] . ",1)";
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
         echo "<script>SetDocInnerHTML('div_nombcliente','Detalles de " . $valor[2] . "');</script>";
         return 1;
      }

/****************************************************************MENU CLIENTES************************************************************************/
/****************************************************************MENU CLIENTES************************************************************************/
/****************************************************************MENU CLIENTES************************************************************************/

/****************************************************************TAB DIRECCIONES************************************************************************/
      function Tab_Direcciones_Cliente($prm){
         $html='<table cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                      <td class="pag_tab_zona_sup">' . $this->Filtros_Listar_Direcciones_Cliente($prm) . '</td>
                  </tr>
                  <tr>
                      <td id="div_Direcciones_Cliente" class="pag_tab_zona_inf">'. $this->Grilla_Listar_Direcciones_Cliente($prm,'','','','','','','','') . '</td>
                  </tr>
                </table>';
         return $html;
         // . $this->Grilla_Listar_Direcciones_Cliente($prm,'','','','','','','','') . '</td>
      }
      function Filtros_Listar_Direcciones_Cliente($prm){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>
                <table id="tbl_listardireccionescliente" border="0" cellpadding="3" cellspacing="0" width="990px">
                    <tr>
                        <td class="textoInput">Tipo Local</td>
                        <td>' . $Helper->hidden("fil_id",++$index,$prm) . $Helper->combo_tipoubicacion("fil_cmb_busquedatipoubicacion",$index,"","") . '</td>
                        <td style="padding-left:5px" class="textoInput">C&oacute;digo</td>
                        <td>' . $Helper->textbox("fil_txtbusquedaCodigoTienda",++$index,"",15,100,$TipoTxt->texto,"","","","textoInput") . '</td>
                        <td style="padding-left:5px" class="textoInput">Nombre</td>
                        <td>' . $Helper->textbox("fil_txtbusquedaNombreTienda",++$index,"",150,200,$TipoTxt->texto,"","","","textoInput") . '</td>
                    </tr>          
                    <tr>
                    	<td style="padding-left:5px" class="textoInput">Direcci&oacute;n</td>
                    	<td colspan="5">' . $Helper->textbox("fil_txtbusquedaDireccionCliente",++$index,"",128,300,$TipoTxt->texto,"","","","textoInput") . '</td>
                    </tr>
           			<tr>
           				<td style="padding-left:5px" class="textoInput">Pais</td>
           				<td>' . $Helper->combo_pais("fil_cmbBusquedapaisDireccionCliente",++$index,"2195","Cargar_Combo('general','Combo_Departamentos','fil_cmbBusquedadepartamentoDireccionCliente','fil_cmbBusquedapaisDireccionCliente','fil_cmbBusquedadepartamentoDireccionCliente');") . '</td>
           				<td style="padding-left:5px" class="textoInput">Departamento</td>
           				<td>' . $Helper->combo_departamento("fil_cmbBusquedadepartamentoDireccionCliente",++$index,"","Cargar_Combo('general','Combo_Provincias','fil_cmbBusquedaprovinciaDireccionCliente','fil_cmbBusquedadepartamentoDireccionCliente','fil_cmbBusquedaprovinciaDireccionCliente');") . '</td>                        
                    </tr>
                   	<tr>
                   		<td style="padding-left:5px" class="textoInput">Provincia</td>
                        <td>' . $Helper->combo_provincia_x_departamento("fil_cmbBusquedaprovinciaDireccionCliente",++$index,"-1","","Cargar_Combo('general','Combo_Distritos','fil_cmbBusquedadistritoDireccionCliente','fil_cmbBusquedaprovinciaDireccionCliente','fil_cmbBusquedadistritoDireccionCliente')") . '</td>
                        <td style="padding-left:5px" class="textoInput">Distrito</td>
                        <td>' . $Helper->combo_distrito_x_provincia("fil_cmbBusquedadistritoDireccionCliente",++$index,"-1","","") . '</td>
                        <td style="padding-left:5px"><input type="button" id="btnBuscarDireccionesCliente" value="Buscar" class="textoInput" onclick="Buscar_Grilla(\'crm\',\'Grilla_Listar_Direcciones_Cliente\',\'tbl_listardireccionescliente\',\'\',\'div_Direcciones_Cliente\')" /></td>
                    </tr>          
                </table>
            </fieldset>';
         return $html;
      }
  function Grilla_Listar_Direcciones_Cliente($idcli,$idtipoubic,$codigo,$nombre,$dir,$idpais,$iddpto,$idprov,$iddist){
         $Obj=new mysqlhelper;
         $sqlComplete="";
         $sql ="SELECT 		dircli.sgo_int_direccion as param,
				        	tpodir.sgo_vch_descripcion as Tipo,
				        	dircli.sgo_vch_codigotienda as 'Codigo',
				        	dircli.sgo_vch_nombretienda as 'Nombre',
				        	dircli.sgo_vch_nombretiendaalias as 'Alias',
				        	dircli.sgo_vch_direccion as Direccion,
				        	(
								select 	concat(trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_vch_pais = ubi.sgo_vch_pais and sgo_vch_dpto = '00' and sgo_vch_prov='00' and sgo_vch_dist='00')),' - ',
											trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_vch_pais = ubi.sgo_vch_pais and sgo_vch_dpto = ubi.sgo_vch_dpto and sgo_vch_prov='00' and sgo_vch_dist='00')), ' - ',
											trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_vch_pais = ubi.sgo_vch_pais and sgo_vch_dpto = ubi.sgo_vch_dpto and sgo_vch_prov=ubi.sgo_vch_prov and sgo_vch_dist='00')), ' - ',
											trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_vch_pais = ubi.sgo_vch_pais and sgo_vch_dpto = ubi.sgo_vch_dpto and sgo_vch_prov=ubi.sgo_vch_prov and sgo_vch_dist=ubi.sgo_vch_dist)))
								from 		tbl_sgo_ubigeo ubi
								where sgo_int_ubigeo = dircli.sgo_int_ubigeo
							) as Ubigeo				       		
				FROM 		tbl_sgo_direccioncliente dircli
				INNER	JOIN tbl_sgo_cliente cli
				on		dircli.sgo_int_cliente = cli.sgo_int_cliente
				INNER 	JOIN tbl_sgo_tipodireccion tpodir 
				on 		tpodir.sgo_int_tipodireccion = dircli.sgo_int_tipodireccion 
				inner	join tbl_sgo_ubigeo ubi
				on		dircli.sgo_int_ubigeo = ubi.sgo_int_ubigeo";
         
         $where = $Obj->sql_where("WHERE cli.sgo_int_cliente=@p1 and tpodir.sgo_int_tipodireccion=@p2 and dircli.sgo_vch_codigotienda like '%@p3%' and dircli.sgo_vch_nombretienda like '%@p4%' and dircli.sgo_vch_direccion like '%@p5%'",                  
         $idcli . '|' . $idtipoubic . '|' . $codigo . '|' . $nombre . '|' . $dir . '|' . $iddpto . '|' . $idprov . '|' . $iddist . '|' . $idpais);
         $orderby=" ORDER BY Ubigeo ";
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql." ".$where." ".$orderby),"PopUp('crm','PopUp_Mant_Direccion','','" . $idcli . "|","","PopUp('crm','PopUp_Mant_Direccion','','" . $idcli . "|","PopUp('crm','Confirma_Eliminar_Direccion_Cliente','','" . $idcli . "|",null, array(), array(), array(),20,null);
      }
      function PopUp_Mant_Direccion($prm){
         $Obj=new mysqlhelper;
         $Helper=new htmlhelper;
         $TipoTxt=new TipoTextBox;
         $valor=explode('|',$prm);$index=0;
         $direccion=""; $tipoubic="";$codigo="";$nombre="";$dist="";$prov="";$dpto="";$pais="";$nombrealias="";
         $result = $Obj->consulta("
         		SELECT 	dircli.sgo_vch_direccion,
						dircli.sgo_int_tipodireccion,
						dircli.sgo_vch_codigotienda,
			         	dircli.sgo_vch_nombretienda,
						dircli.sgo_vch_nombretiendaalias,
						(select sgo_int_ubigeo from tbl_sgo_ubigeo where sgo_vch_pais = ubi.sgo_vch_pais and sgo_vch_dpto = '00' and sgo_vch_prov='00' and sgo_vch_dist='00') as idubipais,
						(select sgo_int_ubigeo from tbl_sgo_ubigeo where sgo_vch_pais = ubi.sgo_vch_pais and sgo_vch_dpto = ubi.sgo_vch_dpto and sgo_vch_prov='00' and sgo_vch_dist='00') as idubidpto,
						(select sgo_int_ubigeo from tbl_sgo_ubigeo where sgo_vch_pais = ubi.sgo_vch_pais and sgo_vch_dpto = ubi.sgo_vch_dpto and sgo_vch_prov=ubi.sgo_vch_prov and sgo_vch_dist='00') as idubiprov,
						(select sgo_int_ubigeo from tbl_sgo_ubigeo where sgo_vch_pais = ubi.sgo_vch_pais and sgo_vch_dpto = ubi.sgo_vch_dpto and sgo_vch_prov=ubi.sgo_vch_prov and sgo_vch_dist=ubi.sgo_vch_dist) as idubidist			
				FROM 	tbl_sgo_direccioncliente dircli
				INNER	JOIN tbl_sgo_ubigeo ubi
				on		dircli.sgo_int_ubigeo = ubi.sgo_int_ubigeo
				WHERE 	sgo_int_direccion = " . $valor[1]);
         while ($row = mysqli_fetch_array($result))
         {
            $direccion=$row["sgo_vch_direccion"];$tipoubic=$row["sgo_int_tipodireccion"];$codigo=$row["sgo_vch_codigotienda"];$nombre=$row["sgo_vch_nombretienda"];
            $nombrealias=$row["sgo_vch_nombretiendaalias"];$dist=$row["idubidist"];$prov=$row["idubiprov"];$dpto=$row["idubidpto"];$pais=$row["idubipais"];
            break;
         }
         $Val_Direccion=new InputValidacion();
         $Val_Direccion->InputValidacion('DocValue("fil_txtDireccion")!=""','Debe especificar una direccion');
         $Val_Codigo=new InputValidacion();
         $Val_Codigo->InputValidacion('DocValue("fil_txtCodigoTienda")!=""','Debe especificar el código');
         $Val_Local=new InputValidacion();
         $Val_Local->InputValidacion('DocValue("fil_txtNombreTienda")!=""','Debe especificar un nombre');
         $Val_LocalAlias=new InputValidacion();
         $Val_LocalAlias->InputValidacion('DocValue("fil_txtAliasTienda")!=""','Debe especificar un alias');
         $Val_TipoUbic=new InputValidacion();
         $Val_TipoUbic->InputValidacion('DocValue("fil_cmb_ubicacion")!=""','Debe especificar un tipo de direccion');
       
         $html='<fieldset class="textoInput"><legend align= "left">Datos del Local</legend>
         		<table id="tbl_PopUp_Mant_Direccion" width="100%" cellpadding="5" cellspacing="0">';
         $html .='<tr>
         			  <td class="textoInput">Direcci&oacute;n</td>
         			  <td>' . $Helper->textbox("fil_txtDireccion",++$index,$direccion,128,250,$TipoTxt->texto,"","","","textoInput",$Val_Direccion) . '</td>
                      <td style="padding-left:5px" class="textoInput">C&oacute;digo</td>
                      <td>' . $Helper->textbox("fil_txtCodigoTienda",++$index,$codigo,15,100,$TipoTxt->texto,"","","","") . '</td>
                      <td style="padding-left:5px" class="textoInput">Nombre</td>
                      <td>' . $Helper->textbox("fil_txtNombreTienda",++$index,$nombre,150,200,$TipoTxt->texto,"","","","textoInput") . '</td>
                 </tr>';
         $html .='<tr>
         			  <td class="textoInput">Alias</td>
         			  <td>' . $Helper->textbox("fil_txtAliasTienda",++$index,$nombrealias,50,200,$TipoTxt->texto,"","","","textoInput") . '</td>
                      <td class="textoInput">Tipo Direcci&oacute;n</td>
                      <td>' . $Helper->combo_tipoubicacion("fil_cmb_ubicacion",++$index,$tipoubic,"",$Val_TipoUbic) . '</td>
                  </tr>';
         $html .='<tr>
         			  <td style="padding-left:5px" class="textoInput">Pais</td>
         			  <td>' . $Helper->combo_pais("fil_cmbpaisDireccionCliente",++$index,($pais==""?"2195":$pais),"Cargar_Combo('general','Combo_Departamentos','fil_cmbdepartamentoDireccionCliente','fil_cmbpaisDireccionCliente','fil_cmbdepartamentoDireccionCliente');") . '</td>
         			  <td style="padding-left:5px" class="textoInput">Departamento</td>
         			  <td>' . $Helper->combo_departamento("fil_cmbdepartamentoDireccionCliente",++$index,$dpto,"Cargar_Combo('general','Combo_Provincias','fil_cmbprovinciaDireccionCliente','fil_cmbdepartamentoDireccionCliente','fil_cmbprovinciaDireccionCliente');") . '</td>
         		  </tr>
         		  <tr>
                  	  <td style="padding-left:5px" class="textoInput">Provincia</td>
                  	  <td>' . $Helper->combo_provincia_x_departamento("fil_cmbprovinciaDireccionCliente",++$index,$dpto,$prov,"Cargar_Combo('general','Combo_Distritos','fil_cmbdistritoDireccionCliente','fil_cmbprovinciaDireccionCliente','fil_cmbdistritoDireccionCliente')") . '</td>
                  	  <td style="padding-left:5px" class="textoInput">Distrito</td>
                  	  <td>' . $Helper->combo_distrito_x_provincia("fil_cmbdistritoDireccionCliente",++$index,$prov,$dist,"","") . '</td>
                  </tr>';
         $html .='</table></fieldset>';
         return $Helper->PopUp("PopUp_Mant_Direccion",($valor[1]=="0"?"Nuevo":"Actualizar") . " Local",650,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Direccion_Cliente','tbl_PopUp_Mant_Direccion','" . $prm . "')",""));
      }
      function Mant_Direccion_Cliente($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm); $resp=0;$sql="";
          if($valor[1]!=0){
          	$sql="UPDATE 	tbl_sgo_direccioncliente
                                        		SET 	sgo_vch_direccion='" . $valor[2] . "',
                                        				sgo_vch_codigotienda='" . $valor[3] . "',
                                        				sgo_vch_nombretienda='" . $valor[4] . "' ,
                                        				sgo_vch_nombretiendaalias='" . $valor[5] ."', 
                                        				sgo_int_tipodireccion=" . $valor[6] . ",
                                        				sgo_int_ubigeo=" . ($valor[10]!=""?$valor[10]:$valor[8]) . "
                                        WHERE sgo_int_direccion=" . $valor[1];
          	$resp=$Obj->execute($sql);
          }
          else {
          	$sql = "INSERT INTO tbl_sgo_direccioncliente
                             (sgo_vch_direccion,sgo_vch_codigotienda,sgo_vch_nombretienda,sgo_vch_nombretiendaalias,sgo_int_tipodireccion,sgo_int_ubigeo,sgo_int_cliente)
                             VALUES ('" . $valor[2] . "','" . $valor[3] . "','" . $valor[4] . "','" . $valor[5] . "'," . $valor[6] . "," . ($valor[10]!=""?$valor[10]:$valor[8]) . "," . $valor[0] . ")";
          	$resp=$Obj->execute($sql);
          }
          if($resp!=-1) return "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Direcciones_Cliente','','" . $valor[0] . "','div_Direcciones_Cliente');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
      function Confirma_Eliminar_Direccion_Cliente($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT sgo_vch_direccion FROM tbl_sgo_direccioncliente WHERE sgo_int_direccion=" . $valor[1]);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  echo $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la dirección ' . $row["sgo_vch_direccion"] . '?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Direccion_Cliente','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_Direccion_Cliente($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("DELETE FROM tbl_sgo_direccioncliente WHERE sgo_int_direccion=" . $valor[1])!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Direcciones_Cliente','','" . $valor[0] . "','div_Direcciones_Cliente');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }

/****************************************************************TAB CONTACTOS************************************************************************/
      function Tab_Contactos_Cliente($prm){
         $html='<table cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                      <td class="pag_tab_zona_sup">' . $this->Filtros_Listar_Contactos_Cliente($prm) . '</td>
                  </tr>
                  <tr>
                      <td id="div_Contactos_Cliente" class="pag_tab_zona_inf">' . $this->Grilla_Listar_Contactos_Cliente($prm,'','','','','','','') . '</td>
                  </tr>
                </table>';
         return $html;
      }
      function Filtros_Listar_Contactos_Cliente($prm){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=0;
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>
                <table id="tbl_listarcontactoscliente" border="0" cellpadding="3" cellspacing="0" width="990px">
                    <tr>
                        <td class="textoInput">Cargo</td><td>' . $Helper->hidden("fil_id",++$index,$prm) . $Helper->textbox("fil_txtBusquedaCargoContacto",++$index,"",128,200,$TipoTxt->texto,"","","","textoInput") . '</td>
                        <td style="padding-left:5px" class="textoInput">Nombre</td><td>' . $Helper->textbox("fil_txtBusquedaNombreContacto",++$index,"",128,300,$TipoTxt->texto,"","","","textoInput") . '</td>
                        <td style="padding-left:5px" class="textoInput">Tel&eacute;fono</td><td>' . $Helper->textbox("fil_txtBusquedaTelefonoContacto",++$index,"",128,200,$TipoTxt->texto,"","","","textoInput") . '</td>
                    </tr>
                    <tr>
                        <td class="textoInput">Celular</td><td>' . $Helper->textbox("fil_txtBusquedaCelularContacto",++$index,"",128,200,$TipoTxt->texto,"","","","textoInput") . '</td>
                        <td style="padding-left:5px" class="textoInput">Cumplea&ntilde;os</td><td>' . $Helper->combo_dias("fil_cmbDiasContacto",++$index,"","") . '&nbsp;/&nbsp;' . $Helper->combo_meses("fil_cmbMesesContacto",++$index,"","") . '</td>
                        <td style="padding-left:5px" class="textoInput">Email</td><td>' . $Helper->textbox("fil_txtBusquedaEmailContacto",++$index,"",128,200,$TipoTxt->texto,"","","","textoInput") . '</td>
                        <td style="padding-left:5px"><input type="button" id="btnBuscarDireccionesCliente" value="Buscar" class="textoInput" onclick="Buscar_Grilla(\'crm\',\'Grilla_Listar_Contactos_Cliente\',\'tbl_listarcontactoscliente\',\'\',\'div_Contactos_Cliente\')" /></td>
                    </tr>
                </table>
            </fieldset>';//<script type="text/javascript">$(".chzn-select").chosen();</script>';
          return $html;
      }
      function Grilla_Listar_Contactos_Cliente($idcli,$cargo,$nombre,$telefono,$celular,$mes,$anio,$email){
      	 $objlog=new loghelper;
         $Obj=new mysqlhelper;
         $sql ="SELECT concli.sgo_int_contacto as param,
              concli.sgo_vch_cargo as Cargo,
              concli.sgo_vch_nombre as Nombre,
              concli.sgo_vch_telefonofijo as Telefono,
              concli.sgo_vch_celular as Celular,
              Concat(LPAD(concli.sgo_int_diacumpleanos,2,'0'),'/',LPAD(concli.sgo_int_mescumpleanos,2,'0')) as Cumpleanos,
              concli.sgo_vch_correoelectronico as Email
              FROM tbl_sgo_contactocliente concli
              INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=concli.sgo_int_cliente
         	  WHERE 1=1";
         	  if($idcli!=0)
         	  	$sql.= " and cli.sgo_int_cliente=". $idcli;   
         	  if($cargo!="")
         	  	$sql.= " and concli.sgo_vch_cargo like '%".$cargo."%'";
         	  if($nombre!="")	  
         	  	$sql.= " and concli.sgo_vch_nombre like '%".$nombre."%'";
         	  if($telefono!="")	  
         	  	$sql.= " and concli.sgo_vch_telefonofijo like '%".$telefono."%'";
         	  if($celular!="")  
         	  	$sql.= " and concli.sgo_vch_celular like '%".$celular."%'";
         	  if($mes!="")
         	  	$sql.= " and concli.sgo_int_diacumpleanos=".$mes;
         	  if($anio!="")
         	  	$sql.= " and concli.sgo_int_mescumpleanos = ".$anio;
         	  if($email!="")	
         	  	$sql.= " and concli.sgo_vch_correoelectronico like '%".$email."%'";
          $objlog->log($sql);
         $Helper=new htmlhelper;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('crm','PopUp_Mant_Contacto','','" . $idcli . "|","","PopUp('crm','PopUp_Mant_Contacto','','" . $idcli . "|","PopUp('crm','Confirma_Eliminar_Contacto_Cliente','','" . $idcli . "|",null, array(), array(), array(),20,null);
      }
      function PopUp_Mant_Contacto($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$valor=explode('|',$prm);$index=0;
         $cargo=""; $nombre="";$telefono="";$celular="";$mes="";$dia="";$email="";
         $result = $Obj->consulta("SELECT sgo_vch_cargo,sgo_vch_nombre,	sgo_vch_telefonofijo,sgo_vch_celular,sgo_int_diacumpleanos,sgo_int_mescumpleanos,sgo_vch_correoelectronico FROM tbl_sgo_contactocliente WHERE sgo_int_contacto = " . $valor[1]);
         while ($row = mysqli_fetch_array($result))
         {
            $cargo=$row["sgo_vch_cargo"];$nombre=$row["sgo_vch_nombre"];$telefono=$row["sgo_vch_telefonofijo"];$celular=$row["sgo_vch_celular"];$mes=$row["sgo_int_mescumpleanos"];$dia=$row["sgo_int_diacumpleanos"];$email=$row["sgo_vch_correoelectronico"];
            break;
         }
         $Val_Cargo=new InputValidacion();
         $Val_Cargo->InputValidacion('DocValue("fil_txtCargo")!=""','Debe especificar un cargo');
         $Val_Nombre=new InputValidacion();
         $Val_Nombre->InputValidacion('DocValue("fil_txtNombre")!=""','Debe especificar un nombre');
         $Val_Email=new InputValidacion();
         $Val_Email->InputValidacion('DocValue("fil_txtDireccion")!=""','Debe especificar una direccion de correo');
         $html='<table id="tbl_PopUp_Mant_Contacto" width="100%" cellpadding="5" cellspacing="0">';
         $html .='<tr><td class="textoInput">Cargo</td><td>' . $Helper->textbox("fil_txtCargo",++$index,$cargo,128,250,$TipoTxt->texto,"","","","textoInput",$Val_Cargo) . '</td>';
              $html .='<td class="textoInput">Nombre</td><td>' . $Helper->textbox("fil_txtNombre",++$index,$nombre,128,250,$TipoTxt->texto,"","","","textoInput",$Val_Nombre) . '</td></tr>';
         $html .='<tr><td class="textoInput">Telefono</td><td>' . $Helper->textbox("fil_txtTelefono",++$index,$telefono,16,100,$TipoTxt->texto,"","","","textoInput") . '</td>';
              $html .='<td class="textoInput">Celular</td><td>' . $Helper->textbox("fil_txtCelular",++$index,$celular,16,100,$TipoTxt->texto,"","","","textoInput") . '</td></tr>';
         $html .='<tr><td class="textoInput">Cumplea&ntilde;os</td><td class="textoInput">' . $Helper->combo_dias("fil_cmbDias",++$index,$dia,"") . '&nbsp;/&nbsp;' . $Helper->combo_meses("fil_cmbMeses",++$index,$mes,"") . '</td>';
              $html .='<td class="textoInput">Email</td><td>' . $Helper->textbox("fil_txtDireccion",++$index,$email,128,250,$TipoTxt->texto,"","","","textoInput","") . '</td></tr>';
         $html .='</table>';
//          $html .='</table><script>$(".chzn-select").chosen();</script>';
         return $Helper->PopUp("PopUp_Mant_Contacto",($valor[1]=="0"?"Nuevo":"Actualizar") . " Contacto",650,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Contacto_Cliente','tbl_PopUp_Mant_Contacto','" . $prm . "')",""));
      }
      function Mant_Contacto_Cliente($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm); $resp=0;
          if($valor[1]!=0) $resp=$Obj->execute("UPDATE tbl_sgo_contactocliente
                                        SET sgo_vch_cargo='" . $valor[2] . "',sgo_vch_nombre='" . $valor[3] . "',sgo_vch_telefonofijo='" . $valor[4] . "',
                                        sgo_vch_celular='" . $valor[5] . "',sgo_int_diacumpleanos=" . $valor[6] . ",sgo_int_mescumpleanos=" . $valor[7] . ",
                                        sgo_vch_correoelectronico='" . $valor[8] . "'
                                        WHERE sgo_int_contacto=" . $valor[1]);
          else $resp=$Obj->execute("INSERT INTO tbl_sgo_contactocliente
                             (sgo_vch_cargo,sgo_vch_nombre,sgo_vch_telefonofijo,sgo_vch_celular,sgo_int_diacumpleanos,sgo_int_mescumpleanos,sgo_vch_correoelectronico,sgo_int_cliente)
                             VALUES ('" . $valor[2] . "','" . $valor[3] . "','" . $valor[4] . "','" . $valor[5] . "'," . $valor[6] . "," . $valor[7] . ",'" . $valor[8] . "'," . $valor[0] . ")");
//          $x = "INSERT INTO tbl_sgo_direccioncliente (sgo_vch_direccion,sgo_int_tipodireccion,sgo_int_ubigeo,sgo_int_cliente) VALUES ('" . $valor[2] . "'," . $valor[3] . "," . $valor[4] . "," . $valor[0] . ")";
//          echo '<script>alert("' . $x . '");</script>';
          if($resp!=-1) return "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Contactos_Cliente','','" . $valor[0] . "','div_Contactos_Cliente');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
      function Confirma_Eliminar_Contacto_Cliente($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT sgo_vch_nombre FROM tbl_sgo_contactocliente WHERE sgo_int_contacto=" . $valor[1]);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar al contacto ' . $row["sgo_vch_nombre"] . '?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Contacto_Cliente','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_Contacto_Cliente($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("DELETE FROM tbl_sgo_contactocliente WHERE sgo_int_contacto=" . $valor[1])!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Contactos_Cliente','','" . $valor[0] . "','div_Contactos_Cliente');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }

/****************************************************************TAB DATOS FINANCIEROS************************************************************************/
      function Tab_DatosFinancieros($prm){
         $Obj=new mysqlhelper; $Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;
         $formapago="";$modalidadpago="";$diaspago="";$obs="";
         $result = $Obj->consulta("SELECT sgo_int_tipoformapago,sgo_int_modalidad,sgo_int_diascredito,sgo_txt_observaciones FROM tbl_sgo_datosfinancieroscliente WHERE sgo_int_cliente = " . $prm);
         while ($row = mysqli_fetch_array($result))
         {
            $formapago=$row["sgo_int_tipoformapago"];$modalidadpago=$row["sgo_int_modalidad"];$diaspago=$row["sgo_int_diascredito"];$obs=$row["sgo_txt_observaciones"];
            break;
         }
         $Val_FormaPago=new InputValidacion();
         $Val_FormaPago->InputValidacion('DocValue("fil_cmb_tipoformapago")!=""','Debe especificar una forma de pago');
         $Val_ModPago=new InputValidacion();
         $Val_ModPago->InputValidacion('DocValue("fil_cmb_modalidadpago")!=""','Debe especificar una modalidad de pago');
         $Val_DiasPago=new InputValidacion();
         $Val_DiasPago->InputValidacion('DocValue("fil_txtDiasPago")!=""','Debe especificar los dias de pago');
         $html='<table id="tbl_Tab_DatosFinancieros" width="100%" cellpadding="5" cellspacing="0">';
            $html .='<tr><td class="textoInput">Forma de Pago</td><td>' . $Helper->combo_tipoformapago("fil_cmb_tipoformapago",++$index,$formapago,"",$Val_FormaPago) . '</td></tr>';
            $html .='<tr><td class="textoInput">Modalidad de Pago</td><td>' . $Helper->combo_modalidadpago("fil_cmb_modalidadpago",++$index,$modalidadpago,"",$Val_ModPago) . '</td></tr>';
            $html .='<tr><td class="textoInput">D&iacute;as de Credito</td><td>' . $Helper->textbox("fil_txtDiasPago",++$index,$diaspago,128,200,$TipoTxt->numerico,"","","","textoInput",$Val_DiasPago) . '</td></tr>';
            $html .='<tr><td class="textoInput">Observaciones</td><td>' . $Helper->textarea("fil_txtObservaciones",++$index,$obs,140,5,"","","","textoInput") . '</td></tr>';
            $html .='<tr><td colspan="2" class="right">' . $Helper->button("","Grabar",70,"Operacion('crm','Actualizar_DatosFinancieros','tbl_Tab_DatosFinancieros','" . $prm . "')","textoInput") . '</td></tr>';
            $html .='</table>';
//            $html .='</table><script>$(".chzn-select").chosen();</script>';
         return $html;
      }
      function Actualizar_DatosFinancieros($prm){
          $Obj=new mysqlhelper;$resp=0;$valor=explode('|',$prm);
          if(mysqli_num_rows($Obj->consulta("SELECT sgo_int_cliente FROM tbl_sgo_datosfinancieroscliente WHERE sgo_int_cliente=" . $valor[0]))>0)
              $resp=$Obj->execute("UPDATE tbl_sgo_datosfinancieroscliente
                            SET sgo_int_tipoformapago=" . $valor[1] . ",sgo_int_modalidad=" . $valor[2] . ",
                            sgo_int_diascredito=" . $valor[3] . ",sgo_txt_observaciones='" . $valor[4] . "'
                            WHERE sgo_int_cliente=" . $valor[0]);
          else $resp=$Obj->execute("INSERT INTO  tbl_sgo_datosfinancieroscliente
                                    (sgo_int_cliente,sgo_int_tipoformapago, sgo_int_modalidad, sgo_int_diascredito, sgo_txt_observaciones)
                                    VALUES (" . $valor[0] . "," . $valor[1] . "," . $valor[2] . "," . $valor[3] . ",'" . $valor[4] . "')");

          if($resp!=-1) return '<script>Operacion_Result(true);</script>';
          else return '<script>Operacion_Result(false);</script>';
      }

/***************************************************************TAB CREDITO************************************************************************/
      function Tab_Credito_Cliente($prm){
         $html='<table cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                      <td id="div_Credito_Cliente" class="pag_tab_zona_sup">' . $this->Grilla_Listar_Credito_Cliente($prm) . '</td>
                  </tr>
                </table>';
         return $html;
      }
      function Grilla_Listar_Credito_Cliente($prm){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT crecli.sgo_int_creditocliente as param,
              mon.sgo_vch_simbolo as Moneda,
              crecli.sgo_dec_credito as 'Línea',
              (crecli.sgo_dec_credito - (select ifnull(sum(sgo_dec_saldo),0) from tbl_sgo_documentoporcobrar where sgo_int_persona = ".$prm.")) as 'Saldo',
              CASE crecli.sgo_bit_activo WHEN 1 THEN 'Activo' ELSE 'Inactivo' END as Estado
              FROM tbl_sgo_creditocliente crecli
              INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=crecli.sgo_int_cliente
              INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=cli.sgo_int_cliente
              INNER JOIN tbl_sgo_moneda mon on mon.sgo_int_moneda=crecli.sgo_int_moneda
              WHERE crecli.sgo_int_cliente=" . $prm;
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('crm','PopUp_Mant_Credito','','" . $prm . "|","","PopUp('crm','PopUp_Mant_Credito','','" . $prm . "|","",null, array(), array(), array(),20,null);
      }
      function PopUp_Mant_Credito($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$index=0;$valor=explode('|',$prm);
          $moneda="";$credito="";$estado="1";$cliente=$valor[0];
          $result = $Obj->consulta("SELECT sgo_int_moneda,sgo_dec_credito,sgo_bit_activo FROM tbl_sgo_creditocliente WHERE sgo_int_creditocliente = " . $valor[1]);
          while ($row = mysqli_fetch_array($result))
          {
             $moneda=$row["sgo_int_moneda"];$credito=$row["sgo_dec_credito"];$estado=$row["sgo_bit_activo"];
             break;
          }
          $Val_Moneda=new InputValidacion();
          $Val_Moneda->InputValidacion('DocValue("fil_cmbmoneda")!=""','Debe especificar la moneda');
          $Val_Credito=new InputValidacion();
          $Val_Credito->InputValidacion('DocValue("fil_txtcredito")!="" && parseFloat(DocValue("fil_txtcredito"))>0','Debe especificar el monto del crédito mayor o igual a 0');
          $Val_Estado=new InputValidacion();
          $Val_Estado->InputValidacion('DocValue("fil_cmbestado")!=""','Debe especificar el estado de la línea');
          $inputs=array(
            "Moneda" => $Helper->combo_moneda_alias("fil_cmbmoneda",$index,$moneda,"",$Val_Moneda,($valor[1]=="0"?false:true)),
            "Crédito" => $Helper->textbox("fil_txtcredito",++$index,$credito,10,100,$TipoTxt->decimal,"","","","",$Val_Credito),
            "Estado" => $Helper->combo_estado("fil_cmbestado",++$index,$estado,$Val_Estado),
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_PopUp_Mant_Credito",$inputs,$buttons,3,550,"","");
         return $Helper->PopUp("PopUp_Mant_Credito",($prm=="0"?"Nueva":"Actualizar") . " Línea de Crédito",560,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Credito_Cliente','tbl_PopUp_Mant_Credito','" . $prm . "')",""));
      }
      function Mant_Credito_Cliente($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm); $resp=0;
          if($valor[1]!=0) $resp=$Obj->execute("UPDATE tbl_sgo_creditocliente
                                        SET sgo_dec_credito=" . $valor[3] . ",sgo_bit_activo=" . $valor[4] . "
                                        WHERE sgo_int_creditocliente=" . $valor[1]);
          else $resp=$Obj->execute("INSERT INTO tbl_sgo_creditocliente
                             (sgo_int_cliente, sgo_int_moneda,sgo_dec_credito,sgo_dec_saldo,sgo_dat_fecharegistro,sgo_bit_activo)
                             VALUES (" . $valor[0] . "," . $valor[2] . "," . $valor[3] . "," . $valor[3] . ",'" . date("Y-m-d H:i") . "',1)");

          if($resp!=-1) return "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Credito_Cliente','','" . $valor[0] . "','div_Credito_Cliente');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
      function Confirma_Eliminar_Credito_Cliente($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          echo $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de anular la línea de crédito ' . $this->Obtener_Nombre_Credito($valor[1]) . '?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Credito_Cliente','','" . $prm . "')"));
      }
      function Eliminar_Credito_Cliente($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("UPDATE tbl_sgo_creditocliente SET sgo_bit_activo=0 WHERE sgo_int_creditocliente=" . $valor[1])!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Credito_Cliente','','" . $valor[0] . "','div_Credito_Cliente');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }

/***************************************************************TAB VISITAS************************************************************************/
      function Tab_Visitas_Cliente($prm){
         $html='<table cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                      <td class="pag_tab_zona_sup">' . $this->Filtros_Listar_Visitas_Cliente($prm) . '</td>
                  </tr>
                  <tr>
                      <td id="div_Visitas_Cliente" class="pag_tab_zona_inf">' . $this->Grilla_Listar_Visitas_Cliente($prm,'','','','') . '</td>
                  </tr>
                </table>';
         return $html;
      }
      function Filtros_Listar_Visitas_Cliente($prm){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=0;
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>
                <table id="tbl_listarvisitascliente" border="0" cellpadding="3" cellspacing="0" width="990px">
                    <tr>
                        <td class="textoInput">Nombre Comercial</td><td>' . $Helper->hidden("fil_id",++$index,$prm) . $Helper->textbox("fil_txtBusquedaNombreComercial",++$index,"",128,250,$TipoTxt->texto,"","","","textoInput") . '</td>
                        <td class="textoInput">Estado</td><td>' . $Helper->combo_estadovisita("fil_cmbtBusquedaEstadoVisita",++$index,"","") . '</td>
                    </tr>
                    <tr>
                        <td style="padding-left:5px" class="textoInput">Desde</td><td>' . $Helper->textdate("fil_txtBusquedaDesdeVisita",++$index,"",false,$TipoDate->fecha,80,"","textoInput") . '</td>
                        <td style="padding-left:5px" class="textoInput">Hasta</td><td>' . $Helper->textdate("fil_txtBusquedaHastaVisita",++$index,"",false,$TipoDate->fecha,80,"","textoInput") . '</td>
                        <td style="padding-left:5px"><input type="button" id="btnBuscarVisitasCliente" value="Buscar" class="textoInput" onclick="Buscar_Grilla(\'crm\',\'Grilla_Listar_Visitas_Cliente\',\'tbl_listarvisitascliente\',\'\',\'div_Visitas_Cliente\')" /></td>
                    </tr>
                </table>
            </fieldset>';//<script type="text/javascript">$(".chzn-select").chosen();</script>';
          return $html;
      }
      function Grilla_Listar_Visitas_Cliente($idcli,$cliente,$estado,$desde,$hasta){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT viscli.sgo_int_visita as param,
              per.sgo_vch_alias as Cliente,
              viscli.sgo_vch_lugarcita as Lugar,
              viscli.sgo_dat_fechaprogramada as 'Fecha Programada',
              estvis.sgo_vch_descripcion as Estado,
              concli.sgo_vch_nombre as Contacto,
              concli.sgo_vch_celular as Celular
              FROM tbl_sgo_visita viscli
              INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=viscli.sgo_int_cliente
              INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=cli.sgo_int_cliente
              INNER JOIN tbl_sgo_estadovisita estvis on estvis.sgo_int_estadovisita = viscli.sgo_int_estadovisita
              INNER JOIN tbl_sgo_contactocliente concli on concli.sgo_int_contacto=viscli.sgo_int_contacto";
         $where = $Obj->sql_where("WHERE per.sgo_int_persona=@p1 and per.sgo_vch_alias like '%@p2%' and estvis.sgo_vch_descripcion=@p3 and (viscli.sgo_dat_fechaprogramada between '@p4 00:00' and '@p5 23:59')",
         $idcli . '|' . $cliente . '|' . $estado . '|' . $Helper->convertir_fecha_ingles($desde) . '|' . $Helper->convertir_fecha_ingles($hasta));
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Visita','','" . $idcli . "|","","PopUp('crm','PopUp_Mant_Visita','','" . $idcli . "|","PopUp('crm','Confirma_Eliminar_Visita_Cliente','','" . $idcli . "|",null, array(), array(), array(),20,null);
      }
      function PopUp_Mant_Visita($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate;$valor=explode('|',$prm);$index=0;
          $html="";$content_tabs = array(); $titulo_tabs = array();

          $lugar="";$fecha="";$estado="";$contacto="";
          $result = $Obj->consulta("SELECT viscli.sgo_vch_lugarcita,DATE_FORMAT(viscli.sgo_dat_fechaprogramada, '%d/%m/%Y %H:%i') as sgo_dat_fechaprogramada,estvis.sgo_int_estadovisita,viscli.sgo_int_contacto FROM tbl_sgo_visita viscli INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=viscli.sgo_int_cliente INNER JOIN tbl_sgo_estadovisita estvis on estvis.sgo_int_estadovisita = viscli.sgo_int_estadovisita LEFT JOIN tbl_sgo_contactocliente concli on concli.sgo_int_cliente=viscli.sgo_int_cliente WHERE viscli.sgo_int_visita = " . $valor[1]);
          while ($row = mysqli_fetch_array($result))
          {
             $lugar=$row["sgo_vch_lugarcita"];$fecha=$row["sgo_dat_fechaprogramada"];$estado=$row["sgo_int_estadovisita"];$contacto=$row["sgo_int_contacto"];
             break;
          }
          $Val_Lugar=new InputValidacion();
          $Val_Lugar->InputValidacion('DocValue("fil_txtLugarCita")!=""','Debe especificar el lugar de cita');
          $Val_Fecha=new InputValidacion();
          $Val_Fecha->InputValidacion('DocValue("fil_txtFechaProgramada")!=""','Debe especificar la fecha programada');
          $Val_Estado=new InputValidacion();
          $Val_Estado->InputValidacion('DocValue("fil_cmb_EstadoVisita")!=""','Debe especificar el estado de la visita');
          $Val_Contacto=new InputValidacion();
          $Val_Contacto->InputValidacion('DocValue("fil_cmbContactoVisita")!=""','Debe especificar el contacto de la visita');
          $html='<table id="tbl_PopUp_Mant_Visita" width="850px" cellpadding="5" cellspacing="0">';
          $html .='<tr><td class="textoInput">Lugar Cita</td><td>' . $Helper->textbox("fil_txtLugarCita",++$index,$lugar,128,250,$TipoTxt->texto,"","","","textoInput",$Val_Lugar) . '</td>';
               $html .='<td class="textoInput">Fecha Programada</td><td>' . $Helper->textdate("fil_txtFechaProgramada",++$index,$fecha,false,$TipoDate->fechahora,120,"","textoInput",$Val_Fecha) . '</td></tr>';
          $html .='<tr><td class="textoInput">Estado Visita</td><td>' . $Helper->combo_estadovisita("fil_cmb_EstadoVisita",++$index,$estado,"",$Val_Estado) . '</td>';
               $html .='<td class="textoInput">Contacto</td><td>' . $Helper->combo_contactos_x_cliente("fil_cmbContactoVisita",++$index,$valor[0],$contacto,"",$Val_Contacto) . '</td></tr>';
          $html .='</table>';
          $content_tabs[0] ="<br/>" . $html;
          $titulo_tabs[0]="Visitas";

          if($valor[1]!=0){
             $sql ="SELECT vistar.sgo_int_tarea as param,
                  vistar.sgo_vch_descripcion as Tarea,
                  DATE_FORMAT(vistar.sgo_dat_fechacomprometida,'%d/%m/%Y') as Fecha,
                  case vistar.sgo_bit_pendiente when 1 then 'Pendiente' else 'Concluido' end as Estado
                  FROM tbl_sgo_visitatarea vistar
                  INNER JOIN tbl_sgo_visita viscli on viscli.sgo_int_visita=vistar.sgo_int_visita
                  INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=viscli.sgo_int_cliente";
             $where = "WHERE cli.sgo_int_cliente=@p1 and vistar.sgo_int_visita=@p2";
             $where = $Obj->sql_where($where,$valor[0] . '|' . $valor[1]);
             $html = "<div id='div_tbl_Visita_Pendiente_Cliente'>" . $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Visita_Pendiente','','" . $prm . "|","","PopUp('crm','PopUp_Mant_Visita_Pendiente','','" . $prm . "|","PopUp('crm','Confirma_Eliminar_Visita_Pendiente_Cliente','','" . $prm . "|",850,array(), array(), array(),20,null) . "</div>";
              $content_tabs[1] ="<br/>" . $html;
              $titulo_tabs[1]="Pendientes";

             $pendiente="";$fecha="";$estado="";
             $result = $Obj->consulta("SELECT sgo_vch_descripcion,DATE_FORMAT(sgo_dat_fechacomprometida, '%d/%m/%Y') as sgo_dat_fechacomprometida,sgo_bit_pendiente FROM tbl_sgo_visitatarea WHERE sgo_int_tarea = " . $valor[1]);
             while ($row = mysqli_fetch_array($result))
             {
                $pendiente=$row["sgo_vch_descripcion"];$fecha=$row["sgo_dat_fechacomprometida"];$estado=$row["sgo_bit_pendiente"];
                break;
             }

             $sql ="SELECT visacu.sgo_int_acuerdo as param,
                    visacu.sgo_txt_descripcion as Acuerdo,
                    DATE_FORMAT(visacu.sgo_dat_fecha,'%d/%m/%Y') as Fecha
                    FROM tbl_sgo_visitaacuerdo visacu
                    INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=visacu.sgo_int_cliente";
             $where = "WHERE cli.sgo_int_cliente=@p1 and visacu.sgo_int_visita=@p2";
             $where = $Obj->sql_where($where,$valor[0] . '|' . $valor[1]);
             $html ="<div id='div_tbl_Visita_Acuerdo_Cliente'>" . $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Visita_Acuerdo','','" . $prm . "|","","PopUp('crm','PopUp_Mant_Visita_Acuerdo','','" . $prm . "|","PopUp('crm','Confirma_Eliminar_Visita_Acuerdo_Cliente','','" . $prm . "|",850, array(), array(), array(),20,null) . "</div>";

              $content_tabs[2] ="<br/>" . $html;
              $titulo_tabs[2]="Acuerdos";
          }
          $html = $Helper->Crear_Tabs("Tabs_Mant_Visita_Cliente",$content_tabs, $titulo_tabs, "s","");

          return $Helper->PopUp("PopUp_Mant_Visita",($valor[1]=="0"?"Nueva":"Actualizar") . " Visita",860,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Visita_Cliente','tbl_PopUp_Mant_Visita','" . $prm . "')",""));
      }
      function Mant_Visita_Cliente($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm); $resp=0;
          if($valor[1]!=0) $resp=$Obj->execute("UPDATE tbl_sgo_visita
                                        SET sgo_vch_lugarcita='" . $valor[2] . "',sgo_dat_fechaprogramada='" . $Helper->convertir_fecha_ingles($valor[3]) . "',sgo_int_estadovisita=" . $valor[4] . ",
                                        sgo_int_contacto=" . $valor[5] . "
                                        WHERE sgo_int_visita=" . $valor[1]);
          else $resp=$Obj->execute("INSERT INTO tbl_sgo_visita
                             (sgo_vch_lugarcita, sgo_dat_fechaprogramada, sgo_int_estadovisita, sgo_int_usuario, sgo_int_contacto, sgo_int_cliente)
                             VALUES ('" . $valor[2] . "','" . $Helper->convertir_fecha_ingles($valor[3]) . "'," . $valor[4] . ",0," . $valor[5] . "," . $valor[0] . ")");
      }
      function Confirma_Eliminar_Visita_Cliente($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT sgo_vch_nombrecomercial FROM tbl_sgo_visita vis INNER JOIN tbl_sgo_cliente cli ON vis.sgo_int_cliente=cli.sgo_int_cliente WHERE sgo_int_visita=" . $valor[1]);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  echo $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la visita al cliente ' . $row["sgo_vch_nombrecomercial"] . '?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Visita_Cliente','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_Visita_Cliente($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("DELETE FROM tbl_sgo_visita WHERE sgo_int_visita=" . $valor[1])!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Visitas_Cliente','','" . $valor[0] . "','div_Visitas_Cliente');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }

/****************************************************************TAB COTIZACIONES************************************************************************/
      function Grilla_Listar_Cotizaciones_Cliente($idcli){
         $Obj=new mysqlhelper;
         $sql ="SELECT co.sgo_int_cotizacion as param, per.sgo_vch_nrodocumentoidentidad as RUC, per.sgo_vch_nombre as Cliente, Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as 'Cotización',group_concat(cod.sgo_vch_observacion) as Observaciones, COUNT(cod.sgo_int_cotizaciondetalle) as Items,FORMAT(SUM(cod.sgo_dec_cantidad * cod.sgo_dec_precio),2) AS Monto,eco.sgo_vch_descripcion as Estado,
                case co.sgo_int_estadocotizacion when 5 then '||green_check.gif' else '--' end as 'Órden de Compra'
                FROM tbl_sgo_cotizacion co
                INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=co.sgo_int_cliente
                INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=cli.sgo_int_cliente
                INNER JOIN tbl_sgo_estadocotizacion eco on eco.sgo_int_estadocotizacion=co.sgo_int_estadocotizacion
                LEFT JOIN tbl_sgo_cotizaciondetalle cod on cod.sgo_int_cotizacion=co.sgo_int_cotizacion
                WHERE cli.sgo_int_cliente=" . $idcli;
         $groupby="GROUP BY co.sgo_int_cotizacion,per.sgo_vch_nrodocumentoidentidad,per.sgo_vch_nombre,eco.sgo_vch_descripcion,co.sgo_int_estadocotizacion";
         $Helper=new htmlhelper;
//         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Cotizacion','','" . $idcli . "|","","PopUp('crm','PopUp_Mant_Cotizacion','','" . $idcli . "|","PopUp('crm','Confirma_Eliminar_Cotizacion_Cliente','','" . $idcli . "|");
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $groupby),"","","","",null, array(), array(), array(),20,null);
      }

/****************************************************************TAB ORDENES DE COMPRA************************************************************************/
      function Grilla_Listar_OrdenesServicio_Cliente($idprv){
         $Obj=new mysqlhelper;
         $sql ="SELECT oc.sgo_int_ordenservicio as param, oc.sgo_dat_fecharegistro as Registro,per.sgo_vch_nrodocumentoidentidad as RUC, per.sgo_vch_nombre as Cliente,LPAD(oc.sgo_int_cotizacion,6,'0') as 'Cotización', Concat(oc.sgo_vch_serie,'-',oc.sgo_vch_numero) as OS,COUNT(ocd.sgo_int_ordenserviciodetalle) as Items,FORMAT(SUM(ocd.sgo_dec_cantidad * ocd.sgo_dec_precio),2) AS Monto,eoc.sgo_vch_descripcion as Estado,
                case when oc.sgo_int_estado!=3 then 'SI' else 'NO' end as 'Órden de Producción'
                FROM tbl_sgo_ordenservicio oc
                INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=oc.sgo_int_cliente
                INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=cli.sgo_int_cliente
                INNER JOIN tbl_sgo_estadoordenservicio eoc on eoc.sgo_int_estadoos=oc.sgo_int_estado
                LEFT JOIN tbl_sgo_ordenserviciodetalle ocd on ocd.sgo_int_ordenservicio=oc.sgo_int_ordenservicio
                WHERE cli.sgo_int_cliente=" . $idprv;
         $groupby="GROUP BY oc.sgo_int_ordenservicio, oc.sgo_dat_fecharegistro,per.sgo_vch_nrodocumentoidentidad, per.sgo_vch_nombre,oc.sgo_int_cotizacion,oc.sgo_vch_serie,oc.sgo_vch_numero,eoc.sgo_vch_descripcion,oc.sgo_int_estado";
         $Helper=new htmlhelper;
//         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_OrdenCompra','','" . $idprv . "|","","PopUp('crm','PopUp_Mant_OrdenCompra','','" . $idprv . "|","PopUp('crm','Confirma_Eliminar_OrdenCompra_Cliente','','" . $idprv . "|");
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $groupby),"","","","",null, array(), array(), array(),20,null);
      }

/****************************************************************TAB FACTURAS************************************************************************/
      function Grilla_Listar_Facturas_Cliente($idcli){
         $Obj=new mysqlhelper;
         $sql ="SELECT		factura.sgo_int_comprobanteventa as param,concat(identidad.sgo_vch_descripcion,'-',per.sgo_vch_nrodocumentoidentidad) as 'Doc. Identidad',
							per.sgo_vch_nombre as Cliente,concat(factura.sgo_vch_serie,'-',factura.sgo_vch_numero) as 'Nro. Factura',
							factura.sgo_dat_fechaemision as 'Fecha Emision',moneda.sgo_vch_nombre as 'Moneda',factura.sgo_dec_subtotal as 'Subtotal',
							factura.sgo_dec_igv as 'IGV',factura.sgo_dec_total as 'Total',estado.sgo_vch_descripcion as 'Estado'
				FROM		tbl_sgo_comprobanteventa factura
				inner		join tbl_sgo_cliente cliente on	factura.sgo_int_cliente = cliente.sgo_int_cliente
                inner       join tbl_sgo_persona per on per.sgo_int_persona=cliente.sgo_int_cliente
				inner		join tbl_sgo_tipodocumentoidentidad identidad on per.sgo_int_tipodocumentoidentidad = identidad.sgo_int_documentoidentidad
				inner		join tbl_sgo_moneda moneda on factura.sgo_int_moneda = moneda.sgo_int_moneda
				inner		join tbl_sgo_estadocomprobante estado on factura.sgo_int_estadocomprobante = estado.sgo_int_estadocomprobante";
         $where = "WHERE cliente.sgo_int_cliente=" . $idcli;
         $Helper=new htmlhelper;
//         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Factura','','" . $idcli . "|","","PopUp('crm','PopUp_Mant_Factura','','" . $idcli . "|","PopUp('crm','Confirma_Eliminar_Factura_Cliente','','" . $idcli . "|");
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"","","","",null, array(), array(), array(),20,null);
      }

/****************************************************************TAB GUIAS************************************************************************/
      function Grilla_Listar_Guias_Cliente($idcli){
         $Obj=new mysqlhelper;
         $sql ="select 	guia.sgo_int_guiaremision as param,
						persona.sgo_vch_nombre as Cliente,
						guia.sgo_vch_serie as Serie,
						guia.sgo_vch_numero as Numero,
						guia.sgo_dat_fecha as Fecha,
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
						(	select group_concat(distinct Concat(factura.sgo_vch_serie,'-',factura.sgo_vch_numero))
							from 	tbl_sgo_comprobanteventa factura
							inner join tbl_sgo_comprobanteventadetalle facturadetalle
							on factura.sgo_int_comprobanteventa = facturadetalle.sgo_int_comprobanteventa
							where facturadetalle.sgo_int_ordenserviciodetalle in
							(
								select gdet.sgo_int_ordenserviciodetalle
								from 	tbl_sgo_guiaremisiondetalle gdet
								where	gdet.sgo_int_guiaremision  =    guia.sgo_int_guiaremision
							)) as FACTURA,
						estado.sgo_vch_descripcion as Estado,
						transportista.sgo_vch_razonsocial as Transportista
			from		tbl_sgo_guiaremision guia
			left		join tbl_sgo_cliente cliente
			on			guia.sgo_int_cliente = cliente.sgo_int_cliente
			inner		join tbl_sgo_persona persona
			on			persona.sgo_int_persona = cliente.sgo_int_cliente
			left		join tbl_sgo_estadoguiaremision estado
			on			estado.sgo_int_estadoguiaremision = guia.sgo_int_estadoguiaremision
			left		join tbl_sgo_transportista transportista
			on			transportista.sgo_int_transportista = guia.sgo_int_transportista";
         $where = "WHERE cliente.sgo_int_cliente=" . $idcli;
         $Helper=new htmlhelper;
//         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Factura','','" . $idcli . "|","","PopUp('crm','PopUp_Mant_Factura','','" . $idcli . "|","PopUp('crm','Confirma_Eliminar_Factura_Cliente','','" . $idcli . "|");
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"","","","",null, array(), array(), array(),20,null);
      }
/****************************************************************TAB ACUERDOS************************************************************************/
      function Tab_Acuerdos_Cliente($prm){
         $html='<table cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                      <td class="pag_tab_zona_sup">' . $this->Filtros_Listar_Acuerdos_Cliente($prm) . '</td>
                  </tr>
                  <tr>
                      <td id="div_Acuerdos_Cliente" class="pag_tab_zona_inf">' . $this->Grilla_Listar_Acuerdos_Cliente($prm,'','','','') . '</td>
                  </tr>
                </table>';
         return $html;
      }
      function Filtros_Listar_Acuerdos_Cliente($prm){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=0;
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>
                <table id="tbl_listaracuerdoscliente" border="0" cellpadding="3" cellspacing="0" width="400px">
                    <tr>
                        <td style="padding-left:5px" class="textoInput">Desde</td><td>' . $Helper->hidden("fil_id",++$index,$prm) . $Helper->textdate("fil_txtBusquedaDesdeAcuerdo",++$index,"",false,$TipoDate->fecha,80,"","textoInput") . '</td>
                        <td style="padding-left:5px" class="textoInput">Hasta</td><td>' . $Helper->textdate("fil_txtBusquedaHastaAcuerdo",++$index,"",false,$TipoDate->fecha,80,"","textoInput") . '</td>
                        <td style="padding-left:5px"><input type="button" id="btnBuscarAcuerdosCliente" value="Buscar" class="textoInput" onclick="Buscar_Grilla(\'crm\',\'Grilla_Listar_Acuerdos_Cliente\',\'tbl_listaracuerdoscliente\',\'\',\'div_Acuerdos_Cliente\')" /></td>
                    </tr>
                </table>
            </fieldset>';//<script type="text/javascript">$(".chzn-select").chosen();</script>';
          return $html;
      }
      function Grilla_Listar_Acuerdos_Cliente($idcli,$desde,$hasta){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT Concat(visacu.sgo_int_visita,'|',visacu.sgo_int_acuerdo) as param,
                visacu.sgo_txt_descripcion as Acuerdo,
                DATE_FORMAT(visacu.sgo_dat_fecha,'%d/%m/%Y') as Fecha
                FROM tbl_sgo_visitaacuerdo visacu
                LEFT JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=visacu.sgo_int_cliente";
         $where = "WHERE visacu.sgo_int_cliente=@p1 and visacu.sgo_dat_fecha between '@p2 00:00' and '@p3 23:59'";
         $where = $Obj->sql_where($where,$idcli . '|' . $Helper->convertir_fecha_ingles($desde) . '|' . $Helper->convertir_fecha_ingles($hasta));
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Acuerdo','','" . $idcli . "|0|","","PopUp('crm','PopUp_Mant_Acuerdo','','" . $idcli . "|","PopUp('crm','Confirma_Eliminar_Acuerdo_Cliente','','" . $idcli . "|",null, array(), array(), array(),20,null);
      }
      function Listar_Visita_Acuerdos_Cliente($idcli,$idvis){
         $Obj=new mysqlhelper;$Helper=new htmlhelper();
         $sql ="SELECT visacu.sgo_int_acuerdo as param,
                visacu.sgo_txt_descripcion as Acuerdo,
                DATE_FORMAT(visacu.sgo_dat_fecha,'%d/%m/%Y') as Fecha
                FROM tbl_sgo_visitaacuerdo visacu
                INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=visacu.sgo_int_cliente";
         $where = "WHERE cli.sgo_int_cliente=@p1 and visacu.sgo_int_visita=@p2";
         $where = $Obj->sql_where($where,$idcli . '|' . $idvis);
         return $sql . " " . $where;
//         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Visita_Acuerdo','','" . $idcli . "|" . $idvis . "|","","PopUp('crm','PopUp_Mant_Visita_Acuerdo','','" . $idcli . "|" . $idvis . "|","PopUp('crm','Confirma_Eliminar_Visita_Acuerdo_Cliente','','" . $idcli . "|" . $idvis . "|",850);
      }
      function Grilla_Listar_Visita_Acuerdos_Cliente($idcli,$idvis){
         $Helper=new htmlhelper();$Obj=new mysqlhelper;
         $sql=$this->Listar_Visita_Acuerdos_Cliente($idcli,$idvis);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('crm','PopUp_Mant_Visita_Acuerdo','','" . $idcli . "|" . $idvis . "|","","PopUp('crm','PopUp_Mant_Visita_Acuerdo','','" . $idcli . "|" . $idvis . "|","PopUp('crm','Confirma_Eliminar_Visita_Acuerdo_Cliente','','" . $idcli . "|" . $idvis . "|",850, array(), array(), array(),20,null);
      }
      function Mant_Acuerdo($prm,$id){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate;$valor=explode('|',$prm);$index=0;
         $acuerdo="";$fecha="";
         $result = $Obj->consulta("SELECT sgo_txt_descripcion,DATE_FORMAT(sgo_dat_fecha, '%d/%m/%Y') as sgo_dat_fecha FROM tbl_sgo_visitaacuerdo WHERE sgo_int_acuerdo = " . $valor[2]);
         while ($row = mysqli_fetch_array($result))
         {
            $acuerdo=$row["sgo_txt_descripcion"];$fecha=$row["sgo_dat_fecha"];
            break;
         }
         $Val_Acuerdo=new InputValidacion();
         $Val_Acuerdo->InputValidacion('DocValue("fil_txtAcuerdo")!=""','Debe especificar el acuerdo');
         $Val_Fecha=new InputValidacion();
         $Val_Fecha->InputValidacion('DocValue("fil_txtFechaAcuerdo")!=""','Debe especificar la feha del acuerdo');
         $html='<table id="' . $id . '" width="100%" cellpadding="5" cellspacing="0">';
         $html .='<tr><td class="textoInput">Acuerdo</td><td>' . $Helper->textarea("fil_txtAcuerdo",++$index,$acuerdo,90,3,"","","","textoInput",$Val_Acuerdo) . '</td></tr>';
         $html .='<tr><td class="textoInput">Fecha</td><td>' . $Helper->textdate("fil_txtFechaAcuerdo",++$index,$fecha,false,$TipoDate->fecha,120,"","textoInput",$Val_Fecha) . '</td></tr>';
         return $html .='</table>';
      }
      function PopUp_Mant_Acuerdo($prm){
        $Helper=new htmlhelper;$valor=explode('|',$prm);
          $id='tbl_PopUp_Mant_Acuerdo';
          $html = $this->Mant_Acuerdo($prm,$id);
          return $Helper->PopUp("PopUp_Mant_Acuerdo",($valor[2]=="0"?"Nuevo":"Actualizar") . " Acuerdo",650,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Acuerdo_Cliente','". $id . "','" . $prm . "')",""));
      }
      function PopUp_Mant_Visita_Acuerdo($prm){
        $Helper=new htmlhelper;$valor=explode('|',$prm);
          $id='tbl_PopUp_Mant_Visita_Acuerdo';
          $html = $this->Mant_Acuerdo($prm,$id);
          return $Helper->PopUp("PopUp_Mant_Visita_Acuerdo",($valor[2]=="0"?"Nuevo":"Actualizar") . " Acuerdo",650,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Visita_Acuerdo_Cliente','". $id . "','" . $prm . "')",""));
      }
      function Mant_Acuerdo_Cliente($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm); $resp=0;
          $sql="";

          if($valor[2]!=0) $sql="UPDATE tbl_sgo_visitaacuerdo SET sgo_txt_descripcion='" . $valor[3] . "',sgo_dat_fecha='" . $Helper->convertir_fecha_ingles($valor[4]) . "' WHERE sgo_int_acuerdo=" . $valor[2];
          else $sql="INSERT INTO tbl_sgo_visitaacuerdo (sgo_txt_descripcion, sgo_dat_fecha, sgo_int_cliente,sgo_int_visita,sgo_bit_activo) VALUES ('" . $valor[3] . "','" . $Helper->convertir_fecha_ingles($valor[4]) . "'," . $valor[0] . "," . $valor[1] . ",1)";

//          return '<script>Ver_PopUp(' . $sql . '");</script>';
          return $Obj->execute($sql);
      }
      function Confirma_Eliminar_Acuerdo_Cliente($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT sgo_vch_nombrecomercial,vis.sgo_txt_descripcion FROM tbl_sgo_visitaacuerdo vis INNER JOIN tbl_sgo_cliente cli ON vis.sgo_int_cliente=cli.sgo_int_cliente WHERE sgo_int_acuerdo=" . $valor[2]);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  echo $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el acuerdo del cliente ' . $row["sgo_vch_nombrecomercial"] . ':  "' . $row["sgo_txt_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Acuerdo_Cliente','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la información'),"");
      }
      function Confirma_Eliminar_Visita_Acuerdo_Cliente($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT sgo_vch_nombrecomercial,vis.sgo_txt_descripcion FROM tbl_sgo_visitaacuerdo vis INNER JOIN tbl_sgo_cliente cli ON vis.sgo_int_cliente=cli.sgo_int_cliente WHERE sgo_int_acuerdo=" . $valor[2]);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  echo $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el acuerdo del cliente ' . $row["sgo_vch_nombrecomercial"] . ':  "' . $row["sgo_txt_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Visita_Acuerdo_Cliente','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atenci&oacute;n",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la información'),"");
      }
      function Eliminar_Acuerdo_Cliente($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          return $Obj->execute("DELETE FROM tbl_sgo_visitaacuerdo WHERE sgo_int_acuerdo=" . $valor[2]);
      }

/****************************************************************TAB PENDIENTES************************************************************************/
      function Tab_Pendientes_Cliente($prm){
         $html='<table cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                      <td class="pag_tab_zona_sup">' . $this->Filtros_Listar_Pendientes_Cliente($prm) . '</td>
                  </tr>
                  <tr>
                      <td id="div_Pendientes_Cliente" class="pag_tab_zona_inf">' . $this->Grilla_Listar_Pendientes_Cliente($prm,'','','','') . '</td>
                  </tr>
                </table>';
         return $html;
      }
      function Filtros_Listar_Pendientes_Cliente($prm){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=0;
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>
                <table id="tbl_listarpendientescliente" border="0" cellpadding="3" cellspacing="0" width="650px">
                    <tr>
                        <td style="padding-left:5px" class="textoInput">Desde</td><td>' . $Helper->hidden("fil_id",++$index,$prm) . $Helper->textdate("fil_txtBusquedaDesdePendiente",++$index,"",false,$TipoDate->fecha,80,"","textoInput") . '</td>
                        <td style="padding-left:5px" class="textoInput">Hasta</td><td>' . $Helper->textdate("fil_txtBusquedaHastaPendiente",++$index,"",false,$TipoDate->fecha,80,"","textoInput") . '</td>
                        <td style="padding-left:5px" class="textoInput">Estado</td><td>' . $Helper->combo_estadopendiente("fil_txtBusquedaEstadoPendiente",++$index,"","") . '</td>
                        <td style="padding-left:5px"><input type="button" id="btnBuscarPendientesCliente" value="Buscar" class="textoInput" onclick="Buscar_Grilla(\'crm\',\'Grilla_Listar_Pendientes_Cliente\',\'tbl_listarpendientescliente\',\'\',\'div_Pendientes_Cliente\')" /></td>
                    </tr>
                </table>
            </fieldset>';//<script type="text/javascript">$(".chzn-select").chosen();</script>';
          return $html;
      }
      function Listar_Pendientes_Cliente($idcli,$desde,$hasta,$estado){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT Concat(vistar.sgo_int_visita,'|',vistar.sgo_int_tarea) as param,
              vistar.sgo_vch_descripcion as Tarea,
              DATE_FORMAT(vistar.sgo_dat_fechacomprometida,'%d/%m/%Y') as Fecha,
              case vistar.sgo_bit_pendiente when 1 then 'Pendiente' else 'Concluido' end as Estado
              FROM tbl_sgo_visitatarea vistar
              LEFT JOIN tbl_sgo_visita viscli on viscli.sgo_int_visita=vistar.sgo_int_visita";
         $where = "WHERE vistar.sgo_int_cliente=@p1 and (vistar.sgo_dat_fechacomprometida between '@p2 00:00' and '@p3 23:59') and vistar.sgo_bit_pendiente=@p4";
         $where = $Obj->sql_where($where,$idcli . '|' . $Helper->convertir_fecha_ingles($desde) . '|' . $Helper->convertir_fecha_ingles($hasta) . '|' . $estado);
         return $sql . " " . $where;
      }
      function Grilla_Listar_Pendientes_Cliente($idcli,$desde,$hasta,$estado){
         $Helper=new htmlhelper();$Obj=new mysqlhelper;
         $sql=$this->Listar_Pendientes_Cliente($idcli,$desde,$hasta,$estado);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('crm','PopUp_Mant_Pendiente','','" . $idcli . "|0|","","PopUp('crm','PopUp_Mant_Pendiente','','" . $idcli . "|","PopUp('crm','Confirma_Eliminar_Pendiente_Cliente','','" . $idcli . "|",null, array(), array(), array(),20,null);
      }
      function Listar_Visita_Pendientes_Cliente($idcli,$idvis){
         $Obj=new mysqlhelper;
         $sql ="SELECT vistar.sgo_int_tarea as param,
              vistar.sgo_vch_descripcion as Tarea,
              DATE_FORMAT(vistar.sgo_dat_fechacomprometida,'%d/%m/%Y') as Fecha,
              case vistar.sgo_bit_pendiente when 1 then 'Pendiente' else 'Concluido' end as Estado
              FROM tbl_sgo_visitatarea vistar
              INNER JOIN tbl_sgo_visita viscli on viscli.sgo_int_visita=vistar.sgo_int_visita
              INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=viscli.sgo_int_cliente";
         $where = "WHERE cli.sgo_int_cliente=@p1 and vistar.sgo_int_visita=@p2";
         $where = $Obj->sql_where($where,$idcli . '|' . $idvis);
         return $sql . " " . $where;
      }
      function Grilla_Listar_Visita_Pendientes_Cliente($idcli,$idvis){
         $Helper=new htmlhelper();$Obj=new mysqlhelper;
         $sql=$this->Listar_Visita_Pendientes_Cliente($idcli,$idvis);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('crm','PopUp_Mant_Visita_Pendiente','','" . $idcli . "|" . $idvis . "|","","PopUp('crm','PopUp_Mant_Visita_Pendiente','','" . $idcli . "|" . $idvis . "|","PopUp('crm','Confirma_Eliminar_Visita_Pendiente_Cliente','','" . $idcli . "|" . $idvis . "|",850, array(), array(), array(),20,null);
      }
      function Mant_Pendiente($prm,$id){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate;$valor=explode('|',$prm);$index=0;
         $pendiente="";$fecha="";$estado="";
         $result = $Obj->consulta("SELECT sgo_vch_descripcion,DATE_FORMAT(sgo_dat_fechacomprometida, '%d/%m/%Y') as sgo_dat_fechacomprometida,sgo_bit_pendiente FROM tbl_sgo_visitatarea WHERE sgo_int_tarea = " . $valor[2]);
         while ($row = mysqli_fetch_array($result))
         {
            $pendiente=$row["sgo_vch_descripcion"];$fecha=$row["sgo_dat_fechacomprometida"];$estado=$row["sgo_bit_pendiente"];
            break;
         }
         $Val_Pendiente=new InputValidacion();
         $Val_Pendiente->InputValidacion('DocValue("fil_txtPendiente")!=""','Debe especificar el pendiente');
         $Val_Fecha=new InputValidacion();
         $Val_Fecha->InputValidacion('DocValue("fil_txtFechaPendiente")!=""','Debe especificar la fecha del pendiente');
         $Val_Estado=new InputValidacion();
         $Val_Estado->InputValidacion('DocValue("fil_cmbEstadoPendiente")!=""','Debe especificar el estado del pendiente');
         $html='<table id="' . $id . '" width="100%" cellpadding="5" cellspacing="0">';
         $html .='<tr><td class="textoInput">Pendiente</td><td colspan="3">' . $Helper->textarea("fil_txtPendiente",++$index,$pendiente,90,3,"","","","textoInput",$Val_Pendiente) . '</td>';
         $html .='<tr><td class="textoInput">Fecha Comprometida</td><td>' . $Helper->textdate("fil_txtFechaPendiente",++$index,$fecha,false,$TipoDate->fecha,120,"","textoInput",$Val_Fecha) . '</td>';
              $html .='<td class="textoInput">Estado</td><td>' . $Helper->combo_estadopendiente("fil_cmbEstadoPendiente",++$index,$estado,"",$Val_Estado) . '</td></tr>';
         return $html .='</table>';
      }
      function PopUp_Mant_Pendiente($prm){
        $Helper=new htmlhelper;$valor=explode('|',$prm);
          $id='tbl_PopUp_Mant_Pendiente';
          $html = $this->Mant_Pendiente($prm,$id);
          return $Helper->PopUp("PopUp_Mant_Pendiente",($valor[2]=="0"?"Nuevo":"Actualizar") . " Pendiente",650,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Pendiente_Cliente','". $id . "','" . $prm . "')",""));
      }
      function PopUp_Mant_Visita_Pendiente($prm){
        $Helper=new htmlhelper;$valor=explode('|',$prm);
          $id='tbl_PopUp_Mant_Visita_Pendiente';
          $html = $this->Mant_Pendiente($prm,$id);
          return $Helper->PopUp("PopUp_Mant_Visita_Pendiente",($valor[2]=="0"?"Nuevo":"Actualizar") . " Pendiente",650,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Visita_Pendiente_Cliente','". $id . "','" . $prm . "')",""));
      }
      function Mant_Pendiente_Cliente($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm); $resp=0;
          if($valor[2]!=0) return $Obj->execute("UPDATE tbl_sgo_visitatarea
                                        SET sgo_vch_descripcion='" . $valor[3] . "',sgo_dat_fechacomprometida='" . $Helper->convertir_fecha_ingles($valor[4]) . "',sgo_bit_pendiente=" . $valor[5] . "," .
                                        ($valor[5]==0?"sgo_dat_fechafintarea='" . date("Y-m-d H:i") . "'":"sgo_dat_fechafintarea=''") . "
                                        WHERE sgo_int_tarea=" . $valor[2]);
          else return $Obj->execute("INSERT INTO tbl_sgo_visitatarea
                             (sgo_vch_descripcion, sgo_dat_fechacomprometida, sgo_bit_pendiente, sgo_int_cliente,sgo_int_visita)
                             VALUES ('" . $valor[3] . "','" . $Helper->convertir_fecha_ingles($valor[4]) . "'," . $valor[5] . "," . $valor[0] . "," . $valor[1] . ")");
      }
      function Confirma_Eliminar_Pendiente_Cliente($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT sgo_vch_nombrecomercial,vis.sgo_vch_descripcion FROM tbl_sgo_visitatarea vis INNER JOIN tbl_sgo_cliente cli ON vis.sgo_int_cliente=cli.sgo_int_cliente WHERE sgo_int_tarea=" . $valor[2]);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  echo $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el pendiente al cliente ' . $row["sgo_vch_nombrecomercial"] . ' :  "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Pendiente_Cliente','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la información'),"");
      }
      function Confirma_Eliminar_Visita_Pendiente_Cliente($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT sgo_vch_nombrecomercial,vis.sgo_vch_descripcion FROM tbl_sgo_visitatarea vis INNER JOIN tbl_sgo_cliente cli ON vis.sgo_int_cliente=cli.sgo_int_cliente WHERE sgo_int_tarea=" . $valor[2]);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  echo $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el pendiente al cliente ' . $row["sgo_vch_nombrecomercial"] . ' :  "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Visita_Pendiente_Cliente','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la información'),"");
      }
      function Eliminar_Pendiente_Cliente($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          return $Obj->execute("DELETE FROM tbl_sgo_visitatarea WHERE sgo_int_tarea=" . $valor[2]);
      }

/****************************************************************MENU VISITAS************************************************************************/
/****************************************************************MENU VISITAS************************************************************************/
/****************************************************************MENU VISITAS************************************************************************/
      function Filtros_Listar_Visitas(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate; $index=0;
         $html = '<fieldset class="textoInput"><legend align= "left">Filtros de b&uacute;squeda</legend>
                <table id="tbl_listarvisitascliente" border="0" cellpadding="3" cellspacing="0" width="990px">
                    <tr>
                        <td class="textoInput">Nombre Comercial</td><td>' . $Helper->textbox("fil_txtBusquedaNombreComercial",++$index,"",128,250,$TipoTxt->texto,"","","","textoInput") . '</td>
                        <td class="textoInput">Estado</td><td>' . $Helper->combo_estadovisita("fil_cmbtBusquedaEstadoVisita",++$index,"","") . '</td>
                    </tr>
                    <tr>
                        <td style="padding-left:5px" class="textoInput">Desde</td><td>' . $Helper->textdate("fil_txtBusquedaDesdeVisita",++$index,"",false,$TipoDate->fecha,80,"","textoInput") . '</td>
                        <td style="padding-left:5px" class="textoInput">Hasta</td><td>' . $Helper->textdate("fil_txtBusquedaHastaVisita",++$index,"",false,$TipoDate->fecha,80,"","textoInput") . '</td>
                        <td style="padding-left:5px"><input type="button" id="btnBuscarVisitasCliente" value="Buscar" class="textoInput" onclick="Buscar_Grilla(\'crm\',\'Grilla_Listar_Visitas\',\'tbl_listarvisitascliente\',\'\',\'td_General\')" /></td>
                    </tr>
                </table>
            </fieldset>';//<script type="text/javascript">$(".chzn-select").chosen();</script>';
          return $html;
      }
      function Grilla_Listar_Visitas($cliente,$estado,$desde,$hasta){
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT Concat(viscli.sgo_int_cliente,'|',viscli.sgo_int_visita) as param,
              per.sgo_vch_alias as Cliente,
              viscli.sgo_vch_lugarcita as Lugar,
              viscli.sgo_dat_fechaprogramada as 'Fecha Programada',
              estvis.sgo_vch_descripcion as Estado,
              concli.sgo_vch_nombre as Contacto,
              concli.sgo_vch_celular as Celular
              FROM tbl_sgo_visita viscli
              INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=viscli.sgo_int_cliente
              INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=cli.sgo_int_cliente
              INNER JOIN tbl_sgo_estadovisita estvis on estvis.sgo_int_estadovisita = viscli.sgo_int_estadovisita
              INNER JOIN tbl_sgo_contactocliente concli on concli.sgo_int_contacto=viscli.sgo_int_contacto";
         $where = "WHERE per.sgo_vch_nombre like '%@p2%' and estvis.sgo_vch_descripcion=@p3 and (viscli.sgo_dat_fechaprogramada between '@p4 00:00' and '@p5 23:59')";
         $where = $Obj->sql_where($where,$cliente . '|' . $estado . '|' . $Helper->convertir_fecha_ingles($desde) . '|' . $Helper->convertir_fecha_ingles($hasta));
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Visitas','','","","PopUp('crm','PopUp_Mant_Visitas','','","PopUp('crm','Confirma_Eliminar_Visita_Cliente','','",null, array(), array(), array(),20,null);
      }
      function Grilla_Listar_Visitas_Pendientes($idcli,$idvis){
         $Helper=new htmlhelper();$Obj=new mysqlhelper;
         $sql=$this->Listar_Visita_Pendientes_Cliente($idcli,$idvis);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('crm','PopUp_Mant_Visitas_Pendiente','','" . $idcli . "|" . $idvis . "|","","PopUp('crm','PopUp_Mant_Visitas_Pendiente','','" . $idcli . "|" . $idvis . "|","PopUp('crm','Confirma_Eliminar_Visitas_Pendiente','','" . $idcli . "|" . $idvis . "|",850, array(), array(), array(),20,null);
      }
      function Grilla_Listar_Visitas_Acuerdos($idcli,$idvis){
         $Helper=new htmlhelper();$Obj=new mysqlhelper;
         $sql=$this->Listar_Visita_Acuerdos_Cliente($idcli,$idvis);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql),"PopUp('crm','PopUp_Mant_Visitas_Acuerdo','','" . $idcli . "|" . $idvis . "|","","PopUp('crm','PopUp_Mant_Visitas_Acuerdo','','" . $idcli . "|" . $idvis . "|","PopUp('crm','Confirma_Eliminar_Visitas_Acuerdo','','" . $idcli . "|" . $idvis . "|",850, array(), array(), array(),20,null);
      }
      function PopUp_Mant_Visitas($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$TipoTxt=new TipoTextBox;$TipoDate=new TipoTextDate;$valor=explode('|',$prm);$index=0;
          $html="";$content_tabs = array(); $titulo_tabs = array();

          $lugar="";$fecha="";$estado="";$contacto="";
          if($valor[0]!=0){
            $result = $Obj->consulta("SELECT viscli.sgo_vch_lugarcita,DATE_FORMAT(viscli.sgo_dat_fechaprogramada, '%d/%m/%Y %H:%i') as sgo_dat_fechaprogramada,estvis.sgo_int_estadovisita,viscli.sgo_int_contacto FROM tbl_sgo_visita viscli INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=viscli.sgo_int_cliente INNER JOIN tbl_sgo_estadovisita estvis on estvis.sgo_int_estadovisita = viscli.sgo_int_estadovisita LEFT JOIN tbl_sgo_contactocliente concli on concli.sgo_int_cliente=viscli.sgo_int_cliente WHERE viscli.sgo_int_visita = " . $valor[1]);
            while ($row = mysqli_fetch_array($result))
            {
               $lugar=$row["sgo_vch_lugarcita"];$fecha=$row["sgo_dat_fechaprogramada"];$estado=$row["sgo_int_estadovisita"];$contacto=$row["sgo_int_contacto"];
               break;
            }
          }
          else $valor[1]=0;

          $Val_Cliente=new InputValidacion();
          $Val_Cliente->InputValidacion('DocValue("fil_cmbBusquedacliente")!=""','Debe especificar el cliente');
          $Val_Lugar=new InputValidacion();
          $Val_Lugar->InputValidacion('DocValue("fil_txtLugarCita")!=""','Debe especificar el lugar de cita');
          $Val_Fecha=new InputValidacion();
          $Val_Fecha->InputValidacion('DocValue("fil_txtFechaProgramada")!=""','Debe especificar la fecha programada');
          $Val_Estado=new InputValidacion();
          $Val_Estado->InputValidacion('DocValue("fil_cmb_EstadoVisita")!=""','Debe especificar el estado de la visita');
          $Val_Contacto=new InputValidacion();
          $Val_Contacto->InputValidacion('DocValue("fil_cmbContactoVisita")!=""','Debe especificar el contacto de la visita');
          $html='<table id="tbl_PopUp_Mant_Visita" width="850px" cellpadding="5" cellspacing="0">';
          if($valor[0]==0)$html .='<tr><td class="textoInput">Cliente</td><td>' . $Helper->combo_cliente("fil_cmbBusquedacliente",$index,$valor[0],"Cargar_Combo('general','Combo_Contactos_x_Cliente','fil_cmbContactoVisita','fil_cmbBusquedacliente','fil_cmbContactoVisita');",$Val_Cliente) . '</td>';
          $html .='<tr><td class="textoInput">Lugar Cita</td><td>' . $Helper->textbox("fil_txtLugarCita",++$index,$lugar,128,250,$TipoTxt->texto,"","","","textoInput",$Val_Lugar) . '</td>';
               $html .='<td class="textoInput">Fecha Programada</td><td>' . $Helper->textdate("fil_txtFechaProgramada",++$index,$fecha,false,$TipoDate->fechahora,120,"","textoInput",$Val_Fecha) . '</td></tr>';
          $html .='<tr><td class="textoInput">Estado Visita</td><td>' . $Helper->combo_estadovisita("fil_cmb_EstadoVisita",++$index,$estado,"",$Val_Estado) . '</td>';
               $html .='<td class="textoInput">Contacto</td><td>' . $Helper->combo_contactos_x_cliente("fil_cmbContactoVisita",++$index,$valor[0],$contacto,"",$Val_Contacto) . '</td></tr>';
          $html .='</table>';
          $content_tabs[0] ="<br/>" . $html;
          $titulo_tabs[0]="Visitas";

          if($valor[1]!=0){
             $sql ="SELECT vistar.sgo_int_tarea as param,
                  vistar.sgo_vch_descripcion as Tarea,
                  vistar.sgo_dat_fechacomprometida as Fecha,
                  case vistar.sgo_bit_pendiente when 1 then 'Pendiente' else 'Concluido' end as Estado
                  FROM tbl_sgo_visitatarea vistar
                  INNER JOIN tbl_sgo_visita viscli on viscli.sgo_int_visita=vistar.sgo_int_visita
                  INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=viscli.sgo_int_cliente";
             $where = "WHERE cli.sgo_int_cliente=@p1 and vistar.sgo_int_visita=@p2";
             $where = $Obj->sql_where($where,$valor[0] . '|' . $valor[1]);
             $html = "<div id='div_tbl_Visitas_Pendiente'>" . $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Visitas_Pendiente','','" . $prm . "|","","PopUp('crm','PopUp_Mant_Visitas_Pendiente','','" . $prm . "|","PopUp('crm','Confirma_Eliminar_Visitas_Pendiente','','" . $prm . "|",850, array(), array(), array(),20,null) . "</div>";
              $content_tabs[1] ="<br/>" . $html;
              $titulo_tabs[1]="Pendientes";

             $pendiente="";$fecha="";$estado="";
             $result = $Obj->consulta("SELECT sgo_vch_descripcion,DATE_FORMAT(sgo_dat_fechacomprometida, '%d/%m/%Y') as sgo_dat_fechacomprometida,sgo_bit_pendiente FROM tbl_sgo_visitatarea WHERE sgo_int_tarea = " . $valor[1]);
             while ($row = mysqli_fetch_array($result))
             {
                $pendiente=$row["sgo_vch_descripcion"];$fecha=$row["sgo_dat_fechacomprometida"];$estado=$row["sgo_bit_pendiente"];
                break;
             }

             $sql ="SELECT visacu.sgo_int_acuerdo as param,
                    visacu.sgo_txt_descripcion as Acuerdo,
                    visacu.sgo_dat_fecha as Fecha
                    FROM tbl_sgo_visitaacuerdo visacu
                    INNER JOIN tbl_sgo_cliente cli on cli.sgo_int_cliente=visacu.sgo_int_cliente";
             $where = "WHERE cli.sgo_int_cliente=@p1 and visacu.sgo_int_visita=@p2";
             $where = $Obj->sql_where($where,$valor[0] . '|' . $valor[1]);
             $html ="<div id='div_tbl_Visitas_Acuerdo'>" . $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where),"PopUp('crm','PopUp_Mant_Visitas_Acuerdo','','" . $prm . "|","","PopUp('crm','PopUp_Mant_Visitas_Acuerdo','','" . $prm . "|","PopUp('crm','Confirma_Eliminar_Visitas_Acuerdo','','" . $prm . "|",850, array(), array(), array(),20,null) . "</div>";

              $content_tabs[2] ="<br/>" . $html;
              $titulo_tabs[2]="Acuerdos";
          }
          $html = $Helper->Crear_Tabs("Tabs_Mant_Visita_Cliente",$content_tabs, $titulo_tabs, "s","");

          return $Helper->PopUp("PopUp_Mant_Visitas",($valor[1]=="0"?"Nueva":"Actualizar") . " Visita",860,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Visitas','tbl_PopUp_Mant_Visita','" . $prm . "')",""));
      }
      function Mant_Visitas($prm){
          $Obj=new mysqlhelper;$Helper=new htmlhelper;$valor=explode('|',$prm); $resp=0;
          if($valor[0]!=0) $resp=$Obj->execute("UPDATE tbl_sgo_visita
                                        SET sgo_vch_lugarcita='" . $valor[2] . "',sgo_dat_fechaprogramada='" . $Helper->convertir_fecha_ingles($valor[3]) . "',sgo_int_estadovisita=" . $valor[4] . ",
                                        sgo_int_contacto=" . $valor[5] . "
                                        WHERE sgo_int_visita=" . $valor[0]);
          else $resp=$Obj->execute("INSERT INTO tbl_sgo_visita
                             (sgo_vch_lugarcita, sgo_dat_fechaprogramada, sgo_int_estadovisita, sgo_int_usuario, sgo_int_contacto, sgo_int_cliente)
                             VALUES ('" . $valor[2] . "','" . $Helper->convertir_fecha_ingles($valor[3]) . "'," . $valor[4] . ",0," . $valor[5] . "," . $valor[1] . ")");
/*          $x = "INSERT INTO tbl_sgo_visita (sgo_vch_lugarcita, sgo_dat_fechaprogramada, sgo_int_estadovisita, sgo_int_usuario, sgo_int_contacto, sgo_int_cliente) VALUES ('" . $valor[2] . "','" . $valor[3] . "'," . $valor[4] . ",0," . $valor[5] . "," . $valor[0] . ")";
          $x = "UPDATE tbl_sgo_visita SET sgo_vch_lugarcita='" . $valor[2] . "',sgo_dat_fechaprogramada='" . $Helper->convertir_fecha_ingles($valor[3]) . "',sgo_int_estadovisita=" . $valor[4] . ",sgo_int_contacto=" . $valor[5] . " WHERE sgo_int_contacto=" . $valor[1];
          return '<script>Ver_PopUp("' . $x . '");</script>';*/
      }
      function PopUp_Mant_Visitas_Acuerdo($prm){
        $Helper=new htmlhelper;$valor=explode('|',$prm);
          $id='tbl_PopUp_Mant_Visitas_Acuerdo';
          $html = $this->Mant_Acuerdo($prm,$id);
          return $Helper->PopUp("PopUp_Mant_Visitas_Acuerdo",($valor[2]=="0"?"Nuevo":"Actualizar") . " Acuerdo",850,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Visitas_Acuerdo','". $id . "','" . $prm . "')",""));
      }
      function PopUp_Mant_Visitas_Pendiente($prm){
        $Helper=new htmlhelper;$valor=explode('|',$prm);
          $id='tbl_PopUp_Mant_Visitas_Pendiente';
          $html = $this->Mant_Pendiente($prm,$id);
          return $Helper->PopUp("PopUp_Mant_Visitas_Pendiente",($valor[2]=="0"?"Nuevo":"Actualizar") . " Pendiente",850,$html,$Helper->button("","Grabar",70,"Operacion('crm','Mant_Visitas_Pendiente','". $id . "','" . $prm . "')",""));
      }
      function Confirma_Eliminar_Visitas($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT sgo_vch_nombrecomercial FROM tbl_sgo_visita vis INNER JOIN tbl_sgo_cliente cli ON vis.sgo_int_cliente=cli.sgo_int_cliente WHERE sgo_int_visita=" . $valor[1]);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  echo $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la visita al cliente ' . $row["sgo_vch_nombrecomercial"] . '?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Visita_Cliente','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la informaci&oacute;n'),"");
      }
      function Eliminar_Visitas($prm){
          $Obj=new mysqlhelper;$valor=explode('|',$prm);
          if($Obj->execute("DELETE FROM tbl_sgo_visita WHERE sgo_int_visita=" . $valor[1])!=-1)
              return "<script>Operacion_Result(true);Buscar_Grilla('crm','Grilla_Listar_Visitas_Cliente','','" . $valor[0] . "','div_Visitas_Cliente');</script>";
          else return "<script>Operacion_Result(false);</script>";
      }
      function Confirma_Eliminar_Visitas_Pendiente($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT sgo_vch_nombrecomercial,vis.sgo_vch_descripcion FROM tbl_sgo_visitatarea vis INNER JOIN tbl_sgo_cliente cli ON vis.sgo_int_cliente=cli.sgo_int_cliente WHERE sgo_int_tarea=" . $valor[2]);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  echo $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el pendiente al cliente ' . $row["sgo_vch_nombrecomercial"] . ' :  "' . $row["sgo_vch_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Visitas_Pendiente','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la información'),"");
      }
      function Confirma_Eliminar_Visitas_Acuerdo($prm){
          $Obj=new mysqlhelper; $Helper=new htmlhelper;$valor=explode('|',$prm);
          $result= $Obj->consulta("SELECT sgo_vch_nombrecomercial,vis.sgo_txt_descripcion FROM tbl_sgo_visitaacuerdo vis INNER JOIN tbl_sgo_cliente cli ON vis.sgo_int_cliente=cli.sgo_int_cliente WHERE sgo_int_acuerdo=" . $valor[2]);
          if (mysqli_num_rows($result) > 0)
          {
             while ($row = mysqli_fetch_array($result))
             {
                  echo $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el acuerdo del cliente ' . $row["sgo_vch_nombrecomercial"] . ':  "' . $row["sgo_txt_descripcion"] . '"?'),$Helper->button("", "Si", 70, "Operacion('crm','Eliminar_Visitas_Acuerdo','','" . $prm . "')"));
                  break;
             }
          }
          else return $Helper->PopUp("","Atención",450,htmlentities('Ha ocurrido un error en el sistema y no se ha podido registrar la información'),"");
      }
/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
/****************************************************************OBTENER DATOS************************************************************************/
      function Obtener_Nombre_Credito($prm)
      {
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT Concat(mon.sgo_vch_simbolo,' ',crecli.sgo_dec_credito) as credito FROM tbl_sgo_creditocliente crecli INNER JOIN tbl_sgo_moneda mon ON crecli.sgo_int_moneda=mon.sgo_int_moneda WHERE crecli.sgo_int_creditocliente=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["credito"];
          }
          return "";
      }
   }
?>