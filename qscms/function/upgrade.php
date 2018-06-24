<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
function getFileHex($name = 'file'){
	$hex = '';
	if ($file = upload::getFile($name)) {
		$data = file::read($file);
		$hex = string::str_hex($data);
		@unlink($file);
	}
	return $hex;
}
function getFileHexXml($name = 'file', $saveAs = ''){
	$data = '';
	if ($rs = upload::getFileFull($name)) {
		$hex = file::read($rs['file']);
		$hex = string::str_hex($hex);
		@unlink($rs['file']);
		$data = '<file>';
		$data .= '<type>hex</type>';
		$data .= '<saveAs>'.$saveAs.$rs['name'].'.'.$rs['suffix'].'</saveAs>';
		$data .= '<data>'.$hex.'</data>';
		$data .= '<size>'.$rs['size'].'</size>';
		$data .= '</file>';
	}
	return $data;
}
function upgrade($name = 'file') {
	if ($file = upload::getFile($name, 'xml|')) {
		if ($data = file::read($file)) {
			@unlink($file);
			$xml = qscms::xmlToArray($data);
			if (!empty($xml['data'])) {
				$data = $xml['data'];
			} else return '升级数据为空';
			if (!empty($data['file'])) {
				if (empty($data['file'][0])) $data['file'] = array($data['file']);
			}
			if (!empty($data['task'])) {
				if (empty($data['task'][0])) $data['task'] = array($data['task']);
			}
			if (!empty($data['cfg'])) {
				if (empty($data['cfg'][0])) $data['cfg'] = array($data['cfg']);
			}
			if (!empty($data['tkd'])) {
				if (empty($data['tkd'][0])) $data['tkd'] = array($data['tkd']);
			}
			if (!empty($data['nav'])) {
				empty($data['nav'][0]) && $data['nav'] = array($data['nav']);
			}
			$fileList = $taskList = array();
			if (!empty($data['file'])) {
				foreach ($data['file'] as $v) {
					$file = array();
					if (empty($v['saveAs'])) return '升级包数据有错';
					$saveData = '';
					switch ($v['type']) {
						case 'hex':
							$saveData = string::hex_str(trim($v['data']));
						break;
						case 'text':
							$saveData = "\r\n/*upgrade to qscms*/\r\n".trim($v['data']);
						break;
						default:
							return '升级文件类型无法识别';
						break;
					}
					$file['type'] = $v['type'];
					$file['saveAs'] = d($v['saveAs']);
					$file['saveType'] = !empty($v['saveType']) ? $v['saveType'] : 'replace';
					$file['data'] = $saveData;
					$fileList[] = $file;
				}
			}
			if (!empty($data['task'])) {
				foreach ($data['task'] as $v) {
					$task = array();
					$task['name']     = trim($v['name']);
					$task['timeType'] = intval(trim($v['timeType']));
					$task['time']     = trim($v['time']);
					$task['type']     = intval(trim($v['type']));
					$task['data']     = trim($v['data']);
					$taskList[] = $task;
				}
			}
			if (!empty($data['cfg'])) {
				$cfgList = array();
				foreach ($data['cfg'] as $v) {
					if (!empty($v['addCate']) || !empty($v['cid'])) {
						if (!empty($v['addCfg']['item'])) {
							if (empty($v['addCfg']['item'][0])) $v['addCfg']['item'] = array($v['addCfg']['item']);
						}
						$cfgList[] = $v;
					}
				}
				foreach ($cfgList as $cfg) {
					if (!empty($cfg['addCate'])) {
						cfg::addCate($cfg['addCate']['name'], $cfg['addCate']['remark']);
					}
					if (!empty($cfg['cid'])) {
						$cid = cfg::getCateId($cfg['cid']);
						if ($cid) {
							if (!empty($cfg['addCfg']['item'])) {
								$valList = array();
								foreach ($cfg['addCfg']['item'] as $v) {
									if (!empty($v['name'])) {
										!isset($v['remark']) && $v['remark'] = '';
										!isset($v['type'])   && $v['type']   = 'text';
										!isset($v['attach']) && $v['attach'] = '';
										!isset($v['run'])    && $v['run']    = '';
										!isset($v['data'])   && $v['data']   = '';
										cfg::addCfg($cid, $v['name'], $v['type'], $v['attach'], $v['remark'], $v['run']);
										$valList[$v['name']] = $v['data'];
									}
								}
								$cfgList = cfg::getCfgs($cid, 0, 0);
								foreach ($cfgList as $k => $v) {
									if (!isset($valList[$v['name']])) {
										$valList[$v['name']] = $v['value'];
									}
								}
								if ($valList) {
									$valList = qscms::addslashes($valList);
									cfg::setCfg($cid, $valList);
								}
							} elseif (!empty($cfg['data'])) {
								if (empty($cfg['data'][0])) $cfg['data']= array($cfg['data']);
								$valList = array();
								foreach ($cfg['data'] as $v) {
									$valList[$v['name']] = trim($v['data']);
								}
								$cfgList = cfg::getCfgs($cid, 0, 0);
								foreach ($cfgList as $k => $v) {
									if (!isset($valList[$v['name']])) {
										$valList[$v['name']] = $v['value'];
									}
								}
								if ($valList) {
									$valList = qscms::addslashes($valList);
									cfg::setCfg($cid, $valList);
								}
							}
						}
					}
				}
			}
			if (!empty($data['tkd'])) {
				$setDatas = array();
				foreach ($data['tkd'] as $tkd) {
					empty($tkd['title']) && $tkd['title'] = '';
					empty($tkd['keywords']) && $tkd['keywords'] = '';
					empty($tkd['description']) && $tkd['description'] = '';
					if (!($tid = db::one_one('tkd', 'id', "marker='$tkd[marker]'"))) {
						if (!empty($tkd['item'])) {
							empty($tkd['item'][0]) && $tkd['item'] = array($tkd['item']);
							$__arr = array(
								'name'     => $tkd['name'],
								'marker'   => $tkd['marker'],
								'varType'  => array(),
								'varName'  => array(),
								'varCheck' => array()
							);
							foreach ($tkd['item'] as $k => $v) {
								$__arr['varType'][$k]  = $v['varType'];
								$__arr['varName'][$k]  = $v['varName'];
								$__arr['varCheck'][$k] = $v['varCheck'];
							}
							if (tkd::addItem($__arr)) {
								$tid = db::one_one('tkd', 'id', "marker='$tkd[marker]'");
							}
						}
					}
					if ($tid && (!empty($tkd['title']) || !empty($tkd['keywords']) || !empty($tkd['description']))) {
						$setDatas[$tid] = qscms::filterArray($tkd, array('title', 'keywords', 'description'), true);
					}
				}
				if ($setDatas) {
					$inDatas = array(
						'title'       => array(),
						'keywords'    => array(),
						'description' => array()
					);
					foreach (tkd::getList(0, 0, 'id,title,keywords,description') as $v) {
						foreach (array('title', 'keywords', 'description') as $v2) {
							$inDatas[$v2][$v['id']] = !empty($setDatas[$v['id']]) ? $setDatas[$v['id']][$v2] : $v[$v2];
							$inDatas[$v2][$v['id']] = qscms::addslashes($inDatas[$v2][$v['id']]);
						}
					}
					tkd::setData($inDatas);
				}
			}
			if (!empty($data['sql'])) {
				$sql = trim($data['sql']);
				$sql && ($sql = str_replace('{pre}', PRE, $sql)) && db::querys($sql);
			}
			if (!empty($data['nav'])) {
				foreach ($data['nav'] as $nav) {
					$nav = qscms::trim($nav);
					$name  = !empty($nav['name']) ? $nav['name'] : '';
					$alias = !empty($nav['alias']) ? $nav['alias'] : '';
					$url   = !empty($nav['url']) ? $nav['url'] : '';
					$parentAlias = !empty($nav['parentAlias']) ? $nav['parentAlias'] : '';
					if ($name && $alias) {
						if (!b_nav::exists($alias, $parentAlias) && (!$parentAlias || b_nav::exists($parentAlias))) {
							b_nav::add2($name, $alias, $parentAlias);
						}
					}
				}
			}
			//upgrade
			foreach ($fileList as $file) {
				switch ($file['saveType']) {
					case 'replace':
						file::createFolderToFile($file['saveAs']);
						file::write($file['saveAs'], $file['data']);
					break;
					case 'append last':
						if (file_exists($file['saveAs'])) {
							$text = file::read($file['saveAs']);
							if (strrpos($text, $file['data']) === false) {
								file::write($file['saveAs'], $file['data'], true);
							}
						} else {
							file::createFolderToFile($file['saveAs']);
							file::write($file['saveAs'], $file['data']);
						}
					break;
				}
			}
			if ($taskList) {
				foreach ($taskList as $task) {
					task::add($task['name'], $task['timeType'], $task['time'], $task['type'], $task['data'], 1);
				}
			}
			return true;
		} else @unlink($file);
		return '获取数据失败';
	}
	return '没有选择升级文件';
}
?>