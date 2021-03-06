function getCookie(name) { var value = "; " + document.cookie;
var parts = value.split("; " + name + "=");
if (parts.length == 2) return parts.pop().split(";").shift();
}
function addEmbedIframe(iframeObj) {
	if(typeof(iframeObj.width)=="undefined"){
		iframeObj.width="100%";
	}
	if(typeof(iframeObj.height)=="undefined"){
		iframeObj.height="100%";
	}
	if(typeof(iframeObj.style)=="undefined"){
		iframeObj.style="";
	}
	var basedata = "null";
	<?php
		include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
	?>
	var cookiePrefix = '<? echo $cookiePrefix;?>';
	if(typeof(getCookie(cookiePrefix+'data'))!="undefined"){
		basedata = getCookie(cookiePrefix+'data');
	}
	var container =document.getElementById("cometchat_embed_"+iframeObj.module+"_container");
	var queryStringSeparator='&';
	if(iframeObj.src.indexOf('?')<0){
		queryStringSeparator='?';
	}
	iframeObj.src+= queryStringSeparator+"basedata="+basedata;
	var iframe = document.createElement('iframe');
	iframe.style.cssText = iframeObj.style;
	iframe.src = iframeObj.src;
	iframe.width = iframeObj.width;
	iframe.height = iframeObj.height;
	iframe.name = 'cometchat_'+iframeObj.module+'_iframe';
	iframe.id = 'cometchat_'+iframeObj.module+'_iframe';
	iframe.setAttribute('class','cometchat_'+iframeObj.module+'_iframe');
	iframe.frameborder = 1;
	container.appendChild(iframe);
}
