<?
namespace Digidez;

class Image{

	function getFileExts($filename){
		$filename = strtolower($filename);
		$parts = explode(".", $filename);
		$exts = end($parts);
		//$n = count($exts)-1;
		//$exts = $exts[$n];
		return $exts;
	} 
	
	function getOnlyFileName($filename){
		$only_name = str_replace(".".self::getFileExts($filename), "", $filename);
		return $only_name;
	} 
	
	function constrain_dimensions( $current_width, $current_height, $max_width=0, $max_height=0 ) {
		if ( !$max_width and !$max_height )
			return array( $current_width, $current_height );
	
		$width_ratio = $height_ratio = 1.0;
	
		if ( $max_width > 0 && $current_width > 0 && $current_width > $max_width )
			$width_ratio = $max_width / $current_width;
	
		if ( $max_height > 0 && $current_height > 0 && $current_height > $max_height )
			$height_ratio = $max_height / $current_height;
	
		// the smaller ratio is the one we need to fit it to the constraining box
		$ratio = min( $width_ratio, $height_ratio );
	
		return array( intval($current_width * $ratio), intval($current_height * $ratio) );
	}
	
	function image_resize_dimensions($orig_w, $orig_h, $dest_w, $dest_h, $crop = false) {
	
		if ($orig_w <= 0 || $orig_h <= 0)
			return false;
		// at least one of dest_w or dest_h must be specific
		if ($dest_w <= 0 && $dest_h <= 0)
			return false;
	
		if ( $crop ) {
			// crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
			$aspect_ratio = $orig_w / $orig_h;
			$new_w = min($dest_w, $orig_w);
			$new_h = min($dest_h, $orig_h);
	
			if ( !$new_w ) {
				$new_w = intval($new_h * $aspect_ratio);
			}
	
			if ( !$new_h ) {
				$new_h = intval($new_w / $aspect_ratio);
			}
	
			$size_ratio = max($new_w / $orig_w, $new_h / $orig_h);
	
			$crop_w = round($new_w / $size_ratio);
			$crop_h = round($new_h / $size_ratio);
	
			$s_x = floor( ($orig_w - $crop_w) / 2 );
			$s_y = floor( ($orig_h - $crop_h) / 2 );
		} else {
			// don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
			$crop_w = $orig_w;
			$crop_h = $orig_h;
	
			$s_x = 0;
			$s_y = 0;
	
			list( $new_w, $new_h ) = self::constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
		}
	
		// if the resulting image would be the same size or larger we don't want to resize it
		if ( $new_w >= $orig_w && $new_h >= $orig_h )
			return false;
	
		// the return array matches the parameters to imagecopyresampled()
		// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
		return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
	
	}
	
