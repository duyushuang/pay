<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class parse_php{
	private static $replace=array();
	private static $m1,$m2, $formatEcho = false;//是否格式化ECHO
	public static function start(){
		self::$m1 = '{';
		self::$m2 = '}';
		$replace = $r = $re = array();
		/*marker start*/$replace = array(0=>array(0=>'{end_clear}',1=>'<?php ob_end_clean();?>',2=>false,3=>array()),1=>array(0=>'{exit}',1=>'<?php exit();?>',2=>false,3=>array()),2=>array(0=>'{eval\\s+0}',1=>'<?php 0?>',2=>false,3=>array()),3=>array(0=>'{if 0}',1=>'<?php if(0){?>',2=>false,3=>array()),4=>array(0=>'{else}',1=>'<?php } else {?>',2=>false,3=>array()),5=>array(0=>'{elseif 0}',1=>'<?php } elseif(0) {?>',2=>false,3=>array()),6=>array(0=>'{/if}',1=>'<?php }?>',2=>false,3=>array()),7=>array(0=>'{loop 0 1 2}',1=>'<?php if(isset(0) && is_array(0)){foreach(0 as 1[=>2?]){?>',2=>false,3=>array()),8=>array(0=>'{/loop}',1=>'<?php }}?>',2=>false,3=>array()),9=>array(0=>'{js_select 0}',1=>'echo template::js_select(\'0\');',2=>true,3=>array()),10=>array(0=>'{css_select 0}',1=>'echo template::css_select(\'0\');',2=>true,3=>array()),11=>array(0=>'{date 0}',1=>'<?php echo 0>1?date(\'Y-m-d H:i:s\',0):\'\';?>',2=>false,3=>array(1=>'0')),12=>array(0=>'{teval 0}',1=>'0',2=>true,3=>array()),13=>array(0=>'{template 0}',1=>'<?php include(template::load(\'0\'));?>',2=>false,3=>array()),14=>array(0=>'{subtemplate 0}',1=>'echo template::loadsubtemplate(\'0\');',2=>true,3=>array()),15=>array(0=>'{tpl_eval 0}',1=>'0',2=>true,3=>array()),16=>array(0=>'{switch 0 1}',1=>'<?php switch(0){case 1:?>',2=>false,3=>array()),17=>array(0=>'{case 0}',1=>'<?php break;case 0:?>',2=>false,3=>array()),18=>array(0=>'{case_else}',1=>'<?php break;default:?>',2=>false,3=>array()),19=>array(0=>'{/switch}',1=>'<?php break;}?>',2=>false,3=>array()),20=>array(0=>'{html 0}',1=>'<?php echo !empty(0)?htmlspecialchars(0):\'\';?>',2=>false,3=>array()),21=>array(0=>'{block 0}',1=>'echo template::stripblock(\'0\');',2=>true,3=>array()),22=>array(0=>'{lang 0}',1=>'echo language::get(\'0\',\'index\',\'default\');',2=>true,3=>array()),23=>array(0=>'{lang2 0}',1=>'<?=language::get(0)?>',2=>false,3=>array()),24=>array(0=>'{ip 0}',1=>'<?php echo 0>1?qscms::intip(0):\'\';?>',2=>false,3=>array(1=>'0')),25=>array(0=>'{rewrite}',1=>'<?php
if(!WEB_REWRITE)echo WEB_URL_S1.\'rewrite.php?rewrite=\';
else echo WEB_URL_S2;
?>',2=>false,3=>array()),26=>array(0=>'{cut 0,1}',1=>'<?php
echo qscms::cutstr(0,1);
?>',2=>false,3=>array()),27=>array(0=>'{url 0}',1=>'<?php
echo urlencode(0);
?>',2=>false,3=>array()),28=>array(0=>'{adminForm\\s+0}',1=>'echo admin::form(\'0\');',2=>true,3=>array()),29=>array(0=>'{adminList\\s+0}',1=>'echo admin::getTplList(\'0\');',2=>true,3=>array()),30=>array(0=>'{echo 0}',1=>'<?php
 echo 0;
?>',2=>false,3=>array()),31=>array(0=>'{sub 0}',1=>'echo template::loadsubtemplate(\'0\');',2=>true,3=>array()),32=>array(0=>'{pa 0}',1=>'if($data=db::one_one(\'page_article\',\'content\',"marker=\'0\'")){
echo string::ubbDecode($data);
}',2=>true,3=>array()),33=>array(0=>'{ad 0 1}',1=>'echo background::getAd(\'0\', \'1\');',2=>true,3=>array()),34=>array(0=>'{ad2 0 1}',1=>'echo background::getAd2(\'0\', \'1\');',2=>true,3=>array(2=>'2')),35=>array(0=>'{cutstr 0,1}',1=>'<?php echo qscms::cutstr(0,1);?>',2=>false,3=>array()),36=>array(0=>'{xheditor 0,1,2,3}',1=>'echo xheditor::getEditorCode(\'1\', 2, 3, \'0\');',2=>true,3=>array()),37=>array(0=>'{for 0 1 2}',1=>'$__s = 0;
$__e = 1;
$__i = \'2\';
!$__i && $__i = \'$i\';
if (strpos(\'0\', \'$\') !== false || strpos(\'1\', \'$\') !== false) {
	echo \'<?php for(\'.$__i.\'=\'.\'0\'.\';\'.$__i.\'<=\'.\'1\'.\';\'.$__i.\'++){?>\';
} else {
	if ($__s <= $__e) {
		echo \'<?php for(\'.$__i.\'=\'.$__s.\';\'.$__i.\'<=\'.$__e.\';\'.$__i.\'++){?>\';
	} else {
		echo \'<?php for(\'.$__i.\'=\'.$__s.\';\'.$__i.\'>=\'.$__e.\';\'.$__i.\'--){?>\';
	}
}',2=>true,3=>array()),38=>array(0=>'{/for}',1=>'<?php }?>',2=>false,3=>array()),39=>array(0=>'{sql\\s+0}',1=>'echo \'<?php
$__query=db::query("\'.trim(\'0\').\'");
$_sqlList=array();
while($line=db::fetch($__query))$_sqlList[]=$line;
foreach($_sqlList as $k=>$v){
?>\';',2=>true,3=>array()),40=>array(0=>'{/sql}',1=>'<?php }?>',2=>false,3=>array()),41=>array(0=>'{plugin 0}',1=>'echo \'<?php plugins::call(\\\'\'.implode(\'\\\',\\\'\', explode(\',\', \'0\')).\'\\\');?>\';',2=>true,3=>array()),42=>array(0=>'{r}',1=>'<?php
if(!WEB_REWRITE)echo WEB_URL_S1.\'rewrite.php?rewrite=\';
else echo WEB_URL_S2;
?>',2=>false,3=>array()),43=>array(0=>'{u}',1=>'<?php echo WEB_URL_S1;?>',2=>false,3=>array()),44=>array(0=>'{?}',1=>'<?php echo WEB_REWRITE?\'?\':\'&\';?>',2=>false,3=>array()),45=>array(0=>'{date2 0}',1=>'<?php echo 0>1?date(\'Y-m-d H:i:s\',0):\'\';?>',2=>false,3=>array(1=>'0')),46=>array(0=>'{cuthtml 0,1}',1=>'<?php echo string::cuthtml(0,1);?>',2=>false,3=>array()),47=>array(0=>'{date3 0}',1=>'<?php echo date(\'Y-m-d\',0);?>',2=>false,3=>array()),48=>array(0=>'{func 0 1}',1=>'<?php 0(1);?>',2=>false,3=>array()),49=>array(0=>'{cssFile 9}',1=>'$__tmp = \'9\';
$__sp = qscms::trimExplode(";", $__tmp);
foreach ($__sp as $__v) {
	$__sp1 = qscms::trimExplode(\',\', $__v);
	!isset($__sp1[1]) && $__sp1[1] = \'qscms\';
	foreach(qscms::trimExplode(\'|\', $__sp1[0]) as $__v0) {
		echo css::get_css($__v0, $__sp1[1]);
	}
}',2=>true,3=>array(0=>'0',1=>'1')),50=>array(0=>'{jsFile 9}',1=>'$__tmp = \'9\';
$__sp = qscms::trimExplode(";", $__tmp);
foreach ($__sp as $__v) {
	$__sp1 = qscms::trimExplode(\',\', $__v);
	!isset($__sp1[1]) && $__sp1[1] = \'index\';
	foreach(qscms::trimExplode(\'|\', $__sp1[0]) as $__v0) {
		echo js::get_js($__v0, $__sp1[1]);
	}
}',2=>true,3=>array(0=>'0',1=>'1')),51=>array(0=>'{fckeditor 0,1,2}',1=>'<?php
include(qd(\'./editor/ckeditor/ckeditor.php\'));
include(qd(\'./editor/ckfinder/ckfinder.php\'));
$CKEditor = new CKEditor();
$CKEditor->basePath         = qu(\'./editor/ckeditor/\');
$CKEditor->config[\'width\']  = 1;
$CKEditor->config[\'height\'] = 2;
$CKEditor->config[\'skin\']   = \'office4\';
$CKEditor->returnOutput     = true;
CKFinder::SetupCKeditor($CKEditor, qu(\'./editor/ckfinder/\'));
$editor_html=$CKEditor->editor(\'0\', !empty($0)?$0:\'\');
echo $editor_html;
?>',2=>false,3=>array(3=>'2',4=>'2003')),52=>array(0=>'{cache 0}',1=>'<?php
if (cacheData::cacheStart(__FILE__, 0)){
?>',2=>false,3=>array()),53=>array(0=>'{/cache}',1=>'<?php
}
cacheData::cacheEnd(__FILE__);
?>',2=>false,3=>array()),54=>array(0=>'{nocache}',1=>'<?php
}
cacheData::nocacheStart(__FILE__);
?>',2=>false,3=>array()),55=>array(0=>'{/nocache}',1=>'<?php
if (cacheData::nocacheEnd(__FILE__)){
?>',2=>false,3=>array()),56=>array(0=>'{clear}',1=>'<?php qscms::ob_clean();?>',2=>false,3=>array()),57=>array(0=>'{stripslashes 0}',1=>'<?php echo stripslashes(0);?>',2=>false,3=>array()),58=>array(0=>'{++ 0}',1=>'<?php echo 0+1;?>',2=>false,3=>array(1=>'1')),59=>array(0=>'{cfg 0,1}',1=>'<?php
echo cfg::get(\'0\', \'1\');
?>',2=>false,3=>array()),60=>array(0=>'{cfg2 0,1,2}',1=>'<?php
echo cfg::get2(\'0\', \'1\');
?>',2=>false,3=>array()),61=>array(0=>'{data 0,1}',1=>'<?php
$__list = db::select(\'cms_data|cms_data_cate:id=cid\', \'content|\', "t3.ename=\'0\' and t4.ename=\'1\'");
if ($__list) echo $__list[5][content];
?>',2=>false,3=>array(3=>'0',4=>'1',5=>'0')),62=>array(0=>'{timeAfter 0}',1=>'<?php
echo time::timeDifference(0);
?>',2=>false,3=>array()),63=>array(0=>'{loopQuick 0 1 2}',1=>'$__a = \'0\';
$__k = \'1\';
$__v = \'2\';
if (!$__k) {
	$__k = \'$k\';
	$__v = \'$v\';
}
echo \'<?php foreach (\'.$__a.\' as \'.$__k.($__v ? \' => \'.$__v : \'\').\') {?>\';',2=>true,3=>array()),64=>array(0=>'{/loopQuick}',1=>'<?php
}
?>',2=>false,3=>array()),65=>array(0=>'{mpage 0}',1=>'<?php
if (!empty($total) && !empty($pageUrl) && !empty($pageStyle)) echo multipage::parse($total, $pagesize, $page, $pageUrl, $pageStyle,1,\'0\');
?>',2=>false,3=>array(1=>6)),66=>array(0=>'{ajax 0 1}',1=>'echo \'<?php
if ($var->gp_callAjax == \\\'0\\\'){
qscms::ob_clean();
\'.parse_php::parse(\'1\').\'
exit;
}
?>\';',2=>true,3=>array()),67=>array(0=>'{ajaxurl 0}',1=>'<?php
$__arr = $_GET;
$__arr[\'callAjax\'] = \'0\';
echo qscms::getUrl(\'/index.php?\').http_build_query($__arr);
unset($__arr);
?>',2=>false,3=>array()),68=>array(0=>'{v 0}',1=>'<?php
echo $var->0;
?>',2=>false,3=>array()),69=>array(0=>'{var 0}',1=>'<?php
if (isset(0)) echo 0;
?>',2=>false,3=>array()),70=>array(0=>'{qu}',1=>'<?php
echo WEB_URL_S1.WDU;
?>',2=>false,3=>array()),71=>array(0=>'{su}',1=>'<?php
echo WEB_URL_S1.WDU.\'static/\';
?>',2=>false,3=>array()),72=>array(0=>'{link 0}',1=>'if ($link = db::one(\'links\', \'*\', "marker=\'0\'")) {
	echo \'<a href="\'.$link[\'url\'].\'"\'.($link[\'target\']?\' target="_blank"\':\'\').\'>\'.$link[\'title\'].\'</a>\';
}',2=>true,3=>array()),73=>array(0=>'{linkUrl 0}',1=>'if ($link = db::one(\'links\', \'url\', "marker=\'0\'")) {
	echo $link[\'url\'];
}',2=>true,3=>array()),74=>array(0=>'{ajaxCallLoading 0 1 2}',1=>'$title = \'1\';
$title || $title = \'0\';
$postData = \'2\';
echo \'(function(){
			$(\\\'#dialog\\\').html(\\\'正在执行，请稍候…\\\');
			$("#dialog").dialog({
				title:\\\'\'.$title.\'\\\',
				modal:true,//模式窗口（显示遮罩层）
				resizable:false,//是否允许改变大小
				draggable:false,//是否可以拖动
				bgiframe: true,
				overlay: {   
						backgroundColor: \\\'100\\\',   
						opacity: 101   
					},  
				show: \\\'slide\\\',//显示样式
				width:102,
				height:103
			});
			$.ajax({
				type: \\\'\'.($postData?\'post\':\'get\').\'\\\',
				url:\\\'<?php \'.parse_php::parse(\'{ajaxurl 0}\').\'?>\\\',\'.($postData?\'				data:\'.$postData.\',\':\'\').\'
				success:function(html){
					if (html == \\\'\\\') html = \\\'执行完毕\\\';
					$(\\\'#dialog\\\').html(html);
					$("#dialog" ).dialog(\\\'option\\\', \\\'buttons\\\',{
						\\\'确定\\\':function(){
							$(this).dialog(\\\'close\\\');
						}
					});
				},
				error:function(){
					var html = \\\'执行出错，可能超时\\\';
					$(\\\'#dialog\\\').html(html);
					$("#dialog" ).dialog(\\\'option\\\', \\\'buttons\\\',{
						\\\'确定\\\':function(){
							$(this).dialog(\\\'close\\\');
						}
					});
				}
			});
		})();\';',2=>true,3=>array(100=>'#000',101=>'0.5',102=>'300',103=>'150')),75=>array(0=>'{ajaxLoading 0 1}',1=>'if (empty(template::$vars[\'ajax\'][\'callLoading\']) || template::$vars[\'ajax\'][\'callLoading\'] === false){
	echo \'<script language="javascript">$.ajaxSetup({cache:false});</script><?php \'.parse_php::parse(\'{js_select jquery_ui}
		{css_select jquery_ui}
		<div id="dialog" style="display:none">
			<div id="dialog_message"></div>
		</div>\').\'?>\';
	template::$vars[\'ajax\'][\'callLoading\'] = true;
}
echo \'<?php
if ($var->gp_callAjax == \\\'0\\\'){
qscms::ob_clean();
\'.parse_php::parse(\'1\').\'
exit;
}
?>\';',2=>true,3=>array()),76=>array(0=>'{jsStr\\s+0}',1=>'$__str = \'0\';
$__str = \'\\\'\'.qscms::addcslashes($__str).\'\\\'\';
$__str = str_replace("\\r", \'\\r\', $__str);
$__str = str_replace("\\n", \'\\n\', $__str);
$__str = parse_php::parse($__str);
echo \'<?php \'.$__str.\'?>\';
',2=>true,3=>array()),77=>array(0=>'{select\\s+0 1 2}',1=>'$__sql = trim(\'0\');
$__sql = str_replace(\'\\s\', \' \', $__sql);
$__k = \'1\';
$__v = \'2\';
if (!$__k && !$__v) {
$__k = \'$k\';
$__v = \'$v\';
}
echo \'<?php foreach(db::select(\'.$__sql.\') as \'.$__k.($__v?\' => \'.$__v:\'\').\'){?>\';',2=>true,3=>array()),78=>array(0=>'{/select}',1=>'<?php }?>',2=>false,3=>array()),79=>array(0=>'{print\\s+0}',1=>'<?php
print_r(0);
?>',2=>false,3=>array()),80=>array(0=>'{cfgCate 0}',1=>'<?php
$__datas = cfg::getCfgInfoToCate(\'0\');
if ($__datas) {
	$cate = $__datas[\'cate\'];
	$list   = $__datas[\'list\'];
	if (form::is_form_hash()) {
		if (cfg::setCfg($cate[\'id\'], $_POST)) {
			admin::show_message(\'设置成功\', NOW_URL);
		} else admin::show_message(\'设置失败！\');
	}
	include(template::load(\'sub/cfgListInfo\'));
} else {
	admin::show_message(\'该配置不存在\');
}
?>',2=>false,3=>array()),81=>array(0=>'{cssLoad 9}',1=>'$__tmp = \'9\';
$__sp = qscms::trimExplode(";", $__tmp);
foreach ($__sp as $__v) {
	$__sp1 = qscms::trimExplode(\',\', $__v);
	!isset($__sp1[1]) && $__sp1[1] = \'qscms\';
	empty(template::$vars[\'cssFile\']) && template::$vars[\'cssFile\'] = array();
	foreach(qscms::trimExplode(\'|\', $__sp1[0]) as $__v0) {
		template::$vars[\'cssFile\'][] = css::get_css($__v0, $__sp1[1]);
	}
}',2=>true,3=>array(0=>'0',1=>'1')),82=>array(0=>'{css}',1=>'if (!empty(template::$vars[\'cssFile0\'])) {
	foreach(template::$vars[\'cssFile0\'] as $code) {
		echo $code;
	}
}
if (!empty(template::$vars[\'cssFile\'])) {
	foreach(template::$vars[\'cssFile\'] as $code) {
		echo $code;
	}
}',2=>true,3=>array()),83=>array(0=>'{jsLoad 9}',1=>'$__tmp = \'9\';
$__sp = qscms::trimExplode(";", $__tmp);
foreach ($__sp as $__v) {
	$__sp1 = qscms::trimExplode(\',\', $__v);
	!isset($__sp1[1]) && $__sp1[1] = \'index\';
	empty(template::$vars[\'jsFile\']) && template::$vars[\'jsFile\'] = array();
	foreach(qscms::trimExplode(\'|\', $__sp1[0]) as $__v0) {
		template::$vars[\'jsFile\'] [] = js::get_js($__v0, $__sp1[1]);
	}
}',2=>true,3=>array(0=>'0',1=>'1')),84=>array(0=>'{js}',1=>'if (!empty(template::$vars[\'jsFile0\'])) {
	foreach(template::$vars[\'jsFile0\'] as $code) {
		echo $code;
	}
}
if (!empty(template::$vars[\'jsFile\'])) {
	foreach(template::$vars[\'jsFile\'] as $code) {
		echo $code;
	}
}',2=>true,3=>array()),85=>array(0=>'{cssLoad0 9}',1=>'$__tmp = \'9\';
$__sp = qscms::trimExplode(";", $__tmp);
foreach ($__sp as $__v) {
	$__sp1 = qscms::trimExplode(\',\', $__v);
	!isset($__sp1[1]) && $__sp1[1] = \'qscms\';
	empty(template::$vars[\'cssFile0\']) && template::$vars[\'cssFile0\'] = array();
	template::$vars[\'cssFile0\'][] = css::get_css($__sp1[0], $__sp1[1]);
}',2=>true,3=>array(0=>'0',1=>'1')),86=>array(0=>'{jsLoad0 9}',1=>'$__tmp = \'9\';
$__sp = qscms::trimExplode(";", $__tmp);
foreach ($__sp as $__v) {
	$__sp1 = qscms::trimExplode(\',\', $__v);
	!isset($__sp1[1]) && $__sp1[1] = \'index\';
	empty(template::$vars[\'jsFile0\']) && template::$vars[\'jsFile0\'] = array();
	template::$vars[\'jsFile0\'] [] = js::get_js($__sp1[0], $__sp1[1]);
}',2=>true,3=>array(0=>'0',1=>'1')),87=>array(0=>'{call 0}',1=>'<?php
echo db::one_one(\'cms_calldata\', \'content\', "marker=\'0\'");
?>',2=>false,3=>array()),88=>array(0=>'{imgUrl 0}',1=>'<?php
echo qscms::getImgUrl(\'0\');
?>',2=>false,3=>array()),89=>array(0=>'{img\\s+0}',1=>'tplfunc::imageThumb(\'0\');',2=>true,3=>array()),90=>array(0=>'{thumb 0 1}',1=>'tplfunc::imageThumbUrl(\'0\', \'1\');',2=>true,3=>array()),91=>array(0=>'{imgUrlVar 0 1 2}',1=>'<?php
echo qscms::getImgUrl(\'0\').1[\'filename\'].\'2.\'.1[\'suffix\'];
?>',2=>false,3=>array()),92=>array(0=>'{loopQuickCheck 0 1 2}',1=>'$__k = \'1\';
$__v = \'2\';
if (!$__k) {
	$__k = \'$k\';
	$__v = \'$v\';
} elseif (!$__v) {
	$__v = $__k;
	$__k = \'$__k\';
}
echo \'<?php $__count = count(0);foreach (0 as \'.$__k.($__v ? \' => \'.$__v : \'\').\') {$__end = $__count == \'.$__k.\' + 9;?>\';',2=>true,3=>array(9=>'1')),93=>array(0=>'{alert 0}',1=>'<?php
string::alert(0);
?>',2=>false,3=>array()),94=>array(0=>'{durl 0,1}',1=>'<?php
if (1 > 3) echo memory::get(\'disperse_url_\'.1).\'/\';
else echo qscms::getImgUrl(\'0\');
?>',2=>false,3=>array(3=>'0')),95=>array(0=>'{setModule 0:1}',1=>'$__name = trim(\'0\');
$__code = trim(\'1\');
template::$vars[$__name] = $__code;',2=>true,3=>array()),96=>array(0=>'{module 0 1}',1=>'$__name = trim(\'0\');
$__vars = trim(\'1\');
$__code = \'\';
isset(template::$vars[$__name]) && $__code = template::$vars[$__name];
isset(template::$vars[$__name.\'_index\']) || template::$vars[$__name.\'_index\'] = 100;
if ($__vars) {
	eval(\'$__vars = \'.$__vars.\';\');
	$__code = preg_replace(\'/\\[if (\\w+)\\](.+?)\\[\\/if\\]/se\', \'isset($__vars[\\\'$2\\\']) ? stripslashes(\\\'$3\\\') : \\\'\\\'\', $__code);
	$__code = preg_replace(\'/{(\\w+)}/se\', \'isset($__vars[\\\'$2\\\']) ? $__vars[\\\'$2\\\'] : \\\'{$2}\\\'\', $__code);
}
$__code = preg_replace(\'/\\[\\+(\\d+)\\]/se\', \'template::$vars[$__name.\\\'_index\\\']+$2\', $__code);
template::$vars[$__name.\'_index\']++;
echo \'<?php \'.parse_php::parse($__code).\' ?>\';',2=>true,3=>array(2=>1,3=>2,100=>0)),97=>array(0=>'{rand_text 9}',1=>'$__data = \'9\';
$__sp = qscms::trimExplode(\',\', $__data);
$__count = count($__sp);
switch ($__count) {
	case 1:
		echo cfg::getRand(\'webGroup\', $__sp[0]);
	break;
	case 2:
		echo cfg::getRand(\'webGroup\', $__sp[0], $__sp[1]);
	break;
	case 3:
		echo cfg::getRand(\'webGroup\', $__sp[0], $__sp[1], $__sp[2]);
	break;
}',2=>true,3=>array(0=>'0',1=>'1',2=>'2',3=>'3')),98=>array(0=>'{rand_pinyin 9}',1=>'$__data = \'9\';
$__sp = qscms::trimExplode(\',\', $__data);
$__count = count($__sp);
switch ($__count) {
	case 1:
		echo cfg::getRandPinyin(\'webGroup\', $__sp[0]);
	break;
	case 2:
		echo cfg::getRandPinyin(\'webGroup\', $__sp[0], $__sp[1]);
	break;
	case 3:
		echo cfg::getRandPinyin(\'webGroup\', $__sp[0], $__sp[1], $__sp[2]);
	break;
}',2=>true,3=>array(0=>'0',1=>'1',2=>'2',3=>'3')),99=>array(0=>'{techo 0}',1=>'echo 0;',2=>true,3=>array()),100=>array(0=>'{turl}',1=>'<?php echo $__tplUrl;?>',2=>false,3=>array()),101=>array(0=>'{cssLoadStr 0}',1=>'$__tmp = \'0\';
$__sp = qscms::trimExplode(";", $__tmp);
empty(template::$vars[\'cssFile\']) && template::$vars[\'cssFile\'] = array();
foreach ($__sp as $__v) {
	template::$vars[\'cssFile\'][] = \'<link href="\'.su($__v).\'" rel="stylesheet" type="text/css" />\';
}',2=>true,3=>array()),102=>array(0=>'{jsLoadStr 0}',1=>'$__tmp = \'0\';
$__sp = qscms::trimExplode(";", $__tmp);
empty(template::$vars[\'jsFile\']) && template::$vars[\'jsFile\'] = array();
foreach ($__sp as $__v) {
	template::$vars[\'jsFile\'][] = \'<script src="\'.su($__v).\'"></script>\';
}',2=>true,3=>array()),103=>array(0=>'{l 0}',1=>'<?php echo $var->lan->get(\'0\');?>',2=>false,3=>array()),104=>array(0=>'{ifStd 0 1}',1=>'<?php
if (isset(0) && 0 == 1){
echo \' selected="selected"\';
}
?>',2=>false,3=>array()),105=>array(0=>'{ifDbd 0 1}',1=>'<?php
if (isset(0) && 0 == 1){
echo \' disabled="disabled"\';
}
?>',2=>false,3=>array()),106=>array(0=>'{imgUrlWeb 0 1 2}',1=>'<?php
echo qscms::getImgUrl(\'0\').1[\'filename1\'].\'2.\'.1[\'suffix1\'];
?>',2=>false,3=>array()),107=>array(0=>'{for1 0 1 2}',1=>'$__s = 0;
$__e = 1;
$__i = \'2\';
!$__i && $__i = \'$i\';
if (strpos(\'0\', \'$\') !== false || strpos(\'1\', \'$\') !== false) {
	echo \'<?php for(\'.$__i.\'=\'.\'0\'.\';\'.$__i.\'>=\'.\'1\'.\';\'.$__i.\'--){?>\';
} ',2=>true,3=>array()));/*marker end*/
		foreach($replace as $k=>$v){
			$re = array();//add to 20120727
			$v[0] = str_replace('{', self::$m1, $v[0]);
			$v[0] = str_replace('}', self::$m2, $v[0]);
			$name = self::marker_name($v[0]);
			$re['name']     = $name;
			$re['s']        = $v[0];
			$re['d']        = $v[1];
			$re['r0']       = self::$m1.$re['name'].self::$m2 == $re['s'];
			!$re['r0'] && $re['s1'] = substr($re['s'], strlen($re['name']) + 1, -1);
			$re['r1']       = strpos($v[0], self::$m1.'/'.$name.self::$m2) !== false ? true : false;
			$re['output']   = $v[2] === true ? true : false;
			$re['args']     = $v[3] ? $v[3] : array();
			$r[$re['name']] = $re;
		}
		self::$replace = $r;
	}
	private static function marker_name($m){
		$len       = strlen($m);
		$name      = '';
		$at_marker = false;
		for($i = 0; $i < $len; $i++)	{
			$s=substr($m, $i, 1);
			$len2 = $len - $i - 1;
			if($at_marker) {
				if($s == self::$m2 || in_array($s, array(" ", "\r", "\n", "\t", "\\"))) {
					break;
				} else $name .= $s;
			} else {
				if($s == self::$m1) $at_marker = true;
			}
		}
		return $name;
	}
	private static function get_vars($s,$d) {
		$len1=strlen($d);
		$len2=strlen($s);
		$c='';
		$k=-1;
		$rn=array();
		for($i=0;$i<$len1;$i++){
			$l=$k==-1?$i:$k;
				$s1=substr($d,$i,1);
				$s2=substr($s,$l,1);
				if($s1!=$s2 || is_numeric($s1)) {
					if(is_numeric($s1)) {
						$n='';
						$next=-1;
						$end='';
						for($j=$i;$j<$len1;$j++){
							$s3=substr($d,$j,1);
							if(!is_numeric($s3)){
								$next=$j-1;
								$end=$s3;
								break;
							} else $n.=$s3;
						}
						$str='';
						for($j=$l;$j<$len2;$j++){
							$s3=substr($s,$j,1);
							if($end) {
								if($s3==$end)break;
								else $str.=$s3;
							} else $str.=$s3;
						}
						$rn[$n]=$str;
						if($j==$len2)break;
						else $k=$j;
						if($next==-1)$i=$len1;
						else $i=$next;
					} else {
						if($s1=='\\') {
							$c=substr($d,$i+1,1);
							switch($c) {
								case 's':
									$m=substr($d,$i+2,1);
									if($m=='+')$c.=$m;
								break;
								case 'd':
									$m=substr($d,$i+2,1);
									if($m=='+')$c.=$m;
								break;
							}
							$c='\\'.$c;
							for($j=$l;$j<$len2;$j++) {
								$s3=substr($s,$j,1);;
								switch($c) {
									case '\s':
										if(!in_array($s3,array(" ","\r","\n","\t")))break 3;
										else {
											$k=$j+1;
											break 2;
										}
									break;
									case '\s+':
										if(!in_array($s3,array(" ","\r","\n","\t"))) {
											if($j!=$l) {
												$k=$j;
												break 2;
											} else break 3;
										}
									break;
									case '\d':
										if(!is_numeric($s3))break 3;
										else {
											$k=$j+1;
											break 2;
										}
									break;
									case '\d+':
										if(!is_numeric($s3)) {
											if($j!=$l) {
												$k=$j;
												break 2;
											} else break 3;
										}
									break;
								}
							}
							$i+=strlen($c)-1;
							//echo $s1,'|',$s2,'|',$k,'<br />';
						}
					}
				} else {
					if($k!=-1)$k++;
				}
		}
		return $rn;
	}
	private static function replace_marker($str,$k,$arr){
		if(isset($arr[$k]))return $str.$k;
		else return '';
	}
	public static function replace_marker_call($vars, $ms){
		return self::replace_marker($ms[1], $ms[2], $vars);
	}
	private static function addquote($var) {
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}
	private static function css_select($css_list){
		$css_list=explode(',',$css_list);
		foreach($css_list as $css)css::select_lib($css);
		return css::output(true);
	}
	private static function js_select($js_list){
		$js_list=explode(',',$js_list);
		foreach($js_list as $js)javascript::select_lib($js);
		return javascript::output(true);
	}
	private static function replace_vars($var, $vars){
		$var = stripslashes($var);
		switch (substr($var, 0, 1)) {
			case '\'':
				$i = substr($var, 1, -1);
				return '\''.(isset($vars[$i]) ? addcslashes($vars[$i], '\'\\') : '').'\'';
			break;
			case '"':
				$i = substr($var, 1, -1);
				return '"'.(isset($vars[$i]) ? addcslashes($vars[$i], '"\\') : '').'"';
			break;
			default:
				$i = $var;
				return isset($vars[$i]) ? $vars[$i] : '';
			break;
		}
	}
	public static function replace_vars_call($vars, $matches){
		return self::replace_vars($matches[1], $vars);
	}
	public static function parse($code){
		$len=strlen($code);
		$at_marker=$at_str=$at_php=$at_var=$at_m2=$var_end=false;
		$code2=$phpcode=$m='';
		$m1=$m2=$m2A=$m2B=0;
		$var='';
		for($i=0;$i<$len;$i++){
			$s=substr($code,$i,1);
			$len2=$len-$i-1;
			if($at_php) {
				if($at_str) {
					if($s=='\\' && $len2>0 && substr($code,$i+1,1)==$m) {
						$phpcode.=$s.$m;
						$i++;
					} elseif($s==$m) {
						$phpcode.=$s;
						$at_str=false;
					} else $phpcode.=$s;
				} else {
					if(s=='\'' || $s=='"'){
						$at_str=true;
						$m=$s;
						$phpcode.=$s;
					} elseif($s=='?' && $len2>0 && substr($code,$i+1,1)=='>') {
						$i++;
						$at_php=false;
						//$phpcode=self::phpcode_trim($phpcode);
						if(!$phpcode || substr($phpcode,0,1)!='=')$phpcode=' '.$phpcode;
						$code2.=$phpcode.'?>';
					} else $phpcode.=$s;
				}
			} else {
				if($at_marker) {
					if($s==self::$m1){
						$m1++;
						$m.=$s;
					} elseif($s==self::$m2) {
						$m2++;
						/*if($m2==77){
							echo $m;
							exit;
						}*/
						if($m1==$m2) {
							$name=self::marker_name(self::$m1.$m.self::$m2);
							$re = empty(self::$replace[$name]) ? '' : self::$replace[$name];
							if($re) {
								$rs='';
								$vars=array();
								if($re['r0']) {
									$rs = $re['d'];
									if ($re['output']) {
										$vars = $re['args'];
										if ($vars) {
											$rs = preg_replace("/\[(.*?)(\d+)\?\]/e","self::replace_marker('\\1','\\2',\$vars)",$rs);
											$rs = preg_replace('/(\'\d+\'|\d+|"\d+")/e', 'self::replace_vars(\'$1\', \$vars)', $rs);
										}
										$rs = qscms::getEval($rs);
									}
								} elseif($re['r1']) {
									
								} else {
									if($vars=self::get_vars(substr($m,strlen($re['name'])),$re['s1'])){
										$rs=$re['d'];
										//$re['args']&&$vars=array_merge($vars,$re['args']);
										$re['args'] && $vars += $re['args'];
										if (PHP55) {
											$call = curry('parse_php::replace_marker_call', 2);
											$rs = preg_replace_callback("/\[(.*?)(\d+)\?\]/",$call($vars),$rs);
											$call = curry('parse_php::replace_vars_call', 2);
											$rs = preg_replace_callback('/(\'\d+\'|\d+|"\d+")/', $call($vars), $rs);
										} else {
											$rs = preg_replace("/\[(.*?)(\d+)\?\]/e","self::replace_marker('\\1','\\2',\$vars)",$rs);
											//$rs=preg_replace('/\'(\d+)\'/e', '\'\\\'\'.addcslashes(\$vars[\'$1\'], \'\\\'\\\\\\\\\').\'\\\'\'', $rs);
											//$rs=preg_replace("/(\d+)/e",'\$vars[\'$1\']',$rs);
											$rs = preg_replace('/(\'\d+\'|\d+|"\d+")/e', 'self::replace_vars(\'$1\', \$vars)', $rs);
										}
										if($re['output']){
											//eval('$rs='.$rs.';');
											$rs = qscms::getEval($rs);
										}
										//foreach($vars as $k=>$v){
										//	$rs=str_replace($k,$v,$rs);
										//}
									}
								}
								$code2.=$rs;
							} else {
								if($m==$name && substr($m,0,1)=='$' && strlen($m)>1 && (($ord=ord(substr($m,1,1)))>0 && ($ord==0x5f || ($ord>=0x41 and $ord<=0x5a) || ($ord>=0x61 and $ord<=0x7a) || ($ord>=0x7f and $ord<=0xff))))$code2.='<?='.$m.'?>';
								else $code2.=self::$m1.'<?php '.self::parse($m).'?>'.self::$m2;
							}
							$at_marker=false;
						} else $m.=$s;
					} else $m.=$s;
				} else {
					if($at_var){
						$ord=ord($s);
						if(!$var_end && ($ord==0x5f  || ($ord == 0x2d && substr($code, $i + 1, 1) == '>') || $ord == 0x3e || ($ord>=0x30 and $ord<=0x39) || ($ord>=0x41 and $ord<=0x5a) || ($ord>=0x61 and $ord<=0x7a) || ($ord>=0x7f and $ord<=0xff))){
							$var.=$s;
							if($len2==0)$code2.='<?=$'.$var.'?>';
						} else {
							if($at_m2) {
								$var.=$s;
								if ($s == ']'){
									$m2B++;
									if($m2A == $m2B){
										$at_m2=false;
										if($len2==0){
											$code2.='<?=$'.$var.'?>';
										}
									}
								} elseif ($s == '[') {
									$m2A++;
								}
								
							} else {
								if($s=='[') {
									$var_end = true;
									$at_m2 = true;
									$var.=$s;
									$m2A = 1;
									$m2B = 0;
								} elseif ($s == '-' && $len2 > 1 && substr($code, $i + 1, 1) == '>') {
									$i++;
									$var .= '->';
									$var_end = false;
									$at_m2 = false;
								} else {
									$code2.='<?=$'.$var.'?>';
									$at_var=$at_m2=false;
									$i--;
								}
							}
						}
					} else {
						if($len2>3 && $s=='<' && substr($code,$i+1,1)=='?') {
							/*$_s = substr($code, $i + 2, 1);
							if (in_array($_s, array(' ', "\r", "\n", "\t"))) {
								$at_php = true;
								$phpcode = '';
								$code2 .= '<?' . $_s;
								$i += 2;
							} else {
								$_f = strpos($code, ' ', $i + 2);
								$_str = substr($code, $i + 2, $_f - 1);
								if ($_str == 'php') {
									$at_php = true;
									$phpcode = '';
									$code2 .= '<?php ';
									$i = $_f + 1;
								} else $code2 .= $s;
							}*/
							$at_php=true;
							$phpcode='';
							$code2.='<?';
							$i++;
							if(substr($code,$i+1,3)=='php'){
								$code2.='php';
								$i+=3;
							}
						} elseif($s==self::$m1) {
							$m1=1;
							$m2=0;
							$m='';
							$at_marker=true;
						} elseif($s=='$') {
							if($len2>0) {
								$ord=ord(substr($code,$i+1,1));
								if($ord==0x5f || ($ord>=0x41 and $ord<=0x5a) || ($ord>=0x61 and $ord<=0x7a) || ($ord>=0x7f and $ord<=0xff)){
									$at_var=true;
									$var='';
									$var_end=false;
								} else $code2.=$s;
							} else $code2.=$s;
						} else $code2.=$s;
					}
				}
			}
		}
		$code2=preg_replace("/<\?\=(.+?)\?>/",'<?php echo $1;?>',$code2);
		$code2=preg_replace('/\?>(\s*)<\?php/','echo \'$1\';',$code2);
		$code2=self::format_phpcode($code2);
		if (self::$formatEcho) {
			$code2 = str_replace(';echo ', ',', $code2);
		}
		return $code2;
	}
	public static function format_phpcode($code,$start=0){
		$rn='';
		$code=str_replace("\r\n","\n",$code);
		if(($fa=strpos($code,'<?',$start))!==false){
			if($fa>$start) {
				//$rn.='echo \''.str_replace('\\\\\'','\\\\\\\'',str_replace('\'','\\\'',substr($code,$start,$fa-$start))).'\';';
				$rn.='echo \''.addcslashes(substr($code,$start,$fa-$start),'\'\\').'\';';
			}
			$len=strlen($code);
			$ignore=false;
			$atstr=false;
			$s_m='';//标记
			$str1=$str2=0;
			$is_echo=substr($code,$fa+2,1)=='='?true:false;
			$phpcode='';
			for($i=$fa;$i<$len;$i++){
				if(!$atstr){
					$s=substr($code,$i,1);
					if($s=='\''||$s=='"') {
						$s_m=$s;
						$atstr=true;
						$str1=$i;
						$phpcode.=$s;
					} elseif($s=='/') {
						if($i+1<$len){
							$s=substr($code,$i+1,1);
							if($s=='/') {
								if(($fb=strpos($code,"\n",$i+2))!==false){
									//echo substr($code,$i,$fb-$i),'<br />';//斜杠注释
									$i=$fb;
								}
							} elseif($s=='*') {
								if(($fb=strpos($code,"*/",$i+2))!==false){
									//echo substr($code,$i,$fb+2-$i),'<br />';//星号注释
									$i=$fb+1;
								}
							} else $phpcode.='/';
						}
					} elseif($s=='?' && $i+1<$len && substr($code,$i+1,1)=='>') {
						$phpcode.='?>';
						//echo $phpcode;exit;
						//$phpcode=substr($code,$fa,$i+2-$fa);
						$phpcode=substr($phpcode,2,strlen($phpcode)-4);
						if($is_echo){
							$phpcode='echo '.substr($phpcode,1).';';
						} else {
							if(substr($phpcode,0,3)=='php')$phpcode=substr($phpcode,3);
							//$phpcode=preg_replace('/\/\*.*?\*\//s','',$phpcode);
							//$phpcode=preg_replace("/\/\/.*?\n/",'',$phpcode);
						}
						$phpcode = str_replace('echo \'\';', '', $phpcode);//去除echo '';
						$phpcode=self::phpcode_trim($phpcode);
						$rn.=$phpcode;
						if($i+2<$len)$rn.=self::format_phpcode($code,$i+2);
						break;
					} else {
						$phpcode.=$s;
						/*if(!$ignore||!in_array($s,array(' ',"\t","\r","\n"))) {
							$phpcode.=$s;
							$ignore=false;
							if(in_array($s,array(';','{','}',',','+','-','*','/','?','|','&','=')))$ignore=true;
						} else {
							if(!$ignore)$phpcode.=$s;
						}*/
					}
				} else {
					$s=substr($code,$i,1);
					$phpcode.=$s;
					/*if($s=='\\'){
						if($i+1<$len) {
							$s2=substr($code,$i+1,1);
							if($s2==$s_m) {
								$phpcode.=$s_m;
								$i++;
							}
						}
					} elseif($s==$s_m) {
						$str2=$i;
						$atstr=false;
					}*/
					if($s=='\\'&& $i+1<$len && in_array(substr($code,$i+1,1),array($s_m,'\\'))==$s_m) {
						$phpcode.=substr($code,$i+1,1);
						$i++;
					} elseif($s==$s_m) {
						$str2=$i;
						$atstr=false;
						//echo substr($code,$str1,$str2-$str1+1),'<br />';
					}
				}
			}
		} else $rn.='echo \''.addcslashes(substr($code,$start),'\'\\').'\';';
		return $rn;
	}
	public static function phpcode_trim($code){
		$code=str_replace("\r\n","\n",$code);
		$len=strlen($code);
		$atstr=false;
		$ignore=false;
		$s_m='';//标记
		$phpcode='';
		for($i=0;$i<$len;$i++){
			if(!$atstr){
				$s=substr($code,$i,1);
				if($s=='\''||$s=='"') {
					$s_m=$s;
					$atstr=true;
					$phpcode.=$s;
					$ignore=false;
				} else {
					if(!$ignore||!in_array($s,array(' ',"\t","\r","\n"))) {
						if($s=='/' && $i+1!=$len) {
							switch(substr($code,$i+1,1)){
								case '/':
									if(($fa=strpos($code,"\n",$i+2))!==false) {
										$i=$fa;
									}
								break;
								case '*':
									if(($fa=strpos($code,"*/",$i+2))!==false) {
										$i=$fa+1;
									}
								break;
								default :
									$phpcode.=$s;
									$ignore = true;
								break;
							}
						} else {
							$phpcode.=$s;
							$ignore=false;
							if(in_array($s,array(';','{','}',',','+','-','*','/','?',':','|','&','=','>','<')))$ignore=true;
						}
					} else {
						if(!$ignore)$phpcode.=$s;
					}
				}
			} else {
				$s=substr($code,$i,1);
				$phpcode.=$s;
				$s2=substr($code,$i+1,1);
				if($s=='\\'&& $i+1<$len && ($s2=='\\' || $s2==$s_m)) {
					$phpcode.=$s2;
					$i++;
				} elseif($s==$s_m) {
					$atstr=false;
				}
			}
		}
		$code=strrev($phpcode);
		$phpcode='';
		$len=strlen($code);
		$atstr=false;
		$ignore=false;
		$s_m='';//标记
		$phpcode='';
		for($i=0;$i<$len;$i++){
			if(!$atstr){
				$s=substr($code,$i,1);
				if($s=='\''||$s=='"') {
					$s_m=$s;
					$atstr=true;
					$phpcode.=$s;
					$ignore=false;
				} else {
					if(!$ignore||!in_array($s,array(' ',"\t","\r","\n"))) {
						$phpcode.=$s;
						$ignore=false;
						if(in_array($s,array(';','{','}',',','+','-','*','/','?',':','|','&','=','>','<')))$ignore=true;
					} else {
						if(!$ignore)$phpcode.=$s;
					}
				}
			} else {
				$s=substr($code,$i,1);
				$phpcode.=$s;
				if($s==$s_m && $i+1<$len && substr($code,$i+1,1)=='\\') {
					$phpcode.='\\';
					$i++;
				} elseif($s==$s_m) {
					$atstr=false;
				}
			}
		}
		$phpcode=trim(strrev($phpcode));
		return $phpcode;
	}
	private static function replaceArray($str){
		$str = stripslashes($str);
		if (substr($str, 0, 1) == '\'' && substr($str, -1, 1) == '\'') return $str;
		if (substr($str, 0, 1) == '"' && substr($str, -1, 1) == '"') return $str;
		if (substr($str, 0, 1) == '$') return $str;
		if (preg_match('/^[1-9]\d*$/', $str)) return $str;
		if (strpos($str, '\'.$') !== false) return $str;
		if (strpos($str, '".$') !==false) return $str;
		if (preg_match('/\s*[a-zA-z_]+\(.*?\)\s*/s', $str)) return $str;
		return '\''.qscms::addcslashes($str).'\'';
	}
	public static function replaceArray_call($ms){
		return '['.self::replaceArray($ms[1]).']';
	}
	public static function formatArray($code){
		if ( ($FA = strpos($code, '<?')) !==false ) {
			$rn = '';
			$offset = 0;
			$len = strlen($code);
			while ( (($FA = strpos($code, '<?', $offset)) !==false) ) {
				$rn .= substr($code, $offset, $FA - $offset);//获取HTML代码
				$FA += 2;
				if (substr($code, $FA, 3) == 'php') {
					$rn.='<?php';
					$FA += 3;
				} else {
					$rn.='<?';
				}
				$inStr = $inVar = $inArr = $varEnd = $end = false;
				$lastChar = $char = $strQuote = $var = '';
				$i = $m1 = $m2 = 0;
				for ($i = $FA; $i < $len; $i++) {
					$char = substr($code, $i, 1);
					if ($i + 1 == $len) $end = true;
					$len2 = $len - $i - 1;
					if ($inStr) {
						$rn .= $char;
						if ($char == $strQuote && ($lastChar != '\\' || substr($code, $i - 2, 1) == '\\' && substr($code, $i - 3, 1) != '\\')) $inStr = false;
					} elseif ($inVar) {
						$ord = ord($char);
						if(!$varEnd && ($ord == 0x5f || $ord == 0x2d || $ord == 0x3e || ($ord>=0x30 and $ord<=0x39) || ($ord>=0x41 and $ord<=0x5a) || ($ord>=0x61 and $ord<=0x7a) || ($ord>=0x7f and $ord<=0xff))){
							$var .= $char;
							if($end) $rn .= '$'.$var;
						} else {
							if($inArr) {
								$var .= $char;
								if ($char == ']'){
									$m2++;
									if($m1 == $m2){
										$inArr = false;
										if ($end) {
											if (PHP55) {
												$var = preg_replace_callback('/\[([^\[\]]+)\]/', array('parse_php', 'replaceArray_call'), $var);
											} else {
												$var = preg_replace('/\[([^\[\]]+)\]/e', '\'[\'.self::replaceArray(\'$1\').\']\'', $var);
											}
											$rn .= '$'.$var;
										}
									}
								} elseif ($char == '[') {
									$m1++;
								}
								
							} else {
								if($char == '[') {
									$varEnd = true;
									$inArr = true;
									$var .= $char;
									$m1 = 1;
									$m2 = 0;
								} elseif ($char == '-' && $len2 > 1 && substr($code, $i + 1, 1) == '>') {
									$i++;
									$var .= '->';
									$varEnd = false;
									$inArr = false;
								} else {
									if (PHP55) {
										$var = preg_replace_callback('/\[([^\[\]]+)\]/', array('parse_php', 'replaceArray_call'), $var);
									} else {
										$var = preg_replace('/\[([^\[\]]+)\]/e', '\'[\'.self::replaceArray(\'$1\').\']\'', $var);
									}
									$rn .= '$'.$var;
									$inVar = $inArr = false;
									$i--;
								}
							}
						}
					} else {
						if (in_array($char, array('\'', '"'))) {
							$inStr = true;
							$strQuote = $char;
							$rn .= $char;
						} elseif ($char == '$') {
							if(!$end) {
								$ord = ord(substr($code, $i + 1, 1));
								if($ord == 0x5f || ($ord>=0x41 and $ord<=0x5a) || ($ord>=0x61 and $ord<=0x7a) || ($ord>=0x7f and $ord<=0xff)){
									$inVar = true;
									$varEnd = false;
									$var = '';
								} else $rn .= $char;
							} else $rn .= $char;
						} elseif ($char == '?') {
							$rn .= $char;
							if (!$end) {
								if (substr($code, $i + 1, 1) == '>') {
									$i += 2;
									$rn .= '>';
									break;
								}
							}
						} else $rn .= $char;
					}
					$lastChar = $char;
				}
				$offset = $i;
				//$FB = strpos($code, '? >', $offset);
				//$offset = $FB + 2;
				
			}
			$rn .= substr($code, $offset);
			return $rn;
		} else {
			return $code;
		}
	}
}
parse_php::start();
?>