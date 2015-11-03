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
include_once(JPATH_SITE.DS."components".DS."com_adagency".DS."helpers".DS."campaigns_chart.php");

$document = JFactory::getDocument();
$document->addScript(JURI::root()."components/com_adagency/includes/js/validate_range.js");
$document->addScript(JURI::root()."components/com_adagency/includes/js/sorttable.js");
$number_of_active_ads = $this->number_of_active_ads;
$number_of_inactive_ads = $this->number_of_inactive_ads;
$highest_click_ratio_ad = $this->highest_click_ratio_ad;
$lowest_click_ratio_ad = $this->lowest_click_ratio_ad;
$all_advertisers = $this->all_advertisers;
$all_campaigns = $this->all_campaigns;
$all_campaigns_by_adv = $this->all_campaigns_by_adv;
$all_ads = $this->all_ads;
$table_cmp_content = $this->table_cmp_content;
$campaigns_stats = JRequest::getVar("campaigns", "0");
$advertisers = JRequest::getVar("advertisers", "0");
$min = $this->min;
$max = $this->max;
$campaigns = JRequest::getVar("campaigns", "0");
$return_url = "";

if($all_campaigns_by_adv && count($all_campaigns_by_adv) > 0){
	$first_campaign = 0;
	$i = 0;
	$selected_camp = false;
	
	foreach($all_campaigns_by_adv as $key=>$value){
		if($i == 0){
			$first_campaign = $value["id"];
			$i++;
		}
		
		if($value["id"] == $campaigns){
			$selected_camp = true;
		}
	}
	
	if($selected_camp === FALSE){
		$campaigns_stats = $first_campaign;
	}
}
else{
	$campaigns_stats = "0";
}
?>

