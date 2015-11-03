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

$db = JFactory::getDBO();
$sql = "SELECT `cost` , `order_date` , `currency` FROM `#__ad_agency_order` WHERE `status`='paid' order by order_date ";
$db->setQuery($sql);
$db->query();
$result = $db->loadAssocList();

if(!isset($result) || count($result) == 0){
	$sql = "select `currencydef` from #__ad_agency_settings";
	$db->setQuery($sql);
	$db->query();
	$currency = $db->loadColumn();
	$currency = @$currency["0"];
	$result = array("0"=>array("cost"=>"0.1","currency"=>$currency, "order_date"=>date("Y-m-d H:i:s")));
}

if(isset($result) && count($result) > 0){
	$total_values = array();
	foreach($result as $key=>$value){
		if(trim($value["currency"]) != "" || $value["order_date"] != "0000-00-00 00:00:00"){
			$price = $value["cost"];
			if(isset($total_values[$value["currency"]][strtotime($value["order_date"])."000"])){
				$total_values[$value["currency"]][strtotime($value["order_date"])."000"] += $price;
			}
			else{
				$total_values[$value["currency"]][strtotime($value["order_date"])."000"] = $price;
			}
		}
	}
	
	if(isset($total_values) && count($total_values) > 0){
		echo '<script type="text/javascript" language="javascript">'."\n";
		echo '$(function() {'."\n";
		$k = 1;
		$params_array = array();
		
		foreach($total_values as $key=>$value){
			$temp_array = array();
			foreach($value as $time=>$price){
				$temp_array[] = '['.$time.','.$price.']';
			}
			$params_array[] = '{ data: variable_'.$k.', label: "Price in '.$key.'" }';
			echo 'var variable_'.$k.' = ['.implode(", ", $temp_array).'];'."\n";
		 	
			$k ++;
		}
		
		echo 'function euroFormatter(v, axis) {
				return v.toFixed(axis.tickDecimals) + "�";
			  }'."\n";
		
		echo 'function doPlot(position) {
					$.plot("#placeholder", [
						'.implode(", \n", $params_array).'
					], {
						xaxes: [ { mode: "time" } ],
						yaxes: [ { min: 0 }, {
							// align if we are to the right
							alignTicksWithAxis: position == "right" ? 1 : null,
							position: position,
							tickFormatter: euroFormatter
						} ],
						legend: { position: "sw" }
					});
				}
				
				doPlot("right");';	  
			  
		echo '});'."\n";
		echo '</script>';
	}
}
?>