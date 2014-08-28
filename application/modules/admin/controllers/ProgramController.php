<?php
class Admin_ProgramController extends Zend_Controller_Action {

   public $id;
   public $xhr;
   public $uri;
   public $post;
   public $format;
   public $render;
   public $callback;
   public $searchField;
   public $searchWord;
   
   
   
   public function init() {
    
        $this->id = $this->getRequest()->getParam('id', $this->getRequest()->getParam('pk', null));
        $this->xhr = $this->getRequest()->isXmlHttpRequest();
        $this->uri = $this->getRequest()->getRequestUri();
        $this->sort = $this->getRequest()->getParam('sort', false);
        $this->post = $this->getRequest()->isPost();
        $this->page = $this->getRequest()->getParam('page', 1);
        $this->params = $this->getRequest()->getParams();
        $this->format = $this->getRequest()->getParam('format', false);
        $this->render = $this->getRequest()->getParam('render', false);
        
        
        $this->callback = $this->getRequest()->getParam('callback', null);
        Zend_Layout::getMvcInstance()->assign('sideNav', 'N');
        $this->form = new Application_Form_Admin_ProvidersPrograms;
        
        $this->searchField = $this->getRequest()->getParam('searchField', 'industry_id');
        $this->searchWord = $this->getRequest()->getParam('searchWord', null);
        
        if( $this->xhr ) {
            $this->_helper->layout->disableLayout();
        }
        
        $this->_model = new Default_Model_Program;
        $this->view->xhr =  $this->xhr;
        $this->view->render = $this->render;
    }
   
    public function indexAction() {
        
        
        $industries = new Default_Model_Industry;
        $this->view->industries = $industries->_index();
        
        $where = array();
        if( isset($this->searchWord) ) {
            $keyword = is_numeric( $this->searchWord ) ? $this->searchWord : "{$this->searchWord}%";
            $hook = is_numeric( $this->searchWord ) ? "=" : "LIKE";
            $where[ trim($this->searchField) . " {$hook} ? " ] =  $keyword;
        }
        $count = $this->_model->_count($where);
        $paginate = new Base_Template_Paginate(50, $count, $this->page);
        
        $this->_model->page_limit = $paginate->get_limit();
        $this->_model->page_offset = $paginate->get_offset();
        $this->view->count = $count;
        $this->view->paginate = $paginate;
        $this->view->params = $this->params;
        $this->view->programs = $this->_model->_joinProviders($where);
        $this->view->title = "Programs";
        
        $providers = new Default_Model_Provider;
        $this->view->providers = $providers->_index();
        
    }

    /*
     * 
    * Allow inline editing of users in the admin pannel.
    */
    public function inlineEditAction() {
        $pk    = (int)$this->getRequest()->getParam('pk');
        $field = strip_tags($this->getRequest()->getParam('name'));
        $value = strip_tags($this->getRequest()->getParam('value'));
        $res   = $this->_model->_update($data);
        $this-> _asJson(array("success"=>$res, "pk"=>$pk, "name"=>$field, "value"=>$value));
    }

    
    protected function _asJson(array $data) {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);   
        $this->getResponse()->setHeader('Content-type', 'application/json')
                            ->setBody(json_encode($data));
    }
}