<?php
class Application_Form_Admin_SkillSrcCenters extends Main_Forms_Builder {

   public $_id;
   public $customSubmitBtn = false;
   public $formLabel = 'Update';
   public $_formId = 'skillsrc_centers_form';
   public $_cancelBtn = true;
   public $_hidden = false;
 
   public function build( $action = "/crud-centers/edit",
                          $id = null,
                          $method = "post" ) {
       
       $this->_id = $id;
       
       $this->setName($this->_formId);
       $this->setMethod($method);
       $this->setAction($action);
       $this->formElementsFromTable('skillsrc_centers', $this->getFields());
       
       if($this->customSubmitBtn == false){
            $this->formElementsFromArray($this->getCustomFields());
       }
       
       $this->createElements();
    }
    
    /*
    * `id`, `name`, `address`, `city`, `state`, `zip`, `phone`, `email`, `tty`, `hours`, `color`
    */
    public function getFields() {

         $fields = array("name"=>array('label'=>'Center Name', 'required'=> true), 
                         "address" => array('label'=>'Address', 'required'=> true),
                         "city" => array('label'=>'City', 'required'=> true),
                         "state"=>array('label'=>'State', 'required'=> true, 'type'=>'select', 'multiOptions'=>Main_Forms_Data:: AmericaStates()), 
                         "zip" => array('label'=>'Zip', 'required'=> false),
                         "phone" => array('label'=>'Phone', 'required'=> false),
                         "site" => array('label'=>"Web Site", 'required'=> false),
                         "tty"=>array(),
                         "hours"=>array(),
                         "color"=>array('label'=>"Color Code", "type"=>'hidden'),
                           );
        
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