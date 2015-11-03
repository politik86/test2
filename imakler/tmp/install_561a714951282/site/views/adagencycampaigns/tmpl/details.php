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
$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.adagency.js" );
$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js");
$document->addScript(JURI::root()."components/com_adagency/includes/js/graybox.js");

require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
include(JPATH_BASE."/components/com_adagency/includes/js/campaigns.php");

$camp_row = $this->camp;
$package_row = $this->package_row;
$configs = $this->configs;
$stats = $this->stats;
@$advertiser = $this->advt;
$lists = $this->lists;
$task = $this->task;
$helper = new adagencyAdminHelper();
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
</style>

<table class="uk-table uk-table-striped">
	<tr>
    	<td>
        	<?php echo JText::_("ADAG_CPM_STARTDATE"); ?>
        </td>
        <td>
			<?php echo $helper->formatime($camp_row->start_date, $configs->params['timeformat']); ?>
        </td>
    </tr>
    
    <tr>
        <td>
			<?php echo JText::_('VIEWPACKAGETERMS'); ?>
		</td>
        <td>
			<?php echo $package_row->details; ?>
		</td>
    </tr>
    
    <tr>
        <td>
			<?php echo JText::_('VIEWORDERSPAID'); ?>
		</td>
        <td>
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
		</td>
    </tr>
    
    <tr>
        <td>
        	<?php echo JText::_("AD_CMPACTIVE"); ?>
		</td>
        <td>
            <?php
				$expired=0;
				if(($camp_row->type=="cpm"  || $camp_row->type=="pc") && $camp_row->quantity < 1){
					$expired=1;
				}
				
				if($camp_row->type=="fr" || $camp_row->type=="in"){
					$datan = date("Y-m-d H:i:s");
					if($datan > $camp_row->validity && $camp_row->validity != "0000-00-00 00:00:00"){
						$expired=1;
					}
				}
				
				
				if(strtotime($camp_row->start_date) > time()){
					echo JText::_("ADAG_START").": ".$helper->formatime($camp_row->start_date, $configs->params['timeformat']);
				}
				elseif($camp_row->status == 1){
					echo '<span class="ad_active_camp">'.JText::_("JYES").'</span>';
				}
				elseif($camp_row->status == 0){
					echo '<span class="ad_inactive_camp">'.JText::_("JNO").'</span>';
				}
            ?>
        </td>
    </tr>
    
</table>
