<?php
class Main_Forms_Crud {
    /*
    * _user
    * curd object. 
    */
    protected $_user = array("db" => "users",
                             "url" => "/crud-user/:crud",
                             "form" => "Application_Form_User",
                             "title" => "Users",
                             "excluded_from_list" => array('id', 'account_id', 'address','city','state','zip','bio', 'password', 'salt', 'role', 'position'),
                             "excluded_from_view"  => array('id', 'account_id', 'password', 'salt'));
    
    
    
   protected $_admin_user = array("db" => "users",
                                    "url" => "/crud-admin-user/:crud",
                                    "form" => "Application_Form_User",
                                    "title" => "Admin Users",
                                    "onCreate"=>"Application_Form_User::setAsAdmin",
                                    "excluded_from_list" => array('id', 'account_id', 'address','city','state','zip','bio', 'password', 'salt', 'role', 'position'),
                                    "excluded_from_view"  => array('id', 'account_id', 'password', 'salt'));

    
  protected $_qualifiers = array("db" => "qualifiers",
                                 "url" => "/crud-qualifiers/:crud",
                                 "form" => "Application_Form_Admin_Qualifiers",
                                 "title" => "Qualifiers",
                                 "excluded_from_list" => array('id'),
                                 "excluded_from_view"  => array('id'));
    
   
    
    /*
    * _pages
    * curd object. 
    */
    protected $_page = array("db" => "pages",
                             "url" => "/crud-page/:crud",
                             "form" => "Application_Form_Admin_Pages",
                             "title" => "Page",
                             "onCreate"=>"Application_Form_Admin_Pages::onCreate",
                             "onUpdate"=>"Application_Form_Admin_Pages::onUpdate",
                             "onValidate"=>"Application_Form_Admin_Pages::onValidate",
                             "excluded_from_list" => array('id', 'modules'),
                             "excluded_from_view"=> array('id'));

                             
    protected $_provider = array("db" => "providers",
                            "url" => "/crud_provider/:crud",
                            "form" => "Application_Form_Admin_Providers",
                            "title" => "Provider",
                            "onCreate"=>"Application_Form_Admin_Providers::onCreate",
                            "onUpdate"=>"Application_Form_Admin_Providers::onUpdate",
                            "onValidate"=>"Application_Form_Admin_Providers::onValidate",
                            'onDelete'=>'Application_Form_Admin_Providers::onDelete',
                            "excluded_from_list" => array('id', 'industry id'),
                            "excluded_from_view"=> array('id'));

    /*
    *
    * curd object. 
    */
    protected $_provider_locations = array("db" => "providers_locations",
                                            "url" => "/crud_provider/locations/:crud/provider_id/:provider_id",
                                            "form" => "Application_Form_Admin_ProvidersLocations",
                                            "title" => "Address",
                                            "where" => "provider_id=>:provider_id",
                                            "excluded_from_list" => "id,provider_id",
                                            "excluded_from_view"=> array('id'));
    
    
    /*
    *
    * curd object. 
    */
    protected $_provider_programs = array("db" => "providers_programs",
                            "url" => "/crud_provider/programs/:crud/provider_id/:provider_id",
                            "form" => "Application_Form_Admin_ProvidersPrograms",
                            "title" => "Programs",
                            "where" => "provider_id=>:provider_id",
                            "afterInsert"=>"Application_Form_Admin_ProvidersPrograms::onInsert",
                            "onUpdate"=>"Application_Form_Admin_ProvidersPrograms::onUpdate",
                            "onDelete"=>"Application_Form_Admin_ProvidersPrograms::onDelete",
                            "excluded_from_list" => "id,provider_id",
                            "excluded_from_view"=> array('id'));



    protected $_skillsrc_centers= array("db" => "skillsrc_centers",
                                        "url" => "/crud-center/:crud",
                                        "form" => "Application_Form_Admin_SkillSrcCenters",
                                        "title" => "Centers",
                                        "excluded_from_list" => "id",
                                        "excluded_from_view" => array('id'));

    
    protected $_module  = array("db" => "modules",
                                "url" => "/crud-module/:crud",
                                "form" => "Application_Form_Admin_PageModules",
                                "title" => "Html Modules",
                                "excluded_from_list" => "id,body",
                                "excluded_from_view"=> array('id'));
    
    
    protected $_content  = array("db" => "contents",
                                "url" => "/crud-content/:crud",
                                "form" => "Application_Form_Admin_PageContent",
                                "title" => "Html Page Contents",
                                "excluded_from_list" => "id",
                                "onCreate"=>"Application_Form_Admin_PageContent::onCreate",
                                "onUpdate"=>"Application_Form_Admin_PageContent::onUpdate",
                                "excluded_from_view"=> array('id'));
    
    
    protected $_event = array("db" => "events",
                              "url" => "/crud-event/:crud",
                              "form" => "Application_Form_Admin_Event",
                              "title" => "Events",
                              "onUpdate"=>"Application_Form_Admin_Event::onUpdate",
                              "onDelete"=>"Application_Form_Admin_Event::onDelete",
                              "excluded_from_list" => "id,body",
                              "excluded_from_view"=> array('id'));    
    
    
    /*
     * return a protected property by name
    */
    public function getProperty($property) {
        if(property_exists ( $this ,$property )) {
            return $this->$property;
        }else{
            return false;
        }
    }
    
}