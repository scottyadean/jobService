<?php

/** 
* Columns:
`id`, `title`, `body`, `created`
*
*/
class Default_Model_Event extends Zend_Db_Table_Abstract 
{
    protected $_name = 'events';
    protected $_primary = 'id';    
    protected $_dependentTables = array( 'Default_Model_EventsQualifiers', 'Default_Model_EventRsvp');    
    protected $_current;
    
    public function getQualifiers() {
    return $this->_current->findManyToManyRowset(
            'Default_Model_Qualifiers','Default_Model_EventsQualifiers')->toArray();    
    }
    
    public function _create($data) {
        $data = $this->_cleanData($data);
        return $this->insert($data);
    }
    
    public function _read($id) {
        $select = $this->select()->from(array($this->_name=>$this->_name))->setIntegrityCheck(false)
                                 ->join(array('rsvp'=>'events_rsvp'), $this->_name.'.id = rsvp.event_id', array(' COUNT(rsvp.id) AS rsvp_count'))
                         ->where(  $this->_name.'.id = ?', (int)$id );
        
        return $this->fetchRow($select);
    }

    public function _find($id) {
        $this->_current = $this->find($id)->current();
        return $this->_current;
    }
    
    
    public function _top10() {
        
        
        $select = $this->select()->from(array($this->_name=>$this->_name))->setIntegrityCheck(false)
                               ->join(array('rsvp'=>'events_rsvp'), $this->_name.'.id = rsvp.event_id', array(' COUNT(rsvp.id) AS rsvp_count'))
                       ->where(  $this->_name.'.views > ?', (int)0 )->limit(10)->order('views ASC');
        
        return $this->fetchAll($select); 
    }
    
    public function findById($id) {
        return $this->_find($id);
    }
    
    public function _update($data) {
        $data = $this->_cleanData($data);
        $where = array('id = ?' => (int)$data['id']);
        return $this->update($data, $where);
    }
    
    public function _delete($id) {     
        return $this->delete(array('id = ?' => (int)$id));
    }
    
    public function findByDate($searchDate=false, $where = array()) {
        
        $select = $this->select();
 
    
 
        if($searchDate) {
            $select->where('created LIKE ?', "{$searchDate}%");
        }
 
        if(is_array($where)) {
            
            foreach($where as $k=>$w) {
                
                $select->where("{$k}", $w);
                
            }
            
        }
 
        
        return $this->fetchAll($select);
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
    
        if(isset($data['created'])) {
            $timestamp = strtotime($data['created']." ".date("H:i:s"));
            $data['created'] = date("Y-m-d H:i:s", $timestamp);
        }
        
        if(array_key_exists('created',$data) && empty($data['created'])){
            unset($data['created']);
        }
        
        $info = $this->info();
        return array_intersect_key($data, $info['metadata']); 
        
    }
 
}