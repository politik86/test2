<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage 	DJ Classifieds Component
* @copyright 	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license 		http://www.gnu.org/licenses GNU/GPL
* @author 		url: http://design-joomla.eu
* @author 		email contact@design-joomla.eu
* @developer 	Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
*
*
* DJ Classifieds is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Classifieds is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Classifieds. If not, see <http://www.gnu.org/licenses/>.
*
*/
defined ('_JEXEC') or die('Restricted access');

?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" enctype='multipart/form-data'>
			<div class="row-fluid">
			<div class="span12 form-horizontal">
				<fieldset class="adminform">
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#details" data-toggle="tab"><?php echo empty($this->field->id) ? JText::_('COM_DJCLASSIFIEDS_NEW') : JText::_('COM_DJCLASSIFIEDS_EDIT'); ?></a>
						</li>
						<?php $cl_se_tab = ($this->field->source==2 ? ' style="display:none;" ' : '');?>
						<li id="search_tab_li" <?php echo $cl_se_tab ;?> >
							<a href="#search" data-toggle="tab"><?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH_OPTIONS');?></a>
						</li>
					</ul>												
					<div class="tab-content">
						<div class="tab-pane active" id="details">
							<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_LABEL');?></div>
								<div class="controls">
									<input class="text_area" onchange="createName();" type="text" name="label" id="label" size="50" maxlength="250" value="<?php echo $this->field->label; ?>" />
								</div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_NAME');?></div>
								<div class="controls">
									<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->field->name; ?>" />
								</div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_TOOLTIP_DESCRIPTION');?></div>
								<div class="controls">
									<textarea name="description" rows="4" cols="30"><?php echo $this->field->description; ?></textarea>
								</div>
							</div>
							<div class="control-group"> 
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_SOURCE_TYPE');?></div>
								<div class="controls">				                	
				                	<select name="source" id="source" onchange="sourceType(this.value)" <?php if($this->field->id){echo 'disabled="disabled"'; }?> >
				   						<option value="0" ><?php echo JText::_('COM_DJCLASSIFIEDS_CUSTOM_FIELD_IN_CATEGORY');?></option>
										<option value="1" <?php if($this->field->source=='1'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_CUSTOM_FIELD_IN_CONTACT'); ?></option>
										<option value="2" <?php if($this->field->source=='2'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_CUSTOM_FIELD_IN_PROFILE'); ?></option>									
									</select>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_TYPE');?></div>
								<div class="controls">
				                	<select name="type" id="type" onchange="fieldType(value);" >
				   						<option value="" ><?php echo JText::_('COM_DJCLASSIFIEDS_SELECT_TYPE');?></option>
										<option value="inputbox" <?php if($this->field->type=='inputbox'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_INPUTBOX'); ?></option>
										<option value="textarea" <?php if($this->field->type=='textarea'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_TEXTAREA');?></option>
										<option value="selectlist" <?php if($this->field->type=='selectlist'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_SELECTLIST');?></option>																													
										<option value="radio" <?php if($this->field->type=='radio'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_RADIO');?></option>
										<option value="checkbox" <?php if($this->field->type=='checkbox'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_CHECKBOX');?></option>
										<option value="date" <?php if($this->field->type=='date'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_DATE');?></option>
										<option value="link" <?php if($this->field->type=='link'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_LINK');?></option>
									</select>
								</div>
							</div>
							<div class="control-group" id="field_values_box" <?php if($this->field->type=='inputbox' || $this->field->type=='textarea' || $this->field->type=='date' || $this->field->type=='link'){ echo 'style="display:none;"';}?>>
								<div class="control-label">
									<?php echo JText::_('COM_DJCLASSIFIEDS_FIELD_VALUES');?>
		                    		<br /><span style="color:#666;width:160px;display:inline-block;"><?php echo JText::_('COM_DJCLASSIFIEDS_VALUES_SEPARATED_BY_SEMICOLON');?></span>
		                    	</div>
								<div class="controls">
									<textarea name="values" rows="4" cols="30"><?php echo $this->field->values; ?></textarea>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
				                    <?php echo JText::_('COM_DJCLASSIFIEDS_FIELD_DEFAULT_VALUE');?>
				                    <br /><span style="color:#666;width:160px;display:inline-block;"><?php echo JText::_('COM_DJCLASSIFIEDS_DEFAULT_CURRENT_DATE');?></span>								
								</div>
								<div class="controls">
		 		                	<input class="text_area" type="text" name="default_value" id="defaul_value" size="50" value="<?php echo $this->field->default_value; ?>" />									
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo JText::_('COM_DJCLASSIFIEDS_FIELD_PARAMS');?>
		                    		<br /><span style="color:#666;width:160px;display:inline-block;"><?php echo JText::_('COM_DJCLASSIFIEDS_PARAMS_EXAMPLE');?></span>
								</div>
								<div class="controls">
		                   			<input class="text_area" type="text" name="params" id="field_params" size="100" maxlength="250" value="<?php echo htmlentities($this->field->params); ?>" />									
								</div>
							</div>	
							<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_PUBLISHED');?></div>
								<div class="controls">
									<input autocomplete="off" type="radio" name="published" value="1" <?php  if($this->field->published==1 || $this->field->id==0){echo "checked";}?> /><span style="float:left; margin:2px 10px 0 5px;"><?php echo JText::_('COM_DJCLASSIFIEDS_YES'); ?></span>				
									<input autocomplete="off" type="radio" name="published" value="0" <?php  if($this->field->published==0 && $this->field->id>0){echo "checked";}?> /><span style="float:left; margin:2px 10px 0 5px;"><?php echo JText::_('COM_DJCLASSIFIEDS_NO'); ?></span>									
								</div>
							</div>	
							<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_REQUIRED');?></div>
								<div class="controls">
									<input type="radio" name="required" value="1" <?php  if($this->field->required==1){echo "checked";}?> /><span style="float:left;margin:2px 10px 0 5px;"><?php echo JText::_('COM_DJCLASSIFIEDS_YES'); ?></span>
									<input type="radio" name="required" value="0" <?php  if($this->field->required==0){echo "checked";}?> /><span style="float:left;margin:2px 10px 0 5px;"><?php echo JText::_('COM_DJCLASSIFIEDS_NO'); ?></span>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_VISIBLE_ONLY_FOR_ADMIN');?></div>
								<div class="controls">
									<input type="radio" name="access" value="1" <?php  if($this->field->access==1){echo "checked";}?> /><span style="float:left;margin:2px 10px 0 5px;"><?php echo JText::_('COM_DJCLASSIFIEDS_YES'); ?></span>
									<input type="radio" name="access" value="0" <?php  if($this->field->access==0 || $this->field->id==0){echo "checked";}?> /><span style="float:left;margin:2px 10px 0 5px;"><?php echo JText::_('COM_DJCLASSIFIEDS_NO'); ?></span>
								</div>
							</div>
							<?php $cl_in_table = ($this->field->source==2 ? ' style="display:none;" ' : '');?>	
							<div class="control-group" id="in_table_box" <?php echo $cl_in_table;?> >
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_SHOW_IN_TABLE_VIEW');?></div>
								<div class="controls">
									<select name="in_table" id="in_table" >
				   						<option value="0" ><?php echo JText::_('COM_DJCLASSIFIEDS_NO'); ?></option>
										<option value="1" <?php if($this->field->in_table=='1'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_YES_FIELD_AS_SEPARATED_COLUMN'); ?></option>
										<option value="2" <?php if($this->field->in_table=='2'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_YES_FIELD_IN_ADDITIONAL_INFO_COLUMN');?></option>
									</select>
								</div>
							</div>
							<?php $cl_in_blog = ($this->field->source==2 ? ' style="display:none;" ' : '');?>
							<div class="control-group" id="in_blog_box" <?php echo $cl_in_blog;?> >
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_SHOW_IN_BLOG_VIEW');?></div>
								<div class="controls">
									<select name="in_blog" id="in_blog" >
				   						<option value="0" ><?php echo JText::_('COM_DJCLASSIFIEDS_NO'); ?></option>
										<option value="1" <?php if($this->field->in_blog=='1'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_YES'); ?></option>
									</select>
								</div>
							</div>
							<?php $cl_in_item = ($this->field->source!=2 ? ' style="display:none;" ' : '');?>
							<div class="control-group" id="in_item_box" <?php echo $cl_in_item;?> >
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_SHOW_IN_AD_DETAILS_VIEW');?></div>
								<div class="controls">
									<select name="in_item" id="in_item" >
				   						<option value="0" ><?php echo JText::_('COM_DJCLASSIFIEDS_NO'); ?></option>
										<option value="1" <?php if($this->field->in_item=='1'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_YES'); ?></option>
									</select>
								</div>
							</div>																							
							<?php if($this->field->id>0 ){ 
								$cl_assigm_box = ($this->field->source!=0 ? ' style="display:none;" ' : '');?>	
								<div class="control-group" id="cat_assignment_box" <?php echo $cl_assigm_box;?>>
									<div class="control-label"><br /><?php echo JText::_('COM_DJCLASSIFIEDS_CATTEGORY_ASSIGNMENT');?></div>
									<div class="controls">
										<br /><br />
										<a class="button renew" href="javascript:void(0)" onclick="confirm_add('<?php echo $this->field->label."','".$this->field->id;?>')" ><?php echo JText::_('COM_DJCLASSIFIEDS_ADD_TO_ALL_CATEGORIES');?></a><br />
										<a class="button delete" href="javascript:void(0)" onclick="confirm_delete('<?php echo $this->field->label."','".$this->field->id;?>')" ><?php echo JText::_('COM_DJCLASSIFIEDS_DELETE_FROM_ALL_CATEGORIES');?></a>	
									</div>
								</div>									
							<?php } ?>																					
						</div>
						<div class="tab-pane" id="search">
							<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_IN_SEARCH');?></div>
								<div class="controls">
									<input type="radio" autocomplete="off" name="in_search" onclick="inSearch(1)" value="1" <?php  if($this->field->in_search==1){echo "checked";}?> /><span style="margin:2px 10px 0 5px;float:left;"><?php echo JText::_('COM_DJCLASSIFIEDS_YES'); ?></span>
									<input type="radio" autocomplete="off" name="in_search" onclick="inSearch(0)" value="0" <?php  if($this->field->in_search==0 || !$this->field->id){echo "checked";}?> /><span style="margin:2px 10px 0 5px;float:left;"><?php echo JText::_('COM_DJCLASSIFIEDS_NO'); ?></span>
								</div>
							</div>																
							<?php
							 if($this->field->in_search){
								$st= "";
							}else{ $st='display:none;'; } ?>
							<div class="control-group" id="search_type_box" style="<?php echo $st;?>" >
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH_TYPE');?></div>
								<div class="controls">
				                   <select autocomplete="off" name="search_type" id="search_type"  onchange="searchType(value);" >
				   						<option value="" ><?php echo JText::_('COM_DJCLASSIFIEDS_SELECT_TYPE'); ?></option>
										<option value="inputbox" <?php if($this->field->search_type=='inputbox'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_INPUTBOX'); ?></option>
										<option value="select" <?php if($this->field->search_type=='select'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_SELECTLIST'); ?></option>
										<option value="radio" <?php if($this->field->search_type=='radio'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_RADIOBUTTON'); ?></option>
										<option value="checkbox" <?php if($this->field->search_type=='checkbox'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_CHECKBOX'); ?></option>
										<option value="inputbox_min_max" <?php if($this->field->search_type=='inputbox_min_max'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_TWO_INPUTBOX_MIN_MAX');?></option>
										<option value="select_min_max" <?php if($this->field->search_type=='select_min_max'){ echo 'selected';}?> ><?php echo JText::_('COM_DJCLASSIFIEDS_TWO_SELECTLIST_MIN_MAX');?></option>																													
									</select>
								</div>
							</div>
							<?php
							 $search_type = $this->field->search_type;
							 if($search_type=='select_min_max' || $search_type=='select' || $search_type=='radio' || $search_type=='checkbox'){
								$st= "";
							}else{ $st='display:none;'; } ?>
							
							<div class="control-group" id="search_value1_box" style="<?php echo $st;?>" >
								<div class="control-label">
									<?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH_VALUES');?>1
									<br /><span style="color:#666;width:160px;display:inline-block;"><?php echo JText::_('COM_DJCLASSIFIEDS_VALUES_SEPARATED_BY_SEMICOLON');?></span>
								</div>
								<div class="controls">
									<input class="text_area" type="text" name="search_value1" id="search_value1" size="100"  value="<?php echo $this->field->search_value1; ?>" />
								</div>
							</div>	

							<?php if($search_type=='select_min_max'){
								$st= "";
							}else{ $st='display:none;'; } ?>
							
							<div class="control-group" id="search_value2_box" style="<?php echo $st;?>" >
								<div class="control-label">
									<?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH_VALUES');?>2
									<br /><span style="color:#666;width:160px;display:inline-block;"><?php echo JText::_('COM_DJCLASSIFIEDS_VALUES_SEPARATED_BY_SEMICOLON');?></span>
								</div>
								<div class="controls">
									<input class="text_area" type="text" name="search_value2" id="search_value2" size="100" value="<?php echo $this->field->search_value2; ?>" />										
								</div>
							</div>							
						</div>
					</div>
				</fieldset>
			</div>
			</div>
			<input type="hidden" name="id" value="<?php echo $this->field->id; ?>" />
			<input type="hidden" name="ordering" value="<?php echo $this->field->ordering; ?>" />
			<input type="hidden" name="option" value="com_djclassifieds" />
			<input type="hidden" name="task" value="field" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
<script type="text/javascript">
	function inSearch(v){
		if(v=='1'){
			$('search_type_box').setStyle("display","");
			var search_type = $('search_type').value;
			if(search_type=='inputbox_min_max_value'){
				$('search_value1_box').setStyle("display","");
				$('search_value2_box').setStyle("display","");
			}
		}else{
			$('search_type_box').setStyle("display","none");
			$('search_value1_box').setStyle("display","none");
			$('search_value2_box').setStyle("display","none");			
		}
	}
	
	function searchType(v){
		var search_type = $('search_type').value;
		if(v=='inputbox' || v=='textarea' || v=='inputbox_min_max' ){
			$('search_value1_box').setStyle("display","none");
			$('search_value2_box').setStyle("display","none");
		}else if(v=='select' || v=='radio' || v=='checkbox'){
			$('search_value1_box').setStyle("display","");
			$('search_value2_box').setStyle("display","none");
		}else if(v=='select_min_max'){
			$('search_value1_box').setStyle("display","");
			$('search_value2_box').setStyle("display","");
		}
	}
	
	function fieldType(v){
		if(v=='inputbox' || v=='textarea' || v=='date' || v=='link' ){
			$('field_values_box').setStyle("display","none");
		}else{
			$('field_values_box').setStyle("display","");
		}
		if(v=='link'){
			$('field_params').value='target="_blank" rel="nofollow" ';
		}
	}

	
	function sourceType(v){
		if(v==2){
			document.id('search_tab_li').setStyle("display","none");
			//document.id('cat_assignment_box').setStyle("display","none");
			document.id('in_table_box').setStyle("display","none");
			document.id('in_blog_box').setStyle("display","none");
			document.id('in_item_box').setStyle("display","");			
		}else{
			document.id('search_tab_li').setStyle("display","");
			//document.id('cat_assignment_box').setStyle("display","block");
			document.id('in_table_box').setStyle("display","");
			document.id('in_blog_box').setStyle("display","");
			document.id('in_item_box').setStyle("display","none");			
		}			
	}
	
	function createName(){
		var label = $('label').value;
		var name = label.toLowerCase();
		name = name.replace(/ /g,"_");
		name = name.replace(/[^a-zA-Z0-9_]/g,'');
		$('name').value = name;

	}

	function confirm_add(title,id){
	
		var answer = confirm ('<?php echo JText::_('COM_DJCLASSIFIEDS_CONFIRM_ADD_FIELDS_TO_CATEGORIES');?>'+' "'+title+'"');
		if (answer){
			 window.location="index.php?option=com_djclassifieds&task=field.addtocategories&id="+id+"";	
		}
	}
	
	function confirm_delete(title,id){
	
		var answer = confirm ('<?php echo JText::_('COM_DJCLASSIFIEDS_CONFIRM_DELETE_FIELDS_FROM_CATEGORIES');?>'+' "'+title+'"');
		if (answer){
			window.location="index.php?option=com_djclassifieds&task=field.deletefromcategories&id="+id+"";	
		}
	}

</script>			
<?php echo DJCFFOOTER; ?>