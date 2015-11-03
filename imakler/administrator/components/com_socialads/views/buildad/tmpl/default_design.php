<?php
	$campselect = array();
	if(isset($this->camp_dd))
	{
		foreach($this->camp_dd as $camp)
		{
			$campname = ucfirst(str_replace('plugpayment', '',$camp->campaign));
			$campselect[] = JHTML::_('select.option',$camp->campaign, $campname);
		}
	}
?>

<div class="row-fluid">
		<fieldset class="sa_fieldset">
			<legend class="hidden-desktop"><?php echo JText::_('DESIGN');?></legend>
			<div class="ad-details" id="ad-details-id">
				<div class="ad-dtl-space">

					<div class="span7 sa_border_right">
						<div class="row-fluid">

						<!-- ad-info start here -->
						<div class="ad-info  span11">

							<div id="default_zone" <?php echo $dis; ?> >


								<!--Added after desing -->

								<div class="control-group ">
									<label for="ad_creator" title="<?php echo JText::_('COM_SA_AD_CREATOR_TITLE');?>">
										<?php echo JText::_('COM_SA_AD_CREATOR_LB');?>
									</label>
									<div class="controls">
										<div class="input-append">
											<input type="text" id="ad_creator_name" name="ad_creator_name" class="input-medium required" disabled="disabled"
										placeholder="<?php echo JText::_('COM_SA_AD_CREATOR');?>" value="<?php echo  (isset( $this->addata_for_adsumary_edit->ad_creator)) ? JFactory::getUser($this->addata_for_adsumary_edit->ad_creator)->name : JFactory::getUser()->name; ?>">
											<a class="modal  button btn btn-info " rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=jform_created_by" title="	<?php echo JText::_('COM_SA_AD_SELECT_CREATOR_LB');?>" class="modal_jform_created_by">
												<i class="icon-user"></i>
											</a>
										</div>
										<input type="hidden" id="ad_creator_id" name="ad_creator_id" class="required" value="<?php echo (isset($this->addata_for_adsumary_edit->ad_creator)) ? $this->addata_for_adsumary_edit->ad_creator : JFactory::getUser()->id; ?>">
									</div>
								</div>

								<!--Added after desing -->



								<div class="control-group">
									<label for="adtype" title="<?php echo JText::_('ADTYPE');?>">
										<?php echo JText::_('ADTYPE');?>
									</label>
									<div class="controls">
										<?php
										$adtype_disabled='';
										if($this->managead_adid)
										{
											$adtype_disabled = 'disabled="disabled"';
										}

										echo JHTML::_('select.genericlist', $this->singleselect, "adtype", 'class="ad-type chzn-done" size="1" onchange="Adchange()"   '.$adtype_disabled.'', "value", "text", $ad_type); ?>
									</div>
								</div>
								<div class="control-group">
									<label for="adzone" title="<?php echo JText::_('DESC_ADZONE'); ?>" >
										<?php echo JText::_('ADZONE'); ?>
									</label>
									<div class="controls">
										<?php
										$adzone_disabled='';
										if($this->managead_adid)
										{
											$adzone_disabled = 'disabled="disabled"';
										}
										?>
										<select size="1" class="chzn-done ad-zone" id="adzone" name="adzone" onchange="getZonesdata( <?php $socialads_config['select_campaign']; ?>)"
												<?php echo $adzone_disabled; ?> >
												<?php
													if($this->edit_ad_adsumary)
													{ ?>

														<option selected="selected" value="<?php echo $this->zone->id; ?>">
															<?php echo $this->zone->zone_name ?>
														</option> <?php
													 }	?>
										</select>
										<input type ="hidden" name="ad_zone_id" id="ad_zone_id" value="<?php echo $this->zone->id;  ?>"/>
									</div>
								</div>
							</div><!--default_zone-->

							<div id="defaulturl">

							    <?php
								$buildadsession = JFactory::getSession();
								$pluginlist = $buildadsession->get('addatapluginlist');

								if($socialads_config['ad_site']==1)
								{
									$show_ad_site_only = 1;
								}
								else
								{
									$show_ad_site_only=0;
								}

								if($pluginlist != '' || $show_ad_site_only==1)
								{
									$display_dest='display:none;';
								}
								else
								{
									$display_dest='display:block;';
								}
								?>
								<div class="control-group" id="destination_url"  style="<?php echo $display_dest; ?>">


								<label for="defaulturl1" title="<?php echo JText::_('TOOLTIP_URL'); ?>">
									<?php echo JText::_('DEST_URL_LINE');?>
									<span class="help-inline"><?php echo JText::_('DEST_URL_OTHER');?></span>
								</label>

								<div id="defaulturl1">
									<div id="urlcontentlable">
										<div></div>
									</div><!--div#urlcontentlable-->
									<?php	// if edit ad from adsummary then use ads data to pre fill,

									if($this->edit_ad_adsumary)
									{
										$url1_edit=$this->addata_for_adsumary_edit->ad_url1;
										$url2_edit=$this->addata_for_adsumary_edit->ad_url2;
									}
									else
									{
										$url1_edit=$this->ad_data[0]['ad_url1'];
										$url2_edit=$this->ad_data[1]['ad_url2'];
									} ?>

									<!--enterlink-->
									<div class="" id="ad-form-spantxt">
										<div id="enterlink">
												<?php echo JHTML::_('select.genericlist',  $this->url1, 'addata[][ad_url1]', 'class="inputbox"', 'value', 'text',$url1_edit); ?>
												<input class="inputbox url" type="text" id="url2" name="addata[][ad_url2]" value="<?php echo $url2_edit;  ?>"  />
												<div class="clearfix"></div>
										</div>

									</div><!--div#ad-form-spantxt-->
									<!--enterlink-->
									<?php
									JPluginHelper::importPlugin( 'socialadspromote' );
									$dispatcher = JDispatcher::getInstance();
									$results 		= $dispatcher->trigger('onPromoteList');

									//added by aniket for config for promote plugin to see by defult.

									if(empty($results) )
									{ ?>
										<div id="selectlink" style="display:none">
										</div>
										<?php
									}
									else
									{
										// if edit ad from adsummary page dont show promote plugin link..
										if(!$this->edit_ad_adsumary)
										{ ?>

											<!--selectlink-->
											<div class="control-group" id="selectlink" style="display:block">
													<span id="ad-form-span">
														<a class="preview-title-lnk" href="javascript:selectapplist();">
														<?php echo JText::sprintf('SELECT_LINK', $sitename); ?>
														</a>
													</span>
											</div>
											<!--selectlink-->
											<?php
										}
									} ?>
								</div><!--div#defaulturl1-->
							</div><!--div.control-group#defaulturl-->

							<?php
								if($pluginlist != '' || $show_ad_site_only==1)
								{
									$display_td="display:block;";
								}
								else
								{
									$display_td="display:none;";
								}
							?>


								<!--promotplugin-->
								<div id="promotplugin" class="promotplugin control-group" style="<?php echo $display_td; ?>">
								<div id="contentlable">
									<label for="addatapluginlist" title="<?php echo JText::_('CONTENT');?>">
										<?php
										echo $sitename . ' ' . JText::_('CONTENT');
										?>
									</label>

									<div id="promote_plg_select" class="controls ">
										<?php
											JPluginHelper::importPlugin( 'socialadspromote' );
											$dispatcher = JDispatcher::getInstance();
											$results 		= $dispatcher->trigger('onPromoteList');

											foreach($results as $result)
											{
											if(!empty($result))
											{
												$plug_name = $result[0]->value;
												$plug_name = explode('|', $plug_name);

												$plugin = JPluginHelper::getPlugin( 'socialadspromote',$plug_name[0]);
												$pluginParams = json_decode( $plugin->params );
												$opt[] = JHTML::_('select.option','<OPTGROUP>', $pluginParams->plugin_name);

												foreach($result as $res)

												{

													$opt[] = JHTML::_('select.option', $res->value, $res->text);

												}

												$opt[] = JHTML::_('select.option','</OPTGROUP>');

											}

											}

											$sel[0]->value = '';
											$sel[0]->text = JText::_('SELECT_PLG');
											$opt = array_merge($sel, $opt);
											echo JHTML::_('select.genericlist',  $opt, 'addatapluginlist', 'class="promotplglist chzn-done" ', 'value', 'text', $pluginlist);
										?>
									</div><!--div.controls-->
								</div><!--div#contentlable-->
								<div id="webpagelink">
									<div>
										<span id="ad-form-span"><a  class="preview-title-lnk" href="javascript:inserturl();"><?php echo JText::_('WEBPAGE');?></a></span>
									</div>
								</div><!--div#webpagelink-->
							</div><!--div#promotplugin-->
						</div>
							<div class="control-group" id='ad_title_name'>
									<label for="ad_title_box" title="<?php echo JText::_('TOOLTIP_TITLE');?>">
										<?php echo JText::_('TITLE');?>
									</label>

									<?php
									// if edit ad from adsummary then prefill ad title from ads data
										if($this->edit_ad_adsumary)
										{
											$ad_title_edit=$this->addata_for_adsumary_edit->ad_title;
										}
										else
										{
											$ad_title_edit=$this->ad_data[2]['ad_title'];
										}
									?>

									<div class="controls" id ='ad_title_box'>
										<input class="inputbox" type="text" id="eBann" value="<?php echo $ad_title_edit; ?>" 	name="addata[][ad_title]" size="28" onKeyUp="toCount('eBann','max_tit1','{CHAR}','<?php echo JText::_('LEFT_CHAR');?>',max_tit.value, this.value,event);" >
										<div class="sa_charlimit help-inline">
											<span id ="max_tit1" > </span>
											<span id="sBann"><?php echo JText::_('LEFT_CHAR') ;?></span>
											<input type ="hidden" name="max_tit" class="max_tit" id="max_tit" value="<?php  ?>"/>
										</div><!--div.sa_charlimit-->
									</div><!--div.controls#ad_title_box-->
							</div><!--div.control-group#ad_title_name-->

							<div class="control-group" id='ad_body_name'>
								<label for="ad_body_box" title="<?php echo JText::_('TOOLTIP_BODY_TEXT');?>">
									<?php echo JText::_('BODY_TEXT');?>
								</label>
								 <!--Extra code for zone pricing -->
								 <input type ="hidden" name="max_body" class="max_body" id="max_body" value="<?php  ?>"/>
								 <input type ="hidden" name="pric_imp" id="pric_imp" value="<?php  ?>"/>
								 <input type ="hidden" name="pric_click" id="pric_click" value="<?php  ?>"/>
								 <input type ="hidden" name="pric_day" id="pric_day" value="<?php  ?>"/>
								 <!--Extra code for zone pricing -->

								<?php
									// if edit ad from adsummary then prefill ad body from ads data
									if($this->edit_ad_adsumary)
									{
										$ad_body_edit=$this->addata_for_adsumary_edit->ad_body;
									}
									else
									{
										$ad_body_edit=$this->ad_data[3]['ad_body'];
									}
								?>
								<div class="controls" id='ad_body_box'>
									<textarea id="eBann1" name="addata[][ad_body]" rows="3"  onKeyUp="toCount1('eBann1','max_body1','{CHAR}','<?php echo JText::_('LEFT_CHAR');?>',max_body.value, this.value,event);"><?php echo $ad_body_edit; ?></textarea>

									<div class="sa_charlimit help-inline">
										<span id ="max_body1" ></span>
										<span id="sBann1"><?php echo JText::_('LEFT_CHAR');?></span>
									</div><!--div.sa_charlimit-->
								</div><!--div.controls#ad_body_box-->
							</div><!--div.control-group#ad_body_name-->

							<!-- image upload-->
							<div class="control-group" id='ad_img_name'>
								<!-- 2.7.5 beta 1 manoj -->
								<label for="ad_image" title="<?php echo JText::_('TOOLTIP_IMAGE');?>">
									<?php echo JText::_('COM_SA_UPLOAD_MEDIA');?>
								 </label>


								<?php
									// if edit ad from adsummary then prefill ad image from ads data
									if($this->edit_ad_adsumary)
									{
										$ad_image=$this->addata_for_adsumary_edit->ad_image;
									}
								?>
								<!--ad_img_box-->
								<div class="controls" id='ad_img_box'>
									<!--ajax upload-->
									<!-- 2.7.5b1 changed start -->

										<span id="direct_upload">
											<div class="input-append">
												<div class="uneditable-input"> <i class="icon-file" style="display:none"></i> <span class="fileupload-preview"></span> </div>
												<span class="btn fileinput-button"> <span class="fileupload-new">Select file</span>
													<input type="file" name="ad_image" id="ad_image" value="<?php echo $ad_image; ?>" onchange="ajaxUpload(this.form,'&filename=ad_image','upload_area','<?php echo JText::_('IMG_UP');?><img src=\'<?php echo JURI::root();?>components/com_socialads/images/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' />','<img src=\'<?php echo JURI::root();?>components/com_socialads/images/error.gif\' width=\'16\' height=\'16\' border=\'0\' /> Error in Upload, check settings and path info in source code.'); return false;">
												</span>
												<div class="clearfix"></div>
											</div>
											<div class="alert alert-info msg_support_type alert-help-inline">
												<div class="sa_charlimit">
													<?php	echo JText::_('COM_SA_RECO_MEDIA_SIZE');?>
													<span id ='img_wid'> </span>px X
													<span id ='img_ht'> </span>px
												</div>
												<div>
												<?php
													echo JText::_('COM_SA_SUPPOERTED_FORMATS');
													if($socialads_config['allow_flash_ads']){
														echo ','.JText::_('COM_SA_SUPPOERTED_FORMATS_FLASH');
													}
													if($socialads_config['allow_vid_ads']){
														echo ','.JText::_('COM_SA_SUPPOERTED_FORMATS_VID');
													}
												?>
												</div>
											</div><!--div.msg_support_typed-->
											<div class="clearfix"></div>
										</span><!--span#direct_upload-->

										 <!--ajax upload-->
								</div><!--div.controls-->
							</div><!--div.control-group#ad_img_name-->
							<!-- image upload-->

					<!-- for alternative ad checkbox-->
					 <?php

						if(JVERSION >= '1.6.0')
						{
							if($this->special_access)
							{
								if($this->addata_for_adsumary_edit->ad_alternative)
								{
									$checked = 'checked="checked"';
								}
								else
								{
									$checked="";
								}
							?>

								<div class="control-group">
									<div class="controls altbutton">
										<input type="checkbox" name="altadbutton" id="altadbutton" onclick="switchCheckboxalt( this, guestbutton ,'<?php echo JText::_("COM_SOCIALADS_BTN_SAVEANDNEXT");?>','<?php echo JText::_("COM_SOCIALADS_BTN_SAVEANDEXIT");?>' )"  <?php echo $checked; ?> />
										<?php echo '<b>'.JText::_('ALT_AD').'</b> ';?>
										<div class="alert alert-info alert-help-inline">
											<?php echo JText::_('ALT_AD_DESCRIPTON');?>
										</div>
									 </div>
								</div>
								<div class="clearfix"></div>
							<?php
							}
						}
					?>
					<!-- for alternative ad checkbox-->

					 <!-- for guest ad checkbox-->
					<?php
						//	if ($socialads_config['integration']== 2)
						 $guest_dis = 'style="display:none;"';
					?>
							<div class="control-group" <?php echo $guest_dis; ?>>
								<?php

									$buildadsession = JFactory::getSession();
									$guest = $buildadsession->get('guestbutton');

									if(isset($guest) || $socialads_config['integration']== 2)
									{
										$checked="checked=checked";
									}
									else
									{
										$checked="";
									}
								?>

								<div class="altbutton controls">
									<input type="checkbox" name="guestbutton" id="guestbutton" onclick="switchCheckboxguest( this, 'altadbutton' );" <?php echo $checked;?> />

									<span class="sa_labels"><?php echo JText::_('GUEST_AD'); ?></span>
									<?php
									echo JText::sprintf('GUEST_AD_DESCRIPTON', $sitename);
									?>
								</div>
							</div>
						 <!-- for guest ad checkbox-->

							<div class="control-group">
								<div id="ad-form-button"></div>
							</div>
						</div><!--div.ad_info-->

						<input type="hidden" name="upimg" id="upimg" class = 'abc' value="<?php echo $ad_image; ?>" />
						<?php
							if (isset($ad_image))
							{ ?>
								<input type="hidden" name="upimgcopy" id="upimgcopy" value="<?php echo $ad_image; ?>" />
								<?php
							}
							else
							{ ?>
								<input type="hidden" name="upimgcopy" id="upimgcopy" value=" "/>
								<?php
							}
						?>
						</div>
					</div><!--span7-->
					<div class="span5">
						<!--adpreview-->
						<div class="adpreview" >
							<!--start for layouts-->

							<div id = "layout_div" class="control-group">
								<label for="layout1" title="<?php echo JText::_("TOOLTIP_LAYOUT");?>">
									<?php echo JText::_('LAYOUT');  ?>
								</label>
								<div class="controls">
									<span id = "layout1" class="row-fluid"></span>
									<input type ="hidden" name="ad_layout_nm" id="ad_layout_nm" value="<?php echo $layout;  ?>"/>
								</div>
							</div>
							<!--end for layouts-->

							<!--sa_preview-->
							<div id="sa_preview" style="margin:0px 0px 0px 5px;">
								<div><span class="sa_labels"><?php echo JText::_('BUILDAD_PREVIEW'); ?></span></div>
								<div class="ad-preview1" id="ad-preview"></div>
								<div style="clear:both;"></div>
							 </div>
							<!--sa_preview-->
						</div>
						<!--adpreview ends here-->


					</div><!--span5-->

					<div style="clear:both;"></div>

					<?php
						// if edit ad from adsummary then dont show continue button directly show loverdiv(targetting div)
						// edit ad
						 if($this->edit_ad_adsumary)
						 {
							if(($this->addata_for_adsumary_edit->ad_alternative == 0 && $this->addata_for_adsumary_edit->ad_affiliate == 0) && ($socialads_config['geo_target'] || ($socialads_config['integration'] != 2) ||$socialads_config['context_target']))
							{
								$loverdiv_style=' display:block ';
							}
							else
							{
								$loverdiv_style=' display:none ';
							}
						 }
					 ?>

				</div><!--ad-dtl-space-->
			<div><!--ad-details-->
		</fieldset><!--fieldset.sa_fieldset -->
</div><!--row-fluid-->



