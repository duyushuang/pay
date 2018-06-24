<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class tree{
	private $datas0, $pid, $datas1, $datasL, $datasR;
	public $list;
	public function __construct($datas){
		$this->datas0 = $datas;
		foreach ($this->datas0 as $v) {
			$this->datas1[$v['id']] = $v;
			$this->datasL[$v['l']]  = $v['id'];
			$this->datasR[$v['r']]  = $v['id'];
		}
		$this->list  = array();
		$this->pid   = 0;
		$this->list = $this->getList(0);
	}
	private function getList($pid){
		$list = array();
		if ($pid > 0) {
			$info = $this->datas1[$pid];
			$l = $info['l'];
			$r = $info['r'];
			if ($r - $l > 1) {
				//有子集
				$l++;
				$r--;
				$i = 0;//echo $l, '|', $r;exit;
				while (isset($this->datasL[$l])) {
					$info = $this->datas1[$this->datasL[$l]];
					$list[$info['id']]['self'] = $info;
					$list[$info['id']]['sub']  = $this->getList($info['id']);
					$l = $info['r'] + 1;
				}
			} else {
				//无子集
			}
		} else {
			$l = 1;
			while (isset($this->datasL[$l])) {
				$info = $this->datas1[$this->datasL[$l]];
				$list[$info['id']]['self'] = $info;
				$list[$info['id']]['sub']  = $this->getList($info['id']);
				$l = $info['r'] + 1;
			}
		}
		return $list;
	}
}
?>