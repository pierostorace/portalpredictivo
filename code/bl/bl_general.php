<?php include_once('../../code/lib/mysqlhelper.php');
  Class bl_general
  {
      function Obtener_Nombre_Persona($prm)
      {
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_nombre FROM tbl_sgo_persona WHERE sgo_int_persona=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_nombre"];
            break;
          }
          return "";
      }
      function Obtener_Alias_Persona($prm)
      {
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_vch_alias FROM tbl_sgo_persona WHERE sgo_int_persona=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_alias"];
            break;
          }
          return "";
      }
      function Obtener_IDDireccion_x_Codigo($prm,$id_cliente)
      {
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_int_direccion FROM tbl_sgo_direccioncliente WHERE sgo_int_cliente=" . $id_cliente . " AND sgo_vch_codigotienda='" . $prm . "' LIMIT 0 , 1");
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_int_direccion"];
            break;
          }
          return "";
      }
      function Obtener_Caracteristica_Comprobante($prm)
      {
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_int_digitosserie,sgo_int_digitoscorrelativo,sgo_bit_autonumerico,sgo_int_serie,sgo_int_correlativo
          FROM tbl_sgo_tipocomprobante WHERE sgo_int_tipocomprobante=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return array(
				"digitosserie"=>$row["sgo_int_digitosserie"],
				"digitoscorrelativo"=>$row["sgo_int_digitoscorrelativo"],
				"autonumerico"=>$row["sgo_bit_autonumerico"],
				"serie"=>$row["sgo_int_serie"],
				"correlativo"=>$row["sgo_int_correlativo"]);
            break;
          }
          return array("digitosserie"=>"","digitoscorrelativo"=>"","autonumerico"=>0,"serie"=>"","correlativo"=>"");
      }
      function Obtener_Caracteristica_CategoriaComprobante($prm)
      {
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("SELECT sgo_int_tipomovimientocaja,CASE sgo_bit_comprobantecobraropagar WHEN 1 THEN 1 ELSE 0 END as sgo_bit_comprobantecobraropagar
          FROM tbl_sgo_categoriacomprobante WHERE sgo_int_categoriacomprobante=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return array(
				"movimientocaja"=>$row["sgo_int_tipomovimientocaja"],"generarcobraropagar"=>$row["sgo_bit_comprobantecobraropagar"]);
            break;
          }
          return array("movimientocaja"=>3,"generarcobraropagar"=>0);
      }
      function Obtener_Correlativo_Comprobante($tipo_comprobante,$ref_trans=null){
          $trans=($ref_trans==null?$Obj->transaction():$ref_trans);
          $comprobante=$this->Obtener_Caracteristica_Comprobante($tipo_comprobante);
          if($comprobante["autonumerico"]==1)
          {
              return $comprobante["correlativo"];
          }
          return -1;
      }
      function Generar_Numeracion_Comprobante($tipo_comprobante,$ref_trans=null){
          $trans=($ref_trans==null?$Obj->transaction():$ref_trans);
          $comprobante=$this->Obtener_Caracteristica_Comprobante($tipo_comprobante);
          if($comprobante["autonumerico"]==1)
          {
              $sql="UPDATE tbl_sgo_tipocomprobante SET sgo_int_correlativo=(sgo_int_correlativo + 1) WHERE sgo_int_tipocomprobante=" . $tipo_comprobante;
              if(!$trans->query($sql))return -1;
          }
          return 1;
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

      function Obtener_Nombre_Articulo($prm){
          $Obj=new mysqlhelper;
          $result= $Obj->consulta("	SELECT 	CONCAT(pdt.sgo_vch_nombre,' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) as sgo_vch_nombre 
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
          							WHERE 	sgo_int_producto=" . $prm);
          while ($row = mysqli_fetch_array($result))
          {
            return $row["sgo_vch_nombre"];
          }
          return "";
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
  }
?>
