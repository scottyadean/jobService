<?php

class Base_Functions_Query {
    
    
    
         public static function ToRegExSearch($val="", $field = "id", $regEx = "equals") {
            
                $field = empty($field) ? 'name' : trim($field);
               
                switch($regEx) {
                    
                    case 'starts':
                    case 'start':
                    case 'begin':
                    case 'begins':
                        $val = "$val%";
                        $opt = 'LIKE';
                    break;
                
                    case "contains":
                    case "contain":
                        $val = "%$val%";
                        $opt = 'LIKE';                        
                    break;    

                    case "equals":
                    case "equal":
                        $val = "%$val%";
                        $opt = '=';                     
                    break;                 
                    
                };        
       
               
               if(empty($opt)) {
                   $val = "%$val%";
                   $opt = 'LIKE';
               }
       
               return array( "val"=>$val, "opt"=>$opt, "field"=>$field, "regEx"=>$regEx );
       
            
            
            
         }
    
    
    
}