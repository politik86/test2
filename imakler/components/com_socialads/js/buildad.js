techjoomla.jQuery(document).ready(function(){
	techjoomla.jQuery('.ad-form select').attr('data-chosen', 'com_socialads');
	Adchange();
	show_hide_geo("geo_target");
	show_hide_geo("social_target");
	show_hide_geo("context_target");

	techjoomla.jQuery(".promotplglist").change(function(){
    		techjoomla.jQuery.ajax({
   			url: "?option=com_socialads&controller=buildad&task=getPreviewData&id="+(techjoomla.jQuery(".promotplglist").val()),
   			type: "GET",
   			dataType: "json",
   			success: function(msg)
   			{


   				 techjoomla.jQuery("#upload_area").html(msg.image);


   				 techjoomla.jQuery("#upimg").val(msg.imagesrc);

   				 techjoomla.jQuery("#addataad_url1").val(msg.url1);
   				 techjoomla.jQuery("#url2").val(msg.url2);
   				 techjoomla.jQuery("#upimgcopy").val(msg.imagesrc);
   				 techjoomla.jQuery("#eBann").val(msg.title);
   				 techjoomla.jQuery("#eBann1").val(msg.bodytext);


   				changelayout(techjoomla.jQuery("input[name=layout]:radio:checked").val());
   			}
   		});
   	});
   	techjoomla.jQuery(".target").click(function()
	{
		show_hide_geo(this.id);
	});
	//Edit ad => Show or Hide Targeting Divs
		var geo_target=techjoomla.jQuery("input[name=geo_target]:radio:checked").val();

		if(geo_target==1)
		{
			techjoomla.jQuery("#geo_target_div").show("slow");
		}
		else
		{
			techjoomla.jQuery("#geo_target_div").hide("slow");
		}

		var social_target=techjoomla.jQuery("input[name=social_target]:radio:checked").val();

		if(social_target==1)
		{
			techjoomla.jQuery("#social_target_div").show("slow");
		}
		else
		{
			techjoomla.jQuery("#social_target_div").hide("slow");
		}

		var context_target=techjoomla.jQuery("input[name=context_target]:radio:checked").val();

		if(context_target==1)
		{
			techjoomla.jQuery("#context_target_div").show("slow");
		}
		else
		{
			techjoomla.jQuery("#context_target_div").hide("slow");
		}

		var unlimited_ad=techjoomla.jQuery("input[name=unlimited_ad]:radio:checked").val();
		//var unlimited_ad=techjoomla.jQuery("input[name=unlimited_ad] radio:checked").val();
		if(unlimited_ad==1)
		{
			hidePayment(1);
		}
});

function promotplglistOnchange()
{
	techjoomla.jQuery.ajax({
		url: "?option=com_socialads&controller=buildad&task=getPreviewData&id="+(techjoomla.jQuery(".promotplglist").val()),
		type: "GET",
		dataType: "json",
		success: function(msg)
		{
			techjoomla.jQuery("#upload_area").html(msg.image);
			techjoomla.jQuery("#upimg").val(msg.imagesrc);
			techjoomla.jQuery("#addataad_url1").val(msg.url1);
			techjoomla.jQuery("#url2").val(msg.url2);
			techjoomla.jQuery("#upimgcopy").val(msg.imagesrc);
			techjoomla.jQuery("#eBann").val(msg.title);
			techjoomla.jQuery("#eBann1").val(msg.bodytext);
			changelayout(techjoomla.jQuery("input[name=layout]:radio:checked").val());
		}
	});
}

/*2.7.5 beta1*/
function hideUpload(show_elm,hide_elm){
	document.getElementById(show_elm).style.display="block";
	document.getElementById(hide_elm).style.display="none";
}
function inserturl(){
	var blanklist = document.getElementById('addatapluginlist').value = '';
	document.getElementById('promotplugin').style.display='none';
	techjoomla.jQuery('#destination_url').show();
}
function show_cop(){

	if(techjoomla.jQuery('#sa_coupon_chk').is(':checked'))
		techjoomla.jQuery('#sa_cop_tr').show();
	else
	{
		// hide all sa releated things
		techjoomla.jQuery('.sa_cop_details').hide();
		techjoomla.jQuery('#sa_cop_tr').hide();

		// make cop empty
		techjoomla.jQuery('#sa_coupon_code').val('');
	}
}

