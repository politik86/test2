techjoomla.jQuery(document).ready(function(){
	techjoomla.jQuery( '.targetting_yes_no label' ).on( "click", function() {
		var radio_id	=	techjoomla.jQuery(this).attr('for');

		techjoomla.jQuery('#'+radio_id).attr('checked', 'checked');

		/*for jQuery 1.9 and higher*/
		techjoomla.jQuery('#'+radio_id).prop("checked", true)

		var radio_btn =techjoomla. jQuery('#'+radio_id);
		var radio_value=radio_btn.val();

		var radio_name	=	techjoomla.jQuery('#'+radio_id).attr('name');
		var target_div	=	radio_name	+"_div"
		techjoomla.jQuery(this).parent().find('label').removeClass('btn-success').removeClass('btn-danger');
		if(radio_value	== 1)
		{
			techjoomla.jQuery(this).addClass('btn-success');
			techjoomla.jQuery('#'+target_div).show("slow");
		}
		if(radio_value	== 0)
		{
			techjoomla.jQuery(this).addClass('btn-danger');
			techjoomla.jQuery('#'+target_div).hide("slow");
		}
	});
	techjoomla.jQuery( '.unlimited_yes_no label' ).on( "click", function() {
		var radio_id	=	techjoomla.jQuery(this).attr('for');

		techjoomla.jQuery('#'+radio_id).attr('checked', 'checked');

		/*for jQuery 1.9 and higher*/
		techjoomla.jQuery('#'+radio_id).prop("checked", true)

		var radio_btn = techjoomla.jQuery('#'+radio_id);
		var radio_value=radio_btn.val();

		var radio_name	=	techjoomla.jQuery('#'+radio_id).attr('name');
		var target_div	=	radio_name	+"_div"
		techjoomla.jQuery(this).parent().find('label').removeClass('btn-success').removeClass('btn-danger');
		if(radio_value	== 1)
		{
			techjoomla.jQuery(this).addClass('btn-success');
			hidePayment(1);

			// vm:hide price and coupon releated things
			techjoomla.jQuery('.sa_hideForUnlimitedads').hide();
		}
		if(radio_value	== 0)
		{
			techjoomla.jQuery(this).addClass('btn-danger');
			hidePayment(0);

			// vm:show price and coupon releated things
			techjoomla.jQuery('.sa_hideForUnlimitedads').show();
		}
		changenexttoexit(radio_value);
	});
});
//function to get characters written in textarea
	function getObject(obj)
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
		}
//function getobject ends here


//function getcount for counting chars in title
	function toCount(entrance,exit,text,msg,characters,value,event)
		{
			if(event.keyCode != 9)
			{
				if(entrance == 'eBann')
					techjoomla.jQuery("#ad-preview").find(".preview-title").text(value);
			}
			if(document.getElementById('adtype').value == 'affiliate'){
				return;
			}
			var entranceObj=getObject(entrance);
			var exitObj=getObject(exit);
			var length=characters - entranceObj.value.length;
			if(length <= 0) {
				length=0;
				var data = text.replace("{CHAR}",length);
				//if (event != '')
					techjoomla.jQuery('#max_tit1').html('<span class="disable"> '+data+msg+' </span>');
//				text='<span class="disable"> '+text+' </span>';
				techjoomla.jQuery('#sBann').hide();
				entranceObj.value=entranceObj.value.substr(0,characters);

//				jQuery("#sBann").addclass('disable');
			}else{
			exitObj.innerHTML = text.replace("{CHAR}",length);
			techjoomla.jQuery('#sBann').show();
			}
		}
//function getcount ends here

//function getcount1 for counting chars	in body text
	function toCount1(entrance,exit,text,msg,characters,value,event)
		{
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
			}else{
			exitObj.innerHTML = text.replace("{CHAR}",length);
			techjoomla.jQuery('#sBann1').show();
			}
		}
//function getcount1 ends here

	function chk(value){
    //if(this.value==''){value='Example Ad'; className="preview-title-lnk";}
    document.getElementById("preview-title").innerHTML= value;
    }


function show_hide_geo(element) {

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
}


