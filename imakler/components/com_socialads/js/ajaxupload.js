function $m(theVar)
{
	return document.getElementById(theVar)
}

function remove(theVar)
{
	var theParent = theVar.parentNode;
	theParent.removeChild(theVar);
}

//function addEvent(obj, evType, fn) //// commented by VM. (affection on modal popup)

function sa_addEvent(obj, evType, fn)
{
	if(obj.addEventListener)
	    obj.addEventListener(evType, fn, true)
	if(obj.attachEvent)
	    obj.attachEvent("on"+evType, fn)
}

function removeEventAd(obj, type, fn)
{
	if(obj.detachEvent){
		obj.detachEvent('on'+type, fn);
	}else{
		obj.removeEventListener(type, fn, false);
	}
}

function isWebKit()
{
	return RegExp(" AppleWebKit/").test(navigator.userAgent);
}

function ajaxUpload(form,url_action,id_element,html_show_loading,html_error_http)
{
//console.log(jQuery("#ad-preview").find("#upload_area"));//id_element = 'layout1_ad_prev_second';
//id_element = jQuery("#ad-preview").find("#upload_area");
	var mediafile	=	jQuery("#ad_image").val().split('/').pop().split('\\').pop();
	jQuery("#ad_img_box #direct_upload .uneditable-input .fileupload-preview").text(mediafile);
	jQuery("#ad_img_box #direct_upload .uneditable-input .icon-file").show();
	var detectWebKit = isWebKit();
	form = typeof(form)=="string"?$m(form):form;
	var erro="";
	if(form==null || typeof(form)=="undefined"){
		erro += "The form of 1st parameter does not exists.\n";
	}else if(form.nodeName.toLowerCase()!="form"){
		erro += "The form of 1st parameter its not a form.\n";
	}//console.log($m(id_element));
	if($m(id_element)==null){
		erro += "The element of 3rd parameter does not exists.\n";
	}
	if(erro.length>0){
		alert("Error in call ajaxUpload:\n" + erro);
		return;
	}
	var iframe = document.createElement("iframe");
	iframe.setAttribute("id","ajax-temp");
	iframe.setAttribute("name","ajax-temp");
	iframe.setAttribute("width","0");
	iframe.setAttribute("height","0");
	iframe.setAttribute("border","0");
	iframe.setAttribute("style","width: 0; height: 0; border: none;");
	form.parentNode.appendChild(iframe);
	window.frames['ajax-temp'].name="ajax-temp";
	var doUpload = function(){
		removeEventAd($m('ajax-temp'),"load", doUpload);
		var cross = "javascript: ";
		cross += "window.parent.$m('"+id_element+"').innerHTML = document.body.innerHTML; void(0);";
		$m(id_element).innerHTML = html_error_http;
		$m('ajax-temp').src = cross;
		//jQuery('#ad-preview').find('.layout1_ad_prev_second').html('heyyy');//console.log($m('ajax-temp').src );
		if(detectWebKit){
        	remove($m('ajax-temp'));
        }else{
        	setTimeout(function(){ remove($m('ajax-temp'))}, 250);
        }
    }
	//addEvent($m('ajax-temp'),"load", doUpload); // commented by VM. (affection on modal popup)
	sa_addEvent($m('ajax-temp'),"load", doUpload);
	form.setAttribute("target","ajax-temp");
	//form.setAttribute("action",url_action);
	form.setAttribute("action",'index.php?option=com_socialads&controller=buildad&task=save'+url_action);
	form.setAttribute("method","post");
	form.setAttribute("enctype","multipart/form-data");
	form.setAttribute("encoding","multipart/form-data");

	if(form.upimg)
	{

		form.upimgcopy.value=form.upimg.value;
	}
	if(html_show_loading.length > 0){
		$m(id_element).innerHTML = html_show_loading;
	}

	form.submit();
}
