<?php
class Zend_View_Helper_EchoIfSet extends Zend_View_Helper_Abstract
{
    function EchoIfSet($value, $prepend='', $append='', $default = false){

        if(!empty($value)) {
           return $prepend.strip_tags($value).$append;
            
        }
        
        if(!empty($default)) {
           return $prepend.strip_tags($default).$append;
        }
        
       return '';
    }
         
         
    
         
    
}

