<?php
class Admin_EventController extends Zend_Controller_Action {
    
   public $id;
   public $xhr;
   public $uri;
   public $post;
   public $form;
   public $params;
   public $format;
   
   protected $_model;
   
   public function init() {
        
        Zend_Layout::getMvcInstance()->assign('sideNav', 'N');
    
        $this->id = $this->getRequest()->getParam('id', null);
        $this->xhr = $this->getRequest()->isXmlHttpRequest();
        $this->uri = $this->getRequest()->getRequestUri();
        $this->sort = $this->getRequest()->getParam('sort', false);
        $this->post = $this->getRequest()->isPost();
        $this->params = $this->getRequest()->getParams();
        $this->format = $this->getRequest()->getParam('format', false);
        
        $this->_model = new Default_Model_Event;
        $this->form = new Application_Form_Admin_Event;
        
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }

    }

    public function indexAction() {

        $this->form->customSubmitBtn = false;
        $this->form->_formId = 'eventsformcustom';
        $this->form->formLabel = 'Create New Event';
        $this->form->_cancelBtn = false;
        $this->form->build( "/admin/events/create", $this->id);
        $this->view->form = $this->form;
        $this->view->events = $this->_model->findByDate(date('Y-m'));
        
        $Qualifiers = new Default_Model_Qualifiers;
        $this->view->qualifiers = $Qualifiers->_index(null);
        
        
        $this->_model = new Default_Model_Crud;
        $this->_model->setTable('skillsrc_centers');
        $this->view->centers =  $this->_model->_index();
        
        
    }
    
    public function bydateAction() {
        
        $y = $this->getRequest()->getParam('y', 0);
        $m = $this->getRequest()->getParam('m', 0);
        $d = $this->getRequest()->getParam('d', 0);
        
        $events = $this->_model->findByDate($y.'-'.$m)->toArray();
        $this->_asJson($events);
        
    }
    
    
    public function duplicateAction() {

        $model = new Default_Model_Event;
        $event = $model->_find($this->id)->toArray();
        $lastid = false;
        
        
        if( $this->params['render'] == 'form'  ) {
            
            
            $form = new Application_Form_Admin_EventDuplicate();
            $form->_created = $event['created'];
            $form->build($this->uri, $this->id);
            $form->populate($event);
            $this->view->form = $form;
            $this->view->event = $event;
            return;
        }
        
        
        if(!empty($event)) {
            
            $qualifiers = $model->getQualifiers();
            
            if(isset($event['id'])){
                unset($event['id']);
            }
            
            
            $event['created'] = $this->params['created'];
            $event['title'] = strip_tags($this->params['title']);
            
            $event = $model->_cleanData($event);
            
            $lastid = $model->_create( $event );
        
        }
        
        $this->_asJson(array("event"=>$event, "success"=>$lastid));
        
    }
    
    
     /*
     * 
    * Allow inline editing of users in the admin pannel.
    */
    public function inlineEditAction() {

        $pk    = (int)$this->getRequest()->getParam('pk');
        $field = str_replace("-inline-edit", '', strip_tags($this->getRequest()->getParam('name')));
        $value = strip_tags($this->getRequest()->getParam('value'));
        $data  = Application_Form_Admin_Providers::onUpdate(array("id"=>$pk, $field=>$value)); 
        $res   = $this->_model->_update($data);
        
        $this-> _asJson(array("success"=>$res, "pk"=>$pk, "name"=>$field, "value"=>$value));
    
    }

    public function createAction() {

        $this->form->customSubmitBtn = $this->xhr;
        $this->form->build( $this->uri, $this->id);
        $this->validationErrors = null;

        
        $res = Main_Forms_Handler::onPost($this->form,
                                          $this->post,
                                          $this->_model,
                                          "_create",
                                          $this->params,
                                          $this->_helper,
                                          $this->uri,
                                          " Event created successfully.",
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
          
       
        
        
    }
    

    public function detailAction() {
        if($this->xhr ) {
            $this->_helper->layout->disableLayout();  
        }
        $eventsModel = new Default_Model_Event;
        $event = $eventsModel->_find($this->id);
        $qualifiers = $eventsModel->getQualifiers();
        
        
        $this->view->qualifiers = $qualifiers;
        $this->view->users = null;
        
        $this->view->event = $eventDetails = $eventsModel->_read($this->id);
        $this->view->seats = abs($eventDetails->rsvp_count - $eventDetails->seats);
        $this->view->rsvp = $eventDetails->rsvp_count;
        $this->view->xhr = $this->xhr;
        
        $eventsRsvpModel = new Default_Model_EventRsvp;
        $reg = $eventsRsvpModel->findByEventId($this->id)->toArray();  
            if(count($reg) > 0){
                $ids = array();
                foreach( $reg as $r) {
                    $ids[] = $r['user_id'];
                }
                $usr = new Default_Model_User;
                $this->view->users = $usr->_in('id', $ids);
            }
    }
            
            
            
    public function exportListAction() {
        
        $eventsModel = new Default_Model_Event;
        $eventDetails = $eventsModel->_read($this->id);
        
        $eventsRsvpModel = new Default_Model_EventRsvp;
        $reg = $eventsRsvpModel->findByEventId($this->id)->toArray();  
    
            if(count($reg) > 0){
                $ids = array();
                foreach( $reg as $r) {
                    $ids[] = $r['user_id'];
                }
                $usr = new Default_Model_User;
                $_users = $usr->_in('id', $ids);
            }
        
        
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.Base_Functions_Strings::Slug($eventDetails->title).'.csv"');

        $csv = Base_Functions_Strings::arrayToCsv(array( 'fname', 'lname', 'phone', 'email', 'address', 'city', 'state', 'zip'));
            
        foreach($_users as $u){
            $csv .= Base_Functions_Strings::arrayToCsv(array( $u['fname'], $u['lname'], $u['phone'], $u['email'], $u['address'], $u['city'], $u['state'], $u['zip']  ));
        }
        
        print $csv;
        
    }        
        
    protected function _asJson(array $data) {
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->getResponse()->setHeader('Content-type', 'application/json')
                            ->setBody(json_encode($data));
    }       
   
}
