<?php
class Application_Form_Admin_EventDuplicate extends Main_Forms_Builder {

   public $_id;
   public $_formId = 'eventsduplicateform';
   public $_created;
   public function build( $action = "/admin/events/duplicate",
                          $id = null,
                          $method = "post" ) {
       
       $this->_id = $id;

       $this->setName('eventsduplicateform');
       $this->setMethod($method);
       $this->setAction($action);
       $this->formElementsFromTable('events', $this->getFields());
       $this->createElements();
       
       
       
    }
    

    
    /*
    * `id`, `created`
    */
    public function getFields() {

         $fields = array();
         $fields['title'] = array("title"=>array('label'=>'Title', 'required'=> true));
         $fields['created'] = array('label'=>'Event Date', 'default'=>$this->created, 'type'=>'text', 'required'=> true, 'attributes' => array('class'=>'date_widget'));
         $fields['id'] = array('default'=>$this->_id, 'type'=>'hidden', 'required'=> true, 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'));       
         $fields['submit'] = array('label'=>'Copy', 'type'=>'button', 'name'=>'submit-event-duplicate', 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'), 
                                   'options' => array('class'=>'btn btn-small btn-primary'),'ignore'=>true);
        
         return $fields;
    }

    
}