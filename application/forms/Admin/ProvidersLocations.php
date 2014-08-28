<?php
class Application_Form_Admin_ProvidersLocations extends Main_Forms_Builder {

   public $_id;
   public $customSubmitBtn = false;
   public $formType = 'Add';
   public $insuranceTypes = array();
   
   public function build( $action = "/crud_provider/locations/new/",
                          $id = null,
                          $method = "post" ) {
       
       $this->_id = $id;
       
       if(!is_null($this->_id)) {    
            $this->formType = 'Update';
       }
       
       $this->setName("providerslocations");
       $this->setMethod($method);
       $this->setAction($action);
       $this->formElementsFromTable('providers_locations', $this->getFields());
       
       if($this->customSubmitBtn == false){
            $this->formElementsFromArray($this->getCustomFields());
       }
       
       $this->createElements();
    }

  /**
    *  `provider_id`,
    * `address`,
    *  `address2`,
    *  `city`,
    *  `state`,
    *  `county`,
    *  `zip`,
    *  `email_public`,
    *  `phone_public`,
    *  `fax_public`,
    *  `email_private`,
    *  `phone_private`,
    *  `fax_private`
   */
    public function getFields() {

         $fields = array("provider_id" => array('required'=> true,  'type'=>'hidden', 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper')),
                         "address" => array('label'=>'Address', 'required'=> true),
                         "address2"=>array('label'=>'Address 2','required'=> false, 'attributes'=>array('rows'=>'4', 'cols'=>'8')),
                         "city" => array('label'=>'City', 'required'=> true),
                         "state"=>array('label'=>'State', 'required'=> true, 'type'=>'select', 'multiOptions'=>Main_Forms_Data:: AmericaStates()), 
                         "county" => array('label'=>'County', 'required'=> false),
                         "zip" => array('label'=>'Zip', 'required'=> true),
                         "site" => array('label'=>"Site Url (If differnt from main site url)"),
                         "email_public" => array('label'=>'Email public', 'required'=> false),
                         "phone_public" => array('label'=>'Phone public', 'required'=> false),
                         
                         "fax_public" => array('label'=>'Fax public', 'required'=> false),
                         "email_private" => array('required'=> false),
                         "phone_private" => array('required'=> false),
                         "fax_private" => array('required'=> false),
           
                         );
        
           if( !empty( $this->_id ) ) {
              
              $fields['id'] = array('default'=>$this->_id, 'type'=>'hidden', 'required'=> true,
                                    'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'));       
           }
           
           return $fields;
    }


    public function getCustomFields() {
        
    if($this->customSubmitBtn) {
     return array();
    }
    $custom = array('submit' => array(
                                 'label'=>$this->formType,
                                 'type'=>'submit',
                                 'name'=>'submit',
                                 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'), 
                                 'options' => array('class'=>'btn btn-small btn-primary'),
                                 'ignore'=>true),
                   'cancel' => array(
                                  'label'=>'Cancel',
                                  'type'=>'button',
                                  'name'=>'cancel',
                                  'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'),
                                  'attributes'=>array('onclick'=>"window.history.back()"),
                                  'options' => array('class'=>'btn btn-small btn-primary'),
                                  'ignore'=>true       
                                 ));
    return $custom;
    
    
    }   
}