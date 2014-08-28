<?php
class Zend_View_Helper_InlineEdit extends Zend_View_Helper_Abstract
{
    function InlineEdit($value,
                        $pk,
                        $url,
                        $name="",
                        $title="",
                        $type="text",
                        $src=false,
                        $class="editable", $id=null, $attrs=''){
        
        
        
        $id = empty($id) ? "edit-".$name : $id;
        $dsrc = ($src) ? "data-source='$src'" : '';
        return "<a class='$class' id='$id' data-name='$name' data-type='$type' data-pk='$pk' $dsrc data-url='$url' {$attrs} data-title='$title' >$value</a>";
    }
    
}
