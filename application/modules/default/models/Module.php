<?php
class Default_Model_Module extends Zend_Db_Table_Abstract {

    public $page_limit;
    public $page_offset = 0;
    public $order_by;
    
    protected $_name = 'modules';
    protected $_primary = 'id';
    protected $_current;

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
    
        return $this->fetchAll($select);
    }

    public function _read($id) {
        $this->_current = $this->find($id)->current();
        return $this->_current;
    }
    
    public function _getByIds($ids=array() , $where=array(), $field = 'id'){
    
        $select = $this->select()->where($field.' IN (?)',  $ids);
        
          if( $where && is_array($where) ) {
            foreach($where as $k=>$v) {
                $select->where($this->getTable.".{$k}", $v);
            }
        }
        
        return $this->fetchAll($select);

        
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