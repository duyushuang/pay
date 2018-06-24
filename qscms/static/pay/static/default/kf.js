
//qq客服
function gdqqnover(){
document.getElementById("gdqqn").style.display="none";	
document.getElementById("gdqqh").style.display="";	
}
function gdqqhout(){
document.getElementById("gdqqn").style.display="";	
document.getElementById("gdqqh").style.display="none";	
}
var tips; 
var theTop = 410/*这是默认高度,越大越往下*/; 
var old = theTop;
function initFloatTips() {
tips = document.getElementById('floatTips');
moveTips();
};
function moveTips() {
tt = 50;
if (window.innerHeight) {
pos = window.pageYOffset
}
else if (document.documentElement && document.documentElement.scrollTop) {
pos = document.documentElement.scrollTop
}
else if (document.body) {
pos = document.body.scrollTop;
}
pos = pos - tips.offsetTop + theTop;
pos = tips.offsetTop + pos / 10;
if (pos < theTop) pos = theTop;
if (pos != old) {
tips.style.top = pos + "px";
tt = 10;
}
old = pos;
setTimeout(moveTips, tt);
}
