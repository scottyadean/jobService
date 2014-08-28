<?php
class Admin_MediaController extends Zend_Controller_Action {
    
    protected $_model;


    public function init() {
         Zend_Layout::getMvcInstance()->assign('sideNav', 'N');
         
        $this->id = $this->getRequest()->getParam('id', null);
        $this->xhr = $this->getRequest()->isXmlHttpRequest();
        $this->uri = $this->getRequest()->getRequestUri();
        $this->sort = $this->getRequest()->getParam('sort', false);
        $this->post = $this->getRequest()->isPost();
        $this->format = $this->getRequest()->getParam('format', false);
                
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }
    }
    
    
    
    public function indexAction() {
        
        $dirs = Base_Functions_Files::listDirRecursive(APPLICATION_PATH."/../public", array("captcha"));
        $this->view->folders = $dirs;
    }
    
    
    public function contentsAction() {
        
        $this->view->current = $this->id;
        $this->view->folders =  Base_Functions_Files::listDirAndFilesRecursive($this->id, array("captcha"));
        
    }

}