	function CreateThumb($image_path, $sourse_file, $dest_file_type="auto", $preview_width=0, $preview_height=0, $quality=100, $prefix="", $sufix="", $dest_path="", $rename=true, $crop_resized=true){
		$flag = true;
		$src_file_type = self::getFileExts($sourse_file);
		$src_file = $image_path.$sourse_file;
	
		if(!is_array($rename)){
			if($rename == true){
				$dest_file_name = mt_rand();
			}else{
				$dest_file_name = self::getOnlyFileName($sourse_file);
			}
		}else{
			if($rename[0] == true){
				$dest_file_name = self::getOnlyFileName($rename[1]);
			}elseif($rename[0] == false || $rename[0] == ""){
				$dest_file_name = self::getOnlyFileName($sourse_file);
			}
		}
		//$dest_file_name = ($rename) ? mt_rand() : str_replace(".".$src_file_type, "", $sourse_file);
		$dest_file_type = ($dest_file_type == 'auto' || $dest_file_type == '') ? $src_file_type : $dest_file_type;
		
		$dst_file = $prefix.$dest_file_name.$sufix.".".$dest_file_type;
		$dst_file = ($dest_path == "") ? $image_path.$dst_file : $dest_path.$dst_file;
		
		list($img_w, $img_h) = @getimagesize($src_file);
		
		//$thumb_size_w = ($preview_width > 0) ? $preview_width : 236;
		//$thumb_size_h = ($preview_height > 0) ? $preview_height : 293;
		$thumb_size_w = $preview_width;
		$thumb_size_h = $preview_height;
	
		if(($thumb_size_w == 0) && ($thumb_size_h == 0)){
			$thumb_size_w = $img_w;
			$thumb_size_h = $img_h;
		}
		
		if(($thumb_size_w == 0) || ($thumb_size_h == 0)){
			//$crop_resized=false;
		}
	
		if($crop_resized == false){
			$tmp_w = imagesx($src_img);
			$tmp_h = imagesy($src_img);
			if($tmp_w == $tmp_h){
				$crop_resized = true;
			}
		}
	
	
		$dst_x = 0;
		$dst_y = 0;
		$src_x = 0;
		$src_y = 0;
	
		if($crop_resized == false){
			$dims = self::image_resize_dimensions($img_w, $img_h, $thumb_size_w, $thumb_size_h, $crop_resized);
			list($dst_x, $dst_y, $src_x, $src_y, $new_w, $new_h, $src_w, $src_h) = array_values($dims);
			$thumb_size_w = $new_w;
			$thumb_size_h = $new_h;
		}else{
			if(($img_w != $thumb_size_w) || ($img_h != $thumb_size_h)){
				if($img_h > $img_w){ //if vertical image
					if($thumb_size_h > 0){
						$img_div = round($img_h / $thumb_size_h, 2);
						$new_h = $thumb_size_h;
					}else{
						$img_div = round($img_h / ($thumb_size_h > 0) ? $thumb_size_h : $img_w / $thumb_size_w, 2);
						$new_h = ($thumb_size_h > 0) ? $thumb_size_h : $thumb_size_h = round($img_h / $img_div);
					}
					$new_w = round($img_w / $img_div);
					if($new_w > $thumb_size_w){
						if($thumb_size_w > 0){
							//$new_w = ($thumb_size_w > 0) ? $thumb_size_w : $thumb_size_w = $new_w;
							$dst_x = "-".round(($new_w - $thumb_size_w) / 2);
						}elseif($thumb_size_w <= 0){
							$thumb_size_w = $new_w;
						}
					}elseif($new_w < $thumb_size_w){
						$img_div = round($img_w / $thumb_size_w, 2);
						$new_w = ($thumb_size_w > 0) ? $thumb_size_w : $thumb_size_w = $new_w;
						$new_h = round($img_h / $img_div);
						$dst_y = "-".round(($new_h - $thumb_size_h) / 2);
					}
				}elseif($img_w > $img_h){ //if horizontal image
					if($thumb_size_w > 0){
						$img_div = round($img_w / $thumb_size_w, 2);
						$new_w = $thumb_size_w;
					}else{
						$img_div = round($img_w / ($thumb_size_w > 0) ? $thumb_size_w : $img_h / $thumb_size_h, 2);
						$new_w = ($thumb_size_w > 0) ? $thumb_size_w : $thumb_size_w = round($img_w / $img_div);
					}
					$new_h = round($img_h / $img_div);
					if($new_h > $thumb_size_h){
						if($thumb_size_h > 0){
							//$new_h = ($thumb_size_h > 0) ? $thumb_size_h : $thumb_size_h = $new_h;
							$dst_y = "-".round(($new_h - $thumb_size_h) / 2);
						}elseif($thumb_size_h <= 0){
							$thumb_size_h = $new_h;
						}
					}elseif($new_h < $thumb_size_h){
						$img_div = round($img_h / $thumb_size_h, 2);
						$new_h = ($thumb_size_h > 0) ? $thumb_size_h : $thumb_size_h = $new_h;
						$new_w = round($img_w / $img_div);
						$dst_x = "-".round(($new_w - $thumb_size_w) / 2);
					}
				}elseif($img_h == $img_w){ //if squared image
					//$new_w = ($thumb_size_w > 0) ? $thumb_size_w : ($thumb_size_h > 0) ? $thumb_size_w = $thumb_size_h : $thumb_size_w = $img_w;
					//$new_h = ($thumb_size_h > 0) ? $thumb_size_h : ($thumb_size_w > 0) ? $thumb_size_h = $thumb_size_w : $thumb_size_h = $img_h;
					
					//$new_w = ($thumb_size_w > 0) ? $thumb_size_w : ($thumb_size_h > 0) ? $thumb_size_h : $img_w;
					//$new_h = ($thumb_size_h > 0) ? $thumb_size_h : ($thumb_size_w > 0) ? $thumb_size_w : $img_h;
					
					if($thumb_size_w == 0 && $thumb_size_h > 0){
						$new_w = $thumb_size_w = $new_h = $thumb_size_h;
					}elseif($thumb_size_h == 0 && $thumb_size_w > 0){
						$new_h = $thumb_size_h = $new_w = $thumb_size_w;
					}else{
						if($thumb_size_h > $thumb_size_w){ //if vertical image
							if($thumb_size_h > 0){
								$img_div = round($img_h / $thumb_size_h, 2);
								$new_h = $thumb_size_h;
							}else{
								$img_div = round($img_h / ($thumb_size_h > 0) ? $thumb_size_h : $img_w / $thumb_size_w, 2);
								$new_h = ($thumb_size_h > 0) ? $thumb_size_h : $thumb_size_h = round($img_h / $img_div);
							}
							$new_w = round($img_w / $img_div);
							if($new_w > $thumb_size_w){
								if($thumb_size_w > 0){
									//$new_w = ($thumb_size_w > 0) ? $thumb_size_w : $thumb_size_w = $new_w;
									$dst_x = "-".round(($new_w - $thumb_size_w) / 2);
								}elseif($thumb_size_w <= 0){
									$thumb_size_w = $new_w;
								}
							}elseif($new_w < $thumb_size_w){
								$img_div = round($img_w / $thumb_size_w, 2);
								$new_w = ($thumb_size_w > 0) ? $thumb_size_w : $thumb_size_w = $new_w;
								$new_h = round($img_h / $img_div);
								$dst_y = "-".round(($new_h - $thumb_size_h) / 2);
							}
						}elseif($thumb_size_w > $thumb_size_h){ //if horizontal image
							if($thumb_size_w > 0){
								$img_div = round($img_w / $thumb_size_w, 2);
								$new_w = $thumb_size_w;
							}else{
								$img_div = round($img_w / ($thumb_size_w > 0) ? $thumb_size_w : $img_h / $thumb_size_h, 2);
								$new_w = ($thumb_size_w > 0) ? $thumb_size_w : $thumb_size_w = round($img_w / $img_div);
							}
							$new_h = round($img_h / $img_div);
							if($new_h > $thumb_size_h){
								if($thumb_size_h > 0){
									//$new_h = ($thumb_size_h > 0) ? $thumb_size_h : $thumb_size_h = $new_h;
									$dst_y = "-".round(($new_h - $thumb_size_h) / 2);
								}elseif($thumb_size_h <= 0){
									$thumb_size_h = $new_h;
								}
							}elseif($new_h < $thumb_size_h){
								$img_div = round($img_h / $thumb_size_h, 2);
								$new_h = ($thumb_size_h > 0) ? $thumb_size_h : $thumb_size_h = $new_h;
								$new_w = round($img_w / $img_div);
								$dst_x = "-".round(($new_w - $thumb_size_w) / 2);
							}
						}else{
							$new_w = $thumb_size_w;
							$new_h = $thumb_size_h;
						}
					}
				
					//echo $thumb_size_w."x".$thumb_size_h." >> ";
					//echo $new_w."x".$new_h." .... ,";
				}
			}else{
				$new_w = $thumb_size_w;
				$new_h = $thumb_size_h;
			}	
		}
		
		switch($src_file_type){
			case "gif":
				$src_img = @imagecreatefromgif($src_file); 
				break;
			case "jpg":
			case "jpeg":
			case "jpe":
				$src_img = @imagecreatefromjpeg($src_file); 
				break;
			case "png":
				$src_img = @imagecreatefrompng($src_file); 
				imagealphablending($src_img, true); // setting alpha blending on
				imagesavealpha($src_img, false); // save alphablending setting (important)
				break;
			case "bmp":
				$src_img = @imagecreatefromwbmp($src_file); 
				break;
		}
		
		if($crop_resized){
			$src_w = imagesx($src_img);
			$src_h = imagesy($src_img);
		}
		
		$dst_img = @imagecreatetruecolor($thumb_size_w, $thumb_size_h) or $flag=false;
		//@imagecopyresized($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $new_w, $new_h, imagesx($src_img), imagesy($src_img)); 
		@imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $new_w, $new_h, $src_w, $src_h); 
		
