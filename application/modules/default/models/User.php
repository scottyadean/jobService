<?php

class Default_Model_User extends Zend_Db_Table_Abstract {

    protected $_name = 'users';
    protected $_primary = 'id';
    protected $_current;
    protected $_dependentTables = array( 'Default_Model_EventRsvp');
    
    
    public function getEvents() {
        return $this->_current->findManyToManyRowset(
                'Default_Model_Event','Default_Model_EventRsvp')->toArray();    
    }
    
    
    public function init() {    
    }

     public function findById($id) {
        $this->_current = $this->find($id)->current();
        return $this->_current;
    }
    
    
    public function getInfo() {
        return $this->info();
    }
    
     public function all($asArray=false, $where=array()) {
      
      $select = $this->select();
      
      if(is_array($where)) {
        
        foreach($where as $wk=>$wv) {
            $select->where( $wk, $wv );
        }
        
      }
      
      
        if( $asArray ){
        
            return $this->fetchAll($select)->toArray();
        
        }else{
        
            return $this->fetchAll($select);
        
        }
        
     }

    public function create($params) {
        /*
            salt the password
        */
        $salt     = Main_Salt::getRandomSha1Hash();
        $pass     = Main_Salt::getSha1Hash($params['password'], $salt);

        /*
            get current sql date
        */
        $date = new Zend_Db_Expr('NOW()');

        /*
            insert data
        */
        $data = array('username'      => strip_tags(trim($params['username'])),
                      'email'         => $params['email'],                
                      'password'      => trim($pass),
                      'salt'          => $salt,
                      'last_log'      => $date,
                      'date_created'  => $date);
        

        foreach($params as $k=>$p){
            
            if( !array_key_exists((string)$k, $data) ) {
                
                $data[$k] = strip_tags( $p );
                
            }
            
            
        }
        
        return $this->insert($this->cleanData($data));

    }

    public function findByUsername($username) {
       $select = $this->select()->where("username = ?", $username);
       return $this->fetchRow($select);
    }

    public function findbyHash($hash) {
    
       $select = $this->select()->where("status = ?", $hash);
       $result = $this->fetchRow($select);

       return $result != null ? $result->toArray() : null;

    }

    public function updateUser($data, $setLastLog = true) {
        
        if($setLastLog == true){
            $data['last_log'] = new Zend_Db_Expr('NOW()');
        }
        
        $where = array('id = ?' => (int)$data['id']);
        return $this->update($data, $where);
    }

    
    public function _update($data) {
        
        return $this->update($data, array('id = ?' => (int)$data['id']));
    }

    public function countByField($field, $val) {
       $select = $this->select()->where("{$field} = ?", $val);
       $select->from($this->_name,'COUNT(id) AS num');
       return $this->fetchRow($select)->num;
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
    
    
    
        
   public function _in($field = 'id', $ids = array(), $where = false) {

        $select = $this->select()->where($field.' IN (?)',  $ids);
        
          if( $where && is_array($where) ) {
            
            foreach($where as $k=>$v) {
                $select->where($this->getTable.".{$k}", $v);
            }
        }
        
        return $this->fetchAll($select);
        
        
    }
    
    public function cleanData($data) {
      $info = $this->getInfo();
      return  array_intersect_key($data, $info['metadata']);       
    }
    
    
}

