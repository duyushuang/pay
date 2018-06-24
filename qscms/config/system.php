<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */

$config = array(
	'TIME_ZONE'    => 8,//时区
	'cacheDirRoot' => './cache/',//总的缓存目录
	'cacheDirs'    => array(
		'css'    => 'css/',
		'data'   => 'data/',
		'tpl'    => 'template/',
		'tpl_m'    => 'template_m/',
		'upload' => 'upload/',
		'multipage' => 'multipage/',
		'lock'      => 'lock/',
		'download'  => 'download/',
		'html'      => 'html/',
		'thumb'     => 'thumb/',
		'program'   => 'program/'
	),
	'tplRoot'  => './template/',
	'tplRoot_m'  => './template_m/',
	'tplRoot_payment'  => './payment/',
	'tplRoot_phpqrcode'  => './phpqrcode/',
	'tplRoot_memberimg'  => './memberimg/',
	'cfgRoot'  => './config/',
	'cssRoot'  => './static/css/',
	'jsRoot'   => './static/js/',
	'imgRoot'  => './img/',
	'fileRoot' => './file/',
	'videoRoot'=> './video/',
	'taskRoot' => './task/',
	'programRoot' => './program/',
	'files' => array(
		'rewrite' => './static/data/rewrite.qscms'
	),
	'pagesize' => 20,
	'pagestyle' => '共{count}条&nbsp;&nbsp;{maxpage}页&nbsp;&nbsp;{page>minpage}<a href="{url minpage}">首 页</a>&nbsp;&nbsp;<a href="{url page-1}">上一页</a>{else}首 页&nbsp;&nbsp;上一页</a>{/page}{pages}
{select}[{page}]</a>{else}<a href="{url}">[{page}]</a>{/select}{/pages}{page<maxpage}<a href="{url page+1}">下一页</a>&nbsp;&nbsp;<a href="{url maxpage}">尾 页</a>{else}下一页&nbsp;&nbsp;尾 页{/page}',
	'newPageStyle' => '<div class="dataTables_paginate paging_bootstrap_full_number">
		<ul class="pagination" style="visibility: visible;">
			<li class="prev{page<=minpage} disabled{/page}"><a title="首页" href="{url minpage}" class="ajaxify"><i class="fa fa-angle-double-left"></i></a></li>
			<li class="prev{page<=minpage} disabled{/page}"><a title="上一页" href="{url page-1}" class="ajaxify"><i class="fa fa-angle-left"></i></a></li>
			{pages}
			{select}<li class="active disabled"><a href="{url}" class="ajaxify">{page}</a></li>
			{else}
			<li><a href="{url}" class="ajaxify">{page}</a></li>
			{/select}
			{/pages}
			<li class="next{page>=maxpage} disabled{/page}"><a title="下一页" href="{url page+1}" class="ajaxify"><i class="fa fa-angle-right"></i></a></li>
			<li class="next{page>=maxpage} disabled{/page}"><a title="尾页" href="{url maxpage}" class="ajaxify"><i class="fa fa-angle-double-right"></i></a></li>
			<li class="next"><input id="gotoPage" class="ajaxify" style="position: relative; float: left; padding: 6px 12px; margin-left: 20px; line-height: 1.42857; color: rgb(51, 122, 183); text-decoration: none; background-color: rgb(255, 255, 255); border: 1px solid rgb(221, 221, 221); width: 100px;" value="{page}">
<a title="跳转" onclick="var gotoPage = $(\'#gotoPage\').val(); if (gotoPage){ window.location.href = \'{url}&page=\'+gotoPage;}else{ $(\'#gotoPage\').focus();}" class="ajaxify">跳转</a></li>
			
		</ul>
	</div>',
	'rewrite'  => true,
	'getSyn' => array(
		'key' => '@#$AWERvdswfk445%%@#4'
	)
);
?>