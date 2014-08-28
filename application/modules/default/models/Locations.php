<?php

class Default_Model_Locations extends Zend_Db_Table_Abstract {

    protected $_name = 'providers_locations';
    protected $_primary = 'id';
    
    public $page_limit = null;
    public $page_offset = 0;
    public $order_by;
    public $group_by;
    public $fields;
    
   
    public function _index($where = array()) {
        
         $select = $this->select();
        
        if(!empty($where) && is_array($where) ) {
            foreach($where as $k=>$v){
                $select->where("{$k}", $v);
            }
        }
        
       if( !empty($this->page_limit)  ) {
            $select->limit($this->page_limit, $this->page_offset);
        }
        
        if(!empty($this->order_by)) {
           $select->order($this->order_by); 
        }
        
        if(is_array($this->fields)) {
            $select->from(array($this->_name),$this->fields);
        }
        
        if(isset($this->group_by) && !empty($this->group_by)) {
            $select->group($this->group_by);
        }
        
        
        return $this->fetchAll($select);
    }
    
    
    public function _create($data) {
        return $this->insert($data);
    }
    
    public function _read($id) {
        return $this->find($id)->current();
    }
    
    public function _update($data) {
        $data['modifed'] = new Zend_Db_Expr('NOW()');
        return $this->update($data, array('id = ?' => (int)$data['id']));
    }
    
    public function _delete($id) {
        return $this->delete(array('id = ?' => $id));
    }
    
    public function removeByProviderId($provider_id) {
        return $this->delete(array('provider_id = ?' => $provider_id));
    }
    
    public function _search($value, $field = 'name') {
        $select = $this->select()->where("{$field} = ?", $value);
        return $this->fetchRow($select);
    }

    public function _count($where = null) {
       $select = $this->select();
        
        if( !empty($where) && is_array($where) ) {
            foreach($where as $k=>$v){
                $select->where("{$k}", $v);
            }
        }
       
       $select->from($this->_name,'COUNT(id) AS num');
       return (int)$this->fetchRow($select)->num;
    }

}

