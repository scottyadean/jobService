<?php
class Default_Model_Crud extends Zend_Db_Table_Abstract {

    public $onCreate;
    public $afterInsert;
    public $onUpdate;
    public $onDelete;
    public $onValidate;
    
    public $page_limit = null;
    public $page_offset;

    protected $_name = '_not_set_';
    protected $_primary = 'id';
    
    
    public function _validate( $data ) {
        
        if(!empty($this->onValidate)) {
            $funct = explode("::",$this->onValidate);
            return call_user_func(array($funct[0], $funct[1]), $data);
        }
        
        return true;
    }
    
    public function setPrimary(  $primary ) {
        $this->_primary = $primary;
    }

    public function setDbName(  $name ) {
        $this->_name = trim($name);
        return $name;
    }
    
    public function setTable($name) {
        return $this->setDbName($name);
    }
    
    public function getTable($name) {
        return $this->_name;
    }
    

    public function getInfo() {
        return $this->info();
    }

    public function cleanData($data) {
      $info = $this->getInfo();
      return  array_intersect_key($data, $info['metadata']);       
    }
    
    
    public function crudData($ex) {
        
       if( !is_array($ex)) {
            $ex = explode(",",$ex);
       }
        
       $f = $this->getInfo();
       $v = array();

       foreach( $f['metadata'] as $k=>$m ){
        $feilds[] = $k;
        if(!in_array((string)$k, $ex )){
              $v[] = $k;      
         }
       }
       
       $d = new stdClass;
       $d->fields = $feilds;
       $d->excluded = $ex;
       $d->visible = $v;
       $d->name = $this->_name;
       $d->primary = $this->_primary;
       return $d;
    }
    
    
    public function _index($where = null) {
  
        $select = $this->select();
        
        if( isset($where) && is_array($where) ) {
            
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

        
        
       $status = $this->insert($this->cleanData($data));
       
       if(!empty($this->afterInsert)) {
            
            $funct = explode("::",$this->afterInsert);
            $data["__id"] = $status;
            call_user_func(array($funct[0], $funct[1]), $data);
       }
       
       
       return $status;
       
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
            $del =  $this->delete($where);
             
            if(!empty($del) && !empty($this->onDelete)) {
                $funct = explode("::",$this->onDelete);
                call_user_func(array($funct[0], $funct[1]), $id);
            }

       }
       
        return $del;
    }

    public function _deleteWhere($where = array('id = ?'=> 0)) {
       return $this->delete($where);
    }
    
    
    public function _in($field = 'id', $ids = array(), $where = false) {

        $select = $this->select()->where($field.' IN (?)',  $ids);
        
          if( $where && is_array($where) ) {
            
            foreach($where as $k=>$v) {
                $select->where($this->getTable.".{$k}", $v);
            }
        }
        
        return $this->fetchAll($select);
        
        
    }
    
    public function _count($where = null) {
       $select = $this->select();
        
        if( isset($where) && is_array($where) ) {
            foreach($where as $k=>$v){
                $select->where("{$k}", $v);
            }
        }
       
       $select->from($this->_name,'COUNT(id) AS count');
       
       return $this->fetchRow($select)->count;
    }

    /*
     $sql = "INSERT INTO `db`.`table` (`name`) VALUES (?);";
            $crudIndie->_query($sql, array($indieName));

    */
    public function _query($sql, $values) {
        return $this->getAdapter()->query($sql, $values);
    }
    
    public function _lastid() {
        return $this->getAdapter()->lastInsertId();
    }
    
}