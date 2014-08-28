<?php
/*
Table: providers_programs_locations
Columns: `event_id`, `qualify_id`
*/
class Default_Model_EventsQualifiers  extends Zend_Db_Table_Abstract  {
    
    protected $_name = 'events_qualifiers';
    protected $_primary = 'event_id';
    protected $_referenceMap = array(
                              'E_Event' => array(
                              'columns' => array('event_id'),
                              'refTableClass'=>'Default_Model_Event',
                              'refColumns' => array('id')
                             ),
                              'Q_Qualify' => array(
                              'columns'=>array('qualify_id'),
                              'refTableClass'=>'Default_Model_Qualifiers',
                              'refColumns' => array('id')
                             ));
    
    public function assign($event_id, $qualify_id) {
        $data = array('event_id'=>$event_id, 'qualify_id'=>$qualify_id);
        return $this->insert($data);
        
    }
    
    public function remove($event_id, $qualify_id) {
         $count = $this->count( $event_id,  $qualify_id);
        if( $count > 0 ){
            return $this->delete(array('event_id = ?' =>
                                       $event_id, 'qualify_id = ?' => $qualify_id));
        }
        return false;
    }
   
   public function removeById($event_id) {
        return $this->delete(array('event_id = ?' => $event_id));
   }
   
   public function removeByqualifyId($qualify_id) {
        return $this->delete(array('qualify_id = ?' => (int)$qualify_id));
   }
   

    public function findById($event_id) {
        $select = $this->select();
        $select->where( 'event_id = ?', $event_id );
        return $this->fetchAll($select);
    }
   
   public function count($event_id,  $qualify_id) {
   
        $select = $this->select();
        $select->from($this->_name, array("num"=>"COUNT(event_id)", "count_id"=>"event_id"));
        $select->where("event_id = ?", $event_id)->where( 'qualify_id = ?', $qualify_id );
        $result = $this->fetchRow($select);
         return (int)$result["num"];
   }
                              
      
}