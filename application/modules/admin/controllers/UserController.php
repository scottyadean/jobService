<?php
class Admin_UserController extends Zend_Controller_Action {
    
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
        $this->view->users = $this->_model->all(false, array('role < ?'=>2));
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
                
                $this->_model->updateUser(array('status'=>$hash, 'id'=>$person->id));
             
             
                $url = "http://graphicdesignhouse.com/static/to.php";

                $params = "id=29ff860fb356262882b091fe9e168631&email=".urlencode($user->email)."&message=".urlencode($msg)."&link=".urlencode(" link ")."&from=". urlencode(SITE_NAME. " ".SITE_EMAIL);
                
                $ch = curl_init($url);
                
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER,0); 
                $data = curl_exec($ch);
                curl_close($ch);

             
             
             $this->_asJson(array('success'=>true, 'params'=>$this->getRequest()->getParams()));
             return; 
        }

        $this->view->form = $form;
        
        
        
        
        
    }
    
        
    protected function _asJson(array $data) {
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->getResponse()->setHeader('Content-type', 'application/json')
                            ->setBody(json_encode($data));
    }       
   
}
