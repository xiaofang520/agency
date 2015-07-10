/*网站跟踪功能
*author:Jiaojinying
*Time:2014.09.20
*/
$(function(){
	var url = location.search;
	var theRequest = new Object();
	if (url.indexOf("?") != -1) {
		var str = url.substr(1);
		if (str.indexOf("&") != -1) {
			strs = str.split("&");
			for (var i = 0; i < strs.length; i++) {
				theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
			}
		} else {
			var key = str.substring(0,str.indexOf("="));
			var value = str.substr(str.indexOf("=")+1);
			theRequest[key] = decodeURI(value);
		}
	}
	return log(theRequest);
})
function log(obj){
	var cannal;
	if(obj.cannal){
		cannal=obj.cannal;
	}else{
		cannal=1;//平台账号互联
	}	
	var from;
	if(obj.from){
		from=obj.from;
	}else{
		if(obj.cannal){
			var fromUrl=document.referrer;
			var from="未知域名";
			if(fromUrl){
				var re = /http:\/\/([^\/]+)\//i;
				var h = fromUrl.match(re);
				from=h[1];//取得来源站点域名
			}
		}else{
			from='me_weblink';//8z电脑端账号
		}
	}
	var count_url="http://www.8z.net/analyse/trace.php";
	var url=window.location.href;
	$.ajax({ 
	async: false, 
	url:count_url, 
	type: "GET", 
	dataType: 'jsonp', 
	jsonp: "callback",
	data: {action_id:1,cannal:cannal,from:from,action_url:url}, //页面统计
	timeout: 5000, 
	contentType: "application/json;utf-8",
	success: function (result) {
			if(result.status==1){
				//统计完成
				return true;
			}else{
				//统计未成功
				return false;
			}
	 }
	});
}