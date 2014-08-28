<?php
class Admin_AdminController extends Zend_Controller_Action {
    
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
                
        $this->_model = new Default_Model_User;
        
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }

    }

    public function indexAction() {
        $this->view->users = $this->_model->all(false, array('role > ?'=>1));
    }
        
    /*
    * Allow inline editing of users in the admin pannel.
    */
    public function inlineEditAction() {

        $pk    = (int)$this->getRequest()->getParam('pk');
        $field = strip_tags($this->getRequest()->getParam('name'));
        $value = strip_tags($this->getRequest()->getParam('value'));
        $res   = $this->_model->_update(array("id"=>$pk, $field=>$value));
        
        $this-> _asJson(array("success"=>$res, "pk"=>$pk, "name"=>$field, "value"=>$value));
    
    }
    
    public function resetPasswordAction() {
        
        $user = $this->_model->findById($this->id); 
        
        $form = new Application_Form_Admin_PasswordReset();
        $form->build($user);
        
        $hash = $this->getRequest()->getParam('hash', null);
        
        if($this->post && !empty( $hash )){
             $msg  = $this->getRequest()->getParam('textarea');
             $this->_model->updateUser(array('status'=>$hash, 'id'=>$this->id));
             $headers = 'From: '.SITE_EMAIL.'' . "\r\n" . 'Reply-To: '.SITE_EMAIL.'' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
             $mail = mail($user->email, 'Password Reset Link', $msg, $headers);
             $this->_asJson(array('success'=>$mail, 'params'=>$this->getRequest()->getParams()));
             return; 
        }

        $this->view->form = $form;
        
    }
    
    
    
    
    /** 
     *Inline data for visible or hidden providers
    */
    public function dataSrcAction() {
        $this-> _asJson(array('7'=>'Inactive', '2'=>'Active'));
    }
    
        
    protected function _asJson(array $data) {
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->getResponse()->setHeader('Content-type', 'application/json')
                            ->setBody(json_encode($data));
    }       
   
}
