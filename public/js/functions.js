/*网站功能js
*author:Jiaojinying
*Time:2013.12.19
*/
//检测登录
function check_login(lxdh,login_path,page){
	var login_url=login_path+"?m=Tools&g=checklogin";
	$.ajax({ 
	async: false, 
	url:login_url, 
	type: "GET", 
	dataType: 'jsonp', 
	jsonp: "callback",
	data: null, 
	timeout: 5000, 
	contentType: "application/json;utf-8",
	success: function (result) {
			if(result.status==1){
				var member=result.data;
				var model=member.usermodelshort_name;
				var usermodelname=member.usermodelname;
				var uc_url=member.ucenter_url;
				var logout_path=uc_url+"?m=Index&g=logout";
				if(page=='fenzhan_index'){
						$("#loupan_more_baoliao").html("[<a href='"+uc_url+"' target='_blank' rel='nofollow'>用户更多爆料</a>]");
						$("#loginbox").html("^0^,<a href='"+uc_url+"' target='_blank' >"+member.nickname+"</a>&nbsp;("+model+")&nbsp;</span>&nbsp;<a href='javascript:;' class='green email_rss' >邮件订阅</a>&nbsp;<a href='"+logout_path+"' class='blue'>退出登录</a>&nbsp;");
						//<span class='vip"+member.vipid+"'>
				}else{
						//已经登录
						$("#loginbox").html("^0^,<a href='"+uc_url+"' target='_blank' >"+member.nickname+"</a>&nbsp;("+model+")&nbsp;<a href='javascript:;' class='green email_rss' >邮件订阅</a>&nbsp;<a href='"+logout_path+"' class='blue'>退出登录</a>&nbsp;");
						switch(page){
							case 'loupan_content':
							//楼盘公积金爆料
							loupan_baoliao(uc_url);
							$("#loupan_lxdh").html("<font color='#00f'>&nbsp;"+lxdh+"</font>");
							break;
							case 'index':
							$(".email_rssbox").addClass('email_rss');
							break;
						}
						email_rss(uc_url);
				}
			}else{
				//未登录
				var jumpurl=escape(window.location.href);
				if(page=='fenzhan_index'){
					$("#loupan_more_baoliao").html("[<a href='javascript:;' class='loginjumpbox' rel='nofollow'>用户更多爆料</a>]");
					$("#loginbox").html('您好,请&nbsp;<a href="javascript:;" class="loginjumpbox" >登录</a>&nbsp;/&nbsp;<a href="'+login_path+'?m=Register&jumpurl='+jumpurl+'" target="_blank">注册</a>&nbsp;<span class="qq_login"><a href="'+login_path+'?m=Qqlogin&g=login&jumpurl='+jumpurl+'">QQ登录</a></span>&nbsp;<a href="javascript:;" class="loginjumpbox green">邮件订阅</a>&nbsp;');
					$(".email_rss").addClass('loginjumpbox');
				}else{
					$("#loginbox").html('您好,请&nbsp;<a href="javascript:;" class="loginjumpbox" >登录</a>&nbsp;/&nbsp;<a href="'+login_path+'?m=Register&jumpurl='+jumpurl+'" target="_blank">注册</a>&nbsp;<span class="qq_login"><a href="'+login_path+'?m=Qqlogin&g=login&jumpurl='+jumpurl+'">QQ登录</a></span>&nbsp;<a href="javascript:;" class="loginjumpbox green">邮件订阅</a>&nbsp;');
					$(".email_rss").addClass('loginjumpbox');
					switch(page){
						case 'loupan_content':
						$(".user_baoliao").addClass('loginjumpbox');
						$("#loupan_lxdh").html('&nbsp;<a href="javascript:;" class="loginjumpbox">^o^亲，登录后才能查看联系电话(⊙o⊙)哦!</a>');
						break;
						case 'index':
						$(".email_rssbox").addClass('loginjumpbox');
						break;					
					}
				}
				loginbox(login_path);
			}
	}
	});
}
// 加入收藏夹
function SetFavorite() {
	var sTitle=document.title;
	var sURL=document.location.href;
    try {
        window.external.addFavorite(sURL,sTitle);
    } catch (e) {
        try {
            window.sidebar.addPanel(sTitle,sURL, "");
        } catch (e) {
            alert("加入收藏失败，请使用Ctrl+D进行添加收藏");
        }
    }
}
//邮件订阅
function email_rss(uc_url){
	$(".email_rss").attr('href',uc_url+"?m=Rss&g=email_rss");
	$(".email_rss").fancybox({
		'padding':0,
		'opacity':true, 
		'scrolling':'no', 
		'speedIn':600, 
		'speedOut':200,
		'overlayOpacity':0.9, 
		'overlayColor':'#404040',
		'type': 'iframe'
	});
}
//登录框
function loginbox(login_path){
	var jumpurl=window.location.href;
	$(".loginjumpbox").attr('href',login_path+"?m=Index&g=jump_login&jumpurl="+jumpurl);
	$(".loginjumpbox").fancybox({
		'padding':0,
		'opacity':true, 
		'scrolling':'no', 
		'speedIn':600, 
		'speedOut':200, 
		'width':342,
		'height':422,
		'overlayOpacity':0.9, 
		'overlayColor':'#404040',
		'type': 'iframe'
	});
}
//区域切换
function scroll_show(zonebiaoshi,biaoshi,id){
	$(".tag"+id).removeClass("selected");
	$(".nav"+biaoshi).addClass("selected");
	$("."+zonebiaoshi).hide();
	$("#"+biaoshi).show();
}
//文章评论
function comment(id,modelname){
	alert(id);
}
//加载楼盘数据
function load_loupan(site_domain,biaoshi,num,flag,type){
	$("#"+biaoshi).html("<li>正在加载中...</li>");
	$.ajax({ 
	async: false, 
	url:site_domain+'/Loupan/ajax_get_loupandata', 
	type: "POST", 
	dataType: 'jsonp', 
	jsonp: "callback",
	data: {num:num,flag:flag,type:type}, 
	timeout: 5000, 
	contentType: "application/json;utf-8",
	success: function (result) {
		if(result.status==1){
			var str='';
			$.each(result.msg,function(i,item){
				str+="<li><font color='#666666'>["+item.city+"]</font>&nbsp;<a href='"+item.url+"' target='_blank'>"+item.loupan_name+"</a></li>";
			})
			$("#"+biaoshi).html(str);
		}else{
			$("#"+biaoshi).html("<li>"+result.msg+"</li>");
		}
	}
	})
}
//楼盘爆料
function loupan_baoliao(ucenter_url){
	var val1=$('#zhichi_url').attr('href');
	var val2=$('#buzhichi_url').attr('href');
	var url1=ucenter_url+'?m=Loupan_baoliao&g=refresh'+val1;
	var url2=ucenter_url+'?m=Loupan_baoliao&g=refresh'+val2;
	$("#zhichi_url").attr('href',url1);
	$("#buzhichi_url").attr('href',url2);
	$(".user_baoliao").fancybox({
		'padding':0,
		'opacity':true, 
		'scrolling':'no', 
		'speedIn':600, 
		'speedOut':200, 
		'overlayOpacity':0.6, 
		'overlayColor':'#404040',
		'type': 'iframe'
	});
}
function jumpto_site(){
	var  host =document.referrer;
	var patt = new RegExp('baibeila.com');
	if(patt.test(host)){
		//站内跳动回首页
		return true;
	}else{
		var  jumpurl=getCookie('blx_userfirstdomain');
		if(jumpurl){
			window.open(jumpurl,'_parent');
			return true;
		}else{
			$('#citysite').click();
			return true;
		}
	}
}

