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
include_once(JPATH_SITE.DS."administrator".DS."components".DS."com_adagency".DS."helpers".DS."amcharts".DS."advertisers_chart.php");
$document = JFactory::getDocument();
$document->addScript(JURI::root()."administrator/components/com_adagency/js/uikit.min.js");
$document->addScript(JURI::root()."administrator/components/com_adagency/js/jquery.flot.js");
$document->addScript(JURI::root()."administrator/components/com_adagency/js/jquery.flot.time.js");
$document->addScript(JURI::root()."administrator/components/com_adagency/js/jquery.flot.navigate.js");
$document->addStyleSheet(JURI::root()."administrator/components/com_adagency/css/g_graph.css");
$document->addStyleSheet(JURI::root()."administrator/components/com_adagency/css/uikit.almost-flat.css");
$document->addStyleSheet(JURI::root()."administrator/components/com_adagency/css/custom.css");
$document->addScript(JURI::root()."administrator/components/com_adagency/includes/js/validate_range.js");
$document->addScript(JURI::root()."administrator/components/com_adagency/includes/js/sorttable.js");
$number_of_active_campaigns = $this->number_of_active_campaigns;
$number_of_inactive_campaigns = $this->number_of_inactive_campaigns;
$number_of_active_ads = $this->number_of_active_ads;
$number_of_inactive_ads = $this->number_of_inactive_ads;
$currency = $this->currency;
$revenue_earned_last_month = $this->revenue_earned_last_month;
$revenue_earned_this_month = $this->revenue_earned_this_month;
$highest_click_ratio_ad = $this->highest_click_ratio_ad;
$most_successful_campaign = $this->most_successful_campaign;
$least_successful_campaign = $this->least_successful_campaign;
$all_advertisers = $this->all_advertisers;
$all_campaigns = $this->all_campaigns;
$table_adv_content = $this->table_adv_content;
$advertisers = JRequest::getVar("advertisers", "0");
$min = $this->min;
$max = $this->max;
?>
<style type="text/css">
    div#js-cpanel .input-prepend > .btn, div#js-cpanel .input-append > .btn{
        margin-left:-32px !important;
        margin-top: 2px !important;
        z-index: 1;
    }
    div#js-cpanel .btn.btn-link{padding: 2px 0 !important;}
    div#js-cpanel .input-append{margin-right: 5px;}
    div#js-cpanel .input-append input{border-radius: 6px !important;}
    div#js-cpanel .btn-default, div#js-cpanel .btn {padding: 2px 6px !important;}
