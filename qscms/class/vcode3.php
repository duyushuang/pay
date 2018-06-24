<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class vcode3{
	public static $font='vcode1.ttf';
	public static function show(){
		$text = self::GetRandChar(5,1);
		$im_x = 100;
		$im_y = 40;
		/*if(function_exists('imagepng')){
			header('Content-Type: image/png');
			$img_func='imagepng';
		} elseif(function_exists('imagejpeg')){
			header('Content-Type: image/jpeg');
			$img_func='imagejpeg';
		}*/
		$im = imagecreatetruecolor($im_x,$im_y);
		//$text_c = imagecolorallocate($im, mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
		$text_c = imagecolorallocate($im, 0, 0, 0);
		$tmpC0=mt_rand(100,255);
		$tmpC1=mt_rand(100,255);
		$tmpC2=mt_rand(100,255);
		$buttum_c = imagecolorallocate($im,0xff,0xff,0xff);
		imagefill($im, 16, 13, $buttum_c);
		self::$font = s('images/fonts/'.self::$font);
		$size = 18;
		for ($i=0;$i<strlen($text);$i++){
			$tmp =substr($text,$i,1);
			$array = array(-1,1);
			$p = array_rand($array);
			$an = $array[$p]*mt_rand(1,10);//角度
			imagettftext($im, $size, $an, $i*$size + 5, 25, $text_c, self::$font, $tmp);
		}
		self::sin_image($im,2);
		//加入干扰象素;
		
	
		$rand = mt_rand(5,30);
		$rand1 = mt_rand(15,25);
		$rand2 = mt_rand(5,10);
		for ($yy=$rand; $yy<=+$rand+2; $yy++){
			for ($px=-80;$px<=80;$px=$px+0.1){
				$x=$px/$rand1;
				if ($x!=0){
					$y=sin($x);
				}
				$py=$y*$rand2;
				imagesetpixel($im, $px+80, $py+$yy, $text_c);
			}
		}
		$rsStr = '';
		for ($y = 0; $y < $im_y; $y++) {
			$str = '';
			for ($x = 0; $x < $im_x; $x++) {
				$c = imagecolorat($im, $x, $y);
				$str .= $c == $text_c ? '#' : ' ';
			}
			$rsStr && $rsStr .= "\n";
			$rsStr .= $str;
		}
		//$img_func($im);
		echo string::json_encode(array('captcha' => $rsStr));
		imagedestroy($im);
		qscms::setcookie('vcode', qscms::authcode($text));
		return $text;
	}
	public static function sin_image($im,$point=1,$A=-1,$ω=-1,$φ=-1,$k=-1){
		$img_width=imagesx($im);
		$img_height=imagesy($im);
		$img_beishu=$img_width/$img_height;
		$x0=floor($img_width/2);
		$y0=floor($img_height/2);
		$img_im=imagecreatetruecolor($img_width,$img_height);
		$color_white=imagecolorallocate($im,0xff,0xff,0xff);
		imagefill($img_im,0,0,$color_white);
		if($point==1){
			//横坐标
			$A==-1&&($A=$img_height/2)&&($A=mt_rand(0.1*$A*$img_beishu,0.2*$A*$img_beishu));
			$ω==-1&&($ω=(M_PI/($img_width/2))*1);//mt_rand(1,3)周期
			$φ==-1&&($φ=0);
			$k==-1&&($k=0);
			for($x=0;$x<$img_width;$x+=0.1){
				$setx=$x-$x0;
				$sety=$A*sin($ω*$setx+$φ)+$k;
				$moveY=$sety-(0-$y0);
				if($moveY!=0){
					for($y=0;$y<$img_height;$y++){
						$atcolor=imagecolorat($im,$x,$y);
						if($atcolor!=$color_white){
							imagesetpixel($img_im,$x,($y-$y0)+$moveY,$atcolor);
						}
					}
				}
			}
		} else {
			//纵坐标
			$A==-1&&($A=$img_width/2)&&($A=mt_rand(0.1*$A,0.2*$A));
			$A = $img_width / 2 * 0.05;
			$ω==-1&&($ω=(M_PI/($img_height/2))*1);//$ω=(M_PI/($img_height/2))*mt_rand(1,3) 波峰数
			$φ==-1&&($φ=0);
			$k==-1&&($k=0);
			for($y=0;$y<$img_height;$y+=0.1){
				$sety=$y-$y0;
				$setx=$A*sin($ω*$sety+$φ)+$k;
				$moveX=$setx-(0-$x0);
				if($moveX!=0){
					for($x=0;$x<$img_width;$x++){
						$atcolor=imagecolorat($im,$x,$y);
						if($atcolor!=$color_white){
							imagesetpixel($img_im,($x-$x0)+$moveX,$y,$atcolor);
						}
					}
				}
			}
		}
		imagecopy($im,$img_im,0,0,0,0,$img_width,$img_height);
		imagedestroy($img_im);
	}
	public static function GetRandChar($len=4,$type=7){
		$return='';
		$set_C_list=array();
		($type&1)&&($set_C_list[]=1);
		($type&2)&&($set_C_list[]=2);
		($type&4)&&($set_C_list[]=3);
		$set_C=count($set_C_list);
		$set_C>0&&($set_C--);
		for($i=0;$i<$len;$i++){
			switch($set_C_list[mt_rand(0,$set_C)]){
				case 1:
				//数字
				$return.=chr(mt_rand(0x30,0x39));
				break;
				case 2:
				//大写字母
				$return.=chr(mt_rand(0x41,0x5A));
				break;
				case 3:
				//小写字母
				$return.=chr(mt_rand(0x61,0x7A));
				break;
			}
		}
		return $return;
	}
	public static function check($code = '', $clear = true){
		$code || $code = isset($_POST['vcode']) ? $_POST['vcode'] :'';
		if (!empty($_COOKIE['vcode']) && qscms::authcode($_COOKIE['vcode'], false) == $code) {
			if ($clear) qscms::unsetcookie('vcode');
			return true;
		} else {
			return false;
		}
	}
}
?>