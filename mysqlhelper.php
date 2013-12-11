<?php
  date_default_timezone_set('America/Lima');
  Class mysqlhelper{
   function conexion_maestra($cad){
        //$mysqli = new mysqli("127.0.0.1", "root", "", "kevlarer_galver");
        $mysqli = new mysqli("localhost", "kevlarer_galver", "50gl@21m", "kevlarer_galver");
        $mysqli->set_charset("utf8");
        if ($mysqli->connect_errno) { printf("Connect failed: %s\n", $mysqli->connect_error); exit(); }
        //$acentos = $mysqli->query("SET NAMES 'utf8'");
        $mysqli->set_charset("utf8");
        $result = $mysqli->query($cad);
        if (!$result){ echo("<b> " . $mysqli->connect_error. "</b>\n"); }
        $mysqli->close();
        return $result;
   }
   function conexion(){
        error_reporting(NULL);
        //session_start();
        //$host="internal-db.s93488.gridserver.com"; $user="db93488_childs"; $pwd="childs2011"; $base="db93488_achildgrows";
        //$mysqli = new mysqli($_SESSION["empresa"]["host"], $_SESSION["empresa"]["user"], $_SESSION["empresa"]["pwd"], $_SESSION["empresa"]["base"]);
        //$mysqli = new mysqli("127.0.0.1", "root", "", "kevlarer_galver");
        $mysqli = new mysqli("localhost", "kevlarer_galver", "50gl@21m", "kevlarer_galver");
        //$acentos = $mysqli->query("SET NAMES 'utf8'");
        $mysqli->set_charset("utf8");        
        if ($mysqli->connect_errno) { printf("Connect failed: %s\n", $mysqli->connect_error); exit(); }
        return $mysqli;
   }
   function transaction(){
     $mysqli = $this->conexion();
     $mysqli->autocommit(FALSE);
     return $mysqli;
   }
   function consulta($cad){
        $mysqli = $this->conexion();
        $mysqli->set_charset("utf8");
        $result = $mysqli->query($cad);
        if (!$result){ echo("<b> " . $mysqli->connect_error. "</b>\n"); }
        $mysqli->close();
        return $result;
   }
  function ExecuteScalar($cad){
  	    $resp="";
        $mysqli = $this->conexion();
        $result = $mysqli->query($cad);
        if (!$result){ echo("<b> " . $mysqli->connect_error. "</b>\n"); }
        $mysqli->close();
        while($row = mysqli_fetch_row($result))
        {
        	$resp=$row[0];
        }
        return $resp;
   }
   function execute($cad){
        $mysqli = $this->conexion();
        $result = $mysqli->query($cad);
        if (!$result){ echo '<script>Ver_Mensaje("Error","' . $mysqli->error . '")</script>'; }
        $res = $mysqli->affected_rows;
        $mysqli->close();
        return $res;
   }
   function execute_insert($cad){
        $mysqli = $this->conexion();
        if($mysqli->query($cad)) $result=mysqli_insert_id($mysqli);
        else $result=0;
        $mysqli->close();
        return $result;
   }
   function sql_where($where,$prm)
   {
        //echo $where . " - " . $prm . "<br/><br/>";
        $valor = explode('|',$prm); $aux_valor="";
        $prm_count= count($valor);
        $variable = ""; $aux_indexof = "";
        $posini = 0; $posini2 = 0; $posfin = 0; $posfin_aux = 0; $index_valor = 0; $count_prm = 0; $indexof = 0; $posini_indexof = 0; $posfin_indexof = 0;
        do
        {
            $posini++;
            if($posini<strlen($where))$posini = strpos($where,"@", $posini);
            else $posini= "";
            if ($posini !="")
            {
                $posfin = strpos($where," ", $posini);
                $posfin_aux = strpos($where,"%", $posini);
                if ($posfin_aux != "" && $posfin_aux < ($posfin == "" ? strlen($where) : $posfin)) $posfin = $posfin_aux;
                $posfin_aux = strpos($where,"'", $posini);
                if ($posfin_aux != "" && $posfin_aux < ($posfin == "" ? strlen($where) : $posfin)) $posfin = $posfin_aux;
                $posfin_aux = strpos($where,")", $posini);
                if ($posfin_aux != "" && $posfin_aux < ($posfin == "" ? strlen($where) : $posfin)) $posfin = $posfin_aux;

                if ($posfin == "") $posfin = strlen($where);
                if ($index_valor < $prm_count)
                {
                    /*if (strpos($valor[$index_valor],"|") != "")
                    {
                        $aux_valor = explode($valor[$index_valor],'|');
                        if ($aux_valor[0] == "0")
                        {
                            $posini = strrpos($where," and ", $posini - 1);
                            if ($posini == "") $posini = 6;
                            else $posini += 5;
                            $variable = substr($where,$posini, ($posfin - $posini));
                            $where = str_replace($variable,$aux_valor[1],$where);
                        }
                        else
                        {
                            $variable = substr($where,$posini, ($posfin - $posini));
                            $where = str_replace($variable,$aux_valor[0],$where);
                        }
                    }
                    else*/ //if ($valor[$index_valor] == "0" || $valor[$index_valor] == "")
                    if ($valor[$index_valor] == "")
                    {
                        $posini = $this->strposrev($where," and ", $posini - 1);
                        if ($posini === false) $posini = 6;
                        $posfin = strpos($where," and ", $posfin);
                        if ($posfin === false) $posfin = strlen($where);
                        else if (strpos(substr($where,$posini, ($posfin - $posini)),"and ")=== false) $posfin += 4;
                        $variable = substr($where,$posini, ($posfin - $posini));
                        $posini2 = strpos($variable,'(');
                        if ($posini2 !== false && strpos($variable,"dbo")===false)
                        {
//                            echo "<br/>0.-" . $variable;
                            $posini2 = strpos($where,'(', $posini);
                            $posfin = strpos($where,')', $posini2) + 1;
                            $posfin_aux = strpos($where,"and ", $posfin);
                            if($posfin_aux!= "")$posfin = $posfin_aux + 4;
                            $variable = substr($where,$posini, ($posfin - $posini));
                            if ($posfin_aux !="")
                            {
                                //echo "<br/>1.-" . $variable;
                                //echo "<br/>2.-" . stripos($variable,"and");
                                if(stripos($variable,"and")==1 && $this->endsWith($variable,"and "))
                                    $variable=substr($variable,0,strlen($variable) - 5);
                            }
                        }
  //                      echo "<br/>3.- " . $variable;
                        $count_prm = -1; $indexof = true;
                        while ($indexof !== false)
                        {
                            $indexof = strpos($variable,"@p",$indexof+1);
                            if ($indexof !== false)
                            {
                                $posini_indexof = $indexof; $aux_indexof = "";
                                do
                                {
                                    $posini_indexof++;
                                    if ($posini_indexof < strlen($variable)) $aux_indexof = substr($variable,$posini_indexof, 1);
                                    else $aux_indexof = " ";
                                } while ($aux_indexof != " " && $aux_indexof != "=" && $aux_indexof != ">" && $aux_indexof != "<" && $aux_indexof != "!");
                                $aux_indexof = substr($variable,$indexof, ($posini_indexof - $indexof));
                                if (strpos($variable,$aux_indexof, $posini_indexof) !== false) $indexof = false;
                            }
                            ++$count_prm;
                        }
                        if ($count_prm > 1) $index_valor += ($count_prm - 1);
                        $where = str_replace($variable,"",$where);
                        //echo "<br/>4.-" . $variable;
                    }
                    else
                    {
                        $variable = substr($where,$posini, ($posfin - $posini));
                        if (strpos($variable,"'", 0) === false)
                            $where = str_replace($variable,$valor[$index_valor],$where);
                        else
                        {
                            $variable = substr($where,$posini, (($posfin - 1) - $posini));
                            $where = str_replace($variable,$valor[$index_valor],$where);
                        }
                    }
                }
                else
                {
                    $posini = strrpos($where,"and ", $posini - 1);
                    if ($posini === false) $posini = 6;
                    $posfin = strpos($where,"and", $posini);
                    if ($posfin === false) $posfin = strlen($where);
                    else $posfin += 4;
                    $variable = substr($where,$posini, ($posfin - $posini));
                    $where = str_replace($variable,"",$where);
                }
                $index_valor++;
            }
        } while ($posini != "");
        //echo "<BR/>WHERE: " . $where;
        return (trim($where)=="WHERE"?"":$where);
   }
   function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
    function strposrev($haystack, $needle,$offset)
    {
        $count = strlen($haystack);
        $posini = $count - $offset;
        $posini = strpos(strrev($haystack),strrev($needle), $posini);
        if ($posini === false) return false;
        else return ($count - $posini)-strlen($needle);
    }

 }
?>