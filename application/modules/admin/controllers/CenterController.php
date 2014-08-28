
<?php
class Admin_CenterController extends Zend_Controller_Action {
    
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
                
        $this->_model = new Default_Model_Crud;
        $this->_model->setTable('skillsrc_centers'); 
        
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }

    }
    
    
    public function indexAction() {
        
        $this->view->centers =  $this->_model->_index();
             
        
    } 


    public function readAction() {
        
        $this-> _asJson($this->_model->_read($this->id)->toArray());
             
    } 
    
    public function colorsAction() {

        $res =  $this->_model->_index();
        $colors = array(0=>"#333");
        foreach($res as $r) {
            
            $colors[$r->id] =  $r->color;
            
        }
        
         $this-> _asJson(array("success"=>true, "colors"=>$colors));
        
        
    }
    
    
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