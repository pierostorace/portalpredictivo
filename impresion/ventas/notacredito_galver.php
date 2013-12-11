<?php
include('../../code/lib/htmlhelper.php');include('../../code/bl/bl_facturacion.php');
$blFacturacion = new bl_facturacion;
//if (is_writable("notacredito.xml")) 
//{
$itemsPagina=100;
$file = fopen("notacredito.xml", "w");

$space_number=0;
$Obj=new mysqlhelper;$Helper=new htmlhelper;
$valor = explode('|',$_REQUEST["prm"]);
$id_comprobante = $valor[0];
$adelanto = explode('|',$blFacturacion->obtenerAdelantosImpresion($id_comprobante));
$tipo = 0;
$sql = "SELECT 	sgo_int_categoriacomprobante from tbl_sgo_comprobanteventa where sgo_int_comprobanteventa = ".$id_comprobante;
$result=$Obj->consulta($sql);
while ($row = mysqli_fetch_array($result))
{
	$tipo = $row["sgo_int_categoriacomprobante"];
	break;
}
fwrite($file,"<Notacredito>");
if(1==1) //Venta de productos
{
  //
        //
$sql ="SELECT 	Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as Comprobante,
			per.sgo_vch_nombre as Persona,
			concat (dir.sgo_vch_direccion,', ',trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_int_ubigeo = dir.sgo_int_ubigeo)),', ',
			trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_int_ubigeo = 
			(select sgo_int_ubigeo from tbl_sgo_ubigeo where sgo_vch_prov = (select sgo_vch_prov from tbl_sgo_ubigeo where sgo_int_ubigeo = dir.sgo_int_ubigeo)
			and sgo_vch_dpto = (select sgo_vch_dpto from tbl_sgo_ubigeo where sgo_int_ubigeo = dir.sgo_int_ubigeo) and sgo_vch_dist = '00'))),', ',
			trim((select sgo_vch_descripcion from tbl_sgo_ubigeo where sgo_int_ubigeo = 
			(select sgo_int_ubigeo from tbl_sgo_ubigeo where sgo_vch_prov = '00'
			and sgo_vch_dpto = (select sgo_vch_dpto from tbl_sgo_ubigeo where sgo_int_ubigeo = dir.sgo_int_ubigeo) and sgo_vch_dist = '00')))) as Direccion,
			per.sgo_vch_nrodocumentoidentidad as RUC,
			co.sgo_dat_fechaemision as Fecha,
			mon.sgo_vch_simbolo as Simbolo,
			mon.sgo_vch_nombre as Moneda,
			format(co.sgo_dec_subtotal,2) as Subtotal,
			format(co.sgo_dec_igv,2) as Igv,
			format(co.sgo_dec_total,2) as Total,
            co.sgo_dec_total as totalsf,
			case when sum(ifnull(codet.sgo_dec_cantidad,0)) > 0 then sum(ifnull(codet.sgo_dec_cantidad,0)) else '' end as Cant,
			ifnull(case when co.sgo_int_categoriacomprobante = 2 then
				case when pdt.sgo_vch_textoimpresion is null or pdt.sgo_vch_textoimpresion = '' 
					 then concat(ifnull(codet.sgo_vch_descripcion,''),' - ', pdt.sgo_vch_codigo)
				     else concat(ifnull(pdt.sgo_vch_textoimpresion,''),' - ', pdt.sgo_vch_codigo)
				end
			else
				 ifnull(codet.sgo_vch_descripcion,'')				 
			end,codet.sgo_vch_descripcion)  as Descripcion,			
			round(codet.sgo_dec_precio,2) as Precio,
			round( (sum(ifnull(codet.sgo_dec_cantidad,1)) * codet.sgo_dec_precio),2)  as Valor
  FROM 	tbl_sgo_comprobanteventa co
  INNER 	JOIN tbl_sgo_comprobanteventadetalle codet
  on 		codet.sgo_int_comprobanteventa=co.sgo_int_comprobanteventa
  INNER 	JOIN tbl_sgo_persona per
  on 		per.sgo_int_persona=co.sgo_int_clientefactura
  INNER 	JOIN tbl_sgo_moneda mon
  on 		mon.sgo_int_moneda=co.sgo_int_moneda
  LEFT 	JOIN tbl_sgo_direccioncliente dir
  on 		dir.sgo_int_direccion=co.sgo_int_direccion
  INNER	JOIN tbl_sgo_ubigeo ubi
  on		dir.sgo_int_ubigeo = ubi.sgo_int_ubigeo
  LEFT 	JOIN tbl_sgo_producto pdt
  on 		pdt.sgo_int_producto=codet.sgo_int_producto
  WHERE co.sgo_int_comprobanteventa=".$valor[0]."
  GROUP BY codet.sgo_int_producto
  ORDER BY codet.sgo_int_ordenserviciodetalle";  
  $result=$Obj->consulta($sql);$i=0;
  $totalsf=0;$comprobante="";$cliente="";$direccion="";$ruc="";$fecha="";$simbolo="";$moneda="";$oc="";$grem="";$subtotal=0;$igv=0;$total=0;$estado="";
  while ($row = mysqli_fetch_array($result))
  {
	  if($i==0){
		$comprobante=$row["Comprobante"];
		$cliente=$row["Persona"];
		$direccion=$row["Direccion"];
		$ruc=$row["RUC"];
		$fecha=strtotime($row["Fecha"]);
		$simbolo=$row["Simbolo"];
		$moneda=$row["Moneda"];
		$subtotal=$row["Subtotal"];
		$igv=$row["Igv"];
		$total=$row["Total"];
        $totalsf=$row["totalsf"];
		$estado=$row["Estado"];
		$i++;
	  }
	  $data[] = array('cant'=> $row["Cant"] , 'descripcion'=> $row["Descripcion"], 'precio'=> $row["Precio"] , 'valor'=> $row["Valor"]);
  }
