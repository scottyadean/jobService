<?php
class Base_Functions_Array {

    public static function In(array $ary, $index = "id", $ids = array()) {
       
        foreach( $ary as $k=>$v ) {
            $ids[] = $v[$index];   
        }
        
        return $ids;
        
    }
}