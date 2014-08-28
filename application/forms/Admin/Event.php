<?php
class Application_Form_Admin_Event extends Main_Forms_Builder {

   public $_id;
   public $customSubmitBtn = false;
   public $formLabel = 'Update';
   public $_formId = 'eventsform';
   public $_cancelBtn = true;
   public $_hidden = false;
   public $_types = array("open"=>"Open", "limited registration" => "Limited registration", "open registration"=>"Open registration");
   
   public function build( $action = "/crud-event/edit",
                          $id = null,
                          $method = "post" ) {
       
       $this->_id = $id;
       
       $this->setName($this->_formId);
       $this->setMethod($method);
       $this->setAction($action);
       $this->formElementsFromTable('events', $this->getFields());
       
       if($this->customSubmitBtn == false){
            $this->formElementsFromArray($this->getCustomFields());
       }
       
       $this->createElements();
    }
    
    /*
    * `id`, `title`, `body`, `created`
    */
    public function getFields() {

         $fields = array("title"=>array('label'=>'Title', 'required'=> true), 
                         "type"=>array('label'=>'Event Type', 'required'=> true, 'type'=>'select', 'multiOptions'=>$this->_types), 
                         "visible"=>array('label'=>'Visible', 'required'=> true, 'type'=>'select', 'multiOptions'=>array('N'=>'Hidden','Y'=>'Visible')),
                         "seats"=>array('label'=>'Event Seats (default 0 for open events)', 'required'=> true, 'type'=>'text'), 
                         "message"=>array('label'=>'Sign Up Message','required'=> false, 'attributes'=>array('rows'=>'5', 'cols'=>'50', 'class'=>'')),
                         "body"=>array('label'=>'Body','required'=> true, 'attributes'=>array('rows'=>'5', 'cols'=>'50', 'class'=>'wysiwyg')),
                         "location" => array('label'=>'Location Name', 'required'=> false),
                         "address" => array('label'=>'Address', 'required'=> true),
                         "city" => array('label'=>'City', 'required'=> false),
                         "state"=>array('label'=>'State', 'required'=> true, 'type'=>'select', 'multiOptions'=>Main_Forms_Data:: AmericaStates()), 
                         "zip" => array('label'=>'Zip', 'required'=> false),
                         "site" => array('label'=>"Web Site", 'required'=> false),
                         "start_time"=>array(),
                         "end_time"=>array()
                           );
        
           if( isset($this->_hidden) && $this->_hidden == true) {
            $fields['created'] = array('default'=>'', 'type'=>'hidden', 'required'=> true, 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'));
           }else{
            
            $fields['created'] = array('label'=>'Event Date', 'default'=>'', 'type'=>'text', 'required'=> true, 'attributes' => array('class'=>'date_widget'));
            
           }
        
        
           if( isset( $this->_id ) ) {
              
              $fields['id'] = array('default'=>$this->_id, 'type'=>'hidden', 'required'=> true,
                                    'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'));       
           }
           
           return $fields;
    }


    public function getCustomFields() {
    $custom = array('submit' => array(
                                 'label'=>$this->formLabel,
                                 'type'=>'submit',
                                 'name'=>'submit',
                                 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'), 
                                 'options' => array('class'=>'btn btn-small btn-primary'),
                                 'ignore'=>true));
    
    
    if($this->_cancelBtn ) {
        
        
        $custom['cancel'] =  array(
                                  'label'=>'Cancel',
                                  'type'=>'button',
                                  'name'=>'cancel',
                                  'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'),
                                  'attributes'=>array('onclick'=>"window.history.back()"),
                                  'options' => array('class'=>'btn btn-small btn-primary'),
                                  'ignore'=>true       
                                 );
        
        
    }
    
    return $custom;
    
    }
    
    
    
    public static function onCreate($data) {
        
        if(isset($data['created'])) {
            $timestamp = strtotime($data['created']);
            $data['created'] = date("Y-m-d H:i:s", $timestamp);
        }
       
        
        return $data;

    }
    
    
    public static function onUpdate($data) {
        
        if(isset($data['created']) && !empty($data['created'])) {
            $timestamp = strtotime($data['created']);
            $data['created'] = date("Y-m-d H:i:s", $timestamp);
        }
        
       
        return $data;
    }
    
    
    public static function onDelete($data) {
        
        return $data;
        
    }


    
}