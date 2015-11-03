techjoomla.jQuery(document).ready(function() {
    techjoomla.jQuery("#toggle").click(function() {
		var checkBoxes = techjoomla.jQuery("input[name='cid\\[\\]']")
        checkBoxes.prop("checked", !checkBoxes.prop("checked"));
    });
});

function publishcheck(selector)
	{
		var id = selector.id;
		var status;
		if(selector.checked==true)
		{
			status = 1;
		}
		else
		{
			status = 0;
		}
		var brokenstring  = id.split("_");

		var url = root_url+'?option=com_socialads&task=savepublish&id='+brokenstring[1]+'&status='+status;
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
		 	http=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
		  	http=new ActiveXObject("Microsoft.XMLHTTP");
		}
			var params = "save"+brokenstring[1];
			http.onreadystatechange=function()
			 {
			  if (http.readyState==4 && http.status==200)
			  {
				alert(http.responseText);
				  //alert(.'JText::_('STATUS').');
			  }
			 }
			http.open("GET", url, true);

			//Send the proper header information along with the request
			http.send();
		return;
	}
//function to get characters written in textarea
	/*function getObject(obj)
		{
			var theObj;
			if(document.all) {
				if(typeof obj=="string") {
				  return document.all(obj);
				} else {
				  return obj.style;
				}
			}
			if(document.getElementById) {
				if(typeof obj=="string") {
				  return document.getElementById(obj);
				} else {
				  return obj.style;
				}
			}
			return null;
		}*/
//function getobject ends here

//function getcount for counting chars in title
	/*function toCount(entrance,exit,text,msg,characters,value,event)
		{
			if(event.keyCode != 9)
			{
				techjoomla.jQuery("#ad-preview").find(".preview-title").text(value);
			}
if(document.getElementById('adtype').value == 'affiliate'){
return;
}
			//alert(characters);
			var entranceObj=getObject(entrance);
			var exitObj=getObject(exit);
			var length=characters - entranceObj.value.length;
			if(length <= 0) {
				length=0;
				var data = text.replace("{CHAR}",length);
				techjoomla.jQuery('#max_tit1').html('<span class="disable"> '+data+msg+' </span>');
				//text='<span class="disable"> '+text+' </span>';
				techjoomla.jQuery('#sBann').hide();
				entranceObj.value=entranceObj.value.substr(0,characters);

			}else{
			exitObj.innerHTML = text.replace("{CHAR}",length);
			techjoomla.jQuery('#sBann').show();
			}
		}*/
//function getcount ends here

//function getcount1 for counting chars	in body text
	/*function toCount1(entrance,exit,text,msg,characters,value,event)
		{
			//document.getElementById('previewbody').innerHTML = document.getElementById('eBann1').value;
			if(event.keyCode != 9)
			{
				techjoomla.jQuery("#ad-preview").find(".preview-bodytext").text(value);
			}
if(document.getElementById('adtype').value == 'affiliate'){
return;
}
			var entranceObj=getObject(entrance);
			var exitObj=getObject(exit);
			var length=characters - entranceObj.value.length;
			if(length <= 0) {
				length=0;
				var data = text.replace("{CHAR}",length)
				techjoomla.jQuery('#max_body1').html('<span class="disable"> '+data+msg+' </span>');
				entranceObj.value=entranceObj.value.substr(0,characters);
				//exitObj.innerHTML = text.replace("{CHAR}",length);
				techjoomla.jQuery('#sBann1').hide();
			}
			else{
			exitObj.innerHTML = text.replace("{CHAR}",length);
			techjoomla.jQuery('#sBann1').show();
			}
		}*/
//function getcount1 ends here

