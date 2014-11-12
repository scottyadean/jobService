<?php
class Default_EventController extends Zend_Controller_Action
{

   public $id;
   public $xhr;
   public $uri;
   public $post;
   public $params;
   public $format;
   public $callback;
   
   protected $_model;
   
   public function init() {
        $this->id = $this->getRequest()->getParam('id', null);
        $this->xhr = $this->getRequest()->isXmlHttpRequest();
        $this->uri = $this->getRequest()->getRequestUri();
        $this->post = $this->getRequest()->isPost();
        $this->params = $this->getRequest()->getParams();
    }
    
    public function indexAction() {
        
        $model = new Default_Model_Crud;
        $model->setTable('skillsrc_centers');
        $eventCenters = $model->_index()->toArray();
        
        $this->view->eventCenters = $eventCenters;
    
    }
    
    
    public function myEventsAction() {
        $id = Zend_Auth::getInstance()->getIdentity()->id;
        $user = new Default_Model_User;
        $this->view->user = $user->findById($id);
        $this->view->events = $user->getEvents();
    }
    
    
    public function waitListAction() {
        
        if($this->xhr){
            $this->_helper->layout->disableLayout();
            
        }
        
        $rsvp = new Default_Model_EventRsvp;
        $user_id = Zend_Auth::getInstance()->getIdentity()->id;
        $res = false;
  
        $eventsModel = new Default_Model_Event;     
        $event = $eventsModel->_find($this->id);
  
        if(!empty($this->id) && !empty($event)){
            $res = $rsvp->addToWaitList($this->id, $user_id);
        }
        
        $this->view->user = $user_id;
        $this->view->event =$event;
        $this->view->success = $res;
    }
    
    public function waitListRemoveAction() {
        
        if($this->xhr){
            $this->_helper->layout->disableLayout();
            
        }
        
        $rsvp = new Default_Model_EventRsvp;
        $user_id = Zend_Auth::getInstance()->getIdentity()->id;
        $res = false;
  
        $eventsModel = new Default_Model_Event;     
        $event = $eventsModel->_find($this->id);
  
        if(!empty($this->id) && !empty($event)){
            $res = $rsvp->removeFromWaitList($this->id, $user_id);
        }
        
        $this->view->user = $user_id;
        $this->view->event =$event;
        $this->view->success = $res;
    }
    
    public function removeFromListAction() {
        $user_id = Zend_Auth::getInstance()->getIdentity()->id;
        $rsvp = new Default_Model_EventRsvp;
        $event = $rsvp->findByEventAndUserId($this->id, $user_id);
        $update = null;
        
        if( !empty($event) ) {
            
           $update = $rsvp->_update(array('rsvp_visible'=>'N', "id"=>$event->id));
            
        }
        
        $this->_asJson(  array( 'id'=>$this->id, 'u'=>$user_id, 'success'=>true, "update"=>$update));
        
    }
    
    
    public function cancelAction() {
        
        $rsvp = new Default_Model_EventRsvp;
        $user_id = Zend_Auth::getInstance()->getIdentity()->id;
        $res = $rsvp->_deleteByEventIdAndUserId($this->id, $user_id);
        $wait = $rsvp->_autoFillEmptySpotByNextUserOnWaitList($this->id);
        
        //TODO: PUT EMAIL ACTION HERE
        $this->_asJson(  array( 'id'=>$this->id, 'u'=>$user_id, 'success'=>$res, 'wait'=>$wait ));
        
    }
    
    
    public function eventQualifyAction() {
        
        $event_id = $this->getRequest()->getParam('event_id', null);        
        
        if(!$this->xhr || empty($event_id) || !$this->post){
            print "Please select an <a href='/event'>event</a> to sign up for.";
            return;
        } 
        
        $this->_helper->layout->disableLayout();  
        $id = Zend_Auth::getInstance()->getIdentity()->id;
        $eventsModel = new Default_Model_Event;
        
        $event = $eventsModel->_find($event_id);
        $qualifiers = $eventsModel->getQualifiers();
        $this->view->qualifiers = $qualifiers;
        $this->view->event_id = $event_id;
        
        $eventDetails = $eventsModel->_read($event_id);
        $this->view->seats = abs($eventDetails->rsvp_count - $eventDetails->seats);
        
        if( is_null($qualifiers) && $this->view->seats > 0 ) {
        $eventsRsvpModel = new Default_Model_EventRsvp;
        $eventsRsvpModel->_create(array('user_id'=>$id,
                                        'uid'=>'1',
                                        'event_id'=>$event_id,
                                        'status'=>1, ));
        }
        
    }
    
    
    public function eventSignUpAction() {
        
        $event_id = $this->getRequest()->getParam('event_id', null);
        $id = Zend_Auth::getInstance()->getIdentity()->id;
        $this->_helper->layout->disableLayout();  
        $message = 'Error Sign up can not be compleated.';
        
        $eventsRsvpModel = new Default_Model_EventRsvp;
        
        if(!empty($event_id) && $this->post && !empty($id)){
        
            $eventsModel = new Default_Model_Event;
            $eventDetails = $eventsModel->_read($event_id);
            $seats = abs($eventDetails->rsvp_count - $eventDetails->seats);
    
            if( $eventDetails->type == "limited registration" && $seats > 0 ) {
                
                $success = $eventsRsvpModel->_create(array('user_id'=>$id,
                                                           'uid'=>'1',
                                                            'event_id'=>$event_id,
                                                            'status'=>1, ));
                $message = 'Sign up Complete';        
            }

        } 

        $this->_asJson(array( 'success'=>(int)$success,
                              'event_id'=>$event_id,
                              'id'=>$id,
                              'count'=>$seats,
                              'message'=>$message,
                              'logged'=>LOGGED_IN, 
                              'errors'=>array() ));
        
        
    }
      
    protected function _asJson(array $data) {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-type', 'application/json')
                            ->setBody(json_encode($data));
    }
    
}