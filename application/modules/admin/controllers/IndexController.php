<?php
class Admin_IndexController extends Zend_Controller_Action {


    public $model;
    
    public function init() {
        Zend_Layout::getMvcInstance()->assign('sideNav', 'N');
    
    }


    public function indexAction() {
        
        $usr = new Default_Model_User;
        $this->view->admin_users =  $usr->_count(array('role > ?' => 1));
        $this->view->users = $usr->_count(array('role < ?' => 2));
        
        $pages = new Default_Model_Page;
        $this->view->pages = $pages->_count(array('id > ?'=>0));
        $this->view->pagesTop10 = $pages->_top10();
        
        $provider = new Default_Model_Provider;
        $this->view->providers = $provider->_count();

        $programs = new Default_Model_Program;
        $this->view->programs = $programs->_count();
        $events = new Default_Model_Event;
        
        $this->view->eventsTop10 = $events->_top10();
        $this->view->events = $events->_count();
        
    }
    
    
    public function mostViewedAction() {
        
        
        $allowed = array("events", 'providers', 'programs');
        $success = false;
        
        if($this->xhr && !empty($this->id)) {
            $m = $this->getRequest()->getParam('m', null);
            if(in_array((string)$m, $allowed)){
                
                switch($m) {
                    case "events":
                        $model = new Default_Model_Event;
                    break;
                    
                    case "providers":
                        $model = new Default_Model_Provider;
                    break;
                    
                    case "'programs":
                        $model = new Default_Model_Provider;
                    break;
                    
                }
                
                
                $sql = "UPDATE {$m} SET views = views + 1 WHERE id = ?";
                $model = new Default_Model_Crud;
                $model->setDbName($m);
                $model->_query($sql, array($this->id));
                $success = true;
            }
        }

        $this->_asJson(array( 'success'=>$success,
                              'model'=>$m,
                              'id'=>$this->id,));
        
        
    }
    
   public function __buildprovidorAction() {
    
    $data  = Base_Functions_Seed::getProviderData();
    $industries = new Default_Model_Industry; 
    $allIndustries = $industries->_index()->toArray();
    $providers = new Default_Model_Provider; 
    
    foreach($data as $k=>$d) {
        
        $found = false;
       
        foreach( $allIndustries as $key=>$ind ) {
            
            if(  strtolower( $d[0] )  == strtolower($ind['name']) ){
                 
                $found = $ind['id'];
                
                if( $ind['parent_id'] == 0 ){
                    break;
                }
            }
        } 
        
        
        if( !$found ){
            print  $k." : ".$d[0] . "<br />";
        }else{
            $val   = trim( $k );
            $where = array(  "name LIKE ?" => "$val%"   );
            $query = $providers->_fuzzyupdate(array('industry_id' => $found, 'tags'=> implode(",", $d)), $where );
            
            print $k . " ~ " . $query;
            
            if($query == 0)
             $res[] = array($k, $query);
            
        }
        
        
        
    }
    
    
    print "<hr /><pre>";
        var_export($res);
        print "</pre>";
    
    
   }
   
    
   public function _buildcatsAction() {
    
    $data = Base_Functions_Seed::getIndustry();
    
    $industries = new Default_Model_Industry; 
    $allIndustries = $industries->_index()->toArray();
    $programs = new Default_Model_Program; 
     $res = array();
    foreach($data as $k=>$d) {
        
        $found = false;
       
        foreach( $allIndustries as $key=>$ind ) {
            
            if(  strtolower( $d['industry'] )  == strtolower($ind['name']) ){
                 
                $found = $ind['id'];
                break;
        
            }
        } 
        
        
        if( !$found ){
            print  $k." : ".$d['industry'] . "<br />";
        }else{
            $val   = trim( $d['program_name'] );
            $where = array(  "name LIKE ?" => "$val%"   );
            $query = $programs->_fuzzyupdate(array('industry_id' => $found), $where );
            
            print $d['program_name'] . " ~ " . $query;
             $res[] = array($d['program_name'], $query);
            
        }
        
        
        
    }
    
    
    print "<hr /><pre>";
        var_export($res);
        print "</pre>";
    
    
   }
    
   
  
   public function _seedAction() {
    
        $data = Base_Functions_Seed::getSeedData();
        

        $curd = new Default_Model_Crud;
        $curd->setTable('providers');
        
        $industry = new  Default_Model_Industry;
        $industries = $industry->_index()->toArray();
        
        $programs = new Default_Model_Program;
        
        $locations = new Default_Model_Locations;
        
        $programs_locs = new Default_Model_ProgramLocations;
        
        
        foreach($data as $k=>$d) {
           $industry_id = 0;
           
           
           if( count($d['addresses']) == 0){
            
             //University of Fairfax, MedCerts, CVS Pharmacy 
                print "$k has no address ----- continue<br />";
                continue;
            
            
           }
           
           
           if( $d['industry'] == "" ) {
            
                $d['industry'] = "other";
            
           }
           
           foreach($industries as $ind) {
                if($ind['name'] == trim($d['industry'])) {
                    $industry_id = $ind['id'];
                }
           }
           
           if($industry_id == 0 ) {
                $industry_id =  $industry->_create(array('name'=>trim($d['industry'])));
           }
           
           
           $tags = array(trim($d['industry']));
           foreach($d[ 'programs'] as $pro) {
                if(trim($pro['industry']) != "")
                    $tags[] = $pro['industry'];
            } 
            
            
           $insert_id =  $curd->_create(array("name"=>trim($k),
                                                "info"=>"",
                                                "industry_id"=>(int)$industry_id,
                                                "visible" => 'Y',
                                                "site"=> trim($d['site']),
                                                "tags" =>trim(implode(",", $tags))));
           
           
            print $insert_id ." -------------- New Insert <br />";
           
            $loc_ids = array();
            foreach($d['addresses'] as $l) {
                
                
                $loc_ids[] =   $locations->_create(array("provider_id"=> $insert_id,
                                    "address"=> $l['address'],
                                    "address2"=> $l['address2'],
                                    "city"=> $l['city'],
                                    "state"=> $l['state'],
                                    "zip"=> $l['zip'],
                                    "site"=> trim($l['website']),
                                    "email_public"=> $l['email'],
                                    "phone_public"=>$l['phone']));
                
            } 
 
 
             print implode(", ", $loc_ids) ." -------------- loc_ids <br />";
            
            $pro_ids = array();
            foreach($d['programs'] as $_p) {
                
               
                
                $pro_industry_id = $industry_id;  
                
                if(trim($_p['industry']) != '') {
                $pro_industry_id = 0;
                foreach($industries as $ind) {
                    if($ind['name'] == trim($_p['industry'])) {
                        $pro_industry_id = $ind['id'];
                    }
                }
                
                if( $pro_industry_id  == 0 ) {
                    $pro_industry_id =  $industry->_create(array('name'=>trim($_p['industry'])));
                }  
                
                }
                
                
                
                
              $pro_ids[] =  $programs->_create(array("provider_id"=>$insert_id,
                                                    "industry_id"=>$pro_industry_id,
                                                    "name"=>$_p['name'],
                                                    "tag"=>trim($_p['industry']),
                                                    "address_id"=> $loc_ids[0],
                                                    "info"=>""));
                
            }
            
             print implode(", ", $pro_ids) ." -------------- pro_ids <br />";
            
            
            foreach($pro_ids as $program_id ) {
               $programs_locs->assign($insert_id, $program_id, $loc_ids[0]);
               
                
            }
            print "DONE ------ <hr />";
            
           }
           
    
            
        }
    
    
       
    

    
   
    
    
        
   
}

 

