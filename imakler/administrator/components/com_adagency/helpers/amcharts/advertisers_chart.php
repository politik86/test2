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

function getAllAdvertisers(){
	$db = JFactory::getDBO();
	$sql = "select a.`aid`, u.`name` from #__ad_agency_advertis a, #__users u where a.`user_id`=u.`id`";
	$db->setQuery($sql);
	$db->query();
	$result = $db->loadAssocList('aid');
	return $result;
}

function getAllAds(){
	$db = JFactory::getDBO();
	$sql = "select `id`, `media_type` from #__ad_agency_banners";
	$db->setQuery($sql);
	$db->query();
	$result = $db->loadAssocList('id');
	return $result;
}

$db = JFactory::getDBO();

$chart_type = JRequest::getVar("chart_type", "impressions");
$date_range = JRequest::getVar("date_range", "this_week");
$campaigns = JRequest::getVar("campaigns", "0");
$ad_type = JRequest::getVar("ad_type", "0");

$advertisers = JRequest::getVar("advertisers", "0");
$all_advertisers = getAllAdvertisers();
$all_ads = getAllAds();

if(intval($advertisers) == 0){
	if(isset($all_advertisers) && count($all_advertisers) > 0){
		$temp = @array_shift(array_slice($all_advertisers, 0, 1));
		$advertisers = $temp["aid"];
	}
}

$start_date = "";
$stop_date = "";

if($date_range == "this_week"){
	$start_date = date("Y-m-d", strtotime('monday this week'));
	$stop_date = date("Y-m-d", strtotime("sunday this week"));
}
elseif($date_range == "last_week"){
	$start_date = date("Y-m-d", strtotime('monday last week'));
	$stop_date = date("Y-m-d", strtotime("sunday last week"));
}
elseif($date_range == "last_month"){
	$start_date = date('Y-m-d', strtotime('first day of last month'));
	$stop_date = date('Y-m-d', strtotime('last day of last month'));
}
elseif($date_range == "this_month"){
	$start_date = date('Y-m-d', strtotime('first day of this month'));
	$stop_date = date('Y-m-d', strtotime('last day of this month'));
}

$start_date_request = JRequest::getVar("start_date", "");
$quick_range = JRequest::getVar("quick-range", "");
if($start_date_request != "" && $quick_range == ""){
	$start_date = $start_date_request;
}

$stop_date_request = JRequest::getVar("stop_date", "");
$quick_range = JRequest::getVar("quick-range", "");
if($stop_date_request != "" && $quick_range == ""){
	$stop_date = $stop_date_request;
}

