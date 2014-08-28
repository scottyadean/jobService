<?php
class Base_Template_Paginate
{
    public $current_page;
    public $per_page;
    public $total_count;
    public $asyncAttr = "";
    public $className = "";
    public $id = "pagination";
    
    public function __construct($per_page=20, $total_count=0, $curr_page=1){
        $this->current_page	= (int)$curr_page;
        $this->per_page		= (int)$per_page;
        $this->total_count	= (int)$total_count;
    }
    
    public function offset() {
            return ($this->current_page -1) * $this->per_page;
    }				
    
    public function links($self = '', $html = '') {
        $html .= '<div id="'.$this->id.'" class="pagination" data-total="'
                            .$this->total_count.'" data-pages="'
                            .$this->total_pages().'" data-current="'
                            .$this->current_page.'"><ul>';
                                
            if($this->total_pages() > 1) {
                    
                    if($this->has_previous_page()){
                            $html .=  "<li><a class='{$this->className} page-back' data-page='back' {$this->asyncAttr}href='{$self}/{$this->previous_page()}'>back</a></li>";
                    }else{	
                            $html .=  "<li class='disabled'><a class='{$this->className}  page-back' data-page='back' >back</a></li>";	
                    }
                            
                    for($i=1; $i <= $this->total_pages(); $i++){
                            $html.= $i==$this->current_page ? "<li class='active'><a class='{$this->className}' data-page='{$i}'>{$i}</a></li>"
                            :" <li><a class='{$this->className}'  data-page='{$i}' {$this->asyncAttr}href='{$self}/{$i}'>{$i}</a><li>";
                    }
                    
                    if($this->has_next_page()){
                            $html .= "<li><a class='{$this->className} page-next' data-page='next' {$this->asyncAttr}href='{$self}/{$this->next_page()}'>next</a></li>";
                    }else{
                            $html .= "<li class='disabled'><a data-page='next' class='{$this->className}  page-next' >next</a></li>";	
                    }
            }
            
            $html .= '</ul></div>';
            
            return $html;
    }
    
    
    public function get_offset()
    {	
            return (int)$this->offset();
    }
    
    public function get_limit()
    {	
            return (int)$this->per_page;
    }
    
    
    public function total_pages()
    {
            return ceil($this->total_count/$this->per_page);
    }
    
    public function next_page()
    {
            return $this->current_page + 1;
    }
    
    public function has_next_page()
    {
            return $this->next_page() <= $this->total_pages() ? true : false;
    }
    
    public function previous_page()
    {
            return $this->current_page - 1;
    }
            
    public function has_previous_page()
    {
            return $this->previous_page() >= 1 ? true : false;
    }	
            
}		