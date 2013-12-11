<?php
include('../../code/lib/htmlhelper.php');
if (is_writable("guia.xml")) {
$itemsPagina=100;
$file = fopen("guia.xml", "w");
$space_number=0;
$Obj=new mysqlhelper;$Helper=new htmlhelper;
$sql ="SELECT 	    distinct persona.sgo_vch_nombre,
        			concat (direccion.sgo_vch_direccion,', ',trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_int_ubigeo = direccion.sgo_int_ubigeo)),', ',
					trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_int_ubigeo = 
					(select sgo_int_ubigeo from tbl_sgo_ubigeo where sgo_vch_prov = (select sgo_vch_prov from tbl_sgo_ubigeo where sgo_int_ubigeo = direccion.sgo_int_ubigeo)
					and sgo_vch_dpto = (select sgo_vch_dpto from tbl_sgo_ubigeo where sgo_int_ubigeo = direccion.sgo_int_ubigeo) and sgo_vch_dist = '00'))),', ',
					trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_int_ubigeo = 
					(select sgo_int_ubigeo from tbl_sgo_ubigeo where sgo_vch_prov = '00'
					and sgo_vch_dpto = (select sgo_vch_dpto from tbl_sgo_ubigeo where sgo_int_ubigeo = direccion.sgo_int_ubigeo) and sgo_vch_dist = '00'   and sgo_vch_pais = '139')))) as sgo_vch_direccion,
					concat (tra.sgo_vch_direccion,', ',trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_int_ubigeo = tra.sgo_int_ubigeo)),', ',
					trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_int_ubigeo = 
					(select sgo_int_ubigeo from tbl_sgo_ubigeo where sgo_vch_prov = (select sgo_vch_prov from tbl_sgo_ubigeo where sgo_int_ubigeo = tra.sgo_int_ubigeo)
					and sgo_vch_dpto = (select sgo_vch_dpto from tbl_sgo_ubigeo where sgo_int_ubigeo = tra.sgo_int_ubigeo) and sgo_vch_dist = '00'))),', ',
					trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_int_ubigeo = 
					(select sgo_int_ubigeo from tbl_sgo_ubigeo where sgo_vch_prov = '00'
					and sgo_vch_dpto = (select sgo_vch_dpto from tbl_sgo_ubigeo where sgo_int_ubigeo = tra.sgo_int_ubigeo) and sgo_vch_dist = '00')))) as sgo_vch_direcciontransportista,
                    persona.sgo_int_persona,
        			persona.sgo_vch_nrodocumentoidentidad,
        			guiadet.sgo_vch_item,
        			ifnull(case 	when art.sgo_vch_textoimpresion is null or art.sgo_vch_textoimpresion = '' 
					  		then concat(ifnull(guiadet.sgo_vch_descripcion,''),' - ', art.sgo_vch_codigo)
					  		else concat(ifnull(art.sgo_vch_textoimpresion,''),' - ', art.sgo_vch_codigo)
					end,guiadet.sgo_vch_descripcion) as sgo_vch_descripcion,
        			sum(guiadet.sgo_dec_cantidad) as sgo_dec_cantidad,
               		ifnull(ocdet.sgo_int_unidadesporbulto,0) as sgo_int_unidadesporbulto,
        			ifnull(group_concat(distinct oc.sgo_vch_nroordencompracliente),'') as sgo_vch_nroordencompracliente,
        			guia.sgo_int_motivo,
        			guia.sgo_dat_fecha,
        			guia.sgo_dat_fechadespacho,
        			dir2.sgo_vch_codigotienda,
        			dir2.sgo_vch_nombretienda,
        			oc.sgo_vch_albaran,
        			cliente.sgo_int_division,
        			cliente.sgo_int_cliente,
        			dir2.sgo_vch_direccion as dirtienda,
        			guia.sgo_vch_precinto,
        			ifnull(tra.sgo_vch_razonsocial,'') as razonsocialtransportista,
        			ifnull(tra.sgo_vch_nrodocumentoidentidad,'') as dctoidentidadtransportista,
        			ifnull(tra.sgo_vch_direccion,'') as direcciontransportista,
        			ifnull(cho.sgo_vch_chofer,'') as chofer,
        			ifnull(cho.sgo_vch_licencia,'') as licencia,
        			ifnull(veh.sgo_vch_marcamodelo,'') as marcamodelo,
        			ifnull(veh.sgo_vch_placa,'') as placa,
        			ifnull(veh.sgo_vch_certificado,'') as certificado,
        			ifnull(ubi.sgo_vch_descripcion,'') as ubigeo
        FROM	 	tbl_sgo_guiaremision guia
        inner	 	join tbl_sgo_guiaremisiondetalle guiadet
        on	 		guia.sgo_int_guiaremision = guiadet.sgo_int_guiaremision
        inner	 	join tbl_sgo_cliente cliente
        on	 		guia.sgo_int_clienteguia = cliente.sgo_int_cliente
        inner	 	join tbl_sgo_persona persona
        on	 		persona.sgo_int_persona = cliente.sgo_int_cliente
        inner	 	join tbl_sgo_direccioncliente direccion
        on	 		guia.sgo_int_direccion = direccion.sgo_int_direccion
        left	 	join tbl_sgo_ordenservicio oc
        on	 		oc.sgo_int_ordenservicio = guiadet.sgo_int_ordenservicio
        left	 	join tbl_sgo_ordenserviciodetalle ocdet
        on	 		guiadet.sgo_int_ordenserviciodetalle = ocdet.sgo_int_ordenserviciodetalle        
        left	 	join tbl_sgo_direccioncliente dir2
        on	 		dir2.sgo_int_direccion = ocdet.sgo_int_direccion        
        left 		join tbl_sgo_transportista tra
        on 			tra.sgo_int_transportista = guia.sgo_int_transportista
        left		join tbl_sgo_vehiculo veh
        on			guia.sgo_int_vehiculo = veh.sgo_int_vehiculo
        left		join tbl_sgo_chofer cho
        on			guia.sgo_int_chofer = cho.sgo_int_chofer
        left	 	join tbl_sgo_ubigeo ubi
        on	 		ubi.sgo_int_ubigeo = tra.sgo_int_ubigeo
        left 		join tbl_sgo_producto art
        on 			art.sgo_int_producto = ocdet.sgo_int_producto
        LEFT		JOIN tbl_sgo_categoriaproductocolor catprodcol
   		on			art.sgo_int_categoriaproducto = catprodcol.sgo_int_categoriaproducto
   		and			art.sgo_int_color = catprodcol.sgo_int_color
   		LEFT		JOIN tbl_sgo_categoriaproductotamano catprodtam
   		on			art.sgo_int_categoriaproducto = catprodtam.sgo_int_categoriaproducto
   		and			art.sgo_int_tamano = catprodtam.sgo_int_tamano	
   		LEFT		JOIN tbl_sgo_categoriaproductocalidad catprodcal
   		on			art.sgo_int_categoriaproducto = catprodcal.sgo_int_categoriaproducto
   		and			art.sgo_int_calidad = catprodcal.sgo_int_calidad
        WHERE 		guia.sgo_int_guiaremision=" . $_REQUEST["prm"]."
        group 		by case when case when art.sgo_vch_textoimpresion is null or art.sgo_vch_textoimpresion='' then CONCAT(ifnull(art.sgo_vch_nombre,''),' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) else art.sgo_vch_textoimpresion end ='' then guiadet.sgo_vch_descripcion else art.sgo_int_producto end 
        order 		by case when case when art.sgo_vch_textoimpresion is null or art.sgo_vch_textoimpresion='' then CONCAT(ifnull(art.sgo_vch_nombre,''),' ',ifnull(catprodcol.sgo_vch_color,''), ' ', ifnull(catprodtam.sgo_vch_tamano,''), ' ',ifnull(catprodcal.sgo_vch_calidad,'')) else art.sgo_vch_textoimpresion end ='' then guiadet.sgo_int_guiaremisiondetalle else guiadet.sgo_int_ordenserviciodetalle end";
        //group by art.sgo_int_producto
        //ORDER BY guiadet.sgo_int_ordenserviciodetalle";

      $result=$Obj->consulta($sql);$i=0;
      $persona="";$unidadesporbulto=0;$marcamodelo="";$placa="";$certificado="";$codigoProd="";$documetopersona="";$ubigeo="";$direcciontransportista="";$dctoidentidadtransportista="";$razonsocialtransportista="";$precinto="";$direcciontienda="";$cliente="";$division="";$razonsocial=""; $direccion=""; $codigo=""; $tienda=""; $albaran=""; $totalun=0; $totalbultos=0; $documento=""; $chofer=""; $licencia=""; $informaciontransporte=""; $fecha=""; $fechadespacho=""; $ordencompra=""; $motivo="";
      while ($row = mysqli_fetch_array($result))
      {
          if($i==0){
            $persona=$row["sgo_int_persona"];
            $marcamodelo=$row["marcamodelo"];
            $placa=$row["placa"];
            $certificado=$row["certificado"];
            $documetopersona=$row["sgo_vch_nrodocumentoidentidad"];
            $razonsocial= utf8_decode(str_replace("Ñ","&#209;",$row["sgo_vch_nombre"]));
			$direccion=$row["sgo_vch_direccion"];
			$documento=$row["sgo_vch_nrodocumentoidentidad"];
			$chofer=$row["chofer"];
			$licencia=$row["licencia"];
			$fecha=strtotime($row["sgo_dat_fecha"]);
			$fechadespacho = strtotime($row["sgo_dat_fechadespacho"]);
			$ordencompra=$row["sgo_vch_nroordencompracliente"];
			$motivo=$row["sgo_int_motivo"];
			$codigo=$row["sgo_vch_codigotienda"];
			$tienda=str_replace("ó", "&#243;", $row["sgo_vch_nombretienda"]);
			$albaran=$row["sgo_vch_albaran"];
            $division=$row["sgo_int_division"];
            $cliente=$row["sgo_int_cliente"];
            $direcciontienda=$row["dirtienda"];
            $precinto=$row["sgo_vch_precinto"];
            $razonsocialtransportista=$row["razonsocialtransportista"];
            $dctoidentidadtransportista=$row["dctoidentidadtransportista"];
            $direcciontransportista=$row["sgo_vch_direcciontransportista"];
            $ubigeo=$row["ubigeo"];
          }
          //$totalun += $row["sgo_dec_cantidad"];
          //$totalunidadesxBulto=$row["sgo_int_unidadesporbulto"];
		  $i++;
          $data[] = array('item'=>$i, 'descripcion'=>"   " . str_replace("Ñ","&#209;",$row["sgo_vch_descripcion"]), 'cantidad'=>$row["sgo_dec_cantidad"], 'unidadesxbulto'=>$row["sgo_int_unidadesporbulto"]);
      }
$paginas = ($i)/$itemsPagina;
$valx = explode('.',$paginas);
if($valx[1]==0)
{
  $paginas=$valx[0];
}
else
{
  $paginas=$valx[0]+1;
}
for($m=0;$m<$paginas;$m++)
{
fwrite($file,"<?xml version='1.0' encoding='iso-8859-1'?>");
fwrite($file,  "<GuiaRemision><Cabecera><Cliente>");
fwrite($file,  "<RazonSocial><![CDATA[".$razonsocial."]]></RazonSocial>");
fwrite($file,  "<DireccionDestino><![CDATA[".utf8_decode(str_replace("Ñ","&#209;",$direccion))."]]></DireccionDestino>");
fwrite($file,  "<Ruc>".$documento."</Ruc>");
switch($motivo)
        {
          case 1: fwrite($file,  "<Tipo>venta</Tipo>");
          break;
          case 2: fwrite($file,  "<Tipo>venta</Tipo>");
          break;
          case 3: fwrite($file,  "<Tipo>venta</Tipo>");
          break;
          case 4: fwrite($file,  "<Tipo>consignacion</Tipo>");
          break;
          case 5: fwrite($file,  "<Tipo>devolucion</Tipo>");
          break;          
          case 7: fwrite($file,  "<Tipo>transformacion</Tipo>");
          break;
          case 6: 
          case 8:
          case 9:
          case 10:
          case 11:
          case 12:
          case 13:	
          		  fwrite($file,  "<Tipo>devolucion</Tipo>");
          break;
          
        }
fwrite($file,  "</Cliente>");        
fwrite($file,  "<Transportista>");
fwrite($file,  "<RazonSocial>".utf8_decode($razonsocialtransportista)."</RazonSocial>");
fwrite($file,  "<Direccion>".trim(utf8_decode($direcciontransportista))."</Direccion>");
fwrite($file,  "<Chofer>");
fwrite($file,  "<Nombre>".utf8_decode($chofer)."</Nombre>");
fwrite($file,  "<Licencia>".utf8_decode($licencia)."</Licencia>");
fwrite($file,  "</Chofer>");
fwrite($file,  "<Vehiculo>");
fwrite($file,  "<Modelo>".utf8_decode($marcamodelo)."</Modelo>");
fwrite($file,  "<Placa>".$placa."</Placa>");
fwrite($file,  "<Certificado>".utf8_decode($certificado)."</Certificado>");
fwrite($file,  "</Vehiculo>");
fwrite($file,  "<Ruc>".$dctoidentidadtransportista."</Ruc>");
fwrite($file,  "</Transportista>");
fwrite($file,  "<Fecha>".date("d",$fecha) ."/". date("m",$fecha) ."/". date("Y",$fecha)."</Fecha>");
fwrite($file,  "<Remitente>");
fwrite($file,  "<FechaDespacho>".date("d",$fechadespacho) ."/". date("m",$fechadespacho) ."/". date("Y",$fechadespacho)."</FechaDespacho>");
fwrite($file,  "<RazonSocial>GALVER SAC</RazonSocial>");
fwrite($file,  "<Ruc>20503322090</Ruc>");
fwrite($file,  "<Direccion>AV. VIA DE EVITAMIENTO MZ D LT 16 URB. SAN FRANCISCO - ATE, LIMA, LIMA</Direccion>");
fwrite($file,  "</Remitente>");
fwrite($file,  "</Cabecera>");
fwrite($file,  "<Detalles>");
//Imprimiendo la data
$itemIndex=1;
$totalbultos=0;
$totalun=0;
for($i=($m*$itemsPagina);$i<($itemsPagina*($m+1));$i++)
{
  if(count($data)==$i)
  {
    break;
  }
fwrite($file,  "<Detalle>");
fwrite($file,  "<Item>".$itemIndex."</Item>");
fwrite($file,  "<Descripcion><![CDATA[".utf8_decode(str_replace("&", "&amp;",$data[$i]['descripcion']))."]]></Descripcion>");
fwrite($file,  "<Cantidad>".$data[$i]['cantidad']."</Cantidad>");
fwrite($file,  "<UnBulto>".$data[$i]['unidadesxbulto']."</UnBulto>");
fwrite($file,  "</Detalle>");
  //calculando el total de bultos
  if($data[$i]['unidadesxbulto']==0)
  {
    $totalbultos +=1;
  }
  else
  {
    $totalbultos += $data[$i]['cantidad'] / $data[$i]['unidadesxbulto'];
  }
  $totalun += $data[$i]['cantidad'];
  $itemIndex++;
}
$aux = explode('.',$totalbultos);
if($aux[1]>0)
{
    $totalbultos=$aux[0]+1;
}
}
fwrite($file,  "</Detalles>");
fwrite($file,  "<Sellos><Sello>");
if($division==1)
{
  if($documetopersona=="20100128056")  //saga falabella
  {  	
    fwrite($file,  "<Tienda>".$codigo."</Tienda>");
    fwrite($file,  "<Nombre>".utf8_decode($tienda)."</Nombre>");
    fwrite($file,  "<Albaran>".$albaran."</Albaran>");
    fwrite($file,  "<OC>".$ordencompra."</OC>");
    fwrite($file,  "<Unidades>".$totalun."</Unidades>");
    fwrite($file,  "<Bultos>".$totalbultos."</Bultos>");   	    
  }
  elseif($documetopersona=="20109072177" && $persona==121)  //almacenes paris
  {
  	fwrite($file,  "<Tienda>".$codigo."</Tienda>");
    fwrite($file,  "<Nombre>".utf8_decode($tienda)."</Nombre>");
    fwrite($file,  "<Precintos>".$precinto."</Precintos>");
    fwrite($file,  "<Direccion>".$direcciontienda."</Direccion>");
    fwrite($file,  "<OC>".$ordencompra."</OC>");
    fwrite($file,  "<Unidades>".$totalun."</Unidades>");
    fwrite($file,  "<Bultos>".$totalbultos."</Bultos>");           	             
  }
  elseif($documetopersona=="20508565934")  //hipermercados Tottus
  { 
  	fwrite($file,  "<Tienda>".$codigo."</Tienda>");
    fwrite($file,  "<Nombre>".utf8_decode($tienda)."</Nombre>");
    fwrite($file,  "<Albaran>".$albaran."</Albaran>");
    fwrite($file,  "<OC>".$ordencompra."</OC>");
    fwrite($file,  "<Unidades>".$totalun."</Unidades>");
    fwrite($file,  "<Bultos>".$totalbultos."</Bultos>"); 	       		 
  }
  elseif($documetopersona=="20109072177" && $persona==14)  //hipermercados Metro
  { 
  	fwrite($file,  "<Tienda>".$codigo."</Tienda>");
    fwrite($file,  "<Nombre>".utf8_decode($tienda)."</Nombre>");    
    fwrite($file,  "<Unidades>".$totalun."</Unidades>");
    fwrite($file,  "<OC>".$ordencompra."</OC>");
    fwrite($file,  "<Bultos>".$totalbultos."</Bultos>");     	           	    
  }
  else{  	
    fwrite($file,  "<OC>".$ordencompra."</OC>");
    fwrite($file,  "<Bultos>".$totalbultos."</Bultos>");   	
  }

}
else
{
    fwrite($file,  "<OC>".$ordencompra."</OC>");
    fwrite($file,  "<Bultos>".$totalbultos."</Bultos>");   
}
fwrite($file,  "</Sello></Sellos></GuiaRemision>");
fclose($file);
if ( function_exists('gzwrite') ) 
{
	$fp = fopen("guia.xml", "r"); 
	$data = fread ($fp, filesize("guia.xml")); 
	fclose($fp); 
	$zp = gzopen("guia.xml.gz", "w9"); 
	gzwrite($zp, $data); 
	gzclose($zp);
	echo "<script language='javascript'> self.location.href='guia.xml.gz';  </script>"; 	
}
}
else
{
  echo "El archivo no puede ser modificado";
}

//echo $sql;
?>

