function sa_init(thisad,module_id, ad_rotationdelay)
		{
			/*console.log('ininit');*/
			var preview_for	=	techjoomla.jQuery(thisad).attr('preview_for');
			var zone_id	=	techjoomla.jQuery(thisad).parent().attr('havezone');
			var ad_type		=	techjoomla.jQuery(thisad).find('.adtype').attr('adtype');
			if(ad_type != 'video'){
				techjoomla.jQuery(thisad).show(decidetororate(preview_for,zone_id,module_id,ad_type, ad_rotationdelay));
			}
			else{
				/*console.log(flowplayer('vid_player_'+ preview_for).getState());*/
				if(flowplayer('vid_player_'+ preview_for).getState() != -1 )
				{
				var t = setTimeout(function() {  decidetororate(preview_for,zone_id,module_id,ad_type, ad_rotationdelay); }, 5000);
				}
				else{
				flowplayer('vid_player_'+ preview_for).onLoad(function()  {
						var t = setTimeout(function() {  decidetororate(preview_for,zone_id,module_id,ad_type, ad_rotationdelay); }, 5000);
					});
				}
			}
		}


		function getadforswitch(this_addiv,zone_id,module_id,ad_id,cntset,ad_entry_number,ad_type, ad_rotationdelay){
				var donotrotate	= 0;
				countdown = setTimeout(function() {
					var isHovered = techjoomla.jQuery(this_addiv).is(":hover");
					if(isHovered)
						donotrotate=1;

					if(ad_type == 'video'){
							var state	=	flowplayer('vid_player_'+ ad_id).getState();
							/*console.log('cnt' + state);*/
							if(state == 3)
								donotrotate=1;
					}


					if(donotrotate	== 1){
						sa_init(this_addiv,module_id, ad_rotationdelay);
					}
					else{
						var switch_addata	=	checkifadsavailable(ad_id,zone_id,module_id);
						if(switch_addata.ad_id){
							var switch_ad_html=	getAdHTML(switch_addata.ad_id,module_id);
							techjoomla.jQuery(this_addiv).replaceWith(switch_ad_html);

							var switched_ad	=	techjoomla.jQuery('div[preview_for="'+switch_addata.ad_id+'"]');
							techjoomla.jQuery(switched_ad).attr("ad_entry_number",ad_entry_number);
							sa_init(switched_ad,module_id, ad_rotationdelay);
						}
					}

				}, cntset);

		}
		function decidetororate(preview_for,zone_id,module_id,ad_type, ad_rotationdelay){
				//console.log('decidetororate');
				var this_addiv	=	techjoomla.jQuery( "div[preview_for="+preview_for+"]" );
				var ad_entry_number	=	techjoomla.jQuery(this_addiv).attr('ad_entry_number');
				//console.log(preview_for);


				if(ad_type == 'video'){
					var cli_state	=	flowplayer('vid_player_'+ preview_for).getState();
					/*console.log('load' + cli_state);*/
						var cntset	= ad_entry_number *  ad_rotationdelay * 1000;
					if(cli_state == 4 || cli_state == -1)
					{
						//var this_video_div	=	techjoomla.jQuery( "div[preview_for="+preview_for+"]" );
						var cntset	= ad_entry_number *  ad_rotationdelay * 1000;
					}
					else if(cli_state == 3){

							var cntset = flowplayer('vid_player_'+ preview_for).getClip().fullDuration;

					}
				}
				else{
					var cntset	= ad_entry_number *  ad_rotationdelay * 1000;
				}


				getadforswitch(this_addiv,zone_id,module_id,preview_for,cntset,ad_entry_number,ad_type, ad_rotationdelay);

		}

		function checkifadsavailable(ad_id,zone_id,module_id)
		{
			/*console.log(module_id);*/
			var ad_data='';
			techjoomla.jQuery.ajax({
				url: site_link + "index.php?option=com_socialads&task=checkifadsavailable",
				async : false,
				type: 'POST',
				dataType: "html",
				data: {
					ad_id	:ad_id,
					zone_id:zone_id,
					module_id:"'"+module_id+"'",
				},
				success: function (response) {
					ad_data	=	techjoomla.jQuery.parseJSON(response);
				}
			});
			return	ad_data;
		}
		function getAdHTML(ad_id,module_id)
		{
			var ad_html='';
			techjoomla.jQuery.ajax({
				url: site_link + "index.php?option=com_socialads&task=getAdHTML",
				async : false,
				type: 'POST',
				dataType: "html",
				data: {
					ad_id:ad_id,
					module_id:module_id
				},
				success: function (response) {
					ad_html	=	response;
				}
			});
			return	ad_html;

		}
