<?php
class Default_Model_Page extends Zend_Db_Table_Abstract {

    public $page_limit;
    public $page_offset = 0;
    public $order_by;
    
    
    protected $_name = 'pages';
    protected $_primary = 'id';
    protected $_current;
    protected $_dependentTables = array( 'Default_Model_PagesModules');
    
    
    public function getPageModules() {
    return $this->_current->findManyToManyRowset(
            'Default_Model_Module','Default_Model_PagesModules')->toArray();    
    }
    
    
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

    
    public function _top10() {

        $select = $this->select()->where(  $this->_name.'.views > ?', 1 )->limit(10)->order('views DESC');
        return $this->fetchAll($select); 
    }
    
    public function _read($id) {
        $this->_current = $this->find($id)->current();
        return $this->_current;
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

