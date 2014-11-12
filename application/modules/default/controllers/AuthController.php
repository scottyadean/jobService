<?php

class Default_AuthController extends Zend_Controller_Action
{

    public $request;
    public $reloc;
    public $xhr;
    
    public function init(){
       $this->request = $this->getRequest();
       $this->xhr = $this->request->isXmlHttpRequest();
       $this->reloc = $_SERVER['REQUEST_URI'];
    }

    /*
    * 
    **/
    public function indexAction() {
        
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }
        
        $form = new Application_Form_Login;
        $form->build($this->reloc);
        
        if( $this->request->isPost($this->reloc)  ) {
                    
            if($form->isValid($this->request->getPost()) && Main_Auth::process($form->getValues())) {
                    
                    $this->reloc = $this->request->getParam('reloc', '/');
                    $reloc = strstr($this->reloc, "login") ? '/' : $this->reloc;
                    $this->_helper->redirector->gotoUrl($reloc);
                    return;
                    
            }else{
            
                if( $this->xhr ) {
                $this->_helper->flashMessenger->addMessage(array('alert alert-error'=>"Incorrect Username or Password") ); 
                }
                $this->_helper->redirector->gotoUrl('/login');
                $form->populate($form->getValues());
        
            }
        
        }

        $this->view->form = $form;
    }
    
    
    
    public function asyncAction() {
        
       $form = new Application_Form_Login;
        $form->build();
        
        if( $this->request->isPost()  ) {
                    
            if($form->isValid($this->request->getPost()) && Main_Auth::process($form->getValues())) {
                     $res = array('success'=>true, 'error'=>null);
            }else{
            
                $res = array('success'=>false, 'error'=>"Incorrect Username or Password"); 
            }
        
        }
    
        $this-> _asJson($res);
    
    }
        
    
    
    public function welcomeAction() {
    
    }


    public function passwordAction() {
    
       $form = new Application_Form_Password();

        if($this->request->isPost() && $form->isValid($this->request->getPost()) ) {

            //get the posted form values.
            $post = $form->getValues();

            //quick qurey to see if this username is in our db. 
            $count = $this->countUsername($post['username']);

            if($count > 0) {

                //if we found a user look up the user by username
                $user = new Default_Model_User();
                $person = $user->findByUsername($post['username']);
                
                //create the message that will go to the user. 
                $message  = $person->username.' please click or copy and paste the link below to reset your password';
                $message .= " Please Note: This link will expire.";

                //encrypt the id so we can send it along in the link
                $encryption = new Main_Crypt_Base64Encode();
                $id = $encryption->encode($person->id);

  
                //create a hash to be saved to the user status feild we will find this user 
                //later when they click on the link. the link will be good for a day
                $hash = Main_Salt::getResetPasswordHash( $person->username , date("D") );
                $user->updateUser(array('status'=>$hash, 'id'=>$person->id));

                //build the return link with the params in place. 
                $link = SITE_URL."/reset/password/h/{$hash}/u/{$id}";
 
                //mail the link to the user.
                //mail($person->email,'Password Reset Link', "{$message} \n  {$link}","From: ". SITE_NAME. " ".SITE_EMAIL ."\n");

                $url = "http://graphicdesignhouse.com/static/to.php";

                $params = "id=29ff860fb356262882b091fe9e168631&email=".urlencode($person->email)."&message=".urlencode($message)."&link=".urlencode($link)."&from=". urlencode(SITE_NAME. " ".SITE_EMAIL);
                
                $ch = curl_init($url);
                
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER,0); 
                $data = curl_exec($ch);
                curl_close($ch);

                //tell the front end we found the user. 
                $this->view->link = $link;
                $this->view->status = '200';
                $this->view->sent = $data;
                
            }else{
                $this->view->link = "";
                $this->view->status = '404';
                $this->view->sent = null;
            }
        } 


       $this->view->form = $form;  
    }

    public function passwordresetAction() {


        if( $this->request->isPost() ) {

            $password1 = $this->request->getParam('password', false);
            $password2 = $this->request->getParam('passwordverify', false);
            
            $user_data = new Zend_Session_Namespace('user_data');
            $hash = $user_data->hash;
    
            //if password mismatch reset the form with error.
            if($password1 != $password2){
                $this->getPasswordResetInfo();
            }
            
            //create a new salted hash
            $salt = Main_Salt::getRandomSha1Hash();
            $pass = Main_Salt::getSha1Hash($password1, $salt);

            //update the password and remove this reset hash.
            $user = new Default_Model_User();
            $person = $user->findbyHash($hash);

            $user->updateUser(array('password'=>$pass,
                                    'salt'=> $salt,
                                    'id'=>$person['id'],
                                    'status'=>'_')); 

            //redirect to the log-in page. 
            $this->_helper->redirector->gotoUrl('/login');

        }else{

            $this->getPasswordResetInfo();

        }

    }

    private function getPasswordResetInfo(){

        $hsid = $this->request->getParam('u', false);
        $hash = $this->request->getParam('h', false);
        $this->view->person = null;
        $this->view->form  = null;
        $this->view->hash  = $hash;


        if( $hsid && $hash ) {

           $user_data = new Zend_Session_Namespace('user_data');
           $user_data->hash = $hash;

           $encryption = new Main_Crypt_Base64Encode;
           $id = $encryption->decode($hsid);
           $user = new Default_Model_User();

           if($user->countByField('id', $id) > 0)
                $this->view->person = $user->findbyHash($hash);
           
           if($this->view->person != null)
            $this->view->form = new Application_Form_PasswordReset;
        }
    }

    public function joinAction() {
       
        $form = new Application_Form_Join;
        $form->build('/join', null, null);    
        $post = $this->request->getPost();
        $response = array('success'=>false, 'error'=>'Form was not posted', 'message'=>'', 'logged'=>false, 'uid'=>0);
        
        if($this->request->isPost() && $form->isValid($post) ){
            
            $user = new Default_Model_User();
            $usename_count = $this->countUsername($post['username']);
            $email_count = $this->countEmail($post['email']);

            if( $usename_count == 0 && $email_count == 0 ){

            if($new_user = $user->create($post)){
            
                $response['success'] = true;
                $response['error'] = false;
                $response['message'] = "Welcome To ". SITE_NAME;
                $response['uid'] = $new_user;
                $creds =  $user->findById($new_user);
                $response['logged'] = Main_Auth::process(array('username'=>$creds['username'], 'password'=>trim($post['password'])));
                 
                try{ $this->sendInfoToNewUser($creds, trim($post['password'])); } catch(Exception $e){}
                     
            }else{
            
                 $response['success'] = false;
                 $response['error'] = "User Creation Failed";
                 $response['message'] = "An unknown error occurred creating your account.";
            
            }   
        
            }else{
            
                $response['success'] = false;
                $response['error'] = "User Exists";
                $msg = '';
                if( $usename_count > 0 ){
                    $msg .= 'Username is not available. <br />';
                }
                if( $email_count > 0 ){
                    $msg .= 'Email is not available.';
                }
                
                $response['message'] = $msg;
                
            }
        
        }

       if($this->xhr){
            $this->_asJson($response); 
       }
       
       $this->view->form = $form;
       
    }

    
        public function joinViaEventAction() {
            
         $form = new Application_Form_Join();
         $post = $this->request->getPost();
         $response = array('success'=>false, 'error'=>'Form was not posted', 'message'=>'', 'logged'=>false, 'uid'=>0);
        if($this->request->isPost() && $form->isValid($post) ){
            
            
            $user = new Default_Model_User();
            $usename_count = $this->countUsername($post['username']);
            $email_count = $this->countEmail($post['email']);

            if( $usename_count == 0 && $email_count == 0 ){

            if($new_user = $user->create($post)){
            
                $response['success'] = true;
                $response['error'] = false;
                $response['message'] = "Welcome To ". SITE_NAME;
                $response['uid'] = $new_user;
                $creds =  $user->findById($new_user);
                $response['logged'] = Main_Auth::process(array('username'=>$creds['username'], 'password'=>trim($post['password'])));
                 
                try{ $this->sendInfoToNewUser($creds, trim($post['password'])); } catch(Exception $e){}
                 
            
            }else{
            
                 $response['success'] = false;
                 $response['error'] = "User Creation Failed";
                 $response['message'] = "An unknown error occurred creating your account.";
            
              
            }   
        
            }else{
            
                $response['success'] = false;
                $response['error'] = "User Exists";
                $msg = '';
                if( $usename_count > 0 ){
                    $msg .= 'Username is not available. <br />';
                }
                if( $email_count > 0 ){
                    $msg .= 'Email is not available.';
                }
                
                $response['message'] = $msg;
                
            }
        
        }


       $this->_asJson($response); 
    }
    
    
    
    public function sendInfoToNewUser($info, $password){
        
        $message  = "Hello ". $info['fname']  . ",<br />\n";
        $message .= "Welcome to ". SITE_NAME . ", We're glad you joined!. Please see your login info below. <br />\n";
        $message .= " - user: ". $info['username'] . "<br />\n";
        $message .= " - pass: ". $password . "<br />\n";
        
        $text = strip_tags($message);
        
        $mail = new Zend_Mail();
        $mail->setBodyText($text);
        $mail->setBodyHtml( $message );
        $mail->setFrom(SITE_EMAIL, SITE_NAME);
        $mail->addTo($info['email'], $info['username']);
        $mail->setSubject('Welcome to '. SITE_NAME);
        $mail->send();
     
        
    }
    
    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector->gotoUrl('/');
    }

    public function countUsername($uname) {
        $user = new Default_Model_User();
        return $user->countByField('username', $uname);
    }

    public function countEmail($email) {
        $user = new Default_Model_User();
        return $user->countByField('email', $email);
    }


    protected function _asJson(array $data) {
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->getResponse()->setHeader('Content-type', 'application/json')
                            ->setBody(json_encode($data));
    }    

}
