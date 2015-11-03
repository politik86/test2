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

class Diagram{
	
	function getSumScale($max_value){
		if($max_value <= 6){
			return array("6", "5", "4", "3", "2", "1");
		}
		elseif($max_value <= 12){
			return array("12", "10", "8", "6", "4", "2");
		}
		else{
			$step = ceil($max_value / 6);
			while($step % 5 != 0){
				$step ++;
			}
			
			$return = array();
			$val = 0;
			for($i=1; $i<=6; $i++){
				$val += $step;
				$return[] = $val;
			}
			$return = array_reverse($return);
			
			return $return;
		}
	}
	
	function getEditLine($advertisers, $campaigns, $ad_name, $date_range, $all_ads){
		$edit_line = array();
		$db = JFactory::getDBO();
		
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
		
		$sql = "select `entry_date`, `impressions`, `click` from #__ad_agency_statistics where `entry_date` >= '".$start_date."' and `entry_date` <= '".$stop_date."' order by `entry_date` asc";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		if(isset($result) && count($result) > 0){
			$temp = array();
			foreach($result as $key=>$value){
				$impressions = json_decode($value["impressions"], true);
				$click = json_decode($value["click"], true);
				$nr_imp = 0;
				$nr_click = 0;
				
				if(isset($impressions) && count($impressions) > 0){
					foreach($impressions as $imp_key=>$imp_value){
						if(intval($ad_name) == 0){
							if(intval($imp_value["advertiser_id"]) == intval($advertisers) && intval($imp_value["campaign_id"]) == intval($campaigns) && isset($all_ads[$imp_value["banner_id"]])){
								$nr_imp += $imp_value["how_many"];
							}
						}
						else{
							if(intval($imp_value["advertiser_id"]) == intval($advertisers) && intval($imp_value["campaign_id"]) == intval($campaigns) && intval($imp_value["banner_id"]) == intval($ad_name)){
								$nr_imp += $imp_value["how_many"];
							}
						}
					}
					$temp[$value["entry_date"]]["impressions"] = $nr_imp;
				}
				else{
					$temp[$value["entry_date"]]["impressions"] = 0;
				}
				
				if(isset($click) && count($click) > 0){
					foreach($click as $click_key=>$click_value){
						if(intval($ad_name) == 0){
							if(intval($click_value["advertiser_id"]) == intval($advertisers) && intval($click_value["campaign_id"]) == intval($campaigns) && isset($all_ads[$click_value["banner_id"]])){
								$nr_click += $click_value["how_many"];
							}
						}
						else{
							if(intval($click_value["advertiser_id"]) == intval($advertisers) && intval($click_value["campaign_id"]) == intval($campaigns) && intval($click_value["banner_id"]) == intval($ad_name)){
								$nr_click += $click_value["how_many"];
							}
						}
					}
					$temp[$value["entry_date"]]["click"] = $nr_click;
				}
				else{
					$temp[$value["entry_date"]]["click"] = 0;
				}
				
				if($nr_imp != 0 && $nr_click != 0){
					$nr = $nr_click / $nr_imp;
					$temp[$value["entry_date"]]["ctr"] = number_format($nr, 2, '.', '');
				}
				else{
					$temp[$value["entry_date"]]["ctr"] = "0";
				}
			}
			$edit_line = $temp;
		}
		
		// add new values to diagram ------------------------------------------------------------
		$start = strtotime($start_date);
		$stop = strtotime($stop_date);
		
		if(count($edit_line) > 0){
			for($i=$start; $i<=$stop; $i+=86400){
				if(!isset($edit_line[date("Y-m-d", $i)])){
					$temp = array("impressions"=>0, "click"=>0, "ctr"=>0);
					$edit_line[date("Y-m-d", $i)] = $temp;
				}
			}
		}
		ksort($edit_line);
		// add new values to diagram ------------------------------------------------------------
		
		return $edit_line;
	}
	
	function createLine($sum, $type, $scale){
		$scale = array_reverse($scale);
		$return  = '';
		$step = 50;
		$height = 1;
		
		foreach($scale as $key=>$value){
			if($sum == $value){
				$height = ($key+1) * 50 + 5;
			}
			elseif($sum < $value){
				$height = $key * 50;
				$diff = $sum - @$scale[$key-1];
				$diff_step = $value - @$scale[$key-1];
				$x = ($diff * $step) / $diff_step;
				$height += $x;
				$height += 5;
			}
		}
		
		$return .= '<div style="display: table-cell;">
						<div class="'.$type.'" style="height:'.$height.'px;">
							<span class="nr-total">'.$sum.'</span>
						</div>
					</div>';
		
		return $return;
	}
	
