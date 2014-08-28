<?php
class Application_Form_Contact extends Main_Forms_Builder {    

   
   public function build( $action = "/contact.html" ) {
       $this->setName("contactusform");
       $this->setMethod("POST");
       $this->setAction($action);
       $this->formElementsFromArray($this->getCustomFields());
       $this->createElements();
    }

    public function getCustomFields() {
    $custom = array('fname' => array('label'=>'First Name',
                                    'type'=>'text',
                                    'name'=>'fname',
                                    'required'=> true,
                                    'options' => array('class'=>'')),
                    'lname' => array('label'=>'Last Name',
                                    'type'=>'text',
                                    'name'=>'lname',
                                    'required'=> true,
                                    'options' => array('class'=>'')),
                    'subject' => array('label'=>'Subject',
                                    'type'=>'text',
                                    'name'=>'subject',
                                    'required'=> true,
                                    'options' => array('class'=>'')),
                    'email' => array('label'=>'Email',
                                     'type'=>'text',
                                     'required'=> true,
                                     'name'=>'email',
                                     'options' => array('class'=>'')),
                    'emailVerify' => array('label'=>'Email Verify',
                                     'type'=>'text',
                                     'name'=>'emailVerify',
                                     'required'=> true,
                                     'options' => array('class'=>'')),
                    "message"  => array('label'=>'Message',
                                        'required'=> true,
                                        'name'=>'message',
                                        'type'=>'textarea',
                                        'attributes'=>array('rows'=>'5', 'cols'=>'50', 'class'=>'')),
                    'submit' => array('label'=>'Send',
                                     'type'=>'submit',
                                     'name'=>'submit',
                                     'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'), 
                                     'options' => array('class'=>'btn btn-small btn-primary'),
                                     'ignore'=>true));
        return $custom;
    }

    public function verify($params=array()) {
        
        var_dump($params);
        
    }


}