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

JHtml::_('behavior.tooltip');

$langfe = $this->langfe;
$langbe = $this->langbe;
$agmailfrom =  $this->agmailfrom;
$agfromname =  $this->agfromname;
$agfromemail = $this->agfromemail;
$plugs = $this->plugs;
$default_currency = $this->default_currency;
$normal_plugs = $this->normal_plugs;
$currency_list = $this->currency_list;
$test = $this->plugin_data;
$row = $this->configs;

$defaultplug = $this->defaultplug;
$editor  = JFactory::getEditor();
$startOffset=$this->startOffset;
$approvals = $this->approvals;
$task2 = JRequest::getVar("task2", "general");

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
if ($row->jomfields) { $row->jomfields = json_decode($row->jomfields); }
if (!is_array($row->jomfields)) { $row->jomfields = array(); }
?>

<script type="application/javascript" language="javascript">
	
	window.onload = setDefaultTab;	
	
	function setDefaultTab(){
		var tab = '<?php echo $task2; ?>';
		var children = document.getElementById('my_tabs').getElementsByTagName('dt');
		var pos = 0;
		for(i=0; i<children.length; i++){
			var class_name = String(children[i].className);
			var new_class = class_name.split(' ');
			
			if(!class_name.indexOf(tab)){
				pos = i;
				children[i].setAttribute("class", new_class["0"]+" open");
			}
			else{
				children[i].setAttribute("class", new_class["0"]+" closed");
			}
		}
		
		var children = document.getElementsByTagName('dd');
		for(i=0; i<children.length; i++){
			if(i == pos){
				children[i].style.display = "block";
			}
			else{
				children[i].style.display = "none";
			}
		}
	}
</script>

<?php
	include(JPATH_BASE."/components/com_adagency/includes/js/configs.php");
?>

