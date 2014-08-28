<?php
class Zend_View_Helper_GetAuthLink extends Zend_View_Helper_Abstract
{
   function GetAuthLink($message = 'Login')
    {
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            //$username = $auth->getIdentity()->fname;
            return "<a href='/my/account' class='lite btn btn-small btn-primary'>My Account</a> 
                    <a href='/logout' class='btn btn-small'>Logout</a>";

        } 

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        
        if($controller == 'auth' && $action == 'index'){
            return '';
        }
        
        
        return "<a href='/login' class='login'>{$message}</a>";
    }

}