	function plot($advertisers, $campaigns, $ad_name, $all_ads){
		$chart_type = JRequest::getVar("chart_type", "summary");
		$date_range = JRequest::getVar("date_range", "this_week");
		$diagram = "";
		
		$user = JFactory::getUser();
		$user_id = $user->id;
		$db = JFactory::getDBO();
		$sql = "select `aid` from #__ad_agency_advertis where `user_id`=".intval($user_id);
		$db->setQuery($sql);
		$db->query();
		$advertisers = $db->loadColumn();
		$advertisers = @$advertisers["0"];
		
		$edit_line = $this->getEditLine($advertisers, $campaigns, $ad_name, $date_range, $all_ads);
		
		$max = 0;
		if(isset($edit_line) && count($edit_line) > 0){
			foreach($edit_line as $key=>$value){
				if($chart_type == "summary"){
					$nr = $value["impressions"];
					if($nr > $max){
						$max = $nr;
					}
					
					$nr = $value["click"];
					if($nr > $max){
						$max = $nr;
					}
					
					$nr = $value["ctr"];
					if($nr > $max){
						$max = $nr;
					}
				}
				elseif($chart_type == "impressions"){
					$nr = $value["impressions"];
					if($nr > $max){
						$max = $nr;
					}
				}
				elseif($chart_type == "clicks"){
					$nr = $value["click"];
					if($nr > $max){
						$max = $nr;
					}
				}
				elseif($chart_type == "ctr"){
					$nr = $value["ctr"];
					if($nr > $max){
						$max = $nr;
					}
				}
			}
		}
		
		$scale = $this->getSumScale($max);		
		$height = array("0"=>"50", "1"=>"50", "2"=>"50", "3"=>"50", "4"=>"50", "5"=>"0");
		
		$diagram .= '<table style="width:100%">';
		$diagram .= 	'<tr>';
		$diagram .= 		'<td width="30px" nowrap align="right" valign="bottom" style="padding-top:20px;">';
		//start sum line ------------------------------------------
		$diagram .= 			'<div id="sum_line">';
		if(isset($scale) && count($scale) > 0){
			foreach($scale as $key=>$value){
				$diagram .= '<div style="height: '.$height[$key].'px;">'.$value.' -</div>';
			}
			$diagram .= '<div style="padding-top: 50px;">0</div>';
		}
		$diagram .= 			'</div>';
		//end sum line ------------------------------------------
		$diagram .= 		'</td>';
		$diagram .= 		'<td id="td_lines" valign="bottom">';
		$diagram .= 			'<table>';
		$diagram .= 				'<tr>';
		if(isset($edit_line) && count($edit_line) > 0){
			foreach($edit_line as $key=>$value){
				// start day diagram -----------------------------------------------------------------------
				if($chart_type == "summary"){
					$diagram .=				'<td class="element">';
					$diagram .=					'<div style="display: table-row;">';
					$diagram .=					$this->createLine($value["impressions"], "impressions", $scale);
					$diagram .=					$this->createLine($value["click"], "clicks", $scale);
					$diagram .=					$this->createLine($value["ctr"], "ctr", $scale);
					$diagram .=					'</div>';
					$diagram .=				'</td>';
				}
				elseif($chart_type == "impressions"){
					$diagram .=				'<td class="element">';
					$diagram .=					'<div style="display: table-row;">';
					$diagram .=					$this->createLine($value["impressions"], "impressions", $scale);
					$diagram .=					'</div>';
					$diagram .=				'</td>';
				}
				elseif($chart_type == "clicks"){
					$diagram .=				'<td class="element">';
					$diagram .=					'<div style="display: table-row;">';
					$diagram .=					$this->createLine($value["click"], "clicks", $scale);
					$diagram .=					'</div>';
					$diagram .=				'</td>';
				}
				elseif($chart_type == "ctr"){
					$diagram .=				'<td class="element">';
					$diagram .=					'<div style="display: table-row;">';
					$diagram .=					$this->createLine($value["ctr"], "ctr", $scale);
					$diagram .=					'</div>';
					$diagram .=				'</td>';
				}
				// stop day diagram -----------------------------------------------------------------------
			}
		}
		$diagram .= 				'</tr>';
		$diagram .= 			'</table>';
		$diagram .= 		'</td>';
		$diagram .= 	'</tr>';
		//start edit line ------------------------------------------
		$diagram .= 	'<tr>';
		$diagram .= 		'<td>';
		$diagram .= 		'</td>';
		$diagram .= 		'<td>';
		$diagram .= 			'<table>';
		$diagram .= 				'<tr>';
		if(isset($edit_line) && count($edit_line) > 0){
			foreach($edit_line as $key=>$value){
				// start day diagram -----------------------------------------------------------------------
				if($chart_type == "summary"){
					$diagram .=				'<td class="element-label">';
					$diagram .=					$key;
					$diagram .=				'</td>';
				}
				elseif($chart_type == "impressions"){
					$diagram .=				'<td class="element-label">';
					$diagram .=					$key;
					$diagram .=				'</td>';
				}
				elseif($chart_type == "clicks"){
					$diagram .=				'<td class="element-label">';
					$diagram .=					$key;
					$diagram .=				'</td>';
				}
				elseif($chart_type == "ctr"){
					$diagram .=				'<td class="element-label">';
					$diagram .=					$key;
					$diagram .=				'</td>';
				}
				// stop day diagram -----------------------------------------------------------------------
			}
		}
		$diagram .= 				'</tr>';
		//end edit line ------------------------------------------
		$diagram .= 			'</table>';
		$diagram .= 		'</td>';
		$diagram .= 	'</tr>';
		$diagram .= '</table>';
		
		echo $diagram;
	}
}
?>