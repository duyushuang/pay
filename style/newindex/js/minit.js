//定义根节点字体
(function(){
    var a =function (){
        var raines = this;
        raines.width = 720;//设置默认最大宽度
        raines.fontSize = 100;//默认字体大小
        raines.maxSize = 720;//设置最大宽度，超过这一宽度，字体大小不再变大，设置为0意思为不做限制
        raines.minSize = 320;//设置最小宽度，小于这一宽度，字体大小不再变小，设置为0意思为不做限制
        var dpr = window.devicePixelRatio || 1;
        var scale = 1 / dpr;
        var metaEl=document.querySelector('meta[name="viewport"]');
        var p=document.body&&document.body.clientWidth||document.getElementsByTagName("html")[0].offsetWidth;
       if(raines.maxSize >0 ){
       p= p >raines.maxSize?raines.maxSize:p;
     }
    if(raines.minSize >0 ){
        p= p <raines.minSize?raines.minSize:p;
        }
        raines.widthProportion = function(){var i = p/raines.width;return i<0.5?0.5:i;};
        raines.changePage = function(){
            document.getElementsByTagName("html")[0].setAttribute("style","font-size:"+raines.widthProportion()*raines.fontSize*dpr+"px !important");
            metaEl.setAttribute('content', 'width=' + dpr * p + ',initial-scale=' + scale + ',maximum-scale=' + scale + ', minimum-scale=' + scale + ',user-scalable=no');
        };
        raines.changePage();
    };
    a();
    /*  说明：
     **  引入该文件，头部别忘记加<meta name="viewport" content="width=device-width, height=device-height,initial-scale=1.0, minimum-scale=1.0 , maximum-scale=1.0, user-scalable=0">
     **
     **  
     **
     ****/
     })();
//判断是否支持触摸事件 

 function isTouchDevice(){

  try{

   document.createEvent("TouchEvent");

   return true;

  }catch(e){

   return false;

  }

 }

 function touchScroll(obj){

  if(isTouchDevice()){

   var el=obj[0];

   var startY=0,diffY=0;

   var scrollHeight=el.scrollHeight;

   var bindPreventTouch=function(){

    $(document.body).on("touchmove",function(e){

     e.preventDefault();

    });

   };

   obj.on('touchstart',function(e){

    startY=e.touches[0].pageY;

   });

   obj.on('touchmove',function(e){

    diffY=e.touches[0].pageY-startY;

    if(el.scrollTop===0&&diffY>0){

     //到最上面

     bindPreventTouch();

    }else if((scrollHeight-el.scrollTop-el.offsetHeight)===0&&diffY<0){

     //到最下面

     bindPreventTouch();

    }

   });

    obj.on('touchend',function(e){

     $(document.body).off('touchmove');

    });

  }

 }
