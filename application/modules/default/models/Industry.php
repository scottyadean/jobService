<?php
class Default_Model_Industry extends Zend_Db_Table_Abstract {

    public $onCreate;
    public $onUpdate;
    
    public $page_limit = null;
    public $page_offset = null;

    protected $_name = 'industries';
    protected $_primary = 'id';
    
    public function getInfo() {
        return $this->info();
    }
        
    public function cleanData($data) {
       $info = $this->getInfo();
       return array_intersect_key($data, $info['metadata']);  
    }

    public function _index($where = array()) {
        
        $select = $this->select();
        
        if( $where && is_array($where) ) {        
            foreach($where as $k=>$v) {
                $select->where("{$k}", $v);
            }
        }
        
        if( !empty($this->page_limit)  ) {
            $select->limit($this->page_limit, $this->page_offset);
        }
        
      return $this->fetchAll($select);    
    }
    
    public function _create($data) {
        
        if( isset($data['id']) ) {
            unset($data['id']);     
        }
       
        if(!empty($this->onCreate)) {
          
            $funct = explode("::",$this->onCreate);
            $data = call_user_func(array($funct[0], $funct[1]), $data);
        } 

       return $this->insert($this->cleanData($data));
    }

    public function _read($id) {
       return  $this->find((int)$id)->current();
    }

    
    public function _update($data) {
        
        if(!empty($this->onUpdate)) {  
            $funct = explode("::",$this->onUpdate);
            $data = call_user_func(array($funct[0], $funct[1]), $data);
        }
        
        $where = array('id = ?' => (int)$data['id']);
        return $this->update($this->cleanData($data), $where);
    }
        
    public function _delete($id) {
       $found = $this->find((int)$id)->current()->toArray();
       if(count($found) > 0) {
            $where = array('id = ?' => (int)$id);
           return  $this->delete($where);
       }
        return false;
    }

    public function _count($where) {
       $select = $this->select();
        
        if( $where && is_array($where) ) {
            foreach($where as $k=>$v){
                $select->where("{$k}", $v);
            }
        }
       
       $select->from($this->_name,'COUNT(id) AS num');
       return $this->fetchRow($select)->num;
    }
    
    
    
    
    public function seed() {
        $data = Main_Forms_Data::Industries();
        
        foreach($data as $k=>$v  ) {
            $this->_create(array('name'=>trim($v)));
        }
        
    }
    
    
    
    
    
    
    
    
}