/*function changelayout(rad)
{
	if(document.getElementById('adtype').value == 'affiliate'){
		return;
	}
	if(techjoomla.jQuery("#upload_area").find("div").children("[name=upimg]").val() != null)
		techjoomla.jQuery('#upimg').val(techjoomla.jQuery('#upload_area').find('div').children('[name=upimg]').val());
	var adtype=''+document.getElementById('adtype').value; /*added by manoj 2.7.5.beta.2*/
	/*var adzone=document.getElementById('adzone').options[document.getElementById('adzone').selectedIndex].value; /*added by manoj 2.7.5 stable*/
	/*techjoomla.jQuery.ajax({
		url: root_url+'?option=com_socialads&task=changelayout&layout='+rad+'&title='+techjoomla.jQuery("#eBann").val()+'&body='+techjoomla.jQuery("#eBann1").val()+'&img='+techjoomla.jQuery('#upimg').val()+'&adtype='+adtype+'&adzone='+adzone,
		type: 'GET',
		dataType: "json",
		success: function(data){
			if (!document.getElementById(rad+'css'))	  //to check if the css is already added
			{
				var head  = document.getElementsByTagName('head')[0];
				var link  = document.createElement('link');
				link.id   = rad+'css';
				link.rel  = 'stylesheet';
				link.type = 'text/css';
				link.href = data.css;
				link.media = 'all';
				head.appendChild(link);
			}
			document.getElementById('ad-preview').innerHTML = data.html;
			if(data.js){/*added by manoj 2.7.5 stable*/
				//eval(data.js);
			/*}
		}
	});
}*/

	/*function chk(value){
    //if(this.value==''){value='Example Ad'; className="preview-title-lnk";}
    document.getElementById("preview-title").innerHTML= value;
    }*/

/*	function open_datebox()
	{
		document.getElementById('bottom1').style.display="block";
	}
*/
	/*function close_datebox()
	{
		document.getElementById('bottom1').style.display="none";
	}*/

	function checkreview_manage(datel,msg2)
	{
		if(chkAdValid())
		{
			document.getElementById("adsform").setAttribute("target","");
			document.getElementById("adsform").setAttribute("action","");
		}
		else
		{
			return false;
		}
		if(datel)
		{

			if(!checkdate(datel,'_low',msg2))
				return false;
			if(!checkdate(datel,'_high',msg2))
				return false;
		}
	}// function checkreview ends here
/*
	function checkdate(date,str,msg)
	{
		multiarray = date.split(',');
		for(var j=0; j<multiarray.length; j++)
		{
			var flag = 0;
			test = document.getElementById('mapdata[]['+multiarray[j]+str+']').value;

			if(test.match(/^[0-9]{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])/))
				flag=1;
			if(test=='')
				flag=1;
			if ( flag==0 )
			{
				alert(msg);
				return;
			}
		}
		return true;
	}
	*/
	function altadjsm()
	{
		if(!chkAdValid())
		{
			return false;
		}
		else
		{
			document.myForm.task.value = 'altadupdate';
			document.getElementById('adsform').setAttribute('target','');
			document.getElementById('adsform').setAttribute('action','');
		}
	}

	//function for multiselect to select- select all
	/*function selectallList(checkbox,id)
	 {

	      var test=document.getElementById('mapdata'+id).options.length;
	      if(checkbox.checked)
	      {
	      for (var i=0; i<test; i++)
	        {
				document.getElementById('mapdata'+id).options[i].selected=true;

			}

		  }

		  else
		  {
		  	 for (var i=0; i<test; i++) {
					document.getElementById('mapdata'+id).options[i].selected=false;
			}

		  }

			return true;
	 }*/

	//function to deselect checkbox-select all
	 /*function selectClicked(mulbox)
	 {
	 	document.getElementById('multiselect-'+mulbox).checked = false;
	 }*/

	/* function show_hide_geo(element) {

// If checked
			if (techjoomla.jQuery("#"+element).is(":checked"))
			{
				//show the hidden div
				techjoomla.jQuery("#"+element+"_div").show("slow");
			}
			else
			{
				//otherwise, hide it
				techjoomla.jQuery("#"+element+"_div").hide("slow");
			}
 }*/

