
<!--for showing selection type of fields which is imported from jomsosial fields-->
<div id="lowerdiv">

	<fieldset class="sa_fieldset">
		<legend class="hidden-desktop"><?php echo JText::_('TARGETING'); ?></legend>
		<div class="alert"><span class="sa_labels1"><?php echo JText::_('TARGETING_MSG');?> </span>
		</div>
		<!--<div class="componentheading" id="componentheading_field_id"><?php echo JText::_('TARGETING');?></div>-->
		<!-- geo target start here -->
		<?php


		if( $this->socialads_config['geo_target'] )
		{

			if(!empty($this->geo_target))
			{
				$geo_dis = 'style="display:block;"';
			}
			else
			{
				$geo_dis = 'style="display:none;"';
			}

			// if edit ad from adsummary then prefill targeting for geo targeting...else placeholder
			$check_radio_region='';
			$check_radio_city='';
			$everywhere='';
			$country='';
			$region='';
			$city='';
			if($this->edit_ad_adsumary)
			{


				if(isset($this->geo_target['country']) )
				{
					$country=$this->geo_target['country'];
				}
				else
				{
					$country='';
				}

				if(!empty($this->geo_target['region'])) // for region field to prefilled...
				{
					$check_radio_region=1;
					$region=$this->geo_target['region'];
				}
				else
				{
					$region='' ;
				}
				if(!empty($this->geo_target['city'])) // for city field to prefilled...
				{
					$check_radio_city=1;
					if(isset($this->geo_target['city']))
					{
						$city=$this->geo_target['city'];
					}
				}
				else
				{
					$city='' ;
				}
				if(empty($this->geo_target['region']) && empty($this->geo_target['city']))
				{
					$everywhere=1;
				}
			}
			else
			{
				if(isset($this->geo_fields['country']) )
				{
					$country=$this->geo_fields['country'];
				}
				else
				{
					$country='';
				}
				if($this->geo_type == "byregion" ) // for region field to prefilled...
				{
					$check_radio_region=1;
					if(isset($this->geo_fields['region']))
					{
						$region=$this->geo_fields['region'];
					}
				}
				else
				{
					$region='' ;
				}
				if($this->geo_type == "bycity" ) // for city field to prefilled...
				{
					$check_radio_city=1;
					if(isset($this->geo_fields['city']))
					{
						$city=$this->geo_fields['city'];
					}
				}
				else
				{
					$city='' ;
				}

				if(($this->input->get('frm','','STRING')!='editad' || $this->geo_type == "everywhere" ))
				{
					$everywhere=1;
				}
			}  ?>


			<?php

			$publish1=$publish2=$publish1_label=$publish2_label='';
			if(isset($this->geo_target))
			{
				if($this->geo_target)
				{
					$publish1='checked="checked"';
					$publish1_label	=	'btn-success';
				}
				else
				{
					$publish2='checked="checked"';
					$publish2_label	=	'btn-danger';
				}
			}else
			{
				$publish2='checked="checked"';
				$publish2_label	=	'btn-danger';
			}
			?>
			<div id="geo_target_space" class="target_space well">

				<div class="control-group">
					<label class="control-label" title="<?php echo JText::_('GEO_TARGETING');?>">
						<?php echo JText::_('GEO_TARGETING');?>
					</label>
					<div class="controls input-append targetting_yes_no">
						<input type="radio" name="geo_targett" id="publish1" value="1" <?php echo $publish1;?> >
						<label class="first btn <?php echo $publish1_label;?>" type="button" for="publish1"><?php echo JText::_('SA_YES');?></label>
						<input type="radio" name="geo_targett" id="publish2" value="0" <?php echo $publish2;?> >
						<label class="last btn <?php echo $publish2_label;?>" type="button" for="publish2"><?php echo JText::_('SA_NO');?></label>

					</div>
				</div>

				<div id="geo_targett_div" <?php echo $geo_dis; ?> class="targetting">
					<div class="alert alert-info"> <span class="sa_labels1"><?php	 echo JText::_('GEO_TARGET_TIP'); ?> </span>
					</div>
				<?php

				if( JFile::exists($this->geodbfile) )
				{ ?>
					<div id="mapping-field-table">
						<div class="control-group">

							<label for="" title="<?php echo JText::_('GEO_COUNTRY');?>">
								<?php echo JText::_('GEO_COUNTRY');?>
							</label>

							<div class="controls">
								<ul class='selections' id='selections_country'>
									<input type="text" class="geo_fields ad-fields-inputbox"  id="country" value="<?php echo $country ; ?>" placeholder="Start typing country.."/>
									<input type="hidden" class="geo_fields_hidden" name="geo[country]" id="country_hidden" value="" />
								</ul>
							</div>
						</div>
						<div class="control-group">

							<div class="controls">

								<div id ="geo_others" style="display:none;">

									<label class="saradioLabel radio" for="everywhere"><input type="radio" <?php echo ($everywhere)?'checked="checked"' : ''; ?> value="everywhere" name="geo_type" id="everywhere" class="saradioLabel"><?php echo JText::_("SAGEOEVERY"); ?></label>

									<div <?php echo (in_array('byregion',$this->socialads_config['geo_opt']) )?'' :'style="display:none;"'; ?> >

										<label class="saradioLabel radio" for="byregion"><input type="radio" <?php echo ($check_radio_region)?'checked="checked"' : ''; ?> value="byregion" name="geo_type" id="byregion" class="saradioLabel"><?php echo JText::_("GEO_STATE"); ?></label>
										<ul style="display:none;" class="selections byregion_ul" id='selections_region' >
											<input type="text" class="geo_fields ad-fields-inputbox"  id="region" value="<?php echo $region ; ?>"  placeholder="Start typing region.."/>
											<input type="hidden" class="geo_fields_hidden" name="geo[region]" id="region_hidden" value="" />
										</ul>
									</div>
									<div <?php echo (in_array('bycity',$this->socialads_config['geo_opt']) )?'' :'style="display:none;"'; ?>>

										<label class="saradioLabel radio" for="bycity"><input type="radio" <?php echo ($check_city_region)?'checked="checked"' : ''; ?> value="bycity" name="geo_type" id="bycity" class="saradioLabel"><?php echo JText::_("GEO_CITY"); ?></label>
										<ul style="display:none;" class="selections bycity_ul"  id='selections_city' >
											<input type="text" class="geo_fields ad-fields-inputbox "  id="city" value="<?php echo $city ; ?>" placeholder="Start typing city.."/>
											<input type="hidden" class="geo_fields_hidden" name="geo[city]" id="city_hidden" value="" />
										</ul>
									</div>
								</div>
							</div>
						</div>

					</div>
			<?php
			} /*geo db file chk*/
			else
			{ ?>
				<div><span class="sa_labels"><?php	 echo JText::_('GEO_NO_DB_FILE'); ?> </span></div>
				<?php
			} ?>
				</div><!-- geo_target_div end here -->
				<div style="clear:both;"></div>
			</div>

			<?php
		} /*chk for geo target*/?>
	<!-- geo target end here -->

	<?php
	if($this->socialads_config['integration']!= 2)
	{

	?>
	<?php

	 if(!empty($this->social_target))
			$social_dis = 'style="display:block;"';
		else
			$social_dis = 'style="display:none;"';

			$publish1=$publish2=$publish1_label=$publish2_label='';
			if(!empty($this->social_target))
			{
				if($this->social_target)
				{
					$publish1='checked';
					$publish1_label	=	'btn-success';
				}
				else
				{
					$publish2='checked';
					$publish2_label	=	'btn-danger';
				}
			}
			else
			{
				$publish2='checked';
				$publish2_label	=	'btn-danger';
			}
		?>

		<div id="social_target_space" class="target_space well">
			<div class="control-group">
				<label class="control-label" title="<?php echo JText::_('SOCIAL_TARGETING');?>">
					<?php echo JText::_('SOCIAL_TARGETING');?>
				</label>
				<div class="controls input-append targetting_yes_no">
					<input type="radio" name="social_targett" id="social_target1" value="1" <?php echo $publish1;?> />
					<label class="first btn <?php echo $publish1_label;?>" type="button" for="social_target1"><?php echo JText::_('SA_YES');?></label>
					<input type="radio" name="social_targett" id="social_target2" value="0" <?php echo $publish2;?> />
					<label class="last btn <?php echo $publish2_label;?>" type="button" for="social_target2"><?php echo JText::_('SA_NO');?></label>
				</div>
			</div><!--sa_h3_chkbox-->
			<div id="social_targett_div" <?php echo $social_dis; ?> class="targetting">
					<div class="alert alert-info">
						<span id="ad-form-span" class="sa_labels1" >
							<?php if($this->fields ==null){
									echo JText::_('TARGET_NOT_SET'); ?>  <?php
							}else{
								echo JText::_('MESSAGE1'); ?> </span><br/><span class="sa_labels1" > <?php echo JText::_('MESSAGE2'); ?>
						<?php } ?>
						</span>
					</div>
				<?php

				if(!empty($this->fields)){ ?>
					<!-- field_target starts here -->
					<div id="field_target">
						<!-- floatmain starts here -->
						<div id="floatmain" >
							<div id="mapping-field-table">
						<!--for loop which shows JS fields with select types-->

							<?php
//load easy social language file
if($socialads_config['integration'] == 3){
$lang = JFactory::getLanguage();
$extension = 'com_easysocial';
$base_dir = JPATH_SITE;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

}

							$i=1;
							foreach($this->fields as $key=>$fields)
							{
								if($fields->mapping_fieldtype!='targeting_plugin'){

									if($i==1)
									{?>
										<div class="row-fluid">
							<?php
									}
							?>
								<div class="control-group span6">
									<label class="ad-fields-lable "><?php
										if($this->socialads_config['integration'] == 0){
										$fields->mapping_label = htmlspecialchars( getLangDefinition( $fields->mapping_label));
										}
										else{
											$fields->mapping_label = JText::_("$fields->mapping_label");
										}

										echo $fields->mapping_label;?>
									</label>
									<div class="controls">

									   <!--Numeric Range-->
										<?php
										//for easysocial fileds of those app are created..(gender,boolean and address)

										if($fields->mapping_fieldtype=="gender")
										{
											$gender[] = JHtml::_('select.option','', JText::_("SELECT"));
											$gender[] = JHtml::_('select.option','2', JText::_("FEMALE"));
											$gender[] = JHtml::_('select.option','1', JText::_("MALE"));
											echo JHtml::_('select.genericlist', $gender, 'mapdata[]['.$fields->mapping_fieldname.',select]', ' class="ad-fields-inputbox" id="mapdata[]['.$fields->mapping_fieldname.',select]" size="1"',   'value', 'text', $flds[$fields->mapping_fieldname.',select']);
										}
										if($fields->mapping_fieldtype=="boolean")
										{
											$boolean[] = JHtml::_('select.option','', JText::_("SELECT"));
											$boolean[] = JHtml::_('select.option','1', JText::_("YES"));
											$boolean[] = JHtml::_('select.option','0', JText::_("NO"));
											echo JHtml::_('select.genericlist', $boolean, 'mapdata[]['.$fields->mapping_fieldname.',select]', ' class="ad-fields-inputbox" id="mapdata[]['.$fields->mapping_fieldname.',select]" size="1"',   'value', 'text', $flds[$fields->mapping_fieldname.',select']);
										}
										/*
										if($fields->mapping_fieldtype=="address")
										{

										}
										*/
										if($fields->mapping_fieldtype=="numericrange")
										{
											$lowvar = $fields->mapping_fieldname.'_low';
											$highvar = $fields->mapping_fieldname.'_high';
											if(isset($flds[$fields->mapping_fieldname.'_low']) || isset($this->addata_for_adsumary_edit->$lowvar))
											{
												$grad_low=0;
												$grad_high=2030;
												if($this->edit_ad_adsumary)
												{
													$onkeyup=" ";
													if(strcmp($this->addata_for_adsumary_edit->$lowvar,$grad_low)==0)
															$this->addata_for_adsumary_edit->$lowvar = '';
													if( (strcmp($this->addata_for_adsumary_edit->$highvar,$grad_high)==0) || (strcmp($this->addata_for_adsumary_edit->$highvar,$grad_low)==0) )
															$this->addata_for_adsumary_edit->$highvar = '';
															?>
															<input type="textbox"  class="ad-fields-inputbox input-small" name="mapdata[][<?php echo $fields->mapping_fieldname.'_low|numericrange|0'; ?>]" value="<?php echo $this->addata_for_adsumary_edit->$lowvar; ?>" <?php echo $display_reach; ?> />
															<?php echo JText::_('SA_TO'); ?>
															<input type="textbox" class="ad-fields-inputbox input-small" name="mapdata[][<?php echo $fields->mapping_fieldname.'_high|numericrange|1'; ?>]" value="<?php echo $this->addata_for_adsumary_edit->$highvar?>" <?php echo $display_reach; ?> />

										<?php	}
												else
												{
													$onkeyup="  Onkeyup = checkforalpha(this) ";
													if(strcmp($flds[$fields->mapping_fieldname.'_low'],$grad_low)==0)
															$flds[$fields->mapping_fieldname.'_low'] = '';
													if( (strcmp($flds[$fields->mapping_fieldname.'_high'],$grad_high)==0)|| (strcmp($flds[$fields->mapping_fieldname.'_high'],$grad_high)==0) )
															$flds[$fields->mapping_fieldname.'_high'] = '';
													?>
														<input type="textbox"  class="ad-fields-inputbox input-small" name="mapdata[][<?php echo $fields->mapping_fieldname.'_low|numericrange|0'; ?>]" value="<?php echo $flds[$fields->mapping_fieldname.'_low']?>" Onkeyup = checkforalpha(this); <?php echo $display_reach; ?> />
														<?php echo JText::_('SA_TO'); ?>
														<input type="textbox" class="ad-fields-inputbox input-small" name="mapdata[][<?php echo $fields->mapping_fieldname.'_high|numericrange|1'; ?>]" value="<?php echo $flds[$fields->mapping_fieldname.'_high']?>" <?php echo $display_reach; ?> Onkeyup = checkforalpha(this); />
										<?php	}

											?>

										<?php }
											else
											{ ?>
											<input type="textbox"  class="ad-fields-inputbox input-small" name="mapdata[][<?php echo $fields->mapping_fieldname.'_low|numericrange|0'; ?>]" value="" <?php echo $display_reach; ?> <?php echo $onkeyup; ?> />
											<?php echo JText::_('SA_TO'); ?>
											<input type="textbox" class="ad-fields-inputbox input-small" name="mapdata[][<?php echo $fields->mapping_fieldname.'_high|numericrange|1'; ?>]" value="" <?php echo $display_reach; ?> <?php echo $onkeyup; ?> />
										<?php }
										} ?>



										<!--Freetext-->
										<?php if($fields->mapping_fieldtype=="textbox")
										{
											$textvar = $fields->mapping_fieldname;
											if(isset($flds[$fields->mapping_fieldname]) || isset($this->addata_for_adsumary_edit->$textvar))
											{
												if($this->edit_ad_adsumary)
												{
												?>
												<input type="textbox" class="ad-fields-inputbox input-medium" name="mapdata[][<?php  echo $fields->mapping_fieldname; ?>]" value="<?php echo $this->addata_for_adsumary_edit->$textvar; ?>" <?php echo $display_reach; ?> />
												<?php }
												else
												{
													?>
											<input type="textbox" class="ad-fields-inputbox input-medium" name="mapdata[][<?php echo $fields->mapping_fieldname; ?>]" value="<?php echo $flds[$fields->mapping_fieldname]; ?>" <?php echo $display_reach; ?>/>
											<?php	}
											}
											else
												{?>
											<input type="textbox" class="ad-fields-inputbox input-medium" name="mapdata[][<?php echo $fields->mapping_fieldname; ?>]" value=""
												<?php echo $display_reach; ?> />
											<?php }
										}?>


										<!--Single Select-->
										<?php
											if($fields->mapping_fieldtype=="singleselect")
											{
												$singlevar = $fields->mapping_fieldname;
												if(isset($flds[$fields->mapping_fieldname.',select']) || isset($this->addata_for_adsumary_edit->$singlevar))
												{
													$singleselect = $fields->mapping_options;
													$singleselect = explode("\n",$singleselect);
													for($count=0;$count<count($singleselect); $count++){


																$options[] = JHtml::_('select.option',$singleselect[$count],JText::_($singleselect[$count]),'value','text');
														}

													$s= array();
													$s[0]->value = '';
													$s[0]->text = JText::_('SINGSELECT');
													$options = array_merge($s, $options);
													if($this->edit_ad_adsumary)
													{
														$mdata = str_replace('||',',',$this->addata_for_adsumary_edit->$singlevar);
														$mdata = str_replace('|','',$mdata);
														echo JHtml::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox input-medium" size="1" '.$display_reach,   'value', 'text', $mdata);
													}
													else
													echo JHtml::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', ' class="ad-fields-inputbox input-medium"'.$display_reach.' id="mapdata[]['.$fields->mapping_fieldname.',select]" size="1"',   'value', 'text', $flds[$fields->mapping_fieldname.',select']);
													$options= array();
												}
												else
												{
													$singleselect = $fields->mapping_options;
													$singleselect = explode("\n",$singleselect);
													for($count=0;$count<count($singleselect); $count++)
														{

																$options[] = JHtml::_('select.option',$singleselect[$count], JText::_($singleselect[$count]),'value','text');
														}

													$s= array();
													$s[0]->value = '';
													$s[0]->text = JText::_('SINGSELECT');
													$options = array_merge($s, $options);

													echo JHtml::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox input-medium"  id="mapdata[]['.$fields->mapping_fieldname.',select]"'.$display_reach.' size="1"',   'value', 'text', '');
													$options= array();
												}
											}

									//Multiselect
											if($fields->mapping_fieldtype=="multiselect" )
											{
												$multivar = $fields->mapping_fieldname;
												if(isset($flds[$fields->mapping_fieldname.',select']) || isset($this->addata_for_adsumary_edit->$multivar))
													{
														$multiselect = $fields->mapping_options;
														$multiselect = explode("\n",$multiselect);
														if($this->edit_ad_adsumary)
														{
															$mdata = str_replace('||',',',$this->addata_for_adsumary_edit->$multivar);
															$mdata = str_replace('|','',$mdata);
															$multidata = explode(",",$mdata);
															//print_r($multidata);
														}
															for($cnt=0;$cnt<count($multiselect); $cnt++)
															{

																$options[] = JHtml::_('select.option',$multiselect[$cnt], JText::_($multiselect[$cnt]),'value','text');
															}

															if($cnt > 20)
															{
																$size = '6';
															}
															else
															{
																$size = '3';
															}

															echo JHtml::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox input-medium" id="mapdata[]['.$fields->mapping_fieldname.',select]" size="'.$size.'" multiple="true"'.$display_reach,   'value', 'text', $multidata);
															$options= array();
													}
													else
													{

														$multiselect = $fields->mapping_options;
														$multiselect = explode("\n",$multiselect);
														for($cnt=0;$cnt<count($multiselect); $cnt++)
														{

																$options[] = JHtml::_('select.option',$multiselect[$cnt], JText::_($multiselect[$cnt]),'value','text');
														}

														if($cnt > 20)
														{	$size = '6';}
														else
															$size = '3';
														echo JHtml::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox input-medium"  size="'.$size.'" id="mapdata[]['.$fields->mapping_fieldname.',select]" multiple="true"'.$display_reach,   'value', 'text', '');

														$options= array();
													}
										  }

											 //daterange
											if($fields->mapping_fieldtype=="daterange")
											{
												$this->datelowvar = $fields->mapping_fieldname.'_low';
												$this->datehighvar = $fields->mapping_fieldname.'_high';
												if(isset($flds[$fields->mapping_fieldname.'_low']) || isset($this->addata_for_adsumary_edit->$this->datelowvar))
												{
															$date_low = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,1910));
															$date_high = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,2030));
														if($this->edit_ad_adsumary){
															if(strcmp($this->addata_for_adsumary_edit->$this->datelowvar,$date_low)==0)
															$this->addata_for_adsumary_edit->$this->datelowvar = '';

															if(strcmp($this->addata_for_adsumary_edit->$this->datehighvar,$date_high)==0)
															$this->addata_for_adsumary_edit->$this->datehighvar = '';
															echo JHtml::_('calendar', $this->addata_for_adsumary_edit->$this->datelowvar, 'mapdata[]['.$fields->mapping_fieldname.'_low|daterange|0]', 'mapdata['.$key.']['.$fields->mapping_fieldname.'_low]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small',$display_reach_fun));
															echo JText::_('SA_TO');
															echo JHtml::_('calendar', $this->addata_for_adsumary_edit->$this->datehighvar, 'mapdata[]['.$fields->mapping_fieldname.'_high|daterange|1]', 'mapdata['.$key.']['.$fields->mapping_fieldname.'_high]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small',$display_reach_fun));
														}
														else
														{
															if(strcmp($flds[$fields->mapping_fieldname.'_low'],$date_low)==0)
																$flds[$fields->mapping_fieldname.'_low'] = '';
															if(strcmp($flds[$fields->mapping_fieldname.'_high'],$date_high)==0)
																$flds[$fields->mapping_fieldname.'_high'] = '';

															echo JHtml::_('calendar', $flds[$fields->mapping_fieldname.'_low'], 'mapdata[]['.$fields->mapping_fieldname.'_low]', 'mapdata['.$key.']['.$fields->mapping_fieldname.'_low]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small',$display_reach_fun));
															echo JText::_('SA_TO');
															echo JHtml::_('calendar', $flds[$fields->mapping_fieldname.'_high'], 'mapdata[]['.$fields->mapping_fieldname.'_high]', 'mapdata['.$key.']['.$fields->mapping_fieldname.'_high]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small',$display_reach_fun));
														}
												}
												else
												{
													if($this->edit_ad_adsumary)
													{
															echo JHtml::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.'_low|daterange|0]', 'mapdata['.$key.']['.$fields->mapping_fieldname.'_low]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small','onchange'=>'calculatereach()'));
															echo JText::_('SA_TO');
															echo JHtml::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.'_high|daterange|1]', 'mapdata['.$key.']['.$fields->mapping_fieldname.'_high]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small',$display_reach_fun));
													}
													else
													{
															echo JHtml::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.'_low|daterange|0]', 'mapdata['.$key.']['.$fields->mapping_fieldname.'_low]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small',$display_reach_fun));
															echo JText::_('SA_TO');
															echo JHtml::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.'_high|daterange|1]', 'mapdata['.$key.']['.$fields->mapping_fieldname.'_high]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small',$display_reach_fun));
													}
												}
												if($this->datelow==null) { $this->datelow = $fields->mapping_fieldname; } else {  $this->datelow .= ','.$fields->mapping_fieldname; }

											}


										 //date
												if($fields->mapping_fieldtype=="date")
												{
													$datevar = $fields->mapping_fieldname;
													if(isset($flds[$fields->mapping_fieldname]) || isset($this->addata_for_adsumary_edit->$datevar))
													{
														if($this->edit_ad_adsumary)
														{
															echo JHtml::_('calendar', $this->addata_for_adsumary_edit->$datevar , 'mapdata[]['.$fields->mapping_fieldname.']', 'mapdata['.$key.']['.$fields->mapping_fieldname.']','%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small','onchange'=>$display_reach_fun_for_calendar));
														}
														else
														{
															echo JHtml::_('calendar', $flds[$fields->mapping_fieldname] , 'mapdata[]['.$fields->mapping_fieldname.']', 'mapdata['.$key.']['.$fields->mapping_fieldname.']','%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small','onchange'=>$display_reach_fun_for_calendar));
														}
													}
													else
													{
														echo JHtml::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.']', 'mapdata['.$key.']['.$fields->mapping_fieldname.']','%Y-%m-%d', array('class'=>'ad-fields-inputbox input-small','onchange'=>$display_reach_fun_for_calendar));
													} ?>
										<?php 	}?>
									</div>
								</div>
						 <?php
									if($i == 2)
									{?>
										</div>
								<?php
										$i=0;
									}
									$i++;
								}

							}
							if($i==2)
							echo "</div>";
							 ?>

							<?php

									$adid[0]= $this->edit_ad_adsumary;
									JPluginHelper::importPlugin('socialadstargeting');
									$dispatcher = JDispatcher::getInstance();
									$results = $dispatcher->trigger('onFrontendTargetingDisplay', array($this->addata_for_adsumary_edit,$this->adfieldsTableColumn));

									$j=1;
									foreach($results as $value)
									{
										if(!empty($value))
										{
											foreach($value as $val)
											{
												if($val){
													if($j == 1)
													{
														echo "<div class='row-fluid'>";
													}?>
													<?php  echo $val;?>

												<?php
													if($j == 2){
														echo "</div>";
														$j	=	0;
													}
													$j++;

												}
											}

										}
									}
									if($j==2)
										echo "</div>";
									?>

								<div style="clear:both"></div>
							</div>
						</div><!-- End fo floatmain div -->

						<?php if($this->socialads_config['display_reach']){ ?>
						<div id="fixedElement" >
							<div id="estimated_reach">
								<div class="estimated_reach_head"><?php echo $this->EST_HEAD;?></div>
								<div class="estimated_reach_end"><span class="estimated_reach_value"><?php echo $socialads_config['estimated_reach'] ?></span><?php echo $this->EST_END;?></div>
							</div>
						</div>
						<?php } ?>
					</div><!-- End fo field_target div -->
					<?php }//end for fields not empty condition

					?>
			</div> <!--end of social_target div -->
				<div style="clear:both;"></div>
		</div>
	<?php }//end of social integration chk ?>



	<!-- context target start here -->
	<?php
	if( $this->socialads_config['context_target'] )
	{

		if(isset($this->context_target))
		{
			$context_dis = 'style="display:block;"';
		}
		else
		{
			$context_dis = 'style="display:none;"';
		}

		$context_target1=$context_target2=$context_target1_label=$context_target2_label='';
		if(isset($this->context_target))
		{
			if($this->context_target)
			{
				$context_target1='checked="checked"';
				$context_target1_label	='btn-success';
			}
			else
			{
				$context_target2='checked="checked"';
				$context_target2_label	='btn-danger';
			}
		}
		else
		{
			$context_target2='checked="checked"';
			$context_target2_label	='btn-danger';
		}

	?>
		<div id="context_target_space" class="target_space well">

			<div class="control-group">
				<label class="control-label" title="<?php echo JText::_('CONTEXT_TARGET');?>">
					<?php echo JText::_('CONTEXT_TARGET');?>
				</label>
				<div class="controls input-append targetting_yes_no">
					<input type="radio" name="context_targett" id="context_target1" value="1" class="target" <?php echo $context_target1; ?> >
					<label  class="first btn <?php echo $context_target1_label;?>" type="button" for="context_target1"><?php echo JText::_('SA_YES');?></label>
					<input type="radio" name="context_targett" id="context_target2" value="0" class="target" <?php echo $context_target2; ?> >
					<label class="last btn <?php echo $context_target2_label;?>" type="button" for="context_target2"><?php echo JText::_('SA_NO');?></label>
				</div>
			</div>

			<div id="context_targett_div" <?php echo $context_dis; ?> class="targetting">
				<div class="alert alert-info"><span class="sa_labels1"><?php	 echo JText::_('CONTEXT_TARGET_TIP'); ?> </span>
				</div>

				<div id="mapping-field-table">
					<div class="control-group">
						<label for="context_target_data" title="<?php echo JText::_('CONTEXT_TARGET_INPUTBOX');?>">
							<?php echo JText::_('CONTEXT_TARGET_INPUTBOX');?>
						</label>
						<div class="controls">
							<input type="text" name="context_target_data[keywordtargeting]" class="inputbox input-xlarge" id="context_target_data" value="<?php echo $this->context_target_data_keywordtargeting;?>" onchange="" />
						</div>
					</div>
				</div>


			</div><!-- context_target_div end here -->
			<div style="clear:both;"></div>
		</div>

	<?php
	} /*chk for context target*/
	?>
	<!-- context target end here -->
		<div id="ad-targeting-error" class="alert alert-error " style="display:none;">
			<?php echo JText::_('COM_SA_SOMETHING_WENT_WRONG_AD_TARGETING'); ?>
		</div>
	</fieldset>
	<!--fieldset-->

	<?php
	// // if edit ad from adsummary then dont show continue and back button ...show update button directly..
	if($this->edit_ad_adsumary)
	{

		if(($this->addata_for_adsumary_edit->ad_alternative == 0 && $this->addata_for_adsumary_edit->ad_affiliate == 0))
		{ ?>
			<?php
		}
	}
	else
	{ ?>
	<?php
	} ?>

</div><!--lowerdiv ends here-->
<!--added by ANIKET -->