//function for the ad-type change and sort out the ad zones.
//selected_pricingmode depends on the pricing mode selected. (0- pay per ad) (1-Ad wallet mode).
function Adchange()
{

var aa=''+document.getElementById('adtype').value;
if(aa=='')
{
alert('There are no Zones created for this Ad type!!');
return;
}
techjoomla.jQuery.ajax({

	url: root_url+'?option=com_socialads&task=getzones&ad_type='+document.getElementById('adtype').value,
	type: 'GET',
	dataType: "json",
	success: function(data) {

		techjoomla.jQuery("#adzone").html('');
		var ad_zone_id=(document.getElementById('ad_zone_id').value);

		if(data != ''){
			for (i=0;i<data.length;i++)
			{
				if(parseInt(data[i].zone_id)==parseInt(ad_zone_id))
				{
					techjoomla.jQuery("#adzone").append('<option value="'+data[i].zone_id+'" selected>'+data[i].zone_name+'</option>');
				}
				else
				{
					techjoomla.jQuery("#adzone").append('<option value="'+data[i].zone_id+'" >'+data[i].zone_name+'</option>');
				}
			}
			if(document.getElementById('adtype').value != 'affiliate'){
			techjoomla.jQuery("#eBann1").removeAttr('style');
				getZonesdata(selected_pricing_mode); //call the zones to get the layouts //change by aniket to call caltotal function from get getzonesdata function--which helps in getting proper value for total amount during edit ad
			}//added in 2.8 for priority_random Bug #12664
			else if(document.getElementById('adtype').value =='affiliate')
			{
				techjoomla.jQuery('#ad_zone_id').val(techjoomla.jQuery('#adzone option:selected').val());
			}
			techjoomla.jQuery("select").trigger("liszt:updated");  /* IMP : to update to chz-done selects*/
			//End added in 2.8 for priority_random Bug #12664
		}
		else{
			alert('There are no Zones created for this Ad type!!');
//	document.getElementById('adsform').adtype[0].selected = true;
			techjoomla.jQuery('#adtype option:selected').next('option').attr('selected', 'selected');
			return;

		}
	}
});
if(techjoomla.jQuery("#upload_area").find("div").children("[name=upimg]").val() != null)
	techjoomla.jQuery('#upimg').val(techjoomla.jQuery('#upload_area').find('div').children('[name=upimg]').val());

switch (document.getElementById('adtype').value){
	case "img":
		techjoomla.jQuery("#ad_img_name").show();
		techjoomla.jQuery("#ad_img_box").show();
		techjoomla.jQuery("#ad_title_name").show();
		techjoomla.jQuery("#ad_title_box").show();
		techjoomla.jQuery("#ad_body_name").hide();
		techjoomla.jQuery("#ad_body_box").hide();
techjoomla.jQuery("#layout_div").show();
techjoomla.jQuery("#sa_preview").show();
techjoomla.jQuery("#defaulturl").show();	/*bug fixed for 2.7.5 beta 2*/
	break;

	case "text":
		techjoomla.jQuery("#ad_title_name").show();
		techjoomla.jQuery("#ad_title_box").show();
		techjoomla.jQuery("#ad_body_name").show();
		techjoomla.jQuery("#ad_body_box").show();
		techjoomla.jQuery("#ad_img_name").hide();
		techjoomla.jQuery("#ad_img_box").hide();
techjoomla.jQuery("#max_tit1").show();
techjoomla.jQuery("#max_body1").show();
techjoomla.jQuery("#layout_div").show();
techjoomla.jQuery("#sa_preview").show();
techjoomla.jQuery("#defaulturl").show();	/*bug fixed for 2.7.5 beta 2*/
break;

	case "text_img":
		techjoomla.jQuery("#ad_title_name").show();
		techjoomla.jQuery("#ad_title_box").show();
		techjoomla.jQuery("#ad_body_name").show();
		techjoomla.jQuery("#ad_body_box").show();
		techjoomla.jQuery("#ad_img_name").show();
		techjoomla.jQuery("#ad_img_box").show();
techjoomla.jQuery("#max_tit1").show();
techjoomla.jQuery("#max_body1").show();
techjoomla.jQuery("#layout_div").show();
techjoomla.jQuery("#sa_preview").show();
techjoomla.jQuery("#defaulturl").show();	/*bug fixed for 2.7.5 beta 2*/
break;

	case "affiliate":
techjoomla.jQuery("#defaulturl").hide();
		techjoomla.jQuery("#ad_body_name").show();
		techjoomla.jQuery("#ad_body_box").show();
techjoomla.jQuery("#max_body1").hide();
techjoomla.jQuery("#sBann1").hide();
techjoomla.jQuery("#eBann1").height(150);
techjoomla.jQuery("#eBann1").width(312);
techjoomla.jQuery("#eBann1").removeAttr('maxlength');
		techjoomla.jQuery("#ad_title_name").show();
		techjoomla.jQuery("#ad_title_box").show();
techjoomla.jQuery("#max_tit1").hide();
techjoomla.jQuery("#sBann").hide();

		techjoomla.jQuery("#ad_img_name").hide();
		techjoomla.jQuery("#ad_img_box").hide();
techjoomla.jQuery("#layout_div").hide();
techjoomla.jQuery("#sa_preview").hide();
break;

	default : ;
}
switchCheckboxalt();
}

