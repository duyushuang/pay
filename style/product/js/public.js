
$(document).ready(function(){
  /*topset();*/
/*  setTimeout("ewm_hover()","800");
  ewm_leave();*/
    iesidebar(); 
    $(".ewm").addClass("v");
    $(".ewm").bind('click',function(){
      if ($(".ewm").hasClass("v")) {
        $(".ewmimg").animate({'width':'120','height':'120'},500); 
        $(".ewm").removeClass("v");
      }
      else{
        $(".ewmimg").animate({'width':'0','height':'0'},500); 
        $(".ewm").addClass("v");
      }; 
    });
     
    $(".box-btn").bind('click',function(){
      $(".leave-show").css("display","block"); 
      $(".background").fadeIn();
      $(".leave-con").animate({'top':0},500); 
    });
        //点击隐藏
    $(".close_show").bind('click',function(){
    $(".sqzs_show").slideUp("fast");  
    $(".sqzs_show").animate({'opacity':0},500); 
    $(".ckys_show").slideUp("fast"); 
    $(".ckys_show").animate({'opacity':0},500);  
    $(".background").fadeOut();
    $(".leave-con").animate({'top':-500},500); 
    $(".leave-show").css("display","none"); 
    $(".cue").text("");
    $("#entry").val("");
    });




});


/*鼠标上移显示*/
/*function ewm_hover(){
  $(".ewm").mouseover(function(){
    $(".ewm").oneTime(50,function(){  
        $(".ewmimg").animate({'width':'120','height':'120'},500); 
    });  
  });
}
function ewm_leave(){
    $(".ewm").mouseleave(function(){
      $(".ewm").stopTime(); 
      $(".ewmimg").animate({'width':'0','height':'0'},500); 
  });
}


function aqln_hover(){
    $(".aqln_menu").mouseover(function(){
      $(".aqln_menu").oneTime(50,function(){  
          $(".nav2menu").slideDown(100);
      });
      
  });
}
function aqln_leave(){
    $(".aqln_menu").mouseleave(function(){
      $(".aqln_menu").stopTime(); 
      $(".nav2menu").slideUp(100);
  });
}
*/

/*滚动条在IE下隐藏*/
function iesidebar(){
   if ($.browser.msie && ($.browser.version == "7.0")) {
            $(".topcontent").css("display", "none");
            //$(".topscroll").css("display", "none");
            $(".circlel").each(function(){
              $(this).removeClass();
            });
            $(".circler").each(function(){
              $(this).removeClass();
            });
        }
     else   if ($.browser.msie && ($.browser.version == "6.0")) {
            $(".topcontent").css("display", "none");
            $(".topscroll").css("display", "none");
            $("body").html("<b>您的浏览器太旧拉!更新现代浏览器体验会更好哦！</b>");
            $("b").css({"text-align":"center","padding-top":"60px","display":"block"});
        }
else   if ($.browser.msie && ($.browser.version == "8.0")) {
            $(".topcontent").css("display", "none");
             $(".circlel").each(function(){
              $(this).removeClass();
            });
            $(".circler").each(function(){
              $(this).removeClass();
            });
        }
}

/*获取滚动的高度*/
 function getScroll(){  
     var bodyTop = 0;    
     if (typeof window.pageYOffset != 'undefined') {    
         bodyTop = window.pageYOffset;    
     } else if (typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat') {    
         bodyTop = document.documentElement.scrollTop;    
     }    
     else if (typeof document.body != 'undefined') {    
         bodyTop = document.body.scrollTop;    
     }    
     return bodyTop  
}  



/*top的定位220*/
/*function topset(){
	scrolltotop.offset(0,216);
	scrolltotop.init(); 
}*/

