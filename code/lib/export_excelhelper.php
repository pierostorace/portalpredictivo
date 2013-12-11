<?php

	include('../../code/lib/loghelper.php');
    //header('Content-type: application/ms-excel');
    $objloghelper = new loghelper;
    header("Content-Type: application/vnd.ms-excel;charset=utf-8");
    header("Expires: 0");
    header("Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0");
    header("content-disposition: attachment;filename=excelreport.xls");
    
    //session_start();
    //$grilla = $_SESSION["grilla"];
    $grilla = utf8_decode($_POST["hfGrilla"]);
    //$objloghelper->log("GRILLA DESDE LA SESIÓN: ".$grilla);
    $count = strlen($grilla);
    $posini=0;$posfin=0;
    while($posini!==false){
        $posini=stripos($grilla,"<img ",$posini);
        if($posini!==false){
           $posfin=stripos($grilla,"/>",$posini);
           $substring=substr($grilla,$posini,($posfin+2-$posini));
           $grilla = str_replace($substring,"",$grilla);
        }
    }
    $posini=0;$posfin=0;
    while($posini!==false){
      $posini=stripos($grilla,'<td class="no_print',$posini);
      if($posini!==false){
         $posfin=stripos($grilla,"</td>",$posini);
         $substring=substr($grilla,$posini,($posfin+5-$posini));
         $grilla = str_replace($substring,"",$grilla);
      }
    }
/*    $posini=0;$posfin=0;
    $posini=stripos($grilla,"<tr class='tituloTablaResultado",$posini);
    if($posini!==false){
       $posfin=stripos($grilla,"</tr>",$posini);
       $substring=substr($grilla,$posini,($posfin+5-$posini));
       $grilla = str_replace($substring,"",$grilla);
    }*/
    $posini=0;$posfin=0;
    $posini=stripos($grilla,"<tr class='toolboxTablaResultado",$posini);
    if($posini!==false){
       $posfin=stripos($grilla,"</tr>",$posini);
       $substring=substr($grilla,$posini,($posfin+5-$posini));
       $grilla = str_replace($substring,"",$grilla);
    }
    $grilla = str_replace("Â Â Â Â ","",$grilla);
    $grilla = str_replace("Â","",$grilla);
    $grilla = str_replace("class","style",$grilla);
    $grilla = str_replace("cabeceraTablaResultado","font-family:Arial;font-size:20px;color:#000;font-weight:bold;height:30px;",$grilla);
    $grilla = str_replace("tituloTablaResultado","font-family:Arial;font-size:20px;color:#000;height:25px;",$grilla);
//    $grilla = str_replace("tituloTablaCelda","padding-left:15px;",$grilla);
    $grilla = str_replace("tituloTablaCelda","",$grilla);
    $grilla = str_replace("toolboxTablaResultado","font-family:Arial;font-size:20px;color:#000;font-weight:bold;height:30px;",$grilla);
    $grilla = str_replace("filaTablaResultado","padding:3px;border:solid #000 1px;font-size:20px;height:30px;",$grilla);
    $grilla = str_replace("textoData","font-family:Arial;font-size:20px;color:#000;text-decoration:none;",$grilla);
    $grilla = str_replace(" center","text-align:center;",$grilla);
    $grilla = str_replace("no_print ","display:none;",$grilla);
    $grilla = str_replace(array("link","no_sort"),"",$grilla);
    include_once('htmlhelper.php'); $Helper=new htmlhelper;
    echo "<br/>";
    //echo "<img src='http://www.galverperu.com/portal/img/logo.jpg' /><br/><br/><br/><br/><br/><br/>";
//    echo "Empresa: " . $_SESSION["empresa"]["nombre"] . "<br/>";
//    echo "Usuario: " . $_SESSION["usuario"][1]." ".$_SESSION["usuario"][2] . "<br/>";
    //echo "Emitido: " . $Helper->Fecha_Castellano(time()) . "<br/><br/>";
    //$objloghelper->log("GRILLA DESPUÉS DE LA TRANSFORMACIÓN: ".$grilla);
	echo $grilla;
	
?>