// function to get zones
// "camp_price" decide the pricing mode. (0- pay per ad) (1-Ad wallet mode).
function getZonesdata(camp_price)
{
		techjoomla.jQuery('#ad_zone_id').val(techjoomla.jQuery('#adzone option:selected').val());

techjoomla.jQuery.ajax({
	url: root_url+'?option=com_socialads&task=getZonesdata&zone_id='+document.getElementById('adzone').value,
	type: 'GET',
	dataType: "json",
	success: function(data) {




		techjoomla.jQuery("#layout1").html('');
//document.getElementById('max_tit1').innerHTML=data[0].max_des;
		techjoomla.jQuery(".max_tit").val(data[0].max_title);		//hidden i/p box for max limits
if(document.getElementById('adtype').value != 'affiliate')
		techjoomla.jQuery(".max_body").val(data[0].max_des);		//

		techjoomla.jQuery("#max_tit1").text(data[0].max_title);	/// spans for max limits
if(document.getElementById('adtype').value != 'affiliate')
		techjoomla.jQuery("#max_body1").text(data[0].max_des);		//
		//techjoomla.jQuery("#sBann").prepend(data[0].max_title);
		//techjoomla.jQuery("#sBann1").prepend(data[0].max_des);
		techjoomla.jQuery('#sBann').show();
		techjoomla.jQuery('#sBann1').show();

		techjoomla.jQuery("#eBann").attr('maxlength',data[0].max_title);	//apply maxlength to the i/p box
if(document.getElementById('adtype').value != 'affiliate')
		techjoomla.jQuery("#eBann1").attr('maxlength',data[0].max_des);	//
		toCount('eBann','max_tit1','{CHAR}',techjoomla.jQuery('#sBann').text(),techjoomla.jQuery('#max_tit').val(),techjoomla.jQuery('#eBann').val(),'');
if(document.getElementById('adtype').value != 'affiliate')
		toCount1('eBann1','max_body1','{CHAR}',techjoomla.jQuery('#sBann1').text(),techjoomla.jQuery('#max_body').val(),techjoomla.jQuery('#eBann1').val(),'');

if(document.getElementById('adtype').value == 'affiliate')
		return;
		techjoomla.jQuery("#img_wid").text(data[0].img_width);
		techjoomla.jQuery("#img_ht").text(data[0].img_height);
		ad_layout_nm = selected_layout;

		for (i=0;i<data[0].layout.length;i++)		//showing the layout's radio buttons
		{
			sel = '';
			if(ad_layout_nm==data[0].layout[i])
			{
				sel = 'checked';
			}
			else if(i == 0) //needs to be veried
			{
				sel = 'checked';
			}
		//added by sagar
			joomla_version=document.getElementById('joomla_version').value;

			if(parseFloat(joomla_version)>=1.6)
			{

					techjoomla.jQuery("#layout1").append('<span class="layout_span span6"><input class="layout_radio" type="radio" name="layout" value="'+data[0].layout[i]+'" '+sel+' onclick="changelayout(this.value)" ><img class="layout_radio" src="'+data[0].base+'plugins/socialadslayout/plug_'+data[0].layout[i]+'/plug_'+data[0].layout[i]+'/layout.png" ></span>');

			}
			else
				techjoomla.jQuery("#layout1").append('<span class="layout_span span6"><input class="layout_radio" type="radio" name="layout" value="'+data[0].layout[i]+'" '+sel+' onclick="changelayout(this.value)" ><img class="layout_radio" src="'+data[0].base+'plugins/socialadslayout/plug_'+data[0].layout[i]+'/layout.png" ></span>');
		//added by sagar
			if(sel == 'checked')
				changelayout(data[0].layout[i]);

			if(techjoomla.jQuery("#upload_area").find("div").children("[name=upimg]").val() != null)
				techjoomla.jQuery('#upimg').val(techjoomla.jQuery('#upload_area').find('div').children('[name=upimg]').val());
			}

		if(document.getElementById('ad_image').value != '' && document.getElementById('adtype').value != 'text'	 )
		{
			ajaxUpload(document.adsform,'&filename=ad_image','upload_area','IMG_UP<br /><img src=\''+root_url+'/components/com_socialads/images/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' />','<img src=\'/components/com_socialads/images/error.gif\' width=\'16\' height=\'16\' border=\'0\' /> Error in Upload, check settings and path info in source code.');
		}



		if(document.getElementById('adtype').value == 'img' && data[0].layout.length == 1)		//for ad type= image
		{
			techjoomla.jQuery("#layout_div").hide();
		}
		else
			techjoomla.jQuery("#layout_div").show();

		techjoomla.jQuery("#pric_imp").val(data[0].per_imp);		//put pricing values in hidden fields
		techjoomla.jQuery("#pric_click").val(data[0].per_click);
		techjoomla.jQuery("#pric_day").val(data[0].per_day);
		techjoomla.jQuery("#pric_month").val(data[0].per_month);

			//if(document.getElementById('camp_dis').style.display=="none")		//call helper.php function
			//condition changed for wrong calculation while editing when changed adtype//
			if(camp_price==0)
			{
				if((document.getElementById('totalamount').value!=''))
				caltotal();
			}
		getzone_priceForInfo();
	}
});


}

