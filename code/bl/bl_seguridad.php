<?php include_once('../../code/lib/htmlhelper.php');include_once('../../code/lib/loghelper.php');
  Class bl_seguridad
  {
/****************************************************************usuario********************************************************************/
      function Filtros_Listar_usuario(){
         $Helper=new htmlhelper; $TipoTxt=new TipoTextBox; $index=10;
         $inputs=array(
            "Usuario" => $Helper->textbox("fil_txtBNombre",++$index,"",128,250,$TipoTxt->texto,"","","",""),            
            "Estado" => $Helper->combo_estado("fil_cmbBEstado",++$index,"","")
         );
         $buttons=array($Helper->button("btnBuscar_usuario","Buscar",70,"Buscar_Grilla('seguridad','Grilla_Listar_usuario','tbl_listar_usuario','','td_General')","textoInput"));
         return $Helper->Crear_Filtros_Layer("tbl_listar_usuario",$inputs,$buttons,3,1200,"","");
      }
      function Grilla_Listar_usuario($prm){
      	 $log = new loghelper;
         $Obj=new mysqlhelper;$Helper=new htmlhelper;
         $sql ="SELECT sgo_int_usuario as param,CONCAT(ifnull(sgo_vch_nombre,''),' ', ifnull(sgo_vch_apellidopaterno,''),' ',ifnull(sgo_vch_apellidomaterno,'')) as USUARIO,
         		sgo_vch_usuario as 'LOGIN',
                CASE sgo_bit_activo WHEN B'1' THEN 'Activo' ELSE 'Inactivo' END as ESTADO
                FROM tbl_sgo_usuario";
         $where = $Obj->sql_where(" WHERE CONCAT(sgo_vch_nombre,' ', sgo_vch_apellidopaterno,' ',sgo_vch_apellidomaterno) like '%@p1%' and usr.kvl_bit_activo= B'@p2'",
                 $prm);
         $orderby = "ORDER BY CONCAT(ifnull(sgo_vch_nombre,''),' ', ifnull(sgo_vch_apellidopaterno,''),' ',ifnull(sgo_vch_apellidomaterno,''))";
         $log->log($sql . " " . $where . " " . $orderby);
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"PopUp('seguridad','PopUp_Mant_usuario','','","","PopUp('seguridad','PopUp_Mant_usuario','','","PopUp('seguridad','Confirma_Eliminar_usuario','','",null,array(),array(),array(),10,"USUARIOS REGISTRADOS");
      }
      function Grilla_Listar_usuarioperfil($prm){
         $Obj=new postgreshelper;$Helper=new htmlhelper;$valor=explode('|',$prm);
         $sql ="SELECT (perusr.kvl_int_usuario || '|' || perusr.kvl_int_modulo) as param,COALESCE(modpadre.kvl_str_nombre,mod.kvl_str_nombre) as \"MODULO\", mod.kvl_str_nombre as \"OPCION\"
                FROM tbl_kvl_modulousuario perusr
                INNER JOIN tbl_kvl_modulo mod ON mod.kvl_int_modulo=perusr.kvl_int_modulo
                LEFT JOIN tbl_kvl_modulo modpadre ON modpadre.kvl_int_modulo=mod.kvl_int_modulopadre";
         $where = $Obj->sql_where("WHERE perusr.kvl_int_usuario=@p1",
                 $valor[0]);
         $orderby = "ORDER BY mod.kvl_int_grupo ASC,mod.kvl_int_nivel ASC";
         return $Helper->Imprimir_Grilla($Obj->consulta($sql . " " . $where . " " . $orderby),"","","","PopUp('seguridad','Confirma_Eliminar_usuarioperfil','','",null,array(),array(),array(),10,"MODULOS ASIGNADOS");
      }
      function PopUp_Mant_usuario($prm){
         $Obj=new postgreshelper;$Helper=new htmlhelper; $TipoTxt=new TipoTextBox;$index=10;
         $usuario="";$clave="";$agente="";$nombre="";$estado="1";
         $result = $Obj->consulta("SELECT kvl_str_login,kvl_str_clave,kvl_int_agenteaduana,kvl_str_nombre,kvl_bit_activo FROM tbl_kvl_usuario WHERE kvl_int_usuario=" . $prm);
    	 while($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)){
            $usuario=$row["kvl_str_login"];$clave=$row["kvl_str_clave"];$agente=$row["kvl_int_agenteaduana"];$nombre=$row["kvl_str_nombre"];$estado=$row["kvl_bit_activo"];break;
         }
         $Val_Usuario=new InputValidacion();
         $Val_Usuario->InputValidacion('DocValue("fil_txtUsuario")!=""','Debe especificar un usuario');
         $Val_Clave=new InputValidacion();
         $Val_Clave->InputValidacion('DocValue("fil_txtClave")!=""','Debe especificar una clave');
         $Val_Agente=new InputValidacion();
         $Val_Agente->InputValidacion('DocValue("fil_cmbAgente")!=""','Debe especificar el agente aduana');
         $Val_Nombre=new InputValidacion();
         $Val_Nombre->InputValidacion('DocValue("fil_txtNombre")!=""','Debe especificar el nombre');
         $Val_Estado=new InputValidacion();
         $Val_Estado->InputValidacion('DocValue("fil_cmbEstado")!=""','Debe especificar el estado');
         $inputs=array(
            "Usuario" => $Helper->textbox("fil_txtUsuario",++$index,$usuario,15,200,$TipoTxt->texto,"","","","",$Val_Usuario),
            "Clave" => $Helper->textbox("fil_txtClave",++$index,$clave,10,200,$TipoTxt->texto,"","","","",$Val_Clave),
            "Agente Aduana" => $Helper->combo_agentes("fil_cmbAgente",++$index,$agente,"",$Val_Agente),
            "Nombre" => $Helper->textbox("fil_txtNombre",++$index,$nombre,100,200,$TipoTxt->texto,"","","","",$Val_Estado),
            "Estado" => $Helper->combo_estado("fil_cmbEstado",++$index,$estado,"",""),
         );
         $buttons=array();
         $html = $Helper->Crear_Layer("tbl_mant_usuario",$inputs,$buttons,2,850,"","");

         $titulo_tabs = array(); $content_tabs = array();
         $titulo_tabs[0]="Datos Generales";
         $content_tabs[0] ="<br/> " . $html;

         $Val_Modulo=new InputValidacion();
         $Val_Modulo->InputValidacion('DocValue("fil_cmbModulo")!=""','Debe seleccionar un módulo');
         $inputs=array(
            "Modulos Disponibles" =>$Helper->combo_modulolibre_usuario("fil_cmbModulo",++$index,$prm,"","",$Val_Modulo),
         );
         $buttons=array(
             $Helper->button("","Agregar",70,"Operacion('seguridad','Mant_usuarioperfil','tbl_mant_usuarioperfil','" . $prm . "')","textoInput")
         );
         $html = $Helper->Crear_Layer("tbl_mant_usuarioperfil",$inputs,$buttons,2,850,"","");

         $titulo_tabs[1]="Modulos";
         $content_tabs[1] ="<br/> " . $html . "<br/><div id='div_mant_usuarioperfil'>" . $this->Grilla_Listar_usuarioperfil($prm) . "</div>";
         $html = $Helper->Crear_Tabs("Tabs_Detalles",$content_tabs, $titulo_tabs, "m","");

         return $Helper->PopUp("",($prm==0?"Nuevo":"Actualizar") . " Usuario",900,$html,$Helper->button("","Grabar",70,"Operacion('seguridad','Mant_usuario','tbl_mant_usuario','" . $prm . "')","textoInput"));
      }
      function Mant_usuario($prm){
          $Obj=new postgreshelper;$valor=explode('|',$prm);
          if($valor[0]!=0) $sql= "UPDATE tbl_kvl_usuario SET kvl_str_login='" . $valor[1] . "',kvl_str_clave='" . $valor[2] . "',
                                kvl_int_agenteaduana=" . $valor[3] . ",kvl_str_nombre='" . $valor[4] . "',
                                kvl_bit_activo=B'" . $valor[5] . "' WHERE kvl_int_usuario=" . $valor[0];
          else  $sql= "INSERT INTO tbl_kvl_usuario (kvl_str_login,kvl_str_clave,kvl_int_agenteaduana,kvl_str_nombre,kvl_bit_activo)
                       VALUES('" . $valor[1] . "','" . $valor[2] . "'," . $valor[3] . ",'" . $valor[4] . "',B'1')";
          return $Obj->execute($sql);
      }
      function Confirma_Eliminar_usuario($prm){
          $Helper=new htmlhelper;
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar el usuario ' . $this->Obtener_Nombre_usuario($prm) . '?',null,"utf-8"),$Helper->button("", "Si", 70, "Operacion('seguridad','Eliminar_usuario','','" . $prm . "')"));
      }
      function Eliminar_usuario($prm){
          $Obj=new postgreshelper;
          $sql="UPDATE tbl_kvl_usuario SET kvl_bit_activo=0 WHERE kvl_int_usuario=" . $prm;
          return $Obj->execute($sql);
      }
      function Mant_usuarioperfil($prm){
          $Obj=new postgreshelper;$valor=explode('|',$prm);
          $sql= "INSERT INTO tbl_kvl_modulousuario (kvl_int_usuario,kvl_int_modulo) VALUES(" . $valor[0] . "," . $valor[1] . ")";
          return $Obj->execute($sql);
      }
      function Confirma_Eliminar_usuarioperfil($prm){
          $Helper=new htmlhelper;$valor=explode('|',$prm);
          return $Helper->PopUp("","Confirmación",450,htmlentities('¿Está seguro de eliminar la opción ' . $this->Obtener_Nombre_perfilusuario($valor[1]) . '?',null,"utf-8"),$Helper->button("", "Si", 70, "Operacion('seguridad','Eliminar_usuarioperfil','','" . $prm . "')"));
      }
      function Eliminar_usuarioperfil($prm){
          $Obj=new postgreshelper;$valor=explode('|',$prm);
          $sql="DELETE FROM tbl_kvl_modulousuario WHERE kvl_int_usuario=" . $valor[0] . " AND kvl_int_modulo=" . $valor[1];
          return $Obj->execute($sql);
      }

      /*****************************************OBTENER DATOS************************************/
      /*****************************************OBTENER DATOS************************************/
      function Obtener_Nombre_usuario($prm){
          $Obj=new postgreshelper;
          $result= $Obj->consulta("SELECT kvl_str_nombre as nombre FROM tbl_kvl_usuario WHERE kvl_int_usuario=" . $prm);
  		  while($row = pg_fetch_array($result, NULL, PGSQL_ASSOC))
          {
            return $row["nombre"];
          }
          return "";
      }
      function Obtener_Nombre_perfilusuario($prm){
          $Obj=new postgreshelper;
          $result= $Obj->consulta("SELECT mod.kvl_str_nombre as nombre
                                    FROM tbl_kvl_modulo mod
                                    WHERE kvl_int_modulo=" . $prm);
  		  while($row = pg_fetch_array($result, NULL, PGSQL_ASSOC))
          {
            return $row["nombre"];
          }
          return "";
      }
    }
?>