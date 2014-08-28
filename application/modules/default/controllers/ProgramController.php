<?php
class Default_ProgramController extends Zend_Controller_Action {    
   
   
   public $id;
   public $xhr;
   public $uri;
   public $post;
   public $format;
   public $callback;
   
   protected $_model;
   
   public function init() {
    
        $this->id       = $this->getRequest()->getParam('id', null);
        $this->xhr      = $this->getRequest()->isXmlHttpRequest();
        $this->uri      = $this->getRequest()->getRequestUri();
        $this->sort     = $this->getRequest()->getParam('sort', false);
        $this->post     = $this->getRequest()->isPost();
        $this->format   = $this->getRequest()->getParam('format', false);
        $this->callback = $this->getRequest()->getParam('callback', null);
        $this->_model   = new Default_Model_Program;
    
    }
   
    public function indexAction() {
        
        //abcd efghi jklmn opqrs tuvwxyz
        
        $this->view->programsA_D = $this->_model->all('a-d')->toArray();
        $this->view->programsE_I = $this->_model->all('e-i')->toArray();
        $this->view->programsJ_N = $this->_model->all('j-n')->toArray();
        $this->view->programsO_S = $this->_model->all('o-s')->toArray();
        $this->view->programsT_Z = $this->_model->all('t-z')->toArray();
        
        $this->view->locations = $this->locations();
    }
    
    
    public function searchAction() {
        
        if( $this->xhr ){ 
            $this->_helper->layout->disableLayout();
            $this->view->disableLayout = true;
        }else{     
            $this->view->disableLayout = false;
        }
        
        if($this->id == false) {
            $this->id = 'a';
        }
        
        $this->field = $this->getRequest()->getParam('field', 'name');
        $this->regex = $this->getRequest()->getParam('regex', 'starts');
        
        $this->view->programs = $this->_model->findbyField($this->id, $this->field, $this->regex );
        $this->view->locations = $this->locations();
       
    }
    

    
    public function locations(){
        
        $locations = new Default_Model_Crud;
        $locations->setTable("providers_locations");
        $addresses = $locations->_index()->toArray(); 
        
        foreach( $addresses as $key=>$address ) {
            
            if(!isset( $formated[$address['provider_id']])) {
                
                $formated[$address['provider_id']] = array();
            }
            
            $formated[$address['provider_id']][] = $address;
            
        }
        return $formated;
        
    }
    
  
}  