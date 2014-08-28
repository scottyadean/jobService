<?php
class Admin_SettingController extends Zend_Controller_Action {
    
   public $id;
   public $xhr;
   public $uri;
   public $post;
   public $format;
   
   protected $_model;
   
   public function init() {
  
        Zend_Layout::getMvcInstance()->assign('sideNav', 'N');
        $this->id = $this->getRequest()->getParam('id', null);
        $this->xhr = $this->getRequest()->isXmlHttpRequest();
        $this->uri = $this->getRequest()->getRequestUri();
        $this->sort = $this->getRequest()->getParam('sort', false);
        $this->post = $this->getRequest()->isPost();
        $this->format = $this->getRequest()->getParam('format', false);
                
        $this->_model = new Default_Model_User;
        
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }

    }
}