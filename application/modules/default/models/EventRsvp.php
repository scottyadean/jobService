<?php

/** 
* Columns:
`id`, `event_id`, `user_id`, `status`, `uid`, `created`
*
*/
class Default_Model_EventRsvp extends Zend_Db_Table_Abstract 
{
    protected $_name = 'events_rsvp';
    protected $_primary = 'id';
    protected $_referenceMap = array(
                         'U_User' => array(
                         'columns' => array('user_id'),
                         'refTableClass'=>'Default_Model_User',
                         'refColumns' => array('id'),
                        ),
                         'E_Event' => array(
                         'columns'=>array('event_id'),
                         'refTableClass'=>'Default_Model_Event',
                         'refColumns' => array('id')
                        ));
 
   
    public function _create($data) {
        $data = $this->_cleanData($data);
        return $this->insert($data);
    }
    
    
    public function onWaitList($event_id, $user_id) {
        $model = new Default_Model_Crud;
        $model->setTable('events_waitlist');
       return $model->_index(array('event_id = ?'=> $event_id, 'user_id = ?' => $user_id ));
    }
    
    
    public function addToWaitList($event_id, $user_id) {
        $model = new Default_Model_Crud;
        $model->setTable('events_waitlist');
        $model->_deleteWhere(array('event_id = ?'=> $event_id, 'user_id = ?' => $user_id ));
       return $model->_create(array('event_id'=> $event_id, 'user_id' => $user_id));
    }
    
    public function removeFromWaitList($event_id, $user_id) {
        $model = new Default_Model_Crud;
        $model->setTable('events_waitlist');
       return  $model->_deleteWhere(array('event_id = ?'=> $event_id, 'user_id = ?' => $user_id ));
    }
    
    
    
    public function _read($id) {
        $select = $this->select()->where( 'id = ?', (int)$id );
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

    public function _deleteByEventIdAndUserId($event_id, $user_id) {
            
           $where = array('event_id = ?' => (int)$event_id,
                          'user_id = ?' => (int)$user_id);       
        return $this->delete( $where);
    }
    
    public function findByEventId($event_id) {
        $select = $this->select();
            $select->where('event_id = ?', (int)$event_id);
        return $this->fetchAll($select);
    }

    public function findByUserId($user_id) {
        $select = $this->select();
        $select->where('user_id = ?', (int)$user_id);
        return $this->fetchAll($select);
    }


    public function findByEventAndUserId($event_id, $user_id) {
        $select = $this->select();
        $select->where('event_id = ?', (int)$event_id)->where('user_id = ?', (int)$user_id)
            ->limit(1);
        
        return $this->fetchRow($select);
    }


    public function findByDate($searchDate=false) {
        $select = $this->select();
        if($searchDate) {
            $select->where('created LIKE ?', "{$searchDate}%");
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
        
        if(isset($data['uid'])){
            $data['uid'] = Base_Functions_Strings::Guid();    
        }
        
        $info = $this->info();
        return array_intersect_key($data, $info['metadata']); 
        
    }
 
}