function changelayout(rad)
{

	if(techjoomla.jQuery("#upload_area").find("div").children("[name=upimg]").val() != null)
		techjoomla.jQuery('#upimg').val(techjoomla.jQuery('#upload_area').find('div').children('[name=upimg]').val());
	var adtype=''+document.getElementById('adtype').value; /*added by manoj 2.7.5.beta.2*/
	var adzone=document.getElementById('adzone').options[document.getElementById('adzone').selectedIndex].value;/*added by manoj

	2.7.5 stable*/
	techjoomla.jQuery.ajax({
	url: root_url+'?option=com_socialads&task=changelayout&layout='+rad+'&title='+techjoomla.jQuery("#eBann").val()+'&body='+techjoomla.jQuery("#eBann1").val()+'&img='+techjoomla.jQuery('#upimg').val()+'&adtype='+adtype+'&adzone='+adzone,
	type: 'GET',
	async:false,
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
			eval(data.js);
		}
	},
	error:function(data)
	{
	}
});
}
	//function for validation of overview
	function open_div(geo,camp)
	{

		//------------------Remove comment-------------------
		//if(chkAdValid())
		//-------------------------------------
		{
			//alternatead button
			/*if(document.getElementById("altadbutton"))
			{
				if(document.getElementById("altadbutton").checked==true || document.getElementById('adtype').value == 'affiliate' )
				{
				  techjoomla.jQuery("#guestbutton").attr("disabled","disabled");
					//document.myForm.task.value = 'altad';
					document.getElementById('adsform').setAttribute('target','');
					document.getElementById('adsform').setAttribute('action','');
					document.myForm.submit();
					return;
				}
				else
				{
					//techjoomla.jQuery(".altbutton").css("display","none");
				}
			}*/
			//guestbutton
			if(document.getElementById("guestbutton"))
			{
				if(document.getElementById("guestbutton").checked==true)
				{
						//document.getElementById('btnWizardNext').style.display="none";
						if(geo=="1")
						{
							//document.getElementById('lowerdiv').style.display="block";//target0div show
							//document.getElementById('tab2_continue').style.display="block"; //target show
						}
						else
						{
							if(camp==1)
							{
								document.getElementById('camp_dis').style.display="block";
							}
							else
							{
								if(techjoomla.jQuery('#bottomdiv').length)
								{
									document.getElementById('bottomdiv').style.display="block";
								}
							}
							//document.getElementById('btnWizardNext').style.display="none";//ad hide
							//document.getElementById('lowerdiv').style.display="none";//target0div hide
							//document.getElementById('review').style.display="block";//pricing show
						}

					//------------------Remove comment-------------------
					//return false;
					//-------------------------------------
				}
				else
				{
					//techjoomla.jQuery(".altbutton").css("display","none");
				}

			}
			// code for geo targeting
			//document.getElementById('btnWizardNext').style.display="none";
			if(techjoomla.jQuery('#lowerdiv').length)
			{
				document.getElementById('lowerdiv').style.display="block";
			}
			//document.getElementById('tab2_continue').style.display="block";

			btnWizardNext();

			return true;
		}


	}//function open_div() ends here

	function onbackone(){

 	//document.getElementById('btnWizardNext').style.display="block";
	//document.getElementById('lowerdiv').style.display="none";
	techjoomla.jQuery(".altbutton").css("display","block");
	techjoomla.jQuery(".guestbutton").css("display","block");

	}

	function onbacktwo(camp,geo)
	{

		document.getElementById('review').style.display="none";
		if(camp==1)
		{
			document.getElementById('camp_dis').style.display="none";
		}
		else
		{
			//document.getElementById('bottomdiv').style.display="none";
		}

		if(geo==1)
		{
			//document.getElementById('tab2_continue').style.display="block";
		}
		else
		{
			//document.getElementById('btnWizardNext').style.display="block";
		}

		techjoomla.jQuery(".altbutton").css("display","block");

	}

	//call on guest checkbox
	function switchCheckboxguest( guestbutton, altadbutton )
	{
		if (techjoomla.jQuery('#guestbutton').is(':checked'))
		{
			techjoomla.jQuery('#altadbutton').attr('checked', false);
		}

	}//function switchCheckboxguest ends

	//call on alt checkbox
	function switchCheckboxalt()
	{
		var ischecked=0;
		var is_affliatead=0;
		if (techjoomla.jQuery('#altadbutton').is(':checked'))
		{
			techjoomla.jQuery('#guestbutton').attr('checked', false);
			ischecked=1;
		}
		if(document.getElementById('adtype').value == "affiliate")
			var is_affliatead	=	1;

		if(ischecked	== 1 || is_affliatead	==	1 || (showTargeting==0 && allowWholeAdEdit==0))
		{
			changenexttoexit(1);
		}
		else
		{
			changenexttoexit(0);
		}
	}
	function changenexttoexit(ischecked)
	{
		if (ischecked == 1)
		{
			techjoomla.jQuery('.prev_next_wizard_actions #btnWizardNext').removeClass('btn-primary');
			techjoomla.jQuery('.prev_next_wizard_actions #btnWizardNext').addClass('btn-success');
			techjoomla.jQuery('.prev_next_wizard_actions #btnWizardNext span').text(savenexitbtn_text);
		}
		else
		{
			techjoomla.jQuery('.prev_next_wizard_actions #btnWizardNext span').text(savennextbtn_text);
			techjoomla.jQuery('.prev_next_wizard_actions #btnWizardNext').addClass('btn-primary');
			techjoomla.jQuery('.prev_next_wizard_actions #btnWizardNext').removeClass('btn-success');
		}

	}

	//function switchCheckboxalt ends

	/*function to disabled checkbox-guest
	function hideguest(check)
	{
		if(check.checked==true)
		{
			techjoomla.jQuery("#guestbutton").attr("disabled","disabled");
		}
		else
		{
			techjoomla.jQuery("#guestbutton").removeAttr("disabled");
		}
	}

	//function to disabled checkbox-alternate
	function hidealt(check)
	{
		if(check.checked==true)
		{
			techjoomla.jQuery("#altadbutton").attr("disabled","disabled");
		}
		else
		{
			techjoomla.jQuery("#altadbutton").removeAttr("disabled");
		}
	}*/

	function hidePayment(selector)
	{

		if(selector==1)
		{
			techjoomla.jQuery("#totaldisplay").val('');
			techjoomla.jQuery("#ad_totalamount").html('');
			techjoomla.jQuery("#daterangefrom").val('');

			techjoomla.jQuery("#totaldays").html('');


			techjoomla.jQuery("#chargeoption").attr("disabled","disabled");
			techjoomla.jQuery("#totaldisplay").attr("disabled","disabled");
			techjoomla.jQuery("#datefrom").attr("disabled","disabled");
			techjoomla.jQuery("#dateto").attr("disabled","disabled");
			//techjoomla.jQuery("#gateway").attr("disabled","disabled");
			techjoomla.jQuery("#pricing_opt").val('');
			techjoomla.jQuery("#pricing_opt").attr("disabled","disabled");
			techjoomla.jQuery("#bid_value").attr("disabled","disabled");
			techjoomla.jQuery("select").trigger("liszt:updated");

		}
		else
		{
			techjoomla.jQuery("#chargeoption").removeAttr("disabled");
			techjoomla.jQuery("#totaldisplay").removeAttr("disabled");
			//techjoomla.jQuery("#gateway").removeAttr("disabled");
			techjoomla.jQuery("#datefrom").removeAttr("disabled");
			techjoomla.jQuery("#dateto").removeAttr("disabled");
			techjoomla.jQuery("#pricing_opt").removeAttr("disabled");
			techjoomla.jQuery("#bid_value").removeAttr("disabled");
			techjoomla.jQuery("select").trigger("liszt:updated");

		}
	}

	function selectallList(checkbox,id)
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
		  	 for (var i=0; i<test; i++)
	        {
				document.getElementById('mapdata'+id).options[i].selected=false;
			}

		  }
			return true;
	 }
	 function selectClicked(mulbox)
	 {
	 	document.getElementById('multiselect-'+mulbox).checked = false;
	 }

	//function for duration showing ad
	function open_bottomdiv(multistr,msg,datel,msg2,camp)
	{
		if(datel)
		{

			if(!checkdate(datel,'_low',msg2))
				return false;
			if(!checkdate(datel,'_high',msg2))
				return false;
		}


		if(camp==1)
		{
			document.getElementById('camp_dis').style.display="block";
		}
		else
		{
			if(techjoomla.jQuery('#bottomdiv').length)
			{
				document.getElementById('bottomdiv').style.display="block";
			}
		}

		//document.getElementById('tab2_continue').style.display="none";
		document.getElementById('review').style.display="block";
	}//open_bottomdiv() ends here

	function open_datebox()
	{
		document.getElementById('bottom1').style.display="block";
	}


	function close_datebox()
	{
		document.getElementById('bottom1').style.display="none";
	}

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



	/* function for checking multicheck options*/
	function checkAllMulti(multistr,msg)
	{
		multiarray = multistr.split(',');
		for(var j=0; j<multiarray.length; j++)
		{
			var flag = 0;
		   	var test=document.getElementById('mapdata'+multiarray[j]).options.length;
			for(var i=0; i<test; i++)
			{
				if(document.getElementById('mapdata'+multiarray[j]).options[i].selected==true)
				{
					flag=1;
				}
			}
			if(flag==0)
			{
				alert(msg);
				return false;
			}
		}
		return true;
	}

	/*function check reviews -on review submit*/



	function checkreview(mim_daily,mim_bid,camp,charge, msg2, currency,msgvaliddate, datemsg, wrongdates, invalid,chkcontextual)
	{
/*		if(!chkAdValid())
			return false;
		if (document.getElementById("context_target_data[][keywordtargeting]"))
		{
				contextual_keywords=document.getElementById("context_target_data[][keywordtargeting]").value.toString();
				if(document.getElementById('context_target').checked==true && (contextual_keywords==""))
				{
					alert(chkcontextual);
					return false;
				}
		}
*/

/*code for stripping the http:// from url*/
		var urlstring = document.getElementById('url2').value;
		var urlpointer = -1;
		urlpointer = urlstring.indexOf('http://');
		if(urlpointer == 0){
			var newstr = urlstring.substr(7);
			document.getElementById('url2').value = newstr;
		}
		if(camp==1)
		{

			if(!checkreview_camp(mim_daily,currency))
				{

					return false;
				}

					{
						var camp_price_opt = document.getElementById('pricing_opt').value;
						if(camp_price_opt=='value')				//check for payment_type selected or not
								{
										alert("Please select Pricing option");
										document.getElementById('pricing_opt').focus();
										return false;
								}
					}


			return true;
		}
	else
		{
		var form = document.adsform;
		var totaldisplay = form.totaldisplay.value;
		var totalamount = form.totalamount.value;
		var charge_points = 0;
		var daterangefrom = form.datefrom.value;

		var chargeoption = document.getElementById('chargeoption').value;
		var unlimited_ad = '';
		if(techjoomla.jQuery(":radio[name=unlimited_ad]").length > 0)
			unlimited_ad=techjoomla.jQuery("input[name=unlimited_ad]:radio:checked").val();


		if(document.getElementById('sa_recuring').checked==true)
		{
			totalamount=(document.getElementById('totaldays').value)*totalamount;
		}

		if((parseInt(chargeoption) >= 2) &&  (unlimited_ad != 1) )
		{
			if((daterangefrom == ' ' ) && (parseInt(chargeoption)== 2))
			{
				alert(datemsg);
				return false;
			}
			var now=new Date();
			var year = now.getFullYear();
			var month = now.getMonth()+1;
			var date = now.getDate();
			if(date >=1 && date <=9)
			{
				var newdate = '0'+date;
			}
			else
			{
				var newdate = date;
			}
			if(month >=1 && month <=9)
			{
				var newmonth = '0'+month;
			}
			else
			{
				var newmonth = month;
			}

			today = year+'-'+newmonth+'-'+newdate;

			if((daterangefrom) < (today))
			{
				alert(wrongdates);
				return false;
			}
			var daycount=document.getElementById("totaldays").value;

			if(isNaN(daycount) || daycount<=0)
			{
				alert(msgvaliddate);
				document.getElementById("totaldays").focus();
				return false;
			}
		}

	 	if((parseInt(chargeoption)!=2) && (unlimited_ad != 1))
		{
			if(parseInt(chargeoption)<2)
			{
				if(totaldisplay == '' ){
					alert(invalid);
					document.getElementById('totaldisplay').focus();
				return false;
				}

		 		if(isNaN(totaldisplay) || (totaldisplay <= 0)){
					alert(invalid);
					document.getElementById('totaldisplay').focus();
					return false;
				}
			}
		}

		if(parseFloat(totalamount) < parseFloat(charge))
		{
			alert(msg2);
			document.getElementById('totaldisplay').focus();
			return false;
		}


//		document.getElementById('adsform').setAttribute('target','');
//		document.getElementById('adsform').setAttribute('action','');
	}//else end
	return true;
	}
	//function checkreviews ends here

	//validation for campaign layout in buildad.
	function checkreview_camp(mim_daily,currency)
	{
		var ncamp = document.getElementById('camp').value;
		if(document.getElementById('new_campaign').style.display=='block')
		{
			var camp_name = document.getElementById('camp_name').value;
			var camp_amount = document.getElementById('camp_amount').value;

			if(camp_name=='' && ncamp=='0')			//both list camp or new camp not present
			{
				alert("Enter Campaign");
				document.getElementById('camp_name').focus();
				return false;
			}
			if((camp_name && camp_amount=='') || (camp_name && parseFloat(camp_amount) < parseFloat(mim_daily)))		//if new camp then check for daily budget
			{
				alert("The Minimum Allowed Daily Budget for Campaigns is "+mim_daily+" "+currency+". Please enter a value larger than that.");
				document.getElementById('camp_amount').focus();
				return false;
			}

		}
		else
		{
			if(ncamp=='0')		//if new camp is none then check for select list camp
			{
					alert("Please select a campaign");
					document.getElementById('camp').focus();
					return false;
			}
		}
	//		document.getElementById('adsform').setAttribute('target','');
	//		document.getElementById('adsform').setAttribute('action','');

		return true;
	}

	function campaignValidation()
	{
		if(document.getElementById('new_campaign').style.display=='block')
		{
			var camp_name = document.getElementById('camp_name').value;
			var camp_amount = document.getElementById('camp_amount').value;

			//both list camp or new camp not present
			if(camp_name=='' && camp_amount==0)
			{
				alert("Enter Campaign");
				document.getElementById('camp_name').focus();
				return false;
			}

			//if new camp then check for daily budget
			if((camp_name && camp_amount=='') || (camp_name && parseFloat(camp_amount) < parseFloat(camp_currency_daily)))
			{
				alert("The Minimum Allowed Daily Budget for Campaigns is "+camp_currency_daily+" "+currency+". Please enter a value larger than that.");
				document.getElementById('camp_amount').focus();
				return false;
			}
		}
		else
		{
			var selectedCampaign=techjoomla.jQuery('#camp option:selected').val();
			if(selectedCampaign==0)
			{
				alert('Please select the campaign');
				return false;
			}
		}
		return true;
	}
