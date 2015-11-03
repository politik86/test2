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

$lists = $this->lists;
$task = $this->task;
$filds = $this->filds_out;
$data_row = $this->data_row;
$params = $this->params;
$start_date = $this->start_date;
$end_date = $this->end_date;
$k = 0;
$helper = new adagencyAdminHelper();
?>
<?php require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php"); ?>
<?php include(JPATH_BASE."/components/com_adagency/includes/js/reports.php"); ?>

<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	  <div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWTREEREPORTS'); ?>
				</h2>
				<a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="http://www.ijoomla.com/redirect/adserver/videos/reports.htm">
					<img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
					<?php echo JText::_("AD_VIDEO"); ?>   
				</a>
            </div>
      </div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('REPADVERTISER'); ?> </label>
			<div class="controls">
				<?php echo $lists['aid']; ?>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('REPCAMPAIGN'); ?> </label>
			<div class="controls">
				<?php  echo $lists['cid']; ?>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('REPMTYPE'); ?> </label>
			<div class="controls">
				<?php  echo $lists['type']; ?>
			</div>
	</div>
	
	<div class="well"> <?php echo JText::_('REPTIME'); ?>: </div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('REPSTARTDATE'); ?> </label>
			<div class="controls">
				<?php 
					switch($params){
						case "0" : {
							$format_string = "Y-m-d H:i:s";
							break;
						}
						case "1" : {
							$format_string = "m/d/Y H:i:s";
							break;
						}
						case "2" : {
							$format_string = "d-m-Y H:i:s";
							break;
						}
						case "3" : {
							$format_string = "Y-m-d";
							break;
						}
						case "4" : {
							$format_string = "m/d/Y";
							break;
						}
						case "5" : {
							$format_string = "d-m-Y";
							break;
						}
					}
					
					$format_string_2 = str_replace ("-", "-%", $format_string);
					$format_string_2 = str_replace ("/", "/%", $format_string_2);
					$format_string_2 = "%".$format_string_2;
					$format_string_2 = str_replace("H:i:s", "%H:%M:%S", $format_string_2);
					$ymd = $format_string_2;

					if(!isset($start_date)||($start_date==NULL)){
						$start_date = date($format_string, time());
					}
					
					echo JHTML::calendar($start_date, 'start_date', 'start_date', $ymd, '');
					?>
                    <input type="hidden" id="start_date2" value="" />
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('REPENDDATE'); ?> </label>
			<div class="controls">
					<?php 	
					echo JHTML::calendar($end_date, 'end_date', 'end_date', $ymd, '');		
					echo "<input type='hidden' name='tfa' id='tfa_adag' value='".$params."' />";
					?>
                    <input type="hidden" id="end_date2" value="" />
			</div>
	</div>
	<div class="control-group">
			<label class="control-label"></label>
			<div class="controls">
				<select id="adag_datepicker" name="adag_datepicker" onchange="adagsetdate(this.value)" class="inputbox">
					<option value="1" <?php if($this->adag_datepicker == "1") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_SELDATE');?></option>
					<option value="2" <?php if($this->adag_datepicker == "2") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_TODAY');?></option>
					<option value="3" <?php if($this->adag_datepicker == "3") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_YEST');?></option>
					<option value="4" <?php if($this->adag_datepicker == "4") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_LASTWK');?></option>
					<option value="5" <?php if($this->adag_datepicker == "5") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_LASTMT');?></option>
					<option value="6" <?php if($this->adag_datepicker == "6") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_LASTYR');?></option>
					<option value="7" <?php if($this->adag_datepicker == "7") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_ALLTM');?></option>
				</select>
			</div>
	</div>
<div style="display:<?php if (@$_GET['act']=='reports' || @$_POST['type']=='Summary') echo 'block'; else echo 'none';?>" id="breackdown">
	<div class="well"> <?php echo JText::_('REPBREAKDOWN'); ?>: </div>
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('REPADVERTISER'); ?> </label>
			<div class="controls">
				<input name="chkAdvertiser" type="checkbox" value="1" <?php if (@$_REQUEST['chkAdvertiser']) { ?>checked<?php } ?> >
				<span class="lbl"></span>
			</div>
	</div>
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('REPCAMPAIGN'); ?> </label>
			<div class="controls">
				<input name="chkCampaign" type="checkbox" value="1" <?php if (@$_REQUEST['chkCampaign']) { ?>checked<?php } ?> >
				<span class="lbl"></span>
			</div>
	</div>
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('REPBANNER'); ?> </label>
			<div class="controls">
				<input name="chkBanner" type="checkbox" value="1" <?php if (@$_REQUEST['chkBanner']) { ?>checked<?php } ?> >
				<span class="lbl"></span>
			</div>
	</div>
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('REPDAY'); ?> </label>
			<div class="controls">
				<input name="chkDay" type="checkbox" value="1" <?php if (@$_REQUEST['chkDay']) { ?>checked<?php } ?> >
				<span class="lbl"></span>
			</div>
	</div>
		
</div>	
	
	<?php if ($task=='creat') { ?>
	<TABLE class="table table-striped table-bordered">
			<thead>
			<?php foreach ($filds as $val) {
				echo '<th  style="text-align:left;">'.$val.'</th>';
			} ?>
			</head>
			<tbody>
			<?php 
					$k=0;
					foreach ($data_row as $row) {
						?>
						<TR class="<?php echo "row$k"; ?>">
						<?php 
						$db =  JFactory::getDBO();
						foreach ($row as $key=>$val) { ?>
									<TD  style="text-align:left;">
										<?php 
										if ('ip_address'==$key) {
											echo long2ip($val);	
										} elseif($key == 'entry_date'){
											echo $helper->formatime($val, $params);
										} elseif($key == 'aid') {
											$sql = 'SELECT u.name FROM #__users AS u WHERE u.id = (SELECT user_id FROM #__ad_agency_advertis WHERE aid = '.intval($val).' LIMIT 1) LIMIT 1';
											$db->setQuery($sql);
											$res = $db->loadResult();
											echo $res; $res = NULL;
										} else {
											echo $val;
										}
								}?>
									</TD>
					<?php $k = 1 - $k; } ?>
						</TR>
				</tbody>
	</TABLE>
	<?php } ?>
		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="adagencyReports" />
	</form>