</style>
<form class="form-horizontal uk-form margin-fix" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
<div class="ad-block-main uk-clearfix">
    <!-- Begin PAGE TITLE AREA -->
    <div class="ad-page-title ad-block-fluid uk-clearfix">
        <!-- Begin PAGE TITLE -->
        <h4 class="uk-margin-remove"><i class="uk-icon uk-icon-users"></i> <?php echo JText::_("VIEWTREEADVERTISERS"); ?></h4>
        <!-- End PAGE TITLE -->
    </div>
    <!-- End PAGE TITLE AREA -->
    <div class="ad-block-fluid-m">
        <!-- Begin GRID -->
        <div class="uk-grid uk-grid-preserve" data-uk-grid-margin>
            <div class="uk-width-1-1 uk-width-large-1-3 uk-width-xlarge-1-6">
                <div class="ad-block-box ad-block-box-rounded ad-bbox-info">
                    <table class="uk-width-1-1">
                        <tr>
                        <!-- CAMPAIGNS * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->
                            <td><small><?php echo JText::_("ADAG_ACTIVE_CAMPAIGNS"); ?></small></td>
                            <td class="uk-text-right">
                        <?php
                            if(intval($number_of_active_campaigns) == 0){
                                echo "<span class='btn btn-mini btn-info disabled'>0</span>";
                            }
                            else{
                        ?>
                                <a class="btn btn-mini btn-info" href="index.php?option=com_adagency&controller=adagencyCampaigns&from=stats&active=Y&advertiser_id=<?php echo intval($advertisers); ?>"><?php echo intval($number_of_active_campaigns); ?></a>
                        <?php
                            }
                        ?>
                            </td>
                        </tr>
                        <tr><td><hr class="uk-margin-top uk-margin-bottom"></td></tr>
                        <tr>
                        <!-- INACTIVE CAMPAIGNS * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->
                            <td><small><?php echo JText::_("ADAG_INACTIVE_CAMPAIGNS"); ?></small></td>
                            <td class="uk-text-right">
                        <?php
                            if(intval($number_of_inactive_campaigns) == 0){
                                echo "<span class='btn btn-mini btn-info disabled'>0</span>";
                            }
                            else{
                        ?>
                                <a class="btn btn-mini btn-info" href="index.php?option=com_adagency&controller=adagencyCampaigns&from=stats&active=N&advertiser_id=<?php echo intval($advertisers); ?>"><?php echo intval($number_of_inactive_campaigns); ?></a>
                        <?php
                            }
                        ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="uk-width-1-1 uk-width-large-1-3 uk-width-xlarge-1-6">
                <div class="ad-block-box ad-block-box-rounded ad-bbox-primary">
                    <table class="uk-width-1-1">
                        <tr>
                        <!-- ADS * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->
                            <td><small><?php echo JText::_("ADAG_ACTIVE_ADS"); ?></small></td>
                            <td class="uk-text-right">
                        <?php
                            if(intval($number_of_active_ads) == 0){
                                echo "<span class='btn btn-mini btn-primary disabled'>0</span>";
                            }
                            else{
                        ?>
                                <a class="btn btn-mini btn-primary" href="index.php?option=com_adagency&controller=adagencyAds&status_select=Y&advertiser_id=<?php echo intval($advertisers); ?>"><?php echo intval($number_of_active_ads); ?></a>
                        <?php
                            }
                        ?>
                            </td>
                        </tr>
                        <tr><td><hr class="uk-margin-top uk-margin-bottom"></td></tr>
                        <tr>
                        <!-- INACTIVE ADS * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->
                            <td><small><?php echo JText::_("ADAG_INACTIVE_ADS"); ?></small></td>
                            <td class="uk-text-right">
                        <?php
                            if(intval($number_of_inactive_ads) == 0){
                                echo "<span class='btn btn-mini btn-primary disabled'>0</span>";
                            }
                            else{
                        ?>
                                <a class="btn btn-mini btn-primary" href="index.php?option=com_adagency&controller=adagencyAds&status_select=N&advertiser_id=<?php echo intval($advertisers); ?>"><?php echo intval($number_of_inactive_ads); ?></a>
                        <?php
                            }
                        ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="uk-width-1-1 uk-width-large-1-3 uk-width-xlarge-1-6">
                <div class="ad-block-box ad-block-box-rounded ad-bbox-warning">
                    <table class="uk-width-1-1">
                        <tr>
                        <!-- REVENUE EARNED LAST MONTH * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->
                            <td><small><?php echo JText::_("ADAG_REVENUE_EARNED_LAST_MONTH"); ?></small></td>
                            <td class="uk-text-right">
                        <?php
                            if(intval($revenue_earned_last_month) == 0){
                                echo"<span class='btn btn-mini btn-warning disabled'>";
                                echo JText::_("ADAG_C_".$currency)."0";
                                echo"</span>";
                            }
                            else{
                        ?>
                                <span class="uk-button uk-button-small"><?php echo JText::_("ADAG_C_".$currency)." ".$revenue_earned_last_month; ?></span>
                        <?php
                            }
                        ?>
                            </td>
                        </tr>
                        <tr><td><hr class="uk-margin-top uk-margin-bottom"></td></tr>
                        <tr>
                        <!-- REVENUE EARNED THIS MONTH * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->
                            <td><small><?php echo JText::_("ADAG_REVENUE_EARNED_THIS_MONTH"); ?></small></td>
                            <td class="uk-text-right">
                        <?php
                            if(intval($revenue_earned_this_month) == 0){
                                echo"<span class='btn btn-mini btn-warning disabled' href='#'>";
                                echo JText::_("ADAG_C_".$currency)."0";
                                echo"</span>";
                            }
                            else{
                        ?>
                            <span class="btn btn-mini btn-warning"><?php echo JText::_("ADAG_C_".$currency)." ".$revenue_earned_this_month; ?></span>
                        <?php
                            }
                        ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="uk-width-1-1 uk-width-large-2-3 uk-width-xlarge-1-6">
                <div class="ad-block-box ad-block-box-rounded ad-bbox-info">
                    <table class="uk-width-1-1">
                        <tr>
                        <!-- MOST SUCCESSFUL CAMPAIGN * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->
                            <td><small><?php echo JText::_("ADAG_MOST_SUCCESSFUL_CAMPAIGN"); ?></small></td>
                            <td class="uk-text-right">
                            <?php
                                if(isset($most_successful_campaign) && count($most_successful_campaign) > 0){
                                    echo '<a class="btn btn-mini btn-link text-right" href="index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid[]='.$most_successful_campaign["0"]["id"].'">'.$most_successful_campaign["0"]["name"].'</a>';
                                }
                            ?></td>
                        </tr>
                        <tr><td><hr class="uk-margin-top uk-margin-bottom"></td></tr>
                        <tr>
                        <!-- LEAST SUCCESSFUL CAMPAIGN * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->
                            <td><small><?php echo JText::_("ADAG_LEAST_SUCCESSFUL_CAMPAIGN"); ?></small></td>
                            <td class="uk-text-right">
                            <?php
                                if(isset($least_successful_campaign) && count($least_successful_campaign) > 0){
                                    echo '<a class="btn btn-mini btn-link text-right" href="index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid[]='.$least_successful_campaign["0"]["id"].'">'.$least_successful_campaign["0"]["name"].'</a>';
                                }
                            ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="uk-width-1-1 uk-width-large-1-3 uk-width-xlarge-1-6">
                <div class="ad-block-box ad-block-box-rounded ad-bbox-success">
                    <table class="uk-width-1-1">
                        <tr>
                        <!-- HIGHEST CLICK RATIO * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->
                            <td><small><?php echo JText::_("ADAG_HIGHEST_CLICK_RATIO_AD"); ?></small></td>
                            <td class="uk-text-right">
                            <?php
                                if(isset($highest_click_ratio_ad) && count($highest_click_ratio_ad) > 0){
                                    echo '<a class="btn btn-mini btn-success" href="index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]='.intval($highest_click_ratio_ad["0"]["id"]).'&act=new">'.$highest_click_ratio_ad["0"]["title"].'</a>';
                                }
                            ?>
                            </td>
                        </tr>
                        <tr><td><hr class="uk-margin-top uk-margin-bottom"></td></tr>
                        <tr>
                        <!-- LOWEST CLICK RATIO * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->
                            <td><small><?php echo JText::_("ADAG_LOWEST_CLICK_RATIO_AD"); ?></small></td>
                            <td class="uk-text-right">
                            <?php
                                if(isset($lowest_click_ratio_ad) && count($lowest_click_ratio_ad) > 0){
                                    echo '<a class="btn btn-mini btn-success" href="index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]='.intval($lowest_click_ratio_ad["0"]["id"]).'&act=new">'.$lowest_click_ratio_ad["0"]["title"].'</a>';
                                }
                                else{
                                    echo "<span class='btn btn-mini btn-success disabled'>N/A<?span>";
                                }
                            ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- End GRID -->
    </div>
