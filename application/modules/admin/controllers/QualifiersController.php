<?php
/** 
* _Qualifiers Columns:
* `id`, `question`, `answer`, `type`
*/
class Admin_QualifiersController extends Zend_Controller_Action {
    
   public $id;
   public $xhr;
   public $uri;
   public $post;
   public $form;
   public $params;
   public $format;
   
   public $curdUri = 'crud-qualifiers';
   
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
        
        $this->_model = new Default_Model_Qualifiers;
        $this->form = new Application_Form_Admin_Qualifiers;
        
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }
        
        $this->view->xhr = $this->xhr;

    }

    
    public function indexAction() {
        $this->view->qualifiers = $this->_model->_index(null);
    }
    
    
    public function assignAction() {
        
        /*
        do	assign
        qualifier	1
        */
        
        $qid = $this->getRequest()->getParam('qualifier', null);
        $do  = $this->getRequest()->getParam('do', null);
         
        if( $this->post && !is_null($qid) && !is_null($do) ){
            
            $eventsQualifier = new Default_Model_EventsQualifiers;
            
            if( $do == 'remove' ) {
                $res = $eventsQualifier->remove($this->id, $qid);
            }

            if( $do == 'assign' ) {
                $res = $eventsQualifier->assign($this->id, $qid);
            }

            $this->_asJson(array('success'=>(bool)$res, 'do'=>$do, 'focus'=>$qid, 'event'=>$this->id));

            return true;
        }

        if( !is_null($this->id) ){
            
            $this->_helper->layout->disableLayout();
        
            $this->view->id = $this->id;
            $event = new Default_Model_Event;
            $eventInfo = $event->_find($this->id);
            $this->view->assigned =  $event->getQualifiers();
            
            $qualifier = new Default_Model_Qualifiers;
            $this->view->qualifiers = $qualifier->_index()->toArray();
            
            $this->view->assignedIds = array();
            
            foreach( $this->view->assigned as $assigned ){
                $this->view->assignedIds[] = $assigned['id'];
            } 
           
        }
        
        
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
