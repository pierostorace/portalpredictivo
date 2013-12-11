<?php include('../../code/lib/class.ezpdf.php'); include('../../code/lib/htmlhelper.php');
//$pdf = new Cezpdf("LETTER","LANDSCAPE"); // A4
$pdf = new Cezpdf("LETTER"); // A4
$pdf->selectFont('../../code/lib/fonts/Helvetica.afm');
$pdf->ezSetCmMargins(1.2,1,1,1); //$top,$bottom,$left,$right

$Obj=new mysqlhelper;$Helper=new htmlhelper;
$sql ="SELECT Concat(co.sgo_vch_serie,'-',co.sgo_vch_numero) as Comprobante,per.sgo_vch_nombre as Persona,dir.sgo_vch_direccion as Direccion, per.sgo_vch_nrodocumentoidentidad as RUC,
      co.sgo_dat_fechaemision as Fecha,group_concat(Concat(oco.sgo_vch_serie,'-',oco.sgo_vch_numero)) as OS,group_concat(Concat(grem.sgo_vch_serie,'-',grem.sgo_vch_numero)) as GuiaRemision,
      mon.sgo_vch_simbolo as Simbolo,mon.sgo_vch_nombre as Moneda,co.sgo_dec_subtotal as Subtotal,co.sgo_dec_igv as Igv, co.sgo_dec_total as Total,efa.sgo_vch_descripcion as Estado,
      codet.sgo_dec_cantidad as Cant,codet.sgo_vch_descripcion as Descripcion,codet.sgo_dec_precio as Precio,codet.sgo_dec_valorventa as Valor,
      group_concat(Concat(co2.sgo_vch_serie,'-',co2.sgo_vch_numero)) as Facturas,tco.sgo_vch_descripcion as Referencia,co2.sgo_dat_fechaemision as Fecha2
      FROM tbl_sgo_comprobanteventa co
      INNER JOIN tbl_sgo_comprobanteventadetalle codet on codet.sgo_int_comprobanteventa=co.sgo_int_comprobanteventa
      INNER JOIN tbl_sgo_persona per on per.sgo_int_persona=co.sgo_int_cliente
      INNER JOIN tbl_sgo_moneda mon on mon.sgo_int_moneda=co.sgo_int_moneda
      INNER JOIN tbl_sgo_estadocomprobante efa on efa.sgo_int_estadocomprobante=co.sgo_int_estadocomprobante
      LEFT JOIN tbl_sgo_direccioncliente dir on dir.sgo_int_direccion=co.sgo_int_direccion
      LEFT JOIN tbl_sgo_ordenservicio oco on oco.sgo_int_ordenservicio=codet.sgo_int_ordenservicio
      LEFT JOIN tbl_sgo_guiaremisiondetalle gremdet on gremdet.sgo_int_ordenservicio=oco.sgo_int_ordenservicio
      LEFT JOIN tbl_sgo_guiaremision grem on grem.sgo_int_guiaremision=gremdet.sgo_int_guiaremision
      INNER JOIN tbl_sgo_comprobanteventadocreferencia coref ON coref.sgo_int_docreferenciador=co.sgo_int_comprobanteventa
      INNER JOIN tbl_sgo_comprobanteventa co2 ON co2.sgo_int_comprobanteventa=coref.sgo_int_docreferenciado
      INNER JOIN tbl_sgo_tipocomprobante tco ON tco.sgo_int_tipocomprobante=co2.sgo_int_tipocomprobante
      WHERE co.sgo_int_comprobanteventa=" . $_REQUEST["prm"] . "
      GROUP BY co.sgo_vch_serie,co.sgo_vch_numero,codet.sgo_vch_descripcion,co.sgo_dat_fechaemision,co.sgo_dec_subtotal,co.sgo_dec_igv, co.sgo_dec_total,codet.sgo_dec_cantidad,codet.sgo_vch_descripcion,codet.sgo_dec_precio,codet.sgo_dec_valorventa,co2.sgo_vch_serie,co2.sgo_vch_numero,tco.sgo_vch_descripcion";
      $result=$Obj->consulta($sql);$i=0;
      $comprobante="";$cliente="";$direccion="";$ruc="";$fecha="";$simbolo="";$moneda="";$oc="";$grem="";$subtotal=0;$igv=0;$total=0;$estado="";$telefono="";$referencia="";$factura="";
      while ($row = mysqli_fetch_array($result))
      {
          if($i==0){
            $comprobante=$row["Comprobante"];$cliente=$row["Persona"];$direccion=$row["Direccion"];$ruc=$row["RUC"];$fecha=strtotime($row["Fecha"]);
            $oc=$row["OS"];$grem=$row["GuiaRemision"];$simbolo=$row["Simbolo"];$moneda=$row["Moneda"];$subtotal=$row["Subtotal"];$igv=$row["Igv"];$total=$row["Total"];$estado=$row["Estado"];
            $telefono="";$referencia=$row["Referencia"];$factura=$row["Facturas"];$fecha2=strtotime($row["Fecha2"]);
            $i++;
          }
          $data[] = array('cant'=>"  " . $row["Cant"],'descripcion'=>"              " . $row["Descripcion"], 'precio'=>$row["Precio"], 'valor'=>$row["Valor"]);
      }

