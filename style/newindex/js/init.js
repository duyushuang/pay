var indexInit = null;
var serviceList=null;
var projectlist=null;
 $(document).ready(function(){
 	var windowSize = {
 		width:$(window).width(),
 		height:$(window).height()
 	}
 	//首页
 	indexInit = new initIndex(windowSize.width,windowSize.height);
 	indexInit.init();
 	//服务页面
 	serviceList = new initService(windowSize);
 	serviceList.init();
 	//项目页面
 	projectlist = new changeProjectSize(windowSize.width,windowSize.height);
 	projectlist.init(projectlist);
 	//合作
 
 	//关于页面
 	var contactList = new initAbout(windowSize.height);
 	contactList.changeAboutTop(contactList);

 	$(window).resize(function(){
 		windowSize.width = $(window).width();
 		windowSize.height = $(window).height();

 		//首页
 		indexInit.resize(windowSize.width,windowSize.height);
 		indexInit.locationLight();
 		//服务页面
 		serviceList.wheight = windowSize.height;
 		serviceList.wwidth = windowSize.width;
 		serviceList.changeSize();
 		//项目页面
 	 	projectlist.wwidth = windowSize.width;
 	 	projectlist.wheight = windowSize.height;
 		projectlist.changeSize(projectlist);
 		//关于页面
 		contactList.wheight = windowSize.height;
	 	contactList.changeAboutTop(contactList);
 	});
 	
 	

 	

 });

