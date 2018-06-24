$(document).ready(function(){
	$("a.prettyPhoto").prettyPhoto({social_tools:false});
	$('.video_preview a').click(function(){
		var self=this;
		var parent=$(self).parent();
		var video_url=$(this).attr('href');
		var code=$('<video controls>').append($('<source>').attr('src',video_url).attr('type','video/mp4'));
		parent.html(code);
		setTimeout(function(){
			$(parent).find('video').get(0).play();
		},200)
return false;
})});