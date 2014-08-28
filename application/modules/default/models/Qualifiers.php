<?php
/** 
* Columns:
* `id`, `question`, `answer`, `type`
*/
class Default_Model_Qualifiers extends Zend_Db_Table_Abstract {
    
    protected $_name = 'qualifiers';
    protected $_primary = 'id';
    
    public $page_limit = null;
    public $page_offset = 0;
    public $order_by;
   
    public function _index($where = array()) {
        
        $select = $this->select();
        
        if(!empty($where) && is_array($where) ){
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
    
    public function _create($data) {
        $data = $this->_cleanData($data);
        return $this->insert($data);
    }
    
    public function _read($id) {
        $select = $this->select();
        return $this->fetchRow($select);
    }
    
    public function _update($data) {
        $data = $this->_cleanData($data);
        $where = array('id = ?' => (int)$data['id']);
        return $this->update($data, $where);
    }
    
    public function _delete($id) {     
        return $this->delete(array('id = ?' => (int)$id));
    }
    
    public function _count($id=0) {
       $select = $this->select();
       $select->from($this->_name, array("num"=>"COUNT(id)"));
       
       if((int)$id > 0){
        $select->where("id = ?", $id);
       }
       
       $result = $this->fetchRow($select);
       return (int)$result["num"];
    }
    
    
    protected function _cleanData($data) {
        return $data;
    }
 
}