<?php
class Zend_View_Helper_Truncate extends Zend_View_Helper_Abstract
{
    function Truncate($str, $limit = 50, $appendix='...'){
        
       return  Base_Functions_Strings::truncate($str, $limit, $appendix);
        
    }
}