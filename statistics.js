//alert(returnCitySN["cname"]);
//设置cookie
function setCookie(cname, cvalue, exhours) {
    var d = new Date();
    d.setTime(d.getTime() + (exhours*60*1000));//过期时间2分钟 2小时exhours*60*60*1000
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
//获取cookie
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}
//清除cookie
function clearCookie(name) {
    setCookie(name, "", -1);
}
function checkCookie() {
    var title = document.getElementsByClassName("articletitle")[0].innerHTML,
        articleid=document.getElementsByClassName("articleid")[0].innerHTML,
        author=document.getElementsByClassName("articleauthor")[0].innerHTML,
        fromip = returnCitySN["cip"],
		fromaddress = returnCitySN["cname"],
        domain = window.location.host,
        pageurl = window.location.href,
        cookieurl = getCookie(pageurl);
    if (cookieurl== "" && cookieurl!=pageurl) {
        script = document.createElement("script");
        param = "?articleid="+articleid+"&title="+title+"&author="+author+"&fromip="+fromip+"&fromaddress="+fromaddress+"&domain="+domain+"&pageurl="+pageurl;
	    script.src = "http://61.166.55.173:8181/index.php/visit/index.html"+param;
        document.getElementsByTagName("head")[0].appendChild(script);
        if (pageurl!= "" && pageurl!=null) {
            setCookie(pageurl, pageurl, 0);
        }
    }}
checkCookie();
