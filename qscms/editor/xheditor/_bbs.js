{
	plugins:{
        Upload:{c:'uploadIco',t:'上传图片(Ctrl+U)',s:'ctrl+u',e:function(){
            var _this=this;
			_this.saveBookmark();
            _this.showIframeModal('上传图片','{rewrite}/dialog/upload/',function(rs){
				_this.loadBookmark();
					if (rs.id) {
						var attach={'type':rs.type,'src':rs.src};
						attachList[rs.id] = attach;
						switch(attach.type){
							case 'img':
								_this.pasteHTML('<img src="'+attach.src+'" attach="'+rs.id+'" />');
							break;
							default:
								_this.pasteHTML('<a href="'+attach.src+'" attach="'+rs.id+'">下载附件</a>');
							break;
						}
					}
				},500,150);
        	}
		},
		insertPic:{c:'insertPic',t:'插入图片(Ctrl+P)',s:'ctrl+p',e:function(){
            var _this=this;
			_this.saveBookmark();
            _this.showIframeModal('插入图片，多个ID用逗号隔开','{u}js_lib/xheditor/php/insert.php?type=pic',function(rs){
				_this.loadBookmark();
					for (var i = 0; i < rs.length; i++) {
						insertDatas['pic'+rs[i].id] = rs[i];
						_this.pasteHTML('<img src="'+rs[i].tiny+'" insertpic="'+rs[i].id+'" />');
					}
				},500,300);
        	}
		},
		insertAlbum:{c:'insertAlbum',t:'插入专辑(Ctrl+A)',s:'ctrl+a',e:function(){
            var _this=this;
			_this.saveBookmark();
            _this.showIframeModal('插入专辑，多个ID用逗号隔开','{u}js_lib/xheditor/php/insert.php?type=album',function(rs){
				_this.loadBookmark();
					for (var i = 0; i < rs.length; i++) {
						insertDatas['album'+rs[i].id] = rs[i];
						_this.pasteHTML('<img src="'+rs[i].tiny+'" insertalbum="'+rs[i].id+'" />');
					}
				},500,300);
        	}
		}
	},
	tools:'Cut,Copy,Paste,Pastetext,Blocktag,Fontface,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,BackColor,SelectAll,Removeformat,Align,List,Outdent,Indent,Link,Unlink,insertPic,insertAlbum,Flash,Media,Emot,Table,Source,Preview,Print,Fullscreen',
	emotPath:'{$weburl2}js_lib/xheditor/e/',
	emotMark:true,
	emots:{qq:{name:'QQ',count:77,width:24,height:24,line:11,index:-1}},
	defEmot:'qq',
	hideDefEmot:true,
	beforeSetSource:ubb2html,
	beforeGetSource:html2ubb,
	upImgUrl:'{$weburl2}ajax/upload.php?action=image',
	upImgExt:'jpg,jpeg,gif,png'
}