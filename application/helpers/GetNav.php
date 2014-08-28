<?php
class Zend_View_Helper_GetNav extends Zend_View_Helper_Abstract
{
   function GetNav($id = 1)
    {
        return Default_Model_PagesModules::getNav($id);
    }

}
