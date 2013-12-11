<?php
  Class uploadhelper{
   function grabar_archivo($dir,$id,$file){
/*    $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
//            'exe' => 'application/x-msdownload',
//            'msi' => 'application/x-msdownload',
//            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );          */
        $arrayExtensions = array('.txt',
            '.htm',
            '.html',
            '.php',
            '.css',
            '.js',
            '.json',
            '.xml',
            '.swf',
            '.flv',
            '.png',
            '.jpe',
            '.jpeg',
            '.jpg',
            '.gif',
            '.bmp',
            '.ico',
            '.tiff',
            '.tif',
            '.svg',
            '.svgz',
            '.zip',
            '.rar',
//            'exe' => 'application/x-msdownload',
//            'msi' => 'application/x-msdownload',
//            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            '.mp3',
            '.qt',
            '.mov',

            // adobe
            '.pdf',
            '.psd',
            '.ai',
            '.eps',
            '.ps',

            // ms office
            '.doc',
            '.docx',
            '.rtf',
            '.xls',
            '.xlsx',
            '.ppt',
            '.pptx',

            // open office
            '.odt',
            '.ods',
        );
        $extension = (false === $pos = strrpos($file['name'], '.')) ? '' : substr($file['name'], $pos);
//        echo 'tipo: ' . $file['type'] . ' - search:' . array_search($file['type'],$mime_types);
           if(isset($file['tmp_name'])){
             if(in_array($extension, $arrayExtensions)){
             //if(array_search($file['type'],$mime_types)!==false){
              if(!copy($file['tmp_name'],$dir.$file['name']))
                return '<script>parent.Upload_Result("'.$id.'",3,"'.$file['name'].'","'.$dir.'");</script>';
              else return '<script>parent.Upload_Result("'.$id.'",0,"'.$file['name'].'","'.$dir.'");</script>';
             }else return '<script>parent.Upload_Result("'.$id.'",2,"'.$file['name'].'","'.$dir.'");</script>';
          }else return '<script>parent.Upload_Result("'.$id.'",1,"'.$file['name'].'","'.$dir.'");</script>';
   }
   function form_cargar_archivo($dir,$id,$file,$arrayExtensions){
        $extension = (false === $pos = strrpos($file['name'], '.')) ? '' : substr($file['name'], $pos);
           if(isset($file['tmp_name'])){
             if(in_array($extension, $arrayExtensions)){
              if(!copy($file['tmp_name'],$dir.$file['name']))
                return '<script>parent.Upload_Result("'.$id.'",3,"'.$file['name'].'","'.$dir.'");</script>';
              else return 1;
             }else return '<script>parent.Upload_Result("'.$id.'",2,"'.$file['name'].'","'.$dir.'");</script>';
          }else return '<script>parent.Upload_Result("'.$id.'",1,"'.$file['name'].'","'.$dir.'");</script>';
   }
 }
?>
