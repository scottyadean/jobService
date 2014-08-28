<?php
class Zend_View_Helper_Crud extends Zend_View_Helper_Abstract
{
    
    /*
    * @param $month<int> two digit month
    * @param $year<int> four digit year
    */
    function Crud($action = 'create', $label="Create", $binder="", $params= "", $colspan=1) {
      
      if( strtolower($action) == 'create' ) {
            
        return "<tr><td colspan='".$colspan."'><a class='crud-create-update pull-right'
                                                          data-action='create'
                                                          data-bind='".$binder."'
                                                          data-params='".$params."'>".$label."</a></td></tr>";
      }
          
    }
}