// start impressions chart --------------------------------------------------
if($chart_type == "impressions"){
	$sql = "select `entry_date`, `impressions` from #__ad_agency_statistics where `entry_date` >= '".$start_date."' and `entry_date` <= '".$stop_date."' order by `entry_date` asc";
	$db->setQuery($sql);
	$db->query();
	$result = $db->loadAssocList();
	
	if(!isset($result) || count($result) == 0){
		$result = array("0"=>array("cost"=>"0.1", "order_date"=>date("Y-m-d H:i:s")));
	}
	
	if(isset($result) && count($result) > 0){
		$total_values = array();
		foreach($result as $key=>$value){
			if(isset($value["impressions"]) && trim($value["impressions"]) != ""){
				$impressions = json_decode($value["impressions"], true);
				$entry_date = $value["entry_date"]." 00:00:00";
				
				if(isset($impressions) && count($impressions) > 0){
					foreach($impressions as $imp_key=>$imp_value){
						if($advertisers != 0 && $imp_value["advertiser_id"] != $advertisers){
							continue;
						}
						
						if($campaigns != 0 && $imp_value["campaign_id"] != $campaigns){
							continue;
						}
						
						if(trim($ad_type) != "0" && $all_ads[$imp_value["banner_id"]]["media_type"] != $ad_type){
							continue;
						}
						
						if(intval($imp_value["campaign_id"]) <= 0){
							continue;
						}
						
						if(intval($imp_value["how_many"]) != "0"){
							$how_many = intval($imp_value["how_many"]);
							$campaign_id = $imp_value["campaign_id"];
							
							if(isset($total_values[$campaign_id][strtotime($entry_date)."000"])){
								$total_values[$campaign_id][strtotime($entry_date)."000"] += $how_many;
							}
							else{
								$total_values[$campaign_id][strtotime($entry_date)."000"] = $how_many;
							}
						}
					}
				}
			}
		}
		
		// add new values to diagram ------------------------------------------------------------
		$start = strtotime($start_date);
		$stop = strtotime($stop_date);
		
		if(count($total_values) > 0){
			for($i=$start; $i<=$stop; $i+=86400){
				$current_key = "";
				foreach($total_values as $key=>$value){
					$current_key = $key;
					$j = $i."000";
					if(!isset($value[$j])){
						$total_values[$key][$j] = "0.01";
					}
					ksort($total_values[$current_key]);
				}
			}
		}
		// add new values to diagram ------------------------------------------------------------
		
		if(isset($total_values) && count($total_values) > 0){
			$sql = "select `id`, `name` from #__ad_agency_campaign";
			$db->setQuery($sql);
			$db->query();
			$all_campaigns = $db->loadAssocList("id");
			
			echo '<script type="text/javascript" language="javascript">'."\n";
			echo '$(function() {'."\n";
			$k = 1;
			$params_array = array();
			
			foreach($total_values as $key=>$value){
				$temp_array = array();
				
				foreach($value as $time=>$how_many){
					$temp_label  = "<b>".JText::_("ADAG_HEAD_CAMPAIGN")."</b>: ".@$all_campaigns[$key]["name"]."<br/>";
					$temp_label .= "<b>".JText::_("ADAG_HEAD_DATE")."</b>: ".date("Y-m-d", substr($time, 0, -3))."<br/>";
					if($how_many == 0.01){
						$temp_label .= "<b>".JText::_("ADAG_HEAD_IMPRESSIONS")."</b>: "."0";
					}
					else{
						$temp_label .= "<b>".JText::_("ADAG_HEAD_IMPRESSIONS")."</b>: ".$how_many;
					}
					
					$temp_array[] = '['.$time.','.$how_many.',"'.$temp_label.'"]';
				}
				
				$params_array[] = '{ data: variable_'.$k.', label: "'.@$all_campaigns[$key]["name"].'" }';
				echo 'var variable_'.$k.' = ['.implode(", ", $temp_array).'];'."\n";
				
				$k ++;
			}
			
			echo 'function doPlot(position) {
						document.getElementById("placeholder").innerHTML = "";
						var plot = $.plot("#placeholder", [
							'.implode(", \n", $params_array).'
						], {
							xaxes: [ { mode: "time" } ],
							yaxes: [ { min: 0 }, {
								// align if we are to the right
								alignTicksWithAxis: position == "right" ? 1 : null,
								position: position
							} ],
							legend: { position: "nw", container: $(".diagram-legend"), noColumns: 4 },
							series: {
							   lines: { show: true },
							   points: { show: true }
						   	},
						   	grid: { hoverable: true, clickable: true },
							zoom: {
								interactive: false
							},
							pan: {
								interactive: true
							}
						});
						
						// add zoom text

						$("<div class=\'button uk-text-bold\' style=\'float: right; margin-right: 95px; margin-top: -24px;\'>Zoom</div>").appendTo(placeholder);

						

						// add zoom home reset

						$("<div class=\'button btn btn-mini btn-info\' style=\' cursor: pointer; float: right; margin-right: -2px; margin-top: -28px;\'><i class=\'uk-icon uk-icon-refresh\'></i></div>")

							.appendTo(placeholder)

							.click(function (event) {

								event.preventDefault();

								doPlot("left");

							});

						

						// add zoom in button

						$("<div class=\'button btn btn-mini btn-success\' style=\' cursor: pointer; float: right; margin-top: -28px; margin-right: ;\'><i class=\'uk-icon uk-icon-plus-circle\'></i></div>")

							.appendTo(placeholder)

							.click(function (event) {

								event.preventDefault();

								plot.zoom();

							});

							

						// add zoom out button

						$("<div class=\'button btn btn-mini btn-danger\' style=\' cursor: pointer; float: right; margin-right: ; margin-top: -28px;\'><i class=\'uk-icon uk-icon-minus-circle\'></i></div>")

							.appendTo(placeholder)

							.click(function (event) {

								event.preventDefault();

								plot.zoomOut();

							});
							
						$("#placeholder").bind("plothover", function (event, pos, item) {
							$("#tooltip").remove();
							if (item) {
								var tooltip = item.series.data[item.dataIndex][2];
								
								$(\'<div id="tooltip">\' + tooltip + \'</div>\')
									.css({
										position: \'absolute\',
										display: \'none\',
										top: item.pageY + 15,
										left: item.pageX + 15,
										border: \'1px solid #fdd\',
										padding: \'2px\',
										\'background-color\': \'#fee\',
										opacity: 0.80 })
									.appendTo("body").fadeIn(200);
					
								
								showTooltip(item.pageX, item.pageY, tooltip);
							}
						});
					}
					
					doPlot("left");';	  
				  
			echo '});'."\n";
			echo '</script>';
		}
	}
}
// stop impressions chart --------------------------------------------------

