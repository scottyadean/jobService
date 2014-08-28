<?php
class Application_Form_Admin_PageModules extends Main_Forms_Builder {

   public $_id;
   public $customSubmitBtn = false;
   public $formLabel = 'Update';

   
   public function build( $action = "/crud-modules/edit",
                          $id = null,
                          $method = "post" ) {
       
       $this->_id = $id;
       
       $this->setName("pagemodulesform");
       $this->setMethod($method);
       $this->setAction($action);
       $this->formElementsFromTable('modules', $this->getFields());
       
       if($this->customSubmitBtn == false){
            $this->formElementsFromArray($this->getCustomFields());
       }
       
       $this->createElements();
    }

    
    
    
    
    public function getFields() {

         $fields = array("name"=>array('label'=>'Title', 'required'=> true), 
                         "body"=>array('label'=>'Body','required'=> false, 'attributes'=>array('rows'=>'5', 'cols'=>'50', 'class'=>'textarea-large-size')),
                         "type"=>array('label'=>'Position', 'required'=> true, 'type'=>'select', 'multiOptions'=>array("R"=>"Right", "L"=>"Left", "H"=>"Header")), 
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