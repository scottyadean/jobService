<?php
class Admin_SeedController extends Zend_Controller_Action {


    public $model;
    public $states;
    
    public function init() {
        $this->states = Main_Forms_Data::AmericaStates();
    }

    public function inState($state){
        $found = "VA";
        foreach($this->states as $k=>$s) {
            
            
            if($k == strtoupper($state) || strstr(strtoupper($s), strtoupper($state))) {
                $found = $k;
                break;
            }
            
        }
        
       
        return $found;
        
    }    
   
   public function checkIndie($search, $in) {
    
        $found = false;
        foreach($in as $k=>$v) {
            
            if( strtolower($v['name']) == trim(strtolower($search)) ) {
                //print strtolower($v['name'])."  == ".trim(strtolower($search))."<br>";
                $found = $v['id'];
                break;
                
            }
            
        }
        
        return $found;
       
    
    
   }

   
   
   public function check($value, $what) {
    
        
        
        switch($what) {
            
            case"email":
            $value = trim($value);
            $value = str_replace("https","",$value);
            $value = str_replace("http","",$value);
            $value = str_replace("://","",$value);
            $value = str_replace("www.","",$value);
            $value = str_replace("www","",$value);
            
             if($value != "" && strstr($value, '@')) {
                
                   if(strstr($value, "*")) {
                    
                        $bits = explode("*", $value);
                        
                        foreach($bits as $b=>$e) {
                        
                            if(filter_var(trim($e), FILTER_VALIDATE_EMAIL)){
                                $value = trim($e);
                                break;
                            }
                                
                            
                         }
                        
                    
                    }
                    
                if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $value = "";
                  }

           }
                  
            
            
            
          
            break;
        
        
            case"site":
                $value = trim($value);
                
                if(trim($value) == "www."
                   || trim($value) == "http"
                   || trim($value) == "http:"
                   || trim($value) == "http://"){
                  $value = "";
                }
                
                if($value != "") {
                    
                    if(strstr($value, '@')) {
                    
                        $value = "";
                        
                    }    
                    
                }  
            
                if($value != "") {
                    
                   if(strstr($value, "*") || strstr($value, "**")) {
                    
                        $bits = explode("*", $value);
                        
                        $value = $bits[0];
                    
                    } 
                }
                
                 if($value != "" && Base_Functions_Strings::StartsWith($value, 'www.')) {
                    
                    $value = "http://".$value;
                    
                 }
                
            
            break;
        
          
            case"phone":
                $value = trim($value);    
                if(trim($value) != "") {
                    $value = ereg_replace("[^0-9 *Ex]", "", $value);
                }    
            
            
                if($value != "") {
                    
                   if(strstr($value, "*") || strstr($value, "**")) {
                    
                        $bits = explode("*", $value);
                        
                        $value = trim($bits[0]);
                    
                    }
                    
                }
            
            break;
            //1 '1st CDL Training Center of NOVA,
            //2 ,
            //3 ,
            //4 www.cdlnow.com,
            //5 703 347 7999,
            //6 5716 Telegraph Road,
            //7 Suite B,
            //8 Alexandria,
            //9 Virginia,
            //10 22303'                        
            /*
            $location = array($pid, $this->check($d, 'address'),
            $this->check($d, 'address2'),
            $this->check($d, 'city'),
            $this->check($d, 'state'),
            $this->check($d, 'zip'), $this->check(trim($d[3]), 'email'), $this->check(trim($d[4]), 'phone'));
            
            'AVI Career Training,,,http://avicareertraining.com/,703 759 2200,10130-B Colvin Run Road, Great Falls,Virginia,22066'
            */           
             
            case"address":
                
                $value = trim($value[5]);
                
            break;
        
            case"address2":
                if(count($value) > 9){
                    $value = trim($value[6]);
                }else{
                    
                    $value = '';
                }
            break;
        
        
            case"city":
                
                if(count($value) > 9){
                    $value = trim($value[7]);
                }else{
                    $value = trim($value[6]);
                }
                
            break;
        
        
            case"state":
                
                if(count($value) > 9){
                    $value = $this->inState(trim($value[8]));
                }else{
                    $value = $this->inState(trim($value[7]));
                }
                
            break;

        case"zip":
            
                            
                if(count($value) > 9){
                    $value = trim($value[9]);
                }else{
                    $value = trim($value[8]);
                }
                
            
            break;


            
        }
        
