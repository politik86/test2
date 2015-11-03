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
JHTML::_('behavior.calendar');

$camp_row = $this->camp;
$stats = $this->stats;
$package_row = $this->package_row;
$advertiser = $this->advt;
$camps_ads = $this->camps_ads;
$helper = new adagencyAdminHelper();
if(isset($advertiser->approved)&&($advertiser->approved != 'Y')){
    $advertiser_appr = "<div style='clear:both'></div><div class='well'  id='ajax_adv' >".JText::_('ADAG_NOT_APPROVED_CAMP')."<br /<br />- <span id='approve_and_email' style='text-decoration:underline; cursor:pointer;'>".JText::_('ADAG_NOT_APPR_1')."</span><br />- <span id='approve_no_email' style='text-decoration:underline; cursor:pointer;'>".JText::_('ADAG_NOT_APPR_2')."</span><br /><br /><span class='close_it' style='font-weight:bold; text-decoration: underline; cursor: pointer;' >".JText::_('ADAG_CLOSE')."</span><input type='hidden' id='advertiser_aid' value='".$advertiser->aid."' /></div>";
} else {
    $advertiser_appr = NULL;
}
$ban_row = $this->ban_row;
$cbrw = NULL;
if(isset($ban_row)&&(is_array($ban_row))){
    foreach($ban_row as $el){
        $cbrw[] = $el->id;
    }
    $cbrw = @implode(",",$cbrw);
}
$configs = $this->configs;

if ( isset($camp_row->params['adslim']) ) {
    $adslim = (int)$camp_row->params['adslim'];
} elseif ( (!isset($camp_row->id) || ($camp_row->id <= 0)) && isset($configs->params['adslim']) ) {
    $adslim = $configs->params['adslim'];
} else {
    $adslim = 999;
}

$lists = $this->lists;
$task = $this->task;
// JHTML::_('behavior.modal');
// JHTML::_('behavior.mootools');
$document = JFactory::getDocument();
$document->addScript(JURI::root()."components/com_adagency/includes/js/graybox.js");
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");

require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
$helper = new adagencyAdminHelper();
$ymd = "";
$hms = "";
$ymd.$hms = "";