function ad_gatewayHtml(ele,orderid,payPerAd,loadingMsg,loadingImgPath)
{
	techjoomla.jQuery.ajax({
		url: '?option=com_socialads&controller=buildad&task=ad_gatewayHtml&payPerAd='+payPerAd+'&gateway='+ele+'&order_id='+orderid+'&tmpl=component&format=raw',
		type: 'POST',
		data:'',
		dataType: 'text',
		beforeSend: function()
		{
			techjoomla.jQuery('#sa_paymentGatewayList').after('<div class=\"com_socialad_ajax_loading\"><div class=\"com_socialad_ajax_loading_text\">'+loadingMsg+' ...</div><img class=\"com_socialad_ajax_loading_img\" src="'+root_url+'components/com_socialads/images/ajax.gif"></div>');

		},
		complete: function() {
			techjoomla.jQuery('.com_socialad_ajax_loading').remove();

		},
		success: function(data)
		{
			if(data)
			{
				techjoomla.jQuery('#ad_payHtmlDiv').html(data);
				techjoomla.jQuery('#ad_payHtmlDiv div.form-actions input[type="submit"]').addClass('pull-right');
				var prev_button_html='<button id="btnWizardPrev1" onclick="techjoomla.jQuery(\'#MyWizard\').wizard(\'previous\');"	type="button" class="btn btn-primary pull-left" > <i class="icon-circle-arrow-left icon-white" ></i>Prev</button>';
				techjoomla.jQuery('#ad_payHtmlDiv div.form-actions').prepend( prev_button_html );


			}

		}
	});
}

function getPromoterPlugin(uid)
{
	techjoomla.jQuery.ajax({
		url:base_url+'?option=com_socialads&controller=buildad&task=promoterPlugin&tmpl=component&format=raw&uid='+uid,
		type: 'POST',
		dataType:'json',
		data:'',
		success:function(response)
		{
			techjoomla.jQuery("#promote_plg_select #addatapluginlist").html(response['select_promote_plg_html']);
			techjoomla.jQuery("select").trigger("liszt:updated");  /* IMP : to update to chz-done selects*/
		},
		error:function(response)
		{
			console.log('ERROR');
		}
	});
}

function showHide(value,div_id)
{

	if(value)
	{
		techjoomla.jQuery("#"+div_id).show("slow");
	}
	else
	{
		techjoomla.jQuery("#"+div_id).hide("slow");
	}
}
function jSelectUser_jform_created_by(id, title)
{
	var old_id = document.getElementById("ad_creator_id").value;
	if (old_id != id)
	{
		document.getElementById("ad_creator_id").value = id;
		document.getElementById("ad_creator_name").value = title;

		//Get selected user social promoter plugins
		getPromoterPlugin(id);
		//Get selected user campaign
		getUserCampaign(id);
	}
	SqueezeBox.close();

}




//vm:
function paymentList_showHide()
{
	// Get the DOM reference of bill details
	var billEle = document.getElementById("sa_paymentlistWrapper");

	if(document.getElementById('sa_termsCondCk').checked)
	{
		billEle.style.display = "block";
	}
	else
	{
		// if not visible then show
		billEle.style.display = "none";
	}
}
	//Select newly added campaign
	function newCampaignSelect(camp_id)
	{
		if(techjoomla.jQuery("#new_campaign").length)
		{
			techjoomla.jQuery("#new_campaign").hide();
		}

		var camp_name = techjoomla.jQuery('#camp_name').val();
		var option = "<option value="+camp_id+" selected='selected'>"+camp_name+"</option>";
		var select = techjoomla.jQuery('#camp');
		select.append(option);

	}

	function loadingImage()
	{
		techjoomla.jQuery('<div id="appsloading"></div>')
		.css("background", "rgba(255, 255, 255, .8) url('"+root_url+"components/com_socialads/images/ajax.gif') 50% 15% no-repeat")
		.css("top", techjoomla.jQuery('#TabConetent').position().top - techjoomla.jQuery(window).scrollTop())
		.css("left", techjoomla.jQuery('#TabConetent').position().left - techjoomla.jQuery(window).scrollLeft())
		.css("width", techjoomla.jQuery('#TabConetent').width())
		.css("height", techjoomla.jQuery('#TabConetent').height())
		.css("position", "fixed")
		.css("z-index", "1000")
		.css("opacity", "0.80")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.appendTo('#TabConetent');
	}
	function hideImage()
	{
		techjoomla.jQuery('#appsloading').remove();
	}
