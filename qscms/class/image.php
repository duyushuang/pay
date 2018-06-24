<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
//define('ImageMagick',true);
class image{
	public static function getImage($name, $dir, $width = 0, $height = 0, $saveName = ''){
		$upload = new upload();
		$rs = $upload->toupload($name, 'image');
		if ($rs['count'] == 1) {
			!file_exists($dir) && file::createFolder($dir);
			if ($rs = $upload->move2($rs['info'][$name]['db_id'], $dir)) {
				if ($width > 0 && $height > 0) {
					$saveName || $saveName = $rs['filename'].'1';
					$saveBasename = $saveName.'.'.$rs['suffix'];
					$save = $dir.$saveBasename;
					self::thumb($rs['source'], $save, array('width' => $width, 'height' => $height));
					@unlink($rs['source']);
					return $saveBasename;
				} else {
					return $rs['filename'].'.'.$rs['suffix'];
				}
			}
		}
		return false;
	}
	public static function thumb($s,$d,$flag,$type='cutout', $ignoreSize = false){
		$ignoreSize = false;//忽略放大
		if(file_exists($s)){
			$s_info=pathinfo($s);
			$s_info['extension'] && ($s_info['extension']=strtolower($s_info['extension'])) && $s_info['extension']=='jpg' && $s_info['extension']='jpeg';
			if ($__info = @getimagesize($s)) {
				list($__type0, $__type1) = explode('/', $__info['mime']);
				if ($__type1) {
					$img_func='imagecreatefrom'.$__type1;
					$img_func2='image'.$__type1;
				} else return false;
			} else return false;
				if($type=='cutout'){
					$width = isset($flag['width']) ? $flag['width'] : 0;
					$height = isset($flag['height']) ? $flag['height'] : 0;
					$width||($width = $height);
					$width||($width = isset($flag['maxwidth']) ? $flag['maxwidth'] : 0);
					$height||($height = $width);
					if(qscms::defineTrue('ImageMagick')===true){
						self::ImagickResizeImage($s,$d,$width,$height,true);
					} else {
						@$im=$img_func($s);
						$im || ($img_func != 'imagecreatefromjpeg' && $im=imagecreatefromjpeg($s));
						if($im) {
							$pic_width  = imagesx($im);
							$pic_height = imagesy($im);
							
							/*yu edit*/
							if ($width > $pic_width) $width = $pic_width;// 如果裁剪宽度大于图片宽度 宽度就不改变
							if ($height > $pic_height) $height = $pic_height;// 如果裁剪高度大于图片高度 高度就不改变
							
							$maxwidth   = $width;
							$maxwidth < $height && ($maxwidth = $height);
							$pic_minwidth = $pic_width;
							$pic_minwidth > $pic_height && ($pic_minwidth = $pic_height);
							$b1 = $width / $height;
							$b2 = $pic_width / $pic_height;
							//echo $b1, '|', $b2, '<br />';
							if ($b1 >= 0) {
								//长大于等于高
								if ($b2 < 0 || $b1 > $b2) {
									//长度小于高度
									$create_width  = $width;
									$create_height = floor($create_width / $pic_width * $pic_height);
								} else {
									//
									$create_height = $height;
									$create_width  = floor($create_height / $pic_height * $pic_width);
								}
							} else {
								//长度小于高度
								if ($b2 >=0 || $b1 < $b2) {
									$create_height = $height;
									$create_width  = floor($create_height / $pic_height * $pic_width);
								} else {
									$create_width  = $width;
									$create_height = floor($create_width / $pic_width * $pic_height);
								}
							}
							/*$percent = floor(($maxwidth / $pic_minwidth) * 1000 + 0.5) / 1000;
							if ($percent < 1) {
								$create_width = floor($pic_width * $percent);
								$create_width < $width && ($create_width = $width);
								$create_height = floor($pic_height * $percent);
								$create_height < $height&&($create_width=$height);
							} else {
								$create_width  = $pic_width;
								$create_height = $pic_height;
							}*/
							$rsX = $rsY = 0;//剪切区域
							switch (cfg::getInt('sys', 'thumb_position')) {
								case 1:
									$rsX = $rsY = 0;
								break;
								case 2:
									$rsX = floor($create_width / 2 - $width / 2);
									$rsY = 0;
								break;
								case 3:
									$rsX = $create_width - $width;
									$rsY = 0;
								break;
								
								case 4:
									$rsX = 0;
									$rsY = floor($create_height / 2 - $height / 2);
								break;
								case 5:
									$rsX = floor($create_width / 2 - $width / 2);
									$rsY = floor($create_height / 2 - $height / 2);
								break;
								case 6:
									$rsX = $create_width - $width;
									$rsY = floor($create_height / 2 - $height / 2);
								break;
								
								case 7:
									$rsX = 0;
									$rsY = $create_height - $height;
								break;
								case 8:
									$rsX = floor($create_width / 2 - $width / 2);
									$rsY = $create_height - $height;
								break;
								case 9:
									$rsX = $create_width - $width;
									$rsY = $create_height - $height;
								break;
							}
							if (!$ignoreSize && $width <= $pic_width && $height <= $pic_height || $ignoreSize) {
								$result  = imagecreatetruecolor($create_width, $create_height);
								$result1 = imagecreatetruecolor($width, $height);
								$color=imagecolorallocate($result,255,255,255); 
								imagecolortransparent($result,$color);
								imagecolortransparent($result1,$color);
								imagefill($result,0,0,$color);
								imagefill($result1,0,0,$color);
								
								imagecopyresampled($result, $im, 0, 0, 0, 0, $create_width, $create_height, $pic_width, $pic_height);
								imagecopy($result1, $result, 0, 0, $rsX, $rsY, $create_width, $create_height);
								//imagecopyresampled($result1, $result, 0, 0, 0, 0, $width, $height, $create_width, $create_height);
								//echo $pic_width, '|', $pic_height, ';', $width, '|', $height, ';', $create_width, '|', $create_height, '<br />';
								//echo $rsX, '|', $rsY;
								$img_func2($result1,$d);
								imagedestroy($result);
								imagedestroy($result1);
							} else {
								@copy($s, $d);
							}
							imagedestroy($im);
							return true;
						}
						return false;
					}
					
				} elseif ($type == 'cutAt') {
					if(qscms::defineTrue('ImageMagick')===true){
						
					} else {
						@$im = $img_func($s);
						$im || ($img_func != 'imagecreatefromjpeg' && $im = imagecreatefromjpeg($s));
						if($im) {
							$pic_width  = imagesx($im);
							$pic_height = imagesy($im);
							$rsX = $rsY = 0;//剪切区域
							$rsX = $flag['x0'];
							$rsY = $flag['y0'];
							$create_width = $flag['x1'] - $flag['x0'] + 1;
							$create_height = $flag['y1'] - $flag['y0'] + 1;
							if (!$ignoreSize && $create_width <= $pic_width && $create_height <= $pic_height || $ignoreSize) {
								$result  = imagecreatetruecolor($create_width, $create_height);
								imagecopy($result, $im, 0, 0, $rsX, $rsY, $create_width, $create_height);
								$img_func2($result, $d);
								imagedestroy($result);
							} else {
								@copy($s, $d);
							}
							imagedestroy($im);
							return true;
						}
						return false;
					}
				} elseif($type=='zoom') {
					$width  = isset($flag['width']) ? $flag['width'] : 0;
					$height = isset($flag['height']) ? $flag['height'] : 0;
					if(qscms::defineTrue('ImageMagick') === true){
						self::ImagickResizeImage($s,$d,$width,$height,false);
					} else {
						@$im = $img_func($s);
						$im || ($img_func != 'imagecreatefromjpeg' && $im=imagecreatefromjpeg($s));
						if($im) {
							$pic_width    = imagesx($im);
							$pic_height   = imagesy($im);
							$pic_minwidth = $pic_width;
							$pic_maxwidth = $pic_height;
							if($pic_minwidth > $pic_maxwidth){
								$pic_tmpwidth = $pic_minwidth;
								$pic_minwidth = $pic_maxwidth;
								$pic_maxwidth = $pic_tmpwidth;
							}
							$minwidth = isset($flag['minwidth']) ? $flag['minwidth'] : 0;
							$maxwidth = isset($flag['maxwidth']) ? $flag['maxwidth'] : 0;
							if($minwidth){
								$percent = floor(($minwidth / $pic_minwidth) * 1000 + 0.5) / 1000;
								$width   = floor($pic_width * $percent);
								$height  = floor($pic_height * $percent);
							} elseif($maxwidth){
								$percent=floor(($maxwidth / $pic_maxwidth) * 1000 + 0.5) / 1000;
								$width  = floor($pic_width  * $percent);
								$height = floor($pic_height * $percent);
							} elseif ($width && !$height) {
								//只有宽度
								$percent = floor(($width / $pic_width) * 1000 + 0.5) / 1000;
								$height  = floor($pic_height * $percent);
							} elseif ($height && !$width) {
								//只有高度
								$percent = floor(($height / $pic_height) * 1000 + 0.5) / 1000;
								$width  = floor($pic_width * $percent);
							}
							if (!$ignoreSize && $width <= $pic_width && $height <= $pic_height || $ignoreSize) {
								$result=imagecreatetruecolor($width,$height);
								$color=imagecolorallocate($result,255,255,255); 
								imagecolortransparent($result,$color);
								imagefill($result,0,0,$color);
								imagecopyresampled($result,$im,0,0,0,0,$width,$height,$pic_width,$pic_height);
								//eval("image{$s_info[suffix]}(\$d);");
								$img_func2($result,$d);
								imagedestroy($result);
							} else {
								@copy($s, $d);
							}
							imagedestroy($im);
							return true;
						}
						return false;
					}
				}
			}
			//return true;
		//}
		return false;
	}
	public static function ImagickResizeImage($srcFile,$destFile,$new_w,$new_h, $trim=false){
		if($new_w <= 0 || $new_h <= 0 || !file_exists($srcFile))return false;
		$src = new Imagick($srcFile);
		$image_format = strtolower($src->getImageFormat()); 
		if($image_format != 'jpeg' && $image_format != 'gif' && $image_format != 'png' && $image_format != 'jpg') return false; 
		$src_page = $src->getImagePage(); 
		//如果是 bbsposts 目录里的图片文件，这加入水印 
		if(strpos($destFile, 'bbsposts') !== false){ 
			//先算出最终缩略图的尺寸 
			$src_w = $src_page['width']; 
			$src_h = $src_page['height']; 
			$rate_w  = $new_w / $src_w; 
			$rate_h  = $new_h / $src_h; 
			$rate    = (!$trim && $rate_w < $rate_h) || ($trim && $rate_w > $rate_h) ? $rate_w : $rate_h; 
			$rate = $rate > 1 ? 1 : $rate; 
			$thumb_w = round($src_w * $rate); 
			$thumb_h = round($src_h * $rate); 
			//确定使用对应尺寸的水印图片 
			$watermask = true; 
			if($thumb_w >= 300 && $thumb_h >= 300){ 
				$watermaskfile = "images/watermask/1.png"; 
			}else if($thumb_w >= 100 && $thumb_h >= 100){ 
				$watermaskfile = "images/watermask/2.png"; 
			}else{ 
				$watermask = false; 
				$watermaskfile = ''; 
			} 
			if($watermask){ 
				$water = new Imagick($watermaskfile); 
				$water_page = $water->getImagePage(); 
				$water_w = $water_page['width']; 
				$water_h = $water_page['height']; 
			}
		}
		  
		//如果是 jpg jpeg gif 
		if($image_format != 'gif'){ 
			$dest = $src; 
			if(!$trim){ 
			$dest->thumbnailImage($new_w, $new_h, true); 
			}else{ 
			$dest->cropthumbnailImage($new_w, $new_h); 
			} 
			if($watermask) $dest->compositeImage($water, Imagick::COMPOSITE_OVER, $dest->getImageWidth() - $water_w, $dest->getImageHeight() - $water_h); 
			  
			$dest->writeImage($destFile); 
			$dest->clear(); 
			//gif需要以帧一帧的处理 
		}else{ 
			$dest = new Imagick(); 
			$color_transparent = new ImagickPixel("transparent"); //透明色 
			foreach($src as $img){ 
				$page = $img->getImagePage(); 
				$tmp = new Imagick(); 
				$tmp->newImage($page['width'], $page['height'], $color_transparent, 'gif'); 
				$tmp->compositeImage($img, Imagick::COMPOSITE_OVER, $page['x'], $page['y']); 
				if(!$trim){ 
					$tmp->thumbnailImage($new_w, $new_h, true); 
				}else{ 
					$tmp->cropthumbnailImage($new_w, $new_h); 
				} 
				if($watermask) $tmp->compositeImage($water, Imagick::COMPOSITE_OVER, $tmp->getImageWidth() - $water_w, $tmp->getImageHeight() - $water_h); 
				$dest->addImage($tmp); 
				$dest->setImagePage($tmp->getImageWidth(), $tmp->getImageHeight(), 0, 0); 
				$dest->setImageDelay($img->getImageDelay()); 
				$dest->setImageDispose($img->getImageDispose()); 
				  
			} 
			$dest->coalesceImages(); 
			$dest->writeImages($destFile, true); 
			  
			$dest->clear(); 
		}
	}
	public static function watermark($img_file, $water_file = '', $pos = ''){
		$img_file = d($img_file);
		$allow = cfg::getBoolean('sys', 'water_allow');
		$water_file=='' && $water_file = d('./'.cfg::get('sys', 'water_img'));
		$pos == '' && $pos = cfg::getInt('sys', 'water_pos');
		$padding = 2;
		$img_info   = @getimagesize($img_file);
		$water_info = @getimagesize($water_file);
				//	echo $allow;exit;
		if($allow && $img_info && $water_info){
			if(substr($img_info['mime'],0,5) == 'image' && substr($water_info['mime'],0,5)=='image'){
				if($img_info[0] - $padding >= $water_info[0] && $img_info[1] - $padding >= $water_info[1]){
					$img_type   = substr($img_info['mime'], 6);
					$water_type = substr($water_info['mime'], 6);
					$img_c   = 'imagecreatefrom'.$img_type;
					$img_s   = 'image'.$img_type;
					$water_c = 'imagecreatefrom'.$water_type;
					$pos || $pos = 9;
					switch($pos){
						case 1:
							$pos = array($padding, $padding);
						break;
						case 2:
							$pos = array(floor($img_info[0] / 2) - floor($water_info[0] / 2), $padding);
						break;
						case 3:
							$pos = array($img_info[0] - $water_info[0] - $padding, $padding);
						break;
						case 4:
							$pos = array($padding, floor($img_info[1] / 2) - floor($water_info[1] / 2));
						break;
						case 5:
							$pos = array(floor($img_info[0] / 2) - floor($water_info[0] / 2), floor($img_info[1] / 2) - floor($water_info[1] / 2));
						break;
						case 6:
							$pos = array($img_info[0] - $water_info[0] - $padding, floor($img_info[1] / 2) - floor($water_info[1] / 2));
						break;
						case 7:
							$pos = array($padding, $img_info[1] - $water_info[1] - $padding);
						break;
						case 8:
							$pos = array(floor($img_info[0] / 2) - floor($water_info[0] / 2), $img_info[1] - $water_info[1] - $padding);
						break;
						case 9:
							$pos = array($img_info[0] - $water_info[0] - $padding, $img_info[1] - $water_info[1] - $padding);
						break;
					}
					$img_im   = $img_c($img_file);
					$water_im = $water_c($water_file);
					imagecopy($img_im, $water_im, $pos[0], $pos[1], 0, 0, $water_info[0], $water_info[1]);
					$img_s($img_im, $img_file);
					imagedestroy($img_im);
					imagedestroy($water_im);
					
					return true;
				} else return array('status' => false, 'info' => '图片尺寸小于水印图片尺寸');
			} else return array('status' => false, 'info' => '图片类型错误');
		}
	}
}
?>