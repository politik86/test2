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

class adagencyAdminHelper {

	function get_topcountries_option ($configs) {
	    $db = JFactory::getDBO();
		$sql = "select distinct country from #__ad_agency_states order by country";
		$db->setQuery($sql);
		$countries = $db->loadObjectList();

		$mitems = array();
		$topcountries = explode (",", $configs->topcountries);        
		foreach ($countries as $country) {

			if ($country != '0') {
				$mitems[] = JHTML::_('select.option',  $country->country, $country->country );
			} else {

			}
		}
		$output = JHTML::_('select.genericlist',  $mitems, 'topcountries[]', 'class="inputbox" size="10" multiple ', 'value', 'text', $topcountries);

		return $output;
	}

	function get_country_options ($profile, $ship = false, $configs) {
		$db = JFactory::getDBO();	
		$country_word = 'country';
	        if ($ship) $country_word = 'ship'.$country_word;
		if (!isset($profile->$country_word)) @$profile->$country_word = '';
		$query = "SELECT country FROM #__ad_agency_states GROUP BY country ORDER BY country ASC";
		$db->setQuery( $query );
		$countries = $db->loadObjectList();
		global $topcountries;
	
		$country_option = "<select name='".$country_word."' id='".$country_word."' onChange='changeProvince".($ship?'_ship':'')."();'>";
		$country_option .= '<option value="" ';
		if (!$profile->$country_word) $country_option .= 'selected';
		$country_option .= '>'.(JText::_('HELPERCOUNSEL')).'</option>';             
		$country_option .= '<option value="" ></option>';   
		$configs->topcountries = '';            
		$topcountries = explode (",", $configs->topcountries);

        	if (count ($topcountries) > 0) {
			foreach ($topcountries as $topcountry){
				if ($topcountry != '0') {
					$country_option .= '<option value="'.$topcountry.'" ';
					if ( $profile->$country_word == $topcountry && strlen (trim ($topcountry)) > 0) {
					        $country_option .= 'selected';  
        				}
					$country_option .= ' >'.$topcountry.'</option>';  
				}
			
			}
        		
		} else {
		    $country_option .= '<option value="United-States" ';
		    if ( $profile->$country_word == 'United-States') {
		        $country_option .= 'selected';  
		    }
        	    $country_option .= ' >United-States</option>';  
		    
        	    $country_option .= '<option value="Canada" ';
		    if ($profile->$country_word == 'Canada') {
		        $country_option .= 'selected';  
		
		    	$country_option .= '  >Canada</option>';                
        	
			}
	
        	}                
		$country_option .= '<option value=""  >-------</option>';               
		foreach( $countries as $country ) {
        		if (($country->country != 'United-States' && $country->country != 'Canada' && count($topcountries) < 1) || (count($topcountries) > 0 && !in_array($country->country, $topcountries))) {
	        		$country_option .= "<option value='" . $country->country ."' ";
        	
				if ($country->country == $profile->$country_word) $country_option .= "selected";
    
	        	    	$country_option .=  " >" . $country->country . "</option>"; 
		        }
		}
		$country_option .= "</select>"; 
		return $country_option; 

	}

	function get_store_province ($configs, $ship = 0) {
		$db = JFactory::getDBO();
		$province_word = "province";
	        if ($ship) $province_word = 'ship'.$province_word;
	        if (isset($configs->state)){
         		$query = "select state FROM #__ad_agency_states where country='".$configs->country."'";
            		$db->setQuery($query);
		        $res = $db->loadObjectList();
		        $output = '
                		<div id="'.$province_word.'">
		                    <select name="state">';
			foreach ($res as $i => $v ) {
                        	$output .= '<option value="'.$v->state.'" '; 
		                if ($v->state == $configs->state) $output .= 'selected';
				$output .= '>'.$v->state.'</option>';


		        }

		        $output .= '</select></div>';
	       	} else {
			$output = '<div id="'.$province_word.'">
         	           <select><option>'.(JText::_('HELPERSELECTCOUNTY')).'</option></select>
                	</div>
		            ';
            
	       	}
		return $output;
		
	}

	function parseDate($format, $date) {
		$format = explode ("-", $format);
		$date = explode ("-", $date);
		$res = 0;
		foreach ($format as $i => $v) {
			switch ($v) {
				case "YYYY":
				case "Y":
					$year = $date[$i];
					break;
					
				case "MM":
				case "m":
					$month = $date[$i];
					break;
				
				case "DD":
				case "d":
					$day = $date[$i];
					break;
			}
		}
		if ((int )$day > 0 && (int )$month > 0 && (int )$year > 0) {
			$res = mktime (0,0,0, (int)$month, (int)$day, (int)$year);
		} else {
			$res = 0;
		}
		return $res;
	}

