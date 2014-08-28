<?php
class Application_Form_Admin_PasswordReset extends Main_Forms_Builder {

    public $_message;
    public $_link;
    public $_hash;
    public $_id;

    public function build($user = null) {
        $this->setName("password-reset-form");
        $this->setMethod('post');
        $this->setAction("/admin/users/reset-password/?render=request");
        $this->getData($user);
        $this->formElementsFromArray($this->getCustomFields());
        $this->createElements();
    }
    
    public function getData($user) {
       
        //encrypt the id so we can send it along in the link
        $encryption = new Main_Crypt_Base64Encode();
        $this->_id = $encryption->encode($user->id);
    
        //create a hash to be saved to the user status feild we will find this user 
        //later when they click on the link. the link will be good for a day
        $this->_hash = Main_Salt::getResetPasswordHash( $user->username , date("D") );

        //build the return link with the params in place. 
        $this->_link = SITE_URL."/reset/password/h/{$this->_hash}/u/{$this->_id}";
       
        //create the message that will go to the user. 
        $this->_message  = "Hello ".$user->fname.",";
        $this->_message .= "\nThe ".SITE_NAME." Admin have sent you a password re-set link.";
        $this->_message .= "\nPlease click or copy and paste the link below into your browser to reset your password. ";
        $this->_message .= " Please Note: Do not click on any reset passwords links that do not start with: ".SITE_URL;
        $this->_message .= "\n ---------------- reset link below ------------------------ \n";
        
        $this->_message .= $this->_link;
        
    }

    public function getCustomFields() {

       return array("textarea"=>array( "label"=>"Email Message",
                                        "type"=>"textarea",
                                        "default"=>$this->_message,
                                        'options' => array('class'=>'standard-textarea-size', 'cols'=>50, 'rows'=>5),
                                       ),
                    'hash'=>array('required'=> true, 'default'=>$this->_hash,  'type'=>'hidden', 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper')),
                    'id'=>array('required'=> true, 'default'=>$this->_id, 'type'=>'hidden', 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper')),
                    'submit' => array( 'label'=>'Send',
                                        'type'=>'submit',
                                        'name'=>'submit',
                                        'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'), 
                                        'options' => array('class'=>'btn btn-small btn-primary'),
                                        'ignore'=>true));
    }   
}