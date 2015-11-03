jQuery(document).ready(function(){
	jQuery( '.yes_no_toggle label' ).on( "click", function() {
		var radio_value	=	toggle_classes(this);
		if(jQuery(this).parent().attr('id') == 'allow_vid_ads'){
			show_autoplay_tr(radio_value,'.autoplay_video');
		}
		if(jQuery(this).parent().attr('id') == 'select_campaign'){
			camp_hide(radio_value,'.camp_price');
		}
		if(jQuery(this).parent().attr('id') == 'show_slab'){
			hideshow(radio_value,'.slab_tr_hide');
		}
		if(jQuery(this).parent().attr('id') == 'article'){
			hideshow(radio_value,'.tncclass');
		}
		if(jQuery(this).parent().attr('id') == 'priority_random'){
			hideshow(radio_value,'.priority_tr');
		}
		if(jQuery(this).parent().attr('id') == 'geo_target'){
			hideshow(radio_value,'.geo_target_tr');
		}
		if(jQuery(this).parent().attr('id') == 'context_target'){
			hideshow(radio_value,'.context_target_tr');
			if(radio_value==0)
			{
				hideshow(radio_value,'#context_target_keywordsearch_id');
				hideshow(radio_value,'.contextual_smartsearch_cron_tr');
			}
			else
			{
				var selected = jQuery("input[type='radio'][name='config[context_target_keywordsearch]']:checked").val();
				if (selected == 1 || selected == "1" ) {
						hideshow(1,'#context_target_keywordsearch_id');
					//selectedVal = selected.val();
				}
				else
				{
					hideshow(0,'#context_target_keywordsearch_id');
				}

				var selected = jQuery("input[type='radio'][name='config[context_target_smartsearch]']:checked").val();
				if (selected == 1 || selected == "1" ) {
						hideshow(1,'.contextual_smartsearch_cron_tr');
					//selectedVal = selected.val();
				}
				else
				{
					hideshow(0,'.contextual_smartsearch_cron_tr');
				}
			}
		}
		if(jQuery(this).parent().attr('id') == 'context_target_keywordsearch'){
			hideshow(radio_value,'#context_target_keywordsearch_id');
		}
		if(jQuery(this).parent().attr('id') == 'context_target_smartsearch'){
			hideshow(radio_value,'.contextual_smartsearch_cron_tr');
		}
	});

});
function toggle_classes(thisradiolable)
{
		var radio_id	=	jQuery(thisradiolable).attr('for');

		jQuery('#'+radio_id).attr('checked', 'checked');

		/*for jQuery 1.9 and higher*/
		jQuery('#'+radio_id).prop("checked", true)

		var radio_btn = jQuery('#'+radio_id);
		var radio_value=radio_btn.val();

		var radio_name	=	jQuery('#'+radio_id).attr('name');
		var target_div	=	radio_name	+"_div"
		jQuery(thisradiolable).parent().find('label').removeClass('btn-success').removeClass('btn-danger');
		if(radio_value	== 1)
		{
			jQuery(thisradiolable).addClass('btn-success');
		}
		if(radio_value	== 0)
		{
			jQuery(thisradiolable).addClass('btn-danger');
		}
		return radio_value;
}
