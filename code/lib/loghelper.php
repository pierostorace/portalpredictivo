<?php
  Class loghelper{
   function log($mensaje){
		if (is_writable("../../log/log.txt")) 
		{		
			$file = fopen("../../log/log.txt", "a");		   	
			fwrite($file,$mensaje);		   	
			fwrite($file,"\r\n");
		   	fclose($file);
		}
   }      
 }
?>
