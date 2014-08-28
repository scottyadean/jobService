<?php
class Application_Form_Admin_ProvidersPrograms extends Main_Forms_Builder {

   public $_id;
   public $customSubmitBtn = false;
   public $formType = 'Add';
   public $insuranceTypes = array();
   
   public $_industries;
   public $_locations;
   public $_selectedLocations;
   public $_params = array();
   
   public function build( $action = "/crud_provider/programs/new/",
                          $id = null,
                          $method = "post" ) {
       
       
       $this->_id = $id;
       
       if(!is_null($this->_id)) {    
            $this->formType = 'Update';
       }
       $this->getData();
       
       $this->setName("providersprograms");
       $this->setMethod($method);
       $this->setAction($action);
       $this->formElementsFromTable('providers_programs', $this->getFields());
       $this->formElementsFromArray($this->getCustomFields());
       
       
       $this->createElements();
    }

  /**
    * `id`,
    * `provider_id`,
    * `industry_id`,
    * `name`,
    * `tag`,
    * `info`,
    * `end_date`,
    * `start_date`,
   */
    public function getFields() {

         $fields = array("provider_id" => array('required'=> true,  'type'=>'hidden', 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper')),
                         "industry_id"=>array('label'=>'Industry', 'required'=> true, 'type'=>'select', 'multiOptions'=>$this->_industries), 
                         "tag" => array('label'=>'Search Tags', 'required'=> false),
                         "name" => array('label'=>'name', 'required'=> true),
                         "modifed"=>array('default'=>time(), 'value'=>time(), 'type'=>'hidden', 'required'=> false, 'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper')),
                         "start_date" => array('label'=>'Start Date', 'required'=> false, 'attributes'=>array('class'=>'date_widget')),
                         "end_date" => array('label'=>'End Date', 'required'=> false, 'attributes'=>array('class'=>'date_widget') ),
                         "info"=>array('label'=>'Info','required'=> false, 'attributes'=>array('rows'=>'5', 'cols'=>'50', 'class'=>'textarea-large-size wysiwyg')),
                     );
        
           if( !empty( $this->_id ) ) {
              
              $fields['id'] = array('default'=>$this->_id, 'type'=>'hidden', 'required'=> true,
                                    'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'));       
           }
           
           return $fields;
    }
    
    
      public function getData() {
    
        $locations = new Default_Model_Crud();
        $locations->setTable("providers_locations");
        $addresses = $locations->_index(array('provider_id  = ?' => (int)$this->_params['provider_id']));
       

        $ploc = new Default_Model_ProgramLocations;
        $savedLocation = !empty($this->_params['id']) ? $ploc->findById($this->_params['id'])->toArray() : array();
        
        $this->_selectedLocations = array(); 
        
        if( !empty($savedLocation) && is_array($savedLocation) ) {
          foreach($savedLocation  as $key=>$loc) {
            $this->_selectedLocations[] = $loc["location_id"];
          }
        }
       
        $locations = array();
        foreach( $addresses as $d ) {
            $locations[$d['id']] = $d['address'];
        }
        
        $this->_locations = $locations;
        
       
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
        
 
     $custom = array("address_ids"=>array('label'=>'Addresses (select all that apply)',
                                          'name'=>'address_ids[]', 'required'=> false,
                                          'default'=>$this->_selectedLocations,
                                          'type'=>'select', 'multiOptions'=>$this->_locations));
        
    if($this->customSubmitBtn) {
        return $custom;
    }
    
    $custom['submit'] = array('label'=>$this->formType,
                              'type'=>'submit',
                              'name'=>'submit',
                              'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'), 
                              'options' => array('class'=>'btn btn-small btn-primary'),
                              'ignore'=>true);
    
    $custom['cancel'] = array('label'=>'Cancel',
                               'type'=>'button',
                               'name'=>'cancel',
                               'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'),
                               'attributes'=>array('onclick'=>"window.history.back()"),
                               'options' => array('class'=>'btn btn-small btn-primary'),
                               'ignore'=>true);
    return $custom;
    
    }
    
    public static function onDelete($id){
        $ploc = new Default_Model_ProgramLocations;
        return $ploc->removeById($id);
    }
    
    public static function onInsert($data) {
        return self::AddressSave($data, $data['__id']);       
    }
    
    public static function onUpdate($data) {
        
        return self::AddressSave($data, $data['id']);
    }
    
    public static function AddressSave($data, $id) {
        
        if(isset($data['address_ids']) && is_array($data['address_ids'])) {            
            $ploc = new Default_Model_ProgramLocations;
            $ploc->removeById($id);
            $pid = $data["provider_id"];
            foreach( $data['address_ids'] as $k=>$lid ) {
                $ploc->assign($pid, $id, $lid);
            }
        }
        
        return $data;
    }
    
}