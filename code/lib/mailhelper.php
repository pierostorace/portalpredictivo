<?php
Class mailhelper
{
    function enviarEmail($para, $subject, $cuerpo)
    {
      $cabeceras = "From: kevlar@galverperu.com\r\nContent-type: text/html\r\n";            
      mail($para,$subject,$cuerpo,$cabeceras);
    }    
}
?>