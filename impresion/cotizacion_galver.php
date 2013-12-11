<?php
include('../code/lib/class.ezpdf.php');
$pdf = new Cezpdf(); // A4
$pdf->selectFont('../code/lib/fonts/Helvetica.afm');
$pdf->ezSetCmMargins(1,1,1.5,1.5);
$datacreator = array (
					'Title'=>'Ejemplo PDF',
					'Author'=>'unijimpe',
					'Subject'=>'PDF con Tablas',
					'Creator'=>'unijimpe@hotmail.com',
					'Producer'=>'http://blog.unijimpe.net'
					);
$pdf->addInfo($datacreator);

$data[] = array('num'=>1, 'mes'=>'Enero');
$data[] = array('num'=>2, 'mes'=>'Febrero');
$data[] = array('num'=>3, 'mes'=>'Marzo');
$data[] = array('num'=>4, 'mes'=>'Abril');
$data[] = array('num'=>5, 'mes'=>'Mayo');
$data[] = array('num'=>6, 'mes'=>'Junio');
$data[] = array('num'=>7, 'mes'=>'Julio');
$data[] = array('num'=>8, 'mes'=>'Agosto');
$data[] = array('num'=>9, 'mes'=>'Septiembre');
$data[] = array('num'=>10, 'mes'=>'Octubre');
$data[] = array('num'=>11, 'mes'=>'Noviembre');
$data[] = array('num'=>12, 'mes'=>'Diciembre');

$titles = array('num'=>'<b>Numero</b>', 'mes'=>'<b>Mes</b>');
$options = array(
                'shadeCol'=>array(0.9,0.9,0.9),
                'xOrientation'=>'center',
                'width'=>500
            );
$pdf->ezText("<b>Meses en PHP</b>\n",16);
$pdf->ezText("Listado de Meses\n",12);
$pdf->ezTable($data,$titles,'Algo',$options );
$pdf->ezText("\n\n\n",10);
$pdf->ezText("<b>Fecha:</b> ".date("d/m/Y"),10);
$pdf->ezText("<b>Hora:</b> ".date("H:i:s")."\n\n",10);
$pdf->ezStream();
?>