<!-- Reports Container -->
<div class="ada-reports">
  <nav class="uk-navbar ada-toolbar">
  <!-- Toolbar -->
  <ul class="uk-navbar-nav">
    <li><a href="<?php echo @$cpn_link;?>"><i class="uk-icon-home"></i></a></li>
    <li><a href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=<?php echo intval(@$my->id);?>"><?php echo JText::_('ADG_PROF'); ?></a></li>
    <li><a href="index.php?option=com_adagency&controller=adagencyAds<?php echo @$Itemid_ads; ?>"><?php echo JText::_('ADG_ADS'); ?></a></li>
    <li><a href="index.php?option=com_adagency&controller=adagencyOrders<?php echo @$Itemid_ord; ?>"><?php echo JText::_('ADG_ORDERS'); ?></a></li>
    <li class="uk-active"><a href="index.php?option=com_adagency&controller=adagencyReports<?php echo @$Itemid_rep; ?>"><?php echo JText::_('ADG_REPORTS'); ?></a></li>
    <li><a href="index.php?option=com_adagency&controller=adagencyCampaigns<?php echo @$Itemid_cmp; ?>"><?php echo JText::_('ADG_CAMP'); ?></a></li>
    <li><a href="index.php?option=com_adagency&controller=adagencyPackage<?php echo @$Itemid_pck; ?>"><?php echo JText::_('ADG_PACKAGES'); ?></a></li>
  </ul>
  <div class="uk-navbar-flip">
    <ul class="uk-navbar-nav">
      <li><a href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><i class="uk-icon-sign-out"></i></a></li>
    </ul>
  </div>
  </nav>

  <select name="ada-toolbar-mobile" class="ada-toolbar-mobile" id="ada-toolbar-mobile" onchange="window.open(this.value, '_self');" >
    <option value="<?php echo @$cpn_link;?>"><i class="fa fa-home"></i><?php echo JText::_('ADG_DASH'); ?></a></li>
    <option value="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=<?php echo intval(@$my->id);?>"><?php echo JText::_('ADG_PROF'); ?></option>
    <option value="index.php?option=com_adagency&controller=adagencyAds<?php echo @$Itemid_ads; ?>"><?php echo JText::_('ADG_ADS'); ?></option>
    <option value="index.php?option=com_adagency&controller=adagencyOrders<?php echo @$Itemid_ord; ?>"><?php echo JText::_('ADG_ORDERS'); ?></option>
    <option selected="selected" value="index.php?option=com_adagency&controller=adagencyReports<?php echo @$Itemid_rep; ?>"><?php echo JText::_('ADG_REPORTS'); ?></option>
    <option value="index.php?option=com_adagency&controller=adagencyCampaigns<?php echo @$Itemid_cmp; ?>"><?php echo JText::_('ADG_CAMP'); ?></option>
    <option value="index.php?option=com_adagency&controller=adagencyPackage<?php echo @$Itemid_pck; ?>"><?php echo JText::_('ADG_PACKAGES'); ?></option>                               
    <option value="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><?php echo JText::_('ADG_LOGOUT'); ?></option>
  </select>

  <!-- Title -->
  <div class="ada-reports-heading">
    <h2 class="ada-reports-title"><?php echo JText::_('REP_REPORTS'); ?></h2>
  </div>

  <div class="uk-grid uk-grid-small uk-grid-fix">
    <!-- Box: 01 -->
    <div class="uk-width-1-1 uk-width-medium-1-2">
      <div class="ada-reports-box ada-reports-box--primary">

        <div class="ada-reports-box-row">
          <div class="ada-reports-box-title">
            <small><?php echo JText::_("ADAG_ACTIVE_ADS"); ?></small>
          </div>
          <div class="ada-reports-box-counter">
            <?php
                if(intval($number_of_active_ads) == 0){
                    echo "<span>0</span>";
                }
                else{
            ?>
                    <a href="index.php?option=com_adagency&controller=adagencyAds&status_select=Y&camp_id=<?php echo intval($campaigns_stats); ?>"><?php echo intval($number_of_active_ads); ?></a>
            <?php
                }
            ?>
          </div>
        </div>

        <div class="ada-reports-box-row">
          <div class="ada-reports-box-title">
            <small><?php echo JText::_("ADAG_INACTIVE_ADS"); ?></small>
          </div>
          <div class="ada-reports-box-counter">
            <?php
                if(intval($number_of_inactive_ads) == 0){
                    echo "<span>0</span>";
                }
                else{
            ?>
                    <a href="index.php?option=com_adagency&controller=adagencyAds&status_select=N&camp_id=<?php echo intval($campaigns_stats); ?>"><?php echo intval($number_of_inactive_ads); ?></a>
            <?php
                }
            ?>
          </div>
        </div>

      </div>
    </div>

    <!-- Box: 02 -->
    <div class="uk-width-1-1 uk-width-medium-1-2">
      <div class="ada-reports-box ada-reports-box--secondary">

        <div class="ada-reports-box-row">
          <div class="ada-reports-box-title">
            <small><?php echo JText::_("ADAG_HIGHEST_CLICK_RATIO_AD"); ?></small>
          </div>
          <div class="ada-reports-box-counter">
            <?php
                if(isset($highest_click_ratio_ad) && count($highest_click_ratio_ad) > 0){
                    echo '<a href="index.php?option=com_adagency&controller=adagency'.$highest_click_ratio_ad["0"]["media_type"].'&task=edit&cid='.intval($highest_click_ratio_ad["0"]["id"]).'">'.$highest_click_ratio_ad["0"]["title"].'</a>';
                }
                else{
                    echo "<span>N/A</span>";
                }
            ?>
          </div>
        </div>

        <div class="ada-reports-box-row">
          <div class="ada-reports-box-title">
            <small><?php echo JText::_("ADAG_LOWEST_CLICK_RATIO_AD"); ?></small>
          </div>
          <div class="ada-reports-box-counter">
            <?php
                if(isset($lowest_click_ratio_ad) && count($lowest_click_ratio_ad) > 0){
                    echo '<a href="index.php?option=com_adagency&controller=adagency'.$lowest_click_ratio_ad["0"]["media_type"].'&task=edit&cid='.intval($lowest_click_ratio_ad["0"]["id"]).'">'.$lowest_click_ratio_ad["0"]["title"].'</a>';
                }
                else{
                    echo "<span>N/A</span>";
                }
            ?>
          </div>
        </div>

      </div>
    </div>
  </div>

  <form class="uk-form ada-reports-form" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
    <!-- Range Form -->
    <div class="ada-reports-range">
      <div class="ada-reports-range-heading">
        <h4 class="ada-reports-range-title"><?php echo JText::_("ADAG_SELECT_DATE_RANGE"); ?></h4>
      </div>

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

      <div class="uk-grid uk-grid-small">
        <div class="uk-width-large-1-2">

          <div class="ada-reports-range-data">
            
            <?php
                $start_date = strtotime($start_date);
                $start_date = date("Y-m-d", $start_date);
                
                $start_date_request = JRequest::getVar("start_date", "");
                $quick_range = JRequest::getVar("quick-range", "");
                if($start_date_request != "" && $quick_range == ""){
                    $start_date = $start_date_request;
                }
                
                echo"<span>";
                echo JHTML::_('calendar', $start_date, 'start_date', 'start_date', "%Y-%m-%d", array('maxlength'=>'19', 'onchange'=>'javascript:validateFrom(\''.JText::_("ADAG_FROM_LESS_START").'\', \''.JText::_("ADAG_FROM_GREATER_STOP").'\', \''.JText::_("ADAG_INVALID_DATE").'\');'));
                echo"</span>";
                
                $stop_date = strtotime($stop_date);
                $stop_date = date("Y-m-d", $stop_date);
                
                $stop_date_request = JRequest::getVar("stop_date", "");
                $quick_range = JRequest::getVar("quick-range", "");
                if($stop_date_request != "" && $quick_range == ""){
                    $stop_date = $stop_date_request;
                }
                
                echo"<span>";
                echo JHTML::_('calendar', $stop_date, 'stop_date', 'stop_date', "%Y-%m-%d", array('maxlength'=>'19', 'onchange'=>'javascript:validateTo(\''.JText::_("ADAG_TO_GREATER_STOP").'\', \''.JText::_("ADAG_TO_LESS_START").'\', \''.JText::_("ADAG_TO_LESS_FROM").'\', \''.JText::_("ADAG_INVALID_DATE").'\');'));
                echo"</span>";
            ?>

            <span class="uk-text-right">
              <a class="uk-button" href="#" onclick="document.getElementById('task').value='campaigns'; document.adminForm.submit(); return false;">
                <i class="uk-icon uk-icon-refresh"></i>
              </a>
            </span>
          </div>
        </div>

        <div class="uk-width-large-1-2">
          <div class="ada-reports-range-options">
            
            <?php
                $date_range = JRequest::getVar("date_range", "this_week");
                $active_range = JRequest::getVar("active_range", "tw");
                
                $lm_active = "";
                $lw_active = "";
                $tm_active = "";
                $tw_active = "";
                
                switch($active_range){
                    case "tw" : {
                        $tw_active = "active";
                        break;
                    }
                    case "tm" : {
                        $tm_active = "active";
                        break;
                    }
                    case "lw" : {
                        $lw_active = "active";
                        break;
                    }
                    case "lm" : {
                        $lm_active = "active";
                        break;
                    }
                }
            ?>

            <input type="hidden" value="<?php echo $date_range; ?>" name="date_range" />
            <input type="hidden" value="" name="active_range" />

            <ul class="ada-reports-range-list">
                <?php
                  echo '<li><a class="'.$lm_active.'" href="#" onclick="document.adminForm.date_range.value=\'last_month\';  document.adminForm.active_range.value=\'lm\'; document.getElementById(\'quick-range\').value=\'quick-range\'; document.getElementById(\'task\').value=\'campaigns\'; document.adminForm.submit(); return false;">'.JText::_("ADAG_LAST_MONTH").'</a></li>';
                ?>
                <?php
                  echo '<li><a class="'.$lw_active.'" href="#" onclick="document.adminForm.date_range.value=\'last_week\';  document.adminForm.active_range.value=\'lw\'; document.getElementById(\'quick-range\').value=\'quick-range\'; document.getElementById(\'task\').value=\'campaigns\';  document.adminForm.submit(); return false;">'.JText::_("ADAG_LAST_WEEK").'</a></li>';
                ?>
                <?php
                  echo '<li><a class="'.$tw_active.'" href="#" onclick="document.adminForm.date_range.value=\'this_week\';  document.adminForm.active_range.value=\'tw\'; document.getElementById(\'quick-range\').value=\'quick-range\'; document.getElementById(\'task\').value=\'campaigns\'; document.adminForm.submit(); return false;">'.JText::_("ADAG_THIS_WEEK").'</a></li>';
                ?>
                <?php
                  echo '<li><a class="'.$tm_active.'" href="#" onclick="document.adminForm.date_range.value=\'this_month\';  document.adminForm.active_range.value=\'tm\'; document.getElementById(\'quick-range\').value=\'quick-range\'; document.getElementById(\'task\').value=\'campaigns\'; document.adminForm.submit(); return false;">'.JText::_("ADAG_THIS_MONTH").'</a></li>';
                ?>
            </ul>
          </div>
        </div>

      </div>
    </div>

    <!-- Filters -->
    <div class="ada-reports-filters">
      <?php
          $user = JFactory::getUser();
          $user_id = $user->id;
      ?>
      <input type="hidden" name="advertisers" value="<?php echo intval($user_id); ?>" />

      <div class="ada-reports-filters-row">
        <ul class="uk-grid uk-grid-width-medium-1-3 uk-grid-fix">
          <!-- Campaigns -->
          <li>
            <h4 class="ada-reports-filter-title">
              <i class="uk-icon uk-icon-bullhorn uk-text-muted"></i>
              <?php echo JText::_("VIEWTREECAMPAIGNS"); ?>:
            </h4>
            <select class="uk-form-small uk-width-1-1" name="campaigns" onchange="document.getElementById('task').value='campaigns'; document.adminForm.submit();" class="select-reports">
                <?php
                    $campaigns = JRequest::getVar("campaigns", "0");
                    if($all_campaigns_by_adv && count($all_campaigns_by_adv) > 0){
                        $first_campaign = 0;
                        $i = 0;
                        $selected_camp = false;
                        
                        foreach($all_campaigns_by_adv as $key=>$value){
                            if($i == 0){
                                $first_campaign = $value["id"];
                                $i++;
                            }
                            
                            $selected = "";
                            if($value["id"] == $campaigns){
                                $selected = 'selected="selected"';
                                $selected_camp = true;
                            }
                            echo '<option value="'.$value["id"].'" '.$selected.'>'.$value["name"]."</option>";
                        }
                        
                        if($selected_camp === FALSE){
                            $campaigns = $first_campaign;
                        }
                    }
                    else{
                        echo '<option value="0">'.JText::_("AD_CMP_CMPNAME").'</option>';
                        $campaigns = "0";
                    }
                ?>
            </select>
          </li>
          <!-- Ad Name -->
          <li>
            <h4 class="ada-reports-filter-title">
              <i class="uk-icon uk-icon-tag uk-text-muted"></i>
              <?php echo JText::_("ADAG_AD_NAME"); ?>:
            </h4>

            <?php
                $ad_name = JRequest::getVar("ad_name", "0");
            ?>

            <select class="uk-form-small uk-width-1-1" name="ad_name" onchange="document.getElementById('task').value='campaigns'; document.adminForm.submit();" class="select-reports">
                <?php
                echo '<option value="0">'.JText::_("ADAG_HEAD_AD_NAME").'</option>';
                
                if(isset($all_ads) && count($all_ads) > 0){
                    $first_ad = 0;
                    $i = 0;
                    $selected_ad = false;
                    
                    foreach($all_ads as $key=>$value){
                        if($i == 0){
                            $first_ad = $value["id"];
                            $i++;
                        }
                        
                        $selected = "";
                        if($ad_name == $value["id"]){
                            $selected = 'selected="selected"';
                            $selected_ad = true;
                        }
                        echo '<option value="'.$value["id"].'" '.$selected.'>'.$value["title"].'</option>';
                    }
                }
                ?>
            </select>
          </li>
          <!-- Stats -->
          <li>
            <h4 class="ada-reports-filter-title">
              <i class="uk-icon uk-icon-bar-chart-o uk-text-muted"></i>
              <?php echo JText::_("ADAG_STATS"); ?>:
            </h4>

            <?php
                $chart_type = JRequest::getVar("chart_type", "summary");
            ?>

            <select class="uk-form-small uk-width-1-1" name="chart_type" onchange="document.getElementById('task').value='campaigns'; document.adminForm.submit();" class="select-reports">
                <option value="summary" <?php if($chart_type == "summary"){echo 'selected="selected"';} ?> > <?php echo JText::_("ADAG_SUMMARY"); ?> </option>
                <option value="impressions" <?php if($chart_type == "impressions"){echo 'selected="selected"';} ?> > <?php echo JText::_("ADAG_HEAD_IMPRESSIONS"); ?> </option>
                <option value="clicks" <?php if($chart_type == "clicks"){echo 'selected="selected"';} ?> > <?php echo JText::_("ADAG_HEAD_CLICKS"); ?> </option>
                <option value="ctr" <?php if($chart_type == "ctr"){echo 'selected="selected"';} ?> > <?php echo JText::_("ADAG_CTR"); ?> </option>
            </select>
          </li>
        </ul>
      </div>
    </div>

    <div class="ada-reports-graph">
      <ul class="ada-reports-graph-legend">
        <li><span class="legend-impressions"></span><?php echo JText::_("ADAG_HEAD_IMPRESSIONS"); ?></li>
        <li><span class="legend-clicks"></span><?php echo JText::_("ADAG_HEAD_CLICKS"); ?></li>
        <li><span class="legend-ctr"></span><?php echo JText::_("ADAG_HEAD_CLICK_RATIO"); ?></li>
      </ul>
      <div class="ada-reports-graph-charts">
        <?php
            $diagram = new Diagram();
            $diagram->plot($advertisers, $campaigns, $ad_name, $all_ads);
        ?>
      </div>
    </div>

    <div class="ada-reports-table uk-clearfix">
      <div class="ada-reports-table-heading">
        <div class="uk-grid uk-grid-small">

          <div class="uk-width-medium-2-3">
            <h4 class="ada-reports-table-title">
              <?php
                  echo JText::_("ADAG_DATE_RANGE_PER_SELECTED_PERIOD");
              ?>
            </h4>
          </div>

          <div class="uk-width-medium-1-3">
            <div class="uk-button-dropdown uk-align-medium-right btn-group" data-uk-dropdown>
                <button class="uk-button uk-button-success dropdown-toggle" data-toggle="dropdown">
                    <?php echo JText::_("ADAG_EXPORT_TO"); ?> &nbsp; <span class="uk-icon uk-icon-caret-down"></span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#" onclick="document.adminForm.task.value='campaigns_csv'; document.adminForm.submit();">CSV</a>
                    </li>
                    <li class="uk-nav-divider"></li>
                    <li>
                        <a href="#" onclick="document.adminForm.task.value='campaigns_pdf'; document.adminForm.submit();">PDF</a>
                    </li>
                </ul>
            </div>
          </div>

        </div>
      </div>

      <table class="uk-table table-counter sortable">
          <thead>
              <tr>
                  <th>#</th>
                  <th nowrap="nowrap" class="sorttable_nosort"><input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" /></th>
                  <th nowrap="nowrap"><?php echo JText::_("ADAG_HEAD_CAMPAIGN"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                  <th nowrap="nowrap" class="uk-visible-large"><?php echo JText::_("ADAG_HEAD_CAMPAIGN_COST"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                  <th nowrap="nowrap" class="uk-visible-large"><?php echo JText::_("ADAG_HEAD_IMPRESSIONS"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                  <th nowrap="nowrap" class="uk-visible-large"><?php echo JText::_("ADAG_HEAD_CLICKS"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                  <th nowrap="nowrap"><?php echo JText::_("ADAG_HEAD_CLICK_RATIO"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                  <th nowrap="nowrap"><?php echo JText::_("ADAG_HEAD_CPC"); ?> <i class="uk-icon uk-icon-sort"></i></th>
                  <th nowrap="nowrap"><?php echo JText::_("ADAG_HEAD_CPI"); ?> <i class="uk-icon uk-icon-sort"></i></th>
              </tr>
          </thead>
          <tbody>
            <?php
                if(isset($table_cmp_content) && count($table_cmp_content) > 0){
                    foreach($table_cmp_content as $key=>$value){
            ?>
              <tr>
                  <td></td>
                  <td><?php echo JHTML::_('grid.id', $i, $value["campaign_id"]); ?></td>
                  <td>
                      <a class="uk-button uk-button-small" href="index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid[]=<?php echo intval($value["campaign_id"]); ?>">
                          <?php echo $value["campaign"]; ?>
                      </a>
                  </td>
                  <td class="uk-visible-large">
                      <?php echo $value["cost"]; ?>
                  </td>
                  <td class="uk-visible-large">
                      <?php echo $value["impressions"]; ?>
                  </td>
                  <td class="uk-visible-large">
                      <?php echo $value["click"]; ?>
                  </td>
                  <td>
                      <?php echo $value["click_ratio"]; ?>
                  </td>
                  <td style="word-spacing: -3px; white-space: nowrap;">
                          <?php
                              echo $value["cpc"];
                          ?>
                  </td>
                  <td style="word-spacing: -3px; white-space: nowrap;">
                          <?php
                              echo $value["cpi"];
                          ?>
                  </td>
              </tr>
            <?php
                    }
                }
            ?>
          </tbody>
      </table>

      <div class="ada-reports-table-opt">
        <div class="uk-align-medium-right">
          <?php echo JText::_("ADAG_VALUES_NOT_AFFECTED_BY_DATE"); ?>
        </div>
        
        <div class="uk-left">
          <?php echo $this->pagination->getLimitBox(); ?>
        </div>
        
        <div class="uk-clearfix">
          <?php echo $this->pagination->getListFooter(); ?>
        </div>
      </div>
    </div>

    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="controller" value="adagencyReports" />
    <input type="hidden" name="task" id="task" value="campaigns" />
    <input type="hidden" name="new_adv" value="0" />
    <input type="hidden" name="quick-range" id="quick-range" value="" />
    <input type="hidden" name="min" id="min" value="<?php echo trim($min); ?>" />
    <input type="hidden" name="max" id="max" value="<?php echo trim($max); ?>" />
  </form>
</div>
