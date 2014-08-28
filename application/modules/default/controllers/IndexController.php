<?php
class Default_IndexController extends Zend_Controller_Action
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
         $this->_helper->layout->setLayout('index');
    }
    
    public function pageAction() {
        $this->_model = new Default_Model_Page;
        $page = $this->_model->_read($this->id);
        
        if(!empty( $page )){
            
            $mods = new Default_Model_Module;
            $modules = $mods->_getByIds(array($page->module_id, $page->module_id_right, $page->header_id));
            
            $this->view->mods = $modules;
            
        }
        
        $this->view->page = $page;
    }
    
    public function eventsAction() {
        $eventsModel = new Default_Model_Event;
        $this->view->events = $eventsModel->findByDate(date("Y").'-'. date("m"), array( 'visible = ?' => 'Y' ))->toArray();
        $this->view->centerColors = $this->getHexColors();
    }
    
    
    public function eventColorsAction() {
        $this->_asJson($this->getHexColors());
    }
    
    public function getHexColors() {
        
        
        $centersModel = new Default_Model_Crud;
        $centersModel->setTable('skillsrc_centers');
        $res =  $centersModel->_index();
        $centerColors = array();
        
        foreach($res as $r) {
            $centerColors[$r->id] = array( 'hex' => $r->color ) ;
        }
        
        return $centerColors;
        
        
    }

    public function calendarAction() {
        if($this->xhr){
            $this->_helper->layout->disableLayout();
        }
        $this->view->year  = $this->getRequest()->getParam('year', date("Y"));
        $this->view->month = $this->getRequest()->getParam('month', date("n"));
        $this->view->day   = $this->getRequest()->getParam('day', date("d"));
    }
      
    public function eventByDateAction(){
        if($this->xhr){
            $this->_helper->layout->disableLayout();
        }
        
        $this->view->year  = $this->getRequest()->getParam('year', date("Y"));
        $this->view->month = $this->getRequest()->getParam('month', date("m"));
        $this->view->day   = $this->getRequest()->getParam('day', date("d"));
        
        $eventsModel = new Default_Model_Event;
        $this->view->events = $eventsModel->findByDate($this->view->year.'-'.$this->view->month)->toArray();
        
        
        $dateTimeObj = DateTime::createFromFormat('!m', $this->view->month);
        $this->view->month = $dateTimeObj->format('F');
        $this->view->centerColors = $this->getHexColors();
        
    }
    
    
    public function centerInfoAction() {
        
        
        $centersModel = new Default_Model_Crud;
        $centersModel->setTable('skillsrc_centers');
        
        $res = array();
        
        $res =  $centersModel->_read($this->id);
        
        if(!empty($res)) {
            
            
            $res = $res->toArray();
            
            $html = "<h2>".$res['name']."</h2>";
            $html .= "<p>".$res['address']."<br />";
            $html .= $res['city']." ".$res['state'].". ".$res['zip']."</p>";
            
            
            
        }
        
        $this->_asJson(array("data"=>$res, "html"=>$html));
        
    }
    
  
    public function eventDetailAction() {
        
        if(empty($this->id)) {
            $this->_helper->flashMessenger->addMessage(array('alert alert-error'=> 'Error: no "event id" sent to the view page.') );
            $this->_redirect("/events");   
        }
        
        if($this->xhr){
            $this->_helper->layout->disableLayout();
        }
        
        $eventsModel = new Default_Model_Event;
        $event = $eventsModel->_read($this->id);
        
        $this->view->eventSigned = null;
        $this->view->isOnWaitList = null;
        
        if(  LOGGED_IN ){
            
            $user_id = Zend_Auth::getInstance()->getIdentity()->id;
            $eventsRsvpModel = new Default_Model_EventRsvp;
            $this->view->eventSigned = $eventsRsvpModel->findByEventAndUserId($this->id, $user_id);
  
            $waiting = $eventsRsvpModel->onWaitList($this->id, $user_id);
            $this->view->isOnWaitList = (!empty($waiting)) ? $waiting->toArray() : null;
            
        }
        
        
        if( empty($event)) {
            $this->_helper->flashMessenger->addMessage(array('alert alert-error'=> 'Event Not Found!') );
            $this->_redirect("/events");   
        }
        $this->view->xhr = $this->xhr;
        $this->view->event = $event->toArray();
        
       
        $form = new Application_Form_Join;
        $form->build( "/join-via-event", null, $this->id);
        
        $this->view->form =$form;
    }
    
    
    public function eventQualifyAction() {
        
        $event_id = $this->getRequest()->getParam('event_id', null);
        $id = null;
        
        if(!$this->xhr || empty($event_id) || !$this->post){
            print "Please select an <a href='/event'>event</a> to sign up for.";
            return;
        } 
        
        $this->_helper->layout->disableLayout();  
        $eventsModel = new Default_Model_Event;
        $event = $eventsModel->_find($event_id);
        
        if(empty($event)){
           print "Please select an <a href='/event'>event</a> to sign up for.";
           return;
        } 
        
        if(LOGGED_IN){
            $id = Zend_Auth::getInstance()->getIdentity()->id;
        }        
        
        $qualifiers = $eventsModel->getQualifiers();
        
        $this->view->qualifiers = $qualifiers;
        $this->view->event_id = $event_id;
        $this->view->event = $event; 
         $this->view->isOnWaitList  = null;
         $this->view->eventSigned = null;
         
        $eventDetails = $eventsModel->_read($event_id);
        $allow = $this->view->seats = abs($eventDetails->rsvp_count - $eventDetails->seats);
        if( $eventDetails->type != 'limited registration'  ) {$allow = 1;}
        
        if( is_null($qualifiers) && $allow > 0 ) {
        $eventsRsvpModel = new Default_Model_EventRsvp;
        $eventsRsvpModel->_create(array('user_id'=>$id,
                                        'uid'=>'1',
                                        'event_id'=>$event_id,
                                        'status'=>1, ));
        }
        
        
        
        if(  LOGGED_IN && !empty($id) ){
            
            $eventsRsvpModel = new Default_Model_EventRsvp;
            $this->view->eventSigned = $eventsRsvpModel->findByEventAndUserId($event_id, $id);  
            $waiting = $eventsRsvpModel->onWaitList($event_id, $id);
            $this->view->isOnWaitList = (!empty($waiting)) ? $waiting->toArray() : null;
            
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
    
            if( $seats > 0 ) {
                
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
    
    
    public function viewsAction() {
        
        $allowed = array("events", 'pages', 'providers', 'programs');
        $success = false;
        if($this->xhr && !empty($this->id)) {
            $m = $this->getRequest()->getParam('m', null);
            if(in_array((string)$m, $allowed)){
                $sql = "UPDATE {$m} SET views = views + 1 WHERE id = ?";
                $model = new Default_Model_Crud;
                $model->setDbName($m);
                $model->_query($sql, array($this->id));
                $success = true;
            }
        }

        $this->_asJson(array( 'success'=>$success,
                              'model'=>$m,
                              'id'=>$this->id,));
    }
    
    
    
    public function favsAction() {
        $time   = time() + (1 * 365 * 24 * 60 * 60);    
        $addto  = (int)$this->getRequest()->getParam('add', 0);
        $remove = (int)$this->getRequest()->getParam('remove', false);
        $query  = (bool)$this->getRequest()->getParam('query', false);
        $value  = null;
        $check = $this->getRequest()->getCookie('favs');
        if(!empty($check)) {
           $value = $check;
        }
        
        if($remove) {    
            $sub = explode("~", $value);
            foreach( $sub as $k=>$s) {
                if( $remove == $s || $s == "" ) {
                    unset($sub[$k]);
                }
            }
            
            $value = implode("~",$sub);
            setcookie("favs",$value, $time, '/');
        }
    
         $cookie = explode("~", $value);    
    
        if($addto > 0 && !in_array((string)$addto, $cookie)) {
            setcookie("favs", $addto."~".$value, $time, '/');
            $cookie = explode("~", $this->getRequest()->getCookie('favs'));
        }
       
        if( $query ) {
            $providers = new Default_Model_Provider;
            $query = $providers->_index(array( "id IN (?)"=> $cookie ));
        } 
       
       
        $this->_asJson(array('success'=>true,
                             'value'=>$cookie,
                             'remove'=>$remove,
                             'add'=>$addto,
                             'query'=> !empty($query) ?  $query->toArray() : false
                             ));
    }
    
    
    public function contactHtmlAction() {
        
        $form = new Application_Form_Contact;
        $form->build();
        $this->view->form = $form;
        
        if($this->post) {
            var_dump($form->getValues());   
            print "sent";
            
        }
        
        
    }
    
      
    protected function _asJson(array $data) {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-type', 'application/json')
                            ->setBody(json_encode($data));
    }
    
}