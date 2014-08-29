<?php

class Default_Model_Program  extends Zend_Db_Table_Abstract {

    protected $_name = 'providers_programs';
    protected $_primary = 'id';
    protected $_dependentTables = array( 'Default_Model_ProgramLocations');
    
    protected $_current;
    
    public $page_limit = null;
    public $page_offset = 0;
    
    public $order_by;
    public $group_by;
    
    public function getProgramLocations() {
    return $this->_current->findManyToManyRowset(
            'Default_Model_Locations','Default_Model_ProgramLocations')->toArray();    
    }
    
    

    public function _index($where = array()) {
    
        $select = $this->select()->from($this->_name)
                                 ->setIntegrityCheck(false)->joinLeft(array('i'=>'industries'),
                                 $this->_name.'.industry_id = i.id', array('name AS industry_name','id AS industry_id'));
        
        if(!empty($where) && is_array($where) ) {
            foreach($where as $k=>$v){
                $select->where("{$this->_name}.{$k}", $v);
            }
        }
        
        if(!empty($this->group_by)) {
            $select->group($this->_name.'.'.$this->group_by);
        }
        
        if( !empty($this->page_limit)  ) {
            $select->limit($this->page_limit, $this->page_offset);
        }
        
        if(!empty($this->order_by)) {
            $select->order($this->order_by);    
        }
    
        return $this->fetchAll($select);
    }
    
    public function all($regExp = false) {
      
      $select = $this->select()->from(array("{$this->_name}"=>$this->_name))->setIntegrityCheck(false)
                     ->join(array('i'=>'industries'),
                         $this->_name.'.industry_id = i.id', array('name AS industry_name','id AS industry_id'))
                     ->order($this->_name.'.name ASC');
    
       //$select->where($this->_name.'.visible = ?', 'Y'); 
    
    
    if($regExp != false) {          
        $select->where($this->_name.".name REGEXP '^[".$regExp."]'");
    }
    
    return $this->fetchAll($select);
    
    }
    
    

    public function _joinProviders($where = array()) {
    
        $select = $this->select()->from($this->_name)
                                 ->setIntegrityCheck(false)
                                 ->join(array('p'=>'providers'),
                                  $this->_name.'.provider_id = p.id', array('name AS provider_name'));
        
        
        if(!empty($where) && is_array($where) ) {
            foreach($where as $k=>$v){
                $select->where("{$this->_name}.{$k}", $v);
            }
        }
        
        if( !empty($this->page_limit)  ) {
            $select->limit($this->page_limit, $this->page_offset);
        }
        
        
        if(!empty($this->order_by)) {
            $select->order($this->order_by);    
        }
    
        return $this->fetchAll($select);
    }
    
    public function _create($data) {
        return $this->insert($data);
    }
    
    public function findById($id) {
       $this->_current = $this->find($id)->current();
       return $this->_current;
    }

    public function _read($id) {
        $this->_current = $this->find($id)->current();
        return $this->_current;
    }
    
    public function _update($data) {
        $data['modifed'] = new Zend_Db_Expr('NOW()');
        return $this->update($data, array('id = ?' => (int)$data['id']));
    }
 
    public function _fuzzyupdate($data, $where = array('id = ?' => 0)) {
        $data['modifed'] = new Zend_Db_Expr('NOW()');
        return $this->update($data, $where);
    }
    
    public function _search($value, $field = 'name') {
       $select = $this->select()->where("{$field} = ?", $value);
       return $this->fetchRow($select);
    }

    public function _count($where = array()) {
       
       $select = $this->select();
        
        if( !empty($where) && is_array($where) && count($where) > 0 ) {
            foreach($where as $k=>$v){
                $select->where("{$this->_name}.{$k}", $v);
            }
        }
       
       $select->from($this->_name,'COUNT(id) AS num');
       return (int)$this->fetchRow($select)->num;
    }

    
      public function findbyField($val, $field = 'name', $regEx = 'starts') {
        
        $field = trim($field);
        
                
        if(empty($field)) {
            $field = 'name';    
        }
        
        if($field == 'tags') {
            $field = 'tag';    
        }

        if( $regEx == 'starts' ) {
            $val = "$val%";
            $opt = 'LIKE';
        }
        
        if( $regEx == 'contains' ) {
            $val = "%$val%";
            $opt = 'LIKE';
        }
        
        if( $regEx == 'equals' ) {
            $val = "%$val%";
            $opt = '=';
        }
        
        if(empty($opt)) {
            $val = "%$val%";
            $opt = 'LIKE';
        }

       if($field == "zip" || $field == "city"){ 

      
        $locations = new Default_Model_Locations;
        
        if( $field == 'zip' ) {
          $opt = "=";
          $val = str_replace("%", "", $val);
          // 22043
          if(strlen ($val) < 5) {
              $opt = "LIKE";
              $val = $val."%";
          }
        }
        
        
        
        $found = $locations->_index(array("{$field} {$opt} ?" => $val  ));
        $in = array();
        
        foreach($found  as $k=>$f) {
             $in[] = $f->provider_id;
        }


        $select = $this->select()->from(array($this->_name=>$this->_name))->setIntegrityCheck(false)
                                  ->joinLeft(array('i'=>'industries'),
                                      $this->_name.'.industry_id = i.id', array('name AS industry_name','id AS industry_id'))
                                  ->order($this->_name.'.name ASC');
        
        if(count($in) == 0) {
            return array();
        }
        $select->where($this->_name.".id IN (?)", $in);
        return $this->fetchAll($select)->toArray();
              
       
       }else{
      
        $select = $this->select()->from(array($this->_name=>$this->_name))->setIntegrityCheck(false)
                          ->joinLeft(array('i'=>'industries'),
                           $this->_name.'.industry_id = i.id', array('name AS industry_name','id AS industry_id'))
                          ->order($this->_name.'.name ASC');
        
        $select->where($this->_name.".".$field." ".$opt." ?", "$val");
        
        return $this->fetchAll($select)->toArray();
      
      
       }
       
       
        $select = $this->select()->from(array($this->_name=>$this->_name))->setIntegrityCheck(false)
                                  ->joinLeft(array('i'=>'industries'),
                                      $this->_name.'.industry_id = i.id', array('name AS industry_name','id AS industry_id'))
                                  ->order($this->_name.'.name ASC');
        
        if(count($in) == 0) {
            return array();
        }
        
        $select->where($this->_name.".id IN (?)", $in);
   
       print $select->__toString();
   exit;
        return $this->fetchAll($select)->toArray();
              
       
       
       
    }
     

      
       
       
       
       
    
    
    
}

