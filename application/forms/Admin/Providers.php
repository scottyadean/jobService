<?php
class Application_Form_Admin_Providers extends Main_Forms_Builder {

   public $_id;
   public $customSubmitBtn = false;
   public $formLabel = 'Update';
   
   protected $_industries;
   protected $_visible; 
   
   public function build( $action = "/crud_provider/edit",
                          $id = null,
                          $method = "post" ) {
       
       $this->_id = $id;       
       $this->setName("providerform");
       $this->getData();
       $this->setMethod($method);
       $this->setAction($action);
       $this->formElementsFromTable('providers', $this->getFields());
       
       if($this->customSubmitBtn == false){
            $this->formElementsFromArray($this->getCustomFields());
       }
       
       $this->createElements();
    }

    /**
    * _industries
    * id	int(11) AI PK
    * name	varchar(255)
    * info	text
    * industry_id	int(11)
    * tags	varchar(255)
    * created	timestamp
    * modifed	timestamp    
    */
    public function getFields() {
        
     $fields = array("name"=>array('label'=>'Name', 'required'=> true),
                     "site" => array('label'=>'Web site', 'required'=> false),
                     "industry_id"=>array('label'=>'Industry', 'required'=> true, 'type'=>'select', 'multiOptions'=>$this->_industries), 
                     "visible"=>array('label'=>'Visible', 'required'=> true, 'type'=>'select', 'multiOptions'=>$this->_visible), 
                     "info"=>array('label'=>'Info ( Quick Overview )','required'=> false),
                     "detail"=>array('label'=>'Detail ( Detailed Infomation )','required'=> false, 'attributes'=>array('rows'=>'5', 'cols'=>'50', 'class'=>'textarea-large-size wysiwyg')),
                     "tags"=>array('required'=>false));   
        
           if( isset( $this->_id ) ) {
              
              $fields['id'] = array('default'=>$this->_id, 'type'=>'hidden', 'required'=> true,
                                    'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'));       
           }
           
           return $fields;
    }

    public function getData() {
    
        $this->_visible = array('Y'=>'Visible', 'N'=>'Hidden');    
    
        $industry = new Default_Model_Crud();
        $industry->setTable('industries');
        
        $industryData = array();
        $data = $industry->_index();
        
        foreach( $data as $d ) {
            $industryData[$d['id']] = $d['name'];
        }
        
        $this->_industries = $industryData;
        
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
    
    public static function onCreate($data) {    
        if(isset($data['info'])) {
            $data['info'] = strip_tags($data['info']);
        }

        return $data;
    }
    
    
    public static function onUpdate($data) {
        
        if(isset($data['info'])) {
            $data['info'] = strip_tags($data['info']);
        }
        
        $data['modifed'] = new Zend_Db_Expr('NOW()');
        
        return $data;
    }

    public static function onDelete($id) {
        
        $loc = new Default_Model_Locations;
        $loc->removeByProviderId($id);
        
        $ploc = new Default_Model_ProgramLocations;
        $ploc->removeByProviderId($id);
        
        return true;
    
    }



    public static function onValidate($data) {
        $errors = false;
        return $errors;
    }
    
}