switch($configs->params['timeformat']){
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

$ymd.$hms = $format_string_2;

?>
<?php include(JPATH_BASE."/components/com_adagency/includes/js/campaigns.php"); ?>

<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	
  <div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php if ($camp_row->id<1) echo JText::_('AD_CMP_NEWCMP'); else echo JText::_('AD_CMP_EDITCMP');?>
				</h2>
            </div>
      </div>
      
<div class="well"><?php echo JText::_('AD_CMP_MAININFO'); ?></div>

<div class="control-group">
	<label class="control-label"> <?php echo JText::_('NEWADADVERTISER'); ?> </label>
	<div class="controls">
		 <div id="to_be_replaced" style="float:left "><?php echo $lists['advertiser_id']; ?></div>
		<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADADVERTISER_TIP'); ?>" >
		<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
		 <?php if($camp_row->id <= 0){ ?>
                        <div style="float:left; padding-left:10px; ">
                            <a rel="{handler: 'iframe', size: {x: 700, y: 450}}" href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=addadv&tmpl=component" class="modal2"><?php echo JText::_('AD_CMP_ADD_ADVERTISER'); ?></a>
                        </div>
                <?php } ?>
        <?php echo $advertiser_appr; ?>
	</div>
</div>

<div class="control-group">
	<label class="control-label"> <?php echo JText::_('AD_CMP_CMPNAME'); ?> </label>
	<div class="controls">
		<input class="inputbox" type="text" name="name" size="40" maxlength="255" value="<?php echo $camp_row->name; ?>" />
		<span class="editlinktip hasTip" title="<?php echo JText::_('AD_CMP_CMPNAME_TIP'); ?>" >
		<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
	</div>
</div>
<?php if (!$camp_row->id) { ?>
	<div class="control-group">
		<label class="control-label"> <?php echo JText::_('AD_CMP_CMPPACK'); ?> </label>
		<div class="controls">
			<div  id="to_be_replaced_p" style="float:left "><?php echo $lists["package"]; ?></div>
			<span class="editlinktip hasTip" title="<?php echo JText::_('AD_CMP_CMPPACK_TIP'); ?>" >
			<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			<div style="float:left; padding-left:10px; ">
				<a rel="{handler: 'iframe', size: {x: 700, y: 450}}" href="index.php?option=com_adagency&controller=adagencyPackages&task=addpack&tmpl=component&advert_id=<?php echo intval($this->advertiser_id);?>" class="modal2"><?php echo JText::_('AD_CMP_ADD_PACKAGE'); ?></a>&nbsp;
            </div>
		</div>
	</div>
	
	<div class="alert alert-notice">
		<?php echo JText::_('AD_NOTICE_CAMP');?>
	</div>
	
	<div class="control-group">
		<label class="control-label"> <?php echo JText::_('AD_CAMP_START_DATE'); ?> </label>
		<div class="controls">
			<?php
				echo JHTML::_('calendar', $helper->formatime($camp_row->start_date, $configs->params['timeformat']), 'start_date', 'start_date', ''.$ymd.$hms, array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
				echo "<input type='hidden' name='tfa' value='".$configs->params['timeformat']."' />";
			?>
			<span class="editlinktip hasTip" title="<?php echo JText::_('AD_CAMP_START_DATE_TIP'); ?>" >
			<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			
		</div>
	</div>
	
<?php }	?>	
<div class="control-group">
	<label class="control-label"></label>
	<div class="controls">
		<input type="hidden" name="default" <?php if ($camp_row->default=='Y') echo 'checked'; ?> value ="Y">
	</div>
</div>

<div class="control-group">
	<label class="control-label"> <?php echo JText::_('AD_CAMP_APPROVED'); ?> </label>
	<div class="controls">
		 <?php echo $lists['approved'] ?>
		<span class="editlinktip hasTip" title="<?php echo JText::_('AD_CAMP_APPROVED_TIP'); ?>" >
		<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
	</div>
</div>	

<div class="control-group">
	<label class="control-label"> <?php echo JText::_('AD_CMP_INCLUDED_ADS'); ?> </label>
	<span class="editlinktip hasTip" title="<?php echo JText::_('AD_CMP_INCLUDED_ADS_TIP'); ?>" >
	<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
	<div class="controls">
		  <?php
                if ( isset($adslim) && ($adslim != 999) ) {
                    echo "<p><span class='adslim'>" . JText::_('ADAG_CMP_ADS_LIM') . ": " . $adslim . "</span></p>";
                }
            ?>
			<TABLE class="table table-striped table-bordered" id="banner_table">			
			
			<thead >
				<TH style="text-align:center;"><?php echo JText::_("AD_BANNER_ID"); ?></TH>
				<TH><?php echo JText::_("AD_BANNER_NAME"); ?></TH>
                <TH><?php echo JText::_("NEWADZONE"); ?> </TH>
				<TH><?php echo JText::_("VIEWADPREVIEW");?></TH>
				<TH style="text-align:center;"><?php echo JText::_("VIEWADSTATUS"); ?></TH>
				<TH><?php echo JText::_("ADAG_INCLUDE");?></TH>
				<TH style="text-align:left;"><?php echo JText::_("AD_BANNER_RW"); ?></TH>
			</thead>
			<tbody>
			<?php
				$k=0;
				for ($i=0, $n=count( $ban_row ); $i < $n; $i++) {
					$row = $ban_row[$i];
			?>
					<TR class="<?php echo "row$k"; ?>">
						<TD style="text-align:center;"><?php echo $row->id; ?></TD>
						<TD><?php echo $row->title; ?> <!--(<?php if ($row->width==0 or $row->height==0) { echo  "Size"." Unknown"; } else {echo "$row->width x $row->height"; }?>)--></TD>
                        <TD>
							<?php
								if(isset($row->zones)){
									echo $row->zones;
								}
							?>
						</TD>
						<TD><a href="<?php echo str_replace("administrator/","",JURI::base())."index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&no_html=1&adid=".$row->id;?>" class="modal2"><?php echo strtolower(JText::_("VIEWADPREVIEW"));?></a></TD>
                        <TD style="text-align:center;">
                            <?php if ($row->approved == 'Y') { ?>
                               <i class="icon-ok"></i>
                            <?php } elseif ($row->approved == 'N') { ?>
                                 <i class="icon-remove"></i>
                            <?php } elseif ($row->approved == 'P') { ?>
                                  <i class="icon-clock"></i>
                            <?php } ?>
                        </TD>

						<TD class="add_column">
							<input type="checkbox" name="banner[<?php echo $row->id; ?>][add]" value="1" <?php if (in_array($row->id, $camps_ads)){echo 'checked="checked"';} ?> />
                        	<span class="lbl"></span>
                        </TD>

						<?php if ($task=="edit" || $task=='editA') { /* ?>
						<TD><?php if ($row->relative_weighting>0) {?><INPUT TYPE="checkbox" NAME="banner[<?php echo $row->id;?>][del]" VALUE="1"><?php } ?></TD>
						<?php */ }  ?>

						<TD style="text-align:left;"><INPUT TYPE="text" NAME="banner[<?php echo $row->id;?>][rw]"  size="5" maxlength="6" value="<?php echo $row->relative_weighting>0?$row->relative_weighting: '100';?>"></TD>
					</TR>
					<?php
					$k = 1 - $k;
				//}
			}?>
			</TBODY>
			</TABLE>

			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
			<input class="inputbox" type="hidden" name="notes" size="40" maxlength="255" value="<?php //echo $camp_row->notes; ?>" />
			</td>
		</tr>
	</table>
	</div>
	<div class="control-group">
		<?php if ($camp_row->id > 0) { ?>
	<table class="table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<th align="left" colspan="2">
				<?php echo JText::_('ADAG_CPM_STUS');?>
			</th>
		</thead>
		<tbody>
		<tr>
			<td><?php echo JText::_('ADAG_CPM_PACKNAME');?>: </td>
			<td><a href="index.php?option=com_adagency&controller=adagencyPackages&task=edit&cid[]=<?php echo $package_row->tid; ?>"><?php echo $package_row->description; ?></a></td>
		</tr>

		<tr>
			<td><?php echo JText::_('VIEWDSADMINZONES');?>: </td>
			<td>
	            <?php
					if(is_array($package_row->allzones)) {
						$nr2 = 0;
						foreach($package_row->allzones as $zone){
							if(trim($zone->zoneid) == ""){ continue; }
							if($nr2 > 0) { echo ", "; }
							echo '<a href="index.php?option=com_adagency&controller=adagencyZones&task=edit&cid[]='.intval($zone->zoneid).'">'.$zone->z_title.'</a>';
							$nr2++;
						}
					}
				?>
            </td>
		</tr>

		<tr>
			<td><?php echo JText::_('ADAG_CPM_PACKTYPE');?>: </td>
			<td><?php if ($camp_row->type=="cpm") { echo JText::_('ADAG_CPM'); } elseif ($camp_row->type=="pc") { echo JText::_('ADAG_PC'); } elseif ($camp_row->type=="fr") { echo JText::_('ADAG_FR'); } elseif ($camp_row->type=="in") { echo JText::_('ADAG_IN'); } ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('ADAG_CPM_PACKDETLS');?>: </td>
			<td><?php echo $package_row->details; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('ADAG_CPM_STARTDATE'); ?>:</td>
			<td><?php echo $helper->formatime($camp_row->start_date, $configs->params['timeformat']); ?></td>
		</tr>
        
        <tr>
			<td><?php echo JText::_('REPENDDATE'); ?>:</td>
			<td>
				<?php
					$end_date = $camp_row->validity;
					$calendar = JHtml::calendar($helper->formatime($end_date, $configs->params['timeformat']), 'validity', 'validity', ''.$ymd.$hms, '');
					if($end_date == "0000-00-00 00:00:00"){
						$end_date = "Never";
						$calendar = str_replace('value=""', 'value="'.trim($end_date).'"', $calendar);
					}
					echo $calendar;
				?>
            </td>
		</tr>


		<?php if ($camp_row->type == "cpm") {
			if ($camp_row->quantity > 0) {
			$package_row->quantity = intval($package_row->quantity) - intval($camp_row->quantity);
			?>
				<tr>
					<td colspan="2"><span style="font-size: 24px; font-weight: bold; color: #000000;"><span style="color: #FF0000;"><?php echo $package_row->quantity; ?></span>&nbsp;<?php echo JText::_('AD_CAMP_IMP');?>,&nbsp;<span style="color: #FF0000;"><?php echo $camp_row->quantity; ?></span>&nbsp;<?php echo JText::_('AD_CAMP_IMP_LEFT');?></span></td>
				</tr>
		<?php }
			else { ?>
			<tr>
				<td colspan="2">
				<span style="font-size: 24px; font-weight: bold; color: #FF0000;"><?php echo JText::_('AD_CAMP_EXPIRED');?></span>
				</td>
			</tr>
			<?php }
		}

		if ($camp_row->type == "pc") {
			if ($camp_row->quantity > 0) {

				$package_row->quantity = intval($package_row->quantity) - intval($camp_row->quantity);
				?>
					<tr>
						<td colspan="2"><span style="font-size: 24px; font-weight: bold; color: #000000;"><span style="color: #FF0000;"><?php echo $package_row->quantity; ?></span>&nbsp;<?php echo JText::_('AD_CAMP_CLK');?>,&nbsp;<span style="color: #FF0000;"><?php echo $camp_row->quantity; ?></span>&nbsp;<?php echo JText::_('AD_CAMP_CLK_LEFT');?></span></td>
					</tr>
		<?php } else { ?>
				<tr>
					<td colspan="2">
					<span style="font-size: 24px; font-weight: bold; color: #FF0000;"><?php echo JText::_('AD_CAMP_EXPIRED');?></span>
					</td>
				</tr>

			<?php }
		}
		
		if ($camp_row->type == "fr" || $camp_row->type == "in") {
		if ($camp_row->expired) {
		?>
			<tr>
				<td colspan="2">
				<span style="font-size: 24px; font-weight: bold; color: #FF0000;"><?php echo JText::_('AD_CAMP_EXPIRED');?></span>
				</td>
			</tr>
			<?php } else { ?>
			<tr>
				<td colspan="2">
				<span style="font-size: 24px; font-weight: bold; color: #000000;"><span style="color: #FF0000;"><?php echo $camp_row->time_left['days']; ?></span>&nbsp;<?php echo JText::_('AD_CAMP_DAYS');?>&nbsp;<span style="color: #FF0000;"><?php echo $camp_row->time_left['hours']; ?></span>&nbsp;<?php echo JText::_('AD_CAMP_HOURS');?>&nbsp;<span style="color: #FF0000;"><?php echo $camp_row->time_left['mins']; ?></span>&nbsp;<?php echo JText::_('AD_CAMP_MINS');?>&nbsp;<?php echo JText::_('AD_CAMP_NOIMP_LEFT');?></span>
				</td>
			</tr>
		<?php }
			} ?>
			</tbody>
			</table>
			<table class="table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<th align="left" colspan="2">
					<?php echo JText::_('AD_CAMP_STATS'); ?>
				</th>
			</thead>
			<tbody>
			<tr>
				<td><?php echo JText::_('AD_CAMP_DURATION');?>: </td>
				<td>
					<?php
                    	if($stats['days'] < 0 || $stats['hours'] < 0 || $stats['mins'] < 0){
							echo JText::_("ADAG_WILL_START")." ".$helper->formatime($camp_row->start_date, $configs->params['timeformat']);
						}
						else{
							echo $stats['days'] . "&nbsp;".JText::_("ADAG_DAYS")."&nbsp;" . $stats['hours'] . "&nbsp;".JText::_('AD_CAMP_HOURS')."&nbsp;" . $stats['mins'] . "&nbsp;".JText::_('AD_CAMP_MINS');
						}
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_('AD_CAMP_CLICKS');?>: </td>
				<td><?php if (@$stats['click']) { echo @$stats['click']; } else echo '0';?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('AD_CAMP_IMPS');?>: </td>
				<td><?php if (@$stats['impressions']) { echo @$stats['impressions']; } else echo '0'; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('AD_CLICK_RATE');?>: </td>
				<td><?php if (@$stats['click_rate']) { echo @$stats['click_rate']; } else echo '0.00';  ?>%</td>
			</tr>

		<?php } ?>
			</tbody>
		</table>
	</div>
</div>	
<div class="well"><span style="font-size:25px; font-weight:bold;"><?php echo JText::_("ADAG_HISTORY"); ?></span></div>
    <table class="table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
           <?php
            $activities = $camp_row->activities;
			if(trim($activities) != ""){
			?>
                <thead>
                    <th width="20%">
                        <?php echo JText::_("ADAG_DATE"); ?>
                    </th>
                    
                    <th width="30%">
                        <?php echo JText::_("VIEWADACTION"); ?>
                    </th>
                    
                    <th>
                        <?php echo JText::_("ADAG_BY"); ?>
                    </th>
                </thead>
                <tbody>
                <?php
                    $activities = $camp_row->activities;
                    $users = array();
                    $db = JFactory::getDBO();
                    if(trim($activities) != ""){
                        $activities_array = explode(";", $activities);
                        if(is_array($activities_array) && count($activities_array) > 0){
                            foreach($activities_array as $key=>$activity){
                                $activity_array = explode(" - ", $activity);
                                if(is_array($activity_array) && count($activity_array) > 0 && trim($activity_array["0"]) != ""){
                                    $row  = '<tr>';
                                    $row .= 	'<td style="font-size: 14px;">';
                                    $row .= 		$helper->formatime($activity_array["1"], $configs->params['timeformat']); 
									                //date("m/d/Y", strtotime(trim($activity_array["1"])));
                                    $row .= 	'</td>';
                                    $row .= 	'<td style="font-size: 14px;">';
                                    $row .= 		trim($activity_array["0"]);
                                    $row .= 	'</td>';
                                    if(isset($activity_array["2"])){
                                        $usertype = "";
                                        $name = "";
                                        
										$sql = "select u.id, u.name, ug.title, a.aid,  GROUP_CONCAT(DISTINCT CAST(ugm.group_id as CHAR)) groups  
    from #__user_usergroup_map ugm, #__usergroups ug, #__users u left outer join #__ad_agency_advertis a on a.user_id = u.id
    where u.id=ugm.user_id and ugm.group_id=ug.id and u.id=".intval($activity_array["2"])." group by u.id and u.id=a.user_id";
										$db->setQuery($sql);
										$db->query();
										$result = $db->loadAssocList();
										
										$user_history_name = "Advertiser";
										$groups = $result["0"]["groups"];
										$groups_array = explode(",", $groups);
										
										if(count($groups_array) > 1){
											$user_history_name = "Admin";
										}
										
                                        if(isset($users[trim($activity_array["2"])])){
                                            $usertype = $user_history_name; //trim($users[$activity_array["2"]]["aid"]) != "" ? "Advertiser" : $users[$activity_array["2"]]["title"];
                                            $name = $users[$activity_array["2"]]["name"];	
                                        }
                                        else{
                                            
											if(isset($result) && count($result) > 0){
												$users[$activity_array["2"]] = $result["0"];
                                                $usertype = $user_history_name; //trim($users[$activity_array["2"]]["aid"]) != "" ? "Advertiser" : $users[$activity_array["2"]]["title"];
                                                $name = $users[$activity_array["2"]]["name"];
                                            }
                                        }
                                        
                                        $row .= 	'<td style="font-size: 14px;">';
                                        $row .= 		trim($usertype)." (".trim($name).")";
                                        $row .= 	'</td>';
                                    }
                                    $row .= '</tr>';
                                    echo $row;
                                }
                            }
                        }
                    }
                ?>
             </tbody>
             <?php
             }
			 else{
			 	echo '<tr><td>'.JText::_("ADAGENCY_NO_HISTORY").'</td></tr>';
			 }
			 ?>
        </table>

        
		<input type="hidden" name="id" value="<?php echo $camp_row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="controller" value="adagencyCampaigns" />
		<?php if ($camp_row->id>0) { ?>
        <input type="hidden" name="cbrw" value="<?php echo $cbrw; ?>" />
		<input type="hidden" name="otid" value="<?php echo $camp_row->otid; ?>" />
		<input type="hidden" name="aid" value="<?php echo $camp_row->aid; ?>" />
		<input type="hidden" name="type" value="<?php echo $camp_row->type; ?>" />
		<input type="hidden" name="quantity" value="<?php echo $camp_row->quantity; ?>" />
		<!-- <input type="hidden" name="validity" value="<?php //echo $camp_row->validity; ?>" /> -->
		<input type="hidden" id="start_date" name="start_date" value="<?php echo date($format_string, strtotime($camp_row->start_date)); ?>" />
		<input type="hidden" name="cost" value="<?php echo $camp_row->cost; ?>" />
		<input type="hidden" id="sendmail" name="sendmail" value="1" />
		<input type="hidden" id="initvalcamp" value="" />
        <input type="hidden" name="time_format" id="time_format" value="<?php echo $configs->params['timeformat']; ?>" />
		<?php } else { ?>
		<input type="hidden" id="initvalcamp" value="" />
		<input type="hidden" name="now_datetime" value="<?php echo date("Y-m-d"); ?>" />
        <input type="hidden" name="time_format" id="time_format" value="<?php echo $configs->params['timeformat']; ?>" />
		<?php } ?>
		</form>