touchScroll($("#sitecontent"));
//加载
$(function(){
    //$('#header').sidenav();
    /*
    初始化大小
     */
    //初始化菜单高度
    $('#nav').height($(window).height()-($(window).width()>720?720:$(window).width())/720*100);
    //轮播图大小
    $('#mslider .slider-list').height($(window).width()/7.2*4.2);
    $('#mslider .slider-list>li').width($(window).width());

    $(window).resize(function(){
        //初始化菜单高度
        $('#nav').height($(window).height()-($(window).width()>720?720:$(window).width())/720*100);
        //轮播图大小
        $('#mslider .slider-list').height($(window).width()/7.2*4.2);
        $('#mslider .slider-list>li').width($(window).width());

    });
    /*
    初始化功能
     */
    
    
    //初始化菜单
    $('#sidenav-toggle').on('click',function(){
        $('#mask').show();
        $('#mask').attr('data-event','menu');
        $('body').css({
            'transform':'translate(4.8rem,0)',
            '-webkit-transform':'translate(4.8rem,0)',
            '-moz-transform':'translate(4.8rem,0)',
            '-o-transform':'translate(4.8rem,0)',
            '-ms-transform':'translate(4.8rem,0)'
        });

    });
    //初始化弹出二维码
    $('#open-qr').on('click',function(){
        $('#mask').show();
        $('#mask').attr('data-event','qr');
        $('#qr').show();
        $('#qr').addClass('show');
    });
    //遮罩层点击事件
    $('#mask').on('click',function(){
        var eve = $(this).attr('data-event');
        switch(eve){
            case 'menu':
               $('body').attr('style','');
            break;
            case 'qr':
                $('#qr').removeClass('show');
                setTimeout(function(){$('#qr').hide();},300);  
        }
        
        $('#mask').hide();
    });
    //各个子页弹出
    //contact
    var openContact = function(){
        $('#detail-container .main').html('<div id="detail-contact"></div>');
        var obj =$('#mcontact .contact .title').clone(true).appendTo($('#detail-contact'));
        obj.addClass('maintitle');
        $('#detail-contact').append('<img src="/style/newindex/images/m_contact.jpg"/>');
        obj =$('#mcontact .about-us .title').clone(true).appendTo($('#detail-contact'));
        obj.addClass('subtitle');
        $('#detail-contact').append('<div class="description-container"></div>');
        $('#mcontact .about-us .description').clone(true).appendTo($('#detail-contact .description-container'));
        $('#detail-contact').append($('#mcontact .contact .info').clone(true));
    }
    //industry
    var openIndustry = function(){
        $('#detail-container .main').html('<div id="detail-industry"><ul></ul></div>');
       
        $('#mproject .projectlist>li').each(function(){
            var liEle = $('<li></li>').appendTo($('#detail-industry > ul'));
            liEle.append('<div class="projectlist-img"><img src="/style/newindex/images/m_'+$(this).attr('data-bg')+'.jpg"/></div>');
            var cont = $('<div class="projectlist-cont"></div>').appendTo(liEle);
            cont.append($(this).find('.info .title').clone(true));
            cont.append($(this).find('.info p').clone(true));
        });
    }
    //partners
    var openPartners = function(){
        $('#detail-container .main').html('<div id="detail-partners"><ul></ul></div>');
       
        $('#mcoopration .coopration-list').each(function(){
            var liEle = $('<li></li>').appendTo($('#detail-partners > ul'));
            liEle.append($(this).children('img').clone(true));
        });
    }
    $('#nav li>a').on('click',function(){
        if($(this).hasClass('active')) return false;
        var l = $(this).parent('li');
        if(l.hasClass('pnav')){
            if(l.hasClass('open')){
                l.removeClass('open');
            }else{
                l.addClass('open');
            } 
        }else{
             $('#nav li>a.active').removeClass('active');
             $(this).addClass('active');
        }

        //单页跳转
        var target = $(this).attr('data-href');
        switch(target){
            case 'index':
                $('#detail-container').hide();
                $('#detail-container .main').hide();
                $('#logo').html('<img src="/img/web/1499583867552.png" />');
            break;
            case 'industry':
                $('#detail-container .main').hide();
                openIndustry();
                $('#detail-container .main').show();
                $('#detail-container').show();
                $('#logo').html('<h1>行业方案</h1>');
            break;
            case 'partners':
                $('#detail-container .main').hide();
                openPartners();
                $('#detail-container .main').show();
                $('#detail-container').show();
                $('#logo').html('<h1>合作伙伴</h1>');
            break;
            case 'contact':
                $('#detail-container .main').hide();
                openContact();
                $('#detail-container .main').show();
                $('#detail-container').show();
                $('#logo').html('<h1>联系我们</h1>');
            break;
            default:
                return false;
            break;
        }
        $('body').attr('style','');
        $('#mask').hide();
    });
    //初始化轮播图
    
    //添加相应按钮
        for(var i =0;i<$('.slider-list>li').length;i++){
            $('#mslider #btn-bar').append('<li><span></span></li>');
        }
        $('#mslider #btn-bar>li:first').addClass('active');
    //添加banner滚动效果
    var bannerScroll = null;
    var bannerIndex = 1;
    var le = $('.slider-list>li').length;
    $('#mslider').cxScroll({
            auto:false,
            prevBtn: false,        
            nextBtn: false,
            hoverLock: false, 
            gotofn: function(){ 
                $('#mslider #btn-bar li.active').removeClass('active');
                $('#mslider #btn-bar li').eq(bannerIndex).addClass('active');
                bannerIndex = (bannerIndex+1)%le;
            }
        }, function(api){ 
          bannerScroll = api; 
        });
    $(window).resize(function(){
        $('#mslider .list-container').scrollLeft($('#mslider #btn-bar li.active').index()*$(window).width());
        bannerScroll.changeWidth($(window).width());

    });
    $('#mslider .list-container').scrollLeft(0);
    $('#mslider #btn-bar li').on('click',function(){
        if($(this).hasClass('active')) return false;
        bannerScroll.stop();
        var o = $('#mslider #btn-bar li.active').index();
        var i = $(this).index();
        bannerIndex = i;
        o = i-o;
        if(o >0){
            bannerScroll.setStep(o);
            bannerScroll.next();
        }else{
            bannerScroll.setStep(-o);
            bannerScroll.prev();
        }
        bannerScroll.setStep(1);
        bannerScroll.play();
    });
    $('#mslider').swipe({
        swipe:function(event,
        direction, distance, duration, fingerCount) {
        //    alert("你用"+fingerCount+"个手指以"+duration+"秒的速度向"
        //         + direction + "滑动了" +distance+ "像素 " );
            bannerScroll.stop();
            if(direction == 'right'){
                bannerIndex = $('#mslider #btn-bar li.active').index()-1>=0?$('#mslider #btn-bar li.active').index()-1:le-1;
                bannerScroll.prev();
            }else if(direction == 'left'){
                bannerIndex = $('#mslider #btn-bar li.active').index()+1<le?$('#mslider #btn-bar li.active').index()+1:0;
                bannerScroll.next();
            }else if(direction == 'up'){
                var dis = $('#sitecontent').scrollTop()+distance;
                $('#sitecontent').scrollTop(dis)
            }else if(direction == 'down'){
                var dis = $('#sitecontent').scrollTop()-distance;
                $('#sitecontent').scrollTop(dis<0?0:dis);
            }
            bannerScroll.play();
        },
    });
    bannerScroll.play();
    //初始化账户管理
     $('#product-more').on('click',function(){
        $('#mservice .product-container .computer').css('height','7.44rem');
         $(this).hide();
     });
    //初始化云pos
    $('#pos-cont').css('left',0);
    $('#pos-cont').closest('li').find('.left-btn').hide();
    $('#pos-cont').closest('li').find('.left-btn').on('click',function(){
        $('#pos-cont').animate({ 
            left:'0'
          }, 500 );
        $('#pos-cont').closest('li').find('.left-btn').hide();
        $('#pos-cont').closest('li').find('.right-btn').show();
    });
    $('#pos-cont').closest('li').find('.right-btn').on('click',function(){
        $('#pos-cont').animate({ 
            left:'-100%'
          }, 500 );
        $('#pos-cont').closest('li').find('.left-btn').show();
        $('#pos-cont').closest('li').find('.right-btn').hide();
    });
    //初始化行业的数据
    $('#service-data .num').each(function(){
        $(this).html($(this).attr('data-value'));
    });

});
/*定义全局变量*/
//页面跳转方法
    var highline = {
        'scroll_To' : function(obj){

        }
    }