<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	
	<?php

		$general_active = 'none';
		$payments_active = 'none';
		$email_active = 'none';
		$content_active = 'none';
		$overview_active = 'none';
		$registration_active = 'none';
		$approvals_active = 'none';
		$jomsocial_active = 'none';
		
		if($task2 == 'general'){
			$general_active = 'block';				
		}
		elseif($task2 == 'payments'){
			$payments_active = 'block';
		}
		elseif($task2 == 'email'){
			$email_active = 'block';
		}
		elseif($task2 == 'content'){
			$content_active = 'block';
		}
		elseif($task2 == 'overview'){
			$overview_active = 'block';
		}
		elseif($task2 == 'registration'){
			$registration_active = 'block';
		}
		elseif($task2 == 'approvals'){
			$approvals_active = 'block';
		}
		elseif($task2 == 'jomsocial'){
			$jomsocial_active = 'block';
		}
	?>
	
	<div style="display:<?php echo $general_active; ?>;" id="general">
		<div class="well"><?php echo JText::_('VIEWCONFIGCATGENERALSETT');?></div>
		<p class="text-error"><?php echo JText::_('AD_CONFIG_MAIL_WARN'); ?></p>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('CONFIGADMINEMAIL'); ?> </label>
			<div class="controls">
				<input class="inputbox" type="text" name="adminemail" size="40" value="<?php if ($row->adminemail) echo $row->adminemail; else echo $agmailfrom; //?>" />
				&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('CONFIGADMINEMAIL_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('CONFIGFROMEMAIL'); ?> </label>
			<div class="controls">
				<input class="inputbox" type="text" name="fromemail" size="40" value="<?php if ($row->fromemail) echo $row->fromemail; else echo $agfromemail;//?>" />
				&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('CONFIGFROMEMAIL_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('CONFIGFROMNAME'); ?> </label>
			<div class="controls">
				<input class="inputbox" type="text" name="fromname" size="40" value="<?php if ($row->fromname) echo $row->fromname; else echo $agfromname;//?>" />
				&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('CONFIGFROMNAME_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="well"><?php echo JText::_('CONFIGBSET');?></div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('CONFIGIMGFOLDER'); ?> </label>
			<div class="controls">
				<input class="inputbox" type="text" name="imgfolder" size="60" value="<?php echo $row->imgfolder;?>">
				&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('CONFIGIMGFOLDER_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('CONFIGMAXCHARS'); ?> </label>
			<div class="controls">
				<input class="inputbox" type="text" name="maxchars" size="6" value="<?php echo $row->maxchars;?>">
				&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('CONFIGMAXCHARS_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_ALLOW_IM_TEXT');?>
			</label>
			<div class="controls">
			   <fieldset class="radio btn-group" id="showtxtimg_yes">
					<?php
						$no_checked = "";
						$yes_cheched = "";
						
						if(!isset($row->params['showtxtimg'])||($row->params['showtxtimg']==1)) {
							$yes_cheched = 'checked="checked"';
						}
						else{
							$no_checked = 'checked="checked"';
						}
					?>
					<input type="hidden" name="params[showtxtimg]" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[showtxtimg]">
					<span class="lbl"></span>
				</fieldset>
			</div>
		</div>
		
		<div class="well"><?php echo JText::_('ADS_LIM');?></div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADS_LIM_P_CAMP'); ?> </label>
			<div class="controls">
				<?php echo $this->adslim; ?>
				&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADS_LIM_P_CAMP_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="well"><?php echo JText::_('ADAG_LIM_IMP_CLICK'); ?></div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_IPLIM1'); ?> </label>
			<div class="controls">
				<input class="inputbox" type="text" value="<?php echo $row->limit_ip; ?>" size="6" name="limit_ip"/>&nbsp;<?php echo JText::_('ADAG_IPLIM2');?>
				&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_IPLIM1_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_CLICKLIM1'); ?> </label>
			<div class="controls">
				<input class="inputbox" type="text" value="<?php echo $row->params['click_limit']; ?>" size="6" name="params[click_limit]"/>&nbsp;<?php echo JText::_('ADAG_CLICKLIM2');?>
				&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_CLICKLIM1_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="well"><?php echo JText::_('ADG_JQUERY'); ?></div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADG_JQUERY_FRONT'); ?> </label>
			<div class="controls">
				 <fieldset class="radio btn-group" id="zinfo">
				<?php
						$yes_cheched = "";
						

						if($row->params['jquery_front'] == "1") {
							$yes_cheched = 'checked="checked"';
						}
					?>
					<input type="hidden" name="params[jquery_front]" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[jquery_front]">
					<span class="lbl"></span>
					</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADG_JQUERY_END_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADG_JQUERY_END'); ?> </label>
			<div class="controls">
				 <fieldset class="radio btn-group" id="zinfo">
					<?php
						$yes_cheched = "";
						
						if($row->params['jquery_back'] == "1") {
							$yes_cheched = 'checked="checked"';
						}
					?>
					<input type="hidden" name="params[jquery_back]" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[jquery_back]">
					<span class="lbl"></span>
					</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADG_JQUERY_END_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>

		<div class="well"><?php echo JText::_('ADAG_TIMESETS'); ?></div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_FORMTIM'); ?> </label>
			<div class="controls">
				<select name="params[timeformat]">
					<option value="0" <?php if(isset($row->params['timeformat']) && ($row->params['timeformat'] == "0")){ echo 'selected="selected"'; } ?> > Y-m-d H:M:S </option>
                    <option value="1" <?php if(isset($row->params['timeformat']) && ($row->params['timeformat'] == "1")){ echo 'selected="selected"'; } ?> > m/d/Y H:M:S </option>
                    <option value="2" <?php if(isset($row->params['timeformat']) && ($row->params['timeformat'] == "2")){ echo 'selected="selected"'; } ?> > d-m-Y H:M:S </option>
                    
                    <option value="3" <?php if(isset($row->params['timeformat']) && ($row->params['timeformat'] == "3")){ echo 'selected="selected"'; } ?> > Y-m-d </option>
                    <option value="4" <?php if(isset($row->params['timeformat']) && ($row->params['timeformat'] == "4")){ echo 'selected="selected"'; } ?> > m/d/Y </option>
                    <option value="5" <?php if(isset($row->params['timeformat']) && ($row->params['timeformat'] == "5")){ echo 'selected="selected"'; } ?> > d-m-Y </option>
                </select>
				&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_FORMTIM_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="well"><?php echo JText::_('ADAG_SHOW_ZONE_INFO'); ?></div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_SHOW_ZONE_INFO'); ?>
			</label>
			<div class="controls">
			   <fieldset class="radio btn-group" id="zinfo">
					<?php
						$no_checked = "";
						$yes_cheched = "";
						
						if(isset($row->show)&&(in_array('zinfo',$row->show))) {
							$yes_cheched = 'checked="checked"';
						}
						else{
							$no_checked = 'checked="checked"';
						}
					?>
					<input type="hidden" name="show[zinfo]" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="show[zinfo]">
					<span class="lbl"></span>
				</fieldset>
			</div>
		</div>
		
		<div class="well"><?php echo JText::_('ADAG_PREV_ZONS'); ?></div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_PREV_ZONS'); ?>
			</label>
			<div class="controls">
			   <fieldset class="radio btn-group" id="showpreview">
					<?php
						$no_checked = "";
						$yes_cheched = "";
						
						if (isset($row->showpreview) && ($row->showpreview > 0)) {
							$yes_cheched = 'checked="checked"';
						}
						else{
							$no_checked = 'checked="checked"';
						}
					?>
					<input type="hidden" name="showpreview" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="showpreview">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_PREV_ZONS_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="well">
			<?php echo JText::_('ADAG_AFTERCAMP'); ?>
			<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_AFTERCAMP_TIP'); ?>" >
			<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
		</div>
		
		<div class="control-group">
			<label class="control-label">
				<?php
					if(isset($row->show)&&(in_array('aftercamp1',$row->show))) {
						$aftercamp1 = true;$aftercamp2 = false;$aftercamp3 = false;
					} elseif(isset($row->show)&&(in_array('aftercamp2',$row->show))) {
						$aftercamp1 = false;$aftercamp2 = true;$aftercamp3 = false;
					} elseif(isset($row->show)&&(in_array('aftercamp3',$row->show))) {
						$aftercamp1 = false;$aftercamp2 = false;$aftercamp3 = true;
					} else {
						$aftercamp1 = true;$aftercamp2 = false;$aftercamp3 = false;
					}
				?>
				
				<input type="radio" value="1" id="aftercamp1" name="show[aftercamp1]" onclick="selafter(2,3);" <?php if($aftercamp1) { echo 'checked="checked"'; } ?> />
				<span class="lbl"></span>
				<?php echo JText::_("ADAG_AFT1"); ?>
				<br/>
				
				<input type="radio" value="1" id="aftercamp2" onclick="selafter(1,3);" name="show[aftercamp2]" <?php if($aftercamp2) { echo 'checked="checked"'; } ?> />
				<span class="lbl"></span>
				<?php echo JText::_("ADAG_AFT2"); ?>
				<br/>
				
				<input type="radio" value="1" id="aftercamp3" onclick="selafter(1,2);" name="show[aftercamp3]" <?php if($aftercamp3) { echo 'checked="checked"'; } ?> />
				<span class="lbl"></span>
				<?php echo JText::_("ADAG_AFT3"); ?>
			</label>
			<div class="controls">
				
			</div>
		</div>
		
		<div class="well">
			<?php echo JText::_('CONFIGALLOWTOAD'); ?>
			<span class="editlinktip hasTip" title="<?php echo JText::_('CONFIGALLOWTOAD_TIP'); ?>" >
			<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<span style="text-decoration:none;">
				<a class="modal adagency-video-manager" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=13145504">
					<img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
					<?php echo JText::_("AD_VIDEO"); ?>   
				</a>
			</span>
		</div>
		
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWTREEADDSTANDARD'); ?> </label>
			<div class="controls">
					<?php
						$yes_cheched = "";
						if ($row->allowstand == "1") {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
				<input type="hidden" name="allowstand" value="0"> 
				<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="allowstand">
				<span class="lbl"></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWTREEADDADCODE'); ?> </label>
			<div class="controls">
				<?php
						$yes_cheched = "";
						if ($row->allowadcode == "1") {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
				<input type="hidden" name="allowadcode" value="0">
				<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="allowadcode">
				<span class="lbl"></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWTREEADDPOPUP'); ?> </label>
			<div class="controls">
					<?php
						$yes_cheched = "";
						if ($row->allowpopup == "1") {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
				<input type="hidden" name="allowpopup" value="0">
				<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="allowpopup">
				<span class="lbl"></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWTREEADDFLASH'); ?> </label>
			<div class="controls">
					<?php
						$yes_cheched = "";
						if ($row->allowswf == "1") {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
				<input type="hidden" name="allowswf" value="0">
				<input type="checkbox" <?php echo $yes_cheched; ?>  value="1" class="ace-switch ace-switch-5" name="allowswf">
				<span class="lbl"></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWTREEADDTEXTLINK'); ?> </label>
			<div class="controls">
					<?php
						$yes_cheched = "";
						if ($row->allowtxtlink == "1") {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
				<input type="hidden" name="allowtxtlink" value="0">
				<input type="checkbox" <?php echo $yes_cheched; ?>  value="1" class="ace-switch ace-switch-5" name="allowtxtlink">
				<span class="lbl"></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWTREEADDTRANSITION'); ?> </label>
			<div class="controls">
				<?php
						$yes_cheched = "";
						if ($row->allowtrans == "1") {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
				<input type="hidden" name="allowtrans" value="0">
				<input type="checkbox" <?php echo $yes_cheched; ?>  value="1" class="ace-switch ace-switch-5" name="allowtrans">
				<span class="lbl"></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWTREEADDFLOATING'); ?> </label>
			<div class="controls">
				<?php
						$yes_cheched = "";
						if ($row->allowfloat == "1") {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
				<input type="hidden" name="allowfloat" value="0">
				<input type="checkbox" <?php echo $yes_cheched; ?>  value="1" class="ace-switch ace-switch-5" name="allowfloat">
				<span class="lbl"></span>
			</div>
		</div>
        <?php
        	if($this->isJomSocialStreamAd){
		?>
                <div class="control-group">
                    <label class="control-label"> <?php echo JText::_('VIEWTREEADDJOMSOCIAL'); ?> </label>
                    <div class="controls">
                        <?php
                                $yes_cheched = "";
                                /*if ($row->allowsocialstream == "1") {
                                    $yes_cheched = 'checked="checked"';
                                }
                                */
                            ?>
                        <input type="hidden" name="allowsocialstream" value="0">
                        <input type="checkbox" disabled="disabled" <?php echo $yes_cheched; ?>  value="1" class="ace-switch ace-switch-5" name="allowsocialstream">
                        <span class="lbl"></span>
                    </div>
                </div>
		<?php
        	}
			else{
		?>
        		<input type="hidden" name="allowsocialstream" value="0" />
        <?php
			}
		?>
		
		
		<div class="well"><?php echo JText::_('FORCE_HTTPS'); ?></div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('FORCE_HTTPS'); ?>
			</label>
			<div class="controls">
			   <fieldset class="radio btn-group" id="forcehttps">
					<?php
						
						$yes_cheched = "";
						
						if (isset($row->forcehttps) && ($row->forcehttps == "1")) {
							$yes_cheched = 'checked="checked"';
						}
					?>
					<input type="hidden" name="forcehttps" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="forcehttps">
					<span class="lbl"></span>
				</fieldset>
			</div>
		</div>
		
		<div class="well"><?php echo JText::_('ADAG_PROMO_CODES'); ?></div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_SHOW_PROMOCODE'); ?>
			</label>
			<div class="controls">
			   <fieldset class="radio btn-group" id="showpromocode">
					<?php
						
						$yes_cheched = "";						
						if (isset($row->forcehttps) && $row->showpromocode == "1") {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
					<input type="hidden" name="showpromocode" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="showpromocode">
					<span class="lbl"></span>
				</fieldset>
			</div>
		</div>
	</div>
	
	<div style="display:<?php echo $payments_active; ?>;" id="payments">
		<div class="well"><?php echo JText::_('VIEWCONFIGCATPAYSETT'); ?></div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_CURRECNY_PRICE'); ?>
			</label>
			<div class="controls">
				<?php
					$currency_price = 0;
					if(isset($row->params["currency_price"])){
						$currency_price = $row->params["currency_price"];
					}
				?>
				
				<input type="radio" value="0" disabled="disabled" name="params[currency_price]" <?php if($currency_price == 0){echo 'checked="checked"'; } ?> />
				<span class="lbl"></span>
				<?php echo JText::_("ADAG_BEFORE_PRICE"); ?>
				&nbsp;&nbsp;
				<input type="radio" value="1" disabled="disabled" name="params[currency_price]" <?php if($currency_price == 1){echo 'checked="checked"'; } ?> />
				<span class="lbl"></span>
				<?php echo JText::_("ADAG_AFTER_PRICE"); ?>
			</div>
		</div>
		
		<table class="table table-striped">	
			<tr class="<?php  echo "row1"; ?>">
				<td>
					<?php echo JText::_('AD_PAYMENT_CURRENCY');?>
                </td>

                <td>
					<table>
						<tr>
							<td style="border:none;">
								<?php echo $currency_list; ?>
							</td>
							<td style="border:none;">
								<?php
								$flag = 0;
								echo '<span style="vertical-align:middle" id="plugins_to_currencies">';
								global $k;
								
								foreach ($plugs as $i => $v) {
									if($i == $default_currency){
										$v1 = $v["0"];
										
										if(count ($v1) != count($normal_plugs)){
											foreach($normal_plugs as $n => $p){
												foreach($v1 as $i2 => $v2) {
														if ($p->plugname == $v2->plugname) {
														$flag = 1;
														break;
													} else {
														 $flag = 0;
													}
												}
												if (!$flag) echo ($p->plugname."<br/>");

											}
										}
										elseif (count($v1) != 0) {
										}
										else {
										}
										break;
									}
								}
								echo '</span>';
								?>
							</td>
						</tr>
					</table>
				</td>
				<td class="center">
					<?php  echo JText::_('AD_PAYMENT_DEFAULT');?>
				</td>
				<td class="center">
					<?php  echo JText::_('AD_PAYMENT_TEST_MODE');?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				
				global $mosConfig_absolute_path, $plugin_handler;
				$content = '';
				$content .= '
					<style type="text/css">
						.plug_unpublished {
							color:gray;
						}
						.plug_published {
							color:black;
						}
					</style>
				';
				
				if( count($test) > 0)
					foreach( $test as $i => $v) {
						if ($v["published"] == 1) {
							$content .= '
								<tr class="row'.$k.'">
									<td>
										<a href="index.php?option=com_adagency&controller=adagencyPlugins&task=edit&cid[]='.$v["id"].'">
											'.$v["header"].'
										</a>
									</td>

									<td width="170px"> 
										<table>';
											foreach ($v['name'] as $i1 => $v1 ) {
												$content .= '<tr>
																<td>
																	'.$v["header1"][$i1].'
																</td>';
												$content .= '</tr>';
											}
											$content .= '
										</table>
									</td>
									<td style="text-align:center">';
										if( $v["pluginname"] == $defaultplug ) {
											$content .= '<input value="'.$v["isdef"].'" disabled="disabled" type="radio" name="default" checked="checked"/><span class="lbl"></span>' ;
										}
										else{
											$content .= '<input value="'.$v["isdef"].'" disabled="disabled" type="radio" name="default" /><span class="lbl"></span>' ;
										}
										$content .= "
									</td>" ;
										if (isset ($v["sandbox"])) {
											$content .= '
									<td style="text-align:center">
										<input type="checkbox" '.((isset($v["sandbox"])&& $v['sbx'] == 1)?"checked":"").' disabled value="1" name="'.$v["sandbox"].'" /><span class="lbl"></span>
									</td>';
										}
										else {
											$content .= 
									'<td style="text-align:center">
										<input type="checkbox" disabled /><span class="lbl"></span>
									</td>';
										}
										if(isset ($v["reqhttps"])){
											$content .= 
									'<td style="display:none">
										<input type="checkbox" '.((isset($v["reqhttps"])&& $v['reqhttps'] == 1)?"checked":"").' disabled="disabled" value="1" name="'.$v["reqhttps_name"].'" /><span class="lbl"></span>
									</td>';
										}
										else {
											$content .= '
									<td style="display:none">
										<input type="checkbox" disabled /><span class="lbl"></span>
									</td>';
										}
										$content .= "
								</tr>";


							$k = 1 - $k;
						}
						else {
							$content .= '
								<tr class="row'.$k.'">
									<td>
										<a href="index.php?option=com_adagency&controller=adagencyPlugins&task=edit&cid[]='.$v["id"].'">
											'.$v["header"].'
										</a>
									</td>
									
									<td width="170px">
										<table>';
											if(isset($v) && isset($v['name']) && is_array($v['name']) && count($v['name']) > 0){
												foreach ($v['name'] as $i1 => $v1 ) {
													$content .= '<tr>
																	<td>
																		'.$v["header1"][$i1].'
																	</td>';
																	$content .= '
																</tr>';

												}
											}

											$content .= 
										'</table>
									</td>
									<td style="text-align:center">';
										$content .= '<input value="'.$v["isdef"].'" type="radio" name="default" disabled/> <span class="lbl"></span>' ;
										$content .= "
									</td>" ;
										if (isset ($v["sandbox"])) {
											$content .= '
									<td style="text-align:center">
										<input type="checkbox" '.((isset($v["sandbox"])&& $v['sbx'] == 1)?"checked":"").' disabled value="1" name="'.$v["sandbox"].'" /><span class="lbl"></span>
									</td>';
										} else {
											$content .= '
									<td style="text-align:center">
										<input type="checkbox" disabled /><span class="lbl"></span>
									</td>';
										}
										if (isset ($v["reqhttps"])) {
											$content .= '
									<td style="display:none">
										<input type="checkbox" '.((isset($v["reqhttps"])&& $v['reqhttps'] == 1)?"checked":"").' disabled value="1" name="'.$v["reqhttps_name"].'" /><span class="lbl"></span>
									</td>';
										} else {
											$content .= '
									<td style="display:none">
										<input type="checkbox" disabled /><span class="lbl"></span>
									</td>';
										}
										$content .= "
								</tr>";
								$k = 1 - $k;

						}
					}
			echo ($content);
			?>
		</table>
		<?php
			/*echo "<div style='font-size:13px; font-weight: bold;'>
					<a href='index.php?option=com_adagency&controller=adagencyPlugins'>".
						JText::_('Plugin Manager - Upload new payment plugins here')."
					</a>
				</div>";*/
		?>
	</div>
	
	<div style="display:<?php echo $email_active; ?>;" id="email">
		<div class="well">
			<?php echo JText::_('VIEWCONFIGEMAILSSET');?>
			<a class="modal adagency-video pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674033">
				<img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
				<?php echo JText::_("COM_ADAGENCY_VIDEO_EMAIL_SETTINGS"); ?>   
			</a>
		</div>
		
        <table>
        	<tr>
            	<th id="th-send-email" style="display:none;"><?php echo JText::_("ADAG_SEND_EMAIL"); ?></th>
            </tr>
        	<tr>
            	<td>
                	<div id="send_1" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_after_reg'])||($row->params['send_after_reg']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_after_reg]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_after_reg]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_2" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_after_reg_auto_app'])||($row->params['send_after_reg_auto_app']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_after_reg_auto_app]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_after_reg_auto_app]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_3" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_after_reg_need_act'])||($row->params['send_after_reg_need_act']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_after_reg_need_act]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_after_reg_need_act]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_4" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_report_to_advertiser'])||($row->params['send_report_to_advertiser']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_report_to_advertiser]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_report_to_advertiser]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_5" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_camp_app'])||($row->params['send_camp_app']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_camp_app]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_camp_app]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_6" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_camp_dis'])||($row->params['send_camp_dis']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_camp_dis]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_camp_dis]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_7" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_ban_app'])||($row->params['send_ban_app']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_ban_app]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_ban_app]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_8" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_ban_dis'])||($row->params['send_ban_dis']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_ban_dis]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_ban_dis]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_9" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_adv_dis'])||($row->params['send_adv_dis']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_adv_dis]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_adv_dis]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_10" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_campaign_expired'])||($row->params['send_campaign_expired']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_campaign_expired]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_campaign_expired]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_11" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_ban_added'])||($row->params['send_ban_added']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_ban_added]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_ban_added]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_12" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_camp_added'])||($row->params['send_camp_added']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_camp_added]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_camp_added]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_13" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_advertiser_reg'])||($row->params['send_advertiser_reg']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_advertiser_reg]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_advertiser_reg]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_14" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_camp_expired'])||($row->params['send_camp_expired']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_camp_expired]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_camp_expired]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                    
                    <div id="send_15" style="display:none;">
                    	<fieldset class="radio btn-group" id="showtxtimg_yes">
							<?php
                                $no_checked = "";
                                $yes_cheched = "";
								
                                if(!isset($row->params['send_ad_modified'])||($row->params['send_ad_modified']==1)) {
                                    $yes_cheched = 'checked="checked"';
                                }
                                else{
                                    $no_checked = 'checked="checked"';
                                }
                            ?>
                            <input type="hidden" name="params[send_ad_modified]" value="0">
                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[send_ad_modified]">
                            <span class="lbl"></span>
                        </fieldset>
                    </div>
                </td>
            </tr>
        </table>
        
		<table class="adminform">
            <tr>
				<th align="left" colspan="2" id="variables" style="display:none;">
					<?php echo JText::_('CONFIGVARIAB');?>
				</th>
			</tr>
			<tr>
				<td colspan="2">
                    <span id="vbl0" style="display: none;"><?php echo JText::_('CONFIGPLEASEUSEVARS');?><br></span>
                    <span id="vbl1" style="display: none;">{name} - <?php echo JText::_('CONFIGCNAME');?><br></span>
                    <span id="vbl11" style="display: none;">{company} - <?php echo JText::_('VIEWADVERTISERCOMPNAME');?><br></span>
                    <span id="vbl2" style="display: none;">{login} - <?php echo JText::_('CONFIGLNAME');?><br></span>
                    <span id="vbl3" style="display: none;">{password} - <?php echo JText::_('CONFIGPASSWD');?><br></span>
                    <span id="vbl4" style="display: none;">{email} - <?php echo JText::_('CONFIGADVEM');?><br></span>
                    <span id="vbl15" style="display: none;">{phone} - <?php echo JText::_('ADAG_PHONE');?><br></span>
                    <span id="vbl20" style="display: none;">{url} - <?php echo JText::_('ADAG_URL');?><br></span>
                    <span id="vbl21" style="display: none;">{username} - <?php echo JText::_('ADAG_USER');?><br></span>
                    <span id="vbl22" style="display: none;">{description} - <?php echo JText::_('VIEWORDERSORDERDESC');?><br></span>
                    <span id="vbl23" style="display: none;">{street} - <?php echo JText::_('ADAG_STREET');?><br></span>
                    <span id="vbl24" style="display: none;">{country} - <?php echo JText::_('VIEWADVERTISERCOUNTRY');?><br></span>
                    <span id="vbl25" style="display: none;">{state} - <?php echo JText::_('ADAG_STATE');?><br></span>
                    <span id="vbl26" style="display: none;">{zipcode} - <?php echo JText::_('ADAG_ZIPCODE');?><br></span>
                    <span id="vbl27" style="display: none;">{approve_advertiser_url} - <?php echo JText::_('ADAG_AAMSG');?><br></span>
                    <span id="vbl28" style="display: none;">{decline_advertiser_url} - <?php echo JText::_('ADAG_DAMSG');?><br></span>
                    <span id="vbl5" style="display: none;">{activate_url} - <?php echo JText::_('CONFIGLNKADV');?><br></span>
                    <span id="vbl6" style="display: none;">{campaign} - <?php echo JText::_('CONFIGCMP');?><br></span>
                    <span id="vbl7" style="display: none;">{banner} - <?php echo JText::_('AD_BANNER_NAME');?><br></span>
                    <span id="vbl12" style="display: none;">{banner_preview_url} - <?php echo JText::_('ADAG_BANPREV');?><br></span>
                    <span id="vbl17" style="display: none;">{package} - <?php echo JText::_('ADAG_PCKDTS');?><br></span>
                    <span id="vbl13" style="display: none;">{approve_banner_url} - <?php echo JText::_('ADAG_BAMSG');?><br></span>
                    <span id="vbl19" style="display: none;">{decline_banner_url} - <?php echo JText::_('ADAG_BDMSG');?><br></span>
                    <span id="vbl16" style="display: none;">{approve_campaign_url} - <?php echo JText::_('ADAG_CMPVAPR');?><br></span>
                    <span id="vbl18" style="display: none;">{decline_campaign_url} - <?php echo JText::_('ADAG_CMPVDEN');?><br></span>
                    <span id="vbl14" style="display: none;">{approval_status} - <?php echo JText::_('ADAG_APRSTS');?><br></span>
                    <span id="vbl8" style="display: none;">{daterange} - <?php echo JText::_('CONFIGRPDRNG');?><br></span>
                    <span id="vbl9" style="display: none;">{clicks} - <?php echo JText::_('AGENCYCLICKS');?><br></span>
                    <span id="vbl10" style="display: none;">{impressions} - <?php echo JText::_('AGENCYIMPRESSIONS');?><br></span>
                    <span id="vbl31" style="display: none;">{campaign_renew_URL} - <?php echo JText::_('ADAG_TEMPL_RENURL');?><br></span>
                    <span id="vbl32" style="display: none;">{packages_url} - <?php echo JText::_('ADAG_TEMPL_PACKURL');?><br></span>
                    <span id="vbl33" style="display: none;">{expire_date} - <?php echo JText::_('ADAG_TEMPL_EXPDATE');?><br></span>
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<strong><?php echo JText::_('ADAG_SEL_EM_TEMP'); ?></strong>&nbsp;&nbsp;
					<select id="showem" disabled onchange="javascript:buz(this.options[this.options.selectedIndex].id);">
					<option>- select -</option>
					<optgroup label="Advertiser">
					<option value="1" id="1"><?php echo JText::_('CONFIGTEXTAFTER');?></option>
					<option value="2" id="2"><?php echo JText::_('CONFIGBODYAFTER');?></option>
					<option value="14" id="14"><?php echo JText::_('ADAG_REGAA');?></option>
					<option value="3" id="3"><?php echo JText::_('CONFIGBODYACTIV');?></option>
					<option value="4" id="4"><?php echo JText::_('CONFIGREPORTEMAIL');?></option>
					<option value="5" id="5"><?php echo JText::_('CONFIGCMPAPP');?></option>
					<option value="6" id="6"><?php echo JText::_('CONFIGCMPDISAPP');?></option>
					<option value="7" id="7"><?php echo JText::_('CONFIGADAPP');?></option>
					<option value="8" id="8"><?php echo JText::_('CONFIGADDISAPP');?></option>
					<option value="9" id="9"><?php echo JText::_('CONFIGUSERDISAPP');?></option>
					<option value="11" id="12"><?php echo JText::_('CONFIGCMPEXP');?></option>
					</optgroup>
					<optgroup label="Admin">
					<option value="12" id="10"><?php echo JText::_('CONFIGBANNERAD');?></option>
					<option value="10" id="11"><?php echo JText::_('CONFIGCMPADDED');?></option>
					<option value="13" id="13"><?php echo JText::_('CONFIGADVREGIST');?></option>
					<option value="15" id="15"><?php echo JText::_('ADAG_ADMCMPEXP');?></option>
					<option value="16" id="16"><?php echo JText::_('ADAG_ADMODIFIED');?></option>
					</optgroup>
					</select>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset1" style="display:none;">
				<div>
				 <?php
				 echo $editor->display( 'txtafterreg', ''.$row->txtafterreg,'100%', '330', '75', '50' );?>
				 </div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset2" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbafterreg" size="40" value="<?php echo $row->sbafterreg;?>" />
				 <?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodyafterreg', ''.$row->bodyafterreg,'100%', '330', '75', '50' );?>
				 </div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset14" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbafterregaa" size="40" value="<?php echo $row->sbafterregaa;?>" />
				 <?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodyafterregaa', ''.$row->bodyafterregaa,'100%', '330', '75', '50' );?>
				 </div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset3" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbactivation" size="40" value="<?php echo $row->sbactivation;?>" />
				 <?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodyactivation', ''.$row->bodyactivation,'100%', '330', '75', '50' );?>
				 </div>
				</td>
			</tr>
			
			<tr>
				<td colspan="2" id="emset4" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbrep" size="40" value="<?php echo $row->sbrep;?>" />
				<?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodyrep', ''.$row->bodyrep,'100%', '330', '75', '50' );?>
				 </div>
				</td>
			</tr>
			
			<tr>
				<td colspan="2"  id="emset5" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbcmpappv" size="40" value="<?php echo $row->sbcmpappv;?>" />
				<?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodycmpappv', ''.$row->bodycmpappv,'100%', '330', '75', '50' );?>
				</div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset6" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbcmpdis" size="40" value="<?php echo $row->sbcmpdis;?>" />
				<?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodycmpdis', ''.$row->bodycmpdis,'100%', '330', '75', '50' );?>
				</div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset7" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbadappv" size="40" value="<?php echo $row->sbadappv;?>" />
				 <?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodyadappv', ''.$row->bodyadappv,'100%', '330', '75', '50' );?>
				 </div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset8" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbaddisap" size="40" value="<?php echo $row->sbaddisap;?>" />
				 <?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodyaddisap', ''.$row->bodyaddisap,'100%', '330', '75', '50' );?>
				 </div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset9" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbadvdis" size="40" value="<?php echo $row->sbadvdis;?>" />
				 <?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodyadvdis', ''.$row->bodyadvdis,'100%', '330', '75', '50' );?>
				</div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset10" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbnewad" size="40" value="<?php echo $row->sbnewad;?>" />
				 <?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodynewad', ''.$row->bodynewad,'100%', '330', '75', '50' );?>
				</div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset11" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbnewcmp" size="40" value="<?php echo $row->sbnewcmp;?>" />
				 <?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodynewcmp', ''.$row->bodynewcmp,'100%', '330', '75', '50' );?>
				</div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset12" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbcmpex" size="40" value="<?php echo $row->sbcmpex;?>" />
				 <?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodycmpex', ''.$row->bodycmpex,'100%', '330', '75', '50' );?>
				</div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset13" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbnewuser" size="40" value="<?php echo $row->sbnewuser;?>" />
				 <?php $editor  =  JFactory::getEditor();
				 echo $editor->display( 'bodynewuser', ''.$row->bodynewuser,'100%', '330', '75', '50' );?>
				</div>
				</td>
			</tr>

			<tr>
				<td colspan="2" id="emset15" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbcmpexpadm" size="40" value="<?php echo $row->sbcmpexpadm;?>" />
				<?php
					$editor  =  JFactory::getEditor();
					echo $editor->display( 'bodycmpexpadm', ''.$row->bodycmpexpadm,'100%', '330', '75', '50' );
				?>
				</div>
				</td>
			</tr>
        
			<tr>
				<td colspan="2" id="emset16" style="display:none;">
				<div>
				<?php echo JText::_('CONFIGSBJ');?>: <input class="inputbox" type="text" name="sbadchanged" size="40" value="<?php echo $row->sbadchanged;?>" />
				<?php
					$editor  =  JFactory::getEditor();
					echo $editor->display( 'boadchanged', ''.$row->boadchanged,'100%', '330', '75', '50' );
				?>
				</div>
				</td>
			</tr>
		</table>
	</div>
	
	<div style="display:<?php echo $overview_active; ?>; opacity: 0.5;" id="overview">
		<div class="well">
			<?php echo JText::_('VIEWDSADMINOVERVIEW');?>
			<a class="modal adagency-video pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674043">
				<img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
				<?php echo JText::_("COM_ADAGENCY_VIDEO_OVERVIEW_SETTINGS"); ?>   
			</a>
		</div>
		
		<div class="">
			<?php echo JText::_('VIEWDSADMINOVERVIEW_INTRO');?>
			<br/>
			<?php echo JText::_('VIEWDSADMINOVERVIEW_AVAILABLEVAR'). ' : {packages}'.JText::_('VIEWDSADMINOVERVIEW_AVAILABLEVAR_TEXT') ;?>
		</div>
		<br/>
		<?php
			$editor = JFactory::getEditor();
			echo $editor->display( 'overviewcontent', ''.$row->overviewcontent,'100%', '300', '75', '50' );
		?>
	</div>
	
	<div style="display:<?php echo $registration_active; ?>;" id="registration">
		<div class="well">
			<?php echo JText::_('ADAG_REGISTRATION');?>
			<a class="modal adagency-video pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674046">
				<img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
				<?php echo JText::_("COM_ADAGENCY_VIDEO_REGISTRATION_SETTINGS"); ?>   
			</a>
		</div>
		<p><?php echo JText::_('ADAG_REG_MSSG'); ?></p>
		
		<table class="adminForm" style="width: 350px; font-size:14px; border:1px solid #CCCCCC; margin: 10px;" cellpadding="0" cellspacing="0">
			<tr style="background:#CCCCCC; color:#000000;">
				<th align="left" width="40" align="left" style="padding:6px;">&nbsp;</td>
				<th align="left" width="30%" align="left" style="padding:6px;"><?php echo JText::_('ADAG_SHOW');?></td>
				<th align="left" width="30%" align="left" style="padding:6px;"><?php echo JText::_('ADAG_MANDATORY');?></td>
			</tr>
			<tr>
				<td style="padding:4px;"><?php echo JText::_('VIEWADVERTISERURL');?></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->show)&&(in_array('url',$row->show))) { echo 'checked="checked"'; } ?> name="show[url]" disabled="disabled" value="1" /><span class="lbl"></span></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->mandatory)&&(in_array('url',$row->mandatory))) { echo 'checked="checked"'; } ?> name="mandatory[url]" disabled="disabled" value="1" /><span class="lbl"></span></td>
			</tr>
			<tr>
				<td style="padding:4px;"><?php echo JText::_('VIEWADVERTISERPHONE');?></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->show)&&(in_array('phone',$row->show))) { echo 'checked="checked"'; } ?> name="show[phone]" disabled="disabled" value="1" /><span class="lbl"></span></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->mandatory)&&(in_array('phone',$row->mandatory))) { echo 'checked="checked"'; } ?> name="mandatory[phone]" disabled="disabled" value="1" /><span class="lbl"></span></td>
			</tr>
			<tr>
				<td style="padding:4px;"><?php echo JText::_('VIEWADVERTISERCOMPANY');?></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->show)&&(in_array('company',$row->show))) { echo 'checked="checked"'; } ?> name="show[company]" disabled="disabled" value="1" /><span class="lbl"></span></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->mandatory)&&(in_array('company',$row->mandatory))) { echo 'checked="checked"'; } ?> name="mandatory[company]" disabled="disabled" value="1" /><span class="lbl"></span></td>
			</tr>
			<tr>
				<td style="padding:4px;"><?php echo JText::_('ADAG_EMREP');?></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->show)&&(in_array('email',$row->show))) { echo 'checked="checked"'; } ?> name="show[email]" disabled="disabled" value="1" /><span class="lbl"></span></td>
				<td style="padding:4px;">&nbsp;</td>
			</tr>
			<tr>
				<td style="padding:4px;"><?php echo JText::_('VIEWADVERTISERADDRESS');?></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->show)&&(in_array('address',$row->show))) { echo 'checked="checked"'; } ?> name="show[address]" disabled="disabled" value="1" /><span class="lbl"></span></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->mandatory)&&(in_array('address',$row->mandatory))) { echo 'checked="checked"'; } ?> name="mandatory[address]" disabled="disabled" value="1" /><span class="lbl"></span></td>
			</tr>
			<tr>
				<td style="padding:4px;"><?php echo JText::_('ADAG_CALCULATION');?></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->show)&&(in_array('calculation',$row->show))) { echo 'checked="checked"'; } ?> name="show[calculation]" disabled="disabled" value="1" /><span class="lbl"></span></td>
				<td style="padding:4px;">&nbsp;</td>
			</tr>
			<tr>
				<td style="padding:4px;"><?php echo JText::_('ADAG_CAPTCHA');?></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->show)&&(in_array('captcha',$row->show))) { echo 'checked="checked"'; } ?> name="show[captcha]" disabled="disabled" value="1" /><span class="lbl"></span></td>
				<td style="padding:4px;">&nbsp;</td>
			</tr>
			<tr style="display:none;">
				<td style="padding:4px;">- <?php echo JText::_('ADAG_REFR');?></td>
				<td style="padding:4px;"><input type="checkbox" <?php if(isset($row->show)&&(in_array('refresh',$row->show))) { echo 'checked="checked"'; } ?> name="show[refresh]" disabled="disabled" value="1" /><span class="lbl"></span></td>
				<td style="padding:4px;">&nbsp;</td>
			</tr>
		</table>
		
		<div class="well">
			<?php echo JText::_('ADAG_REGFL');?>
		</div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_USWIZ');?>
			</label>
			<div class="controls">
				<?php
					$no_checked = "";
					$yes_cheched = "";
					
					if(isset($row->show)&&(in_array('wizzard',$row->show))) {
						$yes_cheched = 'checked="checked"';
					}
					else{
						$no_checked = 'checked="checked"';
					}
				?>
				<input type="hidden" name="show[wizzard]" value="0">
				<input type="checkbox" disabled="disabled" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="show[wizzard]">
				<span class="lbl"></span>
				
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_USWIZ_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
				
                <a class="modal adagency-video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674047">
                    <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
                    <?php echo JText::_("COM_ADAGENCY_VIDEO_REGISTRATION_SETTINGS"); ?>   
                </a>
			</div>
		</div>
		
		<div class="well">
			<?php echo JText::_('ADAG_OPTNOWIZ');?>
		</div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_NWONE');?>
			</label>
			<div class="controls">
				<?php
					$no_checked = "";
					$yes_cheched = "";
					
					if(isset($row->show)&&(in_array('nwone',$row->show))) {
						$yes_cheched = 'checked="checked"';
					}
					else{
						$no_checked = 'checked="checked"';
					}
				?>
				<input type="hidden" name="show[nwone]" value="0">
				<input type="checkbox" disabled="disabled" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="show[nwone]">
				<span class="lbl"></span>
				
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_NWONE_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_NWTWO');?>
			</label>
			<div class="controls">
				<?php
					$no_checked = "";
					$yes_cheched = "";
					
					if(isset($row->show)&&(in_array('nwtwo',$row->show))){
						$yes_cheched = 'checked="checked"';
					}
					else{
						$no_checked = 'checked="checked"';
					}
				?>
				<input type="hidden" name="show[nwtwo]" value="0">
				<input type="checkbox" disabled="disabled" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="show[nwtwo]">
				<span class="lbl"></span>
				
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_NWTWO_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('CONFIGTERMSANDCOND');?>
			</label>
			<div class="controls">
				<?php
					$no_checked = "";
					$yes_cheched = "";
					
					if($row->askterms == '1'){
						$yes_cheched = 'checked="checked"';
					}
					else{
						$no_checked = 'checked="checked"';
					}
				?>
				<input type="hidden" name="askterms" value="0">
				<input type="checkbox" disabled="disabled" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="askterms">
				<span class="lbl"></span>
				
				<?php echo JText::_('CONFIGTERMSANDCONDID');?>&nbsp;
				<input size="11" type="text" name="termsid" disabled="disabled" id="termsid" value="<?php echo (($row->termsid >0) ? $row->termsid : '');?>" />&nbsp;
				<a href="index.php?option=com_content&tmpl=component" rel="{handler: 'iframe', size: {x: 700, y: 450}}" class="modal">
					<?php echo JText::_('ADAG_VIEW_ART');?>
				</a>
				
				<span class="editlinktip hasTip" title="<?php echo JText::_('CONFIGTERMSANDCOND_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
	</div>
	
	<div style="display:<?php echo $approvals_active; ?>;" id="approvals">
		<div class="well">
			<?php echo JText::_('ADAG_APPROVALS');?>
			<a class="modal adagency-video pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674035">
				<img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
				<?php echo JText::_("COM_ADAGENCY_VIDEO_APPROVALS_SETTINGS"); ?>   
			</a>
		</div>
		
		<div class="control-group" style="display: flex; height: 0; margin: 0; visibility: hidden;">
			<label class="control-label">
				<?php echo JText::_('ADAG_AA_ADV');?>
			</label>
			<div class="controls">
				<select name="aa[advertis]" disabled="disabled">
					<option value='P' <?php if($approvals['adv']=='N') { echo "selected='selected'";}?>>No</option>
					<option value='Y' <?php if($approvals['adv']=='Y') { echo "selected='selected'";}?>>Yes</option>
				</select>
				
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_AA_ADV_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_AA_ADS');?>
			</label>
			<div class="controls">
				<select name="aa[banners]" disabled="disabled">
					<option value='P' <?php if($approvals['ads']=='N') { echo "selected='selected'";}?>>No</option>
					<option value='Y' <?php if($approvals['ads']=='Y') { echo "selected='selected'";}?>>Yes</option>
				</select>
				
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_AA_ADS_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_AA_CMP');?>
			</label>
			<div class="controls">
				<select name="aa[campaign]" disabled="disabled">
					<option value='P' <?php if($approvals['cmp']=='N') { echo "selected='selected'";}?>>No</option>
					<option value='Y' <?php if($approvals['cmp']=='Y') { echo "selected='selected'";}?>>Yes</option>
				</select>
				
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_AA_CMP_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_AA_RENEW_CMP');?>
			</label>
			<div class="controls">
				<select name="aa[campaign_2]" disabled="disabled">
					<option value='0' <?php if($approvals['renewcmp']=='0') { echo "selected='selected'";}?>>No</option>
					<option value='1' <?php if($approvals['renewcmp']=='1') { echo "selected='selected'";}?>>Yes</option>
					<option value='2' <?php if($approvals['renewcmp']=='2') { echo "selected='selected'";}?>><?php echo JText::_("ADAG_ASK_ADVERTISER"); ?></option>
				</select>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_AA_RENEW_CMP_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
				&nbsp;&nbsp;
				<p class="text-error"><?php echo JText::_('ADAG_AA_RENEW_CMP_MESSAGE'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('ADAG_AA_ALLOW_ADD_KEYWORDS');?>
			</label>
			<div class="controls">
				<select name="allow_add_keywords" disabled="disabled">
					<option value='0' <?php if($approvals['allow_add_keywords']=='0') { echo "selected='selected'";}?>>No</option>
					<option value='1' <?php if($approvals['allow_add_keywords']=='1') { echo "selected='selected'";}?>>Yes</option>
				</select>
			</div>
		</div>
	</div>
	
	<div style="display:<?php echo $jomsocial_active; ?>;" id="jomsocial">
		<ul class="nav nav-tabs">
            <li class="active"><a href="#ads_settings" data-toggle="tab"><?php echo JText::_('ADAG_ADS_SETTINGS');?></a></li>
            <li><a href="#social" data-toggle="tab"><?php echo JText::_('ADAG_JOMSOC_TARGETING');?></a></li>
        </ul>
        
        <div class="tab-content">
        	<div class="tab-pane active" id="ads_settings">
			<?php
				// check if JomSocial is installed and if is greater than 4.0.0
				$version = $this->isJomSocialStreamAd;
				if($version >= 4){
			?>
                <div class="row-fluid">
                	<div class="span6">
                    	<fieldset>
							<legend><?php echo JText::_("VIEWCONFIGCATGENERAL"); ?></legend>
							
                            <input type="hidden"  name="params[js_ad_location]" value="geolocation" />
                            
                            <div class="control-group">
                                <label class="control-label">
                                    <?php echo JText::_('ADAG_TARGET_AUDIENCE_PREVIEW');?>
                                </label>
                                <div class="controls" style="margin-left:180px !important;">
                                   <fieldset class="radio btn-group" id="showtxtimg_yes">
                                        <?php
                                            $no_checked = "";
                                            $yes_cheched = "";
                                            
                                            if(!isset($row->params['target_audience_preview'])||($row->params['target_audience_preview']==1)) {
                                                $yes_cheched = 'checked="checked"';
                                            }
                                            else{
                                                $no_checked = 'checked="checked"';
                                            }
                                        ?>
                                        <input type="hidden" name="params[target_audience_preview]" value="0">
                                        <input type="checkbox" disabled="disabled" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[target_audience_preview]">
                                        <span class="lbl"></span>
                                    </fieldset>
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <label class="control-label">
                                    <?php echo JText::_('ADAG_SHOW_JS_STREAM_ADS_ON');?>
                                </label>
                                <div class="controls" style="margin-left:180px !important;">
                                    <?php
										if(!isset($row->params['js_stream_ads_on'])){
											$row->params['js_stream_ads_on'] = array();
										}
									?>
                                    <ul style="list-style-type:none;">
                                    	<li>
                                            <input type="checkbox" disabled="disabled" name="params[js_stream_ads_on][]" <?php if(in_array("front_page_stream", $row->params['js_stream_ads_on'])){echo 'checked="checked"';} ?> value="front_page_stream">
                                            <span class="lbl"></span>
                                            <?php echo JText::_("ADAG_FRONT_PAGE_STREAM"); ?>
										</li>
                                        <li>
                                            <input type="checkbox" disabled="disabled" name="params[js_stream_ads_on][]" <?php if(in_array("profile_stream", $row->params['js_stream_ads_on'])){echo 'checked="checked"';} ?> value="profile_stream">
                                            <span class="lbl"></span>
                                            <?php echo JText::_("ADAG_PROFILE_STREAM"); ?>
										</li>
                                        <li>
                                            <input type="checkbox" disabled="disabled" name="params[js_stream_ads_on][]" <?php if(in_array("event_stream", $row->params['js_stream_ads_on'])){echo 'checked="checked"';} ?> value="event_stream">
                                            <span class="lbl"></span>
                                            <?php echo JText::_("ADAG_EVENT_STREAM"); ?>
										</li>
                                        <li>
                                            <input type="checkbox" disabled="disabled" name="params[js_stream_ads_on][]" <?php if(in_array("group_stream", $row->params['js_stream_ads_on'])){echo 'checked="checked"';} ?> value="group_stream">
                                            <span class="lbl"></span>
                                            <?php echo JText::_("ADAG_GROUP_STREAM"); ?>
                                   		</li>
									</ul>
                                </div>
                            </div>
                            
						</fieldset>
                    </div>
                    <div class="span6">
                    	<fieldset>
                        	<legend><?php echo JText::_("ADAG_JOMSOCIAL_STREAM_ADS"); ?></legend>
                    		<div class="control-group">
                                <label class="control-label">
                                    <?php echo JText::_('ADAG_HEADLINE_CHARACTER_LIMIT');?>
                                </label>
                                <div class="controls" style="margin-left:180px !important;">
                                    <input type="text" disabled="disabled" name="params[headline_limit]" size="5" value="<?php echo intval($row->params['headline_limit']); ?>" />
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <label class="control-label">
                                    <?php echo JText::_('ADAG_CONTENT_CHARACTER_LIMIT');?>
                                </label>
                                <div class="controls" style="margin-left:180px !important;">
                                    <input type="text" disabled="disabled" name="params[content_limit]" size="5" value="<?php echo intval($row->params['content_limit']); ?>" />
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <label class="control-label">
                                    <?php echo JText::_('ADAG_SPONSORED_INFO');?>
                                </label>
                                <div class="controls" style="margin-left:180px !important;">
                                   <fieldset class="radio btn-group" id="showtxtimg_yes">
                                        <?php
                                            $no_checked = "";
                                            $yes_cheched = "";
                                            
                                            if(!isset($row->params['show_sponsored_stream_info'])||($row->params['show_sponsored_stream_info']==1)) {
                                                $yes_cheched = 'checked="checked"';
                                            }
                                            else{
                                                $no_checked = 'checked="checked"';
                                            }
                                        ?>
                                        <input type="hidden" name="params[show_sponsored_stream_info]" value="0">
                                        <input type="checkbox" disabled="disabled" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[show_sponsored_stream_info]">
                                        <span class="lbl"></span>
                                    </fieldset>
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <label class="control-label">
                                    <?php echo JText::_('ADAG_CREATE_AD_LINK');?>
                                </label>
                                <div class="controls" style="margin-left:180px !important;">
                                   <fieldset class="radio btn-group" id="showtxtimg_yes">
                                        <?php
                                            $no_checked = "";
                                            $yes_cheched = "";
                                            
                                            if(!isset($row->params['show_create_ad_link'])||($row->params['show_create_ad_link']==1)) {
                                                $yes_cheched = 'checked="checked"';
                                            }
                                            else{
                                                $no_checked = 'checked="checked"';
                                            }
                                        ?>
                                        <input type="hidden" name="params[show_create_ad_link]" value="0">
                                        <input type="checkbox" disabled="disabled" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[show_create_ad_link]">
                                        <span class="lbl"></span>
                                    </fieldset>
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <label class="control-label">
                                    <?php echo JText::_('ADAG_IMAGE_CONTENT_SIZE');?>
                                </label>
                                <div class="controls" style="margin-left:180px !important;">
                                   <?php
										$image_content_height = 270;
										if(isset($row->params['image_content_height']) && intval($row->params['image_content_height']) > 0){
											$image_content_height = intval($row->params['image_content_height']);
										}
								   ?>
                                   <input type="text" size="1" disabled="disabled" value="<?php echo $image_content_height; ?>" name="params[image_content_height]"> px
                                </div>
                            </div>
                            
						</fieldset>
                    </div>
                </div>
                <div class="row-fluid">
                	<div class="span6">
                    	<fieldset>
                        	<legend><?php echo JText::_("ADAG_DISPLAY"); ?></legend>
                    		<div class="control-group">
                                <div class="controls" style="margin-left:0px !important;">
                                    <input type="radio" disabled="disabled" value="0" name="params[display_stream_ads]" <?php if($row->params['display_stream_ads'] == "0"){ echo 'checked="checked"';} ?> />
                                    <span class="lbl"></span>
                                    <?php echo JText::_('ADAG_DISPLAY_JS_ADS_EVERY');?>
                                    <select name="params[display_stream_ads_every_value]" disabled="disabled" class="input-mini">
                                    	<?php
											for($i=1; $i<=50; $i++){
												$selected = "";
												if($i == $row->params['display_stream_ads_every_value']){
													$selected = 'selected="selected"';
												}
                                        		echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
											}
										?>
                                    </select>
                                    <?php echo JText::_('ADAG_JOMSOCIAL_STREAM_ITEMS');?>
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <div class="controls" style="margin-left:0px !important;">
                                    <input type="radio" disabled="disabled" value="1" name="params[display_stream_ads]" <?php if($row->params['display_stream_ads'] == "1"){ echo 'checked="checked"';} ?> />
                                    <span class="lbl"></span>
                                    <?php echo JText::_('ADAG_DISPLAY_JS_ADS_AFTER');?>
                                    <select name="params[display_stream_ads_after_value]" disabled="disabled" class="input-mini">
                                    	<?php
											for($i=1; $i<=50; $i++){
												$selected = "";
												if($i == $row->params['display_stream_ads_after_value']){
													$selected = 'selected="selected"';
												}
                                        		echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
											}
										?>
                                    </select>
                                    <?php echo JText::_('ADAG_JOMSOCIAL_STREAM_ITEMS');?>
                                </div>
                            </div>
						</fieldset>
                    </div>
                </div>
			<?php
                }
				elseif($version < 4){
					$text = '<p class="text-error">'.JText::_("ADAG_INSTALL_LATEST_JOMSOCIAL").'</p>
<a href="http://www.jomsocial.com/" target="_blank"><img src="http://ijoomla.com/images/jomsocial-adagency.fw.png" width="1140" height="600"  alt=""/></a>';
            ?>
                    <div>
                        <div class="alert alert-notice">
                            <p><?php echo $text; ?></p>
                        </div>
                    </div>
            <?php
				}
				elseif($version === FALSE){
					$text = '<p class="text-error">'.JText::_("ADAG_INSTALL_JOMSOCIAL").'</p>
<a href="http://www.jomsocial.com/" target="_blank"><img src="http://ijoomla.com/images/jomsocial-adagency.fw.png" width="1140" height="600"  alt=""/></a>';
            ?>
                    <div>
                        <div class="alert alert-notice">
                            <p><?php echo $text; ?></p>
                        </div>
                    </div>
            <?php
				}
            ?>
            </div>
            
            <div class="tab-pane" id="social">
            	<div class="well">
					<?php echo JText::_('ADAG_JOMSOC');?>
                    <a class="modal adagency-video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674049">
                        <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
                        <?php echo JText::_("COM_ADAGENCY_VIDEO_JOMSOC_SETTINGS"); ?>   
                    </a>
                </div>
                
                <?php
                    if ($this->isJomSocial){
                ?>
                        <div class="well well-minimized">
                            <?php echo JText::_('ADAG_ALLOW_JOMSOC'); ?>
                        </div>
                        <input type="checkbox" disabled="disabled" name="params[jom_front]" <?php 
                            if (isset($row->params['jom_front'])) { 
                                echo ' checked="checked" ';
                            } ?> value="1" />
                        <span class="lbl"></span>
                            
                        <span style="font-size: 13px;">
                            <?php echo ucfirst(JText::_('ADAG_FRONTEND')); ?>
                        </span>
                        <br />
                        
                        <input type="checkbox" disabled="disabled" name="params[jom_back]" <?php 
                            if (isset($row->params['jom_back'])) { 
                                echo ' checked="checked" ';
                            } ?> value="1" />
                            <span class="lbl"></span>
            
                        <span style="font-size: 13px;">
                            <?php echo ucfirst(JText::_('ADAG_BACKEND')); ?>
                        </span>
                        <br /><br />
            
                        <div class="well">
                            <?php echo JText::_("ADAG_JOMFIELDS_TO_INCLUDE"); ?>:
                        </div>
                        
                        <?php echo "<span style='font-size: 13px;'>" . JText::sprintf('ADAG_JOMINFO', "<a href='".JURI::root()."administrator/index.php?option=com_community&view=profiles'>", '</a>') . "</span>"; ?> 
                        <?php echo JText::_('ADAG_JOMSOC_INFO'); ?></p>
                        
                        <table class="adminForm" border="0" style='margin-left: -4px; margin-top: 10px; width: 100%;' cellpadding="3" cellspacing="3">
                            <?php foreach ($this->jomFields as $group) { ?>
                            <?php if (!isset($group->opts)) continue; ?>	
                            <tr>
                                <td colspan="2">
                                    <div class="well well-minimized"><?php echo $group->name; ?></div>
                                </td>
                            </tr>
                            <?php foreach ($group->opts as $field) { ?>
                            <tr>
                                <td width="5">
                                    <input type="checkbox" disabled="disabled" name="jomfields[]" value="<?php echo $field->id; ?>" 
                                    <?php if (in_array($field->id, $row->jomfields)) { echo ' checked="checked" '; } ?>
                                    /> 
                                    <span class="lbl"></span>
                                </td>
                                <td align="left">
                                    <?php echo $field->name; ?>
                                </td>
                            </tr>
                            <?php } } ?>
                        </table>
            <?php 	}
                    else{
                        $extensions = get_loaded_extensions();
                        $text = "";
                        if(in_array("curl", $extensions)){
                            $data = 'http://www.ijoomla.com/annoucements/adagency_no_jomsocial.txt';
                            $ch = curl_init($data);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 20); 
                            $text = curl_exec($ch); 
                        }
                        else{
                            $text = file_get_contents('http://www.ijoomla.com/annoucements/adagency_no_jomsocial.txt');
                        }
            ?>
                
                        <div>
                            <div class="alert alert-notice">
                                <p><?php echo $text; ?></p>
                            </div>
                        </div>
            <?php
                    }
            ?>
            </div>
		</div>
	</div>
	
	<input type="hidden" name="id" value="<?php echo ($row->id? $row->id:"");?>" />
	<input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="tab" id="tab" value="" />
	<input type="hidden" name="controller" value="adagencyConfigs" />
	<input type="hidden" name="task2" value="<?php if(isset($_GET['task2'])) {echo $_GET['task2'];}?>" />
</form>