// start clicks chart --------------------------------------------------
if($chart_type == "clicks"){
	$sql = "select `entry_date`, `click` from #__ad_agency_statistics where `entry_date` >= '".$start_date."' and `entry_date` <= '".$stop_date."' order by `entry_date` asc";
	$db->setQuery($sql);
	$db->query();
	$result = $db->loadAssocList();
	
	if(!isset($result) || count($result) == 0){
		$result = array("0"=>array("cost"=>"0.1", "order_date"=>date("Y-m-d H:i:s")));
	}
	
	if(isset($result) && count($result) > 0){
		$total_values = array();
		foreach($result as $key=>$value){
			if(trim($value["click"]) != ""){
				$click = json_decode($value["click"], true);
				$entry_date = $value["entry_date"]." 00:00:00";
				
				if(isset($click) && count($click) > 0){
					foreach($click as $click_key=>$click_value){
						if($advertisers != 0 && $click_value["advertiser_id"] != $advertisers){
							continue;
						}
						
						if($campaigns != 0 && $click_value["campaign_id"] != $campaigns){
							continue;
						}
						
						if(intval($click_value["how_many"]) != "0"){
							$how_many = intval($click_value["how_many"]);
							$campaign_id = $click_value["campaign_id"];
							
							if(isset($total_values[$campaign_id][strtotime($entry_date)."000"])){
								$total_values[$campaign_id][strtotime($entry_date)."000"] += $how_many;
							}
							else{
								$total_values[$campaign_id][strtotime($entry_date)."000"] = $how_many;
							}
						}
					}
				}
			}
		}
		
		// add new values to diagram ------------------------------------------------------------
		$start = strtotime($start_date);
		$stop = strtotime($stop_date);
		
		if(count($total_values) > 0){
			for($i=$start; $i<=$stop; $i+=86400){
				$current_key = "";
				foreach($total_values as $key=>$value){
					$current_key = $key;
					$j = $i."000";
					if(!isset($value[$j])){
						$total_values[$key][$j] = "0.01";
					}
					ksort($total_values[$current_key]);
				}
			}
		}
		// add new values to diagram ------------------------------------------------------------
		
		if(isset($total_values) && count($total_values) > 0){
			$sql = "select `id`, `name` from #__ad_agency_campaign";
			$db->setQuery($sql);
			$db->query();
			$all_campaigns = $db->loadAssocList("id");
			
			echo '<script type="text/javascript" language="javascript">'."\n";
			echo '$(function() {'."\n";
			$k = 1;
			$params_array = array();
			
			foreach($total_values as $key=>$value){
				$temp_array = array();
				
				foreach($value as $time=>$how_many){
					$temp_label  = "<b>".JText::_("ADAG_HEAD_CAMPAIGN")."</b>: ".@$all_campaigns[$key]["name"]."<br/>";
					$temp_label .= "<b>".JText::_("ADAG_HEAD_DATE")."</b>: ".date("Y-m-d", substr($time, 0, -3))."<br/>";
					if($how_many == 0.01){
						$temp_label .= "<b>".JText::_("ADAG_HEAD_CLICKS")."</b>: "."0";
					}
					else{
						$temp_label .= "<b>".JText::_("ADAG_HEAD_CLICKS")."</b>: ".$how_many;
					}
					
					$temp_array[] = '['.$time.','.$how_many.',"'.$temp_label.'"]';
				}
				
				$params_array[] = '{ data: variable_'.$k.', label: "'.@$all_campaigns[$key]["name"].'" }';
				echo 'var variable_'.$k.' = ['.implode(", ", $temp_array).'];'."\n";
				
				$k ++;
			}
			
			echo 'function doPlot(position) {
						document.getElementById("placeholder").innerHTML = "";
						var plot = $.plot("#placeholder", [
							'.implode(", \n", $params_array).'
						], {
							xaxes: [ { mode: "time" } ],
							yaxes: [ { min: 0 }, {
								// align if we are to the right
								alignTicksWithAxis: position == "right" ? 1 : null,
								position: position
							} ],
							legend: { position: "nw", container: $(".diagram-legend"), noColumns: 4 },
							series: {
							   lines: { show: true },
							   points: { show: true }
						   	},
						   	grid: { hoverable: true, clickable: true },
							zoom: {
								interactive: false
							},
							pan: {
								interactive: true
							}
						});
						
						// add zoom text

						$("<div class=\'button uk-text-bold\' style=\'float: right; margin-right: 95px; margin-top: -24px;\'>Zoom</div>").appendTo(placeholder);

						

						// add zoom home reset

						$("<div class=\'button btn btn-mini btn-info\' style=\' cursor: pointer; float: right; margin-right: -2px; margin-top: -28px;\'><i class=\'uk-icon uk-icon-refresh\'></i></div>")

							.appendTo(placeholder)

							.click(function (event) {

								event.preventDefault();

								doPlot("left");

							});

						

						// add zoom in button

						$("<div class=\'button btn btn-mini btn-success\' style=\' cursor: pointer; float: right; margin-top: -28px; margin-right: ;\'><i class=\'uk-icon uk-icon-plus-circle\'></i></div>")

							.appendTo(placeholder)

							.click(function (event) {

								event.preventDefault();

								plot.zoom();

							});

							

						// add zoom out button

						$("<div class=\'button btn btn-mini btn-danger\' style=\' cursor: pointer; float: right; margin-right: ; margin-top: -28px;\'><i class=\'uk-icon uk-icon-minus-circle\'></i></div>")

							.appendTo(placeholder)

							.click(function (event) {

								event.preventDefault();

								plot.zoomOut();

							});
							
						$("#placeholder").bind("plothover", function (event, pos, item) {
							$("#tooltip").remove();
							if (item) {
								var tooltip = item.series.data[item.dataIndex][2];
								
								$(\'<div id="tooltip">\' + tooltip + \'</div>\')
									.css({
										position: \'absolute\',
										display: \'none\',
										top: item.pageY + 15,
										left: item.pageX + 15,
										border: \'1px solid #fdd\',
										padding: \'2px\',
										\'background-color\': \'#fee\',
										opacity: 0.80 })
									.appendTo("body").fadeIn(200);
					
								
								showTooltip(item.pageX, item.pageY, tooltip);
							}
						});
					}
					
					doPlot("left");';	  
				  
			echo '});'."\n";
			echo '</script>';
		}
	}
}
// stop clicks chart --------------------------------------------------