$paginas = ($i+1)/$itemsPagina;
$valx = explode('.',$paginas);
$oc=$blFacturacion->obtenerOCPorComprobante($valor[0]);
$grem=$blFacturacion->obtenerGUIASPorComprobante($valor[0]);
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
//Header

fwrite($file,"<Cabecera>");
fwrite($file,"<Cliente>");
fwrite($file,"<RazonSocial>".$cliente."</RazonSocial>");
fwrite($file,"<Ruc>".$ruc."</Ruc>");
fwrite($file,"<Direccion>".utf8_decode($direccion)."</Direccion>");
fwrite($file,"<FechaEmision>");
fwrite($file,"<Dia>".date("d",$fecha)."</Dia>");
fwrite($file,"<Mes>".$Helper->mesLetras(date("m",$fecha))."</Mes>");
fwrite($file,"<Ano>".date("Y",$fecha)."</Ano>");
fwrite($file,"</FechaEmision>");
fwrite($file,"</Cliente>");
fwrite($file,"<Montos>");
fwrite($file,"<Monto>");
fwrite($file,"<Tipo>Subtotal</Tipo>");
fwrite($file,"<Moneda>".$moneda."</Moneda>");
fwrite($file,"<Valor>".$subtotal."</Valor>");
fwrite($file,"</Monto>");
fwrite($file,"<Monto>");
fwrite($file,"<Tipo>Igv</Tipo>");
fwrite($file,"<Moneda>".$moneda."</Moneda>");
fwrite($file,"<Valor>".$igv."</Valor>");
fwrite($file,"</Monto>");
fwrite($file,"<Monto>");
fwrite($file,"<Tipo>Total</Tipo>");
fwrite($file,"<Moneda>".$moneda."</Moneda>");
fwrite($file,"<Valor>".$total."</Valor>");
fwrite($file,"</Monto>");
fwrite($file,"</Montos>");

