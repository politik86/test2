<?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
?>

<script language="javascript" type="text/javascript">

	var imagearray = [<?php echo $realimgs?>];

	function changeDisplayImage() {
		var imageFolder = '<?php echo $lists['image_directory'] ?>';
		var imgSrc = document.adminForm['image_url'].value;
		foundimg = false;
		mydiv = document.getElementById("imgdiv");
		for(i = 0; i < imagearray.length; i ++)
			if(imagearray[i][2] == imgSrc)
			{
				foundimg = true;
				document.adminForm['width'].value = imagearray[i][0];
				document.adminForm['height'].value = imagearray[i][1];
  				mydiv.innerHTML = '<img alt="preview" src="'+imageFolder+'/'+imgSrc+'" id="imgs" />';
			}
	if(!foundimg)
	{
	  mydiv.innerHTML = '';
	  document.adminForm['width'].value = "";
	  document.adminForm['height'].value = "";
	}

	return;
	}

		ADAG(function(){

            ADAG('#change_cb').openDOMWindow({
                height: 450,
                width: 600,
                positionTop: 50,
                eventType: 'click',
                positionLeft: 50,
                windowSource: 'iframe',
                windowPadding: 0,
                loader: 1,
                loaderImagePath: '<?php echo JURI::root()."components/com_adagency/images/loading.gif"; ?>',
                loaderHeight: 31,
                loaderWidth: 31
            });
            ADAG('#close_cb').closeDOMWindow({eventType:'click'});

        <?php
            for ($i=0;$i<count($camps);$i++) {
                // store adslimit & totalbanners for each campaign
                // for later checks
        ?>
                $current_camp = ADAG('#adv_cmp<?php echo $i+1; ?>');
                if ($current_camp.length) {
                    ADAG.data($current_camp[0], 'adslim', '<?php
                        if ( isset( $camps[$i]->params['adslim'] ) ) {
                            $adslim = $camps[$i]->params['adslim'];
                        } else {
                            $adslim = '999';
                        }
                        echo $adslim;
                    ?>');
                    ADAG.data($current_camp[0], 'totalbanners', '<?php
                        if ( isset( $camps[$i]->totalbanners ) ) {
                            echo $camps[$i]->totalbanners;
                        } else {
                            echo '0';
                        }
                    ?>');
                    // check if campaign has reached max number of ads
                    // and don't allow banner assignment if so
                    $current_camp.live('click', function(event) {
                        var adslim = ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'adslim'),
                        totalbanners = ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'totalbanners'),
                        availableads = adslim - totalbanners,
                        val = ADAG(this).val();
					
                        if ( !ADAG(this).prop('checked') ) {
                            newTotalbanners = parseInt(totalbanners,10) - 1;
                            ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'totalbanners', newTotalbanners);
                        } else {
                            //alert("Available ads: " + adslim + " - " + totalbanners + " = " + availableads);
                            if ( (availableads <= 0) && (ADAG('#DOMWindow').length == 0) ) {
                                event.preventDefault();
                                window.setTimeout(function() {
                                    //alert(val);
                                    var answer = confirm('<?php echo JText::sprintf('ADAG_CMP_LIMIT_AD_WARN2', $adslim); ?>');
                                    if (answer) {
                                        ADAG('#change_cb').prop(
                                            'href',
                                            '<?php echo JURI::root(); ?>index.php?option=com_adagency&controller=adagencyCampaigns&task=changecb&id=' + val + '&tmpl=component'
                                        ).click();
                                    }
                                    // ADAG('#close_cb').click();
                                }, 1);
                            } else if (availableads <= 0) {
                                event.preventDefault();
                            } else {
                                newTotalbanners = parseInt(totalbanners,10) + 1;
                                ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'totalbanners', newTotalbanners);
                            }
                        }

                    });
                }
        <?php
            }
        ?>

			ADAG('.close_it').click(function(){
				ADAG('#ajax_adv').remove();
			});
			ADAG('#approve_and_email').click(function(){
				var aid = ADAG('#advertiser_aid').val();
				ADAG.ajax({
				  url: 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=ajax_adv&aid='+aid+'&no_html=1&tmpl=component',
				  success: function(data) {
					data = ADAG.trim(data);
					if(data == 'ok'){
						ADAG('#ajax_adv').html('<?php echo JText::_('ADVAPPROVED')."<br /><br /><span class=\'close_it\' onclick=\'document.getElementById(\"ajax_adv\").style.display=\"none\"\' style=\'font-weight:bold; text-decoration: underline; cursor: pointer;\' >".JText::_('ADAG_CLOSE')."</span>"; ?>');
					}
					//alert('Load was performed: |' + data + '|');
				  }
				});
			});
			ADAG('#approve_no_email').click(function(){
				var aid = ADAG('#advertiser_aid').val();
				ADAG.ajax({
				  url: 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=ajax_adv&aid='+aid+'&no_html=1&tmpl=component&sendmail=0',
				  success: function(data) {
				  	data = ADAG.trim(data);
					if(data == 'ok'){
						ADAG('#ajax_adv').html('<?php echo JText::_('ADVAPPROVED')."<br /><br /><span class=\'close_it\' onclick=\'document.getElementById(\"ajax_adv\").style.display=\"none\"\' style=\'font-weight:bold; text-decoration: underline; cursor: pointer;\' >".JText::_('ADAG_CLOSE')."</span>"; ?>');
					}
					//alert('Load was performed: |' + data + '|');
				  }
				});
			});
		});

	function refreshAdv(id){
		var id = id || null;
		ADAG.get('index.php?option=com_adagency&controller=adagencyAdvertisers&task=getAdvertisersAjax&no_html=1', function(data) {
				if(data != ''){
					var sel = '<?php echo JText::_('AD_SELECT_ADVERTISER'); ?>';
					var toAppend = '<option value="0">'+sel+'</option>';
					var temp = data.split('|');
					for(var i=0;i<=temp.length-1;i++){
						var temp2 = temp[i].split(',');
						if((id != null)&&(id == temp2[0])) { var selected = ' selected = "selected" '; } else { var selected = ''; }
						toAppend +='<option '+ selected +' value="'+ADAG.trim(temp2[0])+'">'+temp2[1]+'</option>';
					}
					//alert(toAppend);
					ADAG('#advertiser_id').find('option').remove().end().append(toAppend);
					//alert(data); */
				}
			});
		}

	function getSelectedValue2( frmName, srcListName ) {
		try {
			var form = eval( 'document.' + frmName );
			var srcList = form[srcListName];
			//alert(srcList);

			i = srcList.selectedIndex;
			if (i != null && i > -1) {
				return srcList.options[i].value;
			} else {
				return null;
			}
		}
		catch(err){

		}
	}

		function Change() {
			document.adminForm.task.value = "addpopup";
			document.adminForm.submit();
			return true;
		}

		function checkZones(){
			var ok = true;
			ADAG('#campaign_table').find('tr:gt(0)').each(function(index){
    			if((ADAG(this).find('.check_camp input.formField').prop('checked') == true)&&(ADAG(this).find('.check_ad .w145').val() == 0)){
    			    ok = false;
    			}
			});
			return ok;
		}
		
		function timeToStamp(string_date){
			var form = document.adminForm;
			var time_format = form["time_format"].value;
			myDate = string_date.split(" ");
			myDate = myDate[0].split("-");
			
			if(myDate instanceof Array){
			}
			else{
				myDate = myDate[0].split("/");
			}
			var newDate = '';
			
			switch (time_format){
				case "0" :
					newDate = myDate[1]+"/"+myDate[2]+"/"+myDate[0];
					break;
				case "1" :
					newDate = myDate[0]+"/"+myDate[1]+"/"+myDate[2];
					break;
				case "2" :
					newDate = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
					break;
				case "3" :
					newDate = myDate[1]+"/"+myDate[2]+"/"+myDate[0];
					break;
				case "4" :
					newDate = myDate[0]+"/"+myDate[1]+"/"+myDate[2];
					break;
				case "5" :
					newDate = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
					break;
			}
			
			return newDate;
		}

        Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;
			if ((pressbutton=='save')||(pressbutton=='apply')) {
				<?php if ($_row->parameters['popup_type']=="webpage") { ?>
				if (form['title'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
					} else if (getSelectedValue2('adminForm','advertiser_id') < 1) {
						alert( "<?php echo JText::_("JS_SELECT_ADV");?>" );
					}/*else if (getSelectedValue2('adminForm','zone') < 1) {
						alert( "<?php echo JText::_("JS_SELECT_ZONE");?>" );
					}*/else if (form['parameters[page_url]'].value == "" || form['parameters[page_url]'].value == "http://") {
						alert( "<?php echo JText::_("JS_TARGETURL") ?>" );
					} else if (!(form['parameters[window_width]'].value>0)) {
						alert( "<?php echo JText::_("ADAG_WINDOW_WIDTH") ?>" );
					} else if (!(form['parameters[window_height]'].value>0)) {
						alert( "<?php echo JText::_("ADAG_WINDOW_HEIGHT"); ?>" );
					} else if(checkZones() == false){
						alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_FOR_CAMP"); ?>" );
					} else {
						if(form['ad_end_date'].value != "Never" && form['ad_end_date'].value != ""){
							start_date = form['ad_start_date'].value;
							end_date = form['ad_end_date'].value;
							
							start_date = new Date(timeToStamp(start_date)).getTime();
							end_date = new Date(timeToStamp(end_date)).getTime();
							
							if(Date.parse(start_date) > Date.parse(end_date)){
								alert("<?php echo JText::_("ADAG_FINISH_DATE_AND_START_DATE"); ?>");
								return false;
							}
						}
						
						<?php
							if(isset($_GET['cid'][0])&&(intval($_GET['cid'][0])>0)) {
						?>
							if((document.getElementById("approved").value != 'P')&&(document.getElementById("initvalcamp").value != document.getElementById("approved").value)) {
								if(document.getElementById("approved").value == 'Y') {
									var question = "<?php echo JText::_('ADAG_QUESTBANY');?>";
								} else if(document.getElementById("approved").value == 'N') {
									var question = "<?php echo JText::_('ADAG_QUESTBANN');?>";
								}

								var answer = confirm(question);
								if (answer) { } else { document.getElementById("sendmail").value = 0; }
							}
						<?php
							}
						?>
						<?php if(!isset($configs->geoparams['allowgeo'])&&!isset($configs->geoparams['allowgeoexisting'])) { ?>
						submitform( pressbutton );
						<?php } else {?>
						if((ADAG('#geo_type1').prop('checked') == true)&&(!checkChannel(true))) { return false;}
						sanitizeAndSubmit(pressbutton);
						<?php } ?>
					}
				<?php } else if	($_row->parameters['popup_type']=="image") { ?>
				if (form['title'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
					} else if (getSelectedValue2('adminForm','advertiser_id') < 1) {
						alert( "<?php echo JText::_("JS_SELECT_ADV");?>" );
					} else if (getSelectedValue2('adminForm','zone') < 1) {
						alert( "<?php echo JText::_("JS_SELECT_ZONE");?>" );
					} else if (form['target_url'].value == "" || form['target_url'].value == "http://") {
						alert( "<?php echo JText::_("JS_TARGETURL") ?>" );
					} else if (form['image_url'].value == "") {
						alert( "<?php echo JText::_("JS_SELECT_IMAGE");?>" );
					} else if (!(form['parameters[window_width]'].value>0)) {
						alert( "<?php echo JText::_("ADAG_WINDOW_WIDTH") ?>" );
					} else if (!(form['parameters[window_height]'].value>0)) {
						alert( "<?php echo JText::_("ADAG_WINDOW_HEIGHT"); ?>" );
					} else if(checkZones() == false){
						alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_FOR_CAMP"); ?>" );
					} else {
						if(form['ad_end_date'].value != "Never" && form['ad_end_date'].value != ""){
							start_date = form['ad_start_date'].value;
							end_date = form['ad_end_date'].value;
							
							start_date = new Date(timeToStamp(start_date)).getTime();
							end_date = new Date(timeToStamp(end_date)).getTime();
							
							if(Date.parse(start_date) > Date.parse(end_date)){
								alert("<?php echo JText::_("ADAG_FINISH_DATE_AND_START_DATE"); ?>");
								return false;
							}
						}
						
						<?php
							if(isset($_GET['cid'][0])&&(intval($_GET['cid'][0])>0)) {
						?>
							if((document.getElementById("approved").value != 'P')&&(document.getElementById("initvalcamp").value != document.getElementById("approved").value)) {
								if(document.getElementById("approved").value == 'Y') {
									var question = "<?php echo JText::_('ADAG_QUESTBANY');?>";
								} else if(document.getElementById("approved").value == 'N') {
									var question = "<?php echo JText::_('ADAG_QUESTBANN');?>";
								}

								var answer = confirm(question);
								if (answer) { } else { document.getElementById("sendmail").value = 0; }
							}
						<?php
							}
						?>
						<?php if(!isset($configs->geoparams['allowgeo'])&&!isset($configs->geoparams['allowgeoexisting'])) { ?>
						submitform( pressbutton );
						<?php } else {?>
						if((ADAG('#geo_type1').prop('checked') == true)&&(!checkChannel(true))) { return false;}
						sanitizeAndSubmit(pressbutton);
						<?php } ?>
					}
				<?php } else if	($_row->parameters['popup_type']=="html") { ?>
				if (form['title'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
					} else if (getSelectedValue2('adminForm','advertiser_id') < 1) {
						alert( "<?php echo JText::_("JS_SELECT_ADV");?>" );
					} else if (getSelectedValue2('adminForm','zone') < 1) {
						alert( "<?php echo JText::_("JS_SELECT_ZONE");?>" );
					} else if (!(form['parameters[window_width]'].value>0)) {
						alert( "<?php echo JText::_("ADAG_WINDOW_WIDTH") ?>" );
					} else if (!(form['parameters[window_height]'].value>0)) {
						alert( "<?php echo JText::_("ADAG_WINDOW_HEIGHT"); ?>" );
					} else if(checkZones() == false){
						alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_FOR_CAMP"); ?>" );
					} else {
						if(form['ad_end_date'].value != "Never" && form['ad_end_date'].value != ""){
							start_date = form['ad_start_date'].value;
							end_date = form['ad_end_date'].value;
							
							start_date = new Date(timeToStamp(start_date)).getTime();
							end_date = new Date(timeToStamp(end_date)).getTime();
							
							if(Date.parse(start_date) > Date.parse(end_date)){
								alert("<?php echo JText::_("ADAG_FINISH_DATE_AND_START_DATE"); ?>");
								return false;
							}
						}
					
						<?php echo $editor1->save( 'parameters[html]' );?>
						<?php
							if(isset($_GET['cid'][0])&&(intval($_GET['cid'][0])>0)) {
						?>
							if((document.getElementById("approved").value != 'P')&&(document.getElementById("initvalcamp").value != document.getElementById("approved").value)) {
								if(document.getElementById("approved").value == 'Y') {
									var question = "<?php echo JText::_('ADAG_QUESTBANY');?>";
								} else if(document.getElementById("approved").value == 'N') {
									var question = "<?php echo JText::_('ADAG_QUESTBANN');?>";
								}

								var answer = confirm(question);
								if (answer) { } else { document.getElementById("sendmail").value = 0; }
							}
						<?php
							}
						?>
						<?php if(!isset($configs->geoparams['allowgeo'])&&!isset($configs->geoparams['allowgeoexisting'])) { ?>
						submitform( pressbutton );
						<?php } else {?>
						if((ADAG('#geo_type1').prop('checked') == true)&&(!checkChannel(true))) { return false;}
						sanitizeAndSubmit(pressbutton);
						<?php } ?>
					}
				<?php } ?>
			}
			else
				submitform( pressbutton );

		}

		window.onload = function(){
			document.getElementById("initvalcamp").value = document.getElementById("approved").value;
		};
		</script>
