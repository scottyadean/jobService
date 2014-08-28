<?php
/** 
* Table: providers_programs_locations
* Columns: `provider_id`, `program_id`, `location_id`
**/
class Default_Model_PagesModules  extends Zend_Db_Table_Abstract 
{
    protected $_name = 'pages_modules';
    protected $_primary = 'module_id';
    protected $_referenceMap = array(                            
                              'P_Page' => array(
                              'columns' => array('page_id'),
                              'refTableClass'=>'Default_Model_Page',
                              'refColumns' => array('id')
                             ),
                              'P_Mod' => array(
                              'columns'=>array('module_id'),
                              'refTableClass'=>'Default_Model_Module',
                              'refColumns' => array('id')
                             ));
    
    public function assign($page_id, $module_id, $align = 'R') {
        $data = array('page_id'=>$page_id, 'module_id'=>$module_id, 'align'=>$align);
        return $this->insert($data);
    }
    
    public function remove($page_id, $module_id){
        $count = $this->count( $program_id,  $module_id);    
        if( $count > 0 ){
            return $this->delete(array('page_id = ?'=>$page_id, 'module_id = ?'=>$module_id));
        }
        return false;
    }
    
    public static function getNav($id) {
        
        $db =  Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                     ->where("id = ?", $id)
                     ->from('modules');
        $res = $db->fetchRow($select);
        $body = "";
        
        if(!empty($res) && isset($res['body']) ) {
            $body = $res['body'];
            
        }
                     
        
        return $body; 
        
    } 
    
    
    public function removeById($module_id) {
        return $this->delete(array('module_id = ?' => $module_id));
    }
    
    public function removeByPageId($page_id) {
        return $this->delete(array('page_id = ?' => (int)$page_id));
    }
    
    public function findById($module_id) {
        $select = $this->select();
        $select->where( 'module_id = ?', $module_id );
        return $this->fetchAll($select);
    }
    
    public function findByPageId($page_id) {
        $select = $this->select();
        $select->where( 'page_id = ?', $page_id  );
        return $this->fetchAll($select);
    }
    
    public function count($page_id, $module_id) {
        $select = $this->select();
        $select->from($this->_name, array("num"=>"COUNT(page_id)", "count_id"=>"page_id"));
        $select->where("page_id = ?", $page_id)->where( 'module_id = ?', $module_id );
        $result = $this->fetchRow($select);
        return (int)$result["num"];
    }
}