// start CTR chart --------------------------------------------------
if($chart_type == "ctr"){
	$sql = "select `entry_date`, `impressions`, `click` from #__ad_agency_statistics where `entry_date` >= '".$start_date."' and `entry_date` <= '".$stop_date."' order by `entry_date` asc";
	$db->setQuery($sql);
	$db->query();
	$result = $db->loadAssocList();
	
	if(!isset($result) || count($result) == 0){
		$result = array("0"=>array("cost"=>"0.1", "order_date"=>date("Y-m-d H:i:s")));
	}
	
	if(isset($result) && count($result) > 0){
		$total_values_imp = array();
		$total_values_click = array();
		
		foreach($result as $key=>$value){
			$click = @json_decode($value["click"], true);
			$impressions = @json_decode($value["impressions"], true);
			$entry_date = $value["entry_date"]." 00:00:00";
			
			if(isset($impressions) && count($impressions) > 0){
				foreach($impressions as $imp_key=>$imp_value){
					if($advertisers != 0 && $imp_value["advertiser_id"] != $advertisers){
						continue;
					}
					
					if($campaigns != 0 && $imp_value["campaign_id"] != $campaigns){
						continue;
					}
					
					if(intval($imp_value["how_many"]) != "0"){
						$how_many = intval($imp_value["how_many"]);
						$campaign_id = $imp_value["campaign_id"];
						
						if(isset($total_values_imp[$campaign_id][strtotime($entry_date)."000"])){
							$total_values_imp[$campaign_id][strtotime($entry_date)."000"] += $how_many;
						}
						else{
							$total_values_imp[$campaign_id][strtotime($entry_date)."000"] = $how_many;
						}
					}
				}
			}
			
			if(isset($click) && count($click) > 0){
				foreach($click as $click_key=>$click_value){
					if($advertisers != 0 && $click_value["advertiser_id"] != $advertisers){
						continue;
					}
					
					if($campaigns != 0 && $click_value["campaign_id"] != $campaigns){
						continue;
					}
					
					if(intval($click_value["how_many"]) != "0"){
						$how_many = intval($click_value["how_many"]);
						$campaign_id = $click_value["campaign_id"];
						
						if(isset($total_values_click[$campaign_id][strtotime($entry_date)."000"])){
							$total_values_click[$campaign_id][strtotime($entry_date)."000"] += $how_many;
						}
						else{
							$total_values_click[$campaign_id][strtotime($entry_date)."000"] = $how_many;
						}
					}
				}
			}
		}
		
		$total_values = array();
		if(isset($total_values_imp) && count($total_values_imp) > 0){
			foreach($total_values_imp as $camp_id=>$value){
				if(isset($total_values_click[$camp_id])){
					foreach($value as $date=>$how_many){
						if(isset($total_values_click[$camp_id][$date])){
							$nr = $total_values_click[$camp_id][$date] / $total_values_imp[$camp_id][$date];
							$total_values[$camp_id][$date] = number_format($nr, 2, '.', '');
						}
					}
				}
			}
		}
		
		// add new values to diagram ------------------------------------------------------------
		$start = strtotime($start_date);
		$stop = strtotime($stop_date);
		
		if(count($total_values) > 0){
			for($i=$start; $i<=$stop; $i+=86400){
				$current_key = "";
				foreach($total_values as $key=>$value){
					$current_key = $key;
					$j = $i."000";
					if(!isset($value[$j])){
						$total_values[$key][$j] = "0.01";
					}
					ksort($total_values[$current_key]);
				}
			}
		}
		// add new values to diagram ------------------------------------------------------------
		
		if(isset($total_values) && count($total_values) > 0){
			$sql = "select `id`, `name` from #__ad_agency_campaign";
			$db->setQuery($sql);
			$db->query();
			$all_campaigns = $db->loadAssocList("id");
			
			echo '<script type="text/javascript" language="javascript">'."\n";
			echo '$(function() {'."\n";
			$k = 1;
			$params_array = array();
			
			foreach($total_values as $key=>$value){
				$temp_array = array();
				
				foreach($value as $time=>$how_many){
					$temp_label  = "<b>".JText::_("ADAG_HEAD_CAMPAIGN")."</b>: ".@$all_campaigns[$key]["name"]."<br/>";
					$temp_label .= "<b>".JText::_("ADAG_HEAD_DATE")."</b>: ".date("Y-m-d", substr($time, 0, -3))."<br/>";
					if($how_many == 0.01){
						$temp_label .= "<b>".JText::_("VIEWADCRT")."</b>: "."0";
					}
					else{
						$temp_label .= "<b>".JText::_("VIEWADCRT")."</b>: ".$how_many;
					}
					
					$temp_array[] = '['.$time.','.$how_many.',"'.$temp_label.'"]';
				}
				
				$params_array[] = '{ data: variable_'.$k.', label: "'.@$all_campaigns[$key]["name"].'" }';
				echo 'var variable_'.$k.' = ['.implode(", ", $temp_array).'];'."\n";
				
				$k ++;
			}
			
			echo 'function doPlot(position) {
						document.getElementById("placeholder").innerHTML = "";
						var plot = $.plot("#placeholder", [
							'.implode(", \n", $params_array).'
						], {
							xaxes: [ { mode: "time" } ],
							yaxes: [ { min: 0 }, {
								// align if we are to the right
								alignTicksWithAxis: position == "right" ? 1 : null,
								position: position
							} ],
							legend: { position: "nw", container: $(".diagram-legend"), noColumns: 4 },
							series: {
							   lines: { show: true },
							   points: { show: true }
						   	},
						   	grid: { hoverable: true, clickable: true },
							zoom: {
								interactive: false
							},
							pan: {
								interactive: true
							}
						});
						
						// add zoom text

						$("<div class=\'button uk-text-bold\' style=\'float: right; margin-right: 95px; margin-top: -24px;\'>Zoom</div>").appendTo(placeholder);

						

						// add zoom home reset

						$("<div class=\'button btn btn-mini btn-info\' style=\' cursor: pointer; float: right; margin-right: -2px; margin-top: -28px;\'><i class=\'uk-icon uk-icon-refresh\'></i></div>")

							.appendTo(placeholder)

							.click(function (event) {

								event.preventDefault();

								doPlot("left");

							});

						

						// add zoom in button

						$("<div class=\'button btn btn-mini btn-success\' style=\' cursor: pointer; float: right; margin-top: -28px; margin-right: ;\'><i class=\'uk-icon uk-icon-plus-circle\'></i></div>")

							.appendTo(placeholder)

							.click(function (event) {

								event.preventDefault();

								plot.zoom();

							});

							

						// add zoom out button

						$("<div class=\'button btn btn-mini btn-danger\' style=\' cursor: pointer; float: right; margin-right: ; margin-top: -28px;\'><i class=\'uk-icon uk-icon-minus-circle\'></i></div>")

							.appendTo(placeholder)

							.click(function (event) {

								event.preventDefault();

								plot.zoomOut();

							});
							
						$("#placeholder").bind("plothover", function (event, pos, item) {
							$("#tooltip").remove();
							if (item) {
								var tooltip = item.series.data[item.dataIndex][2];
								
								$(\'<div id="tooltip">\' + tooltip + \'</div>\')
									.css({
										position: \'absolute\',
										display: \'none\',
										top: item.pageY + 15,
										left: item.pageX + 15,
										border: \'1px solid #fdd\',
										padding: \'2px\',
										\'background-color\': \'#fee\',
										opacity: 0.80 })
									.appendTo("body").fadeIn(200);
					
								
								showTooltip(item.pageX, item.pageY, tooltip);
							}
						});
					}
					
					doPlot("left");';	  
				  
			echo '});'."\n";
			echo '</script>';
		}
	}
}
// stop CTR chart --------------------------------------------------
?>