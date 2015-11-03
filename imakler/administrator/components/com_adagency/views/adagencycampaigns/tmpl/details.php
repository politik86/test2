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

$document = JFactory::getDocument();
$document->addScript(JURI::root()."components/com_adagency/includes/js/graybox.js");
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");

require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
include(JPATH_BASE."/components/com_adagency/includes/js/campaigns.php");
$helper = new adagencyAdminHelper();
$camp_row = $this->camp;
$package_row = $this->package_row;
$configs = $this->configs;
$stats = $this->stats;
$advertiser = $this->advt;
$lists = $this->lists;
$task = $this->task;

?>

<style type="text/css">	
	.ad_paid_yes {
		color: #669900;
		font-size: 12px;
	}
	
	.ad_paid_no {
		color: #FF0000;
		font-size: 12px;
	}
	
	.ad_inactive_camp {
    color: #FF0000;
		font-size: 12px;
	}
	
	.ad_active_camp {
		color: #669900;
		font-size: 12px;
	}
	
	.campaign-details{
		margin-left: -190px !important;
	}
</style>


<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
<div class="campaign-details">	
    <div class="control-group">
        <label class="control-label"> <?php echo JText::_('ADAG_CPM_STARTDATE'); ?> </label>
        <div class="controls">
            <?php echo $helper->formatime($camp_row->start_date, $configs->params['timeformat']); ?>
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label"> <?php echo JText::_('VIEWPACKAGETERMS'); ?> </label>
        <div class="controls">
            <?php echo $package_row->details; ?>
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label"> <?php echo JText::_('VIEWORDERSPAID'); ?> </label>
        <div class="controls">
            <?php
                    $package_id = $camp_row->otid;
                    $order_details = $this->getOrderDetails($camp_row->id, $package_id);
                    if(isset($order_details["0"]) && $order_details["0"]["payment_type"] == "Free"){
                        echo '<span class="ad_paid_yes">'.JText::_("VIEWPACKAGEFREE").'</span>';
                    }
                    elseif(isset($order_details) && isset($order_details["0"]) && $order_details["0"]["status"] == "paid"){
                        echo '<span class="ad_paid_yes">'.JText::_("VIEWORDERSYES").'</span>';
                    }
                    else{
                        echo '<span class="ad_paid_no">'.JText::_("VIEWORDERSNO");
                    }
                ?>
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label"> <?php echo JText::_('AD_CMPACTIVE'); ?> </label>
        <div class="controls">
            <?php
                    $expired=0;
                    if(($camp_row->type=="cpm" || $camp_row->type=="pc") && $camp_row->quantity < 1){
                        $expired=1;
                    }
                    
                    if($camp_row->type=="fr"){
                        $datan = date("Y-m-d H-i-s");
                        if(strtotime($datan) > strtotime($camp_row->camp_validity)){
                            $expired=1;
                        }
                    }
                    
                    if(strtotime($camp_row->start_date) > time() && $camp_row->status != "-1"){
                        echo JText::_("ADAG_START").": ".$helper->formatime($camp_row->start_date, $configs->params['timeformat']);
                    }
                    elseif($camp_row->status == "1"){
                        echo '<span class="ad_active_camp">'.JText::_("JYES").'</span>';
                    }
                    elseif($camp_row->status == "0"){
                        echo '<span class="ad_inactive_camp">'.JText::_("JNO").'</span>';
                    }
                    elseif($camp_row->status == "-1"){
                        echo '<span class="ad_inactive_camp">'.JText::_("JNO").'</span>';
                    }
                ?>
        </div>
    </div>
</div>
</form>