</div>
    
<!-- Begin RANGE - Form -->
<div class="ad-block-secondary uk-clearfix">
    <div class="ad-block-fluid">
            <?php
                $date_range = JRequest::getVar("date_range", "this_week");
                $start_date = "";
                $stop_date = "";
                
                if($date_range == "this_week"){
                    $start_date = date("d M Y", strtotime('monday this week'));
                    $stop_date = date("d M Y", strtotime("sunday this week"));
                }
                elseif($date_range == "last_week"){
                    $start_date = date("d M Y", strtotime('monday last week'));
                    $stop_date = date("d M Y", strtotime("sunday last week"));
                }
                elseif($date_range == "last_month"){
                    $start_date = date('d M Y', strtotime('first day of last month'));
                    $stop_date = date('d M Y', strtotime('last day of last month'));
                }
                elseif($date_range == "this_month"){
                    $start_date = date('d M Y', strtotime('first day of this month'));
                    $stop_date = date('d M Y', strtotime('last day of this month'));
                }
            ?>
        <h4><i class="uk-icon uk-icon-calendar"></i> <?php echo JText::_("ADAG_SELECT_DATE_RANGE"); ?></h4>
        <div class="uk-grid uk-grid-preserve" data-uk-grid-margin>
            <div class="uk-width-1-1 uk-width-medium-4-10">
                <?php
                    $start_date = strtotime($start_date);
                    $start_date = date("Y-m-d", $start_date);
                    
                    $start_date_request = JRequest::getVar("start_date", "");
                    $quick_range = JRequest::getVar("quick-range", "");
                    if($start_date_request != "" && $quick_range == ""){
                        $start_date = $start_date_request;
                    }
                    
                    echo JHTML::_('calendar', $start_date, 'start_date', 'start_date', "%Y-%m-%d", array('size'=>'25',  'maxlength'=>'19', 'onchange'=>'javascript:validateFrom(\''.JText::_("ADAG_FROM_LESS_START").'\', \''.JText::_("ADAG_FROM_GREATER_STOP").'\', \''.JText::_("ADAG_INVALID_DATE").'\');'));
                    
                    $stop_date = strtotime($stop_date);
                    $stop_date = date("Y-m-d", $stop_date);
                    
                    $stop_date_request = JRequest::getVar("stop_date", "");
                    $quick_range = JRequest::getVar("quick-range", "");
                    if($stop_date_request != "" && $quick_range == ""){
                        $stop_date = $stop_date_request;
                    }
                    
                    echo JHTML::_('calendar', $stop_date, 'stop_date', 'stop_date', "%Y-%m-%d", array('size'=>'25',  'maxlength'=>'19', 'onchange'=>'javascript:validateTo(\''.JText::_("ADAG_TO_GREATER_STOP").'\', \''.JText::_("ADAG_TO_LESS_START").'\', \''.JText::_("ADAG_TO_LESS_FROM").'\', \''.JText::_("ADAG_INVALID_DATE").'\');'));
                ?>
                <a class="uk-button uk-button-info uk-float-left" href="#" onclick="document.getElementById('task').value='advertisers'; document.adminForm.submit(); return false;">
                    <i class="uk-icon uk-icon-refresh"></i> <span class="uk-hidden-medium"><?php echo JText::_("ADAG_RELOAD"); ?></span>
                </a>
            </div>
            <div class="uk-width-1-1 uk-width-medium-6-10">
                <?php
                    $date_range = JRequest::getVar("date_range", "this_week");
                    $active_range = JRequest::getVar("active_range", "tw");
                    
                    $lm_active = "";
                    $lw_active = "";
                    $tm_active = "";
                    $tw_active = "";
					
					switch($active_range){
						case "tw" : {
							$tw_active = "uk-active";
							break;
						}
						case "tm" : {
							$tm_active = "uk-active";
							break;
						}
						case "lw" : {
							$lw_active = "uk-active";
							break;
						}
						case "lm" : {
							$lm_active = "uk-active";
							break;
						}
					}
                ?>
                <input type="hidden" value="<?php echo $date_range; ?>" name="date_range" />
                <input type="hidden" value="" name="active_range" />
                <ul class="uk-list-inline uk-align-medium-right">
                    
                    <?php
                        echo '<li><a class="uk-button uk-button-dark '.$lm_active.'" href="#" onclick="document.adminForm.date_range.value=\'last_month\';  document.adminForm.active_range.value=\'lm\'; document.getElementById(\'quick-range\').value=\'quick-range\'; document.getElementById(\'task\').value=\'advertisers\'; document.adminForm.submit(); return false;"><i class="uk-icon uk-icon-calendar"></i> '.JText::_("ADAG_LAST_MONTH").'</a></li>';
                    ?>
                    <?php
                        echo '<li><a class="uk-button uk-button-dark '.$lw_active.'" href="#" onclick="document.adminForm.date_range.value=\'last_week\';  document.adminForm.active_range.value=\'lw\'; document.getElementById(\'quick-range\').value=\'quick-range\'; document.getElementById(\'task\').value=\'advertisers\';  document.adminForm.submit(); return false;"><i class="uk-icon uk-icon-calendar"></i> '.JText::_("ADAG_LAST_WEEK").'</a></li>';
                    ?>
                    <?php
                        echo '<li><a class="uk-button uk-button-dark '.$tw_active.'" href="#" onclick="document.adminForm.date_range.value=\'this_week\'; document.adminForm.active_range.value=\'tw\'; document.getElementById(\'quick-range\').value=\'quick-range\'; document.getElementById(\'task\').value=\'advertisers\'; document.adminForm.submit(); return false;"><i class="uk-icon uk-icon-calendar"></i> '.JText::_("ADAG_THIS_WEEK").'</a></li>';
                    ?>
                    <?php
                        echo '<li><a class="uk-button uk-button-dark '.$tm_active.'" href="#" onclick="document.adminForm.date_range.value=\'this_month\'; document.adminForm.active_range.value=\'tm\'; document.getElementById(\'quick-range\').value=\'quick-range\'; document.getElementById(\'task\').value=\'advertisers\'; document.adminForm.submit(); return false;"><i class="uk-icon uk-icon-calendar"></i> '.JText::_("ADAG_THIS_MONTH").'</a></li>';
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End RANGE - Form -->
<!-- Begin FILTER - Form -->
<div class="ad-block-normal ad-block-form-stacked uk-clearfix">
    <div class="ad-block-fluid">
        <div class="uk-grid uk-grid-preserve" data-uk-grid-margin>
			<div class="uk-width-1-1 uk-width-medium-1-3 uk-width-large-1-6">
                <label><i class="uk-icon uk-icon-user uk-text-muted"></i> <?php echo JText::_("VIEWTREEADVERTISERS"); ?>:</label>
				
                <select class="uk-form-small uk-width-1-1" name="advertisers" onchange="document.getElementById('task').value='advertisers'; document.adminForm.submit();">
					<?php
						if($all_advertisers && count($all_advertisers) > 0){
							foreach($all_advertisers as $key=>$value){
								$selected = "";
								
								if(intval($advertisers) == 0){
									$advertisers = $value["aid"];
								}
								
								if($value["aid"] == $advertisers){
									$selected = 'selected="selected"';
								}
								echo '<option value="'.$value["aid"].'" '.$selected.'>'.$value["name"]."</option>";
							}
						}
					?>
                </select>
            </div>
            
            <div class="uk-width-1-1 uk-width-medium-1-3 uk-width-large-1-6">
                <label><i class="uk-icon uk-icon-bullhorn uk-text-muted"></i> <?php echo JText::_("VIEWTREECAMPAIGNS") ?>:</label>
                <select class="uk-form-small uk-width-1-1" name="campaigns" onchange="document.getElementById('task').value='advertisers'; document.adminForm.submit();">
                    <option value="0"> <?php echo JText::_("ADAG_ALL_CAMPAIGNS"); ?> </option>
                <?php
                    $campaigns = JRequest::getVar("campaigns", "0");
                    if($all_campaigns && count($all_campaigns) > 0){
                        foreach($all_campaigns as $key=>$value){
                            $selected = "";
                            if($value["id"] == $campaigns){
                                $selected = 'selected="selected"';
                            }
                            echo '<option value="'.$value["id"].'" '.$selected.'>'.$value["name"]."</option>";
                        }
                    }
                ?>
                </select>
            </div>
            <?php
                $ad_type = JRequest::getVar("ad_type", "0");
            ?>
            <div class="uk-width-1-1 uk-width-medium-1-3 uk-width-large-1-6">
                <label><i class="uk-icon uk-icon-tag uk-text-muted"></i> <?php echo JText::_("ADAG_AD_TYPE_LABEL"); ?>:</label>
                <select class="uk-form-small uk-width-1-1" name="ad_type" onchange="document.getElementById('task').value='advertisers'; document.adminForm.submit();">
                    <option value="0"> <?php echo JText::_("ADAG_AD_TYPE"); ?> </option>
                    <option value="Standard" <?php if($ad_type == "Standard"){echo 'selected="selected"';} ?> >Standard</option>
                    <option value="Advanced" <?php if($ad_type == "Advanced"){echo 'selected="selected"';} ?> >Affiliate Ad</option>
                    <option value="Popup" <?php if($ad_type == "Popup"){echo 'selected="selected"';} ?> >Pop Up</option>
                    <option value="Flash" <?php if($ad_type == "Flash"){echo 'selected="selected"';} ?> >Flash</option>
                    <option value="TextLink" <?php if($ad_type == "TextLink"){echo 'selected="selected"';} ?> >Text Ad</option>
                    <option value="Transition" <?php if($ad_type == "Transition"){echo 'selected="selected"';} ?> >Transition</option>
                    <option value="Floating" <?php if($ad_type == "Floating"){echo 'selected="selected"';} ?> >Floating</option>
                    <option value="Jomsocial" <?php if($ad_type == "Jomsocial"){echo 'selected="selected"';} ?> >JomSocial Stream Ad</option>
                </select>
            </div>
            <?php
                $chart_type = JRequest::getVar("chart_type", "impressions");
            ?>
            <div class="uk-width-1-1 uk-width-medium-1-3 uk-width-large-1-6">
                <label><i class="uk-icon uk-icon-signal uk-text-muted"></i> <?php echo JText::_("ADAG_STATS"); ?>:</label>
            <select class="uk-form-small uk-width-1-1" name="chart_type" onchange="document.getElementById('task').value='advertisers'; document.adminForm.submit();">
                <option value="impressions" <?php if($chart_type == "impressions"){echo 'selected="selected"';} ?> > <?php echo JText::_("ADAG_IMPRESSIONS_SELECT"); ?> </option>
                <option value="clicks" <?php if($chart_type == "clicks"){echo 'selected="selected"';} ?> > <?php echo JText::_("ADAG_CLICKS_SELECT"); ?> </option>
                <option value="ctr" <?php if($chart_type == "ctr"){echo 'selected="selected"';} ?> > <?php echo JText::_("ADAG_CTR_SELECT"); ?> </option>
            </select>
            </div>
        </div>
    </div>