//function camp_review ends

	/* vm:this function allow only numberic and specified char (at 0th position)
	// ascii (code 43 for +) (48 for 0 ) (57 for 9)  (45 for  - (negative sign))
				(code 46 for dot/full stop .)
		@param el :: html element
		@param allowed_ascii::ascii code that shold allow

	*/
	function ad_checkforalpha(el, allowed_ascii,enter_numerics )
	{
		allowed_ascii= (typeof allowed_ascii === "undefined") ? "" : allowed_ascii;
		var i =0 ;
		for(i=0;i<el.value.length;i++)
		{
		  if((el.value.charCodeAt(i) <= 47 || el.value.charCodeAt(i) >= 58) || (el.value.charCodeAt(i) == 45 ))
		  {

		  		if(allowed_ascii ==el.value.charCodeAt(i) )  //&& i==0)  // + allowing for phone no at first char
					{
						var temp=1;
					}
					else
					{
							alert(enter_numerics);
							el.value = el.value.substring(0,i);
							return false;
					}


		  }
		}
		return true;
	}
	/*
	var statebackup;

	function ads_generateState(countryId,Dbvalue,selOptionMsg,root_url)
	{
		var country=techjoomla.jQuery('#'+countryId).val();
		if(country==undefined)
		{
			return (false);
		}
		techjoomla.jQuery.ajax({
			url: root_url+'?option=com_socialads&controller=checkout&task=loadState&country='+country+'&tmpl=component&format=raw',
			type: 'GET',
			dataType: 'json',
			success: function(data)
			{
				if(countryId=='country')
				{
					statebackup=data;
				}
				generateoption(data,countryId,Dbvalue,selOptionMsg);
			}
		});
	}

	function generateoption(data,countryId,Dbvalue,selOptionMsg)
	{
		var country=techjoomla.jQuery('#'+countryId).val();
		var options, index, select, option;

		// add empty option according to billing or shipping
		select = techjoomla.jQuery('#state');
		default_opt = selOptionMsg; //"<?php echo JText::_('ADS_BILLIN_SELECT_STATE')?>";

		// REMOVE ALL STATE OPTIONS
		select.find('option').remove().end();

		// To give msg TASK  "please select country START"
		selected="selected=\"selected\"";
		var op='<option '+selected+' value="">'  +default_opt+   '</option>'     ;
		techjoomla.jQuery('#state').append(op);
		 // END OF msg TASK

		if(data)
		{
			options = data.options;
			for (index = 0; index < data.length; ++index)
			{
				var name=data[index];
				selected="";
				if(name==Dbvalue)
				{
					selected="selected=\"selected\"";
				}
				var op='<option '+selected+' value=\"'+data[index]+'\">'  +data[index]+   '</option>';

				techjoomla.jQuery('#state').append(op);
			}	 // end of for
		}
	}
	*/
	function hideNewCampaign()
	{
		techjoomla.jQuery("#new_campaign").hide();
		populateCampaign();
	}