function cookie(name){    
   var cookieArray=document.cookie.split("; "); //得到分割的cookie名值对    
   var cookie=new Object();    
   for (var i=0;i<cookieArray.length;i++){    
      var arr=cookieArray[i].split("=");       //将名和值分开    
      if(arr[0]==name)return unescape(arr[1]); //如果是指定的cookie，则返回它的值    
   } 
   return ""; 
} 
function delCookie(name)//删除cookie
{
   document.cookie = name+"=;expires="+(new Date(0)).toGMTString();
}
function getCookie(objName){//获取指定名称的cookie的值
    var arrStr = document.cookie.split("; ");
    for(var i = 0;i < arrStr.length;i ++){
        var temp = arrStr[i].split("=");
        if(temp[0] == objName) return unescape(temp[1]);
   } 
}
function addCookie(objName,objValue,objHours){      //添加cookie
    var str = objName + "=" + escape(objValue);
 
    if(objHours > 0){                               //为时不设定过期时间，浏览器关闭时cookie自动消失
 
        var date = new Date();
 
        var ms = objHours*3600*1000;
 
        date.setTime(date.getTime() + ms);
 
        str += "; expires=" + date.toGMTString();
 
   }
 
   document.cookie = str;
}
function SetCookie(name,value)//两个参数，一个是cookie的名子，一个是值
{
    var Days = 30; //此 cookie 将被保存 30 天
    var exp = new Date();    //new Date("December 31, 9998");
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function getCookie(name)//取cookies函数        
{
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
     if(arr != null) return unescape(arr[2]); return null;
}
function delCookie(name)//删除cookie
{
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}