</div>
<!-- End FILTER - Form -->
<!-- Begin GRAPHS -->
<div class="ad-block-normal ad-block-main">
    <div class="ad-block-fluid-s">
        <div id="g_daily_chart" class="uk-width-1-1">
            <div id="content">
                <div class="demo-container">
                    <div id="placeholder" class="demo-placeholder">
                        <div style="position: relative;top: 50%;"><?php echo JText::_("ADAG_NO_DIAGRAM"); ?></div>
                    </div>
                </div>
            </div>    
        </div>
    </div>
    <div class="diagram-legend" id="diagram-legend">
    </div>
</div>
<!-- End GRAPHS -->
<!-- Begin TABLE -->
<div class="uk-clearfix">
    <div class="ad-block-fluid">
        <div class="uk-grid uk-grid-preserve" data-uk-grid-margin>
            <div class="uk-width-1-1 uk-width-medium-1-2"><h5 class="uk-text-muted">
            <?php
                echo JText::_("ADAG_DATE_RANGE_PER_SELECTED_PERIOD");
            ?>
            </h5></div>
            <div class="uk-width-1-1 uk-width-medium-1-2">
                <div class="uk-button-dropdown uk-align-medium-right btn-group" data-uk-dropdown>
                    <button class="uk-button uk-button-success dropdown-toggle" data-toggle="dropdown">
                        <?php echo JText::_("ADAG_EXPORT_TO"); ?> &nbsp; <span class="uk-icon uk-icon-caret-down"></span>
                    </button>
                    <ul class="dropdown-menu" style="min-width:113px !important;">
                        <li>
                            <a href="#" onclick="document.adminForm.task.value='advertisers_csv'; document.adminForm.submit();">CSV</a>
                        </li>
                        <li class="uk-nav-divider"></li>
                        <li>
                            <a href="#" onclick="document.adminForm.task.value='advertisers_pdf'; document.adminForm.submit();">PDF</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <hr />
        <table class="uk-table table-counter sortable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                    	<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
						<span class="lbl"></lbl>
                    </th>
                    <th><?php echo JText::_("ADAG_HEAD_DATE"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                    <th><?php echo JText::_("ADAG_HEAD_CAMPAIGN"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                    <th><?php echo JText::_("ADAG_HEAD_AD_TYPE"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                    <th><?php echo JText::_("ADAG_HEAD_AD_NAME"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                    <th><?php echo JText::_("ADAG_HEAD_IMPRESSIONS"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                    <th><?php echo JText::_("ADAG_HEAD_CLICKS"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                    <th><?php echo JText::_("ADAG_HEAD_CLICK_RATIO"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                </tr>
            </thead>
            <tbody>
        <?php
            if(isset($table_adv_content) && count($table_adv_content) > 0){
                $i = 0;
				foreach($table_adv_content as $key=>$value){
                    $tmp_key = explode("-", $key);
                    $camp_id = $tmp_key["0"];
                    $ad_id = $tmp_key["1"];
                    $impression = $value["impressions"];
                    $click = @$value["click"];
                    $type = $value["type"];
                    $ad_name = $value["ad_name"];
                    $ad_id = $value["ad_id"];
                    $date = $value["date"];
        ?>
                <tr>
                    <td></td>
                    <td>
                    	<?php echo JHTML::_('grid.id', $i, $camp_id."-".$ad_id); ?>
						<span class="lbl"></lbl>
                    </td>
                    <td>
                        <?php echo $date; ?></a>
                    </td>
                    <td>
                        <a class="uk-button uk-button-small" href="index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid[]=<?php echo intval($camp_id); ?>"><?php echo $all_campaigns[$camp_id]["name"]; ?></a>
                    </td>
                    <td>
                        <?php echo $type; ?>
                    </td>
                    <td>
                            <?php
                                $link = "";
                                $act = '&act=new';
                                
                                switch($value["type"]){
                                    case 'Advanced':
                                        $link = "index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid[]=".intval($ad_id).$act;
                                        break;
                                    case 'Standard':
                                        $link = "index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]=".intval($ad_id).$act;
                                        break;
                                    case 'Flash':
                                        $link = "index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid[]=".intval($ad_id).$act;
                                        break;
                                    case 'Transition':
                                        $link = "index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid[]=".intval($ad_id).$act;
                                        break;
                                    case 'Floating':
                                        $link = "index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid[]=".intval($ad_id).$act;
                                        break;
                                    case 'Popup':
                                        $link = "index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid[]=".intval($ad_id).$act;
                                        break;
                                    case 'TextLink':
                                        $link = "index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=".intval($ad_id).$act;
                                        break;
                                }
                            ?>
                            <a href="<?php echo $link; ?>">
                                <?php echo $ad_name; ?>
                            </a>
                    </td>
                    <td>
                            <?php
                                if(trim($impression) != ""){
                                    echo $impression;
                                }
                                else{
                                    echo "0";
                                }
                            ?>
                    </td>
                    <td>
                            <?php
                                if(trim($click) != ""){
                                    echo $click;
                                }
                                else{
                                    echo "0";
                                }
                            ?>
                    </td>
                    <td>
                            <?php
                                $nr = 0;
                                if(intval($impression) != 0){
                                    $nr = $click / $impression * 100;
                                }
                                echo number_format($nr, 2, '.', '')."%";
                            ?>
                    </td>
                </tr>
        <?php
					$i++;
                }
            }
        ?>
            </tbody>
        </table>
        <div style="float:left;">
        	<?php echo $this->pagination->getLimitBox(); ?>
        </div>
        
        <div style="float:left; margin-left:15px;">
        	<?php echo $this->pagination->getListFooter(); ?>
        </div>
    </div>
</div>
<!-- End TABLE -->
    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="controller" value="adagencyReports" />
    <input type="hidden" name="task" id="task" value="advertisers" />
    <input type="hidden" name="quick-range" id="quick-range" value="" />
    <input type="hidden" name="min" id="min" value="<?php echo trim($min); ?>" />
    <input type="hidden" name="max" id="max" value="<?php echo trim($max); ?>" />
</form>