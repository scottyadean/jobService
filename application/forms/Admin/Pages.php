<?php
class Application_Form_Admin_Pages extends Main_Forms_Builder {

   public $_id;
   public $customSubmitBtn = false;
   public $formLabel = 'Update';
   
   public $html_mods_left;
   public $html_mods_right;
   public $html_mods_header;

   public function build( $action = "/crud-pages/edit",
                          $id = null,
                          $method = "post" ) {
       
       $this->_id = $id;
       
       $this->setName("pagesform");
       $this->setMethod($method);
       $this->setAction($action);
       
      $this->getModules();
       
       
       $this->formElementsFromTable('pages', $this->getFields());
       
       if($this->customSubmitBtn == false){
            $this->formElementsFromArray($this->getCustomFields());
       }
       
       $this->createElements();
    }

    
    public function getFields() {

         $fields = array("title"=>array('label'=>'Title', 'required'=> true), 
                         "header_id"=>array('label'=>'Header', 'required'=> true, 'type'=>'select', 'multiOptions'=>$this->html_mods_header), 
                         //"nav_left_display"=>array('label'=>'Display Left Nav', 'required'=> true, 'type'=>'select', 'multiOptions'=>array("Y"=>"Yes", "N"=>"No")), 
                         "module_id"=>array('label'=>'Left Module', 'required'=> true, 'type'=>'select', 'multiOptions'=>$this->html_mods_left), 
                         //"nav_right_display"=>array('label'=>'Display Right Nav', 'required'=> true, 'type'=>'select', 'multiOptions'=>array("Y"=>"Yes", "N"=>"No")), 
                         "module_id_right"=>array('label'=>'Right Module', 'required'=> true, 'type'=>'select', 'multiOptions'=>$this->html_mods_right), 
                         "body"=>array('label'=>'Body','required'=> false, 'attributes'=>array('rows'=>'5', 'cols'=>'50', 'class'=>'textarea-large-size')));
        
           if( isset( $this->_id ) ) {
              
              $fields['id'] = array('default'=>$this->_id, 'type'=>'hidden', 'required'=> true,
                                    'disableDecorator' => array('HtmlTag', 'Label', 'DtDdWrapper'));       
           }
           
           return $fields;
    }

    public function  getModules() {
        
        $model = new Default_Model_Module;
        $modules = $model->_index();
        $this->html_mods_header = $this->html_mods_left = $this->html_mods_right = array(0=>"  ----  ");
        foreach($modules as $k=>$m) {
            
            if($m->type == 'L'){
                $this->html_mods_left[$m->id] = $m->name;
            }elseif($m->type == 'R'){
                $this->html_mods_right[$m->id] = $m->name;
            }else{
               $this->html_mods_header[$m->id] = $m->name;  
            }
        }
        
       
  
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
        
        if(isset( $data['title'])) {
            $data['slug'] = Base_Functions_Strings::Slug($data['title']);
        }
        
        return $data;

    }
    
    
    public static function onUpdate($data) {
        
        $data = self::onCreate($data);
        $data['updated'] = new Zend_Db_Expr('NOW()');
        
        return $data;
    }


    public static function onValidate($data) {

        //check if the title is unique
        $errors = false;
        
        
        if(isset($data['title'])) {
           
           $pages = new Default_Model_Crud();
           $pages->setDbName('pages');
           $where = array( 'slug = ?' => Base_Functions_Strings::Slug($data['title']));
           
           if(isset($data['id']) ) {
                $where['id != ?'] = (int)$data['id'];
           } 
           
           $count = $pages->_count($where);
          
           if($count != '0'){
                $errors =  array('title'=> array('Title must be unique.'));
           }
           
        }
        
        return $errors;
    }
    
}