//Header
$pdf->ezText("\n\n\n\n\n\n<b>" . $comprobante . "</b>",0,array('left'=>400));//Comprobante
$pdf->ezText("\n<b>" . $cliente . "</b>",0,array('left'=>65));//Señores
$pdf->ezText("\n<b>" . $ruc . "</b>",0,array('left'=>65));//RUC
$pdf->ezText("\n<b>" . date("d",$fecha) . " / " . date("m",$fecha) . " / " . date("Y",$fecha) . "</b>",0,array('left'=>110));//Fecha
$pdf->ezText($referencia,8,array('left'=>400,'atop'=>-20));//Referencia
$pdf->ezText($factura,8,array('left'=>365,'atop'=>-15));//Factura
$pdf->ezText(date("d",$fecha) . " / " . date("m",$fecha) . " / " . date("Y",$fecha),8,array('left'=>400,'atop'=>-5));//Fecha
//$pdf->ezText("\n<b>" . $direccion . "</b>",0,array('left'=>85));//Dirección
//$pdf->ezText("<b>" . $telefono . "</b>\n\n",0,array('left'=>350,'top'=>1));//RUC
//$pdf->ezText("\n<b>" . $referencia . "</b>",0,array('left'=>85));//Referencia
//$pdf->ezText("\n<b>" . $factura . "</b>",0,array('left'=>285,'top'=>1));//Factura
$pdf->ezText("\n\n\n");
//Body
$titles = array('cant'=>'<b>Cantidad</b>','descripcion'=>'<b>Descripcion</b>', 'precio'=>'<b>P. Unitario</b>', 'valor'=>'<b>Valor de Venta</b>');
$options = array('showLines'=>0,'showHeadings'=>0,'shaded'=>0,'xOrientation'=>'center','width'=>515,
                'cols'=>array('cant'=>array('justification'=>'left','width'=>45),'descripcion'=>array('justification'=>'left','width'=>340),'precio'=>array('justification'=>'right','width'=>60),'valor'=>array('justification'=>'right','width'=>90))
            );
$pdf->ezTable($data,$titles,"",$options );

//Footer
$pdf->ezSetY(410); // Lugar donde empezará de abajo a arriba.
//$pdf->ezText($Helper->numtoletras($total,$moneda),0,array('left'=>60));//nroGuia
$pdf->ezText($simbolo,0,array('left'=>480,'top'=>1));//Moneda
$pdf->ezText($subtotal,0,array('left'=>490,'top'=>1,'width'=>50,'atop'=>3,'justification'=>'right'));//SubTotal
$pdf->ezText("\n" . $simbolo,0,array('left'=>480,'atop'=>-3));//Moneda
$pdf->ezText($igv,0,array('left'=>490,'top'=>1,'width'=>50,'justification'=>'right'));//IGV
$pdf->ezText("\n" . $simbolo,0,array('left'=>480,'atop'=>-5));//Moneda
$pdf->ezText($total,0,array('left'=>490,'top'=>1,'width'=>50,'atop'=>-6,'justification'=>'right'));//Total
$pdf->ezStream();
?>