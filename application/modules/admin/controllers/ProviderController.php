<?php
class Admin_ProviderController extends Zend_Controller_Action {

   public $id;
   public $xhr;
   public $uri;
   public $post;
   public $format;
   public $callback;
   
   public function init() {
    
        $this->id = $this->getRequest()->getParam('id', $this->getRequest()->getParam('pk', null));
        $this->xhr = $this->getRequest()->isXmlHttpRequest();
        $this->uri = $this->getRequest()->getRequestUri();
        $this->sort = $this->getRequest()->getParam('sort', false);
        $this->post = $this->getRequest()->isPost();
        $this->params = $this->getRequest()->getParams();
        $this->format = $this->getRequest()->getParam('format', false);
        $this->callback = $this->getRequest()->getParam('callback', null);
        Zend_Layout::getMvcInstance()->assign('sideNav', 'N');
    
        $this->form = new Application_Form_Admin_Providers;
        
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }
        
        $this->_model = new Default_Model_Provider;
        
        
        $this->view->xhr =  $this->xhr;
    }
   
    public function indexAction() {
        //$this->_model = new Default_Model_Provider;
        $this->view->providers = $this->_model->_index();
        $this->view->title = "Providers";
    }

    /*
     * 
    * Allow inline editing of users in the admin pannel.
    */
    public function inlineEditAction() {

        $pk    = (int)$this->getRequest()->getParam('pk');
        $field = strip_tags($this->getRequest()->getParam('name'));
        $value = strip_tags($this->getRequest()->getParam('value'));
        $data  = Application_Form_Admin_Providers::onUpdate(array("id"=>$pk, $field=>$value)); 
        $res   = $this->_model->_update($data);
        
        $this-> _asJson(array("success"=>$res, "pk"=>$pk, "name"=>$field, "value"=>$value));
    
    }

    public function updateAction() {

        $this->form->customSubmitBtn = $this->xhr;
        
        $this->form->build( $this->uri, $this->id);
        $data = $this->_model->_read((int)$this->id)->toArray();
        
        
        $this->form->populate($data);
        $this->validationErrors = null;
        //if( $this->post && !empty($this->_model->onValidate) ) {
        //$this->_model->_validate($this->params);
        //}
       
        
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
          
        
        $locations = new Default_Model_Crud;
        $locations->setTable("providers_locations");
        $this->view->addresses = $locations->_index(array('provider_id  = ?' => (int)$this->id));
        
        $programs = new Default_Model_Crud;
        $programs->setTable("providers_programs");
        $this->view->programs = $programs->_index(array('provider_id  = ?' => (int)$this->id));
        
        
        $this->view->provider_id = $this->id;
        
    }
    
    
    /** 
     *Inline data for visible or hidden providers
    */
    public function dataSrcAction() {
        
        $feild = $this->getRequest()->getParam('f');
         if($feild == 'state') {
            
            return $this-> _asJson(Main_Forms_Data::AmericaStates());
            
         }
        $this-> _asJson(array('Y'=>'Visible', 'N'=>'Hidden'));
    }
    
    
    protected function _asJson(array $data) {
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);   
        $this->getResponse()->setHeader('Content-type', 'application/json')
                            ->setBody(json_encode($data));
    }        
}