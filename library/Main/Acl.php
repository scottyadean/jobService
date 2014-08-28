<?php
class Main_Acl extends Zend_Acl {

    public static $roles = array( 'guest', 'user', 'admin', 'power');
    
    public $resources = array( 'default' => array('index','error', 'auth', 'user', 'provider', 'event', 'program'),
                               'media'   => array('index'),
                               'admin'   => array('index', 'user', 'admin', 'page', 'provider', 'crud', 'module', 'event', 'qualifiers', 'program'));

    public $guests = array('default-index',
                           'default-provider',
                           'default-auth',
                           'default-error',
                           'default-program',
                           'media-index');

    public $users = array('default-user',
                          'default-event'
                          );
    public $power = array();
    public $admins = array('admin-index',
                           'admin-user',
                           'admin-admin',
                           'admin-program',
                           'admin-crud',
                           'admin-module',
                           'admin-qualifiers',
                           'admin-event',
                           'admin-page',
                           'admin-provider');
                          
  
    public function __construct() {
        
        $this->_addRolesAndPermissions();
    }
    
    
    public static function Roles($id = 0) {
        $roles = static::$roles;
        
        if( isset( $roles[(int)$id] ) ) {
            return $roles[(int)$id];
        }
        
        return 'guest';
    
    }
    

    protected function _addRolesAndPermissions(){

        //Add a new role called "guest"        
        $this->addRole(new Zend_Acl_Role(static::Roles(0)));

        //Add a role called user, which inherits from guest
        $this->addRole(new Zend_Acl_Role(static::Roles(1) ), static::Roles(0) );

        //Add a role called admins, which inherits from user
        $this->addRole(new Zend_Acl_Role(static::Roles(2)), static::Roles(1) );

        //Add each resource to the are new ACL
        foreach($this->resources as $resource=>$views ) {
            
            foreach( $views as $v ) {
                $this->addResource(new Zend_Acl_Resource("{$resource}-{$v}"));
            }
        
        }
        
        //guests and users have no permissions. 
        $this->deny('guest');

        //admins have all privlages.  
        $this->allow('admin');

        //set what guests are allowed to access
        foreach( $this->guests as $key=>$views ) {
            $this->allow('guest', $views, 'view');
        }
        
        //set what users are allowed to access
        foreach( $this->users as $key=>$views ) {
            $this->allow('user', $views, 'view');
        }
        
        //set what admins are allowed to access
        foreach( $this->admins as $key=>$views ) {
            $this->allow('admin', $views, 'view');
        }
        
    }





}
