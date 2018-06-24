
$(document).ready(function(){
		

		highline.judgeDevice();

		highline.setSize();//调整
		$(window).resize(function(){
			highline.setSize();//调整
			highline.setPosIe6();
		});

		$(window).scroll(function(){
			highline.setPosIe6();
		});
		$(document).mousewheel(function (event, num) {
			event=event||window.event;
			event.preventDefault();
			var $dom_cur=!$(event.target).hasClass("module")?$(event.target).closest(".module"):$(event.target);
			var idx=$dom_cur.index();
			if(!highline.ifScroll) return;
			var $dom=null;
			if(num<0){
				if($dom_cur.next().length>0) $dom=$dom_cur.next();
				else $dom=highline.jq_contpar.children().eq(0);
			}else{
				if($dom_cur.prev().length>0) $dom=$dom_cur.prev();
				else return;
			}		
			highline.scroll_To($dom);
		});
/*		$(document).swipe(
		{
			swipe:function(event,
			 direction, distance, duration, fingerCount) {
				alert("你用"+fingerCount+"个手指以"+duration+"秒的速度向"
				 + direction + "滑动了" +distance+ "像素 " );
			},
		});*/
		$(document).keydown(function(e){
			if(e.keyCode==40||e.which==38) e.preventDefault();
			if(e.keyCode==40) highline.triggerBtnlist(true);
			else if(e.which==38) highline.triggerBtnlist(false);
		});
		$(".btn-up").click(function(){ highline.triggerBtnlist(false); });
		$(".btn-down").click(function(){ highline.triggerBtnlist(true); });
/*
		highline.jq_btn_down.click(function(){
			highline.scroll_To($(".cont1"));
		});


		$('.s').slides({
			preload: true,
			preloadImage: 'img/loading.gif',
			//play: 1000,
			pause: 2500,
			hoverPause: true
			,generatePagination: false
		});*/


		setTimeout(function(){
			var url=window.location.href,urlArr=url.split("#@");
			if(urlArr.length>1){
				highline.jq_btnlistpar.children("a").eq(parseInt(urlArr[1])).trigger("click");
			}else{
				$(document.body).scrollTop(0);
				highline.jq_btnlistpar.children("a").eq(0).trigger("click");
			}
		},200);

});
var partner = new initPartner($(window).height());
var about = new initAbout($(window).height());

