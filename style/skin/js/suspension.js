var str = '<style type="text/css">.suspension { position: fixed; z-index: 55; right: 30px; bottom: 50px; width: 80px; height: 204px; }.suspension-box { position: relative; float: right; }.suspension-box a.a { display: block; width: 50px; height: 50px; background-color: #797979; margin-bottom: 1px; cursor: pointer; }.suspension-box a.back_top { background-color: #797979; }.suspension-box a.qq:hover,.suspension-box a.tel:hover,.suspension-box a.code:hover { opacity: 0.8; filter: alpha(opacity=80); }.suspension-box a.back_top:hover { background-color: #cc2e2e; }.suspension-box a i { display: block; float: left; width: 25px; margin-left: 15px; margin-top: 15px; background-image: url(/skin/images/suspension-bg.png); background-repeat: no-repeat; text-indent: -9999px; }.suspension-box a.qq i { background-position: 0 0; }.suspension-box a.tel i { background-position: -25px 0; }.suspension-box a.code i { background-position: 0 -25px; }.suspension-box a.back_top i { background-position: -28px -25px; }.suspension-box div.code { position: absolute; left: -187px; *left: -207px; top: 0; display: none; width: 178px; height: 198px; padding-left: 10px; padding-top: 10px; background-color: #fff; border: 1px solid #D9D9D9; }.suspension-box div.code p { width: 155px; text-align: center; }.suspension-box div.code i { width: 8px; height: 15px; position: absolute; top: 118px; right: -8px; background-image: url(/skin/images/trangle-right-suspension.png); background-repeat: no-repeat; }.suspension-box div.tencent { position: absolute; left: -187px; top: 0px; width: 177px; height: 85px; display:none; background-color: #fff; border: 1px solid #D7D7D7; }.suspension-box div.tencent p.tencent-p { width: 75px; float: right; margin-right: 18px; margin-top: 18px; font-size: 16px; color: #333; }.suspension-box div.tencent i.tencent-i { width: 8px; height: 15px; position: absolute; top: 10px; right: -8px; text-indent: -9999px; background-image: url(/skin/images/trangle-right-suspension.png); background-repeat: no-repeat; }.suspension-box div.tencent b.tencent-b { position: absolute; left: 10px; top: 10px; width: 62px; height: 64px; background-image: url(/skin/images/circle-tencent.png); background-repeat: no-repeat; }.suspension-box div.tencent span.tencent-span { position: absolute; left: 8px; top: 7px; border-radius: 22px; width: 45px; height: 48px; text-align: center; z-index: 99; overflow: hidden; }.suspension-box div.tencent .tencent-qq {position: absolute; left: 3px;top:0px; height: 45px; width: 39px; }.suspension-box div.tel { position: absolute; left: -187px; top: 51px; display: none; width: 177px; height: 85px; background-color: #fff; border: 1px solid #D7D7D7; }.suspension-box div.tel i { width: 8px; height: 15px; position: absolute; top: 20px; right: -8px; background-image: url(/skin/images/trangle-right-suspension.png); background-repeat: no-repeat; }.suspension-box div.tel strong { display: block; margin-left: 17px; margin-top: 16px; font-size: 16px; font-weight: normal; }.suspension-box div.tel p { margin-left: 17px; font-size: 23px; font-family: arial; color: #FD696D; }.icon { margin-top:-5px; }.nav a.cart-title-name { color: #000; text-decoration: none; line-height: normal; }.nav a.cart-title-name:hover { color: #ef353d; text-decoration: underline; line-height: normal; }.nav a font { padding: 8px 15px; background:#fd7878; border-radius: 5px; }.nav a font:hover { padding: 8px 15px; background:#ea5050; border-radius: 5px; }</style><div class="suspension"><div class="suspension-box"><a href="//wpa.qq.com/msgrd?v=3&uin=983322908&site=qq&menu=yes" target="_blank" class="qq j-icon a"><i>qq</i></a><a data-type="iframe" title="联系客服" href="/plus/list.php?tid=89" class="venoboxframe vbox-item a tel j-icon"><i>tel</i></a><a class="code j-icon a"><i>code</i></a><a class="back_top j-back-top a"><i>back_top</i></a><div class="tencent j-tencent j-box"><a href="//wpa.qq.com/msgrd?v=3&uin=983322908&site=qq&menu=yes" target="_blank"><i class="tencent-i">tencent</i><b class="tencent-b"><span class="tencent-span"><img class="tencent-qq" src="/skin/images/qq-tencent.png" alt=""></span></b><p class="tencent-p">在线客服<br>点击交谈</p></a></div><div class="tel j-box"><strong>定制服务，专业的一对一服务！</strong><p> </p><i></i></div><div class="code j-box"><img src="/skin/images/jinmiyun-code.png" alt=""><p>在手机上看</p><i></i></div></div></div>';
//加载字符串
$("body").append(str);
// 效果
$(".suspension").find(".j-icon").mouseover(
    function(){
        $(".suspension").find(".j-box").eq($(this).index()).show().siblings(".j-box").hide();
    }
);
//判断ie浏览器
	
	if(navigator.appVersion.match(/11./i) == '11.' || navigator.appVersion.match(/10./i) == '10.') {
		$(".suspension").css("right","30px");
	}

$(".suspension .j-back-top").hide()

$(window).scroll(function(){
    if($(window).scrollTop() > 100){
        $(".suspension .j-back-top").fadeIn()
    }else {
        $(".suspension .j-back-top").fadeOut()
    }
});
$(".j-tencent").hover(function(){
            $(".tencent-qq").stop(true,false).animate({width:"51",height:"59",left:"-3"});
        },function(){
            $(".tencent-qq").stop(true,false).animate({width:"39",height:"45",left:"3"});
        });
// 效果
$(".suspension").mouseleave(function(event){
    event.preventDefault();
    $(".suspension").find(".j-box").hide();
});

$(".j-back-top").click(function(){
    $("html,body").animate({scrollTop:0},500);
});