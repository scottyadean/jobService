<?php
class Admin_ModuleController extends Zend_Controller_Action {
    
   public $id;
   public $xhr;
   public $uri;
   public $post;
   public $form;
   public $format;
   
   protected $_model;
   
   public function init() {
    
        $this->id = $this->getRequest()->getParam('id', null);
        $this->xhr = $this->getRequest()->isXmlHttpRequest();
        $this->uri = $this->getRequest()->getRequestUri();
        $this->sort = $this->getRequest()->getParam('sort', false);
        $this->post = $this->getRequest()->isPost();
        $this->format = $this->getRequest()->getParam('format', false);
        Zend_Layout::getMvcInstance()->assign('sideNav', 'N');
    
        $this->_model = new Default_Model_Crud;
        $this->_model->setDbName('modules');
        
        
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }

    }

    public function indexAction() {
        $this->view->modules = $this->_model->_index(null);
    }
    
    
        
    /*
    * Allow inline editing of users in the admin pannel.
    */
    public function inlineEditAction() {
        
        $pk    = (int)$this->getRequest()->getParam('pk');
        $field = strip_tags($this->getRequest()->getParam('name'));
        $value = strip_tags($this->getRequest()->getParam('value'));
        $data  = array("id"=>$pk, $field=>$value); 
        $res   = $this->_model->_update($data);
        
        $this-> _asJson(array("success"=>$res, "pk"=>$pk, "name"=>$field, "value"=>$value));
    
    }
    
    
        
    protected function _asJson(array $data) {
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->getResponse()->setHeader('Content-type', 'application/json')
                            ->setBody(json_encode($data));
    }       
   
}
