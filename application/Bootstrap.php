<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initGlobal() {
    
        date_default_timezone_set("America/Kentucky/Monticello");
    
    
        define('SITE_SSL', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://' );
        define('SITE_URL',    SITE_SSL.$_SERVER['HTTP_HOST']);
        define('SITE_EMAIL', 'info@'.$_SERVER['HTTP_HOST']);
        define('SITE_NAME',  'SkillSource Group, Inc.');
        define('SITE_NAME_FANCY',  '<b><i>SkillSource</i></b> Group, Inc.');
        define('LOGO_NAME',  'SSG');
    }
    
    protected function _initAuth() {
        $fc = Zend_Controller_Front::getInstance();
        $acl = new Main_Acl();
        $fc->registerPlugin(new Plugin_Acl($acl));
        $auth = Zend_Auth::getInstance();
        define('LOGGED_IN',  $auth->hasIdentity() );
    }

    protected function _initRoutes() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routes');
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->removeDefaultRoutes();
        $router->addConfig($config,'routes');
    }
}
