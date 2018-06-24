<?php
/**

 */
class html_label{
	private $innerHTML, $simple, $c, $p, $layer, $isFormat, $style;
	public $name, $datas, $childIndex;
	public function __construct($name, $innerHTML = '', $simple = false, $parent = false, $layer = 0, $isFormat = false){
		$this->name      = $name;
		$this->innerHTML = $innerHTML;
		$this->simple    = $simple;
		$this->datas     = array();
		$this->c         = false;
		$this->p         = $parent;
		$this->layer     = $layer;
		$this->isFormat  = $isFormat;
		$this->stayle    = false;
		$this->childIndex = -1;
	}
	public function addAti($key, $val = false){
		if (is_array($key) && $val === false) {
			foreach ($key as $k => $v) $this->datas[$k] = $v;
		} else {
			$this->datas[$key] = $val;
		}
		return $this;
	}
	public function reChild($name, $innerHTML, $simple = false){
		$isFind = false;
		if ($this->childIndex != -1) {
			foreach ($this->c as &$v) {
				if ($v->name == $name) {
					$v = new self($name, $innerHTML, $v->simple, $v->p, $v->layer, $v->isFormat);
					$isFind = true;
					return $v;
				}
			}
		}
		if (!$isFind) {;
			return $this->addChild($name, $innerHTML, $simple);
		}
		return $this;
	}
	public function reChild2($name, $where, $args, $innerHTML = '', $simple = false){
		$isFind = true;
		if ($this->childIndex != -1) {
			foreach ($this->c as &$v) {
				if ($v->name == $name) {
					foreach ($where as $k2 => $v2) {
						if (!isset($v->datas[$k2]) || $v->datas[$k2] != $v2) {
							$isFind = false;
						} else {
							$isFind = true;
						}
					}
					if ($isFind) {
						foreach ($args as $k2 => $v2) {
							$v->datas[$k2] = $v2;
						}
						break;
					}
				}
			}
		}
		if (!$isFind) {
			return $this->addChild($name, $innerHTML, $simple)->addAti($where + $args);
		}
		return $this;
	}
	public function addChild($name, $innerHTML = '', $simple = false){
		$this->childIndex++;
		$this->c[$this->childIndex] = new self($name, $innerHTML, $simple, $this, $this->layer + 1, $this->isFormat);
		return $this->c[$this->childIndex];
	}
	public function addParent($name, $innerHTML = '', $simple = false){
		if ($this->p) {
			return $this->p->addChild($name, $innerHTML, $simple, $this->p, $this->layer - 1, $this->isFormat);
		}
		return $this;
	}
	public function setStyle($key, $val = false){
		if (!$this->style) {
			$this->style = new css_aTb();
		}
		$this->style->add($key, $val);
		return $this;
	}
	public function getHTML(){
		$str = '';
		foreach ($this->datas as $k => $v) {
			$str && $str .= ' ';
			$str .= $k.'="'.$v.'"';
		}
		$flag1 = $this->isFormat ? str_repeat("\t", $this->layer) : '';
		$flag2 = $this->isFormat ? "\r\n" : '';
		$str = $flag1.'<'.$this->name.($str ? ' '.$str : '');
		$style = $this->style ? $this->style : '';
		$style && $str .= ' style="'.$style.'"';
		if ($this->simple) {
			$str .= ' />';
		} else {
			$str .= '>'.($this->innerHTML ? $this->innerHTML: '');
			if ($this->childIndex >= 0) {
				$str .= $flag2;
				foreach ($this->c as $v) {
					$str .= $v->getHTML().$flag2.$flag1;
				}
				//$str .= $flag1;
			}
			$str .= '</'.$this->name.'>';
		}
		return $str;
	}
	public function __toString(){
		return $this->getHTML();
	}
}
?>