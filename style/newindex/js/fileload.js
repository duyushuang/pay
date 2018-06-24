function is_pc(){
// 其他类型的移动操作系统类型，自行添加
    var os = new Array("Android","iPhone","Windows Phone","iPod","iPad","BlackBerry","MeeGo","SymbianOS");  
    var info = navigator.userAgent;
    var len = os.length;
    for (var i = 0; i < len; i++) {
        if (info.indexOf(os[i]) > 0){
            return false;
        }
    }
    return true;
}
