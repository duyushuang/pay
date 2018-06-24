<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class ccharset { 	
	var $gb_map   = ''; //如果要放到別的路徑,要加上完整路徑 
	var $big5_map = ''; //例如 ="/home/table/gb.map 
	var $dep_char = 127; 
	public function __construct(){
		$this->gb_map  = s('data/gb.map');
		$this->big5_map = s('data/big5.map');
	}
	//----------------------------------------------------------------- 
	function cbig5_gb($str,$fd) {
		$c       = ord(substr($str, 0, 1)); 
		$x       = ord(substr($str, 1, 1)); 
		$address = (($c - 160) * 510) + ($x - 1) * 2; 
		fseek($fd, $address); 
		$hi=fgetc($fd);
		$lo=fgetc($fd);
		return "$hi$lo";
	}
	function cgb_big5($str,$fd) {
		$c       = ord(substr($str, 0, 1));
		$x       = ord(substr($str, 1, 1));
		$address = (($c - 160) * 510) + ($x -1 ) * 2;
		fseek($fd, $address);
		$hi = fgetc($fd); 
		$lo = fgetc($fd); 
		return "$hi$lo";
	}
	//----------------------------------------------------------------- 
	function big5_gb($str) { 
		$fd     = fopen ($this->gb_map, "r"); 
		$str    = str_replace("charset=big5", "charset=gb2312", $str);
		$outstr = "";
		for($i = 0; $i < strlen($str); $i++) { 
			$ch=ord(substr($str, $i, 1)); 
			if($ch > $this->dep_char) { 
				$outstr .= $this->cbig5_gb(substr($str, $i, 2), $fd); 
				$i++;
			} else {
				$outstr .= substr($str, $i, 1);
			} 
		} 
		fclose ($fd); 
		return $outstr; 
	} 
	//----------------------------------------------------------------- 
	function gb_big5($str) {
		$fd     = fopen ($this->big5_map, "r");
		$str    = str_replace("charset=gb2312", "charset=big5", $str); 
		$outstr = ""; 
		for($i=0;$i<strlen($str);$i++) {
			$ch=ord(substr($str,$i,1));
			if($ch > $this->dep_char) {
				$outstr.=$this->cgb_big5(substr($str,$i,2),$fd); 
				$i++;
			} else {
				$outstr .= substr($str,$i,1); 
			}
		} 
		fclose ($fd); 
		return $outstr; 
	}
	function big5($str) {
		$str = iconv(ENCODING, 'GBK', $str);
		$fd     = fopen ($this->big5_map, "r");
		$str    = str_replace("charset=gb2312", "charset=big5", $str); 
		$outstr = ""; 
		for($i=0;$i<strlen($str);$i++) {
			$ch=ord(substr($str,$i,1));
			if($ch > $this->dep_char) {
				$outstr.=$this->cgb_big5(substr($str,$i,2),$fd); 
				$i++;
			} else {
				$outstr .= substr($str,$i,1); 
			}
		} 
		fclose ($fd); 
		return iconv('BIG5', ENCODING, $outstr);
	}
} 
?>