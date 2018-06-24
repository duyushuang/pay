
$(document).ready(function(){
  val();
  cur();
    jQuery("#slideBox1").slide({mainCell:"#bd1 ul",effect:"left",trigger:"click",easing:"easeInBack",delayTime:1000,pnLoop:false,prevCell:"#prev1",nextCell:"#next1"});
    jQuery("#slideBox2").slide({mainCell:"#bd2 ul",effect:"left",trigger:"click",easing:"easeInBack",delayTime:1000,pnLoop:false,prevCell:"#prev2",nextCell:"#next2"});
    jQuery("#slideBox3").slide({mainCell:"#bd3 ul",effect:"left",trigger:"click",easing:"easeInBack",delayTime:1000,pnLoop:false,prevCell:"#prev3",nextCell:"#next3"});
    jQuery("#slideBox21").slide({mainCell:"#bd21 ul",effect:"left",trigger:"click",easing:"easeInBack",delayTime:1000,pnLoop:false,prevCell:"#prev21",nextCell:"#next21"});
    jQuery("#slideBox22").slide({mainCell:"#bd22 ul",effect:"left",trigger:"click",easing:"easeInBack",delayTime:1000,pnLoop:false,prevCell:"#prev22",nextCell:"#next22"});
    jQuery("#slideBox23").slide({mainCell:"#bd23 ul",effect:"left",trigger:"click",easing:"easeInBack",delayTime:1000,pnLoop:false,prevCell:"#prev23",nextCell:"#next23"});
    $(window).scroll(function(){
        var top = getScroll();
        if(top <=385 ){
        }
        if(top > 385){
        }
    });
   $(".relbox").removeClass("show");
   $(".relbox[rel='"+2+"']").addClass("show"); 
    $(".relbox[rel='"+22+"']").addClass("show"); 
    $(".ckys").bind("click",function(){
         $(".ckys_show").animate({'opacity':1},500); 
         $(".ckys_show").slideDown("fast");
    });
    $(".sqzs").bind("click",function(){
         $(".sqzs_show").animate({'opacity':1},500);
         $(".sqzs_show").slideDown("fast"); 
    });

});
function val(){
  $("#search").bind("click",function(){/*
    var isAddress=([w-]+.)+[w-]+(/[w-./?%&=]*)?;*/
    var vl=$.trim($("#entry").val());
   if (vl=='') {
      $(".cue").text("请输入查询的内容");
   }
  /* else if (!isAddress.test(vl)) {
      $(".cue").text("请输入正确的域名哦！");
   }*/
   else{
      var query = {};
      query.domain = vl;
      $.ajax({
        url:"/https://auth.tenwang.net/?url=",
        type:"get",
        dataType:"jsonp",
        data:query,
        success:function(obj){
          if (obj.auth==1) {
             $(".cue").text("您的域名已授权");
          }
          else{
            $(".cue").text("您的域名还未授权哦");
          }
        }
      });
   }
 
  });
}


/*选择的选项卡*/
function cur(){
$(".section1 .s4img").bind("click",function(){
    $(".section1 .s4img").removeClass("imgcur");
   $(this).addClass("imgcur");
   var idx = $(this).attr("rel");
   $(".section1 .relbox").fadeOut();
   $(".relbox[rel='"+idx+"']").fadeIn(); 
});
 
$(".section2 .s4img").bind("click",function(){
    $(".section2 .s4img").removeClass("imgcur");
   $(this).addClass("imgcur");
   var idx = $(this).attr("rel");
   $(".section2 .relbox").fadeOut();
   $(".relbox[rel='"+idx+"']").fadeIn(); 
});

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