//首页相关内容
function initIndex(width,height){
	this.wheight = height;
	this.wwidth = width;
	this.test = 10;
	this.slideTimer = null;
	this.condition = {
		imgWidth : 1920,
		imgHeight : 740,
		left:64,
		top:32,
		maxDegX:4,
		maxDegY:12,
		bannerTime:10000,
		fadeTime:800
	};
	//定位banner1
	var o = this;
	this.resize = function(w,h) {
		o.wheight = h;
		o.wwidth = w;
	}
	this.locationLight = function(){
		o.wwidth = o.wwidth<1280?1280:o.wwidth;
		var left = o.condition.left ;
		var top = o.condition.top;
	/*	if((o.wwidth/o.wheight) == (o.condition.imgWidth / o.condition.imgHeight)){
			
		}else if((o.wwidth/o.wheight) > (o.condition.imgWidth / o.condition.imgHeight)){
			left = (o.condition.left-50)*o.condition.imgWidth*o.wheight/
			(o.condition.imgHeight*o.wwidth)+50;
			
		}else {
			bottom =(o.condition.bottom*o.wwidth*o.condition.imgHeight)/
			(o.condition.imgWidth*o.wheight);
			
		}*/
		left =(0.5+(left/100-0.5)*(o.wheight/800))*o.wwidth;
		top = o.wheight*top/100+60;
		$('.banner1-light').css({
				'top':top+'px',
				'left':left+'px'
			});
	}
	
	this.banner1Init = function(){
		o.locationLight();

	}
	var flag = false;//作为切换一个banner的标识，切换动画还在进行中为true
	//banner之间的切换
	this.changeBanner = function(i){
		flag = true;
		var oldI = $('#mslider #btn-bar>li.active').index();
		$('#mslider #btn-bar>li.active').removeClass('active');
		$('#mslider #btn-bar>li:eq('+i+')').addClass('active');
		$('#mslider .slider-list>li:eq('+oldI+')').stop();

		$('#mslider .slider-list>li:eq('+oldI+')').css('z-index',2);
		$('#mslider .slider-list>li:eq('+i+')').css('z-index',3);
		$('#mslider .slider-list>li:eq('+i+')').fadeIn(o.condition.fadeTime,function(){
			$('#mslider .slider-list>li:eq('+oldI+')').hide();
			$('#mslider .slider-list>li:eq('+oldI+')').css('z-index',1);
			$('#mslider .slider-list>li:eq('+oldI+')').removeClass('active');
			$('#mslider .slider-list>li:eq('+i+')').addClass('active');
			flag = false;
			
		});
	}
	//设置自动播放的定时器
	this.setSlideTimer = function(){
		if(o.slideTimer != null) window.clearInterval(o.slideTimer);
		o.slideTimer = window.setInterval(function(){
			var i = $('#mslider #btn-bar>li.active').index()+1;
			i = i>=$('#mslider #btn-bar>li').length?0:i;
			o.changeBanner(i);
		},o.condition.bannerTime);
		 
		
	}
	this.init = function(){
		//右边菜单隐藏
		$('#btnlistpar').hide();
		//初始化banner第一屏
		o.banner1Init();

		$('.img-cont').parallax({
			calibrateX: true,
			calibrateY: true,
			scalarX: 7,
			scalarY: 5,
			frictionX: 0.1,
			frictionY: 0.1,
			originX: 0.5,
			originY: 0.5
		});
		//隐藏右侧滚动按钮
		$('.btnlistpar').hide();
		$('#mslider .banner1').addClass('active');
		//当banner只有一个不需要一下操作
		if($('.slider-list>li').length<2){
			
			return false;
		}
		//添加相应按钮与左右箭头
		for(var i =0;i<$('.slider-list>li').length;i++){
			$('#mslider #btn-bar').append('<li><span></span></li>');
		}
		$('#mslider #btn-bar>li:first').addClass('active');
		//绑定相应按钮的方法
		$('#mslider #btn-bar>li').on('click',function(){
			if($(this).hasClass('active') || flag){
				return false;
			}
			o.changeBanner($(this).index());

		});
		$('#mslider .left-btn').on('click',function(){
			if(flag){
				return false;
			}
			var i = $('#mslider #btn-bar>li.active').index()-1<0?$('#mslider #btn-bar>li').length-1:$('#mslider #btn-bar>li.active').index()-1;
			o.changeBanner(i);
		});
		$('#mslider .right-btn').on('click',function(){
			if(flag){
				return false;
			}
			var i = $('#mslider #btn-bar>li.active').index()+1 >= $('#mslider #btn-bar>li').length?0:$('#mslider #btn-bar>li.active').index()+1;
			o.changeBanner(i);
		});
		//设定计时器
		o.setSlideTimer();
		//按钮悬浮停止轮播
		$('#mslider').find('.left-btn,.right-btn,#btn-bar>li').on('mouseover',function(){
			window.clearInterval(o.slideTimer);
		});
		$('#mslider').find('.left-btn,.right-btn,#btn-bar>li').on('mouseout',function(){
			o.setSlideTimer();
		});
		$('#mslider').on('mousemove',function(e){
			e = e||event;
 			var x=e.clientX;  
            var y=e.clientY;
            if((x < 100 || x > o.wwidth-100) && (y >100 && y<o.wheight-100)){
            	$('#mslider').find('.left-btn,.right-btn').show();
            }else{
            	$('#mslider').find('.left-btn,.right-btn').hide();
            }
		});
		
	}
	this.loadBanner = function(){
		var i = $('#mslider #btn-bar>li.active').index();
		$('#mslider').find('.left-btn,.right-btn,#btn-bar>li').on('mouseout',function(){
			o.setSlideTimer();
		});
		$('#mslider .slider-list>li:eq('+i+')').addClass('active');
		o.setSlideTimer();
	}
	this.back = function(){
		$('#mslider').find('.left-btn,right-btn,#btn-bar>li').off('mouseout');
		window.clearInterval(o.slideTimer);
		var i = $('#mslider #btn-bar>li.active').index();
		if(flag){
			$('#mslider .slider-list>li').not($('#mslider .slider-list>li:eq('+i+')')).css({
				'z-index':1,
				'display':'none'
			});
			if(!$('#mslider .slider-list>li:eq('+i+')').hasClass('active')){
				return false;
			}
		}
		$('#mslider .slider-list>li:eq('+i+')').removeClass('active');
	}
	
}
//服务页面
 function initService(windowSize){
 	var o =this;
 	var appFlag = false;
 	this.wheight = windowSize.height;
 	this.wwidth = windowSize.width;
 	this.computer = {
 		computerFootHeight : 70,
 		servicePagePaddingBottom : 20,
 		computerBorderWidth : 40,
 		computerOuterside : 2,
 		listBoderWidth : 2,
 		listColNum : 3,
 		listBlockHeight : 90,
 		minPadding : 5,
 		nomalPadding : 10,
 		maxPadding : 35
 	};//初始化computer相关数据
 	this.condition ={
 		listNum : 3
 	}
 	
 	this.serviceListHeight = function(wheight,sbottom){
 		return wheight+$('#mslider').height()-$('#service-page').offset().top - sbottom;

 	}
 	this.serviceContHeight = function(){
 		return o.wheight+$('#mslider').height()-$('#servicelist').offset().top;

 	}
 	this.changeListSize = function(padding,height){
 		$('.service-block').css('padding',padding+'px 0');
 		$('.service-list').css('height',height+padding*2+'px');
 	}
 	this.computerHeight = function(obj) {
 		var servicePageBottom = obj.computer.computerFootHeight 
	 		+ obj.computer.servicePagePaddingBottom 
	 		+ obj.computer.computerBorderWidth 
	 		+ obj.computer.computerOuterside;
 		var servicePageBottomShort = obj.computer.servicePagePaddingBottom 
	 		+ obj.computer.computerBorderWidth 
	 		+ obj.computer.computerOuterside;
 		var padding = ((obj.serviceListHeight(obj.wheight,servicePageBottom)
 			- obj.computer.listBoderWidth) / obj.computer.listColNum
 		    - obj.computer.listBlockHeight) / 2;
 		if(padding < obj.computer.maxPadding && padding > obj.computer.nomalPadding){
 			obj.changeListSize(padding,obj.computer.listBlockHeight);
 		}else if(padding > obj.computer.maxPadding ){
 			obj.changeListSize(obj.computer.maxPadding,obj.computer.listBlockHeight);
 			$('.computer').css({
 				'bottom' : '0',
 				'position':'absolute'
 			});
 		}else{
 			padding = ((obj.serviceListHeight(obj.wheight,servicePageBottomShort)
 			- obj.computer.listBoderWidth) / obj.computer.listColNum
 		    - obj.computer.listBlockHeight) / 2;
 		    if(padding > obj.computer.nomalPadding) {
 		    	obj.changeListSize(obj.computer.nomalPadding,obj.computer.listBlockHeight);
 		    }else if(padding > obj.computer.minPadding){
 		    	obj.changeListSize(padding,obj.computer.listBlockHeight);
 		    }else{
 		    	obj.changeListSize(obj.computer.minPadding,obj.computer.listBlockHeight);
 		    }
 		}
 	}
 	this.changeSize = function(){
 		o.computerHeight(o);
 		var w = o.wwidth<1280?1280:o.wwidth;
 		$('#mservice .sevice-container>ul').css('width',w*o.condition.listNum+'px');
 		$('#mservice .sevice-container>ul>li').css('width',w+'px');
 		$('#mservice .sevice-container>ul').css('left',-$('#mservice .tab-bar>a').index($('#mservice .tab-bar>a.active'))*o.wwidth+'px');
 		//$('#mservice .sevice-container>ul>li').css('height',o.serviceContHeight()+'px');

 	}
 	this.goTo = function(l){
 		$("#mservice .sevice-container>ul").animate({ 
		    left:l+'px'
		  }, 500 );
 	}
 	this.appListScoll = null;
 	this.terminalListScoll = null;
 	this.titleListScoll = null;
 	this.appInit = function(){
 		$('#applist').cxScroll({
 			auto:false,
 			prevBtn: false,        
    		nextBtn: false,
    		gotofn: function(){
    			
    			
    			
    		}
 		}, function(api){ 
		  o.appListScoll = api; 
		});
		$('#terminallist').cxScroll({
 			auto:false,
 			prevBtn: false,        
    		nextBtn: false
 		}, function(api){ 
		  o.terminalListScoll = api; 
		});
		$('#titlelist').cxScroll({
 			auto:false,
 			prevBtn: false,        
    		nextBtn: false,
    		direction: 'bottom'
 		}, function(api){ 
		  o.titleListScoll = api; 
		});
		
		$('#app-btn a').on('click',function(){
			if($(this).hasClass('active')) return;
			if(appFlag) return;
			appFlag = true;
			var old = $('#app-btn a.active').attr('data-href');
			var n = $(this).attr('data-href');
			switch(n){
				case 't1':
				if(old == 't2'){
					$('#applist li').each(function(){
	    				$(this).attr('data-index',(parseInt($(this).attr('data-index'))+5)%6);
	    			});
					o.appListScoll.prev('',240);
					if($('#terminallist').hasClass($('#applist li[data-index="0"]').attr('data-terminal'))) {
    				return;
    			}

    			$('#terminallist').attr('class','terminal-cont '+$('#applist li[data-index="0"]').attr('data-terminal'));
					o.terminalListScoll.prev();
					o.titleListScoll.prev();
					
				}else if(old == 't3') {
					$('#applist li').each(function(){
	    				$(this).attr('data-index',(parseInt($(this).attr('data-index'))+1)%6);
	    			});
					o.appListScoll.next('',543);
					if($('#terminallist').hasClass($('#applist li[data-index="0"]').attr('data-terminal'))) {
    				return;
    			}

    			$('#terminallist').attr('class','terminal-cont '+$('#applist li[data-index="0"]').attr('data-terminal'));
					o.terminalListScoll.next();
					o.titleListScoll.next();
				}
				break;
				case 't2':
				if(old == 't3'){
					$('#applist li').each(function(){
	    				$(this).attr('data-index',(parseInt($(this).attr('data-index'))+5)%6);
	    			});
					o.appListScoll.prev('',240);
					if($('#terminallist').hasClass($('#applist li[data-index="0"]').attr('data-terminal'))) {
    				return;
    			}

    			$('#terminallist').attr('class','terminal-cont '+$('#applist li[data-index="0"]').attr('data-terminal'));
					o.terminalListScoll.prev();
					o.titleListScoll.prev();
				}else if(old == 't1') {
					$('#applist li').each(function(){
	    				$(this).attr('data-index',(parseInt($(this).attr('data-index'))+1)%6);
	    			});
					o.appListScoll.next('',240);
					if($('#terminallist').hasClass($('#applist li[data-index="0"]').attr('data-terminal'))) {
    				return;
    			}

    			$('#terminallist').attr('class','terminal-cont '+$('#applist li[data-index="0"]').attr('data-terminal'));
					o.terminalListScoll.next();
					o.titleListScoll.next();
				}
				break;
				case 't3':
				if(old == 't1'){
					$('#applist li').each(function(){
	    				$(this).attr('data-index',(parseInt($(this).attr('data-index'))+5)%6);
	    			});
					o.appListScoll.prev('',543);
					if($('#terminallist').hasClass($('#applist li[data-index="0"]').attr('data-terminal'))) {
    					return;
    				}

    				$('#terminallist').attr('class','terminal-cont '+$('#applist li[data-index="0"]').attr('data-terminal'));
					o.terminalListScoll.prev();
					o.titleListScoll.prev();
				}else if(old == 't2') {
					$('#applist li').each(function(){
	    				$(this).attr('data-index',(parseInt($(this).attr('data-index'))+1)%6);
	    			});
					o.appListScoll.next('',240);
					if($('#terminallist').hasClass($('#applist li[data-index="0"]').attr('data-terminal'))) {
    					return;
    				}

    				$('#terminallist').attr('class','terminal-cont '+$('#applist li[data-index="0"]').attr('data-terminal'));
					o.terminalListScoll.next();
					o.titleListScoll.next();
				}
				break;
			}
			if($(this).hasClass('active')){
 				return false;
 			}
 			$(this).closest('.btn-bar').find('a.active').removeClass('active');
 			$(this).addClass('active');
			setTimeout(function(){appFlag = false;console.log(appFlag);},800);

		});
 	}
 	this.init = function(){
 		o.changeSize();
 		o.appInit();
 		//初始化左上角按钮
 		$('#mservice .tab-bar>a').on('click',function(){
 			if($(this).hasClass('active')){
 				return false;
 			}
 			if($('#mservice .tab-bar>a.active').index() == 0){
 				$('.computer').removeClass('animation');
 			}else if($('#mservice .tab-bar>a.active').index() == 2){
 				var str =$('#mservice .sevice-container .btn-bar a.active').attr('data-href');
 				$('.'+str+'-cont').removeClass('active');
 			}else if($('#mservice .tab-bar>a.active').index() == 4){
	 			//stScoll.stop();
	 		}
 			$('#mservice .tab-bar>a.active').removeClass('active');
 			$(this).addClass('active');
 			//$('#mservice .sevice-container>ul').css('left',-$('#mservice .tab-bar>a').index($(this))*o.wwidth+'px');
 			o.goTo(-$('#mservice .tab-bar>a').index($(this))*o.wwidth);
 			if($(this).index() == 0){
 				setTimeout(function(){$('.computer').addClass('animation');},80);
 				
 			}else if($(this).index() == 2){
 				var str =$('#mservice .sevice-container .btn-bar a.active').attr('data-href');
 				$('.'+str+'-cont').addClass('active');
 			}else if($(this).index() == 4){
	 			//o.appListScoll.play();
	 		}
 		});
 		$('#terminal-btn a').on('click',function(){
 			if($(this).hasClass('active')){
 				return false;
 			}
 			$('.pos-container.active>div').removeClass('active');
 			$('.pos-container.active').removeClass('active');
 			$('.'+$(this).attr('data-href')+'-container').addClass('active');
 			$('.'+$(this).attr('data-href')+'-cont').addClass('active');
 			$(this).closest('.btn-bar').find('a.active').removeClass('active');
 			$(this).addClass('active');
 		});
 		
 	}
 	this.loadService = function(){
 		if($('#mservice .tab-bar>a.active').index() == 0){
 			$('.computer').addClass('animation');
 		}else if($('#mservice .tab-bar>a.active').index() == 2){
 			var str =$('#mservice .sevice-container .terminal-btn a.active').attr('data-href');
 			$('.'+str+'-cont').addClass('active');
 		}else if($('#mservice .tab-bar>a.active').index() == 4){
 			//o.appListScoll.play();
 		}
 	}
 	this.back = function(){
 		setTimeout(function(){
 			$('#mservice .sevice-container>ul').css('left','0');
 			$('#mservice .tab-bar>a:first').addClass('active');
 		},500);
 		if($('#mservice .tab-bar>a.active').index() == 0){
 			$('.animation').removeClass('animation');
 		}else if($('#mservice .tab-bar>a.active').index() == 2){
 			var str =$('#mservice .sevice-container .btn-bar a.active').attr('data-href');
 			$('.'+str+'-cont').removeClass('active');
 		}else if($('#mservice .tab-bar>a.active').index() == 4){
 			//o.appListScoll.stop();
 		}
 		$('#mservice .tab-bar>a.active').removeClass('active');
 		

 		
 		
 	}

 }

 //装图片div的大小
 function changeProjectSize(width,height){
 	var o = this;
 	this.wwidth = width<1280?1280:width;
 	this.wheight = height;
 	this.condition = {
 		navHeight : 60,
 		//headHeight : 0.109375,
 		//footHeight : 0.09375,
 		listNum : 7,
 		listScreenNum : 5.5,
 		leftWidth : 0.75,
 		infoHeight : 281,
 		headHeight :0.25,
 		headTop:0.03125,
 		footHeight:0.25,
 		footBottom:-0.03125,
 		dataValueNum:4,//需要递增数字的个数
 		scrollStep:5//左右按钮批量移动li数量

 	};
 	this.productListScoll = null;
 	this.timerList = new Array();
 	this.loadProduct = function(){
 		$('#service-data .num').each(function(){
 			var i = $(this).closest('li').index();
 			var e = $(this);
 			o.timerList.push(null);
 			var step =1;
 			if(parseInt(e.attr('data-value'))>1000){step = 11};

 			o.timerList[i] = window.setInterval(function(){
 				e.html(parseInt(e.html())+step);
 				if(parseInt(e.html())>=parseInt(e.attr('data-value'))){
 					e.html(e.attr('data-value'));
 					window.clearInterval(o.timerList[i]);
 				}
 			},2);
 		});
 		o.productListScoll.play();
 		
 	}
 	this.back = function(){
 		o.productListScoll.stop();
 	}
 	this.changeSize = function(obj){
 		obj.wwidth = obj.wwidth<1280?1280:obj.wwidth;
 		var headHeight = (o.condition.headHeight+o.condition.headTop-o.condition.headHeight/2)*o.wheight-o.condition.navHeight;
 		var footHeight = (o.condition.footHeight+o.condition.footBottom-o.condition.footHeight/2)*o.wheight;
 		$('ul.projectlist,.projectlist-container').css('height',(obj.wheight-obj.condition.navHeight
 			-headHeight-footHeight)+'px');
 		$('ul.projectlist li').css('width',obj.wwidth/obj.condition.listScreenNum+'px');
 		$('#indexPage #mproject .content .foot .data-container').css('width',obj.wwidth/obj.condition.listScreenNum*3+'px');
 		$('ul.projectlist li .info').css('margin-top',(obj.wheight-obj.condition.navHeight
 			-headHeight-footHeight-obj.condition.infoHeight)/2+'px');
 		$('.projectlist-container').css('margin-top',headHeight+'px');
 		var w = obj.wwidth/obj.condition.listScreenNum;
 		$('.projectlist-container').css({
 			'margin-left':-w*0.75+'px',
 			'width':obj.wwidth+w*0.75
 		});
 	};

 	this.init = function(obj){
 		obj.changeSize(obj);

 		
 		$('#projectlist').cxScroll({
 			auto:false
 		}, function(api){ 
		  obj.productListScoll = api; 
		});

 	}
 	

 }
 //合作页面初始化
 function initPartner(height){
 	this.wheight = height;
 	this.condition = {
 		initTime : 0,
 		betwweenTime : 100
 	};
 	var o = this;
 	this.changePartnerLoc = function(){

 	};
 	this.back = function(){
 		$('.coopration-list,#next-tip').removeClass('animation');
 	}
 	this.init =function(){
 		setTimeout(function(){
 			$('#plist1').addClass('animation')
 		},o.condition.initTime);
 		setTimeout(function(){
 			$('#plist2').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*1);
 		setTimeout(function(){
 			$('#plist3').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*2);
 		setTimeout(function(){
 			$('#plist4').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*3);
 		setTimeout(function(){
 			$('#plist5').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*4);
 		setTimeout(function(){
 			$('#plist6').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*5);
 		setTimeout(function(){
 			$('#plist7').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*6);
 		setTimeout(function(){
 			$('#plist8').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*7);
 		setTimeout(function(){
 			$('#plist9').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*8);
 		setTimeout(function(){
 			$('#plist10').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*9);
 		setTimeout(function(){
 			$('#plist11').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*10);
 		setTimeout(function(){
 			$('#plist12').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*11);
 		setTimeout(function(){
 			$('#next-tip').addClass('animation')
 		},o.condition.initTime+o.condition.betwweenTime*12);

 	};
 }
//about us居中问题
 function initAbout(height){
 	this.wheight = height;
 	this.condition = {
 		navHeight : 60,
 		headHeight : 70,
 		aboutHeight:400

 	}
 	this.changeAboutTop = function(obj){
// 		var marginTop = (obj.wheight - obj.condition.navHeight-obj.condition.headHeight -
// 			obj.condition.aboutHeight)/2;
 //		marginTop = marginTop > 0?marginTop:0;
// 		$('#contactlist').css('margin-top',marginTop);
 	}
 	this.init = function(){
 		$('.about-us,.contact').addClass('animation');
 	}
 	this.back =function(){
 		$('.about-us,.contact').removeClass('animation');
 	}
 }