if($adelanto[1]!="")
{
    $textoAdelanto = $adelanto[0];
	$montoAdelanto = $adelanto[1];
	fwrite($file,"<Adelantos>");
            	
    	if(strlen($textoAdelanto)>0)
    	{
	    	fwrite($file,"<Adelanto>");
	    	fwrite($file,"<Texto>".utf8_decode($textoAdelanto)."</Texto>");
	    	fwrite($file,"<Moneda>S/.</Moneda>");
	    	fwrite($file,"<Valor>".$montoAdelanto."</Valor>");
	    	fwrite($file,"</Adelanto>");
    	}			
    
    fwrite($file,"</Adelantos>");
}
else 
{
	fwrite($file,"<Adelantos>");
    fwrite($file,"<Adelanto>");
  	fwrite($file,"<Texto></Texto>");
  	fwrite($file,"<Moneda></Moneda>");
  	fwrite($file,"<Valor>0</Valor>");
   	fwrite($file,"</Adelanto>");    	
    fwrite($file,"</Adelantos>");
}
fwrite($file,"</Cabecera>");
fwrite($file,"<Detalles>");
for($i=($m*$itemsPagina);$i<($itemsPagina*($m+1));$i++)
{
  if(count($data)==$i)
  {
    break;
  }
  fwrite($file,"<Detalle>");
  fwrite($file,"<Cantidad>".round($data[$i]['cant'],0)."</Cantidad>");
  fwrite($file,"<Description>".str_replace("Ñ","&#209;",str_replace("&", "&amp;",$data[$i]['descripcion']))."</Description>");
  fwrite($file,"<PrecioUnitario>".$data[$i]['precio']."</PrecioUnitario>");
  fwrite($file,"<ValorVenta>".$data[$i]['valor']."</ValorVenta>");
  fwrite($file,"</Detalle>");     
}
fwrite($file,"</Detalles>");
fwrite($file,"<Sellos><Sello>");  
$ocMod=$Helper->limpiarOCRepetidas($oc);  
$guiaMod=$Helper->limpiarGuiasRepetidas($grem);

  
fwrite($file,"<oc>");
  for($m=0;$m<count($ocMod);$m++)
  {    
    if($m==0)
    {
      if($m==count($ocMod)-1)
      {
        fwrite($file,$ocMod[$m]);
      }
      else
      {
        fwrite($file,$ocMod[$m].",");
      }

    }
    else
    {
        if($m==count($ocMod)-1)
        {
            fwrite($file,$ocMod[$m]);
        }
        else
        {
          fwrite($file,$ocMod[$m].",");
        }

    }
  }
  fwrite($file,"</oc>");
  fwrite($file, "<Guias>");
  for($m=0;$m<count($guiaMod);$m++)
  {    
    if($m==0)
    {
        if($m==count($guiaMod)-1)
        {
            fwrite($file,$guiaMod[$m]);
        }
        else
        {
            fwrite($file,$guiaMod[$m].",");
        }
    }
    else
    {
        if($m==count($guiaMod)-1)
        {
            fwrite($file,$guiaMod[$m]);
        }
        else
        {
          fwrite($file,$guiaMod[$m].",");
        }

    }
  }
  fwrite($file, "</Guias>");


  fwrite($file, "<Son>".utf8_decode($Helper->numtoletras($totalsf,$moneda))."</Son></Sello></Sellos>");
  
}  
}
else
{
	
}
fwrite($file,"</Notacredito>");
fclose($file);
fclose($file);
if ( function_exists('gzwrite') ) 
{
	$fp = fopen("notacredito.xml", "r"); 
	$data = fread ($fp, filesize("notacredito.xml")); 
	fclose($fp); 
	$zp = gzopen("notacredito.xml.gz", "w9"); 
	gzwrite($zp, $data); 
	gzclose($zp);
	echo "<script language='javascript'> self.location.href='notacredito.xml.gz';  </script>"; 	
}
//}else{echo "El archivo no puede ser modificado";}

//echo $sql;
?>