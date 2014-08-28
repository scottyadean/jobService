<?php
/*

Table: providers_programs_locations
Columns:
`provider_id`, `program_id`, `location_id`

*/
class Default_Model_ProgramLocations   extends Zend_Db_Table_Abstract 
{
    public $groupBy;
   
   
    protected $_name = 'providers_programs_locations';
    protected $_primary = 'program_id';
    
    protected $_referenceMap = array(
                              
                              'P_Location' => array(
                              'columns' => array('location_id'),
                              'refTableClass'=>'Default_Model_Locations',
                              'refColumns' => array('id')
                             ),
        
                              'P_Program' => array(
                              'columns'=>array('program_id'),
                              'refTableClass'=>'Default_Model_Program',
                              'refColumns' => array('id')
                             ));
    
    public function assign($provider_id, $program_id, $location_id) {
        $data = array('provider_id'=>$provider_id, 'program_id'=>$program_id, 'location_id'=>$location_id);
        return $this->insert($data);
        
    }
    
    
    
    public function _index($where = array(), $fields=array()) {
        
         $select = $this->select();
        
        if(count($fields)) {
            $select->from(array($this->_name),$fields);
        }
        
        if(!empty($where) && is_array($where) ) {
            foreach($where as $k=>$v){
                $select->where("{$k}", $v);
            }
        }
        
        if(isset($this->groupBy) && !empty($this->groupBy)) {
            $select->group($this->groupBy);
        }
        
        
        return $this->fetchAll($select);
    }
       
    
    
    public function remove($program_id, $location_id) {
            
         $count = $this->count( $program_id,  $location_id);
        
        if( $count > 0 ){
            return $this->delete(array('program_id = ?' =>
                                       $program_id, 'location_id = ?' => $location_id));
        }
        
        return false;
    }
   
   public function removeById($program_id) {
        return $this->delete(array('program_id = ?' => $program_id));
   }
   
   public function removeByProviderId($provider_id) {

        return $this->delete(array('provider_id = ?' => (int)$provider_id));
   }
   

    public function findById($program_id) {
        
        $select = $this->select();
        $select->where( 'program_id = ?', $program_id );
        return $this->fetchAll($select);
    }
   
    public function findByProviderId($provider_id ) {
        
        $select = $this->select();
        $select->where( 'provider_id = ?', $provider_id  );
        return $this->fetchAll($select);
    }
   
    
   public function count($program_id,  $location_id) {
   
        $select = $this->select();
        $select->from($this->_name, array("num"=>"COUNT(program_id)", "count_id"=>"program_id"));
        $select->where("program_id = ?", $program_id)->where( 'location_id = ?', $location_id );
        $result = $this->fetchRow($select);
         return (int)$result["num"];
   }
                              
      
}