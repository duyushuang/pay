$(function(){
	
	
	var slideMenu=function(o){
		var f=$("."+o.f),s=f.children("."+o.s),h=s.outerHeight();
		f.css({position:"relative"});
		s.css({height:0,opacity:0});
		f.hover(function(){
		s.show().stop(true,false).animate({height:h,opacity:1},350,function(){
			s.css({overflow:"visible"});
		});
		},function(){
			s.stop(true,false).animate({height:0,opacity:0},350,function(){
				s.hide();
			});
		});
		
	}
	// �������������Ʒ������
	slideMenu({
		f:"nav-menus",
		s:"j-categorys"
	});
	// �����Ҳ�Ĺ��ﳵ������
	slideMenu({
		f:"cart",
		s:"car_ul"

	});
	//��¼����ĵ�����
	slideMenu({
		f:"j-user-img",
		s:"j-logined"
	});
	
	
	var moveNav=function(o){
		var f=$("."+o.f),a=f.find("."+o.a);
		f.css({position:"relative"});
		var moveDiv=function(w,l,a,b){
			var div=$("<div class='move_div'></div>");
			f.append(div);
			if(b){
				div.addClass("active");
			}
			div.css({position:"absolute",left:l,width:w});
			addEvent(w,l,a,div,b);
		}
		
		var addEvent=function(w,l,a,div,b){
			a.each(function(){
				$(this).hover(function(){
					if(b){
						div.removeClass("active");
					}
					var w2=$(this).outerWidth();
					var l2=$(this).position().left;
					div.stop(true,false).animate({left:l2,width:w2});
				},function(){
					if(b){
						div.stop(true,false).animate({left:l,width:w},function(){
							div.addClass("active");
						});
					}
					else{
						div.stop(true,false).animate({left:l,width:w});
					}
				});
			});
		}
		
		a.each(function(i){
			if($(this).hasClass("channel-now")){
				var w=$(this).outerWidth();
				var l=$(this).position().left;
				if(i==0){
					moveDiv(w,l,a,true);
				}else{
					moveDiv(w,l,a);
				}
			}
		});
	}
	moveNav({
		f:"cover-page-wrapper2",
		a:"channel"
	});
});