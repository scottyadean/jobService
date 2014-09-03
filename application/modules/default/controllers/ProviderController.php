<?php
class Default_ProviderController extends Zend_Controller_Action {    
   
   
   public $id;
   public $xhr;
   public $uri;
   public $post;
   public $format;
   public $callback;
   
   protected $_model;
   
   public function init() {
    
        $this->id = $this->getRequest()->getParam('id', null);
        $this->xhr = $this->getRequest()->isXmlHttpRequest();
        $this->uri = $this->getRequest()->getRequestUri();
        $this->sort = $this->getRequest()->getParam('sort', false);
        $this->post = $this->getRequest()->isPost();
        $this->format = $this->getRequest()->getParam('format', false);
        $this->callback = $this->getRequest()->getParam('callback', null);
        $this->_model = new Default_Model_Provider;
        
    }
   
    public function indexAction() {
        $this->view->providersA_H = $this->_model->all('a-h')->toArray();
        $this->view->providersI_P = $this->_model->all('i-p')->toArray();
        $this->view->providersQ_Z = $this->_model->all('q-z')->toArray();
        $this->view->locations = $this->locations();
        $this->view->programs = $this->programs();
        $this->view->programsGroups = $this->programsGroups();
        
       
        $industries = new Default_Model_Industry; 
        $this->view->baseIndustries = $industries->_index(array( 'parent_id  = ?' => 0 ));
        $this->view->nestedIndustries = $industries->_index(array( 'parent_id  != ?' => 0 ));
        
        
        $locations = new Default_Model_Locations;
        $locations->group_by = 'city';
        $locations->fields = array('city');
        $this->view->locationsFilter =  $locations->_index()->toArray();
    
        $providerNames = new Default_Model_Provider;
        $locations->group_by = 'name';
        $locations->fields = array('name', 'tags');
        $this->view->providersFilter =  $providerNames->_index()->toArray();
     
       
       
        
    }
    
    public function industryAction() {
        
        if( $this->xhr ){ 
            $this->_helper->layout->disableLayout();
            $this->view->disableLayout = true;
        }
        
        if(empty($this->id)) {
            $this->_helper->flashMessenger->addMessage(array('alert alert-error'=> 'Error: no "industry id" sent to the view page.') );
            $this->_redirect("/providers");   
        }
        
        $industry = new Default_Model_Industry;
        $childElements = $industry->_index(array("parent_id = ?" => (int)$this->id))->toArray();
        $this->view->industry = $industry->_read((int)$this->id);
        
        if(empty($this->view->industry)){
                $this->_helper->flashMessenger->addMessage(array('alert alert-error'=> 'Error: Industry not found') );
                $this->_redirect("/providers");   
        }
        
        $programs = new Default_Model_Program;
        $where = !empty($childElements)
        ? array('industry_id IN (?)' => (int)Base_Functions_Array::In($childElements))
        : array('industry_id = ?' => (int)$this->id);
                 
        $this->view->programs = $programs->_joinProviders($where);

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
        
       
        
        $this->view->providers = $this->_model->findbyField(trim($this->id), $this->field, $this->regex );
        $this->view->locations = $this->locations();
        $this->view->field     = $this->field;
        
        
       $programModel = new Default_Model_Program;
       $this->view->programs = $programModel->findbyField(trim($this->id), $this->field, $this->regex );
        
        
    }
    
    public function viewAction() {
        
        if(empty($this->id)) {
            $this->_helper->flashMessenger->addMessage(array('alert alert-error'=> 'Error: no "provider id" sent to the view page.') );
            $this->_redirect("/providers");   
        }
        
        $this->view->provider = array_shift($this->_model->_index(array('id = ?'=> $this->id))->toArray());
       
        $locations = new Default_Model_Locations;
        $this->view->locations = $locations->_index(array('provider_id = ?'=> $this->id))->toArray();
       
        $programsModel = new Default_Model_Program;
        $this->view->programs = $programsModel->_index(array('provider_id = ?'=> $this->id))->toArray();
        
       $programslocations = new Default_Model_ProgramLocations;
       $this->view->programs_locations = $programslocations->findByProviderId($this->id)->toArray();
        
        
    }
 
    public function programsGroups() {
        $programs = new Default_Model_Program;
        return $programs->_index();    
    }
    
    public function programs() {
        $programs = new Default_Model_Program;
        $programs->group_by = "industry_id";
        $programs->order_by = "industry_name";
        return $programs->_index();    
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