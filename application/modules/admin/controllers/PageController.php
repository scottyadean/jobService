<?php
class Admin_PageController extends Zend_Controller_Action {
    
   public $id;
   public $xhr;
   public $uri;
   public $post;
   public $form;
   public $params;
   public $format;
   
   
   protected $_model;
   
   public function init() {
    
        $this->id = $this->getRequest()->getParam('id', null);
        $this->xhr = $this->getRequest()->isXmlHttpRequest();
        $this->uri = $this->getRequest()->getRequestUri();
        $this->sort = $this->getRequest()->getParam('sort', false);
        $this->post = $this->getRequest()->isPost();
        $this->params = $this->getRequest()->getParams();
        $this->format = $this->getRequest()->getParam('format', false);
        Zend_Layout::getMvcInstance()->assign('sideNav', 'N');
    
        $this->_model = new Default_Model_Page;
        $this->form = new Application_Form_Admin_Pages;
        
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }

    }

    public function indexAction() {
        $this->view->pages = $this->_model->_index(null);
    }


    public function updateAction() {

        $this->form->customSubmitBtn = $this->xhr;
        
        $this->form->build( $this->uri, $this->id);
        $data = $this->_model->_read((int)$this->id)->toArray();
        $this->form->populate($data);
        $this->validationErrors = null;
         
        $res = Main_Forms_Handler::onPost($this->form,
                                          $this->post,
                                          $this->_model,
                                          "_update",
                                          $this->params,
                                          $this->_helper,
                                          $this->uri,
                                          " update successful.",
                                          $this->xhr,
                                          $this->validationErrors);  
         
         if($this->xhr && $this->post && !empty($res)) {
            $this->_asJson($res);
            return;
         }
        
        if($this->xhr && $this->post) {
            $this->_asJson(array( 'success'=>false, 'id'=>$this->id, 'action'=>'no change', 'message'=>'form not changed', 'errors'=>array() ));
        }else{        
            $this->view->form  = $this->form;
        }
        
        $mods = new Default_Model_Module;  
        $this->view->avlModules =  $mods->_index(); 
        $this->view->modules = $this->_model->getPageModules(); 
        $this->view->page_id = $this->id;
        $this->view->form = $this->form;
    }    

        
    /*
    * Allow inline editing of users in the admin pannel.
    */
    public function inlineEditAction() {

        $pk    = (int)$this->getRequest()->getParam('pk');
        $field = strip_tags($this->getRequest()->getParam('name'));
        $value = strip_tags($this->getRequest()->getParam('value'));
        $data  = Application_Form_Admin_Pages::onUpdate(array("id"=>$pk, $field=>$value)); 
        $res   = $this->_model->_update($data);
        
        $this-> _asJson(array("success"=>$res, "pk"=>$pk, "name"=>$field, "value"=>$value));
    
    }
    
    
    
    /*
     All edit of home page
    */
    public function homepageAction() {
        $this->view->updated = false;
        $path = APPLICATION_PATH ."/modules/default/views/scripts/index/index.phtml";
        $content = $this->getRequest()->getParam('HomePageContent', false);
        $this->view->contents = file_get_contents( $path );
        if( $this->post && $content ) {
            $this->view->updated = file_put_contents($path, html_entity_decode($content));
            
            $data = array('message'=>'File Updated');
            
            $this->_asJson($data);
            
            
        } 
    }
    
        
    protected function _asJson(array $data) {
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->getResponse()->setHeader('Content-type', 'application/json')
                            ->setBody(json_encode($data));
    }       
   
}