		switch($dest_file_type){
			case "gif":
				//header('Content-Type: image/gif');
				@imagegif($dst_img, $dst_file); 
				break;
			case "jpg":
			case "jpeg":
			case "jpe":
				//header('Content-Type: image/jpeg');
				@imagejpeg($dst_img, $dst_file, $quality); 
				break;
			case "png":
				//header('Content-Type: image/png');
				//if($quality > 90) $quality = 90;
				@imagepng($dst_img, $dst_file, 0); 
				break;
			case "bmp":
				//header('Content-type: image/wbmp');
				@imagewbmp($dst_img, $dst_file); 
				break;
		}
		
		@imagedestroy($src_img);
		@imagedestroy($dst_img);
		
		if($flag) return basename($dst_file);
		else return $flag;
	}
	
	function CropImage($image_path, $sourse_file, $dest_file_type="jpg", $quality=100, $prefix="", $sufix="", $dest_path="", $rename=false, $coord_arr){
		if(!is_array($coord_arr)){
			return false;
			exit;
		}
	
		$flag = true;
		$src_file_type = self::getFileExts($sourse_file);
		$src_file = $image_path.$sourse_file;
		//$dst_file = str_replace(array('gif', 'jpg', 'png', 'swf', 'psd', 'bmp', 'tif', 'tif', 'jpc', 'jp2', 'jpx', 'jb2', 'swc', 'iff', 'wbmp', 'xbm'), $dest_file_type, basename($sourse_file));
		$dest_file_name = ($rename) ? mt_rand() : str_replace(".".$src_file_type, "", $sourse_file);
		$dst_file = $prefix.$dest_file_name.$sufix.".".$dest_file_type;
		$dst_file = ($dest_path == "") ? $image_path.$dst_file : $dest_path.$dst_file;
		
		list($src_w, $src_h) = @getimagesize($src_file);
	
		//$thumb_size_w = ($preview_width > 0) ? $preview_width : 236;
		//$thumb_size_h = ($preview_height > 0) ? $preview_height : 293;
		$thumb_size_w = $coord_arr[6];
		$thumb_size_h = $coord_arr[7];
	
		if(($thumb_size_w == 0) && ($thumb_size_h == 0)){
			$thumb_size_w = $src_w;
			$thumb_size_h = $src_h;
		}
	
	
		$dst_x = $coord_arr[0];
		$dst_y = $coord_arr[1];
		
		$src_x = $coord_arr[2];
		$src_y = $coord_arr[3];
		
		$dst_w = $coord_arr[6];
		$dst_h = $coord_arr[7];
		
		switch($src_file_type){
			case "gif":
				$src_img = @imagecreatefromgif($src_file); 
				break;
			case "jpg":
			case "jpeg":
			case "jpe":
				$src_img = @imagecreatefromjpeg($src_file); 
				break;
			case "png":
				$src_img = @imagecreatefrompng($src_file); 
				break;
			case "bmp":
				$src_img = @imagecreatefromwbmp($src_file); 
				break;
		}
		
		$dst_img = @imagecreatetruecolor($thumb_size_w, $thumb_size_h) or $flag=false;
		//@imagecopyresized($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $new_w, $new_h, imagesx($src_img), imagesy($src_img));
	   //imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x'], $_POST['y'], $targ_w, $targ_h, $_POST['w'], $_POST['h']); 
		@imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $src_w, $src_h); 
		
		switch($dest_file_type){
			case "gif":
				//header('Content-Type: image/gif');
				@imagegif($dst_img, $dst_file); 
				break;
			case "jpg":
			case "jpeg":
			case "jpe":
				//header('Content-Type: image/jpeg');
				@imagejpeg($dst_img, $dst_file, $quality); 
				break;
			case "png":
				//header('Content-Type: image/png');
				//if($quality > 90) $quality = 90;
				@imagepng($dst_img, $dst_file, 0); 
				break;
			case "bmp":
				//header('Content-type: image/wbmp');
				@imagewbmp($dst_img, $dst_file); 
				break;
		}
		
		@imagedestroy($src_img);
		@imagedestroy($dst_img);
		
		if($flag) return basename($dst_file);
		else return $flag;
	}

	function setFileType($type, $fileType){
		$ret = $fileType;
		if($fileType == 'auto'){
			switch($type){
				case IMAGETYPE_PNG:
					$ret = 'png';
					break;
				case IMAGETYPE_GIF:
					$ret = 'gif';
					break;
				case IMAGETYPE_JPEG:
				default:
					$ret = 'jpg';
					break;
			}
		}
		return $ret;
	}
	
	/* ------------------------ FROM PS ----------------------------- */

	/**
	  * Resize, cut and optimize image
	  *
	  * @param array $sourceFile Image object from $_FILE
	  * @param string $destFile Destination filename
	  * @param integer $destWidth Desired width (optional)
	  * @param integer $destHeight Desired height (optional)
	  *
	  * @return boolean Operation result
	  */
	function imageResize($args_arr = array()){
		$ml = ini_get('memory_limit');
		ini_set('memory_limit','512M');
		
		$sourceFile = $args_arr['sourceFile'];
		$destFile = $args_arr['destFile'];
		$destWidth = (isset($args_arr['destWidth']) && $args_arr['destWidth'] != NULL) ? $args_arr['destWidth'] : NULL;
		$destHeight = (isset($args_arr['destHeight']) && $args_arr['destHeight'] != NULL) ? $args_arr['destHeight'] : NULL;
		$fileType = (isset($args_arr['fileType'])) ? $args_arr['fileType'] : 'jpg';
		$module_conf = $args_arr['conf'];
		if(isset($args_arr['destGenerationMethod']) && $args_arr['destGenerationMethod'] != '')
			$module_conf['generation_method'] = $args_arr['destGenerationMethod'];
		
		if (!file_exists($sourceFile))
			return false;
		list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($sourceFile);
		// If PS_IMAGE_QUALITY is activated, the generated image will be a PNG with .jpg as a file extension.
		// This allow for higher quality and for transparency. JPG source files will also benefit from a higher quality
		// because JPG reencoding by GD, even with max quality setting, degrades the image.

		$fileType = self::setFileType($type, $fileType);

		if (!$sourceWidth)
			return false;
		if ($destWidth == NULL) $destWidth = $sourceWidth;
		if ($destHeight == NULL) $destHeight = $sourceHeight;
	
		//print_r($args_arr); exit;
		$sourceImage = self::createSrcImage($type, $sourceFile);
	
		$widthDiff = $destWidth / $sourceWidth;
		$heightDiff = $destHeight / $sourceHeight;
	
		if ($widthDiff > 1 && $heightDiff > 1){
			$nextWidth = $sourceWidth;
			$nextHeight = $sourceHeight;
		}else{
			if ($module_conf['generation_method'] == 'by_height' || (!$module_conf['generation_method'] && $widthDiff > $heightDiff)){
				$nextHeight = $destHeight;
				$nextWidth = round(($sourceWidth * $nextHeight) / $sourceHeight);
				$destWidth = ($module_conf['generation_method'] == 'auto') ? $destWidth : $nextWidth;
			}else{
				$nextWidth = $destWidth;
				$nextHeight = round($sourceHeight * $destWidth / $sourceWidth);
				$destHeight = ($module_conf['generation_method'] == 'auto') ? $destHeight : $nextHeight;
			}
		}
	
		$destImage = imagecreatetruecolor($destWidth, $destHeight);
	
		// If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
		if ($fileType == 'png' && $type == IMAGETYPE_PNG){
			imagealphablending($destImage, false);
			imagesavealpha($destImage, true);	
			$transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
			imagefilledrectangle($destImage, 0, 0, $destWidth, $destHeight, $transparent);
		}else{
			$white = imagecolorallocate($destImage, 255, 255, 255);
			imagefilledrectangle($destImage, 0, 0, $destWidth, $destHeight, $white);
		}
		
		imagecopyresampled($destImage, $sourceImage, (int)(($destWidth - $nextWidth) / 2), (int)(($destHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
		
		if(isset($args_arr['watermark']) && $args_arr['watermark']['logo'] != '' && file_exists($args_arr['watermark']['logo'])){
			$destImage = self::setWatermark($destImage, $destWidth, $destHeight, $args_arr);
		}
	
		return (self::returnDestImage($fileType, $destImage, $destFile, $module_conf));
		ini_set('memory_limit',$ml);
	}


	function setWatermark($destImage, $destWidth, $destHeight, $args_arr=array()){
		$Xoffset = $Yoffset = $xpos = $ypos = 0;
		list($wm_width, $wm_height, $wm_type, $wm_attr) = getimagesize($args_arr['watermark']['logo']); 
		$wm_image = imagecreatefrompng($args_arr['watermark']['logo']);
		//$__image=imagecreatetruecolor($wm_width, $wm_height);
		
		
		/*if($wm_type == IMAGETYPE_PNG){
			//imagesavealpha($wm_image, true);
			//imageinterlace($wm_image, true);
			//imagesavealpha($wm_image, true);	
			//imagealphablending($wm_image, false);
			//$transparent = imagecolorallocatealpha($__image, 255, 255, 255, 0);
			//imagefilledrectangle($__image, 0, 0, $wm_width, $wm_height, $transparent);
			//imagealphablending($__image, true);
			//imagecopy($__image, $wm_image, 0, 0, 0, 0, $wm_width, $wm_height);
		}else{
			//$white = imagecolorallocate($wm_image, 255, 255, 255);
			//imagefilledrectangle($wm_image, 0, 0, $wm_width, $wm_height, $white);
		}*/
		
		$Xoffset = $Yoffset = intval($args_arr['watermark']['margin']);
		
		if ($args_arr['watermark']['x_align'] == "middle") $xpos = $destWidth/2 - $wm_width/2 + $Xoffset;
		if ($args_arr['watermark']['x_align'] == "left") $xpos = 0 + $Xoffset;
		if ($args_arr['watermark']['x_align'] == "right") $xpos = $destWidth - $wm_width - $Xoffset;
		if ($args_arr['watermark']['y_align'] == "middle") $ypos = $destHeight/2 - $wm_height/2 + $Yoffset;
		if ($args_arr['watermark']['y_align'] == "top") $ypos = 0 + $Yoffset;
		if ($args_arr['watermark']['y_align'] == "bottom") $ypos = $destHeight - $wm_height - $Yoffset;
		
		//imagecopymerge($destImage, $wm_image, $xpos, $ypos, 0, 0, $wm_width, $wm_height, 100);
		imagecopy($destImage, $wm_image, $xpos, $ypos, 0, 0, $wm_width, $wm_height);
		
		return $destImage;
	}
	

	function createSrcImage($type, $filename){
		switch ($type){
			case 1:
				return @imagecreatefromgif($filename);
				break;
			case 3:
				return @imagecreatefrompng($filename);
				break;
			case 2:
			default:
				return @imagecreatefromjpeg($filename);
				break;
		}
	}


	function returnDestImage($type, $ressource, $filename, $module_conf){
		$flag = false;
		switch ($type){
			case 'gif':
				$flag = imagegif($ressource, $filename);
				break;
			case 'png':
				$quality = (!isset($module_conf['image_params']['png_quality'])) ? 7 : $module_conf['image_params']['png_quality'];
				$flag = imagepng($ressource, $filename, (int)$quality);
				break;		
			case 'jpg':
			case 'jpeg':
			default:
				$quality = (!isset($module_conf['image_params']['jpg_quality'])) ? 90 : $module_conf['image_params']['jpg_quality'];
				$flag = imagejpeg($ressource, $filename, (int)$quality);
				break;
		}
		imagedestroy($ressource);
		@chmod($filename, 0664);

		if($flag) return basename($filename);
		else return $flag;
	}

	/**
	  * Cut image
	  *
	  * @param array $srcFile Image object from $_FILE
	  * @param string $destFile Destination filename
	  * @param integer $destWidth Desired width (optional)
	  * @param integer $destHeight Desired height (optional)
	  *
	  * @return boolean Operation result
	  */
	function imageCut($srcFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg', $destX = 0, $destY = 0){
		if (!isset($srcFile['tmp_name']) OR !file_exists($srcFile['tmp_name']))
			return false;
	
		// Source infos
		$srcInfos = getimagesize($srcFile['tmp_name']);
		$src['width'] = $srcInfos[0];
		$src['height'] = $srcInfos[1];
		$src['ressource'] = self::createSrcImage($srcInfos[2], $srcFile['tmp_name']);
	
		// Destination infos
		$dest['x'] = $destX;
		$dest['y'] = $destY;
		$dest['width'] = $destWidth != NULL ? $destWidth : $src['width'];
		$dest['height'] = $destHeight != NULL ? $destHeight : $src['height'];
		$dest['ressource'] = self::createDestImage($dest['width'], $dest['height']);
	
		$white = imagecolorallocate($dest['ressource'], 255, 255, 255);
		imagecopyresampled($dest['ressource'], $src['ressource'], 0, 0, $dest['x'], $dest['y'], $dest['width'], $dest['height'], $dest['width'], $dest['height']);
		imagecolortransparent($dest['ressource'], $white);
		$return = self::returnDestImage($fileType, $dest['ressource'], $destFile);
		return	($return);
	}

	function createDestImage($width, $height){
		$image = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($image, 255, 255, 255);
		imagefill($image, 0, 0, $white);
		return $image;
	}
		
}
?>
