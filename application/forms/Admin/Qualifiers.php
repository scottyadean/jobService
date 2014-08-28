<?php
/** 
* _Qualifiers Columns:
* `id`, `question`, `answer`, `type`
*/
class Application_Form_Admin_Qualifiers extends Main_Forms_Builder {

   public $_id;
   public $customSubmitBtn = false;
   public $formLabel = 'Update';
   
   public function build( $action = "/crud-modules/edit",
                          $id = null,
                          $method = "post" ) {
       
       $this->_id = $id;
       
       $this->setName("qualifiersform");
       $this->setMethod($method);
       $this->setAction($action);
       $this->formElementsFromTable('qualifiers', $this->getFields());
       
       if($this->customSubmitBtn == false){
            $this->formElementsFromArray($this->getCustomFields());
       }
       
       $this->createElements();
    }

    
    
    public function getFields() {

         $fields = array("answer"=>array('label'=>'Answer', 'required'=> true), 
                         "question"=>array('label'=>'Question', 'required'=> true, 'attributes'=>array('rows'=>'5', 'cols'=>'50' )),
                         "type"=>array('label'=>'Type', 'required'=> true, 'type'=>'select', 'multiOptions'=>array("checkbox"=>"Checkbox")), 
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