	function publishAndExpiryHelper(&$img, &$alt, &$times, &$status, $timestart, $timeend, $published, $configs) {
		$now = time();
		$nullDate = 0;

		if ( $now <= $timestart && $promo->publishing == "1" ) {
	                $img = "tick.png";
        	        $alt = JText::_('HELPERPUBLISHED');
	        } else if ( ( $now <= $timeend || $timeend == $nullDate ) && $published == "1" ) {
        	        $img = "tick.png";
                	$alt = JText::_('HELPERPUBLISHED');
	        } else if ( $now > $timeend && $published == "1" && $timeend != $nullDate) {
        	        $img = "publish_r.png";
                	$alt = JText::_('HELPEREXPIRED');
	        } elseif ( $published == "0" ) {
        	        $img = "publish_x.png";
                	$alt = JText::_('HELPERUNPUBLICHED');
	        }       
  	        $times = '';
          	if (isset( $timestart)) {
          		if ( $timestart == $nullDate) {
                		$times .= "<tr><td>".(JText::_("HELPERALWAWSPUB"))."</td></tr>";
	                } else {
        		        $times .= "<tr><td>".(JText::_("HELPERSTARTAT"))." ".date($configs->time_format, $timestart)."</td></tr>";
	                }
        	}
	        if ( isset( $timeend ) ) {
        	        if ( $timeend == $nullDate) {
                		$times .= "<tr><td>".(JText::_("HELPERNEVEREXP"))."</td></tr>";
	                } else {
        		        $times .= "<tr><td>".(JText::_("HELPEXPAT"))." ".date($configs->time_format, $timeend)."</td></tr>";
	                }
        	}


                $status = '';
		if (!isset ($promo->codelimit)) {
			$promo->codelimit = 0;
		}
		if (!isset ($promo->used)) {
			$promo->used = 0;
		}

		$remain = $promo->codelimit - $promo->used;
		if (($timeend > $now || $timeend == $nullDate )&& ($remain > 0 || $promo->codelimit == 0)) {
			$status = JText::_("HELPERACTIVE");
		} else if ($timeend != $nullDate && $timeend < $now && ($remain < 1 && $promo->codelimit > 0)) {
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE"))." (".(JText::_("Date"))." ,".(JText::_("Amount")).")</span>";
		} else if ($remain < 1 && $promo->codelimit > 0) {
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE"))." (".(JText::_("Amount")).")</span>";
		} else if ($timeend < $now && $timeend != $nullDate){
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE"))." (".(JText::_("Date")).")</span>";
		} else {
			$status = "<span style='color:red'>".(JText::_("HELPERPROMOERROR"))."</span>";
		}

	}
	
	function formatime($time, $option = 1){
		if($time == "Never"){
			return "Never";
		}
		
		if(is_null($time)){
			$joomla_date = JFActory::getDate();
			$time = $joomla_date->toSql();
		}
		
		$time = str_replace("/", "-", $time);
		
		$date_time = explode(" ",$time);
		$tdate = explode("-", $date_time["0"]);
		$output = NULL;
		if(!isset($date_time["1"])){
			$date_time["1"] = NULL;
		}
		
		switch($option){
			case "0":
				$output = $tdate["0"]."-".$tdate["1"]."-".$tdate["2"]." ".$date_time["1"];
				break;
			case "1":
				$output = $tdate["1"]."/".$tdate["2"]."/".$tdate["0"]." ".$date_time["1"];
				break;
			case "2":
				$output = $tdate["2"]."-".$tdate["1"]."-".$tdate["0"]." ".$date_time["1"];
				break;
			case "3":	
				$output = $tdate["0"]."-".$tdate["1"]."-".$tdate["2"];
				break;
			case "4":
				$output = $tdate["1"]."/".$tdate["2"]."/".$tdate["0"];
				break;
			case "5":
				$output = $tdate["2"]."-".$tdate["1"]."-".$tdate["0"];
				break;
			default:
				$output = $time;
				break;
		}
		
		return $output;
	}
	
	function ip_is_private($ip){
        $pri_addrs = array(
                          '10.0.0.0|10.255.255.255',
                          '172.16.0.0|172.31.255.255',
                          '192.168.0.0|192.168.255.255',
                          '169.254.0.0|169.254.255.255',
                          '127.0.0.0|127.255.255.255'
                         );

        $long_ip = ip2long($ip);
        if($long_ip != -1) {
            foreach($pri_addrs AS $pri_addr){
                list($start, $end) = explode('|', $pri_addr);
                 // IF IS PRIVATE
                 if($long_ip >= ip2long($start) && $long_ip <= ip2long($end))
                 return (TRUE);
            }
		}
		return (FALSE);
	}
	
	function getRevenueByDate($date){
		$db = JFactory::getDBO();
		$sql = "select sum(`cost`) from #__ad_agency_order where `order_date`='".$date."' and `status`='paid'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		if(!isset($result["0"])){
			return 0;
		}
		else{
			return $result["0"];
		}
	}
};
?>