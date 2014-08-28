<?php

class Base_Functions_Assets
{
	public $img = NULL;
	public $typ = 1;
	
	public static $currentAssets;


        public static function saveImageTo($upload, $handle, $path, $scale=500)
        {
        
             //get the file name
             $fileInfo = $upload->getFileInfo($handle);
     
             //get temp name
             $temp = $fileInfo[$handle]['tmp_name'];
     
             //clean the file name
             $filename = Base_Functions_Files::fileNameCleaner($fileInfo[$handle]['name']);
     
             //resize the image to 500px and save it.     
             return self::imageResizeSave($temp, $path, $scale);

        }


	
	/*Resize and save an image*/
	public static function imageResizeSave($src, $path='db', $size=500)
	{	/*Get max W x H  */
		$mw = $mh = $size;
		/*Get Dimensions and type */ 
		list($w, $h, $type) = getimagesize($src);
		
                /*if w/h larger then max height || max width resize W/ aspect ratio */
		if($w > $mw || $h > $mh)
		{	($mw && ($w < $h)) ? $mw =($mh/$h) * $w : $mh = ($mw/$w) * $h;
		}else{
			$mw = $w; $mh = $h;	
		}
		
                
                
                /* Resample */
		$new = self::detailed_resize_image($src, $mw, $mh, true, 'return', false, false );
                
                
                if( $path != 'db' ){
                
                    /*overwrite the src file with the temp file*/
                    Base_Functions_Files::imageSave($temp,$path,$type);
                    
                    /*clear memory*/
                    imagedestroy($new);

                    return true;
		
                }else{
                   
                    ob_start();
                    imagejpeg($new, null, 100);
                    $imageData = ob_get_clean();
                    imagedestroy($new);
                    return  $imageData;
 
                }
                
               
		
                return false;
	} 
	
	/*Rotate an image */
	public static function rotate($resource,$degree=0)
	{	$resource = isset($degree) ? imagerotate($resource, intval($degree), 0) : $resource;
		return $resource;
	} 
	
	/*Resize save and crop an image */
	public function crop($src,$nw=50,$nh=50,$x,$y,$rotate=NULL,$save=false,$as=NULL)
	{	
		/* Get image info */
		list($w, $h, $type) = getimagesize($src);
		/*Create image from src */
		$new = Base_Functions_Files::createFrom($src,$type);
		/*Create a temp image*/
		$temp = Base_Functions_Files::createGDFile($nw,$nh);
		/* Resample */
		imagecopyresampled($temp, $new, 0, 0, $x, $y, $nw, $nh, $nw, $nh);
		/* Rotate */
		if(isset($rotate))$temp = image::rotate($temp,$rotate);
		/*Save the new image file */
		if($save){$as = isset($as) ? Base_Functions_Files::imageSave($temp,trim($as),$type) : Base_Functions_Files::imageSave($temp,$src,$type);}
		/* Capture the temp image in a public var */
		$this->img = $temp;
		$this->typ = $type;
		/*Clear memory*/
		imagedestroy($new);
		//imagedestroy($temp);
	}  
	


   public static function fileExists( $dir, $id , $name ){ 
   
        return in_array($name,self::getAssetNames($dir,$id));
   
   }
   
   
   public static function getAssetNames($dir,$id) {
   
        if(is_null(self::$currentAssets))
            self::$currentAssets = Base_Functions_Files::listDirContents($dir.DS.$id);
   
        return self::$currentAssets;
   
   }


   public static function imageSave() {
    
    
        print "No Upload function applied to this app @ ".__FILE__;
        exit;
   }




  public static function detailed_resize_image( $file,
                                                $width              = 0, 
                                                $height             = 0, 
                                                $proportional       = false, 
                                                $output             = 'file', 
                                                $delete_original    = true, 
                                                $use_linux_commands = false ) {
      
    if ( $height <= 0 && $width <= 0 ) return false;

    # Setting defaults and meta
    $info                         = getimagesize($file);
    $image                        = '';
    $final_width                  = 0;
    $final_height                 = 0;
    list($width_old, $height_old) = $info;

    # Calculating proportionality
    if ($proportional) {
      if      ($width  == 0)  $factor = $height/$height_old;
      elseif  ($height == 0)  $factor = $width/$width_old;
      else                    $factor = min( $width / $width_old, $height / $height_old );

      $final_width  = round( $width_old * $factor );
      $final_height = round( $height_old * $factor );
    }
    else {
      $final_width = ( $width <= 0 ) ? $width_old : $width;
      $final_height = ( $height <= 0 ) ? $height_old : $height;
    }

    # Loading image to memory according to type
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:   $image = imagecreatefromgif($file);   break;
      case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($file);  break;
      case IMAGETYPE_PNG:   $image = imagecreatefrompng($file);   break;
      default: return false;
    }
    
    
    # This is the resizing/resampling/transparency-preserving magic
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $transparency = imagecolortransparent($image);

      if ($transparency >= 0) {
        $transparent_color  = imagecolorsforindex($image, $trnprt_indx);
        $transparency       = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
        imagefill($image_resized, 0, 0, $transparency);
        imagecolortransparent($image_resized, $transparency);
      }
      elseif ($info[2] == IMAGETYPE_PNG) {
        imagealphablending($image_resized, false);
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
        imagefill($image_resized, 0, 0, $color);
        imagesavealpha($image_resized, true);
      }
    }
    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
    
    # Taking care of original, if needed
    if ( $delete_original ) {
      if ( $use_linux_commands ) exec('rm '.$file);
      else @unlink($file);
    }

    # Preparing a method of providing result
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $file;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
    
    # Writing image according to type to the output destination
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
      case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output);   break;
      case IMAGETYPE_PNG:   imagepng($image_resized, $output);    break;
      default: return false;
    }

    return true;
  }



}
?>