var highline={
	jq_contpar:$("#indexPage")
	,jq_btn_down:$("#btn_down")
	,jq_btnlistpar:$("#btnlistpar")
	,jq_lodpar:$("#lodpar")
	,ifScroll:1
	,moreSub:2
	,win:$(window)
	,anitime:1000
	,jq_curboxIdx:null
	,judgeDevice:function(){
		var bo=is_pc();
		if(bo) return;
		$("#btn_down").hide();
		$(".btndowntit").hide();
		highline.jq_btnlistpar.addClass("dis-none");
		$(".btn-up").show();
		$(".btn-down").show();
		$("#videopar .left,#videopar .right").css({"visibility":"hidden"});
	}
	,getWinH:function(){
		return this.win.height();
	}
	,setSize:function(){
		var w=$(window).width();
		var h=this.getWinH();
		$('html').height(h);
		$("#indexPage .module").css({"height":h+"px"});
		switch(true){
			case w<500||h<280:
			this.setClass(0);
			break;
			case w<720||h<430:
			this.setClass(1);
			break;
			case w<870||h<490:
			this.setClass(2);
			break;
			case w<1024||h<580:
			this.setClass(3);
			break;
			case w<1150||h<680:
			this.setClass(4);
			break;
			case w<1280||h<720:
			this.setClass(5);
			break;
			case w<1366||h<768:
			this.setClass(6);
			break;
			case w<1600||h<900:
			this.setClass(7);
			break;
			default:
			this.setClass(8);
			break;
		}
		if(highline.jq_curboxIdx!=null){
			$(document).scrollTop(highline.jq_contpar.children().eq(highline.jq_curboxIdx)[0].offsetTop);
		} 
	}
	,setClass:function(num){
		this.jq_contpar.attr("class","modal modal_"+num);
	}
	,scroll_To:function($obj){
		if(highline.ifScroll==0) return;
		var i=$obj.index();
		var $btnlistpar=highline.jq_btnlistpar;
		if($btnlistpar.children(".sel").index()==i) return;
		highline.ifScroll=0;
		highline.animationBack($btnlistpar.children(".sel").index());
		$btnlistpar.children(".sel").removeClass("sel").end().children("a").eq(i).addClass("sel");
		$('#nav .navitem').children(".active").removeClass('active').end().children('a').eq(i).addClass('active');
		$.scrollTo($obj, {
			duration: this.anitime,
			//easing: "easeInOutExpo",
			axis: "y",
			onAfter: function() {
				highline.setIfScrollOnAfter(i);
				if(i==0) highline.animationBtnDown();
				highline.animation_(i);
				highline.jq_curboxIdx=i;
				if(!is_pc()){
					if(highline.jq_curboxIdx==0)
						$(".btn-up").hide();
					else
						$(".btn-up").show();
				} 
			}
		});
	}
	,setIfScrollOnAfter:function(num){
		switch(num){
			case 1:
			setTimeout(function(){highline.ifScroll=1;},300);
			break;
			case 2:
			setTimeout(function(){highline.ifScroll=1;},1000);
			break;
			case 3:
			setTimeout(function(){highline.ifScroll=1;},1500);
			break;
			case 4:
			setTimeout(function(){highline.ifScroll=1;},1200);
			break;
			default:
			highline.ifScroll=1;
			break;
		}
	}
	,setPosIe6:function(){
		var h=this.jq_btnlistpar.height();
		var tScro=$(window).scrollTop();
		var hWin=$(window).height();
		this.jq_btnlistpar.attr({"style":"_top:"+(tScro+hWin/2)+"px"});
	}
	,triggerBtnlist:function(bo){
		var $dds=this.jq_btnlistpar.children("a").not("#bdshare");
		var i=this.jq_btnlistpar.children(".sel").eq(0).index();
		if(bo&&i<$dds.length-1)
			this.jq_btnlistpar.children("a").eq(i+1).trigger("click");
		else if(bo&&i==$dds.length-1)
			this.jq_btnlistpar.children("a").eq(0).trigger("click");
		else if(!bo&&i>0) 
			this.jq_btnlistpar.children("a").eq(i-1).trigger("click");
	}
	,animation_:function(num){
		switch(num){
			case 0:
				$('.btnlistpar').hide();
				$('#gotop').hide();
				//判断是否是第一次加载
				if($('.slider-list').hasClass('not-first')){
					indexInit.loadBanner();
				}else{
					$('.slider-list').addClass('not-first')
				}
				
			break;
			case 1:
				serviceList.loadService();
			break;
			case 2:
				$('.btnlistpar').hide();
				projectlist.loadProduct();
			break;
			case 3:
				partner.init();
			break;	
			case 4:
				about.init();
			break;
		}				
	}
	,animationBack:function(num){
		switch(num){
			case 0:
				$('.btnlistpar').show();
				$('#gotop').show();
				indexInit.back();
			break;
			case 1:
				serviceList.back();
			break;
			case 2:
				$('.btnlistpar').show();
				projectlist.back();
			break;
			case 3:
				partner.back();	
			break;	
			case 4:
				about.back();
			break;
		}						
	}
	,aniCom_MtopOpacity:function($obj,Mtop,Opacity,time){
		$obj.animate({
			"opacity":Opacity
			,"margin-top":Mtop
		},time);
	}
	,animationBtnDown:function(){
		highline.jq_btn_down.addClass("btn_down_animation");
		setTimeout(function(){
			highline.jq_btn_down.removeClass("btn_down_animation");
		},1000);	
		setTimeout(function(){
			highline.jq_btn_down.addClass("btn_down_animation");
		},3000);
		setTimeout(function(){
			highline.jq_btn_down.removeClass("btn_down_animation");
		},4000);	
	}
	,xManPreload:function(fnIng,fnEd){

		var count=0;
		var aImg=document.getElementsByTagName('img');
		var load=[];
		for(var i=0;i<aImg.length;i++){
			load[i]=new Image();
			load[i].onload=function(){
				count++;
				if(fnIng) fnIng(count,aImg.length);
				if(count>=aImg.length){
					if(fnEd) fnEd();
				}
			};
			load[i].src=aImg[i].src;
		}
	}
	,loadIng:function(cou,sum){
		var percent=parseInt(cou/sum*100);
		var $lodtit=$(".lodtit"),$lodpar=highline.jq_lodpar;
		var wWin=$(window).width();
		var wLodtit=$lodtit.width();
		var wStar=(wWin-wLodtit)/2;
		$lodpar.show();
		if(percent<=50){
			var w=percent/100*2*wStar;
			$(".lodleft,.lodright").css({"width":w+"px"});
			if(percent==50){
				$lodtit.addClass("lodtit_sel");
				$(".lodleft").css({
					"left":"auto"
					,"right":wWin-w+"px"
				});
				$(".lodright").css({
					"right":"auto"
					,"left":wWin-w+"px"
				});
			} 
		}else{
			var n=1-((percent-50)/100*2);
			var w=n*wStar;
			$(".lodleft,.lodright").css({"width":w+"px"});
			var scale=(percent-50)/100*2*2+1;
			$lodtit.css({
				"-webkit-transform":"scale("+scale+")"
				,"-moz-transform":"scale("+scale+")"
				,"-o-transform":"scale("+scale+")"
				,"transform":"scale("+scale+")"
				,"opacity":n
			});
		}

	}
	,loadEd:function(){
		var $lodtit=$(".lodtit"),$lodpar=highline.jq_lodpar;
		$lodpar.animate({"opacity":"0"},1000,function(){
			$(this).hide().remove();
		});

		

	}
}

function openFlinks(obj){
	var $obj=$(obj);
	var $p=$obj.parent();
	if($p.is(".flinks_sel")){
		$p.removeClass("flinks_sel");
	}else{
		$p.addClass("flinks_sel");
	}
}


