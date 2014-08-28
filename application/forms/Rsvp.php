<?php
class Application_Form_Rsvp extends Main_Forms_Builder {

   public $_id;
   public $_event_id;
   public $formLabel = 'Sign Up';
   public $_formId   = 'rsvpform';

   public function build( $action = "/event-signup",
                          $id = null,
                          $event_id,
                          $method = "post" ) {
       
       $this->_id = $id;
       $this->_event_id = (int)$event_id;
       
       $this->setName($this->_formId);
       $this->setMethod($method);
       $this->setAction($action);
       $this->formElementsFromTable('events_rsvp', $this->getFields());
       $this->formElementsFromArray($this->getCustomFields());
       
       $this->createElements();
    }
    
    /*
    * `id`, `event_id`, `status`, `fname`, `lname`, `email`, `phone`, `created`
    */
    public function getFields() {

         $fields = array("event_id"=>array('default'=>$this->_event_id, 'type'=>'hidden', 'required'=> true, 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper')), 
                         "fname"=>array('label'=>'First Name', 'required'=> true, 'type'=>'text'), 
                         "lname"=>array('label'=>'Last Name','required'=> true, 'type'=>'text'),
                         "email"=>array('label'=>'Email','required'=> true, 'type'=>'text'),
                         "phone" => array('label'=>'Phone', 'required'=> false));
        
        
           if( isset( $this->_id ) ) {
              
              $fields['id'] = array('default'=>(int)$this->_id, 'type'=>'hidden', 'required'=> true,
                                    'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'));       
           }
           
           return $fields;
    }


    public function getCustomFields() {
    return array("email2"=>array('label'=>'Email Verify','required'=> true, 'type'=>'text'),
                 
                "robots"=>array('label'=>'Please Enter The Value In the box','required'=> true, 'type'=>'text', 'ignore'=>true),
                 'submit' => array(
                                 'label'=>$this->formLabel,
                                 'type'=>'submit',
                                 'name'=>'submit',
                                 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'), 
                                 'options' => array('class'=>'btn btn-small btn-primary'),
                                 'ignore'=>true));

    }
    
    
    
    public static function onCreate($data) {
        
        
        return $data;

    }
    
    
    public static function onUpdate($data) {
        
        
        return $data;
    }
    
    
    public static function onDelete($data) {
        
        return $data;
        
    }

    public static function onValidate($data) {
        
        $errors = null;
        
        return $errors;
        
    }
    
    

    
}