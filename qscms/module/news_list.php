<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');
$cid = '';
$var->p1 != 'index' && $cid = $var->p1;
$cateList = db::select('cms_news_cate', '*', '', 'sort,id DESC');
$pagestyle = qscms::getCfgPath('/system/newPageStyle');
$pagestyle = '<nav class="pagination"><ul><li class="next-page"><a href="{url minpage}">首页</a></li>{page>minpage}<li class="next-page"><a href="{url page-1}">上一页</a></li>{/page} {pages}{select}<li class="active"><span>{page}</span></li>{else}<li><a href="{url}">{page}</a></li>{/select}{/pages}{page<maxpage}<li class="next-page"><a href="{url page+1}">下一页</a></li>{/page}<li class="next-page"><a href="{url maxpage}">尾页</a></li><li><span>共 {maxpage} 页</span></li></ul></nav>';



$list = array();
$multipage = '';
$pagesize = 4;
$count =  db::dataCount('cms_news');
$one = db::one('cms_news', '*', "top=1", 'rand()');
$hotList = db::select('cms_news', '*', '', 'clicks DESC', 4);
$wh = $cid ? "cid='$cid'" : '';
if ($keyword = $var->gp_keyword){
	if (mb_strlen($keyword) > 20) qscms::showMessage('关键词长度过长');
	$wh && $wh .= ' AND ';
	$wh .= "title like '%$keyword%' OR content like '%$keyword%'";
}
if ($total = db::dataCount('cms_news', $wh)){
	$list = db::select('cms_news', '*', $wh, 'sort,id DESC', $pagesize, $page);
	$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.($keyword ? '&keyword='.$keyword : '').'&page={page}', $pagestyle);
}
?>