        return is_array($value) ? '' : $value;
    
    
   }
   
   
   public function indieId($crudIndie, $indieNames, $industries, $index=0) {
       
       if(count($indieNames) == 0) {
            return 1;
       }
       
       if(!isset($indieNames[$index])) {
            return 1;
       }
       
       $indieName = trim($indieNames[$index]);
       
       if($indieName == '') {
            $index++;
            return $this->indieId($crudIndie, $indieNames, $industries, $index);
       }
       
       
       $id =  $this->checkIndie($indieName,$industries);
       
       if( $id == false) {
        
            $in = "INSERT INTO `skillSrc`.`industries` (`name`) VALUES (?);";
            $crudIndie->_query($in, array($indieName));
            
            $id =  $crudIndie->_lastid();
        
       }
       
       return $id;
    
    
    
   }
   
   public function seedAction() {
        $providers  = $this->providers();
        
        $crudIndie = new Default_Model_Crud;
        $crudIndie->setDbName('industries');
        
        $providers_locations = new Default_Model_Crud;
        $providers_locations->setDbName('providers_locations');
        $lsql = "INSERT INTO `skillSrc`.`providers_locations` ( `provider_id`, `address`, `address2`, `city`, `state`, `zip`, `email_public`, `phone_public`)
        VALUES (?,?,?,?,?,?,?,?);";
        
        
        $providers_programs = new Default_Model_Crud;
        $providers_programs->setDbName('providers_programs');
        $psql = "INSERT INTO `skillSrc`.`providers_programs` (`provider_id`, `industry_id`, `name`, `tag`, `address_id`) VALUES (?,?,?,?,?);";
        
        
        $crud = new Default_Model_Crud;
        $crud->setDbName('providers');
        $sql = "INSERT INTO `skillSrc`.`providers` (`name`, `site`, `info`, `detail`, `industry_id`, `visible`, `tags`) VALUES (?, ?, ?, ?, ?, ?, ?);";
        
        
        foreach( $providers  as $key=>$data ) {
            //'Training Provider,Programs,Industry,Website,Phone Number,Physical Address,City,State,Zipcode';
            //'America School of Nursing & Allied Health,,,http://www.asnahlearning.com/,703 490 8402,14910 Jefferson Davis Highway,Woodbridge,Virginia,22193'
            //,CDL (Commercial Driver\'s License) Class A,Commercial Driving,,,,,,',
            $d = explode(",", $data['data']);
            $p = $this->tags($data['programs']);
            
            $insert = array(trim($d[0]),
                          $this->check(trim($d[3]), 'site'),
                          '',
                          '',
                          $this->indieId($crudIndie, $p['tags'], $crudIndie->_index()->toArray()),
                          'N',
                          count($p['tags']) > 0 ? implode(",",$p['tags']) : '');
            
            
             $crud->_query($sql, $insert);
             $pid = $crud->_lastid();
             
             
             //'1st CDL Training Center of NOVA,,,www.cdlnow.com,703 347 7999,5716 Telegraph Road, Suite B,Alexandria,Virginia,22303'
             //`provider_id`, `address`, `address2` `city`, `state`, `zip`, `email_public`, `phone_public`
             $location = array($pid, $this->check($d, 'address'),
                                     $this->check($d, 'address2'),
                                     $this->check($d, 'city'),
                                     $this->check($d, 'state'),
                                     $this->check($d, 'zip'),
                                     $this->check(trim($d[3]), 'email'),
                                     $this->check(trim($d[4]), 'phone'));
            
             
             $providers_locations->_query($lsql, $location);
             $lid = $providers_locations->_lastid();
             
             //"INSERT INTO `skillSrc`.`providers_programs` (`provider_id`, `industry_id`, `name`, `tag`, `address_id`) VALUES (?,?,?,?,?);"
             foreach($p['clean'] as $pc=>$pv){
                $programs = array($pid,
                                  $this->indieId($crudIndie, $pv['tag'], $crudIndie->_index()->toArray()),
                                  $pv['name'],
                                  count($p['tags']) > 0 ? implode(",",$p['tags']) : '',
                                  $lid);
             
                print $key." : $pc :".implode(",",  $pv['tag']);
                print "<hr />";
                
                $providers_programs->_query($psql, $programs);
             
            }
        }
        
        
       
       
        print "DONE";
        
        exit;
    
        
    
   }
   
   
   public function tags($pros) {
    
    $tags = array();
    $clean = array();
    foreach($pros as $k=>$pro) {
    
        $pro = ltrim ($pro,',');
        $pro = rtrim ($pro,',,,,,,');
        $pro =  explode(",", $pro);
        
        $tag = '';
        
        if(isset($pro[1]) && trim($pro[1]) != ''){
             $tag = eregi_replace('[^0-9A-Za-z ]', '', trim($pro[1]));
             
             if(!in_array($pro[1],$tags)){
                $tags[] = $tag;
             }
        }

        $clean[] = array('name'=> isset($pro[0]) ? trim($pro[0]) : '', 'tag'=>array($tag));
        
    }
    
    
    return array("tags"=> $tags, 'clean'=>$clean );
    
   }
   
   
   
   public function providers() {
    
 
return array (
  0 => 
  array (
    'data' => '1st CDL Training Center of NOVA,,,www.cdlnow.com,703 347 7999,5716 Telegraph Road, Suite B,Alexandria,Virginia,22303',
    'programs' => 
    array (
      0 => ',CDL (Commercial Driver\'s License) Class A,Commercial Driving,,,,,,',
      1 => ',CDL (Commercial Driver\'s License) Class B,Commercial Driving,,,,,,',
    ),
  ),
  3 => 
  array (
    'data' => 'A-1 CDL School,,,www.a1cdlschool.com,703 335 5333,11301 Coles Drive,Manassas,Virginia,20112',
    'programs' => 
    array (
      0 => ',CDL (Commercial Driver\'s License) Certification (Class A & B),Commercial Driving,,,,,,',
    ),
  ),
  5 => 
  array (
    'data' => 'AAA CDL School,,,www.aaacdlschool.com,571 266 0077,7223 Centreville Road,Manassas,Virginia,20111',
    'programs' => 
    array (
      0 => ',CDL (Commercial Driver\'s License) Certification (Class A & B),Commercial Driving,,,,,,',
    ),
  ),
  8 => 
  array (
    'data' => 'Ace Tech Institute,,,http://temp.contractva.com/,703 298 5789,7777 Leesburg Pike, Suite 205,Falls Church,Virginia,22043',
    'programs' => 
    array (
      0 => ',Licensed Electrician,Trade,,,,,,',
      1 => ',Licensed HVAC,Trade,,,,,,',
      2 => ',Licensed Plumber,Trade,,,,,,',
      3 => ',Building Technical,Trade,,,,,,',
    ),
  ),
  13 => 
  array (
    'data' => 'Action in Community Through Service (ACTS),,,http://actspwc.org,703 441 8606,3900 ACTS Lane,Dumfries,Virginia,22026',
    'programs' => 
    array (
      0 => ',Nurse Aide Program,Healthcare,,,,,,',
    ),
  ),
  15 => 
  array (
    'data' => 'Aerostar Training Services,,,http://www.aerostartyperatings.com/,407 888 9011,4215 Lindy Circle,Orlando,Florida,32827',
    'programs' => 
    array (
      0 => ',Airbus 320 Recurrent,Aviation,,,,,,',
    ),
  ),
  17 => 
  array (
    'data' => 'America School of Nursing & Allied Health,,,http://www.asnahlearning.com/,703 490 8402,14910 Jefferson Davis Highway,Woodbridge,Virginia,22193',
    'programs' => 
    array (
      0 => ',Certified Nursing Assistant/Nurses Aide,Healthcare,,,,,,',
      1 => ',Certified Phlebotomy Technician,Healthcare,,,,,,',
      2 => ',Medication Aide/Medical Technician Training,Healthcare,,,,,,',
      3 => ',Pharmacy Technician,Healthcare,,,,,,',
    ),
  ),
  22 => 
  array (
    'data' => 'American Beauty College,,,http://,703 536 8700,1057 West Broad Street, Suite 217,Falls Church,Virginia,22046',
    'programs' => 
    array (
      0 => ',Barbering,Cosmetology, Beauty,,,,,,',
      1 => ',Cosmetology School, Cosmetology, Beauty,,,,,,',
    ),
  ),
  25 => 
  array (
    'data' => 'American College of Commerce & Technology,,,http://www.acct2day.org/,,150 South Washington Street #101 ,Falls Church,Virginia,22046',
    'programs' => 
    array (
      0 => ',Business Administration (Associate & BS Degrees),Professional,,,,,,',
      1 => ',Computer Technician (CIP Code 47.0104),Information Technology,,,,,,',
      2 => ',Dental Assistant,Healthcare,,,,,,',
      3 => ',Electronic Health Records (CIP Code 51.0720),Healthcare,,,,,,',
      4 => ',Health Care Management (CIP Code 51.0701),Healthcare,,,,,,',
      5 => ',Information Security Certificate Program,Information Technology,,,,,,',
      6 => ',Information Technology Certificate,Information Technology,,,,,,',
      7 => ',Medical Administrative Assistant (CIP Code 57.0710),Healthcare, Administration,,,,,,',
      8 => ',Paralegal Studies,Professional,,,,,,',
      9 => ',Phlebotomy Technician (CIP Code 51.1009),Healthcare,,,,,,',
      10 => ',Tax Preparation Certificate (CIP Code 13.2082),Professional,,,,,,',
      11 => ',Web Development & Maintenance Business (CIP Code 11.1004),Information Technology,,,,,,',
      12 => ',Accounting Certificate,Professional,,,,,,',
    ),
  ),
  39 => 
  array (
    'data' => 'American College of Technology,,,http://acot.edu/,816 279 7000,2700 N Belt Highway Suite 201,St. Joseph,Missouri,64506',
    'programs' => 
    array (
      0 => ',Business Administration and IT - AAS Degree,Information Technology,,,,,,',
      1 => ',Computer Programming and Systems Design - Degree,Information Technology,,,,,,',
      2 => ',Computer Information Systems and Multimedia Technology - AAS Degree,Information Technology,,,,,,',
      3 => ',Computer Information Systems and Multimedia Technology - Diploma,Information Technology,,,,,,',
      4 => ',Computer Programming and Systems Design - AAS Degree,Information Technology,,,,,,',
      5 => ',Computer Programming and Systems Design - Diploma,Information Technology,,,,,,',
      6 => ',Criminal Justice - AAS,Professional,,,,,,',
      7 => ',Criminal Justice - Technical Diploma,Professional ,,,,,,',
      8 => ',Criminal Justice Paralegal Certificate,Professional,,,,,,',
      9 => ',Desktop Publishing Specialist - Certificate,Information Technology,,,,,,',
      10 => ',Health Information Technology - AAS,Information Technology, ,,,,,,',
      11 => ',Health Information Technology - Technical Diploma,Information Technology, ,,,,,,',
      12 => ',Information Technology - Certificate,Information Technology,,,,,,',
      13 => ',Network Administration and Information Security - AAS,Information Technology,,,,,,',
      14 => ',Business Administration and Information Technology - Diploma,Information Technology,,,,,,',
    ),
  ),
  55 => 
  array (
    'data' => 'American National University,,,http://www.an.edu/,703 962 9657,1980 Gallows Road, Suite 220 ,Vienna,Virginia,22182',
    'programs' => 
    array (
      0 => ',Microsoft Office Skills,Information Technology,,,,,,',
    ),
  ),
  57 => 
  array (
    'data' => 'American Society for Training and Development,,,http://www.astd.org/,703 683 8123,1640 King Street Box 1443 ,Alexandria,Virginia,22313-1443',
    'programs' => 
    array (
      0 => ',CPLP (Certified Professional in Learning & Performance) Bundle: i.CPLP Prep On-line workshop ii. pCPLP Practice Exam iii.Professional Development Resources iv.CPLP Certification Testing,Professional,,,,,,',
    ),
  ),
  63 => 
  array (
    'data' => 'Annie Beauty & Tech School,,,http://www.anniebt.com/,,4220 Annandale Road,Annandale,Virginia,22003',
    'programs' => 
    array (
      0 => ',Cosmetology,Cosmetology, Beauty,,,,,,',
      1 => ',Nail Technician,Cosmetology, Beauty,,,,,,',
      2 => ',Waxing Technician,Cosmetology, Beauty,,,,,,',
    ),
  ),
  67 => 
  array (
    'data' => 'Astar Education Institute,,,http://astarinstitute.org/,703 368 6838,7864 Donegan Drive ,Manassas,Virginia,20109',
    'programs' => 
    array (
      0 => ',Citizenship Class,Literacy,,,,,,',
      1 => ',Computer Skills Class,Information Technology,,,,,,',
      2 => ',ESL (English as a Second Language),Literacy,,,,,,',
      3 => ',Nursing Assistant,Healthcare,,,,,,',
      4 => ',TESOL (Teaching English to Speakers of Other Languages),Literacy,,,,,,',
    ),
  ),
  73 => 
  array (
    'data' => 'AVI Career Training,,,http://avicareertraining.com/,703 759 2200,10130-B Colvin Run Road,Great Falls,Virginia,22066',
    'programs' => 
    array (
      0 => ',Esthetics - 1200 hours,Cosmetology, Beauty,,,,,,',
      1 => ',Esthetics - 600 hours,Cosmetology, Beauty,,,,,,',
      2 => ',Make-Up Artistry,Cosmetology, Beauty,,,,,,',
      3 => ',Permanent Make-Up,Cosmetology, Beauty,,,,,,',
      4 => ',Cosmetology 1500 hours,Cosmetology, Beauty,,,,,,',
    ),
  ),
  79 => 
  array (
    'data' => 'Brooke Transportation Training Solutions,,,http://www.brooketraining.com/,817 999 5398,7001 Boulevard 26 Grapevine Highway 26, Suite 375,North Richland Hills,Texas,76180',
    'programs' => 
    array (
      0 => ',Freight Broker Training/Basic,Commercial Driving,,,,,,',
    ),
  ),
  81 => 
  array (
    'data' => 'Business Interface Inc.,,,http://www.businessinterfaceinc.com/,410 685 3935 ** 202 955 3005,1150 Connecticut Avenue, NW Suite 525,Washington,DC,20036',
    'programs' => 
    array (
      0 => ',Customer Service Associate,Miscellaneous,,,,,,',
    ),
  ),
  83 => 
  array (
    'data' => 'First Nursing Academy,,,www.,703 772 8900,8811 Sudley Road Suite 110 ,Manassas,Virginia,20110',
    'programs' => 
    array (
      0 => ',Certified Nursing Assistant,Healthcare,,,,,,',
      1 => ',Home Health Aide/Direct Care (HHA),Healthcare,,,,,,',
      2 => ',Medication Aide,Healthcare,,,,,,',
    ),
  ),
  87 => 
  array (
    'data' => 'Care Perfections Career Institute,,,http://www.careperfections.com/careperfectionsci/,703 659 9640,9105-C Owens Drive Suite 202 ,Manassas Park,Virginia,20111',
    'programs' => 
    array (
      0 => ',Nurse Aide,Healthcare,,,,,,',
    ),
  ),
  89 => 
  array (
    'data' => 'Career Step,,,www.careerstep.com,801 769 8447*800 246 7837,4692 North 300 W Suite 150,Springville,Utah,84604',
    'programs' => 
    array (
      0 => ',Medical Coding & Billing,Healthcare,,,,,,',
      1 => ',Medical Transcription,Healthcare,,,,,,',
    ),
  ),
  92 => 
  array (
    'data' => 'CDS Tractor Trailer Training,,,www.cdscdltraining.com,540 582 4022,6200 Jefferson Davis Highway ,Woodford,Virginia,22580',
    'programs' => 
    array (
      0 => ',CDL Class A or Tractor Trailer Training,Commercial Driving,,,,,,',
      1 => ',CDL Class B (3 weeks),Commercial Driving,,,,,,',
      2 => ',CDL Class B with Passenger Endorsement or Bus Training (4 weeks),Commercial Driving,,,,,,',
    ),
  ),
  96 => 
  array (
    'data' => 'Centura College,,,www.centura.edu,703 778 4444,8870 Rixlew Lane, Suite 201 ,Manassas,Virginia,20109',
    'programs' => 
    array (
      0 => ',Massage Therapy,Wellness,,,,,,',
      1 => ',Medical Assisting,Healthcare,,,,,,',
    ),
  ),
  99 => 
  array (
    'data' => 'ClickCE LLC,,,www.clickce.com,512 275 6603*877 254 2523,10685-B Hazelhurst Drive #8897,Houston,Texas,77043',
    'programs' => 
    array (
      0 => ',Virginia Real Estate Principles & Practices,Professional,,,,,,',
    ),
  ),
  101 => 
  array (
    'data' => 'Columbia Lighthouse for the Blind,,,www.clb.org,301 589 0896,6200 Baltimore Street, Suite 100,Riverdale,Maryland,20737',
    'programs' => 
    array (
      0 => ',Digital Data Scanning Class,Administration,,,,,,',
      1 => ',Mail Center Training Program,Administration,,,,,,',
    ),
  ),
  104 => 
  array (
    'data' => 'Computer C.O.R.E.,,,www.computercore.org,703 931 7346,3846 King Street,Alexandria,Virginia,22302',
    'programs' => 
    array (
      0 => ',CORE Technology and Life Skills Training,Information Technology,,,,,,',
    ),
  ),
  106 => 
  array (
    'data' => 'Construction Management Association of America (CMAA),,,http://cmaanet.org,703 356 2622 ,7926 Jones Branch Drive Suite 800,McLean,Virginia,22102',
    'programs' => 
    array (
      0 => ',Standards of Practice Course (Construction Manager Certification),Trade,,,,,,',
    ),
  ),
  108 => 
  array (
    'data' => 'CVS Pharmacy,,,www.cvscaremark.com,630 422 4255,1128 Tower Lane,Bensenville,Illinois,60108',
    'programs' => 
    array (
      0 => ',Career Prescription for Success (CAPS),Healthcare,,,,,,',
    ),
  ),
  110 => 
  array (
    'data' => 'David Hoffman Agency - JobForce,,,www.,571 970 9408,2101 L Street, NW Suite 800 ,Washington ,DC,20037, ** 2050 Wilson Boulevard,Arlington,Virginia,22201',
    'programs' => 
    array (
      0 => ',CompTIA A+,Information Technology,,,,,,',
    ),
  ),
  112 => 
  array (
    'data' => 'Daja Institute,,,www.dajainstitute.com,703 383 1600,10400 Eaton Place Suite 201 ,Fairfax,Virginia,22030',
    'programs' => 
    array (
      0 => ',Certified Nursing Assistant,Healthcare,,,,,,',
    ),
  ),
  114 => 
  array (
    'data' => 'Dental Assisting Training & Certification Program of Virginia LLC,,,www.datcpofva.com,703 409 1992 ** 866 249 2440,5973 Piney Grove Way,Gainesville,Virginia,20155',
    'programs' => 
    array (
      0 => ',Dental Administrative Assistant,Healthcare,,,,,,',
      1 => ',Dental Assistant Training,Healthcare,,,,,,',
      2 => ',Dental Office Management,Healthcare,,,,,,',
    ),
  ),
  118 => 
  array (
    'data' => 'Esthetic Institute,,,www.esthetic-institute.com,703 288 4228,8381 Old Courthouse Road Suite 300,Vienna,Virginia,22182',
    'programs' => 
    array (
      0 => ',Esthetic Standard,Cosmetology, Beauty,,,,,,',
      1 => ',Laser Hair Removal,Cosmetology, Beauty,,,,,,',
      2 => ',Laser Skin Specialist,Cosmetology, Beauty,,,,,,',
      3 => ',Make-up Artistry,Cosmetology, Beauty,,,,,,',
      4 => ',Massage Therapy,Wellness,,,,,,',
      5 => ',Master Esthetics,Cosmetology, Beauty,,,,,,',
      6 => ',Nail Technician,Cosmetology, Beauty,,,,,,',
      7 => ',Permanent Makeup Cosmetics,Cosmetology, Beauty,,,,,,',
      8 => ',Waxing Technician,Cosmetology, Beauty,,,,,,',
    ),
  ),
  128 => 
  array (
    'data' => 'Everest College,,,www.cci.edu/campus/woodbridge ** http://tysons-corner.everestcollege.edu/ ,714 348 5919*703 248 8887, 22182 14555 Potomac Mills Road, Woodbridge, Virginia, 22192',
    'programs' => 
    array (
      0 => ',Business Administration (Associate of Science),Professional,,,,,,',
      1 => ',Criminal Justice (Associate of Science) (Arlington and Tysons Corner),Professional,,,,,,',
      2 => ',Medical Administrative Assistant (Tysons Corner and Woodbridge),Healthcare,,,,,,',
      3 => ',Dental Assistant (Woodbridge only),Healthcare,,,,,,',
      4 => ',Medical Assistant,Healthcare,,,,,,',
      5 => ',Medical Insurance Billing and Coding (Tysons Corner and Woodbridge),Healthcare,,,,,,',
      6 => ',Nursing (Tysons Corner only),Healthcare,,,,,,',
      7 => ',Paralegal (Associate of Science) (Arlington only),Professional,,,,,,',
      8 => ',Pharmacy Technician (Woodbridge only),Healthcare,,,,,,',
    ),
  ),
  138 => 
  array (
    'data' => 'Earned Value Management Institute (EVMI),,,www.evmi.com,703 864 6865,724 Hetzel Terrace, SE,Leesburg,Virginia,20177',
    'programs' => 
    array (
      0 => ',Bachelor\'s Certificate in Project Management &trade;,Professional,,,,,,',
      1 => ',CCAM (Certified Control Account Manager &reg; ),Professional,,,,,,',
      2 => ',Chief Earned Value Officer &reg; CEVO &reg; Executive Certificate,Professional,,,,,,',
      3 => ',Earned Value Management Fundamentals,Professional,,,,,,',
      4 => ',Earned Value Management Professional &reg;, EVMP &reg;,Professional,,,,,,',
      5 => ',I Am EVMBOKed&trade;,Professional,,,,,,',
      6 => ',Project Management Fundamentals,Professional,,,,,,',
      7 => ',Project Skills for Obama Administration Stimulus Projects,Professional,,,,,,',
      8 => ',Risk & Quality Management Professional&trade;, RQMP&trade;,Professional,,,,,,',
      9 => ',SPMP (Schedule & Planning Management Professional&trade;),Professional,,,,,,',
    ),
  ),
  149 => 
  array (
    'data' => 'Express Care Inc.,,,www.expresscare.org,703 471 1891,1183 Cypress Tree Place,Herndon,Virginia,20170',
    'programs' => 
    array (
      0 => ',Personal Care Aide,Healthcare,,,,,,',
    ),
  ),
  151 => 
  array (
    'data' => 'Fairfax County Public Schools Adult and Community Education,,,www.fcps.edu/aceclasses,703 658 2784,6815 Edsall Road ,Springfield,Virginia,22151',
    'programs' => 
    array (
      0 => ',Accounting Assistant Certificate,Professional,,,,,,',
      1 => ',Administrative Assistant Certificate,Administration,,,,,,',
      2 => ',Certified Nursing Assistant (CNA),Healthcare,,,,,,',
      3 => ',Child Care Provider Certification,Miscellaneous,,,,,,',
      4 => ',Cisco Certified Network Associate,Information Technology,,,,,,',
      5 => ',Comptia A+ Certification,Information Technology,,,,,,',
      6 => ',Dental Assistant Certificate,Healthcare,,,,,,',
      7 => ',Dental Radiation Safety Certificate,Healthcare,,,,,,',
      8 => ',Dialysis Technician,Healthcare,,,,,,',
      9 => ',EKG Technician Certification,Healthcare,,,,,,',
      10 => ',Electronic Healthcare Records Management,Healthcare,,,,,,',
      11 => ',Emergency Medical Technician Certification (EMT),Healthcare,,,,,,',
      12 => ',Home Healthcare Aide,Healthcare,,,,,,',
      13 => ',Hospital Registration Clerk,Healthcare,,,,,,',
      14 => ',Hospital Unit Secretary,Healthcare,,,,,,',
      15 => ',Human Resources Management,Professional,,,,,,',
      16 => ',Histology Assistant,Healthcare,,,,,,',
      17 => ',Intravenous Therapy Training,Healthcare,,,,,,',
      18 => ',Introduction to Clinical Laboratory,Healthcare,,,,,,',
      19 => ',MCTS (Microsoft Certified Technology Specialist) Windows,Information Technology,,,,,,',
      20 => ',Medical Administrative Assistant Certificate,Healthcare,,,,,,',
      21 => ',Medical Assistant Certificate,Healthcare,,,,,,',
      22 => ',Medical Insurance Biller Certificate,Healthcare,,,,,,',
      23 => ',Microsoft Certified Professional (MCP),Information Technology,,,,,,',
      24 => ',Microsoft Certified Systems Administrator,Information Technology,,,,,,',
      25 => ',Microsoft Certified Systems Engineer,Information Technology,,,,,,',
      26 => ',Oracle Database Administration Certification,Information Technology,,,,,,',
      27 => ',Personal Trainer Certification,Wellness,,,,,,',
      28 => ',Pharmacy Technician,Healthcare,,,,,,',
      29 => ',Pharmacy Technician Exam Prep,Healthcare,,,,,,',
      30 => ',Phlebotomy Technician II,Healthcare,,,,,,',
      31 => ',Physical Therapy Aide,Wellness,,,,,,',
      32 => ',Professional Activity Director Certification,Miscellaneous,,,,,,',
      33 => ',Programming,Information Technology,,,,,,',
      34 => ',Project Management Professional (PMP),Professional,,,,,,',
      35 => ',Small Business Management,Professional,,,,,,',
      36 => ',Tax Preparation Certificate,Professional,,,,,,',
      37 => ',Veterinary Assistant Certificate,Miscellaneous,,,,,,',
      38 => ',Web Design/Development,Information Technology,,,,,,',
      39 => ',Windows Professional Training,Information Technology,,,,,,',
      40 => ',Apprenticeship Programs: Carpentry, Electricity, HVAC, Painting, Plumbing, Optician, Surveying,Trade,,,,,,',
      41 => ',Copper-based CAT & Network Cabling Certification,Trade,,,,,,',
      42 => ',Fiber Optic Network Cabling Certification,Trade,,,,,,',
      43 => ',Property Maintenance Technician Certificate (PMT),Trade,,,,,,',
    ),
  ),
  196 => 
  array (
    'data' => 'Five Star Home Health Care,,,www.fshhc.com,703 662 7500 ,4500 Southgate Place, Suite 200,Chantilly,Virginia,20151',
    'programs' => 
    array (
      0 => ',Personal Care Aide,Healthcare,,,,,,',
    ),
  ),
  198 => 
  array (
    'data' => 'Flightsafety International Inc.,,,www.flightsafety.com,718 565 4141,Marine Air Terminal LaGuardia Airport,Flushing,NY,11371-1061',
    'programs' => 
    array (
      0 => ',International Procedures Training,Aviation,,,,,,',
    ),
  ),
  200 => 
  array (
    'data' => 'Gee\'s Career Training Institute,,,,www.,1822 Featherstone Road ,Woodbridge,Virginia,22191',
    'programs' => 
    array (
      0 => ',Certified Nurse Assistant,Healthcare,,,,,,',
      1 => ',Phlebotomy Technician (VA42),Healthcare,,,,,,',
    ),
  ),
  203 => 
  array (
    'data' => 'George Mason University - Office of Continuing Education,,,http://ocpe.gmu.edu,703 993 2113 ** 703 993 2109,4400 University Drive, MS 2G2,Fairfax,Virginia,22030',
    'programs' => 
    array (
      0 => ',PMP 0402: Project Estimating, Measures and Controls,Professional,,,,,,',
      1 => ',PMP 0403: Managing and Leading Teams,Professional,,,,,,',
      2 => ',PMP 0406: Managing Project Risk,Professional,,,,,,',
      3 => ',PMP 0407: Negotiation and Conflict Resolution,Professional,,,,,,',
      4 => ',PMP 0501: Measuring Project Performance Through Earned Value Management,Professional,,,,,,',
      5 => ',PMP 0503: PMI Methodology Integration with Microsoft Project 2007,Professional,,,,,,',
      6 => ',Additional PM courses,Professional,,,,,,',
      7 => ',Certified Internal Auditor Certificate Program,Professional,,,,,,',
      8 => ',CFG 0201: Timekeeping and Labor Regulations for Federal Contractors,Professional,,,,,,',
      9 => ',CFG 0202: Federal Acquisition Regulations (FAR) and Cost Accounting Standards (CAS),Professional,,,,,,',
      10 => ',CFG 0203: Federal Contract Procurement Cycle,Professional,,,,,,',
      11 => ',CFG 0204: Accounting for Federal Contracts and Dealing with Audits,Professional,,,,,,',
      12 => ',CFG 0205: Government Contracting Fundamentals,Professional,,,,,,',
      13 => ',CIA 0100: The Internal Audit Activity\'s Role in Governance (Risk and Control),Professional,,,,,,',
      14 => ',CIA 0101: Conducting the Internal Audit Engagement,Professional,,,,,,',
      15 => ',CIA 0102: Business Analysis and Information Technology,Professional,,,,,,',
      16 => ',CIA 0103: Business Management Skills,Professional,,,,,,',
      17 => ',Contracting with the Federal Government Certificate Program,Professional,,,,,,',
      18 => ',Core Courses +1 Elective Core,Professional,,,,,,',
      19 => ',Elective Courses,Professional,,,,,,',
      20 => ',FM 0705 FMP Operations and Maintenance,Professional,,,,,,',
      21 => ',FM 0710 FMP Project Management,Professional,,,,,,',
      22 => ',FM 0715 FMP Finance and Business Essentials,Professional,,,,,,',
      23 => ',FM 0720 FMP Leadership and Strategy Essentials,Professional,,,,,,',
      24 => ',Gatlin Education Online Courses - Business, Management, & Leadership,Professional,,,,,,',
      25 => ',Gatlin Education Online Courses - Communications,Professional,,,,,,',
      26 => ',Gatlin Education Online Courses - Construction & Automotive Technology,Professional,,,,,,',
      27 => ',Gatlin Education Online Courses - Finance,Professional,,,,,,',
      28 => ',Gatlin Education Online Courses - Healthcare,Professional,,,,,,',
      29 => ',Gatlin Education Online Courses - Heavy Industry,Professional,,,,,,',
      30 => ',Gatlin Education Online Courses - Internet, Design, & Technical,Professional,,,,,,',
      31 => ',Gatlin Education Online Courses - Video Game Design & Development,Professional,,,,,,',
      32 => ',GIS 0100: Introduction to GIS,Information Technology,,,,,,',
      33 => ',GIS 0110: Introduction to ArcGIS Desktop,Information Technology,,,,,,',
      34 => ',GIS 0200: Components of GIS,Information Technology,,,,,,',
      35 => ',GIS 0300: Introduction to Spatial Analysis,Information Technology,,,,,,',
      36 => ',GIS Fundamentals Track,Information Technology,,,,,,',
      37 => ',HRM 0100: Human Resource Management Certificate Program,Professional,,,,,,',
      38 => ',HRM 0300: Global Professional in Human Resource Management Certificate,Professional,,,,,,',
      39 => ',HRM 202: Essentials of Human Resource Management,Professional,,,,,,',
      40 => ',Human Resource Management Certificate Courses,Professional,,,,,,',
      41 => ',IFMA FMP (Facility Management Professional) Credential Program,Professional,,,,,,',
      42 => ',LCOP 0200: Leadership Coaching for Organizational Performance,Professional,,,,,,',
      43 => ',Leadership Coaching for Organizational Performance,Professional,,,,,,',
      44 => ',Managerial Development for Organizational Effectiveness Certificate,Professional,,,,,,',
      45 => ',MODV 0800: Defining and Developing a Strategic Direction for your Business Unit,Professional,,,,,,',
      46 => ',MODV 0801: Building Commitment and Trust in your Organization,Professional,,,,,,',
      47 => ',MODV 0802: Using Performance Expectations and Feedback to Increase Performance,Professional,,,,,,',
      48 => ',MODV 0803: Leading and Managing Change Successfully,Professional,,,,,,',
      49 => ',MODV 0804: Designing and Using Measures that Matter,Professional,,,,,,',
      50 => ',Paralegal Certificate Course,Professional,,,,,,',
      51 => ',PLGL 100: Paralegal Certificate Program,Professional,,,,,,',
      52 => ',PMP 0400: Essentials of Project Management,Professional,,,,,,',
      53 => ',PMP 0401: Project Management Certification: PMP &reg; Exam Preparation,Professional,,,,,,',
      54 => ',PMP 0502: Financial Analysis For Managers,Professional,,,,,,',
      55 => ',Project Management Certificate Program,Professional,,,,,,',
      56 => ',SUSB 0406: Overview of Sustainability for Existing Buldings,Professional,,,,,,',
      57 => ',SUSB 0415: Sustainability Solutions for O & M,Professional,,,,,,',
      58 => ',SUSB 0425: Implementation Process for Sustainability,Professional,,,,,,',
      59 => ',Sustainability for Existing Building Courses,Professional,,,,,,',
      60 => ',TAIT 0100: Information Technology Foundation (A+, Network+, MCP, CCNA, Security+),Information Technology,,,,,,',
      61 => ',TAIT 0115: IT Business Analyst,Professional,,,,,,',
      62 => ',TAIT 0200: Microsoft Certified Systems Engineer (MCSE),Information Technology,,,,,,',
      63 => ',TAIT 0205: Microsoft Office SharePoint Server 2007 - Configuration,Information Technology,,,,,,',
      64 => ',TAIT 0206: MCITP: Enterprise Administrator Windows Server 2008,Information Technology,,,,,,',
      65 => ',TAIT 0207: Microsoft Certified Application Specialist,Information Technology,,,,,,',
      66 => ',TAIT 0209: Microsoft Office SharePoint 2010: Configuration and Administration,Information Technology,,,,,,',
      67 => ',TAIT 0406: Oracle 11g Database Administration,Information Technology,,,,,,',
      68 => ',TAIT 0407: Introduction to Oracle 11g SQL,Information Technology,,,,,,',
      69 => ',TAIT 0408: Introduction to Oracle 11g PL/SQL,Information Technology,,,,,,',
      70 => ',TAIT 0409: Oracle 11g Certified Associate,Information Technology,,,,,,',
      71 => ',TAIT 0410: Oracle 11g Certified Professional,Information Technology,,,,,,',
      72 => ',TAIT 0412: Oracle 11g RAC Administration,Information Technology,,,,,,',
      73 => ',TAIT 0415: Oracle E-Business Suite R12 Financials: Payables,Information Technology,,,,,,',
      74 => ',TAIT 0502: Web Design and Developer,Information Technology,,,,,,',
      75 => ',TAIT 0510: Advanced Web Developer with ASP.NET,Information Technology,,,,,,',
      76 => ',TAIT 0511: Sun Certified Java Programmer,Information Technology,,,,,,',
      77 => ',TAIT 0515: Advanced Web Solutions,Information Technology,,,,,,',
      78 => ',TAIT 0600: Certified Information System Security Professional (CISSP),Information Technology,,,,,,',
      79 => ',TAIT 0602: Certified Hacking Forensic Investigator (CHFI),Information Technology,,,,,,',
      80 => ',TAIT 0603: Certified Ethical Hacker (CEH),Information Technology,,,,,,',
      81 => ',TechAdvance Information Technology Courses,Information Technology,,,,,,',
    ),
  ),
  286 => 
  array (
    'data' => 'Global Health College,,,www.global.edu,703 212 7410,25 South Quaker Lane ,Alexandria,Virginia,22314',
    'programs' => 
    array (
      0 => ',Associate Degree in Nursing/Registered Nurse (AAS),Healthcare,,,,,,',
      1 => ',Nurse\'s Aide (100621),Healthcare,,,,,,',
      2 => ',Practical Nursing,Healthcare,,,,,,',
    ),
  ),
  290 => 
  array (
    'data' => 'Global Knowledge Training LLC,,,www.globalknowledge.com,919 463 4223*800 268 7737,900 Regency Parkway Suite 500,Cary,North Carolina,27518',
    'programs' => 
    array (
      0 => ',TSHOOT - Troubleshooting and Maintaining Cisco IP Networks v1.0 (5171W) Self-Paced/E-Learning,Information Technology,,,,,,',
      1 => ',A+ Certification Prep Course (Classroom),Information Technology,,,,,,',
      2 => ',A+ Certification Prep Course (E-Learning),Information Technology,,,,,,',
      3 => ',Business Process Analysis (Classroom),Professional,,,,,,',
      4 => ',Business Process Analysis (E-Learning),Professional,,,,,,',
      5 => ',CCNA Boot Camp 2.0 (Classroom),Information Technology,,,,,,',
      6 => ',CCNA Boot Camp 2.0 (E-Learning),Information Technology,,,,,,',
      7 => ',CCNAX-CCNA Boot Camp [v1.1] (5031C) Classroom,Information Technology,,,,,,',
      8 => ',CCNAX-CCNA Boot Camp [v1.1] (5031s) Self-Paced e-Learning,Information Technology,,,,,,',
      9 => ',Certified Ethical Hacker (Classroom),Information Technology,,,,,,',
      10 => ',Certified ScrumMaster Workshop (2512C) Classroom,Information Technology,,,,,,',
      11 => ',CISSP Certified Information Systems Security Professional Prep (Classroom),Information Technology,,,,,,',
      12 => ',CISSP Certified Information Systems Security Professional Prep (E- Learning),Information Technology,,,,,,',
      13 => ',CISSP Certified Information Systems Security Professional Prep (Self- Paced),Information Technology,,,,,,',
      14 => ',ICND1 - Interconnecting Cisco Network Devices (Classroom),Information Technology,,,,,,',
      15 => ',ICND1 - Interconnecting Cisco Network Devices (E-Learning),Information Technology,,,,,,',
      16 => ',ICND1 - Interconnecting Cisco Network Devices (Self-Paced),Information Technology,,,,,,',
      17 => ',ICND2 - Interconnecting Cisco Network Devices (Classroom),Information Technology,,,,,,',
      18 => ',ICND2 - Interconnecting Cisco Network Devices (E-Learning),Information Technology,,,,,,',
      19 => ',ICND2 - Interconnecting Cisco Network Devices (Self-Paced),Information Technology,,,,,,',
      20 => ',ICOMM-Introducing Cisco Voice and UC Administration [v8.0] (5756C) Classroom,Information Technology,,,,,,',
      21 => ',IINS-Implementing Cisco IOS Network Security [v1.0] (5241C) Classroom,Information Technology,,,,,,',
      22 => ',IINS-Implementing Cisco IOS Network Security [v1.0] (5241L) Live Virtual Classroom,Information Technology,,,,,,',
      23 => ',IINS-Implementing Cisco IOS Network Security [v1.0] (5241s) Self-Paced e-Learning,Information Technology,,,,,,',
      24 => ',ITIL   Service   Capability:   Release,   Control,   and   Validation   (2726C) Classroom,Information Technology,,,,,,',
      25 => ',ITIL Information Technology Infrastructure Library v3 Foundation (Classroom),Information Technology,,,,,,',
      26 => ',ITIL Information Technology Infrastructure Library v3 Foundation (E- Learning),Information Technology,,,,,,',
      27 => ',ITIL Information Technology Infrastructure Library v3 Foundation (Self Paced),Information Technology,,,,,,',
      28 => ',ITIL Service Capability: Release, Control, and Validation (2726V) Live Virtual Classroom,Information Technology,,,,,,',
      29 => ',ITIL Service Lifecycle: Service Strategy (2719C) Classroom,Information Technology,,,,,,',
      30 => ',ITIL Service Lifecycle: Service Strategy (2719V) Live Virtual Classroom,Information Technology,,,,,,',
      31 => ',IUWNE-Implementing  Cisco  Unified  Wireless  Networks  [v1.0]  (5999G) Classroom,Information Technology,,,,,,',
      32 => ',IUWNE-Implementing Cisco Unified Wireless Networks [v1.0] (5999L) Live Virtual Classroom,Information Technology,,,,,,',
      33 => ',MCITP: Database Administrator Boot Camp (Classroom),Information Technology,,,,,,',
      34 => ',MCITP: Database Administrator Boot Camp (Self-Paced),Information Technology,,,,,,',
      35 => ',MCITP: Server Administrator Boot Camp (Classroom),Information Technology,,,,,,',
      36 => ',MCITP: Server Administrator Boot Camp (Self-Paced),Information Technology,,,,,,',
      37 => ',MCITP: Server and Enterprise Administrator Combo Boot Camp (Classroom),Information Technology,,,,,,',
      38 => ',MCITP: Server and Enterprise Administrator Combo Boot Camp (E- Learning),Information Technology,,,,,,',
      39 => ',Microsoft 2003 MCSA Boot Camp (Classroom),Information Technology,,,,,,',
      40 => ',Microsoft 2003 MCSA Boot Camp (Self-paced E-Learning),Information Technology,,,,,,',
      41 => ',Microsoft 2003 MCSE Boot Camp (Classroom),Information Technology,,,,,,',
      42 => ',Microsoft 2003 MCSE Boot Camp (Self-paced E-Learning),Information Technology,,,,,,',
      43 => ',Network+ Prep Course (Classroom),Information Technology,,,,,,',
      44 => ',Network+ Prep Course (E-Learning),Information Technology,,,,,,',
      45 => ',Network+ Prep Course (Self-Paced),Information Technology,,,,,,',
      46 => ',PMP Exam Prep Boot Camp (Classroom),Professional,,,,,,',
      47 => ',PMP Exam Prep Boot Camp (E-Learning),Professional,,,,,,',
      48 => ',Project Management Skills Builder Program,Professional,,,,,,',
      49 => ',RH124 - Red Hat System Administration I (1268C) Classroom,Information Technology,,,,,,',
      50 => ',RH124 - Red Hat System Administration I (1268L) Virtual Classroom,Information Technology,,,,,,',
      51 => ',RH135 - Red Hat System Administration II with RHCSA (Red Hat Certified Systems Administrator) Exam (1273C) Classroom,Information Technology,,,,,,',
      52 => ',RH200 - RHCSA (Red Hat Certified Systems Administrator) Rapid Track Course with Exam (1277C) Classroom,Information Technology,,,,,,',
      53 => ',RH300 - RHCE (Red Hat Certified Engineer) Rapid Track Course with RHCSA (Red Hat Certified Systems Administrator) & RHCE Exam (1282C) Classroom,Information Technology,,,,,,',
      54 => ',ROUTE - Implementing Cisco IP Routing v1.0 (5169C) Classroom,Information Technology,,,,,,',
      55 => ',ROUTE - Implementing Cisco IP Routing v1.0 (5169W) Self-Paced/E- Learning,Information Technology,,,,,,',
      56 => ',Security + Prep Course (9829C) Classroom,Information Technology,,,,,,',
      57 => ',Security + Prep Course (9829L) Virtual Classroom,Information Technology,,,,,,',
      58 => ',Security + Prep Course (9829W) Self-Paced/E-Learning,Information Technology,,,,,,',
      59 => ',SWITCH - Implementing Cisco IP (Internet Protocol) Switching v1.0 (5170C) Classroom,Information Technology,,,,,,',
      60 => ',SWITCH - Implementing Cisco IP (Internet Protocol) Switching v1.0 (5170W) Self-Paced/E-Learning,Information Technology,,,,,,',
      61 => ',TSHOOT - Troubleshooting and Maintaining Cisco IP Networks v1.0,Information Technology,,,,,,',
      62 => ',TSHOOT - Troubleshooting and Maintaining Cisco IP Networks v1.0 (5171C) Classroom,Information Technology,,,,,,',
      63 => ',VMware vSphere: Install, Configure, Manage v4.1 (Classroom),Information Technology,,,,,,',
      64 => ',VMware vSphere: Install, Configure, Manage v4.1 (E-Learning),Information Technology,,,,,,',
    ),
  ),
  356 => 
  array (
    'data' => 'Goodwill of Greater Washington,,,www.dcgoodwill.org,703 769 3706,2200 South Dakota Avenue, NE,Washington,DC,20018',
    'programs' => 
    array (
      0 => ',Customer Service Training,Miscellaneous,,,,,,',
      1 => ',Security & Protective Services Training Program,Security,,,,,,',
    ),
  ),
  359 => 
  array (
    'data' => 'Grace Ministries of the UMC,,,www.,703 793 0026,13600 Frying Pan Road ,Herndon,Virginia,20171',
    'programs' => 
    array (
      0 => ',CDL/ESOL Prep Training,Commercial Driving,,,,,,',
    ),
  ),
  361 => 
  array (
    'data' => 'Harrisburg Area Community College,,,www.hacc.edu,717 221 1358 ** 717 221 1300,One HACC Drive, CS 402 ,Harrisburg,Pennsylvania,17110',
    'programs' => 
    array (
      0 => ',Building Analyst/Energy Audit,Trade,,,,,,',
      1 => ',Solar PV Electricity Systems (40 hours),Trade,,,,,,',
      2 => ',Solar PV Electricity Systems (60 hours - offers more fundamental training in Electrical/Electricity Functions),Trade,,,,,,',
    ),
  ),
  365 => 
  array (
    'data' => 'Headz 1st Barber Institute,,,www.headz1st.com,571 313 8872,46000 Old Ox Road, Unit 104,Sterling,Virginia,20109',
    'programs' => 
    array (
      0 => ',Barbering,Cosmetology,,,,,,',
    ),
  ),
  367 => 
  array (
    'data' => 'Higher Power Aviation Inc.,,,www.jetcrew.com,972 641 4661,4650 Diplomacy Road,Forth Worth,Texas,76155',
    'programs' => 
    array (
      0 => ',Boeing 737 Aircraft Type Rating,Aviation,,,,,,',
    ),
  ),
  369 => 
  array (
    'data' => 'Independent Electrical Contractors Chesapeake,,,www.iec-chesapeake.com,301 621 9545 Ex 105 ** 800 470 3013,13944-D Willard Road,Chantilly,Virginia,20151',
    'programs' => 
    array (
      0 => ',Apprenticeship Training for Electrical Construction Workers,Trade,,,,,,',
    ),
  ),
  371 => 
  array (
    'data' => 'Intellectual Point,,,www.intellectualpoint.com,703 554 3827,11321 Sunset Hills Road,Reston,Virginia,20190',
    'programs' => 
    array (
    ),
  ),
  372 => 
  array (
    'data' => 'Joyce Carelock Ministries H.E.L.P Center,,,www.joycecarelockmin.org,571 428 2695**571 379 5583,9161 Liberia Avenue, Suite 200,Manassas,Virginia,20110',
    'programs' => 
    array (
      0 => ',Administrative Certificate,Administration,,,,,,',
      1 => ',Career Development Training,Professional,,,,,,',
      2 => ',Customer Service Certificate,Miscellaneous,,,,,,',
      3 => ',Parenting Education Training,Miscellaneous,,,,,,',
    ),
  ),
  377 => 
  array (
    'data' => 'Knowledge Center Inc.,,,www.knowledgecenterinc.com,703 726 9666,44075 Pipeline Plaza Suite 120,Ashburn,Virginia,20147',
    'programs' => 
    array (
      0 => ',CEH - Certified Ethical Hacking,Information Technology,,,,,,',
      1 => ',CISSP (Certified Information Systems Security Professional),Information Technology,,,,,,',
      2 => ',Comp TIA A+,Information Technology,,,,,,',
      3 => ',Comp TIA Network +,Information Technology,,,,,,',
      4 => ',Comp TIA Security +,Information Technology,,,,,,',
      5 => ',Computer Forensics,Information Technology,,,,,,',
      6 => ',Information Security Management System (ISMS)-ISO 27001 Foundation Training,Information Technology,,,,,,',
      7 => ',Information Technology Infrastructure Library (ITIL) Foundation,Information Technology,,,,,,',
      8 => ',ISMS/ISO 27001 Lead Auditor,Information Technology,,,,,,',
      9 => ',ISMS/ISO 27001 Lead Implementer,Information Technology,,,,,,',
      10 => ',IT Service Management System (ITSMS)-ISO/IEC 20000 Foundation Training,Information Technology,,,,,,',
      11 => ',ITIL v3 Intermediate Qualification/Managing Across the Lifecycle (MALC),Information Technology,,,,,,',
      12 => ',ITIL/Intermediate Continual Service Improvement (CSI),Information Technology,,,,,,',
      13 => ',ITIL/Service Transition & Operation,Information Technology,,,,,,',
      14 => ',ITSMS-ISO/IEC 20000 Lead Auditor Training,Information Technology,,,,,,',
      15 => ',ITSMS-ISO/IEC 20000 Lead Implementer Training,Information Technology,,,,,,',
      16 => ',MCSE (Microsoft Certified Solutions Expert),Information Technology,,,,,,',
      17 => ',Microsoft Dynamics AX Training,Information Technology,,,,,,',
      18 => ',Project Management Professional (PMP),Professional,,,,,,',
      19 => ',SOX - Sarbanes Oxley,Information Technology,,,,,,',
    ),
  ),
  398 => 
  array (
    'data' => 'Le Arai Beauty & Barber Academy,,,www.,703 204 1166,8630-K Lee Highway,Fairfax,Virginia,22031',
    'programs' => 
    array (
      0 => ',Barber,Cosmetology, Beauty,,,,,,',
      1 => ',Cosmetology,Cosmetology, Beauty,,,,,,',
      2 => ',Esthetician,Cosmetology, Beauty,,,,,,',
      3 => ',Massage Therapist,Wellness,,,,,,',
      4 => ',Master Esthetician,Cosmetology, Beauty,,,,,,',
      5 => ',Nail Technician,Cosmetology, Beauty,,,,,,',
    ),
  ),
  405 => 
  array (
    'data' => 'Loudoun County Public Schools,,,www.lcps.org/adulted,703 771 6406,21000 Education Court,Ashburn,Virginia,20148',
    'programs' => 
    array (
      0 => ',English as a Second Language,Literacy,,,,,,',
      1 => ',General Education Development,Literacy,,,,,,',
    ),
  ),
  408 => 
  array (
    'data' => 'M.C. Dean Inc.,,,www.mcdean.com,571 262 8554**703 802 6231,22461 Shaw Road,Dulles,Virginia,20165',
    'programs' => 
    array (
      0 => ',BICSI Installer I and II,Trade,,,,,,',
      1 => ',BICSI Technician,Trade,,,,,,',
    ),
  ),
  411 => 
  array (
    'data' => 'Manpower International,,,www.us.manpower.com,703 771 5871*703 481 5202,607 Herndon Parkway #208,Herndon,Virginia,20170',
    'programs' => 
    array (
      0 => ',Administrative Assistant,Administration,,,,,,',
      1 => ',Bookkeeping, Accounting, Auditing,Professional,,,,,,',
      2 => ',Customer Service Representative,Miscellaneous,,,,,,',
      3 => ',Data Entry Operator,Administration,,,,,,',
      4 => ',Office Clerk - General,Administration,,,,,,',
      5 => ',Receptionist,Administration,,,,,,',
      6 => ',Warehousing,Miscellaneous,,,,,,',
    ),
  ),
  419 => 
  array (
    'data' => 'Marcella Ellis School of Braiding Arts,,,www.marcellaellisschool.com,703 261 6313,10560 Main Street, Suite 506,Fairfax,Virginia,22030',
    'programs' => 
    array (
      0 => ',Hair Braiding/Weaving,Cosmetology, Beauty,,,,,,',
    ),
  ),
  421 => 
  array (
    'data' => 'MedCerts,,,www.medcerts.com,800 496 6112,Online,Online,Online,Online',
    'programs' => 
    array (
      0 => ',Electronic Health Records & Reimbursement Specialist Program,Healthcare,,,,,,',
      1 => ',Electronic Health Records Specialist Program,Healthcare,,,,,,',
      2 => ',Medical Billing Specialist,Healthcare,,,,,,',
      3 => ',Medical Front Office & Electronic Health Records Program,Healthcare,,,,,,',
      4 => ',Medical Front Office Administration Specialist,Healthcare,,,,,,',
      5 => ',Medical Front Office Assistant & Administration Specialist,Healthcare,,,,,,',
      6 => ',Pharmacy Technician Program (510805),Healthcare,,,,,,',
    ),
  ),
  429 => 
  array (
    'data' => 'MedTech,,,www.medtech.edu,703 237 6200,6565 Arlington Boulevard,Falls Church,Virginia,22042',
    'programs' => 
    array (
      0 => ',Medical Assisting, Diploma,Healthcare,,,,,,',
      1 => ',Medical Office Specialist, Diploma,Healthcare,,,,,,',
      2 => ',Medical Assisting, Associate of Applied Science (AAS),Healthcare,,,,,,',
      3 => ',Medical Billing and Coding, AAS,Healthcare,,,,,,',
    ),
  ),
  434 => 
  array (
    'data' => 'Metamorphosis LLC,,,www.,703 682 0219,10560 Main Street, Suite LL15,Fairfax,Virginia,22030',
    'programs' => 
    array (
      0 => ',Cosmetology,Cosmetology, Beauty,,,,,,',
    ),
  ),
  436 => 
  array (
    'data' => 'Metropolitan Institute of Health & Technology (MIHT),,,www.mihtschool.com,866 496 3046,8170-C Silverbrook Road,Lorton,Virginia,22079',
    'programs' => 
    array (
      0 => ',Clinical Medical Assisting,Healthcare,,,,,,',
      1 => ',Dental Assistant,Healthcare,,,,,,',
      2 => ',EKG Technician,Healthcare,,,,,,',
      3 => ',Medical Billing and Coding,Healthcare,,,,,,',
      4 => ',Pharmacy Technician,Healthcare,,,,,,',
      5 => ',Phlebotomy Technician,Healthcare,,,,,,',
    ),
  ),
  443 => 
  array (
    'data' => 'MULTD LLC,,,www.multd.org,703 662 5808,13557 Tabscott Drive,Chantilly,Virginia,20151',
    'programs' => 
    array (
      0 => ',The GigSpire Program,Professional,,,,,,',
    ),
  ),
  445 => 
  array (
    'data' => 'Multivision Inc.,,,www.multivision.cc,703 225 1000,10565 Fairfax Boulevard, Suite 301,Fairfax,Virginia,22030',
    'programs' => 
    array (
      0 => ',J2EE Training,Information Technology,,,,,,',
      1 => ',Sharepoint,Information Technology,,,,,,',
      2 => ',Quality Analyst/Software Testing (QA),Information Technology,,,,,,',
    ),
  ),
  449 => 
  array (
    'data' => 'My Language Institute,,,www.mlius.com,703 243 1422,6066 Leesburg Pike, Suite 600,Falls Church,Virginia,22041',
    'programs' => 
    array (
      0 => ',ESL (English as a Second Language),Literacy,,,,,,',
    ),
  ),
  451 => 
  array (
    'data' => 'National Massage Therapy Institute,,,www.nmti.edu,703 237 3905,803 West Broad Street, Suite 400,Falls Church,Virginia,22046',
    'programs' => 
    array (
      0 => ',Massage Therapy,Wellness,,,,,,',
    ),
  ),
  453 => 
  array (
    'data' => 'National Personal Training Institute,,,www.nptifitness.com,800 960 6294,8500 Leesburg Pike, #205,Vienna,Virginia,22182',
    'programs' => 
    array (
      0 => ',Certified Personal Trainer,Wellness,,,,,,',
    ),
  ),
  455 => 
  array (
    'data' => 'New Horizons Computer Learning Center of Washington D.C.,,,www.dcnewhorizons.com,703 749 4030,2010 Corporate Ridge Suite 200,McLean,Virginia,22102',
    'programs' => 
    array (
      0 => ',A+ Certification,Information Technology,,,,,,',
      1 => ',MCTS - Microsoft Certified Technology Specialist Program,Information Technology,,,,,,',
      2 => ',Network+ Certification,Information Technology,,,,,,',
      3 => ',Office Productivity Skills Program With Microsoft Project,Information Technology,,,,,,',
      4 => ',PC Support Technician Program (Richmond only),Information Technology,,,,,,',
      5 => ',Security+ Certification,Information Technology,,,,,,',
      6 => ',Web & Graphics Skills Program,Information Technology,,,,,,',
    ),
  ),
  463 => 
  array (
    'data' => 'North American Lineman Training Center,,,www.,931 582 4161,490 Gravelly Run Road,McEwen,Tennessee,37101',
    'programs' => 
    array (
      0 => ',Pre-Apprentice Lineworker Program,Trade,,,,,,',
    ),
  ),
  465 => 
  array (
    'data' => 'Northern Virginia Community College,,,www.nvcc.edu,703 323 3168, CE 202 8333 Little River Turnpike, Annandale Campus ,Annandale,Virginia,22003, ** 703 845 6280,3001 N. Beauregard Street,Alexandria,Virginia,22311 ** 703 450 2551,21335 Signal Hill Plaza, Suite 300,Sterling,Virginia,20165 ** 703 257 6630,6901 Sudley Road,Manassas,Virginia,20109 ** 703 822 6523,Medical Center, 6699 Springfield Center Drive,Springfield,Virginia,22150 ** 703 878 5770,15200 Neabsco Mills Road, WS 226,Woodbridge,Virginia,22191',
    'programs' => 
    array (
      0 => ',C / C++ Certificate Program,Information Technology,,,,,,',
      1 => ',CAD and Auto CAD,Information Technology,,,,,,',
      2 => ',Career Switchers (EducateVA-Career Switchers),Professional,,,,,,',
      3 => ',Certificate in Management Practices,Professional,,,,,,',
      4 => ',Certified Nursing Assistant (CNA) Certificate Program,Healthcare,,,,,,',
      5 => ',Cisco Networking Certificate Program,Information Technology,,,,,,',
      6 => ',Coaching Certificate Program,Professional,,,,,,',
      7 => ',Computer and Networking Security Certificate Program,Information Technology,,,,,,',
      8 => ',Computer Technology Certificate Program,Information Technology,,,,,,',
      9 => ',Configuration Management / Unix Certificate Program,Information Technology,,,,,,',
      10 => ',Dental Assistant, I DENT 1912,Healthcare,,,,,,',
      11 => ',Dental Office Administration Certificate Program,Healthcare,,,,,,',
      12 => ',Dental Orthodontic Certificate Program,Healthcare,,,,,,',
      13 => ',Digital Video & Film Production Certificate Program,Information Technology,,,,,,',
      14 => ',Federal Contract Management,Professional,,,,,,',
      15 => ',Hospitality Readiness Certificate Program,Healthcare,,,,,,',
      16 => ',Human Resource Management Certificate Program,Professional,,,,,,',
      17 => ',Interior Decoration Certificate Program,Miscellaneous,,,,,,',
      18 => ',Internet Networking Certificate Program,Information Technology,,,,,,',
      19 => ',JAVA Certificate Program,Information Technology,,,,,,',
      20 => ',Medical Clinical Assistant HLTH 1587,Healthcare,,,,,,',
      21 => ',Medical Office Specialist HLTH 1744,Healthcare,,,,,,',
      22 => ',Microsoft Office Certificate Program,Information Technology,,,,,,',
      23 => ',Multimedia and Web Design Certificate Program: Two Tracks: (A) Multimedia Design Track,Information Technology,,,,,,',
      24 => ',Multimedia and Web Design Certificate Program: Two Tracks: (B) Web Design Track,Information Technology,,,,,,',
      25 => ',Nonprofit Leadership & Administration Certificate,Professional,,,,,,',
      26 => ',Oracle Database Certificate Program,Information Technology,,,,,,',
      27 => ',Organizational Development & Leadership Certificate,Professional,,,,,,',
      28 => ',Pharmacy Tech HLTH 1503,Healthcare,,,,,,',
      29 => ',Photography and Emerging Imaging Certificate Program,Miscellaneous,,,,,,',
      30 => ',Practical Nurse Program (28-139),Healthcare,,,,,,',
      31 => ',Project Management Program,Professional,,,,,,',
      32 => ',RN Return to Practical Nursing HLTH 1756,Healthcare,,,,,,',
      33 => ',Technology Retraining Internship Program (TRIP),Information Technology,,,,,,',
      34 => ',TESOL/TESL Certificate Program,Education,,,,,,',
      35 => ',Unix Boot Camp Certificate Program,Information Technology,,,,,,',
      36 => ',Unix Certificate Program,Information Technology,,,,,,',
      37 => ',Web and Internet Programming,Information Technology,,,,,,',
      38 => ',Wedding Coordinator Certificate Program,Miscellaneous,,,,,,',
    ),
  ),
  505 => 
  array (
    'data' => 'Northern Virginia Family Service,,,www.nvfs.org,Tysons: 571 748 2860,8301 Greensboro Drive, Suite 130,McLean,Virginia,22102, ** 9485 Innovation Drive, Suite 103,Manassas,Virginia,20110',
    'programs' => 
    array (
      0 => ',Training Futures - Microsoft Office / Administrative Assistant,Administration,,,,,,',
    ),
  ),
  507 => 
  array (
    'data' => 'Northern Virginia Security Academy,,,www.millerserve.com,703 241 4911,118-B East Broad Street,Falls Church,Virginia,22046',
    'programs' => 
    array (
      0 => ',Firearms Training Entry Level,Security,,,,,,',
      1 => ',Private Investigator Entry Level,Security,,,,,,',
      2 => ',Shotgun Training Entry Level,Security,,,,,,',
    ),
  ),
  511 => 
  array (
    'data' => 'Not Furlong Temps Inc.,,,http://medicallearningcenter.net,703 444 7232,20609 Gordon Park Square, #130,Ashburn,Virginia,20147',
    'programs' => 
    array (
      0 => ',I.V. Training Course,Healthcare,,,,,,',
      1 => ',Medical Assistant II (Includes Medical Billing & Coding, Phlebotomy),Healthcare,,,,,,',
      2 => ',Medical Assistant Training,Healthcare,,,,,,',
      3 => ',Medical Front Desk/Billing and Coding,Healthcare,,,,,,',
      4 => ',Phlebotomy Training,Healthcare,,,,,,',
    ),
  ),
  517 => 
  array (
    'data' => 'Nurse Aide Training Institute of Northern Virginia,,,www.natinstitute.com,571 278 5783,8703 Shadowlake Way, Springfield,Virginia,22153',
    'programs' => 
    array (
      0 => ',Nurses Aide Training Program,Healthcare,,,,,,',
    ),
  ),
  519 => 
  array (
    'data' => 'Off Peak Training,,,www.offpeaktraining.com,866 661 2521,1801 Robert Fulton Dr., Suite 240,Reston,Virginia,20191',
    'programs' => 
    array (
      0 => ',ITIL (Information Technology Infrastructure Library) Foundation (3 Day Bootcamp),Information Technology,,,,,,',
      1 => ',PMI-RMP (Risk Management Professional) - Exam Prep (2 Day Bootcamp),Professional,,,,,,',
      2 => ',PMI-SP (Project Scheduling Professional) Exam Prep (2 Day Bootcamp),Professional,,,,,,',
      3 => ',PMP0000 (Project Management Professional) Exam Prep (5 Day Bootcamp),Professional,,,,,,',
      4 => ',PMP0001 - PMP Exam Prep (Night/Weekend),Professional,,,,,,',
      5 => ',PMP0004 - PMP Exam Prep (4 Day Bootcamp),Professional,,,,,,',
    ),
  ),
  526 => 
  array (
    'data' => 'Peoplentech Institute of Information Technology Inc.,,,www.peoplentech.com,703 586 7848,1604 Spring Hill Road, Suite 302,Vienna,Virginia,22182',
    'programs' => 
    array (
      0 => ',Database Administrators: 15-1061.00,Information Technology,,,,,,',
      1 => ',Software Testing Code: 15-1501.00,Information Technology,,,,,,',
    ),
  ),
  529 => 
  array (
    'data' => 'Piedmont School of Professional Massage,,,www.piedmontmassage.com/school.htm,703 497 4437,13885 Hedgewood Drive, Suite 333,Dale City,Virginia,22193',
    'programs' => 
    array (
      0 => ',Massage Therapy,Wellness,,,,,,',
    ),
  ),
  531 => 
  array (
    'data' => 'PM Express Inc.,,,www.pmexpressinc.com,866 792 9260,11025 Burywood Lane,Reston,Virginia,20194',
    'programs' => 
    array (
      0 => ',PME-01 - PMP/CAPM (Project Management Professional/ Certified Associate in Project Management),Professional,,,,,,',
    ),
  ),
  533 => 
  array (
    'data' => 'Potomac Massage Training Institute,,,www.pmti.org,202 686 7046,5028 Wisconsin Avenue, NW, Suite LL,DC,20016',
    'programs' => 
    array (
      0 => ',Personal Training Program,Wellness,,,,,,',
    ),
  ),
  535 => 
  array (
    'data' => 'Prince William County Public Schools,,,www.pwcs.edu,703 791 7357,14800 Joplin Road,Manassas,Virginia,20112',
    'programs' => 
    array (
      0 => ',Adult Basic Education / GED Preparation,Literacy,,,,,,',
      1 => ',English for Speakers of Other Languages (ESOL),Literacy,,,,,,',
      2 => ',National External Diploma Program,Literacy,,,,,,',
    ),
  ),
  539 => 
  array (
    'data' => 'Roses Home Healthcare,,,www.roseshhc.com,703 590 4750,13512 Minnieville Road, Suite 220,Woodbridge,Virginia,22192',
    'programs' => 
    array (
      0 => ',Certified Nursing Assistant (100119),Healthcare,,,,,,',
      1 => ',Medication Aide Training Program,Healthcare,,,,,,',
      2 => ',Personal Care Aide Training (Virginia DMAS Approved),Healthcare,,,,,,',
    ),
  ),
  543 => 
  array (
    'data' => 'Rutgers University School of Business Development - Camden,,,http://www.rutgers.edu*imed@camden.rutgers.edu,856 225 6685,227 Penn Street,Camden,New Jersey,8102',
    'programs' => 
    array (
      0 => ',Lean and Six Sigma Green Belt Training - Career Fast Track (offered Online and at Moorestown New Jersey),Professional,,,,,,',
    ),
  ),
  545 => 
  array (
    'data' => 'Salvation Academy,,,www.salvationacademy.org,703 763 1115,4613 Pinecrest Office Park Drive,Alexandria,Virginia,22312',
    'programs' => 
    array (
      0 => ',Certified Medication Aide,Healthcare,,,,,,',
      1 => ',Certified Nursing Aide,Healthcare,,,,,,',
      2 => ',CPR(CardioPulmonary Resuscitation), First Aid,Healthcare,,,,,,',
      3 => ',Pharmacy Technician Training Program,Healthcare,,,,,,',
      4 => ',AED (Automated External Defibrillator),First Aid,,,,,,'
    ),
  ),
  550 => 
  array (
    'data' => 'Security Training Academy,,,www.jbissi.com,703 916 8411,7263 Maple Place, Suite 203,Annandale,Virginia,22003',
    'programs' => 
    array (
      0 => ',Advanced Handgun (09E),Security,,,,,,',
      1 => ',Armed Security Officer Arrest Authority (05E),Security,,,,,,',
      2 => ',Bail Bondsman (40E),Security,,,,,,',
      3 => ',Bail Enforcement (44E),Security,,,,,,',
      4 => ',Handgun (07E),Security,,,,,,',
      5 => ',Personal Protection Specialist (32E),Security,,,,,,',
      6 => ',Private Investigator (02E),Security,,,,,,',
      7 => ',Security Officer (01E),Security,,,,,,',
      8 => ',Shotgun (08E),Security,,,,,,',
      9 => ',Special Conservator of the Peace (06E),Security,,,,,,',
    ),
  ),
  561 => 
  array (
    'data' => 'Security University,,,www.securityuniversity.net,877 357 7744,510 Spring Street, Suite 130,Herndon,Virginia,20170',
    'programs' => 
    array (
      0 => ',CISSP (Certified Information Systems Security Professional)  Training class; Training with eStudy; Training with eStudy and video,Information Technology,,,,,,',
      1 => ',CompTIA (Computing Technology Industry Association) Security + IAT (Information Assurance Technical) II Certification Training class; Training with eStudy; Training class with eStudy and exam; Training class with eStudy exam and video,Information Technology,,,,,,',
      2 => ',Q/ISP (Qualified/Information Security Professional) QISP001 Q/SA Qualified/Security Analyst Penetration Tester; QISP002 Q/PTL Qualified/Penetration Tester Licenseo; QISP003 Q/EH Qualified/Ethical Hacker; QISP004 Q/ND Qualified/Network Defender; QISP005 Q/FE Qualified/Forensic Expert,Information Technology,,,,,,',
    ),
  ),
  565 => 
  array (
    'data' => 'ServiceSource,,,www.servicesource.org,703 461 6000,6295 Edsall Road, Suite 175,Alexandria,Virginia,22312',
    'programs' => 
    array (
      0 => ',Employability Certification Microsoft Office Specialist,Information Technology,,,,,,',
      1 => ',Laurie Mitchell Employment Center,Information Technology,,,,,,',
    ),
  ),
  568 => 
  array (
    'data' => 'Shippers\' Choice of Virginia Inc.,,,www.shipperschoice.com,703 396 8822,9202 Manassas Drive,Manassas Park,Virginia,20111',
    'programs' => 
    array (
      0 => ',CDL Class A/B Tractor Trailer Driver Training (no passenger endorsement),Commercial Driving,,,,,,',
    ),
  ),
  570 => 
  array (
    'data' => 'Sholla Corporation,,,www.sholla.com,703 755 0807**301 588 3893,911 Silver Spring Ave., Suite 101,Silver Spring,Maryland,20910',
    'programs' => 
    array (
      0 => ',ASP.Net,Information Technology,,,,,,',
      1 => ',Basic Computer Application,Information Technology,,,,,,',
      2 => ',Microsoft Business Intelligence,Information Technology,,,,,,',
      3 => ',Microsoft Office 2003/2007,Information Technology,,,,,,',
      4 => ',Microsoft SharePoint 2007,Information Technology,,,,,,',
      5 => ',MS SQL Server Database Administration,Information Technology,,,,,,',
      6 => ',Oracle Database 11g,Information Technology,,,,,,',
      7 => ',Quickbooks 2009,Information Technology,,,,,,',
    ),
  ),
  579 => 
  array (
    'data' => 'SkillsQuest (A Division of SecBay Inc.),,,www.skillsquest.com,703 766 6311,1900 Campus Commons Drive, Suite 100,Reston,Virginia,20191',
    'programs' => 
    array (
      0 => ',Biometrics Security Engineer,Information Technology,,,,,,',
      1 => ',Linux Professional II,Information Technology,,,,,,',
    ),
  ),
  582 => 
  array (
    'data' => 'SmartPath LLC,,,www.smartpathllc.com, 855 762 7850 * 301 584 8614,1050 Connecticut Ave. NW, 10th Floor,Washington,DC,20036',
    'programs' => 
    array (
      0 => ',BUSPR52 Business Process Analysis Training,Professional,,,,,,',
      1 => ',MSPRO10 Microsoft Project (Power Learning),Information Technology,,,,,,',
      2 => ',PMTR01 CAPM Certification Training with Exam Preparation,Professional,,,,,,',
      3 => ',TRPM22 PMP Certification Training with Exam Preparation,Professional,,,,,,',
    ),
  ),
  587 => 
  array (
    'data' => 'SOFEI Group Inc.,,,www.sofeigroup.org,888 246 2006,211 North Union Street Suite 100,Alexandria,Virginia,22314',
    'programs' => 
    array (
      0 => ',Microsoft Office Specialist (MOS) Certification Training 2007 & 2010 (Fastrack),Information Technology,,,,,,',
    ),
  ),
  589 => 
  array (
    'data' => 'Southeast Lineman Training Center,,,www.lineworker.com,706 657 3792,9481 Highway 11,Trenton,Georgia,30752',
    'programs' => 
    array (
      0 => ',Electrical Lineworker Program,Trade,,,,,,',
    ),
  ),
  591 => 
  array (
    'data' => 'Spectrum Beauty Academy,,,www.learnatspectrum.com,703 370 9700,25 South Quaker Lane, Suite 15 ,Alexandria,Virginia,22314',
    'programs' => 
    array (
      0 => ',Cosmetology,Cosmetology, Beauty,,,,,,',
      1 => ',Nail Technology,Cosmetology, Beauty,,,,,,',
    ),
  ),
  594 => 
  array (
    'data' => 'Spofford & Associates Inc.,,,www.spoffordassociates.com,703 755 0031,4031 Chain Bridge Road, Suite 102,Fairfax,Virginia,22030',
    'programs' => 
    array (
      0 => ',Private Investigator (02E),Security,,,,,,',
    ),
  ),
  596 => 
  array (
    'data' => 'StraightPath Consulting,,,www.,540 547 2342,3415 Novum Road,Reva,Virginia,22735',
    'programs' => 
    array (
      0 => ',Intermediate Work Zone Safety,Trade,,,,,,',
      1 => ',Basic Work Zone Safety,Trade,,,,,,',
    ),
  ),
  599 => 
  array (
    'data' => 'Stratford University,,,www.stratford.edu,703 821 8570 ,7777 Leesburg Pike,Falls Church,Virginia,22043',
    'programs' => 
    array (
      0 => ',Certificate in Accounting,Professional,,,,,,',
      1 => ',Event Management Certificate,Professional,,,,,,',
    ),
  ),
  602 => 
  array (
    'data' => 'Super Hair\'s Beauty Academy,,,www.superhairsbeautyacademy.com,703 273 7920 * 571 224 2329,1061 West Broad Street,Falls Church,Virginia,22046',
    'programs' => 
    array (
      0 => ',Cosmetology,Cosmetology, Beauty,,,,,,',
      1 => ',Cosmetology Instructor,Cosmetology, Beauty,,,,,,',
    ),
  ),
  605 => 
  array (
    'data' => 'SZ PM Consultants Inc.,,,www.szpmconsultants.com,703 678 8622,700 Bennett Street,Herndon,Virginia,20170',
    'programs' => 
    array (
      0 => ',Project Management Professional (PMP) Certification Exam Preparation,Professional,,,,,,',
    ),
  ),
  607 => 
  array (
    'data' => 'TASC Management Corporation,,,www.nawaz@tascmanagement.com * nosherwan.raja@tascmanagement.com,703 310 7760,21525 Ridgetop Circle, Suite 180,Sterling,Virginia,20166',
    'programs' => 
    array (
      0 => ',Certified Ethical Hacker (CEH),Information Technology,,,,,,',
      1 => ',Certified Information Systems Security Professional (CISSP),Information Technology,,,,,,',
      2 => ',Cisco Certified Network Associate (CCNA),Information Technology,,,,,,',
      3 => ',Cloud Computing Fundamentals,Information Technology,,,,,,',
      4 => ',CompTIA A+,Information Technology,,,,,,',
      5 => ',CompTIA Network +,Information Technology,,,,,,',
      6 => ',CompTIA Security +,Information Technology,,,,,,',
      7 => ',Information Technology Infrastructure Library (ITIL) V3 Foundation,Information Technology,,,,,,',
      8 => ',Microsoft Certified IT Professional (MCITP),Information Technology,,,,,,',
      9 => ',Microsoft Certified Systems Engineer (MCSE),Information Technology,,,,,,',
      10 => ',Oracle Database Administration 11g,Information Technology,,,,,,',
      11 => ',Project Management Professional,Professional,,,,,,',
      12 => ',Virtualization Foundation,Information Technology,,,,,,',
      13 => ',Web Development,Information Technology,,,,,,',
      14 => ',ISO 27001 Lead Implementer,Information Technology,,,,,,',
    ),
  ),
  623 => 
  array (
    'data' => 'TransformationServices Inc.,,,www.drhicks@transformationservices.info,414 933 7083,835 N 23rd Street, Suite 212,Milwaukee,Wisconsin,53233',
    'programs' => 
    array (
      0 => ',Substance Abuse & Prevention Specialist Counselor (Online),Wellness,,,,,,',
    ),
  ),
  625 => 
  array (
    'data' => 'Tysons College,,,www.tysonscollege.com,703 506 1300,8230 Old Courthouse Road, Suite 425,Vienna,Virginia,22182',
    'programs' => 
    array (
      0 => ',A+ Certified Technician,Information Technology,,,,,,',
      1 => ',Accounting Certificate,Professional,,,,,,',
      2 => ',English as Second Language (ESL),Literacy,,,,,,',
      3 => ',Java,Information Technology,,,,,,',
      4 => ',Microsoft Office 2000/XP/2003,Information Technology,,,,,,',
      5 => ',Oracle Database,Information Technology,,,,,,',
      6 => ',Teaching English as a Second Language (TESL),Literacy,,,,,,',
      7 => ',Web Design Program,Information Technology,,,,,,',
    ),
  ),
  634 => 
  array (
    'data' => 'Ultimate Health Care Services Inc.,,,www.ultimatehealthschool.com,703 933 9430,7839 Ashton Ave,Manassas,Virginia,20109',
    'programs' => 
    array (
      0 => ',Certified Phlebotomy Technician,Healthcare,,,,,,',
      1 => ',Medication Aide,Healthcare,,,,,,',
      2 => ',Nursing Assistant Program,Healthcare,,,,,,',
      3 => ',Practical Nursing Program,Healthcare,,,,,,',
    ),
  ),
  639 => 
  array (
    'data' => 'United Air Temp,,,www.unitedairtemp.com,800 890 4328 Ex 8603,6900 Hill Park Drive,Lorton,Virginia,22079',
    'programs' => 
    array (
      0 => ',HVAC Technician Training Program Level I,Trade,,,,,,',
      1 => ',HVAC Technician Training Program Level II,Trade,,,,,,',
      2 => ',HVAC Technician Training Program Level III,Trade,,,,,,',
    ),
  ),
  643 => 
  array (
    'data' => 'University of Fairfax,,,www.ufairfax.edu,703 790 3200,Online,Online,Online,Online',
    'programs' => 
    array (
      0 => ',Cybersecurity Best Practices (CBP) - CISSP - online program,Information Technology,,,,,,',
      1 => ',Information Security Professional Practices (ISPP) - online program,Information Technology,,,,,,',
    ),
  ),
  646 => 
  array (
    'data' => 'VETS Group/Metrodata Networks,,,www.vetsgroup.org,202 822 0011 ,1200 - 18th Street, N.W. Suite LL-100 ,Washington,DC,20036',
    'programs' => 
    array (
      0 => ',Certified Technology Specialist (CTS),Information Technology,,,,,,',
      1 => ',Cisco Certified Entry Network Technician,Information Technology,,,,,,',
      2 => ',Cisco Certified Network Associate,Information Technology,,,,,,',
      3 => ',IT Essentials: PC Hardware & Software,Information Technology,,,,,,',
      4 => ',MCTS (Microsoft Certified Technology Specialist) - Administering and Maintaining Windows 7,Information Technology,,,,,,',
      5 => ',Microsoft Office Specialist,Information Technology,,,,,,',
      6 => ',MTA - (Microsoft Technology Associate) Networking Fundamentals,Information Technology,,,,,,',
      7 => ',MTA - Security Fundamentals,Information Technology,,,,,,',
      8 => ',MTA - Windows O.S. (Operating Systems) Fundamentals,Information Technology,,,,,,',
    ),
  ),
  656 => 
  array (
    'data' => 'Virginia School of Pet Grooming,,,www.virginiaschoolofpetgrooming.com,703 361 3868,9471 Manassas Drive ,Manassas Park,Virginia,20111',
    'programs' => 
    array (
      0 => ',All Breed Grooming,Veterinary Tech,,,,,,',
      1 => ',Cat Grooming,Veterinary Tech,,,,,,',
      2 => ',Dog Grooming,Veterinary Tech,,,,,,',
    ),
  ),
  658 => 
  array (
    'data' => 'Virginia Tech Continuing & Professional Education,,,www.cpe.vt.edu,571 858 3006,900 N. Glebe Road ,Arlington,Virginia,22203',
    'programs' => 
    array (
      0 => ',Achieving ITIL (Information Technology Infrastructure Library) Certification,Information Technology,,,,,,',
      1 => ',Advanced Legal Research and Writing,Professional,,,,,,',
      2 => ',Advanced Paralegal Certificate Course,Professional,,,,,,',
      3 => ',Agile Project Management with Scrum,Professional,,,,,,',
      4 => ',Basic PM Practitioners Certificate Program,Professional,,,,,,',
      5 => ',Complex Project Management,Professional,,,,,,',
      6 => ',Developing User Requirements,Professional,,,,,,',
      7 => ',Dispute Resolution Certificate Course,Professional,,,,,,',
      8 => ',Intellectual Property Law for Engineers,Professional,,,,,,',
      9 => ',Legal Investigation Certificate Course,Professional,,,,,,',
      10 => ',Legal Nurse Consultant Course,Professional,,,,,,',
      11 => ',Legal Secretary Consultant Course,Professional,,,,,,',
      12 => ',Managing Global Projects,Professional,,,,,,',
      13 => ',Personal Injury for Paralegals,Professional,,,,,,',
      14 => ',Preparing for the PMP (Project Management Professional) Exam,Professional,,,,,,',
      15 => ',Project Budgeting and Estimating,Professional,,,,,,',
      16 => ',Project Management - Skills for Success,Professional,,,,,,',
      17 => ',Project Management in the Cloud,Professional,,,,,,',
      18 => ',Project Team Leadership,Professional,,,,,,',
      19 => ',Project Team Leadership,Professional,,,,,,',
      20 => ',Risk Management,Professional,,,,,,',
      21 => ',Software Essentials in the Law Office,Information Technology,,,,,,',
      22 => ',Victim Advocacy Certificate Course,Professional,,,,,,',
    ),
  ),
);
    
    
   }
   
}   