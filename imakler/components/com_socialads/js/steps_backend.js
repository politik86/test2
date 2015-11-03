jQuery(document).ready(function(){

	jQuery('#MyWizard').on('change', function(e, data) {


		if(techjoomla.jQuery("#sa_BillForm").length)
		{
			values=jQuery('#adsform,#sa_BillForm').serialize();
		}
		else
		{
			values=jQuery('#adsform').serialize();
		}

		//Get tab ID
		var ref_this = jQuery("#sa-steps li[class='active']");
		var stepId = ref_this[0].id;

		if(data.direction==='next')
		{

			//Check if unlimited ad radio value
			var unlimited_ad='';
			if(jQuery(":radio[name=unlimited_ad]").length > 0)
				unlimited_ad=jQuery("input[name=unlimited_ad]:radio:checked").val();

			//First step1 validation
			if(!chkAdValid(stepId,unlimited_ad))
			{
				return false;
			}

			loadingImage();

			//var stepId = data.step;  // added by vm
			jQuery.ajax({
				url: base_url+'?option=com_socialads&controller=buildad&task=autoSave&stepId='+stepId+'&tmpl=component&format=raw',
				type: 'POST',
				async:false,
				data:values,
				dataType: 'json',
				beforeSend: function() {
					/*//console.log('___ befor send *');
					techjoomla.jQuery('#confirm-order').after('<div class=\"com_jgive_ajax_loading\"><div class=\"com_jgive_ajax_loading_text\">".JText::_('COM_JGIVE_LOADING_PAYMET_FORM_MSG')."</div><img class=\"com_jgive_ajax_loading_img\" src=\"".JUri::root()."components/com_jgive/assets/images/ajax.gif\"></div>');
					// CODE TO HIDE EDIT LINK
					jgive_hideAllEditLinks(); */
				},
				complete: function() {
					/*
					techjoomla.jQuery('#jgive_order_details_tab').show()
					techjoomla.jQuery('.com_jgive_ajax_loading').remove();
					jgive_showAllEditLinks();
					*/
				},
				success: function(response)
				{
					//redirect to the manage ad after geo targetting data fill up
					if(stepId=='ad-design' && data.direction==='next')
					{
						var altadbutton=0;
						var affiliate=0;

						if(techjoomla.jQuery('#altadbutton').length)
						{
							if(document.getElementById("altadbutton").checked==true )
							{
								altadbutton=1;
							}
						}

						if(techjoomla.jQuery('#adtype').length)
						{
							if(document.getElementById('adtype').value == 'affiliate')
							{
								affiliate=1;
							}
						}

						if( (altadbutton==1) || (affiliate==1))
						{
							window.location.assign(base_url+"?option=com_socialads&controller=buildad&task=windowLocation");
							return e.preventDefault();
						}
					}
					//Unlimited ad =Yes then redirect to the manage ad after geo targetting data fill up
					if(stepId=='ad-pricing' && data.direction==='next')
					{
						if(unlimited_ad==1)
						{
							window.location.assign(base_url+"?option=com_socialads&controller=buildad&task=windowLocation");
							return e.preventDefault();
						}

						//Select newly added campaign
						if(response['camp_id'])
						{
							newCampaignSelect(response['camp_id'])
						}

					}

					//IF ad payment is confrimed then allow only edit of ad desing & geo targeting ()
					if((stepId=='ad-targeting' && allowWholeAdEdit==0) || (showTargeting==0 && allowWholeAdEdit==0))
					{
						window.location.assign(base_url+"?option=com_socialads&controller=buildad&task=windowLocation");
						return e.preventDefault();
					}

					// vm: start
					//add billing details

					if(response['billingDetail'])
					{
						techjoomla.jQuery('.sa_build_ad_billing').html(response['billingDetail']);
					}
					//if((stepId==3 && sa_hide_billTab==1) || stepId==4)

					// add payment relreated detail
					if(response['payAndReviewHtml'])
					{
						techjoomla.jQuery('#ad_reviewAndPayHTML').html(response['payAndReviewHtml']);
					}
					// vm: end


					//Amol : start
					if(response['adPreviewHtml'])
					{
						techjoomla.jQuery('#adPreviewHtml').html(response['adPreviewHtml']);
					}
					//Amol : end
				},
				error: function(response) {
					// show ckout error msg
					console.log(' ERRORRR' );
				}
			});

			setTimeout(function(){ hideImage() },10);
		}

		// Scroll to top
		techjoomla.jQuery('html,body').animate({scrollTop: techjoomla.jQuery("#sa-steps").offset().top},'slow');

	  if(data.step===1 && data.direction==='next') {
		// return e.preventDefault();
	  }
	});

	jQuery('#MyWizard').on('changed', function(e, data) {

		// The save & exit button remains same even if we navigate to first tab hence added code
		changenexttoexit(0);

		var thisactive = jQuery("#sa-steps li[class='active']");
		stepthisactive = thisactive[0].id;
		if(stepthisactive == jQuery("#sa-steps li").first().attr('id'))
			jQuery(".ad-form #btnWizardPrev").hide();
		else
			jQuery(".ad-form #btnWizardPrev").show();

		if(stepthisactive == jQuery("#sa-steps li").last().attr('id')){
			jQuery(".ad-form .prev_next_wizard_actions").hide();
			var prev_button_html='<button id="btnWizardPrev1" onclick="jQuery(\'#MyWizard\').wizard(\'previous\');"	type="button" class="btn btn-prev" > <i class="icon-circle-arrow-left icon-white"></i>Prev</button>';

			if(stepthisactive == "ad-summery" ){
				jQuery('#ad_payHtmlDiv div.form-actions').prepend( prev_button_html );
				techjoomla.jQuery('#ad_payHtmlDiv div.form-actions input[type="submit"]').addClass('pull-right');
			}
			if(stepthisactive == "ad-review" ){
				jQuery('.ad_reviewAdmainContainer div.form-actions').prepend( prev_button_html );
			}
		}
		else
			jQuery(".ad-form .prev_next_wizard_actions").show();

		if(stepthisactive=='ad-targeting' && allowWholeAdEdit==0)
		{
			changenexttoexit(1);
		}
	});
	jQuery('#MyWizard').on('finished', function(e, data) {
	});
	jQuery('#btnWizardPrev').on('click', function() {
	  jQuery('#MyWizard').wizard('previous');
	});

	/*
	 jQuery('#btnWizardNext').on('click', function()
	{
		jQuery('#MyWizard').wizard('next','foo');
	});
	*/

	jQuery('#btnWizardStep').on('click', function() {
	  var item = jQuery('#MyWizard').wizard('selectedItem');
	});
	jQuery('#MyWizard').on('stepclick', function(e, data) {
	  if(data.step===1) {
		// return e.preventDefault();
	  }
	});

	// optionally navigate back to 2nd step
	jQuery('#btnStep2').on('click', function(e, data) {
	  jQuery('[data-target=#step2]').trigger("click");
	});

});

function btnWizardNext()
{
	jQuery('#MyWizard').wizard('next','foo');
}
