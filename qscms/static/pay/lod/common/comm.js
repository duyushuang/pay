function goUrl(url) {
    if (url == -1 || url == false) {
        history.go(-1)
    } else {
        window.location.href = url
    }
}
function geturl() {
    history.go(-1);
}
function showError(tip) {
	layer.msg(tip);
	/*
    layer.open({
        content: tip,
       // time: 2 //2秒后自动关闭
    });
	*/
}
function showContent(title,url){
    $('#waModal').modal('show');
    $('#waModal .modal-title').text(title);
    $.get(url,{t:new Date().getTime()},function(data){
        $('#waModal .modal-body').html(data);
    });
}
function loading() {
    layer.open({type: 2, shadeClose: false});
}
function closelayer() {
    layer.closeAll();
}
// 几秒后自动跳转
function countDown(obj) {
    var o = $(obj), u = o.data("url"), t, n;
    t = setInterval(function () {
        n = parseInt(o.html());
        if (--n <= 0) {
            location.href = u;
            clearInterval(t);
        }
        o.html(n);
    }, 1000);
}

$(function(){

    $('.form-ajax').submit(function(e){
        e.preventDefault();
        $.ajax({
            url : $(this).attr('action'),
            type : 'POST',
            dataType : 'json',
            data: $(this).serialize(),
            beforeSend: function(){
                $('.prompt-error').text('');
                $('.woody-prompt').hide();
            },
            success : function(result){
                if(result.status==false){
					showError(result.msg);
					return false;
                }
                if(result.status==true){
					if (result.msg){
						showError(result.msg);
					}else{
						showError('操作成功');	
					}
                    if(result.url){
						setTimeout(function(){
							window.location.href = result.url;
						},1000);
                    }else{
						setTimeout(function(){
							history.go(-1);
						},1000);
						//window.location.href = webUrl;
						
					}
                }
            }
        });
    });

$(window).scroll(function(){
    $('.top-notice').hide();
    if($(this).scrollTop()==0){
        $('.top-notice').fadeIn();
    }
});
});
